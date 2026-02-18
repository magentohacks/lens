<?php

namespace BoostMyShop\AdvancedStock\Block\Warehouse\Edit\Tab\Renderer;

use Magento\Framework\DataObject;

class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        $url = $this->getUrl('catalog/product/edit', ['id' => $row->getentity_id(), 'active_tab' => 'product-advancedstock']);
        return '<a href="'.$url .'">'.$row->getsku().'</a>';
    }
}