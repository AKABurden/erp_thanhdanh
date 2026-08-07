<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_salary_list extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewCategorySalary = has_permission('category_salary','','view');
        $this->preEditCategorySalary = has_permission('category_salary','','edit');
        $this->preDeleteCategorySalary = has_permission('category_salary','','delete');
    }
	
    public function index() {
		if(!$this->preViewCategorySalary){
			access_denied();
		}
        $data['title'] = _l('c_table_category_salary_list');
        $this->load->view('admin/category_salary_list/manage', $data);
    }
	
	public function table() {
		$aColumns = [
			'tbl_category_salary_list.id as id',
			'tbl_category_salary_list.code as code',
			'tbl_category_salary_list.name as name',
			'(
				SELECT GROUP_CONCAT(CONCAT(tbl_category_salary_list_child.id, "--", tbl_category_salary_list_child.code, "--", tbl_category_salary_list_child.name) SEPARATOR "|||")
				FROM tbl_category_salary_list tbl_category_salary_list_child
				WHERE tbl_category_salary_list_child.id_parent = tbl_category_salary_list.id
			) as category_salary_list_child',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_category_salary_list';
		$where = [
			'AND tbl_category_salary_list.id_parent = 0',
			'AND tbl_category_salary_list.type = 1',
		];
		$join = [];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = '';
			$row[] = '';
			$row[] = '<div class="text-center"><a class="btn btn-danger btn-icon deleteItems" data-href="'.admin_url('category_salary_list/delete/' . $aRow['id']).'"><i class="fa fa-remove"></i></a></div>';
			$row['DT_RowClass'] = 'alert-header bold danger';
			$output['aaData'][] = $row;
			
			$category_salary_list_child = explode('|||', $aRow['category_salary_list_child']);
			if(!empty($category_salary_list_child)) {
				foreach($category_salary_list_child as $k => $v) {
					if(!empty($v)) {
						$v = explode('--', $v);
						if(!empty($v[0])) {
							$row = [];
							$row[] = '';
							$row[] = '';
							$row[] = '';
							$row[] = $v[1];
							$row[] = $v[2];
							$row[] = '<div class="text-center"><a class="btn btn-danger btn-icon deleteItems" data-href="' . admin_url('category_salary_list/delete/' . $v[0]) . '"><i class="fa fa-remove"></i></a></div>';
							$output['aaData'][] = $row;
						}
					}
				}
			}
		}
		echo json_encode($output);die();
	}
	
	public function modal_excel_import() {
        if ($this->preEditCategorySalary){
            accessDenied(true);
        }
		$data['title'] = _l('Import bảng danh mục lương bằng File Excel');
		$this->load->view('admin/category_salary_list/excel_import', $data);
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
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('E');
			$arraydata = array();
			
			$fields = $this->input->post('fields');
			for ($row = 4; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 4][$col] = $value;
				}
			}
			
			$keyCode = '';
			$list_data = [];
			foreach($arraydata as $key => $row) {
				if(!empty($row[1]) && $keyCode != $row[1]) {
					$keyCode = $row[1];
					$list_data[$keyCode] = [
						'code' => $row[1],
						'name' => $row[2],
						'type' => 1
					];
				}
				else {
					$errors .= 'Dòng['.$key.'] Không tìm thấy mã mục';
				}
				
				if(!empty($list_data[$keyCode]) && !empty($row[3])) {
					$list_data[$keyCode]['list_code'][] = $row[3];
					$list_data[$keyCode]['data'][] = [
						'code' => $row[3],
						'name' => $row[4],
						'type' => 2
					];
				}
				else {
					$errors .= 'Dòng['.$key.'] Không tìm thấy mã khoản lương';
				}
			}
			
			$dataUpdateParent = [];
			$dataUpdateChild = [];
			$dataInsertChild = [];
			$count_insert_parent = 0;
			foreach ($list_data as $key => $value) {
				$this->db->where('code', $value['code']);
				$this->db->where('type', 1);
				$category_salary_parent = $this->db->get('tbl_category_salary_list')->row();
				if(!empty($category_salary_parent)) {
					$value['id'] = $category_salary_parent->id;
					$dataUpdateParent[] = [
						'id' => $category_salary_parent->id,
						'name' => $value['name']
					];
				}
				else {
					$insertParent = $this->db->insert('tbl_category_salary_list', [
						'code' => $value['code'],
						'name' => $value['name'],
						'type' => $value['type'],
						'create_by' => get_staff_user_id(),
					]);
					if(!empty($insertParent)) {
						$value['id'] = $this->db->insert_id();
						$count_insert_parent++;
					}
				}
				
				$arrayListChild = [];
				if(!empty($value['list_code'])) {
					$this->db->where_in('code', $value['list_code']);
					$this->db->where('type', 2);
					$category_salary_child = $this->db->get('tbl_category_salary_list')->result_array();
					foreach ($category_salary_child as $k => $v) {
						$arrayListChild[$v['code']] = $v['id'];
					}
				}
				
				foreach($value['data'] as $k => $v) {
					if(!empty($arrayListChild[$v['code']])) {
						$dataUpdateChild[] = [
							'id' => $arrayListChild[$v['code']],
							'name' => $v['name'],
							'id_parent' => $value['id'],
						];
					}
					else {
						$dataInsertChild[] = [
							'id_parent' => $value['id'],
							'code' => $v['code'],
							'name' => $v['name'],
							'type' => 2,
							'create_by' => get_staff_user_id(),
						];
					}
				}
			}
			
			$viewSuccess = [];
			if(!empty($count_insert_parent)) {
				$viewSuccess[] = "Thêm mới ".$count_insert_parent." mục ";
			}
			
			
			if(!empty($dataUpdateParent)){
				$this->db->update_batch('tbl_category_salary_list', $dataUpdateParent, 'id');
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Cập nhật ".$this->db->affected_rows()." mục ";
				}
			}
			
			if(!empty($dataUpdateChild)){
				$this->db->update_batch('tbl_category_salary_list', $dataUpdateChild, 'id');
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Cập nhật ".$this->db->affected_rows()." mã khoản lương ";
				}
			}
			if(!empty($dataInsertChild)){
				$this->db->insert_batch('tbl_category_salary_list', $dataInsertChild);
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Thêm mới ".$this->db->affected_rows()." mã khoản lương ";
				}
			}
			
			if(empty($viewSuccess)) {
				$viewSuccess[] = " Không có dữ liệu được thay đổi";
			}
			
			echo json_encode(
				[
					'success' => true,
					'errors' => $errors,
					'alert_type' => 'success',
					'message' => implode('Và ', $viewSuccess),
				]
			);
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
        if (!$this->preDeleteCategorySalary){
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
