<?php

namespace BoostMyShop\AdvancedStock\Block\StockMovement\Renderer;

use Magento\Framework\DataObject;

class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        $url = $this->getUrl('catalog/product/edit', ['id' => $row->getsm_product_id(), 'active_tab' => 'product-advancedstock']);
        return '<a href="'.$url .'">'.$row->getsku().'</a>';
    }
}