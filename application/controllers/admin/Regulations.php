<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Regulations extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		if (!is_admin()) {
			access_denied();
		}
	}

	public function rules()
	{
		$data['title'] = _l('Nội Quy');
		$data['name'] = _l('Nội Quy');
		$data['type'] = 1;
		$this->load->view('admin/regulations/manage', $data);
	}

	public function fixation()
	{
		$data['title'] = _l('Quy Định');
		$data['name'] = _l('Quy Định');
		$data['type'] = 2;
		$this->load->view('admin/regulations/manage', $data);
	}

	public function table($type = '')
	{
		if ($type == 1) {
			$aColumns = [
				'tbl_regulations.id as id',
				'tbl_category_regulations.code as code_category',
				'tbl_category_regulations.name as name_category',
				'tbl_regulations.code as code',
				'tbl_regulations.name as name',
				'tbl_regulations.type_regulations as type_regulations',
				'tbl_regulations.content as content',
				'tbl_regulations.directing as directing',
				'tbl_regulations.violate as violate',
				'tbl_regulations.forms_processing as forms_processing',
				'tbl_regulations.date_issued as date_issued',
				'tbl_regulations.time_of_use as time_of_use',
                '(
                    SELECT GROUP_CONCAT(CONCAT(tblinternal_proposal.id, ",", tblinternal_proposal.code) SEPARATOR "|||")
                    FROM tblinternal_proposal 
                    JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id 
                        AND tbl_category_recommended.type = "rules"
                        AND tbl_category_recommended.name_table = "tbl_regulations"
                    WHERE tblinternal_proposal.suggest_id = tbl_regulations.id
                ) as internal_proposal'
			];

			$sIndexColumn = 'id';
			$sTable       = 'tbl_regulations';
			$join = [
				'LEFT JOIN tbl_category_regulations ON tbl_category_regulations.id = tbl_regulations.id_category'
			];
			$where = [];
			$where[] = 'AND tbl_regulations.type = "' . (!empty($type) ? $type : '1') . '"';
			$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
			$output       = $result['output'];
			$rResult      = $result['rResult'];
			foreach ($rResult as $aRow) {
				$row = [];
				$row[] = $aRow['id'];
				$row[] = $aRow['code_category'];
				$row[] = $aRow['name_category'];
				$row[] = $aRow['code'];
				$row[] = $aRow['name'];
				$row[] = $aRow['type_regulations'];
				$row[] = '<div style="white-space: pre-wrap;max-width: 350px;">' . $aRow['content'] . '</div>';
				$row[] = '<div style="white-space: pre-wrap;max-width: 350px;">' . $aRow['directing'] . '</div>';
				$row[] = '<div style="white-space: pre-wrap;">' . $aRow['violate'] . '</div>';
				$row[] = '<div style="white-space: pre-wrap;">' . $aRow['forms_processing'] . '</div>';
				$row[] = _dC($aRow['date_issued']);
				$row[] = $aRow['time_of_use'];
                $viewInternal = [];
                $str = '';
                if(!empty($aRow['internal_proposal'])) {
                    $internal_proposal = explode("|||", $aRow['internal_proposal']);
                    foreach($internal_proposal as $k => $v) {
                        $detail_internal_proposal = explode(",", $v);
                        $viewInternal[] = '<a class="c_modal" href="'.admin_url('internal_proposal/view/'.$detail_internal_proposal[0]).'">'.$detail_internal_proposal[1].'</a>';
                    }
                    $str .= '<hr class="mtop5 mbot5"/><div class="text-center">' . implode("<br/>", $viewInternal).'</div>';
                }
                $buttonCreateInternal = '<a class="btn btn-icon btn-info c_modal" href="'.admin_url('internal_proposal/add_modal?type_object=regulations&id_object='.$aRow['id'].'&type_append=rules').'">Tạo Đề Xuất Nội Bộ</a>';
                $row[] = '<div class="text-center">'.$buttonCreateInternal . $str.'</div>';
                $options = '';
                $options .= '<a class="btn btn-icon btn-danger deleteItems" data-href="' . (admin_url('regulations/delete/' . $aRow['id'])) . '"><i class="fa fa-remove"></i></a>';
				$row[] = '<div class="text-center">'.$options.'</div>';
				$output['aaData'][] = $row;
			}
		} elseif ($type == 2) {
			$aColumns = [
				'tbl_regulations.id as id',
				'tbl_category_regulations.code as code_category',
				'tbl_category_regulations.name as name_category',
				'tbl_regulations_code.code as code',
				'tbl_regulations_code.name as name',
				'tbl_regulations_code.name as type_regulations',
				'tbl_regulations.content as content',
				'tbl_regulations.directing as directing',
				'tbl_regulations.violate as violate',
				'tbl_regulations.forms_processing as forms_processing',
				'tbl_regulations.date_issued as date_issued',
                'tbl_regulations.time_of_use as time_of_use',
                '(
                    SELECT GROUP_CONCAT(CONCAT(tblinternal_proposal.id, ",", tblinternal_proposal.code) SEPARATOR "|||")
                    FROM tblinternal_proposal 
                    JOIN tbl_category_recommended ON tbl_category_recommended.id = tblinternal_proposal.category_recommended_id 
                        AND tbl_category_recommended.type = "fixation"
                        AND tbl_category_recommended.name_table = "tbl_regulations"
                    WHERE tblinternal_proposal.suggest_id = tbl_regulations.id
                ) as internal_proposal'
			];

			$sIndexColumn = 'id';
			$sTable       = 'tbl_regulations';
			$join = [
				'LEFT JOIN tbl_category_regulations ON tbl_category_regulations.id = tbl_regulations.id_category',
				'LEFT JOIN tbl_regulations_code ON tbl_regulations_code.id = tbl_regulations.regulations_code'
			];
			$where = [];
			$where[] = 'AND tbl_regulations.type = "' . (!empty($type) ? $type : '1') . '"';
			$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
			$output       = $result['output'];
			$rResult      = $result['rResult'];
			foreach ($rResult as $aRow) {
				$row = [];
				$row[] = $aRow['id'];
				$row[] = $aRow['code_category'];
				$row[] = $aRow['name_category'];
				$row[] = $aRow['code'];
				$row[] = $aRow['name'];
				$row[] = $aRow['type_regulations'];
				$row[] = '<div style="white-space: pre-wrap;max-width: 350px;">' . $aRow['content'] . '</div>';
				$row[] = '<div style="white-space: pre-wrap;max-width: 350px;">' . $aRow['directing'] . '</div>';
				$row[] = '<div style="white-space: pre-wrap;">' . $aRow['violate'] . '</div>';
				$row[] = '<div style="white-space: pre-wrap;">' . $aRow['forms_processing'] . '</div>';
				$row[] = _dC($aRow['date_issued']);
				$row[] = $aRow['time_of_use'];
                $str = '';
                $viewInternal = [];
                if(!empty($aRow['internal_proposal'])) {
                    $internal_proposal = explode("|||", $aRow['internal_proposal']);
                    foreach($internal_proposal as $k => $v) {
                        $detail_internal_proposal = explode(",", $v);
                        $viewInternal[] = '<a class="c_modal" href="'.admin_url('internal_proposal/view/'.$detail_internal_proposal[0]).'">'.$detail_internal_proposal[1].'</a>';
                    }
                    $str .= '<hr class="mtop5 mbot5"/><div class="text-center">' . implode("<br/>", $viewInternal).'</div>';
                }
                $buttonCreateInternal = '<a class="btn btn-icon btn-info c_modal" href="'.admin_url('internal_proposal/add_modal?type_object=regulations&id_object='.$aRow['id'].'&type_append=fixation').'">Tạo Đề Xuất Nội Bộ</a>';

                $row[] = '<div class="text-center">'.$buttonCreateInternal . $str.'</div>';

                $row[] = '<div class="text-center"><a class="btn btn-icon btn-danger deleteItems" data-href="' . (admin_url('regulations/delete/' . $aRow['id'])) . '"><i class="fa fa-remove"></i></a></div>';
				$output['aaData'][] = $row;
			}
		}
		echo json_encode($output);
		die();
	}

	public function delete($id = '')
	{
		$this->db->where('id', $id);
		$successDelete = $this->db->delete('tbl_regulations');
		if (!empty($successDelete)) {
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Xóa dữ liệu thành công'
			]);
			die();
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa dữ liệu không thành công'
		]);
		die();
	}

	public function modal_excel_import($type = '1')
	{

		if ($type == 1) {
			$data['name'] = 'Nội Quy';
			$data['fileTemplate'] = base_url('uploads/template/mau_import_noi_quy.xlsx?vs=0.2');
		} else {
			$data['name'] = 'Quy Định';
			$data['fileTemplate'] = base_url('uploads/template/mau_import_quy_dinh.xlsx?vs=0.2');
		}
		$data['title'] = _l('Import ' . $data['name'] . ' bằng File Excel');
		$data['type'] = $type;
		$this->load->view('admin/regulations/excel_import', $data);
	}

	public function excel_import($type = '1')
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		$count = 0;
		$errors = '';
		$data = [];
		if ($type == 1) {
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
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('J');
				$arraydata = array();
				$fields = $this->input->post('fields');
				for ($row = 4; $row <= $highestRow; ++$row) {
					for ($col = 0; $col < $highestColumnIndex; ++$col) {
						$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
						if ((isset($value)) && $value != "") {
							if ($col == 9) {
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
				foreach ($arraydata as $key => $row) {
					$list_data[] = [
						'code_category' => !empty($row[1]) ? $row[1] : NULL,
						'name_category' => !empty($row[2]) ? $row[2] : NULL,
						'code' => !empty($row[3]) ? $row[3] : NULL,
						'type_regulations' => !empty($row[4]) ? $row[4] : NULL,
						'content' => !empty($row[5]) ? $row[5] : NULL,
						'directing' => !empty($row[6]) ? $row[6] : NULL,
						'violate' => !empty($row[7]) ? $row[7] : NULL,
						'forms_processing' => !empty($row[8]) ? $row[8] : NULL,
						'date_issued' => !empty($row[9]) ? $row[9] : NULL,
						'time_of_use' => !empty($row[10]) ? $row[10] : NULL,
					];
				}

				$dataUpdate = [];
				$dataInsert = [];
				foreach ($list_data as $key => $value) {
					$this->db->where('code', $value['code_category']);
					$this->db->where('type', $type);
					$category_regulations = $this->db->get('tbl_category_regulations')->row();
					if (!empty($category_regulations)) {
						$value['id_category'] =  $category_regulations->id;
					} else {
						$succCategory = $this->db->insert('tbl_category_regulations', [
							'code' => $value['code_category'],
							'name' => $value['name_category'],
							'type' => $type,
							'create_by' => get_staff_user_id(),
						]);
						if (!empty($succCategory)) {
							$value['id_category'] =  $this->db->insert_id();
						}
					}
					$edit = false;
					if (!empty($value['code'])) {
						$this->db->where('code', $value['code']);
						$this->db->where('type', $type);
						$kt_regulations = $this->db->get('tbl_regulations')->row();
						if (!empty($kt_regulations)) {
							$dataUpdate[] = [
								'id' => $kt_regulations->id,
								'id_category' => $value['id_category'],
								'type' => $type,
								'type_regulations' => $value['type_regulations'],
								'content' => $value['content'],
								'directing' => $value['directing'],
								'violate' => $value['violate'],
								'forms_processing' => $value['forms_processing'],
								'date_issued' => $value['date_issued'],
								'time_of_use' => $value['time_of_use'],
							];
							$edit = true;
						}
					}

					if (empty($edit)) {
						$dataInsert[] = [
							'id_category' => $value['id_category'],
							'type' => $type,
							'code' => !empty($value['code']) ? $value['code'] : NULL,
							'type_regulations' => $value['type_regulations'],
							'content' => $value['content'],
							'directing' => $value['directing'],
							'violate' => $value['violate'],
							'forms_processing' => $value['forms_processing'],
							'date_issued' => $value['date_issued'],
							'time_of_use' => $value['time_of_use'],
							'create_by' => get_staff_user_id(),
						];
					}
				}

				$viewSuccess = [];
				if (!empty($dataUpdate)) {
					$this->db->update_batch('tbl_regulations', $dataUpdate, 'id');
					if ($this->db->affected_rows() > 0) {
						$viewSuccess[] = " Cập nhật " . $this->db->affected_rows() . " thành công ";
					}
				}

				if (!empty($dataInsert)) {
					$this->db->insert_batch('tbl_regulations', $dataInsert);
					if ($this->db->affected_rows() > 0) {
						$affected_rows = $this->db->affected_rows();

						$this->db->where('code IS NULL', false, false);
						$this->db->where('type', $type);
						$regulationsInsert = $this->db->get('tbl_regulations')->result_array();
						if (!empty($regulationsInsert)) {
							$updateCode = [];
							foreach ($regulationsInsert as $key => $value) {
								$updateCode[] = [
									'id' => $value['id'],
									'code' => ($type == 1 ? 'NQ-' : 'QĐ-') . $value['id']
								];
							}
							if (!empty($updateCode)) {
								$this->db->update_batch('tbl_regulations', $updateCode, 'id');
							}
						}
						$viewSuccess[] = " Thêm mới " . $affected_rows . " dữ liệu ";
					}
				}

				if (empty($viewSuccess)) {
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
		} elseif ($type == 2) {
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
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('J');
				$arraydata = array();
				$fields = $this->input->post('fields');
				for ($row = 4; $row <= $highestRow; ++$row) {
					for ($col = 0; $col < $highestColumnIndex; ++$col) {
						$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
						if ((isset($value)) && $value != "") {
							if ($col == 8) {
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
				foreach ($arraydata as $key => $row) {
					$list_data[] = [
						'code_category' => !empty($row[1]) ? $row[1] : NULL,
						'name_category' => !empty($row[2]) ? $row[2] : NULL,
						'code' => !empty($row[3]) ? $row[3] : NULL,
						'content' => !empty($row[4]) ? $row[4] : NULL,
						'directing' => !empty($row[5]) ? $row[5] : NULL,
						'violate' => !empty($row[6]) ? $row[6] : NULL,
						'forms_processing' => !empty($row[7]) ? $row[7] : NULL,
						'date_issued' => !empty($row[8]) ? $row[8] : NULL,
						'time_of_use' => !empty($row[9]) ? $row[9] : NULL,
					];
				}
				$dataUpdate = [];
				$dataInsert = [];
				foreach ($list_data as $key => $value) {
					$this->db->where('code', $value['code_category']);
					$this->db->where('type', $type);
					$category_regulations = $this->db->get('tbl_category_regulations')->row();
					if (!empty($category_regulations)) {
						$value['id_category'] =  $category_regulations->id;
					} else {
						$succCategory = $this->db->insert('tbl_category_regulations', [
							'code' => $value['code_category'],
							'name' => $value['name_category'],
							'type' => $type,
							'create_by' => get_staff_user_id(),
						]);
						if (!empty($succCategory)) {
							$value['id_category'] =  $this->db->insert_id();
						}
					}
					$this->db->where('code', $value['code']);
					$regulations_code = $this->db->get('tbl_regulations_code')->row();
					if (!empty($regulations_code)) {
						$value['regulations_code'] =  $regulations_code->id;
					} else {
						$succregulations_code = $this->db->insert('tbl_regulations_code', [
							'code' => $value['code'],
							'name' => $value['code'],
							'staff_create' => get_staff_user_id(),
							'date_create' => date('Y-m-d H:i:s'),

						]);
						if (!empty($succregulations_code)) {
							$value['regulations_code'] =  $this->db->insert_id();
						}
					}
					$edit = false;
					if (!empty($value['code'])) {
						$this->db->where('regulations_code', $value['regulations_code']);
						$this->db->where('id_category', $value['id_category']);
						$this->db->where('type', $type);
						$kt_regulations = $this->db->get('tbl_regulations')->row();
						if (!empty($kt_regulations)) {
							$dataUpdate[] = [
								'id' => $kt_regulations->id,
								'id_category' => $value['id_category'],
								'regulations_code' => $value['regulations_code'],
								'type' => $type,
								'type_regulations' => $value['type_regulations'],
								'content' => $value['content'],
								'directing' => $value['directing'],
								'violate' => $value['violate'],
								'forms_processing' => $value['forms_processing'],
								'date_issued' => $value['date_issued'],
								'time_of_use' => $value['time_of_use'],
							];
							$edit = true;
						}
					}

					if (empty($edit)) {
						$dataInsert[] = [
							'id_category' => $value['id_category'],
							'type' => $type,
							'regulations_code' => $value['regulations_code'],
							'code' => NULL,
							'content' => $value['content'],
							'directing' => $value['directing'],
							'violate' => $value['violate'],
							'forms_processing' => $value['forms_processing'],
							'date_issued' => $value['date_issued'],
							'time_of_use' => $value['time_of_use'],
							'create_by' => get_staff_user_id(),
						];
					}
				}

				$viewSuccess = [];
				if (!empty($dataUpdate)) {
					$this->db->update_batch('tbl_regulations', $dataUpdate, 'id');
					if ($this->db->affected_rows() > 0) {
						$viewSuccess[] = " Cập nhật " . $this->db->affected_rows() . " thành công ";
					}
				}

				if (!empty($dataInsert)) {
					$this->db->insert_batch('tbl_regulations', $dataInsert);
					if ($this->db->affected_rows() > 0) {
						$affected_rows = $this->db->affected_rows();

						$this->db->where('code IS NULL', false, false);
						$this->db->where('type', $type);
						$regulationsInsert = $this->db->get('tbl_regulations')->result_array();
						if (!empty($regulationsInsert)) {
							$updateCode = [];
							foreach ($regulationsInsert as $key => $value) {
								$updateCode[] = [
									'id' => $value['id'],
									'code' => ($type == 1 ? 'NQ-' : 'QĐ-') . $value['id']
								];
							}
							if (!empty($updateCode)) {
								$this->db->update_batch('tbl_regulations', $updateCode, 'id');
							}
						}
						$viewSuccess[] = " Thêm mới " . $affected_rows . " dữ liệu ";
					}
				}

				if (empty($viewSuccess)) {
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
		}
		echo json_encode([
			'success' => true,
			'errors' => $errors,
			'alert_type' => 'success',
			'message' => 'Import thành công ' . $count . ' dòng',
		]);
		die();
	}

	public function export_excel($type = '')
	{
		if ($type == 1) {
			$name = 'Nội Quy';
			$nameFile = 'noi_quy';
		} else {
			$name = 'Quy Định';
			$nameFile = 'quy_dinh';
		}
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
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(30);

		$objPHPExcel->getActiveSheet()->SetCellValue("A1", (mb_strtoupper($name, "UTF8") . ' CHUNG-HÌNH THỨC XỬ LÝ VI PHẠM'))->getStyle("A1")->applyFromArray($style_excel['BStyle_center']);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

		$numberRow = 3;
		$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Mã Danh Mục')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Tên Danh Mục')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Mã ' . $name)->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Loại ' . $name)->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Nội Dung')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Hướng Dẫn-Tiêu Chí')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'Lần Vi Phạm')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", 'Hình Thức Xử Lý')->getStyle("I$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("J$numberRow", 'Ngày Ban Hành')->getStyle("J$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$objPHPExcel->getActiveSheet()->SetCellValue("K$numberRow", 'Thời Gian Sử Dụng')->getStyle("K$numberRow")->applyFromArray($style_excel['Background_header_one']);
		$numberRow++;

		if ($type == 1) {
			$this->db->select([
				'tbl_regulations.*',
				'tbl_category_regulations.code as code_category',
				'tbl_category_regulations.name as name_category',
			]);
			$this->db->where('tbl_regulations.type', $type);
			$this->db->join('tbl_category_regulations', 'tbl_category_regulations.id = tbl_regulations.id_category', 'left');
			$data_result = $this->db->get('tbl_regulations')->result_array();
			if (!empty($data_result)) {
				foreach ($data_result as $key => $value) {
					$i = 0;
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($key + 1))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_category'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_category'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['type_regulations'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['content'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['directing'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['violate'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['forms_processing'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", _dC($value['date_issued']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;
					$numberRow++;

                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['time_of_use']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;
					$numberRow++;
				}
			}
		}elseif ($type == 2) {
			$this->db->select([
				'tbl_regulations.*',
				'tbl_category_regulations.code as code_category',
				'tbl_category_regulations.name as name_category',
				'tbl_regulations_code.code as regulations_code',
				'tbl_regulations_code.name as type_regulations',
			]);
			$this->db->where('tbl_regulations.type', $type);
			$this->db->join('tbl_category_regulations', 'tbl_category_regulations.id = tbl_regulations.id_category', 'left');
			$this->db->join('tbl_regulations_code', 'tbl_regulations_code.id = tbl_regulations.regulations_code', 'left');
			$data_result = $this->db->get('tbl_regulations')->result_array();
			if (!empty($data_result)) {
				foreach ($data_result as $key => $value) {
					$i = 0;
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($key + 1))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['code_category'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['name_category'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['regulations_code'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['type_regulations'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['content'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['directing'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['violate'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", $value['forms_processing'])->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", _dC($value['date_issued']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;
					$numberRow++;

                    $objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", ($value['time_of_use']))->getStyle("$cloumns_excel[$i]$numberRow")->getAlignment()->setWrapText(true)->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$i++;
					$numberRow++;
				}
			}
		}


		$filename = $nameFile . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}
