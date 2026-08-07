<?php

use ParagonIE\Sodium\Core\Curve25519\Ge\P2;

defined('BASEPATH') or exit('No direct script access allowed');
class Purchase_invoice extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');
        $this->load->model('import_model');
    }
    public function index()
    {
        if (!has_permission('purchase_invoice', '', 'view') && !has_permission('purchase_invoice', '', 'view_own')) {
            access_denied('purchase_invoice');
        }
        // $data['suppliers'] = get_table_where('tblsuppliers');
        $this->db->select('tblsuppliers.*');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_invoice.id_supplier');
        $this->db->group_by('tblsuppliers.id');
        $data['suppliers'] = $this->db->get('tblpurchase_invoice')->result_array();

        $data['title']          = _l('Danh sách hóa đơn');
        $this->load->view('admin/purchase_invoice/manage', $data);
    }
    public function table()
    {

        $this->app->get_table_data('purchase_invoice');
    }
    public function update_link($id = '')
    {
        $alert_type = 'warning';
        $message    = _l('cong_update_false');
        if ($this->input->post()) {
            if ($this->db->update('tblpurchase_invoice', array('link' => $this->input->post('link')), array('id' => $id))) {
                $alert_type = 'success';
                $message    = _l('cong_update_true');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function electronic_bill($id = '')
    {
        $data['id_invoice'] = $id;
        $data['invoice'] = get_table_where('tblpurchase_invoice', array('id' => $id), '', 'row');
        $data['invoice']->attachments = $this->get_invoice_attachments('', $id);
        $this->load->view('admin/purchase_invoice/electronic_bill', $data);
    }
    public function payment_all()
    {
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
        $datas = $this->input->post();
        $data['total'] = 0;
        $data['id_old'] = trim($datas['ids'], ',');
        foreach (explode(',', trim($datas['ids'], ',')) as $key => $value) {
            $invoice = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
            $data['total'] += $invoice->total_price_befor_vat;
        }
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/purchase_invoice/payment_all_modal', $data);
    }
    public function pay_slip_all()
    {
        if (!has_permission('pay_slip', '', 'create')) {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => $alert_type,
                'message' => 'Bạn không có quyền tạo phiếu chi mua hàng!'
            ));
            die;
        }
        $success = false;
        $alert_type = 'warning';
        $message    = _l('ch_added_successfuly_not');
        if ($this->input->post()) {
            $data = $this->input->post();
            $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
            $_data['date'] = date('Y-m-d H:i:s');
            $_data['staff_id'] = get_staff_user_id();
            $_data['receiver'] = $data['receiver'];
            $_data['id_costs'] = $data['id_costs'];
            $_data['payment_mode'] = $data['payment_mode'];
            $_data['payment'] = str_replace(',', '', $data['payment']);
            $_data['total'] = str_replace(',', '', $data['total']);
            $_data['note'] = $data['note'];
            $_data['id_supplierss'] = $data['id_supplierss'];
            $_data['type'] = 1;
            $_data['id_old'] = $data['id_old'];
            $_data['prefix'] = get_option('prefix_pay_slip');
            $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
            $this->db->insert('tblpay_slip', $_data);
            $id_pay = $this->db->insert_id();
            if ($id_pay) {
                $id_old = explode(',', $data['id_old']);
                foreach ($id_old as $key => $value) {
                    $__data['id_old'] = $value;
                    $__data['id_pay_slip'] = $id_pay;
                    $__data['type'] = 1;
                    $invoice = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
                    $__data['total'] = $invoice->total_price_befor_vat;
                    $__data['payment'] = $invoice->total_price_befor_vat;
                    $this->db->insert('tblpay_slip_detail', $__data);
                    $this->db->update('tblpurchase_invoice', array('amount_paid' => $invoice->total_price_befor_vat, 'status' => 2), array('id' => $invoice->id));

                    $get_code = get_table_where('tblpurchase_invoice', array('id' => $invoice->id), '', 'row');
                    activity_log_v2('work_debt_buy', 'tblpurchase_invoice', $invoice->id, $get_code->code_invoice, 'Thêm mới phiếu chi hóa đơn mua hàng [' . $get_code->code_invoice . ']');
                }
                $success = true;
                $alert_type = 'success';
                $message    = _l('ch_added_successfuly');
            }
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function payment($id = '')
    {
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['id_invoice'] = $id;
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
        $data['invoice'] = get_table_where('tblpurchase_invoice', array('id' => $id), '', 'row');
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/purchase_invoice/payment_modal', $data);
    }
    public function pay_slip($id = '')
    {
        if (!has_permission('pay_slip', '', 'create')) {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => $alert_type,
                'message' => 'Bạn không có quyền tạo phiếu chi mua hàng!'
            ));
            die;
        }
        $success = false;
        $alert_type = 'warning';
        $message    = _l('ch_added_successfuly_not');
        if ($this->input->post()) {
            $invoicess = get_table_where('tblpurchase_invoice', array('id' => $id), '', 'row');
            $data = $this->input->post();
            if (($invoicess->total_price_befor_vat - ($invoicess->amount_paid + $invoicess->price_other_expenses)) < str_replace(',', '', $data['payment'])) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'warning',
                    'message' => 'Có sự thay đổi về giá trị vui lòng xem lại !'
                ));
                die;
            }
            $data = $this->input->post();
            $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
            $_data['date'] = date('Y-m-d H:i:s');
            $_data['id_costs'] = $data['id_costs'];
            $_data['staff_id'] = get_staff_user_id();
            $_data['receiver'] = $data['receiver'];
            $_data['payment_mode'] = $data['payment_mode'];
            $_data['payment'] = str_replace(',', '', $data['payment']);
            $_data['total'] = str_replace(',', '', $data['total']);
            $_data['note'] = $data['note'];
            $_data['id_supplierss'] = $invoicess->id_supplier;
            $_data['type'] = 1;
            $_data['id_old'] = $id;
            $_data['prefix'] = get_option('prefix_pay_slip');
            $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
            $this->db->insert('tblpay_slip', $_data);
            $id_pay = $this->db->insert_id();
            if ($id_pay) {
                $__data['id_old'] = $id;
                $__data['id_pay_slip'] = $id_pay;
                $__data['type'] = 1;
                $__data['total'] = str_replace(',', '', $data['total']);
                $__data['payment'] = str_replace(',', '', $data['payment']);
                $this->db->insert('tblpay_slip_detail', $__data);
                $invoice = get_table_where('tblpurchase_invoice', array('id' => $id), '', 'row');
                $amount_paid =  $invoice->amount_paid + $__data['payment'];
                if (($amount_paid + $invoice->price_other_expenses) == $invoice->total_price_befor_vat) {
                    $status = 2;
                } else {
                    $status = 1;
                }
                $this->db->update('tblpurchase_invoice', array('amount_paid' => $amount_paid, 'status' => $status), array('id' => $invoice->id));
                $get_code = get_table_where('tblpurchase_invoice', array('id' => $id), '', 'row');
                activity_log_v2('work_debt_buy', 'tblpurchase_invoice', $id, $get_code->code_invoice, 'Thêm mới phiếu chi hóa đơn mua hàng [' . $get_code->code_invoice . ']');
                $success = true;
                $alert_type = 'success';
                $message    = _l('ch_added_successfuly');
            }
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function delele_file($id = "")
    {
        if (is_numeric($id)) {
            $file = get_table_where('tblfiles', array('id' => $id), '', 'row');
            $this->db->where('id', $id);
            if ($this->db->delete('tblfiles')) {
                if (file_exists('uploads/invoices/' . $file->rel_id . '/' . $file->file_name)) {
                    unlink('uploads/invoices/' . $file->rel_id . '/' . $file->file_name);
                }
            }
        }
    }
    public function get_invoice_attachments($attachment_id = '', $id = '')
    {
        if (is_numeric($attachment_id)) {
            $this->db->where('id', $attachment_id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'invoice');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }
    public function add_attachment($id)
    {
        $return = handle_invoice_attachment($id);
        $returns = get_table_where('tblfiles', array('id' => $return), '', 'row');
        if ($return) {
            if (substr($returns->filetype, 0, 5) == 'image') {
                $returns->image = true;
            } else {
                $returns->image = false;
            }
            $success = true;
            $alert_type = 'success';
            $message    = _l('Tải lên thành công', _l('Hóa đơn thuế'));
        } else {
            $success = false;
            $alert_type = 'danger';
            $message    = _l('Tải lên thất bại', _l('Hóa đơn thuế'));
        }

        echo json_encode(array(
            'alert_type' => $alert_type,
            'success' => $success,
            'message' => $message,
            'result'  => $returns
        ));
    }
    public function add_all()
    {
        $alert_type = 'warning';
        $message    = _l('ch_added_successfuly_not');
        if (!has_permission('purchase_invoice', '', 'create')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => $alert_type,
                'message' => 'Bạn không có quyền lập hóa đơn!'
            ));
            die;
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $count = 0;
            $id_import_all = explode(',', trim($data['id_import_all'], ','));
            $_data['id_supplier'] = $data['id_supplier'];
            $_data['date_invoice'] = to_sql_date($data['date_invoice_all']);
            $_data['date_create'] = date('Y-m-d H:i:s');
            $_data['code_invoice'] = $data['code_invoice_all'];
            $_data['note'] = $data['note_all'];
            $_data['staff_create'] = get_staff_user_id();
            //này sẽ đổi lại là id đơn hàng
            $_data['id_import'] = trim($data['id_import_all'], ',');
            $_data['total_price_befor_vat'] = 0;
            $_data['total_price_vat'] = 0;
            $_data['total_price_affter_vat'] = 0;
            $_data['promotion_expected'] = 0;
            $_data['price_other_expenses'] = 0;
            foreach ($id_import_all as $key => $value) {
                $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
                $_data['total_price_befor_vat'] += $import->totalAll_suppliers;
                $_data['total_price_vat'] += $import->totalAll_suppliers + $import->promotion_expected - $import->total_novat;
                $_data['total_price_affter_vat'] += $import->total_novat;
                $_data['promotion_expected'] += $import->promotion_expected;
                $_data['price_other_expenses'] += $import->price_other_expenses;
            }
            if ($this->db->insert('tblpurchase_invoice', $_data)) {
                $id_invoice = $this->db->insert_id();
                foreach ($id_import_all as $key => $value) {
                    $this->db->update('tblpurchase_order', array('red_invoice' => $id_invoice), array('id' => $value));
                }
                $alert_type = 'success';
                $message    = _l('ch_added_successfuly');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function add()
    {
        $alert_type = 'warning';
        $message    = _l('ch_added_successfuly_not');
        if (!has_permission('purchase_invoice', '', 'create')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => $alert_type,
                'message' => 'Bạn không có quyền lập hóa đơn!'
            ));
            die;
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['date_invoice'] = to_sql_date($data['date_invoice']);
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['staff_create'] = get_staff_user_id();
            //id import sẽ là id đơn hàng
            $import = get_table_where('tblpurchase_order', array('id' => $data['id_import']), '', 'row');
            $data['total_price_befor_vat'] = $import->totalAll_suppliers;
            $data['total_price_vat'] = $import->totalAll_suppliers + $import->promotion_expected - $import->total_novat;
            $data['total_price_affter_vat'] = $import->total_novat;
            $data['promotion_expected'] = $import->promotion_expected;
            $data['price_other_expenses'] = $import->price_other_expenses;
            if ($this->db->insert('tblpurchase_invoice', $data)) {
                $id_invoice = $this->db->insert_id();
                $this->db->update('tblpurchase_order', array('red_invoice' => $id_invoice), array('id' => $data['id_import']));
                $alert_type = 'success';
                $message    = _l('ch_added_successfuly');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function update($id)
    {
        $alert_type = 'warning';
        $message    = _l('cong_update_false');
        if ($this->input->post()) {
            $data = $this->input->post();
            $_data['code_invoice'] = $data['code_invoice'];
            $_data['date_invoice'] = to_sql_date($data['date_invoice']);
            $_data['note'] = $data['note'];
            if ($this->db->update('tblpurchase_invoice', $_data, array('id_import' => $id))) {
                $alert_type = 'success';
                $message    = _l('cong_update_true');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function delete($id)
    {
        if (!has_permission('purchase_invoice', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        $ktr = get_table_where('tblpurchase_invoice', array('id' => $id), '', 'row');
        $response = $this->db->delete('tblpurchase_invoice', array('id' => $id));
        if ($ktr->type_create == 0) {
            if ($response) {
                if (file_exists(get_upload_path_by_type('invoice') . $id)) {
                    delete_dir_ch(get_upload_path_by_type('invoice') . $id);
                }
                $response = $this->db->delete('tblpurchase_invoice_items', array('purchase_invoice_id' => $id));
                $id_import = explode(',', $ktr->id_import);
                foreach ($id_import as $key => $value) {
                    $this->import_model->refreshRed_invoice($value); // Cập nhật lại cột Đã xuất hóa đơn hết chưa
                    $this->import_model->refreshInvoice_id($value); // Cập nhật lại cột Id Hóa đơn

                    // $this->db->update('tblpurchase_order', array('red_invoice' => 0), array('id' => $value));
                }
                insertActivityLog([
                    'type_parent_obj' => 'purchase_invoice',
                    'table_obj' => 'tblpurchase_invoice',
                    'id_obj' => $id,
                    'name_obj' => $ktr->code_invoice,
                    'content' => lang('Xóa hóa đơn mua hàng') . ' [' . $ktr->code_invoice . ']',
                    'actions' => 'delete'
                ]);
                $alert_type = 'success';
                $message    = _l('ch_delete');
            }
        } else {
            if ($response) {
                if (file_exists(get_upload_path_by_type('invoice') . $id)) {
                    delete_dir_ch(get_upload_path_by_type('invoice') . $id);
                }
                $items = get_table_where('tblpurchase_invoice_items', array('purchase_invoice_id' => $id));
                $response = $this->db->delete('tblpurchase_invoice_items', array('purchase_invoice_id' => $id));
                foreach ($items as $key => $value) {
                    $this->db->where('id', $value['id_import_item']);
                    $this->db->update('tblsuggestion', ['red_invoice' => 0]);
                }

                insertActivityLog([
                    'type_parent_obj' => 'purchase_invoice',
                    'table_obj' => 'tblpurchase_invoice',
                    'id_obj' => $id,
                    'name_obj' => $ktr->code_invoice,
                    'content' => lang('Xóa hóa đơn mua hàng') . ' [' . $ktr->code_invoice . ']',
                    'actions' => 'delete'
                ]);

                $alert_type = 'success';
                $message    = _l('ch_delete');
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    // yct start
    public function modalAdd()
    {
        $data['id'] = 0;

        $data['taxs'] = $this->site_model->getTaxs();
        $data['title'] = 'Tạo hóa đơn mua hàng';
        $this->load->view('admin/purchase_invoice/add_modal', $data);
    }

    public function loadSuppliers()
    {
        $data = [];
        $term = $this->input->get('term');
        $this->db->select('tblsuppliers.id, tblsuppliers.company as text');
        // $this->db->join('tblsuppliers', 'tblsuppliers.id = tblimport.suppliers_id', 'inner');
        $this->db->group_by('tblsuppliers.id');
        $this->db->where('tblsuppliers.active', 1);
        if (!empty($term)) {
            $this->db->like('tblsuppliers.company', $term);
        }
        $dbresult = $this->db->get('tblsuppliers')->result_array();

        $data['results'] = [
            ['text' => lang('c_tasks_supplier'), 'children' => $dbresult]
        ];

        echo json_encode($data);
    }

    public function loadSuggestion()
    {
        $this->db->select('tblsuggestion.id as id, tblsuggestion.code as reference_no, tblsuggestion.date, tblsuggestion.price_total as total');
        $this->db->where('tblsuggestion.purchase_order_id', NULL);
        $this->db->order_by('tblsuggestion.date', 'desc');
        $this->db->where('tblsuggestion.red_invoice', 0);
        $dbresult = $this->db->get('tblsuggestion')->result_array();
        $arrImports = [];
        $count = 0;
        foreach ($dbresult as $key => $import) {
            $import['total'] = number_format($import['total']);
            $import['date'] = _d($import['date']);
            $flag = false;
            foreach ($arrImports as $grImport) {
                if ($grImport['date'] == $import['date']) {
                    $arrImports[$count]['imports'][] = $import;
                    $flag = true;
                }
            }
            if (!$flag) {
                $count++;
                $arrImports[$count]['date'] = $import['date'];
                $arrImports[$count]['imports'][] = $import;
            }
        }

        $data['arrImports'] = $arrImports;
        echo json_encode($data);
    }
    public function loadImports($supplier_id)
    {
        $this->db->select('tblimport.id as id, CONCAT(tblimport.prefix, "-", tblimport.code) as reference_no, tblimport.date, tblimport.total');
        $this->db->where('tblimport.suppliers_id', $supplier_id);
        $this->db->order_by('tblimport.date', 'desc');
        // $this->db->where('tblimport.red_invoice', 0);
        $dbresult = $this->db->get('tblimport')->result_array();
        $arrImports = [];
        $count = 0;
        foreach ($dbresult as $key => $import) {
            if ($this->checkInvoicedImport($import['id'])) {
                unset($dbresult[$key]);
            } else {
                $import['total'] = number_format($import['total']);
                $import['date'] = _d($import['date']);
                $flag = false;
                foreach ($arrImports as $grImport) {
                    if ($grImport['date'] == $import['date']) {
                        $arrImports[$count]['imports'][] = $import;
                        $flag = true;
                    }
                }
                if (!$flag) {
                    $count++;
                    $arrImports[$count]['date'] = $import['date'];
                    $arrImports[$count]['imports'][] = $import;
                }
            }
        }

        $data['arrImports'] = $arrImports;
        echo json_encode($data);
    }

    public function loadItems()
    {
        $data = array();
        $arr_import_id = array();
        if (!empty($this->input->post())) {
            $dataPost = $this->input->post();
            if (!empty($dataPost['arr_import_id'])) {
                $arr_import_id = $dataPost['arr_import_id'];
            }
        }
        $arr_items = array();
        foreach ($arr_import_id as $key => $import_id) {
            $items = $this->import_model->get_items_import($import_id);
            // var_dump($items);die;
            $importCode = $this->getImportCodeById($import_id);
            $arr_items[$key]['import_code'] = $importCode;
            $itemsTemp = array();
            foreach ($items as $key2 => $item) {
                // var_dump($this->isInvoice($item['id']));
                if ($this->isInvoice($item['id'])) {
                    continue;
                }
                $itemsTemp[$key2]['import_item_id'] = $item['id'];
                $itemsTemp[$key2]['import_code'] = $importCode;
                $itemsTemp[$key2]['product_id'] = $item['product_id'];
                $itemsTemp[$key2]['code_item'] = $item['code_item'];
                $itemsTemp[$key2]['name_item'] = $item['name_item'];
                $itemsTemp[$key2]['price'] = ($item['quantity_payment'] * $item['price']) - $item['promotion_suppliers'];
            }
            if (!empty($itemsTemp)) {
                $arr_items[$key]['items'] = $itemsTemp;
            }
        }
        // var_dump($arr_items);die;
        $data['items'] = $arr_items;
        echo json_encode($data);
    }

    public function add_details()
    {
        $alert_type = 'warning';
        $message    = _l('ch_added_successfuly_not');
        if (!has_permission('purchase_invoice', '', 'create')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => $alert_type,
                'message' => 'Bạn không có quyền lập hóa đơn!'
            ));
            die;
        }
        if ($this->input->post()) {
            $dataPost = $this->input->post();
            $type_create = $dataPost['type_create'];
            if ($type_create == 0) {
                //             print_arrays($dataPost);
                $arrEmptyImport = $this->checkInvoicedItems($dataPost['imports'], $dataPost['items']);
                if (!empty($arrEmptyImport)) {
                    $arrImportCode = [];
                    foreach ($arrEmptyImport as $importId) {
                        $arrImportCode[] = $this->getImportCodeById($importId);
                    }
                    $message = 'Phiếu ' . implode(', ', $arrImportCode) . ' không có mặt hàng nào được chọn';
                    $alert_type = 'danger';
                    $isSuccess    = false;
                    echo json_encode(array(
                        'result' => $isSuccess,
                        'alert_type' => $alert_type,
                        'message' => $message
                    ));
                    die;
                }
            }
            $dataInsert['tax_id'] = $dataPost['tax_id'];
            $dataInsert['tax_rate'] = $this->site_model->rowTax($dataPost['tax_id'])['taxrate'];
            $dataInsert['type_create'] = $type_create;
            $dataInsert['date_invoice'] = to_sql_date($dataPost['date']);
            $dataInsert['date_misa'] = !empty($dataPost['date_misa']) ? to_sql_date($dataPost['date_misa']) : null;
            $dataInsert['id_import'] = (!empty($dataPost['imports']) ? implode(',', $dataPost['imports']) : '');
            $dataInsert['id_suggestion'] =  (!empty($dataPost['suggestion']) ? implode(',', $dataPost['suggestion']) : '');
            $dataInsert['code_invoice'] = $dataPost['reference_bill'];
            $dataInsert['id_supplier'] = $dataPost['suppliers'];
            $dataInsert['note'] = $dataPost['note'];
            $dataInsert['date_create'] = date('Y-m-d H:i:s');
            $dataInsert['staff_create'] = get_staff_user_id();


            if ($this->db->insert('tblpurchase_invoice', $dataInsert)) {
                $id_invoice = $this->db->insert_id();

                $dataInsertPiItems = array();
                $dataInsertPiItems['purchase_invoice_id'] = $id_invoice;
                $totalBeforeTax = 0;
                $totalTax = 0;
                $totalAfterTax = 0;
                $arrPurchaseOrderItems = [];
                if ($type_create == 0) {
                    foreach ($dataPost['items'] as $id_import_item) {
                        $dataInsertPiItems['id_import_item'] = $id_import_item;
                        $isSuccess = $this->db->insert('tblpurchase_invoice_items', $dataInsertPiItems);
                        if ($isSuccess) {
                            $item = get_table_where('tblimport_items', ['id' => $id_import_item], '', 'row', '', 'quantity_payment, price, promotion_suppliers, id_import, id_purchase_order_items');

                            $dataUpdateImportItems = array();
                            $dataUpdateImportItems['tax_id'] = $dataPost['tax_id'];
                            $dataUpdateImportItems['tax_rate'] = $this->site_model->rowTax($dataPost['tax_id'])['taxrate'];
                            $amountBeforeTax = $item->quantity_payment * $item->price - $item->promotion_suppliers;
                            $tax = $amountBeforeTax * ($dataUpdateImportItems['tax_rate'] / 100);
                            $amountAfterTax = $amountBeforeTax + $tax;
                            $dataUpdateImportItems['amount'] = $amountAfterTax;
                            $this->db->where('id', $id_import_item);
                            $isSuccess2 = $this->db->update('tblimport_items', $dataUpdateImportItems);

                            if ($isSuccess2) {
                                $this->import_model->refreshTotalMoney($item->id_import); // Cập nhật lại Tổng tiền của Nhập hàng

                                $totalBeforeTax += $amountBeforeTax;
                                $totalTax += $tax;
                                $totalAfterTax += $amountAfterTax;
                            }

                            $this->import_model->refreshRed_invoice($item->id_import); // Cập nhật lại cột Đã xuất hóa đơn hết chưa
                            $this->import_model->refreshInvoice_id($item->id_import); // Cập nhật lại cột Id Hóa đơn

                            //dt
                            $this->db->select('count(tblimport_items.id) as total');
                            $this->db->from('tblimport_items');
                            $this->db->where('tblimport_items.id_purchase_order_items', $item->id_purchase_order_items);
                            $dtCountImport = $this->db->get()->row_array()['total'];
                            if ($dtCountImport == 1) {
                                $this->db->select('
                                tblpurchase_order.amount_to_vnd as amount_to_vnd,
                                tblpurchase_order_items.quantity_payment as quantity_payment,
                                tblpurchase_order_items.price_suppliers as price_suppliers,
                                tblpurchase_order_items.price_expected as price_expected,
                                tblpurchase_order_items.promotion_expected as promotion_expected,
                                tblpurchase_order_items.id_purchase_order as id_purchase_order,
                                tblpurchase_order_items.id as id,
                                tblpurchase_order_items.tax_id as tax_id,
                            ');
                                $this->db->from('tblpurchase_order_items');
                                $this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblpurchase_order_items.id_purchase_order');
                                $this->db->where('tblpurchase_order_items.id', $item->id_purchase_order_items);
                                $dtPurchaseOrderItems = $this->db->get()->row_array();
                                if (!empty($dtPurchaseOrderItems)) {
                                    if ($dtPurchaseOrderItems['tax_id'] == 0) {
                                        $tax_rate = $this->site_model->rowTax($dataPost['tax_id'])['taxrate'];
                                        $amount_to_vnd = $dtPurchaseOrderItems['amount_to_vnd'];
                                        $total_expected = $dtPurchaseOrderItems['quantity_payment'] * $dtPurchaseOrderItems['price_expected'] * (1 + ($tax_rate / 100));
                                        $total_suppliers = (($dtPurchaseOrderItems['quantity_payment'] * $dtPurchaseOrderItems['price_suppliers'] * (1 + ($tax_rate / 100))) - $dtPurchaseOrderItems['promotion_expected']);
                                        $arrPurchaseOrderItems[$dtPurchaseOrderItems['id_purchase_order']][] = [
                                            'id' => $dtPurchaseOrderItems['id'],
                                            'id_purchase_order' => $dtPurchaseOrderItems['id_purchase_order'],
                                            'amount_to_vnd' => $amount_to_vnd,
                                            'tax_id' => $dataPost['tax_id'],
                                            'tax_rate' => $tax_rate,
                                            'total_expected' => $total_expected,
                                            'total_suppliers' => $total_suppliers,
                                        ];
                                    }
                                }
                            }
                            //end

                        }
                        // var_dump($item);
                        // var_dump($dataUpdateImportItems);
                    }
                } else {
                    foreach ($dataPost['suggestion'] as $suggestion) {
                        $ins = [];
                        $ins['id_import_item'] = $suggestion;
                        $ins['purchase_invoice_id'] = $id_invoice;
                        $isSuccess = $this->db->insert('tblpurchase_invoice_items', $ins);
                        $item = get_table_where('tblsuggestion', ['id' => $suggestion], '', 'row', '', 'price_total');
                        $dataUpdateImportItems = array();
                        $tax_id = $dataPost['tax_id'];
                        $tax_rate = $this->site_model->rowTax($dataPost['tax_id'])['taxrate'];
                        $amountBeforeTax = $item->price_total;
                        $tax = $amountBeforeTax * ($tax_rate / 100);
                        $amountAfterTax = $amountBeforeTax + $tax;
                        $dataUpdateImportItems['amount'] = $amountAfterTax;
                        $totalBeforeTax += $amountBeforeTax;
                        $totalTax += $tax;
                        $totalAfterTax += $amountAfterTax;

                        $this->db->where('id', $suggestion);
                        $isSuccess2 = $this->db->update('tblsuggestion', ['red_invoice' => $id_invoice]);
                    }
                }
                $this->db->where('id', $id_invoice);
                $this->db->update('tblpurchase_invoice', array('total_price_befor_vat' => $totalBeforeTax, 'total_price_vat' => $totalTax, 'total_price_affter_vat' => $totalAfterTax));

                // $import = get_table_where('tblpurchase_order', array('id' => $data['id_import']), '', 'row');
                // $data['total_price_befor_vat'] = $import->totalAll_suppliers;
                // $data['total_price_vat'] = $import->totalAll_suppliers + $import->promotion_expected - $import->total_novat;
                // $data['total_price_affter_vat'] = $import->total_novat;
                // $data['promotion_expected'] = $import->promotion_expected;
                // $data['price_other_expenses'] = $import->price_other_expenses;

                // $this->db->update('tblpurchase_order', array('red_invoice' => $id_invoice), array('id' => $data['id_import']));

                //update po
                if (!empty($arrPurchaseOrderItems)) {
                    foreach ($arrPurchaseOrderItems as $kk => $vv) {
                        $id_purchase_order = $kk;
                        $amount_to_vnd = 1;
                        $total_expected_all = 0;
                        $total_suppliers_all = 0;
                        if (!empty($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $this->db->where('id', $vvv['id']);
                                $this->db->update('tblpurchase_order_items', [
                                    'tax_id' => $vvv['tax_id'],
                                    'tax_rate' => $vvv['tax_rate'],
                                    'total_expected' => $vvv['total_expected'],
                                    'total_suppliers' => $vvv['total_suppliers'],
                                ]);
                            }
                        }

                        $dtPurchaseOrderItemNew = get_table_where('tblpurchase_order_items', ['id_purchase_order' => $id_purchase_order]);
                        if (!empty($dtPurchaseOrderItemNew)) {
                            foreach ($dtPurchaseOrderItemNew as $kk => $vv) {
                                $total_expected_all += $vv['total_expected'];
                                $total_suppliers_all += $vv['total_suppliers'];
                            }
                        }

                        $dtPurchaseOrder = get_table_where('tblpurchase_order', ['id' => $id_purchase_order], '', 'row_array');
                        $amount_to_vnd = $dtPurchaseOrder['amount_to_vnd'];

                        $discount_percent_suppliers = $dtPurchaseOrder['discount_percent_suppliers'];
                        $valtype_check_suppliers = $dtPurchaseOrder['valtype_check_suppliers'];
                        $sub_expected = 0;
                        $delivery_cost = $dtPurchaseOrder['delivery_cost'];
                        $reduce_cost = $dtPurchaseOrder['reduce_cost'];

                        $price_expected = $total_expected_all - $sub_expected;

                        if ($valtype_check_suppliers == 1) {
                            $sub_suppliers = $total_suppliers_all * $discount_percent_suppliers / 100;
                        } else if ($valtype_check_suppliers == 2) {
                            $sub_suppliers = $discount_percent_suppliers;
                        }
                        $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
                        $total_dqd = $price_suppliers * $amount_to_vnd;
                        $_items =  array(
                            'totalAll_expected' => $total_expected_all,
                            'totalAll_suppliers' => $price_suppliers,
                            'price_expected' => $price_expected,
                            'price_suppliers' => $price_suppliers,
                            'total_cqd' => $price_suppliers,
                            'total_dqd' => $price_suppliers * $amount_to_vnd,
                        );
                        $this->db->update('tblpurchase_order', $_items, array('id' => $id_purchase_order));
                    }
                }
                //end
            }

            $alert_type = 'success';
            $message    = _l('ch_added_successfuly');
            $isSuccess    = true;
            echo json_encode(array(
                'result' => $isSuccess,
                'alert_type' => $alert_type,
                'message' => $message
            ));
            die;
        }
    }

    public function getImportCodeById($id)
    {
        $result = get_table_where('tblimport', ['id' => $id], '', 'row', '', 'CONCAT(prefix, "-", code) as reference');
        if (empty($result)) {
            return '';
        } else {
            return $result->reference;
        }
    }

    public function isInvoice($import_item_id)
    {
        $this->db->from('tblpurchase_invoice_items');
        $this->db->where('tblpurchase_invoice_items.id_import_item', $import_item_id);
        $this->db->limit(1);
        $reslut = $this->db->get()->num_rows();
        if (empty($reslut)) {
            return false;
        } else {
            return true;
        }
    }

    public function checkInvoicedItems($arrImportId, $arrItemsId)
    {
        $arrEmptyImport = $arrImportId;
        foreach ($arrImportId as $key => $importId) {
            $items = get_table_where('tblimport_items', array('id_import' => $importId));
            foreach ($items as $item) {
                if (in_array($item['id'], $arrItemsId)) {
                    unset($arrEmptyImport[$key]);
                    break;
                }
            }
        }
        return $arrEmptyImport;
    }

    public function checkInvoicedImport($import_id)
    {
        $this->db->select('tblimport_items.id');
        $this->db->from('tblimport_items');
        $this->db->where('tblimport_items.id_import', $import_id);
        $this->db->where('tblimport_items.id NOT IN (SELECT id_import_item FROM tblpurchase_invoice_items)');
        $this->db->limit(1);
        // $reslut = $this->db->get()->result_array();
        $reslut = $this->db->get()->num_rows();
        // var_dump($this->db->last_query());
        if (empty($reslut)) {
            return true;
        } else {
            return false;
        }
        // return false;
    }

    public function test($id)
    {
        var_dump($this->import_model->isInvoiced($id));
    }
    // yct end

    public function synthetic_invoice()
    {
        if (!has_permission('purchase_invoice', '', 'view') && !has_permission('purchase_invoice', '', 'view_own')) {
            access_denied('purchase_invoice');
        }
        $this->db->select('tblsuppliers.*');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_invoice.id_supplier');
        $this->db->group_by('tblsuppliers.id');
        $data['suppliers'] = $this->db->get('tblpurchase_invoice')->result_array();

        $data['title'] = _l('Chi tiết hóa đơn');
        $this->load->view('admin/purchase_invoice/synthetic_invoice', $data);
    }

    public function getSyntheticInvoice()
    {
        $invoice_search = $this->input->post('invoice_search');
        $suppliers_id = $this->input->post('suppliers_id');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblpurchase_invoice.id as id',
            'tblpurchase_invoice.code_invoice as code_invoice',
            'tblpurchase_invoice.date_invoice as date_invoice',
            'CONCAT(tblpay_slip.prefix,"-",tblpay_slip.code) as code_pay_slip',
            'tblpay_slip.day_vouchers as date_pay_slip',
            'CONCAT(tblimport.prefix,"-",tblimport.code) as code_import',
            'CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code_purchase_order',
            'tblimport.date as date_import',
            'tblpurchase_order.date as date_purchase_order',
            'CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase',
            'tblpurchases.name_purchase as name_purchase',
            'tblpurchases.date as date',
            'tblpurchases.delivery_date as delivery_date',
            'tblsuppliers.company as company',
            'supplier_purchase.code as code_supplier',
            'supplier_purchase.company as company_import',
            'supplier_purchase.time_payment as time_payment',
            'supplier_purchase.tm_ck as tm_ck',
            'tblimport.status_qc as status_qc',
            'tblimport.warehouseman_id as warehouseman_id',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblpurchase_invoice';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblpurchase_invoice_items ON tblpurchase_invoice_items.purchase_invoice_id = tblpurchase_invoice.id',
            'INNER JOIN tblimport_items ON tblimport_items.id = tblpurchase_invoice_items.id_import_item',
            'INNER JOIN tblimport ON tblimport.id = tblimport_items.id_import',
            'INNER JOIN tblpurchase_order ON tblpurchase_order.id = tblimport.id_order',
            'INNER JOIN tblpurchase_order_items ON tblpurchase_order_items.id = tblimport_items.id_purchase_order_items',
            'INNER JOIN tblsuppliers supplier_purchase ON supplier_purchase.id = tblpurchase_order.suppliers_id',
            'LEFT JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id = tblpurchase_order_items.id_internal_proposal_purchase_items',
            'LEFT JOIN tblpurchases ON tblpurchases.id = tbl_internal_proposal_purchase_items.id_purchases',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
            'LEFT JOIN tblsuggestion ON tblsuggestion.id_internal_proposal = tbl_internal_proposal_purchase_items.id_internal_proposal',
            'LEFT JOIN tblpay_slip_detail ON tblpay_slip_detail.id_old = tblpurchase_order.id',
            'LEFT JOIN tblpay_slip ON tblpay_slip.id = tblpay_slip_detail.id_pay_slip',
        ];


        if (!empty($invoice_search)) {
            array_push($where, "AND tblpurchase_invoice.id = '" . $invoice_search . "'");
        }

        if (!empty($suppliers_id)) {
            array_push($where, "AND tblpurchase_invoice.id_supplier = '" . $suppliers_id . "'");
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tblpurchase_invoice.date_invoice >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tblpurchase_invoice.date_invoice <= '" . $end_date_search . "'");
        }

        array_push($where, "AND tblpurchase_invoice.type_create = 0");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblimport_items.product_id as id_items, 
             tblimport_items.type as type, 
             tblimport_items.quantity_net as quantity,
             tblimport_items.quantity_payment as quantity_payment,
             tblimport_items.price as price,
             tblimport_items.tax_rate as tax_rate,
             tblsuggestion.code as code_suggest,
             tblsuggestion.staff_create as staff_create,
             tblsuggestion.status_dn as status_dn,
             tblsuggestion.staff_status_dn as staff_status_dn,
             tblsuggestion.status_tp as status_tp,
             tblsuggestion.staff_status_tp as staff_status_tp,
             tblsuggestion.treasurer as treasurer,
             tblpurchase_invoice.total_price_affter_vat as total_price_affter_vat
             '
        ], 'GROUP BY tblpurchase_invoice_items.id ORDER BY tblpurchase_invoice.id DESC', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $total_amount = 0;
        $total_tax = 0;
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $type_item = $aRow['type'];
            $items_id = $aRow['id_items'];
            $getItem = get_full_item_new($items_id, $type_item);

            $row = array();
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_invoice'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dhau($aRow['date_invoice']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_pay_slip'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . (!empty($aRow['date_pay_slip']) ? _dhau($aRow['date_pay_slip']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_import'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_purchase_order'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . (!empty($aRow['date_import']) ? _dhau($aRow['date_import']) : '') . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_purchase_order'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dhau($aRow['date_purchase_order']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_supplier'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['company_import'] . '</div>';
            $row[] = '<div class="text-left" style="width: 90px">' . (!empty($aRow['time_payment']) ? $aRow['time_payment'] : '') . '</div>';
            $htmlPaymentMode = '';
            if ($aRow['tm_ck'] == 1) {
                $htmlPaymentMode = 'Tiền mặt';
            } elseif ($aRow['tm_ck'] == 2) {
                $htmlPaymentMode = 'Chuyển khoản';
            }
            $row[] = '<div class="text-left" style="width: 90px">' . $htmlPaymentMode . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_purchase'] . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . $aRow['name_purchase'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . _dt($aRow['delivery_date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 160px">' . $aRow['company'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->name_category . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->name_species . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->code . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $getItem->name . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . $getItem->name_mode . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . $getItem->unit_name . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . $getItem->unit_name_stock . '</div>';
            $row[] = '<div class="text-center" style="width: 70px">' . $getItem->unit_name_payment . '</div>';
            $row[] = '<div class="text-center" style="width: 90px">' . formatNumber($aRow['quantity_payment']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['price']) . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['quantity_payment'] * $aRow['price']) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px"></div>';
            $row[] = '<div class="text-center" style="width: 80px">' . $getItem->time_stock . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . $getItem->quantity_minimum . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . (!empty($aRow['tax_rate']) ? $aRow['tax_rate'] : '') . '</div>';
            $totalTax = ($aRow['quantity_payment'] * $aRow['price']) * $aRow['tax_rate'] / 100;
            $row[] = '<div class="text-right" style="width: 100px">' . ($totalTax > 0 ? formatMoney($totalTax) : '') . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney(($totalTax + ($aRow['quantity_payment'] * $aRow['price']))) . '</div>';
            $grand_total += ($totalTax + ($aRow['quantity_payment'] * $aRow['price']));
            $htmlQc = '';
            if ($aRow['status_qc'] == 0) {
                $htmlQc = 'Chưa kiểm tra';
            } else {
                $htmlQc = 'Đã kiểm tra';
            }
            $row[] = '<div class="text-left" style="width: 100px">' . $htmlQc . '<div>';
            $htmlWarehouse = '';
            if (empty($aRow['warehouseman_id'])) {
                $htmlWarehouse = 'Chưa duyệt kho';
            } else {
                $htmlWarehouse = 'Đã duyệt kho. Thủ kho :' . get_staff_full_name($aRow['warehouseman_id']) . '';
            }
            $row[] = '<div class="text-left" style="width: 150px">' . $htmlWarehouse . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['code_suggest'] . '</div>';
            $row[] = '<div class="text-left" style="width: 150px">' . (!empty($aRow['staff_create']) ? get_staff_full_name($aRow['staff_create']) : '') . '</div>';
            $htmlStatusDx = '';
            if (!empty($aRow['status_dn'])) {
                if ($aRow['status_dn'] == 1) {
                    $htmlStatusDx = 'Đã duyệt, ' . get_staff_full_name($aRow['staff_status_dn']);
                } else {
                    $htmlStatusDx = 'Chưa duyệt';
                }
            }
            $row[] = '<div class="text-left" style="width: 150px">' . $htmlStatusDx . '</div>';
            $htmlStatusTp = '';
            if (!empty($aRow['status_tp'])) {
                if ($aRow['status_tp'] == 1) {
                    $htmlStatusTp = 'Đã duyệt, ' . get_staff_full_name($aRow['staff_status_tp']);
                } else {
                    $htmlStatusTp = 'Chưa duyệt';
                }
            }
            $row[] = '<div class="text-left" style="width: 150px">' . $htmlStatusTp . '</div>';
            $htmlStatus = '';
            if (!empty($aRow['treasurer'])) {
                if (!empty($aRow['treasurer'])) {
                    $htmlStatus = 'Đã duyệt, ' . get_staff_full_name($aRow['treasurer']);
                } else {
                    $htmlStatus = 'Chưa duyệt';
                }
            }
            $row[] = '<div class="text-left" style="width: 150px">' . $htmlStatus . '</div>';

            $total_tax += $totalTax;
            $output['aaData'][] = $row;
        }
        $output['total_quantity'] = $total_quantity;
        $output['total_amount'] = $total_amount;
        $output['total_tax'] = $total_tax;
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }
    public function getSyntheticInvoiceSuggestion()
    {
        $invoice_search = $this->input->post('invoice_search');
        $suppliers_id = $this->input->post('suppliers_id');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblpurchase_invoice.code_invoice as code_invoice',
            'tblpurchase_invoice.date_invoice as date_invoice',
            'tblsuggestion.code as code_suggestion',
            'tblsuggestion.price_total as price_total',
            'tblpurchase_invoice.tax_rate as tax_rate',
            '0 as total_price_vat',
            '0 as total_price_affter_vat'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblpurchase_invoice';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblpurchase_invoice_items ON tblpurchase_invoice_items.purchase_invoice_id = tblpurchase_invoice.id',
            'INNER JOIN tblsuggestion ON tblsuggestion.id = tblpurchase_invoice_items.id_import_item',
        ];


        if (!empty($invoice_search)) {
            array_push($where, "AND tblpurchase_invoice.id = '" . $invoice_search . "'");
        }

        if (!empty($suppliers_id)) {
            array_push($where, "AND tblpurchase_invoice.id_supplier = '" . $suppliers_id . "'");
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tblpurchase_invoice.date_invoice >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tblpurchase_invoice.date_invoice <= '" . $end_date_search . "'");
        }

        array_push($where, "AND tblpurchase_invoice.type_create = 1");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'ORDER BY tblpurchase_invoice.id DESC', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $total_amount = 0;
        $total_tax = 0;
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-left" style="">' . $aRow['code_invoice'] . '</div>';
            $row[] = '<div class="text-left" style="">' . _dhau($aRow['date_invoice']) . '</div>';
            $row[] = '<div class="text-left" style="">' . $aRow['code_suggestion'] . '</div>';
            $row[] = '<div class="text-right" style="">' . formatMoney($aRow['price_total']) . '</div>';
            $row[] = '<div class="text-center" style="">' . (empty($aRow['tax_rate']) ? 0 : $aRow['tax_rate']) . '%</div>';
            $row[] = '<div class="text-right" style="">' . formatMoney($aRow['price_total'] * (($aRow['tax_rate'] / 100))) . '</div>';
            $row[] = '<div class="text-right" style="">' . formatMoney($aRow['price_total'] * (1 + ($aRow['tax_rate'] / 100))) . '</div>';
            $total_tax += $aRow['total_price_vat'];
            $total_amount += $aRow['price_total'];
            $grand_total += $aRow['price_total'] * (1 + ($aRow['tax_rate'] / 100));
            $output['aaData'][] = $row;
        }
        $output['total_amount'] = $total_amount;
        $output['total_tax'] = $total_tax;
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }
    public function exportExcelSyntheticInvoice()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $invoice_search = $this->input->post('invoice_search');
            $suppliers_id = $this->input->post('suppliers_id');
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


            $this->db->select('
                tblpurchase_invoice.id as id,
                tblpurchase_invoice.code_invoice as code_invoice,
                tblpurchase_invoice.date_invoice as date_invoice,
                CONCAT(tblpay_slip.prefix,"-",tblpay_slip.code) as code_pay_slip,
                tblpay_slip.day_vouchers as date_pay_slip,
                CONCAT(tblimport.prefix,"-",tblimport.code) as code_import,
                CONCAT(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code_purchase_order,
                tblimport.date as date_import,
                tblpurchase_order.date as date_purchase_order,
                CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase,
                tblpurchases.name_purchase as name_purchase,
                tblpurchases.date as date,
                tblpurchases.delivery_date as delivery_date,
                tblsuppliers.company as company,
                supplier_purchase.code as code_supplier,
                supplier_purchase.company as company_import,
                supplier_purchase.time_payment as time_payment,
                supplier_purchase.tm_ck as tm_ck,
                tblimport.status_qc as status_qc,
                tblimport.warehouseman_id as warehouseman_id,
                tblimport_items.product_id as id_items, 
                tblimport_items.type as type, 
                tblimport_items.quantity_net as quantity,
                tblimport_items.quantity_payment as quantity_payment,
                tblimport_items.price as price,
                tblimport_items.tax_rate as tax_rate,
                tblsuggestion.code as code_suggest,
                tblsuggestion.staff_create as staff_create,
                tblsuggestion.status_dn as status_dn,
                tblsuggestion.staff_status_dn as staff_status_dn,
                tblsuggestion.status_tp as status_tp,
                tblsuggestion.staff_status_tp as staff_status_tp,
                tblsuggestion.treasurer as treasurer,
                tblpurchase_invoice.total_price_affter_vat as total_price_affter_vat
            ');
            $this->db->from('tblpurchase_invoice');
            $this->db->join('tblpurchase_invoice_items', 'tblpurchase_invoice_items.purchase_invoice_id = tblpurchase_invoice.id', 'inner');
            $this->db->join('tblimport_items', 'tblimport_items.id = tblpurchase_invoice_items.id_import_item', 'inner');
            $this->db->join('tblimport', 'tblimport.id = tblimport_items.id_import', 'inner');
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblimport.id_order', 'inner');
            $this->db->join(
                'tblpurchase_order_items',
                'tblpurchase_order_items.id = tblimport_items.id_purchase_order_items',
                'inner'
            );
            $this->db->join(
                'tblsuppliers supplier_purchase',
                'supplier_purchase.id = tblpurchase_order.suppliers_id',
                'inner'
            );
            $this->db->join(
                'tbl_internal_proposal_purchase_items',
                'tbl_internal_proposal_purchase_items.id = tblpurchase_order_items.id_internal_proposal_purchase_items',
                'left'
            );
            $this->db->join(
                'tblpurchases',
                'tblpurchases.id = tbl_internal_proposal_purchase_items.id_purchases',
                'left'
            );
            $this->db->join(
                'tblsuppliers',
                'tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
                'left'
            );
            $this->db->join(
                'tblsuggestion',
                'tblsuggestion.id_internal_proposal = tbl_internal_proposal_purchase_items.id_internal_proposal',
                'left'
            );
            $this->db->join(
                'tblpay_slip_detail',
                'tblpay_slip_detail.id_old = tblpurchase_order.id',
                'left'
            );
            $this->db->join(
                'tblpay_slip',
                'tblpay_slip.id = tblpay_slip_detail.id_pay_slip',
                'left'
            );
            $this->db->where("tblpurchase_invoice.type_create", 0);

            if (!empty($invoice_search)) {
                $this->db->where("tblpurchase_invoice.id = '" . $invoice_search . "'");
            }

            if (!empty($suppliers_id)) {
                $this->db->where("tblpurchase_invoice.id_supplier = '" . $suppliers_id . "'");
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("tblpurchase_invoice.date_invoice >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("tblpurchase_invoice.date_invoice <= '" . $end_date_search . "'");
            }
            $this->db->group_by('tblpurchase_invoice_items.id');
            $this->db->order_by('tblpurchase_invoice.id desc');
            $dtSyntheticInvoice = $this->db->get()->result_array();

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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name' => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('HÓA ĐƠN MUA HÀNG')
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:AN1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Số Hóa Đơn');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Ngày Lập Hóa Đơn')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Mã Phiếu Chi Mua Hàng')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Ngày Lập PCMH');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Mã Nhập Kho');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Mã Tham Chiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Ngày Nhập Kho');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'PO-Đơn Mua Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Ngày Lập PO');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Mã NCC');
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'L' . $sttRow . '',
                'Thời Hạn Thanh Toán'
            )->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'M' . $sttRow . '',
                'Phương Pháp Thanh Toán'
            )->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Mã YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Tên Mã YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Ngày Lập YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Ngày Về NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Loại Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '', 'Nhóm NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '', 'Chủng Loại NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '', 'Mã NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '', 'Tên NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $sttRow . '', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'X' . $sttRow . '',
                'Đơn Vị Chuẩn'
            )->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'Y' . $sttRow . '',
                'Đơn Vị Vào Kho'
            )->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'Z' . $sttRow . '',
                'Đơn Vị Thanh toán'
            )->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $sttRow . '', 'Số Lượng');
            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $sttRow . '', 'Giá Nhập');
            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $sttRow . '', 'Tổng Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AD' . $sttRow . '',
                'Tiêu Chuẩn Đóng Gói'
            )->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AE' . $sttRow . '',
                'Thời Gian Lưu Kho'
            )->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AF' . $sttRow . '',
                'Tồn Cho Phép'
            )->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AG' . $sttRow . '',
                '% Thuế'
            )->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AH' . $sttRow . '',
                'Tổng Tiền Thuế'
            )->getStyle("AH$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AI' . $sttRow . '',
                'Thành Tiền'
            )->getStyle("AI$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AJ' . $sttRow . '',
                'QC'
            )->getStyle("AJ$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AK' . $sttRow . '',
                'Duyệt Kho'
            )->getStyle("AK$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AL' . $sttRow . '',
                'Mã Phiếu Đề Xuất Tài Chính'
            )->getStyle("AL$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AM' . $sttRow . '',
                'Người Đề Xuất'
            )->getStyle("AM")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AN' . $sttRow . '',
                'Người Đề Xuất Duyệt'
            )->getStyle("AN$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AO' . $sttRow . '',
                'Trưởng Phòng Duyệt'
            )->getStyle("AO$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'AP' . $sttRow . '',
                'Thủ Quỹ Hoàn Thành'
            )->getStyle("AP$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AP$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
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
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:B$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFE699'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("C$sttRow:D$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00B0F0'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("E$sttRow:G$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("H$sttRow:M$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'A9D08E'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AG$sttRow:AI$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'A9D08E'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AJ$sttRow:AK$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AL$sttRow:AP$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00B0F0'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticInvoice)) {
                foreach ($dtSyntheticInvoice as $key => $value) {
                    $type_item = $value['type'];
                    $items_id = $value['id_items'];
                    $getItem = get_full_item_new($items_id, $type_item);
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rowBegin", $value['code_invoice'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $rowBegin);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dhau($value['date_invoice']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $value['code_pay_slip']);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", (!empty($value['date_pay_slip']) ? _dhau($value['date_pay_slip']) : ''));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['code_import']);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['code_purchase_order']);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", (!empty($aRow['date_import']) ? _dhau($value['date_import']) : ''));
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", ($value['code_purchase_order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", _dhau($value['date_purchase_order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", ($value['code_supplier']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "K$rowBegin",
                        ($value['company_import'])
                    )->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "L$rowBegin",
                        (!empty($value['time_payment']) ? $value['time_payment'] : '')
                    );
                    $htmlPaymentMode = '';
                    if ($value['tm_ck'] == 1) {
                        $htmlPaymentMode = 'Tiền mặt';
                    } elseif ($value['tm_ck'] == 2) {
                        $htmlPaymentMode = 'Chuyển khoản';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $htmlPaymentMode);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['code_purchase']);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "O$rowBegin",
                        $value['name_purchase']
                    )->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", _dt($value['delivery_date']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "R$rowBegin",
                        ($value['company'])
                    )->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "S$rowBegin",
                        $getItem->name_category
                    )->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "T$rowBegin",
                        $getItem->name_species
                    )->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "U$rowBegin",
                        $getItem->code
                    )->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "V$rowBegin",
                        $getItem->name
                    )->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "W$rowBegin",
                        $getItem->name_mode
                    )->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $getItem->unit_name);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", $getItem->unit_name_stock);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", $getItem->unit_name_payment);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AA$rowBegin",
                        $value['quantity_payment']
                    )->getStyle("AA$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AB$rowBegin",
                        $value['price']
                    )->getStyle("AB$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AC$rowBegin",
                        ($value['quantity_payment'] * $value['price'])
                    )->getStyle("AC$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment'] * $value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AD$rowBegin",
                        ''
                    )->getStyle("AD$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AE$rowBegin",
                        $getItem->time_stock
                    )->getStyle("AE$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AF$rowBegin",
                        $getItem->quantity_minimum
                    )->getStyle("AF$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AG$rowBegin",
                        (!empty($value['tax_rate']) ? $value['tax_rate'] : '')
                    )->getStyle("AG$rowBegin");

                    $totalTax = ($value['quantity_payment'] * $value['price']) * $value['tax_rate'] / 100;
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AH$rowBegin",
                        ($totalTax > 0 ? ($totalTax) : '')
                    )->getStyle("AH$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($totalTax));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AI$rowBegin",
                        (($totalTax + ($value['quantity_payment'] * $value['price'])))
                    )->getStyle("AI$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel(($totalTax + ($value['quantity_payment'] * $value['price']))));
                    $htmlQc = '';
                    if ($value['status_qc'] == 0) {
                        $htmlQc = 'Chưa kiểm tra';
                    } else {
                        $htmlQc = 'Đã kiểm tra';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AJ$rowBegin",
                        $htmlQc
                    )->getStyle("AJ$rowBegin")->getAlignment()->setWrapText(true);
                    $htmlWarehouse = '';
                    if (empty($value['warehouseman_id'])) {
                        $htmlWarehouse = 'Chưa duyệt kho';
                    } else {
                        $htmlWarehouse = 'Đã duyệt kho. Thủ kho :' . get_staff_full_name($value['warehouseman_id']) . '';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AK$rowBegin",
                        $htmlWarehouse
                    )->getStyle("AK$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AL$rowBegin",
                        $value['code_suggest']
                    )->getStyle("AL$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AM$rowBegin",
                        (!empty($value['staff_create']) ? get_staff_full_name($value['staff_create']) : '')
                    )->getStyle("AM$rowBegin")->getAlignment()->setWrapText(true);

                    $htmlStatusDx = '';
                    if (!empty($value['status_dn'])) {
                        if ($value['status_dn'] == 1) {
                            $htmlStatusDx = 'Đã duyệt, ' . get_staff_full_name($value['staff_status_dn']);
                        } else {
                            $htmlStatusDx = 'Chưa duyệt';
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AN$rowBegin",
                        $htmlStatusDx
                    )->getStyle("AN$rowBegin")->getAlignment()->setWrapText(true);

                    $htmlStatusTp = '';
                    if (!empty($value['status_tp'])) {
                        if ($value['status_tp'] == 1) {
                            $htmlStatusTp = 'Đã duyệt, ' . get_staff_full_name($value['staff_status_tp']);
                        } else {
                            $htmlStatusTp = 'Chưa duyệt';
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AO$rowBegin",
                        $htmlStatusTp
                    )->getStyle("AO$rowBegin")->getAlignment()->setWrapText(true);

                    $htmlStatus = '';
                    if (!empty($value['treasurer'])) {
                        if (!empty($value['treasurer'])) {
                            $htmlStatus = 'Đã duyệt, ' . get_staff_full_name($value['treasurer']);
                        } else {
                            $htmlStatus = 'Chưa duyệt';
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "AP$rowBegin",
                        $htmlStatus
                    )->getStyle("AP$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AP$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("X$rowBegin:Z$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("AA$rowBegin:AA$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("AE$rowBegin:AF$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('hoa_don_mua_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AO')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AP')->setWidth(30);
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
    public function exportExcelSyntheticInvoiceSuggestion()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $invoice_search = $this->input->post('invoice_search');
            $suppliers_id = $this->input->post('suppliers_id');
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


            $this->db->select('
                tblpurchase_invoice.id as id,
                tblpurchase_invoice.code_invoice as code_invoice,
                tblpurchase_invoice.date_invoice as date_invoice,
                tblsuggestion.code as code_suggestion,
                tblsuggestion.price_total as price_total,
                tblpurchase_invoice.tax_rate as tax_rate,
            ');
            $this->db->from('tblpurchase_invoice');
            $this->db->join('tblpurchase_invoice_items', 'tblpurchase_invoice_items.purchase_invoice_id = tblpurchase_invoice.id', 'inner');
            $this->db->join('tblsuggestion', 'tblsuggestion.id = tblpurchase_invoice_items.id_import_item', 'inner');
            $this->db->where("tblpurchase_invoice.type_create", 1);


            if (!empty($invoice_search)) {
                $this->db->where("tblpurchase_invoice.id = '" . $invoice_search . "'");
            }
            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("tblpurchase_invoice.date_invoice >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("tblpurchase_invoice.date_invoice <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tblpurchase_invoice.id desc');
            $dtSyntheticInvoice = $this->db->get()->result_array();

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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name' => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('HÓA ĐƠN MUA HÀNG')
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Số Hóa Đơn');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Ngày Lập Hóa Đơn')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Số Đế Xuất Tài Chính')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Tổng Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', '% Thuế');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Tiền Thuế');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Thành tiền');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:G$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name' => 'Times New Roman'
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
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:B$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFE699'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("C$sttRow:D$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00B0F0'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("E$sttRow:G$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticInvoice)) {
                foreach ($dtSyntheticInvoice as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rowBegin", $value['code_invoice'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $rowBegin);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dhau($value['date_invoice']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $value['code_suggestion']);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "D$rowBegin",
                        ($value['price_total'])
                    )->getStyle("D$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['price_total']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "E$rowBegin",
                        (!empty($value['tax_rate']) ? $value['tax_rate'] : '')
                    )->getStyle("E$rowBegin");
                    $totalTax = ($value['price_total']) * $value['tax_rate'] / 100;
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "F$rowBegin",
                        ($totalTax > 0 ? ($totalTax) : '')
                    )->getStyle("F$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($totalTax));
                    $totalAmount = ($value['price_total']) + $totalTax;
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "G$rowBegin",
                        ($totalAmount > 0 ? ($totalAmount) : '')
                    )->getStyle("G$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($totalAmount));
                }
            }
            $filename = lang('hoa_don_mua_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
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
    public function searchPurchaseInvoice()
    {
        $data = [];
        $search = $this->input->get('term');
        $limit = 50;
        $this->db->select(
            '
            tblpurchase_invoice.id as id,
            tblpurchase_invoice.code_invoice as text',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tblpurchase_invoice.code_invoice', $search);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $items = $this->db->get('tblpurchase_invoice')->result_array();
        if (!empty($items)) {
            $data['results'] = $items;
        }
        echo json_encode($data);
        die();
    }
}
