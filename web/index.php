<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function() use($app) {
    return 'Hello World';
});

$app->post('/topics', function() use($app) {
    return $app->json('Hello');
});

$app->post('/topics/{id}', function($id) use($app) {
    return $app->json($id);
});


$app->run();