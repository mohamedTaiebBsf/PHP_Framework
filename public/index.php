<?php

use Framework\App;
use Framework\Renderer;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

require '../vendor/autoload.php';

$renderer = new Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');

$app = new App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);
$request = ServerRequest::fromGlobals();
$response = $app->run($request);

send($response);