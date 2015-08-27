<?php

namespace Wecamp\TalkBack\Controller;


use Wecamp\TalkBack\Repository\BadgeRepository;

class UserController extends AbstractController
{
    /**
     * $var BadgeRepository
     */
    private $badgeRepository;

    public function __construct(BadgeRepository $badgeRepository)
    {
        $this->badgeRepository= $badgeRepository;
    }

    public function getEarnedBadges()
    {

    }


}