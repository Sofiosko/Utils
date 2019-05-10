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
$walker = \BiteSHOP\Utils\ArrayWalker::create($array);

echo '<pre>';
foreach ($keys as $key){

    var_dump($walker->get($key));

}
echo '</pre>';