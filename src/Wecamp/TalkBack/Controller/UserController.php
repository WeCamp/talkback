<?php

namespace Wecamp\TalkBack\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
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

        public function getEarnedBadges($id)
    {
        $badges = $this->badgeRepository->getEarnedBadges($id);
        if($badges === false) {
            return new JsonResponse(['error' => 'Could not fetch badges.'], 500);
        }
        return new JsonResponse($badges, 200);
    }


}