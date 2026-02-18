<?php

namespace BoostMyShop\OrderPreparation\Block\Preparation;

use Magento\Backend\Block\Widget\Grid\Column;

class InProgress extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;

    protected $_inProgressFactory = null;

    protected $_preparationRegistry;

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
        \BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress\CollectionFactory $inProgressFactory,
        \Magento\Framework\Registry $coreRegistry,
        \BoostMyShop\OrderPreparation\Model\Registry $preparationRegistry,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_inProgressFactory = $inProgressFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_preparationRegistry = $preparationRegistry;

        parent::__construct($context, $backendHelper, $data);

        $this->setMessageBlockVisibility(false);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('tab_in_progress');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_inProgressFactory->create();
        $collection->addOrderDetails();

        $userId = $this->_preparationRegistry->getCurrentOperatorId();
        $warehouseId = $this->_preparationRegistry->getCurrentWarehouseId();

        $collection->addUserFilter($userId);
        $collection->addWarehouseFilter($warehouseId);
        //$collection->addStoreFilter($storeId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('increment_id', ['header' => __('#'), 'index' => 'increment_id']);
        $this->addColumn('created_at', ['header' => __('Date'), 'index' => 'created_at']);
        $this->addColumn('status', ['header' => __('Order Status'), 'index' => 'status']);
        $this->addColumn('store_id', ['header' => __('Store'), 'index' => 'store_id', 'renderer' => '\Magento\Backend\Block\Widget\Grid\Column\Renderer\Store']);
        $this->addColumn('customer_name', ['header' => __('Customer'), 'index' => 'customer_name', 'renderer' => '\BoostMyShop\OrderPreparation\Block\Preparation\Renderer\CustomerName']);
        $this->addColumn('shipping_description', ['header' => __('Shipping method'), 'index' => 'shipping_description']);
        $this->addColumn('products', ['header' => __('Products'), 'index' => 'index_id', 'renderer' => '\BoostMyShop\OrderPreparation\Block\Preparation\Renderer\InProgressProducts']);
        $this->addColumn('ip_status', ['header' => __('Preparation Status'), 'index' => 'ip_status']);
        $this->addColumn('action', ['header' => __('Action'), 'index' => 'index_id', 'align' => 'center', 'filter' => false, 'renderer' => '\BoostMyShop\OrderPreparation\Block\Preparation\Renderer\InProgressActions']);

        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        //nothing, hide buttons
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/ajaxGrid', ['_current' => true, 'grid' => 'selected']);
    }

}
