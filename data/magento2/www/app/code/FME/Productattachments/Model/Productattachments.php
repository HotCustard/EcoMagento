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

class Productattachments extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    protected $_objectManager;

    protected $_coreResource;

    protected $_urlBuilder;

    protected $_storeManager;

    protected $_cmsPageFactory;

    // ---Functions---
    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\App\ResourceConnection $coreResource, \FME\Productattachments\Model\ResourceModel\Productattachments $resource_m, \FME\Productattachments\Model\ResourceModel\Productattachments\Collection $resourceCollection, \Magento\Framework\UrlInterface $urlBuilder, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Cms\Model\Page $cmsPageFactory
    )
    {
        $this->_objectManager  = $objectManager;
        $this->_coreResource   = $coreResource;
        $this->_urlBuilder     = $urlBuilder;
        $this->_storeManager   = $storeManager;
        $this->_cmsPageFactory = $cmsPageFactory;
        parent::__construct($context, $registry, $resource_m, $resourceCollection);
    }//end __construct()
    public function _construct()
    {
        $this->_init('FME\Productattachments\Model\ResourceModel\Productattachments');
    }//end _construct()
    public function getRelatedProducts($attachmentId)
    {
        $productattachmentsTable = $this->_coreResource->getTableName('productattachments_products');

        $collection = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->getCollection()->addAttachmentIdFilter($attachmentId);

        $collection->getSelect()->joinLeft(
            ['related' => $productattachmentsTable],
            'main_table.productattachments_id = related.productattachments_id'
        )->order('main_table.productattachments_id');
        return $collection->getData();
    }//end getRelatedProducts()
    public function getProducts(\FME\Productattachments\Model\Productattachments $object)
    {
              $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('productattachments_products'))->where('productattachments_id = ?', $object->getId());
        $data         = $this->_getResource()->getConnection()->fetchAll($select);
        if ($data) {
            $productsArr = [];
            foreach ($data as $_i) {
                $productsArr[] = $_i['product_id'];
            }

            return $productsArr;
        }
    }//end getProducts()
    public function getRelatedAttachments($productId)
    {
        $productattachmentsTable = $this->_coreResource->getTableName('productattachments_products');
        $collection = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->getCollection();
        $collection->getSelect()->join(
            ['related' => $productattachmentsTable],
            'main_table.productattachments_id = related.productattachments_id '
            .'and related.product_id = '.$productId
        )->order('main_table.productattachments_id');
        return $collection->getData();
    }//end getRelatedAttachments()
    public function getRelatedCms($cmsid)
    {
        $productcmsTable = $this->_coreResource->getTableName('productattachments_cms');
        $collection              = $this->_objectManager->create('FME\Productattachments\Model\Productattachments')->getCollection();
        $collection->getSelect()->join(
            ['related' => $productcmsTable],
            'main_table.productattachments_id = related.productattachments_id '
            .'and related.cms_id = '.$cmsid
        )->order('main_table.productattachments_id');
        return $collection->getData();
    }//end getRelatedCms()
    public function updateCounter($id)
    {
        return $this->_getResource()->updateDownloadsCounter($id);
    }//end updateCounter()
    public function getCMSPage()
    {
        $CMSTable    = $this->_coreResource->getTableName('cms_page');
        $sqry        = 'select title as label, page_id as value from '.$CMSTable.' where is_active=1';
        $connection  = $this->_coreResource->getConnection('core_read');
        $select      = $connection->query($sqry);
        return $rows = $select->fetchAll();
    }//end getCMSPage()
    public function getAvailableStatuses()
    {
        return [
                self::STATUS_ENABLED  => __('Enabled'),
                self::STATUS_DISABLED => __('Disabled'),
               ];
    }//end getAvailableStatuses()
    /**
     * Check if productattachment identifier exist for specific store
     * return productattachment id if productattachment exists
     *
     * @param  string  $identifier
     * @param  integer $storeId
     * @return integer
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }//end checkIdentifier()

    public function setIsPkAutoIncrement($flag = true)
    {
        $this->_getResource()->setIsPkAutoIncrement($flag);
    }//end setIsPkAutoIncrement()
    public function getProductRelatedAttachments($blockPosition = null, $categoryId = null, $productId = null)
    {
        $productattachmentsTable         = $this->_coreResource->getTableName('productattachments');
        $productattachmentsProductsTable = $this->_coreResource->getTableName('productattachments_products');
        $productattachmentsStoreTable    = $this->_coreResource->getTableName('productattachments_store');
        $productattachmentsCategoryTable = $this->_coreResource->getTableName('productattachments_cats');
        $storeId = $this->_storeManager->getStore()->getId();

        $collection = $this->getCollection()->addStoreFilter($storeId);

        $collection->getSelect()->join(['pastore' => $productattachmentsStoreTable], 'main_table.productattachments_id = pastore.productattachments_id')->join(['paproduct' => $productattachmentsProductsTable], 'main_table.productattachments_id = paproduct.productattachments_id')->join(['pacat' => $productattachmentsCategoryTable], 'main_table.cat_id = pacat.category_id')->where('paproduct.product_id = (?)', $productId)->where('pastore.store_id in (?)', [0, $storeId]);
        if ($blockPosition != null) {
            $collection->getSelect()->where('main_table.block_position LIKE ?', '%'.$blockPosition.'%');
        }

        if ($categoryId != null) {
            $collection->getSelect()->where('main_table.cat_id = (?)', $categoryId)->where('pacat.status = (?)', 1);
        }

        $collection->getSelect()->where('main_table.status = (?)', 1);

        return $collection;
    }//end getProductRelatedAttachments()
    public function getCmsPageRelatedAttachments($categoryId = null)
    {
        //$cmsHelper       = $this->_cmsPageFactory->create();
        $dataCurrentPage =  $this->_cmsPageFactory->getId();
        $pageid          = $dataCurrentPage;

        $storeId = $this->_storeManager->getStore()->getId();

        $productattachmentsTable = $this->_coreResource->getTableName('productattachments');

        $productattachmentsCategoryTable = $this->_coreResource->getTableName('productattachments_cats');
        $productattachmentscmstable = $this->_coreResource->getTableName('productattachments_cms');

        $collection = $this->getCollection()->addStoreFilter($storeId);

            $collection->getSelect()->join(['cms' => $productattachmentscmstable], 'main_table.productattachments_id = cms.productattachments_id AND cms.cms_id = '.$pageid.' AND main_table.status=1');

             $collection->getSelect()->join(['cats' => $productattachmentsCategoryTable], 'main_table.cat_id = cats.category_id AND cats.status =1');
             $collection->setOrder('main_table.cat_id', 'ASC');
         
        return $collection;
    }//end getCmsPageRelatedAttachments()
    public function getpageid()
    {
        $dataCurrentPage =  $this->_cmsPageFactory->getId();
        $pageid          = $dataCurrentPage;
        return $pageid;
    }
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products_position');
        if ($array === null) {
            $temp = $this->getData('product_id');
            
            if($this->getData('product_id') !== null):
                for ($i = 0; $i < sizeof($this->getData('product_id')); $i++) {
                    $array[$temp[$i]] = 0;
                }
            endif;    
            $this->setData('products_position', $array);
        }
        return $array;
    }//end getProductsPosition()
}//end class
