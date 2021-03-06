<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Wecamp\TalkBack\Controller\TopicController;
use Wecamp\TalkBack\Validate\CommentValidator;
use Wecamp\TalkBack\Validate\TopicValidator;

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

$app['propertyAccessor'] = $app->share(function() use ($app) {
    return new \Symfony\Component\PropertyAccess\PropertyAccessor();
});

$app['fixtures'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\LoadFixtures(
        $app['userRepository'],
        $app['topicRepository'],
        $app['badgeRepository']
    );
});

// Validators
$app['topicValidator'] = function() use ($app) {
    return new TopicValidator($app['validator']);
};
$app['commentValidator'] = function() use ($app) {
    return new CommentValidator($app['validator']);
};

// Badges

$app['storeEventSubscriber'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Subscriber\StoreEventSubscriber($app['badgeRepository']);
});

$app['superIdeaBadge'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Badge\SuperIdeaBadge($app['badgeRepository']);
});

// Twig

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


/**
 * API Controllers & routes
 */
$app['TopicController'] = $app->share(function() use ($app) {
    return new TopicController($app);
});

$app['UserController'] = $app->share(function() use ($app) {
    return new \Wecamp\TalkBack\Controller\UserController($app['badgeRepository']);
});

// Topic
$app->post('/api/topics', 'TopicController:newTopic')->bind('api.topic.new');
$app->get('/api/topics/detailed', 'TopicController:getAllDetailedTopics');
$app->get('/api/topics/{id}', 'TopicController:getTopicByIdentifier')->bind('api.topic.get_one');
$app->get('/api/topics', 'TopicController:getAllTopics')->bind('api.topic.get_all');
$app->post('/api/topic/{topicId}/comments', 'TopicController:newComment')->bind('api.comment.new');
$app->get('/api/topic/{topicId}/comments/{commentId}', 'TopicController:getCommentByIdentifier')->bind('api.comment.get_one');
$app->post('/api/topics/{id}/vote', 'TopicController:addVote')->bind('api.topic.vote');

// User
$app->get('/api/users/{id}/badges', 'UserController:getEarnedBadges')->bind('api.user.badges');

/**
 * HTML pages
 */
$app->get('/', function() use($app) {
    return $app['twig']->render('homepage.html.twig');
})->bind('homepage');

$app->get('/topics', function() use($app) {
    return $app['twig']->render('topiclist.html.twig', ['header_title' => 'browse topics', 'header_underline' => 'Read and vote on topics you like.']);
})->bind('topiclist');

$app->get('/topic/add', function() use($app) {
    return $app['twig']->render('addtopic.html.twig', ['header_title' => 'add a new topic', 'header_underline' => 'Share your creativity']);
})->bind('addtopic');

$app->get('/topic/{id}', function($id) use($app) {
    return $app['twig']->render('showtopic.html.twig', ['id' => $id,'header_title' => 'Topic', 'header_underline' => 'Amazing topic']); //todo: fix this with dynamic header and subline
})->bind('showtopic');

$app->get('/profile/change', function() use ($app) {
    return $app['twig']->render('profile/change.html.twig', ['header_title' => 'Change User', 'header_underline' => 'Change to whatever you like.']);
})->bind('profile.change');

$app->get('/profile/badges', function() use ($app) {
    /**
     * @todo - Updated to use user ID of current user
     */
    return $app['twig']->render('profile/badges.html.twig', ['user_id' => 1, 'header_title' => 'Badges', 'header_underline' => 'Watch your awesomeness.']);
})->bind('profile.badges');


$app->get('/setup', function() use($app) {
    /** @var \Wecamp\TalkBack\LoadFixtures $fixtures */
    $fixtures = $app['fixtures'];
    $fixtures->load();

    return 'Setup complete!';
});

/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
$dispatcher = $app['dispatcher'];
$dispatcher->addSubscriber($app['storeEventSubscriber']);
$dispatcher->addSubscriber($app['superIdeaBadge']);

$app->run();