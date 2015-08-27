<?php

namespace Wecamp\TalkBack\Event;

use Symfony\Component\EventDispatcher\Event;

class UserTriggeredEvent extends Event
{
    /**
     * @var int
     */
    protected $user;

    /**
     * @param int $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }
}