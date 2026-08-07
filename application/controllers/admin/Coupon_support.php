<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Coupon_support extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('type_orders_model');
        $this->load->model('status_orders_model');

        $this->fail_factor = [
            'progress'=>'Tiến độ',
            'materials'=>'Nguyên phụ liệu',
            'sample'=>'Mẫu',
            'missing_quantity'=>'Số lượng thiếu',
            'color_difference'=>'Lệch màu',
            'defects'=>'Khiếm khuyết',
            'mold'=>'Không bế',
            'wrong_size'=>'Size - lẫn lộn',
            'pack'=>'Tem - Bao bì - Đóng gói',
            'wrong_address'=>'Giao sai địa chỉ',
            'missing_information'=>'Thông tin thiếu',
            'quotation_failed'=>'Báo giá không đạt',
            'test_method'=>'Phương pháp kiểm tra'
        ];
    }

    public function index()
    {
        $data['title'] = lang('coupon_support');
        $this->load->view('admin/coupon_support/manage', $data);
    }

    public function table_coupon_support()
    {
        // if (!has_permission('set_prices', '', 'view')) {
        //     ajax_access_denied();
        // }
        $this->app->get_table_data('coupon_support');
    }

    public function table_client($clientid = '')
    {
        $this->app->get_table_data('coupon_support_client', [
            'clientid' => $clientid,
        ]);
    }

    public function getView_add()
    {
        $dataResults = array();
        $this->load->view('admin/coupon_support/add', $dataResults);
    }

    public function getView_edit($id = '')
    {
        $dataResults = array();
        $dataResults['id'] = $id;
        $dataResults['dataMain'] = get_table_where('tblcoupon_support',array('id'=>$id),'','row');
        $dataResults['dataMain']->customer_id = 'customers__'.$dataResults['dataMain']->customer_id;
        $this->load->view('admin/coupon_support/edit', $dataResults);
    }

    public function add()
    {
        $data = $this->input->post();
        $customer_id = explode("__", $data['customer_id']);
        $in = array(
            'code' => 'PCS-'.sprintf('%06d', ch_getMaxID('id', 'tblcoupon_support') + 1),
            'appointment_date' => to_sql_date($data['appointment_date'], true),
            'customer_id' => $customer_id[1],
            'employees' => $data['employees'],
            'method' => $data['method'],
            'note' => $data['note'],
            'date_create' => date('Y-m-d'),
            'staff_create' => get_staff_user_id()
        );
        $this->db->insert('tblcoupon_support', $in);
        echo json_encode(array(
            'success' => true,
            'alert_type' => 'success',
            'message' => _l('add_coupon_support_true')
        ));
    }

    public function edit($id = '')
    {
        $data = $this->input->post();
        $customer_id = explode("__", $data['customer_id']);
        $in = array(
            'appointment_date' => to_sql_date($data['appointment_date'], true),
            'customer_id' => $customer_id[1],
            'employees' => $data['employees'],
            'method' => $data['method'],
            'note' => $data['note']
        );
        $this->db->where('id', $id);
        $this->db->update('tblcoupon_support', $in);
        echo json_encode(array(
            'success' => true,
            'alert_type' => 'success',
            'message' => _l('edit_coupon_support_true')
        ));
    }

    public function change_type()
    {
        $data = $this->input->post();
        $this->db->set('method', $data['type']);
        $this->db->where('id', $data['id']);
        $this->db->update('tblcoupon_support');
    }

    public function change_status($id = '')
    {
        $this->db->set('status', 1);
        $this->db->where('id', $id);
        $this->db->update('tblcoupon_support');
        echo json_encode(array(
            'success' => true,
            'alert_type' => 'success',
            'message' => _l('cong_update_true')
        ));
    }

    public function delete_coupon_support($id='')
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcoupon_support');

        echo json_encode(array('success' => true, 'alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));
    }

    public function customer_order()
    {
        $data['type_orders'] = $this->type_orders_model->getTypeOrders();
        $data['status_orders'] = $this->status_orders_model->getStatusOrders();
        $data['branch'] = $this->site_model->getBranch();

        $data['fail_factor'] = $this->fail_factor;
        
        $data['title'] = lang('care_of_clients');
        $this->load->view('admin/coupon_support/customer_order', $data);
    }

    public function getCustomerDelivery()
    {
        $customer_search = $this->input->post('customer_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $type_orders_search = $this->input->post('type_orders_search');
        $status_orders_search = $this->input->post('status_orders_search');
        $items_search = $this->input->post('items_search');
        $branch_search = $this->input->post('branch_search');
        $delivery_search = $this->input->post('delivery_search');

        $aColumns = [
            'tbl_delivery_items.id as id',
            'tblclients.zcode as zcode',
            'tbl_orders.reference_no as reference_no',
            'tbl_deliveries.reference_no as delivery_code',
            'tbl_products.code as item_code',
            'tbl_products.mode as mode',
            'tblunits.unit as unit',
            'tbl_delivery_items.quantity as quantity_delivery',
            'tblcustomer_order.is_pass as is_pass',
            'tblcustomer_order.staff_check as staff_check',
        ];

        foreach ($this->fail_factor as $key => $value) {
            $aColumns[] = '(SELECT tblcustomer_order_detail.value FROM tblcustomer_order_detail WHERE tblcustomer_order_detail.fail_factor = "'.$key.'" AND tblcustomer_order_detail.customer_order_id = tblcustomer_order.id) as '.$key;
        }

        $sIndexColumn = 'id';
        $sTable       = 'tbl_delivery_items';
        $where        = [];

        if (!empty($customer_search)) {
            array_push($where, "AND tbl_orders.customer_id = " . $this->db->escape($customer_search));
        }

		if (!empty($items_search)) {
			$items_search = explode('__', $items_search);
            array_push($where, "AND tbl_delivery_items.item_id = " . $items_search[0]);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_deliveries.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_deliveries.date <= '" . $end_date_search . "'");
        }

        if (!empty($type_orders_search)) {
            array_push($where, "AND tbl_orders.type_orders = " . $type_orders_search . "");
        }

        if (!empty($status_orders_search)) {
            array_push($where, "AND tbl_orders.status_orders = " . $status_orders_search . "");
        }

        if (!empty($branch_search)) {
            array_push($where, "AND tbl_deliveries.id_branch = " . $branch_search . "");
        }

        if (!empty($delivery_search)) {
            array_push($where, "AND tbl_deliveries.id = " . $delivery_search . "");
        }

        $filter = [];

        $join = [
            'INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id',
            'LEFT JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id',
            'INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_delivery_items.item_id AND tbl_delivery_items.type_item = "products"',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',

            'LEFT JOIN tblcustomer_order ON tblcustomer_order.order_item_id = tbl_delivery_items.id',
            'LEFT JOIN tblcustomer_order_detail ON tblcustomer_order_detail.customer_order_id = tblcustomer_order.id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_order.staff_check'
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_orders.id as order_id'
        ], 'GROUP BY tbl_deliveries.id ORDER BY tbl_deliveries.date DESC, tbl_deliveries.reference_no ASC', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $group = '';
        // $quantity_not_delivery = 0;
        // $quantity_orders = 0;
        // $quantity_delivery = 0;
        // $quantity_rest = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];

            $row[0] = '<div class="text-center">' . $start . '</div>';
            $row[1] = '<div class="text-center">' . $aRow['zcode'] . '</div>';
            $row[2] = '<div class="text-left" style="width: 125px;">' . $aRow['reference_no'] . '</div>';
            $row[3] = '<div class="text-left" style="width: 125px;">' . $aRow['delivery_code'] . '</div>';
            $row[4] = '<div class="text-left">' . ($aRow['item_code']) . '</div>';
            $row[5] = '<div class="text-center">' . ($aRow['mode']) . '</div>';
            $row[6] = '<div class="text-center">' . ($aRow['unit']) . '</div>';
            $row[7] = '<div class="text-center">' . formatNumber($aRow['quantity_delivery']) . '</div>';
            $row[8] = '<div class="text-left form-check">
                <input class="form-check-input evaluate" onchange="evaluateOrder(this)" type="radio" id="evaluate_pass_'.$aRow['id'].'" name="evaluate['.$aRow['id'].']" value="1" '. (($aRow['is_pass'] === '1') ? 'checked' : '').'>
                <label class="form-check-label" for="evaluate_pass_'.$aRow['id'].'">Đạt</label><br>
                <input class="form-check-input evaluate" onclick="evaluateOrder(this)" type="radio" id="evaluate_fail_'.$aRow['id'].'" name="evaluate['.$aRow['id'].']" value="0" '. (($aRow['is_pass'] === '0') ? 'checked' : '').'>
                <label class="form-check-label" for="evaluate_fail_'.$aRow['id'].'">Không đạt</label><br>
                '. ($aRow['is_pass'] === '0' ? '<a target="_blank" href="'.base_url('admin/production_report/detail?default_value[id_order]=').$aRow['order_id'].'" class="btn btn-warning">Tạo phiếu báo cáo</a>' : '') .'
            </div>';
            $row[9] = !empty($aRow['staff_check']) ? (staff_profile_image($aRow['staff_check'], ['staff-profile-image'], 'small', ['width'=>'30px', 'style'=>'border-radius: 50%;']) . get_staff_full_name($aRow['staff_check'])) : '';

            $col_num = 10;
            foreach ($this->fail_factor as $k => $v) {
                if ($aRow['is_pass'] === '1') {
                    $row[$col_num] = '<div class="text-center">' . '<div style="color: green;">ĐẠT</div>' . '</div>';
                } else {
                    $row[$col_num] = '<div class="text-center">' . (!empty($aRow[$k]) ? '<div style="color: red; font-weight: bold">X</div>' : '') . '</div>';
                }
                $col_num++;
            }

            // $quantity_delivery += $aRow['quantity_delivery'];
            $output['aaData'][] = $row;
        }
        // $output['quantity_not_delivery'] = $quantity_not_delivery;
        // $output['quantity_orders'] = $quantity_orders;
        // $output['quantity_delivery'] = $quantity_delivery;
        // $output['quantity_rest'] = $quantity_rest;
        echo json_encode($output);
    }
    public function getCustomerOrder()
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

        $aColumns = [
            'tbl_order_items.id as id',
            'tblclients.zcode as zcode',
            'tbl_orders.reference_no as reference_no',
            'tbl_products.code as item_code',
            'tbl_products.mode as mode',
            'tblunits.unit as unit',
            'tbl_order_items.quantity_delivery as quantity_delivery',
            'tblcustomer_order.is_pass as is_pass',
            'tblcustomer_order.staff_check as staff_check',
        ];

        foreach ($this->fail_factor as $key => $value) {
            $aColumns[] = '(SELECT tblcustomer_order_detail.value FROM tblcustomer_order_detail WHERE tblcustomer_order_detail.fail_factor = "'.$key.'" AND tblcustomer_order_detail.customer_order_id = tblcustomer_order.id) as '.$key;
        }

        $sIndexColumn = 'id';
        $sTable       = 'tbl_orders';
        $where        = [
            ' AND tbl_order_items.type_item = "products"',
            ' AND tbl_order_items.quantity_delivery > 0'
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
            array_push($where, "AND tbl_orders.status_orders = " . $status_orders_search . "");
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

        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            'INNER JOIN tbl_order_items ON tbl_order_items.order_id = tbl_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id',
            'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',
            'LEFT JOIN tblbranch ON tblbranch.id = tbl_orders.id_branch',
            'LEFT JOIN tbl_status_orders ON tbl_status_orders.id = tbl_orders.status_orders',
            'LEFT JOIN tbl_species ON tbl_species.id = tbl_products.species',
            'LEFT JOIN tbl_type_print ON tbl_type_print.id = tbl_products.type_print',
            'LEFT JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders',
            'LEFT JOIN ' . $tbDateExpectedDelivery . ' ON tb_order_item_shippings.order_item_id = tbl_order_items.id',
            'LEFT JOIN ' . $tbDelivery . ' ON tb_delivery_item.order_item_id = tbl_order_items.id',
            'LEFT JOIN ' . $tbGroupCustomer . ' ON tb_customer_group.customer_id = tblclients.userid',
            'LEFT JOIN tblcustomer_order ON tblcustomer_order.order_item_id = tbl_order_items.id',
            'LEFT JOIN tblcustomer_order_detail ON tblcustomer_order_detail.customer_order_id = tblcustomer_order.id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_order.staff_check'
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_status_orders.name as name_status_orders',
            'tbl_status_orders.time as time',
            'tbl_orders.is_cancel as is_cancel',
            'tbl_orders.id as order_id',
        ], 'GROUP BY tbl_order_items.id ORDER BY tbl_orders.date DESC, tbl_orders.reference_no ASC', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $group = '';
        $quantity_not_delivery = 0;
        $quantity_orders = 0;
        $quantity_delivery = 0;
        $quantity_rest = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];

            $is_cancel = $aRow['is_cancel'];
            $str_cancel = '';
            if ($is_cancel) {
                $str_cancel = '<div class="mtop5 text-danger">'.lang('tnh_cancelled_order').'</div>';
            }

            $row[0] = '<div class="text-center">' . $start . '</div>';
            $row[1] = '<div class="text-center">' . $aRow['zcode'] . '</div>';
            $row[2] = '<div class="text-left">' . $aRow['reference_no'] . $str_cancel. '</div>';
            $row[3] = '<div class="text-left">' . ($aRow['item_code']) . '</div>';
            $row[4] = '<div class="text-center">' . ($aRow['mode']) . '</div>';
            $row[5] = '<div class="text-center">' . ($aRow['unit']) . '</div>';
            $row[6] = '<div class="text-center">' . formatNumber($aRow['quantity_delivery']) . '</div>';
            $row[7] = '<div class="text-left form-check">
                <input class="form-check-input evaluate" onchange="evaluateOrder(this)" type="radio" id="evaluate_pass_'.$aRow['id'].'" name="evaluate['.$aRow['id'].']" value="1" '. (($aRow['is_pass'] === '1') ? 'checked' : '').'>
                <label class="form-check-label" for="evaluate_pass_'.$aRow['id'].'">Đạt</label><br>
                <input class="form-check-input evaluate" onclick="evaluateOrder(this)" type="radio" id="evaluate_fail_'.$aRow['id'].'" name="evaluate['.$aRow['id'].']" value="0" '. (($aRow['is_pass'] === '0') ? 'checked' : '').'>
                <label class="form-check-label" for="evaluate_fail_'.$aRow['id'].'">Không đạt</label><br>
                '. ($aRow['is_pass'] === '0' ? '<a target="_blank" href="'.base_url('admin/production_report/detail?default_value[id_order]=').$aRow['order_id'].'" class="btn btn-warning">Tạo phiếu báo cáo</a>' : '') .'
            </div>';
            $row[8] = !empty($aRow['staff_check']) ? (staff_profile_image($aRow['staff_check'], ['staff-profile-image'], 'small', ['width'=>'30px', 'style'=>'border-radius: 50%;']) . get_staff_full_name($aRow['staff_check'])) : '';

            $col_num = 9;
            foreach ($this->fail_factor as $k => $v) {
                if ($aRow['is_pass'] === '1') {
                    $row[$col_num] = '<div class="text-center">' . '<div style="color: green;">ĐẠT</div>' . '</div>';
                } else {
                    $row[$col_num] = '<div class="text-center">' . (!empty($aRow[$k]) ? '<div style="color: red; font-weight: bold">X</div>' : '') . '</div>';
                }
                $col_num++;
            }

            // $quantity_delivery += $aRow['quantity_delivery'];
            $output['aaData'][] = $row;
        }
        $output['quantity_not_delivery'] = $quantity_not_delivery;
        $output['quantity_orders'] = $quantity_orders;
        $output['quantity_delivery'] = $quantity_delivery;
        $output['quantity_rest'] = $quantity_rest;
        echo json_encode($output);
    }

    public function evaluateOrder()
    {
        $formData = $this->input->post();
        // echo '<pre>'; var_dump($formData);die;
        foreach ($formData['evaluate'] as $order_item_id =>$value) {
            $customer_order = get_table_where('tblcustomer_order', ['order_item_id' => $order_item_id], '', 'row_array');
            $data = ['is_pass'=>$value, 'staff_check'=>get_staff_user_id()];
            if (!empty($customer_order['id'])) {
                $result = $this->db->update('tblcustomer_order', $data, ['id'=>$customer_order['id']]);
                $customer_order_id = $customer_order['id'];
            } else {
                $data['order_item_id'] = $order_item_id;
                $result = $this->db->insert('tblcustomer_order', $data);
                $customer_order_id = $this->db->insert_id();
            }
        }
        if ($value == 0 && !empty($formData['fail_factor'])) {
            if (!empty($customer_order['id'])) {
                $this->db->delete('tblcustomer_order_detail', ['customer_order_id'=>$customer_order['id']]);
            }
            foreach ($formData['fail_factor'] as $fail_factor =>$value) {
                $data = [
                    'customer_order_id' => $customer_order_id,
                    'fail_factor' => $fail_factor,
                    'value'=>$value
                ];
                $this->db->insert('tblcustomer_order_detail', $data);
            }
        } else {
            if (!empty($customer_order['id'])) {
                $this->db->delete('tblcustomer_order_detail', ['customer_order_id'=>$customer_order['id']]);
            }
        }
        if (!empty($result)) {
            $response['isSuccess'] = 'success';
            $response['message'] = 'Đánh giá thành công';
        } else {
            $response['isSuccess'] = 'danger';
            $response['message'] = 'Đánh giá thất bại';
        }
        echo json_encode($response);die;
    }

    public function getCustomer_order_detail() {
        $formData = $this->input->post();
        $response = [];
        foreach ($formData['evaluate'] as $order_item_id =>$evaluateValue) {
            $customer_order = get_table_where('tblcustomer_order', ['order_item_id' => $order_item_id], '', 'row_array');
            if (!empty($customer_order['id'])) {
                $this->db->select('*');
                $this->db->where('customer_order_id', $customer_order['id']);
                $result = $this->db->get('tblcustomer_order_detail')->result_array();
                $response = [];
                foreach ($result as $key => $value) {
                    $response[$value['fail_factor']] = $value['value'];
                }
            }
        }

        echo json_encode($response);die;
    }

}