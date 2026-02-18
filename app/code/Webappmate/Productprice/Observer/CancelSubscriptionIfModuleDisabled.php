<?php
namespace Webappmate\Productprice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class CancelSubscriptionIfModuleDisabled implements ObserverInterface
{
    protected $logger;
    protected $moduleManager;
    protected $resource;

    public function __construct(
        LoggerInterface $logger,
        ModuleManager $moduleManager,
        ResourceConnection $resource
    ) {
        $this->logger = $logger;
        $this->moduleManager = $moduleManager;
        $this->resource = $resource;
    }

    public function execute(Observer $observer)
    {
        try {
            // Check if Amasty Subscriptions module is disabled
            if (!$this->moduleManager->isEnabled('Amasty_RecurringPayments')) {
                $this->logger->info("Amasty Subscriptions module is disabled. Cancelling all active subscriptions.");

                // Get database connection
                $connection = $this->resource->getConnection();
                $tableName = $this->resource->getTableName('amasty_recurring_payments_subscription');

                // Update subscription status to "Canceled"
                $updateQuery = "UPDATE $tableName SET status = 'canceled' WHERE status != 'canceled'";
                $connection->query($updateQuery);

                $this->logger->info("All active Amasty subscriptions have been canceled.");
            }
        } catch (\Exception $e) {
            $this->logger->error("Error canceling Amasty subscriptions: " . $e->getMessage());
        }
    }
}
