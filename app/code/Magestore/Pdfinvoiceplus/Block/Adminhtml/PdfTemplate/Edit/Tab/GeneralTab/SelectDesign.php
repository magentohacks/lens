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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab\GeneralTab;

/**
 * class SelectDesign
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class SelectDesign extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magestore_Pdfinvoiceplus::form/select-design.phtml';

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\ResourceModel\TemplateType\CollectionFactory
     */
    protected $_templateTypeCollectionFactory;

    /**
     * SelectDesign constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                      $context
     * @param \Magestore\Pdfinvoiceplus\Model\ResourceModel\TemplateType\CollectionFactory $templateTypeCollectionFactory
     * @param array                                                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Pdfinvoiceplus\Model\ResourceModel\TemplateType\CollectionFactory $templateTypeCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_templateTypeCollectionFactory = $templateTypeCollectionFactory;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\ResourceModel\TemplateType\Collection
     */
    public function getSystemTemplateCollection()
    {
        return $this->_templateTypeCollectionFactory->create();
    }
}