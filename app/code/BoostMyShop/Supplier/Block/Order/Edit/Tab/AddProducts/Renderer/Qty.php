<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer;

/**
 * Renderer for Qty field in sales create new order search grid
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Qty extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{

    /**
     * Render product qty field
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        $qty = '';
        $disabled = 'disabled="disabled" ';
        $addClass = ' input-inactive';

        // Compose html
        $name = "qty_".$row->getId();
        $html = '<input type="text" ';
        $html .= 'name="' . $name . '" ';
        $html .= 'id="' . $name . '" ';
        $html .= 'onchange="order.changeProductToAddQty(' . $row->getId(). ');" ';
        $html .= 'value="' . $qty . '" ' . $disabled;
        $html .= 'class="input-text admin__control-text ' . $this->getColumn()->getInlineCss() . $addClass . '" />';
        return $html;
    }
}
