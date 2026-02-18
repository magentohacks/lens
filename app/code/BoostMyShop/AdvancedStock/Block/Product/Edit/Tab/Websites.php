<?php
namespace BoostMyShop\AdvancedStock\Block\Product\Edit\Tab;

class Websites extends AbstractTab
{
    protected $_template = 'Product/Edit/Tab/Websites.phtml';

    public function getWebsites()
    {
        return $this->_stockItemCollectionFactory->create()->addProductFilter($this->getProduct()->getId())->joinWebsite();
    }


}