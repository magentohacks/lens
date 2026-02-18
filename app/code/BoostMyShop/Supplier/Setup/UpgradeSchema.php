<?php

namespace BoostMyShop\Supplier\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.4', '<')) {

            $setup->getConnection()->changeColumn(
                $setup->getTable('bms_purchase_order_product'),
                'pop_sku',
                'pop_sku',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('bms_purchase_order_product'),
                'pop_supplier_sku',
                'pop_supplier_sku',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255
                ]
            );

        }

        if (version_compare($context->getVersion(), '0.0.5', '<'))  {

            $setup->getConnection()->changeColumn($setup->getTable('bms_supplier'),
                'sup_minimum_of_order',
                'sup_minimum_of_order',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_supplier'),
                'sup_carriage_free_amount',
                'sup_carriage_free_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );

            $setup->getConnection()->changeColumn($setup->getTable('bms_supplier_product'),
                'sp_price',
                'sp_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_supplier_product'),
                'sp_base_price',
                'sp_base_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );

            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_shipping_cost',
                'po_shipping_cost',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_shipping_cost_base',
                'po_shipping_cost_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_additionnal_cost',
                'po_additionnal_cost',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_additionnal_cost_base',
                'po_additionnal_cost_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_tax',
                'po_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_tax_base',
                'po_tax_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_subtotal',
                'po_subtotal',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_subtotal_base',
                'po_subtotal_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_grandtotal',
                'po_grandtotal',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order'),
                'po_grandtotal_base',
                'po_grandtotal_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );

            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_price',
                'pop_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_price_base',
                'pop_price_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_tax',
                'pop_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_tax_base',
                'pop_tax_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_subtotal',
                'pop_subtotal',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_subtotal_base',
                'pop_subtotal_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_grandtotal',
                'pop_grandtotal',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
            $setup->getConnection()->changeColumn($setup->getTable('bms_purchase_order_product'),
                'pop_grandtotal_base',
                'pop_grandtotal_base',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.6', '<'))  {

            $setup->getConnection()->addColumn(
                $setup->getTable('bms_purchase_order'),
                'po_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                    'comment' => 'PO Type',
                    'default' => 'po'
                ]
            );


        }

        if (version_compare($context->getVersion(), '0.0.7', '<'))  {
            $setup->getConnection()->addColumn(
                $setup->getTable('bms_supplier'),
                'sup_payment_terms',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 30,
                    'nullable' => true,
                    'comment' => 'Supplier payment terms'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.8', '<'))  {
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_supplier_product'),
                $setup->getIdxName('bms_supplier_product', 'sp_product_id', 'sp_sup_id'),
                ['sp_product_id', 'sp_sup_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }

        if (version_compare($context->getVersion(), '0.0.9', '<'))  {

            $setup->getConnection()->addColumn(
                $setup->getTable('bms_supplier_product'),
                'sp_primary',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => null,
                    'nullable' => true,
                    'default'   => 0,
                    'comment' => 'Primary supplier'
                ]
            );

        }

        if (version_compare($context->getVersion(), '0.0.10', '<'))  {
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_purchase_order_product'),
                $setup->getIdxName('bms_purchase_order_product', 'pop_po_id'),
                ['pop_po_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_purchase_order_product'),
                $setup->getIdxName('bms_purchase_order_product', 'pop_product_id'),
                ['pop_product_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_purchase_order_reception'),
                $setup->getIdxName('bms_purchase_order_reception', 'por_po_id'),
                ['por_po_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_purchase_order_reception_item'),
                $setup->getIdxName('bms_purchase_order_reception_item', 'pori_por_id'),
                ['pori_por_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_purchase_order_reception_item'),
                $setup->getIdxName('bms_purchase_order_reception_item', 'pori_product_id'),
                ['pori_product_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_supplier_product'),
                $setup->getIdxName('bms_supplier_product', 'sp_product_id'),
                ['sp_product_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('bms_supplier_product'),
                $setup->getIdxName('bms_supplier_product', 'sp_sup_id'),
                ['sp_sup_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            );
        }

        if (version_compare($context->getVersion(), '0.0.11', '<'))  {

            $setup->getConnection()->addColumn(
                $setup->getTable('bms_purchase_order'),
                'po_shipping_method',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'PO Shipping Method',
                    'default' => ''
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('bms_purchase_order'),
                'po_shipping_tracking',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => true,
                    'comment' => 'PO Tracking number',
                    'default' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.12', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable('bms_purchase_order'),
                'po_invoice_status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                    'comment' => 'PO invoice status',
                    'default' => 'missing'
                ]
            );
        }

        $setup->endSetup();
    }

}
