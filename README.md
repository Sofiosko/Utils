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

WebLoader using WLCreator
```php
use BiteSHOP\Utils\WLCreator;

$wlc = new WLCreator(__DIR__.'/webtemp/', 'http://localhost/Utils/tests/webtemp', __DIR__.'/assets', 'http://localhost/Utils/tests/assets');
$wlc->setCache(true);

$wl = $wlc->getCssLoader();
$wl->addLocal('style.css');
$wl->addLocal('style-2.css');
$wl->addRemote('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
echo $wl->render();
```

Standard usage
```php
use BiteSHOP\Utils\WebLoader;

$wl = new WebLoader(WebLoader::TYPE_CSS, __DIR__.'/assets', 'http://localhost/Utils/tests/assets');
$wl->setCache(true);
$wl->setDestination(__DIR__.'/webtemp/', 'http://localhost/Utils/tests/webtemp');
$wl->addLocal('style.css');
$wl->addLocal('style-2.css');
$wl->addRemote('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');

echo $wl->render();
```