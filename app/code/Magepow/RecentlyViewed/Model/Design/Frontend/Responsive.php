<?php

namespace Magepow\RecentlyViewed\Model\Design\Frontend;

class Responsive
{

    public static function getBreakpoints()
    {
        return [1921=>'visible', 1920=>'widescreen', 1480=>'desktop', 1200=>'laptop', 992=>'notebook', 768=>'tablet', 576=>'landscape', 481=>'portrait', 361=>'mobile', 1=>'mobile'];
    }

}
