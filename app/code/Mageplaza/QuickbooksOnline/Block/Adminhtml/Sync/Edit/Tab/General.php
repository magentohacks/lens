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
use Magento\Config\Model\Config\Source\Website;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Model\Source\Status;
use Mageplaza\QuickbooksOnline\Model\SyncFactory;

/**
 * Class General
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * @var QuickbooksModule
     */
    protected $quickbooksModule;

    /**
     * @var MagentoObject
     */
    protected $magentoObject;

    /**
     * @var Website
     */
    protected $website;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var SyncFactory
     */
    protected $syncFactory;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param QuickbooksModule $quickbooksModule
     * @param MagentoObject $magentoObject
     * @param Website $website
     * @param Status $status
     * @param SyncFactory $syncFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        QuickbooksModule $quickbooksModule,
        MagentoObject $magentoObject,
        Website $website,
        Status $status,
        SyncFactory $syncFactory,
        array $data = []
    ) {
        $this->quickbooksModule = $quickbooksModule;
        $this->magentoObject    = $magentoObject;
        $this->website          = $website;
        $this->status           = $status;
        $this->syncFactory      = $syncFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('sync');
        $sync             = $this->_coreRegistry->registry('sync_rule');
        $syncFactory      = $this->syncFactory->create()->getCollection();
        $hasPaymentMethod = $syncFactory->addFieldToFilter(
            'magento_object',
            ['eq' => MagentoObject::PAYMENT_METHOD]
        )->getSize();
        $magentoObj       = $this->magentoObject->toOptionArray();

        if ($hasPaymentMethod === 1 && !$this->getRequest()->getParam('id')) {
            unset($magentoObj[5]);
        }

        $fieldset = $form->addFieldset(
            'general',
            [
                'legend' => __('Sync Information')
            ]
        );

        if ($sync->getId()) {
            $fieldset->addField('sync_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'required' => true,
                'label'    => __('Name'),
                'title'    => __('Name'),
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name'     => 'status',
                'required' => true,
                'label'    => __('Status'),
                'title'    => __('Status'),
                'values'   => $this->status->toOptionArray()
            ]
        );

        $fieldset->addField(
            'magento_object',
            'select',
            [
                'name'     => 'magento_object',
                'title'    => __('Magento Object'),
                'label'    => __('Magento Object'),
                'required' => true,
                'values'   => $magentoObj
            ]
        );

        $fieldset->addField(
            'quickbooks_module',
            'select',
            [
                'name'     => 'quickbooks_module',
                'title'    => __('Quickbooks Module'),
                'label'    => __('Quickbooks Module'),
                'required' => true,
                'values'   => $this->quickbooksModule->toOptionArray()
            ]
        );

        $fieldset->addField(
            'website_ids',
            'multiselect',
            [
                'name'     => 'website_ids',
                'title'    => __('Website'),
                'label'    => __('Website'),
                'required' => true,
                'values'   => $this->website->toOptionArray()
            ]
        );

        $fieldset->addField(
            'priority',
            'text',
            [
                'name'  => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'class' => 'validate-number validate-zero-or-greater',
                'note'  => __('If several rules meet the condition, the one with the highest priority will be applied. 
                Smaller number means higher priority.')
            ]
        );

        $form->setValues($sync->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Sync Information');
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
        return false;
    }
}
