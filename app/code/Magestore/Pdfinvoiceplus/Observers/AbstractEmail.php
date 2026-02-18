<?php

namespace Magestore\Pdfinvoiceplus\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magestore\Pdfinvoiceplus\Mail\Mail;
/**
 * Class AbstractEmail
 * @package Magestore\Pdfinvoiceplus\Observers
 */
class AbstractEmail implements ObserverInterface
{
    /**
     * @var Mail
     */
    private $mail;
    /**
     * TransportBuilder constructor.
     *
     * @param Mail $mail
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->mail->setTemplateVars($observer->getTransport());
    }
}