<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * Category ExportExcel action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportExcel extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $fileName = 'category.xls';

        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Konstant\Managefaq\Block\Adminhtml\Category\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
