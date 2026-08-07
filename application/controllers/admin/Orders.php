<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Orders extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('quotes_orders_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('deliveries_model');
        $this->load->model('returned_goods_model');
        $this->load->model('type_orders_model');
        $this->load->model('status_orders_model');
        $this->load->model('price_list_model');
        $this->load->model('transfer_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('orders');
        $this->datetime_now = time();
        $this->tnh = true;

        //permission orders
        $this->perViewOrders = has_permission('orders', '', 'view');
        $this->perViewOwnOrders = has_permission('orders', '', 'view_own');
        $this->perAddOrders = has_permission('orders', '', 'create');
        $this->perEditOrders = has_permission('orders', '', 'edit');
        $this->perDeleteOrders = has_permission('orders', '', 'delete');
        $this->perExportOrders = has_permission('orders', '', 'export');
        $this->perApproveOrders = has_permission('orders', '', 'approve');
        $this->perPrintOrders = has_permission('orders', '', 'print');
        $this->perCostOrders = has_permission('orders', '', 'cost');
        $this->perProfitOrders = has_permission('orders', '', 'profit');
        $this->perViewWarehouses = has_permission('orders', '', 'profit');
		$this->is_branch = true;
    }

    public function index()
    {

        // $channels = ['tnh-custom'];
        // $channels = array_unique($channels);
        // $this->load->library('app_pusher');

        // $this->app_pusher->trigger($channels, 'tnh-custom', [createdPopupNotification('orders')]);

        if (!$this->perViewOrders && !$this->perViewOwnOrders) {
            accessDenied();
        }

        foreach (workflowOrders() as $key => $value) {
            $data['status'][$key] = $this->orders_model->countOrdersStatus($key);
        }
        $data['un_approved'] = $this->orders_model->countOrdersStatus('un_approved');
        $data['approved'] = $this->orders_model->countOrdersStatus('approved');
        $data['all'] = $this->orders_model->countOrdersStatus('all');
        $data['type_orders'] = $this->type_orders_model->getTypeOrders();
        $data['status_orders'] = $this->status_orders_model->getStatusOrders();

//		$this->db->where("exists ((
//									SELECT 1
//									FROM tbl_order_item_shippings
//									JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
//									WHERE
//										tbl_order_items.order_id = tbl_orders.id
//										AND
//										(
//											(
//												DATE_FORMAT(date_shipping, '%Y-%m-%d') <  ".date('Y-m-d')."
//												AND tbl_order_items.quantity > tbl_order_items.quantity_delivery
//											)
//											OR
//											(
//												SELECT MAX(DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d'))
//												FROM tbl_deliveries
//												WHERE tbl_deliveries.order_id = tbl_order_items.order_id
//											) > DATE_FORMAT(date_shipping, '%Y-%m-%d')
//										)
//									LIMIT 1
//								))", false, false);
//		$data['out_time_ship'] = $this->db->get('tbl_orders')->num_rows();

        $data['type_items'] = $this->db->get('tbltype_orders_items')->result_array();


		$this->db->where("exists ((
									SELECT 1
									FROM tbl_order_item_shippings
									JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
									WHERE
										tbl_order_items.order_id = tbl_orders.id
										AND DATE_FORMAT(date_shipping, '%Y-%m-%d') <  '".date('Y-m-d')."'
										AND tbl_order_items.quantity > (SELECT
										    SUM(tblorders_ship.quantity_shipping)
										    FROM tbl_order_item_shippings tblorders_ship
										    WHERE tblorders_ship.order_item_id = tbl_order_items.id
                                        )
									LIMIT 1
								))", false, false);
		$data['out_time_ship'] = $this->db->get('tbl_orders')->num_rows();

        $data['tnh'] = $this->tnh;
        $data['title'] = lang('tnh_orders');
        $this->load->view('admin/orders/index_fix', $data);
    }

    public function add()
    {
        if (!$this->perAddOrders) {
            accessDenied();
        }
        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required|is_unique[tbl_orders.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('type_orders', lang("tnh_type_orders"), 'required');
            $this->form_validation->set_rules('id_branch', lang("id_branch"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = getReference('orders');
                $date = to_sql_date($this->input->post('date'), true);
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $address_delivery = $this->input->post('address_delivery');
                $employees = $this->input->post('employees') ? $this->input->post('employees') : 0;
                $note = $this->input->post('note');
                $table_price_id = $this->input->post('table_price_id');
                $table_discount_id = $this->input->post('table_discount_id');
                $cost_delivery = number_unformat($this->input->post('cost_delivery'));
                $transporters = $this->input->post('transporters');
                $charge_party = $this->input->post('charge_party');

                $currencies = $this->input->post('currencies');
                $amount_to_vnd = number_unformat($this->input->post('amount_to_vnd'));
                $type_orders = $this->input->post('type_orders');
                $status_orders = $this->input->post('status_orders');
                $type_items = $this->input->post('type_items');
                $reference_no_customer = !empty($this->input->post('reference_no_customer')) ? $this->input->post('reference_no_customer') : null;
                $person_contact_id = $this->input->post('person_contact') ? $this->input->post('person_contact') : 0;

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $tax_id = $this->input->post('tax_id') ? $this->input->post('tax_id') : 0;
                $tax_name = 0;
                $tax_rate = 0;
                $total_tax = 0;
                $discount_percent = $this->input->post('discount_percent') ? $this->input->post('discount_percent') : 0;
                $total_discount_percent = 0;
                $total_discount_direct = $this->input->post('discount_direct') ? number_unformat($this->input->post('discount_direct')) : 0;
                $discount_percent = 0;
                $total_discount_direct = 0;

                $grand_total = 0;
                $status = 'un_approved';
                $gift = $this->input->post('gift') ? $this->input->post('gift') : 0;
                $total_cost_temporary_capital = 0;
                $total_profit_temporary_capital = 0;

                $payment = number_unformat($this->input->post('payment_money'));
                $staff_coupon = $this->input->post('staff_coupon');
                $payment_mode = $this->input->post('payment_mode');
                $status_payment = $this->input->post('status_payment');
                $hold_the_goods = $this->input->post('hold_the_goods') ? $this->input->post('hold_the_goods') : 0;
                $staffIdAll = get_staff_user_id();
                $dateAll = date('Y-m-d H:i:s');
                $id_branch = $this->input->post('id_branch') ? $this->input->post('id_branch') : null;

                $so = $this->input->post('so');
                $pi = $this->input->post('pi');
                $po_style = $this->input->post('po_style');
                $item_code_tem = $this->input->post('item_code');

                $counter = $this->input->post('counter');
                $arr_id = [];
                $arr_info = [];

                $itemsChild = $this->input->post('itemsChild');
                $itemsChildSize = $this->input->post('itemsChildSize');
                $itemsChildColumns = $this->input->post('itemsChildColumns');
                $counter_item = 0;
                $grand_total_quantity = 0;
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arrs = explode('__', $items_id);
                        $item_id = $arrs[0];
                        $type_item = $arrs[1];
                        $quantity_child_sheet = 0;
                        $quantity_sheet_bale = 0;

                        $unit_id = !empty($this->input->post('unit')[$value]) ? $this->input->post('unit')[$value] : 0;
                        $conversion_quantity_unit = 1;
                        $conversion_quantity_unit_default = 1;
                        if (empty($unit_id)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn đơn vị tính');
                            echo json_encode($data); die;
                        }

                        $info = null;
                        $loss = 0;
                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($item_id);
                            $quantity_child_sheet = $info['quantity_child_sheet'];
                            $quantity_sheet_bale = $info['quantity_sheet_bale'];
                            $loss = $info['loss'];
                            if ($unit_id != $info['unit_id']) {
                                $conversion_quantity_unit = $info['conversion_quantity_unit'];
                            }

                            $conversion_quantity_unit_default = $info['conversion_quantity_unit'];
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($item_id);
                        } else if ($type_item == "materials") {
                            $info = $this->items_model->rowMaterial($item_id);
                        }
                        if (empty($info)) {
                            continue;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];

                        // $order_code = $this->input->post('order_code')[$value];
                        // $command = $this->input->post('command')[$value];
                        // $quantity_loss = number_unformat($this->input->post('quantity_loss')[$value]);


                        $ct_counter_item = 0;
                        $arrItemsChildColumns = [];
                        $counter_items_number = 0;
                        $quantity = 0;
                        $total_quantity_loss = 0;
                        $total_quantity_sample = 0;
                        if ($type_orders == ORDER_CHANGE || $type_orders == ORDER_DEFAULT || $type_orders) {
                            if (!empty($itemsChildColumns[$value])) {
                                foreach ($itemsChildColumns[$value] as $kICC => $vICC) {
                                    $columns_id = !empty($vICC['columns_id']) ? $vICC['columns_id'] : NULL;
                                    $columns_value = !empty($vICC['columns_value']) ? $vICC['columns_value'] : NULL;
                                    $columns_name = !empty($vICC['columns_name']) ? $vICC['columns_name'] : NULL;
                                    if (!empty($columns_id)) {
                                        foreach ($columns_id as $kC => $vC) {
                                            $columns_name_item = !empty($columns_name[$kC]) ? $columns_name[$kC] : '';
                                            $columns_value_item = !empty($columns_value[$kC]) ? $columns_value[$kC] : '';
                                            if (empty($columns_name_item)) continue;
                                            $arrItemsChildColumns[] = [
                                                'counter_item' => $counter_item,
                                                'columns_id' => $vC,
                                                'columns_name' => $columns_name_item,
                                                'columns_value' => $columns_value_item,
                                                'counter_items_number' => $counter_items_number,
                                            ];
                                        }
                                    }

                                    //default
                                    $columns_value_date_ship = $vICC['columns_value_date_ship'];
                                    $date_ship = $vICC['date_ship'];
									
                                    $columns_value_order_code = $vICC['columns_value_order_code'];
                                    $order_code = $vICC['order_code'];
                                    $columns_value_command = $vICC['columns_value_command'];
                                    $command = $vICC['command'];

                                    $columns_value_quantity_put = $vICC['columns_value_quantity_put'];
                                    $quantity_put = number_unformat($vICC['quantity_put']);

                                    $columns_value_quantity_loss = $vICC['columns_value_quantity_loss'];
                                    $quantity_loss = number_unformat($vICC['quantity_loss']);

                                    $columns_value_sample_quantity_item = $vICC['columns_value_sample_quantity_item'];
                                    $sample_quantity_item = number_unformat($vICC['sample_quantity_item']);

                                    if (empty($date_ship)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập ngày giao');
                                        echo json_encode($data);
                                        die;
                                    }
									
                                    if (empty($order_code)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập mã đơn đặt');
                                        echo json_encode($data);
                                        die;
                                    }

                                    if (empty($command)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập chỉ lệnh');
                                        echo json_encode($data);
                                        die;
                                    }

                                    if (empty($quantity_put)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập số lượng đặt > 0');
                                        echo json_encode($data);
                                        die;
                                    }

                                    if ($quantity_loss === '') {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập số lượng loss');
                                        echo json_encode($data);
                                        die;
                                    }

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $date_ship,
                                        'columns_value' => $columns_value_date_ship,
                                        'counter_items_number' => $counter_items_number,
                                    ];
									$arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $order_code,
                                        'columns_value' => $columns_value_order_code,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $command,
                                        'columns_value' => $columns_value_command,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $quantity_put,
                                        'columns_value' => $columns_value_quantity_put,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $quantity_loss,
                                        'columns_value' => $columns_value_quantity_loss,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $sample_quantity_item,
                                        'columns_value' => $columns_value_sample_quantity_item,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $quantity += $quantity_put;
                                    $total_quantity_loss += $quantity_loss;
                                    $total_quantity_sample += $sample_quantity_item;

                                    $counter_items_number++;
                                    $counter_item++;
                                }
                            }
                        }

                        $ct_counter_item = $counter_items_number;
                        // $sample_quantity =  number_unformat($this->input->post('sample_quantity')[$value]);
                        $sample_quantity =  $total_quantity_sample;
                        $note_item = $this->input->post('note_items')[$value];
                        // $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $price = number_unformat($this->input->post('price')[$value]);
                        $is_lot = !empty($this->input->post('is_lot')[$value]) ? 1 : 0;
                        if ($is_lot) {
                            $amount = $price;
                        } else {
                            $amount = $quantity * $price;
                        }

                        $total_quantity_item = $sample_quantity + $quantity + $total_quantity_loss;
                        if ($type_orders == TYPE_PTM) {
                            if ($total_quantity_item < QUANTITY_PTM) {
                                $data['result'] = 0;
                                $data['danger'] = 'Loại đơn hàng phát triển mẫu vui lòng tổng số lượng lớn hơn bằng 200';
                                echo json_encode($data); die;
                            }
                        }

                        $grand_total_quantity += $total_quantity_item;

                        $sub = [];
                        $date_sub = !empty($this->input->post('date_sub')[$value]) ? $this->input->post('date_sub')[$value] : false;
                        $total_quantity_sub = 0;
                        if (!empty($date_sub)) {
                            foreach ($date_sub as $k => $val) {
                                if (empty($val)) continue;
								if(strtotime(to_sql_date($val.' 23:59:59', true)) < strtotime($date)) {
									$data['result'] = 0;
									$data['message'] = 'Ngày giao hàng dự kiến không được nhỏ hơn ngày đơn hàng';
									echo json_encode($data);die();
								}

                                // $quantity_sub = number_unformat($this->input->post('quantity_sub')[$value][$k]);
                                $quantity_sub = $total_quantity_item;
                                $sub[] = [
                                    'date_shipping' => to_sql_date($val),
                                    'quantity_shipping' => $quantity_sub
                                ];
                                $total_quantity_sub += $quantity_sub;
                            }

                            if ($total_quantity_sub > $total_quantity_item) {
                                $data['result'] = 0;
                                $data['message'] = lang('tnh_check_date_enter');
                                echo json_encode($data);
                                die;
                            }
                        }

                        if (empty($sub)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn ngày giao hàng dự kiến');
                            echo json_encode($data);
                            die;
                        }

                        $grand_total_item = $amount;
                        //tax item
                        $tax_id_item = $this->input->post('tax_item')[$value] ? $this->input->post('tax_item')[$value] : 0;
                        $tax_name_item = "0%";
                        $tax_rate_item = 0;
                        $tax_amount_item = 0;
                        if (!empty($tax_id_item)) {
                            $info_tax = $this->site_model->rowTax($tax_id_item);
                            if (!empty($info_tax)) {
                                $tax_name_item = $info_tax['name'];
                                $tax_rate_item = $info_tax['taxrate'];
                            }
                        }

                        //end
                        //discount percent item
                        $discount_percent_item = !empty($this->input->post('discount_percent_item')[$value]) ? number_unformat($this->input->post('discount_percent_item')[$value]) : 0;
                        $discount_percent_amount_item = 0;
                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                            $total_discount_percent_items += $discount_percent_amount_item;
                            $grand_total_item -= $discount_percent_amount_item;
                        }
                        //end
                        $discount_direct_amount_item = !empty($this->input->post('discount_direct_item')[$value]) ? number_unformat($this->input->post('discount_direct_item')[$value]) : 0;

                        $total_discount_direct_items += $discount_direct_amount_item;
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

                        if ($tax_rate_item > 0) {
                            $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                            $total_tax_items += $tax_amount_item;
                            $grand_total_item += $tax_amount_item;
                        }

                        //exchange
                        $exchange = [];
                        if ($type_item == "products") {
                            $exchangeUnits = $this->products_model->getExchangeProductsByProductId($item_id);
                            if (!empty($exchangeUnits)) {
                                foreach ($exchangeUnits as $k => $val) {
                                    if (empty($val)) continue;
                                    $quantity_exchange = $val['number_exchange'];
                                    if (empty($quantity_exchange)) $quantity_exchange = 1;
                                    $total_quantity_exchange = $quantity / $quantity_exchange;
                                    $exchange[] = [
                                        'unit_id' => $val['unit_id'],
                                        'quantity_exchange' => $quantity_exchange,
                                        'total_quantity_exchange' => $total_quantity_exchange,
                                    ];
                                }
                            }
                        }
                        //end exchange
                        $arrChange = [];
                        if ($type_orders == ORDER_CHANGE) {
                            if (!empty($itemsChild[$value])) {
                                foreach ($itemsChild[$value] as $kC => $vC) {
                                    if (empty($vC['size'])) continue;
                                    $arrChange[] = [
                                        'size ' => $vC['size'],
                                        'size_dc' => $vC['size_dc'],
                                        'style_number' => $vC['style_number'],
                                        'color' => $vC['color'],
                                        'quantity' => number_unformat($vC['quantity']),
                                    ];
                                }
                            }
                        }

                        $arrChangeSize = [];
                        if ($type_orders == ORDER_CHANGE_SIZE) {
                            if (!empty($itemsChildSize[$value])) {
                                foreach ($itemsChildSize[$value] as $kC => $vC) {
                                    if (empty($vC['number_size'])) continue;

                                    $quantity_child = number_unformat($vC['quantity']);
                                    $even_quantity = 0;
                                    $odd_quantity = 0;
                                    $quantity_sheet = 0;
                                    if ($quantity_child_sheet > 0) {
                                        $quantity_sheet = $quantity_child / $quantity_child_sheet;
                                        $even_quantity = floor($quantity_sheet);
                                        $quantity_ceil = ceil($quantity_sheet);
                                        $odd_quantity = $quantity_ceil - $even_quantity;
                                    }

                                    $even_quantity_bale = 0;
                                    $odd_quantity_bale = 0;
                                    $quantity_bale = 0;
                                    if ($quantity_sheet_bale > 0) {
                                        $quantity_bale = $quantity_child / $quantity_sheet_bale;
                                        $even_quantity_bale = floor($quantity_bale);
                                        $quantity_ceil_bale = ceil($quantity_bale);
                                        $odd_quantity_bale = $quantity_ceil_bale - $even_quantity_bale;
                                    }

                                    $arrChangeSize[] = [
                                        'number_size' => $vC['number_size'],
                                        'quantity' => $quantity_child,
                                        'even_sheet' => $even_quantity,
                                        'odd_sheet' => $odd_quantity,
                                        'even_bale' => $even_quantity_bale,
                                        'odd_bale' => $odd_quantity_bale,
                                        'quantity_sheet' => $quantity_sheet,
                                        'quantity_bale' => $quantity_bale,
                                    ];
                                }
                            }
                        }

                        $hand_input_price = !empty($this->input->post('hand_input_price')[$value]) ? $this->input->post('hand_input_price')[$value] : 0;

                        $product_name_customer = !empty($this->input->post('product_name_customer')[$value]) ? $this->input->post('product_name_customer')[$value] : '';
                        if (empty($product_name_customer)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng nhập tên thành phẩm của khách hàng');
                            echo json_encode($data);
                            die;
                        }

						$ship = !empty($this->input->post('ship')[$value]) ? $this->input->post('ship')[$value] : [];

                        $items[] = [
                            // 'order_code' => $order_code,
                            // 'command' => $command,
                            'quantity_loss' => $total_quantity_loss,
                            'sample_quantity' => $sample_quantity,
                            'total_quantity_item' => $total_quantity_item,

                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'tax_id_item' => $tax_id_item,
                            'tax_name_item' => $tax_name_item,
                            'tax_rate_item' => $tax_rate_item,
                            'tax_amount_item' => $tax_amount_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'note_item' => $note_item,
                            'cost_temporary_capital' => $cost_temporary_capital,
                            'profit_temporary_capital' => $profit_temporary_capital,
                            'quantity_child_sheet' => $quantity_child_sheet,
                            'quantity_sheet_bale' => $quantity_sheet_bale,
                            'sub' => $sub,
                            'exchange' => $exchange,
                            'arrChange' => $arrChange,
                            'arrChangeSize' => $arrChangeSize,
                            'arrItemsChildColumns' => $arrItemsChildColumns,
                            'ct_counter_item' => $ct_counter_item,
                            'hand_input_price' => $hand_input_price,
                            'loss' => $loss,
                            'product_name_customer' => $product_name_customer,
                            'ship' => $ship,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                            'conversion_quantity_unit_default' => $conversion_quantity_unit_default,
                            'is_lot' => $is_lot
                        ];

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                        $total_cost_temporary_capital += $cost_temporary_capital;
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }


                // print_arrays($items);
                $count_items = count($items);
                $grand_total = $grand_total_items;
                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }

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

                if ($type_customer == "customers") {
                    $row_customer = $this->site_model->rowCustomer($customer_id);
                    if (empty($row_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_customer_not_exist');
                        echo json_encode($data);
                        die;
                    }
                    $customer_name = $row_customer['company_short'];
                    $address_delivery_id = $address_delivery;
                }

                //handling upload file
                $this->load->library('upload');
                if (!empty($_FILES['files']) && !empty($_FILES['files']['size'])) {
                    $fileCount = count($_FILES['files']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $name = $_FILES['files']['name'][$i];
                        $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                        $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                        $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                        $config['upload_path'] = $this->upload_path;
                        $config['allowed_types'] = $this->types;
                        $config['file_name'] = $this->datetime_now . '_' . vn_to_str($name);
                        $config['overwrite'] = TRUE;
                        //$config['max_filename'] = 25;
                        $config['encrypt_name'] = false;
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('file')) {
                            $uploadData[$i] = $this->upload->file_name;
                        }
                    }
                }
                //end handling upload file

                $status_payment = 0;
                if ($payment >= $grand_total && $payment > 0) {
                    $status_payment = 2;
                } else if ($payment > 0) {
                    $status_payment = 1;
                }

                //
                $orderSub = [];
                $arrData = [];
                if ($type_orders == TYPE_SAMPLE_ORDER || $type_orders == TYPE_KH_ORDER || $type_orders == TYPE_PTM) {
                    $date_quotes_chonse = date('Y-m-d H:i:s');
                    $staff_quotes_chonse = get_staff_user_id();
                    $quote_id_chonse = $this->input->post('quote_id_chonse');
                    if (empty($quote_id_chonse) && $type_orders == TYPE_SAMPLE_ORDER) {
                        // $data['result'] = 0;
                        // $data['message'] = lang('Đơn hàng mẫu vui lòng chọn báo giá mẫu');
                        // echo json_encode($data);
                        // die;
                    }

                    if ($quote_id_chonse) {
                        $orderSub = [
                            'order_id' => 0,
                            'quote_id_chonse' => $quote_id_chonse,
                            'date_quotes_chonse' => $date_quotes_chonse,
                            'staff_quotes_chonse' => $staff_quotes_chonse,
                        ];
                    }
                } else if ($type_orders == TYPE_COMPENSATE_ORDER) {
                    $orders_choose = $this->input->post('orders_choose');
                    $productions_orders_choose = $this->input->post('productions_orders_choose');

                    if (empty($orders_choose) && empty($productions_orders_choose)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đơn hàng bù vui lòng chọn bù cho đơn hàng hoặc bù cho LSX');
                        echo json_encode($data);
                        die;
                    }
                }

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery_id,
                    'employee_id' => $employees,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'total_amount_items' => $total_amount_items,
                    'total_tax_items' => $total_tax_items,
                    'total_discount_percent_items' => $total_discount_percent_items,
                    'total_discount_direct_items' => $total_discount_direct_items,
                    'grand_total_items' => $grand_total_items,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax, //tổng thuế
                    'discount_percent' => $discount_percent, //% chiết khấu
                    'total_discount_percent' => $total_discount_percent, //tổng tiền chiết khấu phần trăm
                    'total_discount_direct' => $total_discount_direct, //tổng tiền chiết khấu tiền mặt
                    'grand_total' => $grand_total, //tổng tiền đơn hàng
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                    'table_price_id' => $table_price_id,
                    'table_discount_id' => $table_discount_id,
                    'cost_delivery' => $cost_delivery,
                    'gift' => $gift,
                    'transporter_id' => $transporters,
                    'charge_party' => $charge_party,
                    'person_contact_id' => $person_contact_id,
                    'total_cost_temporary_capital' => $total_cost_temporary_capital, //giá vốn tạm thời
                    'total_profit_temporary_capital' => $total_profit_temporary_capital, //chi phí lợi nhuận tạm thời
                    'total_payment' => $payment,
                    'staff_coupon' => $staff_coupon,
                    'payment_mode' => $payment_mode,
                    'status_payment_orders' => $status_payment,
                    'status_payment' => $status_payment,
                    'hold_the_goods' => $hold_the_goods,
                    'id_branch' => $id_branch,
                    'currencies' => $currencies,
                    'amount_to_vnd' => $amount_to_vnd,
                    'type_orders' => $type_orders,
                    'status_orders' => $status_orders,
                    'type_items' => $type_items,
                    'grand_total_quantity' => $grand_total_quantity,
                    'so' => $so,
                    'pi' => $pi,
                    'po_style' => $po_style,
                    'item_code' => $item_code_tem,
                    'reference_no_customer' => $reference_no_customer,
                ];

                // print_arrays($orderSub);
                if (get_option('auto_agree_orders') == 1) {
                    $options['status'] = "approved";
                    $options['user_status'] = get_staff_user_id();
                    $options['date_status'] = date('Y-m-d H:i:s');
                }

                if (!empty($uploadData)) {
                    $options['attachments'] = implode('||', $uploadData);
                } else {
                    $options['attachments'] = NULL;
                }

                // print_arrays($options);

                $order_id = $this->orders_model->insertOrdersNew($options);
                if ($order_id) {

                    // if (getReference('orders') == $this->input->post('reference_no')) {
                    updateReference('orders');
                    // }

                    foreach ($items as $key => $value) {
                        $value['order_id'] = $order_id;
                        $sub = $value['sub'];
                        $ship = $value['ship'];
                        $exchange = $value['exchange'];
                        $arrChange = $value['arrChange'];
                        $arrChangeSize = $value['arrChangeSize'];
                        $arrItemsChildColumns = $value['arrItemsChildColumns'];
                        unset($value['sub']);
                        unset($value['ship']);
                        unset($value['exchange']);
                        unset($value['arrChange']);
                        unset($value['arrChangeSize']);
                        unset($value['arrItemsChildColumns']);
                        $order_item_id = $this->orders_model->insertOrderItemsNew($value);
                        if ($order_item_id) {
                            if (!empty($sub)) {
                                foreach ($sub as $k => $val) {
                                    $val['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemShippingsNew($val);
                                }
                            }
							if (!empty($ship)) {
                                foreach ($ship['date'] as $k => $val) {
                                    if(empty($ship['date'][$k])) continue;
									$this->db->insert('tbl_orders_ship', [
										'order_item_id' => $order_item_id,
										'date' => to_sql_date($ship['date'][$k]),
										'quantity' => number_unformat($ship['quantity'][$k]),
									]);
                                }
                            }

                            if (!empty($exchange)) {
                                foreach ($exchange as $k => $val) {
                                    $val['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemExchange($val);
                                }
                            }

                            if (!empty($arrChange)) {
                                foreach ($arrChange as $kC => $vC) {
                                    $arrChange[$kC]['order_id'] = $order_id;
                                    $arrChange[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsSize($arrChange);
                            }

                            if (!empty($arrChangeSize)) {
                                foreach ($arrChangeSize as $kC => $vC) {
                                    $arrChangeSize[$kC]['order_id'] = $order_id;
                                    $arrChangeSize[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsChangeSize($arrChangeSize);
                            }

                            if (!empty($arrItemsChildColumns)) {
                                foreach ($arrItemsChildColumns as $kC => $vC) {
                                    $arrItemsChildColumns[$kC]['order_id'] = $order_id;
                                    $arrItemsChildColumns[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsColumns($arrItemsChildColumns);
                            }
                        }
                    }

                    // $wf = $this->site_model->insertOrdersWorkflow([
                    //     'workflow_id' => 0,
                    //     'order_id' => $order_id,
                    //     'created_by' => get_staff_user_id(),
                    //     'date_created' => date('Y-m-d H:i:s'),
                    // ]);

                    //payment
                    if ($payment > 0) {
                        $paymentData = [
                            'staff_create' => get_staff_user_id(),
                            'date_create' => date('Y-m-d H:i:s'),
                            'date_vouchers' => $date,
                            'arr_code_orders' => $order_id . '|' . $payment,
                            'code_vouchers' => get_option('prefix_coupon') . sprintf('%06d', ch_getMaxID('id', 'tblvouchers_coupon') + 1),
                            'customer' => $customer_id,
                            'staff' => get_staff_user_id(),
                            'payment_mode' => $payment_mode,
                            'total' => $grand_total,
                            'payment' => $payment
                        ];

                        $insertPayment = $this->db->insert('tblvouchers_coupon', $paymentData);
                        if ($insertPayment) {
                            $idPayment = $this->db->insert_id();

                            $this->db->insert('tblvouchers_coupon_detal', [
                                'id_order' => $order_id,
                                'id_vouchers' => $idPayment,
                                'payment' => $payment,
                            ]);
                        }
                    }
                    //

                    //stages
                    $this->orders_model->handlingStagesOrders($order_id);
                    if (!empty($orderSub)) {
                        $orderSub['order_id'] = $order_id;
                        $rs = $this->orders_model->handlingOrdersSub($order_id, $orderSub);
                    }

                    if (!empty($orders_choose)) {
                        foreach ($orders_choose as $key => $value) {
                            $arrData[] = [
                                'order_id' => $order_id,
                                'type_relationship' => 2,
                                'object_id' => $value,
                            ];
                        }
                    }
        
                    if (!empty($productions_orders_choose)) {
                        foreach ($productions_orders_choose as $key => $value) {
                            $arrData[] = [
                                'order_id' => $order_id,
                                'type_relationship' => 1,
                                'object_id' => $value,
                            ];
                        }
                    }

                    if (!empty($arrData)) {
                        $this->orders_model->handlingOrdersRelationship($order_id, $arrData);
                    }
                    //

                    set_alert('success', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    $data['gift'] = $gift;
                    $data['linkGift'] = base_url('admin/orders/gift/' . $order_id);
                    $data['order_id'] = $order_id;
                    $data['action'] = 'add';

                    noti_custom('create_orders', $order_id, get_staff_user_id(), 0, '', ['actions' => 'add']);

                    @pusherTNHNotfication();
                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $order_id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_add_orders') . ' [' . $reference_no . ']',
                        'actions' => 'add'
                    ]);
                } else {
                    if (!empty($uploadData)) {
                        foreach ($uploadData as $key => $value) {
                            if (file_exists($this->upload_path . '' . $value)) {
                                unlink($this->upload_path . '' . $value);
                            }
                        }
                    }
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        //hoàng crm bổ xung tự động select customer
        if ($this->input->get('customer_id')) {
            $data['id_customer'] = 'customers' . '__' . $this->input->get('customer_id');
        }
        //end
        $data['size'] = $this->site_model->getSize();
        $data['colors'] = $this->site_model->getColors();
        $data['type_orders'] = $this->type_orders_model->getTypeOrders();
        $data['status_orders'] = $this->status_orders_model->getStatusOrders();

        $data['type_items'] = $this->db->get('tbltype_orders_items')->result_array();
        $data['currencies'] = $this->site_model->getCurrencies();
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $data['quotes_note_default'] = $this->site_model->getQuotesNoteDefault();
        $data['reference_no'] = getReference('orders');
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['taxs'] = $this->site_model->getTaxs();
        $data['tnh'] = true;
        $data['branch'] = get_table_where('tblbranch');
        $data['title'] = lang('tnh_add_order');
        $data['breadcrumb'] = [array('link' => base_url('admin/orders'), 'page' => lang('tnh_orders')), array('link' => '#', 'page' => lang('tnh_add_order'))];
        $this->load->view('admin/orders/add', $data);
    }

    public function edit($id)
    {
        if (!$this->perEditOrders) {
            accessDenied();
        }

        $this->db->where('order_id', $id);
        $this->db->where('active', 1);
        $order_items_kt = $this->db->get('tbl_order_items_stages')->num_rows();
        if (!empty($order_items_kt)) {
            set_alert('danger',  'Đơn hàng đã được duyệt không thể chỉnh sửa');
            redirect($_SERVER["HTTP_REFERER"]);
            die();
        }


        $order = $this->orders_model->rowOrderById($id);
        checkMyData($order['created_by']);

        if (empty($order)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_orders.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tbl_orders.id = 0', false, false);
				}
				$this->db->where('id', $id);
				$ktOrders = $this->db->get('tbl_orders')->row();
				if (empty($ktOrders)) {
					accessDenied();
				}
			}
		}



        if ($order['status'] == "approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($order['hold_the_goods'] == 1) {
            $transfer = $this->site_model->getTransferWarehouse($id);
            if (!empty($transfer)) {
                set_alert('danger', lang('tnh_dghkts'));
                redirect($_SERVER["HTTP_REFERER"]);
                die;
            }
        }

        if ($this->input->post('edit')) {
            $data = [];
            if ($order['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required|is_unique[tbl_orders.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('type_orders', lang("tnh_type_orders"), 'required');
            $this->form_validation->set_rules('id_branch', lang("id_branch"), 'required');
            // $this->form_validation->set_rules('employees', lang("tnh_employees_charge"), 'required');
            if ($this->form_validation->run() == true) {
                // $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                // $customer = explode("__", $this->input->post('customers'));
                $type_customer = 'customers';
                $customer_id = $order['customer_id'];
                $address_delivery = $this->input->post('address_delivery');
                $employees = $this->input->post('employees') ? $this->input->post('employees') : 0;
                $note = $this->input->post('note');
                $cost_delivery = number_unformat($this->input->post('cost_delivery'));
                $transporters = $this->input->post('transporters');
                $charge_party = $this->input->post('charge_party');
                $id_branch = $this->input->post('id_branch') ? $this->input->post('id_branch') : null;
                $currencies = $this->input->post('currencies');
                $amount_to_vnd = number_unformat($this->input->post('amount_to_vnd'));
                $type_orders = $this->input->post('type_orders');
                $status_orders = $this->input->post('status_orders');
                $type_items = $this->input->post('type_items');
                $reference_no_customer = !empty($this->input->post('reference_no_customer')) ? $this->input->post('reference_no_customer') : null;
                $table_price_id = $this->input->post('table_price_id');

                // $person_contact_id = $this->input->post('person_contact');
                $person_contact_id = $this->input->post('person_contact') ? $this->input->post('person_contact') : 0;
                $person_contact_id = str_replace('customers__', '', $person_contact_id);
                $person_contact_id = str_replace('leads__', '', $person_contact_id);

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $tax_id = $this->input->post('tax_id') ? $this->input->post('tax_id') : 0;
                $tax_name = 0;
                $tax_rate = 0;
                $total_tax = 0;
                $discount_percent = $this->input->post('discount_percent') ? $this->input->post('discount_percent') : 0;
                $total_discount_percent = 0;
                $total_discount_direct = $this->input->post('discount_direct') ? number_unformat($this->input->post('discount_direct')) : 0;
                $discount_percent = 0;
                $total_discount_direct = 0;

                $so = $this->input->post('so');
                $pi = $this->input->post('pi');
                $po_style = $this->input->post('po_style');
                $item_code_tem = $this->input->post('item_code');

                $grand_total = 0;
                $status = 'un_approved';
                $total_cost_temporary_capital = 0;
                $total_profit_temporary_capital = 0;

                $counter = $this->input->post('counter');
                $itemsChild = $this->input->post('itemsChild');
                $itemsChildSize = $this->input->post('itemsChildSize');
                $itemsChildColumns = $this->input->post('itemsChildColumns');
                $counter_item = 0;
                $grand_total_quantity = 0;
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arrs = explode('__', $items_id);
                        $item_id = $arrs[0];
                        $type_item = $arrs[1];
                        $quantity_child_sheet = 0;
                        $quantity_sheet_bale = 0;

                        $unit_id = !empty($this->input->post('unit')[$value]) ? $this->input->post('unit')[$value] : 0;
                        $conversion_quantity_unit = 1;
                        $conversion_quantity_unit_default = 1;
                        if (empty($unit_id)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn đơn vị tính');
                            echo json_encode($data); die;
                        }

                        $info = null;
                        $loss = 0;
                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($item_id);
                            $quantity_child_sheet = $info['quantity_child_sheet'];
                            $quantity_sheet_bale = $info['quantity_sheet_bale'];
                            $loss = $info['loss'];
                            if ($unit_id != $info['unit_id']) {
                                $conversion_quantity_unit = $info['conversion_quantity_unit'];
                            }
                            $conversion_quantity_unit_default = $info['conversion_quantity_unit'];
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($item_id);
                        } else if ($type_item == "materials") {
                            $info = $this->items_model->rowMaterial($item_id);
                        }

                        if (empty($info)) {
                            continue;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];

                        // $order_code = $this->input->post('order_code')[$value];
                        // $command = $this->input->post('command')[$value];
                        // $quantity_loss = number_unformat($this->input->post('quantity_loss')[$value]);

                        $ct_counter_item = 0;
                        $arrItemsChildColumns = [];
                        $counter_items_number = 0;
                        $quantity = 0;
                        $total_quantity_loss = 0;
                        $total_quantity_sample = 0;
                        if ($type_orders == ORDER_CHANGE || $type_orders == ORDER_DEFAULT || $type_orders) {
                            if (!empty($itemsChildColumns[$value])) {
                                foreach ($itemsChildColumns[$value] as $kICC => $vICC) {
                                    $columns_id = !empty($vICC['columns_id']) ? $vICC['columns_id'] : NULL;
                                    $columns_value = !empty($vICC['columns_value']) ? $vICC['columns_value'] : NULL;
                                    $columns_name = !empty($vICC['columns_name']) ? $vICC['columns_name'] : NULL;
                                    if (!empty($columns_id)) {
                                        foreach ($columns_id as $kC => $vC) {
                                            $columns_name_item = !empty($columns_name[$kC]) ? $columns_name[$kC] : '';
                                            $columns_value_item = !empty($columns_value[$kC]) ? $columns_value[$kC] : '';
                                            if (empty($columns_name_item)) continue;
                                            $arrItemsChildColumns[] = [
                                                'counter_item' => $counter_item,
                                                'columns_id' => $vC,
                                                'columns_name' => $columns_name_item,
                                                'columns_value' => $columns_value_item,
                                                'counter_items_number' => $counter_items_number,
                                            ];
                                        }
                                    }

                                    //default
                                    $columns_value_date_ship = $vICC['columns_value_date_ship'];
                                    $date_ship = $vICC['date_ship'];
									
                                    $columns_value_order_code = $vICC['columns_value_order_code'];
                                    $order_code = $vICC['order_code'];
									
                                    $columns_value_command = $vICC['columns_value_command'];
                                    $command = $vICC['command'];

                                    $columns_value_quantity_put = $vICC['columns_value_quantity_put'];
                                    $quantity_put = number_unformat($vICC['quantity_put']);

                                    $columns_value_quantity_loss = $vICC['columns_value_quantity_loss'];
                                    $quantity_loss = number_unformat($vICC['quantity_loss']);

                                    $columns_value_sample_quantity_item = $vICC['columns_value_sample_quantity_item'];
                                    $sample_quantity_item = number_unformat($vICC['sample_quantity_item']);

                                    if (empty($date_ship)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập ngày giao');
                                        echo json_encode($data);
                                        die;
                                    }
									if (empty($order_code)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập mã đơn đặt');
                                        echo json_encode($data);
                                        die;
                                    }

                                    if (empty($command)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập chỉ lệnh');
                                        echo json_encode($data);
                                        die;
                                    }

                                    if (empty($quantity_put)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập số lượng đặt > 0');
                                        echo json_encode($data);
                                        die;
                                    }

                                    if ($quantity_loss === '') {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Vui lòng nhập số lượng loss');
                                        echo json_encode($data);
                                        die;
                                    }

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $date_ship,
                                        'columns_value' => $columns_value_date_ship,
                                        'counter_items_number' => $counter_items_number,
                                    ];
									
									$arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $order_code,
                                        'columns_value' => $columns_value_order_code,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $command,
                                        'columns_value' => $columns_value_command,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $quantity_put,
                                        'columns_value' => $columns_value_quantity_put,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $quantity_loss,
                                        'columns_value' => $columns_value_quantity_loss,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $arrItemsChildColumns[] = [
                                        'counter_item' => $counter_item,
                                        'columns_id' => 0,
                                        'columns_name' => $sample_quantity_item,
                                        'columns_value' => $columns_value_sample_quantity_item,
                                        'counter_items_number' => $counter_items_number,
                                    ];

                                    $quantity += $quantity_put;
                                    $total_quantity_loss += $quantity_loss;
                                    $total_quantity_sample += $sample_quantity_item;

                                    $counter_items_number++;
                                    $counter_item++;
                                }
                            }
                        }

                        $ct_counter_item = $counter_items_number;
                        // $sample_quantity =  number_unformat($this->input->post('sample_quantity')[$value]);
                        $sample_quantity =  $total_quantity_sample;
                        $note_item = $this->input->post('note_items')[$value];
                        // $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $price = number_unformat($this->input->post('price')[$value]);
                        $is_lot = !empty($this->input->post('is_lot')[$value]) ? 1 : 0;
                        if ($is_lot) {
                            $amount = $price;
                        } else {
                            $amount = $quantity * $price;
                        }

                        $total_quantity_item = $sample_quantity + $quantity + $total_quantity_loss;
                        if ($type_orders == TYPE_PTM) {
                            if ($total_quantity_item < QUANTITY_PTM) {
                                $data['result'] = 0;
                                $data['danger'] = 'Loại đơn hàng phát triển mẫu vui lòng tổng số lượng lớn hơn bằng 200';
                                echo json_encode($data); die;
                            }
                        }

                        $grand_total_quantity += $total_quantity_item;

                        $sub = [];
                        $date_sub = !empty($this->input->post('date_sub')[$value]) ? $this->input->post('date_sub')[$value] : false;
                        $total_quantity_sub = 0;
                        if (!empty($date_sub)) {
                            foreach ($date_sub as $k => $val) {
                                if (empty($val)) continue;

								if(strtotime(to_sql_date($val.' 23:59:59', true)) < strtotime($date)) {
									$data['result'] = 0;
									$data['message'] = 'Ngày giao hàng dự kiến không được nhỏ hơn ngày đơn hàng';
									echo json_encode($data);die();
								}

                                // $quantity_sub = number_unformat($this->input->post('quantity_sub')[$value][$k]);
                                $quantity_sub = $total_quantity_item;
                                $sub[] = [
                                    'date_shipping' => to_sql_date($val),
                                    'quantity_shipping' => $quantity_sub
                                ];
                                $total_quantity_sub += $quantity_sub;
                            }

                            if ($total_quantity_sub > $total_quantity_item) {
                                $data['result'] = 0;
                                $data['message'] = lang('tnh_check_date_enter');
                                echo json_encode($data);
                                die;
                            }
                        }

                        if (empty($sub)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn ngày giao hàng dự kiến');
                            echo json_encode($data);
                            die;
                        }

						$ship = !empty($this->input->post('ship')[$value]) ? $this->input->post('ship')[$value] : [];

                        $grand_total_item = $amount;
                        //tax item
                        $tax_id_item = $this->input->post('tax_item')[$value] ? $this->input->post('tax_item')[$value] : 0;
                        $tax_name_item = "0%";
                        $tax_rate_item = 0;
                        $tax_amount_item = 0;
                        if (!empty($tax_id_item)) {
                            $info_tax = $this->site_model->rowTax($tax_id_item);
                            if (!empty($info_tax)) {
                                $tax_name_item = $info_tax['name'];
                                $tax_rate_item = $info_tax['taxrate'];
                            }
                        }

                        //end
                        //discount percent item
                        $discount_percent_item = number_unformat($this->input->post('discount_percent_item')[$value]);
                        $discount_percent_amount_item = 0;
                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                            $total_discount_percent_items += $discount_percent_amount_item;
                            $grand_total_item -= $discount_percent_amount_item;
                        }
                        //end
                        $discount_direct_amount_item = number_unformat($this->input->post('discount_direct_item')[$value]);

                        $total_discount_direct_items += $discount_direct_amount_item;
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
                        $cost_temporary_capital = $priceCost;
                        $profit_temporary_capital = $grand_total_item - $priceCost;
                        //end handling cost temporary capital

                        if ($tax_rate_item > 0) {
                            $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                            $total_tax_items += $tax_amount_item;
                            $grand_total_item += $tax_amount_item;
                        }

                        $exchange = [];
                        if ($type_item == "products") {
                            $exchangeUnits = $this->products_model->getExchangeProductsByProductId($item_id);
                            if (!empty($exchangeUnits)) {
                                foreach ($exchangeUnits as $k => $val) {
                                    if (empty($val)) continue;
                                    $quantity_exchange = $val['number_exchange'];
                                    if (empty($quantity_exchange)) $quantity_exchange = 1;
                                    $total_quantity_exchange = $quantity / $quantity_exchange;
                                    $exchange[] = [
                                        'unit_id' => $val['unit_id'],
                                        'quantity_exchange' => $quantity_exchange,
                                        'total_quantity_exchange' => $total_quantity_exchange,
                                    ];
                                }
                            }
                        }

                        $arrChange = [];
                        if ($type_orders == ORDER_CHANGE) {
                            if (!empty($itemsChild[$value])) {
                                foreach ($itemsChild[$value] as $kC => $vC) {
                                    if (empty($vC['size'])) continue;
                                    $arrChange[] = [
                                        'id' => !empty($vC['id']) ? $vC['id'] : 0,
                                        'size ' => $vC['size'],
                                        'size_dc' => $vC['size_dc'],
                                        'style_number' => $vC['style_number'],
                                        'color' => $vC['color'],
                                        'quantity' => number_unformat($vC['quantity']),
                                    ];
                                }
                            }
                        }

                        $arrChangeSize = [];
                        if ($type_orders == ORDER_CHANGE_SIZE) {
                            if (!empty($itemsChildSize[$value])) {
                                foreach ($itemsChildSize[$value] as $kC => $vC) {
                                    if (empty($vC['number_size'])) continue;

                                    $quantity_child = number_unformat($vC['quantity']);
                                    $even_quantity = 0;
                                    $odd_quantity = 0;
                                    $quantity_sheet = 0;
                                    if ($quantity_child_sheet > 0) {
                                        $quantity_sheet = $quantity_child / $quantity_child_sheet;
                                        $even_quantity = floor($quantity_sheet);
                                        $quantity_ceil = ceil($quantity_sheet);
                                        $odd_quantity = $quantity_ceil - $even_quantity;
                                    }

                                    $even_quantity_bale = 0;
                                    $odd_quantity_bale = 0;
                                    $quantity_bale = 0;
                                    if ($quantity_sheet_bale > 0) {
                                        $quantity_bale = $quantity_child / $quantity_sheet_bale;
                                        $even_quantity_bale = floor($quantity_bale);
                                        $quantity_ceil_bale = ceil($quantity_bale);
                                        $odd_quantity_bale = $quantity_ceil_bale - $even_quantity_bale;
                                    }

                                    $arrChangeSize[] = [
                                        'id' => !empty($vC['id']) ? $vC['id'] : 0,
                                        'number_size' => $vC['number_size'],
                                        'quantity' => $quantity_child,
                                        'even_sheet' => $even_quantity,
                                        'odd_sheet' => $odd_quantity,
                                        'even_bale' => $even_quantity_bale,
                                        'odd_bale' => $odd_quantity_bale,
                                        'quantity_sheet' => $quantity_sheet,
                                        'quantity_bale' => $quantity_bale,
                                    ];
                                }
                            }
                        }

                        $hand_input_price = !empty($this->input->post('hand_input_price')[$value]) ? $this->input->post('hand_input_price')[$value] : 0;

                        $product_name_customer = !empty($this->input->post('product_name_customer')[$value]) ? $this->input->post('product_name_customer')[$value] : '';
                        if (empty($product_name_customer)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng nhập tên thành phẩm của khách hàng');
                            echo json_encode($data);
                            die;
                        }

                        $items[$key] = [
                            // 'order_code' => $order_code,
                            // 'command' => $command,
                            'quantity_loss' => $total_quantity_loss,
                            'sample_quantity' => $sample_quantity,
                            'total_quantity_item' => $total_quantity_item,

                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'tax_id_item' => $tax_id_item,
                            'tax_name_item' => $tax_name_item,
                            'tax_rate_item' => $tax_rate_item,
                            'tax_amount_item' => $tax_amount_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'note_item' => $note_item,
                            'cost_temporary_capital' => $cost_temporary_capital,
                            'profit_temporary_capital' => $profit_temporary_capital,
                            'sub' => $sub,
                            'exchange' => $exchange,
                            'arrChange' => $arrChange,
                            'arrChangeSize' => $arrChangeSize,
                            'quantity_child_sheet' => $quantity_child_sheet,
                            'quantity_sheet_bale' => $quantity_sheet_bale,
                            'arrItemsChildColumns' => $arrItemsChildColumns,
                            'ct_counter_item' => $ct_counter_item,
                            'hand_input_price' => $hand_input_price,
                            'loss' => $loss,
                            'product_name_customer' => $product_name_customer,
							'ship' => $ship,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                            'conversion_quantity_unit_default' => $conversion_quantity_unit_default,
                            'is_lot' => $is_lot
                        ];
                        if (!empty($this->input->post('order_item_id')[$value])) {
                            $items[$key]['id'] = $this->input->post('order_item_id')[$value];
                        }

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                        $total_cost_temporary_capital += $cost_temporary_capital;
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }
                $count_items = count($items);
                $grand_total = $grand_total_items;
                // print_arrays($grand_total);
                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }

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
                } else {
                    //công ty
                    // $total_profit_temporary_capital-= $cost_delivery;
                }
                //handing customer
                if ($type_customer == "customers") {
                    $row_customer = $this->site_model->rowCustomer($customer_id);
                    if (empty($row_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_customer_not_exist');
                        echo json_encode($data);
                        die;
                    }
                    $customer_name = $row_customer['company'];
                    $address_delivery_id = $address_delivery;
                } else if ($type_customer == "leads") {
                    $lead_id = $customer_id;
                    $convert_customer = convertCustomerLeadToCustomer($customer_id, $address_delivery);
                    if (empty($convert_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_customer_not_exist');
                        echo json_encode($data);
                        die;
                    }
                    $customer_id = $convert_customer['customer_id'];
                    $customer_name = $convert_customer['customer_name'];
                    $address_delivery_id = $convert_customer['address_delivery_id'];
                }

                //handling upload file
                $attachments_multiple_old = $order['attachments'];
                $attachments_multiple_old_form = $this->input->post('attachments_old[]');
                $this->load->library('upload');
                if (!empty($_FILES['files']) && !empty($_FILES['files']['size'])) {
                    $fileCount = count($_FILES['files']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $name = $_FILES['files']['name'][$i];
                        $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                        $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                        $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                        $config['upload_path'] = $this->upload_path;
                        $config['allowed_types'] = $this->types;
                        $config['file_name'] = $this->datetime_now . '_' . vn_to_str($name);
                        $config['overwrite'] = TRUE;
                        //$config['max_filename'] = 25;
                        $config['encrypt_name'] = false;
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('file')) {
                            $uploadData[$i] = $this->upload->file_name;
                        }
                    }
                }
                //end handling upload file

                //
                $orderSub = [];
                $arrData = [];
                if ($type_orders == TYPE_SAMPLE_ORDER || $type_orders == TYPE_KH_ORDER || $type_orders == TYPE_PTM) {
                    $date_quotes_chonse = date('Y-m-d H:i:s');
                    $staff_quotes_chonse = get_staff_user_id();
                    $quote_id_chonse = $this->input->post('quote_id_chonse');
                    if (empty($quote_id_chonse) && $type_orders == TYPE_SAMPLE_ORDER) {
                        // $data['result'] = 0;
                        // $data['message'] = lang('Đơn hàng mẫu vui lòng chọn báo giá mẫu');
                        // echo json_encode($data);
                        // die;
                    }

                    if ($quote_id_chonse) {
                        $orderSub = [
                            'order_id' => 0,
                            'quote_id_chonse' => $quote_id_chonse,
                            'date_quotes_chonse' => $date_quotes_chonse,
                            'staff_quotes_chonse' => $staff_quotes_chonse,
                        ];
                    }
                } else if ($type_orders == TYPE_COMPENSATE_ORDER) {
                    $orders_choose = $this->input->post('orders_choose');
                    $productions_orders_choose = $this->input->post('productions_orders_choose');

                    if (empty($orders_choose) && empty($productions_orders_choose)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đơn hàng bù vui lòng chọn bù cho đơn hàng hoặc bù cho LSX');
                        echo json_encode($data);
                        die;
                    }
                }

                //end
                $options = [
                    'date' => $date,
                    // 'reference_no' => $reference_no,
                    // 'customer_id' => $customer_id,
                    // 'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery_id,
                    'employee_id' => $employees,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'total_amount_items' => $total_amount_items,
                    'total_tax_items' => $total_tax_items,
                    'total_discount_percent_items' => $total_discount_percent_items,
                    'total_discount_direct_items' => $total_discount_direct_items,
                    'grand_total_items' => $grand_total_items,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                    'date_updated' => date('Y-m-d H:i'),
                    'updated_by' => get_staff_user_id(),
                    'cost_delivery' => $cost_delivery,
                    'transporter_id' => $transporters,
                    'charge_party' => $charge_party,
                    'person_contact_id' => $person_contact_id,
                    'total_cost_temporary_capital' => $total_cost_temporary_capital, //giá vốn tạm thời
                    'total_profit_temporary_capital' => $total_profit_temporary_capital, //chi phí lợi nhuận tạm thời
                    'id_branch' => $id_branch,
                    'currencies' => $currencies,
                    'amount_to_vnd' => $amount_to_vnd,
                    'type_orders' => $type_orders,
                    'status_orders' => $status_orders,
                    'type_items' => $type_items,
                    'grand_total_quantity' => $grand_total_quantity,
                    'so' => $so,
                    'pi' => $pi,
                    'po_style' => $po_style,
                    'item_code' => $item_code_tem,
                    'reference_no_customer' => $reference_no_customer,
                    'table_price_id' => $table_price_id
                ];

                if (!empty($uploadData)) {
                    if (!empty($attachments_multiple_old_form)) {
                        $options['attachments'] = implode('||', $attachments_multiple_old_form) . '||' . implode('||', $uploadData);
                    } else {
                        $options['attachments'] = implode('||', $uploadData);
                    }
                } else {
                    if (!empty($attachments_multiple_old_form)) {
                        $options['attachments'] = implode('||', $attachments_multiple_old_form);
                    } else {
                        $options['attachments'] = null;
                    }
                }
                // print_arrays($items);

                $up = $this->orders_model->updateOrdersNew($id, $options);
                if ($up) {
                    //delete items and shipping
                    $itemsOld = $this->orders_model->getOrderItemsByOrderId($id);
                    $this->orders_model->deleteOrdersItemsByOrderIdNotGift($id);
                    $this->orders_model->deleteOrderItemsSizeByOrderId($id);
                    $this->orders_model->deleteBatchOrderItemsChangeSize($id);
                    $this->orders_model->deleteOrderItemsColumns($id);
                    foreach ($itemsOld as $key => $value) {
                        $this->orders_model->deleteOrderItemShippings($value['id']);
                        $this->orders_model->getOrderItemExchange($value['id']);


						$this->db->where('order_item_id', $value['id']);
						$this->db->delete('tbl_orders_ship');
                    }
                    // end
                    // insert items and shipping
                    foreach ($items as $key => $value) {
                        $value['order_id'] = $id;
                        $sub = $value['sub'];
						$ship = $value['ship'];
                        $exchange = $value['exchange'];
                        $arrChange = $value['arrChange'];
                        $arrChangeSize = $value['arrChangeSize'];
                        $arrItemsChildColumns = $value['arrItemsChildColumns'];
                        unset($value['sub']);
                        unset($value['ship']);
                        unset($value['exchange']);
                        unset($value['arrChange']);
                        unset($value['arrChangeSize']);
                        unset($value['arrItemsChildColumns']);
                        $order_item_id = $this->orders_model->insertOrderItemsNew($value);
                        if ($order_item_id) {
                            if (!empty($sub)) {
                                foreach ($sub as $k => $val) {
                                    $val['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemShippingsNew($val);
                                }
                            }

							if (!empty($ship)) {
								foreach ($ship['date'] as $k => $val) {
                                    if(empty($ship['date'][$k])) continue;
									$this->db->insert('tbl_orders_ship', [
										'order_item_id' => $order_item_id,
										'date' => to_sql_date($ship['date'][$k]),
										'quantity' => number_unformat($ship['quantity'][$k]),
									]);
								}
							}

                            if (!empty($exchange)) {
                                foreach ($exchange as $k => $val) {
                                    $val['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemExchange($val);
                                }
                            }

                            if (!empty($arrChange)) {
                                foreach ($arrChange as $kC => $vC) {
                                    $arrChange[$kC]['order_id'] = $id;
                                    $arrChange[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsSize($arrChange);
                            }

                            if (!empty($arrChangeSize)) {
                                foreach ($arrChangeSize as $kC => $vC) {
                                    $arrChangeSize[$kC]['order_id'] = $id;
                                    $arrChangeSize[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsChangeSize($arrChangeSize);
                            }

                            if (!empty($arrItemsChildColumns)) {
                                foreach ($arrItemsChildColumns as $kC => $vC) {
                                    $arrItemsChildColumns[$kC]['order_id'] = $id;
                                    $arrItemsChildColumns[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsColumns($arrItemsChildColumns);
                            }
                        }
                    }

                    if ($this->input->post('remove_image')) {
                        foreach (explode('||', $attachments_multiple_old) as $key => $value) {
                            if (!empty($attachments_multiple_old_form)) {
                                if (!in_array($value, $attachments_multiple_old_form)) {
                                    if (file_exists($this->upload_path . '' . $value)) {
                                        @unlink($this->upload_path . '' . $value);
                                    }
                                }
                            } else {
                                if (file_exists($this->upload_path . '' . $value)) {
                                    @unlink($this->upload_path . '' . $value);
                                }
                            }
                        }
                    }


                    $this->orders_model->handlingStagesOrders($id);
                    //stages

                    if ($type_orders != TYPE_SAMPLE_ORDER) {
                        $this->orders_model->deleteOrdersSub($id);
                    }

                    if ($type_orders != TYPE_COMPENSATE_ORDER) {
                        $this->orders_model->deleteOrdersRelationship($id);
                    }

                    if (!empty($orderSub)) {
                        $orderSub['order_id'] = $id;
                        $rs = $this->orders_model->handlingOrdersSub($id, $orderSub);
                    }

                    if (!empty($orders_choose)) {
                        foreach ($orders_choose as $key => $value) {
                            $arrData[] = [
                                'order_id' => $id,
                                'type_relationship' => 2,
                                'object_id' => $value,
                            ];
                        }
                    }
        
                    if (!empty($productions_orders_choose)) {
                        foreach ($productions_orders_choose as $key => $value) {
                            $arrData[] = [
                                'order_id' => $id,
                                'type_relationship' => 1,
                                'object_id' => $value,
                            ];
                        }
                    }

                    if (!empty($arrData)) {
                        $this->orders_model->handlingOrdersRelationship($id, $arrData);
                    }
                    //

                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_his_edit_orders') . ' [' . $order['reference_no'] . ']',
                        'actions' => 'edit'
                    ]);

                    set_alert('success', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {

                    if (!empty($uploadData)) {
                        foreach ($uploadData as $key => $value) {
                            if (file_exists($this->upload_path . '' . $value)) {
                                @unlink($this->upload_path . '' . $value);
                            }
                        }
                    }

                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $data['taxs'] = $this->site_model->getTaxs();
        $data['size'] = $this->site_model->getSize();
        $data['colors'] = $this->site_model->getColors();

        $data['order'] = $order;

        $row_customer = $this->site_model->rowCustomer($order['customer_id']);
        $data['order']['customer_name'] = $row_customer['company_short'];
        $items = $this->orders_model->getOrderItemsByOrderId($id);
        $bodyItems = '';
        $counter = 0;
        $counter_child = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $strMoreProduct = '';

                $images = '';
                $size_name = '';
                $loss = '';
                $info = null;
                $mode_product = '';
                $name_customer = '';
                $arrUnit = [];
				$unit_cvs = [];
				$price_default = $this->orders_model->getPriceCustomer($order['table_price_id'], $order['customer_id'], $items_id, 'product', $value['quantity']);
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
	
	
//					$unit_cvs[$info['unit_id']] =  1;
					$unit_cvs[$info['conversion_unit']] =  $info['conversion_quantity_unit'];

                    $strMoreProduct = '
                        <div>SL con/tờ: <span class="quantity_child_sheet">' . $info['quantity_child_sheet'] . '</span></div>
                        <div>SL tờ/kiện: <span class="quantity_sheet_bale">' . $info['quantity_sheet_bale'] . '</div>
                    ';
                    $mode_product = $info['mode_product'];
                    $dtSize = $this->products_model->getSizeById($info['size']);
                    $size_name = !empty($dtSize) ? $dtSize['name'] : '';
                    $loss = $info['loss'];
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                    $name_customer = $info['product_name_customer'];

                    $unit_conversion = $this->unit_model->rowUnit($info['conversion_unit']);
                    $arrUnit[] = ['id' => $unit['unitid'], 'text' => $unit['unit']];
                    if ($unit['unitid'] != $unit_conversion['unitid']) {
                        $arrUnit[] = ['id' => $unit_conversion['unitid'], 'text' => $unit_conversion['unit']];
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                    $name_customer = $info['name_customer'];
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $sub_date = $this->orders_model->getOrderItemShippingsByOrderItemId($value['id']);
                $htmlShipping = '';
                if (!empty($sub_date)) {
                    foreach ($sub_date as $k => $val) {

                        // '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>' .
                        $htmlShipping .= '<div class="sb">' .
                            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub[' . $counter . '][]" id="input" autocomplete="off" class="form-control datepicker date_sub" placeholder="' . lang('date') . '" value="' . _d($val['date_shipping'], true) . '" style="width: 100%;" title=""></div>' .
                            '</div>';
                    }
                }

                // '<div class="col-md-5" style="padding: 0px;"><input type="text" style="width: 100%;" name="quantity_sub[' . $counter . '][]" id="input" class="form-control quantity_sub number-format" value="" title=""></div>' .
                //             '</div>';

                if (empty($htmlShipping)) {
                    $htmlShipping .= '<div class="sb">' .
                        '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub[' . $counter . '][]" id="input" autocomplete="off" class="form-control datepicker date_sub" placeholder="' . lang('date') . '" value="" style="width: 100%;" title=""></div></div>';
                }

                $optionTax = '<option value="0">' . lang('0%') . '</option>';
                if (!empty($data['taxs'])) {
                    foreach ($data['taxs'] as $k => $val) {
                        $selected = $val['id'] == $value['tax_id_item'] ? 'selected' : '';
                        $optionTax .= '<option ' . $selected . ' data-rate="' . $val['taxrate'] . '" value="' . $val['id'] . '">' . $val['name'] . '</option>';
                    }
                }

                // $typeWarehouse = $type_item == "products" ? "product" : 'items';
                $typeWarehouse = $type_item;
                if ($typeWarehouse == "products") {
                    $typeWarehouse = 'product';
                } else if ($typeWarehouse == "items") {
                    $typeWarehouse = 'items';
                } else if ($typeWarehouse == "materials") {
                    $typeWarehouse = 'nvl';
                }

                $quantityWarehouse = $this->site_model->getTotalQuantityWarehouseItems($items_id, $typeWarehouse)['total_quantity'];

                $htmlExchange = '';
                if ($type_item == "products") {
                    $exchange_units = $this->orders_model->getOrderItemExchange($value['id']);
                    if (!empty($exchange_units)) {
                        foreach ($exchange_units as $k => $val) {
                            $htmlExchange .= '<div class="list-exchange">
                                <input type="hidden" class="form-control number-exchange" value="' . $val['quantity_exchange'] . '">
                                <span>' . $val['unit_name'] . ': </span>
                                <span class="text-number-exchange">' . formatNumber($val['total_quantity_exchange']) . '</span>
                            </div>';
                        }
                    }
                }

                $strTypeItems = '';
                if ($type_item == "products") {
                    $strTypeItems = '<span class="label label-success">' . lang($type_item) . '</span>';
                } else if ($type_item == "items") {
                    $strTypeItems = '<span class="label label-primary">' . lang('ch_items') . '</span>';
                } else if ($type_item == "materials") {
                    $strTypeItems = '<span class="label label-warning">' . lang('materials') . '</span>';
                }

                $tdNumber = '<div class="stt text-center">' . (++$key) . '</div>';
                $tdNumber = '
                    <div class="text-right checkbox checkbox-info">
                        <input type="checkbox" name="checkbox_item['.$counter.']" id="checkbox_item_' . $counter . '" class="checkbox_item" value="1">
                        <label for="checkbox_item_' . $counter . '"></label>
                    </div>
                ';

                $tdOrderCode = '<div>
                    <input type="text" name="order_code[' . $counter . ']" placeholder="Mã đơn đặt" class="form-control order_code" value="' . $value['order_code'] . '">
                </div>';

                $tdCommand = '<div>
                    <input type="text" name="command[' . $counter . ']" placeholder="Chỉ lệnh" class="form-control command" value="' . $value['command'] . '">
                </div>';

                $tdCode = '<div class="td-code mbot10">
                    <input type="hidden" name="order_item_id[' . $counter . ']" class="form-control" value="' . $value['id'] . '">
                    <input type="hidden" name="counter[' . $counter . ']" id="counter" class="form-control counter" value="' . $counter . '">
                        <input type="text" name="items_id[' . $counter . ']" id="items_' . $counter . '" class="items_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $items_id . '__' . $type_item . '"></div>' .
                    '<div class="type-item">' . $strMoreProduct . '' . $strTypeItems . '</div>' .
                    '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row">' . lang('delete') . '</a></div></div>';
                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                    '<div class="display-block contract-attachment-wrapper img">' .
                    '<div style="width:45px; margin: auto;">' .
                    '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                    '<div class="">' .
                    '<img src="' . $images . '" style="border-radius: 50%">' .
                    '</div>' .
                    '</a>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>';
                // $tdName = '<div class="td-item-name">' . $name_customer . '</div>';
                $tdName = '<div class="">
                    <input type="text" placeholder="' . lang('tnh_product_name_customer') . '" name="product_name_customer[' . $counter . ']" class="form-control" value="' . $value['product_name_customer'] . '">
                </div>';
                $tdModeProduct = '<div class="td-mode-product text-center">' . $mode_product . '</div>';

                $optionUnit = '<option></option>';
                if (!empty($arrUnit)) {
                    foreach ($arrUnit as $k => $val) {
                        $optionUnit.= '<option data-conversion_quantity_unit="'.(!empty($unit_cvs[$val['id']]) ? $unit_cvs[$val['id']] : 1).'" '.($val['id'] == $value['unit_id'] ? 'selected' : '').' value="'.$val['id'].'">'.$val['text'].'</option>';
                    }
                }
                $tdUnit = '<div class="td-unit">
                    <select name="unit[' . $counter . ']" data-placeholder="ĐVT" id="unit_' . $counter . '" class="unit" style="width: 100%;">
                        '.$optionUnit.'
                    </select>
                </div>';

                $tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[' . $counter . ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="' . formatNumber($value['quantity']) . '"><div class="show-warehouses text-danger mtop10">' . lang('tnh_qty_warehoused') . ': ' . formatNumber($quantityWarehouse) . '</div><div class="show-exchange text-primary mtop5">' . $htmlExchange . '</span></div></div>';

                $tdQuantityLoss = '<div>
                    <input type="text" name="quantity_loss[' . $counter . ']" class="form-control quantity_loss" value="' . formatNumber($value['quantity_loss']) . '">
                </div>';
                $tdSampleQuantity = '<div>
                    <input type="hidden" name="quantity[' . $counter . ']" class="form-control quantity number-format" style="width: 100%;" value="' . $value['quantity'] . '">
                    <input type="text" name="sample_quantity[' . $counter . ']" readonly class="form-control sample_quantity" value="' . formatNumber($value['sample_quantity']) . '">
                </div>';

                $tdTotalQuantityPut = '<div class="td-total-quantity-put text-center">' . formatNumber($value['quantity']) . '</div>';

                $tdTotalQuantity = '<div class="td-total-quantity text-center">' . formatNumber($value['total_quantity_item']) . '</div>';

                $tdPrice = '<div class="td-price">
					<input type="hidden" name="price_default" class="form-control price_default" value="'.$price_default.'">
                    <input type="text" ' . ($value['hand_input_price'] ? '' : 'readonly') . ' name="price[' . $counter . ']" id="price[]" class="form-control price money-format" style="width: 100%;" value="' . formatMoney($value['price']) . '">
                    <div class="checkbox checkbox-info" style="margin-top: 5px;">
                        <input type="checkbox" ' . ($value['hand_input_price'] ? 'checked' : '') . ' id="hand_input_price_' . $counter . '" class="hand_input_price" name="hand_input_price[' . $counter . ']" value="1">
                        <label for="hand_input_price_' . $counter . '">Nhập tay</label>
                    </div>
                    <div class="checkbox checkbox-danger mtop5">
                        <input type="checkbox" name="is_lot[' . $counter . ']" '.($value['is_lot'] ? 'checked' : '').' onchange="totalOrders()" id="is_lot_' . $counter . '" class="is_lot" value="1">
                        <label for="is_lot_' . $counter . '">Giá theo lô</label>
                    </div>
                </div>';
                $tdTotalAmount = '<div class="td-total-amount text-right">' . formatMoney($value['amount']) . '</div>';
                $tdTaxItems = '<div class="td-tax">' .
                    '<select name="tax_item[' . $counter . ']" id="tax_item" class="tax_item" data-placeholder="' . lang('tax') . '" style="width: 100%;">' . $optionTax . '</select>' .
                    '</div>';
                $tdDisPercent = '<div class="td-dis-percent">' .
                    '<input type="text" name="discount_percent_item[' . $counter . ']" id="discount_percent_item" class="form-control discount_percent_item number-format" value="' . $value['discount_percent_item'] . '" style="width: 100%;">' .
                    '</div>';
                $tdDisDirect = '<div class="td-dis-direct">' .
                    '<input type="text" name="discount_direct_item[' . $counter . ']" id="discount_direct_item[]" class="form-control discount_direct_item money-format" style="width: 100px;" value="' . formatMoney($value['discount_direct_amount_item']) . '">' .
                    '</div>';
                $tdGrandTotal = '<div class="td-grand-total td-total-amount text-right">' . formatMoney($value['total_amount']) . '</div>';

                $tdSize = '<div class="text-center td-size">' . $size_name . '</div>';
                $tdLoss = '<div class="text-center td-loss">' . $loss . '</div>';

                // '<div style="display: inline-block;"><a class="pointer" onclick="addRowShipping(' . $counter . ', this)"><i class="fa fa-plus"></i> ' . lang('tnh_expected_date') . '</a></div>' .
                $tdShipping = '<div class="td-date">' .
                    '<div class="sub">' . $htmlShipping . '</div>' .
                    '<div class="text-danger show-errors"></div>' .
                    '</div>';


				$htmlDateShipping = '';
				$DateShipping = $this->db->get_where('tbl_orders_ship', ['order_item_id' => $value['id']])->result_array();
				if(!empty($DateShipping)) {
					foreach($DateShipping as $k => $v) {
						$htmlDateShipping .= '<div class="sb mtop5">
													<div class="col-md-12 input-group" style="padding: 0px; padding-right: 5px;">
														<input type="text" name="ship['.$counter.'][date][]" class="form-control datepicker date_ship" autocomplete="off" placeholder="Ngày" value="'._d($v['date']).'" style="width: 150px">
														<span class="input-group-addon" style="padding: 0 0;border: 0px solid black;">
															<input type="text" name="ship['.$counter.'][quantity][]" min="0" class="form-control quantity_ship" autocomplete="off" placeholder="Số lượng" value="'.($v['quantity']).'" style="width: 100px">
														</span>
														<span class="input-group-addon" style="padding-left: 5px;padding-right: 5px;border: 0px solid black;">
															<a class="btn btn-danger removeSubShip"><i class="fa fa-remove"></i></a>
														</span>
													</div>
												</div>';
					}
				}
				$tdDateShipping = '<div class="td-date-ship">' .
									'<div class="subShip" data-counter="'.$counter.'">' . $htmlDateShipping . '</div>' .
									'<div class="text-danger show-errors"></div>' .
									'<a class="btn createSubShip">
										<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
											<circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
												<path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
										</svg>
									</a>'.
								'</div>';
                $tdNote = '<div class="td-note"><textarea name="note_items[' . $counter . ']" id="note_items[]" class="form-control" rows="3">' . $value['note_item'] . '</textarea></div>';
                $tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

                // <td>' . $tdModeProduct . '</td>
                // <td>' . $tdDisPercent . '</td>
                // <td>' . $tdSize . '</td>

                $bodyItems .= '<tr>
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdCode . '</td>
                    <td>' . $tdImage . '</td>
                    <td>' . $tdName . '</td>
                    <td>' . $tdUnit . '</td>
                    <td>' . $tdSampleQuantity . '</td>
                    <td>' . $tdTotalQuantityPut . '</td>
                    <td>' . $tdTotalQuantity . '</td>
                    <td>' . $tdPrice . '</td>
                    <td>' . $tdGrandTotal . '</td>
                    <td>' . $tdLoss . '</td>
                    <td>' . $tdShipping . '</td>
                    <td>' . $tdDateShipping . '</td>
                    <td>' . $tdNote . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';

                if ($order['type_orders'] == ORDER_CHANGE) {
                    $orderItemsSize = $this->orders_model->getOrderItemsSize($value['id']);
                    if (!empty($orderItemsSize)) {
                        $trHtmlChild = '';
                        foreach ($orderItemsSize as $kS => $vS) {
                            $optionSize = '<option value=""></option><option value="0"></option>';
                            if (!empty($data['size'])) {
                                foreach ($data['size'] as $kSize => $vSize) {
                                    $selected = $vS['size'] == $vSize['id'] ? 'selected' : '';
                                    $optionSize .= '<option ' . $selected . ' value="' . $vSize['id'] . '">' . $vSize['name'] . '</option>';
                                }
                            }

                            $optionColor = '<option value=""></option><option value="0"></option>';
                            if (!empty($data['colors'])) {
                                foreach ($data['colors'] as $kColor => $vColor) {
                                    $selected = $vS['color'] == $vColor['id'] ? 'selected' : '';
                                    $optionColor .= '<option ' . $selected . ' value="' . $vColor['id'] . '">' . $vColor['name'] . '</option>';
                                }
                            }

                            $tdNumberChild = '<td></td>';
                            $tdSizeSPChild = '<td>
                                <input type="hidden" name="itemsChild[' . $counter . '][' . $counter_child . '][id]" class="form-control" value="' . $vS['id'] . '">
                                <select name="itemsChild[' . $counter . '][' . $counter_child . '][size]" data-placeholder="Size SP" id="size-child-' . $counter_child . '" style="width: 100%;" class="size_sp">
                                    ' . $optionSize . '
                                </select>
                            </td>';
                            $tdSizeDCChild = '<td>
                                <input type="text" name="itemsChild[' . $counter . '][' . $counter_child . '][size_dc]" placeholder="Size ĐC" class="form-control size_dc" value="' . $vS['size_dc'] . '">
                            </td>';
                            $tdSizeNumberChild = '<td>
                                <input type="text" name="itemsChild[' . $counter . '][' . $counter_child . '][style_number]" placeholder="Style Number" class="form-control style_number" value="' . $vS['style_number'] . '">
                            </td>';
                            $tdColorChild = '<td>
                                <select name="itemsChild[' . $counter . '][' . $counter_child . '][color]" data-placeholder="Color" id="color-' . $counter_child . '" style="width: 100%;" class="color">
                                    ' . $optionColor . '
                                </select>
                            </td>';
                            $tdQuantityChild = '<td>
                                <input type="text" name="itemsChild[' . $counter . '][' . $counter_child . '][quantity]" class="form-control number-format" value="' . formatNumber($vS['quantity']) . '">
                            </td>';
                            $tdActionsChild = '<td class="text-center">
                                <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
                            </td>';

                            $trHtmlChild .= '<tr tr-counter="' . $counter . '" class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $tdSizeSPChild . '
                                ' . $tdSizeDCChild . '
                                ' . $tdSizeNumberChild . '
                                ' . $tdColorChild . '
                                ' . $tdQuantityChild . '
                                ' . $tdActionsChild . '
                            </tr>';
                            $counter_child++;
                        }

                        $bodyItems .= '<tr id="tr-child-' . $counter . '" class="not-tr">
                            <td colspan="20">
                                <table class="table table-child" style="width: 50%; margin-left: 50px !important;">
                                    <thead>
                                        <tr class="not-tr">
                                            <th class="text-center" style="width: 50px;">
                                                <a href="javascript:void(0)" onclick="addChild(this, ' . $counter . ')"><i class="fa fa-plus"></i></a>
                                            </th>
                                            <th class="text-center" style="width: 120px;">Size SP</th>
                                            <th class="text-center" style="width: 120px;">Size ĐC</th>
                                            <th class="text-center" style="width: 120px;">Style Number</th>
                                            <th class="text-center" style="width: 120px;">Color</th>
                                            <th class="text-center" style="width: 100px;">Số lượng</th>
                                            <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody class="child">
                                        ' . $trHtmlChild . '
                                    </tbody>
                                </table>
                            </td>
                        </tr>';
                    }
                }

                //
                if ($order['type_orders'] == ORDER_CHANGE_SIZE) {
                    $orderItemsChangeSize = $this->orders_model->getOrderItemsChangeSizeByOrderItemId($value['id']);
                    if (!empty($orderItemsChangeSize)) {
                        $trHtmlChild = '';
                        foreach ($orderItemsChangeSize as $kS => $vS) {

                            $tdNumberChild = '<td></td>';

                            $tdNumberSizeChild = '<td>
                                <input type="hidden" name="itemsChildSize[' . $counter . '][' . $counter_child . '][id]" class="form-control" value="' . $vS['id'] . '">
                                <input type="text" name="itemsChildSize[' . $counter . '][' . $counter_child . '][number_size]" placeholder="Số Size" class="form-control" value="' . $vS['number_size'] . '">
                            </td>';

                            $tdQuantityChild = '<td>
                                <input type="text" name="itemsChildSize[' . $counter . '][' . $counter_child . '][quantity]" onchange="totalChildChangeSize(' . $counter . ')" class="form-control number-format quantity" placeholder="Số lượng" value="' . $vS['quantity'] . '">
                            </td>';

                            $tdEvenSheetChild = '<td class="even-sheet text-center">
                            </td>';

                            $tdOddSheetChild = '<td class="odd-sheet text-center">
                            </td>';

                            $tdEvenBaleChild = '<td class="even-bale text-center">
                            </td>';

                            $tdOddBaleChild = '<td class="odd-bale text-center">
                            </td>';

                            $tdActionsChild = '<td class="text-center">
                                <a href="javascript:void(0)" class="text-danger" onClick="removeChildChangeSize(this)"><i class="fa fa-remove"></i><a/>
                            </td>';

                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $tdNumberSizeChild . '
                                ' . $tdQuantityChild . '
                                ' . $tdEvenSheetChild . '
                                ' . $tdOddSheetChild . '
                                ' . $tdEvenBaleChild . '
                                ' . $tdOddBaleChild . '
                                ' . $tdActionsChild . '
                            </tr>';
                            $counter_child++;
                        }

                        $bodyItems .= '<tr id="tr-child-' . $counter . '" class="not-tr">
                                <td colspan="20">
                                <table class="table table-child-size table-child-size-' . $counter . '" style="width: 50%; margin-left: 50px !important;">
                                    <thead>
                                        <tr class="not-tr">
                                            <th class="text-center" style="width: 50px;">
                                                <a href="javascript:void(0)" onclick="addChildChangeSize(this, ' . $counter . ')"><i class="fa fa-plus"></i></a>
                                            </th>
                                            <th class="text-center" style="width: 120px;">Số Size</th>
                                            <th class="text-center" style="width: 120px;">Số lượng</th>
                                            <th class="text-center" style="width: 120px;">Tờ chẵn</th>
                                            <th class="text-center" style="width: 120px;">Tờ lẻ</th>
                                            <th class="text-center" style="width: 100px;">Kiện chẵn</th>
                                            <th class="text-center" style="width: 100px;">Kiện lẻ</th>
                                            <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody class="child">
                                        ' . $trHtmlChild . '
                                    </tbody>
                                </table>
                            </td>
                        </tr>';
                    }
                }

                if (($order['type_orders'] == ORDER_DEFAULT || $order['type_orders'] == ORDER_CHANGE || $order['type_orders']) && $type_item == "products") {
                    $productsColumns = $this->products_model->getProductsColumns($items_id);

                    $trHtmlChild = '';

                    $thSub = '';
                    $trAddChild = '';
                    $html_sub = '';
                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                    $ct_counter_item = $value['ct_counter_item'];
                    $trHtmlChild = '';
                    $trHtmlColumns = '';
                    if (!empty($productsColumns)) {
                        foreach ($productsColumns as $k => $v) {
                            $thSub .= '<th class="text-center" style="width:130px;">' . $v['name'] . '</th>';
                            $trAddChild .= '
                                <td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id][]" class="form-control" value="' . $v['id'] . '">
                                    <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value][]" class="form-control" value="' . $v['name'] . '">
                                    <input type="text" placeholder="' . $v['name'] . '" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_name][]" class="form-control" value="">
                                </td>
                            ';
                        }
                    }

                    if ($ct_counter_item > 0) {
                        for ($i = 0; $i < $ct_counter_item; $i++) {
                            $trHtmlColumns = '';
                            foreach ($productsColumns as $k => $v) {
                                $columns_name = '';
                                $isFlag = false;
                                foreach ($orderItemsColumns as $kO => $vO) {
                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                        $columns_name = $vO['columns_name'];
                                        $isFlag = true;
                                        break;
                                    }
                                }

                                $trHtmlColumns .= '
                                    <td>
                                        <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id][]" class="form-control" value="' . $v['id'] . '">
                                        <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value][]" class="form-control" value="' . $v['name'] . '">
                                        <input type="text" placeholder="' . $v['name'] . '" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_name][]" class="form-control" value="' . $columns_name . '">
                                    </td>
                                ';
                            }

                            $date_ship = '';
                            $order_code = '';
                            $command = '';
                            $quantity_put = '';
                            $quantity_loss = '';
                            $sample_quantity_item = '';
                            foreach ($orderItemsColumns as $kO => $vO) {
                                if ($vO['columns_value'] == 'date_ship' && $i == $vO['counter_items_number']) {
									$date_ship = $vO['columns_name'];
                                    continue;
                                } if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                    $order_code = $vO['columns_name'];
                                    continue;
                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                    $command = $vO['columns_name'];
                                    continue;
                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                    $quantity_put = $vO['columns_name'];
                                    continue;
                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                    $quantity_loss = $vO['columns_name'];
                                    continue;
                                } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                    $sample_quantity_item = $vO['columns_name'];
                                    continue;
                                }
                            }


                            $tdDateShipping = '<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id_date_ship]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value_date_ship]" class="form-control" value="date_ship">
                                <input type="text" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][date_ship]" placeholder="Ngày giao" class="datepicker form-control date_ship" value="' . $date_ship . '">
                            </td>';
							$tdOrderCode = '<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id_order_code]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value_order_code]" class="form-control" value="order_code">
                                <input type="text" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][order_code]" placeholder="Mã đơn đặt" class="form-control order_code" value="' . $order_code . '">
                            </td>';

                            $tdCommand = '<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id_command]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value_command]" class="form-control" value="command">
                                <input type="text" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][command]" placeholder="Chỉ lệnh" class="form-control command" value="' . $command . '">
                            </td>';

                            $tdQuantityPut = '<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id_quantity_put]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value_quantity_put]" class="form-control" value="quantity_put">
                                <input type="text" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][quantity_put]" class="form-control quantity_put number-format" style="width: 100%;" value="' . formatNumber($quantity_put) . '">
                            </td>';

                            $tdQuantityLoss = '<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id_quantity_loss]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value_quantity_loss]" class="form-control" value="quantity_loss">
                                <input type="text" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][quantity_loss]" class="form-control quantity_loss number-format" readonly style="width: 100%;" value="' . formatNumber($quantity_loss) . '">
                            </td>';

                            $tdSampleQuantityItem = '<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_id_sample_quantity_item]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][columns_value_sample_quantity_item]" class="form-control" value="sample_quantity_item">
                                <input type="text" name="itemsChildColumns[' . $counter . '][' . $counter_child . '][sample_quantity_item]" class="form-control sample_quantity_item number-format" style="width: 100%;" value="' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '">
                            </td>';


                            $counter_child++;
                            if (empty($trHtmlColumns) && empty($order_code)) continue;

                            $tdNumberChild = '<td></td>';
                            $tdActionsChild = '<td class="text-center">
                                <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
                            </td>';

                            $trHtmlChild .= '<tr tr-counter="' . $counter . '" class="not-tr tr-sub-items">
                                ' . $tdNumberChild . '
                                ' . $tdDateShipping . '
                                ' . $tdOrderCode . '
                                ' . $tdCommand . '
                                ' . $tdQuantityPut . '
                                ' . $tdQuantityLoss . '
                                ' . $tdSampleQuantityItem . '
                                ' . $trHtmlColumns . '
                                ' . $tdActionsChild . '
                            </tr>';
                        }
                    }

                    $html_sub .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
                    <thead>
                        <tr class="not-tr">
                            <th class="text-center" style="width: 50px;">
                                <a href="javascript:void(0)" onclick="addChild' . $counter . '(this, ' . $counter . ')"><i class="fa fa-plus"></i></a>
                            </th>
                            <th class="text-center" style="width: 150px;">' . lang('Ngày giao') . '<small class="req text-danger">*</small></th>
                            <th class="text-center" style="width: 150px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                            <th class="text-center" style="width: 150px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                            <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                            <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                            <th class="text-center" style="width: 100px;">' . lang('tnh_sample_quantity') . '<small class="req text-danger">*</small></th>
                            ' . $thSub . '
                            <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                        </tr>
                    </thead>
                        <tbody class="child">
                            ' . $trHtmlChild . '
                        </tbody>
                    </table>
                    <script>

                        function addChild' . $counter . '(_this, temp_counter) {
                            trChild = $(_this).parents("tr");
                            tdNumberChild = `<td></td>`;
                            tdActionsChild = `<td class="text-center">
                                <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
                            </td>`;

                            tdDateShip = `<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_date_ship]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_date_ship]" class="form-control" value="date_ship">
                                <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][date_ship]" placeholder="Mã đơn đặt" class="form-control date_ship" value="">
                            </td>`;
                            
                            tdOrderCode = `<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_order_code]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_order_code]" class="form-control" value="order_code">
                                <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][order_code]" placeholder="Mã đơn đặt" class="form-control order_code" value="">
                            </td>`;

                            tdCommand = `<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_command]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_command]" class="form-control" value="command">
                                <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][command]" placeholder="Chỉ lệnh" class="form-control command" value="">
                            </td>`;

                            tdQuantityPut = `<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_quantity_put]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_quantity_put]" class="form-control" value="quantity_put">
                                <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][quantity_put]" class="form-control quantity_put number-format" style="width: 100%;" value="0">
                            </td>`;

                            tdQuantityLoss = `<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_quantity_loss]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_quantity_loss]" class="form-control" value="quantity_loss">
                                <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][quantity_loss]" class="form-control quantity_loss number-format" readonly style="width: 100%;" value="0">
                            </td>`;

                            tdSampleQuantityItem = `<td>
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_id_sample_quantity_item]" class="form-control" value="0">
                                <input type="hidden" name="itemsChildColumns[' . $counter . '][${counter_child}][columns_value_sample_quantity_item]" class="form-control" value="sample_quantity_item">
                                <input type="text" name="itemsChildColumns[' . $counter . '][${counter_child}][sample_quantity_item]" class="form-control sample_quantity_item number-format" style="width: 100%;" value="0">
                            </td>`;

                            trHtmlChild = `<tr tr-counter="' . $counter . '" class="not-tr tr-sub-items">
                                ${tdNumberChild}
                                ${tdDateShip}
                                ${tdOrderCode}
                                ${tdCommand}
                                ${tdQuantityPut}
                                ${tdQuantityLoss}
                                ${tdSampleQuantityItem}
                                ' . $trAddChild . '
                                ${tdActionsChild}
                            </tr>`;
                            trChild.find(".table-child tbody").append(trHtmlChild);
                            counter_child++;
                            init_datepicker();
                        }

                        $(document).ready(function () {

                        });
                    </script>
                    ';

                    $bodyItems .= '<tr id="tr-child-' . $counter . '" class="not-tr">
                        <td colspan="20">
                            ' . $html_sub . '
                        </td>
                    </tr>';
                }
                $counter++;
            }
        }

        $data['status_orders'] = $this->status_orders_model->getStatusOrders();

        $data['type_items'] = $this->db->get('tbltype_orders_items')->result_array();
        $data['type_orders'] = $this->type_orders_model->getTypeOrders();
        $data['currencies'] = $this->site_model->getCurrencies();
        $data['branch'] = get_table_where('tblbranch');
        $data['quotes_note_default'] = $this->site_model->getQuotesNoteDefault();
        $data['counter'] = $counter;
        $data['counter_child'] = $counter_child;
        $data['bodyItems'] = $bodyItems;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['tnh'] = true;
        $data['title'] = lang('tnh_edit_order');
        $data['id'] = $id;
        $ckView = checkView('orders', $order['list_users'], $id);
        $data['table_price'] = $this->site_model->rowSetPricesById($order['table_price_id']);
        $data['table_discount'] = $this->site_model->rowDiscountById($order['table_discount_id']);
        $data['breadcrumb'] = [array('link' => base_url('admin/orders'), 'page' => lang('tnh_orders')), array('link' => '#', 'page' => lang('tnh_edit_order'))];
        $this->load->view('admin/orders/edit', $data);
    }

    public function getOrders()
    {
        $arrIDStaff = employee_manage_staff();
        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status_table = $this->input->post('status_table');
        $type_orders_search = $this->input->post('type_orders_search');
        $status_orders_search = $this->input->post('status_orders_search');
        $items_search = $this->input->post('items_search');

        $staff_id = get_staff_user_id();
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $delivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) as total_quantity_delivery
            FROM tbl_delivery_items
            WHERE tbl_delivery_items.id_import IS NOT NULL AND tbl_delivery_items.id_import != ''
            GROUP BY tbl_delivery_items.order_item_id
        ) dv";

        $orderItems = "(
            SELECT
                ot.order_id as order_id,
                ot.type_item as type_item,
                ot.item_id as item_id,
                SUM(ot.quantity_check) as quantity_check
            FROM (
                SELECT
                    tbl_order_items.order_id,
                    tbl_order_items.type_item,
                    tbl_order_items.item_id,
                    tbl_order_items.quantity - COALESCE(dv.total_quantity_delivery, 0) as quantity_check
                FROM tbl_order_items
                LEFT JOIN $delivery ON tbl_order_items.id = dv.order_item_id
            ) ot
            GROUP BY ot.order_id, ot.type_item, ot.item_id
        )";

        $warehouseTotal = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                IF(tblwarehouse_items.type_items = 'product', 'products', IF(tblwarehouse_items.type_items = 'nvl', 'materials', 'items')) as type_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
            FROM tblwarehouse_items
            WHERE (tblwarehouse_items.type_items = 'product' OR tblwarehouse_items.type_items = 'items' OR tblwarehouse_items.type_items = 'nvl')
            GROUP BY tblwarehouse_items.type_items, tblwarehouse_items.id_items
        )";

        $isQtyWarehouse = "(
            SELECT count(*)
            FROM $orderItems ott
            LEFT JOIN $warehouseTotal wt ON wt.id_items = ott.item_id AND wt.type_items = ott.type_item
            WHERE ott.order_id = tbl_orders.id AND ott.quantity_check > COALESCE(wt.quantity_warehouse, 0)
        )";

        $tbQtyWarehouse = "(
            SELECT
                ott.order_id as order_id,
                count(ott.order_id) as ct_order
            FROM $orderItems ott
            LEFT JOIN $warehouseTotal wt ON wt.id_items = ott.item_id AND wt.type_items = ott.type_item
            WHERE ott.quantity_check > COALESCE(wt.quantity_warehouse, 0)
            GROUP BY ott.order_id
        ) tb_qty";

        $countTransfer = "(
            SELECT
                tbltransfer_warehouse.order_id as order_id,
                count(tbltransfer_warehouse.id) as ct_transfer
            FROM tbltransfer_warehouse
            WHERE tbltransfer_warehouse.order_id > 0
            GROUP BY tbltransfer_warehouse.order_id
        ) tb_transfer";

		$ShippingOutTime = "(
            SELECT 1
            FROM tbl_order_item_shippings
            JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
            WHERE DATE_FORMAT(date_shipping, '%Y-%m-%d') <  ".date('Y-m-d')."
            AND tbl_order_items.order_id = tbl_orders.id
            LIMIT 1
        )";

        $aColumns = [
            'tbl_orders.id as id',
            'tbl_orders.date as date',
            'tbl_type_orders.name as name_type_orders',
            'tbl_orders.reference_no as reference_no',
            'tblclients.company_short as customer_name',
            'tblshipping_client.address as address_delivery',
            'tbl_orders.grand_total as grand_total',
            '(tbl_orders.grand_total * tbl_orders.amount_to_vnd) as grand_total_vnd',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
            'tbl_orders.status as status',
            'tbl_status_orders.name as status_orders',
            'tbl_orders.note as note',
            '"" as workflow_orders',
            'tbl_orders.type_bills as type_bills',
            'CONCAT(tbl_contracts_sales.prefix, "-", tbl_contracts_sales.code) as status_contracts',
            'tbl_orders.status_payment as status_payment',
            'tblbranch.name as name_branch',
            '"" as actions',
            'tbl_orders.id as id_sort',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_orders';
        $where        = [];
        $filter = [];

        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            // 'LEFT JOIN '.$tbQtyWarehouse.' ON tb_qty.order_id = tbl_orders.id',
            'LEFT JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            'LEFT JOIN tbl_status_orders ON tbl_status_orders.id = tbl_orders.status_orders',
            'LEFT JOIN tblshipping_client ON tblshipping_client.id = tbl_orders.address_delivery_id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_orders.created_by',
            'LEFT JOIN tbl_contracts_sales ON tbl_contracts_sales.id = tbl_orders.contract_id',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_orders.id_branch',
            'LEFT JOIN tblstaff staff_status ON staff_status.staffid = tbl_orders.user_status',
            'LEFT JOIN ' . $countTransfer . ' ON tb_transfer.order_id = tbl_orders.id',
        ];

        if (!empty($items_search)) {
            $items_search = explode('__', $items_search);
            array_push($where, 'AND EXISTS (
                SELECT tbl_order_items.order_id
                FROM tbl_order_items
                WHERE tbl_order_items.order_id = tbl_orders.id
                AND tbl_order_items.item_id = ' . $items_search[0] . '
            )');
        }

        if (!empty($orders_search)) {
            array_push($where, "AND tbl_orders.id = " . $this->db->escape($orders_search));
        }

        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_orders.date <= '" . $end_date_search . "'");
        }

        if (!$this->perViewOrders) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                array_push($where, 'AND (tbl_orders.created_by IN (' . $coverStr . ') OR tbl_orders.employee_id IN (' . $coverStr . '))');
            }
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
            array_push($where, "AND tbl_orders.status_orders = " . $status_orders_search . "");
        }

        if ($status_table != 'all') {
            if ($status_table == "un_approved" || $status_table == "approved") {
                array_push($where, "AND tbl_orders.status = '" . $status_table . "'");
            } else {
                if ($status_table == "gh") {
                    array_push($where, "AND tbl_orders.count_delivery > 0");
                } else if ($status_table == "lkhsx") {
                    $isPlan = "(
                        SELECT
                            tbl_productions_plan.id as id
                        FROM tbl_productions_plan_items
                        INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id
                        WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.object_id = tbl_orders.id
                    )";
                    array_push($where, "AND exists ($isPlan)");
                } else if ($status_table == "dsxtcty") {
                    $isPo = "(
                        SELECT
                            tbl_productions_plan_orders.id
                        FROM tbl_productions_plan_orders
                        INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
                        WHERE tbl_productions_plan_orders.productions_plan_id = tbl_orders.id AND tbl_productions_plan_orders.object_type = 'orders'
                    )";
                    array_push($where, "AND exists ($isPo)");
                } else if ($status_table == "sxx") {
                    $isPP = "(
                        SELECT
                            tbl_purchase_products.id as id
                        FROM tbl_productions_orders_details
                        INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                        WHERE tbl_productions_orders_details.object_id = tbl_orders.id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_purchase_products.final_stage = 1
                    )";
                    array_push($where, "AND exists ($isPP)");
                } else if ($status_table == "gh") {
                    $isDelivery = "(
                        SELECT
                            tbl_deliveries.id as id,
                            tbl_deliveries.reference_no
                        FROM tbl_orders_deliveries
                        INNER JOIN tbl_deliveries ON tbl_orders_deliveries.delivery_id = tbl_deliveries.id
                        WHERE tbl_orders_deliveries.order_id = tbl_orders.id
                    )";
                    array_push($where, "AND exists ($isDelivery)");
                } else if ($status_table == "xgcn") {
                    $isOutsource = "(
                        SELECT
                            tbl_outsource.id as id,
                            tbl_outsource.reference_no
                        FROM tbl_outsource_items
                        INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
                        WHERE tbl_outsource_items.object_type = 'orders' AND tbl_outsource_items.order_id = tbl_orders.id
                    )";
                    array_push($where, "AND exists ($isOutsource)");
                } else if ($status_table == "ngcn") {
                    $isImportOutsource = "(
                        SELECT
                            tbl_import_outsource.id as id,
                            tbl_import_outsource.reference_no
                        FROM tbl_import_outsource_items
                        INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
                        WHERE tbl_import_outsource_items.object_type = 'orders' AND tbl_import_outsource_items.order_id = tbl_orders.id
                    )";
                    array_push($where, "AND exists ($isImportOutsource)");
                } else if ($status_table == "ghc") {
                    $ghc = "(
                        SELECT
                            tbl_tranfer_business_item.id as id
                        FROM tbl_tranfer_business_item
                        WHERE tbl_tranfer_business_item.order_id = tbl_orders.id
                    )";
                    array_push($where, "AND exists ($ghc)");
                }
				else if ($status_table == "out_time_ship") {
                    // array_push($where, "AND exists ((
                    //     SELECT 1
                    //     FROM tbl_order_item_shippings
                    //     JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
                    //     WHERE
                    //         tbl_order_items.order_id = tbl_orders.id
                    //         AND
                    //         (
                    //             (
                    //                 DATE_FORMAT(date_shipping, '%Y-%m-%d') <  ".date('Y-m-d')."
                    //                 AND tbl_order_items.quantity > tbl_order_items.quantity_delivery
                    //             )
                    //             OR
                    //             (
                    //                 SELECT MAX(DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d'))
                    //                 FROM tbl_deliveries
                    //                 WHERE tbl_deliveries.order_id = tbl_order_items.order_id
                    //             ) > DATE_FORMAT(date_shipping, '%Y-%m-%d')
                    //         )
                    //     LIMIT 1
                    // ))");
					array_push($where, "AND exists ((
                        SELECT 1
                        FROM tbl_order_item_shippings
                        JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
                        WHERE
                            tbl_order_items.order_id = tbl_orders.id
                            AND DATE_FORMAT(date_shipping, '%Y-%m-%d') <  '".date('Y-m-d')."'
                            AND tbl_order_items.quantity > (SELECT
                                SUM(tblorders_ship.quantity_shipping)
                                FROM tbl_order_item_shippings tblorders_ship
                                WHERE tblorders_ship.order_item_id = tbl_order_items.id
                            )
                        LIMIT 1
                    ))");
                }
            }
        }

		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
				if (!empty($list_branch)) {
					$where[] = 'AND (tbl_orders.id_branch IN (' . $list_branch . '))';
				} else {
					$where[] = 'AND tbl_orders.id = 0';
				}
			}
		}

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'CONCAT(staff_status.firstname, " ", staff_status.lastname) as user_status',
            'tbl_type_orders.color as color_type_orders',
            'tb_transfer.ct_transfer as status_transfer',
            'tbl_status_orders.color as color_status_orders',
            'tbl_orders.is_cancel as is_cancel',
            'tblclients.is_separate_guest as is_separate_guest',
            'tbl_type_orders.id as type_orders',
            'tbl_orders.is_end as is_end',
			($ShippingOutTime .' as ShippingOutTime')
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $grand_total = 0;
        $grand_total_vnd = 0;

        $order_ids = [];
        if (!empty($rResult)) {
            foreach ($rResult as $key => $value) {
                $order_ids[] = $value['id'];
            }
        }

        if (!empty($order_ids)) {
            $this->db->select('tbltasks.id, tbltasks.order_id', false);
            $this->db->from('tbltasks');
            $this->db->where_in('tbltasks.order_id', $order_ids);
            $tasks = $this->db->get()->result_array();
            if (!empty($tasks)) {
                $tasks = array_reduce($tasks, function($carry, $item) {
                    $carry[$item['order_id']][] = $item;
                    return $carry;
                }, []);
            }
        }

        foreach ($rResult as $key => $aRow) {
			$row = [];
            $start++;
            $order_id = $aRow['id'];
            $user_status = $aRow['user_status'];
            $status = $aRow['status'];
            $color_type_orders = $aRow['color_type_orders'];
            $color_status_orders = $aRow['color_status_orders'];
            $type_orders = $aRow['type_orders'];
            $is_end = $aRow['is_end'];
            $aRow['grand_total'] = $aRow['grand_total_vnd'];

            $dtOrderSub = null;
            $strPassFail = '';
            $pass_fail = '';
            $choose_quotes = '';
            $strQuoteChoose = '';
            $choose_order_manu = '';

            if ($type_orders == TYPE_SAMPLE_ORDER) {
                $dtOrderSub = $this->orders_model->getOrdersSubById($order_id);
                $pass_fail = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/pass_fail/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-check"></i> ' . lang('tnh_pass_fail') . '</a>' : '';

                $choose_quotes = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/choose_quotes/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> ' . lang('tnh_choose_quotes') . '</a>' : '';

                if (!empty($dtOrderSub)) {
                    if ($dtOrderSub['is_pass_fail'] == 1) {
                        $strPassFail = '<div class="text-center" style="margin-top: 5px;"><span class="label label-primary">'.lang('Đạt').'</span></div>';
                    } else if ($dtOrderSub['is_pass_fail'] == 2) {
                        $strPassFail = '<div class="text-center" style="margin-top: 5px;"><span class="label label-danger">'.lang('Không đạt').'</span></div>';
                    } else {
                        $strPassFail = '';
                    }

                    if ($dtOrderSub['quote_id_chonse'] != 0) {
                        $dtQuote = get_table_where('tbl_quotes', ['id' => $dtOrderSub['quote_id_chonse']], '', 'row_array', '', 'reference_no');
                        $strQuoteChoose = '<div class="text-center mtop5">BG mẫu: '.$dtQuote['reference_no'].'</div>';
                    }
                }
            } else if ($type_orders == TYPE_KH_ORDER || $type_orders == TYPE_PTM) {

                $dtOrderSub = $this->orders_model->getOrdersSubById($order_id);
                $choose_quotes = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/choose_quotes/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> ' . lang('tnh_choose_quotes') . '</a>' : '';
                if (!empty($dtOrderSub)) {
                    if ($dtOrderSub['quote_id_chonse'] != 0) {
                        $dtQuote = get_table_where('tbl_quotes', ['id' => $dtOrderSub['quote_id_chonse']], '', 'row_array', '', 'reference_no');
                        $strQuoteChoose = '<div class="text-center mtop5">BG mẫu: '.$dtQuote['reference_no'].'</div>';
                    }
                }

            } else if ($type_orders == TYPE_COMPENSATE_ORDER) {
                $choose_order_manu = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/choose_order_manu/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-bars"></i> ' . lang('tnh_choose_order_manu') . '</a>' : '';
            }

            $row[0] = $aRow['id'];
            $row[1] = $aRow['date'];
            $row[2] = '<div class="text-center"><span class="btn" style="background: ' . $color_type_orders . '; color: white; cursor: auto;">' . $aRow['name_type_orders'] . '<span></div>'.$strPassFail.$strQuoteChoose;
            $status_transfer = $aRow['status_transfer'];

            $this->db->from('tbl_tranfer_business_item');
            $this->db->where('tbl_tranfer_business_item.order_id', $order_id);
            $tranfer_business_item = $this->db->count_all_results();

            if ($status_transfer > 0 || $tranfer_business_item > 0) {
                $keepWarehouses = '<div class="text-left mtop5">
                    <a href="' . base_url('admin/orders/view_keep_warehouses/' . $order_id) . '" data-tnh="modal" class="tnh-modal" data-toggle="modal" data-target="#myModal"><span class="label label-success">' . lang('Đã giữ kho') . '</span></a>
                </div>';
            } else {
                $keepWarehouses = '<div class="text-left mtop5"><span class="label label-warning">' . lang('Chưa giữ kho') . '</span></div>';
            }
            $link = '<div style="min-width: 100px;" class="">
                <a data-tnh="modal" class="tnh-modal" href="' . base_url() . 'admin/orders/view_order/' . $order_id . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a>
                </div>
            ';

            $is_cancel = $aRow['is_cancel'];
            $str_cancel = '';
            if ($is_cancel) {
                $str_cancel = '<div class="mtop5 text-danger">'.lang('tnh_cancelled_order').'</div>';
            }

            if ($is_end) {
                $str_cancel = '<div class="mtop5 text-success"><span class="">'.lang('Đã kết thúc đơn hàng').'</span></div>';
            }

            $task = $tasks[$order_id] ?? [];
            $_html_task = '';
            if (!empty($task)) {
                $html_task = [];
                foreach ($task as $kT => $vT) {
                    $html_task[] = '<a href="javascript:void(0)" onclick="init_task_modal('.$vT['id'].')">'.$vT['id'].'<a/>';
                }
                $_html_task = '<div class="mtop5 text-muted"><i class="fa fa-tasks"></i> CV: ' . implode(', ', $html_task) . '</div>';
            }

            $row[3] = $link .(!empty($aRow['name_branch']) ? ('<i style="font-size: 11px;padding-bottom:5px; ">Chi nhánh: '.$aRow['name_branch'].'</i><br/>') : ''). $keepWarehouses.$str_cancel.$_html_task;
            $row[4] = $aRow['customer_name'] . (!empty($aRow['company_short']) ? ('(' . $aRow['company_short'] . ')') : '');
            $row[5] = $aRow['address_delivery'];
            $row[6] = $aRow['grand_total'];
            $row[7] = $aRow['grand_total_vnd'];
            $row[8] = $aRow['created_by'];
            $row[9] = $aRow['status'] . '__' . $user_status;
            $row[10] = '<div class="text-center"><span class="btn" style="background: ' . $color_status_orders . '; color: white; cursor: auto;">' . $aRow['status_orders'] . '<span></div>';
            $row[11] = $aRow['note'];

            //khsx
            $plan = "(
                SELECT
                    tbl_productions_plan.id as id,
                    tbl_productions_plan.reference_no
                FROM tbl_productions_plan_items
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id
                WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.object_id = $order_id
                GROUP BY tbl_productions_plan.id
            )";
            $dtPlan = $this->db->query($plan)->result_array();
            $planActive = '';
            $linkPlan = '';
            if (!empty($dtPlan)) {
                $planActive = 'active';
                foreach ($dtPlan as $k => $val) {
                    $linkPlan .= '<div><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/manufactures/view_productions_plan/' . $val['id']) . '" data-toggle="modal" data-target="#myModal">' . $val['reference_no'] . '</a></div>';
                }
            }

            $po = "(
                SELECT
                    tbl_productions_orders.id as id,
                    tbl_productions_orders.reference_no
                FROM tbl_productions_plan_orders
                INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
                WHERE tbl_productions_plan_orders.productions_plan_id = $order_id AND tbl_productions_plan_orders.object_type = 'orders'
            )";
            $dtPO = $this->db->query($po)->result_array();
            $dsxActive = '';
            $linkDsx = '';
            if (!empty($dtPO)) {
                $dsxActive = 'active';
                foreach ($dtPO as $k => $val) {
                    $linkDsx .= '<div><a target="_blank" href="' . base_url('admin/manufactures/detail_productions_orders/' . $val['id']) . '">' . $val['reference_no'] . '</a></div>';
                }
            }

            //purchases
            $pp = "(
                SELECT
                    tbl_purchase_products.id as id,
                    tbl_purchase_products.reference_no
                FROM tbl_productions_orders_details
                INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                WHERE tbl_productions_orders_details.object_id = $order_id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_purchase_products.final_stage = 1
            )";
            $dtPP = $this->db->query($pp)->result_array();
            $nkActive = '';
            $linkNk = '';
            if (!empty($dtPP)) {
                $nkActive = 'active';
                foreach ($dtPP as $k => $val) {
                    $linkNk .= '<div><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/stock/view_purchase_product/' . $val['id']) . '" data-toggle="modal" data-target="#myModal">' . $val['reference_no'] . '</a></div>';
                }
            }

            //outsource
            $outsource = "(
                SELECT
                    tbl_outsource.id as id,
                    tbl_outsource.reference_no
                FROM tbl_outsource_items
                INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
                WHERE tbl_outsource_items.object_type = 'orders' AND tbl_outsource_items.order_id = $order_id
                GROUP BY tbl_outsource.id
            )";
            $dtOutsource = $this->db->query($outsource)->result_array();
            $outsourceActive = '';
            $linkOutsource = '';
            if (!empty($dtOutsource)) {
                $outsourceActive = 'active';
                foreach ($dtOutsource as $k => $val) {
                    $linkOutsource .= '<div><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/outsource/view_outsource/' . $val['id']) . '" data-toggle="modal" data-target="#myModal">' . $val['reference_no'] . '</a></div>';
                }
            }

            //import
            $import = "(
                SELECT
                    tbl_import_outsource.id as id,
                    tbl_import_outsource.reference_no
                FROM tbl_import_outsource_items
                INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
                WHERE tbl_import_outsource_items.object_type = 'orders' AND tbl_import_outsource_items.order_id = $order_id
                GROUP BY tbl_import_outsource.id
            )";
            $dtImport = $this->db->query($import)->result_array();
            $importActive = '';
            $linkImport = '';
            if (!empty($dtImport)) {
                $importActive = 'active';
                foreach ($dtImport as $k => $val) {
                    $linkImport .= '<div><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/outsource/view_import_outsource/' . $val['id']) . '" data-toggle="modal" data-target="#myModal">' . $val['reference_no'] . '</a></div>';
                }
            }

            //delivery

            $delivery = "(
                SELECT
                    tbl_deliveries.id as id,
                    tbl_deliveries.reference_no
                FROM tbl_orders_deliveries
                INNER JOIN tbl_deliveries ON tbl_orders_deliveries.delivery_id = tbl_deliveries.id
                WHERE tbl_orders_deliveries.order_id = $order_id
            )";
            $dtDelivery = $this->db->query($delivery)->result_array();
            $ghActive = '';
            $linkGh = '';
			$statusGH = false;
            $linkGhNew = '<span class="inline-block label label-danger">Chưa giao</span>';
            if (!empty($dtDelivery)) {
                $this->db->select('COUNT(*) as total');
                $this->db->from('tbl_order_items');
                $this->db->where('tbl_order_items.order_id', $order_id);
                $this->db->where('tbl_order_items.quantity > tbl_order_items.quantity_delivery');
                $checkDelivery = $this->db->get()->row_array();
                if (!empty($checkDelivery)) {
                    if ($checkDelivery['total'] >= 0) {
                        if ($checkDelivery['total'] == 0) {
                            $linkGhNew = '<span class="inline-block label label-primary">Giao đủ</span>';
							$statusGH = true;
                        } else if ($checkDelivery['total'] > 0) {
                            $linkGhNew = '<span class="inline-block label label-warning">Giao 1 phần</span>';
                        }
                    }
                }
                $ghActive = 'active';
                foreach ($dtDelivery as $k => $val) {
                    $linkGh .= '<div><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/view_delivery/' . $val['id']) . '" data-toggle="modal" data-target="#myModal">' . $val['reference_no'] . '</a></div>';
                }
            }
            $ordersBusiness = $this->orders_model->getOrdersBusiness($order_id);

            $sample_production_active = '';
            $link_sample_production = '';

            if ($ordersBusiness) {
                $sample_production_active = 'active';
                $link_sample_production = '<div><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/business_plan/view_business_plan/' . $ordersBusiness['id']) . '" data-toggle="modal" data-target="#myModal">' . $ordersBusiness['reference_no'] . '</a></div>';
            }

            // <li class="'.$outsourceActive.'">
            //         <div>'.lang('tnh_xgcn').'</div>
            //         <div>'.$linkOutsource.'</div>
            //     </li>
            //     <li class="'.$importActive.'">
            //         <div>'.lang('tnh_ngcn').'</div>
            //         <div>'.$linkImport.'</div>
            //     </li>

            // <li class="' . $sample_production_active . '">
            //         <div>' . lang('tnh_sample_production') . '</div>
            //         <div>' . $link_sample_production . '</div>
            //     </li>
			$max_day = 0;
			if(!empty($aRow['ShippingOutTime']) && empty($statusGH)){
				$row['DT_RowClass'] = '_bg-danger';

				$max_day = $this->db->query("SELECT MAX(DATEDIFF( ".date('Y-m-d').", DATE_FORMAT(date_shipping, '%Y-%m-%d'))) as day
									FROM tbl_order_item_shippings
									JOIN tbl_order_items ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
									WHERE DATE_FORMAT(date_shipping, '%Y-%m-%d') < ".date('Y-m-d')."
									AND tbl_order_items.order_id = ".$order_id."
								LIMIT 1"
				)->row('day');
			}

            $process = '<ul class="progressbar" style="display: flex;">
                
                <li class="' . $planActive . '">
                    <div>' . lang('tnh_lkhsx') . '</div>
                    <div>' . $linkPlan . '</div>
                </li>
                <li class="' . $dsxActive . '">
                    <div>' . lang('tnh_dsxtcty') . '</div>
                    <div>' . $linkDsx . '</div>
                </li>
                <li class="' . $nkActive . '">
                    <div>' . lang('tnh_sxx') . '</div>
                    <div>' . $linkNk . '</div>
                </li>
                <li class="' . $ghActive . '">
                    <div>' . lang('tnh_gh') . '</div>
                    <div>' . $linkGhNew . '</div>
                    <div>' . $linkGh . '</div>
                    <div>' . ((!empty($aRow['ShippingOutTime']) && empty($statusGH)) ? '<span class="inline-block label label-warning mtop5" style="font-size: 9px;">Quá hạn GH '.$max_day.' ngày</span>' : '') . '</div>
                </li>
            </ul>';


            $row[12] = $process;
            $row[13] = $aRow['type_bills'];
            $row[14] = $aRow['status_contracts'];
            $row[15] = $aRow['status_payment'];
            $row[16] = $aRow['name_branch'];


            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/view_order/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('tnh_order') . '</a>';

            $edit = $this->perEditOrders ? '<a href="' . base_url('admin/orders/edit/' . $order_id) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('tnh_order') . '</a>' : '';
            // $qrcode = $this->perPrintOrders ? '<a onclick="print_qr_code('.$order_id.')" target="_blank"><i class="fa fa-barcode"></i> ' . lang('c_print_basecode') . '</a>' : '';

            $print_tem = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/print_tem/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-print"></i> ' . lang('In tem') . '</a>';

            $print = $this->perPrintOrders ? '<a href="' . base_url('admin/orders/print_orders/' . $order_id) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_order') . '</a>' : '';

            //$print_detail = $this->perPrintOrders ? '<a href="' . base_url('admin/orders/print_orders_detail/' . $order_id) . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_order') . lang(' chi tiết') . '</a>' : '';
            $print_detail = $this->perPrintOrders ? '<a onclick="print_pdf_detail('.$order_id.')"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_order') . lang(' chi tiết') . '</a>' : '';
            $export_excel_colums_detail = $this->perPrintOrders ? '<a onclick="exportExcelColumsDetail('.$order_id.')"><i class="fa fa-file-excel-o"></i> ' . lang('Xuất') . ' ' . lang('tnh_order') . lang(' chi tiết') . '</a>' : '';

            $convertDelivery = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-convert-delivery" href="' . base_url('admin/orders/convert_delivery/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-truck"></i> ' . lang('convert_delivery') . '</a>' : '';

            $convert_contract = $this->perAddOrders ? '<a  data-tnh="modal" class="tnh-modal cvc" href="' . base_url('admin/quotes/convert_contract/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-exchange"></i> ' . lang('tnh_convert_contract') . '</a>' : '';
            $convert_contract = '';
            $addPayment = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-add-payment" href="' . base_url('admin/orders/add_payment/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> ' . lang('tnh_payment') . '</a>' : '';

            $delete = $this->perDeleteOrders ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/orders/deleteOrders/' . $order_id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('tnh_order') . '</a>' : '';

            $sample_production = $this->perAddOrders ? '<a href="' . base_url('admin/business_plan/add?order_id=' . $order_id) . '" target="_blank"><i class="fa fa-plus"></i> ' . lang('tnh_add_sample_production') . '</a>' : '';

            $sample_production = '';

            $addHold = '<a data-tnh="modal" class="tnh-modal dt-add-hold" href="' . base_url('admin/orders/add_hold_orders/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-cube"></i> ' . lang('Giữ kho') . '</a>';

            $addHoldMore = '<a data-tnh="modal" class="tnh-modal dt-add-hold" href="' . base_url('admin/orders/add_hold_orders/' . $order_id.'/1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-cube"></i> ' . lang('Giữ kho thêm') . '</a>';

            $update_date_ship = '<a class="c_modal" href="' . base_url('admin/orders/update_date_ship/' . $order_id) . '"><i class="fa fa-calendar"></i> ' . lang('Sửa ngày giao hàng dự kiến') . '</a>';

            $cancel = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/cancel/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i> ' . lang('tnh_cancel_order') . '</a>' : '';

            $end = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/cancel/' . $order_id.'/1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-check"></i> ' . lang('Kết thúc đơn hàng') . '</a>' : '';

            $remove_cancel = '';
            
            if ($is_cancel) {
                $cancel = '';
                $end = '';
                $remove_cancel = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/remove_cancel/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-refresh"></i> ' . lang('tnh_remove_cancel_order') . '</a>' : '';
            }

            if ($is_end) {
                $cancel = '';
                $remove_cancel = '';
                $end = $this->perEditOrders ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/remove_cancel/' . $order_id.'/1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash-o"></i> ' . lang('Bỏ kết thúc đơn hàng') . '</a>' : '';
            }

            if (!empty($ordersBusiness)) {
                $sample_production = '';
            }

            if ($status != "approved") {
                $convertDelivery = '';
                $addPayment = '';
                $convert_contract = '';
                $sample_production = '';
            }
            $add_colum_delivery = '';
            $convertDeliveryNew = '';
            if ($aRow['is_separate_guest'] == 1) {
                $convertDelivery = '';
                $add_colum_delivery = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/add_colum_delivery/'.$aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('Thêm cột giao hàng') . '</a>';
                $convertDeliveryNew = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-convert-delivery" href="' . base_url('admin/orders/convert_delivery_new/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-truck"></i> ' . lang('convert_delivery').' hiện size' . '</a>' : '';
            }

            $add_task = $this->perAddOrders ? '<a href="javascript:void(0)" onclick="new_task(\''.base_url('admin/tasks/task?order_id=' . $order_id).'\'); return false;"><i class="fa fa-plus"></i> ' . lang('Tạo công việc') . '</a>' : '';

            $create_ptm = '';
            if ($aRow['type_orders'] == 11 && !empty($dtQuote['reference_no'])) {
                $create_ptm = '<li><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/ptm/create_modal/' . $order_id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_create_ptm') . '</a></li>';
            }

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    ' . $create_ptm . '
                    <li>' . $addHold . '<li>
                    <li>' . $addHoldMore . '<li>
                    <li>' . $print . '<li>
                    <li>' . $print_detail . '<li>
                    <li>' . $export_excel_colums_detail . '<li>
                    <li>' . $print_tem . '</li>
                    <li>' . $add_colum_delivery . '</li>
                    <li>' . $convertDelivery . '<li>
                    <li>' . $convertDeliveryNew . '<li>
                    <li>' . $convert_contract . '</li>
                    <li>' . $sample_production . '</li>
                    <li>' . $addPayment . '<li>
                    <li>' . $update_date_ship . '<li>
                    <li>' . $cancel . '<li>
                    <li>' . $pass_fail . '<li>
                    <li>' . $choose_quotes .'</li>
                    <li>' . $choose_order_manu .'<li>
                    <li>' . $remove_cancel . '<li>
                    <li>' . $end . '<li>
                    <li>' . $add_task . '<li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[17] = $actions;
            $row[18] = $order_id;

            $grand_total += $aRow['grand_total'];
            $grand_total_vnd += $aRow['grand_total_vnd'];
            $output['aaData'][] = $row;
        }

        $output['grand_total'] = $grand_total;
        $output['grand_total_vnd'] = $grand_total_vnd;
        echo json_encode($output);
    }

    public function getOrdersOld()
    {
        // hoàng crm bổ xung
        $arrIDStaff = employee_manage_staff();
        // end

        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status_table = $this->input->post('status_table');

        $staff_id = get_staff_user_id();
        $ckView = "(
            SELECT FIND_IN_SET($staff_id, tbl_orders.list_users)
        )";

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $khsx = "(
            SELECT count(*)
            FROM tbl_productions_plan_items
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
            LIMIT 1
        )";

        $khsxText = "(
            SELECT GROUP_CONCAT(DISTINCT(CONCAT('<a data-tnh=\"modal\" class=\"tnh-modal\" href=\"" . base_url('admin/manufactures/view_productions_plan/') . "', tbl_productions_plan.id, '?view=seen\" data-toggle=\"modal\" data-target=\"#myModal\">', tbl_productions_plan.reference_no, '</a>')) SEPARATOR '</br>')
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
        )";

        $lsxct = "(
            SELECT count(*)
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_orders ON tbl_productions_plan_items.productions_plan_id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_plan_orders.productions_order_id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
            LIMIT 1
        )";

        $lsxctText = "(
            SELECT GROUP_CONCAT(DISTINCT(CONCAT('<a target=\"_blank\" href=\"" . base_url('admin/manufactures/detail_productions/') . "', tbl_productions_orders_details.id, '?view=seen\">', tbl_productions_orders_details.reference_no, '</a>')) SEPARATOR '</br>')
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_orders ON tbl_productions_plan_items.productions_plan_id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_plan_orders.productions_order_id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
        )";


        $sxx = "(
            SELECT count(*)
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_orders ON tbl_productions_plan_items.productions_plan_id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_plan_orders.productions_order_id
            INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
            LIMIT 1
        )";

        $sxxText = "(
            SELECT GROUP_CONCAT(DISTINCT(CONCAT('<a data-tnh=\"modal\" class=\"tnh-modal\" href=\"" . base_url('admin/stock/view_purchase_product/') . "', tbl_purchase_products.id, '?view=seen\" data-toggle=\"modal\" data-target=\"#myModal\">', tbl_purchase_products.reference_no, '</a>')) SEPARATOR '</br>')
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_orders ON tbl_productions_plan_items.productions_plan_id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_plan_orders.productions_order_id
            INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
        )";

        $xkgh = "(
            SELECT COUNT(*)
            FROM tbl_orders_deliveries
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_orders_deliveries.delivery_id
            WHERE tbl_orders_deliveries.order_id = tbl_orders.id AND tbl_deliveries.count_export_warehouse = 1
            LIMIT 1
        )";

        $xkghText = "(
            SELECT GROUP_CONCAT(DISTINCT(CONCAT('<a data-tnh=\"modal\" class=\"tnh-modal\" href=\"" . base_url('admin/releases/view_export_warehouse/') . "', tbl_export_warehouses.id, '?view=seen\" data-toggle=\"modal\" data-target=\"#myModal\">', tbl_export_warehouses.reference_no, '</a>')) SEPARATOR '</br>')
            FROM tbl_orders_deliveries
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_orders_deliveries.delivery_id
            INNER JOIN tbl_export_warehouses ON tbl_export_warehouses.delivery_id = tbl_deliveries.id
            WHERE tbl_orders_deliveries.order_id = tbl_orders.id
        )";

        $xkghText = 0;

        $xgc = "(
            SELECT COUNT(*)
            FROM tbl_outsource
            INNER JOIN tbl_orders_outsource ON tbl_outsource.id = tbl_orders_outsource.outsource_id
            WHERE tbl_orders_outsource.order_id = tbl_orders.id AND workflow > 0
            LIMIT 1
        )";

        $xgcText = "(
            SELECT GROUP_CONCAT(DISTINCT(CONCAT('<a data-tnh=\"modal\" class=\"tnh-modal\" href=\"" . base_url('admin/outsource/view_outsource/') . "', tbl_outsource.id, '?view=seen\" data-toggle=\"modal\" data-target=\"#myModal\">', tbl_outsource.reference_no, '</a>')) SEPARATOR '</br>')
            FROM tbl_outsource
            INNER JOIN tbl_orders_outsource ON tbl_outsource.id = tbl_orders_outsource.outsource_id
            WHERE tbl_orders_outsource.order_id = tbl_orders.id AND workflow > 0
            LIMIT 1
        )";

        $ngc = "(
            SELECT COUNT(*)
            FROM tbl_outsource
            INNER JOIN tbl_orders_outsource ON tbl_outsource.id = tbl_orders_outsource.outsource_id
            WHERE tbl_orders_outsource.order_id = tbl_orders.id AND workflow > 1
            LIMIT 1
        )";

        $ngcText = "(
            SELECT GROUP_CONCAT(DISTINCT(CONCAT('<a data-tnh=\"modal\" class=\"tnh-modal\" href=\"" . base_url('admin/outsource/view_import_outsource/') . "', tbl_import_outsource.id, '?view=seen\" data-toggle=\"modal\" data-target=\"#myModal\">', tbl_import_outsource.reference_no, '</a>')) SEPARATOR '</br>')
            FROM tbl_outsource
            INNER JOIN tbl_orders_outsource ON tbl_outsource.id = tbl_orders_outsource.outsource_id
            INNER JOIN tbl_import_outsource ON tbl_import_outsource.outsource_id = tbl_outsource.id
            WHERE tbl_orders_outsource.order_id = tbl_orders.id AND workflow > 1
        )";

        $delivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) as total_quantity_delivery
            FROM tbl_delivery_items
            WHERE tbl_delivery_items.id_import IS NOT NULL AND tbl_delivery_items.id_import != ''
            GROUP BY tbl_delivery_items.order_item_id
        ) dv";

        $orderItems = "(
            SELECT
                ot.order_id as order_id,
                ot.type_item as type_item,
                ot.item_id as item_id,
                SUM(ot.quantity_check) as quantity_check
            FROM (
                SELECT
                    tbl_order_items.order_id,
                    tbl_order_items.type_item,
                    tbl_order_items.item_id,
                    tbl_order_items.quantity - COALESCE(dv.total_quantity_delivery, 0) as quantity_check
                FROM tbl_order_items
                LEFT JOIN $delivery ON tbl_order_items.id = dv.order_item_id
            ) ot
            GROUP BY ot.order_id, ot.type_item, ot.item_id
        )";

        $warehouseTotal = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                IF(tblwarehouse_items.type_items = 'product', 'products', IF(tblwarehouse_items.type_items = 'nvl', 'materials', 'items')) as type_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
            FROM tblwarehouse_items
            WHERE (tblwarehouse_items.type_items = 'product' OR tblwarehouse_items.type_items = 'items' OR tblwarehouse_items.type_items = 'nvl')
            GROUP BY tblwarehouse_items.type_items, tblwarehouse_items.id_items
        )";

        $isQtyWarehouse = "(
            SELECT count(*)
            FROM $orderItems ott
            LEFT JOIN $warehouseTotal wt ON wt.id_items = ott.item_id AND wt.type_items = ott.type_item
            WHERE ott.order_id = tbl_orders.id AND ott.quantity_check > COALESCE(wt.quantity_warehouse, 0)
        )";


        $orderItemDelivery = "(
            SELECT COUNT(*)
            FROM tbl_order_items
            WHERE tbl_order_items.order_id = tbl_orders.id AND tbl_order_items.quantity > tbl_order_items.quantity_delivery
            LIMIT 1
        )";

        $statusDelivery = "(
            IF (tbl_orders.count_delivery > 0, $orderItemDelivery, -1)
        )";

        $transfer = "(
            SELECT CONCAT(tbltransfer_warehouse.prefix, '-', tbltransfer_warehouse.code)
            FROM tbltransfer_warehouse
            WHERE tbltransfer_warehouse.order_id = tbl_orders.id
        )";

        $this->db->dbprefix = '';
        $this->datatables->select("
            tbl_orders.id as id,
            tbl_orders.date as date,
            CONCAT(tbl_orders.reference_no, '___', COALESCE($transfer, '')) as reference_no,
            tblclients.company as customer_name,
            tblshipping_client.address as address_delivery,
            tbl_orders.grand_total as grand_total,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            tbl_orders.status as status,
            CONCAT(staff_status.firstname, ' ', staff_status.lastname, '') as user_status,
            $ckView as list_users,
            $isQtyWarehouse as status_warehouses,
            tbl_orders.type_bills as type_bills,
            tbl_orders.note as note,
            CONCAT(COALESCE($khsxText, ''), '||', COALESCE($lsxctText, ''), '||', COALESCE($sxxText, ''), '||', COALESCE($xkghText, '-1'), '||', COALESCE($xgcText, ''), '||', COALESCE($ngcText, ''), '||', COALESCE($statusDelivery, '')) as workflow_orders,
            CONCAT(tbl_contracts_sales.prefix, '-', tbl_contracts_sales.code) as status_contracts,
            tbl_orders.status_payment as status_payment,
            tblbranch.name as name_branch
            ", FALSE)

            ->from('tbl_orders')
            ->join('tblshipping_client', 'tblshipping_client.id = tbl_orders.address_delivery_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_orders.created_by', 'left')
            ->join('tbl_contracts_sales', 'tbl_contracts_sales.id = tbl_orders.contract_id', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left')
            ->join('tblstaff staff_status', 'staff_status.staffid = tbl_orders.user_status', 'left');

        if ($status_table != 'all') {
            if ($status_table == "un_approved" || $status_table == "approved") {
                $this->datatables->where('tbl_orders.status', $status_table);
            } else {
                if ($status_table == "lkhsx") {
                    $this->datatables->where("$khsx >", 0);
                } else if ($status_table == "dsxtcty") {
                    $this->datatables->where("$lsxct >", 0);
                } else if ($status_table == "sxx") {
                    $this->datatables->where("$sxx >", 0);
                } else if ($status_table == "xgcn") {
                    $this->datatables->where("$xgc >", 0);
                } else if ($status_table == "ngcn") {
                    $this->datatables->where("$ngc >", 0);
                } else if ($status_table == "gh") {
                    $this->datatables->where("tbl_orders.count_delivery >", 0);
                } else if ($status_table == "xkgh") {
                    $this->datatables->where("$xkgh >", 0);
                }
            }
        }

        if (!empty($orders_search)) {
            $this->datatables->where('tbl_orders.id', $orders_search);
        }

        if (!empty($customer_search)) {
            $this->datatables->where('tbl_orders.customer_id', $customer_search);
        }

        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }
        //hoàng crm bổ xung
        if (!$this->perViewOrders) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->datatables->where('tbl_orders.created_by IN (' . $coverStr . ') OR tbl_orders.employee_id IN (' . $coverStr . ')');
            }
        }
        //end

        $custom[] = ['index' => 2, 'select' => 'reference_no'];
        $custom_select[2] = "CONCAT(tbl_orders.reference_no, '___', COALESCE($transfer, ''))";
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/orders/view_order/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('tnh_order') . '</a>';

        $edit = $this->perEditOrders ? '<a href="' . base_url('admin/orders/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('tnh_order') . '</a>' : '';
        $qrcode = $this->perPrintOrders ? '<a onclick="print_qr_code($1)" target="_blank"><i class="fa fa-barcode"></i> ' . lang('c_print_basecode') . '</a>' : '';

        $print = $this->perPrintOrders ? '<a href="' . base_url('admin/orders/print_orders/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_order') . '</a>' : '';

        $convertDelivery = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-convert-delivery" href="' . base_url('admin/orders/convert_delivery/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-truck"></i> ' . lang('convert_delivery') . '</a>' : '';

        $convert_contract = $this->perAddOrders ? '<a  data-tnh="modal" class="tnh-modal cvc" href="' . base_url('admin/quotes/convert_contract/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-exchange"></i> ' . lang('tnh_convert_contract') . '</a>' : '';

        $addPayment = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal tnh-add-payment" href="' . base_url('admin/orders/add_payment/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-money"></i> ' . lang('tnh_payment') . '</a>' : '';

        $delete = $this->perDeleteOrders ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/orders/deleteOrders/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('tnh_order') . '</a>' : '';

        $addProductionPlanning = $this->perAddOrders ? '<a class="tnh-modal tnh-add-production-plan" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url('admin/manufactures/add_production_planning_orders/$1') . '"><i class="fa fa-plus"></i> ' . lang('tnh_add_production_planning') . '</a>' : '';

        // $addPurchase = $this->perAddOrders ? '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/orders/add_purchase/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> '.lang('tnh_pruchases_items').'</a>' : '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li>' . $qrcode . '</li>
                <li>' . $print . '<li>
                <li>' . $convertDelivery . '<li>
                <li>' . $convert_contract . '</li>
                <li>' . $addPayment . '<li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // foreach ($data->aaData as $key => $value) {
        //     // $data->aaData[$key][0] = ++$iDisplayStart;
        // }
        echo json_encode($data);
    }

    public function refereshReferenceOrders()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $reference_no = getReference('orders');
            if ($this->orders_model->checkExistOrders($reference_no)) {
                $ct = countReferenceMinus('orders');
                $this->db->select("MAX(right(tbl_orders.reference_no, char_length(tbl_orders.reference_no) - $ct) + 0) as reference_no", false);
                $this->db->from('tbl_orders');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                // $max = subReference($max);
                updateReferenceNormal('orders', $max);
                $reference_no = getReference('orders');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    public function view_order($id)
    {
        if (!$this->perViewOrders && !$this->perViewOwnOrders) {
            accessDenied($js = true);
        }
        $order = $this->orders_model->rowOrderById($id);
        if (!$this->perViewOrders) {
            checkMyData($order['created_by'], true);
        }
		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_orders.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tbl_orders.id = 0', false, false);
				}
				$this->db->where('id', $id);
				$ktOrders = $this->db->get('tbl_orders')->row();
				if (empty($ktOrders)) {
					accessDenied($js = true);
				}
			}
		}

        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $items = $this->orders_model->getOrderItemsByOrderId($id);
        $table_price = $this->site_model->rowSetPricesById($order['table_price_id']);
        $table_discount = $this->site_model->rowDiscountById($order['table_discount_id']);
        $ckView = checkView('orders', $order['list_users'], $id);
        $data['flagView'] = $ckView;
        $person_contact = $this->site_model->rowContact($order['person_contact_id']);

        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $images = '';
                $info = null;
                $mode_product = '';
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    // $unit = $this->unit_model->rowUnit($info['unit_id']);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                    $mode_product = $info['mode_product'];

                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                }

                if (!empty($value['image_product'])) {
                    $images = base_url('download/preview_image?path=uploads/products/' . $value['id'] . '/' . $value['image_product']);
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $sub_date = $this->orders_model->getOrderItemShippingsByOrderItemId($value['id']);
                $html_sub_date = '';
                if (!empty($sub_date)) {
                    foreach ($sub_date as $k => $val) {
//                        $html_sub_date .= '<div class="">' . _d($val['date_shipping']) . ' - ' . number_format($val['quantity_shipping']) . '</div>';
                        $html_sub_date .= '<div class="text-center">' . _d($val['date_shipping']) . '</div>';
                    }
                }

                $exchange = $this->orders_model->getOrderItemExchangeView($value['id']);
                $html_exchange = '';
                if (!empty($exchange)) {
                    foreach ($exchange as $k => $val) {
                        $html_exchange .= '<div class="">' .
                            '<div class="col-md-12" style="padding: 0px;">' . $val['unit_name'] . ' - ' . $val['quantity_exchange'] . '(' . formatNumber($val['total_quantity_exchange']) . ')</div>' .
                            '</div>';
                    }
                }

                $strTypeItems = '';
                if ($type_item == "products") {
                    $strTypeItems = '<span class="label label-success">' . lang($type_item) . '</span>';
                } else if ($type_item == "items") {
                    $strTypeItems = '<span class="label label-primary">' . lang('ch_items') . '</span>';
                } else if ($type_item == "materials") {
                    $strTypeItems = '<span class="label label-warning">' . lang('materials') . '</span>';
                }

                $tdNumber = '<td class="details-control">' . (++$key) . '</td>';
                $tdOrderCode = '<td class="text-center">' . $value['order_code'] . '</td>';
                $tdCommand = '<td class="text-center">' . $value['command'] . '</td>';

                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdCode = '<td>' . $info['code'] . '' . '<div class="type-item">' . $strTypeItems . '</div></td>';
                $tdName = '<td>' . $value['product_name_customer'] . '</td>';

                $tdModeProduct = '<td class="text-center">' . $mode_product . '</td>';

                $tdUnit = '<td class="text-center">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $tdQuantityLoss = '<td class="text-center">' . formatNumber($value['quantity_loss']) . '</td>';
                $tdSampleQuantity = '<td class="text-center">' . formatNumber($value['sample_quantity']) . '</td>';
                $tdTotalQuantityItems = '<td class="text-center">' . formatNumber($value['total_quantity_item']) . '</td>';

                $tdUnitPrice = '<td class="text-right">
                    ' . formatMoney($value['price']) . '
                    '.($value['is_lot'] == 1 ? '<div class="text-danger">Giá theo lô</div>' : '').'
                </td>';
                $tdTotalAmount = '<td class="text-right">' . formatMoney($value['amount']) . '</td>';
                $tdTaxItem = '<td class="text-center">' . $value['tax_name_item'] . '</td>';
                $tdDiscountPercent = '<td class="text-center">' . $value['discount_percent_item'] . '</td>';
                $tdDiscountDirect = '<td class="text-right">' . formatMoney($value['discount_direct_amount_item']) . '</td>';
                $tdGrandTotal = '<td class="text-right">' . formatMoney($value['total_amount']) . '</td>';
                $tdShipping = '<td>' . $html_sub_date . '</td>';

				$html_ship_date = '';
				$this->db->where('order_item_id', $value['id']);
				$orders_ship = $this->db->get('tbl_orders_ship')->result_array();
				if(!empty($orders_ship)) {
					foreach($orders_ship as $k => $v) {
						$html_ship_date .= '<div class="row_update">
												<div class="view_data"><span class="date">'._d($v['date']).'</span> - <span class="quantity">'.($v['quantity']).'</span> <a onclick="viewEdit(this)"><i class="fa fa-edit"></i></a></div>
												<div class="input_data col-md-12 hide input-group mtop5" style="padding: 0px; padding-right: 5px;">
													<input type="text" name="date" data-value="'._d($v['date']).'" class="form-control datepicker date_ship" autocomplete="off" placeholder="Ngày" value="'._d($v['date']).'" style="width: 150px">
													<span class="input-group-addon" style="padding: 0 0;border: 0px solid black;">
														<input type="text" name="quantity"  data-value="'.($v['quantity']).'" class="form-control quantity_ship" min="0" autocomplete="off" placeholder="Số lượng" value="'.($v['quantity']).'" style="width: 100px">
														<input type="hidden" name="id" class="form-control id" value="'.$v['id'].'">
													</span>
													<span class="input-group-addon" style="padding-left: 5px;padding-right: 5px;border: 0px solid black;">
														<a class="btn btn-info" title="Lưu" onclick="submitFrom(this)"><i class="fa fa-edit"></i></a>
														<a class="btn btn-danger" onclick="hideEdit(this)"><i class="fa fa-remove"></i></a>
													</span>
												</div>
											</div>';
					}
				}
                $tdDateShipping = '<td>' . $html_ship_date . '</td>';


                $tdUnitExchange = '<td>' . $html_exchange . '</td>';
                $tdNote = '<td>' . $value['note_item'] . '</td>';

                $workflow = '';
                if ($type_item == "products") {
                    $itemsProcess = $this->site_model->getProductionsOrdersItemsStages('orders', $value['id']);
                    if (!empty($itemsProcess)) {
                        foreach ($itemsProcess as $kk => $vv) {
                            $li = '';
                            $process = $vv['process'];
                            if (!empty($process)) {
                                foreach ($process as $kkk => $vvv) {
                                    $li .= '<li ' . ($vvv['active'] ? 'class="active"' : '') . '>' . $vvv['stage_name'] .
                                        (!empty($vvv['staff_active']) ? ('<p class="active_poin">' . ('Được ' . get_staff_full_name($vvv['staff_active']) . ($vvv['date_active'] ? ' duyệt vào lúc: ' . _dt($vvv['date_active']) : '')) . '</p>') : '')
                                        . '</li>';
                                }
                            }

                            $workflow .= '<div style="display: table; justify-content: center;">
                                <div class="pull-left mtop20"><span class="label label-primary">' . $vv['reference_no'] . ' (SL: ' . formatNumber($vv['quantity']) . ')</span></div>
                                <ul class="progressbar" style="display: flex;">
                                ' . $li . '
                                </ul>
                            </div>';
                        }
                    } else {
                        $workflow = '<div class="text-danger italic">' . lang('tnh_no_productions_orders_yet') . '</div>';
                    }
                } else {
                    $workflow = '';
                }

                //
                $htmlOrderChange = '';
                if ($order['type_orders'] == ORDER_CHANGE) {
                    $orderItemsSize = $this->orders_model->getOrderItemsSizeView($value['id']);
                    if (!empty($orderItemsSize)) {
                        $trHtmlChild = '';
                        foreach ($orderItemsSize as $kS => $vS) {

                            $tdNumberChild = '<td class="text-center">' . (++$kS) . '</td>';
                            $tdSizeSPChild = '<td class="text-center">
                                ' . $vS['name_size'] . '
                            </td>';
                            $tdSizeDCChild = '<td class="text-center">
                                ' . $vS['size_dc'] . '
                            </td>';
                            $tdSizeNumberChild = '<td class="text-center">
                                ' . $vS['style_number'] . '
                            </td>';
                            $tdColorChild = '<td class="text-center">
                                ' . $vS['name_color'] . '
                            </td>';
                            $tdQuantityChild = '<td class="text-center">
                                ' . formatNumber($vS['quantity']) . '
                            </td>';

                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $tdSizeSPChild . '
                                ' . $tdSizeDCChild . '
                                ' . $tdSizeNumberChild . '
                                ' . $tdColorChild . '
                                ' . $tdQuantityChild . '
                            </tr>';
                        }

                        $htmlOrderChange .= '<div>
                            <table class="table table-child" style="width: 50%; margin: 0;">
                                <thead>
                                    <tr class="not-tr">
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th class="text-center" style="width: 120px;">Size SP</th>
                                        <th class="text-center" style="width: 120px;">Size ĐC</th>
                                        <th class="text-center" style="width: 120px;">Style Number</th>
                                        <th class="text-center" style="width: 120px;">Color</th>
                                        <th class="text-center" style="width: 100px;">Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        </div>';
                    }
                }

                $htmlOrderChangeSize = '';
                if ($order['type_orders'] == ORDER_CHANGE_SIZE) {
                    $orderItemschangeSize = $this->orders_model->getOrderItemsChangeSizeByOrderItemId($value['id']);
                    if (!empty($orderItemschangeSize)) {
                        $trHtmlChild = '';
                        foreach ($orderItemschangeSize as $kS => $vS) {

                            $tdNumberChild = '<td class="text-center">' . (++$kS) . '</td>';

                            $tdNumberSizeChild = '<td class="text-center">
                                ' . $vS['number_size'] . '
                            </td>';

                            $tdQuantityChild = '<td class="text-center">
                                ' . formatNumber($vS['quantity']) . '
                            </td>';

                            $tdEvenSheetChild = '<td class="even-sheet text-center">
                                ' . formatNumber($vS['even_sheet']) . '
                            </td>';

                            $tdOddSheetChild = '<td class="odd-sheet text-center">
                                ' . formatNumber($vS['odd_sheet']) . '
                            </td>';

                            $tdEvenBaleChild = '<td class="even-bale text-center">
                                ' . formatNumber($vS['even_bale']) . '
                            </td>';

                            $tdOddBaleChild = '<td class="odd-bale text-center">
                                ' . formatNumber($vS['odd_bale']) . '
                            </td>';

                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $tdNumberSizeChild . '
                                ' . $tdQuantityChild . '
                                ' . $tdEvenSheetChild . '
                                ' . $tdOddSheetChild . '
                                ' . $tdEvenBaleChild . '
                                ' . $tdOddBaleChild . '
                            </tr>';
                        }

                        $htmlOrderChangeSize .= '<div>
                            <table class="table table-child" style="width: 50%; margin: 0;">
                                <thead>
                                    <tr class="not-tr">
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th class="text-center" style="width: 120px;">Số Size</th>
                                        <th class="text-center" style="width: 120px;">Số lượng</th>
                                        <th class="text-center" style="width: 120px;">Tờ chẵn</th>
                                        <th class="text-center" style="width: 120px;">Tờ lẻ</th>
                                        <th class="text-center" style="width: 100px;">Kiện chẵn</th>
                                        <th class="text-center" style="width: 100px;">Kiện lẻ</th>
                                    </tr>
                                </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        </div>';
                    }
                }

                $htmlOrderColumns = '';
                if ($order['type_orders'] == ORDER_CHANGE || $order['type_orders'] == ORDER_DEFAULT || $order['type_orders']) {
                    if ($type_item == "products") {
                        $productsColumns = $this->products_model->getProductsColumns($items_id);
                        $trHtmlChild = '';
                        $thSub = '';
                        $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                        $ct_counter_item = $value['ct_counter_item'];
                        $trHtmlChild = '';
                        $trHtmlColumns = '';
                        if (!empty($productsColumns)) {
                            foreach ($productsColumns as $k => $v) {
                                $thSub .= '<th class="text-center" style="width:80px;">' . $v['name'] . '</th>';
                            }
                        }

                        $orderItemsMap = [];
                        $orderItemsMap1 = [];
                        foreach ($orderItemsColumns as $item) {
                            $orderItemsMap[$item['counter_items_number']][$item['columns_value']] = $item['columns_name'];
                            $orderItemsMap1[$item['counter_items_number']][$item['columns_id']] = $item['columns_name'];
                        }

                        if ($ct_counter_item > 0) {
                            for ($i = 0; $i < $ct_counter_item; $i++) {
                
                                $trHtmlColumns = '';
                                foreach ($productsColumns as $k => $v) {
                                    $columns_name = '';
                                    // foreach ($orderItemsColumns as $kO => $vO) {
                                    //     if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                    //         $columns_name = $vO['columns_name'];
                                    //         break;
                                    //     }
                                    // }
                
                                    $columns_name = isset($orderItemsMap1[$i][$v['id']]) ? $orderItemsMap1[$i][$v['id']] : '';
                                    $trHtmlColumns .= '
                                        <td class="text-center">
                                            ' . $columns_name . '
                                        </td>
                                    ';
                                }
                                
                                // Lấy dữ liệu từ danh sách dự phòng thay vì lặp qua $orderItemsColumns
                                $date_ship = isset($orderItemsMap[$i]['date_ship']) ? $orderItemsMap[$i]['date_ship'] : '';
                                $order_code = isset($orderItemsMap[$i]['order_code']) ? $orderItemsMap[$i]['order_code'] : '';
                                $command = isset($orderItemsMap[$i]['command']) ? $orderItemsMap[$i]['command'] : '';
                                $quantity_put = isset($orderItemsMap[$i]['quantity_put']) ? formatNumber($orderItemsMap[$i]['quantity_put']) : '';
                                $quantity_loss = isset($orderItemsMap[$i]['quantity_loss']) ? formatNumber($orderItemsMap[$i]['quantity_loss']) : '';
                                $sample_quantity_item = isset($orderItemsMap[$i]['sample_quantity_item']) ? formatNumber($orderItemsMap[$i]['sample_quantity_item']) : '';
                        
                                // Tạo các ô TD một cách tối ưu
                                $tdDateShipping = '<td class="text-center">' . $date_ship . '</td>';
                                $tdOrderCode = '<td class="text-center">' . $order_code . '</td>';
                                $tdCommand = '<td class="text-center">' . $command . '</td>';
                                $tdQuantityPut = '<td class="text-center">' . $quantity_put . '</td>';
                                $tdQuantityLoss = '<td class="text-center">' . $quantity_loss . '</td>';
                                $tdSampleQuantityItem = '<td class="text-center">' . $sample_quantity_item . '</td>';
                        
                                if (empty($order_code)) continue;
                        
                                $stt = $i + 1;
                                $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                        
                                $trHtmlChild .= '<tr class="not-tr">
                                    ' . $tdNumberChild . '
                                    ' . $tdDateShipping . '
                                    ' . $tdOrderCode . '
                                    ' . $tdCommand . '
                                    ' . $tdQuantityPut . '
                                    ' . $tdQuantityLoss . '
                                    ' . $tdSampleQuantityItem . '
                                    ' . $trHtmlColumns . '
                                </tr>';
                            }
                
                            $htmlOrderColumns .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
                            <thead>
                                <tr class="not-tr">
                                    <th class="text-center" style="width: 50px;">
                                        ' . lang('tnh_numbers') . '
                                    </th>
                                    <th class="text-center" style="width: 100px;">' . lang('Ngày giao') . '<small class="req text-danger">*</small></th>
                                    <th class="text-center" style="width: 100px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                                    <th class="text-center" style="width: 100px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                                    <th class="text-center" style="width: 80px;">' . lang('tnh_sample_quantity') . '<small class="req text-danger">*</small></th>
                                    ' . $thSub . '
                                </tr>
                            </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                            ';
                        }

                        // if ($ct_counter_item > 0) {
                        //     for ($i = 0; $i < $ct_counter_item; $i++) {
                        //         $trHtmlColumns = '';
                        //         foreach ($productsColumns as $k => $v) {
                        //             $columns_name = '';
                        //             foreach ($orderItemsColumns as $kO => $vO) {
                        //                 if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                        //                     $columns_name = $vO['columns_name'];
                        //                     break;
                        //                 }
                        //             }

                        //             $trHtmlColumns .= '
                        //                 <td class="text-center">
                        //                     ' . $columns_name . '
                        //                 </td>
                        //             ';
                        //         }

                        //         $order_code = '';
                        //         $command = '';
                        //         $quantity_put = '';
                        //         $quantity_loss = '';
                        //         $sample_quantity_item = '';
                        //         foreach ($orderItemsColumns as $kO => $vO) {
                        //             if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                        //                 $order_code = $vO['columns_name'];
                        //                 continue;
                        //             } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                        //                 $command = $vO['columns_name'];
                        //                 continue;
                        //             } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                        //                 $quantity_put = $vO['columns_name'];
                        //                 continue;
                        //             } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                        //                 $quantity_loss = $vO['columns_name'];
                        //                 continue;
                        //             } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                        //                 $sample_quantity_item = $vO['columns_name'];
                        //                 continue;
                        //             }
                        //         }

                        //         $tdOrderCode = '<td class="text-center">
                        //             ' . $order_code . '
                        //         </td>';

                        //         $tdCommand = '<td class="text-center">
                        //             ' . $command . '
                        //         </td>';

                        //         $tdQuantityPut = '<td class="text-center">
                        //             ' . formatNumber($quantity_put) . '
                        //         </td>';

                        //         $tdQuantityLoss = '<td class="text-center">
                        //             ' . formatNumber($quantity_loss) . '
                        //         </td>';

                        //         $tdSampleQuantityItem = '<td class="text-center">
                        //             ' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '
                        //         </td>';


                        //         if (empty($trHtmlColumns) && empty($order_code)) continue;
                        //         $stt = $i + 1;
                        //         $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                        //         $trHtmlChild .= '<tr class="not-tr">
                        //             ' . $tdNumberChild . '
                        //             ' . $tdOrderCode . '
                        //             ' . $tdCommand . '
                        //             ' . $tdQuantityPut . '
                        //             ' . $tdQuantityLoss . '
                        //             ' . $tdSampleQuantityItem . '
                        //             ' . $trHtmlColumns . '
                        //         </tr>';
                        //     }

                        //     $htmlOrderColumns .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
                        //     <thead>
                        //         <tr class="not-tr">
                        //             <th class="text-center" style="width: 50px;">
                        //                 ' . lang('tnh_numbers') . '
                        //             </th>
                        //             <th class="text-center" style="width: 100px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                        //             <th class="text-center" style="width: 100px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                        //             <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                        //             <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                        //             <th class="text-center" style="width: 80px;">' . lang('tnh_sample_quantity') . '<small class="req text-danger">*</small></th>
                        //             ' . $thSub . '
                        //         </tr>
                        //     </thead>
                        //         <tbody class="child">
                        //             ' . $trHtmlChild . '
                        //         </tbody>
                        //     </table>
                        //     ';
                        // }
                    }
                }

                $tdWorkFlow = '<td class="hide">' . $htmlOrderColumns . '' . $htmlOrderChangeSize . ' ' . $htmlOrderChange . '' . $workflow . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdImages . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdModeProduct . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdSampleQuantity . '
                    ' . $tdTotalQuantityItems . '
                    ' . $tdUnitPrice . '
                    ' . $tdDiscountPercent . '
                    ' . $tdGrandTotal . '
                    ' . $tdShipping . '
                    ' . $tdDateShipping . '
                    ' . $tdNote . '
                    ' . $tdWorkFlow . '
                </tr>';
            }
        }

        $data['bodyItems'] = $bodyItems;

        $purchases = $this->orders_model->getPurchasesByOrders($id);
        $data['purchases'] = $purchases;

        $deliveries = $this->orders_model->getDeliveriesByOrderId($id);
        $data['deliveries'] = $deliveries;

        if (!empty($order['employee_id'])) {
            $data['employee'] = get_staff_full_name($order['employee_id']);
        }
        $data['staff_coupon'] = get_staff_full_name($order['staff_coupon']);
        $data['payment_mode'] = $this->site_model->rowPaymentMode($order['payment_mode']);
        $data['address_delivery'] = $address_delivery;
        $data['created_by'] = get_staff_full_name($order['created_by']);
        $data['updated_by'] = !empty($order['updated_by']) ? get_staff_full_name($order['updated_by']) : '';
        $data['user_status'] = !empty($order['user_status']) ? get_staff_full_name($order['user_status']) : '';

        $data['workflow'] = $this->site_model->getOrdersWorkflow($id);
        $data['person_contact'] = $person_contact;
        $data['returned_goods'] = $this->returned_goods_model->getReturnedGoodsByOrder($id);
        $data['transport'] = $this->site_model->rowSupplier($order['transporter_id']);
        $data['gifts'] = $this->site_model->getGiftOrderItems($id);
        $data['id'] = $id;
        $data['order'] = $order;
        $data['name_branch'] = get_table_where('tblbranch', array('id' => $order['id_branch']), '', 'row_array');
        $data['table_price'] = $table_price;
        $data['table_discount'] = $table_discount;
        $data['complains'] = $this->site_model->getTicketsByOrderId($id);
        $data['purchases'] = $purchases;
        $data['total_returns'] = $this->orders_model->getTotalOrdersReturn($id);
        $data['company'] = $this->site_model->rowCustomer($order['customer_id']);
        $data['currencies'] = $this->site_model->getCurrenciesById($order['currencies']);

        $this->db->where('id_orders', $id);
        $this->db->order_by('date_create', 'desc');
        $data['feedback'] = $this->db->get('tblorders_feedback')->result();
        foreach ($data['feedback'] as $key => $value) {
            $this->db->where('rel_id', $value->id);
            $this->db->where('rel_type', 'feedback_o');
            $data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
        }

        $this->load->view('admin/orders/view_order', $data);
    }

    public function print_orders($id)
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_orders.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tbl_orders.id = 0', false, false);
				}
				$this->db->where('id', $id);
				$ktOrders = $this->db->get('tbl_orders')->row();
				if (empty($ktOrders)) {
					accessDenied();
				}
			}
		}


        ob_end_clean();
        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $customer = $this->clients_model->rowCustomer($order['customer_id']);
        $status_orders = $this->status_orders_model->getStatusOrdersById($order['status_orders']);

        if (!empty($order['person_contact_id'])) {
            $contact = get_table_where('tblcontacts', ['id' => $order['person_contact_id']], '', 'row_array');
        } else {
            $contact = get_table_where('tblcontacts', ['userid' => $customer['userid']], '', 'row_array');
        }

        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $employee = '';
        if (!empty($order['employee_id'])) {
            $employee = get_staff_full_name($order['employee_id']);
        }
        $items = $this->orders_model->getOrderItemsByOrderId($id);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_order');
        $data['type'] = 'L';
        $data['img'] = '';

        $bodyItems = '';
        $totalBox = 0;
        $totalSample_quantity = 0;
        $totalTotal_quantity_item = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $info = null;
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }
//                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdCode = '<td style="width: 18%;text-align: left;">' . $info['code'] . '</td>';
                $tdName = '<td style="width: 18%;font-family: kozgopromedium;font-size:11px;text-align: left;">' . $value['product_name_customer'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 8%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 8%;">' . formatNumber($value['quantity']) . '</td>';
                $tdSample_quantity = '<td class="text-center" style="width: 8%;">' . formatNumber($value['sample_quantity']) . '</td>';
                $totalSample_quantity += $value['sample_quantity'];
                $tdTotal_quantity_item = '<td class="text-center" style="width: 8%;">' . formatNumber($value['total_quantity_item']) . '</td>';
                $totalTotal_quantity_item += $value['total_quantity_item'];
                $dtBox = $this->orders_model->getOrderItemExchangeBox($value['id']);

                $box = !empty($dtBox['total_quantity_exchange']) ? $dtBox['total_quantity_exchange'] : 0;
                if (!empty($box)) {
                    $totalBox += $box;
                }

                $tdBox = '<td class="text-center" style="width: 8%;">' . formatNumber($box) . '</td>';

                $tdUnitPrice = '<td class="text-center" style="width: 12%;">' . formatMoney($value['price']) . '</td>';
                $tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
                $tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';

                $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                $dateDelivery = '';
                if (!empty($dtDateDelivery)) {
                    $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                }
                $tdDateDelivery = '<td class="text-center" style="width: 10%;">' . $dateDelivery . '</td>';
                $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                $name_type_print = '';
                if (!empty($typePrint)) {
                    $name_type_print = $typePrint['name'];
                }
                $tdType = '<td class="text-center" style="width: 8%;">' . $name_type_print . '</td>';
                $tdNote = '<td style="width: 14%;text-align: left;">' . htmlentities($value['note_item'], ENT_QUOTES, 'UTF-8') . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdSample_quantity . '
                    ' . $tdTotal_quantity_item . '
                    ' . $tdDateDelivery . '
                    ' . $tdType . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $divAddress = !empty($address_delivery['address']) ? '<span>' . _l('tnh_address_delivery') . ': <span>' . $address_delivery['address'] . '</span></span><br>' : '';
        $divEmployeeCharge = !empty($employee) ? '<span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>' : '';
        $divNote = !empty($order['note']) ? '<span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>' : '';

        $trTax = !empty($order['total_tax']) ? '
            <tr class="bold hide" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('tax') . '</th>
                <th class="text-center">' . $order['tax_name'] . '</th>
                <th></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_tax']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trDiscount = !empty($order['total_discount_percent']) ? '
            <tr class="bold hide" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('tnh_discount') . '(%)</th>
                <th class="text-center"></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_discount_percent']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trCostDelivery = ($order['charge_party'] == "customer" && !empty($order['cost_delivery'])) ? '
            <tr class="bold hide" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('Chi phí giao hàng') . '</th>
                <th class="text-center"></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['cost_delivery']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trDiscountDirect = !empty($order['total_discount_direct']) ? '
            <tr class="bold hide" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('tnh_discount_direct') . '</th>
                <th class="text-center"></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_discount_direct']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $day = date_format(date_create($order['date']), 'd');
        $month = date_format(date_create($order['date']), 'm');
        $year = date_format(date_create($order['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $phoneContact = '';
        if (!empty($contact) && !empty($contact['phonenumber'])) {
            $phoneContact = ' (' . $contact['phonenumber'] . ')';
        }
        $typeOrder = get_table_where('tbl_type_orders', ['id' => $order['type_orders']], '', 'row_array');
        echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8" style="width: 90%;margin-left: 35px;"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('orders') . '</h1></td>
                    
                    <td style="width: 10%;">'.((!empty($status_orders)) ?  '<table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;border-style: soild; border-color: '.$status_orders['color'].';color: '.$status_orders['color'].';">
                    <tr nobr="true"><td class="text-center uppercase">'.mb_strtoupper($status_orders['name'], 'UTF-8').'</td></tr></table>': '').'</td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td style="width: 20%;">' . _l('date') . '</td>
                    <td style="width: 80%;"><b>' . _d($order['date'], true) . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_reference_orders') . '</td>
                    <td><b>' . $order['reference_no'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('Loại đơn hàng') . '</td>
                    <td><b>' . $typeOrder['name'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('customers') . '</td>
                    <td><b>' . $customer['company_short'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_address_delivery') . '</td>
                    <td><b>' . $address_delivery['address'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('Người liên hệ') . '</td>
                    <td><b>' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_note') . '</td>
                    <td><b>' . $order['note'] . '</b></td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%; border-style: soild; border-color: black;font-size: 11px;">
                <tr nobr="true" style="background-color: #ddd;">
                    <td class="bold text-center" style="width: 18%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</td>
                    <td class="bold text-center" style="width: 18%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('SL làm mẫu') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tổng SL') . '</td>
                    <td class="bold text-center" style="width: 10%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Ngày dk giao') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Loại hình in') . '</td>
                    <td class="bold text-center" style="width: 14%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</td>
                </tr>
                ' . $bodyItems . '
                <tr class="bold" nobr="true" style="background-color: #ddd;">
                    <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($order['total_quantity']) . '</th>
                    <th class="text-center">' . formatNumber($totalSample_quantity) . '</th>
                    <th class="text-center">' . formatNumber($totalTotal_quantity_item) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </table>
            <br><br>
            <table style="width: 100%">
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();
        $data['pageCustome'] = 'orders_detail';

//      $content = '<img src="'.($this->createImg()).'"/>';
        $data['content'] = $content;
        
        $qrStyle = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $data['qrCode'] = [
            'code' => 'orders||'.$id,
            'type' => 'QRCODE,Q',
            'x' => 260,
            'y' => 42,
            'width' => 70,
            'height' => 70,
            'style' => $qrStyle,
            'align' => 'N',
        ];

//      echo ($data['content']);die();
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }



    public function print_orders_detail($id)
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $customer = $this->clients_model->rowCustomer($order['customer_id']);

        $contact = get_table_where('tblcontacts', ['userid' => $customer['userid']], '', 'row_array');

        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $employee = '';
        if (!empty($order['employee_id'])) {
            $employee = get_staff_full_name($order['employee_id']);
        }
        $items = $this->orders_model->getOrderItemsByOrderId($id);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_order');
        $data['type'] = 'L';
        $data['img'] = '';

        $bodyItems = '';
        $totalBox = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $info = null;
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdCode = '<td style="width: 17%;">' . $info['code'] . '</td>';
                $tdName = '<td style="width: 19%;font-family: kozgopromedium;font-size:11px">' . $value['product_name_customer'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 8%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';

                $dtBox = $this->orders_model->getOrderItemExchangeBox($value['id']);

                $box = !empty($dtBox['total_quantity_exchange']) ? $dtBox['total_quantity_exchange'] : 0;
                if (!empty($box)) {
                    $totalBox += $box;
                }

                $tdBox = '<td class="text-center" style="width: 8%;">' . formatNumber($box) . '</td>';

                $tdUnitPrice = '<td class="text-center" style="width: 12%;">' . formatMoney($value['price']) . '</td>';
                $tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
                $tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';

                $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                $dateDelivery = '';
                if (!empty($dtDateDelivery)) {
                    $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                }
                $tdDateDelivery = '<td class="text-center" style="width: 12%;">' . $dateDelivery . '</td>';
                $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                $name_type_print = '';
                if (!empty($typePrint)) {
                    $name_type_print = $typePrint['name'];
                }
                $tdType = '<td class="text-center" style="width: 13%;">' . $name_type_print . '</td>';

                $tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';

                $htmlOrderColumns = '';
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $value['ct_counter_item'];
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                    $styleTd = '';
                    if (!empty($productsColumns)) {
                        $styleTd = 'width: ' . 72 / count($productsColumns) . '%';
                        foreach ($productsColumns as $k => $v) {
                            $thSub .= '<th class="text-center" style="' . $styleTd . '">' . $v['name'] . '</th>';
                        }
                    }
                    $orderItemsColumnsNew = [];
                    if ($ct_counter_item > 0) {
                        for ($i = 0; $i < $ct_counter_item; $i++) {
                            $arrNew = [];
                            foreach ($productsColumns as $k => $v) {
                                $columns_name = [];
                                foreach ($orderItemsColumns as $kO => $vO) {
                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                        $columns_name = [
                                            vn_to_str($vO['columns_value']) => $vO['columns_name']
                                        ];
                                        break;
                                    }
                                }
                                $arrNew = array_merge($arrNew, $columns_name);
                            }
                            $orderItemsColumnsNew[$i] = $arrNew;
                            $date_ship = '';
                            $order_code = '';
                            $command = '';
                            $quantity_put = '';
                            $quantity_loss = '';
                            $sample_quantity_item = '';
                            foreach ($orderItemsColumns as $kO => $vO) {
                                if ($vO['columns_value'] == 'date_ship' && $i == $vO['counter_items_number']) {
                                    $date_ship = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['date_ship'] = $date_ship;
                                    continue;
                                }
                                else if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                    $order_code = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['code'] = $order_code;
                                    continue;
                                }
								else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                    $command = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['command'] = $command;
                                    continue;
                                }
								else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                    $quantity_put = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
                                    continue;
                                }
								else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                    $quantity_loss = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                    continue;
                                }
								else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                    $sample_quantity_item = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                    continue;
                                }
                            }
                        }
                    }
                    $orderItemsColumnsNewVs1 = [];
                    if (!empty($orderItemsColumnsNew)) {
                        foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                            $columns_name_new = 'default';
                            if (!empty($productsColumns)) {
                                foreach ($productsColumns as $k => $v) {
                                    $name_check = vn_to_str($v['name']);
                                    if (!empty($vvv[$name_check])) {
                                        $columns_name_new .= $vvv[$name_check] . '__';
                                    }
                                }
                            }
                            $columns_name_new = trim($columns_name_new, '__');
                            $check_key = $columns_name_new;
                            if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
                                $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
                                $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
                            } else {
                                $orderItemsColumnsNewVs1[$check_key] = $vvv;
                            }
                        }
                    }
                    $ii = 1;
                    if (!empty($orderItemsColumnsNewVs1)) {
                        foreach ($orderItemsColumnsNewVs1 as $kk => $vv) {
                            $order_code = $vv['code'];
                            $command = $vv['command'];
                            $quantity_put = $vv['quantity_put'];
                            $quantity_loss = $vv['quantity_loss'];
                            $sample_quantity_item = $vv['sample_quantity_item'];
                            $trHtmlColumns = '';
                            $columns_name_new = '';
                            if (!empty($productsColumns)) {
                                foreach ($productsColumns as $k => $v) {
                                    $name_check = vn_to_str($v['name']);
                                    if (!empty($vv[$name_check])) {
                                        $columns_name_new = $vv[$name_check];
                                        $trHtmlColumns .= '
                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">
                                            ' . $columns_name_new . '
                                        </td>
                                        ';
                                    } else {
                                        $trHtmlColumns .= '
                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">
                                            ' . $columns_name_new . '
                                        </td>
                                        ';
                                    }
                                }
                            }
							
                            $tdOrderCode = '<td class="text-center">
                                ' . $order_code . '
                            </td>';

                            $tdCommand = '<td class="text-center">
                                ' . $command . '
                            </td>';

                            $tdQuantityPut = '<td class="text-center">
                                ' . formatNumber($quantity_put) . '
                            </td>';

                            $tdQuantityLoss = '<td class="text-center">
                                ' . formatNumber($quantity_loss) . '
                            </td>';

                            $tdSampleQuantityItem = '<td class="text-center">
                                ' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '
                            </td>';

                            $tdQuantityOld = '<td class="text-center">
                                ' . (!empty($quantity_put + $quantity_loss + $sample_quantity_item) ? formatNumber($quantity_put + $quantity_loss + $sample_quantity_item) : '') . '
                            </td>';


                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                            $stt =  $ii;
                            $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $trHtmlColumns . '
                                ' . $tdQuantityPut . '
                                ' . $tdQuantityLoss . '
                                ' . $tdSampleQuantityItem . '
                                ' . $tdQuantityOld . '
                            </tr>';
                            $ii++;
                        }
                    }
                    $htmlOrderColumns .= '<table class="table" border="1">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 4%">
                                        ' . lang('tnh_numbers') . '
                                    </th>
                                     ' . $thSub . '
                                    <th class="text-center" style="width: 6%">' . lang('tnh_quantity_put') . '</th>
                                    <th class="text-center" style="width: 6%">' . lang('tnh_quantity_loss') . '</th>
                                    <th class="text-center" style="width: 6%">' . lang('tnh_sample_quantity') . '</th>
                                    <th class="text-center" style="width: 6%">' . lang('Tổng số lượng') . '</th>
                                </tr>
                            </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        ';
                }

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdDateDelivery . '
                    ' . $tdType . '
                    ' . $tdNote . '
                </tr>
                <tr>
                    <td colspan="8">
                        ' . $htmlOrderColumns . '
                    </td>
                </tr>
                ';
            }
        }

        $divAddress = !empty($address_delivery['address']) ? '<span>' . _l('tnh_address_delivery') . ': <span>' . $address_delivery['address'] . '</span></span><br>' : '';
        $divEmployeeCharge = !empty($employee) ? '<span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>' : '';
        $divNote = !empty($order['note']) ? '<span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>' : '';


        $day = date_format(date_create($order['date']), 'd');
        $month = date_format(date_create($order['date']), 'm');
        $year = date_format(date_create($order['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $phoneContact = '';
        if (!empty($contact) && !empty($contact['phonenumber'])) {
            $phoneContact = ' (' . $contact['phonenumber'] . ')';
        }
        $typeOrder = get_table_where('tbl_type_orders', ['id' => $order['type_orders']], '', 'row_array');
        echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('orders') . '</h1></td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td style="width: 20%;">' . _l('date') . '</td>
                    <td style="width: 80%;"><b>' . _d($order['date'], true) . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_reference_orders') . '</td>
                    <td><b>' . $order['reference_no'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('Loại đơn hàng') . '</td>
                    <td><b>' . $typeOrder['name'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('customers') . '</td>
                    <td><b>' . $customer['company_short'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_address_delivery') . '</td>
                    <td><b>' . $address_delivery['address'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('Người liên hệ') . '</td>
                    <td><b>' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_note') . '</td>
                    <td><b>' . $order['note'] . '</b></td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%; border-style: soild; border-color: black;">
                <tr nobr="true" style="background-color: #ddd;">
                    <td class="bold text-center" style="width: 6%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</td>
                    <td class="bold text-center" style="width: 17%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</td>
                    <td class="bold text-center" style="width: 19%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</td>
                    <td class="bold text-center" style="width: 10%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</td>
                    <td class="bold text-center" style="width: 12%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Ngày dk giao') . '</td>
                    <td class="bold text-center" style="width: 13%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Loại hình in') . '</td>
                    <td class="bold text-center" style="width: 15%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</td>
                </tr>
                ' . $bodyItems . '
                <tr class="bold" nobr="true" style="background-color: #ddd;">
                    <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                    <th></th>
                    <th class="text-center">' . formatNumber($order['total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </table>
            <br><br>
            <table style="width: 100%">
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'orders_detail';
        $pdf = @print_pdf_tnh_new($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_orders_vs1($id)
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $customer = $this->clients_model->rowCustomer($order['customer_id']);

        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $employee = '';
        if (!empty($order['employee_id'])) {
            $employee = get_staff_full_name($order['employee_id']);
        }
        $items = $this->orders_model->getOrderItemsByOrderId($id);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_order');
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        $totalBox = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $info = null;
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 38%;">' . $info['name'] . ' (' . $info['code'] . ')</td>';
                $tdUnit = '<td class="text-center" style="width: 8%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';

                $dtBox = $this->orders_model->getOrderItemExchangeBox($value['id']);

                $box = !empty($dtBox['total_quantity_exchange']) ? $dtBox['total_quantity_exchange'] : 0;
                if (!empty($box)) {
                    $totalBox += $box;
                }

                $tdBox = '<td class="text-center" style="width: 8%;">' . formatNumber($box) . '</td>';

                $tdUnitPrice = '<td class="text-center" style="width: 12%;">' . formatMoney($value['price']) . '</td>';
                $tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
                $tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';
                $tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTotalAmount . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $divAddress = !empty($address_delivery['address']) ? '<span>' . _l('tnh_address_delivery') . ': <span>' . $address_delivery['address'] . '</span></span><br>' : '';
        $divEmployeeCharge = !empty($employee) ? '<span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>' : '';
        $divNote = !empty($order['note']) ? '<span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>' : '';

        $trTax = !empty($order['total_tax']) ? '
            <tr class="bold" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('tax') . '</th>
                <th class="text-center">' . $order['tax_name'] . '</th>
                <th></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_tax']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trDiscount = !empty($order['total_discount_percent']) ? '
            <tr class="bold" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('tnh_discount') . '(%)</th>
                <th class="text-center"></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_discount_percent']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trCostDelivery = ($order['charge_party'] == "customer" && !empty($order['cost_delivery'])) ? '
            <tr class="bold" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('Chi phí giao hàng') . '</th>
                <th class="text-center"></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['cost_delivery']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trDiscountDirect = !empty($order['total_discount_direct']) ? '
            <tr class="bold" nobr="true" style="background-color: #ddd;">
                <th class="text-right" colspan="3">' . _l('tnh_discount_direct') . '</th>
                <th class="text-center"></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_discount_direct']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $day = date_format(date_create($order['date']), 'd');
        $month = date_format(date_create($order['date']), 'm');
        $year = date_format(date_create($order['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('orders') . '</h1></td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td>' . _l('date') . '</td>
                    <td>' . _l('tnh_reference_orders') . '</td>
                    <td>' . _l('customers') . '</td>
                </tr>
                <tr nobr="true">
                    <td><b>' . _d($order['date'], true) . '</b></td>
                    <td><b>' . $order['reference_no'] . '</b></td>
                    <td><b>' . $customer['company'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td>' . _l('tnh_address_delivery') . '</td>
                    <td>' . _l('tnh_employees_charge') . '</td>
                    <td>' . _l('tnh_note') . '</td>
                </tr>
                <tr nobr="true">
                    <td><b>' . $address_delivery['address'] . '</b></td>
                    <td><b>' . $employee . '</b></td>
                    <td><b>' . $order['note'] . '</b></td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%; border-style: soild; border-color: black;">
                <tr nobr="true" style="background-color: #ddd;">
                    <td class="bold text-center" style="width: 6%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</td>
                    <td class="bold text-center" style="width: 38%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_its') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</td>
                    <td class="bold text-center" style="width: 10%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</td>
                    <td class="bold text-center" style="width: 12%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('price') . '</td>
                    <td class="bold text-center" style="width: 13%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_subtotal') . '</td>
                    <td class="bold text-center" style="width: 15%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</td>
                </tr>
                ' . $bodyItems . '
                <tr class="bold" nobr="true" style="background-color: #ddd;">
                    <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($order['total_quantity']) . '</th>
                    <th></th>
                    <th class="text-right">' . formatMoney($order['grand_total_items']) . '</th>
                    <th></th>
                </tr>
                ' . $trTax . '
                ' . $trDiscount . '
                ' . $trDiscountDirect . '
                ' . $trCostDelivery . '
                ' . ($order['grand_total_items'] != $order['grand_total'] ? '<tr class="bold" nobr="true" style="background-color: #ddd;">
                    <th class="text-right" colspan="3">' . _l('tnh_grand_total') . '</th>
                    <th class="text-center"></th>
                    <th></th>
                    <th class="text-right">' . formatMoney($order['grand_total']) . '</th>
                    <th></th>
                </tr>' : '') . '
            </table>
            <br><br>
            <table style="width: 100%">
                <tr nobr="true">
                    <td colspan="3"><span>' . _l("tnh_money_characters") . ': ' . convert_number_to_words($order['grand_total']) . '</span></td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_orders_backup($id)
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $employee = '';
        if (!empty($order['employee_id'])) {
            $employee = get_staff_full_name($order['employee_id']);
        }
        $items = $this->orders_model->getOrderItemsByOrderId($id);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_order');
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 35%;">' . $info['name'] . ' (' . $info['code'] . ')</td>';
                $tdUnit = '<td class="text-center" style="width: 6%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td class="text-right" style="width: 10%;">' . formatMoney($value['price']) . '</td>';
                $tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
                $tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';
                $tdNote = '<td style="width: 10%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdDiscount . '
                    ' . $tdTotalAmount . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $divAddress = !empty($address_delivery['address']) ? '<span>' . _l('tnh_address_delivery') . ': <span>' . $address_delivery['address'] . '</span></span><br>' : '';
        $divEmployeeCharge = !empty($employee) ? '<span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>' : '';
        $divNote = !empty($order['note']) ? '<span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>' : '';

        $trTax = !empty($order['total_tax']) ? '
            <tr class="bold">
                <th class="text-right" colspan="3">' . _l('tax') . '</th>
                <th class="text-center"></th>
                <th></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_tax']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trDiscount = !empty($order['total_discount_percent']) ? '
            <tr class="bold">
                <th class="text-right" colspan="3">' . _l('tnh_discount') . '(%)</th>
                <th class="text-center"></th>
                <th></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_discount_percent']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $trDiscountDirect = !empty($order['total_discount_direct']) ? '
            <tr class="bold">
                <th class="text-right" colspan="3">' . _l('tnh_discount_direct') . '</th>
                <th class="text-center"></th>
                <th></th>
                <th></th>
                <th class="text-right">' . formatMoney($order['total_discount_direct']) . '</th>
                <th></th>
            </tr>
        ' : '';

        $day = date_format(date_create($order['date']), 'd');
        $month = date_format(date_create($order['date']), 'm');
        $year = date_format(date_create($order['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <h1 class="text-center uppercase">' . lang('SALE ORDER') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_orders') . ': ' . $order['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($order['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('customers') . ': <span class="bold">' . $order['customer_name'] . '</span></span><br>
                ' . $divAddress . '
                ' . $divEmployeeCharge . '
                ' . $divNote . '
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 35%;">' . _l('tnh_its') . '</th>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('price') . '</th>
                        <th class="bold text-center" style="width: 12%;">' . _l('tnh_discount') . '</th>
                        <th class="bold text-center" style="width: 13%;">' . _l('tnh_subtotal') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('tnh_note') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                        <th class="text-center">' . formatNumber($order['total_quantity']) . '</th>
                        <th></th>
                        <th></th>
                        <th class="text-right">' . formatMoney($order['grand_total_items']) . '</th>
                        <th></th>
                    </tr>
                    ' . $trTax . '
                    ' . $trDiscount . '
                    ' . $trDiscountDirect . '
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_grand_total') . '</th>
                        <th class="text-center"></th>
                        <th></th>
                        <th></th>
                        <th class="text-right">' . formatMoney($order['grand_total']) . '</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <p>' . _l("tnh_money_characters") . ': ' . convert_number_to_words($order['grand_total']) . '</p>
            <p class="text-right"><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $pdf = print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_orders_old($id)
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
        $area = $this->site_model->rowDeliveryArea($address_delivery['city_shipping'], $address_delivery['district_shipping']);
        $employee = '';
        if (!empty($order['employee_id'])) {
            $employee = get_staff_full_name($order['employee_id']);
        }
        $customer = $this->clients_model->rowCustomer($order['customer_id']);
        $codeCustomer = $customer['zcode'];
        $companyCustomer = $customer['company'];
        $addressCompany = $customer['address'];
        $phoneCompany = $customer['phonenumber'];
        $zaloCompany = $this->site_model->getZaloClient($order['customer_id'])['zalo'];
        $emailCompany = $customer['email_client'];

        $contact = $this->site_model->rowContact($order['person_contact_id']);
        $personContact = $contact['firstname'];
        $phoneContact = $contact['phonenumber'];

        $items = $this->orders_model->getOrderItemsByOrderId($id);
        $created_by = get_staff_full_name($order['created_by']);

        $note_default = '';
        $i = 0;
        foreach (typeNotificationForm() as $key => $value) {
            if (!empty($order[$key])) {
                $note_default .= $order[$key] . ',';
            }
        }

        if (!empty($note_default)) {
            $note_default = $this->site_model->getNotificationFormText(explode(',', $note_default))['note_default'];
        }

        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_order');
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 20%;">' . $info['name'] . '</td>';
                $tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';
                $tdCode = '<td style="width: 15%;">' . $info['name'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 6%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td class="text-right" style="width: 13%;">' . formatMoney($value['price']) . '</td>';
                // $tdTax = '<td class="text-right" style="width: 10%;">'.formatMoney($value['tax_amount_item']).'</td>';
                // $tdDiscount = '<td class="text-right" style="width: 12%;">'.formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']).'</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 15%;">' . formatMoney($value['total_amount']) . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdNote . '
                    ' . $tdCode . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTotalAmount . '
                </tr>';
            }
        }

        // $day = date_format(date_create($order['date']), 'd');
        // $month = date_format(date_create($order['date']), 'm');
        // $year = date_format(date_create($order['date']), 'Y');

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 25px;">' . _l('tnh_sales_orders') . '</h1></td>
                </tr>
            </table>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="5"><span>' . _l('tnh_code_customer') . '</span>: ' . $codeCustomer . '</td>
                    <td colspan="3"><span>' . _l('Số ĐHB') . '</span>: ' . $order['reference_no'] . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="8"><span>' . _l('tnh_ncustomer') . '</span>: ' . $personContact . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="8"><span>' . _l('tnh_unit') . '</span>: ' . $companyCustomer . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="8"><span>' . _l('tnh_address') . '</span>: ' . $addressCompany . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="5"><span>' . _l('tnh_person_contact') . '</span>: ' . $personContact . '</td>
                    <td colspan="3"><span>' . _l('tnh_telephone') . '</span>: ' . $phoneCompany . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="5"><span>' . _l('Zalo') . '</span>: ' . $zaloCompany . '</td>
                    <td colspan="3"><span>' . _l('Email') . '</span>: ' . $emailCompany . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="8"><span>' . _l('tnh_address_delivery') . '</span>: ' . $address_delivery['address'] . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="5"><span>' . _l('tnh_person_contact') . ' (ĐT)</span>: ' . $phoneContact . '</td>
                    <td colspan="3"><span>' . _l('Mã phân khu GH') . '</span>: ' . $area['code'] . '</td>
                </tr>
                <tr nobr="true">
                    <td colspan="8"><span>' . _l('tnh_note_default') . '</span>: ' . $note_default . '</td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td colspan="1" style="width: 6%;"><span>' . _l('tnh_numbers') . '</span></td>
                    <td colspan="1" style="width: 20%;"><span>' . _l('tnh_item_name') . '</span></td>
                    <td colspan="1" style="width: 15%;"><span>' . _l('tnh_note') . '</span></td>
                    <td colspan="1" style="width: 15%;"><span>' . _l('tnh_num') . '</span></td>
                    <td colspan="1" style="width: 6%;"><span>' . _l('tnh_dvt') . '</span></td>
                    <td colspan="1" style="width: 10%;"><span>' . _l('quantity') . '</span></td>
                    <td colspan="1" style="width: 13%;"><span>' . _l('tnh_prices') . '</span></td>
                    <td colspan="1" style="width: 15%;"><span>' . _l('tnh_subtotal') . '</span></td>
                </tr>
                ' . $bodyItems . '
            </table>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true" class="bold text-right">
                    <td colspan="6" style="border: none;">' . _l('tnh_subtotal') . ':</td>
                    <td colspan="2" style="border: none;">' . formatMoney($order['total_amount_items']) . '</td>
                </tr>
                ' . ($order['total_tax'] > 0 ?
            '<tr nobr="true" class="bold text-right">
                        <td colspan="6">' . _l('Thuế VAT') . ':</td>
                        <td colspan="2">' . formatMoney($order['total_tax']) . '</td>
                    </tr>
                    <tr nobr="true" class="bold text-right">
                        <td colspan="6">' . _l('tnh_grand_total') . ':</td>
                        <td colspan="2">' . formatMoney($order['grand_total']) . '</td>
                    </tr>' : '') . '
                <tr nobr="true" class="bold">
                    <td class="" colspan="8">Thành tiền bằng chữ: ' . ucfirst(convert_number_to_words($order['grand_total'])) . ' đồng chẵn</td>
                </tr>
                <tr nobr="true" class="bold">
                    <td class="" colspan="8">Ghi chú: ' . $order['note'] . '</td>
                </tr>
                <tr nobr="true" class="bold">
                    <td class="" colspan="8"></td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td colspan="5"><span></span></td>
                    <td colspan="3"><span>TP.HCM, Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span>: </td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td colspan="5"><span>Người lập biểu</span></td>
                    <td colspan="3"><span>Đại điện công ty Thiệp Đức Quyền</span></td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td colspan="5"><span></span></td>
                    <td colspan="3"><span>(phê duyệt)</span></td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td colspan="5"><span class="uppercase"><br><br><br><br>' . $created_by . '</span></td>
                    <td colspan="3"><span class="uppercase"><br><br><br><br>NGÔ ĐỨC QUYỀN</span></td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        // $barcode = file_get_contents(genBarcode($order['reference_no']));
        $order['reference_no'] = str_replace('Đ', 'D', $order['reference_no']);
        $barcode = file_get_contents(genBarcode($order['reference_no']));
        $barcode = '<img style="width: 130px;" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';
        $data['content'] = $content;
        $data['barcode'] = $barcode;
        $data['content'] = $content;
        $pdf = print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function agree()
    {
        $data = [];
        if (!$this->perApproveOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($this->input->get()) {
            $order_id = $this->input->get('order_id');
            $status = $this->input->get('status');
            $order = $this->orders_model->rowOrderById($order_id);
            $ckView = checkView('orders', $order['list_users'], $order_id);
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();
            if ($order['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }

            // if ($order['status_payment'] != 0) {
            //     $data['result'] = 0;
            //     $data['message'] = lang('tnh_dttktbd');
            //     echo json_encode($data); die;
            // }

            $checkExist = get_table_where('tbltransfer_warehouse', ['order_id_new' => $order_id], '', 'row_array');
            if (!empty($checkExist)) {
                $data['result'] = 0;
                $data['message'] = lang('Đơn hàng đã giữ kho không thể bỏ duyệt !!!');
                echo json_encode($data);
                die;
            }

            if ($order['count_delivery'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_created_delivery_not_agree');
                echo json_encode($data);
                die;
            }

            if ($order['type_bills'] == 1) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_this_order_has_generated_tax_invoices_not_un_approved');
                echo json_encode($data);
                die;
            }

            if ($order['productions_plan_id'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_dtkhsx');
                echo json_encode($data);
                die;
            }

            $this->db->from('tbl_productions_plan_items');
            $this->db->where('tbl_productions_plan_items.type_object', 'orders');
            $this->db->where('tbl_productions_plan_items.object_id', $order_id);
            $ck = $this->db->count_all_results();
            if ($ck) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_dtkhsx');
                echo json_encode($data); die;
            }

            $up = $this->orders_model->updateOrdersNew($order_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                @pusherTNHNotfication();
                insertActivityLog([
                    'type_parent_obj' => 'orders',
                    'table_obj' => 'tbl_orders',
                    'id_obj' => $order_id,
                    'name_obj' => $order['reference_no'],
                    'content' => lang('tnh_his_agree_orders') . ' [' . $order['reference_no'] . ']',
                    'actions' => 'agree'
                ]);

                //
                if ($status == "approved") {
                    $staffOrders = $this->site_model->getStaffChildPermission('notification_orders', 'can_agree_notifications');
                    if (!empty($staffOrders)) {
                        $notifiedUsers = [];
                        foreach ($staffOrders as $member) {
                            if ($member['id_staff'] != 0) {
                                $description = lang('tnh_agree_orders') . ' [' . $order['reference_no'] . ']';
                                $notified = add_notification([
                                    'description'     => $description,
                                    'touserid'        => $member['id_staff'],
                                    'fromcompany'     => 1,
                                    'fromuserid'      => null,
                                    'additional_data' => serialize([
                                        $description,
                                    ]),
                                    'link' => 'orders',
                                ]);
                                if ($notified) {
                                    array_push($notifiedUsers, $member['id_staff']);
                                }
                            }

                            $channels = ['tnh-notification-popup-' . $member['id_staff']];
                            $channels = array_unique($channels);
                            $this->load->library('app_pusher');
                            $this->app_pusher->trigger($channels, 'tnh-notification-popup', [createdPopupNotification('orders', $order_id, $description)]);
                        }
                        pusher_trigger_notification($notifiedUsers);
                    }
                }
                //

                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function deleteOrders($id)
    {
        $data = [];
        if (!$this->perDeleteOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            $order = $this->orders_model->rowOrderById($id);
            if (!checkMyDataTF($order['created_by'])) {
                $data['result'] = 0;
                $data['message'] = lang('access_denied');
                echo json_encode($data);
                die;
            }

            if ($order['hold_the_goods'] == 1) {
                $transfer = $this->site_model->getTransferWarehouse($id);
                if (!empty($transfer)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_dghktx');
                    echo json_encode($data);
                    die;
                }
            }

            if ($order['status_payment'] != 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_dttktx');
                echo json_encode($data);
                die;
            }

            $countPurchase = $this->orders_model->countPurchaseOrders($id);
            if ($countPurchase) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_dtpycmhkdx');
                echo json_encode($data);
                die;
            }

            $items = $this->orders_model->getOrderItemsByOrderId($id);
            // hoàng crm bổ xung đk xóa phiếu thu
            if ($order['status'] == "un_approved" && $order['status_payment'] == 0) {
                if ($this->orders_model->deleteOrdersById($id)) {
                    $this->site_model->deleteOrderGifts($id);
                    $this->orders_model->deleteOrderItemsStages($id);
                    $this->orders_model->deleteOrdersItemsByOrderId($id);
                    $this->orders_model->deleteOrderItemsSizeByOrderId($id);
                    $this->orders_model->deleteBatchOrderItemsChangeSize($id);
                    $this->orders_model->deleteOrderItemsColumns($id);

                    foreach ($items as $key => $value) {
                        $this->orders_model->deleteOrderItemShippings($value['id']);
                        $this->orders_model->deleteOrderItemExchange($value['id']);
                    }

                    if (checkModule('quotes')) {
                        $this->db->where('order_id', $id);
                        $this->db->update('tbl_quotes', ['order_id' => 0]);
                    }
                    //delete remove attachments
                    if (!empty($order['attachments'])) {
                        foreach (explode('||', $order['attachments']) as $key => $value) {
                            if (file_exists($this->upload_path . '' . $value)) {
                                @unlink($this->upload_path . '' . $value);
                            }
                        }
                    }
                    //

                    noti_custom('create_orders', $id, get_staff_user_id(), 0, '', ['actions' => 'delete']);
                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_his_delete_orders') . ' [' . $order['reference_no'] . ']',
                        'actions' => 'delete'
                    ]);

                    $this->site_model->deleteOrdersWorkflowByOrderId($id);
                    $this->orders_model->deleteOrdersRelationship($id);
                    $this->orders_model->deleteOrdersSub($id);
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('browsed_and_payment_cannot_be_deleted');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function convert_delivery($id)
    {
        if (!$this->perAddOrders) {
            accessDenied($js = true);
        }
        $order = $this->orders_model->rowOrderById($id);
        // $items = $this->orders_model->getOrderItemsByOrderId($id);
        $items = $this->orders_model->getOrderItemsDelivery($id);

        if ($order['status'] != "approved") {
            refererModel(lang('tnh_please_approved'));
        }
        $customer_id = $order['customer_id'];
        $customer_name = $order['customer_name'];

        if ($this->input->post('save')) {
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('staff_admin', lang("tnh_employees_charge"), 'required');
            $this->form_validation->set_rules('address_delivery', lang("tnh_address_delivery"), 'required');
            // $this->form_validation->set_rules('person_contact', lang("tnh_person_contact"), 'required');
            $this->form_validation->set_rules('warehouses[]', lang("tnh_warehouses"), 'required');
            // $this->form_validation->set_rules('locations[]', lang("tnh_location_warehouse"), 'required');
            if ($this->form_validation->run() == true) {
                //print_arrays($this->input->post());
                $date = to_sql_date($this->input->post('date'), true);
                $address_delivery = $this->input->post('address_delivery');
                $address_delivery = str_replace('customers__', '', $address_delivery);
                $employee_id = $this->input->post('staff_admin');
                $note = $this->input->post('note');

                $type = $this->input->post('type');
                $id_branch = $this->input->post('id_branch');

                $person_contact_id = $this->input->post('person_contact');
                $person_contact_id = str_replace('customers__', '', $person_contact_id);
                $noti_phone = $this->input->post('noti_phone');
                $noti_phone = !empty($noti_phone) ? implode(',', $noti_phone) : null;
                $noti_email = $this->input->post('noti_email');
                $noti_email = !empty($noti_email) ? implode(',', $noti_email) : null;
                $noti_zalo = $this->input->post('noti_zalo');
                $noti_zalo = !empty($noti_zalo) ? implode(',', $noti_zalo) : null;
                $noti_note_other = $this->input->post('noti_note_other');
                $noti_note_other = !empty($noti_note_other) ? implode(',', $noti_note_other) : null;

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $tax_id = $order['tax_id'];
                $tax_name = $order['tax_name'];
                $tax_rate = $order['tax_rate'];
                $total_tax = 0;
                $discount_percent = $order['discount_percent'];
                $total_discount_percent = 0;
                if ($order['count_delivery'] == 0) {
                    $total_discount_direct = $order['total_discount_direct'];
                } else {
                    $total_discount_direct = 0;
                }

                $grand_total = 0;
                $status = 'un_approved';
                $errorItems = '';

                $counter = $this->input->post('counter');
                $arr_id = [];
                $arr_info = [];
                $items_check = [];
                foreach ($counter as $key => $value) {
                    $order_item_id = $this->input->post('order_item_id')[$value];
                    if (empty($order_item_id)) {
                        continue;
                    }
                    $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                    if (empty($order_item)) {
                        continue;
                    }

                    $check_loss = !empty($this->input->post('check_loss')[$value]) ? $this->input->post('check_loss')[$value] : 0;

                    $item_id = $order_item['item_id'];
                    $items_code = $order_item['item_code'];
                    $items_name = $order_item['item_name'];
                    $type_item = $order_item['type_item'];
                    $quantity_delivery = number_unformat($this->input->post('quantity_delivery')[$value]);
                    $quantity = $quantity_delivery;

                    $quantity_loss_delivery = number_unformat($this->input->post('quantity_loss')[$value]);
                    $quantity_order_loss = ($order_item['quantity_loss']);

                    $this->db->select('SUM(tbl_delivery_items.quantity_loss) as quantity_loss');
                    $this->db->from('tbl_delivery_items');
                    $this->db->where('order_item_id', $order_item['id']);
                    $quantity_had_delivery_loss = $this->db->get()->row_array()['quantity_loss'];

                    $quantity_sample = number_unformat($this->input->post('quantity_sample')[$value]);
                    $quantity_order_sample = ($order_item['sample_quantity']);

                    $this->db->select('SUM(tbl_delivery_items.quantity_sample) as quantity_sample');
                    $this->db->from('tbl_delivery_items');
                    $this->db->where('order_item_id', $order_item['id']);
                    $quantity_had_delivery_sample = $this->db->get()->row_array()['quantity_sample'];

                    $note_item = $this->input->post('note_item')[$value];

                    //check quantity delivery
                    $quantity_order = $order_item['quantity'];
                    $quantity_had_delivery = $order_item['quantity_delivery'];
                    $quantity_max = $quantity_order - $quantity_had_delivery;

                    $dataWarehouses = $this->input->post('warehouses')[$value];
                    $dataWarehouses = explode('__', $dataWarehouses);
                    $warehouses = $dataWarehouses[0];
                    $locations = $dataWarehouses[1];
                    $lot_code = $dataWarehouses[2];
                    $date_sx = $dataWarehouses[3];
                    $date_sd = $dataWarehouses[4];
                    $date_use = $dataWarehouses[5];
                    if (empty($warehouses) || empty($locations)) {
                        $data['result'] = 0;
                        $data['message'] = 'Vui lòng chọn kho hàng ?';
                        echo json_encode($data);
                        die;
                    }


                    // $warehouses = $this->input->post('warehouses')[$value];
                    // $locations = $this->input->post('locations')[$value];


                    // if ($quantity_delivery > $quantity_max) {
                    //     $errorItems.= lang('tnh_quantity_delivery_have_change_please_referesh');
                    //     break;
                    // }

                    $price = $order_item['price'];
                    $is_lot = $order_item['is_lot'];
                    if ($is_lot == 1) {
                        $amount = $price;
                    } else {
                        $amount = $quantity * $price;
                    }
                    $grand_total_item = $amount;
                    $tax_id_item = $order_item['tax_id_item'];
                    $tax_name_item = $order_item['tax_name_item'];
                    $tax_rate_item = $order_item['tax_rate_item'];
                    $tax_amount_item = 0;

                    if ($tax_rate_item > 0) {
                        $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                        $total_tax_items += $tax_amount_item;
                        $grand_total_item += $tax_amount_item;
                    }

                    //discount percent item
                    $discount_percent_item = number_unformat($order_item['discount_percent_item']);
                    $discount_percent_amount_item = 0;
                    if ($discount_percent_item > 0) {
                        $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                        $total_discount_percent_items += $discount_percent_amount_item;
                        $grand_total_item -= $discount_percent_amount_item;
                    }
                    //end
                    if ($order['count_delivery'] == 0) {
                        $discount_direct_amount_item = $order_item['discount_direct_amount_item'];
                    } else {
                        $discount_direct_amount_item = 0;
                    }

                    $total_discount_direct_items += $discount_direct_amount_item;
                    $grand_total_item -= $discount_direct_amount_item;


                    if (($quantity + $quantity_loss_delivery + $quantity_sample) <= 0) {
                        $data['result'] = 0;
                        $data['message'] = 'Mặt hàng '.$items_code.'('.$items_name.') '.'Số lượng giao hàng lớn hơn 0';
                        echo json_encode($data);
                        die;
                    }
                    //dt
                    $orderItemsColumnsNewVS1 = [];
                    if ($type_item == "products") {
                        $json = !empty($this->input->post('json')[$value]) ? $this->input->post('json')[$value] : false;
                        if (!empty($json)) {
                            foreach ($json as $k => $val) {
                                $val = json_decode($val);
                                if (empty($val)) {
                                    continue;
                                }
                                $quantity_put = number_unformat($this->input->post('quantity_put')[$value][$k]);
                                $quantity_loss_new = number_unformat($this->input->post('quantity_loss_new')[$value][$k]);
                                $orderItemsColumnsNewVS1[$k] = (array)$val;
                                $orderItemsColumnsNewVS1[$k]['quantity_put'] = $quantity_put;
                                $orderItemsColumnsNewVS1[$k]['quantity_loss_new'] = $quantity_loss_new;
                            }
                        }
                    }
                    if ($quantity_max > 0) {
                        if (empty($orderItemsColumnsNewVS1)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng nhập chi tiết đơn đặt');
                            echo json_encode($data);
                            die;
                        }
                    }
                    $items_check = array_merge($items_check, $orderItemsColumnsNewVS1);
                    //end

                    $info = $this->products_model->rowProduct($item_id);
                    $conversion_quantity_unit = $info['conversion_quantity_unit'];
                    $unit_id = $order_item['unit_id'];
                    if ($info['unit_id'] == $unit_id) {
                        $quantity_unit = $quantity;
                        $quantity_stock = roundNumberFormat($quantity_unit * $conversion_quantity_unit, 4);
                        $quantity_payment = $quantity_unit;

                        $quantity_unit_loss = $quantity_loss_delivery;
                        $quantity_stock_loss = roundNumberFormat($quantity_unit_loss * $conversion_quantity_unit, 4);
                        $quantity_payment_loss = $quantity_unit_loss;

                        $quantity_unit_sample = $quantity_sample;
                        $quantity_stock_sample = roundNumberFormat($quantity_unit_sample * $conversion_quantity_unit,
                            4);
                        $quantity_payment_sample = $quantity_unit_sample;
                    } else {
                        $quantity_stock = $quantity;
                        $quantity_unit = roundNumberFormat($quantity_stock / $conversion_quantity_unit, 4);
                        $quantity_payment = $quantity_stock;

                        $quantity_stock_loss = $quantity_loss_delivery;
                        $quantity_unit_loss = roundNumberFormat($quantity_stock_loss / $conversion_quantity_unit, 4);
                        $quantity_payment_loss = $quantity_stock_loss;

                        $quantity_stock_sample = $quantity_sample;
                        $quantity_unit_sample = roundNumberFormat($quantity_stock_sample / $conversion_quantity_unit,
                            4);
                        $quantity_payment_sample = $quantity_stock_sample;
                    }

                    //new
                    $conversion_quantity_unit_new = 1;

                    $info = $this->products_model->rowProduct($item_id);
                    if ($order_item['unit_id'] == $info['unit_id']) {
                        $conversion_quantity_unit_new = $info['conversion_quantity_unit'];
                    }

                    $this->db->select("
                        tblwarehouse_items.product_quantity as quantity_warehouse,
                    ");
                    $this->db->from('tblwarehouse_items');
                    $this->db->where('tblwarehouse_items.warehouse_id', $warehouses);
                    $this->db->where('tblwarehouse_items.localtion', $locations);
                    $this->db->where('tblwarehouse_items.id_items', $item_id);
                    $this->db->where('tblwarehouse_items.type_items', 'product');
                    if (!empty($lot_code)) {
                        $this->db->where('tblwarehouse_items.lot_code', $lot_code);
                    } else {
                        $this->db->where('tblwarehouse_items.lot_code IS NULL');
                    }
                    if (!empty($date_sx)) {
                        $this->db->where('tblwarehouse_items.date_sx', $date_sx);
                    } else {
                        $this->db->where('tblwarehouse_items.date_sx IS NULL');
                    }
                    if (!empty($date_sd)) {
                        $this->db->where('tblwarehouse_items.date_sd', $date_sd);
                    } else {
                        $this->db->where('tblwarehouse_items.date_sd IS NULL');
                    }
                    if (!empty($date_use)) {
                        $this->db->where('tblwarehouse_items.date_use', $date_use);
                    } else {
                        $this->db->where('tblwarehouse_items.date_use IS NULL');
                    }
                    $quantityWarehouse = $this->db->get()->row_array()['quantity_warehouse'];
                    $quantityWarehouse = $quantityWarehouse / $conversion_quantity_unit_new;
                    if ($quantityWarehouse < ($quantity + $quantity_loss_delivery + $quantity_sample)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mặt hàng '.$items_code.'('.$items_name.') '.'Số lượng trong kho không đủ!');
                        echo json_encode($data);
                        die;
                    }
                    //end


                    if (!empty($arr_info[$order_item_id])) {
                        $arr_info[$order_item_id]['quantity_delivery'] = $arr_info[$order_item_id]['quantity_delivery'] + $quantity;
                        $arr_info[$order_item_id]['quantity_delivery_loss'] = $arr_info[$order_item_id]['quantity_delivery_loss'] + $quantity_loss_delivery;
                        $arr_info[$order_item_id]['quantity_delivery_sample'] = $arr_info[$order_item_id]['quantity_delivery_sample'] + $quantity_sample;
                    } else {
                        $arr_info[$order_item_id] = [
                            'items_code' => $items_code,
                            'items_name' => $items_name,
                            'quantity_delivery' => $quantity,
                            'quantity_had_delivery' => $order_item['quantity'] - $order_item['quantity_delivery'],
                            'quantity_delivery_loss' => $quantity_loss_delivery,
                            'quantity_had_delivery_loss' => $quantity_order_loss - $quantity_had_delivery_loss,
                            'quantity_delivery_sample' => $quantity_sample,
                            'quantity_had_delivery_sample' => $quantity_order_sample - $quantity_had_delivery_sample,
                            'item_id' => $item_id,
                        ];
                    }

                    $items_in[] = [
                        'order_item_id' => $order_item_id,
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $items_code,
                        'item_name' => $items_name,
                        'quantity' => $quantity,
                        'quantity_loss' => $quantity_loss_delivery,
                        'quantity_sample' => $quantity_sample,
                        'price' => $price,
                        'amount' => $amount,
                        'tax_id_item' => $tax_id_item,
                        'tax_name_item' => $tax_name_item,
                        'tax_rate_item' => $tax_rate_item,
                        'tax_amount_item' => $tax_amount_item,
                        'discount_percent_item' => $discount_percent_item,
                        'discount_percent_amount_item' => $discount_percent_amount_item,
                        'discount_direct_amount_item' => $discount_direct_amount_item,
                        'total_amount' => $grand_total_item,
                        'note_item' => $note_item,
                        'warehouse_id' => $warehouses,
                        'location_id' => $locations,
                        'date_sx' => !empty($date_sx) ? ($date_sx) : null,
                        'date_sd ' => !empty($date_sd) ? ($date_sd) : null,
                        'date_use ' => !empty($date_use) ? $date_use : null,
                        'lot_code ' => !empty($lot_code) ? $lot_code : null,
                        'orderItemsColumnsNew' => $orderItemsColumnsNewVS1,
                        'unit_id' => $unit_id,
                        'conversion_quantity_unit' => $conversion_quantity_unit,
                        'quantity_unit' => ($quantity_unit + $quantity_unit_loss + $quantity_unit_sample),
                        'quantity_stock' => ($quantity_stock + $quantity_stock_loss + $quantity_stock_sample),
                        'quantity_payment' => ($quantity_payment + $quantity_payment_loss + $quantity_payment_sample),
                        'quantity_unit_loss' => $quantity_unit_loss,
                        'quantity_stock_loss' => $quantity_stock_loss,
                        'quantity_payment_loss' => $quantity_payment_loss,
                        'quantity_unit_sample' => $quantity_unit_sample,
                        'quantity_stock_sample' => $quantity_stock_sample,
                        'quantity_payment_sample' => $quantity_payment_sample,
                        'check_loss' => $check_loss,
                    ];

                    $total_quantity += $quantity;
                    $total_amount_items += $amount;
                    $grand_total_items += $grand_total_item;
                }

                if (!empty($arr_info)) {
                    foreach ($arr_info as $key => $value) {
                        $items_code = $value['items_code'];
                        $items_name = $value['items_name'];
                        $quantity_had_delivery = $value['quantity_had_delivery'];
                        $quantity_delivery = $value['quantity_delivery'];
                        $quantity_delivery_loss = $value['quantity_delivery_loss'];
                        $quantity_had_delivery_loss = $value['quantity_had_delivery_loss'];
                        $quantity_delivery_sample = $value['quantity_delivery_sample'];
                        $quantity_had_delivery_sample = $value['quantity_had_delivery_sample'];

                        if ($quantity_delivery > $quantity_had_delivery) {
                            $errorItems .= '<div>'.$items_code.'('.$items_name.') '.lang('tnh_quantity_delivery_less').' '.$quantity_had_delivery.'</div>';
                        }

                        if ($quantity_delivery_loss > $quantity_had_delivery_loss) {
                            $errorItems .= '<div>'.$items_code.'('.$items_name.') '.lang('SL loss phải nhỏ hơn hoặc bằng').' '.$quantity_had_delivery_loss.'</div>';
                        }

                        if ($quantity_delivery_sample > $quantity_had_delivery_sample) {
                            $errorItems .= '<div>'.$items_code.'('.$items_name.') '.lang('SL mẫu phải nhỏ hơn hoặc bằng').' '.$quantity_had_delivery_sample.'</div>';
                        }
                    }
                }

                $items_check_new = [];
                if (!empty($items_check)) {
                    foreach ($items_check as $key => $value) {
                        $check_exists = $value['order_item_id'].'__'.$value['counter_items_number'];
                        if (!empty($value['quantity_loss_new'])) {
                            if (!empty($items_check_new[$check_exists])) {
                                $items_check_new[$check_exists]['count'] += 1;
                            } else {
                                $items_check_new[$check_exists] = [
                                    'code' => $value['code'],
                                    'command' => $value['command'],
                                    'count' => 1,
                                ];
                            }
                        }
                    }
                }
                if (!empty($items_check_new)) {
                    foreach ($items_check_new as $key => $value) {
                        $code = $value['code'];
                        $command = $value['command'];
                        $count = $value['count'];
                        if ($count > 1) {
                            $errorItems .= '<div>Mã đơn đặt '.$code.' chỉ lệnh ('.$command.') chỉ được giao loss 1 lần</div>';
                        }
                    }
                }

                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if (empty($items_in)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }


                $count_items = count($items_in);
                $grand_total = $grand_total_items;


                if ($discount_percent > 0) {
                    $total_discount_percent = $grand_total * ($discount_percent / 100);
                }

                $grand_total -= $total_discount_percent;
                $grand_total -= $total_discount_direct;

                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }
                $grand_total += $total_tax;

                $options = [
                    'date' => $date,
                    'reference_no' => getReference('deliveries'),
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery,
                    'employee_id' => $employee_id,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'total_amount_items' => $total_amount_items,
                    'total_tax_items' => $total_tax_items,
                    'total_discount_percent_items' => $total_discount_percent_items,
                    'total_discount_direct_items' => $total_discount_direct_items,
                    'grand_total_items' => $grand_total_items,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'order_id' => $id,
                    'type_id' => 1,
                    'person_contact_id' => $person_contact_id,
                    'noti_phone' => $noti_phone,
                    'noti_email' => $noti_email,
                    'noti_zalo' => $noti_zalo,
                    'noti_note_other' => $noti_note_other,
                    'id_branch' => $id_branch,
                ];

                $ordersDeliveries = [
                    'order_id' => $id,
                    'tax_id' => $tax_id,
                    'total_quantity' => $total_quantity,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                ];

            //print_arrays($items_in);

                $delivery_id = $this->deliveries_model->insertDeliveries($options);
                if ($delivery_id) {
                    updateReference('deliveries');
                    //insert order delivery
                    $ordersDeliveries['delivery_id'] = $delivery_id;
                    $this->deliveries_model->insertOrdersDeliveries($ordersDeliveries);
                    //end insert order delivery
                    $total_quantity_had_delivery = 0;
                    $total_quantity_not_delivery = 0;
                    foreach ($items_in as $key => $value) {
                        $value['delivery_id'] = $delivery_id;
                        $orderItemsColumnsNew = $value['orderItemsColumnsNew'];
                        unset($value['orderItemsColumnsNew']);
                        $delivery_item_id = $this->deliveries_model->insertDeliveryItems($value);
                        if ($delivery_item_id) {
                            // $order_item = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                            // $quantity_delivery = $order_item['quantity_delivery'] + $value['quantity'];
                            // $quantity_not_delivery = $order_item['quantity'] - $quantity_delivery;
                            // $upOrderItem = $this->orders_model->updateOrderItemNew($value['order_item_id'], ['quantity_delivery' => $quantity_delivery, 'quantity_not_delivery' => $quantity_not_delivery]);
                            // if ($upOrderItem) {
                            //     $total_quantity_had_delivery += $quantity_delivery;
                            //     $total_quantity_not_delivery += $quantity_not_delivery;
                            // }
                            //

                            //dt
                            $productsColumns = $this->products_model->getProductsColumns($value['item_id']);
                            $arrInsert = [];
                            if (!empty($orderItemsColumnsNew)) {
                                foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                                    if (!empty($productsColumns)) {
                                        foreach ($productsColumns as $kk => $vv) {
                                            $name_check = ($vv['name']);
                                            if (!empty($vvv[$name_check])) {
                                                $columns_name_new = $vvv[$name_check];
                                                $arrInsert[] = [
                                                    'columns_name' => $columns_name_new,
                                                    'columns_value' => $vv['name'],
                                                    'order_item_id' => $vvv['order_item_id'],
                                                    'order_id' => $vvv['order_id'],
                                                    'counter_item' => $vvv['counter_item'],
                                                    'columns_id' => $vvv['columns_id'],
                                                    'counter_items_number' => $vvv['counter_items_number'],
                                                ];
                                            }
                                        }
                                    }
                                    $arrInsert[] = [
                                        'columns_value' => 'order_code',
                                        'columns_name' => $vvv['code'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'command',
                                        'columns_name' => $vvv['command'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'quantity_put',
                                        'columns_name' => $vvv['quantity_put'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'quantity_loss',
                                        'columns_name' => $vvv['quantity_loss'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'sample_quantity_item',
                                        'columns_name' => $vvv['sample_quantity_item'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'quantity_loss_new',
                                        'columns_name' => $vvv['quantity_loss_new'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                }
                            }

                            if (!empty($arrInsert)) {
                                foreach ($arrInsert as $kk => $vv) {
                                    $vv['delivery_id'] = $delivery_id;
                                    $vv['delivery_item_id'] = $delivery_item_id;
                                    $this->db->insert('tbl_delivery_items_columns', $vv);
                                }
                            }

                            //end
                        }
                    }

                    $count_delivery = $order['count_delivery'] + 1;
                    $this->orders_model->updateOrdersNew($id, [
                        'count_delivery' => $count_delivery,
                        // 'total_quantity_had_delivery' => $total_quantity_had_delivery,
                        // 'total_quantity_not_delivery' => $total_quantity_not_delivery,
                    ]);

                    $this->orders_model->updateOrdersDelivery($id);

                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_his_convert_delivery_orders').' ['.$order['reference_no'].']',
                        'actions' => 'convert_delivery',
                    ]);
                    $this->db->select('tbl_delivery_items.quantity,tbl_delivery_items.id,tbl_delivery_items.item_code,tbl_delivery_items.item_name,tbl_deliveries.date,tbl_deliveries.reference_no,tblclients.company,tblclients.zcode,tbl_orders.reference_no as code_orders,tbl_deliveries.warehouseman_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tbl_deliveries.employee_id,tblunits.unit as unit_name');
                    $this->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
                    $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
                    $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
                    $this->db->join('tblstaff', 'tblstaff.staffid=tbl_deliveries.employee_id', 'left');
                    $this->db->join('tbl_products', 'tbl_products.id=tbl_delivery_items.item_id', 'left');
                    $this->db->join('tblunits', 'tblunits.unitid=tbl_products.unit_id', 'left');
                    $this->db->where('tbl_deliveries.id', $delivery_id);
                    $rows = $this->db->get('tbl_delivery_items')->result_array();
                    foreach ($rows as $key => $value) {
                        $updatedRow = $this->_api_row_export_delivery($value);
                        sendSocket([
                            'action'     => 'add',
                            'newRow' => $updatedRow,
                        ], [], 'ExportDeliveryloadProgress');
                    }
                    $data['delivery_id'] = $delivery_id;
                    $data['type'] = $type;
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['items'] = $items;
            $warehouses = $this->site_model->getWarehouse();
            $data['warehouses'] = $warehouses;

            $data['branch'] = $this->db->get('tblbranch')->result_array();
            $data['staff'] = $this->site_model->getStaff();
            $data['customer_id'] = 'customers__'.$order['customer_id'];
            $data['id'] = $id;
            $data['order'] = $order;
            $this->load->view('admin/orders/convert_delivery', $data);
        }
    }
    private function _api_row_export_delivery($r)
    {
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _dt($r['date']),
            'code_orders' => $r['code_orders'],
            'reference_no' => $r['reference_no'],
            'item_code'        => $r['item_code'],
            'item_name'        => $r['item_name'],
            'unit_name'        => $r['unit_name'],
            'zcode'        => $r['zcode'],
            'company'        => $r['company'],
            'quantity'   => (int) $r['quantity'],
            'warehouseman_id'   => $r['warehouseman_id'],
            'fullname_employee'   => (int) $r['fullname_employee'],
            'image_employee'   => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ], 'thumb') . '<br><span>' . $r['fullname_employee'] . '</span>'
        ];
    }
    public function convert_delivery_new($id)
    {
        if (!$this->perAddOrders) {
            accessDenied($js = true);
        }
        $order = $this->orders_model->rowOrderById($id);
        // $items = $this->orders_model->getOrderItemsByOrderId($id);
        $items = $this->orders_model->getOrderItemsDelivery($id);

        if ($order['status'] != "approved") {
            refererModel(lang('tnh_please_approved'));
        }
        $customer_id = $order['customer_id'];
        $customer_name = $order['customer_name'];

        if ($this->input->post('save')) {
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('staff_admin', lang("tnh_employees_charge"), 'required');
            $this->form_validation->set_rules('address_delivery', lang("tnh_address_delivery"), 'required');
            // $this->form_validation->set_rules('person_contact', lang("tnh_person_contact"), 'required');
            $this->form_validation->set_rules('id_branch', lang("id_branch"), 'required');
            $this->form_validation->set_rules('warehouses[]', lang("tnh_warehouses"), 'required');
            // $this->form_validation->set_rules('locations[]', lang("tnh_location_warehouse"), 'required');
            if ($this->form_validation->run() == true) {
//                print_arrays($this->input->post());
                $date = to_sql_date($this->input->post('date'), true);
                $address_delivery = $this->input->post('address_delivery');
                $address_delivery = str_replace('customers__', '', $address_delivery);
                $employee_id = $this->input->post('staff_admin');
                $note = $this->input->post('note');

                $type = $this->input->post('type');

                $person_contact_id = $this->input->post('person_contact');
                $person_contact_id = str_replace('customers__', '', $person_contact_id);
                $noti_phone = $this->input->post('noti_phone');
                $noti_phone = !empty($noti_phone) ? implode(',', $noti_phone) : null;
                $noti_email = $this->input->post('noti_email');
                $noti_email = !empty($noti_email) ? implode(',', $noti_email) : null;
                $noti_zalo = $this->input->post('noti_zalo');
                $noti_zalo = !empty($noti_zalo) ? implode(',', $noti_zalo) : null;
                $noti_note_other = $this->input->post('noti_note_other');
                $noti_note_other = !empty($noti_note_other) ? implode(',', $noti_note_other) : null;
                $id_branch = $this->input->post('id_branch');

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $tax_id = $order['tax_id'];
                $tax_name = $order['tax_name'];
                $tax_rate = $order['tax_rate'];
                $total_tax = 0;
                $discount_percent = $order['discount_percent'];
                $total_discount_percent = 0;
                if ($order['count_delivery'] == 0) {
                    $total_discount_direct = $order['total_discount_direct'];
                } else {
                    $total_discount_direct = 0;
                }

                $grand_total = 0;
                $status = 'un_approved';
                $errorItems = '';

                $counter = $this->input->post('counter');
                $arr_id = [];
                $arr_info = [];
                $items_check = [];
                foreach ($counter as $key => $value) {
                    $order_item_id = $this->input->post('order_item_id')[$value];
                    if (empty($order_item_id)) {
                        continue;
                    }
                    $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                    if (empty($order_item)) {
                        continue;
                    }

                    $check_loss = !empty($this->input->post('check_loss')[$value]) ? $this->input->post('check_loss')[$value] : 0;

                    $item_id = $order_item['item_id'];
                    $items_code = $order_item['item_code'];
                    $items_name = $order_item['item_name'];
                    $type_item = $order_item['type_item'];
                    $quantity_delivery = number_unformat($this->input->post('quantity_delivery')[$value]);
                    $quantity = $quantity_delivery;

                    $quantity_loss_delivery = number_unformat($this->input->post('quantity_loss')[$value]);
                    $quantity_order_loss = ($order_item['quantity_loss']);

                    $this->db->select('SUM(tbl_delivery_items.quantity_loss) as quantity_loss');
                    $this->db->from('tbl_delivery_items');
                    $this->db->where('order_item_id', $order_item['id']);
                    $quantity_had_delivery_loss = $this->db->get()->row_array()['quantity_loss'];

                    $quantity_sample = number_unformat($this->input->post('quantity_sample')[$value]);
                    $quantity_order_sample = ($order_item['sample_quantity']);

                    $this->db->select('SUM(tbl_delivery_items.quantity_sample) as quantity_sample');
                    $this->db->from('tbl_delivery_items');
                    $this->db->where('order_item_id', $order_item['id']);
                    $quantity_had_delivery_sample = $this->db->get()->row_array()['quantity_sample'];

                    $note_item = $this->input->post('note_item')[$value];

                    //check quantity delivery
                    $quantity_order = $order_item['quantity'];
                    $quantity_had_delivery = $order_item['quantity_delivery'];
                    $quantity_max = $quantity_order - $quantity_had_delivery;

                    $dataWarehouses = $this->input->post('warehouses')[$value];
                    $dataWarehouses = explode('__', $dataWarehouses);
                    $warehouses = $dataWarehouses[0];
                    $locations = $dataWarehouses[1];
                    $lot_code = $dataWarehouses[2];
                    $date_sx = $dataWarehouses[3];
                    $date_sd = $dataWarehouses[4];
                    $date_use = $dataWarehouses[5];
                    if (empty($warehouses) || empty($locations)) {
                        $data['result'] = 0;
                        $data['message'] = 'Vui lòng chọn kho hàng ?';
                        echo json_encode($data);
                        die;
                    }


                    // $warehouses = $this->input->post('warehouses')[$value];
                    // $locations = $this->input->post('locations')[$value];


                    // if ($quantity_delivery > $quantity_max) {
                    //     $errorItems.= lang('tnh_quantity_delivery_have_change_please_referesh');
                    //     break;
                    // }

                    $price = $order_item['price'];
                    $is_lot = $order_item['is_lot'];
                    if ($is_lot == 1) {
                        $amount = $price;
                    } else {
                        $amount = $quantity * $price;
                    }
                    // $amount = $quantity * $price;
                    $grand_total_item = $amount;
                    $tax_id_item = $order_item['tax_id_item'];
                    $tax_name_item = $order_item['tax_name_item'];
                    $tax_rate_item = $order_item['tax_rate_item'];
                    $tax_amount_item = 0;

                    if ($tax_rate_item > 0) {
                        $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                        $total_tax_items += $tax_amount_item;
                        $grand_total_item += $tax_amount_item;
                    }

                    //discount percent item
                    $discount_percent_item = number_unformat($order_item['discount_percent_item']);
                    $discount_percent_amount_item = 0;
                    if ($discount_percent_item > 0) {
                        $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                        $total_discount_percent_items += $discount_percent_amount_item;
                        $grand_total_item -= $discount_percent_amount_item;
                    }
                    //end
                    if ($order['count_delivery'] == 0) {
                        $discount_direct_amount_item = $order_item['discount_direct_amount_item'];
                    } else {
                        $discount_direct_amount_item = 0;
                    }

                    $total_discount_direct_items += $discount_direct_amount_item;
                    $grand_total_item -= $discount_direct_amount_item;


                    if (($quantity + $quantity_loss_delivery + $quantity_sample) <= 0) {
                        $data['result'] = 0;
                        $data['message'] = 'Mặt hàng '.$items_code.'('.$items_name.') '.'Số lượng giao hàng lớn hơn 0';
                        echo json_encode($data);
                        die;
                    }
                    //dt
                    $orderItemsColumnsNewVS1 = [];
                    if ($type_item == "products") {
                        $json = !empty($this->input->post('json')[$value]) ? $this->input->post('json')[$value] : false;
                        if (!empty($json)) {
                            foreach ($json as $k => $val) {
                                $val = json_decode($val);
                                if (empty($val)) {
                                    continue;
                                }
                                $quantity_put = number_unformat($this->input->post('quantity_put')[$value][$k]);
                                $quantity_loss_new = number_unformat($this->input->post('quantity_loss_new')[$value][$k]);
                                $orderItemsColumnsNewVS1[$k] = (array)$val;
                                $orderItemsColumnsNewVS1[$k]['quantity_put'] = $quantity_put;
                                $orderItemsColumnsNewVS1[$k]['quantity_loss_new'] = $quantity_loss_new;
                            }
                        }
                    }
                    if ($quantity_max > 0) {
                        if (empty($orderItemsColumnsNewVS1)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng nhập chi tiết đơn đặt');
                            echo json_encode($data);
                            die;
                        }
                    }
                    $items_check = array_merge($items_check, $orderItemsColumnsNewVS1);
                    //end

                    $info = $this->products_model->rowProduct($item_id);
                    $conversion_quantity_unit = $info['conversion_quantity_unit'];
                    $unit_id = $order_item['unit_id'];
                    if ($info['unit_id'] == $unit_id) {
                        $quantity_unit = $quantity;
                        $quantity_stock = roundNumberFormat($quantity_unit * $conversion_quantity_unit, 4);
                        $quantity_payment = $quantity_unit;

                        $quantity_unit_loss = $quantity_loss_delivery;
                        $quantity_stock_loss = roundNumberFormat($quantity_unit_loss * $conversion_quantity_unit, 4);
                        $quantity_payment_loss = $quantity_unit_loss;

                        $quantity_unit_sample = $quantity_sample;
                        $quantity_stock_sample = roundNumberFormat($quantity_unit_sample * $conversion_quantity_unit,
                            4);
                        $quantity_payment_sample = $quantity_unit_sample;
                    } else {
                        $quantity_stock = $quantity;
                        $quantity_unit = roundNumberFormat($quantity_stock / $conversion_quantity_unit, 4);
                        $quantity_payment = $quantity_stock;

                        $quantity_stock_loss = $quantity_loss_delivery;
                        $quantity_unit_loss = roundNumberFormat($quantity_stock_loss / $conversion_quantity_unit, 4);
                        $quantity_payment_loss = $quantity_stock_loss;

                        $quantity_stock_sample = $quantity_sample;
                        $quantity_unit_sample = roundNumberFormat($quantity_stock_sample / $conversion_quantity_unit,
                            4);
                        $quantity_payment_sample = $quantity_stock_sample;
                    }

                    //new
                    $conversion_quantity_unit_new = 1;

                    $info = $this->products_model->rowProduct($item_id);
                    if ($order_item['unit_id'] == $info['unit_id']) {
                        $conversion_quantity_unit_new = $info['conversion_quantity_unit'];
                    }

                    $this->db->select("
                        tblwarehouse_items.product_quantity as quantity_warehouse,
                    ");
                    $this->db->from('tblwarehouse_items');
                    $this->db->where('tblwarehouse_items.warehouse_id', $warehouses);
                    $this->db->where('tblwarehouse_items.localtion', $locations);
                    $this->db->where('tblwarehouse_items.id_items', $item_id);
                    $this->db->where('tblwarehouse_items.type_items', 'product');
                    if (!empty($lot_code)) {
                        $this->db->where('tblwarehouse_items.lot_code', $lot_code);
                    } else {
                        $this->db->where('tblwarehouse_items.lot_code IS NULL');
                    }
                    if (!empty($date_sx)) {
                        $this->db->where('tblwarehouse_items.date_sx', $date_sx);
                    } else {
                        $this->db->where('tblwarehouse_items.date_sx IS NULL');
                    }
                    if (!empty($date_sd)) {
                        $this->db->where('tblwarehouse_items.date_sd', $date_sd);
                    } else {
                        $this->db->where('tblwarehouse_items.date_sd IS NULL');
                    }
                    if (!empty($date_use)) {
                        $this->db->where('tblwarehouse_items.date_use', $date_use);
                    } else {
                        $this->db->where('tblwarehouse_items.date_use IS NULL');
                    }
                    $quantityWarehouse = $this->db->get()->row_array()['quantity_warehouse'];
                    $quantityWarehouse = $quantityWarehouse / $conversion_quantity_unit_new;
                    if ($quantityWarehouse < ($quantity + $quantity_loss_delivery + $quantity_sample)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mặt hàng '.$items_code.'('.$items_name.') '.'Số lượng trong kho không đủ!');
                        echo json_encode($data);
                        die;
                    }
                    //end


                    if (!empty($arr_info[$order_item_id])) {
                        $arr_info[$order_item_id]['quantity_delivery'] = $arr_info[$order_item_id]['quantity_delivery'] + $quantity;
                        $arr_info[$order_item_id]['quantity_delivery_loss'] = $arr_info[$order_item_id]['quantity_delivery_loss'] + $quantity_loss_delivery;
                        $arr_info[$order_item_id]['quantity_delivery_sample'] = $arr_info[$order_item_id]['quantity_delivery_sample'] + $quantity_sample;
                    } else {
                        $arr_info[$order_item_id] = [
                            'items_code' => $items_code,
                            'items_name' => $items_name,
                            'quantity_delivery' => $quantity,
                            'quantity_had_delivery' => $order_item['quantity'] - $order_item['quantity_delivery'],
                            'quantity_delivery_loss' => $quantity_loss_delivery,
                            'quantity_had_delivery_loss' => $quantity_order_loss - $quantity_had_delivery_loss,
                            'quantity_delivery_sample' => $quantity_sample,
                            'quantity_had_delivery_sample' => $quantity_order_sample - $quantity_had_delivery_sample,
                            'item_id' => $item_id,
                        ];
                    }

                    $items_in[] = [
                        'order_item_id' => $order_item_id,
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $items_code,
                        'item_name' => $items_name,
                        'quantity' => $quantity,
                        'quantity_loss' => $quantity_loss_delivery,
                        'quantity_sample' => $quantity_sample,
                        'price' => $price,
                        'amount' => $amount,
                        'tax_id_item' => $tax_id_item,
                        'tax_name_item' => $tax_name_item,
                        'tax_rate_item' => $tax_rate_item,
                        'tax_amount_item' => $tax_amount_item,
                        'discount_percent_item' => $discount_percent_item,
                        'discount_percent_amount_item' => $discount_percent_amount_item,
                        'discount_direct_amount_item' => $discount_direct_amount_item,
                        'total_amount' => $grand_total_item,
                        'note_item' => $note_item,
                        'warehouse_id' => $warehouses,
                        'location_id' => $locations,
                        'date_sx' => !empty($date_sx) ? ($date_sx) : null,
                        'date_sd ' => !empty($date_sd) ? ($date_sd) : null,
                        'date_use ' => !empty($date_use) ? $date_use : null,
                        'lot_code ' => !empty($lot_code) ? $lot_code : null,
                        'orderItemsColumnsNew' => $orderItemsColumnsNewVS1,
                        'unit_id' => $unit_id,
                        'conversion_quantity_unit' => $conversion_quantity_unit,
                        'quantity_unit' => ($quantity_unit + $quantity_unit_loss + $quantity_unit_sample),
                        'quantity_stock' => ($quantity_stock + $quantity_stock_loss + $quantity_stock_sample),
                        'quantity_payment' => ($quantity_payment + $quantity_payment_loss + $quantity_payment_sample),
                        'quantity_unit_loss' => $quantity_unit_loss,
                        'quantity_stock_loss' => $quantity_stock_loss,
                        'quantity_payment_loss' => $quantity_payment_loss,
                        'quantity_unit_sample' => $quantity_unit_sample,
                        'quantity_stock_sample' => $quantity_stock_sample,
                        'quantity_payment_sample' => $quantity_payment_sample,
                        'check_loss' => $check_loss,
                    ];

                    $total_quantity += $quantity;
                    $total_amount_items += $amount;
                    $grand_total_items += $grand_total_item;
                }

                if (!empty($arr_info)) {
                    foreach ($arr_info as $key => $value) {
                        $items_code = $value['items_code'];
                        $items_name = $value['items_name'];
                        $quantity_had_delivery = $value['quantity_had_delivery'];
                        $quantity_delivery = $value['quantity_delivery'];
                        $quantity_delivery_loss = $value['quantity_delivery_loss'];
                        $quantity_had_delivery_loss = $value['quantity_had_delivery_loss'];
                        $quantity_delivery_sample = $value['quantity_delivery_sample'];
                        $quantity_had_delivery_sample = $value['quantity_had_delivery_sample'];

                        if ($quantity_delivery > $quantity_had_delivery) {
                            $errorItems .= '<div>'.$items_code.'('.$items_name.') '.lang('tnh_quantity_delivery_less').' '.$quantity_had_delivery.'</div>';
                        }

                        if ($quantity_delivery_loss > $quantity_had_delivery_loss) {
                            $errorItems .= '<div>'.$items_code.'('.$items_name.') '.lang('SL loss phải nhỏ hơn hoặc bằng').' '.$quantity_had_delivery_loss.'</div>';
                        }

                        if ($quantity_delivery_sample > $quantity_had_delivery_sample) {
                            $errorItems .= '<div>'.$items_code.'('.$items_name.') '.lang('SL mẫu phải nhỏ hơn hoặc bằng').' '.$quantity_had_delivery_sample.'</div>';
                        }
                    }
                }

                $items_check_new = [];
                if (!empty($items_check)) {
                    foreach ($items_check as $key => $value) {
                        $check_exists = $value['order_item_id'].'__'.$value['counter_items_number'];
                        if (!empty($value['quantity_loss_new'])) {
                            if (!empty($items_check_new[$check_exists])) {
                                $items_check_new[$check_exists]['count'] += 1;
                            } else {
                                $items_check_new[$check_exists] = [
                                    'code' => $value['code'],
                                    'command' => $value['command'],
                                    'count' => 1,
                                ];
                            }
                        }
                    }
                }
                if (!empty($items_check_new)) {
                    foreach ($items_check_new as $key => $value) {
                        $code = $value['code'];
                        $command = $value['command'];
                        $count = $value['count'];
                        if ($count > 1) {
                            $errorItems .= '<div>Mã đơn đặt '.$code.' chỉ lệnh ('.$command.') chỉ được giao loss 1 lần</div>';
                        }
                    }
                }

                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if (empty($items_in)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }


                $count_items = count($items_in);
                $grand_total = $grand_total_items;


                if ($discount_percent > 0) {
                    $total_discount_percent = $grand_total * ($discount_percent / 100);
                }

                $grand_total -= $total_discount_percent;
                $grand_total -= $total_discount_direct;

                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }
                $grand_total += $total_tax;

                $options = [
                    'date' => $date,
                    'reference_no' => getReference('deliveries'),
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery,
                    'employee_id' => $employee_id,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'total_amount_items' => $total_amount_items,
                    'total_tax_items' => $total_tax_items,
                    'total_discount_percent_items' => $total_discount_percent_items,
                    'total_discount_direct_items' => $total_discount_direct_items,
                    'grand_total_items' => $grand_total_items,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'order_id' => $id,
                    'type_id' => 1,
                    'person_contact_id' => $person_contact_id,
                    'noti_phone' => $noti_phone,
                    'noti_email' => $noti_email,
                    'noti_zalo' => $noti_zalo,
                    'noti_note_other' => $noti_note_other,
                    'id_branch' => $id_branch,
                ];

                $ordersDeliveries = [
                    'order_id' => $id,
                    'tax_id' => $tax_id,
                    'total_quantity' => $total_quantity,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                ];

//                 print_arrays($items_in);

                $delivery_id = $this->deliveries_model->insertDeliveries($options);
                if ($delivery_id) {
                    updateReference('deliveries');
                    //insert order delivery
                    $ordersDeliveries['delivery_id'] = $delivery_id;
                    $this->deliveries_model->insertOrdersDeliveries($ordersDeliveries);
                    //end insert order delivery
                    $total_quantity_had_delivery = 0;
                    $total_quantity_not_delivery = 0;
                    foreach ($items_in as $key => $value) {
                        $value['delivery_id'] = $delivery_id;
                        $orderItemsColumnsNew = $value['orderItemsColumnsNew'];
                        unset($value['orderItemsColumnsNew']);
                        $delivery_item_id = $this->deliveries_model->insertDeliveryItems($value);
                        if ($delivery_item_id) {
                            // $order_item = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                            // $quantity_delivery = $order_item['quantity_delivery'] + $value['quantity'];
                            // $quantity_not_delivery = $order_item['quantity'] - $quantity_delivery;
                            // $upOrderItem = $this->orders_model->updateOrderItemNew($value['order_item_id'], ['quantity_delivery' => $quantity_delivery, 'quantity_not_delivery' => $quantity_not_delivery]);
                            // if ($upOrderItem) {
                            //     $total_quantity_had_delivery += $quantity_delivery;
                            //     $total_quantity_not_delivery += $quantity_not_delivery;
                            // }
                            //

                            //dt
                            $productsColumns = $this->products_model->getProductsColumns($value['item_id']);
                            $arrInsert = [];
                            if (!empty($orderItemsColumnsNew)) {
                                foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                                    if (!empty($productsColumns)) {
                                        foreach ($productsColumns as $kk => $vv) {
                                            $name_check = ($vv['name']);
                                            if (!empty($vvv[$name_check])) {
                                                $columns_name_new = $vvv[$name_check];
                                                $arrInsert[] = [
                                                    'columns_name' => $columns_name_new,
                                                    'columns_value' => $vv['name'],
                                                    'order_item_id' => $vvv['order_item_id'],
                                                    'order_id' => $vvv['order_id'],
                                                    'counter_item' => $vvv['counter_item'],
                                                    'columns_id' => $vvv['columns_id'],
                                                    'counter_items_number' => $vvv['counter_items_number'],
                                                ];
                                            }
                                        }
                                    }
                                    $arrInsert[] = [
                                        'columns_value' => 'order_code',
                                        'columns_name' => $vvv['code'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'command',
                                        'columns_name' => $vvv['command'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'quantity_put',
                                        'columns_name' => $vvv['quantity_put'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'quantity_loss',
                                        'columns_name' => $vvv['quantity_loss'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'sample_quantity_item',
                                        'columns_name' => $vvv['sample_quantity_item'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                    $arrInsert[] = [
                                        'columns_value' => 'quantity_loss_new',
                                        'columns_name' => $vvv['quantity_loss_new'],
                                        'order_item_id' => $vvv['order_item_id'],
                                        'order_id' => $vvv['order_id'],
                                        'counter_item' => $vvv['counter_item'],
                                        'columns_id' => $vvv['columns_id'],
                                        'counter_items_number' => $vvv['counter_items_number'],
                                    ];
                                }
                            }

                            if (!empty($arrInsert)) {
                                foreach ($arrInsert as $kk => $vv) {
                                    $vv['delivery_id'] = $delivery_id;
                                    $vv['delivery_item_id'] = $delivery_item_id;
                                    $this->db->insert('tbl_delivery_items_columns', $vv);
                                }
                            }

                            //end
                        }
                    }

                    $count_delivery = $order['count_delivery'] + 1;
                    $this->orders_model->updateOrdersNew($id, [
                        'count_delivery' => $count_delivery,
                        // 'total_quantity_had_delivery' => $total_quantity_had_delivery,
                        // 'total_quantity_not_delivery' => $total_quantity_not_delivery,
                    ]);

                    $this->orders_model->updateOrdersDelivery($id);

                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_his_convert_delivery_orders').' ['.$order['reference_no'].']',
                        'actions' => 'convert_delivery',
                    ]);

                    $data['delivery_id'] = $delivery_id;
                    $data['type'] = $type;
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['items'] = $items;
            $warehouses = $this->site_model->getWarehouse();
            $data['warehouses'] = $warehouses;

            $data['staff'] = $this->site_model->getStaff();
            $data['customer_id'] = 'customers__'.$order['customer_id'];
            $data['id'] = $id;
            $data['order'] = $order;
            $data['branch'] = $this->db->get('tblbranch')->result_array();
            $this->load->view('admin/orders/convert_delivery_new', $data);
        }
    }

    public function getOrderItems()
    {
        $data = [];
        if ($this->input->post()) {
            $order_item_id = $this->input->post('order_item_id');
            if (!empty($order_item_id)) {
                $order_item = $this->orders_model->rowOrderItemsByIdForDelivery($order_item_id);
                $type_item = $order_item['type_item'];
                $item_id = $order_item['item_id'];
                $tv1_text = $order_item['colum_delivery1'];
                $tv2_text = $order_item['colum_delivery2'];
                $orderItemsColumnsNew = [];
                $arrColumsNew = [];
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $order_item['ct_counter_item'];
                    $productsColumns = $this->products_model->getProductsColumns($item_id);

                    $this->db->select('tbl_order_items_columns.*');
                    $this->db->from('tbl_order_items_columns');
                    $this->db->where('tbl_order_items_columns.order_item_id', $order_item['id']);
                    $orderItemsColumns =  $this->db->get()->result_array();
                    $orderItemsColumnsNew = [];
                    if ($ct_counter_item > 0) {
                        for ($i = 0; $i < $ct_counter_item; $i++) {
                            $arrNew = [];
                            foreach ($productsColumns as $k => $v) {
                                $columns_name = [];
                                foreach ($orderItemsColumns as $kO => $vO) {
                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                        $columns_name = [
                                            ($vO['columns_value']) => $vO['columns_name']
                                        ];
                                        break;
                                    }
                                }
                                $arrNew = array_merge($arrNew, $columns_name);
                            }
                            $orderItemsColumnsNew[$i] = $arrNew;
                            $date_ship = '';
                            $order_code = '';
                            $command = '';
                            $quantity_put = '';
                            $quantity_loss = '';
                            $sample_quantity_item = '';
                            foreach ($orderItemsColumns as $kO => $vO) {

                                if ($i == $vO['counter_items_number']) {
                                    $orderItemsColumnsNew[$i]['order_item_id'] = $vO['order_item_id'];
                                    $orderItemsColumnsNew[$i]['order_id'] = $vO['order_id'];
                                    $orderItemsColumnsNew[$i]['counter_item'] = $vO['counter_item'];
                                    $orderItemsColumnsNew[$i]['columns_id'] = $vO['columns_id'];
                                    $orderItemsColumnsNew[$i]['columns_id'] = $vO['columns_id'];
                                    $orderItemsColumnsNew[$i]['counter_items_number'] = $vO['counter_items_number'];
                                }

                                if ($vO['columns_value'] == 'date_ship' && $i == $vO['counter_items_number']) {
                                    $date_ship = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['date_ship'] = $date_ship;
                                    continue;
                                }
                                else if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                    $order_code = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['code'] = $order_code;
                                    continue;
                                }
								else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                    $command = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['command'] = $command;
                                    continue;
                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                    $quantity_put = $vO['columns_name'];
                                    $this->db->select('SUM(columns_name) as quantity_put');
                                    $this->db->from('tbl_delivery_items_columns');
                                    $this->db->where('columns_value',$vO['columns_value']);
                                    $this->db->where('order_item_id',$orderItemsColumnsNew[$i]['order_item_id']);
                                    $this->db->where('order_id',$orderItemsColumnsNew[$i]['order_id']);
                                    $this->db->where('counter_item',$orderItemsColumnsNew[$i]['counter_item']);
                                    $this->db->where('columns_id',$orderItemsColumnsNew[$i]['columns_id']);
                                    $this->db->where('counter_items_number',$orderItemsColumnsNew[$i]['counter_items_number']);
                                    $dtColumDelivery = $this->db->get()->row_array()['quantity_put'];
                                    $quantity_put = $quantity_put - $dtColumDelivery;
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;

                                    continue;
                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                    $quantity_loss = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                    continue;
                                } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                    $sample_quantity_item = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                    continue;
                                }

                            }
                        }
                    }
                }
                $orderItemsColumnsNewVs1 = [];
                if(!empty($orderItemsColumnsNew)){
                    foreach ($orderItemsColumnsNew as $kk => $vv){
                        if ($vv['quantity_put'] <= 0){
                            continue;
                        }
                        $check_key = $vv['code'].'__'.$vv['command'];
                        if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                            $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vv['quantity_put'];
                            $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vv['quantity_loss'];
                            $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vv['sample_quantity_item'];
                            $orderItemsColumnsNewVs1[$check_key]['id_check'] .= '__'.$vv['counter_items_number'];
                        } else {
                            $orderItemsColumnsNewVs1[$check_key] = $vv;
                            $orderItemsColumnsNewVs1[$check_key]['id_check'] = $vv['order_item_id'].'__'.$vv['counter_items_number'];
                        }
                    }
                }
                $order_item['orderItemsColumnsNew'] = array_values($orderItemsColumnsNewVs1);
                foreach ($productsColumns as $k => $v) {
                    if ($tv1_text == $v['name']) {
                        $arrColumsNew[] = $v['name'];
                    }
                    if ($tv2_text == $v['name']) {
                        $arrColumsNew[] = $v['name'];
                    }
                }
                $order_item['arrColumsNew'] = array_values($arrColumsNew);

                $dtUnit = $this->unit_model->rowUnit($order_item['unit_id']);
                $unit_name = $dtUnit['unit'];
                $order_item['unit_name'] = $unit_name;
                $data['order_item'] = $order_item;
               
            }

            $order_id = $this->input->post('order_id');
            if (!empty($order_id)) {
                $items = $this->orders_model->getOrderItemsDelivery($order_id);
                if (!empty($items)){
                    foreach ($items as $key => $value){
                        $type_item = $value['type_item'];
                        $item_id = $value['item_id'];
                        $tv1_text = $value['colum_delivery1'];
                        $tv2_text = $value['colum_delivery2'];
                        $orderItemsColumnsNew = [];
                        $arrColumsNew = [];
                        if ($type_item == "products") {
                            $thSub = '';
                            $trHtmlChild = '';
                            $ct_counter_item = $value['ct_counter_item'];
                            $productsColumns = $this->products_model->getProductsColumns($item_id);

                            $this->db->select('tbl_order_items_columns.*');
                            $this->db->from('tbl_order_items_columns');
                            $this->db->where('tbl_order_items_columns.order_item_id', $value['id']);
                            $orderItemsColumns =  $this->db->get()->result_array();
                            $orderItemsColumnsNew = [];
                            if ($ct_counter_item > 0) {
                                for ($i = 0; $i < $ct_counter_item; $i++) {
                                    $arrNew = [];
                                    foreach ($productsColumns as $k => $v) {
                                        $columns_name = [];
                                        foreach ($orderItemsColumns as $kO => $vO) {
                                            if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                $columns_name = [
                                                    ($vO['columns_value']) => $vO['columns_name']
                                                ];
                                                break;
                                            }
                                        }
                                        $arrNew = array_merge($arrNew, $columns_name);

                                    }
                                    $orderItemsColumnsNew[$i] = $arrNew;
                                    $date_ship = '';
                                    $order_code = '';
                                    $command = '';
                                    $quantity_put = '';
                                    $quantity_loss = '';
                                    $sample_quantity_item = '';
                                    foreach ($orderItemsColumns as $kO => $vO) {

                                        if ($i == $vO['counter_items_number']) {
                                            $orderItemsColumnsNew[$i]['order_item_id'] = $vO['order_item_id'];
                                            $orderItemsColumnsNew[$i]['order_id'] = $vO['order_id'];
                                            $orderItemsColumnsNew[$i]['counter_item'] = $vO['counter_item'];
                                            $orderItemsColumnsNew[$i]['columns_id'] = $vO['columns_id'];
                                            $orderItemsColumnsNew[$i]['counter_items_number'] = $vO['counter_items_number'];
                                        }

                                        if ($vO['columns_value'] == 'date_ship' && $i == $vO['counter_items_number']) {
                                            $date_ship = $vO['columns_name'];
                                            $orderItemsColumnsNew[$i]['date_ship'] = $date_ship;
                                            continue;
                                        }
                                        else if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                            $order_code = $vO['columns_name'];
                                            $orderItemsColumnsNew[$i]['code'] = $order_code;
                                            continue;
                                        }
										else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                            $command = $vO['columns_name'];
                                            $orderItemsColumnsNew[$i]['command'] = $command;
                                            continue;
                                        } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                            $quantity_put = $vO['columns_name'];
                                            $this->db->select('SUM(columns_name) as quantity_put');
                                            $this->db->from('tbl_delivery_items_columns');
                                            $this->db->where('columns_value',$vO['columns_value']);
                                            $this->db->where('order_item_id',$orderItemsColumnsNew[$i]['order_item_id']);
                                            $this->db->where('order_id',$orderItemsColumnsNew[$i]['order_id']);
                                            $this->db->where('counter_item',$orderItemsColumnsNew[$i]['counter_item']);
                                            $this->db->where('columns_id',$orderItemsColumnsNew[$i]['columns_id']);
                                            $this->db->where('counter_items_number',$orderItemsColumnsNew[$i]['counter_items_number']);
                                            $dtColumDelivery = $this->db->get()->row_array()['quantity_put'];
                                            $quantity_put = $quantity_put - $dtColumDelivery;
                                            $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;

                                            continue;
                                        } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                            $quantity_loss = $vO['columns_name'];
                                            $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                            continue;
                                        } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                            $sample_quantity_item = $vO['columns_name'];
                                            $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                            continue;
                                        }

                                    }
                                }
                            }
                        }
                        $orderItemsColumnsNewVs1 = [];
                        if(!empty($orderItemsColumnsNew)){
                            foreach ($orderItemsColumnsNew as $kk => $vv){
                                
                                if ($vv['quantity_put'] <= 0){
                                    continue;
                                }
                                $check_key = $vv['code'].'__'.$vv['command'];
                                if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                    $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vv['quantity_put'];
                                    $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vv['quantity_loss'];
                                    $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vv['sample_quantity_item'];
                                    $orderItemsColumnsNewVs1[$check_key]['id_check'] .= '__'.$vv['counter_items_number'];
                                } else {
                                    $orderItemsColumnsNewVs1[$check_key] = $vv;
                                    $orderItemsColumnsNewVs1[$check_key]['id_check'] = $vv['order_item_id'].'__'.$vv['counter_items_number'];
                                }
                            }
                        }
                        $items[$key]['orderItemsColumnsNew'] = array_values($orderItemsColumnsNewVs1);
                        foreach ($productsColumns as $k => $v) {
                            if ($tv1_text == $v['name']) {
                                $arrColumsNew[] = $v['name'];
                            }
                            if ($tv2_text == $v['name']) {
                                $arrColumsNew[] = $v['name'];
                            }
                        }
                        $items[$key]['arrColumsNew'] = array_values($arrColumsNew);
                        $dtUnit = $this->unit_model->rowUnit($value['unit_id']);
                        $unit_name = $dtUnit['unit'];
                        $items[$key]['unit_name'] = $unit_name;
                    }
                }
                $data['items'] = $items;
            }
        }
        echo json_encode($data);
    }


    //-------------------------------------------------------------------------------------------------------------

    // public function index()
    // {
    //     if (!has_permission('orders', '', 'view')) {
    //         if (!has_permission('import', '', 'create')) {
    //             access_denied('import');
    //         }
    //     }
    //     $data['title']          = _l('cong_orders');
    //     $procedure = get_table_where(db_prefix().'procedure_client', [
    //         'type' => 'orders'
    //     ] ,'', 'row');
    //     $this->db->where('id_detail', $procedure->id);
    //     $this->db->order_by('orders', 'ASC');
    //     $data['procedure_detail'] = $this->db->get(db_prefix().'procedure_client_detail')->result_array();
    //     $this->load->view('admin/orders/manage', $data);
    // }

    public function table()
    {
        if (!has_permission('orders', '', 'view')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('orders');
    }

    public function detail($id = '')
    {
        if ($this->input->post()) {
            $data  = $this->input->post();
            if (empty($id)) {
                $data['note'] = $this->input->post('note', false);
                $success = $this->orders_model->add($data);
                if ($success) {
                    set_alert('success', _l('cong_add_true'));
                    redirect(admin_url('orders'));
                } else {
                    set_alert('danger', _l('cong_add_false'));
                    redirect(admin_url('orders/detail'));
                }
            } else {
                $data['note'] = $this->input->post('note', false);
                $success = $this->orders_model->update($id, $data);
                if ($success) {
                    set_alert('success', _l('cong_update_true'));
                    redirect(admin_url('orders'));
                } else {
                    set_alert('danger', _l('cong_update_false'));
                    redirect(admin_url('orders/detail'));
                }
            }
        } else {
            if (!has_permission('orders', '', 'create')) {
                ajax_access_denied();
            }
            if ($id != '') {
                $data['title']          = _l('cong_update_orders');
                $data['orders'] = $this->orders_model->get($id);
                $data['orders']->name_status = $this->orders_model->get_status_orders($id, $data['orders']->status);
                $data['staff'] = get_table_where(db_prefix() . 'staff', '( active = 1 or staffid = ' . $data['orders']->assigned . ' )');
                $this->db->where('userid', $data['orders']->client);
                $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();

                $data['shipping'] = get_table_where('tblshipping_client', ['client' => $data['orders']->client]);
            } else {

                if ($this->input->get('convert_quotes')) // tại phiếu từ phiếu báo giá
                {
                    $id_quotes = $this->input->get('convert_quotes');
                    $this->db->where('id_quotes_orders', $id_quotes);
                    $orders = $this->db->get(db_prefix() . 'orders')->num_rows();
                    if ($orders == 0) {
                        $data['title']          = _l('cong_add_orders_to_quotes');
                        $data['orders'] = $this->quotes_orders_model->get($id_quotes);
                        if ($data['orders']->status != 2) {
                            $data['orders']->name_status = _l('cong_add_orders_to_quotes');
                            $data['staff'] = get_table_where(db_prefix() . 'staff', '( active = 1 or staffid = ' . $data['orders']->assigned . ' )');
                            $this->db->where('userid', $data['orders']->client);
                            $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();
                            $data['shipping'] = get_table_where('tblshipping_client', ['client' => $data['orders']->client]);
                            $data['convert_quotes'] = $id_quotes;
                        } else {
                            set_alert('danger', _l('cong_quotes_orders_cancel'));
                            redirect(admin_url('quotes_orders'));
                        }
                    } else {
                        set_alert('danger', _l('cong_quotes_orders_isset_orders'));
                        redirect(admin_url('quotes_orders'));
                    }
                } else {
                    //Lấy khách hàng
                    $this->db->limit(10);
                    $data['clients'] = $this->db->get(db_prefix() . 'clients')->result_array();
                    //End lấy khách hàng

                    $data['staff'] = get_table_where(db_prefix() . 'staff', ['active' => 1]);
                    $data['title']          = _l('cong_add_orders');
                    $data['shipping'] = [];
                }
            }
            $this->load->view('admin/orders/detail', $data);
        }
    }

    public function getItemsAjax()
    {
        $search = $this->input->post('q');
        $limit = 100;
        if (!empty($search)) {
            $this->db->select('id, name, code, price, avatar, "items" as type_items');
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('code', $search);
            $this->db->group_end();
            $this->db->limit($limit);
            $items = $this->db->get('tblitems')->result_array();

            $group_items = [['id' => 'group', 'name' => lang('ch_items'), 'code' => lang('ch_items'), 'price' => 0, 'avatar' => false, 'type_items' => "items"]];
            $list = array_merge($group_items, $items);

            $this->db->select('id, name, code, price_sell as price, CONCAT("uploads/products/", "", images, "") as avatar, "products" as type_items');
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('code', $search);
            $this->db->group_end();
            $this->db->limit($limit);
            $products = $this->db->get('tbl_products')->result_array();
            $group_products = [['id' => 'group', 'name' => lang('tnh_products'), 'code' => lang('tnh_products'), 'price' => 0, 'avatar' => false, 'type_items' => "items"]];
            $list = array_merge($list, $group_products);
            $list = array_merge($list, $products);

            echo json_encode($list);
            die();

            // print_arrays(json_encode($items));
            // echo json_encode($items);die();
        }
        echo json_encode([]);
        die();
    }

    public function getCustomerAjax()
    {
        $search = $this->input->post('q');
        if (!empty($search)) {
            $this->db->select('userid, company,concat(prefix_client,code_client," - ",code_type) as full_code');
            $this->db->like('company', $search);
            $customer = $this->db->get('tblclients')->result_array();
            echo json_encode($customer);
            die();
        }
        echo json_encode([]);
        die();
    }

    //Cập nhật trạng thái
    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        if (!empty($id)) {
            $this->db->where('id', $id);
            $orders = $this->db->get(db_prefix() . 'orders')->row();
            if (!empty($orders)) {
                if ($status != -1 && $status != -2 && $status != -3) {
                    $produce = GetProducedure($orders->status);
                    if ($produce->id == $status) {
                        $this->db->where('id', $id);
                        $success = $this->db->update('tblorders', ['status' => $status]);
                        if ($success) {
                            $this->db->insert('tblorders_step', [
                                'id_orders' => $id,
                                'id_procedure' => $status,
                                'date_create' => date('Y-m-d H:i:s'),
                                'id_staff' => get_staff_user_id(),
                                'name_procedure' => $produce->name,
                                'active' => 1,
                                'order_by' => $produce->orders
                            ]);
                            $id_step = $this->db->insert_id();
                            if (!empty($id_step)) {
                                $this->db->where('id_orders', $id);
                                $this->db->where('id !=', $id_step);
                                $this->db->where('id_procedure != ', $status);
                                $this->db->update('tblorders_step', ['active' => 0]);
                            }

                            $produce_next = GetProducedure($status);
                            if (empty($produce_next)) {
                                $this->db->where('id', $id);
                                $success = $this->db->update('tblorders', ['status' => '-1']);
                            }


                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'message' => _l('cong_change_status_true')
                            ]);
                            die();
                        }
                        echo json_encode([
                            'success' => false,
                            'alert_type' => 'danger',
                            'message' => _l('cong_change_status_false')
                        ]);
                        die();
                    }
                } else if ($status != -1) {
                    $this->db->where('id', $id);
                    $success = $this->db->update('tblorders', ['status' => $status]);
                    if ($success) {
                        $this->db->select('max(order_by) as max_order');
                        $this->db->where('id_orders', $id);
                        $max_step = $this->db->get('tblorders_step')->row();
                        $this->db->insert('tblorders_step', [
                            'id_orders' => $id,
                            'id_procedure' => $status,
                            'date_create' => date('Y-m-d H:i:s'),
                            'id_staff' => get_staff_user_id(),
                            'name_procedure' => $status == -2 ? _l('cong_orders_delay') : _l('cong_orders_cancel'),
                            'active' => 1,
                            'order_by' => ($max_step->max_order + 1)
                        ]);
                        $id_step = $this->db->insert_id();
                        if (!empty($id_step)) {
                            $this->db->where('id_orders', $id);
                            $this->db->where('id != ', $id_step);
                            $this->db->where('id_procedure != ', $status);
                            $this->db->update('tblorders_step', ['active' => 0]);
                        }
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'success',
                            'message' => _l('cong_change_status_true')
                        ]);
                        die();
                    }
                }
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_data_change_pls')
        ]);
        die();
    }

    public function restore_step()
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            if (!empty($id)) {
                $this->db->where('id', $id);
                $orders = $this->db->get('tblorders')->row();
                if ($orders->status > 0 || $orders->status == -1) {
                    if ($orders->status == -1) {
                        $this->db->where('id_orders', $orders->id);
                        $this->db->where('id_procedure > 0');
                        $this->db->order_by('order_by', 'desc');
                        $orders_step = $this->db->get('tblorders_step')->row();
                        if (!empty($orders_step)) {
                            $orders->status = $orders_step->id_procedure;
                        } else {
                            $orders->status = 0;
                        }
                    }
                    $producedure = ResProducedure($orders->status);
                    if (!empty($producedure)) {
                        $this->db->where('id', $id);
                        $success = $this->db->update('tblorders', ['status' => $producedure->id]);
                        if ($success) {
                            $this->db->where('id_orders', $id);
                            $this->db->where('id_procedure', $orders->status);
                            $this->db->delete('tblorders_step');
                            echo json_encode([
                                'success' => true,
                                'alert_type' => 'success',
                                'message' => _l('cong_restore_true')
                            ]);
                            die();
                        }
                    }
                }
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => _l('cong_restore_false')
                ]);
                die();
            }
        }
    }

    public function restore_orders()
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            if (!empty($id)) {
                $this->db->where('id', $id);
                $orders = $this->db->get('tblorders')->row();
                if (($orders->status == -2 || $orders->status == -3) && $orders->status == $status) {
                    $this->db->where('id_orders', $id);
                    $this->db->where('id_procedure !=' . $status);
                    $this->db->order_by('order_by', 'DESC');
                    $orders_step = $this->db->get('tblorders_step')->row();

                    $this->db->where('id', $id);
                    $success = $this->db->update('tblorders', ['status' => !empty($orders_step->id_procedure) ? $orders_step->id_procedure : '0']);
                    if ($success) {
                        $this->db->where('id_orders', $id);
                        $this->db->where('id_procedure', $status);
                        $this->db->delete('tblorders_step');
                        if (!empty($orders_step->id_procedure)) {
                            $this->db->where('id_orders', $id);
                            $this->db->where('id_procedure', $orders_step->id_procedure);
                            $this->db->update('tblorders_step', [
                                'active' => 1
                            ]);
                        }
                        echo json_encode([
                            'success' => true,
                            'alert_type' => 'success',
                            'message' => _l('cong_change_status_true')
                        ]);
                        die();
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => _l('cong_data_change_pls')
                    ]);
                    die();
                }
            }
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => _l('cong_change_status_false')
            ]);
            die();
        }
    }

    public function delete_orders()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {
            $this->db->where('id', $id);
            $orders = $this->db->get('tblorders')->row();
            $this->db->where('id', $id);
            $success = $this->db->delete('tblorders');
            if (!empty($success)) {

                $this->db->where('id_orders', $id);
                $this->db->delete('tblorders_detail_shipping');

                $this->db->where('id_orders', $id);
                $this->db->delete('tblorders_items');

                $this->db->where('id_orders', $id);
                $this->db->delete('tblorders_step');
                if (!empty($orders->id_quotes_orders)) {
                    $this->db->where('id', $orders->id_quotes_orders);
                    $this->db->update('tblquotes_orders', ['status' => 0]);
                }

                //tnh
                if (checkModule('quotes')) {
                    $this->db->where('order_id', $id);
                    $this->db->update('tbl_quotes', ['order_id' => 0]);
                }
                //
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => _l('cong_delete_true')
                ]);
                die();
            }
        }
        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => _l('cong_delete_false')
        ]);
        die();
    }

    public function loadViewOrder($id = "")
    {
        if (!empty($id)) {
            $data['title'] = _l('cong_detail_orders');
            $data['orders'] = $this->orders_model->get_view($id);
            $data['orders']->name_status = $this->orders_model->get_status_orders($id, $data['orders']->status);
            echo json_encode(['success' => true, 'data' => $this->load->view('admin/orders/view_modal', $data, true)]);
            die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => _l('cong_found_data')]);
        die();
    }

    public function getShipping()
    {
        $client = $this->input->post('client');
        if (!empty($client)) {
            $this->db->where('client', $client);
            $shipping_client = $this->db->get('tblshipping_client')->result_array();
            if (!empty($shipping_client)) {
                echo json_encode($shipping_client);
                die();
            }
        }
        echo json_encode([]);
        die();
    }

    public function ViewModalShipping()
    {
        $client = $this->input->post('client');
        $idSelect = $this->input->post('idSelect');
        if (!empty($client)) {
            echo json_encode([
                'data' => $this->load->view('admin/orders/Addshipping', ['client' => $client, 'idSelect' => $idSelect], true),
                'success' => true
            ]);
            die();
        }
        echo json_encode([
            'success' => false,
            'message' => _l('cong_not_found_client_shipping')
        ]);
        die();
    }

    public function getTablePriceByCustomer($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;

        if (!empty($customer_id)) {
            $customer_id = str_replace('customers__', '', $customer_id);
            $year = date('Y');
            $this->db->select('GROUP_CONCAT(distinct tblcustomer_groups.groupid) as groupid', false);
            $this->db->from('tblcustomer_groups');
            $this->db->where('tblcustomer_groups.customer_id', $customer_id);
            $customerGroups = $this->db->get()->row_array();
            $group_in = $customerGroups['groupid'];
            if (empty($group_in)) {
                $results = false;
            } else {
                $this->db->select('
                    tblgroup_price.id as id,
                    tblgroup_price.name_price as text
                ', false);
                $this->db->from('tblgroup_price');
                $this->db->group_start();
                $this->db->like('tblgroup_price.name_price', $term);
                $this->db->group_end();
                // $this->db->where('tblgroup_price.year', $year);

                $this->db->group_start();
                // $this->db->where('exists (
                //     SELECT
                //         tblgroup_price_discount.id
                //     FROM tblgroup_price_discount
                //     WHERE tblgroup_price_discount.group_price_id = tblgroup_price.id AND tblgroup_price_discount.group_id IN ('.$group_in.')
                // )', false, false);

                // $this->db->where('exists (
                //     SELECT
                //         tblgroup_price_discount.id
                //     FROM tblgroup_price_discount
                //     WHERE tblgroup_price_discount.group_price_id = tblgroup_price.id AND tblgroup_price_discount.client = ' . $customer_id . '
                // )', false, false);

                // $this->db->or_where_in('tblgroup_price.group_id', explode(',', $group_in));

                $this->db->where('tblgroup_price.client', $customer_id);
                $this->db->group_end();
                $results = $this->db->get()->result_array();
            }
        }
        $data['results'] = $results;
        if ($id) {
            $this->db->select('tblgroup_price.id, tblgroup_price.name_price', false);
            $this->db->from('tblgroup_price');
            $this->db->where('tblgroup_price.id', $id);
            $group_price = $this->db->get()->row_array();
            if (!empty($group_price)) {
                $data['row'] = ['id' => $group_price['id'], 'text' => $group_price['name_price']];
            } else {
                $data['row'] = ['id' => 0, 'text' => lang('choose')];
            }
        }


        // if (!empty($customer_id)) {
        //     $arr = explode('__', $customer_id);
        //     $type_customer = $arr[0];
        //     $customer_id = $arr[1];
        //     if ($type_customer == "customers") {
        //         $results = $this->orders_model->searchTablePrices($term, $limit, $customer_id);
        //     } else if ($type_customer == "leads") {
        //         $results = false;
        //     }
        // }
        // $data['results'] = $results;
        // if ($id) {
        //     $price = $this->site_model->rowSetPricesByNewId($id);
        //     if (!empty($price)) {
        //         $data['row'] = ['id' => $price['id'], 'text' => $price['name']];
        //     } else {
        //         $data['row'] = ['id' => 0, 'text' => lang('choose')];
        //     }
        // }
        echo json_encode($data);
    }

    public function getTableDiscountByCustomer($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;
        if (!empty($customer_id)) {
            $arr = explode('__', $customer_id);
            $type_customer = $arr[0];
            $customer_id = $arr[1];
            if ($type_customer == "customers") {
                $results = $this->site_model->searchTableDiscount($term, $limit, $customer_id);
            } else if ($type_customer == "leads") {
                $results = false;
            }
        }
        $data['results'] = $results;
        if ($id) {
            $ds = $this->site_model->rowDiscountByIdNew($id);
            if (!empty($ds)) {
                $data['row'] = ['id' => $ds['id'], 'text' => $ds['name_discount']];
            } else {
                $data['row'] = ['id' => 0, 'text' => lang('choose')];
            }
        }
        echo json_encode($data);
    }

    public function getPricesForItems()
    {
        $data = [];
        if ($this->input->get()) {
            $item_id = $this->input->get('item_id');
            $table_price_id = $this->input->get('table_price_id');
            $customers_price_list = $this->input->get('customers_price_list');
            $year = $this->input->get('year');
            $counter_index = $this->input->get('counter_index');

            if (!empty($item_id)) {
                $arr = explode("__", $item_id);
                $type = $arr[1];
                $item_type = $type;
                if ($type == "products") {
                    $type = "product";
                } else if ($type == "items") {
                    $type = "items";
                } else if ($type == "materials") {
                    $type = "nvl";
                }

                $id = $arr[0];
                $priceItem = $this->site_model->rowSetPricesItems($table_price_id, $type, $id);
                if (!empty($priceItem)) {
                    $data['priceItem'] = $priceItem['prices_new'];
                } else {
                    if ($type == "product") {
                        $item = $this->site_model->rowProduct($id);
                        $dataPrice = $this->price_list_model->showPrice($item_id, $customers_price_list, $year);
                        if ($dataPrice['result'] == 1) {
                            $data['priceItem'] = $dataPrice['price'];
                        } else {
                            $data['priceItem'] = $item['price_sell'];
                        }
                    } else if ($type == "nvl") {
                        $item = $this->items_model->rowMaterial($id);
                        $data['priceItem'] = $item['price_sell'];
                    } else {
                        $item = $this->site_model->rowItem($id);
                        $data['priceItem'] = $item['price'];
                    }
                }
                $quantityWarehouse = $this->site_model->getTotalQuantityWarehouseItems($id, $type);
                $data['quantity_warehouse'] = $quantityWarehouse['total_quantity'];

                $htmlExchange = '';
                if ($arr[1] == "products") {
                    $exchange = $this->site_model->getExchangeProducts($id);
                    if (!empty($exchange)) {
                        foreach ($exchange as $k => $val) {
                            $htmlExchange .= '<div class="list-exchange">
                                <input type="hidden" class="form-control number-exchange" value="' . $val['number_exchange'] . '">
                                <span>' . $val['unit_name'] . ': </span>
                                <span class="text-number-exchange">0</span>
                            </div>';
                        }
                    }
                }
                $data['htmlExchange'] = $htmlExchange;

                //items
                $html_sub = '';
                if ($item_type == "products") {
                    $productsColumns = $this->products_model->getProductsColumns($id);
                    $thSub = '';
                    $trAddChild = '';
                    if (!empty($productsColumns)) {
                        foreach ($productsColumns as $k => $v) {
                            $thSub .= '<th class="text-center" style="width:130px;">' . $v['name'] . '</th>';
                            $trAddChild .= '
                                <td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id][]" class="form-control" value="' . $v['id'] . '">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value][]" class="form-control" value="' . $v['name'] . '">
                                    <input type="text" placeholder="' . $v['name'] . '" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_name][]" class="form-control" value="">
                                </td>
                            ';
                        }
                    }

                    $html_sub .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
                        <thead>
                            <tr class="not-tr">
                                <th class="text-center" style="width: 50px;">
                                    <a href="javascript:void(0)" onclick="addChild' . $counter_index . '(this, ' . $counter_index . ')"><i class="fa fa-plus"></i></a>
                                </th>
                                <th class="text-center" style="width: 150px;">' . lang('Ngày giao') . '<small class="req text-danger">*</small></th>
                                <th class="text-center" style="width: 150px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                                <th class="text-center" style="width: 150px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                                <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                                <th class="text-center" style="width: 100px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                                <th class="text-center" style="width: 100px;">' . lang('tnh_sample_quantity') . '</th>
                                ' . $thSub . '
                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                            <tbody class="child">
                            </tbody>
                        </table>
                        <script>

                            function addChild' . $counter_index . '(_this, temp_counter) {
                                trChild = $(_this).parents("tr");
                                tdNumberChild = `<td></td>`;
                                tdDateShip = `<td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id_date_ship]" class="form-control" value="0">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value_date_ship]" class="form-control" value="date_ship">
                                    <input type="text" name="itemsChildColumns[' . $counter_index . '][${counter_child}][date_ship]" placeholder="Ngày giao" class="datepicker form-control date_ship" value="">
                                </td>`;
                                
                                tdOrderCode = `<td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id_order_code]" class="form-control" value="0">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value_order_code]" class="form-control" value="order_code">
                                    <input type="text" name="itemsChildColumns[' . $counter_index . '][${counter_child}][order_code]" placeholder="Mã đơn đặt" class="form-control order_code" value="">
                                </td>`;

                                tdCommand = `<td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id_command]" class="form-control" value="0">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value_command]" class="form-control" value="command">
                                    <input type="text" name="itemsChildColumns[' . $counter_index . '][${counter_child}][command]" placeholder="Chỉ lệnh" class="form-control command" value="">
                                </td>`;

                                tdQuantityPut = `<td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id_quantity_put]" class="form-control" value="0">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value_quantity_put]" class="form-control" value="quantity_put">
                                    <input type="text" name="itemsChildColumns[' . $counter_index . '][${counter_child}][quantity_put]" class="form-control quantity_put number-format" style="width: 100%;" value="0">
                                </td>`;

                                tdQuantityLoss = `<td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id_quantity_loss]" class="form-control" value="0">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value_quantity_loss]" class="form-control" value="quantity_loss">
                                    <input type="text" name="itemsChildColumns[' . $counter_index . '][${counter_child}][quantity_loss]" class="form-control quantity_loss number-format" readonly style="width: 100%;" value="0">
                                </td>`;

                                tdSampleQuantity = `<td>
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_id_sample_quantity_item]" class="form-control" value="0">
                                    <input type="hidden" name="itemsChildColumns[' . $counter_index . '][${counter_child}][columns_value_sample_quantity_item]" class="form-control" value="sample_quantity_item">
                                    <input type="text" name="itemsChildColumns[' . $counter_index . '][${counter_child}][sample_quantity_item]" class="form-control sample_quantity_item number-format" style="width: 100%;" value="0">
                                </td>`;

                                tdActionsChild = `<td class="text-center">
                                    <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
                                </td>`;

                                trHtmlChild = `<tr tr-counter="' . $counter_index . '" class="not-tr tr-sub-items">
                                    ${tdNumberChild}
                                    ${tdDateShip}
                                    ${tdOrderCode}
                                    ${tdCommand}
                                    ${tdQuantityPut}
                                    ${tdQuantityLoss}
                                    ${tdSampleQuantity}
                                    ' . $trAddChild . '
                                    ${tdActionsChild}
                                </tr>`;
                                trChild.find(".table-child tbody").append(trHtmlChild);
                                counter_child++;
                                init_datepicker();
                            }

                            $(document).ready(function () {

                            });
                        </script>
                        ';
                }
                $data['html_sub'] = $html_sub;

                $arrUnit = [];
                $isSelected = 0;
                if ($item_type == "products") {
                    $this->db->select('
                        tbl_products.unit_id as unit_id,
                        tblunits.unit as unit_name,
                        tbl_products.conversion_unit as conversion_unit,
                        unit_exchange.unit as unit_name_conversion,
                        tbl_products.conversion_quantity_unit as conversion_quantity_unit
                    ', false);
                    $this->db->from('tbl_products');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
                    $this->db->join('tblunits unit_exchange', 'unit_exchange.unitid = tbl_products.conversion_unit');
                    $this->db->where('tbl_products.id', $item_id);
                    $dtProduct = $this->db->get()->row_array();
                    if (!empty($dtProduct)) {
                        $arrUnit[] = ['id' => $dtProduct['unit_id'], 'text' => $dtProduct['unit_name'], 'selected' => 'selected', 'conversion_quantity_unit' => 1];
                        $isSelected = $dtProduct['unit_id'];
                        if ($dtProduct['unit_id'] != $dtProduct['conversion_unit']) {
                            $arrUnit[] = ['id' => $dtProduct['conversion_unit'], 'text' => $dtProduct['unit_name_conversion'], 'selected' => '', 'conversion_quantity_unit' => $dtProduct['conversion_quantity_unit']];
                        }
                    }
                }

                $data['arrUnit'] = $arrUnit;
                $data['isSelected'] = $isSelected;
            }
        }
        echo json_encode($data);
    }

    public function getPricesForItemsNew()
    {
        $data = [];
        $priceItem = 0;
        if ($this->input->get()) {
            $item_id = $this->input->get('item_id');
            $table_price_id = $this->input->get('table_price_id');
            $customer_id = $this->input->get('customers');
            $moq = number_unformat($this->input->get('moq'));

            if (!empty($item_id) && $table_price_id) {
                $arr = explode("__", $item_id);
                $type = $arr[1];
                $item_type = $type;
                if ($type == "products") {
                    $type = "product";
                } else if ($type == "items") {
                    $type = "items";
                } else if ($type == "materials") {
                    $type = "nvl";
                }

                $product_id = $arr[0];
                $customer_id = str_replace('customers__', '', $customer_id);
                $this->db->select('GROUP_CONCAT(distinct tblcustomer_groups.groupid) as groupid', false);
                $this->db->from('tblcustomer_groups');
                $this->db->where('tblcustomer_groups.customer_id', $customer_id);
                $customerGroups = $this->db->get()->row_array();
                $group_in = $customerGroups['groupid'];
                // if (empty($group_in)) {
                //     $results = false;
                // } else {
                // $query = "(
                //     SELECT
                //         tblgroup_price_discount.group_price_id as group_price_id,
                //         tblgroup_price_discount.discount as discount
                //     FROM tblgroup_price_discount
                //     WHERE tblgroup_price_discount.group_id IN ($group_in) AND tblgroup_price_discount.group_price_id = $table_price_id
                //     LIMIT 1
                // ) tb_group_price_discount";

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

                // $this->db->where('tblgroup_price_detail.money_start <=', $moq);
                // $this->db->where('tblgroup_price_detail.money_end >=', $moq);
                $this->db->order_by('tblgroup_price_detail.money_start DESC');
                $rs = $this->db->get()->row_array();
                if (!empty($rs)) {
                    $priceItem = $rs['price'];
                }
            }
            // }
        }

        $data['priceItem'] = $priceItem;
        echo json_encode($data);
    }
	

    public function getDiscountForItems()
    {
        $data = [];
        if ($this->input->get()) {
            $item_id = $this->input->get('item_id');
            $table_discount_id = $this->input->get('table_discount_id');
            if (!empty($item_id)) {
                $arr = explode("__", $item_id);
                $type = $arr[1];
                $id = $arr[0];

                if ($type == "products") {
                    $item = $this->site_model->rowProduct($id);
                    $ds = $this->site_model->rowDiscountDetailProduct($table_discount_id, $item['category_id']);
                } else {
                    $item = $this->site_model->rowItem($id);
                    $ds = $this->site_model->rowDiscountDetail($table_discount_id, $item['category_id']);
                }
                if (!empty($ds)) {
                    $data['discount'] = $ds['discounts'];
                } else {
                    // $data['discount'] = 'none';
                    $data['discount'] = 0;
                }
            }
        }
        echo json_encode($data);
    }

    public function agreeStatusCustom()
    {
        $data = [];
        if (!$this->perApproveOrders) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($this->input->get()) {
            // print_arrays($this->input->get());
            $order_id = $this->input->get('order_id');
            $status = $this->input->get('status');
            $workflow = $this->site_model->rowOrdersWorkflow($status, $order_id);
            if (!empty($workflow)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_this_process_has_been_selected');
                echo json_encode($data);
                die;
            }
            // $status = $this->input->get('arrStatus');
            // if (!empty($status)) $status = implode(',', $status);
            // $up = $this->orders_model->updateOrdersNew($order_id, [
            //     'status_custom' => $status,
            // ]);
            $wf = $this->site_model->insertOrdersWorkflow([
                'workflow_id' => $status,
                'order_id' => $order_id,
                'created_by' => get_staff_user_id(),
                'date_created' => date('Y-m-d H:i:s'),
            ]);
            if ($wf) {
                insertActivityLog([
                    'type_parent_obj' => 'orders',
                    'table_obj' => 'tbl_orders',
                    'id_obj' => $order_id,
                    'name_obj' => $order['reference_no'],
                    'content' => lang('tnh_his_status_orders') . ' [' . $order['reference_no'] . ']',
                    'actions' => 'status'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function getPromotionOrders()
    {
        die;
        $data = [];
        // if ($this->input->post()) {
        $customer = $this->input->post('customer_id');
        if (!empty($customer)) {
            $customer = explode("__", $customer);
            $customer_id = $customer[1];
        } else {
            $customer_id = 0;
        }
        $total_money = number_unformat($this->input->post('total_amount'));
        $customer_id = 341;
        $total_money = 10000000000000;
        $promotions = $this->site_model->getPromotionForOrders($customer_id, $total_money);
        // print_arrays($total_money);
        $html = "";
        if (!empty($promotions)) {
            foreach ($promotions as $key => $value) {
                $type = $value['type'];
                $strType = '';
                if ($type == "discount") {
                    $strType = lang('promotion_by_discount');
                } else if ($type = "item") {
                    $strType = lang('promotion_by_item');
                } else if ($type == "promotion_by_sales") {
                    $strType = lang('promotion_by_sales');
                }


                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdName = '<td>' . $value['name'] . '</td>';
                $tdType = '<td>' . $strType . '</td>';
                $tdTime = '<td>' . _d($value['date_active_start']) . ' - ' . _d($value['date_active_end']) . '</td>';
                $tdActions = '<td class="text-center">
                        <div class="checkbox checkbox-primary" style="margin-bottom: 0;">
                            <input type="checkbox" name="chosen_promotions[]" id="chosen_promotions_' . $key . '" class="chosen_promotions" value="' . $value['id'] . '">
                            <label for="chosen_promotions_' . $key . '">' . lang('choose') . '</label>
                        </div>
                    </td>';

                $tr = '<tr>
                        ' . $tdNumber . '
                        ' . $tdName . '
                        ' . $tdType . '
                        ' . $tdTime . '
                        ' . $tdActions . '
                    </tr>';

                $html .= $tr;
            }
        }
        $data['html'] = $html;
        // print_arrays($promotions);
        // }
        echo json_encode($data);
    }

    public function gift($id)
    {
        $order = $this->orders_model->rowOrderById($id);
        $items = $this->orders_model->getOrderItemsForGift($id);

        if ($this->input->post('save')) {
            $promotions = $this->input->post('chosen_promotions');
            $options = [];
            if (!empty($promotions)) {
                $this->site_model->deleteOrderGifts($id);
                $this->site_model->deleteOrderItemsByGift($id);
                foreach ($promotions as $key => $value) {
                    $promotion_id = $value;
                    $order_id = $id;
                    $gifts = !empty($this->input->post('gift')[$value]) ? $this->input->post('gift')[$value] : false;
                    $items = [];
                    if (!empty($gifts)) {
                        foreach ($gifts as $k => $val) {
                            $promotion_item_gift_id = $val;
                            if ($this->site_model->checkGiftExistOrderItems($id, $promotion_item_gift_id)) continue;
                            $productionItemGift = $this->site_model->rowPromotionItemGift($promotion_item_gift_id);
                            $type_item = $productionItemGift['type_item'] == 'product' ? 'products' : 'items';
                            $item_id = $productionItemGift['id_item'];
                            if ($type_item == "products") {
                                $info = $this->products_model->rowProduct($item_id);
                            } else if ($type_item == "items") {
                                $info = $this->items_model->rowItems($item_id);
                            }

                            if (empty($info)) {
                                continue;
                            }
                            $item_code = $info['code'];
                            $item_name = $info['name'];
                            $quantity = $productionItemGift['quantity'];
                            $promotion_item_id =  $this->input->post('promotion_item_id')[$promotion_id][$k];
                            $quantity = number_unformat($this->input->post('quantity_gift')[$promotion_id][$k]);
                            $quantity_bs = number_unformat($this->input->post('quantity_bs')[$promotion_id][$k]);
                            $quantity_condition = number_unformat($this->input->post('quantity_condition')[$promotion_id][$k]);

                            $items[] = [
                                'order_id' => $order_id,
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'item_code' => $item_code,
                                'item_name' => $item_name,
                                'quantity' => $quantity,
                                'price' => 0,
                                'amount' => 0,
                                'tax_id_item' => 0,
                                'tax_name_item' => '0%',
                                'tax_rate_item' => 0,
                                'tax_amount_item' => 0,
                                'discount_percent_item' => 0,
                                'discount_percent_amount_item' => 0,
                                'discount_direct_amount_item' => 0,
                                'total_amount' => 0,
                                'note_item' => '',
                                'type_gift' => 1,
                                'promotion_item_gift_id' => $promotion_item_gift_id,
                                'promotion_item_id' => $promotion_item_id,
                                'quantity_bs' => $quantity_bs,
                                'quantity_condition' => $quantity_condition,
                            ];
                        }
                    }

                    $options[] = [
                        'order_id' => $order_id,
                        'gift_id' => $promotion_id,
                        'items' => $items
                    ];
                }
                $flag = false;
                if (!empty($options)) {
                    foreach ($options as $key => $value) {
                        $items = $value['items'];
                        unset($value['items']);
                        $ins = $this->site_model->insertOrdersGifts($value);
                        if ($ins) {
                            foreach ($items as $k => $val) {
                                $this->orders_model->insertOrderItemsNew($val);
                            }
                            $flag = true;
                        }
                    }
                }

                if ($flag) {
                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_his_gift_orders') . ' [' . $order['reference_no'] . ']',
                        'actions' => 'edit'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_chonse_gift');
            }
            // print_arrays($this->input->post());
            echo json_encode($data);
            die;
        } else {
            $customer_id = $order['customer_id'];
            $date = date_format(date_create($order['date']), "Y-m-d");
            $gift = $this->site_model->getGiftForOrders($order['customer_id'], $date, $items);
            $orderGift = $this->site_model->getOrdersGifts($id);

            $data['orderGift'] = $orderGift;
            $data['gift'] = $gift;
            $data['order'] = $order;
            $data['tnh'] = $this->tnh;
            $data['title'] = lang('tnh_gift');
            $data['id'] = $id;
            $data['breadcrumb'] = [array('link' => base_url('admin/orders'), 'page' => lang('tnh_orders')), array('link' => '#', 'page' => lang('tnh_gift'))];
            $this->load->view('admin/orders/gift', $data);
        }
    }

    public function getGiftItems()
    {
        $data = [];
        if ($this->input->post()) {
            $promotion_id = $this->input->post('promotion_id');
            $order_id = $this->input->post('order_id');
            $order = $this->orders_model->rowOrderById($order_id);
            $date = date_format(date_create($order['date']), "Y-m-d");
            $items = $this->orders_model->getOrderItemsForGift($order_id);

            $htmlBody = '';
            // $giftItems = $this->site_model->getGiftItemForOrders($order['customer_id'], $date, $items, $promotion_id);
            $giftItems = $this->site_model->getGiftItemForOrdersNew($order['customer_id'], $date, $items, $promotion_id);
            foreach ($giftItems as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['id_item'];
                $strType = '';
                if ($type_item == "product") {
                    $info = $this->products_model->rowProduct($items_id);
                    $strType = lang('tnh_products');
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $strType = lang('ch_items');
                }

                $checked = '';
                if ($this->site_model->checkGiftExistOrderItems($order_id, $value['id'])) {
                    $checked = "checked";
                }

                $quantityBS = $value['qty_bs'];
                $quantityCondition = $value['quantity_condition'];
                $quantityGift = $quantityBS * $value['quantity'];

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdName = '<td>' . $value['name_gift'] . '</td>';
                $tdNameItem = '<td>' . $info['name'] . '</td>';
                $tdType = '<td>' . $strType . '</td>';
                $tdQuantity = '<td class="text-center">
                            <input type="hidden" name="quantity_condition[' . $promotion_id . '][]" id="input" class="form-control" value="' . $quantityCondition . '">
                            <input type="hidden" name="quantity_bs[' . $promotion_id . '][]" id="input" class="form-control" value="' . $quantityBS . '">
                            <input type="hidden" name="quantity_gift[' . $promotion_id . '][]" id="input" class="form-control" value="' . $quantityGift . '">
                            <input type="hidden" name="promotion_item_id[' . $promotion_id . '][]" id="input" class="form-control" value="' . $value['promotion_item_id'] . '">
                            ' . formatNumber($quantityGift) . '
                </td>';
                $tdActions = '<td class="text-center">
                    <div class="checkbox checkbox-primary" style="margin-bottom: 0;">
                        <input type="checkbox" ' . $checked . ' name="gift[' . $promotion_id . '][]" id="gift[' . $value['id'] . ']" class="gift[' . $value['id'] . ']" value="' . $value['id'] . '">
                        <label for="gift[' . $value['id'] . ']">' . lang('choose') . '</label>
                    </div>
                </td>';

                $tr = '<tr data-promotion-id="' . $promotion_id . '">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdNameItem . '
                    ' . $tdType . '
                    ' . $tdQuantity . '
                    ' . $tdActions . '
                </tr>';

                $htmlBody .= $tr;
            }
            $data['htmlBody'] = $htmlBody;
        }
        echo json_encode($data);
    }

    public function gannt_orders()
    {
        //permission gannt orders
        $this->perViewGanntOrders = has_permission('orders_gannt_orders', '', 'view');
        $this->perViewOwnGanntOrders = has_permission('orders_gannt_orders', '', 'view_own');
        if (!$this->perViewGanntOrders && !$this->perViewOwnGanntOrders) {
            accessDenied();
        }

        if ($this->input->post('search') == 'unsearch') {
            $_POST = array();
        }

        $sum = $this->site_model->countOrders();
        $numberPage = get_option('number_page_gantt');
        $numPages = ceil($sum / $numberPage);
        $pageCurrent = !empty($this->input->get('page')) ? $this->input->get('page') : 1;
        $start = ($pageCurrent - 1) * $numberPage;
        $data['numberPage'] = $numberPage;
        $data['numPages'] = $numPages;
        $data['pageCurrent'] = $pageCurrent;

        $data['gantt_data'] = $this->site_model->loadGanttOrders($start, $numberPage);
        // print_arrays($data['gantt_data']);
        $data['title'] = lang('tnh_diagram_gantt_orders');
        $data['tnh'] = $this->tnh;
        $this->load->view('admin/orders/gannt_orders', $data);
    }

    public function searchOrders($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $data['results'] = $this->orders_model->searchOrders($term, $limit);
        if ($id) {
            $order = $this->orders_model->rowOrderById($id);
            $data['row'] = ['id' => $order['id'], 'text' => $order['reference_no']];
        }
        echo json_encode($data);
    }

    public function searchOrdersByParams($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');

        $data['results'] = $this->orders_model->searchOrdersByParams($term, $limit, $params);
        if ($id) {
            $order = $this->orders_model->rowOrderById($id);
            $data['row'] = ['id' => $order['id'], 'text' => $order['reference_no']];
        }
        echo json_encode($data);
    }

    public function tax_bill($id)
    {
        $order = $this->orders_model->rowOrderById($id);
        if (empty($order)) {
            refererModel(lang('no_data_exists'));
            die;
        }
        if ($order['type_bills'] == 1) {
            refererModel(lang('tnh_this_order_has_generated_tax_invoices'));
            die;
        }
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('reference_bill', lang("tnh_reference_bill"), 'trim|required|is_unique[tbl_invoices.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('tax_id', lang("tnh_taxs"), 'required');
            if ($this->form_validation->run() == true) {
                $date = to_sql_date($this->input->post('date'), true);
                $reference_no = $this->input->post('reference_bill');
                $tax_id = $this->input->post('tax_id');
                $tax_name = 0;
                $tax_rate = 0;
                $note = $this->input->post('note');
                $type = "orders";
                $object_id = $id;
                $status = 'un_approved';

                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }

                $tax_id_old = $order['tax_id'];
                $tax_name_old = $order['tax_name'];
                $tax_rate_old = $order['tax_rate'];
                $cost_delivery_item = $order['charge_party'] == "customer" ? $order['cost_delivery'] : 0;
                $total_item = $order['grand_total'] - $order['total_tax'] - $cost_delivery_item;
                $total_tax_item = 0;
                if ($tax_rate > 0) {
                    $total_tax_item = $total_item * ($tax_rate / 100);
                }
                $grand_total_item = $total_item + $total_tax_item + $cost_delivery_item;

                $invoiceItems = [
                    'object_id' => $object_id,
                    'tax_id_old' => $tax_id_old,
                    'tax_name_old' => $tax_name_old,
                    'tax_rate_old' => $tax_rate_old,
                    'total_item' => $total_item,
                    'total_tax_item' => $total_tax_item,
                    'cost_delivery_item' => $cost_delivery_item,
                    'grand_total_item' => $grand_total_item,
                ];

                $total = $total_item;
                $total_tax = $total_tax_item;
                $grand_total = $grand_total_item;
                $cost_delivery = $cost_delivery_item;

                $total_cost_temporary_capital = 0;
                $total_profit_temporary_capital = 0;
                $total_cost = 0;
                $total_profit = 0;

                $invoice = [
                    'reference_no' => $reference_no,
                    'date' => $date,
                    'type' => $type,
                    'customer_id' => $order['customer_id'],
                    'customer_name' => $order['customer_name'],
                    'object_id' => $object_id,
                    'tax_id' => $tax_id,
                    'tax_rate' => $tax_rate,
                    'tax_name' => $tax_name,
                    'total' => $total,
                    'total_tax' => $total_tax,
                    'cost_delivery' => $cost_delivery,
                    'grand_total' => $grand_total,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                    'note' => $note,
                    'status' => $status,
                ];

                // print_arrays($order['cost_delivery']);

                $invoice_id = $this->site_model->insertInvoices($invoice);
                if ($invoice_id) {
                    $get_code = get_table_where('tbl_invoices', array('id' => $invoice_id), '', 'row');
                    activity_log_v2('work_debt_sales', 'tbl_invoices', $invoice_id, $get_code->reference_no, 'Thêm mới hóa đơn bán hàng [' . $get_code->reference_no . ']');

                    $invoiceItems['invoice_id'] = $invoice_id;
                    if ($this->site_model->insertInvoiceItems($invoiceItems)) {
                        $this->orders_model->updateOrdersNew($id, [
                            'type_bills' => 1,
                            'tax_id' => $tax_id,
                            'tax_name' => $tax_name,
                            'total_tax' => $total_tax,
                            'tax_rate' => $tax_rate,
                            'grand_total' => $grand_total,
                        ]);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_his_tax_bill_orders') . ' [' . $order['reference_no'] . ']',
                        'actions' => 'tax_bill'
                    ]);

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
        } else {
            $data['order'] = $order;
            $data['id'] = $id;
            $data['taxs'] = $this->site_model->getTaxs();
            $this->load->view('admin/orders/tax_bill', $data);
        }
    }

    public function searchSuppliers($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $params = $this->input->get('params');
        $type = $params['type'];
        $data['results'] = $this->site_model->searchSuppliers($term, $limit, $type);
        if ($id) {
            $supplier = $this->site_model->rowSupplier($id);
            // $data['row'] = ['id' => $supplier['id'], 'text' => $supplier['company'], 'code' => $supplier['prefix']."-".$supplier['code']];
            $data['row'] = ['id' => $supplier['id'], 'text' => $supplier['company'], 'code' => $supplier['code']];
        }
        echo json_encode($data);
    }

    //hoàng crm bổ xung table customer
    public function table_single_client($clientid = '')
    {
        $this->app->get_table_data('orders_singer_client', [
            'clientid' => $clientid,
        ]);
    }

    public function staff_orders()
    {
        $this->app->get_table_data('staff_orders');
    }
    //end

    public function showPriceCost()
    {
        $cItemId = $this->input->post('cItemId');
        $cQuantity = number_unformat($this->input->post('cQuantity'));
        $order_id = $this->input->post('order_id');
        $data = [];
        if ($cItemId && $cQuantity) {
            $arrItem = explode("__", $cItemId);
            $itemId = $arrItem[0];
            $itemType = $arrItem[1];
            $itemTypeTerm = $itemType;

            if ($itemType == "products") {
                $itemType = "product";
            } else if ($itemType == "materials") {
                $itemType = "nvl";
            } else if ($itemType == "items") {
                $itemType = "items";
            }

            $result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $itemId);

            $priceCost = 0;
            $testQty = 0;
            $pLast = 0;
            foreach ($result as $key => $value) {
                if ($cQuantity <= 0) break;
                $qty = $value['quantity_left'];
                $p = $value['price'];
                $cQuantityTerm = $cQuantity;
                $cQuantity -= $qty;
                if ($cQuantity >= 0) {
                    $pCost = $qty * $p;
                    // $testQty+= $qty;
                } else if ($cQuantity < 0) {
                    $pCost = $cQuantityTerm * $p;
                    // $testQty+= $cQuantityTerm;
                }
                $priceCost += $pCost;
            }

            if ($cQuantity > 0) {
                $priceLast = $this->site_model->getPriceLast($itemType, $itemId);
                if (!empty($priceLast)) {
                    $pLast = $priceLast['price'];
                }
                $priceCost += $cQuantity * $pLast;
                // $rs = $this->site_model->getOrdersItemSellFirst($itemId, $itemTypeTerm, $order_id);
                // if (!empty($rs)) {
                //     $priceCost+= $rs['price'] * $cQuantity;
                // } else {
                //     if ($itemTypeTerm == "products")
                //     {
                //         $product = $this->products_model->rowProduct($itemId);
                //         if (!empty($product))
                //         {
                //             $priceCost+= $product['price_import'] * $cQuantity;
                //         }
                //     } else if ($itemTypeTerm == "items") {
                //         $item = $this->items_model->rowItems($itemId);
                //         if (!empty($item))
                //         {
                //             $priceCost+= $item['price_import'] * $cQuantity;
                //         }
                //     }
                // }
            }
            $data['priceCost'] = $priceCost;
        }
        echo json_encode($data);
    }

    public function import_orders()
    {
		
		//có sửa ngày date ship để hoàng làm
        if (!$this->perAddOrders) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            // if ($_FILES['userfile']['size'] > $this->allowed_file_size * 1024) {
            //     $this->session->set_flashdata('warning', lang('Không vượt quá '. $this->allowed_file_size. ' size'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            //     return;
            // }
            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('AJ');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 4; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 4;
            $index_parent = 0;
            $index_parent_items = 0;
            $ref = '';
            $refItem = '';
            $dataImport = [];
            foreach ($arraydata as $key => $value) {
                // 0: reference: Số đơn hàng
                // 1: date: Ngày
                // 2: customer: Khách hàng
                // 3: person_contact: Người liên lạc
                // 4: address_delivery: Đỉa chỉ giao hàng
                // 5: id_branch: chi nhánh xưởng
                // 6: currencies: tiền tệ
                // 7: amount_to_vnd: Quy đổi VND
                // 8: type_orders: Loại đơn hàng
                // 9: type_items: Loại sản phẩm
                // 10: status_orders: Trạng thái đơn hàng
                // 11: employees: Nhân viên phụ trách
                // 12: tax: thuế
                // 13: cost_delivery: Chi phí giao hàng
                // 14: transporters: Nhà vận chuyển
                // 15: charge_party: Bên chịu phí
                // 16: note: Ghi chú tổng
                // 17: item_code: Mã thành phẩm
                // 18: item_name: Tên thành phẩm
                // 19: product_name_customer: Tên TP của khách hàng
                // 20: unit: đơn vị
                // 21: order_code: Mã đơn đặt
                // 22: command: Chỉ lệnh
                // 23: quantity_put: SL đặt
                // 24: quantity_loss: SL loss
                // 25: sample_quantity_item: SL làm mẫu
                // 26: total_quantity_item: tổng sl
                // 27: price: Đơn giá
                // 28: amount: Tổng tiền
                // 29: date_delivery: Ngày giao hàng
                // 30: detail_delivery: Chi tiết giao hàng (Ngày - SL ||)
                // 31: note_item: ghi chú mặt hàng
                // 32: so
                // 33: pi
                // 34: po_style
                // 35: item_code_tem


                $reference = trim($value[0]);
                $date = $value[1];
                if (gettype($date) == 'double' || gettype($date) == 'int') {
                    $date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date));
                } else if (gettype($date) == 'string') {
                    $date = to_sql_date($date);
                }
//				print_arrays($value);
                $customer = trim($value[2]);
                $person_contact = trim($value[3]);
                $address_delivery = trim($value[4]);
                $id_branch = trim($value[5]);
                $currencies = trim($value[6]);
                $amount_to_vnd = number_unformat($value[7]);
                $type_orders = trim($value[8]);
                $type_items = trim($value[9]);
                $status_orders = trim($value[10]);
                $employees = trim($value[11]);
                $tax = trim($value[12]);
                $cost_delivery = number_unformat($value[13]);
                $transporters = trim($value[14]);
                $charge_party = trim($value[15]);
                $discount_percent = 0;
                $discount_direct = 0;
                $note = trim($value[16]);
                $item_code = trim($value[17]);
                $item_name = trim($value[18]);
                $product_name_customer = trim($value[19]);
                $unit = trim($value[20]);
                $date_ship = trim($value[21]);
                $order_code = trim($value[22]);
                $command = trim($value[23]);
                $quantity_put = number_unformat($value[24]);
                $quantity_loss = number_unformat($value[25]);
                $sample_quantity_item = number_unformat($value[26]);
                $total_quantity_item = number_unformat($value[27]);
                $price = number_unformat($value[28]);
                $amount = number_unformat($value[29]);
                $date_delivery = $value[30];
                $detail_delivery = trim($value[31]);
                $note_item = trim($value[32]);
                $so = trim($value[33]);
                $pi = trim($value[34]);
                $po_style = trim($value[35]);
                $item_code_tem = !empty($value[36]) ? trim($value[36]) : NULL;

                if (!empty($reference) && $reference != $ref) {
                    $dataImport[$index_parent]['reference'] = $reference;
                    $dataImport[$index_parent]['date'] = $date;
                    $dataImport[$index_parent]['customer'] = $customer;
                    $dataImport[$index_parent]['person_contact'] = $person_contact;
                    $dataImport[$index_parent]['address_delivery'] = $address_delivery;
                    $dataImport[$index_parent]['id_branch'] = $id_branch;
                    $dataImport[$index_parent]['currencies'] = $currencies;
                    $dataImport[$index_parent]['amount_to_vnd'] = $amount_to_vnd;
                    $dataImport[$index_parent]['type_orders'] = $type_orders;
                    $dataImport[$index_parent]['type_items'] = $type_items;
                    $dataImport[$index_parent]['status_orders'] = $status_orders;
                    $dataImport[$index_parent]['employees'] = $employees;
                    $dataImport[$index_parent]['tax'] = $tax;
                    $dataImport[$index_parent]['cost_delivery'] = $cost_delivery;
                    $dataImport[$index_parent]['transporters'] = $transporters;
                    $dataImport[$index_parent]['charge_party'] = $charge_party;
                    $dataImport[$index_parent]['discount_percent'] = $discount_percent;
                    $dataImport[$index_parent]['discount_direct'] = $discount_direct;
                    $dataImport[$index_parent]['note'] = $note;
                    $dataImport[$index_parent]['so'] = $so;
                    $dataImport[$index_parent]['pi'] = $pi;
                    $dataImport[$index_parent]['po_style'] = $po_style;
                    $dataImport[$index_parent]['item_code_tem'] = $item_code_tem;

                    $refItem = '';
                    $parent_current = $index_parent;
                    $ref = $reference;
                    $index_parent++;
                }

                if (!empty($item_code) && $item_code != $refItem) {
                    $dataImport[$parent_current]['items'][$index_parent_items] = [
                        'item_code' => $item_code,
                        'item_name' => $item_name,
                        'product_name_customer' => $product_name_customer,
                        'unit' => $unit,
                        'price' => $price,
                        'amount' => $amount,
                        'date_delivery' => $date_delivery,
                        'detail_delivery' => $detail_delivery,
                        'total_quantity_item' => $total_quantity_item,
                        'note_item' => $note_item,
                    ];

                    $parent_current_item = $index_parent_items;
                    $refItem = $item_code;
                    $index_parent_items++;
                }

                if (!empty($order_code)) {
                    $dataImport[$parent_current]['items'][$parent_current_item]['detail'][] = [
                        'date_ship' => $date_ship,
                        'order_code' => $order_code,
                        'command' => $command,
                        'quantity_put' => $quantity_put,
                        'quantity_loss' => $quantity_loss,
                        'sample_quantity_item' => $sample_quantity_item,
                    ];
                }
            }

            $listRef = [];
            if (!empty($dataImport)) {
                foreach ($dataImport as $key => $value) {
                    $date = $value['date'];
                    $reference_no = $value['reference'];
                    $_reference_no = getReference('orders');
                    if ($this->orders_model->checkExistOrders($_reference_no)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] không thể thêm vì đã tồn tại trong phần mềm</div>';
                        continue;
                    }

                    $customerName = $value['customer'];
                    $customer = $this->site_model->getClientByZcodeOrCompany($customerName);
                    if (empty($customer)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại trong phầm mềm</div>';
                        continue;
                    }
                    $customer_id = $customer['userid'];
                    $customer_name = $customer['company'];

                    $so = $value['so'];
                    $pi = $value['pi'];
                    $po_style = $value['po_style'];
                    $item_code_tem = $value['item_code_tem'];

                    //handling person contract
                    $person_contract = $value['person_contact'];
                    if (empty($person_contract)) {
                        $this->db->select('tblcontacts.id');
                        $this->db->from('tblcontacts');
                        $this->db->where('tblcontacts.userid', $customer_id);
                        $this->db->limit(1);
                        $contract = $this->db->get()->row_array();
                        $person_contact_id = !empty($contract['id']) ? $contract['id'] : 0;
                    } else {
                        $contract = $this->site_model->getContractByFirstName($person_contract, $customer_id);
                        if (empty($contract)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại người liên lạc [' . $person_contract . '] trong phầm mềm</div>';
                            continue;
                        }
                        $person_contact_id = $contract['id'];
                    }

                    //end

                    $str_id_branch = $value['id_branch'];
                    $id_branch = 0;
                    if (!empty($str_id_branch)) {
                        $dtBranch = $this->site_model->getBranchByName($str_id_branch);
                        if (empty($dtBranch)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì chi nhánh xưởng [' . $str_id_branch . '] không tồn tại trong phầm mềm</div>';
                            continue;
                        }
                        $id_branch = $dtBranch['id'];
                    }

                    $str_currencies = $value['currencies'];
                    $currencies = 0;
                    if (!empty($str_currencies)) {
                        $dtCurrencies = $this->site_model->getCurrenciesByName($str_currencies);
                        if (empty($dtCurrencies)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì tiền tệ [' . $str_currencies . '] không tồn tại trong phầm mềm</div>';
                            continue;
                        }
                        $currencies = $dtCurrencies['id'];
                    }

                    $amount_to_vnd = $value['amount_to_vnd'];
                    if (empty($amount_to_vnd)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì quy đổi VND [' . $amount_to_vnd . '] không nhập</div>';
                        continue;
                    }

                    $type_orders = $value['type_orders'];
                    if (!in_array($type_orders, [1, 2, 3, 4, 11, 12, 13, 14])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì loại đơn hàng không đúng định dạng [1, 2, 3, 4, 11, 12, 13, 14]</div>';
                        continue;
                    }

                    $type_items = $value['type_items'];
                    if (!in_array($type_items, [1, 2])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì loại sản phẩm không đúng định dạng [1, 2]</div>';
                        continue;
                    }

                    $status_orders = $value['status_orders'];
                    if (!in_array($status_orders, [1, 4, 5])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì trạng thái đơn hàng không đúng định dạng [1, 4, 5]</div>';
                        continue;
                    }

                    $addressDelivery = $value['address_delivery'];
                    $address_delivery_id = 0;
                    if (!empty($addressDelivery)) {
                        $address_delivery = $this->site_model->getShippingClientByClientAndAddress($customer_id, $addressDelivery);
                        if (empty($address_delivery)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại đỉa chỉ [' . $addressDelivery . '] trong phầm mềm</div>';
                            continue;
                        }
                        $address_delivery_id = $address_delivery['id'];
                    } else {
                        $this->db->select('
                            tblshipping_client.id
                        ', false);
                        $this->db->from('tblshipping_client');
                        $this->db->where('tblshipping_client.client', $customer_id);
                        $this->db->limit(1);
                        $shipping_client = $this->db->get()->row_array();
                        $address_delivery_id = !empty($shipping_client) ? $shipping_client['id'] : 0;
                    }

                    //handling employee
                    $employee = $value['employees'];
                    if (empty($employee)) {
                        // $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại nhân viên phụ trách</div>';
                        // continue;
                        $employeeId = get_staff_user_id();
                    }

                    if (!empty($employee)) {
                        $staffName = $employee;
                        $staff = $this->site_model->getStaffByName($staffName);
                        if (empty($staff)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhân viên phụ trách [' . $staffName . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $employeeId = $staff['staffid'];
                    }
                    //end employee

                    //handling tax
                    $tax = $value['tax'];
                    $tax_id = 0;
                    $tax_rate = 0;
                    $tax_name = 0;
                    if (!empty($tax)) {
                        $dTax = $this->site_model->getTaxesByName($tax);
                        if (empty($dTax)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $tax_id = $dTax['id'];
                        $tax_rate = $dTax['taxrate'];
                        $tax_name = $dTax['name'];
                    }
                    //end tax

                    //handling transporters
                    $transporters = $value['transporters'];
                    $transporter_id = 0;
                    if (!empty($transporters)) {
                        $transport = $this->site_model->getTransportByName($transporters);
                        if (empty($transport)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhà vận chuyển [' . $transporters . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $transporter_id = $transport['id'];
                    }
                    //end handling transporters

                    //charge party: Bên chịu phí
                    $charge_party = !empty($value['charge_party']) ? $value['charge_party'] : 1;
                    if (!in_array($charge_party, [1, 2])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] vì bên chịu phí không đúng giá trị [1, 2]</div>';
                        continue;
                    }
                    $charge_party = ($charge_party == 1) ? 'company' : 'customer';
                    //

                    $items = $value['items'];
                    if (empty($items)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại mặt hàng</div>';
                        continue;
                    }

                    //handling items
                    $note = $value['note'];
                    $count_items = 0;
                    $total_quantity = 0;
                    $total_amount_items = 0;
                    $total_tax_items = 0;
                    $total_discount_percent_items = 0;
                    $total_discount_direct_items = 0;
                    $grand_total_items = 0;
                    $discount_percent = !empty($value['discount_percent']) ? $value['discount_percent'] : 0;
                    $total_discount_percent = 0;
                    $total_discount_direct = !empty($value['discount_direct']) ? $value['discount_direct'] : 0;
                    $cost_delivery = !empty($value['cost_delivery']) ? $value['cost_delivery'] : 0;
                    $grand_total = 0;
                    $status = 'un_approved';
                    $gift = 0;
                    $total_cost_temporary_capital = 0;
                    $total_profit_temporary_capital = 0;

                    $flagErrorsItems = false;
                    $itemsIn  = [];
                    $grand_total_quantity = 0;
                    $counter_item = 0;

                    $dtTablePrice = $this->orders_model->getGroupPriceCustomer($customer_id);
                    $table_price_id = !empty($dtTablePrice) ? $dtTablePrice['id'] : 0;
                    foreach ($items as $k => $val) {
                        $item_type = !empty($val['item_type']) ? $val['item_type'] : 1;
                        $item_code = trim($val['item_code']);
                        if (empty($item_code)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng bị trống</div>';
                            $flagErrorsItems = true;
                            break;
                        }
                        if (empty($item_code)) continue;
                        $loss = 0;
                        $type_item = "products";
                        $item = $this->site_model->getProductsByCode($item_code);
                        if (empty($item)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không tồn tại trong phần mềm</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $conversion_quantity_unit = 1;
                        $conversion_quantity_unit_default = 1;
                        $quantity_child_sheet = $item['quantity_child_sheet'];
                        $quantity_sheet_bale = $item['quantity_sheet_bale'];
                        $loss = $item['loss'];

                        $product_name_customer = $val['product_name_customer'];
                        if (empty($product_name_customer)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] chưa nhập tên TP của khách hàng</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $unit = $val['unit'];
                        $dtUnits = $this->unit_model->rowUnitByCode($unit, '*', 'where');
                        if (empty($dtUnits)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] đơn vị ['.$unit.'] chưa có trong phần mềm</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $unit_id = $dtUnits['unitid'];
                        if ($unit_id != $item['unit_id'] && $unit_id != $item['conversion_unit']) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có đơn vị tính ['.$unit.'] trong thành phẩm</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        if ($unit_id != $item['unit_id']) {
                            $conversion_quantity_unit = $item['conversion_quantity_unit'];
                        }

                        $ct_counter_item = 0;
                        $arrItemsChildColumns = [];
                        $counter_items_number = 0;
                        $quantity = 0;
                        $total_quantity_loss = 0;
                        $total_quantity_sample = 0;
                        $detail = !empty($val['detail']) ? $val['detail'] : null;
                        if (empty($detail)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có các dòng mã đơn đặt</div>';
                            $flagErrorsItems = true;
                            break;
                        } else {
                            foreach ($detail as $kD => $vD) {
                                $date_ship = $vD['date_ship'];
                                if (empty($date_ship)) {
                                    $errors .= '<div>Ngày giao [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có ngày giao bị trống</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }
								
								$order_code = $vD['order_code'];
                                if (empty($order_code)) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có mã đơn đặt bị trống</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $command = $vD['command'];
                                if (empty($command)) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có chỉ lệnh bị trống</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $quantity_put = $vD['quantity_put'];
                                if (empty($quantity_put)) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có số lượng đặt</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $quantity_loss = roundNumberFormat($quantity_put * $loss/100, 0);
                                $sample_quantity_item = $vD['sample_quantity_item'];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $date_ship,
                                    'columns_value' => 'date_ship',
                                    'counter_items_number' => $counter_items_number,
                                ];
								$arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $order_code,
                                    'columns_value' => 'order_code',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $command,
                                    'columns_value' => 'command',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $quantity_put,
                                    'columns_value' => 'quantity_put',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $quantity_loss,
                                    'columns_value' => 'quantity_loss',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $sample_quantity_item,
                                    'columns_value' => 'sample_quantity_item',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $quantity += $quantity_put;
                                $total_quantity_loss += $quantity_loss;
                                $total_quantity_sample += $sample_quantity_item;

                                $counter_items_number++;
                                $counter_item++;
                            }
                        }
                        
                        $ct_counter_item = $counter_items_number;
                        $sample_quantity =  $total_quantity_sample;
                        $item_id = $item['id'];
                        $items_code = $item['code'];
                        $items_name = $item['name'];
                        // $quantity = $val['quantity'];
                        $price = $val['price'];
                        if (empty($price) && !empty($table_price_id)) {
                            $price = $this->orders_model->getPriceCustomer($table_price_id, $customer_id, $item_id, 'product', $quantity);
							if($unit_id == $item['conversion_unit'] && !empty($item['conversion_quantity_unit'])) {
								$price = $price / $item['conversion_quantity_unit'];
							}
                        }

                        $note_item = $val['note_item'];
                        $amount = $quantity * $price;

                        $total_quantity_item = $sample_quantity + $quantity + $total_quantity_loss;
                        if ($type_orders == TYPE_PTM) {
                            if ($total_quantity_item < QUANTITY_PTM) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] loại phát triển mẫu có số lượng >= '.QUANTITY_PTM.'</div>';
                                $flagErrorsItems = true;
                                break;
                            }
                        }

                        $grand_total_quantity += $total_quantity_item;

                        $sub = [];
                        $total_quantity_sub = 0;
                        $date_delivery = $val['date_delivery'];
                        if (!empty($date_delivery)) {
                            $date_shipping = $date_delivery;
                            if (gettype($date_shipping) == 'double' || gettype($date_shipping) == 'int') {
                                $date_shipping = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_shipping));
                            } else if (gettype($date_shipping) == 'string') {
                                $date_shipping = to_sql_date($date_shipping);
                            }

                            $sub[] = [
                                'date_shipping' => $date_shipping,
                                'quantity_shipping' => $total_quantity_item,
                            ];

                            // $date_delivery = explode("::", $date_delivery);
                            // foreach ($date_delivery as $i => $v) {
                            //     $v = explode("||", $v);
                            //     if (empty($v) || empty($v[0])) continue;
                            //     $date_shipping = to_sql_date($v[0]);
                            //     $quantity_sub = !empty($v[1]) ? number_unformat($v[1]) : 0;

                            //     $sub[] = [
                            //         'date_shipping' => $date_shipping,
                            //         'quantity_shipping' => $quantity_sub,
                            //     ];
                            //     $total_quantity_sub += $quantity_sub;
                            // }
                            // if ($total_quantity_sub > $quantity) {
                            //     $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được số lượng giao hàng dự kiến lớn hơn số lượng trong mặt hàng</div>';
                            //     $flagErrorsItems = true;
                            //     break;
                            // }
                        } else {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có ngày giao hàng dự kiến</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $detail_delivery = $val['detail_delivery'];
                        $ship = [];
                        if (!empty($detail_delivery)) {
                            $detail_delivery = explode('||', $detail_delivery);
                            if (!empty($detail_delivery)) {
                                foreach ($detail_delivery as $kD => $vD) {
                                    $arr_detail_delivery = explode('-', $vD);
                                    $date_detail_delivery = trim($arr_detail_delivery[0]);
                                    if (empty($date_detail_delivery)) continue;
                                    $quantity_detail_delivery = number_unformat($arr_detail_delivery[1]);
                                    if (gettype($date_detail_delivery) == 'double' || gettype($date_detail_delivery) == 'int') {
                                        $date_detail_delivery = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_detail_delivery));
                                    } else if (gettype($date_detail_delivery) == 'string') {
                                        $date_detail_delivery = to_sql_date($date_detail_delivery);
                                    }

                                    $ship[] = [
                                        'date' => $date_detail_delivery,
                                        'quantity' => $quantity_detail_delivery,
                                    ];
                                }
                            }
                        }

                        $grand_total_item = $amount;
                        $tax_amount_item = 0;
                        $tax_name_item = '';
                        if (!empty($tax_name_item)) {
                            $info_tax = $this->site_model->getTaxesByName($tax_name_item);
                            if (empty($info_tax)) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax_name_item . '] không tồn tại trong phần mềm</div>';
                                break;
                            }
                            $tax_name_item = $info_tax['name'];
                            $tax_rate_item = $info_tax['taxrate'];
                            $tax_id_item = $info_tax['id'];
                        } else {
                            $tax_name_item = "0%";
                            $tax_rate_item = 0;
                            $tax_id_item = 0;
                        }

                        $discount_percent_item = 0;
                        $discount_percent_amount_item = 0;
                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                            $total_discount_percent_items += $discount_percent_amount_item;
                            $grand_total_item -= $discount_percent_amount_item;
                        }
                        $discount_direct_amount_item = 0;
                        $total_discount_direct_items += $discount_direct_amount_item;
                        $grand_total_item -= $discount_direct_amount_item;

                        //handling cost temporary capital
                        if ($type_item == "products") {
                            $itemType = "product";
                        } else if ($type_item == "items") {
                            $itemType = "items";
                        }
                        $result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $item_id);
                        $priceCost = 0;
                        $cQuantity = $quantity;
                        foreach ($result as $i => $v) {
                            if ($cQuantity <= 0) break;
                            $qty = $v['quantity_left'];
                            $p = $v['price'];

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
                            $rs = $this->site_model->getOrdersItemSellFirst($item_id, $type_item);
                            if (!empty($rs)) {
                                $priceCost += $rs['price'] * $cQuantity;
                            } else {
                                $priceCost += $item['price_import'] * $cQuantity;
                            }
                        }

                        //end handling cost temporary capital
                        $cost_temporary_capital = $priceCost;
                        $profit_temporary_capital = $grand_total_item - $priceCost;

                        if ($tax_rate_item > 0) {
                            $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                            $total_tax_items += $tax_amount_item;
                            $grand_total_item += $tax_amount_item;
                        }

                        $itemsIn[] = [
                            'quantity_loss' => $total_quantity_loss,
                            'sample_quantity' => $sample_quantity,
                            'total_quantity_item' => $total_quantity_item,

                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'tax_id_item' => $tax_id_item,
                            'tax_name_item' => $tax_name_item,
                            'tax_rate_item' => $tax_rate_item,
                            'tax_amount_item' => $tax_amount_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'note_item' => $note_item,
                            'cost_temporary_capital' => $cost_temporary_capital,
                            'profit_temporary_capital' => $profit_temporary_capital,
                            'quantity_child_sheet' => $quantity_child_sheet,
                            'quantity_sheet_bale' => $quantity_sheet_bale,
                            'sub' => $sub,
                            'arrItemsChildColumns' => $arrItemsChildColumns,
                            'ct_counter_item' => $ct_counter_item,
                            'hand_input_price' => 1,
                            'loss' => $loss,
                            'product_name_customer' => $product_name_customer,
                            'ship' => $ship,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                            'conversion_quantity_unit_default' => $conversion_quantity_unit_default
                        ];

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                        $total_cost_temporary_capital += $cost_temporary_capital;
                    }

                    if ($flagErrorsItems) continue;

                    if (empty($itemsIn)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không có mặt hàng</div>';
                        continue;
                    }

                    $count_items = count($itemsIn);
                    $grand_total = $grand_total_items;

                    if ($discount_percent > 0) {
                        $total_discount_percent = $grand_total * ($discount_percent / 100);
                    }
                    $grand_total -= $total_discount_percent;
                    $grand_total -= $total_discount_direct;

                    $total_profit_temporary_capital = $grand_total - $total_cost_temporary_capital;
                    $total_profit_temporary_capital -= $cost_delivery;

                    $total_tax = 0;
                    if ($tax_rate > 0) {
                        $total_tax = $grand_total * ($tax_rate / 100);
                    }

                    $grand_total += $total_tax;
                    if ($charge_party == "customer") {
                        $grand_total += $cost_delivery;
                    } else {
                        //công ty
                    }

                    $options = [
                        'date' => $date,
                        // 'reference_no' => $reference_no,
                        'reference_no' => $_reference_no,
                        'customer_id' => $customer_id,
                        'customer_name' => $customer_name,
                        'address_delivery_id' => $address_delivery_id,
                        'employee_id' => $employeeId,
                        'note' => $note,
                        'count_items' => $count_items,
                        'total_quantity' => $total_quantity,
                        'total_amount_items' => $total_amount_items,
                        'total_tax_items' => $total_tax_items,
                        'total_discount_percent_items' => $total_discount_percent_items,
                        'total_discount_direct_items' => $total_discount_direct_items,
                        'grand_total_items' => $grand_total_items,
                        'tax_id' => $tax_id,
                        'tax_name' => $tax_name,
                        'tax_rate' => $tax_rate,
                        'total_tax' => $total_tax, //tổng thuế
                        'discount_percent' => $discount_percent, //% chiết khấu
                        'total_discount_percent' => $total_discount_percent, //tổng tiền chiết khấu phần trăm
                        'total_discount_direct' => $total_discount_direct, //tổng tiền chiết khấu tiền mặt
                        'grand_total' => $grand_total, //tổng tiền đơn hàng
                        'status' => $status,
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                        'table_price_id' => $table_price_id,
                        'table_discount_id' => 0,
                        'cost_delivery' => $cost_delivery,
                        'gift' => $gift,
                        'transporter_id' => $transporter_id,
                        'charge_party' => $charge_party,
                        'person_contact_id' => $person_contact_id,
                        'total_cost_temporary_capital' => $total_cost_temporary_capital, //giá vốn tạm thời
                        'total_profit_temporary_capital' => $total_profit_temporary_capital, //chi phí lợi nhuận tạm thời
                        'id_branch' => $id_branch,
                        'currencies' => $currencies,
                        'amount_to_vnd' => $amount_to_vnd,
                        'type_orders' => $type_orders,
                        'status_orders' => $status_orders,
                        'type_items' => $type_items,
                        'grand_total_quantity' => $grand_total_quantity,
                        'so' => $so,
                        'pi' => $pi,
                        'po_style' => $po_style,
                        'item_code' => $item_code_tem,
                    ];

                    // print_arrays($options, $itemsIn);
                    $order_id = $this->orders_model->insertOrdersNew($options);
                    if ($order_id) {
                        if (getReference('orders') == $_reference_no) {
                            updateReference('orders');
                        }

                        foreach ($itemsIn as $k => $val) {
                            $val['order_id'] = $order_id;
                            $sub = $val['sub'];
                            $ship = $val['ship'];
                            $arrItemsChildColumns = $val['arrItemsChildColumns'];
                            unset($val['sub']);
                            unset($val['ship']);
                            unset($val['arrItemsChildColumns']);

                            $order_item_id = $this->orders_model->insertOrderItemsNew($val);
                            if ($order_item_id) {
                                if (!empty($sub)) {
                                    foreach ($sub as $i => $v) {
                                        $v['order_item_id'] = $order_item_id;
                                        $this->orders_model->insertOrderItemShippingsNew($v);
                                    }
                                }

                                if (!empty($ship)) {
                                    foreach ($ship as $kSh => $valSh) {
                                        $this->db->insert('tbl_orders_ship', [
                                            'order_item_id' => $order_item_id,
                                            'date' => $valSh['date'],
                                            'quantity' => $valSh['quantity'],
                                        ]);
                                    }
                                }

                                if (!empty($arrItemsChildColumns)) {
                                    foreach ($arrItemsChildColumns as $kC => $vC) {
                                        $arrItemsChildColumns[$kC]['order_id'] = $order_id;
                                        $arrItemsChildColumns[$kC]['order_item_id'] = $order_item_id;
                                    }
                                    $this->orders_model->insertBatchOrderItemsColumns($arrItemsChildColumns);
                                }

                                if ($val['type_item'] == "products") {
                                    $exchangeUnits = $this->products_model->getExchangeProductsByProductId($val['item_id']);
                                    if (!empty($exchangeUnits)) {
                                        foreach ($exchangeUnits as $kk => $vv) {
                                            if (empty($vv)) continue;
                                            $quantity_exchange = $vv['number_exchange'];
                                            $total_quantity_exchange = $val['quantity'] / $quantity_exchange;
                                            $exchange = [
                                                'order_item_id' => $order_item_id,
                                                'unit_id' => $vv['unit_id'],
                                                'quantity_exchange' => $quantity_exchange,
                                                'total_quantity_exchange' => $total_quantity_exchange,
                                            ];
                                            $this->orders_model->insertOrderItemExchange($exchange);
                                        }
                                    }
                                }
                            }
                        }

                        // $wf = $this->site_model->insertOrdersWorkflow([
                        //     'workflow_id' => 0,
                        //     'order_id' => $order_id,
                        //     'created_by' => get_staff_user_id(),
                        //     'date_created' => date('Y-m-d H:i:s'),
                        // ]);

                        $listRef[] = $reference_no;
                        $count++;
                    }
                }
            }
            //handling show nofitications
            $data['errors'] = $errors;
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
                insertActivityLog([
                    'type_parent_obj' => 'orders',
                    'table_obj' => 'tbl_orders',
                    'id_obj' => $order_id,
                    'name_obj' => $reference_no,
                    'content' => lang('tnh_his_add_orders') . ' [' . implode(',', $listRef) . ']',
                    'actions' => 'import'
                ]);
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data);
            die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_orders'). ' cố định';
            $this->load->view('admin/orders/import_orders', $data);
        }
    }

    public function add_payment($id)
    {
        $order = $this->orders_model->rowOrderById($id);
        $total_returns = $this->orders_model->getTotalOrdersReturn($id);
        if ($this->input->post()) {
            $data = [];
            $this->form_validation->set_rules('staff', lang("staff_coupon"), 'required');
            $this->form_validation->set_rules('payment_mode', lang("acs_sales_payment_modes_submenu"), 'required');
            $this->form_validation->set_rules('payment', lang("tnh_payment"), 'required');
            if ($this->form_validation->run() == true) {
                $payment = number_unformat($this->input->post('payment'));
                $date = to_sql_date($this->input->post('date'));
                $staff_coupon = $this->input->post('staff');
                $payment_mode = $this->input->post('payment_mode');
                $note = $this->input->post('note');
                $customer_id = $order['customer_id'];
                $grand_total = $order['grand_total'];
                $paid = $order['total_payment'];
                $total_return = $total_returns['total_return'];

                $paymentNeed = $grand_total - $paid  - $total_return - $order['price_other_expenses'];
                if ($paymentNeed < 0) $paymentNeed = 0;
                $order_id = $id;

                if ($payment <= 0) {
                    $data['result'] = 0;
                    $data['message'] = lang('Số tiền thanh toán phải lớn hơn 0');
                    echo json_encode($data);
                    die;
                }

                $total_rest = $grand_total - $paid - $total_return  - $order['price_other_expenses'];
                $total_rest = formatRound($total_rest);
                if ($payment > $total_rest) {
                    $data['result'] = 0;
                    $data['message'] = lang('Số tiền thanh toán vượt quá cho phép');
                    echo json_encode($data);
                    die;
                }

                if ($payment > 0 && !empty($order_id)) {
                    $paymentData = [
                        'staff_create' => get_staff_user_id(),
                        'date_create' => date('Y-m-d H:i:s'),
                        'date_vouchers' => $date,
                        'arr_code_orders' => $order_id . '|' . $payment,
                        'code_vouchers' => get_option('prefix_coupon') . sprintf('%06d', ch_getMaxID('id', 'tblvouchers_coupon') + 1),
                        'customer' => $customer_id,
                        'staff' => $staff_coupon,
                        'payment_mode' => $payment_mode,
                        'total' => $paymentNeed,
                        'payment' => $payment,
                        'note' => $note
                    ];

                    $this->db->insert('tblvouchers_coupon', $paymentData);
                    $insertPayment = $this->db->insert_id();
                    if ($insertPayment) {
                        $_pay_detail['id_order'] = $id;
                        $_pay_detail['id_vouchers'] = $insertPayment;
                        $_pay_detail['payment'] = $payment;
                        $this->db->insert('tblvouchers_coupon_detal', $_pay_detail);
                        $idPayment = $this->db->insert_id();

                        if ($payment >= $paymentNeed && $payment > 0) {
                            $status_payment = 2;
                        } else if ($payment > 0) {
                            $status_payment = 1;
                        }
                        $total_payment = $paid + $payment;
                        $this->orders_model->updateOrdersNew($id, ['total_payment' => $total_payment, 'status_payment' => $status_payment]);

                        $data['result'] = 1;
                        $data['message'] = lang('success');
                    }
                }
                if (empty($idPayment)) {
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
            $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
            $staff = get_table_where('tblstaff', array('active' => 1));
            $arr_staff = array();
            foreach ($staff as $key => $value) {
                $arr_staff[$key]['staffid'] = $value['staffid'];
                $arr_staff[$key]['fullname'] = $value['firstname'] . ' ' . $value['lastname'];
            }
            $data['staff'] = $arr_staff;
            $data['order'] = $order;
            $data['id'] = $id;
            $data['total_returns'] = $total_returns;

            $this->load->view('admin/orders/add_payment', $data);
        }
    }

    public function add_purchase($id)
    {
        $order = $this->orders_model->rowOrderById($id);

        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true) {
                $date = $this->input->post('date');
                $name = $this->input->post('name');
                $note = $this->input->post('note');
                $counter = $this->input->post('counter');
                $arr_id = [];
                $errors = '';

                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $order_item_id = $this->input->post('order_item_id')[$value];
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arr = explode("__", $items_id);
                        $type = $arr[0];
                        $itemId = $arr[1];
                        if ($type == "items") {
                            $type = "items";
                        } else if ($type == "materials") {
                            $type = "nvl";
                        } else {
                            continue;
                        }
                        $quantity = number_unformat($this->input->post('quantity')[$value]);

                        $strItem = $type . '__' . $items_id;
                        $index = array_search($strItem, $arr_id);
                        if ($index === false) {
                            $arr_id[] = $strItem;
                        } else {
                            $errors .= 'Có mặt hàng bị trùng vui lòng xóa';
                        }

                        $items[] = [
                            'order_item_id' => $order_item_id,
                            'id' => $itemId,
                            'quantity' => $quantity,
                            'quantity_net' => $quantity,
                            'type' => $type,
                            'note' => ''
                        ];
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('un_not_items_purchase');
                    echo json_encode($data);
                    die;
                }

                $fields = [
                    'order_id' => $id,
                    'name' => $name,
                    'reason' => $note,
                    'date' => $date,
                    'items' => $items
                ];
                $purchases_id = $this->orders_model->convertOrdersToPurchase($fields);
                if ($purchases_id > 0) {

                    // foreach ($items as $key => $value) {
                    //     $this->orders_model->upOrderItemQuantityPurchase($value['order_item_id'], $value['quantity'], $plus = 1);
                    // }

                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('tnh_pruchases_items') . ' [' . $order['reference_no'] . ']',
                        'actions' => 'add_purchase'
                    ]);

                    $channels = ['tnh-purchases'];
                    $channels = array_unique($channels);
                    $this->load->library('app_pusher');
                    $this->app_pusher->trigger($channels, 'tnh-purchases', []);

                    $staffPurchases = $this->site_model->getStaffChildPermission('notification_purchases', 'can_add_notifications');
                    if (!empty($staffPurchases)) {
                        $notifiedUsers = [];
                        $purchase = $this->orders_model->getPurchaseById($purchases_id);
                        foreach ($staffPurchases as $member) {
                            if ($member['id_staff'] != 0) {
                                $description = lang('tnh_add_request_purchase') . ' [' . $purchase['prefix'] . $purchase['code'] . ']';
                                $notified = add_notification([
                                    'description'     => $description,
                                    'touserid'        => $member['id_staff'],
                                    'fromcompany'     => 1,
                                    'fromuserid'      => null,
                                    'additional_data' => serialize([
                                        $description,
                                    ]),
                                    'link' => 'purchases',
                                ]);
                                if ($notified) {
                                    array_push($notifiedUsers, $member['id_staff']);
                                }

                                $channels = ['tnh-notification-popup-' . $member['id_staff']];
                                $channels = array_unique($channels);
                                $this->load->library('app_pusher');
                                $this->app_pusher->trigger($channels, 'tnh-notification-popup', [createdPopupNotification('purchases', $purchases_id, $description)]);
                            }
                        }
                        pusher_trigger_notification($notifiedUsers);
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
        }

        $data['order'] = $order;
        $data['id'] = $id;
        $this->load->view('admin/orders/add_purchase', $data);
    }

    public function searchItemsOrders($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $order_id = $params['order_id'];

        $this->db->select('
            tbl_order_items.id as order_item_id,
            tbl_order_items.type_item as type_item,
            tbl_order_items.item_id,
            CONCAT(tbl_order_items.type_item ,"__", tblitems.id) as id,
            CONCAT(tblitems.code, "(", tblitems.name, ")") as text,
            tblitems.name as name,
            tblitems.unit as unit_id,
            tblunits.unit as unit_name,
            SUM(tbl_order_items.quantity) as total_quantity', false);
        $this->db->from('tbl_order_items');
        $this->db->join('tblitems', 'tblitems.id = tbl_order_items.item_id', 'inner');
        $this->db->join('tblunits', 'tblunits.unitid = tblitems.unit', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tblitems.code', $q);
            $this->db->or_like('tblitems.name', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_order_items.type_item', 'items');
        $this->db->where('tbl_order_items.order_id', $order_id);
        $this->db->group_by('tbl_order_items.item_id');
        $this->db->limit($limit);
        $items = $this->db->get()->result_array();

        //materials
        $this->db->select('
            tbl_order_items.id as order_item_id,
            tbl_order_items.type_item as type_item,
            tbl_order_items.item_id,
            CONCAT(tbl_order_items.type_item ,"__", tbl_materials.id) as id,
            CONCAT(tbl_materials.code, "(", tbl_materials.name, ")") as text,
            tbl_materials.name as name,
            tbl_materials.unit_id as unit_id,
            tblunits.unit as unit_name,
            SUM(tbl_order_items.quantity) as total_quantity', false);
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_order_items.item_id', 'inner');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_order_items.type_item', 'materials');
        $this->db->where('tbl_order_items.order_id', $order_id);
        $this->db->group_by('tbl_order_items.item_id');
        $this->db->limit($limit);
        $materials = $this->db->get()->result_array();

        $results = [];

        if (!empty($items)) {
            $results[] = ['text' => lang('ch_items'), 'children' => $items];
        }

        if (!empty($materials)) {
            $results[] = ['text' => lang('materials'), 'children' => $materials];
        }
        $data['results'] = $results;

        if ($id) {
            $dt = explode('__', $id);
            $id = $dt[1];
            $type_item = $dt[0];
            if ($type_item == "products") {
                // $product = $this->products_model->rowProduct($id);
                // $data['row'] = ['id' => $product['id'].'__'.'products', 'text' => $product['code']];
            } else if ($type_item == "items") {
                $item = $this->items_model->rowItems($id);
                $data['row'] = ['id' => 'items__' . $item['id'], 'text' => $item['code'] . ' (' . $item['name'] . ')'];
            } else if ($type_item == "materials") {
                $item = $this->items_model->rowMaterial($id);
                $data['row'] = ['id' => 'items__' . $item['id'], 'text' => $item['code'] . ' (' . $item['name'] . ')'];
            }
        }
        echo json_encode($data);
    }

    public function getAllItemsMissingWarehouse()
    {
        $data = [];
        $order_id = $this->input->post('order_id');
        if ($order_id) {
            $arrItems = [];
            $index = 0;
            $this->db->select('
                tbl_order_items.id as order_item_id,
                tbl_order_items.item_id,
                CONCAT("items__", tblitems.id) as id,
                CONCAT(tblitems.code, "(", tblitems.name, ")") as text,
                tblitems.name as name,
                tblitems.unit as unit_id,
                tblunits.unit as unit_name,
                SUM(tbl_order_items.quantity) as total_quantity', false);
            $this->db->from('tbl_order_items');
            $this->db->join('tblitems', 'tblitems.id = tbl_order_items.item_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tblitems.unit', 'left');
            $this->db->where('tbl_order_items.type_item', 'items');
            $this->db->where('tbl_order_items.order_id', $order_id);
            $this->db->group_by('tbl_order_items.item_id');
            $items = $this->db->get()->result_array();
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $item_id = $value['item_id'];
                    $quantity = $value['total_quantity'];
                    $quantityWarehouse = $this->site_model->getTotalQuantityWarehouseItems($item_id, 'items')['total_quantity'];
                    $quantityPurchase = $quantity - $quantityWarehouse;
                    if ($quantityPurchase <= 0) {
                        unset($items[$key]);
                    } else {
                        // $items[$key]['quantity_purchase'] = $quantityPurchase;
                        $arrItems[$index] = $value;
                        $arrItems[$index]['quantity_purchase'] = $quantityPurchase;
                        $index++;
                    }
                }
            }

            //materials
            $this->db->select('
                tbl_order_items.id as order_item_id,
                tbl_order_items.item_id,
                CONCAT("materials__", tbl_materials.id) as id,
                CONCAT(tbl_materials.code, "(", tbl_materials.name, ")") as text,
                tbl_materials.name as name,
                tbl_materials.unit_id as unit_id,
                tblunits.unit as unit_name,
                SUM(tbl_order_items.quantity) as total_quantity', false);
            $this->db->from('tbl_order_items');
            $this->db->join('tbl_materials', 'tbl_materials.id = tbl_order_items.item_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
            $this->db->where('tbl_order_items.type_item', 'materials');
            $this->db->where('tbl_order_items.order_id', $order_id);
            $this->db->group_by('tbl_order_items.item_id');
            $items = $this->db->get()->result_array();
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $item_id = $value['item_id'];
                    $quantity = $value['total_quantity'];
                    $quantityWarehouse = $this->site_model->getTotalQuantityWarehouseItems($item_id, 'nvl')['total_quantity'];
                    $quantityPurchase = $quantity - $quantityWarehouse;
                    if ($quantityPurchase <= 0) {
                        unset($items[$key]);
                    } else {
                        // $items[$key]['quantity_purchase'] = $quantityPurchase;
                        $arrItems[$index] = $value;
                        $arrItems[$index]['quantity_purchase'] = $quantityPurchase;
                        $index++;
                    }
                }
            }

            $data['items'] = $arrItems;
        }
        echo json_encode($data);
    }

    public function getWarehousesLocation()
    {
        $data = [];
        $item_id = $this->input->post('item_id');
        $type_item = $this->input->post('type_item');
        $order_id = $this->input->post('order_id');
        $order_item_id = $this->input->post('c_order_item_id');
        $dtOrderItem = $this->orders_model->rowOrderItemsById($order_item_id);
        $conversion_quantity_unit = 1;

        if ($type_item == "materials") {
            $type_item = "nvl";
        } else if ($type_item == "products") {
            $type_item = "product";
            $info = $this->products_model->rowProduct($item_id);
            if ($dtOrderItem['unit_id'] == $info['unit_id']) {
                $conversion_quantity_unit = $info['conversion_quantity_unit'];
            }

        } else if ($type_item == "items") {
            $type_item = "items";
        } else if ($type_item == "tools_supplies") {
            $type_item = "tools";
        }

        $warehouses = $this->site_model->getWarehouseNew();
        $group = '';
        $option = '';
        // $option = '<option value=""></option>';
        foreach ($warehouses as $key => $value) {
            $warehouseId = $value['id'];
            if ($group != $warehouseId) {
                if ($group != '') {
                    $option .= '</optgroup>';
                }
                $option .= '<optgroup label="' . $value['name'] . '">';
            }
            $locationWarehouse = $this->site_model->getLocationWarehouseQuantityNew($warehouseId, $item_id, $type_item, $order_id);
            if (!empty($locationWarehouse)) {
                foreach ($locationWarehouse as $k => $val) {
                    $val['quantity_warehouse'] = $val['quantity_warehouse']/$conversion_quantity_unit;
                    $lot_code = !empty($val['lot']) ? ' (Lot : ' . $val['lot'] . ')' : '';
                    $lot_code_check = '__' . (!empty($val['lot']) ? $val['lot'] : '') . '__' . (!empty($val['date_sx']) ? $val['date_sx'] : '') . '__' . (!empty($val['date_sd']) ? $val['date_sd'] : '') . '__' . (!empty($val['date_use']) ? $val['date_use'] : '');
                    $option .= '<option data-quantity="' . $val['quantity_warehouse'] . '" value="' . $val['id'] . $lot_code_check . '">' . $val['name_location'] . ' - ' . formatNumber($val['quantity_warehouse']) . $lot_code . '</option>';
                }
            }

            $group = $warehouseId;
        }
        if ($group != '') {
            $option .= '</optgroup>';
        }
        $data['option'] = $option;
        echo json_encode($data);
    }

    public function getCalendarOrdersDelivery($start, $end, $client_id = '', $contact_id = '', $filters = false)
    {
        $this->load->model('manufactures_model');
        $is_admin                     = is_admin();
        $has_permission_tasks_view    = has_permission('tasks', '', 'view');
        $data                         = [];

        $client_data = false;
        if (is_numeric($client_id) && is_numeric($contact_id)) {
            $client_data = true;
        }

        $hook = [
            'client_data' => $client_data,
        ];
        if ($client_data == true) {
            $hook['client_id']  = $client_id;
            $hook['contact_id'] = $contact_id;
        }

        $data = hooks()->apply_filters('before_fetch_events', $data, $hook);

        $ff = false;
        if ($filters) {
            // excluded calendar_filters from post
            $ff = (count($filters) > 1 && isset($filters['calendar_filters']) ? true : false);
        }

        // print_arrays($start, '</br>', $end);

        $this->db->simple_query('SET SESSION group_concat_max_len=1500000000000000');
        $this->db->select('
            tbl_orders.id,
            tbl_orders.reference_no,
            tbl_order_item_shippings.date_shipping,
            GROUP_CONCAT(CONCAT(tbl_order_items.type_item, "___", tbl_order_items.item_id, "___", tbl_order_item_shippings.quantity_shipping) SEPARATOR "|||") as items
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
        $this->db->join('tbl_order_item_shippings', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
        $this->db->where('tbl_order_item_shippings.date_shipping >=', $start);
        $this->db->where('tbl_order_item_shippings.date_shipping <', $end);
        $this->db->group_by('tbl_orders.id');
        $orders = $this->db->get()->result_array();
        if (!empty($orders)) {
            foreach ($orders as $key => $value) {
                $orderId = $value['id'];
                $items = $value['items'];
                $dtItems = explode('|||', $items);
                $strTooltip = '<table class=\'\' style=\'width: 300px;\'>';
                foreach ($dtItems as $k => $val) {
                    $item = explode("___", $val);
                    // print_arrays($item);
                    if (!empty($item)) {
                        $type_item = $item[0];
                        $items_id = $item[1];
                        $quantity_shipping = $item[2];
                        $images = '';

                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($items_id);
                            // $unit = $this->unit_model->rowUnit($info['unit_id']);
                            if (!empty($info['images'])) {
                                $images = base_url('uploads/products/' . $info['images']);
                            }
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($items_id);
                            // $unit = $this->unit_model->rowUnit($info['unit']);
                            if (!empty($info['avatar'])) {
                                $images = base_url($info['avatar']);
                            }
                        }

                        $tdImage = '<td></td>';
                        $tdName = '<td>' . $info['code'] . ' (' . $info['name'] . ')</td>';
                        $tdQuantity = '<td class=\'text-center\'>' . formatNumber($quantity_shipping) . '</td>';

                        $strTooltip .= '<tr>
                            ' . $tdName . '
                            ' . $tdQuantity . '
                        </tr>';
                    }
                }
                $strTooltip .= '</table>';



                $title = '<a data-tnh="modal" style="color: white;" class="tnh-modal" href="' . base_url('admin/orders/view_order/' . $value['id']) . '" data-toggle="modal" data-target="#myModal"><div data-html="true" data-toggle="tooltip" data-title="' . $strTooltip . '">' . $value['reference_no'] . '</div></a>
                ';
                $eventOrders['_tooltip'] = $strTooltip;
                $eventOrders['title'] = $title;
                $eventOrders['start'] = $value['date_shipping'];
                $eventOrders['end'] = $value['date_shipping'];
                $eventOrders['public'] = 1;
                $eventOrders['onclick'] = false;

                // $eventOrders['url'] = site_url('admin/orders/view_order/' . $value['id']);
                array_push($data, $eventOrders);
            }
        }

        return hooks()->apply_filters('calendar_data', $data, [
            'start'      => $start,
            'end'        => $end,
            'client_id'  => $client_id,
            'contact_id' => $contact_id,
        ]);
    }

    public function getCalendarOrdersDeliveryData()
    {
        echo json_encode($this->getCalendarOrdersDelivery(
            $this->input->post('startDate'),
            $this->input->post('endDate'),
            '',
            '',
            $this->input->post()
        ));
        die();
    }

    public function delivery_schedules()
    {
        $data['title'] = lang('tnh_delivery_schedules');
        add_calendar_assets();
        $this->load->view('admin/orders/delivery_schedules', $data);
    }

    public function calOrders()
    {
        $data = [];
        $order_id = $this->input->post('order_id');
        $customer_id = $this->input->post('customer_id');
        $start_date = $this->input->post('start_date') ? to_sql_date($this->input->post('start_date')) : NULL;
        $end_date = $this->input->post('end_date') ? to_sql_date($this->input->post('end_date')) : NULL;
        $customer_id = $this->input->post('customer_id');
        $type_orders_search = $this->input->post('type_orders_search');
        $status_orders_search = $this->input->post('status_orders_search');


        if (empty($start_date) && empty($end_date) && empty($order_id) && empty($customer_id)) {
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
        }

        $this->db->select("
            COUNT(*) as count_orders,
            SUM(tbl_orders.grand_total * tbl_orders.amount_to_vnd) as sum_grand_total,
            0 as sum_total_shopee,
        ", false);
        $this->db->from('tbl_orders');

        if (!empty($order_id)) {
            $this->db->where('tbl_orders.id', $order_id);
        }

        if (!empty($customer_id)) {
            $this->db->where('tbl_orders.customer_id', $customer_id);
        }
        if (!empty($start_date)) {
            $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE_FORMAT(tbl_orders.date, "%Y-%m-%d") <=', $end_date);
        }

        if (!empty($type_orders_search)) {
            $this->db->where('tbl_orders.type_orders =', $type_orders_search);
        }

        if (!empty($status_orders_search)) {
            $this->db->where('tbl_orders.status_orders =', $status_orders_search);
        }

        $result = $this->db->get()->row_array();

        $data['count_orders'] = $result['count_orders'];
        $data['sum_grand_total'] = $result['sum_grand_total'];
        $data['sum_total_shopee'] = $result['sum_total_shopee'];

        echo json_encode($data);
    }


    public function print_qrcode_orders($id)
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        ob_end_clean();

        $this->db->where('order_id', $id);
        $items = $this->db->get('tbl_order_items')->result_array();
        $data['data'] = [];
        foreach ($items as $key => $value) {
            $type_item = $value['type_item'];
            $items_id = $value['item_id'];
            $info = null;
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($items_id);
                //                $unit = $this->unit_model->rowUnit($info['unit_id']);
            } else if ($type_item == "items") {
                $info = $this->items_model->rowItems($items_id);
                //                $unit = $this->unit_model->rowUnit($info['unit']);
            } else if ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($items_id);
                //                $unit = $this->unit_model->rowUnit($info['unit_id']);
            }
            $data['data'][$key]['code'] = $info['code'];
            $data['data'][$key]['name'] = $info['name'];
        }
        ob_start();
        $data['title'] = lang('In QR');
        // $data['type'] = 'P';
        $data['type'] = 'L';
        $data['img'] = '';
        $content = ob_get_contents();
        $data['hide'] = 'hide';
        $data['content'] = $content;
        ob_end_clean();
        ob_clean();
        $pdf = print_pdf_orders_qcode($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }





    public function print_qr_code_html($id)
    {

        $id_detail = $this->input->get('id_detail');
        $quantity = $this->input->get('quantity');

        $this->db->select('tbl_orders.*');
        $this->db->where('tbl_order_items.id', $id_detail[0]);
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
        $orders = $this->db->get('tbl_orders')->row();

        $this->db->where('tbl_order_items.order_id', $orders->id);
        $this->db->order_by('id', 'desc');
        $order_items = $this->db->get('tbl_order_items')->result_array();
        $arrayOrder_items = [];
        foreach ($order_items as $key => $value) {
            $arrayOrder_items[$value['id']] = ($key + 1);
        }

        $this->db->where_in('tbl_order_items.id', $id_detail);
        $this->db->select('reference_no, tbl_order_items.id, type_item, item_id, referenceId_api, DATE_FORMAT(tbl_orders.date, "%m%d_%Hh") as date_lo');
        $this->db->where('order_id', $id);
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id', 'left');
        $items = $this->db->get('tbl_order_items')->result_array();

        $data['data'] = [];
        foreach ($items as $key => $value) {
            $type_item = $value['type_item'];
            $items_id = $value['item_id'];
            $info = null;
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($items_id);
            } else if ($type_item == "items") {
                $info = $this->items_model->rowItems($items_id);
            } else if ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($items_id);
            }

            $data['data'][$key]['item_id'] = $value['item_id'];;
            $data['data'][$key]['code_items'] = $info['code'];
            $data['data'][$key]['name_items'] = $info['name'];
            $data['data'][$key]['code'] = $value['reference_no'];
            $data['data'][$key]['id'] = $value['id'];
            $data['data'][$key]['date_lo'] = $value['date_lo'];
            $data['data'][$key]['referenceId_api'] = $value['referenceId_api'];
            $data['data'][$key]['reference_no'] = $value['reference_no'];
            $data['data'][$key]['size'] = $info['size'];



            $data['data'][$key]['category_code'] = $info['category_code'];
            $data['data'][$key]['stt'] = $arrayOrder_items[$value['id']];
            if (!empty($info['size'])) {
                $data['data'][$key]['name_size'] = $this->db->get_where('tblsize', ['id' => $info['size']])->row('name');
            }

            $up = $this->orders_model->updateOrderItemNew($value['id'], [
                'active' => 1,
                'staff_active' => get_staff_user_id(),
                'date_active' => date('Y-m-d H:i:s'),
            ]);

            if ($up) {
                $this->orders_model->handlingStageProductionsWhenPrintBarcode($value['id']);
            }
            // $this->db->where('stage_id', 1);
            // $this->db->where('order_item_id', $value['id']);
            // $this->db->update('tbl_order_items_stages', [
            //     'active' => 1,
            //     'staff_active' => get_staff_user_id(),
            //     'date_active' => date('Y-m-d H:i:s'),
            // ]);
        }
        $data['quantity'] = $quantity;
        $this->load->view('admin/orders/printBarcode', $data, false);
    }


    public function get_modal_orders($id = "")
    {
        $this->db->select('tbl_orders.reference_no, tblclients.company, tbl_order_items.id, tbl_order_items.type_item, tbl_order_items.item_id,tbl_order_items.quantity');
        $this->db->where('tbl_orders.id', $id);
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        $data['items'] = $this->db->get('tbl_order_items')->result_array();
        foreach ($data['items'] as $key => $value) {
            $type_item = $value['type_item'];
            $items_id = $value['item_id'];
            $info = null;
            if ($type_item == "products") {
                $info = $this->products_model->rowProduct($items_id);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/products/' . $info['images']);
                }
            } else if ($type_item == "items") {
                $info = $this->items_model->rowItems($items_id);
                if (!empty($info['avatar'])) {
                    $images = base_url($info['avatar']);
                }
            } else if ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($items_id);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/materials/' . $info['images']);
                }
            }
            if (empty($images)) {
                $images = base_url('assets/images/tnh/no_image.png');
            }
            $data['items'][$key]['code'] = $info['code'];
            $data['items'][$key]['name'] = $info['name'];
            $data['items'][$key]['images'] = $images;
        }
        $data['title'] = _l('c_print_basecode');
        $data['id'] = $id;
        $this->load->view('admin/orders/modal_print_qr', $data);
    }

    public function print_tem($id)
    {
        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $items = $this->orders_model->getOrderItemsByOrderId($id);

        $data['order'] = $order;
        $data['items'] = $items;
        $data['id'] = $id;
        $this->load->view('admin/orders/print_tem', $data);
    }

    public function get_print_tem()
    {
        if (!$this->perPrintOrders) {
            accessDenied();
            die;
        }
        ob_end_clean();
        $order_id = $this->input->post('order_id');
        $p_id = $this->input->post('p_id');
        $type_print = $this->input->post('type_print');
        $vt1 = $this->input->post('vt1');
        $vt2 = $this->input->post('vt2');
        $vt3 = $this->input->post('vt3');
        $vt4 = $this->input->post('vt4');

        $order = $this->orders_model->rowOrderById($order_id);
        $customer = $this->clients_model->rowCustomer($order['customer_id']);
        $tableTem = '';
        if (!empty($p_id)) {
            $vt1_ar = array();
            $vt1_id = array();
            if(!empty($vt1)){
                $vt1_ar = explode('_____',$vt1);
                foreach ($vt1_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt1_id[$vas[0]] = $vas[1];
                }
            }
            $vt2_ar = array();
            $vt2_id = array();
            if(!empty($vt2)){
                $vt2_ar = explode('_____',$vt2);
                foreach ($vt2_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt2_id[$vas[0]] = $vas[1];
                }
            }

            $vt3_ar = array();
            $vt3_id = array();
            if(!empty($vt3)){
                $vt3_ar = explode('_____',$vt3);
                foreach ($vt3_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt3_id[$vas[0]] = $vas[1];
                }
            }
            $vt4_ar = array();
            $vt4_id = array();
            if(!empty($vt4)){
                $vt4_ar = explode('_____',$vt4);
                foreach ($vt4_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt4_id[$vas[0]] = $vas[1];
                }
            }
            $p_id = explode(',', $p_id);
            $this->db->select('tbl_orders.*, tblclients.company as company,tblclients.company_short as company_short, tblclients.zcode as code_customer,tblclients.is_separate_guest as is_separate_guest', false);
            $this->db->from('tbl_orders');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
            $this->db->where('tbl_orders.id', $order_id);
            $order = $this->db->get()->row_array();


            $this->db->select('
                tbl_order_items.*
            ', false);
            $this->db->from('tbl_order_items');
            $this->db->where('tbl_order_items.order_id', $order_id);
            $this->db->where_in('tbl_order_items.id', $p_id);
            $order_items = $this->db->get()->result_array();
            if (!empty($order_items)) {
                foreach ($order_items as $key => $value) {
                    $order_item_id = $value['id'];
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];

                    $product_name_customer =  '';
                    $mode = '';
                    $quantity_child_sheet = 0;
                    $lost = '';

                    $even_quantity = '';
                    $odd_quantity = '';

                    $even_quantity_bale = '';
                    $odd_quantity_bale = '';

                    $item_code = '';
                    $color_size = '';
                    $gw = '';
                    $carton_size = '';

                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($value['product_name_customer'])) {
                            $product_name_customer = $value['product_name_customer'];
                        } else if (!empty($info['product_name_customer'])) {
                            $product_name_customer = $info['product_name_customer'];
                        } else {
                            $product_name_customer = $info['name'] . ' ' . $info['code'];
                        }

                        $mode = $info['mode_product'];
                        $lost = $info['loss'];
                        $quantity_child_sheet = $info['quantity_child_sheet'];
                        $quantity_sheet_bale = $info['quantity_sheet_bale'];
                        if(empty($quantity_child_sheet)){
                            $quantity_child_sheet = 1;
                        }
                        if(empty($quantity_sheet_bale)){
                            $quantity_sheet_bale = 1;
                        }
                        if ($quantity_child_sheet > 0) {
                            $quantity_sheet = $value['quantity'] / $quantity_child_sheet;
                            $even_quantity = floor($quantity_sheet);
                            $quantity_ceil = ceil($quantity_sheet);
                            $odd_quantity = $quantity_ceil - $even_quantity;
                        }

                        $quantity_bale = 0;
                        $quantity_ceil_bale_chan = 0;
                        if ($quantity_sheet_bale > 0) {
                            $quantity_bale = $value['quantity'] / $quantity_sheet_bale;
                            $even_quantity_bale = floor($quantity_bale);
                            $quantity_ceil_bale = ceil($quantity_bale);
                            $quantity_ceil_bale_chan = floor($quantity_bale);
                            $odd_quantity_bale = $quantity_ceil_bale - $even_quantity_bale;
                        }

                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }

                        $item_code = $info['code'];
                        $color_size = $info['color_size'];
                        $gw = $info['gw'];
                        $carton_size = $info['carton_size'];
                    } else if ($type_item == "materials") {
                        $info = $this->items_model->rowMaterial($items_id);

                        if (!empty($value['product_name_customer'])) {
                            $product_name_customer = $value['product_name_customer'];
                        } else if (!empty($info['name_customer'])) {
                            $product_name_customer = $info['name_customer'];
                        } else {
                            $product_name_customer = $info['name'] . ' ' . $info['code'];
                        }

                        $mode = $info['mode'];

                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/' . $info['images']);
                        }

                        $item_code = $info['code'];
                    }
                    $font = 'dejavuserifcondensed';
                    // if(preg_match("/\p{Han}+/u", $product_name_customer)){
                    //     $font = 'kozgopromedium';
                    // }
                    // if (preg_match("/[\p{Hiragana}\p{Katakana}\p{Han}（）]+/u", $product_name_customer)) {
                    //     $font = 'kozgopromedium';
                    //     // $font = 'droidsansfallback';
                    // }

                    if (preg_match('/[\x{4e00}-\x{9fff}]/u', $product_name_customer)) {
                        // Có ký tự Trung Quốc, ưu tiên font droidsansfallback
                        $font = 'droidsansfallback';
                    } elseif (preg_match('/[\p{Hiragana}\p{Katakana}（）]/u', $product_name_customer)) {
                        // Chỉ có ký tự tiếng Nhật, dùng kozgopromedium
                        $font = 'kozgopromedium';
                    }
                    // if ($order['type_orders'] == ORDER_DEFAULT || $order['type_orders'] == ORDER_CHANGE) {

                    if ($order['type_orders'] != ORDER_CHANGE && $order['type_orders'] != ORDER_CHANGE_SIZE) {
                        if ($type_print == 1) {
                            if (!empty($quantity_ceil_bale)) {
                                for ($i = 0; $i < $quantity_ceil_bale; $i++) {
                                    $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Mã KH</td>
                                            <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                            <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                            <td class="" style="width: 15%;">Qui Cách</td>
                                            <td class="text-center" style="width: 10%;">ĐVT</td>
                                            <td class="" style="width: 15%;">Ghi Chú</td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Mã ĐĐH</td>
                                            <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                            <td class="text-center" rowspan="2" colspan="3" style="width: 35%; line-height: 45px;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                            <td class="" style="width: 15%;">' . $mode . '</td>
                                            <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                            <td class="" style="width: 15%;">' . $value['note_item'] . '</td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Số lượng giao</td>
                                            <td class="text-right" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>
                                            <td class="" style="width: 15%;">SL Con/Tờ</td>
                                            <td class="text-center" style="width: 10%;">' . formatNumber($quantity_child_sheet) . '</td>
                                            <td class="" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">Tờ Chẵn</td>
                                            <td class="text-center" style="width: 15%;">Tờ Lẻ</td>
                                            <td class="text-center" style="width: 15%;">Kiện Chẵn</td>
                                            <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;">Lost</td>
                                            <td class="text-center" style="width: 10%;">' . $lost . '</td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">' . $even_quantity . '</td>
                                            <td class="text-center" style="width: 15%;">' . $odd_quantity . '</td>
                                            <td class="text-center" style="width: 15%;">' . $quantity_sheet_bale . '</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">QC Kiểm</td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;">Ngày giao</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                    </table><div style="line-height: 0.0000000001em;"></div>';
                                }
                            }


                            if (!empty($odd_quantity_bale)) {
                                $quantity_odd = $value['quantity'] - $quantity_sheet_bale * $even_quantity_bale;
                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Mã KH</td>
                                        <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                        <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                        <td class="" style="width: 15%;">Qui Cách</td>
                                        <td class="text-center" style="width: 10%;">ĐVT</td>
                                        <td class="" style="width: 15%;">Ghi Chú</td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Mã ĐĐH</td>
                                        <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                        <td class="text-center" rowspan="2" colspan="3" style="width: 35%; line-height: 45px;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                        <td class="" style="width: 15%;">' . $mode . '</td>
                                        <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                        <td class="" style="width: 15%;">' . $value['note_item'] . '</td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Số lượng giao</td>
                                        <td class="text-right" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>
                                        <td class="" style="width: 15%;">SL Con/Tờ</td>
                                        <td class="text-center" style="width: 10%;">' . formatNumber($quantity_child_sheet) . '</td>
                                        <td class="" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">Tờ Chẵn</td>
                                        <td class="text-center" style="width: 15%;">Tờ Lẻ</td>
                                        <td class="text-center" style="width: 15%;">Kiện Chẵn</td>
                                        <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;">Lost</td>
                                        <td class="text-center" style="width: 10%;">' . $lost . '</td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">' . $even_quantity . '</td>
                                        <td class="text-center" style="width: 15%;">' . $odd_quantity . '</td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;">' . $quantity_odd . '</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">QC Kiểm</td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;">Ngày giao</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                </table><div style="line-height: 0.0000000001em;"></div>';
                            }
                        } elseif ($type_print == 3) {
                            if ($quantity_child_sheet > 0) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    if ($ct_counter_item > 0) {
                                        $check_key = 0;
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $quantity_colum = floor($quantity_put / $quantity_child_sheet);
                                            $quantity_odd = $quantity_put - $quantity_child_sheet * $quantity_colum;
                                            if ($check_key == 0) {
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0"><tr nobr="true" style="">';
                                            }

                                            $tableTem .= '<td class="" style="width: 50%;"><table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr nobr="true" style="">
                                                <td class=""  colspan="2">K/H: ' . $order['company_short'] . '</td>
                                                <td colspan="4" >M/Đ: ' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="6" style="font-family: '.$font.';font-size:11px" >T/T: ' . $product_name_customer . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3"  >CL: ' . $command . '</td>
                                                <td class=""  >SL: </td>
                                                <td class="text-right" >' . formatNumber($quantity_put) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3"  >QC</td>
                                                <td class="" style="border-right: none;border-bottom: none;">Tờ: </td>
                                                <td class="text-right" style="border-left: none;border-right: none;border-bottom: none;">' . $quantity_colum . '</td>
                                                <td class="" style="border-left: none;border-bottom: none;"></td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3">N/Giao: ' . $dateDelivery . '</td>
                                                <td class="" style="border-right: none;border-top: none;border-right: none;">Lẻ: </td>
                                                <td class="text-right" style="border-left: none;border-right: none;border-top: none;">' . formatNumber($quantity_odd) . '</td>
                                                <td class="" style="border-left: none;border-top: none;"></td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class="text-center" colspan="3">' . $order['reference_no'] . '</td>
                                                <td class="" >SL Thêm: </td>
                                                <td class="text-right" >' . formatNumber($quantity_loss) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                            </tr>
                                        </table></td>';
                                            if ($check_key == 1) {
                                                $tableTem .= '</tr></table><div style="line-height: 0.0000000001em;"></div>';
                                                $check_key = 0;
                                            } else {
                                                if (($i + 1) == ($ct_counter_item)) {
                                                    $tableTem .= '</tr></table><div style="line-height: 0.0000000001em;"></div>';
                                                }
                                                $check_key++;
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($type_print == 4) {
                            if ($quantity_sheet_bale > 0) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    if ($ct_counter_item > 0) {
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            // $quantity_sheet_bale = 2;
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;

                                            if(!isset($value_size[$order_code.$command])){

                                                $value_size[$order_code.$command]['order_code'] = $order_code;
                                                $value_size[$order_code.$command]['command'] = $command;
                                                $value_size[$order_code.$command]['quantity_put_sum'] = $quantity_put;
                                                $value_size[$order_code.$command]['quantity_put'] = $quantity_put;
                                                $value_size[$order_code.$command]['quantity_loss'] = $quantity_loss;


                                            }else{
                                                $value_size[$order_code.$command]['quantity_put_sum'] += $quantity_put;
                                                $value_size[$order_code.$command]['quantity_put'] += $quantity_put;
                                                $value_size[$order_code.$command]['quantity_loss'] += $quantity_loss;
                                            }
                                            // $__data['order_code'] = $order_code;
                                            // $__data['command'] = $command;
                                            // $__data['quantity_put_sum'] = $quantity_put;
                                            // $__data['quantity_put'] = $quantity_put;
                                            // $__data['quantity_loss'] = $quantity_loss;
                                            // $value_size[$order_code][] = $__data;
                                        }

                                        foreach ($value_size as $kk => $vv) {
                                            $order_code = $vv['order_code'];
                                            $command = $vv['command'];
                                            $quantity_put = $vv['quantity_put'];
                                            $quantity_loss = $vv['quantity_loss'];

                                            $quantity_colum = floor($quantity_put / $quantity_sheet_bale);
                                            $quantity_odd = $quantity_put - $quantity_sheet_bale * $quantity_colum;
                                            $loss = $quantity_odd + $quantity_put * ($lost / 100);
                                            $quantity_colum_show = $quantity_sheet_bale;
                                            $quantity_colum_loss_show = $quantity_sheet_bale;
                                            if($quantity_sheet_bale == 1){
                                                $quantity_colum = 1;
                                                $quantity_colum_show = $quantity_put;
                                                $quantity_colum_loss_show = $quantity_put+$quantity_loss;
                                            }
                                            $limits = $quantity_colum;
                                            $page = 0;
                                            if($quantity_odd > 0){
                                                $limits++;
                                            }
                                            for ($j = 0; $j < $quantity_colum; $j++) {
                                                if($quantity_odd == 0){
                                                    if(($quantity_colum - 1) == $j){
                                                        if($quantity_sheet_bale != 1){
                                                            $quantity_colum_loss_show = $quantity_colum_loss_show+$quantity_loss;
                                                        }
                                                    }
                                                }
                                                $page++;
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr  nobr="true" style="">
                                                <td style="width: 18%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 40%;font-family: '.$font.';font-size:11px" class="text-left;white-space: unset;" colspan="4">' . $product_name_customer . '</td>
                                                <td style="width: 9%;">PO#</td>
                                                <td style="width: 25%;" colspan="3">' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 10%;" class=""  >SL: </td>
                                                <td style="width: 8%;" class="text-right" >' . formatNumber($quantity_colum_show) . '</td>
                                                <td style="width: 8%;" class="" >' . $unit['unit'] . '</td>
                                                <td style="width: 8%;" class="">1 kiện: </td>
                                                <td style="width: 10%;" class="">' . formatNumber($quantity_sheet_bale) . '</td>
                                                <td style="width: 10%;" class="">Số kiện: </td>
                                                <td style="width: 12%;" >'.$page.'/'.$limits.'</td>
                                                <td style="width: 9%;" class="" >CL: </td>
                                                <td style="width: 25%;" colspan="3" >' . $command . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class=""  >SL+Loss: </td>
                                                <td class="text-right" >' . formatNumber($quantity_colum_loss_show) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                                <td class=""  colspan="2">' . $order['reference_no'] . '</td>
                                                <td class=""  >Lẻ</td>
                                                <td ></td>
                                                <td class="" style="width: 9%;" >N/GIAO:</td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                                <td style="width: 5%;" class="" >QC: </td>
                                                <td style="width: 8%;"></td>
                                            </tr>
                                        </table><div style="line-height: 0.0000000001em;"></div>';
                                            }
                                            if ($quantity_odd > 0) {
                                                $page++;
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr  nobr="true" style="">
                                                <td style="width: 18%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 40%;font-family: '.$font.';font-size:11px" class="text-left;white-space: unset;" colspan="4">' . $product_name_customer . '</td>
                                                <td style="width: 9%;">PO#</td>
                                                <td style="width: 25%;" colspan="3">' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 10%;" class=""  >SL: </td>
                                                <td style="width: 8%;" class="text-right" >' . formatNumber($quantity_odd) . '</td>
                                                <td style="width: 8%;" class="" >' . $unit['unit'] . '</td>
                                                <td style="width: 8%;" class="">1 kiện: </td>
                                                <td style="width: 10%;" class="">' . formatNumber($quantity_sheet_bale) . '</td>
                                                <td style="width: 10%;" class="">Số kiện: </td>
                                                <td style="width: 12%;" >'.$page.'/'.$limits.'</td>
                                                <td style="width: 9%;" class="" >CL: </td>
                                                <td style="width: 25%;" colspan="3" >' . $command . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class=""  >SL+Loss: </td>
                                                <td class="text-right" >' . number_format($loss) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                                <td class=""  colspan="2">' . $order['reference_no'] . '</td>
                                                <td class=""  >Lẻ</td>
                                                <td ></td>
                                                <td class="" style="width: 9%;" >N/GIAO:</td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                                <td style="width: 5%;" class="" >QC: </td>
                                                <td style="width: 8%;"></td>
                                            </tr>
                                        </table><div style="line-height: 0.0000000001em;"></div>';
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($type_print == 5) {

                            if ($quantity_child_sheet > 0 && !empty($vt2_id)) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    $value_size = array();
                                    if ($ct_counter_item > 0) {
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            $Size = '';
                                            $vt1_text_show = '';
                                            $vt3_text_show = '';
                                            $vt4_text_show = '';

                                            $text_size = '';
                                            $tv1_text = '';
                                            $tv3_text = '';
                                            $tv4_text = '';
                                            $text_size = '';
                                            if(!empty($vt1_id[$value['id']])){
                                                $tv1_text = $vt1_id[$value['id']];
                                            }
                                            if(!empty($vt3_id[$value['id']])){
                                                $tv3_text = $vt3_id[$value['id']];
                                            }
                                            if(!empty($vt4_id[$value['id']])){
                                                $tv4_text = $vt4_id[$value['id']];
                                            }

                                            if(!empty($vt2_id[$value['id']])){
                                                $text_size = $vt2_id[$value['id']];
                                            }else{
                                                continue;
                                            }

                                            if ($value['type_item'] == 'products') {
                                                $this->db->where('id', $value['item_id']);
                                                $this->db->update('tbl_products', [
                                                    'colum_vt1' => $tv1_text,
                                                    'colum_vt2' => $text_size,
                                                    'colum_vt3' => $tv3_text,
                                                    'colum_vt4' => $tv4_text,
                                                ]);
                                            }

                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == $text_size && $i == $vO['counter_items_number']) {
                                                    $Size = $vO['columns_name'];
                                                    continue;
                                                }
                                                else if ($vO['columns_value'] == $tv1_text && $i == $vO['counter_items_number']) {
                                                    $vt1_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                                else if ($vO['columns_value'] == $tv3_text && $i == $vO['counter_items_number']) {
                                                    $vt3_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                                else if ($vO['columns_value'] == $tv4_text && $i == $vO['counter_items_number']) {
                                                    $vt4_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $__data['order_code'] = $order_code;
                                            $__data['command'] = $command;
                                            $__data['quantity_put_sum'] = $quantity_put;
                                            $__data['quantity_put'] = $quantity_put;
                                            $__data['quantity_loss'] = $quantity_loss;
                                            $__data['Size'] = $Size;
                                            $__data['vt1_text_show'] = $vt1_text_show;
                                            $__data['vt3_text_show'] = $vt3_text_show;
                                            $__data['vt4_text_show'] = $vt4_text_show;
                                            $value_size[$order_code . $command][] = $__data;
                                        }

                                        foreach ($value_size as $h => $hv) {
                                            $total_quanliti = 0;
                                            foreach ($hv as $ks => $vs) {
                                                $total_quanliti+=$vs['quantity_put_sum'];
                                            }
                                            // $quantity_colum = floor($quantity_put / $quantity_sheet_bale);
                                            // $quantity_odd = $quantity_put - $quantity_sheet_bale * $quantity_colum;
                                            // $loss = $quantity_odd + $quantity_put * ($lost / 100);

                                            $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1" style="font-size:8px">
                                            <tr nobr="true" style="">
                                                <td style="width: 6%;">K/H:</td>
                                                <td style="width: 12%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 36%;font-family: '.$font.';font-size:11px" colspan="6">' . $product_name_customer . '</td>
                                                <td style="width: 4%;">SL</td>
                                                <td style="width: 16%;" class="text-right" colspan="2">' . formatNumber($total_quanliti)  . '</td>
                                                <td style="width: 8%;">' . $unit['unit'] . '</td>
                                                <td style="width: 12%;" colspan="2">QC</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 6%;">MĐ: </td>
                                                <td style="width: 12%;"> ' . $hv[0]['order_code'] . '</td>
                                                <td style="width: 8%;" class="">C/L</td>
                                                <td style="width: 12%;" colspan="2">' . $hv[0]['command'] . '</td>
                                                <td style="width: 12%;font-family: '.$font.';font-size:11px" colspan="2" class="">' . $hv[0]['vt3_text_show'] . '</td>
                                                <td style="width: 12%;font-family: '.$font.';font-size:11px" colspan="2" class="">' . $hv[0]['vt4_text_show'] . '</td>
                                                <td style="width: 20%;" >' . $order['reference_no'] . '</td>
                                                <td style="width: 8%;" class="" >N/GIAO </td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                            </tr>';
                                            $tableTem .= '<tr nobr="true" style="">
                                                <td style="width: 6%;"></td>';
                                                $font_size = '8px';

                                                if(count($hv) > 32){
                                                    $width_size = '2%';
                                                    $font_size = '6px';
                                                }elseif(count($hv) > 24){
                                                    $width_size = '3%';
                                                }elseif(count($hv) > 16){
                                                    $width_size = '4%';
                                                }else{
                                                    $width_size = '6%';
                                                }
                                                $width_size_main = '6%';
                                                foreach ($hv as $ks => $vs) {
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . $vs['vt1_text_show'] . ' </td>';
                                                }
                                                $tableTem .= '</tr><tr nobr="true" style="">
                                                    <td style="width: 6%;">Size: </td>';
                                                $width_size_main = '6%';
                                                foreach ($hv as $ks => $vs) {
                                                    // $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . $vs['Size'] . ' </td>';
                                                    $fontS = 'dejavuserifcondensed';
                                                    if (preg_match("/[\p{Hiragana}\p{Katakana}\p{Han}（）]+/u", $vs['Size'])) {
                                                        $fontS = 'kozgopromedium';
                                                    }
                                                    $tableTem .= '<td style="width: ' . $width_size . '; font-family: '.$fontS.';font-size:'.$font_size.'" class="text-center">' . $vs['Size'] . ' </td>';
                                                }
                                                $tableTem .= '</tr>
                                                <tr nobr="true" style="">
                                                    <td style="width: ' . $width_size_main . ';">SL: </td>';
                                                foreach ($hv as $ks => $vs) {
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'"  class="text-center">' . $vs['quantity_put'] . ' </td>';
                                                }
                                                $tableTem .= '</tr>
                                                <tr nobr="true" style="">
                                                    <td style="width: ' . $width_size_main . ';">SX: </td>';
                                                foreach ($hv as $ks => $vs) {
                                                    $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 0 ? ($vs['quantity_put'] * $lost/100)  : 0));
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . number_format($clost) . ' </td>';
                                                }
                                                $tableTem .= '</tr>
                                                <tr nobr="true" style="">
                                                    <td style="width: ' . $width_size_main . ';">Tờ: </td>';
                                                foreach ($hv as $ks => $vs) {
                                                    $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 0 ? ($vs['quantity_put'] * $lost/100)  : 0));
                                                    $quantity_colum = floor($clost / $quantity_child_sheet);
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . number_format($quantity_colum) . ' </td>';
                                                }
                                            $tableTem .= '</tr>
                                            <tr nobr="true" style="">
                                                <td style="width: ' . $width_size_main . ';">Lẻ: </td>';
                                            foreach ($hv as $ks => $vs) {
                                                $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 0 ? ($vs['quantity_put'] * $lost/100)  : 0));
                                                $quantity_colum = floor($clost / $quantity_child_sheet);
                                                $quantity_odd = $clost - $quantity_child_sheet * $quantity_colum;
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:8px" class="text-center">' . number_format($quantity_odd) . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                        </table><div style="line-height: 0.0000000001em;"></div>';
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->load->library('ciqrcode');
                            if (!empty($quantity_ceil_bale_chan)) {

                            	$quantity_odd = $value['quantity'] - $quantity_sheet_bale * $quantity_ceil_bale_chan;
                                if($quantity_odd > 0){
                                	$quantity_ceil_bale_chan += 1;
                                }
                                if($quantity_odd == 0){
                                    $quantity_odd = $value['quantity'] - $quantity_sheet_bale * ($quantity_ceil_bale_chan - 1);
                                }
                                $quantity_double = ceil($quantity_ceil_bale_chan / 2);
                                // print_arrays($quantity_ceil_bale);

                                $sttQ = 0;
                                $isBreak = false;
                                for ($i = 0; $i < $quantity_double; $i++) {
                                    if ($i == 100) break;
                                    $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0"><tr nobr="true" style="">';
                                    for ($j = 0; $j < 2; $j++) {
                                        $sttQ++;
                                        if ($quantity_ceil_bale_chan == $sttQ) {
                                        	$quantity_sheet_bale = $quantity_odd;
										}
                                        $qr = $order['so'] . '-' . $sttQ;

                                        $params['data'] = $qr;
                                        $params['level'] = 'H';
                                        $params['size'] = 20;
                                        $params['savename'] = FCPATH . 'uploads/orders/qrcode/' . $qr . '.png';
                                        $this->ciqrcode->generate($params);
                                        $img = file_get_contents(FCPATH . 'uploads/orders/qrcode/' . $qr . '.png');
                                        $is_separate_guest = '';
                                        if($order['is_separate_guest'] == 1){
                                            $is_separate_guest = '<tr><td  colspan="3">Made in Viet Nam</td></tr>';
                                        }
                                        $tableTem .= '<td class="" style="width: 50%;">
                                            <table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                                <tr>
                                                    <td colspan="3" class="text-center" style="width: 100%;">' . $customer['company'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold" style="width: 30%;">Vendor code:</td>
                                                    <td style="width: 40%;"></td>
                                                    <td rowspan="5" style="width: 30%;" class="text-center">
                                                        <img width="80" src="data:image/png;base64,' . base64_encode($img) . '"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">SO#</td>
                                                    <td>' . $order['so'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">PI#:</td>
                                                    <td>' . $order['pi'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">PO#/Style:</td>
                                                    <td class="text-left">' . $order['po_style'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Item code:</td>
                                                    <td class="text-left">' . $order['item_code'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Color/ Size:</td>
                                                    <td>' . $color_size . '</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Q\'ty:</td>
                                                    <td>' . formatNumber($quantity_sheet_bale) . '</td>
                                                    <td>pcs</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">G.W:</td>
                                                    <td>' . $gw . '</td>
                                                    <td>kg</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Carton:</td>
                                                    <td>' . ($sttQ . ' of ' . $quantity_ceil_bale_chan) . '</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Carton size:</td>
                                                    <td>' . $carton_size . '</td>
                                                    <td>cm</td>
                                                </tr>
                                                '.$is_separate_guest.'
                                                <tr>
                                                    <td colspan="3"><br><br><br><br></td>
                                                </tr>
                                            </table>
                                        </td>';

                                        if ($quantity_ceil_bale_chan == $sttQ) {
                                            $isBreak = true;
                                            break;
                                        }
                                    }
                                    $tableTem .= '</tr></table><div style="line-height: 0.0000000001em;"></div>';
                                    if ($isBreak) break;
                                }
                            }

                            // $tableTem.= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0">
                            //     <tr nobr="true" style="">
                            //         <td class="" style="width: 50%;">
                            //             <table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                            //                 <tr>
                            //                     <td colspan="3" class="text-center" style="width: 100%;">'.$customer['company'].'</td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Vendor code:</td>
                            //                     <td></td>
                            //                     <td rowspan="5" class="text-center">
                            //                         <img width="80" src="data:image/png;base64,' . base64_encode($img) . '"/>
                            //                     </td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">SO#</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">PI#:</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">PO#/Style:</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Item code:</td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Color/ Size:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Q\'ty:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">G.W:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Carton:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //                 <tr>
                            //                     <td class="bold">Carton size:</td>
                            //                     <td></td>
                            //                     <td></td>
                            //                 </tr>
                            //             </table>
                            //         </td>
                            //         <td class="" style="width: 50%;">
                            //         </td>
                            //     </tr>
                            // </table><br><br>';
                        }
                    } else if ($order['type_orders'] == ORDER_CHANGE) {

                        $arrSize = [1];
                        $trSize = '<td style="width: 10%;"></td>';
                        $trQuantityChild = '';
                        $trEvenQuantityChild = '';
                        $trOddQuantityChild = '';
                        $trEvenQuantityBaleChild = '';
                        $trOddQuantityBaleChild = '';

                        $this->db->select('
                            tbl_order_items_size.*,
                            tblsize.name as name_size,
                            tbl_colors.name as name_color,
                        ', false);
                        $this->db->from('tbl_order_items_size');
                        $this->db->join('tblsize', 'tblsize.id = tbl_order_items_size.size', 'left');
                        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_order_items_size.color', 'left');
                        $this->db->where('tbl_order_items_size.order_item_id', $order_item_id);
                        $this->db->order_by('tblsize.name ASC');
                        $order_items_size = $this->db->get()->result_array();
                        if (!empty($order_items_size)) {
                            foreach ($order_items_size as $kS => $vS) {
                                // if (!empty($vS['size']) && !in_array($vS['size'], $arrSize)) {
                                $arrSize[] = $vS['size'];
                                $trSize .= '<td class="text-center">' . $vS['name_size'] . '</td>';
                                // }

                                $quantity_child = $vS['quantity'];
                                $even_quantity_child = '';
                                $odd_quantity_child = '';

                                $even_quantity_bale_child = '';
                                $odd_quantity_bale_child = '';

                                if ($quantity_child_sheet > 0) {
                                    $quantity_sheet = $quantity_child / $quantity_child_sheet;
                                    $even_quantity_child = floor($quantity_sheet);
                                    $quantity_ceil = ceil($quantity_sheet);
                                    $odd_quantity_child = $quantity_ceil - $even_quantity_child;
                                }


                                if ($quantity_sheet_bale > 0) {
                                    $quantity_bale = $value['quantity'] / $quantity_sheet_bale;
                                    $even_quantity_bale_child = floor($quantity_bale);
                                    $quantity_ceil_bale = ceil($quantity_bale);
                                    $odd_quantity_bale_child = $quantity_ceil_bale - $even_quantity_bale_child;
                                }

                                $trQuantityChild .= '<td class="text-center">' . $quantity_child . '</td>';
                                $trEvenQuantityChild .= '<td class="text-center">' . $even_quantity_child . '</td>';
                                $trOddQuantityChild .= '<td class="text-center">' . $odd_quantity_child . '</td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center">' . $even_quantity_bale_child . '</td>';
                                $trOddQuantityBaleChild .= '<td class="text-center">' . $odd_quantity_bale_child . '</td>';
                            }
                        }

                        if (count($arrSize) < 10) {
                            $nS = 10 - count($arrSize);
                            for ($i = 0; $i < $nS; $i++) {
                                $trSize .= '<td class="text-center" style="width: 10%;"></td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                            }
                        }

                        $tableTem .= '<table nobr="true" class="" cellspacing="0" cellpadding="5" border="1">
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã KH</td>
                                <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                <td class="" style="width: 10%;">Qui Cách</td>
                                <td class="text-center" style="width: 10%;">ĐVT</td>
                                <td class="text-center" style="width: 10%;">Mã Đơn</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã ĐĐH</td>
                                <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                <td class="" style="width: 10%;">' . $mode . '</td>
                                <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                <td class="text-center" style="width: 10%;">Chỉ Lệnh</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="text-center" style="width: 10%;">Size SP</td>
                                <td class="text-center" style="width: 10%;">Size ĐC</td>
                                <td class="text-center" style="width: 10%;">Style Number</td>
                                <td class="text-center" style="width: 10%;">Color Name</td>
                                <td class="text-center" style="width: 10%;">Số Lượng</td>
                                <td class="text-center" style="width: 10%;">SL Tờ Chẵn</td>
                                <td class="text-center" style="width: 10%;">SL Tờ Lẻ</td>
                                <td class="text-center" style="width: 10%;">Kiện Chẵn</td>
                                <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                <td class="text-center" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" style="">
                                ' . $trSize . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Số Lượng</td>
                                ' . $trQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Tờ Chẵn</td>
                                ' . $trEvenQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Tờ Lẻ</td>
                                ' . $trOddQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Kiện Chặn</td>
                                ' . $trEvenQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Kiện Lẻ</td>
                                ' . $trOddQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td style="width: 10%;">QC Kiểm</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;">Ngày Giao</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>
                        </table><div style="line-height: 0.0000000001em;"></div>';
                    } else if ($order['type_orders'] == ORDER_CHANGE_SIZE) {

                        $this->db->select('tbl_order_items_change_size.*');
                        $this->db->from('tbl_order_items_change_size');
                        $this->db->where('tbl_order_items_change_size.order_item_id', $order_item_id);
                        $this->db->order_by('tbl_order_items_change_size.number_size ASC');
                        $order_items_change_size = $this->db->get()->result_array();

                        $trSizeChild = '';
                        $trQuantityChild = '';
                        $trEvenQuantityChild = '';
                        $trOddQuantityChild = '';
                        $trEvenQuantityBaleChild = '';
                        $trOddQuantityBaleChild = '';

                        $arrSize = [];
                        if (!empty($order_items_change_size)) {
                            foreach ($order_items_change_size as $kS => $vS) {
                                $arrSize[] = $vS['number_size'];

                                $quantity_child = $vS['quantity'];
                                $even_quantity_child = $vS['even_sheet'];
                                $odd_quantity_child = $vS['odd_sheet'];
                                $even_quantity_bale_child = $vS['even_bale'];
                                $odd_quantity_bale_child = $vS['odd_bale'];

                                $trSizeChild .= '<td class="text-center" style="width: 10%;">' . $vS['number_size'] . '</td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;">' . $quantity_child . '</td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;">' . $even_quantity_child . '</td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;">' . $odd_quantity_child . '</td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;">' . $even_quantity_bale_child . '</td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;">' . $odd_quantity_bale_child . '</td>';
                            }
                        }

                        if (count($arrSize) < 10) {
                            $nS = 10 - count($arrSize);
                            for ($i = 1; $i < $nS; $i++) {
                                $trSizeChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                            }
                        }

                        $tableTem .= '<table nobr="true" class="" cellspacing="0" cellpadding="5" border="1">
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã KH</td>
                                <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                <td class="" style="width: 10%;">Qui Cách</td>
                                <td class="text-center" style="width: 10%;">ĐVT</td>
                                <td class="text-center" style="width: 10%;">Mã Đơn</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã ĐĐH</td>
                                <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                <td class="" style="width: 10%;">' . $mode . '</td>
                                <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                <td class="text-center" style="width: 10%;">Chỉ Lệnh</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Size Đối Chiếu</td>
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Số Size</td>
                                ' . $trSizeChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Số Lượng</td>
                                ' . $trQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Tờ Chẵn</td>
                                ' . $trEvenQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Tờ Lẻ</td>
                                ' . $trOddQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Kiện Chẵn</td>
                                ' . $trEvenQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Kiện Lẻ</td>
                                ' . $trOddQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td style="width: 10%;">QC Kiểm</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;">Ngày Giao</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>
                        </table><div style="line-height: 0.0000000001em;"></div>';
                    }
                }
            }
        }

        $data = [];
        $data['title'] = lang('tnh_print_tem');
        if ($type_print == 1) {
            $data['type'] = 'L';
        }  else {
            $data['type'] = 'P';
        }

        $data['img'] = '';

        ob_start();
        stylePdf();
        echo $tableTem;
        $content = ob_get_contents();
        ob_end_clean();

        $data['showHeader'] = 'hide';
        $data['content'] = $content;
        $pdf = @print_pdf_tem($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function information()
    {
        $data['type_orders'] = $this->type_orders_model->getTypeOrders();
        $data['status_orders'] = $this->status_orders_model->getStatusOrders();
        $data['branch'] = $this->site_model->getBranch();

        $data['title'] = lang('order_information');
        $this->load->view('admin/orders/information', $data);
    }

    public function getOrdersInfomation()
    {
        $customer_search = $this->input->post('customer_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $type_orders_search = $this->input->post('type_orders_search');
        $status_orders_search = $this->input->post('status_orders_search');
        $items_search = $this->input->post('items_search');
        $branch_search = $this->input->post('branch_search');

        $tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
            FROM tbl_delivery_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery_item";

        $tbDateExpectedDelivery = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_order_item_shippings";

        $tbWarehousesProducts = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0
            GROUP BY tblwarehouse_items.id_items
        ) tb_warehouses_products";

        $aColumns = [
            'tbl_orders.id as id',
            'tbl_orders.type_items as type_items',
            'tbl_products.images as images',
			'tbl_products.code as item_code',
            '"" as status_orders',
            'tbl_orders.date as date',
            'tblclients.zcode as zcode',
            'tb_customer_group.group_name as brand',
            'tbl_orders.reference_no as reference_no',
            'tb_order_item_shippings.date_shipping as date_delivery',
            'tbl_order_items.product_name_customer as product_name_customer',
            '(tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_not_delivery',
            'tbl_order_items.quantity as quantity_orders',
            'tbl_order_items.quantity_delivery as quantity_delivery',
            '(tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_rest',
            // 'tb_warehouses_products.product_quantity as quantity_warehouse',
            '0 as quantity_warehouse',
            'tbl_order_items.price as price',
            // 'tb_delivery_item.date_delivery as quantity_detail',
            '"" as quantity_detail',
            'tbl_species.name as name_species',
            'tbl_type_print.name as name_type_print',
			'tbl_order_items.note_item as note_item',
			'tbl_type_orders.name as name_type_orders',
			// 'tb_production.code_production as code_production',
			'"" as code_production',
			'tbl_orders.created_by as staff_create_orders',
            'tblbranch.name as name_branch',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_orders';
        $where        = [
            ' AND tbl_order_items.type_item = "products"'
        ];
 
        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

		if (!empty($items_search)) {
			$items_search = explode('__', $items_search);
            array_push($where, "AND tbl_order_items.item_id = " . $items_search[0]);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_orders.date <= '" . $end_date_search . "'");
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
            //array_push($where, "AND tbl_orders.status_orders = " . $status_orders_search . "");
			
			$this->db->where('id', $status_orders_search);
			$data_status_orders = $this->db->get('tbl_status_orders')->row();
			if(!empty($data_status_orders)) {
				if(!empty($data_status_orders->day_start) && !empty($data_status_orders->day_end)) {
					$date_ship_start = date("Y-m-d", strtotime("+$data_status_orders->day_start day"));
					$date_ship_end = date("Y-m-d", strtotime("+$data_status_orders->day_end day"));
					$where[] = 'AND tb_order_item_shippings.date_shipping >= "' . $date_ship_start . '"';
					$where[] = 'AND tb_order_item_shippings.date_shipping <= "' . $date_ship_end . '"';
				}
			}
        }

        if (!empty($branch_search)) {
            array_push($where, "AND tbl_orders.id_branch = " . $branch_search . "");
        }

        $filter = [];

        $tbGroupCustomer = '(
            SELECT
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as group_name
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group';
		
		$tbProductions = '(
            SELECT
                tbl_productions_orders_items.production_plan_item_id,
                GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR "</br>") as code_production
            FROM tbl_productions_orders_items
            JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
            WHERE tbl_productions_orders_items.object_item_type = "orders"
            GROUP BY tbl_productions_orders_items.production_plan_item_id
        ) tb_production';

        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            'INNER JOIN tbl_order_items ON tbl_order_items.order_id = tbl_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_orders.id_branch',
            'LEFT JOIN tbl_status_orders ON tbl_status_orders.id = tbl_orders.status_orders',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tbl_type_print ON tbl_type_print.id = tbl_products.type_print',
            'LEFT JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            'LEFT JOIN ' . $tbDateExpectedDelivery . ' ON tb_order_item_shippings.order_item_id = tbl_order_items.id',
            // 'LEFT JOIN ' . $tbDelivery . ' ON tb_delivery_item.order_item_id = tbl_order_items.id',
            'LEFT JOIN ' . $tbGroupCustomer . ' ON tb_customer_group.customer_id = tblclients.userid',
            // 'LEFT JOIN ' . $tbWarehousesProducts . ' ON tb_warehouses_products.id_items = tbl_order_items.item_id',
            // 'LEFT JOIN ' . $tbProductions . ' ON tb_production.production_plan_item_id = tbl_order_items.id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_order_items.id as order_item_id',
            'tbl_order_items.item_id as item_id',
            'tbl_status_orders.name as name_status_orders',
            'tbl_status_orders.time as time',
            'tbl_orders.is_cancel as is_cancel',
        ], 'ORDER BY tbl_status_orders.id DESC', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $group = '';
        $quantity_not_delivery = 0;
        $quantity_orders = 0;
        $quantity_delivery = 0;
        $quantity_rest = 0;
        $quantity_warehouse = 0;

        if (!empty($rResult)) {
            $arrOrderItemId = [];
            $arrItemId = [];
            foreach ($rResult as $key => $value) {
                $arrOrderItemId[] = $value['order_item_id'];
                $arrItemId[] = $value['item_id'];
            }

            if (!empty($arrOrderItemId)) {
                $arrOrderItemId = array_unique($arrOrderItemId);
                $tbDelivery = "
                    SELECT
                        tbl_delivery_items.order_item_id as order_item_id,
                        GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
                    FROM tbl_delivery_items
                    INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                    WHERE tbl_delivery_items.order_item_id IN (".implode(',', $arrOrderItemId).")
                    GROUP BY tbl_delivery_items.order_item_id
                ";
                $listDelivery = $this->db->query($tbDelivery)->result_array();
                if (!empty($listDelivery)) {
                    $listDelivery = array_reduce($listDelivery, function($carry, $item) {
                        $carry[$item['order_item_id']] = $item;
                        return $carry;
                    });
                }

                //production
                $tbProductions = '
                    SELECT
                        tbl_productions_orders_items.production_plan_item_id,
                        GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR "</br>") as code_production
                    FROM tbl_productions_orders_items
                    JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                    WHERE tbl_productions_orders_items.object_item_type = "orders" AND tbl_productions_orders_items.production_plan_item_id IN ('.implode(",", $arrOrderItemId).')
                    GROUP BY tbl_productions_orders_items.production_plan_item_id
                ';
                $listProductions = $this->db->query($tbProductions)->result_array();
                if (!empty($listProductions)) {
                    $listProductions = array_reduce($listProductions, function($carry, $item) {
                        $carry[$item['production_plan_item_id']] = $item;
                        return $carry;
                    });
                }

                
            }

            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                //warehouses
                $tbWarehousesProducts = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                    LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
                    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0 AND tblwarehouse_items.id_items IN (".implode(',', $arrItemId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listWarehousesProducts = $this->db->query($tbWarehousesProducts)->result_array();
                if (!empty($listWarehousesProducts)) {
                    $listWarehousesProducts = array_reduce($listWarehousesProducts, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }
            }
        }

        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];
            $order_item_id = $aRow['order_item_id'];
            $item_id = $aRow['item_id'];

            $dtDelivery = $listDelivery[$order_item_id] ?? null;
            if (!empty($dtDelivery)) $aRow['quantity_detail'] = $dtDelivery['date_delivery'];

            $dtProductions = $listProductions[$order_item_id] ?? null;
            if (!empty($dtProductions)) $aRow['code_production'] = $dtProductions['code_production'];

            $dtWarehousesProducts = $listWarehousesProducts[$item_id] ?? null;
            if (!empty($dtWarehousesProducts)) $aRow['quantity_warehouse'] = $dtWarehousesProducts['product_quantity'];

            $row = [];
            $name_status_orders = !empty($aRow['name_status_orders']) ? $aRow['name_status_orders'] : 'Chưa xác định';
            if ($group != $name_status_orders) {
                $row[] = '';
                $row[] = 'group';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '<div class="text-left">' . (!empty($aRow['time']) ? $aRow['time'] : 'Chưa xác định') . '</div>';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';
                $row[] = '';

                $group = $name_status_orders;
                $output['aaData'][] = $row;
            }
			$row = [];
            $is_cancel = $aRow['is_cancel'];
            $str_cancel = '';
            if ($is_cancel) {
                $str_cancel = '<div class="mtop5 text-danger">'.lang('tnh_cancelled_order').'</div>';
            }

            $row[] = '<div class="text-center">' . $start . '</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['type_items']) ? ($aRow['type_items'] == '1' ? 'Cố Định' : 'Thay Đổi') : '') . '</div>';
            $images = base_url('assets/images/tnh/no_image.png');
            if (!empty($aRow['images'])) {
                $images = base_url('uploads/products/' . $aRow['images']);
            }
            $row[] = '<div class="td-image">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:45px; margin: auto;">
                            <a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class="">
                                    <img src="' . $images . '" style="border-radius: 50%">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>';
            
            $row[] = '<div class="text-left">' . ($aRow['item_code']) . '</div>';
            $row[] = '<div class="text-center">' . $name_status_orders . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center">' . $aRow['zcode'] . '</div>';
            $row[] = '<div class="text-center">'.$aRow['brand'].'</div>';
            $row[] = '<div class="text-left">' . $aRow['reference_no'] . $str_cancel. '</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['date_delivery']) ? _d($aRow['date_delivery']) : '') . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['product_name_customer']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_not_delivery']) . '</div>';

            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_orders']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_delivery']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_rest']) . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity_warehouse']) . '</div>';
            $row[] = '<div class="text-center">' . formatMoney($aRow['price']) . '</div>';
            $row[] = '<div class="text-center">' . $aRow['quantity_detail'] . '</div>';

            $row[] = '<div class="text-center">' . $aRow['name_species'] . '</div>';
            $row[] = '<div class="text-center">' . $aRow['name_type_print'] . '</div>';

            
			$row[] = $aRow['note_item'];
            $row[] = $aRow['name_type_orders'];
			
            $row[] = $aRow['code_production'];
            $row[] = !empty($aRow['staff_create_orders']) ? get_staff_full_name($aRow['staff_create_orders']) : '';
			
            $row[] = $aRow['name_branch'];

            $quantity_not_delivery += $aRow['quantity_not_delivery'];
            $quantity_orders += $aRow['quantity_orders'];
            $quantity_delivery += $aRow['quantity_delivery'];
            $quantity_rest += $aRow['quantity_rest'];
            $quantity_warehouse+= (float)$aRow['quantity_warehouse'];
            $output['aaData'][] = $row;
        }
        $output['quantity_not_delivery'] = $quantity_not_delivery;
        $output['quantity_orders'] = $quantity_orders;
        $output['quantity_delivery'] = $quantity_delivery;
        $output['quantity_rest'] = $quantity_rest;
        $output['quantity_warehouse'] = $quantity_warehouse;
        echo json_encode($output);
    }

    public function excel_orders_information()
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        
        if (ob_get_level() > 0) {
            ob_clean();
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $objPHPExcel = new PHPExcel();

        $customer_search = $this->input->get('customer_search');
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $type_orders_search = $this->input->get('type_orders_search');
        $status_orders_search = $this->input->get('status_orders_search');
        $items_search = $this->input->get('items_search');
        $branch_search = $this->input->get('branch_search');

        $str_customer = lang('all');
        if (!empty($customer_search)) {
            $dtCustomer = get_table_where('tblclients', ['userid' => $customer_search], '', 'row_array', '', 'company');
            $str_customer = $dtCustomer['company'];
        }

        $str_start_date = lang('all');
        if (!empty($start_date_search)) {
            $str_start_date = $start_date_search;
        }

        $str_end_date = lang('all');
        if (!empty($start_date_search)) {
            $str_end_date = $start_date_search;
        }

        $str_type_orders = lang('all');
        if (!empty($type_orders_search)) {
            $dtTypeOrders = get_table_where('tbl_type_orders', ['id' => $type_orders_search], '', 'row_array', '', 'name');
            $str_type_orders = $dtTypeOrders['name'];
        }

        $str_status_orders = lang('all');
        if (!empty($status_orders_search)) {
            $dtStatusOrders = get_table_where('tbl_status_orders', ['id' => $status_orders_search], '', 'row_array', '', 'name, day_start, day_end');
            $str_status_orders = $dtStatusOrders['name'];
        }

        $tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
            FROM tbl_delivery_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery_item";

        $tbDateExpectedDelivery = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_order_item_shippings";

        $tbGroupCustomer = '(
            SELECT
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as group_name
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group';

        $tbWarehousesProducts = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0
            GROUP BY tblwarehouse_items.id_items
        ) tb_warehouses_products";


        $this->db->select('
            tbl_orders.id as id,
            tbl_orders.date as date,
            tblclients.zcode as zcode,
            tb_customer_group.group_name as brand,
            tbl_orders.reference_no as reference_no,
            tb_order_item_shippings.date_shipping as date_delivery,
            tbl_order_items.product_name_customer as product_name_customer,
            tbl_products.code as item_code,
            tbl_products.product_code_customer as product_code_customer,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_not_delivery,
            tbl_order_items.quantity as quantity_orders,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_rest,
            0 as quantity_warehouse,
            tbl_order_items.price as price,
            "" as quantity_detail,
            tbl_species.name as name_species,
            tbl_type_print.name as name_type_print,
            tbl_products.images as images,
            tbl_order_items.note_item as note_item,
            tbl_status_orders.name as name_status_orders,
            tbl_status_orders.time as time,
            tbl_type_orders.name as name_type_orders,
            tbl_orders.created_by as created_by,
            tblbranch.name as name_branch,
            tbl_orders.is_cancel as is_cancel,
            tbl_orders.type_items as type_items,
            tbl_order_items.id as order_item_id,
            tbl_order_items.item_id as item_id,
            "" as code_production
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'inner');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left');
        $this->db->join('tbl_status_orders', 'tbl_status_orders.id = tbl_orders.status_orders', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
        $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left');
        $this->db->join($tbDateExpectedDelivery, 'tb_order_item_shippings.order_item_id = tbl_order_items.id', 'left');
        // $this->db->join($tbDelivery, 'tb_delivery_item.order_item_id = tbl_order_items.id', 'left');
        $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
        // $this->db->join($tbWarehousesProducts, 'tb_warehouses_products.id_items = tbl_order_items.item_id', 'left');

        $this->db->order_by('tbl_status_orders.id DESC');

        $where        = [
            'tbl_order_items.type_item = "products"'
        ];

        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

        if (!empty($items_search)) {
			$items_search = explode('__', $items_search);
            array_push($where, "AND tbl_order_items.item_id = " . $items_search[0]);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_orders.date <= '" . $end_date_search . "'");
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
			if(!empty($dtStatusOrders)) {
				if(!empty($dtStatusOrders['day_start']) && !empty($dtStatusOrders['day_end'])) {
					$day_start = $dtStatusOrders['day_start'];
					$day_end = $dtStatusOrders['day_end'];
					$date_ship_start = date("Y-m-d", strtotime("+$day_start day"));
					$date_ship_end = date("Y-m-d", strtotime("+$day_end day"));
					$where[] = 'AND tb_order_item_shippings.date_shipping >= "' . $date_ship_start . '"';
					$where[] = 'AND tb_order_item_shippings.date_shipping <= "' . $date_ship_end . '"';
				}
			}
        }

        if (!empty($branch_search)) {
            array_push($where, "AND tbl_orders.id_branch = " . $branch_search . "");
        }

        $where = implode(' ', $where);
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }
        $rs = $this->db->get()->result_array();

        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $objPHPExcel->getActiveSheet()->setCellValue('E5', 'THỐNG KÊ ĐƠN HÀNG');
        $objPHPExcel->getActiveSheet()->mergeCells("E5:M5");

        $objPHPExcel->getActiveSheet()->getStyle("E5")->applyFromArray($style_excel['c_head']);

        $objPHPExcel->getActiveSheet()->setCellValue('E6', 'Khách hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F6', $str_customer);

        $objPHPExcel->getActiveSheet()->setCellValue('E7', 'Loại đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F7', $str_type_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('E8', 'Trạng thái đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F8', $str_status_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('E9', 'Ngày bắt đầu: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F9', $str_start_date);

        $objPHPExcel->getActiveSheet()->setCellValue('G9', 'Ngày kết thúc: ');
        $objPHPExcel->getActiveSheet()->setCellValue('H9', $str_end_date);

		
		
        $objPHPExcel->getActiveSheet()->setCellValue('A11', 'STT');
	
		$objPHPExcel->getActiveSheet()->setCellValue('B11', 'Nhóm Đơn Hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('C11', 'Hình sản phẩm');

		$objPHPExcel->getActiveSheet()->setCellValue('D11', 'Mã TP');
		$objPHPExcel->getActiveSheet()->setCellValue('E11', 'Trạng Thái ĐH');
		
		
        $objPHPExcel->getActiveSheet()->setCellValue('F11', 'Ngày mở đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('G11', 'Mã KH');
        $objPHPExcel->getActiveSheet()->setCellValue('H11', 'Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('I11', 'Mã ĐĐH');
        $objPHPExcel->getActiveSheet()->setCellValue('J11', 'Ngày giao dự kiến');
        $objPHPExcel->getActiveSheet()->setCellValue('K11', 'Tên TP của khách');
        $objPHPExcel->getActiveSheet()->setCellValue('L11', 'SL chưa giao');
        $objPHPExcel->getActiveSheet()->setCellValue('M11', 'SL đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('N11', 'SL đã giao');
        $objPHPExcel->getActiveSheet()->setCellValue('O11', 'SL còn lại');
        $objPHPExcel->getActiveSheet()->setCellValue('P11', 'Số lượng tồn');
        $objPHPExcel->getActiveSheet()->setCellValue('q11', 'Đơn giá');
        $objPHPExcel->getActiveSheet()->setCellValue('R11', 'Ngày giao hàng - SL chi tiết');
        $objPHPExcel->getActiveSheet()->setCellValue('S11', 'Chủng loại');
        $objPHPExcel->getActiveSheet()->setCellValue('T11', 'Loại hình in');
        $objPHPExcel->getActiveSheet()->setCellValue('U11', 'Ghi chú');
        $objPHPExcel->getActiveSheet()->setCellValue('V11', 'Loại đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('W11', 'LSX');
        $objPHPExcel->getActiveSheet()->setCellValue('X11', 'Người lập đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('Y11', 'Chi nhánh xưởng');
        $objPHPExcel->getActiveSheet()->setCellValue('Z11', 'Trạng thái hủy');


        $objPHPExcel->getActiveSheet()->getStyle("A5:Z11")->applyFromArray([
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => '000000'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
        ]);


        $row = 11;
        $group = '';
        $start = 0;
        $quantity_not_delivery = 0;
        $quantity_orders = 0;
        $quantity_delivery = 0;
        $quantity_rest = 0;
        $quantity_warehouse = 0;
        if (!empty($rs)) {
            $arrOrderItemId = [];
            $arrItemId = [];
            foreach ($rs as $key => $value) {
                $arrOrderItemId[] = $value['order_item_id'];
                $arrItemId[] = $value['item_id'];
            }

            if (!empty($arrOrderItemId)) {
                $arrOrderItemId = array_unique($arrOrderItemId);
                $tbDelivery = "
                    SELECT
                        tbl_delivery_items.order_item_id as order_item_id,
                        GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
                    FROM tbl_delivery_items
                    INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                    WHERE tbl_delivery_items.order_item_id IN (".implode(',', $arrOrderItemId).")
                    GROUP BY tbl_delivery_items.order_item_id
                ";
                $listDelivery = $this->db->query($tbDelivery)->result_array();
                if (!empty($listDelivery)) {
                    $listDelivery = array_reduce($listDelivery, function($carry, $item) {
                        $carry[$item['order_item_id']] = $item;
                        return $carry;
                    });
                }

                //production
                $tbProductions = '
                    SELECT
                        tbl_productions_orders_items.production_plan_item_id,
                        GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR ", ") as code_production
                    FROM tbl_productions_orders_items
                    JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                    WHERE tbl_productions_orders_items.object_item_type = "orders" AND tbl_productions_orders_items.production_plan_item_id IN ('.implode(",", $arrOrderItemId).')
                    GROUP BY tbl_productions_orders_items.production_plan_item_id
                ';
                $listProductions = $this->db->query($tbProductions)->result_array();
                if (!empty($listProductions)) {
                    $listProductions = array_reduce($listProductions, function($carry, $item) {
                        $carry[$item['production_plan_item_id']] = $item;
                        return $carry;
                    });
                }

                
            }

            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                //warehouses
                $tbWarehousesProducts = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                    LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
                    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0 AND tblwarehouse_items.id_items IN (".implode(',', $arrItemId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listWarehousesProducts = $this->db->query($tbWarehousesProducts)->result_array();
                if (!empty($listWarehousesProducts)) {
                    $listWarehousesProducts = array_reduce($listWarehousesProducts, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }
            }

            foreach ($rs as $key => $aRow) {
                // Sanitize emojis and control characters that corrupt Excel when combined with Drawing blocks
                $fields_to_sanitize = ['item_code', 'zcode', 'brand', 'reference_no', 'product_name_customer', 'note_item', 'name_species', 'name_type_print', 'name_type_orders', 'code_production', 'name_branch', 'name_status_orders'];
                foreach ($fields_to_sanitize as $field) {
                    if (isset($aRow[$field]) && is_string($aRow[$field])) {
                        // Remove 4-byte characters (emojis) and unprintable control chars
                        $aRow[$field] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $aRow[$field]);
                        $aRow[$field] = preg_replace('/[\x00-\x1F\x7F]/', ' ', $aRow[$field]); // Note: stripped \n and \r
                        // $aRow[$field] = mb_substr($aRow[$field], 0, 500); // Prevent PHPExcel CONTINUE record issues
                        if (strpos($aRow[$field], '=') === 0) $aRow[$field] = ' ' . $aRow[$field]; // Prevent formula macro injection
                    }
                }

                $row++;
                $start++;
                $order_item_id = $aRow['order_item_id'];
                $item_id = $aRow['item_id'];
                $dtDelivery = $listDelivery[$order_item_id] ?? null;
                if (!empty($dtDelivery)) $aRow['quantity_detail'] = $dtDelivery['date_delivery'];

                $dtProductions = $listProductions[$order_item_id] ?? null;
                if (!empty($dtProductions)) $aRow['code_production'] = $dtProductions['code_production'];

                $dtWarehousesProducts = $listWarehousesProducts[$item_id] ?? null;
                if (!empty($dtWarehousesProducts)) $aRow['quantity_warehouse'] = $dtWarehousesProducts['product_quantity'];

                $name_status_orders = !empty($aRow['name_status_orders']) ? $aRow['name_status_orders'] : 'Chưa xác định';
                if ($group != $name_status_orders) {
                    $group = $name_status_orders;

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, (!empty($aRow['time']) ? $aRow['time'] : 'Chưa xác định'));
                    $objPHPExcel->getActiveSheet()->mergeCells("A$row:Z$row");
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'e3b897')
                            ),
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => '000000'),
								'size' => 12,
								'name' => 'Times New Roman'
							),
                        )
                    );
                    $row++;
                }

				// $po = "(
				// 	SELECT
				// 		GROUP_CONCAt(tbl_productions_orders.reference_no) as reference_no
				// 	FROM tbl_productions_plan_orders
				// 	INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
				// 	WHERE tbl_productions_plan_orders.productions_plan_id = ".$aRow['id']." AND tbl_productions_plan_orders.object_type = 'orders'
            	// )";
				// $dtPO = $this->db->query($po)->row();

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start);
	
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, (!empty($aRow['type_items']) ? ($aRow['type_items'] == '1' ? 'Cố Định' : 'Thay Đổi') : ''));

                // $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                // $allowedExtensions = ['png', 'jpg'];
                $ext = strtolower(pathinfo($aRow['images'], PATHINFO_EXTENSION));
                if (!empty($aRow['images'])) $aRow['images'] = 'uploads/products/'.$aRow['images'];
                // if ($aRow['images'] != '' && file_exists($aRow['images']) && in_array($ext, $allowedExtensions)) {
                // $arrDiff = [14, 15, 23, 24, 25, 44, 54, 55, 56, 57, 58, 59, 63, 66, 67, 72];
                // $arrDiff = [55, 56, 57, 58, 59, 63, 66, 67, 72];
                // $arrDiff = [63, 66, 67, 72];
                // $arrDiff = [67, 72];
                // $arrDiff = [67, 72];
                $arrDiff = [14];
                if ($aRow['images'] != '' && file_exists($aRow['images']) && !is_dir($aRow['images']) && !in_array($row, $arrDiff)) {
                    $imgSize = @getimagesize($aRow['images']);
                    if ($imgSize !== false && $imgSize[0] > 0 && $imgSize[1] > 0) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($aRow['images']);

                        list($originalWidth, $originalHeight) = $imgSize;
        
                        $maxWidth = 80;  // Chiều rộng tối đa của ô
                        $maxHeight = 80; // Chiều cao tối đa của ô

                        // Tính tỷ lệ để giữ nguyên khung hình
                        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                        $scaledWidth = (int)($originalWidth * $scale);
                        $scaledHeight = (int)($originalHeight * $scale);

                        $objDrawing1->setWidth($scaledWidth);
                        $objDrawing1->setHeight($scaledHeight);

                        $offsetX = (int)(($maxWidth - $scaledWidth) / 2);
                        $offsetY = (int)(($maxHeight - $scaledHeight) / 2);
                        $objDrawing1->setOffsetX($offsetX + 2);
                        $objDrawing1->setOffsetY($offsetY + 2);
                        $objDrawing1->setCoordinates('C' . ($row));
                        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($maxHeight);
                    }
                }

				$objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $aRow['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $name_status_orders);

                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, _dC($aRow['date']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $aRow['zcode'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $aRow['brand'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $aRow['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, _dC($aRow['date_delivery']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $aRow['product_name_customer'], PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $aRow['quantity_not_delivery']);
                $objPHPExcel->getActiveSheet()->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_not_delivery']));

                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['quantity_orders']);
                $objPHPExcel->getActiveSheet()->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_orders']));

                $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $aRow['quantity_delivery']);
                $objPHPExcel->getActiveSheet()->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_delivery']));

                $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['quantity_rest']);
                $objPHPExcel->getActiveSheet()->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_rest']));

                $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $aRow['quantity_warehouse']);
                $objPHPExcel->getActiveSheet()->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_warehouse']));

                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $aRow['price']);
                $objPHPExcel->getActiveSheet()->getStyle("Q$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['price']));

                $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $aRow['quantity_detail']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, $aRow['name_species'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('T' . $row, $aRow['name_type_print'], PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit('U' . $row, $aRow['note_item'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('V' . $row, $aRow['name_type_orders'], PHPExcel_Cell_DataType::TYPE_STRING);
                // $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, (!empty($dtPO->reference_no) ? $dtPO->reference_no : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, $aRow['code_production'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('X' . $row, get_staff_full_name($aRow['created_by']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Y' . $row, $aRow['name_branch'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $aRow['is_cancel'] ? 'Có' : '');


				$quantity_not_delivery += $aRow['quantity_not_delivery'];
                $quantity_orders += $aRow['quantity_orders'];
                $quantity_delivery += $aRow['quantity_delivery'];
                $quantity_rest += $aRow['quantity_not_delivery'];
                $quantity_warehouse+= (float)$aRow['quantity_warehouse'];
            }
        }

        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, 'Tổng cộng');
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $quantity_not_delivery)->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_not_delivery));
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $quantity_orders)->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_orders));
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $quantity_delivery)->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_delivery));
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $quantity_rest)->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_rest));
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $quantity_warehouse)->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_warehouse));

        $objPHPExcel->getActiveSheet()->getStyle("A$row:Z$row")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		
		
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
        $filename = lang('tnh_excel_orders_information') . '.xlsx';

        $objPHPExcel->getActiveSheet()->getStyle("A11:Z$row")->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A1:AA$row")->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }

    public function excel_orders_information11()
    {
        ob_end_clean();
        
        if (!$this->perPrintOrders) {
            accessDenied();
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $objPHPExcel = new PHPExcel();

        $customer_search = $this->input->get('customer_search');
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $type_orders_search = $this->input->get('type_orders_search');
        $status_orders_search = $this->input->get('status_orders_search');
        $items_search = $this->input->get('items_search');
        $branch_search = $this->input->get('branch_search');

        $str_customer = lang('all');
        if (!empty($customer_search)) {
            $dtCustomer = get_table_where('tblclients', ['userid' => $customer_search], '', 'row_array', '', 'company');
            $str_customer = $dtCustomer['company'];
        }

        $str_start_date = lang('all');
        if (!empty($start_date_search)) {
            $str_start_date = $start_date_search;
        }

        $str_end_date = lang('all');
        if (!empty($start_date_search)) {
            $str_end_date = $start_date_search;
        }

        $str_type_orders = lang('all');
        if (!empty($type_orders_search)) {
            $dtTypeOrders = get_table_where('tbl_type_orders', ['id' => $type_orders_search], '', 'row_array', '', 'name');
            $str_type_orders = $dtTypeOrders['name'];
        }

        $str_status_orders = lang('all');
        if (!empty($status_orders_search)) {
            $dtStatusOrders = get_table_where('tbl_status_orders', ['id' => $status_orders_search], '', 'row_array', '', 'name, day_start, day_end');
            $str_status_orders = $dtStatusOrders['name'];
        }

        $tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
            FROM tbl_delivery_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery_item";

        $tbDateExpectedDelivery = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_order_item_shippings";

        $tbGroupCustomer = '(
            SELECT
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as group_name
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group';

        $tbWarehousesProducts = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0
            GROUP BY tblwarehouse_items.id_items
        ) tb_warehouses_products";
//		$tbProductions = '(
//            SELECT
//                tbl_productions_orders_items.production_plan_item_id,
//                GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR "</br>") as code_production
//            FROM tbl_productions_orders_items
//            JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
//            WHERE tbl_productions_orders_items.object_item_type = "orders"
//            GROUP BY tbl_productions_orders_items.production_plan_item_id
//        ) tb_production';

        $this->db->select('
            tbl_orders.id as id,
            tbl_orders.date as date,
            tblclients.zcode as zcode,
            tb_customer_group.group_name as brand,
            tbl_orders.reference_no as reference_no,
            tb_order_item_shippings.date_shipping as date_delivery,
            tbl_order_items.product_name_customer as product_name_customer,
            tbl_products.code as item_code,
            tbl_products.product_code_customer as product_code_customer,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_not_delivery,
            tbl_order_items.quantity as quantity_orders,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_rest,
            0 as quantity_warehouse,
            tbl_order_items.price as price,
            "" as quantity_detail,
            tbl_species.name as name_species,
            tbl_type_print.name as name_type_print,
            tbl_products.images as images,
            tbl_order_items.note_item as note_item,
            tbl_status_orders.name as name_status_orders,
            tbl_status_orders.time as time,
            tbl_type_orders.name as name_type_orders,
            tbl_orders.created_by as created_by,
            tblbranch.name as name_branch,
            tbl_orders.is_cancel as is_cancel,
            tbl_orders.type_items as type_items,
            tbl_order_items.id as order_item_id,
            tbl_order_items.item_id as item_id,
            "" as code_production
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'inner');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left');
        $this->db->join('tbl_status_orders', 'tbl_status_orders.id = tbl_orders.status_orders', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
        $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left');
        $this->db->join($tbDateExpectedDelivery, 'tb_order_item_shippings.order_item_id = tbl_order_items.id', 'left');
        // $this->db->join($tbDelivery, 'tb_delivery_item.order_item_id = tbl_order_items.id', 'left');
        $this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
        // $this->db->join($tbWarehousesProducts, 'tb_warehouses_products.id_items = tbl_order_items.item_id', 'left');
//		$this->db->join($tbProductions, 'tb_production.production_plan_item_id = tbl_order_items.id', 'left');

        $this->db->order_by('tbl_status_orders.id DESC');

        $where        = [
            'tbl_order_items.type_item = "products"'
        ];

        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

        if (!empty($items_search)) {
			$items_search = explode('__', $items_search);
            array_push($where, "AND tbl_order_items.item_id = " . $items_search[0]);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_orders.date <= '" . $end_date_search . "'");
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
//            array_push($where, "AND tbl_orders.status_orders = " . $status_orders_search . "");
			if(!empty($dtStatusOrders)) {
				if(!empty($dtStatusOrders['day_start']) && !empty($dtStatusOrders['day_end'])) {
					$day_start = $dtStatusOrders['day_start'];
					$day_end = $dtStatusOrders['day_end'];
					$date_ship_start = date("Y-m-d", strtotime("+$day_start day"));
					$date_ship_end = date("Y-m-d", strtotime("+$day_end day"));
					$where[] = 'AND tb_order_item_shippings.date_shipping >= "' . $date_ship_start . '"';
					$where[] = 'AND tb_order_item_shippings.date_shipping <= "' . $date_ship_end . '"';
				}
			}
        }

        if (!empty($branch_search)) {
            array_push($where, "AND tbl_orders.id_branch = " . $branch_search . "");
        }

        $where = implode(' ', $where);
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }
        $rs = $this->db->get()->result_array();

        insertCompanyInfo($objPHPExcel, 'C1:P2');

        $objPHPExcel->getActiveSheet()->setCellValue('E5', 'THỐNG KÊ ĐƠN HÀNG');
        $objPHPExcel->getActiveSheet()->mergeCells("E5:M5");

        $objPHPExcel->getActiveSheet()->getStyle("E5")->applyFromArray($style_excel['c_head']);

        $objPHPExcel->getActiveSheet()->setCellValue('E6', 'Khách hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F6', $str_customer);

        $objPHPExcel->getActiveSheet()->setCellValue('E7', 'Loại đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F7', $str_type_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('E8', 'Trạng thái đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F8', $str_status_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('E9', 'Ngày bắt đầu: ');
        $objPHPExcel->getActiveSheet()->setCellValue('F9', $str_start_date);

        $objPHPExcel->getActiveSheet()->setCellValue('G9', 'Ngày kết thúc: ');
        $objPHPExcel->getActiveSheet()->setCellValue('H9', $str_end_date);

		
		
        $objPHPExcel->getActiveSheet()->setCellValue('A11', 'STT');
	
		$objPHPExcel->getActiveSheet()->setCellValue('B11', 'Nhóm Đơn Hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('C11', 'Hình sản phẩm');

		$objPHPExcel->getActiveSheet()->setCellValue('D11', 'Mã TP');
		$objPHPExcel->getActiveSheet()->setCellValue('E11', 'Trạng Thái ĐH');
		
		
        $objPHPExcel->getActiveSheet()->setCellValue('F11', 'Ngày mở đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('G11', 'Mã KH');
        $objPHPExcel->getActiveSheet()->setCellValue('H11', 'Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('I11', 'Mã ĐĐH');
        $objPHPExcel->getActiveSheet()->setCellValue('J11', 'Ngày giao dự kiến');
//        $objPHPExcel->getActiveSheet()->setCellValue('J7', 'Mã sản phẩm');
        $objPHPExcel->getActiveSheet()->setCellValue('K11', 'Tên TP của khách');
        $objPHPExcel->getActiveSheet()->setCellValue('L11', 'SL chưa giao');
        $objPHPExcel->getActiveSheet()->setCellValue('M11', 'SL đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('N11', 'SL đã giao');
        $objPHPExcel->getActiveSheet()->setCellValue('O11', 'SL còn lại');
        $objPHPExcel->getActiveSheet()->setCellValue('P11', 'Số lượng tồn');
        $objPHPExcel->getActiveSheet()->setCellValue('q11', 'Đơn giá');
        $objPHPExcel->getActiveSheet()->setCellValue('R11', 'Ngày giao hàng - SL chi tiết');
        $objPHPExcel->getActiveSheet()->setCellValue('S11', 'Chủng loại');
        $objPHPExcel->getActiveSheet()->setCellValue('T11', 'Loại hình in');
        $objPHPExcel->getActiveSheet()->setCellValue('U11', 'Ghi chú');
        $objPHPExcel->getActiveSheet()->setCellValue('V11', 'Loại đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('W11', 'LSX');
        $objPHPExcel->getActiveSheet()->setCellValue('X11', 'Người lập đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('Y11', 'Chi nhánh xưởng');
        $objPHPExcel->getActiveSheet()->setCellValue('Z11', 'Trạng thái hủy');


        $objPHPExcel->getActiveSheet()->getStyle("A5:Z11")->applyFromArray([
			'font' => array(
				'bold' => true,
				'color' => array('rgb' => '000000'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
        ]);

        $row = 11;
        $group = '';
        $start = 0;
        $quantity_not_delivery = 0;
        $quantity_orders = 0;
        $quantity_delivery = 0;
        $quantity_rest = 0;
        $quantity_warehouse = 0;
        if (!empty($rs)) {
            $arrOrderItemId = [];
            $arrItemId = [];
            foreach ($rs as $key => $value) {
                $arrOrderItemId[] = $value['order_item_id'];
                $arrItemId[] = $value['item_id'];
            }

            if (!empty($arrOrderItemId)) {
                $arrOrderItemId = array_unique($arrOrderItemId);
                $tbDelivery = "
                    SELECT
                        tbl_delivery_items.order_item_id as order_item_id,
                        GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
                    FROM tbl_delivery_items
                    INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                    WHERE tbl_delivery_items.order_item_id IN (".implode(',', $arrOrderItemId).")
                    GROUP BY tbl_delivery_items.order_item_id
                ";
                $listDelivery = $this->db->query($tbDelivery)->result_array();
                if (!empty($listDelivery)) {
                    $listDelivery = array_reduce($listDelivery, function($carry, $item) {
                        $carry[$item['order_item_id']] = $item;
                        return $carry;
                    });
                }

                //production
                $tbProductions = '
                    SELECT
                        tbl_productions_orders_items.production_plan_item_id,
                        GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR ", ") as code_production
                    FROM tbl_productions_orders_items
                    JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
                    WHERE tbl_productions_orders_items.object_item_type = "orders" AND tbl_productions_orders_items.production_plan_item_id IN ('.implode(",", $arrOrderItemId).')
                    GROUP BY tbl_productions_orders_items.production_plan_item_id
                ';
                $listProductions = $this->db->query($tbProductions)->result_array();
                if (!empty($listProductions)) {
                    $listProductions = array_reduce($listProductions, function($carry, $item) {
                        $carry[$item['production_plan_item_id']] = $item;
                        return $carry;
                    });
                }

                
            }

            if (!empty($arrItemId)) {
                $arrItemId = array_unique($arrItemId);
                //warehouses
                $tbWarehousesProducts = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as product_quantity
                    FROM tblwarehouse_items
                    INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                    LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
                    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0 AND tblwarehouse_items.id_items IN (".implode(',', $arrItemId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listWarehousesProducts = $this->db->query($tbWarehousesProducts)->result_array();
                if (!empty($listWarehousesProducts)) {
                    $listWarehousesProducts = array_reduce($listWarehousesProducts, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }
            }

            foreach ($rs as $key => $aRow) {
                $row++;
                $start++;
                $order_item_id = $aRow['order_item_id'];
                $item_id = $aRow['item_id'];
                $dtDelivery = $listDelivery[$order_item_id] ?? null;
                if (!empty($dtDelivery)) $aRow['quantity_detail'] = $dtDelivery['date_delivery'];

                $dtProductions = $listProductions[$order_item_id] ?? null;
                if (!empty($dtProductions)) $aRow['code_production'] = $dtProductions['code_production'];

                $dtWarehousesProducts = $listWarehousesProducts[$item_id] ?? null;
                if (!empty($dtWarehousesProducts)) $aRow['quantity_warehouse'] = $dtWarehousesProducts['product_quantity'];

                $name_status_orders = !empty($aRow['name_status_orders']) ? $aRow['name_status_orders'] : 'Chưa xác định';
                if ($group != $name_status_orders) {
                    $group = $name_status_orders;

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, (!empty($aRow['time']) ? $aRow['time'] : 'Chưa xác định'));
                    $objPHPExcel->getActiveSheet()->mergeCells("A$row:Z$row");
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'e3b897')
                            ),
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => '000000'),
								'size' => 12,
								'name' => 'Times New Roman'
							),
                        )
                    );
                    $row++;
                }

				// $po = "(
				// 	SELECT
				// 		GROUP_CONCAt(tbl_productions_orders.reference_no) as reference_no
				// 	FROM tbl_productions_plan_orders
				// 	INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
				// 	WHERE tbl_productions_plan_orders.productions_plan_id = ".$aRow['id']." AND tbl_productions_plan_orders.object_type = 'orders'
            	// )";
				// $dtPO = $this->db->query($po)->row();

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start);
	
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, (!empty($aRow['type_items']) ? ($aRow['type_items'] == '1' ? 'Cố Định' : 'Thay Đổi') : ''));

                // $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                // $allowedExtensions = ['png', 'jpg'];
                $ext = strtolower(pathinfo($aRow['images'], PATHINFO_EXTENSION));
                if (!empty($aRow['images'])) $aRow['images'] = 'uploads/products/'.$aRow['images'];
                // if ($aRow['images'] != '' && file_exists($aRow['images']) && in_array($ext, $allowedExtensions)) {
                // $arrDiff = [14, 15, 23, 24, 25, 44, 54, 55, 56, 57, 58, 59, 63, 66, 67, 72];
                // $arrDiff = [55, 56, 57, 58, 59, 63, 66, 67, 72];
                // $arrDiff = [63, 66, 67, 72];
                // $arrDiff = [67, 72];
                // $arrDiff = [67, 72];
                $arrDiff = [14];
                // $arrDiff = [];
                if ($aRow['images'] != '' && file_exists($aRow['images']) && !in_array($row, $arrDiff)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($aRow['images']);

                    list($originalWidth, $originalHeight) = getimagesize($aRow['images']);
    
                    $maxWidth = 80;  // Chiều rộng tối đa của ô
                    $maxHeight = 80; // Chiều cao tối đa của ô

                    // Tính tỷ lệ để giữ nguyên khung hình
                    $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                    $scaledWidth = $originalWidth * $scale;
                    $scaledHeight = $originalHeight * $scale;

                    $objDrawing1->setWidth($scaledWidth);
                    $objDrawing1->setHeight($scaledHeight);

                    $offsetX = ($maxWidth - $scaledWidth) / 2;
                    $offsetY = ($maxHeight - $scaledHeight) / 2;
                    $objDrawing1->setOffsetX($offsetX + 2);
                    $objDrawing1->setOffsetY($offsetY + 2);
                    $objDrawing1->setCoordinates('C' . ($row));
                    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($maxHeight);

                    // $objDrawing1->setWidth(80);
                    // $objDrawing1->setOffsetX(10);
                    // $objDrawing1->setOffsetY(2);
                    // $objDrawing1->setCoordinates('C' . ($row));
                    // $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(80);

                    // $objDrawing1->setHeight(80);
                    // $objDrawing1->setHeight(50);
                }

                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $aRow['item_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $name_status_orders);

                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, _dC($aRow['date']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $aRow['zcode'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $aRow['brand'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row, $aRow['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, _dC($aRow['date_delivery']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, $aRow['product_name_customer'], PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $aRow['quantity_not_delivery']);
                $objPHPExcel->getActiveSheet()->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_not_delivery']));

                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['quantity_orders']);
                $objPHPExcel->getActiveSheet()->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_orders']));

                $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $aRow['quantity_delivery']);
                $objPHPExcel->getActiveSheet()->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_delivery']));

                $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['quantity_rest']);
                $objPHPExcel->getActiveSheet()->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_rest']));

                $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $aRow['quantity_warehouse']);
                $objPHPExcel->getActiveSheet()->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_warehouse']));

                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $aRow['price']);
                $objPHPExcel->getActiveSheet()->getStyle("Q$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['price']));

                $objPHPExcel->getActiveSheet()->setCellValue('R' . $row, $aRow['quantity_detail']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row, $aRow['name_species'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('T' . $row, $aRow['name_type_print'], PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit('U' . $row, $aRow['note_item'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('V' . $row, $aRow['name_type_orders'], PHPExcel_Cell_DataType::TYPE_STRING);
                // $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, (!empty($dtPO->reference_no) ? $dtPO->reference_no : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('W' . $row, $aRow['code_production'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('X' . $row, get_staff_full_name($aRow['created_by']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Y' . $row, $aRow['name_branch'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row, $aRow['is_cancel'] ? 'Có' : '');


				$quantity_not_delivery += $aRow['quantity_not_delivery'];
                $quantity_orders += $aRow['quantity_orders'];
                $quantity_delivery += $aRow['quantity_delivery'];
                $quantity_rest += $aRow['quantity_not_delivery'];
                $quantity_warehouse+= (float)$aRow['quantity_warehouse'];
            }
        }

        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, 'Tổng cộng');
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $quantity_not_delivery)->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_not_delivery));
        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $quantity_orders)->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_orders));
        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $quantity_delivery)->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_delivery));
        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $quantity_rest)->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_rest));
        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $quantity_warehouse)->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_warehouse));

        $objPHPExcel->getActiveSheet()->getStyle("A$row:Z$row")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		
		
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
        $filename = lang('tnh_excel_orders_information') . '.xls';

        $objPHPExcel->getActiveSheet()->getStyle("A11:Z$row")->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
			'font' => array(
				'color' => array('rgb' => '000000'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A1:AA$row")->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }


    public function excel_orders_information_detail()
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }

        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $objPHPExcel = new PHPExcel();

        $customer_search = $this->input->get('customer_search');
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $type_orders_search = $this->input->get('type_orders_search');
        $status_orders_search = $this->input->get('status_orders_search');

        $str_customer = lang('all');
        if (!empty($customer_search)) {
            $dtCustomer = get_table_where('tblclients', ['userid' => $customer_search], '', 'row_array', '', 'company');
            $str_customer = $dtCustomer['company'];
        }

        $str_start_date = lang('all');
        if (!empty($start_date_search)) {
            $str_start_date = $start_date_search;
        }

        $str_end_date = lang('all');
        if (!empty($start_date_search)) {
            $str_end_date = $start_date_search;
        }

        $str_type_orders = lang('all');
        if (!empty($type_orders_search)) {
            $dtTypeOrders = get_table_where('tbl_type_orders', ['id' => $type_orders_search], '', 'row_array', '', 'name');
            $str_type_orders = $dtTypeOrders['name'];
        }

        $str_status_orders = lang('all');
        if (!empty($status_orders_search)) {
            $dtStatusOrders = get_table_where('tbl_status_orders', ['id' => $status_orders_search], '', 'row_array', '', 'name');
            $str_status_orders = $dtStatusOrders['name'];
        }

        $tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
            FROM tbl_delivery_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery_item";

        $tbDateExpectedDelivery = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_order_item_shippings";

        $this->db->select('
            tbl_orders.id as id,
            tbl_orders.date as date,
            tblclients.zcode as zcode,
            tbl_orders.reference_no as reference_no,
            tb_order_item_shippings.date_shipping as date_delivery,
            tbl_order_items.product_name_customer as product_name_customer,
            tbl_products.code as item_code,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_not_delivery,
            tbl_order_items.quantity as quantity_orders,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_rest,
            tbl_order_items.price as price,
            tb_delivery_item.date_delivery as quantity_detail,
            tbl_species.name as name_species,
            tbl_type_print.name as name_type_print,
            tbl_products.images as images,
            tbl_order_items.note_item as note_item,
            tbl_status_orders.name as name_status_orders,
            tbl_status_orders.time as time,
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'inner');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
        $this->db->join('tbl_status_orders', 'tbl_status_orders.id = tbl_orders.status_orders', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
        $this->db->join($tbDateExpectedDelivery, 'tb_order_item_shippings.order_item_id = tbl_order_items.id', 'left');
        $this->db->join($tbDelivery, 'tb_delivery_item.order_item_id = tbl_order_items.id', 'left');

        $this->db->order_by('tbl_status_orders.id DESC');

        $where        = [
            'tbl_order_items.type_item = "products"'
        ];

        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_orders.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_orders.date <= '" . $end_date_search . "'");
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
            array_push($where, "AND tbl_orders.status_orders = " . $status_orders_search . "");
        }

        $where = implode(' ', $where);
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }
        $rs = $this->db->get()->result_array();

        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'THỐNG KÊ ĐƠN HÀNG');
        $objPHPExcel->getActiveSheet()->mergeCells("B1:J1");

        $objPHPExcel->getActiveSheet()->getStyle("B1")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 20,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Khách hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', $str_customer);

        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Loại đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', $str_type_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Trạng thái đơn hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', $str_status_orders);

        $objPHPExcel->getActiveSheet()->setCellValue('B5', 'Ngày bắt đầu: ');
        $objPHPExcel->getActiveSheet()->setCellValue('C5', $str_start_date);

        $objPHPExcel->getActiveSheet()->setCellValue('D5', 'Ngày kết thúc: ');
        $objPHPExcel->getActiveSheet()->setCellValue('E5', $str_end_date);

        $objPHPExcel->getActiveSheet()->setCellValue('A7', 'STT');
        $objPHPExcel->getActiveSheet()->setCellValue('B7', 'Ngày mở đơn');
        $objPHPExcel->getActiveSheet()->setCellValue('C7', 'Mã KH');
        $objPHPExcel->getActiveSheet()->setCellValue('D7', 'Mã ĐĐH');
        $objPHPExcel->getActiveSheet()->setCellValue('E7', 'Ngày giao dự kiến');
        $objPHPExcel->getActiveSheet()->setCellValue('F7', 'Mã sản phẩm');
        $objPHPExcel->getActiveSheet()->setCellValue('G7', 'Tên TP của khách');
        $objPHPExcel->getActiveSheet()->setCellValue('H7', 'Mã đơn đặt');
        $objPHPExcel->getActiveSheet()->setCellValue('I7', 'Mã chi lệnh');
        $objPHPExcel->getActiveSheet()->setCellValue('J7', 'SL đơn hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('K7', 'Số lượng loss');
        $objPHPExcel->getActiveSheet()->setCellValue('L7', 'SL làm mẫu');
        $objPHPExcel->getActiveSheet()->setCellValue('M7', 'Đơn giá');
//        $objPHPExcel->getActiveSheet()->setCellValue('M7', 'Ngày giao hàng - SL chi tiết');
        $objPHPExcel->getActiveSheet()->setCellValue('N7', 'Chủng loại');
        $objPHPExcel->getActiveSheet()->setCellValue('O7', 'Loại hình in');
//        $objPHPExcel->getActiveSheet()->setCellValue('P7', 'Hình sản phẩm');
        $objPHPExcel->getActiveSheet()->setCellValue('P7', 'Ghi chú');

        $objPHPExcel->getActiveSheet()->getStyle("B1:P7")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A7:P7")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $row = 7;
        $group = '';
        $start = 0;
//        $quantity_not_delivery = 0;
//        $quantity_orders = 0;
//        $quantity_delivery = 0;
//        $quantity_rest = 0;
		$quantity_put = 0;
		$quantity_loss = 0;
		$sample_quantity_item = 0;
        if (!empty($rs)) {
            foreach ($rs as $key => $aRow) {

				$row++;
                $name_status_orders = !empty($aRow['name_status_orders']) ? $aRow['name_status_orders'] : 'Chưa xác định';
                if ($group != $name_status_orders) {
                    $group = $name_status_orders;

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, (!empty($aRow['time']) ? $aRow['time'] : 'Chưa xác định'));
                    $objPHPExcel->getActiveSheet()->mergeCells("A$row:P$row");
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('rgb' => 'e3b897')
                            )
                        )
                    );
                }

				$this->db->where('order_id', $aRow['id']);
				$this->db->group_start();
				$this->db->where('columns_value', 'order_code');
				$this->db->or_where('columns_value', 'command');
				$this->db->or_where('columns_value', 'quantity_put');
				$this->db->or_where('columns_value', 'quantity_loss');
				$this->db->or_where('columns_value', 'sample_quantity_item');
				$this->db->group_end();
				$data_columns = $this->db->get('tbl_order_items_columns')->result_array();
				$dataTr = [];
				foreach($data_columns as $k => $value) {
					$dataTr[$value['counter_item']][$value['columns_value']] = $value['columns_name'];
				}

				foreach($dataTr as $k => $value) {
					$row++;
					$start++;
					$value['quantity_put'] = !empty($value['quantity_put']) ? $value['quantity_put'] : 0;
					$value['quantity_loss'] = !empty($value['quantity_loss']) ? $value['quantity_loss'] : 0;
					$value['sample_quantity_item'] = !empty($value['sample_quantity_item']) ? $value['sample_quantity_item'] : 0;
					$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start);
					$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, _dt($aRow['date']));
					$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $aRow['zcode']);
					$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $aRow['reference_no']);
					$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $aRow['date_delivery']);
					$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $aRow['item_code']);
					$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $aRow['product_name_customer']);
					$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, (!empty($value['order_code']) ? $value['order_code'] : ''));
					$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, (!empty($value['command']) ? $value['command'] : ''));

					$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $value['quantity_put'])->getStyle("J$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity_put']));

					$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $value['quantity_loss'])->getStyle("K$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity_loss']));
					$objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $value['sample_quantity_item'])->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['sample_quantity_item']));

//					$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['quantity_orders'])->getStyle("I$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_orders']));
//					$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $aRow['quantity_delivery'])->getStyle("J$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_delivery']));
//					$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $aRow['quantity_rest'])->getStyle("K$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_rest']));
					$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['price'])->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['price']));
//					$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['quantity_detail']);
					$objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $aRow['name_species']);
					$objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['name_type_print']);
					$objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $aRow['note_item']);
					$quantity_put += $value['quantity_put'];
					$quantity_loss += $value['quantity_loss'];
					$sample_quantity_item += $value['sample_quantity_item'];
				}
            }
        }

        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, 'Tổng cộng');
        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $quantity_put)->getStyle("J$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_put));
        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $quantity_loss)->getStyle("K$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_loss));
        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $sample_quantity_item)->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($sample_quantity_item));

        $objPHPExcel->getActiveSheet()->getStyle("A$row:P$row")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $filename = lang('Danh sách chi tiết đơn hàng') . '.xls';

        $objPHPExcel->getActiveSheet()->getStyle("A7:P$row")->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A1:P$row")->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }

    public function print_orders_information()
    {
        if (!$this->perPrintOrders) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $data['title'] = lang('tnh_print_orders_information');
        $data['type'] = 'P';
        $data['img'] = '';
        $data['img'] = '';

        ob_start();
        stylePdf();
        echo '<h1 class="text-center">THỐNG KÊ ĐƠN HÀNG</h1><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
            <tr nobr="true">
                <td class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</td>
                <td class="bold text-center" style="width: 12%;">' . _l('Mã KH') . '</td>
                <td class="bold text-center" style="width: 12%;">' . _l('Mã ĐĐH') . '</td>
                <td class="bold text-center" style="width: 10%;">' . _l('Ngày giao') . '</td>
                <td class="bold text-center" style="width: 15%;">' . _l('Mã sản phẩm') . '</td>
                <td class="bold text-center" style="width: 10%;">' . _l('SL chưa giao') . '</td>
                <td class="bold text-center" style="width: 12%;">' . _l('Chủng loại') . '</td>
                <td class="bold text-center" style="width: 12%;">' . _l('Loại hình in') . '</td>
                <td class="bold text-center" style="width: 12%;">' . _l('Ghi chú') . '</td>
            </tr>
        </table>';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['showHeader'] = 'hide';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function import_columns()
    {
        $data = [];
        if ($this->input->post()) {
            $code_import = $this->input->post('code_import');

			$code_import = explode(',', $code_import);
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            if (empty($_FILES['file_import']['tmp_name'])) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $fullfile = $_FILES['file_import']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $extension = strtoupper(pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            if (empty($code_import)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng nhập mã thành phẩm Import');
                echo json_encode($data);
                return;
            }

            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $arraydata          = array();

            $row_start = 1;
            $row_end = $highestRow < 2000 ? $highestRow : 2000;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
					$_date_sd = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					$ktDate= NULL;
					if (is_numeric($_date_sd) && $col == 0) {
						$dateValue = PHPExcel_Shared_Date::ExcelToPHPObject($_date_sd);
						$ktDate = @to_sql_date($dateValue->format('d/m/Y'));
						if(strtotime($ktDate) < strtotime("1990-01-01")) {
							$ktDate = NULL;
						}
					}
					
					if(!empty($ktDate)) {
						$arraydata[$row - 1][$col] = _d($ktDate);
					}
					else {
						$value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
						$arraydata[$row - 1][$col] = $value;
					}
                }
            }

            $this->db->select('tbl_products.*', false);
            $this->db->from('tbl_products');
            $this->db->where('tbl_products.code', $code_import[0]);
            $product = $this->db->get()->row_array();
            if (empty($product)) {
                $data['result'] = 0;
                $data['message'] = lang('Mã này không tồn tại');
                echo json_encode($data);
                return;
            }


			$this->db->select('GROUP_CONCAT(tbl_products_columns.columns_id  ORDER BY tbl_products_columns.columns_id ASC) as code_colums, tbl_products.id');
			$this->db->group_start();
			foreach($code_import as $key => $value) {
				$this->db->or_where('tbl_products.code', $value);
			}
			$this->db->group_end();
			$this->db->join('tbl_products_columns', 'tbl_products_columns.product_id = tbl_products.id', 'left');
			$this->db->group_by('tbl_products.id');
			$kt_products = $this->db->get('tbl_products')->result_array();


			$ktColums = !empty($kt_products[0]['code_colums']) ? $kt_products[0]['code_colums'] : '';

			$arrayIDproduct = [];
			foreach($kt_products as $key => $value) {
				if($ktColums != $value['code_colums']) {
					$data['result'] = 0;
					$data['message'] = lang('Có thành phẩm có Columns không khớp với nhau nên không thể import');
					echo json_encode($data);die();
//					return;
				}
				$arrayIDproduct[$value['id']] = true;
			}



            $dtColumns = $arraydata[0];
            $options = [];
            $count = 0;
            $errors = '';

            $productsColumns = $this->products_model->getProductsColumns($product['id']);
            $counter_index = $this->input->post('counter_index');
            $counter_child = $this->input->post('counter_child');
            $counter = $this->input->post('counter');

//            if (!$flagItem) {
//                $data['result'] = 0;
//                $data['message'] = lang('Mã này chưa được chọn ở dưới mặt hàng');
//                echo json_encode($data);
//                return;
//            }


            $trHtmlChildList = [];

			$listProduct = [];
			foreach($counter as $k =>  $v_counter) {
				$trHtmlChild = '';
				$temp_counter = $v_counter;
				$items_id = $this->input->post('items_id')[$v_counter];
				if (empty($items_id)) continue;
				if (empty($this->input->post('checkbox_item')[$v_counter])) continue;
				$arrItem = explode('__', $items_id);
				$items_id = $arrItem[0];
				$items_type = $arrItem[1];

				$this->db->select('tbl_products.*', false);
				$this->db->from('tbl_products');
				$this->db->where('tbl_products.id', $items_id);
				$this->db->where('tbl_products.type_products', $items_type);
				$product = $this->db->get()->row_array();

				if(empty($arrayIDproduct[$items_id])) {
					$data['result'] = 0;
					$data['message'] = lang('Mã {' . $product['code'] . '} này chưa được chọn trong mã thành phẩm import');
					echo json_encode($data);die();
					return;
				}

				$listProduct[$items_id] = true;

				foreach ($arraydata as $key => $item) {
					if ($key == 0) continue;
					$n = count($item);
					$date_ship = $item[0];
					$order_code = $item[1];
					$command = $item[2];
					$quantity_put = $item[3];
					$quantity_loss = $item[4];
					$sample_quantity_item = $item[5];
					if (empty($order_code) && empty($command)) continue;
					$trHtmlColumns = '';
					// for ($i = 4; $i < $n; $i++)
					// {
					//     $columns = !empty($dtColumns[$i]) ? $dtColumns[$i] : $i;
					//     $columns_value = $item[$i];
					//
					// }
					foreach ($productsColumns as $k => $v) {
						$columns_name = '';
						for ($i = 6; $i < $n; $i++) {
							$columns = !empty($dtColumns[$i]) ? $dtColumns[$i] : $i;
							$columns_name_import = $item[$i];
							if ($v['name'] == $columns) {
								$columns_name = $columns_name_import;
								break;
							}
						}
						$trHtmlColumns .= '
							<td>
								<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id][]" class="form-control" value="' . $v['id'] . '">
								<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value][]" class="form-control" value="' . $v['name'] . '">
								<input type="text" placeholder="' . $v['name'] . '" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_name][]" class="form-control" value="' . $columns_name . '">
							</td>';
					}
					$tdDateShip = '<td>
										<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id_date_ship]" class="form-control" value="0">
										<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value_date_ship]" class="form-control" value="date_ship">
										<input type="text" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][date_ship]" placeholder="Mã đơn đặt" class="form-control date_ship" value="' . $date_ship . '">
									</td>';
					$tdOrderCode = '<td>
										<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id_order_code]" class="form-control" value="0">
										<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value_order_code]" class="form-control" value="order_code">
										<input type="text" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][order_code]" placeholder="Mã đơn đặt" class="form-control order_code" value="' . $order_code . '">
									</td>';
					$tdCommand = '<td>
									<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id_command]" class="form-control" value="0">
									<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value_command]" class="form-control" value="command">
									<input type="text" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][command]" placeholder="Chỉ lệnh" class="form-control command" value="' . $command . '">
								</td>';
					$tdQuantityPut = '<td>
										<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id_quantity_put]" class="form-control" value="0">
										<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value_quantity_put]" class="form-control" value="quantity_put">
										<input type="text" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][quantity_put]" class="form-control quantity_put number-format" style="width: 100%;" value="' . formatNumber($quantity_put) . '">
									</td>';
					$tdQuantityLoss = '<td>
											<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id_quantity_loss]" class="form-control" value="0">
											<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value_quantity_loss]" class="form-control" value="quantity_loss">
											<input type="text" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][quantity_loss]" class="form-control quantity_loss number-format" style="width: 100%;" readonly value="' . formatNumber($quantity_loss) . '">
										</td>';
					$tdSampleQuantityItem = '<td>
												<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_id_sample_quantity_item]" class="form-control" value="0">
												<input type="hidden" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][columns_value_sample_quantity_item]" class="form-control" value="sample_quantity_item">
												<input type="text" name="itemsChildColumns[' . $temp_counter . '][' . $counter_child . '][sample_quantity_item]" class="form-control sample_quantity_item number-format" style="width: 100%;" value="' . formatNumber($sample_quantity_item) . '">
											</td>';

					$counter_child++;
					$tdNumberChild = '<td class="text-center">'.($key + 1).'</td>';
					$tdActionsChild = '<td class="text-center">
											<a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
										</td>';

					$trHtmlChild .= '<tr tr-counter="' . $temp_counter . '" class="not-tr tr-sub-items">
						' . $tdNumberChild . '
						' . $tdDateShip . '
						' . $tdOrderCode . '
						' . $tdCommand . '
						' . $tdQuantityPut . '
						' . $tdQuantityLoss . '
						' . $tdSampleQuantityItem . '
						' . $trHtmlColumns . '
						' . $tdActionsChild . '
					</tr>';
				}

				$trHtmlChildList[$v_counter] = $trHtmlChild;
			}


			if(count($listProduct) != count($arrayIDproduct)) {
				echo json_encode([
					'result' => 0,
					'message' => 'Mã thành phẩm import và danh sách thành phẩm được chọn không khớp nhau'
				]);die();
			}

            $data['counter_index'] = $temp_counter;
//            $data['trHtmlChild'] = $trHtmlChild;
            $data['trHtmlChildList'] = $trHtmlChildList;
            $data['counter_child'] = $counter_child;
            $data['result'] = 1;
            $data['message'] = lang('success');
            $data['errors'] = $errors;
            echo json_encode($data);
            die;
        }
        echo json_encode($data);
    }

    public function getPricesQuotes()
    {
        $data = [];
        $price = 0;
        if ($this->input->post()) {
            $cItemId = $this->input->post('cItemId');
            $customers = $this->input->post('customers');
            $quantity = number_unformat($this->input->post('quantity'));

            $cItemId = explode('__', $cItemId);
            $customers = str_replace('customers__', '', $customers);
            if (!empty($cItemId)) {
                $dtPrice = $this->orders_model->getPricesOfQuotes($cItemId[0], $cItemId[1], $customers, $quantity);
                if (!empty($dtPrice)) {
                    $price = $dtPrice['unit_price'];
                }
            }
        }

        $data['price'] = $price;
        echo json_encode($data);
    }

    public function add_hold_orders($id, $add_more = 0)
    {
        $data = [];
        $order = get_table_where('tbl_orders', ['id' => $id], '', 'row_array');
        $order_id = $id;
        if ($order['status'] != 'approved') {
            refererModel(lang('tnh_please_approved'));
        }

        if ($this->input->post('save')) {
            $dataPost = $this->input->post();
            $data['result'] = 0;
            $data['message'] = lang('fail');
            //            print_arrays($dataPost);

            $note = $dataPost['note'];
            if (!empty($dataPost['tick'])) {
                $counter = $this->input->post('counter');
                $errors = '';
                $totalWarehouse = 0;
                $total = 0;
                $tranferItems = [];
                $arr_id = [];
                $arr_info = [];
                foreach ($counter as $key => $value) {
                    $order_item_id = $this->input->post('order_item_id')[$value];
                    $item_id = $this->input->post('item_id')[$value];
                    if (empty($order_item_id) || empty($item_id)) {
                        continue;
                    }
                    $order_item = get_table_where(
                        'tbl_order_items',
                        ['id' => $order_item_id],
                        '',
                        'row_array'
                    );
                    if (empty($order_item)) {
                        continue;
                    }

                    $unit_id = $order_item['unit_id'];
                    $info = $this->products_model->rowProduct($item_id);
                    $exchange_unit = 1;
                    $exchange_stock = $info['conversion_quantity_unit'];
                    $exchange_payment = 1;


                    $this->db->select('SUM(tbl_delivery_items.quantity) + SUM(tbl_delivery_items.quantity_loss) + SUM(tbl_delivery_items.quantity_sample) as quantity_delivery');
                    $this->db->from('tbl_delivery_items');
                    $this->db->join('tbl_deliveries', 'tbl_deliveries.id = tbl_delivery_items.delivery_id');
                    $this->db->where('tbl_delivery_items.order_item_id', $order_item_id);
                    $dtDeliveryItemsW = $this->db->get()->row_array();

                    $quantity_delivery = 0;
                    if (!empty($dtDeliveryItemsW)) {
                        $quantity_delivery = $dtDeliveryItemsW['quantity_delivery'];
                    }

                    // $this->db->select('SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_hold');
                    // $this->db->from('tbltransfer_warehouse_detail');
                    // $this->db->where('tbltransfer_warehouse_detail.order_id_item', $order_item_id);
                    // $dtTranferItemW = $this->db->get()->row_array();

                    $tb_warehouse_product = "(
                        SELECT SUM(tblwarehouse_product.quantity_export) as quantity_export,
                        tblwarehouse_product.warehouse_id,
                        tblwarehouse_product.localtion,
                        tblwarehouse_product.import_id,
                        tblwarehouse_product.product_id,
                        tblwarehouse_product.type_items,
                        tblwarehouse_product.lot_code,
                        tblwarehouse_product.date_sx,
                        tblwarehouse_product.date_sd,
                        tblwarehouse_product.date_use
                        FROM `tblwarehouse_product`
                        WHERE type_export = 2
                        GROUP BY `warehouse_id`,`localtion`,`import_id`,`product_id`,`type_items`,lot_code,date_sx,date_sd,date_use
                    ) tb_warehouse_product";

                    $tbTranfer = "(
                        SELECT
                            tbltransfer_warehouse_detail.order_id_item as order_id_item,
                            SUM(tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) as quantity_hold
                        FROM tbltransfer_warehouse_detail
                        JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                        LEFT JOIN $tb_warehouse_product ON tb_warehouse_product.warehouse_id = tbltransfer_warehouse_detail.warehouses_to
                        AND tb_warehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to
                        AND tb_warehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer
                        AND tb_warehouse_product.product_id = tbltransfer_warehouse_detail.id_items
                        AND tb_warehouse_product.type_items = tbltransfer_warehouse_detail.type
                        AND COALESCE(tb_warehouse_product.lot_code,-1) = COALESCE(tbltransfer_warehouse_detail.lot_code,-1)
                        AND COALESCE(tb_warehouse_product.date_sx,-1) = COALESCE(tbltransfer_warehouse_detail.date_sx,-1)
                        AND COALESCE(tb_warehouse_product.date_sd,-1) = COALESCE(tbltransfer_warehouse_detail.date_sd,-1)
                        AND COALESCE(tb_warehouse_product.date_use,-1) = COALESCE(tbltransfer_warehouse_detail.date_use,-1)
                        WHERE (tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) > 0
                        AND tbltransfer_warehouse.purchase_product_id = 0
                        AND tbltransfer_warehouse_detail.order_id_item = $order_item_id
                    )";
                    $dtTranferItemW = $this->db->query($tbTranfer)->row_array();

                    $keepTranferBusinessItem = '(
                        SELECT
                            SUM(tbl_tranfer_business_item.quantity) as quantity
                        FROM tbl_tranfer_business_item
                        WHERE tbl_tranfer_business_item.order_item_id = '.$order_item_id.'
                    )';
                    $dtTranferBu = $this->db->query($keepTranferBusinessItem)->row_array();

                    //new
                    $tbTranferNew = "(
                        SELECT
                            tbltransfer_warehouse_detail.order_id_item as order_id_item,
                            SUM(tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) as quantity_hold
                        FROM tbltransfer_warehouse_detail
                        JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                        LEFT JOIN $tb_warehouse_product ON tb_warehouse_product.warehouse_id = tbltransfer_warehouse_detail.warehouses_to
                        AND tb_warehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to
                        AND tb_warehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer
                        AND tb_warehouse_product.product_id = tbltransfer_warehouse_detail.id_items
                        AND tb_warehouse_product.type_items = tbltransfer_warehouse_detail.type
                        AND COALESCE(tb_warehouse_product.lot_code,-1) = COALESCE(tbltransfer_warehouse_detail.lot_code,-1)
                        AND COALESCE(tb_warehouse_product.date_sx,-1) = COALESCE(tbltransfer_warehouse_detail.date_sx,-1)
                        AND COALESCE(tb_warehouse_product.date_sd,-1) = COALESCE(tbltransfer_warehouse_detail.date_sd,-1)
                        AND COALESCE(tb_warehouse_product.date_use,-1) = COALESCE(tbltransfer_warehouse_detail.date_use,-1)
                        WHERE (tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) > 0
                        AND tbltransfer_warehouse.purchase_product_id != 0
                        AND tbltransfer_warehouse_detail.order_id_item = $order_item_id
                    )";
                    $dtTranferItemWNew = $this->db->query($tbTranferNew)->row_array();

                    $tbPurchaseProductError = "(
	                    SELECT
	                        tbl_tranfer_business_item.order_item_id as order_item_id,
	                            SUM(tbl_purchase_products.total_quantity) as quantity
	                    FROM tbl_purchase_products
	                    JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
	                    JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
	                    JOIN tbl_tranfer_business_item ON tbl_tranfer_business_item.business_plan_item_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'
	                    WHERE tbl_purchase_products.is_errors = 1 AND tbl_purchase_products.warehouseman_id != 0
	                    AND tbl_tranfer_business_item.order_item_id = $order_item_id
                    )";
                    $dtPurchaseProductError = $this->db->query($tbPurchaseProductError)->row_array();

                    $quantityPurchase = 0;
                    if (!empty($dtPurchaseProductError)){
                        $quantityPurchase = (float)$dtPurchaseProductError['quantity'];
                    }

                    $quantity_hold = 0;

                    if ($order_item['unit_id'] == $info['unit_id']) {
                        $quantity = $order_item['total_quantity_item'] * $exchange_stock;
                        $quantity_delivery = $quantity_delivery * $exchange_stock;
                    } else {
                        $quantity = $order_item['total_quantity_item'];
                    }

                    if ($quantityPurchase > 0 && ($quantity - $quantity_delivery - ((float)$dtTranferItemW['quantity_hold'] + (float)$dtTranferBu['quantity'])) <= 0){
                        $quantity_hold = $dtTranferItemWNew['quantity_hold'] + $dtTranferItemW['quantity_hold'];
                    } else {
                        $quantity_hold = (float)$dtTranferItemW['quantity_hold'] + (float)$dtTranferBu['quantity'];
                    }
                    //end

                    if (!empty($add_more)) {
                        $quantity_hold = 0;
                    }

                    // $quantity = $order_item['quantity'];

                    $quantityNeedHold = $quantity - $quantity_delivery - $quantity_hold;
                    if ($quantityNeedHold < 0) {
                        continue;
                    }

                    $type_item = $this->input->post('type_item')[$value];
                    $item_id = $item_id;
                    $type_item = $type_item;

                    $itemType = null;
                    if ($type_item == "products" || $type_item == "semi_products" || $type_item == "semi_products_outside") {
                        $itemType = "product";
                    } elseif ($type_item == "items") {
                        $itemType = "items";
                    } elseif ($type_item == "tools_supplies") {
                        $itemType = "tools";
                    }
                    if (empty($itemType)) {
                        continue;
                    }

                    $get_item = get_items($item_id, $itemType);

                    $tick = !empty($this->input->post('tick')[$value]) ? $this->input->post('tick')[$value] : null;

                    if (!empty($tick)) {
                        $totalQuantityCorrdinator = 0;
                        foreach ($tick as $k => $val) {
                            $warehousesLocation = explode('__', $val);
                            $warehouse_id = $warehousesLocation[0];
                            $location_id = $warehousesLocation[1];
                            $quantityCoordinator = number_unformat($this->input->post('quantity_coordinator')[$value][$k]);
                            $date_sx = ($this->input->post('date_sx')[$value][$k]);
                            $date_sd = ($this->input->post('date_sd')[$value][$k]);
                            $date_use = ($this->input->post('date_use')[$value][$k]);
                            $lot_code = ($this->input->post('lot_code')[$value][$k]);

                            $date_sx_check = !empty($date_sx) ? to_sql_date($date_sx) : null;
                            $date_sd_check = !empty($date_sd) ? to_sql_date($date_sd) : null;
                            $date_use_check = !empty($date_use) ? $date_use : null;
                            $lot_code_check = !empty($lot_code) ? $lot_code : null;

                            $this->db->select('
                                tbllocaltion_warehouses.name as name_location,
                                tblwarehouse.name as name_warehouse,
                                SUM(tblwarehouse_items.product_quantity) as product_quantity
                            ', false);
                            $this->db->from('tblwarehouse_items');
                            $this->db->join(
                                'tbllocaltion_warehouses',
                                'tbllocaltion_warehouses.id = tblwarehouse_items.localtion'
                            );
                            $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_items.warehouse_id');
                            $this->db->where('tblwarehouse_items.id_items', $item_id);
                            $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
                            $this->db->where('tblwarehouse_items.type_items', $itemType);
                            $this->db->where('tblwarehouse_items.localtion', $location_id);
                            $this->db->where('tblwarehouse_items.date_sx', $date_sx_check);
                            $this->db->where('tblwarehouse_items.date_sd', $date_sd_check);
                            $this->db->where('tblwarehouse_items.date_use', $date_use_check);
                            $this->db->where('tblwarehouse_items.lot_code', $lot_code_check);
                            $dtWarehouse = $this->db->get()->row_array();

                            $quantityWarehouse = $dtWarehouse['product_quantity'];

                            if (empty($quantityCoordinator)) {
                                $data['result'] = 0;
                                $data['message'] = lang('Số lượng giữ phải lớn hơn 0');
                                echo json_encode($data); die;
                                continue;
                            }

                            if ($quantityCoordinator > $quantityWarehouse) {
                                $errors .= '<div>Kho hàng [' . $dtWarehouse['name_warehouse'] . '] vị trí [' . $dtWarehouse['name_location'] . ']] số lượng  giữ kho nhỏ hơn [' . $dtWarehouse['product_quantity'] . ']</div>';
                                continue;
                            }

                            $locations = get_table_where('tbllocaltion_warehouses', array(
                                'warehouse' => WAREHOUSES_HOLD,
                                'order_id' => $id,
                                'tranfer_business_id' => 0,
                            ), '', 'row');
                            if (empty($locations)) {
                                $orderCheck = get_table_where(
                                    'tbl_orders',
                                    array('id' => $id),
                                    '',
                                    'row'
                                );
                                $in_local = array();
                                $in_local['name'] = $orderCheck->reference_no;
                                $in_local['code'] = $orderCheck->reference_no;
                                $in_local['name_parent'] = $orderCheck->reference_no;
                                $in_local['warehouse'] = WAREHOUSES_HOLD;
                                $in_local['child'] = 1;
                                $in_local['create_by'] = get_staff_user_id();
                                $in_local['date_create'] = date('Y-m-d H:i:s');
                                $in_local['status'] = 0;
                                $in_local['lever'] = 1;
                                $in_local['productions_plan_id'] = 0;
                                $in_local['pod_id'] = 0;
                                $in_local['stage_id'] = 0;
                                $in_local['stage_id_import_outsource'] = 0;
                                $in_local['order_item_id'] = 0;
                                $in_local['order_id'] = $id;
                                $this->db->insert('tbllocaltion_warehouses', $in_local);
                                $location_to = $this->db->insert_id();
                            } else {
                                $location_to = $locations->id;
                            }

                            $amountTranfer = $get_item->price * $quantityCoordinator;

                            $quantity_unit = 0;
                            $quantity_stock = 0;
                            if ($unit_id == $info['unit_id']) {
                                // $quantity_unit = $quantityCoordinator;
                                // $quantity_stock = roundNumberFormat($quantityCoordinator * $exchange_stock);
                                $quantity_stock = $quantityCoordinator;
                                $quantity_unit = roundNumberFormat($quantity_stock / $exchange_stock, 0);
                            } else {
                                // $quantity_unit = roundNumberFormat($quantityCoordinator / $exchange_stock);
                                // $quantity_stock = $quantityCoordinator;
                                $quantity_stock = $quantityCoordinator;
                                $quantity_unit = roundNumberFormat($quantity_stock / $exchange_stock, 0);
                            }

                            $str_item_id = $item_id . '__' . $type_item . '__' . $warehouse_id . '__' . $location_id;
                            if (!empty($arr_info[$str_item_id])) {
                                // $arr_info[$str_item_id]['quantity'] = $arr_info[$str_item_id]['quantity'] + $quantityCoordinator;
                                $arr_info[$str_item_id]['quantity'] = $arr_info[$str_item_id]['quantity'] + $quantity_stock;
                            } else {
                                $arr_info[$str_item_id] = [
                                    'item_id' => $item_id,
                                    'type_item' => $type_item,
                                    'type_warehouse' => $itemType,
                                    'warehouse_id' => $warehouse_id,
                                    'location_id' => $location_id,
                                    'item_code' => $get_item->name,
                                    'item_name' => $get_item->code,
                                    // 'quantity' => $quantityCoordinator,
                                    'quantity' => $quantity_stock,
                                ];
                            }

                            $tranferItems[] = array(
                                'order_id_item' => $order_item_id,
                                'id_items' => $item_id,
                                'quantity' => $quantityCoordinator,
                                'quantity_net' => $quantityCoordinator,
                                'type' => $itemType,
                                'note' => '',
                                'warehouses_to' => WAREHOUSES_HOLD,
                                'warehouses_id' => $warehouse_id,
                                'localtion_id' => $location_id,
                                'localtion_to' => $location_to,
                                'price' => $get_item->price,
                                'amount' => $amountTranfer,
                                'quantity_unit' => $quantity_unit,
                                'quantity_stock' => $quantity_stock,
                                'quantity_payment' => $quantityCoordinator,
                                'exchange_unit' => $exchange_unit,
                                'exchange_stock' => $exchange_stock,
                                'exchange_payment' => $exchange_payment,
                                'date_sx' => !empty($date_sx) ? to_sql_date($date_sx) : null,
                                'date_sd' => !empty($date_sd) ? to_sql_date($date_sd) : null,
                                'date_use' => !empty($date_use) ? $date_use : null,
                                'lot_code' => !empty($lot_code) ? $lot_code : null,
                                'unit_id' => $unit_id,
                            );


                            $totalQuantityCorrdinator += $quantityCoordinator;
                            $totalWarehouse += $quantityCoordinator;
                            $total += $amountTranfer;
                        }

                        //Kiểm tra số lượng cần giữ hàng
                        if ($totalQuantityCorrdinator > $quantityNeedHold) {
                            $errors .= '<div>' . lang('Thành phẩm') . ' ' . $get_item->name . ' số lượng giữ hàng phải <= ' . formatNumber($quantityNeedHold) . '</div>';
                            continue;
                        }
                    }
                }

                if (empty($tranferItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có mặt hàng để thêm');
                    echo json_encode($data);
                    die;
                }

                // print_arrays($tranferItems);
                foreach ($arr_info as $key => $value) {
                    $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
                    $this->db->from('tblwarehouse_items');
                    $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
                    $this->db->where('tblwarehouse_items.id_items', $value['item_id']);
                    $this->db->where('tblwarehouse_items.type_items',  $value['type_warehouse']);
                    $this->db->where('tblwarehouse_items.warehouse_id',  $value['warehouse_id']);
                    $this->db->where('tblwarehouse_items.localtion', $value['location_id']);
                    $quantity_warehouse = $this->db->get()->row_array()['total_quantity'];
                    $quantity = $value['quantity'];
                    if ($quantity > $quantity_warehouse) {
                        $errors .= '<div>Mặt hàng [' . $value['item_name'] . '] không đủ số lượng giữ trong kho</div>';
                        continue;
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                $statusTransfer = 2;
                $staffIdTransfer = get_staff_user_id();
                $dateTransfer = date('Y-m-d H:i:s');
                $history_status = '|' . $staffIdTransfer . ',' . $dateTransfer;

                $transfer = array(
                    'code' => sprintf('%06d', ch_getMaxID('id', 'tbltransfer_warehouse') + 1),
                    'prefix' => get_option('prefix_transfer'),
                    'note' => $note,
                    'warehouse_id' => 0,
                    'warehouse_to' => 0,
                    'date' => date('Y-m-d'),
                    'staff_id' => get_staff_user_id(),
                    'date_create' => date('Y-m-d H:i:s'),
                    'status' => 2,
                    'history_status' => '|' . get_staff_user_id() . ',' . date('Y-m-d H:i:s'),
                    'total' => $total,
                    'order_id_new' => $id,
                    'order_id' => $id,
                );
                $this->db->insert('tbltransfer_warehouse', $transfer);
                $transfer_id = $this->db->insert_id();
                if ($transfer_id) {
                    foreach ($tranferItems as $k => $v) {
                        $v['id_transfer'] = $transfer_id;
                        $this->db->insert('tbltransfer_warehouse_detail', $v);
                        $ins = $this->db->insert_id();
                        if ($ins) {
                            $order_item = get_table_where('tbl_order_items', array(
                                'id' => $v['order_id_item'],
                            ), '', 'row');
                            $quantity_hold = $order_item->quantity_condition + $v['quantity_net'];
                            $this->db->update(
                                'tbl_order_items',
                                array('quantity_condition' => $quantity_hold),
                                array('id' => $order_item->id)
                            );
                        }
                    }


                    if (!test_quantity_tranfer($transfer_id)) {
                    } else {
                        $dataTransfer = array(
                            'warehouseman_id' => $staffIdTransfer,
                            'warehouseman_date' => $dateTransfer,
                        );
                        $this->db->update('tbltransfer_warehouse', $dataTransfer, array('id' => $transfer_id));
                        $this->transfer_model->increaseTranfersWarehouse($transfer_id);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'orders',
                        'table_obj' => 'tbl_orders',
                        'id_obj' => $id,
                        'name_obj' => $order['reference_no'],
                        'content' => lang('Giữ kho ') . ' [' . $order['reference_no'] . ']',
                        'actions' => 'keep_stock_orders',
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng chọn dòng giữ kho ?');
            }
            echo json_encode($data);
            die;
        }

        $items = [];

        $tb_warehouse_product = "(
            SELECT SUM(tblwarehouse_product.quantity_export) as quantity_export,
            tblwarehouse_product.warehouse_id,
            tblwarehouse_product.localtion,
            tblwarehouse_product.import_id,
            tblwarehouse_product.product_id,
            tblwarehouse_product.type_items,
            tblwarehouse_product.lot_code,
            tblwarehouse_product.date_sx,
            tblwarehouse_product.date_sd,
            tblwarehouse_product.date_use
            FROM `tblwarehouse_product`
            WHERE type_export = 2
            GROUP BY `warehouse_id`,`localtion`,`import_id`,`product_id`,`type_items`,lot_code,date_sx,date_sd,date_use
        ) tb_warehouse_product";

        $tbTranfer = "(
            SELECT
                tbltransfer_warehouse_detail.order_id_item as order_id_item,
                SUM(tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) as quantity
            FROM tbltransfer_warehouse_detail
            JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
            LEFT JOIN $tb_warehouse_product ON tb_warehouse_product.warehouse_id = tbltransfer_warehouse_detail.warehouses_to
            AND tb_warehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to
            AND tb_warehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer
            AND tb_warehouse_product.product_id = tbltransfer_warehouse_detail.id_items
            AND tb_warehouse_product.type_items = tbltransfer_warehouse_detail.type
            AND COALESCE(tb_warehouse_product.lot_code,-1) = COALESCE(tbltransfer_warehouse_detail.lot_code,-1)
            AND COALESCE(tb_warehouse_product.date_sx,-1) = COALESCE(tbltransfer_warehouse_detail.date_sx,-1)
            AND COALESCE(tb_warehouse_product.date_sd,-1) = COALESCE(tbltransfer_warehouse_detail.date_sd,-1)
            AND COALESCE(tb_warehouse_product.date_use,-1) = COALESCE(tbltransfer_warehouse_detail.date_use,-1)
            WHERE (tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) > 0
            AND tbltransfer_warehouse.purchase_product_id = 0
            GROUP BY tbltransfer_warehouse_detail.order_id_item
        ) tb_tranfer";

        $tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) + SUM(tbl_delivery_items.quantity_loss) + SUM(tbl_delivery_items.quantity_sample) as quantity_delivery
            FROM tbl_delivery_items
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery";

        $keepTranferBusinessItem = 'COALESCE((
            SELECT
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            WHERE tbl_tranfer_business_item.order_item_id = tbl_order_items.id
        ), 0)';


        $slKeep = "COALESCE((
            SELECT SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id AND tbltransfer_warehouse_detail.tranfer_business_item_id = 0
        ), 0)";

        // coalesce(tb_tranfer.quantity,0) + '.$keepTranferBusinessItem.' as quantity_condition,
        // tbl_order_items.total_quantity_item as quantity,

        $tbPurchaseProductError = "(
            SELECT
                tbl_tranfer_business_item.order_item_id as order_item_id,
                SUM(tbl_purchase_products.total_quantity) as quantity
            FROM tbl_purchase_products
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
            JOIN tbl_tranfer_business_item ON tbl_tranfer_business_item.business_plan_item_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'
            WHERE tbl_purchase_products.is_errors = 1 AND tbl_purchase_products.warehouseman_id != 0
            GROUP BY tbl_tranfer_business_item.order_item_id
        ) tb_purchase_product_error";

        $tbTranferNew = "(
            SELECT
                tbltransfer_warehouse_detail.order_id_item as order_id_item,
                SUM(tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) as quantity
            FROM tbltransfer_warehouse_detail
            JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
            LEFT JOIN $tb_warehouse_product ON tb_warehouse_product.warehouse_id = tbltransfer_warehouse_detail.warehouses_to
            AND tb_warehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to
            AND tb_warehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer
            AND tb_warehouse_product.product_id = tbltransfer_warehouse_detail.id_items
            AND tb_warehouse_product.type_items = tbltransfer_warehouse_detail.type
            AND COALESCE(tb_warehouse_product.lot_code,-1) = COALESCE(tbltransfer_warehouse_detail.lot_code,-1)
            AND COALESCE(tb_warehouse_product.date_sx,-1) = COALESCE(tbltransfer_warehouse_detail.date_sx,-1)
            AND COALESCE(tb_warehouse_product.date_sd,-1) = COALESCE(tbltransfer_warehouse_detail.date_sd,-1)
            AND COALESCE(tb_warehouse_product.date_use,-1) = COALESCE(tbltransfer_warehouse_detail.date_use,-1)
            WHERE (tbltransfer_warehouse_detail.quantity_net - COALESCE(tb_warehouse_product.quantity_export,0)) > 0
            AND tbltransfer_warehouse.purchase_product_id != 0
            GROUP BY tbltransfer_warehouse_detail.order_id_item
        ) tb_tranfer_new";

        $this->db->select('
            tbl_order_items.id as id,
            tbl_order_items.id as order_item_id,
            tbl_order_items.type_item as type_item,
            tbl_order_items.item_id as item_id,
            tbl_order_items.order_id as order_id,
            tbl_order_items.total_quantity_item as quantity,
            IF(tb_purchase_product_error.quantity > 0 AND ((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - (coalesce(tb_delivery.quantity_delivery,0) * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) -  (coalesce(tb_tranfer.quantity,0) + '.$keepTranferBusinessItem.')) <= 0,(coalesce(tb_tranfer.quantity,0) + coalesce(tb_tranfer_new.quantity,0)),coalesce(tb_tranfer.quantity,0) + '.$keepTranferBusinessItem.') as quantity_condition,
            coalesce(tb_delivery.quantity_delivery, 0) * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1) as quantity_delivery,
            tbl_order_items.unit_id as unit_id
        ', false);
        $this->db->from('tbl_order_items');
        $this->db->join("$tbTranfer", 'tb_tranfer.order_id_item = tbl_order_items.id', 'left');
        $this->db->join("$tbDelivery", 'tb_delivery.order_item_id = tbl_order_items.id', 'left');
        $this->db->join("$tbPurchaseProductError", 'tb_purchase_product_error.order_item_id = tbl_order_items.id', 'left');
        $this->db->join("$tbTranferNew", 'tb_tranfer_new.order_id_item = tbl_order_items.id', 'left');
        $this->db->join("tbl_products", 'tbl_products.id = tbl_order_items.item_id', 'inner');

        $this->db->where('tbl_order_items.order_id', $id);

        if (empty($add_more)) {
            $this->db->where('((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - (coalesce(tb_delivery.quantity_delivery,0) * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) -  IF(tb_purchase_product_error.quantity > 0 AND ((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - (coalesce(tb_delivery.quantity_delivery,0) * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) -  (coalesce(tb_tranfer.quantity,0) + '.$keepTranferBusinessItem.')) <= 0,(coalesce(tb_tranfer.quantity,0) + coalesce(tb_tranfer_new.quantity,0)),(coalesce(tb_tranfer.quantity,0) + '.$keepTranferBusinessItem.'))) > 0');
        }
        $items = $this->db->get()->result_array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $order_item_id = $value['order_item_id'];
                $type_item = $value['type_item'];
                $item_id = $value['item_id'];
                $quantity = $value['quantity'];
                $quantity_transfer = $value['quantity_condition'];
                $images = '';
                $info = null;
                $type_warehouse = '';
                $conversion_quantity_unit = 1;

                if (!empty($add_more)) {
                    $items[$key]['quantity_condition'] = 0;
                }

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($item_id);
                    // $unit = $this->unit_model->rowUnit($info['unit_id']);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }

                    if ($value['unit_id'] == $info['unit_id']) {
                        $conversion_quantity_unit = $info['conversion_quantity_unit'];
                    }

                    $type_warehouse = 'product';
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($item_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                    $type_warehouse = 'nvl';
                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['avatar'])) {
                        $images = base_url('uploads/tools_supplies/' . $info['images']);
                    }
                    $type_warehouse = 'tools';
                }
                $quantity_delivery = $value['quantity_delivery'];

                // $quantityMax = $quantity - $quantity_delivery - $quantity_transfer;
                // if ($quantityMax <= 0) {
                //     continue;
                // }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $this->db->select("
                    tblwarehouse_items.*,
                    (tblwarehouse_items.product_quantity) as product_quantity,
                    tbllocaltion_warehouses.name as name_local,
                    tblwarehouse.name as name_warehouse,
                    tblwarehouse_items.lot_code as lot_code,
                    tblwarehouse_items.date_sx as date_sx,
                    tblwarehouse_items.date_sd as date_sd,
                    tblwarehouse_items.date_use as date_use,
                    tbllocaltion_warehouses.pod_id as pod_id
                    ");
                $this->db->where('(tblwarehouse_items.product_quantity) > ', 0);
                $this->db->where('tblwarehouse_items.id_items', $item_id);
                $this->db->where('tblwarehouse_items.type_items', $type_warehouse);
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_HOLD);
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_TAMP);
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_ERRORS);
                $this->db->where('tbllocaltion_warehouses.stage_id', 0);
                $this->db->join(
                    'tbllocaltion_warehouses',
                    'tbllocaltion_warehouses.id = tblwarehouse_items.localtion and tbllocaltion_warehouses.warehouse = tblwarehouse_items.warehouse_id',
                    'left'
                );
                $this->db->join('tblwarehouse', 'tblwarehouse.id = tbllocaltion_warehouses.warehouse', 'left');
                $this->db->group_start();
                $this->db->where('tbllocaltion_warehouses.pod_id', 0);
                $this->db->or_where('exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan"
                )', false, false);
                $this->db->or_where('exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
                    WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "orders" AND tbl_orders.type_orders = 4
                )', false, false);
                $this->db->group_end();

                $this->db->group_by('tblwarehouse_items.warehouse_id, tblwarehouse_items.localtion,lot_code,date_sx,date_sd,date_use');
                $warehouse = $this->db->get('tblwarehouse_items')->result_array();
                if (!empty($warehouse)) {
                    foreach ($warehouse as $kk => $vv) {
                        $warehouse[$kk]['date_sx'] = !empty($vv['date_sx']) ? _dhau($vv['date_sx']) : '';
                        $warehouse[$kk]['date_sd'] = !empty($vv['date_sd']) ? _dhau($vv['date_sd']) : '';
                        $warehouse[$kk]['lot_code'] = !empty($vv['lot_code']) ? ($vv['lot_code']) : '';
                        $warehouse[$kk]['date_use'] = !empty($vv['date_use']) ? ($vv['date_use']) : '';

                        $this->db->select('tbl_business_plan.reference_no as reference_no');
                        $this->db->from('tbl_productions_orders_details');
                        $this->db->join('tbl_business_plan','tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"');
                        $this->db->where('tbl_productions_orders_details.id', $vv['pod_id']);
                        $bussiness = $this->db->get()->row_array();

                        if (empty($bussiness)) {
                            $this->db->select('tbl_orders.reference_no as reference_no');
                            $this->db->from('tbl_productions_orders_details');
                            $this->db->join('tbl_orders','tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"');
                            $this->db->where('tbl_productions_orders_details.id', $vv['pod_id']);
                            $bussiness = $this->db->get()->row_array();
                        }

                        $warehouse[$kk]['bussiness'] = !empty($bussiness) ? ($bussiness['reference_no']) : '';
                    }
                }
                $items[$key]['warehouse'] = $warehouse;
                $items[$key]['name_item'] = $info['name'];
                $items[$key]['code_item'] = $info['code'];
                $items[$key]['images'] = $images;
                $items[$key]['dt_unit'] = $unit;
                $items[$key]['conversion_quantity_unit'] = $conversion_quantity_unit;
            }
        }

        $data['items'] = $items;
        $data['order'] = $order;
        $data['id'] = $id;
        $data['add_more'] = $add_more;
        $this->load->view('admin/orders/keep_stock', $data);
    }

    public function view_keep_warehouses($id)
    {
        $data['id'] = $id;
        $data['order'] = $this->orders_model->rowOrderById($id);
        $this->load->view('admin/orders/view_keep_warehouses', $data);
    }

    public function loadKeepOrders()
    {
        $transfer_id = $this->input->get('transfer_id');
        $data['transfer_id'] = $transfer_id;
        $this->load->view('admin/orders/load_keep_orders', $data);
    }

    public function getKeepOrders()
    {
        $order_id = $this->db->escape($this->input->post('order_id'));
        $aColumns = [
            'tbltransfer_warehouse.id as id',
            'tbltransfer_warehouse.date as date',
            'CONCAT(tbltransfer_warehouse.prefix, "-", tbltransfer_warehouse.code) as reference_no',
            'tbltransfer_warehouse.warehouseman_id as warehouseman_id',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
            'tbltransfer_warehouse.note as note',
            '5 as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbltransfer_warehouse';
        $where = [
            'AND tbltransfer_warehouse.order_id = ' . $order_id . '',
        ];
        $filter = [];
        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbltransfer_warehouse.staff_id',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $totalQuantityPrimary = 0;
        $totalQuantityNeed = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $transfer_id = $aRow['id'];
            $row[0] = '<div class="text-center"><a class="fa fa-caret-right font-size-20" onclick="rowChildKeepOrders(this, \'' . $transfer_id . '\')" href="javascript:void(0)"></a></div>';
            $row[1] = _d($aRow['date']);
            $row[2] = $aRow['reference_no'];

            $strStatus = '';
            if ($aRow['warehouseman_id'] > 0) {
                $strStatus = '<span class="label label-success">' . lang('ch_warehouse_d') . '</span>';
            } else {
                $strStatus = '<span class="label label-warning">' . lang('ch_warehouse_nd') . '</span>';
            }

            $styleDelete = '';
            $test_quantity = get_table_where(
                'tblwarehouse_product',
                array('import_id' => $transfer_id, 'quantity_export >' => 0, 'type_export ' => 2),
                '',
                'row'
            );
            if (!empty($test_quantity)) {
                $strStatus = '<span class="inline-block label label-danger" task-status-table="">Đã có xuất kho</span>';
                $styleDelete = 'pointer-events: none; opacity: 0.5;';
            }

            $row[3] = '<div class="text-center">' . $aRow['staff_name'] . '</div>';
            $row[4] = '<div class="text-center">' . $strStatus . '</div>';
            $row[5] = '<div class="">' . $aRow['note'] . '</div>';
            $row[6] = '<div class="text-center"><span class="btn btn-danger fa fa-trash" onclick="deleteTransferToOrders(' . $transfer_id . ')" style="' . $styleDelete . '"></span></div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function deleteTransferToOrders()
    {
        if (!has_permission('transfer', '', 'delete')) {
            $data['result'] = 0;
            $data['message'] = lang('ch_no_delete');
            echo json_encode($data);
            die;
        }

        $data['result'] = 0;
        $data['message'] = lang('fail');


        $transfer_id = $this->input->post('transfer_id');

        //tnh
        $this->db->select('tbl_outsource.*');
        $this->db->from('tbl_outsource');
        $this->db->where('tbl_outsource.tranfer_id', $transfer_id);
        $outsource = $this->db->get()->row_array();
        if (!empty($outsource) && $outsource['workflow'] > 1) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('Đã nhập gia công không thể xóa')
            ));
            die;
        }
        //

        $items = get_table_where(
            'tbltransfer_warehouse_detail',
            ['tbltransfer_warehouse_detail.id_transfer' => $transfer_id]
        );
        $test_quantity = get_table_where(
            'tblwarehouse_product',
            array('import_id' => $transfer_id, 'quantity_export >' => 0, 'type_export ' => 2),
            '',
            'row'
        );
        if (!empty($test_quantity)) {
            $data['result'] = 0;
            $data['message'] = lang('ch_quantity_export');
            echo json_encode($data);
            die;
        }
        if ($this->transfer_model->delete($transfer_id)) {

            //tnh
            if (!empty($outsource) && $outsource['workflow'] == 1) {
                $this->outsource_model->updateOutsource($outsource['id'], ['workflow' => 0, 'tranfer_id' => 0]);
            }
            //

            //dt
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    if ($value['order_id_item']) {
                        $order_item = get_table_where(
                            'tbl_order_items',
                            ['id' => $value['order_id_item']],
                            '',
                            'row_array'
                        );
                        $qty = $order_item['quantity_condition'] - $value['quantity_net'];

                        $this->db->where('id', $value['order_id_item']);
                        $this->db->update(
                            'tbl_order_items',
                            ['quantity_condition' => $qty]
                        );
                    }
                }
            }
            //

            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        echo json_encode($data);
    }

    public function exportExcelTemplate()
    {
        if ($this->input->post('export_excel')) {
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $code_import = $this->input->post('code_import');
            if (empty($code_import)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng nhập mã thành phẩm');
                echo json_encode($data);
                return;
            }

			$code_import = explode(',', $code_import);

            $this->db->select('tbl_products.*', false);
            $this->db->from('tbl_products');
            $this->db->where('tbl_products.code', $code_import[0]);
            $product = $this->db->get()->row_array();
            if (empty($product)) {
                $data['result'] = 0;
                $data['message'] = lang('Mã này không tồn tại');
                echo json_encode($data);
                return;
            }

			$this->db->select('GROUP_CONCAT(tbl_products_columns.columns_id  ORDER BY tbl_products_columns.columns_id ASC) as code_colums');
			$this->db->group_start();
			foreach($code_import as $key => $value) {
				$this->db->or_where('tbl_products.code', $value);
			}
			$this->db->group_end();
			$this->db->join('tbl_products_columns', 'tbl_products_columns.product_id = tbl_products.id', 'left');
			$this->db->group_by('tbl_products.id');
			$kt_products = $this->db->get('tbl_products')->result_array();

			$ktColums = !empty($kt_products[0]['code_colums']) ? $kt_products[0]['code_colums'] : '';
			foreach($kt_products as $key => $value) {
				if($ktColums != $value['code_colums']) {
					$data['result'] = 0;
					$data['message'] = lang('Columns ' . count($code_import) . ' thành phẩm Không khớp với nhau nên không thể tạo mẫu');
					echo json_encode($data);
					return;
				}
			}




            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            $productsColumns = $this->products_model->getProductsColumns($product['id']);


            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Ngày Giao');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Mã đơn đặt');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Chỉ lệnh');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'SL đặt');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Số lượng loss');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'SL làm mẫu');

            $row = 1;
            $kCs = 6;
            if (!empty($productsColumns)) {
                foreach ($productsColumns as $k => $val) {
                    $index = $cloumns_excel[$kCs] . $row;
                    $objPHPExcel->getActiveSheet()->SetCellValue($index, $val['name']);
                    $kCs++;
                }
            }

            $filename = lang('template_orders') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);

            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response =  array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function calOrdersNoti()
    {
        $this->db->select('tbl_orders.id');
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.status', 'un_approved');
        $orders = $this->db->get()->result_array();
        if (!empty($orders)) {
            foreach ($orders as $key => $value) {
                noti_custom('create_orders', $value['id'], get_staff_user_id(), 0, '', ['actions' => 'add']);
            }
        }
    }

    public function updatePricesOrders()
    {
        $this->db->select('
            tbl_orders.id,
            tbl_orders.table_price_id as table_price_id,
            tbl_orders.grand_total,
            tbl_orders.tax_rate as tax_rate,
            tbl_orders.customer_id as customer_id,
            tbl_orders.charge_party as charge_party,
            tbl_orders.cost_delivery as cost_delivery,
        ');
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.grand_total', 0);
        $this->db->where('tbl_orders.table_price_id >', 0);
        $this->db->limit(100);
        $orders = $this->db->get()->result_array();
        // print_arrays($orders);
        $arrOrders = [];
        $arrOrderItems = [];
        if (!empty($orders)) {
            foreach ($orders as $key => $value) {
                $order_id = $value['id'];

                $this->db->select('tbl_order_items.*', false);
                $this->db->from('tbl_order_items');
                $this->db->where('tbl_order_items.order_id', $order_id);
                $order_items = $this->db->get()->result_array();

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;

                $tax_name = 0;
                $tax_rate = $value['tax_rate'];
                $total_tax = 0;
                $customer_id = $value['customer_id'];
                $table_price_id = $value['table_price_id'];
                $charge_party = $value['charge_party'];
                $cost_delivery = $value['cost_delivery'];

                $isUpdateItems = false;
                if (!empty($order_items)) {
                    foreach ($order_items as $k => $v) {
                        $quantity = $v['quantity'];
                        $type = $v['type_item'];
                        if ($type == 'materials') {
                            $type = 'nvl';
                        } else if ($type == 'tools_supplies') {
                            $type = 'tools';
                        } else {
                            $type = 'product';
                        }

                        $product_id = $v['item_id'];
                        $moq = $quantity;

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
                            $isUpdateItems = true;

                            $amount = $quantity * $priceItem;
                            $grand_total_item = $amount;

                            $discount_percent_item = $v['discount_percent_item'];
                            $discount_percent_amount_item = 0;
                            if ($discount_percent_item > 0) {
                                $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                                $total_discount_percent_items += $discount_percent_amount_item;
                                $grand_total_item -= $discount_percent_amount_item;
                            }

                            $arrOrderItems[] = [
                                'id' => $v['id'],
                                'price' => $priceItem,
                                'amount' => $amount,
                                'discount_percent_amount_item' => $discount_percent_amount_item,
                                'discount_percent_amount_item' => $discount_percent_amount_item,
                                'total_amount' => $grand_total_item
                            ];

                            $total_amount_items += $amount;
                            $grand_total_items += $grand_total_item;
                        }
                    }
                }


                if ($isUpdateItems) {

                    $grand_total = $grand_total_items;
                    if ($tax_rate > 0) {
                        $total_tax = $grand_total * ($tax_rate / 100);
                    }
                    $grand_total += $total_tax;
                    if ($charge_party == "customer") {
                        $grand_total += $cost_delivery;
                    }

                    $arrOrders[] = [
                        'id' => $order_id,
                        'total_tax' => $total_tax,
                        'grand_total_items' => $grand_total_items,
                        'grand_total' => $grand_total,
                        'total_amount_items' => $total_amount_items,
                        'total_discount_percent_items' => $total_discount_percent_items
                    ];
                }
            }
        }

        if (!empty($arrOrderItems)) {
            $this->db->update_batch('tbl_order_items', $arrOrderItems, 'id');
        }

        if (!empty($arrOrders)) {
            $this->db->update_batch('tbl_orders', $arrOrders, 'id');
        }
    }


	public function print_orders_detail_html($id)
	{
		$font_size = get_option('pdf_font_size');
		if(!empty($font_size)) {
			$font_size = 'font-size:' . $font_size . 'px;';
		}
		else {
			$font_size = '';
		}
		ob_end_clean();
		echo '<style>
					#header, #nav, .noprint
					{
						display: none;
					}
                    @page {
                        size: landscape; /* hoặc size: landscape; */
                    }
			</style>';
		$data = [];
		$order = $this->orders_model->rowOrderById($id);
        $status_orders = $this->status_orders_model->getStatusOrdersById($order['status_orders']);
		$customer = $this->clients_model->rowCustomer($order['customer_id']);

		$contact = get_table_where('tblcontacts', ['userid' => $customer['userid']], '', 'row_array');

		$address_delivery = $this->site_model->rowShippingClient($order['address_delivery_id']);
		$employee = '';
		if (!empty($order['employee_id'])) {
			$employee = get_staff_full_name($order['employee_id']);
		}
		$items = $this->orders_model->getOrderItemsByOrderId($id);
		// $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
		// $data['title'] = lang('tnh_print_order');
		// $data['type'] = 'L';
		// $data['img'] = '';


		$company_logo = get_option('company_logo');
		$img = base_url('uploads/company/'.$company_logo);
//		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">';
		echo '<div  style="float: left;-webkit-print-color-adjust: exact;"><img  height="80px" src="'.$img.'"></div>';
		$html = '<div style="text-align: right">';
		$html .= '<span style="font-weight: bold; font-size: 13px; color: red;">'.get_option('invoice_company_name').'</span><br>';
		$html .= '<span style="font-size: 10px;">'._l('Địa chỉ').' : '.get_option('invoice_company_address').'</span><br>';
		$html .= '<span style="font-size: 10px;">'._l('Điện thoại').' : '.get_option('invoice_company_phonenumber').'</span> <span style="font-size: 9px;"> '._l('Fax').' : '.get_option('fax_company').'</span><br>';
		$html .= '<span style="font-size: 10px;">'._l('Email').' : '.get_option('email_company').'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">'._l('tnh_website').' : '.get_option('company_website').'</span><br>';
		$html .='</div>';
		echo '<div  style="float: right;-webkit-print-color-adjust: exact;">'.$html.'</div>';
		echo "<div class='clearfix'></div>";
		echo '<hr width="100%" style="margin-top: 10px;float:left;"/>';
		echo "<div class='clearfix'></div>";


		$bodyItems = '';
		$totalBox = 0;
		if (!empty($items)) {
			foreach ($items as $key => $value) {
				$type_item = $value['type_item'];
				$items_id = $value['item_id'];

				$info = null;
				if ($type_item == "products") {
					$info = $this->products_model->rowProduct($items_id);
					$unit = $this->unit_model->rowUnit($value['unit_id']);
				} else if ($type_item == "items") {
					$info = $this->items_model->rowItems($items_id);
					$unit = $this->unit_model->rowUnit($info['unit']);
				} else if ($type_item == "materials") {
					$info = $this->items_model->rowMaterial($items_id);
					$unit = $this->unit_model->rowUnit($info['unit_id']);
				}

				$tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
				$tdCode = '<td style="width: 17%;">' . $info['code'] . '</td>';
				$tdNameItem = '<td style="width: 19%;font-family: kozgopromedium;font-size:11px">' . $value['item_name'] . '</td>';
				$tdName = '<td style="width: 19%;font-family: kozgopromedium;font-size:11px">' . $value['product_name_customer'] . '</td>';
				$tdUnit = '<td class="text-center" style="width: 8%;">' . $unit['unit'] . '</td>';
				$tdUnitDelivery = '<td class="text-center" style="width: 8%;"></td>';
				$tdUnitExchange = '<td class="text-center" style="width: 8%;"></td>';
				$tdUnitPay = '<td class="text-center" style="width: 8%;"></td>';
				$tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['total_quantity_item']) . '</td>';

				$dtBox = $this->orders_model->getOrderItemExchangeBox($value['id']);

				$box = !empty($dtBox['total_quantity_exchange']) ? $dtBox['total_quantity_exchange'] : 0;
				if (!empty($box)) {
					$totalBox += $box;
				}

				$tdBox = '<td class="text-center" style="width: 8%;">' . formatNumber($box) . '</td>';

				$tdUnitPrice = '<td class="text-center" style="width: 12%;">' . formatMoney($value['price']) . '</td>';
				$tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
				$tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
				$tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';

				$dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
				$dateDelivery = '';
				if (!empty($dtDateDelivery)) {
					$dateDelivery = _dhau($dtDateDelivery['date_shipping']);
				}
				$tdDateDelivery = '<td class="text-center" style="width: 12%;">' . $dateDelivery . '</td>';


				$dateShip = '';
				$dtDateShip = get_table_where('tbl_orders_ship', ['order_item_id' => $value['id']]);
				if (!empty($dtDateShip)) {
					foreach($dtDateShip as $k => $v) {
						$dateShip .= _dhau($v['date']) .' - ' . formatNumber($v['quantity']) . '<br/>';
					}
				}
				$tdDateShip = '<td class="text-center" style="width: 12%;">' . $dateShip . '</td>';


				$typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
				$name_type_print = '';
				if (!empty($typePrint)) {
					$name_type_print = $typePrint['name'];
				}
				$tdType = '<td class="text-center" style="width: 13%;">' . $name_type_print . '</td>';

				$tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';

				$htmlOrderColumns = '';
				if ($type_item == "products") {
					$thSub = '';
					$trHtmlChild = '';
					$ct_counter_item = $value['ct_counter_item'];
					$productsColumns = $this->products_model->getProductsColumns($items_id);
					$orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
					$styleTd = '';
					if (!empty($productsColumns)) {
						$styleTd = 'width: ' . 72 / count($productsColumns) . '%';
						foreach ($productsColumns as $k => $v) {
							$thSub .= '<th class="text-center" style="' . $styleTd . '">' . $v['name'] . '</th>';
						}
					}
					$orderItemsColumnsNew = [];
					if ($ct_counter_item > 0) {
						for ($i = 0; $i < $ct_counter_item; $i++) {
							$arrNew = [];
							foreach ($productsColumns as $k => $v) {
								$columns_name = [];
								foreach ($orderItemsColumns as $kO => $vO) {
									if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
										$columns_name = [
											vn_to_str($vO['columns_value']) => $vO['columns_name']
										];
										break;
									}
								}
								$arrNew = array_merge($arrNew, $columns_name);
							}
							$orderItemsColumnsNew[$i] = $arrNew;
							$date_ship = '';
							$order_code = '';
							$command = '';
							$quantity_put = '';
							$quantity_loss = '';
							$sample_quantity_item = '';
							foreach ($orderItemsColumns as $kO => $vO) {
								if ($vO['columns_value'] == 'date_ship' && $i == $vO['counter_items_number']) {
									$date_ship = $vO['columns_name'];
									$orderItemsColumnsNew[$i]['date_ship'] = $date_ship;
									continue;
								}
								if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
									$order_code = $vO['columns_name'];
									$orderItemsColumnsNew[$i]['code'] = $order_code;
									continue;
								} else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
									$command = $vO['columns_name'];
									$orderItemsColumnsNew[$i]['command'] = $command;
									continue;
								} else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
									$quantity_put = $vO['columns_name'];
									$orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
									continue;
								} else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
									$quantity_loss = $vO['columns_name'];
									$orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
									continue;
								} else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
									$sample_quantity_item = $vO['columns_name'];
									$orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
									continue;
								}
							}
						}
					}
					$orderItemsColumnsNewVs1 = [];
					if (!empty($orderItemsColumnsNew)) {
						foreach ($orderItemsColumnsNew as $kkk => $vvv) {
							$columns_name_new = 'default';
							if (!empty($productsColumns)) {
								foreach ($productsColumns as $k => $v) {
									$name_check = vn_to_str($v['name']);
									if (!empty($vvv[$name_check])) {
										$columns_name_new .= $vvv[$name_check] . '__';
									}
								}
							}
							$columns_name_new = trim($columns_name_new, '__');
							$check_key = $columns_name_new;
							if (!empty($orderItemsColumnsNewVs1[$check_key])) {
								$orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
								$orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
								$orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
							} else {
								$orderItemsColumnsNewVs1[$check_key] = $vvv;
							}
						}
					}
					$ii = 1;
                    $totalQuantity_put = 0;
                    $totalQuantity_loss = 0;
                    $totalQuantityNew = 0;
                    $totalQuantityOld = 0;
                    $colspan = 0;
					if (!empty($orderItemsColumnsNewVs1)) {
						foreach ($orderItemsColumnsNewVs1 as $kk => $vv) {
//							$date_ship = $vv['date_ship'];
							$order_code = $vv['code'];
							$command = $vv['command'];
							$quantity_put = $vv['quantity_put'];
							$quantity_loss = $vv['quantity_loss'];
							$sample_quantity_item = $vv['sample_quantity_item'];
							$trHtmlColumns = '';
							$columns_name_new = '';
                            $colspan = (count($productsColumns));
							if (!empty($productsColumns)) {
								foreach ($productsColumns as $k => $v) {
									$name_check = vn_to_str($v['name']);
									if (!empty($vv[$name_check])) {
										$columns_name_new = $vv[$name_check];
										$trHtmlColumns .= '
                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">
                                            ' . $columns_name_new . '
                                        </td>
                                        ';
									} else {
                                        $columns_name_new = '';
										$trHtmlColumns .= '
                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">
                                            ' . $columns_name_new . '
                                        </td>
                                        ';
									}
								}
							}

							$tdOrderCode = '<td class="text-center">
                                ' . $order_code . '
                            </td>';

							$tdCommand = '<td class="text-center">
                                ' . $command . '
                            </td>';

							$tdQuantityPut = '<td class="text-center">
                                ' . formatNumber($quantity_put) . '
                            </td>';

							$tdQuantityLoss = '<td class="text-center">
                                ' . formatNumber($quantity_loss) . '
                            </td>';

							$tdSampleQuantityItem = '<td class="text-center">
                                ' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '
                            </td>';

							$tdQuantityOld = '<td class="text-center">
                                ' . (!empty($quantity_put + $quantity_loss + $sample_quantity_item) ? formatNumber($quantity_put + $quantity_loss + $sample_quantity_item) : '') . '
                            </td>';


                            $totalQuantity_put += $quantity_put;
                            $totalQuantityOld += $quantity_put + $quantity_loss + $sample_quantity_item;

							if (empty($trHtmlColumns) && empty($order_code)) continue;
							$stt =  $ii;
							$tdNumberChild = '<td class="text-center">' . $stt . '</td>';
							$trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $trHtmlColumns . '
                                ' . $tdQuantityPut . '
                                ' . $tdQuantityLoss . '
                                ' . $tdSampleQuantityItem . '
                                ' . $tdQuantityOld . '
                            </tr>';
							$ii++;
						}
					}
                    $trHtmlChild .= '<tr class="not-tr">
                                <td colspan="'.($colspan + 1).'">Tổng cộng</td>
                                <td style="text-align: center">'.formatNumber($totalQuantity_put).'</td>
                                <td></td>
                                <td></td>
                                <td style="text-align: center">'.formatNumber($totalQuantityOld).'</td>
                            </tr>';
					$htmlOrderColumns .= '<table class="" border="1" style="'.$font_size.'">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 4%">
                                        ' . lang('tnh_numbers') . '
                                    </th>
                                     ' . $thSub . '
                                    <th class="text-center" style="width: 12%">' . lang('tnh_quantity_put') . '</th>
                                    <th class="text-center" style="width: 12%">' . lang('tnh_quantity_loss') . '</th>
                                    <th class="text-center" style="width: 12%">' . lang('tnh_sample_quantity') . '</th>
                                    <th class="text-center" style="width: 12%">' . lang('Tổng số lượng') . '</th>
                                </tr>
                            </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        ';
				}

				$bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdNameItem . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdUnitDelivery . '
                    ' . $tdUnitExchange . '
                    ' . $tdUnitPay . '
                    ' . $tdQuantity . '
                    ' . $tdDateDelivery . '
                    ' . $tdDateShip . '
                    ' . $tdType . '
                    ' . $tdNote . '
                </tr>
                <tr>
                    <td colspan="8">
                        ' . $htmlOrderColumns . '
                    </td>
                </tr>
                ';
			}
		}

		$divAddress = !empty($address_delivery['address']) ? '<span>' . _l('tnh_address_delivery') . ': <span>' . $address_delivery['address'] . '</span></span><br>' : '';
		$divEmployeeCharge = !empty($employee) ? '<span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>' : '';
		$divNote = !empty($order['note']) ? '<span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>' : '';

		$day = date_format(date_create($order['date']), 'd');
		$month = date_format(date_create($order['date']), 'm');
		$year = date_format(date_create($order['date']), 'Y');
		$message = "";
		ob_start();
		stylePdf();
		$phoneContact = '';
		if (!empty($contact) && !empty($contact['phonenumber'])) {
			$phoneContact = ' (' . $contact['phonenumber'] . ')';
		}
		$typeOrder = get_table_where('tbl_type_orders', ['id' => $order['type_orders']], '', 'row_array');
		echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;" style="'.$font_size.'">
            <tr nobr="true">
            <td colspan="8" style="width: 90%;margin-left: 35px;"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('orders') . '</h1></td>
            
            <td style="width: 10%;">'.((!empty($status_orders)) ?  '<table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;border-style: soild;color: '.$status_orders['color'].';">
            <tr nobr="true"><td class="text-center uppercase">'.mb_strtoupper($status_orders['name'], 'UTF-8').'</td></tr></table>': '').'</td>
        </tr>
            </table>
            <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;" style="'.$font_size.'">
                <tr nobr="true">
                    <td style="width: 15%;"><b>' . _l('Nhóm Đơn Hàng') . '</b></td>
                    <td style="width: 35%;">:</td>
                    <td style="width: 15%;"><b>' . _l('date') . '</b></td>
                    <td style="width: 35%;">: ' . _d($order['date'], true) . '</td>
                </tr>
                <tr nobr="true">
                    <td><b>' . _l('tnh_reference_orders') . '</b></td>
                    <td>: ' . $order['reference_no'] . '</td>
                    <td><b>' . _l('customers') . '</b></td>
                    <td>: ' . $customer['company_short'] . '</td>
                </tr>
                <tr nobr="true">
                    <td><b>' . _l('Loại đơn hàng') . '</b></td>
                    <td>: ' . $typeOrder['name'] . '</td>
                    <td><b>' . _l('tnh_address_delivery') . '</b></td>
                    <td>: ' . $address_delivery['address'] . '</td>
                </tr>
                <tr nobr="true">
                    <td></td>
                    <td></td>
                    <td><b>' . _l('Người liên hệ') . '</b></td>
                    <td>: ' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</td>
                </tr>
                <tr nobr="true">
                    <td></td>
                    <td></td>
                    <td><b>' . _l('tnh_note') . '</b></td>
                    <td>: ' . $order['note'] . '</td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%; border-style: soild; border-color: black;'.$font_size.'">
                <tr nobr="true" style="background-color: #ddd;-webkit-print-color-adjust: exact;">
                    <td class="bold text-center" style="width: 6%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</td>
                    <td class="bold text-center" style="width: 17%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</td>
                    <td class="bold text-center" style="width: 19%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</td>
                    <td class="bold text-center" style="width: 19%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP (KH)') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('ĐV Giao Hàng') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Quy Đổi') . '</td>
                    <td class="bold text-center" style="width: 8%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('ĐV Thanh Toán') . '</td>
                    <td class="bold text-center" style="width: 10%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</td>
                    <td class="bold text-center" style="width: 12%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Ngày dk giao') . '</td>
                    <td class="bold text-center" style="width: 12%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Chi tiết giao hàng') . '</td>
                    <td class="bold text-center" style="width: 13%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('Loại hình in') . '</td>
                    <td class="bold text-center" style="width: 15%; border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</td>
                </tr>
                ' . $bodyItems . '
                <tr class="bold" nobr="true" style="background-color: #ddd;-webkit-print-color-adjust: exact;">
                    <th class="text-left" colspan="7">' . _l('tnh_total') . '</th>
                    <th></th>
                    <th class="text-center">' . formatNumber($order['grand_total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </table>
            <br><br>
            <table style="width: 100%" style="'.$font_size.'">
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td class="text-center">
                        <span class="bold">Người Giao</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Người Nhận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                    <td class="text-center">
                        <span class="bold">Trưởng Bộ Phận</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                    </td>
                </tr>
            </table>
        ';

		return $content = ob_get_contents();
		ob_end_clean();

		$data['content'] = $content;
        // var_dump($data['content']);die;
		echo $data['content'];die();
//		$data['pageCustome'] = 'orders_detail';
//		$pdf = @print_pdf_tnh_new($data);
//		$type = 'I';
//		$pdf->Output(slug_it('123') . '.pdf', $type);
	}


    public function update_date_ship($order_id = '') {
        if($this->input->post()) {
            $date_shipping = $this->input->post('date_shipping');
            $date_shipping = to_sql_date($date_shipping);
            $this->db->select('tbl_order_item_shippings.*');
            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
            $this->db->where('date_shipping is not null', false, false);
            $this->db->where('order_id', $order_id);
            $order_item_shippings = $this->db->get('tbl_order_item_shippings')->result_array();
            $count = 0;
            foreach($order_item_shippings as $key => $value) {
                $this->db->where('id', $value['id']);
                $success = $this->db->update('tbl_order_item_shippings', [
                    'date_shipping' => $date_shipping
                ]);

                if(!empty($success)) {
                    $this->db->select('tbl_productions_plan_details.*');
                    $this->db->where('tbl_productions_plan_items.type_object', 'orders');
                    $this->db->where('tbl_productions_plan_items.item_object_id', $value['order_item_id']);
                    $this->db->where('tbl_productions_plan_items.object_id', $order_id);
                    $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_plan_details.productions_plan_item_id');
                    $plan_details = $this->db->get('tbl_productions_plan_details')->result_array();
                    if(!empty($plan_details)) {
                        foreach($plan_details as $k => $v) {
                            $this->db->where('id', $v['id']);
                            $this->db->update('tbl_productions_plan_details', ['date' => $date_shipping]);
                        }
                    }

                    $count++;
                }
            }
            if(!empty($count)) {
                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => 'Cập nhật ngày giao hàng dự kiến thành công'
                ]);die();
            }
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Cập nhật ngày giao hàng dự kiến không thành công'
            ]);die();

        }
        else {
            $data = [];
            $data['order_id'] = $order_id;
            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_order_item_shippings.order_item_id');
            $this->db->where('date_shipping is not null', false, false);
            $this->db->where('order_id', $order_id);
            $data['date_shipping'] = $this->db->get('tbl_order_item_shippings')->row('date_shipping');
            $data['title'] = 'Cập nhật ngày giao hàng dự kiến';
            $this->load->view('admin/orders/update_date_ship', $data);
        }
    }

    public function getDetailColumsOrders()
    {
        $data = [];
        $dtSelect = $this->input->post('dtSelect');
        $order_item_id = $this->input->post('order_item_id');
        $arrId = [];
        if (!empty($dtSelect)) {
            foreach ($dtSelect as $key => $value) {
                $value = explode('__', $value);
                if (!empty($value)) {
                    foreach ($value as $kk => $vv) {
                        if ($kk == 0) {
                            continue;
                        }
                        $arrId[] = $vv;
                    }
                }
            }
        }
        $order_item = get_table_where('tbl_order_items', ['id' => $order_item_id], '', 'row_array');
        $type_item = $order_item['type_item'];
        $item_id = $order_item['item_id'];
        $orderItemsColumnsNew = [];
        if (!empty($arrId)) {
            if ($type_item == "products") {
                $thSub = '';
                $trHtmlChild = '';
                $ct_counter_item = $order_item['ct_counter_item'];
                $productsColumns = $this->products_model->getProductsColumns($item_id);

                $this->db->select('tbl_order_items_columns.*');
                $this->db->from('tbl_order_items_columns');
                $this->db->where('tbl_order_items_columns.order_item_id', $order_item['id']);
                $this->db->where_in('tbl_order_items_columns.counter_items_number', $arrId);
                $orderItemsColumns = $this->db->get()->result_array();
                $orderItemsColumnsNew = [];
                if ($ct_counter_item > 0) {
                    for ($i = 0; $i < $ct_counter_item; $i++) {
                        $arrNew = [];
                        foreach ($productsColumns as $k => $v) {
                            $columns_name = [];
                            foreach ($orderItemsColumns as $kO => $vO) {
                                if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                    $columns_name = [
                                        ($vO['columns_value']) => $vO['columns_name'],
                                    ];
                                    break;
                                }
                            }
                            $arrNew = array_merge($arrNew, $columns_name);
                        }
                        $orderItemsColumnsNew[$i] = $arrNew;
						
                        $order_code = '';
                        $command = '';
                        $quantity_put = '';
                        $quantity_loss = '';
                        $sample_quantity_item = '';
                        foreach ($orderItemsColumns as $kO => $vO) {

                            if ($i == $vO['counter_items_number']) {
                                $orderItemsColumnsNew[$i]['order_item_id'] = $vO['order_item_id'];
                                $orderItemsColumnsNew[$i]['order_id'] = $vO['order_id'];
                                $orderItemsColumnsNew[$i]['counter_item'] = $vO['counter_item'];
                                $orderItemsColumnsNew[$i]['columns_id'] = $vO['columns_id'];
                                $orderItemsColumnsNew[$i]['columns_id'] = $vO['columns_id'];
                                $orderItemsColumnsNew[$i]['counter_items_number'] = $vO['counter_items_number'];
                            }
							if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                $order_code = $vO['columns_name'];
                                $orderItemsColumnsNew[$i]['code'] = $order_code;
                                continue;
                            } elseif ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                $command = $vO['columns_name'];
                                $orderItemsColumnsNew[$i]['command'] = $command;
                                continue;
                            } elseif ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                $quantity_put = $vO['columns_name'];
                                $orderItemsColumnsNew[$i]['quantity_put_check'] = $quantity_put;

                                $this->db->select('SUM(columns_name) as quantity_put');
                                $this->db->from('tbl_delivery_items_columns');
                                $this->db->where('columns_value', $vO['columns_value']);
                                $this->db->where('order_item_id', $orderItemsColumnsNew[$i]['order_item_id']);
                                $this->db->where('order_id', $orderItemsColumnsNew[$i]['order_id']);
                                $this->db->where('counter_item', $orderItemsColumnsNew[$i]['counter_item']);
                                $this->db->where('columns_id', $orderItemsColumnsNew[$i]['columns_id']);
                                $this->db->where('counter_items_number',
                                    $orderItemsColumnsNew[$i]['counter_items_number']);
                                $dtColumDelivery = $this->db->get()->row_array()['quantity_put'];
                                $quantity_put = $quantity_put - $dtColumDelivery;
                                $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;

                                $this->db->select('COALESCE(SUM(columns_name),0) as quantity_loss_new');
                                $this->db->from('tbl_delivery_items_columns');
                                $this->db->where('columns_value', 'quantity_loss_new');
                                $this->db->where('order_item_id', $orderItemsColumnsNew[$i]['order_item_id']);
                                $this->db->where('order_id', $orderItemsColumnsNew[$i]['order_id']);
                                $this->db->where('counter_item', $orderItemsColumnsNew[$i]['counter_item']);
                                $this->db->where('columns_id', $orderItemsColumnsNew[$i]['columns_id']);
                                $this->db->where('counter_items_number',
                                    $orderItemsColumnsNew[$i]['counter_items_number']);
                                $dtColumDeliveryLossNew = $this->db->get()->row_array();
                                $dtColumDeliveryLoss = !empty($dtColumDeliveryLossNew) ? $dtColumDeliveryLossNew['quantity_loss_new'] : 0;
                                $orderItemsColumnsNew[$i]['quantity_loss_new'] = $dtColumDeliveryLoss;

                                continue;
                            } elseif ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                $quantity_loss = $vO['columns_name'];
                                $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                continue;
                            } elseif ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                $sample_quantity_item = $vO['columns_name'];
                                $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                continue;
                            }

                        }
                    }
                }
            }
        }
        $orderItemsColumnsNewVs1 = [];
        if (!empty($orderItemsColumnsNew)) {
            foreach ($orderItemsColumnsNew as $kk => $vv) {
                if (empty($vv)) {
                    continue;
                }
                if ($vv['quantity_put'] <= 0) {
                    continue;
                }
                $dtOrderItems = get_table_where('tbl_order_items', ['id' => $vv['order_item_id']], '', 'row_array');
                $quantity_loss_new_vs1 = $vv['quantity_put_check'] * $dtOrderItems['loss'] / 100;
                $vv['quantity_loss_new_vs1'] = $vv['quantity_loss'];

                $colum_delivery = [];
                $colum_delivery1 = '';
                $colum_delivery2 = '';
                if (!empty($order_item['colum_delivery1'])){
                    if(!empty($vv[$order_item['colum_delivery1']])){
                        $colum_delivery1 = $vv[$order_item['colum_delivery1']];
                        $colum_delivery[] = $vv[$order_item['colum_delivery1']];
                    }
                }
                if (!empty($order_item['colum_delivery2'])){
                    if(!empty($vv[$order_item['colum_delivery2']])){
                        $colum_delivery2 = $vv[$order_item['colum_delivery2']];
                        $colum_delivery[] = $vv[$order_item['colum_delivery2']];
                    }
                }
                $vv['colum_delivery1'] = $colum_delivery1;
                $vv['colum_delivery2'] = $colum_delivery2;
                $orderItemsColumnsNewVs1[$kk] = $vv;
                $orderItemsColumnsNewVs1[$kk]['colum_delivery'] = $colum_delivery;
                $orderItemsColumnsNewVs1[$kk]['json'] = (json_encode($vv));
            }
        }
        $data['orderItemsColumnsNew'] = array_values($orderItemsColumnsNewVs1);
        echo json_encode($data);
    }

	public function update_ship($id = '') {
		if(!empty($id)) {
			$date_ship = $this->input->post('date_ship');
			$quantity_ship = $this->input->post('quantity_ship');
			$quantity_ship = number_unformat($quantity_ship);

			$this->db->where('id', $id);
			$orders_ship = $this->db->get('tbl_orders_ship')->row();
			if(!empty($orders_ship)) {
				$this->db->select('sum(quantity) as sum_quantity');
				$this->db->where('order_item_id', $orders_ship->order_item_id);
				$quantity_all = $this->db->get('tbl_orders_ship')->row('sum_quantity');
				$quantity_all = !empty($quantity_all) ? $quantity_all : 0;
				$quantity_all += $quantity_ship;
				$quantity_all -= $orders_ship->quantity;


				$this->db->where('id', $orders_ship->order_item_id);
				$quantity_items = $this->db->get('tbl_order_items')->row('total_quantity_item');
				if($quantity_items < $quantity_all) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Số ngày của chi tiết giao hàng không được lớn hơn tổng số lượng'
					]);die();
				}
			}


			$this->db->where('id', $id);
			$success = $this->db->update('tbl_orders_ship', [
				'date' => to_sql_date($date_ship),
				'quantity' => number_unformat($quantity_ship)
			]);
			if(!empty($success)) {
				echo json_encode([
					'success' => true,
					'alert_type' => 'success',
					'message' => 'Cập nhật thành công',
					'date' => $date_ship,
					'quantity' => $quantity_ship
				]);die();
			}

			echo json_encode([
				'success' => false,
				'alert_type' => 'danger',
				'message' => 'Cập nhật không thành công'
			]);die();
		}
	}

    public function getKeepOrdersTT()
    {
        $order_id = $this->input->post('order_id');

        $aColumns = [
            'tbl_tranfer_business.id as id',
            'tbl_tranfer_business.date as date',
            'tbl_tranfer_business.reference_no as reference_no',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
            'tbl_tranfer_business.note as note',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_tranfer_business';
        $where = [
            'AND exists (
                SELECT tbl_tranfer_business_item.id
                FROM tbl_tranfer_business_item
                WHERE tbl_tranfer_business_item.order_id = '.$order_id.' AND tbl_tranfer_business_item.tranfer_business_id = tbl_tranfer_business.id
            ) ',
        ];
        $filter = [];
        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_tranfer_business.created_by',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        
        foreach ($rResult as $key => $aRow) {
            $start++;
            $transfer_id = $aRow['id'];
            $row[0] = '<div class="text-center"><a class="fa fa-caret-right font-size-20" onclick="rowChildKeepOrdersTT(this, \'' . $transfer_id . '\')" href="javascript:void(0)"></a></div>';
            $row[1] = _d($aRow['date']);
            $row[2] = $aRow['reference_no'];
            $row[3] = '<div class="text-center">' . $aRow['staff_name'] . '</div>';
            $row[4] = $aRow['note'];

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function loadKeepOrdersTT()
    {
        $transfer_id = $this->input->get('transfer_id');
        $order_id = $this->input->get('order_id');
        $data['transfer_id'] = $transfer_id;
        $data['order_id'] = $order_id;
        $this->load->view('admin/orders/load_keep_orders_tt', $data);
    }

    public function cancel($id, $is_end = 0)
    {
        if (!$this->perEditOrders) {
            accessDenied($js = true);
        }

        $data = [];
        $order = $this->orders_model->rowOrderById($id);

        if ($order['is_end'] == 1) {
            refererModel(lang('Đơn này đã được kết thúc đơn hàng'));
            die;
        } else if ($order['is_cancel'] == 1) {
            refererModel(lang('Đơn này đã được hủy'));
            die;
        }

        if ($this->input->post('save')) {

            $date_cancel = date('Y-m-d H:i:s');
            $note_cancel = $this->input->post('note_cancel', false);
            $is_cancel = 1;
            $user_cancel = get_staff_user_id();

            if (empty($note_cancel)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng nhập ghi chú hủy');
                echo json_encode($data); die;
            }

            $options = [
                'is_cancel' => $is_cancel,
                'is_end' => $is_end,
                'date_cancel' => $date_cancel,
                'user_cancel' => $user_cancel,
                'note_cancel' => $note_cancel,
            ];

            $up = $this->orders_model->updateOrdersNew($id, $options);
            
            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data); die;
        }
        $data['order'] = $order;
        $data['id'] = $id;
        $data['is_end'] = $is_end;
        $this->load->view('admin/orders/cancel', $data);
    }

    public function remove_cancel($id, $is_end = 0) {
        if (!$this->perEditOrders) {
            accessDenied($js = true);
        }

        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        if ($is_end == 1 && $order['is_end'] == 0) {
            refererModel(lang('Đơn này không thể bỏ kết thúc hoàn thành'));
            die;
        } else if ($is_end == 0 && $order['is_cancel'] == 0) {
            refererModel(lang('Đơn này không thể bỏ hủy'));
            die;
        }

        if ($this->input->post('save')) {

            $date_cancel = date('Y-m-d H:i:s');
            $user_cancel = get_staff_user_id();

            $options = [
                'is_cancel' => 0,
                'is_end' => 0,
                'date_cancel' => $date_cancel,
                'user_cancel' => $user_cancel,
                'note_cancel' => '',
            ];

            $up = $this->orders_model->updateOrdersNew($id, $options);
            
            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data); die;
        }

        $data['order'] = $order;
        $data['id'] = $id;
        $data['is_end'] = $is_end;
        $this->load->view('admin/orders/remove_cancel', $data);
    }

    public function import_orders_change()
    {
        if (!$this->perAddOrders) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            // if ($_FILES['userfile']['size'] > $this->allowed_file_size * 1024) {
            //     $this->session->set_flashdata('warning', lang('Không vượt quá '. $this->allowed_file_size. ' size'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            //     return;
            // }
            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 4; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 4;
            $index_parent = 0;
            $index_parent_items = 0;
            $ref = '';
            $refItem = '';
            $dataImport = [];
            $flagContinue = false;
            foreach ($arraydata as $key => $value) {
                // 0: reference: Số đơn hàng
                // 1: date: Ngày
                // 2: customer: Khách hàng
                // 3: person_contact: Người liên lạc
                // 4: address_delivery: Đỉa chỉ giao hàng
                // 5: id_branch: chi nhánh xưởng
                // 6: currencies: tiền tệ
                // 7: amount_to_vnd: Quy đổi VND
                // 8: type_orders: Loại đơn hàng
                // 9: type_items: Loại sản phẩm
                // 10: status_orders: Trạng thái đơn hàng
                // 11: employees: Nhân viên phụ trách
                // 12: tax: thuế
                // 13: cost_delivery: Chi phí giao hàng
                // 14: transporters: Nhà vận chuyển
                // 15: charge_party: Bên chịu phí
                // 16: note: Ghi chú tổng
                // 17: item_code: Mã thành phẩm
                // 18: item_name: Tên thành phẩm
                // 19: product_name_customer: Tên TP của khách hàng
                // 20: unit: đơn vị
                // 21: order_code: Mã đơn đặt
                // 22: command: Chỉ lệnh
                // 23: quantity_put: SL đặt
                // 24: quantity_loss: SL loss
                // 25: sample_quantity_item: SL làm mẫu
                // 26: total_quantity_item: tổng sl
                // 27: price: Đơn giá
                // 28: amount: Tổng tiền
                // 29: date_delivery: Ngày giao hàng
                // 30: detail_delivery: Chi tiết giao hàng (Ngày - SL ||)
                // 31: note_item: ghi chú mặt hàng
                // 32: so
                // 33: pi
                // 34: po_style
                // 35: item_code_tem

                if ($flagContinue) {
                    $flagContinue = false;
                    continue;
                }

                $reference = trim($value[0]);
                $date = $value[1];
                if (gettype($date) == 'double' || gettype($date) == 'int') {
                    $date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date));
                } else if (gettype($date) == 'string') {
                    $date = to_sql_date($date);
                }

                $customer = trim($value[2]);
                $person_contact = trim($value[3]);
                $address_delivery = trim($value[4]);
                $id_branch = trim($value[5]);
                $currencies = trim($value[6]);
                $amount_to_vnd = number_unformat($value[7]);
                $type_orders = trim($value[8]);
                $type_items = trim($value[9]);
                $status_orders = trim($value[10]);
                $employees = trim($value[11]);
                $tax = trim($value[12]);
                $cost_delivery = number_unformat($value[13]);
                $transporters = trim($value[14]);
                $charge_party = trim($value[15]);
                $discount_percent = 0;
                $discount_direct = 0;
                $note = trim($value[16]);
                $item_code = trim($value[17]);
                $item_name = trim($value[18]);
                $product_name_customer = trim($value[19]);
                $unit = trim($value[20]);
                $order_code = trim($value[21]);
                $command = trim($value[22]);
                $quantity_put = number_unformat($value[23]);
                $quantity_loss = number_unformat($value[24]);
                $sample_quantity_item = number_unformat($value[25]);
                $total_quantity_item = number_unformat($value[26]);
                $price = number_unformat($value[27]);
                $amount = number_unformat($value[28]);
                $date_delivery = $value[29];
                $detail_delivery = trim($value[30]);
                $note_item = trim($value[31]);
                $so = trim($value[32]);
                $pi = trim($value[33]);
                $po_style = trim($value[34]);
                $item_code_tem = trim($value[35]);

                if (!empty($reference) && $reference != $ref) {
                    $dataImport[$index_parent]['reference'] = $reference;
                    $dataImport[$index_parent]['date'] = $date;
                    $dataImport[$index_parent]['customer'] = $customer;
                    $dataImport[$index_parent]['person_contact'] = $person_contact;
                    $dataImport[$index_parent]['address_delivery'] = $address_delivery;
                    $dataImport[$index_parent]['id_branch'] = $id_branch;
                    $dataImport[$index_parent]['currencies'] = $currencies;
                    $dataImport[$index_parent]['amount_to_vnd'] = $amount_to_vnd;
                    $dataImport[$index_parent]['type_orders'] = $type_orders;
                    $dataImport[$index_parent]['type_items'] = $type_items;
                    $dataImport[$index_parent]['status_orders'] = $status_orders;
                    $dataImport[$index_parent]['employees'] = $employees;
                    $dataImport[$index_parent]['tax'] = $tax;
                    $dataImport[$index_parent]['cost_delivery'] = $cost_delivery;
                    $dataImport[$index_parent]['transporters'] = $transporters;
                    $dataImport[$index_parent]['charge_party'] = $charge_party;
                    $dataImport[$index_parent]['discount_percent'] = $discount_percent;
                    $dataImport[$index_parent]['discount_direct'] = $discount_direct;
                    $dataImport[$index_parent]['note'] = $note;
                    $dataImport[$index_parent]['so'] = $so;
                    $dataImport[$index_parent]['pi'] = $pi;
                    $dataImport[$index_parent]['po_style'] = $po_style;
                    $dataImport[$index_parent]['item_code_tem'] = $item_code_tem;

                    $refItem = '';
                    $parent_current = $index_parent;
                    $ref = $reference;
                    $index_parent++;
                }

                if (!empty($item_code) && $item_code != $refItem) {
                    $dataImport[$parent_current]['items'][$index_parent_items] = [
                        'item_code' => $item_code,
                        'item_name' => $item_name,
                        'product_name_customer' => $product_name_customer,
                        'unit' => $unit,
                        'price' => $price,
                        'amount' => $amount,
                        'date_delivery' => $date_delivery,
                        'detail_delivery' => $detail_delivery,
                        'total_quantity_item' => $total_quantity_item,
                        'note_item' => $note_item,
                    ];

                    $parent_current_item = $index_parent_items;
                    $refItem = $item_code;
                    $index_parent_items++;

                    //columns auto
                    $columnsAuto = [];
                    $keyCol = $key + 1;
                    for ($iStart = 26; $iStart < $highestColumnIndex; $iStart++) {
                        $_col = trim($arraydata[$keyCol][$iStart]);
                        if (empty($_col)) break;
                        $columnsAuto[$iStart] = $_col;
                    }
                    $dataImport[$parent_current]['items'][$parent_current_item]['columns'] = $columnsAuto;
                    $flagContinue = true;
                    continue;
                }

                if (!empty($order_code)) {

                    $arrDetail = [];

                    $arrDetail = [
                        'order_code' => $order_code,
                        'command' => $command,
                        'quantity_put' => $quantity_put,
                        'quantity_loss' => $quantity_loss,
                        'sample_quantity_item' => $sample_quantity_item,
                    ];

                    if (!empty($columnsAuto)) {
                        foreach ($columnsAuto as $kC => $vC) {
                            if ($kC > 25) {
                                $arrDetail[$vC] = trim($value[$kC]);
                            }
                        }
                    }

                    $dataImport[$parent_current]['items'][$parent_current_item]['detail'][] = $arrDetail;
                }
            }

            // print_arrays($dataImport);
            $listRef = [];
            if (!empty($dataImport)) {
                foreach ($dataImport as $key => $value) {
                    $date = $value['date'];
                    $reference_no = $value['reference'];
                    $_reference_no = getReference('orders');
                    if ($this->orders_model->checkExistOrders($_reference_no)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] không thể thêm vì đã tồn tại trong phần mềm</div>';
                        continue;
                    }

                    $customerName = $value['customer'];
                    $customer = $this->site_model->getClientByZcodeOrCompany($customerName);
                    if (empty($customer)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại trong phầm mềm</div>';
                        continue;
                    }
                    $customer_id = $customer['userid'];
                    $customer_name = $customer['company'];

                    $so = $value['so'];
                    $pi = $value['pi'];
                    $po_style = $value['po_style'];
                    $item_code_tem = $value['item_code_tem'];

                    //handling person contract
                    $person_contract = $value['person_contact'];
                    if (empty($person_contract)) {
                        $this->db->select('tblcontacts.id');
                        $this->db->from('tblcontacts');
                        $this->db->where('tblcontacts.userid', $customer_id);
                        $this->db->limit(1);
                        $contract = $this->db->get()->row_array();
                        $person_contact_id = !empty($contract['id']) ? $contract['id'] : 0;
                    } else {
                        $contract = $this->site_model->getContractByFirstName($person_contract, $customer_id);
                        if (empty($contract)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại người liên lạc [' . $person_contract . '] trong phầm mềm</div>';
                            continue;
                        }
                        $person_contact_id = $contract['id'];
                    }

                    //end

                    $str_id_branch = $value['id_branch'];
                    $id_branch = 0;
                    if (!empty($str_id_branch)) {
                        $dtBranch = $this->site_model->getBranchByName($str_id_branch);
                        if (empty($dtBranch)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì chi nhánh xưởng [' . $str_id_branch . '] không tồn tại trong phầm mềm</div>';
                            continue;
                        }
                        $id_branch = $dtBranch['id'];
                    }

                    $str_currencies = $value['currencies'];
                    $currencies = 0;
                    if (!empty($str_currencies)) {
                        $dtCurrencies = $this->site_model->getCurrenciesByName($str_currencies);
                        if (empty($dtCurrencies)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì tiền tệ [' . $str_currencies . '] không tồn tại trong phầm mềm</div>';
                            continue;
                        }
                        $currencies = $dtCurrencies['id'];
                    }

                    $amount_to_vnd = $value['amount_to_vnd'];
                    if (empty($amount_to_vnd)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì quy đổi VND [' . $amount_to_vnd . '] không nhập</div>';
                        continue;
                    }

                    $type_orders = $value['type_orders'];
                    if (!in_array($type_orders, [1, 2, 3, 4, 11, 12, 13, 14])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì loại đơn hàng không đúng định dạng [1, 2, 3, 4, 11, 12, 13, 14]</div>';
                        continue;
                    }

                    $type_items = $value['type_items'];
                    if (!in_array($type_items, [1, 2])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì loại sản phẩm không đúng định dạng [1, 2]</div>';
                        continue;
                    }

                    $status_orders = $value['status_orders'];
                    if (!in_array($status_orders, [1, 4, 5])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì trạng thái đơn hàng không đúng định dạng [1, 4, 5]</div>';
                        continue;
                    }

                    $addressDelivery = $value['address_delivery'];
                    $address_delivery_id = 0;
                    if (!empty($addressDelivery)) {
                        $address_delivery = $this->site_model->getShippingClientByClientAndAddress($customer_id, $addressDelivery);
                        if (empty($address_delivery)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại đỉa chỉ [' . $addressDelivery . '] trong phầm mềm</div>';
                            continue;
                        }
                        $address_delivery_id = $address_delivery['id'];
                    } else {
                        $this->db->select('
                            tblshipping_client.id
                        ', false);
                        $this->db->from('tblshipping_client');
                        $this->db->where('tblshipping_client.client', $customer_id);
                        $this->db->limit(1);
                        $shipping_client = $this->db->get()->row_array();
                        $address_delivery_id = !empty($shipping_client) ? $shipping_client['id'] : 0;
                    }

                    //handling employee
                    $employee = $value['employees'];
                    if (empty($employee)) {
                        // $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại nhân viên phụ trách</div>';
                        // continue;
                        $employeeId = get_staff_user_id();
                    }

                    if (!empty($employee)) {
                        $staffName = $employee;
                        $staff = $this->site_model->getStaffByName($staffName);
                        if (empty($staff)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhân viên phụ trách [' . $staffName . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $employeeId = $staff['staffid'];
                    }
                    //end employee

                    //handling tax
                    $tax = $value['tax'];
                    $tax_id = 0;
                    $tax_rate = 0;
                    $tax_name = 0;
                    if (!empty($tax)) {
                        $dTax = $this->site_model->getTaxesByName($tax);
                        if (empty($dTax)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $tax_id = $dTax['id'];
                        $tax_rate = $dTax['taxrate'];
                        $tax_name = $dTax['name'];
                    }
                    //end tax

                    //handling transporters
                    $transporters = $value['transporters'];
                    $transporter_id = 0;
                    if (!empty($transporters)) {
                        $transport = $this->site_model->getTransportByName($transporters);
                        if (empty($transport)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhà vận chuyển [' . $transporters . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $transporter_id = $transport['id'];
                    }
                    //end handling transporters

                    //charge party: Bên chịu phí
                    $charge_party = !empty($value['charge_party']) ? $value['charge_party'] : 1;
                    if (!in_array($charge_party, [1, 2])) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] vì bên chịu phí không đúng giá trị [1, 2]</div>';
                        continue;
                    }
                    $charge_party = ($charge_party == 1) ? 'company' : 'customer';
                    //

                    $items = $value['items'];
                    if (empty($items)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại mặt hàng</div>';
                        continue;
                    }

                    //handling items
                    $note = $value['note'];
                    $count_items = 0;
                    $total_quantity = 0;
                    $total_amount_items = 0;
                    $total_tax_items = 0;
                    $total_discount_percent_items = 0;
                    $total_discount_direct_items = 0;
                    $grand_total_items = 0;
                    $discount_percent = !empty($value['discount_percent']) ? $value['discount_percent'] : 0;
                    $total_discount_percent = 0;
                    $total_discount_direct = !empty($value['discount_direct']) ? $value['discount_direct'] : 0;
                    $cost_delivery = !empty($value['cost_delivery']) ? $value['cost_delivery'] : 0;
                    $grand_total = 0;
                    $status = 'un_approved';
                    $gift = 0;
                    $total_cost_temporary_capital = 0;
                    $total_profit_temporary_capital = 0;

                    $flagErrorsItems = false;
                    $itemsIn  = [];
                    $grand_total_quantity = 0;
                    $counter_item = 0;

                    $dtTablePrice = $this->orders_model->getGroupPriceCustomer($customer_id);
                    $table_price_id = !empty($dtTablePrice) ? $dtTablePrice['id'] : 0;
                    foreach ($items as $k => $val) {
                        $item_type = !empty($val['item_type']) ? $val['item_type'] : 1;
                        $item_code = trim($val['item_code']);
                        if (empty($item_code)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng bị trống</div>';
                            $flagErrorsItems = true;
                            break;
                        }
                        if (empty($item_code)) continue;
                        $loss = 0;
                        $type_item = "products";
                        $item = $this->site_model->getProductsByCode($item_code);
                        if (empty($item)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không tồn tại trong phần mềm</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $conversion_quantity_unit = 1;
                        $conversion_quantity_unit_default = 1;
                        $quantity_child_sheet = $item['quantity_child_sheet'];
                        $quantity_sheet_bale = $item['quantity_sheet_bale'];
                        $loss = $item['loss'];

                        $product_name_customer = $val['product_name_customer'];
                        if (empty($product_name_customer)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] chưa nhập tên TP của khách hàng</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $unit = $val['unit'];
                        $dtUnits = $this->unit_model->rowUnitByCode($unit, '*', 'where');
                        if (empty($dtUnits)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] đơn vị ['.$unit.'] chưa có trong phần mềm</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $unit_id = $dtUnits['unitid'];
                        if ($unit_id != $item['unit_id'] && $unit_id != $item['conversion_unit']) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có đơn vị tính ['.$unit.'] trong thành phẩm</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        if ($unit_id != $item['unit_id']) {
                            $conversion_quantity_unit = $item['conversion_quantity_unit'];
                        }

                        $arrCol = [];
                        $_columnsAuto = $val['columns'];
                        if (!empty($_columnsAuto)) {
                            foreach ($_columnsAuto as $_kC => $_vC) {
                                $this->db->select('tbl_columns_detail.*');
                                $this->db->from('tbl_products_columns');
                                $this->db->join('tbl_columns_detail', 'tbl_columns_detail.columns_id = tbl_products_columns.columns_id');
                                $this->db->where('tbl_products_columns.product_id', $item['id']);
                                $this->db->where('tbl_columns_detail.name', $_vC);
                                $is_products_columns = $this->db->get()->row_array();
                                if (empty($is_products_columns['id'])) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì  [' . $item_code . '] không có cột ['.$_vC.'] trong thành phẩm</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $arrCol[$_vC] = $is_products_columns['id'];
                            }
                        }

                        $ct_counter_item = 0;
                        $arrItemsChildColumns = [];
                        $counter_items_number = 0;
                        $quantity = 0;
                        $total_quantity_loss = 0;
                        $total_quantity_sample = 0;
                        $detail = !empty($val['detail']) ? $val['detail'] : null;
                        // print_arrays($detail);
                        if (empty($detail)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có các dòng mã đơn đặt</div>';
                            $flagErrorsItems = true;
                            break;
                        } else {
                            foreach ($detail as $kD => $vD) {
                                $order_code = $vD['order_code'];
                                if (empty($order_code)) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có mã đơn đặt bị trống</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $command = $vD['command'];
                                if (empty($command)) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có chỉ lệnh bị trống</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $quantity_put = $vD['quantity_put'];
                                if (empty($quantity_put)) {
                                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có số lượng đặt</div>';
                                    $flagErrorsItems = true;
                                    break;
                                }

                                $quantity_loss = roundNumberFormat($quantity_put * $loss/100, 0);
                                $sample_quantity_item = $vD['sample_quantity_item'];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $order_code,
                                    'columns_value' => 'order_code',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $command,
                                    'columns_value' => 'command',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $quantity_put,
                                    'columns_value' => 'quantity_put',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $quantity_loss,
                                    'columns_value' => 'quantity_loss',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $sample_quantity_item,
                                    'columns_value' => 'sample_quantity_item',
                                    'counter_items_number' => $counter_items_number,
                                ];

                                // print_arrays($_columnsAuto);
                                if (!empty($_columnsAuto)) {
                                    foreach ($_columnsAuto as $kCC => $vCC) {
                                        if (empty($vD[$vCC])) continue;
                                        $arrItemsChildColumns[] = [
                                            'counter_item' => $counter_item,
                                            'columns_id' => $arrCol[$vCC],
                                            'columns_name' => $vD[$vCC],
                                            'columns_value' => $vCC,
                                            'counter_items_number' => $counter_items_number,
                                        ];
                                    }
                                }

                                $quantity += $quantity_put;
                                $total_quantity_loss += $quantity_loss;
                                $total_quantity_sample += $sample_quantity_item;

                                $counter_items_number++;
                                $counter_item++;
                            }
                        }
                        // print_arrays($arrItemsChildColumns);
                        $ct_counter_item = $counter_items_number;
                        $sample_quantity =  $total_quantity_sample;
                        $item_id = $item['id'];
                        $items_code = $item['code'];
                        $items_name = $item['name'];
                        // $quantity = $val['quantity'];
                        $price = $val['price'];
                        if (empty($price) && !empty($table_price_id)) {
                            $price = $this->orders_model->getPriceCustomer($table_price_id, $customer_id, $item_id, 'product', $quantity);
							if($unit_id == $item['conversion_unit'] && !empty($item['conversion_quantity_unit'])) {
								$price = $price / $item['conversion_quantity_unit'];
							}
                        }

                        $note_item = $val['note_item'];
                        $amount = $quantity * $price;

                        $total_quantity_item = $sample_quantity + $quantity + $total_quantity_loss;
                        if ($type_orders == TYPE_PTM) {
                            if ($total_quantity_item < QUANTITY_PTM) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] loại phát triển mẫu có số lượng >= '.QUANTITY_PTM.'</div>';
                                $flagErrorsItems = true;
                                break;
                            }
                        }
                        
                        $grand_total_quantity += $total_quantity_item;

                        $sub = [];
                        $total_quantity_sub = 0;
                        $date_delivery = $val['date_delivery'];
                        if (!empty($date_delivery)) {
                            $date_shipping = $date_delivery;
                            if (gettype($date_shipping) == 'double' || gettype($date_shipping) == 'int') {
                                $date_shipping = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_shipping));
                            } else if (gettype($date_shipping) == 'string') {
                                $date_shipping = to_sql_date($date_shipping);
                            }

                            $sub[] = [
                                'date_shipping' => $date_shipping,
                                'quantity_shipping' => $total_quantity_item,
                            ];

                            // $date_delivery = explode("::", $date_delivery);
                            // foreach ($date_delivery as $i => $v) {
                            //     $v = explode("||", $v);
                            //     if (empty($v) || empty($v[0])) continue;
                            //     $date_shipping = to_sql_date($v[0]);
                            //     $quantity_sub = !empty($v[1]) ? number_unformat($v[1]) : 0;

                            //     $sub[] = [
                            //         'date_shipping' => $date_shipping,
                            //         'quantity_shipping' => $quantity_sub,
                            //     ];
                            //     $total_quantity_sub += $quantity_sub;
                            // }
                            // if ($total_quantity_sub > $quantity) {
                            //     $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được số lượng giao hàng dự kiến lớn hơn số lượng trong mặt hàng</div>';
                            //     $flagErrorsItems = true;
                            //     break;
                            // }
                        } else {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có ngày giao hàng dự kiến</div>';
                            $flagErrorsItems = true;
                            break;
                        }

                        $detail_delivery = $val['detail_delivery'];
                        $ship = [];
                        if (!empty($detail_delivery)) {
                            $detail_delivery = explode('||', $detail_delivery);
                            if (!empty($detail_delivery)) {
                                foreach ($detail_delivery as $kD => $vD) {
                                    $arr_detail_delivery = explode('-', $vD);
                                    $date_detail_delivery = trim($arr_detail_delivery[0]);
                                    if (empty($date_detail_delivery)) continue;
                                    $quantity_detail_delivery = number_unformat($arr_detail_delivery[1]);
                                    if (gettype($date_detail_delivery) == 'double' || gettype($date_detail_delivery) == 'int') {
                                        $date_detail_delivery = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_detail_delivery));
                                    } else if (gettype($date_detail_delivery) == 'string') {
                                        $date_detail_delivery = to_sql_date($date_detail_delivery);
                                    }

                                    $ship[] = [
                                        'date' => $date_detail_delivery,
                                        'quantity' => $quantity_detail_delivery,
                                    ];
                                }
                            }
                        }

                        $grand_total_item = $amount;
                        $tax_amount_item = 0;
                        $tax_name_item = '';
                        if (!empty($tax_name_item)) {
                            $info_tax = $this->site_model->getTaxesByName($tax_name_item);
                            if (empty($info_tax)) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax_name_item . '] không tồn tại trong phần mềm</div>';
                                break;
                            }
                            $tax_name_item = $info_tax['name'];
                            $tax_rate_item = $info_tax['taxrate'];
                            $tax_id_item = $info_tax['id'];
                        } else {
                            $tax_name_item = "0%";
                            $tax_rate_item = 0;
                            $tax_id_item = 0;
                        }

                        $discount_percent_item = 0;
                        $discount_percent_amount_item = 0;
                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                            $total_discount_percent_items += $discount_percent_amount_item;
                            $grand_total_item -= $discount_percent_amount_item;
                        }
                        $discount_direct_amount_item = 0;
                        $total_discount_direct_items += $discount_direct_amount_item;
                        $grand_total_item -= $discount_direct_amount_item;

                        //handling cost temporary capital
                        if ($type_item == "products") {
                            $itemType = "product";
                        } else if ($type_item == "items") {
                            $itemType = "items";
                        }
                        $result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $item_id);
                        $priceCost = 0;
                        $cQuantity = $quantity;
                        foreach ($result as $i => $v) {
                            if ($cQuantity <= 0) break;
                            $qty = $v['quantity_left'];
                            $p = $v['price'];

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
                            $rs = $this->site_model->getOrdersItemSellFirst($item_id, $type_item);
                            if (!empty($rs)) {
                                $priceCost += $rs['price'] * $cQuantity;
                            } else {
                                $priceCost += $item['price_import'] * $cQuantity;
                            }
                        }

                        //end handling cost temporary capital
                        $cost_temporary_capital = $priceCost;
                        $profit_temporary_capital = $grand_total_item - $priceCost;

                        if ($tax_rate_item > 0) {
                            $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                            $total_tax_items += $tax_amount_item;
                            $grand_total_item += $tax_amount_item;
                        }

                        $itemsIn[] = [
                            'quantity_loss' => $total_quantity_loss,
                            'sample_quantity' => $sample_quantity,
                            'total_quantity_item' => $total_quantity_item,

                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'tax_id_item' => $tax_id_item,
                            'tax_name_item' => $tax_name_item,
                            'tax_rate_item' => $tax_rate_item,
                            'tax_amount_item' => $tax_amount_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'note_item' => $note_item,
                            'cost_temporary_capital' => $cost_temporary_capital,
                            'profit_temporary_capital' => $profit_temporary_capital,
                            'quantity_child_sheet' => $quantity_child_sheet,
                            'quantity_sheet_bale' => $quantity_sheet_bale,
                            'sub' => $sub,
                            'arrItemsChildColumns' => $arrItemsChildColumns,
                            'ct_counter_item' => $ct_counter_item,
                            'hand_input_price' => 1,
                            'loss' => $loss,
                            'product_name_customer' => $product_name_customer,
                            'ship' => $ship,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                            'conversion_quantity_unit_default' => $conversion_quantity_unit_default
                        ];

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                        $total_cost_temporary_capital += $cost_temporary_capital;
                    }

                    // print_arrays($itemsIn, $errors);
                    if ($flagErrorsItems) continue;

                    if (empty($itemsIn)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không có mặt hàng</div>';
                        continue;
                    }

                    $count_items = count($itemsIn);
                    $grand_total = $grand_total_items;

                    if ($discount_percent > 0) {
                        $total_discount_percent = $grand_total * ($discount_percent / 100);
                    }
                    $grand_total -= $total_discount_percent;
                    $grand_total -= $total_discount_direct;

                    $total_profit_temporary_capital = $grand_total - $total_cost_temporary_capital;
                    $total_profit_temporary_capital -= $cost_delivery;

                    $total_tax = 0;
                    if ($tax_rate > 0) {
                        $total_tax = $grand_total * ($tax_rate / 100);
                    }

                    $grand_total += $total_tax;
                    if ($charge_party == "customer") {
                        $grand_total += $cost_delivery;
                    } else {
                        //công ty
                    }

                    $options = [
                        'date' => $date,
                        // 'reference_no' => $reference_no,
                        'reference_no' => $_reference_no,
                        'customer_id' => $customer_id,
                        'customer_name' => $customer_name,
                        'address_delivery_id' => $address_delivery_id,
                        'employee_id' => $employeeId,
                        'note' => $note,
                        'count_items' => $count_items,
                        'total_quantity' => $total_quantity,
                        'total_amount_items' => $total_amount_items,
                        'total_tax_items' => $total_tax_items,
                        'total_discount_percent_items' => $total_discount_percent_items,
                        'total_discount_direct_items' => $total_discount_direct_items,
                        'grand_total_items' => $grand_total_items,
                        'tax_id' => $tax_id,
                        'tax_name' => $tax_name,
                        'tax_rate' => $tax_rate,
                        'total_tax' => $total_tax, //tổng thuế
                        'discount_percent' => $discount_percent, //% chiết khấu
                        'total_discount_percent' => $total_discount_percent, //tổng tiền chiết khấu phần trăm
                        'total_discount_direct' => $total_discount_direct, //tổng tiền chiết khấu tiền mặt
                        'grand_total' => $grand_total, //tổng tiền đơn hàng
                        'status' => $status,
                        'date_created' => date('Y-m-d H:i:s'),
                        'created_by' => get_staff_user_id(),
                        'table_price_id' => $table_price_id,
                        'table_discount_id' => 0,
                        'cost_delivery' => $cost_delivery,
                        'gift' => $gift,
                        'transporter_id' => $transporter_id,
                        'charge_party' => $charge_party,
                        'person_contact_id' => $person_contact_id,
                        'total_cost_temporary_capital' => $total_cost_temporary_capital, //giá vốn tạm thời
                        'total_profit_temporary_capital' => $total_profit_temporary_capital, //chi phí lợi nhuận tạm thời
                        'id_branch' => $id_branch,
                        'currencies' => $currencies,
                        'amount_to_vnd' => $amount_to_vnd,
                        'type_orders' => $type_orders,
                        'status_orders' => $status_orders,
                        'type_items' => $type_items,
                        'grand_total_quantity' => $grand_total_quantity,
                        'so' => $so,
                        'pi' => $pi,
                        'po_style' => $po_style,
                        'item_code' => $item_code_tem,
                    ];

                    // print_arrays($options, $itemsIn);
                    $order_id = $this->orders_model->insertOrdersNew($options);
                    if ($order_id) {
                        if (getReference('orders') == $_reference_no) {
                            updateReference('orders');
                        }

                        foreach ($itemsIn as $k => $val) {
                            $val['order_id'] = $order_id;
                            $sub = $val['sub'];
                            $ship = $val['ship'];
                            $arrItemsChildColumns = $val['arrItemsChildColumns'];
                            unset($val['sub']);
                            unset($val['ship']);
                            unset($val['arrItemsChildColumns']);

                            $order_item_id = $this->orders_model->insertOrderItemsNew($val);
                            if ($order_item_id) {
                                if (!empty($sub)) {
                                    foreach ($sub as $i => $v) {
                                        $v['order_item_id'] = $order_item_id;
                                        $this->orders_model->insertOrderItemShippingsNew($v);
                                    }
                                }

                                if (!empty($ship)) {
                                    foreach ($ship as $kSh => $valSh) {
                                        $this->db->insert('tbl_orders_ship', [
                                            'order_item_id' => $order_item_id,
                                            'date' => $valSh['date'],
                                            'quantity' => $valSh['quantity'],
                                        ]);
                                    }
                                }

                                if (!empty($arrItemsChildColumns)) {
                                    foreach ($arrItemsChildColumns as $kC => $vC) {
                                        $arrItemsChildColumns[$kC]['order_id'] = $order_id;
                                        $arrItemsChildColumns[$kC]['order_item_id'] = $order_item_id;
                                    }
                                    $this->orders_model->insertBatchOrderItemsColumns($arrItemsChildColumns);
                                }

                                if ($val['type_item'] == "products") {
                                    $exchangeUnits = $this->products_model->getExchangeProductsByProductId($val['item_id']);
                                    if (!empty($exchangeUnits)) {
                                        foreach ($exchangeUnits as $kk => $vv) {
                                            if (empty($vv)) continue;
                                            $quantity_exchange = $vv['number_exchange'];
                                            $total_quantity_exchange = $val['quantity'] / $quantity_exchange;
                                            $exchange = [
                                                'order_item_id' => $order_item_id,
                                                'unit_id' => $vv['unit_id'],
                                                'quantity_exchange' => $quantity_exchange,
                                                'total_quantity_exchange' => $total_quantity_exchange,
                                            ];
                                            $this->orders_model->insertOrderItemExchange($exchange);
                                        }
                                    }
                                }
                            }
                        }

                        // $wf = $this->site_model->insertOrdersWorkflow([
                        //     'workflow_id' => 0,
                        //     'order_id' => $order_id,
                        //     'created_by' => get_staff_user_id(),
                        //     'date_created' => date('Y-m-d H:i:s'),
                        // ]);

                        $listRef[] = $reference_no;
                        $count++;
                    }
                }
            }
            //handling show nofitications
            $data['errors'] = $errors;
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
                insertActivityLog([
                    'type_parent_obj' => 'orders',
                    'table_obj' => 'tbl_orders',
                    'id_obj' => $order_id,
                    'name_obj' => $reference_no,
                    'content' => lang('tnh_his_add_orders') . ' [' . implode(',', $listRef) . ']',
                    'actions' => 'import'
                ]);
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data);
            die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('tnh_import_orders'). ' thay đổi';
            $this->load->view('admin/orders/import_orders_change', $data);
        }
    }

    public function add_colum_delivery($id)
    {
        $data = [];
        $order = get_table_where('tbl_orders', ['id' => $id], '', 'row_array');

        $this->db->select('*');
        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.order_id', $id);
        $items = $this->db->get()->result_array();

        $data['order'] = $order;
        $data['items'] = $items;
        $data['id'] = $id;
        $this->load->view('admin/orders/view_add_colum_delivery', $data);
    }

    public function save_colum_delivery(){
        $data = [];
        $data['result'] = 0;
        $data['message'] = lang('Thất bại');
        $dataPost = $this->input->post();
        $order_id = $dataPost['order_id'];
        $p_id = $dataPost['p_id'];
        $vt1 = $dataPost['vt1'];
        $vt2 = $dataPost['vt2'];
        $vt3 = $dataPost['vt3'];
        $vt4 = $dataPost['vt4'];
        if (!empty($p_id)) {
            $vt1_ar = array();
            $vt1_id = array();
            if(!empty($vt1)){
                $vt1_ar = explode('_____',$vt1);
                foreach ($vt1_ar as $ka => $va) {
                    if (empty($va)) {
                        continue;
                    }
                    $vas = explode('|_|',$va);
                    $vt1_id[$vas[0]] = $vas[1];
                }
            }
            $vt2_ar = array();
            $vt2_id = array();
            if(!empty($vt2)){
                $vt2_ar = explode('_____',$vt2);
                foreach ($vt2_ar as $ka => $va) {
                    if (empty($va)) {
                        continue;
                    }
                    $vas = explode('|_|',$va);
                    $vt2_id[$vas[0]] = $vas[1];
                }
            }

            $vt3_ar = array();
            $vt3_id = array();
            if(!empty($vt3)){
                $vt3_ar = explode('_____',$vt3);
                foreach ($vt3_ar as $ka => $va) {
                    if (empty($va)) {
                        continue;
                    }
                    $vas = explode('|_|',$va);
                    $vt3_id[$vas[0]] = $vas[1];
                }
            }
            $vt4_ar = array();
            $vt4_id = array();
            if(!empty($vt4)){
                $vt4_ar = explode('_____',$vt4);
                foreach ($vt4_ar as $ka => $va) {
                    if (empty($va)) {
                        continue;
                    }
                    $vas = explode('|_|',$va);
                    $vt4_id[$vas[0]] = $vas[1];
                }
            }
            $p_id = explode(',', $p_id);
            $this->db->select('tbl_orders.*, tblclients.company as company,tblclients.company_short as company_short, tblclients.zcode as code_customer', false);
            $this->db->from('tbl_orders');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
            $this->db->where('tbl_orders.id', $order_id);
            $order = $this->db->get()->row_array();


            $this->db->select('
                tbl_order_items.*
            ', false);
            $this->db->from('tbl_order_items');
            $this->db->where('tbl_order_items.order_id', $order_id);
            $this->db->where_in('tbl_order_items.id', $p_id);
            $order_items = $this->db->get()->result_array();
            $success = false;
            if (!empty($order_items)) {
                foreach ($order_items as $key => $value) {
                    $order_item_id = $value['id'];
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];
                    if (!empty($vt1_id) || !empty($vt2_id)) {
                        $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                        if ($type_item == "products") {
                            $productsColumns = $this->products_model->getProductsColumns($items_id);
                            $trHtmlChild = '';
                            $thSub = '';
                            $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                            $ct_counter_item = $value['ct_counter_item'];
                            $trHtmlChild = '';
                            $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                            if ($ct_counter_item > 0) {
                                for ($i = 0; $i < $ct_counter_item; $i++) {
                                    $trHtmlColumns = '';
                                    foreach ($productsColumns as $k => $v) {
                                        foreach ($orderItemsColumns as $kO => $vO) {
                                            if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                $columns_name = $vO['columns_name'];
                                                break;
                                            }
                                        }
                                    }

                                    $tv1_text = null;
                                    $tv3_text = null;
                                    $tv4_text = null;
                                    $text_size = null;
                                    if(!empty($vt1_id[$value['id']])){
                                        $tv1_text = $vt1_id[$value['id']];
                                    }
                                    if(!empty($vt3_id[$value['id']])){
                                        $tv3_text = $vt3_id[$value['id']];
                                    }
                                    if(!empty($vt4_id[$value['id']])){
                                        $tv4_text = $vt4_id[$value['id']];
                                    }

                                    if(!empty($vt2_id[$value['id']])){
                                        $text_size = $vt2_id[$value['id']];
                                    }else{
                                        continue;
                                    }

                                    if ($value['type_item'] == 'products') {
                                        $this->db->where('id', $value['id']);
                                        $success = $this->db->update('tbl_order_items', [
                                            'colum_delivery1' => $tv1_text,
                                            'colum_delivery2' => $text_size,
                                            'colum_delivery3' => $tv3_text,
                                            'colum_delivery4' => $tv4_text,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($success)){
                $data['result'] = 1;
                $data['message'] = lang('Thêm thành công');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Không cột nào được thêm');
            }
        }
        echo json_encode($data);
    }
	
    //	public function get_order_shipping_next() {
    //		$this->db->where('DATE_FORMAT(date_shipping, "%Y-%m-%d") > "'.date('Y-m-d').'"', false, false);
    //		$this->db->get('tbl_order_item_shippings')->result_array();
    //	}

    public function pass_fail($id)
    {
        if (!$this->perEditOrders) {
            accessDenied($js = true);
        }

        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $order_sub = $this->orders_model->getOrdersSubById($id);
        // if ($order['is_cancel'] == 1) {
        //     refererModel(lang('Đơn này đã được hủy'));
        //     die;
        // }

        if ($order['type_orders'] != TYPE_SAMPLE_ORDER) {
            refererModel(lang('Không phải đơn hàng mẫu'));
            die;
        }

        if ($this->input->post('save')) {
            $dataPOST = $this->input->post();
            $date_pass_fail = date('Y-m-d H:i:s');
            $staff_pass_fail = get_staff_user_id();
            $is_pass_fail = $dataPOST['is_pass_fail'];
            $note_pass_fail = $dataPOST['note_pass_fail'];
            
            $rs = $this->orders_model->handlingOrdersSub($id, [
                'order_id' => $id,
                'date_pass_fail' => $date_pass_fail,
                'staff_pass_fail' => $staff_pass_fail,
                'is_pass_fail' => $is_pass_fail,
                'note_pass_fail' => $note_pass_fail,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data); die;
        }
        $data['order'] = $order;
        $data['id'] = $id;
        $data['order_sub'] = $order_sub;
        $this->load->view('admin/orders/pass_fail', $data);
    }

    public function choose_quotes($id)
    {
        if (!$this->perEditOrders) {
            accessDenied($js = true);
        }

        $data = [];
        $order = $this->orders_model->rowOrderById($id);
        $order_sub = $this->orders_model->getOrdersSubById($id);
        // if ($order['is_cancel'] == 1) {
        //     refererModel(lang('Đơn này đã được hủy'));
        //     die;
        // }

        if ($order['type_orders'] != TYPE_SAMPLE_ORDER && $order['type_orders'] != TYPE_KH_ORDER && $order['type_orders'] != TYPE_PTM) {
            refererModel(lang('Không phải đơn hàng mẫu, ĐĐH KH'));
            die;
        }

        if ($this->input->post('save')) {
            $dataPOST = $this->input->post();
            $date_quotes_chonse = date('Y-m-d H:i:s');
            $staff_quotes_chonse = get_staff_user_id();
            $quote_id_chonse = $dataPOST['quote_id_chonse'];
            
            $rs = $this->orders_model->handlingOrdersSub($id, [
                'order_id' => $id,
                'quote_id_chonse' => $quote_id_chonse,
                'date_quotes_chonse' => $date_quotes_chonse,
                'staff_quotes_chonse' => $staff_quotes_chonse,
            ]);

            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data); die;
        }

        $data['order'] = $order;
        $data['id'] = $id;
        $data['order_sub'] = $order_sub;
        $this->load->view('admin/orders/choose_quotes', $data);
    }

    public function choose_order_manu($id)
    {
        if (!$this->perEditOrders) {
            accessDenied($js = true);
        }

        $data = [];
        $order = $this->orders_model->rowOrderById($id);

        if ($order['type_orders'] != TYPE_COMPENSATE_ORDER) {
            refererModel(lang('Không phải đơn hàng bù'));
            die;
        }

        if ($this->input->post('save')) {

            $orders_choose = $this->input->post('orders_choose');
            $productions_orders_choose = $this->input->post('productions_orders_choose');
            $arrData = [];
            if (!empty($orders_choose)) {
                foreach ($orders_choose as $key => $value) {
                    $arrData[] = [
                        'order_id' => $id,
                        'type_relationship' => 2,
                        'object_id' => $value,
                    ];
                }
            }

            if (!empty($productions_orders_choose)) {
                foreach ($productions_orders_choose as $key => $value) {
                    $arrData[] = [
                        'order_id' => $id,
                        'type_relationship' => 1,
                        'object_id' => $value,
                    ];
                }
            }

            $this->orders_model->handlingOrdersRelationship($id, $arrData);
            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data); die;
        }

        $data['order'] = $order;
        $data['id'] = $id;
        $this->load->view('admin/orders/choose_order_manu', $data);
    }

    function searchOrdersPicker()
	{
		$data = [];
		if ($this->input->get()) {
			$q = $this->input->get('q');
			$limit = 50;

            $this->db->select('tbl_orders.id as id, tbl_orders.reference_no as name', false);
            $this->db->from('tbl_orders');
            if (!empty($q))
            {
                $this->db->group_start();
                $this->db->like('tbl_orders.reference_no', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $data = $this->db->get()->result_array();
		}
		echo json_encode($data);
	}

    function searchProductionsOrdersPicker()
	{
		$data = [];
		if ($this->input->get()) {
			$q = $this->input->get('q');
			$limit = 50;

            $this->db->select('tbl_productions_orders.id as id, tbl_productions_orders.reference_no as name', false);
            $this->db->from('tbl_productions_orders');
            if (!empty($q))
            {
                $this->db->group_start();
                $this->db->like('tbl_productions_orders.reference_no', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $data = $this->db->get()->result_array();
		}
		echo json_encode($data);
	}

    public function searchQR() {
        $code = $this->input->post('code');
        $checkCode = explode('||',$code)[0];
        if ($checkCode != 'products') {
            $response['items'] = [];
            $response['result'] = 0;
            $response['message'] = lang('Chỉ có thể chọn thành phẩm');
            echo json_encode($response);die;
        }
        $response = $this->products_model->searchQR($code);
        if (!empty($response['items']->size)) {
            $size = get_table_where('tblsize', ['id'=>$response['items']->size], '', 'row_array');
            $response['items']->size_name = (!empty($size['name']) ? $size['name'] : '');
        } else {
            $response['items']->size_name = '';
        }
        echo json_encode($response);
    }
	
	public function excel_orders_information_table()
	{
		ob_end_clean();
		if (!$this->perPrintOrders) {
			accessDenied();
		}
		
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		
		$style_excel = style_excel();
		$cloumns_excel = cloumns_excel();
		$objPHPExcel = new PHPExcel();
		
		$list_id_order = $this->input->get('list_id_order');
		$tbDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(DATE_FORMAT(tbl_deliveries.date, '%d/%m/%Y'), ' - ', tbl_delivery_items.quantity)) as date_delivery
            FROM tbl_delivery_items
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery_item";
		
		$tbDateExpectedDelivery = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                tbl_order_item_shippings.date_shipping as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_order_item_shippings";
		
		$tbGroupCustomer = '(
            SELECT
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as group_name
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group';
		
		$tbWarehousesProducts = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.product_quantity > 0 AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders') or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0)) AND tblwarehouse_items.warehouse_id NOT IN (".WAREHOUSES_SYSTEM.") AND tbllocaltion_warehouses.stage_id = 0
            GROUP BY tblwarehouse_items.id_items
        ) tb_warehouses_products";
		
		$tbProductions = '(
            SELECT
                tbl_productions_orders_items.production_plan_item_id,
                GROUP_CONCAT(tbl_productions_orders.reference_no SEPARATOR "</br>") as code_production
            FROM tbl_productions_orders_items
            JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
            WHERE tbl_productions_orders_items.object_item_type = "orders"
            GROUP BY tbl_productions_orders_items.production_plan_item_id
        ) tb_production';
		
		
		
		
		$this->db->select('
            tbl_orders.id as id,
            tbl_orders.date as date,
            tblclients.zcode as zcode,
            tb_customer_group.group_name as brand,
            tbl_orders.reference_no as reference_no,
            tb_order_item_shippings.date_shipping as date_delivery,
            tbl_order_items.product_name_customer as product_name_customer,
            tbl_products.code as item_code,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_not_delivery,
            tbl_order_items.quantity as quantity_orders,
            tbl_order_items.quantity_delivery as quantity_delivery,
            (tbl_order_items.quantity - tbl_order_items.quantity_delivery) as quantity_rest,
            tb_warehouses_products.product_quantity as quantity_warehouse,
            tbl_order_items.price as price,
            tb_delivery_item.date_delivery as quantity_detail,
            tbl_species.name as name_species,
            tbl_type_print.name as name_type_print,
            tbl_products.images as images,
            tbl_order_items.note_item as note_item,
            tbl_status_orders.name as name_status_orders,
            tbl_status_orders.time as time,
            tbl_type_orders.name as name_type_orders,
            tbl_orders.created_by as created_by,
            tblbranch.name as name_branch,
            tbl_orders.is_cancel as is_cancel,
            tb_production.code_production as code_production,
			"" as status_orders,
			tbl_orders.created_by as staff_create_orders
        ', false);
		$this->db->from('tbl_orders');
		$this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'inner');
		$this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
		$this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
		$this->db->join('tblbranch', 'tblbranch.id = tbl_orders.id_branch', 'left');
		$this->db->join('tbl_status_orders', 'tbl_status_orders.id = tbl_orders.status_orders', 'left');
		$this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
		$this->db->join('tbl_type_print', 'tbl_type_print.id = tbl_products.type_print', 'left');
		$this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left');
		$this->db->join($tbDateExpectedDelivery, 'tb_order_item_shippings.order_item_id = tbl_order_items.id', 'left');
		$this->db->join($tbDelivery, 'tb_delivery_item.order_item_id = tbl_order_items.id', 'left');
		$this->db->join($tbGroupCustomer, 'tb_customer_group.customer_id = tblclients.userid', 'left');
		$this->db->join($tbWarehousesProducts, 'tb_warehouses_products.id_items = tbl_order_items.item_id', 'left');
		$this->db->join($tbProductions, 'tb_production.production_plan_item_id = tbl_order_items.id', 'left');
		$this->db->order_by('tbl_status_orders.id DESC');
		$this->db->where('tbl_orders.id', $list_id_order);
		$this->db->where('tbl_order_items.type_item', "products");
		$rs = $this->db->get()->result_array();
		
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'THỐNG KÊ ĐƠN HÀNG')->getStyle("E1")->applyFromArray($style_excel['c_head']);
		$objPHPExcel->getActiveSheet()->mergeCells("E1:M1")->getStyle("E1")->applyFromArray($style_excel['c_head']);
		
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Khách hàng: ');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', '');
		
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Loại đơn hàng: ');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', '');
		
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Trạng thái đơn hàng: ');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', '');
		
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Ngày bắt đầu: ');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', '');
		
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Ngày kết thúc: ');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', '');
		
		
		$listTitle = [
			'STT',
			'Mã KH',
			'Brand',
			'Mã ĐĐH',
			'Ngày giao dự kiến',
			'Mã sản phẩm',
			'Tên TP của khách',
			'SL chưa giao',
			'SL đơn hàng',
			'SL đã giao',
			'SL còn lại',
			'Số lượng tồn',
			'Đơn giá',
			"Ngày giao hàng -\n SL chi tiết",
			"Chủng loại",
			"Loại hình in",
			"Hình sản phẩm",
			"Ghi chú",
			"Loại đơn hàng",
			"LSX",
			"Trạng thái đơn hàng",
			"Người lập đơn",
			"Chi nhánh xưởng",
		];
		$numberRow = 7;
		foreach ($listTitle as $key => $value) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->SetCellValue("$cloumns_excel[$key]$numberRow", $value)->getStyle("$cloumns_excel[$key]$numberRow")->applyFromArray($style_excel['c_th']);
		}
		$objPHPExcel->getActiveSheet()->getStyle("A1:Y$numberRow")->applyFromArray([
			'font' => array(
				'bold' => true,
			),
		]);
		
		$row = 7;
		$group = '';
		$start = 0;
		$quantity_not_delivery = 0;
		$quantity_orders = 0;
		$quantity_delivery = 0;
		$quantity_rest = 0;
		$quantity_warehouse = 0;
		if (!empty($rs)) {
			foreach ($rs as $key => $aRow) {
				
				$row++;
				$start++;
				
				$name_status_orders = !empty($aRow['name_status_orders']) ? $aRow['name_status_orders'] : 'Chưa xác định';
				if ($group != $name_status_orders) {
					$group = $name_status_orders;
					
					$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, (!empty($aRow['time']) ? $aRow['time'] : 'Chưa xác định'));
					$objPHPExcel->getActiveSheet()->mergeCells("A$row:Y$row");
					$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'e3b897')
							)
						)
					);
					$row++;
				}
				
				$po = "(
					SELECT
						GROUP_CONCAt(tbl_productions_orders.reference_no) as reference_no
					FROM tbl_productions_plan_orders
					INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
					WHERE tbl_productions_plan_orders.productions_plan_id = ".$aRow['id']." AND tbl_productions_plan_orders.object_type = 'orders'
            	)";
				$dtPO = $this->db->query($po)->row();
				
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start);
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, _dC($aRow['date']));
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $aRow['zcode']);
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $aRow['brand']);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, ($aRow['reference_no']));
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, _dC($aRow['date_delivery']));
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $aRow['item_code']);
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $aRow['product_name_customer']);
				$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $aRow['quantity_not_delivery'])->getStyle("I$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_not_delivery']));
				
				$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $aRow['quantity_orders'])->getStyle("J$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_orders']));
				$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $aRow['quantity_delivery'])->getStyle("K$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_delivery']));
				$objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $aRow['quantity_rest'])->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_rest']));
				$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $aRow['quantity_warehouse'])->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['quantity_warehouse']));
				$objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $aRow['price'])->getStyle("N$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($aRow['price']));
				$objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $aRow['quantity_detail']);
				$objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $aRow['name_species']);
				$objPHPExcel->getActiveSheet()->setCellValue('Q' . $row, $aRow['name_type_print']);
				
				if ($aRow['images'] != '' && file_exists($aRow['images'])) {
					$objDrawing1 = new PHPExcel_Worksheet_Drawing();
					$objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
					$objDrawing1->setPath($aRow['images']);
					$objDrawing1->setWidth(110);
					$objDrawing1->setHeight(85);
					$objDrawing1->setOffsetX(20);
					$objDrawing1->setOffsetY(5);
					$objDrawing1->setCoordinates('R' . ($row));
				}
				$objPHPExcel->getActiveSheet()->setCellValue('S' . $row, $aRow['note_item']);
				$objPHPExcel->getActiveSheet()->setCellValue('T' . $row, $aRow['name_type_orders']);
				$objPHPExcel->getActiveSheet()->setCellValue('U' . $row, (!empty($dtPO->reference_no) ? $dtPO->reference_no : ''));
				$objPHPExcel->getActiveSheet()->setCellValue('V' . $row, $name_status_orders);
				$objPHPExcel->getActiveSheet()->setCellValue('W' . $row, get_staff_full_name($aRow['created_by']));
				$objPHPExcel->getActiveSheet()->setCellValue('X' . $row, $aRow['name_branch']);
				$objPHPExcel->getActiveSheet()->setCellValue('Y' . $row, $aRow['is_cancel'] ? 'Có' : '');
				
				
				$quantity_not_delivery += $aRow['quantity_not_delivery'];
				$quantity_orders += $aRow['quantity_orders'];
				$quantity_delivery += $aRow['quantity_delivery'];
				$quantity_rest += $aRow['quantity_not_delivery'];
				$quantity_warehouse+= (float)$aRow['quantity_warehouse'];
			}
		}
		
		$row++;
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, 'Tổng cộng');
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $quantity_not_delivery)->getStyle("I$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_not_delivery));
		$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $quantity_orders)->getStyle("J$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_orders));
		$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $quantity_delivery)->getStyle("K$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_delivery));
		$objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $quantity_rest)->getStyle("L$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_rest));
		$objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $quantity_warehouse)->getStyle("M$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($quantity_warehouse));
		
		$objPHPExcel->getActiveSheet()->getStyle("A$row:Y$row")->applyFromArray([
			'font' => array(
				'bold' => true,
			),
		]);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(15);
		$filename = lang('tnh_excel_orders_information') . '.xls';
		
		$objPHPExcel->getActiveSheet()->getStyle("A7:X$row")->applyFromArray([
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		]);
		
		$objPHPExcel->getActiveSheet()->getStyle("A1:X$row")->getAlignment()->setWrapText(true);
		
		$objPHPExcel->getActiveSheet()->freezePane('A1');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		
		$objWriter->save('php://output');
		exit();
	}

    public function export_excel() {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => array(
                'name'  => 'Times New Roman'
            ),
        ]);

        $filter = $this->input->post();

        $tblcustomer_groups = '(SELECT
                GROUP_CONCAT(tblcustomers_groups.name SEPARATOR ", ") as client_category,
                tblcustomer_groups.customer_id as customer_id
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
            ORDER by tblcustomers_groups.code ASC
        ) AS tblcustomer_groups';
        $tbl_order_items_columns = '(SELECT
                GROUP_CONCAT(tbl_order_items_columns.columns_name SEPARATOR "; ") as command,
                tbl_order_items_columns.order_item_id as order_item_id
            FROM tbl_order_items_columns
            WHERE tbl_order_items_columns.columns_value = "command"
            GROUP BY tbl_order_items_columns.order_item_id
        ) as tbl_order_items_columns';
        $this->db->select('
            tbl_orders.reference_no as code,
            tbl_orders.reference_no_customer as code_client,
            tbl_type_orders.name as type,
            tbl_orders.date as date,
            tbl_order_item_shippings.date_shipping as delivery_date,
            tblclients.zcode as client_code,
            tblclients.company as client_name,
            tblcustomer_groups.client_category as client_category,
            tbl_order_items_columns.command as command,
            tbl_order_items.item_code as item_code,
            tbl_order_items.item_name as item_name,
            tbl_order_items.product_name_customer as item_name_client,
            tbl_products.mode as item_specification,
            tblunits.unit as item_unit,
            tbl_order_items.quantity_sheet_bale as item_quantity_sheet_bale,
            (tbl_order_items.quantity_sheet_bale * tbl_order_items.quantity) as item_total_bale,
            tbl_order_items.total_quantity_item as item_total_quantity,
            tbl_order_items.price as item_unit_price,
            IF(tbl_order_items.is_lot <> 0, \'Theo Giá Lô\', \'Theo Con\') as applied_price_type,
            tbl_order_items.total_amount as item_amount
        ');
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id', 'inner');
        $this->db->join('tbl_type_orders', 'tbl_type_orders.id = tbl_orders.type_orders', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_order_items.unit_id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id AND tbl_order_items.type_item = "products"', 'left');
        $this->db->join($tblcustomer_groups, 'tblcustomer_groups.customer_id = tblclients.userid', 'left');
        $this->db->join($tbl_order_items_columns, 'tbl_order_items_columns.order_item_id = tbl_order_items.id', 'left');

        $this->db->group_by('tbl_order_items.id');
        $this->db->order_by('tbl_orders.id', 'desc');

        if (!empty($filter['customer_search'])) {
            $this->db->where('tbl_orders.customer_id', $filter['customer_search']);
        }
        if (!empty($filter['orders_search'])) {
            $this->db->where('tbl_orders.id', $filter['orders_search']);
        }
        if (!empty($filter['start_date_search'])) {
            $this->db->where('tbl_orders.date >=', to_sql_date($filter['start_date_search']) . ' 00:00:00');
        }
        if (!empty($filter['end_date_search'])) {
            $this->db->where('tbl_orders.date <=', to_sql_date($filter['end_date_search']) . ' 23:59:59');
        }
        if (!empty($filter['type_orders_search'])) {
            $this->db->where('tbl_orders.type_orders', $filter['type_orders_search']);
        }
        if (!empty($filter['items_search'])) {
            $item = explode('__', $filter['items_search']);
            $item_id = $item[0];
            $item_type = $item[1];
            $this->db->where('tbl_order_items.item_id', $item_id);
            $this->db->where('tbl_order_items.type_item', $item_type);
        }

        $result = $this->db->get()->result_array();

        
        $colName = [
            'code' => 'Mã ĐĐH (TD)',
            'code_client' => 'Mã ĐĐH (KH)',
            'type' => 'Loại ĐĐH',
            'date' => 'Ngày Lập ĐĐH',
            'delivery_date' => 'Ngày Giao',
            'client_code' => 'Mã KH',
            'client_name' => 'Khách Hàng',
            'client_category' => 'Brand',
            'command' => 'Chỉ lệnh',
            'item_code' => 'Mã Thành Phẩm (TD)',
            'item_name' => 'Tên Thành Phẩm (TD)',
            'item_name_client' => 'Tên Thành Phẩm (KH)',
            'item_specification' => 'Quy cách',
            'item_unit' => 'ĐVT',
            'item_quantity_sheet_bale' => 'Số Con/Kiện',
            'item_total_bale' => 'Tổng Số Kiện',
            'item_total_quantity' => 'Tổng Số Lượng',
            'item_unit_price' => 'Đơn Giá Bán',
            'applied_price_type' => 'Loại Giá Áp Dụng',
            'item_amount' => 'Tổng Tiền',
        ];
        $aColumns = array_keys($colName);

        $excelRowNum = 1;
        $maxCol = count($colName) - 1;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excelRowNum, ('PHIẾU ĐƠN ĐẶT HÀNG'))->getStyle("A".$excelRowNum)->applyFromArray($styleTitle);
        // $objPHPExcel->getActiveSheet()->freezePane('A1');
        
        $excelRowNum = 2;
        foreach ($aColumns as $key => $value) {
            foreach($headerFillColor as $colIndex => $color) {
                if ($cloumns_excel[$key] == $colIndex) {
                    $styleHeader['fill']['color'] = $color;
                    unset($headerFillColor[$colIndex]);
                    break;
                }
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
        }

        $excelRowNum = 3;
        foreach ($result as $key => $aRow) {
            foreach ($aColumns as $colIndex => $colCode) {
                if (str_contains($colCode, 'date')) {
                    $cellValue = (isset($aRow[$colCode]) ? _d($aRow[$colCode]) : '');
                } else {
                    $cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
                }

                $objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
            }
            $excelRowNum++;
        }

        $filename = 'Phieu_don_dat_hang' . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'result' => 1,
            'message' => lang('success'),
            'filename' => $filename,
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }
	
	//cong lam
	public function export_excel_colums_detail()
	{
		$id = $this->input->post('id');
		if (!$this->perPrintOrders) {
			accessDenied();
		}
		
		ini_set('memory_limit', '3500M');
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$this->load->library('PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
		$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
		$objPHPExcel->getDefaultStyle()->applyFromArray([
			'font' => array(
				'name'  => 'Times New Roman'
			),
		]);
		$styleTitle = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				'bold' => true,
				'size' => 18,
				'name' => 'Times New Roman'
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		];
		
		$styleHeader = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				// 'bold' => true,
				// 'color' => array('rgb' => '111112'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '4BACC6'),
				'size' => 12,
				// 'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		];
		
		$styleTotal = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				 'bold' => true,
				 'color' => array('rgb' => 'fc2d42'),
				'size' => 12,
				'name' => 'Times New Roman'
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'size' => 12,
				// 'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		];
		
		$stylePlain = [
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'font' => array(
				// 'bold' => false,
				// 'color' => array('rgb' => '111112'),
				'size' => 11,
				'name' => 'Times New Roman'
			),
		];
		
		$headerFillColor = [
			'A' => array('rgb' => 'FFFF00'),
		];
		
		$cloumns_excel = cloumns_excel();
		$data = [];
		$order = $this->orders_model->rowOrderById($id);
		$items = $this->orders_model->getOrderItemsByOrderId($id);
		
		
		
		
		$colName = [
			'stt' => _l('tnh_numbers'),
			'date_ship' => _l('Ngày Giao'),
			'order_code' => _l('Mã Đơn đặt'),
			'command' => _l('Chỉ Lệnh'),
			'product_name_customer' => _l('Tên Liệu'),
			'quantity_put' => _l('Số Lượng')
		];
		$colNameTotal = [
			'0' => '',
			'1' => '',
			'2' => '',
			'3' => '',
			'4' => '',
			'5' => 'total'
		];
		$aColumns = array_keys($colName);
		
        insertCompanyInfo($objPHPExcel, 'C1:K2', 'B1', 'orders');

		$excelRowNum = 1 + 4;
		$maxCol = count($colName) - 1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($excelRowNum).':'.$cloumns_excel[$maxCol].$excelRowNum);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$excelRowNum, ('ĐƠN HÀNG CHI TIẾT:' . $order['reference_no']))->getStyle("A".$excelRowNum)->applyFromArray($styleTitle);
		// $objPHPExcel->getActiveSheet()->freezePane('A1');
		
		$excelRowNum = 2 + 4;
		foreach ($aColumns as $key => $value) {
			foreach($headerFillColor as $colIndex => $color) {
				if ($cloumns_excel[$key] == $colIndex) {
					$styleHeader['fill']['color'] = $color;
					unset($headerFillColor[$colIndex]);
					break;
				}
			}
			$objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$key] . $excelRowNum, ($colName[$value]))->getStyle($cloumns_excel[$key] . ($excelRowNum))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getColumnDimension($cloumns_excel[$key])->setAutoSize(true);
		}
		$stt = 1;
		$excelRowNum = 3 + 4;
		$listArray = [];
		foreach ($items as $key => $value) {
			$orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
			$total = 0;
			foreach ($orderItemsColumns as $k => $v) {
				if(empty($listArray[$v['counter_item']])) {
					$listArray[$v['counter_item']]['product_name_customer'] = $value['product_name_customer'];
					$listArray[$v['counter_item']]['stt'] = $stt;
					$stt++;
				}
				$listArray[$v['counter_item']][$v['columns_value']] = $v['columns_name'];
				if($v['columns_value'] == 'quantity_put') {
					$total += $v['columns_name'];
				}
			}
		}
		$listArrayOrder = [];
		foreach ($listArray as $key => $value) {
			$timeKey = '0';
			if(!empty($value['date_ship']) && !empty(to_sql_date($value['date_ship']))) {
				$timeKey = strtotime(to_sql_date($value['date_ship']));
			}
			$listArrayOrder[$timeKey][] = $value;
		}
		
		foreach ($listArrayOrder as $key => $listArray) {
			$total = 0;
			foreach ($listArray as $k => $aRow) {
				foreach ($aColumns as $colIndex => $colCode) {
					$cellValue = (isset($aRow[$colCode]) ? $aRow[$colCode] : '');
					$objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$colIndex] . $excelRowNum, $cellValue)->getStyle($cloumns_excel[$colIndex] . $excelRowNum)->applyFromArray($stylePlain);
				}
				$total += $aRow['quantity_put'];
				$excelRowNum++;
			}
			foreach($colNameTotal as $k => $v){
				$objPHPExcel->getActiveSheet()->setCellValue($cloumns_excel[$k] . $excelRowNum, (!empty($v) ? $total : ''))->getStyle($cloumns_excel[$k] . $excelRowNum)->applyFromArray($styleTotal);
			}
			$excelRowNum++;
		}
		
		
		
		
		$filename = 'Don_hang_chi_tiet' . '.xls';
		ob_start();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="$filename"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		
		$response =  array(
			'result' => 1,
			'message' => lang('success'),
			'filename' => $filename,
			'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
		);
		die(json_encode($response));
	}
}
