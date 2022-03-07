<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Block\Adminhtml\Productgrid;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Webkul\Accordionfaq\Model\ImagesFactory
     */
    protected $_suppliers;

    protected $_productloader;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Webkul\Accordionfaq\Model\AddfaqFactory $faqFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_productloader = $_productloader;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('wk_quotesystem_products');
        $this->setDefaultSort('id');
    }
    protected function _prepareCollection()
    {
        $collection = $this->_productloader->create()->getCollection();
        $collection->addAttributeToSelect('*')
                    ->addAttributeToFilter('quote_status', '1')
                    ->addAttributeToFilter('type_id', ['neq'=>'bundle'])
                    ->addAttributeToFilter('visibility', ['in'=>[4]]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header'    => __('Product Id'),
                'align'     => 'left',
                'width'     => '50',
                'index'     => 'entity_id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header'    => __('Name'),
                'align'     =>'left',
                'index'     => 'name',
            ]
        );
        $this->addColumn("sku", [
                "header"    => __("SKU"),
                "width"     => "80",
                "index"     => "sku"
        ]);
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'column_css_class' => 'price',
                'type' => 'currency',
                'index' => 'price',
            ]
        );
        $this->addColumn("action", [
                "header"    =>  __("Action"),
                "width"     => "100",
                "type"      => "action",
                "getter"    => "getEntityId",
                'column_css_class' => 'wk_quotesystem_column_action',
                "actions"   => [
                    [
                        "caption"   => __("Add Quote"),
                        "url"       => ["base"=> "*/*/*"],
                        "field"     => "id"
                    ]
                ],
                "filter"    => false,
                "sortable"  => false
            ]);

        return parent::_prepareColumns();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction()
    {
        return $this->_authorization->isAllowed();
    }
}
