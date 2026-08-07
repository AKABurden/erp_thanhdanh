<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_eloquence extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->preViewCategoryEloquence = has_permission('category_eloquence','','view');
        $this->preEditCategoryEloquence = has_permission('category_eloquence','','edit');
        $this->preDeleteCategoryEloquence = has_permission('category_eloquence','','delete');
    }
	
    public function index() {
		if(!$this->preViewCategoryEloquence){
			access_denied();
		}
        $data['title'] = _l('c_table_category_eloquence');
        $this->load->view('admin/category_eloquence/manage', $data);
    }
	
	public function table() {
		$aColumns = [
			'tbl_category_eloquence.id as id',
			'tbl_category_eloquence.code as code',
			'tbl_category_eloquence.name as name',
			'tbl_category_eloquence.criteria as criteria',
			'tbl_category_eloquence.worth as worth',
			'tbl_category_eloquence.unit_id as unit_id',
			'tbl_category_eloquence.applicable_formula as applicable_formula',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_category_eloquence';
		$where = [];
		$join = [];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$row[] = '<div class="text-center">' . $aRow['id'] . '</div>';
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = $aRow['criteria'];
			$row[] = $aRow['worth'];
			$row[] = $aRow['unit_id'];
			$row[] = $aRow['applicable_formula'];
			$row[] = '<div class="text-center"><a class="btn btn-danger btn-icon deleteItems" data-href="'.admin_url('category_eloquence/delete/' . $aRow['id']).'"><i class="fa fa-remove"></i></a></div>';
			$output['aaData'][] = $row;
			
		}
		echo json_encode($output);die();
	}
	
	public function modal_excel_import() {
        if(!$this->preEditCategoryEloquence){
            accessDenied(true);
        }
		$data['title'] = _l('Import bảng khoản phép bằng File Excel');
		$this->load->view('admin/category_eloquence/excel_import', $data);
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
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('G');
			$arraydata = array();
			$fields = $this->input->post('fields');
			for ($row = 4; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row][$col] = $value;
				}
			}
			
			$keyCode = '';
			$list_data = [];
			foreach($arraydata as $key => $row) {
				if(!empty($row[1]) && $keyCode != $row[1]) {
					$list_data[] = [
						'code' => $row[1],
						'name' => $row[2],
						'criteria' => $row[3],
						'worth' => $row[4],
						'unit_id' => $row[5],
						'applicable_formula' => $row[6],
					];
					
				}
				else {
					$errors .= "Dòng[$key] Không tìm thấy mã khoản phép<br>";
				}
			}
			
			$dataUpdate = [];
			$dataInsert = [];
			foreach ($list_data as $key => $value) {
				$this->db->where('code', $value['code']);
				$category_eloquence = $this->db->get('tbl_category_eloquence')->row();
				if(!empty($category_eloquence)) {
					$dataUpdate[] = [
						'id' => $category_eloquence->id,
						'name' => $value['name'],
						'criteria' => $value['criteria'],
						'worth' => $value['worth'],
						'unit_id' => $value['unit_id'],
						'applicable_formula' => $value['applicable_formula'],
					];
				}
				else {
					$dataInsert[] = [
						'code' => $value['code'],
						'name' => $value['name'],
						'criteria' => $value['criteria'],
						'worth' => $value['worth'],
						'unit_id' => $value['unit_id'],
						'applicable_formula' => $value['applicable_formula'],
						'create_by' => get_staff_user_id()
					];
				}
			}
			
			$viewSuccess = [];
			
			if(!empty($dataUpdate)){
				$this->db->update_batch('tbl_category_eloquence', $dataUpdate, 'id');
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Cập nhật ".$this->db->affected_rows()." khoản phép ";
				}
			}
			
			if(!empty($dataInsert)){
				$this->db->insert_batch('tbl_category_eloquence', $dataInsert);
				if ($this->db->affected_rows() > 0) {
					$viewSuccess[] = " Thêm mới ".$this->db->affected_rows()." khoản phép ";
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
        if (!$this->preDeleteCategoryEloquence){
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Không có quyền xóa']);die();
        }
		if(!empty($id)) {
			$this->db->where('id', $id);
			$category_eloquence= $this->db->get('tbl_category_eloquence')->row();
			if(!empty($category_eloquence)) {
				$this->db->where('id', $id);
				$success = $this->db->delete('tbl_category_eloquence');
				if(!empty($success)) {
					echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa dữ liệu thành công']);die();
				}
			}
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa dữ liệu không thành công']);die();
	}
}
