<?php


namespace BoostMyShop\Rma\Block\Customer\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Customer\Controller\RegistryConstants;

class Rma extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_rmaCollection;
    protected $_statuses;
    protected $_storeCollectionFactory;
    protected $_userList = null;
    protected $_coreRegistry = null;
    protected $_customer;
    protected $_customerFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\CollectionFactory $rmaCollection,
        \BoostMyShop\Rma\Model\Rma\Status $statuses,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userList,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_rmaCollection = $rmaCollection;
        $this->_statuses = $statuses;
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_userList = $userList;
        $this->_pagerVisibility = false;
        $this->_coreRegistry = $registry;
        $this->_customerFactory = $customerFactory;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rmaGrid');
        $this->setDefaultSort('rma_id');
        $this->setDefaultDir('desc');
        $this->setTitle(__('Rma'));
    }

    public function getCustomer()
    {
        if (!$this->_customer)
        {
            $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
            $this->_customer = $this->_customerFactory->create()->load($customerId);
        }
        return $this->_customer;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_rmaCollection->create()->joinOrder();
        $collection->addCustomerFilter($this->getCustomer()->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn('rma_reference', ['header' => __('#'), 'filter' => false, 'sortable' => false, 'index' => 'rma_reference']);
        $this->addColumn('rma_created_at', ['header' => __('Created at'), 'filter' => false, 'sortable' => false, 'index' => 'rma_created_at']);
        $this->addColumn('rma_customer_name', ['header' => __('Customer'), 'filter' => false, 'sortable' => false, 'index' => 'rma_customer_name']);
        $this->addColumn('rma_products', ['header' => __('Products'), 'filter' => false, 'sortable' => false, 'filter' => false, 'sortable' => false, 'renderer' => 'BoostMyShop\Rma\Block\Rma\Renderer\Rma\Products']);
        $this->addColumn('rma_status', ['header' => __('Status'), 'filter' => false, 'sortable' => false, 'index' => 'rma_status', 'type' => 'options', 'options' => $this->_statuses->toOptionArray()]);
        $this->addColumn('rma_store_id', ['header' => __('Store'), 'filter' => false, 'sortable' => false, 'index' => 'rma_store_id', 'type' => 'options', 'options' => $this->getStoreOptions()]);
        $this->addColumn('rma_manager', ['header' => __('Manager'), 'filter' => false, 'sortable' => false, 'index' => 'rma_manager', 'type' => 'options', 'options' => $this->getManagerOptions()]);
        $this->addColumn('rma_customer_comments', ['header' => __('Customer comments'), 'filter' => false, 'sortable' => false, 'index' => 'rma_customer_comments']);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('rma/rma/edit', ['rma_id' => $row->getId()]);
    }

    public function getStoreOptions()
    {
        $options = [];
        $options[''] = ' ';
        foreach($this->_storeCollectionFactory->create() as $item)
        {
            $options[$item->getId()] = $item->getname();
        }
        return $options;
    }

    public function getManagerOptions()
    {
        $users = [];
        foreach($this->_userList->create() as $user)
            $users[$user->getId()] = $user->getusername();
        return $users;
    }

    public function getMainButtonsHtml()
    {
        $html = '';
        return $html;
    }

    public function isAjaxLoaded()
    {
        return false;
    }


    /**
     * ######################## TAB settings #################################
     */

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Rma');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Rma');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return ($this->getCustomer()->getId() > 0);
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !($this->getCustomer()->getId() > 0);
    }
}
