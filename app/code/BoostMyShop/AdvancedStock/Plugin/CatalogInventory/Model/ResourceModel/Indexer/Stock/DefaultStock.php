<?php

namespace BoostMyShop\AdvancedStock\Plugin\CatalogInventory\Model\ResourceModel\Indexer\Stock;

class DefaultStock extends \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock
{
    public function aroundReindexAll(\Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock $subject, $proceed)
    {
        $this->setTypeId($subject->getTypeId());

        $this->tableStrategy->setUseIdxTable(true);
        $this->beginTransaction();
        try {
            $this->_prepareIndexTable();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    public function aroundReindexEntity(\Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock $subject, $proceed, $entityIds)
    {
        $this->setTypeId($subject->getTypeId());

        $this->_updateIndex($entityIds);
        return $this;
    }

    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $connection = $this->getConnection();
        $qtyExpr = $connection->getCheckSql('cisi.qty > 0', 'cisi.qty', 0);
        $select = $connection->select()->from(
            ['e' => $this->getTable('catalog_product_entity')],
            ['entity_id']
        );
        $select->join(
            ['cis' => $this->getTable('cataloginventory_stock')],
            '',
            ['stock_id']
        )->joinInner(
            ['cisi' => $this->getTable('cataloginventory_stock_item')],
            'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id',
            ['website_id']
        )->columns(
            ['qty' => $qtyExpr]
        )
        //    ->where(
        //    'cis.website_id = ?',
        //    $this->getStockConfiguration()->getDefaultScopeId()
        //)
            ->where('e.type_id = ?', $this->getTypeId())
            ->group(['e.entity_id', 'cisi.website_id', 'cis.stock_id']);

        $select->columns(['status' => $this->getStatusExpression($connection, true)]);
        if ($entityIds !== null) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        return $select;
    }

}