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
     * @param $data
     * @return bool|string
     */
    public function createTopic($data)
    {
        $createdAt = new \DateTime();
        $connection = $this->getConnection();
        $format = $createdAt->format('Y-m-d H:i:s');
        $tempUser = 1;

        $insert = "INSERT INTO topic (title, excerpt, details,creator, owned_by_creator, created_at)
                VALUES (:title, :excerpt, :details, :creator, :ownedByCreator, :createdAt)";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':details', $data['details']);
        $stmt->bindParam(':excerpt', $data['excerpt']);
        $stmt->bindParam(':creator', $tempUser);
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

        $insert = "SELECT topic.title, topic.excerpt, topic.details, topic.owned_by_creator, topic.created_at,
        user.name as creator_name, count(vote.voter) as vote_count FROM topic LEFT JOIN vote on topic.id = vote.topic
        LEFT JOIN user on user.id = topic.creator WHERE topic.id=:id GROUP BY topic.id";

        $stmt = $connection->prepare("SELECT topic.title, topic.excerpt, topic.details, topic.owned_by_creator, topic.created_at,
        user.name as creator_name, count(vote.voter) as vote_count FROM topic LEFT JOIN vote on topic.id = vote.topic
        LEFT JOIN user on user.id = topic.creator WHERE topic.id=10 GROUP BY topic.id");
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



}