<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hand_over extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('hand_over_model');
		$this->load->model('misc_model');
		$this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
		$this->allowed_file_size = '1024';
		$this->upload_path = get_upload_path_by_type('evaluate');
		$this->datetime_now = time();
		$this->tnh = true;
		$this->hasPerView = has_permission('delivery_records', '', 'view');
		$this->hasPerViewOwn = has_permission('delivery_records', '', 'view_own');
		$this->hasPerEdit = has_permission('delivery_records', '', 'edit');
		$this->hasPerCreate = has_permission('delivery_records', '', 'create');
		$this->hasPerDelete = has_permission('delivery_records', '', 'delete');
		$this->hasPerPrint = has_permission('delivery_records', '', 'print');
//		$this->type_object = [
//			'productions_orders' => 'Lệnh sản xuất tổng',
//			'production_report' => 'Phiếu báo cáo sự cố',
//			'orders' => 'Đơn hàng bán',
//			'internal_proposal' => 'Phiếu đề xuất nội bộ',
//		];
		$this->type_object = [];
		$this->type_object_internal_proposal = $this->db->get_where('tbltype_object_internal_proposal', ['type_hide' => 0])->result_array();
		foreach($this->type_object_internal_proposal as $key => $value){
			$this->type_object[$value['key_object']] = $value['name'];
		}
		$this->is_branch = true;
	}

	public function category()
	{
		if (!has_permission('category_hand_over', '', 'view')) {
			access_denied();
		}
		$data = [];
		$data['module_hand_over'] = $this->hand_over_model->getModuleHandOver();
		$data['title'] = lang('tnh_category_hand_over');
		$this->load->view('admin/hand_over/category', $data);
	}

	public function handling_category($id = 0)
	{
		if (!empty($id)) {
			if (!has_permission('category_hand_over', '', 'edit')) {
				ajax_access_denied();
			}
		} else if (empty($id)) {
			if (!has_permission('category_hand_over', '', 'create')) {
				ajax_access_denied();
			}
		}
		$data = [];
		$category_hand_over = $id ? $this->hand_over_model->getCategoryHandOverById($id) : [];
		if ($this->input->post('save')) {
			if ((!empty($category_hand_over) && $category_hand_over['code'] != $this->input->post('code')) || empty($category_hand_over['code'])) {
				$this->form_validation->set_rules('code', lang("tnh_code_category_hand_over"), 'required|is_unique[tbl_category_hand_over.code]');
			}
			$this->form_validation->set_rules('name', lang("tnh_name_category_hand_over"), 'required');
//            $this->form_validation->set_rules('type', lang("tnh_module_category_hand_over"), 'required');
			if ($this->form_validation->run() == true) {
				$code = $this->input->post('code');
				$name = $this->input->post('name');
				$type = $this->input->post('type');
				$option = [
					'code' => $code,
					'name' => $name,
					'type' => $type,
				];
				if ($id) {
					$ins = $this->hand_over_model->updateCategoryHandOver($id, $option);
					$category_hand_over_id = $id;
				} else {
					$ins = $this->hand_over_model->insertCategoryHandOver($option);
					$category_hand_over_id = $ins;
				}
				if (!empty($ins)) {
					$data['result'] = 1;
					$data['message'] = lang('success');
				} else {
					$data['result'] = 0;
					$data['message'] = lang('fail');
				}
			} else {
				$data['result'] = 0;
				$data['message'] = validation_errors();
			}
			echo json_encode($data);
			return;
		}
		$data['category_hand_over'] = $category_hand_over;
		$data['module_hand_over'] = $this->hand_over_model->getModuleHandOver();
		$data['id'] = $id;
		$data['title'] = $id ? lang('tnh_edit_category_hand_over') : lang('tnh_add_category_hand_over');
		$this->load->view('admin/hand_over/handling_category', $data);
	}

	public function getCategoryHandOver()
	{
		$module_category_hand_over_search = $this->input->post('module_category_hand_over_search');
		$aColumns = [
			'tbl_category_hand_over.id as id',
			'tbl_category_hand_over.code as code',
			'tbl_category_hand_over.name as name',
//            'tbl_module_hand_over.name as type',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_category_hand_over';
		$where = [
            'AND tbl_category_hand_over.type_show = 1'
        ];
		$filter = [];
		$join = [
//            'INNER JOIN tbl_module_hand_over ON tbl_module_hand_over.id = tbl_category_hand_over.type'
		];
		if (!empty($module_category_hand_over_search)) {
//            array_push($where, ' AND tbl_category_hand_over.type =' . $module_category_hand_over_search);
		}
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$start++;
			$id = $aRow['id'];
			$row[0] = $id;
			$row[1] = $aRow['code'];
			$row[2] = $aRow['name'];
//            $row[3] = '<div class="text-center">' . $aRow['type'] . '</div>';
			$edit = "";
			$delete = "";
			if(has_permission('category_hand_over', '', 'edit')) {
				$edit = '<a class="tnh-modal" href="' . base_url('admin/hand_over/handling_category/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_category_hand_over') . '</a>';
			}
			if(has_permission('category_hand_over', '', 'delete')) {
				$delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
					<button href=\'' . base_url('admin/hand_over/delete_category/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
					<button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
				"><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_category_hand_over') . '</a>';
			}
			$actions = '
				<div class="dropdown text-center">
					<button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					' . lang('actions') . '
					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
						<li>' . $edit . '</li>
						<li class="not-outside">' . $delete . '</li>
					</ul>
				</div>';
			$row[3] = $actions;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function delete_category($id)
	{
		if (!has_permission('category_hand_over', '', 'delete')) {
			ajax_access_denied();
		}
		$data = [];
		$isCategoryHandOver = $this->hand_over_model->isCategoryHandOver($id);
		if ($isCategoryHandOver) {
			$data['result'] = 0;
			$data['message'] = lang('tnh_exist_not_delete');
			echo json_encode($data);
			die;
		}
		if ($this->hand_over_model->deleteCategoryHandOver($id)) {
			$data['result'] = 1;
			$data['message'] = lang('success');
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function category_modal_excel_import()
	{
		$data['title'] = 'Import '._l('tnh_category_hand_over');
		$this->load->view('admin/hand_over/category_excel_import', $data);
	}

	public function category_excel_import()
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
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('B');
			$arraydata          = array();

			$fields = $this->input->post('fields');
			for ($row = 2; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 2][$col] = $value;
				}
			}

			foreach ($arraydata as $key => $value) {
				// 0: code
				// 1: name
				$code = $value[0];
				$name = $value[1];

				if (empty($code) || empty($name)) {
					continue;
				}
				$categoryId = $this->hand_over_model->getCategoryIdByCode($code);
				if (empty($categoryId)){ // insert
					if (!has_permission('category_hand_over', '', 'create')) {
						continue;
					}
					$options = [
						'code' => $code,
						'name' => $name,
					];
					$rs = $this->hand_over_model->insertCategoryHandOver($options);
					if (!empty($rs)) {
						$count++;
					}
				} else { //update
					if (!has_permission('category_hand_over', '', 'edit')) {
						continue;
					}
					$options = [
						'name' => $name,
					];
					$rs = $this->hand_over_model->updateCategoryHandOver($categoryId, $options);
					if (!empty($rs)) {
						$count++;
					}
				}
			}
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

//*****************************************************************************//

	public function task()
	{
		if (!has_permission('handover_task', '', 'view')) {
			access_denied();
		}
		$data = [];
		$data['title'] = lang('tnh_handover_task');
		$data['category_hand_over'] = $this->hand_over_model->getCategoryHandOver();
		$this->load->view('admin/hand_over/task', $data);
	}

	public function handling_hand_over_task($id = 0)
	{
		if (!empty($id)) {
			if (!has_permission('handover_task', '', 'edit')) {
				ajax_access_denied();
			}
		} else if (empty($id)) {
			if (!has_permission('handover_task', '', 'create')) {
				ajax_access_denied();
			}
		}
		$data = [];
		$hand_over_task = $id ? $this->hand_over_model->getHandOverTaskById($id) : [];
		if ($this->input->post('save')) {
			if ((!empty($hand_over_task) && $hand_over_task['code'] != $this->input->post('code')) || empty($hand_over_task['code'])) {
//				$this->form_validation->set_rules('code', lang("tnh_code_handover_task"), 'required|is_unique[tbl_hand_over_task.code]');
			}
			$this->form_validation->set_rules('name', lang("tnh_name_handover_task"), 'required');
			$this->form_validation->set_rules('category_hand_over_id', lang("tnh_category_hand_over"), 'required');
			if ($this->form_validation->run() == true) {
				$category_hand_over_id = $this->input->post('category_hand_over_id');
				$name = $this->input->post('name');
				$standard = $this->input->post('standard');
				$method = $this->input->post('method');
				$id_stage = $this->input->post('id_stage');
				$option = [
					'name' => $name,
					'category_hand_over_id' => $category_hand_over_id,
					'standard' => $standard,
					'method' => $method,
					'id_stage' => $id_stage,
				];
				if ($id) {
					$ins = $this->hand_over_model->updateHandOverTask($id, $option);
					$category_hand_over_id = $id;
				} else {
					$option['code'] = $this->db->select('MAX(id) as maxid')->get('tbl_hand_over_task')->row('maxid');
					if(empty($option['code'])) {
						$option['code'] = 0;
					}
					$option['code'] = 'TCBG-' . ($option['code'] + 1);
					$ins = $this->hand_over_model->insertHandOverTask($option);
					$category_hand_over_id = $ins;
				}
				if (!empty($ins)) {
					$data['result'] = 1;
					$data['message'] = lang('success');
				} else {
					$data['result'] = 0;
					$data['message'] = lang('fail');
				}
			} else {
				$data['result'] = 0;
				$data['message'] = validation_errors();
			}
			echo json_encode($data);
			return;
		}
		$data['hand_over_task'] = $hand_over_task;
		$data['category_hand_over'] = $this->hand_over_model->getCategoryHandOver();
		$data['stage'] = $this->db->get('tbl_stages')->result_array();

		$data['standard'] = $this->db->get('tbl_packaging')->result_array();
		$data['id'] = $id;
		$data['title'] = $id ? lang('tnh_edit_handover_task') : lang('tnh_add_handover_task');
		$this->load->view('admin/hand_over/handling_hand_over_task', $data);
	}

	public function getHandOverTasks()
	{
		$category_hand_over_search = $this->input->post('category_hand_over_search');
		$aColumns = [
			'tbl_hand_over_task.id as id',
			'tbl_stages.code as code_stage',
			'tbl_category_hand_over.code as category_hand_over_code',
			'tbl_category_hand_over.name as category_hand_over_name',
			'tbl_hand_over_task.name as name',
			'tbl_packaging.code as code_standard',
			'tbl_hand_over_task.method as method',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_hand_over_task';
		$where = [];
		$filter = [];
		$join = [
			'INNER JOIN tbl_category_hand_over ON tbl_category_hand_over.id = tbl_hand_over_task.category_hand_over_id',
			'LEFT JOIN tbl_packaging ON tbl_packaging.id = tbl_hand_over_task.standard',
			'LEFT JOIN tbl_stages ON tbl_stages.id = tbl_hand_over_task.id_stage'
		];
		$where[] = 'AND tbl_hand_over_task.type_hide = "0"';

		if (!empty($category_hand_over_search)) {
			array_push($where, ' AND tbl_hand_over_task.category_hand_over_id =' . $category_hand_over_search);
		}

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$start++;
			$id = $aRow['id'];
			$row[0] = $id;
			$row[1] = $aRow['code_stage'];
			$row[2] = $aRow['category_hand_over_code'];
			$row[3] = $aRow['category_hand_over_name'];
			$row[4] = $aRow['name'];
			$row[5] = $aRow['code_standard'];
			$row[6] = $aRow['method'];
			$edit = '';
			$delete = '';
			if(has_permission('handover_task', '', 'edit')) {
				$edit = '<a class="tnh-modal" href="' . base_url('admin/hand_over/handling_hand_over_task/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('Sửa tiêu chí') . '</a>';
			}
			if(has_permission('handover_task', '', 'delete')) {
				$delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
						<button href=\'' . base_url('admin/hand_over/delete_hand_over_task/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
						<button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
					"><i class="fa fa-remove width-icon-actions"></i> ' . lang('Xóa tiêu chí') . '</a>';
			}
			$actions = '<div class="dropdown text-center">
							<button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
							' . lang('actions') . '
							<span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
								<li>' . $edit . '</li>
								<li class="not-outside">' . $delete . '</li>
							</ul>
						</div>';
			$row[7] = $actions;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
	}

	public function delete_hand_over_task($id)
	{
		if (!has_permission('handover_task', '', 'delete')) {
			ajax_access_denied();
		}
		$data = [];
		$isHandOverTask = $this->hand_over_model->isHandOverTask($id);
		if ($isHandOverTask) {
			$data['result'] = 0;
			$data['message'] = lang('tnh_exist_not_delete');
			echo json_encode($data);
			die;
		}
		if ($this->hand_over_model->deleteHandOverTask($id)) {
			$data['result'] = 1;
			$data['message'] = lang('success');
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function task_modal_excel_import()
	{
		$data['title'] = 'Import '._l('tnh_handover_task');
		$this->load->view('admin/hand_over/task_excel_import', $data);
	}

	public function task_excel_import()
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
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('E');
			$arraydata          = array();

			$fields = $this->input->post('fields');
			for ($row = 2; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 2][$col] = $value;
				}
			}
			$arrayStage = [];
			
			foreach ($arraydata as $key => $value) {
				// 0: category_hand_over_id
				// 1: code
				// 2: name
				// 3: standard
				// 4: method

				$id_stage = $value[0];
				$category_hand_over_code = $value[1];
				$name = $value[2];
				$standard = $value[3];
				$method = $value[4];
				if(empty($category_hand_over_code) || empty($name)) {
					continue;
				}



//                $taskId = $this->hand_over_model->getTaskIdByCode($code);
//                if (empty($taskId)){ // insert
				if (!has_permission('handover_task', '', 'create')) {
					continue;
				}

				if(empty($arrayStage[$id_stage])) {
					$this->db->where('code', trim($id_stage));
					$stage = $this->db->get('tbl_stages')->row();
					if(!empty($stage)) {
						$arrayStage[$id_stage] = $stage->id;
					}
				}
				$id_standard = NULL;
				$this->db->where('code', $standard);
				$kt_packaging = $this->db->get('tbl_packaging')->row();

				if(empty($kt_packaging)) {
					$success_pack = false;
					if(!empty($standard)) {
						$success_pack = $this->db->insert('tbl_packaging', [
							'code' => $standard,
							'name' => $method
						]);
					}
					if(!empty($success_pack)) {
						$id_standard = $this->db->insert_id();
					}
				}
				else {
					$id_standard = $kt_packaging->id;
				}


				$code = $this->db->select('MAX(id) as maxid')->get('tbl_hand_over_task')->row('maxid');
				if(empty($code)) {
					$code = 0;
				}
				$code = 'TCBG-' . ($code + 1);
				$category_hand_over_id = $this->hand_over_model->getCategoryIdByCode($category_hand_over_code);
				if (empty($category_hand_over_id) || empty($name)) {
					continue;
				}
				$options = [
					'category_hand_over_id' => $category_hand_over_id,
					'code' => $code,
					'name' => $name,
					'id_stage' => !empty($arrayStage[$id_stage]) ? $arrayStage[$id_stage] : NULL,
					'standard' => $id_standard,
					'method' => $method,
				];

				$rs = $this->hand_over_model->insertHandOverTask($options);
				if ($rs) {


					$count++;
				}
//                } else { //update
//                    if (!has_permission('handover_task', '', 'edit')) {
//                        continue;
//                    }
//                    $category_hand_over_id = $this->hand_over_model->getCategoryIdByName($category_hand_over_name);
//                    $options = [
//                        'category_hand_over_id' => $category_hand_over_id,
//                        'name' => $name,
//						'id_stage' => !empty($arrayStage[$id_stage]) ? $arrayStage[$id_stage] : NULL,
//                        'standard' => $standard,
//                        'method' => $method,
//                    ];
//                    foreach ($options as $key => $value) {
//                        if (empty($value)) {
//                            unset($options[$key]);
//                        }
//                    }
//                    if (empty($options)) {
//                        continue;
//                    }
//                    $rs = $this->hand_over_model->updateHandOverTask($taskId, $options);
//                    if ($rs) {
//                        $count++;
//                    }
//                }
			}
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


	public function task_modal_excel_import_group()
	{
		$data['title'] = 'Import '._l('tnh_handover_task');
		$this->load->view('admin/hand_over/task_excel_import_group', $data);
	}

	public function task_excel_import_group()
	{
		if (!has_permission('handover_task', '', 'create')) {
			ajax_access_denied();
		}
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
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('E');
			$arraydata          = array();

			$fields = $this->input->post('fields');
			for ($row = 2; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 2][$col] = $value;
				}
			}
			$arrayStage = [];
			foreach ($arraydata as $key => $value) {
				// 0: category_hand_over_id
				// 1: code
				// 2: name
				// 3: standard
				// 4: method

				$group_id_stage = $value[0];
				$category_hand_over_code = $value[1];
				$name = $value[2];
				$standard = $value[3];
				$method = $value[4];
				if(empty($category_hand_over_code) || empty($name) || empty($group_id_stage)) {
					continue;
				}

				if(empty($arrayStage[$group_id_stage])) {
					$this->db->where('code', trim($group_id_stage));
					$group_stage = $this->db->get('tbl_category_stages')->row();
					if(!empty($group_stage)) {
						$arrayStage[$group_id_stage] = $this->db->get_where('tbl_stages', ['category_stages' => $group_stage->id])->result_array();
					}
				}
				
				$id_standard = NULL;
				$this->db->where('code', $standard);
				$this->db->where('name', $method);
				$kt_packaging = $this->db->get('tbl_packaging')->row();

				if(empty($kt_packaging)) {
					$success_pack = false;
					if(!empty($standard)) {
						$success_pack = $this->db->insert('tbl_packaging', [
							'code' => $standard,
							'name' => $method
						]);
					}
					if(!empty($success_pack)) {
						$id_standard = $this->db->insert_id();
					}
				}
				else {
					$id_standard = $kt_packaging->id;
				}




				$category_hand_over_id = $this->hand_over_model->getCategoryIdByCode($category_hand_over_code);
				if (empty($category_hand_over_id) || empty($name)) {
					continue;
				}
				if(!empty($arrayStage[$group_id_stage])) {
					foreach ($arrayStage[$group_id_stage] as $k => $v) {
						$code = $this->db->select('MAX(id) as maxid')->get('tbl_hand_over_task')->row('maxid');
						if (empty($code)) {
							$code = 0;
						}
						$code = 'TCBG-' . ($code + 1);
						$options = [
							'category_hand_over_id' => $category_hand_over_id,
							'code' => $code,
							'name' => $name,
							'id_stage' => $v['id'],
							'standard' => $id_standard,
							'method' => $method,
						];
						$rs = $this->hand_over_model->insertHandOverTask($options);
						if ($rs) {
							$count++;
						}
					}
				}
			}
		}
		echo json_encode(
			[
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Import thành công ' . $count . ' dữ liệu',
			]
		);
		die();
	}


//*****************************************************************************//



	public function delivery_records()
	{
		if (!$this->hasPerView && !$this->hasPerViewOwn) {
			access_denied();
		}
		$data = [];
		$data['module_hand_over'] = $this->hand_over_model->getModuleHandOver();
		$data['staffs'] = $this->site_model->getStaffAll();
		$data['title'] = lang('tnh_delivery_records');
		$data['category_hand'] = $this->db->get('tbl_category_hand_over')->result_array();
		$this->load->view('admin/hand_over/delivery_records', $data);
	}

	public function getStaffWhereRole() {

		$staffDepartments = "(
			SELECT
				tblstaff_departments.staffid as staffid,
				GROUP_CONCAT(tbldepartments.name) as name_department 
			FROM tblstaff_departments
			INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
			GROUP BY tblstaff_departments.staffid
		) tb_staff_departments";
		$this->db->select('
			tblstaff.staffid as staffid,
			tblstaff.firstname as firstname,
			tblstaff.lastname as lastname,
			CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname,
			tblroles.name as name_role,
			tb_staff_departments.name_department as name_department
		', false);
		$this->db->from('tblstaff');
		$this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
		$this->db->where('tblroles.roles_parent', 0);
		$this->db->join($staffDepartments, 'tb_staff_departments.staffid = tblstaff.staffid');
		return $this->db->get()->result_array();
	}

	public function handling_delivery_records($id = 0, $id_import = 0)
	{
		if (!empty($id_import)) {
			if (!has_permission('import', '', 'approve_qc')) {
				$data['result'] = 0;
				$data['message'] = lang('Bạn không có quyền duyệt QC');
				echo json_encode($data);
				die;
			}
		}
		if (!empty($id)) {
			if (!$this->hasPerEdit) {
				ajax_access_denied();
			}
		} else if (empty($id)) {
			if (!$this->hasPerCreate) {
				ajax_access_denied();
			}
		}
		$data = [];
		$delivery_records = $id ? $this->hand_over_model->getDeliveryRecordsById($id) : [];
		if ($this->input->post('save')) {
			$reference_no = $this->input->post('reference_no');
			if ((!empty($delivery_records) && $delivery_records['reference_no'] != $this->input->post('reference_no')) || empty($delivery_records['reference_no'])) {
				if(!empty($id)) {
					$this->form_validation->set_rules('reference_no', lang("tnh_reference_no_delivery_records"), 'required|is_unique[tbl_delivery_records.reference_no]');
				}
				else {
					$this->db->where('reference_no', $this->input->post('reference_no'));
					$kt_code = $this->db->get('tbl_delivery_records')->row();
					if(!empty($kt_code)) {
						$reference_no = get_option('prefix_delivery_records') . sprintf('%06d', ch_getMaxID('id', 'tbl_delivery_records') + 1);
					}
				}
			}
			$this->form_validation->set_rules('date', lang("tnh_date_delivery_records"), 'required');
			$this->form_validation->set_rules('staff', lang("staff_delivery_records"), 'required');
			$this->form_validation->set_rules('id_branch', lang("id_branch"), 'required');
//            $this->form_validation->set_rules('module_category_hand_over[]', lang("Bộ phận bàn giao"), 'required');
			if ($this->form_validation->run() == true) {
				$date = to_sql_date($this->input->post('date'), true);
				$staff = $this->input->post('staff');
				$module_category_hand_over = $this->input->post('module_category_hand_over');
				$category_hand = $this->input->post('category_hand');
				$task_hand_over = $this->input->post('task_hand_over');
				$type_object = $this->input->post('type_object');
				$id_object = $this->input->post('id_object');
				$receiver = $this->input->post('receiver');
				$id_branch = $this->input->post('id_branch');
				$note = $this->input->post('note');
				if (empty($task_hand_over)) {
					$data['result'] = 0;
					$data['message'] = lang('Vui lòng chọn nội dung bàn giao');
					echo json_encode($data);
					die;
				}
				$staff_id_all = get_staff_user_id();
				$date_all = date('Y-m-d H:i:s');
				$delivery_records = [
					'reference_no' => $reference_no,
					'date' => $date,
					'staff' => $staff,
					'type_object' => $type_object,
					'receiver' => $receiver,
					'id_branch' => $id_branch,
					'note' => $note,
				];
				if(!empty($category_hand)) {
					$delivery_records['category_hand'] = $category_hand;
				}
				if ($id) {


					$this->db->where('id', $id);
					$data_delivery_records = $this->db->get('tbl_delivery_records')->row();

					$delivery_records['updated_by'] = $staff_id_all;
					$delivery_records['date_updated'] = $date_all;
					$ins = $this->hand_over_model->updateDeliveryRecords($id, $delivery_records);
					$delivery_records_id = $id;
				} else {
					$delivery_records['created_by'] = $staff_id_all;
					$delivery_records['date_created'] = $date_all;
					$delivery_records['id_import'] = $id_import;
					$ins = $this->hand_over_model->insertDeliveryRecords($delivery_records);
					$delivery_records_id = $ins;
					if ($id_import) {
						$import = array();
						$import['status_qc'] = get_staff_user_id();
						$import['date_qc'] = date('Y-m-d H:i:s');
						$this->db->update('tblimport', $import, array('id' => $id_import));
					}
				}
				if (!empty($ins)) {
					if ($id) {
						if(!empty($category_hand)) {
							$this->hand_over_model->deleteDeliveryRecordsTask($id);
						}
						$this->hand_over_model->deleteDeliveryRecordsModule($id);
						$this->db->where('id_delivery_records', $id)->delete('tbl_delivery_records_object');
					}
					if(!empty($id_object)) {
						foreach($id_object as $key => $value) {
							$this->db->insert('tbl_delivery_records_object', [
								'id_delivery_records' => $delivery_records_id,
								'id_object' => $value
							]);
						}
					}

					$arrModuleCategoryHandOver = [];
					if (!empty($module_category_hand_over)) {
						foreach ($module_category_hand_over as $key => $value) {
							if (empty($value)) continue;
							$arrModuleCategoryHandOver[] = [
								'delivery_records_id' => $delivery_records_id,
								'module_hand_over_id' => $value,
							];
						}
					}
					if (!empty($arrModuleCategoryHandOver)) {
						$this->hand_over_model->insertDeliveryRecordsModule($arrModuleCategoryHandOver);
					}
					$arrDeliveryRecordsTask = [];
					if (!empty($task_hand_over) && !empty($category_hand)) {
						foreach ($task_hand_over as $key => $value) {
							if (empty($value)) continue;
							$arrData = explode('__', $value);
							$delivery_records_task = !empty($this->input->post('delivery_records_task')[$key]) ? $this->input->post('delivery_records_task')[$key] : 0;
							$arrayArr = [
								'id' => $delivery_records_task,
								'delivery_records_id' => $delivery_records_id,
								'hand_over_task_id' => $arrData[0],
								'task_hand_over_qualified' => $arrData[1],
							];
							if(empty($arrData)) {
								$arrayArr['staff_id'] = NULL;
								$arrayArr['date_check'] = NULL;
							}
							$arrDeliveryRecordsTask[] = $arrayArr;
						}
						$this->hand_over_model->insertDeliveryRecordsTask($arrDeliveryRecordsTask);
					}



					if(!empty($ins) && !empty($delivery_records_id)) {
						$this->load->library('upload');
						if (isset($_FILES['file']['name']) && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
							if (!is_array($_FILES['file']['name'])) {
								$_FILES['file']['name']     = [$_FILES['file']['name']];
								$_FILES['file']['type']     = [$_FILES['file']['type']];
								$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
								$_FILES['file']['error']    = [$_FILES['file']['error']];
								$_FILES['file']['size']     = [$_FILES['file']['size']];
							}
							$path = 'uploads/delivery_records/' . $delivery_records_id . '/';
							if (!file_exists(FCPATH . 'uploads/delivery_records/')) {
								mkdir(FCPATH . 'uploads/delivery_records/');
								fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
							}
							if (!file_exists(FCPATH . 'uploads/delivery_records/' . $delivery_records_id)) {
								mkdir(FCPATH . 'uploads/delivery_records/' . $delivery_records_id);
								fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
							}
							for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
								$tmpFilePath = $_FILES['file']['tmp_name'][$i];
								if (!empty($tmpFilePath) && $tmpFilePath != '') {
									$filename = vn_to_str(unique_filename($path, $_FILES['file']['name'][$i]));
									if (!_upload_extension_allowed($filename)) {
										continue;
									}
									$newFilePath = $path . $filename;
									if (move_uploaded_file($tmpFilePath, $newFilePath)) {
										$typeFile = $_FILES['file']['type'][$i];
										if (file_exists($newFilePath)) {
											$this->db->insert('tblfiles', [
												'rel_id' => $delivery_records_id,
												'rel_type' => 'delivery',
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
					if(!empty($receiver)) {
						if(empty($data_delivery_records) || $data_delivery_records->receiver != $receiver) {
							add_notification([
								'description' => "<a class='c_modal' href='" . admin_url('hand_over/view/' . $delivery_records_id) . "'> Bạn vừa được thêm vào người nhận bàn giao của biên bản bàn giao " . $reference_no . ' Vào lúc ' . _dt(date('Y-m-d H:i:s')) . '</a>',
								'touserid' => $receiver,
								'link' => '',
								'type' => 1
							]);
							pusher_trigger_notification([$receiver]);
							send_notification_app_c($delivery_records_id, [
								'description' => 'Hệ thống - ' . get_staff_full_name() . ' vừa thêm bạn vào người nhận bàn giao của biên bản bàn giao ' . $reference_no . ' vào lúc ' . _dt(date('Y-m-d H:i:s')),
								'title' => 'Biên bản bàn giao',
								'code' => $reference_no,
								'object_type' => 'delivery_records'
							], [$receiver], get_staff_user_id());
						}
					}


					$data['result'] = 1;
					$data['message'] = lang('success');
				} else {
					$data['result'] = 0;
					$data['message'] = lang('fail');
				}
			} else {
				$data['result'] = 0;
				$data['message'] = validation_errors();
			}
			echo json_encode($data);
			return;
		}

		if(!empty($id)) {
			$delivery_records_object = $this->db->get_where('tbl_delivery_records_object', ['id_delivery_records' => $id])->result_array();
			if(!empty($delivery_records_object)) {
				foreach($delivery_records_object as $key => $value) {
					$delivery_records['id_object_'.$value['id_object']] = $value['id_object'];
				}
				$delivery_records['records_object'] = $delivery_records_object;

			}
			if(!empty($delivery_records['type_object'])) {
				$dataOption = get_relation_data($delivery_records['type_object']);
				$relOptions = init_relation_options($dataOption, $delivery_records['type_object']);
				$data['id_object'] = $relOptions;
			}
			$delivery_records['name_type_object'] = !empty($this->type_object[$delivery_records['type_object']]) ? $this->type_object[$delivery_records['type_object']] : '';
			if($delivery_records['type_object'] == 'productions_orders_detail') {
				$delivery_records['name_type_object'] = 'Lệnh SX chi tiết';
			}


			if(!empty($this->is_branch)) {
				if (!is_admin()) {
					$list_branch = get_array_branch_staff();
					if (!empty($list_branch)) {
						$this->db->group_start();
						$this->db->where_in('tbl_delivery_records.id_branch', $list_branch);
						$this->db->group_end();
						$this->db->where('id', $id);
						$ktData = $this->db->get('tbl_delivery_records')->row();
					} else {
						$ktData = false;
					}

					if (empty($ktData)) {
						access_denied();
					}
				}
			}
		}


		$data['delivery_records'] = $delivery_records;
		$data['module_hand_over'] = $this->hand_over_model->getModuleHandOver();
//		$data['staffs'] = $this->getStaffWhereRole();
		
		$this->db->select('
			tblstaff.staffid as staffid,
			tblstaff.firstname as firstname,
			tblstaff.lastname as lastname,
			CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname
		', false);
		$this->db->from('tblstaff');
		$data['staffs'] = $this->db->get()->result_array();
		
		
		$data['id'] = $id;
		$data['id_import'] = $id_import;
		$data['reference_no'] = get_option('prefix_delivery_records') . sprintf('%06d', ch_getMaxID('id', 'tbl_delivery_records') + 1);
		$data['title'] = $id ? lang('tnh_edit_delivery_records') : lang('tnh_add_delivery_records');
		$data['category_hand'] = $this->db->get_where('tbl_category_hand_over', ['type_show' => 1])->result_array();
		$data['branch'] = $this->db->get('tblbranch')->result_array();
		if(!empty($id)) {
			$data['type_object_internal_proposal'] = [];
			$data['type_object'] = [];
			$this->db->where('type_hide', 0);
			$this->db->or_where('key_object', $delivery_records['type_object']);
			$data['type_object_internal_proposal'] = $this->db->get_where('tbltype_object_internal_proposal')->result_array();
			
			foreach($data['type_object_internal_proposal'] as $key => $value){
				$data['type_object'][$value['key_object']] = $value['name'];
			}
		}
		else {
			$data['type_object_internal_proposal'] = $this->type_object_internal_proposal;
			$data['type_object'] = $this->type_object;
		}
		$this->load->view('admin/hand_over/handling_delivery_records', $data);
	}

	public function check_hand_over_qualified($id = '', $task_hand_over_qualified = 0) {
		if(!empty($id)) {
			$dataUpdate = [];
			if(!empty($task_hand_over_qualified)) {
				$dataUpdate['task_hand_over_qualified'] = $task_hand_over_qualified;
				$dataUpdate['staff_id'] = get_staff_user_id();
				$dataUpdate['date_check'] = date('Y-m-d H:i:s');
			}
			else {
				$dataUpdate['task_hand_over_qualified'] = 0;
				$dataUpdate['staff_id'] = NULL;
				$dataUpdate['date_check'] = NULL;
			}
			$this->db->where('id', $id);
			$success = $this->db->update('tbl_delivery_records_task', $dataUpdate);
			if(!empty($success)) {
				$dataUpdate['fullname']  = '';
				if(!empty($dataUpdate['staff_id'])) {
					$dataUpdate['fullname'] = get_staff_full_name($dataUpdate['staff_id']);
				}
				if(!empty($dataUpdate['date_check'])) {
					$dataUpdate['date_check'] = _dt($dataUpdate['date_check']);
				}
				echo json_encode([
					'data' => $dataUpdate,
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Cập nhật thành công'
				]);die();
			}
		}
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Cập nhật không thành công'
		]);die();
	}

	public function view($id = 0, $id_import = 0)
	{
		$data = [];
		$delivery_records = $id ? $this->hand_over_model->getDeliveryRecordsById($id) : [];

		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_delivery_records.id_branch', $list_branch);
					$this->db->group_end();
					$this->db->where('id', $id);
					$ktData = $this->db->get('tbl_delivery_records')->row();
				} else {
					$ktData = false;
				}

				if (empty($ktData)) {
					access_denied();
				}
			}
		}

		if(!empty($id)) {
			$delivery_records_object = $this->db->get_where('tbl_delivery_records_object', ['id_delivery_records' => $id])->result_array();
			if(!empty($delivery_records_object)) {
				$delivery_records['records_object'] = $delivery_records_object;
			}
		}
		$delivery_records['name_type_object'] = !empty($this->type_object[$delivery_records['type_object']]) ? $this->type_object[$delivery_records['type_object']] : '';
		if($delivery_records['type_object'] == 'productions_orders_detail') {
			$delivery_records['name_type_object'] = 'Lệnh SX chi tiết';
		}
		$data['delivery_records'] = $delivery_records;
		$data['module_hand_over'] = $this->hand_over_model->getModuleHandOver();
		$data['staffs'] = $this->site_model->getStaffAll();
		$data['id'] = $id;
		$data['id_import'] = $id_import;
		$data['reference_no'] = get_option('prefix_delivery_records') . sprintf('%06d', ch_getMaxID('id', 'tbl_delivery_records') + 1);
		$data['title'] = 'Xem biên bản bàn giao';
		$data['category_hand'] = $this->db->get('tbl_category_hand_over')->result_array();

		$this->db->where('rel_type', 'delivery');
		$this->db->where('rel_id', $id);
		$data['files'] = $this->db->get('tblfiles')->result();


		$this->load->view('admin/hand_over/view_handling_delivery_records', $data);
	}

	public function getDataHandOver()
	{
		$data = [];
		$arrHandOver = [];
		if ($this->input->post()) {
			$module_category_hand_over = $this->input->post('module_category_hand_over');
			if (!empty($module_category_hand_over)) {
				$this->db->select('
                    tbl_module_hand_over.*
                ', false);
				$this->db->from('tbl_module_hand_over');
				$this->db->where_in('tbl_module_hand_over.id', $module_category_hand_over);
				$module_hand_over = $this->db->get()->result_array();
				if (!empty($module_hand_over)) {
					foreach ($module_hand_over as $key => $value) {
						$this->db->select('tbl_category_hand_over.*');
						$this->db->from('tbl_category_hand_over');
						$this->db->where('tbl_category_hand_over.type', $value['id']);
						$category_hand_over = $this->db->get()->result_array();
						if (!empty($category_hand_over)) {
							foreach ($category_hand_over as $k => $val) {
								$this->db->select('tbl_hand_over_task.*');
								$this->db->from('tbl_hand_over_task');
								$this->db->where('tbl_hand_over_task.category_hand_over_id', $val['id']);
								$hand_over_task = $this->db->get()->result_array();
								$category_hand_over[$k]['task'] = $hand_over_task;
							}
						}
						$arrHandOver[] = [
							'name_category_hand_over' => $value['name'],
							'category_hand_over' => $category_hand_over
						];
					}
				}
			}
		}
		$data['arrHandOver'] = $arrHandOver;
		echo json_encode($data);
	}

	public function getDataHandOverToCategory()
	{
		$data = [];
		$arrHandOver = [];
		if ($this->input->post()) {
			$category_hand = $this->input->post('category_hand');
			if (!empty($category_hand)) {
				$this->db->select('tbl_category_hand_over.*');
				$this->db->from('tbl_category_hand_over');
				$this->db->where('tbl_category_hand_over.id', $category_hand);
				$category_hand_over = $this->db->get()->result_array();
				if (!empty($category_hand_over)) {
					foreach ($category_hand_over as $k => $val) {
						$this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
						$this->db->from('tbl_hand_over_task');
						$this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
						$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
						$this->db->where('tbl_hand_over_task.category_hand_over_id', $val['id']);
						$hand_over_task = $this->db->get()->result_array();
						$category_hand_over[$k]['task'] = $hand_over_task;
					}
				}
				$arrHandOver[] = [
					'category_hand_over' => $category_hand_over
				];
			}
		}
		$data['arrHandOver'] = $arrHandOver;
		echo json_encode($data);
	}

	public function getDeliveryRecords()
	{
		$staff_search = $this->input->post('staff_search');
		$receiver_search = $this->input->post('receiver_search');
		$module_category_hand_over_search = $this->input->post('module_category_hand_over_search');
		$category_hand_over_search = $this->input->post('category_hand_over_search');

		$staffDepartments = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department 
            FROM tblstaff_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_staff_departments";

		$delivery_records_module = "(
            SELECT
                tbl_delivery_records_module.delivery_records_id as delivery_records_id,
                GROUP_CONCAT(tbl_module_hand_over.name SEPARATOR ', ') as module_hand_over_name
            FROM tbl_delivery_records_module
            INNER JOIN tbl_module_hand_over ON tbl_module_hand_over.id = tbl_delivery_records_module.module_hand_over_id 
            GROUP BY tbl_delivery_records_module.delivery_records_id 
        ) tb_delivery_records_module";

		$tbStaff = "(
            SELECT 
                tblstaff.staffid as staffid,
                tblstaff.firstname as firstname,
                tblstaff.lastname as lastname,
                CONCAT(COALESCE(tblstaff.firstname, ''), ' ', COALESCE(tblstaff.lastname, '')) as fullname,
                tblroles.name as name_role,
                tb_staff_departments.name_department as name_department
            FROM tblstaff
            LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role
            LEFT JOIN $staffDepartments ON tb_staff_departments.staffid = tblstaff.staffid
        ) tb_staff";

		$aColumns = [
			'tbl_delivery_records.id as id',
			'tbl_delivery_records.reference_no as reference_no',
			'tbl_delivery_records.date as date',
			'(SELECT tbl_category_hand_over.name FROM tbl_category_hand_over WHERE tbl_category_hand_over.id = tbl_delivery_records.category_hand) as name_category',
			'"" as group_records',
			'"" as title_records',
			'"" as a5',
			'"" as a6',
			'(
				SELECT GROUP_CONCAT(
					CONCAT(
						tblproduction_report.id,
						"|||",
						tblproduction_report.name_report,
						"|||",
						tblproduction_report.date
					) SEPARATOR ",,,"
				)
				FROM tblproduction_report
				WHERE tblproduction_report.id_delivery_records = tbl_delivery_records.id
			) as ProductionReport',
			'tbl_delivery_records.type_object as type_object',
			'tb_staff.fullname as staff_name',
			'CONCAT(COALESCE(tblreceiver.firstname, ""), " ", COALESCE(tblreceiver.lastname, "")) as staff_name_receiver',
			'tbl_delivery_records.note as note',
//            'tb_delivery_records_module.module_hand_over_name as module_hand_over_name',
			'"" as a7',
			'"" as a9',
			'"" as a8'
		];

		$sIndexColumn = 'id';
		$sTable = 'tbl_delivery_records';
		$where = [];
		$filter = [];
		if (!empty($staff_search)) {
			array_push($where, ' AND tbl_delivery_records.staff =' . $staff_search);
		}
		if (!empty($receiver_search)) {
			array_push($where, ' AND tbl_delivery_records.receiver =' . $receiver_search);
		}
		if (!empty($module_category_hand_over_search)) {
			array_push($where, ' AND exists (
                SELECT tbl_delivery_records_module.id
                FROM tbl_delivery_records_module
                WHERE tbl_delivery_records_module.delivery_records_id = tbl_delivery_records.id AND tbl_delivery_records_module.module_hand_over_id = ' . $module_category_hand_over_search . '
            )');
		}
		if (!empty($category_hand_over_search)) {
			$where[] = 'AND tbl_delivery_records.category_hand = "' . $category_hand_over_search . '"';
		}
		if (!$this->hasPerView) {
			$staffNow = get_staff_user_id();
			$where[] = 'AND (tbl_delivery_records.created_by = "' . $staffNow . '" OR tbl_delivery_records.staff = "' . $staffNow . '")';
		}


		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
				if (!empty($list_branch)) {
					$where[] = 'AND (tbl_delivery_records.id_branch IN (' . $list_branch . '))';
				} else {
					$where[] = 'AND tbl_delivery_records.id = 0';
				}
			}
		}


		$join = [
			'LEFT JOIN ' . $tbStaff . ' ON tb_staff.staffid = tbl_delivery_records.staff',
			'LEFT JOIN tblstaff tblreceiver ON tblreceiver.staffid = tbl_delivery_records.receiver',
			'LEFT JOIN ' . $delivery_records_module . ' ON tb_delivery_records_module.delivery_records_id = tbl_delivery_records.id',
			'LEFT JOIN tblbranch ON tblbranch.id = tbl_delivery_records.id_branch',
		];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tbl_delivery_records.staff as staff',
			'tb_staff.name_role as name_role',
			'tb_staff.name_department as name_department',
			'tbl_delivery_records.manager as manager',
			'tbl_delivery_records.user_manager as user_manager',
			'tbl_delivery_records.date_manager as date_manager',
			'tbl_delivery_records.directorate as directorate',
			'tbl_delivery_records.user_directorate as user_directorate',
			'tbl_delivery_records.date_directorate as date_directorate',
			'tbl_delivery_records.id_import as id_import',
			'tblbranch.name as name_branch',
		], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$start++;
			$id = $aRow['id'];
			$name_role = $aRow['name_role'];
			$name_department = $aRow['name_department'];
			$strStaffInfo = '';
			if (!empty($name_role)) {
				$strStaffInfo .= '<div>Chức vụ: ' . $name_role . '</div>';
			}
			if (!empty($name_department)) {
				$strStaffInfo .= '<div>Phòng ban: ' . $name_department . '</div>';
			}
			$row[] = $id;
			$list_code = '<a class="c_modal" href="' . admin_url('hand_over/view/' . $aRow['id']) . '">' . $aRow['reference_no'] . '</a>';
			$list_code .= !empty($aRow['name_branch']) ? ('<br/><i style="font-size: 11px;white-space: nowrap;">Chi nhánh: ' . $aRow['name_branch'] . '</i>') : '';
			$row[] = $list_code;
			
			$row[] = _d($aRow['date']);
			
			$html = '';
			if (!empty($aRow['id_import'])) {
				$import = get_table_where('tblimport', array('id' => $aRow['id_import']), '', 'row');
				if (!empty($import)) {
					$html = '<span class="inline-block label label-warning">' . $import->prefix . '-' . $import->code . '</span>';
				}
			}
			
			$row[] = '<div style="width: 150px;">' . $aRow['name_category'] . '</div>' . $html;
			$row[] = $aRow['group_records'];
			$row[] = $aRow['title_records'];
			
			
			// $this->db->select('
            //     tbl_hand_over_task.*,
            //     tbl_delivery_records_task.task_hand_over_qualified as task_hand_over_qualified
            // ', false);
			// $this->db->from('tbl_delivery_records_task');
			// $this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id');
			// $this->db->where('tbl_delivery_records_task.delivery_records_id', $id);
			// $delivery_records_task = $this->db->get()->result_array();
			
			$delivery_records_task = [];
			$qualifiedSuccess = 0;
			$qualifiedfail = 0;
			$str_delivery_records_task = '';
			$str_delivery_records_taskMore = '';
			if (!empty($delivery_records_task)) {
				foreach ($delivery_records_task as $k => $value) {
//					$str_delivery_records_task .= '<div>' . $value['name'] . (!empty($value['task_hand_over_qualified']) ? (' - (' . ($value['task_hand_over_qualified'] == 1 ? '<span class="text-primary">Đạt</span>' : ('<span class="text-danger">Chưa đạt</span>')) .')') : '')  . '</div>';
					$qualified = '';
					if($value['task_hand_over_qualified'] == 1) {
						$qualified = ' - <span class="text-primary">(Đạt)</span>';
						$qualifiedSuccess++;
					}
					else if($value['task_hand_over_qualified'] == 2){
						$qualified = ' - <span class="text-danger">(Chưa đạt)</span>';
						$qualifiedfail++;
					}

					if($k < 5) {
						$str_delivery_records_task .= '<div>' . $value['name'] . $qualified .($k == 4 && count($delivery_records_task) > 5 ? ' <a data-toggle="collapse" data-target="#viewMore_'.$aRow['id'].'">...</a>' : '') .  '</div>';
					}
					else {
						$str_delivery_records_taskMore .= '<div>' . $value['name'] . $qualified .($k == 4 && count($delivery_records_task) > 5 ? ' ...' : '') .  '</div>';
					}
				}
			}
			$str_delivery_records_taskMore = '<div id="viewMore_'.$aRow['id'].'" class="collapse">' . $str_delivery_records_taskMore . ' <div><a data-toggle="collapse" data-target="#viewMore_'.$aRow['id'].'">(Thu gọn)</a></div></div>';

			$str_approve_department_head = '<a style="font-size:15px;" class="" onclick="handling_status(' . $aRow['id'] . ', 1, \'approve_department_head\'); return false;"><i class="wrap-icon-check fa fa-check-circle-o"></i></a>';
			$str_directorate = '<a style="font-size:15px;" class="" onclick="handling_status(' . $aRow['id'] . ', 1, \'directorate\'); return false;"><i class="wrap-icon-check fa fa-check-circle-o"></i></a>';
			$manager = $aRow['manager'];
			$activeManager = '';
			$user_manager = $aRow['user_manager'];
			$date_manager = $aRow['date_manager'];
			$directorate = $aRow['directorate'];
			$activeDirectorate = '';
			$user_directorate = $aRow['user_directorate'];
			$date_directorate = $aRow['date_directorate'];
			if ($manager) {
				$full_name = get_staff_full_name($user_manager);
				$date_manager = _d($date_manager);
				$str_approve_department_head = '<a class="mright5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $user_manager) . '">' . staff_profile_image(
						$user_manager,
						[
							'staff-profile-image-small-2x mbot5',
						]
					) . '</a> <span>' . $date_manager . '';
				$str_approve_department_head .= '<br><a style="font-size:15px;" class="" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="' . lang('Bỏ duyệt') . '" onclick="handling_status(' . $aRow['id'] . ', 0, \'approve_department_head\'); return false;" return false;"><i class="wrap-icon-check text-danger fa fa-remove"></i></a>';
				$activeManager = 'active';
			}
			if ($directorate) {
				$full_name = get_staff_full_name($user_directorate);
				$date_directorate = _d($date_directorate);
				$str_directorate = '<a class="mright5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $user_directorate) . '">' . staff_profile_image(
						$user_directorate,
						[
							'staff-profile-image-small-2x mbot5',
						]
					) . '</a> <span>' . $date_directorate . '';
				$str_directorate .= '<br><a style="font-size:15px;" class="" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="' . lang('Bỏ duyệt') . '" onclick="handling_status(' . $aRow['id'] . ', 0, \'directorate\'); return false;" return false;"><i class="wrap-icon-check text-danger fa fa-remove"></i></a>';
				$activeDirectorate = 'active';
			}
			$row[] = '<div style="min-width: 200px!important;">' . $str_delivery_records_task . $str_delivery_records_taskMore . '</div>';
			$qualified = '<div class="text-primary" style="min-width: 100px!important;"><b>'.($qualifiedSuccess).'</b> TIÊU CHÍ ĐẠT</div>';
			$qualified .= '<div class="text-danger" style="min-width: 100px!important;"><b>'.($qualifiedfail).'</b> TIÊU CHÍ CHƯA ĐẠT</div>';
			$row[] = $qualified;
			
			$rowReport = '<a class="btn btn-info btn-icon mbot5" href="'.admin_url('production_report/detail?id_delivery_records='.$aRow['id']).'" target="_blank">Tạo phiếu báo cáo</a>';
			$button_rowReport = '';
			if ($aRow['ProductionReport']) {
				$divReport = '<div class="dropdown-menu dropdown-menu-right">
							<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-39" style="">';
				$ProductionReport = explode(',,,', $aRow['ProductionReport']);
				foreach ($ProductionReport as $kPro => $vPro) {
					$vPro = explode('|||', $vPro);
					$divReport .= '<li><a class="c_modal" href="' . admin_url('production_report/modal/' . $vPro[0]) . '">' . $vPro[1] . ' - ' . _dt($vPro[2]) . '</a></li>';
				}
				$divReport .= '</ul></div>';
				$button_rowReport = '<span class="dropdown-toggle no_background label label-info mtop10" type="button" data-toggle="dropdown">Đã tạo ' . count($ProductionReport) . ' phiếu báo cáo . ' . $divReport . '</span>';
			}
			$row[] = $rowReport . '<br/>' . $button_rowReport;
			
			
			$row[] = '<div class="bold">' . $aRow['staff_name'] . '</div>' . $strStaffInfo;
			$row[] = '<div class="bold">' . $aRow['staff_name_receiver'] . '</div>';
			
			$row[] = '<div class="wrap-container-process">
							<div class="wrap-content-process  ' . $activeManager . '">
								<div class="wrap-step-process line"></div>
								<div class="wrap-title-process">
									' . lang('tnh_approve_department_head') . '
								</div>
								<div class="wrap-title-process" style="">
									' . $str_approve_department_head . '
								</div>
							</div>
							<div class="wrap-content-process ' . $activeDirectorate . '">
								<div class="wrap-step-process"></div>
								<div class="wrap-title-process" style="">
									' . lang('tnh_directorate') . '
								</div>
								<div class="wrap-title-process">
									' . $str_directorate . '
								</div>
							</div>
						</div>';
			$row[] = $aRow['a9'];
			$row[] = $aRow['note'];
			
			
			$view_type_object = '<b>' . (!empty($this->type_object[$aRow['type_object']]) ? $this->type_object[$aRow['type_object']] : '') . '</b>';
			if($aRow['type_object'] == 'productions_orders_detail') {
				$view_type_object = '<b>Lệnh SX chi tiết</b>';
			}
			if(!empty($aRow['type_object'])) {
				$delivery_records_object = $this->db->get_where('tbl_delivery_records_object', ['id_delivery_records' => $aRow['id']])->result_array();
				if(!empty($delivery_records_object)) {
					$arrayValue = [];
					foreach ($delivery_records_object as $k => $value) {
						$rel_data = get_relation_data($aRow['type_object'], $value['id_object']);
						$rel_val = get_relation_values($rel_data, $aRow['type_object']);
						$arrayValue[] = $rel_val['name'];
					}
					$view_type_object .='<br/>' . implode('<br>', $arrayValue);
				}
			}
			
			$row[] = $view_type_object;
			
			

			


			$edit = '';
			$delete = '';
			$print = '';
			if ($this->hasPerEdit) {
				if($aRow['type_object'] != 'productions_orders_detail') {
					$edit = '<a class="tnh-modal" href="' . base_url('admin/hand_over/handling_delivery_records/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_delivery_records') . '</a>';
				}
			}
			if ($this->hasPerPrint) {
				$print = '<a target="_blank" href="' . base_url('admin/hand_over/print_delivery_records/' . $id) . '"><i class="fa fa-print"></i> ' . lang('In phiếu') . '</a>';
			}
			if ($this->hasPerDelete) {
				$delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
								<button href=\'' . base_url('admin/hand_over/delete_delivery_records/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
								<button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
							"><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_delivery_records') . '</a>';
			}
			$actions = '<div class="dropdown text-center">
							<button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
							' . lang('actions') . '
							<span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
								<li>' . $edit . '</li>
								<li>' . $print . '</li>
								<li class="not-outside">' . $delete . '</li>
							</ul>
						</div>';
			$row[] = $actions;
			$output['aaData'][] = $row;
		}
		
		echo json_encode($output);
	}

	public function delete_delivery_records($id)
	{
		if (!$this->hasPerDelete) {
			ajax_access_denied();
		}
		$data = [];
		$delivery_records = get_table_where('tbl_delivery_records', array('id' => $id), '', 'row');
		if (!empty($delivery_records->id_import)) {
			$import = get_table_where('tblimport', array('id' => $delivery_records->id_import), '', 'row');
			if ($import->warehouseman_id > 0) {
				$data['result'] = 0;
				$data['message'] = lang('Phiếu nhập kho đã duyệt kho, Không thể xóa phiếu');
				echo json_encode($data);
				die;
			}
		}
		if ($this->hand_over_model->deleteDeliveryRecords($id)) {
			$this->hand_over_model->deleteDeliveryRecordsTask($id);
			$this->hand_over_model->deleteDeliveryRecordsModule($id);
			$this->db->where('id_delivery_records', $id)->delete('tbl_delivery_records_object');
			if (!empty($delivery_records->id_import)) {
				$import = array();
				$import['status_qc'] = 0;
				$import['date_qc'] = NULL;
				$this->db->update('tblimport', $import, array('id' => $delivery_records->id_import));
			}

			$this->db->where('id', $id);
			$this->db->where('rel_type', 'delivery');
			$get_file_delete = $this->db->get('tblfiles')->row();
			if(!empty($get_file_delete)) {
				$linkFile = FCPATH . 'uploads/delivery_records/' . $get_file_delete->rel_id . '/' . $get_file_delete->file_name;
				if(!empty($linkFile)) {
					unlink($linkFile);
				}
				$this->db->where('id', $id);
				$this->db->where('rel_type', 'delivery');
				$this->db->delete('tblfiles');
			}


			$data['result'] = 1;
			$data['message'] = lang('success');
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	function handling_status()
	{
		$data = [];
		$data['result'] = 0;
		$data['type'] = 'danger';
		$data['message'] = lang('fail');
		if ($this->input->post()) {
			$staff_id = get_staff_user_id();
			$date = date('Y-m-d H:i:s');
			$delivery_records_id = $this->input->post('delivery_records_id');
			$status = $this->input->post('status');
			$type = $this->input->post('type');
			$delivery_records = $this->hand_over_model->getDeliveryRecordsById($delivery_records_id);
			$option = [];
			if ($type == 'approve_department_head') {
				if ($status == $delivery_records['manager']) {
					$data['result'] = 1;
					$data['type'] = 'danger';
					$data['message'] = lang('tnh_status_has_changed');
					echo json_encode($data);
					die;
				}
				$option = [
					'manager' => $status,
					'user_manager' => $staff_id,
					'date_manager' => $date,
				];
			} else if ($type == 'directorate') {
				if ($status == $delivery_records['directorate']) {
					$data['result'] = 1;
					$data['type'] = 'danger';
					$data['message'] = lang('tnh_status_has_changed');
					echo json_encode($data);
					die;
				}
				$option = [
					'directorate' => $status,
					'user_directorate' => $staff_id,
					'date_directorate' => $date,
				];
			}
			$up = $this->hand_over_model->updateDeliveryRecords($delivery_records_id, $option);
			if ($up) {
				$data['result'] = 1;
				$data['type'] = 'success';
				$data['message'] = lang('success');
			}
		}
		echo json_encode($data);
	}

	function print_delivery_records($id = '')
	{
		if (!$this->hasPerPrint) {
			access_denied();
		}
		$data = [];
		$delivery_records = $this->hand_over_model->getDeliveryRecordsById($id);


		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_delivery_records.id_branch', $list_branch);
					$this->db->group_end();
					$this->db->where('id', $id);
					$ktData = $this->db->get('tbl_delivery_records')->row();
				} else {
					$ktData = false;
				}

				if (empty($ktData)) {
					access_denied();
				}
			}
		}


		if(!empty($id)) {
			$delivery_records_object = $this->db->get_where('tbl_delivery_records_object', ['id_delivery_records' => $id])->result_array();
		}
		$delivery_records['name_type_object'] = !empty($this->type_object[$delivery_records['type_object']]) ? $this->type_object[$delivery_records['type_object']] : '';
		if($delivery_records['type_object'] == 'productions_orders_detail') {
			$delivery_records['name_type_object'] = 'Lệnh SX chi tiết';
		}
		$trObject = '';
		if(!empty($delivery_records_object)) {
			$arrayValue = [];
			foreach($delivery_records_object as $key => $value) {
				$rel_data = get_relation_data($delivery_records['type_object'], $value['id_object']);
				$rel_val = get_relation_values($rel_data, $delivery_records['type_object']);
				$arrayValue[] = $rel_val['name'];
			}
			$trObject = '<tr>
							<td><span style="font-weight: bold" >'.$delivery_records['name_type_object'].':</span></td>
							<td>
								<span>' . implode(', ', $arrayValue) . '</span>
							</td>
						</tr>';
			echo implode('<br>', $arrayValue);
		}

		$dtStaff = $this->site_model->getStaffByStaffId($delivery_records['staff']);
		$category_hand_over = get_table_where('tbl_category_hand_over', ['id' => $delivery_records['category_hand']], '', 'row')->name;
//		$data['module_hand_over'] = $this->hand_over_model->getModuleHandOver();
		$data['title'] = 'In biên bản bàn giao';
		ob_start();
		$data = new stdClass();
		$table = '';
		$data->content = '';
		$data->content .= '<span style="text-align: right; font-style: italic;"><b>Ngày bàn giao:</b> ' . _dhau($delivery_records['date']) . '</span><br><br>';
		$data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">BIÊN BẢN BÀN GIAO</span><br><br>';
		$table = '
            <table class="table" border="0" width="100%">
                <tbody>
                    <tr>
                        <td width="25%"><span style="font-weight: bold" >Số phiếu bàn giao:</span></td>
                        <td width="75%">
                            <span>' . $delivery_records['reference_no'] . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td><span style="font-weight: bold" >Hạng muc bàn giao:</span></td>
                        <td>
                            <span>' . $delivery_records['name_type_object'] . '</span>
                        </td>
                    </tr>
                    '.$trObject.'
                    <tr>
                        <td>
                            <span style="text-align: left; font-weight: bold">Người bàn giao:</span>
                        </td>
                        <td>
                            <span style="text-align: left;">' . (!empty($delivery_records['staff']) ? get_staff_full_name($delivery_records['staff']) : '') . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="text-align: left; font-weight: bold">Người nhận bàn giao:</span>
                        </td>
                        <td>
                            <span style="text-align: left;">' . (!empty($delivery_records['receiver']) ? get_staff_full_name($delivery_records['receiver']) : '') . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="text-align: left; font-weight: bold">Phòng ban:</span>
                        </td>
                        <td>
                            <span>' . $dtStaff['name_department'] . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="text-align: left; font-weight: bold">Chức vụ:</span>
                        </td>
                        <td>
                            <span>' . $dtStaff['name_role'] . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="text-align: left; font-weight: bold">Loại bàn giao:</span>
                        </td>
                        <td>
                            <span>' . $category_hand_over . '</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
		$arrHandOver = [];
		if (!empty($delivery_records)) {
			$this->db->select('tbl_category_hand_over.*');
			$this->db->from('tbl_category_hand_over');
			$this->db->where('tbl_category_hand_over.id', $delivery_records['category_hand']);
			$category_hand_over = $this->db->get()->result_array();
			if (!empty($category_hand_over)) {
				foreach ($category_hand_over as $k => $val) {
					$this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
					$this->db->from('tbl_hand_over_task');
					$this->db->where('tbl_hand_over_task.category_hand_over_id', $val['id']);
					$this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
					$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
					$hand_over_task = $this->db->get()->result_array();
					$category_hand_over[$k]['task'] = $hand_over_task;
				}
			}
			$arrHandOver[] = [
				'category_hand_over' => $category_hand_over
			];
			$counterDR = 0;
			if (!empty($arrHandOver)) {
				$trHandOver = '';
				foreach ($arrHandOver as $key => $value) {
					$trTask = '';
					if (!empty($value['category_hand_over'])) {
						foreach ($value['category_hand_over'] as $iC => $vC) {
							if (!empty($vC['task'])) {
								foreach ($vC['task'] as $iT => $vT) {
									$deliveryRecordsTask = $this->hand_over_model->getDeliveryRecordsTaskById($id, $vT['id']);
									$arrTask = [];
									if (!empty($deliveryRecordsTask)) {
										foreach ($deliveryRecordsTask as $kTT => $vTT) {
											$arrTask[] = $vTT['hand_over_task_id'] . '__' . $vTT['task_hand_over_qualified'];
										}
										$trTask .= '<tr>
													<td style="width: 15%;">' . $vT['code_stage'] . '</td>
													<td style="width: 20%;">' . $vT['name'] . '</td>
													<td style="width: 20%;">' . $vT['standard'] . '</td>
													<td style="width: 20%;">' . $vT['method'] . '</td>
													<td style="width: 10%;text-align: center;">
														' . (in_array($vT['id'] . '__1', $arrTask) ? 'X' : '') . '
													</td>
													<td style="width: 15%;text-align: center;">
														' . (in_array($vT['id'] . '__2', $arrTask) ? 'X' : '') . '
													</td>
												</tr>';
									}
									$counterDR++;
								}
							}
						}
					}
					if (empty($trTask)) continue;
					$trHandOver .= $trTask;
				}
			}
		}
		$tableItems = '<br/><br/><table border="1" cellpadding="5" class="table table-bordered">
							<thead>
								<tr style="background: #cedae6;">
									<th style="width: 15%;text-align: center;"><b>' . _l('Công đoạn') . '</b></th>
									<th style="width: 20%;text-align: center;"><b>' . _l('Nội dung bàn giao') . '</b></th>
									<th style="width: 20%;text-align: center;"><b>' . _l('Tiêu chuẩn') . '</b></th>
									<th style="width: 20%;text-align: center;"><b>' . _l('Phương thức') . '</b></th>
									<th style="width: 10%;text-align: center;"><b>' . _l('Đạt') . '</b></th>
									<th style="width: 15%;text-align: center;"><b>' . _l('Không đạt') . '</b></th>
								</tr>
							</thead>
							<tbody>' . $trHandOver . '</tbody>
						</table>';
		$data->content .= $table . $tableItems;
		$pdf = print_pdf($data);
		$type = 'I';
		$pdf->Output(slug_it('bien_ban_ban_giao') . '.pdf', $type);
	}

	public function get_data_object($type = "") {
		$data = get_relation_data($type);
		$relOptions = init_relation_options($data, $type);
		echo json_encode($relOptions);die();
	}

	public function get_table_delivery_records() {
		$stage_id = $this->input->post('stage_id');
		$productions_orders_id = $this->input->post('m_productions_orders_id');

		$this->db->select('tbl_category_hand_over.*');
		$this->db->group_start();
		$this->db->where('tbl_category_hand_over.code', CODE_HAND_OVER_CATEGORY);
		$this->db->or_like('tbl_category_hand_over.code', 'BGCD-');
		$this->db->group_end();
		$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.category_hand_over_id = tbl_category_hand_over.id');
		$this->db->where('tbl_hand_over_task.id_stage', $stage_id);
		$this->db->where('tbl_hand_over_task.type_hide', 0);
		$data['category_hand_over'] = $this->db->get('tbl_category_hand_over')->row();
		if (!empty($data['category_hand_over'])) {
			$this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
			$this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
			$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
			$this->db->where('tbl_hand_over_task.category_hand_over_id', $data['category_hand_over']->id);
			$this->db->where('tbl_hand_over_task.id_stage', $stage_id);
			$this->db->where('tbl_hand_over_task.type_hide', 0);
			$hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
			$data['category_hand_over']->task = $hand_over_task;
		}
		$this->db->where('id_create', $productions_orders_id);
		$this->db->where('type_create', 'productions_orders');
		$data['delivery_records'] = $this->db->get('tbl_delivery_records')->row();
		$this->load->view('admin/hand_over/table_production', $data);
	}

	public function get_table_delivery_records_produtions_detail() {
		$stage_id = $this->input->post('stage_id');
		$pod_id = $this->input->post('pod_id');
		$this->db->select('tbl_category_hand_over.*');
		$this->db->group_start();
		$this->db->where('tbl_category_hand_over.code', CODE_HAND_OVER_CATEGORY);
		$this->db->or_like('tbl_category_hand_over.code', 'BGCD-');
		$this->db->group_end();
		$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.category_hand_over_id = tbl_category_hand_over.id');
		$this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
		$this->db->where('tbl_hand_over_task.type_hide', 0);
		$data['category_hand_over'] = $this->db->get('tbl_category_hand_over')->row();
		if (!empty($data['category_hand_over'])) {
			$this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
			$this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
			$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
			$this->db->where('tbl_hand_over_task.category_hand_over_id', $data['category_hand_over']->id);
			$this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
			$this->db->where('tbl_hand_over_task.type_hide', 0);
			$hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
			$data['category_hand_over']->task = $hand_over_task;
		}
		$this->db->where('id_create', $pod_id);
		$this->db->where('type_create', 'productions_orders_detail');
		$data['delivery_records'] = $this->db->get('tbl_delivery_records')->row();

		$this->load->view('admin/hand_over/table_production', $data);
	}

	public function removeFile($id = '') {
		$this->db->where('id', $id);
		$this->db->where('rel_type', 'delivery');
		$get_file_delete = $this->db->get('tblfiles')->row();
		if(!empty($get_file_delete)) {
			$linkFile = FCPATH . 'uploads/delivery_records/' . $get_file_delete->rel_id . '/' . $get_file_delete->file_name;
			if(!empty($linkFile)) {
				unlink($linkFile);
			}
			$this->db->where('id', $id);
			$this->db->where('rel_type', 'delivery');
			$this->db->delete('tblfiles');
			echo json_encode([
				'success' => true
			]);die();
		}
		echo json_encode([
			'success' => false
		]);die();
	}

	public function export_machines()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // print_arrays($this->input->post());
        // $cloumns = $this->input->post('cloumns');
        $style_excel = style_excel();
        // $cloumns_excel = cloumns_excel();
        
        $category_hand_over_search = $this->input->post('category_hand_over_search');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

		insertCompanyInfo($objPHPExcel, 'C1:G2', 'A1');

        $numberRow = 2 + 4;
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);


        $objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'STT')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'Công đoạn')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'Mã loại bàn giao')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'Tên loại bàn giao')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'Tên tiêu chí bàn giao')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'Tiêu chuẩn')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
        $objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'Phương pháp')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header']);
        $numberRow++;

        $stt = 1;
        $this->db->select('
                            tbl_stages.code as code_stage,
                            tbl_category_hand_over.code as category_hand_over_code,
                            tbl_category_hand_over.name as category_hand_over_name,
                            tbl_hand_over_task.name as name,
                            tbl_packaging.code as code_standard,
                            tbl_hand_over_task.method as method');
        $this->db->join('tbl_category_hand_over', 'tbl_category_hand_over.id = tbl_hand_over_task.category_hand_over_id', 'inner');
        $this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
        if (!empty($category_hand_over_search)) {
            $this->db->where('tbl_hand_over_task.category_hand_over_id', $category_hand_over_search);
		}
        $hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
        
        if (!empty($hand_over_task)) {
            foreach ($hand_over_task as $key => $value) {
                $code_stage = $value['code_stage'];
                $category_hand_over_code = $value['category_hand_over_code'];
                $category_hand_over_name = $value['category_hand_over_name'];
                $name = $value['name'];
                $code_standard = $value['code_standard'];
                $method = $value['method'];

                $objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", $stt)->getStyle("A$numberRow")->applyFromArray($style_excel['BStyle_center']);
                $objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", $code_stage)->getStyle("B$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $category_hand_over_code)->getStyle("C$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", $category_hand_over_name)->getStyle("D$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", $name)->getStyle("E$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", $code_standard)->getStyle("F$numberRow")->applyFromArray($style_excel['BStyle_left']);
                $objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $method)->getStyle("G$numberRow")->applyFromArray($style_excel['BStyle_left']);
                
                $numberRow++;
                $stt++;
            }
        }


        $filename = lang('Tieu_chi_ban_giao') . '.xls';
        $objPHPExcel->getActiveSheet()->freezePane('A1');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
	
	public function export_delivery_records()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
		$list_id = $this->input->post('list_id');
		$delivery_records = [];
		if(!empty($list_id)) {
			$this->db->select('*');
			$this->db->select([
				'"" as group_records',
				'"" as title_records',
				'CONCAT(COALESCE(tblreceiver.firstname, ""), " ", COALESCE(tblreceiver.lastname, "")) as staff_name_receiver',
				'CONCAT(COALESCE(tb_staff.firstname, ""), " ", COALESCE(tb_staff.lastname, "")) as staff_name',
				'(
			SELECT GROUP_CONCAT(
					CONCAT(
						tblproduction_report.id,
						"|||",
						tblproduction_report.name_report,
						"|||",
						tblproduction_report.date
					) SEPARATOR ",,,"
				)
				FROM tblproduction_report
				WHERE tblproduction_report.id_delivery_records = tbl_delivery_records.id
			) as ProductionReport',
				'tbl_delivery_records.type_object',
				'tbl_delivery_records.manager',
				'tbl_delivery_records.directorate',
				'tbl_delivery_records.id',
				'tbl_delivery_records.date',
				'tbl_delivery_records.note',
			]);
			$this->db->select('(SELECT tbl_category_hand_over.name FROM tbl_category_hand_over WHERE tbl_category_hand_over.id = tbl_delivery_records.category_hand) as name_category');
			if (!empty($list_id)) {
				$this->db->where_in('tbl_delivery_records.id', $list_id);
			}
			$this->db->join('tblstaff tb_staff', 'tb_staff.staffid = tbl_delivery_records.staff', 'left');
			$this->db->join('tblstaff tblreceiver', 'tblreceiver.staffid = tbl_delivery_records.receiver', 'left');

			$this->db->order_by('tbl_delivery_records.date', 'desc');
			$delivery_records = $this->db->get('tbl_delivery_records')->result_array();
		}
		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		$listTitle = [
			'Mã Phiếu Bàn Giao',
			'Ngày Bàn Giao',
			'Loại Bàn Giao',
			'Nhóm Bàn Giao',
			'Chi Tiết Bàn Giao',
			'Tiêu Chí Bàn Giao',
			'Kết Quả',
			'Báo Cáo Không Phù Hợp',
			'Người Bàn Giao',
			'Người Nhận Bàn Giao',
			'Ghi chú',
			'Trạng Thái',
			'QR'
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

		insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

		$objPHPExcel->getActiveSheet()->SetCellValue("A5", mb_strtoupper(_l('tnh_delivery_records'), 'UTF-8'));
		$objPHPExcel->getActiveSheet()->mergeCells("A5:" . $cloumns_excel[count($listTitle) - 1] . "5")->getStyle("A5:" . $cloumns_excel[count($listTitle) - 1] . "5")->applyFromArray($style_excel['c_head']);
		$numberRow = 3 + 4;
		foreach ($listTitle as $key => $value) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$key]$numberRow", $value)->getStyle("$cloumns_excel[$key]$numberRow")->applyFromArray($style_excel['c_th']);
		}
		$numberRow++;
		$dataItems = [];
		foreach ($delivery_records as $key => $value) {
			$this->db->select('
				tbl_hand_over_task.*,
				tbl_delivery_records_task.task_hand_over_qualified as task_hand_over_qualified
			', false);
			$this->db->from('tbl_delivery_records_task');
			$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.id = tbl_delivery_records_task.hand_over_task_id');
			$this->db->where('tbl_delivery_records_task.delivery_records_id', $value['id']);
			$delivery_records_task = $this->db->get()->result_array();
			$qualifiedSuccess = 0;
			$qualifiedfail = 0;
			$success_records_tasks = '';
			$view_records_tasks = '';
			if (!empty($delivery_records_task)) {
				foreach ($delivery_records_task as $k => $v) {
					$qualified = '';
					if ($v['task_hand_over_qualified'] == 1) {
						$success_records_tasks .= 'Đạt';
						$view_records_tasks .= $v['name'] . " - ĐẠT\n";
						$qualifiedSuccess++;
					} else if ($v['task_hand_over_qualified'] == 2) {
						$success_records_tasks .= 'Chưa đạt';
						$view_records_tasks .= $v['name'] . " - CHƯA ĐẠT\n";
						$qualifiedfail++;
					}
				}
			}
			$manage_directorate = [];
			if (!empty($value['directorate'])) {
				$manage_directorate[] = "Giám đốc duyệt";
			}
			if (!empty($value['manager'])) {
				$manage_directorate[] = "Trưởng phòng duyệt";
			}
			if (!empty($manage_directorate)) {
				$manage_directorate = implode("\n", $manage_directorate);
			} else {
				$manage_directorate = '';
			}
			$dataItems[] = [
				$value['reference_no'],
				_dt($value['date']),
				$value['name_category'],
				$value['group_records'],
				$value['title_records'],
				$view_records_tasks,
				(empty($qualifiedfail) ? 'ĐẠT' : 'CHƯA ĐẠT'),
				(!empty($value['ProductionReport']) ? "Đã ".count(explode(',,,', $value['ProductionReport']))." tạo phiếu báo cáo": ''),
				$value['staff_name'],
				$value['staff_name_receiver'],
				$value['note'],
				$manage_directorate,
				""
			];
		}
		$dataStyle = [
			'c_td_center', //reference_no
			'c_td_center', //date
			'c_td_center', //name_category
			'c_td_center', //group_records
			'c_td_center', //title_records
			'c_td_left', //view_records_tasks
			'c_td_center', //view_records_tasks
			'c_td_center', //qualifiedfail
			'c_td_center', //Phiếu báo cáo
			'c_td_center', //staff_name
			'c_td_center', //staff_name_receiver
			'c_td_left', //manage_directorate
			'c_td_left', //manage_directorate
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
		$filename = lang('bien_ban_ban_giao') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		
    }
	public function get_table_delivery_records_internal_proposal() {
		$stage_id = $this->input->post('stage_id');
		$internal_proposal_id = $this->input->post('internal_proposal_id');
		$this->db->select('tbl_category_hand_over.*');
		$this->db->group_start();
		$this->db->where('tbl_category_hand_over.code', CODE_HAND_OVER_CATEGORY);
		$this->db->or_like('tbl_category_hand_over.code', 'BGCD-');
		$this->db->group_end();
		$this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.category_hand_over_id = tbl_category_hand_over.id');
		$this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
		$this->db->where('tbl_hand_over_task.type_hide', 0);
		$data['category_hand_over'] = $this->db->get('tbl_category_hand_over')->row();
		if (!empty($data['category_hand_over'])) {
			$this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
			$this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
			$this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
			$this->db->where('tbl_hand_over_task.category_hand_over_id', $data['category_hand_over']->id);
			$this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
			$this->db->where('tbl_hand_over_task.type_hide', 0);
			$hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
			$data['category_hand_over']->task = $hand_over_task;
		}
		$this->db->where('id_create', $internal_proposal_id);
		$this->db->where('type_create', 'internal_proposal');
		$data['delivery_records'] = $this->db->get('tbl_delivery_records')->row();

		$this->load->view('admin/hand_over/table_production', $data);
	}
}

 