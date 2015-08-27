<?php

namespace Wecamp\TalkBack\Controller;

use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Wecamp\TalkBack\Event\TopicAddedEvent;
use Wecamp\TalkBack\Repository\TopicRepository;
use Wecamp\TalkBack\Validate\TopicValidator;

class TopicController extends AbstractController
{

    /**
     * @var TopicRepository
     */
    private $topicRepository;

    /**
     * @var TopicValidator
     */
    private $topicValidator;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param TopicRepository $topicRepository
     * @param TopicValidator $topicValidator
     * @param EventDispatcher $dispatcher
     */
    public function __construct(TopicRepository $topicRepository, TopicValidator $topicValidator, EventDispatcher $dispatcher)
    {
        $this->topicRepository  = $topicRepository;
        $this->topicValidator   = $topicValidator;
        $this->dispatcher = $dispatcher;
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
        $data = $request->request->all();

        // @todo: This doesn't work with the boolean value currently
//        if ($this->topicValidator->isNewTopicValid($data) !== true) {
//            $lastErrors = $this->topicValidator->getLastErrors();
//
//            return $this->getInvalidDataResponse($lastErrors);
//        }

        $data['user'] = 1; // Temporary user

        $topicId = $this->topicRepository->createTopic($data);

        if ($topicId === false) {
            return new JsonResponse(['error' => 'Could not create topic.'], 503);
        }

        $newData = $this->topicRepository->getTopicByIdentifier($topicId);

        $this->dispatcher->dispatch('topic.add', new TopicAddedEvent($data['user']));

        return new JsonResponse(
            [
                'id'               => $topicId,
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
    public function getAllTopics()
    {
        $topics = $this->topicRepository->getAllTopics();

        if($topics === false) {
            return new JsonResponse(array('error' => 'No topics found'), 404);
        }

        return new JsonResponse($topics, 200);
    }


    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function getTopicByIdentifier($id)
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

