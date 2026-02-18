<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\LayoutFactory;
use Mageplaza\QuickbooksOnline\Helper\Mapping as HelperMapping;

/**
 * Class Mapping
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync
 */
class Mapping extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var HelperMapping
     */
    protected $helperMapping;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Mapping constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param HelperMapping $helperMapping
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        HelperMapping $helperMapping,
        LayoutFactory $layoutFactory
    ) {
        $this->resultJsonFactory = $jsonFactory;
        $this->helperMapping     = $helperMapping;
        $this->layoutFactory     = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result['canMapping'] = true;

        try {
            $quickbooksModule = $this->getRequest()->getParam('quickbooks_module');
            $magentoObject    = $this->getRequest()->getParam('magento_object');
            $this->_session->unsMpQuickbooksMagentoObject();
            $this->_session->setMpQuickbooksMagentoObject($magentoObject);

            $html                   = $this->helperMapping->createMappingFields($quickbooksModule);
            $result['mapping_html'] = $html;
            $result['module']       = $quickbooksModule;
            $result['variables']    = $this->helperMapping->getDefaultVariable($quickbooksModule, true);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /**
         * @var Json $resultJson
         */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($result);
    }
}
