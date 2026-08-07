<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Releases extends AdminController
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
        $this->tnh = true;

        //permission deliveries
        $this->perViewDeliveries = has_permission('releases_deliveries', '', 'view');
        $this->perViewOwnDeliveries = has_permission('releases_deliveries', '', 'view_own');
        $this->perAddDeliveries = has_permission('releases_deliveries', '', 'create');
        $this->perEditDeliveries = has_permission('releases_deliveries', '', 'edit');
        $this->perDeleteDeliveries = has_permission('releases_deliveries', '', 'delete');
        $this->perPrintDeliveries = has_permission('releases_deliveries', '', 'print');
        $this->perApproveDeliveries = has_permission('releases_deliveries', '', 'approve');
        $this->perPriceDeliveries = has_permission('releases_deliveries', '', 'cost');

        //permission export warehouses
        $this->perViewExportWarehouses = has_permission('export_warehouses', '', 'view');
        $this->perViewOwnExportWarehouses = has_permission('export_warehouses', '', 'view_own');
        $this->perPrintExportWarehouses = has_permission('export_warehouses', '', 'print');
        $this->perDeleteExportWarehouses = has_permission('export_warehouses', '', 'delete');
		$this->is_branch = true;
    }

    public function index()
    {
        if (!$this->perViewDeliveries && !$this->perViewOwnDeliveries) {
            accessDenied();
        }

        $data['status_undelivery'] = $this->deliveries_model->countDeliveriesStatus('status_undelivery');
        $data['status_delivery'] = $this->deliveries_model->countDeliveriesStatus('status_delivery');
        $data['received_certificate'] = $this->deliveries_model->countDeliveriesStatus('received_certificate');
        $data['not_received_certificate'] = $this->deliveries_model->countDeliveriesStatus('not_received_certificate');
        $data['invoice'] = $this->deliveries_model->countDeliveriesStatus('invoice');
        $data['not_invoice'] = $this->deliveries_model->countDeliveriesStatus('not_invoice');
        $data['all'] = $this->deliveries_model->countDeliveriesStatus('all');

        $data['tnh'] = $this->tnh;
        $data['title'] = lang('deliveries');
        $this->load->view('admin/releases/deliveries', $data);
    }

    public function deliveries()
    {
        if (!$this->perViewDeliveries && !$this->perViewOwnDeliveries) {
            accessDenied();
        }

        $data['status_undelivery'] = $this->deliveries_model->countDeliveriesStatus('status_undelivery');
        $data['status_delivery'] = $this->deliveries_model->countDeliveriesStatus('status_delivery');
        $data['received_certificate'] = $this->deliveries_model->countDeliveriesStatus('received_certificate');
        $data['not_received_certificate'] = $this->deliveries_model->countDeliveriesStatus('not_received_certificate');
        $data['invoice'] = $this->deliveries_model->countDeliveriesStatus('invoice');
        $data['not_invoice'] = $this->deliveries_model->countDeliveriesStatus('not_invoice');
        $data['all'] = $this->deliveries_model->countDeliveriesStatus('all');

        $data['tnh'] = $this->tnh;
        $data['title'] = lang('deliveries');
        $this->load->view('admin/releases/deliveries', $data);
    }

    public function add_delivery()
    {
        if (!$this->perAddDeliveries) {
            accessDenied();
        }
        if ($this->input->post('add')) {
            die;
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"),
                'trim|required|is_unique[tbl_deliveries.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('employees', lang("tnh_employees_charge"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $address_delivery = $this->input->post('address_delivery');
                $employee_id = $this->input->post('employees');
                $note = $this->input->post('note');
                // $customer_id = $this->input->post('customers');
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $row_customer = $this->site_model->rowCustomer($customer_id);
                if (empty($row_customer)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_customer_not_exist');
                    echo json_encode($data);
                    die;
                }
                $customer_name = $row_customer['company'];

                $orders_id = $this->input->post('reference_orders');
                $orders_id = explode(',', $orders_id[0]);
                // $order_id = $order_id[0];

                $total_quantity_all = 0;
                $total_amount_items_all = 0;
                $total_tax_items_all = 0;
                $total_discount_percent_items_all = 0;
                $total_discount_direct_items_all = 0;
                $grand_total_items_all = 0;
                $tax_id_all = 0;
                $tax_name_all = 0;
                $tax_rate_all = 0;
                $total_tax_all = 0;
                $discount_percent_all = 0;
                $total_discount_percent_all = 0;
                $total_discount_direct_all = 0;
                $grand_total_all = 0;

                foreach ($orders_id as $key => $order_id) {
                    $order = $this->orders_model->rowOrderById($order_id);
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

                    foreach ($counter as $key => $value) {
                        $order_item_id = $this->input->post('order_item_id')[$value];
                        if (empty($order_item_id)) {
                            continue;
                        }
                        $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                        if (empty($order_item)) {
                            continue;
                        }
                        if ($order_item['order_id'] != $order_id) {
                            continue;
                        }

                        $item_id = $order_item['item_id'];
                        $items_code = $order_item['item_code'];
                        $items_name = $order_item['item_name'];
                        $type_item = $order_item['type_item'];
                        $quantity_delivery = number_unformat($this->input->post('quantity_delivery')[$value]);
                        $quantity = $quantity_delivery;
                        $note_item = $this->input->post('note_item')[$value];

                        //check quantity delivery
                        $quantity_order = $order_item['quantity'];
                        $quantity_had_delivery = $order_item['quantity_delivery'];
                        $quantity_max = $quantity_order - $quantity_had_delivery;
                        if ($quantity_delivery > $quantity_max) {
                            $errorItems .= lang('tnh_quantity_delivery_have_change_please_referesh');
                            break;
                        }

                        $price = $order_item['price'];
                        $amount = $quantity * $price;
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
                        $discount_percent_item = $order_item['discount_percent_item'];
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

                        $items_in[] = [
                            'order_item_id' => $order_item_id,
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
                        ];

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                    }

                    $grand_total = $grand_total_items;

                    if ($tax_rate > 0) {
                        $total_tax = $grand_total_items * ($tax_rate / 100);
                    }
                    $grand_total += $total_tax;
                    if ($discount_percent > 0) {
                        $total_discount_percent = $grand_total * ($discount_percent / 100);
                    }

                    $grand_total -= $total_discount_percent;
                    $grand_total -= $total_discount_direct;

                    $ordersDeliveries[] = [
                        'order_id' => $order_id,
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

                    $total_quantity_all += $total_quantity;
                    $total_amount_items_all += $total_amount_items;
                    $total_tax_items_all += $total_tax_items;
                    $total_discount_percent_items_all += $total_discount_percent_items;
                    $total_discount_direct_items_all += $total_discount_direct_items_all;
                    $grand_total_items_all += $grand_total_items;
                    $tax_id_all += 0;
                    $tax_name_all += 0;
                    $tax_rate_all += 0;
                    $total_tax_all += $total_tax;
                    $discount_percent_all += $discount_percent;
                    $total_discount_percent_all += $total_discount_percent;
                    $total_discount_direct_all += $total_discount_direct;
                    $grand_total_all += $grand_total;
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

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery,
                    'employee_id' => $employee_id,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity_all,
                    'total_amount_items' => $total_amount_items_all,
                    'total_tax_items' => $total_tax_items_all,
                    'total_discount_percent_items' => $total_discount_percent_items_all,
                    'total_discount_direct_items' => $total_discount_direct_items_all,
                    'grand_total_items' => $grand_total_items_all,
                    'tax_id' => $tax_id_all,
                    'tax_name' => $tax_name_all,
                    'tax_rate' => $tax_rate_all,
                    'total_tax' => $total_tax_all,
                    'discount_percent' => $discount_percent_all,
                    'total_discount_percent' => $total_discount_percent_all,
                    'total_discount_direct' => $total_discount_direct_all,
                    'grand_total' => $grand_total_all,
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'order_id' => $this->input->post('reference_orders')[0],
                    'type_id' => 2
                ];

                // print_arrays($options);

                $delivery_id = $this->deliveries_model->insertDeliveries($options);
                if ($delivery_id) {
                    if (getReference('deliveries') == $reference_no) {
                        updateReference('deliveries');
                    }
                    //insert order delivery
                    foreach ($ordersDeliveries as $key => $value) {
                        $value['delivery_id'] = $delivery_id;
                        if ($this->deliveries_model->insertOrdersDeliveries($value)) {
                            $this->orders_model->updateQuantityOrder($value['order_id'], $value['total_quantity'],
                                $plus = 0);
                        }
                    }

                    //end insert order delivery
                    foreach ($items_in as $key => $value) {
                        $value['delivery_id'] = $delivery_id;
                        $delivery_item_id = $this->deliveries_model->insertDeliveryItems($value);
                        if ($delivery_item_id) {
                            $order_item = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                            $quantity_delivery = $order_item['quantity_delivery'] + $value['quantity'];
                            $quantity_not_delivery = $order_item['quantity'] - $quantity_delivery;
                            $upOrderItem = $this->orders_model->updateOrderItemNew($value['order_item_id'], [
                                'quantity_delivery' => $quantity_delivery,
                                'quantity_not_delivery' => $quantity_not_delivery
                            ]);
                        }
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    @pusherTNHNotfication();
                    insertActivityLog([
                        'type_parent_obj' => 'deliveries',
                        'table_obj' => 'tbl_deliveries',
                        'id_obj' => $delivery_id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_add_deliveries') . ' [' . $reference_no . ']',
                        'actions' => 'add'
                    ]);
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
        $data['reference_no'] = getReference('deliveries');
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['taxs'] = $this->site_model->getTaxs();
        $data['tnh'] = true;
        $data['title'] = lang('tnh_add_delivery');
        $data['breadcrumb'] = [
            array('link' => base_url('admin/releases/deliveries'), 'page' => lang('deliveries')),
            array('link' => '#', 'page' => lang('tnh_add_delivery'))
        ];
        $this->load->view('admin/releases/add_delivery', $data);
    }

    public function edit_delivery($id)
    {
        redirect($_SERVER["HTTP_REFERER"]);
        die;
        if (!$this->perEditDeliveries) {
            accessDenied();
        }
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        if (empty($delivery)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($delivery['status'] == "approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($this->input->post('edit')) {
            $data = [];
            if ($delivery['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"),
                    'trim|required|is_unique[tbl_deliveries.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('employees', lang("tnh_employees_charge"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $address_delivery = $this->input->post('address_delivery');
                $employee_id = $this->input->post('employees');
                $note = $this->input->post('note');
                // $customer_id = $this->input->post('customers');
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $row_customer = $this->site_model->rowCustomer($customer_id);
                if (empty($row_customer)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_customer_not_exist');
                    echo json_encode($data);
                    die;
                }
                $customer_name = $row_customer['company'];

                $orders_id = $this->input->post('reference_orders');
                $orders_id = explode(',', $orders_id[0]);
                // $order_id = $order_id[0];

                $total_quantity_all = 0;
                $total_amount_items_all = 0;
                $total_tax_items_all = 0;
                $total_discount_percent_items_all = 0;
                $total_discount_direct_items_all = 0;
                $grand_total_items_all = 0;
                $tax_id_all = 0;
                $tax_name_all = 0;
                $tax_rate_all = 0;
                $total_tax_all = 0;
                $discount_percent_all = 0;
                $total_discount_percent_all = 0;
                $total_discount_direct_all = 0;
                $grand_total_all = 0;

                foreach ($orders_id as $key => $order_id) {
                    $order = $this->orders_model->rowOrderById($order_id);
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
                    $order['count_delivery'] = $order['count_delivery'] - 1;
                    if ($order['count_delivery'] == 0) {
                        $total_discount_direct = $order['total_discount_direct'];
                    } else {
                        $total_discount_direct = 0;
                    }

                    $grand_total = 0;
                    $status = 'un_approved';
                    $errorItems = '';
                    $counter = $this->input->post('counter');

                    foreach ($counter as $key => $value) {
                        $order_item_id = $this->input->post('order_item_id')[$value];
                        if (empty($order_item_id)) {
                            continue;
                        }
                        $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
                        if (empty($order_item)) {
                            continue;
                        }
                        if ($order_item['order_id'] != $order_id) {
                            continue;
                        }

                        $item_id = $order_item['item_id'];
                        $items_code = $order_item['item_code'];
                        $items_name = $order_item['item_name'];
                        $type_item = $order_item['type_item'];
                        $quantity_delivery = number_unformat($this->input->post('quantity_delivery')[$value]);
                        $quantity = $quantity_delivery;
                        $note_item = $this->input->post('note_item')[$value];

                        //check quantity delivery
                        $quantity_order = $order_item['quantity'];
                        $quantity_had_delivery = $order_item['quantity_delivery'];
                        $quantityDeliveryEdit = $this->deliveries_model->rowDeliveryItemByOrderItemId($order_item_id,
                            $id)['quantity_delivery'];
                        $quantity_max = $quantity_order - $quantity_had_delivery + $quantityDeliveryEdit;
                        if ($quantity_delivery > $quantity_max) {
                            $errorItems .= lang('tnh_quantity_delivery_have_change_please_referesh');
                            break;
                        }

                        $price = $order_item['price'];
                        $amount = $quantity * $price;
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
                        $discount_percent_item = $order_item['discount_percent_item'];
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

                        $items_in[] = [
                            'order_item_id' => $order_item_id,
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
                        ];

                        $total_quantity += $quantity;
                        $total_amount_items += $amount;
                        $grand_total_items += $grand_total_item;
                    }

                    $grand_total = $grand_total_items;

                    if ($tax_rate > 0) {
                        $total_tax = $grand_total_items * ($tax_rate / 100);
                    }
                    $grand_total += $total_tax;
                    if ($discount_percent > 0) {
                        $total_discount_percent = $grand_total * ($discount_percent / 100);
                    }

                    $grand_total -= $total_discount_percent;
                    $grand_total -= $total_discount_direct;

                    $ordersDeliveries[] = [
                        'order_id' => $order_id,
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

                    $total_quantity_all += $total_quantity;
                    $total_amount_items_all += $total_amount_items;
                    $total_tax_items_all += $total_tax_items;
                    $total_discount_percent_items_all += $total_discount_percent_items;
                    $total_discount_direct_items_all += $total_discount_direct_items_all;
                    $grand_total_items_all += $grand_total_items;
                    $tax_id_all += 0;
                    $tax_name_all += 0;
                    $tax_rate_all += 0;
                    $total_tax_all += $total_tax;
                    $discount_percent_all += $discount_percent;
                    $total_discount_percent_all += $total_discount_percent;
                    $total_discount_direct_all += $total_discount_direct;
                    $grand_total_all += $grand_total;
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

                $options = [
                    'date' => $date,
                    // 'reference_no' => $reference_no,
                    // 'customer_id' => $customer_id,
                    // 'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery,
                    'employee_id' => $employee_id,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity_all,
                    'total_amount_items' => $total_amount_items_all,
                    'total_tax_items' => $total_tax_items_all,
                    'total_discount_percent_items' => $total_discount_percent_items_all,
                    'total_discount_direct_items' => $total_discount_direct_items_all,
                    'grand_total_items' => $grand_total_items_all,
                    'tax_id' => $tax_id_all,
                    'tax_name' => $tax_name_all,
                    'tax_rate' => $tax_rate_all,
                    'total_tax' => $total_tax_all,
                    'discount_percent' => $discount_percent_all,
                    'total_discount_percent' => $total_discount_percent_all,
                    'total_discount_direct' => $total_discount_direct_all,
                    'grand_total' => $grand_total_all,
                    'status' => $status,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                    // 'order_id' => $this->input->post('reference_orders')[0],
                    // 'type_id' => 2
                ];

                // print_arrays($ordersDeliveries);

                $ordersDeliveriesOld = $this->deliveries_model->getOrdersDeliveries($id);
                $itemsOld = $this->deliveries_model->getDeliveryItems($id);
                $up = $this->deliveries_model->updateDeliveriesById($id, $options);
                if ($up) {

                    //delete
                    $this->deliveries_model->deleteDeliveryItemsByDeliveryID($id);
                    $this->deliveries_model->deleteOrdersDeliveriesByDeliveryId($id);
                    foreach ($itemsOld as $key => $value) {
                        $this->orders_model->updateQuantityOrderItems($value['order_item_id'], $value['quantity'],
                            $minus = 1);
                    }
                    foreach ($ordersDeliveriesOld as $key => $value) {
                        $this->orders_model->updateQuantityOrder($value['order_id'], $value['total_quantity'],
                            $minus = 1);
                    }
                    //

                    //insert order delivery
                    foreach ($ordersDeliveries as $key => $value) {
                        $value['delivery_id'] = $id;
                        if ($this->deliveries_model->insertOrdersDeliveries($value)) {
                            $this->orders_model->updateQuantityOrder($value['order_id'], $value['total_quantity'],
                                $plus = 0);
                        }
                    }

                    //end insert order delivery
                    foreach ($items_in as $key => $value) {
                        $value['delivery_id'] = $id;
                        $delivery_item_id = $this->deliveries_model->insertDeliveryItems($value);
                        if ($delivery_item_id) {
                            $order_item = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                            $quantity_delivery = $order_item['quantity_delivery'] + $value['quantity'];
                            $quantity_not_delivery = $order_item['quantity'] - $quantity_delivery;
                            $upOrderItem = $this->orders_model->updateOrderItemNew($value['order_item_id'], [
                                'quantity_delivery' => $quantity_delivery,
                                'quantity_not_delivery' => $quantity_not_delivery
                            ]);
                        }
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'deliveries',
                        'table_obj' => 'tbl_deliveries',
                        'id_obj' => $id,
                        'name_obj' => $delivery['reference_no'],
                        'content' => lang('tnh_his_edit_deliveries') . ' [' . $delivery['reference_no'] . ']',
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
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $data['taxs'] = $this->site_model->getTaxs();
        $data['delivery'] = $delivery;
        $items = $this->deliveries_model->getDeliveryItems($id);
        $bodyItems = '';
        $counter = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $order = $this->orders_model->rowOrderByOrderItemId($value['order_item_id']);
                $order_item = $this->orders_model->rowOrderItemsById($value['order_item_id']);

                $tdNumber = '<div class="td-number text-center">' . (++$key) . '</div>';
                $tdReferenceOrder = '<div class="td-referece-order">' . $order['reference_no'] . '</div>';
                $tdCode = '<div class="td-code mbot10">' .
                    '<input type="hidden" name="order_item_id[' . $counter . ']" id="order_item_id" class="form-control order_item_id" value="' . $value['order_item_id'] . '">' .
                    '<input type="hidden" name="counter[' . $counter . ']" id="counter" class="form-control counter" value="' . $counter . '">' .
                    $value['item_code'] .
                    '<div class="type-item"></div>' .
                    '</div>';
                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                    '<div class="display-block contract-attachment-wrapper img">' .
                    '<div style="width:45px;">' .
                    '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                    '<div class="">' .
                    '<img src="' . $images . '" style="border-radius: 50%">' .
                    '</div>' .
                    '</a>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>';
                $tdName = '<div class="td-item-name">' . $value['item_name'] . '</div>';
                $tdUnit = '<div class="td-unit">' . $unit['unit'] . '</div>';
                $tdQuantity = '<div class="td-quantity text-center">' . formatNumber($order_item['quantity']) . '</div>';
                $tdQuantityHadDelivery = '<div class="td-quantity-had-delivery text-center">' . formatNumber($order_item['quantity_delivery'] - $value['quantity']) . '</div>';
                $quantityDelivery = $value['quantity'];
                if ($quantityDelivery < 0) {
                    $quantityDelivery = 0;
                }
                $tdQuantityDelivery = '<div class="td-quantity-delivery"><input type="text" name="quantity_delivery[' . $counter . ']" id="quantity_delivery[]" onchange="totalDeliveries()" class="form-control quantity_delivery number-format" value="' . formatNumber($quantityDelivery) . '"><div class="show-error-item text-danger"></div></div>';
                $tdNote = '<div class="td-note">' .
                    '<textarea name="note_item[' . $counter . ']" id="note_item[]" class="form-control" rows="3"></textarea>' .
                    '</div>';
                $tdActions = '<div class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row btn btn-danger"></i></div>';

                $bodyItems .= '<tr>
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdReferenceOrder . '</td>
                    <td>' . $tdCode . '</td>
                    <td>' . $tdImage . '</td>
                    <td>' . $tdName . '</td>
                    <td>' . $tdUnit . '</td>
                    <td>' . $tdQuantity . '</td>
                    <td>' . $tdQuantityHadDelivery . '</td>
                    <td>' . $tdQuantityDelivery . '</td>
                    <td>' . $tdNote . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';
                $counter++;
            }
        }

        $referenceOrder = $this->deliveries_model->rowRefereceNoOrderByDeliveryId($id);
        $data['referenceOrder'] = $referenceOrder;
        $data['counter'] = $counter;
        $data['bodyItems'] = $bodyItems;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['tnh'] = true;
        $data['title'] = lang('tnh_edit_delivery');
        $data['id'] = $id;
        $data['breadcrumb'] = [
            array('link' => base_url('admin/releases/deliveries'), 'page' => lang('deliveries')),
            array('link' => '#', 'page' => lang('tnh_edit_delivery'))
        ];
        $this->load->view('admin/releases/edit_delivery', $data);
    }

    public function getDeliveries()
    {
        if (!$this->perViewDeliveries && !$this->perViewOwnDeliveries) {
            accessDenied($js = true);
        }

		if(!empty($this->is_branch)) {
			$is_admin = true;
			if (!is_admin()) {
				$list_branch = get_list_branch_staff();
				$is_admin = false;
			}
		}

        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $items_search = $this->input->post('items_search');
        $status = $this->input->post('status_table');

        $referenceOrder = "(
            SELECT
                GROUP_CONCAT(tbl_orders.reference_no SEPARATOR '</br>')
            FROM tbl_orders_deliveries
            INNER JOIN tbl_orders ON tbl_orders_deliveries.order_id = tbl_orders.id
            WHERE tbl_orders_deliveries.delivery_id = tbl_deliveries.id
        )";

        $referenceInvoice = "(
            SELECT
                GROUP_CONCAT(tbl_invoices.reference_no SEPARATOR '</br>')
            FROM tbl_invoice_items
            INNER JOIN tbl_invoices ON tbl_invoices.id = tbl_invoice_items.invoice_id
            WHERE tbl_invoice_items.object_id = tbl_deliveries.id
        )";

        $staff_id = get_staff_user_id();
        $ckView = "(
            SELECT FIND_IN_SET($staff_id, tbl_deliveries.list_users)
        )";

        // $custom[] = [
        //     'index' => 5,
        //     'select' => 'reference_orders',
        // ];
        // $custom_select[5] = $referenceOrder;

        $totalMoney = $this->perPriceDeliveries ? 'tbl_deliveries.grand_total' : 0;
        $totalMoneyVnd = $this->perPriceDeliveries ? '(tbl_deliveries.grand_total * tbl_orders.amount_to_vnd)' : 0;


		if(!empty($this->is_branch)) {
			if (!$is_admin) {
				if (!empty($list_branch)) {
					$this->datatables->where('(tbl_deliveries.id_branch IN (' . $list_branch . '))');
				} else {
					$this->datatables->where('tbl_deliveries.id_branch = 0', false, false);
				}
			}
		}

        // $referenceOrder
        // $this->datatables->select("
        //     tbl_deliveries.id as id,
        //     tbl_deliveries.date as date,
        //     tbl_deliveries.reference_no as reference_no,
        //     CONCAT(tbl_deliveries.customer_name,'__',tblclients.is_separate_guest) as customer_name,
        //     tblshipping_client.address as address_delivery,
        //     '' as reference_invoice,
        //     tbl_orders.reference_no as reference_orders,
        //     $totalMoney as grand_total,
        //     $totalMoneyVnd as grand_total_vnd,
        //     CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
        //     tbl_deliveries.warehouseman_id as status,
        //     tbl_deliveries.received_certificate as received_certificate,
        //     tbl_deliveries.note as note,
        //     CONCAT(staff_status.firstname, ' ', staff_status.lastname, '') as user_status,
        //     0 as list_users,
        //     tblbranch.name as name_branch,
        //     ", false)
        //     ->from('tbl_deliveries')
        //     ->join('tblclients', 'tblclients.userid = tbl_deliveries.customer_id', 'inner')
        //     ->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id', 'inner')
        //     ->join('tblshipping_client', 'tblshipping_client.id = tbl_deliveries.address_delivery_id', 'left')
        //     ->join('tblstaff', 'tblstaff.staffid = tbl_deliveries.created_by', 'left')
        //     ->join('tblstaff employees', 'employees.staffid = tbl_deliveries.employee_id', 'left')
        //     ->join('tblstaff staff_status', 'staff_status.staffid = tbl_deliveries.user_status', 'left')
        //     ->join('tblbranch', 'tblbranch.id = tbl_deliveries.id_branch', 'left');

        // $this->datatables->select("
        //     tbl_deliveries.id as id,
        //     tbl_deliveries.date as date,
        //     tbl_deliveries.reference_no as reference_no,
        //     CONCAT(tbl_deliveries.customer_name,'__',tblclients.is_separate_guest) as customer_name,
        //     tblshipping_client.address as address_delivery,
        //     '' as reference_invoice,
        //     tbl_orders.reference_no as reference_orders,
        //     $totalMoney as grand_total,
        //     $totalMoneyVnd as grand_total_vnd,
        //     tbl_deliveries.created_by as created_by,
        //     tbl_deliveries.warehouseman_id as status,
        //     tbl_deliveries.received_certificate as received_certificate,
        //     tbl_deliveries.note as note,
        //     tbl_deliveries.user_status as user_status,
        //     0 as list_users,
        //     tblbranch.name as name_branch,
        //     ", false)
        //     ->from('tbl_deliveries')
        //     ->join('tblclients', 'tblclients.userid = tbl_deliveries.customer_id', 'inner')
        //     ->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id', 'inner')
        //     ->join('tblshipping_client', 'tblshipping_client.id = tbl_deliveries.address_delivery_id', 'left')
        //     ->join('tblbranch', 'tblbranch.id = tbl_deliveries.id_branch', 'left');

        $this->datatables->select("
            tbl_deliveries.id as id,
            tbl_deliveries.date as date,
            tbl_deliveries.reference_no as reference_no,
            tbl_deliveries.customer_id as customer_name,
            tbl_deliveries.address_delivery_id as address_delivery,
            '' as reference_invoice,
            tbl_deliveries.order_id as reference_orders,
            $totalMoney as grand_total,
            $totalMoneyVnd as grand_total_vnd,
            tbl_deliveries.created_by as created_by,
            tbl_deliveries.warehouseman_id as status,
            tbl_deliveries.received_certificate as received_certificate,
            tbl_deliveries.note as note,
            tbl_deliveries.user_status as user_status,
            0 as list_users,
            '' as name_branch,
            ", false)
            ->from('tbl_deliveries')
            ->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id', 'inner');
            // ->join('tblclients', 'tblclients.userid = tbl_deliveries.customer_id', 'inner')
            // ->join('tblshipping_client', 'tblshipping_client.id = tbl_deliveries.address_delivery_id', 'left')
            // ->join('tblbranch', 'tblbranch.id = tbl_deliveries.id_branch', 'left');

        // $this->datatables->custom_ordering($custom);
        // $this->datatables->custom_select($custom_select);
        // print_arrays($this->db->get_compiled_select());

        if (!empty($items_search)) {
            $items_search = explode('__', $items_search);
            $this->datatables->where('EXISTS (
                SELECT tbl_delivery_items.delivery_id
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.delivery_id = tbl_deliveries.id
                AND tbl_delivery_items.item_id = ' . $items_search[0] . '
            )');
        }

        if (!$this->perViewDeliveries) {
            $this->datatables->where('tbl_deliveries.created_by', get_staff_user_id());
        }

        if (!empty($orders_search)) {
            $this->datatables->where("FIND_IN_SET($orders_search, tbl_deliveries.order_id) > 0");
        }

        if (!empty($customer_search)) {
            $this->datatables->where('tbl_deliveries.customer_id', $customer_search);
        }

        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") >=',
                to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }

        if (!empty($status) && $status != 'all') {
            if ($status == 'status_undelivery') {
                $this->datatables->where('tbl_deliveries.warehouseman_id', 0);
            } elseif ($status == 'status_delivery') {
                $this->datatables->where('tbl_deliveries.warehouseman_id >', 0);
            } elseif ($status == 'received_certificate') {
                $this->datatables->where('tbl_deliveries.received_certificate', 1);
            } elseif ($status == 'not_received_certificate') {
                $this->datatables->where('tbl_deliveries.received_certificate', 0);
            } elseif ($status == 'invoice') {
                $this->datatables->where('EXISTS (
                    SELECT tbl_invoice_items.object_id
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
                )');
            } elseif ($status == 'not_invoice') {
                // JOIN tbl_invoices ON tbl_invoices.id = tbl_invoice_items.invoice_id
                $this->datatables->where('NOT EXISTS (
                    SELECT tbl_invoice_items.object_id
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
                )');
            }
        }

        $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/view_delivery/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('deliveries') . '</a>';

        $edit = '<a href="' . base_url('admin/releases/edit_delivery/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('deliveries') . '</a>';
        $print = '<a href="' . base_url('admin/releases/print_delivery/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('deliveries') . '</a>';
        $print_size = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/print_size/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('theo size') . '</a>';
        $editDiscount = $this->perPriceDeliveries ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/edit_discount/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('Cập nhật chiết khấu') . ' </a>' : '';
        $editHaiQuan = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/edit_hai_quan/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i> ' . lang('Cập nhật phiếu hải quan') . ' </a>';

        // $export_warehouse_sales = '<a data-tnh="modal" class="tnh-modal" href="'.base_url('admin/releases/export_warehouse_sales/$1').'" data-toggle="modal" data-target="#myModal"><i class="fa fa-cube"></i> '.lang('tnh_export_warehouse_sales').'</a>';

        $export_warehouse_sales = '<a class="ews" href="' . base_url('admin/releases/export_warehouse_sales/$1') . '"><i class="fa fa-cube"></i> ' . lang('tnh_export_warehouse_sales') . '</a>';

        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/releases/deleteDelivery/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('deliveries') . '</a>';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $print . '<li>
                <li>' . $editDiscount . '<li>
                <li>' . $editHaiQuan . '<li>
                <li class="print_size hide">' . $print_size . '<li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        $aaData = $data->aaData;
        if (!empty($aaData)) {
            $arrDeliveryId = [];
            $arrStaffId = [];
            $arrCustomerId = [];
            $arrOrderId = [];
            $arrShippingId = [];
            foreach ($aaData as $key => $value) {
                $arrDeliveryId[] = $value[0];
                if (!empty($value[9])) {
                    $arrStaffId[] = $value[9];
                }

                if (!empty($value[13])) {
                    $arrStaffId[] = $value[13];
                }

                $customer_id = $value[3] ?? null;
                if (!empty($customer_id)) {
                    $arrCustomerId[] = $customer_id;
                }

                $order_id = $value[6] ?? null;
                if (!empty($order_id)) {
                    $arrOrderId[] = $order_id;
                }

                $shipping_id = $value[4] ?? null;
                if (!empty($shipping_id)) {
                    $arrShippingId[] = $shipping_id;
                }
            }

            if (!empty($arrDeliveryId)) {
                $queryReferenceInvoice = "(
                    SELECT
                        tbl_invoice_items.object_id as object_id,
                        GROUP_CONCAT(tbl_invoices.reference_no SEPARATOR '</br>') as reference_no
                    FROM tbl_invoice_items
                    INNER JOIN tbl_invoices ON tbl_invoices.id = tbl_invoice_items.invoice_id
                    WHERE tbl_invoice_items.object_id IN (".implode(',', $arrDeliveryId).")
                    GROUP BY tbl_invoice_items.object_id
                )";
                $listReferenceInvoice = $this->db->query($queryReferenceInvoice)->result_array();
                if (!empty($listReferenceInvoice)) {
                    $listReferenceInvoice = array_reduce($listReferenceInvoice, function($carry, $item) {
                        $carry[$item['object_id']] = $item;
                        return $carry;
                    });
                }
            }

            if (!empty($arrStaffId)) {
                $arrStaffId = array_unique($arrStaffId);
                $this->db->select("
                    tblstaff.staffid as staffid,
                    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as fullname
                ", false);
                $this->db->from('tblstaff');
                $this->db->where_in('tblstaff.staffid', $arrStaffId);
                $listStaffs = $this->db->get()->result_array();
                if (!empty($listStaffs)) {
                    $listStaffs = array_reduce($listStaffs, function($carry, $item) {
                        $carry[$item['staffid']] = $item;
                        return $carry;
                    });
                }
            }

            if (!empty($arrCustomerId)) {
                $arrCustomerId = array_unique($arrCustomerId);
                $this->db->select("
                    tblclients.userid as userid,
                    tblclients.is_separate_guest as is_separate_guest,
                    tblclients.company as customer_name
                ", false);
                $this->db->from('tblclients');
                $this->db->where_in('tblclients.userid', $arrCustomerId);
                $listCustomers = $this->db->get()->result_array();
                if (!empty($listCustomers)) {
                    $listCustomers = array_reduce($listCustomers, function($carry, $item) {
                        $carry[$item['userid']] = $item;
                        return $carry;
                    });
                }
            }

            if (!empty($arrOrderId)) {
                $arrOrderId = array_unique($arrOrderId);
                $this->db->select("
                    tbl_orders.id as id,
                    tbl_orders.reference_no as reference_no
                ", false);
                $this->db->from('tbl_orders');
                $this->db->where_in('tbl_orders.id', $arrOrderId);
                $listOrders = $this->db->get()->result_array();
                if (!empty($listOrders)) {
                    $listOrders = array_reduce($listOrders, function($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    });
                }
            }

            if (!empty($arrShippingId)) {
                $arrShippingId = array_unique($arrShippingId);
                $this->db->select("
                    tblshipping_client.id as id,
                    tblshipping_client.address as address
                ", false);
                $this->db->from('tblshipping_client');
                $this->db->where_in('tblshipping_client.id', $arrShippingId);
                $listShippings = $this->db->get()->result_array();
                if (!empty($listShippings)) {
                    $listShippings = array_reduce($listShippings, function($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    });
                }
            }

            foreach ($aaData as $key => $value) {
                $delivery_id = $value[0];
                $dtReferenceInvoice = $listReferenceInvoice[$delivery_id] ?? null;
                $reference_invoice = $dtReferenceInvoice['reference_no'] ?? '';
                $aaData[$key][5] = $reference_invoice;

                $created_by = $value[9] ?? null;
                $dtCreatedBy = $listStaffs[$created_by] ?? null;
                $user_status = $value[13] ?? null;
                $dtUserStatus = $listStaffs[$user_status] ?? null;

                $aaData[$key][9] = $dtCreatedBy['fullname'] ?? '';
                $aaData[$key][13] = $dtUserStatus['fullname'] ?? '';

                $customer_id = $value[3] ?? null;
                $customer = $listCustomers[$customer_id] ?? null;
                $customer_name = $customer['customer_name'] ?? '';
                $is_separate_guest = $customer['is_separate_guest'] ?? 0;
                $aaData[$key][3] = $customer_name . '__' . $is_separate_guest;

                $order_id = $value[6] ?? null;
                $order = $listOrders[$order_id] ?? null;
                $reference_orders = $order['reference_no'] ?? '';
                $aaData[$key][6] = $reference_orders;

                $shipping_id = $value[4] ?? null;
                $shipping = $listShippings[$shipping_id] ?? null;
                $address_delivery = $shipping['address'] ?? '';
                $aaData[$key][4] = $address_delivery;
            }
        }
        $data->aaData = $aaData;
        echo json_encode($data);
    }

    public function refereshReferenceDelivery()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $reference_no = getReference('deliveries');
            if ($this->orders_model->checkExistOrders($reference_no)) {
                $ct = countReferenceMinus('deliveries');
                $this->db->select("MAX(right(tbl_deliveries.reference_no, char_length(tbl_deliveries.reference_no) - $ct) + 0) as reference_no",
                    false);
                $this->db->from('tbl_deliveries');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max++;
                // $max = subReference($max);
                updateReferenceNormal('deliveries', $max);
                $reference_no = getReference('deliveries');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    public function view_delivery($id)
    {
        if (!$this->perViewDeliveries && !$this->perViewOwnDeliveries) {
            accessDenied($js = true);
        }

        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        $_order = $this->orders_model->rowOrderById($delivery['order_id']);
        if (!$this->perViewDeliveries) {
            checkMyData($delivery['created_by'], true);
        }
		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_deliveries.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tbl_deliveries.id = 0', false, false);
				}
				$this->db->where('id', $id);
				$ktData = $this->db->get('tbl_deliveries')->row();
				if (empty($ktData)) {
					accessDenied($js = true);
				}
			}
		}

        $referenceOrder = $this->deliveries_model->rowRefereceNoOrderByDeliveryId($id);
        $address_delivery = $this->site_model->rowShippingClient($delivery['address_delivery_id']);
        $items = $this->deliveries_model->getDeliveryItems($id);
        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $order = $this->orders_model->rowOrderByOrderItemId($value['order_item_id']);
                $order_items = $this->db->get_where('tbl_order_items', ['id' => $value['order_item_id']])->row();
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $images = '';
                $info = null;
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
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
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $quantity = $value['quantity'];
                $exchange = $this->orders_model->getOrderItemExchangeView($value['order_item_id']);
                $html_exchange = '';
                if (!empty($exchange)) {
                    foreach ($exchange as $k => $val) {
                        $quantity_exchange = $val['quantity_exchange'];
                        $html_exchange .= '<div class="">' .
                            '<div class="col-md-12" style="padding: 0px;">' . $val['unit_name'] . ' - ' . $val['quantity_exchange'] . '(' . formatNumber($quantity / $quantity_exchange) . ')</div>' .
                            '</div>';
                    }
                }

                $strTypeItems = '';
                if ($type_item == "products") {
                    $strTypeItems = '<span class="label label-success">' . lang($type_item) . '</span>';
                } elseif ($type_item == "items") {
                    $strTypeItems = '<span class="label label-primary">' . lang('ch_items') . '</span>';
                } elseif ($type_item == "materials") {
                    $strTypeItems = '<span class="label label-warning">' . lang('materials') . '</span>';
                }

                $lot_code = !empty($value['lot_code']) ? '<div style="color: green">Lot: ' . $value['lot_code'] . '</div>' : '<div style="color: green">Lot: </div>';
                $date_sx = !empty($value['date_sx']) ? '<div style="color: green">Ngày SX: ' . _dhau($value['date_sx']) . '</div>' : '<div style="color: green">Ngày SX:</div>';
                $date_sd = !empty($value['date_sd']) ? '<div style="color: green">Ngày SD: ' . _dhau($value['date_sd']) . '</div>' : '<div style="color: green">Ngày SD:</div>';

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdCode = '<td>' . $info['code'] . '<div class="type-item mbot10">' . $strTypeItems . '</div></td>';
                $tdName = '<td>' . $order_items->product_name_customer . '</td>';
                $tdReferenceOrder = '<td>' . $order['reference_no'] . '</td>';
                $tdUnit = '<td>' . $unit['unit'] . '</td>';

                $rowWarehouse = $this->site_model->rowWarehouseById($value['warehouse_id']);
                $tdWarehouse = '<td>' . $rowWarehouse['name'] . '</td>';

                $rowLocation = $this->site_model->rowLocationWarehouseById($value['location_id']);
                $tdLocation = '<td>' . $rowLocation['name'] . $lot_code . $date_sx . $date_sd . '</td>';

                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $tdQuantityLoss = '<td class="text-center">' . formatNumber($value['quantity_loss']) . '</td>';
                $tdQuantitySample = '<td class="text-center">' . formatNumber($value['quantity_sample']) . '</td>';
                $tdExchangeQuantity = '<td class="text-center">' . $html_exchange . '</td>';
                $tdUnitPrice = '<td class="text-right">' . formatMoney($value['price']) . '</td>';
                $tdTotalAmount = '<td class="text-right">' . formatMoney($value['amount']) . '</td>';
                $tdTaxItem = '<td class="text-center">' . $value['tax_name_item'] . '</td>';
                $tdDiscountPercent = '<td class="text-center">' . $value['discount_percent_item'] . '</td>';
                $tdDiscountDirect = '<td class="text-right">' . formatMoney($value['discount_direct_amount_item']) . '</td>';
                $tdGrandTotal = '<td class="text-right">' . formatMoney($value['total_amount']) . '</td>';
                $tdNote = '<td>' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdImages . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdReferenceOrder . '
                    ' . $tdUnit . '
                    ' . $tdWarehouse . '
                    ' . $tdLocation . '
                    ' . $tdQuantity . '
                    ' . $tdQuantityLoss . '
                    ' . $tdQuantitySample . '
                    ' . $tdExchangeQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTotalAmount . '
                    ' . $tdDiscountPercent . '
                    ' . $tdDiscountDirect . '
                    ' . $tdGrandTotal . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $data['bodyItems'] = $bodyItems;
        $data['referenceOrder'] = $referenceOrder;

        if (!empty($delivery['employee_id'])) {
            $data['employee'] = get_staff_full_name($delivery['employee_id']);
        }
        $data['address_delivery'] = $address_delivery;
        $data['created_by'] = get_staff_full_name($delivery['created_by']);
        if (!empty($delivery['updated_by'])) {
            $data['updated_by'] = get_staff_full_name($delivery['updated_by']);
        }
        if (!empty($delivery['user_status'])) {
            $data['user_status'] = get_staff_full_name($delivery['user_status']);
        } else {
            $data['user_status'] = '';
        }
        $data['id'] = $id;
        $data['delivery'] = $delivery;
        $data['_order'] = $_order;
        $ckView = checkView('deliveries', $delivery['list_users'], $id);
        $data['flagView'] = $ckView;
        $data['company'] = $this->site_model->rowCustomer($delivery['customer_id']);

        $this->load->view('admin/releases/view_delivery', $data);
    }

    public function print_orders_vs1($id)
    {
        return;
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
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 25%;">' . $info['name'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 6%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td class="text-right" style="width: 10%;">' . formatMoney($value['price']) . '</td>';
                $tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
                $tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';
                $tdNote = '<td style="width: 10%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTax . '
                    ' . $tdDiscount . '
                    ' . $tdTotalAmount . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $day = date_format(date_create($order['date']), 'd');
        $month = date_format(date_create($order['date']), 'm');
        $year = date_format(date_create($order['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_sales_orders') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_orders') . ': ' . $order['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($order['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('customers') . ': <span class="bold">' . $order['customer_name'] . '</span></span><br>
                <span>' . _l('tnh_address_delivery') . ': <span>' . (!empty($address_delivery['address']) ? $address_delivery['address'] : '') . '</span></span><br>
                <span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>
                <span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 25%;">' . _l('tnh_its') . '</th>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('price') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('tnh_taxs') . '</th>
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
                        <th></th>
                        <th class="text-right">' . formatMoney($order['grand_total_items']) . '</th>
                        <th></th>
                    </tr>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tax') . '</th>
                        <th class="text-center"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-right">' . formatMoney($order['total_tax']) . '</th>
                        <th></th>
                    </tr>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_discount') . '</th>
                        <th class="text-center"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-right">' . formatMoney($order['total_discount_percent'] + $order['total_discount_direct']) . '</th>
                        <th></th>
                    </tr>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_grand_total') . '</th>
                        <th class="text-center"></th>
                        <th></th>
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

    public function agree()
    {
        return;
        $data = [];
        if ($this->input->get()) {
            $order_id = $this->input->get('order_id');
            $status = $this->input->get('status');
            $order = $this->orders_model->rowOrderById($order_id);
            $date = date('Y-m-d H:i:s');
            $user_id = get_staff_user_id();
            if ($order['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }

            $up = $this->orders_model->updateOrdersNew($order_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function deleteDelivery($id)
    {
        $data = [];

        if (!$this->perDeleteDeliveries) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        if ($id) {
            $delivery = $this->deliveries_model->rowDeliveriesById($id);

            if ($delivery['warehouseman_id'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_export_warehouse_not_delete');
                echo json_encode($data);
                die;
            }

            if (!checkMyDataTF($delivery['created_by'])) {
                $data['result'] = 0;
                $data['message'] = lang('access_denied');
                echo json_encode($data);
                die;
            }
            $ordersDeliveries = $this->deliveries_model->getOrdersDeliveries($id);
            $items = $this->deliveries_model->getDeliveryItems($id);
            if ($delivery['status'] == "un_approved") {
                if ($this->deliveries_model->deleteDeliveriesById($id)) {
                    $this->deliveries_model->deleteDeliveryItemsByDeliveryID($id);
                    $this->deliveries_model->deleteOrdersDeliveriesByDeliveryId($id);
                    $this->db->where('tbl_delivery_items_columns.delivery_id', $id);
                    $this->db->delete('tbl_delivery_items_columns');

                    foreach ($items as $key => $value) {
                        $this->orders_model->updateQuantityOrderItems($value['order_item_id'], $value['quantity'],
                            $minus = 1);
                    }

                    foreach ($ordersDeliveries as $key => $value) {
                        $this->orders_model->updateQuantityOrder($value['order_id'], $value['total_quantity'],
                            $minus = 1);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'deliveries',
                        'table_obj' => 'tbl_deliveries',
                        'id_obj' => $id,
                        'name_obj' => $delivery['reference_no'],
                        'content' => lang('tnh_his_delete_deliveries') . ' [' . $delivery['reference_no'] . ']',
                        'actions' => 'delete'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('browsed_cannot_be_deleted');
                }
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

    public function getOrdersByDelivery($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id = $params['customer_id'];
        $results = false;
        if (!empty($customer_id)) {
            $arr = explode('__', $customer_id);
            $type_customer = $arr[0];
            $customer_id = $arr[1];
            $results = $this->deliveries_model->searchOrderByCustomerForDeliveries($term, $limit, $customer_id);
        }
        $data['results'] = $results;
        if ($id) {
            // $shipping = $this->site_model->rowShippingClient($id);
            // if (!empty($shipping)) {
            //     $data['row'] = ['id' => $shipping['id'], 'text' => $shipping['address']];
            // } else {
            //     $data['row'] = ['id' => 0, 'text' => 'Not found!'];
            // }
        }
        echo json_encode($data);
    }

    public function getOrdersItemsByOrderId()
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $orders_id = $params['orders_id'];
        $edit = $params['edit'];
        $delivery_id = !empty($params['delivery_id']);
        $results = false;
        if (!empty($orders_id)) {
            $orders_id = explode(",", $orders_id);
            $results = $this->deliveries_model->searchOrderItemForDelivery($term, $limit, $orders_id, $edit);
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function rowOrderItem()
    {
        $data = [];
        if ($this->input->get()) {
            $order_item_id = $this->input->get('order_item_id');
            $order_item = $this->orders_model->rowOrderItemsById($order_item_id);
            $delivery_id = $this->input->get('delivery_id');
            if (!empty($order_item)) {
                $order = $this->orders_model->rowOrderById($order_item['order_id']);
                $type_item = $order_item['type_item'];
                $items_id = $order_item['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if ($delivery_id > 0) {
                    $quantityDeliveryEdit = $this->deliveries_model->rowDeliveryItemByOrderItemId($order_item_id,
                        $delivery_id)['quantity_delivery'];
                    $order_item['quantity_delivery'] = $order_item['quantity_delivery'] - $quantityDeliveryEdit;
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $order_item['unit'] = $unit['unit'];
                $order_item['images'] = $images;
                $order_item['reference_no'] = $order['reference_no'];
            }
            $data['order_item'] = $order_item;
        }
        echo json_encode($data);
    }

    public function getOrderItem()
    {
        $data = [];
        if ($this->input->post()) {
            $orders_id = $this->input->post('orders_id');
            $items = false;
            if (!empty($orders_id)) {
                $orders_id = explode(",", $orders_id);
                $items = $this->deliveries_model->getOrderItemForDelivery($orders_id);
                foreach ($items as $key => $value) {
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }
                    } elseif ($type_item == "items") {
                        $info = $this->items_model->rowItems($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit']);
                        if (!empty($info['avatar'])) {
                            $images = base_url($info['avatar']);
                        }
                    }
                    if (empty($images)) {
                        $images = base_url('assets/images/tnh/no_image.png');
                    }
                    $items[$key]['unit'] = $unit['unit'];
                    $items[$key]['images'] = $images;
                }
            }
            $data['items'] = $items;
        }
        echo json_encode($data);
    }

    public function print_delivery($id)
    {
        if (!$this->perPrintDeliveries) {
            accessDenied($js = true);
        }

        ob_end_clean();
        $data = [];
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        $address_delivery = $this->site_model->rowShippingClient($delivery['address_delivery_id']);
        $employee = '';
        if (!empty($delivery['employee_id'])) {
            $employee = get_staff_full_name($delivery['employee_id']);
        }
        $company = $this->site_model->rowCustomer($delivery['customer_id']);
        if ($delivery['person_contact_id']) {
            $contact = get_table_where('tblcontacts', ['id' => $delivery['person_contact_id']], '', 'row_array');
        }
        
        if (empty($contact)) {
            $contact = get_table_where('tblcontacts', ['userid' => $delivery['customer_id']], '', 'row_array');
        }
        $items = $this->deliveries_model->getDeliveryItems($id);

        $this->db->select('*,SUM(quantity) as quantity');
        $this->db->from('tbl_delivery_items');
        $this->db->where('tbl_delivery_items.delivery_id', $id);
        $this->db->group_by('tbl_delivery_items.item_id,tbl_delivery_items.type_item');
        $itemsNew = $this->db->get()->result_array();
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_delivery');
        $data['type'] = 'P';
        $data['img'] = '';

        $orderItemsColumnsNewVs1 = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $order_item_id = $value['order_item_id'];
                $note_item = $value['note_item'];
                $unit_id = $value['unit_id'];
                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $dtOrderItem['ct_counter_item'];
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $this->db->select('tbl_delivery_items_columns.*');
                    $this->db->from('tbl_delivery_items_columns');
                    $this->db->where('tbl_delivery_items_columns.delivery_item_id', $value['id']);
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
                                            vn_to_str($vO['columns_value']) => $vO['columns_name'],
                                        ];
                                        break;
                                    }
                                }
                                $arrNew = array_merge($arrNew, $columns_name);
                            }
                            $orderItemsColumnsNew[$i] = $arrNew;
                            foreach ($orderItemsColumns as $kO => $vO) {
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
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
                                    continue;
                                } elseif ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                    $quantity_loss = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                    continue;
                                } elseif ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                    $sample_quantity_item = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                    continue;
                                } elseif ($vO['columns_value'] == 'quantity_loss_new' && $i == $vO['counter_items_number']) {
                                    $quantity_loss_new = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_loss_new'] = $quantity_loss_new;
                                    continue;
                                }
                            }
                        }
                    }

                    if (!empty($orderItemsColumnsNew)) {
                        foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                            if (empty($vvv)) {
                                continue;
                            }
                            $columns_name_new = 'default';
                            if (!empty($productsColumns)) {
                                foreach ($productsColumns as $k => $v) {
                                    $name_check = vn_to_str($v['name']);
                                    if (!empty($vvv[$name_check])) {
                                        $columns_name_new .= $vvv[$name_check].'__';
                                    }
                                }
                            }
                            $columns_name_new = trim($columns_name_new, '__');
                            $check_key = $vvv['code'].'__'.$vvv['command'].'__'.$items_id.'__'.$type_item;
                            if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
                                $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
                                $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
                                $orderItemsColumnsNewVs1[$check_key]['quantity_loss_new'] += $vvv['quantity_loss_new'];
                            } else {
                                $orderItemsColumnsNewVs1[$check_key] = $vvv;
                                $orderItemsColumnsNewVs1[$check_key]['item_id'] = $items_id;
                                $orderItemsColumnsNewVs1[$check_key]['type_item'] = $type_item;
                                $orderItemsColumnsNewVs1[$check_key]['order_item_id'] = $order_item_id;
                                $orderItemsColumnsNewVs1[$check_key]['note_item'] = $note_item;
                                $orderItemsColumnsNewVs1[$check_key]['unit_id'] = $unit_id;
                            }
                        }
                    }
                }
            }
        }

        $orderItemsColumnsNewVs2 = [];
        if (!empty($orderItemsColumnsNewVs1)) {
            foreach ($orderItemsColumnsNewVs1 as $key => $value) {
                $item_id = $value['item_id'];
                $type_item = $value['type_item'];
                $check_key = $item_id.'__'.$type_item;
                unset($value['item_id']);
                unset($value['type_item']);
                $orderItemsColumnsNewVs2[$check_key][] = $value;
            }
        }

        $bodyItems = '';
        $ii = 1;
        $totalQuanityLoss = 0;
        if (!empty($orderItemsColumnsNewVs1)) {
            foreach ($orderItemsColumnsNewVs1 as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($value['unit_id']);
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }

                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);

                $tdName = '<td style="width: 26%;font-family: kozgopromedium;font-size:10px">'.$dtOrderItem['product_name_customer'].'</td>';
                $tdUnit = '<td class="text-center" style="width: 7%;">'.$unit['unit'].'</td>';
                $tdNote = '<td style="width: 18%;">'.$value['note_item'].'</td>';

                $htmlOrderColumns = '';
                $thSub = '';
                $trHtmlChild = '';
                $productsColumns = $this->products_model->getProductsColumns($items_id);
                $styleTd = '';
                if (!empty($productsColumns)) {
                    $styleTd = 'width: '. 72 / count($productsColumns).'%';
                    foreach ($productsColumns as $k => $v) {
                        $thSub .= '<th class="text-center" style="'.$styleTd.'">'.$v['name'].'</th>';
                    }
                }
                $order_code = $value['code'];
                $command = $value['command'];
                $quantity_put = $value['quantity_put'];
                $quantity_loss = $value['quantity_loss'];
                $sample_quantity_item = $value['sample_quantity_item'];
                $quantity_loss_new = !empty($value['quantity_loss_new']) ? $value['quantity_loss_new'] : 0;
                $trHtmlColumns = '';
                $columns_name_new = '';
                if (!empty($productsColumns)) {
                    foreach ($productsColumns as $k => $v) {
                        $name_check = vn_to_str($v['name']);
                        if (!empty($value[$name_check])) {
                            $columns_name_new = $value[$name_check];
                            $trHtmlColumns .= '
                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">'.$columns_name_new.'</td>';
                        } else {
                            $trHtmlColumns .= '
                                        <td class="text-center" style="font-family: kozgopromedium;font-size:11px">'.$columns_name_new.'</td>';
                        }
                    }
                }

                $tdOrderCode = '<td class="text-center" style="width: 15%">'.$order_code.'</td>';

                $tdCommand = '<td class="text-center" style="width: 16%">'.$command.'</td>';

                $tdQuantityPut = '<td class="text-center" style="width: 13%">'.formatNumber($quantity_put).'</td>';

                $tdQuantityLossNew = '<td class="text-center" style="width: 8%">
                                '.formatNumber($quantity_loss_new).'
                            </td>';

                $totalQuanityLoss += $quantity_loss_new;


                if (empty($trHtmlColumns) && empty($order_code)) {
                    continue;
                }
                $stt = $ii;
                // '.$tdQuantityLossNew.'
                $tdNumberChild = '<td class="text-center" style="width: 5%">'.$stt.'</td>';
                $bodyItems .= '<tr class="not-tr">
                                '.$tdNumberChild.'
                                '.$tdOrderCode.'
                                '.$tdCommand.'
                                '.$tdName.'
                                '.$tdQuantityPut.'
                                '.$tdUnit.'
                                '.$tdNote.'
                            </tr>';
                $htmlOrderColumns .= $trHtmlChild;
                $ii++;
            }
        }

        // <th class="text-center" style="width: 8%">'.lang('SL loss').'</th>
        $tdHead = '<thead>
            <tr class="bold">
                <th class="text-center" style="width: 5%">'.lang('tnh_numbers').'
                </th>
                <th class="text-center" style="width: 15%">'.lang('MS đơn đặt').'</th>
                <th class="text-center" style="width: 16%">'.lang('Đơn đặt').'</th>
                <th class="text-center" style="width: 26%">'.lang('Tên hàng').'</th>
                <th class="text-center" style="width: 13%">'.lang('tnh_quantity_put').'</th>
                <th class="text-center" style="width: 7%">'.lang('ĐVT').'</th>
                <th class="text-center" style="width: 18%">'.lang('Ghi chú').'</th>
            </tr>
        </thead>';

        // <th class="text-center">'.formatNumber($totalQuanityLoss).'</th>
        $tfoot = '<tfoot>
            <tr class="bold">
                <th class="text-right" colspan="2">'._l('tnh_total').'</th>
                <th></th>
                <th></th>
                <th class="text-center">'.formatNumber($delivery['total_quantity']).'</th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>';


        $day = date_format(date_create($delivery['date']), 'd');
        $month = date_format(date_create($delivery['date']), 'm');
        $year = date_format(date_create($delivery['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $phoneContact = '';
        if (!empty($contact) && !empty($contact['phonenumber'])) {
            $phoneContact = ' ('.$contact['phonenumber'].')';
        }
        $order = get_table_where('tbl_orders', ['id' => $delivery['order_id']], '', 'row_array');
        $typeOrder = get_table_where('tbl_type_orders', ['id' => $order['type_orders']], '', 'row_array');

        $note_delivery = '';
        $contact_delivery = '';
        if (!empty($order['note'])) {
            $note_delivery = '<span style="line-height: 10px">'._l('Ghi chú').':<span>'.(!empty($order) ? $order['note'] : '').'</span></span>';
        } else {
            $contact_delivery = '<span style="line-height: 10px">'._l('Người liên hệ').':<span>'.(!empty($contact) ? $contact['firstname'].$phoneContact : '').'</span></span>';
        }
        $font_size = '12px';
        echo '
            <h1 class="text-center uppercase">'.lang('tnh_print_delivery').'</h1>
            <span style="font-size: '.$font_size.'" class="text-right">
                <span class="italic">' . _l('Loại đơn hàng') . ': ' . $typeOrder['name'] . '</span><br><span class="italic">'._l('Số giao hàng').': '.$delivery['reference_no'].'</span><br><span class="italic">'._l('date').': '._d($delivery['date'], true).'</span>
            </span>
            <p style="font-size: '.$font_size.'"><span style="line-height: 10px">'._l('Mã đơn hàng').':<span class="bold">'.$order['reference_no'].'</span></span><br><span style="line-height: 10px">'._l('customers').':<span class="bold">'.$company['company'].'</span></span><br><span style="line-height: 10px">'._l('tnh_address_delivery').':<span>'.(!empty($address_delivery['address']) ? $address_delivery['address'] : '').'</span></span><br>'.$contact_delivery.$note_delivery.'
            </p>
            <table class="" cellspacing="0" cellpadding="5" style="width: 100%;" border="1">
                '.$tdHead.'
                <tbody>
                    '.$bodyItems.'
                </tbody>
                '.$tfoot.'
            </table>
            <br>
            <br>
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
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'delivery';
        $pdf = @print_pdf_dt_delivery($data);
        $type = 'I';
        $pdf->Output(slug_it('123').'.pdf', $type);
    }

    public function print_delivery_old_vs1($id)
    {
        if (!$this->perPrintDeliveries) {
            accessDenied($js = true);
        }

        ob_end_clean();
        $data = [];
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        $address_delivery = $this->site_model->rowShippingClient($delivery['address_delivery_id']);
        $employee = '';
        if (!empty($delivery['employee_id'])) {
            $employee = get_staff_full_name($delivery['employee_id']);
        }
        $company = $this->site_model->rowCustomer($delivery['customer_id']);
        $contact = get_table_where('tblcontacts', ['userid' => $delivery['customer_id']], '', 'row_array');
        $items = $this->deliveries_model->getDeliveryItems($id);

        $this->db->select('*,SUM(quantity) as quantity');
        $this->db->from('tbl_delivery_items');
        $this->db->where('tbl_delivery_items.delivery_id', $id);
        $this->db->group_by('tbl_delivery_items.item_id,tbl_delivery_items.type_item');
        $itemsNew = $this->db->get()->result_array();
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_delivery');
        $data['type'] = 'P';
        $data['img'] = '';

        $orderItemsColumnsNewVs1 = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $dtOrderItem['ct_counter_item'];
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $this->db->select('tbl_delivery_items_columns.*');
                    $this->db->from('tbl_delivery_items_columns');
                    $this->db->where('tbl_delivery_items_columns.delivery_item_id', $value['id']);
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
                                            vn_to_str($vO['columns_value']) => $vO['columns_name']
                                        ];
                                        break;
                                    }
                                }
                                $arrNew = array_merge($arrNew, $columns_name);
                            }
                            $orderItemsColumnsNew[$i] = $arrNew;
                            foreach ($orderItemsColumns as $kO => $vO) {
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
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
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

                    if (!empty($orderItemsColumnsNew)) {
                        foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                            if (empty($vvv)) {
                                continue;
                            }
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
                            $check_key = $vvv['code'] . '__' . $vvv['command'] . '__' . $items_id . '__' . $type_item;
                            if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
                                $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
                                $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
                            } else {
                                $orderItemsColumnsNewVs1[$check_key] = $vvv;
                                $orderItemsColumnsNewVs1[$check_key]['item_id'] = $items_id;
                                $orderItemsColumnsNewVs1[$check_key]['type_item'] = $type_item;
                            }
                        }
                    }
                }
            }
        }

        $orderItemsColumnsNewVs2 = [];
        if (!empty($orderItemsColumnsNewVs1)) {
            foreach ($orderItemsColumnsNewVs1 as $key => $value) {
                $item_id = $value['item_id'];
                $type_item = $value['type_item'];
                $check_key = $item_id . '__' . $type_item;
                unset($value['item_id']);
                unset($value['type_item']);
                $orderItemsColumnsNewVs2[$check_key][] = $value;
            }
        }
        $bodyItems = '';

        if (!empty($itemsNew)) {
            foreach ($itemsNew as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }

                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);

                $htmlOrderColumns = '';
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $styleTd = '';
                    if (!empty($productsColumns)) {
                        $styleTd = 'width: ' . 72 / count($productsColumns) . '%';
                        foreach ($productsColumns as $k => $v) {
                            $thSub .= '<th class="text-center" style="' . $styleTd . '">' . $v['name'] . '</th>';
                        }
                    }
                    $ii = 1;
                    $check_key = $items_id . '__' . $type_item;
                    $orderItemsColumnsNewVs3 = $orderItemsColumnsNewVs2[$check_key];
                    if (!empty($orderItemsColumnsNewVs3)) {
                        foreach ($orderItemsColumnsNewVs3 as $kk => $vv) {
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


                            if (empty($trHtmlColumns) && empty($order_code)) {
                                continue;
                            }
                            $stt = $ii;
                            $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $tdOrderCode . '
                                ' . $tdCommand . '
                                ' . $tdQuantityPut . '
                            </tr>';
                            $ii++;
                        }
                    }
                    $htmlOrderColumns .= '<table class="table" border="1">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">' . lang('tnh_numbers') . '
                                    </th>
                                    <th class="text-center" style="width: 35%">' . lang('Mã đơn đặt') . '</th>
                                    <th class="text-center" style="width: 30%">' . lang('Chỉ lệnh') . '</th>
                                    <th class="text-center" style="width: 30%">' . lang('tnh_quantity_put') . '</th>
                                </tr>
                            </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        ';
                }

                if ($this->perPriceDeliveries) {
                    $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                    $tdCode = '<td style="width: 20%;">' . $info['code'] . '</td>';
                    $tdName = '<td style="width: 25%;font-family: kozgopromedium;font-size:11px">' . $dtOrderItem['product_name_customer'] . '</td>';
                    $tdUnit = '<td class="text-center" style="width: 13%;">' . $unit['unit'] . '</td>';
                    $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';
                    $tdUnitPrice = '<td class="text-right" style="width: 15%;">' . formatMoney($value['price']) . '</td>';
                    // $tdTax = '<td class="text-right" style="width: 10%;">'.formatMoney($value['tax_amount_item']).'</td>';
                    $tdDiscount = '<td class="text-right" style="width: 15%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                    $tdTotalAmount = '<td class="text-right" style="width: 15%;">' . formatMoney($value['total_amount']) . '</td>';

                    $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                    $name_type_print = '';
                    if (!empty($typePrint)) {
                        $name_type_print = $typePrint['name'];
                    }
                    $tdType = '<td class="text-center" style="width: 15%;">' . $name_type_print . '</td>';
                    $tdNote = '<td style="width: 21%;">' . $value['note_item'] . '</td>';


                    $bodyItems .= '<tr nobr="true">
                        ' . $tdNumber . '
                        ' . $tdCode . '
                        ' . $tdName . '
                        ' . $tdUnit . '
                        ' . $tdQuantity . '
                        ' . $tdNote . '
                    </tr>
                    <tr>
                        <td colspan="8">
                            ' . $htmlOrderColumns . '
                        </td>
                    </tr>';
                } else {
                    $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                    $tdCode = '<td style="width: 20%;">' . $info['code'] . '</td>';
                    $tdName = '<td style="width: 25%;font-family: kozgopromedium;font-size:11px">' . $dtOrderItem['product_name_customer'] . '</td>';
                    $tdUnit = '<td class="text-center" style="width: 13%;">' . $unit['unit'] . '</td>';
                    $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';
                    $tdNote = '<td style="width: 21%;">' . $value['note_item'] . '</td>';

                    $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                    $name_type_print = '';
                    if (!empty($typePrint)) {
                        $name_type_print = $typePrint['name'];
                    }
                    $tdType = '<td class="text-center" style="width: 15%;">' . $name_type_print . '</td>';

                    $bodyItems .= '<tr nobr="true">
                        ' . $tdNumber . '
                        ' . $tdCode . '
                        ' . $tdName . '
                        ' . $tdUnit . '
                        ' . $tdQuantity . '
                        ' . $tdNote . '
                    </tr>
                    <tr>
                        <td colspan="8">' . $htmlOrderColumns . '
                        </td>
                    </tr>';
                }
            }
        }

        if ($this->perPriceDeliveries) {
            $tdHead = '<thead>
                <tr style="background-color: #ddd;">
                    <th class="bold text-center" style="width: 6%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</th>
                    <th class="bold text-center" style="width: 20%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</th>
                    <th class="bold text-center" style="width: 25%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</th>
                    <th class="bold text-center" style="width: 13%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</th>
                    <th class="bold text-center" style="width: 21%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</th>
                </tr>
            </thead>';

            $tfoot = '<tfoot>
                <tr class="bold" style="background-color: #ddd;">
                    <th class="text-right" colspan="4">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($delivery['total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>';
        } else {
            $tdHead = '<thead>
                <tr style="background-color: #ddd;">
                    <th class="bold text-center" style="width: 6%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã SP') . '</th>
                    <th class="bold text-center" style="width: 25%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên SP') . '</th>
                    <th class="bold text-center" style="width: 8%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Loại hình in') . '</th>
                    <th class="bold text-center" style="width: 16%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</th>
                </tr>
            </thead>';

            $tfoot = '<tfoot>
                <tr class="bold" style="background-color: #ddd;">
                    <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($delivery['total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>';
        }

        $day = date_format(date_create($delivery['date']), 'd');
        $month = date_format(date_create($delivery['date']), 'm');
        $year = date_format(date_create($delivery['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $phoneContact = '';
        if (!empty($contact) && !empty($contact['phonenumber'])) {
            $phoneContact = ' (' . $contact['phonenumber'] . ')';
        }
        $order = get_table_where('tbl_orders', ['id' => $delivery['order_id']], '', 'row_array');
        $font_size = '12px';
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_print_delivery') . '</h1>
            <span style="font-size: ' . $font_size . '" class="text-center">
                <span class="italic">' . _l('Số giao hàng') . ': ' . $delivery['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($delivery['date'], true) . '</span>
            </span>
            <p style="font-size: ' . $font_size . '"><span>' . _l('Mã đơn hàng') . ':<span class="bold">' . $order['reference_no'] . '</span></span><br><span>' . _l('customers') . ':<span class="bold">' . $company['company'] . '</span></span><br><span>' . _l('tnh_address_delivery') . ':<span>' . (!empty($address_delivery['address']) ? $address_delivery['address'] : '') . '</span></span><br><span>' . _l('Người liên hệ') . ':<span>' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</span></span><br>
            </p>
            <table class="" cellspacing="0" cellpadding="5" style="width: 100%; border-style: soild; border-color: black;">
                ' . $tdHead . '
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                ' . $tfoot . '
            </table>
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
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'delivery';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function print_delivery_old($id)
    {
        if (!$this->perPrintDeliveries) {
            accessDenied($js = true);
        }

        ob_end_clean();
        $data = [];
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        $address_delivery = $this->site_model->rowShippingClient($delivery['address_delivery_id']);
        $employee = '';
        if (!empty($delivery['employee_id'])) {
            $employee = get_staff_full_name($delivery['employee_id']);
        }
        $company = $this->site_model->rowCustomer($delivery['customer_id']);
        $contact = get_table_where('tblcontacts', ['userid' => $delivery['customer_id']], '', 'row_array');
        $items = $this->deliveries_model->getDeliveryItems($id);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_delivery');
        $data['type'] = 'P';
        $data['img'] = '';


        $bodyItems = '';
        $totalBox = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }

                $dtBox = $this->orders_model->getOrderItemExchangeBox($value['order_item_id']);

                $box = !empty($dtBox['quantity_exchange']) ? $value['quantity'] / $dtBox['quantity_exchange'] : 0;
                if (!empty($box)) {
                    $totalBox += $box;
                }
                $quantityBox = $box;
                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);

                $htmlOrderColumns = '';
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $dtOrderItem['ct_counter_item'];
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $this->db->select('tbl_delivery_items_columns.*');
                    $this->db->from('tbl_delivery_items_columns');
                    $this->db->where('tbl_delivery_items_columns.delivery_item_id', $value['id']);
                    $orderItemsColumns = $this->db->get()->result_array();
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
                            $order_code = '';
                            $command = '';
                            $quantity_put = '';
                            $quantity_loss = '';
                            $sample_quantity_item = '';
                            foreach ($orderItemsColumns as $kO => $vO) {
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
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
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
                    $orderItemsColumnsNewVs1 = [];
                    if (!empty($orderItemsColumnsNew)) {
                        foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                            if (empty($vvv)) {
                                continue;
                            }
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
                            $check_key = $vvv['code'] . '__' . $vvv['command'];
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


                            if (empty($trHtmlColumns) && empty($order_code)) {
                                continue;
                            }
                            $stt = $ii;
                            $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $tdOrderCode . '
                                ' . $tdCommand . '
                                ' . $tdQuantityPut . '
                            </tr>';
                            $ii++;
                        }
                    }
                    $htmlOrderColumns .= '<table class="table" border="1">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">' . lang('tnh_numbers') . '
                                    </th>
                                    <th class="text-center" style="width: 35%">' . lang('Mã đơn đặt') . '</th>
                                    <th class="text-center" style="width: 30%">' . lang('Chỉ lệnh') . '</th>
                                    <th class="text-center" style="width: 30%">' . lang('tnh_quantity_put') . '</th>
                                </tr>
                            </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        ';
                }

                if ($this->perPriceDeliveries) {
                    $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                    $tdCode = '<td style="width: 20%;">' . $info['code'] . '</td>';
                    $tdName = '<td style="width: 25%;font-family: kozgopromedium;font-size:11px">' . $dtOrderItem['product_name_customer'] . '</td>';
                    $tdUnit = '<td class="text-center" style="width: 13%;">' . $unit['unit'] . '</td>';
                    $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';
                    $tdQuantityBox = '<td class="text-center" style="width: 15%;">' . formatNumber($quantityBox) . '</td>';

                    $tdUnitPrice = '<td class="text-right" style="width: 15%;">' . formatMoney($value['price']) . '</td>';
                    // $tdTax = '<td class="text-right" style="width: 10%;">'.formatMoney($value['tax_amount_item']).'</td>';
                    $tdDiscount = '<td class="text-right" style="width: 15%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                    $tdTotalAmount = '<td class="text-right" style="width: 15%;">' . formatMoney($value['total_amount']) . '</td>';

                    $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                    $name_type_print = '';
                    if (!empty($typePrint)) {
                        $name_type_print = $typePrint['name'];
                    }
                    $tdType = '<td class="text-center" style="width: 15%;">' . $name_type_print . '</td>';
                    $tdNote = '<td style="width: 21%;">' . $value['note_item'] . '</td>';


                    $bodyItems .= '<tr nobr="true">
                        ' . $tdNumber . '
                        ' . $tdCode . '
                        ' . $tdName . '
                        ' . $tdUnit . '
                        ' . $tdQuantity . '
                        ' . $tdNote . '
                    </tr>
                    <tr>
                        <td colspan="8">
                            ' . $htmlOrderColumns . '
                        </td>
                    </tr>';
                } else {
                    $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                    $tdCode = '<td style="width: 20%;">' . $info['code'] . '</td>';
                    $tdName = '<td style="width: 25%;font-family: kozgopromedium;font-size:11px">' . $dtOrderItem['product_name_customer'] . '</td>';
                    $tdUnit = '<td class="text-center" style="width: 13%;">' . $unit['unit'] . '</td>';
                    $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';
                    $tdQuantityBox = '<td class="text-center" style="width: 15%;">' . formatNumber($quantityBox) . '</td>';
                    $tdNote = '<td style="width: 21%;">' . $value['note_item'] . '</td>';

                    $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                    $name_type_print = '';
                    if (!empty($typePrint)) {
                        $name_type_print = $typePrint['name'];
                    }
                    $tdType = '<td class="text-center" style="width: 15%;">' . $name_type_print . '</td>';

                    $bodyItems .= '<tr nobr="true">
                        ' . $tdNumber . '
                        ' . $tdCode . '
                        ' . $tdName . '
                        ' . $tdUnit . '
                        ' . $tdQuantity . '
                        ' . $tdNote . '
                    </tr>
                    <tr>
                        <td colspan="8">' . $htmlOrderColumns . '
                        </td>
                    </tr>';
                }
            }
        }

        if ($this->perPriceDeliveries) {
            $tdHead = '<thead>
                <tr style="background-color: #ddd;">
                    <th class="bold text-center" style="width: 6%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</th>
                    <th class="bold text-center" style="width: 20%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</th>
                    <th class="bold text-center" style="width: 25%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</th>
                    <th class="bold text-center" style="width: 13%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</th>
                    <th class="bold text-center" style="width: 21%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</th>
                </tr>
            </thead>';

            $tfoot = '<tfoot>
                <tr class="bold" style="background-color: #ddd;">
                    <th class="text-right" colspan="4">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($delivery['total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>';
        } else {
            $tdHead = '<thead>
                <tr style="background-color: #ddd;">
                    <th class="bold text-center" style="width: 6%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã SP') . '</th>
                    <th class="bold text-center" style="width: 25%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên SP') . '</th>
                    <th class="bold text-center" style="width: 8%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Loại hình in') . '</th>
                    <th class="bold text-center" style="width: 16%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</th>
                </tr>
            </thead>';

            $tfoot = '<tfoot>
                <tr class="bold" style="background-color: #ddd;">
                    <th class="text-right" colspan="3">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($delivery['total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>';
        }

        $day = date_format(date_create($delivery['date']), 'd');
        $month = date_format(date_create($delivery['date']), 'm');
        $year = date_format(date_create($delivery['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $phoneContact = '';
        if (!empty($contact) && !empty($contact['phonenumber'])) {
            $phoneContact = ' (' . $contact['phonenumber'] . ')';
        }
        $order = get_table_where('tbl_orders', ['id' => $delivery['order_id']], '', 'row_array');
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_print_delivery') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_orders') . ': ' . $delivery['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($delivery['date'], true) . '</span>
            </span>
            <p><span>' . _l('Mã đơn hàng') . ':<span class="bold">' . $order['reference_no'] . '</span></span><br><span>' . _l('customers') . ':<span class="bold">' . $company['company'] . '</span></span><br><span>' . _l('tnh_address_delivery') . ':<span>' . (!empty($address_delivery['address']) ? $address_delivery['address'] : '') . '</span></span><br><span>' . _l('Người liên hệ') . ':<span>' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</span></span><br><span>' . _l('tnh_note') . ':<span>' . $delivery['note'] . '</span></span><br>
            </p>
            <table class="" cellspacing="0" cellpadding="5" style="width: 100%; border-style: soild; border-color: black;">
                ' . $tdHead . '
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                ' . $tfoot . '
            </table>
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
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'delivery';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function export_warehouse_sales($id)
    {
        redirect($_SERVER["HTTP_REFERER"]);
        die;
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        $items = $this->deliveries_model->getDeliveryItemsTotal($id);
        if (empty($delivery)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($delivery['status'] == "approved") {
            set_alert('danger', lang('tnh_dgh'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('warehouses[]', lang("tnh_warehouses"), 'required');
            $this->form_validation->set_rules('locations[]', lang("tnh_location_warehouse"), 'required');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                $date = date('Y-m-d H:i:s');
                $counter = $this->input->post('counter');
                $total_quantity = 0;
                $arr_id = [];
                $arr_info = [];
                foreach ($counter as $key => $value) {
                    $delivery_item_id = $this->input->post('delivery_item_id')[$value];
                    if (empty($delivery_item_id)) {
                        continue;
                    }
                    $items_id = $this->input->post('item_id')[$value];
                    if (empty($items_id)) {
                        continue;
                    }

                    $arrs = explode('__', $items_id);
                    $item_id = $arrs[1];
                    $type_item = $arrs[0];

                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                    } elseif ($type_item == "items") {
                        $info = $this->items_model->rowItems($item_id);
                    }
                    if (empty($info)) {
                        continue;
                    }

                    $item_code = $info['code'];
                    $item_name = $info['name'];
                    $quantity = number_unformat($this->input->post('quantity')[$value]);

                    $index = array_search($delivery_item_id, $arr_id);
                    if ($index === false) {
                        $arr_id[] = $delivery_item_id;
                        $arr_info[]['quantity'] = $quantity;
                    } else {
                        $arr_info[$index]['quantity'] = $arr_info[$index]['quantity'] + $quantity;
                    }
                    $warehouse_id = $this->input->post('warehouses')[$value];
                    $location_id = $this->input->post('locations')[$value];
                    $note_item = $this->input->post('note_item')[$value];

                    $subList[] = [
                        'delivery_item_id' => $delivery_item_id,
                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $item_code,
                        'item_name' => $item_name,
                        'warehouse_id' => $warehouse_id,
                        'location_id' => $location_id,
                        'quantity' => $quantity,
                        'note_item' => $note_item,
                    ];

                    $total_quantity += $quantity;
                }

                // print_arrays($subList);
                //check errors quantity
                $errors = '';
                foreach ($items as $key => $value) {
                    // $item_current_id = $value['type_item'].'__'.$value['item_id'];
                    $item_current_id = $value['delivery_item_id'];
                    $quantity_current = $value['quantity'];
                    $index = array_search($item_current_id, $arr_id);
                    if ($index == '' && $index != 0) {
                        $errors .= lang('tnh_dsmh_cd');
                        break;
                    }
                    $quantity_delivery = $arr_info[$index]['quantity'];
                    if ($quantity_current != $quantity_delivery) {
                        $errors .= lang('tnh_dsmh_cd');
                        break;
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }
                //end

                if (empty($subList)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                $count_items = count($subList);

                $field = [
                    'date' => $date,
                    'reference_no' => getReference('export_warehouses'),
                    'delivery_id' => $id,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'status' => 'un_approved',
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                ];

                $export_warehouse_id = $this->deliveries_model->insertExportWarehouses($field);
                if ($export_warehouse_id) {
                    updateReference('export_warehouses');
                    foreach ($subList as $key => $value) {
                        $subList[$key]['export_warehouse_id'] = $export_warehouse_id;
                    }
                    $this->deliveries_model->insertBatchExportWarehouseItems($subList);
                    $this->deliveries_model->updateDeliveriesById($id,
                        ['status' => 'approved', 'count_export_warehouse' => 1]);

                    insertActivityLog([
                        'type_parent_obj' => 'deliveries',
                        'table_obj' => 'tbl_deliveries',
                        'id_obj' => $delivery['id'],
                        'name_obj' => $delivery['reference_no'],
                        'content' => lang('tnh_his_export_warehouse_sales_deliveries') . ' [' . $delivery['reference_no'] . ']',
                        'actions' => 'export_warehouse_sales'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    set_alert('success', lang('success'));
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
            $address_delivery = $this->site_model->rowShippingClient($delivery['address_delivery_id']);
            $warehouses = $this->site_model->getWarehouse();
            $referenceOrder = $this->deliveries_model->rowRefereceNoOrderByDeliveryId($id);
            $data['referenceOrder'] = $referenceOrder;
            $data['id'] = $id;
            $data['delivery'] = $delivery;
            $data['address_delivery'] = $address_delivery;
            $data['warehouses'] = $warehouses;
            $data['items'] = $items;
            $data['tnh'] = $this->tnh;
            $data['title'] = lang('tnh_export_warehouse_sales');
            $data['breadcrumb'] = [
                array('link' => base_url('admin/releases/deliveries'), 'page' => lang('deliveries')),
                array('link' => '#', 'page' => lang('tnh_export_warehouse_sales'))
            ];
            $this->load->view('admin/releases/export_warehouse_sales', $data);
        }
    }

    public function rowItemLocationWarehouse()
    {
        $data = [];
        if ($this->input->post()) {
            $item = $this->input->post('item_id');
            $warehouse_id = $this->input->post('warehouse_id');
            if (!empty($item)) {
                $item = explode('__', $item);
                $type_item = $item[0];
                $item_id = $item[1];

                if ($type_item == "products") {
                    $type_item = "product";
                } elseif ($type_item == "items") {
                    $type_item = "items";
                }

                $warehouses = $this->site_model->getWarehouseItemsByItemIdAndTypeAndWarehouse($item_id, $type_item,
                    $warehouse_id);
                foreach ($warehouses as $key => $value) {
                    $warehouses[$key]['location_name'] = recursiveLocations($value['localtion']);
                }
                $data['warehouses'] = $warehouses;
            }
        }
        echo json_encode($data);
    }

    public function export_warehouses()
    {
        redirect($_SERVER["HTTP_REFERER"]);
        die;
        if (!$this->perViewExportWarehouses && !$this->perViewOwnExportWarehouses) {
            accessDenied();
        }
        $data['un_approved_ws_stock'] = $this->deliveries_model->countExportWarehousesStatus('un_approved_ws_stock');
        $data['approved_ws_stock'] = $this->deliveries_model->countExportWarehousesStatus('approved_ws_stock');
        $data['all'] = $this->deliveries_model->countExportWarehousesStatus('all');

        $data['title'] = lang('export_warehouses');
        $data['tnh'] = $this->tnh;
        $this->load->view('admin/releases/export_warehouses', $data);
    }

    public function confirm_warehous()
    {
        ini_set('max_execution_time', 300);
        if (!has_permission('export_warehouses', '', 'approve_warehouse')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_q_warehouse')
            ));
            die;
        }
        $id = $this->input->post('id');
        $ktr = get_table_where('tbl_export_warehouses', array('id' => $id), '', 'row');

        if (!empty($ktr->warehouseman_id)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_export_confirm_warehous')
            ));
            die;
        }
        $_data = array(
            'warehouseman_id' => get_staff_user_id(),
            'date_warehouseman' => date('Y-m-d H:i:s')
        );
        if (!test_quantity_export_warehouses($id)) {
            $data['success'] = false;
            $data['message'] = array(
                'alert_type' => 'warning',
                'message' => _l('test_quantyti_time_return')
            );
            $data['item'] = get_items_export_warehouses($id);
            echo json_encode($data);
            die;
        } else {
            $success = $this->db->update('tbl_export_warehouses', $_data, array('id' => $id));
            $alert_type = 'warning';
            $message = _l('ch_no_successful_approval');
            if ($success) {

                $alert_type = 'success';
                $message = _l('ch_successful_approval');
                log_activity('Export Warehouses items approved [ID export_warehouses: ' . $id);
                insertActivityLog([
                    'type_parent_obj' => 'export_warehouses',
                    'table_obj' => 'tbl_export_warehouses',
                    'id_obj' => $ktr->id,
                    'name_obj' => $ktr->reference_no,
                    'content' => lang('tnh_his_warehouse_deliveries') . ' [' . $ktr->reference_no . ']',
                    'actions' => 'warehouses'
                ]);
                $this->orders_model->decreaseWarehouse($id);

                //duyet kho thanh cong, xủ lý giá trong đơn hàng
                $exWarehouseObject = [];
                $exportWarehouseItems = $this->deliveries_model->getExportWarehouseItems($id);
                if (!empty($exportWarehouseItems)) {
                    foreach ($exportWarehouseItems as $k => $val) {
                        $id_import = $val['id_import'];
                        $arrIdImport = explode('|', $id_import);
                        if (empty($arrIdImport)) {
                            continue;
                        }

                        foreach ($arrIdImport as $i => $v) {
                            if (empty($v)) {
                                continue;
                            }
                            $arrIP = explode('-', $v);
                            $idIm = $arrIP[0];
                            $qtyIm = $arrIP[1];
                            $warehouseProduct = $this->site_model->rowWarehouseProduct($idIm);
                            $price = $warehouseProduct['price'];
                            $total = $qtyIm * $price;

                            $exWarehouseObject[] = [
                                'type' => 1,
                                'object_id' => $val['export_warehouse_id'],
                                'object_item_id' => $val['id'],
                                'object_id_more' => $val['delivery_item_id'],
                                'id_import' => $idIm,
                                'quantity' => $qtyIm,
                                'price' => $price,
                                'total' => $total,
                                'date_created' => date('Y-m-d H:i:s'),
                                'created_by' => get_staff_user_id(),
                            ];
                        }
                    }
                    // print_arrays($exWarehouseObject);
                    //insert ex warehouse object
                    $arrOrder = [];
                    if (!empty($exWarehouseObject)) {
                        if ($this->deliveries_model->insertBatchExWarehouseObject($exWarehouseObject)) {
                            // $exWarehouseObSum = $this->deliveries_model->getTotalExWarehouseObject(1, $id, $group_object_id_more = true);
                            // xử lý giá vốn và lợi nhuận trong đơn hàng
                            foreach ($exWarehouseObject as $k => $val) {
                                $cost = $val['total'];
                                $delivery_item_id = $val['object_id_more'];

                                $this->db->select('tbl_order_items.order_id, tbl_order_items.id, tbl_order_items.total_amount, tbl_order_items.cost');
                                $this->db->from('tbl_delivery_items');
                                $this->db->join('tbl_order_items',
                                    'tbl_order_items.id = tbl_delivery_items.order_item_id');
                                $this->db->where('tbl_delivery_items.id', $delivery_item_id);
                                $order_item = $this->db->get()->result_array();

                                if (!empty($order_item)) {
                                    foreach ($order_item as $i => $v) {
                                        $order_id = $v['order_id'];
                                        $order_item_id = $v['id'];
                                        if (!in_array($order_id, $arrOrder)) {
                                            array_push($arrOrder, $order_id);
                                        }
                                        $totalAmount = $v['total_amount'];
                                        $costOld = $v['cost'];
                                        $costNew = $cost + $costOld;
                                        $profitNew = $totalAmount - $costNew;

                                        $this->orders_model->updateOrderItemNew($order_item_id,
                                            ['cost' => $costNew, 'profit' => $profitNew]);
                                    }
                                }
                            }

                            //Update lại đơn hàng cha
                            if (!empty($arrOrder)) {
                                foreach ($arrOrder as $k => $val) {
                                    $this->db->select('tbl_orders.charge_party, tbl_orders.cost_delivery, tbl_orders.grand_total, tbl_orders.total_tax, SUM(tbl_order_items.cost) as total_cost',
                                        false);
                                    $this->db->from('tbl_orders');
                                    $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
                                    $this->db->where('tbl_orders.id', $val);
                                    $orders = $this->db->get()->result_array();
                                    if (!empty($orders)) {
                                        foreach ($orders as $i => $v) {
                                            $totalCost = $v['total_cost'];
                                            $grandTotal = $v['grand_total'] - $v['total_tax'];
                                            $chargeParty = $v['charge_party'];
                                            $costDelivery = $v['cost_delivery'];

                                            if ($chargeParty == "customer") {
                                                $grandTotal = $grandTotal - $costDelivery;
                                            }
                                            $totalProfit = $grandTotal - $costDelivery - $totalCost;

                                            $this->orders_model->updateOrdersNew($val,
                                                ['total_cost' => $totalCost, 'total_profit' => $totalProfit]);
                                        }
                                    }
                                }
                            }
                            // end
                        }
                    }
                }
                //
            }
        }
        @pusherTNHNotfication();
        echo json_encode(array(
            'alert_type' => 'success',
            'message' => _l('Duyệt kho thành công')
        ));
        die;
    }

    public function getExportWarehouses()
    {
        if (!$this->perViewExportWarehouses && !$this->perViewOwnExportWarehouses) {
            accessDenied($js = true);
        }

        $customer_search = $this->input->post('customer_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status = $this->input->post('status_table');

        $this->datatables->select("
            tbl_export_warehouses.id as id,
            tbl_export_warehouses.date as date,
            tbl_export_warehouses.reference_no as reference_no,
            tbl_deliveries.reference_no as reference_delivery,
            tbl_deliveries.customer_name as customer_name,
            tbl_export_warehouses.total_quantity as total_quantity,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            tbl_export_warehouses.warehouseman_id as warehouseman_id,
            ", false)
            ->from('tbl_export_warehouses')
            ->join('tbl_deliveries', 'tbl_deliveries.id = tbl_export_warehouses.delivery_id', 'inner')
            ->join('tblstaff', 'tblstaff.staffid = tbl_export_warehouses.created_by', 'left');

        if (!$this->perViewExportWarehouses) {
            $this->datatables->where('tbl_export_warehouses.created_by', get_staff_user_id());
        }

        if (!empty($customer_search)) {
            $this->datatables->where('tbl_deliveries.customer_id', $customer_search);
        }

        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_export_warehouses.date, "%Y-%m-%d") >=',
                to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_export_warehouses.date, "%Y-%m-%d") <=',
                to_sql_date($end_date_search));
        }

        if (!empty($status) && $status != 'all') {
            if ($status == 'un_approved_ws_stock') {
                $this->datatables->where('tbl_export_warehouses.warehouseman_id', 0);
            } elseif ($status == 'approved_ws_stock') {
                $this->datatables->where('tbl_export_warehouses.warehouseman_id >', 0);
            }
        }

        $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/releases/view_export_warehouse/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('tnh_export_warehouses') . '</a>';

        $print = '<a href="' . base_url('admin/releases/print_export_warehouse/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('tnh_export_warehouses') . '</a>';

        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/releases/deleteExportWarehouses/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('deliveries') . '</a>';

        $actions = '
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $print . '<li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function view_export_warehouse($id)
    {
        if (!$this->perViewExportWarehouses && !$this->perViewOwnExportWarehouses) {
            accessDenied($js = true);
        }

        $export_warehouses = $this->deliveries_model->rowExportWarehouses($id);
        if (!$this->perViewExportWarehouses) {
            checkMyData($export_warehouses['created_by'], true);
        }

        $delivery = $this->deliveries_model->rowDeliveriesById($export_warehouses['delivery_id']);
        $items = $this->deliveries_model->getExportWarehouseItems($id);
        $bodyItems = '';

        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $warehouse = $this->site_model->rowWarehouseById($value['warehouse_id']);
                $location = $this->site_model->rowLocationWarehouseById($value['location_id']);

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';
                $tdType = '<td><div class="type-item">' . (($type_item == "products") ? '<span class="label label-success">' . lang($type_item) . '</span>' : '<span class="label label-primary">' . lang('ch_items') . '</span>') . '</div></td>';
                $tdCode = '<td>' . $info['code'] . '</td>';
                $tdName = '<td>' . $info['name'] . '</td>';
                $tdWarehouses = '<td>' . $warehouse['name'] . '</td>';
                $tdLocations = '<td>' . $location['name'] . '</td>';
                $tdUnit = '<td>' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $tdNote = '<td>' . $value['note_item'] . '</td>';
                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdImages . '
                    ' . $tdType . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdWarehouses . '
                    ' . $tdLocations . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $data['bodyItems'] = $bodyItems;
        $data['id'] = $id;
        $data['export_warehouses'] = $export_warehouses;
        $data['delivery'] = $delivery;
        $data['created_by'] = get_staff_full_name($export_warehouses['created_by']);
        $this->load->view('admin/releases/view_export_warehouse', $data);
    }

    public function deleteExportWarehouses($id)
    {
        $data = [];

        if (!$this->perDeleteExportWarehouses) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        if ($id) {
            $export_warehouses = $this->deliveries_model->rowExportWarehouses($id);
            if (!checkMyDataTF($export_warehouses['created_by'])) {
                $data['result'] = 0;
                $data['message'] = lang('access_denied');
                echo json_encode($data);
                die;
            }
            // $items = $this->deliveries_model->getExportWarehouseItems($id);
            $export_warehouses_ch = get_table_where('tbl_export_warehouses', array('id' => $id), '', 'row');
            $export_warehouses_items = get_table_where('tbl_export_warehous_items',
                array('export_warehouse_id' => $id));

            $exWarehouseObjectTotal = $this->deliveries_model->getTotalExWarehouseObject(1, $id,
                $group_object_id_more = true);


            if ($export_warehouses['status'] == "un_approved") {

                if ($this->deliveries_model->deleteExportWarehouses($id)) {
                    $this->deliveries_model->deleteExportWarehousesItems($id);
                    $this->deliveries_model->updateDeliveriesById($export_warehouses['delivery_id'],
                        ['status' => 'un_approved', 'count_export_warehouse' => 0]);
                    if (!empty($export_warehouses_ch->warehouseman_id)) {
                        $this->orders_model->increaseadWarehouse($id, $export_warehouses_items);
                    }

                    //xử giá giá vốn lợi nhuận
                    $this->deliveries_model->deleteExWarehouseObject(1, $id);
                    if (!empty($exWarehouseObjectTotal)) {
                        $arrOrder = [];
                        foreach ($exWarehouseObjectTotal as $k => $val) {
                            $cost = $val['total'];
                            $delivery_item_id = $val['object_id_more'];

                            $this->db->select('tbl_order_items.order_id, tbl_order_items.id, tbl_order_items.total_amount, tbl_order_items.cost');
                            $this->db->from('tbl_delivery_items');
                            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                            $this->db->where('tbl_delivery_items.id', $delivery_item_id);
                            $order_item = $this->db->get()->result_array();

                            if (!empty($order_item)) {
                                foreach ($order_item as $i => $v) {
                                    $order_id = $v['order_id'];
                                    $order_item_id = $v['id'];
                                    if (!in_array($order_id, $arrOrder)) {
                                        array_push($arrOrder, $order_id);
                                    }
                                    $totalAmount = $v['total_amount'];
                                    $costOld = $v['cost'];
                                    $costNew = $costOld - $cost;
                                    $profitNew = $totalAmount - $costNew;

                                    $this->orders_model->updateOrderItemNew($order_item_id,
                                        ['cost' => $costNew, 'profit' => $profitNew]);
                                }
                            }
                        }

                        //Update lại đơn hàng cha
                        if (!empty($arrOrder)) {
                            foreach ($arrOrder as $k => $val) {
                                $this->db->select('tbl_orders.charge_party, tbl_orders.cost_delivery, tbl_orders.grand_total, tbl_orders.total_tax, SUM(tbl_order_items.cost) as total_cost',
                                    false);
                                $this->db->from('tbl_orders');
                                $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
                                $this->db->where('tbl_orders.id', $val);
                                $orders = $this->db->get()->result_array();
                                if (!empty($orders)) {
                                    foreach ($orders as $i => $v) {
                                        $totalCost = $v['total_cost'];
                                        $grandTotal = $v['grand_total'] - $v['total_tax'];
                                        $chargeParty = $v['charge_party'];
                                        $costDelivery = $v['cost_delivery'];
                                        if ($chargeParty == "customer") {
                                            $grandTotal = $grandTotal - $costDelivery;
                                        }

                                        $totalProfit = $grandTotal - $costDelivery - $totalCost;

                                        $this->orders_model->updateOrdersNew($val,
                                            ['total_cost' => $totalCost, 'total_profit' => $totalProfit]);
                                    }
                                }
                            }
                        }
                    }
                    //end

                    insertActivityLog([
                        'type_parent_obj' => 'export_warehouses',
                        'table_obj' => 'tbl_export_warehouses',
                        'id_obj' => $export_warehouses['id'],
                        'name_obj' => $export_warehouses['reference_no'],
                        'content' => lang('tnh_his_export_warehouse_sales_deliveries') . ' [' . $export_warehouses['reference_no'] . ']',
                        'actions' => 'export_warehouses'
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

    public function tnh_export_warehouses($id)
    {
        return;
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
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 25%;">' . $info['name'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 6%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td class="text-right" style="width: 10%;">' . formatMoney($value['price']) . '</td>';
                $tdTax = '<td class="text-right" style="width: 10%;">' . formatMoney($value['tax_amount_item']) . '</td>';
                $tdDiscount = '<td class="text-right" style="width: 12%;">' . formatMoney($value['discount_percent_amount_item'] + $value['discount_direct_amount_item']) . '</td>';
                $tdTotalAmount = '<td class="text-right" style="width: 13%;">' . formatMoney($value['total_amount']) . '</td>';
                $tdNote = '<td style="width: 10%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTax . '
                    ' . $tdDiscount . '
                    ' . $tdTotalAmount . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $day = date_format(date_create($order['date']), 'd');
        $month = date_format(date_create($order['date']), 'm');
        $year = date_format(date_create($order['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_sales_orders') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_orders') . ': ' . $order['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($order['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('customers') . ': <span class="bold">' . $order['customer_name'] . '</span></span><br>
                <span>' . _l('tnh_address_delivery') . ': <span>' . (!empty($address_delivery['address']) ? $address_delivery['address'] : '') . '</span></span><br>
                <span>' . _l('tnh_employees_charge') . ': <span>' . $employee . '</span></span><br>
                <span>' . _l('tnh_note') . ': <span>' . $order['note'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 25%;">' . _l('tnh_its') . '</th>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('price') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('tnh_taxs') . '</th>
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
                        <th></th>
                        <th class="text-right">' . formatMoney($order['grand_total_items']) . '</th>
                        <th></th>
                    </tr>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tax') . '</th>
                        <th class="text-center"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-right">' . formatMoney($order['total_tax']) . '</th>
                        <th></th>
                    </tr>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_discount') . '</th>
                        <th class="text-center"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-right">' . formatMoney($order['total_discount_percent'] + $order['total_discount_direct']) . '</th>
                        <th></th>
                    </tr>
                    <tr class="bold">
                        <th class="text-right" colspan="3">' . _l('tnh_grand_total') . '</th>
                        <th class="text-center"></th>
                        <th></th>
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

    public function print_export_warehouse($id)
    {
        if (!$this->perPrintExportWarehouses) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $export_warehouses = $this->deliveries_model->rowExportWarehouses($id);
        $delivery = $this->deliveries_model->rowDeliveriesById($export_warehouses['delivery_id']);
        $items = $this->deliveries_model->getExportWarehouseItems($id);
        // $img = file_get_contents(base_url('uploads/company/').get_option('company_logo'));
        $data['title'] = lang('tnh_print_export_warehouse');
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
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $warehouse = $this->site_model->rowWarehouseById($value['warehouse_id']);
                $location = $this->site_model->rowLocationWarehouseById($value['location_id']);

                $tdNumber = '<td class="text-center" style="width: 5%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 28%;">' . $info['name'] . '(' . $info['code'] . ')</td>';
                $tdUnit = '<td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>';
                $tdWarehouses = '<td style="width: 17%;"><div>' . $warehouse['name'] . '</div></td>';
                $tdLocations = '<td style="width: 10%;">' . $location['name'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';
                $tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdWarehouses . '
                    ' . $tdLocations . '
                    ' . $tdQuantity . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        $day = date_format(date_create($export_warehouses['date']), 'd');
        $month = date_format(date_create($export_warehouses['date']), 'm');
        $year = date_format(date_create($export_warehouses['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_ex_warehouses') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('tnh_reference_export_warehouses') . ': ' . $export_warehouses['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($export_warehouses['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('tnh_reference_deliveries') . ': <span class="bold">' . $delivery['reference_no'] . '</span></span><br>
                <span>' . _l('customers') . ': <span class="bold">' . $delivery['customer_name'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th class="bold text-center" style="width: 5%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 28%;">' . _l('tnh_its') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 17%;">' . _l('tnh_warehouses') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('tnh_position') . '</th>
                        <th class="bold text-center" style="width: 15%;">' . _l('quantity') . '</th>
                        <th class="bold text-center" style="width: 15%;">' . _l('tnh_note') . '</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    <tr class="bold">
                        <th class="text-right" colspan="4">' . _l('tnh_total') . '</th>
                        <th></th>
                        <th class="text-center">' . formatNumber($export_warehouses['total_quantity']) . '</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
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
                        <span class="bold">Thủ Kho</span><br>
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

    // public function purchase_products()
    // {
    //     $data['tnh'] = $this->tnh;
    //     $data['title'] = lang('purchase_products');
    //     $this->load->view('admin/releases/purchase_products', $data);
    // }

    // public function add_purchase_product()
    // {
    //     $warehouses = $this->site_model->getWarehouse();
    //     $data['tnh'] = $this->tnh;
    //     $data['reference_no'] = '';
    //     $data['title'] = lang('tnh_add_purchase_product');
    //     $data['breadcrumb'] = [array('link' => base_url('admin/releases/purchase_products'), 'page' => lang('purchase_products')), array('link' => '#', 'page' => lang('tnh_add_purchase_product'))];
    //     $this->load->view('admin/releases/add_purchase_product', $data);
    // }

    public function table_single_client($clientid = '')
    {
        $this->app->get_table_data('deliveries_single_client', [
            'clientid' => $clientid,
        ]);
    }

    public function confirm_warehous_deleveries()
    {
        if (!has_permission('releases_deliveries', '', 'approve_warehouse')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_q_warehouse')
            ));
            die;
        }
        $id = $this->input->post('id');
        $warehouseman_id = $this->input->post('warehouseman_id');
        if (empty($warehouseman_id)) {
            $ktr = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
            if (!empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_export_confirm_warehous')
                ));
                die;
            }

            $_data = array(
                'warehouseman_id' => get_staff_user_id(),
                'date_warehouseman' => date('Y-m-d H:i:s')
            );

            if (!test_quantity_order($id)) {
                $data['success'] = false;
                $data['message'] = array(
                    'alert_type' => 'warning',
                    'message' => _l('test_quantyti_time_return')
                );
                $data['item'] = get_items_deleveries($id);
                echo json_encode($data);
                die;
            } else {
                $success = $this->db->update('tbl_deliveries', $_data, array('id' => $id));
                $alert_type = 'warning';
                $message = _l('ch_no_successful_approval');
                if ($success) {
                    $alert_type = 'success';
                    $message = _l('ch_successful_approval');
                    $this->deliveries_model->decreaseWarehouse($id);

                    //duyet kho thanh cong, xủ lý giá trong đơn hàng
                    $exWarehouseObject = [];
                    $deliveryItems = $this->deliveries_model->getDeliveryItems($id);
                    if (!empty($deliveryItems)) {
                        foreach ($deliveryItems as $k => $val) {
                            $id_import = $val['id_import'];
                            $arrIdImport = explode('|', $id_import);
                            if (empty($arrIdImport)) {
                                continue;
                            }

                            foreach ($arrIdImport as $i => $v) {
                                if (empty($v)) {
                                    continue;
                                }
                                $arrIP = explode('-', $v);
                                $idIm = $arrIP[0];
                                $qtyIm = $arrIP[1];
                                $warehouseProduct = $this->site_model->rowWarehouseProduct($idIm);
                                $price = $warehouseProduct['price'];
                                $total = $qtyIm * $price;

                                $exWarehouseObject[] = [
                                    'type' => 1,
                                    'object_id' => $val['delivery_id'],
                                    'object_item_id' => $val['id'],
                                    'object_id_more' => $val['order_item_id'],
                                    'id_import' => $idIm,
                                    'quantity' => $qtyIm,
                                    'price' => $price,
                                    'total' => $total,
                                    'date_created' => date('Y-m-d H:i:s'),
                                    'created_by' => get_staff_user_id(),
                                ];
                            }
                        }
                        // print_arrays($exWarehouseObject);
                        //insert ex warehouse object
                        $arrOrder = [];
                        if (!empty($exWarehouseObject)) {
                            if ($this->deliveries_model->insertBatchExWarehouseObject($exWarehouseObject)) {
                                // $exWarehouseObSum = $this->deliveries_model->getTotalExWarehouseObject(1, $id, $group_object_id_more = true);
                                // xử lý giá vốn và lợi nhuận trong đơn hàng
                                foreach ($exWarehouseObject as $k => $val) {
                                    $cost = $val['total'];
                                    $delivery_item_id = $val['object_item_id'];

                                    $this->db->select('tbl_order_items.order_id, tbl_order_items.id, tbl_order_items.total_amount, tbl_order_items.cost');
                                    $this->db->from('tbl_delivery_items');
                                    $this->db->join('tbl_order_items',
                                        'tbl_order_items.id = tbl_delivery_items.order_item_id');
                                    $this->db->where('tbl_delivery_items.id', $delivery_item_id);
                                    $order_item = $this->db->get()->result_array();

                                    if (!empty($order_item)) {
                                        foreach ($order_item as $i => $v) {
                                            $order_id = $v['order_id'];
                                            $order_item_id = $v['id'];
                                            if (!in_array($order_id, $arrOrder)) {
                                                array_push($arrOrder, $order_id);
                                            }
                                            $totalAmount = $v['total_amount'];
                                            $costOld = $v['cost'];
                                            $costNew = $cost + $costOld;
                                            $profitNew = $totalAmount - $costNew;

                                            $this->orders_model->updateOrderItemNew($order_item_id,
                                                ['cost' => $costNew, 'profit' => $profitNew]);
                                        }
                                    }
                                }

                                //Update lại đơn hàng cha
                                if (!empty($arrOrder)) {
                                    foreach ($arrOrder as $k => $val) {
                                        $this->db->select('tbl_orders.charge_party, tbl_orders.cost_delivery, tbl_orders.grand_total, tbl_orders.total_tax, SUM(tbl_order_items.cost) as total_cost',
                                            false);
                                        $this->db->from('tbl_orders');
                                        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
                                        $this->db->where('tbl_orders.id', $val);
                                        $orders = $this->db->get()->result_array();
                                        if (!empty($orders)) {
                                            foreach ($orders as $i => $v) {
                                                $totalCost = $v['total_cost'];
                                                $grandTotal = $v['grand_total'] - $v['total_tax'];
                                                $chargeParty = $v['charge_party'];
                                                $costDelivery = $v['cost_delivery'];
                                                if ($chargeParty == "customer") {
                                                    $grandTotal = $grandTotal - $costDelivery;
                                                }

                                                $totalProfit = $grandTotal - $costDelivery - $totalCost;

                                                $this->orders_model->updateOrdersNew($val,
                                                    ['total_cost' => $totalCost, 'total_profit' => $totalProfit]);
                                            }
                                        }
                                    }
                                }
                                // end
                            }
                        }
                    }
                    //
                }
            }
            $this->db->select('tbl_delivery_items.quantity,tbl_delivery_items.id,tbl_delivery_items.item_code,tbl_delivery_items.item_name,tbl_deliveries.date,tbl_deliveries.reference_no,tblclients.company,tblclients.zcode,tbl_orders.reference_no as code_orders,tbl_deliveries.warehouseman_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tbl_deliveries.employee_id,tblunits.unit as unit_name,tblclients.company_short');
            $this->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
            $this->db->join('tblstaff', 'tblstaff.staffid=tbl_deliveries.employee_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id=tbl_delivery_items.item_id', 'left');
            $this->db->join('tblunits', 'tblunits.unitid=tbl_products.unit_id', 'left');
            $this->db->where('tbl_deliveries.id', $id);
            $rows = $this->db->get('tbl_delivery_items')->result_array();
            foreach ($rows as $key => $value) {
                $updatedRow = $this->_api_row_export_delivery($value);
                sendSocket([
                    'action'     => 'update',
                    'updatedRow' => $updatedRow,
                    'removed'    => true,
                ], [], 'ExportDeliveryloadProgress');
            }
            echo json_encode(array(
                'alert_type' => 'success',
                'message' => _l('ch_export_confirm')
            ));
            die;
        } else {
            $_data = array(
                'warehouseman_id' => 0,
                'date_warehouseman' => null
            );
            $success = $this->db->update('tbl_deliveries', $_data, array('id' => $id));

            //xử lý giá thành
            $exWarehouseObjectTotal = $this->deliveries_model->getTotalExWarehouseObject(1, $id,
                $group_object_id_more = true);
            $this->deliveries_model->deleteExWarehouseObject(1, $id);
            if (!empty($exWarehouseObjectTotal)) {
                $arrOrder = [];
                foreach ($exWarehouseObjectTotal as $k => $val) {
                    $cost = $val['total'];
                    $order_item_object_id = $val['object_id_more'];

                    $this->db->select('tbl_order_items.order_id, tbl_order_items.id, tbl_order_items.total_amount, tbl_order_items.cost');
                    $this->db->from('tbl_order_items');
                    $this->db->where('tbl_order_items.id', $order_item_object_id);
                    $order_item = $this->db->get()->result_array();

                    if (!empty($order_item)) {
                        foreach ($order_item as $i => $v) {
                            $order_id = $v['order_id'];
                            $order_item_id = $v['id'];
                            if (!in_array($order_id, $arrOrder)) {
                                array_push($arrOrder, $order_id);
                            }
                            $totalAmount = $v['total_amount'];
                            $costOld = $v['cost'];
                            $costNew = $costOld - $cost;
                            $profitNew = $totalAmount - $costNew;

                            $this->orders_model->updateOrderItemNew($order_item_id,
                                ['cost' => $costNew, 'profit' => $profitNew]);
                        }
                    }
                }

                //Update lại đơn hàng cha
                if (!empty($arrOrder)) {
                    foreach ($arrOrder as $k => $val) {
                        $this->db->select('tbl_orders.charge_party, tbl_orders.cost_delivery, tbl_orders.grand_total, tbl_orders.total_tax, SUM(tbl_order_items.cost) as total_cost',
                            false);
                        $this->db->from('tbl_orders');
                        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
                        $this->db->where('tbl_orders.id', $val);
                        $orders = $this->db->get()->result_array();
                        if (!empty($orders)) {
                            foreach ($orders as $i => $v) {
                                $totalCost = $v['total_cost'];
                                $grandTotal = $v['grand_total'] - $v['total_tax'];
                                $chargeParty = $v['charge_party'];
                                $costDelivery = $v['cost_delivery'];
                                if ($chargeParty == "customer") {
                                    $grandTotal = $grandTotal - $costDelivery;
                                }
                                $totalProfit = $grandTotal - $costDelivery - $totalCost;

                                $this->orders_model->updateOrdersNew($val,
                                    ['total_cost' => $totalCost, 'total_profit' => $totalProfit]);
                            }
                        }
                    }
                }
            }
            //end

            $this->deliveries_model->increaseadWarehouse($id);
            $this->db->select('tbl_delivery_items.quantity,tbl_delivery_items.id,tbl_delivery_items.item_code,tbl_delivery_items.item_name,tbl_deliveries.date,tbl_deliveries.reference_no,tblclients.company,tblclients.zcode,tbl_orders.reference_no as code_orders,tbl_deliveries.warehouseman_id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname_employee,tbl_deliveries.employee_id,tblunits.unit as unit_name,tblclients.company_short');
            $this->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id=tbl_deliveries.order_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
            $this->db->join('tblstaff', 'tblstaff.staffid=tbl_deliveries.employee_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id=tbl_delivery_items.item_id', 'left');
            $this->db->join('tblunits', 'tblunits.unitid=tbl_products.unit_id', 'left');
            $this->db->where('tbl_deliveries.id', $id);
            $rows = $this->db->get('tbl_delivery_items')->result_array();
            foreach ($rows as $key => $value) {
                $updatedRow = $this->_api_row_export_delivery($value);
                sendSocket([
                    'action'     => 'update',
                    'updatedRow' => $updatedRow
                ], [], 'ExportDeliveryloadProgress');
            }
            echo json_encode(array(
                'alert_type' => 'success',
                'message' => _l('Bỏ duyệt thành công')
            ));
            die;
        }
    }

    public function update_received_certificate($id, $status = 0)
    {
        if (!has_permission('releases_deliveries', '', 'approve_accept')) {
            ajax_access_denied();
        }
        $this->db->where('id', $id);
        $success = $this->db->update('tbl_deliveries', [
            'received_certificate' => $status
        ]);
        if (!empty($success)) {
            if (!empty($status)) {
                $message = 'Đã nhận chứng từ thành công';
            } else {
                $message = 'Đã hủy nhận chứng từ thành công';
            }

            echo json_encode([
                'success' => true,
                'alert_type' => 'success',
                'message' => $message
            ]);
            die();
        }
        if (!empty($success)) {
            $message = 'Đã nhận chứng từ không thành công';
        } else {
            $message = 'Đã hủy nhận chứng từ không thành công';
        }

        echo json_encode([
            'success' => false,
            'alert_type' => 'danger',
            'message' => $message
        ]);
        die();
    }

    public function print_size($id)
    {
        $data = [];
        $delivery = get_table_where('tbl_deliveries', ['id' => $id], '', 'row_array');
//        $items = get_table_where('tbl_delivery_items',['delivery_id' => $id]);

        $this->db->select('*,SUM(quantity) as quantity,GROUP_CONCAT(tbl_delivery_items.id) as id_delivery_item');
        $this->db->from('tbl_delivery_items');
        $this->db->where('tbl_delivery_items.delivery_id', $id);
        $this->db->group_by('tbl_delivery_items.order_item_id');
        $items = $this->db->get()->result_array();

        $data['delivery'] = $delivery;
        $data['items'] = $items;
        $data['id'] = $id;
        $this->load->view('admin/releases/print_size', $data);
    }

    public function get_print_size()
    {

        ob_end_clean();
        $delivery_id = $this->input->post('delivery_id');
        $p_id = $this->input->post('p_id');
        $vt1 = $this->input->post('vt1');
        $vt2 = $this->input->post('vt2');

        $delivery = $this->deliveries_model->rowDeliveriesById($delivery_id);
        $company = $this->site_model->rowCustomer($delivery['customer_id']);

        $data['title'] = lang('tnh_print_delivery');
        $data['type'] = 'P';
        $data['img'] = '';

        $orderItemsColumnsNewVs1 = [];
        if (!empty($p_id)) {
            $vt1_ar = array();
            $vt1_id = array();

            if (!empty($vt1)) {
                $vt1_ar = explode('_____', $vt1);
                foreach ($vt1_ar as $ka => $va) {
                    if (empty($va)) {
                        continue;
                    }
                    $vas = explode('|_|', $va);
                    $vt1_id[$vas[0]] = $vas[1];
                }
            }

            $vt2_ar = array();
            $vt2_id = array();
            if (!empty($vt2)) {
                $vt2_ar = explode('_____', $vt2);
                foreach ($vt2_ar as $ka => $va) {
                    if (empty($va)) {
                        continue;
                    }
                    $vas = explode('|_|', $va);
                    $vt2_id[$vas[0]] = $vas[1];
                }
            }

            $p_id = explode(',', $p_id);

            $this->db->select('
                tbl_delivery_items.*
            ', false);
            $this->db->from('tbl_delivery_items');
            $this->db->where('tbl_delivery_items.delivery_id', $delivery_id);
            $this->db->where_in('tbl_delivery_items.id', $p_id);
            $items = $this->db->get()->result_array();

            $this->db->select('tbl_delivery_items.*,SUM(tbl_delivery_items.quantity) as quantity,IF(tbl_order_items.check_delivery = 0,SUM(tbl_delivery_items.quantity_loss),0) as quantity_loss,GROUP_CONCAT(tbl_delivery_items.id) as id_delivery_item');
            $this->db->from('tbl_delivery_items');
            $this->db->join('tbl_order_items','tbl_order_items.id = tbl_delivery_items.order_item_id');
            $this->db->where('tbl_delivery_items.delivery_id', $delivery_id);
            $this->db->where_in('tbl_delivery_items.id', $p_id);
            $this->db->group_by('tbl_delivery_items.order_item_id');
            $itemsNew = $this->db->get()->result_array();

            $orderItemsColumnsNewVs1 = [];
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];
                    $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);
                    if ($type_item == "products") {
                        $thSub = '';
                        $trHtmlChild = '';
                        $ct_counter_item = $dtOrderItem['ct_counter_item'];
                        $productsColumns = $this->products_model->getProductsColumns($items_id);
                        $this->db->select('tbl_delivery_items_columns.*');
                        $this->db->from('tbl_delivery_items_columns');
                        $this->db->where('tbl_delivery_items_columns.delivery_item_id', $value['id']);
                        $orderItemsColumns = $this->db->get()->result_array();
                        $orderItemsColumnsNew = [];
                        if ($ct_counter_item > 0) {
                            for ($i = 0; $i < $ct_counter_item; $i++) {
                                $arrNew = [];
                                foreach ($productsColumns as $k => $v) {
                                    $columns_name = [];
                                    foreach ($orderItemsColumns as $kO => $vO) {
                                        if ($vO['counter_items_number'] == $i && $vO['columns_value'] == $v['name']) {
                                            $columns_name = [
                                                vn_to_str($vO['columns_value']) => $vO['columns_name']
                                            ];
                                            break;
                                        }
                                    }
                                    $arrNew = array_merge($arrNew, $columns_name);
                                }
                                $orderItemsColumnsNew[$i] = $arrNew;
                                foreach ($orderItemsColumns as $kO => $vO) {

                                    if ($i == $vO['counter_items_number']) {
                                        $orderItemsColumnsNew[$i]['order_item_id'] = $vO['order_item_id'];
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
                                        $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
                                        continue;
                                    } elseif ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                        $quantity_loss = $vO['columns_name'];
                                        $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                        continue;
                                    } elseif ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                        $sample_quantity_item = $vO['columns_name'];
                                        $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                        continue;
                                    } elseif ($vO['columns_value'] == 'quantity_loss_new' && $i == $vO['counter_items_number']) {
                                        $quantity_loss_new = $vO['columns_name'];
                                        $orderItemsColumnsNew[$i]['quantity_loss_new'] = $quantity_loss_new;
                                        continue;
                                    }
                                }
                            }
                        }

                        if (!empty($orderItemsColumnsNew)) {
                            foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                                if (empty($vvv)) {
                                    continue;
                                }
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
                                $check_key = $vvv['code'] . '__' . $vvv['command'] . '__' . $items_id . '__' . $type_item . '__' . $vvv['order_item_id'] . '__' . $vvv['counter_items_number'];
                                if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                    $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
                                    $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
                                    $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
                                    $orderItemsColumnsNewVs1[$check_key]['quantity_loss_new'] += $vvv['quantity_loss_new'];
                                } else {
                                    $orderItemsColumnsNewVs1[$check_key] = $vvv;
                                    $orderItemsColumnsNewVs1[$check_key]['item_id'] = $items_id;
                                    $orderItemsColumnsNewVs1[$check_key]['type_item'] = $type_item;
                                }
                            }
                        }
                    }
                }
            }
        }
        $orderItemsColumnsNewVs2 = [];
        if (!empty($orderItemsColumnsNewVs1)) {
            foreach ($orderItemsColumnsNewVs1 as $key => $value) {
                $item_id = $value['item_id'];
                $type_item = $value['type_item'];
                $check_key = $item_id . '__' . $type_item;
                unset($value['item_id']);
                unset($value['type_item']);
                $orderItemsColumnsNewVs2[$check_key][] = $value;
            }
        }

        $bodyItems = '';

        $totalQuantityLoss = 0;
        $totalQuantityPut = 0;
        if (!empty($itemsNew)) {
            foreach ($itemsNew as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }

                $dtOrderItem = $this->orders_model->rowOrderItemsById($value['order_item_id']);

                $htmlOrderColumns = '';
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                    $styleTd = '';
                    $arrColums = [];
                    if (!empty($productsColumns)) {
                        $styleTd = 'width: ' . 72 / count($productsColumns) . '%';
                        foreach ($productsColumns as $k => $v) {
                            $tv1_text = '';
                            if (!empty($vt1_id[$value['id_delivery_item']])) {
                                $tv1_text = $vt1_id[$value['id_delivery_item']];
                            }
                            $tv2_text = '';
                            if (!empty($vt2_id[$value['id_delivery_item']])) {
                                $tv2_text = $vt2_id[$value['id_delivery_item']];
                            }
                            if ($tv1_text == $v['name']) {
                                $arrColums[] = $v['name'];
                            }
                            if ($tv2_text == $v['name']) {
                                $arrColums[] = $v['name'];
                            }
                        }
                    }
                    $colspan = 0;
                    if (!empty($arrColums)) {
                        $styleTd = 'width: ' . 30 / count($arrColums) . '%';
                        foreach ($arrColums as $k => $v) {
                            $thSub .= '<th class="text-center" style="' . $styleTd . '">' . $v . '</th>';
                        }
                    }
                    $colspan = (count($arrColums));
                    $ii = 1;
                    $check_key = $items_id . '__' . $type_item;
                    $orderItemsColumnsNewVs3 = $orderItemsColumnsNewVs2[$check_key];
                    if (!empty($orderItemsColumnsNewVs3)) {
                        foreach ($orderItemsColumnsNewVs3 as $kk => $vv) {
                            $order_code = $vv['code'];
                            $command = $vv['command'];
                            $quantity_put = $vv['quantity_put'];
                            $quantity_loss = $vv['quantity_loss'];
                            $sample_quantity_item = $vv['sample_quantity_item'];
                            $quantity_loss_new = $vv['quantity_loss_new'];
                            $trHtmlColumns = '';
                            $columns_name_new = '';
                            if (!empty($arrColums)) {
                                foreach ($arrColums as $k => $v) {
                                    $name_check = vn_to_str($v);
                                    if (!empty($vv[$name_check])) {
                                        $columns_name_new = $vv[$name_check];
                                        $trHtmlColumns .= '
                                        <td class="text-center" style="font-size:12px">
                                            ' . $columns_name_new . '
                                        </td>
                                        ';
                                    } else {
                                        $trHtmlColumns .= '
                                        <td class="text-center" style="font-size:12px">
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

                            $tdQuantityLossNew = '<td class="text-center">
                                ' . formatNumber($quantity_loss_new) . '
                            </td>';

                            $totalQuantityLoss += $quantity_loss_new;
                            $totalQuantityPut += $quantity_put;

                            if (empty($trHtmlColumns) && empty($order_code)) {
                                continue;
                            }
                            $stt = $ii;
                            $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                            $trHtmlChild .= '<tr class="not-tr">
                                ' . $tdNumberChild . '
                                ' . $trHtmlColumns . '
                                ' . $tdOrderCode . '
                                ' . $tdCommand . '
                                ' . $tdQuantityPut . '
                                ' . $tdQuantityLossNew . '
                            </tr>';
                            $ii++;
                        }
                    }
                    $trHtmlChild .= '<tr class="not-tr bold">
                                <td colspan="'.($colspan + 3).'">Tổng cộng</td>
                                <td style="text-align: center">'.formatNumber($totalQuantityPut).'</td>
                                <td style="text-align: center">'.formatNumber($totalQuantityLoss).'</td>
                            </tr>';
                    $htmlOrderColumns .= '<table class="table" border="1">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">' . lang('tnh_numbers') . '
                                    </th>
                                    ' . $thSub . '
                                    <th class="text-center" style="width: 23%">' . lang('Mã đơn đặt') . '</th>
                                    <th class="text-center" style="width: 20%">' . lang('Chỉ lệnh') . '</th>
                                    <th class="text-center" style="width: 12%">' . lang('tnh_quantity_put') . '</th>
                                    <th class="text-center" style="width: 10%">' . lang('SL loss') . '</th>
                                </tr>
                            </thead>
                                <tbody class="child">
                                    ' . $trHtmlChild . '
                                </tbody>
                            </table>
                        ';
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdCode = '<td style="width: 20%;">' . $info['code'] . '</td>';
                $tdName = '<td style="width: 25%;font-family: kozgopromedium;font-size:11px">' . $dtOrderItem['product_name_customer'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 13%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>';

                $typePrint = get_table_where('tbl_type_print', ['id' => $info['type_print']], '', 'row_array');
                $name_type_print = '';
                if (!empty($typePrint)) {
                    $name_type_print = $typePrint['name'];
                }
                $tdNote = '<td style="width: 21%;">' . $value['note_item'] . '</td>';


                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdNote . '
                </tr>
                <tr>
                    <td colspan="8">
                        ' . $htmlOrderColumns . '
                    </td>
                </tr>';

            }
        }

        $tdHead = '<thead>
                <tr style="background-color: #ddd;">
                    <th class="bold text-center" style="width: 6%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_numbers') . '</th>
                    <th class="bold text-center" style="width: 20%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Mã TP') . '</th>
                    <th class="bold text-center" style="width: 25%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('Tên TP') . '</th>
                    <th class="bold text-center" style="width: 13%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_dvt') . '</th>
                    <th class="bold text-center" style="width: 15%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('quantity') . '</th>
                    <th class="bold text-center" style="width: 21%;border-top-width: 2px; border-bottom-width: 1px;">' . _l('tnh_note') . '</th>
                </tr>
            </thead>';

        $tfoot = '<tfoot>
                <tr class="bold" style="background-color: #ddd;">
                    <th class="text-right" colspan="4">' . _l('tnh_total') . '</th>
                    <th class="text-center">' . formatNumber($delivery['total_quantity']) . '</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>';

        $day = date_format(date_create($delivery['date']), 'd');
        $month = date_format(date_create($delivery['date']), 'm');
        $year = date_format(date_create($delivery['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $phoneContact = '';
        if (!empty($contact) && !empty($contact['phonenumber'])) {
            $phoneContact = ' (' . $contact['phonenumber'] . ')';
        }
        $order = get_table_where('tbl_orders', ['id' => $delivery['order_id']], '', 'row_array');
        $typeOrder = get_table_where('tbl_type_orders', ['id' => $order['type_orders']], '', 'row_array');

        $font_size = '12px';
        echo '
            <h1 class="text-center uppercase">' . lang('tnh_print_delivery') . '</h1>
            <span style="font-size: ' . $font_size . '" class="text-center">
                <span class="italic">' . _l('Loại đơn hàng') . ': ' . $typeOrder['name'] . '</span><br><span class="italic">' . _l('Số giao hàng') . ': ' . $delivery['reference_no'] . '</span><br><span class="italic">' . _l('date') . ': ' . _d($delivery['date'], true) . '</span>
            </span>
            <p style="font-size: ' . $font_size . '"><span>' . _l('Mã đơn hàng') . ':<span class="bold">' . $order['reference_no'] . '</span></span><br><span>' . _l('customers') . ':<span class="bold">' . $company['company'] . '</span></span><br><span>' . _l('tnh_address_delivery') . ':<span>' . (!empty($address_delivery['address']) ? $address_delivery['address'] : '') . '</span></span><br><span>' . _l('Người liên hệ') . ':<span>' . (!empty($contact) ? $contact['firstname'] . $phoneContact : '') . '</span></span><br>
            </p>
            <table class="" cellspacing="0" cellpadding="5" style="width: 100%; border-style: soild; border-color: black;">
                ' . $tdHead . '
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                ' . $tfoot . '
            </table>
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
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'delivery';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);

    }

    public function edit_address_delivery($address_delivery_id) {
        if (!$this->perAddDeliveries) {
            accessDenied($js = true);
        }

        $this->db->select('
            tblshipping_client.*
        ');
        $this->db->from('tblshipping_client');
        $this->db->where('tblshipping_client.id', $address_delivery_id);
        $shipping_client = $this->db->get()->row_array();
        if (empty($shipping_client)) {
            set_alert('danger', lang('Không có địa chỉ để sửa'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '".site_url('releases'). "'; }, 10);</script>");
        }

        $data = [];
        if ($this->input->post('save')) {
            $address_delivery = $this->input->post('address_delivery');
            $this->db->where('tblshipping_client.id', $address_delivery_id);
            $up = $this->db->update('tblshipping_client', ['address' => $address_delivery]);
            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data); die;
        }

        $data['id'] = $address_delivery_id;
        $data['shipping_client'] = $shipping_client;
        $this->load->view('admin/releases/edit_address_delivery', $data);
    }

    public function synthetic_releases(){
        if (!$this->perViewDeliveries && !$this->perViewOwnDeliveries) {
            accessDenied();
        }
        $data['title'] = lang('dt_delivery');
        $this->load->view('admin/releases/synthetic_releases', $data);
    }

    public function getSyntheticDelivery(){
        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $items_search = $this->input->post('items_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_tamp_category_client = "(
            SELECT
                tblcustomer_groups.customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tblcustomers_groups
            JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
        ) tb_tamp_category_client";

        $aColumns = [
            'tbl_deliveries.reference_no as reference_no',
            'tbl_deliveries.date as date',
            'tbl_orders.reference_no as reference_no_order',
            'tbl_orders.reference_no_customer as reference_no_customer',
            'tbl_type_orders.name as name_type_order',
            'tbl_orders.date as date_order',
            'tbl_deliveries.date as date',
            'tblclients.zcode as zcode',
            'tblclients.company as company',
            '(SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                WHERE tblclients.userid = tblcustomer_groups.customer_id) as name_group',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_deliveries';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id',
            'INNER JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            'INNER JOIN tblclients ON tblclients.userid = tbl_deliveries.customer_id',
        ];

        if (!empty($items_search)) {
            $items_search = explode('__', $items_search);
            array_push($where,'AND EXISTS (
                SELECT tbl_delivery_items.delivery_id
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.delivery_id = tbl_deliveries.id
                AND tbl_delivery_items.item_id = ' . $items_search[0] . '
            )');
        }

        array_push($where,'AND EXISTS (
               SELECT 1
               FROM tbl_delivery_items_columns
               WHERE tbl_delivery_items_columns.delivery_id = tbl_deliveries.id
        )');

        if (!$this->perViewDeliveries) {
            array_push($where,'AND tbl_deliveries.created_by = '.get_staff_user_id().'');
        }

        if (!empty($orders_search)) {
            array_push($where,"AND FIND_IN_SET($orders_search, tbl_deliveries.order_id) > 0");
        }

        if (!empty($customer_search)) {
            array_push($where,'AND tbl_deliveries.customer_id = '.$customer_search.'');
        }

        if (!empty($start_date_search)) {
            array_push($where,'AND DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") >= "'.to_sql_date($start_date_search).'"');
        }

        if (!empty($end_date_search)) {
            array_push($where,'AND DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") <= "'.to_sql_date($end_date_search).'"');
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_deliveries.id as id',
        ], 'ORDER BY tbl_deliveries.id desc', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $id_delivery = $aRow['id'];
            $row = array();
            $tb_tamp = '(
                SELECT 
                    (tb_tamp.delivery_id) as delivery_id,
                    (tb_tamp.delivery_item_id) as delivery_item_id,
                    (tb_tamp.command) as command,
                    SUM(tb_tamp.quantity_put) as quantity_put,
                    SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                    SUM(tb_tamp.quantity_loss) as quantity_loss
                FROM (
                    SELECT
                        counter_items_number as counter_items_number,
                        delivery_id as delivery_id,
                        delivery_item_id as delivery_item_id,
                        MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                    FROM `tbl_delivery_items_columns` 
                    WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                    GROUP BY counter_items_number,delivery_id,delivery_item_id
                ) tb_tamp
                GROUP BY tb_tamp.delivery_id,tb_tamp.command,tb_tamp.delivery_item_id  
            ) as tb_tamp';

            $this->db->select('
                tbl_delivery_items.item_id as item_id,
                tb_tamp.command as command,
                tbl_order_items.product_name_customer as product_name_customer,
                tbl_delivery_items.price as price,
                SUM(tb_tamp.quantity_put) as quantity,
                tbl_order_items.is_lot as is_lot,
                tbl_products.code as code_product,
                tbl_products.name as name_product,
                tbl_products.mode as mode,
                tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                0 as quantity_bale,
                GROUP_CONCAT(distinct(DATE_FORMAT(tbl_order_item_shippings.date_shipping,"%d/%m/%Y"))) as date_shipping,
                tblunits.unit as unit
            ');
            $this->db->from('tbl_delivery_items');
            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
            $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
            $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
            $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
            $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command');
            $delivery_items = $this->db->get()->result_array();
            if (!empty($delivery_items)){
                foreach ($delivery_items as $kk => $vv){
                    $row = array();
                    $row[] = '<div class="text-left" style="width: 110px">' . $aRow['reference_no'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 150px">' . _dt($aRow['date']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . ($aRow['reference_no_order']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">'.$aRow['reference_no_customer'].'</div>';
                    $row[] = '<div class="text-left" style="width: 160px">' . $aRow['name_type_order'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['date_order']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . ($vv['date_shipping']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $aRow['zcode'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $aRow['company'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_group'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 70px">' . $vv['command'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $vv['code_product'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . $vv['name_product'] . '</div>';
                    $row[] = '<div class="text-left" style="width: 140px">' . ($vv['product_name_customer']) . '</div>';
                    $row[] = '<div class="text-left" style="width: 100px">' . ($vv['mode']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 70px">' . ($vv['unit']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 100px">' . formatNumber($vv['quantity_sheet_bale']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 60px">' . formatNumber($vv['quantity_bale']) . '</div>';
                    $row[] = '<div class="text-center" style="width: 60px">' . formatNumber($vv['quantity']) . '</div>';
                    $row[] = '<div class="text-right" style="width: 120px">' . formatMoney($vv['price']) . '</div>';
                    if($vv['is_lot'] == 1){
                        $htmlPrice = '<div class="label label-danger">Theo Giá Lô</div>';
                        $amount = $vv['price'];
                    } else {
                        $htmlPrice = '<div class="label label-primary">Nhập Tay</div>';
                        $amount = $vv['quantity'] * $vv['price'];
                    }
                    $row[] = '<div class="text-center" style="width: 120px">' . $htmlPrice . '</div>';
                    $row[] = '<div class="text-right" style="width: 120px">' . formatMoney($amount) . '</div>';

                    $total_quantity += $vv['quantity'];
                    $grand_total += $amount;
                    $output['aaData'][] = $row;
                }
            }
        }
        $output['total_quantity'] = $total_quantity;
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }

    public function exportExcelSyntheticDelivery(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $customer_search = $this->input->post('customer_search');
            $orders_search = $this->input->post('orders_search');
            $items_search = $this->input->post('items_search');
            $end_date_search = $this->input->post('end_date_search');
            $start_date_search = $this->input->post('start_date_search');
            $strDate = 'Từ trước đến nay';
            if (empty($start_date_search) && !empty($end_date_search)) {
                $strDate = '(BAN ĐẦU - ' . $end_date_search . ')';
            }
            if (!empty($start_date_search) && empty($end_date_search)) {
                $strDate = '(' . $start_date_search . ' - HIỆN TẠI' . ')';
            }
            if (!empty($start_date_search) && !empty($end_date_search)) {
                $strDate = '(' . $start_date_search . ' - ' . $end_date_search . ')';
            }
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $tb_tamp_category_client = "(
                SELECT
                    tblcustomer_groups.customer_id,
                    GROUP_CONCAT(tblcustomers_groups.name) as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                GROUP BY tblcustomer_groups.customer_id
            ) tb_tamp_category_client";

            $this->db->select('
                tbl_deliveries.id as id,
                tbl_deliveries.reference_no as reference_no,
                tbl_deliveries.date as date,
                tbl_orders.reference_no as reference_no_order,
                tbl_orders.reference_no_customer as reference_no_customer,
                tbl_type_orders.name as name_type_order,
                tbl_orders.date as date_order, 
                tbl_deliveries.date as date, 
                tblclients.zcode as zcode, 
                tblclients.company as company, 
                (SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
                FROM tblcustomers_groups
                JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
                WHERE tblclients.userid = tblcustomer_groups.customer_id) as name_group, 
            ');
            $this->db->from('tbl_deliveries');
            $this->db->join('tbl_orders','tbl_orders.id = tbl_deliveries.order_id','inner');
            $this->db->join('tbl_type_orders','tbl_type_orders.id = tbl_orders.type_orders','inner');
            $this->db->join('tblclients','tblclients.userid = tbl_deliveries.customer_id','inner');

            if (!empty($items_search)) {
                $items_search = explode('__', $items_search);
                $this->db->where('EXISTS (
                    SELECT tbl_delivery_items.delivery_id
                    FROM tbl_delivery_items
                    WHERE tbl_delivery_items.delivery_id = tbl_deliveries.id
                    AND tbl_delivery_items.item_id = ' . $items_search[0] . '
                )');
            }

            $this->db->where('EXISTS (
               SELECT 1
               FROM tbl_delivery_items_columns
               WHERE tbl_delivery_items_columns.delivery_id = tbl_deliveries.id
            )');

            if (!$this->perViewDeliveries) {
                $this->db->where('tbl_deliveries.created_by = '.get_staff_user_id().'');
            }

            if (!empty($orders_search)) {
                $this->db->where("FIND_IN_SET($orders_search, tbl_deliveries.order_id) > 0");
            }

            if (!empty($customer_search)) {
                $this->db->where('tbl_deliveries.customer_id = '.$customer_search.'');
            }

            if (!empty($start_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") >= "'.to_sql_date($start_date_search).'"');
            }

            if (!empty($end_date_search)) {
                $this->db->where('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") <= "'.to_sql_date($end_date_search).'"');
            }

            $this->db->order_by('tbl_deliveries.id desc');
            $dtSyntheticDelivery = $this->db->get()->result_array();


            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('PHIẾU GIAO HÀNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'Số PGH');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Ngày Lập PGH');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã ĐĐH (TD)');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã ĐĐH (KH)');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Loại ĐĐH');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Ngày Lập ĐĐH');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Ngày Giao');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Mã KH');
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Khách Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Brand');
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Chỉ Lệnh')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Mã Thành Phẩm (TD)')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Tên Thành Phẩm (TD)')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Tên Thành Phẩm (KH)');
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'ĐVT');
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Số Con/Kiện')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Tổng Số Kiện')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'Tổng SL')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$sttRow.'', 'Đơn Giá Bán')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$sttRow.'', 'Loại Giá Áp Dụng')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$sttRow.'', 'Tổng Tiền')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:B$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("C$sttRow:V$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("G$sttRow:G$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman',
                    'color' => array('rgb' => 'FF0000'),
                )
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("M$sttRow:M$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman',
                    'color' => array('rgb' => 'FF0000'),
                )
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("Q$sttRow:R$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman',
                    'color' => array('rgb' => 'FF0000'),
                )
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("U$sttRow:U$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman',
                    'color' => array('rgb' => 'FF0000'),
                )
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticDelivery)) {
                foreach ($dtSyntheticDelivery as $key => $value) {
                    $id_delivery = $value['id'];
                    $tb_tamp = '(
                        SELECT 
                            (tb_tamp.delivery_id) as delivery_id,
                            (tb_tamp.delivery_item_id) as delivery_item_id,
                            (tb_tamp.command) as command,
                            SUM(tb_tamp.quantity_put) as quantity_put,
                            SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                            SUM(tb_tamp.quantity_loss) as quantity_loss
                        FROM (
                            SELECT
                                counter_items_number as counter_items_number,
                                delivery_id as delivery_id,
                                delivery_item_id as delivery_item_id,
                                MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                                MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                            FROM `tbl_delivery_items_columns` 
                            WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                            GROUP BY counter_items_number,delivery_id,delivery_item_id
                        ) tb_tamp
                        GROUP BY tb_tamp.delivery_id,tb_tamp.command,tb_tamp.delivery_item_id  
                    ) as tb_tamp';

                    $this->db->select('
                        tbl_delivery_items.item_id as item_id,
                        tb_tamp.command as command,
                        tbl_order_items.product_name_customer as product_name_customer,
                        tbl_delivery_items.price as price,
                        SUM(tb_tamp.quantity_put) as quantity,
                        tbl_order_items.is_lot as is_lot,
                        tbl_products.code as code_product,
                        tbl_products.name as name_product,
                        tbl_products.mode as mode,
                        tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                        0 as quantity_bale,
                         GROUP_CONCAT(distinct(DATE_FORMAT(tbl_order_item_shippings.date_shipping,"%d/%m/%Y"))) as date_shipping,
                        tblunits.unit as unit
                    ');
                    $this->db->from('tbl_delivery_items');
                    $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                    $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
                    $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
                    $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
                    $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command');
                    $delivery_items = $this->db->get()->result_array();
                    if (!empty($delivery_items)){
                        foreach ($delivery_items as $kk => $vv) {
                            $rowBegin++;
                            $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['reference_no']);
                            $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dt($value['date']))->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['reference_no_order']));
                            $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['reference_no_customer']);
                            $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_type_order']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", _dt($value['date_order']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($vv['date_shipping']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['zcode'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['company'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['name_group'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $vv['command'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $vv['code_product'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $vv['name_product'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin",
                                $vv['product_name_customer'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin",
                                $vv['mode'])->getStyle("O$rowBegin");
                            $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
                                $vv['unit'])->getStyle("P$rowBegin");
                            $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
                                $vv['quantity_sheet_bale'])->getStyle("Q$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity_sheet_bale']));
                            $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
                                $vv['quantity_bale'])->getStyle("R$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity_bale']));
                            $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
                                $vv['quantity'])->getStyle("S$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['quantity']));
                            $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
                                $vv['price'])->getStyle("T$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($vv['price']));
                            if($vv['is_lot'] == 1){
                                $htmlPrice = 'Theo Giá Lô';
                                $amount = $vv['price'];
                            } else {
                                $htmlPrice = 'Nhập Tay';
                                $amount = $vv['quantity'] * $vv['price'];
                            }
                            $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
                                $htmlPrice)->getStyle("U$rowBegin");
                            $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin",
                                $amount)->getStyle("V$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($amount));
                            $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:V$rowBegin")->applyFromArray([
                                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN
                                    )
                                )
                            ]);
                            $objPHPExcel->getActiveSheet()->getStyle("P$rowBegin:Q$rowBegin")->applyFromArray([
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                ),
                            ]);
                            $objPHPExcel->getActiveSheet()->getStyle("R$rowBegin:S$rowBegin")->applyFromArray([
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                ),
                            ]);
                        }
                    }
                }
            }
            $filename = lang('phieu_giao_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function getSyntheticDeliveryOld(){
        $customer_search = $this->input->post('customer_search');
        $orders_search = $this->input->post('orders_search');
        $items_search = $this->input->post('items_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_tamp_category_client = "(
            SELECT
                tblcustomer_groups.customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tblcustomers_groups
            JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
        ) tb_tamp_category_client";

        $aColumns = [
            'tbl_deliveries.reference_no as reference_no',
            'tbl_deliveries.date as date',
            'tbl_orders.reference_no as reference_no_order',
            'tbl_orders.reference_no_customer as reference_no_customer',
            'tbl_type_orders.name as name_type_order',
            'tbl_orders.date as date_order',
            'tbl_deliveries.date as date',
            'tblclients.zcode as zcode',
            'tblclients.company as company',
            'tb_tamp_category_client.name_group as name_group',
            '"" as chi_lenh',
            'tbl_products.code as code_product',
            'tbl_products.name as name_product',
            'tbl_order_items.product_name_customer as product_name_customer',
            'tbl_products.mode as mode',
            'tblunits.unit as unit',
            'tbl_products.quantity_sheet_bale as quantity_sheet_bale',
            '0 as quantity_bale',
            'tbl_delivery_items.quantity as quantity_delivery',
            'tbl_delivery_items.price as price',
            'tbl_order_items.is_lot as is_lot',
            'tbl_delivery_items.amount as amount',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_deliveries';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id',
            'INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_delivery_items.order_item_id',
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id',
            'INNER JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            'INNER JOIN tblclients ON tblclients.userid = tbl_deliveries.customer_id',
            'LEFT JOIN '.$tb_tamp_category_client.' ON tb_tamp_category_client.customer_id = tblclients.userid',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_delivery_items.item_id',
            'INNER JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
        ];

        if (!empty($items_search)) {
            $items_search = explode('__', $items_search);
            array_push($where,'AND EXISTS (
                SELECT tbl_delivery_items.delivery_id
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.delivery_id = tbl_deliveries.id
                AND tbl_delivery_items.item_id = ' . $items_search[0] . '
            )');
        }

        if (!$this->perViewDeliveries) {
            array_push($where,'AND tbl_deliveries.created_by = '.get_staff_user_id().'');
        }

        if (!empty($orders_search)) {
            array_push($where,"AND FIND_IN_SET($orders_search, tbl_deliveries.order_id) > 0");
        }

        if (!empty($customer_search)) {
            array_push($where,'AND tbl_deliveries.customer_id = '.$customer_search.'');
        }

        if (!empty($start_date_search)) {
            array_push($where,'AND DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") >= "'.to_sql_date($start_date_search).'"');
        }

        if (!empty($end_date_search)) {
            array_push($where,'AND DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") <= "'.to_sql_date($end_date_search).'"');
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_deliveries.id as id',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['reference_no'] . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . ($aRow['reference_no_order']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">'.$aRow['reference_no_customer'].'</div>';
            $row[] = '<div class="text-left" style="width: 160px">' . $aRow['name_type_order'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['date_order']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['zcode'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['company'] . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['name_group'] . '</div>';
            $row[] = '<div class="text-left" style="width: 70px">' . $aRow['chi_lenh'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['code_product'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['name_product'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . ($aRow['product_name_customer']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . ($aRow['mode']) . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . ($aRow['unit']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . formatNumber($aRow['quantity_sheet_bale']) . '</div>';
            $row[] = '<div class="text-center" style="width: 60px">' . formatNumber($aRow['quantity_bale']) . '</div>';
            $row[] = '<div class="text-center" style="width: 60px">' . formatNumber($aRow['quantity_delivery']) . '</div>';
            $row[] = '<div class="text-right" style="width: 120px">' . formatMoney($aRow['price']) . '</div>';
            if($aRow['is_lot'] == 1){
                $htmlPrice = '<div class="label label-danger">Theo Giá Lô</div>';
            } else {
                $htmlPrice = '<div class="label label-primary">Nhập Tay</div>';
            }
            $row[] = '<div class="text-center" style="width: 120px">' . $htmlPrice . '</div>';
            $row[] = '<div class="text-right" style="width: 120px">' . formatMoney($aRow['amount']) . '</div>';

            $total_quantity += $aRow['quantity_delivery'];
            $grand_total += $aRow['amount'];
            $output['aaData'][] = $row;
        }
        $output['total_quantity'] = $total_quantity;
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }

    public function edit_discount($id) {
        if (!$this->perPriceDeliveries) {
            accessDenied($js = true);
        }
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        $check = $this->deliveries_model->checkDeliveryInvoice($id);
        if (!empty($check['result'])) {
            refererModel($check['message']); die;
        }

        $items = $this->deliveries_model->getDeliveryItems($id);

        if ($this->input->post('save')) {
            $itemsCur = $this->input->post('itemsCur') ?? null;
            $arrItems = [];
            
            $total_discount_direct_items = 0;
            $grand_total_items = 0;
            if ($items) {
                foreach ($items as $key => $value) {
                    $discount_direct_amount_item = number_unformat($itemsCur[$value['id']]['discount_direct_amount_item'] ?? 0);

                    $quantity = $value['quantity'];
                    $price = $value['price'];
                    $amount = $quantity * $price;
                    $grand_total_item = $amount- $discount_direct_amount_item;

                    $arrItems[] = [
                        'id' => $value['id'],
                        'discount_direct_amount_item' => $discount_direct_amount_item,
                        'total_amount' => $grand_total_item,
                    ];

                    $grand_total_items+= $grand_total_item;
                    $total_discount_direct_items+= $discount_direct_amount_item;
                }
            }

            $grand_total = $grand_total_items;
            $discount_percent = $delivery['discount_percent'];
            $total_discount_percent = 0;
            $total_discount_direct = $delivery['total_discount_direct'];
            if ($discount_percent > 0) {
                $total_discount_percent = $grand_total * ($discount_percent / 100);
            }

            $grand_total -= $total_discount_percent;
            $grand_total -= $total_discount_direct;

            $tax_rate = $delivery['tax_rate'];
            $total_tax = 0;
            if ($tax_rate > 0) {
                $total_tax = $grand_total * ($tax_rate / 100);
            }
            $grand_total += $total_tax;
            $additional_costs = number_unformat($this->input->post('additional_costs'));
            $grand_total+= $additional_costs;

            $options = [
                'total_discount_direct_items' => $total_discount_direct_items,
                'grand_total_items' => $grand_total_items,
                'total_tax' => $total_tax,
                'additional_costs' => $additional_costs,
                'grand_total' => $grand_total,
            ];

            $up = $this->deliveries_model->updateDeliveriesById($id, $options);
            if ($up) {
                $this->deliveries_model->updateBatchDeliveryItems($arrItems);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['fail'] = 0;
                $data['message'] = lang('danger');
            }
            echo responseData($data); die;
        } else {
            $data = [];
            $data['id'] = $id;
            $data['delivery'] = $delivery;
            $data['items'] = $items;
            $this->load->view('admin/releases/edit_discount', $data);
        }

    }

    public function edit_hai_quan($id){
        $data = [];
        $delivery = $this->deliveries_model->rowDeliveriesById($id);
        if ($this->input->post()){
            $this->db->where('tbl_deliveries.id',$id);
            $success = $this->db->update('tbl_deliveries',[
                'code_custom' => !empty($this->input->post('code_custom')) ? $this->input->post('code_custom') : null,
                'date_custom' => !empty($this->input->post('date_custom')) ? to_sql_date($this->input->post('date_custom')) : null,
            ]);
            if ($success){
                $data['result'] = true;
                $data['message'] = lang('Cập nhập thành công');
            } else {
                $data['result'] = false;
                $data['message'] = lang('Cập nhập thất bại');
            }
            echo json_encode($data);die();
        }
        $data['id'] = $id;
        $data['delivery'] = $delivery;
        $this->load->view('admin/releases/edit_hai_quan', $data);
    }
    private function _api_row_export_delivery($r)
    {
        if($r['employee_id'] == 1){
            $r['employee_id'] = 26;
            $r['fullname_employee'] = get_staff_full_name(26);
        }
        // Chuẩn hóa row theo format frontend đang dùng
        return [
            'id' => $r['id'],
            'date' => _dt($r['date']),
            'code_orders' => $r['code_orders'],
            'reference_no' => $r['reference_no'],
            'item_code'        => $r['item_code'],
            'item_name'        => $r['item_name'],
            'unit_name'        => $r['unit_name'],
            'zcode'        => $r['company_short'],
            'company'        => $r['company'],
            'quantity'   => (int) $r['quantity'],
            'warehouseman_id'   => $r['warehouseman_id'],
            'fullname_employee'   => (int) $r['fullname_employee'],
            'image_employee'   => staff_profile_image($r['employee_id'], [
                'staff-profile-image-small-2x mbot5',
            ],'thumb').'<br><span>'.$r['fullname_employee'].'</span>'
        ];
    }
}