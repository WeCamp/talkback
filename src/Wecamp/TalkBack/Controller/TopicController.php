<?php

namespace Wecamp\TalkBack\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Wecamp\TalkBack\Repository\TopicRepository;
use Wecamp\TalkBack\Validate\TopicValidator;

class TopicController extends AbstractController
{

    /**
     * @var TopicRepository
     */
    private $topicRepository;

    /**
     * @var Application
     */
    private $app;


    /**
     * @param TopicRepository $topicRepository
     */
    public function __construct($app, TopicRepository $topicRepository)
    {
        $this->app             = $app;
        $this->topicRepository = $topicRepository;
    }


    /**
     * Creates a new topic
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @internal param $Request
     */
    public function newTopic(Request $request)
    {
        $data           = $request->request->all();
        $topicValidator = new TopicValidator($this->app['validator']);

        if ($topicValidator->isNewTopicValid($data) !== true) {
            $lastErrors = $topicValidator->getLastErrors();

            return $this->getInvalidDataResponse($lastErrors);
        }

        $topicID = $this->topicRepository->createTopic($data);

        if ($topicID === false) {
            return new JsonResponse(['error' => 'Could not create topic.'], 503);
        }

        $newData = $this->topicRepository->getTopicByIdentifier($topicID);

        return new JsonResponse(
            [
                'id'               => $topicID,
                'title'            => $newData['title'],
                'details'          => $newData['details'],
                'excerpt'          => $newData['excerpt'],
                'owned_by_creator' => $newData['owned_by_creator'],
                'created_at'       => $newData['created_at'],
            ], 201
        );
    }


    /**
     * @return JsonResponse
     */
    public function getTopics()
    {
        return new JsonResponse($this->topicRepository->getTopics());
    }


    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function getTopic($id)
    {
        $topic = $this->topicRepository->getTopicByIdentifier($id);

        if ($topic === false) {
            return new JsonResponse(['error' => 'topic not found'], 404);
        }

        return new JsonResponse($topic, 200);
    }


    /**
     * @param ConstraintViolationListInterface $lastErrors
     *
     * @return JsonResponse
     */
    protected function getInvalidDataResponse(ConstraintViolationListInterface $lastErrors)
    {
        $errors = [];
        foreach ($lastErrors as $validationError) {
            $field            = $validationError->getPropertyPath();
            $errors[$field][] = $validationError->getMessage();
        }

        return new JsonResponse(
            [
                'errors'            => [
                    [
                        'message' => 'Data is invalid',
                    ],
                ],
                'validation_errors' => $errors,
            ], 503
        );
    }


}

