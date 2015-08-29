<?php

namespace Wecamp\TalkBack\Event;

class TopicCommentAddedEvent extends UserTriggeredEvent
{
    /**
     * @param int $user
     * @param int $topic
     */
    public function __construct($user, $topic)
    {
        $this->user = $user;
        $this->topic = $topic;
    }

    /**
     * @return int
     */
    public function getTopic()
    {
        return $this->topic;
    }
}