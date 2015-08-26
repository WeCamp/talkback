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
}