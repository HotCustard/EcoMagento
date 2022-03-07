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
namespace FME\Productattachments\Model\ResourceModel\Productattachments\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use FME\Productattachments\Model\ResourceModel\Productattachments\Collection as ProductattachmentsCollection;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Collection
 * Collection for displaying grid of sales documents
 */
class Collection extends ProductattachmentsCollection implements SearchResultInterface
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
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $eventPrefixProductattachments
     * @param mixed                  $eventObjectProductattachments
     * @param mixed                                                        $resourceModel
     * @param string                                                       $model
     * @param null                                                         $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb|null         $resource
     *
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $coreresource,
        $mainTable,
        $eventPrefixProductattachments,
        $eventObjectProductattachments,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
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
        $this->_coreresource       = $coreresource;
        $productattachments_stores = $this->_coreresource->getTableName('productattachments_store');
        $this->getSelect()
            // ->distinct(true)
             // ->columns([
             // 'pa_id' => new \Zend_Db_Expr("distinct(main_table.productattachments_id)")
             // 'main_table.productattachments_id'
             // ])
        ->join(
            ['store_tbl' => $productattachments_stores],
            'main_table.productattachments_id = store_tbl.productattachments_id'
        )->group('main_table.productattachments_id');
            // echo $this->getSelect();exit;
        $this->setMainTable($mainTable);
        $this->_init($model, $resourceModel);
        $this->_eventPrefix = $eventPrefixProductattachments;
        $this->_eventObject = $eventObjectProductattachments;
    }//end __construct()
    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }
    public function getSearchCriteria()
    {
        return null;
    }
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }
    public function getTotalCount()
    {
        return $this->getSize();
    }
    public function setTotalCount($totalCount)
    {
        return $this;
    }
    public function setItems(array $items = null)
    {
        return $this;
    }
}//end class
