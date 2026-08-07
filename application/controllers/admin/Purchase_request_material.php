<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_request_material extends AdminController
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
        $this->preViewPurchaseRequestMaterial = true;
        $this->preViewOwnPurchaseRequestMaterial = true;
        $this->preAddPurchaseRequestMaterial = true;
        $this->preEditPurchaseRequestMaterial = true;
        $this->preApprovePurchaseRequestMaterial = true;
        $this->preDeletePurchaseRequestMaterial = true;
    }

    public function index()
    {
        if (!$this->preViewPurchaseRequestMaterial && !$this->preViewOwnPurchaseRequestMaterial) {
            access_denied();
        }
        $data['title'] = _l('ch_purchase_request_material');
        $this->load->view('admin/purchase_request_material/index', $data);
    }
    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_purchase_request_material');

        $this->db->select('tbl_purchase_request_material.*');
        $this->db->from('tbl_purchase_request_material');
        $this->db->where('tbl_purchase_request_material.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_purchase_request_material_item.*
        ');
        $this->db->from('tbl_purchase_request_material_item');
        $this->db->where('tbl_purchase_request_material_item.purchase_request_material_id', $id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/purchase_request_material/view', $data);
    }
    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_purchase_request_material.*');
        $this->db->from('tbl_purchase_request_material');
        $this->db->where('tbl_purchase_request_material.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_purchase_request_material.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('po_id', lang("Lệnh sản xuất"), 'required');
            $this->form_validation->set_rules('order_id', lang("Đơn hàng"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('supplier_id', lang("Nhà cung cấp"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('purchase_request_material');
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
                            $this->db->select('tbl_productions_plan_bom.item_id as items_id,tbl_productions_plan_bom.item_type');
                            $this->db->from('tbl_productions_plan_bom');
                            $this->db->where('tbl_productions_plan_bom.id', $pod_id);
                            $dtPod = $this->db->get()->row_array();
                            if (empty($dtPod)) {
                                continue;
                            }
                            $item_id = $dtPod['items_id'];
                            $type_item = $dtPod['item_type'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quabtity_manufactures = number_unformat($this->input->post('quabtity_manufactures')[$value]);
                            $quabtity_allow = number_unformat($this->input->post('quabtity_allow')[$value]);
                            $quabtity_purchase = number_unformat($this->input->post('quabtity_purchase')[$value]);
                            $totalheight = number_unformat($this->input->post('totalheight')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $tax_id = number_unformat($this->input->post('tax_id')[$value]);
                            $tax_rate = number_unformat($this->input->post('tax_rate')[$value]);
                            $timequota = number_unformat($this->input->post('timequota')[$value]);
                            $timeregulations = number_unformat($this->input->post('timeregulations')[$value]);
                            $amount = $quabtity_purchase * $price * (1 + $tax_rate / 100);
                            $total += $amount;
                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'quabtity_manufactures' => $quabtity_manufactures,
                                'quabtity_allow' => $quabtity_allow,
                                'quabtity_purchase' => $quabtity_purchase,
                                'totalheight' => $totalheight,
                                'price' => $price,
                                'tax_id' => $tax_id,
                                'tax_rate' => $tax_rate,
                                'timequota' => $timequota,
                                'timeregulations' => $timeregulations,
                                'amount' => $amount,
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
                        'supplier_id' => $supplier_id,
                        'note' => $note,
                        'total' => $total,
                        'staff_create' => get_staff_user_id(),
                        'date_create' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_purchase_request_material', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('purchase_request_material') == $reference_no) {
                            updateReference('purchase_request_material');
                        }

                        foreach ($items as $key => $value) {
                            $value['purchase_request_material_id'] = $id;
                            $this->db->insert('tbl_purchase_request_material_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'purchase_request_material',
                            'table_obj' => 'tbl_purchase_request_material',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới phiếu yêu cầu mua nguyên phụ liệu') . ' [' . $reference_no . ']',
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
                    $supplier_id = !empty($this->input->post('supplier_id')) ? $this->input->post('supplier_id') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    $total = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = $this->input->post('pod_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            $this->db->select('tbl_productions_plan_bom.item_id as items_id,tbl_productions_plan_bom.item_type');
                            $this->db->from('tbl_productions_plan_bom');
                            $this->db->where('tbl_productions_plan_bom.id', $pod_id);
                            $dtPod = $this->db->get()->row_array();
                            if (empty($dtPod)) {
                                continue;
                            }
                            $item_id = $dtPod['items_id'];
                            $type_item = $dtPod['item_type'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $quabtity_manufactures = number_unformat($this->input->post('quabtity_manufactures')[$value]);
                            $quabtity_allow = number_unformat($this->input->post('quabtity_allow')[$value]);
                            $quabtity_purchase = number_unformat($this->input->post('quabtity_purchase')[$value]);
                            $totalheight = number_unformat($this->input->post('totalheight')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $tax_id = number_unformat($this->input->post('tax_id')[$value]);
                            $tax_rate = number_unformat($this->input->post('tax_rate')[$value]);
                            $timequota = number_unformat($this->input->post('timequota')[$value]);
                            $timeregulations = number_unformat($this->input->post('timeregulations')[$value]);
                            $amount = $quabtity_purchase * $price * (1 + $tax_rate / 100);
                            $total += $amount;

                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'quabtity_manufactures' => $quabtity_manufactures,
                                'quabtity_allow' => $quabtity_allow,
                                'quabtity_purchase' => $quabtity_purchase,
                                'totalheight' => $totalheight,
                                'price' => $price,
                                'tax_id' => $tax_id,
                                'tax_rate' => $tax_rate,
                                'timequota' => $timequota,
                                'timeregulations' => $timeregulations,
                                'amount' => $amount,
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
                        'supplier_id' => $supplier_id,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'total' => $total,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_purchase_request_material', $fields);
                    if ($success) {
                        $this->db->where('purchase_request_material_id', $id);
                        $this->db->delete('tbl_purchase_request_material_item');
                        foreach ($items as $key => $value) {
                            $value['purchase_request_material_id'] = $id;
                            $this->db->insert('tbl_purchase_request_material_item', $value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'purchase_request_material',
                            'table_obj' => 'tbl_purchase_request_material',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu mua nguyên phụ liệu') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddPurchaseRequestMaterial) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_purchase_request_material');
                $data['breadcrumb'] = [array('link' => base_url('admin/purchase_request_material'), 'page' => lang('ch_purchase_request_material')), array('link' => '#', 'page' => lang('ch_add_purchase_request_material'))];
            } else {
                if (!$this->preEditPurchaseRequestMaterial) {
                    accessDenied(true);
                }

                $this->db->select('tbl_purchase_request_material_item.*');
                $this->db->from('tbl_purchase_request_material_item');
                $this->db->where('tbl_purchase_request_material_item.purchase_request_material_id', $id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_purchase_request_material');
                $data['breadcrumb'] = [array('link' => base_url('admin/purchase_request_material'), 'page' => lang('ch_purchase_request_material')), array('link' => '#', 'page' => lang('dt_edit_purchase_request_material'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('purchase_request_material');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryStage'] = get_table_where('tbl_category_stages');
        $data['dtStaff'] = get_table_where('tblstaff', ['active' => 1]);
        $data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
        $data['taxes'] = get_taxes_dropdown_template('', 0);
        $data['taxs'] = get_table_where('tbltaxes');
        $this->load->view('admin/purchase_request_material/detail', $data);
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

        // $this->db->select('
        //                     SUM(tblwarehouse_items.product_quantity) as product_quantity,
        //                 ', false);
        //                 $this->db->from('tblwarehouse_items');
        //                 $this->db->where_not_in('tblwarehouse_items.warehouse_id', [WAREHOUSES_CAPACITY]);
        //                 $this->db->where('tblwarehouse_items.type_items', $typeW);
        //                 $this->db->where('tblwarehouse_items.id_items', $item_id);
        //                 $this->db->where('tblwarehouse_items.product_quantity >', 0);
        //                 $dtW = $this->db->get()->row_array();

        //                 $quantityNeed = $quantity + $quantity_compensation;
        //                 $quantity_primary = $quantityNeed * $quantity_exchange / $exchange_unit;
        //                 if ($item_type == "materials") {
        //                     // $quantity_convert_warehouse = roundNumberFormat($quantity_primary / $exchange_standard_unit * $exchange_unit, 0);
        //                     $quantity_convert_warehouse = ceil($quantity_primary / $exchange_standard_unit * $exchange_unit);
        //                 } else {
        //                     // $quantity_convert_warehouse = roundNumberFormat($quantity_primary * $conversion_quantity_unit, 0);
        //                     $quantity_convert_warehouse = ceil($quantity_primary * $conversion_quantity_unit);
        //                 }

        //                 $quantity_warehouse = $dtW['product_quantity'];
        $warehouses = '
            (Select
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.id_items = tbl_materials.id 
                  AND tblwarehouse_items.type_items = "nvl" 
                  AND tblwarehouse_items.product_quantity > 0
                  AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
        $this->db->select('
            tbl_productions_plan_bom.id as id, 
            tbl_productions_plan_bom.productions_plan_items_id as order_item_id, 
            tbl_productions_plan_bom.item_id as item_id, 
            tbl_productions_plan_bom.quantity as total_quantity_item,
            CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
            tbl_materials.code as code_item,
            tbl_materials.name as name_item,
            tbl_materials.name_customer as name_customer,
            tbl_materials.height as height,
            tbl_materials.wide as wide,
            tbl_materials.longs as longs,
            tbl_category_items.name as name_category,
            tbl_species.name as name_species,
            tblunits.unit as unit_name,
            ' . $warehouses . ' as product_quantity
        ', false);
        $this->db->from('tbl_productions_plan_bom');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_plan_bom.productions_plan_items_id', 'inner');
        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id', 'inner');
        $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'inner');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
        $this->db->where('tbl_productions_plan_items.object_id', $order_id);
        $this->db->where('tbl_productions_plan_items.type_object', "orders");
        $this->db->where('tbl_productions_plan_bom.item_type', "materials");
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
    public function getPurchaseRequestMaterial()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_items = "(
            SELECT
                tbl_purchase_request_material_item.purchase_request_material_id,
                SUM(quabtity_purchase * price) as total,
                SUM(((quabtity_purchase * price) * tax_rate) / 100) as total_tax
            FROM tbl_purchase_request_material_item
            GROUP BY tbl_purchase_request_material_item.purchase_request_material_id
        ) tb_items";

        $aColumns = [
            'tbl_purchase_request_material.id as id',
            'tbl_productions_orders.reference_no as reference_no_productions_orders',
            'tbl_purchase_request_material.reference_no as reference_no',
            'tbl_purchase_request_material.date as date',
            'tbl_orders.reference_no as reference_no_order',
            'tblsuppliers.company as company',
            'tbl_purchase_request_material.supplier_id as supplier_id',
            'tb_items.total as total',
            'tb_items.total_tax as total_tax',
            'tbl_purchase_request_material.total as grand_total',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_purchase_request_material';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_orders ON tbl_orders.id = tbl_purchase_request_material.order_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_purchase_request_material.po_id',
            'INNER JOIN tblsuppliers ON tblsuppliers.id = tbl_purchase_request_material.supplier_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_purchase_request_material.staff_create',
            'INNER JOIN '.$tb_items.' ON tb_items.purchase_request_material_id = tbl_purchase_request_material.id',
        ];

        if (!$this->preViewPurchaseRequestMaterial) {
            array_push($where, 'AND (tbl_purchase_request_material.staff_create = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_purchase_request_material.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_purchase_request_material.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $total = 0;
        $total_tax = 0;
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_material/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['reference_no_productions_orders']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['reference_no_order']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-right" >' . formatMoney($aRow['total']) . '</div>';
            $row[] = '<div class="text-right" >' . formatMoney($aRow['total_tax']) . '</div>';
            $row[] = '<div class="text-right" >' . formatMoney($aRow['grand_total']) . '</div>';


            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['fullname']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/purchase_request_material/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditPurchaseRequestMaterial ? '<a href="' . base_url('admin/purchase_request_material/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeletePurchaseRequestMaterial ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/purchase_request_material/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $total += $aRow['total'];
            $total_tax += $aRow['total_tax'];
            $grand_total += $aRow['grand_total'];
            $output['aaData'][] = $row;
        }
        $output['total'] = $total;
        $output['total_tax'] = $total_tax;
        $output['grand_total'] = $grand_total;
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
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_mua_npl.xlsx';
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
            WHERE tblwarehouse_items.id_items = tbl_materials.id 
                  AND tblwarehouse_items.type_items = "nvl" 
                  AND tblwarehouse_items.product_quantity > 0
                  AND tblwarehouse_items.warehouse_id NOT IN(' . WAREHOUSES_CAPACITY . '))
        ';
            $this->db->select('
                tbl_purchase_request_material.reference_no,
                tbl_orders.reference_no as reference_no_order,
                tbl_productions_orders.reference_no as reference_no_productions_orders,
                tbl_purchase_request_material.date,
                tbl_purchase_request_material_item.*,
                CONCAT(tbl_materials.name,"(",tbl_materials.code,")") as text,
                tbl_materials.code as code_item,
                tbl_materials.name as name_item,
                tbl_materials.name_customer as name_customer,
                tbl_materials.height as height,
                tbl_materials.wide as wide,
                tbl_materials.longs as longs,
                tbl_category_items.name as name_category,
                tbl_species.name as name_species,
                tblunits.unit as unit_name,
                tblsuppliers.company as company,
                tbl_productions_plan_bom.quantity as total_quantity_item,
                ' . $warehouses . ' as product_quantity
            ');
            $this->db->from('tbl_purchase_request_material_item');
            $this->db->join('tbl_purchase_request_material', 'tbl_purchase_request_material.id = tbl_purchase_request_material_item.purchase_request_material_id', 'left');
            $this->db->join('tbl_materials', 'tbl_materials.id = tbl_purchase_request_material_item.item_id', 'left');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'inner');
            $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_purchase_request_material.order_id', 'inner');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_purchase_request_material.po_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'inner');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_purchase_request_material.supplier_id', 'inner');

            $this->db->join('tbl_productions_plan_bom', 'tbl_productions_plan_bom.id = tbl_purchase_request_material_item.pod_id', 'inner');
            if (!$this->preViewPurchaseRequestMaterial) {
                $this->db->where('(tbl_purchase_request_material.staff_create = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_purchase_request_material.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_purchase_request_material.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_purchase_request_material.id asc');
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
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['company']), PHPExcel_Cell_DataType::TYPE_STRING);
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
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[17] . $row, $value['longs'])->getStyle($columsExcel[17] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[18] . $row, $value['totalheight'])->getStyle($columsExcel[18] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[19] . $row, $value['price'])->getStyle($columsExcel[19] . $row);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, ($value['tax_rate'] . '%'), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, $value['amount'])->getStyle($columsExcel[21] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[22] . $row, $value['timequota'])->getStyle($columsExcel[22] . $row);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[23] . $row, $value['timeregulations'])->getStyle($columsExcel[23] . $row);
                if (!empty($value['barcode'])){
                    $code = $value['barcode'];
                } else {
                    $code = 'purchase_request_material||'.$value['id'];
                    $this->db->where('id',$value['id']);
                    $this->db->update('tbl_purchase_request_material',['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/purchase_request_material/';
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
                $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder.'qrcode/'. $qr . '.png');
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
            $filename = lang('Phieu_yeu_cau_mua_npl') . '.xls';
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
    public function delete($id){
        if (!$this->preDeletePurchaseRequestMaterial){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_purchase_request_material.*');
        $this->db->from('tbl_purchase_request_material');
        $this->db->where('tbl_purchase_request_material.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }
        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_purchase_request_material');
        if ($success){
            $this->db->where('tbl_purchase_request_material_item.purchase_request_material_id',$id);
            $this->db->delete('tbl_purchase_request_material_item');

            insertActivityLog([
                'type_parent_obj' => 'purchase_request_material',
                'table_obj' => 'tbl_purchase_request_material',
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
}
