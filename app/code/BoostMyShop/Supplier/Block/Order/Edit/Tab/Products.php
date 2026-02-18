<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;

    protected $_orderProductFactory = null;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

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
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \BoostMyShop\Supplier\Model\ResourceModel\Order\Product\CollectionFactory $orderProductFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_orderProductFactory = $orderProductFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);

        $this->setPagerVisibility(false);
        $this->setMessageBlockVisibility(false);
        $this->setDefaultLimit(200);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderProductsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Products'));
        $this->setUseAjax(true);
    }

    protected function getOrder()
    {
        return $this->_coreRegistry->registry('current_purchase_order');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_orderProductFactory->create();
        $collection->addOrderFilter($this->getOrder()->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('image', ['header' => __('Image'), 'sortable' => false, 'filter' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\Image']);
        $this->addColumn('pop_sku', ['header' => __('Sku'), 'filter' => false, 'index' => 'pop_sku', 'type' => 'text', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\Sku']);
        $this->addColumn('pop_supplier_sku', ['header' => __('Supplier Sku'), 'filter' => false, 'index' => 'pop_supplier_sku', 'type' => 'text', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\SupplierSku']);
        $this->addColumn('pop_name', ['header' => __('Name'), 'filter' => false, 'index' => 'pop_name', 'type' => 'text']);
        $this->addColumn('pop_qty', ['header' => __('Qty Ordered'), 'filter' => false, 'index' => 'pop_qty', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\Qty', 'align' => 'center']);
        $this->addColumn('pop_qty_received', ['header' => __('Qty Received'), 'filter' => false, 'index' => 'pop_qty_received', 'type' => 'text', 'align' => 'center']);
        $this->addColumn('pop_buying_price', ['header' => __('Buying price (%1)', $this->getOrder()->getCurrency()->getCode()), 'filter' => false, 'index' => 'pop_price', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\Price', 'align' => 'center']);
        $this->addColumn('pop_tax_rate', ['header' => __('Tax rate %'), 'filter' => false, 'index' => 'pop_tax_rate', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\TaxRate', 'align' => 'center']);

        $this->addColumn('stock_details', ['header' => __('Stock details'),'filter' => false, 'sortable' => false, 'type' => 'renderer', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\StockDetails']);
        $this->addColumn('pop_remove', ['header' => __('Remove'), 'filter' => false, 'sortable' => false, 'index' => 'pop_id', 'renderer' => '\BoostMyShop\Supplier\Block\Order\Edit\Tab\Products\Renderer\Remove', 'align' => 'center']);


        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        //nothing, hide buttons
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', ['po_id' => $this->getOrder()->getId()]);
    }

}
