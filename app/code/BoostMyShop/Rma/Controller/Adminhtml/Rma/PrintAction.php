<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintAction extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    /**
     * @return void
     */
    public function execute()
    {
        $rmaId = $this->getRequest()->getParam('rma_id');
        $rma = $this->_rmaFactory->create()->load($rmaId);

        $pdf = $this->_objectManager->create('BoostMyShop\Rma\Model\Pdf\Rma')->getPdf([$rma]);
        return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
            'rma_' . $rma->getrma_reference(). '.pdf',
            $pdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );

    }
}
