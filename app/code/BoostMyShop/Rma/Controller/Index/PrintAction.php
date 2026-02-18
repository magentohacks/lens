<?php

namespace BoostMyShop\Rma\Controller\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintAction extends AbstractFront
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

        $pdf = $this->_objectManager->create('BoostMyShop\Rma\Model\Pdf\Rma')->getPdf([$rma]);
        return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
            'rma_' . $rma->getrma_reference(). '.pdf',
            $pdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );

    }

}
