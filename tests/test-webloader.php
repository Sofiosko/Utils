<?php
require_once __DIR__ . '/../src/init.php';

use BiteIT\Utils\WebLoader;
use BiteIT\Utils\WLCreator;
use BiteIT\Utils\Timer;

?>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Webloader test</title>
    <meta name="author" content="Marek Konderla">
    <?php
    $url = dirname(explode('?', $_SERVER['REQUEST_URI'])[0]);

    Timer::start('WebLoader');
    $wlc = new WLCreator(__DIR__ . '/webtemp/', $url.'/webtemp', __DIR__ . '/assets', $url.'/assets');
    $wlc->setCache(true);

    $wl = $wlc->getCssLoader();
    $wl->addLocal('style.css');
    $wl->addLocal('style-2.css');
    $wl->addLocal('style-5.min.css');
    echo $wl->render();

    $wl = $wlc->getCssLoader();
    $wl->addLocal('style-3.css');
    $wl->addLocal('style-4.css');
    $wl->addLocal('style-5.min.css');
    $wl->addRemote('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
//    $wl->validateRemoteSources();
    echo $wl->render([], 'fixed-name');

    $wl = $wlc->getJsLoader();
    $wl->addLocal('test.js');
    $wl->addLocal('test-2.js');
    $wl->addLocal('test-3.min.js');
    echo $wl->render();
    Timer::end('WebLoader');
    ?>
</head>

<body>
<?php
echo \BiteIT\Utils\Timer::getInstance()->renderList();
?>
</body>
</html>
