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

$app['TopicController'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Controller\TopicController($app, $app['topicRepository']);
});

$app->get('/', function() use($app) {
    return <<<'EOF'
<!DOCTYPE html>
<html>
  <head>
    <title>Talkback</title>
    <script src="/assets/js/vendor/JSXTransformer.js"></script>
    <script src="/assets/js/vendor/react.min.js"></script>
    <link rel="stylesheet" href="/assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
  </head>
  <body>
    <div class="container">
      <div id="addtopicform"></div>
    </div>
    <script type="text/jsx" src="/assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script type="text/jsx" src="/assets/js/modules/methods.js"></script>
    <script type="text/jsx" src="/assets/js/forms/addtopic.js"></script>
  </body>
</html>
EOF;
})->bind('homepage');

$app->get('/setup', function() use($app) {
    /** @var \Wecamp\TalkBack\LoadFixtures $fixtures */
    $fixtures = $app['fixtures'];
    $fixtures->load();

    return 'Setup complete!';
});

$app->post('/api/topics', 'TopicController:newTopic');
$app->get('/api/topics/{id}', 'TopicController:getTopic');

$app->run();