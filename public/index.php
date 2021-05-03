<?php

use Framework\App;
use Framework\Renderer\PHPRenderer;
use Framework\Renderer\TwigRenderer;
use GuzzleHttp\Psr7\ServerRequest;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function Http\Response\send;

require '../vendor/autoload.php';

$renderer = new TwigRenderer(dirname(__DIR__) . '/views');

$loader = new FilesystemLoader(dirname(__DIR__) . '/views');
$twig = new Environment($loader, []);

$app = new App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);
$request = ServerRequest::fromGlobals();
$response = $app->run($request);

send($response);