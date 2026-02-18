<?php

namespace BoostMyShop\Rma\Controller\Index;


class Index extends AbstractFront
{

    public function execute()
    {
        $redirect = $this->mustRedirect();
        if ($redirect)
            return $redirect;

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Returns'));

        return $resultPage;
    }

}
