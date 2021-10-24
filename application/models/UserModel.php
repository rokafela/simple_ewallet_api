<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model
{
    public function insertUser($new_user)
    {
        $this->db->trans_begin();

        try {
            $this->db->insert('public.t_mtr_user', $new_user);
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                return 'success';
            }
            else {
                $this->db->trans_rollback();
                return 'failed';
            }
        }
        catch (Exception $e) {
            $error_message = (string) $e->getMessage();
            $is_duplicate = strpos($error_message, 'duplicate');
            $this->db->trans_rollback();
            if ($is_duplicate === false) {
                return 'failed';
            }
            else {
                return 'duplicate';
            }
        }
    }
}