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

class Products extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Aion Test cache tag
     */
    const CACHE_TAG = 'productattachments_products';

    /**
     * @var string
     */
    protected $_cacheTag = 'productattachments_products';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'productattachments_products';

    /**
     * @return void
     */

    protected $_resource;
        
    protected $storeManager;
        
    protected $_objectManager;
        
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {  
        $this->storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_resource = $resource;     
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('FME\Productattachments\Model\ResourceModel\Products');
    }
    public function CountAttachments($product_id)
    {
         $connection = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');   
         $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');
        $productsTable = $connection->getTableName('productattachments_products');
         $faqsTable = $connection->getTableName('productattachments');
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.product_id ='.$product_id);
                                    $select->join(
                                        ['fs' => $faqsTable],
                                        'f.productattachments_id = fs.productattachments_id ',
                                        []
                                    );
        $result = $conn->fetchAll($select);
        return $result;
    }
    public function CountVisibleAttachments($product_id)
    {
          $connection = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');  
         $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');       
        $productsTable = $connection->getTableName('productattachments_products');
           $faqsTable = $connection->getTableName('productattachments');
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.product_id ='.$product_id);
                                    $select->join(
                                        ['fs' => $faqsTable],
                                        'f.productattachments_id = fs.productattachments_id AND fs.status=1',
                                        []
                                    );
        $result = $conn->fetchAll($select);
        return $result;
    }
    public function getCmsCommaseprated($attachment_id)
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $connection = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');       
        $productsTable = $connection->getTableName('productattachments_cms');
          // echo $productsTable;exit;
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.productattachments_id ='.$attachment_id.' AND f.cms_id != 0');
        $result = $conn->fetchAll($select);
        foreach ($result as $value) {
             $product = $objectManager->get('Magento\Cms\Model\Page')->load($value['cms_id']);
             $products_arr[] = $product['title'];
        }
      
        if (isset($products_arr)) {
            return implode(',', $products_arr);
        } else {
            return "N/A";
        }
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
