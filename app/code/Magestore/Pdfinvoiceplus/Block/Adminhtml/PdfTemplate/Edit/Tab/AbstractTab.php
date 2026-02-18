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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * abstract class AbstractTab
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractTab extends Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\OptionManager
     */
    protected $_optionManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Block\Widget\ButtonBuilder
     */
    protected $_buttonBuilder;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    protected $_fieldFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magestore\Pdfinvoiceplus\Model\OptionManager $optionManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\System\Store $systemStore,
        \Magestore\Pdfinvoiceplus\Block\Widget\ButtonBuilder $buttonBuilder,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $objectManager;
        $this->_systemStore = $systemStore;
        $this->_optionManager = $optionManager;
        $this->_buttonBuilder = $buttonBuilder;
        $this->_systemConfig = $systemConfig;
        $this->_fieldFactory = $fieldFactory;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function processElementDisableable(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getRequest()->getParam('template_id')) {
            $element->addData([
                'disabled' => true,
                'class'    => 'element-disabled',
            ]);
        }

        return $element;
    }

    /**
     * get registry model.
     *
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplate | null
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('pdftemplate_model');
    }

    /**
     * @param string $src
     * @param string $alt
     *
     * @return string
     */
    protected function _getImgHtml($src = '', $alt = '')
    {
        return sprintf('<img src="%s" alt="%s">', $src, $alt);
    }

    /**
     * @param string $imgSrc
     * @param string $label
     *
     * @return string
     */
    protected function _getToolTipHtml($imgSrc = '', $label = '')
    {
        $dataMageInit = $this->escapeQuote(\Laminas\Json\Json::encode([
            'magestoreToolTip' => [
                'content' => $this->_getImgHtml($imgSrc, $label),
            ],
        ]));

        return sprintf(
            '<a class="mage-tooltip" data-mage-init=\'%s\' href="JavaScript:void(0);">%s</a>',
            $dataMageInit,
            $label
        );
    }
}