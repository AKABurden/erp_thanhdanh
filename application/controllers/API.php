<?php

defined('BASEPATH') or exit('No direct script access allowed');

class API extends App_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('user_autologin');
    }

	public function login()
	{
		if(!empty($this->input->get('phone_number')))
		{
			$phone_number=$this->input->get('phone_number');
		}

		if(!empty($this->input->get('password')))
		{
			$password=$this->input->get('password');
		}

		if(!empty($this->input->post('password')))
		{	
			$password=$this->input->post('password');
		}

		if(!empty($this->input->post('phone_number')))
		{	
			$phone_number=$this->input->post('phone_number');
		}

		if(!empty($phone_number)&&!empty($password))
		{
			$this->load->model('authentication_model');
			$success = $this->authentication_model->login_mobiapp(
                $phone_number,
                $password
            );
            if($success == 2)
            {
            	echo json_encode(array('status'=>false, 'code' => '3', 'messenge' => 'Sai số điện thoại'), JSON_UNESCAPED_UNICODE);die();
            }
            else if($success == 1)
            {
            	echo json_encode(array('status'=>false,'code'=>'4','messenge'=>'Sai mật khẩu'),JSON_UNESCAPED_UNICODE );die();
            }
            else
            {
            	echo json_encode(array('status'=>'success','code'=>'202','data'=>$success),JSON_UNESCAPED_UNICODE );die();
            }
		}
		else
		{
			echo json_encode(array('status'=>false,'code'=>'1','messenge'=>'Đăng nhập không thành công'),JSON_UNESCAPED_UNICODE);die();
		}
	}

	public function login_v2()
	{
		if(!empty($this->input->post('password'))) {	
			$password=$this->input->post('password');
		}

		if(!empty($this->input->post('username'))) {	
			$username=$this->input->post('username');
		}

		if(!empty($username)&&!empty($password)) {
			$table = db_prefix() . 'staff';
            $_id   = 'staffid';
            $this->db->where('phonenumber', $username);
            $this->db->or_where('email', $username);
            $user = $this->db->get($table)->row();
            $staff = true;
            if ($user) {
            	
                // Email is okey lets check the password now
                if (!app_hasher()->CheckPassword($password, $user->password)) {
                    echo json_encode(array('status'=>false,'code'=>'4','messenge'=>'Sai mật khẩu'),JSON_UNESCAPED_UNICODE );die();
                }
            } else {
                echo json_encode(array('status'=>false, 'code' => '3', 'messenge' => 'Sai số điện thoại'), JSON_UNESCAPED_UNICODE);die();
            }

            if ($user->active == 0) {
                return [
                    'memberinactive' => true,
                ];
            }
            $this->load->helper('cookie');
            $key = substr(md5(uniqid(rand() . get_cookie($this->config->item('sess_cookie_name')))), 0, 16);
            $this->user_autologin->delete($user->$_id, $key, true);
            $this->user_autologin->set($user->$_id, md5($key),true);
            $user_data = [
                'staff_user_id'   => $user->$_id,
                'staff_full_name' => get_staff_full_name($user->$_id),
                'staff_logged_in' => true,
                'cookie' => ['name'  => 'autologin',
                            'value' => serialize([
                                'user_id' => $user->$_id,
                                'key'     => $key,
                            ]),
                            'expire' => 60 * 60 * 24 * 31 * 2,
                            ]
            ];
            echo json_encode(array(
                'status'=>'success',
                'code'=>'202',
                'userid'=>$user->staffid,
                'username'=>get_staff_full_name($user->staffid),
                'company'=>get_option('invoice_company_name')
            ),JSON_UNESCAPED_UNICODE );die();
		}
		else {
			echo json_encode(array('status'=>false,'code'=>'1','messenge'=>'Đăng nhập không thành công'),JSON_UNESCAPED_UNICODE);die();
		}
	}

    public function getClient($start = '', $limit = 10)
    {
        $Sstart = ($start - 1) * $limit;
        $this->db->select('tblclients.*');
        $this->db->where('active', 1);
        $this->db->limit($limit, $Sstart);
        $data = $this->db->get('tblclients')->result_array();
        $arr = array();
        $myObj = new stdClass();
        $array = ['#6A63FF','#FF4C4C','#00D8FF','#C2BB00','#790E00','#FC7D00','#FC00FF','#0064B1','#31AC00','#6F6F6F'];
        foreach ($data as $key => $value) {
            $color = rand(1, count($array)) - 1;
            // $arrName = explode(" ", $value['company']);
            // $number1 = count($arrName) - 1;
            // $number2 = count($arrName) - 2;
            $strName = mb_substr($value['company'], 0, 1);

            $myObj->nameCompany = $value['company'];
            $myObj->phone = $value['phonenumber'];
            $myObj->actived = $value['representative'];
            $myObj->address = $value['address'];
            $myObj->color = $array[$color];
            $myObj->strName = $strName;
            $myJSON = json_encode($myObj);
            array_push($arr, $myJSON);
        }
        echo json_encode($arr);die();
    }
}