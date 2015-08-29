<?php

namespace Wecamp\TalkBack;

use Wecamp\TalkBack\Repository\BadgeRepository;
use Wecamp\TalkBack\Repository\TopicRepository;
use Wecamp\TalkBack\Repository\UserRepository;

class LoadFixtures
{
    /**
     * @var TopicRepository
     */
    private $topicRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var BadgeRepository
     */
    private $badgeRepository;

    /**
     * @param UserRepository $userRepository
     * @param TopicRepository $topicRepository
     * @param BadgeRepository $badgeRepository
     */
    public function __construct(
        UserRepository $userRepository,
        TopicRepository $topicRepository,
        BadgeRepository $badgeRepository
    )
    {
        $this->topicRepository = $topicRepository;
        $this->userRepository = $userRepository;
        $this->badgeRepository = $badgeRepository;
    }

    public function load()
    {
        $this->userRepository->createStructure();
        $this->topicRepository->createStructure();
        $this->badgeRepository->createStructure();

        $users = [
            ['name' => 'Rafael Dohms'],
            ['name' => 'Speaker'],
            ['name' => 'Attendee'],
            ['name' => 'Organizer'],
        ];

        $topics = [
            [
                'title'             => 'A journey into your Lizard Brain',
                'excerpt'           => 'Something about a lizard brain',
                'details'           => 'I know nothing about lizards, nor brains, so this looks like a very interesting topic',
                'creator'           => 1,
                'ownedByCreator'    => true,
                'createdAt'         => '2015-08-26 15:05:37',
            ],
        ];

        $votes = [
            [
                'topic'     => 1,
                'voter'     => 3,
                'createdAt' => '2015-08-26 16:01:32'
            ],
        ];

        $comments = [
            [
                'topic'     => 1,
                'commenter' => 3,
                'content'   => 'Looks like a great topic! Go for it!',
                'createdAt' => '2015-08-26 16:01:22',
            ],
        ];

        $badges = [
            [
                'name' => 'Super idea',
                'icon' => 'super-idea-badge.png',
            ],
        ];

        $earnedBadges = [
            [
                'user' => 1,
                'badge' => 1,
                'created_at' => '2015-08-26 16:01:22',
            ],
        ];

        foreach ($users as $user) {
            $this->insertUser($user);
        }

        foreach ($topics as $topic) {
            $this->insertTopic($topic);
        }

        foreach ($votes as $vote) {
            $this->insertVote($vote);
        }

        foreach ($comments as $comment) {
            $this->insertComment($comment);
        }

        foreach ($badges as $badge) {
            $this->insertBadge($badge);
        }

        foreach ($earnedBadges as $earnedBadge) {
            $this->insertEarnedBadge($earnedBadge);
        }

    }

    private function insertUser(array $entity)
    {
        $connection = $this->userRepository->getConnection();

        $insert = "INSERT INTO user (name)
                VALUES (:name)";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':name', $entity['name']);

        $stmt->execute();
    }

    private function insertTopic(array $entity)
    {
        $connection = $this->topicRepository->getConnection();

        $insert = "INSERT INTO topic (title, excerpt, details, creator, owned_by_creator, created_at)
                VALUES (:title, :excerpt, :details, :creator, :ownedByCreator, :createdAt)";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':title', $entity['title']);
        $stmt->bindParam(':excerpt', $entity['excerpt']);
        $stmt->bindParam(':details', $entity['details']);
        $stmt->bindParam(':creator', $entity['creator']);
        $stmt->bindParam(':ownedByCreator', $entity['ownedByCreator']);
        $stmt->bindParam(':createdAt', $entity['createdAt']);
        $stmt->bindParam(':title', $entity['title']);

        $stmt->execute();
    }

    private function insertVote(array $entity)
    {
        $connection = $this->topicRepository->getConnection();

        $insert = "INSERT INTO vote (topic, voter, created_at)
                VALUES (:topic, :voter, :createdAt)";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':topic', $entity['topic']);
        $stmt->bindParam(':voter', $entity['voter']);
        $stmt->bindParam(':createdAt', $entity['createdAt']);

        $stmt->execute();
    }

    private function insertComment(array $entity)
    {
        $connection = $this->topicRepository->getConnection();

        $insert = "INSERT INTO comment (topic, commenter, content, created_at)
                VALUES (:topic, :commenter, :content, :createdAt)";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':topic', $entity['topic']);
        $stmt->bindParam(':commenter', $entity['commenter']);
        $stmt->bindParam(':content', $entity['content']);
        $stmt->bindParam(':createdAt', $entity['createdAt']);

        $stmt->execute();
    }

    private function insertBadge(array $entity)
    {
        $connection = $this->badgeRepository->getConnection();

        $insert = "INSERT INTO badge (name, icon)
                VALUES (:name, :icon)";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':name', $entity['name']);
        $stmt->bindParam(':icon', $entity['icon']);

        $stmt->execute();
    }


    private function insertEarnedBadge(array $entity)
    {
        $connection = $this->badgeRepository->getConnection();

        $insert = "INSERT INTO earned_badge (user, badge, created_at)
                VALUES (:user, :badge, :created_at)";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':user', $entity['user']);
        $stmt->bindParam(':badge', $entity['badge']);
        $stmt->bindParam(':created_at', $entity['created_at']);

        $stmt->execute();
    }
}