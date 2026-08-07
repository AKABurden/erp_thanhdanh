<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_export_products extends AdminController
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
        $this->preViewRequestExportProducts = true;
        $this->preViewOwnRequestExportProducts = true;
        $this->preAddRequestExportProducts = true;
        $this->preEditRequestExportProducts = true;
        $this->preApproveRequestExportProducts = true;
        $this->preDeleteRequestExportProducts = true;
    }

    public function index()
    {
        if (!$this->preViewRequestExportProducts && !$this->preViewOwnRequestExportProducts) {
            access_denied();
        }
        $data['title'] = _l('ch_request_export_products');
        $this->load->view('admin/request_export_products/index', $data);
    }
    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_request_export_products');

        $this->db->select('tbl_request_export_products.*');
        $this->db->from('tbl_request_export_products');
        $this->db->where('tbl_request_export_products.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_request_export_products_item.*
        ');
        $this->db->from('tbl_request_export_products_item');
        $this->db->where('tbl_request_export_products_item.request_export_products_id', $id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/request_export_products/view', $data);
    }
    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_request_export_products.*');
        $this->db->from('tbl_request_export_products');
        $this->db->where('tbl_request_export_products.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_export_products.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('po_id', lang("Lệnh sản xuất"), 'required');
            $this->form_validation->set_rules('order_id', lang("Đơn hàng"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_export_products');
                    $date = to_sql_date($this->input->post('date'), true);
                    $po_id = $this->input->post('po_id');
                    $branch_id = $this->input->post('branch_id');
                    $order_id = !empty($this->input->post('order_id')) ? $this->input->post('order_id') : 0;
                    $supplier_id = !empty($this->input->post('supplier_id')) ? $this->input->post('supplier_id') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
                            $this->db->from('tbl_productions_orders_details');
                            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                            $this->db->where('tbl_productions_orders_details.id', $pod_id);
                            $dtPod = $this->db->get()->row_array();
                            if (empty($dtPod)) {
                                continue;
                            }
                            $item_id = $dtPod['items_id'];
                            $type_item = $dtPod['type_items'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quabtity_manufactures = number_unformat($this->input->post('quabtity_manufactures')[$value]);
                            $quabtity_allow = number_unformat($this->input->post('quabtity_allow')[$value]);
                            $quabtity_purchase = number_unformat($this->input->post('quabtity_purchase')[$value]);

                            $totalcon = number_unformat($this->input->post('totalcon')[$value]);
                            $totalkien = number_unformat($this->input->post('totalkien')[$value]);
                            $totalkg = number_unformat($this->input->post('totalkg')[$value]);
                            $totalallkien = number_unformat($this->input->post('totalallkien')[$value]);
                            $timequota = number_unformat($this->input->post('timequota')[$value]);
                            $timeregulations = number_unformat($this->input->post('timeregulations')[$value]);
                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'quabtity_manufactures' => $quabtity_manufactures,
                                'quabtity_allow' => $quabtity_allow,
                                'quabtity_purchase' => $quabtity_purchase,
                                'totalcon' => $totalcon,
                                'totalkien' => $totalkien,
                                'totalkg' => $totalkg,
                                'totalallkien' => $totalallkien,
                                'timequota' => $timequota,
                                'timeregulations' => $timeregulations,
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
                        'order_id' => $order_id,
                        'note' => $note,
                        'staff_create' => get_staff_user_id(),
                        'date_create' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_request_export_products', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_export_products') == $reference_no) {
                            updateReference('request_export_products');
                        }

                        foreach ($items as $key => $value) {
                            $value['request_export_products_id'] = $id;
                            $this->db->insert('tbl_request_export_products_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_export_products',
                            'table_obj' => 'tbl_request_export_products',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới Phiếu yêu cầu xuất kho TP tồn') . ' [' . $reference_no . ']',
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
                    $order_id = !empty($this->input->post('order_id')) ? $this->input->post('order_id') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
                            $this->db->from('tbl_productions_orders_details');
                            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
                            $this->db->where('tbl_productions_orders_details.id', $pod_id);
                            $dtPod = $this->db->get()->row_array();
                            if (empty($dtPod)) {
                                continue;
                            }
                            $item_id = $dtPod['items_id'];
                            $type_item = $dtPod['type_items'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quabtity_manufactures = number_unformat($this->input->post('quabtity_manufactures')[$value]);
                            $quabtity_allow = number_unformat($this->input->post('quabtity_allow')[$value]);
                            $quabtity_purchase = number_unformat($this->input->post('quabtity_purchase')[$value]);

                            $totalcon = number_unformat($this->input->post('totalcon')[$value]);
                            $totalkien = number_unformat($this->input->post('totalkien')[$value]);
                            $totalkg = number_unformat($this->input->post('totalkg')[$value]);
                            $totalallkien = number_unformat($this->input->post('totalallkien')[$value]);
                            $timequota = number_unformat($this->input->post('timequota')[$value]);
                            $timeregulations = number_unformat($this->input->post('timeregulations')[$value]);
                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'quabtity_manufactures' => $quabtity_manufactures,
                                'quabtity_allow' => $quabtity_allow,
                                'quabtity_purchase' => $quabtity_purchase,
                                'totalcon' => $totalcon,
                                'totalkien' => $totalkien,
                                'totalkg' => $totalkg,
                                'totalallkien' => $totalallkien,
                                'timequota' => $timequota,
                                'timeregulations' => $timeregulations,
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
                        'order_id' => $order_id,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_export_products', $fields);
                    if ($success) {
                        $this->db->where('request_export_products_id', $id);
                        $this->db->delete('tbl_request_export_products_item');
                        foreach ($items as $key => $value) {
                            $value['request_export_products_id'] = $id;
                            $this->db->insert('tbl_request_export_products_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_export_products',
                            'table_obj' => 'tbl_request_export_products',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa Phiếu yêu cầu xuất kho TP tồn') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddRequestExportProducts) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_request_export_products');
                $data['breadcrumb'] = [array('link' => base_url('admin/request_export_products'), 'page' => lang('ch_request_export_products')), array('link' => '#', 'page' => lang('ch_add_request_export_products'))];
            } else {
                if (!$this->preEditRequestExportProducts) {
                    accessDenied(true);
                }

                $this->db->select('tbl_request_export_products_item.*');
                $this->db->from('tbl_request_export_products_item');
                $this->db->where('tbl_request_export_products_item.request_export_products_id', $id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_request_export_products');
                $data['breadcrumb'] = [array('link' => base_url('admin/request_export_products'), 'page' => lang('ch_request_export_products')), array('link' => '#', 'page' => lang('dt_edit_request_export_products'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_export_products');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryStage'] = get_table_where('tbl_category_stages');
        $data['dtStaff'] = get_table_where('tblstaff', ['active' => 1]);
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['taxs'] = get_table_where('tbltaxes');
        $this->load->view('admin/request_export_products/detail', $data);
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
        $warehouses = '
            (Select
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.id_items = tbl_products.id 
                  AND tblwarehouse_items.type_items = "product" 
                  AND tblwarehouse_items.product_quantity > 0
                  AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
        $this->db->select('
            tbl_productions_orders_details.id as id, 
            tbl_productions_orders_items.production_plan_item_id as order_item_id, 
            tbl_productions_orders_items.items_id as item_id, 
            tbl_productions_orders_items.quantity as total_quantity_item,
            CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
            tbl_products.code as code_item,
            tbl_products.name as name_item,
            tbl_products.name_customer as name_customer,
            tbl_products.mode as mode,
            tblunits.unit as unit_name,
            tbl_brand.name as brand_name,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_products.longs as longs,
            tbl_category_products.name as category_name,
            tbl_species.name as name_species,
            ' . $warehouses . ' as product_quantity
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
        $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->where('tbl_productions_orders_details.object_id', $order_id);
        $this->db->where('tbl_productions_orders_details.object_type', "orders");
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $term);
            $this->db->or_like('tbl_products.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Mặt hàng'), 'children' => $dtResult];
        echo json_encode($data);
    }
    public function getRequestExportProducts()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_export_products.id as id',
            'tbl_productions_orders.reference_no as reference_no_productions_orders',
            'tbl_request_export_products.reference_no as reference_no',
            'tbl_request_export_products.date as date',
            'tbl_orders.reference_no as reference_no_order',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
            '1',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_export_products';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_request_export_products.order_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_request_export_products.po_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_request_export_products.staff_create',
            'LEFT JOIN tbl_category_recommended ON tbl_category_recommended.name_table = "tbl_request_export_products"'
        ];

        if (!$this->preViewRequestExportProducts) {
            array_push($where, 'AND (tbl_request_export_products.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_export_products.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_export_products.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            '(SELECT count(tbltasks.id) FROM tbltasks  LEFT JOIN tbl_category_recommended ON tbl_category_recommended.id = tbltasks.category_recommended_id  WHERE suggest_id = tbl_request_export_products.id AND tbl_category_recommended.name_table="tbl_request_export_products") as countTask',
            'tbl_category_recommended.id as category_recommended_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_export_products/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['reference_no_productions_orders']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['reference_no_order']) . '</div>';
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
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_export_products/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestExportProducts ? '<a href="' . base_url('admin/request_export_products/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestExportProducts ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_export_products/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_xuat_kho_TP_ton.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
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
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 4;
            $staff_id = get_staff_user_id();
            $warehouses = '
            (Select
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.id_items = tbl_products.id 
                  AND tblwarehouse_items.type_items = "product" 
                  AND tblwarehouse_items.product_quantity > 0
                  AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
            $this->db->select('
                tbl_request_export_products.reference_no,
                tbl_orders.reference_no as reference_no_order,
                tbl_productions_orders.reference_no as reference_no_productions_orders,
                tbl_request_export_products.date,
                tbl_request_export_products_item.*,
                CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
                tbl_products.code as code_item,
                tbl_products.name as name_item,
                tbl_products.name_customer as name_customer,
                tbl_products.height as height,
                tbl_products.wide as wide,
                tbl_products.longs as longs,
                tbl_products.images as images,
                tbl_category_products.name as name_category,
                tbl_species.name as name_species,
                tblunits.unit as unit_name,
                tbl_productions_orders_items.quantity as total_quantity_item,
                tbl_brand.name as brand_name,
                ' . $warehouses . ' as product_quantity
            ');
            $this->db->from('tbl_request_export_products_item');
            $this->db->join('tbl_request_export_products', 'tbl_request_export_products.id = tbl_request_export_products_item.request_export_products_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_request_export_products_item.item_id', 'left');
            $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'inner');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_request_export_products.order_id', 'inner');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_request_export_products.po_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_request_export_products_item.pod_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
            if (!$this->preViewRequestExportProducts) {
                $this->db->where('(tbl_request_export_products.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_export_products.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_export_products.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_export_products.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['reference_no_productions_orders']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['reference_no_order']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['brand_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['code_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['name_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['name_category']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['name_species']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[10] . $row, $value['total_quantity_item'])->getStyle($columsExcel[10] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $value['product_quantity'])->getStyle($columsExcel[11] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $value['quabtity_manufactures'])->getStyle($columsExcel[12] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, $value['quabtity_allow'])->getStyle($columsExcel[13] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[14] . $row, $value['quabtity_purchase'])->getStyle($columsExcel[14] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, $value['height'])->getStyle($columsExcel[15] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, $value['wide'])->getStyle($columsExcel[16] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['totalcon'])->getStyle($columsExcel[17] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['totalkien'])->getStyle($columsExcel[18] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['totalkg'])->getStyle($columsExcel[19] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[20] . $row, $value['totalallkien'])->getStyle($columsExcel[20] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, $value['timequota'])->getStyle($columsExcel[21] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, $value['timeregulations'])->getStyle($columsExcel[22] . $row);


                $images = '';
                if (!empty($value['images'])) {
                    $images = ('uploads/products/' . $value['images']);
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
                    $objDrawing1->setCoordinates($columsExcel[23] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[23] . $row, '')->getStyle($columsExcel[23] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_export_products||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_export_products', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_export_products/';
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
                    $objDrawing1->setCoordinates($columsExcel[24] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[24] . $row, '')->getStyle($columsExcel[24] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A5:Y' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:Y' . $row)->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(17);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_xuat_kho_TP_ton') . '.xls';
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
        if (!$this->preDeleteRequestExportProducts) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_export_products.*');
        $this->db->from('tbl_request_export_products');
        $this->db->where('tbl_request_export_products.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }
        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_export_products');
        if ($success) {
            $this->db->where('tbl_request_export_products_item.request_export_products_id', $id);
            $this->db->delete('tbl_request_export_products_item');

            insertActivityLog([
                'type_parent_obj' => 'request_export_products',
                'table_obj' => 'tbl_request_export_products',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa Phiếu yêu cầu xuất kho TP tồn') . ' [' . $dtData['reference_no'] . ']',
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
}
