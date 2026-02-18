<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Notify extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    public function execute()
    {

        $rmaId = (int)$this->getRequest()->getParam('rma_id');




        try {
            $rma = $this->_rmaFactory->create()->load($rmaId);
            $rma->notifyCustomer();
            $this->messageManager->addSuccess(__('Customer notified.'));
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }

        }

        $this->_redirect('*/*/Edit', ['rma_id' => $rmaId]);
    }


}
