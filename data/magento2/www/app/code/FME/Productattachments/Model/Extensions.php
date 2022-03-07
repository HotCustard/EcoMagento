<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category FME
* @package FME_Productattachments
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/
namespace FME\Productattachments\Model;

class Extensions extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Aion Test cache tag
     */
    const CACHE_TAG = 'Productattachments_extensions';

    /**
     * @var string
     */
    protected $_cacheTag = 'Productattachments_extensions';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Productattachments_extensions';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('FME\Productattachments\Model\ResourceModel\Extensions');
    }
    public function getExtensionsCommaseprated()
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $connection = $objectManager->get('Magento\Framework\App\ResourceConnection'); 
        $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');       
        $productsTable = $connection->getTableName('productattachments_extensions');
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.status = 1');
        $result = $conn->fetchAll($select);
        foreach ($result as $value) {
             $ext_arr[] = $value['type'];
        }
        if (isset($ext_arr)) {
            return $ext_arr;
        //return implode(',', $ext_arr);
        } else {
            return (array)null;
        }
    }
    public function getExtensions(){
        $collection  = $this->getCollection()->addFieldToFilter('status', 1);
        return $collection->getData();
    }
    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }
    /**
     * Prepare item's statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
