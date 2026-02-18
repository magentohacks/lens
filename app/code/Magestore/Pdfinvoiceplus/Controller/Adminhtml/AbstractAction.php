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
 * abstract class AbstractAction
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class  AbstractAction extends \Magento\Backend\App\Action
{
    const PARAM_CRUD_ID = 'template_id';

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
     * AbstractAction constructor.
     *
     * @param Context $context
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);
        $this->_escaper = $context->getEscaper();
        $this->_logger = $context->getLogger();
        $this->_pdfInvoiceHelper = $context->getPdfHelper();
        $this->_coreRegistry = $context->getCoreRegistry();
        $this->_imageHelper = $context->getImageHelper();
        $this->_fileFactory = $context->getFileFactory();
        $this->_optionManager = $context->getOptionManager();
        $this->_systemConfig = $context->getSystemConfig();
    }

    /**
     * Init page.
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magestore_Pdfinvoiceplus::magestore_pdfinvoiceplus')
            ->addBreadcrumb(__('PDF Invoiceplus'), __('PDF Invoiceplus'))
            ->addBreadcrumb(__('Manage PDF Template'), __('Manage PDF Template'));

        return $resultPage;
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Pdfinvoiceplus::magestore_pdfinvoiceplus');
    }

    /**
     * get back result redirect after add/edit.
     *
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _getBackResultRedirect(
        \Magento\Backend\Model\View\Result\Redirect $resultRedirect,
        $paramCrudId = null
    ) {
        switch ($this->getRequest()->getParam('back')) {
            case 'edit_design':
                $resultRedirect->setPath(
                    '*/design/edit',
                    [
                        '_current' => true,
                        static::PARAM_CRUD_ID => $paramCrudId,
                        'design_type' => $this->getRequest()->getParam('design_type'),
                    ]
                );
                break;
            case 'edit':
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        static::PARAM_CRUD_ID => $paramCrudId,
                        '_current'            => true,
                    ]
                );
                break;
            case 'new':
                $resultRedirect->setPath('*/*/new');
                break;
            default:
                $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}