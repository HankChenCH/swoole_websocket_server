<?php

// Autoload 自动载入
require __DIR__ . '/../vendor/autoload.php';

$ws = new \GHank\WSNotice\WebsocketServer();

$ws->run();