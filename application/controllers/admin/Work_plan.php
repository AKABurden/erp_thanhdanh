<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Work_plan extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('work_plan_model');
		$this->perViewWorkPlan = has_permission('work_plan', '', 'view');
		$this->perEditWorkPlan = has_permission('work_plan', '', 'edit');
	}

	// public function index()
	// {
	//     $data['title'] = lang('tnh_work_plan');
	//     $this->load->view('admin/work_plan/index', $data);
	// }
	public function handling($id = 0)
	{
		if (!$this->perViewWorkPlan) {
			accessDenied();
		}
		$work_plan = $this->work_plan_model->getWorkPlanById($id);
		if ($this->input->post()) {
			// echo '<pre>';
			// var_dump($this->input->post());die;
			$data = [];
			if (!$this->perEditWorkPlan) {
				$data['result'] = 0;
				$data['message'] = lang('Bạn không có quyền lưu');
				echo json_encode($data);
				die;
			}
			$month = $this->input->post('month');
			$year = $this->input->post('year');
			$content = $this->input->post('content');
			if (empty($month) || empty($year) || empty($content)) {
				$data['result'] = 0;
				$data['message'] = lang('Vui lòng nhập các trường bắt buộc');
				echo json_encode($data);
				die;
			}
			$id = $this->input->post('id');
			$created_by = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$main_item = $this->input->post('main_item');
			$items = $this->input->post('items');
			// $arrItems = [];
			// if (!empty($items)) {
			//     foreach ($items as $key => $value) {
			//         if (empty($value['name'])) {
			//             continue;
			//         }
			//         $priority_level = number_unformat($value['priority_level']);
			//         $staffs = !empty($value['staffs']) ? $value['staffs'] : null;
			//         $manage_reports = !empty($value['manage_reports']) ? $value['manage_reports'] : null;
			//         $arrItems[] = [
			//             'id' => !empty($value['work_plan_items_id']) ? $value['work_plan_items_id'] : 0,
			//             'type' => $value['type'],
			//             'name' => $value['name'],
			//             'week_one' => $value['week_one'],
			//             'week_two' => $value['week_two'],
			//             'week_three' => $value['week_three'],
			//             'week_four' => $value['week_four'],
			//             'priority_level' => $priority_level,
			//             'process' => $value['process'],
			//             'staffs' => $staffs,
			//             'manage_reports' => $manage_reports,
			//             'number' => $value['number'],
			//         ];
			//     }
			// }
			if (empty($items)) {
				$data['result'] = 0;
				$data['message'] = lang('Không có công việc để lưu');
				echo json_encode($data);
				die;
			}
			$option = [
				'month' => $month,
				'year' => $year,
				'content' => $content,
			];
			$isSuccess = false;
			if ($id) {
				$option['date_updated'] = $date;
				$option['updated_by'] = $created_by;
				if (isset($data['date_start'])) {
					$option['date_start'] = to_sql_date($data['date_start']);
				}
				if (isset($data['date_end'])) {
					$option['date_end'] = to_sql_date($data['date_end']);
				}
				$up = $this->work_plan_model->updateWorkPlan($id, $option);
				if ($up) {
					$work_plan_id = $id;
					$isSuccess = true;
				}
			} else {
				$option['date_created'] = $date;
				$option['created_by'] = $created_by;
				if (isset($data['date_start'])) {
					$option['date_start'] = to_sql_date($data['date_start']);
				}
				if (isset($data['date_end'])) {
					$option['date_end'] = to_sql_date($data['date_end']);
				}
				$work_plan_id = $this->work_plan_model->insertWorkPlan($option);
				if ($work_plan_id) {
					$isSuccess = true;
				}
			}
			if ($isSuccess) {
				if ($id) {
					$this->work_plan_model->deleteWorkPlanITask($id);
					$this->work_plan_model->deleteWorkPlanItems($id);
					$this->work_plan_model->deleteWorkPlanItemsStaffs($id);
				}
				if (!empty($main_item)) {
					foreach ($main_item as $key => $main_item_value) {
						$work_plan_task_data = [
							'id' => !empty($main_item_value['id']) ? $main_item_value['id'] : null,
							'work_plan_id' => $work_plan_id,
							'category_task_id' => $main_item_value['category_task'],
							'branch_id' => $main_item_value['branch'],
							'content' => $main_item_value['content'],
							'staff_assigner' => (!empty($main_item_value['staff_assigner']) ? $main_item_value['staff_assigner'] : null),
							'staff_assigned' => (!empty($main_item_value['staff_assigned']) ? $main_item_value['staff_assigned'] : null),
							'staff_monitor' => (!empty($main_item_value['staff_monitor']) ? $main_item_value['staff_monitor'] : null),
							'date_tasks' => (!empty($main_item_value['date_tasks']) ? to_sql_date($main_item_value['date_tasks']) : null),
							'date_start' => (!empty($main_item_value['date_start']) ? to_sql_date($main_item_value['date_start']) : null),
							'date_end' => (!empty($main_item_value['date_end']) ? to_sql_date($main_item_value['date_end']) : null),
						];
						$this->db->insert('tbl_work_plan_task', $work_plan_task_data);
						$work_plan_task_id = $this->db->insert_id();
						// $data['result'] = 0;
						// $data['message'] = $work_plan_task_id;
						// echo json_encode($data); die;
						if ($work_plan_task_id) {
							if (!empty($items[$key])) {
								foreach ($items[$key] as $value) {
									$work_plan_items_data = [
										'id' => !empty($value['work_plan_items_id']) ? $value['work_plan_items_id'] : null,
										'work_plan_id' => $work_plan_id,
										// 'type' => $value['type'],
										// 'name' => $value['name'],
										'process_id' => $value['process'],
										'staff_id' => $value['staff_id'],
										'week_one' => $value['week_one'],
										'week_two' => $value['week_two'],
										'week_three' => $value['week_three'],
										'week_four' => $value['week_four'],
										'work_plan_task_id' => $work_plan_task_id,
										'category_tasks_process_name' => $value['category_tasks_process_name'],
										'pass_status' => (!empty($value['pass_status']) ? $value['pass_status'] : null),
										'kpi' => isset($value['kpi']) && $value['kpi'] !== '' ? $value['kpi'] : null,
										'kpi_type' => isset($value['kpi_type']) && $value['kpi_type'] !== '' ? $value['kpi_type'] : null,
										'problem' => $value['problem'],
									];
									$this->work_plan_model->insertWorkPlanItems($work_plan_items_data);
								}
								$taskRel = $this->work_plan_model->getTaskRel($work_plan_task_id);
								if (empty($taskRel) && (empty($work_plan_task_data['date_tasks']) || (!empty($work_plan_task_data['date_tasks']) && ($work_plan_task_data['date_tasks'] <= date('Y-m-d'))))) { // Chưa có phiếu công việc
									$this->work_plan_model->createTask($work_plan_task_id);
								}
							}
						}
					}
				}
				$arrWorkPlanItems = [];
				// foreach ($arrItems as $key => $value) {
				//     $staffs = $value['staffs'];
				//     $manage_reports = $value['manage_reports'];
				//     unset($value['staffs']);
				//     unset($value['manage_reports']);
				//     $value['work_plan_id'] = $work_plan_id;
				//     $work_plan_items_id = $this->work_plan_model->insertWorkPlanItems($value);
				//     if (!empty($staffs)) {
				//         foreach ($staffs as $kS => $vS) {
				//             $arrWorkPlanItems[] = [
				//                 'work_plan_id' => $work_plan_id,
				//                 'work_plan_items_id' => $work_plan_items_id,
				//                 'staff_id' => $vS,
				//                 'type_staff' => 1,
				//             ];
				//         }
				//     }
				//     if (!empty($manage_reports)) {
				//         foreach ($manage_reports as $kS => $vS) {
				//             $arrWorkPlanItems[] = [
				//                 'work_plan_id' => $work_plan_id,
				//                 'work_plan_items_id' => $work_plan_items_id,
				//                 'staff_id' => $vS,
				//                 'type_staff' => 2,
				//             ];
				//         }
				//     }
				// }
				if (!empty($arrWorkPlanItems)) {
					$this->work_plan_model->insertBatchWorkPlanItemsStaffs($arrWorkPlanItems);
				}
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
			echo json_encode($data);
			die;
		} else {
			// $data['title'] = $id ? lang('tnh_edit_work_plan') : lang('tnh_add_work_plan');
			$data['title'] = lang('tnh_work_plan');
			$data['category_task'] = get_table_where('tblcategory_tasks', ['hide' => 0], '', 'result_array', '', 'id, code');
			$data['process_work_plan'] = getProcessWorkPlan();
			$data['staffs'] = $this->site_model->getStaffAll();
			// $data['breadcrumb'] = [array('link' => base_url('admin/work_plan'), 'page' => lang('tnh_work_plan')), array('link' => '#', 'page' => $data['title'])];
			$data['work_plan'] = $work_plan;
			$data['arrCategoryTask'] = get_table_where('tblcategory_tasks', ['hide' => 0], '', 'result_array', '', 'id, code');
			$data['arrBranch'] = get_table_where('tblbranch', [], '', 'result_array', '', 'id, name');
			$data['id'] = $id;
			if (empty($work_plan)) {
				$data['limit_date_start'] = date('Y-m-01');
				$data['limit_date_end'] = date('Y-m-t');
			} else {
				$data['limit_date_start'] = $work_plan['year'] . '-' . $work_plan['month'] . '-01';
				$data['limit_date_end'] = date("Y-m-t", strtotime($work_plan['year'] . '-' . $work_plan['month'] . '-01'));
			}
			$this->load->view('admin/work_plan/add', $data);
		}
	}

	public function getWorkPlan()
	{
		die;
		$aColumns = [
			'tbl_work_plan.id as id',
			'tbl_work_plan.month as month',
			'tbl_work_plan.year as year',
			'tbl_work_plan.content as content',
			'tbl_work_plan.date_created as date_created',
			'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
			'"" as actions',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_work_plan';
		$where = [];
		$filter = [];
		$join = [
			'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_work_plan.created_by'
		];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$aColumns = handlingColumns($aColumns);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$start++;
			$work_plan_id = $aRow['id'];
			$view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/work_plan/view_work_plan/' . $work_plan_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view_work_plan') . '</a>';
			$edit = '<a href="' . base_url('admin/work_plan/handling/' . $work_plan_id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_work_plan') . ' </a>';
			$delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/work_plan/delete/' . $work_plan_id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_work_plan') . '</a>';
			$view = '';
			$delete = '';
			$actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
			foreach ($aColumns as $k => $v) {
				if ($v == 'id') {
					$row[] = '<div class="text-center">' . $start . '</div>';
				} else if ($v == 'date_created') {
					$row[] = '<div class="text-center">' . _dt($aRow[$v]) . '</div>';
				} else if ($v == 'actions') {
					$row[] = $actions;
				} else {
					$row[] = '<div class="text-center">' . $aRow[$v] . '</div>';
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function loadWorkPlan()
	{
		$responsive = [];
		$data = [];
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		$dtWorkPlan = $this->work_plan_model->getWorkPlanByMonthYear($month, $year);
		$id = !empty($dtWorkPlan) ? $dtWorkPlan['id'] : 0;
		$work_plan = $this->work_plan_model->getWorkPlanById($id);
		$data['arrCategoryTask'] = get_table_where('tblcategory_tasks', ['hide' => 0], '', 'result_array', '', 'id, code');
		$data['arrBranch'] = get_table_where('tblbranch', [], '', 'result_array', '', 'id, name');
		$data['id'] = $id;
		$data['work_plan'] = $work_plan;
		$data['process_work_plan'] = getProcessWorkPlan();
		$data['staffs'] = $this->site_model->getStaffAll();
		$data['work_plan_task'] = get_table_where('tbl_work_plan_task', ['work_plan_id' => $id]);
		foreach ($data['work_plan_task'] as $key => $value) {
			$data['work_plan_task'][$key]['arr_task_rel'] = $this->work_plan_model->getTaskRel($value['id']);
		}
		if (empty($work_plan)) {
			$data['limit_date_start'] = date('Y-m-01');
			$data['limit_date_end'] = date('Y-m-t');
		} else {
			$data['limit_date_start'] = $work_plan['year'] . '-' . $work_plan['month'] . '-01';
			$data['limit_date_end'] = date("Y-m-t", strtotime($work_plan['year'] . '-' . $work_plan['month'] . '-01'));
		}
		$this->load->view('admin/work_plan/load_work_plan', $data);
	}

	public function import_data_table()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		$data = [];
		if (!empty($_FILES)) {
			foreach ($_FILES as $kFileImport => $vFileImport) {
				$FILE = $_FILES[$kFileImport];
				$fullfile = $FILE['tmp_name'];
				$nameFile = $FILE['name'];
				$extension = strtoupper(pathinfo($FILE['name'], PATHINFO_EXTENSION));
				if ($extension != 'XLSX' && $extension != 'XLS' && $extension != 'csv') {
					echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
					die();
				}
				$inputFileType = PHPExcel_IOFactory::identify($fullfile);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objReader->setReadDataOnly(true);
				$objPHPExcel = @$objReader->load("$fullfile");
				$total_sheets = $objPHPExcel->getSheetCount();
				$allSheetName = $objPHPExcel->getSheetNames();
				for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
					$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
					$highestRow = $objWorksheet->getHighestRow();
					$highestColumn = $objWorksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					for ($i = 2; $i <= $highestRow; $i++) {
						$redata = [];
						for ($j = 0; $j < $highestColumnIndex; $j++) {
							$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
							if ($j == 2 || $j == 3 || $j == 4) {
								if (is_numeric($Val)) {
									$Val = PHPExcel_Shared_Date::ExcelToPHPObject($Val)->format('d/m/Y');
								}
							}
							$redata[$j] = trim($Val);
						}
						$data[] = $redata;
					}
				}
			}
			// var_dump($data);die;
			$data_show = [];
			$data_show_key = 0;
			$branch_id = null;
			foreach ($data as $key => $value) {
				// if(empty($value[1])) continue;
				$category_task_code = $value[1];
				if (!empty($category_task_code)) {
					$this->db->where('code = "' . trim($category_task_code) . '"', false, false);
					$this->db->where('hide = 0');
					$category_task = $this->db->get('tblcategory_tasks')->row();
					if (!empty($category_task)) {
						$category_task_id = $category_task->id;

						$content = $value[6];
						$date_start = !empty($value[2]) ? $value[2] : NULL;
						$date_end = !empty($value[3]) ? $value[3] : NULL;
						$date_tasks = !empty($value[4]) ? $value[4] : NULL;

						$task_department = '';
						if (!empty($category_task->departments)) {
							$department = get_table_where('tbldepartments', ['departmentid' => $category_task->departments], '', 'row_array', '', 'name');
							if (!empty($department['name'])) {
								$task_department = $department['name'];
							}
						}
						$task_name = $category_task->content;
						$task_content = $category_task->content;
						$staff_assigner_code = $value[7]; // Người giao việc
						$staff_assigner_text = '';
						$staff_assigner_id = '';
						if (!empty($staff_assigner_code)) {
							$this->db->where('code_role = "' . trim($staff_assigner_code) . '"', false, false);
							$this->db->where('active_role', 1);
							$dtRoles = $this->db->get('tblroles')->row();
							if (!empty($dtRoles)) {
								$this->db->where('role', $dtRoles->roleid);
								$staff_assigner = $this->db->get('tblstaff')->row();
								if (!empty($staff_assigner)) {
									$staff_assigner_id = $staff_assigner->staffid;
									$fullname_CREATE = get_staff_full_name($staff_assigner_id);
									// var_dump($fullname_CREATE);die;
									// $staff_assigner_text .= $fullname_CREATE;
									$staff_assigner_text .= '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $staff_assigner_id) . '">' . staff_profile_image($staff_assigner_id, [
										'staff-profile-image-small',
									]) . '</a><div class="hide">' . $fullname_CREATE . '</div></p>';
								}
							} else {
								$staff_assigner_id = '';
								$staff_assigner_text = '';
							}
						} else {
							$staff_assigner_text = '';
						}
						$lstStaff_assigned_code = $value[8]; // Người được phân công
						$staff_assigned_text = '';
						$arr_staff_assigned_id = [];
						$lst_staff_assigned_id = '';
						if (!empty($lstStaff_assigned_code)) {
							$arr_lstStaff_assigned_code = explode(',', trim($lstStaff_assigned_code));
							foreach ($arr_lstStaff_assigned_code as $kk => $vvv) {
								$this->db->where('code_role = "' . trim($vvv) . '"', false, false);
								$this->db->where('active_role', 1);
								$dtRoles = $this->db->get('tblroles')->row();
								if (!empty($dtRoles)) {
									$this->db->where('role', $dtRoles->roleid);
									$staff_assigneds = $this->db->get('tblstaff')->result();
									if (!empty($staff_assigneds)) {
										foreach ($staff_assigneds as $kk => $staff_assigned) {
											$staff_assigned_id = $staff_assigned->staffid;
											$fullname_CREATE = get_staff_full_name($staff_assigned_id);
											$staff_assigned_text .= '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $staff_assigned_id) . '">' . staff_profile_image($staff_assigned_id, [
												'staff-profile-image-small',
											]) . '</a><div class="hide">' . $fullname_CREATE . '</div></p>';
											$arr_staff_assigned_id[] = $staff_assigned->staffid;
										}
									}
								}
							}
							if (!empty($arr_staff_assigned_id)) {
								$lst_staff_assigned_id = implode(',', $arr_staff_assigned_id);
							}
						} else {
							// $staff_assigned_text = '';
						}
						$staff_monitor_code = $value[9]; // Người giao việc
						$staff_monitor_text = '';
						$staff_monitor_id = '';
						if (!empty($staff_monitor_code)) {
							$this->db->where('code_role = "' . trim($staff_monitor_code) . '"', false, false);
							$this->db->where('active_role', 1);
							$dtRoles = $this->db->get('tblroles')->row();
							if (!empty($dtRoles)) {
								$this->db->where('role', $dtRoles->roleid);
								$staff_monitor = $this->db->get('tblstaff')->row();
								if (!empty($staff_monitor)) {
									$staff_monitor_id = $staff_monitor->staffid;
									$fullname_CREATE = get_staff_full_name($staff_monitor_id);
									// var_dump($fullname_CREATE);die;
									// $staff_monitor_text .= $fullname_CREATE;
									$staff_monitor_text .= '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $staff_monitor_id) . '">' . staff_profile_image($staff_monitor_id, [
										'staff-profile-image-small',
									]) . '</a><div class="hide">' . $fullname_CREATE . '</div></p>';
								}
							} else {
								$staff_monitor_id = '';
								$staff_monitor_text = '';
							}
						} else {
							$staff_monitor_text = '';
						}
						$data_show_key++;
						$process = get_table_where('tblcategory_tasks_process', ['id_category_tasks' => $category_task_id], '', 'result_array');
					} else {
						$category_task_id = null;
						// continue;
					}
				}

				// $result['process'] = [];
				$process_name = $value[10];
				if (!empty($process)) {
					foreach ($process as $processKey => $processValue) {
						$process[$processKey]['process'] = $processValue['name'];
						$process[$processKey]['kpi_plus'] = $processValue['kpi_plus'];
						$process[$processKey]['kpi_minus'] = $processValue['kpi_minus'];
						$process[$processKey]['process_id'] = $processValue['id'];
						if ($process_name == $processValue['name']) {
							$process[$processKey]['staff_id'] = '';
							$process[$processKey]['text_staff_id'] = '';
							$this->db->where('code_role = "' . trim($value[11]) . '"', false, false);
							$this->db->where('active_role', 1);
							$dtRoles = $this->db->get('tblroles')->row();
							if (!empty($dtRoles)) {
								$this->db->where('role', $dtRoles->roleid);
								$kt_staff = $this->db->get('tblstaff')->row();
								if (!empty($kt_staff)) {
									$process[$processKey]['staff_id'] = $kt_staff->staffid;
									$fullname_CREATE = get_staff_full_name($kt_staff->staffid);
									$process[$processKey]['text_staff_id'] = '<p class="text-center"><a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $kt_staff->staffid) . '">' . staff_profile_image($kt_staff->staffid, [
										'staff-profile-image-small',
									]) . '</a><div class="hide">' . $fullname_CREATE . '</div></p>';
								}
							}
							$process[$processKey]['staff_code'] = $value[11];
							$process[$processKey]['week_one'] = $value[12];
							$process[$processKey]['week_two'] = $value[13];
							$process[$processKey]['week_three'] = $value[14];
							$process[$processKey]['week_four'] = $value[15];
							$process[$processKey]['pass_status'] = $value[16];
							
							$kpi_val = isset($value[17]) && $value[17] !== '' ? trim($value[17]) : null;
							$kpi_type = null;
							
							if ($kpi_val === null || $kpi_val === '') {
								if ($value[16] !== null && $value[16] !== '') {
									$kpi_val = ($value[16] == 1) ? $processValue['kpi_plus'] : $processValue['kpi_minus'];
									$kpi_type = ($value[16] == 1) ? 1 : 2;
								} else {
									$kpi_val = 0;
									$kpi_type = null;
								}
							} else {
								$firstChar = substr($kpi_val, 0, 1);
								if ($firstChar === '+') {
									$kpi_type = 1;
									$kpi_val = substr($kpi_val, 1);
								} else if ($firstChar === '-') {
									$kpi_type = 2;
									$kpi_val = substr($kpi_val, 1);
								} else {
									if ($value[16] !== null && $value[16] !== '') {
										$kpi_type = ($value[16] == 1) ? 1 : 2;
									} else {
										$kpi_type = 1;
									}
								}
							}
							$process[$processKey]['kpi'] = $kpi_val;
							$process[$processKey]['kpi_type'] = $kpi_type;

							$problem = null;
							if (!empty($value[18])) {
								$problem = 'have_qt';
							} else if (!empty($value[19])) {
								$problem = 'not_qt';
							}
							$process[$processKey]['problem'] = $problem;
						}
					}
				}

				$branch_name = $value[5];
				if (!empty($branch_name)) {
					$this->db->where('name = "' . trim($branch_name) . '"', false, false);
					$branch = $this->db->get('tblbranch')->row();
					if (!empty($branch)) {
						$branch_id = $branch->id;
					} else {
						$branch_id = '';
					}
				}

				if (!empty($category_task_id)) {
					$data_show[$data_show_key] = [
						'category_task' => $category_task_id,
						'branch' => $branch_id,
						'date_start' => $date_start,
						'date_end' => $date_end,
						'date_tasks' => $date_tasks,
						'content' => $content,
						'task_department' => $task_department,
						'task_name' => $task_name,
						'task_content' => $task_content,
						'staff_assigner_id' => $staff_assigner_id,
						'staff_assigner' => $staff_assigner_text,
						'staff_monitor_id' => $staff_monitor_id,
						'staff_monitor' => $staff_monitor_text,
						'staff_assigned_id' => $lst_staff_assigned_id,
						'staff_assigned' => $staff_assigned_text,
						'process' => array_reverse($process)
					];
				}
			}
			// echo json_encode(array_reverse($data_show));die();
			// var_dump($data_show);die;
			echo json_encode(($data_show));
			die();
		}
	}

	public function import_data_table_old()
	{
		ob_end_clean();
		ini_set('max_execution_time', 800);
		$dataPost = $this->input->post();
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');
		$data = [];
		if (!empty($_FILES)) {
			foreach ($_FILES as $kFileImport => $vFileImport) {
				$FILE = $_FILES[$kFileImport];
				$fullfile = $FILE['tmp_name'];
				$nameFile = $FILE['name'];
				$extension = strtoupper(pathinfo($FILE['name'], PATHINFO_EXTENSION));
				if ($extension != 'XLSX' && $extension != 'XLS') {
					echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
					die();
				}
				$inputFileType = PHPExcel_IOFactory::identify($fullfile);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objReader->setReadDataOnly(true);
				$objPHPExcel = @$objReader->load("$fullfile");
				$total_sheets = $objPHPExcel->getSheetCount();
				$allSheetName = $objPHPExcel->getSheetNames();
				for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
					$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
					$highestRow = $objWorksheet->getHighestRow();
					$highestColumn = $objWorksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					for ($i = 2; $i <= $highestRow; $i++) {
						$redata = [];
						for ($j = 0; $j < $highestColumnIndex; $j++) {
							$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
							$redata[$j] = trim($Val);
						}
						$data[] = $redata;
					}
				}
			}
			$data_show = [];
			$process_work_plan = getProcessWorkPlan();
			$keyWorkPlan = [];
			foreach ($process_work_plan as $key => $value) {
				$keyWorkPlan[mb_strtolower($value['name'], 'UTF-8')] = $key;
			}
			foreach ($data as $key => $value) {
				if (empty($value[10])) continue;
				$dataStaff = $value[8];
				$staffs = [];
				if (!empty($dataStaff)) {
					$dataStaff = explode(',', $dataStaff);
					foreach ($dataStaff as $k => $v) {
						$this->db->where('CONCAT(firstname, " ", lastname) = "' . trim($v) . '"', false, false);
						$kt_staff = $this->db->get('tblstaff')->row();
						if (!empty($kt_staff)) {
							$staffs[] = $kt_staff->staffid;
						}
					}
				}
				$dataManageStaff = $value[9];
				$manage_reports = [];
				if (!empty($dataManageStaff)) {
					$dataManageStaff = explode(',', $dataManageStaff);
					foreach ($dataManageStaff as $k => $v) {
						$this->db->where('CONCAT(firstname, " ", lastname) = "' . trim($v) . '"', false, false);
						$kt_staff = $this->db->get('tblstaff')->row();
						if (!empty($kt_staff)) {
							$manage_reports[] = $kt_staff->staffid;
						}
					}
				}
				if (!is_numeric($value[7])) {
					$process = $keyWorkPlan[mb_strtolower($value[7], 'UTF-8')];
				} else {
					$process = $value[7];
				}
				$data_show[] = [
					'name' => !empty($value[1]) ? $value[1] : '',
					'week_one' => !empty($value[2]) ? $value[2] : '',
					'week_two' => !empty($value[3]) ? $value[3] : '',
					'week_three' => !empty($value[4]) ? $value[4] : '',
					'week_four' => !empty($value[5]) ? $value[5] : '',
					'priority_level' => !empty($value[6]) ? $value[6] : '',
					'process' => is_numeric($process) ? $process : '',
					'staffs' => $staffs,
					'manage_reports' => $manage_reports,
					'id' => !empty($value[10]) ? $value[10] : '',
				];
			}
			echo json_encode(array_reverse($data_show));
			die();
		}
	}

	function getCategoryTaskData($id)
	{
		$result = get_table_where('tblcategory_tasks', ['id' => $id], '', 'row_array');
		$result['department'] = get_table_where('tbldepartments', ['departmentid' => $result['departments']], '', 'row_array', '', 'name');
		$result['department'] = (!empty($result['department']['name']) ? $result['department']['name'] : '');
		$process = get_table_where('tblcategory_tasks_process', ['id_category_tasks' => $id], '', 'result_array');
		$result['process'] = [];
		foreach ($process as $value) {
			$result['process'][] = [
				'id' => $value['id'],
				'name' => $value['name'],
				'kpi_plus' => $value['kpi_plus'],
				'kpi_minus' => $value['kpi_minus']
			];
		}
		echo json_encode($result);
	}

	public function export_handling()
	{
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		$id = $this->input->post('id');
		$work_plan = [];


		if (!empty($id)) {
			$this->db->where('id', $id);
			$work_plan = $this->db->get('tbl_work_plan')->row();
			if (!empty($work_plan)) {
				$this->db->select([
					'tbl_work_plan_task.*',
					'tblcategory_tasks.code as code_category',
					'tblcategory_tasks.content as name_category',
					'tblbranch.name as name_branch',
					'tbldepartments.name as name_departments',
				]);
				$this->db->where('work_plan_id', $id);
				$this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbl_work_plan_task.category_task_id', 'left');
				$this->db->join('tblbranch', 'tblbranch.id = tbl_work_plan_task.branch_id', 'left');
				$this->db->join('tbldepartments', 'tbldepartments.departmentid = tblcategory_tasks.departments', 'left');
				$work_plan_task = $this->db->get('tbl_work_plan_task')->result_array();
				foreach ($work_plan_task as $key => $value) {
					$this->db->where('work_plan_id', $id);
					$this->db->where('work_plan_task_id', $value['id']);
					$work_plan_task[$key]['items'] = $this->db->get('tbl_work_plan_items')->result_array();
				}
			}
		}



		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		$listTitle = [
			'Mã Công Việc',
			'Tên Công Việc',
			'Ngày Bắt Đầu Công Việc',
			'Ngày Kết Thúc Công Việc',
			'Chi Nhánh',
			'Bộ Phận',
			'Nội Dung',
			'Người Giao Việc (Mã NV)',
			"Người Được Phân Công \n(Mã NV)\n(Phân Cách Bởi Dấu \",\")",
			'Người Giám sát (Mã NV)',
			'Qui Trình',
			'Tuần 1',
			'Tuần 2',
			'Tuần 3',
			'Tuần 4',
			"Đạt/Không Đạt\n(1: Đạt; 0:Ko Đạt)",
			'Điểm KPI',
			'Đã Có Quy Trình',
			'Chưa Có Quy Trình',
			' QR '
		];
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		insertCompanyInfo($objPHPExcel, 'C1:T2', 'A1');

		$objPHPExcel->getActiveSheet()->SetCellValue("A5", 'PHIẾU KẾ HOẠCH CÔNG VIỆC');
		$objPHPExcel->getActiveSheet()->mergeCells("A5:" . $cloumns_excel[count($listTitle) - 1] . "5")->getStyle("A5:" . $cloumns_excel[count($listTitle) - 1] . "5")->applyFromArray($style_excel['c_head']);
		$numberRow = 3 + 4;
		foreach ($listTitle as $key => $value) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$key]$numberRow", $value)->getStyle("$cloumns_excel[$key]$numberRow")->applyFromArray($style_excel['c_th']);
			$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$key]$numberRow")->getAlignment()->setWrapText(true);
		}
		$numberRow++;
		$dataItems = [];

		if (!empty($work_plan_task)) {
			$ssK = 1;
			foreach ($work_plan_task as $k => $items) {
				$view_assigned = [];
				if (!empty($items['staff_assigned'])) {
					$staff_assigned = explode(',', $items['staff_assigned']);
					foreach ($staff_assigned as $ks => $vs) {
						$view_assigned[] = $this->db->get_where('tblstaff', ['staffid' => $vs])->row('code');
					}
				}

				$_plan_items = $items['items'];

				$dataItems[] = [
					$items['code_category'],
					$items['name_category'],
					_d($items['date_start']),
					_d($items['date_end']),
					$items['name_branch'],
					$items['name_departments'],
					$items['content'],
					(!empty($items['staff_assigner']) ? $this->db->get_where('tblstaff', ['staffid' => $items['staff_assigner']])->row('code') : ''),
					(!empty($view_assigned) ? implode(",\n", $view_assigned) : ''),
					(!empty($items['staff_monitor']) ? $this->db->get_where('tblstaff', ['staffid' => $items['staff_monitor']])->row('code') : ''),
					(!empty($_plan_items[0]) ? $_plan_items[0]['category_tasks_process_name'] : ''),
					(!empty($_plan_items[0]) ? $_plan_items[0]['week_one'] : ''),
					(!empty($_plan_items[0]) ? $_plan_items[0]['week_two'] : ''),
					(!empty($_plan_items[0]) ? $_plan_items[0]['week_three'] : ''),
					(!empty($_plan_items[0]) ? $_plan_items[0]['week_four'] : ''),
					(!empty($_plan_items[0]['pass_status']) ? 1 : '0'),
					(!empty($_plan_items[0]) ? ($_plan_items[0]['kpi_type'] == 1 ? '+' : ($_plan_items[0]['kpi_type'] == 2 ? '-' : '')) . $_plan_items[0]['kpi'] : ''),
					(!empty($_plan_items[0]['problem']) && $_plan_items[0]['problem'] == 'have_qt' ? 'X' : ''),
					(!empty($_plan_items[0]['problem']) && $_plan_items[0]['problem'] == 'not_qt' ? 'X' : ''),
					'',
				];
				unset($_plan_items[0]);
				if (!empty($_plan_items)) {
					foreach ($_plan_items as $ki => $vi) {
						$dataItems[] = [
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							(!empty($vi) ? $vi['category_tasks_process_name'] : ''),
							(!empty($vi) ? $vi['week_one'] : ''),
							(!empty($vi) ? $vi['week_two'] : ''),
							(!empty($vi) ? $vi['week_three'] : ''),
							(!empty($vi) ? $vi['week_four'] : ''),
							(!empty($vi['pass_status']) ? 1 : '0'),
							(!empty($vi) ? ($vi['kpi_type'] == 1 ? '+' : ($vi['kpi_type'] == 2 ? '-' : '')) . $vi['kpi'] : ''),
							(!empty($vi['problem']) && $vi['problem'] == 'have_qt' ? 'X' : ''),
							(!empty($vi['problem']) && $vi['problem'] == 'not_qt' ? 'X' : ''),
							'',
						];
					}
				}
			}
		}




		$dataStyle = [
			'c_td_center',
			'c_td_center',
			'c_td_center',
			'c_td_center',
			'c_td_center',
			'c_td_center',
			'c_td_left',
			'c_td_center',
			'c_td_left',
			'c_td_left',
			'c_td_left',
			'c_td_left',
			'c_td_left',
			'c_td_left',
			'c_td_left',
			'c_td_center',
			'c_td_center',
			'c_td_center',
			'c_td_center',
			'c_td_center',
		];
		foreach ($dataItems as $k => $items) {
			foreach ($listTitle as $key => $value) {
				$styleTd = $style_excel['c_td_center'];
				if (!empty($dataStyle[$key]) && !empty($style_excel[$dataStyle[$key]])) {
					$styleTd = $style_excel[$dataStyle[$key]];
				}
				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$key]$numberRow", $items[$key])->getStyle("$cloumns_excel[$key]$numberRow")->applyFromArray($styleTd);
				$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$key]$numberRow")->getAlignment()->setWrapText(true);
			}
			$numberRow++;
		}
		$filename = lang('phieu_ke_hoach_cong_viec') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
}
