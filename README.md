ArrayWalker
```php
$array = [
    'level-1' => [
        'value' => 1,
        'level-2' => [
            'value' => 2
        ]
    ]
];

$walker = \BiteSHOP\Utils\ArrayWalker::create($array);

var_dump([
    $walker->get('level-1/value'), // returns 1
    $walker->get('level-1/level-2) // returns ['value' => 2]
]);
```

Timer that nests processes and renders nested lists 
```php
\BiteSHOP\Utils\Timer::start('level-1');
// your code
\BiteSHOP\Utils\Timer::start('level-1-1');
// your code
\BiteSHOP\Utils\Timer::end('level-1-1');
\BiteSHOP\Utils\Timer::end('level-1');

// renders nested list
echo \BiteSHOP\Utils\Timer::getInstance()->renderList();
```