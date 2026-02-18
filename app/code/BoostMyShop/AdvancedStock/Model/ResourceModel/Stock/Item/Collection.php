<?php

namespace BoostMyShop\AdvancedStock\Model\ResourceModel\Stock\Item;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magento\CatalogInventory\Model\Stock\Item', 'Magento\CatalogInventory\Model\ResourceModel\Stock\Item');
    }

    public function addProductFilter($productId)
    {
        $this->getSelect()->where('product_id = '.$productId);
        return $this;
    }

    public function joinWebsite()
    {
        $this->getSelect()->join(
            ['tbl_website' => $this->getTable('store_website')],
            'main_table.website_id = tbl_website.website_id',
            ['code', 'name']
        );

        return $this;
    }

}