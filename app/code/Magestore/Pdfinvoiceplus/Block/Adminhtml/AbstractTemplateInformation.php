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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml;

use Magento\Store\Model\ScopeInterface;

/**
 * abstract class AbstractTemplateInformation
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractTemplateInformation extends \Magento\Backend\Block\Template
{
    const XML_PATH_DEFAULT_BUSINESS_INFO = 'pdfinvoiceplus/business_info';

    const XML_PATH_DEFAULT_BUSINESS_CONTACT = 'pdfinvoiceplus/business_contact';

    const XML_PATH_DEFAULT_ADDITIONAL_INFO = 'pdfinvoiceplus/additional_info';

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_pdfTemplateObject;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\Localization
     */
    protected $_localization;

    /**
     * AbstractTemplateInformation constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\Pdfinvoiceplus\Model\Localization $localization
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Pdfinvoiceplus\Model\Localization $localization,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_localization = $localization;
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    protected function _construct()
    {
        parent::_construct();

        if ($this->hasData('pdf_template_object')) {
            $this->setPdfTemplateObject($this->getData('pdf_template_object'));
        }
    }

    /**
     * @param \Magento\Framework\DataObject $templateObject
     *
     * @return $this
     */
    public function setPdfTemplateObject(\Magento\Framework\DataObject $templateObject)
    {
        $this->_pdfTemplateObject = $templateObject;

        return $this;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getPdfTemplateObject()
    {
        if (!$this->_pdfTemplateObject instanceof \Magento\Framework\DataObject) {
            $this->setPdfTemplateObject($this->_dataObjectFactory->create());
        }

        return $this->_pdfTemplateObject;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->_localization->setLocale($this->getPdfTemplateObject()->getData('localization'));
        $this->_localization->loadData();

        return parent::_toHtml();
    }

    /**
     * @param $word
     *
     * @return string
     */
    public function translate($word)
    {
        return $this->_localization->translate($word);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getBusinessInfo()
    {
        $businessInfo = $this->_scopeConfig->getValue(self::XML_PATH_DEFAULT_BUSINESS_INFO,
            ScopeInterface::SCOPE_STORE);

        return $this->filterInfo($businessInfo);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getBusinessContact()
    {
        $businessContact = $this->_scopeConfig->getValue(self::XML_PATH_DEFAULT_BUSINESS_CONTACT,
            ScopeInterface::SCOPE_STORE);

        return $this->filterInfo($businessContact);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getAdditionalInfo()
    {
        $addittionalInfo = $this->_scopeConfig->getValue(self::XML_PATH_DEFAULT_ADDITIONAL_INFO,
            ScopeInterface::SCOPE_STORE);

        return $this->filterInfo($addittionalInfo);
    }

    /**
     * @param array $configInfo
     *
     * @return \Magento\Framework\DataObject
     */
    public function filterInfo($configInfo = [])
    {
        $configInfo = is_array($configInfo) ? $configInfo : [];

        /** @var \Magento\Framework\DataObject $dataObject */
        $dataObject = $this->_dataObjectFactory->create([
            'data' => $configInfo
        ]);

        foreach (array_keys($dataObject->getData()) as $key) {
            $dataObject->setData($key, trim($dataObject->getData($key)));
        }

        return $dataObject;
    }
}