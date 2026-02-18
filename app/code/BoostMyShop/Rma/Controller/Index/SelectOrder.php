<?php

namespace BoostMyShop\Rma\Controller\Index;


class SelectOrder extends AbstractFront
{

    public function execute()
    {
        $redirect = $this->mustRedirect();
        if ($redirect)
            return $redirect;

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Select order to return'));

        return $resultPage;
    }

}
