<?php
class ETopTransactionCest
{
    private $token = '';

    public function _before(ApiTester $I)
    {
        $fh = fopen('token.txt', 'r');
        $this->token = fread($fh, filesize('token.txt'));
        fclose($fh);
    }

    public function topTransactionsWithoutToken(ApiTester $I)
    {
        $I->sendGet('/top_transactions_per_user');
        $I->seeResponseCodeIs(401);
    }

    public function topTransactionsWithInvalidToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('a'.$this->token);
        $I->sendGet('/top_transactions_per_user');
        $I->seeResponseCodeIs(401);
    }

    public function topTransactionsSuccess(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendGet('/top_transactions_per_user');
        $I->seeResponseCodeIs(200);
        $I->haveHttpHeader('content-type', 'application/json');
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'username'  => 'string',
            'amount'    => 'integer'
        ]);
    }
}