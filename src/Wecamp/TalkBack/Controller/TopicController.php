<?php

namespace Wecamp\TalkBack\Controller;

use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Wecamp\TalkBack\Event\TopicAddedEvent;
use Wecamp\TalkBack\Repository\TopicRepository;
use Wecamp\TalkBack\Validate\CommentValidator;
use Wecamp\TalkBack\Validate\TopicValidator;

class TopicController extends AbstractController
{

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
//        $topicValidator = $this->getTopicValidator();
//        if ($topicValidator->isNewTopicValid($data) !== true) {
//            $lastErrors = $this->topicValidator->getLastErrors();
//
//            return $this->getInvalidDataResponse($lastErrors);
//        }

        $data['user'] = 1; // Temporary user

        $topicId = $this->getTopicRepository()->createTopic($data);

        if ($topicId === false) {
            return new JsonResponse(['error' => 'Could not create topic.'], 503);
        }

        $newData = $this->getTopicRepository()->getTopicByIdentifier($topicId);

        $this->getDispatcher()->dispatch('topic.add', new TopicAddedEvent($data['user']));

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
        $topics = $this->getTopicRepository()->getAllTopics();

        if ($topics === false) {
            return new JsonResponse(['error' => 'No topics found'], 404);
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
        $topic = $this->getTopicRepository()->getTopicByIdentifier($id);

        if ($topic === false) {
            return new JsonResponse(['error' => 'topic not found'], 404);
        }

        $topic['comments'] = $this->getTopicRepository()->getCommentsForTopic($id);

        return new JsonResponse($topic, 200);
    }


    /**
     * @param Request $request
     * @param int     $topicId Topic ID
     *
     * @return JsonResponse
     */
    public function newComment(Request $request, $topicId)
    {
        $topic = $this->getTopicRepository()->getTopicByIdentifier($topicId);

        if (empty($topic)) {
            return new JsonResponse(
                [
                    'errors' => [
                        ['message' => 'Topic not found'],
                    ],
                ], 404
            );
        }

        $data          = $request->request->all();
        $data['topic'] = (int)$topicId;
        $userId        = $request->headers->get('X-UserId');

        $commentValidator = $this->getCommentValidator();
        if ($commentValidator->isNewCommentValid($data) !== true) {
            $lastErrors = $commentValidator->getLastErrors();

            return $this->getInvalidDataResponse($lastErrors);
        }

        $commentId = $this->getTopicRepository()->createComment($data, $userId);

        if ($commentId === false) {
            return new JsonResponse(['error' => 'Could not create comment.'], 503);
        }

        $this->dispatcher->dispatch('topic.comment', new TopicCommentAddedEvent($data['user']));

        $newData = $this->getTopicRepository()->getCommentByIdentifier($commentId);

        return new JsonResponse(
            [
                'id'         => (int)$commentId,
                'topic'      => (int)$newData['topic'],
                'commenter'  => $newData['commenter'],
                'content'    => $newData['content'],
                'created_at' => $newData['created_at'],
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
        $comment = $this->getTopicRepository()->getCommentByIdentifier($id);

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

        if ($topics === false) {
            return new JsonResponse(['error' => 'No topics found'], 404);
        }

        return new JsonResponse($topics, 200);
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return TopicRepository
     */
    protected function getTopicRepository()
    {
        return $this->app['topicRepository'];
    }


    /**
     * @return EventDispatcher
     */
    protected function getDispatcher()
    {
        return $this->app['dispatcher'];
    }


    /**
     * @return TopicValidator
     */
    protected function getTopicValidator()
    {
        return $this->app['topicValidator'];
    }


    /**
     * @return CommentValidator
     */
    protected function getCommentValidator()
    {
        return $this->app['commentValidator'];
    }
}

