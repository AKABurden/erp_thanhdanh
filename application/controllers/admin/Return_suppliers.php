<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Return_suppliers extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        $this->load->model('purchase_order_model');
        $this->load->model('return_suppliers_model');
        $this->load->model('costs_model');
    }
    public function count_all()
    {
        if (has_permission('return_suppliers', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select('count(*) as alls', 'tblreturn_suppliers', array('staff_create' => get_staff_user_id()), '', 'row');
            $ch_confirm_22 = get_table_where_select('count(*) as ch_confirm_22', 'tblreturn_suppliers', array('status' => 2, 'staff_create' => get_staff_user_id()), '', 'row');
            $dont_approve = get_table_where_select('count(*) as dont_approve', 'tblreturn_suppliers', array('status' => 1, 'staff_create' => get_staff_user_id()), '', 'row');
            $ch_warehouse_d = get_table_where_select('count(*) as ch_warehouse_d', 'tblreturn_suppliers', array('warehouseman_id !=' => 0, 'staff_create' => get_staff_user_id()), '', 'row');
            $ch_warehouse_nd = get_table_where_select('count(*) as ch_warehouse_nd', 'tblreturn_suppliers', array('warehouseman_id' => 0, 'staff_create' => get_staff_user_id()), '', 'row');
        } else {
            $count = get_table_where_select('count(*) as alls', 'tblreturn_suppliers', array(), '', 'row');
            $ch_confirm_22 = get_table_where_select('count(*) as ch_confirm_22', 'tblreturn_suppliers', array('status' => 2), '', 'row');
            $dont_approve = get_table_where_select('count(*) as dont_approve', 'tblreturn_suppliers', array('status' => 1), '', 'row');
            $ch_warehouse_d = get_table_where_select('count(*) as ch_warehouse_d', 'tblreturn_suppliers', array('warehouseman_id !=' => 0), '', 'row');
            $ch_warehouse_nd = get_table_where_select('count(*) as ch_warehouse_nd', 'tblreturn_suppliers', array('warehouseman_id' => 0), '', 'row');
        }

        $data['all'] = $count->alls;
        $data['dont_approve'] = $dont_approve->dont_approve;
        $data['ch_confirm_22'] = $ch_confirm_22->ch_confirm_22;
        $data['ch_warehouse_d'] = $ch_warehouse_d->ch_warehouse_d;
        $data['ch_warehouse_nd'] = $ch_warehouse_nd->ch_warehouse_nd;

        echo json_encode($data);
    }
    public function getQuantity($item_id, $type, $locationInfo = '')
    {
        $arrLocationInfo = explode('__', $locationInfo);
        $warehouse_id = $arrLocationInfo[0];
        $location = $arrLocationInfo[1];
        $lot_code = $arrLocationInfo[2];
        if (empty($lot_code) || $lot_code === 'NULL' || $lot_code === 'null' || $lot_code == null) {
            $lot_code = NULL;
        }
        $date_sx = $arrLocationInfo[3];
        if (empty($date_sx) || $date_sx === 'NULL' || $date_sx === 'null' || $date_sx == null) {
            $date_sx = NULL;
        }
        $date_sd = $arrLocationInfo[4];
        if (empty($date_sd) || $date_sd === 'NULL' || $date_sd === 'null' || $date_sd == null) {
            $date_sd = NULL;
        }
        $date_use = $arrLocationInfo[5];
        if (empty($date_use) || $date_use === 'NULL' || $date_use === 'null' || $date_use == null) {
            $date_use = NULL;
        }

        $quantity = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse_id, 'localtion' => $location, 'id_items' => $item_id, 'type_items' => $type, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use), '', 'row');
        if (!empty($quantity)) {
            echo json_encode($quantity->product_quantity);
            die;
        } else {
            echo json_encode(0);
            die;
        }
    }

    public function get_quantity($items = '', $warehouse_id = '', $localtion = '', $type = '', $id_export = '')
    {
        $quantity = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse_id, 'localtion' => $localtion, 'id_items' => $items, 'type_items' => $type), '', 'row');
        if (!empty($quantity)) {
            echo json_encode($quantity->product_quantity);
            die;
        } else {
            echo json_encode(0);
            die;
        }
    }
    public function index()
    {
        if (!has_permission('return_suppliers', '', 'view') & !has_permission('return_suppliers', '', 'view_own')) {
            access_denied('return_suppliers');
        }
        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(tblsuppliers.prefix,"-",tblsuppliers.code) as code');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblreturn_suppliers.suppliers_id');
        $this->db->group_by('tblsuppliers.id');
        $data['dataSupplier'] = $this->db->get('tblreturn_suppliers')->result_array();
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->where('tblstaff.active', 1);
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();
        $data['title']          = _l('ch_return_supplierss');
        $this->load->view('admin/return_suppliers/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('return_suppliers');
    }
    public function detail($id = '')
    {
        if (!has_permission('return_suppliers', '', 'create')) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('return_suppliers', '', 'create')) {
                    access_denied('return_suppliers');
                }

                $data                 = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {
                    $id = $this->return_suppliers_model->add($data);
                }

                if ($id) {
                    $get_code = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
                    activity_log_v2('purchase', 'tblreturn_suppliers', $id, $get_code->prefix . $get_code->code, 'Thêm mới phiếu trả hàng [' . $get_code->prefix . $get_code->code . ']');
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('return_suppliers'));
                }
            } else {
                if (!has_permission('return_suppliers', '', 'edit')) {
                    access_denied('return_suppliers');
                }
                $success = $this->return_suppliers_model->update($this->input->post(), $id);
                if ($success == true) {
                    $get_code = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
                    activity_log_v2('purchase', 'tblreturn_suppliers', $id, $get_code->prefix . $get_code->code, 'Cập nhật phiếu trả hàng [' . $get_code->prefix . $get_code->code . ']');
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('return_suppliers/detail/' . $id));
            }
        }
        if ($id != '') {
            $data['title']          = _l('ch_edit_return_supplierss');
            $data['items'] = $this->return_suppliers_model->get($id);
            // echo '<pre>';
            // var_dump($data['items']->items);die;
        } else {
            $data['title']          = _l('ch_add_return_supplierss');
        }
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['warehouse'] = get_table_where('tblwarehouse');
        $data['localtion_warehouses'] = array();

        $this->load->view('admin/return_suppliers/detail', $data);
    }
    public function test_quantity()
    {
        $type_of_document = $this->input->post('type_of_document');
        $id = $this->input->post('id');
        $id_return_suppliers = $this->input->post('id_return_suppliers');
        $test_quantity = 0;
        if ($type_of_document == 1) {

            $product = explode(',', trim($this->input->post('product_id'), ','));
            foreach ($product as $key => $v) {
                $product_id = explode('|', $v);
                $data['items'][$key]['quantity'] = $this->purchase_order_model->sum_quantity_return_suppliers($product_id[0], $id, $product_id[1]);
                if ($data['items'][$key]['quantity'] != NULL) {
                    $data['items'][$key]['type'] = $product_id[0];
                    $data['items'][$key]['id_product'] = $product_id[1];
                    $quantity = get_table_where('tblpurchase_order_items', array('product_id' => $product_id[1], 'type' => $product_id[0], 'id_purchase_order' => $id), '', 'row')->quantity_suppliers;
                    $quantityss = ($quantity - $data['items'][$key]['quantity']);
                    $quantity_old = 0;
                    if (!empty($id_return_suppliers)) {
                        $quantity_old = get_table_where('tblreturn_suppliers_items', array('id_return_suppliers' => $id_return_suppliers, 'product_id' => $product_id[1], 'type' => $product_id[0]), '', 'row')->quantity_net;
                    }
                    if ($product_id[2] > ($quantity + $quantity_old - $data['items'][$key]['quantity'])) {
                        $test_quantity++;
                    }
                    $data['items'][$key]['quantity'] = $quantityss;
                }
            }
        }
        $data['test_quantity'] = $test_quantity;
        echo json_encode($data);
        die;
    }
    public function get_items($id = '', $type = '', $suppliers_id = '')
    {
        $data = $this->return_suppliers_model->get_full_item($id, $type, $suppliers_id);
        $data->html = format_item_color($id, $type);
        $data->avatar = (!empty($data->avatar) ? (file_exists($data->avatar) ? base_url($data->avatar) : (file_exists('uploads/materials/' . $data->avatar) ? base_url('uploads/materials/' . $data->avatar) : (file_exists('uploads/products/' . $data->avatar) ? base_url('uploads/products/' . $data->avatar) : (file_exists('uploads/tools_supplies/' . $data->avatar) ? base_url('uploads/tools_supplies/' . $data->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));

        echo json_encode($data);
    }
    public function get_items_order($id = '', $type = '', $id_order = '')
    {
        $data = $this->purchase_order_model->get_items_return_suppliers_order($id, $type, $id_order);
        if ($data->avatar) {
            $data->avatar = (file_exists($data->avatar) ? base_url($data->avatar) : (file_exists('uploads/materials/' . $data->avatar) ? base_url('uploads/materials/' . $data->avatar) : (file_exists('uploads/products/' . $data->avatar) ? base_url('uploads/products/' . $data->avatar) : base_url('assets/images/preview-not-available.jpg'))));
        } else {
            $data->avatar = base_url('assets/images/preview-not-available.jpg');
        }

        echo json_encode($data);
    }
    public function getLocaltion_warehouses($warehouse_id = '')
    {
        echo json_encode(get_table_where('tbllocaltion_warehouses', array('warehouse' => $warehouse_id)));
    }
    public function int_return_suppliers_view($id = '')
    {
        $data['items'] = $this->return_suppliers_model->get($id);
        $data['warehouse_name'] = get_table_where('tblwarehouse', array('id' => $data['items']->warehouse_id), '', 'row');
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblreturn_suppliers', 'id_obj' => $id), 'id DESC');
        $this->load->view('admin/return_suppliers/view_modal', $data);
    }
    public function delete($id)
    {

        $return_suppliers = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
        if (!empty($return_suppliers->warehouseman_id)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('Vui lòng bỏ duyệt kho để xóa phiếu')
            ));
            die;
        }
        $response = $this->return_suppliers_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {

            insertActivityLog([
                'type_parent_obj' => 'return_suppliers',
                'table_obj' => 'tblreturn_suppliers',
                'id_obj' => $id,
                'name_obj' => $return_suppliers->prefix.'-'.$return_suppliers->code,
                'content' => lang('Xóa phiếu trả hàng') . ' [' . $return_suppliers->prefix.'-'.$return_suppliers->code. ']',
                'actions' => 'delete'
            ]);
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function update_status($value = '')
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $return_suppliers = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
            if ($return_suppliers->status == 2) {
                die;
            }
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $return_suppliers->history_status;
            $history_status .= '|' . $staff_id . ',' . $date;
            $data = array(
                'history_status' => $history_status,
                'status' => ($status + 1),
            );
            $success = $this->return_suppliers_model->update_status($id, $data);
        }
        if ($success) {
            $get_code = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tblreturn_suppliers', $id, $get_code->prefix . $get_code->code, 'Cập nhật trạng thái phiếu trả hàng [' . $get_code->prefix . $get_code->code . ']');
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('ch_successful_approval')
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }
    public function confirm_warehous()
    {
        $id = $this->input->post('id');
        $return_suppliers = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
        $warehouseman_id = $this->input->post('warehouseman_id');
        if (!$id) {
            die('ch_no_items');
        }

        $data = array(
            'warehouseman_id' => get_staff_user_id(),
            'data_warehouseman' => date('Y-m-d H:i:s')
        );
        if ($warehouseman_id) {
            if (empty($return_suppliers->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));
                die;
            }
            $data = array(
                'warehouseman_id' => NULL,
                'data_warehouseman' => NULL
            );
        } else {
            if (!test_quantity_return($id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('test_quantyti_time_return')
                ));
                die;
            }
            if (!empty($return_suppliers->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_confirm_warehouse')
                ));
                die;
            }
        }
        // $this->return_suppliers_model->decreaseWarehouse($id);
        // die;
        $success    = $this->db->update('tblreturn_suppliers', $data, array('id' => $id));
        $alert_type = 'warning';
        $message    = _l('ch_no_successful_approval');
        if ($warehouseman_id) {
            $message    = _l('ch_no_successful_approval_cance');
        }
        if ($success) {
            $get_code = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tblreturn_suppliers', $id, $get_code->prefix . $get_code->code, 'Cập nhật trạng thái duyệt kho phiếu trả hàng [' . $get_code->prefix . $get_code->code . ']');

            $alert_type = 'success';
            $message    = _l('ch_successful_approval');
            if ($warehouseman_id) {
                $message    = _l('ch_successful_approval_cance');
            }
            if (empty($warehouseman_id)) {
                log_activity('Warehouse items approved [ID return_suppliers: ' . $id);
                // $this->return_suppliers_model->increaseWarehouse($id);
                $this->return_suppliers_model->decreaseWarehouse($id);
            } else {
                log_activity('Warehouse items cancel approved [ID return_suppliers: ' . $id);
                $return_suppliers = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
                $items = get_table_where('tblreturn_suppliers_items', array('id_return' => $id));
                $this->return_suppliers_model->increaseadWarehouse($id, $return_suppliers->warehouse_id, $items, $return_suppliers->suppliers_id);
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'return_suppliers'), '', 'row');
        $dataMain = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');

        $dataSub = get_table_where('tblreturn_suppliers_items', array('id_return' => $id));
        $table = '';
        $img = file_get_contents(base_url('uploads/company/') . get_option('company_logo'));
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">_____________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">TRẢ HÀNG</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $suppliers_name = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $data->content .= '
            <span style="font-weight: bold;">' . _l('supplier') . ': </span><span style="font-weight: bold;">' . $suppliers_name->company . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';
        $width1 = '';
        $width2 = '';
        $width3 = '';
        $width4 = '';
        $width5 = '';
        $width6 = '';
        $width7 = '';
        $width8 = '';
        $width9 = '';
        $width10 = '';
        $width11 = '';
        $dem_temp = 2;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_warehouse_localtion_Retrun') {
                    $item_warehouse_localtion_Retrun = true;
                    $dem_temp++;
                }
                if ($value == 'item_unit_Retrun') {
                    $item_unit_Retrun = true;
                    $dem_temp++;
                }
                if ($value == 'item_quantity_Retrun') {
                    $item_quantity_Retrun = true;
                }
                if ($value == 'item_quantity_confirm_Retrun') {
                    $item_quantity_confirm_Retrun = true;
                }
                if ($value == 'item_price_Retrun') {
                    $item_price_Retrun = true;
                }
                if ($value == 'item_promotion_suppliers_Retrun') {
                    $item_promotion_suppliers_Retrun = true;
                }
                if ($value == 'item_tax_Retrun') {
                    $item_tax_Retrun = true;
                }
                if ($value == 'item_invoice_total_Retrun') {
                    $item_invoice_total_Retrun = true;
                }
                if ($value == 'item_note_Retrun') {
                    $item_note_Retrun = true;
                }
            }
            if (isset($item_warehouse_localtion_Retrun) && isset($item_unit_Retrun) && isset($item_quantity_Retrun) && isset($item_quantity_confirm_Retrun) && isset($item_price_Retrun) && isset($item_promotion_suppliers_Retrun) && isset($item_tax_Retrun) && isset($item_invoice_total_Retrun) && isset($item_note_Retrun)) {
                $width1 = 'width: 5%;';
                $width2 = 'width: 16%;';
                $width3 = 'width: 13%;';
                $width4 = 'width: 7%;';
                $width5 = 'width: 7%;';
                $width6 = 'width: 7%;';
                $width7 = 'width: 9%;';
                $width8 = 'width: 9%;';
                $width9 = 'width: 5%;';
                $width10 = 'width: 13%;';
                $width11 = 'width: 9%;';
            }
        }
        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';

        if (isset($item_warehouse_localtion_Retrun)) {
            $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('warehouse_localtion') . '</td>';
        }
        if (isset($item_unit_Retrun)) {
            $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('item_unit') . '</td>';
        }
        if (isset($item_quantity_Retrun)) {
            $table .= '<td style="' . $width5 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . '</td>';
        }
        if (isset($item_quantity_confirm_Retrun)) {
            $table .= '<td style="' . $width6 . 'text-align: center;font-weight: bold;">' . _l('item_quantity_confirm') . '</td>';
        }
        if (isset($item_price_Retrun)) {
            $table .= '<td style="' . $width7 . 'text-align: center;font-weight: bold;">' . _l('Đơn giá') . '</td>';
        }
        if (isset($item_promotion_suppliers_Retrun)) {
            $table .= '<td style="' . $width8 . 'text-align: center;font-weight: bold;">' . _l('promotion_suppliers') . '</td>';
        }
        if (isset($item_tax_Retrun)) {
            $table .= '<td style="' . $width9 . 'text-align: center;font-weight: bold;">' . _l('tax') . '</td>';
        }
        if (isset($item_invoice_total_Retrun)) {
            $table .= '<td style="' . $width10 . 'text-align: center;font-weight: bold;">' . _l('invoice_total') . '</td>';
        }
        if (isset($item_note_Retrun)) {
            $table .= '<td style="' . $width11 . 'text-align: center;font-weight: bold;">' . _l('note') . '</td>';
        }
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_net = 0;
        $sum_price = 0;
        $sum_promotion_suppliers = 0;
        $sum_amount = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $dataLocaltion = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row');

            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: left;">' . $dataItem->name . '</td>';

            if (isset($item_warehouse_localtion_Retrun)) {
                if (!empty($dataLocaltion)) {
                    // $name_parent = str_replace("<i class='fa fa-caret-right text-danger' aria-hidden='true'>","a",$dataLocaltion->name_parent);
                    $table .= '<td style="' . $width3 . 'text-align: center;">' . $dataLocaltion->name_parent . '</td>';
                } else {
                    $table .= '<td></td>';
                }
            }
            if (isset($item_unit_Retrun)) {
                $table .= '<td style="' . $width4 . 'text-align: center;">' . $dataItem->unit_name . '</td>';
            }
            if (isset($item_quantity_Retrun)) {
                $table .= '<td style="' . $width5 . 'text-align: center;">' . formatNumber($value['quantity']) . '</td>';
                $sum_quantity += $value['quantity'];
            }
            if (isset($item_quantity_confirm_Retrun)) {
                $table .= '<td style="' . $width6 . 'text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
                $sum_quantity_net += $value['quantity_net'];
            }
            if (isset($item_price_Retrun)) {
                $table .= '<td style="' . $width7 . 'text-align: right;">' . number_format($value['price']) . '</td>';
                $sum_price += $value['price'];
            }
            if (isset($item_promotion_suppliers_Retrun)) {
                $table .= '<td style="' . $width8 . 'text-align: right;">' . number_format($value['promotion_suppliers']) . '</td>';
                $sum_promotion_suppliers += $value['promotion_suppliers'];
            }
            if (isset($item_tax_Retrun)) {
                $table .= '<td style="' . $width9 . 'text-align: center;">' . number_format($value['tax_rate']) . ' %</td>';
            }
            if (isset($item_invoice_total_Retrun)) {
                $table .= '<td style="' . $width10 . 'text-align: right;">' . number_format($value['amount']) . '</td>';
                $sum_amount += $value['amount'];
            }
            if (isset($item_note_Retrun)) {
                $table .= '<td style="' . $width11 . 'text-align: center;">' . $value['note'] . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        if (isset($item_quantity_Retrun)) {
            $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity) . '</td>';
        }
        if (isset($item_quantity_confirm_Retrun)) {
            $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_net) . '</td>';
        }
        if (isset($item_price_Retrun)) {
            $table .= '<td style="text-align: right;">' . number_format($sum_price) . '</td>';
        }
        if (isset($item_promotion_suppliers_Retrun)) {
            $table .= '<td style="text-align: right;">' . number_format($sum_promotion_suppliers) . '</td>';
        }
        if (isset($item_tax_Retrun)) {
            $table .= '<td></td>';
        }
        if (isset($item_invoice_total_Retrun)) {
            $table .= '<td style="text-align: right;">' . number_format($sum_amount) . '</td>';
        }
        if (isset($item_note_Retrun)) {
            $table .= '<td></td>';
        }
        $table .= '</tr>';
        $table .= '</tbody>
            </table>';
        $data->content .= $table;


        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Giao</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Nhận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Thủ Kho</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;

        $pdf      = print_pdf_return($data);
        $type     = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }
    public function SearchOrder($id = '')
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
        $limit_one = 15;
        $limit_two = 15;
        $limit_all = 50;

        $this->db->select(
            '
            tblpurchase_order.id as id,
            CONCAT(tblpurchase_order.prefix,"",tblpurchase_order.code) as text',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('CONCAT(tblpurchase_order.prefix,"",tblpurchase_order.code)', $search);
            $this->db->group_end();
        } else {
            if ($id > 0) {
                $this->db->group_start();
                $this->db->where('tblpurchase_order.id', $id);
                $this->db->group_end();
            }
        }
        $this->db->where('tblpurchase_order.suppliers_id', $type);
        $this->db->limit($limit_one);
        $items = $this->db->get('tblpurchase_order')->result_array();
        $dataa[0] = array('id' => '', 'text' => '<span style="color: #fff0">a</span>');
        $items = array_merge($dataa, $items);
        if (!empty($items)) {
            $data['results'][] =
                [
                    'children' => $items
                ];
        }
        echo json_encode($data);
        die();
    }
    public function SearchItems($id = '', $types = '', $suppliers_ids = '')
    {

        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
        $suppliers_id = $this->input->get('suppliers_id');
        if (empty($suppliers_id)) {
            $suppliers_id = $suppliers_ids;
        }
        if (empty($type)) {
            $type = $types;
        }
        $limit_one = 12;
        $limit_two = 12;
        $limit_three = 12;
        $limit_all = 50;
        if ($type == -1) {
            $this->db->select(
                '
                    tblitems.id,
                    tblitems.name as text,
                    tblitems.code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tblitems.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tblitems.id AND tblimport_items.type = "items"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tblitems.id AND tblwarehouse_suppliers.type_items = "items" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');
            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tblitems.id');
            $this->db->order_by('name', 'DESC');
            $this->db->limit($limit_one);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
            $count_items = count($items);
            $this->db->select(
                '
                tbl_products.id as id,
                tbl_products.name as text,
                tbl_products.code,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            }
            $this->db->where('tbl_products.type_products', 'semi_products_outside');
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tbl_products.id AND tblimport_items.type = "product"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tbl_products.id AND tblwarehouse_suppliers.type_items = "product" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');

            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tbl_products.id');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm'),
                        'children' => $product
                    ];
            }

            $count_product = count($product);

            $this->db->select(
                '
                tbl_tools_supplies.id as id,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                tbl_tools_supplies.price_sell as price,
                concat("tools") as type,
                CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tbl_tools_supplies.id AND tblimport_items.type = "tools"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tbl_tools_supplies.id AND tblwarehouse_suppliers.type_items = "tools" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');

            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tbl_tools_supplies.id');
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_three);
            // $this->db->limit(($limit_all - $count_product));
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }

            $count_tools = count($tools);
            $this->db->select(
                '
                tbl_materials.id as id,
                tbl_materials.name as text,
                tbl_materials.code,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tbl_materials.id AND tblimport_items.type = "nvl"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tbl_materials.id AND tblwarehouse_suppliers.type_items = "nvl" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');

            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tbl_materials.id');
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(($limit_all - $count_tools - $count_product - $count_items));
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } else
        if ($type == 'items') {
            $this->db->select(
                '
                    tblitems.id as id,
                    tblitems.name as text,
                    tblitems.code,
                    tblitems.price,
                    concat("items") as type,
                    tblitems.avatar as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblitems.name', $search);
                $this->db->or_like('tblitems.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->where('tblitems.id', $id);
                }
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tblitems.id AND tblimport_items.type = "items"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tblitems.id AND tblwarehouse_suppliers.type_items = "items" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tblitems.id');
            $this->db->order_by('name', 'DESC');
            $this->db->limit(50);
            $items = $this->db->get('tblitems')->result_array();
            if (!empty($items)) {
                $data['results'][] =
                    [
                        'text' => _l('Sản phẩm'),
                        'children' => $items
                    ];
            }
        } else
        if ($type == 'product') {
            $this->db->select(
                '
                tbl_products.id as id,
                tbl_products.name as text,
                tbl_products.code,
                tbl_products.price_sell as price,
                concat("product") as type,
                CONCAT("uploads/products/", "", tbl_products.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $search);
                $this->db->or_like('tbl_products.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_products.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tbl_products.id AND tblimport_items.type = "product"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tbl_products.id AND tblwarehouse_suppliers.type_items = "product" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');
            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tbl_products.id');
            $this->db->where('tbl_products.type_products', 'semi_products_outside');
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->limit(50);
            // $this->db->limit(($limit_all - $count_product));
            $product = $this->db->get('tbl_products')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Bán thành phẩm'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'nvl') {
            $this->db->select(
                '
                tbl_materials.id as id,
                tbl_materials.name as text,
                tbl_materials.code,
                tbl_materials.price_sell as price,
                concat("nvl") as type,
                CONCAT("uploads/materials/", "", tbl_materials.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $search);
                $this->db->or_like('tbl_materials.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_materials.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tbl_materials.id AND tblimport_items.type = "nvl"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tbl_materials.id AND tblwarehouse_suppliers.type_items = "nvl" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');
            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tbl_materials.id');
            $this->db->order_by('tbl_materials.name', 'DESC');
            $this->db->limit(50);
            $product = $this->db->get('tbl_materials')->result_array();
            if (!empty($product)) {
                $data['results'][] =
                    [
                        'text' => _l('Nguyên vật liệu'),
                        'children' => $product
                    ];
            }
        } elseif ($type == 'tools') {
            $this->db->select(
                '
                    tbl_tools_supplies.id as id,
                    tbl_tools_supplies.name as text,
                    tbl_tools_supplies.code,
                    tbl_tools_supplies.price_sell as price,
                    concat("tools") as type,
                    CONCAT("uploads/tools_supplies/", "", tbl_tools_supplies.images, "") as img',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tbl_tools_supplies.name', $search);
                $this->db->or_like('tbl_tools_supplies.code', $search);
                $this->db->group_end();
            } else {
                if ($id > 0) {
                    $this->db->group_start();
                    $this->db->where('tbl_tools_supplies.id', $id);
                    $this->db->group_end();
                }
            }
            $this->db->join('tblimport_items', 'tblimport_items.product_id = tbl_tools_supplies.id AND tblimport_items.type = "tools"');
            $this->db->join('tblwarehouse_suppliers', 'tblwarehouse_suppliers.id_items = tbl_tools_supplies.id AND tblwarehouse_suppliers.type_items = "tools" AND tblwarehouse_suppliers.suppliers_id =' . $suppliers_id);
            $this->db->where('tblwarehouse_suppliers.product_quantity > 0');
            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import');
            $this->db->where('tblimport.suppliers_id', $suppliers_id);
            $this->db->group_by('tbl_tools_supplies.id');
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit(50);
            $tools = $this->db->get('tbl_tools_supplies')->result_array();
            if (!empty($tools)) {
                $data['results'][] =
                    [
                        'text' => _l('Công cụ - Vật tư'),
                        'children' => $tools
                    ];
            }
        }
        echo json_encode($data);
        die();
    }
}
