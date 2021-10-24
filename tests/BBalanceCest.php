<?php
class BBalanceCest
{
    private $token = '';

    public function _before(ApiTester $I)
    {
        $fh = fopen('token.txt', 'r');
        $this->token = fread($fh, filesize('token.txt'));
        fclose($fh);
    }

    public function balanceReadWithoutToken(ApiTester $I)
    {
        $I->sendGet('/balance_read');
        $I->seeResponseCodeIs(401);
    }

    public function balanceReadWithInvalidToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('a'.$this->token);
        $I->sendGet('/balance_read');
        $I->seeResponseCodeIs(401);
    }

    public function balanceReadSuccess(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendGet('/balance_read');
        $I->seeResponseCodeIs(200);
        $I->haveHttpHeader('content-type', 'application/json');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'balance' => 'integer'
        ]);
    }
}