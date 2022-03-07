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
namespace FME\Productattachments\Model\ResourceModel\Products;

use \FME\Productattachments\Model\ResourceModel\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'product_id';
    protected $_previewFlag;
    protected function _construct()
    {
        $this->_init('FME\Productattachments\Model\Products', 'FME\Productattachments\Model\ResourceModel\Products');
        $this->_map['fields']['product_id'] ='main_table.product_id';
    }
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->group('product_id');
        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = secondTable.entity_id',
            ['main_table.product_id','main_table.productattachments_id','secondTable.entity_id','secondTable.sku']
        )->join(
            ['thirdtable' => $this->getTable('productattachments')],
            'thirdtable.productattachments_id = main_table.productattachments_id'
        );
    }
    protected function _afterLoad()
    {
    }
}
