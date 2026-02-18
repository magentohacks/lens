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

use Magestore\Pdfinvoiceplus\Model\OptionManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * abstract class InsertVariable
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class InsertVariable extends AbstractAction
{
    /**
     * @return array
     */
    abstract public function getBarcodeFilenameVariables();

    /**
     * @return array
     */
    public function getVariablesOption()
    {
        $variablesOption = [$this->getBarcodeFilenameVariables()];
        $customerVariables = $this->_optionManager->get(OptionManager::OPTION_VARIABLE_CONFIG_CUSTOMER)->toOptionArray();

        if (!empty($customerVariables)) {
            $variablesOption[] = [
                'label' => __('Customer'),
                'value' => $customerVariables,
            ];
        }

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