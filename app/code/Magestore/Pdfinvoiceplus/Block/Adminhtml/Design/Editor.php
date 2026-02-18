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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\Design;

/**
 * class Editor
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Editor extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magestore_Pdfinvoiceplus::editor.phtml';

    protected $_typeMap = [
        'head'    => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor\Head',
        'content' => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor\Content',
        'menu'    => 'Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor\Menu',
    ];


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }


    /**
     * @param $type
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTypeBlockHtml($type)
    {
        if (array_key_exists($type, $this->_typeMap)) {
            return $this->getLayout()->createBlock($this->_typeMap[$type])->toHtml();
        }

        return '';
    }
}