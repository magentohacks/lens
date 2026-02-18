<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 17/3/2016
 * Time: 11:22 AM
 */

namespace Magestore\Pdfinvoiceplus\Model;


abstract class AbstractQuotePdfTemplateRender extends AbstractPdfTemplateRender
{
    /**
     * Render entity data to a html template
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote $entity
     * @param                            $templateHtml
     *
     * @return mixed
     */
    public function renderQuote(\Magento\Quote\Model\Quote $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);
        $this->setTemplateHtml($templateHtml);
        $this->setProcessedHtml($templateHtml);

        return $this->getProcessedHtml();
    }
}