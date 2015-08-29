<?php

namespace Wecamp\TalkBack\Badge;

use Wecamp\TalkBack\Event\TopicCommentAddedEvent;
use Wecamp\TalkBack\Repository\BadgeRepository;
use Wecamp\TalkBack\Repository\UserRepository;

class SwingBackBadge implements Badge
{
    private $badgeRepository;
    private $userRepository;

    public function __construct(BadgeRepository $badgeRepository, UserRepository $userRepository)
    {
        $this->badgeRepository = $badgeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'topic.comment' => 'calculate',
            'topic.vote' => 'calculate',
        );
    }

    /**
     * @param TopicCommentAddedEvent $event
     */
    public function calculate($event)
    {
        $badge = $this->badgeRepository->findOneBadgeByName('Swing back!');
        if (count($this->badgeRepository->findEarnedBadgeByBadgeAndUser($event->getUser(), $badge['id'])) > 0) {
            return;
        }
echo '<pre>';
var_dump($this->userRepository->checkUserForVoteAndComment($event->getUser(), $event->getTopic()));
die('</pre>');
        if (count($this->userRepository->checkUserForVoteAndComment($event->getUser(), $event->getTopic())) < 1) {
            return;
        }

        $this->badgeRepository->earnBadge($event->getUser(), $badge['id'], new \DateTime());
    }
}