<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceModel extends CI_Model
{
    public function getUserBalance($username)
    {
        try {
            $this->db->select('balance');
            $this->db->where('username', $username);
            $data = $this->db->get('public.t_mtr_user');
            return $data->row_array();
        }
        catch (Exception $e) {
            $error_message = (string) $e->getMessage();
            return false;
        }
    }

    public function saveBalanceTopup($username, $after_balance, $trx)
    {
        $this->db->trans_begin();
        try {
            $this->db->insert('public.t_trx_topup', $trx);

            $this->db->set('balance', $after_balance);
            $this->db->where('username', $username);
            $this->db->update('public.t_mtr_user');

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                return true;
            }
            else {
                $this->db->trans_rollback();
                return false;
            }
        }
        catch (Exception $e) {
            $error_message = (string) $e->getMessage();
            $this->db->trans_rollback();
            return false;
        }
    }

    public function saveBalanceTransfer($debit_sender, $credit_receiver, $update_sender, $update_receiver)
    {
        $this->db->trans_begin();
        try {
            $this->db->insert('public.t_trx_topup', $credit_receiver);
            $this->db->insert('public.t_trx_transfer', $debit_sender);

            $this->db->set('balance', $update_sender['balance']);
            $this->db->where('username', $update_sender['username']);
            $this->db->update('public.t_mtr_user');

            $this->db->set('balance', $update_receiver['balance']);
            $this->db->where('username', $update_receiver['username']);
            $this->db->update('public.t_mtr_user');

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                return true;
            }
            else {
                $this->db->trans_rollback();
                return false;
            }
        }
        catch (Exception $e) {
            $error_message = (string) $e->getMessage();
            $this->db->trans_rollback();
            return false;
        }
    }
}