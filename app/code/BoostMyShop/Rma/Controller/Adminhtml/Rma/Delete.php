<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Delete extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($rmaId = $this->getRequest()->getParam('rma_id')) {
            try {

                $model = $this->_rmaFactory->create();
                $model->setId($rmaId);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the RMA.'));
                $this->_redirect('*/*/index');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['rma_id' => $rmaId]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find the RMA to delete.'));
        $this->_redirect('*/*/index');
    }
}
