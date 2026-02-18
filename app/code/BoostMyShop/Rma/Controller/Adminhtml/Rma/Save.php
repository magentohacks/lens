<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Save extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    public function execute()
    {

        $rmaId = (int)$this->getRequest()->getParam('rma_id');
        $data = $this->getRequest()->getPostValue();
        $items = $this->getRequest()->getPostValue('items');

        if (!$data) {
            $this->_redirect('*/*/');
            return;
        }

        /** @var $model \Magento\User\Model\User */
        $model = $this->_rmaFactory->create()->load($rmaId);
        if ($rmaId && $model->isObjectNew()) {
            $this->messageManager->addError(__('This RMA no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }



        try {
            foreach($data as $k => $v)
                $model->setData($k, $v);
            $model->save();

            //update items
            foreach($model->getAllItems() as $item)
            {
                if (isset($items[$item->getId()]))
                {
                    foreach($items[$item->getId()] as $k => $v)
                        $item->setData($k, $v);
                    $item->save();
                }
            }

            $message = $this->getRequest()->getPost('message');
            if ($message) {
                $model->addMessage('admin', $message);
                $this->messageManager->addSuccess(__('Message sent to customer.'));
            }

            $this->messageManager->addSuccess(__('You saved the RMA.'));
            $this->_redirect('*/*/Edit', ['rma_id' => $model->getId()]);
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
     * @param
     * @param array $data
     * @return void
     */
    protected function redirectToEdit(\BoostMyShop\Rma\Model\Rma $model, array $data)
    {
        $this->_getSession()->setUserData($data);
        $arguments = $model->getId() ? ['rma_id' => $model->getId()] : [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        $this->_redirect('*/*/edit', $arguments);
    }

}
