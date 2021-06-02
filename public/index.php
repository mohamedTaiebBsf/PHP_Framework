<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$app = (new Framework\App(dirname(__DIR__) . '/config/config.php'))
    ->addModule(\App\Admin\AdminModule::class)
    ->addModule(\App\Blog\BlogModule::class)
    ->pipe(\Middlewares\Whoops::class)
    ->pipe(\Framework\Middleware\TrainingSlashMiddleware::class)
    ->pipe(\Framework\Middleware\MethodMiddleware::class)
    ->pipe(\Framework\Middleware\CsrfMiddleware::class)
    ->pipe(\Framework\Middleware\RouterMiddleware::class)
    ->pipe(\Framework\Middleware\DispatcherMiddleware::class)
    ->pipe(\Framework\Middleware\NotFoundMiddleware::class);

if (php_sapi_name() !== 'cli') {
    $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    $response = $app->run($request);
    \Http\Response\send($response);
}
