<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_tasks extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		if (!is_admin()) {
			access_denied('Category_tasks');
		}
	}

	public function index()
	{
		$data['title'] = _l('c_category_tasks');

		$data['departments'] = get_table_where('tbl_room');
		// $data['departments'] = get_table_where('tbldepartments', ['active_departments' => 1]);
		$data['roles'] = $this->db->get('tblroles')->result_array();
		$data['roles'] = [];
		$this->get_parent(0, $data['roles']);

		$this->load->view('admin/category_tasks/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('category_tasks');
	}

	public function modal($id = '')
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$process = $this->input->post('process');
			if (!empty($id)) {
				// $this->db->where('code', $data['code']);
				// $this->db->where('id != "' . $id . '"');
				// $ktCategory = $this->db->get('tblcategory_tasks')->row();
				// if (!empty($ktCategory)) {
				// 	echo json_encode([
				// 		'success' => false,
				// 		'alert_type' => 'danger',
				// 		'message' => _l('Mã công việc đã tồn tại')
				// 	]);
				// 	die();
				// }
				if (!empty($process)) {
					$check_stages = 0;
					foreach ($process as $key => $value) {
						if ($value['stages']) {
							$check_stages++;
						}
					}
				}
				if (!empty($check_stages)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'danger',
						'message' => _l('Quy trình chỉ có 1 công đoạn bàn giao')
					]);
					die();
				}
				$this->db->where('id', $id);
				$success = $this->db->update('tblcategory_tasks', [
					'code' => $data['code'],
					'content' => $data['content'],
					'time' => $data['time'],
					'date_approve' => !empty($data['date_approve']) ? to_sql_date($data['date_approve'], true) : NULL,
					'active' => !empty($data['active']) ? $data['active'] : 0,
					'date_update' => date('Y-m-d H:i:s')
				]);
				if (!empty($success)) {
					// $this->db->where('id_category_tasks', $id);
					// $this->db->delete('tblcategory_tasks_process');
					// if (!empty($process)) {
					// 	foreach ($process as $key => $value) {
					// 		$this->db->insert('tblcategory_tasks_process', [
					// 			'id_category_tasks' => $id,
					// 			'name' => $value['name'],
					// 			'stages' => $value['stages']
					// 		]);
					// 	}
					// }

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
				$ktCategory = $this->db->get('tblcategory_tasks')->row();
				if (!empty($ktCategory)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => _l('Mã công việc đã tồn tại')
					]);
					die();
				}

				$success = $this->db->insert('tblcategory_tasks', [
					'code' => $data['code'],
					'content' => $data['content'],
					'time' => $data['time'],
					'departments' => !empty($data['departments']) ? implode(',', $data['departments']) : NULL,
					'create_by' => get_staff_user_id(),
					'date_approve' => !empty($data['date_approve']) ? to_sql_date($data['date_approve'], true) : NULL,
					'active' => !empty($data['active']) ? $data['active'] : 0,
				]);
				if (!empty($success)) {
					$id = $this->db->insert_id();
					// if (!empty($process)) {
					// 	foreach ($process as $key => $value) {
					// 		$this->db->insert('tblcategory_tasks_process', [
					// 			'id_category_tasks' => $id,
					// 			'name' => $value
					// 		]);
					// 	}
					// }
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
			$data['title'] = _l('c_create_category_tasks');
			if (!empty($id)) {
				$data['title'] = _l('c_edit_category_tasks');
				$data['category_tasks'] = $this->db->get_where('tblcategory_tasks', ['id' => $id])->row();
				if (!empty($data['category_tasks'])) {
					$data['category_tasks']->process = $this->db->get_where('tblcategory_tasks_process', ['id_category_tasks' => $id])->result_array();
				}
			}

			$this->db->where('tbldepartments.active_departments', 1);
			$data['departments'] = $this->db->get('tbldepartments')->result_array();

			$this->db->where('tbl_stages.type_use', 0);
			$data['stages'] = $this->db->get('tbl_stages')->result_array();
			$this->load->view('admin/category_tasks/modal', $data);
		}
	}

	public function delete($id = '')
	{
		if (!empty($id)) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tblcategory_tasks');
			if (!empty($delete)) {

				$this->db->where('id_category_tasks', $id);
				$this->db->delete('tblcategory_tasks_process');

				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa mã công việc thành công'
				]);
				die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa mã công việc không thành công'
		]);
		die();
	}


	public function modal_excel()
	{
		$data['title'] = _l('Import excel mã công việc');
		$this->load->view('admin/category_tasks/import', $data);
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
			$process = [];
			$approval_standards = [];
			$completion_control_standards = [];
			$listRow = [
				1 => 'departments_name',
				2 => 'code',
				3 => 'code_new',
				4 => 'content',
				5 => 'role_id_1',
				6 => 'role_id_2',
				7 => 'process',
				8 => 'kpi_plus',
				9 => 'kpi_minus',
				10 => 'process_child',
				11 => 'approval_standards',
				12 => 'completion_control_standards',
				13 => 'time',
				14 => 'type',
				15 => 'date_approve',
				16 => 'active',
				17 => 'role_processing',
			];
			$kpi_plus_val = 0;
			$kpi_minus_val = 0;
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();

				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				$vaKeyprocess = '';
                $maxCol = count($listRow);
				for ($i = 2; $i <= $highestRow; $i++) {
                    $redata = [];

					for ($j = 1; $j <= $maxCol; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						if ($j == 2) {
							if (!empty($Val)) {
								$vaKey = $Val;
								$process = [];
								$stages = [];
							}
						} else if ($j == 7) {
							$process[] = $Val;
							if (!empty($Val)) {
								$vaKeyprocess = $Val;
								$kpi_plus_val = $objWorksheet->getCellByColumnAndRow(8, $i)->getValue();
								$kpi_minus_val = $objWorksheet->getCellByColumnAndRow(9, $i)->getValue();
								$process_child = [];
								$approval_standards = [];
								$completion_control_standards = [];
								$role_processing = [];
							}
						} else if ($j == 15) {
							if (is_numeric($Val)) {
								$Val = PHPExcel_Shared_Date::ExcelToPHPObject($Val)->format('d-m-Y H:i:s');
							}
						} else if ($j == 10) {
							$process_child[] = $Val;
						} else if ($j == 11) {
							$approval_standards[] = $Val;
						} else if ($j == 12) {
							$completion_control_standards[] = $Val;
						} else if ($j == 17) {
							$role_processing[] = $Val;
						}
                        $redata[$listRow[$j]] = trim((string)$Val);
					}

					if (!empty($vaKey)) {

						if (!empty($data[$vaKey])) {
							$data[$vaKey]['process'] = $process;
							if (!empty($vaKeyprocess)) {
								$data[$vaKey][$vaKeyprocess]['process_child'] = $process_child;
								$data[$vaKey][$vaKeyprocess]['approval_standards'] = $approval_standards;
								$data[$vaKey][$vaKeyprocess]['completion_control_standards'] = $completion_control_standards;
								$data[$vaKey][$vaKeyprocess]['role_processing'] = $role_processing;
								if (isset($kpi_plus_val)) {
									$data[$vaKey][$vaKeyprocess]['kpi_plus'] = trim((string)$kpi_plus_val);
								}
								if (isset($kpi_minus_val)) {
									$data[$vaKey][$vaKeyprocess]['kpi_minus'] = trim((string)$kpi_minus_val);
								}
							}
						} else {
							$data[$vaKey] = $redata;
							if (!empty($vaKeyprocess)) {
								$data[$vaKey][$vaKeyprocess]['process_child'] = $process_child;
								$data[$vaKey][$vaKeyprocess]['approval_standards'] = $approval_standards;
								$data[$vaKey][$vaKeyprocess]['completion_control_standards'] = $completion_control_standards;
								$data[$vaKey][$vaKeyprocess]['role_processing'] = $role_processing;
								if (isset($kpi_plus_val)) {
									$data[$vaKey][$vaKeyprocess]['kpi_plus'] = trim((string)$kpi_plus_val);
								}
								if (isset($kpi_minus_val)) {
									$data[$vaKey][$vaKeyprocess]['kpi_minus'] = trim((string)$kpi_minus_val);
								}
							}
						}
					}
				}
			}
		}

		$arrayRoleProcessing = [];
		$count = 0;
		if (!empty($data)) {
			foreach ($data as $value) {
				if (empty($value['code'])) {
					continue;
				}

                $code_new = $value['code_new'] ?? null;
				$role_name_id_1 = $value['role_id_1'];
				$role_name_id_2 = $value['role_id_2'];

				$type = 0;
				if (!empty($value['type'])) {
					$type = $value['type'];
					if ($type != 'Ngày' && $type != 'Tháng' && $type != 'Năm') {
						continue;
					}

					if ($type == 'Ngày') {
						$type = 1;
					} else if ($type == 'Tháng') {
						$type = 2;
					} else if ($type == 'Năm') {
						$type = 3;
					}
				}

				// $id = $this->getIdByCode($value['code']);
				$id = $this->getIdByCodeContent($value['code'], $value['content']);
				$departmentId = $this->getDepartmentIdByName($value['departments_name']);

				$role_id_1 = 0;

				// if (!empty($role_name_id_1)) {
				// 	$this->db->select('
				// 	tbldepartments.departmentid as departmentid,
				// 	', false);
				// 	$this->db->from('tbldepartments');
				// 	//					$this->db->where('tblroles.departments_id', $departmentId);
				// 	$this->db->where('tbldepartments.name', $role_name_id_1);
				// 	$dtRole = $this->db->get()->row_array();

				// 	if (!empty($dtRole)) {
				// 		$role_id_1 = $dtRole['departmentid'];
				// 	} else {
				// 		continue;
				// 	}
				// }

				$role_id_2 = 0;
				if (!empty($role_name_id_2)) {
					$this->db->select('
						tblroles.roleid as roleid,
					', false);
					$this->db->from('tblroles');
					//					$this->db->where('tblroles.departments_id', $departmentId);
					$this->db->where('tblroles.code_role', $role_name_id_2);
					$this->db->where('tblroles.name_position', $role_name_id_1);
					$this->db->where('tblroles.active_role', 1);
					$dtRole = $this->db->get()->row_array();
					if (!empty($dtRole)) {
						$role_id_2 = $dtRole['roleid'];
					} else {
						continue;
					}
				}
				if (!empty($id)) { // update
					$options = [
                        'code_new' => $code_new,
                        'code_old' => $value['code'],
						'departments' => $departmentId,
						'content' => $value['content'],
						'time' => $value['time'],
						'type' => $type,
						'role_id_1' => $role_id_1,
						'role_id_2' => $role_id_2,
						'date_update' => date('Y-m-d H:i:s'),
					];
					foreach ($options as $key => $val) {
						if (empty($val)) {
							unset($options[$key]);
						}
					}
					$success = null;
					if (!empty($options)) {
						$this->db->where('id', $id);
						$success = $this->db->update('tblcategory_tasks', $options);
					}
					$process = $value['process'];

					if (!empty($process)) {
						if (is_array($process)) {
							$category_tasks_process_array = [];
							foreach ($process as $key => $name) {
								if (!empty($name)) {
									$isExistProcessName = $this->isExistProcessName($id, $name);
									if (!$isExistProcessName) {
										$id_stages = 0;
										// if (!empty($stages[$key])) {
										// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages[$key]), '', 'row_array');
										// 	if (!empty($check_stages)) {
										// 		$id_stages = $check_stages['id'];
										// 	}
										// }
										$success = $this->db->insert('tblcategory_tasks_process', [
											'id_category_tasks' => $id,
											'name' => $name,
											'stages' => $id_stages,
											'kpi_plus' => isset($value[$name]['kpi_plus']) ? $value[$name]['kpi_plus'] : 0,
											'kpi_minus' => isset($value[$name]['kpi_minus']) ? $value[$name]['kpi_minus'] : 0,
										]);
										$id_category_tasks_process = $this->db->insert_id();
									} else {
										// $idd = $this->isExistProcessName($id, $name);

										$id_category_tasks_process = $isExistProcessName;
										$id_stages = 0;
										// if (!empty($stages[$key])) {
										// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages[$key]), '', 'row_array');
										// 	if (!empty($check_stages)) {
										// 		$id_stages = $check_stages['id'];
										// 	}
										// }
										$this->db->where('id_category_tasks', $id);
										$this->db->where('name', $name);
										$success = $this->db->update('tblcategory_tasks_process', [
											'stages' => $id_stages,
											'kpi_plus' => isset($value[$name]['kpi_plus']) ? $value[$name]['kpi_plus'] : 0,
											'kpi_minus' => isset($value[$name]['kpi_minus']) ? $value[$name]['kpi_minus'] : 0,
										]);
									}
									$category_tasks_process_array[] = $id_category_tasks_process;
									$this->db->where('id_category_tasks_process', $id_category_tasks_process);
									$this->db->delete('tblcategory_tasks_process_child');

									if ($value[$name]) {
										$process_child = $value[$name]['process_child'];
										$approval_standards = $value[$name]['approval_standards'];
										$completion_control_standards = $value[$name]['completion_control_standards'];
										$role_processing = $value[$name]['role_processing'];

										foreach ($process_child as $vv => $nn) {
											if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($completion_control_standards[$vv])) {
												$roleProcessing = NULL;
												if (!empty($role_processing[$vv])) {
													if (empty($arrayRoleProcessing[$role_processing[$vv]])) {
														$this->db->select('tblroles.roleid as roleid', false);
														$this->db->from('tblroles');
														$this->db->where('tblroles.code_role', $role_processing[$vv]);
														$this->db->where('tblroles.active_role', 1);
														$dtRoleProcessing = $this->db->get()->row_array();
														if (!empty($dtRoleProcessing)) {
															$roleProcessing = $dtRoleProcessing['roleid'];
															$arrayRoleProcessing[$role_processing[$vv]] = $roleProcessing;
														}
													} else {
														$roleProcessing = $arrayRoleProcessing[$role_processing[$vv]];
													}
												}

												$success = $this->db->insert('tblcategory_tasks_process_child', [
													'id_category_tasks' => $id,
													'id_category_tasks_process' => $id_category_tasks_process,
													'name' => $nn,
													'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
													'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
													'role_processing' => !empty($roleProcessing) ? $roleProcessing : NULL,
												]);
											}
										}
									}
									$this->db->where('id_category_tasks', $id);
									$this->db->where_not_in('id', $category_tasks_process_array);
									$this->db->delete('tblcategory_tasks_process');
									$this->db->where('id_category_tasks', $id);
									$this->db->where_not_in('id_category_tasks_process', $category_tasks_process_array);
									$this->db->delete('tblcategory_tasks_process_child');
								}
							}
						} else {
							$isExistProcessName = $this->isExistProcessName($id, $process);
							if (!$isExistProcessName) {
								$id_stages = 0;
								// if (!empty($stages)) {
								// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages), '', 'row_array');
								// 	if (!empty($check_stages)) {
								// 		$id_stages = $check_stages['id'];
								// 	}
								// }
								$success = $this->db->insert('tblcategory_tasks_process', [
									'id_category_tasks' => $id,
									'name' => $process,
									'stages' => $id_stages,
									'kpi_plus' => isset($value[$process]['kpi_plus']) ? $value[$process]['kpi_plus'] : 0,
									'kpi_minus' => isset($value[$process]['kpi_minus']) ? $value[$process]['kpi_minus'] : 0,
								]);
								$id_category_tasks_process = $this->db->insert_id();
							} else {
								// $idd = $this->isExistProcessName($id, $name);
								$id_category_tasks_process = $isExistProcessName;
								$id_stages = 0;
								// if (!empty($stages)) {
								// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages), '', 'row_array');
								// 	if (!empty($check_stages)) {
								// 		$id_stages = $check_stages['id'];
								// 	}
								// }
								$this->db->where('id_category_tasks', $id);
								$this->db->where('name', $process);
								$success = $this->db->update('tblcategory_tasks_process', [
									'stages' => $id_stages,
									'kpi_plus' => isset($value[$process]['kpi_plus']) ? $value[$process]['kpi_plus'] : 0,
									'kpi_minus' => isset($value[$process]['kpi_minus']) ? $value[$process]['kpi_minus'] : 0,
								]);
							}
							if ($value[$process]) {
								$process_child = $value[$process]['process_child'];
								$approval_standards = $value[$process]['approval_standards'];
								$completion_control_standards = $value[$process]['completion_control_standards'];
								$role_processing = $value[$process]['role_processing'];
								foreach ($process_child as $vv => $nn) {
									if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($approval_standards[$vv])) {
										$roleProcessing = NULL;
										if (!empty($role_processing[$vv])) {
											if (empty($arrayRoleProcessing[$role_processing[$vv]])) {
												$this->db->select('tblroles.roleid as roleid', false);
												$this->db->from('tblroles');
												$this->db->where('tblroles.code_role', $role_processing[$vv]);
												$this->db->where('tblroles.active_role', 1);
												$dtRoleProcessing = $this->db->get()->row_array();
												if (!empty($dtRoleProcessing)) {
													$roleProcessing = $dtRoleProcessing['roleid'];
													$arrayRoleProcessing[$role_processing[$vv]] = $roleProcessing;
												}
											} else {
												$roleProcessing = $arrayRoleProcessing[$role_processing[$vv]];
											}
										}
										$success = $this->db->insert('tblcategory_tasks_process_child', [
											'id_category_tasks' => $id,
											'tblcategory_tasks_process_child' => $id_category_tasks_process,
											'name' => $nn,
											'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
											'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
											'role_processing' => !empty($roleProcessing) ? $roleProcessing : NULL,
										]);
									}
								}
							}
						}
					}
					if (!empty($success)) {
						$count++;
					}
				} else { //insert
					$options = [
						'code' => $value['code'],
                        'code_new' => $code_new,
                        'code_old' => $value['code'],
						'departments' => $departmentId,
						'content' => $value['content'],
						'time' => $value['time'],
						'create_by' => get_staff_user_id(),
						'date_create' => date('Y-m-d H:i:s'),
						'type' => $type,
						'role_id_1' => $role_id_1,
						'role_id_2' => $role_id_2,
					];
					$success = null;
					$success = $this->db->insert('tblcategory_tasks', $options);
					if (!empty($success)) {
						$id = $this->db->insert_id();
						$process = $value['process'];
						if (!empty($process)) {
							if (is_array($process)) {
								foreach ($process as $key => $name) {
									if (!empty($name)) {
										$id_stages = 0;
										// if (!empty($stages[$key])) {
										// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages[$key]), '', 'row_array');
										// 	if (!empty($check_stages)) {
										// 		$id_stages = $check_stages['id'];
										// 	}
										// }
										$this->db->insert('tblcategory_tasks_process', [
											'id_category_tasks' => $id,
											'name' => $name,
											'stages' => $id_stages,
											'kpi_plus' => isset($value[$name]['kpi_plus']) ? $value[$name]['kpi_plus'] : 0,
											'kpi_minus' => isset($value[$name]['kpi_minus']) ? $value[$name]['kpi_minus'] : 0,
										]);
										$id_category_tasks_process = $this->db->insert_id();
										$this->db->where('id_category_tasks_process', $id_category_tasks_process);
										$this->db->delete('tblcategory_tasks_process_child');
										if ($value[$name]) {
											$process_child = $value[$name]['process_child'];
											$approval_standards = $value[$name]['approval_standards'];
											$completion_control_standards = $value[$name]['completion_control_standards'];
											$role_processing = $value[$name]['role_processing'];

											foreach ($process_child as $vv => $nn) {
												if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($completion_control_standards[$vv])) {
													$roleProcessing = NULL;
													if (!empty($role_processing[$vv])) {
														if (empty($arrayRoleProcessing[$role_processing[$vv]])) {
															$this->db->select('tblroles.roleid as roleid', false);
															$this->db->from('tblroles');
															$this->db->where('tblroles.code_role', $role_processing[$vv]);
															$this->db->where('tblroles.active_role', 1);
															$dtRoleProcessing = $this->db->get()->row_array();
															if (!empty($dtRoleProcessing)) {
																$roleProcessing = $dtRoleProcessing['roleid'];
																$arrayRoleProcessing[$role_processing[$vv]] = $roleProcessing;
															}
														} else {
															$roleProcessing = $arrayRoleProcessing[$role_processing[$vv]];
														}
													}

													$success = $this->db->insert('tblcategory_tasks_process_child', [
														'id_category_tasks' => $id,
														'id_category_tasks_process' => $id_category_tasks_process,
														'name' => $nn,
														'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
														'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
														'role_processing' => !empty($roleProcessing) ? $roleProcessing : NULL,
													]);
												}
											}
										}
									}
								}
							} else {
								$isExistProcessName = $this->isExistProcessName($id, $process);
								if (!$isExistProcessName) {
									$id_stages = 0;
									// if (!empty($stages)) {
									// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages), '', 'row_array');
									// 	if (!empty($check_stages)) {
										// 		$id_stages = $check_stages['id'];
									// 	}
									// }
									$success = $this->db->insert('tblcategory_tasks_process', [
										'id_category_tasks' => $id,
										'name' => $process,
										'stages' => $id_stages,
										'kpi_plus' => isset($value[$process]['kpi_plus']) ? $value[$process]['kpi_plus'] : 0,
										'kpi_minus' => isset($value[$process]['kpi_minus']) ? $value[$process]['kpi_minus'] : 0,
									]);
									$id_category_tasks_process = $this->db->insert_id();
									$this->db->where('id_category_tasks_process', $id_category_tasks_process);
									$this->db->delete('tblcategory_tasks_process_child');
									if ($value[$name]) {
										$process_child = $value[$name]['process_child'];
										$approval_standards = $value[$name]['approval_standards'];
										$completion_control_standards = $value[$name]['completion_control_standards'];
										$role_processing = $value[$name]['role_processing'];

										foreach ($process_child as $vv => $nn) {
											if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($completion_control_standards[$vv])) {
												$roleProcessing = NULL;
												if (!empty($role_processing[$vv])) {
													if (empty($arrayRoleProcessing[$role_processing[$vv]])) {
														$this->db->select('tblroles.roleid as roleid', false);
														$this->db->from('tblroles');
														$this->db->where('tblroles.code_role', $role_processing[$vv]);
														$this->db->where('tblroles.active_role', 1);
														$dtRoleProcessing = $this->db->get()->row_array();
														if (!empty($dtRoleProcessing)) {
															$roleProcessing = $dtRoleProcessing['roleid'];
															$arrayRoleProcessing[$role_processing[$vv]] = $roleProcessing;
														}
													} else {
														$roleProcessing = $arrayRoleProcessing[$role_processing[$vv]];
													}
												}
												$success = $this->db->insert('tblcategory_tasks_process_child', [
													'id_category_tasks' => $id,
													'id_category_tasks_process' => $id_category_tasks_process,
													'name' => $nn,
													'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
													'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
													'role_processing' => !empty($roleProcessing) ? $roleProcessing : NULL,
												]);
											}
										}
									}
								} else {
									// $idd = $this->isExistProcessName($id, $process);
									$id_stages = 0;
									if (!empty($stages)) {
										$check_stages = get_table_where('tbl_stages', array('code' => $stages), '', 'row_array');
										if (!empty($check_stages)) {
											$id_stages = $check_stages['id'];
										}
									}
									$this->db->where('id_category_tasks', $id);
									$this->db->where('name', $process);
									$success = $this->db->update('tblcategory_tasks_process', [
										'stages' => $id_stages,
										'kpi_plus' => isset($value[$process]['kpi_plus']) ? $value[$process]['kpi_plus'] : 0,
										'kpi_minus' => isset($value[$process]['kpi_minus']) ? $value[$process]['kpi_minus'] : 0,
									]);
									$id_category_tasks_process = $isExistProcessName;
									$this->db->where('id_category_tasks_process', $id_category_tasks_process);
									$this->db->delete('tblcategory_tasks_process_child');
									if ($value[$name]) {
										$process_child = $value[$name]['process_child'];
										$approval_standards = $value[$name]['approval_standards'];
										$completion_control_standards = $value[$name]['completion_control_standards'];
										$role_processing = $value[$name]['role_processing'];

										foreach ($process_child as $vv => $nn) {
											if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($completion_control_standards[$vv])) {
												$roleProcessing = NULL;
												if (!empty($role_processing[$vv])) {
													if (empty($arrayRoleProcessing[$role_processing[$vv]])) {
														$this->db->select('tblroles.roleid as roleid', false);
														$this->db->from('tblroles');
														$this->db->where('tblroles.code_role', $role_processing[$vv]);
														$this->db->where('tblroles.active_role', 1);
														$dtRoleProcessing = $this->db->get()->row_array();
														if (!empty($dtRoleProcessing)) {
															$roleProcessing = $dtRoleProcessing['roleid'];
															$arrayRoleProcessing[$role_processing[$vv]] = $roleProcessing;
														}
													} else {
														$roleProcessing = $arrayRoleProcessing[$role_processing[$vv]];
													}
												}
												$success = $this->db->insert('tblcategory_tasks_process_child', [
													'id_category_tasks' => $id,
													'id_category_tasks_process' => $id_category_tasks_process,
													'name' => $nn,
													'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
													'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
													'role_processing' => !empty($roleProcessing) ? $roleProcessing : NULL,
												]);
											}
										}
									}
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

	public function getIdByCode($code)
	{
		$this->db->select('tblcategory_tasks.id');
		$this->db->from('tblcategory_tasks');
		$this->db->where('tblcategory_tasks.code', $code);
		$result = $this->db->get()->row();
		if (empty($result)) {
			return 0;
		} else {
			return $result->id;
		}
	}

	public function getIdByCodeContent($code, $content)
	{
		$content = str_replace(["\r", "\n"], '', $content);
		$code = str_replace(["\r", "\n"], '', $code);

		$this->db->select('tblcategory_tasks.id');
		$this->db->from('tblcategory_tasks');
		$this->db->where('tblcategory_tasks.code', trim($code));
		$this->db->where("REPLACE(REPLACE(tblcategory_tasks.content, '\r', ''), '\n', '') = '" . $content . "'", false, false);
		$result = $this->db->get()->row();
		if (empty($result)) {
			return 0;
		} else {
			return $result->id;
		}
	}

	public function isExistProcessName($id_category_tasks, $name)
	{
		$this->db->from('tblcategory_tasks_process');
		$this->db->where('tblcategory_tasks_process.id_category_tasks', $id_category_tasks);
		$this->db->where('tblcategory_tasks_process.name', $name);
		$this->db->limit(1);
		$result = $this->db->get()->row();
		if (empty($result)) {
			return false;
		} else {
			return $result->id;
		}
	}

	public function modal_import_update()
	{
		$data['title'] = _l('Import update mã công việc');
		$this->load->view('admin/category_tasks/import_update', $data);
	}

	public function importUpdate()
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
			$process = [];
			$approval_standards = [];
			$completion_control_standards = [];
			$listRow = [
				1 => 'departments',
				2 => 'process',
				3 => 'process_child',
				4 => 'approval_standards',
				5 => 'completion_control_standards',
			];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				$vaKeyprocess = '';
				for ($i = 2; $i <= $highestRow; $i++) {
					$redata = [];
					for ($j = 1; $j < $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						if ($j == 1) {
							if (!empty($Val)) {
								$vaKey = $Val;
								$process = [];
								$stages = [];
							}
						} else if ($j == 2) {
							$process[] = $Val;
							if (!empty($Val)) {
								$vaKeyprocess = $Val;
								$process_child = [];
								$approval_standards = [];
								$completion_control_standards = [];
							}
						} else if ($j == 3) {
							$process_child[] = $Val;
						} else if ($j == 4) {
							$approval_standards[] = $Val;
						} else if ($j == 5) {
							$completion_control_standards[] = $Val;
						}

						$redata[$listRow[$j]] = trim($Val);
					}
					if (!empty($vaKey)) {
						if (!empty($data[$vaKey])) {
							if (!empty($vaKeyprocess)) {
								$data[$vaKey][$vaKeyprocess]['process_child'] = $process_child;
								$data[$vaKey][$vaKeyprocess]['approval_standards'] = $approval_standards;
								$data[$vaKey][$vaKeyprocess]['completion_control_standards'] = $completion_control_standards;
							}
							$data[$vaKey]['process'] = $process;
						} else {
							$data[$vaKey] = $redata;
						}
					}
				}
			}
		}

		$count = 0;
		if (!empty($data)) {
			// var_dump($data);die;
			foreach ($data as $value) {
				$departmentId = $this->getDepartmentIdByName($value['departments']);
				$arr_id = $this->getIdByDepartment($departmentId);
				foreach ($arr_id as $key => $dbValue) {
					$id = $dbValue['id'];
					if (!empty($id)) { // update
						$process = $value['process'];
						if (!empty($process)) {
							if (is_array($process)) {
								foreach ($process as $name) {
									if (!empty($name)) {
										$isExistProcessName = $this->isExistProcessName($id, $name);
										if (!$isExistProcessName) {
											$id_stages = 0;
											// if (!empty($stages)) {
											// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages), '', 'row_array');
											// 	if (!empty($check_stages)) {
											// 		$id_stages = $check_stages['id'];
											// 	}
											// }
											$this->db->insert('tblcategory_tasks_process', [
												'id_category_tasks' => $id,
												'name' => $name,
												'stages' => $id_stages
											]);
											$id_category_tasks_process = $this->db->insert_id();
											$this->db->where('id_category_tasks_process', $id_category_tasks_process);
											$this->db->delete('tblcategory_tasks_process_child');
											if ($value[$name]) {
												$process_child = $value[$name]['process_child'];
												$approval_standards = $value[$name]['approval_standards'];
												$completion_control_standards = $value[$name]['completion_control_standards'];

												foreach ($process_child as $vv => $nn) {
													if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($approval_standards[$vv])) {
														$success = $this->db->insert('tblcategory_tasks_process_child', [
															'id_category_tasks' => $id,
															'id_category_tasks_process' => $id_category_tasks_process,
															'name' => $nn,
															'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
															'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
														]);
													}
												}
											}
										} else {
											$id_category_tasks_process = $isExistProcessName;
											$this->db->where('id_category_tasks_process', $id_category_tasks_process);
											$this->db->delete('tblcategory_tasks_process_child');
											if ($value[$name]) {
												$process_child = $value[$name]['process_child'];
												$approval_standards = $value[$name]['approval_standards'];
												$completion_control_standards = $value[$name]['completion_control_standards'];

												foreach ($process_child as $vv => $nn) {
													if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($approval_standards[$vv])) {
														$success = $this->db->insert('tblcategory_tasks_process_child', [
															'id_category_tasks' => $id,
															'id_category_tasks_process' => $id_category_tasks_process,
															'name' => $nn,
															'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
															'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
														]);
													}
												}
											}
										}
									}
								}
							} else {
								$isExistProcessName = $this->isExistProcessName($id, $process);
								if (!$isExistProcessName) {
									$id_stages = 0;
									// if (!empty($stages)) {
									// 	$check_stages = get_table_where('tbl_stages', array('code' => $stages), '', 'row_array');
									// 	if (!empty($check_stages)) {
									// 		$id_stages = $check_stages['id'];
									// 	}
									// }
									$this->db->insert('tblcategory_tasks_process', [
										'id_category_tasks' => $id,
										'name' => $process,
										'stages' => $id_stages
									]);
									$id_category_tasks_process = $this->db->insert_id();
									$this->db->where('id_category_tasks_process', $id_category_tasks_process);
									$this->db->delete('tblcategory_tasks_process_child');
									if ($value[$process]) {
										$process_child = $value[$process]['process_child'];
										$approval_standards = $value[$process]['approval_standards'];
										$completion_control_standards = $value[$process]['completion_control_standards'];

										foreach ($process_child as $vv => $nn) {
											if (!empty($nn) || !empty($approval_standards[$vv])  || !empty($approval_standards[$vv])) {
												$success = $this->db->insert('tblcategory_tasks_process_child', [
													'id_category_tasks' => $id,
													'id_category_tasks_process' => $id_category_tasks_process,
													'name' => $nn,
													'approval_standards' => !empty($approval_standards[$vv]) ? $approval_standards[$vv] : '',
													'completion_control_standards' => !empty($completion_control_standards[$vv]) ? $completion_control_standards[$vv] : '',
												]);
											}
										}
									}
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

	public function getIdByDepartment($departmentId)
	{
		$this->db->select('tblcategory_tasks.id, tblcategory_tasks.departments');
		$this->db->from('tblcategory_tasks');
		$this->db->like('tblcategory_tasks.departments', $departmentId);
		$result = $this->db->get()->result_array();
		$unset = [];
		foreach ($result as $key => $value) {
			$unset[] = $key;
			$departments = $value['departments'];
			$arrDepartments = explode(",", $departments);
			foreach ($arrDepartments as $k => $v) {
				if ($v == $departmentId) {
					unset($unset[$key]);
				}
			}
		}
		foreach ($unset as $key => $val) {
			unset($result[$key]);
		}
		return $result;
	}

	// public function getDepartmentIdByName($name)
	// {
	// 	if (empty($name)) {
	// 		return 0;
	// 	}
	// 	$this->db->select('tbldepartments.departmentid');
	// 	$this->db->from('tbldepartments');
	// 	$this->db->group_start();
	// 	$this->db->where('tbldepartments.name', $name);
	// 	$this->db->or_where('tbldepartments.code', $name);
	// 	$this->db->group_end();
	// 	$result = $this->db->get()->row();
	// 	if (empty($result)) {
	// 		return 0;
	// 	} else {
	// 		return $result->departmentid;
	// 	}
	// }
	public function getDepartmentIdByName($name)
	{
		if (empty($name)) {
			return 0;
		}
		$this->db->select('tbl_room.id');
		$this->db->from('tbl_room');
		$this->db->group_start();
		$this->db->where('tbl_room.name', $name);
		$this->db->or_where('tbl_room.code', $name);
		$this->db->group_end();
		$result = $this->db->get()->row();
		if (empty($result)) {
			return 0;
		} else {
			return $result->id;
		}
	}
	public function getRoleParent()
	{
		$data = [];
		$department_id = $this->input->post('department_id');

		$this->db->select('
			tblroles.roleid as roleid,
			tblroles.name as name,
		', false);
		$this->db->from('tblroles');
		$this->db->where('tblroles.departments_id', $department_id);
		$roles = $this->db->get()->result_array();
		$data['roles'] = $roles;
		echo json_encode($data);
	}

	public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
	{
		if (is_numeric($level)) {
			$this->db->where(array('roles_parent' => $id_parent));
			$current_level = $this->db->get('tblroles')->result_array();
			if ($current_level) {
				foreach ($current_level as $key => $value) {
					$sub = "";
					for ($i = 0; $i < $level; $i++) {
						$sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					$sub .= "&#10154;";
					$current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
					array_push($array_category, $current_level[$key]);
					$this->get_parent($value['roleid'], $array_category, $level + 1);
				}
			} else {
				return;
			}
		}
	}


	public function export_excel()
	{
		ini_set('memory_limit', '3500M');
		ini_set('max_execution_time', 800);

		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');

		$style_excel   = style_excel();
		$cloumns_excel = cloumns_excel();

		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Template_ma_cong_viec');

		// Column widths: 17 columns A-Q
		$widths = [5, 22, 18, 35, 22, 15, 42, 15, 15, 38, 38, 38, 13, 10, 15, 12, 20];
		foreach ($widths as $k => $w) {
			$sheet->getColumnDimension($cloumns_excel[$k])->setWidth($w);
		}

		$numberRow = 1;

		// === ROW 1: Header tên cột ===
		$headers = [
			'STT',
			'TÊN PHÒNG BAN',
			'MÃ CÔNG VIỆC*',
			'TÊN CÔNG VIỆC',
			'CHỨC VỤ PHÒNG BAN',
			'MÃ VỊ TRÍ',
			'QUY TRÌNH',
			'KPI +',
			'KPI -',
			'Quy Chuẩn Công Việc',
			'Quy Chuẩn Duyệt',
			'Quy Chuẩn Kiểm Soát Hoàn Thành',
			'ĐỊNH MỨC (PHÚT)',
			'Loại CV',
			'NGÀY BAN HÀNH',
			"SỬ DỤNG\n(0 Là chưa sử dụng, 1 là sử dụng, 2 là ngưng sử dụng)",
			'Mã Vị trí Duyệt',
		];

		foreach ($headers as $k => $v) {
			$col = $cloumns_excel[$k];
			$sheet->setCellValue($col . $numberRow, $v)
				->getStyle($col . $numberRow)
				->applyFromArray($style_excel['Background_header']);
			$sheet->getStyle($col . $numberRow)->getAlignment()->setWrapText(true);
		}
		$sheet->getRowDimension($numberRow)->setRowHeight(42);
		$numberRow++;

		// === ROW 2: Số thứ tự cột ===
		foreach ($headers as $k => $v) {
			$col = $cloumns_excel[$k];
			$sheet->setCellValue($col . $numberRow, $k + 1)
				->getStyle($col . $numberRow)
				->applyFromArray($style_excel['c_th']);
		}
		$numberRow++;

		// === Áp dụng bộ lọc từ giao diện ===
		$statusTable  = $this->input->post('status_table');   // room id hoặc 'all'
		$roleSearch   = $this->input->post('role_search');    // mảng roleid

		$whereClauses = ['1=1'];

		// Lọc theo phòng ban (tab)
		if (!empty($statusTable) && $statusTable !== 'all') {
			$whereClauses[] = 'ct.departments = ' . (int)$statusTable;
		}

		// Lọc theo chức vụ/vị trí (role_search[])
		if (!empty($roleSearch) && is_array($roleSearch)) {
			$roleIds = implode(',', array_map('intval', $roleSearch));
			$whereClauses[] = "(ct.role_id_1 IN ({$roleIds}) OR ct.role_id_2 IN ({$roleIds}))";
		}
		$whereClauses[] = 'ct.hide = 0';

		$whereStr = implode(' AND ', $whereClauses);

		// === Lấy dữ liệu ===
		$sql = "
			SELECT
				ct.id,
				ct.code,
				ct.content,
				ct.departments,
				ct.time,
				ct.type,
				ct.date_approve,
				ct.active,
				ct.role_id_1,
				ct.role_id_2,
				r.name AS room_name,
				rl1.name_position AS role1_name,
				rl2.code_role AS role2_code
			FROM tblcategory_tasks ct
			LEFT JOIN tbl_room r ON r.id = ct.departments
			LEFT JOIN tblroles rl1 ON rl1.roleid = ct.role_id_1
			LEFT JOIN tblroles rl2 ON rl2.roleid = ct.role_id_2
			WHERE {$whereStr}
			ORDER BY r.name ASC, ct.code ASC
		";
		$tasks = $this->db->query($sql)->result_array();

		if (empty($tasks)) {
			echo json_encode(['result' => 0, 'message' => 'Không có dữ liệu!']);
			die;
		}

		// === PRELOAD: Lấy toàn bộ processes 1 lần (thay vì query từng task) ===
		$taskIds = array_column($tasks, 'id');
		$taskIdsStr = implode(',', array_map('intval', $taskIds));

		$allProcesses = $this->db->query(
			"SELECT * FROM tblcategory_tasks_process
			 WHERE id_category_tasks IN ({$taskIdsStr})
			 ORDER BY id ASC"
		)->result_array();

		// Group processes theo task id => O(1) lookup
		$processMap = [];
		$processIds = [];
		foreach ($allProcesses as $p) {
			$processMap[(int)$p['id_category_tasks']][] = $p;
			$processIds[] = (int)$p['id'];
		}

		// === PRELOAD: Lấy toàn bộ children 1 lần ===
		$childMap = [];
		$roleProcessingIds = [];
		if (!empty($processIds)) {
			$processIdsStr = implode(',', $processIds);
			$allChildren = $this->db->query(
				"SELECT * FROM tblcategory_tasks_process_child
				 WHERE id_category_tasks_process IN ({$processIdsStr})
				 ORDER BY id ASC"
			)->result_array();
			foreach ($allChildren as $c) {
				$childMap[(int)$c['id_category_tasks_process']][] = $c;
				if (!empty($c['role_processing'])) {
					$roleProcessingIds[] = (int)$c['role_processing'];
				}
			}
		}

		// === PRELOAD: Lấy toàn bộ role codes 1 lần ===
		$roleCodeCache = [];
		if (!empty($roleProcessingIds)) {
			$roleProcessingIds = array_unique($roleProcessingIds);
			$rIdsStr = implode(',', $roleProcessingIds);
			$roleRows = $this->db->query(
				"SELECT roleid, code_role FROM tblroles WHERE roleid IN ({$rIdsStr})"
			)->result_array();
			foreach ($roleRows as $rr) {
				$roleCodeCache[(int)$rr['roleid']] = $rr['code_role'];
			}
		}

		// === Xuất dữ liệu ra Excel ===
		$typeMap = [1 => 'Ngày', 2 => 'Tháng', 3 => 'Năm'];
		$stt = 1;

		foreach ($tasks as $task) {
			$processes = isset($processMap[(int)$task['id']]) ? $processMap[(int)$task['id']] : [];

			$typeLabel   = isset($typeMap[(int)$task['type']]) ? $typeMap[(int)$task['type']] : '';
			$dateApprove = !empty($task['date_approve']) ? date('d/m/Y', strtotime($task['date_approve'])) : '';

			$isFirstRowOfTask = true;

			if (empty($processes)) {
				// Công việc không có quy trình → 1 row
				$cells = [
					$stt,
					$task['room_name'],
					$task['code'],
					$task['content'],
					$task['role1_name'],
					$task['role2_code'],
					'',
					'',
					'',
					'',
					'',
					'',
					$task['time'],
					$typeLabel,
					$dateApprove,
					$task['active'],
					''
				];
				$this->_writeExcelRow($sheet, $style_excel, $cloumns_excel, $numberRow, $cells, true);
				$numberRow++;
				$stt++;
				continue;
			}

			foreach ($processes as $process) {
				$children = isset($childMap[(int)$process['id']]) ? $childMap[(int)$process['id']] : [];

				if (empty($children)) {
					// Quy trình không có công đoạn con
					$cells = [
						$isFirstRowOfTask ? $stt : '',
						$isFirstRowOfTask ? $task['room_name'] : '',
						$isFirstRowOfTask ? $task['code'] : '',
						$isFirstRowOfTask ? $task['content'] : '',
						$isFirstRowOfTask ? $task['role1_name'] : '',
						$isFirstRowOfTask ? $task['role2_code'] : '',
						$process['name'],
						$process['kpi_plus'],
						$process['kpi_minus'],
						'',
						'',
						'',
						$isFirstRowOfTask ? $task['time'] : '',
						$isFirstRowOfTask ? $typeLabel : '',
						$isFirstRowOfTask ? $dateApprove : '',
						$isFirstRowOfTask ? $task['active'] : '',
						''
					];
					$this->_writeExcelRow($sheet, $style_excel, $cloumns_excel, $numberRow, $cells, $isFirstRowOfTask);
					if ($isFirstRowOfTask) {
						$stt++;
						$isFirstRowOfTask = false;
					}
					$numberRow++;
				} else {
					$isFirstChild = true;
					foreach ($children as $child) {
						$roleCode = '';
						if (!empty($child['role_processing'])) {
							$rId = (int)$child['role_processing'];
							$roleCode = isset($roleCodeCache[$rId]) ? $roleCodeCache[$rId] : '';
						}

						$isMain = $isFirstRowOfTask && $isFirstChild;
						$cells = [
							$isMain ? $stt : '',
							$isMain ? $task['room_name'] : '',
							$isMain ? $task['code'] : '',
							$isMain ? $task['content'] : '',
							$isMain ? $task['role1_name'] : '',
							$isMain ? $task['role2_code'] : '',
							$isFirstChild ? $process['name'] : '',
							$isFirstChild ? $process['kpi_plus'] : '',
							$isFirstChild ? $process['kpi_minus'] : '',
							$child['name'],
							$child['approval_standards'],
							$child['completion_control_standards'],
							$isMain ? $task['time'] : '',
							$isMain ? $typeLabel : '',
							$isMain ? $dateApprove : '',
							$isMain ? $task['active'] : '',
							$roleCode,
						];
						$this->_writeExcelRow($sheet, $style_excel, $cloumns_excel, $numberRow, $cells, $isMain);
						if ($isMain) {
							$stt++;
							$isFirstRowOfTask = false;
						}
						$isFirstChild = false;
						$numberRow++;
					}
				}
			}
		}

		$filename = 'ma_cong_viec.xls';
		ob_start();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$writer->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();

		echo json_encode([
			'result'   => 1,
			'filename' => $filename,
			'message'  => 'Xuất dữ liệu thành công!',
			'file'     => 'data:application/vnd.ms-excel;base64,' . base64_encode($xlsData)
		]);
		die;
	}

	private function _writeExcelRow($sheet, $style_excel, $cloumns_excel, $rowNum, $cells, $isMain)
	{
		$styleName = $isMain ? 'BStyle_center' : 'BStyle_center';
		foreach ($cells as $i => $cell) {
			$col = $cloumns_excel[$i];
			$sheet->setCellValue($col . $rowNum, $cell)
				->getStyle($col . $rowNum)
				->applyFromArray($style_excel[$styleName]);
			// Wrap text cho cột nội dung dài
			if (in_array($i, [3, 6, 9, 10, 11])) {
				$sheet->getStyle($col . $rowNum)->getAlignment()->setWrapText(true);
			}
		}
		// Tô màu xám nhạt cho dòng phụ (không phải dòng đầu của task)
		if (!$isMain) {
			$firstCol = $cloumns_excel[0];
			$lastCol  = $cloumns_excel[count($cells) - 1];
			$sheet->getStyle($firstCol . $rowNum . ':' . $lastCol . $rowNum)
				->getFill()
				->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setRGB('F5F5F5');
		}
	}


	public function test()
	{
		$result = [];
		get_childs_id_role(0, $result);
		print_arrays($result);
	}
}
