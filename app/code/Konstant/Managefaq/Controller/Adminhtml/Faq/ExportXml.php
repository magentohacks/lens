<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * ExportXML action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class ExportXml extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $fileName = 'faq.xml';

        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Konstant\Managefaq\Block\Adminhtml\Faq\Grid')->getXml();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
