<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Other_payslips extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('costs_model');
        $this->load->model('import_model');
        $this->type ="import";
        if (!empty($_GET['type'])){
            $this->type = ($_GET['type']);
        }
    }

    public function view_modal($id = '')
    {
        $data['items'] = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        $data['dataLog'] = get_table_where(
            'tblactivity_log_v2',
            array('table_obj' => 'tblother_payslips', 'id_obj' => $id),
            'id DESC'
        );
        $this->load->view('admin/other_payslips/view_modal', $data);
    }

    public function index()
    {
        $data['tnh'] = true;
        if (!has_permission('other_payslips', '', 'view') && !has_permission('other_payslips', '', 'view_own')) {
            access_denied('other_payslips');
        }
        if ($this->type == 'import') {
            $data['title'] = _l('ch_other_payslips');
        } else {
            $data['title'] = _l('dt_other_payslips1');
        }
        $data['type'] = $this->type;
        $this->load->view('admin/other_payslips/manage', $data);
    }

    public function table()
    {
        $this->app->get_table_data('other_payslips');
    }

    public function count_all()
    {
        $type = !empty($this->input->post('type')) ? $this->input->post('type') : 'import';
        $where = '1';
        if ($type == 'import'){
            $where = '= 1';
        } else {
            $where = '!= 1';
        }
        if (has_permission('other_payslips', '', 'view_own') && !is_admin()) {

            $this->db->select('count(*) as alls');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("is_advance",0);
            $this->db->where("staff_id",get_staff_user_id());
            $count = $this->db->get()->row();

            $this->db->select('count(*) as pay_client');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",1);
            $this->db->where("is_advance",0);
            $this->db->where("staff_id",get_staff_user_id());
            $pay_client = $this->db->get()->row();

            $this->db->select('count(*) as pay_suppliers');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",2);
            $this->db->where("is_advance",0);
            $this->db->where("staff_id",get_staff_user_id());
            $pay_suppliers = $this->db->get()->row();

            $this->db->select('count(*) as pay_staff');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",3);
            $this->db->where("is_advance",0);
            $this->db->where("staff_id",get_staff_user_id());
            $pay_staff = $this->db->get()->row();

            $this->db->select('count(*) as pay_other');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",4);
            $this->db->where("is_advance",0);
            $this->db->where("staff_id",get_staff_user_id());
            $pay_other = $this->db->get()->row();

            $this->db->select('count(*) as pay_tscd');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",5);
            $this->db->where("is_advance",0);
            $this->db->where("staff_id",get_staff_user_id());
            $pay_tscd = $this->db->get()->row();

        } else {
            $this->db->select('count(*) as alls');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("is_advance",0);
            $count = $this->db->get()->row();

            $this->db->select('count(*) as pay_client');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",1);
            $this->db->where("is_advance",0);
            $pay_client = $this->db->get()->row();

            $this->db->select('count(*) as pay_suppliers');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",2);
            $this->db->where("is_advance",0);
            $pay_suppliers = $this->db->get()->row();

            $this->db->select('count(*) as pay_staff');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",3);
            $this->db->where("is_advance",0);
            $pay_staff = $this->db->get()->row();

            $this->db->select('count(*) as pay_other');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",4);
            $this->db->where("is_advance",0);
            $pay_other = $this->db->get()->row();

            $this->db->select('count(*) as pay_tscd');
            $this->db->from('tblother_payslips');
            $this->db->where("type_vouchers ".$where."");
            $this->db->where("objects",5);
            $this->db->where("is_advance",0);
            $pay_tscd = $this->db->get()->row();
        }
        $data['all'] = $count->alls;
        $data['pay_client'] = $pay_client->pay_client;
        $data['pay_suppliers'] = $pay_suppliers->pay_suppliers;
        $data['pay_staff'] = $pay_staff->pay_staff;
        $data['pay_other'] = $pay_other->pay_other;
        $data['pay_tscd'] = $pay_tscd->pay_tscd;
        echo json_encode($data);
    }

    public function pay_slip_v2()
    {
        $success = false;
        $alert_type = 'warning';
        $message = _l('ch_added_successfuly_not');
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!has_permission('other_payslips', '', 'create')) {
                echo json_encode(array(
                    'success' => true,
                    'alert_type' => 'warning',
                    'message' => 'Bạn không có quyền tạo mới phiếu chi mới'
                ));
                die;
            }
            $id = $data['id_suppliert'];
            unset($data['id_suppliert']);
            $idd = trim($data['idd'], ',');
            unset($data['idd']);
            $ch_total = 0;
            foreach (explode(',', $idd) as $key => $value) {
                if (!empty($value)) {
                    $import = get_table_where('tbl_orders', array('id' => $value), '', 'row');
                    $ch_total += ($import->cost_delivery - $import->price_other_expenses_delivery);
                }
            }
            if ($ch_total < str_replace(',', '', $data['total'])) {
                echo json_encode(array(
                    'success' => 'true',
                    'alert_type' => 'warning',
                    'message' => 'Đã có thay đổi. Vui lòng làm mới lại trang.'
                ));
                die;
            }
            if (count(explode(',', $idd)) > 1) {
                $data['note'] = $this->input->post('note', true);
                $data['objects'] = 2;
                $data['vouchers_id'] = -1;
                $data['objects_id'] = $id;
                $data['type_vouchers'] = 5;
                $data['code'] = Getinfocode('code_pck');
                $data['date'] = to_sql_date($data['date']);
                $data['staff_id'] = get_staff_user_id();
                $data['total'] = str_replace(',', '', $data['total']);
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['prefix'] = get_option('prefix_other_payslips');
                $this->db->insert('tblother_payslips', $data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    $get_code = get_table_where('tblother_payslips', array('id' => $id_pay), '', 'row');
                    activity_log_v2(
                        'work_debt_buy',
                        'tblother_payslips',
                        $id_pay,
                        $get_code->prefix . '-' . $get_code->code,
                        'Thêm mới phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    $detai = '';
                    foreach (explode(',', $idd) as $key => $value) {
                        $import = get_table_where('tbl_orders', array('id' => $value), '', 'row');
                        $detai .= $import->id . '|' . ($import->cost_delivery - $import->price_other_expenses_delivery) . ',';
                        $total = $import->cost_delivery;
                        $this->db->update(
                            'tbl_orders',
                            array('price_other_expenses_delivery' => $total),
                            array('id' => $value)
                        );
                    }
                    $this->db->update('tblother_payslips', array('detai' => $detai), array('id' => $id_pay));
                }
            } else {
                $data['note'] = $this->input->post('note', true);
                $data['objects'] = 2;
                $data['vouchers_id'] = $idd;
                $data['objects_id'] = $id;
                $data['type_vouchers'] = 5;
                $data['code'] = Getinfocode('code_pck');
                $data['date'] = to_sql_date($data['date']);
                $data['staff_id'] = get_staff_user_id();
                $data['total'] = str_replace(',', '', $data['total']);
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['prefix'] = get_option('prefix_other_payslips');
                $this->db->insert('tblother_payslips', $data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    $get_code = get_table_where('tblother_payslips', array('id' => $id_pay), '', 'row');
                    activity_log_v2(
                        'work_debt_buy',
                        'tblother_payslips',
                        $id_pay,
                        $get_code->prefix . '-' . $get_code->code,
                        'Thêm mới phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    $detai = '';
                    $import = get_table_where('tbl_orders', array('id' => $idd), '', 'row');
                    $detai .= $import->id . '|' . ($import->cost_delivery - $import->price_other_expenses_delivery) . ',';
                    $total = $import->cost_delivery;
                    $this->db->update(
                        'tbl_orders',
                        array('price_other_expenses_delivery' => $total),
                        array('id' => $idd)
                    );
                }
            }
            $success = true;
            $alert_type = 'success';
            $message = _l('ch_added_successfuly');
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
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
                if (!has_permission('other_payslips', '', 'edit')) {
                    access_denied('other_payslips');
                }
                $alert_type = 'warning';
                $message = _l('ch_no_updated_successfuly');
                $orther = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
                $data['type_manager'] = !empty($this->input->post('type_manager')) ? $this->input->post('type_manager') : 0;
                $data['note'] = $this->input->post('note', true);
                $data['date'] = to_sql_date($data['date']);
                $data['total'] = str_replace(',', '', $data['total']);
                $id_pay = $this->db->update('tblother_payslips', $data, array('id' => $id));
                if ($id_pay) {
                    $get_code = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
                    activity_log_v2(
                        'work_debt_buy',
                        'tblother_payslips',
                        $id,
                        $get_code->prefix . '-' . $get_code->code,
                        'Cập nhật phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    if ($orther->objects == 2) {
                        if (!empty($orther->type_vouchers)) {
                            if ($orther->type_vouchers == 1) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where(
                                        'tblpurchase_order',
                                        array('id' => $orther->vouchers_id),
                                        '',
                                        'row'
                                    );
                                    $total = $import->price_other_expenses - $orther->total + $data['total'];
                                    if (($total + $import->amount_paid) == $import->total) {
                                        $status = 2;
                                    } elseif (($total + $import->amount_paid) == 0) {
                                        $status = 0;
                                    } else {
                                        $status = 1;
                                    }
                                    $this->db->update(
                                        'tblpurchase_order',
                                        array('price_other_expenses' => $total, 'status_pay' => $status),
                                        array('id' => $orther->vouchers_id)
                                    );
                                }
                            } elseif ($orther->type_vouchers == 5) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where(
                                        'tbl_orders',
                                        array('id' => $orther->vouchers_id),
                                        '',
                                        'row'
                                    );
                                    $total = $import->price_other_expenses_delivery - $orther->total + $data['total'];
                                    $this->db->update(
                                        'tbl_orders',
                                        array('price_other_expenses_delivery' => $total),
                                        array('id' => $orther->vouchers_id)
                                    );
                                }
                            }
                        }
                    }
                    if ($orther->type_vouchers == 12) {
                        if (!empty($orther->vouchers_id)) {
                            $suggestion = get_table_where(
                                'tblsuggestion',
                                array('id' => $orther->vouchers_id),
                                '',
                                'row'
                            );
                            $total = $suggestion->payments - $orther->total + $data['total'];
                            $this->db->update(
                                'tblsuggestion',
                                array('payments' => $total),
                                array('id' => $orther->vouchers_id)
                            );
                        }
                    }
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_updated_successfuly');
                }
            } else {
                if (!has_permission('other_payslips', '', 'create')) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Bạn không có quyền tạo mới phiếu chi mới'
                    ));
                    die;
                }
                $items = [];
                $id_cost = [];
                if ($data['type_vouchers'] == 1) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                    if (!empty($data['vouchers_id'])) {
                        if (!isset($data['items'])) {
                            echo json_encode(array(
                                'success' => true,
                                'alert_type' => 'warning',
                                'message' => 'Vui lòng chọn mặt hàng nhập hàng'
                            ));
                            die;
                        }
                    }
                    if (isset($data['items'])) {
                        $items = $data['items'];
                    }
                    foreach ($items as $key => $value){
                        $id_items = explode('__', $value);
                        $id_import_items = $id_items[1];
                        $this->db->select('tblcosts.id as id_cost,tblimport_items.amount as total');
                        $this->db->from('tblimport_items');
                        $this->db->join('tbl_materials','tbl_materials.id = tblimport_items.product_id');
                        $this->db->join('tblcosts','tblcosts.object_id = tbl_materials.category_id AND tblcosts.type_cost = 1');
                        $this->db->where('tblimport_items.type','nvl');
                        $this->db->where('tblimport_items.id',$id_import_items);
                        $dtCost = $this->db->get()->row_array();
                        if (empty($dtCost)){
                            echo json_encode(array(
                                'success' => true,
                                'alert_type' => 'warning',
                                'message' => 'Vui lòng tổ chức danh mục chi phí trước'
                            ));
                            die;
                        }
                        $id_cost[] = [
                            'id' => $dtCost['id_cost'],
                            'total' => $dtCost['total'],
                        ];
                    }
                }

                if ($data['type_vouchers'] == 65) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                }
                if ($data['type_vouchers'] == 12) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                    if (empty($data['vouchers_id'])) {
                        echo json_encode(array(
                            'success' => true,
                            'alert_type' => 'warning',
                            'message' => 'Vui lòng danh sách chứng từ'
                        ));
                        die;
                    }
//                    foreach ($vouchers_id as $key => $value){
//                        $query = "
//                            SELECT tblinternal_proposal.category_recommended_id,tblinternal_proposal.suggest_id,tblinternal_proposal.id as internal_proposal_id,price_total
//                            FROM `tblsuggestion`
//                            JOIN tblinternal_proposal ON tblinternal_proposal.id = tblsuggestion.id_internal_proposal
//                            WHERE tblsuggestion.id = $value
//
//                            UNION ALL
//
//                            SELECT tblinternal_proposal.category_recommended_id,tblinternal_proposal.suggest_id,tblinternal_proposal.id as internal_proposal_id,price_total
//                            FROM `tblsuggestion`
//                            JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id
//                            WHERE tblsuggestion.id = $value
//                        ";
//                        $dtData = $this->db->query($query)->row_array();
//                        if (empty($dtData)){
//                            echo json_encode(array(
//                                'success' => true,
//                                'alert_type' => 'warning',
//                                'message' => 'Vui lòng tổ chức danh mục chi phí trước'
//                            ));
//                            die;
//                        }
//                        $category_recommended_id = $dtData['category_recommended_id'];
//                        $internal_proposal_id = $dtData['internal_proposal_id'];
//                        if ($category_recommended_id == 40){
//                            $this->db->select('tblcosts.id as id_cost,tbl_request_repair.amount as total');
//                            $this->db->from('tbl_request_repair');
//                            $this->db->join('tblcosts','tblcosts.id = tbl_request_repair.cost_id');
//                            $this->db->where('tbl_request_repair.id',$dtData['suggest_id']);
//                            $dtCost = $this->db->get()->row_array();
//                            if (empty($dtCost)){
//                                echo json_encode(array(
//                                    'success' => true,
//                                    'alert_type' => 'warning',
//                                    'message' => 'Vui lòng tổ chức danh mục chi phí trước'
//                                ));
//                                die;
//                            }
//                            $id_cost[] = [
//                                'id' => $dtCost['id_cost'],
//                                'total' => $dtCost['total'],
//                            ];
//                        } elseif ($category_recommended_id == 41){
//                            $this->db->select('tblcosts.id as id_cost, tbl_suggest_payslips_items.total as total');
//                            $this->db->from('tbl_suggest_muti_id');
//                            $this->db->join('tbl_suggest_payslips','tbl_suggest_payslips.id = tbl_suggest_muti_id.suggest_id');
//                            $this->db->join('tbl_suggest_payslips_items','tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id');
//                            $this->db->join('tblcosts','tblcosts.id = tbl_suggest_payslips_items.cost_id');
//                            $this->db->where('tbl_suggest_muti_id.id_internal_proposal',$internal_proposal_id);
//                            $dtCost = $this->db->get()->result_array();
//                            if (empty($dtCost)){
//                                echo json_encode(array(
//                                    'success' => true,
//                                    'alert_type' => 'warning',
//                                    'message' => 'Vui lòng tổ chức danh mục chi phí trước'
//                                ));
//                                die;
//                            }
//                            foreach ($dtCost as $kk => $vv) {
//                                $id_cost[] = [
//                                    'id' => $vv['id_cost'],
//                                    'total' => $vv['total'],
//                                ];
//                            }
//                        }
//                    }
                    if (empty($data['cost_id'])){
                        echo json_encode(array(
                            'success' => true,
                            'alert_type' => 'warning',
                            'message' => 'Vui lòng tổ chức danh mục chi phí trước'
                        ));
                        die;
                    }

                    foreach ($data['cost_id'] as $key => $value){
                        $cost_id = ($value);
                        $total = number_unformat($data['total_child'][$key]);
                        $table_object = ($data['table_object'][$key]);
                        $object_id = ($data['object_id'][$key]);
                        $id_cost[] = [
                            'id' => $cost_id,
                            'total' => $total,
                            'object_id' => $object_id,
                            'table_object' => $table_object,
                        ];
                    }
                }
                if (empty($id_cost)){
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Vui lòng tổ chức danh mục chi phí trước'
                    ));
                    die;
                }
                $id_cost_detail = $id_cost;
                $newArray = [];

                foreach ($id_cost as $item) {
                    $found = false;
                    foreach ($newArray as &$newItem) {
                        if ($newItem['id'] == $item['id']) {
                            $newItem['total'] += $item['total'];
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $newArray[] = $item;
                    }
                }
                unset($data['items']);
                unset($data['total_child']);
                unset($data['object_id']);
                unset($data['table_object']);
                unset($data['cost_id']);
                $total_1_service = 0;
                $data['id_costs'] = $newArray[0]['id'];
                $data['type_manager'] = !empty($this->input->post('type_manager')) ? $this->input->post('type_manager') : 0;
                $data['note'] = $this->input->post('note', true);
                $data['code'] = Getinfocode('code_pck');
                $data['date'] = to_sql_date($data['date']);
                $data['staff_id'] = get_staff_user_id();
                $data['total'] = str_replace(',', '', $data['total']);
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['prefix'] = get_option('prefix_other_payslips');
                $data['type_new'] = 1;
                $this->db->insert('tblother_payslips', $data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    if ($data['vouchers_id'] == -1) {
                        $data['vouchers_id'] = $vouchers_id;
                    }
                    $get_code = get_table_where('tblother_payslips', array('id' => $id_pay), '', 'row');
                    activity_log_v2(
                        'work_debt_buy',
                        'tblother_payslips',
                        $id_pay,
                        $get_code->prefix . '-' . $get_code->code,
                        'Thêm mới phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    if ($data['objects'] == 2) {
                        if (!empty($data['type_vouchers'])) {
                            if ($data['type_vouchers'] == 1) {
                                if (!empty($data['vouchers_id'])) {
                                    if (!empty($items)) {
                                        $total = 0;
                                        $array_import = [];
                                        foreach ($items as $key => $value) {
                                            $id_items = explode('__', $value);
                                            $detail = array();
                                            $detail['id_other_payslips'] = $id_pay;
                                            $detail['id_import'] = $id_items[0];
                                            $detail['id_import_items'] = $id_items[1];
                                            $this->db->insert('tblother_payslips_import_items', $detail);
                                            $items_items = get_table_where('tblimport_items', array('id' => $id_items[1]), '', 'row_array');
                                            $total += $items_items['amount'];
                                            if (!empty($array_import[$id_items[0]])) {
                                                $array_import[$id_items[0]] += $items_items['amount'];
                                            } else {
                                                $array_import[$id_items[0]] = $items_items['amount'];
                                            }
                                        }
                                        $array_purchase_order = [];
                                        foreach ($data['vouchers_id'] as $key => $value) {
                                            $import = get_table_where('tblimport', array('id' => $value), '', 'row_array');
                                            $array_purchase_order[] = $import['id_order'];
                                            $total_import = (!empty($array_import[$value]) ? $array_import[$value] : 0);
                                            if (($total_import + $import['amount_paid']) >= $import['total']) {
                                                $status = 2;
                                            } elseif (($total_import + $import['amount_paid']) == 0) {
                                                $status = 0;
                                            } else {
                                                $status = 1;
                                            }
                                            $this->db->update(
                                                'tblimport',
                                                array('amount_paid' => ($total_import + $import['amount_paid']), 'status_pay' => $status),
                                                array('id' => $value)
                                            );
                                            $detail = array();
                                            $detail['other_pay'] = $id_pay;
                                            $detail['id_import'] = $value;
                                            $detail['total'] = $total_import;
                                            $this->db->insert('tblother_payslips_detail', $detail);
                                        }
                                        if (!empty($newArray)){
                                            foreach ($newArray as $k => $v){
                                                $this->db->insert('tblother_payslip_cost',[
                                                    'other_payslip_id' => $id_pay,
                                                    'cost_id' => $v['id'],
                                                    'total' => $v['total'],
                                                ]);
                                            }
                                        }
                                        $array_purchase_order = array_unique($array_purchase_order);
                                        foreach ($array_purchase_order as $key => $value) {
                                            $purchase_order = get_table_where('tblpurchase_order', array('id' => $value), '', 'row_array');
                                            $import = get_table_where('tblimport', array('id_order' => $value));
                                            $price_other_expenses = 0;
                                            foreach ($import as $k => $v) {
                                                $price_other_expenses += $v['amount_paid'];
                                            }
                                            $total = $price_other_expenses;
                                            if (($total) >= $purchase_order['totalAll_suppliers']) {
                                                $status = 2;
                                            } else {
                                                $status = 1;
                                            }
                                            $this->db->update(
                                                'tblpurchase_order',
                                                array('price_other_expenses' => $total, 'status_pay' => $status),
                                                array('id' => $value)
                                            );
                                        }
                                    }
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
                                        $this->db->update(
                                            'tbl_services',
                                            array('payment' => $total_service, 'status_pay' => $status),
                                            array('id' => $value)
                                        );
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
                                    $import = get_table_where(
                                        'tbl_orders',
                                        array('id' => $data['vouchers_id']),
                                        '',
                                        'row'
                                    );
                                    $total = $import->price_other_expenses_delivery + $data['total'];
                                    $this->db->update(
                                        'tbl_orders',
                                        array('price_other_expenses_delivery' => $total),
                                        array('id' => $data['vouchers_id'])
                                    );
                                }
                            }
                        }
                    }
                    if ($data['type_vouchers'] == 12) {
                        if (!empty($data['vouchers_id'])) {
                            //
                            foreach ($data['vouchers_id'] as $key => $value) {
                                $import = get_table_where('tblsuggestion', array('id' => $value), '', 'row');
                                if ($key == 0) {
                                    if ($import->price_total - $import->payments < $data['total']) {
                                        $total_add_service = $import->price_total - $import->payments;
                                        $total_1_service = $data['total'] - $total_add_service;
                                        $total_service = $import->payments + $total_add_service;
                                    } else {
                                        $total_add_service = $data['total'];
                                        $total_1_service = 0;
                                        $total_service = $import->payments + $total_add_service;
                                    }
                                } else {
                                    if ($import->price_total - $import->payments < $total_1_service) {
                                        $total_add_service = $import->price_total - $import->payments;
                                        $total_1_service = $total_1_service - $total_add_service;
                                        $total_service = $import->payments + $total_add_service;
                                    } else {
                                        $total_add_service = $total_1_service;
                                        $total_1_service = 0;
                                        $total_service = $import->payments + $total_add_service;
                                    }
                                }
                                $this->db->update(
                                    'tblsuggestion',
                                    array('payments' => $total_service),
                                    array('id' => $value)
                                );
                                //insert detail
                                $detail = array();
                                $detail['other_pay'] = $id_pay;
                                $detail['id_service'] = $value;
                                $detail['total'] = $total_add_service;
                                $this->db->insert('tblother_payslips_detail', $detail);

                                if (!empty($newArray)){
                                    foreach ($newArray as $k => $v){
                                        $this->db->insert('tblother_payslip_cost',[
                                            'other_payslip_id' => $id_pay,
                                            'cost_id' => $v['id'],
                                            'total' => $v['total'],
                                        ]);
                                    }
                                }

                                if (!empty($id_cost_detail)){
                                    foreach ($id_cost_detail as $k => $v){
                                        $this->db->insert('tblother_payslip_cost_detail',[
                                            'other_payslip_id' => $id_pay,
                                            'cost_id' => $v['id'],
                                            'total' => $v['total'],
                                            'object_id' => $v['object_id'],
                                            'table_object' => $v['table_object'],
                                        ]);
                                    }
                                }

                                update_plan_propose_internal_proposal($value);
                                //
                            }
                            //
                        }
                    }
                    // if ($data['type_vouchers'] == 12) {
                    //  if (!empty($data['vouchers_id'])) {
                    //      $import = get_table_where('tblsuggestion', array('id' => $data['vouchers_id']), '', 'row');
                    //      $total = $import->payments + $data['total'];
                    //      $this->db->update('tblsuggestion', array('payments' => $total), array('id' => $data['vouchers_id']));
                    //  }
                    // }
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

    public function pay_slip_old()
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
                if (!has_permission('other_payslips', '', 'edit')) {
                    access_denied('other_payslips');
                }
                $alert_type = 'warning';
                $message = _l('ch_no_updated_successfuly');
                $orther = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
                $data['type_manager'] = !empty($this->input->post('type_manager')) ? $this->input->post('type_manager') : 0;
                $data['note'] = $this->input->post('note', true);
                $data['date'] = to_sql_date($data['date']);
                $data['total'] = str_replace(',', '', $data['total']);
                $id_pay = $this->db->update('tblother_payslips', $data, array('id' => $id));
                if ($id_pay) {
                    $get_code = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
                    activity_log_v2(
                        'work_debt_buy',
                        'tblother_payslips',
                        $id,
                        $get_code->prefix . '-' . $get_code->code,
                        'Cập nhật phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    if ($orther->objects == 2) {
                        if (!empty($orther->type_vouchers)) {
                            if ($orther->type_vouchers == 1) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where(
                                        'tblpurchase_order',
                                        array('id' => $orther->vouchers_id),
                                        '',
                                        'row'
                                    );
                                    $total = $import->price_other_expenses - $orther->total + $data['total'];
                                    if (($total + $import->amount_paid) == $import->total) {
                                        $status = 2;
                                    } elseif (($total + $import->amount_paid) == 0) {
                                        $status = 0;
                                    } else {
                                        $status = 1;
                                    }
                                    $this->db->update(
                                        'tblpurchase_order',
                                        array('price_other_expenses' => $total, 'status_pay' => $status),
                                        array('id' => $orther->vouchers_id)
                                    );
                                }
                            } elseif ($orther->type_vouchers == 5) {
                                if (!empty($orther->vouchers_id)) {
                                    $import = get_table_where(
                                        'tbl_orders',
                                        array('id' => $orther->vouchers_id),
                                        '',
                                        'row'
                                    );
                                    $total = $import->price_other_expenses_delivery - $orther->total + $data['total'];
                                    $this->db->update(
                                        'tbl_orders',
                                        array('price_other_expenses_delivery' => $total),
                                        array('id' => $orther->vouchers_id)
                                    );
                                }
                            }
                        }
                    }
                    if ($orther->type_vouchers == 12) {
                        if (!empty($orther->vouchers_id)) {
                            $suggestion = get_table_where(
                                'tblsuggestion',
                                array('id' => $orther->vouchers_id),
                                '',
                                'row'
                            );
                            $total = $suggestion->payments - $orther->total + $data['total'];
                            $this->db->update(
                                'tblsuggestion',
                                array('payments' => $total),
                                array('id' => $orther->vouchers_id)
                            );
                        }
                    }
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_updated_successfuly');
                }
            } else {
                if (!has_permission('other_payslips', '', 'create')) {
                    echo json_encode(array(
                        'success' => true,
                        'alert_type' => 'warning',
                        'message' => 'Bạn không có quyền tạo mới phiếu chi mới'
                    ));
                    die;
                }
                $items = [];
                if ($data['type_vouchers'] == 1) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                    if (!empty($data['vouchers_id'])) {
                        if (!isset($data['items'])) {
                            echo json_encode(array(
                                'success' => true,
                                'alert_type' => 'warning',
                                'message' => 'Vui lòng chọn mặt hàng nhập hàng'
                            ));
                            die;
                        }
                    }
                    if (isset($data['items'])) {
                        $items = $data['items'];
                    }
                }
                if ($data['type_vouchers'] == 65) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                }
                if ($data['type_vouchers'] == 12) {
                    $vouchers_id = $data['vouchers_id'];
                    $data['vouchers_id'] = -1;
                }
                unset($data['items']);
                $total_1_service = 0;
                $data['type_manager'] = !empty($this->input->post('type_manager')) ? $this->input->post('type_manager') : 0;
                $data['note'] = $this->input->post('note', true);
                $data['code'] = Getinfocode('code_pck');
                $data['date'] = to_sql_date($data['date']);
                $data['staff_id'] = get_staff_user_id();
                $data['total'] = str_replace(',', '', $data['total']);
                $data['date_create'] = date('Y-m-d H:i:s');
                $data['prefix'] = get_option('prefix_other_payslips');
                $this->db->insert('tblother_payslips', $data);
                $id_pay = $this->db->insert_id();
                if ($id_pay) {
                    if ($data['vouchers_id'] == -1) {
                        $data['vouchers_id'] = $vouchers_id;
                    }
                    $get_code = get_table_where('tblother_payslips', array('id' => $id_pay), '', 'row');
                    activity_log_v2(
                        'work_debt_buy',
                        'tblother_payslips',
                        $id_pay,
                        $get_code->prefix . '-' . $get_code->code,
                        'Thêm mới phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    if ($data['objects'] == 2) {
                        if (!empty($data['type_vouchers'])) {
                            if ($data['type_vouchers'] == 1) {
                                if (!empty($data['vouchers_id'])) {
                                    if (!empty($items)) {
                                        $total = 0;
                                        $array_import = [];
                                        foreach ($items as $key => $value) {
                                            $id_items = explode('__', $value);
                                            $detail = array();
                                            $detail['id_other_payslips'] = $id_pay;
                                            $detail['id_import'] = $id_items[0];
                                            $detail['id_import_items'] = $id_items[1];
                                            $this->db->insert('tblother_payslips_import_items', $detail);
                                            $items_items = get_table_where('tblimport_items', array('id' => $id_items[1]), '', 'row_array');
                                            $total += $items_items['amount'];
                                            if (!empty($array_import[$id_items[0]])) {
                                                $array_import[$id_items[0]] += $items_items['amount'];
                                            } else {
                                                $array_import[$id_items[0]] = $items_items['amount'];
                                            }
                                        }
                                        $array_purchase_order = [];
                                        foreach ($data['vouchers_id'] as $key => $value) {
                                            $import = get_table_where('tblimport', array('id' => $value), '', 'row_array');
                                            $array_purchase_order[] = $import['id_order'];
                                            $total_import = (!empty($array_import[$value]) ? $array_import[$value] : 0);
                                            if (($total_import + $import['amount_paid']) >= $import['total']) {
                                                $status = 2;
                                            } elseif (($total_import + $import['amount_paid']) == 0) {
                                                $status = 0;
                                            } else {
                                                $status = 1;
                                            }
                                            $this->db->update(
                                                'tblimport',
                                                array('amount_paid' => ($total_import + $import['amount_paid']), 'status_pay' => $status),
                                                array('id' => $value)
                                            );
                                            $detail = array();
                                            $detail['other_pay'] = $id_pay;
                                            $detail['id_import'] = $value;
                                            $detail['total'] = $total_import;
                                            $this->db->insert('tblother_payslips_detail', $detail);
                                        }
                                        $array_purchase_order = array_unique($array_purchase_order);
                                        foreach ($array_purchase_order as $key => $value) {
                                            $purchase_order = get_table_where('tblpurchase_order', array('id' => $value), '', 'row_array');
                                            $import = get_table_where('tblimport', array('id_order' => $value));
                                            $price_other_expenses = 0;
                                            foreach ($import as $k => $v) {
                                                $price_other_expenses += $v['amount_paid'];
                                            }
                                            $total = $price_other_expenses;
                                            if (($total) >= $purchase_order['totalAll_suppliers']) {
                                                $status = 2;
                                            } else {
                                                $status = 1;
                                            }
                                            $this->db->update(
                                                'tblpurchase_order',
                                                array('price_other_expenses' => $total, 'status_pay' => $status),
                                                array('id' => $value)
                                            );
                                        }
                                    }
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
                                        $this->db->update(
                                            'tbl_services',
                                            array('payment' => $total_service, 'status_pay' => $status),
                                            array('id' => $value)
                                        );
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
                                    $import = get_table_where(
                                        'tbl_orders',
                                        array('id' => $data['vouchers_id']),
                                        '',
                                        'row'
                                    );
                                    $total = $import->price_other_expenses_delivery + $data['total'];
                                    $this->db->update(
                                        'tbl_orders',
                                        array('price_other_expenses_delivery' => $total),
                                        array('id' => $data['vouchers_id'])
                                    );
                                }
                            }
                        }
                    }
                    if ($data['type_vouchers'] == 12) {
                        if (!empty($data['vouchers_id'])) {
                            //
                            foreach ($data['vouchers_id'] as $key => $value) {
                                $import = get_table_where('tblsuggestion', array('id' => $value), '', 'row');
                                if ($key == 0) {
                                    if ($import->price_total - $import->payments < $data['total']) {
                                        $total_add_service = $import->price_total - $import->payments;
                                        $total_1_service = $data['total'] - $total_add_service;
                                        $total_service = $import->payments + $total_add_service;
                                    } else {
                                        $total_add_service = $data['total'];
                                        $total_1_service = 0;
                                        $total_service = $import->payments + $total_add_service;
                                    }
                                } else {
                                    if ($import->price_total - $import->payments < $total_1_service) {
                                        $total_add_service = $import->price_total - $import->payments;
                                        $total_1_service = $total_1_service - $total_add_service;
                                        $total_service = $import->payments + $total_add_service;
                                    } else {
                                        $total_add_service = $total_1_service;
                                        $total_1_service = 0;
                                        $total_service = $import->payments + $total_add_service;
                                    }
                                }
                                $this->db->update(
                                    'tblsuggestion',
                                    array('payments' => $total_service),
                                    array('id' => $value)
                                );
                                //insert detail
                                $detail = array();
                                $detail['other_pay'] = $id_pay;
                                $detail['id_service'] = $value;
                                $detail['total'] = $total_add_service;
                                $this->db->insert('tblother_payslips_detail', $detail);
                                update_plan_propose_internal_proposal($value);
                                //
                            }
                            //
                        }
                    }
                    // if ($data['type_vouchers'] == 12) {
                    //  if (!empty($data['vouchers_id'])) {
                    //      $import = get_table_where('tblsuggestion', array('id' => $data['vouchers_id']), '', 'row');
                    //      $total = $import->payments + $data['total'];
                    //      $this->db->update('tblsuggestion', array('payments' => $total), array('id' => $data['vouchers_id']));
                    //  }
                    // }
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
                'tblstaff.staffid as id,
                tblstaff.code as code_client,
                CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, "")) as text',
                false
            );
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('CONCAT(COALESCE(firstname, ""), " ", COALESCE(lastname, ""))', $search);
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
            if ($data['objects'] == 2) {
                if ($data['type_vouchers'] == 1) {
                    $this->db->select('tblimport.id as id, CONCAT(tblimport.prefix, "-", tblimport.code) as reference_no, tblimport.date, 
                        tblimport.total
                        ');
                    $this->db->where('tblimport.suppliers_id', $data['objects_id']);
                    $this->db->order_by('tblimport.date', 'desc');
                    $dbresult = $this->db->get('tblimport')->result_array();
                    $arrImports = [];
                    $count = 0;
                    foreach ($dbresult as $key => $import) {
                        if ($this->checkPayImport($import['id'])) {
                            unset($dbresult[$key]);
                        } else {
                            $import['total'] = number_format($import['total']);
                            $import['date'] = _d($import['date']);
                            $flag = false;
                            foreach ($_data as $grImport) {
                                if ($grImport['date'] == $import['date']) {
                                    $_data[$count]['imports'][] = $import;
                                    $flag = true;
                                }
                            }
                            if (!$flag) {
                                $count++;
                                $_data[$count]['date'] = $import['date'];
                                $_data[$count]['imports'][] = $import;
                            }
                        }
                    }
                }
            }
            if ($data['type_vouchers'] == 12) {
                if ($data['objects'] == 3) {
                    if (!empty($data['objects_id'])) {
                        $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` 
                            FROM `tblsuggestion`
                            WHERE `staffid` = " . $data['objects_id'] . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                    } else {
                        $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` FROM `tblsuggestion`WHERE `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                    }
                } else {
                    $objects_id = !empty($data['objects_id']) ? $data['objects_id'] : 0;
                    $whereNew = '';
                    $whereNewRepair = '';
                    if (!empty($objects_id)){
                        $whereNew = 'AND tbl_suggest_payslips.suppliers_id = '.$objects_id.'';
                        $whereNewRepair = 'AND tbl_request_repair.supplier_id = '.$objects_id.'';
                    }
                    $query = "
                        SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total`
                        FROM `tblsuggestion` 
                        JOIN tblinternal_proposal ON tblinternal_proposal.id = tblsuggestion.id_internal_proposal
                        WHERE EXISTS(
                            SELECT 1
                            FROM tbl_suggest_muti_id
                            JOIN tbl_suggest_payslips ON tbl_suggest_payslips.id = tbl_suggest_muti_id.suggest_id
                            JOIN tbl_suggest_payslips_items ON tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id
                            WHERE tbl_suggest_muti_id.id_internal_proposal = tblinternal_proposal.id
                            $whereNew
                        ) AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total` AND tblinternal_proposal.category_recommended_id = 41
                        
                        UNION ALL
                        
                        SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` 
                        FROM `tblsuggestion`
                        JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id 
                        JOIN tbl_request_repair ON tbl_request_repair.id = tblinternal_proposal.suggest_id AND tblinternal_proposal.category_recommended_id = 40
                        WHERE `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total` $whereNewRepair
                    ";
                }
                $_data = $this->db->query($query)->result_array();
            }
        }
        echo json_encode($_data);
        die();
    }

    public function vouchers_id_old()
    {
        $data = $this->input->post();
        $_data = array();
        if (!empty($data)) {
            if ($data['objects'] == 2) {
                if ($data['type_vouchers'] == 1) {
                    if (has_permission('import', '', 'view')) {
                        // $_data = get_table_where_select(
                        //     'tblpurchase_order.*,tblpurchase_order.totalAll_suppliers as total',
                        //     'tblpurchase_order',
                        //     array(
                        //         'suppliers_id' => $data['objects_id'],
                        //         'red_invoice' => 0,
                        //         'status >' => 1,
                        //         'status_pay !=' => 2
                        //     )
                        // );
                        $this->db->select('tblimport.id as id, CONCAT(tblimport.prefix, "-", tblimport.code) as reference_no, tblimport.date, tblimport.total');
                        $this->db->where('tblimport.suppliers_id', $data['objects_id']);
                        $this->db->order_by('tblimport.date', 'desc');
                        $dbresult = $this->db->get('tblimport')->result_array();
                        $arrImports = [];
                        $count = 0;
                        foreach ($dbresult as $key => $import) {
                            if ($this->checkPayImport($import['id'])) {
                                unset($dbresult[$key]);
                            } else {
                                $import['total'] = number_format($import['total']);
                                $import['date'] = _d($import['date']);
                                $flag = false;
                                foreach ($_data as $grImport) {
                                    if ($grImport['date'] == $import['date']) {
                                        $_data[$count]['imports'][] = $import;
                                        $flag = true;
                                    }
                                }
                                if (!$flag) {
                                    $count++;
                                    $_data[$count]['date'] = $import['date'];
                                    $_data[$count]['imports'][] = $import;
                                }
                            }
                        }
                    } else {
                        $_data = get_table_where_select(
                            'tblpurchase_order.*,tblpurchase_order.totalAll_suppliers as total',
                            'tblpurchase_order',
                            array(
                                'suppliers_id' => $data['objects_id'],
                                'red_invoice' => 0,
                                'status >' => 1,
                                'status_pay !=' => 2,
                                'staff_create' => get_staff_user_id()
                            )
                        );
                    }
                } elseif ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array(
                                'object' => $data['objects'],
                                'id_object' => $data['objects_id'],
                                'status >' => 0,
                                'staff_id' => get_staff_user_id()
                            )
                        );
                    }
                } elseif ($data['type_vouchers'] == 5) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tbl_orders.*,tbl_orders.cost_delivery as total',
                            'tbl_orders',
                            array(
                                'price_other_expenses_delivery < cost_delivery',
                                'transporter_id' => $data['objects_id'],
                                'status' => 'approved'
                            )
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tbl_orders.*,tbl_orders.cost_delivery as total',
                            'tbl_orders',
                            array(
                                'price_other_expenses_delivery < cost_delivery',
                                'transporter_id' => $data['objects_id'],
                                'status >' => 'approved',
                                'created_by' => get_staff_user_id()
                            )
                        );
                    }
                } elseif ($data['type_vouchers'] == 65) {
                    $this->db->select('tbl_services.*,tbl_services.subtotal as total,tbl_services.payment as payment');
                    $this->db->where('status > ', 0);
                    $this->db->where('suppliers_id ', $data['objects_id']);
                    $this->db->where('status_pay < ', 2);
                    $_data = $this->db->get('tbl_services')->result_array();
                }
            }
            if ($data['objects'] == 1) {
                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array(
                                'object' => $data['objects'],
                                'id_object' => $data['objects_id'],
                                'status >' => 0,
                                'staff_id' => get_staff_user_id()
                            )
                        );
                    }
                }
            }
            if ($data['objects'] == 3) {
                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array(
                                'object' => $data['objects'],
                                'id_object' => $data['objects_id'],
                                'status >' => 0,
                                'staff_id' => get_staff_user_id()
                            )
                        );
                    }
                }
            }
            if ($data['type_vouchers'] == 12) {
                if ($data['objects'] == 3) {
                    if (!empty($data['objects_id'])) {
                        $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` FROM `tblsuggestion`WHERE `staffid` = " . $data['objects_id'] . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                    } else {
                        $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` FROM `tblsuggestion`WHERE `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                    }
                } else {
                    $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` FROM `tblsuggestion`WHERE `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                }
                $_data = $this->db->query($query)->result_array();
            }
            if ($data['objects'] == 4) {
                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'status >' => 0, 'staff_id' => get_staff_user_id())
                        );
                    }
                }
            }
            if ($data['type_vouchers'] == 9) {
                if (is_admin()) {
                    $_data = get_table_where_select(
                        'tbl_productions_orders_details.*,0 as total',
                        'tbl_productions_orders_details'
                    );
                } else {
                    $_data = get_table_where_select(
                        'tbl_productions_orders_details.*,0 as total',
                        'tbl_productions_orders_details',
                        array('created_by' => get_staff_user_id())
                    );
                }
            }
        }
        echo json_encode($_data);
        die();
    }

    public function vouchers_id_old_vs1()
    {
        $data = $this->input->post();
        $_data = array();
        if (!empty($data)) {
            $data['id_costs'] = !empty($data['id_costs']) ? $data['id_costs'] : 0;
            $machine_id = 0;
            $category_payslip = 0;
            $dtCost = get_table_where('tblcosts',['id' => $data['id_costs']],'','row_array');
            if (!empty($dtCost)){
                if ($dtCost['type_cost'] == 2){
                    $machine_id = $dtCost['object_id'];
                }
                if ($dtCost['type_cost'] == 3){
                    $category_payslip = $dtCost['object_id'];
                }
            }
            if ($data['objects'] == 2) {
                if ($data['type_vouchers'] == 1) {
                    if (has_permission('import', '', 'view')) {
                        // $_data = get_table_where_select(
                        //     'tblpurchase_order.*,tblpurchase_order.totalAll_suppliers as total',
                        //     'tblpurchase_order',
                        //     array(
                        //         'suppliers_id' => $data['objects_id'],
                        //         'red_invoice' => 0,
                        //         'status >' => 1,
                        //         'status_pay !=' => 2
                        //     )
                        // );


                        $this->db->select('tblimport.id as id, CONCAT(tblimport.prefix, "-", tblimport.code) as reference_no, tblimport.date, 
                        COALESCE((  
                                SELECT SUM(tblimport_items.amount)
                                FROM tblcosts
                                JOIN tbl_category_items ON tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1
                                JOIN tbl_materials ON tbl_materials.category_id = tbl_category_items.id
                                JOIN tblimport_items ON tblimport_items.product_id = tbl_materials.id AND tblimport_items.type = "nvl"
                                WHERE tblimport_items.id_import = tblimport.id AND tblcosts.id = '.$data['id_costs'].'),0) as total
                        ');
                        $this->db->where('tblimport.suppliers_id', $data['objects_id']);
                        if (!empty($data['id_costs'])) {
                            $this->db->where('EXISTS (
                                SELECT 1
                                FROM tblcosts
                                JOIN tbl_category_items ON tbl_category_items.id = tblcosts.object_id AND tblcosts.type_cost = 1
                                JOIN tbl_materials ON tbl_materials.category_id = tbl_category_items.id
                                JOIN tblimport_items ON tblimport_items.product_id = tbl_materials.id AND tblimport_items.type = "nvl"
                                WHERE tblcosts.id = '.$data['id_costs'].' AND tblimport_items.id_import = tblimport.id
                            )');
                        }
                        $this->db->order_by('tblimport.date', 'desc');
                        $dbresult = $this->db->get('tblimport')->result_array();
                        $arrImports = [];
                        $count = 0;
                        foreach ($dbresult as $key => $import) {
                            if ($this->checkPayImport($import['id'])) {
                                unset($dbresult[$key]);
                            } else {
                                $import['total'] = number_format($import['total']);
                                $import['date'] = _d($import['date']);
                                $flag = false;
                                foreach ($_data as $grImport) {
                                    if ($grImport['date'] == $import['date']) {
                                        $_data[$count]['imports'][] = $import;
                                        $flag = true;
                                    }
                                }
                                if (!$flag) {
                                    $count++;
                                    $_data[$count]['date'] = $import['date'];
                                    $_data[$count]['imports'][] = $import;
                                }
                            }
                        }
                    } else {
                        $_data = get_table_where_select(
                            'tblpurchase_order.*,tblpurchase_order.totalAll_suppliers as total',
                            'tblpurchase_order',
                            array(
                                'suppliers_id' => $data['objects_id'],
                                'red_invoice' => 0,
                                'status >' => 1,
                                'status_pay !=' => 2,
                                'staff_create' => get_staff_user_id()
                            )
                        );
                    }
                } elseif ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array(
                                'object' => $data['objects'],
                                'id_object' => $data['objects_id'],
                                'status >' => 0,
                                'staff_id' => get_staff_user_id()
                            )
                        );
                    }
                } elseif ($data['type_vouchers'] == 5) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tbl_orders.*,tbl_orders.cost_delivery as total',
                            'tbl_orders',
                            array(
                                'price_other_expenses_delivery < cost_delivery',
                                'transporter_id' => $data['objects_id'],
                                'status' => 'approved'
                            )
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tbl_orders.*,tbl_orders.cost_delivery as total',
                            'tbl_orders',
                            array(
                                'price_other_expenses_delivery < cost_delivery',
                                'transporter_id' => $data['objects_id'],
                                'status >' => 'approved',
                                'created_by' => get_staff_user_id()
                            )
                        );
                    }
                } elseif ($data['type_vouchers'] == 65) {
                    $this->db->select('tbl_services.*,tbl_services.subtotal as total,tbl_services.payment as payment');
                    $this->db->where('status > ', 0);
                    $this->db->where('suppliers_id ', $data['objects_id']);
                    $this->db->where('status_pay < ', 2);
                    $_data = $this->db->get('tbl_services')->result_array();
                }
            }
            if ($data['objects'] == 1) {
                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array(
                                'object' => $data['objects'],
                                'id_object' => $data['objects_id'],
                                'status >' => 0,
                                'staff_id' => get_staff_user_id()
                            )
                        );
                    }
                }
            }
            if ($data['objects'] == 3) {
                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'id_object' => $data['objects_id'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array(
                                'object' => $data['objects'],
                                'id_object' => $data['objects_id'],
                                'status >' => 0,
                                'staff_id' => get_staff_user_id()
                            )
                        );
                    }
                }
            }
            if ($data['type_vouchers'] == 12) {
                if ($data['objects'] == 3) {
                    if (!empty($data['objects_id'])) {
                        if (!empty($data['id_costs'])){
                            if (!empty($category_payslip)){
                                $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` 
                                FROM `tblsuggestion`
                                JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id
                                WHERE EXISTS(
                                    SELECT 1
                                    FROM tbl_suggest_muti_id
                                    JOIN tbl_suggest_payslips ON tbl_suggest_payslips.id = tbl_suggest_muti_id.suggest_id
                                    JOIN tbl_suggest_payslips_items ON tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id
                                    WHERE tbl_suggest_muti_id.id_internal_proposal = tblinternal_proposal.id
                                    AND tbl_suggest_payslips_items.category_payslip = " . $category_payslip . "
                                ) AND `staffid` = " . $data['objects_id'] . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total` AND tblinternal_proposal.category_recommended_id = 41";
                            } else {
                                $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` 
                                FROM `tblsuggestion`
                                JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id
                                JOIN tbl_request_repair ON tbl_request_repair.id = tblinternal_proposal.suggest_id AND tblinternal_proposal.category_recommended_id = 40
                                WHERE tbl_request_repair.machines_id = " . $machine_id . " AND `staffid` = " . $data['objects_id'] . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                            }
                        } else {
                            $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` 
                            FROM `tblsuggestion`
                            WHERE `staffid` = " . $data['objects_id'] . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                        }
                    } else {
                        if (!empty($data['id_costs'])){
                            if (!empty($category_payslip)){
                                $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total`
                                FROM `tblsuggestion` 
                                JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id
                                WHERE EXISTS(
                                    SELECT 1
                                    FROM tbl_suggest_muti_id
                                    JOIN tbl_suggest_payslips ON tbl_suggest_payslips.id = tbl_suggest_muti_id.suggest_id
                                    JOIN tbl_suggest_payslips_items ON tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id
                                    WHERE tbl_suggest_muti_id.id_internal_proposal = tblinternal_proposal.id
                                    AND tbl_suggest_payslips_items.category_payslip = ".$category_payslip."
                                ) AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total` AND tblinternal_proposal.category_recommended_id = 41";
                            } else {
                                $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` 
                                FROM `tblsuggestion`
                                JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id 
                                JOIN tbl_request_repair ON tbl_request_repair.id = tblinternal_proposal.suggest_id AND tblinternal_proposal.category_recommended_id = 40
                                WHERE tbl_request_repair.machines_id = " . $machine_id . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                            }
                        } else {
                            $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total` FROM `tblsuggestion`WHERE `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                        }
                    }
                } else {
                    $objects_id = !empty($data['objects_id']) ? $data['objects_id'] : 0;
                    $whereNew = '';
                    if (!empty($objects_id)){
                        $whereNew = 'AND tbl_suggest_payslips.suppliers_id = '.$objects_id.'';
                    }
                    if (!empty($data['id_costs'])){
                        if (!empty($category_payslip)){
                            $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total`
                                FROM `tblsuggestion` 
                                JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id
                                WHERE EXISTS(
                                    SELECT 1
                                    FROM tbl_suggest_muti_id
                                    JOIN tbl_suggest_payslips ON tbl_suggest_payslips.id = tbl_suggest_muti_id.suggest_id
                                    JOIN tbl_suggest_payslips_items ON tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id
                                    WHERE tbl_suggest_muti_id.id_internal_proposal = tblinternal_proposal.id
                                    AND tbl_suggest_payslips_items.category_payslip = ".$category_payslip." 
                                ) AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total` AND tblinternal_proposal.category_recommended_id = 41";
                        } else {
                            $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total`
                            FROM `tblsuggestion` 
                            JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id 
                            JOIN tbl_request_repair ON tbl_request_repair.id = tblinternal_proposal.suggest_id AND tblinternal_proposal.category_recommended_id = 40
                            WHERE tbl_request_repair.machines_id = " . $machine_id . " AND `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                        }
                    } else {
                        $query = "SELECT `tblsuggestion`.*, (`tblsuggestion`.`price_total` - `tblsuggestion`.`payments`) as `total`
                        FROM `tblsuggestion` 
                        WHERE `status_dn` > 0 AND `status_tp` > 0 AND `payments` < `price_total`";
                    }
                }
                $_data = $this->db->query($query)->result_array();
            }
            if ($data['objects'] == 4) {
                if ($data['type_vouchers'] == 2) {
                    if (is_admin()) {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'status >' => 0)
                        );
                    } else {
                        $_data = get_table_where_select(
                            'tblexport_different.*,tblexport_different.subtotal as total',
                            'tblexport_different',
                            array('object' => $data['objects'], 'status >' => 0, 'staff_id' => get_staff_user_id())
                        );
                    }
                }
            }
            if ($data['type_vouchers'] == 9) {
                if (is_admin()) {
                    $_data = get_table_where_select(
                        'tbl_productions_orders_details.*,0 as total',
                        'tbl_productions_orders_details'
                    );
                } else {
                    $_data = get_table_where_select(
                        'tbl_productions_orders_details.*,0 as total',
                        'tbl_productions_orders_details',
                        array('created_by' => get_staff_user_id())
                    );
                }
            }
        }
        echo json_encode($_data);
        die();
    }

    public function other_payslips($id = '',$old = false)
    {
        $data['vouchers_id'] = array();
        if (!empty($id)) {
            $data['items'] = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
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
                $vouchers_id = get_table_where(
                    'tbl_productions_orders_details',
                    array('id' => $data['items']->vouchers_id)
                );
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
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['cost_parent'] = [];
        $data['costs_list'] = array();
        foreach ($data['costs'] as $key => $value) {
            if (empty($value['costs_parent'])) {
                $data['cost_parent'][$value['id']] = $value;
                $data['costs_list'][$value['id']]['name'] = $value['name'];
            } else {
                $data['costs_list'][$value['costs_parent']]['data'][] = $value;
            }
        }

        if($this->type == 'import'){
            $object_detault = 2;
            $type_voucher_default = 1;
        } else {
            $object_detault = 2;
            $type_voucher_default = 12;
        }

        $data['code'] = get_option('prefix_other_payslips') . '-' . Getinfocode('code_pck');
        $data['object_detault'] = $object_detault;
        $data['type_voucher_default'] = $type_voucher_default;
        $data['type'] = $this->type;
        if ($old == 'true'){
            $this->load->view('admin/other_payslips/other_payslips_old', $data);
        } else {
            $this->load->view('admin/other_payslips/other_payslips', $data);
        }
    }

    public function LoadListObjectByID($id)
    {
        $list = '';
        if ($id == 1) {
            $data = $this->clients_model->get();
            foreach ($data as $key => $value) {
                $list .= '<option value="' . $value['userid'] . '">' . $value['company'] . '</option>';
            }
        } elseif ($id == 2) {
            $data = $this->staff_model->get_all_staff();
            foreach ($data as $key => $value) {
                $list .= '<option value="' . $value['staffid'] . '">' . $value['fullname'] . '</option>';
            }
        } elseif ($id == 3) {
            $data = $this->suppliers_model->get();
            foreach ($data as $key => $value) {
                $list .= '<option value="' . $value['userid'] . '">' . $value['company'] . '</option>';
            }
        } else {
            $list = '';
        }
        print_r($list);
    }

    public function delete_v2($id = '')
    {
        $get_code = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        activity_log_v2(
            'other_payslips',
            'tblother_payslips',
            $id,
            $get_code->prefix . '-' . $get_code->code,
            'Xóa phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']',
            'delete'
        );
        $payslips = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        $response = $this->db->delete('tblother_payslips', array('id' => $id));
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        if ($response) {
            $alert_type = 'success';
            $message = _l('ch_delete');
            if (!empty($payslips)) {
                $datai = explode(',', $payslips->detai);
                foreach ($datai as $key => $value) {
                    if (!empty($value)) {
                        $_datai = explode('|', $value);
                        $import = get_table_where('tbl_orders', array('id' => $_datai[0]), '', 'row');
                        $total = $import->price_other_expenses_delivery - $_datai[1];
                        $this->db->update(
                            'tbl_orders',
                            array('price_other_expenses_delivery' => $total),
                            array('id' => $_datai[0])
                        );
                    }
                }
                if ($payslips->type_vouchers == 1) {
                    if (!empty($payslips->vouchers_id)) {
                        $other_details = get_table_where(
                            'tblother_payslips_detail',
                            array('other_pay' => $payslips->id),
                            '',
                            'result'
                        );
                        $array_purchase_order = [];
                        foreach ($other_details as $other_detail) {
                            $import = get_table_where(
                                'tblimport',
                                array('id' => $other_detail->id_import),
                                '',
                                'row_array'
                            );
                            $array_purchase_order[] = $import['id_order'];
                            $total = $import['amount_paid'] - $other_detail->total;
                            if (($total) == 0) {
                                $status = 0;
                            } else {
                                $status = 1;
                            }
                            $this->db->update(
                                'tblimport',
                                array('amount_paid' => $total, 'status_pay' => $status),
                                array('id' => $other_detail->id_import)
                            );
                        }
                        //delete detail
                        $this->db->where('other_pay', $payslips->id);
                        $this->db->delete('tblother_payslips_detail');
                        $this->db->where('id_other_payslips', $payslips->id);
                        $this->db->delete('tblother_payslips_import_items');

                        $this->db->where('other_payslip_id',$payslips->id);
                        $this->db->delete('tblother_payslip_cost');

                        $array_purchase_order = array_unique($array_purchase_order);
                        foreach ($array_purchase_order as $key => $value) {
                            $purchase_order = get_table_where('tblpurchase_order', array('id' => $value), '', 'row_array');
                            $import = get_table_where('tblimport', array('id_order' => $value));
                            $price_other_expenses = 0;
                            foreach ($import as $k => $v) {
                                $price_other_expenses += $v['amount_paid'];
                            }
                            $total = $price_other_expenses;
                            if (($total) > 0) {
                                $status = 1;
                            } else {
                                $status = 0;
                            }
                            $this->db->update(
                                'tblpurchase_order',
                                array('price_other_expenses' => $total, 'status_pay' => $status),
                                array('id' => $value)
                            );
                        }
                        //
                    }
                }
                if ($payslips->type_vouchers == 65) {
                    if (!empty($payslips->vouchers_id)) {
                        $other_details = get_table_where(
                            'tblother_payslips_detail',
                            array('other_pay' => $payslips->id),
                            '',
                            'result'
                        );
                        foreach ($other_details as $other_detail) {
                            $import = get_table_where(
                                'tbl_services',
                                array('id' => $other_detail->id_service),
                                '',
                                'row'
                            );
                            $total = $import->payment - $other_detail->total;
                            if (($total) == 0) {
                                $status = 0;
                            } else {
                                $status = 1;
                            }
                            $this->db->update(
                                'tbl_services',
                                array('payment' => $total, 'status_pay' => $status),
                                array('id' => $other_detail->id_service)
                            );
                        }
                        //delete detail
                        foreach ($other_details as $other_detail) {
                            $this->db->where('id', $other_detail->id);
                            $this->db->delete('tblother_payslips_detail');
                        }
                        //
                    }
                }
                if ($payslips->type_vouchers == 12) {
                    if (!empty($payslips->vouchers_id)) {
                        $other_details = get_table_where(
                            'tblother_payslips_detail',
                            array('other_pay' => $payslips->id),
                            '',
                            'result'
                        );
                        foreach ($other_details as $other_detail) {
                            $import = get_table_where(
                                'tblsuggestion',
                                array('id' => $other_detail->id_service),
                                '',
                                'row'
                            );
                            $total = $import->payments - $other_detail->total;
                            $this->db->update(
                                'tblsuggestion',
                                array('payments' => $total),
                                array('id' => $other_detail->id_service)
                            );
                        }
                        //delete detail
                        foreach ($other_details as $other_detail) {
                            $this->db->where('id', $other_detail->id);
                            $this->db->delete('tblother_payslips_detail');
                            update_plan_propose_internal_proposal($other_detail->id_service);
                        }
                        //

                        $this->db->where('other_payslip_id',$payslips->id);
                        $this->db->delete('tblother_payslip_cost');

                        $this->db->where('other_payslip_id',$payslips->id);
                        $this->db->delete('tblother_payslip_cost_detail');
                    }
                }
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function delete($id = '')
    {
        $get_code = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        if ($get_code->id_payment > 0) {
            echo json_encode(array(
                'alert_type' => 'danger',
                'message' => 'Phiếu đã tất toán, Không thể xóa'
            ));
            die;
        }
        activity_log_v2(
            'work_debt_buy',
            'tblother_payslips',
            $id,
            $get_code->prefix . '-' . $get_code->code,
            'Xóa phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
        );
        $payslips = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        $response = $this->db->delete('tblother_payslips', array('id' => $id));
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        if ($response) {
            $alert_type = 'success';
            $message = _l('ch_delete');
            if (!empty($payslips)) {
                if ($payslips->objects == 2) {
                    if ($payslips->type_vouchers == 1) {
                        if (!empty($payslips->vouchers_id)) {
                            $import = get_table_where(
                                'tblpurchase_order',
                                array('id' => $payslips->vouchers_id),
                                '',
                                'row'
                            );
                            $total = $import->price_other_expenses - $payslips->total;
                            if (($total + $import->amount_paid) == 0) {
                                $status = 0;
                            } else {
                                $status = 1;
                            }
                            $this->db->update(
                                'tblpurchase_order',
                                array('price_other_expenses' => $total, 'status_pay' => $status),
                                array('id' => $payslips->vouchers_id)
                            );
                        }
                    } elseif ($payslips->type_vouchers == 5) {
                        if (!empty($payslips->vouchers_id)) {
                            $import = get_table_where('tbl_orders', array('id' => $payslips->vouchers_id), '', 'row');
                            $total = $import->price_other_expenses_delivery - $payslips->total;
                            $this->db->update(
                                'tbl_orders',
                                array('price_other_expenses_delivery' => $total),
                                array('id' => $payslips->vouchers_id)
                            );
                        }
                    }
                }
                if ($payslips->type_vouchers == 12) {
                    if (!empty($payslips->vouchers_id)) {
                        $import = get_table_where('tblsuggestion', array('id' => $payslips->vouchers_id), '', 'row');
                        $total = $import->payments - $payslips->total;
                        $this->db->update(
                            'tblsuggestion',
                            array('payments' => $total),
                            array('id' => $payslips->vouchers_id)
                        );
                    }
                }
            }
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function update_status()
    {
        if (!has_permission('other_payslips', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $other_payslips = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
            if ($other_payslips->status == 1) {
                die;
            }
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $staff_id . ',' . $date;
            $data = array(
                'history_status' => $history_status,
                'status' => ($status + 1),
            );
            $success = $this->db->update('tblother_payslips', $data, array('id' => $id));
        }
        if ($success) {
            $get_code = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
            activity_log_v2(
                'work_debt_buy',
                'tblother_payslips',
                $id,
                $get_code->prefix . '-' . $get_code->code,
                'Cập nhật trạng thái phiếu chi khác [' . $get_code->prefix . '-' . $get_code->code . ']'
            );
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
        $dataMain = get_table_where('tblother_payslips', array('id' => $id), '', 'row');
        $table = '';
        $data->content = '';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;uppercase">' . (($dataMain->is_advance == 1) ? _l('ch_advance_in') : _l('PHIẾU CHI')) . '</span><br>';
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
                <span style="font-weight: bold;">' . _l('ch_units_in') . ': </span><span style="font-weight: bold;">' . $supplier->company . '</span><br>';
            $data->content .= '
                <span style="font-weight: bold;">' . _l('Tài khoản ngân hàng') . ': </span><span >' . $supplier->bank_account . '</span><br>';
            $data->content .= '
                <span style="font-weight: bold;">' . _l('tnh_name_account') . ': </span><span >' . $supplier->name_account . '</span><br>';
            $data->content .= '
                <span style="font-weight: bold;">' . _l('ch_address_bank') . ': </span><span >' . $supplier->address_bank . '</span><br>';
            $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_note_pay_slips') . ': </span><span>' . $dataMain->note . '</span><br>
            <span style="font-weight: bold;">' . _l('acs_sales_payment_modes_submenu') . ': </span><span>' . $pay_modes->name . '</span><br>
            <span style="font-weight: bold;">' . _l('expense_add_edit_amount') . ': </span><span>' . number_format($dataMain->total) . '</span><br>
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
        } else {
            if ($dataMain->objects == 1) {
                $client = get_table_where('tblclients', array('userid' => $dataMain->objects_id), '', 'row');
                $data->content .= '
                <span style="font-weight: bold;">' . _l('clients') . ': </span><span style="font-weight: bold;">' . $client->company . '</span><br/><br>';
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
        }
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

    public function exportExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $objects_idd = $this->input->post('objects_idd');
            $objects_ids = $this->input->post('objects_ids');
            $objects_texts = $this->input->post('objects_texts');
            $search_date = $this->input->post('search_date');
            $type_check = $this->input->post('type_check');


            if (!empty($search_date)) {
                $array_search_date = explode(' - ', $search_date);
                $start_date_search = $array_search_date[0];
                $end_date_search = $array_search_date[1];
            }

            $type = $this->input->post('type');
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

            $this->db->select(
                '
                tblother_payslips.id,
                tblother_payslips.date,
                tblother_payslips.prefix,
                tblother_payslips.code,
                tblother_payslips.objects,
                tblother_payslips.objects_id,
                tblother_payslips.type_vouchers,
                tblother_payslips.vouchers_id,
                tblpayment_modes.name as name_payment,
                tblcosts.name as name_cost,
                tblother_payslips.total,
                tblother_payslips.staff_id,
                tblother_payslips.note,
                tblother_payslips.id_costs,
                '
            );
            $this->db->from('tblother_payslips');
            $this->db->join('tblpayment_modes', 'tblpayment_modes.id = tblother_payslips.payment_modes');
            $this->db->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');

            $this->db->where('tblother_payslips.is_advance', 0);
            if ($type_check == 'import'){
                $this->db->where('tblother_payslips.type_vouchers = 1');
            } else {
                $this->db->where('tblother_payslips.type_vouchers != 1');
            }
            if ($objects_idd) {
                if (is_numeric($objects_idd)) {
                    $this->db->where('tblother_payslips.objects = ' . $objects_idd);
                }
            }
            if ($objects_ids) {
                if (is_numeric($objects_ids)) {
                    $this->db->where('tblother_payslips.objects_id = ' . $objects_ids);
                }
            }
            if ($objects_texts) {
                $this->db->where('tblother_payslips.objects_text LIKE "%' . $objects_texts . '%"');
            }
            if ($search_date) {
                $data_start = explode(' - ', $search_date);
                $this->db->where('tblother_payslips.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
            }
            $this->db->order_by('tblother_payslips.id desc');
            $dtOtherPayslip = $this->db->get()->result_array();


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
                (mb_strtoupper($type_check == 'import' ? lang('ch_other_payslips') : lang('dt_other_payslips1'), 'UTF-8'))
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $sttRow = 2;
            if (!empty($strDate)) {

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow, 'THỜI GIAN LẬP PHIẾU : ' . $strDate);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $sttRow . ':J' . $sttRow);
                $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:J$sttRow")->applyFromArray([
                    'font' => array(
                        'size' => 14,
                        'name' => 'Times New Roman'
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    ),
                ]);

                $sttRow++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow, 'Mã Chứng Từ');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow, 'Ngày Lập');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow, 'Nhóm Chi');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow, 'Mã ĐXNB');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow, 'Mã Chứng Từ');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow, 'Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'G' . $sttRow,
                'Thông Tin Đơn Vị Thụ Hưởng'
            )->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow, 'HTTT');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow, 'Loại Chi Phí Ngoài');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow, 'Tổng Số Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow, 'Ghi chú');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow, 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:L$sttRow")->applyFromArray([
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
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtOtherPayslip)) {
                foreach ($dtOtherPayslip as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['prefix'] . '-' . $value['code']);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dhau($value['date']));
                    $this->db->where(array('id' => $value['id_costs']));
                    $costs = $this->db->get('tblcosts')->row_array();
                    $costs_parent_name = '';
                    if (!empty($costs)) {
                        $this->db->where(array('id' => $costs['costs_parent']));
                        $costs_parent = $this->db->get('tblcosts')->row_array();
                        if (!empty($costs_parent)) {
                            $costs_parent_name = $costs_parent['name'];
                        }
                    }
                    $name_supplert = '';
                    if ($value['objects'] == 2) {
                        $this->db->where(array('id' => $value['objects_id']));
                        $suppler = $this->db->get('tblsuppliers')->row_array();
                        if (!empty($suppler)) {
                            $name_supplert = $suppler['company'];
                        }
                    }
                    $name_code = '';
                    if (!empty($value['vouchers_id'])) {
                        $_data = '';
                        if ($value['objects'] == 2) {
                            if ($value['type_vouchers'] == 1) {
                                if (!empty($value['vouchers_id'])) {
                                    $_data = '';
                                    $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $value['id']), '', 'result');
                                    if (!empty($other_items)) {
                                        foreach ($other_items as $key => $v) {
                                            $import = get_table_where('tblimport', array('id' => $v->id_import), '', 'row');
                                            if (!empty($import)) {
                                                $name_code .= $import->prefix . '-' . $import->code . ',';
                                            }
                                        }
                                    }
                                }
                            } else
                        if ($value['type_vouchers'] == 8) {
                                if (!empty($value['vouchers_id'])) {

                                    $return = get_table_where('tblreturn_suppliers', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code = $return->prefix . $return->code;
                                }
                            } else
                        if ($value['type_vouchers'] == 5) {
                                if (!empty($value['vouchers_id']) && ($value['vouchers_id'] != -1)) {

                                    $return = get_table_where('tbl_orders', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code = $return->reference_no;
                                }
                                if (!empty($value['vouchers_id']) && ($value['vouchers_id'] == -1)) {
                                    $detal = explode(',', $value['detai']);
                                    foreach ($detal as $key => $v) {
                                        if (!empty($v)) {
                                            $import = get_table_where('tbl_orders', array('id' => explode('|', $v)[0]), '', 'row');
                                            $name_code .=  $import->reference_no . ',';
                                        }
                                    }
                                }
                            } else
                        if ($value['type_vouchers'] == 2) {
                                if (!empty($value['vouchers_id'])) {

                                    $return = get_table_where('tblexport_different', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code = $return->prefix . $return->code;
                                }
                            }
                            if ($value['type_vouchers'] == 65) {
                                if (!empty($value['vouchers_id'])) {
                                    $_data = '';
                                    $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $value['id']), '', 'result');
                                    if (!empty($other_items)) {
                                        foreach ($other_items as $key => $v) {
                                            $import = get_table_where('tbl_services', array('id' => $v->id_service), '', 'row');
                                            if (!empty($import)) {
                                                $name_code .= $import->prefix . '-' . $import->code . ',';
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($value['objects'] == 1) {
                            if ($value['type_vouchers'] == 1) {
                                if (!empty($value['objects_id']) && ($value['vouchers_id'] != 0)) {
                                    $import = get_table_where('tblpurchase_order', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code = $import->prefix . '-' . $import->code;
                                }
                            } else
                        if ($value['type_vouchers'] == 8) {
                                if (!empty($value['vouchers_id'])) {

                                    $return = get_table_where('tblreturn_suppliers', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code = $return->prefix . $return->code;
                                }
                            } else
                        if ($value['type_vouchers'] == 5) {
                                if (!empty($value['vouchers_id'])) {

                                    $return = get_table_where('tbl_orders', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code =  $return->reference_no;
                                }
                            } else
                        if ($value['type_vouchers'] == 2) {
                                if (!empty($value['vouchers_id'])) {

                                    $return = get_table_where('tblexport_different', array('id' => $value['vouchers_id']), '', 'row');
                                    $name_code = $return->prefix . '-' . $return->code;
                                }
                            }
                        }
                        if ($value['type_vouchers'] == 9) {
                            if (!empty($value['vouchers_id'])) {

                                $productions_orders_details = get_table_where('tbl_productions_orders_details', array('id' => $value['vouchers_id']), '', 'row');
                                if (!empty($productions_orders_details)) {
                                    $name_code =  $productions_orders_details->reference_no;
                                } else {
                                    $name_code = '';
                                }
                            }
                        }
                        if ($value['type_vouchers'] == 12) {
                            if (!empty($value['vouchers_id'])) {
                                $name_code = '';
                                $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $value['id']), '', 'result');
                                if (!empty($other_items)) {
                                    foreach ($other_items as $key => $v) {
                                        $import = get_table_where('tblsuggestion', array('id' => $v->id_service), '', 'row');
                                        if (!empty($import)) {
                                            $name_code .= $import->code . ',';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", $costs_parent_name);
                    $_data = '';
                    $internal_proposal_array = [];
                    $other_items = get_table_where('tblother_payslips_detail', array('other_pay' => $value['id']), '', 'result');
                    if (!empty($other_items)) {
                        foreach ($other_items as $k => $v) {
                            if ($v->id_service) {
                                $import = get_table_where('tblsuggestion', array('id' => $v->id_service), '', 'row_array', '', 'tblsuggestion.*,(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id_suggestion = tblsuggestion.id LIMIT 1) as internal_proposal,(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id = tblsuggestion.id_internal_proposal LIMIT 1) as internal_proposalv2');
                                if (!empty($import)) {
                                    if (!empty($import['internal_proposal'])) {
                                        $internal_proposal = explode('__', $import['internal_proposal']);
                                        if (!in_array($internal_proposal[0], $internal_proposal_array)) {
                                            $internal_proposal_array[] = $internal_proposal[0];
                                            $_data .= $internal_proposal[1] . ',';
                                        }
                                    }
                                    if (!empty($import['internal_proposalv2'])) {
                                        $internal_proposalv2 = explode('__', $import['internal_proposalv2']);
                                        if (!in_array($internal_proposalv2[0], $internal_proposal_array)) {
                                            $internal_proposal_array[] = $internal_proposalv2[0];
                                            $_data .= $internal_proposalv2[1] . ',';
                                        }
                                    }
                                }
                            }
                            if ($v->id_import) {
                                $import = get_table_where('tblimport', array('id' => $v->id_import), '', 'row_array');
                                if (!empty($import)) {
                                    $purchase_order = get_table_where('tblpurchase_order', array('id' => $import['id_order']), '', 'row_array', '', 'tblpurchase_order.*,(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id_suggestion = tblpurchase_order.id LIMIT 1) as internal_proposal,(SELECT CONCAT(tblinternal_proposal.id, "__", tblinternal_proposal.code) FROM tblinternal_proposal WHERE tblinternal_proposal.id = tblpurchase_order.id_internal_proposal LIMIT 1) as internal_proposalv2');
                                    if (!empty($purchase_order)) {
                                        // if (!empty($purchase_order['internal_proposal'])) {
                                        //     $internal_proposal = explode('__', $purchase_order['internal_proposal']);
                                        //     if (!in_array($internal_proposal[0], $internal_proposal_array)) {
                                        //         $internal_proposal_array[] = $internal_proposal[0];
                                        //         $_data .= $internal_proposal[1] . ',';
                                        //     }
                                        // }
                                        if (!empty($purchase_order['internal_proposalv2'])) {
                                            $internal_proposalv2 = explode('__', $purchase_order['internal_proposalv2']);
                                            if (!in_array($internal_proposalv2[0], $internal_proposal_array)) {
                                                $internal_proposal_array[] = $internal_proposalv2[0];
                                                $_data .= $internal_proposalv2[1] . ',';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $_data);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $name_code);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $name_supplert);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", '');
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "H$rowBegin",
                        $value['name_payment']
                    )->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "I$rowBegin",
                        $value['name_cost']
                    )->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "J$rowBegin",
                        $value['total']
                    )->getStyle("J$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['total']));
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['note']);

                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", '');

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:L$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:J$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                            'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                        ),
                    ]);
                }
            }
            if ($type_check == 'import'){
                $filename = lang('chi_dich_vu_po') . '.xls';
            } else {
                $filename = lang('chi_dich_vu_khong_po') . '.xls';
            }
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
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

    public function other_payslip_manage()
    {
        if (!has_permission('other_payslips', '', 'view') && !has_permission('other_payslips', '', 'view_own')) {
            access_denied('other_payslips');
        }
        $data['title'] = _l('dt_other_payslips');
        $this->load->view('admin/other_payslips/other_payslip_manage', $data);
    }

    public function getOtherPayslipManager()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $objects_idd = $this->input->post('objects_idd');
        $objects_ids = $this->input->post('objects_ids');
        $objects_texts = $this->input->post('objects_texts');
        $search_date = $this->input->post('search_date');

        $aColumns = [
            'tblother_payslips.id as id',
            'tblother_payslips.date as date',
            'tblother_payslips.code as code',
            'tblother_payslips.type_vouchers as type_vouchers',
            'tblother_payslips.vouchers_id as vouchers_id',
            'tblpayment_modes.name as name_payment',
            'tblcosts.name as name_cost',
            'tblother_payslips.total as total',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblother_payslips';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblpayment_modes ON tblpayment_modes.id = tblother_payslips.payment_modes',
            'INNER JOIN tblcosts ON tblcosts.id = tblother_payslips.id_costs',
        ];

        array_push($where, 'AND tblother_payslips.type_manager = 1');

        if ($objects_idd) {
            if (is_numeric($objects_idd)) {
                array_push($where, 'AND tblother_payslips.objects = ' . $objects_idd);
            }
        }
        if ($objects_ids) {
            if (is_numeric($objects_ids)) {
                array_push($where, 'AND tblother_payslips.objects_id = ' . $objects_ids);
            }
        }
        if ($objects_texts) {
            array_push($where, 'AND tblother_payslips.objects_text LIKE "%' . $objects_texts . '%"');
        }
        if ($search_date) {
            $data_start = explode(' - ', $search_date);
            array_push($where, 'AND tblother_payslips.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblother_payslips.prefix as prefix,
            tblother_payslips.objects as objects,
            tblother_payslips.staff_id as staff_id,
            tblother_payslips.note as note,
            tblother_payslips.objects_id as objects_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {

            $row = array();
            $row[] = '<div class="text-left" style="width: 100px">' . $aRow['prefix'] . '-' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 140px"></div>';
            $row[] = '<div class="text-left" style="width: 140px"></div>';
            $row[] = '<div class="text-left" style="width: 140px"></div>';
            $row[] = '<div class="text-left" style="width: 140px"></div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['name_payment'] . '</div>';
            $row[] = '<div class="text-left" style="width: 140px">' . $aRow['name_cost'] . '</div>';
            $row[] = '<div class="text-right" style="width: 100px">' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-center" style="width: 100px"></div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function exportExcelManager()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $objects_idd = $this->input->post('objects_idd');
            $objects_ids = $this->input->post('objects_ids');
            $objects_texts = $this->input->post('objects_texts');
            $search_date = $this->input->post('search_date');
            $type = $this->input->post('type');
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

            $this->db->select(
                '
                tblother_payslips.id,
                tblother_payslips.date,
                tblother_payslips.prefix,
                tblother_payslips.code,
                tblother_payslips.objects,
                tblother_payslips.objects_id,
                tblother_payslips.type_vouchers,
                tblother_payslips.vouchers_id,
                tblpayment_modes.name as name_payment,
                tblcosts.name as name_cost,
                tblother_payslips.total,
                tblother_payslips.staff_id,
                tblother_payslips.note,
                '
            );
            $this->db->from('tblother_payslips');
            $this->db->join('tblpayment_modes', 'tblpayment_modes.id = tblother_payslips.payment_modes');
            $this->db->join('tblcosts', 'tblcosts.id = tblother_payslips.id_costs');

            $this->db->where('tblother_payslips.type_manager', $type);

            if ($objects_idd) {
                if (is_numeric($objects_idd)) {
                    $this->db->where('tblother_payslips.objects = ' . $objects_idd);
                }
            }
            if ($objects_ids) {
                if (is_numeric($objects_ids)) {
                    $this->db->where('tblother_payslips.objects_id = ' . $objects_ids);
                }
            }
            if ($objects_texts) {
                $this->db->where('tblother_payslips.objects_text LIKE "%' . $objects_texts . '%"');
            }
            if ($search_date) {
                $data_start = explode(' - ', $search_date);
                $this->db->where('tblother_payslips.date BETWEEN "' . to_sql_date($data_start[0]) . '" and "' . to_sql_date($data_start[1]) . '"');
            }

            $dtOtherPayslip = $this->db->get()->result_array();


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
                ('PHIẾU CHI PHÍ QUẢN LÝ')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Mã Chứng Từ');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Ngày Lập');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Nhóm Chi');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Chi Tiết');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue(
                'F' . $sttRow . '',
                'Thông Tin Đơn Vị Thụ Hưởng'
            )->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'HTTT');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Loại Chi Phí Quản Lý');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Tổng Số Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:J$sttRow")->applyFromArray([
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
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtOtherPayslip)) {
                foreach ($dtOtherPayslip as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['prefix'] . '-' . $value['code']);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dhau($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", '');
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", '');
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", '');
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", '');
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "G$rowBegin",
                        $value['name_payment']
                    )->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "H$rowBegin",
                        $value['name_cost']
                    )->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "I$rowBegin",
                        $value['total']
                    )->getStyle("I$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['total']));
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", '');

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:J$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                            'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_chi_phi_quan_ly') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
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
    public function getImportCodeById($id)
    {
        $result = get_table_where('tblimport', ['id' => $id], '', 'row', '', 'CONCAT(prefix, "-", code) as reference');
        if (empty($result)) {
            return '';
        } else {
            return $result->reference;
        }
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
        $id_costs = $this->input->post('id_costs');
        $category_id = 0;
        $dtCost = get_table_where('tblcosts',['id' => $id_costs],'','row_array');
        if (!empty($dtCost)){
            if ($dtCost['type_cost'] == 1){
                $category_id = $dtCost['object_id'];
            }
        }
        $arr_items = array();
        foreach ($arr_import_id as $key => $import_id) {

            $items = $this->import_model->get_items_import($import_id,$category_id);
//             var_dump($items);die;
            $importCode = $this->getImportCodeById($import_id);
            $arr_items[$key]['import_code'] = $importCode;
            $itemsTemp = array();
            foreach ($items as $key2 => $item) {
                // var_dump($this->isInvoice($item['id']));
                if ($this->isOtherPay($item['id'])) {
                    continue;
                }
                $itemsTemp[$key2]['import_item_id'] = $item['id_import'] . '__' . $item['id'];
                $itemsTemp[$key2]['import_code'] = $importCode;
                $itemsTemp[$key2]['product_id'] = $item['product_id'];
                $itemsTemp[$key2]['code_item'] = $item['code_item'];
                $itemsTemp[$key2]['name_item'] = $item['name_item'];
                $itemsTemp[$key2]['price'] = $item['amount'];
            }
            if (!empty($itemsTemp)) {
                $arr_items[$key]['items'] = $itemsTemp;
            }
        }
        // var_dump($arr_items);die;
        $data['items'] = $arr_items;
        echo json_encode($data);
    }
    public function isOtherPay($import_item_id)
    {
        $this->db->from('tblother_payslips_import_items');
        $this->db->where('tblother_payslips_import_items.id_import_items', $import_item_id);
        $this->db->limit(1);
        $reslut = $this->db->get()->num_rows();
        if (empty($reslut)) {
            return false;
        } else {
            return true;
        }
    }
    public function checkPayImport($import_id)
    {
        $this->db->select('tblimport_items.id');
        $this->db->from('tblimport_items');
        $this->db->where('tblimport_items.id_import', $import_id);
        $this->db->where('tblimport_items.id NOT IN (SELECT id_import_items FROM tblother_payslips_import_items)');
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
    function updatepurchase_import()
    {
        $this->db->select('tblpurchase_order.*');
        $this->db->from('tblpurchase_order');
        $this->db->where('tblpurchase_order.status_pay', 2);
        $reslut = $this->db->get()->result_array();
        foreach ($reslut as $key => $value) {
            $this->db->select('tblimport.*');
            $this->db->from('tblimport');
            $this->db->where('tblimport.id_order', $value['id']);
            $import = $this->db->get()->result_array();
            foreach ($import as $k => $v) {
                $this->db->select('tblimport_items.*');
                $this->db->from('tblimport_items');
                $this->db->where('tblimport_items.id_import', $v['id']);
                $import_items = $this->db->get()->result_array();
                $total_import = 0;
                foreach ($import_items as $kk => $vv) {
                    $detail = array();
                    $detail['id_other_payslips'] = -1;
                    $detail['id_import'] = $v['id'];
                    $detail['id_import_items'] = $vv['id'];
                    $this->db->insert('tblother_payslips_import_items', $detail);
                    $total_import += $vv['amount'];
                }
                $this->db->update(
                    'tblimport',
                    array('amount_paid' => ($total_import), 'status_pay' => 2),
                    array('id' => $v['id'])
                );
            }
        }
    }
    function getbank()
    {
        $objects_id = $this->input->post('objects_id');
        $objects = $this->input->post('objects');
        $data = [];
        $data['account_number'] = '';
        $data['account_name'] = '';
        $data['account_address'] = '';
        if (!empty($objects_id) && !empty($objects)) {
            if ($objects == 1) {
                $this->db->select('tblclients.bank_account as account_number,tblclients.name_account as account_name,tblclients.address_bank as account_address');
                $this->db->where('userid', $objects_id);
                $data = $this->db->get('tblclients')->row_array();
            }
            if ($objects == 2) {
                $this->db->select('tblsuppliers.bank_account as account_number,tblsuppliers.name_account as account_name,tblsuppliers.address_bank as account_address');
                $this->db->where('id', $objects_id);
                $data = $this->db->get('tblsuppliers')->row_array();
            }
            if ($objects == 3) {
                $this->db->select('tblstaff.account_name as account_number,tblstaff.bank as account_name,tblstaff.branch_bank as account_address');
                $this->db->where('staffid', $objects_id);
                $data = $this->db->get('tblstaff')->row_array();
            }
        }
        echo json_encode($data);
    }

    public function loadItemSuggest(){
        $arr_voucher_id = !empty($this->input->post('arr_voucher_id')) ? $this->input->post('arr_voucher_id') : [];
        $dtItems = [];
        if (!empty($arr_voucher_id)) {
            foreach ($arr_voucher_id as $key => $value) {
                $value = !empty($value) ? $value : 0;
                $query = "
                    SELECT tblinternal_proposal.category_recommended_id,tblinternal_proposal.suggest_id,tblinternal_proposal.id as internal_proposal_id,price_total,tblsuggestion.code as code
                    FROM `tblsuggestion` 
                    JOIN tblinternal_proposal ON tblinternal_proposal.id = tblsuggestion.id_internal_proposal
                    WHERE tblsuggestion.id = $value
                    
                    UNION ALL
                    
                    SELECT tblinternal_proposal.category_recommended_id,tblinternal_proposal.suggest_id,tblinternal_proposal.id as internal_proposal_id,price_total,tblsuggestion.code as code
                    FROM `tblsuggestion`
                    JOIN tblinternal_proposal ON tblinternal_proposal.id_suggestion = tblsuggestion.id
                    WHERE tblsuggestion.id = $value
                ";
                $dtData = $this->db->query($query)->row_array();
                if(!empty($dtData)) {
                    $category_recommended_id = $dtData['category_recommended_id'];
                    $internal_proposal_id = $dtData['internal_proposal_id'];
                    if ($category_recommended_id == 40) {

                        $tb_tamp = "
                            (SELECT COALESCE(SUM(total),0) as payment
                            FROM tblother_payslip_cost_detail
                            WHERE tblother_payslip_cost_detail.object_id = tbl_request_repair.id
                            AND tblother_payslip_cost_detail.table_object = 'tbl_request_repair')
                        ";

                        $this->db->select('tblcosts.id as id_cost,tblcosts.name as name_cost,tbl_machines.name as name_machines,tbl_request_repair.amount as total,tbl_request_repair.id as object_id,'.$tb_tamp.' as payment');
                        $this->db->from('tbl_request_repair');
                        $this->db->join('tblcosts', 'tblcosts.id = tbl_request_repair.cost_id');
                        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_repair.machines_id');
                        $this->db->where('tbl_request_repair.id', $dtData['suggest_id']);
                        $dtCost = $this->db->get()->row_array();
                        if (!empty($dtCost)) {
                            $dtItems[] = [
                                'id' => $dtCost['id_cost'],
                                'name' => $dtCost['name_machines'],
                                'name_cost' => $dtCost['name_cost'],
                                'code' => $dtData['code'],
                                'total' => $dtCost['total'] - $dtCost['payment'],
                                'object_id' => $dtCost['object_id'],
                                'table_object' => 'tbl_request_repair'
                            ];
                        }
                    } elseif ($category_recommended_id == 41) {

                        $tb_tamp = "
                            (SELECT COALESCE(SUM(total),0) as payment
                            FROM tblother_payslip_cost_detail
                            WHERE tblother_payslip_cost_detail.object_id = tbl_suggest_payslips_items.id
                            AND tblother_payslip_cost_detail.table_object = 'tbl_suggest_payslips_items')
                        ";

                        $this->db->select('tblcosts.id as id_cost,tblcosts.name as name_cost,tbl_category_payslip.name as name_payslip, tbl_suggest_payslips_items.total as total,tbl_suggest_payslips_items.id as object_id,'.$tb_tamp.' as payment');
                        $this->db->from('tbl_suggest_muti_id');
                        $this->db->join('tbl_suggest_payslips',
                            'tbl_suggest_payslips.id = tbl_suggest_muti_id.suggest_id');
                        $this->db->join('tbl_suggest_payslips_items',
                            'tbl_suggest_payslips_items.suggest_payslips_id = tbl_suggest_payslips.id');
                        $this->db->join('tblcosts', 'tblcosts.id = tbl_suggest_payslips_items.cost_id');
                        $this->db->join('tbl_category_payslip', 'tbl_category_payslip.id = tbl_suggest_payslips_items.category_payslip');
                        $this->db->where('tbl_suggest_muti_id.id_internal_proposal', $internal_proposal_id);
                        $dtCost = $this->db->get()->result_array();
                        if(!empty($dtCost)) {
                            foreach ($dtCost as $kk => $vv) {
                                $dtItems[] = [
                                    'id' => $vv['id_cost'],
                                    'name' => $vv['name_payslip'],
                                    'name_cost' => $vv['name_cost'],
                                    'code' => $dtData['code'],
                                    'total' => $vv['total'] - $vv['payment'],
                                    'object_id' => $vv['object_id'],
                                    'table_object' => 'tbl_suggest_payslips_items'
                                ];
                            }
                        }
                    }
                }
            }
        }
        $data['dtItems'] = $dtItems;
        echo json_encode($data);
    }
}
