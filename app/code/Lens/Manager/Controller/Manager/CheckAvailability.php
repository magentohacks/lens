<?php
namespace Lens\Manager\Controller\Manager;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Lens\Manager\Helper\Data as Helper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;

class CheckAvailability extends Action
{
    /**
     * Constructor function
     */
    public function __construct(
        Helper $helper,
        Context $context,
        Validator $validate
    ) {
        $this->helper = $helper;
        $this->validator = $validate;
        parent::__construct($context);
    }

    /**
     * Execute function for class CheckAvailibility
     */
    public function execute()
    {
        if ($this->validator->validate($this->getRequest())  && false) {
            $params = $this->getRequest()->getParams();
            $productId = $params['productId'];
            $selectedOptions = $params['selectedData'];
            $stockStatus = $this->helper->getInventoryDetails($productId, $selectedOptions);
            $data['status'] = $stockStatus;
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($data);
            return $resultJson;
        }
    }
}