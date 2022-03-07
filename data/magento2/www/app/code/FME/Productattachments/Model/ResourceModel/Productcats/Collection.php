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
namespace FME\Productattachments\Model\ResourceModel\Productcats;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_previewFlag;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource_m = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource_m);
        $this->_storeManager = $storeManager;
    }
    public function _construct()
    {
        $this->_init('\FME\Productattachments\Model\Productcats', '\FME\Productattachments\Model\ResourceModel\Productcats');
        $this->_map['fields']['category_id'] = 'main_table.category_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('category_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('productattachments_category_store')])
                    ->where('cps.category_id IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $pageId = $item->getData('category_id');
                    if (!isset($result[$pageId])) {
                        continue;
                    }
                    if ($result[$pageId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData('category_id')];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$pageId]]);
                }
            }
        }
        $this->_previewFlag = false;
        return parent::_afterLoad();
    }
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Zend_Db_Select::ORDER);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(\Zend_Db_Select::COLUMNS);
        $idsSelect->reset(\Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.' . $this->getResource()->getIdFieldName());
        return $this->getConnection()->fetchCol($idsSelect);
    }
    public function addStatusFilter($enabled = 1)
    {
        $this->getSelect()
                ->where('main_table.status=?', (int) $enabled);
        return $this;
    }
    public function addPortfolioFilter($id = 0)
    {
        $this->getSelect()
                ->where('link.news_id=?', (int) $id);

        return $this;
    }
    public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\StoreManagerInterface) {
            $store = [$store->getId()];
        }  
        $conditions[] = $this->getConnection()->quoteInto('store_table.store_id in (?)', [0, $store]);
        $conditions[] = ('store_table.category_id = main_table.category_id');
        // print_r(join(' AND ', $conditions)) ;exit;
        $this->getSelect()->join(
            ['store_table' => $this->getTable('productattachments_category_store')],
            join(' AND ', $conditions),
            []
        )->group('main_table.category_id');
        return $this;
    }
}
