**ArrayWalker.php** - Enables access to values in multidimensional arrays without using offsetGet and checking the existence of a key.
```php
$array = [
    'level-1' => [
        'value' => 1,
        'level-2' => [
            'value' => 2
        ]
    ]
];

$walker = \BiteIT\Utils\ArrayWalker::create($array);

var_dump([
    $walker->get('level-1/value'), // returns 1
    $walker->get('level-1/level-2) // returns ['value' => 2]
]);
```

**Timer** - Nests traced processes and renders nested lists for better orientation.
```php
\BiteIT\Utils\Timer::start('level-1');
// your code
\BiteIT\Utils\Timer::start('level-1-1');
// your code
\BiteIT\Utils\Timer::end('level-1-1');
\BiteIT\Utils\Timer::end('level-1');

// renders nested list
echo \BiteIT\Utils\Timer::getInstance()->renderList();
```
Outputs:
```html
<ul class="process-tree">
    <li class="branch">
        <span class="name">level-1 (3.0017)</span>
        <ul class="process-tree">
            <li class="branch">
                <span class="name">level-1-1 (3.0016)</span>
                <ul class="process-tree">
                    <li class="branch">
                        <span class="name">level-1-1-1 (1.0009)</span>
                    </li>
                    <li class="branch">
                        <span class="name">level-1-1-2 (2.0007)</span>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li class="branch">
        <span class="name">level-2 (0)</span>
        <ul class="process-tree">
            <li class="branch">
                <span class="name">level-2-1 (0)</span>
            </li>
        </ul>
    </li>
</ul>
```

**WebLoader** - Merges, minifies, cache and renders assets. It also looks for differences in files, automatically removes old merged and creates new.

Method 1 - Standard usage
```php
use BiteIT\Utils\WebLoader;

$wl = new WebLoader(WebLoader::TYPE_CSS, __DIR__.'/assets', 'http://localhost/Utils/tests/assets');
$wl->setCache(true);
$wl->setDestination(__DIR__.'/webtemp/', 'http://localhost/Utils/tests/webtemp');
$wl->addLocal('style.css');
$wl->addLocal('style-2.css');
$wl->addRemote('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');

echo $wl->render();
```

Method 2 - Using WLCreator
```php
use BiteIT\Utils\WLCreator;

$wlc = new WLCreator(__DIR__.'/webtemp/', 'http://localhost/Utils/tests/webtemp', __DIR__.'/assets', 'http://localhost/Utils/tests/assets');
$wlc->setCache(true);

$wl = $wlc->getCssLoader();
$wl->addLocal('style.css');
$wl->addLocal('style-2.css');
$wl->addRemote('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
echo $wl->render();
```

Outputs:
```html
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="http://yourdomain.com/path/to/webtemp/style-3edb6bee1b04609e674e3c2765aabe05-4028fdd3020be3df5cbcc95000720556.css">
```


WebLoader known issues:
- Assets baseUrl must be accessible trough browser for non cached webloader render. Because it will just render list of sources with timestamp at the end.

ArrayWalker known issues:
- Multidimensional OffsetGet and OffsetUnset not working. ArrayWalker was not intended to implement ArrayAccess but may be fixed soon.