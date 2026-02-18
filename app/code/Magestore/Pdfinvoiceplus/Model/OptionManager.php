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
 * class OptionManager
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class OptionManager extends AbstractObjectManager
{
    /**#@+
     * Allowed object types
     */
    const OPTION_BARCODE_TYPE = 'barcode_type';
    /**
     *
     */
    const OPTION_PAGE_SIZES = 'page_sizes';
    /**
     *
     */
    const OPTION_SHOW_BARCODE = 'show_barcode';
    /**
     *
     */
    const OPTION_STATUSES = 'statuses';
    /**
     *
     */
    const OPTION_PAGE_ORIENTATION = 'page_orientation';
    /**
     *
     */
    const OPTION_LANGUANGE = 'language';

    /**
     *
     */
    const OPTION_VARIABLE_ORDER = 'variable_order';
    /**
     *
     */
    const OPTION_VARIABLE_ORDER_ITEM = 'variable_order_item';
    /**
     *
     */
    const OPTION_VARIABLE_INVOICE = 'variable_invoice';
    /**
     *
     */
    const OPTION_VARIABLE_INVOICE_ITEM = 'variable_invoice_item';
    /**
     *
     */
    const OPTION_VARIABLE_CREDITMEMO = 'variable_creditmemo';
    /**
     *
     */
    const OPTION_VARIABLE_CREDITMEMO_ITEM = 'variable_creditmemo_item';
    /**
     *
     */
    const OPTION_VARIABLE_SHIPMENT = 'variable_shipment';
    /**
     *
     */
    const OPTION_VARIABLE_SHIPMENT_ITEM = 'variable_shipment_item';
    /**
     *
     */
    const OPTION_VARIABLE_QUOTE = 'variable_quote';
    /**
     *
     */
    const OPTION_VARIABLE_QUOTE_ITEM = 'variable_quote_item';
    /**
     *
     */
    const OPTION_VARIABLE_CUSTOMER = 'variable_customer';

    /**
     *
     */
    const OPTION_VARIABLE_BARCODEFILENAME_ORDER = 'variable_barcodefilename_order';
    /**
     *
     */
    const OPTION_VARIABLE_BARCODEFILENAME_INVOICE = 'variable_barcodefilename_invoice';
    /**
     *
     */
    const OPTION_VARIABLE_BARCODEFILENAME_CREDITMEMO = 'variable_barcodefilename_creditmemo';
    /**
     *
     */
    const OPTION_VARIABLE_BARCODEFILENAME_SHIPMENT = 'variable_barcodefilename_shipment';
    /**
     *
     */
    const OPTION_VARIABLE_BARCODEFILENAME_QUOTE = 'variable_barcodefilename_quote';

    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_ORDER = 'variable_config_order';
    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_ORDER_ITEM = 'variable_config_order_item';
    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_INVOICE = 'variable_config_invoice';
    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_INVOICE_ITEM = 'variable_config_invoice_item';
    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_CREDITMEMO = 'variable_config_creditmemo';
    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_CREDITMEMO_ITEM = 'variable_config_creditmemo_item';

    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_SHIPMENT = 'variable_config_shipment';

    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_SHIPMENT_ITEM = 'variable_config_shipment_item';
    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_QUOTE = 'variable_config_quote';

    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_QUOTE_ITEM = 'variable_config_quote_item';

    /**
     *
     */
    const OPTION_VARIABLE_CONFIG_CUSTOMER = 'variable_config_customer';

    /**
     * Map of types which are references to classes.
     *
     * @var array
     */
    protected $_typeMap = [
        self::OPTION_BARCODE_TYPE => 'Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\BarcodeType',
        self::OPTION_PAGE_SIZES => 'Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\PageSizes',
        self::OPTION_SHOW_BARCODE => 'Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\ShowBarcode',
        self::OPTION_STATUSES => 'Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\Statuses',
        self::OPTION_PAGE_ORIENTATION => 'Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\PageOrientation',
        self::OPTION_LANGUANGE => 'Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\Language',
        self::OPTION_VARIABLE_ORDER => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Order',
        self::OPTION_VARIABLE_ORDER_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\OrderItem',
        self::OPTION_VARIABLE_INVOICE => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Invoice',
        self::OPTION_VARIABLE_INVOICE_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\InvoiceItem',
        self::OPTION_VARIABLE_CREDITMEMO => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Creditmemo',
        self::OPTION_VARIABLE_CREDITMEMO_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\CreditmemoItem',
        self::OPTION_VARIABLE_SHIPMENT => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Shipment',
        self::OPTION_VARIABLE_SHIPMENT_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\ShipmentItem',
        self::OPTION_VARIABLE_QUOTE => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Quote',
        self::OPTION_VARIABLE_QUOTE_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\QuoteItem',
        self::OPTION_VARIABLE_CUSTOMER => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Customer',
        self::OPTION_VARIABLE_BARCODEFILENAME_ORDER => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\BarcodeFilename\Order',
        self::OPTION_VARIABLE_BARCODEFILENAME_INVOICE => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\BarcodeFilename\Invoice',
        self::OPTION_VARIABLE_BARCODEFILENAME_CREDITMEMO => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\BarcodeFilename\Creditmemo',
        self::OPTION_VARIABLE_BARCODEFILENAME_SHIPMENT => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\BarcodeFilename\Shipment',
        self::OPTION_VARIABLE_BARCODEFILENAME_QUOTE => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\BarcodeFilename\Quote',
        self::OPTION_VARIABLE_CONFIG_ORDER => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\Order',
        self::OPTION_VARIABLE_CONFIG_ORDER_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\OrderItem',
        self::OPTION_VARIABLE_CONFIG_INVOICE => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\Invoice',
        self::OPTION_VARIABLE_CONFIG_INVOICE_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\InvoiceItem',
        self::OPTION_VARIABLE_CONFIG_CREDITMEMO => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\Creditmemo',
        self::OPTION_VARIABLE_CONFIG_CREDITMEMO_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\CreditmemoItem',
        self::OPTION_VARIABLE_CONFIG_SHIPMENT => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\Shipment',
        self::OPTION_VARIABLE_CONFIG_SHIPMENT_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\ShipmentItem',
        self::OPTION_VARIABLE_CONFIG_QUOTE => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\Quote',
        self::OPTION_VARIABLE_CONFIG_QUOTE_ITEM => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\QuoteItem',
        self::OPTION_VARIABLE_CONFIG_CUSTOMER => 'Magestore\Pdfinvoiceplus\Model\Variables\Option\Config\Customer'
    ];

    /**
     * @param       $type
     * @param array $arguments
     *
     * @return \Magento\Framework\Option\ArrayInterface|null
     */
    public function get($type)
    {
        if (empty($this->_typeMap[$type])) {
            throw new \InvalidArgumentException('"' . $type . ': isn\'t allowed');
        }

        $instance = $this->_objectManager->get($this->_typeMap[$type]);
        if (!$instance instanceof \Magento\Framework\Option\ArrayInterface) {
            throw new \InvalidArgumentException(
                get_class($instance)
                . ' isn\'t instance of \Magento\Framework\Option\ArrayInterface !'
            );
        }

        return $instance;
    }
}