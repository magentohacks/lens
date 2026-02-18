<?php
namespace Lens\Manager\Controller\Adminhtml\Import;

use Magento\Framework\App\Action\Action;
use Lens\Manager\Model\LensPrescriptionsFactory;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;

class Prescriptions extends Action
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
        $file = $_FILES['csv'];
        $this->import($file);
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $this->messageManager->addSuccess('Prescriptions uploaded successfully');
        return $resultRedirect;         
    }

    //The function name should match your controller path
    public function import($file)
    {
        if (!isset($file['tmp_name'])) 
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));

        $csvData = $this->csv->getData($file['tmp_name']);

        foreach ($csvData as $row => $data) {
            foreach ($data as $kk => $ll) {
                $data[$kk] = (string)$ll;
            }
            if ($row > 0) {
                $indexArray = $csvData[0];
               $data =  array_combine($indexArray,$data);
                // echo "<pre>";print_r($data);die;


                $this->pescriptionFactory->create()->setData($data)->save();
                //Start your work

            }
        }
    }
}