<?php
class CTopupCest
{
    private $token = '';

    public function _before(ApiTester $I)
    {
        $fh = fopen('token.txt', 'r');
        $this->token = fread($fh, filesize('token.txt'));
        fclose($fh);
    }

    public function topupWithoutToken(ApiTester $I)
    {
        $I->sendPost('/balance_topup');
        $I->seeResponseCodeIs(401);
    }

    public function topupWithInvalidToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('a'.$this->token);
        $I->sendPost('/balance_topup');
        $I->seeResponseCodeIs(401);
    }

    public function topupWithoutBody(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost('/balance_topup');
        $I->seeResponseCodeIs(400);
    }

    public function topupWithNegativeAmount(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/balance_topup',
            ['amount' => -1]
        );
        $I->seeResponseCodeIs(400);
    }

    public function topupWithTooBigAmount(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/balance_topup',
            ['amount' => 10000000]
        );
        $I->seeResponseCodeIs(400);
    }

    public function topupWithNonNumericString(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/balance_topup',
            ['amount' => "foo"]
        );
        $I->seeResponseCodeIs(400);
    }

    public function topupSuccess(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/balance_topup',
            ['amount' => 123]
        );
        $I->seeResponseCodeIs(204);
    }
}