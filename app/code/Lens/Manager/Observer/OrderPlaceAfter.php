<?php

namespace Lens\Manager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    protected $quote;
    /**
     * OrderRepositoryInterface $orderRepo
     *
     */
    protected $orderRepo;

    protected $quoteFactory;

    /**
     * @param OrderRepositoryInterface $orderRepo
     * @param array $data
     */
    public function __construct(
        QuoteFactory $quote,
        OrderRepositoryInterface $orderRepo,
        array $data = []
    ) {
        $this->quoteFactory = $quote;
        $this->orderRepo = $orderRepo;
    }

    /**
     *
     *  @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $quote->setIsActive(0)->save();
    }
}
