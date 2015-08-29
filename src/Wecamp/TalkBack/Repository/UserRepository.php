<?php

namespace Wecamp\TalkBack\Repository;

class UserRepository extends BaseRepository
{
    /**
     * Creates the table structure for users.
     *
     * @return void
     */
    public function createStructure()
    {
        $connection = $this->getConnection();

        $connection->exec("CREATE TABLE IF NOT EXISTS user (
                    id INTEGER PRIMARY KEY,
                    name TEXT)");
    }

    public function checkUserForVoteAndComment($userId, $topicId)
    {
        $connection = $this->getConnection();
        $insert =  '
            SELECT id
            FROM vote
            WHERE
                topic = :topic_id AND
                user = :user_id
            LEFT JOIN comment on comment.commenter = :user_id
        ';
        $stmt = $connection->prepare($insert);
        $stmt->bindParam(':topic_id', $topicId);
        $stmt->bindParam(':user_id', $userId);
        try {
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            //todo: log this!
            return [];
        }
    }
}