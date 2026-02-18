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

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magestore\Pdfinvoiceplus\Model\OptionManager;

/**
 * class OrderDesignTab
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class OrderDesignTab extends AbstractDesignTab implements TabInterface
{
    /**
     * @var string
     */
    protected $_designLabel = 'order';

    /**
     * @return string
     */
    protected function _getDesignType()
    {
        return \Magestore\Pdfinvoiceplus\Model\PdfTemplate::DESIGN_TYPE_ORDER;
    }

    /**
     * @return string
     */
    public function getLoadVariablesUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/insertVariable/order');
    }

    /**
     * @return array
     */
    public function getBarcodeFilenameVariables()
    {
        return [
            'label' => __('Order'),
            'value' => $this->_optionManager->get(OptionManager::OPTION_VARIABLE_BARCODEFILENAME_ORDER)->toOptionArray(),
        ];
    }

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
            'order_design_fieldset',
            [
                'legend' => __('Order Design'),
            ]
        );

        $fieldset->addField(
            'order_filename',
            'text',
            [
                'name'     => 'order_filename',
                'label'    => __('Name to save PDF order'),
                'title'    => __('Name to save PDF order'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'insert_var_order_filename',
            'note',
            [
                'text'               => $this->_getInsertVarButtonHtml('order_filename'),
                'after_element_html' => '<br/>' . $this->getNoteFileName(),
            ]
        );

        $fieldset->addField(
            'barcode_order',
            'text',
            [
                'name'     => 'barcode_order',
                'label'    => __('Information encoded in Barcode'),
                'title'    => __('Information encoded in Barcode'),
                'required' => true,

            ]
        );

        $fieldset->addField(
            'insert_var_order_barcode',
            'note',
            [
                'text'               => $this->_getInsertVarButtonHtml('barcode_order'),
                'after_element_html' => '<br/>' . $this->getNoteBarcode(),
            ]
        );

        $fieldset->addField(
            'edit_design_order',
            'note',
            [
                'text'               => $this->getEditDesignButtonHtml() . ' ' . $this->getPreviewButtonHtml(),
                'after_element_html' => '<br/>' . $this->getNoteBrowser(),
            ]
        );

        if (!$model->getId()) {
            $model->addData([
                'order_filename' => 'order_{{var order_increment_id}}_{{var order_created_at}}',
                'barcode_order'  => '{{var order_increment_id}}',
            ]);
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
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
        return __('Order Design');
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
        return __('Order Design');
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