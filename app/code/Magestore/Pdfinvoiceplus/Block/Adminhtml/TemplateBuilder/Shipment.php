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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder;

/**
 * class Shipment
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Shipment extends \Magestore\Pdfinvoiceplus\Block\Adminhtml\AbstractTemplateBuilder
{
    /**
     * @var string
     */
    protected $_builderType = 'shipment';

    protected $_shipmentTableItemRenderer;

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->getPdfTemplateObject()->getData('barcode_shipment');
    }

    /**
     * @return mixed
     */
    public function getBindedStatus()
    {
        return null;
    }

    /**
     * @param $entityType
     */
    public function getDefaultTemplateLoaderPath()
    {
        return sprintf(
            "Magestore_Pdfinvoiceplus::default-template/%s/shipment/template-loader.phtml",
            $this->getPdfTemplateObject()->getData('template_code')
        );
    }


    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getShipmentTableItemsHtml()
    {
        if (!$this->getTableItemRenderer()) {
            /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTableItem $shipmentTableItemRenderer */
            $shipmentTableItemRenderer = $this->getLayout()
                ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTableItem');
            $shipmentTableItemRenderer->setPdfTemplateObject($this->getPdfTemplateObject());

            $this->setShipmentTableItemRenderer($shipmentTableItemRenderer);
        }

        return $this->getShipmentTableItemRenderer()->toHtml();
    }

    /**
     * @param \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTableItem
     */
    public function setShipmentTableItemRenderer($shipmentTableItemRenderer)
    {
        $this->_shipmentTableItemRenderer = $shipmentTableItemRenderer;

        return $this;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTableItem
     */
    public function getShipmentTableItemRenderer()
    {
        return $this->_shipmentTableItemRenderer;
    }

}