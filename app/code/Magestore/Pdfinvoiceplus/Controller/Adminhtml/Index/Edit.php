<?php

/**
 * Magestore.
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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

/**
 * Edit Pdf Template Action.
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Edit extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractAction
{
    /**
     * Edit Tag.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(self::PARAM_CRUD_ID);
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplate');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This PDF Template no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('pdftemplate_model', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Tag') : __('New PDF Template'),
            $id ? __('Edit Tag') : __('New PDF Template')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Manage PDF Template'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
                __('Edit PDF Template %1', $this->_escaper->escapeHtml($model->getTagName())) : __('New PDF Template')
        );

        return $resultPage;
    }
}
