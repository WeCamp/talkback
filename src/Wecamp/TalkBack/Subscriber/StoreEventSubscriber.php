<?php

namespace Wecamp\TalkBack\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Wecamp\TalkBack\Event\UserTriggeredEvent;
use Wecamp\TalkBack\Repository\BadgeRepository;

class StoreEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var BadgeRepository
     */
    private $repository;

    /**
     * @param BadgeRepository $repository
     */
    public function __construct(BadgeRepository $repository)
    {
        $this->repository = $repository;
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
            'topic.add'     => ['storeEvent', 9999],
            'topic.vote'    => ['storeEvent', 9999],
            'topic.comment' => ['storeEvent', 9999],
        );
    }

    public function storeEvent(UserTriggeredEvent $event)
    {
        $this->repository->addEvent(
            $event->getName(),
            $event->getUser(),
            new \DateTime()
        );
    }
}