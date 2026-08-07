<?php
defined('BASEPATH') or exit('No direct script access allowed');
class List_protocol extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $data['title'] = _l('c_title_list_protocol');
        $this->load->view('admin/list_protocol/manage', $data);
    }

    public function table() {
		$aColumns = [
			'id',
			'code',
			'name',
			'date_create',
			'create_by',
		];

		$where = [];
//		if ($this->ci->input->post('activity_log_date')) {
//			array_push($where, 'AND date LIKE "' . to_sql_date($this->ci->input->post('activity_log_date')) . '%"');
//		}
		$sIndexColumn = 'id';
		$sTable       = 'tbllist_protocol';
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
			$row[] = $profile_CREATE;

			$options = '<a href="'.admin_url('list_protocol/detail/' . $aRow['id']).'" class="btn btn-default btn-icon">
							<i class="fa fa-pencil-square-o"></i>
						</a>';

			$options .= '<a href="'.admin_url('list_protocol/delete/' . $aRow['id']).'" class="btn btn-danger _deleteC btn-icon">
							<i class="fa fa-remove"></i>
						</a>';
			$row[]  = $options;

			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}

	public function detail($id = '') {
    	if($this->input->post()) {
    		$content = $this->input->post('content', false);
    		$name = $this->input->post('name');
			if (!empty($id)) {
				$this->db->where('id', $id);
				$success = $this->db->update('tbllist_protocol', [
					'name' => $name,
					'content' => $content
				]);
				if(!empty($success)) {
					set_alert('success', _l('cong_update_true'));
				}
				else {
					set_alert('danger', _l('cong_update_false'));
				}
				redirect(admin_url('list_protocol'));
			}
			else {
				$this->db->select('MAX(id) as max_id');
				$code = $this->db->get('tbllist_protocol')->row('max_id') + 1;
				$success = $this->db->insert('tbllist_protocol', [
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
				redirect(admin_url('list_protocol'));
			}
		}
    	else {
    		$data['title'] = _l('c_create_list_protocol');
			if (!empty($id)) {
				$data['title'] = _l('c_edit_list_protocol');
				$data['list_protocol'] = $this->db->get_where('tbllist_protocol', ['id' => $id])->row();
				$data['id'] = $id;
			}
			$this->load->view('admin/list_protocol/detail', $data);
		}
	}

	public function delete($id = "") {
		$this->db->where('id', $id);
		$successDelete = $this->db->delete('tbllist_protocol');
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
}
