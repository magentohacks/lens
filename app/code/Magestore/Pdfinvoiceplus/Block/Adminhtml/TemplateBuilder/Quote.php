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
 * class Quote
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Quote extends \Magestore\Pdfinvoiceplus\Block\Adminhtml\AbstractTemplateBuilder
{
    /**
     * @var string
     */
    protected $_builderType = 'quote';

    protected $_quoteTableItemRenderer;

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->getPdfTemplateObject()->getData('barcode_quote');
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
            "Magestore_Pdfinvoiceplus::default-template/%s/quote/template-loader.phtml",
            $this->getPdfTemplateObject()->getData('template_code')
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuoteTableItemsHtml()
    {
        if (!$this->getTableItemRenderer()) {
            /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\QuoteTableItem $quoteTableItemRenderer */
            $quoteTableItemRenderer = $this->getLayout()
                ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\QuoteTableItem');
            $quoteTableItemRenderer->setPdfTemplateObject($this->getPdfTemplateObject());

            $this->setQuoteTableItemRenderer($quoteTableItemRenderer);
        }

        return $this->getQuoteTableItemRenderer()->toHtml();
    }

    /**
     * @param \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTableItem
     */
    public function setQuoteTableItemRenderer($quoteTableItemRenderer)
    {
        $this->_quoteTableItemRenderer = $quoteTableItemRenderer;

        return $this;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\ShipmentTableItem
     */
    public function getQuoteTableItemRenderer()
    {
        return $this->_quoteTableItemRenderer;
    }
}