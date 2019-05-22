<?php
require_once __DIR__ . '/../src/init.php';

/**
 * Array to look in for values
 */
$array = [
    'level-1' => [
        'value' => true,
        'level-2' => [
            'value' => true,
            'level-3' => [
                'value' => true
            ]
        ]
    ]
];


/**
 * Keys
 */
$keys = [
    'level-1',
    'level-1/level-2',
    'level-1/level-2/level-3',
    'test'
];

/**
 * Walker
 */
$walker = \BiteIT\Utils\ArrayWalker::create($array);

echo '<pre>';
echo '<h2>ArrayWalker Get Tests</h2>';
foreach ($keys as $key) {

    var_dump($walker->get($key));

}

echo '<h2>ArrayWalker OffsetGet Tests</h2>';
var_dump($walker['level-1']['value']);
var_dump($walker['level-1']['level-2']);

echo '<h2>ArrayWalker OffsetSet Tests</h2>';
$walker['test'] = true;
var_dump($walker['test']);

echo '<h2>ArrayWalker Unset Tests</h2>';
unset($walker['test']);
var_dump($walker['test']);

echo '</pre>';