<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Socket_controler extends AdminController
{
    public function login_socket()
    {

        $user_id = $this->input->post('user_id');
        $user_name = $this->input->post('user_name');
        if ($user_id && $user_name) {
            $result = ConnectSocket($user_id, $user_name);
            if (isset($result) && !empty($result)) {
                $data = [
                    'status' => true,
                    'message' => 'Login successful',
                    'data' => $result
                ];
            } else {
                $data = [
                    'status' => false,
                    'message' => 'Login failed',
                    'data' => null
                ];
            }
            echo json_encode($data);
            die();
        } else {
            $data = [
                'status' => false,
                'message' => 'Invalid input data',
                'data' => null
            ];
            echo json_encode($data);
        }
    }
}
