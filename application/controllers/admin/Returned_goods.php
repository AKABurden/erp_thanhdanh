<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Returned_goods extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('returned_goods_model');
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->tnh = true;
    }

    public function index()
    {
        $data['tnh'] = $this->tnh;
        $data['title'] = lang('returned_goods');
        $this->load->view('admin/returned_goods/index', $data);
    }

    public function getReturnedGoods()
    {

        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status_table = $this->input->post('status_table');

        $this->datatables->select("
            tbl_returned_goods.id as id,
            tbl_returned_goods.date as date,
            tbl_returned_goods.reference_no as reference_no,
            tblclients.company_short as customer_name,
            tbl_orders.reference_no as reference_order,
            CONCAT(employees.firstname, ' ', employees.lastname) as employee,
            tbl_returned_goods.handling_solution as handling_solution,
            tbl_returned_goods.grand_total as grand_total,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
            tbl_returned_goods.warehouseman_id as status,
            CONCAT(staff_users.firstname, ' ', staff_users.lastname) as user_status,
            tbl_returned_goods.note as note
        ", FALSE)
            ->from('tbl_returned_goods')
            ->join('tblstaff', 'tblstaff.staffid = tbl_returned_goods.created_by', 'left')
            ->join('tbl_orders', 'tbl_orders.id = tbl_returned_goods.order_id', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_returned_goods.customer_id', 'left')
            ->join('tblstaff employees', 'employees.staffid = tbl_returned_goods.employee_id', 'left')
            ->join('tblstaff staff_users', 'staff_users.staffid = tbl_returned_goods.warehouseman_id', 'left');

        if (!empty($orders_search)) {
            $this->datatables->where('tbl_orders.id', $orders_search);
        }

        if (!empty($customer_search)) {
            $this->datatables->where('tbl_returned_goods.customer_id', $customer_search);
        }

        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_returned_goods.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }

        $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/returned_goods/view_returned_goods/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . '</a>';

        $edit = '<a href="' . base_url('admin/returned_goods/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>';
        $print = '<a href="' . base_url('admin/returned_goods/print_returned_goods/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . '</a>';

        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/returned_goods/deleteReturnGoods/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li>' . $print . '<li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function view_returned_goods($id)
    {
        $returned_goods = $this->returned_goods_model->rowReturnedGoodsById($id);
        $items = $this->returned_goods_model->getReturnedGoodsItems($id);


        $returned_goods['customer_name'] = $this->site_model->rowCustomer($returned_goods['customer_id'])['company_short'];
        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $info = null;
                $images = null;
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                if (!empty($returned_goods['warehouseman_id'])) {
                    $htmlWarehouse = '';
                    $htmlWarehouseLocation = '';
                    $warehouse = get_table_where('tblwarehouse', ['id' => $value['warehouse_id']], '', 'row_array');
                    $location = get_table_where('tbllocaltion_warehouses', ['id' => $value['localtion_id']], '', 'row_array');
                    if (!empty($warehouse)) {
                        $htmlWarehouse = '<div>' . $warehouse['name'] . '</div>';
                    }
                    if (!empty($location)) {
                        $htmlWarehouseLocation = '<div>' . $location['name'] . '</div>';
                    }
                } else {
                    $htmlWarehouse = '';
                    $htmlWarehouseLocation = '';
                }


                $lot_code = !empty($value['lot_code']) ? '<div style="color: green">Lot: ' . $value['lot_code'] . '</div>' : '<div style="color: green">Lot: </div>';
                $date_sx = !empty($value['date_sx']) ? '<div style="color: green">Ngày SX: ' . _dhau($value['date_sx']) . '</div>' : '<div style="color: green">Ngày SX:</div>';
                $date_sd = !empty($value['date_sd']) ? '<div style="color: green">Ngày SD: ' . _dhau($value['date_sd']) . '</div>' : '<div style="color: green">Ngày SD:</div>';

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdCode = '<td>' . $info['code'] . '</td>';
                $tdName = '<td>' . $info['name'] . $lot_code . $date_sx . $date_sd . '</td>';
                $tdUnit = '<td>' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td class="text-right">' . formatMoney($value['price']) . '</td>';
                $tdTotalAmount = '<td class="text-right">' . formatMoney($value['amount']) . '</td>';
                $tdQuantityLoss = '<td class="text-center">' . formatNumber($value['quantity_loss']) . '</td>';
                $tdQuantitySample = '<td class="text-center">' . formatNumber($value['quantity_sample']) . '</td>';
                $tdDiscountPercent = '<td class="text-center">' . $value['discount_percent_item'] . '</td>';
                $tdDiscountDirect = '<td class="text-right">' . formatMoney($value['discount_direct_amount_item']) . '</td>';
                $tdGrandTotal = '<td class="text-right">' . formatMoney($value['total_amount']) . '</td>';
                $tdNote = '<td>' . $value['note_item'] . '</td>';
                $tdwahouse = '<td>' . $htmlWarehouse . $htmlWarehouseLocation . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdImages . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTotalAmount . '
                    ' . $tdQuantityLoss . '
                    ' . $tdQuantitySample . '
                    ' . $tdGrandTotal . '
                    ' . $tdNote . '
                    ' . $tdwahouse . '
                </tr>';
            }
        }

        $data['bodyItems'] = $bodyItems;

        $data['order'] = !empty($returned_goods['order_id']) ? $this->orders_model->rowOrderById($returned_goods['order_id']) : '';
        $data['employee'] = !empty($returned_goods['employee_id']) ? get_staff_full_name($returned_goods['employee_id']) : '';
        $data['created_by'] = get_staff_full_name($returned_goods['created_by']);
        if (!empty($returned_goods['updated_by'])) {
            $data['updated_by'] = get_staff_full_name($returned_goods['updated_by']);
        }
        if (!empty($returned_goods['user_status'])) {
            $data['user_status'] = get_staff_full_name($returned_goods['user_status']);
        } else {
            $data['user_status'] = '';
        }

        $data['id'] = $id;
        $data['returned_goods'] = $returned_goods;
        $this->load->view('admin/returned_goods/view_returned_goods', $data);
    }

    public function add()
    {
        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_no_returned_goods"), 'trim|required|is_unique[tbl_returned_goods.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('handling_solution', lang("tnh_handling_solution"), 'required');
            if ($this->form_validation->run() == true) {

                //                print_arrays($this->input->post());
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $employees = $this->input->post('employees');
                $note = $this->input->post('note');
                $order_id = $this->input->post('order_id');
                $handling_solution = $this->input->post('handling_solution');

                $row_customer = $this->site_model->rowCustomer($customer_id);
                if (empty($row_customer)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_customer_not_exist');
                    echo json_encode($data);
                    die;
                }
                $customer_name = $row_customer['company_short'];

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
                $grand_total = 0;
                $status = 'un_approved';
                $deposit = 0;
                $total_cost_temporary_capital = 0;

                if ($order_id > 0) {
                    $order = $this->returned_goods_model->getOrdersReturnedGoods($order_id);
                    if (empty($order)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đơn hàng này không tồn tại hoặc chưa được duyệt');
                        echo json_encode($data);
                        die;
                    }
                    // $tax_id = $order['tax_id'];
                    // $discount_percent = $order['discount_percent'];
                }

                $errorItems = '';
                $arr_id = [];
                $arr_info = [];
                $counter = $this->input->post('counter');
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arrs = explode('__', $items_id);
                        $item_id = $arrs[0];
                        $type_item = $arrs[1];

                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($item_id);
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($item_id);
                        }
                        if (empty($info)) {
                            continue;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];
                        $lot_code = !empty($this->input->post('lot_code')[$value]) ? $this->input->post('lot_code')[$value] : null;
                        $date_sx = !empty($this->input->post('date_sx')[$value]) ? $this->input->post('date_sx')[$value] : null;
                        $date_sd = !empty($this->input->post('date_sd')[$value]) ? $this->input->post('date_sd')[$value] : null;
                        $date_use = !empty($this->input->post('date_use')[$value]) ? $this->input->post('date_use')[$value] : null;
                        $unit_id = !empty($this->input->post('unit_id')[$value]) ? $this->input->post('unit_id')[$value] : 0;
                        $delivery_item_id = !empty($this->input->post('delivery_item_id')[$value]) ? $this->input->post('delivery_item_id')[$value] : 0;
                        $warehouse_id = !empty($this->input->post('warehouse_id')[$value]) ? $this->input->post('warehouse_id')[$value] : 0;
                        $location_id = !empty($this->input->post('location_id')[$value]) ? $this->input->post('location_id')[$value] : 0;
                        $conversion_quantity_unit = !empty($this->input->post('conversion_quantity_unit')[$value]) ? $this->input->post('conversion_quantity_unit')[$value] : 0;
                        $note_item = $this->input->post('note_items')[$value];
                        $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $quantity_loss = number_unformat($this->input->post('quantity_loss')[$value]);
                        $quantity_sample = number_unformat($this->input->post('quantity_sample')[$value]);

                        $price = number_unformat($this->input->post('price')[$value]);
                        $amount = $quantity * $price;

                        $grand_total_item = $amount;
                        $discount_percent_item = $this->input->post('discount_percent_item')[$value];
                        $discount_percent_amount_item = 0;

                        $order_item_id = $this->input->post('order_item_id')[$value];
                        $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                        $delivery_item = get_table_where('tbl_delivery_items', ['id' => $delivery_item_id], '', 'row_array');

                        $quantity_unit_loss = 0;
                        $quantity_stock_loss = 0;

                        $quantity_stock_sample = 0;
                        $quantity_unit_sample = 0;
                        if ($info['unit_id'] == $unit_id) {
                            $quantity_unit = $quantity;
                            $quantity_stock = roundNumberFormat($quantity_unit * $conversion_quantity_unit, 4);

                            $quantity_unit_loss = $quantity_loss;
                            $quantity_stock_loss = roundNumberFormat($quantity_unit_loss * $conversion_quantity_unit, 4);

                            $quantity_unit_sample = $quantity_sample;
                            $quantity_stock_sample = roundNumberFormat($quantity_unit_sample * $conversion_quantity_unit, 4);
                        } else {
                            $quantity_stock  = $quantity;
                            $quantity_unit  = roundNumberFormat($quantity_stock / $conversion_quantity_unit, 4);

                            $quantity_stock_loss  = $quantity;
                            $quantity_unit_loss = roundNumberFormat($quantity_stock_loss / $conversion_quantity_unit, 4);

                            $quantity_stock_sample = $quantity_sample;
                            $quantity_unit_sample = roundNumberFormat($quantity_stock_sample * $conversion_quantity_unit, 4);
                        }

                        //
                        $quantity_unit = $quantity_unit + $quantity_unit_loss + $quantity_unit_sample;
                        $quantity_stock = $quantity_stock + $quantity_stock_loss + $quantity_stock_sample;

                        $discount_direct_amount_item = number_unformat($this->input->post('discount_direct_item')[$value]);

                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                            $total_discount_percent_items += $discount_percent_amount_item;
                            $grand_total_item -= $discount_percent_amount_item;
                        }

                        $total_discount_direct_items += $discount_direct_amount_item;
                        $grand_total_item -= $discount_direct_amount_item;

                        $cost_price = 0;
                        $cost_temporary_capital = 0;
                        $profit_temporary_capital = 0;

                        if (!empty($delivery_item)) {
                            $index = array_search($delivery_item_id, $arr_id);
                            $quantityReturns = "(
                                SELECT SUM(tbl_returned_goods_items.quantity) quantity_returns
                                FROM tbl_returned_goods_items
                                WHERE tbl_returned_goods_items.delivery_item_id = " . $delivery_item['id'] . "
                            )";
                            $quantityReturns = $this->db->query($quantityReturns)->row_array()['quantity_returns'];
                            if (empty($quantityReturns)) $quantityReturns = 0;

                            $quantity_had = $delivery_item['quantity'] - $quantityReturns;
                            if ($index === false) {
                                $arr_id[$key] = $delivery_item_id;
                                $arr_info[$key]['quantity'] = $quantity;
                                $arr_info[$key]['quantity_had'] = $quantity_had;
                                $arr_info[$key]['items_code'] = $items_code;
                                $arr_info[$key]['items_name'] = $items_name;
                            } else {
                                $arr_info[$index]['quantity'] = $arr_info[$index]['quantity'] + $quantity;
                            }
                        }

                        $items[] = [
                            'order_item_id' => $order_item_id,
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'note_item' => $note_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'cost_temporary_capital' => $cost_temporary_capital,
                            'cost_price' => $cost_price,
                            'lot_code' => $lot_code,
                            'date_sx' => $date_sx,
                            'date_sd' => $date_sd,
                            'date_use' => $date_use,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                            'quantity_unit' => $quantity_unit,
                            'quantity_stock' => $quantity_stock,
                            'delivery_item_id' => $delivery_item_id,
                            'warehouse_delivery_id' => $warehouse_id,
                            'location_delivery_id' => $location_id,

                            'quantity_loss' => $quantity_loss,
                            'quantity_unit_loss' => $quantity_unit_loss,
                            'quantity_stock_loss' => $quantity_stock_loss,

                            'quantity_sample' => $quantity_sample,
                            'quantity_unit_sample' => $quantity_unit_sample,
                            'quantity_stock_sample' => $quantity_stock_sample,
                        ];

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                        $total_cost_temporary_capital += $cost_temporary_capital;
                    }
                }

                // print_arrays($items);
                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }


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

                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }

                $grand_total += $total_tax;
                $grand_total = $grand_total - $deposit;

                if (!empty($arr_info)) {
                    foreach ($arr_info as $key => $value) {
                        $items_code = $value['items_code'];
                        $items_name = $value['items_name'];
                        $quantity = $value['quantity'];
                        $quantity_had = $value['quantity_had'];
                        if ($quantity > $quantity_had) {
                            $errorItems .= '<div>' . $items_code . '(' . $items_name . ') số lượng trả <=' . $quantity_had . '</div>';
                        }
                    }
                }


                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if ($handling_solution == "debt_reduction") {
                    $totalDebt = get_debt_client($customer_id);
                    if ($grand_total > $totalDebt) {
                        $data['result'] = 0;
                        $data['message'] = lang('Giảm trừ công nợ tổng tiền phải nhỏ hơn công nợ khách hàng') . ' ' . formatMoney($totalDebt);
                        echo json_encode($data);
                        die;
                    }
                }

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'employee_id' => $employees,
                    'handling_solution' => $handling_solution,
                    'order_id' => $order_id,
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
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'total_cost_temporary_capital' => $total_cost_temporary_capital,
                ];

                // print_arrays($options);
                $returned_goods_id = $this->returned_goods_model->insertReturnedGoods($options);
                if ($returned_goods_id) {

                    if (getReference('returned_goods') == $this->input->post('reference_no')) {
                        updateReference('returned_goods');
                    }

                    foreach ($items as $key => $value) {
                        $items[$key]['returned_goods_id'] = $returned_goods_id;

                        if (!empty($order_id)) {
                            //update qty return
                            $order_item = get_table_where('tbl_order_items', ['id' => $value['order_item_id']], '', 'row_array');
                            $this->db->where('tbl_order_items.id', $value['order_item_id']);
                            $this->db->update('tbl_order_items', ['quantity_returned' => $order_item['quantity_returned'] + $value['quantity']]);
                            //end
                        }
                    }
                    $this->returned_goods_model->insertBatchReturnedGoodsItems($items);

                    @pusherTNHNotfication();

                    insertActivityLog([
                        'type_parent_obj' => 'returned_goods',
                        'table_obj' => 'tbl_returned_goods',
                        'id_obj' => $returned_goods_id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_add_returned_goods') . ' [' . $reference_no . ']',
                        'actions' => 'add'
                    ]);

                    set_alert('success', lang('success'));
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

        $data['taxs'] = $this->site_model->getTaxs();
        $data['reference_no'] = getReference('returned_goods');
        $data['employees'] = $this->site_model->getStaff();
        $data['tnh'] = $this->tnh;
        $data['title'] = lang('tnh_add_returned_goods');
        $data['breadcrumb'] = [array('link' => base_url('admin/returned_goods'), 'page' => lang('returned_goods')), array('link' => '#', 'page' => lang('tnh_add_returned_goods'))];
        $this->load->view('admin/returned_goods/add', $data);
    }

    public function edit($id)
    {
        $returned_goods = $this->returned_goods_model->rowReturnedGoodsById($id);
        if (empty($returned_goods)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($returned_goods['status'] != "un_approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($returned_goods['warehouseman_id'] > 0) {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($this->input->post('edit')) {
            $data = [];
            if ($returned_goods['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_no_returned_goods"), 'trim|required|is_unique[tbl_returned_goods.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('handling_solution', lang("tnh_handling_solution"), 'required');
            if ($this->form_validation->run() == true) {

                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $employees = $this->input->post('employees');
                $note = $this->input->post('note');
                $order_id = $this->input->post('order_id');
                $handling_solution = $this->input->post('handling_solution');

                $row_customer = $this->site_model->rowCustomer($customer_id);
                if (empty($row_customer)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_customer_not_exist');
                    echo json_encode($data);
                    die;
                }
                $customer_name = $row_customer['company_short'];

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
                $grand_total = 0;
                $status = 'un_approved';
                $deposit = 0;
                $total_cost_temporary_capital = 0;

                if ($order_id > 0) {
                    $order = $this->returned_goods_model->getOrdersReturnedGoods($order_id, $id);
                    if (empty($order)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Đơn hàng này không tồn tại hoặc chưa được duyệt');
                        echo json_encode($data);
                        die;
                    }
                }

                $errorItems = '';
                $arr_id = [];
                $arr_info = [];
                $counter = $this->input->post('counter');
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arrs = explode('__', $items_id);
                        $item_id = $arrs[0];
                        $type_item = $arrs[1];

                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($item_id);
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($item_id);
                        }
                        if (empty($info)) {
                            continue;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];
                        $lot_code = !empty($this->input->post('lot_code')[$value]) ? $this->input->post('lot_code')[$value] : null;
                        $date_sx = !empty($this->input->post('date_sx')[$value]) ? $this->input->post('date_sx')[$value] : null;
                        $date_sd = !empty($this->input->post('date_sd')[$value]) ? $this->input->post('date_sd')[$value] : null;
                        $date_use = !empty($this->input->post('date_use')[$value]) ? $this->input->post('date_use')[$value] : null;
                        $unit_id = !empty($this->input->post('unit_id')[$value]) ? $this->input->post('unit_id')[$value] : 0;
                        $delivery_item_id = !empty($this->input->post('delivery_item_id')[$value]) ? $this->input->post('delivery_item_id')[$value] : 0;
                        $warehouse_id = !empty($this->input->post('warehouse_id')[$value]) ? $this->input->post('warehouse_id')[$value] : 0;
                        $location_id = !empty($this->input->post('location_id')[$value]) ? $this->input->post('location_id')[$value] : 0;
                        $conversion_quantity_unit = !empty($this->input->post('conversion_quantity_unit')[$value]) ? $this->input->post('conversion_quantity_unit')[$value] : 0;
                        $note_item = $this->input->post('note_items')[$value];
                        $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $quantity_loss = number_unformat($this->input->post('quantity_loss')[$value]);
                        $quantity_sample = number_unformat($this->input->post('quantity_sample')[$value]);

                        $price = number_unformat($this->input->post('price')[$value]);
                        $amount = $quantity * $price;

                        $grand_total_item = $amount;
                        $discount_percent_item = $this->input->post('discount_percent_item')[$value];
                        $discount_percent_amount_item = 0;

                        $order_item_id = $this->input->post('order_item_id')[$value];
                        $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                        $delivery_item = get_table_where('tbl_delivery_items', ['id' => $delivery_item_id], '', 'row_array');

                        $quantity_unit_loss = 0;
                        $quantity_stock_loss = 0;

                        $quantity_stock_sample = 0;
                        $quantity_unit_sample = 0;

                        if ($info['unit_id'] == $unit_id) {
                            $quantity_unit = $quantity;
                            $quantity_stock = roundNumberFormat($quantity_unit * $conversion_quantity_unit, 4);

                            $quantity_unit_loss = $quantity_loss;
                            $quantity_stock_loss = roundNumberFormat($quantity_unit_loss * $conversion_quantity_unit, 4);

                            $quantity_unit_sample = $quantity_sample;
                            $quantity_stock_sample = roundNumberFormat($quantity_unit_sample * $conversion_quantity_unit, 4);
                        } else {
                            $quantity_stock  = $quantity;
                            $quantity_unit  = roundNumberFormat($quantity_stock / $conversion_quantity_unit, 4);

                            $quantity_stock_loss  = $quantity;
                            $quantity_unit_loss = roundNumberFormat($quantity_stock_loss / $conversion_quantity_unit, 4);

                            $quantity_stock_sample = $quantity_sample;
                            $quantity_unit_sample = roundNumberFormat($quantity_stock_sample * $conversion_quantity_unit, 4);
                        }

                        //
                        $quantity_unit = $quantity_unit + $quantity_unit_loss + $quantity_unit_sample;
                        $quantity_stock = $quantity_stock + $quantity_stock_loss + $quantity_stock_sample;

                        $discount_direct_amount_item = number_unformat($this->input->post('discount_direct_item')[$value]);

                        if ($discount_percent_item > 0) {
                            $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                            $total_discount_percent_items += $discount_percent_amount_item;
                            $grand_total_item -= $discount_percent_amount_item;
                        }

                        $total_discount_direct_items += $discount_direct_amount_item;
                        $grand_total_item -= $discount_direct_amount_item;

                        $cost_price = 0;
                        $cost_temporary_capital = 0;
                        $profit_temporary_capital = 0;

                        if (!empty($delivery_item)) {
                            $index = array_search($delivery_item_id, $arr_id);
                            $quantityReturns = "(
                                SELECT SUM(tbl_returned_goods_items.quantity) quantity_returns
                                FROM tbl_returned_goods_items
                                WHERE tbl_returned_goods_items.delivery_item_id = " . $delivery_item['id'] . "
                            )";

                            $quantityReturns = $this->db->query($quantityReturns)->row_array()['quantity_returns'];
                            if (empty($quantityReturns)) $quantityReturns = 0;

                            $return_item_id = !empty($this->input->post('return_item_id')[$key]) ? $this->input->post('return_item_id')[$key] : 0;
                            $return_item = get_table_where('tbl_returned_goods_items', ['id' => $return_item_id], '', 'row_array');

                            $quantity_had = $delivery_item['quantity'] - $quantityReturns;
                            if (!empty($return_item)) {
                                $quantity_had += $return_item['quantity'];
                            }
                            if ($quantity_had < 0) $quantity_had = 0;
                            if ($index === false) {
                                $arr_id[$key] = $order_item_id;
                                $arr_info[$key]['quantity'] = $quantity;
                                $arr_info[$key]['quantity_had'] = $quantity_had;
                                $arr_info[$key]['items_code'] = $items_code;
                                $arr_info[$key]['items_name'] = $items_name;
                            } else {
                                $arr_info[$index]['quantity'] = $arr_info[$index]['quantity'] + $quantity;
                            }
                        }

                        $items[] = [
                            'returned_goods_id' => $id,
                            'order_item_id' => $order_item_id,
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'quantity' => $quantity,
                            'price' => $price,
                            'amount' => $amount,
                            'note_item' => $note_item,
                            'discount_percent_item' => $discount_percent_item,
                            'discount_percent_amount_item' => $discount_percent_amount_item,
                            'discount_direct_amount_item' => $discount_direct_amount_item,
                            'total_amount' => $grand_total_item,
                            'cost_temporary_capital' => $cost_temporary_capital,
                            'cost_price' => $cost_price,
                            'lot_code' => $lot_code,
                            'date_sx' => $date_sx,
                            'date_sd' => $date_sd,
                            'date_use' => $date_use,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                            'quantity_unit' => $quantity_unit,
                            'quantity_stock' => $quantity_stock,
                            'delivery_item_id' => $delivery_item_id,
                            'warehouse_delivery_id' => $warehouse_id,
                            'location_delivery_id' => $location_id,

                            'quantity_loss' => $quantity_loss,
                            'quantity_unit_loss' => $quantity_unit_loss,
                            'quantity_stock_loss' => $quantity_stock_loss,

                            'quantity_sample' => $quantity_sample,
                            'quantity_unit_sample' => $quantity_unit_sample,
                            'quantity_stock_sample' => $quantity_stock_sample,
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

                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }

                $grand_total += $total_tax;
                $grand_total = $grand_total - $deposit;

                if (!empty($arr_info)) {
                    foreach ($arr_info as $key => $value) {
                        $items_code = $value['items_code'];
                        $items_name = $value['items_name'];
                        $quantity = $value['quantity'];
                        $quantity_had = $value['quantity_had'];
                        if ($quantity > $quantity_had) {
                            $errorItems .= '<div>' . $items_code . '(' . $items_name . ') số lượng trả <=' . $quantity_had . '</div>';
                        }
                    }
                }

                if (!empty($errorItems)) {
                    $data['result'] = 0;
                    $data['message'] = $errorItems;
                    echo json_encode($data);
                    die;
                }

                if ($handling_solution == "debt_reduction") {
                    $totalDebt = get_debt_client($customer_id) + $returned_goods['grand_total'];
                    if ($grand_total > $totalDebt) {
                        $data['result'] = 0;
                        $data['message'] = lang('Giảm trừ công nợ tổng tiền phải nhỏ hơn công nợ khách hàng') . ' ' . formatMoney($totalDebt);
                        echo json_encode($data);
                        die;
                    }
                }

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'employee_id' => $employees,
                    'handling_solution' => $handling_solution,
                    'order_id' => $order_id,
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
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                ];
                // print_arrays($options);
                $up = $this->returned_goods_model->updateReturnedGoods($id, $options);
                if ($up) {
                    // $this->returned_goods_model->deleteReturnedGoodsItemsByRGId($id);
                    // $this->returned_goods_model->insertBatchReturnedGoodsItems($items);

                    //update qty return
                    if (!empty($order_id)) {

                        $returned_item_olds = get_table_where('tbl_returned_goods_items', ['returned_goods_id' => $id], '', 'result_array');
                        $this->returned_goods_model->deleteReturnedGoodsItemsByRGId($id);
                        foreach ($returned_item_olds as $k => $v) {
                            $order_item_old = get_table_where('tbl_order_items', ['id' => $v['order_item_id']], '', 'row_array');
                            $this->db->where('tbl_order_items.id', $v['order_item_id']);
                            $this->db->update('tbl_order_items', ['quantity_returned' => $order_item_old['quantity_returned'] - $v['quantity']]);
                        }
                    }

                    $this->returned_goods_model->insertBatchReturnedGoodsItems($items);

                    $returned_items = get_table_where('tbl_returned_goods_items', ['returned_goods_id' => $id], '', 'result_array');

                    foreach ($returned_items as $key => $value) {
                        if (!empty($order_id)) {
                            $order_item = get_table_where('tbl_order_items', ['id' => $value['order_item_id']], '', 'row_array');
                            $this->db->where('tbl_order_items.id', $value['order_item_id']);
                            $this->db->update('tbl_order_items', ['quantity_returned' => $order_item['quantity_returned'] + $value['quantity']]);
                        }
                    }
                    //end

                    insertActivityLog([
                        'type_parent_obj' => 'returned_goods',
                        'table_obj' => 'tbl_returned_goods',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_edit_returned_goods') . ' [' . $reference_no . ']',
                        'actions' => 'edit'
                    ]);

                    set_alert('success', lang('success'));
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

        $items = $this->returned_goods_model->getReturnedGoodsItems($id);
        $bodyItems = '';
        $counter = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $info = null;
                $images = false;

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $lot_code = !empty($value['lot_code']) ? '<div style="color: green">Lot: ' . $value['lot_code'] . '</div>' : '<div style="color: green">Lot: </div>';
                $date_sx = !empty($value['date_sx']) ? '<div style="color: green">Ngày SX: ' . _dhau($value['date_sx']) . '</div>' : '<div style="color: green">Ngày SX:</div>';
                $date_sd = !empty($value['date_sd']) ? '<div style="color: green">Ngày SD: ' . _dhau($value['date_sd']) . '</div>' : '<div style="color: green">Ngày SD:</div>';

                $tdNumber = '<div class="stt text-center">' . (++$key) . '</div>';
                $tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter[' . $counter . ']" id="counter" class="form-control counter" value="' . $counter . '">
                        <input type="text" name="items_id[' . $counter . ']" id="items_' . $counter . '" class="items_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $items_id . '__' . $type_item . '"></div>' .
                    '<input type="hidden" name="order_item_id[' . $counter . ']" id="order_item_id" class="form-control order_item_id" value="' . $value['order_item_id'] . '">
                        <input type="hidden" name="lot_code[' . $counter . ']" id="lot_code" class="form-control lot_code" value="' . $value['lot_code'] . '">
                        <input type="hidden" name="date_sx[' . $counter . ']" id="date_sx" class="form-control date_sx" value="' . $value['date_sx'] . '">
                        <input type="hidden" name="date_sd[' . $counter . ']" id="date_sd" class="form-control date_sd" value="' . $value['date_sd'] . '">
                        <input type="hidden" name="date_use[' . $counter . ']" id="date_use" class="form-control date_use" value="' . $value['date_use'] . '">
                        <input type="hidden" name="unit_id[' . $counter . ']" id="unit_id" class="form-control unit_id" value="' . $value['unit_id'] . '">
                        <input type="hidden" name="delivery_item_id[' . $counter . ']" id="delivery_item_id" class="form-control delivery_item_id" value="' . $value['delivery_item_id'] . '">
                        <input type="hidden" name="warehouse_id[' . $counter . ']" id="warehouse_id" class="form-control warehouse_id" value="' . $value['warehouse_delivery_id'] . '">
                        <input type="hidden" name="location_id[' . $counter . ']" id="location_id" class="form-control location_id" value="' . $value['location_delivery_id'] . '">
                        <input type="hidden" name="conversion_quantity_unit[' . $counter . ']" id="conversion_quantity_unit" class="form-control conversion_quantity_unit" value="' . $value['conversion_quantity_unit'] . '">
                        <input type="hidden" name="return_item_id[' . $counter . ']" class="form-control return_item_id" value="' . $value['id'] . '">
                        <div class="type-item">' . $lot_code . $date_sx . $date_sd . '</div>' .
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

                $showInfo = '';
                $order_item_id = $value['order_item_id'];
                $delivery_item_id = $value['delivery_item_id'];
                if (!empty($delivery_item_id)) {
                    $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                    $delivery_item = get_table_where('tbl_delivery_items', ['id' => $delivery_item_id], '', 'row_array');
                    $quantityOrders = $delivery_item['quantity'];

                    $quantityReturns = "(
                        SELECT SUM(tbl_returned_goods_items.quantity) quantity_returns
                        FROM tbl_returned_goods_items
                        WHERE tbl_returned_goods_items.delivery_item_id = " . $delivery_item['id'] . "
                    )";
                    $quantityReturns = $this->db->query($quantityReturns)->row_array()['quantity_returns'];
                    $quantityReturns -= $value['quantity'];
                    if (empty($quantityReturns)) $quantityReturns = 0;

                    $showInfo = '
                        <div class="quantity-orders" value="' . $quantityOrders . '">SL đã giao: ' . formatNumber($quantityOrders) . '</div>
                        <div class="quantity-returns" value="' . $quantityReturns . '">SL đã trả: ' . formatNumber($quantityReturns) . '</div>
                    ';
                }

                $tdName = '<div class="td-item-name">' . $value['item_name'] . '</div>';
                $tdUnit = '<div class="td-unit">' . $unit['unit'] . '</div>';

                $tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[' . $counter . ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="' . formatNumber($value['quantity']) . '"><div class="show-info text-primary">' . $showInfo . '</div><div class="show-error-item text-danger"></div></div>';

                $tdPrice = '<div class="td-price"><input type="text" name="price[' . $counter . ']" id="price[]" class="form-control price money-format" style="width: 100%;" value="' . formatMoney($value['price']) . '"></div>';
                $tdTotalAmount = '<div class="td-total-amount text-right">' . formatMoney($value['amount']) . '</div>';

                $tdDisPercent = '<div class="td-dis-percent">' .
                    '<input type="number" name="discount_percent_item[' . $counter . ']" id="discount_percent_item" class="form-control discount_percent_item" value="' . $value['discount_percent_item'] . '" style="width: 100%;">' .
                    '</div>';

                $tdDisDirect = '<div class="td-dis-direct">' .
                    '<input type="text" name="discount_direct_item[' . $counter . ']" id="discount_direct_item[]" class="form-control discount_direct_item money-format" style="width: 100%;" value="' . formatMoney($value['discount_direct_amount_item']) . '">' .
                    '</div>';
                $tdGrandTotal = '<div class="td-grand-total text-right">' . formatMoney($value['total_amount']) . '</div>';

                $tdNote = '<div class="td-note"><textarea name="note_items[' . $counter . ']" id="note_items[]" class="form-control" rows="3">' . $value['note_item'] . '</textarea></div>';
                $tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

                $tdQuantityLoss = '<input type="text" name="quantity_loss[' . $counter . ']" class="form-control quantity_loss number-format" value="' . formatNumber($value['quantity_loss']) . '">';
        
                $tdSampleQuantity = '<input type="text" name="quantity_sample[' . $counter . ']" class="form-control quantity_sample number-format" value="' . formatNumber($value['quantity_sample']) . '">';

                $bodyItems .= '<tr>
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdCode . '</td>
                    <td>' . $tdImage . '</td>
                    <td>' . $tdName . '</td>
                    <td>' . $tdUnit . '</td>
                    <td>' . $tdQuantity . '</td>
                    <td>' . $tdQuantityLoss . '</td>
                    <td>' . $tdSampleQuantity . '</td>
                    <td>' . $tdPrice . '</td>
                    <td>' . $tdTotalAmount . '</td>
                    <td>' . $tdNote . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';
                $counter++;
            }
        }
        $data['counter'] = $counter;
        $data['bodyItems'] = $bodyItems;

        $data['taxs'] = $this->site_model->getTaxs();
        $data['returned_goods'] = $returned_goods;
        $data['items'] = $items;
        $data['employees'] = $this->site_model->getStaff();
        $data['tnh'] = $this->tnh;
        $data['id'] = $id;
        $data['title'] = lang('tnh_edit_returned_goods');
        $data['breadcrumb'] = [array('link' => base_url('admin/returned_goods'), 'page' => lang('returned_goods')), array('link' => '#', 'page' => lang('tnh_edit_returned_goods'))];
        $this->load->view('admin/returned_goods/edit', $data);
    }

    public function refereshReferenceReturnedGoods()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $reference_no = getReference('returned_goods');
            if ($this->returned_goods_model->checkExistReturnedGoods($reference_no)) {
                $ct = countReferenceMinus('returned_goods');
                $this->db->select("MAX(right(tbl_returned_goods.reference_no, char_length(tbl_returned_goods.reference_no) - $ct) + 0) as reference_no", false);
                $this->db->from('tbl_returned_goods');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                // $max = subReference($max);
                updateReferenceNormal('returned_goods', $max);
                $reference_no = getReference('returned_goods');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
        die();
    }

    public function agree()
    {
        $data = [];
        if ($this->input->get()) {
            $returned_goods_id = $this->input->get('returned_goods_id');
            $status = $this->input->get('status');
            $returned_goods = $this->returned_goods_model->rowReturnedGoodsById($returned_goods_id);
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();
            if ($returned_goods['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }

            $up = $this->returned_goods_model->updateReturnedGoods($returned_goods_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                @pusherTNHNotfication();
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function print_returned_goods($id)
    {
        ob_end_clean();
        $data = [];
        $returned_goods = $this->returned_goods_model->rowReturnedGoodsById($id);
        $returned_goods['customer_name'] = $this->site_model->rowCustomer($returned_goods['customer_id'])['company_short'];
        $employee = '';
        if (!empty($returned_goods['employee_id'])) {
            $employee = get_staff_full_name($returned_goods['employee_id']);
        }
        $items = $this->returned_goods_model->getReturnedGoodsItems($id);
        $data['title'] = lang('tnh_print_returned_goods');
        $data['type'] = 'P';
        $data['type_print'] = 'P';
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

                $tdNumber = '<td class="text-center" style="width: 7%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 35%;">' . $info['name'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 7%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td class="text-right" style="width: 10%;">' . formatMoney($value['price']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['amount']) . '</td>';
                $tdNote = '<td style="width: 18%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr>
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

        $day = date_format(date_create($returned_goods['date']), 'd');
        $month = date_format(date_create($returned_goods['date']), 'm');
        $year = date_format(date_create($returned_goods['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_p_returned_goods') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_no_returned_goods') . ': ' . $returned_goods['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($returned_goods['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('customers') . ': <span class="bold">' . $returned_goods['customer_name'] . '</span></span><br>
                <span>' . _l('tnh_employees') . ': <span>' . $employee . '</span></span><br>
                <span>' . _l('tnh_handling_solution') . ': <span>' . _l('tnh_' . $returned_goods['handling_solution']) . '</span></span><br>
                <span>' . _l('tnh_note') . ': <span>' . $returned_goods['note'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 7%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 35%;">' . _l('tnh_its') . '</th>
                        <th class="bold text-center" style="width: 7%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('price') . '</th>
                        <th class="bold text-center" style="width: 13%;">' . _l('tnh_subtotal') . '</th>
                        <th class="bold text-center" style="width: 18%;">' . _l('tnh_note') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                        <th class="text-center">' . formatNumber($returned_goods['total_quantity']) . '</th>
                        <th></th>
                        <th class="text-right">' . formatMoney($returned_goods['grand_total']) . '</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <p>' . _l("tnh_money_characters") . ': ' . convert_number_to_words($returned_goods['grand_total']) . '</p>
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

    public function deleteReturnGoods($id)
    {
        $data = [];
        if ($id) {
            $returned_good_items = get_table_where('tbl_returned_goods_items', ['returned_goods_id' => $id], '', 'result_array');
            $returned_goods = $this->returned_goods_model->rowReturnedGoodsById($id);
            // if ($returned_goods['status'] == "un_approved") {
            if ($returned_goods['warehouseman_id'] == 0 || $returned_goods['warehouseman_id'] == NULL) {
                if ($this->returned_goods_model->deleteReturnedGoodsById($id)) {
                    $this->returned_goods_model->deleteReturnedGoodsItemsByRGId($id);

                    if (!empty($returned_goods['order_id'])) {
                        //update qty order
                        foreach ($returned_good_items as $key => $value) {
                            $order_items = get_table_where('tbl_order_items', ['id' => $value['order_item_id']], '', 'row_array');
                            $this->db->where('id', $value['order_item_id']);
                            $this->db->update('tbl_order_items', ['quantity_returned' => $order_items['quantity_returned'] - $value['quantity']]);
                        }
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'returned_goods',
                        'table_obj' => 'tbl_returned_goods',
                        'id_obj' => $id,
                        'name_obj' => $returned_goods['reference_no'],
                        'content' => lang('tnh_his_delete_returned_goods') . ' [' . $returned_goods['reference_no'] . ']',
                        'actions' => 'delete'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function searchOrdersGiveReturnedGoods($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;

        $params = $this->input->get('params');
        $customer = $params['customer_id'];
        $customer_id = 0;
        if (!empty($customer)) {
            $customer_id = explode('__', $customer)[1];
        }

        $data['results'] = $this->returned_goods_model->searchOrdersGiveReturnedGoods($term, $limit, $customer_id);
        if ($id) {
            $order = $this->orders_model->rowOrderById($id);
            $data['row'] = ['id' => $order['id'], 'text' => $order['reference_no']];
        }
        echo json_encode($data);
    }

    public function searchProductAndGoodsGiveReturnedGoods($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $products = $this->returned_goods_model->searchProductsSelect2RG($term, $limit);
        $items = $this->returned_goods_model->searchItemsSelect2RG($term, $limit);
        $data['results'] = [
            [
                'text' => lang('products'), 'children' => $products
            ],
            [
                'text' => lang('ch_items'), 'children' => $items
            ]
        ];
        if ($id) {
            $dt = explode('__', $id);
            $id = $dt[0];
            $type_item = $dt[1];
            if ($type_item == "products") {
                $product = $this->products_model->rowProduct($id);
                $data['row'] = ['id' => $product['id'] . '__' . 'products', 'text' => $product['code']];
            } else if ($type_item == "items") {
                $item = $this->items_model->rowItems($id);
                $data['row'] = ['id' => $item['id'] . '__' . 'items', 'text' => $item['code']];
            }
        }
        echo json_encode($data);
    }

    public function getItemsByOrders($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $order_id = $params['order_id'];

        $tb_returns = "(
            SELECT
                tbl_returned_goods_items.delivery_item_id as delivery_item_id,
                SUM(tbl_returned_goods_items.quantity) as quantity
            FROM tbl_returned_goods_items
            GROUP BY tbl_returned_goods_items.delivery_item_id
        ) tb_returns";


        $this->db->select('
            CONCAT(tbl_products.id, "__products") as id,
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text, 
            tbl_products.name as item_name, 
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, 
            tblunits.unit as unit_name, 
            tbl_order_items.price as price_sell, 
            tbl_products.info as info, 
            CONCAT(tbl_products.category_id, "__products") as category_id, 
            tbl_order_items.id as order_item_id, 
            COALESCE(tb_returns.quantity,0) as quantity_returns, 
            tbl_delivery_items.quantity as quantity_orders,  
            tbl_order_items.discount_percent_item as discount_percent_item, 
            tbl_order_items.discount_direct_amount_item as discount_direct_amount_item,
            tbl_delivery_items.lot_code as lot_code,
            tbl_delivery_items.date_sx as date_sx,
            tbl_delivery_items.date_sd as date_sd,
            tbl_delivery_items.date_use as date_use,
            tbl_delivery_items.unit_id as unit_id,
            tbl_delivery_items.conversion_quantity_unit as conversion_quantity_unit,
            tbl_delivery_items.id as delivery_item_id,
            tbl_delivery_items.warehouse_id as warehouse_id,
            tbl_delivery_items.location_id as location_id,
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
        $this->db->join('tbl_delivery_items', 'tbl_delivery_items.order_item_id = tbl_order_items.id');
        $this->db->join($tb_returns, 'tb_returns.delivery_item_id = tbl_delivery_items.id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
        $this->db->join('tblunits', 'tbl_delivery_items.unit_id = tblunits.unitid', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_orders.id', $order_id);
        $this->db->where('tbl_order_items.type_item', 'products');
        $this->db->where('tbl_delivery_items.quantity >', 0);
        $this->db->where('tbl_delivery_items.quantity > COALESCE(tb_returns.quantity,0)');
        $products = $this->db->get()->result_array();


        $data['results'] = [
            [
                'text' => lang('products'), 'children' => $products
            ],
        ];


        if ($id) {
            $dt = explode('__', $id);
            $id = $dt[0];
            $type_item = $dt[1];
            if ($type_item == "products") {
                $product = $this->products_model->rowProduct($id);
                $data['row'] = ['id' => $product['id'] . '__' . 'products', 'text' => $product['code']];
            }
        }
        echo json_encode($data);
    }

    //hau
    public function get_items_returned_goods()
    {
        $id = $this->input->post('id');
        $data['returned_goods'] = get_table_where('tbl_returned_goods_items', array('returned_goods_id' => $id));
        $returned_goods = get_table_where('tbl_returned_goods', array('id' => $id), '', 'row');
        foreach ($data['returned_goods'] as $key => $value) {
            $data['returned_goods'][$key]['local'] = get_localtion_warehouses_returned_goods(array(), '', $value['location_delivery_id']);
        }
        $data['location_in_stock'] = array();

        echo json_encode($data);
    }
    public function get_location_in_stock_returned_goods()
    {
        $id = $this->input->post('id');
        $local = get_table_where('tbllocaltion_warehouses', array('id' => $id), '', 'row');
        $data['location_in_stock'] = array();
        echo json_encode($data);
    }
    public function can_confirm_warehous()
    {
        // if (!has_permission('releases_deliveries', '', 'approve_warehouse')) {
        //     echo json_encode(array(
        //         'alert_type' => 'warning',
        //         'message' => _l('ch_not_apper')
        //     ));
        //     die;
        // }
        $id = $this->input->post('id');
        $ktr_warehouse_product = get_table_where('tblwarehouse_product', array('import_id' => $id, 'type_export' => 1324, 'quantity_export >' => 0), '', 'row');
        if (!empty($ktr_warehouse_product)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_export_not_apper')
            ));
            die;
        }

        $ktr = get_table_where('tbl_returned_goods', array('id' => $id), '', 'row');
        if (empty($ktr)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_successful_approval_cance')
            ));
            die;
        }
        if ($ktr->warehouseman_id == 0) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_exsit_cancel_confirm_warehouse')
            ));
            die;
        }
        $data = array(
            'warehouseman_id' => 0,
            'warehouseman_date' => NULL
        );
        $success = $this->db->update('tbl_returned_goods', $data, array('id' => $id));
        $message    = _l('ch_no_successful_approval');
        if ($success) {
            $get_code = get_table_where('tbl_returned_goods', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tbl_returned_goods', $id, $get_code->reference_no, 'Cập nhật trạng thái duyệt kho phiếu nhập hàng bán [' . $get_code->reference_no . ']');
            $alert_type = 'success';
            $message    = _l('ch_successful_approval_cance');
            $this->returned_goods_model->decreaseWarehouse($id);
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function confirm_warehous()
    {
        // if (!has_permission('releases_deliveries', '', 'approve_warehouse')) {
        //     echo json_encode(array(
        //         'alert_type' => 'warning',
        //         'message' => _l('ch_q_warehouse')
        //     ));
        //     die;
        // }
        $data = $this->input->post();
        $ktr = get_table_where('tbl_returned_goods', array('id' => $data['id_return']), '', 'row');
        if (empty($ktr)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_successful_approval_cance')
            ));
            die;
        }
        if ($ktr->warehouseman_id != 0) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_exsit_confirm_warehouse')
            ));
            die;
        }
        foreach ($data['items'] as $key => $value) {
            $warehouse_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_to']), '', 'row');
            $this->db->update('tbl_returned_goods_items', array('warehouse_id' => $warehouse_id->warehouse, 'localtion_id' => $value['localtion_to']), array('id' => $value['id']));
        }
        $id = $data['id_return'];
        $return_suppliers = get_table_where('tbl_returned_goods', array('id' => $id), '', 'row');
        $warehouseman_id = $this->input->post('warehouseman_id');
        if (!$id) {
            die('ch_no_items');
        }

        $data = array(
            'warehouseman_id' => get_staff_user_id(),
            'warehouseman_date' => date('Y-m-d H:i:s')
        );
        $success    = $this->db->update('tbl_returned_goods', $data, array('id' => $id));
        $alert_type = 'warning';
        $message    = _l('ch_no_successful_approval');
        if ($success) {
            $get_code = get_table_where('tbl_returned_goods', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tbl_returned_goods', $id, $get_code->reference_no, 'Cập nhật trạng thái duyệt kho phiếu trả hàng bán [' . $get_code->reference_no . ']');

            $alert_type = 'success';
            $message    = _l('ch_successful_approval');
            $this->returned_goods_model->increaseWarehouse($id);
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function getCustomerReturned()
    {
        $data = [];
        $customer_id = $this->input->get('customer_id');
        $customer_id = str_replace('customers__', '', $customer_id);
        $return_goods_id = $this->input->get('return_goods_id');

        $totalReturnGoods = 0;
        if (!empty($return_goods_id)) {
            $return_goods = $this->returned_goods_model->rowReturnedGoodsById($return_goods_id);
            if (!empty($return_goods)) {
                $totalReturnGoods = $return_goods['grand_total'];
            }
        }

        $totalDebt = get_debt_client($customer_id);
        $data['currentDebt'] = $totalDebt + $totalReturnGoods;
        echo json_encode($data);
    }
}
