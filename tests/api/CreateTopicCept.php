<?php

use Codeception\Module\ApiHelper;

$testPostData     = [
    'title'            => 'This is a cool topic!',
    'details'          => 'Where is the seismic moon? Ships meet on mankind at atlantis tower! Transporter of a '.
                          'sub-light energy, arrest the voyage! Why does the planet meet?  Planets die from '.
                          'voyages like ship-wide space suits.',
    'excerpt'          => 'Yuck! Pieces o\' life are forever swashbuckling. Madness ho! scrape to be commanded.',
    'owned_by_creator' => true,
];


$I = new ApiTester($scenario);
$I->wantTo('create a topic for me to talk about');

// Headers
$I->haveHttpHeader('Content-Type', 'application/json');

// Submit
$I->sendPOST(ApiHelper::ENDPOINT_TOPICS, $testPostData);

// Response
$I->seeResponseCodeIs(201);
$I->seeResponseIsJson();
$jsonResponse = $I->grabDataFromResponseByJsonPath('$');
$I->canSeeResponseJsonMatchesJsonPath('$.id');
$I->assertEquals($testPostData['details'], $I->grabDataFromResponseByJsonPath('$.details'));
$I->assertEquals($testPostData['excerpt'], $I->grabDataFromResponseByJsonPath('$.excerpt'));
$I->assertEquals($testPostData['owned_by_creator'], $I->grabDataFromResponseByJsonPath('$.owned_by_creator'));
$I->canSeeResponseJsonMatchesJsonPath('$.created_at');

// Db record