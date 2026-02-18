<?php

namespace BoostMyShop\AdvancedStock\Block\StockMovement;

use Magento\Backend\Block\Widget\Grid\Column;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_stockMovementCollectionFactory;
    protected $_warehouseCollectionFactory;
    protected $_categories;
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BoostMyShop\AdvancedStock\Model\ResourceModel\StockMovement\CollectionFactory $stockMovementCollectionFactory,
        \BoostMyShop\AdvancedStock\Model\ResourceModel\Warehouse\CollectionFactory $warehouseCollectionFactory,
        \BoostMyShop\AdvancedStock\Model\StockMovement\Category $categories,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_stockMovementCollectionFactory = $stockMovementCollectionFactory;
        $this->_warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->_categories = $categories;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('stockMovementGrid');
        $this->setDefaultSort('sm_created_at');
        $this->setDefaultDir('DESC');
        $this->setTitle(__('Stock Movements'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_stockMovementCollectionFactory->create();
        $this->_addAdditionnalFilterForCollection($collection);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _addAdditionnalFilterForCollection(&$collection)
    {
        return $collection;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        //$this->addColumn('sm_id', ['header' => __('ID'), 'index' => 'sm_id']);
        $this->addColumn('sm_created_at', ['header' => __('Date'), 'index' => 'sm_created_at']);
        $this->addColumn('sku', ['header' => __('Sku'), 'index' => 'sku', 'renderer' => 'BoostMyShop\AdvancedStock\Block\StockMovement\Renderer\Sku']);
        $this->addColumn('name', ['header' => __('Product'), 'index' => 'name']);
        $this->addColumn('sm_from_warehouse_id', ['header' => __('From'), 'align' => 'center', 'index' => 'sm_from_warehouse_id', 'type' => 'options', 'options' => $this->getWarehouseOptions()]);
        $this->addColumn('sm_to_warehouse_id', ['header' => __('To'), 'align' => 'center', 'index' => 'sm_to_warehouse_id', 'type' => 'options', 'options' => $this->getWarehouseOptions()]);
        $this->addColumn('sm_qty', ['header' => __('Qty'), 'align' => 'center', 'type' => 'number', 'index' => 'sm_qty']);
        $this->addColumn('sm_category', ['header' => __('Category'), 'index' => 'sm_category', 'type' => 'options', 'options' => $this->_categories->toOptionArray()]);
        $this->addColumn('sm_comments', ['header' => __('Comments'), 'index' => 'sm_comments']);

        return parent::_prepareColumns();
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }

    public function getWarehouseOptions()
    {
        $options = [];
        foreach($this->_warehouseCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getw_name();
        }
        return $options;
    }

}
