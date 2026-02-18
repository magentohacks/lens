<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * MassDelete action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class MassDelete extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $Ids = $this->getRequest()->getParam('faq');
        if (!is_array($Ids) || empty($Ids)) {
            $this->messageManager->addError(__('Please select faq(s).'));
        } else {
            $faqCollection = $this->_faqCollectionFactory->create()
                ->addFieldToFilter('id', ['in' => $Ids]);
            try {
                foreach ($faqCollection as $faq) {
                    $faq->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($Ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
