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

class Productcats extends \Magento\Framework\Model\AbstractModel
{
    /*
        * #@+
        * Page's Statuses
     */
    protected $_customergroup;
    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;
    /*
        * #@+
        * Page's Statuses
     */
    const STATUS_YES = 1;
    const STATUS_NO  = 0;
    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \FME\Productattachments\Model\ResourceModel\Productcats $resource_m, \FME\Productattachments\Model\ResourceModel\Productcats\Collection $resourceCollection,\Magento\Customer\Model\Group $customer_group
    )
    {
        $this->_customergroup = $customer_group;
        parent::__construct(
            $context,
            $registry,
            $resource_m,
            $resourceCollection
        );
    }//end __construct()
    public function _construct()
    {
        $this->_init('FME\Productattachments\Model\ResourceModel\Productcats');
    }//end _construct()
    /*
        * Add Parent Category Check
        * @return Array
     */
    public function addParnetCategory($left_node, $new_node)
    {
        return $this->getResource()->addNode($left_node, $new_node);
    }//end addParnetCategory()
    /*
        * Add Child Category Check
        * @return Array
     */
    public function addChildCategory($left_node)
    {
        return $this->getResource()->addChildNode($left_node, ($left_node + 1));
    }//end addChildCategory()
    /*
        * Delete Category
        * @return Array
     */
    public function deleteCategory($nodeId)
    {
        return $this->getResource()->deleteNodeRecursive($nodeId);
    }//end deleteCategory()
    /*
        * Update Status Of Category
        * @return Array
     */
    public function changeStatus($node_id, $status)
    {
        return $this->getResource()->setNodeStatusRecursive($node_id, $status);
    }//end changeStatus()
    /*
        * Update Status Of Category
        * @return Array
     */
    public function getChilderns($node_name)
    {
        return $this->getResource()->getLocalSubNodes($node_name);
    }//end getChilderns()
    /*
        * Ger Parent ID
        * @return Array
     */
    public function getParentID($node_id)
    {
        return $this->getResource()->getParentNodeID($node_id);
    }//end getParentID()
    public function getGridData()
    {
        return $this->getResource()->getGrid();
    }//end getGridData()
    /*
        * Checks whether there is a category with the same URL key among the stores the category belongs to
        * @return bool
     */
    public function isUrlKeyUsed()
    {
        $storeIds = $this->getCategoryStoreIds();
        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }
        $sameUrlCategoryStoreIds = $this->getResource()->getSameUrlCategoryStoreIds($this->getId(), $this->getCategoryUrlKey());
        $res = array_intersect($storeIds, $sameUrlCategoryStoreIds);
        return !empty($res);
    }//end isUrlKeyUsed()
    protected function _afterLoad()
    {
        if (($storeIds = $this->getCategoryStoreIds()) == null) {
            $this->setCategoryStoreIds($this->getResource()->getStoreIds($this->getId()));
        } elseif (!is_array($storeIds)) {
            $this->setCategoryStoreIds(array_unique(explode(',', $storeIds)));
        }
        return parent::_afterLoad();
    }//end _afterLoad()
    public function afterLoad()
    {
        $this->_afterLoad();

        
    }//end afterLoad()
    /*
        * Loads itself using the URL key parameter
        * @param string $urlKey URL key used to identify the category
     */
    public function loadByUrlKey($urlKey)
    {
        $id = $this->getResource()->getIdByUrlKey($urlKey);
        return $this->load($id);
    }//end loadByUrlKey()
    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
                self::STATUS_ENABLED  => __('Enabled'),
                self::STATUS_DISABLED => __('Disabled'),
               ];
    }//end getAvailableStatuses()
    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getYesNo()
    {
        return [
                self::STATUS_YES => __('Yes'),
                self::STATUS_NO  => __('No'),
               ];
    }//end getYesNo()
    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
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
    public function getcats()
    {
            $cats = [];

           $collection = $this->getCollection()->addFieldToFilter('status', 1);
            $topicList = [];
            $i         = 0;
        foreach ($collection as $data) {
            $cats[$i] = [
                         'value' => $data->getCategory_id(),
                         'label' => __($data->getCategory_name()),
                        ];
            $i++;
        }

            return $cats;
    }//end getcats()
    public function getcgroups()
    {
        $groups_array = [];
        $allGroups    = $this->_customergroup->getCollection()->toOptionHash();
        foreach ($allGroups as $key => $allGroup) {
            $groups_array[] = [
                               'value' => $key,
                               'label' => $allGroup,
                              ];
        }

        return $groups_array;
    }//end getcgroups()
}//end class
