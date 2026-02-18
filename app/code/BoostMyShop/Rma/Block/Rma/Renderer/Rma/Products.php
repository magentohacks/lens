<?php

namespace BoostMyShop\Rma\Block\Rma\Renderer\Rma;

use Magento\Framework\DataObject;

class Products extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $rma)
    {
        $html = [];

        foreach ($rma->getAllItems() as $item) {
            if ($item->getRiQty())
                $html[] .= $this->renderItem($item);
        }

        return implode('<br>', $html);
    }

    public function renderItem($item)
    {
        $qty = $item->getRiQty();
        return ((int)$qty).'x '.$item->getRiName();
    }
}