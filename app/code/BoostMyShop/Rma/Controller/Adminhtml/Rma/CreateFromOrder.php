<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class CreateFromOrder extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{

    /**
     * @return void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_orderFactory->create()->load($orderId);

        $rma = $this->_createFromOrder->create($order);

        $this->messageManager->addSuccess(__('New RMA created.'));
        $this->_redirect('*/*/edit', ['rma_id' => $rma->getId()]);
    }
}
