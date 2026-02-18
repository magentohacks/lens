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
 * class CreditMemoDesignTab
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class CreditMemoDesignTab extends AbstractDesignTab implements TabInterface
{
    protected $_designLabel = 'creditmemo';

    /**
     * @return string
     */
    protected function _getDesignType()
    {
        return \Magestore\Pdfinvoiceplus\Model\PdfTemplate::DESIGN_TYPE_CREDITMEMO;
    }

    /**
     * @return string
     */
    public function getLoadVariablesUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/insertVariable/creditmemo');
    }

    /**
     * @return array
     */
    public function getBarcodeFilenameVariables()
    {
        return [
            'label' => __('Creditmemo'),
            'value' => $this->_optionManager->get(OptionManager::OPTION_VARIABLE_BARCODEFILENAME_CREDITMEMO)->toOptionArray(),
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
            'creditmemo_design_fieldset',
            [
                'legend' => __('Credit Memo Design'),
            ]
        );

        $fieldset->addField(
            'creditmemo_filename',
            'text',
            [
                'name'     => 'creditmemo_filename',
                'label'    => __('Name to save PDF credit memo'),
                'title'    => __('Name to save PDF credit memo'),
                'required' => true,

            ]
        );

        $fieldset->addField(
            'insert_var_creditmemo_filename',
            'note',
            [
                'text'               => $this->_getInsertVarButtonHtml('creditmemo_filename'),
                'after_element_html' => '<br/>' . $this->getNoteFileName(),
            ]
        );


        $fieldset->addField(
            'barcode_creditmemo',
            'text',
            [
                'name'     => 'barcode_creditmemo',
                'label'    => __('Information encoded in Barcode'),
                'title'    => __('Information encoded in Barcode'),
                'required' => true,

            ]
        );

        $fieldset->addField(
            'insert_var_creditmemo_barcode',
            'note',
            [
                'text'               => $this->_getInsertVarButtonHtml('barcode_creditmemo'),
                'after_element_html' => '<br/>' . $this->getNoteBarcode(),
            ]
        );

        $fieldset->addField(
            'edit_design_creditmemo',
            'note',
            [
                'text'               => $this->getEditDesignButtonHtml() . ' ' . $this->getPreviewButtonHtml(),
                'after_element_html' => '<br/>' . $this->getNoteBrowser(),
            ]
        );

        if (!$model->getId()) {
            $model->addData([
                'creditmemo_filename' => 'creditmemo_{{var creditmemo_increment_id}}',
                'barcode_creditmemo'  => '{{var creditmemo_increment_id}}',
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
        return __('Credit Memo Design');
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
        return __('Credit Memo Design');
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