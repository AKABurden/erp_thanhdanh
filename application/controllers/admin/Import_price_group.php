<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Import_price_group extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('import_price_group_model');
	}

	public function index()
	{
		if (!has_permission('import_price_group', '', 'view')) {
			access_denied('import_price_group');
		}
		$data['title'] = _l('c_improt_price_group');
		$data['data_price'] = get_table_where('tblgroup_price');
		$data['customers_groups'] = get_table_where('tblcustomers_groups', ['id_parent' => 0]);
		$this->load->view('admin/import_price_group/manage', $data);
	}

	public function table()
	{
		$this->app->get_table_data('import_price_group');
	}

	public function show_detail_price($id = '', $table = false)
	{
//		$data['data'] = $this->import_price_group_model->show_list_detail($id);
		
		$this->db->select('tblgroup_price_detail.*,
			tblitemsData.avatar,
			tblitemsData.code_item as code_item,
			tblitemsData.name_item as name_item,
			tblitemsData.unit_name as unit_name,
			tblitemsData.product_id as id_item_data,
		');
		$this->db->where('group_price_id', $id);
		$this->db->join(tableItemsShortMysql(), 'tblitemsData.product_id = tblgroup_price_detail.product_id
			AND tblitemsData.type = tblgroup_price_detail.product_type', 'left');
		$this->db->order_by('tblgroup_price_detail.id', 'desc');
		$data['data'] = $this->db->get('tblgroup_price_detail')->result();
		
		
		$data['import_price_group'] = $this->import_price_group_model->show_List($id);
		$this->db->order_by('id', 'desc');
		$data['history_data'] = $this->db->get_where('tblgroup_price_history', ['group_price_id' => $id])->result();
		if (!empty($table)) {
			return $this->load->view('admin/import_price_group/table', $data, true);
		} else {
			$this->load->view('admin/import_price_group/view_modal', $data);
		}
	}

	public function get_history($id = '')
	{
		$this->db->order_by('id', 'desc');
		$this->db->group_start();
		$this->db->where('type_event', 'price');
		$this->db->or_where('type_event is null', false, false);
		$this->db->group_end();
		$data['history_data'] = $this->db->get_where('tblgroup_price_history', ['group_price_id' => $id])->result();
		$this->load->view('admin/import_price_group/table_history', $data);
	}

	public function show_detail_price_discount($id = '', $client = '')
	{
		$this->db->select('tblgroup_price_discount.*');
		$this->db->where('tblgroup_price_discount.group_price_id', $id);
		$this->db->where('tblgroup_price_discount.client', $client);
		$discount = $this->db->get('tblgroup_price_discount')->row();
		$client = $this->db->get_where('tblclients', ['userid' => $client])->row();
		$name_group = $client->company;
		$discount = !empty($discount->discount) ? $discount->discount : '';
		$data['child'] = true;
		$data['data'] = $this->import_price_group_model->show_list_detail($id);
		$data['import_price_group'] = $this->import_price_group_model->show_List($id);
		$data['import_price_group']->name_group = $name_group;
		$data['import_price_group']->discount = $discount;
		$data['data_discount'] = true;
		$this->load->view('admin/import_price_group/view_modal', $data);
	}

	public function delete_import($id)
	{
		if (!has_permission('import_price_group', '', 'delete')) {
			echo json_encode(array(
				'success' => false,
				'alert_type' => 'warning',
				'message' => _l('ch_no_delete')
			));
			die;
		}
		$checkExistingId = get_table_where('tblgroup_price', array('id' => $id), '', 'row');
		if (!empty($checkExistingId)) {
			$this->db->where('customer_id', $checkExistingId->client);
			$kt_orders = $this->db->get('tbl_orders')->row();
			if (!empty($kt_orders)) {
				echo json_encode([
					'success' => false,
					'alert_type' => 'danger',
					'message' => _l('Khách hàng đã có đơn hàng bán không thể xóa bảng giá')
				]);
				die();
			}
			$delete_import = $this->import_price_group_model->delete_import($id);
			if ($delete_import) {
				$delete_detail = get_table_where('tblgroup_price_detail', array('group_price_id' => $id), '', 'result');
				foreach ($delete_detail as $key => $value) {
					$this->import_price_group_model->delete_import_detail($value->id);
				}
			}
			echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('dt_delete_import_success')]);
			die();
		} else {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('dt_delete_import_error')]);
			die();
		}
	}

	public function delete_import_discount($id)
	{
		if (!has_permission('import_price_group', '', 'delete')) {
			echo json_encode(array(
				'success' => false,
				'alert_type' => 'warning',
				'message' => _l('ch_no_delete')
			));
			die;
		}
		$checkExistingId = get_table_where('tblgroup_price_discount ', array('id' => $id), '', 'row');
		if (!empty($checkExistingId)) {
			$this->db->where('id', $id);
			$delete_import = $this->db->delete('tblgroup_price_discount');
			if ($delete_import) {
				echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => _l('dt_delete_import_success')]);
			}
		} else {
			echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('dt_delete_import_error')]);
		}
	}

	public function import()
	{
		if (!has_permission('import_price_group', '', 'create')) {
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
						$query_array = [];
						$backup_rows = $rows;
						$result_array = [];
						$fetch_columns_step = true;
						$fetch_product_step = false;
						$columns_found = 0;
						$product_count = 0;
						$c = 0;
						$data = [];
						$data_ok = true;
						$reason = "";
						$dem_temp = 2;
						$alert['success'] = 0;
						$alert['success_update'] = 0;
						$alert['fail'] = 0;
						//

						// print_arrays($rows);
						$name_price = $this->input->post('name_price');
						//						$year = $this->input->post('year');
						$client = $this->input->post('client');
						$ktr_price = get_table_where('tblgroup_price', array('client' => $client), '', 'row');
						if (!empty($ktr_price)) {
							//							$data['message'] = "Bảng giá năm $year đã được tạo rồi, Vui lòng kiểm tra lại!";
							$in = array(
								//								'year' => $year,
								'name_price' => $name_price
							);
							$this->db->where('id', $ktr_price->id);
							$success_update = $this->db->update('tblgroup_price', $in);
							if (!empty($success_update)) {
								$id_group_price = $ktr_price->id;
								if (!empty($id_group_price)) {
									foreach ($rows as $row) {
										if (empty($row[1])) {
											//											$reason .= "Không Tìm Thấy loại sản phẩm tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										} else if (empty($row[0])) {
											$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										}
										if ($row[1] == 'product') {
											$checkExisting_SP = get_table_where('tbl_products', array('code' => $row[0]), '', 'row');
										} else if ($row[1] == 'nvl') {
											$checkExisting_SP = get_table_where('tbl_materials', array('code' => $row[0]), '', 'row');
										}
										$data_ok = true;
										if (empty($row[2])) {
											$reason .= "Không tìm thấy giá tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										}
										if (!is_numeric($row[2])) {
											$reason .= "Giá không hợp lệ  " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										} else if (empty($checkExisting_SP)) {
											$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
											$data_ok = false;
											$dem_temp++;
											continue;
										} else {
											$this->db->where('product_id', $checkExisting_SP->id);
											$this->db->where('product_type', $row[1]);
											$this->db->where('group_price_id', $id_group_price);
											$this->db->where('money_start', !empty($row[3]) ? number_format_data_four($row[3], false) : 0);
											$this->db->where('money_end', !empty($row[4]) ? number_format_data_four($row[4], false) : 0);
											$kt_group_price_detail = $this->db->get('tblgroup_price_detail')->row();
											if (!empty($kt_group_price_detail)) {
												$data_tmp = array(
													'price' => !empty($row[2]) ? number_format_data_four($row[2], false) : 0,
													'money_start' => !empty($row[3]) ? number_format_data_four($row[3], false) : 0,
													'money_end' => !empty($row[4]) ? number_format_data_four($row[4], false) : 0,
													'is_lot' => !empty($row[5]) ? 1 : 0,
												);
												$this->db->where('id', $kt_group_price_detail->id);
												$this->db->update('tblgroup_price_detail', $data_tmp);
												$alert['success_update']++;
											} else {
												$data_tmp = array(
													'price' => !empty($row[2]) ? number_format_data_four($row[2], false) : 0,
													'product_id' => $checkExisting_SP->id,
													'product_type' => $row[1],
													'group_price_id' => $id_group_price,
													'money_start' => !empty($row[3]) ? number_format_data_four($row[3], false) : 0,
													'money_end' => !empty($row[4]) ? number_format_data_four($row[4], false) : 0,
													'is_lot' => !empty($row[5]) ? 1 : 0,
												);
												if ($data_ok) {
													$this->db->insert('tblgroup_price_detail', $data_tmp);
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
						} else {
							$in = array(
								//								'year' => $year,
								'name_price' => $name_price,
								'client' => $client,
								'date_create' => date('Y-m-d H:i:s'),
								'staff_create' => get_staff_user_id(),
							);
							$this->db->insert('tblgroup_price', $in);
							$id_group_price = $this->db->insert_id();
							foreach ($rows as $row) {
								if (empty($row[1])) {
									$reason .= "Không Tìm Thấy loại sản phẩm tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								} else if (empty($row[0])) {
									$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								if ($row[1] == 'product') {
									$checkExisting_SP = get_table_where('tbl_products', array('code' => $row[0]), '', 'row');
								} else if ($row[1] == 'nvl') {
									$checkExisting_SP = get_table_where('tbl_materials', array('code' => $row[0]), '', 'row');
								}
								$data_ok = true;
								if (empty($row[2])) {
									$reason .= "Không tìm thấy giá tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								}
								if (!is_numeric($row[2])) {
									$reason .= "Giá không hợp lệ  " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								} else if (empty($checkExisting_SP)) {
									$reason .= "Không Tìm Thấy sản phẩm tại dòng " . $dem_temp . "<br />";
									$data_ok = false;
									$dem_temp++;
									continue;
								} else {
									if (!empty($id_group_price)) {
										$data_tmp = array(
											'price' => !empty($row[2]) ? number_format_data_four($row[2], false) : 0,
											'product_id' => $checkExisting_SP->id,
											'product_type' => $row[1],
											'group_price_id' => $id_group_price,
											'money_start' => !empty($row[3]) ? number_format_data_four($row[3], false) : 0,
											'money_end' => !empty($row[4]) ? number_format_data_four($row[4], false) : 0,
											'is_lot' => !empty($row[5]) ? 1 : 0,
										);
										if ($data_ok) {
											$this->db->insert('tblgroup_price_detail', $data_tmp);
											$alert['success']++;
										}
									}
									$dem_temp++;
								}
							}
							$data['message'] = "Nhập thành công " . $alert['success'] . " nội dung. <br />";
							$data['message'] .= $reason;
						}
					}
				}
			}
		}
		$data['title'] = 'Bảng giá khách hàng';
		$this->load->view('admin/import_price_group/import', $data);
	}

	public function quantity($id_detai = '', $id = '')
	{
		if (!has_permission('import_price_group', '', 'edit')) {
			echo json_encode(array(
				'success' => 'warning',
				'messeger' => _l('Bạn không có quyền sửa')
			));
			die;
		}
		$data = $this->input->post();
		$ktGroup = get_table_where('tblgroup_price_detail', array('id' => $id_detai), '', 'row');
		if (!empty($ktGroup)) {
			if (empty($total)) {
				$total = 0;
			}
			$total = str_replace(',', '', $data['data_input']);
			$success = $this->db->update('tblgroup_price_detail', array(
				'price' => $total,
				'date_update' => date('Y-m-d H:i:s')
			), array('id' => $id_detai));
			if (!empty($success)) {
				$group_price_detail_last = $this->db->get_where('tblgroup_price_detail', ['id' => $id_detai])->row();
				$arrayHistory = [
					'group_price_id' => $ktGroup->group_price_id,
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
				$this->db->insert('tblgroup_price_history', $arrayHistory);
			}
		}
		$totals['id'] = $data['id'];
		$totals['total'] = number_format_data_four($total);
		$totals['success'] = 'success';
		$totals['messeger'] = 'Cập nhật giá thành công';
		echo json_encode($totals);
	}

	public function update_money($type = 'start', $id_detai = '', $id = '')
	{
		if (!has_permission('import_price_group', '', 'edit')) {
			echo json_encode(array(
				'success' => 'warning',
				'messeger' => _l('Bạn không có quyền sửa')
			));
			die;
		}
		$data = $this->input->post();
		$ktGroup = get_table_where('tblgroup_price_detail', array('id' => $id_detai), '', 'row');
		if (!empty($ktGroup)) {
			if (empty($total)) {
				$total = 0;
			}
			$total = str_replace(',', '', $data['data_input']);
			$field = 'money_end';
			if ($type == 'start') {
				$field = 'money_start';
			}
			$success = $this->db->update('tblgroup_price_detail', array(
				$field => $total,
				'date_update' => date('Y-m-d H:i:s')
			), array('id' => $id_detai));
			if (!empty($success)) {
				$group_price_detail_last = $this->db->get_where('tblgroup_price_detail', ['id' => $id_detai])->row();
				$arrayHistory = [
					'group_price_id' => $ktGroup->group_price_id,
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
				$this->db->insert('tblgroup_price_history', $arrayHistory);
			}
		}
		$totals['id'] = $data['id'];
		$totals['total'] = number_format_data_four($total);
		$totals['success'] = 'success';
		$totals['messeger'] = 'Cập nhật giá thành công';
		echo json_encode($totals);
	}

	public function print_pdf($id = '')
	{
		ob_start();
		$data = new stdClass();
		//  $data->title = lang('Bảng giá nhà cung cấp');
		$dataSub = $this->import_price_group_model->show_list_detail($id);
		$main = $this->import_price_group_model->show_List($id);
		$table = '';
		$data->content = '';
		// $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
		$data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">BẢNG GIÁ THEO KHÁCH HÀNG</span><br><br>';
		// $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
		// $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
		$data->content .= '<span style="font-weight: bold;">' . _l('Khách hàng') . ': </span><span>' . $main->company . '</span><br>';
		$data->content .= '<span style="font-weight: bold;">' . _l('Tên bảng giá') . ': </span><span>' . $main->name_price . '</span><br><br>';
		//		$data->content .= '<span style="font-weight: bold;">' . _l('year') . ': </span><span>' . $main->year . '</span><br><br>';
		$width1 = 'width: 6%;';
		$width2 = 'width: 9%;';
		$width3 = 'width: 42%;';
		$width3_1 = 'width: 12%;';
		$width4 = 'width: 19%;';
		$table = '

            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td rowspan="2" style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
		$table .= '<td rowspan="2" style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('Hình ảnh') . '</td>';
		$table .= '<td rowspan="2" style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
		$table .= '<td colspan="2" style="width:24%;text-align: center;font-weight: bold;">' . _l('MOQ') . '</td>';
		$table .= '<td rowspan="2" style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('Giá') . '</td>';
		$table .= '</tr>
					<tr>
						<td style="' . $width3_1 . 'text-align: center;font-weight: bold;">SL từ</td>
						<td style="' . $width3_1 . 'text-align: center;font-weight: bold;">SL đến</td>
					</tr>
                </thead>
                <tbody>';
		foreach ($dataSub as $key => $value) {
			$dataItem = get_full_item($value->product_id, $value->product_type);
			if (empty($dataItem)) continue;
			$table .= '<tr nobr="true">';
			$table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
			$table .= '<td style="' . $width2 . 'text-align: center;"><img src="' . $dataItem->avatar_1 . '" width="30px" height="30px"></td>';
			$table .= '<td style="' . $width3 . 'text-align: left;"><b style="font-size:11px;">' . $dataItem->code . '</b><br/><i style="font-size:11px;">' . $dataItem->name . '</i></td>';
			$table .= '<td style="' . $width3_1 . 'text-align: right;">' . formatNumber($value->money_start) . '</td>';
			$table .= '<td style="' . $width3_1 . 'text-align: right;">' . formatNumber($value->money_end) . '</td>';
			$table .= '<td style="' . $width4 . 'text-align: right;">' . formatNumber($value->price) . '</td>';
			$table .= '</tr>';
		}
		$table .= '</tbody></table>';
		$data->content .= $table;
		$pdf = print_pdf($data);
		$type = 'I';
		$pdf->Output(slug_it('') . '.pdf', $type);
	}

	public function print_pdf_discount($id = '', $client = '')
	{
		ob_start();
		$data = new stdClass();
		$this->db->select('tblgroup_price_discount.*, tblcustomers_groups.name as name_group');
		$this->db->where('tblgroup_price_discount.group_price_id', $id);
		$this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblgroup_price_discount.group_id');
		$discount = $this->db->get('tblgroup_price_discount')->row();
		$name_group = $this->db->get_where('tblclients', ['userid' => $client])->row('company');
		$_discount = !empty($discount->discount) ? $discount->discount : 0;
		//  $data->title = lang('Bảng giá nhà cung cấp');
		$dataSub = $this->import_price_group_model->show_list_detail($id);
		$main = $this->import_price_group_model->show_List($id);
		$table = '';
		$data->content = '';
		// $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
		$data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">BẢNG GIÁ</span><br><br>';
		// $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
		// $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
		$data->content .= '<span style="font-weight: bold;">' . _l('Nhóm khách hàng được áp dụng') . ': </span><span>' . $name_group . '</span><br>';
		$data->content .= '<span style="font-weight: bold;">' . _l('Tên bảng giá') . ': </span><span>' . $main->name_price . '</span><br>';
		$data->content .= '<span style="font-weight: bold;">' . _l('year') . ': </span><span>' . $main->year . '</span><br>';
		$data->content .= '<span style="font-weight: bold;">' . _l('Chiết khấu') . ': </span><span>' . $_discount . '(%)</span><br><br>';
		$width1 = 'width: 6%;';
		$width2 = 'width: 11%;';
		$width3 = 'width: 30%;';
		$width3_1 = 'width: 9%;';
		$width4 = 'width: 15%;';
		$width5 = 'width: 8%;';
		$width6 = 'width: 12%;';
		//		 8+5 + 2 + 3 = 18
		$table = '

            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td rowspan="2"  style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
		$table .= '<td rowspan="2" style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('Hình ảnh') . '</td>';
		$table .= '<td rowspan="2"  style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
		$table .= '<td colspan="2"  style="width:18%;text-align: center;font-weight: bold;">' . _l('MOQ') . '</td>';
		$table .= '<td rowspan="2"  style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('Giá theo BRAND') . '</td>';
		$table .= '<td rowspan="2"  style="' . $width5 . 'text-align: center;font-weight: bold;">' . _l('% Chiết khấu') . '</td>';
		$table .= '<td rowspan="2"  style="' . $width6 . 'text-align: center;font-weight: bold;">' . _l('Giá sau chiết khấu') . '</td>';
		$table .= '</tr>
					<tr>
						<td style="' . $width3_1 . 'text-align: center;font-weight: bold;">SL từ</td>
						<td style="' . $width3_1 . 'text-align: center;font-weight: bold;">SL đến</td>
					</tr>
                </thead>
                <tbody>';
		foreach ($dataSub as $key => $value) {
			$dataItem = @get_full_item($value->product_id, $value->product_type);
			if (empty($dataItem->id)) continue;
			$table .= '<tr nobr="true">';
			$table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
			$table .= '<td style="' . $width2 . 'text-align: center;"><img src="' . $dataItem->avatar_1 . '" width="50px" height="50px"></td>';
			$table .= '<td style="' . $width3 . 'text-align: left;">' . $dataItem->name . '(' . $dataItem->code . ')</td>';
			$table .= '<td style="' . $width3_1 . 'text-align: right;">' . formatNumber($value->money_start) . '</td>';
			$table .= '<td style="' . $width3_1 . 'text-align: right;">' . formatNumber($value->money_end) . '</td>';
			$table .= '<td style="' . $width4 . 'text-align: right;">' . formatNumber($value->price) . '</td>';
			$table .= '<td style="' . $width5 . 'text-align: right;">' . (!empty($discount) ? ($_discount . '(%)') : '') . '</td>';
			$table .= '<td style="' . $width6 . 'text-align: right;">' . formatNumber($value->price - $value->price * ($_discount / 100)) . '</td>';
			$table .= '</tr>';
		}
		$table .= '</tbody></table>';
		$data->content .= $table;
		$pdf = print_pdf($data);
		$type = 'I';
		$pdf->Output(slug_it('') . '.pdf', $type);
	}

	public function modal_add($group_price_id = '', $id_client = '')
	{
		$data['title'] = 'Sửa chiết khấu nhóm';
		if (!empty($group_price_id)) {
			$data['group_price_id'] = $group_price_id;
			$data['id_client'] = $id_client;
			//				$group_price = $this->db->get_where('tblgroup_price', ['id' => $group_price_id])->row();
			$data['group_price_discount'] = $this->db->get_where('tblgroup_price_discount', [
				'group_price_id' => $group_price_id,
				'client' => $id_client
			])->row();
			$data['clients'] = $this->db->get_where('tblclients', ['userid' => $id_client])->row();
			$this->load->view('admin/import_price_group/modal_add', $data);
		}
	}

	public function add_modal_child($id = '')
	{
		if ($this->input->post()) {
			$data = $this->input->post();
			$group_price_id = $data['group_price_id'];
			if (!empty($group_price_id)) {
				if (empty($id)) {
					$this->db->where('group_price_id', $group_price_id);
					$this->db->where('client', $data['client']);
					$group_price_discount = $this->db->get('tblgroup_price_discount')->row();
					if (!empty($group_price_discount)) {
						$this->db->where('tblgroup_price_discount.id', $group_price_discount->id);
						$success = $this->db->update('tblgroup_price_discount', [
							'group_price_id' => $group_price_id,
							'client' => $data['client'],
							'discount' => number_format_data_four($data['discount'], false),
							'create_by' => get_staff_user_id()
						]);
						if (!empty($success)) {
							echo json_encode([
								'success' => true,
								'alert_type' => 'success',
								'message' => 'Cập nhật thành công'
							]);
							die();
						}
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Cập nhật không thành công'
						]);
						die();
					} else {
						$success = $this->db->insert('tblgroup_price_discount', [
							'group_price_id' => $group_price_id,
							'client' => $data['client'],
							'discount' => number_format_data_four($data['discount'], false),
							'create_by' => get_staff_user_id()
						]);
						if (!empty($success)) {
							echo json_encode([
								'success' => true,
								'alert_type' => 'success',
								'message' => 'Cập nhật thành công'
							]);
							die();
						}
						echo json_encode([
							'success' => false,
							'alert_type' => 'danger',
							'message' => 'Cập nhật không thành công'
						]);
						die();
					}
				} else {
					$this->db->where('tblgroup_price_discount.id', $id);
					$success = $this->db->update('tblgroup_price_discount', [
						'group_price_id' => $group_price_id,
						'client' => $data['client'],
						'discount' => number_format_data_four($data['discount'], false),
						'create_by' => get_staff_user_id()
					]);
					if (!empty($success)) {
						echo json_encode([
							'success' => true,
							'alert_type' => 'success',
							'message' => 'Cập nhật thành công'
						]);
						die();
					}
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Cập nhật không thành công'
					]);
					die();
				}
			}
		}
		ajax_access_denied();
	}

	public function add_items()
	{
		$data = $this->input->post();
		$items_products = explode('__', $data['items_products']);
		$product_id = $items_products[0];
		$product_type = $items_products[1];
		$success = $this->db->insert('tblgroup_price_detail', [
			'price' => number_format_data_four($data['price'], false),
			'product_id' => $product_id,
			'product_type' => 'product',
			'group_price_id' => $data['group_price_id'],
			'money_start' => number_format_data_four($data['money_start'], false),
			'money_end' => number_format_data_four($data['money_end'], false),
		]);
		$dataSuccess = [
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Thêm không thành công'
		];
		if (!empty($success)) {
//			$id = $this->db->insert_id();
//
//			$this->db->select('tblgroup_price_detail.*,
//				tblitemsData.avatar,
//				tblitemsData.code_item as code_item,
//				tblitemsData.name_item as name_item,
//				tblitemsData.unit_name as unit_name,
//				tblitemsData.product_id as id_item_data,
//			');
//			$this->db->where('tblgroup_price_detail.id', $id);
//			$this->db->join(tableItemsShortMysql(), 'tblitemsData.product_id = tblgroup_price_detail.product_id
//			AND tblitemsData.type = tblgroup_price_detail.product_type', 'left');
//			$this->db->order_by('tblgroup_price_detail.id', 'desc');
//			$data_items = $this->db->get('tblgroup_price_detail')->row();
//			$dataTr = [];
//			if(!empty($data_items)) {
//				$dataTr[0] = '1<br/><br/><a class="btn btn-icon btn-danger" onclick="removeTrDetail('.$data_items->id.')"><i class="fa fa-remove"></i></a>';
//				$dataTr[1] = '<img src="' . $data_items->avatar . '" width="50px" height="50px" />';
//				$dataTr[2] = $data_items->code_item;
//				$dataTr[3] = $data_items->name_item;
//
//				$numberMoneyStart = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($data_items->money_start), $data_items->id, '', '<a class="pointer" id="money_start_text_v2_' . $data_items->id . '" target="_blank" >' . number_format_data_four($data_items->money_start) . '</a>', '', admin_url('import_price_group/update_money/start/' . $data_items->id . '/' . $import_price_group->id), 'class="formUpdateDataTable"')
//				$dataTr[4] = '<div class="type_v1">
//									</div>
/*								<div class="type_v2 hide" data-id="<?= $value->id ?>" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_start" class="height_auto  money_start H_input align_right" value="<?= number_format_data_four($value->price) ?>"></div>*/
//							';
//			}
			
			
			$dataSuccess = [
				'success' => true,
				'alert_type' => 'success',
				'message' => 'Thêm thành công',
//				'data' => $this->show_detail_price($data['group_price_id'], true)
			];
		}
		echo json_encode($dataSuccess);
		die();
	}


	public function export_excel()
	{
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		// print_arrays($this->input->post());
		// $cloumns = $this->input->post('cloumns');
		$style_excel = style_excel();
		$style_excel['border'] = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
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
		$numberRow = 2;
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", 'KHÁCH HÀNG')->getStyle("A$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", 'TÊN BẢNG GIÁ')->getStyle("B$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", 'MÃ SẢN PHẨM')->getStyle("C$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", 'TÊN SẢN PHẨM')->getStyle("D$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", 'MOQ TỪ')->getStyle("E$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", 'MOQ ĐẾN')->getStyle("F$numberRow")->applyFromArray($style_excel['Background_header']);
		$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", 'GIÁ')->getStyle("G$numberRow")->applyFromArray($style_excel['Background_header']);
		$numberRow++;
		// $stt = 1;
		$this->db->select('tblgroup_price.id');
		$db_results = $this->db->get('tblgroup_price')->result_array();
		if (!empty($db_results)) {
			foreach ($db_results as $key => $value) {
				$id = $value['id'];
				$data['import_price_group'] = $this->import_price_group_model->show_List($id);
				$data['items'] = $this->import_price_group_model->show_list_detail($id);
				$company = $data['import_price_group']->company;
				$name_price = $data['import_price_group']->name_price;
				foreach ($data['items'] as $item) {
					$_item = @get_items($item->product_id, $item->product_type);
					$objPHPExcel->getActiveSheet()->SetCellValue("A$numberRow", $company)->getStyle("A$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->SetCellValue("B$numberRow", $name_price)->getStyle("B$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->SetCellValue("C$numberRow", $_item->code)->getStyle("C$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->SetCellValue("D$numberRow", $_item->name)->getStyle("D$numberRow")->applyFromArray($style_excel['BStyle_left']);
					$objPHPExcel->getActiveSheet()->SetCellValue("E$numberRow", $item->money_start)->getStyle("E$numberRow")->applyFromArray($style_excel['border'])->getNumberFormat()->setFormatCode('#,##0.0###');
					$objPHPExcel->getActiveSheet()->SetCellValue("F$numberRow", $item->money_end)->getStyle("F$numberRow")->applyFromArray($style_excel['border'])->getNumberFormat()->setFormatCode('#,##0.0###');
					$objPHPExcel->getActiveSheet()->SetCellValue("G$numberRow", $item->price)->getStyle("G$numberRow")->applyFromArray($style_excel['border'])->getNumberFormat()->setFormatCode('#,##0.0###');
					$numberRow++;
				}
				// $stt++;
			}
		}
		$filename = lang('Bang_gia_theo_khach_hang') . '.xls';
		$objPHPExcel->getActiveSheet()->freezePane('A1');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function remove_items()
	{
		$data = $this->input->post();
		$id = $data['id'];
		$dataSuccess = [
			'success' => false,
			'alert_type' => 'danger',
			'message' => 'Xóa không thành công'
		];
		$this->db->where('id', $id);
		$group_price_detail = $this->db->get('tblgroup_price_detail')->row();
		if (!empty($group_price_detail)) {
			$this->db->where('id', $id);
			$success = $this->db->delete('tblgroup_price_detail');
			if (!empty($success)) {
				$dataSuccess = [
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Xóa thành công',
//					'data' => $this->show_detail_price($group_price_detail->group_price_id, true)
				];
			}
		}
		echo json_encode($dataSuccess);
		die();
	}

	public function UpdatePirceOrder($id = '')
	{
		// if(!is_admin()){
		// 	echo json_encode(array(
		// 		'success' => false,
		// 		'alert_type' => 'warning',
		// 		'message' => _l('Bạn không có quyền')
		// 	));
		// 	die;
		// }
		$tableprice = get_table_where('tblgroup_price', array('id' => $id), '', 'row');
		$this->db->select('tbl_order_items.*, tbl_products.conversion_unit, tbl_products.conversion_quantity_unit');
		// $this->db->where('tbl_order_items.is_updateprice', 1);
		$this->db->where('tbl_order_items.price', 0);
		$this->db->where('tbl_orders.customer_id', $tableprice->client);
		$this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"', 'left');
		$order_items = $this->db->get('tbl_order_items')->result_array();
		$order = array();
		$deli = array();
		foreach ($order_items as $key => $value) {
			$type_item = $value['type_item'];
			$item_id = $value['item_id'];
			$quantity = $value['quantity'];
			if ($value['type_item'] == "products") {
				$value['type_item'] = "product";
			} else if ($value['type_item'] == "items") {
				$value['type_item'] = "items";
			} else if ($value['type_item'] == "materials") {
				$value['type_item'] = "nvl";
			}
			
			$unit_conversion = 1;
			if(!empty($value['conversion_unit'])
				&& !empty($value['conversion_quantity_unit'])
				&& $value['conversion_quantity_unit'] != 0
				&& $value['conversion_unit'] == $value['unit_id']) {
				$unit_conversion =  1 / $value['conversion_quantity_unit'];
			}
			
			$price = $this->GetPiceProduct($id, $tableprice->client, $value['item_id'], $value['type_item'], $quantity);
			$price = $price * $unit_conversion;
			
			$is_lot = $this->isLotPiceProduct($id, $tableprice->client, $value['item_id'], $value['type_item'], $quantity);

			$ins = array();
			if ($is_lot) {
				$amount = $price;
			} else {
				$amount = $quantity * $price;
			}
			// $amount = $price * $quantity;
			$grand_total_item = $amount;
			$discount_percent_item = $value['discount_percent_item'];
			$discount_percent_amount_item = 0;
			$tax_rate_item = $value['tax_rate_item'];
			if ($discount_percent_item > 0) {
				$discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
				$grand_total_item -= $discount_percent_amount_item;
			}
			//end
			$discount_direct_amount_item = $value['discount_direct_amount_item'];
			$grand_total_item -= $discount_direct_amount_item;
			//handling cost temporary capital
			if ($type_item == "products") {
				$itemType = "product";
			} else if ($type_item == "items") {
				$itemType = "items";
			} else if ($type_item == "materials") {
				$itemType = "nvl";
			}
			$result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $item_id);
			$priceCost = 0;
			$pLast = 0;
			$cQuantity = $quantity;
			foreach ($result as $k => $val) {
				if ($cQuantity <= 0) break;
				$qty = $val['quantity_left'];
				$p = $val['price'];
				// $pLast = $p;
				$cQuantityTerm = $cQuantity;
				$cQuantity -= $qty;
				if ($cQuantity >= 0) {
					$pCost = $qty * $p;
				} else if ($cQuantity < 0) {
					$pCost = $cQuantityTerm * $p;
				}
				$priceCost += $pCost;
			}
			if ($cQuantity > 0) {
				$priceLast = $this->site_model->getPriceLast($itemType, $item_id);
				if (!empty($priceLast)) {
					$pLast = $priceLast['price'];
				}
				$priceCost += $cQuantity * $pLast;
			}
			//end handling cost temporary capital
			$cost_temporary_capital = $priceCost;
			$profit_temporary_capital = $grand_total_item - $priceCost;
			$tax_amount_item = 0;
			if ($tax_rate_item > 0) {
				$tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
				$grand_total_item += $tax_amount_item;
			}

			$ins['is_lot'] = $is_lot;
			$ins['price'] = $price;
			// $ins['amount'] = $price * $value['quantity'];
			$ins['amount'] = $amount;
			$ins['tax_amount_item'] = $tax_amount_item;
			$ins['discount_percent_amount_item'] = $discount_percent_amount_item;
			// $ins['discount_direct_amount_item'] = $discount_direct_amount_item;
			$ins['total_amount'] = $grand_total_item;
			$ins['cost_temporary_capital'] = $cost_temporary_capital;
			$ins['profit_temporary_capital'] = $profit_temporary_capital;
			$ins['is_updateprice'] = 1;
			$order[] = $value['order_id'];
			$this->db->update('tbl_order_items', $ins, array('id' => $value['id']));
			$deli = $this->updateItemsdeli($value['id'], $price, $deli);
			// echo '<pre>';
			// print_arrays($ins);
			// die;
		}
		$order = array_unique($order);
		foreach ($order as $key => $value) {
			$this->GetPiceOrder($value, $id);
		}
		$deli = array_unique($deli);
		foreach ($deli as $key => $value) {
			$this->GetPiceDeli($value, $id);
		}
		echo json_encode(array(
			'success' => false,
			'alert_type' => 'info',
			'message' => _l('Cập nhật thành công')
		));
		die;
	}

	public function updateItemsdeli($id_order_items = 0, $price = '', $deli = array())
	{
		$this->db->select('tbl_delivery_items.*, tbl_order_items.is_lot as is_lot');
		$this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
		$this->db->where('tbl_delivery_items.order_item_id', $id_order_items);
		$order_items = $this->db->get('tbl_delivery_items')->result_array();
		foreach ($order_items as $key => $value) {
			if ($value['is_lot']) {
				$amount = $price;
			} else {
				$amount = $price * $value['quantity'];
			}

			$discount_direct_amount_item = $value['discount_direct_amount_item'] ?? 0;
			$grand_total_item = $amount - $discount_direct_amount_item;

			//

			$ins = array();
			$ins['price'] = $price;
			$ins['amount'] = $amount;
			$ins['total_amount'] = $grand_total_item;
			$this->db->update('tbl_delivery_items', $ins, array('id' => $value['id']));
			$deli[] = $value['delivery_id'];
		}
		return $deli;
	}

	public function GetPiceOrder($id_order = 0, $table_price_id = 0)
	{
		$this->db->select('tbl_order_items.*');
		$this->db->where('tbl_order_items.order_id', $id_order);
		$order_items = $this->db->get('tbl_order_items')->result_array();
		$this->db->select('tbl_orders.*');
		$this->db->where('tbl_orders.id', $id_order);
		$order = $this->db->get('tbl_orders')->row();
		$discount_percent = $order->discount_percent;
		$total_discount_direct = $order->total_discount_direct;
		$cost_delivery = $order->cost_delivery;
		$tax_rate = $order->tax_rate;
		$charge_party = $order->charge_party;
		$total_amount_items = 0;
		$grand_total_items = 0;
		$total_cost_temporary_capital = 0;
		$total_tax_items = 0;
		$total_discount_percent_items = 0;
		$total_tax = 0;
		foreach ($order_items as $key => $value) {
			$total_amount_items += $value['amount'];
			$grand_total_items += $value['total_amount'];
			$total_cost_temporary_capital += $value['cost_temporary_capital'];
			$total_tax_items += $value['tax_amount_item'];
			$total_discount_percent_items += $value['discount_percent_amount_item'];
		}
		$total_discount_percent = 0;
		$grand_total = $grand_total_items;
		if ($discount_percent > 0) {
			$total_discount_percent = $grand_total * ($discount_percent / 100);
		}
		$grand_total -= $total_discount_percent;
		$grand_total -= $total_discount_direct;
		$total_profit_temporary_capital = $grand_total - $total_cost_temporary_capital;
		$total_profit_temporary_capital -= $cost_delivery;
		if ($tax_rate > 0) {
			$total_tax = $grand_total * ($tax_rate / 100);
		}
		$grand_total += $total_tax;
		if ($charge_party == "customer") {
			$grand_total += $cost_delivery;
		}
		$options = [
			'total_amount_items' => $total_amount_items,
			'total_tax_items' => $total_tax_items,
			'total_discount_percent_items' => $total_discount_percent_items,
			'grand_total_items' => $grand_total_items,
			'total_tax' => $total_tax, //tổng thuế
			'total_discount_percent' => $total_discount_percent, //tổng tiền chiết khấu phần trăm
			'total_discount_direct' => $total_discount_direct, //tổng tiền chiết khấu tiền mặt
			'grand_total' => $grand_total, //tổng tiền đơn hàng
			'table_price_id' => $table_price_id,
			'cost_delivery' => $cost_delivery,
			'total_cost_temporary_capital' => $total_cost_temporary_capital, //giá vốn tạm thời
			'total_profit_temporary_capital' => $total_profit_temporary_capital, //chi phí lợi nhuận tạm thời
		];
		$this->db->update('tbl_orders', $options, array('id' => $id_order));
	}

	public function GetPiceDeli($id_order = 0, $table_price_id = 0)
	{
		$this->db->select('tbl_delivery_items.*');
		$this->db->where('tbl_delivery_items.delivery_id', $id_order);
		$order_items = $this->db->get('tbl_delivery_items')->result_array();
		$this->db->select('tbl_deliveries.*');
		$this->db->where('tbl_deliveries.id', $id_order);
		$order = $this->db->get('tbl_deliveries')->row();

		$discount_percent = $order->discount_percent;
		$total_discount_direct = $order->total_discount_direct;
		$tax_rate = $order->tax_rate;
		$total_amount_items = 0;
		$grand_total_items = 0;
		$total_cost_temporary_capital = 0;
		$total_tax_items = 0;
		$total_discount_percent_items = 0;
		$total_tax = 0;
		foreach ($order_items as $key => $value) {
			$total_amount_items += $value['amount'];
			$grand_total_items += $value['total_amount'];
			$total_tax_items += $value['tax_amount_item'];
			$total_discount_percent_items += $value['discount_percent_amount_item'];
		}
		$total_discount_percent = 0;
		$grand_total = $grand_total_items;
		if ($discount_percent > 0) {
			$total_discount_percent = $grand_total * ($discount_percent / 100);
		}
		$grand_total -= $total_discount_percent;
		$grand_total -= $total_discount_direct;
		$total_profit_temporary_capital = $grand_total - $total_cost_temporary_capital;
		if ($tax_rate > 0) {
			$total_tax = $grand_total * ($tax_rate / 100);
		}
		$grand_total += $total_tax;
		
		//costs
		$additional_costs = $order->additional_costs;
		$grand_total+= $additional_costs;

		$options = [
			'total_amount_items' => $total_amount_items,
			'total_tax_items' => $total_tax_items,
			'total_discount_percent_items' => $total_discount_percent_items,
			'grand_total_items' => $grand_total_items,
			'total_tax' => $total_tax,
			'discount_percent' => $discount_percent,
			'total_discount_percent' => $total_discount_percent,
			'total_discount_direct' => $total_discount_direct,
			'grand_total' => $grand_total,
		];
		$this->db->update('tbl_deliveries', $options, array('id' => $id_order));
	}

	public function GetPiceProduct($table_price_id, $customer_id, $product_id, $type, $moq)
	{
		$priceItem = 0;
		$query = "(
			SELECT
				tblgroup_price_discount.group_price_id as group_price_id,
				tblgroup_price_discount.discount as discount
			FROM tblgroup_price_discount
			WHERE tblgroup_price_discount.group_price_id = $table_price_id AND tblgroup_price_discount.client = $customer_id
			LIMIT 1
		) tb_group_price_discount";
		$this->db->select('
			(tblgroup_price_detail.price - (tblgroup_price_detail.price * coalesce(tb_group_price_discount.discount, 0)/100)) as price,
		', false);
		$this->db->from('tblgroup_price_detail');
		$this->db->join($query, 'tb_group_price_discount.group_price_id = tblgroup_price_detail.group_price_id', 'left');
		$this->db->where('tblgroup_price_detail.group_price_id', $table_price_id);
		$this->db->where('tblgroup_price_detail.product_id', $product_id);
		$this->db->where('tblgroup_price_detail.product_type', $type);
		$this->db->where('(
			(tblgroup_price_detail.money_start <= ' . $moq . ' AND tblgroup_price_detail.money_end >= ' . $moq . ')
			OR
			(tblgroup_price_detail.money_start = 0 AND tblgroup_price_detail.money_end = 0)
			OR
			(tblgroup_price_detail.money_start <= ' . $moq . ' AND tblgroup_price_detail.money_end = 0)
			OR
			(tblgroup_price_detail.money_end >= ' . $moq . ' AND tblgroup_price_detail.money_start = 0)
		)', false, false);
		$this->db->order_by('tblgroup_price_detail.money_start DESC');
		$rs = $this->db->get()->row_array();

		if (!empty($rs)) {
			$priceItem = $rs['price'];
		}
		return $priceItem;
	}

	public function isLotPiceProduct($table_price_id, $customer_id, $product_id, $type, $moq)
	{
		$is_lot = 0;
		$query = "(
			SELECT
				tblgroup_price_discount.group_price_id as group_price_id,
				tblgroup_price_discount.discount as discount
			FROM tblgroup_price_discount
			WHERE tblgroup_price_discount.group_price_id = $table_price_id AND tblgroup_price_discount.client = $customer_id
			LIMIT 1
		) tb_group_price_discount";

		$this->db->select('
			tblgroup_price_detail.is_lot as is_lot,
		', false);
		$this->db->from('tblgroup_price_detail');
		$this->db->join($query, 'tb_group_price_discount.group_price_id = tblgroup_price_detail.group_price_id', 'left');
		$this->db->where('tblgroup_price_detail.group_price_id', $table_price_id);
		$this->db->where('tblgroup_price_detail.product_id', $product_id);
		$this->db->where('tblgroup_price_detail.product_type', $type);
		$this->db->where('(
			(tblgroup_price_detail.money_start <= ' . $moq . ' AND tblgroup_price_detail.money_end >= ' . $moq . ')
			OR
			(tblgroup_price_detail.money_start = 0 AND tblgroup_price_detail.money_end = 0)
			OR
			(tblgroup_price_detail.money_start <= ' . $moq . ' AND tblgroup_price_detail.money_end = 0)
			OR
			(tblgroup_price_detail.money_end >= ' . $moq . ' AND tblgroup_price_detail.money_start = 0)
		)', false, false);
		$this->db->order_by('tblgroup_price_detail.money_start DESC');
		$rs = $this->db->get()->row_array();

		if (!empty($rs)) {
			$is_lot = $rs['is_lot'];
		}
		return $is_lot;
	}

	public function UpdatePirceOrderDetail($id = '', $id_detail = '')
	{
		// if(!is_admin()){
		// 	echo json_encode(array(
		// 		'success' => false,
		// 		'alert_type' => 'warning',
		// 		'message' => _l('Bạn không có quyền')
		// 	));
		// 	die;
		// }
		$tableprice = $this->db->get_where('tblgroup_price', ['id' => $id])->row();
		$price_detail = $this->db->get_where('tblgroup_price_detail', ['id' => $id_detail])->row();
		if(empty($price_detail) || empty($tableprice)) {
			echo json_encode(array(
				'success' => false,
				'alert_type' => 'danger',
				'message' => _l('Không tìm thấy sản phẩm trong bảng giá')
			));
			die;
		}

		$this->db->select('tbl_order_items.*, tbl_products.conversion_unit, tbl_products.conversion_quantity_unit');
//		$this->db->where('tbl_order_items.price', 0);
		if ($price_detail->product_type == "product") {
			$this->db->where('tbl_order_items.type_item', 'products');
		}
		else if ($price_detail->product_type == "materials") {
			$this->db->where('tbl_order_items.type_item', 'nvl');
		}
		else {
			$this->db->where('tbl_order_items.type_item', $price_detail->product_type);
		}
		$this->db->where('tbl_order_items.item_id', $price_detail->product_id);
		$this->db->where('tbl_orders.customer_id', $tableprice->client);
		$this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"', 'left');
		$order_items = $this->db->get('tbl_order_items')->result_array();
		$order = array();
		$deli = array();
		foreach ($order_items as $key => $value) {
			$type_item = $value['type_item'];
			$item_id = $value['item_id'];
			$quantity = $value['quantity'];
			if ($value['type_item'] == "products") {
				$value['type_item'] = "product";
			} else if ($value['type_item'] == "items") {
				$value['type_item'] = "items";
			} else if ($value['type_item'] == "materials") {
				$value['type_item'] = "nvl";
			}
			
			
			$unit_conversion = 1;
			if(!empty($value['conversion_unit'])
				&& !empty($value['conversion_quantity_unit'])
				&& $value['conversion_quantity_unit'] != 0
				&& $value['conversion_unit'] == $value['unit_id']) {
				$unit_conversion =  1 / $value['conversion_quantity_unit'];
			}
			
			
			$price = $this->GetPiceProduct($id, $tableprice->client, $value['item_id'], $value['type_item'], $quantity);
			$price = $price * $unit_conversion;
			
			$ins = array();

			$is_lot = $this->isLotPiceProduct($id, $tableprice->client, $value['item_id'], $value['type_item'], $quantity);
			if ($is_lot) {
				$amount = $price;
			} else {
				$amount = $quantity * $price;
			}

			// $amount = $price * $quantity;
			$grand_total_item = $amount;
			$discount_percent_item = $value['discount_percent_item'];
			$discount_percent_amount_item = 0;
			$tax_rate_item = $value['tax_rate_item'];
			if ($discount_percent_item > 0) {
				$discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
				$grand_total_item -= $discount_percent_amount_item;
			}
			//end
			$discount_direct_amount_item = $value['discount_direct_amount_item'];
			$grand_total_item -= $discount_direct_amount_item;
			//handling cost temporary capital
			if ($type_item == "products") {
				$itemType = "product";
			} else if ($type_item == "items") {
				$itemType = "items";
			} else if ($type_item == "materials") {
				$itemType = "nvl";
			}
			$result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $item_id);
			$priceCost = 0;
			$pLast = 0;
			$cQuantity = $quantity;
			foreach ($result as $k => $val) {
				if ($cQuantity <= 0) break;
				$qty = $val['quantity_left'];
				$p = $val['price'];
				// $pLast = $p;
				$cQuantityTerm = $cQuantity;
				$cQuantity -= $qty;
				if ($cQuantity >= 0) {
					$pCost = $qty * $p;
				} else if ($cQuantity < 0) {
					$pCost = $cQuantityTerm * $p;
				}
				$priceCost += $pCost;
			}
			if ($cQuantity > 0) {
				$priceLast = $this->site_model->getPriceLast($itemType, $item_id);
				if (!empty($priceLast)) {
					$pLast = $priceLast['price'];
				}
				$priceCost += $cQuantity * $pLast;
			}
			//end handling cost temporary capital
			$cost_temporary_capital = $priceCost;
			$profit_temporary_capital = $grand_total_item - $priceCost;
			$tax_amount_item = 0;
			if ($tax_rate_item > 0) {
				$tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
				$grand_total_item += $tax_amount_item;
			}

			$ins['is_lot'] = $is_lot;
			$ins['price'] = $price;
			// $ins['amount'] = $price * $value['quantity'];
			$ins['amount'] = $amount;
			$ins['tax_amount_item'] = $tax_amount_item;
			$ins['discount_percent_amount_item'] = $discount_percent_amount_item;
			// $ins['discount_direct_amount_item'] = $discount_direct_amount_item;
			$ins['total_amount'] = $grand_total_item;
			$ins['cost_temporary_capital'] = $cost_temporary_capital;
			$ins['profit_temporary_capital'] = $profit_temporary_capital;
			$ins['is_updateprice'] = 1;
			$order[] = $value['order_id'];
			$this->db->update('tbl_order_items', $ins, array('id' => $value['id']));
			$deli = $this->updateItemsdeli($value['id'], $price, $deli);
		}
		$order = array_unique($order);
		foreach ($order as $key => $value) {
			$this->GetPiceOrder($value, $id);
		}
		$deli = array_unique($deli);
		foreach ($deli as $key => $value) {
			$this->GetPiceDeli($value, $id);
		}
		echo json_encode(array(
			'success' => false,
			'alert_type' => 'info',
			'message' => _l('Cập nhật thành công')
		));
		die;
	}

	public function changeIsLot() {
		$data = [];
		if (!has_permission('import_price_group', '', 'create')) {
			$data['result'] = 0;
			$data['message'] = lang('access_denied');
			echo json_encode($data); die;
		}

		$is_lot = $this->input->post('is_lot');
		$id_detail = $this->input->post('id_detail');
		if ($is_lot === true || $is_lot === 'true' || $is_lot === 1 || $is_lot === '1') {
			$is_lot = 1;
		} else {
			$is_lot = 0;
		}

		$option = [
			'is_lot' => $is_lot
		];

		$this->db->where('tblgroup_price_detail.id', $id_detail);
		$this->db->update('tblgroup_price_detail', $option);

		$data['result'] = 1;
		$data['message'] = lang('success');
		echo json_encode($data);
	}
	
	public function table_import_group_deail($id) {
		$aColumns = [
			'tblgroup_price_detail.id as id',
			'tblitemsData.avatar as avatar',
			'tblitemsData.code_item as code_item',
			'tblitemsData.name_item as name_item',
			'tblitemsData.unit_name_payment as unit_name_payment',
			'tblcurrencies.name as name_curren',
			'tblgroup_price_detail.money_start as money_start',
			'tblgroup_price_detail.money_end as money_end',
			'tblgroup_price_detail.price as price',
			'tblgroup_price_detail.is_lot as is_lot',
		];
		
		$sWhere = [];
		array_push($sWhere, 'AND tblgroup_price_detail.group_price_id = "'.$id.'"');
		$Join = [
			'JOIN '.tableItemsShortMysql().' ON tblitemsData.product_id = tblgroup_price_detail.product_id AND tblitemsData.type = tblgroup_price_detail.product_type',
			'LEFT JOIN tblgroup_price ON tblgroup_price.id = tblgroup_price_detail.group_price_id',
			'LEFT JOIN tblclients ON tblclients.userid = tblgroup_price.client',
			'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblclients.currency',
		];
		$sIndexColumn = 'id';
		$sTable       = 'tblgroup_price_detail';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $Join, $sWhere, [
			'tblgroup_price_detail.product_type','tblgroup_price_detail.product_id'
		], 'order by tblgroup_price_detail.id desc');
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
			
			$isLot = '<div><div class="checkbox checkbox-danger" style="padding-left: 10px;">
				<input type="checkbox" onchange="changeIsLot(this, '.$aRow['id'].')" '.($aRow['is_lot'] == 1 ? 'checked' : '').' name="is_lot" class="is_lot" id="is_lot_'.$aRow['id'].'" value="1">
				<label for="is_lot_'.$aRow['id'].'">'.lang('tnh_is_lot').'</label>
			</div></div>';

			$row[] = $aRow['code_item'].$isLot;
			$row[] = $aRow['name_item'];
			$row[] = $aRow['unit_name_payment'];
			$row[] = $aRow['name_curren'];
			$moneyStart = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($aRow['money_start']), $aRow['id'], '', '<a class="pointer" id="money_start_text_v2_' . $aRow['id'] . '" target="_blank" >' . number_format_data_four($aRow['money_start']) . '</a>', '', admin_url('import_price_group/update_money/start/' . $aRow['id'] . '/' . $id), 'class="formUpdateDataTable"');
			$row[] = '<div class="text-right"  data-id="money_start_text_v2_">
						<div class="type_v1">
							'.$moneyStart.'
							<div class="type_v2 hide" data-id="'.$aRow['id'].'" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_start" class="height_auto  money_start H_input align_right" value="'.number_format_data_four($aRow['price']).'"></div>
						 </div>
					 </div>';
			
			$moneyEnd = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($aRow['money_end']), $aRow['id'], '', '<a class="pointer" id="money_end_text_v2_' . $aRow['id'] . '" target="_blank" >' . number_format_data_four($aRow['money_end']) . '</a>', '', admin_url('import_price_group/update_money/end/' . $aRow['id'] . '/' . $id), 'class="formUpdateDataTable"');
			$row[] = '<div class="text-right"  data-id="money_start_text_v2_">
						<div class="type_v1">
							'.$moneyEnd.'
							<div class="type_v2 hide" data-id="'.$aRow['id'].'" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="money_start" id="money_end" class="height_auto  money_end H_input align_right" value="'.number_format_data_four($aRow['price']).'"></div>
						 </div>
					 </div>';
			
			$moneyPrice = dt_EditColumSelectInput_pricesupplier_Four(number_format_data_four($aRow['price']), $aRow['id'], '', '<a class="pointer" id="quantitys_text_v2_' . $aRow['id'] . '" target="_blank" >' . number_format_data_four($aRow['price']) . '</a>', '', admin_url('import_price_group/quantity/' . $aRow['id'] . '/' . $id), 'class="formUpdateDataTable"');
			$row[] = '<div class="text-right"  data-id="money_start_text_v2_">
						<div class="type_v1">
							'.$moneyPrice.'
							<div class="type_v2 hide" class="quantitys_input" data-id="'.$aRow['id'].'" ><input onkeyup="formatNumBerKeyUpCusFour(this)" type="text" name="quantitys" id="quantitys" class="height_auto quantitys H_input align_right" value="'.number_format_data_four($aRow['price']).'"></div>
						 </div>
					 </div>';
			$row[] = '<div class="text-center">' .
							format_item_purchases($aRow['product_type']).'<br/><a href="#" class="btn btn-info btn-icon mtop5" onclick="updatepriceDetail('.$id.', '.$aRow['id'].'); return false;">Cập nhật giá đơn hàng</a>
					</div>';
			$output['aaData'][] = $row;
		}
		
		echo json_encode($output);die();
		
	}

	public function updatePriceOrders($order_id, $price = 0, $update_is_lot_zero = 0, $update_is_lot_un_zero = 0) {

		$this->db->select('tbl_order_items.*, tbl_products.conversion_unit, tbl_products.conversion_quantity_unit, tbl_orders.table_price_id', false);
		$this->db->where('tbl_order_items.type_item', 'products');
		$this->db->where('tbl_order_items.order_id', $order_id);
		$this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"', 'left');
		$order_items = $this->db->get('tbl_order_items')->result_array();
		$order = array();
		$deli = array();
		$table_price_id = 0;

		foreach ($order_items as $key => $value) {
			$type_item = $value['type_item'];
			$item_id = $value['item_id'];
			$quantity = $value['quantity'];
			if ($value['type_item'] == "products") {
				$value['type_item'] = "product";
			} else if ($value['type_item'] == "items") {
				$value['type_item'] = "items";
			} else if ($value['type_item'] == "materials") {
				$value['type_item'] = "nvl";
			}
			$table_price_id = $value['table_price_id'];
			$is_lot = $value['is_lot'];
			if (!empty($update_is_lot_zero)) {
				$is_lot = 0;
			}

			if (!empty($update_is_lot_un_zero)) {
				$is_lot = 1;
			}

			$unit_conversion = 1;
			if(!empty($value['conversion_unit'])
				&& !empty($value['conversion_quantity_unit'])
				&& $value['conversion_quantity_unit'] != 0
				&& $value['conversion_unit'] == $value['unit_id']) {
				$unit_conversion =  1 / $value['conversion_quantity_unit'];
			}
			
			
			// $price = $this->GetPiceProduct($id, $tableprice->client, $value['item_id'], $value['type_item'], $quantity);
			$price = $price * $unit_conversion;
			
			$ins = array();
			// $is_lot = $this->isLotPiceProduct($id, $tableprice->client, $value['item_id'], $value['type_item'], $quantity);
			if ($is_lot) {
				$amount = $price;
			} else {
				$amount = $quantity * $price;
			}

			// $amount = $price * $quantity;
			$grand_total_item = $amount;
			$discount_percent_item = $value['discount_percent_item'];
			$discount_percent_amount_item = 0;
			$tax_rate_item = $value['tax_rate_item'];
			if ($discount_percent_item > 0) {
				$discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
				$grand_total_item -= $discount_percent_amount_item;
			}
			//end
			$discount_direct_amount_item = $value['discount_direct_amount_item'];
			$grand_total_item -= $discount_direct_amount_item;
			//handling cost temporary capital
			if ($type_item == "products") {
				$itemType = "product";
			} else if ($type_item == "items") {
				$itemType = "items";
			} else if ($type_item == "materials") {
				$itemType = "nvl";
			}

			$result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $item_id);
			$priceCost = 0;
			$pLast = 0;
			$cQuantity = $quantity;
			foreach ($result as $k => $val) {
				if ($cQuantity <= 0) break;
				$qty = $val['quantity_left'];
				$p = $val['price'];
				// $pLast = $p;
				$cQuantityTerm = $cQuantity;
				$cQuantity -= $qty;
				if ($cQuantity >= 0) {
					$pCost = $qty * $p;
				} else if ($cQuantity < 0) {
					$pCost = $cQuantityTerm * $p;
				}
				$priceCost += $pCost;
			}
			if ($cQuantity > 0) {
				$priceLast = $this->site_model->getPriceLast($itemType, $item_id);
				if (!empty($priceLast)) {
					$pLast = $priceLast['price'];
				}
				$priceCost += $cQuantity * $pLast;
			}
			//end handling cost temporary capital
			$cost_temporary_capital = $priceCost;
			$profit_temporary_capital = $grand_total_item - $priceCost;
			$tax_amount_item = 0;
			if ($tax_rate_item > 0) {
				$tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
				$grand_total_item += $tax_amount_item;
			}

			$ins['is_lot'] = $is_lot;
			$ins['price'] = $price;
			$ins['amount'] = $amount;
			$ins['tax_amount_item'] = $tax_amount_item;
			$ins['discount_percent_amount_item'] = $discount_percent_amount_item;
			$ins['total_amount'] = $grand_total_item;
			$ins['cost_temporary_capital'] = $cost_temporary_capital;
			$ins['profit_temporary_capital'] = $profit_temporary_capital;
			$ins['is_updateprice'] = 1;
			$order[] = $value['order_id'];
			$this->db->update('tbl_order_items', $ins, array('id' => $value['id']));
			$deli = $this->updateItemsdeli($value['id'], $price, $deli);
		}

		$order = array_unique($order);
		foreach ($order as $key => $value) {
			$this->GetPiceOrder($value, $table_price_id);
		}

		$deli = array_unique($deli);
		foreach ($deli as $key => $value) {
			$this->GetPiceDeli($value, $table_price_id);
		}

		echo json_encode(array(
			'success' => false,
			'alert_type' => 'info',
			'message' => _l('Cập nhật thành công')
		), JSON_UNESCAPED_UNICODE); die;
	}
}
