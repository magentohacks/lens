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

use \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager;

/**
 * class Shipment
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Shipment extends \Magestore\Pdfinvoiceplus\Model\AbstractPdfTemplateRender
{
    protected $_type = 'shipment';

    protected $_itemRenderer;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\ShipmentTrack
     */
    protected $_trackRenderer;

    /**
     * Render entity data to a html template
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param                            $templateHtml
     *
     * @return mixed
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        parent::render($entity, $templateHtml);
        if (!$entity->getId()) {
            return $this->getProcessedHtml();
        }

        $this->_processItemsHtml();
        $this->_processShipmentTrackHtml();

        $variables = $this->getVariables();

        $processedHtml = $this->getProcessedHtml();
        $processedHtml = $this->_processAddressHtml($processedHtml, $variables, 'shipping_address');
        $processedHtml = $this->_processAddressHtml($processedHtml, $variables, 'billing_address');
        $processedHtml = $this->_pdfHelper->mappingVariablesTemplate($processedHtml, $variables);
        $processedHtml = preg_replace('#\{\*.*\*\}#suU', '', $processedHtml);

        $this->setProcessedHtml($processedHtml);

        return $this->getProcessedHtml();
    }

    /**
     * @param $processedHtml
     * @param $variables
     * @param $addressName
     *
     * @return mixed
     */
    protected function _processAddressHtml($processedHtml, &$variables, $addressName)
    {
        $index = $this->getType() . '_' . $addressName;
        if (isset($variables[$index])) {
            $processedHtml = str_replace(
                '{{var ' . $index . '}}',
                $variables[$index],
                $processedHtml
            );
            unset($variables[$index]);
        }

        return $processedHtml;
    }

    /**
     * @param $srcHtml
     * @param $start
     * @param $end
     *
     * @return string
     */
    public function getTemplatePartForItems($srcHtml, $start, $end)
    {
        $txt = explode($start, $srcHtml);
        $txt2 = explode($end, $txt[1]);
        $explode = explode('<tbody>', $srcHtml);
        $explode2 = explode('</tbody>', $explode[1]);

        return $txt2[0] ? trim($txt2[0]) : trim($explode2[0]);
    }

    /**
     * retrieve template with order items
     * @return $this
     */
    protected function _processItemsHtml()
    {
        $itemsTemplateFilled = '';
        $itemsTemplate = $this->getTemplatePartForItems($this->getProcessedHtml(), self::THE_START, self::THE_END);

        foreach ($this->getRenderingEntity()->getAllItems() as $item) {
            $itemsTemplateFilled .= $this->getItemRenderer()->render($item, $itemsTemplate) . '<br>';
        }

        $templateHtml = str_replace($itemsTemplate, $itemsTemplateFilled, $this->getProcessedHtml());
        $this->setProcessedHtml($templateHtml);

        return $this;
    }

    protected function _processShipmentTrackHtml()
    {
        $trackTableTemplateFilled = '';

        $trackTableTemplate = $this->getTemplatePartForItems($this->getProcessedHtml(), self::TRACK_TABLE_START, self::TRACK_TABLE_END);

        if ($this->getRenderingEntity()->getAllTracks()) {
            $trackItemsTemplateFilled = '';
            $trackItemsTemplate = $this->getTemplatePartForItems($trackTableTemplate, self::TRACKING_START, self::TRACKING_END);

            /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
            foreach ($this->getRenderingEntity()->getAllTracks() as $track) {
                $trackItemsTemplateFilled .= $this->getTrackRenderer()->render($track, $trackItemsTemplate);
            }

            $trackTableTemplateFilled = str_replace($trackItemsTemplate, $trackItemsTemplateFilled, $trackTableTemplate);
        }

        $templateHtml = str_replace($trackTableTemplate, $trackTableTemplateFilled, $this->getProcessedHtml());
        $this->setProcessedHtml($templateHtml);

        return $this;
    }

    /**
     * @param mixed $itemRenderer
     *
     * @return AbstractRender
     */
    public function setItemRenderer($itemRenderer)
    {
        $this->_itemRenderer = $itemRenderer;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\VariableCollector $varCollector */
        $varCollector = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\VariableCollector');
        $templateVars = $varCollector->setData('shipment', $this->getShipment())
            ->setData('order', $this->getOrder())
            ->setData('type', $this->getType())
            ->getInfoMergedVariables();

        return $this->processAllVars($templateVars);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getRenderingEntity()->getOrder();
    }

    public function getShipment()
    {
        return $this->getRenderingEntity();
    }


    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function getItemRenderer()
    {
        if (!$this->_itemRenderer) {
            $this->setItemRenderer($this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_SHIPMENT_ITEM));
        }

        return $this->_itemRenderer;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\ShipmentTrack
     */
    public function getTrackRenderer()
    {
        if (!$this->_trackRenderer) {
            $this->setTrackRenderer($this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_SHIPMENT_TRACK));
        }

        return $this->_trackRenderer;
    }

    /**
     * @param mixed $trackRenderer
     */
    public function setTrackRenderer($trackRenderer)
    {
        $this->_trackRenderer = $trackRenderer;

        return $this;
    }
}