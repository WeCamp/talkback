<?php

namespace Wecamp\TalkBack\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class TopicController
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Creates a new topic
     * @param silex object
     * @return JsonResponse
     */
    public function newTopic()
    {
        return $this->app->json('OK');
    }

}

