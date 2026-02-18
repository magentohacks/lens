<?php

namespace BoostMyShop\Supplier\Model\Order;

class Notification
{
    protected $_config;
    protected $_transportBuilder;

    public function __construct(
        \BoostMyShop\Supplier\Model\Config $config,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        $this->_config = $config;
        $this->_transportBuilder = $transportBuilder;
    }

    public function notifyToSupplier($purchaseOrder)
    {

        $email = $purchaseOrder->getSupplier()->getsup_email();
        $name = $purchaseOrder->getSupplier()->getsup_contact();
        $storeId = ($purchaseOrder->getpo_store_id() ? $purchaseOrder->getpo_store_id() : 1);
        if (!$email)
            throw new \Exception('No email configured for this supplier');

        $template = $this->_config->getSetting('order/email_template', $storeId);
        $sender = $this->_config->getSetting('order/email_identity', $storeId);

        $params = $this->buildParams($purchaseOrder);

        $this->_sendEmailTemplate($template, $sender, $storeId, $email, $name, $params);
    }

    protected function _sendEmailTemplate($template, $sender, $storeId, $recipientEmail, $recipientName,  $templateParams = [])
    {
        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            $sender
        )->addTo(
            $recipientEmail,
            $recipientName
        )->getTransport();
        $transport->sendMessage();

        return $this;
    }

    protected function buildParams($purchaseOrder)
    {
        $datas = [];

        foreach($purchaseOrder->getData() as $k => $v)
            $datas[$k] = $v;

        foreach($purchaseOrder->getSupplier()->getData() as $k => $v)
            $datas[$k] = $v;

        $datas['manager_fullname'] = $purchaseOrder->getManager()->getName();
        $datas['delivery_address'] = $this->_config->getSetting('pdf/billing_address', $purchaseOrder->getpo_store_id());
        $datas['shipping_address'] = $this->_config->getSetting('pdf/shipping_address', $purchaseOrder->getpo_store_id());
        $datas['company_name'] = $this->_config->getGlobalSetting('general/store_information/name', $purchaseOrder->getpo_store_id());

        $datas['order'] = $purchaseOrder;
        $datas['supplier'] = $purchaseOrder->getSupplier();

        return $datas;
    }

}