<?php

namespace Webappmate\Productprice\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentAfter implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
  
        
// $order->setState('processing')->setStatus('processing');
// $order->save();

        
//              $order->save();
        
//        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom11.log');
//         $logger = new \Zend_Log();
//         $logger->addWriter($writer);

    
//         $total_ordered_items = $order->getData('total_qty_ordered')+0;
        


// foreach ($order->getAllVisibleItems() as $item){
//    //$item->getQtyOrdered() // Number of item ordered
//    $arr[] = $item->getQtyShipped();  
//    //$item->getQtyInvoiced()
// }


// //$arr1 =  implode(",",$arr);
// $num_of_shipped_items =  array_sum($arr);


// $total_ordered_items33 ='';
// $total_ordered_items2 = '';

// if($num_of_shipped_items != $total_ordered_items)  {
//              $order->setStatus('processing');
//              $order->save();

//              $total_ordered_items2 = 'hello';

//         }else{
//              $order->setStatus('complete');
//              $order->save();
//              $total_ordered_items33 = 'string';
//         }
      
// $logger->info($total_ordered_items2);
// $logger->info('Catched event succssfully');
// $logger->info($total_ordered_items33);

// $logger->info('Catched');

    }
}