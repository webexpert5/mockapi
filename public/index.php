<?php

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../src/settings.php';

$app = new \Slim\App($settings);

require __DIR__ . '/../src/dependencies.php';

/*require __DIR__ . '/../src/middleware.php';
require __DIR__ . '/../src/Controllers/MockApiController.php';*/

$app->get('/fetch', '\Package\MockApiController:get_method');
$app->get('/getorder/order/{orderid}', '\Package\MockApiController:getorder');
$app->put('/cancelorder/order/{orderid}', '\Package\MockApiController:cancelorder');
$app->get('/cancelorder/{orderid}', '\Package\MockApiController:get_cancelorder');

$app->run();
