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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magestore\Pdfinvoiceplus\Model\OptionManager;

/**
 * General Tab.
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class GeneralTab extends AbstractTab implements TabInterface
{


    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General Information'),
            ]
        );

        if ($model->getId()) {
            $fieldset->addField('template_id', 'hidden', ['name' => 'template_id']);
        }

        $fieldset->addField('template_type_id', 'hidden', ['name' => 'template_type_id']);
        $fieldset->addField('flag_change_design', 'hidden', ['name' => 'flag_change_design']);

        $fieldset->addField(
            'template_name',
            'text',
            [
                'name'     => 'template_name',
                'label'    => __('Template Name'),
                'title'    => __('Template Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'format',
            'select',
            [
                'name'     => 'format',
                'label'    => __('Paper Size'),
                'title'    => __('Paper Size'),
                'required' => true,
                'values'   => $this->_optionManager->get(OptionManager::OPTION_PAGE_SIZES)->toOptionArray(),
            ]
        );

        $this->processElementDisableable(
            $fieldset->addField(
                'localization',
                'select',
                [
                    'name'     => 'localization',
                    'label'    => __('Language'),
                    'title'    => __('Language'),
                    'required' => true,
                    'values'   => $this->_optionManager->get(OptionManager::OPTION_LANGUANGE)->toOptionArray(),
                ]
            )
        );

        $fieldset->addField(
            'btn_select_design',
            'note',
            [
                'text'               => $this->_buttonBuilder->build(
                    __('Select Design'),
                    [
                        'class' => 'action-default scalable primary btn-select-design',
                        'type'  => 'button',
                    ]
                ),
                'after_element_html' => '<br/>' . __('To choose and preview a template design </br> that you will use.'),
            ]
        );

        if ($this->_systemConfig->isUseForMultiStore()) {
            if (!$this->_storeManager->isSingleStoreMode()) {
                $fieldset->addField(
                    'stores',
                    'multiselect', [
                    'name'     => 'stores[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                ]);
            } else {
                $defaultStore = $this->_storeManager->getStore(true)->getId();
                $fieldset->addField(
                    'stores',
                    'hidden',
                    [
                        'name'  => 'stores[]',
                        'value' => $defaultStore,
                    ]
                );
                $model->setStores($defaultStore);
            }
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'name'     => 'status',
                'label'    => __('Status'),
                'title'    => __('Status'),
                'required' => true,
                'values'   => $this->_optionManager->get(OptionManager::OPTION_STATUSES)->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'barcode',
            'select',
            [
                'name'               => 'barcode',
                'label'              => __('Show Barcode'),
                'title'              => __('Show Barcode'),
                'required'           => true,
                'values'             => $this->_optionManager->get(OptionManager::OPTION_SHOW_BARCODE)->toOptionArray(),
                'after_element_html' => $this->_getToolTipHtml(
                    $this->getViewFileUrl('Magestore_Pdfinvoiceplus::images/tooltip/barcode.png'),
                    __('View example')
                ),
            ]
        );

        $fieldset->addField(
            'barcode_type',
            'select',
            [
                'name'     => 'barcode_type',
                'label'    => __('Barcode Type'),
                'title'    => __('Barcode Type'),
                'required' => true,
                'values'   => $this->_optionManager->get(OptionManager::OPTION_BARCODE_TYPE)->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'orientation',
            'select',
            [
                'name'     => 'orientation',
                'label'    => __('Orientation'),
                'title'    => __('Orientation'),
                'required' => true,
                'values'   => $this->_optionManager->get(OptionManager::OPTION_PAGE_ORIENTATION)->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'select_design',
            'note',
            [
                'text' => $this->_getSelectDesignHtml(),
            ]
        );

        if (is_array($model->getData('company_logo'))) {
            $model->setData('company_logo', $model->getData('company_logo/value'));
        }

        if (!$model->getId()) {
            $model->setData('localization', 'en_US');
            $model->setData('barcode', \Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\ShowBarcode::STATUS_NO);
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getMappingFieldDependence()
    {
        return [
            [
                'fieldName'     => ['barcode_type'],
                'fieldNameFrom' => 'barcode',
                'refField'      => '1,',
            ],
        ];
    }

    /**
     * get dependency field.
     *
     * @return \Magento\Config\Model\Config\Structure\Element\Dependency\Field [description]
     */
    public function getDependencyField($refField, $negative = false, $separator = ',', $fieldPrefix = '')
    {
        return $this->_fieldFactory->create(
            ['fieldData'   => ['value' => (string)$refField, 'negative' => $negative, 'separator' => $separator],
             'fieldPrefix' => $fieldPrefix,
            ]
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getSelectDesignHtml()
    {
        return $this->getLayout()
            ->createBlock('Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab\GeneralTab\SelectDesign', '',
                [])
            ->toHtml();
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('General Information');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
