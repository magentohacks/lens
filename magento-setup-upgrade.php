<?php

set_time_limit(0);

exec('php bin/magento setup:static-content:deploy en_US', $output);

foreach($output as $op){
   echo $op.'<br/>';
}
