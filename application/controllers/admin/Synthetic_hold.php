<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Synthetic_hold extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('tools_supplies_model');
    }

    public function index()
    {
        $arrTab = [
            [
                'id' => '1',
                'name' => lang('Thành phẩm'),
            ],
            [
                'id' => '2',
                'name' => lang('Nguyên phụ liệu'),
            ],
        ];
        $data['arrTab'] = $arrTab;
        $data['title'] = _l('Thống kê giữ hàng');

        $this->load->view('admin/synthetic_hold/index', $data);
    }

    public function getSyntheticHolds()
    {
        $name_search = $this->input->post('name_search');
        $status_table = $this->input->post('status_table');
        $product_search = $this->input->post('product_search');
        $orders_search = $this->input->post('orders_search');
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $countQtyItemsTranfer = "(
            SELECT
                tbltransfer_warehouse_detail.id,
                SUM(tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) as quantity
            FROM tbltransfer_warehouse_detail
            INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
            INNER JOIN tbl_orders  ON tbl_orders.id = tbltransfer_warehouse.order_id_new
            INNER JOIN tblwarehouse_product ON tblwarehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer AND tbltransfer_warehouse_detail.warehouses_to = tblwarehouse_product.warehouse_id AND tblwarehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to AND tblwarehouse_product.type_items = 'product' AND tblwarehouse_product.product_id = tbltransfer_warehouse_detail.id_items AND tblwarehouse_product.type_export = 2 AND tblwarehouse_product.quantity > 0
            WHERE tbltransfer_warehouse_detail.warehouses_to != ".WAREHOUSES_CAPACITY." AND (tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) > 0
            AND (COALESCE(tblwarehouse_product.lot_code, -1) = COALESCE(tbltransfer_warehouse_detail.lot_code, -1)) AND (COALESCE(tblwarehouse_product.date_sx, -1) = COALESCE(tbltransfer_warehouse_detail.date_sx, -1))
            AND (COALESCE(tblwarehouse_product.date_sd, -1) = COALESCE(tbltransfer_warehouse_detail.date_sd, -1)) AND (COALESCE(tblwarehouse_product.date_use, -1) = COALESCE(tbltransfer_warehouse_detail.date_use, -1))
            GROUP BY tbltransfer_warehouse_detail.id
        ) tb_count_qty_tranfer";


        $tableAllItems = "(
            (
                SELECT
                    tbl_products.id as id,
                    tbl_products.name as name,
                    tbl_products.code as code,
                    tbl_products.type_products as type_items,
                    tblunits.unit as unit_name,
                    tbl_orders.id as order_id,
                    tbl_orders.reference_no as reference_no,
                    tbl_orders.customer_name as customer_name,
                    COALESCE(tb_count_qty_tranfer.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'orders' as type_order,
                    'products' as type_group
                FROM tbl_products 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_products.id AND ( type = 'product')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_orders ON tbl_orders.id = tbltransfer_warehouse.order_id_new
                INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                LEFT JOIN $countQtyItemsTranfer ON tb_count_qty_tranfer.id = tbltransfer_warehouse_detail.id
                GROUP BY tbltransfer_warehouse_detail.id
            )
        ) table_all_item";


        $aColumns = [
            'table_all_item.id as id',
            'table_all_item.date_tranfer as date_tranfer',
            'table_all_item.code_tranfer as code_tranfer',
            'table_all_item.code as code',
            'table_all_item.name as name',
            'table_all_item.name_warehouse as name_warehouse',
            'table_all_item.customer_name as customer_name',
            'table_all_item.reference_no as reference_no',
            'table_all_item.quantity as quantity'
        ];
        $sIndexColumn = 'table_all_item.id';
        $sTable = $tableAllItems;
        $where = [

        ];
        $filter = [];

        $join = [
        ];

         if (!empty($start_date_search)) {
             $start_date_search = to_sql_date($start_date_search);
             array_push($where, "AND table_all_item.date_tranfer >= '" . $start_date_search . "'");
         }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND table_all_item.date_tranfer <= '" . $end_date_search . "'");
        }

        if (!empty($product_search)){
            $product_search = explode('__',$product_search);
            array_push(
                $where,
                'AND (table_all_item.id = '.$product_search[0].') AND table_all_item.type_items = "'.$product_search[1].'"'
            );
        }

        if (!empty($orders_search)){
            array_push(
                $where,
                'AND (table_all_item.order_id = '.$orders_search.')'
            );
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'table_all_item.type_items as type_items, 
             table_all_item.id as id, 
             table_all_item.order_id as order_id, 
             table_all_item.quantity_old as quantity_old, 
             table_all_item.type_order as type_order, 
             table_all_item.lot_code as lot_code, 
             table_all_item.type_group as type_group'
        ], '', [], ['union_all' => true]);


        $output = $result['output'];
        $rResult = $result['rResult'];
//        usort($rResult, ch_make_cmp(['id' => "asc","order_id" => 'asc']));
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $item_id = $aRow['id'];
            $item_type = $aRow['type_items'];
            $reference_no = $aRow['reference_no'];
            $customer_name = $aRow['customer_name'];
            $order_id = $aRow['order_id'];
            $name = $aRow['name'];
            $code = $aRow['code'];
            $date_tranfer = $aRow['date_tranfer'];
            $code_tranfer = $aRow['code_tranfer'];
            $name_warehouse = $aRow['name_warehouse'];
            $lot_code = $aRow['lot_code'];

            $htmlLotCode = '';
            if (!empty($lot_code)){
                $htmlLotCode = '<div>Lot code : '.$lot_code.'</div>';
            }
            $row = array();

            $row[0] = '<div class="text-center">'.(++$key).'</div>';
            $row[1] ='<div class="text-left">'._dhau($date_tranfer).'</div>';
            $row[2] ='<div class="text-left">'.$code_tranfer.'</div>';
            $row[3] ='<div class="text-left">'.$code.'</div>';
            $row[4] = '<div class="text-left">'.$name.'</div>';
            $row[5] = '<div class="text-left">'.$name_warehouse.$htmlLotCode.'</div>';
            $row[6] = $customer_name;
            if ($aRow['type_order'] == 'orders') {
                $row[7] = '<div class="text-left">
							 '.$reference_no.' 
						</div>';
            } else {
                $row[7] = '<div class="text-left">
							' . $reference_no . '
						</div>';
            }
            $row[8] = '<div class="text-center bold">' . ($aRow['quantity'] > 0 ? formatNumber($aRow['quantity']) : '') . '</div>';
            $row[9] = '<div class="text-center bold">' . ($aRow['quantity_old'] > 0 ? formatNumber($aRow['quantity_old']) : 0) . '</div>';
            $output['aaData'][] = $row;

        }
        echo json_encode($output);
    }

    public function getSyntheticHoldPlan()
    {
        $name_search = $this->input->post('name_search');
        $status_table = $this->input->post('status_table');
        $plan_search = $this->input->post('plan_search');
        $items_search = $this->input->post('items_search');
        $end_date_search = $this->input->post('end_date_search_new');
        $start_date_search = $this->input->post('start_date_search_new');
        $productions_orders_search = $this->input->post('productions_orders_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $countQtyItemsTranferPlan = "(
            SELECT
                tbltransfer_warehouse_detail.id,
                SUM(tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) as quantity
            FROM tbltransfer_warehouse_detail
            INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
            INNER JOIN tblwarehouse_product ON tblwarehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer AND tbltransfer_warehouse_detail.warehouses_to = tblwarehouse_product.warehouse_id AND tblwarehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to AND (tblwarehouse_product.type_items = 'product' OR tblwarehouse_product.type_items = 'nvl' OR tblwarehouse_product.type_items = 'tools') AND tblwarehouse_product.product_id = tbltransfer_warehouse_detail.id_items AND tblwarehouse_product.type_export = 2 AND tblwarehouse_product.quantity > 0
            WHERE tbltransfer_warehouse_detail.warehouses_to != ".WAREHOUSES_HOLD." AND (tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) > 0
            AND (COALESCE(tblwarehouse_product.lot_code, -1) = COALESCE(tbltransfer_warehouse_detail.lot_code, -1)) AND (COALESCE(tblwarehouse_product.date_sx, -1) = COALESCE(tbltransfer_warehouse_detail.date_sx, -1))
            AND (COALESCE(tblwarehouse_product.date_sd, -1) = COALESCE(tbltransfer_warehouse_detail.date_sd, -1)) AND (COALESCE(tblwarehouse_product.date_use, -1) = COALESCE(tbltransfer_warehouse_detail.date_use, -1))
            GROUP BY tbltransfer_warehouse_detail.id
        ) tb_count_qty_tranfer_plan";

        $tbProductionsOrders = "(
            SELECT
                tbl_productions_orders_items.plan_id as plan_id,
                GROUP_CONCAT(distinct tbl_productions_orders.reference_no,'__',tbl_productions_orders.id SEPARATOR '||') as reference_no,
                GROUP_CONCAT(distinct tbl_productions_orders.id) as productions_order_id
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
            GROUP BY tbl_productions_orders_items.plan_id
        ) tb_productions_orders";

        $tableAllItems = "(
             (
                SELECT
                    tbl_materials.id as id,
                    tbl_materials.name as name,
                    tbl_materials.code as code,
                    'materials' as type_items,
                    tblunits.unit as unit_name,
                    tbl_productions_plan.id as order_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tb_productions_orders.reference_no as productions_orders,
                    tb_productions_orders.productions_order_id as productions_order_id,
                    '' as customer_name,
                    COALESCE(tb_count_qty_tranfer_plan.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'plan' as type_order,
                    'materials' as type_group
                FROM tbl_materials 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_materials.id AND (type = 'nvl')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN $tbProductionsOrders ON tb_productions_orders.plan_id = tbl_productions_plan.id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
                LEFT JOIN $countQtyItemsTranferPlan ON tb_count_qty_tranfer_plan.id = tbltransfer_warehouse_detail.id
                GROUP BY tbltransfer_warehouse_detail.id
            )
            UNION ALL
            (
                SELECT
                    tbl_tools_supplies.id as id,
                    tbl_tools_supplies.name as name,
                    tbl_tools_supplies.code as code,
                    'tools_supplies' as type_items,
                    tblunits.unit as unit_name,
                    tbl_productions_plan.id as order_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tb_productions_orders.reference_no as productions_orders,
                    tb_productions_orders.productions_order_id as productions_order_id,
                    '' as customer_name,
                    COALESCE(tb_count_qty_tranfer_plan.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'plan' as type_order,
                    'tools_supplies' as type_group
                FROM tbl_tools_supplies 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_tools_supplies.id AND (tbltransfer_warehouse_detail.type = 'tools')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN $tbProductionsOrders ON tb_productions_orders.plan_id = tbl_productions_plan.id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id
                LEFT JOIN $countQtyItemsTranferPlan ON tb_count_qty_tranfer_plan.id = tbltransfer_warehouse_detail.id
                GROUP BY tbltransfer_warehouse_detail.id
            )
            UNION ALL
            (
                SELECT
                    tbl_products.id as id,
                    tbl_products.name as name,
                    tbl_products.code as code,
                    tbl_products.type_products as type_items,
                    tblunits.unit as unit_name,
                    tbl_productions_plan.id as order_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tb_productions_orders.reference_no as productions_orders,
                    tb_productions_orders.productions_order_id as productions_order_id,
                    '' as customer_name,
                    COALESCE(tb_count_qty_tranfer_plan.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'plan' as type_order,
                    'products' as type_group
                FROM tbl_products 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_products.id AND (tbltransfer_warehouse_detail.type = 'product')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN $tbProductionsOrders ON tb_productions_orders.plan_id = tbl_productions_plan.id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                LEFT JOIN $countQtyItemsTranferPlan ON tb_count_qty_tranfer_plan.id = tbltransfer_warehouse_detail.id
                GROUP BY tbltransfer_warehouse_detail.id
            )
        ) table_all_item";


        $aColumns = [
            'table_all_item.id as id',
            'table_all_item.date_tranfer as date_tranfer',
            'table_all_item.code_tranfer as code_tranfer',
            'table_all_item.code as code',
            'table_all_item.name as name',
            'table_all_item.name_warehouse as name_warehouse',
            'table_all_item.customer_name as customer_name',
            'table_all_item.reference_no as reference_no',
            'table_all_item.productions_orders as productions_orders',
            'table_all_item.quantity as quantity'
        ];
        $sIndexColumn = 'table_all_item.id';
        $sTable = $tableAllItems;
        $where = [

        ];
        $filter = [];

        $join = [
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, "AND table_all_item.date_tranfer >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, "AND table_all_item.date_tranfer <= '" . $end_date_search . "'");
        }

        if (!empty($productions_orders_search)){
            array_push(
                $where,
                'AND FIND_IN_SET('.$productions_orders_search.',table_all_item.productions_order_id)'
            );
        }

        if (!empty($plan_search)){
            array_push(
                $where,
                'AND table_all_item.order_id = '.$plan_search.''
            );
        }

        if (!empty($items_search)){
            $items_search = explode('__',$items_search);
            array_push(
                $where,
                'AND (table_all_item.id = '.$items_search[0].') AND table_all_item.type_items = "'.$items_search[1].'"'
            );
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'table_all_item.type_items as type_items, 
             table_all_item.id as id, 
             table_all_item.order_id as order_id, 
             table_all_item.quantity_old as quantity_old, 
             table_all_item.type_order as type_order, 
             table_all_item.productions_order_id,
             table_all_item.lot_code,
             table_all_item.type_group as type_group'
        ], '', [], ['union_all' => true]);


        $output = $result['output'];
        $rResult = $result['rResult'];
//        usort($rResult, ch_make_cmp(['id' => "asc","order_id" => 'asc']));
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $item_id = $aRow['id'];
            $item_type = $aRow['type_items'];
            $reference_no = $aRow['reference_no'];
            $customer_name = $aRow['customer_name'];
            $order_id = $aRow['order_id'];
            $name = $aRow['name'];
            $code = $aRow['code'];
            $date_tranfer = $aRow['date_tranfer'];
            $code_tranfer = $aRow['code_tranfer'];
            $name_warehouse = $aRow['name_warehouse'];
            $lot_code = $aRow['lot_code'];

            $htmlLotCode = '';
            if (!empty($lot_code)){
                $htmlLotCode = '<div>Lot code : '.$lot_code.'</div>';
            }

            $row = array();

            $row[0] = '<div class="text-center">'.(++$key).'</div>';
            $row[1] ='<div class="text-left">'._dhau($date_tranfer).'</div>';
            $row[2] ='<div class="text-left">'.$code_tranfer.'</div>';
            $row[3] ='<div class="text-left">'.$code.'</div>';
            $row[4] = '<div class="text-left">'.$name.'</div>';
            $row[5] = '<div class="text-left">'.$name_warehouse.$htmlLotCode.'</div>';
            if ($aRow['type_order'] == 'orders') {
                $row[6] = '<div class="text-left">
							<a data-tnh="modal" class="tnh-modal" href="' . base_url() . 'admin/orders/view_order/' . $order_id . '" data-toggle="modal" data-target="#myModal">' . $reference_no . '</a>
						</div>';
            } else {
                $row[6] = '<div class="text-left">
							' . $reference_no . '
						</div>';
            }
            $productions_orders = $aRow['productions_orders'];
            $productions_orders = explode('||',$productions_orders);
            $htmlProOrders = '';
            if (!empty($productions_orders)){
                foreach ($productions_orders as $kk => $vv){
                    $proOrders = explode('__',$vv);
                    $htmlProOrders .= $proOrders[0];
                }
            }
            $row[7] = '<div class="text-left">
							'.$htmlProOrders.'
						</div>';
            $row[8] = '<div class="text-center bold">' . ($aRow['quantity'] > 0 ? formatNumber($aRow['quantity']) : '') . '</div>';
            $row[9] = '<div class="text-center bold">' . ($aRow['quantity_old'] > 0 ? formatNumber($aRow['quantity_old']) : 0) . '</div>';
            $output['aaData'][] = $row;

        }
        echo json_encode($output);
    }

    function searchProductionsPlanNew()
    {
        $data = [];
        $result = [];
        if ($this->input->get())
        {
            $q = $this->input->get('term');
            $limit = 50;
            $this->db->select('tbl_productions_plan.id as id, CONCAT(tbl_productions_plan.reference_no, "(", DATE_FORMAT(tbl_productions_plan.date, "%d/%m/%Y"),")") as text', false);
            $this->db->from('tbl_productions_plan');
            if (!empty($q))
            {
                $this->db->group_start();
                $this->db->like('tbl_productions_plan.reference_no', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $result = $this->db->get()->result_array();
        }
        $data['results'] = [
            [
                'text' => lang('Kế hoạch NPL'), 'children' => $result
            ],
        ];
        echo json_encode($data);
    }

    function searchItemsNew()
    {
        $data = [];
        $result = [];
        $resultNVL = [];
        if ($this->input->get())
        {
            $q = $this->input->get('term');
            $limit = 50;
            $this->db->select('CONCAT(tbl_products.id,"__",tbl_products.type_products) as id, CONCAT(tbl_products.name, "(", tbl_products.code,")") as text', false);
            $this->db->from('tbl_products');
            $this->db->where('tbl_products.type_products != "products"');
            if (!empty($q))
            {
                $this->db->group_start();
                $this->db->like('tbl_products.name', $q);
                $this->db->or_like('tbl_products.code', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $result = $this->db->get()->result_array();

            $this->db->select('CONCAT(tbl_materials.id,"__materials") as id, CONCAT(tbl_materials.name, "(", tbl_materials.code,")") as text', false);
            $this->db->from('tbl_materials');
            if (!empty($q))
            {
                $this->db->group_start();
                $this->db->like('tbl_materials.name', $q);
                $this->db->or_like('tbl_materials.code', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $resultNVL = $this->db->get()->result_array();
        }
        $data['results'] = [
            [
                'text' => lang('Bán thành phẩm'), 'children' => $result
            ],
            [
                'text' => lang('Nguyên phụ liệu'), 'children' => $resultNVL
            ],
        ];
        echo json_encode($data);
    }

    public function print_pdf( $type_pdf = 'I'){
        $product_search = $this->input->get('product_search');
        $orders_search = $this->input->get('orders_search');
        $end_date_search = $this->input->get('end_date_search');
        $start_date_search = $this->input->get('start_date_search');

        $countQtyItemsTranfer = "(
            SELECT
                tbltransfer_warehouse_detail.id,
                SUM(tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) as quantity
            FROM tbltransfer_warehouse_detail
            INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
            INNER JOIN tbl_orders  ON tbl_orders.id = tbltransfer_warehouse.order_id_new
            INNER JOIN tblwarehouse_product ON tblwarehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer AND tbltransfer_warehouse_detail.warehouses_to = tblwarehouse_product.warehouse_id AND tblwarehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to AND tblwarehouse_product.type_items = 'product' AND tblwarehouse_product.product_id = tbltransfer_warehouse_detail.id_items AND tblwarehouse_product.type_export = 2 AND tblwarehouse_product.quantity > 0
            WHERE tbltransfer_warehouse_detail.warehouses_to != ".WAREHOUSES_CAPACITY." AND (tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) > 0
            AND (COALESCE(tblwarehouse_product.lot_code, -1) = COALESCE(tbltransfer_warehouse_detail.lot_code, -1)) AND (COALESCE(tblwarehouse_product.date_sx, -1) = COALESCE(tbltransfer_warehouse_detail.date_sx, -1))
            AND (COALESCE(tblwarehouse_product.date_sd, -1) = COALESCE(tbltransfer_warehouse_detail.date_sd, -1)) AND (COALESCE(tblwarehouse_product.date_use, -1) = COALESCE(tbltransfer_warehouse_detail.date_use, -1))
            GROUP BY tbltransfer_warehouse_detail.id
        ) tb_count_qty_tranfer";


        $where = '';
        if (!empty($product_search)){
            $product_search = explode('__',$product_search);
            $where .=' AND (tbl_products.id = '.$product_search[0].') AND tbl_products.type_products = "'.$product_search[1].'"';
        }

        if (!empty($orders_search)){
            $where .= ' AND (tbl_orders.order_id = '.$orders_search.')';
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            $where .= " AND tbltransfer_warehouse.date >= '" . $start_date_search . "'";
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            $where .=" AND tbltransfer_warehouse.date <= '" . $end_date_search . "'";
        }

        $tableAllItems = "(
            (
                SELECT
                    tbl_products.id as id,
                    tbl_products.name as name,
                    tbl_products.code as code,
                    tbl_products.type_products as type_items,
                    tblunits.unit as unit_name,
                    tbl_orders.id as order_id,
                    tbl_orders.reference_no as reference_no,
                    tbl_orders.customer_name as customer_name,
                    COALESCE(tb_count_qty_tranfer.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'orders' as type_order,
                    'products' as type_group
                FROM tbl_products 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_products.id AND ( type = 'product')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_orders ON tbl_orders.id = tbltransfer_warehouse.order_id_new
                INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                LEFT JOIN $countQtyItemsTranfer ON tb_count_qty_tranfer.id = tbltransfer_warehouse_detail.id
                WHERE tbl_products.id > 0 $where
                GROUP BY tbltransfer_warehouse_detail.id
            )
        )";

        $query = $this->db->query($tableAllItems)->result_array();
        $trItems = '';
        if (!empty($query)) {
            foreach ($query as $key => $value) {

                $type_item = $value['type_items'];
                $items_id = $value['id'];

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                    $model = $info['model'];
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                    $info = $this->tools_supplies_model->rowToolsSupplies($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['avatar'])) {
                        $images = base_url('uploads/tools_supplies/' . $info['images']);
                    }
                }

                $date_tranfer = $value['date_tranfer'];
                $code_tranfer = $value['code_tranfer'];
                $name_warehouse = $value['name_warehouse'];
                $lot_code = $value['lot_code'];

                $htmlLotCode = '';
                if (!empty($lot_code)){
                    $htmlLotCode = '<div>Lot code : '.$lot_code.'</div>';
                }

                $tdNumber = '<td class="text-center">
					' . ++$key . '
				</td>';

                $tdDate = '<td style="text-align: left;">' . _dhau($date_tranfer) . '</td>';
                $tdCodeTranfer = '<td style="text-align: left;">' . $code_tranfer . '</td>';
                $tdCode = '<td style="text-align: left;">' . $info['code'] . '</td>';
                $tdName = '<td style="text-align: left;">' . $info['name'] . '</td>';
                $tdWarehouse = '<td style="text-align: left;">' . $name_warehouse .$htmlLotCode. '</td>';

                $tdCustomer = '<td class="tdLandscapePrintSize text-left">'.($value['customer_name']).'</td>';
                $tdOrder = '<td class="tdVerticalPrintSize text-left">'.($value['reference_no']).'</td>';
                $tdQuantity = '<td class="tdNumberChildrenSize text-center">'.formatNumber($value['quantity']).'</td>';
                $tdQuantityOld = '<td class="tdExchangeValue text-center">'.formatNumber($value['quantity_old']).'</td>';

                $trItems.= '<tr nobr="true">
					'.$tdNumber.'
					'.$tdDate.'
					'.$tdCodeTranfer.'
					'.$tdCode.'
					'.$tdName.'
					'.$tdWarehouse.'
					'.$tdCustomer.'
					'.$tdOrder.'
					'.$tdQuantity.'
				</tr>';
            }
        }

        ob_end_clean();
        ob_start();
        stylePdf();
        $day = date( 'd');
        $month = date('m');
        $year = date('Y');
        $staff = get_staff_full_name();
        echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
			<tr nobr="true">
				<td colspan="8" class="text-center"><span class="text-center uppercase" style="font-size: 20px; font-weight: bold;">' . _l('Thống kê giữ hàng thành phẩm') . '</span><br><span>Ngày in: '.date('d/m/Y').'</span><br></td>
			</tr>
		</table>
		<table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
			<tr nobr="true" style="background-color: #ddd;">
				<td class="bold text-center" style="width: 5%;">' . _l('tnh_numbers') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Ngày giữ') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Mã phiếu') . '</td>
				<td class="bold text-center" style="width: 15%;">' . _l('dt_product_code') . '</td>
				<td class="bold text-center" style="width: 15%;">' . _l('dt_product_name') . '</td>
				<td class="bold text-center" style="width: 11%;">' . _l('Kho hàng') . '</td>
				<td class="bold text-center" style="width: 15%;">' . _l('Khách hàng') . '</td>
				<td class="bold text-center" style="width: 11%;">' . _l('Số đơn hàng') . '</td>
				<td class="bold text-center" style="width: 8%;">' . _l('Số lượng giữ') . '</td>
			</tr>
			'.$trItems.'
		</table>
		<br><br>
            <table style="width: 100%">
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td></td>
                    <td></td>
                    <td class="text-center">
                        <span class="bold">Người Lập</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                        <br>
                    </td>
                </tr>
            </table>
		';
        $content = ob_get_contents();

        ob_end_clean();
        // $data['showHeader'] = 'hide';
        // $data['type_print'] = 'quotes';
        $data['content'] = $content;
        $data['barcode'] = '';
        $data['type'] = 'L';
        $data['img'] = '';
        $data['pageCustome'] = 'orders_detail';
        $pdf = @print_pdf_tnh_new($data);
        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }

    public function print_pdf_plan( $type_pdf = 'I'){
        $product_search = $this->input->get('product_search');
        $orders_search = $this->input->get('orders_search');
        $end_date_search = $this->input->get('end_date_search_new');
        $start_date_search = $this->input->get('start_date_search_new');
        $productions_orders_search = $this->input->get('productions_orders_search');

        $countQtyItemsTranferPlan = "(
            SELECT
                tbltransfer_warehouse_detail.id,
                SUM(tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) as quantity
            FROM tbltransfer_warehouse_detail
            INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
            INNER JOIN tblwarehouse_product ON tblwarehouse_product.import_id = tbltransfer_warehouse_detail.id_transfer AND tbltransfer_warehouse_detail.warehouses_to = tblwarehouse_product.warehouse_id AND tblwarehouse_product.localtion = tbltransfer_warehouse_detail.localtion_to AND (tblwarehouse_product.type_items = 'product' OR tblwarehouse_product.type_items = 'nvl' OR tblwarehouse_product.type_items = 'tools') AND tblwarehouse_product.product_id = tbltransfer_warehouse_detail.id_items AND tblwarehouse_product.type_export = 2 AND tblwarehouse_product.quantity > 0
            WHERE tbltransfer_warehouse_detail.warehouses_to != ".WAREHOUSES_HOLD." AND (tbltransfer_warehouse_detail.quantity_net - tblwarehouse_product.quantity_export) > 0
            AND (COALESCE(tblwarehouse_product.lot_code, -1) = COALESCE(tbltransfer_warehouse_detail.lot_code, -1)) AND (COALESCE(tblwarehouse_product.date_sx, -1) = COALESCE(tbltransfer_warehouse_detail.date_sx, -1))
            AND (COALESCE(tblwarehouse_product.date_sd, -1) = COALESCE(tbltransfer_warehouse_detail.date_sd, -1)) AND (COALESCE(tblwarehouse_product.date_use, -1) = COALESCE(tbltransfer_warehouse_detail.date_use, -1))
            GROUP BY tbltransfer_warehouse_detail.id
        ) tb_count_qty_tranfer_plan";

        $tbProductionsOrders = "(
            SELECT
                tbl_productions_orders_items.plan_id as plan_id,
                GROUP_CONCAT(distinct tbl_productions_orders.reference_no) as reference_no,
                GROUP_CONCAT(distinct tbl_productions_orders.id) as productions_order_id
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
            GROUP BY tbl_productions_orders_items.plan_id
        ) tb_productions_orders";


        $where = '';
        $whereNew = '';
        $whereNewVs1 = '';
        $whereNewVs2 = '';
        if (!empty($product_search)){
            $product_search = explode('__',$product_search);
            $whereNew .=' AND (tbl_products.id = '.$product_search[0].') AND tbl_products.type_products = "'.$product_search[1].'"';
            $whereNewVs1 .=' AND (tbl_tools_supplies.id = '.$product_search[0].')';
            $whereNewVs2 .=' AND (tbl_materials.id = '.$product_search[0].') ';
        }

        if (!empty($orders_search)){
            $where .= ' AND (tbl_productions_plan.id = '.$orders_search.')';
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            $where .= " AND tbltransfer_warehouse.date >= '" . $start_date_search . "'";
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            $where .=" AND tbltransfer_warehouse.date <= '" . $end_date_search . "'";
        }

        if (!empty($productions_orders_search)){
            $where .= ' AND  FIND_IN_SET('.$productions_orders_search.',tb_productions_orders.productions_order_id)';
        }

        $tableAllItems = "
             
                SELECT
                    tbl_materials.id as id,
                    tbl_materials.name as name,
                    tbl_materials.code as code,
                    'materials' as type_items,
                    tblunits.unit as unit_name,
                    tbl_productions_plan.id as order_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tb_productions_orders.reference_no as productions_orders,
                    tb_productions_orders.productions_order_id as productions_order_id,
                    '' as customer_name,
                    COALESCE(tb_count_qty_tranfer_plan.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'plan' as type_order,
                    'materials' as type_group
                FROM tbl_materials 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_materials.id AND (type = 'nvl')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN $tbProductionsOrders ON tb_productions_orders.plan_id = tbl_productions_plan.id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
                LEFT JOIN $countQtyItemsTranferPlan ON tb_count_qty_tranfer_plan.id = tbltransfer_warehouse_detail.id
                WHERE tbl_materials.id > 0 $where $whereNewVs2
                GROUP BY tbltransfer_warehouse_detail.id
            
            UNION ALL
            
                SELECT
                    tbl_tools_supplies.id as id,
                    tbl_tools_supplies.name as name,
                    tbl_tools_supplies.code as code,
                    'tools_supplies' as type_items,
                    tblunits.unit as unit_name,
                    tbl_productions_plan.id as order_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tb_productions_orders.reference_no as productions_orders,
                    tb_productions_orders.productions_order_id as productions_order_id,
                    '' as customer_name,
                    COALESCE(tb_count_qty_tranfer_plan.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'plan' as type_order,
                    'tools_supplies' as type_group
                FROM tbl_tools_supplies 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_tools_supplies.id AND (tbltransfer_warehouse_detail.type = 'tools')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN $tbProductionsOrders ON tb_productions_orders.plan_id = tbl_productions_plan.id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id
                LEFT JOIN $countQtyItemsTranferPlan ON tb_count_qty_tranfer_plan.id = tbltransfer_warehouse_detail.id
                WHERE tbl_tools_supplies.id > 0 $where $whereNewVs1
                GROUP BY tbltransfer_warehouse_detail.id
            
            UNION ALL
            
                SELECT
                    tbl_products.id as id,
                    tbl_products.name as name,
                    tbl_products.code as code,
                    tbl_products.type_products as type_items,
                    tblunits.unit as unit_name,
                    tbl_productions_plan.id as order_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tb_productions_orders.reference_no as productions_orders,
                    tb_productions_orders.productions_order_id as productions_order_id,
                    '' as customer_name,
                    COALESCE(tb_count_qty_tranfer_plan.quantity,0) as quantity_old,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    tblwarehouse.name as name_warehouse,
                    tbltransfer_warehouse_detail.lot_code as lot_code,
                    tbltransfer_warehouse.date as date_tranfer,
                    CONCAT(tbltransfer_warehouse.prefix,'-',tbltransfer_warehouse.code) as code_tranfer,
                    'plan' as type_order,
                    'products' as type_group
                FROM tbl_products 
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_items = tbl_products.id AND (tbltransfer_warehouse_detail.type = 'product')
                INNER JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbltransfer_warehouse.productions_capacity_id
                INNER JOIN tblwarehouse ON tblwarehouse.id = tbltransfer_warehouse_detail.warehouses_id
                LEFT JOIN $tbProductionsOrders ON tb_productions_orders.plan_id = tbl_productions_plan.id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                LEFT JOIN $countQtyItemsTranferPlan ON tb_count_qty_tranfer_plan.id = tbltransfer_warehouse_detail.id
                WHERE tbl_products.id > 0 $where $whereNew
                GROUP BY tbltransfer_warehouse_detail.id
            
        ";

        $query = $this->db->query($tableAllItems)->result_array();
        $trItems = '';
        if (!empty($query)) {
            foreach ($query as $key => $value) {

                $type_item = $value['type_items'];
                $items_id = $value['id'];

                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                    $model = $info['model'];
                } elseif ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                } elseif ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                    $info = $this->tools_supplies_model->rowToolsSupplies($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['avatar'])) {
                        $images = base_url('uploads/tools_supplies/' . $info['images']);
                    }
                }

                $date_tranfer = $value['date_tranfer'];
                $code_tranfer = $value['code_tranfer'];
                $name_warehouse = $value['name_warehouse'];
                $lot_code = $value['lot_code'];

                $htmlLotCode = '';
                if (!empty($lot_code)){
                    $htmlLotCode = '<div>Lot code : '.$lot_code.'</div>';
                }

                $tdNumber = '<td class="text-center">
					' . ++$key . '
				</td>';

                $tdDate = '<td style="text-align: left;">' . _dhau($date_tranfer) . '</td>';
                $tdCodeTranfer = '<td style="text-align: left;">' . $code_tranfer . '</td>';
                $tdCode = '<td style="text-align: left;">' . $info['code'] . '</td>';
                $tdName = '<td style="text-align: left;">' . $info['name'] . '</td>';
                $tdWarehouse = '<td style="text-align: left;">' . $name_warehouse .$htmlLotCode. '</td>';
                $tdProdcutionOrder = '<td class="tdVerticalPrintSize text-left">'.($value['productions_orders']).'</td>';
                $tdOrder = '<td class="tdVerticalPrintSize text-left">'.($value['reference_no']).'</td>';
                $tdQuantity = '<td class="tdNumberChildrenSize text-center">'.formatNumber($value['quantity']).'</td>';
                $tdQuantityOld = '<td class="tdExchangeValue text-center">'.formatNumber($value['quantity_old']).'</td>';

                $trItems.= '<tr nobr="true">
					'.$tdNumber.'
					'.$tdDate.'
					'.$tdCodeTranfer.'
					'.$tdCode.'
					'.$tdName.'
					'.$tdWarehouse.'
					'.$tdOrder.'
					'.$tdProdcutionOrder.'
					'.$tdQuantity.'
				</tr>';
            }
        }

        ob_end_clean();
        ob_start();
        stylePdf();
        $day = date( 'd');
        $month = date('m');
        $year = date('Y');
        $staff = get_staff_full_name();
        echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
			<tr nobr="true">
				<td colspan="8" class="text-center"><span class="text-center uppercase" style="font-size: 20px; font-weight: bold;">' . _l('Thống kê giữ hàng nguyên phụ liệu') . '</span><br><span>Ngày in: '.date('d/m/Y').'</span><br></td>
			</tr>
		</table>
		<table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
			<tr nobr="true" style="background-color: #ddd;">
				<td class="bold text-center" style="width: 5%;">' . _l('tnh_numbers') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Ngày giữ') . '</td>
				<td class="bold text-center" style="width: 10%;">' . _l('Mã phiếu') . '</td>
				<td class="bold text-center" style="width: 15%;">' . _l('dt_nvl_code') . '</td>
				<td class="bold text-center" style="width: 15%;">' . _l('dt_nvl_name') . '</td>
				<td class="bold text-center" style="width: 14%;">' . _l('Kho hàng') . '</td>
				<td class="bold text-center" style="width: 12%;">' . _l('Số kế hoạch NPL') . '</td>
				<td class="bold text-center" style="width: 12%;">' . _l('Lệnh sản xuất tổng') . '</td>
				<td class="bold text-center" style="width: 8%;">' . _l('Số lượng giữ') . '</td>
			</tr>
			'.$trItems.'
		</table>
		<br><br>
            <table style="width: 100%">
                <tr nobr="true" class="text-center">
                    <td></td>
                    <td></td>
                    <td><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></td>
                </tr>
                <tr nobr="true">
                    <td></td>
                    <td></td>
                    <td class="text-center">
                        <span class="bold">Người Lập</span><br>
                        <span>(Ký, ghi rõ họ tên)</span>
                        <br>
                    </td>
                </tr>
            </table>
		';
        $content = ob_get_contents();

        ob_end_clean();
        // $data['showHeader'] = 'hide';
        // $data['type_print'] = 'quotes';
        $data['content'] = $content;
        $data['barcode'] = '';
        $data['type'] = 'L';
        $data['img'] = '';
        $data['pageCustome'] = 'orders_detail';
        $pdf = @print_pdf_tnh_new($data);
        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }
}