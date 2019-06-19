<?php
require_once __DIR__ . '/../src/init.php';

$api = new \BiteIT\Utils\Subjects('key');
$api->setCache(__DIR__ . '/temp', 10);

echo '<pre>';
var_dump([
    $api->validateCompanyCode('05987059'),
    $api->validateTaxNumber('05987059'),
    $api->getCompanyInfo('05987059'),
]);
