<?php

namespace Wecamp\TalkBack\Badge;

use Wecamp\TalkBack\Event\TopicAddedEvent;

class SuperIdeaBadge implements Badge
{
    private $repository;

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
            'topic.add'=> 'calculate',
        );
    }

    /**
     * @param TopicAddedEvent $event
     */
    public function calculate($event)
    {
        echo '<pre>';
        var_dump($this->repository->findEventsByUserAndEventName($event->getUser(), '1'));
        die('</pre>');
        if (count($this->repository->findEventsByUserAndEventName($event->getUser(), '1')) > 0) {

        }
    }
}