<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Request_bussiness extends AdminController
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
			$this->preViewRequestBussiness = true;
			$this->preViewOwnRequestBussiness = true;
			$this->preAddRequestBussiness = true;
			$this->preEditRequestBussiness = true;
			$this->preApproveRequestBussiness = true;
			$this->preDeleteRequestBussiness = true;
		}
		
		public function index()
		{
			if (!$this->preViewRequestBussiness && !$this->preViewOwnRequestBussiness) {
				access_denied();
			}
			$data['title'] = _l('ch_request_bussiness');
			$this->load->view('admin/request_bussiness/index', $data);
		}
		
		public function getRequestBussiness()
		{
			$end_date_search = $this->input->post('end_date_search');
			$start_date_search = $this->input->post('start_date_search');
			$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
			$aColumns = [
				'tbl_request_bussiness.id as id',
				'tbl_request_bussiness.reference_no as reference_no',
				'tbl_request_bussiness.date as date',
				'tbl_request_bussiness.content as content',
				'CONCAT(employees.firstname," ",employees.lastname) as fullname',
				'roleemployees.name as local_employees',
				'tbl_request_bussiness.quantity as quantity',
				'staff_localtion.name as staff_localtion',
				'IF(tbl_request_bussiness.object_type = "customer", tblclients.company, IF(tbl_request_bussiness.object_type = "supplier", tblsuppliers.company, object_id)) as company',
				'tbl_request_bussiness.address as address',
				'tbl_request_bussiness.phone as phone',
				'tbl_request_bussiness.time_start as time_start',
				'tbl_request_bussiness.time_end as time_end',
				'tbl_request_bussiness.amount as amount',
			];
			$sIndexColumn = 'id';
			$sTable = 'tbl_request_bussiness';
			$where = [];
			$filter = [];
			$join = [
				'LEFT JOIN tblclients ON tblclients.userid = cast(tbl_request_bussiness.object_id as int) AND tbl_request_bussiness.object_type = "customer"',
				'LEFT JOIN tblsuppliers ON tblsuppliers.id = cast(tbl_request_bussiness.object_id as int) AND tbl_request_bussiness.object_type = "supplier"',
				'INNER JOIN tblstaff employees ON employees.staffid = tbl_request_bussiness.employees',
				'left JOIN tblroles roleemployees ON roleemployees.roleid = employees.role',
				'left JOIN tblroles staff_localtion ON staff_localtion.roleid = tbl_request_bussiness.staff_localtion',
			];
			if($this->input->post('object_type_search')) {
				$where[] = 'AND tbl_request_bussiness.object_type = "'.$this->input->post('object_type_search').'"';
			}
			if (!$this->preViewRequestBussiness) {
				array_push($where, 'AND tbl_request_bussiness.created_by =', get_staff_user_id());
			}
			if (!empty($start_date_search)) {
				$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
				array_push($where, "AND tbl_request_bussiness.date >= '" . $start_date_search . "'");
			}
			if (!empty($end_date_search)) {
				$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
				array_push($where, "AND tbl_request_bussiness.date <= '" . $end_date_search . "'");
			}
			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
				'tbl_request_bussiness.object_type as object_type'
			], '', []);
			$output = $result['output'];
			$rResult = $result['rResult'];
			foreach ($rResult as $key => $aRow) {
				$row = array();
				$row[] = '<div class="text-center">' . (++$key) . '</div>';
				$row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_bussiness/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
				$row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['content']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['local_employees']) . '</div>';
				$row[] = '<div class="text-center">' . formatNumber($aRow['quantity']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['staff_localtion']) . '</div>';
				$row[] = '<div class="text-left">' .'<b>'._l('object_type_' . $aRow['object_type']).'</b>: '. ($aRow['company']) . '</div>';
				$row[] = '<div class="text-left">' . ($aRow['address']) . '</div>';
				$row[] = '<div class="text-left">' . $aRow['phone'] . '</div>';
				$row[] = '<div class="text-left">' . _dt($aRow['time_start']) . '</div>';
				$row[] = '<div class="text-left">' . _dt($aRow['time_end']) . '</div>';
				$row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
				$view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_bussiness/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';
				$edit = $this->preEditRequestBussiness ? '<a class="tnh-modal" href="' . base_url('admin/request_bussiness/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';
				$delete = $this->preDeleteRequestBussiness ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_bussiness/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
					$this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_bussiness.reference_no]');
					$this->form_validation->set_rules('date', lang("date"), 'required');
					$this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
					$this->form_validation->set_rules('employees', lang("Nhân viên đề xuất"), 'required');
					$this->form_validation->set_rules('staff_localtion', lang("Vị trí người đi"), 'required');
					$this->form_validation->set_rules('object_type', lang("Địa điểm đến"), 'required');
					if ($this->form_validation->run() == true) {
						$reference_no = getReference('request_bussiness');
						$date = to_sql_date($this->input->post('date'), true);
						$branch_id = $this->input->post('branch_id');
						$content = $this->input->post('content');
						$employees = $this->input->post('employees');
						$employees_localtion = $this->input->post('employees_localtion');
						$quantity = number_unformat($this->input->post('quantity'));
						$amount = number_unformat($this->input->post('amount'));
						$staff_localtion = $this->input->post('staff_localtion');
						$object_type = ($this->input->post('object_type'));
						$object_id = trim($this->input->post('object_id'));
						
						$address = ($this->input->post('address'));
						$phone = ($this->input->post('phone'));
						$time_start = to_sql_date($this->input->post('time_start'), true);
						$time_end = to_sql_date($this->input->post('time_end'), true);
						$fields = [
							'reference_no' => $reference_no,
							'date' => $date,
							'content' => $content,
							'employees' => $employees,
							'employees_localtion' => $employees_localtion,
							'amount' => $amount,
							'staff_localtion' => $staff_localtion,
							'object_type' => $object_type,
							'object_id' => $object_id,
							'address' => $address,
							'phone' => $phone,
							'quantity' => $quantity,
							'time_start' => $time_start,
							'time_end' => $time_end,
							'created_by' => get_staff_user_id(),
							'date_created' => date('Y-m-d H:i:s'),
							'branch_id' => $branch_id,
						];
						$this->db->insert('tbl_request_bussiness', $fields);
						$id = $this->db->insert_id();
						if ($id) {
							if (getReference('request_bussiness') == $reference_no) {
								updateReference('request_bussiness');
							}
							insertActivityLog([
								'type_parent_obj' => 'request_bussiness',
								'table_obj' => 'tbl_request_bussiness',
								'id_obj' => $id,
								'name_obj' => $reference_no,
								'content' => lang('Thêm mới yêu cầu công tác') . ' [' . $reference_no . ']',
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
					$this->db->select('tbl_request_bussiness.*');
					$this->db->from('tbl_request_bussiness');
					$this->db->where('tbl_request_bussiness.id', $id);
					$dtData = $this->db->get()->row_array();
					if ($dtData['reference_no'] != $this->input->post('reference_no')) {
						$this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_bussiness.reference_no]');
					}
					$this->form_validation->set_rules('date', lang("date"), 'required');
					$this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
					$this->form_validation->set_rules('employees', lang("Nhân viên đề xuất"), 'required');
					$this->form_validation->set_rules('staff_localtion', lang("Vị trí người đi"), 'required');
					$this->form_validation->set_rules('object_type', lang("Địa điểm đến"), 'required');
					if ($this->form_validation->run() == true) {
						$date = to_sql_date($this->input->post('date'), true);
						$branch_id = $this->input->post('branch_id');
						$content = $this->input->post('content');
						$employees = $this->input->post('employees');
						$employees_localtion = $this->input->post('employees_localtion');
						$quantity = number_unformat($this->input->post('quantity'));
						$amount = number_unformat($this->input->post('amount'));
						$staff_localtion = $this->input->post('staff_localtion');
						$object_type = ($this->input->post('object_type'));
						$object_id = trim($this->input->post('object_id'));
						$address = ($this->input->post('address'));
						$phone = ($this->input->post('phone'));
						$reference_no = ($this->input->post('reference_no'));
						$time_start = to_sql_date($this->input->post('time_start'), true);
						$time_end = to_sql_date($this->input->post('time_end'), true);
						$fields = [
							'reference_no' => $reference_no,
							'date' => $date,
							'content' => $content,
							'employees' => $employees,
							'employees_localtion' => $employees_localtion,
							'amount' => $amount,
							'staff_localtion' => $staff_localtion,
							'object_type' => $object_type,
							'object_id' => $object_id,
							'address' => $address,
							'phone' => $phone,
							'quantity' => $quantity,
							'time_start' => $time_start,
							'time_end' => $time_end,
							'updated_by' => get_staff_user_id(),
							'date_updated' => date('Y-m-d H:i:s'),
							'branch_id' => $branch_id,
						];
						$this->db->where('id', $id);
						$success = $this->db->update('tbl_request_bussiness', $fields);
						if ($success) {
							insertActivityLog([
								'type_parent_obj' => 'request_bussiness',
								'table_obj' => 'tbl_request_bussiness',
								'id_obj' => $id,
								'name_obj' => $dtData['reference_no'],
								'content' => lang('Sửa phiếu yêu cầu công tác') . ' [' . $dtData['reference_no'] . ']',
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
					if (!$this->preAddRequestBussiness) {
						accessDenied(true);
					}
					$data['title'] = lang('ch_add_request_bussiness');
				} else {
					if (!$this->preEditRequestBussiness) {
						accessDenied(true);
					}
					$this->db->select('tbl_request_bussiness.*, tblroles.name as name_employees_localtion');
					$this->db->from('tbl_request_bussiness');
					$this->db->where('tbl_request_bussiness.id', $id);
					$this->db->join('tblroles', 'tblroles.roleid = tbl_request_bussiness.employees_localtion', 'left');
					$dtData = $this->db->get()->row_array();
					
					$this->db->from('tblproduction_report');
					$this->db->where('tblproduction_report.object_type', 'request_bussiness');
					$this->db->where('tblproduction_report.object_id', $id);
					$checkExists = $this->db->count_all_results();
					if (!empty($checkExists)) {
						refererModel(lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !'));
					}
					$data['title'] = lang('ch_edit_request_bussiness');
				}
			}
			$data['dtData'] = $dtData;
			$data['employees'] = $this->manufactures_model->getAllStaffRole();
			$data['id'] = $id;
			$data['reference_no'] = getReference('request_bussiness');
			$data['dtCategoryCalibration'] = get_table_where('tbl_category_calibration');
			$data['dtRoles'] = get_table_where('tblroles');
			$data['dtResult'] = get_table_where('tbl_result');
			$this->load->view('admin/request_bussiness/detail', $data);
		}
		
		public function view($id)
		{
			$data = [];
			$data['title'] = lang('ch_view_request_bussiness');
			$this->db->select('
				tbl_request_bussiness.*,
				tbl_request_bussiness.id as id,
				tbl_request_bussiness.reference_no as reference_no,
				tbl_request_bussiness.date as date,
				tbl_request_bussiness.content as content,
				CONCAT(employees.firstname," ",employees.lastname) as fullname,
				roleemployees.name as local_employees,
				tbl_request_bussiness.quantity as quantity,
				staff_localtion.name as staff_localtion,
				IF(tbl_request_bussiness.object_type = "customer", tblclients.company, IF(tbl_request_bussiness.object_type = "supplier", tblsuppliers.company, object_id)) as company,
				tbl_request_bussiness.address as address,
				tbl_request_bussiness.phone as phone,
				tbl_request_bussiness.time_start as time_start,
				tbl_request_bussiness.time_end as time_end,
				tbl_request_bussiness.amount as amount,
				tbl_request_bussiness.branch_id as branch_id,
        	');
			$this->db->from('tbl_request_bussiness');
			$this->db->join('tblclients', 'tblclients.userid = cast(tbl_request_bussiness.object_id as int) AND tbl_request_bussiness.object_type = "customer"', 'left');
			$this->db->join('tblsuppliers', 'tblsuppliers.id = cast(tbl_request_bussiness.object_id as int) AND tbl_request_bussiness.object_type = "supplier"', 'left');
			$this->db->join('tblstaff employees', 'employees.staffid = tbl_request_bussiness.employees', 'left');
			$this->db->join('tblroles roleemployees', 'roleemployees.roleid = tbl_request_bussiness.employees_localtion', 'left');
			$this->db->join('tblroles staff_localtion', 'staff_localtion.roleid = tbl_request_bussiness.staff_localtion', 'left');
			$this->db->where('tbl_request_bussiness.id', $id);
			$dtData = $this->db->get()->row_array();
			$data['dtData'] = $dtData;
			$this->load->view('admin/request_bussiness/view', $data);
		}
		
		public function agree()
		{
			if (!$this->preApproveRequestBussiness) {
				$data['result'] = 0;
				$data['message'] = lang('access_denied');
				echo json_encode($data);
				die();
			}
			$data = [];
			$suggest_id = $this->input->post('suggest_id');
			$status = $this->input->post('status');
			$this->db->select('tbl_request_bussiness.*');
			$this->db->from('tbl_request_bussiness');
			$this->db->where('tbl_request_bussiness.id', $suggest_id);
			$dtData = $this->db->get()->row_array();
			if (empty($dtData)) {
				$data['result'] = 0;
				$data['message'] = lang('not_data_exists');
			} else {
				if ($status == 0) {
					$this->db->from('tblproduction_report');
					$this->db->where('tblproduction_report.object_type', 'request_bussiness');
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
				$up = $this->db->update('tbl_request_bussiness', $options);
				if ($up) {
					insertActivityLog([
						'type_parent_obj' => 'request_bussiness',
						'table_obj' => 'tbl_request_bussiness',
						'id_obj' => $suggest_id,
						'name_obj' => $dtData['reference_no'],
						'content' => lang('Duyệt phiếu yêu cầu công tác') . ' [' . $dtData['reference_no'] . ']',
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
			if (!$this->preDeleteRequestBussiness) {
				$data['result'] = 0;
				$data['message'] = lang('access_denied');
				echo json_encode($data);
				die();
			}
			$data = [];
			$this->db->select('tbl_request_bussiness.*');
			$this->db->from('tbl_request_bussiness');
			$this->db->where('tbl_request_bussiness.id', $id);
			$dtData = $this->db->get()->row_array();
			if (empty($dtData)) {
				$data['result'] = 0;
				$data['message'] = lang('not_data_exists');
				echo json_encode($data);
				die();
			}
			$this->db->from('tblproduction_report');
			$this->db->where('tblproduction_report.object_type', 'request_bussiness');
			$this->db->where('tblproduction_report.object_id', $id);
			$checkExists = $this->db->count_all_results();
			if (!empty($checkExists)) {
				$data['result'] = 0;
				$data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
				echo json_encode($data);
				die();
			}
			$this->db->where('id', $id);
			$success = $this->db->delete('tbl_request_bussiness');
			if ($success) {
				insertActivityLog([
					'type_parent_obj' => 'request_bussiness',
					'table_obj' => 'tbl_request_bussiness',
					'id_obj' => $id,
					'name_obj' => $dtData['reference_no'],
					'content' => lang('Xóa phiếu yêu cầu công tác') . ' [' . $dtData['reference_no'] . ']',
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
				$inputFileName = 'uploads/import_ch/Phieu_yeu_cau_cong_tac.xlsx';
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
					'font' => array(
						'bold' => true,
						'color' => array('rgb' => '111112'),
						'size' => 11,
						'name' => 'Times New Roman'
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
				$this->db->select('
                tbl_request_bussiness.*,
                tbl_request_bussiness.id as id,
                tbl_request_bussiness.reference_no as reference_no,
                tbl_request_bussiness.date as date,
                tbl_request_bussiness.content as content,
                CONCAT(employees.firstname," ",employees.lastname) as fullname,
                roleemployees.name as local_employees,
                tbl_request_bussiness.quantity as quantity,
                staff_localtion.name as staff_localtion,
                IF(tbl_request_bussiness.object_type = "customer", tblclients.company, IF(tbl_request_bussiness.object_type = "supplier", tblsuppliers.company, object_id)) as company,
                tbl_request_bussiness.address as address,
                tbl_request_bussiness.phone as phone,
                tbl_request_bussiness.time_start as time_start,
                tbl_request_bussiness.time_end as time_end,
                tbl_request_bussiness.amount as amount,
                tbl_request_bussiness.branch_id as branch_id,
            ');
				$this->db->from('tbl_request_bussiness');
				$this->db->join('tblclients', 'tblclients.userid = cast(tbl_request_bussiness.object_id as int) AND tbl_request_bussiness.object_type = "customer"', 'left');
				$this->db->join('tblsuppliers', 'tblsuppliers.id = cast(tbl_request_bussiness.object_id as int) AND tbl_request_bussiness.object_type = "supplier"', 'left');
				$this->db->join('tblstaff employees', 'employees.staffid = tbl_request_bussiness.employees', 'left');
				$this->db->join('tblroles roleemployees', 'roleemployees.roleid = employees.role', 'left');
				$this->db->join('tblroles staff_localtion', 'staff_localtion.roleid = tbl_request_bussiness.staff_localtion', 'left');
				if (!$this->preViewRequestBussiness) {
					$this->db->where('(tbl_request_bussiness.created_by = ' . $staff_id . ')');
				}
				if (!empty($start_date_search)) {
					$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
					$this->db->where("tbl_request_bussiness.date >= '" . $start_date_search . "'");
				}
				if (!empty($end_date_search)) {
					$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
					$this->db->where("tbl_request_bussiness.date <= '" . $end_date_search . "'");
				}
				$this->db->order_by('tbl_request_bussiness.id asc');
				$items = $this->db->get()->result_array();
				$dem = 0;
				$this->load->library('ciqrcode');
				foreach ($items as $key => $value) {
					$row++;
					$dem++;
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['content']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['fullname']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['local_employees']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['quantity']);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['staff_localtion']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['company']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['address']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['phone']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, _dt($value['time_start']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, _dt($value['time_end']), PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, $value['amount']);
					if (!empty($value['barcode'])) {
						$code = $value['barcode'];
					} else {
						$code = 'request_bussiness||' . $value['id'];
						$this->db->where('id', $value['id']);
						$this->db->update('tbl_request_bussiness', ['barcode' => $code]);
					}
					$qr = vn_to_str(str_replace('||', '__', $code));
					$folder = FCPATH . 'uploads/request_bussiness/';
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
						$objDrawing1->setCoordinates($columsExcel[14] . $row);
					}
					$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
					$objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, '')->getStyle($columsExcel[14] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A4:O' . $row)->getAlignment()->setWrapText(true);
				$objPHPExcel->getActiveSheet()->getStyle('A4:O' . $row)->applyFromArray([
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
				$filename = lang('Phieu_yeu_cau_cong_tac') . '.xls';
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
		
		public function searchClients($id = 0)
		{
			$term = $this->input->get('term');
			$limit = get_option('select2_limit');
			$this->db->select('
            tblclients.userid as id, 
            tblclients.company as text,
            tblclients.address as address,
            tblclients.phonenumber as phonenumber,
        ', false);
			$this->db->from('tblclients');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tblclients.company', $term);
				$this->db->or_like('tblclients.zcode', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$pod = $this->db->get()->result_array();
			$data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
			if (!empty($id)) {
				$dtMachines = get_table_where('tblclients', ['userid' => $id], '', 'row_array');
				$data['row'] = ['id' => $dtMachines['userid'], 'text' => $dtMachines['company']];
			}
			echo json_encode($data);
		}
		
		public function searchObject($id = 0) {
			
			$type_object = $this->input->get('type');
			$term = $this->input->get('term');
			$limit = get_option('select2_limit');
			if(empty($id)) {
				if ($type_object == 'customer') {
					$this->db->select('
				tblclients.userid as id,
				tblclients.company as text,
				tblclients.address as address,
				tblclients.phonenumber as phonenumber,
			', false);
					$this->db->from('tblclients');
					if (!empty($term)) {
						$this->db->group_start();
						$this->db->like('tblclients.company', $term);
						$this->db->or_like('tblclients.zcode', $term);
						$this->db->group_end();
					}
					$this->db->limit($limit);
					$pod = $this->db->get()->result_array();
					$data['results'][] = [
						'text' => lang('Khách Hàng'),
						'children' => $pod
					];
				} else if ($type_object == 'supplier') {
					$this->db->select('
					tblsuppliers.id as id,
					tblsuppliers.company as text,
					tblsuppliers.address as address,
					tblsuppliers.phone as phonenumber,
				', false);
					$this->db->from('tblsuppliers');
					if (!empty($term)) {
						$this->db->group_start();
						$this->db->like('tblsuppliers.company', $term);
						$this->db->or_like('tblsuppliers.code', $term);
						$this->db->group_end();
					}
					$this->db->limit($limit);
					$pod = $this->db->get()->result_array();
					$data['results'][] = [
						'text' => lang('Nhà Cung Cấp'),
						'children' => $pod
					];
				}
			}
			else if (!empty($id)) {
				if($type_object == 'customer') {
					$dtClient = get_table_where('tblclients', ['userid' => $id], '', 'row_array');
					$data['row'] = ['id' => $dtClient['userid'], 'text' => $dtClient['company']];
				}
				else if($type_object == 'supplier'){
					$dtSuppliers = get_table_where('tblsuppliers', ['id' => $id], '', 'row_array');
					$data['row'] = ['id' => $dtSuppliers['id'], 'text' => $dtSuppliers['company']];
				}
				
			}
			echo json_encode($data);
		}
	}
