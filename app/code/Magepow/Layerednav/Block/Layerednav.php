<?php 
namespace Magepow\Layerednav\Block;

class Layerednav extends \Magento\Framework\View\Element\Template
{
    protected $registry;
    protected $productCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
    
    public function getCurrentCategoryMaxPrice() {
        $category = $this->getCurrentCategory();
        $currencyRate = $this->getCurrencyRate();
        if($category) {

            $collection = $this->productCollectionFactory->create()->addCategoryFilter($category)->addFinalPrice();
            $rangePrice = [];

            $minPrice = $collection->getMinPrice();

            $minPrice = $currencyRate*$minPrice;
            $rangePrice['min'] = floor($minPrice);

            $maxPrice = $collection->getMaxPrice();

            $maxPrice = $currencyRate*$maxPrice;
            $rangePrice['max'] = ceil($maxPrice);

            return $rangePrice;
        } else {
            return false;
        }
    }

    /**
     * Retrieve active currency rate for filter
     *
     * @return float
     */
    public function getCurrencyRate()
    {
        $rate = $this->_getData('currency_rate');
        if ($rate === null) {
            $rate = $this->_storeManager->getStore()->getCurrentCurrencyRate();
        }
        if (!$rate) {
            $rate = 1;
        }

        return $rate;
    }

}