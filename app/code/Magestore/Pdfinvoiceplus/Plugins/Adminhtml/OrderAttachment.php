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

namespace Magestore\Pdfinvoiceplus\Plugins\Adminhtml;

/**
 * class OrderAttachment
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class OrderAttachment extends AbstractAttachment
{
    /**
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param \Closure                                  $proceed
     * @param                                           $id
     *
     * @return mixed
     */
    public function aroundNotify(
        \Magento\Sales\Model\Service\OrderService $subject,
        \Closure $proceed,
        $id
    ) {
        if ($this->isEnabledEmailAttachment()) {
            /** @var \Magento\Framework\DataObject $printData */
            $printData = $this->getPrintData($id);
            // attachment
//            $attachment = new \Zend_Mime_Part($printData->getData('content'));
//            $attachment->type = 'application/pdf';
//            $attachment->disposition = \Zend_Mime::DISPOSITION_INLINE;
//            $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
//            $attachment->filename = $printData->getData('filename');

            /** @var \Magento\Framework\Mail\MessageInterface $message */
//            $message = $this->_objectManager->get('Magento\Framework\Mail\MessageInterface');
//            $message->addAttachment($printData->getData('content'), "application/pdf", \Zend\Mime\Mime::DISPOSITION_INLINE, \Zend\Mime\Mime::ENCODING_BASE64, $printData->getData('filename'));
        }

        return $proceed($id);
    }

    /**
     * @return mixed
     */
    public function getRenderingEntity($entityId)
    {
        return $this->_objectManager->create('Magento\Sales\Model\Order')->load($entityId);
    }
}