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

namespace Webkul\Quotesystem\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Upgrade Data script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /**
         * insert sellerstorepickup controller's data
         */
        $data = [];
        // convert serialized data to json format
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('wk_quotes'),
                'entity_id',
                'product_option'
            );
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('wk_quotes'),
                'entity_id',
                'links'
            );
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('wk_quotes'),
                'entity_id',
                'bundle_option'
            );
            $fieldsToUpdate[] = new FieldToConvert(
                SerializedToJson::class,
                $setup->getTable('wk_quotes'),
                'entity_id',
                'super_attribute'
            );
            $this->aggregatedFieldConverter->convert($fieldsToUpdate, $setup->getConnection());
        }

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'quote_status'
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'quote_status',
            [
                'label' => 'Quote Status',
                'type' => 'int',
                'input' => 'select',
                'group' => 'Product Details',
                'source' => \Webkul\Quotesystem\Model\Product\Attribute\Options ::class,
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
        $setup->endSetup();
    }
}
