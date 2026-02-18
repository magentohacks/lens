<?php
/**
 * @Author: nguyen
 * @Date:   2020-12-15 14:01:01
 * @Last Modified by:   Alex Dong
 * @Last Modified time: 2023-08-19 16:25:12
 * https://github.com/magento/magento2-samples/tree/master/sample-module-command/Console/Command
 */

namespace Magiccart\Alothemes\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Console\Cli;

class ProductUseDefaultValue extends Command
{

    /**
     * Date argument
     */
    const ID_ARGUMENT = 'id';

    /**
     * Allow option
     */
    const ALL_STORES = 'all-stores';

    /**
     *
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     *
     * @var objectManager
     */
    private $objectManager;

    /**
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $name
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        string $name = null,
        ?LoggerInterface $logger = null
    ) {
        $this->state = $state;
        $this->objectManager = ObjectManager::getInstance();
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
       
        parent::__construct($name);
    }

    protected function configure()
    {
        // command: bin/magento ProductUseDefaultValue *
        $this->setName('ProductUseDefaultValue')
             ->setDescription('Config Product Use Default Value')
            ->setDefinition([
                new InputArgument(
                    self::ID_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Product IDS'
                ),
                new InputOption(
                    self::ALL_STORES,
                    '-a',
                    InputOption::VALUE_NONE,
                    'All Stores'
                ),

            ]);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $connection = $this->resourceConnection->getConnection();

        $returnValue = Cli::RETURN_SUCCESS;
        try {
            
            $productIds = $input->getArgument(self::ID_ARGUMENT);
            $allStores = $input->getOption(self::ALL_STORES);

            if($productIds == '*'){

                $query = $this->getQuery();
                $query = explode("\n", $query);
                foreach ($query as $sql) {
                    $sql = trim($sql);
                    if($sql) $connection->query($sql);
                }

                $message = __("Successfully set all products use default value");
            }else{

                $productIds = explode(',', (string) $productIds);
                $num = 0;
                foreach ($productIds as $productId) {
                    $num++;
                    // code...
                }

                $message = __("Successfully set use default value %1 product(s)!", $num);
            }

            $output->writeln($message);

        } catch (IOExceptionInterface $e) {
            $message = __("An error occurred while deleting your directory at %1", $e->getPath());
            $output->writeln($message);
            $output->writeln($e->getMessage());
            $returnValue = Cli::RETURN_FAILURE;

            $this->logger->critical($e->getMessage());
        }

        return $returnValue;
    }

    public function getQuery() {

        $query = <<<sql
            DELETE FROM catalog_product_entity_int where IFNULL(store_id, 0) <> 0;
            DELETE FROM catalog_product_entity_decimal where IFNULL(store_id, 0) <> 0;
            DELETE FROM catalog_product_entity_text where IFNULL(store_id, 0) <> 0;
            DELETE FROM catalog_product_entity_datetime where IFNULL(store_id, 0) <> 0;
            DELETE FROM catalog_product_entity_varchar where IFNULL(store_id, 0) <> 0;
            DELETE FROM catalog_product_entity_int where IFNULL(store_id, 0) <> 0;
            # DELETE FROM catalog_product_entity_media_gallery_value where IFNULL(store_id, 0) <> 0;

sql;

        return $query;
    }

}