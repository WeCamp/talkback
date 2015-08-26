<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

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
    return new Wecamp\TalkBack\Controller\TopicController($app);
});

$app->get('/', function() use($app) {
    return <<<'EOF'
<!DOCTYPE html>
<html>
  <head>
    <title>Talkback</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.11.1/JSXTransformer.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.11.1/react.js"></script>
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
  </head>
  <body>
    <div class="container">
      <div id="addtopicform"></div>
    </div>
    <script type="text/jsx" src="/assets/js/addtopic.js"></script>
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

$app->post('/topics', 'TopicController:newTopic');

$app->get('/topics/{id}', function($id) use($app) {
    return $app->json($id);
});

$app->run();