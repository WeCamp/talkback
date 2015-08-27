<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

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

/**
 * Twig bits
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


/**
 * API Controllers & routes
 */
$app['TopicController'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Controller\TopicController($app, $app['topicRepository']);
});

$app->post('/api/topics', 'TopicController:newTopic');
$app->get('/api/topics/{id}', 'TopicController:getTopicByIdentifier');
$app->get('/api/topics', 'TopicController:getAllTopics');


/**
 * HTML pages
 */
$app->get('/', function() use($app) {
    return $app['twig']->render('homepage.html.twig');
})->bind('homepage');

$app->get('/topics', function() use($app) {
    return $app['twig']->render('topiclist.html.twig');
})->bind('topiclist');

$app->get('/topic/{id}', function($id) use($app) {
    return $app['twig']->render('showtopic.html.twig', ['id' => $id]);
})->bind('showtopic');

$app->get('/topic/add', function() use($app) {
    return $app['twig']->render('addtopic.html.twig');
})->bind('addtopic');

$app->get('/setup', function() use($app) {
    /** @var \Wecamp\TalkBack\LoadFixtures $fixtures */
    $fixtures = $app['fixtures'];
    $fixtures->load();

    return 'Setup complete!';
});

$app->run();