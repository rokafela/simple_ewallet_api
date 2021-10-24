<?php
class DTransferCest
{
    private $token = '';

    public function _before(ApiTester $I)
    {
        $fh = fopen('token.txt', 'r');
        $this->token = fread($fh, filesize('token.txt'));
        fclose($fh);
    }

    public function transferWithoutToken(ApiTester $I)
    {
        $I->sendPost('/transfer');
        $I->seeResponseCodeIs(401);
    }

    public function transferWithInvalidToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('a'.$this->token);
        $I->sendPost('/transfer');
        $I->seeResponseCodeIs(401);
    }

    public function transferWithoutBody(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost('/transfer');
        $I->seeResponseCodeIs(400);
    }

    public function transferWithNegativeAmount(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/transfer',
            [
                'to_username'   => 'foo',
                'amount'        => -1
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    public function transferWithTooBigAmount(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/transfer',
            [
                'to_username'   => 'foo',
                'amount'        => 1234
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    public function transferWithNonNumericString(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/transfer',
            [
                'to_username'   => 'foo',
                'amount'        => 'bar'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    public function transferWithReceiverUsernameNotFound(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/transfer',
            [
                'to_username'   => 'bar',
                'amount'        => 12
            ]
        );
        $I->seeResponseCodeIs(404);
    }

    public function transferSuccess(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->token);
        $I->sendPost(
            '/transfer',
            [
                'to_username'   => 'foo',
                'amount'        => 12
            ]
        );
        $I->seeResponseCodeIs(204);
    }
}