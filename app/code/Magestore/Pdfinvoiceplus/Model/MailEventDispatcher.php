<?php

namespace Magestore\Pdfinvoiceplus\Model;

use Magento\Framework\Filesystem;
// use Magento\Framework\Mail\Message;
use Magento\Framework\Mail\EmailMessage as Message;
use Magento\Framework\ObjectManagerInterface;
use Magestore\Pdfinvoiceplus\Mail\Mail;
/**
 * Class MailEventDispatcher
 * @package Mageplaza\EmailAttachments\Model
 */
class MailEventDispatcher
{
    /**
     * @var Mail
     */
    private $mail;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfInvoiceHelper;

    /**
     * MailEvent constructor.
     *
     * @param Mail $mail
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Mail $mail,
        ObjectManagerInterface $objectManager,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        \Magestore\Pdfinvoiceplus\Helper\Data $helper
    )
    {
        $this->mail = $mail;
        $this->objectManager = $objectManager;
        $this->_systemConfig = $systemConfig;
        $this->_pdfInvoiceHelper = $helper;
    }

    /**
     * @throws \Zend_Pdf_Exception
     */
    public function dispatch(Message $message)
    {
        $templateVars = $this->mail->getTemplateVars();
        if (!$templateVars) {
            return;
        }
        if ($emailType = $this->getEmailType($templateVars)) {
            /** @var \Magento\Sales\Model\Order|\Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Shipment|\Magento\Sales\Model\Order\Creditmemo $obj */
            $obj = $templateVars[$emailType];
            if ($emailType == "order" || $emailType == "invoice" || $emailType == "shipment" || $emailType == "creditmemo") {
                $this->attachmentPdf($obj, $message);
            }
        }
        $this->mail->setTemplateVars(null);
    }

    protected function attachmentPdf($order, $message)
    {
        if ($this->_isEnabledEmailAttachment()) {
            /** @var \Magento\Sales\Model\Order $result */
            $pdfTemplate = $this->_pdfInvoiceHelper->getCurrentPdfTemplate($order->getStoreId());

            /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter $printAdapter */
            $printAdapter = $this->objectManager->get('Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter');
            /** @var \Magento\Framework\DataObject $printData */
            $printData = $printAdapter->printEntity($order, $pdfTemplate);

            $body = new \Zend\Mime\Message();
            $existingEmailBody = $message->getBody();
            if (\is_object($existingEmailBody) && $existingEmailBody instanceof \Zend\Mime\Message) {
                $htmlPart = $existingEmailBody->getParts()[0];
                $htmlPart = new \Zend\Mime\Part($htmlPart->getRawContent());
                $htmlPart->type = \Zend\Mime\Mime::TYPE_HTML;
                $body->addPart($htmlPart);
            } else {
                $textPart = new \Zend\Mime\Part($existingEmailBody);
                $textPart->type = \Zend\Mime\Mime::TYPE_TEXT;
                $textPart->charset = 'utf-8';
                $textPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
                $body->addPart($textPart);
            }

            $mimeAttachment = new \Zend\Mime\Part($printData->getData('content'));
            $mimeAttachment->filename = $printData->getData('filename');
            $mimeAttachment->type = \Zend\Mime\Mime::TYPE_OCTETSTREAM;
            $mimeAttachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
            $mimeAttachment->disposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT;
            $body->addPart($mimeAttachment);

            $message->setBodyText($body);
        }
    }

    /**
     * @return bool
     */
    protected function _isEnabledEmailAttachment()
    {
        return $this->_systemConfig->allowAttachPdfToEmail() && $this->_pdfInvoiceHelper->getCurrentPdfTemplate()->getId();
    }

    /**
     * @param $templateVars
     *
     * @return bool|string
     */
    private function getEmailType($templateVars)
    {
        $emailTypes = ['invoice', 'shipment', 'creditmemo', 'order'];
        foreach ($emailTypes as $emailType) {
            if (isset($templateVars[$emailType])) {
                return $emailType;
            }
        }
        return false;
    }
}
