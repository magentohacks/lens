<?php

namespace BoostMyShop\Rma\Controller\Index;


class SubmitRequest extends AbstractFront
{

    public function execute()
    {
        $redirect = $this->mustRedirect();
        if ($redirect)
            return $redirect;

        $data = $this->getRequest()->getPost();
        $order = $this->_orderFactory->create()->load($data['order_id']);
        $additional = $data['additional'];
        $additional['rma_status'] = \BoostMyShop\Rma\Model\Rma\Status::requested;
        $items = $data['items'];

        $redirect = $this->checkOrderAuthorization($order);
        if ($redirect)
            return $redirect;

        $rma = $this->_rmaFromOrder->create($order, $additional, $items);

        $this->_adminNotification->notifyForStatus($rma);

        $this->messageManager->addSuccess(__('Your request has been submitted, our customer service will update you quickly.'));
        $this->_redirect('*/*/view', ['rma_id' => $rma->getId()]);
    }


}
