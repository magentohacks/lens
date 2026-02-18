<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer;

use Magento\Framework\DataObject;

class Qty extends AbstractRenderer
{

    public function render(DataObject $row)
    {
        $html = '<input size="6" type="textbox" name="products['.$row->getId().'][qty]" id="products['.$row->getId().'][qty]" value="'.$row->getpop_qty().'">';

        return $html;
    }
}