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

class Productcats extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
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
        $this->_init('productattachments_cats', 'category_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()
                ->select()
                ->from($this->getTable('productattachments_category_store'))
                ->where('category_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }
        return parent::_afterLoad($object);
    }
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $categoryStoreIds = $object->getData('store_id');
        if (!empty($categoryStoreIds)) {
            $condition = $this->getConnection()->quoteInto('category_id = ?', $object->getId());
            $this->getConnection()->delete($this->getTable('productattachments_category_store'), $condition);
            foreach ((array) $categoryStoreIds as $store) {
                $storeArray = [];
                $storeArray['category_id'] = (int)$object->getId();
                $storeArray['store_id'] = (int)$store;
                
                $this->getConnection()->insert($this->getTable('productattachments_category_store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }
    public static function toOptionArray()
    {
        $res = [];
        foreach (self::getCategories() as $key => $value) {
            $res[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $res;
    }
    /*
     * Returns IDs of the stores the category with the ID given belongs to
     * @param int $categoryId The ID of the category
     * @result array ID of the stores
     */
    public function getStoreIds($categoryId)
    {
        if (!$categoryId) {
            return [];
        }
        $db = $this->getConnection();
        $select = $db->select()
                ->from($this->getTable('productattachments_category_store'), 'store_id')
                ->where('category_id=?', $categoryId);
        return $db->fetchCol($select);
    }
    /*
     * Returns the IDs of the stores that have categories with the same URL key as passed
     * @param int $categoryId The ID of current category, needed to exclude from result set
     * @param string $url URL key of the category
     * @return array The IDs of the stores
     */
    public function getSameUrlCategoryStoreIds($categoryId, $url)
    {
        if (!$url) {
            return [];
        }
        $db = $this->getConnection();
        $select = $db->select()
                ->from(['c' => $this->getMainTable()], '')
                ->joinInner(['cs' => $this->getTable('productattachments_category_store')], 'c.category_id=cs.category_id', ['store_ids' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT cs.store_id)')])
                ->where('c.category_url_key=?', $url)
                ->group('c.category_id');
        if ($categoryId) {
            $select->where('c.category_id<>?', $categoryId);
        }
        if ($res = $db->fetchOne($select)) {
            return array_unique(explode(',', $res));
        } else {
            return [];
        }
    }
    /*
     * Returns the ID of the category with the same URL key as passed
     * @param string $urlKey URL key
     * @result int The ID of the category
     */
    public function getIdByUrlKey($urlKey)
    {
        $db = $this->getConnection();
        $select = $db->select()
                ->from(['c' => $this->getMainTable()], 'category_id')
                ->joinLeft(['cs' => $this->getTable('productattachments_category_store')], 'c.category_id=cs.category_id', '')
                ->where('c.category_url_key=?', $urlKey)
                ->where('cs.store_id=?', $this->_storeManager->getStore()->getId())
                ->limit(1);
        return $db->fetchOne($select);
    }
    public function deleteNodeRecursive($node_id)
    {
        $db = $this->getConnection();
        $productcats = $this->_objectManager->create('FME\Productattachments\Model\Productcats');
        $data = $productcats->load($node_id);

        if ($data['category_url_key'] == 'Default_Category') {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Default root category can\'t be deleted'));
        }
        /* Check If Category Has Children */
        $select_child = $db->select()
                ->from($this->getMainTable())
                ->where('parent_category_id=?', $node_id)
                ->group('category_id');
        $result_child = $db->fetchAll($select_child);
        if (!empty($result_child)) {
            $msg = "Error While Deleting This Category Because It Have Childrens";
            return $msg;
        }
        /* Check If Any Attachment Is Connected With Category */
        $select_attachment = $db->select()
                ->from($this->getTable('productattachments'))
                ->where('cat_id=?', $node_id)
                ->group('productattachments_id');
        $result_attachment = $db->fetchAll($select_attachment);
        if (!empty($result_attachment)) {
            $msg = "Error While Deleting This Category Because It is connected with Attachments";
            return $msg;
        }
        try {
            $db->beginTransaction();
            $db->exec("DELETE FROM " . $this->getTable('productattachments_cats') . " WHERE category_id = $node_id");
            $db->exec("DELETE FROM " . $this->getTable('productattachments_category_store') . " WHERE category_id = $node_id");
            $db->commit();
            $msg = "";
        } catch (\Exception $e) {
            $db->rollBack();
            throw new LocalizedException($e);
        }
    }
    /**
     * Check if productattachment identifier exist for specific store
     * return productattachment id if productattachment exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
                ->columns('pc.category_id')
                ->order('pcs.store_id DESC')
                ->limit(1);
        return $this->getConnection()
                        ->fetchOne($select);
    }
    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    private function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['pc' => $this->getMainTable()]
        )->join(
            ['pcs' => $this->getTable('productattachments_category_store')],
            'pc.category_id = pcs.category_id',
            []
        )->where(
            'pc.category_url_key = ?',
            $identifier
        )->where(
            'pcs.store_id IN (?)',
            $store
        );

        if ($isActive != null) {
            $select->where('pc.status = ?', $isActive);
        }
        return $select;
    }
    public function setIsPkAutoIncrement($flag)
    {
        $this->_isPkAutoIncrement = $flag;
    }
}
