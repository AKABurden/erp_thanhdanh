<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_order extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_order_model');
        $this->load->model('purchases_model');
        $this->load->model('invoice_items_model');
        $this->load->model('supplier_quotes_model');
        $this->load->model('misc_model');
        $this->load->model('costs_model');
        $this->load->model('products_model');
    }

    public function index()
    {
        if (!has_permission('purchase_order', '', 'view') && !has_permission('purchase_order', '', 'view_own')) {
            access_denied('purchase_order');
        }
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['dataStaff'] = $this->db->get()->result_array();

        $this->db->select('tblsuppliers.id, tblsuppliers.company, CONCAT(prefix,"-",code) as code');
        $this->db->from('tblsuppliers');
        $data['dataSupplier'] = $this->db->get()->result_array();
        $data['dataPriorities'] = get_table_where('tbltickets_priorities');

        $data['title'] = _l('ch_order');
        $data['id_modal'] = $this->session->flashdata('purchase_order_id_modal');
        //cập nhật dữ liệu mức độ ưu tiên cho người đăng nhập đầu tiên trong ngày
        $checkDone = get_table_where('tbl_update_on_day', array(), '', 'row');
        if (@$checkDone->date != date('Y-m-d')) {
            $this->misc_model->update_priority();

            $this->db->set('date', date('Y-m-d'));
            $this->db->update('tbl_update_on_day');
        }
        //end
        $this->db->select('tblsuppliers.*');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tblpurchase_order.suppliers_id');
        $this->db->group_by('tblsuppliers.id');
        $data['suppliers'] = $this->db->get('tblpurchase_order')->result_array();
        if (is_mobile()) {
            $this->load->view('admin/themes_mobile/manage_purchase_order', $data);
        } else {
            $this->load->view('admin/purchase_order/manage', $data);
        }
    }

    public function table()
    {
        $this->app->get_table_data('purchase_order');
    }

    public function detail($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('purchase_order', '', 'create')) {
                    access_denied('purchase_order');
                }

                $data = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {
                    $id = $this->purchase_order_model->add($data);
                }

                if ($id) {
                    create_order($id);
                    $get_code = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
                    activity_log_v2('purchase', 'tblpurchase_order', $id, $get_code->prefix . '-' . $get_code->code, 'Thêm mới đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
                    set_alert('success', _l('ch_added_successfuly'));
                    $this->session->set_flashdata('purchase_order_id_modal', $id);
                    redirect(admin_url('purchase_order'));
                }
            } else {
                if (!has_permission('purchase_order', '', 'edit')) {
                    access_denied('purchase_order');
                }
                $success = $this->purchase_order_model->update($this->input->post(), $id);
                if ($success == true) {
                    $this->load->model('misc_model');
                    $this->misc_model->changeRowNew_model('tblpurchase_order', $id);

                    $get_code = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
                    activity_log_v2('purchase', 'tblpurchase_order', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');

                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('purchase_order/detail/' . $id));
            }
        }
        if ($id == '') {
            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }
            $title = _l('add_new', _l('ch_purchase_order'));
        } else {
            if (!has_permission('purchase_order', '', 'edit')) {
                access_denied('purchase_order');
            }
            $title = _l('edit', _l('ch_purchase_order'));
            $data['items'] = $this->purchase_order_model->get($id);
        }
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['tax'] = get_table_where('tbltaxes');
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $data['currency'] = get_table_where('tblcurrencies');
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/detail', $data);
    }
    public function purchases_detail($id = '')
    {
        if ($this->input->post()) {
            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }

            $data = $this->input->post();

            $success = $this->purchase_order_model->update($this->input->post(), $id);
            if ($success == true) {
                $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
                $purchase = get_items_purchase_new($purchase_order->id_purchases);
                if ($purchase <= 0) {
                    $purchases = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
                    $staff_id = '1foso';
                    $date = date('Y-m-d H:i:s');
                    $history_status = $purchases->history_status;
                    $history_status .= '|' . $staff_id . ',' . $date;
                    $in = array(
                        'history_status' => $history_status,
                        'note_cancel' => '',
                        'status' => 4,
                    );
                    $this->db->where('id', $purchase_order->id_purchases);
                    $result = $this->db->update('tblpurchases', $in);
                } else {
                    $ktr_purchases = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
                    if ($ktr_purchases->status = 4) {
                        $cance = explode('|', $ktr_purchases->history_status);

                        if (!empty($cance[3])) {
                            $cances = explode(',', $cance[3]);
                        } else {
                            $cances = explode(',', $cance[2]);
                        }
                        if ($cances[0] == '1foso') {
                            $history_statuss = '';
                            $history_status = $ktr_purchases->history_status;
                            $history = explode('|', $history_status);
                            if (count($history == 3)) {
                                foreach ($history as $key => $value) {
                                    if ($key > 0) {
                                        if ($key < 3) {
                                            $history_statuss .= '|' . $value;
                                        }
                                    }
                                }
                            } else {
                                foreach ($history as $key => $value) {
                                    if ($key > 0) {
                                        if ($key < 2) {
                                            $history_statuss .= '|' . $value;
                                        }
                                    }
                                }
                            }
                            $in = array(
                                'history_status' => $history_statuss,
                                'note_cancel' => '',
                                'status' => 3,
                            );
                            $this->db->where('id', $purchase_order->id_purchases);
                            $this->db->update('tblpurchases', $in);
                        }
                    }
                }
                $this->load->model('misc_model');
                $this->misc_model->changeRowNew_model('tblpurchase_order', $id);

                set_alert('success', _l('ch_updated_successfuly'));
            }
            redirect(admin_url('purchase_order/purchases_detail/' . $id));
        }

        $data['type_of_document'] = 1;

        $title = _l('edit', _l('ch_purchase_order'));
        $data['items'] = $this->purchase_order_model->get($id);
        $data['purchase'] = $this->purchases_model->get_create_purchase_order($data['items']->id_purchases, $id);
        $html = '<option></option>';
        foreach ($data['purchase']->items as $key => $value) {
            if ($key == 0) {
                $html .= '<optgroup label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html .= '</optgroup>';
                $html .= '<optgroup label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
            }
        }
        $html .= '</optgroup>';
        $data['html'] = $html;
        $data['idd'] = $id;
        $data['id'] = $data['items']->id_purchases;
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $data['currency'] = get_table_where('tblcurrencies');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/create_detail', $data);
    }
    public function coppy_purchases_detail($id = '')
    {
        if ($this->input->post()) {
            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }
            $data = $this->input->post();
            if (isset($data['items']) && count($data['items']) > 0) {
                $idd = $this->purchase_order_model->add($data);
            }
            if ($idd) {
                $purchase = get_items_purchase_new($id);
                if ($purchase <= 0) {
                    $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
                    $staff_id = '1foso';
                    $date = date('Y-m-d H:i:s');
                    $history_status = $purchases->history_status;
                    $history_status .= '|' . $staff_id . ',' . $date;
                    $in = array(
                        'history_status' => $history_status,
                        'note_cancel' => '',
                        'status' => 4,
                    );
                    $this->db->where('id', $id);
                    $result = $this->db->update('tblpurchases', $in);
                }

                $get_code = get_table_where('tblpurchase_order', array('id' => $idd), '', 'row');
                activity_log_v2('purchase', 'tblpurchase_order', $idd, $get_code->prefix . '-' . $get_code->code, 'Thêm mới đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
                set_alert('success', _l('ch_added_successfuly'));
                $this->session->set_flashdata('purchase_order_id_modal', $idd);
                redirect(admin_url('purchase_order'));
            }
        }

        $data['type_of_document'] = 1;

        $title = _l('Coppy', _l('ch_purchase_order'));
        $data['items'] = $this->purchase_order_model->get($id);

        // $data['purchase'] = $this->purchases_model->get_create_purchase_order($data['items']->id_purchases, $id);
        // $html = '<option></option>';
        // foreach ($data['purchase']->items as $key => $value) {
        //     if ($key == 0) {
        //         $html .= '<optgroup label="' . $value['name'] . '">';
        //     } else if ($value['id'] == 'h') {
        //         $html .= '</optgroup>';
        //         $html .= '<optgroup label="' . $value['name'] . '">';
        //     } else {
        //         $value['name'] = str_replace('"', '', $value['name']);
        //         $value['code_item'] = str_replace('"', '', $value['code_item']);
        //         $value['name'] = str_replace("'", '', $value['name']);
        //         $value['code_item'] = str_replace("'", '', $value['code_item']);
        //         $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
        //     }
        // }
        // $html .= '</optgroup>';
        // $data['html'] = $html;
        $data['idd'] = $id;
        $data['id'] = $data['items']->id_purchases;
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['tax'] = get_table_where('tbltaxes');
        $data['currency'] = get_table_where('tblcurrencies');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/coppy_detail', $data);
    }

    public function create_detail($id = '')
    {
        if ($this->input->post()) {
            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }

            $data = $this->input->post();
            if (isset($data['items']) && count($data['items']) > 0) {
                $idd = $this->purchase_order_model->add($data);
            }
            if ($idd) {
                $purchase = get_items_purchase_new($id);
                if ($purchase <= 0) {
                    $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
                    $staff_id = '1foso';
                    $date = date('Y-m-d H:i:s');
                    $history_status = $purchases->history_status;
                    $history_status .= '|' . $staff_id . ',' . $date;
                    $in = array(
                        'history_status' => $history_status,
                        'note_cancel' => '',
                        'status' => 4,
                    );
                    $this->db->where('id', $id);
                    $result = $this->db->update('tblpurchases', $in);
                }

                $get_code = get_table_where('tblpurchase_order', array('id' => $idd), '', 'row');
                activity_log_v2('purchase', 'tblpurchase_order', $idd, $get_code->prefix . '-' . $get_code->code, 'Thêm mới đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
                set_alert('success', _l('ch_added_successfuly'));
                $this->session->set_flashdata('purchase_order_id_modal', $idd);
                redirect(admin_url('purchase_order'));
            }
        }
        $data['type_of_document'] = 1;
        $title = _l('add_new', _l('ch_purchase_order'));
        $data['purchase'] = $this->purchases_model->get_create_purchase_order($id);

        $html = '<option></option>';
        foreach ($data['purchase']->items as $key => $value) {

            if ($key == 0) {
                $html .= '<optgroup data-text = "' . $value['name'] . '" label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html .= '</optgroup>';
                $html .= '<optgroup data-text = "' . $value['name'] . '" label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                // $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                if ($value['type_items'] == 'tools' || $value['type_items'] == 'items') {
                    $html .= '<option data-text = "[' . $value['code_item'] . '] ' . $value['name'] . '" data-type_items="' . $value['type_items'] . '"  quantity_warehoue="' . $value['quantity_warehoue'] . '" value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                } else {
                    $html .= '<option  quantity_warehoue="' . $value['quantity_warehoue'] . '" data-text = "[' . $value['code_item'] . '] ' . $value['name'] . '" data-type_items="' . $value['type_items'] . '" data-mode="' . $value['mode'] . '"  data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                }
            }
        }
        $html .= '</optgroup>';
        $data['html'] = $html;

        $html1 = '';
        foreach ($data['purchase']->items as $key => $value) {

            if ($key == 0) {
                $html1 .= '<optgroup data-text = "' . $value['name'] . '" label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html1 .= '</optgroup>';
                $html1 .= '<optgroup data-text = "' . $value['name'] . '" label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                // $html1 .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id_purchases_items'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                $text = '<span><b>[' . $value['code_item'] . '] ' . $value['name'] . '</b></span>';
                $get_items = get_items($value['id'], $value['type_items']);
                if ($value['type_items'] == 'tools' || $value['type_items'] == 'items') {
                } elseif ($value['type_items'] == 'product' || $value['type_items'] == 'nvl') {
                }
                $html1 .= '<option data-content="' . $text . '" quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id_purchases_items'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
            }
        }
        $html1 .= '</optgroup>';
        $data['html1'] = $html1;
        $data['id'] = $id;
        $data['idd'] = 0;
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['currency'] = get_table_where('tblcurrencies');
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/create_detail', $data);
    }

    public function create_detailquotes($id = '')
    {
        if ($this->input->post()) {

            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }

            $data = $this->input->post();

            if (isset($data['items']) && count($data['items']) > 0) {
                $idd = $this->purchase_order_model->add($data);
            }

            if ($idd) {
                // $quotes = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                // $this->db->update('tblpurchase_order',array('id_purchase_proce'=>$quotes->id_purchases),array('id'=>$idd));
                set_status_purchse_order($idd);
                // $quotes = get_table_where('tblsupplier_quotes',array('id'=>$id),'','row');
                // if(!empty($quotes->id_purchases))
                // {
                //     $purchase = get_items_purchase_quotes($quotes->id_purchases);
                //     if($purchase <= 0)
                //     {
                //         $purchases = get_table_where('tblpurchases',array('id'=>$quotes->id_purchases),'','row');
                //             $staff_id='1foso';
                //             $date=date('Y-m-d H:i:s');
                //             $history_status = $purchases->history_status;
                //             $history_status.='|'.$staff_id.','.$date;
                //         $in = array(
                //             'history_status'=>$history_status,
                //             'note_cancel' => '',
                //             'status' => 4,
                //         );
                //         $this->db->where('id', $id);
                //         $result = $this->db->update('tblpurchases', $quotes->id_purchases);
                //     }
                // }
                set_alert('success', _l('ch_added_successfuly'));
                $this->session->set_flashdata('purchase_order_id_modal', $idd);
                redirect(admin_url('purchase_order'));
            }
        }
        $title = _l('add_new', _l('ch_purchase_order'));
        $data['quotes'] = $this->supplier_quotes_model->get_full_edit($id);
        $data['load_html'] = $this->supplier_quotes_model->get_items_quotes_combobox($id);
        $html = '<option></option>';
        foreach ($data['load_html'] as $key => $value) {
            if ($key == 0) {
                $html .= '<optgroup label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html .= '</optgroup>';
                $html .= '<optgroup label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_name'] = str_replace('"', '', $value['code_name']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_name'] = str_replace("'", '', $value['code_name']);
                $html .= '<option quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '">(' . $value['code_name'] . ') ' . $value['name'] . '</option>';
            }
        }
        $html .= '</optgroup>';
        $data['html'] = $html;
        $data['id'] = $id;
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/create_detailquotes', $data);
    }

    public function view_purchase_order($id = '')
    {
        $data['items'] = $this->purchase_order_model->get($id);
        $data['amount_to_vnd'] = get_table_where('tblcurrencies', array('id' => $data['items']->currency), '', 'row');

        $data['items_plan'] = $this->purchase_order_model->get_items_purchase_order_plan($id);
        $data['dataLog'] = get_table_where('tblactivity_log_v2', array('table_obj' => 'tblpurchase_order', 'id_obj' => $id), 'id DESC');

        $this->db->where('id_purchase_order', $id);
        $this->db->order_by('date_create', 'desc');
        $data['feedback'] = $this->db->get('tblpurchase_order_feedback')->result();
        foreach ($data['feedback'] as $key => $value) {
            $this->db->where('rel_id', $value->id);
            $this->db->where('rel_type', 'feedback_pr');
            $data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
        }

        $this->load->view('admin/purchase_order/view_modal', $data);
    }

    public function view_purchase_order_import($id = '')
    {
        $data['items'] = $this->purchase_order_model->get_order_import($id);
        $this->load->view('admin/purchase_order/view_modal_import', $data);
    }

    public function update_status($value = '')
    {

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            if ($status == 1) {
                if (!has_permission('purchase_order', '', 'approve_accept')) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_approve_not')
                    ));
                    die;
                }
                approve_order_app($id);
            } else if ($status == 2) {
                if (!has_permission('purchase_order', '', 'approve_qc')) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_approve_not')
                    ));
                    die;
                }
            }
            $checkCancel = get_table_where('tblpurchase_order', array('id' => $id, 'cancel <>' => 0), '', 'row');
            if ($checkCancel) {
                $success = false;
            } else {
                $status = $this->input->post('status');
                if ($status == 1) {
                    approve_order_app($id);
                }
                $purchases = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
                $staff_id = get_staff_user_id();
                $date = date('Y-m-d H:i:s');
                $history_status = $purchases->history_status;
                $history_status .= '|' . $staff_id . ',' . $date;
                $data = array(
                    'history_status' => $history_status,
                    'status' => ($status + 1),
                );
                $success = $this->purchase_order_model->update_status($id, $data);
            }
        }
        if ($success) {
            $get_code = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
            activity_log_v2('purchase', 'tblpurchase_order', $id, $get_code->prefix . '-' . $get_code->code, 'Cập nhật trạng thái đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('Xác nhận đề xuất thành công')
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }

    public function cancel_status($value = '')
    {
        if ($this->input->post()) {

            $id = $this->input->post('id');
            $ktr = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
            if (!empty($ktr->cancel) && (explode(',', $ktr->cancel)[0] != '1foso')) {
                $data = array(
                    'cancel' => ''
                );
                $this->db->where('id', $id);
                $success = $this->db->update('tblpurchase_order', $data);
                echo json_encode(array(
                    'success' => $success,
                    'message' => _l('Xác nhận kết thúc đơn hàng')
                ));
                die;
            }
            $cancel = get_staff_user_id() . ',' . date('Y-m-d H:i:s');
            $data = array(
                'cancel' => $cancel
            );
            $this->db->where('id', $id);
            $success = $this->db->update('tblpurchase_order', $data);
        }
        if ($success) {
            $this->db->set('id_tickets_priorities', NULL);
            $this->db->where('id', $id);
            $this->db->update('tblpurchase_order');
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Xác nhận kết thúc đơn hàng')
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }

    public function delete($id)
    {
        if (!has_permission('purchase_order', '', 'delete')) {
            echo json_encode(array(
                'success' => 'warning',
                'message' => _l('ch_no_delete')
            ));
            die;
        }
        $order = get_table_where('tblimport', array('id_order' => $id), '', 'row');
        if (!empty($order)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => 'Đã tồn tại phiếu nhập hàng! Không thể xóa!'
            ));
            die;
        }
        $suggestion = get_table_where('tblsuggestion', array('purchase_order_id' => $id), '', 'row');
        if (!empty($suggestion)) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => 'Phiếu đã tồn tại phiếu đề xuất tài chính nên không thể xóa!'
            ));
            die;
        }
        $get_code = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        activity_log_v2('purchase_order', 'tblpurchase_order', $id, $get_code->prefix . '-' . $get_code->code, 'Xóa đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']', 'delete');
        $response = $this->purchase_order_model->delete_purchase_order($id);
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        if ($response) {
            $alert_type = 'success';
            $message = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function items($type_of_document = '', $id = '', $type = '')
    {
        if ($type_of_document == 1) {
            $id_array = $this->input->post('id_array');
            $suppliers_id = $this->input->post('suppliers_id');

            $items = $this->purchase_order_model->get_items_purchase($type, $id, $id_array);
            if (!empty($suppliers_id)) {
                foreach ($items as $key => $value) {
                    $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $suppliers_id, 'id_items' => $value['product_id'], 'type' => $value['type']), '', 'row');
                    if (!empty($ktr_supp)) {
                        $items[$key]['price_ch'] = $ktr_supp->price;
                    } else {
                        $items[$key]['price_ch'] = 0;
                    }
                }
            } else {
                foreach ($items as $key => $value) {
                    $items[$key]['price_ch'] = 0;
                }
            }

            echo json_encode($items);
            die;
        }
    }

    public function get_items($id = '', $type = '', $type_of_document = '', $id_product)
    {
        if ($type_of_document == 1) {
            $items = $this->purchase_order_model->get_items_purchases_item($type, $id, $id_product);
            if ($items->avatar == '') {
                $items->avatar = 'uploads/no-img.jpg';
            }
            echo json_encode($items);
            die;
        }
    }

    public function get_items_order($id = '', $type = '', $id_purchases = '', $suppliers_id = '')
    {
        $items = $this->purchases_model->get_items_order($id, $type, $id_purchases, $suppliers_id);

        $items->avatar = (!empty($items->avatar) ? (file_exists($items->avatar) ? base_url($items->avatar) : (file_exists('uploads/materials/' . $items->avatar) ? base_url('uploads/materials/' . $items->avatar) : (file_exists('uploads/products/' . $items->avatar) ? base_url('uploads/products/' . $items->avatar) : (file_exists('uploads/tools_supplies/' . $items->avatar) ? base_url('uploads/tools_supplies/' . $items->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
        echo json_encode($items);
    }

    public function items_quote($id = '', $type = '')
    {
        echo json_encode($this->purchase_order_model->get_items_quote($type, $id));
        die;
    }

    public function test_quantity_all($type = '', $type_of_document = '', $id = '', $id_product = '')
    {
        $type_of_document = $this->input->post('type_of_document');
        $id = $this->input->post('id');
        $id_order = $this->input->post('id_order');
        $test_quantity = 0;
        if ($type_of_document == 1) {

            $product = explode(',', trim($this->input->post('product_id'), ','));

            foreach ($product as $key => $v) {
                $product_id = explode('|', $v);
                $data['items'][$key]['quantity'] = $this->purchase_order_model->test_quantity_all($product_id[0], $id, $product_id[1]);
                $data['items'][$key]['type'] = $product_id[0];
                $data['items'][$key]['id_product'] = $product_id[1];
                $quantity_old = 0;
                if (!empty($id_order)) {
                    $quantity_old = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id_order, 'product_id' => $product_id[1], 'type' => $product_id[0]), '', 'row')->quantity;
                }
                if ($product_id[2] > ($data['items'][$key]['quantity'] + $quantity_old)) {
                    $test_quantity++;
                }
            }
        }

        $data['test_quantity'] = $test_quantity;
        echo json_encode($data);
        die;
    }

    public function test_quantity($type = '', $type_of_document = '', $id = '', $id_product = '')
    {
        $type_of_document = $this->input->post('type_of_document');
        $id = $this->input->post('id');
        $id_order = $this->input->post('id_order');
        $test_quantity = 0;
        if ($type_of_document == 1) {

            $product = explode(',', trim($this->input->post('product_id'), ','));

            foreach ($product as $key => $v) {
                $product_id = explode('|', $v);
                $data['items'][$key]['quantity'] = $this->purchase_order_model->test_quantity($product_id[0], $id, $product_id[1]);
                $data['items'][$key]['type'] = $product_id[0];
                $data['items'][$key]['id_product'] = $product_id[1];
                $quantity_old = 0;
                if (!empty($id_order)) {
                    $quantity_old = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id_order, 'product_id' => $product_id[1], 'type' => $product_id[0]), '', 'row')->quantity_suppliers;
                    $data['items'][$key]['quantity'] = $data['items'][$key]['quantity'] + $quantity_old;
                }
                if ($product_id[2] > ($data['items'][$key]['quantity'])) {
                    $test_quantity++;
                }
            }
        }
        $data['test_quantity'] = $test_quantity;
        echo json_encode($data);
        die;
    }
    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'purchase_order'), '', 'row');
        $dataMain = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
        $table = '';
        $data->img = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        // var_dump($dataMain);
        $supplier = get_table_where('tblsuppliers', ['id' => $dataMain->suppliers_id], '', 'row_array');
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">ĐƠN ĐẶT HÀNG</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
		  	<span style="font-weight: bold;">' . _l('Nhà cung cấp') . ': </span><span>' . $supplier['company'] . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_delivery_date') . ': </span><span>' . _d($dataMain->delivery_date) . '</span><br>
            <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';

        $width1 = '';
        $width2 = '';
        // $width3 = '';
        $width4 = '';
        $width5 = '';
        // $width6 = '';
        $width7 = '';
        // $width8 = '';
        $width9 = '';
        $width10 = '';
        $width11 = '';
        $width12 = '';
        $dem_temp = 3;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_quantity_purchase_order') {
                    $item_quantity_purchase_order = true;
                }
                if ($value == 'item_quantity_suppliers_purchase_order') {
                    $item_quantity_suppliers_purchase_order = true;
                }
                if ($value == 'item_unit_purchase_order') {
                    $item_unit_purchase_order = true;
                }
                if ($value == 'item_price_expected_purchase_order') {
                    $item_price_expected_purchase_order = true;
                }
                if ($value == 'item_price_suppliers_purchase_order') {
                    $item_price_suppliers_purchase_order = true;
                }
                if ($value == 'item_amount_expected_purchase_order') {
                    $item_amount_expected_purchase_order = true;
                }
                if ($value == 'item_promotion_suppliers_purchase_order') {
                    $item_promotion_suppliers_purchase_order = true;
                }
                if ($value == 'item_tax_purchase_order') {
                    $item_tax_purchase_order = true;
                }
                if ($value == 'item_amount_suppliers_purchase_order') {
                    $item_amount_suppliers_purchase_order = true;
                }
                if ($value == 'item_note_purchase_order') {
                    $item_note_purchase_order = true;
                }
            }
            if (!has_permission('purchase_order', '', 'view_price')) {
                unset($item_tax_purchase_order);
                unset($item_amount_expected_purchase_order);
                unset($item_amount_suppliers_purchase_order);
                unset($item_price_suppliers_purchase_order);
                unset($item_promotion_suppliers_purchase_order);
            }
            if (isset($item_quantity_suppliers_purchase_order) && isset($item_unit_purchase_order) && isset($item_price_suppliers_purchase_order) && isset($item_promotion_suppliers_purchase_order) && isset($item_tax_purchase_order) && isset($item_amount_suppliers_purchase_order) && isset($item_note_purchase_order)) {
                $width1 = 'width: 5%;';
                $width2 = 'width: 25%;';
                // $width3 = 'width: 8%;';
                $width4 = 'width: 10%;';
                $width5 = 'width: 10%;';
                // $width6 = 'width: 10%;';
                $width7 = 'width: 15%;';
                // $width8 = 'width: 10%;';
                $width9 = 'width: 10%;';
                $width10 = 'width: 10%;';
                $width11 = 'width: 15%;';
                $width12 = 'width: 10%;';
            }
        }
        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';

        // if(isset($item_quantity_purchase_order)) {
        //     $table .= '<td style="'.$width3.'text-align: center;font-weight: bold;">'._l('item_quantity').'</td>';
        // }
        if (isset($item_unit_purchase_order)) {
            $table .= '<td style="' . $width5 . 'text-align: center;font-weight: bold;">' . _l('item_unit') . '</td>';
        }
        if (isset($item_quantity_suppliers_purchase_order)) {
            $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . '</td>';
        }
        // if(isset($item_price_expected_purchase_order)) {
        //     $table .= '<td style="'.$width6.'text-align: center;font-weight: bold;">'._l('price_expected').'</td>';
        // }
        if (isset($item_price_suppliers_purchase_order)) {
            $table .= '<td style="' . $width7 . 'text-align: center;font-weight: bold;">' . _l('ch_price') . '</td>';
        }
        // if(isset($item_amount_expected_purchase_order)) {
        //     $table .= '<td style="'.$width8.'text-align: center;font-weight: bold;">'._l('amount_expected_vnd').'</td>';
        // }
        // if (isset($item_promotion_suppliers_purchase_order)) {
        //     $table .= '<td style="' . $width9 . 'text-align: center;font-weight: bold;">' . _l('promotion_suppliers') . '</td>';
        // }
        if (isset($item_tax_purchase_order)) {
            $table .= '<td style="' . $width10 . 'text-align: center;font-weight: bold;">' . _l('tax') . '</td>';
        }
        if (isset($item_amount_suppliers_purchase_order)) {
            $table .= '<td style="' . $width11 . 'text-align: center;font-weight: bold;">' . _l('amount_suppliers_vnd') . '</td>';
        }
        if (isset($item_note_purchase_order)) {
            $table .= '<td style="' . $width12 . 'text-align: center;font-weight: bold;">' . _l('note') . '</td>';
        }
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_suppliers = 0;
        $sum_price_expected = 0;
        $sum_price_suppliers = 0;
        $sum_total_expected = 0;
        $sum_promotion_expected = 0;
        $sum_total_suppliers = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: left;">' . $dataItem->name . GetQuycach($value['product_id'], $value['type']) . '</td>';

            // if(isset($item_quantity_purchase_order)) {
            //     $table .= '<td style="'.$width3.'text-align: center;">'.number_format($value['quantity']).'</td>';
            //     $sum_quantity += $value['quantity'];
            // }
            if (isset($item_unit_purchase_order)) {
                $table .= '<td style="' . $width5 . 'text-align: center;">' . $dataItem->unit_name . '</td>';
            }
            if (isset($item_quantity_suppliers_purchase_order)) {
                $table .= '<td style="' . $width4 . 'text-align: center;">' . formatNumber($value['quantity_suppliers']) . '</td>';
                $sum_quantity_suppliers += $value['quantity_suppliers'];
            }
            // if(isset($item_price_expected_purchase_order)) {
            //     $table .= '<td style="'.$width6.'text-align: center;">'.number_format($value['price_expected']).'</td>';
            //     $sum_price_expected += $value['price_expected'];
            // }
            if (isset($item_price_suppliers_purchase_order)) {
                $table .= '<td style="' . $width7 . 'text-align: center;">' . number_format($value['price_suppliers']) . '</td>';
                $sum_price_suppliers += $value['price_suppliers'];
            }
            // if(isset($item_amount_expected_purchase_order)) {
            //     $table .= '<td style="'.$width8.'text-align: right;">'.number_format($value['total_expected']).'</td>';
            //     $sum_total_expected += $value['total_expected'];
            // }
            // if (isset($item_promotion_suppliers_purchase_order)) {
            //     $table .= '<td style="' . $width9 . 'text-align: right;">' . number_format($value['promotion_expected']) . '</td>';
            //     $sum_promotion_expected += $value['promotion_expected'];
            // }
            if (isset($item_tax_purchase_order)) {
                $table .= '<td style="' . $width10 . 'text-align: center;">' . number_format($value['tax_rate']) . ' %</td>';
            }
            if (isset($item_amount_suppliers_purchase_order)) {
                $table .= '<td style="' . $width11 . 'text-align: right;">' . number_format($value['total_suppliers']) . '</td>';
                $sum_total_suppliers += $value['total_suppliers'];
            }
            if (isset($item_note_purchase_order)) {
                $table .= '<td style="' . $width12 . 'text-align: center;">' . $value['note'] . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        // if(isset($item_quantity_purchase_order)) {
        //     $table .= '<td style="text-align: center;">'.number_format($sum_quantity).'</td>';
        // }
        if (isset($item_quantity_suppliers_purchase_order)) {
            $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_suppliers) . '</td>';
        }
        // if(isset($item_price_expected_purchase_order)) {
        //     $table .= '<td style="text-align: center;">'.number_format($sum_price_expected).'</td>';
        // }
        if (isset($item_price_suppliers_purchase_order)) {
            $table .= '<td style="text-align: center;">' . number_format($sum_price_suppliers) . '</td>';
        }
        // if(isset($item_amount_expected_purchase_order)) {
        //     $table .= '<td style="text-align: right;">'.number_format($sum_total_expected).'</td>';
        // }
        // if (isset($item_promotion_suppliers_purchase_order)) {
        //     $table .= '<td style="text-align: right;">' . number_format($sum_promotion_expected) . '</td>';
        // }
        if (isset($item_tax_purchase_order)) {
            $table .= '<td></td>';
        }
        if (isset($item_amount_suppliers_purchase_order)) {
            $table .= '<td style="text-align: right;">' . number_format($sum_total_suppliers) . '</td>';
        }
        if (isset($item_note_purchase_order)) {
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
                            <span style="font-weight: bold;">Giám đốc</span><br>
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
        $pdf = print_pdf_P_ch($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    public function get_items_all($product_id = '', $type = '')
    {
        echo json_encode($this->invoice_items_model->get_full_item($product_id, $type));
        die;
    }

    //tạo đơn hàng tổng từ ycmh
    public function create_detail_all()
    {
        if (!has_permission('purchase_order', '', 'create')) {
            access_denied('purchase_order');
        }
        $id = $this->input->get('id');
        if ($this->input->post()) {
            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }

            $data = $this->input->post();

            if (isset($data['items']) && count($data['items']) > 0) {
                $id = $this->purchase_order_model->add_all($data);
            }
            if ($id) {
                $this->session->set_flashdata('purchase_order_id_modal', $id);
                set_alert('success', _l('ch_added_successfuly'));
                redirect(admin_url('purchase_order'));
            }
        }
        $data['type_of_document'] = 1;
        $title = _l('add_new', _l('ch_purchase_order'));
        // $data['purchase'] = $this->purchases_model->get_items_purchase_order_all($id);
        $data['purchase'] = $this->purchases_model->get_items_purchase_order_all_plan($id);
        $html = '<option></option>';
        foreach ($data['purchase'] as $key => $value) {
            if ($key == 0) {
                $html .= '<optgroup data-text ="' . $value['name'] . '" label="' . $value['name'] . '">';
            } else if ($value['id'] == 'h') {
                $html .= '</optgroup>';
                $html .= '<optgroup  data-text ="' . $value['name'] . '" label="' . $value['name'] . '">';
            } else {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);

                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);

                if ($value['type_items'] == 'tools' || $value['type_items'] == 'items') {
                    $html .= '<option   data-text = "[' . $value['code_item'] . '] ' . $value['name'] . '" data-type_items="' . $value['type_items'] . '"   quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . '  value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                } else {
                    $html .= '<option   data-text = "[' . $value['code_item'] . '] ' . $value['name'] . '" data-type_items="' . $value['type_items'] . '" data-mode="' . $value['mode'] . '"  quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . '  value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
                }
            }
        }
        $html .= '</optgroup>';
        $data['html'] = $html;
        $html1 = '';
        foreach ($data['purchase'] as $key => $value) {
            if ($value['id'] != 'h') {
                $value['name'] = str_replace('"', '', $value['name']);
                $value['code_item'] = str_replace('"', '', $value['code_item']);
                $value['name'] = str_replace("'", '', $value['name']);
                $value['code_item'] = str_replace("'", '', $value['code_item']);
                $text = '<span><b>[' . $value['code_item'] . '] ' . $value['name'] . '</b></span>';
                $get_items = get_items($value['id'], $value['type_items']);

                if ($value['type_items'] == 'nvl') {
                    $text .= "<span class='text-muted'><br><span class='label label-primary'>Nguyên vật liệu</span></span>";
                } elseif ($value['type_items'] == 'product') {
                    $text .= "<span class='text-muted'><span class='label label-warning'>Bán thành phẩm</span></span>";
                }
                if ($value['code_plan'] != null) {
                    $text .= "<span class='text-muted'><br>KHSX:" . $value['code_plan'] . "<br></span>";
                }

                $html1 .= '<option data-content="' . $text . '" quantity_warehoue="' . $value['quantity_warehoue'] . '" data-id=' . $value['type_items'] . ' value="' . $value['id'] . '__' . $value['idd'] . '">[' . $value['code_item'] . '] ' . $value['name'] . '</option>';
            }
        }
        $data['html1'] = $html1;

        $data['id'] = $id;
        $data['idd'] = 0;
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $data['currency'] = get_table_where('tblcurrencies');

        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/create_detail_all', $data);
    }

    public function purchases_detail_all($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('purchase_order', '', 'create')) {
                    access_denied('purchase_order');
                }

                $data = $this->input->post();

                if (isset($data['items']) && count($data['items']) > 0) {
                    $id = $this->purchase_order_model->add($data);
                }

                if ($id) {
                    set_alert('success', _l('ch_added_successfuly'));
                    $this->session->set_flashdata('purchase_order_id_modal', $id);
                    redirect(admin_url('purchase_order'));
                }
            } else {
                if (!has_permission('purchase_order', '', 'edit')) {
                    access_denied('purchase_order');
                }
                $success = $this->purchase_order_model->update_all($this->input->post(), $id);
                if ($success == true) {
                    $this->load->model('misc_model');
                    $this->misc_model->changeRowNew_model('tblpurchase_order', $id);

                    set_alert('success', _l('ch_updated_successfuly'));
                }
                redirect(admin_url('purchase_order/purchases_detail_all/' . $id));
            }
        }
        if ($id == '') {
            if (!has_permission('purchase_order', '', 'create')) {
                access_denied('purchase_order');
            }
            $title = _l('add_new', _l('ch_purchase_order'));
        } else {
            if (!has_permission('purchase_order', '', 'edit')) {
                access_denied('purchase_order');
            }
            $title = _l('edit', _l('ch_purchase_order'));
            $data['items'] = $this->purchase_order_model->get($id);
        }
        $data['staff'] = get_table_where('tblstaff');
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['tax'] = get_table_where('tbltaxes');
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $type_items = get_table_where('tbltype_items', array('active' => 1));
        $count = 0;
        $data['type_items'][0] = array('type' => '-1', 'name' => _l('task_list_all'));
        foreach ($type_items as $key => $value) {
            $count++;
            $data['type_items'][$count] = $value;
        }
        $data['title'] = $title;
        $this->load->view('admin/purchase_order/detail_all', $data);
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
            $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
            $data['total'] += $import->totalAll_suppliers - $import->price_other_expenses - $import->amount_paid;
        }
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/import/payment_all_modal', $data);
    }

    public function payment($id = '')
    {
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['id_import'] = $id;
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
        $data['import'] = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/import/payment_modal', $data);
    }

    public function pay_slip_all()
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
            if ($data['type_invoicesss'] == 2) {
                $data['id_old'] = trim($data['id_old'], ',');
                $totalss = 0;
                $id_olds = explode(',', $data['id_old']);
                foreach ($id_olds as $key => $value) {
                    $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
                    $totalss += $import->totalAll_suppliers - ($import->amount_paid + $import->price_other_expenses);
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
                $_data['date'] = date('Y-m-d H:i:s');
                $_data['id_costs'] = $data['id_costs'];
                $_data['staff_id'] = get_staff_user_id();
                $_data['receiver'] = $data['receiver'];
                $_data['payment_mode'] = $data['payment_mode'];
                $_data['payment'] = str_replace(',', '', $data['payment']);
                $_data['total'] = str_replace(',', '', $data['total']);
                $_data['note'] = $data['note'];
                $_data['id_supplierss'] = $data['id_supplierss'];
                $_data['type'] = 2;
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
                            $__data['type'] = 2;
                            $import = get_table_where('tblpurchase_order', array('id' => $value), '', 'row');
                            $__data['total'] = $import->totalAll_suppliers - $import->price_other_expenses;
                            $__data['payment'] = $import->totalAll_suppliers - $import->price_other_expenses;
                            $this->db->insert('tblpay_slip_detail', $__data);

                            $this->db->update('tblpurchase_order', array('amount_paid' => ($import->totalAll_suppliers - $import->price_other_expenses), 'money_arises' => ($import->totalAll_suppliers - $import->price_other_expenses - $import->amount_paid), 'status_pay' => 2), array('id' => $import->id));
                        }
                    }
                    $success = true;
                    $alert_type = 'success';
                    $message = _l('ch_pay_succes');
                }
            } elseif ($data['type_invoicesss'] == 1) {

                $data['id_old'] = trim($data['id_old'], ',');
                $totalss = 0;
                $id_olds = explode(',', $data['id_old']);
                foreach ($id_olds as $key => $value) {
                    $import = get_table_where('tblpurchase_invoice', array('id' => $value), '', 'row');
                    $totalss += $import->total_price_befor_vat - ($import->amount_paid + $import->price_other_expenses);
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

                            $this->db->update('tbl_outsource', array('amount_paid' => ($import->grand_total), 'status_pay' => 2), array('id' => $import->id));
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

    public function pay_slip($id = '')
    {
        $success = false;
        $alert_type = 'warning';
        $message = _l('ch_added_successfuly_not');
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
            $importsS = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
            // if (($importsS->totalAll_suppliers - ($importsS->amount_paid + $importsS->price_other_expenses)) < str_replace(',', '', $data['payment'])) {
            //     echo json_encode(array(
            //         'success' => true,
            //         'alert_type' => 'warning',
            //         'message' => 'Có sự thay đổi về giá trị vui lòng xem lại !'
            //     ));
            //     die;
            // }
            $_data['day_vouchers'] = to_sql_date($data['day_vouchers']);
            $_data['date'] = date('Y-m-d H:i:s');
            $_data['staff_id'] = get_staff_user_id();
            $_data['receiver'] = $data['receiver'];
            $_data['id_costs'] = $data['id_costs'];
            $_data['payment_mode'] = $data['payment_mode'];
            $_data['payment'] = str_replace(',', '', $data['payment']);
            $_data['total'] = str_replace(',', '', $data['total']);
            $_data['note'] = $data['note'];
            $imports = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
            $_data['id_supplierss'] = $imports->suppliers_id;
            $_data['type'] = 2;
            $_data['id_old'] = $id;
            $_data['prefix'] = get_option('prefix_pay_slip');
            $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
            $this->db->insert('tblpay_slip', $_data);
            $id_pay = $this->db->insert_id();
            if ($id_pay) {
                $__data['id_old'] = $id;
                $__data['id_pay_slip'] = $id_pay;
                $__data['type'] = 2;
                $__data['total'] = str_replace(',', '', $data['total']);
                $__data['payment'] = str_replace(',', '', $data['payment']);
                $this->db->insert('tblpay_slip_detail', $__data);
                $import = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
                $amount_paid = $import->amount_paid + $__data['payment'];
                if (($amount_paid + $import->price_other_expenses) >= $import->totalAll_suppliers) {
                    $status = 2;
                } else {
                    $status = 1;
                }
                $this->db->update('tblpurchase_order', array('amount_paid' => $amount_paid, 'status_pay' => $status), array('id' => $import->id));
                $success = true;
                $alert_type = 'success';
                $message = _l('ch_added_successfuly');
            }
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }

    public function count_all()
    {
        if (has_permission('purchase_order', '', 'view_own') && !is_admin()) {
            $count = get_table_where_select('count(*) as alls', 'tblpurchase_order', array('staff_create' => get_staff_user_id()), '', 'row');
            $status0 = get_table_where_select('count(*) as status0', 'tblpurchase_order', array('status' => 1, 'staff_create' => get_staff_user_id()), '', 'row');
            $status1 = get_table_where_select('count(*) as status1', 'tblpurchase_order', array('status' => 2, 'staff_create' => get_staff_user_id()), '', 'row');
            $status2 = get_table_where_select('count(*) as status2', 'tblpurchase_order', array('status' => 3, 'staff_create' => get_staff_user_id()), '', 'row');
            $red_invoice = get_table_where_select('count(*) as red_invoice', 'tblpurchase_order', array('red_invoice !=' => 0, 'staff_create' => get_staff_user_id()), '', 'row');
            $red_invoice_no = get_table_where_select('count(*) as red_invoice_no', 'tblpurchase_order', array('red_invoice' => 0, 'staff_create' => get_staff_user_id()), '', 'row');

            $this->db->select('count(*) as status_pay');
            $this->db->where('((tblpurchase_order.status_pay = 2 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 2))');
            $this->db->where('tblpurchase_order.staff_create', get_staff_user_id());
            $this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'LEFT');
            $status_pay = $this->db->get('tblpurchase_order')->row();

            $this->db->select('count(*) as status_pay1');
            $this->db->where('((tblpurchase_order.status_pay = 1 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 1))');
            $this->db->where('tblpurchase_order.staff_create', get_staff_user_id());
            $this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'LEFT');
            $status_pay1 = $this->db->get('tblpurchase_order')->row();

            $this->db->select('count(*) as status_pay0');
            $this->db->where('((tblpurchase_order.status_pay = 0 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 0))');
            $this->db->where('tblpurchase_order.staff_create', get_staff_user_id());
            $this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'LEFT');
            $status_pay0 = $this->db->get('tblpurchase_order')->row();


            $this->db->select('count(id) as import1');
            $this->db->where('SUBSTRING(cancel, 1, 5) = "1foso"');
            $this->db->where('tblpurchase_order.staff_create', get_staff_user_id());
            $import1 = $this->db->get('tblpurchase_order')->row();

            $this->db->select('COALESCE(count(tblpurchase_order.id),0) as import');
            $this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id', 'LEFT');
            $this->db->where('tblimport.id is NULL');
            $this->db->where('tblpurchase_order.staff_create', get_staff_user_id());
            $this->db->group_by('tblimport.id_order');
            $import = $this->db->get('tblpurchase_order')->row();
        } else {
            $count = get_table_where_select('count(*) as alls', 'tblpurchase_order', array(), '', 'row');
            $status0 = get_table_where_select('count(*) as status0', 'tblpurchase_order', array('status' => 1), '', 'row');
            $status1 = get_table_where_select('count(*) as status1', 'tblpurchase_order', array('status' => 2), '', 'row');
            $status2 = get_table_where_select('count(*) as status2', 'tblpurchase_order', array('status' => 3), '', 'row');
            $red_invoice = get_table_where_select('count(*) as red_invoice', 'tblpurchase_order', array('red_invoice !=' => 0), '', 'row');
            $red_invoice_no = get_table_where_select('count(*) as red_invoice_no', 'tblpurchase_order', array('red_invoice' => 0), '', 'row');
            $this->db->select('count(*) as status_pay');
            $this->db->where('((tblpurchase_order.status_pay = 2 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 2))');
            $this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'LEFT');
            $status_pay = $this->db->get('tblpurchase_order')->row();

            $this->db->select('count(*) as status_pay1');
            $this->db->where('((tblpurchase_order.status_pay = 1 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 1))');
            $this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'LEFT');
            $status_pay1 = $this->db->get('tblpurchase_order')->row();

            $this->db->select('count(*) as status_pay0');
            $this->db->where('((tblpurchase_order.status_pay = 0 AND tblpurchase_order.red_invoice = 0 ) or (tblpurchase_order.red_invoice != 0 AND tblpurchase_invoice.status = 0))');
            $this->db->join('tblpurchase_invoice', 'tblpurchase_invoice.id = tblpurchase_order.red_invoice', 'LEFT');
            $status_pay0 = $this->db->get('tblpurchase_order')->row();


            $this->db->select('count(id) as import1');
            $this->db->where('SUBSTRING(cancel, 1, 5) = "1foso"');
            $import1 = $this->db->get('tblpurchase_order')->row();

            $this->db->select('COALESCE(count(tblpurchase_order.id),0) as import');
            $this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id', 'LEFT');
            $this->db->where('tblimport.id is NULL');
            $this->db->group_by('tblimport.id_order');
            $import = $this->db->get('tblpurchase_order')->row();
        }
        $data['all'] = $count->alls;
        $data['status0'] = $status0->status0;
        $data['status1'] = $status1->status1;
        $data['status2'] = $status2->status2;
        $data['red_invoice'] = $red_invoice->red_invoice;
        $data['red_invoice_no'] = $red_invoice_no->red_invoice_no;
        $data['status_pay'] = $status_pay->status_pay;
        $data['status_pay0'] = $status_pay0->status_pay0;
        $data['status_pay1'] = $status_pay1->status_pay1;
        $data['import1'] = $import1->import1;
        $data['import'] = (!empty($import)) ? $import->import : 0;
        $data['import2'] = $count->alls - $import1->import1 - $import->import;


        echo json_encode($data);
    }

    public function evaluate_modal($id_purchase_order = '')
    {
        $data['check'] = '';
        if ($id_purchase_order != "") {
            $data['dataMain'] = get_table_where('tblpurchase_order_evaluate', array('id_purchase_order' => $id_purchase_order), '', 'row');
        }
        $this->load->view('admin/purchase_order/view_modal_evaluate', $data);
    }

    public function add_evaluate($id_purchase_order = '')
    {
        $data = $this->input->post();
        $in = array(
            'id_purchase_order' => $id_purchase_order,
            'points' => $data['points'],
            'note' => $data['note'],
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y-m-d H:i:s')
        );
        $insert_id = $this->db->insert('tblpurchase_order_evaluate', $in);
        $alert_type = 'danger';
        $message = _l('edit_slide_false');
        $success = false;
        if ($insert_id) {
            $get_code = get_table_where('tblpurchase_order', array('id' => $id_purchase_order), '', 'row');
            activity_log_v2('purchase', 'tblpurchase_order', $id_purchase_order, $get_code->prefix . '-' . $get_code->code, 'Đánh giá đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
            $alert_type = 'success';
            $message = _l('edit_slide_true');
            $success = true;
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function payment_all_outsource($value = '')
    {
        $data['costs'] = array();
        $this->costs_model->get_by_id(0, $data['costs']);
        $data['code'] = get_option('prefix_pay_slip') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblpay_slip') + 1);
        $datas = $this->input->post();
        $data['total'] = 0;
        $data['id_old'] = trim($datas['ids'], ',');
        foreach (explode(',', trim($datas['ids'], ',')) as $key => $value) {
            $import = get_table_where('tbl_outsource', array('id' => $value), '', 'row');
            $data['total'] += $import->grand_total - $import->amount_paid;
        }
        $data['payment_modes'] = get_table_where('tblpayment_modes', array('active' => 1));
        $this->load->view('admin/import/payment_all_modal_outsource', $data);
    }

    public function edit_evaluate($id_purchase_order = '')
    {
        $data = $this->input->post();
        $in = array(
            'points' => $data['points'],
            'note' => $data['note'],
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y-m-d H:i:s')
        );
        $this->db->where('id_purchase_order', $id_purchase_order);
        $insert_id = $this->db->update('tblpurchase_order_evaluate', $in);
        $alert_type = 'danger';
        $message = _l('edit_slide_false');
        $success = false;
        if ($insert_id) {
            $get_code = get_table_where('tblpurchase_order', array('id' => $id_purchase_order), '', 'row');
            activity_log_v2('purchase', 'tblpurchase_order', $id_purchase_order, $get_code->prefix . '-' . $get_code->code, 'Cập nhật đánh giá đơn đặt hàng [' . $get_code->prefix . '-' . $get_code->code . ']');
            $alert_type = 'success';
            $message = _l('edit_slide_true');
            $success = true;
        }
        echo json_encode(array(
            'success' => $success,
            'alert_type' => $alert_type,
            'message' => $message
        ));
        die;
    }
    public function import_orders()
    {
        // if (!$this->perAddOrders) {
        //     accessDenied();
        // }
        if ($this->input->post('save')) {
            $data = [];
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            $fullfile = $_FILES['file']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);

                return;
            }
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);

                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);

                return;
            }

            // if ($_FILES['userfile']['size'] > $this->allowed_file_size * 1024) {
            //     $this->session->set_flashdata('warning', lang('Không vượt quá '. $this->allowed_file_size. ' size'));
            //     redirect($_SERVER["HTTP_REFERER"]);
            //     return;
            // }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            /**  Load $inputFileName to a PHPExcel Object  **/
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('V');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 4; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            $options = [];
            $count = 0;
            $errors = '';
            $cRow = 4;
            $index_parent = 0;
            $index_parent_element = 0;
            $ref = '';
            $dataImport = [];
            foreach ($arraydata as $key => $value) {

                // 0: code: Số đơn hàng
                // 1: date: Ngày
                // 2: suppliers_id: Nhà cung cấp
                // 3: id_staff: Nhân viên phụ trách
                // 4: delivery_date: Ngày dự kiến giao hàng
                // 5: tax: thuế
                // 6: delivery_cost: Chi phí giao hàng
                // 7: reduce_cost: Khoảng giảm trừ
                // 8: discount_percent_suppliers: Chiết khấu đơn hàng(%)
                // 9: discount_suppliers: Chiết khấu đơn hàng(TM)
                // 10: total: Tổng cộng
                // 11: note: Ghi chú tổng
                // 12: item_type: Loại mặt hàng
                // 13: item_code: Mã thành phẩm
                // 14: item_name: Tên thành phẩm
                // 15: item_name: Màu sắc
                // 16: item_name: Kích thước
                // 17: unit: đơn vị
                // 18: quantity: Số lượng
                // 19: price: Giá
                // 20: amount: Tổng tiền mặt hàng
                // 21: note_item: Ghi chú mặt hàng
                $code = trim($value[0]);
                $date = $value[1];
                if (gettype($date) == 'double' || gettype($date) == 'int') {
                    $date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date));
                } else if (gettype($date) == 'string') {
                    $date = to_sql_date($date);
                }
                $suppliers_id = trim($value[2]);
                $id_staff = trim($value[3]);
                $delivery_date = $value[4];
                if (gettype($delivery_date) == 'double' || gettype($delivery_date) == 'int') {
                    $delivery_date = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($delivery_date));
                } else if (gettype($delivery_date) == 'string') {
                    $delivery_date = to_sql_date($delivery_date);
                }

                $tax = trim($value[5]);
                $delivery_cost = number_unformat($value[6]);
                $reduce_cost = number_unformat($value[7]);
                $discount_percent_suppliers = trim($value[8]);
                $discount_suppliers = number_unformat($value[9]);
                $total = number_unformat($value[10]);
                $note = trim($value[11]);
                $item_type = trim($value[12]);
                $item_code = trim($value[13]);
                $item_name = trim($value[14]);
                $color = trim($value[15]);
                $size = trim($value[16]);
                $unit = trim($value[17]);
                $quantity = number_unformat($value[18]);
                $price = number_unformat($value[19]);
                $amount = number_unformat($value[20]);
                $note_item = trim($value[21]);


                if (!empty($code) && $code != $ref) {
                    $dataImport[$index_parent]['code'] = $code;
                    $dataImport[$index_parent]['date'] = $date;
                    $dataImport[$index_parent]['suppliers_id'] = $suppliers_id;
                    $dataImport[$index_parent]['id_staff'] = $id_staff;
                    $dataImport[$index_parent]['delivery_date'] = $delivery_date;
                    $dataImport[$index_parent]['tax_all'] = $tax;
                    $dataImport[$index_parent]['delivery_cost'] = $delivery_cost;
                    $dataImport[$index_parent]['reduce_cost'] = $reduce_cost;
                    $dataImport[$index_parent]['discount_percent_suppliers'] = $discount_percent_suppliers;
                    $dataImport[$index_parent]['discount_suppliers'] = $discount_suppliers;
                    $dataImport[$index_parent]['total'] = $total;
                    $dataImport[$index_parent]['note'] = $note;
                    $parent_current = $index_parent;
                    $ref = $code;
                    $index_parent++;
                }

                $dataImport[$parent_current]['items'][] = [
                    'item_type' => $item_type,
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'unit' => $unit,
                    'color' => $color,
                    'size' => $size,
                    'quantity' => $quantity,
                    'price' => $price,
                    'amount' => $amount,
                    'note_item' => $note_item,
                ];
            }
            $listRef = [];
            if (!empty($dataImport)) {
                foreach ($dataImport as $key => $value) {
                    $date = $value['date'];
                    $code = $value['code'];
                    if ($this->purchase_order_model->checkExistOrders($code)) {
                        $errors .= '<div>Đơn hàng [' . $code . '] không thể thêm vì đã tồn tại trong phần mềm</div>';
                        continue;
                    }
                    $supplierName = $value['suppliers_id'];
                    $supplier = $this->purchase_order_model->getSuppliertByZcodeOrCompany($supplierName);
                    if (empty($supplier)) {
                        $errors .= '<div>Đơn hàng [' . $code . '] thêm không được vì nhà cung cấp [' . $supplierName . '] không tồn tại trong phầm mềm</div>';
                        continue;
                    }
                    $suppliers_id = $supplier['id'];



                    if (!empty($id_staff)) {
                        $staffName = $id_staff;
                        $staff = $this->site_model->getStaffByName($staffName);
                        if (empty($staff)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhân viên phụ trách [' . $staffName . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $id_staff = $staff['staffid'];
                    }
                    //end employee

                    //handling tax
                    $tax = $value['tax_all'];
                    $tax_id = 0;
                    $tax_rate = 0;
                    if (!empty($tax)) {
                        $dTax = $this->site_model->getTaxesByName($tax);
                        if (empty($dTax)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax . '] không tồn tại trong phần mềm</div>';
                            continue;
                        }
                        $tax_rate = $dTax['taxrate'];
                        $tax_id = $dTax['id'];
                    }
                    //end tax
                    //end handling transporters
                    $items = $value['items'];
                    if (empty($items)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại mặt hàng</div>';
                        continue;
                    }

                    //handling items
                    $note = $value['note'];
                    $count_items = 0;
                    $total_quantity = 0;
                    $total_amount_items = 0;
                    $total_tax_items = 0;
                    $total_discount_percent_items = 0;
                    $total_discount_direct_items = 0;
                    $grand_total_items = 0;

                    $discount_percent_suppliers = !empty($value['discount_percent_suppliers']) ? $value['discount_percent_suppliers'] : 0;
                    $discount_suppliers = !empty($value['discount_suppliers']) ? $value['discount_suppliers'] : 0;
                    $delivery_cost = !empty($value['delivery_cost']) ? $value['delivery_cost'] : 0;
                    $reduce_cost = !empty($value['reduce_cost']) ? $value['reduce_cost'] : 0;
                    $valtype_check_suppliers = ($discount_suppliers > 0) ? 2 : 1;
                    $grand_total = 0;
                    $flagErrorsItems = false;
                    $itemsIn  = [];

                    $total_expected_all = 0;
                    $total_suppliers_all = 0;
                    $total_novat = 0;
                    $promotion_expecteds = 0;

                    foreach ($items as $k => $val) {
                        if (empty($val['item_type'])) {
                            $errors .= '<div>Đơn hàng [' . $code . '] thêm không được vì không có loại mặt hàng</div>';
                            break;
                        }
                        $item_type = $val['item_type'];
                        $item_code = trim($val['item_code']);
                        if (empty($item_code)) continue;
                        if ($item_type == 1) {
                            $type_item = "nvl";
                            $item = $this->purchase_order_model->getNVLByCode($item_code);
                        } else if ($item_type == 2) {
                            $type_item = "product";
                            $item = $this->purchase_order_model->getProductsByCode($item_code);
                        } else if ($item_type == 3) {
                            $type_item = "tools";
                            $item = $this->purchase_order_model->getToolsByCode($item_code);
                        }
                        if (empty($item)) {
                            $errors .= '<div>Đơn hàng [' . $code . '] thêm không được vì mặt hàng [' . $item_code . '] không tồn tại trong phần mềm</div>';
                            break;
                        }

                        $item_id = $item['id'];
                        $quantity = $val['quantity'];
                        $price = $val['price'];
                        $note_item = $val['note_item'];
                        $amount = $quantity * $price;
                        $total_expected = $val['quantity'] * 0 * (1 + ($tax_rate / 100));
                        $total_suppliers = (($val['quantity'] * $price * (1 + ($tax_rate / 100))));
                        $total_novats = ($val['quantity'] * $price);
                        $promotion_expecteds += 0;
                        $itemsIn[] = array(
                            // 'id_purchase_order' => $id,
                            'product_id' => $item_id,
                            'type' => $type_item,
                            'quantity' => $val['quantity'],
                            'tax_id' => $tax_id,
                            'tax_rate' => $tax_rate,
                            'quantity_suppliers' => $val['quantity'],
                            'price_expected' => 0,
                            'price_suppliers' => $price,
                            'promotion_expected' => 0,
                            'total_expected' => $total_expected,
                            'total_suppliers' => $total_suppliers,
                            'note' => $val['note_item'],
                            'plan_id' => 0,
                        );

                        $total_expected_all += $total_expected;
                        $total_suppliers_all += $total_suppliers;
                        $total_novat += $total_novats;
                    }
                    // print_arrays($errors);
                    if (empty($itemsIn)) {
                        $errors .= '<div>Đơn hàng [' . $code . '] thêm không được vì không có mặt hàng</div>';
                        continue;
                    }
                    $price_expected = 0;
                    $price_suppliers = 0;

                    $sub_expected = 0;

                    $price_expected = $total_expected_all - $sub_expected;

                    if ($valtype_check_suppliers == 1) {
                        $sub_suppliers = $total_suppliers_all * $discount_percent_suppliers / 100;
                    } else if ($valtype_check_suppliers == 2) {
                        $sub_suppliers = $discount_percent_suppliers;
                    }
                    $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
                    $quotes = array(
                        'code' => $code,
                        'prefix' => get_option('prefix_purchase_order'),
                        'staff_create' => $id_staff,
                        'date' => $date,
                        'valtype_check_suppliers' => $valtype_check_suppliers,
                        'delivery_date' => $delivery_date,
                        'date_create' => date('Y:m:d H:i:s'),
                        'suppliers_id' => $suppliers_id,
                        'type_items' => -1,
                        'tax_all' => $tax_id,
                        'status' => 3,
                        'note' => $note,
                        'history_status' => $id_staff . ',' . date('Y:m:d H:i:s') . '|' . $id_staff . ',' . date('Y:m:d H:i:s') . '|' . $id_staff . ',' . date('Y:m:d H:i:s'),
                        'delivery_cost' => $delivery_cost,
                        'reduce_cost' => $reduce_cost,
                        'discount_percent_expected' => 0,
                        'discount_percent_suppliers' => $discount_percent_suppliers,
                        'totalAll_expected' => $total_expected_all,
                        'totalAll_suppliers' => $price_suppliers,
                        'price_expected' => $price_expected,
                        'price_suppliers' => $price_suppliers,
                        'total_novat' => $total_novat,
                        'promotion_expected' => $promotion_expecteds,
                        'plan_id' => '',
                    );
                    if ($this->db->insert('tblpurchase_order', $quotes)) {
                        $id = $this->db->insert_id();
                        foreach ($itemsIn as $k => $val) {
                            $val['id_purchase_order'] = $id;
                            if ($this->db->insert('tblpurchase_order_items', $val)) {
                                $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $suppliers_id, 'id_items' => $val['product_id'], 'type' => $val['type']), '', 'row');
                                if (!empty($ktr_supp)) {
                                    $this->db->update('tblmainstream_goods', array('price' => $val['price_suppliers']), array('id' => $ktr_supp->id));
                                } else {
                                    $_mainstream = array(
                                        'id_suppliers' => $suppliers_id,
                                        'id_items' => $val['product_id'],
                                        'type' => $val['type'],
                                        'price' => $val['price_suppliers'],
                                    );
                                    $this->db->insert('tblmainstream_goods', $_mainstream);
                                }
                            }
                        }
                        $count++;
                    }
                }
            }
            $data['errors'] = $errors;
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_data_add');
            }
            echo json_encode($data);
            die;
        } else {
            $data['tnh'] = true;
            $data['title'] = _l('Import đơn đặt hàng mua');
            $this->load->view('admin/purchase_order/import_orders', $data);
        }
    }
    public function print_pdf_qc($id = '')
    {
        ob_start();
        $data = new stdClass();
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'import'), '', 'row');
        $dataMain = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
        $supplier = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $table = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">' . _l('ch_check_qc_in') . '</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br>
            <span style="font-weight: bold;">' . _l('supplier') . ': </span><span>' . $supplier->company . '</span><br>';
        // $purchase_order = format_purchase_order_father_in($dataMain->id_order);
        // if (empty($purchase_order)) {
        //     $purchase_order = format_purchase_order_father_all_in($dataMain->id_order);
        // }
        // if ($purchase_order) {
        //     $data->content .= '<span style="font-weight: bold;">' . _l('code_old_purchase') . ': </span><span>' . $purchase_order . '</span><br>';
        // }
        $data->content .= '
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
        $dem_temp = 1;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_warehouse_localtion_import') {
                    $item_warehouse_localtion_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_unit_import') {
                    $item_unit_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_quantity_import') {
                    $item_quantity_import = true;
                }
                if ($value == 'item_quantity_confirm_import') {
                    $item_quantity_confirm_import = true;
                }
                if ($value == 'item_price_import') {
                    $item_price_import = true;
                }
                if ($value == 'item_promotion_suppliers_import') {
                    $item_promotion_suppliers_import = true;
                }
                if ($value == 'item_tax_import') {
                    $item_tax_import = true;
                }
                if ($value == 'item_invoice_total_import') {
                    $item_invoice_total_import = true;
                }
                if ($value == 'item_note_import') {
                    $item_note_import = true;
                }
            }
        }
        $width1 = 'width: 10%;';
        $width2 = 'width: 40%;';
        $width3 = 'width: 25%;';
        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
        // $table .= '<td style="' . $width12 . 'text-align: center;font-weight: bold;">' . _l('Lot') . '</td>';
        // $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('warehouse_localtion') . '</td>';
        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . ' đơn vị mua </td>';
        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . ' đơn vị vào kho </td>';
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_net = 0;
        $sum_price = 0;
        $sum_promotion_suppliers = 0;
        $quantity_stock = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: left;">' . $dataItem->name . '(' . $dataItem->code . ')</td>';
            // $table .= '<td style="' . $width12 . 'text-align: center;">' . $value['lot_code'] . '</td>';
            // if (!empty($dataLocaltion)) {
            //     $table .= '<td style="' . $width3 . 'text-align: center;">' . $dataLocaltion->name_parent . '</td>';
            // } else {
            //     $table .= '<td></td>';
            // }
            $table .= '<td style="' . $width3 . 'text-align: center;">' . formatNumber($value['quantity_suppliers']) . '/' . $dataItem->unit_name . '</td>';
            $table .= '<td style="' . $width3 . 'text-align: center;">' . formatNumber($value['quantity_stock']) . '/' . $dataItem->unit_name_stock . '</td>';
            $sum_quantity_net += $value['quantity_suppliers'];
            $quantity_stock += $value['quantity_stock'];

            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_net) . '</td>';
        $table .= '<td style="text-align: center;">' . formatNumber($quantity_stock) . '</td>';
        $table .= '<td></td>';
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
        $data->title = lang('Phiếu kiểm tra');
        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }


    public function price_suppliers($id_detai = '', $id = '')
    {
        // if (!has_permission('purchase_order', '', 'edit_price')){
        //     $data['success'] = 'warning';
        //     $data['messeger'] = 'Bạn không có quyền';
        //     echo json_encode($data);die;
        // }
        if (get_staff_user_id() != 67) {
            $data['success'] = 'warning';
            $data['messeger'] = 'Bạn không có quyền';
            echo json_encode($data);
            die;
        }
        $data =  $this->input->post();
        $ktr = get_table_where('tblpurchase_order_items', array('id' => $id_detai), '', 'row_array');
        if (!empty($ktr)) {
            $item = $ktr;
            // price_suppliers
            if (empty($total)) {
                $total = 0;
            }
            $total = str_replace(',', '', $data['data_input']);
            $total_suppliers = (($item['quantity_payment'] * $total * (1 + ($item['tax_rate'] / 100)))  - $item['promotion_expected']);
            $total_supplierss = $total_suppliers;
            $this->db->update('tblpurchase_order_items', array('price_suppliers' => $total, 'total_suppliers' => $total_suppliers), array('id' => $id_detai));
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;
            $main_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row_array');
            $main_items_order = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
            foreach ($main_items_order as $key => $item) {
                $total_suppliers = (($item['quantity_payment'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100)))  - $item['promotion_expected']);
                $total_novats = ($item['quantity_payment'] * $item['price_suppliers']);
                $total_suppliers_all += $total_suppliers;
                $total_novat += $total_novats;
            }
            if ($main_order['valtype_check_suppliers'] == 1) {
                $sub_suppliers = $total_suppliers_all * $main_order['discount_percent_suppliers'] / 100;
            } else if ($main_order['valtype_check_suppliers'] == 2) {
                $sub_suppliers = $main_order['discount_percent_suppliers'];
            }
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $main_order['delivery_cost'] - $main_order['reduce_cost'];
            $total_dqd = $price_suppliers * $main_order['amount_to_vnd'];
            $total_novat_dqd = $total_novat * $main_order['amount_to_vnd'];

            $_items =  array(
                'totalAll_suppliers' => $price_suppliers,
                'price_suppliers' => $price_suppliers,
                'total_cqd' => $price_suppliers,
                'total_novat' => $total_novat,
                'total_dqd' => $price_suppliers * $main_order['amount_to_vnd'],
            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
            // $this->db->update('tblimport', array('total' => $total_suppliers, 'total_novat' => $total), array('id' => $id));

            $import  = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
            $amount_paid = ($import->amount_paid + $import->price_other_expenses) - $import->totalAll_suppliers;

            if (($amount_paid) < 0) {
                $status = 1;
            } elseif ($amount_paid == 0) {
                $status = 2;
            } elseif ($amount_paid > 0) {
                $status = 2;
            }
            if (($import->amount_paid + $import->price_other_expenses) == 0) {
                $status = 0;
            }
            $this->db->update('tblpurchase_order', array('status_pay' => $status), array('id' => $import->id));
        }

        $totals['id'] = $data['id'];
        $totals['total'] = number_format($total);
        $totals['subtotal'] = number_format($total_supplierss);


        $ktr_import = get_table_where('tblimport_items', array('id_purchase_order_items' => $id_detai));
        if (!empty($ktr_import)) {
            foreach ($ktr_import as $key => $value) {
                $amount = ($total * $value['quantity_payment'] - $value['promotion_suppliers_1'] * $value['quantity_payment']) * ($value['tax_rate'] / 100) + ($total * $value['quantity_payment'] - $value['promotion_suppliers_1'] * $value['quantity_payment']);

                $this->db->update('tblimport_items', array('price' => $total, 'amount' => $amount), array('id' => $value['id']));
                $this->Updata_price_import($value['id_import']);
                $this->db->update('tblwarehouse_product', array('price' => $total), array('import_id' => $value['id_import'], 'product_id' => $value['product_id'], 'type_items' => $value['type'], 'price' => $value['price']));
            }
        }

        $totals['success'] = 'success';
        $totals['messeger'] = 'Cập nhật giá thành công';
        echo json_encode($totals);
    }
    public function Updata_price_import($id = '')
    {
        $total = 0;
        $total_novat = 0;
        $main_order = get_table_where('tblimport', array('id' => $id), '', 'row');
        $main_items_order = get_table_where('tblimport_items', array('id_import' => $id));
        foreach ($main_items_order as $key => $item) {
            $total += $item['amount'];
            $total_novat += $item['price'] * $item['quantity_payment'] - $item['promotion_suppliers_1'] * $item['quantity_payment'];
        }
        $this->db->update('tblimport', array('total' => $total, 'total_novat' => $total_novat), array('id' => $id));
    }

    public function searchQR()
    {
        $code = $this->input->post('code');
        $response = $this->products_model->searchQR($code);
        echo json_encode($response);
    }

    public function endPurchaseOrder()
    {
        $data = [];
        if (!has_permission('purchase_order', '', 'edit')) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo responseData($data);
            return;
        }

        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $purchase_order = get_table_where('tblpurchase_order', ['id' => $id], '', 'row_array');
        $is_end = $purchase_order['is_end'];
        if ($status == $is_end) {
            $data['result'] = 0;
            $data['message'] = lang('Vui lòng làm mới danh sách');
            echo responseData($data);
            return;
        }

        $option = [
            'is_end' => $status,
            'user_end' => get_staff_user_id(),
            'date_end' => date('Y-m-d H:i:s'),
        ];

        $rs = $this->db->where('id', $id)->update('tblpurchase_order', $option);
        if ($rs) {
            $data['result'] = 1;
            $data['message'] = lang('Thao tác thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thao tác thất bại');
        }

        echo responseData($data);
    }
}
