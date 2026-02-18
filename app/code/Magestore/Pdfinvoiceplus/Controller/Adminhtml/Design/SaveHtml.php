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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Pdfinvoiceplus\Model\PdfTemplate;

/**
 * class SaveHtml
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class SaveHtml extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $designType = $this->getRequest()->getParam('design_type');
        $html = $this->getRequest()->getParam('html');

        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->_getPdfTemplateModel();

        if (!$model->getId()) {
            return $resultJson->setData([
                'error'   => true,
                'message' => __('This PDF Template no longer exists.'),
            ]);
        }

        $model->setData($designType . '_html', $html);

        try {
            $model->save();

            return $resultJson->setData([
                'error'   => false,
                'message' => __('Save success'),
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'error'   => true,
                'message' => __('Something went wrong while saving the PDF Tempalte.'),
            ]);
        }
    }
}