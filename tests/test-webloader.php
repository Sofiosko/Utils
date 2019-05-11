<?php
require_once __DIR__ . '/../src/init.php';

use BiteSHOP\Utils\WebLoader;
use BiteSHOP\Utils\WLCreator;
?>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Webloader test</title>
    <meta name="author" content="Marek Konderla">
    <?php
    $wlc = new WLCreator(__DIR__.'/webtemp/', 'http://localhost/Utils/tests/webtemp', __DIR__.'/assets', 'http://localhost/Utils/tests/assets');
    $wlc->setCache(true);

    $wl = $wlc->getCssLoader();
    $wl->addLocal('style.css');
    $wl->addLocal('style-2.css');
    $wl->addRemote('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
    echo $wl->render();

    $wl = $wlc->getCssLoader();
    $wl->addLocal('style-3.css');
    $wl->addLocal('style-4.css');
    echo $wl->render();
    ?>
</head>

<body>

<?php
echo '<pre>';
var_dump($wl);
echo '</pre>';
?>

</body>
</html>
