<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Manufacture extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('manufacture_model');
		$this->load->model('manufactures_model');
		$this->load->model('items_model');
		$this->load->model('products_model');
		$this->load->model('items_model');
		$this->load->model('unit_model');
		$this->load->model('category_model');
		$this->load->model('departments_model');
		$this->load->model('tools_supplies_model');
		$this->perView = has_permission('manufacture', '', 'view');
		$this->perViewOwn = has_permission('manufacture', '', 'view_own');
		$this->perAdd = has_permission('manufacture', '', 'create');
		$this->perEdit = has_permission('manufacture', '', 'edit');
		$this->perDelete = has_permission('manufacture', '', 'delete');
		$this->perApprove_warehouse = has_permission('manufacture', '', 'approve');
	}

	public function index()
	{
		if (!$this->perView && !$this->perViewOwn) {
			access_denied();
		}
		$data['tnh'] = true;
		$data['title'] = lang('manufacture');
		$this->load->view('admin/manufacture/index', $data);
	}

	public function getReference($field)
	{
		$CI = &get_instance();
		$q = $CI->db->get_where('tbl_order_ref', array('ref_id' => '1'), 1);
		if ($q->num_rows() > 0) {
			$ref = $q->row();
			switch ($field) {
				case 'productions_plan':
					$prefix = get_option('prefix_productions_plan');
					break;
				case 'business_plan':
					$prefix = get_option('prefix_business_plan');
					break;
				case 'productions_capacity':
					$prefix = get_option('prefix_productions_capacity');
					break;
				case 'productions_orders':
					$prefix = get_option('prefix_productions_orders');
					break;
				case 'productions_orders_details':
					$prefix = get_option('prefix_productions_orders_details');
					break;
				case 'quotes':
					$prefix = get_option('prefix_quotes');
					break;
				case 'suggest_exporting':
					$prefix = get_option('prefix_suggest_exporting');
					break;
				case 'stock':
					$prefix = get_option('prefix_stock');
					break;
				case 'orders':
					$prefix = get_option('prefix_orders');
					break;
				case 'deliveries':
					$prefix = get_option('prefix_deliveries');
					break;
				case 'export_warehouses':
					$prefix = get_option('prefix_export_warehouses');
					break;
				case 'purchase_products':
					$prefix = get_option('prefix_purchase_products');
					break;
				case 'warehousing':
					$prefix = get_option('prefix_warehousing');
					break;
				case 'purchase_internal':
					$prefix = get_option('prefix_purchase_internal');
					break;
				case 'qc':
					$prefix = get_option('prefix_qc');
					break;
				case 'returned_goods':
					$prefix = get_option('prefix_returned_goods');
					break;
				case 'manufactures':
					$prefix = get_option('prefix_manufactures');
					break;
				case 'manufacture':
					$prefix = get_option('CK-');
					break;
				default:
					$prefix = '';
			}
			$separator = get_option('separator');
			$format_date_prefix = get_option('format_date_prefix');
			$ref_no = (!empty($prefix)) ? $prefix . "$separator" : '';
			$ref_no .= date("$format_date_prefix") . sprintf("%04s", $ref->{$field});
			return $ref_no;
		}
		return FALSE;
	}

	public function add1()
	{
		if (!$this->perAdd) {
			accessDenied($js = true);
		}
		if ($this->input->post('add')) {
			$this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required|is_unique[tbl_manufactures.reference_no]');
			$this->form_validation->set_rules('date', lang("date"), 'required');
			if ($this->form_validation->run() == true) {
				$reference_no = $this->getReference('manufacture');
				$date = to_sql_date($this->input->post('date'), true);
				$note = $this->input->post('note', false);
				$count_items = 0;
				$total_quantity = 0;
				$items = [];
				$items_id = $this->input->post('items_id');
				$id_production_detail = $this->input->post('id_production_detail');

				if (empty($id_production_detail)) {
					$data['result'] = 0;
					$data['message'] = lang('Vui lòng nhập phiếu lệnh sản xuất chi tiết');
					echo json_encode($data);
					die;
				}


				if (!empty($items_id)) {
					$index = 0;
					foreach ($items_id as $key => $value) {
						$arrs = explode('__', $value);
						$item_id = $arrs[0];
						$type_item = $arrs[1];
						$quantity = number_unformat($this->input->post('quantity')[$key]);
						$warehouses = $this->input->post('warehouses')[$key];
						// if (empty($warehouses)) {
						// 	$data['result'] = 0;
						// 	$data['message'] = lang('Vui lòng chọn kho mặt hàng');
						// 	echo json_encode($data);
						// 	die;
						// }

						$note_items = $this->input->post('note_items')[$key];
						$warehouses = explode('__', $warehouses);
						$warehouse_ids = WAREHOUSES_CAPACITY; //de a hoang gan
						$location_ids = LOCATIONS_DEFAULT_MANUFACTURES; //de a hoang gan
						$item_id_bom = !empty($this->input->post('item_id_bom')[$key]) ? $this->input->post('item_id_bom')[$key] : '';
						$arrBOM = [];
						$totalBOM = 0;
						if (!empty($item_id_bom)) {
							foreach ($item_id_bom as $k => $val) {
								$arrs1 = explode('__', $val);
								$item_id1 = $arrs1[0];
								$type_item1 = $arrs1[1];
								$warehouses_items = $this->input->post('warehouses_items')[$key][$k];
								$quantity_bom = number_unformat($this->input->post('quantity_bom')[$key][$k]);
								if (empty($warehouses_items)) {
									$data['result'] = 0;
									$data['message'] = lang('Vui lòng chọn kho mặt hàng BOM');
									echo json_encode($data);
									die;
								}
								// $warehouses = explode('__', $warehouses_items);
								$warehouses_id = get_table_where('tblwarehouse_items', array('id' => $warehouses_items), '', 'row');
								$warehouse_id = $warehouses_id->warehouse_id;
								$location_id = $warehouses_id->localtion;
								// $dtWProduct = $this->manufacture_model->getWarehouseProductById($warehouses_items);
								$warehouse_item_id = $warehouse_id;
								$location_item_id = $location_id;
								$quantity_stock =  $quantity_bom;
								if ($type_item1 == 'materials') {
									$data_items = get_items($item_id1, 'nvl');
									$recipe = $data_items->recipe;
									$paper = $data_items->paper;
									$longs = $data_items->longs;
									$wide = $data_items->wide;
									$exchange_unit = $data_items->exchange_unit;    //chuan
									$exchange_standard_unit = $data_items->exchange_standard_unit; //kho
									$exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
									$quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
									if ($recipe == 1) {
										$quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_standard_unit;
									} elseif ($recipe == 2) {
										$quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
									} elseif ($recipe == 3) {
										$quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
									}
								} else {
									$recipe = 1;
									$paper = 1;
									$longs = 1;
									$wide = 1;
									$exchange_unit = 1;    //chuan
									$exchange_standard_unit = 1; //kho
									$exchange_unit_payment = 1; //thanh toan
									$quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
									$quantity_payment = ($quantity_unit / $exchange_unit_payment) / $exchange_standard_unit;
								}
								$info_items = array();
								$info_items['recipe'] = $recipe;
								$info_items['paper'] = $paper;
								$info_items['longs'] = $longs;
								$info_items['wide'] = $wide;
								$info_items_text = json_encode($info_items);
								$arrBOM[] = [
									'item_id' => $item_id1,
									'type_items' => $type_item1,
									'warehouse_item_id' => $warehouse_item_id,
									'location_item_id' => $location_item_id,
									'quantity_item' => $quantity_bom,
									'warehouse_product_id' => $warehouses_items,
									'quantity_stock' => $quantity_stock,
									'quantity_unit' => $quantity_unit,
									'quantity_payment' => $quantity_payment,
									'info_items' => $info_items_text,
									'lot_code' => $warehouses_id->lot_code,
									'date_sx' => $warehouses_id->date_sx,
									'date_sd' => $warehouses_id->date_sd,
									'date_use' => $warehouses_id->date_use,
								];
								$totalBOM += $quantity_bom;
							}
						}
						if ($quantity < 1) {
							$data['result'] = 0;
							$data['message'] = lang('Vui lòng kiểm tra số lượng nhập');
							echo json_encode($data);
							die;
						}

						$quantity_stock_main =  $quantity;
						if ($type_item == 'materials') {
							$data_items = get_items($item_id, 'nvl');
							$recipe_main = $data_items->recipe;
							$paper_main = $data_items->paper;
							$longs_main = $data_items->longs;
							$wide_main = $data_items->wide;
							$exchange_unit_main = $data_items->exchange_unit;    //chuan
							$exchange_standard_unit_main = $data_items->exchange_standard_unit; //kho
							$exchange_unit_payment_main = $data_items->exchange_unit_payment; //thanh toan
							$quantity_unit_main = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
							if ($recipe == 1) {
								$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $exchange_standard_unit_main;
							} elseif ($recipe == 2) {
								$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $paper_main / 100;
							} elseif ($recipe == 3) {
								$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * ($longs_main  * $wide_main) / 10000;
							}
						} else {
							$recipe_main = 1;
							$paper_main = 1;
							$longs_main = 1;
							$wide_main = 1;
							$exchange_unit_main = 1;    //chuan
							$exchange_standard_unit_main = 1; //kho
							$exchange_unit_payment_main = 1; //thanh toan
							$quantity_unit_main = ($quantity_stock_main * $exchange_standard_unit_main) / $exchange_unit_main;
							$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) / $exchange_standard_unit_main;
						}
						$info_items_main = array();
						$info_items_main['recipe'] = $recipe_main;
						$info_items_main['paper'] = $paper_main;
						$info_items_main['longs'] = $longs_main;
						$info_items_main['wide'] = $wide_main;
						$info_items_text_main = json_encode($info_items_main);

						$items[$index] = [
							'item_id' => $item_id,
							'type_items' => $type_item,
							'warehouse_id' => $warehouse_ids,
							'location_id' => $location_ids,
							'quantity' => $quantity,
							'note_item' => $note_items,
							'arrBOM' => $arrBOM,
							'quantity_stock' => $quantity_stock_main,
							'quantity_unit' => $quantity_unit_main,
							'quantity_payment' => $quantity_payment_main,
							'info_items' => $info_items_text_main,
						];
						$count_items++;
						$total_quantity += $quantity;
						$index++;
					}
				}
				if (empty($items)) {
					$data['result'] = 0;
					$data['message'] = lang('tnh_not_items');
					echo json_encode($data);
					die;
				}
				$options = [
					'date' => $date,
					'reference_no' => $reference_no,
					'count_items' => $count_items,
					'total_quantity' => $total_quantity,
					'status' => 1,
					'created_by' => get_staff_user_id(),
					'date_created' => date('Y-m-d H:i:s'),
					'note' => $note,
					'id_production_detail' => $id_production_detail
				];
				$manufactures_id = $this->manufacture_model->insertManufactures($options);
				if ($manufactures_id) {
					updateReference('manufacture');
					$arrManufacturesItemsBOM = [];
					foreach ($items as $key => $value) {
						$arrBOM = $value['arrBOM'];
						$value['manufactures_id'] = $manufactures_id;
						unset($value['arrBOM']);
						$manufactures_items_id = $this->manufacture_model->insertManufacturesItems($value);
						if (!empty($manufactures_items_id)) {
							if (!empty($arrBOM)) {
								foreach ($arrBOM as $k => $v) {
									$v['manufactures_id'] = $manufactures_id;
									$v['manufactures_items_id'] = $manufactures_items_id;
									$arrManufacturesItemsBOM[] = $v;
								}
							}
						}
					}
					if (!empty($arrManufacturesItemsBOM)) {
						$this->manufacture_model->insertManufacturesItemsBOMBatch($arrManufacturesItemsBOM);
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
			die;
		} else {
			$data['title'] = lang('add_manufacture');
			$data['breadcrumb'] = [
				array(
					'link' => base_url('admin/manufacture'),
					'page' => lang('manufacture'),
				),
				array('link' => '#', 'page' => lang('add_manufacture')),
			];

			$this->db->select(['id', 'reference_no']);
			$data['productions_detail'] = $this->db->get_where('tbl_productions_orders_details')->result_array();

			$this->load->view('admin/manufacture/add', $data);
		}
	}

	public function add($po_id_link = 0)
	{
		if (!$this->perAdd) {
			accessDenied($js = true);
		}
		if ($this->input->post('add')) {
			$this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required|is_unique[tbl_manufactures.reference_no]');
			$this->form_validation->set_rules('date', lang("date"), 'required');
			if ($this->form_validation->run() == true) {
				$reference_no = getReference('manufacture');
				$date = to_sql_date($this->input->post('date'), true);
				$note = $this->input->post('note', false);
				$count_items = 0;
				$total_quantity = 0;
				$items = [];
				$items_id = $this->input->post('items_id');
				// $id_production_detail = $this->input->post('id_production_detail');

				// if (empty($id_production_detail)) {
				// 	$data['result'] = 0;
				// 	$data['message'] = lang('Vui lòng nhập phiếu lệnh sản xuất chi tiết');
				// 	echo json_encode($data);
				// 	die;
				// }

				$po_id = $this->input->post('po_id');
				if (empty($po_id)) {
					$data['result'] = 0;
					$data['message'] = lang('Vui lòng chọn lệnh sản xuất tổng');
					echo json_encode($data);
					die;
				}

				$items = $this->input->post('items');
				$arrItems = [];
				if (!empty($items)) {
					foreach ($items as $key => $value) {
						$item_id = $value['item_id'];
						if (empty($item_id)) continue;
						$info = [];
						$arr_item_id = explode('__', $item_id);
						$_item_id = $arr_item_id[1];
						$_item_type = $arr_item_id[0];
						if ($_item_type == "materials") {
							$info = $this->items_model->rowMaterial($_item_id);
						} else {
							$info = $this->products_model->rowProduct($_item_id);
						}

						if (empty($value['detail'])) {
							$data['result'] = 0;
							$data['message'] = lang('NPL không có chi tiết mặt hàng');
							echo json_encode($data);
							die;
						}

						$arrDetail = [];
						foreach ($value['detail'] as $k => $val) {
							$arrDetail[] = [
								'poi_id' => $val['poi_id'],
								'product_id' => $val['product_id'],
								'quantity_order' => $val['quantity_order'],
								'landscape_print_size' => $val['landscape_print_size'],
								'vertical_print_size' => $val['vertical_print_size'],
								'number_children_size' => number_unformat($val['number_children_size'], false),
								'paper_exchange' => number_unformat($val['paper_exchange'], false),
								'quantity_single' => number_unformat($val['quantity_single'], false),
								'quantity_primary' => number_unformat($val['quantity_primary'], false),
								'quantity' => number_unformat($val['quantity'], false),
								'quantity_compensation' => number_unformat($val['quantity_compensation'], false),
								'quantity_compensation_primary' => number_unformat($val['quantity_compensation_primary'], false),
								'stage_id' => $val['stage_id'],
							];
						}

						$arrItems[] = [
							'item_id' => $_item_id,
							'item_type' => $_item_type,
							'item_code' => $info['code'],
							'item_name' => $info['name'],
							'quantity' => number_unformat($value['quantity'], false),
							'quantity_compensation' => number_unformat($value['quantity_compensation'], false),
							'unit_id_manufactures' => $value['unit_id_manufactures'],
							'quantity_use' => number_unformat($value['quantity_use'], false),
							'note' => $value['note'],
							'arrDetail' => $arrDetail,
						];
					}
				}

				// print_arrays($this->input->post());
				// if (!empty($items_id)) {
				// 	$index = 0;
				// 	foreach ($items_id as $key => $value) {
				// 		$arrs = explode('__', $value);
				// 		$item_id = $arrs[0];
				// 		$type_item = $arrs[1];
				// 		$quantity = number_unformat($this->input->post('quantity')[$key]);
				// 		$warehouses = $this->input->post('warehouses')[$key];
				// 		// if (empty($warehouses)) {
				// 		// 	$data['result'] = 0;
				// 		// 	$data['message'] = lang('Vui lòng chọn kho mặt hàng');
				// 		// 	echo json_encode($data);
				// 		// 	die;
				// 		// }

				// 		$note_items = $this->input->post('note_items')[$key];
				// 		$warehouses = explode('__', $warehouses);
				// 		$warehouse_ids = WAREHOUSES_CAPACITY; //de a hoang gan
				// 		$location_ids = LOCATIONS_DEFAULT_MANUFACTURES; //de a hoang gan
				// 		$item_id_bom = !empty($this->input->post('item_id_bom')[$key]) ? $this->input->post('item_id_bom')[$key] : '';
				// 		$arrBOM = [];
				// 		$totalBOM = 0;
                //         $quantity = 0;
				// 		if (!empty($item_id_bom)) {
				// 			foreach ($item_id_bom as $k => $val) {
				// 				$arrs1 = explode('__', $val);
				// 				$item_id1 = $arrs1[0];
				// 				$type_item1 = $arrs1[1];
				// 				$warehouses_items = $this->input->post('warehouses_items')[$key][$k];
				// 				$quantity_bom = number_unformat($this->input->post('quantity_bom')[$key][$k]);
				// 				if (empty($warehouses_items)) {
				// 					$data['result'] = 0;
				// 					$data['message'] = lang('Vui lòng chọn kho mặt hàng BOM');
				// 					echo json_encode($data);
				// 					die;
				// 				}

                //                 $quantity_multiples = number_unformat($this->input->post('quantity_multiples')[$key][$k]);
                //                 $total_quantity_item = $quantity_bom * $quantity_multiples;
                //                 $quantity+= $total_quantity_item;
				// 				// $warehouses = explode('__', $warehouses_items);
				// 				$warehouses_id = get_table_where('tblwarehouse_items', array('id' => $warehouses_items), '', 'row');
				// 				$warehouse_id = $warehouses_id->warehouse_id;
				// 				$location_id = $warehouses_id->localtion;
				// 				// $dtWProduct = $this->manufacture_model->getWarehouseProductById($warehouses_items);
				// 				$warehouse_item_id = $warehouse_id;
				// 				$location_item_id = $location_id;
				// 				$quantity_stock =  $quantity_bom;
				// 				if ($type_item1 == 'materials') {
				// 					$data_items = get_items($item_id1, 'nvl');
				// 					$recipe = $data_items->recipe;
				// 					$paper = $data_items->paper;
				// 					$longs = $data_items->longs;
				// 					$wide = $data_items->wide;
				// 					$exchange_unit = $data_items->exchange_unit;    //chuan
				// 					$exchange_standard_unit = $data_items->exchange_standard_unit; //kho
				// 					$exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
				// 					$quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
				// 					if ($recipe == 1) {
				// 						$quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_standard_unit;
				// 					} elseif ($recipe == 2) {
				// 						$quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
				// 					} elseif ($recipe == 3) {
				// 						$quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
				// 					}
				// 				} else {
				// 					$recipe = 1;
				// 					$paper = 1;
				// 					$longs = 1;
				// 					$wide = 1;
				// 					$exchange_unit = 1;    //chuan
				// 					$exchange_standard_unit = 1; //kho
				// 					$exchange_unit_payment = 1; //thanh toan
				// 					$quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
				// 					$quantity_payment = ($quantity_unit / $exchange_unit_payment) / $exchange_standard_unit;
				// 				}
				// 				$info_items = array();
				// 				$info_items['recipe'] = $recipe;
				// 				$info_items['paper'] = $paper;
				// 				$info_items['longs'] = $longs;
				// 				$info_items['wide'] = $wide;
				// 				$info_items_text = json_encode($info_items);
				// 				$arrBOM[] = [
				// 					'item_id' => $item_id1,
				// 					'type_items' => $type_item1,
				// 					'warehouse_item_id' => $warehouse_item_id,
				// 					'location_item_id' => $location_item_id,
				// 					'quantity_item' => $quantity_bom,
				// 					'warehouse_product_id' => $warehouses_items,
				// 					'quantity_stock' => $quantity_stock,
				// 					'quantity_unit' => $quantity_unit,
				// 					'quantity_payment' => $quantity_payment,
				// 					'info_items' => $info_items_text,
				// 					'lot_code' => $warehouses_id->lot_code,
				// 					'date_sx' => $warehouses_id->date_sx,
				// 					'date_sd' => $warehouses_id->date_sd,
				// 					'date_use' => $warehouses_id->date_use,
				// 					'quantity_multiples' => $quantity_multiples,
				// 					'total_quantity_item' => $total_quantity_item,
				// 				];
				// 				$totalBOM += $quantity_bom;
				// 			}
				// 		} else {
                //             $data['result'] = 0;
				// 			$data['message'] = lang('Vui lòng chọn NPL để xã khổ');
				// 			echo json_encode($data);
				// 			die;
                //         }

				// 		if ($quantity < 1) {
				// 			$data['result'] = 0;
				// 			$data['message'] = lang('Vui lòng kiểm tra số lượng nhập');
				// 			echo json_encode($data);
				// 			die;
				// 		}

				// 		$quantity_stock_main =  $quantity;
				// 		if ($type_item == 'materials') {
				// 			$data_items = get_items($item_id, 'nvl');
				// 			$recipe_main = $data_items->recipe;
				// 			$paper_main = $data_items->paper;
				// 			$longs_main = $data_items->longs;
				// 			$wide_main = $data_items->wide;
				// 			$exchange_unit_main = $data_items->exchange_unit;    //chuan
				// 			$exchange_standard_unit_main = $data_items->exchange_standard_unit; //kho
				// 			$exchange_unit_payment_main = $data_items->exchange_unit_payment; //thanh toan
				// 			$quantity_unit_main = ($quantity_stock_main * $exchange_standard_unit_main) / $exchange_unit;
				// 			if ($recipe == 1) {
				// 				$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $exchange_standard_unit_main;
				// 			} elseif ($recipe == 2) {
				// 				$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $paper_main / 100;
				// 			} elseif ($recipe == 3) {
				// 				$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * ($longs_main  * $wide_main) / 10000;
				// 			}
				// 		} else {
				// 			$recipe_main = 1;
				// 			$paper_main = 1;
				// 			$longs_main = 1;
				// 			$wide_main = 1;
				// 			$exchange_unit_main = 1;    //chuan
				// 			$exchange_standard_unit_main = 1; //kho
				// 			$exchange_unit_payment_main = 1; //thanh toan
				// 			$quantity_unit_main = ($quantity_stock_main * $exchange_standard_unit_main) / $exchange_unit_main;
				// 			$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) / $exchange_standard_unit_main;
				// 		}
				// 		$info_items_main = array();
				// 		$info_items_main['recipe'] = $recipe_main;
				// 		$info_items_main['paper'] = $paper_main;
				// 		$info_items_main['longs'] = $longs_main;
				// 		$info_items_main['wide'] = $wide_main;
				// 		$info_items_text_main = json_encode($info_items_main);

				// 		$items[$index] = [
				// 			'item_id' => $item_id,
				// 			'type_items' => $type_item,
				// 			'warehouse_id' => $warehouse_ids,
				// 			'location_id' => $location_ids,
				// 			'quantity' => $quantity,
				// 			'note_item' => $note_items,
				// 			'arrBOM' => $arrBOM,
				// 			'quantity_stock' => $quantity_stock_main,
				// 			'quantity_unit' => $quantity_unit_main,
				// 			'quantity_payment' => $quantity_payment_main,
				// 			'info_items' => $info_items_text_main,
				// 		];
				// 		$count_items++;
				// 		$total_quantity += $quantity;
				// 		$index++;
				// 	}
				// }

				// if (!empty($items_id)) {
				// 	$index = 0;
				// 	foreach ($items_id as $key => $value) {
				// 		$arrs = explode('__', $value);
				// 		$item_id = $arrs[0];
				// 		$type_item = $arrs[1];

				// 		$info = $this->items_model->rowMaterial($item_id);
				// 		$note_items = $this->input->post('note_items')[$key];

				// 		$dtPOIS = $this->manufacture_model->getProductionsOrdersItemsSub($id_production_detail, $type_item, $item_id);

				// 		$height = $info['height'];
				// 		$quantity = $dtPOIS['quantity'];
				// 		$quantity_compensation = $dtPOIS['quantity_compensation'] + $dtPOIS['quantity_compensation_sm'];
				// 		$total_height = $height * ($quantity_compensation + $quantity);
				// 		$quantity_need = roundNumberFormat($quantity_compensation + $quantity, 0);
				// 		$quantity_single = $dtPOIS['quantity_single'];
				// 		$number_paper = $quantity_need * $quantity_single;
				// 		$paper_exchange = roundNumberFormat($quantity_need/$quantity_single, 0);

				// 		$items[] = [
				// 			'item_id' => $item_id,
				// 			'type_items' => $type_item,
				// 			'quantity' => $quantity,
				// 			'height' => $height,
				// 			'total_height' => $total_height,

				// 			'unit_id' => $dtPOIS['unit_id'],
				// 			'quantity_single' => $dtPOIS['quantity_single'],
				// 			'unit_parent_id' => $dtPOIS['unit_parent_id'],
				// 			'quantity_exchange' => $dtPOIS['quantity_exchange'],
				// 			'quantity_primary' => $dtPOIS['quantity_primary'],
				// 			'stage_item_id' => $dtPOIS['stage_item_id'],
				// 			'quantity_order' => $dtPOIS['quantity_order'],
				// 			'quantity_compensation' => $dtPOIS['quantity_compensation'],
				// 			'type_element_item' => $dtPOIS['type_element_item'],
				// 			'quantity_compensation_primary' => $dtPOIS['quantity_compensation_primary'],
				// 			'quantity_element' => $dtPOIS['quantity_element'],
				// 			'quantity_compensation_sm' => $dtPOIS['quantity_compensation_sm'],
				// 			'quantity_compensation_sm_primary' => $dtPOIS['quantity_compensation_sm_primary'],
				// 			'quantity_cs' => $dtPOIS['quantity_cs'],
				// 			'landscape_print_size' => $dtPOIS['landscape_print_size'],
				// 			'vertical_print_size' => $dtPOIS['vertical_print_size'],
				// 			'number_children_size' => $dtPOIS['number_children_size'],
				// 			'paper_exchange' => $paper_exchange,
				// 			'hand_input_paper_exchange' => $dtPOIS['hand_input_paper_exchange'],
				// 			'number_paper' => $number_paper,
				// 		];

				// 		$count_items++;
				// 		$total_quantity += $quantity;
				// 		$index++;
				// 	}
				// }

				// print_arrays($arrItems);
				if (empty($arrItems)) {
					$data['result'] = 0;
					$data['message'] = lang('tnh_not_items');
					echo json_encode($data);
					die;
				}

				$dtPO = $this->manufactures_model->rowProductionsOrdersById($po_id);

				$options = [
					'date' => $date,
					'reference_no' => $reference_no,
					'count_items' => $count_items,
					'total_quantity' => $total_quantity,
					'status' => 1,
					'created_by' => get_staff_user_id(),
					'date_created' => date('Y-m-d H:i:s'),
					'note' => $note,
					'id_production_detail' => $po_id,
					'id_branch' => $dtPO['location_id'],
				];

				$manufactures_id = $this->manufacture_model->insertManufactures($options);
				if ($manufactures_id) {
					updateReference('manufacture');
					$arrManufacturesItemsBOM = [];

					foreach ($arrItems as $key => $value) {
						$value['manufactures_id'] = $manufactures_id;
						$arrDetail = $value['arrDetail'];
						unset($value['arrDetail']);
						$manufactures_materials_id = $this->manufacture_model->insertManufacturesMaterials($value);
						if ($manufactures_materials_id) {
							foreach ($arrDetail as $k => $val) {
								$val['manufactures_id'] = $manufactures_id;
								$val['manufactures_materials_id'] = $manufactures_materials_id;
								$this->manufacture_model->insertManufacturesMaterialsDetail($val);
							}
						}
					}

					// foreach ($items as $key => $value) {
					// 	// $arrBOM = $value['arrBOM'];
					// 	$value['manufactures_id'] = $manufactures_id;
					// 	// unset($value['arrBOM']);
					// 	// $manufactures_items_id = $this->manufacture_model->insertManufacturesItems($value);
					// 	// if (!empty($manufactures_items_id)) {
					// 	// 	if (!empty($arrBOM)) {
					// 	// 		foreach ($arrBOM as $k => $v) {
					// 	// 			$v['manufactures_id'] = $manufactures_id;
					// 	// 			$v['manufactures_items_id'] = $manufactures_items_id;
					// 	// 			$arrManufacturesItemsBOM[] = $v;
					// 	// 		}
					// 	// 	}
					// 	// }
					// }
					// if (!empty($arrManufacturesItemsBOM)) {
					// 	$this->manufacture_model->insertManufacturesItemsBOMBatch($arrManufacturesItemsBOM);
					// }
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
			die;
		} else {
			$data['title'] = lang('add_manufacture');
			$data['breadcrumb'] = [
				array(
					'link' => base_url('admin/manufacture/index'),
					'page' => lang('manufacture'),
				),
				array('link' => '#', 'page' => lang('add_manufacture')),
			];

			// $this->db->select('
			// 	tbl_productions_orders_details.*,
			// 	tbl_products.code as item_code,
			// 	tbl_products.name as item_name,
			// 	CONCAT(tbl_products.name, "(", tbl_products.code,")", "(SL sản phẩm: ", tbl_productions_orders_items.quantity,")") as name_product,
			// 	tbl_productions_orders_items.quantity as quantity
			// ', false);
			// $this->db->from('tbl_productions_orders_details');
			// $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
			// $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
			// $data['productions_detail'] = $this->db->get()->result_array();
			$data['po_id_link'] = $po_id_link;
			$data['productions_detail'] = [];
			$this->load->view('admin/manufacture/add_1', $data);
		}
	}


	public function rowItems()
	{
		$item_id = $this->input->post('item_id');
		$type = $this->input->post('type');
		$data = [];
		$item = null;
		if (!empty($item_id)) {
			$item_id = str_replace('__items', '', $item_id);
			$this->db->select('*');
			$this->db->from('tblitems');
			$this->db->where('tblitems.id', $item_id);
			$item = $this->db->get()->row_array();
			if (!empty($item)) {
				$images = '';
				if (!empty($item['avatar'])) {
					$images = ($item['avatar']);
				}
				$item['images'] = $images;
				$type_item = "items";
				$warehouses = $this->site_model->getWarehouse();
				$option = '';
				$option = '<option value=""></option>';
				foreach ($warehouses as $key => $value) {
					if ($type != 'materials') {
						$option .= '<optgroup label="' . $value['name'] . '">';
						$this->db->select('
                            tbllocaltion_warehouses.*
                        ', false);
						$this->db->from('tbllocaltion_warehouses');
						$this->db->where('tbllocaltion_warehouses.warehouse', $value['id']);
						$location_warehouses = $this->db->get()->result_array();
						if (!empty($location_warehouses)) {
							foreach ($location_warehouses as $k => $val) {
								$option .= '<option value="' . $value['id'] . '__' . $val['id'] . '">' . $val['name'] . '</option>';
							}
						}
						$option .= '</optgroup>';
					} else {
						$quantity_warehouse = $this->site_model->getTotalQuantityWarehouses($value['id'], $item_id, $type_item)['total_quantity'];
						if ($quantity_warehouse > 0) {
							$option .= '<optgroup label="' . $value['name'] . '">';
							$quantity_warehouse_detail = $this->site_model->getTotalQuantityWarehousesDetail($value['id'], $item_id, $type_item);
							foreach ($quantity_warehouse_detail as $k => $v) {
								$option .= '<option data-quantity="' . $v['quantity_left'] . '" value="' . $v['id'] . '">' . $v['name'] . ' (' . formatNumber($v['price']) . ') (' . formatNumber($v['quantity_left']) . ') - ' . _dt($v['date_warehouse']) . '</option>';
							}
							$option .= '</optgroup>';
						}
					}
				}
				$item['option_warehouses'] = $option;
			}
			$data['item'] = $item;
		}
		echo json_encode($data);
	}

	public function getManufactures()
	{
		if (!$this->perView && !$this->perViewOwn) {
			accessDenied($js = true);
		}
		$staff_id = get_staff_user_id();
		$this->db->simple_query('SET SESSION group_concat_max_len=15000000');
		$start_date_search = $this->input->post('start_date_search');
		$end_date_search = $this->input->post('end_date_search');

		$branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

		$tbProductItem = "(
			SELECT
				tbl_manufactures_materials_detail.manufactures_id as manufactures_id,
				GROUP_CONCAT(distinct tbl_products.code SEPARATOR '</br>') as product_code,
				GROUP_CONCAT(distinct tbl_products.name SEPARATOR '</br>') as product_name
			FROM tbl_manufactures_materials_detail
			INNER JOIN tbl_products ON tbl_products.id = tbl_manufactures_materials_detail.product_id
			GROUP BY tbl_manufactures_materials_detail.manufactures_id
		) tb_product_item";

		$aColumns = [
			'tbl_manufactures.id as id',
			'tbl_manufactures.date as date',
			'tbl_manufactures.reference_no as reference_no',
			'tbl_productions_orders.reference_no as reference_no_manufactures',
			'tb_product_item.product_code as item_code',
			'tb_product_item.product_name as item_name',
			'tbl_manufactures.total_quantity as total_quantity',
			'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
			'tbl_manufactures.note as note',
			'"" as actions',
		];
		$sIndexColumn = 'id';
		$sTable = 'tbl_manufactures';
		$where = [];
		$filter = [];
		$join = [
			'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_manufactures.created_by',
			'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_manufactures.id_production_detail',
			'LEFT JOIN '.$tbProductItem.' ON tb_product_item.manufactures_id = tbl_manufactures.id',
			'LEFT JOIN tblbranch ON tblbranch.id = tbl_manufactures.id_branch',
			// 'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
			// 'INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id',
		];

		if (!$this->perView) {
			array_push($where, "AND tbl_manufactures.created_by = '" . get_staff_user_id() . "'");
		}

		if (!empty($start_date_search)) {
			$start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
			array_push($where, "AND tbl_manufactures.date >= '" . $start_date_search . "'");
		}

		if (!empty($end_date_search)) {
			$end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
			array_push($where, "AND tbl_manufactures.date <= '" . $end_date_search . "'");
		}

		if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            array_push($where, 'AND tbl_manufactures.id_branch IN ('.implode(',', $branch_staff).')');
        }

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			'tblbranch.name as name_branch'
		], '', []);
		$output = $result['output'];
		$rResult = $result['rResult'];
		$start = $this->input->post('start');
		$total_quantity = 0;
		foreach ($rResult as $key => $aRow) {
			$test_quantity = get_table_where('tblwarehouse_product', array('import_id' => $aRow['id'], 'quantity_export >' => 0, 'type_export ' => 338), '', 'row');
			$start++;
			$manufactures_id = $aRow['id'];
			$row = [];
			$row[0] = '<div class="text-center">
                <div class="checkbox" style="padding-left: 8px;">
                    <input type="checkbox" name="order_id[]" id="check-item' . $manufactures_id . '" value="' . $manufactures_id . '"><label for="check-item' . $manufactures_id . '"></label>
                </div>
            </div>';
			$row[1] = _d($aRow['date']);
			$link = '<div style="min-width: 100px;" class="">
                <a data-tnh="modal" class="tnh-modal" href="' . base_url() . 'admin/manufacture/view/' . $manufactures_id . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a>
                </div>
            ';

			$link = '<div class="text-primary">'.$aRow['reference_no'].'</div>';
			$row[2] = $link.'<div class="italic">'.$aRow['name_branch'].'</div>';
			$row[3] = $aRow['reference_no_manufactures'];
			$row[4] = $aRow['item_code'];
			$row[5] = $aRow['item_name'];

			$row[6] = '<div class="text-center">' . formatNumber($aRow['total_quantity']) . '</div>';
			$row[7] = '<div class="text-center">' . $aRow['created_by'] . '</div>';

			$row[8] = ($aRow['note']);
			$view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/manufacture/view/' . $manufactures_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view_manufactures') . '</a>';
			$edit = $this->perEdit ? '<a href="' . base_url('admin/manufacture/edit/' . $manufactures_id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_manufactures') . ' </a>' : '';

			// $print = '<a target="_blank" href="' . base_url('admin/manufacture/print/' . $manufactures_id) . '"><i class="fa fa-print"></i> ' . lang('tnh_print_manufactures') . ' </a>';

			$print = '<a onclick="printPdfManu('.$manufactures_id.')" href="javascript:void(0)"><i class="fa fa-print"></i> ' . lang('tnh_print_manufactures') . ' </a>';

			$delete = $this->perDelete ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/manufacture/delete/' . $manufactures_id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_manufactures') . '</a>' : '';

			$actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $print . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
			$row[9] = $actions;
			$output['aaData'][] = $row;

			$total_quantity+= $aRow['total_quantity'];
		}
		$output['total_quantity'] = $total_quantity;
		echo json_encode($output);
	}

	public function edit($id)
	{
		if (!$this->perEdit) {
			accessDenied($js = true);
		}
		$manufactures = $this->manufacture_model->getManufactures($id);
		if ($manufactures['warehouseman_id'] > 0) {
			set_alert('danger', lang('tnh_export_warehouse_not_edit'));
			redirect($_SERVER["HTTP_REFERER"]);
			die;
		}
		if ($this->input->post('add')) {
			$this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required');
			$this->form_validation->set_rules('date', lang("date"), 'required');
			if ($this->form_validation->run() == true) {
				// $reference_no = getReference('manufactures');
				$date = to_sql_date($this->input->post('date'), true);
				$note = $this->input->post('note', false);
				$id_production_detail = $this->input->post('id_production_detail');
				$count_items = 0;
				$total_quantity = 0;
				$items = [];
				$items_id = $this->input->post('items_id');
				if (empty($id_production_detail)) {
					$data['result'] = 0;
					$data['message'] = lang('Vui lòng nhập phiếu lệnh sản xuất chi tiết');
					echo json_encode($data);
					die;
				}

				// if (!empty($items_id)) {
				// 	$index = 0;
				// 	foreach ($items_id as $key => $value) {
				// 		$arrs = explode('__', $value);
				// 		$item_id = $arrs[0];
				// 		$type_item = $arrs[1];
				// 		$quantity = number_unformat($this->input->post('quantity')[$key]);
				// 		$warehouses = $this->input->post('warehouses')[$key];
				// 		// if (empty($warehouses)) {
				// 		// 	$data['result'] = 0;
				// 		// 	$data['message'] = lang('Vui lòng chọn kho mặt hàng');
				// 		// 	echo json_encode($data);
				// 		// 	die;
				// 		// }
				// 		$note_items = $this->input->post('note_items')[$key];
				// 		$warehouses = explode('__', $warehouses);
				// 		$warehouse_ids = WAREHOUSES_CAPACITY; //de a hoang gan
				// 		$location_ids = LOCATIONS_DEFAULT_MANUFACTURES; //de a hoang gan
				// 		$item_id_bom = !empty($this->input->post('item_id_bom')[$key]) ? $this->input->post('item_id_bom')[$key] : '';
				// 		$arrBOM = [];
				// 		$totalBOM = 0;
				// 		$quantity = 0;
				// 		if (!empty($item_id_bom)) {
				// 			foreach ($item_id_bom as $k => $val) {
				// 				$arrs1 = explode('__', $val);
				// 				$item_id1 = $arrs1[0];
				// 				$type_item1 = $arrs1[1];
				// 				$warehouses_items = $this->input->post('warehouses_items')[$key][$k];
				// 				$quantity_bom = number_unformat($this->input->post('quantity_bom')[$key][$k]);
				// 				if (empty($warehouses_items)) {
				// 					$data['result'] = 0;
				// 					$data['message'] = lang('Vui lòng chọn kho mặt hàng BOM');
				// 					echo json_encode($data);
				// 					die;
				// 				}

				// 				$quantity_multiples = number_unformat($this->input->post('quantity_multiples')[$key][$k]);
                //                 $total_quantity_item = $quantity_bom * $quantity_multiples;
				// 				$quantity+= $total_quantity_item;
				// 				// $warehouses = explode('__', $warehouses_items);
				// 				// $warehouse_id = $warehouses[0];
				// 				// $location_id = $warehouses[1];
				// 				$warehouses_id = get_table_where('tblwarehouse_items', array('id' => $warehouses_items), '', 'row');
				// 				$warehouse_id = $warehouses_id->warehouse_id;
				// 				$location_id = $warehouses_id->localtion;
				// 				$warehouse_item_id = $warehouse_id;
				// 				$location_item_id = $location_id;
				// 				$quantity_stock =  $quantity_bom;
				// 				if ($type_item1 == 'materials') {
				// 					$data_items = get_items($item_id1, 'nvl');
				// 					$recipe = $data_items->recipe;
				// 					$paper = $data_items->paper;
				// 					$longs = $data_items->longs;
				// 					$wide = $data_items->wide;
				// 					$exchange_unit = $data_items->exchange_unit;    //chuan
				// 					$exchange_standard_unit = $data_items->exchange_standard_unit; //kho
				// 					$exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
				// 					$quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
				// 					if ($recipe == 1) {
				// 						$quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_standard_unit;
				// 					} elseif ($recipe == 2) {
				// 						$quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
				// 					} elseif ($recipe == 3) {
				// 						$quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
				// 					}
				// 				} else {
				// 					$recipe = 1;
				// 					$paper = 1;
				// 					$longs = 1;
				// 					$wide = 1;
				// 					$exchange_unit = 1;    //chuan
				// 					$exchange_standard_unit = 1; //kho
				// 					$exchange_unit_payment = 1; //thanh toan
				// 					$quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
				// 					$quantity_payment = ($quantity_unit / $exchange_unit_payment) / $exchange_standard_unit;
				// 				}
				// 				$info_items = array();
				// 				$info_items['recipe'] = $recipe;
				// 				$info_items['paper'] = $paper;
				// 				$info_items['longs'] = $longs;
				// 				$info_items['wide'] = $wide;
				// 				$info_items_text = json_encode($info_items);
				// 				$arrBOM[] = [
				// 					'id' => !empty($this->input->post('manufactures_item_bom_id')[$key][$k]) ? $this->input->post('manufactures_item_bom_id')[$key][$k] : 0,
				// 					'item_id' => $item_id1,
				// 					'type_items' => $type_item1,
				// 					'warehouse_item_id' => $warehouse_item_id,
				// 					'location_item_id' => $location_item_id,
				// 					'quantity_item' => $quantity_bom,
				// 					'warehouse_product_id' => $warehouses_items,
				// 					'quantity_stock' => $quantity_stock,
				// 					'quantity_unit' => $quantity_unit,
				// 					'quantity_payment' => $quantity_payment,
				// 					'info_items' => $info_items_text,
				// 					'lot_code' => $warehouses_id->lot_code,
				// 					'date_sx' => $warehouses_id->date_sx,
				// 					'date_sd' => $warehouses_id->date_sd,
				// 					'date_use' => $warehouses_id->date_use,
				// 					'quantity_multiples' => $quantity_multiples,
				// 					'total_quantity_item' => $total_quantity_item,
				// 				];
				// 				$totalBOM += $quantity_bom;
				// 			}
				// 		}
				// 		if ($quantity < 1) {
				// 			$data['result'] = 0;
				// 			$data['message'] = lang('Vui lòng kiểm tra số lượng nhập');
				// 			echo json_encode($data);
				// 			die;
				// 		}
				// 		$quantity_stock_main =  $quantity;
				// 		if ($type_item == 'materials') {
				// 			$data_items = get_items($item_id, 'nvl');
				// 			$recipe_main = $data_items->recipe;
				// 			$paper_main = $data_items->paper;
				// 			$longs_main = $data_items->longs;
				// 			$wide_main = $data_items->wide;
				// 			$exchange_unit_main = $data_items->exchange_unit;    //chuan
				// 			$exchange_standard_unit_main = $data_items->exchange_standard_unit; //kho
				// 			$exchange_unit_payment_main = $data_items->exchange_unit_payment; //thanh toan
				// 			$quantity_unit_main = ($quantity_stock_main * $exchange_standard_unit) / $exchange_unit;
				// 			if ($recipe == 1) {
				// 				$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $exchange_standard_unit_main;
				// 			} elseif ($recipe == 2) {
				// 				$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $paper_main / 100;
				// 			} elseif ($recipe == 3) {
				// 				$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * ($longs_main  * $wide_main) / 10000;
				// 			}
				// 		} else {
				// 			$recipe_main = 1;
				// 			$paper_main = 1;
				// 			$longs_main = 1;
				// 			$wide_main = 1;
				// 			$exchange_unit_main = 1;    //chuan
				// 			$exchange_standard_unit_main = 1; //kho
				// 			$exchange_unit_payment_main = 1; //thanh toan
				// 			$quantity_unit_main = ($quantity_stock_main * $exchange_standard_unit_main) / $exchange_unit_main;
				// 			$quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) / $exchange_standard_unit_main;
				// 		}
				// 		$info_items_main = array();
				// 		$info_items_main['recipe'] = $recipe_main;
				// 		$info_items_main['paper'] = $paper_main;
				// 		$info_items_main['longs'] = $longs_main;
				// 		$info_items_main['wide'] = $wide_main;
				// 		$info_items_text_main = json_encode($info_items_main);

				// 		$items[$index] = [
				// 			'id' => !empty($this->input->post('manufactures_item_id')[$key]) ? $this->input->post('manufactures_item_id')[$key] : 0,
				// 			'item_id' => $item_id,
				// 			'type_items' => $type_item,
				// 			'warehouse_id' => $warehouse_ids,
				// 			'location_id' => $location_ids,
				// 			'quantity' => $quantity,
				// 			'note_item' => $note_items,
				// 			'arrBOM' => $arrBOM,
				// 			'quantity_stock' => $quantity_stock_main,
				// 			'quantity_unit' => $quantity_unit_main,
				// 			'quantity_payment' => $quantity_payment_main,
				// 			'info_items' => $info_items_text_main
				// 		];
				// 		$count_items++;
				// 		$total_quantity += $quantity;
				// 		$index++;
				// 	}
				// }

				if (!empty($items_id)) {
					$index = 0;
					foreach ($items_id as $key => $value) {
						$arrs = explode('__', $value);
						$item_id = $arrs[0];
						$type_item = $arrs[1];

						$info = $this->items_model->rowMaterial($item_id);
						$note_items = $this->input->post('note_items')[$key];

						$dtPOIS = $this->manufacture_model->getProductionsOrdersItemsSub($id_production_detail, $type_item, $item_id);

						$height = $info['height'];
						$quantity = $dtPOIS['quantity'];
						$quantity_compensation = $dtPOIS['quantity_compensation'] + $dtPOIS['quantity_compensation_sm'];
						$total_height = $height * ($quantity_compensation + $quantity);
						$quantity_need = roundNumberFormat($quantity_compensation + $quantity, 0);
						$quantity_single = $dtPOIS['quantity_single'];
						$number_paper = $quantity_need * $quantity_single;
						$paper_exchange = roundNumberFormat($quantity_need/$quantity_single, 0);

						$items[] = [
							'id' => !empty($this->input->post('manufacture_item_id')[$key]) ? $this->input->post('manufacture_item_id')[$key] : 0,
							'item_id' => $item_id,
							'type_items' => $type_item,
							'quantity' => $quantity,
							'height' => $height,
							'total_height' => $total_height,

							'unit_id' => $dtPOIS['unit_id'],
							'quantity_single' => $dtPOIS['quantity_single'],
							'unit_parent_id' => $dtPOIS['unit_parent_id'],
							'quantity_exchange' => $dtPOIS['quantity_exchange'],
							'quantity_primary' => $dtPOIS['quantity_primary'],
							'stage_item_id' => $dtPOIS['stage_item_id'],
							'quantity_order' => $dtPOIS['quantity_order'],
							'quantity_compensation' => $dtPOIS['quantity_compensation'],
							'type_element_item' => $dtPOIS['type_element_item'],
							'quantity_compensation_primary' => $dtPOIS['quantity_compensation_primary'],
							'quantity_element' => $dtPOIS['quantity_element'],
							'quantity_compensation_sm' => $dtPOIS['quantity_compensation_sm'],
							'quantity_compensation_sm_primary' => $dtPOIS['quantity_compensation_sm_primary'],
							'quantity_cs' => $dtPOIS['quantity_cs'],
							'landscape_print_size' => $dtPOIS['landscape_print_size'],
							'vertical_print_size' => $dtPOIS['vertical_print_size'],
							'number_children_size' => $dtPOIS['number_children_size'],
							// 'paper_exchange' => $dtPOIS['paper_exchange'],
							'paper_exchange' => $paper_exchange,
							'hand_input_paper_exchange' => $dtPOIS['hand_input_paper_exchange'],
							'number_paper' => $number_paper
						];

						$count_items++;
						$total_quantity += $quantity;
						$index++;
					}
				}

				if (empty($items)) {
					$data['result'] = 0;
					$data['message'] = lang('tnh_not_items');
					echo json_encode($data);
					die;
				}
				$options = [
					'date' => $date,
					'count_items' => $count_items,
					'total_quantity' => $total_quantity,
					'updated_by' => get_staff_user_id(),
					'date_updated' => date('Y-m-d H:i:s'),
					'note' => $note,
					'id_production_detail' => $id_production_detail
				];
				$up = $this->manufacture_model->updateManufactures($id, $options);
				if ($up) {
					$manufactures_id = $id;
					$this->manufacture_model->deleteManufacturesItems($id);
					$this->manufacture_model->deleteManufacturesItemsBOM($id);
					$arrManufacturesItemsBOM = [];
					foreach ($items as $key => $value) {
						// $arrBOM = $value['arrBOM'];
						$value['manufactures_id'] = $manufactures_id;
						// unset($value['arrBOM']);
						$manufactures_items_id = $this->manufacture_model->insertManufacturesItems($value);
						// if (!empty($manufactures_items_id)) {
						// 	if (!empty($arrBOM)) {
						// 		foreach ($arrBOM as $k => $v) {
						// 			$v['manufactures_id'] = $manufactures_id;
						// 			$v['manufactures_items_id'] = $manufactures_items_id;
						// 			$arrManufacturesItemsBOM[] = $v;
						// 		}
						// 	}
						// }
					}
					if (!empty($arrManufacturesItemsBOM)) {
						$this->manufacture_model->insertManufacturesItemsBOMBatch($arrManufacturesItemsBOM);
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
			die;
		} else {
			$data['manufactures'] = $manufactures;
			$data['id'] = $id;
			$data['title'] = lang('tnh_edit_manufactures');
			$data['breadcrumb'] = [
				array(
					'link' => base_url('admin/manufacture/index'),
					'page' => lang('manufacture'),
				),
				array('link' => '#', 'page' => lang('tnh_edit_manufactures')),
			];

			$this->db->select('
				tbl_productions_orders_details.*,
				tbl_products.code as item_code,
				tbl_products.name as item_name,
				CONCAT(tbl_products.name, "(", tbl_products.code,")", "(SL sản phẩm: ", tbl_productions_orders_items.quantity,")") as name_product
			', false);
			$this->db->from('tbl_productions_orders_details');
			$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
			$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
			$data['productions_detail'] = $this->db->get()->result_array();
			$this->load->view('admin/manufacture/edit', $data);
		}
	}

	public function view($id)
	{
		// if (!$this->perViewOrders && !$this->perViewOwnOrders) {
		//     accessDenied($js = true);
		// }
		$data = [];
		$manufactures = $this->manufacture_model->getManufactures($id);
		$data['manufactures'] = $manufactures;
		$data['pod'] = $this->manufacture_model->getProductionsOrdersDetail($manufactures['id_production_detail']);
		$data['id'] = $id;
		$this->load->view('admin/manufacture/view', $data);
	}

	public function delete($id)
	{
		if (!$this->perDelete) {
			$data['result'] = 0;
			$data['message'] = lang('access_denied');
			echo json_encode($data);
			die;
		}
		$data = [];
		// if (!$this->perDeleteOrders) {
		//     $data['result'] = 0;
		//     $data['message'] = lang('access_denied');
		//     echo json_encode($data);
		//     die;
		// }
		if ($id) {
			$manufactures = $this->manufacture_model->getManufactures($id);
			if ($manufactures['warehouseman_id'] > 0) {
				$data['result'] = 0;
				$data['message'] = lang('tnh_export_warehouse_not_delete');
				echo json_encode($data);
				die;
			}
			if ($this->manufacture_model->deleteManufactures($id)) {
				$this->manufacture_model->deleteManufacturesItems($id);
				$this->manufacture_model->deleteManufacturesItemsBOM($id);
				$this->manufacture_model->deleteManufacturesMaterials($id);
				$this->manufacture_model->deleteManufacturesMaterialsDetail($id);


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

	public function confirm_warehous()
	{
		die;
		if (!has_permission('manufactures', '', 'approve_warehouse')) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('ch_approve_not'),
			));
			die;
		}
		$id = $this->input->post('id');
		$manufactures = get_table_where('tbl_manufactures', array('id' => $id), '', 'row');

		if (!empty($manufactures->status_manufactures)) {
			echo json_encode(array(
				'alert_type' => 'warning',
				'message' => _l('Đã duyệt chuyển xã qua LSX chi tiết không thể bỏ duyệt kho'),
			));
			die;
		}

		$warehouseman_id = $this->input->post('warehouseman_id');
		if (!$id) {
			die('ch_no_items');
		}
		$data = array(
			'warehouseman_id' => null,
			'warehouseman_date' => null,
		);
		if (empty($warehouseman_id)) {
			if (!empty($manufactures->warehouseman_id)) {
				echo json_encode(array(
					'alert_type' => 'warning',
					'message' => _l('ch_exsit_confirm_warehouse'),
				));
				die;
			}
			// if (!$this->kt_quantity_manufacture($id)) {
			// 	$html = $this->get_warehouse_manufacture($id);
			// 	echo json_encode(array(
			// 		'html' => $html,
			// 		'response' => 0,
			// 		'alert_type' => 'danger',
			// 		'message' => 'Số lượng trong kho không đủ để bỏ duyệt'
			// 	));
			// 	die;
			// }
			//			$manufactures_items = get_table_where('tbl_manufactures_items', array('manufactures_id' => $id));
			$data = array(
				'warehouseman_id' => get_staff_user_id(),
				'warehouseman_date' => date('Y-m-d H:i:s'),
			);
		} else {
			$kt_quantity = get_table_where('tblwarehouse_product', array('import_id' => $id, 'quantity_export >' => 0, 'type_export' => 338), '', 'row');
			if (!empty($kt_quantity)) {
				echo json_encode(array(
					'alert_type' => 'warning',
					'message' => _l('ch_cance_confirm_export_warehouse')
				));
				die;
			}
			if (empty($manufactures->warehouseman_id)) {
				echo json_encode(array(
					'alert_type' => 'warning',
					'message' => _l('ch_exsit_cancel_confirm_warehouse'),
				));
				die;
			}
		}
		$success = $this->db->update('tbl_manufactures', $data, array('id' => $id));
		// $success = true;
		$alert_type = 'warning';
		$message = _l('ch_no_successful_approval');
		if ($warehouseman_id) {
			$message = _l('ch_no_successful_approval_cance');
		}
		if ($success) {
			$get_code = get_table_where('tbl_manufactures', array('id' => $id), '', 'row');
			activity_log_v2(
				'purchase',
				'tbl_manufactures',
				$id,
				$get_code->reference_no,
				'Cập nhật trạng thái duyệt kho phiếu sản xuất [' . $get_code->reference_no . ']'
			);
			$alert_type = 'success';
			$message = _l('ch_successful_approval');
			if ($warehouseman_id) {
				$message = _l('ch_successful_approval_cance');
			}
			if (empty($warehouseman_id)) {
				log_activity('Warehouse items approved [ID Manufacture: ' . $id);
				$this->manufacture_model->decreaseWarehouse($id);
			} else {
				log_activity('Warehouse items cancel approved [ID Manufacture: ' . $id);
				$import = get_table_where('tblimport', array('id' => $id), '', 'row');
				$this->manufacture_model->increaseWarehouse($id);
			}
		}
		echo json_encode(array(
			'alert_type' => $alert_type,
			'message' => $message,
		));
	}

	public function searchMaterialsSelect2($q, $limit = 50, $id_production_detail = '')
	{
		$tbWarehouses = '(
            SELECT
                tblwarehouse_items.id_items as id_items,
                COALESCE(SUM(tblwarehouse_items.product_quantity), 0) as total_quantity 
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            where tblwarehouse_items.type_items = "nvl"
            AND tblwarehouse_items.product_quantity > 0
            GROUP BY tblwarehouse_items.id_items
        ) ws';
		$this->db->select(
			'CONCAT(tbl_materials.id, "__materials") as id, CONCAT(tbl_materials.code, "(", tbl_materials.name, ")") as text, 
            tbl_materials.name as item_name, 
            IF(tbl_materials.images IS NOT NULL && tbl_materials.images != "", CONCAT("uploads/materials/", "", tbl_materials.images, ""), "") as images, 
            tblunits.unit as unit_name, 
            tbl_materials.price_sell as price_sell,
             "" as info, CONCAT(tbl_materials.category_id, "__materials") as category_id,ws.total_quantity as total_quantity',
			false
		);
		if (!empty($id_production_detail)) {
			$this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.item_id = tbl_materials.id');
			$this->db->where('tbl_productions_orders_items_sub.type', 'materials');
			$this->db->where('tbl_productions_orders_items_sub.productions_orders_items_id', $id_production_detail);
		}
		$this->db->from('tbl_materials');
		$this->db->join('tblunits', 'tbl_materials.unit_id = tblunits.unitid', 'left');
		$this->db->join($tbWarehouses, 'ws.id_items = tbl_materials.id', 'left');
		if (!empty($q)) {
			$this->db->group_start();
			$this->db->like('tbl_materials.code', $q);
			$this->db->or_like('tbl_materials.name', $q);
			$this->db->group_end();
		}
		$this->db->limit($limit);
		$this->db->having('total_quantity >', 0);
		return $this->db->get()->result_array();
	}

	public function searchProductAndGoods($id = false)
	{
		$data = [];
		$term = $this->input->get('term', true);
		$limit = get_option('select2_limit');
		$params = $this->input->get('params');
		$id_production_detail = $this->input->get('id_production_detail');
		if (!empty($id_production_detail)) {
			$materia = $this->searchMaterialsSelect2($term, $limit, $id_production_detail);
		} else {
			$materia = [];
		}
		$data['results'] = [
			[
				'text' => lang('materials'),
				'children' => $materia,
			]
		];
		if ($id) {
			$dt = explode('__', $id);
			$id = $dt[0];
			$type_item = $dt[1];
			$item = $this->items_model->rowMaterial($id);
			$data['row'] = ['id' => $item['id'] . '__materials', 'text' => $item['code']];
		}
		echo json_encode($data);
	}

	public function searchProductAndGoodsExport($id = false)
	{
		$data = [];
		$term = $this->input->get('term', true);
		$limit = get_option('select2_limit');
		$params = $this->input->get('params');
		$materia = $this->searchMaterialsSelect2($term, $limit);
		$data['results'] = [
			[
				'text' => lang('materials'),
				'children' => $materia,
			]
		];
		if ($id) {
			$dt = explode('__', $id);
			$id = $dt[0];
			$type_item = $dt[1];
			if ($type_item == "items") {
				$item = $this->items_model->rowItems($id);
				$data['row'] = ['id' => $item['id'] . '__' . 'items', 'text' => $item['code']];
			}
		}
		echo json_encode($data);
	}

	public function getItemsById()
	{
		$item_id = $this->input->post('item_id');
		$t_item_id = $item_id;
		$type = $this->input->post('type');
		$id_production_detail = $this->input->post('id_production_detail');

		$data = [];
		$item = false;
		if (!empty($item_id)) {
			$arrItem = explode('__', $item_id);
			$item_id = $arrItem[0];
			$item_type = $arrItem[1];
			$images = base_url('assets/images/tnh/no_image.png');
			//            $products_accessary = [];
			if ($item_type == "materials") {
				$type_items = 'nvl';
				$this->db->select('
					tbl_materials.id as id,
					tbl_materials.code as item_code,
					tbl_materials.name as item_name,
					tblunits.unit as unit_name,
					tbl_materials.images as images,
					tbl_materials.price_sell as price_sell,
					tbl_materials.height as height
            	');
				$this->db->from('tbl_materials');
				$this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id');
				$this->db->where('tbl_materials.id', $item_id);
				$item = $this->db->get()->row_array();
				if (!empty($item['images'])) {
					$images = base_url('uploads/materials/' . $item['images']);
				}
				$item['item_id'] = $t_item_id;
				$data['item'] = $item;
			}
			$warehouses = $this->manufacture_model->getWarehouse();
			$option = '<option value=""></option>';
			foreach ($warehouses as $key => $v) {
				if ($item_type == 'materials') {
					$type = 'nvl';
					// $quantity_warehouse = $this->manufacture_model->getTotalQuantityWarehousesv2($value['id'], $item_id, $type_items);
					// $option .= '<optgroup label="' . $value['name'] . '">';
					// foreach ($quantity_warehouse as $k => $v) {
					// 	$option .= '<option data-quantity="' . $v['product_quantity'] . '" value="' . $value['id'] . '__' . $v['localtion'] . '">' . $v['name'] . ' (' . formatNumber($v['product_quantity']) . ')</option>';
					// }
					// $option .= '</optgroup>';
					$this->db->select('tbllocaltion_warehouses.*,product_quantity,tblwarehouse_items.type_items,tblwarehouse_items.lot_code,tblwarehouse_items.date_sx,tblwarehouse_items.date_sd,tblwarehouse_items.date_use,tblwarehouse_items.id as idd');
					$this->db->where(array('id_items' => $item_id, 'type_items' => $type));
					$this->db->where('warehouse', $v['id']);
					$this->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
					$this->db->where('product_quantity >= 0');
					$localtion = $this->db->get('tbllocaltion_warehouses')->result_array();
					if (!empty($localtion)) {
						$option .= '<optgroup data-check ="1" data-text ="' . $v['name'] . '" label="' . $v['name'] . '">';
						foreach ($localtion as $key => $value) {
							if (!empty($value['id'])) {
								$name = get_listname_localtion_warehouse($value['id']);
								$option .= '<option data-check ="0" data-type= "' . $value['type_items'] . '" data-text ="' . $name . '(' . $value['product_quantity'] . ')" data-lot = "' . $value['lot_code'] . '"  data-date_sx = "' . _d($value['date_sx']) . '"  data-date_sd = "' . _d($value['date_sd']) . '"  data-date_use = "' . _d($value['date_use']) . '"  quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['idd'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
							}
						}
						$option .= '</optgroup>';
					}
				}
			}
			$item['option_warehouses'] = $option;

			//
			$this->db->select('
				tbl_productions_orders_items_sub.*
			', false);
			$this->db->from('tbl_productions_orders_details');
			$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
			$this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_items.id');
			$this->db->where('tbl_productions_orders_details.id', $id_production_detail);
			$this->db->where('tbl_productions_orders_items_sub.type', $item_type);
			$this->db->where('tbl_productions_orders_items_sub.item_id', $item_id);
			$info_bom = $this->db->get()->row_array();
			$item['info_bom'] = $info_bom;
		}
		$item['item_id'] = $t_item_id;
		$data['item'] = $item;
		echo json_encode($data);
	}


	function kt_quantity_manufacture($id)
	{
		$dem = 0;
		$this->db->select('*,SUM(quantity_item) as quantitys');
		$this->db->where('manufactures_id', $id);
		$this->db->group_by('tbl_manufactures_items_bom.item_id,tbl_manufactures_items_bom.warehouse_item_id,tbl_manufactures_items_bom.location_item_id,tbl_manufactures_items_bom.type_items,lot_code,date_sx,date_sd,date_use');
		$itemss = $this->db->get('tbl_manufactures_items_bom')->result_array();
		usort($itemss, ch_make_cmp(['type_items' => "desc", 'item_id' => "asc"]));
		$this->db->select('count(*) as count');
		foreach ($itemss as $key => $v) {
			if ($v['type_items'] == 'products') {
				$v['type_items'] = 'product';
			} else
				if ($v['type_items'] == 'materials') {
				$v['type_items'] = 'nvl';
			} else
					if ($v['type_items'] == 'tools_supplies') {
				$v['type_items'] = 'tools';
			} else
						if ($v['type_items'] == 'semi_products') {
				$v['type_items'] = 'product';
			}
			$dem++;
			$this->db->or_group_start();
			$this->db->where('product_quantity >=', $v['quantitys']);
			$this->db->where('warehouse_id', $v['warehouse_item_id']);
			$this->db->where('localtion', $v['location_item_id']);
			$this->db->where('type_items', $v['type_items']);
			$this->db->where('id_items', $v['item_id']);
			$this->db->where('lot_code', $v['lot_code']);
			$this->db->where('date_sx', $v['date_sx']);
			$this->db->where('date_sd', $v['date_sd']);
			$this->db->where('date_use', $v['date_use']);
			$this->db->group_end();
		}
		$result = $this->db->get('tblwarehouse_items')->row();

		if ($result->count == $dem) {
			$data = true;
		} else {
			$data = false;
		}
		return $data;
	}


	function get_warehouse_manufacture($id = '')
	{
		$dem = 0;
		$this->db->select('*,SUM(quantity_item) as quantitys');
		$this->db->where('manufactures_id', $id);
		$this->db->group_by('tbl_manufactures_items_bom.item_id,tbl_manufactures_items_bom.warehouse_item_id,tbl_manufactures_items_bom.location_item_id,tbl_manufactures_items_bom.type_items,lot_code,date_sx,date_sd,date_use');
		$itemss = $this->db->get('tbl_manufactures_items_bom')->result_array();
		foreach ($itemss as $key => $v) {
			$local = get_table_where('tbllocaltion_warehouses', array('warehouse' => $v['warehouse_item_id']), '', 'row');
			$itemss[$key]['local'] = $local->id;
		}

		usort($itemss, ch_make_cmp(['type_items' => "desc", 'item_id' => "asc"]));

		foreach ($itemss as $key => $v) {
			if ($v['type_items'] == 'products') {
				$v['type_items'] = 'product';
			} else
				if ($v['type_items'] == 'materials') {
				$v['type_items'] = 'nvl';
			} else
					if ($v['type_items'] == 'tools_supplies') {
				$v['type_items'] = 'tools';
			} else
						if ($v['type_items'] == 'semi_products') {
				$v['type_items'] = 'product';
			}
			$this->db->or_group_start();
			// $CI->db->where('quantity_left >=', $v['quantitys']);
			$this->db->where('warehouse_id', $v['warehouse_item_id']);
			$this->db->where('localtion', $v['location_item_id']);
			$this->db->where('type_items', $v['type_items']);
			$this->db->where('product_id', $v['item_id']);
			$this->db->where('lot_code', $v['lot_code']);
			$this->db->where('date_sx', $v['date_sx']);
			$this->db->where('date_sd', $v['date_sd']);
			$this->db->where('date_use', $v['date_use']);
			$this->db->group_end();
		}

		$result = $this->db->get('tblwarehouse_product')->result_array();

		foreach ($itemss as $k => $v) {
			if ($v['type_items'] == 'products') {
				$v['type_items'] = 'product';
			} else
				if ($v['type_items'] == 'materials') {
				$v['type_items'] = 'nvl';
			} else
					if ($v['type_items'] == 'tools_supplies') {
				$v['type_items'] = 'tools';
			} else
						if ($v['type_items'] == 'semi_products') {
				$v['type_items'] = 'product';
			}
			$ktr = 0;
			$itemss[$k]['name_ware'] =  get_table_where('tblwarehouse', array('id' => $v['warehouse_item_id']), '', 'row')->name;
			$itemss[$k]['name_local'] =  get_table_where('tbllocaltion_warehouses', array('id' => $v['location_item_id']), '', 'row')->name;

			foreach ($result as $key => $value) {
				if (($v['item_id'] == $value['product_id']) && ($v['warehouse_item_id'] == $value['warehouse_id']) && ($v['location_item_id'] == $value['localtion']) && ($v['type_items'] == $value['type_items'])) {

					$ktr = 1;
					// $itemss[$k]['type'] =  format_item_purchases($value['type_items']);
					$itemss[$k]['quantity_net'] =  $v['quantitys'] - $value['quantity_left'];
				}
			}
			if ($ktr == 0) {
				$itemss[$k]['quantity_net'] =  $v['quantitys'];
			}

			if ($itemss[$k]['quantity_net'] <= 0) {
				unset($itemss[$k]);
			} else {


				$itemss[$k]['detail'] = get_items($value['product_id'], $value['type_items']);
			}
		}

		return $itemss;
	}

	public function agree() {
		$manufactures_id = $this->input->post('manufactures_id');
		$status = $this->input->post('status');
		die;
		$data = [];
		if ($this->input->post()) {
			$data = handlingCommuneStages($manufactures_id, $status);
		}
		echo json_encode($data);

	}

	public function print($id, $type_pdf = 'I') {
		if (!$this->perView) {
            accessDenied();
        }


		$type_print = $this->input->get('type_print');
		if (empty($type_print)) {
			set_alert('danger',  'Vui lòng thao tác lại in');
            redirect($_SERVER["HTTP_REFERER"]);
            die();
		}
		$manufactures = $this->manufacture_model->getManufactures($id);
		// $pod = $this->manufacture_model->getProductionsOrdersDetail($manufactures['id_production_detail']);
		// $dtPod = get_table_where('tbl_productions_orders_details', ['id' => $manufactures['id_production_detail']], '', 'row_array');
		$dtPo = get_table_where('tbl_productions_orders', ['id' => $manufactures['id_production_detail']], '', 'row_array');

		$tbProductItem = "(
			SELECT
				tbl_manufactures_materials_detail.manufactures_id as manufactures_id,
				GROUP_CONCAT(distinct tbl_products.code SEPARATOR '<br>') as item_code,
				GROUP_CONCAT(distinct tbl_products.name SEPARATOR '<br>') as item_name
			FROM tbl_manufactures_materials_detail
			INNER JOIN tbl_products ON tbl_products.id = tbl_manufactures_materials_detail.product_id
			WHERE tbl_manufactures_materials_detail.manufactures_id = $id
		)";
		$dtProduct = $this->db->query($tbProductItem)->row_array();

		$manufactures_items = $this->manufacture_model->getManufacturesItems($id);
		$trItems = '';
		if (!empty($manufactures_items)) {
			foreach ($manufactures_items as $key => $value) {
				continue;
				$type_item = $value['type_items'];
                $items_id = $value['item_id'];

				if ($type_item == "products") {
					$info = $this->products_model->rowProduct($items_id);
					$unit = $this->unit_model->rowUnit($info['unit_id']);
					if (!empty($info['images'])) {
						$images = base_url('uploads/products/' . $info['images']);
					}
					$model = $info['model'];
				} elseif ($type_item == "items") {
					$info = $this->items_model->rowItems($items_id);
					$unit = $this->unit_model->rowUnit($info['unit']);
					if (!empty($info['avatar'])) {
						$images = base_url($info['avatar']);
					}
				} elseif ($type_item == "materials") {
					$info = $this->items_model->rowMaterial($items_id);
					$unit = $this->unit_model->rowUnit($info['unit_id']);
					if (!empty($info['images'])) {
						$images = base_url('uploads/materials/' . $info['images']);
					}
				} elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
					$info = $this->tools_supplies_model->rowToolsSupplies($items_id);
					$unit = $this->unit_model->rowUnit($info['unit_id']);
					if (!empty($info['avatar'])) {
						$images = base_url('uploads/tools_supplies/' . $info['images']);
					}
				}

				$tdNumber = '<td class="text-center">
					' . ++$key . '
				</td>';

				$tdCode = '<td style="text-align: left;">' . $info['code'] . '</td>';
                $tdName = '<td style="text-align: left;">' . $info['name'] . '</td>';

				$quantity_compensation = $value['quantity_compensation'] + $value['quantity_compensation_sm'];
				$quantityNeed = roundNumberFormat($value['quantity'] + $quantity_compensation, 0);

				$tdLandscapePrintSize = '<td class="tdLandscapePrintSize text-center">'.($value['landscape_print_size']).'</td>';
				$tdVerticalPrintSize = '<td class="tdVerticalPrintSize text-center">'.formatNumber($value['vertical_print_size']).'</td>';
				$tdNumberChildrenSize = '<td class="tdNumberChildrenSize text-center">'.formatNumber($value['number_children_size']).'</td>';
				$tdExchangeValue = '<td class="tdExchangeValue text-center">'.formatNumber($value['quantity_single']).'</td>';
				$tdPaperExchange = '<td class="tdPaperExchange text-center">'.formatNumber($value['paper_exchange']).'</td>';
				$tdQuantityCompensation = '<td class="tdQuantityCompensation text-center">'.formatNumber($quantity_compensation).'</td>';
				$tdQuantityNeed = '<td class="tdQuantityNeed text-center">'.formatNumber($quantityNeed, 0).'</td>';
				$tdHeight = '<td class="tdHeight text-center">'.$value['height'].'</td>';
				$tdTotalHeight = '<td class="tdTotalHeight text-center">'.formatNumber($value['total_height']).'</td>';
				$tdNote = '<td>' . $value['note_item'] . '</td>';

				$trItems.= '<tr nobr="true">
					'.$tdNumber.'
					'.$tdCode.'
					'.$tdName.'
					'.$tdQuantityNeed.'
					'.$tdHeight.'
					'.$tdTotalHeight.'
					'.$tdLandscapePrintSize.'
					'.$tdExchangeValue.'
					'.$tdPaperExchange.'
					'.$tdNote.'
				</tr>';
			}
		}

		$this->db->select('tbl_manufactures_materials.*');
		$this->db->from('tbl_manufactures_materials');
		$this->db->where('tbl_manufactures_materials.manufactures_id', $id);
		$manufactures_materials = $this->db->get()->result_array();
		if (!empty($manufactures_materials)) {
			foreach ($manufactures_materials as $key => $value) {
				$info = [];
				if ($value['item_type'] == 'materials') {
					$info = $this->items_model->rowMaterial($value['item_id']);
				} else {
					$info = $this->products_model->rowProduct($value['item_id']);
				}
				$unit = $this->unit_model->rowUnit($value['unit_id_manufactures']);

				$this->db->select('
					GROUP_CONCAT(distinct tbl_manufactures_materials_detail.landscape_print_size) as landscape_print_size,
					tbl_manufactures_materials_detail.quantity_single as quantity_single,
				', false);
				$this->db->from('tbl_manufactures_materials_detail');
				$this->db->join('tbl_products', 'tbl_products.id = tbl_manufactures_materials_detail.product_id');
				$this->db->where('tbl_manufactures_materials_detail.manufactures_materials_id', $value['id']);
				$manufactures_materials_detail = $this->db->get()->row_array();

				$quantity = roundNumberFormat($value['quantity'], 0);
				$value['quantity'] = ceil($value['quantity']);
				$quantity_need = roundNumberFormat($value['quantity'] + $value['quantity_compensation'], 0);

				$quantity_single = $manufactures_materials_detail['quantity_single'];
				// $paper_exchange = $quantity_single > 0 ? roundNumberFormat($quantity_need/$quantity_single, 0) : 0;
				$paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;

				$trItems.= '<tr nobr="true">
					<td class="text-center">'.(++$key).'</td>
					<td class="text-center">'.$info['code'].'</td>
					<td class="text-center">'.$info['name'].'</td>
					<td class="text-center">'.formatNumber($quantity_need, 0).'</td>
					<td class="text-center">'.$manufactures_materials_detail['quantity_single'].'</td>
					<td class="text-center">'.$manufactures_materials_detail['landscape_print_size'].'</td>
					<td class="text-center">'.formatNumber($paper_exchange).'</td>
					<td class="text-center">'.$unit['unit'].'</td>
					<td style="text-align: left;">'.$value['note'].'</td>
				</tr>';

				if(!empty($tam_an)) {
					$this->db->select('
					tbl_manufactures_materials_detail.*,
					tbl_products.code as product_code,
					tbl_products.name as product_name,
					tbl_stages.name as stage_name
				', false);
					$this->db->from('tbl_manufactures_materials_detail');
					$this->db->join('tbl_products', 'tbl_products.id = tbl_manufactures_materials_detail.product_id');
					$this->db->join('tbl_stages', 'tbl_stages.id = tbl_manufactures_materials_detail.stage_id', 'left');
					$this->db->where('tbl_manufactures_materials_detail.manufactures_materials_id', $value['id']);
					$manufactures_materials_detail = $this->db->get()->result_array();
					if (!empty($manufactures_materials_detail)) {
						$trItems .= '<tr nobr="true"><td colspan="7"><table style="width: 100%;">
						<tr class="bold" nobr="true">
							<td class="text-center">Mã thành phẩm</td>
							<td class="text-center">Số lượng đơn hàng</td>
							<td class="text-center">Khổ in ngang - dọc (tờ) = cm</td>
							<td class="text-center">SL con/ khổ in</td>
							<td class="text-center">Giá trị quy đổi(tính trên tờ in)</td>
							<td class="text-center">Số tờ quy đổi</td>
							<td class="text-center">Số NPL cần</td>
							<td class="text-center">Số lượng bù hao(khổ liệu)</td>
							<td class="text-center">Công đoạn</td>
						</tr>
					';
						foreach ($manufactures_materials_detail as $k => $val) {
							$trItems .= '<tr nobr="true">
							<td class="text-center">' . $val['product_code'] . '</td>
							<td class="text-center">' . formatNumber($val['quantity_order']) . '</td>
							<td class="text-center">' . $val['landscape_print_size'] . '</td>
							<td class="text-center">' . formatNumber($val['number_children_size']) . '</td>
							<td class="text-center">' . formatNumber($val['quantity_single']) . '</td>
							<td class="text-center">' . formatNumber($val['paper_exchange'], 0) . '</td>
							<td class="text-center">' . formatNumber($val['quantity'], 2) . '</td>
							<td class="text-center">' . formatNumber($val['quantity_compensation']) . '</td>
							<td class="text-center">' . $val['stage_name'] . '</td>
						</tr>';
						}
						$trItems .= '</table></td></tr>';
					}
				}
			}
		}
		ob_end_clean();
		ob_start();
        stylePdf();
        echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
			<tr nobr="true">
				<td colspan="8" class="text-center"><span class="text-center uppercase" style="font-size: 20px; font-weight: bold;">' . _l('Phiếu xả khổ') . '</span><br><span>'._dt($manufactures['date']).'</span></td>
			</tr>
		</table>
		<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td><b>' . _l('Số phiếu xả') . ':</b> '.$manufactures['reference_no'].'</td>
                </tr>
				<tr>
					<td><b>' . _l('Số lệnh sản xuất tổng') . ':</b> '.$dtPo['reference_no'].'</td>
				</tr>
				<tr nobr="true">
					<td><b>' . _l('tnh_product_code') . ':</b> '.$dtProduct['item_code'].'</td>
				</tr>
				<tr nobr="true">
					<td><b>' . _l('tnh_product_name') . ':</b> '.$dtProduct['item_name'].'</td>
				</tr>
            </table>
		<br><br>
		<table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
			<tr nobr="true" style="background-color: #ddd;">
				<td class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</td>
				<td class="bold text-center" style="width: 15%;">' . _l('tnh_material_code') . '</td>
				<td class="bold text-center" style="width: 20%;">' . _l('tnh_material_name') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Tổng SL NPL') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Số lần xả/Tờ') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Khổ xả') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Tổng số tờ in') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('ĐVT') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('note') . '</td>
			</tr>
			'.$trItems.'
		</table><br><br><table style="width: 100%">
			<tr>
				<td class="text-center">
				</td>
				<td class="text-center">
					<span class="bold">' . ('Lần in: '.$manufactures['count_print'].' - Thời gian in : '.(_d($manufactures['date_print'])).'') . '('.get_staff_full_name($manufactures['user_print']).')</span>
				</td>
			</tr>
		</table>';
		$content = ob_get_contents();
		
        ob_end_clean();
        // $data['showHeader'] = 'hide';
        // $data['type_print'] = 'quotes';
		// $data['type'] = 'L';
		$data['type'] = 'P';
		$data['pageCustome'] = 'orders_detail_1';
        $data['content'] = $content;
        $data['barcode'] = '';

        // $data['js'] = "app.alert('JavaScript Popup Example', 3, 0, 'Welcome');
		// var cResponse = app.response({
		// 	cQuestion: 'How are you today?',
		// 	cTitle: 'Your Health Status',
		// 	cDefault: 'Fine',
		// 	cLabel: 'Response:'
		// });
		// if (cResponse == null) {
		// 	app.alert('Thanks for trying anyway.', 3, 0, 'Result');
		// } else {
		// }";

		// $data['js'] = "app.onafterprint = (event) => {
		// 	app.alert('After print');
		//   }; app.alert('After print');";

        $pdf = @print_pdf_tnh($data);
        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
	}

	public function searchProductions($po_id_link = 0) {

		$term = $this->input->get('term');
		$limit = 50;
		$params = $this->input->get('params');
		$type = $params['type'];
		$results = [];

		$branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

		if ($type == 2) {
			$this->db->select('
				tbl_productions_orders_details.id as id,
				tbl_productions_orders_details.reference_no as text,
				tbl_products.code as item_code,
				tbl_products.name as item_name,
				tbl_productions_orders_items.quantity as quantity,
			', false);
			$this->db->from('tbl_productions_orders_details');
			$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
			$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
			if ($term) {
				$this->db->group_start();
				$this->db->like('tbl_products.code', $term);
				$this->db->or_like('tbl_products.name', $term);
				$this->db->or_like('tbl_productions_orders_details.reference_no', $term);
				$this->db->group_end();
			}

			if (!$is_admin) {
				if (empty($branch_staff)) $branch_staff = [0];
				$this->db->where('tbl_productions_orders.location_id IN ('.implode(',', $branch_staff).')', false, false);
			}

			$this->db->limit($limit);
			$results = $this->db->get()->result_array();
			if (!empty($results)) {
				foreach ($results as $key => $value) {
					$results[$key]['items'][] = [
						'item_code' => $value['item_code'],
						'item_name' => $value['item_name'],
						'quantity' => $value['quantity'],
					];
				}
			}
		} else if ($type == 1) {
			$this->db->select('
				tbl_productions_orders.id as id,
				tbl_productions_orders.reference_no as text
			');
			$this->db->from('tbl_productions_orders');
			if ($term) {
				$this->db->group_start();
				$this->db->where(' exists (
					SELECT tbl_productions_orders_items.id
					FROM tbl_productions_orders_items
					INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
					WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id AND (tbl_products.code like "%'.$term.'%" OR tbl_products.name like "%'.$term.'%")
				)', false, false);
				$this->db->or_like('tbl_productions_orders.reference_no', $term);
				$this->db->group_end();
			}

			if (!$is_admin) {
				if (empty($branch_staff)) $branch_staff = [0];
				$this->db->where('tbl_productions_orders.location_id IN ('.implode(',', $branch_staff).')', false, false);
			}

			$this->db->limit($limit);
			$results = $this->db->get()->result_array();
			if (!empty($results)) {
				foreach ($results as $key => $value) {
					$po_id = $value['id'];

					$this->db->select('
						tbl_products.code as item_code,
						tbl_products.name as item_name,
						tbl_productions_orders_items.quantity as quantity,
					', false);
					$this->db->from('tbl_productions_orders_items');
					$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
					$this->db->where('tbl_productions_orders_items.productions_orders_id', $po_id);
					$items = $this->db->get()->result_array();

					$results[$key]['items'] = $items;
				}
			}
		}

		if ($po_id_link) {
			$this->db->select('
				tbl_productions_orders.id as id,
				tbl_productions_orders.reference_no as text
			');
			$this->db->from('tbl_productions_orders');
			$this->db->where('tbl_productions_orders.id', $po_id_link);
			$this->db->limit($limit);
			$rows = $this->db->get()->row_array();
			// if (!empty($results)) {
			// 	foreach ($results as $key => $value) {
			// 		$po_id = $value['id'];

			// 		$this->db->select('
			// 			tbl_products.code as item_code,
			// 			tbl_products.name as item_name,
			// 			tbl_productions_orders_items.quantity as quantity,
			// 		', false);
			// 		$this->db->from('tbl_productions_orders_items');
			// 		$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
			// 		$this->db->where('tbl_productions_orders_items.productions_orders_id', $po_id);
			// 		$items = $this->db->get()->result_array();

			// 		$results[$key]['items'] = $items;
			// 	}
			// }
			$data['row'] = ['id' => $rows['id'], 'text' => $rows['text']];
		}

        $data['results'] = $results;
        echo json_encode($data);
	}

	public function searchMaterialPO() {

		$data = [];
		$term = $this->input->get('term', TRUE);
		$limit = get_option('select2_limit');
		$params = $this->input->get('params');

		$po_id = !empty($params['po_id']) ? $params['po_id'] : 0;
		$tbProductionsPlan = "(
            SELECT
                tbl_productions_plan.note,
                tbl_productions_plan.id as id
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            WHERE tbl_productions_orders_items.productions_orders_id = $po_id
        )";
        $dtProductionsPlan = $this->db->query($tbProductionsPlan)->row_array();
        $pp_id = $dtProductionsPlan['id'];

		$this->db->select('
			CONCAT(tbl_productions_orders_items_sub.type, "__", tbl_productions_orders_items_sub.item_id) as id,
			tbl_productions_orders_items_sub.type as type, 
			tbl_productions_orders_items_sub.item_id as item_id,
			CONCAT(tbl_materials.name, "(", tbl_materials.code,")") as text,
			tbl_materials.name as name,
			tbl_materials.code as code,
			tblunits.unitid as unit_id_manufactures,
			tblunits.unit as unit_name_manufactures,
			SUM(CEIL(ROUND(tbl_productions_orders_items_sub.quantity, 3))) as quantity,
			tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
			0 as quantity_compensation
		', false);
		$this->db->from('tbl_productions_orders_items_sub');
		$this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_orders_items_sub.item_id');
		$this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
		$this->db->where('tbl_productions_orders_items_sub.type', 'materials');
		$this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $po_id);
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('tbl_materials.code', $term);
			$this->db->or_like('tbl_materials.name', $term);
			$this->db->group_end();
		}
		$this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id');
		$this->db->limit($limit);
		$items = $this->db->get()->result_array();
		if (!empty($items)) {
			foreach ($items as $key => $value) {
				$item_id = $value['item_id'];
                $type = $value['type'];
				$productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($pp_id, $item_id, $type);
				if (!empty($productionsPlanCompensation['quantity_compensation'])) {
					$items[$key]['quantity_compensation'] = (float)$productionsPlanCompensation['quantity_compensation'];
				}
			}
		}

		//
		$this->db->select('
			CONCAT(tbl_productions_orders_items_sub.type, "__", tbl_productions_orders_items_sub.item_id) as id,
			tbl_productions_orders_items_sub.type as type, 
			tbl_productions_orders_items_sub.item_id as item_id,
			CONCAT(tbl_products.name, "(", tbl_products.code,")") as text,
			tbl_products.name as name,
			tbl_products.code as code,
			tblunits.unitid as unit_id_manufactures,
			tblunits.unit as unit_name_manufactures,
			SUM(CEIL(ROUND(tbl_productions_orders_items_sub.quantity, 3))) as quantity,
			tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
			0 as quantity_compensation
		', false);
		$this->db->from('tbl_productions_orders_items_sub');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items_sub.item_id');
		$this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
		$this->db->where_in('tbl_productions_orders_items_sub.type', ['semi_products', 'semi_products_outside']);
		$this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $po_id);
		if (!empty($term)) {
			$this->db->group_start();
			$this->db->like('tbl_products.code', $term);
			$this->db->or_like('tbl_products.name', $term);
			$this->db->group_end();
		}
		$this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id');
		$this->db->limit($limit);
		$items_semi = $this->db->get()->result_array();
		if (!empty($items_semi)) {
			foreach ($items_semi as $key => $value) {
				$item_id = $value['item_id'];
                $type = $value['type'];
				$productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($pp_id, $item_id, $type);
				if (!empty($productionsPlanCompensation['quantity_compensation'])) {
					$items_semi[$key]['quantity_compensation'] = (float)$productionsPlanCompensation['quantity_compensation'];
				}
			}
		}

		$items = array_merge($items, $items_semi);

		$data['results'] = $items;
		echo json_encode($data);
	}

	public function getItemsByPOId() {
		$data = [];

		$po_id = $this->input->post('po_id');
		$item_id = $this->input->post('item_id');
		$arr_item_id = explode('__', $item_id);

		$item_type = $arr_item_id[0];
		$_item_id = $arr_item_id[1];

		$this->db->select('
			tbl_productions_orders_items.id as poi_id,
			tbl_products.id as product_id,
			tbl_products.code as product_code,
			tbl_products.name as product_name,
			tbl_productions_orders_items.quantity as quantity_order,
			tbl_productions_orders_items_sub.landscape_print_size as landscape_print_size,
			tbl_productions_orders_items_sub.vertical_print_size as vertical_print_size,
			tbl_productions_orders_items_sub.number_children_size as number_children_size,
			tbl_productions_orders_items_sub.paper_exchange as paper_exchange,
			tbl_productions_orders_items_sub.quantity_single as quantity_single,
			tbl_productions_orders_items_sub.quantity_primary as quantity_primary,
			tbl_productions_orders_items_sub.quantity as quantity,
			tbl_productions_orders_items_sub.quantity_compensation as quantity_compensation,
			tbl_productions_orders_items_sub.quantity_compensation_primary as quantity_compensation_primary,
			tbl_stages.id as stage_id,
			tbl_stages.name as stage_name,
		', false);
		$this->db->from('tbl_productions_orders_items_sub');
		$this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_sub.productions_orders_items_id');
		$this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_sub.stage_item_id', 'left');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');
		$this->db->where('tbl_productions_orders_items_sub.type', $item_type);
		$this->db->where('tbl_productions_orders_items_sub.item_id', $_item_id);
		$this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $po_id);
		$productions_orders_items_sub = $this->db->get()->result_array();
		$data['productions_orders_items_sub'] = $productions_orders_items_sub;
		echo json_encode($data);
	}

	public function printPdfManu() {
		$data = [];

		$id = $this->input->post('id');
		$date = date('Y-m-d H:i:s');
		$staff_id = get_staff_user_id();

		$count_stt = 1;
		$this->db->where('tbl_manufactures.id', $id);
		$this->db->set('count_print', 'count_print+'.$count_stt, false);
		$this->db->set('date_print', $date);
		$this->db->set('user_print', $staff_id);
		$up = $this->db->update('tbl_manufactures');
		if ($up) {
			$data['result'] = 1;
			$data['message'] = lang('success');
			$data['url'] = base_url('admin/manufacture/print/'.$id.'?type_print="true"');
		} else {
			$data['result'] = 0;
			$data['message'] = lang('fail');
		}
		echo json_encode($data);
	}

	public function searchProductionsOrders()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = get_option('select2_limit');

			$this->db->select('
				tbl_productions_orders.id as id,
				tbl_productions_orders.reference_no as name,
			', false);
			$this->db->from('tbl_productions_orders');
			if (!empty($q)) {
				$this->db->group_start();
				$this->db->like('tbl_productions_orders.reference_no', $q);
				$this->db->group_end();
			}
			$this->db->limit($limit);
			$data = $this->db->get()->result_array();
        }
        echo responseData($data);
    }
}
