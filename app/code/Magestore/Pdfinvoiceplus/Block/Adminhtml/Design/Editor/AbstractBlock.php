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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor;

/**
 * Class AbstractBlock
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractBlock extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_designType = '';

    /**
     * @var mixed
     */
    protected $_pdfTemplateId;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrlBuilder;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * AbstractBlock constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Backend\Model\UrlInterface     $backendUrlBuilder
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\UrlInterface $backendUrlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_backendUrlBuilder = $backendUrlBuilder;
    }


    /**
     * Generate url by route and parameters for ajax request
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    public function getAjaxUrl($route = '', $params = [])
    {
        if (!array_key_exists('_secure', $params)) {
            $params['_secure'] = $this->_storeManager->getStore()->isCurrentlySecure();
        }

        return $this->getUrl($route, $params);
    }

    /**
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function isSizeA6OrA7()
    {
        return false;
    }

    /**
     * @param null $routePath
     * @param null $routeParams
     *
     * @return string
     */
    public function getBackendUrl($routePath = null, $routeParams = null)
    {
        return $this->_backendUrlBuilder->getUrl($routePath, $routeParams);
    }

    /**
     * @param string $designType
     *
     * @return $this
     */
    public function setDesignType($designType)
    {
        $this->_designType = $designType;

        return $this;
    }

    /**
     * @return string
     */
    public function getDesignType()
    {
        return $this->_designType;
    }

    /**
     * @param mixed $pdfTemplateId
     *
     * @return $this
     */
    public function setPdfTemplateId($pdfTemplateId)
    {
        $this->_pdfTemplateId = $pdfTemplateId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPdfTemplateId()
    {
        return $this->_pdfTemplateId;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setPdfTemplateId($this->getRequest()->getParam('template_id'));
        $this->setDesignType($this->getRequest()->getParam('design_type'));
    }

    /**
     * @return string|null
     */
    public function getPdfTemplateHtml()
    {
        return $this->getPdfTemplateModel()->getData($this->getDesignType() . '_html');
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplate
     */
    public function getPdfTemplateModel()
    {
        return $this->_coreRegistry->registry('pdftemplate_model');
    }

    /**
     * @return string
     */
    public function getIndicatorImageHtml()
    {
        return sprintf('<img src="%s" />', $this->getIndicatorImageUrl());
    }

    /**
     * @return string
     */
    public function getIndicatorImageUrl()
    {
        return $this->getViewFileUrl('Magestore_Pdfinvoiceplus::images/ui/indicator.gif');
    }


    /**
     * @return string
     */
    public function getChangeBackgroundUrl()
    {
        return $this->getAjaxUrl('pdfinvoiceplusadmin/design/uploadBackground');
    }

    /**
     * @return string
     */
    public function getAjaxUploadLogoUrl()
    {
        return $this->getAjaxUrl('pdfinvoiceplusadmin/design/uploadCompanyLogo',
            ['template_id' => $this->getPdfTemplateId()]);
    }

    /**
     * @return string
     */
    public function getVariablesUrl($type = '')
    {
        return $this->getAjaxUrl('pdfinvoiceplusadmin/design_variable/' . $type, ['isAjax' => true]);
    }

    /**
     * @return string
     */
    public function getSaveHtmlUrl($params = [])
    {
        return $this->getAjaxUrl('pdfinvoiceplusadmin/design/saveHtml', $params);
    }

    /**
     * @return string
     */
    public function getSyncInfoResetTemplateUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/design_syncInformation/resetTemplate', [
            'template_id' => $this->getPdfTemplateId(),
            'design_type' => $this->getDesignType(),
        ]);
    }
}