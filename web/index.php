<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['TopicController'] = $app->share(function() use ($app) {
    return new Wecamp\TalkBack\Controller\TopicController($app);
});

$app->get('/', function() use($app) {
    return 'Hello World';
})->bind('homepage');

$app->post('/topics', 'TopicController:newTopic');

$app->get('/topics/{id}', function($id) use($app) {
    return $app->json($id);
});


$app->run();