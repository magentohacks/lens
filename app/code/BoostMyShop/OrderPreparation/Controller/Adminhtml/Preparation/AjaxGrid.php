<?php

namespace BoostMyShop\OrderPreparation\Controller\Adminhtml\Preparation;

use Magento\Framework\Controller\ResultFactory;

class AjaxGrid extends \BoostMyShop\OrderPreparation\Controller\Adminhtml\Preparation
{

    /**
     * @return void
     */
    public function execute()
    {
        $grid = $this->getRequest()->getParam('grid');

        $blockClass = null;
        switch($grid)
        {
            case 'selected':
                $blockClass = 'BoostMyShop\OrderPreparation\Block\Preparation\InProgress';
                break;
            case 'backorder':
                $blockClass = 'BoostMyShop\OrderPreparation\Block\Preparation\Tab\BackOrder';
                break;
            case 'holded':
                $blockClass = 'BoostMyShop\OrderPreparation\Block\Preparation\Tab\Holded';
                break;
            case 'instock':
                $blockClass = 'BoostMyShop\OrderPreparation\Block\Preparation\Tab\InStock';
                break;
        }

        $layout = $this->_layoutFactory->create();
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents($layout->createBlock($blockClass)->toHtml());

        return $resultRaw;
    }
}
