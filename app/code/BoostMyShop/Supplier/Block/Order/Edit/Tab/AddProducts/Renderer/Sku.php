<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer;

use Magento\Framework\DataObject;

class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(DataObject $row)
    {
        $url = $this->getUrl('catalog/product/edit', ['id' => $row->getentity_id()]);
        $html = '<a href="'.$url.'">'.$row->getsku().'</a>';

        return $html;
    }
}