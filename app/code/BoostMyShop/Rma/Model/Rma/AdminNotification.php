<?php

namespace BoostMyShop\Rma\Model\Rma;

class AdminNotification
{
    protected $_config;
    protected $_transportBuilder;

    public function __construct(
        \BoostMyShop\Rma\Model\Config $config,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        $this->_config = $config;
        $this->_transportBuilder = $transportBuilder;
    }

    public function notifyForStatus($rma)
    {

        $email = $this->_config->getAdminNotificationEmail();
        if (!$email)
        {
            $rma->addHistory('Unable to notify admin (email address not set in configuration)');
        }
        else
        {
            $storeId = $rma->getrma_store_id();
            $template = $this->_config->getSetting('admin_notification/email_template', $storeId);
            $sender = $this->_config->getSetting('admin_notification/email_identity', $storeId);
            $params = $this->buildParams($rma);
            $this->_sendEmailTemplate($template, $sender, $params, $storeId, $email, 'Magento admin');
            $rma->addHistory('Admin email notification sent');
        }
    }

    public function notifyForMessage($rma, $message)
    {
        $template = $this->_config->getSetting('admin_notification/email_template_message', $rma->getrma_store_id());
        if ($template)
        {
            $email = $this->_config->getAdminNotificationEmail();
            $storeId = $rma->getrma_store_id();
            $sender = $this->_config->getSetting('admin_notification/email_identity', $storeId);
            $params = $this->buildParams($rma);
            $params['rmm_message'] = $message;

            $this->_sendEmailTemplate($template, $sender, $storeId, $email, 'Magento admin', $params);

            $rma->addHistory('Customer notification email sent for new message ');
        }
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

        foreach($rma->getData() as $k => $v)
            $datas[$k] = $v;
        if ($rma->getOrder())
        {
            $datas['order'] = [];
            foreach($rma->getOrder()->getData() as $k => $v)
            {
                if (is_string($v))
                    $datas['order'][$k] = $v;
            }
        }
        $datas['order'] = $rma->getOrder()->getData();

        return $datas;
    }

}