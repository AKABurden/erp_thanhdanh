<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Config_ip extends AdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function confim()
	{
		$code_active = $this->input->post('code_active');

		$this->db->where('code_active', $code_active);
		$this->db->where('DATE_FORMAT(date_end_code, "%Y-%m-%d %H:%i:%s") >= "' . date('Y-m-d H:i:s') . '"');
		$this->db->where('staffid', get_staff_user_id());
		$login_active = $this->db->get('tbl_ip_login_active')->row();
		if(!empty($login_active)) {
			$date_now = date('Y-m-d H:i:s');
			$date_end_login = strtotime('+1 days', strtotime($date_now));
			$date_end_login = date('Y-m-d H:i:s', $date_end_login);

			$this->db->where('id', $login_active->id);
			$success = $this->db->update('tbl_ip_login_active', [
				'active' => 1,
				'date_end_login' => $date_end_login
			]);

			$user_data = ['staff_ip'      => true];
			$this->session->set_userdata($user_data);
			if(!empty($success)) {
				set_alert('success', 'Nhập mã thành công');
				redirect('admin');
			}
		}
		set_alert('danger', 'Nhập mã không đúng hoặc mã hết hạn');
		redirect('admin');
	}
}
