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
 * class TemplateBuilderFactory
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class TemplateBuilderFactory
{
    /**
     * @var string
     */
    const ORDER = 'order';
    /**
     * @var string
     */
    const INVOICE = 'invoice';
    /**
     * @var string
     */
    const CREDITMEMO = 'creditmemo';
    /**
     * @var string
     */
    const SHIPMENT = 'shipment';
    /**
     * @var string
     */
    const QUOTE = 'quote';

    /**
     * @var array
     */
    protected $_typeMap = [
        self::ORDER => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Order',
        self::INVOICE => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Invoice',
        self::CREDITMEMO => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Creditmemo',
        self::SHIPMENT => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Shipment',
        self::QUOTE => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder\Quote'
    ];

    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $_layout;

    /**
     * TemplateBuilderFactory constructor.
     *
     * @param \Magento\Framework\View\Layout $layout
     */
    public function __construct(\Magento\Framework\View\Layout $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * @param        $type
     * @param string $name
     * @param string $arguments
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function createTemplateBuilder($type, $pdfTemplateObject, $name = '', $arguments = [])
    {
        if (array_key_exists($type, $this->_typeMap)) {
            /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\AbstractTemplateBuilder $block */
            $block = $this->_layout->createBlock($this->_typeMap[$type], $name, $arguments);

            return $block->setPdfTemplateObject($pdfTemplateObject);
        }

        return $this->_layout->createBlock('Magento\Framework\View\Element\Template', $name, $arguments);
    }

}