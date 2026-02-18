<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * Edit Category action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Edit extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
		$storeViewId = $this->getRequest()->getParam('store');
		$model = $this->_objectManager->create('Konstant\Managefaq\Model\Category');
			
        if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->messageManager->addError(__('This category no longer exists.'));
				$resultRedirect = $this->_resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
            }
        }
		$data = $this->_getSession()->getFormData(true);
		if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('category', $model);
		$resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
