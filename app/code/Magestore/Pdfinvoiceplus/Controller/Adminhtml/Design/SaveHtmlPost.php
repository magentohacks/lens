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
 * class SaveHtmlPost
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class SaveHtmlPost extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
            $designType = $this->getRequest()->getParam('design_type');

            /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
            $model = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplate')->load($id);
            if (isset($data['edit_html'])) {
                $model->setData($designType . '_html', $data['edit_html']);
            }
            try {

                $model->save();

                $this->messageManager->addSuccess(__('The PDF Tempalte has been saved.'));

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addError(__('Something went wrong while saving the PDF Tempalte.'));

                return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $id]);
            }

            return $resultRedirect->setPath('pdfinvoiceplusadmin/design/advanceEdit', [
                'design_type' => $this->getRequest()->getParam('design_type'),
                'template_id' => $id,
            ]);
        }

        return $resultRedirect->setPath('pdfinvoiceplusadmin/index');
    }
}