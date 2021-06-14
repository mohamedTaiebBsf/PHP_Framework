<?php

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$app = (new Framework\App('config/config.php'))
    ->addModule(\App\Admin\AdminModule::class)
    ->addModule(\App\Blog\BlogModule::class)
    ->addModule(\App\Auth\AuthModule::class);
$container = $app->getContainer();
$app->pipe(\Middlewares\Whoops::class)
    ->pipe(\Framework\Middleware\TrainingSlashMiddleware::class)
    ->pipe(\App\Auth\Middleware\ForbiddenMiddleware::class)
    ->pipe($container->get('admin.prefix'), \Framework\Auth\LoggedInMiddleware::class)
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
