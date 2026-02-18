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
 * class PdfTemplateRenderManager
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class PdfTemplateRenderManager extends AbstractObjectManager
{
    /**
     *
     */
    const PDF_RENDER_ORDER = 'Magento\Sales\Model\Order';
    /**
     *
     */
    const PDF_RENDER_ORDER_ITEM = 'Magento\Sales\Model\Order\Item';
    /**
     *
     */
    const PDF_RENDER_INVOICE = 'Magento\Sales\Model\Order\Invoice';
    /**
     *
     */
    const PDF_RENDER_INVOICE_ITEM = 'Magento\Sales\Model\Order\Invoice\Item';
    /**
     *
     */
    const PDF_RENDER_CREDITMEMO = 'Magento\Sales\Model\Order\Creditmemo';
    /**
     *
     */
    const PDF_RENDER_CREDITMEMO_ITEM = 'Magento\Sales\Model\Order\Creditmemo\Item';
    /**
     *
     */
    const PDF_RENDER_SHIPMENT = 'Magento\Sales\Model\Order\Shipment';
    /**
     *
     */
    const PDF_RENDER_SHIPMENT_ITEM = 'Magento\Sales\Model\Order\Shipment\Item';
    /**
     *
     */
    const PDF_RENDER_SHIPMENT_TRACK = 'Magento\Sales\Model\Order\Shipment\Track';
    /**
     *
     */
    const PDF_RENDER_QUOTE = 'Magento\Quote\Model\Quote';
    /**
     *
     */
    const PDF_RENDER_QUOTE_ITEM = 'Magento\Quote\Model\Quote\Item';

    /**
     * Map of types which are references to classes.
     *
     * @var array
     */
    protected $_typeMap = [
        self::PDF_RENDER_ORDER => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\Order',
        self::PDF_RENDER_ORDER_ITEM => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\OrderItem',
        self::PDF_RENDER_INVOICE => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\Invoice',
        self::PDF_RENDER_INVOICE_ITEM => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\InvoiceItem',
        self::PDF_RENDER_CREDITMEMO => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\Creditmemo',
        self::PDF_RENDER_CREDITMEMO_ITEM => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\CreditmemoItem',
        self::PDF_RENDER_SHIPMENT => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\Shipment',
        self::PDF_RENDER_SHIPMENT_ITEM => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\ShipmentItem',
        self::PDF_RENDER_SHIPMENT_TRACK => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\ShipmentTrack',
        self::PDF_RENDER_QUOTE => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\Quote',
        self::PDF_RENDER_QUOTE_ITEM => 'Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\QuoteItem'
    ];

    /**
     * @param $type
     *
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function get($type)
    {
        if (empty($this->_typeMap[$type])) {
            throw new \InvalidArgumentException('"' . $type . ': isn\'t allowed');
        }

        $instance = $this->_objectManager->get($this->_typeMap[$type]);
        if (!$instance instanceof \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface) {
            throw new \InvalidArgumentException(
                get_class($instance)
                . ' isn\'t instance of \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface !'
            );
        }

        return $instance;
    }

    /**
     * @param $type
     *
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function create($type)
    {
        if (empty($this->_typeMap[$type])) {
            throw new \InvalidArgumentException('"' . $type . ': isn\'t allowed');
        }

        $instance = $this->_objectManager->create($this->_typeMap[$type]);
        if (!$instance instanceof \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface) {
            throw new \InvalidArgumentException(
                get_class($instance)
                . ' isn\'t instance of \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface !'
            );
        }

        return $instance;
    }
}