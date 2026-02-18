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

use Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\TemplateBuilderFactory;

/**
 * Class PdfTemplate
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class PdfTemplate extends \Magento\Framework\Model\AbstractModel
{
    /**
     *
     */
    const IMAGE_LOGO_PATH = 'magestore/pdfinvoiceplus/images/companylogo';
    /**
     *
     */
    const IMAGE_BACKGROUND_PATH = 'magestore/pdfinvoiceplus/images/background';

    /**
     *
     */
    const IS_CHANGED_DESIGN = 1;
    /**
     *
     */
    const IS_NOT_CHANGED_DESIGN = 0;

    /**
     *
     */
    const DESIGN_TYPE_ORDER = 'order';
    /**
     *
     */
    const DESIGN_TYPE_INVOICE = 'invoice';
    /**
     *
     */
    const DESIGN_TYPE_CREDITMEMO = 'creditmemo';
    /**
     * @var string
     */
    const DESIGN_TYPE_SHIPMENT = 'shipment';
    /**
     * @var string
     */
    const DESIGN_TYPE_QUOTE = 'quote';

    /**
     * @var BarcodeClassNameBuilderFactory
     */
    protected $_barcodeClassNameBuilderFactory;

    /**
     * @var TemplateBuilderFactory
     */
    protected $_templateBuilderFactory;

    /**
     * @var
     */
    protected $_simpleHtmlDomFactory;

    /**
     * @var bool
     */
    protected $_allowResetOrderTemplate = false;

    /**
     * @var bool
     */
    protected $_allowResetInvoiceTemplate = false;

    /**
     * @var bool
     */
    protected $_allowResetCreditmemoTemplate = false;

    /**
     * @var bool
     */
    protected $_allowResetShipmentTemplate = false;

    /**
     * @var bool
     */
    protected $_allowResetQuoteTemplate = false;

    /**
     * Model constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TemplateBuilderFactory $templateBuilderFactory,
        \Magestore\Pdfinvoiceplus\Model\SimpleHtmlDomFactory $simpleHtmlDomFactory,
        \Magestore\Pdfinvoiceplus\Model\BarcodeClassNameBuilderFactory $barcodeClassNameBuilderFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_templateBuilderFactory = $templateBuilderFactory;
        $this->_simpleHtmlDomFactory = $simpleHtmlDomFactory;
        $this->_barcodeClassNameBuilderFactory = $barcodeClassNameBuilderFactory;
    }

    /**
     * Prepare html data for order html, invoice html, creditmemo html
     */
    protected function _prepareHtmlData()
    {
        $this->loadTemplateCode();

        if ($this->getData('template_code')) {
            if ($this->isChangedDesign()) {
                /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Order $block */
                $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::ORDER, $this);
                $this->setData('order_html', $block->toHtml());

                /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Invoice $block */
                $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::INVOICE, $this);
                $this->setData('invoice_html', $block->toHtml());

                /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Creditmemo $block */
                $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::CREDITMEMO, $this);
                $this->setData('creditmemo_html', $block->toHtml());

                /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Shipment $block */
                $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::SHIPMENT, $this);
                $this->setData('shipment_html', $block->toHtml());

                /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Quote $block */
                $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::QUOTE, $this);
                $this->setData('quote_html', $block->toHtml());
            } else {
                if (!$this->getData('order_html') || $this->isAllowResetOrderTemplate()) {
                    /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Order $block */
                    $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::ORDER, $this);
                    $this->setData('order_html', $block->toHtml());
                }

                if (!$this->getData('invoice_html') || $this->isAllowResetInvoiceTemplate()) {
                    /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Invoice $block */
                    $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::INVOICE, $this);
                    $this->setData('invoice_html', $block->toHtml());
                }

                if (!$this->getData('creditmemo_html') || $this->isAllowResetCreditmemoTemplate()) {
                    /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Creditmemo $block */
                    $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::CREDITMEMO, $this);
                    $this->setData('creditmemo_html', $block->toHtml());
                }

                if (!$this->getData('shipment_html') || $this->isAllowResetShipmentTemplate()) {
                    /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Shipment $block */
                    $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::SHIPMENT, $this);
                    $this->setData('shipment_html', $block->toHtml());
                }

                if (!$this->getData('quote_html') || $this->isAllowResetQuoteTemplate()) {
                    /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Quote $block */
                    $block = $this->_templateBuilderFactory->createTemplateBuilder(TemplateBuilderFactory::QUOTE, $this);
                    $this->setData('quote_html', $block->toHtml());
                }
            }
        }

        return $this;
    }

    /**
     * Check save barcode or not
     */
    protected function _prepareBarcode()
    {
        $this->setData('order_html', $this->_prepareBarcodeClassName($this->getData('order_html')));
        $this->setData('invoice_html', $this->_prepareBarcodeClassName($this->getData('invoice_html')));
        $this->setData('creditmemo_html', $this->_prepareBarcodeClassName($this->getData('creditmemo_html')));
        $this->setData('shipment_html', $this->_prepareBarcodeClassName($this->getData('shipment_html')));
        $this->setData('quote_html', $this->_prepareBarcodeClassName($this->getData('quote_html')));
    }

    /**
     * @param $html
     *
     * @return mixed
     */
    protected function _prepareBarcodeClassName($html)
    {
        $simpleHtmlDom = $this->_simpleHtmlDomFactory->create();
        $simpleHtmlDom->load($html);

        if ($barcode = $simpleHtmlDom->find('.pdf-header-template .barcode', 0)) {
            /** @var BarcodeClassNameBuilder $barcodeClassNameBuilder */
            $barcodeClassNameBuilder = $this->_barcodeClassNameBuilderFactory->create([
                'data' => [
                    'is_show' => $this->isShowBarcode(),
                    'type' => $this->getData('barcode_type'),
                ]
            ]);

            $barcode->class = $barcodeClassNameBuilder->buildClassNames();
        }

        return $simpleHtmlDom->__toString();
    }

    /**
     * @return bool
     */
    public function isShowBarcode()
    {
        return $this->getData('barcode') == \Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\ShowBarcode::STATUS_YES;
    }


    /**
     * Check is change design
     *
     * @return bool
     */
    public function isChangedDesign()
    {
        return $this->getData('flag_change_design') == self::IS_CHANGED_DESIGN;
    }

    /**
     * Set flag for change design or not
     *
     * @param int $flag
     */
    public function setIsChangeDesign($flag = true)
    {

        $this->setData('flag_change_design', $flag ? self::IS_CHANGED_DESIGN : self::IS_NOT_CHANGED_DESIGN);

        return $this;
    }

    /**
     * @return $this
     */
    public function afterLoad()
    {
        $this->setData('flag_change_design', self::IS_NOT_CHANGED_DESIGN);
        $this->setAllowResetTemplateAll(false);

        return parent::afterLoad();
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->_prepareHtmlData();
        $this->_prepareBarcode();

        return parent::beforeSave();
    }

    /**
     * @param bool $flag
     */
    public function setAllowResetTemplateAll($flag = true)
    {
        $this->setAllowResetOrderTemplate($flag)
            ->setAllowResetInvoiceTemplate($flag)
            ->setAllowResetCreditmemoTemplate($flag)
            ->setAllowResetShipmentTemplate($flag)
            ->setAllowResetQuoteTemplate($flag);
    }

    /**
     * @param boolean $allowResetOrderTemplate
     *
     * @return PdfTemplate
     */
    public function setAllowResetOrderTemplate($allowResetOrderTemplate)
    {
        $this->_allowResetOrderTemplate = $allowResetOrderTemplate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowResetOrderTemplate()
    {
        return $this->_allowResetOrderTemplate;
    }

    /**
     * @param boolean $allowResetInvoiceTemplate
     *
     * @return PdfTemplate
     */
    public function setAllowResetInvoiceTemplate($allowResetInvoiceTemplate)
    {
        $this->_allowResetInvoiceTemplate = $allowResetInvoiceTemplate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowResetInvoiceTemplate()
    {
        return $this->_allowResetInvoiceTemplate;
    }

    /**
     * @param boolean $allowResetCreditmemoTemplate
     *
     * @return PdfTemplate
     */
    public function setAllowResetCreditmemoTemplate($allowResetCreditmemoTemplate)
    {
        $this->_allowResetCreditmemoTemplate = $allowResetCreditmemoTemplate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowResetCreditmemoTemplate()
    {
        return $this->_allowResetCreditmemoTemplate;
    }

    /**
     * @param boolean $allowResetCreditmemoTemplate
     *
     * @return PdfTemplate
     */
    public function setAllowResetShipmentTemplate($allowResetShipmentTemplate)
    {
        $this->_allowResetShipmentTemplate = $allowResetShipmentTemplate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllowResetShipmentTemplate()
    {
        return $this->_allowResetShipmentTemplate;
    }

    /**
     * @return boolean
     */
    public function isAllowResetQuoteTemplate()
    {
        return $this->_allowResetQuoteTemplate;
    }

    /**
     * @param boolean $allowResetQuoteTemplate
     */
    public function setAllowResetQuoteTemplate($allowResetQuoteTemplate)
    {
        $this->_allowResetQuoteTemplate = $allowResetQuoteTemplate;
    }


    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadTemplateCode()
    {
        $this->_getResource()->loadTemplateCode($this);

        return $this;
    }
}
