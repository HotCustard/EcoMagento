<?php
/**
 * Quote InstallData.php
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory.
     *
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Init.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'quote_status',
            [
                'label' => 'Quote Status',
                'input' => 'select',
                'group' => 'Product Details',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean ::class,
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend ::class,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => true,
                'default' => 0,
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => true,
                'note' => 'Quote enable on this product or not',
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'min_quote_qty',
            [
                'label' => 'Minimum Quote Quantity',
                'input' => 'text',
                'group' => 'Product Details',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean ::class,
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend ::class,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'is_configurable' => false,
                'searchable' => true,
                'default' => '',
                'filterable' => true,
                'comparable' => true,
                'visible_in_advanced_search' => false,
                'note' => 'Minimum Quote quantity for this product',
                'apply_to' => 'simple,downloadable,virtual,bundle,configurable',
            ]
        );
    }
}
