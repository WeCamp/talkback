<?php

namespace Wecamp\TalkBack\Controller;

use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Wecamp\TalkBack\Event\TopicAddedEvent;
use Wecamp\TalkBack\Event\TopicCommentAddedEvent;
use Wecamp\TalkBack\Repository\TopicRepository;
use Wecamp\TalkBack\Validate\CommentValidator;
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
        $data   = $request->request->all();
        $userId = $request->headers->get('X-UserId');

        // @todo: This doesn't work with the boolean value currently
//        if ($this->topicValidator->isNewTopicValid($data) !== true) {
//            $lastErrors = $this->topicValidator->getLastErrors();
//
//            return $this->getInvalidDataResponse($lastErrors);
//        }

        $topicId = $this->topicRepository->createTopic($data, $userId);

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

        $topic['comments'] = $this->topicRepository->getCommentsForTopic($id);

        return new JsonResponse($topic, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function newComment(Request $request)
    {
        $data             = $request->request->all();
        $userId           = $request->headers->get('X-UserId');
        $commentValidator = new CommentValidator($this->app['validator']);

        if ($commentValidator->isNewCommentValid($data) !== true) {
            $lastErrors = $commentValidator->getLastErrors();

            return $this->getInvalidDataResponse($lastErrors);
        }

        $commentId = $this->topicRepository->createComment($data, $userId);

        if ($commentId === false) {
            return new JsonResponse(['error' => 'Could not create comment.'], 503);
        }

        $this->dispatcher->dispatch('topic.comment', new TopicCommentAddedEvent($data['user']));

        $newData = $this->topicRepository->getCommentByIdentifier($commentId);

        return new JsonResponse(
            [
                'id'               => $commentId,
                'topic'            => $newData['topic'],
                'commenter'        => $newData['commenter'],
                'content'          => $newData['content'],
                'created_at'       => $newData['created_at']
            ], 201
        );
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function getCommentByIdentifier($id)
    {
        $comment = $this->topicRepository->getCommentByIdentifier($id);

        if ($comment === false) {
            return new JsonResponse(['error' => 'comment not found'], 404);
        }

        return new JsonResponse($comment, 200);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addVote(Request $request)
    {
        $topic  = $request->get('topic');
        $userId = $request->headers->get('X-UserId');

        $success = $this->topicRepository->addVote($topic, $userId, new \DateTime());

        if ($success === false) {
            return new JsonResponse(['error' => 'Could not create vote.'], 503);
        }

        return new JsonResponse('ok', 201);
    }


    /**
     * @return JsonResponse
     */
    public function getAllDetailedTopics()
    {
        $topics = $this->topicRepository->getAllDetailedTopics();

        if($topics === false) {
            return new JsonResponse(array('error' => 'No topics found'), 404);
        }

        return new JsonResponse($topics, 200);
    }

}

