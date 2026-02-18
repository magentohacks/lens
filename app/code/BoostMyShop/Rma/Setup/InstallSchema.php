<?php

namespace BoostMyShop\Rma\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $setup->getConnection()
            ->newTable($setup->getTable('bms_rma'))
            ->addColumn('rma_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id')
            ->addColumn('rma_created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [ 'unsigned' => true, 'nullable' => false, ], 'Created at')
            ->addColumn('rma_updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [ 'unsigned' => true, 'nullable' => false, ], 'Updated at')
            ->addColumn('rma_expire_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [ 'unsigned' => true, 'nullable' => false, ], 'Expire at')
            ->addColumn('rma_status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, [ 'unsigned' => true, 'nullable' => false, ], 'Status')
            ->addColumn('rma_reference', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, [ 'unsigned' => true, 'nullable' => false, ], 'Reference')
            ->addColumn('rma_customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => true, ], 'Customer')
            ->addColumn('rma_customer_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [ 'unsigned' => true, 'nullable' => false, ], 'Customer name')
            ->addColumn('rma_customer_email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [ 'unsigned' => true, 'nullable' => false, ], 'Customer email')
            ->addColumn('rma_shipping_address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 400, [ 'unsigned' => true, 'nullable' => false, ], 'Shipping address')
            ->addColumn('rma_order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => true, ], 'Order')
            ->addColumn('rma_store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Store id')
            ->addColumn('rma_manager', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Manager')
            ->addColumn('rma_customer_comments', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 400, [ 'unsigned' => true, 'nullable' => true, ], 'Customer comments')
            ->addColumn('rma_private_comments', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 400, [ 'unsigned' => true, 'nullable' => true, ], 'Private comments')
            ->addColumn('rma_public_comments', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 400, [ 'unsigned' => true, 'nullable' => true, ], 'Public comments')
            ->addColumn('rma_currency_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 5, [ 'unsigned' => true, 'nullable' => true, ], 'Currency code')
            ->addIndex($setup->getIdxName('bms_rma', ['rma_customer_id']), ['rma_customer_id'])
            ->addIndex($setup->getIdxName('bms_rma', ['rma_order_id']), ['rma_order_id'])
            ->setComment('Rma');
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable('bms_rma_item'))
            ->addColumn('ri_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id')
            ->addColumn('ri_rma_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Rma ID')
            ->addColumn('ri_qty', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Qty')
            ->addColumn('ri_product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Product ID')
            ->addColumn('ri_order_item_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => true, ], 'Order Item ID')
            ->addColumn('ri_sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [ 'unsigned' => true, 'nullable' => true, ], 'Sku')
            ->addColumn('ri_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [ 'unsigned' => true, 'nullable' => true, ], 'Product name')
            ->addColumn('ri_reason', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [ 'unsigned' => true, 'nullable' => true, ], 'Reason for return')
            ->addColumn('ri_request', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [ 'unsigned' => true, 'nullable' => true, ], 'Request')
            ->addColumn('ri_comments', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 400, [ 'unsigned' => true, 'nullable' => true, ], 'Comments')
            ->addIndex($setup->getIdxName('bms_rma_item', ['ri_rma_id']), ['ri_rma_id'])
            ->setComment('Rma item');
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable('bms_rma_history'))
            ->addColumn('rh_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id')
            ->addColumn('rh_rma_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Rma ID')
            ->addColumn('rh_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [ 'unsigned' => true, 'nullable' => false, ], 'Date')
            ->addColumn('rh_details', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 400, [ 'unsigned' => true, 'nullable' => false, ], 'Details')
            ->addIndex($setup->getIdxName('bms_rma_history', ['rh_rma_id']), ['rh_rma_id'])
            ->setComment('Rma history');
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable('bms_rma_messages'))
            ->addColumn('rmm_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id')
            ->addColumn('rmm_rma_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [ 'unsigned' => true, 'nullable' => false, ], 'Rma ID')
            ->addColumn('rmm_date', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [ 'unsigned' => true, 'nullable' => false, ], 'Date')
            ->addColumn('rmm_author', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 10, [ 'unsigned' => true, 'nullable' => false, ], 'author')
            ->addColumn('rmm_message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2000, [ 'unsigned' => true, 'nullable' => false, ], 'Message')
            ->addIndex($setup->getIdxName('bms_rma_messages', ['rmm_rma_id']), ['rmm_rma_id'])
            ->setComment('Rma messages');
        $setup->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
