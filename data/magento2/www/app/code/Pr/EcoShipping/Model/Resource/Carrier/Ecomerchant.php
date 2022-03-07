<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Pr\EcoShipping\Model\Resource\Carrier;

/**
 * Shipping table rates
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ecomerchant extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Import table rates website ID
     *
     * @var int
     */
    protected $_importWebsiteId     = 0;

    /**
     * Errors in import process
     *
     * @var array
     */
    protected $_importErrors        = array();

    /**
     * Count of imported table rates
     *
     * @var int
     */
    protected $_importedRows        = 0;

    /**
     * Array of unique table rate keys to protect from duplicates
     *
     * @var array
     */
    protected $_importUniqueHash    = array();

    /**
     * Array of countries keyed by iso2 code
     *
     * @var array
     */
    protected $_importIso2Countries;

    /**
     * Array of countries keyed by iso3 code
     *
     * @var array
     */
    protected $_importIso3Countries;

    /**
     * Associative array of countries and regions
     * [country_id][region_code] = region_id
     *
     * @var array
     */
    protected $_importRegions;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $directoryResourceModelCountryCollectionFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $directoryResourceModelRegionCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\FileFactory
     */
    protected $ioFileFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $directoryResourceModelCountryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $directoryResourceModelRegionCollectionFactory,
        \Magento\Framework\Filesystem\Io\FileFactory $ioFileFactory
    ) {
        $this->ioFileFactory = $ioFileFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->directoryResourceModelCountryCollectionFactory = $directoryResourceModelCountryCollectionFactory;
        $this->directoryResourceModelRegionCollectionFactory = $directoryResourceModelRegionCollectionFactory;
    }
    /**
     * Define main table and id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('shipping_ecomerchant', 'pk');
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array|boolean
     */
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array(
            ':website_id' => (int) $request->getWebsiteId(),
            ':country_id' => $request->getDestCountryId(),
            ':region_id' => (int) $request->getDestRegionId(),
            ':postcode' => $request->getDestPostcode()
        );
        
        $select = $adapter->select()
            
            ->from($this->getMainTable())
            ->columns(array(
                'dest_region_id',
            ))
            ->where('website_id = :website_id')
            ->order(array('dest_country_id DESC', 'dest_region_id DESC', 'LENGTH(dest_zip) DESC'))
            ->limit(1);

        // Render destination condition
        $orWhere = '(' . implode(') OR (', array(
            "dest_country_id = :country_id AND dest_region_id = :region_id AND LOCATE(dest_zip, :postcode) = 1",
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",

            // Handle asterix in dest_zip field
            "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
            "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
            "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",

            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
            "dest_country_id = :country_id AND dest_region_id = 0 AND LOCATE(dest_zip, :postcode) = 1",
            "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
        )) . ')';
        $select->where($orWhere);

        $result = $adapter->fetchRow($select, $bind);
        // Normalize destination zip code
        if ($result && $result['dest_zip'] == '*') {
            $result['dest_zip'] = '';
        }
        return $result;
    }

    /**
     * Upload table rate file and import data from it
     *
     * @param \Magento\Framework\DataObject $object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    public function uploadAndImport(\Magento\Framework\DataObject $object)
    {
        if (empty($_FILES['groups']['tmp_name']['gws_shipping_ecomerchant']['fields']['import']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['gws_shipping_ecomerchant']['fields']['import']['value'];
        $website = $this->storeManager->getWebsite($object->getScopeId());

        $this->_importWebsiteId     = (int)$website->getId();
        $this->_importUniqueHash    = array();
        $this->_importErrors        = array();
        $this->_importedRows        = 0;

        $io     = $this->ioFileFactory->create();
        $info   = pathinfo($csvFile);
        $io->open(array('path' => $info['dirname']));
        $io->streamOpen($info['basename'], 'r');

        // check and skip headers
        $headers = $io->streamReadCsv();
        if ($headers === false || count($headers) < 6) {
            $io->streamClose();
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Table Rates File Format'));
        }

        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $rowNumber  = 1;
            $importData = array();

            $this->_loadDirectoryCountries();
            $this->_loadDirectoryRegions();

            // delete old data by website and condition name
            $condition = array(
                'website_id = ?'     => $this->_importWebsiteId,
            );
            $adapter->delete($this->getMainTable(), $condition);

            while (false !== ($csvLine = $io->streamReadCsv())) {
                $rowNumber ++;

                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if (count($importData) == 5000) {
                    $this->_saveImportData($importData);
                    $importData = array();
                }
            }
            $this->_saveImportData($importData);
            $io->streamClose();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
        } catch (Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(__('An error occurred while import table rates.'));
        }

        $adapter->commit();

        if ($this->_importErrors) {
            $error = __('File has not been imported. See the following list of errors: %s', implode(" \n", $this->_importErrors));
            throw new \Magento\Framework\Exception\LocalizedException($error);
        }

        return $this;
    }

    /**
     * Load directory countries
     *
     * @return \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    protected function _loadDirectoryCountries()
    {
        if (!is_null($this->_importIso2Countries) && !is_null($this->_importIso3Countries)) {
            return $this;
        }

        $this->_importIso2Countries = array();
        $this->_importIso3Countries = array();

        /** @var $collection Mage_Directory_Model_Resource_Country_Collection */
        $collection = $this->directoryResourceModelCountryCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->_importIso2Countries[$row['iso2_code']] = $row['country_id'];
            $this->_importIso3Countries[$row['iso3_code']] = $row['country_id'];
        }

        return $this;
    }

    /**
     * Load directory regions
     *
     * @return \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    protected function _loadDirectoryRegions()
    {
        if (!is_null($this->_importRegions)) {
            return $this;
        }

        $this->_importRegions = array();

        /** @var $collection Mage_Directory_Model_Resource_Region_Collection */
        $collection = $this->directoryResourceModelRegionCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->_importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }

        return $this;
    }

    /**
     * Validate row for import and return table rate array or false
     * Error will be add to _importErrors array
     *
     * @param array $row
     * @param int $rowNumber
     * @return array|false
     */
    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row
        if (count($row) < 5) {
            $this->_importErrors[] = __('Invalid Table Rates format in the Row #%s', $rowNumber);
            return false;
        }

        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        // validate country
        if (isset($this->_importIso2Countries[$row[0]])) {
            $countryId = $this->_importIso2Countries[$row[0]];
        } elseif (isset($this->_importIso3Countries[$row[0]])) {
            $countryId = $this->_importIso3Countries[$row[0]];
        } elseif ($row[0] == '*' || $row[0] == '') {
            $countryId = '0';
        } else {
            $this->_importErrors[] = __('Invalid Country "%s" in the Row #%s.', $row[0], $rowNumber);
            return false;
        }

        // validate region
        if ($countryId != '0' && isset($this->_importRegions[$countryId][$row[1]])) {
            $regionId = $this->_importRegions[$countryId][$row[1]];
        } elseif ($row[1] == '*' || $row[1] == '') {
            $regionId = 0;
        } else {
            $this->_importErrors[] = __('Invalid Region/State "%s" in the Row #%s.', $row[1], $rowNumber);
            return false;
        }

        // detect zip code
        if ($row[2] == '*' || $row[2] == '') {
            $zipCode = '*';
        } else {
            $zipCode = $row[2];
        }
        
        // validate price
        $price_economy = $this->_parseDecimalValue($row[3]);
        if ($price_economy === false) {
            $this->_importErrors[] = __('Invalid Shipping Price "%s" in the Row #%s.', $row[3], $rowNumber);
            return false;
        }
        
        // validate price
        $price_next_day = $this->_parseDecimalValue($row[4]);
        if ($price_next_day === false) {
            $this->_importErrors[] = __('Invalid Shipping Price "%s" in the Row #%s.', $row[4], $rowNumber);
            return false;
        }

        // validate price
        $price_0kg = $this->_parseDecimalValue($row[5]);
        if ($price_0kg === false) {
            $this->_importErrors[] = __('Invalid Shipping Price "%s" in the Row #%s.', $row[5], $rowNumber);
            return false;
        }
        
        // validate price
        $price_20kg = $this->_parseDecimalValue($row[6]);
        if ($price_20kg === false) {
            $this->_importErrors[] = __('Invalid Shipping Price "%s" in the Row #%s.', $row[6], $rowNumber);
            return false;
        }
        
        // validate price
        $price_100kg = $this->_parseDecimalValue($row[7]);
        if ($price_100kg === false) {
            $this->_importErrors[] = __('Invalid Shipping Price "%s" in the Row #%s.', $row[7], $rowNumber);
            return false;
        }

        // protect from duplicate
        $hash = sprintf("%s-%d-%s", $countryId, $regionId, $zipCode);
        if (isset($this->_importUniqueHash[$hash])) {
            $this->_importErrors[] = __('Duplicate Row #%s (Country "%s", Region/State "%s", Zip "%s").', $rowNumber, $row[0], $row[1], $zipCode);
            return false;
        }
        $this->_importUniqueHash[$hash] = true;

        return array(
            $this->_importWebsiteId,    // website_id
            $countryId,                 // dest_country_id
            $regionId,                  // dest_region_id,
            $zipCode,                   // dest_zip
            $price_economy,             // price
            $price_next_day,            // price
            $price_0kg,                 // price 0kg
            $price_20kg,                // price 20kg
            $price_100kg,               // price 100kg
        );
    }

    /**
     * Save import data batch
     *
     * @param array $data
     * @return \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'price_economy', 'price_next_day', 'price_0kg', 'price_20kg', 'price_100kg');
            $this->_getWriteAdapter()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }

    /**
     * Parse and validate positive decimal value
     * Return false if value is not decimal or is not positive
     *
     * @param string $value
     * @return bool|float
     */
    protected function _parseDecimalValue($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, array('flags'=>FILTER_FLAG_ALLOW_FRACTION));
    }

    /**
     * Parse and validate positive decimal value
     *
     * @see self::_parseDecimalValue()
     * @deprecated since 1.4.1.0
     * @param string $value
     * @return bool|float
     */
    protected function _isPositiveDecimalNumber($value)
    {
        return $this->_parseDecimalValue($value);
    }
}
