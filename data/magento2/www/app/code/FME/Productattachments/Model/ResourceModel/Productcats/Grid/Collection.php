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
namespace FME\Productattachments\Model\ResourceModel\Productcats\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use FME\Productattachments\Model\ResourceModel\Productcats\Collection as ProductcatsCollection;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Collection
 * Collection for displaying grid of sales documents
 */
class Collection extends ProductcatsCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregationss;
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param mixed|null                                                   $mainTable
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb              $eventPrefix
     * @param mixed                                                        $eventObject
     * @param mixed                                                        $resourceModel
     * @param string                                                       $model
     * @param null                                                         $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb|null         $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $coreresource,
        $mainTable,
        $eventPrefixProductcats,
        $eventObjectProductcats,
        $resourceModel,
        $productcatsModel = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource_m = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource_m
        );
        $this->_coreresource                = $coreresource;
        $productattachments_category_stores = $this->_coreresource->getTableName('productattachments_category_store');
        $this->getSelect()->join(
            ['store_tbl' => $productattachments_category_stores],
            'main_table.category_id = store_tbl.category_id'
        )->group('main_table.category_id');
        $this->_init($productcatsModel, $resourceModel);
        $this->setMainTable($mainTable);
        $this->_eventPrefix = $eventPrefixProductcats;
        $this->_eventObject = $eventObjectProductcats;
    }//end __construct()
    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregationss;
    }//end getAggregations()
    /**
     * @param AggregationInterface $aggregationss
     * @return $this
     */
    public function setAggregations($aggregationss)
    {
        $this->aggregationss = $aggregationss;
    }//end setAggregations()
    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }//end getSearchCriteria()
    /**
     * Set search criteria.
     *
     * @param                                         \Magento\Framework\Api\SearchCriteriaInterface $searchCriteriaa
     * @return                                        $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteriaa = null)
    {
        return $this;
    }//end setSearchCriteria()
    /**
     * Get total count.
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }//end getTotalCount()
    /**
     * Set total count.
     *
     * @param                                         integer $totalCount
     * @return                                        $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCountt)
    {
        return $this;
    }//end setTotalCount()
    /**
     * Set items list.
     *
     * @param                                         \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return                                        $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $itemss = null)
    {
        return $this;
    }//end setItems()
}//end class
