<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Index;

use Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\Statuses as PdfTemplateStatus;

/**
 * class MassEnable
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class MassEnable extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractAction
{
    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('pdftemplate');
        if (!is_array($templateIds) || empty($templateIds)) {
            $this->messageManager->addError(__('Please select PDF Template(s).'));
        } else {
            /** @var \Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\Collection $collection */
            $collection = $this->_objectManager->create(
                'Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\Collection'
            );

            $collection->addFieldToSelect('template_id')
                ->addFieldToFilter('template_id', ['in' => $templateIds]);

            try {
                /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $item */
                foreach ($collection as $item) {
                    $item->setData('status', PdfTemplateStatus::STATUS_ACTIVE);
                    $item->save();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($templateIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
