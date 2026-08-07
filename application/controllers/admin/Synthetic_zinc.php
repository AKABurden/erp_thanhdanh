<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Synthetic_zinc extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('tools_supplies_model');
    }

    public function index(){
        $data['type_print'] = $this->products_model->getTypePrint();
        $data['title'] = lang('Tổng hợp xuất kẽm');
        $data['CategoryStages'] = recursiveCategoryStages();
        $this->load->view('admin/synthetic_zinc/index', $data);
    }

    public function getSyntheticZinc(){

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $status_table = $this->input->post('status_table');
        $productions_orders_search = $this->input->post('productions_orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $orders_search = $this->input->post('orders_search');
        $business_plan_search = $this->input->post('business_plan_search');
        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        $type_print_search = $this->input->post('type_print_search');
        $customer_search = $this->input->post('customer_search');
        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
            }
        }

        $period_time = $this->input->post('period_time');
        if (!empty($period_time)) {
            $period_time = explode('-', $period_time);
            $start_date_search = trim($period_time[0]);
            $end_date_search = trim($period_time[1]);
        }

        $items_search = $this->input->post('items_search');
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
        }

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";

        $tbPurchasesErrors = "(
            SELECT
                tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            WHERE tbl_purchase_products.is_errors = 1
            GROUP BY tbl_purchase_products.productions_orders_details_id
        ) tb_purchases_errors";


        // $tbProductionsOrderItems = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         tbl_productions_plan.note as note_plan,
        //         tbl_productions_orders_items.items_id as items_id,
        //         tbl_productions_orders_items.items_name as items_name,
        //         tbl_productions_orders_items.items_code as items_code,
        //         SUM(tbl_productions_orders_items.quantity) as quantity,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        //         tbl_productions_orders_items.plan_id as plan_id,
        //         SUM(tb_purchases_errors.quantity_errors) as quantity_errors
        //     FROM tbl_productions_orders_items
        //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        //     LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_production_order_item";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                GROUP_CONCAT(tbl_productions_orders_items.id) as _poi_id,
                GROUP_CONCAT(tbl_productions_orders_details.id) as _pod_id,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id,
                SUM(0) as quantity_errors
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";


        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.code as items_code',
            'tbl_products.name as items_name',
            '"" as vertical',
            '(tb_production_order_item.quantity) as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity_warehoused as quantity_finished',
            'tb_production_order_item.quantity_errors as quantity_errors',
            'tb_production_order_item.quantity_errors as quantity_errors',
            'tb_production_order_item.quantity_errors as quantity_errors',
            'tb_production_order_item.note_plan as note',
            '"" as status'
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $where        = [
        ];
        $filter = [];

        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_productions_orders.location_id',
            'LEFT JOIN '.$tbProductionsOrderItems.' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
            'LEFT JOIN tbl_type_print ON tbl_type_print.id = tbl_products.type_print',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_productions_orders.created_by',
            'LEFT JOIN '.$tbProductionsPlanOrdersByOrders.' ON tb_orders.productions_order_id = tbl_productions_orders.id',
            'LEFT JOIN '.$tbProductionsPlanOrdersByBusinessPlan.' ON tb_business_plan.productions_order_id = tbl_productions_orders.id',
        ];

        array_push($where, "AND EXISTS (
                SELECT tbl_productions_orders_items_stages.productions_orders_id
                FROM tbl_productions_orders_items_stages
                JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
                WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id
                AND tbl_category_stages.check_offset = 1
            )");

        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id = ".$this->db->escape($productions_orders_search));
        }

        if (!empty($customer_search)){
            array_push($where, "AND EXISTS (
                 SELECT tbl_productions_orders_details.productions_orders_id
                 FROM tbl_productions_orders_details
                 JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = 'orders'
                 WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
                 AND tbl_orders.customer_id = $customer_search
             )");
        }

        if(!empty($type_print_search)){
            $type_print_search = explode('__',$type_print_search);
            if($type_print_search[0] == 'main'){
                array_push($where, "AND EXISTS (
                    SELECT tbl_productions_orders_items_stages.productions_orders_id
                    FROM tbl_productions_orders_items_stages
                    JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                    JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
                    WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id
                    AND tbl_category_stages.id = ".$type_print_search[1]."
                )");
            }elseif($type_print_search[0] == 'detail'){
                array_push($where, "AND EXISTS (
                    SELECT tbl_productions_orders_items_stages.productions_orders_id
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id
                    AND tbl_productions_orders_items_stages.stage_id = ".$type_print_search[1]."
                )");
            }

        }

        if (!empty($item_id)) {
            $item_id = $this->db->escape($item_id);
            array_push($where, "AND EXISTS (
                SELECT tbl_productions_orders_items.id
                FROM tbl_productions_orders_items
                WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_items.items_id = $item_id
            )");
        }

        if (!empty($orders_search)) {
            $orders_search = $this->db->escape($orders_search);
            array_push($where, "AND EXISTS (
                SELECT tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.productions_plan_id = $orders_search AND tbl_productions_plan_orders.object_type = 'orders'
            )");
        }

        if (!empty($business_plan_search)) {
            $business_plan_search = $this->db->escape($business_plan_search);
            array_push($where, "AND EXISTS (
                SELECT tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.productions_plan_id = $business_plan_search AND tbl_productions_plan_orders.object_type = 'business_plan'
            )");
        }


        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            array_push($where, 'AND tbl_productions_orders.location_id IN ('.implode(',', $branch_staff).')');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblbranch.name as location_name',
            'tbl_type_print.name as name_type_print',
            'tbl_productions_orders.status_details',
            'tbl_productions_orders.options1 as options1',
            'tbl_productions_orders.options2 as options2',
            'tb_orders.reference_no_orders as reference_no_orders',
            'tb_business_plan.reference_no_business_plan as reference_no_business_plan',
            'tbl_productions_orders.total_quantity as total_quantity',
            'tb_production_order_item.items_id as items_id',
            // 'tb_production_order_item.items_code as items_code',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
            'tbl_productions_orders.status_orders as status_orders',
            'tb_production_order_item.plan_id as plan_id',
            'tb_production_order_item._poi_id as _poi_id',
            'tb_production_order_item._pod_id as _pod_id',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $total_quantity_sx = 0;
        $total_quantity_hold = 0;
        $total_quantity_new = 0;
        $total_quantity_finished = 0;
        $total_quantity_errors = 0;
        $group_id = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $productions_orders_id = $aRow['id'];
            $items_id = $aRow['items_id'];
            $status_details = $aRow['status_details'];
            $row[0] = '<div class="text-center">'.$start.'</div>';
            $row[1] = _d($aRow['date']);
            $row[2] = '
                <div class="bold"><a target="_blank" href="'.base_url('admin/manufactures/detail_productions_orders/'.$productions_orders_id).'">'.$aRow['reference_no'].'</a></div><div class="italic">'.$aRow['location_name'].'</div>
            ';

            $_poi_id = $aRow['_poi_id'];
            $_pod_id = $aRow['_pod_id'];

            $tbPurchasesErrors = "(
                SELECT
                    tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                    SUM(tbl_purchase_products.total_quantity) as quantity_errors
                FROM tbl_purchase_products
                WHERE tbl_purchase_products.is_errors = 1 AND tbl_purchase_products.productions_orders_details_id IN ($_pod_id)
            )";
            $_query = $this->db->query($tbPurchasesErrors)->row_array();
            $aRow['quantity_errors'] = !empty($_query['quantity_errors']) ? $_query['quantity_errors'] : 0;

            $flagGroup = false;
            if ($group_id != $productions_orders_id) {
                $group_id = $productions_orders_id;
                $flagGroup = true;
            }

            $this->db->select('
                tbl_products.code as product_code,
                tbl_products.name as product_name,
                GROUP_CONCAT(DISTINCT ppb_materials.landscape_print_size SEPARATOR "<br>") as landscape_print_size,
                SUM(ppb_materials.paper_exchange) as paper_exchange,
            ', false);
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            $this->db->where('(ppb_materials.item_type)', 'materials');
            $this->db->where('(tbl_productions_orders_items.items_id)', $items_id);
            $dtQuantityNew = $this->db->get()->row_array();

            $plan_id = $aRow['plan_id'];

            // $quantityNew = $dtQuantityNew['paper_exchange'];
            $this->db->select('
                (ppb_materials.item_type) as type, 
                (ppb_materials.item_id), 
                (ppb_materials.landscape_print_size), 
                (ppb_materials.number_children_size), 
                (ppb_materials.unit_parent_id), 
                (ppb_materials.quantity_single),
                SUM(ppb_materials.quantity) as quantity,
                (ppb_materials.quantity_single) as quantity_single,
            ', false);
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            // $this->db->where('(ppb_materials.item_type)', 'materials');
            $this->db->where('(tbl_productions_orders_items.items_id)', $items_id);

            $this->db->where('(
                ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                OR exists (
                    SELECT
                        tbl_materials.id
                    FROM tbl_materials
                    INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                    WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                )
            )', false, false);
            
            $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
            $bom = $this->db->get()->result_array();

            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $kB => $vB) {
                        $strKey = $vB['type'].'__'.$vB['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }
            // print_arrays($bom);
            $total_paper_exchange = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    if ($flagGroup == true) {
                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                        $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                    } else {
                        $quantity_compensation = 0;
                    }

                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $vB['type'].'__'.$vB['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation/$count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //

                    $quantity = ceil(round($vB['quantity'], 4));
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;
                    $total_paper_exchange+= $paper_exchange;
                }
            }
            $quantityNew = $total_paper_exchange; 

            $this->db->from('tblorder_production_details_feedback');
            $this->db->join('tbl_productions_orders_details','tbl_productions_orders_details.id = tblorder_production_details_feedback.id_order_production_details');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.productions_orders_id',$productions_orders_id);
            $this->db->where('tbl_productions_orders_items.items_id',$items_id);
            $quantitycomment = $this->db->count_all_results();
            $row[3] = $aRow['items_code'];
            $row[4] = '<div class="text-left">'.($aRow['items_name']).'</div>';
            $row[5] = '<div class="text-center">'.($dtQuantityNew['landscape_print_size']).'</div>';

            $this->db->select('SUM(quantity) as quantity');
            $this->db->from('tbl_productions_orders_items');
            $this->db->where('productions_orders_id',$productions_orders_id);
            $this->db->where('items_id',$items_id);
            $this->db->where('object_item_type','business_plan');
            $quantityDp = $this->db->get()->row_array()['quantity'];

            $row[6] = '<div class="text-center">'.formatNumber($aRow['quantity'] - $quantityDp).'</div>';

            $row[7] = '<div class="text-center">'.formatNumber($aRow['quantity']).'</div>';

            $this->db->select('SUM(quantity_net) as quantity');
            $this->db->from('tbltransfer_warehouse');
            $this->db->join('tbltransfer_warehouse_detail','tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id');
            $this->db->where('tbltransfer_warehouse_detail.id_items',$items_id);
            $this->db->where('tbltransfer_warehouse.order_id_new != ',0);
            $this->db->where('tbltransfer_warehouse_detail.type','product');
            $quantityHold = $this->db->get()->row_array()['quantity'];

            $row[8] = '<div class="text-center">'.formatNumber($quantityHold).'</div>';



            $row[9] = '<div class="text-center">'.formatNumber($quantityNew,0).'</div>';

            $row[10] = '<div class="text-center">'.formatNumber($aRow['quantity_finished']).'</div>';
            $row[11] = '<div class="text-center">'.formatNumber($aRow['quantity_errors']).'</div>';
            $this->db->select('
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.id as id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_products.images as images,
                tbl_productions_orders_items.quantity,
                tbl_productions_orders_items.object_item_type as object_item_type,
                tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
                tbl_productions_orders_details.id as pod_id,
            ');
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id', 'left');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $productions_orders_items = $this->db->get()->result_array();
            $arrProcess = [];
            if (!empty($productions_orders_items)) {
                foreach ($productions_orders_items as $k => $v) {
                    $productions_orders_items_id = $v['id'];
                    $this->db->select('
                        tbl_productions_orders_items_stages.id as id,
                        tbl_productions_orders_items_stages.stage_id as stage_id,
                        '.$items_id.' as item_id,
                        tbl_productions_orders_items_stages.active as active,
                        tbl_productions_orders_items_stages.staff_active as staff_active,
                        tbl_productions_orders_items_stages.date_active as date_active,
                        tbl_stages.name as stage_name,
                        tbl_category_stages.is_in as is_in,
                        CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                        IF (tblstaff.profile_image IS NOT NULL, CONCAT("'.base_url('uploads/staff_profile_images/').'", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                        tbl_productions_orders_items_stages.final_stage as final_stage
                    ', false);
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                    $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages','left');
                    $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                    $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                    $process = $this->db->get()->result_array();
                    foreach ($process as $kk => $vv){
                        $checkKey = $vv['stage_id'].'__'.$vv['item_id'];
                        if (empty($arrProcess[$checkKey])){
                            $arrProcess[$checkKey] = $vv;
                        }
                    }
                }
            }
            $htmlProcess = '';
            $htmlProcessin = '';
            if (!empty($arrProcess)){
                $iKey = 1;
                foreach ($arrProcess as $k => $v){
                    if($v['is_in'] == 1){
                        $htmlProcessin .= $v['stage_name'].',<br>';
                    }
                    $htmlProcess .= '<span style="font-weight: bold">'.($iKey).'</span>.'.$v['stage_name'].'<br>';
                    $iKey ++;
                }
            }
            $row[12] = trim($htmlProcessin,',<br>');
            $row[13] = $htmlProcess;
            $row[14] = $aRow['note'];

            //items
            // $productions_orders_id
            $statusAuto = 1;
            $isNotProducedYet = 0;
            $isProduction = 0;
            $isFinished = 0;
            $isCancel = 0;

            $strStatus = '';
            $status_orders = $aRow['status_orders'];
            if (!empty($status_orders)) {
                $strStatus = '<span class="label label-danger">'.lang('Kết thúc sản xuất').'</span>';
            } else if (!$isFinished) {
                $strStatus = '<span class="label label-success">'.lang('Hoàn thành').'</span>';
            } else if (!empty($isProduction)) {
                $strStatus = '<span class="label label-primary">'.lang('Đang sản xuất').'</span>';
            } else {
                $strStatus = '<span class="label label-warning">'.lang('Chưa sản xuất').'</span>';
            }
            $row[15] = '<div class="text-center">'.$strStatus.'</div>';
            $output['aaData'][] = $row;
            $total_quantity += $aRow['quantity'] - $quantityDp;
            $total_quantity_sx += $aRow['quantity'];
            $total_quantity_hold += $quantityHold;
            $total_quantity_new += $quantityNew;
            $total_quantity_finished+= $aRow['quantity_finished'];
            $total_quantity_errors+= $aRow['quantity_errors'];
        }
        $output['total_quantity'] = $total_quantity;
        $output['total_quantity_sx'] = $total_quantity_sx;
        $output['total_quantity_hold'] = $total_quantity_hold;
        $output['total_quantity_new'] = formatNumber($total_quantity_new,0);
        $output['total_quantity_finished'] = $total_quantity_finished;
        $output['total_quantity_errors'] = $total_quantity_errors;
        echo json_encode($output);
    }
}