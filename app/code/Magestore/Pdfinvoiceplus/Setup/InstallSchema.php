<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * class InstallSchema
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Schema table.
     */
    const SCHEMA_TEMPLATE_TYPE = 'magestore_pdfinvoiceplus_template_type';
    const SCHEMA_TEMPLATE = 'magestore_pdfinvoiceplus_template';

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Drop table if
         */
        $installer->getConnection()->dropTable($installer->getTable(self::SCHEMA_TEMPLATE));
        $installer->getConnection()->dropTable($installer->getTable(self::SCHEMA_TEMPLATE_TYPE));

        $installer->startSetup();
        /*
         * Create table magestore_pdfinvoiceplus_system_template
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::SCHEMA_TEMPLATE_TYPE)
        )->addColumn(
            'type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Template type Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Template Type Name'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Template Type Code'
        )->addColumn(
            'secret_key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Secret Key'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Secret Key'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Template Type Image'
        )->addIndex(
            $installer->getIdxName(
                $installer->getTable(self::SCHEMA_TEMPLATE_TYPE),
                ['name'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['name'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $installer->getIdxName(
                $installer->getTable(self::SCHEMA_TEMPLATE_TYPE),
                ['code'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['code'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment(
            'Template Types'
        );

        $installer->getConnection()->createTable($table);
        /*
         * End create table magestore_pdfinvoiceplus_system_template
         */

        /*
         * Create table magestore_pdfinvoiceplus_template
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::SCHEMA_TEMPLATE)
        )->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Template Id'
        )->addColumn(
            'template_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Template Name'
        )->addColumn(
            'stores',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Stores'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 1],
            'Status'
        )->addColumn(
            'css',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => ''],
            'Css'
        )->addColumn(
            'order_filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Order Filename'
        )->addColumn(
            'invoice_filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Invoice Filename'
        )->addColumn(
            'creditmemo_filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Creditmemo Filename'
        )->addColumn(
            'shipment_filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Shipment Filename'
        )->addColumn(
            'quote_filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Quote Filename'
        )->addColumn(
            'vat_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Vat Number'
        )->addColumn(
            'format',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 'Letter'],
            'Format'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'footer',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => ''],
            'Footer'
        )->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => ''],
            'note'
        )->addColumn(
            'color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Color'
        )->addColumn(
            'company_logo',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Company Logo'
        )->addColumn(
            'company_address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => ''],
            'Company Address'
        )->addColumn(
            'company_fax',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Company Fax'
        )->addColumn(
            'company_telephone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Company Telephone'
        )->addColumn(
            'company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Company Name'
        )->addColumn(
            'company_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Company Email'
        )->addColumn(
            'business_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Business Id'
        )->addColumn(
            'orientation',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 1],
            'Orientation'
        )->addColumn(
            'terms_conditions',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => ''],
            'Terms Conditions'
        )->addColumn(
            'barcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 1],
            'Barcode'
        )->addColumn(
            'barcode_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Barcode Type'
        )->addColumn(
            'display_images',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            255,
            ['nullable' => false, 'unsigned' => true, 'default' => 1],
            'Display Images'
        )->addColumn(
            'vat_office',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => ''],
            'Vat Office'
        )->addColumn(
            'barcode_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Barcode Order'
        )->addColumn(
            'barcode_invoice',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Barcode Invoice'
        )->addColumn(
            'barcode_creditmemo',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Barcode Creditmemo'
        )->addColumn(
            'barcode_shipment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Barcode Shipment'
        )->addColumn(
            'barcode_quote',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['default' => ''],
            'Barcode Quote'
        )->addColumn(
            'template_type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 1],
            'Template Type Id'
        )->addColumn(
            'localization',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 'default'],
            'localization'
        )->addColumn(
            'footer_height',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 60],
            'Footer Height'
        )->addColumn(
            'order_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false, 'default' => ''],
            'Order Html'
        )->addColumn(
            'invoice_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false, 'default' => ''],
            'Invoice Html'
        )->addColumn(
            'creditmemo_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false, 'default' => ''],
            'Creditmemo Html'
        )->addColumn(
            'shipment_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false, 'default' => ''],
            'Shipment Html'
        )->addColumn(
            'quote_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false, 'default' => ''],
            'Quote Html'
        )->addIndex(
            $installer->getIdxName(
                $installer->getTable(self::SCHEMA_TEMPLATE),
                ['template_type_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['template_type_id'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addForeignKey(
            $installer->getFkName(
                self::SCHEMA_TEMPLATE,
                'template_type_id',
                self::SCHEMA_TEMPLATE_TYPE,
                'type_id'
            ),
            'template_type_id',
            $installer->getTable(self::SCHEMA_TEMPLATE_TYPE),
            'type_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'PDF Template Table'
        );

        $installer->getConnection()->createTable($table);
        /*
         * End create table magestore_pdfinvoiceplus_template
         */

        $installer->endSetup();
    }
}
