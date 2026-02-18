<?php

/**
 * Magestore.
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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate;

/**
 * class Grid
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * Grid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context      $context
     * @param \Magento\Backend\Helper\Data                 $backendHelper
     * @param \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig
     * @param array                                        $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_systemConfig = $systemConfig;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->_systemConfig->isUseForMultiStore()) {
            $this->getChildBlock('grid.columnSet')->unsetChild('stores');
        }

        return parent::_prepareLayout();
    }
}