<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * MassStatus Change action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class MassStatus extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $Ids = $this->getRequest()->getParam('faq');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');

        if (!is_array($Ids) || empty($Ids)) {
            $this->messageManager->addError(__('Please select faq(s).'));
        } else {
            $faqCollection = $this->_faqCollectionFactory->create()
                ->setStoreViewId($storeViewId)
                ->addFieldToFilter('id', ['in' => $Ids]);
            try {
                foreach ($faqCollection as $faq) {
                    $faq->setStoreViewId($storeViewId)
                        ->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($Ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/', ['store' => $this->getRequest()->getParam('store')]);
    }
}
