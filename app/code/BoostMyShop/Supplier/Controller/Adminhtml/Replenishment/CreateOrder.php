<?php

namespace BoostMyShop\Supplier\Controller\Adminhtml\Replenishment;

class CreateOrder extends \BoostMyShop\Supplier\Controller\Adminhtml\Replenishment
{
    public function execute()
    {

        $data = $this->getRequest()->getPostValue();
        $supplierId = $data['supplier'];
        $productIds = $data['massaction'];

        $order = $this->_orderFactory->create();
        $order->applyDefaultData($supplierId);
        $order->save();

        foreach($productIds as $productId)
        {
            $objReplenishment = $this->_replenishmentFactory->create()->loadByProductId($productId);

            $order->addProduct($productId, $objReplenishment->getQtyToOrder());
        }

        $this->messageManager->addSuccess(__('Order created.'));
        $this->_redirect('supplier/order/edit', ['po_id' => $order->getId()]);
    }

}
