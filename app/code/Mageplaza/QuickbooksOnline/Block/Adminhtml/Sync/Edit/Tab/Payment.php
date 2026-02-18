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
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mageplaza\QuickbooksOnline\Block\Adminhtml\Render\Payment as RenderPayment;
use Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid\Payment as PaymentGrid;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Model\Sync;

/**
 * Class Payment
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab
 */
class Payment extends Generic implements TabInterface
{
    /**
     * @var Sync
     */
    protected $_syncRule;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Payment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperData $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->addTabToForm();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Payment Method List')
            ]
        );

        $fieldset->addField(
            'mpqb_custom_button',
            'hidden',
            [
                'after_element_html' => $this->_addActionButtonHtml()
            ]
        );

        $form->addField(
            'queues',
            RenderPayment::class,
            [
                'queue_data' => $this->getLayout()->createBlock(PaymentGrid::class)->toHtml()
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Form
     * @throws LocalizedException
     */
    protected function addTabToForm()
    {
        /**
         * @var Form $form
         */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('payment');

        return $form;
    }

    /**
     * @return string
     */
    protected function _addActionButtonHtml()
    {
        return '<button type="button" class="action-default primary" id="btn-check-update">'
            . __('Reindex Payment') . '</button>';
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Payment Method List');
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
        $type = $this->getSyncRule()->getQuickbooksModule();

        return ($type && $type === QuickbooksModule::PAYMENT_METHOD);
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getAllPaymentMethod()
    {
        return $this->helperData->getPaymentMethods();
    }

    /**
     * @return mixed
     */
    public function getSyncRule()
    {
        return $this->_coreRegistry->registry('sync_rule');
    }

    /**
     * @return array|mixed
     */
    public function getPaymentAdded()
    {
        if ($this->getRequest()->getParam('id')) {
            return HelperData::jsonDecode($this->getSyncRule()->getMapping());
        }

        return [];
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
