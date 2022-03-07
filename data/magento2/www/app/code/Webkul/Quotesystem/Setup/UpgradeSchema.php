<?php
/**
 * Quote InstallSchema
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Update tables 'wk_quotes'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_quotes'),
            'attachments',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '',
                'comment' => 'attachments'
            ]
        );
        /**
         * Update tables 'wk_quote_conversation'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_quote_conversation'),
            'attachments',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '',
                'comment' => 'attachments'
            ]
        );

        /**
         * Update tables 'wk_quotes'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('wk_quotes'),
            'quote_currency',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'quote currency'
            ]
        );

        $setup->endSetup();
    }
}
