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
 * class Quote
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Quote extends \Magestore\Pdfinvoiceplus\Model\AbstractQuotePdfTemplateRender
{
    protected $_type = 'quote';

    /** @var  \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\QuoteItem */
    protected $_itemRenderer;


    /**
     * Render entity data to a html template
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote $entity
     * @param                            $templateHtml
     *
     * @return mixed
     */
    public function renderQuote(\Magento\Quote\Model\Quote $entity, $templateHtml)
    {
        parent::renderQuote($entity, $templateHtml);
        if (!$entity->getData('entity_id')) {
            return $this->getProcessedHtml();
        }

        $this->_processItemsHtml();

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

        foreach ($this->_getAllQuoteItem() as $item) {
            $itemsTemplateFilled .= $this->getItemRenderer()->renderQuoteItem($item, $itemsTemplate) . '<br>';
        }

        $templateHtml = str_replace($itemsTemplate, $itemsTemplateFilled, $this->getProcessedHtml());

        $this->setProcessedHtml($templateHtml);

        return $this;
    }

    protected function _getAllQuoteItem()
    {
        $quoteItems = [];
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->getRenderingEntity()->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $quoteItems[] = $item;
            }
        }

        return $quoteItems;
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
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\QuoteVariableCollector $varCollector */
        $varCollector = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\QuoteVariableCollector');
        $templateVars = $varCollector->setData('quote', $this->getQuote())
            ->getQuoteVariables();

        return $this->processAllVars($templateVars);
    }

    public function getQuote()
    {
        return $this->getRenderingEntity();
    }


    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\QuoteItem
     */
    public function getItemRenderer()
    {
        if (!$this->_itemRenderer) {
            $this->setItemRenderer($this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_QUOTE_ITEM));
        }

        return $this->_itemRenderer;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return null;
    }
}