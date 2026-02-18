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

namespace Magestore\Pdfinvoiceplus\Plugins\Adminhtml\PrintButton;

/**
 * Class CustomPrintButton
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class CustomPrintButton
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_helper;

    /**
     * CustomPrintButton constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        \Magestore\Pdfinvoiceplus\Helper\Data $helper

    ) {
        $this->_systemConfig = $systemConfig;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Button\Toolbar $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\View\Element\AbstractBlock $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
     */
    public function aroundPushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar $subject,
        \Closure $proceed,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    )
    {
        if($this->canShowCustomPrintPdf()) {
            if ($context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
                $context->addButton(
                    'print_custom_order',
                    [
                        'label' => __('Print Custom Order'),
                        'class' => 'print_custom_order',
                        'data_attribute' => [
                            'mage-init' => [
                                'magestorePrintButton' => ['printUrl' => $this->getPrintOrderUrl($context)],
                            ],
                        ],
                    ]
                );
            }

            if ($context instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\View) {
                $context->addButton(
                    'print_custom_invoice',
                    [
                        'label' => __('Print Custom Invoice'),
                        'class' => 'print_custom_invoice',
                        'data_attribute' => [
                            'mage-init' => [
                                'magestorePrintButton' => ['printUrl' => $this->getPrintInvoiceUrl($context)],
                            ],
                        ],
                    ]
                );

                if($this->_systemConfig->isDisableCorePrinting()) {
                    $context->removeButton('print');
                }
            }

            if ($context instanceof \Magento\Sales\Block\Adminhtml\Order\Creditmemo\View) {
                $context->addButton(
                    'print_custom_creditmemo',
                    [
                        'label' => __('Print Custom Creditmemo'),
                        'class' => 'print_custom_creditmemo',
                        'data_attribute' => [
                            'mage-init' => [
                                'magestorePrintButton' => ['printUrl' => $this->getPrintCreditmemoUrl($context)],
                            ],
                        ],
                    ]
                );

                if($this->_systemConfig->isDisableCorePrinting()) {
                    $context->removeButton('print');
                }
            }
        }

        $proceed($context, $buttonList);
    }

    /**
     * @return bool
     */
    public function canShowCustomPrintPdf()
    {
        return $this->_systemConfig->isEnablePdfInvoicePlus() && $this->_helper->getCurrentPdfTemplate()->getId();
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $context
     *
     * @return string
     */
    public function getPrintOrderUrl(\Magento\Sales\Block\Adminhtml\Order\View $context)
    {
        return $context->getUrl('pdfinvoiceplusadmin/printPdf/order', ['order_id' => $context->getOrder()->getId()]);
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Invoice\View $context
     *
     * @return string
     */
    public function getPrintInvoiceUrl(\Magento\Sales\Block\Adminhtml\Order\Invoice\View $context)
    {
        return $context->getUrl(
            'pdfinvoiceplusadmin/printPdf/invoice',
            ['invoice_id' => $context->getInvoice()->getId()]
        );
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Creditmemo\View $context
     *
     * @return string
     */
    public function getPrintCreditmemoUrl(\Magento\Sales\Block\Adminhtml\Order\Creditmemo\View $context)
    {
        return $context->getUrl(
            'pdfinvoiceplusadmin/printPdf/creditmemo',
            ['creditmemo_id' => $context->getCreditmemo()->getId()]
        );
    }
}