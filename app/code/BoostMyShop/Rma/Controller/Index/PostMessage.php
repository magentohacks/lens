<?php

namespace BoostMyShop\Rma\Controller\Index;


class PostMessage extends AbstractFront
{

    public function execute()
    {
        $redirect = $this->mustRedirect();
        if ($redirect)
            return $redirect;

        $rmaId = $this->getRequest()->getPost('rma_id');
        $message = $this->getRequest()->getPost('message');
        $rma = $this->_rmaFactory->create()->load($rmaId);
        $redirect = $this->checkRmaAuthorization($rmaId);
        if ($redirect)
            return $redirect;

        if ($message)
        {
            $rma->addMessage('customer', $message);
            $this->messageManager->addSuccess(__('Message sent.'));
        }
        else
            $this->messageManager->addSuccess(__('You can not send an empty message.'));

        $this->_redirect('*/*/view', ['rma_id' => $rma->getId()]);
    }


}
