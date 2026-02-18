<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Process extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    /**
     * @return void
     */
    public function execute()
    {

        $rmaId = $this->getRequest()->getParam('rma_id');
        $rma = $this->_rmaFactory->create()->load($rmaId);

        if (!$rma->hasInvoice())
            $this->messageManager->addError('The order is not invoiced, you can not process refund for this RMA');

        $this->_coreRegistry->register('current_rma', $rma);

        $breadcrumb = __('Process RMA');
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Process RMA %1', $rma->getrma_reference()));
        $this->_view->renderLayout();
    }
}
