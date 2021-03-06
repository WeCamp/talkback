<?php

namespace Wecamp\TalkBack\Repository;

final class TopicRepository extends BaseRepository
{
    /**
     * Creates the table structure for topics.
     *
     * @return void
     */
    public function createStructure()
    {
        $connection = $this->getConnection();

        $connection->exec("CREATE TABLE IF NOT EXISTS topic (
                    id INTEGER PRIMARY KEY,
                    title VARCHAR(120),
                    excerpt VARCHAR(255),
                    details TEXT,
                    creator INTEGER,
                    owned_by_creator BOOL,
                    created_at TEXT)");

        $connection->exec("CREATE TABLE IF NOT EXISTS vote (
                    topic INTEGER,
                    voter INTEGER,
                    created_at TEXT,
                    PRIMARY KEY (topic, voter))");

        $connection->exec("CREATE TABLE IF NOT EXISTS comment (
                    id INTEGER PRIMARY KEY,
                    topic INTEGER,
                    commenter INTEGER,
                    content TEXT,
                    created_at TEXT)");
    }

    /**
     * @param array $data
     * @param int $user
     *
     * @return bool|string
     */
    public function createTopic(array $data, $user)
    {
        $createdAt = new \DateTime();
        $connection = $this->getConnection();
        $format = $createdAt->format('Y-m-d H:i:s');

        $insert = "INSERT INTO topic (title, excerpt, details,creator, owned_by_creator, created_at)
                VALUES (:title, :excerpt, :details, :creator, :ownedByCreator, :createdAt)";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':details', $data['details']);
        $stmt->bindParam(':excerpt', $data['excerpt']);
        $stmt->bindParam(':creator', $user);
        $stmt->bindParam(':ownedByCreator', $data['owned_by_creator']);
        $stmt->bindParam(':createdAt', $format);

        try {
            $stmt->execute();
            return $connection->lastInsertId();
        }catch(\PDOException $e) {
            //todo: log this!
            return false;
        }
    }

    /**
     * @return array|bool
     */
    public function getAllTopics()
    {
        $connection = $this->getConnection();
        $insert =  "SELECT topic.id, topic.title, topic.excerpt, topic.owned_by_creator, topic.created_at,
            user.name as creator_name, count(vote.voter) as vote_count FROM topic LEFT JOIN vote on topic.id = vote.topic
            LEFT JOIN user on user.id = topic.creator GROUP BY topic.id";
        $stmt = $connection->prepare($insert);

        try{
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            //todo: log this!
            return false;
        }
    }

    /**
     * @param $id
     * @return false|array
     */
    public function getTopicByIdentifier($id)
    {
        $connection = $this->getConnection();

            $insert = "SELECT topic.id, topic.title, topic.excerpt, topic.details, topic.owned_by_creator, topic.created_at,
            user.name as creator_name, count(vote.voter) as vote_count FROM topic LEFT JOIN vote on topic.id = vote.topic
            LEFT JOIN user on user.id = topic.creator  WHERE topic.id=:id GROUP BY topic.id";

            $stmt = $connection->prepare($insert);
            $stmt->bindParam(':id', $id);
            try {
                $stmt->execute();
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            }catch(\PDOException $e) {
                //todo: log this!
                return false;
            }
    }

    /**
     * @param $topicIdentifier
     * @return array|bool
     */
    public function getCommentsForTopic($topicIdentifier)
    {
        $connection = $this->getConnection();
        $insert = "SELECT comment.id, comment.topic, comment.content, comment.created_at, user.name FROM comment
        LEFT JOIN user on comment.commenter = user.id WHERE topic=:topicid";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':topicid', $topicIdentifier);

        try {
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e) {
            //todo: log this!
            return false;
        }
    }

    /**
     * @param array $data
     * @param int $user
     *
     * @return bool|string
     */
    public function createComment(array $data, $user)
    {
        $createdAt = new \DateTime();
        $connection = $this->getConnection();
        $format = $createdAt->format('Y-m-d H:i:s');

        $insert = "INSERT INTO comment (topic, commenter, content, created_at)
                VALUES (:topic, :commenter, :content, :created_at)";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':topic', $data['topic']);
        $stmt->bindParam(':commenter', $user);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':created_at', $format);

        try {
            $stmt->execute();
            return $connection->lastInsertId();
        }catch(\PDOException $e) {
            //todo: log this!
            return false;
        }
    }

    /**
     * @param $id
     * @return false|array
     */
    public function getCommentByIdentifier($id)
    {
        $connection = $this->getConnection();

        $insert = "SELECT comment.id, comment.topic, comment.content, comment.created_at, user.name as commenter
        FROM comment LEFT JOIN user on user.id = comment.commenter WHERE comment.id=:id";
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e) {
            //todo: log this!
            return false;
        }
    }

    /**
     * @param int $topic
     * @param int $user
     * @param \DateTime $createdAt
     *
     * @return bool
     */
    public function addVote($topic, $user, \DateTime $createdAt)
    {
        $connection = $this->getConnection();
        $format = $createdAt->format('Y-m-d H:i:s');

        $insert = "INSERT INTO vote (topic, voter, created_at)
                VALUES (:topic, :voter, :created_at)";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':voter', $user);
        $stmt->bindParam(':created_at', $format);

        try {
            $stmt->execute();
            return true;
        }catch(\PDOException $e) {
            //todo: log this!
            return false;
        }
    }


    public function getAllDetailedTopics()
    {
        $connection = $this->getConnection();
        $insert =  "SELECT topic.id, topic.details, topic.title, topic.excerpt, topic.owned_by_creator, topic.created_at,
            user.name as creator_name, count(vote.voter) as vote_count FROM topic LEFT JOIN vote on topic.id = vote.topic
            LEFT JOIN user on user.id = topic.creator GROUP BY topic.id";
        $stmt = $connection->prepare($insert);

        try{
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            //todo: log this!
            return false;
        }
    }
}