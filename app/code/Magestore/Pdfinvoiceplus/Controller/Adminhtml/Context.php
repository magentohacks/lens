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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml;

/**
 * class Context
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Context extends \Magento\Backend\App\Action\Context
{
    /**
     * Escaper.
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfInvoiceHelper;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\OptionManager
     */
    protected $_optionManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * Context constructor.
     *
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Framework\App\ResponseInterface           $response
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param \Magento\Framework\Event\ManagerInterface          $eventManager
     * @param \Magento\Framework\UrlInterface                    $url
     * @param \Magento\Framework\App\Response\RedirectInterface  $redirect
     * @param \Magento\Framework\App\ActionFlag                  $actionFlag
     * @param \Magento\Framework\App\ViewInterface               $view
     * @param \Magento\Framework\Message\ManagerInterface        $messageManager
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\Controller\ResultFactory        $resultFactory
     * @param \Magento\Backend\Model\Session                     $session
     * @param \Magento\Framework\AuthorizationInterface          $authorization
     * @param \Magento\Backend\Model\Auth                        $auth
     * @param \Magento\Backend\Helper\Data                       $helper
     * @param \Magento\Backend\Model\UrlInterface                $backendUrl
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator
     * @param \Magento\Framework\Locale\ResolverInterface        $localeResolver
     * @param \Magestore\Pdfinvoiceplus\Helper\Data              $pdfInvoiceHelper
     * @param \Magento\Framework\Escaper                         $escaper
     * @param \Magento\Framework\Registry                        $coreRegistry
     * @param bool                                               $canUseBaseUrl
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magestore\Pdfinvoiceplus\Helper\Data $pdfInvoiceHelper,
        \Magestore\Pdfinvoiceplus\Helper\Image $imageHelper,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magestore\Pdfinvoiceplus\Model\OptionManager $optionManager,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory,
            $session,
            $authorization,
            $auth,
            $helper,
            $backendUrl,
            $formKeyValidator,
            $localeResolver,
            $canUseBaseUrl
        );

        $this->_pdfInvoiceHelper = $pdfInvoiceHelper;
        $this->_imageHelper = $imageHelper;
        $this->_escaper = $escaper;
        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_optionManager = $optionManager;
        $this->_systemConfig = $systemConfig;
    }

    /**
     * @return \Magento\Framework\Escaper
     */
    public function getEscaper()
    {
        return $this->_escaper;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Helper\Data
     */
    public function getPdfHelper()
    {
        return $this->_pdfInvoiceHelper;
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getCoreRegistry()
    {
        return $this->_coreRegistry;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->_imageHelper;
    }

    /**
     * @return \Magento\Framework\App\Response\Http\FileFactory
     */
    public function getFileFactory()
    {
        return $this->_fileFactory;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\OptionManager
     */
    public function getOptionManager()
    {
        return $this->_optionManager;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }
}