<?php
  
namespace Lens\Manager\Model\ResourceModel\LensPrescriptions;
  
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
  
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Lens\Manager\Model\LensPrescriptions',
            'Lens\Manager\Model\ResourceModel\LensPrescriptions'
        );
    }
}