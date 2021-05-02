<?php

use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

require '../vendor/autoload.php';

$app = new App([
    \App\Blog\BlogModule::class
]);
$request = ServerRequest::fromGlobals();
$response = $app->run($request);

send($response);