<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_overcome_products extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('purchases_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('transfer_model');

        $this->preViewSuggestOvercomeProduct = true;
        $this->preViewOwnSuggestOvercomeProduct = true;
        $this->preAddSuggestOvercomeProduct = true;
        $this->preEditSuggestOvercomeProduct = true;
        $this->preApproveSuggestOvercomeProduct = true;
        $this->preDeleteSuggestOvercomeProduct = true;
    }

    public function index(){
        if (!$this->preViewSuggestOvercomeProduct && !$this->preViewOwnSuggestOvercomeProduct){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_overcome_products');
        $this->load->view('admin/suggest_overcome_products/index', $data);
    }

    public function getSuggestOvercomeProducts(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $tbOrders = "(
            SELECT
                tbl_orders.id as object_id,
                tbl_orders.reference_no as reference_no
            FROM tbl_orders
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no as reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";
        $aColumns = [
            'tbl_suggest_overcome_product.id as id',
            'tbl_suggest_overcome_product.reference_no as reference_no',
            'tbl_suggest_overcome_product.date as date',
            'tbl_productions_orders_details.reference_no as reference_no_pod',
            'IF(tbl_productions_orders_details.object_type = "orders",tb_orders.reference_no,tb_business_plan.reference_no) as orders',
            'tbl_suggest_overcome_product.date_import as date_import',
            'tbl_suggest_overcome_product.employee_id as employee_id',
            'tbl_suggest_overcome_product.created_by as created_by',
            '1',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_overcome_product';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_suggest_overcome_product.pod_id',
            'LEFT JOIN '.$tbOrders.' ON tb_orders.object_id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"',
            'LEFT JOIN '.$tbBusinessPlan.' ON tb_business_plan.object_id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tbl_suggest_overcome_product"'
        ];

        if (!$this->preViewSuggestOvercomeProduct) {
            array_push($where,'AND tbl_suggest_overcome_product.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_overcome_product.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_overcome_product.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_overcome_product.status',
            'tbl_suggest_overcome_product.date_status',
            'tbl_suggest_overcome_product.staff_status',
            '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tbl_suggest_overcome_product.id AND tbl_category_recommended.name_table="tbl_suggest_overcome_product") as countTask',
            'tbl_category_recommended.id as category_recommended_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_overcome_products/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['reference_no_pod']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . $aRow['orders'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dhau($aRow['date_import']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 1)\' id=\'agree\' suggest_purchase_id=\''.$aRow['id'].'\' value=\'1\' class=\'btn btn-success\'>'.lang('tnh_agree').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-danger po">'.lang('Chưa duyệt').'</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="'.lang('tnh_agree').'" data-content="<p><a onclick=\'agree(this, '.$aRow['id'].', 0)\' id=\'agree\' suggest_purchase_id=\''.$aRow['id'].'\' value=\'0\' class=\'btn btn-danger\'>'.lang('Hủy duyệt').'</a><button class=\'btn po-close\'>'.lang('close').'</button></p>" class="label label-success po">'.lang('Đã duyệt').'</span></div>';
                $_data.= '<div style="margin-top: 5px"> Người duyệt: '.get_staff_full_name($aRow['staff_status']).'</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">'.$_data.'</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['employee_id']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';
            if (!has_permission('tasks', '', 'create')) {
                $row[] = '';
            } else {
                $task = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . $aRow['category_recommended_id']) . '\')">Tạo công việc</a>';
                if (!empty($aRow['countTask'])) {
                    $data_tasks = get_table_where('tbltasks', ['suggest_id' => $aRow['id'], 'category_recommended_id' => $aRow['category_recommended_id']], '', 'result_array', '', 'tbltasks.id,tbltasks.name');
                    $__data = '';
                    $_data = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $aRow['countTask'] . ' Phiếu
                        </button>';
                    foreach ($data_tasks as $kk => $vv) {
                        $__data .= '<li><a href="' . admin_url('tasks/view') . $vv['id'] . '" class="display-block main-tasks-table-href-name mbot5" onclick="init_task_modal(' . $vv['id'] . '); return false;">' . $vv['name'] . '</a>';
                    }
                    $_data .= '<ul style="top:100%;bottom:unset;left:unset;right: 12%" class="dropdown-menu ch_foso">' . $__data;
                    $_data .= '</ul>';
                    $_data .= '</div>';
                    $task .= $_data;
                    // $column[15] .= '<br/><span class="dropdown-toggle no_background label label-info mtop10">' . $aRow['countTask'] . ' phiếu công việc . </span>';
                    // '(SELECT count(tbltasks.id) FROM tbltasks WHERE rel_id = tblinternal_proposal.id AND rel_type="internal_proposal") as countTask',

                }
                $row[] = $task;
            }

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_overcome_products/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestOvercomeProduct ? '<a href="' . base_url('admin/suggest_overcome_products/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestOvercomeProduct ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_overcome_products/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div style="width: 120px">'.$actions.'</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_overcome_product.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('date_import', lang("Ngày Nhập"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_overcome_product');
                    $date = to_sql_date($this->input->post('date'), true);
                    $pod_id = $this->input->post('pod_id');
                    $branch_id = $this->input->post('branch_id');
                    $employee_id = !empty($this->input->post('employee_id')) ? $this->input->post('employee_id') : 0;
                    $date_import = to_sql_date($this->input->post('date_import'));
                    $note = ($this->input->post('note'));
                    $dtPod = get_table_where('tbl_productions_orders_details',['id' => $pod_id],'','row_array');
                    $object_id = $dtPod['object_id'];
                    $object_type = $dtPod['object_type'];
                    $total_quantity = 0;
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $items_id = $this->input->post('item_id')[$value];
                            $arr_item = explode('__', $items_id);
                            $type_item = $arr_item[1];
                            $item_id = $arr_item[0];
                            if ($type_item == "products") {
                                $info_item = $this->products_model->rowProduct($item_id);
                            }
                            if (empty($info_item)) continue;

                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quantity_kien = number_unformat($this->input->post('quantity_kien')[$value]);
                            $quantity_kg = number_unformat($this->input->post('quantity_kg')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'quantity' => $quantity,
                                'quantity_kien' => $quantity_kien,
                                'quantity_kg' => $quantity_kg,
                                'standard' => $standard,
                            ];

                            $total_quantity += $quantity;
                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'pod_id' => $pod_id,
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'total_quantity' => $total_quantity,
                        'date_import' => $date_import,
                        'employee_id' => $employee_id,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_overcome_product',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_overcome_product') == $reference_no) {
                            updateReference('suggest_overcome_product');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_overcome_product_id'] = $id;
                            $this->db->insert('tbl_suggest_overcome_product_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_overcome_product',
                            'table_obj' => 'tbl_suggest_overcome_product',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu nhập kho TP') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            } else {
                $this->db->select('tbl_suggest_overcome_product.*');
                $this->db->from('tbl_suggest_overcome_product');
                $this->db->where('tbl_suggest_overcome_product.id',$id);
                $dtSuggestPurchase = $this->db->get()->row_array();
                if ($dtSuggestPurchase['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_overcome_product.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('date_import', lang("Ngày Nhập"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $pod_id = $this->input->post('pod_id');
                    $branch_id = $this->input->post('branch_id');
                    $employee_id = !empty($this->input->post('employee_id')) ? $this->input->post('employee_id') : 0;
                    $date_import = to_sql_date($this->input->post('date_import'));
                    $note = ($this->input->post('note'));
                    $dtPod = get_table_where('tbl_productions_orders_details',['id' => $pod_id],'','row_array');
                    $object_id = $dtPod['object_id'];
                    $object_type = $dtPod['object_type'];
                    $total_quantity = 0;
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $items_id = $this->input->post('item_id')[$value];
                            $arr_item = explode('__', $items_id);
                            $type_item = $arr_item[1];
                            $item_id = $arr_item[0];
                            if ($type_item == "products") {
                                $info_item = $this->products_model->rowProduct($item_id);
                            }
                            if (empty($info_item)) continue;

                            $suggest_purchase_product_item_id = !empty($this->input->post('suggest_purchase_product_item_id')[$value]) ? $this->input->post('suggest_purchase_product_item_id')[$value] : 0;
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quantity_kien = number_unformat($this->input->post('quantity_kien')[$value]);
                            $quantity_kg = number_unformat($this->input->post('quantity_kg')[$value]);
                            $standard = ($this->input->post('standard')[$value]);
                            $items[] = [
                                'id' => $suggest_purchase_product_item_id,
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'quantity' => $quantity,
                                'quantity_kien' => $quantity_kien,
                                'quantity_kg' => $quantity_kg,
                                'standard' => $standard,
                            ];

                            $total_quantity += $quantity;
                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'date' => $date,
                        'pod_id' => $pod_id,
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'total_quantity' => $total_quantity,
                        'date_import' => $date_import,
                        'employee_id' => $employee_id,
                        'note' => $note,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_overcome_product',$fields);
                    if ($success){
                        $this->db->where('suggest_overcome_product_id',$id);
                        $this->db->delete('tbl_suggest_overcome_product_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_overcome_product_id'] = $id;
                            $this->db->insert('tbl_suggest_overcome_product_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_overcome_product',
                            'table_obj' => 'tbl_suggest_overcome_product',
                            'id_obj' => $id,
                            'name_obj' => $dtSuggestPurchase['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu nhập kho TP') . ' [' . $dtSuggestPurchase['reference_no'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);die();
            }
        } else {
            if (empty($id)){
                if (!$this->preAddSuggestOvercomeProduct){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_overcome_products');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_overcome_products'), 'page' => lang('dt_suggest_overcome_products')), array('link' => '#', 'page' => lang('dt_add_suggest_overcome_products'))];
            } else {
                if (!$this->preEditSuggestOvercomeProduct){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_overcome_product.*');
                $this->db->from('tbl_suggest_overcome_product');
                $this->db->where('tbl_suggest_overcome_product.id',$id);
                $dtSuggestPurchase = $this->db->get()->row_array();

                if ($dtSuggestPurchase['status'] == 1){
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_overcome_product_item.*');
                $this->db->from('tbl_suggest_overcome_product_item');
                $this->db->where('tbl_suggest_overcome_product_item.suggest_overcome_product_id',$id);
                $dtSuggestPurchaseItems = $this->db->get()->result_array();

                $data['dtSuggestPurchase'] = $dtSuggestPurchase;
                $data['dtSuggestPurchaseItems'] = $dtSuggestPurchaseItems;
                $data['title'] = lang('dt_edit_suggest_overcome_products');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_overcome_products'), 'page' => lang('dt_suggest_overcome_products')), array('link' => '#', 'page' => lang('dt_edit_suggest_overcome_products'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_overcome_product');;
        $this->load->view('admin/suggest_overcome_products/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_overcome_products');

        $this->db->select('tbl_suggest_overcome_product.*');
        $this->db->from('tbl_suggest_overcome_product');
        $this->db->where('tbl_suggest_overcome_product.id',$id);
        $dtSuggestPurchase = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_overcome_product_item.*');
        $this->db->from('tbl_suggest_overcome_product_item');
        $this->db->where('tbl_suggest_overcome_product_item.suggest_overcome_product_id',$id);
        $dtSuggestPurchaseItems = $this->db->get()->result_array();

        $data['dtSuggestPurchase'] = $dtSuggestPurchase;
        $data['dtSuggestPurchaseItems'] = $dtSuggestPurchaseItems;
        $this->load->view('admin/suggest_overcome_products/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestOvercomeProduct) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_purchase_id = $this->input->post('suggest_purchase_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_overcome_product.*');
        $this->db->from('tbl_suggest_overcome_product');
        $this->db->where('tbl_suggest_overcome_product.id',$suggest_purchase_id);
        $dtSuggestPurchase = $this->db->get()->row_array();
        if (empty($dtSuggestPurchase)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {
            if (($dtSuggestPurchase['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data); return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id',$suggest_purchase_id);
            $up = $this->db->update('tbl_suggest_overcome_product',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_overcome_product',
                    'table_obj' => 'tbl_suggest_overcome_product',
                    'id_obj' => $suggest_purchase_id,
                    'name_obj' => $dtSuggestPurchase['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu nhập kho TP') . ' [' . $dtSuggestPurchase['reference_no'] . ']',
                    'actions' => 'approved'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function delete($id){
        if (!$this->preDeleteSuggestOvercomeProduct){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_overcome_product.*');
        $this->db->from('tbl_suggest_overcome_product');
        $this->db->where('tbl_suggest_overcome_product.id',$id);
        $dtSuggestPurchase = $this->db->get()->row_array();
        if (empty($dtSuggestPurchase)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtSuggestPurchase['status'] == 1){
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_overcome_product');
        if ($success){
            $this->db->where('tbl_suggest_overcome_product_item.suggest_overcome_product_id',$id);
            $this->db->delete('tbl_suggest_overcome_product_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_overcome_product',
                'table_obj' => 'tbl_suggest_overcome_product',
                'id_obj' => $id,
                'name_obj' => $dtSuggestPurchase['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu nhập kho TP') . ' [' . $dtSuggestPurchase['reference_no'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function searchPod($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders_details.id as id, 
            tbl_productions_orders_details.reference_no as text
        ', false);
        $this->db->from('tbl_productions_orders_details');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Lệch sản xuất chi tiết'), 'children' => $pod];
        if (!empty($id)){
            $dtPod = get_table_where('tbl_productions_orders_details',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtPod['id'], 'text' => $dtPod['reference_no']];
        }
        echo json_encode($data);
    }

    function searchProductsSelect2($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $pod_id = !empty($this->input->get('pod_id')) ? $this->input->get('pod_id') : 0;
        $limit = get_option('select2_limit');
        if (!empty($pod_id)){
            $this->db->select('  
                CONCAT(tbl_products.id, "__products") as id, 
                CONCAT(tbl_products.code) as text, 
                tbl_products.name as item_name, 
                IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, 
                tblunits.unit as unit_name, 
                tbl_products.price_sell as price_sell, 
                tbl_products.info as info, 
                tbl_products.code as item_code, 
                CONCAT(tbl_products.category_id, "__products") as category_id,
                tblsize.name as size_name,
                tbl_products.loss as loss,
                tbl_products.quantity_child_sheet as quantity_child_sheet,
                tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                tbl_products.mode_product as mode_product,
                tbl_products.product_name_customer as name_customer,
                tbl_products.loss as loss,
                unit_stock.unit as unit_stock'
            );
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->join('tbl_products','tbl_products.id = tbl_productions_orders_items.items_id');
            $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
            $this->db->join('tblunits unit_stock', 'tbl_products.conversion_unit = unit_stock.unitid', 'left');
            $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
            $this->db->where('tbl_productions_orders_details.id',$pod_id);
            if (!empty($term))
            {
                $this->db->group_start();
                $this->db->like('tbl_products.code', $term);
                $this->db->or_like('tbl_products.name', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $data['results'] = $this->db->get()->result_array();
        } else {
            $data['results'] = $this->products_model->searchProductsSelect2($term, $limit);
        }
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    public function exportExcel(){
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $tbOrders = "(
                SELECT
                    tbl_orders.id as object_id,
                    tbl_orders.reference_no as reference_no
                FROM tbl_orders
                WHERE tbl_orders.status_productions_orders != 0
            ) tb_orders";

            $tbBusinessPlan = "(
                SELECT
                    tbl_business_plan.id as object_id,
                    tbl_business_plan.reference_no as reference_no
                FROM tbl_business_plan
                WHERE tbl_business_plan.status_productions_orders != 0
            ) tb_business_plan";
            $this->db->select('
                tbl_suggest_overcome_product.id as id,
                tbl_suggest_overcome_product.reference_no as reference_no,
                tbl_suggest_overcome_product.date as date,
                tbl_productions_orders_details.reference_no as reference_no_pod,
                IF(tbl_productions_orders_details.object_type = "orders",tb_orders.reference_no,tb_business_plan.reference_no) as "order",
                tbl_brand.name as name_brand,
                tbl_category_products.name as name_category,
                tbl_species.name as name_species,
                tbl_products.name as name_product,
                tbl_suggest_overcome_product_item.quantity as quantity,
                tbl_products.quantity_sheet_bale as quantity_sheet_bale,
                tbl_suggest_overcome_product_item.quantity_kien as quantity_kien,
                tbl_suggest_overcome_product_item.quantity_kg as quantity_kg,
                tbl_suggest_overcome_product.date_import as date_import,
                tbl_products.time_stock as time_stock,
                tbl_suggest_overcome_product.employee_id as employee_id,
                tbl_suggest_overcome_product_item.standard as standard,
                tbl_products.images as images
            ');
            $this->db->from('tbl_suggest_overcome_product');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_suggest_overcome_product.pod_id', 'left');
            $this->db->join('tbl_suggest_overcome_product_item','tbl_suggest_overcome_product_item.suggest_overcome_product_id = tbl_suggest_overcome_product.id');
            $this->db->join('tbl_products','tbl_products.id = tbl_suggest_overcome_product_item.item_id');
            $this->db->join('tbl_category_products','tbl_category_products.id = tbl_products.category_id');
            $this->db->join('tbl_species','tbl_species.id = tbl_products.species','left');
            $this->db->join('tbl_brand','tbl_brand.id = tbl_products.brand_id','left');
            $this->db->join($tbOrders, 'tb_orders.object_id = tbl_suggest_overcome_product.object_id AND tbl_suggest_overcome_product.object_type = "orders"', 'left');
            $this->db->join($tbBusinessPlan, 'tb_business_plan.object_id = tbl_suggest_overcome_product.object_id AND tbl_suggest_overcome_product.object_type = "business_plan"', 'left');


            if (!$this->preViewSuggestOvercomeProduct) {
                $this->db->where('tbl_suggest_overcome_product.created_by = '.get_staff_user_id().'');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_overcome_product.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_overcome_product.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_overcome_product.id desc');
            $dtSuggestPurchase = $this->db->get()->result_array();


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
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('PHIẾU YÊU CẦU NHẬP KHO THÀNH PHẨM VƯỢT'))->getStyle("A1")->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Mã Lệnh SX');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mã Đơn Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Tên Brand');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Nhóm SP');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Tên Chủng Loại SP')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Chi Tiết')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Số Lượng Nhập')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Số Con');
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Số Kiện');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Số Kg');
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Ngày Nhập');
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Thời Gian Lưu Kho')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Người Phụ Trách Kiểm')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'Hình Ảnh SP');
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$sttRow.'', 'QR');
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
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $rowBegin = $sttRow;
            if (!empty($dtSuggestPurchase)) {
                foreach ($dtSuggestPurchase as $key => $value) {
                    $rowBegin++;
                    $images = '';
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['reference_no_pod']));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['order']));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_brand']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", ($value['name_category']))->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",($value['name_species']));
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['name_product'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['quantity']);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", ($value['quantity_sheet_bale']));
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['quantity_kien']));
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['quantity_kg']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin",_dhau($value['date_import']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", $value['time_stock'])->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", get_staff_full_name($value['employee_id']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", $value['standard'])->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    if (!empty($value['images'])) {
                        $images = 'uploads/products/' . $value['images'];
                    }
                    if (empty($images)) {
                        $images = 'assets/images/tnh/no_image.png';
                    }
                    if (!empty($images) && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(30);
                        $objDrawing1->setHeight(30);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('R' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(30);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", '')->getStyle("R$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", '');

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:S$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("J$rowBegin:M$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_nhap_kho_tp_vuot') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(30);
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