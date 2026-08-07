<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stage_price_list extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->HasView = has_permission('stage_price_list', '', 'view');
		$this->HasEdit = has_permission('stage_price_list', '', 'view');
		$this->HasCreate = has_permission('stage_price_list', '', 'view');
		$this->HasDelete = has_permission('stage_price_list', '', 'view');
	}

	public function index()
	{
		if (!$this->HasView) {
			access_denied('stage_price_list');
		}
		$data['title'] = _l('Danh sách bảng giá đơn vị gia công');
		$this->load->view('admin/stage_price_list/manage', $data);
	}

	public function table()
	{
		$aColumns = array(
			'tblstage_price_list.id as id',
			'name_price',
			'tblsuppliers.company as company',
		);
		$sIndexColumn = "id";
		$sTable = 'tblstage_price_list';
		$where = [];
		$join = array(
			'LEFT JOIN tblsuppliers ON tblsuppliers.id = tblstage_price_list.id_supplier'
		);
		if ($this->input->post('price_name')) {
			array_push($where, 'AND tblstage_price_list.id = ' . $this->input->post('price_name'));
		}
		if ($this->input->post('supplier_search')) {
			array_push($where, 'AND tblstage_price_list.id_supplier = ' . $this->input->post('supplier_search'));
		}
		if ($this->input->post('year_search')) {
			array_push($where, 'AND tblstage_price_list.year = "' . $this->ci->input->post('year_search') . '"');
		}
//		if ($this->input->post('items_search')){
//			$items_search = explode('__', $this->ci->input->post('items_search'));
//			$type_product = $items_search[1];
//			if ($items_search[1] == 'products' || $items_search[1] == 'semi_products'){
//				$type_product = 'product';
//			}
//			array_push($where,'AND EXISTS (
//				SELECT 1
//				FROM tblgroup_price_detail
//				WHERE tblgroup_price_detail.group_price_id = tblgroup_price.id
//				AND tblgroup_price_detail.product_id = ' . $items_search[0] . ' AND tblgroup_price_detail.product_type = "'.$type_product.'"
//			)');
//		}
		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblstage_price_list.id_supplier'));
		$output = $result['output'];
		$rResult = $result['rResult'];
		$j = 0;
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$j++;
			$row[] = '<div class="text-center"> ' . ($key + 1) . ' </div>';
			$row[] = '<a href="'.admin_url('stage_price_list/detail/' . $aRow['id']).'" class="c_modal" >' . $aRow['name_price'] . '</a>';
			$row[] = $aRow['company'];
			
			$options = '';
			$options .= '<a href="'.admin_url('stage_price_list/detail/' . $aRow['id']).'" class="btn btn-default btn-icon c_modal"><i class="fa fa-eye"></i></a>';
			if($this->HasDelete) {
				$options .= icon_btn('stage_price_list/delete/' . $aRow['id'], 'remove', 'btn-danger delete-remind');
			}
			$row[] = '<div class="text-center">' . $options . '</div>';
			$output['aaData'][] = $row;
		}
		
		echo json_encode($output);die();
	}
	
	public function import()
	{
		if (!$this->HasCreate) {
			access_denied();
		}
		
		if ($this->input->post()) {
			if (isset($_FILES['file_excel']['name']) && $_FILES['file_excel']['name'] != '') {
				$tmpFilePath = $_FILES['file_excel']['tmp_name'];
				$ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
				$type = $_FILES["file_excel"]["type"];
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					// Setup our new file path
					$newFilePath = TEMP_FOLDER . $_FILES['file_excel']['name'];
					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 777);
					}
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$load_result = true;
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
								$objPHPExcel = $objReader->load($newFilePath);
								$allSheetName = $objPHPExcel->getSheetNames();
								$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
								$highestRow = $objWorksheet->getHighestRow();
								$highestColumn = $objWorksheet->getHighestColumn();
								$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
								for ($row = 2; $row <= $highestRow; ++$row) {
									for ($col = 0; $col < $highestColumnIndex; ++$col) {
										$cell = $objWorksheet->getCellByColumnAndRow($col, $row);
										$value = $cell->getCalculatedValue();
										$rows[$row - 1][$col] = $value;
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
						
						$data = [];
						$reason = "";
						$dem_temp = 2;
						$alert['success'] = 0;
						$alert['success_update'] = 0;
						$name_price = $this->input->post('name_price');
						$id_supplier = $this->input->post('id_supplier');
						$kt_stage_price_list = get_table_where('tblstage_price_list', array('id_supplier' => $id_supplier), '', 'row');
						if (!empty($kt_stage_price_list)) {
							$arrayIn = array(
								'name_price' => $name_price
							);
							$this->db->where('id', $kt_stage_price_list->id);
							$success_update = $this->db->update('tblstage_price_list', $arrayIn);
							if (!empty($success_update)) {
								$price_list_id = $kt_stage_price_list->id;
								if (!empty($price_list_id)) {
									foreach ($rows as $row) {
										
										$code_product = $row[0];
										$type_product = $row[1];
										$moq_start = $row[2];
										$moq_end = $row[3];
										$price = $row[4];
										
										
										
										
										
										$data_ok = true;
										if (empty($type_product)) {
											$dem_temp++;
											continue;
										} else if (empty($code_product)) {
											$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										}
										if ($type_product == 'product') {
											$checkExisting_SP = get_table_where('tbl_products', array('code' => $code_product), '', 'row');
										}
										else if ($type_product == 'nvl') {
											$checkExisting_SP = get_table_where('tbl_materials', array('code' => $code_product), '', 'row');
										}
										
										if (empty($checkExisting_SP)) {
											$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										}
										
										
										
										
										
										
										$data_ok = true;
										if (empty($price)) {
											$reason .= "Không tìm thấy giá tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										}
										if (!is_numeric($price)) {
											$reason .= "Giá không hợp lệ  " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										}
										else {
											$this->db->where('product_id', $checkExisting_SP->id);
											$this->db->where('product_type', $type_product);
											$this->db->where('price_list_id', $price_list_id);
											$kt_stage_price_list_detail = $this->db->get('tblstage_price_list_detail')->row();
											if (!empty($kt_stage_price_list_detail)) {
												$data_tmp = array(
													'price' => !empty($price) ? number_format_data_four($price, false) : 0,
													'money_start' => !empty($moq_start) ? number_format_data_four($moq_start, false) : 0,
													'money_end' => !empty($moq_end) ? number_format_data_four($moq_end, false) : 0,
												);
												$this->db->where('id', $kt_stage_price_list_detail->id);
												$this->db->update('tblstage_price_list_detail', $data_tmp);
												$alert['success_update']++;
											} else {
												$data_tmp = array(
													'price' => !empty($price) ? number_format_data_four($price, false) : 0,
													'product_id' => $checkExisting_SP->id,
													'product_type' => $type_product,
													'price_list_id' => $price_list_id,
													'money_start' => !empty($moq_start) ? number_format_data_four($moq_start, false) : 0,
													'money_end' => !empty($moq_end) ? number_format_data_four($moq_end, false) : 0,
												);
												if ($data_ok) {
													$this->db->insert('tblstage_price_list_detail', $data_tmp);
													$alert['success']++;
												}
											}
											$dem_temp++;
										}
									}
								}
								$data['message'] = "Nhập thành công " . $alert['success'] . " và cập nhật " . $alert['success_update'] . " thành công nội dung. <br />";
								$data['message'] .= $reason;
							}
						}
						else {
							$in = array(
								'name_price' => $name_price,
								'id_supplier' => $id_supplier,
								'date_create' => date('Y-m-d H:i:s'),
								'staff_create' => get_staff_user_id(),
							);
							$this->db->insert('tblstage_price_list', $in);
							$id_stage_price_list = $this->db->insert_id();
							foreach ($rows as $row) {
								$code_product = $row[0];
								$type_product = $row[1];
								$moq_start = $row[2];
								$moq_end = $row[3];
								$price = $row[4];
								
								$data_ok = true;
								if (empty($type_product)) {
									$dem_temp++;
									continue;
								} else if (empty($code_product)) {
									$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								if ($type_product == 'product') {
									$checkExisting_SP = get_table_where('tbl_products', array('code' => $code_product), '', 'row');
								}
								else if ($type_product == 'nvl') {
									$checkExisting_SP = get_table_where('tbl_materials', array('code' => $code_product), '', 'row');
								}
								
								if (empty($checkExisting_SP)) {
									$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								
								
								
								
								
								$data_ok = true;
								if (empty($price)) {
									$reason .= "Không tìm thấy giá tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								if (!is_numeric($price)) {
									$reason .= "Giá không hợp lệ  " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								
								if (!empty($id_stage_price_list)) {
									$data_tmp = array(
										'price' => !empty($price) ? number_format_data_four($price, false) : 0,
										'product_id' => $checkExisting_SP->id,
										'product_type' => $type_product,
										'price_list_id' => $id_stage_price_list,
										'money_start' => !empty($moq_start) ? number_format_data_four($moq_start, false) : 0,
										'money_end' => !empty($moq_end) ? number_format_data_four($moq_end, false) : 0,
									);
									if ($data_ok) {
										$this->db->insert('tblstage_price_list_detail', $data_tmp);
										$alert['success']++;
									}
								}
								$dem_temp++;
							}
							$data['message'] = "Nhập thành công " . $alert['success'] . " nội dung. <br />";
							$data['message'] .= $reason;
						}
					}
				}
			}
		}
		
		
		$data['title'] = 'Bảng Giá Đơn Vị Gia Công';
		$this->load->view('admin/stage_price_list/import', $data);
	}
	
	
	public function SearchSupplier($id = '')
	{
		$data = [];
		$search = $this->input->get('term');
		$type = $this->input->get('type');
		$limit_one = 15;
		$limit_two = 15;
		$limit_all = 50;
		$this->db->select(
			'
            tblsuppliers.id as id,
            tblsuppliers.company as text',
			false
		);
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('tblsuppliers.company', $search);
			$this->db->group_end();
		} else {
			if ($id > 0) {
				$this->db->group_start();
				$this->db->where('tblsuppliers.id', $id);
				$this->db->group_end();
			}
		}
		$this->db->order_by('tblsuppliers.company', 'DESC');
		$this->db->limit($limit_one);
		$items = $this->db->get('tblsuppliers')->result_array();
		if (!empty($items)) {
			$data['results'] = $items;
		}
		echo json_encode($data);
		die();
	}
	
	
	
	public function detail($id = '', $table = false)
	{
//		$this->db->select('tblstage_price_list_detail.*,
//			tblitemsData.avatar,
//			tblitemsData.code_item as code_item,
//			tblitemsData.name_item as name_item,
//			tblitemsData.unit_name as unit_name,
//			tblitemsData.product_id as id_item_data,
//		');
//		$this->db->where('price_list_id', $id);
//		$this->db->join(tableItemsShortMysql(), 'tblitemsData.product_id = tblstage_price_list_detail.product_id
//			AND tblitemsData.type = tblstage_price_list_detail.product_type', 'left');
//		$this->db->order_by('tblstage_price_list_detail.id', 'desc');
//		$data['data'] = $this->db->get('tblstage_price_list_detail')->result();
	
		
		
		$this->db->select('tblstage_price_list.*, tblsuppliers.company');
		$this->db->join('tblsuppliers', 'tblsuppliers.id = tblstage_price_list.id_supplier');
		$data['stage_price_list'] = $this->db->get_where('tblstage_price_list', ['tblstage_price_list.id' => $id])->row();
		
		$this->db->order_by('id', 'desc');
		$data['history_data'] = $this->db->get_where('tblstage_price_list_history', ['price_list_id' => $id])->result();
		$data['title'] = 'Bảng giá đơn vị gia công của nhà cung cấp “' . $data['stage_price_list']->company.'”';
		if (!empty($table)) {
			return $this->load->view('admin/stage_price_list/table', $data, true);
		} else {
			$this->load->view('admin/stage_price_list/view_modal', $data);
		}
	}
	
	
	public function table_detail($id) {
		$aColumns = [
			'tblstage_price_list_detail.id as id',
			'tblitemsData.avatar as avatar',
			'tblitemsData.code_item as code_item',
			'tblitemsData.name_item as name_item',
			'tblitemsData.unit_name_payment as unit_name_payment',
			'tblcurrencies.name as name_curren',
			'tblstage_price_list_detail.money_start as money_start',
			'tblstage_price_list_detail.money_end as money_end',
			'tblstage_price_list_detail.price as price',
		];
		
		$sWhere = [];
		array_push($sWhere, 'AND tblstage_price_list_detail.price_list_id = "'.$id.'"');
		$Join = [
			'JOIN '.tableItemsShortMysql().' ON tblitemsData.product_id = tblstage_price_list_detail.product_id AND tblitemsData.type = tblstage_price_list_detail.product_type',
			'JOIN tblstage_price_list ON tblstage_price_list.id = tblstage_price_list_detail.price_list_id',
			'LEFT JOIN tblsuppliers ON tblsuppliers.id = tblstage_price_list.id_supplier',
			'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblsuppliers.default_currency',
		];
		$sIndexColumn = 'id';
		$sTable       = 'tblstage_price_list_detail';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $Join, $sWhere, [
			'tblstage_price_list_detail.product_type','tblstage_price_list_detail.product_id'
		], 'order by tblstage_price_list_detail.id desc');
		$output       = $result['output'];
		$rResult      = $result['rResult'];
		foreach ($rResult as $key => $aRow) {
			$row = [];
			$row[] = '<div class="text-center">' .($key + 1) .'<br/></br><a class="btn btn-icon btn-danger" onclick="removeTrDetail('.$aRow['id'].')"><i class="fa fa-remove"></i></a></div>';
			$row[] = '<div class="preview_image" style="width: auto;">
							<div class="display-block contract-attachment-wrapper img">
								<div style="width:30px; margin: auto;">
									<a href="'.$aRow['avatar'].'" data-lightbox="customer-profile" class="display-block mbot5">
										<div class="">
											<img src="'.$aRow['avatar'].'" style="border-radius: 50%">
										</div>
									</a>
								</div>
							</div>
						</div>';
			
			$row[] = $aRow['code_item'];
			$row[] = $aRow['name_item'];
			$row[] = $aRow['unit_name_payment'];
			
			$moneyStart = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($aRow['money_start']), $aRow['id'], '', '<a class="pointer" id="money_start_text_v2_' . $aRow['id'] . '" target="_blank" >' . number_format_data_four($aRow['money_start']) . '</a>', '', admin_url('stage_price_list/update_money/start/' . $aRow['id'] . '/' . $id), 'class="formUpdateDataTable"');
			$row[] = '<div class="text-right"  data-id="money_start_text_v2_">
						<div class="type_v1">
							'.$moneyStart.'
							<div class="type_v2 hide" data-id="'.$aRow['id'].'" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_start" class="height_auto  money_start H_input align_right" value="'.number_format_data_four($aRow['price']).'"></div>
						 </div>
					 </div>';
			
			$moneyEnd = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($aRow['money_end']), $aRow['id'], '', '<a class="pointer" id="money_end_text_v2_' . $aRow['id'] . '" target="_blank" >' . number_format_data_four($aRow['money_end']) . '</a>', '', admin_url('stage_price_list/update_money/end/' . $aRow['id'] . '/' . $id), 'class="formUpdateDataTable"');
			$row[] = '<div class="text-right"  data-id="money_start_text_v2_">
						<div class="type_v1">
							'.$moneyEnd.'
							<div class="type_v2 hide" data-id="'.$aRow['id'].'" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_end" class="height_auto  money_end H_input align_right" value="'.number_format_data_four($aRow['price']).'"></div>
						 </div>
					 </div>';
			
			$moneyPrice = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($aRow['price']), $aRow['id'], '', '<a class="pointer" id="quantitys_text_v2_' . $aRow['id'] . '" target="_blank" >' . number_format_data_four($aRow['price']) . '</a>', '', admin_url('stage_price_list/quantity/' . $aRow['id'] . '/' . $id), 'class="formUpdateDataTable"');
			$row[] = '<div class="text-right"  data-id="money_start_text_v2_">
						<div class="type_v1">
							'.$moneyPrice.'
							<div class="type_v2 hide" class="quantitys_input" data-id="'.$aRow['id'].'" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="quantitys" id="quantitys" class="height_auto quantitys H_input align_right" value="'.number_format_data_four($aRow['price']).'"></div>
						 </div>
					 </div>';
			
			$row[] = $aRow['name_curren'];
			
			$row[] = '<div class="text-center">' . format_item_purchases($aRow['product_type']) . '</div>';
			$output['aaData'][] = $row;
		}
		
		echo json_encode($output);die();
		
	}
	
	public function add_items()
	{
		$data = $this->input->post();
		$items_products = explode('__', $data['items_products']);
		$product_id = $items_products[0];
		$product_type = $items_products[1];
		$success = $this->db->insert('tblstage_price_list_detail', [
			'price' => number_format_data_four($data['price'], false),
			'product_id' => $product_id,
			'product_type' => 'product',
			'price_list_id' => $data['price_list_id'],
			'money_start' => number_format_data_four($data['money_start'], false),
			'money_end' => number_format_data_four($data['money_end'], false),
		]);
		$dataSuccess = [
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Thêm không thành công'
		];
		if (!empty($success)) {
			$dataSuccess = [
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Thêm thành công',
			];
		}
		echo json_encode($dataSuccess);
		die();
	}
	
	public function remove_items() {
		$data = $this->input->post();
		$id = $data['id'];
		$dataSuccess = [
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa không thành công'
		];
		$this->db->where('id', $id);
		$stage_price_list_detail = $this->db->get('tblstage_price_list_detail')->row();
		if (!empty($stage_price_list_detail)) {
			$this->db->where('id', $id);
			$success = $this->db->delete('tblstage_price_list_detail');
			if (!empty($success)) {
				$dataSuccess = [
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa thành công',
				];
			}
		}
		echo json_encode($dataSuccess);
		die();
	}
	
	public function update_money($type = 'start', $id_detai = '', $id = '')
	{
		if (!$this->HasEdit) {
			echo json_encode([
				'success' => 'warning',
				'messeger' => _l('Bạn không có quyền sửa')
			]);
			die;
		}
		$data = $this->input->post();
		
		$ktGroup = get_table_where('tblstage_price_list_detail', array('id' => $id_detai), '', 'row');
		if (!empty($ktGroup)) {
			if (empty($total)) {
				$total = 0;
			}
			$total = str_replace(',', '', $data['data_input']);
			$field = 'money_end';
			if ($type == 'start') {
				$field = 'money_start';
			}
			$success = $this->db->update('tblstage_price_list_detail', array(
				$field => $total,
				'date_update' => date('Y-m-d H:i:s')
			), array('id' => $id_detai));
			if (!empty($success)) {
				$group_price_detail_last = $this->db->get_where('tblstage_price_list_detail', ['id' => $id_detai])->row();
				$arrayHistory = [
					'price_list_id' => $ktGroup->price_list_id,
					'product_id' => $ktGroup->product_id,
					'product_type' => $ktGroup->product_type,
					'price' => $ktGroup->price,
					'price_last' => $group_price_detail_last->price,
					'money_start' => $ktGroup->money_start,
					'money_start_last' => $group_price_detail_last->money_start,
					'money_end' => $ktGroup->money_end,
					'money_end_last' => $group_price_detail_last->money_end,
					'date_before' => $ktGroup->date_update,
					'type_event' => $field,
					'create_by' => get_staff_user_id(),
				];
				$this->db->insert('tblstage_price_list_history', $arrayHistory);
			}
		}
		$totals['id'] = $data['id'];
		$totals['total'] = number_format_data_four($total);
		$totals['success'] = 'success';
		$totals['messeger'] = 'Cập nhật giá thành công';
		echo json_encode($totals);
	}
	
	public function quantity($id_detai = '', $id = '')
	{
		if (!$this->HasEdit) {
			echo json_encode(array(
				'success' => 'warning',
				'messeger' => _l('Bạn không có quyền sửa')
			));
			die;
		}
		
		$data = $this->input->post();
		$ktGroup = get_table_where('tblstage_price_list_detail', array('id' => $id_detai), '', 'row');
		if (!empty($ktGroup)) {
			if (empty($total)) {
				$total = 0;
			}
			$total = str_replace(',', '', $data['data_input']);
			
			$success = $this->db->update('tblstage_price_list_detail', array(
				'price' => $total,
				'date_update' => date('Y-m-d H:i:s')
			), array('id' => $id_detai));
			if (!empty($success)) {
				$group_price_detail_last = $this->db->get_where('tblstage_price_list_detail', ['id' => $id_detai])->row();
				$arrayHistory = [
					'price_list_id' => $ktGroup->price_list_id,
					'product_id' => $ktGroup->product_id,
					'product_type' => $ktGroup->product_type,
					'price' => $ktGroup->price,
					'price_last' => $group_price_detail_last->price,
					'money_start' => $ktGroup->money_start,
					'money_start_last' => $group_price_detail_last->money_start,
					'money_end' => $ktGroup->money_end,
					'money_end_last' => $group_price_detail_last->money_end,
					'date_before' => $ktGroup->date_update,
					'type_event' => 'price',
					'create_by' => get_staff_user_id(),
				];
				$this->db->insert('tblstage_price_list_history', $arrayHistory);
			}
		}
		$totals['id'] = $data['id'];
		$totals['total'] = number_format_data_four($total);
		$totals['success'] = 'success';
		$totals['messeger'] = 'Cập nhật giá thành công';
		echo json_encode($totals);
	}
	
	
	public function get_history($id = '')
	{
		$this->db->order_by('id', 'desc');
		$this->db->group_start();
		$this->db->where('type_event', 'price');
		$this->db->or_where('type_event is null', false, false);
		$this->db->group_end();
		$data['history_data'] = $this->db->get_where('tblstage_price_list_history', ['price_list_id' => $id])->result();
		$this->load->view('admin/stage_price_list/table_history', $data);
	}
	
	public function export_excel()
	{
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		$style_excel = style_excel();
		$style_excel['border'] = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
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
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
		
		$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'NHÀ CUNG CẤP')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'TÊN BẢNG GIÁ')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'MÃ SẢN PHẨM')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'TÊN SẢN PHẨM')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'MOQ TỪ')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'MOQ ĐẾN')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'GIÁ')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", 'ĐƠN VỊ TIỀN TỆ')->getStyle("H$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", 'LOẠI SẢN PHẨM')->getStyle("I$numberRow")->applyFromArray($style_excel['Background_header']);
		$numberRow++;
		
		
		$this->db->select('tblstage_price_list.id');
		$db_results = $this->db->get('tblstage_price_list')->result_array();
		if (!empty($db_results)) {
			foreach ($db_results as $key => $value) {
				$id = $value['id'];
				
				$this->db->select('tblstage_price_list.*, tblsuppliers.company, tblcurrencies.name as name_curren');
				$this->db->join('tblsuppliers', 'tblsuppliers.id = tblstage_price_list.id_supplier');
				$this->db->join('tblcurrencies', 'tblcurrencies ON tblcurrencies.id = tblsuppliers.default_currency', 'left');
				$this->db->where('tblstage_price_list.id', $id);
				$data['import_price_group'] = $this->db->get_where('tblstage_price_list')->row();
				
				$this->db->select('tblstage_price_list_detail.*,
					tblitemsData.avatar,
					tblitemsData.code_item as code,
					tblitemsData.name_item as name,
					tblitemsData.unit_name as unit_name,
					tblitemsData.product_id as id_item_data,
				');
				$this->db->where('price_list_id', $id);
				$this->db->join(tableItemsShortMysql(), 'tblitemsData.product_id = tblstage_price_list_detail.product_id
					AND tblitemsData.type = tblstage_price_list_detail.product_type', 'left');
				$this->db->order_by('tblstage_price_list_detail.price_list_id', 'asc');
				$this->db->order_by('tblstage_price_list_detail.id', 'desc');
				$data['items'] = $this->db->get('tblstage_price_list_detail')->result();
				
				
				$company = $data['import_price_group']->company;
				$name_price = $data['import_price_group']->name_price;
				foreach ($data['items'] as $item) {
					$_item = @get_items($item->product_id, $item->product_type);
					$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", $company)->getStyle("A$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", $name_price)->getStyle("B$numberRow")->applyFromArray($style_excel['BStyle_left'])->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $_item->code)->getStyle("C$numberRow")->applyFromArray($style_excel['BStyle_left'])->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", $_item->name)->getStyle("D$numberRow")->applyFromArray($style_excel['BStyle_left'])->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", $item->money_start)->getStyle("E$numberRow")->applyFromArray($style_excel['border'])->getNumberFormat()->setFormatCode('#,##0.0###');
					$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", $item->money_end)->getStyle("F$numberRow")->applyFromArray($style_excel['border'])->getNumberFormat()->setFormatCode('#,##0.0###');
					$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $item->price)->getStyle("G$numberRow")->applyFromArray($style_excel['border'])->getNumberFormat()->setFormatCode('#,##0.0###');
					$objPHPExcel->getActiveSheet()->SetCellValue("H$numberRow", (!empty($item->name_curren) ? $item->name_curren : ''))->getStyle("H$numberRow")->applyFromArray($style_excel['border']);
					$objPHPExcel->getActiveSheet()->SetCellValue("I$numberRow", (!empty($item->product_type) ? strip_tags(format_item_purchases($item->product_type)) : ''))->getStyle("I$numberRow")->applyFromArray($style_excel['border']);
					$numberRow++;
				}
			}
		}
		$filename = lang('Bang_gia_theo_don_vi_gia_cong') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	public function delete($id = '') {
		$this->db->where('id', $id);
		$stage_price_list = $this->db->get('tblstage_price_list')->row();
		if(!empty($stage_price_list)) {
			$this->db->where('id', $id);
			$success = $this->db->delete('tblstage_price_list');
			if(!empty($success)) {
				$this->db->where('price_list_id', $id);
				$this->db->delete('tblstage_price_list_detail');
				
				$this->db->where('price_list_id', $id);
				$this->db->delete('tblstage_price_list_history');
				
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa bảng giá thành công'
				]);die();
			}
		}
		
		echo json_encode([
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa bảng giá không thành công'
		]);die();
	}
	
	public function get_price() {
		$id_supplier = $this->input->post('id_supplier');
		$item_id = $this->input->post('item_id');
		$type_item = $this->input->post('type_item');
		$quantity = number_format_data($this->input->post('quantity'), false);
		$dataResult = [
			'price' => 0,
			'id' => 0
		];
		
		if($type_item == 'products' || $type_item == 'semi_products') {
			$type_item = 'product';
		}
		else if($type_item == 'materials' || $type_item == 'material') {
			$type_item = 'nvl';
		}
		
		if(!empty($id_supplier) && !empty($item_id) && !empty($type_item)) {
			$this->db->select('tblstage_price_list_detail.*');
			$this->db->where('id_supplier', $id_supplier);
			$this->db->where('tblstage_price_list_detail.product_id', $item_id);
			$this->db->where('tblstage_price_list_detail.product_type', $type_item);
			$this->db->group_start();
				$this->db->where('tblstage_price_list_detail.money_start <= ' . $quantity, false, false);
				$this->db->or_where('tblstage_price_list_detail.money_start = 0', false, false);
			$this->db->group_end();
			$this->db->group_start();
				$this->db->where('tblstage_price_list_detail.money_end >= ' . $quantity, false, false);
				$this->db->or_where('tblstage_price_list_detail.money_end = 0', false, false);
			$this->db->group_end();
			$this->db->join('tblstage_price_list', 'tblstage_price_list.id = tblstage_price_list_detail.price_list_id');
			$stage_price_list_detail = $this->db->get('tblstage_price_list_detail')->row();
			if(!empty($stage_price_list_detail)) {
				$dataResult = [
					'price' => $stage_price_list_detail->price,
					'id' => $stage_price_list_detail->id
				];
			}
		}
		echo json_encode($dataResult);die();
	}
	
}
