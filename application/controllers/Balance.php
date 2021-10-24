<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Balance extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('BalanceModel');
    }

    public function balance_read_get()
    {
        $headers = apache_request_headers();
        $token_data = validateToken($headers);

        $balance_data = $this->BalanceModel->getUserBalance($token_data->usr);
        if ($balance_data === false) $this->response(null, 500);
        if ($balance_data === null) $this->response(null, 404);

        $res = array(
            "balance" => (int) $balance_data['balance']
        );
        $this->response($res, 200);
    }

    public function balance_topup_post()
    {
        $headers = apache_request_headers();
        $token_data = validateToken($headers);

        $body = $this->post();
        $this->form_validation->set_data($body);
        $this->form_validation->set_rules('amount', 'amount', 'required|integer|greater_than[0]|less_than[10000000]');
        if ($this->form_validation->run() == FALSE) $this->response(null, 400);

        $username = $token_data->usr;
        $amount = $body['amount'];

        $balance_data = $this->BalanceModel->getUserBalance($username);
        if ($balance_data === false) $this->response(null, 500);
        if ($balance_data === null) $this->response(null, 404);

        $before_balance = (int) $balance_data['balance'];
        $after_balance = $before_balance + $amount;
        $trx = array(
            'transaction_type'  => 'credit',
            'username'          => $username,
            'amount'            => $amount,
            'before_balance'    => $before_balance,
            'after_balance'     => $after_balance,
            'transaction_time'  => date('Y-m-d H:i:s')
        );

        $process_topup = $this->BalanceModel->saveBalanceTopup($username, $after_balance, $trx);
        if (!$process_topup) $this->response(null, 500);
        else $this->response(null, 204);
    }

    public function balance_transfer_post()
    {
        $headers = apache_request_headers();
        $token_data = validateToken($headers);

        $body = $this->post();
        $this->form_validation->set_data($body);
        $this->form_validation->set_rules('to_username', 'to_username', 'required|trim');
        $this->form_validation->set_rules('amount', 'amount', 'required|integer|greater_than[0]');
        if ($this->form_validation->run() == FALSE) $this->response(null, 400);
        if (!is_string($body['to_username'])) $this->response(null, 400);

        $sender_username = $token_data->usr;
        $receiver_username = trim($body['to_username']);
        $amount = $body['amount'];

        $receiver_balance = $this->BalanceModel->getUserBalance($receiver_username);
        if ($receiver_balance === false) $this->response(null, 500);
        if ($receiver_balance === null) $this->response(null, 404);

        $sender_balance = $this->BalanceModel->getUserBalance($sender_username);
        if ($sender_balance === false) $this->response(null, 500);
        if ($sender_balance === null) $this->response(null, 404);

        $receiver_balance = (int) $receiver_balance['balance'];
        $sender_balance = (int) $sender_balance['balance'];
        if ($sender_balance < $amount) $this->response(null, 400);
        $receiver_after_balance = $receiver_balance + $amount;
        $sender_after_balance = $sender_balance - $amount;

        $debit_sender = array(
            'transaction_type'          => 'debit',
            'amount'                    => $amount,
            'sender_username'           => $sender_username,
            'sender_before_balance'     => $sender_balance,
            'sender_after_balance'      => $sender_after_balance,
            'receiver_username'         => $receiver_username,
            'receiver_before_balance'   => $receiver_balance,
            'receiver_after_balance'    => $receiver_after_balance,
            'transaction_time'          => date('Y-m-d H:i:s')
        );

        $update_sender = array(
            'username'  => $sender_username,
            'balance'   => $sender_after_balance
        );

        $credit_receiver = array(
            'transaction_type'  => 'credit',
            'username'          => $receiver_username,
            'amount'            => $amount,
            'before_balance'    => $receiver_balance,
            'after_balance'     => $receiver_after_balance,
            'transaction_time'  => date('Y-m-d H:i:s')
        );

        $update_receiver = array(
            'username'  => $receiver_username,
            'balance'   => $receiver_after_balance
        );

        $process_transfer = $this->BalanceModel->saveBalanceTransfer($debit_sender, $credit_receiver, $update_sender, $update_receiver);
        if (!$process_transfer) $this->response(null, 500);
        else $this->response(null, 204);
    }
}
