<?php

namespace BoostMyShop\Erp\Block\Products;

use Magento\Backend\Block\Widget\Grid\Column;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_productCollectionFactory;
    protected $_config;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \BoostMyShop\AdvancedStock\Model\Config $config,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('erpProductsGrid');
        $this->setTitle(__('ERP -Products'));
        $this->setSaveParametersInSession(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('status');
        $collection->addAttributeToSelect('thumbnail');
        $collection->addAttributeToSelect('cost');

        if ($this->_config->getBarcodeAttribute())
            $collection->addAttributeToSelect($this->_config->getBarcodeAttribute());

        $collection->addFieldToFilter('type_id', ['nin' => ['configurable', 'bundle']]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('image', ['header' => __('Image'),'filter' => false, 'sortable' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Erp\Block\Products\Renderer\Image']);
        $this->addColumn('sku', ['header' => __('Sku'), 'index' => 'sku', 'renderer' => '\BoostMyShop\Erp\Block\Products\Renderer\Sku']);
        if ($this->_config->getBarcodeAttribute())
            $this->addColumn($this->_config->getBarcodeAttribute(), ['header' => __('Barcode'), 'index' => $this->_config->getBarcodeAttribute()]);
        $this->addColumn('name', ['header' => __('Product'), 'index' => 'name']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status', 'type' => 'options', 'options' => [0 => 'Disabled', 1 => 'Enabled']]);
        $this->addColumn('cost', ['header' => __('Cost'), 'index' => 'cost']);
        $this->addColumn('stock_details', ['header' => __('Stock details'), 'filter' => false, 'sortable' => false, 'index' => 'entity_id', 'align' => 'left', 'renderer' => 'BoostMyShop\Erp\Block\Products\Renderer\StockDetails']);
        //$this->addColumn('sales_history', ['header' => __('Sales History'), 'index' => 'entity_id', 'sortable' => false, 'filter' => false, 'align' => 'center', 'renderer' => 'BoostMyShop\Erp\Block\Renderer\History']);
        $this->addColumn('suppliers', ['header' => __('Suppliers'), 'index' => 'entity_id', 'sortable' => false, 'align' => 'left', 'renderer' => 'BoostMyShop\Supplier\Block\Replenishment\Renderer\Suppliers', 'filter' => 'BoostMyShop\Supplier\Block\Replenishment\Filter\Suppliers']);
        $this->addColumn('expected_po', ['header' => __('Expected PO'), 'index' => 'entity_id', 'filter' => false, 'sortable' => false, 'align' => 'left', 'renderer' => 'BoostMyShop\Erp\Block\Products\Renderer\ExpectedPo']);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $row->getId()]);
    }



}
