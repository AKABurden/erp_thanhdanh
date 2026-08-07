<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchases extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchases_model');
        $this->load->model('invoice_items_model');
        $this->load->model('manufactures_model');
    }

    function get_local_ipv4()
    {
        $out = explode(PHP_EOL, shell_exec("/sbin/ifconfig"));
        $local_addrs = array();
        $ifname = 'unknown';
        foreach ($out as $str) {
            $matches = array();
            if (preg_match('/^([a-z0-9]+)(:\d{1,2})?(\s)+Link/', $str, $matches)) {
                $ifname = $matches[1];
                if (strlen($matches[2]) > 0) {
                    $ifname .= $matches[2];
                }
            } elseif (preg_match(
                '/inet addr:((?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3})\s/',
                $str,
                $matches
            )) {
                $local_addrs[$ifname] = $matches[1];
            }
        }
        return $local_addrs;
    }

    public function index()
    {
        // var_dump(get_debt_client(1));die;
        // var_dump(test_purchase_import(105));die;
        if (!has_permission('purchases', '', 'view') && !has_permission('purchases', '', 'view_own')) {
            access_denied('purchases');
        }
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();

        $data['title'] = _l('ch_purchases');

        if (is_mobile()) {
            $this->load->view('admin/themes_mobile/manage_purchase', $data);
        } else {
            $this->load->view('admin/purchases/manage', $data);
        }
    }

    public function get_id_purchases()
    {
        $data = $this->input->post();
        $check = false;
        if (has_permission('purchases', '', 'view_own') && !is_admin()) {
            $check = true;
        }
        if ($data['id'] == 2) {
            $this->db->select('tblpurchases.*');
            $this->db->where('tblpurchases.status', 3);
            $this->db->where('tblpurchases.id_order', 0);
            $this->db->where('tblpurchases.type_plan', 0);
            $this->db->where('tblrfq_ask_price.id is NULL');
            $this->db->where('tblsupplier_quotes.id is NULL');
            if ($check) {
                $this->db->where('tblpurchases.staff_create', get_staff_user_id());
            }
            $this->db->join('tblrfq_ask_price', 'tblrfq_ask_price.id_purchases = tblpurchases.id', 'left');
            $this->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id_purchases = tblpurchases.id', 'left');
            $purchases = $this->db->get('tblpurchases')->result_array();
        } else {
            $this->db->select('tblpurchases.*');
            $this->db->where('tblpurchases.status', 3);
            $this->db->where('id_order', 0);
            $this->db->where('tblpurchases.type_plan !=', 0);
            $this->db->where('tblrfq_ask_price.id is NULL');
            $this->db->where('tblsupplier_quotes.id is NULL');
            if ($check) {
                $this->db->where('tblpurchases.staff_create', get_staff_user_id());
            }
            $this->db->join('tblrfq_ask_price', 'tblrfq_ask_price.id_purchases = tblpurchases.id', 'left');
            $this->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id_purchases = tblpurchases.id', 'left');
            $purchases = $this->db->get('tblpurchases')->result_array();
        }
        $purchasess = array();
        foreach ($purchases as $key => $value) {
            $value['date'] = _dt($value['date']);
            $purchasess[$key] = $value;
        }
        echo json_encode($purchasess);
        die;
        // if(empty($aRow['process'])&&($aRow['tblpurchases.status'] == 3)&&(empty($aRow['id_order'])))
        // {
        // $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['tblpurchases.id'] . '"><label></label></div>';
        // }else
        // {
        // $processas = explode(',', $aRow['process']);
        //     if($processas[0] == 3&&($aRow['tblpurchases.status'] == 3&&(empty($aRow['id_order']))))
        //     {
        //     $purchase = get_items_purchase_new($aRow['tblpurchases.id']);
        //     if($purchase > 0){
        //     $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['tblpurchases.id'] . '"><label></label></div>';
        //     }else
        //     {
        //     $row[] ='';
        //     }
        //     }else
        //     {
        //     $row[] ='';
        //     }
        // }
    }

    public function note_cancel($id = '')
    {
        $data = $this->input->post();
        $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
        $staff_id = get_staff_user_id();
        $date = date('Y-m-d H:i:s');
        $history_status = $purchases->history_status;
        $history_status .= '|' . $staff_id . ',' . $date;
        $in = array(
            'history_status' => $history_status,
            'note_cancel' => $data['note_cancel'],
            'status' => 4,
        );
        $this->db->where('id', $id);
        $result = $this->db->update('tblpurchases', $in);
        if ($result) {
            $message = _l('updated_successfuly');
            $alert_type = 'success';
            log_activity('Purchases cancel [ID: ' . $id . ']');

            $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
            activity_log_v2(
                'purchase',
                'tblpurchases',
                $id,
                $get_code->prefix . $get_code->code,
                'Kết thúc phiếu yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']'
            );
        }
        echo json_encode(array(
            'success' => $result,
            'message' => $message,
            'alert_type' => $alert_type
        ));
        die;
    }

    public function no_note_cancel($id = '')
    {
        $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
        $staff_id = get_staff_user_id();
        $date = date('Y-m-d H:i:s');
        $history_no_note_cancel = $staff_id . ',' . $date;
        $history_statuss = '';
        $history_status = $purchases->history_status;
        $history = explode('|', $history_status);
        foreach ($history as $key => $value) {
            if ($key > 0) {
                if ($key < 3) {
                    $history_statuss .= '|' . $value;
                }
            }
        }
        $history_status = trim($history_status, '|');
        $in = array(
            'history_status' => $history_statuss,
            'note_cancel' => '',
            'status' => 3,
            'history_no_note_cancel' => $history_no_note_cancel,
        );
        $this->db->where('id', $id);
        $result = $this->db->update('tblpurchases', $in);
        if ($result) {
            $message = _l('updated_successfuly');
            $alert_type = 'success';
            log_activity('Cancel purchases cancel [ID: ' . $id . ']');

            $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
            activity_log_v2(
                'purchase',
                'tblpurchases',
                $id,
                $get_code->prefix . $get_code->code,
                'Cập nhật trạng thái yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']'
            );
        }
        echo json_encode(array(
            'success' => $result,
            'message' => $message,
            'alert_type' => $alert_type
        ));
        die;
    }

    public function table()
    {
        $this->app->get_table_data('purchases');
    }

    public function detail($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('purchases', '', 'create')) {
                    access_denied('purchases');
                }

                $data = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {

                    $id = $this->purchases_model->add($data);
                }

                if ($id) {
                    $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
                    activity_log_v2(
                        'purchase',
                        'tblpurchases',
                        $id,
                        $get_code->prefix . $get_code->code,
                        'Thêm mới yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']'
                    );
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('purchases'));
                }
            } else {
                if (!has_permission('purchases', '', 'edit')) {
                    access_denied('purchases');
                }
                $success = $this->purchases_model->update($this->input->post(), $id);
                if ($success == true) {
                    $this->load->model('misc_model');
                    $this->misc_model->changeRowNew_model('tblpurchases', $id);

                    $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
                    activity_log_v2(
                        'purchase',
                        'tblpurchases',
                        $id,
                        $get_code->prefix . $get_code->code,
                        'Cập nhật yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']'
                    );
                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('purchases/detail/' . $id));
            }
        }
        if ($id == '') {
            if (!has_permission('purchases', '', 'create')) {
                access_denied('purchases');
            }
            $title = _l('add_new', _l('ch_purchases'));
        } else {
            if (!has_permission('purchases', '', 'edit')) {
                access_denied('purchases');
            }
            $title = _l('edit', _l('ch_purchases'));
            $data['purchase'] = $this->purchases_model->get($id);
        }
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchases/detail', $data);
    }

    public function add_rfq($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if (empty($data['id'])) {
                unset($data['id']);
                $success = $this->purchases_model->add_rfq($data, $id);
                if ($success) {
                    create_purchase_to_rfq($success);
                    $get_code = get_table_where('tblrfq_ask_price', array('id' => $success), '', 'row');
                    activity_log_v2(
                        'purchase',
                        'tblrfq_ask_price',
                        $success,
                        $get_code->prefix . '-' . $get_code->code,
                        'Thêm mới phiếu hỏi giá nhà cung cấp [' . $get_code->prefix . '-' . $get_code->code . ']'
                    );
                    $success = true;
                    $message = _l('ch_added_successfuly', _l('ch_evaluation_criteria'));
                } else {
                    $success = false;
                    $message = _l('ch_added_successfuly_not', _l('ch_evaluation_criteria'));
                }
            } else {
                $success = $this->purchases_model->update_rfq($data, $id);

                $get_code = get_table_where('tblrfq_ask_price', array('id' => $data['id']), '', 'row');
                activity_log_v2(
                    'purchase',
                    'tblrfq_ask_price',
                    $data['id'],
                    $get_code->prefix . '-' . $get_code->code,
                    'Cập nhật phiếu hỏi giá nhà cung cấp [' . $get_code->prefix . '-' . $get_code->code . ']'
                );
                $success = true;
                $message = _l('updated_successfully', _l('ch_evaluation_criteria'));
            }
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function views_purchases($id = '')
    {
        $data['purchase'] = $this->purchases_model->get($id);
        $data['dataLog'] = get_table_where(
            'tblactivity_log_v2',
            array('table_obj' => 'tblpurchases', 'id_obj' => $id),
            'id DESC'
        );

        $this->db->where('id_purchases', $id);
        $this->db->order_by('date_create', 'desc');
        $data['feedback'] = $this->db->get('tblpurchases_feedback')->result();
        foreach ($data['feedback'] as $key => $value) {
            $this->db->where('rel_id', $value->id);
            $this->db->where('rel_type', 'feedback_p');
            $data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
        }

        $this->load->view('admin/purchases/view_modal', $data);
    }

    public function test_quotes_suppliers()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $quotes_suppliers = get_table_where(
                'tblsupplier_quotes',
                array('id_ask_price' => $data['id'], 'suppliers_id' => $data['supplier_id']),
                '',
                'row'
            );
            if ($quotes_suppliers) {
                $ktr = true;
            } else {
                $ktr = false;
            }
        } else {
            $ktr = false;
        }
        echo json_encode($ktr);
    }

    public function rfq_modal($id = '', $type = 1)
    {
        $data['purchasess'] = get_table_where('tblpurchases', array('id' => $id), '', 'row');
        $data['id'] = $id;
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $ktr = get_table_where('tblrfq_ask_price', array('id_purchases' => $id), '', 'row');
        $id_ask = '';
        if (!empty($ktr)) {
            $id_ask = $ktr->id;
        }
        $data['purchase'] = $this->purchases_model->get_items_purchases_v2($id, $id_ask);
        if ($ktr) {
            $data['ask_price'] = $this->purchases_model->get_ask_price($id);
            $suppliers_id = explode(',', $data['ask_price']->suppliers_id);
            foreach ($suppliers_id as $key => $value) {
                $data['suppliers_id'][$key]['id'] = $value;
                $data['suppliers_id'][$key]['company'] = get_table_where(
                    'tblsuppliers',
                    array('id' => $value),
                    '',
                    'row'
                )->company;
            }
            $data['suppliers_id'] = array_reverse($data['suppliers_id'], true);
            $data['targert'] = $this->purchases_model->get_targert($data['ask_price']->id);
            $supplier_quotes = get_table_where('tblsupplier_quotes', array('id_ask_price' => $ktr->id));
            $_data['supplier_quotes'] = null;
            if (!empty($supplier_quotes)) {
                $dem_temp = 0;
                foreach ($data['purchase'] as $key => $value) {
                    $_data['dataMain'][$dem_temp]['id_items'] = $value['product_id'];
                    $_data['dataMain'][$dem_temp]['name'] = $value['name_item'];
                    $_data['dataMain'][$dem_temp]['code'] = $value['code_item'];
                    $_data['dataMain'][$dem_temp]['type'] = $value['type'];
                    $dem_temp_sub = 0;
                    foreach ($supplier_quotes as $k => $v) {
                        $sub = get_table_where('tblsupplier_quote_items', array(
                            'id_supplier_quotes' => $v['id'],
                            'product_id' => $value['product_id'],
                            'type' => $value['type']
                        ), '', 'row');
                        // echo '<pre>';
                        // var_dump($v['id'],$value['id'],$value['type']);
                        if (!empty($sub)) {
                            $suppliers = get_table_where('tblsuppliers', array('id' => $v['suppliers_id']), '', 'row');
                            $_data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['name_supplier'] = $suppliers->company;
                            $_data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['unit_cost'] = $sub->unit_cost;
                            $_data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['subtotal'] = $sub->subtotal;
                            $_data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['quantity'] = $sub->quantity;
                            $dem_temp_sub++;
                        }
                    }
                    usort($_data['dataMain'][$dem_temp]['sub'], ch_make_cmp(['unit_cost' => "asc"]));
                    $dem_temp++;
                }
            }
            $data['supplier_quotes'] = $_data['dataMain'];
        }
        $data['type_items'] = get_table_where('tbltype_items', array('active' => 1));
        $data['type'] = $type;

        //hoàng crm bổ xung
        // $dem_temp = 0;
        // $main = get_table_where('tblevaluation_criteria');
        // foreach ($main as $key => $value) {
        //     $data['dataMain'][$dem_temp]['id_main'] = $value['id'];
        //     $data['dataMain'][$dem_temp]['main'] = $value['name'];
        //     $sub = get_table_where('tblevaluation_criteria_children',array('id_evaluation'=>$value['id']));
        //     $dem_temp_sub = 0;
        //     foreach ($sub as $keySub => $valueSub) {
        //         $data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['id_sub'] = $valueSub['id'];
        //         $data['dataMain'][$dem_temp]['sub'][$dem_temp_sub]['name'] = $valueSub['name_children'];
        //         $dem_temp_sub++;
        //     }
        //     $dem_temp++;
        // }
        //end
        $this->load->view('admin/purchases/rfq_modal', $data);
    }

    public function get_items_supplier()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $mainstream_goods = $this->purchases_model->get_items_supplier($data['id'], $data['supplier_id']);
            foreach ($mainstream_goods as $key => $value) {
                $mainstream_goods[$key]['avatar'] = (file_exists($value['avatar']) ? base_url($value['avatar']) : (file_exists('uploads/materials/' . $value['avatar']) ? base_url('uploads/materials/' . $value['avatar']) : (file_exists('uploads/products/' . $value['avatar']) ? base_url('uploads/products/' . $value['avatar']) : base_url('assets/images/preview-not-available.jpg'))));
            }

            echo json_encode($mainstream_goods);
        }
    }

    public function update_status($value = '')
    {
        if (!has_permission('purchases', '', 'approve')) {
            echo json_encode(array(
                'success' => false,
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            if ($status == 1) {
                approve_purchase_app1($id);
            }
            if ($status == 2) {
                approve_purchase_app2($id);
            }
            $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $purchases->history_status;
            $history_status .= '|' . $staff_id . ',' . $date;
            $data = array(
                'history_status' => $history_status,
                'status' => ($status + 1),
            );

            //			$title_noti = 'ĐIỂM THƯỞNG';
            //			$content_noti = 'Bạn vừa được cộng <span style="color: red">'.$point_order.'</span> điểm cho đơn hàng '.$reference_no.'.';
            //			$data_noti = array(
            //				'title' => $title_noti,
            //				'type_noti' => 2,
            //				'content' => $content_noti,
            //				'file' => '',
            //				'type' => '',
            //				'staff_id' => get_client_user_id(),
            //			);
            //			$this->notification_model->add($data_noti);
            $success = $this->purchases_model->update_status($id, $data);
        }
        if ($success) {
            $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
            activity_log_v2(
                'purchase',
                'tblpurchases',
                $id,
                $get_code->prefix . $get_code->code,
                'Cập nhật trạng thái yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']'
            );
            echo json_encode(array(
                'success' => $success,
                'message' => _l('ch_confirm_3')
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('ch_confirm_3_no')
            ));
        }
        die;
    }

    public function items($type = '')
    {
        //HAU
        if ($type == 'items') {
            echo json_encode($this->purchases_model->get_items_ch());
        }
    }

    public function get_items($id = '')
    {
        echo json_encode($this->invoice_items_model->get_full_edit($id));
    }

    public function delete($id)
    {
        if (!has_permission('purchases', '', 'delete')) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_delete_not')
            ));
            die;
        }
        $ktr = get_table_where('tblrfq_ask_price', array('id_purchases' => $id), '', 'row');
        if ($ktr) {
            $response = false;
            $alert_type = 'warning';
            $message = _l('ch_exsit_rfq');
        } else {
            $ktr_supplier_quotes = get_table_where('tblsupplier_quotes', array('id_purchases' => $id), '', 'row');
            if ($ktr_supplier_quotes) {
                $response = false;
                $alert_type = 'warning';
                $message = _l('ch_exsit_quotes');
            } else {

                $order = get_table_where('tblpurchase_order', array('id_purchases' => $id), '', 'row');
                if (!empty($order)) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_delete_not')
                    ));
                    die;
                }

                $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
                activity_log_v2(
                    'purchase',
                    'tblpurchases',
                    $id,
                    $get_code->prefix . $get_code->code,
                    'Xóa phiếu yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']',
                    'delete'
                );

                $response = $this->purchases_model->delete_purchases($id);
                $alert_type = 'warning';
                $message = _l('ch_no_delete');
            }
        }
        if ($response) {
            $alert_type = 'success';
            $message = _l('ch_delete');
            //tnh
            //update status and purchase id in capacity
            $this->db->where('purchases_id', $id);
            $this->db->update('tbl_productions_capacity', ['purchases_id' => 0, 'status_purchases' => 'un_purchases']);
            //
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function change_purchases_type($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->purchases_model->change_purchases_type($id, $status);

            $get_code = get_table_where('tblpurchases', array('id' => $id), '', 'row');
            activity_log_v2(
                'purchase',
                'tblpurchases',
                $id,
                $get_code->prefix . $get_code->code,
                'Cập nhật hỏi giá yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']'
            );
        }
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'purchases'), '', 'row');
        $dataMain = get_table_where('tblpurchases', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblpurchases_items', array('purchases_id' => $id));
        $table = '';
        $data->img = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">______________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">PHIẾU YÊU CẦU MUA HÀNG</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_name_p') . ': </span><span>' . $dataMain->name_purchase . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br><br>
        ';

        $widthSTT = '';
        $widthITEM = '';
        $widthUNIT = '';
        $widthQUANTITY = '';
        $widthQUANTITY_NET = '';
        $widthNOTE = '';
        $dem_temp = 2;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_unit_purchases') {
                    $item_unit_purchases = true;
                    $dem_temp = 3;
                }
                if ($value == 'item_quantity_purchases') {
                    $item_quantity_purchases = true;
                }
                if ($value == 'item_quantity_confirm_purchases') {
                    $item_quantity_confirm_purchases = true;
                }
                if ($value == 'item_note_purchases') {
                    $item_note_purchases = true;
                }
            }
            if (isset($item_unit_purchases) && isset($item_quantity_purchases) && isset($item_quantity_confirm_purchases) && isset($item_note_purchases)) {
                $widthSTT = 'width: 8%;';
                $widthITEM = 'width: 30%;';
                $widthUNIT = 'width: 12%;';
                $widthQUANTITY = 'width: 15%;';
                $widthQUANTITY_NET = 'width: 15%;';
                $widthNOTE = 'width: 20%;';
            }
        }
        $table = '
            <table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
                <thead>
                    <tr>
                        <td style="' . $widthSTT . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $widthITEM . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';

        if (isset($item_unit_purchases)) {
            $table .= '<td style="' . $widthUNIT . 'text-align: center;font-weight: bold;">' . _l('item_unit') . '</td>';
        }
        if (isset($item_quantity_purchases)) {
            $table .= '<td style="' . $widthQUANTITY . 'text-align: center;font-weight: bold;">' . _l('item_quantity_all') . '</td>';
        }
        if (isset($item_quantity_confirm_purchases)) {
            $table .= '<td style="' . $widthQUANTITY_NET . 'text-align: center;font-weight: bold;">' . _l('item_quantity_confirm') . '</td>';
        }
        if (isset($item_note_purchases)) {
            $table .= '<td style="' . $widthNOTE . 'text-align: center;font-weight: bold;">' . _l('note') . '</td>';
        }
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_net = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $table .= '<td style="' . $widthSTT . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $widthITEM . 'text-align: left;">' . $dataItem->name . GetQuycach(
                $value['product_id'],
                $value['type']
            ) . '</td>';
            if (isset($item_unit_purchases)) {
                $table .= '<td style="' . $widthUNIT . 'text-align: center;">' . $dataItem->unit_name . '</td>';
            }
            if (isset($item_quantity_purchases)) {
                $table .= '<td style="' . $widthQUANTITY . 'text-align: center;">' . formatNumber($value['quantity']) . '</td>';
                $sum_quantity += $value['quantity'];
            }
            if (isset($item_quantity_confirm_purchases)) {
                $table .= '<td style="' . $widthQUANTITY_NET . 'text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
                $sum_quantity_net += $value['quantity_net'];
            }
            if (isset($item_note_purchases)) {
                $table .= '<td style="' . $widthNOTE . 'text-align: center;">' . $value['note'] . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        if (isset($item_quantity_purchases)) {
            $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity) . '</td>';
        }
        if (isset($item_quantity_confirm_purchases)) {
            $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_net) . '</td>';
        }
        if (isset($item_note_purchases)) {
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
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Trưởng Bộ Phận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Lãnh Đạo</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        // $data->content .= '<img src="'. (base_url('Barcode/set_barcode/').'purchases%7C%7C' . $id) .'" />';
        $qrStyle = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $data->qrCode = [
            'code' => 'purchases||' . $id,
            'type' => 'QRCODE,Q',
            'x' => 170,
            'y' => 29,
            'width' => 25,
            'height' => 25,
            'style' => $qrStyle,
            'align' => 'N',
        ];

        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }
    

    public function count_all()
    {
        if (has_permission('purchases', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select(
                'count(*) as alls',
                'tblpurchases',
                array('tblpurchases.staff_create' => get_staff_user_id()),
                '',
                'row'
            );
            $status1 = get_table_where_select(
                'count(*) as status1',
                'tblpurchases',
                array('status' => 1, 'tblpurchases.staff_create' => get_staff_user_id()),
                '',
                'row'
            );
            $status2 = get_table_where_select(
                'count(*) as status2',
                'tblpurchases',
                array('status' => 2, 'tblpurchases.staff_create' => get_staff_user_id()),
                '',
                'row'
            );
            $status3 = get_table_where_select(
                'count(*) as status3',
                'tblpurchases',
                array('status' => 3, 'tblpurchases.staff_create' => get_staff_user_id()),
                '',
                'row'
            );
            $status4 = get_table_where_select(
                'count(*) as status4',
                'tblpurchases',
                array('status' => 4, 'tblpurchases.staff_create' => get_staff_user_id()),
                '',
                'row'
            );
            $productions = get_table_where_select(
                'count(*) as productions_capacitys',
                'tblpurchases',
                array('id_plan <>' => 0, 'tblpurchases.staff_create' => get_staff_user_id()),
                '',
                'row'
            );

            $this->db->select('count(*) as not_po');
            $this->db->select('tblpurchase_order.id', null);
            $this->db->select('tblpurchases.staff_create', get_staff_user_id());
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id_purchases=tblpurchases.id', 'left');
            $not_po = $this->db->get('tblpurchases')->row();

            $this->db->select('count(*) as part_po');
            $this->db->select('tblpurchase_order.id !=', null);
            $this->db->select('tblpurchases.staff_create', get_staff_user_id());
            $this->db->select('tblpurchase_order.status <', 4);
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id_purchases=tblpurchases.id', 'left');
            $part_po = $this->db->get('tblpurchases')->row();

            $this->db->select('count(*) as all_po');
            $this->db->select('tblpurchase_order.id !=', null);
            $this->db->select('tblpurchases.staff_create', get_staff_user_id());
            $this->db->select('tblpurchase_order.status', 4);
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id_purchases=tblpurchases.id', 'left');
            $all_po = $this->db->get('tblpurchases')->row();
        } else {
            $count = get_table_where_select('count(*) as alls', 'tblpurchases', array(), '', 'row');
            $status1 = get_table_where_select('count(*) as status1', 'tblpurchases', array('status' => 1), '', 'row');
            $status2 = get_table_where_select('count(*) as status2', 'tblpurchases', array('status' => 2), '', 'row');
            $status3 = get_table_where_select('count(*) as status3', 'tblpurchases', array('status' => 3), '', 'row');
            $status4 = get_table_where_select('count(*) as status4', 'tblpurchases', array('status' => 4), '', 'row');
            $productions = get_table_where_select(
                'count(*) as productions_capacitys',
                'tblpurchases',
                array('id_plan <>' => 0),
                '',
                'row'
            );

            $this->db->select('tblpurchases.id');
            $this->db->where('tblpurchase_order.id', null);
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id_purchases=tblpurchases.id', 'left');
            $this->db->group_by('tblpurchases.id');
            $not_po = $this->db->get('tblpurchases')->num_rows();


            $this->db->select('tblpurchases.id');
            $this->db->where('tblpurchase_order.id !=', null);
            $this->db->where('tblpurchases.status <', 4);
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id_purchases=tblpurchases.id', 'left');
            $this->db->group_by('tblpurchases.id');
            $part_po = $this->db->get('tblpurchases')->num_rows();

            $this->db->select('tblpurchases.id');
            $this->db->where('tblpurchase_order.id !=', null);
            $this->db->where('tblpurchases.status', 4);
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id_purchases=tblpurchases.id', 'left');
            $this->db->group_by('tblpurchases.id');
            $all_po = $this->db->get('tblpurchases')->num_rows();
        }

        $data['all'] = $count->alls;
        $data['status1'] = $status1->status1;
        $data['status2'] = $status2->status2;
        $data['status3'] = $status3->status3;
        $data['status4'] = $status4->status4;
        $data['not_po'] = $not_po;
        $data['part_po'] = $part_po;
        $data['all_po'] = $all_po;
        $data['productions'] = $productions->productions_capacitys;

        echo json_encode($data);
    }

    public function SearchManufactures($id = '')
    {
        $search = $this->input->get('term');
        $this->db->select(
            '
                    tbl_productions_orders_details.id as id,
                    tbl_productions_orders_details.reference_no as text,
                    tbl_productions_orders_items.items_name as items_name,
                    tbl_productions_orders_items.items_code as items_code',
            false
        );
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $search);
            $this->db->or_like(
                'concat(tbl_productions_orders_items.items_code,"",tbl_productions_orders_items.items_name)',
                $search
            );
            $this->db->group_end();
        } else {
            if ($id > 0) {
                $this->db->where('tbl_productions_orders_details.id', $id);
            }
        }
        $this->db->join(
            'tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'left'
        );
        $this->db->order_by('tbl_productions_orders_details.reference_no', 'DESC');
        $this->db->limit(50);
        $items['results'] = $this->db->get('tbl_productions_orders_details')->result_array();
        echo json_encode($items);
        die();
    }

    public function SearchItems($id = '', $types = '')
    {
        $data = [];
        $search = $this->input->get('term');
        $type = $this->input->get('type');
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
                    id,
                    tblitems.name as text,
                    tblitems.code,
                    tblitems.price,
                    "" as mode,
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
                id as id,
                tbl_products.name as text,
                tbl_products.code,
                mode,
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
            $this->db->where('tbl_products.type_products !=', 'products');
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
                id as id,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                "" as mode,
                tbl_tools_supplies.price_import as price,
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
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_two);
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
                id as id,
                tbl_materials.name as text,
                tbl_materials.code,
                mode,
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
        } elseif ($type == 'items') {
            $this->db->select(
                '
                    id as id,
                    tblitems.name as text,
                    "" as mode,
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
        } elseif ($type == 'product') {
            $this->db->select(
                '
                id as id,
                tbl_products.name as text,
                tbl_products.code,
                mode,
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
                id as id,
                tbl_materials.name as text,
                tbl_materials.code,
                mode,
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
                id as id,
                tbl_tools_supplies.name as text,
                tbl_tools_supplies.code,
                "" as mode,
                tbl_tools_supplies.price_import as price,
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
            $this->db->order_by('tbl_tools_supplies.name', 'DESC');
            $this->db->limit($limit_two);
            // $this->db->limit(($limit_all - $count_product));
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

    public function SearchQR()
    {
        $data = [];
        $code = $this->input->post('code');
        $code = explode('||', $code);
        if (count($code) == 1) {
            $data['items'] = [];
            $data['result'] = 0;
            $data['message'] = lang('Mã đúng đúng định dạng');
            echo json_encode($data);
            die;
        }
        $type = $code[0];
        $id = $code[1];
        if ($type == 'products') {
            $type = 'product';
        } elseif ($type == 'materials') {
            $type = 'nvl';
        } elseif ($type == 'tools_supplies') {
            $type = 'tools';
        }
        $items = get_full_item_new($id, $type);
        if(empty($items)){
            $data['items'] = [];
            $data['result'] = 0;
            $data['message'] = lang('Không tìm thấy mặt hàng');
            echo json_encode($data);
            die;
        }
        $items->type = $type;
        if ($type == 'tools') {
            $items->type_item = 'tools_supplies';
        }
        $items->html = format_item_color($id, $type);
        $items->avatar = (!empty($items->avatar) ? (file_exists($items->avatar) ? base_url($items->avatar) : (file_exists('uploads/materials/' . $items->avatar) ? base_url('uploads/materials/' . $items->avatar) : (file_exists('uploads/products/' . $items->avatar) ? base_url('uploads/products/' . $items->avatar) : (file_exists('uploads/tools_supplies/' . $items->avatar) ? base_url('uploads/tools_supplies/' . $items->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
        if ($items->type_item == 'products') {
            $data['items'] = [];
            $data['result'] = 0;
            $data['message'] = lang('Không thể yêu cầu mua thành phẩm');
        } else {
            $data['items'] = $items;
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        }
        echo json_encode($data);
    }

    public function synthetic_purchase()
    {
        $data = [];
        if (!has_permission('purchases', '', 'view') && !has_permission('purchases', '', 'view_own')) {
            access_denied('synthetic_purchase');
        }
        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(prefix,"-",code) as code');
        $this->db->from('tblsuppliers');
        $data['dataSupplier'] = $this->db->get()->result_array();
        $data['title'] = lang('dt_purchases');
        $this->load->view('admin/purchases/synthetic_purchase', $data);
    }

    public function getSyntheticPurchase()
    {
        $search_code = $this->input->post('search_code');
        $search_id_suppliers = $this->input->post('search_id_suppliers');
        $custom_item_select = $this->input->post('custom_item_select');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tblpurchases.id as id',
            'CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase',
            'tblpurchases.name_purchase as name_purchase',
            'tblpurchases.date as date',
            'tblpurchases.delivery_date as delivery_date',
            'tblsuppliers.company as company',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblpurchases';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id',
            'LEFT JOIN tbl_internal_proposal_purchase_items ON tbl_internal_proposal_purchase_items.id_purchases_items = tblpurchases_items.id',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id',
        ];

        array_push($where, 'AND tblpurchases.id_plan = 0');

        if (!empty($search_code)) {
            if (is_numeric($search_code)) {
                array_push($where, "AND tblpurchases.id = '" . $search_code . "'");
            }
        }

        if (!empty($custom_item_select)) {
            array_push($where, "AND tbl_internal_proposal_purchase_items.id_items = '" . $custom_item_select . "'");
        }

        if ($search_id_suppliers) {
            array_push($where, 'AND tbl_internal_proposal_purchase_items.suppliers_id IN (' . implode(
                ', ',
                $search_id_suppliers
            ) . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND DATE_FORMAT(tblpurchases.date, '%Y-%m-%d') >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND DATE_FORMAT(tblpurchases.date, '%Y-%m-%d') <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblpurchases_items.product_id as id_items, 
             tblpurchases_items.type as type, 
             tbl_internal_proposal_purchase_items.quantity as quantity, 
             tbl_internal_proposal_purchase_items.quantity_stock as quantity_stock, 
             (CASE WHEN tbl_internal_proposal_purchase_items.id IS NULL THEN tblpurchases_items.quantity_net ELSE tbl_internal_proposal_purchase_items.quantity_payment END) as quantity_payment, 
             tbl_internal_proposal_purchase_items.price as price'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $type_item = $aRow['type'];
            $items_id = $aRow['id_items'];
            $getItem = get_full_item_new($items_id, $type_item);

            $row = array();
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
            $row[] = '<div class="text-center" style="width: 60px">' . $getItem->time_stock . '</div>';
            $row[] = '<div class="text-center" style="width: 60px">' . $getItem->quantity_minimum . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    function exportExcelSyntheticPurchase()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $search_id_suppliers = $this->input->post('search_id_suppliers');
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $search_code = $this->input->post('search_code');
            $custom_item_select = $this->input->post('custom_item_select');
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
                'tblpurchases.id as id,
                CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchase,
                tblpurchases.name_purchase as name_purchase,
                tblpurchases.date as date,
                tblpurchases.delivery_date as delivery_date,
                tblpurchases_items.product_id as id_items, 
                tblpurchases_items.type as type, 
                tbl_internal_proposal_purchase_items.quantity as quantity, 
                tbl_internal_proposal_purchase_items.quantity_stock as quantity_stock, 
                tbl_internal_proposal_purchase_items.quantity_payment as quantity_payment, 
                tbl_internal_proposal_purchase_items.price as price,
                tblsuppliers.company as company'
            );
            $this->db->from('tblpurchases');
            $this->db->join('tblpurchases_items', 'tblpurchases_items.purchases_id = tblpurchases.id', 'inner');
            $this->db->join('tbl_internal_proposal_purchase_items', 'tbl_internal_proposal_purchase_items.id_purchases_items = tblpurchases_items.id', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_internal_proposal_purchase_items.suppliers_id', 'left');

            $this->db->where('tblpurchases.id_plan = 0');

            if (!empty($search_code)) {
                if (is_numeric($search_code)) {
                    $this->db->where("tblpurchases.id = '" . $search_code . "'");
                }
            }

            if (!empty($custom_item_select)) {
                $this->db->where("tbl_internal_proposal_purchase_items.id_items = '" . $custom_item_select . "'");
            }

            if ($search_id_suppliers) {
                $this->db->where('tbl_internal_proposal_purchase_items.suppliers_id IN (' . implode(
                    ', ',
                    $search_id_suppliers
                ) . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search);
                $this->db->where("DATE_FORMAT(tblpurchases.date, '%Y-%m-%d') >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search);
                $this->db->where("DATE_FORMAT(tblpurchases.date, '%Y-%m-%d') <= '" . $end_date_search . "'");
            }

            $this->db->order_by('CONCAT(tblpurchases.prefix,"",tblpurchases.code) desc');
            $dtSyntheticPurchase = $this->db->get()->result_array();


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
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('YÊU CẦU MUA HÀNG (PR)')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:S1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'Mã YCMH');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Tên Mã YCMH');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập YCMH');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Ngày Về NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Loại Nhà Cung Cấp');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Nhóm NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Chủng Loại NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Mã NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Tên NPL');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Quy Cách');
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Đơn Vị Chuẩn')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Đơn Vị Vào Kho')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Đơn Vị Thanh toán')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Số Lượng');
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Giá Nhập');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Tổng Tiền');
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Tiêu Chuẩn Đóng Gói')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Thời Gian Lưu Kho')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '', 'Tồn Cho Phép')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:S$sttRow")->applyFromArray([
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
            $rowBegin = $sttRow;
            if (!empty($dtSyntheticPurchase)) {
                foreach ($dtSyntheticPurchase as $key => $value) {
                    $type_item = $value['type'];
                    $items_id = $value['id_items'];
                    $getItem = get_full_item_new($items_id, $type_item);
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['code_purchase']);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_purchase'])->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", _dt($value['delivery_date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['company']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $getItem->name_category)->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $getItem->name_species)->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $getItem->code)->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $getItem->name)->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $getItem->name_mode)->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $getItem->unit_name);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $getItem->unit_name_stock);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $getItem->unit_name_payment);
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "N$rowBegin",
                        $value['quantity_payment']
                    )->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "O$rowBegin",
                        $value['price']
                    )->getStyle("O$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "P$rowBegin",
                        ($value['quantity_payment'] * $value['price'])
                    )->getStyle("P$rowBegin")->getNumberFormat()->setFormatCode(formatNumberExcel($value['quantity_payment'] * $value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "Q$rowBegin",
                        ''
                    )->getStyle("Q$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "R$rowBegin",
                        $getItem->time_stock
                    )->getStyle("R$rowBegin");
                    $objPHPExcel->getActiveSheet()->setCellValue(
                        "S$rowBegin",
                        $getItem->quantity_minimum
                    )->getStyle("S$rowBegin");

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:S$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("K$rowBegin:N$rowBegin")->applyFromArray([
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
            $filename = lang('phieu_yeu_cau_mua_hang') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
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
}
