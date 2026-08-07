<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Export_different extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('export_different_model');
        $this->isAdmin = is_admin();
    }
    public function updatepricequantity_payment()
    {
        $this->db->select('tbltblexport_different_items.*');
        $this->db->where('tbltblexport_different_items.quantity_payment', 0);
        $items = $this->db->get('tbltblexport_different_items')->result_array();
        foreach ($items as $key => $value) {
            $quantity_stock =  $value['quantity_net'];
            $data_items = get_items($value['product_id'], $value['type']);
            if ($value['type'] == 'nvl') {
                $recipe = $data_items->recipe;
                $paper = $data_items->paper;
                $longs = $data_items->longs;
                $wide = $data_items->wide;
                $exchange_unit = $data_items->exchange_unit;    //chuan
                $exchange_standard_unit = $data_items->exchange_standard_unit; //kho
                $exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
                $quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
                if ($recipe == 1) {
                    $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_unit;
                } elseif ($recipe == 2) {
                    $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
                } elseif ($recipe == 3) {
                    $quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
                }
            } elseif ($value['type'] == 'product') {
                $recipe = 1;
                $paper = 1;
                $longs = 1;
                $wide = 1;
                $exchange_unit = 0;    //chuan
                $exchange_standard_unit = $data_items->conversion_quantity_unit; //kho
                $exchange_unit_payment = 0; //thanh toan
                $quantity_unit = ($quantity_stock / $exchange_standard_unit);
                $quantity_payment = 0;
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
            $this->db->where('id', $value['id']);
            $this->db->update('tbltblexport_different_items', array('quantity_payment' => $quantity_payment));
        }
        echo '<pre>';
        print_arrays($items);
        die;
    }
    public function updateprice()
    {
        $this->db->select('tbltblexport_different_items.*');
        $this->db->where('tbltblexport_different_items.quantity_payment >', 0);
        $this->db->where('tbltblexport_different_items.quantity_payment != tbltblexport_different_items.quantity_net');
        $items = $this->db->get('tbltblexport_different_items')->result_array();
        foreach ($items as $key => $value) {
            $amount = $value['quantity_payment'] * $value['price'];
            $this->db->where('id', $value['id']);
            $this->db->update('tbltblexport_different_items', array('amount' => $amount));
        }
        echo '<pre>';
        print_arrays($items);
        die;
    }
    public function updatepricev2()
    {
        $this->db->select('tblexport_different.*');
        // $this->db->where('tbltblexport_different_items.quantity_payment >', 0);
        // $this->db->where('tbltblexport_different_items.quantity_payment != tbltblexport_different_items.quantity_net');
        $items = $this->db->get('tblexport_different')->result_array();
        foreach ($items as $key => $value) {
            $this->db->select('tbltblexport_different_items.*');
            $this->db->where('id_export_different', $value['id']);
            $items_array = $this->db->get('tbltblexport_different_items')->result_array();
            $amounts = 0;
            foreach ($items_array as $k => $v) {
                $amounts += $v['amount'];
            }
            $this->db->where('id', $value['id']);
            $this->db->update('tblexport_different', array('subtotal' => $amounts));
        }
        echo '<pre>';
        print_arrays($items);
        die;
    }
    public function index()
    {
        if (!has_permission('export_different', '', 'view') && !has_permission('export_different', '', 'view_own')) {
            access_denied('Debt export_different');
        }
        $data['title']          = _l('ch_export_different');
        $data['branch'] = getListBranch();
        $this->load->view('admin/export_different/manage', $data);
    }
    public function table()
    {
        if (!has_permission('export_different', '', 'view') && !has_permission('export_different', '', 'view_own')) {
            access_denied('export_different suppliers');
        }
        $this->app->get_table_data('export_different');
    }
    public function get_quantity($items = '', $localtion = '', $type = '', $id_export = '')
    {
        $quantity = get_table_where('tblwarehouse_items', array('id' => $localtion), '', 'row');
        if (!empty($quantity)) {
            echo json_encode($quantity->product_quantity);
            die;
        } else {
            echo json_encode(0);
            die;
        }
    }
    public function detail($id = '')
    {
        if (!has_permission('export_different', '', 'create')) {
            access_denied('export_different');
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('export_different', '', 'create')) {
                    access_denied('export_different');
                }

                $data                 = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {
                    $id = $this->export_different_model->add($data);
                }

                if ($id) {
                    if (!has_permission('export_different', '', 'edit')) {
                        access_denied('export_different');
                    }
                    $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
                    activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Thêm mới phiếu xuất kho khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('export_different'));
                }
            } else {
                if (!has_permission('export_different', '', 'edit')) {
                    access_denied('export_different');
                }
                if (!has_permission('export_different', '', 'edit')) {
                    access_denied('export_different');
                }
                $success = $this->export_different_model->update($this->input->post(), $id);
                $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
                activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật phiếu xuất kho khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                if ($success == true) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('export_different/detail/' . $id));
            }
        }
        if ($id != '') {
            if (!has_permission('export_different', '', 'edit')) {
                access_denied('export_different');
            }
            $data['title']          = _l('edit_ch_export_different');
            $data['items'] = $this->export_different_model->get($id);
        } else {
            if (!has_permission('export_different', '', 'create')) {
                access_denied('export_different');
            }
            $data['title']          = _l('add_ch_export_different');
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
        $data['suppliers'] = get_table_where('tblsuppliers');
        $data['warehouse'] = get_table_where('tblwarehouse');
        $html = '<option></option>';
        foreach ($data['warehouse'] as $key => $value) {
            $html .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }
        $data['html_warehouse'] = $html;
        $data['localtion_warehouses'] = array();

        $this->load->view('admin/export_different/detail', $data);
    }
    public function int_export_different_view($id = '')
    {
        $data['items'] = $this->export_different_model->get($id);
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblexport_different', 'id_obj' => $id), 'id DESC');
        $this->load->view('admin/export_different/view_modal', $data);
    }
    public function delete($id)
    {
        if (!has_permission('export_different', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }

        $outsource_id = -1;
        $item_material = get_table_where('tbl_outsource_material', ['export_different_id' => $id], '', 'row_array');
        if (!empty($item_material)) {
            $outsource_id = $item_material['outsource_id'];
        }
        $import_outsource = get_table_where('tbl_import_outsource', ['outsource_id' => $outsource_id], '', 'row_array');
        if (!empty($import_outsource)) {
            echo json_encode(array(
                'alert_type' => 'danger',
                'message' => 'Phiếu này liên quan đến gia công ngoài đã nhập gia công không thể xoá',
            ));
            die();
        }

        $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
        activity_log_v2('export_different', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Xóa phiếu chuyển kho [' . $get_code->prefix . '-' . $get_code->code . ']','delete');
        $response = $this->export_different_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {

            //delete outsoure_material
            $outsource_id = -1;
            $item_material = get_table_where('tbl_outsource_material', ['export_different_id' => $id]);
            if (!empty($item_material)) {
                foreach ($item_material as $key => $value) {
                    $outsource_id = $value['outsource_id'];
                }
            }
            $this->db->where('export_different_id', $id);
            $success = $this->db->delete('tbl_outsource_material');
            if ($success) {
                $total_quantity_material_update = 0;
                $total_material_update = 0;
                $grand_total_material_update = 0;
                //update outsource
                if ($outsource_id != -1) {
                    $dataMaterals = get_table_where(
                        'tbl_outsource_material',
                        ['outsource_id' => $outsource_id],
                        '',
                        'result_array'
                    );
                    if (!empty($dataMaterals)) {
                        foreach ($dataMaterals as $key => $value) {
                            $total_quantity_material_update += $value['quantity'];
                            $total_material_update += $value['amount'];
                            $grand_total_material_update += $value['amount'];
                        }
                    }
                    $this->db->where('id', $outsource_id);
                    $this->db->update('tbl_outsource', [
                        'total_quantity_material' => $total_quantity_material_update,
                        'total_material' => $total_material_update,
                        'grand_total_material' => $grand_total_material_update,
                    ]);

                    if (empty($dataMaterals)) {
                        $this->db->where('id', $outsource_id);
                        $this->db->update('tbl_outsource', [
                            'workflow' => 0,
                        ]);
                    }
                }
            }

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
        if (!has_permission('export_different', '', 'approve')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $return_suppliers = get_table_where('tblexport_different', array('id' => $id), '', 'row');
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
            $success = $this->export_different_model->update_status($id, $data);
        }
        if ($success) {
            $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
            activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật trạng thái phiếu chuyển kho [' . $get_code->prefix . '-' . $get_code->code . ']');
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
    // hau
    public function confirm_warehous()
    {
        if (!has_permission('export_different', '', 'approve_warehouse')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        $id = $this->input->post('id');
        $warehouseman_id = $this->input->post('warehouseman_id');
        $ktr = get_table_where('tblexport_different', array('id' => $id), '', 'row');
        $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
        activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật trạng thái duyệt kho phiếu chuyển kho [' . $get_code->prefix . '-' . $get_code->code . ']');
        if (empty($warehouseman_id)) {
            if (!empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_export_confirm_warehous')
                ));
                die;
            }
            $_data = array(
                'warehouseman_id' => get_staff_user_id(),
                'warehouseman_date' => date('Y-m-d H:i:s')
            );
            if (!test_quantity_export_different_warehouses($id)) {
                $data['success'] = false;
                $data['message'] = array(
                    'alert_type' => 'warning',
                    'message' => _l('test_quantyti_time_return')
                );
                $data['item'] = get_items_export_different_warehouses($id);
                echo json_encode($data);
                die;
            } else {
                $success    = $this->db->update('tblexport_different', $_data, array('id' => $id));
                $alert_type = 'warning';
                $message    = _l('ch_no_successful_approval');
                if ($success) {
                    $alert_type = 'success';
                    $message    = _l('ch_successful_approval');
                    log_activity('Export Warehouses items approved [ID export_warehouses: ' . $id);
                    $this->export_different_model->decreaseWarehouse($id);
                }
                echo json_encode(array(
                    'alert_type' => 'success',
                    'message' => _l('ch_export_confirm')
                ));
                die;
            }
        } else {
            if (empty($ktr->warehouseman_id)) {
                echo json_encode(array(
                    'alert_type' => 'warning',
                    'message' => _l('ch_exsit_cancel_confirm_warehouse')
                ));
                die;
            } else {
                $_data = array(
                    'warehouseman_id' => 0,
                    'warehouseman_date' => NULL
                );
                $success    = $this->db->update('tblexport_different', $_data, array('id' => $id));
                if ($success) {
                    $items = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
                    $this->export_different_model->increaseadWarehouse($id, $items);
                    echo json_encode(array(
                        'alert_type' => 'success',
                        'message' => _l('Hủy duyệt kho thành công!')
                    ));
                    die;
                }
            }
        }
    }

    public function print_pdf($id)
    {

        ob_end_clean();
        $data = [];

        $export_different = get_table_where('tblexport_different', ['id' => $id], '', 'row_array');

        $items = $this->export_different_model->get($id);
        $number_print = GetNumberPrint($id, '3');
        $text_number = '<div class="text-left" style="font-size: 9px">
        <span class="bold">' . _l('Lần in') . ':</span> <span>' . $number_print['number'] . '</span><br>
        <span class="bold">' . _l('Giờ in') . ':</span> <span>' . _dt($number_print['date']) . '</span></div>';
        $data['number_print'] = $text_number;
        $data['title'] = lang('PHIẾU XUẤT KHO');
        $data['type'] = 'P';
        $data['img'] = '';

        $widthNumber = '5%';
        $widthNameCode = '20%';
        $widthName = '20%';
        $widthWarehouses = '22%';
        $widthWarehousesLocation = '15%';
        $widthUnit = '8%';
        $widthQuantity = '10%';
        $widthNote = '20%';


        $trHeadItems = '<tr>
            <th class="bold text-center" style="width: ' . $widthNumber . ';">' . _l('tnh_numbers') . '</th>
            <th class="bold text-center" style="width: ' . $widthNameCode . ';">' . _l('Mã hàng') . '</th>
            <th class="bold text-center" style="width: ' . $widthName . ';">' . _l('Tên hàng') . '</th>
            <th class="bold text-center" style="width: ' . $widthWarehouses . ';">' . _l('Kho hàng') . '</th>
            <th class="bold text-center" style="width: ' . $widthWarehousesLocation . ';">' . _l('Vị trí kho') . '</th>
            <th class="bold text-center" style="width: ' . $widthUnit . ';">' . _l('tnh_dvt') . '</th>
            <th class="bold text-center" style="width: ' . $widthQuantity . ';">' . _l('quantity') . '</th>
        </tr>';

        $trFotterItems = '';

        $text = '';
        if ($export_different['object'] == 1) {
            $text = 'KHÁCH HÀNG';
        }
        if ($export_different['object'] == 2) {
            $text = 'NHÀ CUNG CẤP';
        }
        if ($export_different['object'] == 3) {
            $text = 'NHÂN VIÊN';
        }
        if ($export_different['object'] == 4) {
            $text = 'KHÁC';
        }
        $_data = '';
        $phone = '';
        $address = '';
        if ($export_different['object'] == 2) {
            $supplier = get_table_where('tblsuppliers', array('id' => $export_different['id_object']), '', 'row');
            $_data = $supplier->company;
            $phone = $supplier->phone;
            $address = $supplier->address;
        }
        if ($export_different['object'] == 1) {
            $client = get_table_where('tblclients', array('userid' => $export_different['id_object']), '', 'row');
            $_data = $client->company;
            $phone = $client->phonenumber;
            $address = $client->address;
        }
        if ($export_different['object'] == 3) {
            $_data = get_staff_full_name($export_different['id_object']);
        }
        if ($export_different['object'] == 4) {
            $_data = $export_different['object_text'];
        }

        $bodyItems = '';
        $grand_total = 0;
        if (!empty($items)) {
            foreach ($items->items as $key => $value) {
                //                $type_item = $value['type'];
                //                $items_id = $value['product_id'];
                //                if ($type_item == "product") {
                //                    $info = $this->products_model->rowProduct($items_id);
                //                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                //                } else if ($type_item == "items") {
                //                    $info = $this->items_model->rowItems($items_id);
                //                    $unit = $this->unit_model->rowUnit($info['unit']);
                //                }

                $htmlLotCode = '';
                if ($value['type'] == 'nvl' || $value['type'] == 'product') {
                    $htmlLotCode = '<br>' . _l('ch_date_of_manufacture_m') . ': ' . _d($value['date_sx']) . '
                                  <br>' . _l('ch_items_dateed_m') . ':' . _d($value['date_sd']) . '';
                }
                $warehouse_name = $value['warehouse_name'];
                $warehouse_name .= '<div style="font-size: 11px;font-style: italic;" >Lot: ' . $value['lot_code'] . $htmlLotCode . '
                          </div>';

                $tdNumber = '<td class="text-center" style="width: ' . $widthNumber . ';">' . (++$key) . '</td>';
                $tdCode = '<td style="width: ' . $widthNameCode . ';">' . $value['code_item'] . '</td>';
                $tdName = '<td style="width: ' . $widthName . ';">' . $value['name_item'] . '</td>';
                $tdWarehouse = '<td style="width: ' . $widthWarehouses . ';">' . $warehouse_name . '</td>';
                $tdLocation = '<td style="width: ' . $widthWarehousesLocation . ';">' . $value['localtion_name'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: ' . $widthUnit . ';">' . $value['unit_name_stock'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: ' . $widthQuantity . ';">' . formatNumber($value['quantity_net']) . '</td>';


                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdWarehouse . '
                    ' . $tdLocation . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                </tr>';
            }
        }


        $day = date_format(date_create($export_different['date']), 'd');
        $month = date_format(date_create($export_different['date']), 'm');
        $year = date_format(date_create($export_different['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        $staff_create = !empty($export_different['warehouseman_id']) ? get_staff_full_name($export_different['warehouseman_id']) : '';
        echo '
            <table class="" cellspacing="0" cellpadding="1" border="0">
                <tr>
                    <td style="width: 100%;"><span style="font-size: 18px; font-weight: bold;" class="text-center uppercase">' . lang('PHIẾU XUẤT KHO') . '</span></td>
                </tr>
                <tr>
                    <td style="width: 70%;"><span class="text-right">Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                    <td style="width: 30%;"><span class="text-center">Mã Phiếu: ' . $export_different['prefix'] . '-' . $export_different['code'] . '</span></td>
                </tr>
            </table>
            <table class="" cellspacing="0" cellpadding="1" border="0">
                <tr>
                    <td style="width: 100%;" class="bold">' . $text . ': ' . $_data . '</td>
                </tr>
                <tr>
                    <td style="width: 100%;" class="bold">' . _l('Điện thoại') . ': ' . $phone . '</td>
                </tr>
				<tr>
					<td style="width: 100%;" class="bold">' . _l('Địa chỉ') . ': ' . $address . '</td>
				</tr>
				<tr>
					<td style="width: 100%;"><b class="bold">' . _l('Ghi chú') . ':</b> ' . $export_different['note'] . '</td>
				</tr>
				<tr><td></td></tr>
            </table>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    ' . $trHeadItems . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
                <tfoot>
                    ' . $trFotterItems . '
                </tfoot>
            </table>
          
            <br><br><table style="width: 100%" class="">
                <tr nobr="true">
                    <td class="text-center" style="width: 25%;">
                        <span class="bold">Người nhận hàng</span><br><br><br>
                        <span></span>
                    </td>
                    <td class="text-center" style="width: 25%;">
                        <span class="bold">Người giao hàng</span><br><br><br>
                        <span></span>
                    </td>
                    <td class="text-center" style="width: 25%;">
                        <span class="bold">Người nhận tiền</span><br><br><br>
                        <span></span>
                    </td>
                    <td class="text-center" style="width: 25%;">
                        <span class="bold">Người lập phiếu</span><br><br>
                        <p>' . $staff_create . '</p>
                    </td>
                </tr>
            </table>
        ';

        // '.(!empty($delivery['warehouseman_id']) ? get_staff_full_name($delivery['warehouseman_id']) : '').'
        $content = ob_get_contents();
        ob_end_clean();

        $content = str_replace('font-size: 12pt;', '', $content);
        $data['content'] = $content;
        $data['type_page'] = 'deliveries';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function detail_warehouse($id = '')
    {
        if (!has_permission('export_different', '', 'create')) {
            access_denied('export_different');
        }
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('export_different', '', 'create')) {
                    access_denied('export_different');
                }

                $data                 = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {
                    $id = $this->export_different_model->add($data);
                }

                if ($id) {
                    if (!has_permission('export_different', '', 'edit')) {
                        access_denied('export_different');
                    }
                    $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
                    activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Thêm mới phiếu xuất kho khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('export_different'));
                }
            } else {
                if (!has_permission('export_different', '', 'edit')) {
                    access_denied('export_different');
                }
                if (!has_permission('export_different', '', 'edit')) {
                    access_denied('export_different');
                }
                $success = $this->export_different_model->update($this->input->post(), $id);
                $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
                activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật phiếu xuất kho khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                if ($success == true) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('export_different/detail/' . $id));
            }
        }
        if ($id != '') {
            if (!has_permission('export_different', '', 'edit')) {
                access_denied('export_different');
            }
            $data['title']          = _l('edit_ch_export_different');
            $data['items'] = $this->export_different_model->get($id);
        } else {
            if (!has_permission('export_different', '', 'create')) {
                access_denied('export_different');
            }
            $filterStatus = $this->input->get('filterStatus');
            // if (!empty($filterStatus)) {
            //     $data['items_warehouse'] = get_table_where('tblwarehouse_items', array('warehouse_id' => 1, 'product_quantity >' => 0, 'type_items' => $filterStatus));
            // } else {
            //     $data['items_warehouse'] = array();
            // }
            $data['items_warehouse'] = get_table_where('tblwarehouse_items', array('warehouse_id' => WAREHOUSES_ERRORS, 'product_quantity >' => 0));
            $data['title']          = _l('add_ch_export_different');
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
        $data['suppliers'] = get_table_where('tblsuppliers');
        $data['warehouse'] = get_table_where('tblwarehouse');
        $html = '<option></option>';
        foreach ($data['warehouse'] as $key => $value) {
            $html .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }
        $data['html_warehouse'] = $html;
        $data['localtion_warehouses'] = array();

        $this->load->view('admin/export_different/detail_warehouse', $data);
    }

    public function count_all()
    {
        $arrBranch = get_branch_staff();
        if (has_permission('export_different', '', 'view_own') && !is_admin()) {
            $arrIDStaff = employee_manage_staff();
            $coverStr = implode(",", $arrIDStaff);

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.staff_id IN ('.$coverStr.')');
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $all = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.status',1);
            $this->db->where('tblexport_different.staff_id IN ('.$coverStr.')');
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $ch_confirm_22 = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.status',0);
            $this->db->where('tblexport_different.staff_id IN ('.$coverStr.')');
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $dont_approve = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.warehouseman_id !=',0);
            $this->db->where('tblexport_different.staff_id IN ('.$coverStr.')');
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $ch_warehouse_d = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.warehouseman_id ',0);
            $this->db->where('tblexport_different.staff_id IN ('.$coverStr.')');
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $ch_warehouse_nd = $this->db->count_all_results();

        } else {
            $this->db->from('tblexport_different');
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $all = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.status',1);
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $ch_confirm_22 = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.status',0);
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $dont_approve = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.warehouseman_id !=',0);
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $ch_warehouse_d = $this->db->count_all_results();

            $this->db->from('tblexport_different');
            $this->db->where('tblexport_different.warehouseman_id ',0);
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->db->where('tblexport_different.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->db->where('tblexport_different.id',0);
                }
            }
            $ch_warehouse_nd = $this->db->count_all_results();
        }
        $data['all'] = $all;
        $data['ch_confirm_22'] = $ch_confirm_22;
        $data['dont_approve'] = $dont_approve;
        $data['ch_warehouse_d'] = $ch_warehouse_d;
        $data['ch_warehouse_nd'] = $ch_warehouse_nd;

        echo json_encode($data);
    }
}
