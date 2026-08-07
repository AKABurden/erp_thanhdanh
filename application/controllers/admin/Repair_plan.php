<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Repair_plan extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('products_model');
			$this->load->model('items_model');
			$this->load->model('unit_model');
			$this->load->model('category_model');
			$this->load->model('manufactures_model');
			$this->load->model('purchases_model');
			$this->load->model('business_plan_model');
			$this->load->model('orders_model');
			$this->load->model('departments_model');
			$this->load->model('stock_model');
			$this->load->model('tools_supplies_model');
			$this->load->model('transfer_model');
			
			$this->preViewRepairPlan = true;
			$this->preViewOwnRepairPlan = true;
			$this->preAddRepairPlan = true;
			$this->preEditRepairPlan = true;
			$this->preApproveRepairPlan = true;
			$this->preDeleteRepairPlan = true;
		}
		
		public function index()
		{
			if (!$this->preViewRepairPlan && !$this->preViewOwnRepairPlan) {
				access_denied();
			}
			$data['title'] = _l('Phiếu Kế Hoạch Yêu Cầu Sửa Chữa');
			$this->load->view('admin/repair_plan/manage', $data);
		}
		
		public function table()
		{
			$end_date_search = $this->input->post('end_date_search');
			$start_date_search = $this->input->post('start_date_search');
			
			$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
			$aColumns = [
				'tbl_repair_plan.id as id',
				'tbl_repair_plan.reference_no as reference_no',
				'tbl_repair_plan.date as date',
				'tbl_repair_plan.unit_repair as unit_repair',
				'tbl_category_maintenance.name as category_maintenance',
				'tbl_repair_plan.bp_maintenance as bp_maintenance',
				'tbl_repair_plan.detail_maintenance as detail_maintenance',
				'tbl_repair_plan.quantity as quantity',
				'tbl_repair_plan.price as price',
				'tbl_repair_plan.amount as amount',
				'tbl_machines.code as code_machines',
				'tbl_machines.name as name_machines',
				'tbl_result.name as name_result',
				'(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
             FROM tblproduction_report
             WHERE tblproduction_report.object_id = tbl_repair_plan.id AND tblproduction_report.object_type = "repair_plan"
            ) as name_report',
				'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname',
				'tbl_repair_plan.status as status',
			];
			$sIndexColumn = 'id';
			$sTable = 'tbl_repair_plan';
			$where = [];
			$filter = [];
			
			$join = [
				'INNER JOIN tbl_machines ON tbl_machines.id = tbl_repair_plan.machines_id',
				'INNER JOIN tbl_result ON tbl_result.id = tbl_repair_plan.result_id',
				'INNER JOIN tblstaff ON tblstaff.staffid = tbl_repair_plan.employees',
				'INNER JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_repair_plan.category_maintenance',
			];
			
			if (!$this->preViewRepairPlan) {
				array_push($where, 'AND tbl_repair_plan.created_by =', get_staff_user_id());
			}
			
			if (!empty($start_date_search)) {
				$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
				array_push($where, "AND tbl_repair_plan.date >= '" . $start_date_search . "'");
			}
			
			if (!empty($end_date_search)) {
				$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
				array_push($where, "AND tbl_repair_plan.date <= '" . $end_date_search . "'");
			}
			
			
			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
				'tbl_repair_plan.date_status',
				'tbl_repair_plan.staff_status'
			], '', []);
			
			
			$output = $result['output'];
			$rResult = $result['rResult'];
			foreach ($rResult as $key => $aRow) {
				$row = array();
				$row[] = '<div class="text-center">' . (++$key) . '</div>';
				$row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/repair_plan/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
				$row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['unit_repair']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['category_maintenance']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['bp_maintenance']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['detail_maintenance']) . '</div>';
				$row[] = '<div class="text-right">' . formatMoney($aRow['quantity']) . '</div>';
				$row[] = '<div class="text-right">' . formatMoney($aRow['price']) . '</div>';
				$row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
				
				$row[] = '<div class="text-left">' . ($aRow['code_machines']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['name_machines']) . '</div>';
				$row[] = '<div class="text-left">' . $aRow['name_result'] . '</div>';
				$arrReport = $aRow['name_report'];
				$htmlReport = '';
				if (!empty($arrReport)) {
					$arrReport = explode('||', $arrReport);
					if (!empty($arrReport)) {
						foreach ($arrReport as $kk => $vv) {
							$vv = explode('__', $vv);
							$htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
						}
					}
				}
				
				if ($aRow['status'] == 1) {
					$row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=repair_plan') . '" class="btn btn-info btn-icon">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
				} else {
					$row[] = '';
				}
				$row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
				if ($aRow['status'] == 0) {
					$_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
				} elseif ($aRow['status'] == 1) {
					$_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
					$_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
				} else {
					$_data = '';
				}
				$row[] = '<div class="text-left">' . $_data . '</div>';
				
				$view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/repair_plan/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';
				
				$edit = $this->preEditRepairPlan ? '<a class="tnh-modal" href="' . base_url('admin/repair_plan/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';
				
				$delete = $this->preDeleteRepairPlan ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/repair_plan/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
				$actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
				$row[] = '<div>' . $actions . '</div>';
				$output['aaData'][] = $row;
			}
			echo json_encode($output);
		}
		
		public function detail($id = 0)
		{
			$data = [];
			$dtData = [];
			if ($this->input->post()) {
				if (empty($id)) {
					$this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_repair_plan.reference_no]');
					$this->form_validation->set_rules('date', lang("date"), 'required');
					$this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
					$this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
					$this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
					$this->form_validation->set_rules('category_maintenance', lang("Nhóm bảo dưỡng"), 'required');
					if ($this->form_validation->run() == true) {
						$reference_no = getReference('repair_plan');
						$date = to_sql_date($this->input->post('date'), true);
						$branch_id = $this->input->post('branch_id');
						$machines_id = $this->input->post('machines_id');
						$result_id = $this->input->post('result_id');
						$category_maintenance = ($this->input->post('category_maintenance'));
						$bp_maintenance = ($this->input->post('bp_maintenance'));
						$unit_repair = ($this->input->post('unit_repair'));
						$detail_maintenance = ($this->input->post('detail_maintenance'));
						$employees = ($this->input->post('employees'));
						$test_records = ($this->input->post('test_records'));
						$evaluate = ($this->input->post('evaluate'));
						$payment = ($this->input->post('payment'));
						$quantity = number_unformat($this->input->post('quantity'));
						$price = number_unformat($this->input->post('price'));
						$fields = [
							'reference_no' => $reference_no,
							'date' => $date,
							'machines_id' => $machines_id,
							'result_id' => $result_id,
							'category_maintenance' => $category_maintenance,
							'unit_repair' => $unit_repair,
							'detail_maintenance' => $detail_maintenance,
							'quantity' => $quantity,
							'price' => $price,
							'amount' => $quantity * $price,
							'branch_id' => $branch_id,
							'bp_maintenance' => $bp_maintenance,
							'employees' => $employees,
							'test_records' => $test_records,
							'evaluate' => $evaluate,
							'payment' => $payment,
							'created_by' => get_staff_user_id(),
							'date_created' => date('Y-m-d H:i:s'),
						];
						$this->db->insert('tbl_repair_plan', $fields);
						$id = $this->db->insert_id();
						if ($id) {
							if (getReference('repair_plan') == $reference_no) {
								updateReference('repair_plan');
							}
							insertActivityLog([
								'type_parent_obj' => 'repair_plan',
								'table_obj' => 'tbl_repair_plan',
								'id_obj' => $id,
								'name_obj' => $reference_no,
								'content' => lang('Thêm mới kế hoạch yêu cầu sửa chữa') . ' [' . $reference_no . ']',
								'actions' => 'add'
							]);
							$data['result'] = 1;
							$data['message'] = lang('Thêm thành công');
						} else {
							$data['result'] = 0;
							$data['message'] = lang('Thêm thất bại');
						}
					} else {
						$data['result'] = 0;
						$data['message'] = validation_errors();
					}
					echo json_encode($data);
					die();
				} else {
					$this->db->select('tbl_repair_plan.*');
					$this->db->from('tbl_repair_plan');
					$this->db->where('tbl_repair_plan.id', $id);
					$dtData = $this->db->get()->row_array();
					if ($dtData['reference_no'] != $this->input->post('reference_no')) {
						$this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_repair_plan.reference_no]');
					}
					$this->form_validation->set_rules('date', lang("date"), 'required');
					$this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
					$this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
					$this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
					$this->form_validation->set_rules('category_maintenance', lang("Nhóm bảo dưỡng"), 'required');
					if ($this->form_validation->run() == true) {
						$date = to_sql_date($this->input->post('date'), true);
						$branch_id = $this->input->post('branch_id');
						$machines_id = $this->input->post('machines_id');
						$result_id = $this->input->post('result_id');
						$category_maintenance = ($this->input->post('category_maintenance'));
						$bp_maintenance = ($this->input->post('bp_maintenance'));
						$unit_repair = ($this->input->post('unit_repair'));
						$detail_maintenance = ($this->input->post('detail_maintenance'));
						$employees = ($this->input->post('employees'));
						$test_records = ($this->input->post('test_records'));
						$evaluate = ($this->input->post('evaluate'));
						$payment = ($this->input->post('payment'));
						$quantity = number_unformat($this->input->post('quantity'));
						$price = number_unformat($this->input->post('price'));
						$fields = [
							'date' => $date,
							'machines_id' => $machines_id,
							'result_id' => $result_id,
							'category_maintenance' => $category_maintenance,
							'unit_repair' => $unit_repair,
							'detail_maintenance' => $detail_maintenance,
							'quantity' => $quantity,
							'price' => $price,
							'amount' => $quantity * $price,
							'branch_id' => $branch_id,
							'bp_maintenance' => $bp_maintenance,
							'employees' => $employees,
							'test_records' => $test_records,
							'evaluate' => $evaluate,
							'payment' => $payment,
							'updated_by' => get_staff_user_id(),
							'date_updated' => date('Y-m-d H:i:s'),
						];
						$this->db->where('id', $id);
						$success = $this->db->update('tbl_repair_plan', $fields);
						if ($success) {
							insertActivityLog([
								'type_parent_obj' => 'repair_plan',
								'table_obj' => 'tbl_repair_plan',
								'id_obj' => $id,
								'name_obj' => $dtData['reference_no'],
								'content' => lang('Sửa phiếu kế hoạch yêu cầu sửa chữa') . ' [' . $dtData['reference_no'] . ']',
								'actions' => 'edit'
							]);
							$data['result'] = 1;
							$data['message'] = lang('Sửa thành công');
						} else {
							$data['result'] = 0;
							$data['message'] = lang('Sửa thất bại');
						}
					} else {
						$data['result'] = 0;
						$data['message'] = validation_errors();
					}
					echo json_encode($data);
					die();
				}
			} else {
				if (empty($id)) {
					if (!$this->preAddRepairPlan) {
						accessDenied(true);
					}
					$data['title'] = lang('Thêm Kế hoạch yêu cầu sửa chữa');
				} else {
					if (!$this->preEditRepairPlan) {
						accessDenied(true);
					}
					$this->db->select('tbl_repair_plan.*');
					$this->db->from('tbl_repair_plan');
					$this->db->where('tbl_repair_plan.id', $id);
					$dtData = $this->db->get()->row_array();
					if ($dtData['status'] == 1) {
						refererModel(lang('Phiếu đã duyệt không thể sửa !'));
					}
					$data['title'] = lang('Sửa kế hoạch yêu cầu sửa chữa');
				}
			}
			$data['dtData'] = $dtData;
			$data['employees'] = $this->manufactures_model->getAllStaff();
			$data['id'] = $id;
			$data['reference_no'] = getReference('repair_plan');
			$data['dtCategorymaintenance'] = get_table_where('tbl_category_maintenance');
			$data['dtResult'] = get_table_where('tbl_result');
			$this->load->view('admin/repair_plan/detail', $data);
		}
		
		public function view($id)
		{
			$data = [];
			$data['title'] = lang('Xem chi tiết kế hoạch yêu cầu sửa chữa');
			
			$this->db->select('tbl_repair_plan.*,
           tbl_result.name as name_result,
           tbl_machines.status as status_machines,
           tbl_machines.code as code_machines,
           tbl_machines.name as name_machines,
           tbl_category_maintenance.name as category_maintenance,
           (SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
            FROM tbl_machines_stage
            JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
            WHERE tbl_machines_stage.machines_id = tbl_machines.id
           ) as name_stage
        ');
			$this->db->from('tbl_repair_plan');
			$this->db->join('tbl_machines', 'tbl_machines.id = tbl_repair_plan.machines_id', 'inner');
			$this->db->join('tbl_result', 'tbl_result.id = tbl_repair_plan.result_id', 'left');
			$this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_repair_plan.category_maintenance', 'left');
			$this->db->where('tbl_repair_plan.id', $id);
			$dtData = $this->db->get()->row_array();
			
			
			$data['dtData'] = $dtData;
			$this->load->view('admin/repair_plan/view', $data);
		}
		
		public function agree()
		{
			if (!$this->preApproveRepairPlan) {
				$data['result'] = 0;
				$data['message'] = lang('access_denied');
				echo json_encode($data);
				die();
			}
			
			$data = [];
			$suggest_id = $this->input->post('suggest_id');
			$status = $this->input->post('status');
			
			$this->db->select('tbl_repair_plan.*');
			$this->db->from('tbl_repair_plan');
			$this->db->where('tbl_repair_plan.id', $suggest_id);
			$dtData = $this->db->get()->row_array();
			if (empty($dtData)) {
				$data['result'] = 0;
				$data['message'] = lang('not_data_exists');
			} else {
				
				if ($status == 0) {
					$this->db->from('tblproduction_report');
					$this->db->where('tblproduction_report.object_type', 'repair_plan');
					$this->db->where('tblproduction_report.object_id', $suggest_id);
					$checkExists = $this->db->count_all_results();
					if (!empty($checkExists)) {
						$data['result'] = 0;
						$data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
						echo json_encode($data);
						die();
					}
				}
				
				if (($dtData['status'] == $status)) {
					$data['result'] = 0;
					$data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
					echo responseData($data);
					return;
				}
				
				$date_status = date('Y-m-d H:i:s');
				$staff_status = get_staff_user_id();
				
				$options = [
					'status' => $status,
					'date_status' => $date_status,
					'staff_status' => $staff_status,
				];
				
				$this->db->where('id', $suggest_id);
				$up = $this->db->update('tbl_repair_plan', $options);
				if ($up) {
					
					insertActivityLog([
						'type_parent_obj' => 'repair_plan',
						'table_obj' => 'tbl_repair_plan',
						'id_obj' => $suggest_id,
						'name_obj' => $dtData['reference_no'],
						'content' => lang('Duyệt phiếu yêu cầu kế hoạch sửa chửa') . ' [' . $dtData['reference_no'] . ']',
						'actions' => 'approved'
					]);
					$data['result'] = 1;
					$data['message'] = lang('success');
				} else {
					$data['result'] = 0;
					$data['message'] = lang('fail');
				}
			}
			echo json_encode($data);
		}
		
		public function delete($id)
		{
			if (!$this->preDeleteRepairPlan) {
				$data['result'] = 0;
				$data['message'] = lang('access_denied');
				echo json_encode($data);
				die();
			}
			$data = [];
			$this->db->select('tbl_repair_plan.*');
			$this->db->from('tbl_repair_plan');
			$this->db->where('tbl_repair_plan.id', $id);
			$dtData = $this->db->get()->row_array();
			if (empty($dtData)) {
				$data['result'] = 0;
				$data['message'] = lang('not_data_exists');
				echo json_encode($data);
				die();
			}
			
			if ($dtData['status'] == 1) {
				$data['result'] = 0;
				$data['message'] = lang('Phiếu đã duyệt không thể xóa !');
				echo json_encode($data);
				die();
			}
			
			$this->db->where('id', $id);
			$success = $this->db->delete('tbl_repair_plan');
			if ($success) {
				
				insertActivityLog([
					'type_parent_obj' => 'repair_plan',
					'table_obj' => 'tbl_repair_plan',
					'id_obj' => $id,
					'name_obj' => $dtData['reference_no'],
					'content' => lang('Xóa phiếu kế hoạch yêu cầu sửa chữa') . ' [' . $dtData['reference_no'] . ']',
					'actions' => 'delete'
				]);
				$data['result'] = 1;
				$data['message'] = lang('Xóa thành công');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('Xóa thất bại');
			}
			echo json_encode($data);
		}
		
		public function exportExcel()
		{
			$columsExcel = [
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
				'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
				'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
				'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
			];
			if ($this->input->post('export_excel')) {
				
				ini_set('memory_limit', '3500M');
				require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
				$this->load->library('PHPExcel');
				$inputFileName = 'uploads/import_ch/Phieu_ke_hoach_yeu_cau_sua_chua.xlsx';
				//  Read your Excel workbook
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
				}
				$BStylenumber = array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					),
					'font'  => array(
						'bold'  => true,
						'color' => array('rgb' => '111112'),
						'size'  => 11,
						'name'  => 'Times New Roman'
					),
					'alignment' => array(
						'horizontal' => 'center',
					),
				);
				$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestRow = $objWorksheet->getHighestRow();
				$check_key = array_search($highestColumn, $columsExcel);
				$start_date_search = $this->input->post('start_date_search');
				$end_date_search = $this->input->post('end_date_search');
				$row = 3;
				$staff_id = get_staff_user_id();
				$this->db->select('tbl_repair_plan.*,
                tbl_result.name as name_result,
                tbl_machines.status as status_machines,
                tbl_machines.code as code_machines,
                tbl_machines.name as name_machines,
                tbl_category_maintenance.name as category_maintenance,
                (SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
                    FROM tbl_machines_stage
                    JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
                    WHERE tbl_machines_stage.machines_id = tbl_machines.id
                ) as name_stage,
                (SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_repair_plan.id AND tblproduction_report.object_type = "repair_plan"
                ) as name_report
            ');
				$this->db->from('tbl_repair_plan');
				$this->db->join('tbl_machines', 'tbl_machines.id = tbl_repair_plan.machines_id', 'inner');
				$this->db->join('tbl_result', 'tbl_result.id = tbl_repair_plan.result_id', 'left');
				$this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_repair_plan.category_maintenance', 'left');
				if (!$this->preViewRepairPlan) {
					$this->db->where('(tbl_repair_plan.created_by = ' . $staff_id . ')');
				}
				
				if (!empty($start_date_search)) {
					$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
					$this->db->where("tbl_repair_plan.date >= '" . $start_date_search . "'");
				}
				
				if (!empty($end_date_search)) {
					$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
					$this->db->where("tbl_repair_plan.date <= '" . $end_date_search . "'");
				}
				$this->db->order_by('tbl_repair_plan.id asc');
				$items = $this->db->get()->result_array();
				
				$dem = 0;
				$this->load->library('ciqrcode');
				
				foreach ($items as $key => $value) {
					$row++;
					$dem++;
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['unit_repair']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, $value['price']);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $value['amount']);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['category_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['bp_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['detail_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, $value['quantity']);
					
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['name_stage']), PHPExcel_Cell_DataType::TYPE_STRING);
					
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['code_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['name_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
					
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);
					
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($value['test_records']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, ($value['payment']), PHPExcel_Cell_DataType::TYPE_STRING);
					$arrReport = $value['name_report'];
					$htmlReport = '';
					if (!empty($arrReport)) {
						$arrReport = explode('||', $arrReport);
						if (!empty($arrReport)) {
							foreach ($arrReport as $kk => $vv) {
								$vv = explode('__', $vv);
								$htmlReport .= $vv[0] . ',';
							}
						}
					}
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, ($htmlReport), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, ($value['evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, get_staff_full_name($value['employees']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, (!empty($value['staff_status']) ? get_staff_full_name($value['staff_status']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, ($value['standard']), PHPExcel_Cell_DataType::TYPE_STRING);
					
					if (!empty($value['barcode'])) {
						$code = $value['barcode'];
					} else {
						$code = 'repair_plan||' . $value['id'];
						$this->db->where('id', $value['id']);
						$this->db->update('tbl_repair_plan', ['barcode' => $code]);
					}
					$qr = vn_to_str(str_replace('||', '__', $code));
					$folder = FCPATH . 'uploads/repair_plan/';
					if (!file_exists($folder)) {
						mkdir($folder);
						fopen($folder . 'index.html', 'w');
					}
					if (!file_exists($folder . 'qrcode' . '/')) {
						mkdir($folder . 'qrcode' . '/');
						fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
					}
					$params['data'] = $code;
					$params['level'] = 'H';
					$params['size'] = 40;
					$params['savename'] = $folder . 'qrcode/' . $qr . '.png';
					$this->ciqrcode->generate($params);
					$img = ($folder . 'qrcode/' . $qr . '.png');
					if (!empty($img)) {
						$objDrawing1 = new PHPExcel_Worksheet_Drawing();
						$objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
						$objDrawing1->setPath($img);
						$objDrawing1->setWidth(90);
						$objDrawing1->setHeight(65);
						$objDrawing1->setOffsetX(20);
						$objDrawing1->setOffsetY(2);
						$objDrawing1->setCoordinates($columsExcel[21] . $row);
					}
					$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, '')->getStyle($columsExcel[21] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A4:V' . $row)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A4:V' . $row)->applyFromArray([
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					),
				]);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(40);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(17);
				
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
				$cacheSettings = array(' memoryCacheSize ' => '8MB');
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$filename = lang('Phieu_yeu_cau_sua_chua') . '.xls';
				ob_start();
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="$filename"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
				$xlsData = ob_get_contents();
				ob_end_clean();
				$response = array(
					'result' => 1,
					'filename' => $filename,
					'message' => lang('success'),
					'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
				);
				die(json_encode($response));
			}
		}
	}
