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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder;

/**
 * class Invoice
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Invoice extends \Magestore\Pdfinvoiceplus\Block\Adminhtml\AbstractTemplateBuilder
{
    /**
     * @var string
     */
    protected $_builderType = 'invoice';

    /**
     * @var string
     */
    protected $_tableItemsTemplate = 'Magestore_Pdfinvoiceplus::default-template/table-items/invoice.phtml';


    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->getPdfTemplateObject()->getData('barcode_invoice');
    }

    /**
     * @return string
     */
    public function getBindedStatus()
    {
        return $this->bindVariableName('state');
    }

    /**
     * @param $entityType
     */
    public function getDefaultTemplateLoaderPath()
    {
        return sprintf(
            "Magestore_Pdfinvoiceplus::default-template/%s/template-loader.phtml",
            $this->getPdfTemplateObject()->getData('template_code')
        );
    }
}