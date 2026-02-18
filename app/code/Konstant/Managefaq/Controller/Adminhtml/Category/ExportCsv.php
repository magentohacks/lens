<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * Category ExportCsv action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCsv extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $fileName = 'category.csv';

        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Konstant\Managefaq\Block\Adminhtml\Category\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
