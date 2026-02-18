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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * abstract class Design
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class Design extends AbstractAction
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\ImageUploaderFactory
     */
    protected $_imageUploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\MainVariablesManager
     */
    protected $_mainVariablesManager;

    /**
     * AbstractAction constructor.
     *
     * @param Context $context
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magestore\Pdfinvoiceplus\Model\ImageUploaderFactory $imageUploaderFactory,
        \Magestore\Pdfinvoiceplus\Model\MainVariablesManager $mainVariablesManager
    ) {
        parent::__construct($context);
        $this->_mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_imageUploaderFactory = $imageUploaderFactory;
        $this->_mainVariablesManager = $mainVariablesManager;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplate
     */
    protected function _getPdfTemplateModel()
    {
        $id = $this->getRequest()->getParam(self::PARAM_CRUD_ID);
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplate');
        $model->load($id);

        return $model;
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Pdfinvoiceplus::managetemplate');
    }
}