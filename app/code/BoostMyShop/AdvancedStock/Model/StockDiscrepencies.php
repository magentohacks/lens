<?php

namespace BoostMyShop\AdvancedStock\Model;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class StockDiscrepencies
{
    private $objectManagerFactory;
    private $objectManager;
    /**
     * Constructor
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(
        ObjectManagerFactory $objectManagerFactory,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->objectManagerFactory = $objectManagerFactory;
        $this->_filesystem = $filesystem;
    }

    public function run($fix = false)
    {
        $results = [];

        foreach($this->getAnalysers() as $item)
            $item->run($results, $fix);

        $this->hydatesResults($results);

        $this->saveResults($results);

        return $results;
    }

    public function getAnalysers()
    {
        $analysers = [];
        $analysers[] = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockDiscrepencies\MissingWarehouseItems');
        $analysers[] = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockDiscrepencies\NegativeStock');
        $analysers[] = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockDiscrepencies\WrongStockItemQuantity');
        $analysers[] = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockDiscrepencies\WrongWarehouseItemQuantity');
        $analysers[] = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockDiscrepencies\WrongQuantityToShip');
        $analysers[] = $this->getObjectManager()->create('BoostMyShop\AdvancedStock\Model\StockDiscrepencies\UnconsistantReservedQuantity');

        return $analysers;
    }

    protected function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = FrontNameResolver::AREA_CODE;
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            $appState = $this->objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->objectManager->configure($configLoader->load($area));
        }
        return $this->objectManager;
    }

    public function hydatesResults(&$results)
    {
        foreach($results as $k => $item)
        {
            if (count($results[$k]['items']) > 0)
                $results[$k]['status'] = 'error';
            else
                $results[$k]['status'] = 'success';
        }
    }

    public function saveResults($results)
    {
        $path = $this->getFilePath();
        file_put_contents($path, json_encode($results));
    }

    public function hasReport()
    {
        return file_exists($this->getFilePath());
    }

    public function getData()
    {
        if ($this->hasReport())
            return json_decode(file_get_contents($this->getFilePath()));
        else
            return '';
    }

    public function getFilePath()
    {
        $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::TMP)->getAbsolutePath();
        if (!file_exists($dir))
            mkdir($dir);
        return $this->_filesystem->getDirectoryWrite(DirectoryList::TMP)->getAbsolutePath('stock_discrepencies.json');
    }
}