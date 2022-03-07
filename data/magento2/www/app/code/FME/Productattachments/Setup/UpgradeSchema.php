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
namespace FME\Productattachments\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $setup->startSetup();
        

        $tableCats = $setup->getTable('productattachments_cats');
        $tableCatsStore = $setup->getTable('productattachments_category_store');
        $tableProductattachmentsStore = $setup->getTable('productattachments_store');

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn($tableCats, 'path', [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'path column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'level', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'category level column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'children_counts', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'child count column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'is_visible_front', [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 1,
                'comment' => 'visible on front page column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'is_visible_prod', [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 1,
                'comment' => 'visible on product page column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'meta_title', [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'meta title column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'meta_desc', [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'meta desc column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'meta_keywords', [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'meta keywords column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'created_at', [
                'type' => Table::TYPE_DATETIME,
                'nullable' => false,
                'comment' => 'created at column'
            ]);

            $setup->getConnection()->addColumn($tableCats, 'updated_at', [
                'type' => Table::TYPE_DATETIME,
                'nullable' => false,
                'comment' => 'updated at column'
            ]);
        }
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->getConnection()
                ->addIndex(
                    $tableProductattachmentsStore,
                    'PRIMARY',
                    ['productattachments_id', 'store_id'],
                    AdapterInterface::INDEX_TYPE_PRIMARY
                );
        }
        
        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $setup->getConnection()
                ->addIndex(
                    $tableCatsStore,
                    'PRIMARY',
                    ['category_id','store_id'],
                    AdapterInterface::INDEX_TYPE_PRIMARY
                );
        }

        if (version_compare($context->getVersion(), '1.2.8', '<=')) {
            $table = $installer->getConnection()
            ->newTable($installer->getTable('productattachments_cms'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'productattachments_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Productattachments Id'
            )
            ->addColumn(
                'cms_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'CMS Id'
            )
            ->addForeignKey(
                $installer->getFkName('productattachments_cms_ibfk_1', 'productattachments_id', 'productattachments', 'productattachments_id'),
                'productattachments_id',
                $installer->getTable('productattachments'),
                'productattachments_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Product attachemnts cms Table');
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.4.1', '<')) {
            $table = $installer->getConnection()
            ->newTable($installer->getTable('productattachments_extensions'))
            ->addColumn(
                'extension_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Extension type'
            )
            ->addColumn(
                'icon',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => '', 'nullable' => false],
                'Extension icon'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'status'
            )
            ->setComment('Product attachemnts Extensions Table');
            $installer->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.4.1', '<')) {
             $fme_faq_table = $installer->getTable('productattachments');
                                
                  $installer->getConnection()->addColumn(
                      $fme_faq_table,
                      'product_names',
                      [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                         'nullable' => true,
                            'comment' => 'Product name'
                                                    ]
                  );
        }
        $setup->endSetup();
    }
}
