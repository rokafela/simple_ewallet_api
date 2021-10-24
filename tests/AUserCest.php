<?php

class AUserCest
{
    public function _before(ApiTester $I)
    {
        $I->sendPost(
            '/create_user',
            [
                'username' => 'foo'
            ]
        );
    }

    // tests
    public function createUserWithoutBody(ApiTester $I)
    {
        $I->sendPost('/create_user');
        $I->seeResponseCodeIs(400);
    }

    public function createUserAlreadyExist(ApiTester $I)
    {
        $I->sendPost(
            '/create_user',
            [
                'username' => 'foo'
            ]
        );
        $I->seeResponseCodeIs(409);
    }

    public function createUserSuccess(ApiTester $I)
    {
        $random_number = rand(1,1000);
        $I->sendPost(
            '/create_user',
            ['username' => 'foo'.$random_number]
        );
        $I->seeResponseCodeIs(201);
        $I->haveHttpHeader('content-type', 'application/json');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'token' => 'string'
        ]);

        list($token) = $I->grabDataFromResponseByJsonPath('$.token');
        $fh = fopen('token.txt', 'w');
        fwrite($fh, $token);
        fclose($fh);
    }
}
