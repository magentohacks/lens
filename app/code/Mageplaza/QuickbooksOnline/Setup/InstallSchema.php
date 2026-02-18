<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\QuickbooksOnline\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        if (!$installer->tableExists('mageplaza_quickbooks_sync')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_quickbooks_sync'))
                ->addColumn(
                    'sync_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'sync_id'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Name'
                )
                ->addColumn(
                    'magento_object',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Magento Object'
                )
                ->addColumn(
                    'quickbooks_module',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Quickbooks Module'
                )
                ->addColumn(
                    'website_ids',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Website Ids'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    64,
                    ['nullable' => false],
                    'Status'
                )
                ->addColumn(
                    'pending_queue',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Pending Queue'
                )
                ->addColumn(
                    'mapping',
                    Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => false],
                    'Mapping'
                )
                ->addColumn(
                    'conditions_serialized',
                    Table::TYPE_TEXT,
                    '2M'
                )
                ->addColumn(
                    'priority',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default'  => 0
                    ]
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->setComment('Mageplaza Quickbooks Sync');
            $connection->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_quickbooks_queue')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_quickbooks_queue'))
                ->addColumn(
                    'queue_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'queue_id'
                )
                ->addColumn(
                    'object',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Object'
                )
                ->addColumn(
                    'sync_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'sync_id'
                )
                ->addColumn(
                    'magento_object',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Magento Object'
                )
                ->addColumn(
                    'quickbooks_module',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Quickbooks Module'
                )
                ->addColumn(
                    'website',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Website'
                )
                ->addColumn(
                    'action',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Action'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    64,
                    ['nullable' => false],
                    'Status'
                )
                ->addColumn(
                    'total_sync',
                    Table::TYPE_SMALLINT,
                    64,
                    ['nullable' => false],
                    'Total Sync'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At'
                )
                ->addColumn(
                    'json_response',
                    Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => false],
                    'json Response'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_quickbooks_queue',
                        'sync_id',
                        'mageplaza_quickbooks_sync',
                        'sync_id'
                    ),
                    'sync_id',
                    $installer->getTable('mageplaza_quickbooks_sync'),
                    'sync_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Mageplaza Quickbooks Queue');
            $connection->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_quickbooks_payment_method')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_quickbooks_payment_method'))
                ->addColumn(
                    'method_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Payment Method ID'
                )
                ->addColumn(
                    'code',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Code'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Title'
                )
                ->addColumn(
                    'quickbooks_entity',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Quickbooks Entity'
                )
                ->addColumn(
                    'quickbooks_sync_token',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Quickbooks Sync Token'
                )
                ->setComment('Mageplaza Quickbooks Payment Method Sync');
            $connection->createTable($table);
        }

        $installer->endSetup();
    }
}
