<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Violation_records extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('kpi_model');
	}

	public function index()
	{
		$data['title'] = _l('c_title_violation_records');
		$this->load->view('admin/violation_records/manage', $data);
	}

	public function table()
	{
		$aColumns = [
			'tblviolation_records.id as id',
			'tblviolation_records.code as code',
			'tblviolation_records.date as date',
			'tblviolation_records.staff_id as staff_id',
			'tbllist_protocol.code as code_list_protocol',
			'tblviolation_records.object_type as object_type',
			'tblviolation_records.object_id as object_id',
			'tblviolation_records.status as status',
			'tblviolation_records.status_staff as status_staff',
			'tblviolation_records.object_id as object_id',
			'tblviolation_records.note as note',
			'tblviolation_records.create_by as create_by',
		];
		$where = [];

		if(is_numeric($this->input->post('filterStatus'))) {
			$where[] = 'AND tblviolation_records.status = "'.$this->input->post('filterStatus').'"';
		}
		$join = [
			'LEFT JOIN tbl_orders ON tbl_orders.id = tblviolation_records.object_id AND tblviolation_records.object_type = "orders"',
			'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tblviolation_records.object_id AND tblviolation_records.object_type = "productions_orders_detail"',
			'LEFT JOIN tblpurchase_order ON tblpurchase_order.id = tblviolation_records.object_id AND tblviolation_records.object_type = "purchase_order"',
			'LEFT JOIN tbltasks ON tbltasks.id = tblviolation_records.object_id AND tblviolation_records.object_type = "tasks"',
			'LEFT JOIN tbl_check_quality ON tbl_check_quality.id = tblviolation_records.object_id AND tblviolation_records.object_type = "qc"',
			'LEFT JOIN tbllist_protocol ON tbllist_protocol.id = tblviolation_records.id_list_protocol',
		];
		$sIndexColumn = 'id';
		$sTable = 'tblviolation_records';
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tbl_orders.reference_no as code_orders',
			'tbl_productions_orders_details.reference_no as code_productions_orders_detail',
			'CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) as code_purchase_order',
			'CONCAT(tbltasks.name, "-", tbltasks.description) as code_tasks',
			'tbl_check_quality.reference_no as code_qc',
			'tbllist_protocol.name as name_list_protocol',
			'tblviolation_records.user_status as user_status',
			'tblviolation_records.date_status as date_status',
			'tblviolation_records.user_status_staff as user_status_staff',
			'tblviolation_records.date_status_staff as date_status_staff',
			'tblviolation_records.person_status as person_status',
			'tblviolation_records.stages as stages',
		]);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = '<a class="c_modal" href="'.admin_url('violation_records/view/' . $aRow['id']).'">' . $aRow['code'] . '</a>';
			$row[] = _dt($aRow['date']);
			$fullname_staff_id = get_staff_full_name($aRow['staff_id']);
			$profile_staff_id = '<a data-toggle="tooltip" data-title="' . $fullname_staff_id . '" href="' . admin_url('profile/' . $aRow['staff_id']) . '">' . staff_profile_image($aRow['staff_id'], [
					'staff-profile-image-small',
				]) . '<br/>' . $fullname_staff_id . '</a>';
			$row[] = $profile_staff_id;
			$row[] = $aRow['code_list_protocol']. '(' . $aRow['name_list_protocol'] . ')';
			$row[] = _l('c_object_' . $aRow['object_type']);
			$object_id = '';
			if ($aRow['object_type'] == 'orders') {
				$object_id = $aRow['code_orders'];
			} else if ($aRow['object_type'] == 'productions_orders_detail') {
				$object_id = $aRow['code_productions_orders_detail'];
			} else if ($aRow['object_type'] == 'purchase_order') {
				$object_id = $aRow['code_purchase_order'];
			} else if ($aRow['object_type'] == 'tasks') {
				$object_id = $aRow['code_tasks'];
			} else if ($aRow['object_type'] == 'qc') {
				$object_id = $aRow['code_qc'];
			}

			if(!empty($aRow['stages'])) {
				$this->db->where('id', $aRow['stages']);
				$_stages = $this->db->get('tbl_stages')->row();
				$object_id.= '<br/>(' . $_stages->name . ')';
			}
			$row[] = $object_id;

			$user_status = $aRow['user_status'];
			if (!empty($aRow['date_status'])) {
				$date_status = _d($aRow['date_status']);
			}
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


			$user_status_staff = $aRow['user_status_staff'];
			if (!empty($aRow['date_status_staff'])) {
				$date_status_staff = _d($aRow['date_status_staff']);
			}
			$full_name_staff = get_staff_full_name($user_status_staff);
			$strApproveHtml_staff = '';
			if (!empty($user_status_staff)) {
				$strApproveHtml_staff = '<a class="mright5 mtop5" data-toggle="tooltip" data-title="' . $full_name_staff . '" href="' . admin_url('profile/' . $user_status_staff) . '">' . staff_profile_image(
						$user_status_staff,['staff-profile-image-small mbot5']
					) . '</a> <span>' . $date_status_staff . '';
			}
			$strApprove_staff = '';

			if ($aRow['status_staff'] == 0) {
				$html_staff = "<p><a id='agree_staff' value='1' data-id='".$aRow['id']."' class='btn btn-success btn-icon'>Duyệt</a><button class='btn po-close'>Thoát</button></p>";
				$strApprove_staff = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html_staff.'" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
			}
			elseif ($aRow['status_staff'] == 1) {
				$html_staff = "<p><a id='agree_staff' value='0' data-id='".$aRow['id']."' class='btn btn-danger btn-icon'>Bỏ duyệt</a><button class='btn po-close'>Thoát</button></p>";
				$strApprove_staff = '<div class="text-left mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="'.$html_staff.'" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
			}
			$row[] = $strApprove_staff . $strApproveHtml_staff;




			$row[] = $aRow['note'];
			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '<br/>' . $fullname_CREATE . '</a>';
			$row[] = $profile_CREATE;
			$options = '<a href="' . admin_url('violation_records/detail/' . $aRow['id']) . '" class="btn btn-default btn-icon">
							<i class="fa fa-pencil-square-o"></i>
						</a>';

			$options .= '<a href="' . admin_url('violation_records/print_pdf/' . $aRow['id']) . '" class="btn btn-default btn-icon">
							<i class="fa fa-print" aria-hidden="true"></i>
						 </a>';

			$options .= '<a href="' . admin_url('violation_records/delete/' . $aRow['id']) . '" class="btn btn-danger _deleteC btn-icon">
							<i class="fa fa-remove"></i>
						</a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}

		$output['status'][0] = $this->db->get_where('tblviolation_records', ['status' => 0])->num_rows();
		$output['status'][1] = $this->db->get_where('tblviolation_records', ['status' => 1])->num_rows();
		$output['status'][2] = $this->db->get_where('tblviolation_records', ['status' => 2])->num_rows();
		$output['status']['all'] = $this->db->get_where('tblviolation_records')->num_rows();

		echo json_encode($output);
		die();
	}

	public function detail($id = '')
	{
		if ($this->input->post()) {
			$content = $this->input->post('content', false);
			$forms_processing = $this->input->post('forms_processing', false);
			$note = $this->input->post('note', false);
			$data = $this->input->post();
			if (!empty($id)) {
				$this->db->where('id', $id);
				$violation_records = $this->db->get('tblviolation_records')->row();
				if(!empty($violation_records->status)) {
					if($violation_records->status == 1) {
						set_alert('danger', _l('Phiếu đã duyêt không thể sửa'));
					}
					else if($violation_records->status == 2) {
						set_alert('danger', _l('Phiếu đã hủy không thể sửa'));
					}
					redirect(admin_url('violation_records/detail/' . $id));
				}

				$this->db->where('id', $id);
				$success = $this->db->update('tblviolation_records', [
					'date' => to_sql_date($data['date'], true),
					'staff_id' => $data['staff_id'],
					'id_list_protocol' => $data['id_list_protocol'],
					'object_type' => $data['object_type'],
					'object_id' => $data['object_id'],
					'note' => $note,
					'content' => $content,
					'stages' => !empty($data['stages']) ? $data['stages'] : NULL,
					'cal_kpi' => !empty($data['cal_kpi']) ? $data['cal_kpi'] : 0,
					'kpi_criteria' => !empty($data['kpi_criteria']) ? $data['kpi_criteria'] : 0,
					'forms_processing' => $forms_processing,
				]);
				if (!empty($success)) {
					set_alert('success', _l('cong_update_true'));
				} else {
					set_alert('danger', _l('cong_update_false'));
				}
				redirect(admin_url('violation_records'));
			}
			else {
				$this->db->select('MAX(id) as max_id');
				$code = $this->db->get('tblviolation_records')->row('max_id') + 1;
				$success = $this->db->insert('tblviolation_records', [
					'code' => 'DMVP-' . sprintf("%06s", $code),
					'date' => to_sql_date($data['date'], true),
					'staff_id' => $data['staff_id'],
					'id_list_protocol' => $data['id_list_protocol'],
					'object_type' => $data['object_type'],
					'object_id' => $data['object_id'],
					'note' => $note,
					'content' => $content,
					'forms_processing' => $forms_processing,
					'stages' => !empty($data['stages']) ? $data['stages'] : NULL,
					'cal_kpi' => !empty($data['cal_kpi']) ? $data['cal_kpi'] : 0,
					'kpi_criteria' => !empty($data['kpi_criteria']) ? $data['kpi_criteria'] : 0,
					'create_by' => get_staff_user_id(),
				]);
				if (!empty($success)) {
					set_alert('success', _l('cong_add_true'));
				} else {
					set_alert('danger', _l('cong_add_false'));
				}
				redirect(admin_url('violation_records'));
			}
		}
		else {
			$data['title'] = _l('c_create_violation_records');
			if (!empty($id)) {
				$data['title'] = _l('c_edit_list_protocol');
				$data['violation_records'] = $this->db->get_where('tblviolation_records', ['id' => $id])->row();
				$data['id'] = $id;
				if ($data['violation_records']->object_type == 'orders') {
					$this->db->select('reference_no as code, id as id');
					$data['object_id'] = $this->db->get('tbl_orders')->result_array();
				} else if ($data['violation_records']->object_type == 'productions_orders_detail') {
//					$this->db->select('reference_no as code, id as id');
//					$data['object_id'] = $this->db->get('tbl_productions_orders')->result_array();

					$this->db->select([
						'tbl_productions_orders.reference_no as code',
						'tbl_productions_orders_details.reference_no as code_detail',
						'tbl_productions_orders.id as id',
						'tbl_productions_orders_details.id as id_detail',
						'tbl_productions_orders_items.items_code as items_code',
						'tbl_productions_orders_items.items_name as items_name',
						'tbl_orders.reference_no as code_orders',
					]);
					$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
					$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
					$this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
					$this->db->order_by('tbl_productions_orders.id', 'desc');
					$_production_data = $this->db->get('tbl_productions_orders')->result_array();
					if(!empty($_production_data)) {
						$data['object_id'] = [];
						foreach($_production_data as $key => $value) {
							$data['object_id'][$value['id']][] = $value;
						}
					}
				} else if ($data['violation_records']->object_type == 'purchase_order') {
					$this->db->select('CONCAT(prefix, "-", code) as code, id as id');
					$data['object_id'] = $this->db->get('tblpurchase_order')->result_array();
				} else if ($data['violation_records']->object_type == 'tasks') {
					$this->db->select('CONCAT(name, "-", description) as code, id as id');
					$data['object_id'] = $this->db->get('tbltasks')->result_array();
				} else if ($data['violation_records']->object_type == 'qc') {
					$this->db->select('reference_no as code, id as id');
					$data['object_id'] = $this->db->get('tbl_check_quality')->result_array();
				}
				else if ($data['violation_records']->object_type == 'production_report') {
					$this->db->select('CONCAT(name_report, " - ", DATE_FORMAT(date, "%d/%m/%Y %H:%i:%s")) as code, id as id');
					$data['object_id'] = $this->db->get('tblproduction_report')->result_array();
				}
				$data['violation_records']->data_content = $this->get_data_content($data['violation_records']->staff_id, $data['violation_records']->content);
			}
			$this->db->select('staffid, CONCAT(COALESCE(firstname), " ", COALESCE(lastname)) as fullname');
			$data['staff'] = $this->db->get('tblstaff')->result_array();
			$data['list_protocol'] = $this->db->get('tbllist_protocol')->result_array();

			$data['kpi_criteria'] = $this->kpi_model->getKpiCriteria();

			$data['stages'] = $this->db->get('tbl_stages')->result_array();
			$this->load->view('admin/violation_records/detail', $data);
		}
	}

	public function view($id = '')
	{
		$data['title'] = _l('Xem bên bản vi phạm');
		if (!empty($id)) {
			$this->db->select('tblviolation_records.*, tbllist_protocol.name as name_list_protocol, tbllist_protocol.code as code_list_protocol');
			$this->db->join('tbllist_protocol', 'tbllist_protocol.id = tblviolation_records.id_list_protocol', 'left');
			$data['violation_records'] = $this->db->get_where('tblviolation_records', ['tblviolation_records.id' => $id])->row();
			$data['id'] = $id;
			if ($data['violation_records']->object_type == 'orders') {
				$this->db->select('reference_no as code');
				$this->db->where('id', $data['violation_records']->object_id);
				$data['violation_records']->object_id = $this->db->get('tbl_orders')->row('code');
			} else if ($data['violation_records']->object_type == 'productions_orders_detail') {
				$this->db->select([
					'tbl_productions_orders.reference_no as code',
					'tbl_productions_orders_details.reference_no as code_detail',
					'tbl_productions_orders.id as id',
					'tbl_productions_orders_details.id as id_detail',
					'tbl_productions_orders_items.items_code as items_code',
					'tbl_productions_orders_items.items_name as items_name',
					'tbl_orders.reference_no as code_orders',
				]);

				$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
				$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
				$this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
				$this->db->where('tbl_productions_orders_details.id', $data['violation_records']->object_id);
				$this->db->order_by('tbl_productions_orders.id', 'desc');
				$_production_data = $this->db->get('tbl_productions_orders')->row();
				$stages = $this->db->get_where('tbl_stages', ['id' => $data['violation_records']->stages])->row();
				$data['violation_records']->object_id = $_production_data->code_detail .' ('.$stages->name.')'. '<br/><b>Mã đơn hàng:</b> '.$_production_data->code_orders . '<br/><b>Mã sản phẩm:</b> '. $_production_data->items_code;



			} else if ($data['violation_records']->object_type == 'purchase_order') {
				$this->db->select('CONCAT(prefix, "-", code) as code, id as id');
				$this->db->where('id', $data['violation_records']->object_id);
				$data['violation_records']->object_id = $this->db->get('tblpurchase_order')->row('code');
			} else if ($data['violation_records']->object_type == 'tasks') {
				$this->db->select('CONCAT(name, "-", description) as code, id as id');
				$this->db->where('id', $data['violation_records']->object_id);
				$data['violation_records']->object_id = $this->db->get('tbltasks')->row('code');
			} else if ($data['violation_records']->object_type == 'qc') {
				$this->db->select('reference_no as code, id as id');
				$this->db->where('id', $data['violation_records']->object_id);
				$data['violation_records']->object_id = $this->db->get('tbl_check_quality')->row('code');
			}
			else if ($data['violation_records']->object_type == 'production_report') {
				$this->db->select('CONCAT(name_report, " - ", DATE_FORMAT(date, "%d/%m/%Y %H:%i:%s")) as code, id as id');
				$this->db->where('id', $data['violation_records']->object_id);
				$data['violation_records']->object_id = $this->db->get('tblproduction_report')->row('code');
			}
			$data['violation_records']->data_content = $this->get_data_content($data['violation_records']->staff_id, $data['violation_records']->content);
		}

		$fullname_staff_id = get_staff_full_name($data['violation_records']->staff_id);
		$data['violation_records']->staff_id = '<a data-toggle="tooltip" data-title="' . $fullname_staff_id . '" href="' . admin_url('profile/' . $data['violation_records']->staff_id) . '">' . staff_profile_image($data['violation_records']->staff_id, [
				'staff-profile-image-small',
			]) . $fullname_staff_id . '</a>';

		if(!empty($data['violation_records']->user_status)) {
			$fullname_staff_user_status = get_staff_full_name($data['violation_records']->user_status);
			$data['violation_records']->user_status = '<a data-toggle="tooltip" data-title="' . $fullname_staff_user_status . '" href="' . admin_url('profile/' . $data['violation_records']->user_status) . '">' . staff_profile_image($data['violation_records']->user_status, [
					'staff-profile-image-small',
				]) . $fullname_staff_user_status . '</a>';
		}

		if(!empty($data['violation_records']->user_status_staff)) {
			$fullname_staff_user_status_staff = get_staff_full_name($data['violation_records']->user_status_staff);
			$data['violation_records']->user_status_staff = '<a data-toggle="tooltip" data-title="' . $fullname_staff_user_status . '" href="' . admin_url('profile/' . $data['violation_records']->user_status_staff) . '">' . staff_profile_image($data['violation_records']->user_status_staff, [
					'staff-profile-image-small',
				]) . $fullname_staff_user_status_staff . '</a>';
		}

		$this->db->where('id_violation_records', $id);
		$this->db->order_by('date_create', 'desc');
		$data['feedback'] = $this->db->get('tblviolation_records_feedback')->result();
		foreach ($data['feedback'] as $key => $value) {
			$this->db->where('rel_id', $value->id);
			$this->db->where('rel_type', 'feedback_vr');
			$data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
		}

		$this->load->view('admin/violation_records/modal', $data);
	}

	public function get_info_list_protocol($id = '')
	{
		if (!empty($id)) {
			$this->db->where('id', $id);
			$list_protocol = $this->db->get('tbllist_protocol')->row();
			echo json_encode($list_protocol);
			die();
		}
		echo json_encode([]);
		die();
	}

	public function get_info_object_type()
	{
		$object_type = $this->input->post('object_type');
		$staff_id = $this->input->post('staff_id');
		$data = [];
		if ($object_type == 'orders') {
			$this->db->select('reference_no as code, id as id');
			$data = $this->db->get('tbl_orders')->result_array();
		} else if ($object_type == 'productions_orders_detail') {

//			$this->db->select([
//				'tbl_productions_orders.reference_no as code',
//				'tbl_productions_orders_details.id as id',
//				'tbl_productions_orders_items.items_code as items_code',
//				'tbl_productions_orders_items.items_name as items_name',
//			]);
//			$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
//			$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
//			$data = $this->db->get('tbl_productions_orders')->result_array();


		} else if ($object_type == 'purchase_order') {
			$this->db->select('CONCAT(prefix, "-", code) as code, id as id');
			$data = $this->db->get('tblpurchase_order')->result_array();
		} else if ($object_type == 'tasks') {
			$this->db->select('CONCAT(name, "-", description) as code, id as id');
			$data = $this->db->get('tbltasks')->result_array();
		}
		else if ($object_type == 'qc') {
			$this->db->select('reference_no as code, id as id');
			$data = $this->db->get('tbl_check_quality')->result_array();
		}
		else if ($object_type == 'production_report') {
			$this->db->select('CONCAT(name_report, " - ", DATE_FORMAT(date, "%d/%m/%Y %H:%i:%s")) as code, id as id');
			$data = $this->db->get('tblproduction_report')->result_array();
		}
		echo json_encode($data);
		die();
	}

	public function get_info_productions_orders()
	{
		$this->db->select([
			'tbl_productions_orders.reference_no as code',
			'tbl_productions_orders_details.reference_no as code_detail',
			'tbl_productions_orders.id as id',
			'tbl_productions_orders_details.id as id_detail',
			'tbl_productions_orders_items.items_code as items_code',
			'tbl_productions_orders_items.items_name as items_name',
			'tbl_orders.reference_no as code_orders',
		]);
		$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
		$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
		$this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
		$this->db->order_by('tbl_productions_orders.id', 'desc');
		$data = $this->db->get('tbl_productions_orders')->result_array();
		if(!empty($data)) {
			$_data = [];
			foreach($data as $key => $value) {
				$_data[$value['id']][] = $value;
			}
		}

		echo json_encode($_data);
		die();
	}

	public function delete($id = "")
	{
		$this->db->where('id', $id);
		$violation_records = $this->db->get('tblviolation_records')->row();
		if($violation_records->status == 1) {
			echo json_encode([
				'success' => false,
				'alert_type' => 'danger',
				'message' => _l('Phiếu đã duyệt không thể xóa')
			]);
			die();
		}

		$this->db->where('id', $id);
		$successDelete = $this->db->delete('tblviolation_records');
		if (!empty($successDelete)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => _l('cong_delete_true')
			]);
			die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => _l('cong_delete_false')
		]);
		die();
	}

	function get_data_content($staffid = "", $content = '')
	{
		$this->db->select([
			'CONCAT(COALESCE(tblstaff.lastname)," ",COALESCE(tblstaff.firstname)) as fullname',
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

	public function get_data_html_content()
	{
		$staff_id = $this->input->post('staff_id');
		$content = $this->input->post('content');
		echo ($this->get_data_content($staff_id, $content));die();
	}


	public function update_status_admin() {
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		$person_status = $this->input->post('person_status');
		if(!empty($id)) {

			$this->db->where('id', $id);
			$violation_records = $this->db->get('tblviolation_records')->row();
			if($violation_records->status == $status) {
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
				$data_update['person_status'] = $person_status;
			}
			else {
				$data_update['user_status'] = NULL;
				$data_update['date_status'] = NULL;
				$data_update['person_status'] = NULL;
			}
			$this->db->where('id', $id);
			$success = $this->db->update('tblviolation_records', $data_update);
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

	public function update_status_staff() {
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		if(!empty($id)) {

			$this->db->where('id', $id);
			$violation_records = $this->db->get('tblviolation_records')->row();
			if($violation_records->staff_id != get_staff_user_id() && !is_admin()) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('Chỉ có nhân viên vi phạm mới có quyền xác nhận')
				]);die();
			}

			if($violation_records->status_staff == $status) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('Phiếu đang ở trạng thái này không thể duyệt được nữa')
				]);die();
			}


			$data_update = ['status_staff' => $status];
			if(!empty($status)) {
				$data_update['user_status_staff'] = get_staff_user_id();
				$data_update['date_status_staff'] = date('Y-m-d H:i:s');
			}
			else {
				$data_update['user_status_staff'] = NULL;
				$data_update['date_status_staff'] = NULL;
			}
			$this->db->where('id', $id);
			$success = $this->db->update('tblviolation_records', $data_update);
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

	public function print_pdf($id = '') {
//		if (!$this->perPrintOrders) {
//			accessDenied();
//		}
		ob_end_clean();
		$data = [];

		$this->db->select([
			'tblviolation_records.id as id',
			'tblviolation_records.code as code',
			'tblviolation_records.date as date',
			'tblviolation_records.staff_id as staff_id',
			'tbllist_protocol.code as code_list_protocol',
			'tblviolation_records.object_type as object_type',
			'tblviolation_records.object_id as object_id',
			'tblviolation_records.status as status',
			'tblviolation_records.status_staff as status_staff',
			'tblviolation_records.object_id as object_id',
			'tblviolation_records.note as note',
			'tblviolation_records.create_by as create_by',
			'tbl_orders.reference_no as code_orders',
			'tbl_productions_orders_details.reference_no as code_productions_orders',
			'CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.code) as code_purchase_order',
			'CONCAT(tbltasks.name, "-", tbltasks.description) as code_tasks',
			'tbl_check_quality.reference_no as code_qc',
			'tblproduction_report.name_report as name_report',
			'tbllist_protocol.name as name_list_protocol',
			'tblviolation_records.user_status as user_status',
			'tblviolation_records.date_status as date_status',
			'tblviolation_records.user_status_staff as user_status_staff',
			'tblviolation_records.date_status_staff as date_status_staff',
			'tblviolation_records.person_status as person_status',
			'CONCAT(COALESCE(tblstaff.lastname)," ",COALESCE(tblstaff.firstname)) as fullname',
			'tblviolation_records.content',
			'tblviolation_records.forms_processing',
			'tblviolation_records.stages',
		]);
		$this->db->join('tbl_orders', 'tbl_orders.id = tblviolation_records.object_id AND tblviolation_records.object_type = "orders"', 'left', false);
		$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tblviolation_records.object_id AND tblviolation_records.object_type = "productions_orders_detail"', 'left', false);
		$this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblviolation_records.object_id AND tblviolation_records.object_type = "purchase_order"', 'left', false);
		$this->db->join('tbltasks', 'tbltasks.id = tblviolation_records.object_id AND tblviolation_records.object_type = "tasks"', 'left', false);
		$this->db->join('tbl_check_quality', 'tbl_check_quality.id = tbl_check_quality.id = tblviolation_records.object_id AND tblviolation_records.object_type = "qc"', 'left', false);
		$this->db->join('tblproduction_report', 'tblproduction_report.id = tblviolation_records.object_id AND tblviolation_records.object_type = "production_report"', 'left', false);
		$this->db->join('tbllist_protocol', 'tbllist_protocol.id = tblviolation_records.id_list_protocol', 'left', false);
		$this->db->join('tblstaff', 'tblstaff.staffid = tblviolation_records.staff_id', 'left', false);
		$this->db->where('tblviolation_records.id', $id);
		$violation_records = $this->db->get('tblviolation_records')->row();

		$this->db->select('GROUP_CONCAT(tbldepartments.name) as name_departments');
		$this->db->where('staffid', $violation_records->staff_id);
		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid');
		$violation_records->name_departments = $this->db->get('tblstaff_departments')->row('name_departments');

		$name_object = _l('c_object_' . $violation_records->object_type);
		$object_id_add = '';
		$object_id = '';
		if ($violation_records->object_type == 'orders') {
			$object_id = $violation_records->code_orders;
		} else if ($violation_records->object_type == 'productions_orders_detail') {
			$object_id = $violation_records->code_productions_orders;

			$this->db->select([
				'tbl_productions_orders.reference_no as code',
				'tbl_productions_orders_details.reference_no as code_detail',
				'tbl_productions_orders.id as id',
				'tbl_productions_orders_details.id as id_detail',
				'tbl_productions_orders_items.items_code as items_code',
				'tbl_productions_orders_items.items_name as items_name',
				'tbl_orders.reference_no as code_orders',
			]);

			$this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id');
			$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
			$this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
			$this->db->where('tbl_productions_orders_details.id', $violation_records->object_id);
			$this->db->order_by('tbl_productions_orders.id', 'desc');
			$_production_data = $this->db->get('tbl_productions_orders')->row();


			$stages = $this->db->get_where('tbl_stages', ['id' => $violation_records->stages])->row();
			$object_id = $_production_data->code_detail. ' ('.$stages->name.')';
//			$object_id_add = '<tr><td style="text-align: left" colspan="2"><b>Mã lệnh SXCT:</b> '.$_production_data->code_detail . ' ('.$stages->name.')</td></tr>';





		} else if ($violation_records->object_type == 'purchase_order') {
			$object_id = $violation_records->code_purchase_order;
		} else if ($violation_records->object_type == 'tasks') {
			$object_id = $violation_records->code_tasks;
		} else if ($violation_records->object_type == 'qc') {
			$object_id = $violation_records->code_qc;
		}
		else if ($violation_records->object_type == 'production_report') {
			$object_id = $violation_records->name_report;
		}

		$violation_records->content = $this->get_data_content($violation_records->staff_id, $violation_records->content);


		$data['title'] = lang('In biên bản vi phạm');
		$data['type'] = 'P';
		$data['img'] = '';

		$day = date_format(date_create($violation_records->date), 'd');
		$month = date_format(date_create($violation_records->date), 'm');
		$year = date_format(date_create($violation_records->date), 'Y');
		$hours = date_format(date_create($violation_records->date), 'H');
		$minute = date_format(date_create($violation_records->date), 'i');
		ob_start();
		stylePdf();

		echo '<br/>
			<br/><br/>
			
			<br/><table cellspacing="0" cellpadding="5">
				<tr>
					<td style="text-align: center;font-size: 13px;"><b>'.get_option('invoice_company_name').'<br/>Địa chỉ: </b>'.get_option('invoice_company_address').'</td>
					<td style="text-align: center;font-size: 13px;"><b>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br/>Độc lập - Tự do - Hạnh phúc<br/>=====o0o=====</b></td>
				</tr>
			</table>
			<br/>
			<br/>
			<table cellpadding="5">
				<tr><td style="text-align: center;font-size: 16px"><b>BIÊN BẢN VI PHẠM</b></td></tr>
				<tr><td style="text-align: center"></td></tr>
			</table>
			<table cellpadding="5">
				<tr><td style="text-align: left" colspan="2"><b>Hôm nay, ngày '.$day.' tháng '.$month.' năm '.$year.', vào lúc '.$hours.' giờ '.$minute.' phút</b></td></tr>
				<tr><td style="text-align: left"><b>Họ tên người vi phạm:</b> '.$violation_records->fullname.'</td><td style="text-align: left"> <b>Bộ phận:</b> '.$violation_records->name_departments.'</td></tr>
				<tr><td style="text-align: left" colspan="2"><b>Loại vi phạm:</b> '.$violation_records->name_list_protocol.'</td></tr>
				<tr><td style="text-align: left" colspan="2"><b>Liên quan đến:</b> '.$name_object.' ('.$object_id.')</td></tr>'.$object_id_add.'
				<tr><td style="text-align: left" colspan="2"><b>Nội dung vi phạm:</b> '.$violation_records->content.'</td></tr>
				<tr><td style="text-align: left" colspan="2"><b>Hình thức xử lý:</b> '.$violation_records->forms_processing.'</td></tr>
			</table>
			<table cellpadding="5">
				<tr><td></td><td></td></tr>
				<tr><td style="text-align: center;"><b style="font-size: 14px;">Người vi phạm</b><br/><br/><br/><br/><br/><br/>'.get_staff_full_name($violation_records->staff_id).'</td><td style="text-align: center"><b>Người lập biên bản/ Tổ kiểm tra</b><br/><br/><br/><br/><br/><br/>'.get_staff_full_name($violation_records->create_by).'</td></tr>
			</table>';


		$content = ob_get_contents();
		ob_end_clean();

		$data['content'] = $content;
		$data['showHeader'] = 'hide';
		$pdf = @print_pdf_tnh($data);
		$type = 'I';
		$pdf->Output(slug_it('123') . '.pdf', $type);
	}
}
