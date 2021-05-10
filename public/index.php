<?php

use DI\ContainerBuilder;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    \App\Admin\AdminModule::class,
    \App\Blog\BlogModule::class,
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$container = $builder->build();

$app = new App($container, $modules);
if (php_sapi_name() !== 'cli') {
    $request = ServerRequest::fromGlobals();
    $response = $app->run($request);
    send($response);
}
