<?php
namespace BoostMyShop\AdvancedStock\Console\Command;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Magento\Framework\App\ObjectManagerFactory;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Magento\Framework\Setup\SchemaSetupInterface;


class RefreshQuantityToShip extends Command
{
    protected $_pendingOrderCollectionFactory;
    protected $_config;
    protected $_router;
    protected $_state;

    /**
     * Constructor
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(
        \BoostMyShop\AdvancedStock\Model\ResourceModel\Product\PendingOrders\CollectionFactory $pendingOrderCollectionFactory,
        \BoostMyShop\AdvancedStock\Model\Config $config,
        \BoostMyShop\AdvancedStock\Model\Router $router,
        \Magento\Framework\App\State $state
    )
    {
        $this->_pendingOrderCollectionFactory = $pendingOrderCollectionFactory;
        $this->_config = $config;
        $this->_router = $router;
        $this->_state = $state;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('bms_advancedstock:refresh_quantity_to_ship')->setDescription('Refresh quantity to ship for warehouse items');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('START quantity to ship');

        $this->_state->setAreaCode('adminhtml');

        $productIds = $this->getProductIds();

        $count = count($productIds);
        $processed = 0;
        $lastProgessPercent = null;

        foreach($productIds as $productId)
        {
            $this->_router->updateQuantityToShip($productId, 1);

            $progessPercent = (int)($processed / $count * 100);
            if ($progessPercent != $lastProgessPercent)
            {
                $output->writeln('Progress : '.$progessPercent.'%');
                $lastProgessPercent = $progessPercent;
            }
            $processed++;
        }

        $output->writeln('END quantity to ship');
    }

    protected function getProductIds()
    {
        return $this->_pendingOrderCollectionFactory
                                ->create()
                                ->addExtendedDetails()
                                ->addOrderDetails()
                                ->addStatusesFilter($this->_config->getPendingOrderStatuses())
                                ->getAllProductIds();
    }

}
