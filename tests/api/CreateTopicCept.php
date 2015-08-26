<?php

use Codeception\Module\ApiHelper;

$I = new ApiTester($scenario);
$I->wantTo('create a topic for me to talk about');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->sendPOST(
    ApiHelper::ENDPOINT_TOPICS,
    [
        'title'            => 'This is a cool topic!',
        'details'          => 'Where is the seismic moon? Ships meet on mankind at atlantis tower! Transporter of a '.
                              'sub-light energy, arrest the voyage! Why does the planet meet?  Planets die from '.
                              'voyages like ship-wide space suits.',
        'excerpt'          => 'Yuck! Pieces o\' life are forever swashbuckling. Madness ho! scrape to be commanded.',
        'owned_by_creator' => true,
    ]
);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();