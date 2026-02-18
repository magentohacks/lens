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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\Shipping;

/**
 * Class View
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class View extends \Magento\Shipping\Block\Adminhtml\View
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        array $data = []
    )
    {
        $this->_systemConfig = $systemConfig;
        parent::__construct($context, $registry, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        if ($this->getShipment()->getId() && $this->isPdfInvoiceEnabled()) {
            $this->buttonList->add(
                'print_custom_shipment_pdf',
                [
                    'label' => __('Print Custom PDF'),
                    'class' => 'save',
                    'onclick' => 'setLocation(\'' . $this->getCustomPdfPrintUrl() . '\')'
                ]
            );
        }

        if ($this->isCorePrintDisabled()) {
            $this->buttonList->remove('print');
        }
    }

    public function isPdfInvoiceEnabled()
    {
        return (bool)$this->_systemConfig->isEnablePdfInvoicePlus();
    }

    public function isCorePrintDisabled()
    {
        return (bool)$this->_systemConfig->isDisableCorePrinting();
    }

    /**
     * @return string
     */
    public function getCustomPdfPrintUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/printPdf/shipment', ['shipment_id' => $this->getShipment()->getId()]);
    }
}