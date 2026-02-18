<?php

namespace BoostMyShop\Supplier\Block\ProductSupplier\Renderer;

use Magento\Framework\DataObject;

class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        $url = $this->getUrl('catalog/product/edit', ['id' => $row->getId(), 'active_tab' => 'product-supplier']);
        return '<a href="'.$url .'">'.$row->getsku().'</a>';
    }

    public function renderExport(DataObject $row)
    {
        return $row->getsku();
    }

}