<?php
namespace BoostMyShop\Rma\Block\Rma\Edit\Tab;

class Messages extends \Magento\Backend\Block\Template
{
    protected $_template = 'Rma/Edit/Tabs/Messages.phtml';

    protected $_rma;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->_coreRegistry = $registry;

    }

    public function getRma()
    {
        if (!$this->_rma)
            $this->_rma = $this->_coreRegistry->registry('current_rma');
        return $this->_rma;
    }


}