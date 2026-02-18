<?php
namespace Webappmate\Productprice\Cron;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class SendEmail
{
    protected $transportBuilder;
    protected $scopeConfig;
    protected $logger;

    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $toEmail = 'info@beamingbaby.co.uk'; // Fetch dynamically from the database if needed
            $templateId = '11'; // Use the template ID from Admin

            $storeScope = ScopeInterface::SCOPE_STORE;
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope),
                'email' => $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope),
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => 'frontend',
                    'store' => 1
                ])
                ->setTemplateVars([])
                ->setFrom($sender)
                ->addTo($toEmail)
                ->getTransport();

            $transport->sendMessage();
            $this->logger->info("Scheduled email sent successfully.");
        } catch (\Exception $e) {
            $this->logger->error("Error sending email: " . $e->getMessage());
        }
    }
}
