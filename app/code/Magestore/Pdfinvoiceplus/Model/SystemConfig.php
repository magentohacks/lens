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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * class SystemConfig
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class SystemConfig
{
    /**
     *
     */
    const FULL_MODULENAME_PDFINVOICEPLUS = 'Magestore_Pdfinvoiceplus';
    /**
     *
     */
    const XML_PATH_ENABLED_PDFINVOICEPLUS = 'pdfinvoiceplus/general/enable';
    /**
     *
     */
    const XML_PATH_USE_FOR_MULTI_STORE = 'pdfinvoiceplus/general/use_for_multi_stores';
    /**
     *
     */
    const XML_PATH_DISABLE_CORE_PRINTING = 'pdfinvoiceplus/general/disable_core_printing';
    /**
     *
     */
    const XML_PATH_ALLOW_ATTACH_PDF_TO_EMAIL = 'pdfinvoiceplus/general/allow_attach_pdf_to_email';
    /**
     *
     */
    const XML_PATH_AUTO_SEND_PDF_INVOICE_STATUS = 'pdfinvoiceplus/general/auto_send_invoice_status';
    /**
     *
     */
    const XML_PATH_ALLOW_PAGE_NUMBER = 'pdfinvoiceplus/general/pdf_page_number';

    /**
     * Font family for PDF invoice printing
     */
    const XML_PATH_PDF_FONT_FAMILY = 'pdfinvoiceplus/general/pdf_font';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * SystemConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Get config by path.
     *
     * @param $path
     *
     * @return mixed
     */
    public function getConfig($path, $storeId = null)
    {
        if ($storeId !== null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function isEnablePdfInvoicePlus()
    {
        return $this->_moduleManager->isOutputEnabled(self::FULL_MODULENAME_PDFINVOICEPLUS)
        && $this->getConfig(self::XML_PATH_ENABLED_PDFINVOICEPLUS);
    }

    /**
     * @return bool
     */
    public function isUseForMultiStore()
    {
        return (boolean)$this->getConfig(self::XML_PATH_USE_FOR_MULTI_STORE);
    }

    /**
     * @return bool
     */
    public function isDisableCorePrinting()
    {
        return (boolean)$this->getConfig(self::XML_PATH_DISABLE_CORE_PRINTING);
    }

    /**
     * @return string
     */
    public function getAutoSendInvoiceStatus()
    {
        return $this->getConfig(self::XML_PATH_AUTO_SEND_PDF_INVOICE_STATUS);
    }

    /**
     * @return bool
     */
    public function allowPageNumber()
    {
        return (boolean)$this->getConfig(self::XML_PATH_ALLOW_PAGE_NUMBER);
    }

    /**
     * @return string
     */
    public function getPdfFontFamily()
    {
        return $this->getConfig(self::XML_PATH_PDF_FONT_FAMILY);
    }

    public function allowAttachPdfToEmail()
    {
        return $this->getConfig(self::XML_PATH_ALLOW_ATTACH_PDF_TO_EMAIL);
    }
}