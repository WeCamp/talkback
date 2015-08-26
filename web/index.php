<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function() use($app) {
    return 'Hello World';
});

$app->post('/topics', function() use($app) {
    $topicController = new \Wecamp\TalkBack\Controller\TopicController();
    return $topicController->newTopic();
});

$app->get('/topics/{id}', function($id) use($app) {
    return $app->json($id);
});


$app->run();