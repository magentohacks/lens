<?php

namespace BoostMyShop\Rma\Block\Rma;


class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'rma_id';
        $this->_controller = 'Rma';
        $this->_blockGroup = 'BoostMyShop_Rma';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save RMA'));


        $this->buttonList->add('print', [
            'id' => 'print',
            'label' => __('Print'),
            'class' => 'print',
            'onclick' => 'setLocation(\''.$this->getPrintUrl().'\')'
        ]);

        $this->buttonList->add('notify', [
            'id' => 'notify',
            'label' => __('Send email'),
            'onclick' => 'setLocation(\''.$this->getNotifyUrl().'\')'
        ]);

        if ($this->getRma()->getrma_status() != \BoostMyShop\Rma\Model\Rma\Status::complete) {
            $this->buttonList->add('process', [
                'id' => 'process',
                'label' => __('Process'),
                'class' => 'process',
                'onclick' => 'setLocation(\'' . $this->getProcessUrl() . '\')'
            ]);
        }

    }

    public function getRma()
    {
        return $this->_coreRegistry->registry('current_rma');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_rma')->getId()) {
            return __("Edit RMA '%1'", $this->_coreRegistry->registry('current_rma')->getrma_reference());
        } else {
            return __('New RMA');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        //return $this->getUrl('adminhtml/*/validate', ['_current' => true]);
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', ['rma_id' => $this->getRma()->getId()]);
    }

    public function getProcessUrl()
    {
        return $this->getUrl('*/*/process', ['rma_id' => $this->getRma()->getId()]);
    }

    public function getNotifyUrl()
    {
        return $this->getUrl('*/*/notify', ['rma_id' => $this->getRma()->getId()]);
    }
}
