<?php

namespace BoostMyShop\AdvancedStock\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class OrderItemWarehouseChange implements ObserverInterface
{
    protected $_router;
    protected $_warehouseItemFactory;

    public function __construct(
        \BoostMyShop\AdvancedStock\Model\Router $router,
        \BoostMyShop\AdvancedStock\Model\Warehouse\ItemFactory $warehouseItemFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_backendAuthSession = $backendAuthSession;
        $this->_router = $router;
        $this->_warehouseItemFactory = $warehouseItemFactory;
    }


    public function execute(EventObserver $observer)
    {
        $extendedOrderItem = $observer->getEvent()->getextended_item();
        $orderItem = $extendedOrderItem->getOrderItem();
        $oldWarehouseId = $observer->getEvent()->getold_warehouse_id();
        $newWarehouseId = $observer->getEvent()->getnew_warehouse_id();

        if ($oldWarehouseId)
            $this->updateWarehouseItem($oldWarehouseId, $orderItem->getProductId());

        $warehouseItem = $this->_warehouseItemFactory->create()->loadByProductWarehouse($orderItem->getProductId(), $newWarehouseId);
        $reservableQuantity = $warehouseItem->getReservableQuantity();
        $reservableQuantity = min($reservableQuantity, $extendedOrderItem->getQuantityToShip());
        $extendedOrderItem->setesfoi_qty_reserved($reservableQuantity)->save();

        $this->updateWarehouseItem($newWarehouseId, $orderItem->getProductId());

        return $this;
    }

    protected function updateWarehouseItem($warehouseId, $productId)
    {
        $this->_router->updateQuantityToShip($productId, $warehouseId);
        $this->_router->updateReservedQuantity($productId, $warehouseId);
    }

}
