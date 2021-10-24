<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportModel extends CI_Model
{
    public function getUserTopTransaction($username)
    {
        $sql = "
            select transaction_type, username, amount
            from public.t_trx_topup
            where username = '{$username}'
            union all
            select transaction_type, sender_username as username, amount
            from public.t_trx_transfer
            where sender_username = '{$username}'
            order by amount desc
            limit 10;
        ";
        try {
            $data = $this->db->query($sql);
            return $data->result_array();
        }
        catch (Exception $e) {
            $error_message = (string) $e->getMessage();
            return false;
        }
    }

    public function getTopUsers()
    {
        $sql = "
            select sender_username as username, sum(amount) as transacted_value
            from public.t_trx_transfer
            group by sender_username
            order by sum(amount) desc
            limit 10;
        ";
        try {
            $data = $this->db->query($sql);
            return $data->result_array();
        }
        catch (Exception $e) {
            $error_message = (string) $e->getMessage();
            return false;
        }
    }
}