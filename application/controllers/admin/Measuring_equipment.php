<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Measuring_equipment extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('category_model');
		$this->load->model('personnel_model');
	}

	public function index()
	{
		$data['tnh'] = true;
		$data['title'] = _l('Danh sách thiết bị đo kiểm');
		$this->load->view('admin/measuring_equipment/manage', $data);
	}

	public function detail($id = '')
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			if(!empty($id)) {
				$this->db->where('code', $data['code']);
				$this->db->where('id != "'.$id.'"', false, false);
				$ktCode = $this->db->get('tblmeasuring_equipment')->row();
				if(!empty($ktCode)) {
					echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Mã thiết bị đã tồn tại vui lòng nhập mã thiết bị khác']);die();
				}
				
				
				$dataUpdate = [
					'code' => $data['code'],
					'name' => $data['name'],
					'product_in_month' => $data['product_in_month'],
					'stage_id' => $data['stage_id'],
					'status' => $data['status'],
					'specifications' => $this->input->post('specifications', false),
					'note' => $this->input->post('note', false),
				];
				
				$this->db->where('id', $id);
				$success = $this->db->update('tblmeasuring_equipment', $dataUpdate);
				if(!empty($success)) {
					echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật thành công']);die();
				}
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật không thành công']);die();
			}
			else {
				$this->db->where('code', $data['code']);
				$ktCode = $this->db->get('tblmeasuring_equipment')->row();
				if(!empty($ktCode)) {
					echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Mã thiết bị đã tồn tại vui lòng nhập mã thiết bị khác']);die();
				}
				
				$dataInsert = [
					'code' => $data['code'],
					'name' => $data['name'],
					'product_in_month' => $data['product_in_month'],
					'stage_id' => $data['stage_id'],
					'status' => $data['status'],
					'specifications' => $this->input->post('specifications', false),
					'note' => $this->input->post('note', false),
					'date_create' => date('Y-m-d H:i:s'),
					'create_by' => get_staff_user_id()
				];
				$success = $this->db->insert('tblmeasuring_equipment', $dataInsert);
				if(!empty($success)) {
					echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Thêm mới thành công']);die();
				}
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm mới không thành công']);die();
			}
		}
		else {
			$data['title'] = 'Thêm thiết bị đo kiểm';
			if(!empty($id)) {
				$data['title'] = 'Sửa thiết bị đo kiểm';
				$data['measuring_equipment'] = $this->db->get_where('tblmeasuring_equipment', ['id' => $id])->row();
			}
			$data['list_stage'] = $this->db->get_where('tbl_category_stages')->result_array();
			$this->load->view('admin/measuring_equipment/detail', $data);
		}
	}

	function table()
	{
		
		$aColumns = [
			'tblmeasuring_equipment.id as id',
			'tblmeasuring_equipment.code as code',
			'tblmeasuring_equipment.name as name',
			'tblmeasuring_equipment.product_in_month as product_in_month',
			'tblmeasuring_equipment.status as status',
			'tblmeasuring_equipment.specifications as specifications',
			'tbl_category_stages.code as code_stage',
			'tblmeasuring_equipment.note as note',
		];
		$sIndexColumn = 'id';
		$sTable = 'tblmeasuring_equipment';
		$where = [];
		$filter = [];
		$j = 0;
		$join = [
			'LEFT JOIN tbl_category_stages ON tbl_category_stages.id = tblmeasuring_equipment.stage_id'
		];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblmeasuring_equipment.id'], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$status_machine_new = status_machine_new();
		foreach ($rResult as $key => $aRow) {
			$row = array();
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = $aRow['product_in_month'];
			$status = '<div class="text-left mbot5"><span class="label label-danger btn-icon" data-original-title="Duyệt">'.(!empty($status_machine_new[$aRow['status']]) ? $status_machine_new[$aRow['status']] : '').'</span></div>';
			$row[] = $status;
			$row[] = $aRow['specifications'];
			$row[] = $aRow['code_stage'];
			$row[] = $aRow['note'];
			$options = '';
			$options .= '<a class="btn btn-default btn-icon c_modal" href="'.admin_url('measuring_equipment/detail/' .$aRow['id']).'"><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-danger btn-icon c_delete" href="'.admin_url('measuring_equipment/delete').'" data-id="'.$aRow['id'].'"><i class="fa fa-remove"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if (!empty($id)) {
			$this->db->where('id', $id);
			$success = $this->db->delete('tblmeasuring_equipment');
			if(!empty($success)) {
				echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa dữ liệu thành công']);die();
			}
			
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa dữ liệu không thành công']);die();
	}
	
	public function modal_excel()
	{
		$data['title'] = _l('Import excel thiết bị đo kiểm');
		$this->load->view('admin/measuring_equipment/import', $data);
	}
	
	
	public function import()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		
		//		$type_items = $this->db->get('tbltype_items')->result_array();
		//		foreach($type_items as $key => $value) {
		//			$data_type_items[mb_strtolower($value['name'],'UTF-8')] = $value['type'];
		//		}
		
		
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
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			$total_sheets = $objPHPExcel->getSheetCount();
			$allSheetName = $objPHPExcel->getSheetNames();
			$listRow = [
				1 => 'code',
				2 => 'name',
				3 => 'status',
				4 => 'product_in_month',
				5 => 'stage_id',
				6 => 'specifications',
				7 => 'note',
			];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				for ($i = 2; $i <= $highestRow; $i++) {
					$redata = [];
					for ($j = 1; $j < $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						if ($j == 2) {
							if (!empty($Val)) {
								$vaKey = $Val;
							}
						}
						$redata[$listRow[$j]] = trim($Val);
					}
					if (!empty($vaKey)) {
						$data[$vaKey] = $redata;
					}
				}
			}
		}
		$count = 0;
		if (!empty($data)) {
			
			$status_machine = status_machine_new();
			$data_status_machine = [];
			foreach ($status_machine as $key => $value) {
				$data_status_machine[mb_strtolower($value, 'UTF-8')] = $key;
			}
			
			foreach ($data as $key => $value) {
				$stage_id = $this->db->get_where('tbl_category_stages', ['code' => $value['stage_id']])->row('id');
				$options = [
					'code' => $value['code'],
					'name' => $value['name'],
					'status' => !empty($data_status_machine[mb_strtolower($value['status'], 'UTF-8')]) ? $data_status_machine[mb_strtolower($value['status'], 'UTF-8')] : 0,
					'product_in_month' => $value['product_in_month'],
					'specifications' => $value['specifications'],
					'note' => $value['note'],
					'stage_id' => (!empty($stage_id) ? $stage_id : 0),
					'create_by' => get_staff_user_id()
				];
				$this->db->where('code', $value['code']);
				$ktmachines = $this->db->get('tblmeasuring_equipment')->row();
				if (!empty($ktmachines)) {
					continue;
				}
				
				$id = $this->db->insert('tblmeasuring_equipment', $options);
				if (!empty($id)) {
					$count++;
				}
			}
		}
		echo json_encode(
			[
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Import thành công ' . $count . ' Items',
			]
		);
		die();
	}
	
	public function export_excel()
	{
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		
		// print_arrays($this->input->post());
		$cloumns = $this->input->post('cloumns');
		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		
		$status_machine = status_machine_new();
		
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
		
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		
		
		$numberRow = 2;
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(15);
		$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Mã Thiết Bị/Công Việc')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Tên Thiết Bị/Công Việc')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Trạng Thái')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Định Mức Năng Suất/Tháng')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Nhóm Công Đoạn')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Thông số kỹ thuật')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'Ghi chú')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header']);
		$numberRow++;
		
		$stt = 1;
		
		$this->db->select('tblmeasuring_equipment.*, tbl_category_stages.code as code_category_stages');
		$this->db->join('tbl_category_stages', 'tbl_category_stages.id = tblmeasuring_equipment.stage_id', 'left');
		$measuring_equipment = $this->db->get('tblmeasuring_equipment')->result_array();
		if (!empty($measuring_equipment)) {
			foreach ($measuring_equipment as $key => $value) {
				$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", $stt)->getStyle("A$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", $value['code'])->getStyle("B$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $value['name'])->getStyle("C$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", (!empty($status_machine[$value['status']]) ? $status_machine[$value['status']] : ''))->getStyle("D$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", $value['product_in_month'])->getStyle("E$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->getStyle("E$numberRow")->getAlignment()->setWrapText(true);
				
				$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", $value['code_category_stages'])->getStyle("F$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $value['specifications'])->getStyle("G$numberRow")->applyFromArray($style_excel['BStyle_center']);
				
				$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", $value['note'])->getStyle("H$numberRow")->applyFromArray($style_excel['BStyle_center']);
				$objPHPExcel->getActiveSheet()->getStyle("H$numberRow")->getAlignment();
				$stt++;
				$numberRow++;
			}
		}
		
		
		$filename = lang('Danh_sach_thiet_bi_do_kiem') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}
