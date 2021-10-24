<?php
class FTopUserCest
{
    private $token = '';

    public function _before(ApiTester $I)
    {
        $fh = fopen('token.txt', 'r');
        $this->token = fread($fh, filesize('token.txt'));
        fclose($fh);
    }

    public function topUsersWithoutToken(ApiTester $I)
    {
        $I->sendGet('/top_users');
        $I->seeResponseCodeIs(401);
    }

    public function topUsersWithInvalidToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('a'.$this->token);
        $I->sendGet('/top_users');
        $I->seeResponseCodeIs(401);
    }

    public function topUsersSuccess(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendGet('/top_users');
        $I->seeResponseCodeIs(200);
        $I->haveHttpHeader('content-type', 'application/json');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username'  => 'string',
            'transacted_value'    => 'integer'
        ]);
    }
}