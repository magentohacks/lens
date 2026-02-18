<?php
if (!isset($_GET['key']) || $_GET['key'] != '!Test123!')
{
    die('no access');
}
echo "<pre>";
print_r($_SERVER);
phpinfo();
echo 'test';

