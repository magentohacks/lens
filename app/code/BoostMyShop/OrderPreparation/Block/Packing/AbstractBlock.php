<?php
namespace BoostMyShop\OrderPreparation\Block\Packing;

class AbstractBlock extends \Magento\Backend\Block\Template
{

    protected $_coreRegistry = null;
    protected $_inProgressFactory = null;
    protected $_product;
    protected $_carrierTemplateHelper = null;
    protected $_preparationRegistry;
    protected $_config = null;


    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress\CollectionFactory $inProgressFactory,
                                \BoostMyShop\OrderPreparation\Model\ProductFactory $product,
                                \BoostMyShop\OrderPreparation\Helper\CarrierTemplate $carrierTemplateHelper,
                                \BoostMyShop\OrderPreparation\Model\Config $config,
                                \BoostMyShop\OrderPreparation\Model\Registry $preparationRegistry,
                                array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;
        $this->_inProgressFactory = $inProgressFactory;
        $this->_product = $product;
        $this->_carrierTemplateHelper = $carrierTemplateHelper;
        $this->_config = $config;
        $this->_preparationRegistry = $preparationRegistry;
    }

    public function currentOrderInProgress()
    {
        return $this->_coreRegistry->registry('current_packing_order');
    }

    public function hasOrderSelect()
    {
        return ($this->currentOrderInProgress()->getId() > 0);
    }

    public function canDisplay()
    {
        return ($this->hasOrderSelect()
                    && $this->currentOrderInProgress()->getip_status() != \BoostMyShop\OrderPreparation\Model\InProgress::STATUS_SHIPPED
                    && $this->currentOrderInProgress()->getip_status() != \BoostMyShop\OrderPreparation\Model\InProgress::STATUS_PACKED
                );
    }
}