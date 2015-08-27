<?php

namespace Wecamp\TalkBack\Repository;

use Symfony\Component\Validator\Constraints\DateTime;

class BadgeRepository extends BaseRepository
{
    /**
     * Creates the table structure for badges.
     *
     * @return void
     */
    public function createStructure()
    {
        $connection = $this->getConnection();

        $connection->exec("CREATE TABLE IF NOT EXISTS badge (
                    id INTEGER PRIMARY KEY,
                    name TEXT,
                    icon TEXT)");

        $connection->exec("CREATE TABLE IF NOT EXISTS earned_badge (
                    user INTEGER,
                    badge INTEGER,
                    created_at TEXT,
                    PRIMARY KEY (user, badge))");

        $connection->exec("CREATE TABLE IF NOT EXISTS event (
                    id INTEGER PRIMARY KEY,
                    name TEXT,
                    user INTEGER,
                    created_at TEXT)");
    }

    /**
     * @param string $name
     * @param int $user
     * @param \DateTime $createdAt
     */
    public function addEvent($name, $user, \DateTime $createdAt)
    {
        $connection = $this->getConnection();

        $createdAt = $createdAt->format('Y-m-d H:i:s');

        $insert = "INSERT INTO event (name, user, created_at)
                VALUES (:name, :user, :createdAt)";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':createdAt', $createdAt);

        try {
            $stmt->execute();
        }catch(\PDOException $e) {
            //todo: log this!
            var_dump($e);
        }
    }

    /**
     * @param string $eventName
     *
     * @return array
     */
    public function findOneBadgeByName($badgeName)
    {
        $connection = $this->getConnection();
        $insert =  "SELECT * FROM badge WHERE name = :name";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':name', $badgeName);

        try{
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            //todo: log this!
            return [];
        }
    }

    /**
     * @param int $user
     * @param string $eventName
     *
     * @return array
     */
    public function findEventsByUserAndEventName($user, $eventName)
    {
        $connection = $this->getConnection();
        $insert =  "SELECT * FROM event WHERE user = :user AND name = :name";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':name', $eventName);

        try{
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            //todo: log this!
            return [];
        }
    }

    /**
     * Let a user earn a badge.
     *
     * @param int $userIdentifier
     * @param int $badge
     * @param \DateTime $createdAt
     *
     * @return void
     */
    public function earnBadge($userIdentifier, $badge, \DateTime $createdAt)
    {
        $connection = $this->getConnection();

        $createdAt = $createdAt->format('Y-m-d H:i:s');

        $insert = "INSERT INTO earned_badge (user, badge, created_at)
                VALUES (:user, :badge, :createdAt)";
        $stmt = $connection->prepare($insert);

        $stmt->bindParam(':user', $userIdentifier);
        $stmt->bindParam(':badge', $badge);
        $stmt->bindParam(':createdAt', $createdAt);

        try {
            $stmt->execute();
        }catch(\PDOException $e) {
            //todo: log this!
            var_dump($e);
        }
    }
}