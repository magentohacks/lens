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
 * class Shipment
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Shipment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const SHIPMENT_STATUS = '{{var shipment_status}}';
    /**
     *
     */
    const SHIPMENT_INCREMENT_ID = '{{var shipment_increment_id}}';
    /**
     *
     */
    const SHIPMENT_CREATED_AT = '{{var shipment_created_at}}';
    /**
     *
     */
    const SHIPMENT_TOTAL_QTY = '{{var shipment_total_qty}}';
    /**
     *
     */
    const SHIPMENT_BILLING_ADDRESS = '{{var shipment_billing_address}}';
    /**
     *
     */
    const SHIPMENT_SHIPPING_ADDRESS = '{{var shipment_shipping_address}}';
    /**
     *
     */
    const SHIPMENT_BILLING_METHOD = '{{var shipment_billing_method}}';
    /**
     *
     */
    const SHIPMENT_SHIPPING_METHOD = '{{var shipment_shipping_method}}';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SHIPMENT_STATUS, 'label' => 'Status'],
            ['value' => self::SHIPMENT_INCREMENT_ID, 'label' => 'Increment Id'],
            ['value' => self::SHIPMENT_CREATED_AT, 'label' => 'Invoice Date'],
            ['value' => self::SHIPMENT_TOTAL_QTY, 'label' => 'Qty'],
            ['value' => self::SHIPMENT_BILLING_ADDRESS, 'label' => 'Billing Address'],
            ['value' => self::SHIPMENT_SHIPPING_ADDRESS, 'label' => 'Shipping Address'],
            ['value' => self::SHIPMENT_BILLING_METHOD, 'label' => 'Billing Method'],
            ['value' => self::SHIPMENT_SHIPPING_METHOD, 'label' => 'Shipping Method'],
        ];
    }
}