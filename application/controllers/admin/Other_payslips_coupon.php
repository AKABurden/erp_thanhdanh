<?php
defined('BASEPATH') or exit('No direct script access allowed');
class other_payslips_coupon extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');
        $this->load->model('collect_categories_model');
    }

    public function index()
    {
        if (!has_permission('other_payslips_coupon', '', 'view') && !has_permission('other_payslips_coupon', '', 'view_own')) {
            access_denied('other_payslips_coupon');
        }
        $data['title']          = _l('other_payslips_coupon');
        $this->load->view('admin/other_payslips_coupon/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('other_payslips_coupon');
    }
    public function count_all()
    {
        if (has_permission('purchases', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select('count(*) as alls', 'tblother_payslips_coupon', array('staff_id' => get_staff_user_id()), '', 'row');

            $pay_client = get_table_where_select('count(*) as pay_client', 'tblother_payslips_coupon', array('objects' => 1, 'staff_id' => get_staff_user_id()), '', 'row');
            $pay_suppliers = get_table_where_select('count(*) as pay_suppliers', 'tblother_payslips_coupon', array('objects' => 2, 'staff_id' => get_staff_user_id()), '', 'row');
            $pay_staff = get_table_where_select('count(*) as pay_staff', 'tblother_payslips_coupon', array('objects' => 3, 'staff_id' => get_staff_user_id()), '', 'row');
            $pay_other = get_table_where_select('count(*) as pay_other', 'tblother_payslips_coupon', array('objects' => 4, 'staff_id' => get_staff_user_id()), '', 'row');
        } else {
            $count = get_table_where_select('count(*) as alls', 'tblother_payslips_coupon', array(), '', 'row');
            $pay_client = get_table_where_select('count(*) as pay_client', 'tblother_payslips_coupon', array('objects' => 1), '', 'row');
            $pay_suppliers = get_table_where_select('count(*) as pay_suppliers', 'tblother_payslips_coupon', array('objects' => 2), '', 'row');
            $pay_staff = get_table_where_select('count(*) as pay_staff', 'tblother_payslips_coupon', array('objects' => 3), '', 'row');
            $pay_other = get_table_where_select('count(*) as pay_other', 'tblother_payslips_coupon', array('objects' => 4), '', 'row');
        }
        $data['all'] = $count->alls;
        $data['pay_client'] = $pay_client->pay_client;
        $data['pay_suppliers'] = $pay_suppliers->pay_suppliers;
        $data['pay_staff'] = $pay_staff->pay_staff;
        $data['pay_other'] = $pay_other->pay_other;
        echo json_encode($data);
    }
    public function pay_slip()
    {
        $success = false;
        $alert_type = 'warning';
        $message    = _l('ch_added_successfuly_not');
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = '';
            if (!empty($data['id_orther'])) {
                $id = $data['id_orther'];
                unset($data['id_orther']);
            }
            if (!empty($id)) {
                if (!has_permission('other_payslips_coupon', '', 'edit')) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Bạn không có quyền sửa'
                    ));
                    die;
                }
                $alert_type = 'warning';
                $message    = _l('ch_no_updated_successfuly');
                $orther = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
                $data['note'] = $this->input->post('note', true);
                $data['date'] = to_sql_date($data['date']);
                $data['total'] = str_replace(',', '', $data['total']);
                $id_pay = $this->db->update('tblother_payslips_coupon', $data, array('id' => $id));
                if ($id_pay) {
                    if ($orther->objects == 1) {
                        if (!empty($orther->type_vouchers)) {
                            if ($orther->type_vouchers == 5) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where('tbl_orders', array('id' => $orther->vouchers_id), '', 'row');

                                    $total = $import->price_other_expenses - $orther->total + $data['total'];

                                    if (($total + $import->total_payment) == $import->grand_total) {
                                        $status = 2;
                                    } elseif (($total + $import->total_payment) == 0) {
                                        $status = 0;
                                    } else {
                                        $status = 1;
                                    }
                                    $this->db->update('tbl_orders', array('price_other_expenses' => $total, 'status_payment' => $status), array('id' => $orther->vouchers_id));
                                }
                            }
                        }
                    }
                    $get_code = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
                    activity_log_v2('work_debt_sales', 'tblother_payslips_coupon', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật phiếu thu khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                    $success = true;
                    $alert_type = 'success';
                    $message    = _l('ch_updated_successfuly');
                }
            } else {
                if (!has_permission('other_payslips_coupon', '', 'create')) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Bạn không có quyền thêm'
                    ));
                    die;
                }
                $data['note'] = $this->input->post('note', true);
                $data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblother_payslips_coupon') + 1);
                $data['date'] = to_sql_date($data['date']);
                $data['staff_id'] = get_staff_user_id();
                $data['total'] = str_replace(',', '', $data['total']);
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['prefix'] = get_option('prefix_other_payslips_coupon');
                $this->db->insert('tblother_payslips_coupon', $data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    if ($data['objects'] == 1) {
                        if (!empty($data['type_vouchers'])) {
                            if ($data['type_vouchers'] == 5) {
                                if (!empty($data['vouchers_id'])) {
                                    $import = get_table_where('tbl_orders', array('id' => $data['vouchers_id']), '', 'row');
                                    $total = $import->price_other_expenses + $data['total'];
                                    if (($total + $import->total_payment) == $import->grand_total) {
                                        $status = 2;
                                    } else {
                                        $status = 1;
                                    }
                                    $this->db->update('tbl_orders', array('price_other_expenses' => $total, 'status_payment' => $status), array('id' => $data['vouchers_id']));
                                }
                            }
                        }
                    }
                    if ($data['objects'] == 3) {
                        if (!empty($data['type_vouchers'])) {
                            if ($data['type_vouchers'] == 12) {
                                if (!empty($data['vouchers_id'])) {
                                    $import = get_table_where('tblother_payslips', array('id' => $data['vouchers_id']), '', 'row');
                                    $total = $import->id_payment + $data['total'];
                                    $this->db->update('tblother_payslips', array('id_payment' => $total), array('id' => $data['vouchers_id']));
                                }
                            }
                        }
                    }
                    $get_code = get_table_where('tblother_payslips_coupon', array('id' => $id_pay), '', 'row');
                    activity_log_v2('work_debt_sales', 'tblother_payslips_coupon', $id_pay, $get_code->prefix . '-' . $get_code->code, 'Thêm mới phiếu thu khác [' . $get_code->prefix . '-' . $get_code->code . ']');
                    $success = true;
                    $alert_type = 'success';
                    $message    = _l('ch_added_successfuly');
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
            tblclients.company as text,
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
            tblsuppliers.company as text,
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
            CONCAT(tblstaff.lastname,tblstaff.firstname) as text',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('CONCAT(tblstaff.lastname, tblstaff.firstname)', $search);
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
    public function vouchers_id()
    {
        $data = $this->input->post();
        $_data = array();
        if (!empty($data)) {

            if ($data['objects'] == 1) {
                if ($data['type_vouchers'] == 5) {
                    if (!is_admin()) {
                        $_data = get_table_where_select('tbl_orders.*,tbl_orders.grand_total as total,(SELECT SUM(tbl_returned_goods.grand_total)
                    FROM tbl_returned_goods
                    WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.order_id = tbl_orders.id) as total_return', 'tbl_orders', array('customer_id' => $data['objects_id'], 'status' => 'approved', 'created_by' => get_staff_user_id(), 'status_payment !=' => 2));
                    } else {
                        $_data = get_table_where_select('tbl_orders.*,tbl_orders.grand_total as total,(SELECT SUM(tbl_returned_goods.grand_total)
                        FROM tbl_returned_goods
                        WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.order_id = tbl_orders.id) as total_return', 'tbl_orders', array('customer_id' => $data['objects_id'], 'status' => 'approved', 'status_payment !=' => 2));
                    }
                } elseif ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0));
                    } else {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0, 'staff_id' => get_staff_user_id()));
                    }
                }
            }
            if ($data['objects'] == 2) {

                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0));
                    } else {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0, 'staff_id' => get_staff_user_id()));
                    }
                }
            }
            if ($data['objects'] == 3) {

                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0));
                    } else {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0, 'staff_id' => get_staff_user_id()));
                    }
                }
            }
            if ($data['objects'] == 4) {

                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'status >' => 0));
                    } else {
                        $_data = get_table_where_select('tblexport_different.*,tblexport_different.subtotal as total', 'tblexport_different', array('object' => $data['objects'], 'status >' => 0, 'staff_id' => get_staff_user_id()));
                    }
                }
            }
        }
        echo json_encode($_data);
        die();
    }
    public function other_payslips_coupon($id = '')
    {
        $data['vouchers_id'] = array();
        if (!empty($id)) {
            $data['items'] = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
            if ($data['items']->objects == 1) {
                $vouchers_id = get_table_where('tbl_orders', array('id' => $data['items']->vouchers_id));
                foreach ($vouchers_id as $key => $value) {
                    $data['vouchers_id'][$key]['id'] = $value['id'];
                    $data['vouchers_id'][$key]['name'] = $value['reference_no'];
                    $data['vouchers_id'][$key]['total_import'] = $value['grand_total'] - $value['total_payment'] - $value['price_other_expenses'] + $data['items']->total;
                }
            }
        }
        $data['id'] = 0;
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));

        // yct start
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
        // var_dump($data['colcat_list']);die;
        // yct end
        
        // $this->costs_model->get_by_id(0, $data['costs']);
        $data['code'] = get_option('prefix_other_payslips_coupon') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblother_payslips_coupon') + 1);
        $this->load->view('admin/other_payslips_coupon/other_payslips_coupon', $data);
    }
    public function LoadListObjectByID($id)
    {
        $list = '';
        if ($id == 1) {
            $data = $this->clients_model->get();
            foreach ($data as $key => $value) {
                $list .= '<option value="' . $value['userid'] . '">' . $value['company'] . '</option>';
            }
        } else if ($id == 2) {
            $data = $this->staff_model->get_all_staff();
            foreach ($data as $key => $value) {
                $list .= '<option value="' . $value['staffid'] . '">' . $value['fullname'] . '</option>';
            }
        } else if ($id == 3) {
            $data = $this->suppliers_model->get();
            foreach ($data as $key => $value) {
                $list .= '<option value="' . $value['userid'] . '">' . $value['company'] . '</option>';
            }
        } else {
            $list = '';
        }
        print_r($list);
    }
    public function delete($id = '')
    {
        if (!has_permission('other_payslips_coupon', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'alert_type' => 'danger',
                'message' => _l('ch_delete_not')
            ));
            die;
        }
        $payslips = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
        $response = $this->db->delete('tblother_payslips_coupon', array('id' => $id));
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {
            $alert_type = 'success';
            $message    = _l('ch_delete');

            if (!empty($payslips)) {
                if ($payslips->objects == 2) {
                    if ($payslips->type_vouchers == 1) {
                        if (!empty($payslips->vouchers_id)) {
                            $import = get_table_where('tblpurchase_order', array('id' => $payslips->vouchers_id), '', 'row');
                            $total = $import->price_other_expenses - $payslips->total;
                            if (($total + $import->amount_paid) == 0) {
                                $status = 0;
                            } else {
                                $status = 1;
                            }

                            $this->db->update('tblpurchase_order', array('price_other_expenses' => $total, 'status_pay' => $status), array('id' => $payslips->vouchers_id));
                        }
                    }
                }
                if ($payslips->objects == 1) {
                    if ($payslips->type_vouchers == 5) {
                        if (!empty($payslips->vouchers_id)) {
                            $this->db->select('tbl_orders.*,(SELECT SUM(tbl_returned_goods.grand_total)
                        FROM tbl_returned_goods
                        WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.order_id = tbl_orders.id) as total_return');
                            $this->db->where('id', $payslips->vouchers_id);
                            $import = $this->db->get('tbl_orders')->row();
                            $total = $import->price_other_expenses - $payslips->total;
                            $status_payment = 1;
                            if (($import->total_payment + $total) >= ($import->grand_total - $import->total_return)) {
                                $status_payment = 2;
                            }
                            $this->db->update('tbl_orders', array('status_payment' => $status_payment, 'price_other_expenses' => $total), array('id' => $payslips->vouchers_id));
                        }
                    }
                }
                if ($payslips->objects == 3) {
                    if ($payslips->type_vouchers == 12) {
                        if (!empty($payslips->vouchers_id)) {
                            $import = get_table_where('tblother_payslips', array('id' => $payslips->vouchers_id), '', 'row');
                            $total = $import->id_payment - $payslips->total;

                            $this->db->update('tblother_payslips', array('id_payment' => $total), array('id' => $payslips->vouchers_id));
                        }
                    }
                }
            }

            insertActivityLog([
                'type_parent_obj' => 'other_payslips_coupon',
                'table_obj' => 'tblother_payslips_coupon',
                'id_obj' => $id,
                'name_obj' => $payslips->prefix . '-' . $payslips->code,
                'content' => lang('Xóa phiếu thu khác') . ' [' . $payslips->prefix . '-' . $payslips->code . ']',
                'actions' => 'delete'
            ]);
        }
        if (!empty($payslips->files)) {
            if (file_exists('uploads/payments/' . $payslips->files)) {
                unlink('uploads/payments/' . $payslips->files);
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    // public function delete($id = '')
    // {
    //     if (!has_permission('other_payslips_coupon', '', 'delete')) {
    //         echo json_encode(array(
    //             'alert_type' => 'warning',
    //             'message' => _l('ch_delete_not')
    //         ));
    //         die;
    //     }
    //     $payslips = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
    //     $response = $this->db->delete('tblother_payslips_coupon', array('id' => $id));
    //     $alert_type = 'warning';
    //     $message    = _l('ch_no_delete');
    //     if ($response) {
    //         $alert_type = 'success';
    //         $message    = _l('ch_delete');

    //         if (!empty($payslips)) {
    //             if ($payslips->objects == 2) {
    //                 if ($payslips->type_vouchers == 1) {
    //                     if (!empty($payslips->vouchers_id)) {
    //                         $import = get_table_where('tblpurchase_order', array('id' => $payslips->vouchers_id), '', 'row');
    //                         $total = $import->price_other_expenses - $payslips->total;
    //                         if (($total + $import->amount_paid) == 0) {
    //                             $status = 0;
    //                         } else {
    //                             $status = 1;
    //                         }
    //                         $this->db->update('tblpurchase_order', array('price_other_expenses' => $total, 'status_pay' => $status), array('id' => $payslips->vouchers_id));
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     echo json_encode(array(
    //         'alert_type' => $alert_type,
    //         'message' => $message
    //     ));
    // }
    public function update_status()
    {
        if (!has_permission('other_payslips_coupon', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $other_payslips_coupon = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
            if ($other_payslips_coupon->status == 1) {
                die;
            }
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $staff_id . ',' . $date;
            $data = array(
                'history_status' => $history_status,
                'status' => ($status + 1),
            );
            $success = $this->db->update('tblother_payslips_coupon', $data, array('id' => $id));
        }
        if ($success) {
            $get_code = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
            activity_log_v2('work_debt_sales', 'tblother_payslips_coupon', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật trạng thái phiếu chuyển kho [' . $get_code->prefix . '-' . $get_code->code . ']');
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
    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataMain = get_table_where('tblother_payslips_coupon', array('id' => $id), '', 'row');
        $table = '';
        $img = file_get_contents(base_url('uploads/company/') . get_option('company_logo'));
        $data->img = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">' . _l('PHIẾU THU KHÁC') . '</span><br>';

        $data->content .= '<span style="text-align: center;font-style: italic;">' . _l('ch_number') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';

        $day = date('d', strtotime($dataMain->date));
        $month = date('m', strtotime($dataMain->date));
        $year = date('Y', strtotime($dataMain->date));
        $date = _l('ch_day') . ' ' . $day . ' ' . _l('ch_month') . ' ' . $month . ' ' . _l('ch_year') . ' ' . $year;
        $data->content .= '<span style="text-align: center;font-style: italic;">' . $date . '</span><br><br>';
        $pay_modes = get_table_where('tblpayment_modes', array('id' => $dataMain->payment_modes), '', 'row');
        if ($dataMain->objects == 2) {
            $supplier = get_table_where('tblsuppliers', array('id' => $dataMain->objects_id), '', 'row');
            $data->content .= '
                <span style="font-weight: bold;">' . _l('ch_units_in') . ': </span><span style="font-weight: bold;">' . $supplier->company . '</span><br/><br>';
        }
        if ($dataMain->objects == 1) {
            $client = get_table_where('tblclients', array('userid' => $dataMain->objects_id), '', 'row');
            $data->content .= '
                <span style="font-weight: bold;">' . _l('clients') . ': </span><span style="font-weight: bold;">' . !empty($client) ? !empty(!empty($client->company) ? $client->company : '') : '' . '</span><br/><br>';
        }
        if ($dataMain->objects == 3) {
            $_data = get_staff_full_name($dataMain->objects_id);
            $data->content .= '
                <span style="font-weight: bold;">' . _l('ch_units_in') . ': </span><span style="font-weight: bold;">' . $_data . '</span><br/><br>';
        }
        if ($dataMain->objects == 4) {
            $data->content .= '
                <span style="font-weight: bold;">' . _l('ch_units_in') . ': </span><span style="font-weight: bold;">' . $dataMain->objects_text . '</span><br/><br>';
        }


        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_note_pay_slips') . ': </span><span>' . $dataMain->note . '</span><br><br>
            <span style="font-weight: bold;">' . _l('acs_sales_payment_modes_submenu') . ': </span><span>' . $pay_modes->name . '</span><br><br>
            <span style="font-weight: bold;">' . _l('expense_add_edit_amount') . ': </span><span>' . number_format($dataMain->total) . '</span><br><br>
            <span style="font-weight: bold;">' . _l('ch_write_in_words') . ': </span><span>' . ucfirst(convert_number_to_words($dataMain->total)) . ' đồng</span><br>';
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
        $datas = '<br><br><br><br><br><span style="text-align: center;">__________________________________________________________________</span><br>';
        //        $data->content .= $datas . $html . $data->content;
        $pdf      = print_pdf_dt($data);
        $type     = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function table_single_client($clientid = '')
    {
        $this->app->get_table_data('other_payslips_coupon_single_client', [
            'clientid' => $clientid,
        ]);
    }
}
