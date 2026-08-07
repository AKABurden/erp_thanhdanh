<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Trouble extends AdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data['title'] = _l('c_trouble');
		$this->load->view('admin/trouble/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('trouble');
	}

	public function modal($id = '')
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$items = $this->input->post('items');
			$id_procedure = $this->input->post('id_procedure');
			if (!empty($id)) {
				$this->db->where('code', $data['code']);
				$this->db->where('id != "' . $id . '"');
				$ktCategory = $this->db->get('tbltrouble')->row();
				if (!empty($ktCategory)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => _l('Mã sự cố đã tồn tại')
					]);
					die();
				}

				$this->db->where('id', $id);
				$success = $this->db->update('tbltrouble', [
					'id_criteria' => $data['id_criteria'],
					'id_departments' => $data['id_departments'],
					'name_stage' => $data['name_stage'],
					'name_task' => $data['name_task'],
					'code' => $data['code'],
					'name' => $data['name'],
					'trouble_violation_point_id' => $data['trouble_violation'],
				]);
				if (!empty($success)) {
					$this->db->where('id_trouble', $id);
					$this->db->where('type != "procedure"');
					$this->db->delete('tbltrouble_items');
					$id_procedure_not_delete = [];


					if (!empty($items)) {
						foreach ($items as $type => $item) {
							if ($type != 'procedure') {
								foreach ($item as $k => $v) {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $v,
										'type' => $type
									]);
								}
							} else {
								foreach ($item as $k => $v) {
									if (empty($id_procedure[$k])) {
										$this->db->insert('tbltrouble_items', [
											'id_trouble' => $id,
											'name' => $v,
											'type' => $type
										]);
										$id_items = $this->db->insert_id();
										$id_procedure_not_delete[] = $id_items;
									} else {
										$id_items = $id_procedure[$k];
										$this->db->where('id', $id_items);
										$this->db->update('tbltrouble_items', [
											'name' => $v
										]);
										$id_procedure_not_delete[] = $id_items;
									}
									if (!empty($_FILES['file']['name'][$k]) && !empty($id_items)) {
										if (!file_exists(FCPATH . 'uploads/trouble/')) {
											mkdir(FCPATH . 'uploads/trouble/');
											fopen(FCPATH . 'uploads/trouble/', 'w');
										}
										if (!file_exists(FCPATH . 'uploads/trouble/' . $id_items . '/')) {
											mkdir(FCPATH . 'uploads/trouble/' . $id_items . '/');
											fopen(FCPATH . 'uploads/trouble/' . $id_items . '/index.html', 'w');
										}
										$filename = $_FILES['file']['name'][$k] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name'][$k]));
										if (is_uploaded_file($_FILES['file']['tmp_name'][$k])) {
											$typeFile = $_FILES['file']['type'][$k];
											$source_path = $_FILES['file']['tmp_name'][$k];
											$target_path = FCPATH . 'uploads/trouble/' . $id_items . '/' . $_FILES['file']['name'][$k];
											if (move_uploaded_file($source_path, $target_path)) {
												$this->db->insert('tblfiles', [
													'rel_id' => $id_items,
													'rel_type' => 'trouble',
													'file_name' => $filename,
													'filetype' => $typeFile,
													'staffid' => get_staff_user_id(),
													'dateadded' => date('Y-m-d H:i:s'),
												]);
											}
										}
									}
								}
							}
						}
					}

					if (!empty($id_procedure_not_delete)) {
						$this->db->where_not_in('id', $id_procedure_not_delete);
					}
					$this->db->where('id_trouble', $id);
					$this->db->where('type', 'procedure');
					$procedure_delete = $this->db->get('tbltrouble_items')->result_array();
					if (!empty($procedure_delete)) {
						foreach ($procedure_delete as $key => $value) {
							$files = $this->db->get_where('tblfiles', ['rel_type' => 'trouble', 'rel_id' => $value['id']])->row();
							if (!empty($files)) {
								$link = FCPATH . 'uploads/trouble/' . $files->id . '/' . $files->file_name;
								@unlink($link);
								$this->db->where('id', $files->id);
								$this->db->delete('tblfiles');
							}
							$this->db->where('id', $value['id']);
							$this->db->delete('tbltrouble_items');
						}
					}



					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => _l('cong_update_true')
					]);
					die();
				}
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('cong_update_false')
				]);
				die();
			} else {
				$this->db->where('code', $data['code']);
				$ktTrouble = $this->db->get('tbltrouble')->row();
				if (!empty($ktTrouble)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => _l('Mã sự cố đã tồn tại')
					]);
					die();
				}

				$success = $this->db->insert('tbltrouble', [
					'code' => $data['code'],
					'name' => $data['name'],
					'id_criteria' => $data['id_criteria'],
					'id_departments' => $data['id_departments'],
					'name_stage' => $data['name_stage'],
					'name_task' => $data['name_task'],
					'create_by' => get_staff_user_id(),
					'trouble_violation_point_id' => $data['trouble_violation'],
				]);
				if (!empty($success)) {
					$id = $this->db->insert_id();
					if (!empty($items)) {
						foreach ($items as $type => $item) {
							if ($type != 'procedure') {
								foreach ($item as $k => $v) {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $v,
										'type' => $type
									]);
								}
							} else {
								foreach ($item as $k => $v) {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $v,
										'type' => $type
									]);
									$id_items = $this->db->insert_id();
									if (!empty($_FILES['file']['name'][$k]) && !empty($id_items)) {
										if (!file_exists(FCPATH . 'uploads/trouble/')) {
											mkdir(FCPATH . 'uploads/trouble/');
											fopen(FCPATH . 'uploads/trouble/', 'w');
										}
										if (!file_exists(FCPATH . 'uploads/trouble/' . $id_items . '/')) {
											mkdir(FCPATH . 'uploads/trouble/' . $id_items . '/');
											fopen(FCPATH . 'uploads/trouble/' . $id_items . '/index.html', 'w');
										}
										$filename = $_FILES['file']['name'][$k] = time() . '_' . preg_replace("/[\/\'\"\$]/", "_", vn_to_str($_FILES['file']['name'][$k]));
										if (is_uploaded_file($_FILES['file']['tmp_name'][$k])) {
											$typeFile = $_FILES['file']['type'][$k];
											$source_path = $_FILES['file']['tmp_name'][$k];
											$target_path = FCPATH . 'uploads/trouble/' . $id_items . '/' . $_FILES['file']['name'][$k];
											if (move_uploaded_file($source_path, $target_path)) {
												$this->db->insert('tblfiles', [
													'rel_id' => $id_items,
													'rel_type' => 'trouble',
													'file_name' => $filename,
													'filetype' => $typeFile,
													'staffid' => get_staff_user_id(),
													'dateadded' => date('Y-m-d H:i:s'),
												]);
											}
										}
									}
								}
							}
						}
					}
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => _l('cong_add_true')
					]);
					die();
				}
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('cong_add_false')
				]);
				die();
			}
		} else {
			$data['title'] = _l('c_create_trouble');
			if (!empty($id)) {
				$data['title'] = _l('c_edit_trouble');
				$data['trouble'] = $this->db->get_where('tbltrouble', ['id' => $id])->row();
				if (!empty($data['trouble'])) {
					$data['trouble']->material = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'material'])->result_array();
					$data['trouble']->man = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'man'])->result_array();
					$data['trouble']->machine = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'machine'])->result_array();
					$data['trouble']->method = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'method'])->result_array();
					$data['trouble']->environment = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'environment'])->result_array();
					$data['trouble']->procedure = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'procedure'])->result_array();
					$data['trouble']->fix = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'fix'])->result_array();
				}
			}

			$data['departments'] = $this->db->get_where('tbldepartments')->result_array();
			$data['criteria'] = $this->db->get_where('tbl_kpi_criteria')->result_array();
			$this->load->view('admin/trouble/modal', $data);
		}
	}

	public function delete($id = '')
	{
		if (!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tbltrouble');
			if (!empty($delete)) {
				$this->db->where('id_trouble', $id);
				$this->db->delete('tbltrouble_items');
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa sự cố thành công'
				]);
				die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa sự cố không thành công'
		]);
		die();
	}
	function countTrouble()
	{
		$id = $this->input->post('id');
		$date = $this->input->post('date');
		$count = 0;
		if (!empty($id)) {
			if (!empty($date)) {
				$date = to_sql_date($date, true);
				$date = strtotime($date);
				$date = strftime("%m", $date);
			} else {
				$date = date('m');
			}
			$count = countTrouble($id, $date);
		}
		echo json_encode($count);
		die();
	}
	public function get_trouble($id = '')
	{
		// $data['trouble'] = $this->db->get_where('tbltrouble', ['id' => $id])->row();
		$this->db->select('tbltrouble.*, tbltrouble_violation_point.name as violation_name, tbltrouble_violation_point.point as violation_point');
		$this->db->join('tbltrouble_violation_point', 'tbltrouble_violation_point.id = tbltrouble.trouble_violation_point_id', 'left');
		$this->db->where('tbltrouble.id', $id);
		$data['trouble'] = $this->db->get('tbltrouble')->row();
		if (!empty($data['trouble'])) {
			$data['trouble']->material = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'material'])->result_array();
			$data['trouble']->man = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'man'])->result_array();
			$data['trouble']->machine = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'machine'])->result_array();
			$data['trouble']->method = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'method'])->result_array();
			$data['trouble']->environment = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'environment'])->result_array();
			$data['trouble']->procedure = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'procedure'])->result_array();
			$data['trouble']->fix = $this->db->get_where('tbltrouble_items', ['id_trouble' => $id, 'type' => 'fix'])->result_array();
		}
		echo json_encode($data['trouble']);
		die();
	}

	public function modal_excel()
	{
		$data['title'] = _l('Import excel sự cố');
		$this->load->view('admin/trouble/import', $data);
	}

	public function import()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');

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

			$vaKey = '';
			$material = [];
			$man = [];
			$machine = [];
			$method = [];
			$procedure = [];
			$listRow = [
				1 => 'id_criteria',
				2 => 'id_departments',
				3 => 'name_stage',
				4 => 'name_task',
				5 => 'code',
				6 => 'name',
				7 => 'trouble_violation',
				8 => 'material',
				9 => 'man',
				10 => 'machine',
				11 => 'method',
				12 => 'environment',
				13 => 'procedure',
				14 => 'fix',
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
						//							echo '<pre>';
						if ($j == 5) {
							if (!empty($Val)) {
								$vaKey = $Val;
								$material = [];
								$man = [];
								$machine = [];
								$method = [];
								$environment = [];
								$procedure = [];
								$fix = [];
							}
						}
						//						else if ($j == 8) {
						//							if(is_numeric($Val)) {
						//								$Val = PHPExcel_Shared_Date::ExcelToPHPObject($Val)->format('d-m-Y H:i:s');
						//							}
						//						}
						else if ($j == 8) {
							$material[] = $Val;
						} else if ($j == 9) {
							$man[] = $Val;
						} else if ($j == 10) {
							$machine[] = $Val;
						} else if ($j == 11) {
							$method[] = $Val;
						} else if ($j == 12) {
							$environment[] = $Val;
						} else if ($j == 13) {
							$procedure[] = $Val;
						} else if ($j == 14) {
							$fix[] = $Val;
						}

						$redata[$listRow[$j]] = trim($Val);
					}
					if (!empty($vaKey)) {
						if (!empty($data[$vaKey])) {
							$data[$vaKey]['material'] = $material;
							$data[$vaKey]['man'] = $man;
							$data[$vaKey]['machine'] = $machine;
							$data[$vaKey]['method'] = $method;
							$data[$vaKey]['environment'] = $environment;
							$data[$vaKey]['procedure'] = $procedure;
							$data[$vaKey]['fix'] = $fix;
						} else {
							$data[$vaKey] = $redata;
						}
					}
				}
			}
		}

		$count = 0;
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				//				$value['standard'] = $this->db->get_where('tbl_packaging', ['code' => $value['standard']])->row('id');
				//				if(empty($value['standard'])) {
				//					$value['standard'] = NULL;
				//				}

				$value['id_criteria'] = $this->db->get_where('tbl_kpi_criteria', ['code_criteria' => $value['id_criteria']])->row('id');
				$value['id_departments'] = $this->db->get_where('tbldepartments', ['code' => $value['id_departments']])->row('departmentid');

				$id = $this->getIdByCode($value['code']);
				if (!empty($id)) { // update
					$options = [
						'name' => $value['name'],
						'id_criteria' => $value['id_criteria'],
						'id_departments' => $value['id_departments'],
						'name_stage' => $value['name_stage'],
						'name_task' => $value['name_task'],
						'trouble_violation_point_id' => $value['trouble_violation'],
					];
					$this->db->where('id', $id);
					$success = $this->db->update('tbltrouble', $options);
					if (!empty($success)) {
						$material = $value['material'];
						$man = $value['man'];
						$machine = $value['machine'];
						$method = $value['method'];
						$environment = $value['environment'];
						$procedure = $value['procedure'];
						$fix = $value['fix'];
						foreach ($material as $name) {
							if (!empty($name)) {
								$itemId = $this->getItemIdByName($id, $name);
								if (!empty($itemId)) { // update
									// $this->db->where('id', $itemId);
									// $this->db->update('tbltrouble_items', [
									// 	'type' => 'material',
									// ]);
								} else {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'material',
									]);
								}
							}
						}
						foreach ($man as $name) {
							if (!empty($name)) {
								$itemId = $this->getItemIdByName($id, $name);
								if (!empty($itemId)) { // update
									// $this->db->where('id', $itemId);
									// $this->db->update('tbltrouble_items', [
									// 	'type' => 'man',
									// ]);
								} else {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'man',
									]);
								}
							}
						}
						foreach ($machine as $name) {
							if (!empty($name)) {
								$itemId = $this->getItemIdByName($id, $name);
								if (!empty($itemId)) { // update
									// $this->db->where('id', $itemId);
									// $this->db->update('tbltrouble_items', [
									// 	'id_trouble' => $id,
									// ]);
								} else {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'machine',
									]);
								}
							}
						}
						foreach ($method as $name) {
							if (!empty($name)) {
								$itemId = $this->getItemIdByName($id, $name);
								if (!empty($itemId)) { // update
									// $this->db->where('id', $itemId);
									// $this->db->update('tbltrouble_items', [
									// 	'type' => 'method',
									// ]);
								} else {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'method',
									]);
								}
							}
						}
						foreach ($environment as $name) {
							if (!empty($name)) {
								$itemId = $this->getItemIdByName($id, $name);
								if (!empty($itemId)) { // update
									// $this->db->where('id', $itemId);
									// $this->db->update('tbltrouble_items', [
									// 	'type' => 'environment',
									// ]);
								} else {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'environment',
									]);
								}
							}
						}
						foreach ($procedure as $name) {
							if (!empty($name)) {
								$itemId = $this->getItemIdByName($id, $name);
								if (!empty($itemId)) { // update
									// $this->db->where('id', $itemId);
									// $this->db->update('tbltrouble_items', [
									// 	'type' => 'procedure',
									// ]);
								} else {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'procedure',
									]);
								}
							}
						}

						if (!empty($fix)) {
							foreach ($fix as $name) {
								if (!empty($name)) {
									$itemId = $this->getItemIdByName($id, $name, 'fix');
									if (!empty($itemId)) { // update
										// $this->db->where('id', $itemId);
										// $this->db->update('tbltrouble_items', [
										// 	'type' => 'procedure',
										// ]);
									} else {
										$this->db->insert('tbltrouble_items', [
											'id_trouble' => $id,
											'name' => $name,
											'type' => 'fix',
										]);
									}
								}
							}
						}
						$count++;
					}
				} else { // insert
					$options = [
						'code' => $value['code'],
						'name' => $value['name'],
						'id_criteria' => $value['id_criteria'],
						'id_departments' => $value['id_departments'],
						'name_stage' => $value['name_stage'],
						'name_task' => $value['name_task'],
						'create_by' => get_staff_user_id(),
						'date_create' => date('Y-m-d H:i:s'),
						'trouble_violation_point_id' => $value['trouble_violation'],
					];
					$success = $this->db->insert('tbltrouble', $options);
					if (!empty($success)) {
						$id = $this->db->insert_id();
						$material = $value['material'];
						$man = $value['man'];
						$machine = $value['machine'];
						$method = $value['method'];
						$environment = $value['environment'];
						$procedure = $value['procedure'];
						$fix = $value['fix'];
						foreach ($material as $name) {
							if (!empty($name)) {
								$this->db->insert('tbltrouble_items', [
									'id_trouble' => $id,
									'name' => $name,
									'type' => 'material',
								]);
							}
						}
						foreach ($man as $name) {
							if (!empty($name)) {
								$this->db->insert('tbltrouble_items', [
									'id_trouble' => $id,
									'name' => $name,
									'type' => 'man',
								]);
							}
						}
						foreach ($machine as $name) {
							if (!empty($name)) {
								$this->db->insert('tbltrouble_items', [
									'id_trouble' => $id,
									'name' => $name,
									'type' => 'machine',
								]);
							}
						}
						foreach ($method as $name) {
							if (!empty($name)) {
								$this->db->insert('tbltrouble_items', [
									'id_trouble' => $id,
									'name' => $name,
									'type' => 'method',
								]);
							}
						}
						foreach ($environment as $name) {
							if (!empty($name)) {
								$this->db->insert('tbltrouble_items', [
									'id_trouble' => $id,
									'name' => $name,
									'type' => 'environment',
								]);
							}
						}
						foreach ($procedure as $name) {
							if (!empty($name)) {
								$this->db->insert('tbltrouble_items', [
									'id_trouble' => $id,
									'name' => $name,
									'type' => 'procedure',
								]);
							}
						}

						if (!empty($fix)) {
							foreach ($fix as $name) {
								if (!empty($name)) {
									$this->db->insert('tbltrouble_items', [
										'id_trouble' => $id,
										'name' => $name,
										'type' => 'fix',
									]);
								}
							}
						}

						$count++;
					}
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

	public function remove_file($id = "")
	{
		if (!empty($id)) {
			$this->db->where('tblfiles.id', $id);
			$this->db->where('tblfiles.rel_type', 'trouble');
			$files = $this->db->get('tblfiles')->row();
			if (!empty($files)) {
				$link = FCPATH . 'uploads/trouble/' . $files->id . '/' . $files->file_name;
				@unlink($link);
				$this->db->where('id', $id);
				$success = $this->db->delete('tblfiles');
				if (!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Xóa file thành công'
					]);
					die();
				}
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa file không thành công'
		]);
		die();
	}

	public function getIdByCode($code)
	{
		$this->db->select('tbltrouble.id');
		$this->db->from('tbltrouble');
		$this->db->where('tbltrouble.code', $code);
		$result = $this->db->get()->row();
		if (empty($result)) {
			return 0;
		} else {
			return $result->id;
		}
	}

	public function getItemIdByName($id_trouble, $name, $type = '')
	{
		$this->db->select('tbltrouble_items.id');
		$this->db->from('tbltrouble_items');
		$this->db->where('tbltrouble_items.id_trouble', $id_trouble);
		$this->db->where('tbltrouble_items.name', $name);
		if ($type) {
			$this->db->where('tbltrouble_items.type', $type);
		}
		$result = $this->db->get()->row();
		if (empty($result)) {
			return 0;
		} else {
			return $result->id;
		}
	}

	public function category_problem()
	{
		$this->perAddCategoryProblem = true;
		$perViewCategoryProblem = true;
		if (!$perViewCategoryProblem) {
			access_denied('category_problem');
		}
		$data['title'] = _l('dt_category_problem');
		$this->load->view('admin/trouble/category_problem', $data);
	}


	public function getCategoryProblem()
	{
		$perEditCategoryProblem = true;
		$perDeleteCategoryProblem = true;
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tbl_category_problem.id as id',
			'tbl_category_problem.code as code',
			'tbl_category_problem.name as name',
			'tbl_category_problem.note as note',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_category_problem';
		$where = [];
		$filter = [];

		$join = [];


		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = array();
			$row[] = '<div class="text-center">' . (++$key) . '</div>';
			$row[] = '<div class="text-left">' . $aRow['code'] . '</div>';
			$row[] = '<div class="text-left">' . $aRow['name'] . '</div>';
			$row[] = '<div class="text-left">' . $aRow['note'] . '</div>';

			$edit = $perEditCategoryProblem ? '<a class="tnh-modal" href="' . base_url('admin/trouble/detail_category_problem/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit')  . '</a>' : '';

			$delete = $perDeleteCategoryProblem ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/trouble/delete_category_problem/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>' : '';
			$actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
			$row[] = '<div>' . $actions . '</div>';
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function detail_category_problem($id = 0)
	{
		$data = [];
		$perEditCategoryProblem = true;
		$perAddCategoryProblem = true;
		$this->db->select('tbl_category_problem.*');
		$this->db->from('tbl_category_problem');
		$this->db->where('tbl_category_problem.id', $id);
		$dtData = $this->db->get()->row_array();
		if ($this->input->post()) {
			if (!empty($id)) {
				if ($dtData['code'] != $this->input->post('code')) {
					$this->form_validation->set_rules('code', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_category_problem.code]');
				}
			} else {
				$this->form_validation->set_rules('code', lang("Mã Phiếu"), 'required|is_unique[tbl_category_problem.code]');
			}
			$this->form_validation->set_rules('name', lang("name"), 'required');
			if (empty($id)) {
				if ($this->form_validation->run() == true) {
					$code = ($this->input->post('code'));
					$name = ($this->input->post('name'));
					$note = ($this->input->post('note'));
					$fields = [
						'code' => $code,
						'name' => $name,
						'note' => $note,
						'created_by' => get_staff_user_id(),
						'date_created' => date('Y-m-d H:i:s'),
					];
					$this->db->insert('tbl_category_problem', $fields);
					$id = $this->db->insert_id();
					if ($id) {
						insertActivityLog([
							'type_parent_obj' => 'category_problem',
							'table_obj' => 'tbl_category_problem',
							'id_obj' => $id,
							'name_obj' => $code,
							'content' => lang('Thêm mới nhóm sự cố') . ' [' . $code . ']',
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
				if ($this->form_validation->run() == true) {
					$code = ($this->input->post('code'));
					$name = ($this->input->post('name'));
					$note = ($this->input->post('note'));
					$fields = [
						'code' => $code,
						'name' => $name,
						'note' => $note,
					];
					$this->db->where('id', $id);
					$success = $this->db->update('tbl_category_problem', $fields);
					if ($success) {
						insertActivityLog([
							'type_parent_obj' => 'category_problem',
							'table_obj' => 'tbl_category_problem',
							'id_obj' => $id,
							'name_obj' => $dtData['code'],
							'content' => lang('Sửa nhóm sự cố') . ' [' . $dtData['code'] . ']',
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
				if (!$perAddCategoryProblem) {
					accessDenied(true);
				}
				$data['title'] = lang('dt_add_category_problem');
			} else {
				if (!$perEditCategoryProblem) {
					accessDenied(true);
				}
				$data['dtData'] = $dtData;
				$data['title'] = lang('dt_edit_category_problem');
			}
		}
		$data['id'] = $id;
		$this->load->view('admin/trouble/detail_category_problem', $data);
	}

	public function delete_category_problem($id)
	{
		$preDeleteCategoryProblem = true;
		if (!$preDeleteCategoryProblem) {
			$data['result'] = 0;
			$data['message'] = lang('access_denied');
			echo json_encode($data);
			die();
		}
		$data = [];
		$this->db->select('tbl_category_problem.*');
		$this->db->from('tbl_category_problem');
		$this->db->where('tbl_category_problem.id', $id);
		$dtData = $this->db->get()->row_array();
		if (empty($dtData)) {
			$data['result'] = 0;
			$data['message'] = lang('not_data_exists');
			echo json_encode($data);
			die();
		}

		$this->db->where('id', $id);
		$success = $this->db->delete('tbl_category_problem');
		if ($success) {

			insertActivityLog([
				'type_parent_obj' => 'category_problem',
				'table_obj' => 'tbl_category_problem',
				'id_obj' => $id,
				'name_obj' => $dtData['code'],
				'content' => lang('Xóa nhóm sự cố') . ' [' . $dtData['code'] . ']',
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

	public function changeStatusSalary3p($id, $salary_p3)
	{
		$data = [];
		$this->db->where('id', $id);
		$success = $this->db->update('tbltrouble', [
			'salary_p3' => $salary_p3
		]);

		if ($success) {
			$data['result'] = 1;
			$data['message'] = lang('Thành công');
		} else {
			$data['result'] = 0;
			$data['message'] = lang('Thất bại');
		}
		echo json_encode($data);
	}
}
