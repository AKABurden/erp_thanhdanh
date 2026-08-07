<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'controllers/api_app/Api_Controller.php');

class Api_internal_proposal extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $tokenAccount = '';
		$this->data = [];
        $data_post = file_get_contents('php://input');
        if (!empty($data_post)) {
            if (!is_array($data_post)) {
                $data_post = json_decode($data_post, true);
                if (!empty($data_post['tokenAccount'])) {
                    $tokenAccount = $data_post['tokenAccount'];
					unset($data_post['tokenAccount']);
                }
				$this->data = $data_post;
            }
        } else if ($this->input->post()) {
            $data_post = $this->input->post();
            if (!empty($data_post['tokenAccount'])) {
                $tokenAccount = $data_post['tokenAccount'];
				unset($data_post['tokenAccount']);
            }
			$this->data = $data_post;
        }

        $staffid = checkTokenLoginApp($tokenAccount);
        $staff = get_table_where('tblstaff', array('staffid' => $staffid), '', 'row');
        if (!empty($staff)) {
            $this->staffid = $staffid;
        } else {
            echo json_encode([
                'code' => 111,
                'message' => 'User không tồn tại',
                'result' => false,
            ]);
            die;
        }

		$this->status = [
			'0' => 'Chưa duyệt',
			'1' => 'Đã duyệt',
			'2' => 'Không duyệt',
		];

		$this->load->model('internal_proposal_model');

		$this->perView = has_permission('internal_proposal', $this->staffid, 'view');
		$this->perViewOwn = has_permission('internal_proposal', $this->staffid, 'view_own');

		$this->perAdd = has_permission('internal_proposal', $this->staffid, 'create');
		$this->perEdit = has_permission('internal_proposal', $this->staffid, 'edit');
		$this->perDelete = has_permission('internal_proposal', $this->staffid, 'delete');
		$this->perApprove = has_permission('internal_proposal', $this->staffid, 'approve_accept');
		$this->perPdf = has_permission('internal_proposal', $this->staffid, 'print');
		$this->is_branch = true;
    }

	public function get_status() {
		echo json_encode($this->status);
	}

	public function get_branch() {
		echo json_encode($this->db->get('tblbranch')->result_array());
	}

	public function get_category_tasks() {
		if(!empty($this->data['search'])) {
			$this->db->where('code like "%'.$this->data['search'].'%"', false, false);
		}
		$this->db->group_start();
		$this->db->like('content', 'đề xuất');
		$this->db->or_like('code', 'đề xuất');
		$this->db->group_end();
		echo json_encode($this->db->get('tblcategory_tasks')->result_array());
	}

	public function get_staff() {

		if(!empty($this->data['search'])) {
			$this->db->where('CONCAT(firstname, " ", lastname) like "%'.$this->data['search'].'%"', false, false);
		}
		$staff = $this->db->get('tblstaff')->result_array();
		foreach($staff as $key => $value) {
			$staff[$key]['fullname'] = get_staff_full_name($value['staffid']);
			$staff[$key]['staff_image'] = staff_profile_image_url($value['staffid']);
		}
		echo json_encode($staff);die();
	}

	public function get_list_staff_departments() {
		$code_departments = [
			'1.BOD-CFO',
			'1.BOD-PRE',
			'1.CEO',
			'1.KH',
			'1.KH',
		];
		$StringWhere = [];
		foreach($code_departments as $key => $value) {
			$StringWhere[] = 'tbldepartments.code = "'.$value.'"';
		}

		$staffDepartments = "(
			SELECT
				tblstaff_departments.staffid as staffid,
				GROUP_CONCAT(tbldepartments.name) as name_department 
			FROM tblstaff_departments
			INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
			WHERE tbldepartments.departmentid != 0 ".(!empty($StringWhere) ? ('AND (' . implode(' OR ', $StringWhere) . ')') : '')."
			GROUP BY tblstaff_departments.staffid
		) tb_staff_departments";
		$this->db->select('
			tblstaff.staffid as staffid,
			tblstaff.firstname as firstname,
			tblstaff.lastname as lastname,
			CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname,
			tblroles.name as name_role,
			tb_staff_departments.name_department as name_department
		', false);
		$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
		$this->db->join($staffDepartments, 'tb_staff_departments.staffid = tblstaff.staffid');
		$staff = $this->db->get('tblstaff')->result_array();
		foreach($staff as $key => $value) {
			$staff[$key]['fullname'] = get_staff_full_name($value['staffid']);
			$staff[$key]['staff_image'] = staff_profile_image_url($value['staffid']);
		}
		echo json_encode($staff);die();

	}

	public function get_services() {
		$services = $this->db->get('tbl_services')->result_array();
		echo json_encode($services);die();
	}

	public function get_purchase_order() {
		$this->db->select('tblpurchase_order.*, CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as fullcode, tblsuppliers.company as company');
		$this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id', 'left');
		$purchase_order = $this->db->get('tblpurchase_order')->result_array();
		echo json_encode($purchase_order);die();
	}

    public function get_list($page = 1, $limit = 10)
    {
		$next = false;
		$start = ($page - 1) * $limit;
		$is_admin = is_admin($this->staffid);
		if(!empty($this->is_branch) && !$is_admin) {
			$list_branch = get_array_branch_staff($this->staffid);
		}
		$this->db->limit(($limit + 1), $start);
		if(isset($this->data['filterStatus']) && is_numeric($this->data['filterStatus'])) {
			$filterStatus = $this->data['filterStatus'];
			if($filterStatus == 1) {
				$this->db->where('tblinternal_proposal.status', 1);
			}
			else if($filterStatus == 2) {
				$this->db->where('tblinternal_proposal.status', 2);
			}
			else {
				$this->db->where('tblinternal_proposal.status', 0);
			}
		}
		if(!empty($this->data['date_start'])) {
			$this->db->where('DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") >= "'.to_sql_date($this->data['date_start']).'"', false, false);
		}
		if(!empty($this->data['date_end'])) {
			$this->db->where('DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") <= "'.to_sql_date($this->data['date_end']).'"', false, false);
		}
		if(!empty($this->data['staff_search'])) {
			$this->db->where('tblinternal_proposal.staff = "' . $this->data['staff_search'] . '"', false, false);
		}
		if(!empty($this->data['code_search'])) {
			$this->db->where('tblinternal_proposal.code LIKE "%' . $this->data['code_search'] . '%"', false, false);
		}
		if(!empty($this->data['category_tasks'])) {
			$this->db->where('tblinternal_proposal.category_tasks = "' . $this->data['category_tasks'] . '"', false, false);
		}

		if(!$this->perView) {
			$this->db->where('
							(
								EXISTS (
									SELECT 1 
									FROM tblinternal_proposal_assigned 
									WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
									AND tblinternal_proposal_assigned.id_staff = "'.$this->staffid.'"
								)
								OR tblinternal_proposal.staff = "'.$this->staffid.'"
							)
				', false, false);
		}

		if(!empty($this->is_branch) && !$is_admin) {
			if (!empty($list_branch)) {
				$this->db->where_in('tblinternal_proposal.id_branch', $list_branch);
			}
			else {
				$this->db->where_in('tblinternal_proposal.id', 0);
			}
		}


		$this->db->select([
			'tblinternal_proposal.id as id',
			'tblinternal_proposal.date as date',
			'tblinternal_proposal.code as code',
			'tblinternal_proposal.staff as staff',
			'tblcategory_tasks.code as code_category_tasks',
			'tblinternal_proposal.money as money',
			'tblinternal_proposal.approved_by as approved_by',
			'tblinternal_proposal.content as content',
			'tblinternal_proposal.date_create',
			'id_suggestion',
			'tblsuggestion.code as code_suggestion',
			'CONCAT(tblother_payslips.prefix, "-", tblother_payslips.code) as code_other_payslips',
			'tblinternal_proposal.id_other_payslips',
			'(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',
			'CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchases',
			'CONCAT(tbl_services.prefix, tbl_services.code) as code_service',
			'CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order',
			'tblinternal_proposal.id_purchases',
			'tblinternal_proposal.id_service',
			'tblinternal_proposal.id_purchase_order',
			'tblinternal_proposal.status',
			'tblinternal_proposal.reason',
			'tblinternal_proposal.staff',
			'tblbranch.name as name_branch',
		]);
		$this->db->join('tblsuggestion', 'tblsuggestion.id = tblinternal_proposal.id_suggestion', 'left');
		$this->db->join('tblother_payslips', 'tblother_payslips.id = tblinternal_proposal.id_other_payslips', 'left');
		$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks', 'left');
		$this->db->join('tblpurchases', 'tblpurchases.id = tblinternal_proposal.id_purchases', 'left');
		$this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
		$this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblinternal_proposal.id_purchase_order', 'left');
		$this->db->join('tblbranch', 'tblbranch.id = tblinternal_proposal.id_branch', 'left');
		$internal_proposal = $this->db->get('tblinternal_proposal')->result_array();
		if(count($internal_proposal) == ($limit + 1)) {
			$next = true;
			unset($internal_proposal[$limit]);
		}

		foreach($internal_proposal as $key => $value) {

			if(!empty($value['content'])) {
				$internal_proposal[$key]['content'] = (c_html_to_text($value['content']));
				$internal_proposal[$key]['content'] = (mb_strlen($internal_proposal[$key]['content'], 'UTF-8') > 500 ? mb_substr($internal_proposal[$key]['content'], 0, 500, "utf-8").'...' : $internal_proposal[$key]['content']);
			}

			$internal_proposal[$key]['staff_assigned'] = $this->db->get_where('tblinternal_proposal_assigned', ['id_internal_proposal' => $value['id']])->result_array();
			if(!empty($internal_proposal[$key]['staff_assigned'])) {
				foreach($internal_proposal[$key]['staff_assigned'] as $k => $v) {
					$internal_proposal[$key]['staff_assigned'][$k]['fullname'] = get_staff_full_name($v['id_staff']);
					$internal_proposal[$key]['staff_assigned'][$k]['image'] = staff_profile_image_url($v['id_staff']);
				}
			}

			$internal_proposal[$key]['staff_name'] = get_staff_full_name($value['staff']);
			$internal_proposal[$key]['staff_image'] = staff_profile_image_url($value['staff']);

			$internal_proposal[$key]['approved_by_name'] = get_staff_full_name($value['approved_by']);
			$internal_proposal[$key]['approved_by_image'] = staff_profile_image_url($value['approved_by']);

			$this->db->select('CONCAT("'.base_url('uploads/internal_proposal/' . $value['id'].'/').'", file_name) as file, filetype');
			$this->db->where('rel_type', 'internal');
			$this->db->where('rel_id', $value['id']);
			$internal_proposal[$key]['file'] = $this->db->get('tblfiles')->result_array();
		}

		$data_count_status = [];
		foreach($this->status as $key => $value) {
			$this->db->where('tblinternal_proposal.status', $key);
			if(!empty($this->data['date_start'])) {
				$this->db->where('DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") >= "'.to_sql_date($this->data['date_start']).'"', false, false);
			}
			if(!empty($this->data['date_end'])) {
				$this->db->where('DATE_FORMAT(tblinternal_proposal.date, "%Y-%m-%d") <= "'.to_sql_date($this->data['date_end']).'"', false, false);
			}
			if(!empty($this->data['staff_search'])) {
				$this->db->where('tblinternal_proposal.staff = "' . $this->data['staff_search'] . '"', false, false);
			}
			if(!empty($this->data['code_search'])) {
				$this->db->where('tblinternal_proposal.code LIKE "%' . $this->data['code_search'] . '%"', false, false);
			}
			if(!empty($this->data['category_tasks'])) {
				$this->db->where('tblinternal_proposal.category_tasks = "' . $this->data['category_tasks'] . '"', false, false);
			}
			$data_count_status[$key] = $this->db->get('tblinternal_proposal')->num_rows();
		}

		echo json_encode([
			'result' => true,
			'next' => $next,
			'data' => $internal_proposal,
			'count_status' => $data_count_status,
		]);die();
    }

	public function add_or_edit()
	{
		if ($this->data) {
			$message = '';
			$data = $this->data;

			if(empty($data['id_branch'])) {
				echo json_encode(array(
					'success' => false,
					'message' => 'Vui lòng chọn chi nhánh'
				));
				die;
			}

			$id = !empty($data['id']) ? $data['id'] : '';
			unset($data['id']);
			$data['date'] = to_sql_date($data['date']);
			$data['money'] = number_format_data($data['money'], false);
			$staff_assigned = $data['staff_assigned'];
			unset($data['staff_assigned']);
			if (empty($id)) { //add a new
				if(empty($this->perAdd)) {
					echo json_encode([
						'success' => false,
						'message' => 'Bạn không có quyền tạo phiếu đề xuất nội bộ'
					]);die();
				}
				$data['code'] = $this->internal_proposal_model->getCode();
				$data['create_by'] = $this->staffid;
				$this->db->insert('tblinternal_proposal', $data);
				$id = $this->db->insert_id();
				if (!empty($id)) {
					$success = true;
					$message = _l('ch_added_successfuly');
					if(!empty($staff_assigned)) {
						$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name($this->staffid) . ' Vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ '. $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
						foreach($staff_assigned as $key => $value) {
							$this->db->insert('tblinternal_proposal_assigned', [
								'id_internal_proposal' => $id,
								'id_staff' => $value,
							]);


							$notification_data = [
								'date' => date('Y-m-d H:i:s'),
								'description' => $dataHtml,
								'touserid' => $value,
								'link' => 'internal_proposal/view/' . $id,
								'type' => 13,
								'object_id' => $id,
								'object_type' => 'internal_proposal',
							];


							if (!empty($notification_data)) {
								$this->db->insert('tblnotifications', $notification_data);
								pusher_trigger_notification($notification_data);
							}
							send_notification_app_c($id, [
								'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ '. $data['code'] . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
								'title' => 'Theo dõi phiếu đề xuất nội bộ',
								'code' => $data['code'],
								'object_type' => 'internal_proposal'
							], [$value], $this->staffid);
						}
						$this->SendEmailNoti($staff_assigned, $id);
					}


				} else {
					$success = false;
					$message = _l('ch_added_successfuly_not');
				}
			}
			else {
				if(empty($this->perEdit)) {
					ajax_access_denied();
				}
				$this->db->where('id', $id);
				$internal_proposal = $this->db->get('tblinternal_proposal')->row();

				$this->db->where('id', $id);
				$success_update = $this->db->update('tblinternal_proposal', $data);
				if ($success_update) {
					$success = true;
					$message = _l('Sửa thành công');

					$this->db->where('id_internal_proposal', $id);
					$list_assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
					$arrayList = [];
					if(!empty($list_assigned)) {
						foreach($list_assigned as $key => $value) {
							$arrayList[$value['id_staff']] = true;
						}
					}

					$this->db->where('id_internal_proposal', $id);
					$this->db->delete('tblinternal_proposal_assigned');

					if(!empty($staff_assigned)) {
						$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="' . lang('system') . '"> 
							Hệ thống - ' . get_staff_full_name($this->staffid) . ' Vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';
						$arraySendEmail = [];
						foreach($staff_assigned as $key => $value) {
							$this->db->insert('tblinternal_proposal_assigned', [
								'id_internal_proposal' => $id,
								'id_staff' => $value,
							]);
							if(empty($arrayList[$value])) {
								$arraySendEmail[] = $value;
								$notification_data = [
									'date' => date('Y-m-d H:i:s'),
									'description' => $dataHtml,
									'touserid' => $value,
									'link' => 'internal_proposal/view/' . $id,
									'type' => 13,
									'object_id' => $id,
									'object_type' => 'internal_proposal',
								];


								if (!empty($notification_data)) {
									$this->db->insert('tblnotifications', $notification_data);
									pusher_trigger_notification($notification_data);
								}
								send_notification_app_c($id, [
									'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
									'title' => 'Theo dõi phiếu đề xuất nội bộ',
									'code' => $internal_proposal->code,
									'object_type' => 'internal_proposal'
								], [$value],  $this->staffid);
							}
						}

						if(!empty($arraySendEmail)) {
							$this->SendEmailNoti($arraySendEmail, $id);
						}
					}
				} else {
					$success = false;
					$message = _l('Sửa không thành công');
				}

			}

			if(!empty($success) && !empty($id)) {
				$this->load->library('upload');
				if (isset($_FILES['file']['name']) && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
					if (!is_array($_FILES['file']['name'])) {
						$_FILES['file']['name']     = [$_FILES['file']['name']];
						$_FILES['file']['type']     = [$_FILES['file']['type']];
						$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
						$_FILES['file']['error']    = [$_FILES['file']['error']];
						$_FILES['file']['size']     = [$_FILES['file']['size']];
					}
					$path = 'uploads/internal_proposal/' . $id . '/';
					if (!file_exists(FCPATH . 'uploads/internal_proposal/')) {
						mkdir(FCPATH . 'uploads/internal_proposal/');
						fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
					}
					if (!file_exists(FCPATH . 'uploads/internal_proposal/' . $id)) {
						mkdir(FCPATH . 'uploads/internal_proposal/' . $id);
						fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
					}
					for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
						$tmpFilePath = $_FILES['file']['tmp_name'][$i];
						if (!empty($tmpFilePath) && $tmpFilePath != '') {
							$filename = vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
							if (!_upload_extension_allowed($filename)) {
								continue;
							}
							$newFilePath = $path . $filename;
							if (move_uploaded_file($tmpFilePath, $newFilePath)) {
								$typeFile = $_FILES['file']['type'][$i];
								if (file_exists($newFilePath)) {
									$this->db->insert('tblfiles', [
										'rel_id' => $id,
										'rel_type' => 'internal',
										'file_name' => $filename,
										'filetype' => $typeFile,
										'staffid' =>  $this->staffid,
										'dateadded' => date('Y-m-d H:i:s'),
									]);
								}
							}
						}
					}
				}
			}

			echo json_encode(array(
				'success' => $success,
				'message' => $message,
				'id' => $id
			));
			die;
		}
	}

	public function SendEmailNoti($id_staff = [], $id_internal_proposal = '') {
//		return true;// tạm thời đóng do ở local
		if(!empty($id_staff) && !empty($id_internal_proposal)) {
			$this->db->where_in('staffid', $id_staff);
			$this->db->where('active', 1);
			$staff = $this->db->get('tblstaff')->result_array();
			if(!empty($staff)) {
				$this->db->where('id', $id_internal_proposal);
				$internal_proposal = $this->db->get('tblinternal_proposal')->row();

				$list_staff = [];
				foreach ($staff as $key => $value) {
					if (!empty($value['email'])) {
						$list_staff[] = $value['email'];
					}
				}
				$this->load->config('email');
				$template = new StdClass();
				$template->message = get_option('email_header') .'<br/> '.get_staff_full_name($this->staffid).' Vừa thêm bạn vào người duyệt phiếu đề xuất nội bộ ' .$internal_proposal->code.' vào lúc ' . _dt(date('Y-m-d H:i:s')).'<br/>
				 <b>Số tiền:</b> '.(!empty($internal_proposal->money) ? number_format_data($internal_proposal->money) : '0').'<br/>
				 <b>Nội dung:</b> '.$internal_proposal->content.'<br/>
				 Vui lòng theo dõi và tiến hành cập nhật!<br/>';
				$template->fromname = get_option('companyname') != '' ? get_option('companyname') : '';
				$template->subject = 'PHIẾU ĐỀ XUẤT NỘI BỘ';
				$this->email->initialize();
				if (get_option('mail_engine') == 'phpmailer') {
					$this->email->set_debug_output(function ($err) {
//						if (!isset($GLOBALS['debug'])) {
//							$GLOBALS['debug'] = '';
//						}
//						$GLOBALS['debug'] .= $err . '<br />';
//						return $err;
						return false;
					});
//					$this->email->set_smtp_debug(3);
				}
				$this->email->set_newline(config_item('newline'));
				$this->email->set_crlf(config_item('crlf'));
				$this->email->from(get_option('smtp_email'), $template->fromname);
				$this->email->to($list_staff);
				$systemBCC = get_option('bcc_emails');
				if ($systemBCC != '') {
					$this->email->bcc($systemBCC);
				}
				$this->email->subject($template->subject);
				$this->email->message($template->message);
				if ($this->email->send(true)) {
					return true;
				} else {
//					set_debug_alert('<h1>Your SMTP settings are not set correctly here is the debug log.</h1><br />' . $this->email->print_debugger() . (isset($GLOBALS['debug']) ? $GLOBALS['debug'] : ''));
//					hooks()->do_action('smtp_test_email_failed');
				}
			}
		}
		return false;
	}

	public function delete($id = '')
	{
		if(empty($this->perDelete)) {
			echo json_encode([
				'success' => false,
				'message' => 'Tài khoản không có quyền xóa phiếu đề xuất nội bộ'
			]);die();
		}
		if ($this->internal_proposal_model->isApproved($id)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Đề xuất này đã được duyệt. Không thể xóa!'
			));
			die;
		}
		$this->db->where('rel_id', $id);
		$this->db->where('rel_type', 'internal_proposal');
		$tasks = $this->db->get('tbltasks')->row();
		if(!empty($tasks)) {
			echo json_encode(array(
				'success' => false,
				'message' => 'Đang tồn tại phiếu công việc không thể xóa'
			));
			die;
		}

		$isSuccess = $this->db->delete('tblinternal_proposal', array('id' => $id));
		if ($isSuccess) {
			$success = true;
			$message = _l('Xóa thành công');
		} else {
			$success = false;
			$message = _l('Xóa không thành công');
		}
		echo json_encode(array(
			'success' => $success,
			'message' => $message
		));
		die;
	}

	public function approve($id, $status = 0)
	{
		if(!$this->perApprove) {
			echo json_encode([
				'success' => false,
				'message' => 'Tài khoản không có quyền duyệt'
			]);die();
		}
		$id_tasks = '';

		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('tblinternal_proposal')->row();


		if (!$this->internal_proposal_model->isApproved($id) && $status == 1) {
			$staff = $this->staffid;
			$data = [
				'approved_by' => $staff,
				'status' => 1,
				'reason' => NULL,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('tblinternal_proposal', $data);
			if ($success) {
				$id_tasks = $this->createTaskAuto($id);
				$success = true;
				$message = _l('Duyệt thành công');

				$staff_assigned = $this->db->get_where('tblinternal_proposal_assigned', ['id_internal_proposal' => $id])->result_array();
				if(!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="Hệ thống"> 
							Hệ thống - ' . get_staff_full_name($this->staffid) . ' Vừa duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';

					foreach($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'internal_proposal/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'internal_proposal',
						];

						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
							'code' => $internal_proposal->code,
							'object_type' => 'internal_proposal'
						], [$value['id_staff']], $this->staffid);
					}
				}

			} else {
				$success = false;
				$message = _l('Duyệt không thành công');
			}
		}
		else if($status == 0){
			$data = [
				'approved_by' => 0,
				'status' => 0,
				'reason' => NULL,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('tblinternal_proposal', $data);
			if ($success) {
				$success = true;
				$message = _l('Hủy duyệt thành công');

				$contentStatus = '';
				if($internal_proposal->status == 2) {
					$contentStatus = ' không';
				}


				$staff_assigned = $this->db->get_where('tblinternal_proposal_assigned', ['id_internal_proposal' => $id])->result_array();
				if(!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="Hệ thống"> 
							Hệ thống - ' . get_staff_full_name($this->staffid) . ' Vừa hủy'.$contentStatus.' duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';

					foreach($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'internal_proposal/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'internal_proposal',
						];

						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa hủy'.$contentStatus.' duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
							'code' => $internal_proposal->code,
							'object_type' => 'internal_proposal'
						], [$value['id_staff']], $this->staffid);
					}
				}

			} else {
				$success = false;
				$message = _l('Hủy duyệt không thành công');
			}
		}
		echo json_encode(array(
			'id_task' => $id_tasks,
			'success' => $success,
			'message' => $message
		));
		die;
	}

	public function not_approve($id, $status = 0)
	{
		if(!$this->perApprove) {
			ajax_access_denied();
		}
		$success = false;
		$message = _l('Không duyệt không thành công');

		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('tblinternal_proposal')->row();


		if (!$this->internal_proposal_model->isApproved($id) && $status = 2) {
			$staff = $this->staffid;
			$reason = !empty($this->data['reason']) ? $this->data['reason'] : NULL;
			$data = [
				'approved_by' => $staff,
				'status' => 2,
				'reason' => $reason,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('tblinternal_proposal', $data);
			if ($success) {
				$success = true;
				$message = _l('Không duyệt thành công');

				$staff_assigned = $this->db->get_where('tblinternal_proposal_assigned', ['id_internal_proposal' => $id])->result_array();
				if(!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="Hệ thống"> 
							Hệ thống - ' . get_staff_full_name($this->staffid) . ' Vừa không duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';

					foreach($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'internal_proposal/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'internal_proposal',
						];

						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa không duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
							'code' => $internal_proposal->code,
							'object_type' => 'internal_proposal'
						], [$value['id_staff']], $this->staffid);
					}
				}
			}
		}
		else if($status == 0){
			$data = [
				'approved_by' => 0,
				'status' => 0,
				'reason' => NULL,
			];
			$this->db->where('id', $id);
			$success = $this->db->update('tblinternal_proposal', $data);
			if ($success) {
				$success = true;
				$message = _l('Hủy không duyệt thành công');


				$staff_assigned = $this->db->get_where('tblinternal_proposal_assigned', ['id_internal_proposal' => $id])->result_array();
				if(!empty($staff_assigned)) {
					$dataHtml = '
							<img src="' . base_url('uploads/foso.png') . '" class="staff-profile-image-small img-circle notification-image pull-left" alt="Hệ thống"> 
							Hệ thống - ' . get_staff_full_name($this->staffid) . ' Vừa hủy không duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')) . ' Vui lòng theo dõi và tiến hành cập nhật!
						';

					foreach($staff_assigned as $key => $value) {
						$notification_data = [
							'date' => date('Y-m-d H:i:s'),
							'description' => $dataHtml,
							'touserid' => $value['id_staff'],
							'link' => 'internal_proposal/view/' . $id,
							'type' => 13,
							'object_id' => $id,
							'object_type' => 'internal_proposal',
						];

						if (!empty($notification_data)) {
							$this->db->insert('tblnotifications', $notification_data);
							pusher_trigger_notification($notification_data);
						}
						send_notification_app_c($id, [
							'description' => 'Hệ thống - ' . get_staff_full_name($this->staffid) . ' vừa hủy không duyệt phiếu đề xuất nội bộ '. $internal_proposal->code . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
							'title' => 'Đổi trạng thái phiếu đề xuất nội bộ',
							'code' => $internal_proposal->code,
							'object_type' => 'internal_proposal'
						], [$value['id_staff']], $this->staffid);
					}
				}
			} else {
				$success = false;
				$message = _l('Hủy không duyệt không thành công');
			}
		}
		echo json_encode(array(
			'success' => $success,
			'message' => $message
		));
		die;
	}


	public function createTaskAuto($id = '') {
		$this->db->where('id', $id);
		$internal_proposal = $this->db->get('tblinternal_proposal')->row();
		$name = '';
		if(!empty($internal_proposal)) {
			if(!empty($internal_proposal->category_tasks)) {
				$this->db->where('id', $internal_proposal->category_tasks);
				$category_tasks = $this->db->get('tblcategory_tasks')->row();
				$staff_department = !empty($category_tasks) ? $category_tasks->departments : NULL;
				$name = !empty($category_tasks) ? $category_tasks->content : NULL;
			}

			$_data = [
				'name' => $name,
				'hourly_rate' => 0,
				'category_tasks' => $internal_proposal->category_tasks,
				'startdate' => $internal_proposal->date,
				'duedate' => NULL,
				'priority' => 2,
				'rel_type' => 'internal_proposal',
				'rel_id' => $id,
				'description' => $internal_proposal->content,
				'_addedfrom' => $this->staffid,
				'department_id' => !empty($staff_department) ? explode(',', $staff_department) : [],
				'id_branch' => $internal_proposal->id_branch,
			];
			$id_tasks = $this->tasks_model->add($_data, false, true);
			if (!empty($id_tasks)) {
				$staffNow = $this->staffid;
				$this->db->where('id_internal_proposal', $internal_proposal->id);
				$this->db->where('id_staff != "'.$staffNow.'"', false, false);
				$internal_assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
				if(!empty($internal_assigned)) {
					foreach($internal_assigned as $key => $value) {
						$this->db->insert('tbltask_followers', [
							'staffid' => $value['id_staff'],
							'taskid' => $id_tasks
						]);
					}
				}

				return $id_tasks;
			}
		}
		return false;
	}


	public function view($id = '') {
		if(!empty($id)) {
			if(!empty($this->is_branch)) {
				if (!is_admin($this->staffid)) {
					$list_branch = get_array_branch_staff($this->staffid);
					if (!empty($list_branch)) {
						$this->db->group_start();
						$this->db->where_in('tblinternal_proposal.id_branch', $list_branch);
						$this->db->group_end();
						$this->db->where('id', $id);
						$ktData = $this->db->get('tblinternal_proposal')->row();
					} else {
						$ktData = false;
					}

					if (empty($ktData)) {
						echo json_encode(array(
							'success' => false,
							'message' => 'Tài khoản không có quyền xem'
						));
						die;
					}
				}
			}


			$this->db->select('tblinternal_proposal.*, 
				tblcategory_tasks.code as code_category, 
				tblcategory_tasks.content as content_category,
				CONCAT(tblpurchases.prefix, tblpurchases.code) as code_purchase,
				CONCAT(tbl_services.prefix, tbl_services.code) as code_services,
				CONCAT(tblpurchase_order.prefix,"-", tblpurchase_order.code) as code_purchase_order,
				tblbranch.name as name_branch'
			);
			$this->db->where('tblinternal_proposal.id', $id);
			$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tblinternal_proposal.category_tasks');
			$this->db->join('tblpurchases', 'tblpurchases.id = tblinternal_proposal.id_purchases', 'left');
			$this->db->join('tbl_services', 'tbl_services.id = tblinternal_proposal.id_service', 'left');
			$this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblinternal_proposal.id_purchase_order', 'left');
			$this->db->join('tblbranch', 'tblbranch.id = tblinternal_proposal.id_branch', 'left');
			$internal_proposal = $this->db->get('tblinternal_proposal')->row();

			if(!$this->perView && $this->perViewOwn) {
				if(!empty($internal_proposal)) {
					$this->db->where('tblinternal_proposal.id', $id);
					$this->db->group_start();
					$this->db->where('EXISTS (
								SELECT 1 
								FROM tblinternal_proposal_assigned 
								WHERE tblinternal_proposal_assigned.id_internal_proposal = tblinternal_proposal.id
								AND (tblinternal_proposal_assigned.id_staff = "' . $this->staffid . '")
							)', false, false);
					$this->db->or_where('tblinternal_proposal.staff', $this->staffid);
					$this->db->group_end();
					$internal_proposal = $this->db->get('tblinternal_proposal')->row();
					if(empty($internal_proposal)) {
						echo json_encode(array(
							'success' => false,
							'message' => 'Bạn không có quyền truy cập'
						));
						die;
					}
				}
			}

			if(!empty($internal_proposal->staff)) {
				$internal_proposal->name_staff = get_staff_full_name($internal_proposal->staff);
				$internal_proposal->avatar_staff = staff_profile_image_url($internal_proposal->staff);
			}

			$this->db->where('id_internal_proposal', $id);
			$internal_proposal->assigned = $this->db->get('tblinternal_proposal_assigned')->result_array();
			if(!empty($internal_proposal->assigned)){
				foreach($internal_proposal->assigned as $k => $v) {
					$internal_proposal->assigned[$k]['name_staff'] = get_staff_full_name($v['id_staff']);
					$internal_proposal->assigned[$k]['avatar_staff'] = staff_profile_image_url($v['id_staff']);
				}
			}

			$this->db->where('rel_type', 'internal');
			$this->db->where('rel_id', $id);
			$internal_proposal->files = $this->db->get('tblfiles')->result_array();
			foreach($internal_proposal->files as $key => $value) {
				$internal_proposal->files[$key]['link'] = base_url('uploads/internal_proposal/'.$id.'/'.$value['file_name']);
			}
		}
		if(empty($internal_proposal)) {
			echo json_encode(array(
				'success' => false,
				'message' => '"Không tìm thấy phiếu đề xuất nội bộ'
			));
			die;
		}
		echo json_encode($internal_proposal);die();
	}


}
