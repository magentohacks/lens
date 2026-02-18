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
 * Class Creditmemo
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Creditmemo implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const CREDITMEMO_STATUS = '{{var creditmemo_state}}';
    /**
     *
     */
    const CREDITMEMO_INCREMENT_ID = '{{var creditmemo_increment_id}}';
    /**
     *
     */
    const CREDITMEMO_CREATED_AT = '{{var creditmemo_created_at}}';
    /**
     *
     */
    const CREDITMEMO_ORDER_ID = '{{var creditmemo_order_id}}';
    /**
     *
     */
    const CREDITMEMO_TOTAL_QTY = '{{var creditmemo_total_qty}}';
    /**
     *
     */
    const CREDITMEMO_BILLING_ADDRESS = '{{var creditmemo_billing_address}}';
    /**
     *
     */
    const CREDITMEMO_SHIPPING_ADDRESS = '{{var creditmemo_shipping_address}}';
    /**
     *
     */
    const CREDITMEMO_BILLING_METHOD = '{{var creditmemo_billing_method}}';
    /**
     *
     */
    const CREDITMEMO_SHIPPING_METHOD = '{{var creditmemo_shipping_method}}';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CREDITMEMO_STATUS, 'label' => 'Status'],
            ['value' => self::CREDITMEMO_INCREMENT_ID, 'label' => 'Increment Id'],
            ['value' => self::CREDITMEMO_CREATED_AT, 'label' => 'Invoice Date'],
            ['value' => self::CREDITMEMO_ORDER_ID, 'label' => 'Order Id'],
            ['value' => self::CREDITMEMO_TOTAL_QTY, 'label' => 'Qty'],
            ['value' => self::CREDITMEMO_BILLING_ADDRESS, 'label' => 'Billing Address'],
            ['value' => self::CREDITMEMO_SHIPPING_ADDRESS, 'label' => 'Shipping Address'],
            ['value' => self::CREDITMEMO_BILLING_METHOD, 'label' => 'Billing Method'],
            ['value' => self::CREDITMEMO_SHIPPING_METHOD, 'label' => 'Shipping Method'],
        ];
    }
}
