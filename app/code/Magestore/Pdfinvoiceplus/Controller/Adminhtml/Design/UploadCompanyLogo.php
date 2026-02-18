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
use Magestore\Pdfinvoiceplus\Model\PdfTemplate;
use Magento\Framework\Controller\ResultFactory;

/**
 * class UploadCompanyLogo
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class UploadCompanyLogo extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (isset($_FILES['insert-logo']['name']) && $_FILES['insert-logo']['name'] != '') {
            try {
                /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                $uploader = $this->_imageUploaderFactory->create(['fileId' => 'insert-logo']);
                $uploader->save($this->_mediaDirectory->getAbsolutePath(PdfTemplate::IMAGE_BACKGROUND_PATH));

                $result = [
                    'url' => $this->_imageHelper->getMediaUrlImage(
                        PdfTemplate::IMAGE_BACKGROUND_PATH . $uploader->getUploadedFileName()
                    ),
                ];

            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
                $this->_logger->critical(__($e->getMessage()));
            }

            /** @var \Magento\Framework\Controller\Result\Raw $response */
            $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $response->setHeader('Content-type', 'text/plain');
            $response->setContents(json_encode($result));

            return $response;
        }
    }
}