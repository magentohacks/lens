<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 10/3/2016
 * Time: 10:59 AM
 */

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magestore\Pdfinvoiceplus\Model\OptionManager;

class ShipmentDesignTab extends AbstractDesignTab implements TabInterface
{
    /**
     * @var string
     */
    protected $_designLabel = 'shipment';

    /**
     * @return string
     */
    protected function _getDesignType()
    {
        return \Magestore\Pdfinvoiceplus\Model\PdfTemplate::DESIGN_TYPE_SHIPMENT;
    }

    /**
     * @return mixed
     */
    public function getLoadVariablesUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/insertVariable/shipment');
    }

    /**
     * @return array
     */
    public function getBarcodeFilenameVariables()
    {
        return [
            'label' => __('Shipment'),
            'value' => $this->_optionManager->get(OptionManager::OPTION_VARIABLE_BARCODEFILENAME_SHIPMENT)->toOptionArray(),
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
            'shipment_design_fieldset',
            [
                'legend' => __('Shipment Design'),
            ]
        );

        $fieldset->addField(
            'shipment_filename',
            'text',
            [
                'name'     => 'shipment_filename',
                'label'    => __('Name to save PDF shipment'),
                'title'    => __('Name to save PDF shipment'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'insert_var_shipment_filename',
            'note',
            [
                'text'               => $this->_getInsertVarButtonHtml('shipment_filename'),
                'after_element_html' => '<br/>' . $this->getNoteFileName(),
            ]
        );

        $fieldset->addField(
            'barcode_shipment',
            'text',
            [
                'name'     => 'barcode_shipment',
                'label'    => __('Information encoded in Barcode'),
                'title'    => __('Information encoded in Barcode'),
                'required' => true,

            ]
        );

        $fieldset->addField(
            'insert_var_shipment_barcode',
            'note',
            [
                'text'               => $this->_getInsertVarButtonHtml('barcode_shipment'),
                'after_element_html' => '<br/>' . $this->getNoteBarcode(),
            ]
        );

        $fieldset->addField(
            'edit_design_shipment',
            'note',
            [
                'text'               => $this->getEditDesignButtonHtml() . ' ' . $this->getPreviewButtonHtml(),
                'after_element_html' => '<br/>' . $this->getNoteBrowser(),
            ]
        );

        if (!$model->getId()) {
            $model->addData([
                'shipment_filename' => 'shipment_{{var shipment_increment_id}}_{{var shipment_created_at}}',
                'barcode_shipment'  => '{{var shipment_increment_id}}',
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
        return __('Shipment Design');
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
        return __('Shipment Design');
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