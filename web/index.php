<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app['topicRepository'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Repository\TopicRepository();
});

$app['userRepository'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Repository\UserRepository();
});

$app['badgeRepository'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Repository\BadgeRepository();
});

$app['fixtures'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\LoadFixtures(
        $app['userRepository'],
        $app['topicRepository'],
        $app['badgeRepository']
    );
});

$app->get('/', function() use($app) {
    return 'Hello World';
});

$app->get('/setup', function() use($app) {
    /** @var \Wecamp\TalkBack\LoadFixtures $fixtures */
    $fixtures = $app['fixtures'];
    $fixtures->load();

    return 'Setup complete!';
});

$app->post('/topics', function() use($app) {
    $topicController = new \Wecamp\TalkBack\Controller\TopicController();
    return $topicController->newTopic();
});

$app->get('/topics/{id}', function($id) use($app) {
    return $app->json($id);
});


$app->run();