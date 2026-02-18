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
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\QuickbooksOnline\Model\QueueFactory;
use Mageplaza\QuickbooksOnline\Model\Sync;
use Mageplaza\QuickbooksOnline\Model\SyncFactory;

/**
 * Class AbstractSync
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml
 */
abstract class AbstractSync extends Action
{
    const ADMIN_RESOURCE = 'Mageplaza_QuickbooksOnline::sync_rules';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var SyncFactory
     */
    protected $syncFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * AbstractSync constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param SyncFactory $syncFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        SyncFactory $syncFactory,
        QueueFactory $queueFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry          = $registry;
        $this->filter            = $filter;
        $this->syncFactory       = $syncFactory;
        $this->queueFactory      = $queueFactory;
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->addBreadcrumb(__('Manage Sync Rules'), __('Manage Sync Rules'));

        return $resultPage;
    }

    /**
     * @return Sync|null
     */
    protected function _initSync()
    {
        $syncId    = $this->getRequest()->getParam('id');
        $syncModel = $this->syncFactory->create();

        if ($syncId) {
            $syncModel->load($syncId);

            if (!$syncModel->getId()) {
                $this->messageManager->addErrorMessage(__('This item does not exists.'));

                return null;
            }
        }

        $this->registry->register('sync_rule', $syncModel);

        return $syncModel;
    }
}
