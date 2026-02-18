<?php

namespace Lens\Manager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class InvoiceSaveAfter implements ObserverInterface
{
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * OrderRepositoryInterface $orderRepo
     *
     */
    protected $orderRepo;

    /**
     * @param OrderRepositoryInterface $orderRepo
     * @param array $data
     */
    public function __construct(
        OrderRepositoryInterface $orderRepo,
        array $data = []
    ) {
        $this->orderRepo = $orderRepo;
    }

    /**
     *
     *  @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getInvoice()->getOrder();
        $order->getId();
        $order->setState('processing')->setStatus('processing');
        $this->orderRepo->save($order);
    }
}
