<?php

use DI\ContainerBuilder;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

require '../vendor/autoload.php';

$modules = [
    \App\Blog\BlogModule::class
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


$request = ServerRequest::fromGlobals();
$response = $app->run($request);

send($response);