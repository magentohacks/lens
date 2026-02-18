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

namespace Magestore\Pdfinvoiceplus\Model;

/**
 * class MainVariablesManager
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class MainVariablesManager extends AbstractObjectManager
{
    /**
     *
     */
    const MAIN_VARIABLE_ORDER = 'main_variable_order';
    /**
     *
     */
    const MAIN_VARIABLE_ORDER_ITEM = 'main_variable_order_item';
    /**
     *
     */
    const MAIN_VARIABLE_INVOICE = 'main_variable_invoice';
    /**
     *
     */
    const MAIN_VARIABLE_INVOICE_ITEM = 'main_variable_invoice_item';
    /**
     *
     */
    const MAIN_VARIABLE_CREDITMEMO = 'main_variable_creditmemo';
    /**
     *
     */
    const MAIN_VARIABLE_CREDITMEMO_ITEM = 'main_variable_creditmemo_item';
    /**
     *
     */
    const MAIN_VARIABLE_SHIPMENT = 'main_variable_shipment';
    /**
     *
     */
    const MAIN_VARIABLE_SHIPMENT_ITEM = 'main_variable_shipment_item';
    /**
     *
     */
    const MAIN_VARIABLE_QUOTE = 'main_variable_quote';
    /**
     *
     */
    const MAIN_VARIABLE_QUOTE_ITEM = 'main_variable_quote_item';
    /**
     *
     */
    const MAIN_VARIABLE_CUSTOMER = 'main_variable_customer';

    /**
     * Map of types which are references to classes.
     *
     * @var array
     */
    protected $_typeMap = [
        self::MAIN_VARIABLE_ORDER => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\Order',
        self::MAIN_VARIABLE_ORDER_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\OrderItem',
        self::MAIN_VARIABLE_INVOICE => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\Invoice',
        self::MAIN_VARIABLE_INVOICE_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\InvoiceItem',
        self::MAIN_VARIABLE_CREDITMEMO => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\Creditmemo',
        self::MAIN_VARIABLE_CREDITMEMO_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\CreditmemoItem',
        self::MAIN_VARIABLE_SHIPMENT => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\Shipment',
        self::MAIN_VARIABLE_SHIPMENT_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\ShipmentItem',
        self::MAIN_VARIABLE_QUOTE => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\Quote',
        self::MAIN_VARIABLE_QUOTE_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\QuoteItem',
        self::MAIN_VARIABLE_CUSTOMER => 'Magestore\Pdfinvoiceplus\Model\Variables\MainVariables\Customer'
    ];

    /**
     * @param $type
     *
     * @return \Magestore\Pdfinvoiceplus\Model\Variables\MainVariablesInterface
     */
    public function get($type)
    {
        if (empty($this->_typeMap[$type])) {
            throw new \InvalidArgumentException('"' . $type . ': isn\'t allowed');
        }

        $instance = $this->_objectManager->get($this->_typeMap[$type]);
        if (!$instance instanceof \Magestore\Pdfinvoiceplus\Model\Variables\MainVariablesInterface) {
            throw new \InvalidArgumentException(
                get_class($instance)
                . ' isn\'t instance of \Magestore\Pdfinvoiceplus\Model\Variables\MainVariablesInterface !'
            );
        }

        return $instance;
    }
}