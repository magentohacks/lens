<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * Edit FAQ action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Edit extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_faqFactory->create();

        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This FAQ no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('faq', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
