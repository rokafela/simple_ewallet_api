<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Report extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('ReportModel');
    }

    public function top_trx_per_user_get()
    {
        $headers = apache_request_headers();
        $token_data = validateToken($headers);

        $top_trx_report = $this->ReportModel->getUserTopTransaction($token_data->usr);
        if ($top_trx_report === false) $this->response(null, 500);

        for ($i=0; $i < count($top_trx_report); $i++) {
            if ($top_trx_report[$i]['transaction_type'] == 'debit') $top_trx_report[$i]['amount'] = '-'.$top_trx_report[$i]['amount'];
            $top_trx_report[$i]['amount'] = (int) $top_trx_report[$i]['amount'];
            unset($top_trx_report[$i]['transaction_type']);
        }

        $this->response($top_trx_report, 200);
    }

    public function top_users_get()
    {
        $headers = apache_request_headers();
        $token_data = validateToken($headers);

        $top_users_report = $this->ReportModel->getTopUsers();
        if ($top_users_report === false) $this->response(null, 500);

        for ($i=0; $i < count($top_users_report); $i++) {
            $top_users_report[$i]['transacted_value'] = (int) $top_users_report[$i]['transacted_value'];
        }

        $this->response($top_users_report, 200);
    }
}
