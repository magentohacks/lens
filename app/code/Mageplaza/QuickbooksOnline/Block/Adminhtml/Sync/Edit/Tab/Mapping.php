<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mageplaza\QuickbooksOnline\Helper\Mapping as HelperMapping;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class Mapping
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab
 */
class Mapping extends Generic implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_QuickbooksOnline::widget/form.phtml';

    /**
     * @var HelperMapping
     */
    protected $helperMapping;

    /**
     * Mapping constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelperMapping $helperMapping
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperMapping $helperMapping,
        array $data = []
    ) {
        $this->helperMapping = $helperMapping;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Mapping Fields');
    }

    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        $type = $this->getSyncRule()->getQuickbooksModule();

        return ($type && $type === QuickbooksModule::PAYMENT_METHOD);
    }

    /**
     * @return string
     */
    public function getMappingFields()
    {
        $sync = $this->getSyncRule();
        if ($sync->getId()) {
            return $this->helperMapping->getMappingFieldsByRule($sync);
        }

        return '';
    }

    /**
     * @return string
     */
    public function isEdit()
    {
        $sync = $this->getSyncRule();

        return $sync->getId() ?: '0';
    }

    /**
     * @return mixed
     */
    public function getSyncRule()
    {
        return $this->_coreRegistry->registry('sync_rule');
    }

    /**
     * @return string
     */
    public function getMappingUrl()
    {
        return $this->getUrl('mpquickbooks/sync/mapping');
    }

    /**
     * @return string
     */
    public function getReindexPaymentUrl()
    {
        return $this->getUrl('mpquickbooks/sync/reindexPayment', ['id' => $this->getSyncRule()->getId()]);
    }

    /**
     * @return string
     */
    public function getMappingObject()
    {
        return HelperMapping::jsonEncode($this->helperMapping->getMappingObject());
    }

    /**
     * @return array|string
     */
    public function getVariables()
    {
        $variables = '{}';
        $sync      = $this->getSyncRule();

        if ($sync->getId()) {
            $variables = $this->helperMapping->getDefaultVariable($sync->getQuickbooksModule(), true);
        }

        return $variables;
    }
}
