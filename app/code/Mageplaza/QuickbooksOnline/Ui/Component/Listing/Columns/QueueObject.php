<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\QuickbooksOnline\Model\Queue;
use Mageplaza\QuickbooksOnline\Model\QueueFactory;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QueueActions;

/**
 * Class QueueObject
 * @package Mageplaza\QuickbooksOnline\Ui\Component\Listing\Columns
 */
class QueueObject extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * QueueObject constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param QueueFactory $queueFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        QueueFactory $queueFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder   = $urlBuilder;
        $this->queueFactory = $queueFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        /**
         * @var Queue $queueModel
         */
        $queueModel = $this->queueFactory->create();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ((int) $item['action'] !== QueueActions::DELETE
                    && $item['magento_object'] !== MagentoObject::PAYMENT_METHOD
                ) {
                    $queueObject                  = $queueModel->getQueueObject($item, $this->urlBuilder);
                    $item[$this->getData('name')] =
                        '<a href="' . $queueObject['url'] . '" target="_blank">' . $queueObject['name'] . '</a>';
                } else {
                    $item[$this->getData('name')] = $item['object'];
                }
            }
        }

        return $dataSource;
    }
}
