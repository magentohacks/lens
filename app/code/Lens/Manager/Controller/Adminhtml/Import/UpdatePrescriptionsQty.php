<?php
namespace Lens\Manager\Controller\Adminhtml\Import;

use Magento\Framework\App\Action\Action;
use Lens\Manager\Model\LensPrescriptionsFactory;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;

class UpdatePrescriptionsQty extends Action
{
    protected $csv;

    public function __construct(
        Context $context,
        LensPrescriptionsFactory $lensPrescriptionsFactory,
        ResultFactory $result,
        \Magento\Framework\File\Csv $csv
    ) {
        $this->csv = $csv;
        $this->resultRedirect = $result;
        $this->pescriptionFactory = $lensPrescriptionsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $file = $_FILES['csvupdate'];
        $this->import($file);
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $this->messageManager->addSuccess('Quantities updated successfully');
        return $resultRedirect;         
    }

    //The function name should match your controller path
    public function import($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $csvData = $this->csv->getData($file['tmp_name']);
        $i = 1;
        foreach ($csvData as $index => $value) {
            $prescriptions = $this->pescriptionFactory->create()->getCollection()->addFieldToFilter('gtin', ['eq' => $csvData[$index]['0']]);
            foreach ($prescriptions as $onePrescription) {
                $onePrescription->setQuantity($csvData[$index]['1'] + $onePrescription->getQuantity())->save();
            }
        }
    }
}