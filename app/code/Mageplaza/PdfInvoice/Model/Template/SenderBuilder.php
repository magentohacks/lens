<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Model\Template;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Helper\PrintProcess;
use Mageplaza\PdfInvoice\Model\Source\Type;
use Mageplaza\PdfInvoice\Model\Template\TransportBuilder as TransportBuilderPdf;

/**
 * Class SenderBuilder
 * @package Mageplaza\PdfInvoice\Model\Template
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var PrintProcess
     */
    protected $printHelper;

    /**
     * @var CoreSession
     */
    protected $_coreSession;

    /**
     * @var TransportBuilderPdf
     */
    protected $transportBuilderPdf;

    /**
     * SenderBuilder constructor.
     *
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param TransportBuilder $transportBuilder
     * @param Data $helper
     * @param PrintProcess $printHelper
     * @param SessionManagerInterface $sessionManager
     * @param TransportBuilderPdf $transportBuilderPdf
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        Data $helper,
        PrintProcess $printHelper,
        SessionManagerInterface $sessionManager,
        TransportBuilderPdf $transportBuilderPdf
    ) {
        $this->helper              = $helper;
        $this->printHelper         = $printHelper;
        $this->_coreSession        = $sessionManager;
        $this->transportBuilderPdf = $transportBuilderPdf;

        parent::__construct($templateContainer, $identityContainer, $transportBuilder);
    }

    /**
     * @inheritdoc
     * @throws MailException|LocalizedException
     */
    public function send()
    {
        if ($this->helper->versionCompare('2.4.8')) {
            [$content, $fileName] = $this->getAttachPDFData();

            if (!$content) {
                parent::send();

                return;
            }

            $this->configureEmailTemplate();
            $this->transportBuilder->addTo(
                $this->identityContainer->getCustomerEmail(),
                $this->identityContainer->getCustomerName()
            );

            $copyTo = $this->identityContainer->getEmailCopyTo();
            if (!empty($copyTo) && $this->identityContainer->getCopyMethod() === 'bcc') {
                foreach ($copyTo as $email) {
                    $this->transportBuilder->addBcc($email);
                }
            }

            $transport      = $this->transportBuilder->getTransport();
            $message        = $transport->getMessage();
            $symfonyMessage = $message->getSymfonyMessage();
            $attachment     = new \Symfony\Component\Mime\Part\DataPart(
                $content, $fileName, \Magento\Framework\HTTP\Mime::TYPE_OCTETSTREAM
            );
            $symfonyMessage->setBody(
                new \Symfony\Component\Mime\Part\Multipart\MixedPart($symfonyMessage->getBody(), $attachment)
            );

            $transport->sendMessage();
        } else {
            // Legacy approach
            $attachPdf = $this->attachPDF();
            if ($attachPdf) {
                $this->configureEmailTemplate();
                $this->transportBuilder->addTo(
                    $this->identityContainer->getCustomerEmail(),
                    $this->identityContainer->getCustomerName()
                );
                $copyTo = $this->identityContainer->getEmailCopyTo();
                if (!empty($copyTo) && $this->identityContainer->getCopyMethod() === 'bcc') {
                    foreach ($copyTo as $email) {
                        $this->transportBuilder->addBcc($email);
                    }
                }
                $this->attachEmail($attachPdf);
            } else {
                parent::send();
            }
        }
    }

    /**
     * @inheritdoc
     * @throws MailException|LocalizedException
     */
    public function sendCopyTo()
    {
        if ($this->helper->versionCompare('2.4.8')) {
            [$content, $fileName] = $this->getAttachPDFData();
            if (!$content) {
                parent::sendCopyTo();

                return;
            }

            $this->configureEmailTemplate();
            $copyTo = $this->identityContainer->getEmailCopyTo();

            if (!empty($copyTo)) {
                foreach ($copyTo as $email) {
                    $this->transportBuilder->addTo($email);
                    $transport      = $this->transportBuilder->getTransport();
                    $message        = $transport->getMessage();
                    $symfonyMessage = $message->getSymfonyMessage();
                    $attachment     = new \Symfony\Component\Mime\Part\DataPart(
                        $content, $fileName, \Magento\Framework\HTTP\Mime::TYPE_OCTETSTREAM
                    );
                    $symfonyMessage->setBody(
                        new \Symfony\Component\Mime\Part\Multipart\MixedPart($symfonyMessage->getBody(), $attachment)
                    );
                    $transport->sendMessage();
                }
            }
        } else {
            $attachPdf = $this->attachPDF();
            if ($attachPdf) {
                $copyTo = $this->identityContainer->getEmailCopyTo();
                if (!empty($copyTo) && $this->identityContainer->getCopyMethod() === 'copy') {
                    foreach ($copyTo as $email) {
                        $this->configureEmailTemplate();
                        $this->transportBuilder->addTo($email);
                        $this->attachEmail($attachPdf);
                    }
                }
            } else {
                parent::sendCopyTo();
            }
        }
    }

    public function getAttachPDFData()
    {
        $templateVars = $this->templateContainer->getTemplateVars();
        $storeId      = $templateVars['store']->getId();

        if (!$this->helper->isEnabled($storeId)) {
            return [false, false];
        }

        try {
            $type   = null;
            $entity = null;
            $order  = null;

            if (isset($templateVars['invoice'])) {
                $type   = Type::INVOICE;
                $entity = $templateVars['invoice'];
            } elseif (isset($templateVars['shipment'])) {
                $type   = Type::SHIPMENT;
                $entity = $templateVars['shipment'];
            } elseif (isset($templateVars['creditmemo'])) {
                $type   = Type::CREDIT_MEMO;
                $entity = $templateVars['creditmemo'];
            } else {
                $type   = Type::ORDER;
                $entity = $templateVars['order'];
            }

            $order   = ($type === Type::ORDER) ? $entity : $entity->getOrder();
            $content = $this->printHelper->processPDFTemplate($type, $templateVars, $storeId, $order);

            if (empty($content)) {
                return [false, false];
            }

            $fileName = $this->generateFileName($type, $entity, $storeId);

            return [$content, $fileName];
        } catch (Exception $e) {
            return [false, false];
        }
    }

    private function generateFileName($type, $entity, $storeId)
    {
        $defaultName    = ucfirst($type) . $entity->getIncrementId();
        $configFileName = $this->helper->getFileName($type, $storeId);

        if (!$configFileName) {
            return $defaultName;
        }

        if (strpos($configFileName, '%increment_id') !== false) {
            return str_replace('%increment_id', $entity->getIncrementId(), $configFileName);
        }

        return $configFileName;
    }

    /**
     * Attach pdf
     *
     * @return bool|TransportBuilder
     */
    public function attachPDF()
    {
        $templateVars = $this->templateContainer->getTemplateVars();
        $storeId      = $templateVars['store']->getId();

        if (!$this->helper->isEnabled($storeId)) {
            return false;
        }

        try {
            $type     = null;
            $entity   = null;
            $order    = null;
            $content  = null;
            $fileName = null;

            // Extract content based on document type
            if (isset($templateVars['invoice'])) {
                $invoice  = $templateVars['invoice'];
                $content  = $this->printHelper->processPDFTemplate(Type::INVOICE, $templateVars, $storeId,
                    $invoice->getOrder());
                $fileName = $this->getDocumentFileName('invoice', $invoice, $storeId);
            } elseif (isset($templateVars['shipment'])) {
                $shipment = $templateVars['shipment'];
                $content  = $this->printHelper->processPDFTemplate(Type::SHIPMENT, $templateVars, $storeId,
                    $shipment->getOrder());
                $fileName = $this->getDocumentFileName('shipment', $shipment, $storeId);
            } elseif (isset($templateVars['creditmemo'])) {
                $creditmemo = $templateVars['creditmemo'];
                $content    = $this->printHelper->processPDFTemplate(Type::CREDIT_MEMO, $templateVars, $storeId,
                    $creditmemo->getOrder());
                $fileName   = $this->getDocumentFileName('creditmemo', $creditmemo, $storeId);
            } else {
                $order    = $templateVars['order'];
                $content  = $this->printHelper->processPDFTemplate(Type::ORDER, $templateVars, $storeId, $order);
                $fileName = $this->getDocumentFileName('order', $order, $storeId);
            }

            if ($content) {
                return $this->transportBuilderPdf->addAttachment(
                    $content,
                    'application/pdf',
                    \Laminas\Mime\Mime::DISPOSITION_ATTACHMENT,
                    \Laminas\Mime\Mime::ENCODING_BASE64,
                    $fileName . '.pdf'
                );
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    private function getDocumentFileName($type, $entity, $storeId)
    {
        $configFileName = $this->helper->getFileName($type, $storeId);
        $defaultName    = ucfirst($type) . $entity->getIncrementId();

        if (!$configFileName) {
            return $defaultName;
        }

        if (strpos($configFileName, '%increment_id') !== false) {
            return str_replace('%increment_id', $entity->getIncrementId(), $configFileName);
        }

        return $configFileName;
    }

    /**
     * @param $attachPdf
     *
     * @throws MailException
     * @throws  LocalizedException
     */
    public function attachEmail($attachPdf)
    {
        $transport = $this->transportBuilder->getTransport();
        $html      = $transport->getMessage();
        $message   = \Laminas\Mail\Message::fromString($html->getRawMessage());
        $body      = $message->getBody();

        if ($this->helper->versionCompare('2.3.3')) {
            $body = quoted_printable_decode($body);
        }

        $part = new \Laminas\Mime\Part($body);
        $part->setCharset('utf-8');

        if ($this->helper->versionCompare('2.3.3')) {
            $part->setEncoding(\Laminas\Mime\Mime::ENCODING_QUOTEDPRINTABLE);
            $part->setDisposition(\Laminas\Mime\Mime::DISPOSITION_INLINE);
        }

        $part->setType(\Laminas\Mime\Mime::TYPE_HTML);
        $bodyPart = new \Laminas\Mime\Message();
        $bodyPart->setParts([$part, $attachPdf]);
        $html->setBody($bodyPart);

        $transport->sendMessage();
    }
}
