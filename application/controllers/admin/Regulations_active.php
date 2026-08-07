<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Regulations_active extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			if(!is_admin()) {
				access_denied();
			}
		}
		
		public function index() {
			$data['title'] = _l('Quy chế hoạt động phòng ban');
			$this->load->view('admin/regulations_active/manage', $data);
		}
		
		public function table() {
			
			$aColumns = [
				'tbl_regulations_active.id as id',
				'tbl_regulations_active.code as code_department',
				'tbl_regulations_active.name as name_department',
				'tbl_regulations_active.under as under',
				'tbl_regulations_active.date_issued as date_issued',
				'tbl_regulations_active.version as version',
				'tbl_regulations_active.date_update as date_update',
			];
			
			$sIndexColumn = 'id';
			$sTable       = 'tbl_regulations_active';
			$join = [];
			$where = [];
			$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
			$output       = $result['output'];
			$rResult      = $result['rResult'];
			foreach ($rResult as $aRow) {
				$row = [];
				$row[] = $aRow['id'];
				$row[] = '<a target="_blank" href="'.(admin_url('regulations_active/detail/' . $aRow['id'])).'">' . $aRow['code_department']. '</a>';
				$row[] = $aRow['name_department'];
				$row[] = $aRow['under'];
				$row[] = _dC($aRow['date_issued']);
				$row[] = $aRow['version'];
				$row[] = _dC($aRow['date_update']);
				$row[] = '<div class="text-center">
							<a target="_blank" class="btn btn-icon btn-default" href="'.(admin_url('regulations_active/detail/' . $aRow['id'])).'"><i class="fa fa-edit"></i></a>
							<a class="btn btn-icon btn-danger deleteItems" data-href="'.(admin_url('regulations_active/delete/' . $aRow['id'])).'"><i class="fa fa-remove"></i></a>
						 </div>';
				$output['aaData'][] = $row;
			}
			echo json_encode($output);die();
		}
		
		public function detail($id = '') {
			$this->db->where('id', $id);
			$data['regulations_active'] = $this->db->get('tbl_regulations_active')->row();
			if(!empty($data['regulations_active'])) {
				$this->db->where('id_regulations_active', $id);
				$data['regulations_active_detail'] = $this->db->get('tbl_regulations_active_detail')->result_array();
			}
			else {
				set_alert('danger', 'Không tìm thấy quy chế hoạt động phòng ban');
				redirect(admin_url('regulations_active'));
			}
			$data['title'] = 'Xem Quy Chế Hoạt Động Phòng Ban - ' . $data['regulations_active']->name;
			$this->load->view('admin/regulations_active/detail', $data);
		}
		
		public function delete($id = '') {
			$this->db->where('id', $id);
			$successDelete = $this->db->delete('tbl_regulations_active');
			if(!empty($successDelete)) {
				$this->db->where('id_regulations_active', $id);
				$this->db->delete('tbl_regulations_active_detail');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa dữ liệu thành công'
				]);die();
			}
			echo json_encode([
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Xóa dữ liệu không thành công'
			]);die();
		}
		
		public function delete_detail($id = '') {
			$this->db->where('id', $id);
			$successDelete = $this->db->delete('tbl_regulations_active_detail');
			if(!empty($successDelete)) {
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa dữ liệu thành công'
				]);die();
			}
			echo json_encode([
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Xóa dữ liệu không thành công'
			]);die();
		}
		
		public function modal_excel_import() {
			$data['title'] = _l('Import Quy Chế Hoạt Động Phòng Ban Bằng File Excel');
			$data['fileTemplate'] = base_url('uploads/template/mau_import_quy_che_hoat_dong_phong_ban.xlsx');
			$this->load->view('admin/regulations_active/excel_import', $data);
		}
		
		public function excel_import() {
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
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('U');
				$arraydata = array();
				$fields = $this->input->post('fields');
				for ($row = 4; $row <= $highestRow; ++$row) {
					for ($col = 0; $col < $highestColumnIndex; ++$col) {
						$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
						if ((isset($value)) && $value != "") {
							if ($col == 4 || $col == 6) {
								if (gettype($value) == 'double' || gettype($value) == 'int') {
									$dateTime = PHPExcel_Shared_Date::ExcelToPHP($value);
									$days = floor($dateTime / 86400);
									$time = round((($dateTime / 86400) - $days) * 86400);
									$hours = round($time / 3600);
									$minutes = round($time / 60) - ($hours * 60);
									$seconds = round($time) - ($hours * 3600) - ($minutes * 60);
									$dateObj = date_create('1-Jan-1970+' . $days . ' days');
									$value = $dateObj->setTime($hours, $minutes, $seconds);
									$value = $value->format('Y-m-d H:i:s');
								}
							}
						}
						$arraydata[$row][$col] = $value;
					}
				}
				
				$keyCode = '';
				$list_data = [];
				foreach($arraydata as $key => $row) {
					if(empty($row[1])) {
						$errors .= 'Dòng ' . $key . ' Không tìm thấy mã phòng ban<br/>';
						continue;
					}
					$list_data[] = [
						'code' => !empty($row[1]) ? $row[1] : NULL,
						'name' => !empty($row[2]) ? $row[2] : NULL,
						'under' => !empty($row[3]) ? $row[3] : NULL,
						'date_issued' => !empty($row[4]) ? $row[4] : NULL,
						'version' => !empty($row[5]) ? $row[5] : NULL,
						'date_update' => !empty($row[6]) ? $row[6] : NULL,
						'room_function' => !empty($row[7]) ? $row[7] : NULL,
						'room_tasks' => !empty($row[8]) ? $row[8] : NULL,
						'room_powers' => !empty($row[9]) ? $row[9] : NULL,
						'file_under' => !empty($row[10]) ? $row[10] : NULL,
						'code_locus' => !empty($row[11]) ? $row[11] : NULL,
						'name_position' => !empty($row[12]) ? $row[12] : NULL,
						'job_position' => !empty($row[13]) ? $row[13] : NULL,
						'procedure' => !empty($row[14]) ? $row[14] : NULL,
						'goals_year' => !empty($row[15]) ? $row[15] : NULL,
						'result_year' => !empty($row[16]) ? $row[16] : NULL,
						'result_quarter_one' => !empty($row[17]) ? $row[17] : NULL,
						'result_quarter_two' => !empty($row[18]) ? $row[18] : NULL,
						'result_quarter_three' => !empty($row[19]) ? $row[19] : NULL,
						'result_quarter_four' => !empty($row[20]) ? $row[20] : NULL,
					];
				}
				$dataUpdate = [];
				$dataInsert = [];
				foreach ($list_data as $key => $value) {
					if(empty($value['code'])) {
						continue;
					}
					$this->db->where('code', $value['code']);
					$regulations_active = $this->db->get('tbl_regulations_active')->row();
					if(!empty($regulations_active)) {
						$value['id_regulations_active'] =  $regulations_active->id;
						$this->db->where('id', $regulations_active->id);
						$this->db->update('tbl_regulations_active', [
							'name' => $value['name'],
							'under' => $value['under'],
							'date_issued' => $value['date_issued'],
							'version' => $value['version'],
							'date_update' => $value['date_update'],
						]);
					}
					else {
						$succCategory = $this->db->insert('tbl_regulations_active', [
							'code' => $value['code'],
							'name' => $value['name'],
							'under' => $value['under'],
							'date_issued' => $value['date_issued'],
							'version' => $value['version'],
							'date_update' => $value['date_update'],
							'create_by' => get_staff_user_id(),
						]);
						if(!empty($succCategory)) {
							$value['id_regulations_active'] =  $this->db->insert_id();
						}
					}
					
					if(!empty($value['id_regulations_active'])) {
						$dataInsert[] = [
							'id_regulations_active' => $value['id_regulations_active'],
							'room_function' => !empty($value['room_function']) ? $value['room_function'] : NULL,
							'room_tasks' => !empty($value['room_tasks']) ? $value['room_tasks'] : NULL,
							'room_powers' => !empty($value['room_powers']) ? $value['room_powers'] : NULL,
							'file_under' => !empty($value['file_under']) ? $value['file_under'] : NULL,
							'code_locus' => !empty($value['code_locus']) ? $value['code_locus'] : NULL,
							'name_position' => !empty($value['name_position']) ? $value['name_position'] : NULL,
							'job_position' => !empty($value['job_position']) ? $value['job_position'] : NULL,
							'procedure' => !empty($value['procedure']) ? $value['procedure'] : NULL,
							'goals_year' => !empty($value['goals_year']) ? $value['goals_year'] : NULL,
							'result_year' => !empty($value['result_year']) ? $value['result_year'] : NULL,
							'result_quarter_one' => !empty($value['result_quarter_one']) ? $value['result_quarter_one'] : NULL,
							'result_quarter_two' => !empty($value['result_quarter_two']) ? $value['result_quarter_two'] : NULL,
							'result_quarter_three' => !empty($value['result_quarter_three']) ? $value['result_quarter_three'] : NULL,
							'result_quarter_four' => !empty($value['result_quarter_four']) ? $value['result_quarter_four'] : NULL,
						];
						
					}
				}
				
				$viewSuccess = [];
				if(!empty($dataInsert)){
					$this->db->insert_batch('tbl_regulations_active_detail', $dataInsert);
					if ($this->db->affected_rows() > 0) {
						$affected_rows = $this->db->affected_rows();
						$viewSuccess[] = " Thêm mới " . $affected_rows . " dữ liệu ";
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
		
		public function export_excel($id = '')
		{
			ini_set('memory_limit', '3500M');
			include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
			$this->load->library('PHPExcel');
			$style_excel = style_excel();
			$cloumns_excel = cloumns_excel();
			$style_excel['Background_header_one'] = $style_excel['Background_header'];
			$style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
			$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
			$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
			
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			
			
			$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(25);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(50);
			$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension("U")->setWidth(30);
			
			$this->db->where('id', $id);
			$regulations_active = $this->db->get('tbl_regulations_active')->row();
			
			
			$objPHPExcel->getActiveSheet()->SetCellValue("A1", ('Quy Chế Hoạt Động Phòng Ban'))->getStyle("A1")->applyFromArray($style_excel['c_head']);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
			
			$numberRow = 2;
			$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'Mã Phòng Ban');
			$objPHPExcel->getActiveSheet()->mergeCells("A$numberRow:B$numberRow")->getStyle("A$numberRow:B$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $regulations_active->code);
			$objPHPExcel->getActiveSheet()->mergeCells("C$numberRow:D$numberRow")->getStyle("C$numberRow:D$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Ngày Ban Hành');
			$objPHPExcel->getActiveSheet()->mergeCells("E$numberRow:F$numberRow")->getStyle("E$numberRow:F$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", _dC($regulations_active->date_issued));
			$objPHPExcel->getActiveSheet()->mergeCells("G$numberRow:H$numberRow")->getStyle("G$numberRow:H$numberRow")->applyFromArray($style_excel['BStyle']);
			$numberRow++;
			
			$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'Tên Phòng Ban');
			$objPHPExcel->getActiveSheet()->mergeCells("A$numberRow:B$numberRow")->getStyle("A$numberRow:B$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $regulations_active->name);
			$objPHPExcel->getActiveSheet()->mergeCells("C$numberRow:D$numberRow")->getStyle("C$numberRow:D$numberRow")->applyFromArray($style_excel['BStyle']);
			$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Phiên Bản');
			$objPHPExcel->getActiveSheet()->mergeCells("E$numberRow:F$numberRow")->getStyle("E$numberRow:F$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $regulations_active->version);
			$objPHPExcel->getActiveSheet()->mergeCells("G$numberRow:H$numberRow")->getStyle("G$numberRow:H$numberRow")->applyFromArray($style_excel['BStyle']);
			$numberRow++;
			
			$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'Trực Thuộc');
			$objPHPExcel->getActiveSheet()->mergeCells("A$numberRow:B$numberRow")->getStyle("A$numberRow:B$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $regulations_active->under);
			$objPHPExcel->getActiveSheet()->mergeCells("C$numberRow:D$numberRow")->getStyle("C$numberRow:D$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Ngày Cập nhật mới nhất');
			$objPHPExcel->getActiveSheet()->mergeCells("E$numberRow:F$numberRow")->getStyle("E$numberRow:F$numberRow")->applyFromArray($style_excel['BStyle']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", _dC($regulations_active->date_update));
			$objPHPExcel->getActiveSheet()->mergeCells("G$numberRow:H$numberRow")->getStyle("G$numberRow:H$numberRow")->applyFromArray($style_excel['BStyle']);
			$numberRow++;
			
			$numberRow = 6;
			$numberRowNext = 7;
			$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'STT');
			$objPHPExcel->getActiveSheet()->mergeCells("A$numberRow:A$numberRowNext")->getStyle("A$numberRow:A$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
//			$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Mã Phòng Ban');
//			$objPHPExcel->getActiveSheet()->mergeCells("B$numberRow:B$numberRowNext")->getStyle("B$numberRow:B$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
//
//			$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Tên Phòng Ban');
//			$objPHPExcel->getActiveSheet()->mergeCells("C$numberRow:C$numberRowNext")->getStyle("C$numberRow:C$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
//
//			$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Trực Thuộc');
//			$objPHPExcel->getActiveSheet()->mergeCells("D$numberRow:D$numberRowNext")->getStyle("D$numberRow:D$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
//
//			$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Ngày Ban Hành');
//			$objPHPExcel->getActiveSheet()->mergeCells("E$numberRow:E$numberRowNext")->getStyle("E$numberRow:E$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
//
//			$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Phiên Bản');
//			$objPHPExcel->getActiveSheet()->mergeCells("F$numberRow:F$numberRowNext")->getStyle("F$numberRow:F$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
//
//			$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Ngày Cập nhật mới nhất');
//			$objPHPExcel->getActiveSheet()->mergeCells("G$numberRow:G$numberRowNext")->getStyle("G$numberRow:G$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Chức Năng Phòng');
			$objPHPExcel->getActiveSheet()->mergeCells("B$numberRow:B$numberRowNext")->getStyle("B$numberRow:B$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Nhiệm Vụ Phòng');
			$objPHPExcel->getActiveSheet()->mergeCells("C$numberRow:C$numberRowNext")->getStyle("C$numberRow:C$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Quyền Hạn Phòng');
			$objPHPExcel->getActiveSheet()->mergeCells("D$numberRow:D$numberRowNext")->getStyle("D$numberRow:D$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Cơ Cấu Tổ Chức');
			$objPHPExcel->getActiveSheet()->mergeCells("E$numberRow:G$numberRow")->getStyle("E$numberRow:G$numberRow")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRowNext", 'Sơ Đồ Trực Thuộc')->getStyle("E$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRowNext", 'Mã Vị Trí')->getStyle("F$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRowNext", 'Chức Vụ')->getStyle("G$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'Công Việc Vị Trí');
			$objPHPExcel->getActiveSheet()->mergeCells("H$numberRow:H$numberRowNext")->getStyle("H$numberRow:H$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", 'Qui Trình');
			$objPHPExcel->getActiveSheet()->mergeCells("I$numberRow:I$numberRowNext")->getStyle("I$numberRow:I$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("J$numberRow", 'Mục Tiêu Phòng Ban Năm');
			$objPHPExcel->getActiveSheet()->mergeCells("J$numberRow:J$numberRowNext")->getStyle("J$numberRow:J$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("K$numberRow", 'Kết Quả Năm');
			$objPHPExcel->getActiveSheet()->mergeCells("K$numberRow:K$numberRowNext")->getStyle("K$numberRow:K$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("L$numberRow", 'Kết Quả KPIs Quý I');
			$objPHPExcel->getActiveSheet()->mergeCells("L$numberRow:L$numberRowNext")->getStyle("L$numberRow:L$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("M$numberRow", 'Kết Quả KPIs Quý II');
			$objPHPExcel->getActiveSheet()->mergeCells("M$numberRow:M$numberRowNext")->getStyle("M$numberRow:M$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("N$numberRow", 'Kết Quả KPIs Quý III');
			$objPHPExcel->getActiveSheet()->mergeCells("N$numberRow:N$numberRowNext")->getStyle("N$numberRow:N$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			
			$objPHPExcel->getActiveSheet()->SetCellValue("O$numberRow", 'Kết Quả KPIs Quý IV');
			$objPHPExcel->getActiveSheet()->mergeCells("O$numberRow:O$numberRowNext")->getStyle("O$numberRow:O$numberRowNext")->applyFromArray($style_excel['Background_header_one']);
			$numberRow = $numberRowNext + 1;
			
			if(!empty($regulations_active)) {
				$this->db->where('id_regulations_active', $regulations_active->id);
				$regulations_active_detail = $this->db->get('tbl_regulations_active_detail')->result_array();
				if (!empty($regulations_active_detail)) {
					foreach ($regulations_active_detail as $key => $value) {
						$i = 0;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($key + 1))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['room_function'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['room_tasks'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['room_powers']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['file_under']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['code_locus']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['name_position']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['job_position']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['procedure']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['goals_year']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['result_year']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['result_quarter_one']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['result_quarter_two']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['result_quarter_three']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['result_quarter_four']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
						$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
						$i++;
						$numberRow++;
					}
				}
			}
			
			
			
			$filename = 'quy_che_phong_ban.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}
