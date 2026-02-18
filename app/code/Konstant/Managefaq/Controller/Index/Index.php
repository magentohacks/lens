<?php
namespace Konstant\Managefaq\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Konstant\Managefaq\Model\FaqFactory;

/**
 * Index action
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Index extends \Magento\Framework\App\Action\Action
{	
	protected $_modelFaqFactory;
	
	public function __construct(
		Context $context,
		FaqFactory $modelFaqFactory
	) {
		parent::__construct($context);
		$this->_modelFaqFactory = $modelFaqFactory;
    }
	
	public function execute()
    {
		$this->_view->loadLayout();
        $this->_view->renderLayout();
	}
}
