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
namespace FME\Productattachments\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

class Productattachments extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /** ---Functions--- */
    public $isPkAutoIncrement = true;
    /**
     * Store model
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Resource\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }
    public function _construct()
    {
        $this->_init('productattachments', 'productattachments_id');
    }
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
                $products = $this->__listProducts($object);
                $object->setData('product_id', $products);
        }
        $select = $this->getConnection()->select()
                ->from($this->getTable('productattachments_store'))
                ->where('productattachments_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }
        $customerGroupIds = $object->getData('customer_group_id');
        if (!empty($customerGroupIds)) {
            $customer_group_array = explode(',', $customerGroupIds);
            $object->setData('customer_group_id', $customer_group_array);
        }
        return parent::_afterLoad($object);
    }
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = $object->getData('store_id');
        if (!empty($stores)) {
            $condition = $this->getConnection()->quoteInto('productattachments_id = ?', $object->getId());
            $this->getConnection()->delete($this->getTable('productattachments_store'), $condition);
            foreach ((array) $stores as $store) {
                $storeArray = [];
                $storeArray['productattachments_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->getConnection()->insert($this->getTable('productattachments_store'), $storeArray);
            }
        }
        //there is only 1 product for save, coming from save-product-after
        $one_product_only = $object->getData("one_product_only");
        $links = $object->getData("product_id");
        if (isset($links)) {
            $productIds = $links;
            $cond[] = $this->getConnection()->quoteInto('productattachments_id = ?', $object->getId());
            if(isset($one_product_only)){
                $cond[] = $this->getConnection()->quoteInto('product_id = ?', $productIds[0]); 
            }
            $this->getConnection()->delete($this->getTable('productattachments_products'), $cond);
            foreach ($productIds as $_product) {
                $newsArray = [];
                $newsArray['productattachments_id'] = $object->getId();
                $newsArray['product_id'] = $_product;
                $this->getConnection()->insert($this->getTable('productattachments_products'), $newsArray);
            }
        }
        $cms = $object->getData("cmspage_ids");
        if (isset($cms) && !isset($one_product_only)) {
            $productIds = $cms;
            $condition = $this->getConnection()->quoteInto('productattachments_id = ?', $object->getId());
            $this->getConnection()->delete($this->getTable('productattachments_cms'), $condition);   
            if($productIds):
                foreach ($productIds as $_product) {
                    $newsArray = [];
                    $newsArray['productattachments_id'] = $object->getId();
                    $newsArray['cms_id'] = $_product;
                    $this->getConnection()->insert($this->getTable('productattachments_cms'), $newsArray);
                }
            endif;
        }
        return parent::_afterSave($object);
    }
    private function __listProducts(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()
                ->from($this->getTable('productattachments_products'))
                ->where('productattachments_id = ?', $object->getId());
        $data = $this->getConnection()->fetchAll($select);
        if ($data) {
            $productsArr = [];
            foreach ($data as $_i) {
                $productsArr[] = $_i['product_id'];
            }
            return $productsArr;
        }
    }
    public function updateDownloadsCounter($id)
    {
        $attachmentsTable = $this->getTable('productattachments');
        $db = $this->getConnection();
        try {
            $db->beginTransaction();
            $db->exec("UPDATE " . $attachmentsTable . " SET downloads = (downloads+1) WHERE productattachments_id = $id");
            $db->commit();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $db->rollBack();
            throw new LocalizedException($e);
        }
    }
    public function setIsPkAutoIncrement($flag)
    {
        $this->_isPkAutoIncrement = $flag;
    }
}
