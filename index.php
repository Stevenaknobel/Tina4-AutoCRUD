<?php
require_once './vendor/autoload.php';

global $DBA;
$DBA = new \Tina4\DataSQLite3('test.db', $username = '', $password = '');

$config = new \Tina4\Config(static function (\Tina4\Config $config) {
    // Additional configurations, if needed
});

echo new \Tina4\Tina4Php($config);
