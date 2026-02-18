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

namespace Magestore\Pdfinvoiceplus\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * class MassAction
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class MassAction extends \Magento\Ui\Component\AbstractComponent
{
    /**
     *
     */
    const NAME = 'massaction';

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_helper;

    /**
     * MassAction constructor.
     *
     * @param ContextInterface                             $context
     * @param \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig
     * @param \Magestore\Pdfinvoiceplus\Helper\Data        $helper
     * @param                                              $components
     * @param array                                        $data
     */
    public function __construct(
        ContextInterface $context,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        \Magestore\Pdfinvoiceplus\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->_systemConfig = $systemConfig;
        $this->_helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {

        $config = $this->getConfiguration();

        $customPrintActions = [
            'pdfinvoicesplus_print_order',
            'pdfinvoicesplus_print_invoice',
            'pdfinvoicesplus_print_creditmemo',
        ];

        $corePrintActions = [
            'pdfinvoices_order',
            'pdfcreditmemos_order',
            'pdfdocs_order',
        ];

        $canshowCustomPrintPdf = $this->canShowCustomPrintPdf();
        $canDisableCorePrinting = $this->canDisableCorePrinting();

        foreach ($this->getChildComponents() as $actionComponent) {
            if (in_array($actionComponent->getName(), $customPrintActions)) {
                if ($canshowCustomPrintPdf) {
                    $config['actions'][] = $actionComponent->getConfiguration();
                }
            } else {
                if (in_array($actionComponent->getName(), $corePrintActions)) {
                    if (!$canDisableCorePrinting) {
                        $config['actions'][] = $actionComponent->getConfiguration();
                    }
                } else {
                    $config['actions'][] = $actionComponent->getConfiguration();
                }
            }
        };

        $origConfig = $this->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $this->setData('config', $config);
        $this->components = [];
        parent::prepare();
    }

    /**
     * @return bool
     */
    public function canShowCustomPrintPdf()
    {
        return $this->_systemConfig->isEnablePdfInvoicePlus() && $this->_helper->getCurrentPdfTemplate()->getId();
    }

    /**
     * @return bool
     */
    public function canDisableCorePrinting()
    {
        return $this->_systemConfig->isEnablePdfInvoicePlus() && $this->_systemConfig->isDisableCorePrinting();
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}