<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_request_zinc extends AdminController
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
        $this->preViewPurchaseRequestZinc = true;
        $this->preViewOwnPurchaseRequestZinc = true;
        $this->preAddPurchaseRequestZinc = true;
        $this->preEditPurchaseRequestZinc = true;
        $this->preApprovePurchaseRequestZinc = true;
        $this->preDeletePurchaseRequestZinc = true;
    }

    public function index()
    {
        if (!$this->preViewPurchaseRequestZinc && !$this->preViewOwnPurchaseRequestZinc) {
            access_denied();
        }
        $data['title'] = _l('ch_purchase_request_zinc');
        $this->load->view('admin/purchase_request_zinc/index', $data);
    }
    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_purchase_request_zinc');

        $this->db->select('tbl_purchase_request_zinc.*');
        $this->db->from('tbl_purchase_request_zinc');
        $this->db->where('tbl_purchase_request_zinc.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_purchase_request_zinc_item.*
        ');
        $this->db->from('tbl_purchase_request_zinc_item');
        $this->db->where('tbl_purchase_request_zinc_item.purchase_request_zinc_id', $id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/purchase_request_zinc/view', $data);
    }
    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_purchase_request_zinc.*');
        $this->db->from('tbl_purchase_request_zinc');
        $this->db->where('tbl_purchase_request_zinc.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_purchase_request_zinc.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('po_id', lang("Lệnh sản xuất"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('purchase_request_zinc');
                    $date = to_sql_date($this->input->post('date'), true);
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];

                            $item_id = $pod_id;
                            $type_item = 'materials';
                            $quabtity_total = number_unformat($this->input->post('quabtity_total')[$value]);
                            $code_file = ($this->input->post('code_file')[$value]);
                            $link_file = ($this->input->post('link_file')[$value]);
                            $operating_equipment = ($this->input->post('operating_equipment')[$value]);
                            $productivity_norms = number_unformat($this->input->post('productivity_norms')[$value]);


                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quabtity_total' => $quabtity_total,
                                'code_file' => $code_file,
                                'link_file' => $link_file,
                                'operating_equipment' => $operating_equipment,
                                'productivity_norms' => $productivity_norms
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'po_id' => $po_id,
                        'note' => $note,
                        'total' => $total,
                        'staff_create' => get_staff_user_id(),
                        'date_create' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_purchase_request_zinc', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('purchase_request_zinc') == $reference_no) {
                            updateReference('purchase_request_zinc');
                        }

                        foreach ($items as $key => $value) {
                            $value['purchase_request_zinc_id'] = $id;
                            $this->db->insert('tbl_purchase_request_zinc_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'purchase_request_zinc',
                            'table_obj' => 'tbl_purchase_request_zinc',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới phiếu yêu cầu ghi kẽm') . ' [' . $reference_no . ']',
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
                echo json_encode($data);
                die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];

                            $item_id = $pod_id;
                            $type_item = 'materials';
                            $quabtity_total = number_unformat($this->input->post('quabtity_total')[$value]);
                            $code_file = ($this->input->post('code_file')[$value]);
                            $link_file = ($this->input->post('link_file')[$value]);
                            $operating_equipment = ($this->input->post('operating_equipment')[$value]);
                            $productivity_norms = number_unformat($this->input->post('productivity_norms')[$value]);

                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quabtity_total' => $quabtity_total,
                                'code_file' => $code_file,
                                'link_file' => $link_file,
                                'operating_equipment' => $operating_equipment,
                                'productivity_norms' => $productivity_norms
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'date' => $date,
                        'po_id' => $po_id,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'total' => $total,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_purchase_request_zinc', $fields);
                    if ($success) {
                        $this->db->where('purchase_request_zinc_id', $id);
                        $this->db->delete('tbl_purchase_request_zinc_item');
                        foreach ($items as $key => $value) {
                            $value['purchase_request_zinc_id'] = $id;
                            $this->db->insert('tbl_purchase_request_zinc_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'purchase_request_zinc',
                            'table_obj' => 'tbl_purchase_request_zinc',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu ghi kẽm') . ' [' . $dtData['reference_no'] . ']',
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
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$this->preAddPurchaseRequestZinc) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_purchase_request_zinc');
                $data['breadcrumb'] = [array('link' => base_url('admin/purchase_request_zinc'), 'page' => lang('ch_purchase_request_zinc')), array('link' => '#', 'page' => lang('ch_add_purchase_request_zinc'))];
            } else {
                if (!$this->preEditPurchaseRequestZinc) {
                    accessDenied(true);
                }

                $this->db->select('tbl_purchase_request_zinc_item.*');
                $this->db->from('tbl_purchase_request_zinc_item');
                $this->db->where('tbl_purchase_request_zinc_item.purchase_request_zinc_id', $id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_purchase_request_zinc');
                $data['breadcrumb'] = [array('link' => base_url('admin/purchase_request_zinc'), 'page' => lang('ch_purchase_request_zinc')), array('link' => '#', 'page' => lang('dt_edit_purchase_request_zinc'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('purchase_request_zinc');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryStage'] = get_table_where('tbl_category_stages');
        $data['dtStaff'] = get_table_where('tblstaff', ['active' => 1]);
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['taxs'] = get_table_where('tbltaxes');
        $this->load->view('admin/purchase_request_zinc/detail', $data);
    }
    public function searchPo($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders.id as id, 
            CONCAT(tbl_productions_orders.reference_no) as text,
         
        ', false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_plan_orders
            WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.object_type = "orders"
        )');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
        if (!empty($id)) {
            $dtData = get_table_where('tbl_productions_orders', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
        }
        echo json_encode($data);
    }

    public function searchOrders($id = 0)
    {
        $term = $this->input->get('term');
        $po_id = !empty($this->input->get('po_id')) ? $this->input->get('po_id') : 0;
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_orders.id as id, 
            CONCAT(tbl_orders.reference_no,"(",tbl_orders.customer_name,")") as text,
         
        ', false);
        $this->db->from('tbl_orders');
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_plan_orders
            WHERE tbl_productions_plan_orders.productions_plan_id = tbl_orders.id AND tbl_productions_plan_orders.object_type = "orders"
            AND tbl_productions_plan_orders.productions_order_id = ' . $po_id . '
        )');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Đơn hàng'), 'children' => $dtResult];
        if (!empty($id)) {
            $dtData = get_table_where('tbl_orders', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no'] . '(' . $dtData['customer_name'] . ')'];
        }
        echo json_encode($data);
    }
    public function searchProductByOrders()
    {
        $term = $this->input->get('term');
        $order_id = $this->input->get('order_id');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_materials.id as id, 
            0 as order_item_id, 
            tbl_materials.id as item_id, 
            0 as total_quantity_item,
            CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
            tbl_materials.code as code_item,
            tbl_materials.name as name_item,
            tbl_materials.name_customer as name_customer,
            tbl_materials.height as height,
            tbl_materials.wide as wide,
            tbl_materials.longs as longs,
            tbl_category_items.name as name_category,
            tbl_species.name as name_species,
            tblunits.unit as unit_name
        ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'inner');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $term);
            $this->db->or_like('tbl_materials.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mặt hàng'), 'children' => $dtResult];
        echo json_encode($data);
    }
    // public function searchProductByOrders()
    // {
    //     $term = $this->input->get('term');
    //     $order_id = $this->input->get('order_id');
    //     $limit = get_option('select2_limit');
    //     $warehouses = '
    //         (Select
    //             SUM(tblwarehouse_items.product_quantity)
    //         FROM tblwarehouse_items
    //         WHERE tblwarehouse_items.id_items = tbl_materials.id 
    //               AND tblwarehouse_items.type_items = "nvl" 
    //               AND tblwarehouse_items.product_quantity > 0
    //               AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
    //     ';
    //     $this->db->select('
    //         tbl_productions_plan_bom.id as id, 
    //         tbl_productions_plan_bom.productions_plan_items_id as order_item_id, 
    //         tbl_productions_plan_bom.item_id as item_id, 
    //         tbl_productions_plan_bom.quantity as total_quantity_item,
    //         CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
    //         tbl_materials.code as code_item,
    //         tbl_materials.name as name_item,
    //         tbl_materials.name_customer as name_customer,
    //         tbl_materials.height as height,
    //         tbl_materials.wide as wide,
    //         tbl_materials.longs as longs,
    //         tbl_category_items.name as name_category,
    //         tbl_species.name as name_species,
    //         tblunits.unit as unit_name,
    //         ' . $warehouses . ' as product_quantity
    //     ', false);
    //     $this->db->from('tbl_productions_plan_bom');
    //     $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_plan_bom.productions_plan_items_id', 'inner');
    //     $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id', 'inner');
    //     $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'inner');
    //     $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
    //     $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
    //     $this->db->join('tbl_productions_orders', 'tbl_productions_orders.unitid = tbl_materials.unit_id', 'inner');
    //     $this->db->where('tbl_productions_plan_items.object_id', $order_id);
    //     $this->db->where('tbl_productions_plan_items.type_object', "orders");
    //     $this->db->where('tbl_productions_plan_bom.item_type', "materials");
    //     if (!empty($term)) {
    //         $this->db->group_start();
    //         $this->db->like('tbl_materials.code', $term);
    //         $this->db->or_like('tbl_materials.name', $term);
    //         $this->db->group_end();
    //     }
    //     $this->db->limit($limit);
    //     $dtResult = $this->db->get()->result_array();

    //     $data['results'][] = ['text' => lang('Mặt hàng'), 'children' => $dtResult];
    //     echo json_encode($data);
    // }
    public function getPurchaseRequestZinc()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_purchase_request_zinc.id as id',
            'tbl_purchase_request_zinc.reference_no as reference_no',
            'tbl_productions_orders.reference_no as reference_no_productions_orders',
            'tbl_purchase_request_zinc.date as date',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
            '1',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_purchase_request_zinc';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_purchase_request_zinc.staff_create',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_purchase_request_zinc.po_id',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tbl_purchase_request_zinc"'
        ];

        if (!$this->preViewPurchaseRequestZinc) {
            array_push($where, 'AND (tbl_purchase_request_zinc.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_purchase_request_zinc.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_purchase_request_zinc.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_purchase_request_zinc.po_id',
            '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tbl_purchase_request_zinc.id AND tbl_category_recommended.name_table="tbl_purchase_request_zinc") as countTask',
            'tbl_category_recommended.id as category_recommended_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_zinc/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 120px">
            <a target="_blank" href="' . base_url('admin/manufactures/detail_productions_orders/' . $aRow['po_id']) . '">' . $aRow['reference_no_productions_orders'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';


            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['fullname']) . '</div>';
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
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_zinc/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditPurchaseRequestZinc ? '<a href="' . base_url('admin/purchase_request_zinc/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeletePurchaseRequestZinc ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/purchase_request_zinc/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div style="width: 120px">' . $actions . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
    public function exportExcel()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_ghi_kem.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 4;
            $staff_id = get_staff_user_id();
            $this->db->select('
                tbl_purchase_request_zinc.reference_no,
                tbl_productions_orders.reference_no as reference_no_productions_orders,
                tbl_purchase_request_zinc.date,
                tbl_purchase_request_zinc_item.*,
                CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
                tbl_materials.code as code_item,
                tbl_materials.name as name_item,
                tbl_materials.name_customer as name_customer,
                tbl_materials.height as height,
                tbl_materials.wide as wide,
                tbl_materials.longs as longs,
                tbl_materials.images as images,
                tblunits.unit as unit_name,
                tbl_machines.name as name_machines
            ');
            // $images = base_url('uploads/materials/' . $info['images']);

            $this->db->from('tbl_purchase_request_zinc_item');
            $this->db->join('tbl_purchase_request_zinc', 'tbl_purchase_request_zinc.id = tbl_purchase_request_zinc_item.purchase_request_zinc_id', 'left');
            $this->db->join('tbl_materials', 'tbl_materials.id = tbl_purchase_request_zinc_item.item_id', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_purchase_request_zinc.po_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_purchase_request_zinc_item.operating_equipment', 'left');
            if (!$this->preViewPurchaseRequestZinc) {
                $this->db->where('(tbl_purchase_request_zinc.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_purchase_request_zinc.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_purchase_request_zinc.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_purchase_request_zinc.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['reference_no_productions_orders']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['code_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['name_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[6] . $row, $value['height'])->getStyle($columsExcel[8] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[7] . $row, $value['wide'])->getStyle($columsExcel[9] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['unit_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, $value['quabtity_total'])->getStyle($columsExcel[9] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['code_file']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['link_file']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['name_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, $value['productivity_norms'])->getStyle($columsExcel[13] . $row);

                $images = '';
                if (!empty($value['images'])) {
                    $images = ('uploads/materials/' . $value['images']);
                }
                if ($value['images'] != '' && file_exists($images)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    // $objDrawing1->setName('Sample image');
                    // $objDrawing1->setDescription('Sample image');
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($images);
                    // $objDrawing1->setResizeProportional(false);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    // $objDrawing1->setWidthAndHeight(50,20);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(5);
                    $objDrawing1->setCoordinates($columsExcel[14] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, '')->getStyle($columsExcel[14] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'purchase_request_zinc||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_purchase_request_zinc', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/purchase_request_zinc/';
                if (!file_exists($folder)) {
                    mkdir($folder);
                    fopen($folder . 'index.html', 'w');
                }
                if (!file_exists($folder . 'qrcode' . '/')) {
                    mkdir($folder . 'qrcode' . '/');
                    fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                }
                $params['data'] = $code;
                $params['level'] = 'H';
                $params['size'] = 40;
                $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder . 'qrcode/' . $qr . '.png');
                if (!empty($img)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($img);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[15] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, '')->getStyle($columsExcel[15] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A5:P' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:P' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_ghi_kem') . '.xls';
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
    public function delete($id)
    {
        if (!$this->preDeletePurchaseRequestZinc) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_purchase_request_zinc.*');
        $this->db->from('tbl_purchase_request_zinc');
        $this->db->where('tbl_purchase_request_zinc.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }
        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_purchase_request_zinc');
        if ($success) {
            $this->db->where('tbl_purchase_request_zinc_item.purchase_request_zinc_id', $id);
            $this->db->delete('tbl_purchase_request_zinc_item');

            insertActivityLog([
                'type_parent_obj' => 'purchase_request_zinc',
                'table_obj' => 'tbl_purchase_request_zinc',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu mua nguyên phụ liệu') . ' [' . $dtData['reference_no'] . ']',
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
    public function searchOperating_equipment($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_machines.id as id, 
            tbl_machines.name as text,
            tbl_machines.quota_productivity as quota_productivity
        ', false);
        $this->db->from('tbl_machines');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Máy móc thiết bị'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tbl_machines', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name'], 'quota_productivity' => $dtMachines['quota_productivity']];
        }
        echo json_encode($data);
    }
}
