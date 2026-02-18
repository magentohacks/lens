<?php
  
namespace Lens\Manager\Model;
  
use Magento\Framework\Model\AbstractModel;
  
class LensPrescriptions extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Lens\Manager\Model\ResourceModel\LensPrescriptions');
    }
}