<?php
namespace Webappmate\Productprice\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
 
class PricecalculationsAfterAddtoCart2 implements ObserverInterface
{
       /**
    * @var JsonSerializer
    */
    private $serializer;

    private $_request;

    public function __construct(
    \Magento\Framework\Serialize\Serializer\Json $serializer,
    \Magento\Framework\App\RequestInterface $request

)
{
     $this->serializer = $serializer;
    $this->_request = $request;
}
     
public function execute(\Magento\Framework\Event\Observer $observer) 
{
           // $option_right = $this->_request->getParam('selected_configurable_option_right');
           // $option_left = $this->_request->getParam('selected_configurable_option');



$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
$logger = new \Zend_Log();
$logger->addWriter($writer);

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
    if($test1 != ''){
         $myattribute = $product->getResource()->getAttribute('promotional_price')->getFrontend()->getValue($product);
        if ($this->_request->getFullActionName() == 'checkout_cart_add') { //checking when product is adding to cart
            // $product = $observer->getProduct();
            // $additionalOptions = [];
            // $additionalOptions[] = array(
            //     'label' => "Promotional Discount Price",
            //     'value' => $myattribute
            // );
            // $observer->getProduct()->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
        }
    
            $logger->info($test1);
}


            
            
    }
}