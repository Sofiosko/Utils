<?php
require_once __DIR__ . '/../src/init.php';

\BiteSHOP\Utils\Timer::start('level-1');

\BiteSHOP\Utils\Timer::start('level-1-1');

\BiteSHOP\Utils\Timer::start('level-1-1-1');
sleep(1);
\BiteSHOP\Utils\Timer::end('level-1-1-1');

\BiteSHOP\Utils\Timer::start('level-1-1-2');
sleep(2);
\BiteSHOP\Utils\Timer::end('level-1-1-2');

\BiteSHOP\Utils\Timer::end('level-1-1');

\BiteSHOP\Utils\Timer::end('level-1');

\BiteSHOP\Utils\Timer::start('level-2');

\BiteSHOP\Utils\Timer::start('level-2-1');
\BiteSHOP\Utils\Timer::end('level-2-1');

\BiteSHOP\Utils\Timer::end('level-2');

echo '<pre>';
var_dump(\BiteSHOP\Utils\Timer::getInstance()->renderList());
echo '</pre>';
