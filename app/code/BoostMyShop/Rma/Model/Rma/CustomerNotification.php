<?php

namespace BoostMyShop\Rma\Model\Rma;

class CustomerNotification
{
    protected $_config;
    protected $_transportBuilder;
    protected $_storeManager;

    public function __construct(
        \BoostMyShop\Rma\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        $this->_config = $config;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
    }

    public function notifyForStatus($rma)
    {
        $template = $this->_config->getSetting('notification/email_template_'.$rma->getrma_status(), $rma->getrma_store_id());
        if ($template)
        {
            $storeId = $rma->getrma_store_id();
            $sender = $this->_config->getSetting('notification/email_identity', $storeId);
            $params = $this->buildParams($rma);

            $this->_sendEmailTemplate($template, $sender, $params, $storeId, $rma->getrma_customer_email(), $rma->getrma_customer_name());

            $rma->addHistory('Customer notification email sent for status '.$rma->getrma_status());
        }
    }

    public function notifyForMessage($rma, $message)
    {
        $template = $this->_config->getSetting('notification/email_template_message', $rma->getrma_store_id());
        if ($template)
        {
            $storeId = $rma->getrma_store_id();
            $sender = $this->_config->getSetting('notification/email_identity', $storeId);
            $params = $this->buildParams($rma);
            $params['rmm_message'] = $message;

            $this->_sendEmailTemplate($template, $sender, $storeId, $rma->getrma_customer_email(), $rma->getrma_customer_name(), $params);

            $rma->addHistory('Customer notification email sent for new message ');
        }
        else
            $rma->addHistory('Can not notify customer for message, email template is not set');
    }

    protected function _sendEmailTemplate($template, $sender, $storeId, $recipientEmail, $recipientName, $templateParams = [])
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

    protected function buildParams($rma)
    {
        $datas = [];

        foreach ($rma->getData() as $k => $v) {
            if (is_string($v))
                $datas[$k] = $v;
        }

        if ($rma->getOrder())
        {
            foreach($rma->getOrder()->getData() as $k => $v)
            {
                if (is_string($v))
                    $datas['order_'.$k] = $v;
            }
        }

        $datas['company_name'] = $this->_config->getCompanyName($rma->getrma_store_id());

        $datas['download_pdf_url'] = $this->_storeManager->getStore($rma->getrma_store_id())->getUrl('rma/index/download', ['rma_id' => $rma->getId(), 'key' => $rma->getKey(), '_nosid' => 1]);

        return $datas;
    }

}