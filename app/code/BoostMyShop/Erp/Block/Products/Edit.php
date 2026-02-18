<?php

namespace BoostMyShop\Erp\Block\Products;


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
        $this->_objectId = 'entity_id';
        $this->_controller = 'Products';
        $this->_blockGroup = 'BoostMyShop_Erp';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));

        $this->buttonList->add('switch_to_magento_view', [
            'id' => 'switch_to_magento_view',
            'label' => __('Switch to Magento view'),
            'class' => '',
            'onclick' => 'setLocation(\''.$this->getMagentoViewUrl().'\')'
        ]);



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

    public function getMagentoViewUrl()
    {
        return $this->getUrl('catalog/product/edit', ['id' => $this->_coreRegistry->registry('current_product')->getId()]);
    }

}
