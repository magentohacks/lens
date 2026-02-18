<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magiccart\Alothemes\Helper;

/**
 * Alothemes category helper
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryInstance;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
    ) {

        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryInstance = $categoryFactory->create();
        parent::__construct($context);
    }

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    public function getCategory($categoryId)
    {
        return $this->categoryRepository->get($categoryId);
    }
  
    public function getCategories($categoryIds, $limit=0)
    {
        $storeId = $this->getStoreId();
        $rootId = $this->storeManager->getStore()->getRootCategoryId();
        $collection = $this->categoryInstance->getCollection();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect(['name', 'magic_label'])
                    ->addAttributeToFilter('entity_id', $categoryIds);;
        
        $collection->addFieldToFilter('path', ['like' => '1/' . $rootId . '/%']); //load only from store root
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        // $collection->addNavigationMaxDepthFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', 'ASC');
        $collection->addOrder('position', 'ASC');
        $collection->addOrder('parent_id', 'ASC');
        $collection->addOrder('entity_id', 'ASC');

        return $limit ?  $collection->setPageSize($limit) : $collection ;
    }
    
}
