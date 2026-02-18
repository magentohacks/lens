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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * class Edit
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Edit extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->_getPdfTemplateModel();
        if (!$model->getId()) {
            $this->messageManager->addError(__('This PDF Template no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $this->_coreRegistry->register('pdftemplate_model', $model);

        /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor $block */
        $block = $resultLayout->getLayout()->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor');
        $this->getResponse()->setBody($block->toHtml());
    }
}