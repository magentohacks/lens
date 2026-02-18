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
 * class Invoice
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Invoice implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const INVOICE_STATUS = '{{var invoice_state}}';
    /**
     *
     */
    const INVOICE_INCREMENT_ID = '{{var invoice_increment_id}}';
    /**
     *
     */
    const INVOICE_CREATED_AT = '{{var invoice_created_at}}';
    /**
     *
     */
    const INVOICE_ORDER_ID = '{{var invoice_order_id}}';
    /**
     *
     */
    const INVOICE_TOTAL_QTY = '{{var invoice_total_qty}}';
    /**
     *
     */
    const INVOICE_BILLING_ADDRESS = '{{var invoice_billing_address}}';
    /**
     *
     */
    const INVOICE_SHIPPING_ADDRESS = '{{var invoice_shipping_address}}';
    /**
     *
     */
    const INVOICE_BILLING_METHOD = '{{var invoice_billing_method}}';
    /**
     *
     */
    const INVOICE_SHIPPING_METHOD = '{{var invoice_shipping_method}}';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::INVOICE_STATUS, 'label' => 'Status'],
            ['value' => self::INVOICE_INCREMENT_ID, 'label' => 'Increment Id'],
            ['value' => self::INVOICE_CREATED_AT, 'label' => 'Invoice Date'],
            ['value' => self::INVOICE_ORDER_ID, 'label' => 'Order Id'],
            ['value' => self::INVOICE_TOTAL_QTY, 'label' => 'Qty'],
            ['value' => self::INVOICE_BILLING_ADDRESS, 'label' => 'Billing Address'],
            ['value' => self::INVOICE_SHIPPING_ADDRESS, 'label' => 'Shipping Address'],
            ['value' => self::INVOICE_BILLING_METHOD, 'label' => 'Billing Method'],
            ['value' => self::INVOICE_SHIPPING_METHOD, 'label' => 'Shipping Method'],
        ];
    }
}
