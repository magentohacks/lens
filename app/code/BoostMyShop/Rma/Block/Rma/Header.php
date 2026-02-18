<?php
namespace BoostMyShop\Rma\Block\Rma;

class Header extends \Magento\Backend\Block\Template
{
    protected $_template = 'Rma/Header.phtml';

    public function getCreateFromOrderUrl()
    {
        return $this->getUrl('*/*/selectOrder');
    }
}