<?php
require_once __DIR__ . '/../src/init.php';

$storage = new ExampleDataStorage();

echo '<pre>';
var_dump($storage->getData('level-1'));
echo '</pre>';