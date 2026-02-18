<?php
namespace Magestore\Pdfinvoiceplus\Plugins;

use Magento\Framework\Mail\TransportInterfaceFactory;
use Magestore\Pdfinvoiceplus\Model\MailEventDispatcher;
/**
 * Class TransportFactory
 * @package Magestore\Pdfinvoiceplus\Plugins
 */
class TransportFactory
{
    /**
     * @var MailEventDispatcher
     */
    private $mailEvent;
    /**
     * TransportFactory constructor.
     *
     * @param MailEventDispatcher $mailEvent
     */
    public function __construct(MailEventDispatcher $mailEvent)
    {
        $this->mailEvent = $mailEvent;
    }
    /**
     * @param TransportInterfaceFactory $subject
     * @param \Closure $proceed
     * @param array $data
     *
     * @return mixed
     * @throws \Zend_Pdf_Exception
     */
    public function aroundCreate(
        TransportInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    )
    {
        if (isset($data['message'])) {
            $this->mailEvent->dispatch($data['message']);
        }
        return $proceed($data);
    }
}