<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Costs extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('costs_model');
        $this->type_cost = [
            [
                'id' => 1,
                'name' => 'Nhóm nguyên liệu'
            ],
            [
                'id' => 2,
                'name' => 'Thiết bị'
            ],
            [
                'id' => 3,
                'name' => 'Nhóm yêu cầu chi'
            ]
        ];
	}

	public function index()
	{

		//        if (!has_permission('costs', '', 'view')) {
		//                access_denied('Debt suppliers');
		//        }
		$type_cost = $this->input->get('type');
		$data['type_cost'] = $type_cost;
		$title = '';
		$data['title'] = _l('ch_costs');
		//		$data['costs'] = [];
		//		$this->costs_model->get_by_id(0, $data['costs']);
		$data['costs'] = $this->db->get_where('tblcosts', ['costs_parent' => 0])->result_array();

		$full_costs = $this->costs_model->get_full_costs();
		$data['full_costs'] = $full_costs;
        $data['dtType'] = get_table_where('tbl_type_cost');
        $data['dtTypeCost'] = $this->type_cost;
        $dtDepartment = get_table_where('tbldepartments', ['room_id !=' => 0]);
        $data['dtDepartment'] = $dtDepartment;
		$this->load->view('admin/costs/manage', $data);
	}

	public function table()
	{
		//        if (!has_permission('costs', '', 'view')) {
		//                ajax_access_denied();
		//        }
		$this->app->get_table_data('costs');
	}

	public function get_parent($id = '')
	{
		$lever = get_table_where('tblcosts', array('id' => $id), '', 'row');
		if ($lever->lever > 1) {
			$data['data'] = get_table_where('tblcosts', array('lever' => ($lever->lever - 1)));
		} else {
			$data['data'] = get_table_where('tblcosts', array('lever' => 1));
		}
		$data['costs_parent'] = $lever->costs_parent;
		echo json_encode($data);
	}

	public function get_costs_parents()
	{
		$data['costs'] = [];
		$this->costs_model->get_costs_parent(0, $data['costs']);
		echo json_encode($data['costs']);
	}

	public function add()
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
            if (empty($data['type'])){
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Vui lòng chọn loại!'
                ));
                die;
            }
            $department_id = $data['department_id'];
			unset($data['id']);
			unset($data['department_id']);
			if ($data['costs_parent'] == NULL || $data['costs_parent'] == '') {
				$data['lever'] = 1;
			} else {
				$lever = 1;
				$parent = $data['costs_parent'];
				while ($parent > 0) {
					$ktr = get_table_where('tblcosts', array('id' => $parent), '', 'row');
					$parent = $ktr->costs_parent;
					$lever++;
				}
				$data['lever'] = $lever;
			}
			$this->db->insert('tblcosts', $data);
			$id = $this->db->insert_id();
			if ($id) {
                if (!empty($department_id)) {
                    foreach ($department_id as $key => $value) {
                        $this->db->insert('tblcost_department', [
                            'cost_id' => $id,
                            'department_id' => $value
                        ]);
                    }
                }
				$success = true;
				$message = _l('ch_added_successfuly');
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function update()
	{
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$id = $data['id'];
            $department_id = $data['department_id'];
			unset($data['id']);
			unset($data['department_id']);
			$this->db->where('id', $id);
			$idd = $this->db->update('tblcosts', $data);
			if ($id) {
                $this->db->where('tblcost_department.cost_id',$id);
                $this->db->delete('tblcost_department');
                if (!empty($department_id)){
                    foreach ($department_id as $key => $value){
                        $this->db->insert('tblcost_department', [
                            'cost_id' => $id,
                            'department_id' => $value
                        ]);
                    }
                }
				$success = true;
				$message = _l('ch_updated_successfuly');
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	public function price_items($nam = '', $thang = '')
	{
		$data = $this->input->post();
		$ktr = get_table_where('tblfinancial_control_detail', array('id_financial_control' => $data['id'], 'nam' => $nam), '', 'row');
		if (!$ktr) {
			$_data = array(
				'id_financial_control' => $data['id'],
				'nam' => $nam,
				$thang => number_unformat($data['data_input']),
			);
			$this->db->insert('tblfinancial_control_detail', $_data);
		} else {
			$_data = array(
				$thang => number_unformat($data['data_input']),
			);
			$this->db->update('tblfinancial_control_detail', $_data, array('id' => $ktr->id));
		}
		$message = _l('Thêm thành công');
		echo json_encode(array(
			'success' => true,
			'message' => $message
		));
	}

	public function detail()
	{
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data('financial_control');
		}
		$data['financial'] = [];
		$this->costs_model->get_by_ids(0, $data['financial']);
		$this->load->view('admin/costs/detail', $data);
	}

	public function import()
	{
		$total_imported = 0;
		$load_result = false;
		$alert = [
			'success' => 0,
			'fail' => [],
		];
		if ($this->input->post()) {
			if (isset($_FILES['file_import']['name']) && $_FILES['file_import']['name'] != '') {
				$tmpFilePath = $_FILES['file_import']['tmp_name'];
				$ext = strtolower(pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION));
				$type = $_FILES["file_import"]["type"];
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$newFilePath = TEMP_FOLDER . $_FILES['file_import']['name'];
					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 777);
					}
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$fd = fopen($newFilePath, 'r');
						$rows = array();
						if ($ext == 'csv') {
							while ($row = fgetcsv($fd)) {
								$rows[] = $row;
							}
						} else if ($ext == 'xlsx' || $ext == 'xls') {
							if ($type == "application/octet-stream" || $type == "application/vnd.ms-excel" || $type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
								require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
								$inputFileType = PHPExcel_IOFactory::identify($newFilePath);
								$objReader = PHPExcel_IOFactory::createReader($inputFileType);
								$objReader->setReadDataOnly(true);
								/**  Load $inputFileName to a PHPExcel Object  **/
								$objPHPExcel = $objReader->load($newFilePath);
								$allSheetName = $objPHPExcel->getSheetNames();
								$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
								$highestRow = $objWorksheet->getHighestRow();
								$highestColumn = $objWorksheet->getHighestColumn();
								$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
								for ($row = 4; $row <= $highestRow; ++$row) {
									for ($col = 0; $col < $highestColumnIndex; ++$col) {
										$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
										$rows[$row - 2][$col] = $value;
									}
								}
							}
						} else {
							fclose($fd);
							unlink($newFilePath);
							redirect('/');
						}
						fclose($fd);
						$data['total_rows_post'] = count($rows);
						unlink($newFilePath);
						$query_array = [];
						$backup_rows = $rows;
						$result_array = [];
						$fetch_columns_step = true;
						$fetch_product_step = false;
						$columns_found = 0;
						$product_count = 0;
						$data = [];
						$data_ok = true;
						$reason = "";
						$dem_temp = 4;
						$alert['success'] = 0;
						$alert['fail'] = 0;
						foreach ($rows as $row) {
							$data_ok = true;
							if (!is_numeric($row[0])) {
								$reason .= "Không phải ID tại dòng " . $dem_temp . "<br />";
								$data_ok = false;
								$dem_temp++;
								continue;
							} elseif ($row[6] != 0 && !is_numeric($row[6]) && ($row[6] != 'Không được thêm')) {
								$reason .= "Tiền kế hoạch phải là số tại dòng " . $dem_temp . "<br />";
								$data_ok = false;
								$dem_temp++;
								continue;
							} elseif ($row[6] == 'Không được thêm') {
								$data_ok = false;
								$dem_temp++;
								continue;
							} else {
								$ktr = get_table_where('tblcosts', array('id' => $row[0]), '', 'row');
								if (!$ktr) {
									$reason .= "Số ID đã bị sửa tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								$ktrdetail = get_table_where('tblfinancial_control_detail', array('id_financial_control' => $row[0], 'nam' => $row[5]), '', 'row');
								if (!$ktrdetail) {
									$_data['id_financial_control'] = $ktr->id;
									$_data['nam'] = $row[5];
									$_data[cover_month($row[4])] = $row[6];
									if ($data_ok) {
										$this->db->insert('tblfinancial_control_detail', $_data);
										$alert['success']++;
									}
								} else {
									$_data[cover_month($row[4])] = $row[6];
									if ($data_ok) {
										$this->db->update('tblfinancial_control_detail', $_data, array('id' => $ktrdetail->id));
										$alert['success']++;
									}
								}
								$dem_temp++;
							}
						}
						$data['message'] = "Nhập thành công " . $alert['success'] . " dòng. <br />";
						$data['message'] .= $reason;
					}
				} else {
					set_alert('warning', _l('import_upload_failed'));
					redirect(admin_url('costs/import/'));
				}
			}
		}
		$data['title'] = 'Import';
		$this->load->view('admin/costs/import', $data);
	}

	public function excel($month, $year)
	{
		// $client=$this->input->get('client');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		// $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$colum_array = array('I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$BStyle = array(
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
			)
		);
		$BStyle1 = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				'color' => array('rgb' => '111112'),
				'size' => 11,
				'name' => 'Times New Roman'
			)
		);
		for ($row = 1; $row <= 100; $row++) {
			$styleArray = [
				'font' => [
					'size' => 12
				]
			];
			$objPHPExcel->getActiveSheet()
				->getStyle("A1:N2")
				->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'IMPORT KẾ HOẠCH (Những dòng không được sửa để lấy dữ liệu import)');
			$objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
			$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Năm')->getStyle('A2')->applyFromArray($BStyle);
			$objPHPExcel->getActiveSheet()->setCellValue('B2', $year)->getStyle('B2')->applyFromArray($BStyle);
			$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Tháng')->getStyle('C2')->applyFromArray($BStyle);
			$objPHPExcel->getActiveSheet()->setCellValue('D2', $month)->getStyle('D2')->applyFromArray($BStyle);
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A3', 'ID ( không được sửa)')->getStyle('A3')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Mã cha')->getStyle('B3')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Mã')->getStyle('C3')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Tên')->getStyle('D3')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Tháng ( không được sửa)')->getStyle('E3')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Năm ( không được sửa)')->getStyle('F3')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Tiền kế hoạch (Phần được sửa)')->getStyle('G3')->applyFromArray($BStyle);
		$ktr = get_table_where('tblcosts', array('costs_parent' => 0));
		$data = array();
		foreach ($ktr as $key => $value) {
			$data = $this->get_financial_control($value['id'], $data);
		}
		foreach ($data as $rom => $item) {
			$financial = get_table_where('tblcosts', array('id' => $item->costs_parent), '', 'row');
			$objPHPExcel->getActiveSheet()->setCellValue('A' . ($rom + 4), $item->id)->getStyle('A' . ($rom + 4))->applyFromArray($BStyle1);
			if (!empty($financial)) {
				$objPHPExcel->getActiveSheet()->setCellValue('B' . ($rom + 4), $financial->code)->getStyle('B' . ($rom + 4))->applyFromArray($BStyle1);
			} else {
				$objPHPExcel->getActiveSheet()->setCellValue('B' . ($rom + 4), '')->getStyle('B' . ($rom + 4))->applyFromArray($BStyle1);
			}
			$objPHPExcel->getActiveSheet()->setCellValue('C' . ($rom + 4), $item->code)->getStyle('C' . ($rom + 4))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($rom + 4), $item->name)->getStyle('D' . ($rom + 4))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($rom + 4), $month)->getStyle('E' . ($rom + 4))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . ($rom + 4), $year)->getStyle('F' . ($rom + 4))->applyFromArray($BStyle1);
			if ($item->cha == 1) {
				$objPHPExcel->getActiveSheet()->setCellValue('G' . ($rom + 4), 'Không được thêm')->getStyle('G' . ($rom + 4))->applyFromArray($BStyle1);
			} else {
				$objPHPExcel->getActiveSheet()->setCellValue('G' . ($rom + 4), 0)->getStyle('G' . ($rom + 4))->applyFromArray($BStyle1);
			}
		}
		// $objPHPExcel->getActiveSheet()->freezePane('A4');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="kethoachdongtienthang' . $month . '_' . $year . '.xls"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		exit();
	}

	public function get_financial_control($id = '', $data = array())
	{
		$financial = get_table_where('tblcosts', array('id' => $id), '', 'row');
		$ktr = get_table_where('tblcosts', array('costs_parent' => $id));
		if ($ktr) {
			$financial->cha = 1;
			$data[] = $financial;
			foreach ($ktr as $key => $value) {
				$data = $this->get_financial_control($value['id'], $data);
			}
		} else {
			$financial->cha = 0;
			$data[] = $financial;
		}
		return $data;
	}

	public function get_exsit($id = '')
	{
		$items = get_table_where('tblpay_slip', array('id_costs' => $id), '', 'row');
		$itemss = get_table_where('tblother_payslips', array('id_costs' => $id), '', 'row');
		if (!empty($items) || !empty($itemss)) {
			echo json_encode(true);
			die;
		} else {
			$parent = get_table_where('tblcosts', array('costs_parent' => $id), '', 'row');
			if (!empty($parent)) {
				echo json_encode(true);
				die;
			}
			$success = $this->db->delete('tblcosts', array('id' => $id));
			if ($success) {
                $this->db->where('tblcost_department.cost_id',$id);
                $this->db->delete('tblcost_department');
				$success = true;
				$message = _l('ch_delete_successfuly');
			}
			echo json_encode(array(
				'success' => $success,
				'message' => $message
			));
			die;
		}
	}

	// yct start
	public function modal_excel_import()
	{
		$data['title'] = _l('t_import_costs');
		$this->load->view('admin/costs/excel_import', $data);
	}

	public function excel_import()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		$count = 0;
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

			$allSheetName       = $objPHPExcel->getSheetNames();
			$objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
			$highestRow         = $objWorksheet->getHighestRow();
			$highestColumn      = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('L');
			$arraydata          = array();

			$fields = $this->input->post('fields');
			for ($row = 2; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 2][$col] = $value;
				}
			}
            $arrId = [];
			foreach ($arraydata as $key => $value) {
				$code_type = $value[0];
				$name_type = $value[1];
				$costs_parent_code = $value[2];
				$costs_parent_name = $value[3];
                $code = $value[4];
                $name = $value[5];
                $object = $value[6];
                $code_object = $value[7];
                $category_machines = $value[8];
                $type_cost = $value[9];
                $code_object = trim($code_object);
                $department_code = $value[10];


				if (empty($code) || empty($name) || empty($code_type) || empty($name_type) || empty($type_cost)) {
					continue;
				}
                $this->db->from('tbldepartments');
                $this->db->where_in('tbldepartments.code',explode(',',$department_code));
                $this->db->where('tbldepartments.room_id !=',0);
                $dtDerpartment = $this->db->get()->result_array();
                $arrDepartmentId = [];
                if (!empty($dtDerpartment)){
                    foreach ($dtDerpartment as $k => $v){
                        $arrDepartmentId[] = $v['departmentid'];
                    }
                }
                $object_id = 0;
                if ($type_cost == 1){
                    $this->db->from('tbl_category_items');
                    $this->db->where('tbl_category_items.code',$code_object);
                    $dtObject = $this->db->get()->row_array();
                    if(!empty($dtObject)){
                        $object_id = $dtObject['id'];
                    } else {
                        $this->db->insert('tbl_category_items',[
                            'code' => $code_object,
                            'name' => $object,
                        ]);
                        $object_id = $this->db->insert_id();
                    }
                } elseif ($type_cost == 2){
                    $this->db->from('tbl_machines');
                    $this->db->where('tbl_machines.code',$code_object);
                    $dtObject = $this->db->get()->row_array();
                    if(!empty($dtObject)){
                        $object_id = $dtObject['id'];
                    } else {
                        $this->db->insert('tbl_machines',[
                            'code' => $code_object,
                            'name' => $object,
                            'category_machine_id' => $category_machines,
                            'status' => 'producing',
                        ]);
                        $object_id = $this->db->insert_id();
                    }
                } elseif ($type_cost == 3){
                    $this->db->from('tbl_category_payslip');
                    $this->db->where('tbl_category_payslip.code',$code_object);
                    $dtObject = $this->db->get()->row_array();
                    if (!empty($dtObject)){
                        $object_id = $dtObject['id'];
                    } else {
                        $this->db->insert('tbl_category_payslip',[
                            'code' => $code_object,
                            'name' => $object,
                            'created_by' => get_staff_user_id(),
                            'date_created' => date('Y-m-d H:i:s')
                        ]);
                        $object_id = $this->db->insert_id();
                    }
                }

                $dtType = get_table_where('tbl_type_cost',['code' => $code_type],'','row_array');
                $type = 0;
                if (!empty($dtType)){
                    $type = $dtType['id'];
                } else {
                    $this->db->insert('tbl_type_cost', [
                        'code' => $code_type,
                        'name' => $name_type,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ]);
                    $type = $this->db->insert_id();
                }

				$costs_parent_id = '';
				if (!empty($costs_parent_code)) {
					$costs_parent = get_table_where('tblcosts', array('code' => $costs_parent_code), '', 'row');
					if (!empty($costs_parent)) {
                        if ($costs_parent_name != $costs_parent->name){
                            $this->db->where('id',$costs_parent->id);
                            $this->db->update('tblcosts',[
                                'name' => $costs_parent_name
                            ]);
                        }
						$costs_parent_id = $costs_parent->id;
					}
				}
				if (empty($costs_parent_id)) {
					if (!empty($costs_parent_name)) {
						$data_parent = [
							'code' => $costs_parent_code,
							'name' => $costs_parent_name,
							'costs_parent' => '',
                            'active' => 1,
                            'department_id' => 0,
						];
						$costs_parent_id = $this->costs_model->insertCosts($data_parent);

						if (empty($costs_parent_id)) {
							continue;
						} else {
							$count++;
						}
					} else {
						continue;
					}
				}

                $dtCost = get_table_where('tblcosts',['code' => $code],'','row_array');
                if (!empty($dtCost)) {
                    $options = [
                        'code' => $code,
                        'name' => $name,
                        'costs_parent' => $costs_parent_id,
                        'detail' => null,
                        'type' => $type,
                        'type_cost' => $type_cost,
                        'object_id' => $object_id,
                        'department_id' => 0,
                    ];
                    $this->db->where('id',$dtCost['id']);
                    $rs = $this->db->update('tblcosts',$options);
                    $arrId[] = $dtCost['id'];
                    $cost_id = $dtCost['id'];
                } else {
                    $options = [
                        'code' => $code,
                        'name' => $name,
                        'costs_parent' => $costs_parent_id,
                        'active' => 1,
                        'detail' => null,
                        'type' => $type,
                        'type_cost' => $type_cost,
                        'object_id' => $object_id,
                        'department_id' => 0,
                    ];
                    $rs = $this->costs_model->insertCosts($options);
                    $cost_id = $rs;
                    $arrId[] = $rs;
                }
				if ($rs) {
					$count++;
				}
                $this->db->where('cost_id',$cost_id);
                $this->db->delete('tblcost_department');
                if (!empty($arrDepartmentId)){
                    foreach ($arrDepartmentId as $k => $v){
                        $this->db->insert('tblcost_department',[
                            'cost_id' => $cost_id,
                            'department_id' => $v
                        ]);
                    }
                }
			}
//            $this->db->where('id NOT IN ('.implode(',',$arrId).')');
//            $this->db->delete('tblcosts');
		}
		echo json_encode(
			[
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Import thành công ' . $count . ' dòng',
			]
		);
		die();
	}


	public function excel_export()
	{
		// $client=$this->input->get('client');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		// $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(45);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$colum_array = array('I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$BStyle = array(
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
			)
		);
		$BStyleHeader = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => '111112'),
				'size' => 14,
				'name' => 'Times New Roman'
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);
		$BStyle1 = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				'color' => array('rgb' => '111112'),
				'size' => 11,
				'name' => 'Times New Roman'
			)
		);
        $BStyleCenter = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font' => array(
                'color' => array('rgb' => '111112'),
                'size' => 11,
                'name' => 'Times New Roman'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
		for ($row = 1; $row <= 100; $row++) {
			$styleArray = [
				'font' => [
					'size' => 12
				]
			];
			$objPHPExcel->getActiveSheet()
				->getStyle("A1:N2")
				->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'DS LOẠI CHI PHÍ');
			$objPHPExcel->getActiveSheet()->mergeCells('A1:K1')->getStyle('A1:D1')->applyFromArray($BStyleHeader);;
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'MÃ LOẠI')->getStyle('A2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'TÊN LOẠI')->getStyle('B2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'MÃ CHI PHÍ CHA')->getStyle('C2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('D2', 'TÊN CHI PHÍ CHA')->getStyle('D2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('E2', 'MÃ CHI PHÍ')->getStyle('E2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('F2', 'TÊN CHI PHÍ')->getStyle('F2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('G2', 'STT')->getStyle('G2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('H2', 'Tên Danh Mục')->getStyle('H2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('I2', 'Mã Danh Mục')->getStyle('I2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('J2', 'Mã Nhóm Thiết Bị')->getStyle('J2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('K2', 'Loại Danh Mục')->getStyle('K2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('L2', 'Mã Phòng Ban')->getStyle('L2')->applyFromArray($BStyle);
		$objPHPExcel->getActiveSheet()->setCellValue('M2', 'Tên Phòng Ban')->getStyle('M2')->applyFromArray($BStyle);
		$this->db->select('
		    tblcosts.*,
		    tbl_type_cost.code as code_type,
		    tbl_type_cost.name as name_type,
		    tblcosts_parent.code as code_parent,
		    tblcosts_parent.name as name_parent,
		    tbldepartments.code as code_department,
		    tbldepartments.name as name_department,
		');
		$this->db->join('tblcosts tblcosts_parent', 'tblcosts_parent.id = tblcosts.costs_parent');
		$this->db->join('tbl_type_cost', 'tbl_type_cost.id = tblcosts.type');
		$this->db->join('tbl_machines', 'tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2','left');
		$this->db->join('tbl_category_payslip', 'tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3','left');
		$this->db->join('tbl_category_items', 'tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1','left');
		$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblcosts.department_id','left');
		$this->db->where('tblcosts.lever', 2);
		$this->db->where('tblcosts.active', 1);
		$this->db->order_by('tbl_type_cost.id asc,tblcosts_parent.code asc');
		$ktr = $this->db->get('tblcosts')->result();
        $stt = 1;
        $group = "";
		foreach ($ktr as $rom => $item) {
            $type_cost = $item->type_cost;
            $object_id = $item->object_id;
            $code_machines = '';
            $name_object = '';
            $code_object = '';
            if ($type_cost == 1){
                $this->db->from('tbl_category_items');
                $this->db->where('tbl_category_items.id',$object_id);
                $dtObject = $this->db->get()->row_array();
                if(!empty($dtObject)){
                    $name_object = $dtObject['name'];
                    $code_object = $dtObject['code'];
                }
            } elseif ($type_cost == 2){
                $this->db->select('tbl_machines.*,tbl_category_machines.code as code_machines');
                $this->db->from('tbl_machines');
                $this->db->join('tbl_category_machines','tbl_category_machines.id = tbl_machines.category_machine_id');
                $this->db->where('tbl_machines.id',$object_id);
                $dtObject = $this->db->get()->row_array();
                if(!empty($dtObject)){
                    $name_object = $dtObject['name'];
                    $code_object = $dtObject['code'];
                    $code_machines = $dtObject['code_machines'];
                }
            } elseif ($type_cost == 3){
                $this->db->from('tbl_category_payslip');
                $this->db->where('tbl_category_payslip.id',$object_id);
                $dtObject = $this->db->get()->row_array();
                if (!empty($dtObject)){
                    $name_object = $dtObject['name'];
                    $code_object = $dtObject['code'];
                }
            }
            if ($group != $item->code_parent){
                $stt = 1;
            }
            $group = $item->code_parent;
			$objPHPExcel->getActiveSheet()->setCellValue('A' . ($rom + 3), $item->code_type)->getStyle('A' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . ($rom + 3), $item->name_type)->getStyle('B' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . ($rom + 3), $item->code_parent)->getStyle('C' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . ($rom + 3), $item->name_parent)->getStyle('D' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . ($rom + 3), $item->code)->getStyle('E' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . ($rom + 3), $item->name)->getStyle('F' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('G' . ($rom + 3), $stt)->getStyle('G' . ($rom + 3))->applyFromArray($BStyleCenter);
			$objPHPExcel->getActiveSheet()->setCellValue('H' . ($rom + 3), $name_object)->getStyle('H' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('I' . ($rom + 3), $code_object)->getStyle('I' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('J' . ($rom + 3), $code_machines)->getStyle('J' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('K' . ($rom + 3), $type_cost)->getStyle('K' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('L' . ($rom + 3), $item->code_department)->getStyle('L' . ($rom + 3))->applyFromArray($BStyle1);
			$objPHPExcel->getActiveSheet()->setCellValue('M' . ($rom + 3), $item->name_department)->getStyle('M' . ($rom + 3))->applyFromArray($BStyle1);
            $stt++;
        }
		// $objPHPExcel->getActiveSheet()->freezePane('A4');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="DS_loai_chi_phi.xls"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		exit();
	}
	// yct end

	public function getCosts()
	{

		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_cost_department = "(
            SELECT 
                tblcost_department.cost_id,
                GROUP_CONCAT(tbldepartments.departmentid) as department_id,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tblcost_department
            JOIN tbldepartments ON tblcost_department.department_id = tbldepartments.departmentid
            GROUP BY tblcost_department.cost_id
        ) tb_cost_department ";

		$aColumns = [
			'tbl_type_cost.code as code_type',
			'tbl_type_cost.name as name_type',
            'tb_cost.code as code_parent',
            'tb_cost.name as name_parent',
			'tblcosts.code as code',
			'tblcosts.name as name',
            '"" as stt',
			'CASE 
                WHEN tblcosts.type_cost = 1 THEN tbl_category_items.name
                WHEN tblcosts.type_cost = 2 THEN tbl_machines.name
                WHEN tblcosts.type_cost = 3 THEN tbl_category_payslip.name
                ELSE tblcosts.detail
             END as detail',
            'tb_cost_department.name_department as name_department',
			'"" as actions',
		];
		$sIndexColumn = 'id';
		$sTable       = 'tblcosts';
		$where        = [
            'AND tblcosts.active = 1 AND tblcosts.lever > 1'
        ];
		$filter = [];
		$type_cost = $this->input->post('type_cost');
		if (!empty($type_cost)) {
			$where[]        = 'AND tblcosts.type = ' . $type_cost;
		}
		$join = [
			'LEFT JOIN tblcosts tb_cost ON tb_cost.id = tblcosts.costs_parent',
			'LEFT JOIN tbl_type_cost ON tbl_type_cost.id = tblcosts.type',
			'LEFT JOIN '.$tb_cost_department.' ON tb_cost_department.cost_id = tblcosts.id',
			'LEFT JOIN tbl_machines ON tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2',
			'LEFT JOIN tbl_category_payslip ON tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3',
			'LEFT JOIN tbl_category_items ON tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1',
		];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblcosts.id','tblcosts.type','tblcosts.type_cost','tblcosts.object_id','tb_cost_department.department_id'], 'ORDER BY tbl_type_cost.id asc,tb_cost.code asc', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
        $group = "";
        $stt = 1;
		foreach ($rResult as $key => $aRow) {
			$start++;
			$cost_id = $aRow['id'];
            $row = array();
            if ($group != $aRow['code_parent']){
                $stt = 1;
            }
            $group = $aRow['code_parent'];
			$row[] = '<div>' . $aRow['code_type'] . '</div>';
			$row[] = '<div>' . $aRow['name_type'] . '</div>';
            $row[] = $aRow['code_parent'];
            $row[] = $aRow['name_parent'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = '<div class="text-center">'.$stt.'</div>';
			$row[] = $aRow['detail'];
			$row[] = $aRow['name_department'];
			$html = '<div>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "edit_costs(" . $aRow['id'] . ",'" . $aRow['code'] . "','" . $aRow['name'] . "', 0, '" . $aRow['type'] . "','".$aRow['type_cost']."','".$aRow['object_id']."','".$aRow['department_id']."'); return false;"));
			$ktr = get_table_where('tblcosts', array('costs_parent' => $aRow['id']), '', 'row');
			if (empty($ktr) && !exsit_costs($aRow['id'])) {
				$html .= '<a onclick="delete_costs(' . $aRow['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a></div>';
			}
            $stt++;
			if ($aRow['id'] == 95) {
				$html = '';
			}
			$row[] = $html;
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}

    public function getDataCategory(){
        $type_cost = $this->input->post('type_cost');
        $dtObject = [];
        if ($type_cost == 1){
            $this->db->from('tbl_category_items');
            $dtObject = $this->db->get()->result_array();
        } elseif ($type_cost == 2){
            $this->db->from('tbl_machines');
            $dtObject = $this->db->get()->result_array();
        } elseif ($type_cost == 3){
            $this->db->from('tbl_category_payslip');
            $dtObject = $this->db->get()->result_array();
        }
        $data['dtObject'] = $dtObject;
        echo json_encode($data);
    }
}
