<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Service extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Service_model');
        $this->load->model('costs_model');
    }
    public function index()
    {
        if (!has_permission('service', '', 'view') && !has_permission('service', '', 'view_own')) {
            access_denied('service');
        }
        $data['title'] = _l('Service_ticket');
        $data['data_supplier'] = get_table_where('tblsuppliers');
        $this->load->view('admin/service/manage', $data);
    }
    public function detail($id = '')
    {
        if (!has_permission('service', '', 'create')) {
            ajax_access_denied();
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            if ($id == '') {
                if (!has_permission('service', '', 'create')) {
                    access_denied('service');
                }
                if (isset($data['items']) && count($data['items']) > 0) {
                    $in = array();
                    $in['date'] = to_sql_date($data['date']);
                    $in['prefix'] = 'DV-';
                    $in['code'] = sprintf('%06d', ch_getMaxID('id', 'tbl_services') + 1);
                    $in['suppliers_id'] = $data['suppliertid'];
                    $in['note'] = $data['note'];
                    $in['detail_discount'] = str_replace(',', '', $data['discount']);
                    $in['staff_id'] = get_staff_user_id();
                    $in['date_create'] = date('Y-m-d H:i:s');

                    if (!empty($data['type_discount'])) {
                        $in['type_discount'] = 1;
                    } else {
                        $in['type_discount'] = 0;
                    }
                    $in['tax_rate'] = $data['tax_rate'];
                    $in['tax_id'] = $data['tax_id'];
                    $in['type_service'] = $data['id_costs'];
                    $this->db->insert('tbl_services', $in);
                    $insert_id = $this->db->insert_id();
                    if (!empty($insert_id)) {
                        $count = 0;
                        $items = $data['items'];
                        $totals = 0;
                        $total_amount = 0;
                        foreach ($items as $key => $item) {
                            $itm = array();
                            $itm['id_services'] = $insert_id;
                            $itm['name'] = $item['name'];
                            $itm['quanliti'] = str_replace(',', '', $item['quanliti']);
                            $itm['price'] = str_replace(',', '', $item['price']);
                            $total = $itm['quanliti'] * $itm['price'];
                            $itm['vat'] = 0;
                            $itm['total'] = $total;
                            $totals += $total;
                            $this->db->insert('tbl_services_items', $itm);
                        }
                        $discount = 0;
                        if (!empty($data['type_discount'])) {
                            $discount =  str_replace(',', '', $in['detail_discount']);
                        } else {
                            $discount =  ($totals * str_replace(',', '', $in['detail_discount'])) / 100;
                        }
                        $sub = $totals - $discount;
                        $vat = ($sub * $in['tax_rate']) / 100;
                        $sub_total = $sub + $vat;
                        $this->db->update('tbl_services', array('vat' => $vat, 'total_discount' => $discount, 'subtotal' => $sub_total, 'total_novat' => $sub), array('id' => $insert_id));
                        $get_code = get_table_where('tbl_services', array('id' => $insert_id), '', 'row');
                        activity_log_v2('services', 'tbl_services', $id, $get_code->prefix . $get_code->code, 'Thêm phiếu dịch vụ [' . $get_code->prefix . $get_code->code . ']');
                        set_alert('success', _l('ch_added_successfuly'));
                        redirect(admin_url('service'));
                    }
                }
            } else {
                if (!has_permission('service', '', 'edit')) {
                    access_denied('service');
                }
                $in['date'] = to_sql_date($data['date']);
                $in['suppliers_id'] = $data['suppliertid'];
                $in['note'] = $data['note'];
                $in['detail_discount'] = str_replace(',', '', $data['discount']);
                if (!empty($data['type_discount'])) {
                    $in['type_discount'] = 1;
                } else {
                    $in['type_discount'] = 0;
                }
                $in['tax_rate'] = $data['tax_rate'];
                $in['tax_id'] = $data['tax_id'];
                $in['type_service'] = $data['id_costs'];
                $this->db->update('tbl_services', $in, array('id' => $id));
                $count = 0;
                $items = $data['items'];
                $totals = 0;
                $total_amount = 0;
                $id_array = array();
                foreach ($items as $key => $item) {
                    $itm = array();
                    if (!empty($item['id'])) {
                        $itm = array();
                        $itm['name'] = $item['name'];
                        $itm['quanliti'] = str_replace(',', '', $item['quanliti']);
                        $itm['price'] = str_replace(',', '', $item['price']);
                        $total = $itm['quanliti'] * $itm['price'];
                        $itm['vat'] = 0;
                        $itm['total'] = $total;
                        $totals += $total;
                        $this->db->update('tbl_services_items', $itm, array('id' => $item['id']));
                        $id_array[] = $item['id'];
                    } else {
                        $itm['id_services'] = $id;
                        $itm['name'] = $item['name'];
                        $itm['quanliti'] = str_replace(',', '', $item['quanliti']);
                        $itm['price'] = str_replace(',', '', $item['price']);
                        $total = $itm['quanliti'] * $itm['price'];
                        $itm['vat'] = 0;
                        $itm['total'] = $total;
                        $totals += $total;
                        $this->db->insert('tbl_services_items', $itm);
                        $id_array[] = $this->db->insert_id();
                    }
                }
                $discount = 0;
                if (!empty($data['type_discount'])) {
                    $discount =  str_replace(',', '', $in['detail_discount']);
                } else {
                    $discount =  ($totals * str_replace(',', '', $in['detail_discount'])) / 100;
                }

                $sub = $totals - $discount;
                $vat = ($sub * $in['tax_rate']) / 100;
                $sub_total = $sub + $vat;
                $this->db->update('tbl_services', array('vat' => $vat, 'total_discount' => $discount, 'subtotal' => $sub_total, 'total_novat' => $sub), array('id' => $id));
                if (empty($id_array)) {
                    $this->db->where('id_services', $id);
                    $this->db->delete('tbl_services_items');
                } else {
                    $this->db->where('id_services', $id);
                    $this->db->where_not_in('id', $id_array);
                    $this->db->delete('tbl_services_items');
                }
                $get_code = get_table_where('tbl_services', array('id' => $id), '', 'row');
                activity_log_v2('services', 'tbl_services', $id, $get_code->prefix . $get_code->code, 'Sửa phiếu dịch vụ [' . $get_code->prefix . $get_code->code . ']');
                set_alert('success', _l('ch_updated_successfuly'));
                redirect(admin_url('service'));
            }
        }
        if ($id != '') {
            if (!has_permission('service', '', 'edit')) {
                access_denied('service');
            }
            $data['title']          = _l('ch_edit_service');
            $data['invoice'] = get_table_where('tbl_services', array('id' => $id), '', 'row');
            $data['invoice']->items = get_table_where('tbl_services_items', array('id_services' => $id));
        } else {
            if (!has_permission('service', '', 'create')) {
                ajax_access_denied();
            }
            $data['title']          = _l('ch_add_service');
        }
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $this->load->view('admin/service/detail', $data);
    }
    public function update_status($value = '')
    {
        if (!has_permission('service', '', 'approve')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $import = get_table_where('tbl_services', array('id' => $id), '', 'row');
            if ($import->status == 1) {
                die;
            }
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $import->history_status;
            $history_status .= '|' . $staff_id . ',' . $date;
            $data = array(
                'history_status' => $history_status,
                'status' => ($status + 1),
            );
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_services', $data);
        }
        if ($success) {
            $get_code = get_table_where('tbl_services', array('id' => $id), '', 'row');
            activity_log_v2('services', 'tbl_services', $id, $get_code->prefix . $get_code->code, 'Cập nhật trạng thái phiếu dịch vụ [' . $get_code->prefix . $get_code->code . ']');
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
    public function delete($id)
    {
        if (!has_permission('service', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }
        $ktr = get_table_where('tbl_services', array('id' => $id), '', 'row');
        if ($ktr->status_pay > 0) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('Phiếu đã chi không thể xóa')
            ));
            die;
        }
        $response = $this->db->delete('tbl_services', array('id' => $id));
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function table()
    {
        $this->app->get_table_data('service');
    }
    public function SearchSuppliert($id = '')
    {
        $data = [];
        $search = $this->input->get('term');
        $limit_one = 20;
        $this->db->select(
            '
            tblsuppliers.id as id,
            tblsuppliers.company as text,
            CONCAT(tblsuppliers.prefix,tblsuppliers.code) as code_client',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $search);
            $this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $search);
            $this->db->group_end();
        } else
            if (!empty($id)) {
                $this->db->where('tblsuppliers.id', $id);
            }
        $this->db->order_by('tblsuppliers.company', 'DESC');
        $this->db->limit($limit_one);
        $suppliers = $this->db->get('tblsuppliers')->result_array();
        $data['results'] = $suppliers;

        echo json_encode($data);
        die();
    }
    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'import'), '', 'row');
        $dataMain = get_table_where('tbl_services', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tbl_services_items', array('id_services' => $id), '', 'result');
        $suppliers = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $table = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">' . _l('dt_Service_ticket') . '</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_service_suppliers') . ': </span><span>' . $suppliers->company . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_id) . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';

        $width1 = '';
        $width2 = '';
        $width3 = '';
        $width4 = '';
        $width5 = '';
        $dem_temp = 2;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            $width1 = 'width: 5%;';
            $width2 = 'width: 26%;';
            $width3 = 'width: 14%;';
            $width4 = 'width: 25%;';
            $width5 = 'width: 30%;';
        }
        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('dt_service_name') . '</td>';

        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('dt_service_qty') . '</td>';
        $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('dt_service_price') . '</td>';
        $table .= '<td style="' . $width5 . 'text-align: center;font-weight: bold;">' . _l('dt_service_total') . '</td>';
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_total = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: center;">' . $value->name . '</td>';
            $table .= '<td style="' . $width3 . 'text-align: center;">' . $value->quanliti . '</td>';
            $table .= '<td style="' . $width4 . 'text-align: right;">' . formatNumber($value->price) . '</td>';
            $table .= '<td style="' . $width5 . 'text-align: right;">' . formatNumber($value->quanliti * $value->price) . '</td>';
            $sum_total += $value->quanliti * $value->price;
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="4" style="text-align: center;font-weight: bold;">' . _l('dt_sum_total') . '</td><td style="text-align: right;font-weight: bold;">' . number_format($sum_total) . '</td></tr>';
        if ($dataMain->total_discount > 0) {
            $table .= '<tr>
            <td colspan="4" style="text-align: center;font-weight: bold;">' . _l('dt_discount_sum_total') . '</td><td style="text-align: right;font-weight: bold;">' . number_format($dataMain->total_discount) . '</td></tr>';
        }
        $table .= '<tr>
                <td colspan="4" style="text-align: center;font-weight: bold;">' . _l('VAT (' . $dataMain->tax_rate . '%)') . '</td><td style="text-align: right;font-weight: bold;">' . number_format($dataMain->vat) . '</td></tr>';
        $table .= '<tr>
                <td colspan="4" style="text-align: center;font-weight: bold;font-weight: bold;">' . _l('total_vat_ch') . '</td><td style="text-align: right;font-weight: bold;">' . number_format($dataMain->subtotal) . '</td></tr>';
        $table .= '</tbody>
            </table>';
        $table .= '<table class="table table-bordered"  width="100%"><tbody><tr><td style="width: 70%;;text-align: left;font-weight: bold;font-size: 15px;"><b>Tổng giá trị</b></td><td style="width: 20%;;text-align: right;font-weight: bold;font-size: 15px;">' . number_format($dataMain->subtotal) . '</td><td style="width: 10%;;text-align: LEFT;font-weight: bold;font-size: 15px;">VNĐ</td></tr></tbody></table>';
        $data->content .= $table;
        $data->content .= '<span style="text-align: left;font-style: italic;font-weight: bold;">' . _l('Bằng chữ') . ': ' . ucfirst(convert_number_to_words($dataMain->subtotal)) . '</span><br><br>';
        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">XÁC NHẬN BÊN MUA</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">XÁC NHẬN BÊN BÁN</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf      = print_pdf($data);
        $type     = 'I';
        $pdf->Output($dataMain->prefix . '-' . $dataMain->code . '.pdf', $type);
    }
    public function view_service_detail($id = '')
    {
        $data['service'] = $this->Service_model->show_service($id);
        $data['data'] = $this->Service_model->show_detail_service($id);
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tbl_services', 'id_obj' => $id), 'id DESC');
        $this->load->view('admin/service/view_modal', $data);
    }
}
