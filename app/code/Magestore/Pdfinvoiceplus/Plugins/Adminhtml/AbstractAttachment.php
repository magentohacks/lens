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

use Magento\Framework\ObjectManagerInterface;

/**
 * class AbstractAttachment
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractAttachment
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
     * AbstractAttachment constructor.
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
     * @param $entityId
     *
     * @return \Magento\Framework\DataObject
     */
    public function getPrintData($entityId)
    {
        $renderingEntity = $this->getRenderingEntity($entityId);
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate */
        $pdfTemplate = $this->_pdfInvoiceHelper->getCurrentPdfTemplate($renderingEntity->getStoreId());

        /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter $printAdapter */
        $printAdapter = $this->_objectManager->get('Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter');

        return $printAdapter->printEntity($renderingEntity, $pdfTemplate);
    }

    /**
     * @return bool
     */
    public function isEnabledEmailAttachment()
    {
        return $this->_pdfInvoiceHelper->getCurrentPdfTemplate()->getId();
    }

    /**
     * @return mixed
     */
    public abstract function getRenderingEntity($entityId);
}