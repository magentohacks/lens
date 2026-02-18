<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

class MassStatus extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $categoryIds = $this->getRequest()->getParam('category');
        $status = $this->getRequest()->getParam('status');
		
        if (!is_array($categoryIds) || empty($categoryIds)) {
            $this->messageManager->addError(__('Please select category(s).'));
        } else {
            try {
				foreach($categoryIds as $categoryId) {
					$categoryCollection = $this->_objectManager->create('Konstant\Managefaq\Model\Category')->load($categoryId);
					$categoryCollection->setStatus($status)
						->setIsMassupdate(true)
						->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($categoryIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
