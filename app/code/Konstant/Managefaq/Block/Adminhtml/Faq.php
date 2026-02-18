<?php
namespace Konstant\Managefaq\Block\Adminhtml;

/**
 * FAQ grid container.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Faq extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_faq';
        $this->_blockGroup = 'Konstant_Managefaq';
        $this->_headerText = __('FAQ');
        $this->_addButtonLabel = __('Add New FAQ');
        parent::_construct();
    }
}
