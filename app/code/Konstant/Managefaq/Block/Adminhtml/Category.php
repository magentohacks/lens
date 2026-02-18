<?php
namespace Konstant\Managefaq\Block\Adminhtml;

/**
 * Category grid container.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Category extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Konstant_Managefaq';
        $this->_headerText = __('FAQ Categories');
        $this->_addButtonLabel = __('Add New Category');
        parent::_construct();
    }
}
