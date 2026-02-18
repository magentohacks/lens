<?php
namespace Konstant\Managefaq\Model;

/**
 * Status
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Status
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled')
            , self::STATUS_DISABLED => __('Disabled'),
        ];
    }
}
