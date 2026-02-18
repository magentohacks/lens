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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * class InstallData
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $data = [
            [
                'Template 01',
                'template01',
                'a12a676590711f8ee8c5c3700e3d9a2c',
                1,
                'Magestore_Pdfinvoiceplus::images/default-template/template01.jpg',
            ],
            [
                'Template 02',
                'template02',
                'e4b84d299ec8e24adb1a697cbf786d09',
                2,
                'Magestore_Pdfinvoiceplus::images/default-template/template02.jpg',
            ],
        ];

        $columns = ['name', 'code', 'secret_key', 'sort_order', 'image'];
        $setup->getConnection()->insertArray(
            $setup->getTable(\Magestore\Pdfinvoiceplus\Setup\InstallSchema::SCHEMA_TEMPLATE_TYPE),
            $columns,
            $data
        );

        $installer->endSetup();
    }
}
