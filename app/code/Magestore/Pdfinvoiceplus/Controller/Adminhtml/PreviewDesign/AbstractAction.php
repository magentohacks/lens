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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\PreviewDesign;

use Magento\Framework\Controller\ResultFactory;

/**
 * abstract class AbstractAction
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractAction extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractActionRenderPdf
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (!$this->getPdfTemplate()->getId()) {
            $this->messageManager->addError(__('Can\' preview PDF template !'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $designType = $this->getRequest()->getParam('design_type');

        $additionHtml = '<style>body{position: relative; border: 1px solid #c9c9c9;}</style>';
        $additionHtml .= '<style>div, p, span, th, td {word-wrap: break-word;word-break: break-all;}</style>';

        $html = $this->getPdfRenderer()->render(
            $this->getRenderingEntity(),
            $this->getPdfTemplate()->getData($designType . '_html')
        );

        $preg = "/(\s)contenteditable(\s*)=(\s*)[\"']?true[\"']?/i";
        $html = preg_replace($preg, " ", $html);
        $html = $additionHtml . $html;


        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setHeader('Content-type', 'text/html');
        $resultRaw->setContents($html);

        return $resultRaw;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplate
     */
    public function getPdfTemplate()
    {
        $template_id = $this->getRequest()->getParam('template_id');

        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate */
        $pdfTemplate = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplate');
        $pdfTemplate->load($template_id);

        return $pdfTemplate;
    }

    /**
     * @return mixed
     */
    abstract public function getRenderingEntity();


    /**
     * @return \Magestore\Pdfinvoiceplus\Model\AbstractPdfTemplateRender
     */
    abstract public function getPdfRenderer();
}