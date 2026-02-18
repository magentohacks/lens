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

use Exception;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;

/**
 * Class QueueActions
 * @package Mageplaza\QuickbooksOnline\Ui\Component\Listing\Columns
 */
class QueueActions extends Column
{
    /**
     * @var QueueStatus
     */
    protected $queueStatus;

    /**
     * QueueActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param QueueStatus $queueStatus
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        QueueStatus $queueStatus,
        array $components = [],
        array $data = []
    ) {
        $this->queueStatus = $queueStatus;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     * @throws Exception
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'label' => __('View')
                ];

                $item['popup_content'] = $this->getPopupContent($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    public function getPopupContent($item)
    {
        $json = json_encode(
            json_decode($item['json_response']),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        $html = '<div id="mpquickbooks-popup"><table class="data-table admin__table-secondary"><tbody>';
        $html .= '<tr><th>' . __('Queue ID') . '</th><td>' . $item['queue_id'] . '</td></tr>';
        $html .= '<tr><th>' . __('Object') . '</th><td>' . $item['object'] . '</td></tr>';
        $html .= '<tr><th>' . __('Status') . '</th><td>';
        $html .= $this->queueStatus->getOptionArray()[$item['status']] . '</td></tr>';
        $html .= '<tr><th>' . __('Sync Rule') . '</th><td>' . $item['sync_id'] . '</td></tr>';
        $html .= '<tr><th>' . __('Website') . '</th><td>' . $item['website'] . '</td></tr>';
        $html .= '<tr><th>' . __('Magento Object') . '</th><td>' . $item['magento_object'] . '</td></tr>';
        $html .= '<tr><th>' . __('Quickbooks Module') . '</th><td>' . $item['quickbooks_module'] . '</td></tr>';
        $html .= '<tr><th>' . __('Json Response') . '</th><td><pre>' . $json . '</pre></td></tr>';
        $html .= '</tbody></table></div>';

        return $html;
    }
}
