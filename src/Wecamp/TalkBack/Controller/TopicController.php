<?php

namespace Wecamp\TalkBack\Controller;

use Codeception\Module\Asserts;
use Codeception\Module\Silex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Wecamp\TalkBack\Repository\TopicRepository;
use Wecamp\TalkBack\Validate\TopicValidate;

use Symfony\Component\Validator\Constraints as Assert;

class TopicController
{

    /**
     * @var TopicRepository
     */
    private $topicRepository;

    /**
     * @var app
     */
    private $app;

    /**
     * @param TopicRepository $topicRepository
     */
    public function __construct($app, TopicRepository $topicRepository)
    {
        $this->app = $app;
        $this->topicRepository = $topicRepository;
    }

    /**
     * Creates a new topic
     * @param Request $request
     * @return JsonResponse
     * @internal param $Request
     */
    public function newTopic(Request $request)
    {
        $data = $request->request->all();
        $topicValidate = new TopicValidate($this->app);

        if($topicValidate->validateTopic($data) !== true) {
            return new JsonResponse(array('error' => 'Could not create topic.'), 400);
        }

        $topicID = $this->topicRepository->createTopic($data);

        if($topicID === false) {
            return new JsonResponse(array('error' => 'Could not create topic.'), 503);
        }

        return new JsonResponse(array(
            'id' => $topicID,
            'title' => $data['title'],
            'details' => $data['details'],
            'excerpt' => $data['excerpt'],
            'owned_by_creator' => $data['owned_by_creator']
        ) , 201);
    }

    public function getTopics()
    {
        return new JsonResponse($this->topicRepository->getTopics());
    }

    public function getTopic($id)
    {
        return new JsonResponse($this->topicRepository->getTopicWithID($id));
    }


}

