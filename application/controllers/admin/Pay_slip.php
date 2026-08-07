<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pay_slip extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');
        $this->load->model('purchase_order_model');
    }

    public function index()
    {
        if (!has_permission('pay_slip', '', 'view') && !has_permission('pay_slip', '', 'view_own')) {
            access_denied('pay_slip');
        }
        $data['title'] = _l('ch_pay_slip');
        $this->load->view('admin/pay_slip/manage', $data);
    }

    public function count_all()
    {
        if (has_permission('pay_slip', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select('count(*) as alls', 'tblpay_slip',
                array('tblpay_slip.staff_id' => get_staff_user_id()), '', 'row');
            $ch_invoice_tax = get_table_where_select('count(*) as ch_invoice_tax', 'tblpay_slip',
                array('type' => 1, 'tblpay_slip.staff_id' => get_staff_user_id()), '', 'row');
            $ch_retail_invoice = get_table_where_select('count(*) as ch_retail_invoice', 'tblpay_slip',
                array('type' => 2, 'tblpay_slip.staff_id' => get_staff_user_id()), '', 'row');
            $ch_gcn = get_table_where_select('count(*) as ch_gcn', 'tblpay_slip',
                array('type' => 5, 'tblpay_slip.staff_id' => get_staff_user_id()), '', 'row');

        } else {
            $count = get_table_where_select('count(*) as alls', 'tblpay_slip', array(), '', 'row');
            $ch_invoice_tax = get_table_where_select('count(*) as ch_invoice_tax', 'tblpay_slip', array('type' => 1),
                '', 'row');
            $ch_retail_invoice = get_table_where_select('count(*) as ch_retail_invoice', 'tblpay_slip',
                array('type' => 2), '', 'row');
            $ch_gcn = get_table_where_select('count(*) as ch_gcn', 'tblpay_slip', array('type' => 5), '', 'row');
        }

        $data['all'] = $count->alls;
        $data['ch_invoice_tax'] = $ch_invoice_tax->ch_invoice_tax;
        $data['ch_retail_invoice'] = $ch_retail_invoice->ch_retail_invoice;
        $data['ch_gcn'] = $ch_gcn->ch_gcn;
        echo json_encode($data);
    }

    public function modal()
    {
        $type = $this->input->get('type');
        $data['currency'] = get_table_where('tblcurrencies');
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);

        $is_admin = is_admin();
        $arrIDStaff = employee_manage_staff();
        $this->db->select('tblpayment_modes.*');
        $this->db->from('tblpayment_modes');
        $this->db->where('active', 1);
        $payment_modes = $this->db->get()->result_array();
        $data['payment_modes'] = $payment_modes;

        $this->load->view('admin/pay_slip/add_modal', $data);
    }

    public function searchSuppliers($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');

        $this->db->select('tblsuppliers.id as id, tblsuppliers.company as text,tblsuppliers_groups.name as name_category', false);
        $this->db->from('tblsuppliers');
        $this->db->join('tblsuppliers_groups','tblsuppliers_groups.id = tblsuppliers.groups_in','left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $term);
            $this->db->or_like('tblsuppliers.code', $term);
            $this->db->group_end();
        }
//        $this->db->where('tblsuppliers.active', 1);
        $this->db->limit($limit);
        $customers = $this->db->get()->result_array();
        $data['results'] = [
            [
                'text' => lang('supplier'),
                'children' => $customers
            ]
        ];
        if ($id) {
            $customer_id = $id;
            $customer = $this->clients_model->rowCustomer($customer_id);
            $data['row'] = ['id' => $customer['id'], 'text' => $customer['company']];
        }
        echo json_encode($data);
    }

    public function getData_order()
    {
        $data = $this->input->post();
        $html = '';
        if (($data['id_supplierss'] != '') && ($data['type'] != '')) {
            if ($data['type'] == 2) {
                $this->db->select('tblpurchase_order.*');
                $this->db->where('suppliers_id', $data['id_supplierss']);
                $this->db->where('status >', 1);
                $this->db->where('red_invoice', 0);
                $this->db->where('status_pay <>', 2); //loại trừ phiếu đã thu đủ
                $result = $this->db->get('tblpurchase_order')->result_array();
                foreach ($result as $key => $value) {
                    $html .= '<option data-subtext="' . formatNumber(($value['total_dqd'] - $value['amount_paid_qd'])) . '" data-total="' . ($value['total_dqd'] - $value['amount_paid_qd']) . '" data-total_cqd="' . $value['total_cqd'] . '"  value="' . $value['id'] . '">' . $value['prefix'] . '-' . $value['code'] . '</option>';
                }
            } elseif ($data['type'] == 1) {
                $this->db->select('tblpurchase_invoice.*');
                $this->db->where('id_supplier', $data['id_supplierss']);
                $this->db->where('status <>', 2); //loại trừ phiếu đã thu đủ
                $result = $this->db->get('tblpurchase_invoice')->result_array();
                foreach ($result as $key => $value) {
                    $supplier = get_table_where('tblsuppliers', ['id' => $value['id_supplier']], '', 'row_array');
                    $usd = 1;
                    if (!empty($supplier)) {
                        $curren = get_table_where('tblcurrencies', ['id' => $supplier['default_currency']], '',
                            'row_array');
                        if (!empty($curren)) {
                            $usd = $curren['amount_to_vnd'];
                        }
                    }
                    $html .= '<option data-subtext="' . number_format((($value['total_price_befor_vat'] * $usd) - $value['amount_paid_qd'])) . '" data-total="' . (($value['total_price_befor_vat'] * $usd) - $value['amount_paid_qd']) . '"   value="' . $value['id'] . '">' . $value['code_invoice'] . '</option>';
                }
            } else {

            }

        }
        echo $html;
    }

    public function add()
    {
        $success = false;
        $alert_type = 'warning';
        $message = _l('ch_pay_false');
        if (!has_permission('pay_slip', '', 'create')) {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => $alert_type,
                'message' => 'Bạn không có quyền tạo phiếu chi mua hàng!'
            ));
            die;
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $suppliert = get_table_where('tblsuppliers', array('id' => $data['id_supplierss']), '', 'row');
            $to_vnd = get_table_where('tblcurrencies', array('id' => $suppliert->default_currency), '', 'row');
            $usd = 1;
            if (!empty($to_vnd)) {
                $usd = $to_vnd->amount_to_vnd;
            }
            if ($data['type'] == 2) {
                $data['code_orders'] = implode(',', $data['code_orders']);
                $data['id_old'] = trim($data['code_orders'], ',');
                $totalss = 0;
                $id_olds = explode(',', $data['id_old']);
                foreach ($id_olds as $key => $value) {
                    $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');

                    $totalss += ($import->total_dqd) - (($import->amount_paid_qd));
                }
                // if ($totalss < str_replace(',', '', $data['payment'])) {
                //     echo json_encode(array(
                //         'success' => true,
                //         'alert_type' => 'warning',
                //         'message' => 'Có sự thay đổi về giá trị vui lòng xem lại !'
                //     ));
                //     die;
                // }
                $_data = array();

                $_data['day_vouchers'] = to_sql_date($data['date_vouchers']);
                $_data['date'] = date('Y-m-d H:i:s');
                $_data['id_costs'] = $data['id_costs'];
                $_data['staff_id'] = get_staff_user_id();
                $_data['receiver'] = $data['receiver'];
                $_data['payment_mode'] = $data['payment_mode'];
                $_data['payment'] = str_replace(',', '', $data['payment']);
                $_data['total'] = str_replace(',', '', $data['total']);
                $_data['amount_to_vnd'] = $usd;
                $_data['note'] = $data['note'];
                $_data['id_supplierss'] = $data['id_supplierss'];
                $_data['type'] = 2;
                $_data['id_old'] = $data['id_old'];
                $_data['prefix'] = get_option('prefix_pay_slip');
                $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
                $this->db->insert('tblpay_slip', $_data);
                $id_pay = $this->db->insert_id();
                $id_oldss = '';
                if ($id_pay) {
                    $id_old = explode(',', $data['id_old']);
                    $total_payment = (int)(str_replace(',', '', $data['payment']));
                    foreach ($id_old as $key => $value) {
                        if (!empty($value)) {
                            $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
                            $payment_total = $total_payment;

                            $total_payment = $total_payment - (int)($import->total_dqd - $import->amount_paid_qd);
                            if ($total_payment >= 0) {
                                $__data['id_old'] = $value;
                                $__data['id_pay_slip'] = $id_pay;
                                $__data['type'] = 2;

                                $__data['total'] = ($import->total_dqd - $import->amount_paid_qd);
                                $__data['payment'] = $import->total_dqd - $import->amount_paid_qd;
                                if (($key + 1) == count($id_old)) {
                                    $__data['total'] = ($payment_total);
                                    $__data['payment'] = $payment_total;
                                }
                                $__data['amount_to_vnd'] = $usd;
                                $this->db->insert('tblpay_slip_detail', $__data);
                                $amount_paid = $total_payment;
                                $amount_paid_qds = ((float)($__data['payment']) + $import->amount_paid_qd);
                                $amount_paids = (($__data['payment'] / $usd) + $import->amount_paid);
                                $this->db->update('tblpurchase_order', array(
                                    'amount_paid_qd' => $amount_paid_qds,
                                    'amount_paid' => $amount_paids,
                                    'status_pay' => 2
                                ), array('id' => $import->id));
                                $id_oldss .= $value . ',';
                            } else {
                                $__data['id_old'] = $value;
                                $__data['id_pay_slip'] = $id_pay;
                                $__data['type'] = 2;

                                $__data['total'] = ($import->total_dqd - $import->amount_paid_qd);
                                $__data['payment'] = $payment_total;
                                $__data['amount_to_vnd'] = $usd;
                                $this->db->insert('tblpay_slip_detail', $__data);
                                $amount_paid = $payment_total / $usd;
                                $status_pay = 1;
                                if ((int)((($amount_paid * $usd) + $import->amount_paid_qd)) == (int)$import->total_dqd) {
                                    $status_pay = 2;
                                }
                                $amount_paid_qds = ((float)(($amount_paid * $usd) + $import->amount_paid_qd));
                                $amount_paids = ($amount_paid + $import->amount_paid);
                                $this->db->update('tblpurchase_order', array(
                                    'amount_paid_qd' => $amount_paid_qds,
                                    'amount_paid' => $amount_paids,
                                    'status_pay' => $status_pay
                                ), array('id' => $import->id));
                                $id_oldss .= $value . ',';
                                break;
                            }
                            if ($import->status = 2) {
                                $staff_id = get_staff_user_id();
                                $date = date('Y-m-d H:i:s');
                                $history_status = $import->history_status;
                                $history_status .= '|' . $staff_id . ',' . $date;
                                $___data = array(
                                    'history_status' => $history_status,
                                    'status' => (3),
                                );
                                $success = $this->purchase_order_model->update_status($value, $___data);
                            }
                        }
                    }
                    $id_oldss = trim($id_oldss, ',');
                    $this->db->update('tblpay_slip', array('id_old' => $id_oldss), array('id' => $id_pay));
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_pay_succes');

                    $get_code = get_table_where('tblpay_slip', ['id' => $id_pay], '', 'row');
                    insertActivityLog([
                        'type_parent_obj' => 'pay_slip',
                        'table_obj' => 'tblpay_slip',
                        'id_obj' => $id_pay,
                        'name_obj' => $get_code->prefix . $get_code->code,
                        'content' => lang('Thêm phiếu chi mua hàng') . ' [' . $get_code->prefix . $get_code->code . ']',
                        'actions' => 'insert'
                    ]);
                }
            } elseif ($data['type'] == 1) {
                $data['code_orders'] = implode(',', $data['code_orders']);
                $data['id_old'] = trim($data['code_orders'], ',');
                $totalss = 0;
                $id_olds = explode(',', $data['code_orders']);
                foreach ($id_olds as $key => $value) {
                    $import = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
                    $totalss += $import->total_price_befor_vat * $usd - (($import->amount_paid_qd));
                }
                if ($totalss < str_replace(',', '', $data['payment'])) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Có sự thay đổi về giá trị vui lòng xem lại !'
                    ));
                    die;
                }
                $_data = array();
                $_data['day_vouchers'] = to_sql_date($data['date_vouchers']);
                $_data['date'] = date('Y-m-d H:i:s');
                $_data['id_costs'] = $data['id_costs'];
                $_data['staff_id'] = get_staff_user_id();
                $_data['receiver'] = $data['receiver'];
                $_data['payment_mode'] = $data['payment_mode'];
                $_data['payment'] = str_replace(',', '', $data['payment']);
                $_data['total'] = str_replace(',', '', $data['total']);
                $_data['amount_to_vnd'] = $usd;
                $_data['note'] = $data['note'];
                $_data['id_supplierss'] = $data['id_supplierss'];
                $_data['type'] = 1;
                $_data['id_old'] = $data['id_old'];
                $_data['prefix'] = get_option('prefix_pay_slip');
                $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
                $this->db->insert('tblpay_slip', $_data);
                $id_pay = $this->db->insert_id();
                $id_oldss = '';
                if ($id_pay) {
                    $id_old = explode(',', $data['id_old']);
                    $total_payment = (int)(str_replace(',', '', $data['payment']));
                    foreach ($id_old as $key => $value) {
                        if (!empty($value)) {
                            $import = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
                            $payment_total = $total_payment;

                            $total_payment = $total_payment - (int)(($import->total_price_befor_vat * $usd) - $import->amount_paid_qd);
                            if ($total_payment >= 0) {
                                $__data['id_old'] = $value;
                                $__data['id_pay_slip'] = $id_pay;
                                $__data['type'] = 2;

                                $__data['total'] = (($import->total_price_befor_vat * $usd) - $import->amount_paid_qd);
                                $__data['payment'] = (($import->total_price_befor_vat * $usd) - $import->amount_paid_qd);
                                $__data['amount_to_vnd'] = $usd;
                                $this->db->insert('tblpay_slip_detail', $__data);
                                $amount_paid = $total_payment;
                                $this->db->update('tblpurchase_invoice', array(
                                    'amount_paid_qd' => ((float)(($__data['payment']) + $import->amount_paid_qd)),
                                    'amount_paid' => (($__data['payment'] / $usd) + $import->amount_paid),
                                    'status' => 2
                                ), array('id' => $import->id));
                                $id_oldss .= $value . ',';
                            } else {
                                $__data['id_old'] = $value;
                                $__data['id_pay_slip'] = $id_pay;
                                $__data['type'] = 2;

                                $__data['total'] = (($import->total_price_befor_vat * $usd) - $import->amount_paid_qd);
                                $__data['payment'] = $payment_total;
                                $__data['amount_to_vnd'] = $usd;
                                $this->db->insert('tblpay_slip_detail', $__data);
                                $amount_paid = $payment_total / $usd;
                                $status_pay = 1;
                                if ((int)((($amount_paid * $usd) + $import->amount_paid_qd)) == (int)$import->total_price_befor_vat) {
                                    $status_pay = 2;
                                }
                                $this->db->update('tblpurchase_invoice', array(
                                    'amount_paid_qd' => ((float)(($amount_paid * $usd) + $import->amount_paid_qd)),
                                    'amount_paid' => ($amount_paid + $import->amount_paid),
                                    'status' => $status_pay
                                ), array('id' => $import->id));
                                $id_oldss .= $value . ',';
                                break;
                            }
                        }
                    }
                    $id_oldss = trim($id_oldss, ',');
                    $this->db->update('tblpay_slip', array('id_old' => $id_oldss), array('id' => $id_pay));

                    $get_code = get_table_where('tblpay_slip', ['id' => $id_pay], '', 'row');
                    insertActivityLog([
                        'type_parent_obj' => 'pay_slip',
                        'table_obj' => 'tblpay_slip',
                        'id_obj' => $id_pay,
                        'name_obj' => $get_code->prefix . $get_code->code,
                        'content' => lang('Thêm phiếu chi mua hàng') . ' [' . $get_code->prefix . $get_code->code . ']',
                        'actions' => 'insert'
                    ]);
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_added_successfuly');
                }
            } else {
                $data['id_old'] = trim($data['id_old'], ',');
                $totalss = 0;
                $id_olds = explode(',', $data['id_old']);
                foreach ($id_olds as $key => $value) {
                    $import = get_table_where('tbl_outsource', array('id' => $value), '', 'row');
                    $totalss += $import->grand_total - $import->amount_paid;
                }
                if ($totalss != str_replace(',', '', $data['payment'])) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Có sự thay đổi về giá trị vui lòng xem lại !'
                    ));
                    die;
                }
                $_data = array();

                $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
                $_data['accounting_date'] = to_sql_date($data['accounting_date']);

                $_data['date'] = date('Y-m-d H:i:s');
                $_data['id_costs'] = $data['id_costs'];
                $_data['staff_id'] = get_staff_user_id();
                $_data['receiver'] = $data['receiver'];
                $_data['payment_mode'] = $data['payment_mode'];
                $_data['payment'] = str_replace(',', '', $data['payment']);
                $_data['total'] = str_replace(',', '', $data['total']);
                $_data['note'] = $data['note'];
                $_data['id_supplierss'] = $data['id_supplierss'];
                $_data['type'] = 5;
                $_data['id_old'] = $data['id_old'];
                $_data['prefix'] = get_option('prefix_pay_slip');
                $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
                $this->db->insert('tblpay_slip', $_data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    $id_old = explode(',', $data['id_old']);
                    foreach ($id_old as $key => $value) {
                        if (!empty($value)) {
                            $__data['id_old'] = $value;
                            $__data['id_pay_slip'] = $id_pay;
                            $__data['type'] = 5;
                            $import = get_table_where('tbl_outsource', array('id' => $value), '', 'row');
                            $__data['total'] = $import->grand_total - $import->amount_paid;
                            $__data['payment'] = $import->grand_total - $import->amount_paid;
                            $this->db->insert('tblpay_slip_detail', $__data);

                            $this->db->update('tbl_outsource',
                                array('amount_paid' => ($import->grand_total), 'status_pay' => 2),
                                array('id' => $import->id));
                        }
                    }
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_pay_succes');
                }
            }
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }

    public function table()
    {

        $this->app->get_table_data('pay_slip');
    }

    public function view_pay_slip($id, $type)
    {
        if ($type == 1) {
            $this->app->get_table_data('view_pay_slip_invoice', array('id' => $id));
        } else {
            $this->app->get_table_data('view_pay_slip', array('id' => $id));
        }
    }

    public function update_status()
    {
        if (!has_permission('pay_slip', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not'),
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $pay_slip = get_table_where('tblpay_slip', array('id' => $id), '', 'row');
            if ($pay_slip->status == 1) {
                die;
            }
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $staff_id . ',' . $date;
            $data = array(
                'history_status' => $history_status,
                'status' => ($status + 1),
            );
            $success = $this->db->update('tblpay_slip', $data, array('id' => $id));
        }
        if ($success) {
            $get_code = get_table_where('tblpay_slip', array('id' => $id), '', 'row');
            activity_log_v2('work_debt_buy', 'tblpay_slip', $id, $get_code->prefix . '-' . $get_code->code,
                'Cập nhật trạng thái phiếu chi mua hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
            $pay_slip_detail = get_table_where('tblpay_slip_detail', array('id_pay_slip' => $id));
            foreach ($pay_slip_detail as $key => $value) {
                if ($pay_slip->type == 2) {
                    $import = get_table_where('tblpurchase_order', array('id' => $value['id_old']), '', 'row');
                    $amount_paid_debt = $import->amount_paid_debt + $value['payment'];
                    $this->db->update('tblpurchase_order', array('amount_paid_debt' => $amount_paid_debt),
                        array('id' => $import->id));
                } else {
                    $import = get_table_where('tblpurchase_invoice', array('id' => $value['id_old']), '', 'row');
                    $amount_paid_debt = $import->amount_paid_debt + $value['payment'];
                    $this->db->update('tblpurchase_invoice', array('amount_paid_debt' => $amount_paid_debt),
                        array('id' => $import->id));
                }
            }
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('ch_successful_approval'),
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval'),
            ));
        }
        die;
    }

    public function electronic_bill($id = '')
    {
        $data['items'] = get_table_where('tblpay_slip', array('id' => $id), '', 'row');
        $invoice = explode(',', $data['items']->id_old);
        if ($data['items']->type == 1) {
            $this->db->where_in('id', $invoice);
            $data['items']->item = $this->db->get('tblpurchase_invoice')->result_array();
            foreach ($data['items']->item as $key => $value) {
                $data['items']->item[$key]['total'] = $value['total_price_befor_vat'];
                $data['items']->item[$key]['code'] = $value['code_invoice'];
            }
        } elseif ($data['items']->type == 5) {
            $this->db->where_in('id', $invoice);
            $data['items']->item = $this->db->get('tbl_outsource')->result_array();
            foreach ($data['items']->item as $key => $value) {
                $data['items']->item[$key]['total'] = $value['grand_total'];
                $data['items']->item[$key]['code'] = $value['reference_no'];
            }
        } else {
            $this->db->where_in('id', $invoice);
            $data['items']->item = $this->db->get('tblimport')->result_array();
            foreach ($data['items']->item as $key => $value) {
                $data['items']->item[$key]['code'] = $value['prefix'] . '-' . $value['code'];
            }
        }
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblpay_slip', 'id_obj' => $id),
            'id DESC');
        $this->load->view('admin/pay_slip/view_modal', $data);
    }

    public function delete($id)
    {
//        if (!is_admin()) {
//            access_denied('Delete Pay Slip');
//        }
        if (!has_permission('pay_slip', '', 'delete')) {
            access_denied('Delete Pay Slip');
        }
        $get_code = get_table_where('tblpay_slip', array('id' => $id), '', 'row');
        $supplier = get_table_where('tblsuppliers', ['id' => $get_code->id_supplierss], '', 'row_array');
        $usd = 1;
        if (!empty($supplier)) {
            $curren = get_table_where('tblcurrencies', ['id' => $supplier['default_currency']], '', 'row_array');
            if (!empty($curren)) {
                $usd = $curren['amount_to_vnd'];
            }
        }
        activity_log_v2('work_debt_buy', 'tblpay_slip', $id, $get_code->prefix . '-' . $get_code->code,
            'Xóa phiếu chi mua hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        $pay_slip = get_table_where('tblpay_slip', array('id' => $id), '', 'row');
        $response = $this->db->delete('tblpay_slip', array('id' => $id));
        if ($response) {
            if ($pay_slip->type == 1) {
                $id_old = explode(',', $pay_slip->id_old);
                if (count($id_old) == 1) {
                    foreach ($id_old as $key => $value) {
                        $invoice = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
                        $amount_paid = ($invoice->amount_paid_qd) - $pay_slip->payment;
                        if ($amount_paid == 0) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                        $this->db->update('tblpurchase_invoice', array(
                            'amount_paid_qd' => $amount_paid,
                            'amount_paid_debt' => $amount_paid,
                            'amount_paid' => ($amount_paid / $usd),
                            'status' => $status,
                        ), array('id' => $invoice->id));
                    }
                } else {
                    foreach ($id_old as $key => $value) {
                        $invoice = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
                        $pay_detail = get_table_where('tblpay_slip_detail', ['id_old' => $value, 'id_pay_slip' => $id],
                            '', 'row_array');
                        $amount_paid = $invoice->amount_paid_qd - $pay_detail['payment'];
                        if ($amount_paid <= 0) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                        $this->db->update('tblpurchase_invoice',
                            array(
                                'amount_paid_qd' => ($invoice->amount_paid_qd - $pay_detail['payment']),
                                'amount_paid_debt' => $amount_paid,
                                'amount_paid' => (($invoice->amount_paid_qd - $pay_detail['payment']) / $usd),
                                'status' => $status
                            ), array('id' => $value));
                    }
                }
            } elseif ($pay_slip->type == 2) {
                $id_old = explode(',', $pay_slip->id_old);
                if (count($id_old) == 1) {
                    foreach ($id_old as $key => $value) {
                        $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');

                        $amount_paid = $import->amount_paid_qd - $pay_slip->payment;
                        if (($amount_paid) <= 0) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                        if ($amount_paid < 0) {
                            $amount_paid = 0;
                        }
                        $this->db->update('tblpurchase_order',
                            array(
                                'amount_paid_qd' => $amount_paid,
                                'amount_paid' => ($amount_paid / $usd),
                                'status_pay' => $status
                            ), array('id' => $import->id));
                    }
                } else {
                    foreach ($id_old as $key => $value) {
                        $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
                        $pay_detail = get_table_where('tblpay_slip_detail', ['id_old' => $value, 'id_pay_slip' => $id],
                            '', 'row_array');
                        $amount_paid = $import->amount_paid_qd - $pay_detail['payment'];
                        if (($amount_paid) <= 0) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                        if ($amount_paid < 0) {
                            $amount_paid = 0;
                        }
                        $this->db->update('tblpurchase_order', array(
                            'amount_paid' => (($amount_paid) / $usd),
                            'status_pay' => $status,
                            'amount_paid_qd' => $amount_paid,
                        ), array('id' => $value));
                    }
                }
            } elseif ($pay_slip->type == 5) {
                $id_old = explode(',', $pay_slip->id_old);
                if (count($id_old) == 1) {
                    foreach ($id_old as $key => $value) {
                        $import = get_table_where('tbl_outsource', array('id' => $value), '', 'row');

                        $amount_paid = $import->amount_paid - $pay_slip->payment;
                        if (($amount_paid) <= 0) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                        $this->db->update('tbl_outsource',
                            array('amount_paid' => $amount_paid, 'status_pay' => $status), array('id' => $import->id));
                    }
                } else {
                    foreach ($id_old as $key => $value) {
                        $import = get_table_where('tbl_outsource', array('id' => $value), '', 'row');
                        $amount_paid = 0;
                        $status = 0;
                        $this->db->update('tbl_outsource',
                            array('amount_paid' => $amount_paid, 'status_pay' => $status), array('id' => $value));
                    }
                }
            }
            $this->db->delete('tblpay_slip_detail', array('id_pay_slip' => $id));
            $alert_type = 'success';
            $message = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message,
        ));
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblpay_slip', array('id' => $id), '', 'row');
        $table = '';
        $data->content = '<br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">' . _l('ch_pay_slip_IN') . '</span><br>';

        $data->content .= '<span style="text-align: center;font-style: italic;">' . _l('ch_number') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';

        $day = date('d', strtotime($dataMain->day_vouchers));
        $month = date('m', strtotime($dataMain->day_vouchers));
        $year = date('Y', strtotime($dataMain->day_vouchers));
        $date = _l('ch_day') . ' ' . $day . ' ' . _l('ch_month') . ' ' . $month . ' ' . _l('ch_year') . ' ' . $year;
        $data->content .= '<span style="text-align: center;font-style: italic;">' . $date . '</span><br><br>';
        $suppliers = get_table_where('tblsuppliers', array('id' => $dataMain->id_supplierss), '', 'row');
        $pay_modes = get_table_where('tblpayment_modes', array('id' => $dataMain->payment_mode), '', 'row');
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_units_in') . ': </span><span style="font-weight: bold;">' . $suppliers->company . '</span><br/><br>
            <span style="font-weight: bold;">' . _l('ch_note_pay_slips') . ': </span><span>' . $dataMain->note . '</span><br><br>
            <span style="font-weight: bold;">' . _l('acs_sales_payment_modes_submenu') . ': </span><span>' . $pay_modes->name . '</span><br><br>
            <span style="font-weight: bold;">' . _l('expense_add_edit_amount') . ': </span><span>' . number_format($dataMain->payment) . '</span><br><br>
            <span style="font-weight: bold;">' . _l('ch_write_in_words') . ': </span><span>' . ucfirst(convert_number_to_words($dataMain->payment)) . ' đồng</span><br>';
        $date_2 = _l('ch_day') . ' ........ ' . _l('ch_month') . ' ........ ' . _l('ch_year') . ' ........';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . $date_2 . '</span><br>';
        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('ch_ceo') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('ch_chief_accountant') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('ch_cashier') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('ch_vote_maker') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">' . _l('ch_recipient_pirce') . '</span><br>
                            <span>' . _l('ch_signature') . '</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $datas = '<br><br><br><br><br><span style="text-align: center;">__________________________________________________________________</span><br>';
        $company_logo = get_option('company_logo');
        $img = file_get_contents(base_url('uploads/company/') . $company_logo);
        $html = '<table  class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 20%;">
                        <img width="112" height="50" src="data:image/png;base64,' . base64_encode($img) . '"/>
                        </td>
                        <td style="width: 80%;">
                            <span style="font-weight: bold;font-size: 14px;">' . get_option('invoice_company_name') . '</span><br>
                            <span style="font-size: 12px;">' . lang('tnh_address') . ': ' . get_option('invoice_company_address') . '</span><br>
                            <span style="font-size: 12px;">' . lang('tnh_phone') . ': ' . get_option('invoice_company_phonenumber') . '</span><br>
                            <span style="font-size: 12px;">' . lang('Fax') . ': ' . get_option('fax_company') . '</span><br>
                            <span style="font-size: 12px;">' . _l('Email') . ': ' . get_option('email_company') . '</span><br>
                        </td>
                    </tr>
                </tbody>
            </table>';
//        $data->content .=$datas.$html.$data->content;
        $pdf = print_pdf_dt($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function synthetic_payslip()
    {
        if (!has_permission('pay_slip', '', 'view') && !has_permission('pay_slip', '', 'view_own')) {
            access_denied('pay_slip');
        }
        $data['title'] = _l('dt_pay_slip');
        $this->load->view('admin/pay_slip/synthetic_payslip', $data);
    }

    public function getSyntheticPayslip()
    {
        $pay_slip_search = $this->input->post('pay_slip_search');
        $suppliers_id = $this->input->post('suppliers_id');
        $custom_item_select = $this->input->post('custom_item_select');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblpay_slip.id as id',
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
        $sTable = 'tblpay_slip';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tblpay_slip_detail ON tblpay_slip_detail.id_pay_slip = tblpay_slip.id',
            'INNER JOIN tblpurchase_order ON tblpurchase_order.id = tblpay_slip_detail.id_old',
            'INNER JOIN tblpurchase_order_items ON tblpurchase_order_items.id_purchase_order = tblpurchase_order.id',
            'INNER JOIN tblsuppliers supplier_purchase ON supplier_purchase.id = tblpurchase_order.suppliers_id',
            'LEFT JOIN tblimport ON tblimport.id_order = tblpurchase_order.id',
            'LEFT JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id = tblpurchase_order_items.id_internal_proposal_purchase_items',
            'LEFT JOIN tblpurchases ON tblpurchases.id = tbl_internal_proposal_purchase_items.id_purchases',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
            'LEFT JOIN tblsuggestion ON tblsuggestion.id_internal_proposal = tbl_internal_proposal_purchase_items.id_internal_proposal',
        ];


        if (!empty($pay_slip_search)) {
            array_push($where, "AND tblpay_slip.id = '" . $pay_slip_search . "'");
        }

        if (!empty($suppliers_id)) {
            array_push($where, "AND tblpay_slip.id_supplierss = '" . $suppliers_id . "'");
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND tblpay_slip.day_vouchers >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND tblpay_slip.day_vouchers <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblpurchase_order_items.product_id as id_items, 
             tblpurchase_order_items.type as type, 
             tblpurchase_order_items.quantity_suppliers as quantity,
             tblpurchase_order_items.quantity_payment as quantity_payment,
             tblpurchase_order_items.price_suppliers as price,
             tblpurchase_order_items.tax_rate as tax_rate,
             tblsuggestion.code as code_suggest,
             tblsuggestion.staff_create as staff_create,
             tblsuggestion.status_dn as status_dn,
             tblsuggestion.staff_status_dn as staff_status_dn,
             tblsuggestion.status_tp as status_tp,
             tblsuggestion.staff_status_tp as staff_status_tp,
             tblsuggestion.treasurer as treasurer,
             tblpay_slip.payment as payment
             '
        ], 'ORDER BY tblpay_slip.id DESC', []);


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
            $row[] = '<div class="text-left" style="width: 110px">' . $aRow['code_pay_slip'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dhau($aRow['date_pay_slip']) . '</div>';
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
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney(($aRow['payment'])) . '</div>';
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
            $grand_total += $aRow['payment'];
            $output['aaData'][] = $row;
        }
        $output['total_quantity'] = $total_quantity;
        $output['total_amount'] = $total_amount;
        $output['total_tax'] = $total_tax;
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }

    public function exportExcelSyntheticPaySlip()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $pay_slip_search = $this->input->post('pay_slip_search');
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
                tblpay_slip.id as id,
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
                tblpurchase_order_items.product_id as id_items, 
                tblpurchase_order_items.type as type, 
                tblpurchase_order_items.quantity_suppliers as quantity,
                tblpurchase_order_items.quantity_payment as quantity_payment,
                tblpurchase_order_items.price_suppliers as price,
                tblpurchase_order_items.tax_rate as tax_rate,
                tblsuggestion.code as code_suggest,
                tblsuggestion.staff_create as staff_create,
                tblsuggestion.status_dn as status_dn,
                tblsuggestion.staff_status_dn as staff_status_dn,
                tblsuggestion.status_tp as status_tp,
                tblsuggestion.staff_status_tp as staff_status_tp,
                tblsuggestion.treasurer as treasurer,
                tblpay_slip.payment as payment
            ');
            $this->db->from('tblpay_slip');
            $this->db->join('tblpay_slip_detail', 'tblpay_slip_detail.id_pay_slip = tblpay_slip.id', 'inner');
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id = tblpay_slip_detail.id_old', 'inner');
            $this->db->join('tblpurchase_order_items',
                'tblpurchase_order_items.id_purchase_order = tblpurchase_order.id', 'inner');
            $this->db->join('tblsuppliers supplier_purchase', 'supplier_purchase.id = tblpurchase_order.suppliers_id',
                'inner');
            $this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id', 'left');
            $this->db->join('tbl_internal_proposal_purchase_items',
                'tbl_internal_proposal_purchase_items.id = tblpurchase_order_items.id_internal_proposal_purchase_items',
                'left');
            $this->db->join('tblpurchases', 'tblpurchases.id = tbl_internal_proposal_purchase_items.id_purchases',
                'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
                'left');
            $this->db->join('tblsuggestion',
                'tblsuggestion.id_internal_proposal = tbl_internal_proposal_purchase_items.id_internal_proposal',
                'left');

            if (!empty($pay_slip_search)) {
                $this->db->where("tblpay_slip.id = '" . $pay_slip_search . "'");
            }

            if (!empty($suppliers_id)) {
                $this->db->where("tblpay_slip.id_supplierss = '" . $suppliers_id . "'");
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("tblpay_slip.day_vouchers >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("tblpay_slip.day_vouchers <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tblpay_slip.id desc');
            $dtSyntheticPaySlip = $this->db->get()->result_array();


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
                    'name' => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('PHIẾU CHI MUA HÀNG'))->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Mã Phiếu Chi Mua Hàng')->getStyle("A$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Ngày Lập PCMH');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Mã Nhập Kho');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Mã Tham Chiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Ngày Nhập Kho');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'PO-Đơn Mua Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Ngày Lập PO');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Mã NCC');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '',
                'Thời Hạn Thanh Toán')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '',
                'Phương Pháp Thanh Toán')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Mã YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Tên Mã YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Ngày Lập YCMHH');
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Ngày Về NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Loại Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Nhóm NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Chủng Loại NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '', 'Mã NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '', 'Tên NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '',
                'Đơn Vị Chuẩn')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $sttRow . '',
                'Đơn Vị Vào Kho')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $sttRow . '',
                'Đơn Vị Thanh toán')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $sttRow . '', 'Số Lượng');
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $sttRow . '', 'Giá Nhập');
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $sttRow . '', 'Tổng Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $sttRow . '',
                'Tiêu Chuẩn Đóng Gói')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $sttRow . '',
                'Thời Gian Lưu Kho')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AD' . $sttRow . '',
                'Tồn Cho Phép')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AE' . $sttRow . '',
                '% Thuế')->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $sttRow . '',
                'Tổng Tiền Thuế')->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AG' . $sttRow . '',
                'Thành Tiền')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AH' . $sttRow . '',
                'QC')->getStyle("AH$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AI' . $sttRow . '',
                'Duyệt Kho')->getStyle("AI$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $sttRow . '',
                'Mã Phiếu Đề Xuất Tài Chính')->getStyle("AJ$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AK' . $sttRow . '',
                'Người Đề Xuất')->getStyle("AK$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AL' . $sttRow . '',
                'Người Đề Xuất Duyệt')->getStyle("AL$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AM' . $sttRow . '',
                'Trưởng Phòng Duyệt')->getStyle("AM$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AN' . $sttRow . '',
                'Thủ Quỹ Hoàn Thành')->getStyle("AN$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AN$sttRow")->applyFromArray([
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
                    'color' => array('rgb' => '00B0F0'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("C$sttRow:E$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("F$sttRow:K$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'A9D08E'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AE$sttRow:AG$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'A9D08E'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AH$sttRow:AI$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BDD7EE'),
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getStyle("AJ$sttRow:AN$sttRow")->applyFromArray([
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '00B0F0'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticPaySlip)) {
                foreach ($dtSyntheticPaySlip as $key => $value) {
                    $type_item = $value['type'];
                    $items_id = $value['id_items'];
                    $getItem = get_full_item_new($items_id, $type_item);
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['code_pay_slip']);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dhau($value['date_pay_slip']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $value['code_import']);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['code_purchase_order']);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", (!empty($aRow['date_import']) ? _dhau($value['date_import']) : ''));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['code_purchase_order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", _dhau($value['date_purchase_order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", ($value['code_supplier']));
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",
                        ($value['company_import']))->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",
                        (!empty($value['time_payment']) ? $value['time_payment'] : ''));
                    $htmlPaymentMode = '';
                    if ($value['tm_ck'] == 1) {
                        $htmlPaymentMode = 'Tiền mặt';
                    } elseif ($value['tm_ck'] == 2) {
                        $htmlPaymentMode = 'Chuyển khoản';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $htmlPaymentMode);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['code_purchase']);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin",
                        $value['name_purchase'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", _dt($value['delivery_date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin",
                        ($value['company']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin",
                        $getItem->name_category)->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin",
                        $getItem->name_species)->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin",
                        $getItem->code)->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin",
                        $getItem->name)->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin",
                        $getItem->name_mode)->getStyle("U$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", $getItem->unit_name);
                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", $getItem->unit_name_stock);
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", $getItem->unit_name_payment);
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin",
                        $value['quantity_payment'])->getStyle("Y$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment']));
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin",
                        $value['price'])->getStyle("Z$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin",
                        ($value['quantity_payment'] * $value['price']))->getStyle("AA$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment'] * $value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin",
                        '')->getStyle("AB$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue("AC$rowBegin",
                        $getItem->time_stock)->getStyle("AC$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue("AD$rowBegin",
                        $getItem->quantity_minimum)->getStyle("AD$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue("AE$rowBegin",
                        (!empty($value['tax_rate']) ? $value['tax_rate'] : ''))->getStyle("AE$rowBegin");

                    $totalTax = ($value['quantity_payment'] * $value['price']) * $value['tax_rate'] / 100;
                    $objPHPExcel->getActiveSheet()->setCellValue("AF$rowBegin",
                        ($totalTax > 0 ? ($totalTax) : ''))->getStyle("AF$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($totalTax));
                    $objPHPExcel->getActiveSheet()->setCellValue("AG$rowBegin",
                        ($value['payment']))->getStyle("AG$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['payment']));
                    $htmlQc = '';
                    if ($value['status_qc'] == 0) {
                        $htmlQc = 'Chưa kiểm tra';
                    } else {
                        $htmlQc = 'Đã kiểm tra';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("AH$rowBegin",
                        $htmlQc)->getStyle("AH$rowBegin")->getAlignment()->setWrapText(true);
                    $htmlWarehouse = '';
                    if (empty($value['warehouseman_id'])) {
                        $htmlWarehouse = 'Chưa duyệt kho';
                    } else {
                        $htmlWarehouse = 'Đã duyệt kho. Thủ kho :' . get_staff_full_name($value['warehouseman_id']) . '';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("AI$rowBegin",
                        $htmlWarehouse)->getStyle("AI$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("AJ$rowBegin",
                        $value['code_suggest'])->getStyle("AJ$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("AK$rowBegin",
                        (!empty($value['staff_create']) ? get_staff_full_name($value['staff_create']) : ''))->getStyle("AK$rowBegin")->getAlignment()->setWrapText(true);

                    $htmlStatusDx = '';
                    if (!empty($value['status_dn'])) {
                        if ($value['status_dn'] == 1) {
                            $htmlStatusDx = 'Đã duyệt, ' . get_staff_full_name($value['staff_status_dn']);
                        } else {
                            $htmlStatusDx = 'Chưa duyệt';
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue("AL$rowBegin",
                        $htmlStatusDx)->getStyle("AL$rowBegin")->getAlignment()->setWrapText(true);

                    $htmlStatusTp = '';
                    if (!empty($value['status_tp'])) {
                        if ($value['status_tp'] == 1) {
                            $htmlStatusTp = 'Đã duyệt, ' . get_staff_full_name($value['staff_status_tp']);
                        } else {
                            $htmlStatusTp = 'Chưa duyệt';
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue("AM$rowBegin",
                        $htmlStatusTp)->getStyle("AM$rowBegin")->getAlignment()->setWrapText(true);

                    $htmlStatus = '';
                    if (!empty($value['treasurer'])) {
                        if (!empty($value['treasurer'])) {
                            $htmlStatus = 'Đã duyệt, ' . get_staff_full_name($value['treasurer']);
                        } else {
                            $htmlStatus = 'Chưa duyệt';
                        }
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue("AN$rowBegin",
                        $htmlStatus)->getStyle("AN$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AN$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("V$rowBegin:Y$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("AE$rowBegin:AE$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("AC$rowBegin:AD$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_chi_mua_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setWidth(30);
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

    public function searchPayslips()
    {
        $data = [];
        $search = $this->input->get('term');
        $limit = 50;
        $this->db->select('
            tblpay_slip.id as id,
            CONCAT(tblpay_slip.prefix,"-",tblpay_slip.code) as text',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('CONCAT(tblpay_slip.prefix,"-",tblpay_slip.code)', $search);
            $this->db->or_like('tblpay_slip.prefix', $search);
            $this->db->or_like('tblpay_slip.code', $search);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $items = $this->db->get('tblpay_slip')->result_array();
        if (!empty($items)) {
            $data['results'] = $items;
        }
        echo json_encode($data);
        die();
    }
}