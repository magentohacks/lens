<?php
namespace Lens\Manager\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context) 
    {
        $setup->startSetup();
        $conn = $setup->getConnection();
        /**
         * Create table 'lens_prescriptions'
         */
        $tableName = $setup->getTable('lens_prescriptions');
        if ($conn->isTableExists($tableName) != true) {
            $table = $conn->newTable($tableName)
                ->addColumn(
                   'entity_id',
                   Table::TYPE_INTEGER, 10, 
                   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Batch Id'
                )
                ->addColumn(
                    'gtin', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => null],
                    'GTIN CODE'
                )
                ->addColumn(
                    'quantity',
                    Table::TYPE_SMALLINT,
                    6,
                    ['nullable' => false, 'default' => '0'],
                    'QUANTITY'
                )
                ->addColumn(
                    'product_sku', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'Product Sku'
                )
                ->addColumn(
                    'product_id', 
                    Table::TYPE_INTEGER,
                    10,
                    ['nullable' => true],
                    'Product Id'
                )
                ->addColumn(
                    'addition', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'ADDITION'
                )
                ->addColumn(
                    'axis', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'AXIS'
                )
                ->addColumn(
                    'base_curve', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'BASE_CURVE'
                )
                ->addColumn(
                    'cylinder', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'CYLINDER'
                )
                ->addColumn(
                    'diameter', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'DIAMETER'
                )
                ->addColumn(
                    'dominance', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'DOMINANCE'
                )
                ->addColumn(
                    'power', 
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'POWER'
                )
                ->addColumn(
                    'update_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT,
                    ],
                    'Modification Time'
                )
                ->addIndex(
                    $setup->getIdxName('lens_prescriptions', ['gtin']),
                    ['gtin']
                )
                ->addIndex(
                    $setup->getIdxName('lens_prescriptions', ['quantity']),
                    ['quantity']
                )
                ->addIndex(
                    $setup->getIdxName('lens_prescriptions', ['product_sku']),
                    ['product_sku']
                );
            $conn->createTable($table);                        
            $conn->addIndex('lens_prescriptions', $setup->getIdxName('gtin', ['gtin']), ['gtin'], \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE);
              
        }
       
        $setup->endSetup();
    }
}
