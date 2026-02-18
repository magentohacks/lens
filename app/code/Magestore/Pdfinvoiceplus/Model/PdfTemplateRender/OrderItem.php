<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender;

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * class OrderItem
 *
 * @method \Magento\Sales\Model\Order\Item getRenderingEntity()
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class OrderItem extends AbstractItemRender
{

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getRenderingEntity()->getOrder();
    }

    /**
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getOrderItem()
    {
        return $this->getRenderingEntity();
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     *
     * @return bool
     */
    public function canCalculateData(\Magento\Sales\Model\Order\Item $orderItem, $key = '')
    {
        return $orderItem->getData($key)
        && ($orderItem->isChildrenCalculated() && $orderItem->getParentItem()) || !$orderItem->isChildrenCalculated();
    }

    /**
     *
     * @return string
     */
    public function getTaxAmount()
    {
        return $this->canCalculateData($this->getOrderItem(), OrderItemInterface::TAX_AMOUNT)
            ? $this->getOrder()->formatPriceTxt($this->getOrderItem()->getTaxAmount()) : $this->getOrder()->formatPriceTxt(0);
    }

    /**
     *
     * @return null|string
     */
    public function getTaxPercent()
    {
        return $this->canCalculateData($this->getOrderItem(), OrderItemInterface::TAX_PERCENT)
            ? number_format($this->getOrderItem()->getTaxPercent(), 2, ',', '') . '%' : null;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     *
     * @return null|string
     */
    public function getRowTotal(\Magento\Sales\Model\Order\Item $orderItem)
    {
        return $this->canCalculateData($orderItem, OrderItemInterface::ROW_TOTAL)
            ? $this->getOrder()->formatPriceTxt($orderItem->getRowTotal()) : null;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     *
     * @return null|string
     */
    public function getPrice(\Magento\Sales\Model\Order\Item $orderItem)
    {
        return $this->canCalculateData($orderItem, OrderItemInterface::PRICE)
            ? $this->getOrder()->formatPriceTxt($orderItem->getPrice()) : null;
    }

    /**
     * @return int
     */
    public function getQtyOrdered()
    {
        return (int)$this->getOrderItem()->getQtyOrdered();
    }

    /**
     * @return array|null
     */
    public function getItemsPriceData()
    {
        return $this->getOrderItem()->isChildrenCalculated() ? $this->isPriceDisplayOptions() : [];
    }

    /**
     * Get the Item prices for display - need to review this part adn move the item system to do
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        /** @var \Magento\Sales\Model\Order\Item $item */
        $item = $this->getOrderItem();

        $price = [];
        foreach ($item->getData() as $key => $value) {
            $price['items_' . $key] = ['value' => $value];

            if ($key == 'price_incl_tax') {
                $price['items_price_incl_tax'] = [
                    'value' => $order->formatPriceTxt($item->getPriceInclTax()),
                ];
            }
            if ($key == 'row_total_incl_tax') {
                $price['items_row_total_incl_tax'] = [
                    'value' => $order->formatPriceTxt($item->getRowTotalInclTax()),
                ];
            }
        }

        return $price;
    }

    /**
     * @return array
     */
    public function getStandardItemVars()
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $this->getOrderItem();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $orderItem->getOrder();

        $productOptions = $this->getItemOptions();
        $itemsDetails = $orderItem->getData();

        $qtyOrdered = $this->getQtyOrdered() ? 'Ordered: ' . $this->getQtyOrdered() : null;
        $qtyInvoiced = $orderItem->getQtyInvoiced() ? 'Invoiced: ' . (int)$orderItem->getQtyInvoiced() : null;
        $qtyRefunded = $orderItem->getQtyRefunded() ? 'Refunded: ' . (int)$orderItem->getQtyRefunded() : null;

        $standardVars = [];
        foreach ($itemsDetails as $key => $value) {
            $standardVars['items_' . $key] = ['value' => $value];

            if ($key == 'qty_ordered') {
                $standardVars['items_qty_ordered'] = ['value' => $qtyOrdered];
            }
            if ($key == 'qty_invoiced') {
                $standardVars['items_qty_invoiced'] = ['value' => $qtyInvoiced];
            }
            if ($key == 'qty_refunded') {
                $standardVars['items_qty_refunded'] = ['value' => $qtyRefunded];
            }
            if ($key == 'row_total') {
                $standardVars['items_row_total'] = ['value' => $this->getRowTotal($orderItem)];
            }
            if ($key == 'price') {
                $standardVars['items_price'] = ['value' => $this->getPrice($orderItem)];
            }
            if ($key == 'original_price') {
                $standardVars['items_original_price'] = [
                    'value' => $order->formatPrice($itemsDetails['original_price']),
                ];
            }
            if ($key == 'discount_amount') {
                $standardVars['items_discount_amount'] = [
                    'value' => $order->formatPrice($itemsDetails['discount_amount']),
                ];
            }
            if ($key == 'tax_amount') {
                $standardVars['items_tax_amount'] = ['value' => $this->getTaxAmount()];
            }

            if ($key == 'tax_percent') {
                $standardVars['items_tax_percent'] = ['value' => $this->getTaxPercent()];
            }

            if ($key == 'product_options') {
                $standardVars['items_product_options'] = ['value' => $productOptions];
            }

            if ($key == 'sku') {
                $standardVars['items_sku'] = ['value' => $this->getSku()];
            }
        }

        return $standardVars;
    }
}