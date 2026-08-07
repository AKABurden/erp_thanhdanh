<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $GLOBALS['EXT']->call_hook('pre_controller_constructor');
        $this->lang->load('vietnamese', 'vietnamese');


        /*  if(!$this->input->is_ajax_request()){
                $this->output->enable_profiler(TRUE);
            }
        */

        /**
         * Fix for users who don't replace all files during update !!!
         */
        if (!class_exists('ForceUTF8\Encoding') && file_exists(APPPATH . 'vendor/autoload.php')) {
            require_once(APPPATH . 'vendor/autoload.php');
        }

        if (is_dir(FCPATH . 'install') && ENVIRONMENT != 'development') {
            echo '<h3>Delete the install folder</h3>';
            die;
        }

        $this->db->reconnect();

        /**
         * Set system timezone based on selected timezone from options
         * @var string
         */
        $timezone = get_option('default_timezone');
        if ($timezone != '') {
            date_default_timezone_set($timezone);
        }

        /**
         * Clear last upgrade copy data
         * @var object
         */
        if ($lastUpdate = get_last_upgrade_copy_data()) {
            if ((time() - $lastUpdate->time) > _delete_temporary_files_older_then()) {
                @unlink($lastUpdate->path);
                update_option('last_upgrade_copy_data', '');
            }
        }

        hooks()->do_action('app_init');
        $this->load->library('encryption');
    }
    public function Login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $playerid = $this->input->post('playerid');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $email = !empty($data_post['email']) ? $data_post['email'] : NULL;
                    $password = !empty($data_post['password']) ? $data_post['password'] : NULL;
                    $playerid = !empty($data_post['playerid']) ? $data_post['playerid'] : NULL;
                }
            }
        }
        if (!empty($email) && !empty($password)) {
            $this->db->group_start();
            $this->db->where('email', $email);
            $this->db->or_where('phonenumber', $email);
            $this->db->group_end();
            $staff = $this->db->get('tblstaff')->row();
            if (!empty($staff)) {
                if (app_hasher()->CheckPassword($password, $staff->password)) {
                    $url = base_url('assets/images/user-placeholder.jpg');
                    if ($staff && $staff->profile_image !== null) {
                        $profile_image = base_url('uploads/staff_profile_images/' . $staff->staffid . '/small_' . $staff->profile_image);
                    } else {
                        $profile_image = $url;
                    }
                    if (!empty($playerid)) {
                        $ktrplayerid = get_table_where('tblplayer_id', array('staffid' => $staff->staffid, 'player_id' => $playerid), '', 'row');
                        if (empty($ktrplayerid)) {
                            $ins['staffid'] = $staff->staffid;
                            $ins['player_id'] = $playerid;
                            $this->db->insert('tblplayer_id', $ins);
                        }
                    }
                    $this->CreateTokenLogin([
                        'password' => $staff->password,
                        'id' => $staff->staffid,
                        'email' => $email,
                        'admin' => is_admin($staff->staffid),
                        'name' => get_staff_full_name($staff->staffid),
                        'userid' => $staff->staffid,
                        'profile_image' => $profile_image,
                    ]);
                }
            }
        }
        echo json_encode((object)[
            'result' => (object)[
                'login' => false,
                'token' => false
            ]
        ]);
        die();
    }

    /*------------------------Tạo token login account-------------------------*/
    private function CreateTokenLogin($data = [])
    {
        $dateNow = date('Y-m-d H:i:s');
        $dateNow = new DateTime($dateNow);
        @$dateEndToken = date("Y-m-d H:i:s", strtotime("$dateNow +30 days"));

        $data['password'] = !empty($data['password']) ? $data['password'] : NULL;
        $data['id'] = !empty($data['id']) ? $data['id'] : NULL;
        $data['email'] = !empty($data['email']) ? $data['email'] : NULL;
        $token = base64_encode($data['password']) . '---' . base64_encode($data['id'] . '---' . $dateEndToken);
        $tokenAccount = $this->encryption->encrypt(base64_encode($data['id']));
        $tokenAccount = trim(base64_encode(str_replace("/", "---", $tokenAccount)), '=');

        $dataResult = [
            'login' => true,
            'token' => $token,
            'token_account' => $tokenAccount,
            'admin' => $data['admin'],
            'name' => $data['name'],
            'userid' => $data['userid'],
            'profile_image' => $data['profile_image']
        ];
        echo json_encode(
            (object)[
                'result' => (object)$dataResult
            ]
        );
        die();
    }


    public function Log_out()
    {
        $playerid = $this->input->post('playerid');

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
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
    public function Reader_Notification()
    {
        $tokenAccount = $this->input->post('tokenAccount');
        $notificationID = $this->input->post('notificationID');

        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $tokenAccount = $data_post['tokenAccount'];
                    $notificationID = $data_post['notificationID'];
                }
            }
        }

        if (!empty($notificationID) && !empty($tokenAccount)) {
            $staffid = checkTokenLoginApp($tokenAccount);
            if (!empty($staffid)) {
                $this->db->update('tblnotification_app', array('readed' => 1), array('staff_id' => $staffid, 'notificationID' => $notificationID));
                echo json_encode([
                    'reader' => true,
                ]);
                die;
            }
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'notification' => false,
            ]);
            die;
        }
        echo json_encode([
            'code' => 222,
            'message' => 'Lỗi server',
            'notification' => false,
        ]);
        die;
    }
    public function Reader_all_Notification()
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
            $staffid = checkTokenLoginApp($tokenAccount);
            if (!empty($staffid)) {
                $this->db->update('tblnotification_app', array('readed' => 1), array('staff_id' => $staffid));
                echo json_encode([
                    'reader' => true,
                ]);
                die;
            }
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'notification' => false,
            ]);
            die;
        }
        echo json_encode([
            'code' => 222,
            'message' => 'Lỗi server',
            'notification' => false,
        ]);
        die;
    }
    public function GetListNotification($page = 1, $limit = 10)
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
            $staffid = checkTokenLoginApp($tokenAccount);
            $player_id = !empty($data_post['player_id']) ? $data_post['player_id'] : null;
            // print_arrays($player_id);
            if (!empty($staffid)) {
                if (empty($limit)) {
                    $limit = 10;
                }
                if ($page == 0) {
                    $page = 1;
                }
                $row_start = (($page - 1) * $limit);
                $this->db->select('
                    tblnotification_app.*,
                    tblstaff.profile_image,
                    tblnotification_app.time, 
                    tblnotification_app.staff_id, 
                    tblnotification_app.type
                ');
                $this->db->where('tblnotification_app.staff_id', $staffid);
//                $this->db->where('tblnotification_app.player_id', $player_id);
                $this->db->join('tblstaff', 'tblstaff.staffid = tblnotification_app.staff_id', 'left');
                // $this->db->group_by('tblnotification_app.time, tblnotification_app.staff_id, tblnotification_app.type');

                $this->db->order_by('time DESC');
                $this->db->limit($limit, $row_start);
                // print_arrays($this->db->get_compiled_select('tblnotification_app'));
                $data['notification'] = $this->db->get('tblnotification_app')->result_array();

                foreach ($data['notification'] as $key => $value) {
                    // $data['notification'][$key]['staff_name'] = 
                    if ($value['profile_image'] !== null) {
                        $data['notification'][$key]['profile_image'] = base_url('uploads/staff_profile_images/' . $value['created_by'] . '/thumb_' . $value['profile_image']);
                    }
                    $data['notification'][$key]['staff_name'] = get_staff_full_name($value['created_by']);
                }
                // $data['notification'] = get_table_where('tblnotification_app', array('staff_id' => $staffid), '', 'result_array','notificationID');

                if (!empty($limit)) {
                    $this->db->where('staff_id', $staffid);
//                    $this->db->where('tblnotification_app.player_id', $player_id);
                    $this->db->group_by('notificationID');
                    $this->db->limit($limit, ($row_start + $limit));
                    $check = $this->db->get('tblnotification_app')->result_array();
                    if (count($check) > 0) {
                        $data['next_page'] = true;
                    } else {
                        $data['next_page'] = false;
                    }
                } else {
                    $data['next_page'] = false;
                }
                $data['count_all'] = (total_rows_join('tblnotification_app', array('staff_id' => $staffid), array(), 'notificationID'));
                $data['count_readed'] = (total_rows_join('tblnotification_app', array('staff_id' => $staffid, 'readed' => 1), array(), 'notificationID'));
                $data['count_not_readed'] = (total_rows_join('tblnotification_app', array('staff_id' => $staffid, 'readed' => 0), array(), 'notificationID'));

                echo json_encode($data);
                die;
            }
            echo json_encode([

                'code' => 111,
                'message' => 'User không tồn tại',
                'notification' => false,
            ]);
            die;
        }
        echo json_encode([

            'code' => 222,
            'message' => 'Lỗi server',
            'notification' => false,
        ]);
        die;
    }
    public function GetPermission()
    {
        $tokenAccount = '';
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['tokenAccount'])) {
                    $tokenAccount = $data_post['tokenAccount'];
                }
            }
        }
        $staffid = checkTokenLoginApp($tokenAccount);
        $staff = get_table_where('tblstaff', array('staffid' => $staffid), '', 'row');
        if (!empty($staff)) {
            $Permission['purchases'] = has_permission('purchases', $staffid, 'view_price');
            $Permission['purchase_order'] = has_permission('purchase_order', $staffid, 'view_price');
            $Permission['purchase_order_onsumable'] = has_permission('purchase_order', $staffid, 'view_price_onsumable');
            $Permission['import'] = has_permission('import', $staffid, 'view_price');
            echo json_encode($Permission, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'result' => false,
            ]);
            die;
        }
    }

    public function Login_Admin()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $playerid = $this->input->post('playerid');
        if (empty($this->input->post())) {
            $data_post = file_get_contents('php://input');
            if (!empty($data_post)) {
                if (!is_array($data_post)) {
                    $data_post = (array)json_decode($data_post);
                    $email = !empty($data_post['email']) ? $data_post['email'] : NULL;
                    $password = !empty($data_post['password']) ? $data_post['password'] : NULL;
                    $playerid = !empty($data_post['playerid']) ? $data_post['playerid'] : NULL;
                }
            }
        }
        if (!empty($email) && !empty($password)) {
            $this->db->group_start();
            $this->db->where('email', $email);
            $this->db->group_end();
            $staff = $this->db->get('tblstaff')->row();
            if (!empty($staff)) {
                if (app_hasher()->CheckPassword($password, $staff->password)) {
                    $url = base_url('assets/images/user-placeholder.jpg');
                    if ($staff && $staff->profile_image !== null) {
                        $profile_image = base_url('uploads/staff_profile_images/' . $staff->staffid . '/small_' . $staff->profile_image);
                    } else {
                        $profile_image = $url;
                    }
                    if (!empty($playerid)) {
                        $ktrplayerid = get_table_where('tblplayer_id_admin', array('staffid' => $staff->staffid, 'player_id' => $playerid), '', 'row');
                        if (empty($ktrplayerid)) {
                            $ins['staffid'] = $staff->staffid;
                            $ins['player_id'] = $playerid;
                            $this->db->insert('tblplayer_id_admin', $ins);
                        }
                    }
                    $this->CreateTokenLogin([
                        'password' => $staff->password,
                        'id' => $staff->staffid,
                        'email' => $email,
                        'admin' => is_admin($staff->staffid),
                        'name' => get_staff_full_name($staff->staffid),
                        'userid' => $staff->staffid,
                        'profile_image' => $profile_image,
                    ]);
                }
            }
        }
        echo json_encode((object)[
            'result' => (object)[
                'login' => false,
                'token' => false
            ]
        ]);
        die();
    }
}