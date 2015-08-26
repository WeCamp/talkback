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
}