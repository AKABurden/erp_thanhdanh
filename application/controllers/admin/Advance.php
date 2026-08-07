<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Advance extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('collect_categories_model');
        $this->load->model('costs_model');
    }
    public function view_modal($id = '')
    {
        $data['items'] = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblother_payslips', 'id_obj' => $id), 'id DESC');
        $this->load->view('admin/advance/view_modal', $data);
    }
    public function index()
    {
        // if (!has_permission('advance', '', 'view') && !has_permission('advance', '', 'view_own')) {
        //     access_denied('advance');
        // }
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();
        $data['tnh'] = true;
        // if (!has_permission('advance', '', 'view') && !has_permission('advance', '', 'view_own')) {
        //     access_denied('advance');
        // }
        $data['title']          = _l('ch_advance');
        $this->load->view('admin/advance/manage', $data);
    }
    public function other_payslips_coupon($id = '')
    {
        $data['vouchers_id'] = array();
        $data['other_payslips'] = get_table_where('tblother_payslips', array('id' => $id), '', 'row');

        // if (!empty($id)) {
        //     $data['items'] = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
        //     if ($data['items']->objects == 1) {
        $data['vouchers_id'][0]['id'] = $data['other_payslips']->id;
        $data['vouchers_id'][0]['name'] = $data['other_payslips']->prefix . '-' . $data['other_payslips']->code;
        $data['vouchers_id'][0]['total_import'] = $data['other_payslips']->total;
        //         }
        //     }
        // }
        $data['id'] = 0;
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['code'] = get_option('prefix_other_payslips_coupon') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblother_payslips_coupon') + 1);
        $data['collect_categories'] = array();
		$this->collect_categories_model->get_by_id(0, $data['collect_categories']);
		$data['colcat_parent'] = [];
		$data['colcat_list'] = array();
		foreach ($data['collect_categories'] as $key => $value) {
			if (empty($value['costs_parent'])) {
				$data['colcat_parent'][$value['id']] = $value;
				$data['colcat_list'][$value['id']]['name'] = $value['name'];
			} else {
				$data['colcat_list'][$value['costs_parent']]['data'][] = $value;
			}
		}
        $this->load->view('admin/advance/other_payslips_coupon', $data);
    }
    public function table()
    {
        $this->app->get_table_data('advance');
    }
    public function advance($id = '')
    {
        $data['vouchers_id'] = array();
        if (!empty($id)) {
            $data['items'] = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
            if ($data['items']->id_payment > 0) {
                echo json_encode(array(
                    'alert_type' => 'danger',
                    'message' => 'Phiếu đã tất toán, Không thể xóa'
                ));
                die;
            }
            if ($data['items']->type_vouchers == 5) {
                $vouchers_id = get_table_where('tbl_orders', array('id' => $data['items']->vouchers_id));
                foreach ($vouchers_id as $key => $value) {
                    $data['vouchers_id'][$key]['id'] = $value['id'];
                    $data['vouchers_id'][$key]['name'] = $value['reference_no'];
                    $data['vouchers_id'][$key]['total_import'] = $value['cost_delivery'] - $value['price_other_expenses_delivery'] + $data['items']->total;
                }
            } elseif ($data['items']->type_vouchers == 1) {
                $vouchers_id = get_table_where('tblpurchase_order', array('id' => $data['items']->vouchers_id));
                foreach ($vouchers_id as $key => $value) {
                    $data['vouchers_id'][$key]['id'] = $value['id'];
                    $data['vouchers_id'][$key]['name'] = $value['prefix'] . '-' . $value['code'];
                    $data['vouchers_id'][$key]['total_import'] = $value['totalAll_suppliers'] - $value['amount_paid'] - $value['price_other_expenses'] + $data['items']->total;
                }
            } elseif ($data['items']->type_vouchers == 2) {
                $vouchers_id = get_table_where('tblexport_different', array('id' => $data['items']->vouchers_id));
                foreach ($vouchers_id as $key => $value) {
                    $data['vouchers_id'][$key]['id'] = $value['id'];
                    $data['vouchers_id'][$key]['name'] = $value['prefix'] . '-' . $value['code'];
                    $data['vouchers_id'][$key]['total_import'] = $value['subtotal'];
                }
            } elseif ($data['items']->type_vouchers == 8) {
                $vouchers_id = get_table_where('tblreturn_suppliers', array('id' => $data['items']->vouchers_id));
                foreach ($vouchers_id as $key => $value) {
                    $data['vouchers_id'][$key]['id'] = $value['id'];
                    $data['vouchers_id'][$key]['name'] = $value['prefix'] . '' . $value['code'];
                    $data['vouchers_id'][$key]['total_import'] = $value['total'];
                }
            } elseif ($data['items']->type_vouchers == 9) {
                $vouchers_id = get_table_where('tbl_productions_orders_details', array('id' => $data['items']->vouchers_id));
                foreach ($vouchers_id as $key => $value) {
                    $data['vouchers_id'][$key]['id'] = $value['id'];
                    $data['vouchers_id'][$key]['name'] = $value['reference_no'];
                    $data['vouchers_id'][$key]['total_import'] = 0;
                }
            }
        }
        $data['id'] = 0;
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $data['costs'] = array();
        $dtCostDefault = get_table_where('tblcosts',['code' => 'LUONG'],'','row_array');
        $this->costs_model->get_by_id_new($dtCostDefault['id'], $data['costs']);
        $data['cost_parent'] = [];
        $data['costs_list'] = $data['costs'];
        foreach ($data['costs'] as $key => $value) {
            if (empty($value['costs_parent'])) {
                $data['cost_parent'][$value['id']] = $value;
                $data['costs_list'][$value['id']]['name'] = $value['name'];
            } else {
                $data['costs_list'][$value['costs_parent']]['data'][] = $value;
            }
        }
        $data['code'] = 'TU-' . Getinfocode('code_tu');
        $this->load->view('admin/advance/advance', $data);
    }
    public function SearchClient($id = '', $type = '')
    {
        $data = [];
        $search = $this->input->get('term');
        if (empty($type)) {
            $type = $this->input->get('type');
        }
        $limit_one = 20;
        if ($type == 1) {
            $this->db->select(
                '
            tblclients.userid as id,
            CONCAT(COALESCE(tblclients.company, ""), "<br/><b>TK Ngân hàng: </b>", COALESCE(bank_account, "")) as text,
            CONCAT(tblclients.prefix_client,tblclients.code_client) as code_client',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblclients.company', $search);
                $this->db->or_like('CONCAT(tblclients.prefix_client, tblclients.code_client)', $search);
                $this->db->group_end();
            }
            if (!empty($id)) {
                $this->db->where('tblclients.userid', $id);
            }
            $this->db->order_by('tblclients.company', 'DESC');
            $this->db->limit($limit_one);
            $client = $this->db->get('tblclients')->result_array();
            $data['results'] = $client;
        } elseif ($type == 2) {
            $this->db->select(
                '
            tblsuppliers.id as id,
            CONCAT(COALESCE(tblsuppliers.company), "<br/><b>TK Ngân hàng: </b>", COALESCE(bank_account, "")) as text,
            CONCAT(tblsuppliers.prefix,tblsuppliers.code) as code_client',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('tblsuppliers.company', $search);
                $this->db->or_like('CONCAT(tblsuppliers.prefix, tblsuppliers.code)', $search);
                $this->db->group_end();
            }
            if (!empty($id)) {
                $this->db->where('tblsuppliers.id', $id);
            }
            $this->db->order_by('tblsuppliers.company', 'DESC');
            $this->db->limit($limit_one);
            $suppliers = $this->db->get('tblsuppliers')->result_array();
            $data['results'] = $suppliers;
        } elseif ($type == 3) {
            $this->db->select(
                '
            tblstaff.staffid as id,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as text',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('CONCAT(tblstaff.firstname," ",tblstaff.lastname)', $search);
                $this->db->group_end();
            }
            if (!empty($id)) {
                $this->db->where('tblstaff.staffid', $id);
            }
            $this->db->limit($limit_one);
            $suppliers = $this->db->get('tblstaff')->result_array();
            $data['results'] = $suppliers;
        }
        echo json_encode($data);
        die();
    }
    public function pay_slip()
    {
        $success = false;
        $alert_type = 'warning';
        $message = _l('ch_added_successfuly_not');
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = '';
            if (!empty($data['id_orther'])) {
                $id = $data['id_orther'];
                unset($data['id_orther']);
            }
            if (!empty($id)) {
                // if (!has_permission('other_payslips', '', 'edit')) {
                //     access_denied('other_payslips');
                // }
                $alert_type = 'warning';
                $message = _l('ch_no_updated_successfuly');
                $orther = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
                $data['note'] = $this->input->post('note', true);
                $data['date'] = to_sql_date($data['date']);
                $data['total'] = str_replace(',', '', $data['total']);
                $id_pay = $this->db->update('tblother_payslips', $data, array('id' => $id));
                if ($id_pay) {
                    $get_code = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
                    activity_log_v2('work_debt_buy', 'tblother_payslips', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                    if ($orther->objects == 2) {
                        if (!empty($orther->type_vouchers)) {
                            if ($orther->type_vouchers == 1) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where('tblpurchase_order', array('id' => $orther->vouchers_id), '', 'row');
                                    $total = $import->price_other_expenses - $orther->total + $data['total'];
                                    if (($total + $import->amount_paid) == $import->total) {
                                        $status = 2;
                                    } elseif (($total + $import->amount_paid) == 0) {
                                        $status = 0;
                                    } else {
                                        $status = 1;
                                    }
                                    $this->db->update('tblpurchase_order', array('price_other_expenses' => $total, 'status_pay' => $status), array('id' => $orther->vouchers_id));
                                }
                            } elseif ($orther->type_vouchers == 5) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where('tbl_orders', array('id' => $orther->vouchers_id), '', 'row');
                                    $total = $import->price_other_expenses_delivery - $orther->total + $data['total'];
                                    $this->db->update('tbl_orders', array('price_other_expenses_delivery' => $total), array('id' => $orther->vouchers_id));
                                }
                            }
                        }
                    }
                    if ($orther->type_vouchers == 12) {
                        if (!empty($orther->vouchers_id)) {
                            $suggestion = get_table_where('tblsuggestion', array('id' => $orther->vouchers_id), '', 'row');
                            $total = $suggestion->payments - $orther->total + $data['total'];
                            $this->db->update('tblsuggestion', array('payments' => $total), array('id' => $orther->vouchers_id));
                        }
                    }
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_updated_successfuly');
                }
            } else {
                // if (!has_permission('other_payslips', '', 'create')) {
                //     echo json_encode(array(
                //         'success' => true,
                //         'alert_type' => 'warning',
                //         'message' => 'Bạn không có quyền tạo mới phiếu chi mới'
                //     ));
                //     die;
                // }
                if ($data['type_vouchers'] == 65) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                }
                $data['note'] = $this->input->post('note', true);
                $data['code'] = Getinfocode('code_tu');
                $data['date'] = to_sql_date($data['date']);
                $data['staff_id'] = get_staff_user_id();
                $data['total'] = str_replace(',', '', $data['total']);
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['is_advance'] = 1;
                $data['prefix'] = 'TU';
                $this->db->insert('tblother_payslips', $data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    if ($data['vouchers_id'] == -1) {
                        $data['vouchers_id'] = $vouchers_id;
                    }
                    $get_code = get_table_where('tblother_payslips', array('id' => $id_pay), '', 'row');
                    activity_log_v2('work_debt_buy', 'tblother_payslips', $id_pay, $get_code->prefix . '-' . $get_code->code, 'Thêm mới phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                    if ($data['objects'] == 2) {
                        if (!empty($data['type_vouchers'])) {
                            if ($data['type_vouchers'] == 1) {
                                if (!empty($data['vouchers_id'])) {
                                    $import = get_table_where('tblpurchase_order', array('id' => $data['vouchers_id']), '', 'row');
                                    $total = $import->price_other_expenses + $data['total'];
                                    if (($total + $import->amount_paid) == $import->totalAll_suppliers) {
                                        $status = 2;
                                    } else {
                                        $status = 1;
                                    }
                                    $this->db->update('tblpurchase_order', array('price_other_expenses' => $total, 'status_pay' => $status), array('id' => $data['vouchers_id']));
                                }
                            } elseif ($data['type_vouchers'] == 65) {
                                if (!empty($data['vouchers_id'])) {
                                    //
                                    foreach ($data['vouchers_id'] as $key => $value) {
                                        $import = get_table_where('tbl_services', array('id' => $value), '', 'row');
                                        if ($key == 0) {
                                            if ($import->subtotal - $import->payment < $data['total']) {
                                                $total_add_service = $import->subtotal - $import->payment;
                                                $total_1_service = $data['total'] - $total_add_service;
                                                $total_service = $import->payment + $total_add_service;
                                            } else {
                                                $total_add_service = $data['total'];
                                                $total_1_service = 0;
                                                $total_service = $import->payment + $total_add_service;
                                            }
                                        } else {
                                            if ($import->subtotal - $import->payment < $total_1_service) {
                                                $total_add_service = $import->subtotal - $import->payment;
                                                $total_1_service = $total_1_service - $total_add_service;
                                                $total_service = $import->payment + $total_add_service;
                                            } else {
                                                $total_add_service = $total_1_service;
                                                $total_1_service = 0;
                                                $total_service = $import->payment + $total_add_service;
                                            }
                                        }
                                        if (($total_service) == $import->subtotal) {
                                            $status = 2;
                                        } elseif (($total_service) == 0) {
                                            $status = 0;
                                        } else {
                                            $status = 1;
                                        }
                                        $this->db->update('tbl_services', array('payment' => $total_service, 'status_pay' => $status), array('id' => $value));
                                        //insert detail
                                        $detail = array();
                                        $detail['other_pay'] = $id_pay;
                                        $detail['id_service'] = $value;
                                        $detail['total'] = $total_add_service;
                                        $this->db->insert('tblother_payslips_detail', $detail);
                                        //
                                    }
                                    //
                                }
                            } elseif ($data['type_vouchers'] == 5) {
                                if (!empty($data['vouchers_id'])) {
                                    $import = get_table_where('tbl_orders', array('id' => $data['vouchers_id']), '', 'row');
                                    $total = $import->price_other_expenses_delivery + $data['total'];
                                    $this->db->update('tbl_orders', array('price_other_expenses_delivery' => $total), array('id' => $data['vouchers_id']));
                                }
                            }
                        }
                    }
                    if ($data['type_vouchers'] == 12) {
                        if (!empty($data['vouchers_id'])) {
                            $import = get_table_where('tblsuggestion', array('id' => $data['vouchers_id']), '', 'row');
                            $total = $import->payments + $data['total'];
                            $this->db->update('tblsuggestion', array('payments' => $total), array('id' => $data['vouchers_id']));
                        }
                    }
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_added_successfuly');
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
}
