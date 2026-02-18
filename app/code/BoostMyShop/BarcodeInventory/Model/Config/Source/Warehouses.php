<?php

namespace BoostMyShop\BarcodeInventory\Model\Config\Source;

class Warehouses implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\CollectionFactory
     */
    protected $_collectionFactory;

    public function __construct(\Magento\CatalogInventory\Model\ResourceModel\Stock\CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options = array();
        $collection = $this->_collectionFactory->create();

        foreach($collection as $item)
        {
            $options[] = array('value' => $item->getId(), 'label' => $item->getStockName());
        }

        return $options;
    }

}
