<?php

require_once('nusoap/src/nusoap.php');

require_once('arsys-dns-update.php');

# Update below variables
$domain = 'example.com';
$api_password = 'secret';
$dns_record = 'site1.' . $domain;
$record_type = 'A';

$a = new ArsysDnsUpdate($domain, $api_password, $dns_record, $record_type, $record_type);
$response = $a->modifyDNSEntry();

?>
