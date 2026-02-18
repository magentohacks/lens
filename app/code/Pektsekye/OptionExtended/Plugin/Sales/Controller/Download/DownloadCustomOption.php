<?php

namespace Pektsekye\OptionExtended\Plugin\Sales\Controller\Download;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Controller\Result\ForwardFactory;

class DownloadCustomOption extends \Magento\Sales\Controller\Download\DownloadCustomOption
{ 
 
    
    public function aroundExecute(\Magento\Sales\Controller\Download\DownloadCustomOption $subject, \Closure $proceed)
    {  
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var $option \Magento\Quote\Model\Quote\Item\Option */
        $option = $this->_objectManager->create(
            \Magento\Quote\Model\Quote\Item\Option::class
        )->load($quoteItemOptionId);
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        if (!$option->getId()) {
            return $resultForward->forward('noroute');
        }
 
        $optionId = null;
        if (strpos($option->getCode(), AbstractType::OPTION_PREFIX) === 0) {
            $optionId = str_replace(AbstractType::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                $optionId = null;
            }
        }
        $productOption = null;
        if ($optionId) {
            /** @var $productOption \Magento\Catalog\Model\Product\Option */
            $productOption = $this->_objectManager->create(
                \Magento\Catalog\Model\Product\Option::class
            )->load($optionId);
        }
 
        if (!$productOption || !$productOption->getId() || $productOption->getType() != 'file') {
        // to allow Option Template options
        //    return $resultForward->forward('noroute');
        }

        try {
            $serializer = $this->_objectManager->get(\Magento\Framework\Serialize\Serializer\Json::class);
            $info = $serializer->unserialize($option->getValue());
            if ($this->getRequest()->getParam('key') != $info['secret_key']) {
                return $resultForward->forward('noroute');
            }
            $this->download->downloadFile($info);
        } catch (\Exception $e) {
            return $resultForward->forward('noroute');
        }
        $this->endExecute();
        
    }


}
