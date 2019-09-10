<?php
require_once __DIR__ . '/../src/init.php';

$images = [
    'https://www.mmservis-pracky.cz/images/category/1.jpg',
    'https://www.mmservis-pracky.cz/images/category/388.jpg'
];

$fetcher = new BiteIT\Utils\FileFetcher\Fetcher();
$fetcher->setCache(__DIR__.'/temp/');

foreach ($images as $imageUrl){
    $result = $fetcher->downloadFile($imageUrl, __DIR__.'/temp/'.basename($imageUrl));
    var_dump($result);
}
