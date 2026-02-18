<?php

namespace BoostMyShop\Supplier\Block\Replenishment\Renderer;


class Sku extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{


    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '<a href="'.$this->getUrl('catalog/product/edit', ['id' => $row->getId()]).'">'.$row->getSku().'</a>';
        return $html;
    }

}