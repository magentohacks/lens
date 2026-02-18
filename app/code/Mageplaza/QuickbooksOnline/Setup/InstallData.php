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
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class InstallData
 * @package Mageplaza\QuickbooksOnline\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * @var SalesSetup $salesInstaller
         */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
        $salesInstaller->addAttribute(
            'order',
            'quickbooks_entity',
            [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]
        );
        $salesInstaller->addAttribute(
            'order',
            'quickbooks_sync_token',
            [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]
        );
        $salesInstaller->addAttribute(
            'invoice',
            'quickbooks_entity',
            [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]
        );
        $salesInstaller->addAttribute(
            'invoice',
            'quickbooks_sync_token',
            [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]
        );
        $salesInstaller->addAttribute(
            'creditmemo',
            'quickbooks_entity',
            [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]
        );
        $salesInstaller->addAttribute(
            'creditmemo',
            'quickbooks_sync_token',
            [
                'type'    => Table::TYPE_TEXT,
                'visible' => false
            ]
        );
        $entityDefinition    = [
            'type'    => Table::TYPE_TEXT,
            'comment' => 'Quickbooks Entity',
        ];
        $syncTokenDefinition = [
            'type'    => Table::TYPE_TEXT,
            'comment' => 'Quickbooks Entity',
        ];

        $connection = $installer->getConnection();
        $connection->addColumn($installer->getTable('customer_entity'), 'quickbooks_entity', $entityDefinition);
        $connection->addColumn($installer->getTable('catalog_product_entity'), 'quickbooks_entity', $entityDefinition);
        $connection->addColumn($installer->getTable('tax_calculation_rate'), 'quickbooks_entity', $entityDefinition);
        $connection->addColumn($installer->getTable('customer_entity'), 'quickbooks_sync_token', $syncTokenDefinition);
        $connection->addColumn(
            $installer->getTable('catalog_product_entity'),
            'quickbooks_sync_token',
            $syncTokenDefinition
        );
    }
}
