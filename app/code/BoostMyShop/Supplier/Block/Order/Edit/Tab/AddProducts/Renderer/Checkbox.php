<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab\AddProducts\Renderer;

/**
 * Renderer for Qty field in sales create new order search grid
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Checkbox extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{

    /**
     * Render product qty field
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {


        // Compose html
        $name = "check_".$row->getId();
        $html = '<input type="checkbox" ';
        $html .= 'name="' . $name . '" ';
        $html .= 'id="' . $name . '" ';
        $html .= 'onclick="order.toggleProductToAddQty(' . $row->getId(). ')" ';
        $html .= 'value="' . $row->getId() . '" ';
        $html .= ' />';
        return $html;
    }
}
