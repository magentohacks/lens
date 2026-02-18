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

namespace Magestore\Pdfinvoiceplus\Model\Variables\Option\BarcodeFilename;

/**
 * class Order
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Order implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const ORDER_STATUS = '{{var order_status}}';
    /**
     *
     */
    const ORDER_INCREMENT_ID = '{{var order_increment_id}}';
    /**
     *
     */
    const ORDER_CREATED_AT = '{{var order_created_at}}';
    /**
     *
     */
    const ORDER_TOTAL_QTY = '{{var order_total_qty}}';
    /**
     *
     */
    const ORDER_BILLING_ADDRESS = '{{var order_billing_address}}';
    /**
     *
     */
    const ORDER_SHIPPING_ADDRESS = '{{var order_shipping_address}}';
    /**
     *
     */
    const ORDER_BILLING_METHOD = '{{var order_billing_method}}';
    /**
     *
     */
    const ORDER_SHIPPING_METHOD = '{{var order_shipping_method}}';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ORDER_STATUS, 'label' => 'Status'],
            ['value' => self::ORDER_INCREMENT_ID, 'label' => 'Increment Id'],
            ['value' => self::ORDER_CREATED_AT, 'label' => 'Invoice Date'],
            ['value' => self::ORDER_TOTAL_QTY, 'label' => 'Qty'],
            ['value' => self::ORDER_BILLING_ADDRESS, 'label' => 'Billing Address'],
            ['value' => self::ORDER_SHIPPING_ADDRESS, 'label' => 'Shipping Address'],
            ['value' => self::ORDER_BILLING_METHOD, 'label' => 'Billing Method'],
            ['value' => self::ORDER_SHIPPING_METHOD, 'label' => 'Shipping Method'],
        ];
    }
}
