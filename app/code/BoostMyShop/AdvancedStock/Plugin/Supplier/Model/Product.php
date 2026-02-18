<?php

namespace BoostMyShop\AdvancedStock\Plugin\Supplier\Model;

//Rewrite the way stocks details are rendered
class Product
{
    protected $_warehouseItemCollectionFactory;

    public function __construct(
        \BoostMyShop\AdvancedStock\Model\ResourceModel\Warehouse\Item\CollectionFactory $warehouseItemCollection
    ){
        $this->_warehouseItemCollectionFactory = $warehouseItemCollection;
    }

    public function aroundGetStockDetails(\BoostMyShop\Supplier\Model\Product $subject, $proceed, $productId)
    {
        $html = [];

        if ($subject->productIsDeleted($productId))
            return 'Product deleted';

        $collection = $this->_warehouseItemCollectionFactory->create()->addProductFilter($productId)->joinWarehouse();
        foreach($collection as $item)
        {
            $color = $this->getColor($item);
            $row = '<font color="'.$color.'">';
            $row .= $item->getw_name().' : '.$item->getwi_available_quantity().'/'.$item->getwi_physical_quantity();

            $details = [];
            if ($item->getwi_quantity_to_ship() > 0)
                $details[] = $item->getwi_quantity_to_ship().' to ship';
            if ($item->getQtyNeededForIdealStockLevel() > 0)
                $details[] = $item->getQtyNeededForIdealStockLevel().' for ideal';
            if (count($details) > 0)
                $row .= ' <i>('.implode(', ', $details).')</i>';

            $row .= '</font>';
            $html[] = $row;
        }

        return implode('<br>', $html);
    }

    protected function getColor($item)
    {
        if ($item->getwi_available_quantity() <= 0)
            return 'red';

        if ($item->getQtyNeededForOrders() > 0)
            return 'red';

        if ($item->getQtyNeededForIdealStockLevel() > 0)
            return 'orange';

        return 'black';
    }
}