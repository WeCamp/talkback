<?php

namespace Wecamp\TalkBack\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;

class TopicController
{

    /**
     * Creates a new topic
     * @param silex object
     * @return JsonResponse
     */
    public function newTopic()
    {
        return new JsonResponse('HI');
    }

}

