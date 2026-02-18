<?php
/**
 * Magepow 
 * @category    Magepow 
 * @copyright   Copyright (c) 2014 Magiccart (https://magepow.com/) 
 * @license     https://magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2020-04-13 10:54:47
 * @@Function:
 */

namespace Magepow\RecentlyViewed\Block\Product;

class GridProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{

    protected $sqlBuilder;


    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_objectManager;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var _stockconfig
     */
    protected $_stockConfig;

     /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $_stockFilter;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */

    protected $_limit; // Limit Product

    /**
     * @param Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Magento\CatalogInventory\Model\Configuration $stockConfig,
        array $data = []
    ) {
        $this->urlHelper = $urlHelper;
        $this->_objectManager = $objectManager;
        $this->categoryRepository = $categoryRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_stockFilter = $stockFilter;
        $this->_stockConfig = $stockConfig;
        parent::__construct( $context, $data );
    }

    public function getTypeFilter()
    {
        $type = $this->escapeHtml($this->getRequest()->getPost('type'));
        if(!$type){
            $type = 'recently-viewed';
        }
        return $type;
    }

    public function getWidgetCfg()
    {
        $ajax = [];
        foreach (['cart', 'compare', 'wishlist', 'review', 'limit'] as $option) {
            $ajax[$option] = $this->getRequest()->getParam($option);
        }
        return $ajax;
    }

    public function getLoadedProductCollection()
    {
        $this->_limit = (int) $this->getData('limit');
        $collection = $this->getRecentlyViewedProducts();
        if ($this->_stockConfig->isShowOutOfStock() != 1) {
            $this->_stockFilter->addInStockFilterToCollection($collection);
        }
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );
        $collection->setPageSize($this->_limit)->setCurPage(1);

        return $collection;
    }

    public function getRecentlyViewedProducts()
    {    
		$producIds = $this->getProductIds();

        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter('entity_id', array('in' => $producIds));
        return $collection;
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int|null
     */
    public function getProductQty($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $qty = $stockItem->getQty();
        return $qty > 0 ? $qty : 0;
    }

    public function getCategory($categoryId)
    {
        try {
            $category = $this->categoryRepository->get($categoryId);
        } catch (\Exception $e) {
            return;
        }
        return $category;
    }

    public function getPositioned()
	{
        $positioned = parent::getPositioned();
        if($positioned == NULL){
            return '';
        }else{
            return $positioned;
        }

	}
}
