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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * abstract class AbstractExportAction
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractExportAction extends AbstractAction
{
    protected $_exportFileName = 'export.txt';

    /**
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        return $this->_fileFactory->create(
            $this->_exportFileName,
            $this->_getContent(),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * content to export file.
     *
     * @return string
     */
    abstract protected function _getContent();

    /**
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    protected function _getGridExportBlock()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage->getLayout()
            ->getChildBlock('pdfinvoiceplus.pdftemplate.grid', 'grid.export');
    }
}