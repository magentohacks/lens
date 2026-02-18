<?php
namespace Webappmate\Productprice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Amasty\RecurringPayments\Model\ResourceModel\Subscription\CollectionFactory as SubscriptionCollectionFactory;
use Amasty\RecurringPayments\Model\Subscription;
use Psr\Log\LoggerInterface;

class CancelSubscription implements ObserverInterface
{
    protected $logger;
    protected $subscriptionCollectionFactory;
    protected $productRepository;

    public function __construct(
        LoggerInterface $logger,
        SubscriptionCollectionFactory $subscriptionCollectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->logger = $logger;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getProduct();
            $productId = $product->getId();

            if (!$productId) {
                return;
            }

            // Load original product data before saving
            $originalProduct = $this->productRepository->getById($productId);
            $originalSubscriptionStatus = (bool) $originalProduct->getData('am_recurring_enable'); // Amasty's field
            $newSubscriptionStatus = (bool) $product->getData('am_recurring_enable');

            // if ($originalSubscriptionStatus && !$newSubscriptionStatus) {
            if ($newSubscriptionStatus == 'no') {
                // Fetch active subscriptions for this product using collection factory
                $subscriptionCollection = $this->subscriptionCollectionFactory->create()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('status', ['neq' => Subscription::STATUS_CANCELED]); // Only active subscriptions

                foreach ($subscriptionCollection as $subscription) {
                    try {
                        // Change subscription status to "Canceled"
                        $subscription->setStatus(Subscription::STATUS_CANCELED);
                        $subscription->save(); // Save changes

                        $this->logger->info("Canceled Amasty Subscription ID {$subscription->getSubscriptionId()} for product ID {$productId}");
                    } catch (\Exception $e) {
                        $this->logger->error("Error canceling Amasty subscription: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("Error in CancelAmastySubscription observer: " . $e->getMessage());
        }
    }
}
