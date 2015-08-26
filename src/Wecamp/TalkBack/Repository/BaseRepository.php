<?php

namespace Wecamp\TalkBack\Repository;

use PDO;

class BaseRepository
{
    /**
     * Returns a connection to the database
     *
     * @return PDO
     */
    public function getConnection()
    {
        $connection = new PDO('sqlite:talkback.sqlite3');
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }
}