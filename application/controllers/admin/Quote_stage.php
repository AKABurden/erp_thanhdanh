<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Quote_stage extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->HasView = has_permission('quote_stage', '', 'view');
	}

	public function index()
	{
		if (!$this->HasView) {
			access_denied();
		}
		$data['title'] = _l('Bảng giá công đoạn');
		$data['data_client'] = $this->db->get('tblclients')->result_array();
		$data['quote_stage'] = $this->db->get('tbl_stage_quote')->result_array();
		$this->load->view('admin/quote_stage/manage', $data);
	}

	public function table()
	{
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$aColumns = [
			'tbl_stage_quote.id as id',
			'tbl_stage_quote.code as code',
			'tbl_stage_quote.name as name',
			'(
				SELECT GROUP_CONCAT(tblclients.company_short SEPARATOR ", ") 
				FROM tbl_stage_quote_client 
			    JOIN tblclients ON tblclients.userid = tbl_stage_quote_client.id_client
			    WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote.id
			 ) as list_client',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_stage_quote';
		$where = [];

		if ($this->input->post('quote_stage_search')) {
			$where[] = 'AND tbl_stage_quote.id = "' . $this->input->post('quote_stage_search') . '"';
		}
		if ($this->input->post('client_search')) {
			$where[] = 'AND EXISTS (
				SELECT 1
				FROM tbl_stage_quote_client 
			    WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote.id
			    AND tbl_stage_quote_client.id_client = "' . $this->input->post('client_search') . '"
			 )';
		}

		$join = [];

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tbl_stage_quote.id'
		]);
		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$row[] = ($key + 1);
			$row[] = '<a class="c_modal" href="' . admin_url('quote_stage/view/' . $aRow['id']) . '">' . $aRow['code'] . '</a>';
			$row[] = $aRow['name'];
			$row[] = '<div style="width: 450px;white-space: break-spaces;">' . $aRow['list_client'] . '</div>';

			$options = '';
			$options .= '<a class="btn btn-icon btn-default" data-toggle="tooltip" data-title="Sửa bảng giá công đoạn" href="' . admin_url('quote_stage/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-icon btn-default c_modal" data-toggle="tooltip" data-title="Xem chi tiết bảng giá công đoạn"  href="' . admin_url('quote_stage/view/' . $aRow['id']) . '"><i class="fa fa-eye"></i></a>';
			$options .= '<a class="btn btn-icon btn-warning c_modal" data-toggle="tooltip" data-title="Thêm khách hàng áp dụng bảng giá công đoạn" href="' . admin_url('quote_stage/add_stage_quote_customer/' . $aRow['id']) . '"><i class="fa fa-user-circle-o"></i></a>';
			$options .= '<a class="btn btn-icon btn-primary"  data-toggle="tooltip" data-title="Copy và tạo bảng giá công đoạn mới"  href="' . admin_url('quote_stage/detail?copy=' . $aRow['id']) . '"><i class="fa fa-files-o"></i></a>';
			$options .= '<a class="btn btn-icon btn-danger deleteQuoteStage"  data-toggle="tooltip" data-title="Xóa bảng giá công đoạn"   data-id="' . $aRow['id'] . '"><i class="fa fa-remove"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);
		die();
	}

	public function view($id = '')
	{
		$data['title'] = 'Xem chi tiết bảng giá công đoạn';
		$data['quote_stage'] = $this->db->get_where('tbl_stage_quote', ['id' => $id])->row();
		if (!empty($data['quote_stage'])) {
			$this->db->select('tbl_stage_quote_detail.*, tbl_stages.name as name_stages, tbl_stages.code as code_stages, tbl_category_stages.code as code_category, tblunits.unit, tbl_category_stages.name as name_category');
			$this->db->where('id_stage_quote', $id);
			$this->db->join('tbl_stages', 'tbl_stages.id = tbl_stage_quote_detail.id_stage');
			$this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages', 'left');
			$this->db->join('tblunits', 'tblunits.unitid = tbl_stage_quote_detail.unit_id', 'left');
			$data['quote_stage']->items = $this->db->get('tbl_stage_quote_detail')->result_array();


			$this->db->select('tblclients.userid, tblclients.zcode, tblclients.company_short');
			$this->db->where('id_stage_quote', $id);
			$this->db->join('tblclients', 'tblclients.userid = tbl_stage_quote_client.id_client');
			$data['quote_stage']->items_client = $this->db->get('tbl_stage_quote_client')->result_array();
		}
		$this->load->view('admin/quote_stage/modal_view', $data);
	}

	public function add_stage_quote_customer($id = '')
	{
		if ($this->input->post()) {
			$list_client = $this->input->post('list_client');
			$this->db->where('id_stage_quote', $id);
			$this->db->delete('tbl_stage_quote_client');
			//			$arrayIsset = [];
			if (!empty($list_client)) {
				foreach ($list_client as $key => $value) {
					//					$this->db->select('tblclients.company_short');
					//					$this->db->join('tblclients', 'tblclients.userid = tbl_stage_quote_client.id_client');
					//					$kt_quote_client = $this->db->get_where('tbl_stage_quote_client', [
					//						'id_client' => $value,
					//					])->row();
					//					if(!empty($kt_quote_client)) {
					//						$arrayIsset[] = $kt_quote_client->company_short;
					//						continue;
					//					}

					$this->db->insert('tbl_stage_quote_client', [
						'id_client' => $value,
						'id_stage_quote' => $id,
					]);
				}
			}
			echo json_encode([
				'success' => true,
				'alert_type' => 'success',
				//				'message' => 'Cập nhật khách hàng được áp dụng bảng giá thành công ' . (!empty($arrayIsset) ? 'và khách hàng ' . implode(',', $arrayIsset) .' đã tồn tại ở bảng giá khác không thể thêm' : '')
				'message' => 'Cập nhật khách hàng được áp dụng bảng giá thành công'
			]);
			die();
		} else {
			$data['title'] = 'Cập nhật khách hàng áp dụng bảng giá';
			$this->db->select('GROUP_CONCAT(id_client) as list_client');
			$this->db->where('id_stage_quote', $id);
			$data['list_client'] = $this->db->get('tbl_stage_quote_client')->row('list_client');
			$data['list_client'] = !empty($data['list_client']) ? explode(',', $data['list_client']) : [];

			$this->db->select('userid, zcode, company_short, company');
			//			$this->db->where('NOT EXISTS (SELECT 1 FROM tbl_stage_quote_client WHERE tbl_stage_quote_client.id_stage_quote != '.$id.' AND tbl_stage_quote_client.id_client = tblclients.userid)');
			$data['data_client'] = $this->db->get('tblclients')->result_array();

			$data['id'] = $id;
			$this->load->view('admin/quote_stage/add_stage_quote_customer', $data);
		}
	}

	public function detail($id = '')
	{
		if ($id && !has_permission('quote_stage', '', 'edit')) {
			access_denied();
		}

		if ($this->input->post()) {
			if (!$this->HasView) {
				ajax_access_denied();
			}
			$data = $this->input->post();
			$items = $data['items'];
			unset($data['items']);
			if (!empty($id)) {
				$this->db->where('id != "' . $id . '"', false, false);
				$this->db->where('code', $data['code']);
				$ktIsset = $this->db->get('tbl_stage_quote')->row();
				if (!empty($ktIsset)) {
					echo json_encode(['success' =>  false, 'alert_type' => 'danger', 'message' => 'Mã bảng giá công đoạn đã tồn tại vui lòng chọn mã bảng giá khác']);
					die();
				}

				$this->db->where('id', $id);
				$success = $this->db->update('tbl_stage_quote', [
					'code' => $data['code'],
					'name' => $data['name'],
					'cost_of_brand' => !empty($data['cost_of_brand']) ? number_unformat($data['cost_of_brand']) : 0,
					'labor_cost' => !empty($data['labor_cost']) ? number_unformat($data['labor_cost']) : 0,
					'loss_cost' => !empty($data['loss_cost']) ? number_unformat($data['loss_cost']) : 0,
					'profit' => !empty($data['profit']) ? number_unformat($data['profit']) : 0,
				]);
				if (!empty($success)) {
					$dataItems = [];
					$dataItemsUpdate = [];
					$id_not_delete = [];
					foreach ($items as $key => $value) {
						if (empty($value['id_stage'])) continue;
						if (!empty($value['id'])) {
							$dataItemsUpdate[] = [
								'id' => $value['id'],
								'id_stage' => $value['id_stage'],
								'unit_id' => $value['unit_id'],
								'height' => number_format_data($value['height'], false),
								'width' => number_format_data($value['width'], false),
								'price' => number_format_data($value['price'], false),
							];
							$id_not_delete[] = $value['id'];
						} else {
							$dataItems[] = [
								'id_stage_quote' => $id,
								'id_stage' => $value['id_stage'],
								'unit_id' => $value['unit_id'],
								'height' => number_format_data($value['height'], false),
								'width' => number_format_data($value['width'], false),
								'price' => number_format_data($value['price'], false),
							];
						}
					}
					if (!empty($dataItems)) {
						foreach ($dataItems as $key => $value) {
							$insert_detail = $this->db->insert('tbl_stage_quote_detail', $value);
							if (!empty($insert_detail)) {
								$id_not_delete[] = $this->db->insert_id();
							}
						}
					}
					if (!empty($dataItemsUpdate)) {
						$this->db->update_batch('tbl_stage_quote_detail', $dataItemsUpdate, 'id');
					}

					$this->db->where('id_stage_quote', $id);
					if (!empty($id_not_delete)) {
						$this->db->where_not_in('id', $id_not_delete);
					}
					$this->db->delete('tbl_stage_quote_detail');

					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Cập nhật bảng giá thành công'
					]);
					die();
				}
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => 'Cập nhật bảng giá không thành công'
				]);
				die();
			} else {
				$this->db->where('code', $data['code']);
				$ktIsset = $this->db->get('tbl_stage_quote')->row();
				if (!empty($ktIsset)) {
					echo json_encode(['success' =>  false, 'alert_type' => 'danger', 'message' => 'Mã bảng giá công đoạn đã tồn tại vui lòng chọn mã bảng giá khác']);
					die();
				}

				$success = $this->db->insert('tbl_stage_quote', [
					'code' => $data['code'],
					'name' => $data['name'],
					'cost_of_brand' => !empty($data['cost_of_brand']) ? number_unformat($data['cost_of_brand']) : 0,
					'labor_cost' => !empty($data['labor_cost']) ? number_unformat($data['labor_cost']) : 0,
					'loss_cost' => !empty($data['loss_cost']) ? number_unformat($data['loss_cost']) : 0,
					'profit' => !empty($data['profit']) ? number_unformat($data['profit']) : 0,
					'create_by' => get_staff_user_id(),
					'date_create' => date('Y-m-d H:i:s')
				]);
				if (!empty($success)) {
					$id = $this->db->insert_id();
					$dataItems = [];
					foreach ($items as $key => $value) {
						if (empty($value['id_stage'])) continue;
						$dataItems[] = [
							'id_stage_quote' => $id,
							'id_stage' => $value['id_stage'],
							'unit_id' => $value['unit_id'],
							'height' => number_format_data($value['height'], false),
							'width' => number_format_data($value['width'], false),
							'price' => number_format_data($value['price'], false),
						];
					}
					if (!empty($dataItems)) {
						$this->db->insert_batch('tbl_stage_quote_detail', $dataItems);
					}

					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Thêm bảng giá thành công'
					]);
					die();
				}

				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => 'Thêm bảng giá không thành công'
				]);
				die();
			}
		} else {
			if (!$this->HasView) {
				access_denied();
			}
			$data['title'] = _l('Thêm bảng giá công đoạn');
			if (!empty($id)) {
				$data['title'] = _l('Sửa bảng giá công đoạn');
				$this->db->where('id', $id);
				$data['quote_stage'] = $this->db->get('tbl_stage_quote')->row();
				if (!empty($data['quote_stage'])) {
					$this->db->select('tbl_stage_quote_detail.*, tbl_stages.name as name_stages, tbl_stages.code as code_stages, tblunits.unit');
					$this->db->where('id_stage_quote', $id);
					$this->db->join('tbl_stages', 'tbl_stages.id = tbl_stage_quote_detail.id_stage');
					$this->db->join('tblunits', 'tblunits.unitid = tbl_stage_quote_detail.unit_id', 'left');
					$data['quote_stage']->items = $this->db->get('tbl_stage_quote_detail')->result_array();
				}
			} else if ($this->input->get('copy')) {
				$id_copy = $this->input->get('copy');
				$this->db->where('id', $id_copy);
				$data['quote_stage'] = $this->db->get('tbl_stage_quote')->row();
				if (!empty($data['quote_stage'])) {
					unset($data['quote_stage']->code);
					$this->db->select([
						'tbl_stage_quote_detail.id_stage',
						'tbl_stage_quote_detail.id_stage_quote',
						'tbl_stage_quote_detail.unit_id',
						'tbl_stage_quote_detail.height',
						'tbl_stage_quote_detail.width',
						'tbl_stage_quote_detail.price',
						'tbl_stages.name as name_stages',
						'tbl_stages.code as code_stages',
						'tblunits.unit'
					]);
					$this->db->where('id_stage_quote', $id_copy);
					$this->db->join('tbl_stages', 'tbl_stages.id = tbl_stage_quote_detail.id_stage');
					$this->db->join('tblunits', 'tblunits.unitid = tbl_stage_quote_detail.unit_id', 'left');
					$data['quote_stage']->items = $this->db->get('tbl_stage_quote_detail')->result_array();
				}
			}
			$this->load->view('admin/quote_stage/detail', $data);
		}
	}

	public function delete($id = '')
	{
		if ($id && !has_permission('quote_stage', '', 'edit')) {
			echo json_encode([
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Bạn không có quyền xóa dữ liệu này'
			]);
			die();
		}

		$this->db->where('id', $id);
		$success = $this->db->delete('tbl_stage_quote');
		if (!empty($success)) {

			$this->db->where('id_stage_quote', $id);
			$this->db->delete('tbl_stage_quote_client');

			$this->db->where('id_stage_quote', $id);
			$this->db->delete('tbl_stage_quote_detail');

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

	public function searchStage($id = '')
	{
		$data = [];
		$term = $this->input->get('term');
		$limit = 50;
		$data['results'] = [];
		if (empty($id)) {
			$this->db->select('tbl_stages.id as id, tbl_stages.code as text, tbl_stages.name as name', false);
			$this->db->from('tbl_stages');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tbl_stages.code', $term);
				$this->db->or_like('tbl_stages.name', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$data['results'] = $this->db->get()->result_array();
		} else {
			$this->db->select('tbl_stages.id as id, tbl_stages.code as text, tbl_stages.name as name', false);
			$this->db->from('tbl_stages');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tbl_stages.code', $term);
				$this->db->or_like('tbl_stages.name', $term);
				$this->db->group_end();
			}
			$this->db->where('id', $id);
			$this->db->limit($limit);
			$results = $this->db->get()->row_array();
			$data['row'] = ['id' => $results['id'], 'text' => $results['text'], 'name' => $results['name']];
		}
		echo json_encode($data);
		die();
	}

	public function searchUnit($id = '')
	{
		$data = [];
		$term = $this->input->get('term');
		$limit = 50;
		$data['results'] = [];
		if (empty($id)) {
			$this->db->select('tblunits.unitid as id, tblunits.unit as text', false);
			$this->db->from('tblunits');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tblunits.unit', $term);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$data['results'] = $this->db->get()->result_array();
		} else {
			$this->db->select('tblunits.unitid as id, tblunits.unit as text', false);
			$this->db->from('tblunits');
			if (!empty($term)) {
				$this->db->group_start();
				$this->db->like('tblunits.unit', $term);
				$this->db->group_end();
			}
			$this->db->where('unitid', $id);
			$this->db->limit($limit);
			$results = $this->db->get()->row_array();
			$data['row'] = ['id' => $results['id'], 'text' => $results['text']];
		}
		echo json_encode($data);
		die();
	}

	public function modal_excel()
	{
		if (!$this->HasView) {
			access_denied();
		}
		$data['title'] = _l('Import excel bảng giá công đoạn');
		$this->load->view('admin/quote_stage/import', $data);
	}

	public function import()
	{
		if (!$this->HasView) {
			ajax_access_denied();
		}
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
			$listRow = [
				0 => 'code_stage',
				1 => 'unit_id',
				2 => 'height',
				3 => 'width',
				4 => 'price',
			];

			$data = [];
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$vaKey = '';
				for ($i = 2; $i <= $highestRow; $i++) {
					for ($j = 0; $j < $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						$data[$i][$listRow[$j]] = trim($Val);
					}
				}
			}
		}
		$arrayData = [];
		$arrayList = [];
		$arrayStage = [];
		$arrayUnit = [];

		if (!empty($data)) {
			foreach ($data as $key => $value) {
				if (!empty($value['code_stage'])) {
					$arrayList[] = $value;
				}

				// if(empty($arrayStage[$value['code_stage']])) {
				// 	$stages = $this->db->get_where('tbl_stages', ['code' => $value['code_stage']])->row();
				// 	if(!empty($stages)) {
				// 		$arrayStage[$value['code_stage']] = $stages->id;
				// 	}
				// 	else {
				// 		continue;
				// 	}
				// }
				// if(empty($arrayUnit[$value['unit_id']])) {
				// 	$unit = $this->db->get_where('tblunits', ['unit' => $value['unit_id']])->row();
				// 	if(!empty($unit)) {
				// 		$arrayUnit[$value['unit_id']] = $unit->unitid;
				// 	}
				// }

				// if(empty($arrayStage[$value['code_stage']])) {
				$stages = $this->db->get_where('tbl_stages', ['code' => $value['code_stage']])->row();
				if (!empty($stages)) {
					$arrayStage[$value['code_stage']] = $stages->id;
				} else {
					continue;
				}
				// }
				// if(empty($arrayUnit[$value['unit_id']])) {
				$unit = $this->db->get_where('tblunits', ['unit' => $value['unit_id']])->row();
				if (!empty($unit)) {
					$arrayUnit[$value['unit_id']] = $unit->unitid;
				} else {
					continue;
				}
				// }

				$arrayData[] = [
					// 'id_stage' => $arrayStage[$value['code_stage']],
					// 'unit_id' => !empty($arrayUnit[$value['unit_id']]) ? $arrayUnit[$value['unit_id']] : NULL,

					'id_stage' => $stages->id,
					'stage_name' => $stages->name,
					'stage_code' => $stages->code,
					'unit_id' => $unit->unitid,
					'unit' => $unit->unit,
					'price' => number_format_data($value['price'], false),
					'height' => number_format_data($value['height'], false),
					'width' => number_format_data($value['width'], false),
				];
			}
		}
		echo json_encode([
			'success' => true,
			'alert_type' => 'success',
			'message' => 'Import thành công ' . count($arrayData) . '/' . count($arrayList),
			'data' => $arrayData
		]);
		die();
	}

	public function get_table()
	{
		$id_customer = $this->input->get('id_customer');
		if (!empty($id_customer)) {
			$this->db->where_in('userid', $id_customer);
			$this->db->select('zcode, userid, company_short');
			$this->db->order_by('userid', 'desc');
			$data['data_client'] = $this->db->get('tblclients')->result_array();
		}
		$this->load->view('admin/quote_stage/table', $data);
	}



	public function modal_excel_full()
	{
		if (!$this->HasView) {
			access_denied();
		}
		$data['title'] = _l('Import excel bảng giá công đoạn');
		$this->load->view('admin/quote_stage/import_full', $data);
	}

	public function import_full()
	{
		if (!$this->HasView) {
			ajax_access_denied();
		}
		ob_end_clean();
		ini_set('max_execution_time', 800);
		require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
		$this->load->helper('security');

		$data = [];
		$errors = [];
		$arrayData = [];
		$successCount = 0;

		if (!empty($_FILES['file'])) {
			$fullfile = $_FILES['file']['tmp_name'];
			$extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			if ($extension != 'XLSX' && $extension != 'XLS') {
				echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_not_type')]);
				die();
			}
			$inputFileType = PHPExcel_IOFactory::identify($fullfile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load("$fullfile");
			$total_sheets = $objPHPExcel->getSheetCount();

			$listRow = [
				0 => 'code',
				1 => 'name',
				2 => 'cost_of_brand',
				3 => 'labor_cost',
				4 => 'loss_cost',
				5 => 'profit',
				6 => 'code_category',
				7 => 'name_category',
				8 => 'code_stage',
				9 => 'name_stage',
				10 => 'unit_id',
				11 => 'height',
				12 => 'width',
				13 => 'price',
			];

			$rawData = [];
			$allSheetName = $objPHPExcel->getSheetNames();
			for ($sheet = 0; $sheet < $total_sheets; $sheet++) {
				$objWorksheet = $objPHPExcel->setActiveSheetIndex($sheet);
				$sheetName = isset($allSheetName[$sheet]) ? $allSheetName[$sheet] : ('Sheet ' . ($sheet + 1));
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				for ($i = 2; $i <= $highestRow; $i++) {
					$row = [];
					for ($j = 0; $j < $highestColumnIndex; $j++) {
						$Val = $objWorksheet->getCellByColumnAndRow($j, $i)->getValue();
						$row[$listRow[$j]] = trim($Val);
					}
					$row['__sheet_name'] = $sheetName;
					$row['__row_index'] = $i;
					$rawData[] = $row;
				}
			}

			// Nhóm theo dòng liên tiếp: dòng có code là đầu nhóm, các dòng phía dưới trống code cùng nhóm
			$groups = [];
			$currentGroup = null;
			foreach ($rawData as $row) {
				if (!empty($row['code'])) {
					// Dòng mới có mã bảng giá → tạo nhóm mới
					$groups[] = [
						'code' => $row['code'],
						'name' => $row['name'],
						'cost_of_brand' => $row['cost_of_brand'],
						'labor_cost' => $row['labor_cost'],
						'loss_cost' => $row['loss_cost'],
						'profit' => $row['profit'],
						'sheet_name' => $row['__sheet_name'],
						'row_index' => $row['__row_index'],
						'items' => []
					];
					$currentGroup = &$groups[count($groups) - 1];
				}
				if ($currentGroup !== null && !empty($row['code_stage'])) {
					$currentGroup['items'][] = $row;
				}
			}

			// Kiểm tra mỗi nhóm phải có ít nhất 1 item và mỗi item phải có dữ liệu hợp lệ
			foreach ($groups as $key => $group) {
				if (empty($group['items'])) {
					$errors[] = [
						'sheet' => $group['sheet_name'],
						'code' => $group['code'],
						'row' => $group['row_index'],
						'message' => 'Bảng giá "' . $group['code'] . '" không có dữ liệu công đoạn'
					];
					unset($groups[$key]);
					continue;
				}
				$invalidItems = [];
				foreach ($group['items'] as $item) {
					$missing = [];
					if (empty($item['height']) || floatval($item['height']) <= 0) {
						$missing[] = 'Chiều cao (Height)';
					}
					if (empty($item['width']) || floatval($item['width']) <= 0) {
						$missing[] = 'Chiều rộng (Width)';
					}

					if (!empty($missing)) {
						$invalidItems[] = [
							'sheet' => $item['__sheet_name'],
							'code' => $group['code'],
							'row' => $item['__row_index'],
							'message' => 'Công đoạn "' . $item['code_stage'] . '" thiếu/không hợp lệ: ' . implode(', ', $missing)
						];
					}
				}
				if (!empty($invalidItems)) {
					foreach ($invalidItems as $invErr) {
						$errors[] = $invErr;
					}
					unset($groups[$key]);
				}
			}

			// Batch query: lấy tất cả mã công đoạn và đơn vị tính cần thiết
			$allCodeStages = [];
			$allUnits = [];
			foreach ($groups as $group) {
				foreach ($group['items'] as $item) {
					$allCodeStages[] = $item['code_stage'];
					$allUnits[] = $item['unit_id'];
				}
			}
			$allCodeStages = array_unique($allCodeStages);
			$allUnits = array_unique($allUnits);

			$stageMap = [];
			if (!empty($allCodeStages)) {
				$this->db->where_in('code', $allCodeStages);
				$stages = $this->db->get('tbl_stages')->result_array();
				foreach ($stages as $stage) {
					$stageMap[$stage['code']] = $stage;
				}
			}

			$unitMap = [];
			if (!empty($allUnits)) {
				$this->db->where_in('unit', $allUnits);
				$units = $this->db->get('tblunits')->result_array();
				foreach ($units as $unit) {
					$unitMap[$unit['unit']] = $unit;
				}
			}

			// Fetch existing quotes to avoid N+1 queries
			$allCodes = [];
			foreach ($groups as $group) {
				$allCodes[] = $group['code'];
			}
			$allCodes = array_unique($allCodes);

			$existingQuotes = [];
			if (!empty($allCodes)) {
				$this->db->where_in('code', $allCodes);
				$quotes = $this->db->get('tbl_stage_quote')->result_array();
				foreach ($quotes as $q) {
					$existingQuotes[$q['code']] = $q;
				}
			}

			// Fetch existing details for all existing quotes to avoid N+1 queries
			$existingQuoteIds = array_column($existingQuotes, 'id');
			$existingDetails = [];
			if (!empty($existingQuoteIds)) {
				$this->db->where_in('id_stage_quote', $existingQuoteIds);
				$details = $this->db->get('tbl_stage_quote_detail')->result_array();
				foreach ($details as $d) {
					$key = $d['id_stage_quote'] . '_' . $d['id_stage'] . '_' . $d['unit_id'] . '_' . floatval($d['height']) . '_' . floatval($d['width']);
					$existingDetails[$key] = $d;
				}
			}

			$detailsToInsert = [];
			$detailsToUpdate = [];

			foreach ($groups as $key => $group) {
				// Kiểm tra từng công đoạn và đơn vị tính từ map
				$validGroup = true;
				$groupErrors = [];
				foreach ($group['items'] as $item) {
					if (empty($stageMap[$item['code_stage']])) {
						$groupErrors[] = [
							'sheet' => $item['__sheet_name'],
							'code' => $group['code'],
							'row' => $item['__row_index'],
							'message' => 'Mã công đoạn "' . $item['code_stage'] . '" không tồn tại'
						];
						$validGroup = false;
					}
					if (empty($unitMap[$item['unit_id']])) {
						$groupErrors[] = [
							'sheet' => $item['__sheet_name'],
							'code' => $group['code'],
							'row' => $item['__row_index'],
							'message' => 'Đơn vị tính "' . $item['unit_id'] . '" không tồn tại'
						];
						$validGroup = false;
					}
				}
				if (!$validGroup) {
					foreach ($groupErrors as $gErr) {
						$errors[] = $gErr;
					}
					unset($groups[$key]);
					continue;
				}

				if (isset($existingQuotes[$group['code']])) {
					$id_stage_quote = $existingQuotes[$group['code']]['id'];
					$this->db->where('id', $id_stage_quote);
					$this->db->update('tbl_stage_quote', [
						'name' => !empty($group['name']) ? $group['name'] : $group['code'],
						'cost_of_brand' => !empty($group['cost_of_brand']) ? number_format_data($group['cost_of_brand'], false) : 0,
						'labor_cost' => !empty($group['labor_cost']) ? number_format_data($group['labor_cost'], false) : 0,
						'loss_cost' => !empty($group['loss_cost']) ? number_format_data($group['loss_cost'], false) : 0,
						'profit' => !empty($group['profit']) ? number_format_data($group['profit'], false) : 0,
					]);

					foreach ($group['items'] as $item) {
						$id_stage = $stageMap[$item['code_stage']]['id'];
						$unit_id = $unitMap[$item['unit_id']]['unitid'];
						$height = number_format_data($item['height'], false);
						$width = number_format_data($item['width'], false);
						$price = number_format_data($item['price'], false);

						$key = $id_stage_quote . '_' . $id_stage . '_' . $unit_id . '_' . floatval($height) . '_' . floatval($width);
						if (isset($existingDetails[$key])) {
							$detailsToUpdate[] = [
								'id' => $existingDetails[$key]['id'],
								'price' => $price
							];
						} else {
							$detailsToInsert[] = [
								'id_stage_quote' => $id_stage_quote,
								'id_stage' => $id_stage,
								'unit_id' => $unit_id,
								'height' => $height,
								'width' => $width,
								'price' => $price,
							];
						}

						$arrayData[] = [
							'id_stage_quote' => $id_stage_quote,
							'id_stage' => $id_stage,
							'unit_id' => $unit_id,
							'height' => $height,
							'width' => $width,
							'price' => $price,
						];
					}
					$successCount++;
				} else {
					// Insert bảng giá (lấy thông tin từ dòng đầu tiên của nhóm)
					$insert_id = $this->db->insert('tbl_stage_quote', [
						'code' => $group['code'],
						'name' => !empty($group['name']) ? $group['name'] : $group['code'],
						'cost_of_brand' => !empty($group['cost_of_brand']) ? number_format_data($group['cost_of_brand'], false) : 0,
						'labor_cost' => !empty($group['labor_cost']) ? number_format_data($group['labor_cost'], false) : 0,
						'loss_cost' => !empty($group['loss_cost']) ? number_format_data($group['loss_cost'], false) : 0,
						'profit' => !empty($group['profit']) ? number_format_data($group['profit'], false) : 0,
						'create_by' => get_staff_user_id(),
						'date_create' => date('Y-m-d H:i:s')
					]);

					if ($insert_id) {
						$id_stage_quote = $this->db->insert_id();
						foreach ($group['items'] as $item) {
							$id_stage = $stageMap[$item['code_stage']]['id'];
							$unit_id = $unitMap[$item['unit_id']]['unitid'];
							$height = number_format_data($item['height'], false);
							$width = number_format_data($item['width'], false);
							$price = number_format_data($item['price'], false);

							$detailsToInsert[] = [
								'id_stage_quote' => $id_stage_quote,
								'id_stage' => $id_stage,
								'unit_id' => $unit_id,
								'height' => $height,
								'width' => $width,
								'price' => $price,
							];

							$arrayData[] = [
								'id_stage_quote' => $id_stage_quote,
								'id_stage' => $id_stage,
								'unit_id' => $unit_id,
								'height' => $height,
								'width' => $width,
								'price' => $price,
							];
						}
						$successCount++;
					}
				}
			}

			if (!empty($detailsToUpdate)) {
				$this->db->update_batch('tbl_stage_quote_detail', $detailsToUpdate, 'id');
			}
			if (!empty($detailsToInsert)) {
				$this->db->insert_batch('tbl_stage_quote_detail', $detailsToInsert);
			}
		}

		echo json_encode([
			'success' => true,
			'alert_type' => 'success',
			'is_success' => $successCount,
			'message' => 'Import thành công ' . $successCount . ' bảng giá, ' . count($arrayData) . ' dòng chi tiết',
			'data' => $arrayData,
			'errors' => $errors,
		]);
		die();
	}
}
