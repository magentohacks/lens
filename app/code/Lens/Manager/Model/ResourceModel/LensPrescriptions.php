<?php
  
namespace Lens\Manager\Model\ResourceModel;
  
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
  
class LensPrescriptions extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('lens_prescriptions', 'entity_id');
    }
}
