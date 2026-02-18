<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * ExportExcel action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class ExportExcel extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $fileName = 'faq.xls';

        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Konstant\Managefaq\Block\Adminhtml\Faq\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
