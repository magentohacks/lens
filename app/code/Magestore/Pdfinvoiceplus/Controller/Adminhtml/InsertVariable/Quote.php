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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\InsertVariable;

use Magestore\Pdfinvoiceplus\Model\OptionManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * class Quote
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Quote extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractAction
{

    /**
     * @return array
     */
    public function getBarcodeFilenameVariables()
    {
        return [
            'label' => __('Quote'),
            'value' => $this->_optionManager->get(OptionManager::OPTION_VARIABLE_BARCODEFILENAME_QUOTE)->toOptionArray()
        ];

    }

    public function getVariablesOption()
    {
        $variablesOption = [$this->getBarcodeFilenameVariables()];

        return $variablesOption;
    }

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

        return $resultJson->setData($this->getVariablesOption());
    }
}