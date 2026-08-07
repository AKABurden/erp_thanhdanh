<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Recommended_list extends AdminController
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('recommended_list_model');
		$this->isAdmin = is_admin();
		if (!$this->isAdmin) {
			access_denied('recommended_list');
		}
	}

	public function index()
	{
		$data['title'] = _l('tnh_recommended_list');
		$this->load->view('admin/recommended_list/index', $data);
	}

	public function getRecommendedList()
	{
		$aColumns = [
			'tbl_recommended_list.id as stt',
			'tbl_recommended_list.id as id',
			'tbl_recommended_list.code as code',
			'tbl_recommended_list.name as name',
			'tbl_recommended_list.note as note',
			'tbl_recommended_list.type as type',
			'tbl_recommended_list.type_plan_propose as type_plan_propose',
			'tbl_recommended_list.category_recommended_id as category_recommended_id',
			'"" as actions',
			'"" as items',
			'"" as process',
		];
		$sIndexColumn = 'id';
		$sTable       = 'tbl_recommended_list';
		$where        = [
			'AND tbl_recommended_list.parent_id = 0 AND tbl_recommended_list.type_show = 1 '
		];
		$filter = [];

		$join = [];
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

		$aColumns = handlingColumns($aColumns);

		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		$dtCategoryRecom = get_table_where('tbl_category_recommended');
		$j = 0;
		$type_plan_propose = type_plan_propose();
		foreach ($rResult as $key => $aRow) {
			$start++;
			$row = [];
			$id = $aRow['id'];

			$addChild = '<a class="btn btn-success btn-icon pull-left tnh-modal active-modal" href="' . admin_url('recommended_list/submit/?parent_id=' . $id) . '" data-toggle="modal" data-tnh="modal" data-target="#myModal"><i class="fa fa-plus"></i></a>';
			$edit = '<a class="btn btn-info btn-icon pull-left tnh-modal active-modal" href="' . admin_url('recommended_list/submit_parent/' . $id) . '" data-toggle="modal" data-tnh="modal" data-target="#myModal"><i class="fa fa-pencil"></i></a>';

			$delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/recommended_list/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove"></i></button>';

			foreach ($aColumns as $k => $value) {
				if ($value == 'stt') {
					$row[] = '<div class="text-center">' . $start . '</div>';
				} elseif ($value == 'type_plan_propose') {
					$__data = '';
					foreach ($type_plan_propose as $kk => $vv) {
						if($vv['id'] == $aRow['type_plan_propose']){
							$__data = $vv['name'];
							break;
						}
					}
					$row[] = '<div class="text-center">' . $__data . '</div>';
				} elseif ($value == 'id') {
					$row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right" data-id="' . $aRow['id'] . '"></a></div>';
				} else if ($value == 'type') {
					$row[] = '<div class="text-center">' . (!empty($aRow[$value]) ? getTypeCategoryTasks($aRow[$value]) : '') . '</div>';
				} else if ($value == 'category_recommended_id') {
					$_data = '<select data-recommended_id="' . $aRow['id'] . '" data-placeholder="Chọn" data-live-search="true" id="category_recommended_id_' . $j . '" class="category_recommended_id modal-select2" style="width: 100%">
                    <option></option>';
					foreach ($dtCategoryRecom as $kk => $vv) {
						$selected = '';
						if ($aRow['category_recommended_id'] == $vv['id']) {
							$selected = 'selected';
						}
						$_data .= '<option ' . $selected . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
					}
					$_data .= '</select>';
					$row[] = $_data;
				} else if ($value == 'actions') {
					$row[] = '<div class="text-center">' . $addChild .$edit. $delete . '</div>';
				} else if ($value == 'items') {
					$items = '';
					$this->recursiveTableRecommendedList($items, $id);
					$row[] = $items;
				} else if ($value == 'process') {
					$process = '';
					$this->recursiveTableProcessRecommendedList($process, $id);
					$row[] = $process;
				} else {
					$row[] = $aRow[$value];
				}
			}
			$j++;
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
	public function recursiveTableProcessRecommendedList(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
	{
		$process[0] = '';
		$process[1] = 'Người duyệt thực thi';
		$process[2] = 'Người duyệt đề xuất';
		$process[3] = 'Người Hoàn Thành 2';
		$process[4] = 'Người kiểm soát hoàn thành';
		$process[5] = 'Người lập đề xuất';
		$process[6] = 'Người hoàn thành 1';
		$process[7] = 'Người Kiểm Toán Hoàn Thành';
		$process[8] = 'Người hoàn thành 3';
		$process[9] = 'Người hoàn thành 4';

		$this->db->select('tbl_recommended_list_process.*,tblroles.code_role');
		$this->db->from('tbl_recommended_list_process');
		$this->db->where('tbl_recommended_list_process.recommended_list_id', $parent_id);
		$this->db->join('tblroles', 'tblroles.roleid=tbl_recommended_list_process.roles', 'left');
		$this->db->order_by('tbl_recommended_list_process.id asc');
		$query = $this->db->get()->result_array();
		foreach ($query as $key => $item) {
			$this->db->select('tbl_recommended_list_process_child.*');
			$this->db->from('tbl_recommended_list_process_child');
			$this->db->where('tbl_recommended_list_process_child.id_recommended_list_process', $item['id']);
			$query_child = $this->db->get()->result_array();

			$output .= '<tr>
                    <td>' . $item['name'] . '</td>
                    <td>' . $process[$item['bod']] . '</td>
					<td>' . $item['code_role'] . '</td>
					<td>' . (!empty($query_child) ? $query_child[0]['name'] : '') . '</td>
					<td>' . (!empty($query_child) ? $query_child[0]['approval_standards'] : '') . '</td>
					<td>' . (!empty($query_child) ? $query_child[0]['completion_control_standards'] : '') . '</td>
					<td>' . (!empty($query_child) ? $query_child[0]['minus_point'] : '') . '</td>
					<td>' . (!empty($query_child) ? $query_child[0]['plus_point'] : '') . '</td>
					<td>' . (!empty($query_child) ? $query_child[0]['warning'] : '') . '</td>
	                </tr>';
			if ($query_child) {
				unset($query_child[0]);
				foreach ($query_child as $kv => $iv) {
					$output .= '<tr>
					<td></td>
					<td></td>
					<td></td>
                    <td>' . $iv['name'] . '</td>
                    <td>' . $iv['approval_standards'] . '</td>
					<td>' . $iv['completion_control_standards'] . '</td>
					<td>' . $iv['minus_point'] . '</td>
					<td>' . $iv['plus_point'] . '</td>
					<td>' . $iv['warning'] . '</td>
	                </tr>';
				}
			}
		}
		return $output;
	}
	// public function recursiveTableProcessRecommendedList(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
	// {

	// 	$this->db->select('tbl_recommended_list_process.*,tblroles.code_role');
	// 	$this->db->from('tbl_recommended_list_process');
	// 	$this->db->where('tbl_recommended_list_process.recommended_list_id', $parent_id);
	// 	$this->db->join('tblroles', 'tblroles.roleid=tbl_recommended_list_process.roles', 'left');
	// 	$this->db->order_by('tbl_recommended_list_process.id asc');
	// 	$query = $this->db->get()->result_array();
	// 	foreach ($query as $key => $item) {
	// 		$output .= '<tr>
	//                 <td>' . $item['name'] . '</td>
	//                 <td>' . $item['code_role'] . '</td>
	//                 <td class="text-center">' . (!empty($item['bod']) ? 'X' : '') . '</td>
	//             </tr>';
	// 	}
	// 	return $output;
	// }
	public function recursiveTableRecommendedList(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
	{
		$ktSTT = $stt;
		$this->db->select('tbl_recommended_list.*', false);
		$this->db->select('(
			SELECT 1
			FROM tbl_recommended_list tbl_child
			WHERE tbl_child.parent_id = tbl_recommended_list.id
			limit 1
		) as count_child');
		$this->db->from('tbl_recommended_list');
		$this->db->where('tbl_recommended_list.parent_id', $parent_id);
		//		$this->db->where('tbl_recommended_list.type_show', 1);
		$this->db->order_by('tbl_recommended_list.parent_id');
		$query = $this->db->get()->result_array();

		foreach ($query as $key => $item) {
			if ($item['parent_id'] == $parent_id) {

				$edit = '<a class="btn btn-info btn-icon pull-left tnh-modal active-modal" href="' . admin_url('recommended_list/submit/' . $item['id']) . '" data-toggle="modal" data-tnh="modal" data-target="#myModal"><i class="fa fa-pencil"></i></a>';

				$delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/recommended_list/delete/' . $item['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                "><i class="fa fa-remove"></i></button>';

				$rowChild = !empty($item['count_child']) ? '<a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-2 fa fa-caret-right" data-id="' . $item['id'] . '" data-parent="' . $item['parent_id'] . '"></a>' : '';

				$outputChild = '';
				$outputHtml = '';
				if (!empty($item['count_child'])) {
					$this->recursiveTableRecommendedList($outputChild, $item['id'], NULL, ++$stt);
					$outputHtml = base64_encode($outputChild);
				}
				//c
				$addChild = '';
				if ($ktSTT == 1) {
					$addChild = '<a class="btn btn-success btn-icon pull-left tnh-modal active-modal" href="' . admin_url('recommended_list/submit/?parent_id=' . $item['id']) . '" data-toggle="modal" data-tnh="modal" data-target="#myModal"><i class="fa fa-plus"></i></a>';
				}
				//c
				$is_excel = !empty($item['is_excel']) ? '<i class="fa fa-check text-success"></i>' : '';
				$output .= '<tr>
                    <td>' . $rowChild . '</td>
                    <td>' . $indent . '' . $item['code'] . '</td>
                    <td>' . $item['name'] . '</td>
                    <td class="text-center">' . (!empty($item['type']) ? getTypeCategoryTasks($item['type']) : '') . '</td>
                    <td>' . $item['note'] . '</td>
                    <td class="text-center">' . $is_excel . '</td>
                    <td class="text-center">
						' . $addChild . '
						' . $edit . '
                        ' . $delete . '
                    </td>
                    <td>' . $outputHtml . '</td>
                </tr>';
				//                $this->recursiveTableRecommendedList($output, $item['id'], $indent . "|---", ++$stt);
			}
		}
		return $output;
	}

	public function modal_excel_import()
	{
		$data['title'] = _l('tnh_recommended_list_import_excel');
		$this->load->view('admin/recommended_list/excel_import', $data);
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

			$allSheetName       = $objPHPExcel->getSheetNames();
			$objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
			$highestRow         = $objWorksheet->getHighestRow();
			$highestColumn      = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString('P');
			$arraydata          = array();

			$fields = $this->input->post('fields');
			for ($row = 3; $row <= $highestRow; ++$row) {
				for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$arraydata[$row - 3][$col] = $value;
				}
			}
			$listType = [
				mb_strtoupper('ngày', 'UTF-8') => 1,
				mb_strtoupper('Tháng', 'UTF-8') => 2,
				mb_strtoupper('Năm', 'UTF-8') => 3,
			];
			$dataArray = [];
			$parent_id_one = NULL;
			$parent_id_two = NULL;
			$parent_id_three = NULL;
			$parent_id_process = NULL;

			foreach ($arraydata as $key => $value) {
				if (!empty($value[0])) {
					$parent_id_one = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[0])), 'UTF-8');
					$parent_id_two = NULL;
					$parent_id_three = NULL;
					$dataArray[$parent_id_one] = [
						'name' => preg_replace('/\s+/', ' ', trim($value[0])),
						'code' => preg_replace('/\s+/', ' ', trim($value[0])),
						'type' => !empty($value[15]) ? $listType[mb_strtoupper(trim($value[15]), 'UTF-8')] : NULL,
						'note' => trim($value[5])
					];
				} else if (empty($parent_id_one)) {
					$errors .= '<div>Dòng [' . ($key + 1) . '] thêm hoặc cập nhật không được vì không tìm thấy loại đề xuất</div>';
				}

				if (!empty($value[1]) && !empty($parent_id_one)) {
					$parent_id_two = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[1])), 'UTF-8');
					$dataArray[$parent_id_one]['items'][$parent_id_two] = [
						'code' => preg_replace('/\s+/', ' ', trim($value[1])),
						'name' => preg_replace('/\s+/', ' ', trim($value[2])),
						'type' => !empty($value[15]) ? $listType[mb_strtoupper(trim($value[15]), 'UTF-8')] : NULL,
						'note' => trim($value[5])
					];
				} else if (!empty($value[1]) && empty($parent_id_one)) {
					$errors .= '<div>Dòng [' . ($key + 1) . '] thêm hoặc cập nhật không được vì không tìm thấy mã nhóm đề xuất</div>';
				}

				if (!empty($value[6]) && !empty($parent_id_one)) {
					$parent_id_process = preg_replace('/\s+/', ' ', trim($value[6]));
					$dataArray[$parent_id_one]['process'][] = [
						'name' => preg_replace('/\s+/', ' ', trim($value[6])),
						'bod' => !empty($value[7]) ? preg_replace('/\s+/', ' ', trim($value[7])) : 0,
						'stages' =>  0,
						'roles' => !empty($value[8]) ? preg_replace('/\s+/', ' ', trim($value[8])) : 0,
					];
				}
				if ((!empty($value[9]) || !empty($value[13]) || !empty($value[14])) && !empty($parent_id_process)) {
					$dataArray[$parent_id_one]['process'][$parent_id_process]['process_child'][] = [
						'name' => preg_replace('/\s+/', ' ', trim($value[9])),
						'approval_standards' => preg_replace('/\s+/', ' ', trim($value[13])),
						'completion_control_standards' => preg_replace('/\s+/', ' ', trim($value[14])),
						'minus_point' => !empty($value[10]) ? preg_replace('/\s+/', ' ', trim($value[10])) : 0,
						'plus_point' => !empty($value[11]) ? preg_replace('/\s+/', ' ', trim($value[11])) : 0,
						'warning' => !empty($value[12]) ? preg_replace('/\s+/', ' ', trim($value[12])) : '',
					];
				}
				if (!empty($value[3]) && !empty($parent_id_two)) {
					$parent_id_three = mb_strtoupper(preg_replace('/\s+/', ' ', trim($value[3])), 'UTF-8');
					$dataArray[$parent_id_one]['items'][$parent_id_two]['items'][] = [
						'code' => preg_replace('/\s+/', ' ', trim($value[3])),
						'name' => preg_replace('/\s+/', ' ', trim($value[4])),
						'type' => !empty($value[15]) ? $listType[mb_strtoupper(trim($value[15]), 'UTF-8')] : NULL,
						'note' => trim($value[5])
					];
				} else if (!empty($value[3]) && !empty($parent_id_two)) {
					$errors .= '<div>Dòng [' . ($key + 1) . '] thêm hoặc cập nhật không được vì không tìm thấy mã chi tiết</div>';
				}
			}
			$data_update_batch = [];
			foreach ($dataArray as $key => $value) {
				$this->db->where('code', $value['code']);
				$recommended_list = $this->db->get('tbl_recommended_list')->row();
				$id_parent = '';
				if (!empty($recommended_list)) {
					$id_parent = $recommended_list->id;
					$data_update_batch[] = [
						'id' => $recommended_list->id,
						'code' => $value['code'],
						'type' => $value['type'],
						'note' => $value['note']
					];
				} else {
					$success = $this->db->insert('tbl_recommended_list', [
						'parent_id' => 0,
						'code' => $value['code'],
						'name' => $value['name'],
						'note' => $value['note'],
						'type' => $value['type'],
					]);
					if (!empty($success)) {
						$id_parent = $this->db->insert_id();
						$count++;
					}
				}
				if (!empty($id_parent) && !empty($value['process'])) {
					$checkbod = 0;
					$checkstages = 0;
					$checkerrors = 0;
					foreach ($value['process'] as $kprocess => $vprocess) {
						if (is_numeric($kprocess)) {
							if (empty($vprocess['bod'])) {
								$errors .= '<div>Quy trình thuộc Đề xuất ' . $value['name'] . ' Phải có người duyệt đầy đủ</div>';
								$checkerrors = 1;
							}
							if ($checkerrors == 0) {
								if ($vprocess['bod'] != 1 && $vprocess['bod'] != 2 && $vprocess['bod'] != 3 && $vprocess['bod'] != 4 && $vprocess['bod'] != 5 && $vprocess['bod'] != 6 && $vprocess['bod'] != 7 && $vprocess['bod'] != 8 && $vprocess['bod'] != 9) {
									$errors .= '<div>Quy trình thuộc Đề xuất ' . $value['name'] . ' Phải là 1,2,3,4,5,6,7,8,9</div>';
									$checkerrors = 1;
								}
							}
							if ($checkerrors == 0) {
								if ($vprocess['bod'] == 1) {
									if ($checkbod == 1) {
										$errors .= '<div>Đề xuất ' . $value['name'] . ' Quy trình chỉ có 1 Người duyệt thực thi</div>';
										$checkerrors = 1;
									} else {
										$checkbod = 1;
									}
								}
								// if (!empty($vprocess['stages'])) {
								// 	if ($checkstages == 1) {
								// 		$errors .= '<div>Đề xuất ' . $value['name'] . ' Quy trình chỉ có 1 mã công đoạn</div>';
								// 		$checkerrors = 1;
								// 	} else {
								// 		$check_stages = get_table_where('tbl_stages', array('code' => $vprocess['stages']), '', 'row_array');
								// 		if (!empty($check_stages)) {
								// 			$value['process'][$kprocess]['stages'] = $check_stages['id'];
								// 			$checkstages = 1;
								// 		} else {
								// 			$errors .= '<div>Đề xuất ' . $value['name'] . ' Mã công đoạn ' . $vprocess['stages'] . ' không tìm thấy</div>';
								// 			$checkerrors = 1;
								// 		}
								// 	}
								// }
							}
						}
					}

					// if ($checkstages == 0) {
					// 	$errors .= '<div>Đề xuất ' . $value['name'] . ' Quy trình phải có 1 mã công đoạn</div>';
					// 	$checkerrors = 1;
					// }
					if ($checkbod == 0) {
						$errors .= '<div>Đề xuất ' . $value['name'] . ' Quy trình phải có 1 Người duyệt thực thi</div>';
						$checkerrors = 1;
					}
					if ($checkerrors == 0) {
						$this->db->where('recommended_list_id', $id_parent);
						$this->db->delete('tbl_recommended_list_process');
						$this->db->where('recommended_list_id', $id_parent);
						$this->db->delete('tbl_recommended_list_process_child');
						$id_recommended_list_process = 0;
						foreach ($value['process'] as $kprocess => $vprocess) {
							if (is_numeric($kprocess)) {
								$roles = '';
								if (!empty($vprocess['roles'])) {
									$role = get_table_where('tblroles', array('code_role' => $vprocess['roles'], 'type' => 0), '', 'row_array');
									if (!empty($role)) {
										$roles = $role['roleid'];
									}
								}
								$success = $this->db->insert('tbl_recommended_list_process', [
									'recommended_list_id' => $id_parent,
									'name' => $vprocess['name'],
									'bod' => $vprocess['bod'],
									'roles' => $roles,
									'stages' => $vprocess['stages'],
								]);
								$id_recommended_list_process = $this->db->insert_id();
								if (!empty($id_recommended_list_process)) {
									if (!empty($value['process'][$vprocess['name']])) {
										foreach ($value['process'][$vprocess['name']]['process_child'] as $kprocess_child => $vprocess_child) {
											$success = $this->db->insert('tbl_recommended_list_process_child', [
												'recommended_list_id' => $id_parent,
												'id_recommended_list_process' => $id_recommended_list_process,
												'name' => $vprocess_child['name'],
												'approval_standards' => $vprocess_child['approval_standards'],
												'completion_control_standards' => $vprocess_child['completion_control_standards'],
												'minus_point' => $vprocess_child['minus_point'],
												'plus_point' => $vprocess_child['plus_point'],
												'warning' => $vprocess_child['warning'],
											]);
										}
									}
								}
							}
							if (!empty($success)) {
								$count++;
							}
						}
					}
				}
				if (!empty($id_parent) && !empty($value['items'])) {
					foreach ($value['items'] as $kone => $vone) {
						$this->db->where('code', $vone['code']);
						$recommended_list_one = $this->db->get('tbl_recommended_list')->row();
						$id_parent_one = '';
						if (!empty($recommended_list_one)) {
							$id_parent_one = $recommended_list_one->id;

							$data_update_batch[] = [
								'id' => $recommended_list_one->id,
								'parent_id' => $id_parent,
								'code' => $vone['code'],
								'name' => $vone['name'],
								'type' => $vone['type'],
								'note' => $vone['note']
							];
						} else {
							$success = $this->db->insert('tbl_recommended_list', [
								'parent_id' => $id_parent,
								'code' => $vone['code'],
								'name' => $vone['name'],
								'note' => $vone['note'],
								'type' => $vone['type'],
							]);
							if (!empty($success)) {
								$id_parent_one = $this->db->insert_id();
								$count++;
							}
						}


						if (!empty($id_parent_one) && !empty($vone['items'])) {
							foreach ($vone['items'] as $ktwo => $vtwo) {
								$this->db->where('code', $vtwo['code']);
								$recommended_list_two = $this->db->get('tbl_recommended_list')->row();
								if (!empty($recommended_list_two)) {
									$data_update_batch[] = [
										'id' => $recommended_list_two->id,
										'parent_id' => $id_parent_one,
										'code' => $vtwo['code'],
										'name' => $vtwo['name'],
										'type' => $vtwo['type'],
										'note' => $vtwo['note']
									];
								} else {
									$this->db->insert('tbl_recommended_list', [
										'parent_id' => $id_parent_one,
										'code' => $vtwo['code'],
										'name' => $vtwo['name'],
										'note' => $vtwo['note'],
										'type' => $vtwo['type'],
									]);
								}
							}
						}
					}
				}
			}
			$count_update = 0;
			if (!empty($data_update_batch)) {
				$this->db->update_batch('tbl_recommended_list', $data_update_batch, 'id');
				$count_update = $this->db->affected_rows();
			}
			echo json_encode(
				[
					'success' => true,
					'errors' => $errors,
					'alert_type' => 'success',
					'message' => 'Import Thêm mới thành công ' . $count . ' dòng và cập nhật ' . $count_update . ' dòng',
				]
			);
			die();



			//            $errors = '';
			//            $rows = 1;
			//            foreach ($arraydata as $key => $row) {
			//                $rows++;
			//                $code_parent = trim($row[0]);
			//                $code = trim($row[1]);
			//                $name = trim($row[2]);
			//                $type = trim($row[3]);
			//                $note = trim($row[4]);
			//
			//                $type_id = 0;
			//                if (!empty($type)) {
			//                    if ($type != 'Ngày' && $type != 'Tháng' && $type != 'Năm') {
			//                        $errors.= '<div>Dòng ['.$rows.'] thêm không được vì không đúng loại</div>';
			//                        continue;
			//                    } else if ($type == 'Ngày') {
			//                        $type_id = 1;
			//                    } else if ($type == 'Tháng') {
			//                        $type_id = 2;
			//                    } else if ($type == 'Năm') {
			//                        $type_id = 3;
			//                    }
			//                }
			//
			//                $dtRecommended = $this->recommended_list_model->getRecommendedListByCode($code);
			//                if (!empty($dtRecommended)) {
			//                    $errors.= '<div>Dòng ['.$rows.'] thêm không được vì mã ['.$code.'] đã có trong phần mềm</div>';
			//                    continue;
			//                }
			//
			//                $parent_id = 0;
			//                if (!empty($code_parent)) {
			//                    $dtRecommended = $this->recommended_list_model->getRecommendedListByCode($code_parent);
			//                    if (empty($dtRecommended)) {
			//                        $errors.= '<div>Dòng ['.$rows.'] thêm không được vì mã đề xuất cha không có trong phần mềm</div>';
			//                        continue;
			//                    }
			//                    $parent_id = $dtRecommended['id'];
			//                }
			//
			//                $options = [
			//                    'parent_id' => $parent_id,
			//                    'code' => $code,
			//                    'name' => $name,
			//                    'note' => $note,
			//                    'type' => $type_id,
			//                ];
			//                $rs = $this->recommended_list_model->insertRecommendedList($options);
			//                if ($rs) {
			//                    $count++;
			//                }
			//            }
		}
		echo json_encode([
			'success' => true,
			'errors' => $errors,
			'alert_type' => 'success',
			'message' => 'Import thành công ' . $count . ' dòng',
		]);
		die();
	}

	function delete($id)
	{
		$data = [];
		if ($id) {
			$this->db->from('tblinternal_proposal');
			$this->db->where('tblinternal_proposal.recommended_list_group_id', $id);
			$this->db->or_where('tblinternal_proposal.recommended_list_id', $id);
			$isUse = $this->db->count_all_results();
			if (!empty($isUse)) {
				$data['result'] = 0;
				$data['message'] = lang('tnh_exist_not_delete');
				echo json_encode($data);
				return;
			}

			if ($this->recommended_list_model->deleteRecommendedList($id)) {
				$data['result'] = 1;
				$data['message'] = lang('success');
			} else {
				$data['result'] = 0;
				$data['message'] = lang('fail');
			}
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function export_excel()
	{
		if (!has_permission('roles', '', 'export')) {
			access_denied();
		}
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');

		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		$style_excel['Background_header_one'] = $style_excel['Background_header'];
		$style_excel['Background_header_one']['fill']['color']['rgb'] = '81dcf7';

		$style_excel['Background_header_two'] = $style_excel['Background_header'];
		$style_excel['Background_header_two']['fill']['color']['rgb'] = 'f79e83';

		$style_excel['Background_header_three'] = $style_excel['Background_header'];
		$style_excel['Background_header_three']['fill']['color']['rgb'] = '8ac78c';
		$style_excel['Background_header']['font']['bold'] = true;
		$style_excel['Background_header']['fill']['color']['rgb'] = 'fef7e2';


		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(35);

		$s = 0;
		$numberRow = 1;
		for ($i = 1; $i <= 16; $i++) {
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", (string)$i)->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
			$s++;
		}

		$numberRow++;
		$s = 0;
		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'LOẠI ĐỀ XUẤT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'MÃ NHÓM ĐỀ XUẤT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN NHÓM ĐỀ XUẤT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'MÃ CHI TIẾT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN CHI TIẾT')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN CÔNG VIỆC')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'TÊN QUY TRÌNH')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '(QUY TRÌNH NGƯỜI DUYỆT)')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'MÃ VỊ TRÍ')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'QUY CHUẨN CÔNG VIỆC')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'ĐIỂM TRỪ')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'ĐIỂM CỘNG')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'CẢNH BÁO')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'QUY CHUẨN KIỂM TRA')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'QUY CHUẨN KIỂM SOÁT HOÀN THÀNH')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", 'LOẠI (Ngày, Tháng, Năm)')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header']);
		$s++;

		$numberRow++;


		$this->db->select('(
			SELECT 1
			FROM tbl_recommended_list tbl_child
			WHERE tbl_child.parent_id = tbl_recommended_list.id
			limit 1
		) as count_child');
		$this->db->select('tbl_recommended_list.*');
		$this->db->where('parent_id', 0);
		$this->db->where('type_show', 1);
		$recommended_list_one = $this->db->get('tbl_recommended_list')->result_array();

		foreach ($recommended_list_one as $key => $value) {
			$s = 0;
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $value['code'])->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;
			$sDefault = $s;
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", '')->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $value['note'])->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;
			for ($c = 6; $c <= 14; $c++) {
				$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$c]$numberRow", '')->getStyle("$cloumns_excel[$c]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			}
			$s = 15;
			$viewType = '';
			if (!empty($value['type'])) {
				if ($value['type'] == 1) {
					$viewType = 'Ngày';
				} else if ($value['type'] == 2) {
					$viewType = 'Tháng';
				} else if ($value['type'] == 3) {
					$viewType = 'Năm';
				}
			}
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$s]$numberRow", $viewType)->getStyle("$cloumns_excel[$s]$numberRow")->applyFromArray($style_excel['Background_header_one']);
			$s++;

			$numberRow++;
			if (!empty($value['count_child'])) {
				$this->db->select('(
					SELECT 1
					FROM tbl_recommended_list tbl_child
					WHERE tbl_child.parent_id = tbl_recommended_list.id
					limit 1
				) as count_child');
				$this->db->select('tbl_recommended_list.*');
				$this->db->where('parent_id', $value['id']);
				$recommended_list_two = $this->db->get('tbl_recommended_list')->result_array();


				$this->db->select('tbl_recommended_list_process.*');
				$this->db->where('recommended_list_id', $value['id']);
				$this->db->order_by('id', 'asc');
				$this->db->order_by('id');
				$_recommended_list_process = $this->db->get('tbl_recommended_list_process')->result_array();

				$numberRowprocess = $numberRow - 1;
				if (!empty($_recommended_list_process)) {
					$sprocess = $sDefault + 5; // index 6 (G)
					foreach ($_recommended_list_process as $kk => $vv) {
						$this->db->select('tbl_recommended_list_process_child.*');
						$this->db->where('id_recommended_list_process', $vv['id']);
						$this->db->order_by('id', 'asc');
						$this->db->order_by('id');
						$_recommended_list_process_child = $this->db->get('tbl_recommended_list_process_child')->result_array();
						$indexprocess = $sprocess; // index 6 (G)
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", $vv['name'])->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						
						$indexprocess++; // index 7 (H)
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", $vv['bod'])->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						
						$indexprocess++; // index 8 (I)
						$roles = '';
						if (!empty($vv['roles'])) {
							$roles = get_table_where('tblroles', ['roleid' => $vv['roles']], '', 'row_array');
						}
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", (!empty($roles) ? $roles['code_role'] : ''))->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						
						$indexprocess++; // index 9 (J) - child name
						if (!empty($_recommended_list_process_child)) {
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", $_recommended_list_process_child[0]['name'])->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						} else {
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", '')->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						}
						
						$indexprocess++; // index 10 (K) - minus_point
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", (!empty($_recommended_list_process_child) ? $_recommended_list_process_child[0]['minus_point'] : ''))->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						
						$indexprocess++; // index 11 (L) - plus_point
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", (!empty($_recommended_list_process_child) ? $_recommended_list_process_child[0]['plus_point'] : ''))->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						
						$indexprocess++; // index 12 (M) - warning
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", (!empty($_recommended_list_process_child) ? $_recommended_list_process_child[0]['warning'] : ''))->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						
						$indexprocess++; // index 13 (N) - approval_standards
						if (!empty($_recommended_list_process_child)) {
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", $_recommended_list_process_child[0]['approval_standards'])->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						} else {
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", '')->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						}
						
						$indexprocess++; // index 14 (O) - completion_control_standards
						if (!empty($_recommended_list_process_child)) {
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", $_recommended_list_process_child[0]['completion_control_standards'])->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						} else {
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess]$numberRowprocess", '')->getStyle("$cloumns_excel[$indexprocess]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
						}
						
						$numberRowprocess++;
						if (!empty($_recommended_list_process_child)) {
							unset($_recommended_list_process_child[0]);
							foreach ($_recommended_list_process_child as $kkc => $vvchild) {
								$indexprocess_child = 9; // J - child name
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess_child]$numberRowprocess", $vvchild['name'])->getStyle("$cloumns_excel[$indexprocess_child]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
								
								$indexprocess_child++; // K - minus_point
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess_child]$numberRowprocess", $vvchild['minus_point'])->getStyle("$cloumns_excel[$indexprocess_child]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
								
								$indexprocess_child++; // L - plus_point
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess_child]$numberRowprocess", $vvchild['plus_point'])->getStyle("$cloumns_excel[$indexprocess_child]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
								
								$indexprocess_child++; // M - warning
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess_child]$numberRowprocess", $vvchild['warning'])->getStyle("$cloumns_excel[$indexprocess_child]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
								
								$indexprocess_child++; // N - approval_standards
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess_child]$numberRowprocess", $vvchild['approval_standards'])->getStyle("$cloumns_excel[$indexprocess_child]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
								
								$indexprocess_child++; // O - completion_control_standards
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$indexprocess_child]$numberRowprocess", $vvchild['completion_control_standards'])->getStyle("$cloumns_excel[$indexprocess_child]$numberRowprocess")->applyFromArray($style_excel['BStyle_left']);
								
								$numberRowprocess++;
							}
						}
					}
				}
				foreach ($recommended_list_two as $ktwo => $vtwo) {
					$sTwo = $sDefault;
					for ($i = 0; $i < ($sTwo); $i++) {
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '')->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
					}
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", $vtwo['code'])->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['Background_header_two']);
					$sTwo++;
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", $vtwo['name'])->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['Background_header_two']);
					$sTwo++;
					$sTwoDefault = $sTwo;
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", '')->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['BStyle_center']);
					$sTwo++;
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", '')->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['BStyle_center']);
					$sTwo++;
					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", '')->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['BStyle_center']);
					$sTwo++;
					for ($c = 7; $c <= 14; $c++) {
						$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$c]$numberRow", '')->getStyle("$cloumns_excel[$c]$numberRow")->applyFromArray($style_excel['BStyle_center']);
					}
					$sTwo = 15;
					$viewType = '';
					if (!empty($vtwo['type'])) {
						if ($vtwo['type'] == 1) {
							$viewType = 'Ngày';
						} else if ($vtwo['type'] == 2) {
							$viewType = 'Tháng';
						} else if ($vtwo['type'] == 3) {
							$viewType = 'Năm';
						}
					}

					$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sTwo]$numberRow", $viewType)->getStyle("$cloumns_excel[$sTwo]$numberRow")->applyFromArray($style_excel['BStyle_center']);
					$sTwo++;
					$numberRow++;
					if (!empty($vtwo['count_child'])) {
						$this->db->select('(
							SELECT 1
							FROM tbl_recommended_list tbl_child
							WHERE tbl_child.parent_id = tbl_recommended_list.id
							limit 1
						) as count_child');
						$this->db->select('tbl_recommended_list.*');
						$this->db->where('parent_id', $vtwo['id']);
						$recommended_list_three = $this->db->get('tbl_recommended_list')->result_array();
						foreach ($recommended_list_three as $kthree => $vthree) {
							$sThree = $sTwoDefault;
							for ($i = 0; $i < $sTwoDefault; $i++) {
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$i]$numberRow", '')->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
							}
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sThree]$numberRow", $vthree['code'])->getStyle("$cloumns_excel[$sThree]$numberRow")->applyFromArray($style_excel['BStyle_center']);
							$sThree++;
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sThree]$numberRow", $vthree['name'])->getStyle("$cloumns_excel[$sThree]$numberRow")->applyFromArray($style_excel['BStyle_center']);
							$sThree++;
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sThree]$numberRow", '')->getStyle("$cloumns_excel[$sThree]$numberRow")->applyFromArray($style_excel['BStyle_center']);
							$sThree++;
							for ($c = 7; $c <= 14; $c++) {
								$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$c]$numberRow", '')->getStyle("$cloumns_excel[$c]$numberRow")->applyFromArray($style_excel['BStyle_center']);
							}
							$sThree = 15;
							$viewType = '';
							if (!empty($vthree['type'])) {
								if ($vthree['type'] == 1) {
									$viewType = 'Ngày';
								} else if ($vthree['type'] == 2) {
									$viewType = 'Tháng';
								} else if ($vthree['type'] == 3) {
									$viewType = 'Năm';
								}
							}
							$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$sThree]$numberRow", $viewType)->getStyle("$cloumns_excel[$sThree]$numberRow")->applyFromArray($style_excel['BStyle_center']);
							$sThree++;
							$numberRow++;
						}
					}
				}
				if ($numberRowprocess > $numberRow) {
					for ($ij = $numberRow; $ij < $numberRowprocess; $ij++) {
						$numberRow++;
						for ($i = 0; $i <= 15; $i++) {
							$objPHPExcel->getActiveSheet()->getStyle("$cloumns_excel[$i]$numberRow")->applyFromArray($style_excel['BStyle_center']);
						}
					}
				}
			}
		}
		$objPHPExcel->getActiveSheet()->getStyle('G1:P' . ($numberRow - 1))->applyFromArray($style_excel['BStyle_center']);
		$filename = lang('DS_danh_muc_de_xuat') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function submit($id = null)
	{
		if ($this->input->post()) {
			$formData = $this->input->post();
			if (isset($formData['is_excel'])) {
				$formData['is_excel'] = 1;
				if (!empty($_FILES['excel_file']['tmp_name'])) {
					require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
					$fullfile = $_FILES['excel_file']['tmp_name'];
					$inputFileType = PHPExcel_IOFactory::identify($fullfile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load("$fullfile");
					$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
					
					$highestColumn = $objWorksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					$highestRow = $objWorksheet->getHighestRow();
					
					$headerRow = 1;
					for ($r = 1; $r <= min(15, $highestRow); $r++) {
						$hasSTT = false;
						$rowValuesCount = 0;
						for ($col = 0; $col < $highestColumnIndex; $col++) {
							$cellValue = trim($objWorksheet->getCellByColumnAndRow($col, $r)->getValue());
							if ($cellValue !== '') {
								$rowValuesCount++;
								if (strcasecmp($cellValue, 'STT') === 0) {
									$hasSTT = true;
								}
							}
						}
						if ($hasSTT || $rowValuesCount > 3) {
							$headerRow = $r;
							break;
						}
					}
					
					$headers = [];
					for ($col = 0; $col < $highestColumnIndex; $col++) {
						$cellValue = $objWorksheet->getCellByColumnAndRow($col, $headerRow)->getValue();
						if ($cellValue !== null && $cellValue !== '') {
							$headers[] = trim($cellValue);
						}
					}
					$formData['excel'] = json_encode($headers, JSON_UNESCAPED_UNICODE);
				}
			} else {
				$formData['is_excel'] = 0;
				$formData['excel'] = '';
			}

			if (empty($id)) { // insert
				$this->db->insert('tbl_recommended_list', $formData);
				$submitId = $this->db->insert_id();
			} else {
				$this->db->update('tbl_recommended_list', $formData, ['id' => $id]);
				$submitId = $id;
			}

			$alert_type = false;
			$message = _l('Lưu thất bại');
			if ($submitId) {
				$alert_type = true;
				$message = _l('Lưu thành công');
			}
			echo json_encode(array(
				'isSuccess' => $alert_type,
				'message' => $message
			));
		} else {
			$parent_id = $this->input->get('parent_id') ?? null;

			if (!empty($parent_id)) {
				$data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['id' => $parent_id]);
			} else {
				$data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['parent_id' => 0]);
			}

			$data['arrType'] = [];
			$arrType = getTypeCategoryTasks();
			foreach ($arrType as $code => $name) {
				$data['arrType'][] = ['code' => $code, 'name' => $name];
			}

			if (!empty($id)) {
				$data['value'] = get_table_where('tbl_recommended_list', ['id' => $id], '', 'row_array');
				$data['id'] = $id;
				if (!empty($data['value']['parent_id'])) {
					$data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['id' => $data['value']['parent_id']]);
				}
			} else {
				$data['value']['parent_id'] = $parent_id;
			}

			$data['title'] = _l('tnh_recommended_list');
			$data['type_plan_propose'] = type_plan_propose();
			$this->load->view('admin/recommended_list/submit', $data);
		}
	}
	public function submit_parent($id = null)
	{
		if ($this->input->post()) {
			$formData = $this->input->post();
			if (empty($id)) { // insert
				$this->db->insert('tbl_recommended_list', $formData);
				$submitId = $this->db->insert_id();
			} else {
				$this->db->update('tbl_recommended_list', $formData, ['id' => $id]);
				$submitId = $id;
			}

			$alert_type = false;
			$message = _l('Lưu thất bại');
			if ($submitId) {
				$alert_type = true;
				$message = _l('Lưu thành công');
			}
			echo json_encode(array(
				'isSuccess' => $alert_type,
				'message' => $message
			));
		} else {
			$parent_id = $this->input->get('parent_id') ?? null;

			if (!empty($parent_id)) {
				$data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['id' => $parent_id]);
			} else {
				$data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['parent_id' => 0]);
			}

			$data['arrType'] = [];
			$arrType = getTypeCategoryTasks();
			foreach ($arrType as $code => $name) {
				$data['arrType'][] = ['code' => $code, 'name' => $name];
			}

			if (!empty($id)) {
				$data['value'] = get_table_where('tbl_recommended_list', ['id' => $id], '', 'row_array');
				$data['id'] = $id;
				if (!empty($data['value']['parent_id'])) {
					$data['arrRecommendedList'] = get_table_where('tbl_recommended_list', ['id' => $data['value']['parent_id']]);
				}
			} else {
				$data['value']['parent_id'] = $parent_id;
			}

			$data['title'] = _l('tnh_recommended_list');
			$data['type_plan_propose'] = type_plan_propose();
			$this->load->view('admin/recommended_list/submit_parent', $data);
		}
	}

	public function setCategoryRecomended()
	{
		$data = $this->input->post();
		$this->db->set('category_recommended_id', $data['category_recommended_id']);
		$this->db->where('id', $data['recommended_id']);
		$this->db->update('tbl_recommended_list');
		echo json_encode(array(
			'success' => 1,
			'message' => _l('add_slide_true'),
			'alert_type' => 'success'
		));
		die;
	}
}
