<?php
defined('BASEPATH') or exit('No direct script access allowed');
class List_subsidize extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewListSubsidize = has_permission('list_subsidize','','view');
        $this->preEditListSubsidize = has_permission('list_subsidize','','edit');
        $this->preDeleteListSubsidize= has_permission('list_subsidize','','delete');
    }
	
    public function index() {
		if(!$this->preViewListSubsidize){
			access_denied();
		}
        $data['title'] = _l('c_list_subsidize');
        $this->load->view('admin/list_subsidize/manage', $data);
    }
	
	public function table() {
		$aColumns = [
			'tbl_list_subsidize.id as id',
			'tbl_list_subsidize.name as name',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_list_subsidize';
		$where = [];
		$join = [];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['name'];
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '';
			$row[] = '<div class="text-center"><a class="btn btn-danger btn-icon deleteItems" data-href="'.admin_url('list_subsidize/delete/' . $aRow['id']).'"><i class="fa fa-remove"></i></a></div>';
			$row['DT_RowClass'] = 'alert-header bold danger';
			$output['aaData'][] = $row;
			
			$this->db->select([
				'tbl_list_subsidize_detail.*',
				'tblroles.code_role',
				'CONCAT(COALESCE(firstname), " ", COALESCE(lastname)) as fullname'
			]);
			$this->db->where('id_list_subsidize', $aRow['id']);
			$this->db->join('tblroles', 'tblroles.roleid = tbl_list_subsidize_detail.id_role');
			$this->db->join('tblstaff', 'tblstaff.staffid = tbl_list_subsidize_detail.id_staff');
			$list_subsidize_detail = $this->db->get('tbl_list_subsidize_detail')->result_array();
			if(!empty($list_subsidize_detail)) {
				foreach($list_subsidize_detail as $k => $v) {
					$row = [];
					$row[] = '';
					$row[] = '';
					$row[] = $v['code_role'];
					$row[] = $v['fullname'];
					$row[] = '<div class="text-center">' . _dC($v['date_start']) . '</div>';
					$row[] = '<div class="text-center">' . (!empty($v['date_end']) ? _dC($v['date_end']) : NULL). '</div>';
					$row[] = '<div class="text-center">' . number_format_data($v['quantity_child']). '</div>';
					$row[] = '<div class="text-center">' . number_format_data($v['worth']) . '</div>';
					$row[] = '<div class="text-center">' . $v['unit'] . '</div>';
					$row[] = '<div class="text-center"><a class="btn btn-danger btn-icon deleteItems" data-href="'.admin_url('list_subsidize/delete/' . $aRow['id']).'"><i class="fa fa-remove"></i></a></div>';
					$output['aaData'][] = $row;
				}
			}
			
		}
		echo json_encode($output);die();
	}
	
	public function modal_excel_import() {
        if(!$this->preEditListSubsidize){
            accessDenied(true);
        }
		$data['title'] = _l('Import danh sách trợ cấp bằng File Excel');
		$this->load->view('admin/list_subsidize/excel_import', $data);
	}
	
	public function excel_import()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		$count = 0;
		$errors = '';
		$data = [];
		if (!empty($_FILES['file'])) {
			$fullfile = $_FILES['file']['tmp_name'];
			$nameFile = $_FILES['file']['name'];
			$extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			if ($extension != 'XLSX' && $extension != 'XLS') {
				echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
				die();
			}
			$inputFileType = PHPExcel_IOFactory::identify($fullfile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			// $objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			
			$total_sheets = $objPHPExcel->getSheetCount();
			
			$allSheetName = $objPHPExcel->getSheetNames();
			$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
			$highestRow = $objWorksheet->getHighestRow();
			$highestColumn = $objWorksheet->getHighestColumn();
			// $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('W');
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('K');
			$arraydata = array();
			
			$fields = $this->input->post('fields');
			for ($row = 4; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					if ($col == 5 || $col == 6) {
						if (gettype($value) == 'double' || gettype($value) == 'int') {
							$dateTime = PHPExcel_Shared_Date::ExcelToPHP($value);
							$days = floor($dateTime / 86400);
							$time = round((($dateTime / 86400) - $days) * 86400);
							$hours = round($time / 3600);
							$minutes = round($time / 60) - ($hours * 60);
							$seconds = round($time) - ($hours * 3600) - ($minutes * 60);
							$dateObj = date_create('1-Jan-1970+' . $days . ' days');
							$value = $dateObj->setTime($hours, $minutes, $seconds);
							$value = $value->format('d-m-Y H:i:s');
						}
					}
					$arraydata[$row][$col] = $value;
				}
			}
			$keyCode = '';
			$list_data = [];
			foreach($arraydata as $key => $row) {
				if(!empty($row[1]) && $keyCode != $row[1]) {
					$keyCode = $row[1];
					$list_data[$keyCode] = [
						'name' => $row[1],
					];
				}
				else {
					if(empty($keyCode)) {
						$errors .= 'Dòng[' . $key . '] Không tìm thấy tên nhóm trợ cấp<br/>';
					}

                    $this->db->from('tbl_allowance_reduce');
                    $this->db->where('tbl_allowance_reduce.name',$keyCode);
                    $dtAllowance = $this->db->get()->row_array();
                    if (!empty($dtAllowance)){
                        $allowance_reduce_id = $dtAllowance['id'];
                        $list_data[$keyCode]['allowance_reduce_id'] = $allowance_reduce_id;
                    } else {
                        $allowance_reduce_id = 0;
                        $list_data[$keyCode]['allowance_reduce_id'] = $allowance_reduce_id;
                        continue;
                    }
				}
				
				if(!empty($list_data[$keyCode]) && !empty($row[2])) {
					$list_data[$keyCode]['role'][] = $row[2];
					$list_data[$keyCode]['data'][$key]['role'] = $row[2];
				}
				else {
					if(empty($row[1])) {
						$errors .= 'Dòng[' . $key . '] Không tìm thấy mã vị trí<br/>';
						continue;
					}
				}
				
				if(!empty($list_data[$keyCode]) && !empty($row[3])) {
					$list_data[$keyCode]['staff'][] = $row[3];
					$list_data[$keyCode]['data'][$key]['staff'] = $row[3];
				}
				else {
					if(empty($row[1])) {
						$errors .= 'Dòng[' . $key . '] Không tìm thấy mã nhân viên<br/>';
						continue;
					}
				}
				$list_data[$keyCode]['data'][$key]['date_start'] = !empty($row[5]) ? @to_sql_date($row[5], true) : NULL;
				$list_data[$keyCode]['data'][$key]['date_end'] = !empty($row[6]) ? @to_sql_date($row[6], true) : NULL;
				$list_data[$keyCode]['data'][$key]['quantity_child'] = !empty($row[7]) ? $row[7] : NULL;
				$list_data[$keyCode]['data'][$key]['worth'] = !empty($row[8]) ? $row[8] : NULL;
				$list_data[$keyCode]['data'][$key]['unit'] = !empty($row[9]) ? $row[9] : NULL;
			}
			
			$dataUpdateParent = [];
			$dataInsert = [];
			$count_insert_parent = 0;
			foreach ($list_data as $key => $value) {
                if (empty($value['allowance_reduce_id'])) {
                    $errors .= '[' . $value['name'] . '] Không tìm thấy tên trợ cấp trên phần mềm<br/>';
                    continue;
                }
				$this->db->where('allowance_reduce_id', $value['allowance_reduce_id']);
				$list_subsidize = $this->db->get('tbl_list_subsidize')->row();
				if(!empty($list_subsidize)) {
					$value['id_list_subsidize'] = $list_subsidize->id;
					$dataUpdateParent[] = [
						'id' => $list_subsidize->id,
						'allowance_reduce_id' => $value['allowance_reduce_id']
					];
				} else {
					$insertParent = $this->db->insert('tbl_list_subsidize', [
						'code' => !empty($value['code']) ? $value['code'] : null,
						'name' => !empty($value['name']) ? $value['name'] : null,
						'create_by' => get_staff_user_id(),
						'allowance_reduce_id' => $value['allowance_reduce_id'],
					]);
					if(!empty($insertParent)) {
						$value['id_list_subsidize'] = $this->db->insert_id();
						$count_insert_parent++;
					}
				}
				
				$arrayListRole = [];
				if(!empty($value['role'])) {
					$this->db->where_in('code_role', $value['role']);
					$list_roles = $this->db->get('tblroles')->result_array();
					foreach ($list_roles as $k => $v) {
						$arrayListRole[$v['code_role']] = $v['roleid'];
					}
				}
				
				$arrayListStaff = [];
				if(!empty($value['staff'])) {
					$this->db->where_in('code', $value['staff']);
					$list_staffs = $this->db->get('tblstaff')->result_array();
					foreach ($list_staffs as $k => $v) {
						$arrayListStaff[$v['code']] = $v['staffid'];
					}
				}
				
				foreach($value['data'] as $k => $v) {
					if(empty($v['role']) || empty($v['staff'])) {
						continue;
					}
					$this->db->where('id_list_subsidize', $value['id_list_subsidize']);
					$this->db->where('id_role', $arrayListRole[$v['role']]);
					$this->db->where('id_staff', $arrayListStaff[$v['staff']]);
					$this->db->where('date_start', $v['date_start']);
					$subsidize_detail = $this->db->get('tbl_list_subsidize_detail')->row();
					if(!empty($subsidize_detail)) {
						$dataUpdate[] = [
							'id' => $subsidize_detail->id,
							'id_list_subsidize' => $value['id_list_subsidize'],
							'id_role' => !empty($arrayListRole[$v['role']]) ? $arrayListRole[$v['role']] : NULL,
							'id_staff' => !empty($arrayListStaff[$v['staff']]) ? $arrayListStaff[$v['staff']] : NULL,
							'date_start' => (!empty($v['date_start']) ? $v['date_start'] : NULL),
							'date_end' => (!empty($v['date_end']) ? $v['date_end'] : NULL),
							'quantity_child' => $v['quantity_child'],
							'worth' => $v['worth'],
							'unit' => $v['unit'],
						];

					}
					else {
						$dataInsert[] = [
							'id_list_subsidize' => $value['id_list_subsidize'],
							'id_role' => !empty($arrayListRole[$v['role']]) ? $arrayListRole[$v['role']] : NULL,
							'id_staff' => !empty($arrayListStaff[$v['staff']]) ? $arrayListStaff[$v['staff']] : NULL,
							'date_start' => (!empty($v['date_start']) ? $v['date_start'] : NULL),
							'date_end' => (!empty($v['date_end']) ? $v['date_end'] : NULL),
							'quantity_child' => $v['quantity_child'],
							'worth' => $v['worth'],
							'unit' => $v['unit'],
						];
					}
                    $this->db->from('tbl_staff_allowance');
                    $this->db->where('tbl_staff_allowance.category_id',$value['allowance_reduce_id']);
                    $this->db->where('tbl_staff_allowance.staff_id',(!empty($arrayListStaff[$v['staff']]) ? $arrayListStaff[$v['staff']] : 0));
                    $dtStaffAllowance = $this->db->get()->row_array();
                    if (!empty($dtStaffAllowance)){
                        $this->db->where('id',$dtStaffAllowance['id']);
                        $this->db->update('tbl_staff_allowance',[
                            'amount' =>$v['worth']
                        ]);
                    } else {
                        $this->db->insert('tbl_staff_allowance',[
                            'category_id' => $value['allowance_reduce_id'],
                            'staff_id' => $arrayListStaff[$v['staff']],
                            'amount' => $v['worth'],
                            'date_created' => date('Y-m-d H:i:s'),
                            'created_by' => get_staff_user_id()
                        ]);
                    }
				}
			}
			
			$viewSuccess = [];
			if(!empty($count_insert_parent)) {
				$viewSuccess[] = "Thêm mới ".$count_insert_parent." mục ";
			}
			
			if(!empty($dataUpdateParent)){
				$this->db->update_batch('tbl_list_subsidize', $dataUpdateParent, 'id');
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Cập nhật ".$this->db->affected_rows()." nhóm trợ cấp ";
				}
			}
			
			if(!empty($dataInsert)){
				$this->db->insert_batch('tbl_list_subsidize_detail', $dataInsert);
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Thêm mới ".$this->db->affected_rows()." chi tiết trợ cấp ";
				}
			}
			
			if(!empty($dataUpdate)){
				$this->db->update_batch('tbl_list_subsidize_detail', $dataUpdate, 'id');
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Cập nhật ".$this->db->affected_rows()." chi tiết trợ cấp ";
				}
			}
			
			if(empty($viewSuccess)) {
				$viewSuccess[] = " Không có dữ liệu được thay đổi";
			}
			
			echo json_encode([
					'success' => true,
					'errors' => $errors,
					'alert_type' => 'success',
					'message' => implode('Và ', $viewSuccess),
			]);
			die();
		}
		echo json_encode([
			'success' => true,
			'errors' => $errors,
			'alert_type' => 'success',
			'message' => 'Import thành công ' . $count . ' dòng',
		]);
		die();
	}
	
	public function delete($id = '') {
        if (!$this->preDeleteListSubsidize){
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Không có quyền xóa']);die();
        }
		if(!empty($id)) {
			$this->db->where('id', $id);
			$category_salary = $this->db->get('tbl_category_salary_list')->row();
			if(!empty($category_salary)) {
				$this->db->where('id', $id);
				$success = $this->db->delete('tbl_category_salary_list');
				if(!empty($success)) {
					if(empty($category_salary->id_parent)) {
						$this->db->where('id', $category_salary->id_parent);
						$this->db->delete('tbl_category_salary_list');
					}
					
					echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa dữ liệu thành công']);die();
				}
			}
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa dữ liệu không thành công']);die();
	}
}
