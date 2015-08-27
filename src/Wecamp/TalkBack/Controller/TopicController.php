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
        $data          = $request->request->all();
        $topicValidator = new TopicValidator($this->app['validator']);

        if ($topicValidator->isNewTopicValid($data) !== true) {
            $lastErrors = $topicValidator->getLastErrors();

            return $this->getInvalidDataResponse($lastErrors);
        }

        $topicID = $this->topicRepository->createTopic($data);

        if ($topicID === false) {
            return new JsonResponse(['error' => 'Could not create topic.'], 503);
        }

        return new JsonResponse(
            [
                'id'               => $topicID,
                'title'            => $data['title'],
                'details'          => $data['details'],
                'excerpt'          => $data['excerpt'],
                'owned_by_creator' => $data['owned_by_creator'],
            ], 201
        );
    }


    public function getTopic($id)
    {
        return new JsonResponse(['error' => 'Nothing here yet'], 503);
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
                    ]
                ],
                'validation_errors' => $errors
            ], 503
        );
    }


}

