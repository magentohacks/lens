<?php
namespace BoostMyShop\BarcodeInventory\Controller\Adminhtml\Main;

use Magento\Framework\Controller\ResultFactory;

class ProductInformation extends \Magento\Backend\App\AbstractAction
{
    protected $resultJsonFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        try
        {
            $barcode = $this->getRequest()->getParam('barcode');
            if (!$barcode)
                throw new \Exception(__('Please enter a barcode'));

            $data = $this->_objectManager->get('\BoostMyShop\BarcodeInventory\Model\ProductInformation')->getJsonDataForBarcode($barcode);
            $data['success'] = true;

            return $result->setData($data);
        }
        catch(\Exception $ex)
        {

            return $result->setData(['success' => false, 'msg' => $ex->getMessage()]);
        }

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Boostmyshop_Barcodeinventory::Main');
    }

}