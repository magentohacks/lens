<?php

namespace BoostMyShop\Supplier\Controller\Adminhtml\Order;

class Save extends \BoostMyShop\Supplier\Controller\Adminhtml\Order
{
    public function execute()
    {

        $poId = (int)$this->getRequest()->getParam('po_id');
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        /** @var $model \Magento\User\Model\User */
        $model = $this->_orderFactory->create()->load($poId);
        if ($poId && $model->isObjectNew()) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_redirect('supplier/order/index');
            return;
        }

        $model->setData($data);

        try {
            $model->save();

            foreach($model->getAllItems() as $item)
            {
                if (isset($data['products'][$item->getId()]))
                    $this->updateOrderProduct($item, $data['products'][$item->getId()]);
            }

            if (isset($data['products_to_add']))
                $this->addProducts($model, $data['products_to_add']);

            $model->updateDeliveryProgress();
            $model->updateTotals();
            $model->updateQtyToReceive();

            $this->messageManager->addSuccess(__('You saved the order.'));
            $this->_redirect('*/*/Edit', ['po_id' => $model->getId()]);
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($model, $data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($model, $data);
        }
    }

    /**
     * Update single order product from post data
     *
     * @param $orderProduct
     * @param $data
     */
    protected function updateOrderProduct($orderProduct, $data)
    {
        $hasChanges = false;

        if (isset($data['remove']))
            $orderProduct->delete();
        else
        {
            if (
                ($data['qty'] != $orderProduct->getPopQty())
                || ($data['price'] != $orderProduct->getPopPrice())
                || ($data['tax_rate'] != $orderProduct->getPopTaxRate())
                || ($data['supplier_sku'] != $orderProduct->getPopSupplierSku())
            )
            {
                $orderProduct->setPopQty($data['qty']);
                $orderProduct->setPopPrice($data['price']);
                $orderProduct->setPopTaxRate($data['tax_rate']);
                $orderProduct->setPopSupplierSku($data['supplier_sku']);
                $orderProduct->save();

                $hasChanges = true;
            }
        }

        return $hasChanges;
    }

    /**
     * @param $order
     * @param $productsToAdd
     */
    protected function addProducts($order, $productsToAdd)
    {
        $hasChanges = false;

        $productsToAdd = explode(';', $productsToAdd);
        foreach($productsToAdd as $item)
        {
            if (count(explode('=', $item)) == 2)
            {
                list($productId, $qty) = explode('=', $item);
                $order->addProduct($productId, $qty);
                $hasChanges = true;
            }
        }

        return $hasChanges;
    }

    /**
     * @param \Magento\User\Model\User $model
     * @param array $data
     * @return void
     */
    protected function redirectToEdit(\BoostMyShop\Supplier\Model\Order $model, array $data)
    {
        $this->_getSession()->setUserData($data);
        $arguments = $model->getId() ? ['po_id' => $model->getId()] : [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        $this->_redirect('supplier/order/edit', $arguments);
    }
}
