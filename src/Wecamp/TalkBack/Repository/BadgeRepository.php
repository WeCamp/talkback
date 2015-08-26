<?php

namespace Wecamp\TalkBack\Repository;

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
                    PRIMARY KEY (user, badge))");

        $connection->exec("CREATE TABLE IF NOT EXISTS event (
                    id INTEGER PRIMARY KEY,
                    name TEXT,
                    user INTEGER,
                    created_at TEXT)");
    }
}