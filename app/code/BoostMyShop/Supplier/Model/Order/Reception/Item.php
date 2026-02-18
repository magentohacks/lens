<?php

namespace BoostMyShop\Supplier\Model\Order\Reception;


class Item extends \Magento\Framework\Model\AbstractModel
{
    protected $_stockUpdater;
    protected $_receptionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \BoostMyShop\Supplier\Model\StockUpdater $stockUpdater,
        \BoostMyShop\Supplier\Model\Order\ReceptionFactory $receptionFactory,
        array $data = []
    )
    {
        $this->_stockUpdater = $stockUpdater;
        $this->_receptionFactory = $receptionFactory;

        parent::__construct($context, $registry, null, null, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\ResourceModel\Order\Reception\Item');
    }

    public function getReception()
    {
        return $this->_receptionFactory->create()->load($this->getpori_por_id());
    }

    public function afterSave()
    {
        //update inventory
        $po = $this->getReception()->getOrder();
        $reason = 'PO#'.$po->getpo_reference().' (supplier '.$po->getSupplier()->getsup_name().')';
        $this->_stockUpdater->incrementStock($this->getpori_product_id(), $this->getpori_qty(), $reason, $po);
    }

}
