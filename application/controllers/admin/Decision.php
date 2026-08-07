<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Decision extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function category() {
		if(!has_permission('decision_category', '', 'view')) {
			access_denied();
		}
        $data['title'] = _l('c_title_category_decision');
        $this->load->view('admin/decision/category', $data);
    }

	public function table_category() {
		if(!has_permission('decision_category', '', 'view')) {
			access_denied();
		}
		$aColumns = [
			'id',
			'code',
			'name',
			'date_create',
			'create_by',
		];
		$where = [];
		$sIndexColumn = 'id';
		$sTable       = 'tblcategory_decision';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where);
		$output       = $result['output'];
		$rResult      = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = _dt($aRow['date_create']);

			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '</a>';
			$row[] = $profile_CREATE . ' ' . $fullname_CREATE;

            $options = '<a href="'.admin_url('decision/detail_category/' . $aRow['id']).'" class="btn btn-default btn-icon">
							<i class="fa fa-pencil-square-o"></i>
						</a>';

			$options .= '<a href="'.admin_url('decision/print_category/' . $aRow['id']).'" class="btn btn-default btn-icon" target="_blank">
							<i class="fa fa-print" aria-hidden="true"></i>
						</a>';

			$options .= '<a href="'.admin_url('decision/delete_category/' . $aRow['id']).'" class="btn btn-danger _deleteC btn-icon">
							<i class="fa fa-remove"></i>
						</a>';
			$row[]  = $options;

			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}

	public function detail_category($id = '') {
    	if($this->input->post()) {
    		$content = $this->input->post('content', false);
    		$name = $this->input->post('name');
			if (!empty($id)) {
				if(!has_permission('decision_category', '', 'edit')) {
					access_denied();
				}
				$this->db->where('id', $id);
				$success = $this->db->update('tblcategory_decision', [
					'name' => $name,
					'content' => $content
				]);
				if(!empty($success)) {
					set_alert('success', _l('cong_update_true'));
				}
				else {
					set_alert('danger', _l('cong_update_false'));
				}
				redirect(admin_url('decision/category'));
			}
			else {
				if(!has_permission('decision_category', '', 'create')) {
					access_denied();
				}
				$this->db->select('MAX(id) as max_id');
				$code = $this->db->get('tblcategory_decision')->row('max_id') + 1;
				$success = $this->db->insert('tblcategory_decision', [
					'code' => 'DMBB-' . sprintf("%06s", $code ),
					'name' => $name,
					'content' => $content,
					'create_by' => get_staff_user_id(),
				]);

				if(!empty($success)) {
					set_alert('success', _l('cong_add_true'));
				}
				else {
					set_alert('danger', _l('cong_add_false'));
				}
				redirect(admin_url('decision/category'));
			}
		}
    	else {
    		$data['title'] = _l('c_create_decision_category');
			if (!empty($id)) {
				if(!has_permission('decision_category', '', 'edit')) {
					access_denied();
				}
				$data['title'] = _l('c_edit_decision_category');
				$data['category_decision'] = $this->db->get_where('tblcategory_decision', ['id' => $id])->row();
				$data['id'] = $id;
			}
			else {
				if(!has_permission('decision_category', '', 'create')) {
					access_denied();
				}
			}
			$this->load->view('admin/decision/detail_category', $data);
		}
	}

	public function delete_category($id = "") {
		if(!has_permission('decision_category', '', 'delete')) {
			ajax_access_denied();
		}

		$this->db->where('id', $id);
		$successDelete = $this->db->delete('tblcategory_decision');
		if(!empty($successDelete)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => _l('cong_delete_true')
			]);die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('cong_delete_false')
		]);die();
	}

	public function list() {
		if(!has_permission('decision_list', '', 'view') && !has_permission('decision_list', '', 'view_own')) {
			access_denied();
		}
		$data['title'] = _l('c_title_list_decision');
		$data['category'] = $this->db->get_where('tblcategory_decision')->result_array();
		$data['staff'] = $this->db->get_where('tblstaff')->result_array();
		$this->load->view('admin/decision/list', $data);
	}

	public function table_list() {
		$aColumns = [
			'tbldecision.id as id',
			'tbldecision.code as code',
			'tbldecision.date as date',
			'tbldecision.staff_id as staff_id',
			'tblcategory_decision.code as code_category_decision',
			'tbldecision.status as status',
			'tbldecision.active as active',
			'tbldecision.note as note',
			'tbldecision.create_by as create_by',

		];
		$where = [];
		if(!has_permission('decision_list', '', 'view')) {
			$where[] = 'AND tbldecision.staff_id = "' . get_staff_user_id() . '"';
		}

		if(is_numeric($this->input->post('filterStatus'))) {
			$where[] = 'AND tbldecision.status = "'.$this->input->post('filterStatus').'"';
		}
		$join = [
			'LEFT JOIN tblcategory_decision ON tblcategory_decision.id = tbldecision.id_category'
		];
		$sIndexColumn = 'id';
		$sTable = 'tbldecision';
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tblcategory_decision.name as name_category_decision',
			'tbldecision.person_status',
			'tbldecision.user_status',
			'tbldecision.date_status',
		]);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = '<a class="c_modal" href="'.admin_url('decision/view/' . $aRow['id']).'">' . $aRow['code'] . '</a>';
			$row[] = _d($aRow['date']);
			$fullname_staff_id = get_staff_full_name($aRow['staff_id']);
			$profile_staff_id = '<a data-toggle="tooltip" data-title="' . $fullname_staff_id . '" href="' . admin_url('profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], [
					'staff-profile-image-small',
				]) . '<br/>' . $fullname_staff_id . '</a>';
			$row[] = $profile_staff_id;
			$row[] = $aRow['code_category_decision']. '(' . $aRow['name_category_decision'] . ')';
			if (!empty($aRow['date_status'])) {
				$date_status = _d($aRow['date_status']);
			}
			$user_status = $aRow['user_status'];
			$full_name = get_staff_full_name($user_status);
			$strApproveHtml = '';
			if (!empty($user_status)) {
				$strApproveHtml = '<a class="mright5 mtop5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $user_status) . '">' . staff_profile_image(
						$user_status,['staff-profile-image-small mbot5']
					) . '</a> <span>' . $date_status . '' .(!empty($aRow['person_status']) ? ('<br/>Lý do hủy: '. $aRow['person_status']) : '');
			}

			$strApprove = '';
			if ($aRow['status'] == 0) {
				$html = "<p><a id='agree' value='1' data-id='".$aRow['id']."' class='btn btn-success btn-icon'>Duyệt</a><a id='agree' value='2' data-id='".$aRow['id']."' class='btn btn-warning btn-icon'>Hủy Phiếu</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
				$strApprove = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
			}
			elseif ($aRow['status'] == 1) {
				$html = "<p><a id='agree' value='0' data-id='".$aRow['id']."' class='btn btn-danger btn-icon'>Bỏ duyệt</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
				$strApprove = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html.'" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
			}
			elseif ($aRow['status'] == 2) {
				$strApprove = '<div class="text-left mbot5"><span class="label label-danger btn-icon" data-original-title="Duyệt">Đã Hủy</span></div>';
			}
			$row[] = $strApprove . $strApproveHtml;



            $strActive = '';
            if ($aRow['active'] == 0) {
                $htmlActive = "<p><a id='agreeActive' value='1' data-id='".$aRow['id']."' class='btn btn-success btn-icon'>Sử dụng</a><a id='agreeActive' value='2' data-id='".$aRow['id']."' class='btn btn-danger btn-icon'>Ngừng sử dụng</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
                $strActive = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$htmlActive.'" class="label label-warning po" data-original-title="Duyệt">Chưa sử dụng</span></div>';
            }
            elseif ($aRow['active'] == 1) {
                $htmlActive = "<p><a id='agreeActive' value='0' data-id='".$aRow['id']."' class='btn btn-warning btn-icon'>Chưa sử dụng</a><button class='btn po-close  btn-icon'>Thoát</button></p>";
                $strActive = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$htmlActive.'" class="label label-success po" data-original-title="Duyệt">Sử dụng</span></div>';
            }
            elseif ($aRow['active'] == 2) {
                $strActive = '<div class="text-left mbot5"><span class="label label-danger btn-icon" data-original-title="Ngừng sử dụng">Ngừng sử dụng</span></div>';
            }
            $row[] = $strActive;

			$row[] = $aRow['note'];
			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '<br/>' . $fullname_CREATE . '</a>';
			$row[] = $profile_CREATE;
            $row[] = '<a class="btn btn-icon btn-info c_modal" href="'.admin_url('internal_proposal/add_modal?type_object=decision&id_object='.$aRow['id'].'&type_append=decision').'">Tạo Đề Xuất Nội Bộ</a>';
            $options = '<a href="' . admin_url('decision/detail_list/' . $aRow['id']) . '" class="btn btn-default btn-icon">
							<i class="fa fa-pencil-square-o"></i>
						</a>';

			$options .= '<a href="' . admin_url('decision/print/' . $aRow['id']) . '" class="btn btn-default btn-icon" target="_blank">
							<i class="fa fa-print" aria-hidden="true"></i>
						 </a>';

			$options .= '<a href="' . admin_url('decision/delete_list/' . $aRow['id']) . '" class="btn btn-danger _deleteC btn-icon">
							<i class="fa fa-remove"></i>
						</a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}

		$output['status'][0] = $this->db->get_where('tbldecision', ['status' => 0])->num_rows();
		$output['status'][1] = $this->db->get_where('tbldecision', ['status' => 1])->num_rows();
		$output['status'][2] = $this->db->get_where('tbldecision', ['status' => 2])->num_rows();
		$output['status']['all'] = $this->db->get_where('tbldecision')->num_rows();

		echo json_encode($output);
		die();
	}

	public function detail_list($id = '')
	{
		if ($this->input->post()) {
			$content = $this->input->post('content', false);
			$note = $this->input->post('note', false);
			$data = $this->input->post();
			if (!empty($id)) {

				if(!has_permission('decision_list', '', 'edit')) {
					access_denied();
				}

				$this->db->where('id', $id);
				$decision = $this->db->get('tbldecision')->row();
				if(!empty($decision->status)) {
					if($decision->status == 1) {
						set_alert('danger', _l('Phiếu đã duyêt không thể sửa'));
					}
					else if($decision->status == 2) {
						set_alert('danger', _l('Phiếu đã hủy không thể sửa'));
					}
					redirect(admin_url('violation_records/detail/' . $id));
				}

				$this->db->where('id', $id);
				$success = $this->db->update('tbldecision', [
					'date' => to_sql_date($data['date'], true),
					'staff_id' => $data['staff_id'],
					'id_category' => $data['id_category'],
					'note' => $note,
					'content' => $content,
                    'name' => !empty($data['name']) ? $data['date_issued'] : null,
                    'date_issued' => !empty($data['date_issued']) ? to_sql_date($data['date_issued'], true) : null,
                    'reissue_date' => !empty($data['reissue_date']) ? to_sql_date($data['reissue_date'], true) : null,
                    'adjustment_date' => !empty($data['reissue_date']) ? to_sql_date($data['adjustment_date'], true) : null,
                    'time_of_use' => !empty($data['time_of_use']) ? $data['time_of_use'] : null,
                    'active' => !empty($data['active']) ? $data['active'] : 0,
				]);
				if (!empty($success)) {
					set_alert('success', _l('cong_update_true'));
				} else {
					set_alert('danger', _l('cong_update_false'));
				}
				redirect(admin_url('decision/list'));
			}
			else {
				if(!has_permission('decision_list', '', 'create')) {
					access_denied();
				}
				$this->db->select('MAX(id) as max_id');
				$code = $this->db->get('tbldecision')->row('max_id') + 1;
				$success = $this->db->insert('tbldecision', [
					'code' => 'DMVP-' . sprintf("%06s", $code),
					'date' => to_sql_date($data['date'], true),
					'staff_id' => $data['staff_id'],
					'id_category' => $data['id_category'],
					'note' => $note,
					'content' => $content,
                    'name' => !empty($data['name']) ? $data['date_issued'] : null,
					'date_issued' => !empty($data['date_issued']) ? to_sql_date($data['date_issued'], true) : null,
					'reissue_date' => !empty($data['reissue_date']) ? to_sql_date($data['reissue_date'], true) : null,
					'adjustment_date' => !empty($data['reissue_date']) ? to_sql_date($data['adjustment_date'], true) : null,
					'time_of_use' => !empty($data['time_of_use']) ? $data['time_of_use'] : null,
					'active' => !empty($data['active']) ? $data['active'] : 0,
					'create_by' => get_staff_user_id(),
				]);
				if (!empty($success)) {
					set_alert('success', _l('cong_add_true'));
				} else {
					set_alert('danger', _l('cong_add_false'));
				}
				redirect(admin_url('decision/list'));
			}
		} else {
			$data['title'] = _l('c_create_decision');
			if (!empty($id)) {
				if(!has_permission('decision_list', '', 'edit')) {
					access_denied();
				}
				$data['title'] = _l('c_edit_decision');
				$data['decision'] = $this->db->get_where('tbldecision', ['id' => $id])->row();
				$data['id'] = $id;
				$data['decision']->data_content = $this->get_data_content($data['decision']->staff_id, $data['decision']->content);
			}
			else {
				if(!has_permission('decision_list', '', 'create')) {
					access_denied();
				}
			}
			$this->db->select('staffid, CONCAT(COALESCE(firstname), " ", COALESCE(lastname)) as fullname');
			$data['staff'] = $this->db->get('tblstaff')->result_array();

			$data['category'] = $this->db->get('tblcategory_decision')->result_array();
			$this->load->view('admin/decision/detail_list', $data);
		}
	}

	public function print_category($id = '') {



		ob_end_clean();
		$data = [];
		$this->db->where('tblcategory_decision.id', $id);
		$category_decision = $this->db->get('tblcategory_decision')->row();



		$data['title'] = lang('In quyết định');
		$data['type'] = 'P';
		$data['img'] = '';
//
//		$day = date_format(date_create($violation_records->date), 'd');
//		$month = date_format(date_create($violation_records->date), 'm');
//		$year = date_format(date_create($violation_records->date), 'Y');
//		$hours = date_format(date_create($violation_records->date), 'H');
//		$minute = date_format(date_create($violation_records->date), 'i');
		ob_start();
		stylePdf();

		echo $category_decision->content;

		$content = ob_get_contents();
		ob_end_clean();

		$data['content'] = $content;
		$data['showHeader'] = 'hide';
		$pdf = @print_pdf_tnh($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}

	function get_data_content($staffid = "", $content = '')
	{
		$this->db->select([
			'CONCAT(COALESCE(tblstaff.lastname)," ",COALESCE(tblstaff.firstname)) as fullname',
			'DATE_FORMAT(tblstaff.birthday, "%d/%m/%Y") as birthday',
			'DATE_FORMAT(tblstaff.date_range, "%d/%m/%Y") as date_range',
			'tblstaff.birthplace',
			'tblstaff.domicile',
			'tblstaff.cmnd_id_passport',
			'tblstaff.issued_by',
			'tblstaff.nationality',
			'tblstaff.personal_tax_code',
			'tblstaff.resident',
			'tblstaff.current_accommodation',
			'tblroles.name as name_role'
		]);
		$this->db->where('staffid', $staffid);
		$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
		$staff = $this->db->get('tblstaff')->row();
		if (!empty($staff)) {
			$this->db->select('GROUP_CONCAT(tbldepartments.name) as name_departments');
			$this->db->where('staffid', $staffid);
			$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid');
			$staff->name_departments = $this->db->get('tblstaff_departments')->row('name_departments');
			foreach ($staff as $key => $rom) {
				if(empty($rom)) {
					$rom = '';
				}
				$content = @preg_replace('"{' . $key . '}"', $rom, $content);
			}
		}
		return $content;
	}

	function get_info_category($id_category = '') {
		if(!empty($id_category)) {
			$this->db->where('id', $id_category);
			$category_decision = $this->db->get('tblcategory_decision')->row();
			echo json_encode($category_decision);die();
		}
		echo json_encode([]);die();
	}

	public function delete_list($id = "") {
		if(!has_permission('decision_list', '', 'delete')) {
			ajax_access_denied();
		}
		$successDelete = false;
		$this->db->where('id', $id);
		$decision= $this->db->get('tbldecision')->row();
		if(!empty($decision)) {
			if($decision->status == 1) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('Phiếu đã duyệt không thể xóa')
				]);die();
			}
			$this->db->where('id', $id);
			$successDelete = $this->db->delete('tbldecision');
		}
		if(!empty($successDelete)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => _l('cong_delete_true')
			]);die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('cong_delete_false')
		]);die();
	}

	public function update_status() {
		if(!has_permission('decision_list', '', 'approve')) {
			ajax_access_denied();
		}
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		if(!empty($id)) {
			$this->db->where('id', $id);
			$decision = $this->db->get('tbldecision')->row();

			if($decision->status == $status) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('Phiếu đang ở trạng thái này không thể duyệt được nữa')
				]);die();
			}
			$data_update = ['status' => $status];
			if(!empty($status)) {
				$data_update['user_status'] = get_staff_user_id();
				$data_update['date_status'] = date('Y-m-d H:i:s');
			}
			else {
				$data_update['user_status'] = NULL;
				$data_update['date_status'] = NULL;
			}
			$this->db->where('id', $id);
			$success = $this->db->update('tbldecision', $data_update);
			if(!empty($success)) {
				echo json_encode([
					'success' => $success,
					'alert_type' => 'success',
					'message' => _l('cong_update_true')
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('cong_update_false')
		]);die();
	}


	public function update_active() {
		if(!has_permission('decision_list', '', 'approve')) {
			ajax_access_denied();
		}
		$id = $this->input->post('id');
		$active = $this->input->post('active');
		if(!empty($id)) {
			$this->db->where('id', $id);
			$decision = $this->db->get('tbldecision')->row();

			if($decision->active == $active) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('Phiếu đang ở trạng thái này không thể duyệt được nữa')
				]);die();
			}
			$data_update = ['active' => $active];
			$this->db->where('id', $id);
			$success = $this->db->update('tbldecision', $data_update);
			if(!empty($success)) {
				echo json_encode([
					'success' => $success,
					'alert_type' => 'success',
					'message' => _l('cong_update_true')
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('cong_update_false')
		]);die();
	}

	public function get_data_html_content()
	{
		$staff_id = $this->input->post('staff_id');
		$content = $this->input->post('content', false);
		echo ($this->get_data_content($staff_id,  $content));die();
	}

	public function view($id = '')
	{

		$data['title'] = _l('Xem phiếu quyết định');
		if (!empty($id)) {
			$this->db->select('tbldecision.*, tblcategory_decision.name as name_category, tblcategory_decision.code as code_category');
			$this->db->join('tblcategory_decision', 'tblcategory_decision.id = tbldecision.id_category', 'left');
			$data['decision'] = $this->db->get_where('tbldecision', ['tbldecision.id' => $id])->row();
			$data['id'] = $id;
			$data['decision']->data_content = $this->get_data_content($data['decision']->staff_id, $data['decision']->content);
		}

		if(!has_permission('decision_list', '', 'view')  && $data['decision']->staff_id != get_staff_user_id()) {
			ajax_access_denied();
		}

		$fullname_staff_id = get_staff_full_name($data['decision']->staff_id);
		$data['decision']->staff_id = '<a data-toggle="tooltip" data-title="' . $fullname_staff_id . '" href="' . admin_url('profile/' . $data['decision']->staff_id) . '">' . staff_profile_image($data['violation_records']->staff_id, [
				'staff-profile-image-small',
			]) . $fullname_staff_id . '</a>';

		if(!empty($data['decision']->user_status)) {
			$fullname_staff_user_status = get_staff_full_name($data['decision']->user_status);
			$data['decision']->user_status = '<a data-toggle="tooltip" data-title="' . $fullname_staff_user_status . '" href="' . admin_url('profile/' . $data['decision']->user_status) . '">' . staff_profile_image($data['violation_records']->user_status, [
					'staff-profile-image-small',
				]) . $fullname_staff_user_status . '</a>';
		}
		$this->load->view('admin/decision/modal', $data);
	}

	public function print($id = '') {
		if(!has_permission('decision_list', '', 'print')) {
			access_denied();
		}
		ob_end_clean();
		$data = [];

		$this->db->select([
			'tbldecision.id as id',
			'tbldecision.code as code',
			'tbldecision.date as date',
			'tbldecision.staff_id as staff_id',
			'tblcategory_decision.code as code_category',
			'tbldecision.status as status',
			'tbldecision.note as note',
			'tbldecision.create_by as create_by',
			'tbldecision.date_status as date_status',
			'tbldecision.user_status as user_status',
			'tbldecision.person_status as person_status',
			'CONCAT(COALESCE(tblstaff.lastname)," ",COALESCE(tblstaff.firstname)) as fullname',
			'tbldecision.content'
		]);
		$this->db->join('tblcategory_decision', 'tblcategory_decision.id = tbldecision.id_category', 'left', false);
		$this->db->join('tblstaff', 'tblstaff.staffid = tbldecision.staff_id', 'left', false);
		$this->db->where('tbldecision.id', $id);
		$decision = $this->db->get('tbldecision')->row();

		$this->db->select('GROUP_CONCAT(tbldepartments.name) as name_departments');
		$this->db->where('staffid', $decision->staff_id);
		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid');
		$decision->name_departments = $this->db->get('tblstaff_departments')->row('name_departments');
		$decision->content = $this->get_data_content($decision->staff_id, $decision->content);


		$data['title'] = lang('In quyết định');
		$data['type'] = 'P';
		$data['img'] = '';
//
//		$day = date_format(date_create($violation_records->date), 'd');
//		$month = date_format(date_create($violation_records->date), 'm');
//		$year = date_format(date_create($violation_records->date), 'Y');
//		$hours = date_format(date_create($violation_records->date), 'H');
//		$minute = date_format(date_create($violation_records->date), 'i');
		ob_start();
		stylePdf();

		echo $decision->content;

		$content = ob_get_contents();
		ob_end_clean();

		$data['content'] = $content;
		$data['showHeader'] = 'hide';
		$pdf = @print_pdf_tnh($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}
}
