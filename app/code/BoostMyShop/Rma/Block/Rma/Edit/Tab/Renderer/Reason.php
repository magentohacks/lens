<?php

namespace BoostMyShop\Rma\Block\Rma\Edit\Tab\Renderer;

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Reason extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;
    protected $_imageHelper;
    protected $_registry;
    protected $_config;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,
                                \Magento\Catalog\Helper\Image $imageHelper,
                                StoreManagerInterface $storemanager,
                                \Magento\Framework\Registry $registry,
                                \BoostMyShop\Rma\Model\Config $config,
                                array $data = [])
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
        $this->_imageHelper = $imageHelper;
        $this->_registry = $registry;
        $this->_config = $config;
    }

    public function render(DataObject $row)
    {
        $name = 'items['.$row->getId().'][ri_reason]';
        $html = '<select name="'.$name.'">';
        foreach($this->_config->getReasons() as $k => $v)
        {
            $selected = ($k == $row->getri_reason() ? 'selected="selected"' : '');
            $html .= '<option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        $html .= '</select>';
        return $html;
    }
}