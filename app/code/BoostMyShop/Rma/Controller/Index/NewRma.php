<?php

namespace BoostMyShop\Rma\Controller\Index;


class NewRma extends AbstractFront
{

    public function execute()
    {
        $redirect = $this->mustRedirect();
        if ($redirect)
            return $redirect;

        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_orderFactory->create()->load($orderId);
        $this->registry->register('rma_order', $order);

        $redirect = $this->checkOrderAuthorization($order);
        if ($redirect)
            return $redirect;

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Request return for Order #%1', $order->getincrement_id()));

        return $resultPage;
    }

}
