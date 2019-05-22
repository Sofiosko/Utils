<?php
require_once __DIR__ . '/../src/init.php';

\BiteIT\Utils\Timer::start('level-1');

\BiteIT\Utils\Timer::start('level-1-1');

\BiteIT\Utils\Timer::start('level-1-1-1');
sleep(1);
\BiteIT\Utils\Timer::end('level-1-1-1');

\BiteIT\Utils\Timer::start('level-1-1-2');
sleep(2);
\BiteIT\Utils\Timer::end('level-1-1-2');

\BiteIT\Utils\Timer::end('level-1-1');

\BiteIT\Utils\Timer::end('level-1');

\BiteIT\Utils\Timer::start('level-2');

\BiteIT\Utils\Timer::start('level-2-1');
\BiteIT\Utils\Timer::end('level-2-1');

\BiteIT\Utils\Timer::end('level-2');


echo \BiteIT\Utils\Timer::getInstance()->renderList();
