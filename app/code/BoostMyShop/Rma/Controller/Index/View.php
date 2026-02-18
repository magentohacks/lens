<?php

namespace BoostMyShop\Rma\Controller\Index;



class View extends AbstractFront
{

    public function execute()
    {
        $rmaId = $this->getRequest()->getParam('rma_id');

        $redirect = $this->mustRedirect();
        if ($redirect)
            return $redirect;

        $redirect = $this->checkRmaAuthorization($rmaId);
        if ($redirect)
            return $redirect;

        $rma = $this->_rmaFactory->create()->load($rmaId);
        $this->registry->register('current_rma', $rma);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Return #%1', $rma->getrma_reference()));

        return $resultPage;
    }

}
