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

namespace Magestore\Pdfinvoiceplus\Controller;

/**
 * class Context
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Context extends \Magento\Framework\App\Action\Context
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfInvoiceHelper;

    /**
     * Context constructor.
     *
     * @param \Magento\Framework\App\RequestInterface              $request
     * @param \Magento\Framework\App\ResponseInterface             $response
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager
     * @param \Magento\Framework\Event\ManagerInterface            $eventManager
     * @param \Magento\Framework\UrlInterface                      $url
     * @param \Magento\Framework\App\Response\RedirectInterface    $redirect
     * @param \Magento\Framework\App\ActionFlag                    $actionFlag
     * @param \Magento\Framework\App\ViewInterface                 $view
     * @param \Magento\Framework\Message\ManagerInterface          $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\Controller\ResultFactory          $resultFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory     $fileFactory
     * @param \Magestore\Pdfinvoiceplus\Helper\Data                $pdfInvoiceHelper
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
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magestore\Pdfinvoiceplus\Helper\Data $pdfInvoiceHelper
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
            $resultFactory
        );
        $this->_fileFactory = $fileFactory;
        $this->_pdfInvoiceHelper = $pdfInvoiceHelper;
    }

    /**
     * @return \Magento\Framework\App\Response\Http\FileFactory
     */
    public function getFileFactory()
    {
        return $this->_fileFactory;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Helper\Data
     */
    public function getPdfHelper()
    {
        return $this->_pdfInvoiceHelper;
    }
}