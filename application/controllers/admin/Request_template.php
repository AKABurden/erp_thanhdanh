<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_template extends AdminController
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
        $this->preViewRequestTemplate = true;
        $this->preViewOwnRequestTemplate = true;
        $this->preAddRequestTemplate = true;
        $this->preEditRequestTemplate = true;
        $this->preApproveRequestTemplate = true;
        $this->preDeleteRequestTemplate = true;
    }

    public function index()
    {
        if (!$this->preViewRequestTemplate && !$this->preViewOwnRequestTemplate) {
            access_denied();
        }
        $data['title'] = _l('ch_request_template');
        $this->load->view('admin/request_template/index', $data);
    }
    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_request_template');

        $this->db->select('tbl_request_template.*');
        $this->db->from('tbl_request_template');
        $this->db->where('tbl_request_template.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_request_template_item.*
        ');
        $this->db->from('tbl_request_template_item');
        $this->db->where('tbl_request_template_item.request_template_id', $id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/request_template/view', $data);
    }
    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_request_template.*');
        $this->db->from('tbl_request_template');
        $this->db->where('tbl_request_template.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_template.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            // $this->form_validation->set_rules('id_quotes', lang("Số báo giá"), 'required');
            $this->form_validation->set_rules('client_id', lang("Khách hàng"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_template');
                    $date = to_sql_date($this->input->post('date'), true);
                    $id_quotes = $this->input->post('id_quotes');
                    $client_id = $this->input->post('client_id');
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $is_rerun_sample = $this->input->post('is_rerun_sample') ?? 0;

                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $item_id = $this->input->post('item_id')[$value];
                            $quote_items_id = $this->input->post('quote_items_id')[$value];
                            $date_run_sample = to_sql_date($this->input->post('date_run_sample')[$value] ?? '') ?? null;
                            $date_finished = to_sql_date($this->input->post('date_finished')[$value] ?? '') ?? null;
                            $date_request_sample = to_sql_date($this->input->post('date_request_sample')[$value] ?? '') ?? null;
                            $date_approved_sample = to_sql_date($this->input->post('date_approved_sample')[$value] ?? '') ?? null;
                            $date_runs_sample = to_sql_date($this->input->post('date_runs_sample')[$value] ?? '') ?? null;
                            $date_finished_manufactures = to_sql_date($this->input->post('date_finished_manufactures')[$value] ?? '') ?? null;

                            $type_item = 'products';
                            $items[] = [
                                'quote_items_id' => $quote_items_id,
                                'items_id' => $item_id,
                                'type_item' => $type_item,
                                'date_run_sample' => $date_run_sample,
                                'date_finished' => $date_finished,
                                'date_request_sample' => $date_request_sample,
                                'date_approved_sample' => $date_approved_sample,
                                'date_runs_sample' => $date_runs_sample,
                                'date_finished_manufactures' => $date_finished_manufactures,
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
                        'id_quotes' => $id_quotes,
                        'note' => $note,
                        'client_id' => explode('__', $client_id)[1],
                        'staff_create' => get_staff_user_id(),
                        'date_create' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                        'is_rerun_sample' => $is_rerun_sample
                    ];
                    $this->db->insert('tbl_request_template', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_template') == $reference_no) {
                            updateReference('request_template');
                        }

                        foreach ($items as $key => $value) {
                            $value['request_template_id'] = $id;
                            $this->db->insert('tbl_request_template_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_template',
                            'table_obj' => 'tbl_request_template',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới phiếu yêu cầu phát triển mẫu') . ' [' . $reference_no . ']',
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
                    $id_quotes = $this->input->post('id_quotes');
                    $client_id = $this->input->post('client_id');
                    $branch_id = $this->input->post('branch_id');
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $is_rerun_sample = $this->input->post('is_rerun_sample') ?? 0;

                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $item_id = $this->input->post('item_id')[$value];
                            $quote_items_id = $this->input->post('quote_items_id')[$value];
                            $date_run_sample = to_sql_date($this->input->post('date_run_sample')[$value] ?? '') ?? null;
                            $date_finished = to_sql_date($this->input->post('date_finished')[$value] ?? '') ?? null;
                            $date_request_sample = to_sql_date($this->input->post('date_request_sample')[$value] ?? '') ?? null;
                            $date_approved_sample = to_sql_date($this->input->post('date_approved_sample')[$value] ?? '') ?? null;
                            $date_runs_sample = to_sql_date($this->input->post('date_runs_sample')[$value] ?? '') ?? null;
                            $date_finished_manufactures = to_sql_date($this->input->post('date_finished_manufactures')[$value] ?? '') ?? null;

                            $type_item = 'products';
                            $items[] = [
                                'quote_items_id' => $quote_items_id,
                                'items_id' => $item_id,
                                'type_item' => $type_item,
                                'date_run_sample' => $date_run_sample,
                                'date_finished' => $date_finished,
                                'date_request_sample' => $date_request_sample,
                                'date_approved_sample' => $date_approved_sample,
                                'date_runs_sample' => $date_runs_sample,
                                'date_finished_manufactures' => $date_finished_manufactures,
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
                        'id_quotes' => $id_quotes,
                        'note' => $note,
                        'client_id' => explode('__', $client_id)[1],
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'is_rerun_sample' => $is_rerun_sample
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_template', $fields);
                    if ($success) {
                        $this->db->where('request_template_id', $id);
                        $this->db->delete('tbl_request_template_item');
                        foreach ($items as $key => $value) {
                            $value['request_template_id'] = $id;
                            $this->db->insert('tbl_request_template_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_template',
                            'table_obj' => 'tbl_request_template',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu phát triển mẫu') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddRequestTemplate) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_request_template');
                $data['breadcrumb'] = [array('link' => base_url('admin/request_template'), 'page' => lang('ch_request_template')), array('link' => '#', 'page' => lang('ch_add_request_template'))];
            } else {
                if (!$this->preEditRequestTemplate) {
                    accessDenied(true);
                }

                $this->db->select('tbl_request_template_item.*');
                $this->db->from('tbl_request_template_item');
                $this->db->where('tbl_request_template_item.request_template_id', $id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_request_template');
                $data['breadcrumb'] = [array('link' => base_url('admin/request_template'), 'page' => lang('ch_request_template')), array('link' => '#', 'page' => lang('dt_edit_request_template'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_template');
        $data['dtStaff'] = get_table_where('tblstaff', ['active' => 1]);
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxs'] = get_table_where('tbltaxes');
        $this->load->view('admin/request_template/detail', $data);
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

    public function searchQuotes($id = 0)
    {
        $term = $this->input->get('term');
        $client_id = !empty($this->input->get('client_id')) ? $this->input->get('client_id') : 0;
        $customer_id = 0;
        if (!empty($client_id)) {
            $customer_id = explode('__', $client_id)[1];
        }
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_quotes.id as id, 
            tbl_quotes.reference_no as text,
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->where('tbl_quotes.customer_id', $customer_id);

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_quotes.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Báo giá'), 'children' => $dtResult];
        if (!empty($id)) {
            $dtData = get_table_where('tbl_quotes', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
        }
        echo json_encode($data);
    }
    public function searchProductByOrders()
    {
        $term = $this->input->get('term');
        $id_quotes = $this->input->get('id_quotes');
        $limit = get_option('select2_limit');
        // $this->db->select('
        //     tbl_quote_items.id as id, 
        //     tbl_quote_items.item_id as item_id, 
        //     CONCAT(tbl_products.code) as text, 
        //     tbl_products.name as item_name, 
        //     IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, 
        //     tblunits.unit as unit_name, 
        //     tbl_products.price_sell as price_sell, 
        //     tbl_products.info as info, 
        //     tbl_products.code as item_code, 
        //     CONCAT(tbl_products.category_id, "__products") as category_id,
        //     tblsize.name as size_name,
        //     tbl_products.loss as loss,
        //     tbl_products.quantity_child_sheet as quantity_child_sheet,
        //     tbl_products.quantity_sheet_bale as quantity_sheet_bale,
        //     tbl_products.mode_product as mode_product,
        //     tbl_products.product_name_customer as name_customer,
        //     tbl_products.height as height,
        //     tbl_products.wide as wide,
        //     tbl_products.wide as wide,
        //     tbl_products.packing as packing,
        //     tbl_products.quantity_max as quantity_max,
        //     tbl_products.time_inventory as time_inventory,
        //     tbl_products.quota_time_change_one as quota_time_change_one,
        //     unit_stock.unit as unit_stock,
        //     tbl_species.name as specie_name,
        //     tbl_category_products.name as category_name,
        //     tb_unit_measure.unit as unit_measure,
        //     tbl_brand.name as brand_name,
        // ', false);
        // $this->db->from('tbl_quote_items');
        // $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id', 'inner');

        $select = '';
        if ($id_quotes) {
            $select = 'tbl_quote_items.id as id, 
            tbl_quote_items.item_id as item_id, ';
        } else {
            $select = 'tbl_products.id as id, 
            tbl_products.id as item_id, ';
        }


        $this->db->select('
            '.$select.' 
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
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_products.wide as wide,
            tbl_products.packing as packing,
            tbl_products.quantity_max as quantity_max,
            tbl_products.time_inventory as time_inventory,
            tbl_products.quota_time_change_one as quota_time_change_one,
            unit_stock.unit as unit_stock,
            tbl_species.name as specie_name,
            tbl_category_products.name as category_name,
            tb_unit_measure.unit as unit_measure,
            tbl_brand.name as brand_name,
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits unit_stock', 'tbl_products.conversion_unit = unit_stock.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
        if (!empty($id_quotes)) {
            $this->db->join('tbl_quote_items', 'tbl_products.id = tbl_quote_items.item_id', 'inner');
            $this->db->where('tbl_quote_items.quote_id', $id_quotes);
        }
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
    public function getRequestTemplate()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_customer_group = '(
            SELECT
                GROUP_CONCAT(tblcustomers_groups.name) as name,
                tblcustomer_groups.customer_id AS client_id
            FROM tblcustomer_groups
            INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id
        ) as tb_customer_group';

        $aColumns = [
            'tbl_request_template.id as id',
            'tbl_request_template.reference_no as reference_no',
            'tb_customer_group.name as client_group',
            'tblclients.zcode as client_code',
            'tblclients.company as company',
            'tbl_quotes.reference_no as reference_no_quotes',
            'tbl_request_template.date as date',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_template';
        $where = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_request_template.staff_create',
            'LEFT JOIN tbl_quotes ON tbl_quotes.id = tbl_request_template.id_quotes',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_request_template.client_id',
            'LEFT JOIN '.$tb_customer_group.' ON tb_customer_group.client_id = tblclients.userid',
        ];

        if (!$this->preViewRequestTemplate) {
            array_push($where, 'AND (tbl_request_template.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_template.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_template.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tbl_request_template.id_quotes'], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_template/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['client_group']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['client_code']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">
            <a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotes/view_quotes/' . $aRow['id_quotes']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no_quotes'] . '</a>
            </div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _d($aRow['date']) . '</div>';


            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['fullname']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_template/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestTemplate ? '<a href="' . base_url('admin/request_template/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestTemplate ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_template/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_phat_trien_mau.xlsx';
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
            $tb_customer_group = '(
                SELECT
                    GROUP_CONCAT(tblcustomers_groups.name) as name,
                    tblcustomer_groups.customer_id AS client_id
                FROM tblcustomer_groups
                INNER JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
                GROUP BY tblcustomer_groups.customer_id
            ) as tb_customer_group';
            $this->db->select('
                tbl_request_template.reference_no,
                tbl_quotes.reference_no as reference_no_quotes,
                tbl_request_template.date,
                tbl_request_template_item.*,
                tbl_products.code as code_item,
                tbl_products.name as name_item,
                tbl_products.name_customer as name_customer,
                tbl_products.height as height,
                tbl_products.wide as wide,
                tbl_products.longs as longs,
                tbl_products.images as images,
                tbl_products.height as height,
                tbl_products.wide as wide,
                tbl_products.packing as packing,
                tbl_products.quantity_max as quantity_max,
                tbl_products.time_inventory as time_inventory,
                tbl_products.quota_time_change_one as quota_time_change_one,
                tblunits.unit as unit_name,
                tblclients.company as company,
                tblclients.zcode as client_code,
                tb_unit_measure.unit as unit_measure,
                tb_customer_group.name as client_group,
                tbl_category_products.name as category_name,
                tbl_species.name as specie_name,
                tbl_brand.name as brand_name
            ');
            // $images = base_url('uploads/materials/' . $info['images']);

            $this->db->from('tbl_request_template_item');
            $this->db->join('tbl_request_template', 'tbl_request_template.id = tbl_request_template_item.request_template_id', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_request_template_item.items_id', 'left');
            $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_request_template.client_id', 'inner');
            $this->db->join('tbl_quotes', 'tbl_quotes.id = tbl_request_template.id_quotes', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
            $this->db->join($tb_customer_group, 'tb_customer_group.client_id = tbl_request_template.client_id', 'left');

            if (!$this->preViewRequestTemplate) {
                $this->db->where('(tbl_request_template.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_template.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_template.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_template.id asc');
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
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['reference_no_quotes']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['brand_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['client_group']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['client_code']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['company']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['category_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['specie_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['unit_name']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[11] . $row, $value['height']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[12] . $row, $value['wide']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['unit_measure']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($value['code_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, ($value['name_item']), PHPExcel_Cell_DataType::TYPE_STRING);
                
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[16] . $row, $value['packing']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['quantity_max']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['time_inventory']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['quota_time_change_one']);
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
                    $objDrawing1->setCoordinates($columsExcel[20] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[20] . $row, '')->getStyle($columsExcel[20] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_template||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_template', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_template/';
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
                    $objDrawing1->setCoordinates($columsExcel[21] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, '')->getStyle($columsExcel[21] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A5:V' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A5:V' . $row)->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(25);
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
            $filename = lang('Phieu_yeu_cau_phat_trien_mau') . '.xls';
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
        if (!$this->preDeleteRequestTemplate) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_template.*');
        $this->db->from('tbl_request_template');
        $this->db->where('tbl_request_template.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }
        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_template');
        if ($success) {
            $this->db->where('tbl_request_template_item.request_template_id', $id);
            $this->db->delete('tbl_request_template_item');

            insertActivityLog([
                'type_parent_obj' => 'request_template',
                'table_obj' => 'tbl_request_template',
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
