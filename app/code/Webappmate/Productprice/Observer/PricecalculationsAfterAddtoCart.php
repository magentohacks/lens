<?php
namespace Webappmate\Productprice\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
 
class PricecalculationsAfterAddtoCart implements ObserverInterface
{
    private $_request;
    
    public function __construct(
    \Magento\Framework\App\RequestInterface $request
)
{
    $this->_request = $request;
}
     
public function execute(\Magento\Framework\Event\Observer $observer) 
{
           // $option_right = $this->_request->getParam('selected_configurable_option_right');
           // $option_left = $this->_request->getParam('selected_configurable_option');


$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom2.log');
$logger = new \Zend_Log();
$logger->addWriter($writer);
$logger->info('testtt');
        
            // $writer = new \Zend\Log\Writer\Stream(BP.'/var/log/stackexchange.log');
            // $logger = new \Zend\Log\Logger();
            // $logger->addWriter($writer);
            $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\UrlInterface::class);
            $currentUrl = $urlInterface->getCurrentUrl();
            $product = $observer->getEvent()->getProduct();

            $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\UrlInterface::class);
            $currentUrl = $urlInterface->getCurrentUrl();
            $logger->info($currentUrl);
                $test = '';
                $test1 = '';
                $array = explode('?', $currentUrl); 
                //print_r($array);
                $test = $array['0'];
                //$test1 = $array['1'];
            $key_to_check = '1';
            if(isset($array[$key_to_check])){
                $test1 = $array['1'];
            }
             $logger->info($test1);
    if($test1 != ''){
    $myattribute = $product->getResource()->getAttribute('promotional_price')->getFrontend()->getValue($product);
            $pId = $product->getId();
            $abc = $product->getFinalPrice();
            $currentUrl1 = $product->getProductUrl();
            $final_price = $abc - $myattribute;
            $quote_item = $observer->getEvent()->getQuoteItem();
            $price = $final_price; //set your price here
            $quote_item->setCustomPrice($price);
            $quote_item->setOriginalCustomPrice($price);
            $quote_item->getProduct()->setIsSuperMode(true);

            $logger->info($final_price);
}


            
            
    }
}