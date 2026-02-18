<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Plugins;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;

/**
 * class OrderAttachment
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class OrderEmailAttachment
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfInvoiceHelper;

    /**
     * OrderEmailAttachment constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig
     * @param ObjectManagerInterface                       $objectManager
     * @param \Magestore\Pdfinvoiceplus\Helper\Data        $helper
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        ObjectManagerInterface $objectManager,
        \Magestore\Pdfinvoiceplus\Helper\Data $helper
    ) {
        $this->_systemConfig = $systemConfig;
        $this->_objectManager = $objectManager;
        $this->_pdfInvoiceHelper = $helper;
    }

    /**
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param \Magento\Sales\Api\Data\OrderInterface    $result
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|Order
     */
    public function afterPlace(
        \Magento\Sales\Model\Service\OrderService $subject,
        \Magento\Sales\Api\Data\OrderInterface $result
    ) {
        if ($this->_isEnabledEmailAttachment()) {
            /** @var \Magento\Sales\Model\Order $result */
            $pdfTemplate = $this->_pdfInvoiceHelper->getCurrentPdfTemplate($result->getStoreId());

            /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter $printAdapter */
            $printAdapter = $this->_objectManager->get('Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter');
            /** @var \Magento\Framework\DataObject $printData */
            $printData = $printAdapter->printEntity($result, $pdfTemplate);

//            $attachment = new \Zend_Mime_Part($printData->getData('content'));
//            $attachment->type = 'application/pdf';
//            $attachment->disposition = \Zend_Mime::DISPOSITION_INLINE;
//            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
//            $attachment->filename = $printData->getData('filename');

            /** @var \Magento\Framework\Mail\Message $message */
//            $message = $this->_objectManager->get('Magento\Framework\Mail\MessageInterface');
//            $message->addAttachment($printData->getData('content'), "application/pdf", \Zend\Mime\Mime::DISPOSITION_INLINE, \Zend\Mime\Mime::ENCODING_BASE64, $printData->getData('filename'));
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function _isEnabledEmailAttachment()
    {
        return $this->_systemConfig->allowAttachPdfToEmail() && $this->_pdfInvoiceHelper->getCurrentPdfTemplate()->getId();
    }
}