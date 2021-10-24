<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class User extends RestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function create_user_post()
    {
        $body = $this->post();
        $this->form_validation->set_data($body);
        $this->form_validation->set_rules('username', 'username', 'required|trim');
        if ($this->form_validation->run() === false) $this->response(null, 400);
        if (!is_string($body['username'])) $this->response(null, 400);

        $username = trim($body['username']);
        $token = generateToken($username);
        $new_user = [
            'username'      => $username,
            'create_date'   => date('Y-m-d'),
            'token'         => $token
        ];

        $save_user_result = $this->UserModel->insertUser($new_user);
        if ($save_user_result == 'failed') $this->response(null, 500);
        if ($save_user_result == 'duplicate') $this->response(null, 409);

        $res = array(
            'token' => $token
        );
        $this->response($res, 201);
    }
}
