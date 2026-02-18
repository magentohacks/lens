<?php

namespace BoostMyShop\OrderPreparation\Model\CarrierTemplate\Renderer;

class OrderDetailsExport extends RendererAbstract
{
    public function getShippingLabelFile($ordersInProgress, $carrierTemplate){

        $content = '';

        $content .= $this->appendTemplate($carrierTemplate->getct_export_file_header(), []);

        foreach($ordersInProgress as $orderInProgress)
        {

            $content .= $this->appendTemplate($carrierTemplate->getct_export_file_order_header(), $orderInProgress->getDatasForExport());
            foreach($orderInProgress->getAllItems() as $item)
            {
                $content .= $this->appendTemplate($carrierTemplate->getct_export_file_order_products(), $item->getDatasForExport());
            }
            $content .= $this->appendTemplate($carrierTemplate->getct_export_file_order_footer(), $orderInProgress->getDatasForExport());
        }

        return $content;
    }

    protected function appendTemplate($template, $data)
    {
        $regExp = '*({[^}]+})*';
        preg_match_all($regExp, $template, $result, PREG_OFFSET_CAPTURE);
        foreach ($result[0] as $item) {
            $code = str_replace('{', '', str_replace('}', '', $item[0]));
            if (isset($data[$code]))
                $template = str_replace($item[0], $data[$code], $template);
            else
                $template = str_replace($item[0], '', $template);
        }

        return $template;
    }

}