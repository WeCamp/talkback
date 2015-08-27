<?php

namespace Wecamp\TalkBack\Badge;

use \Symfony\Component\EventDispatcher\EventSubscriberInterface;

interface Badge extends EventSubscriberInterface
{
    public function calculate($event);
}