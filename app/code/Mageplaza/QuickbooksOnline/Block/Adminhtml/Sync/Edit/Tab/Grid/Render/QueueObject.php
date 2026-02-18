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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QueueActions;

/**
 * Class QueueObject
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit\Tab\Grid\Render
 */
class QueueObject extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        if ((int) $row->getAction() === QueueActions::DELETE
            || $row->getMagentoObject() === MagentoObject::PAYMENT_METHOD
        ) {
            return $row->getObject();
        }
        $queueObject = $row->getQueueObject($row, $this->_urlBuilder);

        return '<a href="' . $queueObject['url'] . '" target="_blank">' . $queueObject['name'] . '</a>';
    }
}
