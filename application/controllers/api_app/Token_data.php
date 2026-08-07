<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Token_data extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_chat');   
    }
    //Phần login
    public function getToKen()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $playerid = $this->input->post('playerid');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');

            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $username = $data_post['username'];
                    $password = $data_post['password'];
                    $playerid = $data_post['playerid'];
                }
            }
        }
        $token = false;
        if (!empty($username) && !empty($password)) {
            $this->db->where('email', $username);
            $user = $this->db->get('tblstaff')->row();
            // $this->load->helper('phpass');
            // $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            // if ($hasher->CheckPassword($password, $user->password)) {
            if (app_hasher()->CheckPassword($password, $user->password)) {
                log_activity('Non Existing User Tried to Login [Email: ' . $username . ', Is Staff Member: Yes), IP: ' . $this->input->ip_address() . ']');
                $dateNow = date('Y-m-d H:i:s');
                $dateNow = new DateTime($dateNow);
                @$dateEndToken = date("Y-m-d H:i:s", strtotime("$dateNow +3 days"));
                $token =  base64_encode($user->password) . '---' . base64_encode($user->staffid . '---' . $dateEndToken);
                $tokenAccount =  $this->encryption->encrypt(base64_encode($token));
                $url = base_url('assets/images/user-placeholder.jpg');
                if ($user && $user->profile_image !== null) {
                    $profile_image = base_url('uploads/staff_profile_images/' . $user->staffid . '/small_' . $user->profile_image);
                } else {
                    $profile_image = $url;
                }
                // $role = $this->api_model->get_has_permission($user->staffid);
                if (!empty($playerid)) {
                    $ktrplayerid = get_table_where('tblplayer_id', array('staffid' => $user->staffid, 'player_id' => $playerid), '', 'row');
                    if (empty($ktrplayerid)) {
                        $ins['staffid'] = $user->staffid;
                        $ins['player_id'] = $playerid;
                        $this->db->insert('tblplayer_id', $ins);
                    }
                }
                echo json_encode([
                    'code' => 202,
                    'login' => true,
                    'tokenAccount' => $tokenAccount,
                    // 'role' => $role,
                    'admin' => is_admin($user->staffid),
                    'name' => $user->fullname,
                    'userid' => $user->staffid,
                    'profile_image' => $profile_image,
                ]);
                die();
            }
        }
        log_activity('Failed Login Attempt [Email: ' . $username . ', Is Staff Member: Yes, IP: ' . $this->input->ip_address() . ']');
        echo json_encode([
            'code' => 101,
            'token' => false
        ]);
        die();
    }

    public function Log_out()
    {
        $tokenAccount = $this->input->post('tokenAccount');
        $playerid = $this->input->post('playerid');

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $tokenAccount = $data_post['tokenAccount'];
                    $playerid = $data_post['playerid'];
                }
            }
        }
        if (!empty($playerid)) {
            $ktr = get_table_where('tblplayer_id', array('player_id' => $playerid), '', 'row');
            if (!empty($ktr)) {
                $this->db->delete('tblplayer_id', array('id' => $ktr->id));
            }
        }
    }
    public function CheckToken($tokenAccount = '')
    {
        if (!empty($tokenAccount)) {
            $tokenAccount =  base64_decode($this->encryption->decrypt(($tokenAccount)));
            $user = explode('---', $tokenAccount);

            if (!empty($user) && count($user) == 2) {
                $staffid = explode('---', base64_decode($user[1]));
                $this->db->where('staffid', $staffid[0]);
                $staff = $this->db->get('tblstaff')->row();

                if (!empty($staff) && $staff->password == base64_decode($user[0])) {
                    return $staff->staffid;
                }
                return 0;
            }
            return 0;
        }
    }
    //chat
    public function getInfoPusher()
	{
		$data['pusher_app_key'] = get_option('pusher_app_key');
		$data['pusher_app_secret'] = get_option('pusher_app_secret');
		$data['pusher_app_id'] = get_option('pusher_app_id');
		$data['cluster'] = get_option('pusher_cluster');
		echo json_encode((object)$data);
		die();
	}
    public function GetUserChat()
    {
        $tokenAccount = $this->input->post('tokenAccount');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $tokenAccount = $data_post['tokenAccount'];
                }
            }
        }
        if (!empty($tokenAccount)) {
            $staffid = $this->CheckToken($tokenAccount);
            if (!empty($staffid)) {
                $users = $this->api_chat->getUsers();
                if ($users) {
                    echo json_encode($users, true);die;
                } else {
                    die(_l('chat_error_table'));die;
                }
            }
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'clients' => false,
            ]);
            die;
        }
        echo json_encode([
            'code' => 111,
            'message' => 'Lỗi server',
            'clients' => false,
        ]);
        die;
    }



    public function get_vesion_app() {
        echo json_encode([
            'version_app' => get_option('version_app'),
            'link_app_android' => get_option('link_app_android'),
            'link_app_ios' => get_option('link_app_ios'),
            'note_app' => get_option('note_app'),
        ]);die();
    }


    
}
