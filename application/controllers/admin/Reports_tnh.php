<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_tnh extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();
        // if (!has_permission('reports', '', 'view')) {
        //     access_denied('reports');
        // }
        $this->ci = &get_instance();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');

        $this->perViewOrdersOfQuotes = has_permission('orders_of_quotes', '', 'view');
        $this->perViewDeliverySchedules = has_permission('delivery_schedules', '', 'view');
        $this->perViewSalesOfOrder = has_permission('sales_of_order', '', 'view');
        $this->perViewNearestSellingPrice = has_permission('nearest_selling_price', '', 'view');
        $this->perViewReturnedGoods = has_permission('returned_goods', '', 'view');
        $this->perViewOrderStatus = has_permission('order_status', '', 'view');
        $this->perViewSalesAnalysis = has_permission('sales_analysis', '', 'view');
        $this->perViewSellingDiary = has_permission('selling_diary', '', 'view');

        $this->perViewMaterialNorms = has_permission('material_norms', '', 'view');
        $this->perViewUsageMaterial = has_permission('usage_material', '', 'view');
        $this->perViewProductionDetailed = has_permission('production_detailed', '', 'view');
        $this->perViewSituationOrderExecution = has_permission('situation_order_execution', '', 'view');
        $this->perViewStatusProduction = has_permission('status_production', '', 'view');
        $this->perViewUseMlAcProductionOrders = has_permission('use_ml_ac_production_orders', '', 'view');
        $this->perViewGeneralProduction = has_permission('general_production', '', 'view');
        $this->perViewProductionScheduleByOrder = has_permission('production_schedule_by_order', '', 'view');
        $this->perViewExpensesIncome = has_permission('expenses_vs_income', '', 'view');

    }

    public function getMaterialProductionOrders()
    {
        if (!$this->perViewUseMlAcProductionOrders) {
            accessDenied($js = true);
        }

        $productions_orders = $this->input->post('productions_orders');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $purchaseInternal = "(
            SELECT SUM(tbl_purchase_internal_items.quantity)
            FROM tbl_productions_orders_details
            INNER JOIN tbl_purchase_internal ON tbl_purchase_internal.pod_id = tbl_productions_orders_details.id
            INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
            WHERE tbl_purchase_internal.status = 'approved' AND tbl_purchase_internal_items.type_item = mt.type_item AND tbl_purchase_internal_items.item_id = mt.item_id
        )";

        $material = "(
            SELECT
                material.productions_orders_id as productions_orders_id,
                material.type_item as type_item,
                material.item_id as item_id,
                material.item_code as item_code,
                material.item_name as item_name,
                material.unit_id as unit_id,
                SUM(material.quantity_quota) as quantity_quota,
                SUM(material.quantity_exported) as quantity_exported
            FROM (
                SELECT
                    tbl_productions_orders_items_sub.productions_orders_id as productions_orders_id,
                    tbl_productions_orders_items_sub.type as type_item,
                    tbl_productions_orders_items_sub.item_id as item_id,
                    tbl_productions_orders_items_sub.item_code as item_code,
                    tbl_productions_orders_items_sub.item_name as item_name,
                    tbl_productions_orders_items_sub.unit_parent_id as unit_id,
                    tbl_productions_orders_items_sub.quantity_primary as quantity_quota,
                    0 as quantity_exported
                FROM tbl_productions_orders_items_sub
                WHERE (tbl_productions_orders_items_sub.type = 'materials' OR  tbl_productions_orders_items_sub.type = 'semi_products_outside')
                UNION ALL
                SELECT
                    tbl_productions_orders_details.productions_orders_id as productions_orders_id,
                    tbl_suggest_exporting_items.type_item as type_item,
                    tbl_suggest_exporting_items.item_id as item_id,
                    tbl_suggest_exporting_items.item_code as item_code,
                    tbl_suggest_exporting_items.item_name as item_name,
                    tbl_suggest_exporting_items.unit_parent_id as unit_id,
                    0 as quantity_quota,
                    tbl_suggest_exporting_items.quantity_exchange as quantity_exported
                FROM tbl_productions_orders_details
                INNER JOIN tbl_suggest_exporting ON tbl_suggest_exporting.productions_orders_details_id = tbl_productions_orders_details.id
                INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                WHERE tbl_suggest_exporting.warehouseman_id != 0
            ) as material
            GROUP BY material.productions_orders_id, material.type_item, material.item_id
        ) as mt";
        // AND tbl_suggest_exporting.type != 1

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR ', ') reference_no_orders
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR ', ') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";

        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
            }
        }

        $this->datatables->select("
            tbl_productions_orders.id as id,
            CONCAT(COALESCE(tb_orders.reference_no_orders, ''), ', ', COALESCE(tb_business_plan.reference_no_business_plan, '')) as reference_orders,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders.date as date,
            mt.item_code as item_code,
            mt.item_name as item_name,
            tblunits.unit as unit_name,
            mt.quantity_quota as quantity_quota,
            mt.quantity_exported as quantity_exported,
            concat(round(( COALESCE(mt.quantity_exported, 0)/COALESCE(mt.quantity_quota, 0) * 100 ), 2), '%') as percent,
            (COALESCE(mt.quantity_quota, 0) - COALESCE(mt.quantity_exported, 0)) as quantity_end,
            (COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0)) as quantity_used,
            (COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0) - COALESCE(mt.quantity_exported, 0)) as missing,
        ", FALSE)
        ->from('tbl_productions_orders')
        ->join($material, 'mt.productions_orders_id = tbl_productions_orders.id', 'inner')
        ->join($tbProductionsPlanOrdersByOrders, 'tb_orders.productions_order_id = tbl_productions_orders.id', 'left')
        ->join($tbProductionsPlanOrdersByBusinessPlan, 'tb_business_plan.productions_order_id = tbl_productions_orders.id', 'left')
        ->join('tblunits', 'tblunits.unitid = mt.unit_id', 'left');

        if (!empty($orders_search)) {
            $orders_search = $this->db->escape($orders_search);
            $this->datatables->where("EXISTS (
                SELECT tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.productions_plan_id = $orders_search AND tbl_productions_plan_orders.object_type = 'orders'
            )");
        }

        if (!empty($business_plan_search)) {
            $business_plan_search = $this->db->escape($business_plan_search);
            $this->datatables->where("EXISTS (
                SELECT tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.productions_plan_id = $business_plan_search AND tbl_productions_plan_orders.object_type = 'business_plan'
            )");
        }

        if (!empty($productions_orders)) {
            $productions_orders = str_replace(',', "','", $productions_orders);
            $this->datatables->where("tbl_productions_orders.id IN ('".$productions_orders."')");
        }

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        $custom[] = ['index' => 8, 'select' => 'percent'];
        $custom[] = ['index' => 10, 'select' => 'quantity_used'];
        $custom_select[10] = '(COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0))';
        $custom[] = ['index' => 11, 'select' => 'missing'];
        $custom_select[11] = "(COALESCE(mt.quantity_exported, 0) - COALESCE($purchaseInternal, 0) - COALESCE(mt.quantity_exported, 0))";
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }
        echo json_encode($data);
    }

    public function getProcessOrders() {
        $isAdmin = is_admin();

        $tbStatus = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                MAX(tbl_stages.name) as stage_name,
                MAX(tbl_stages.id) as stage_id,
                MAX(tbl_productions_orders_items_stages.final_stage) as final_stage,
                MAX(tbl_productions_orders_items_stages.number) as number
            FROM tbl_productions_orders_items_stages
            INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
            WHERE tbl_productions_orders_items_stages.active = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
            ORDER BY tbl_productions_orders_items_stages.active DESC, MAX(tbl_productions_orders_items_stages.number) DESC
        ) tb_status";

        $tbStatus = "(
            SELECT
                tb_cs.productions_orders_items_id as productions_orders_items_id,
                tb_cs.final_stage as final_stage,
                tb_cs.number as number,
                (tbl_stages.name) as stage_name,
                (tbl_stages.id) as stage_id
            FROM (
                SELECT
                    tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                    MAX(tbl_productions_orders_items_stages.id) as id,
                    MAX(tbl_productions_orders_items_stages.final_stage) as final_stage,
                    MAX(tbl_productions_orders_items_stages.number) as number
                FROM tbl_productions_orders_items_stages
                WHERE tbl_productions_orders_items_stages.active = 1
                GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
                ORDER BY tbl_productions_orders_items_stages.active DESC, MAX(tbl_productions_orders_items_stages.number) DESC
            ) tb_cs
            INNER JOIN tbl_productions_orders_items_stages ON tb_cs.id = tbl_productions_orders_items_stages.id
            INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
        ) tb_status";

        $tbOrders = "(
            SELECT
                tbl_orders.id as id,
                tbl_orders.reference_no as reference_no,
                tblclients.company as company,
                tbl_orders.customer_id as customer_id,
                tbl_orders.note as note,
                tbl_type_orders.name as name_type_orders,
                tbl_type_orders.color as color,
                tbl_orders.is_cancel as is_cancel,
                tbl_orders.date as date
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            LEFT JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders
        ) tb_orders";

        $tbOrdersItemsTranfer = "(
            SELECT
                tbltransfer_warehouse_detail.order_id_item as order_id_item,
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.order_id_item != 0
            GROUP BY tbltransfer_warehouse_detail.order_id_item
        ) tb_orders_items_tranfer";

        $tbOrdersItemsDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) as quantity
            FROM tbl_delivery_items
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_orders_items_delivery";

        $tbOrdersItemsShipping = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                GROUP_CONCAT(tbl_order_item_shippings.date_shipping) as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_orders_items_shipping";

        $tbOrdersItems = "(
            SELECT
                tbl_order_items.id as id,
                tbl_orders.id as order_id,
                tbl_order_items.item_id as item_id,
                tb_orders_items_shipping.date_shipping as date_shipping,
                tbl_order_items.total_quantity_item as quantity,
                tb_orders_items_delivery.quantity as quantity_delivery,
                tb_orders_items_tranfer.quantity as quantity_hold
            FROM tbl_order_items
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_order_items.order_id
            LEFT JOIN $tbOrdersItemsTranfer ON tb_orders_items_tranfer.order_id_item = tbl_order_items.id
            LEFT JOIN $tbOrdersItemsDelivery ON tb_orders_items_delivery.order_item_id = tbl_order_items.id
            LEFT JOIN $tbOrdersItemsShipping ON tb_orders_items_shipping.order_item_id = tbl_order_items.id
        ) tb_orders_items";

        $tbOrdersItemsShippingNew = "(
            SELECT
                tbl_orders_ship.order_item_id as order_item_id,
                GROUP_CONCAT(CONCAT(tbl_orders_ship.date,'__',tbl_orders_ship.quantity)) as date_shipping
            FROM tbl_orders_ship
            GROUP BY tbl_orders_ship.order_item_id
        ) tb_orders_items_shipping_new";


        $tb_production_order_tranfer = "(
            SELECT
                tbl_tranfer_business_item.order_item_id as order_item_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity,
                GROUP_CONCAT(DISTINCT tbl_productions_orders.reference_no SEPARATOR '<br>') as reference_no_production_order,
                GROUP_CONCAT(DISTINCT tbl_productions_orders.id SEPARATOR '__') as id_reference_no_production_order
            FROM tbl_tranfer_business_item
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_tranfer_business_item.business_plan_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'
            JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id 
            GROUP BY tbl_tranfer_business_item.order_item_id
        ) tb_production_order_tranfer";


        $aColumns = [
            'tbl_order_items.id as id',
            'tb_orders.date as date',
            'tb_orders.company as company',
            '"0" as numbers',
            'tbl_productions_orders.reference_no as reference_no_order',
            'tb_production_order_tranfer.reference_no_production_order as reference_no_production_order',
            'tb_orders.name_type_orders as name_type_orders',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tbl_order_items.total_quantity_item as sldat',
            'COALESCE(tb_orders_items_tranfer.quantity,0) as slgiu',
            'tbl_productions_orders_items.quantity as slsanxuat',
            'COALESCE(tbl_productions_orders_details.quantity_warehoused,0) as quantity_finished',
            'COALESCE(tb_orders_items_delivery.quantity,0) as sldagiao',
            '(tbl_order_items.quantity_loss + tbl_order_items.sample_quantity) as slloss',
            '(tbl_order_items.total_quantity_item - (COALESCE(tb_orders_items_delivery.quantity,0) + tbl_order_items.quantity_loss + tbl_order_items.sample_quantity)) as slconlai',
            '"" as status_new',
            'tb_orders_items_shipping.date_shipping as ngdk',
            '"" as date_delivery',
            'tb_orders_items_shipping_new.date_shipping as detail_date_delivery',
            '"" as quantity_delivery',
            'tb_status.stage_name as status',
            'tb_orders.note as note_orders',
            'tbl_order_items.note_item as note_item',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_order_items';
        $where        = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN '.$tbOrders.' ON tb_orders.id = tbl_order_items.order_id',
            'LEFT JOIN '.$tbOrdersItemsTranfer.' ON tb_orders_items_tranfer.order_id_item = tbl_order_items.id',
            'LEFT JOIN '.$tbOrdersItemsDelivery.' ON tb_orders_items_delivery.order_item_id = tbl_order_items.id',
            'LEFT JOIN '.$tbOrdersItemsShipping.' ON tb_orders_items_shipping.order_item_id = tbl_order_items.id',
            'LEFT JOIN '.$tbOrdersItemsShippingNew.' ON tb_orders_items_shipping_new.order_item_id = tbl_order_items.id',
            'LEFT JOIN '.$tb_production_order_tranfer.' ON tb_production_order_tranfer.order_item_id = tbl_order_items.id',
            'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.object_id  = tb_orders.id AND tbl_productions_orders_details.object_type = "orders"',
            'LEFT JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id  = tbl_order_items.id AND tbl_productions_orders_items.object_item_type = "orders"',
            'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id',
            'LEFT JOIN '.$tbStatus.' ON tb_status.productions_orders_items_id = tbl_productions_orders_items.id',
            'LEFT JOIN tblbranch ON tbl_productions_orders.location_id  = tblbranch.id',
        ];

        $status_search = $this->input->post('status_search');

        if (!empty($status_search)){
            if ($status_search == 1){
                array_push($where, 'AND (COALESCE(tb_orders_items_tranfer.quantity,0) = 0 AND COALESCE(tbl_productions_orders_items.quantity,0) = 0 
                    AND COALESCE(tbl_productions_orders_details.quantity_warehoused,0) = 0 AND COALESCE(tb_orders_items_delivery.quantity,0) = 0)
                ');
            } elseif ($status_search == 2){
                array_push($where, 'AND COALESCE(tbl_productions_orders_items.quantity,0) > 0 AND COALESCE(tbl_productions_orders_details.quantity_warehoused,0) < COALESCE(tbl_productions_orders_items.quantity,0)
                    AND COALESCE(tb_orders_items_delivery.quantity,0) = 0 AND COALESCE(tb_orders_items_tranfer.quantity,0) = 0 AND COALESCE(tbl_productions_orders_details.quantity_warehoused,0) = 0
                ');
            } elseif ($status_search == 3){
                array_push($where, 'AND (COALESCE(tb_orders_items_tranfer.quantity,0) > 0 OR COALESCE(tbl_productions_orders_details.quantity_warehoused,0) > 0)
                    AND (COALESCE(tb_orders_items_delivery.quantity,0) < tbl_order_items.quantity)
                ');
            } elseif ($status_search == 4){
                array_push($where, 'AND (tbl_order_items.quantity = COALESCE(tb_orders_items_delivery.quantity,0) AND COALESCE(tb_orders_items_delivery.quantity,0) > 0)
                ');
            }
        }

        $search_date_delivery = $this->input->post('search_date_delivery');
        if ($search_date_delivery){
            $search_date_delivery = explode('-',$search_date_delivery);
            $search_date_delivery_start = to_sql_date(trim($search_date_delivery[0]));
            $search_date_delivery_end = to_sql_date(trim($search_date_delivery[1]));
            array_push($where, "AND tb_orders_items_shipping.date_shipping >= '$search_date_delivery_start'");
            array_push($where, "AND tb_orders_items_shipping.date_shipping <= '$search_date_delivery_end'");
        }

        $search_date_delivery_new = $this->input->post('search_date_delivery_new');
        if ($search_date_delivery_new){
            $search_date_delivery_new = explode('-',$search_date_delivery_new);
            $search_date_delivery_new_start = to_sql_date(trim($search_date_delivery_new[0]));
            $search_date_delivery_new_end = to_sql_date(trim($search_date_delivery_new[1]));
            array_push($where, "AND EXISTS(
                SELECT 1
                FROM tbl_delivery_items
                JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                WHERE tbl_delivery_items.order_item_id = tbl_order_items.id
                AND DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d') >= '$search_date_delivery_new_start'
            )");
            array_push($where, "AND EXISTS(
                SELECT 1
                FROM tbl_delivery_items
                JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                WHERE tbl_delivery_items.order_item_id = tbl_order_items.id
                AND DATE_FORMAT(tbl_deliveries.date, '%Y-%m-%d') <= '$search_date_delivery_new_end'
            )");
        }

        $customer_search = $this->input->post('customer_search');
        if (!empty($customer_search)) {
            array_push($where, 'AND tb_orders.customer_id = '.$customer_search.'');
        }


        $stage_search = $this->input->post('stage_search');
        if (!empty($stage_search)) {
            $stage_search = implode(',',$stage_search);
            array_push($where, 'AND tb_status.stage_id IN ('.$stage_search.')');
        }


        $items_search = $this->input->post('items_search');
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            array_push($where, 'AND tbl_order_items.item_id = "'.$item_id.'"');
        }

        $start_date_search = $this->input->post('start_date_search');
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tb_orders.date >= '$start_date_search'");
        }

        $end_date_search = $this->input->post('end_date_search');
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tb_orders.date <= '$end_date_search'");
        }

        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
                array_push($where, "AND tb_orders.id = '$orders_search'");
            }
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_production_order_tranfer.reference_no_production_order as reference_no_production_order',
            'tb_production_order_tranfer.id_reference_no_production_order as id_reference_no_production_order',
            'tb_production_order_tranfer.quantity as quantity_chuyen',
            'tbl_productions_orders.id as production_order_id',
            'tb_orders.color as color_type_orders',
            'tblbranch.name as branch_name',
            'tbl_productions_orders_details.object_type as object_type',
            'tb_orders.reference_no as reference_no_orders',
            'tb_orders.id as order_id',
            'tbl_productions_orders_items.quantity as quantity',
            'tb_status.final_stage as final_stage',
            'tb_status.number as number',
            'tbl_order_items.quantity as quantity_order',
            'tbl_productions_orders_items.production_plan_item_id',
            'tbl_productions_orders_items.id as poi_id',
            'tb_orders.is_cancel as is_cancel'
        ], '', []);
        
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $totalQuantityFinished = 0;
        $totalCost = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $poi_id = $aRow['poi_id'];
            $maxStages = "(
                SELECT
                    MAX(tbl_productions_orders_items_stages.number) as number
                FROM tbl_productions_orders_items_stages
                INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                WHERE tbl_productions_orders_items_stages.productions_orders_items_id = '$poi_id'
            )";
            $max = $this->db->query($maxStages)->row_array()['number'];
            $number = $aRow['number'];
            $percent = 0;
            if ($max > 0) {
                $percent = $number/$max *100;
            }
            $txtReferenceObject = '';
            if ($aRow['object_type'] == "orders") {
                $txtReferenceObject = '<div class="bold">'.$aRow['reference_no_orders'].' - '.$aRow['company'].'</div>';
            } else if ($aRow['object_type'] == "business_plan") {
                $txtReferenceObject = '<div class="bold">'.$aRow['reference_no_business_plan'].'</div>';
            }

            $productions_orders_details_id = $aRow['id'];
            $row[0] = $productions_orders_details_id;

            $reference_no_orders = $aRow['reference_no_orders'];
            $company = $aRow['company'];

            $txtRefereceOrders = $reference_no_orders;

            $row[1] = '<div class="text-left" style="width: 100px">'._dhau($aRow['date']).'</div>';
            $row[2] = '<div class="text-left" style="width: 80px">'.$txtRefereceOrders.'</div>';
            $row[3] = '<div class="text-left">'.$company.'</div>';
            $reference_no_production_order = '';
            if (!empty($aRow['reference_no_production_order'])){
                // $reference_no_production_order = $aRow['reference_no_production_order'];
            }
            $row[4] = $aRow['reference_no_order'].'<br>'.$reference_no_production_order;
            $row[5] = $aRow['reference_no_production_order'];
            $color_type_orders = $aRow['color_type_orders'];
            $row[6] = $aRow['name_type_orders'];
            $row[7] = $aRow['item_code'];
            $row[8] = $aRow['item_name'];
            $row[9] = $aRow['sldat'];
            $row[10] = $aRow['slgiu'];
            $row[11] = $aRow['slsanxuat'];
            $row[12] = $aRow['quantity_finished'];
            $row[13] = $aRow['quantity_chuyen'];
            $row[14] = $aRow['sldagiao'];
            $row[15] = $aRow['slloss'];
            $row[16] = $aRow['slconlai'];
            $status_new = '';
            if ($aRow['sldagiao'] == $aRow['quantity_order'] && $aRow['sldagiao'] > 0){
                $status_new = '<div class="label label-success">Hoàn thành</div>';
            } elseif($aRow['slgiu'] == 0 && $aRow['slsanxuat'] == 0 && $aRow['quantity_finished'] == 0 && $aRow['sldagiao'] == 0) {
                $status_new = '<div class="label label-primary">Chờ sản xuất</div>';
            } else {
                if ($aRow['slsanxuat'] > 0 && $aRow['quantity_finished'] < $aRow['slsanxuat'] && $aRow['slgiu'] == 0 && $aRow['sldagiao'] == 0 && $aRow['quantity_finished'] == 0){
                    $status_new = '<div class="label label-danger">Đang sản xuất</div>';
                } elseif(($aRow['slgiu'] > 0 || $aRow['quantity_finished'] > 0 ) && ($aRow['sldagiao'] < $aRow['quantity_order'])){
                    $status_new = '<div class="label label-warning">Chờ giao hàng</div>';
                }
            }
            $row[17] = $status_new;
            $strStatusNewLSX = '';
            $production_order_id = $aRow['production_order_id'];
            if (!empty($production_order_id)){
                $isProduction = 0;
                $isFinished = 0;
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
                        tbl_productions_orders_details.price_costing as price_costing,
                        tbl_productions_orders_items.versions_stage as versions_stage
                    ');
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id', 'left');
                $this->db->where('tbl_productions_orders_items.productions_orders_id', $production_order_id);
                $productions_orders_items = $this->db->get()->result_array();
                if (!empty($productions_orders_items)) {
                    foreach ($productions_orders_items as $k => $v) {
                        $productions_orders_items_id = $v['id'];

                        //process
                        $this->db->select('
                                tbl_productions_orders_items_stages.id as id,
                                tbl_productions_orders_items_stages.active as active,
                                tbl_productions_orders_items_stages.staff_active as staff_active,
                                tbl_productions_orders_items_stages.date_active as date_active,
                                tbl_stages.name as stage_name,
                                CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                                IF (tblstaff.profile_image IS NOT NULL, CONCAT("'.base_url('uploads/staff_profile_images/').'", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                                tbl_productions_orders_items_stages.final_stage as final_stage
                            ', false);
                        $this->db->from('tbl_productions_orders_items_stages');
                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                        $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                        $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                        $process = $this->db->get()->result_array();
                        $isActive = 0;
                        if (!empty($process)) {
                            foreach ($process as $kkk => $vvv) {
                                if (!empty($vvv['active'])) {
                                    $isActive = $vvv['active'];
                                }
                                if ($kkk == 1 && empty($vvv['active'])) {
                                    $isNotProducedYet = 1;
                                }

                                if (!empty($vvv['active']) && empty($vvv['pois_id'])) {
                                    $isProduction = 1;
                                }

                                if ($vvv['final_stage'] && empty($vvv['active'])) {
                                    $isFinished = 1;
                                }
                            }
                        }
                    }
                }
                $strStatus = '';
                $dtPro = get_table_where('tbl_productions_orders',['id' => $production_order_id],'','row_array');
                $status_orders = $dtPro['status_orders'];
                if (!empty($status_orders)) {
                    if($status_orders == 1) {
                        $strStatus = '<span class="label label-danger">' . lang('Kết thúc sản xuất') . '</span>';
                    }
                    if($status_orders == 2) {
                        $strStatus = '<span class="label label-danger">' . lang('Đang tạm dừng sản xuất') . '</span>';
                    }
                } else if (!$isFinished) {
                    $strStatus = '<span class="label label-success">'.lang('Hoàn thành').'</span>';
                } else if (!empty($isProduction)) {
                    $strStatus = '<span class="label label-primary">'.lang('Đang sản xuất').'</span>';

                } else {
                    $strStatus = '<span class="label label-warning">'.lang('Chưa sản xuất').'</span>';
                }
                $strStatusNewLSX .= $strStatus;
            }
            $row[18] = $strStatusNewLSX;
            $strStatusNew = '';
            $id_reference_no_production_order = $aRow['id_reference_no_production_order'];
            if (!empty($id_reference_no_production_order)){
                $id_reference_no_production_order = explode('__',$id_reference_no_production_order);
                foreach ($id_reference_no_production_order as $kk => $vv){
                    $isProduction = 0;
                    $isFinished = 0;
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
                        tbl_productions_orders_details.price_costing as price_costing,
                        tbl_productions_orders_items.versions_stage as versions_stage
                    ');
                    $this->db->from('tbl_productions_orders_items');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                    $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id', 'left');
                    $this->db->where('tbl_productions_orders_items.productions_orders_id', $vv);
                    $productions_orders_items = $this->db->get()->result_array();
                    if (!empty($productions_orders_items)) {
                        foreach ($productions_orders_items as $k => $v) {
                            $productions_orders_items_id = $v['id'];

                            //process
                            $this->db->select('
                                tbl_productions_orders_items_stages.id as id,
                                tbl_productions_orders_items_stages.active as active,
                                tbl_productions_orders_items_stages.staff_active as staff_active,
                                tbl_productions_orders_items_stages.date_active as date_active,
                                tbl_stages.name as stage_name,
                                CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                                IF (tblstaff.profile_image IS NOT NULL, CONCAT("'.base_url('uploads/staff_profile_images/').'", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                                tbl_productions_orders_items_stages.final_stage as final_stage
                            ', false);
                            $this->db->from('tbl_productions_orders_items_stages');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                            $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                            $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                            $process = $this->db->get()->result_array();
                            $isActive = 0;
                            if (!empty($process)) {
                                foreach ($process as $kkk => $vvv) {
                                    if (!empty($vvv['active'])) {
                                        $isActive = $vvv['active'];
                                    }
                                    if ($kkk == 1 && empty($vvv['active'])) {
                                        $isNotProducedYet = 1;
                                    }

                                    if (!empty($vvv['active']) && empty($vvv['pois_id'])) {
                                        $isProduction = 1;
                                    }

                                    if ($vvv['final_stage'] && empty($vvv['active'])) {
                                        $isFinished = 1;
                                    }
                                }
                            }
                        }
                    }
                    $strStatus = '';
                    $dtPro = get_table_where('tbl_productions_orders',['id' => $vv],'','row_array');
                    $status_orders = $dtPro['status_orders'];
                    if (!empty($status_orders)) {
                        if($status_orders == 1) {
                            $strStatus = '<span class="label label-danger">' . lang('Kết thúc sản xuất') . '</span>';
                        }
                        if($status_orders == 2) {
                            $strStatus = '<span class="label label-danger">' . lang('Đang tạm dừng sản xuất') . '</span>';
                        }
                    } else if (!$isFinished) {
                        $strStatus = '<span class="label label-success">'.lang('Hoàn thành').'</span>';
                    } else if (!empty($isProduction)) {
                        $strStatus = '<span class="label label-primary">'.lang('Đang sản xuất').'</span>';

                    } else {
                        $strStatus = '<span class="label label-warning">'.lang('Chưa sản xuất').'</span>';
                    }
                    $strStatusNew .= $strStatus;
                }
            }
            $row[19] = $strStatusNew;
            $row[20]= '<div class="text-left">'._dhau($aRow['ngdk']).'</div>';
            $detail_date_delivery = explode(',',$aRow['detail_date_delivery']);
            $htmlDetailDate = '';
            if (!empty($detail_date_delivery)){
                foreach ($detail_date_delivery as $k => $v){
                    $v = explode('__',$v);
                    if (!empty($v[0])) {
                        $htmlDetailDate .= '<div>' . _dhau($v[0]) . ' - <span>' . formatNumber($v[1]) . '</span></div>';
                    }
                }
            }
            $row[21]= '<div class="text-left">'.$htmlDetailDate.'</div>';
            $this->db->select('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d") as date,SUM(tbl_delivery_items.quantity) as quantity');
            $this->db->from('tbl_delivery_items');
            $this->db->join('tbl_deliveries','tbl_deliveries.id = tbl_delivery_items.delivery_id');
            $this->db->where('order_item_id',$aRow['production_plan_item_id']);
            $this->db->group_by('DATE_FORMAT(tbl_deliveries.date, "%Y-%m-%d")');
            $deliveryItems = $this->db->get()->result_array();
            $htmlDelivery = '';
            $htmlQuantityDelivery = '';
            if (!empty($deliveryItems)){
                foreach ($deliveryItems as $kk => $vv){
                    // $htmlDelivery .= '<div>'._dhau($vv['date']).' - <span>'.formatNumber($vv['quantity']).'</span></div>';
                    $htmlDelivery .= '<div>'._dhau($vv['date']).'</span></div>';
                    $htmlQuantityDelivery .= '<div><span>'.formatNumber($vv['quantity']).'</span></div>';
                }
            }
            $row[22]= '<div class="text-left">'.$htmlDelivery.'</div>';
            $row[23] = '<div class="text-left">'.$htmlQuantityDelivery.'</div>';
            $final_stage = $aRow['final_stage'];
            $divFinised = '';
            if ($final_stage) {
                // $divFinised = '<div class="panel-finished" style="margin: auto;">
                //     <div class="">'.lang('tnh_finished_production').'</div>
                // </div>';
            }

            $row[24] = !empty($aRow['status']) ? '
                <div class="text-left" style="width: 120px; position: relative;">
                    <span class="dot-cs"></span>
                    <div style="margin-left: 10px;">'.$aRow['status'].'</div>
                    '.$divFinised.'
                </div>' : '<div class="text-danger text-left italic tag-not-stage" style="width: 120px;">'.lang('Chưa thực hiện sản xuất').'</div>';
            $row[25] = !empty($aRow['note_orders']) ? $aRow['note_orders'] : " ";
            $row[26] = !empty($aRow['note_item']) ? $aRow['note_item'] : " ";

            $is_cancel = $aRow['is_cancel'];
            $row[27] = $is_cancel > 0 ? '<div class="text-center"><span class="label label-danger">Hủy</span></div>' : '';

            $totalQuantity+= $aRow['quantity'];
            $totalQuantityFinished+= $aRow['quantity_finished'];
            $output['aaData'][] = $row;
        }

        $output['totalQuantity']= $totalQuantity;
        $output['totalQuantityFinished']= $totalQuantityFinished;
        $output['totalCost']= $totalCost;
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getReportOverProductions() {
        $isAdmin = is_admin();

        $tbStatus = "(
            SELECT
                tb_cs.productions_orders_items_id as productions_orders_items_id,
                tb_cs.final_stage as final_stage,
                tb_cs.number as number,
                (tbl_stages.name) as stage_name,
                (tbl_stages.id) as stage_id
            FROM (
                SELECT
                    tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                    MAX(tbl_productions_orders_items_stages.id) as id,
                    MAX(tbl_productions_orders_items_stages.final_stage) as final_stage,
                    MAX(tbl_productions_orders_items_stages.number) as number
                FROM tbl_productions_orders_items_stages
                WHERE tbl_productions_orders_items_stages.active = 1
                GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
                ORDER BY tbl_productions_orders_items_stages.active DESC, MAX(tbl_productions_orders_items_stages.number) DESC
            ) tb_cs
            INNER JOIN tbl_productions_orders_items_stages ON tb_cs.id = tbl_productions_orders_items_stages.id
            INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
        ) tb_status";

        $tbOrders = "(
            SELECT
                tbl_orders.id as object_id,
                tbl_orders.reference_no as reference_no,
                tblclients.company as company,
                tbl_orders.customer_id as customer_id,
                tbl_orders.note as note,
                tbl_orders.date as date
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbOrdersItemsTranfer = "(
            SELECT
                tbltransfer_warehouse_detail.order_id_item as order_id_item,
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.order_id_item != 0
            GROUP BY tbltransfer_warehouse_detail.order_id_item
        ) tb_orders_items_tranfer";

        $tbOrdersItemsDelivery = "(
            SELECT
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) as quantity
            FROM tbl_delivery_items
            JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            WHERE tbl_deliveries.warehouseman_id != 0
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_orders_items_delivery";

        $tbOrdersItemsShipping = "(
            SELECT
                tbl_order_item_shippings.order_item_id as order_item_id,
                GROUP_CONCAT(tbl_order_item_shippings.date_shipping) as date_shipping
            FROM tbl_order_item_shippings
            GROUP BY tbl_order_item_shippings.order_item_id
        ) tb_orders_items_shipping";

        $tbOrdersItems = "(
            SELECT
                tbl_order_items.id as object_item_id,
                tb_orders_items_shipping.date_shipping as date_shipping,
                tbl_order_items.total_quantity_item as quantity,
                tbl_order_items.quantity as quantity_new,
                tbl_order_items.quantity_loss as quantity_loss,
                tbl_order_items.sample_quantity as sample_quantity,
                tb_orders_items_delivery.quantity as quantity_delivery,
                tb_orders_items_tranfer.quantity as quantity_hold
            FROM tbl_order_items
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_order_items.order_id
            LEFT JOIN $tbOrdersItemsTranfer ON tb_orders_items_tranfer.order_id_item = tbl_order_items.id
            LEFT JOIN $tbOrdersItemsDelivery ON tb_orders_items_delivery.order_item_id = tbl_order_items.id
            LEFT JOIN $tbOrdersItemsShipping ON tb_orders_items_shipping.order_item_id = tbl_order_items.id
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders_items";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $tbQuantityWarehousePod = "(
            SELECT
                tbllocaltion_warehouses.pod_id as pod_id,
                SUM(tblwarehouse_items.product_quantity) as quantity
            FROM tblwarehouse_items
            JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tbllocaltion_warehouses.pod_id != 0 AND stage_id = 0 AND tbllocaltion_warehouses.warehouse != '".WAREHOUSES_ERRORS."'
            GROUP BY tbllocaltion_warehouses.pod_id
        ) tb_quantity_warehose_pod";

        $priceMaterial = "(
            SELECT
                tbl_suggest_exporting.productions_orders_details_id as pod_id,
                SUM(tbl_suggest_exporting.grand_total) as grand_total
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.reference_stock IS NOT NULL AND tbl_suggest_exporting.grand_total > 0 
            GROUP BY tbl_suggest_exporting.productions_orders_details_id
        ) tb_price_material";

        $payslips = "(
            SELECT
                tblother_payslips.vouchers_id as pod_id,
                SUM(tblother_payslips.total) as grand_total
            FROM tblother_payslips
            WHERE tblother_payslips.type_vouchers = 9
            GROUP BY tblother_payslips.vouchers_id
        ) tb_payslips";

        $purchaseInternal = "(
            SELECT
                tbl_purchase_internal.pod_id as pod_id,
                SUM(tbl_purchase_internal.grand_total) as grand_total
            FROM tbl_purchase_internal
            WHERE tbl_purchase_internal.grand_total > 0
            GROUP BY tbl_purchase_internal.pod_id
        ) tb_purchase_internal";

        $selectCost = "(COALESCE(tb_price_material.grand_total, 0) + COALESCE(tb_payslips.grand_total, 0) - COALESCE(tb_purchase_internal.grand_total, 0))";
        if (!$isAdmin) {
            $selectCost = 0;
        }

        $aColumns = [
            'tbl_productions_orders_details.id as id',
            'tb_orders.company as company',
            '"0" as numbers',
            'tbl_productions_orders.reference_no as reference_no_order',
            'tbl_productions_orders_details.reference_no as reference_no',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tb_orders_items.quantity as sldat',
            'tbl_productions_orders_details.quantity_warehoused as quantity_finished',
            '(tb_orders_items.quantity_delivery) as sldagiao',
            '(tb_orders_items.quantity_loss + tb_orders_items.sample_quantity) as slloss',
            'tb_quantity_warehose_pod.quantity as slconlai',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders_details';
        $where        = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id',
            'LEFT JOIN '.$tbStatus.' ON tb_status.productions_orders_items_id = tbl_productions_orders_items.id',
            'LEFT JOIN tblbranch ON tbl_productions_orders.location_id  = tblbranch.id',
            'LEFT JOIN '.$tbOrdersItems.' ON tb_orders_items.object_item_id  = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = "orders"',
            'LEFT JOIN '.$tbOrders.' ON tb_orders.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"',
            'LEFT JOIN '.$tbBusinessPlan.' ON tb_business_plan.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"',
            'LEFT JOIN '.$tbQuantityWarehousePod.' ON tb_quantity_warehose_pod.pod_id  = tbl_productions_orders_details.id',
        ];

        if ($isAdmin) {
        }

        array_push($where, 'AND tbl_productions_orders_details.object_type = "orders"');
        array_push($where, 'AND (tb_orders_items.quantity_new - tb_orders_items.quantity_delivery) = 0');
        array_push($where, 'AND (tb_quantity_warehose_pod.quantity - (tb_orders_items.quantity_loss + tb_orders_items.sample_quantity)) > 0');

        $customer_search = $this->input->post('customer_search');
        if (!empty($customer_search)) {
            array_push($where, 'AND tb_orders.customer_id = '.$customer_search.'');
        }

        $stage_search = $this->input->post('stage_search');
        if (!empty($stage_search)) {
            $stage_search = implode(',',$stage_search);
            array_push($where, 'AND tb_status.stage_id IN ('.$stage_search.')');
        }


        $items_search = $this->input->post('items_search');
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            array_push($where, 'AND tbl_productions_orders_items.items_id = "'.$item_id.'"');
        }

        $start_date_search = $this->input->post('start_date_search');
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_productions_orders_details.date_created >= '$start_date_search'");
        }

        $end_date_search = $this->input->post('end_date_search');
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tbl_productions_orders_details.date_created <= '$end_date_search'");
        }

        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
                array_push($where, "AND tb_orders.object_id = '$orders_search'");
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
                array_push($where, "AND tb_business_plan.object_id = '$business_plan_search'");
            }
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblbranch.name as branch_name',
            'tbl_productions_orders_details.object_type as object_type',
            'tb_orders.reference_no as reference_no_orders',
            'tb_orders.object_id as order_id',
            'tb_business_plan.object_id as business_plan_id',
            'tb_business_plan.reference_no as reference_no_business_plan',
            'tbl_productions_orders_items.quantity as quantity',
            'tb_status.final_stage as final_stage',
            'tb_status.number as number',
            'tbl_productions_orders_items.production_plan_item_id',
            'tbl_productions_orders_items.id as poi_id',
			'tbl_products.id as id_product'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $totalQuantityFinished = 0;
        $totalCost = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $poi_id = $aRow['poi_id'];
            $maxStages = "(
                SELECT
                    MAX(tbl_productions_orders_items_stages.number) as number
                FROM tbl_productions_orders_items_stages
                INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                WHERE tbl_productions_orders_items_stages.productions_orders_items_id = '$poi_id'
            )";
            $max = $this->db->query($maxStages)->row_array()['number'];
            $number = $aRow['number'];
            $percent = 0;
            if ($max > 0) {
                $percent = $number/$max *100;
            }
            $txtReferenceObject = '';
            if ($aRow['object_type'] == "orders") {
                $txtReferenceObject = '<div class="bold">'.$aRow['reference_no_orders'].' - '.$aRow['company'].'</div>';
            } else if ($aRow['object_type'] == "business_plan") {
                $txtReferenceObject = '<div class="bold">'.$aRow['reference_no_business_plan'].'</div>';
            }

            $productions_orders_details_id = $aRow['id'];
            $row[0] = $productions_orders_details_id;

            $reference_no_orders = $aRow['reference_no_orders'];
            $company = $aRow['company'];
            $reference_no_business_plan = $aRow['reference_no_business_plan'];

            $txtRefereceOrders = $reference_no_orders;
            if ($reference_no_business_plan) {
                $txtRefereceOrders = $reference_no_business_plan;
            }

            $row[1] = '<div class="text-left row_stranfer" data-product="'.$aRow['id_product'].'" data-quanliti="'.$aRow['slconlai'].'" data-pod="'.$aRow['poi_id'].'" data-type="products">'.$txtRefereceOrders.'</div>';
            $row[2] = '<div class="text-left">'.$company.'</div>';
            $row[3] = $aRow['reference_no_order'];
            $row[4] = $aRow['reference_no'];
            $row[5] = $aRow['item_code'];
            $row[6] = $aRow['item_name'];
            $row[7] = $aRow['sldat'];
            $row[8] = $aRow['quantity_finished'];
            $row[9] = $aRow['sldagiao'];
            $row[10] = $aRow['slloss'];
            $row[11] = $aRow['slconlai'];

            $totalQuantity+= $aRow['quantity'];
            $totalQuantityFinished+= $aRow['quantity_finished'];
            $output['aaData'][] = $row;
        }

        $output['totalQuantity']= $totalQuantity;
        $output['totalQuantityFinished']= $totalQuantityFinished;
        $output['totalCost']= $totalCost;
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getProductionsOrders() {
        $isAdmin = is_admin();
        if (!$this->perViewUseMlAcProductionOrders) {
            accessDenied($js = true);
        }

        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders,
                GROUP_CONCAT(tbl_orders.note SEPARATOR '</br>') as note
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

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            '"" as reference_orders',
            '"" as time',
            'tbl_productions_orders.note as note',
            'tb_orders.note as note_orders',
            '"" as items'
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $where        = [
        ];
        $filter = [];
        
        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_productions_orders.location_id',
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_productions_orders.created_by',
            'LEFT JOIN '.$tbProductionsPlanOrdersByOrders.' ON tb_orders.productions_order_id = tbl_productions_orders.id',
            'LEFT JOIN '.$tbProductionsPlanOrdersByBusinessPlan.' ON tb_business_plan.productions_order_id = tbl_productions_orders.id',
        ];

        $isFinished = "(
            SELECT tbl_productions_orders_items_stages.id
            FROM tbl_productions_orders_items_stages
            WHERE tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_items_stages.final_stage = 1 AND tbl_productions_orders_items_stages.active = 0
        )";

        array_push($where, "AND NOT exists ($isFinished)");

        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id = ".$this->db->escape($productions_orders_search));
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

        $start_date_search = $this->input->post('start_date_search');
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
        }

        $end_date_search = $this->input->post('end_date_search');
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblbranch.name as location_name',
            'tbl_productions_orders.status_details',
            'tbl_productions_orders.options1 as options1',
            'tbl_productions_orders.options2 as options2',
            'tb_orders.reference_no_orders as reference_no_orders',
            'tb_business_plan.reference_no_business_plan as reference_no_business_plan',
            'tbl_productions_orders.total_quantity as total_quantity',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
            'tbl_productions_orders.status_orders as status_orders'
        ], '', []);
        

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $productions_orders_id = $aRow['id'];
            $status_details = $aRow['status_details'];
            $row[0] = '<div class="text-center">'.$start.'</div>';
            $row[1] = _d($aRow['date']);

            $row[2] = '
                <div class="bold"><a target="_blank" href="'.base_url('admin/manufactures/detail_productions_orders/'.$productions_orders_id).'">'.$aRow['reference_no'].'</a></div>
                <div class="italic">'.$aRow['location_name'].'</div>
            ';

            $options1 = $aRow['options1'];
            $options2 = $aRow['options2'];
            $reference_no_orders = $aRow['reference_no_orders'];
            $reference_no_business_plan = $aRow['reference_no_business_plan'];
            $countReference = 0;
            $refereceNoOrdersBusiness = '';
            $countReferenceOrders = 0;
            $countReferenceBusiness = 0;
            $refereceNoOrders = '';
            $refereceNoBusiness = '';

            if (!empty($reference_no_orders)) {
                foreach (explode('|||', $reference_no_orders) as $key => $value) {
                    $refereceNoOrdersBusiness.= '<div>'.$value.'</div>';
                    $refereceNoOrders.= '<div>'.$value.'</div>';
                    $countReference++;
                    $countReferenceOrders++;
                }
            }

            if (!empty($reference_no_business_plan)) {
                foreach (explode('|||', $reference_no_business_plan) as $key => $value) {
                    $refereceNoOrdersBusiness.= '<div>'.$value.'</div>';
                    $refereceNoBusiness.= '<div>'.$value.'</div>';
                    $countReference++;
                    $countReferenceBusiness++;
                }
            }

            $labelOptions1 = '';
            if ($options1) {
                $labelOptions1 = '<div>'.$refereceNoOrders.'</div>';
            }
            $labelOptions2 = '';
            if ($options2) {
                $labelOptions2 = '<div class="'.(!empty($labelOptions1) ? 'mtop10' : '').'">'.$refereceNoBusiness.'</div>';
            }
           
            $row[3] = $labelOptions1.''.$labelOptions2;
           
            $row[4] = '';
            $row[5] = $aRow['note'];
            $row[6] = $aRow['note_orders'];

            //items
            // $productions_orders_id
            $dateEnd = '';
            $statusAuto = 1;
            $isNotProducedYet = 0;
            $isProduction = 0;
            $isFinished = 0;
            $isCancel = 0;
            $this->db->select('
                tbl_productions_orders_items.id as id,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_products.images as images,
                tbl_productions_orders_items.quantity,
                tbl_productions_orders_items.object_item_type as object_item_type,
                tbl_productions_orders_items.production_plan_item_id as production_plan_item_id
            ');
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $productions_orders_items = $this->db->get()->result_array();
            if (!empty($productions_orders_items)) {
                foreach ($productions_orders_items as $k => $v) {
                    $object_item_type = $v['object_item_type'];
                    $production_plan_item_id = $v['production_plan_item_id'];
                    $productions_orders_items_id = $v['id'];
                    $reference_no = '';
                    if ($object_item_type == "orders") {
                        $this->db->select('
                            tbl_orders.reference_no as reference_no,
                            tblclients.company as company
                        ');
                        $this->db->from('tbl_order_items');
                        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
                        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
                        $this->db->where('tbl_order_items.id', $production_plan_item_id);
                        $order = $this->db->get()->row_array();
                        if (!empty($order)) {
                            $reference_no = $order['reference_no'].' - '.$order['company'];
                        }
                    } else if ($object_item_type == "business_plan") {
                        $this->db->select('
                            tbl_business_plan.reference_no as reference_no,
                        ');
                        $this->db->from('tbl_business_plan_items');
                        $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_business_plan_items.business_plan_id');
                        $this->db->where('tbl_business_plan_items.id', $production_plan_item_id);
                        $business_plan = $this->db->get()->row_array();
                        if (!empty($business_plan)) {
                            $reference_no = $business_plan['reference_no'];
                        }
                    }
                    $productions_orders_items[$k]['reference_no'] = $reference_no;

                    //process
                    $this->db->select('
                        tbl_productions_orders_items_stages.id as id,
                        tbl_productions_orders_items_stages.active as active,
                        tbl_productions_orders_items_stages.staff_active as staff_active,
                        tbl_productions_orders_items_stages.date_active as date_active,
                        tbl_stages.name as stage_name,
                        CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name,
                        IF (tblstaff.profile_image IS NOT NULL, CONCAT("'.base_url('uploads/staff_profile_images/').'", tblstaff.staffid, "/small__", tblstaff.profile_image), null) as staff_image,
                        tbl_productions_orders_items_stages.final_stage as final_stage
                    ', false);
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                    $this->db->join('tblstaff', 'tblstaff.staffid = tbl_productions_orders_items_stages.staff_active', 'left');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                    $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                    $process = $this->db->get()->result_array();
                    $workflow = '';
                    $li = '';
                    $isActive = 0;
                    if (!empty($process)) {
                        foreach ($process as $kkk => $vvv) {
                            if (!empty($vvv['active'])) {
                                $isActive = $vvv['active'];
                            }
                            $li .= '<li ' . ($vvv['active'] ? 'class="active"' : '') . '>' . $vvv['stage_name'] .
                                (!empty($vvv['staff_active']) ? ('<p class="active_poin">' . ('Được ' . get_staff_full_name($vvv['staff_active']) . ($vvv['date_active'] ? ' hoàn thành vào lúc: ' . _dt($vvv['date_active']) : '')) . '</p>') : '')
                                . '</li>';

                            if ($kkk == 1 && empty($vvv['active'])) {
                                $isNotProducedYet = 1;
                            }

                            if (!empty($vvv['active'])) {
                                $isProduction = 1;
                            }

                            if ($vvv['final_stage'] && empty($vvv['active'])) {
                                $isFinished = 1;
                            }

                            if ($vvv['final_stage'] && $vvv['date_active'] > $dateEnd) {
                                $dateEnd = $vvv['date_active'];
                            }
                        }
                    }
                    $workflow .= '<div style="display: table; justify-content: center;">
                        <ul class="progressbar" style="display: flex;">
                        ' . $li . '
                        </ul>
                    </div>';

                    if (empty($isActive)) {
                        $workflow = '</br><div style="width: 250px;" class="text-danger text-left italic tag-not-stage">'.lang('tnh_chua_thuc_hien_san_xuat').'</div>';
                    }
                    $productions_orders_items[$k]['workflow'] = $workflow;
                }
            }

            if (!empty($dateEnd)) {
                $dateStart = new DateTime($aRow['date']);
                $dateEnd = new DateTime($dateEnd);
                $diff = $dateStart->diff($dateEnd);
                $h = $diff->h;
                $days = $diff->days;

                $row[4] = '<div class="text-center">'.$days.' Ngày '.$h.' giờ</div>';
            }


            $row[7] = $productions_orders_items;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function getProductionsOrdersDetail() {
        $isAdmin = is_admin();
        if (!$this->perViewUseMlAcProductionOrders) {
            accessDenied($js = true);
        }

        $tbStatus = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                MAX(tbl_stages.name) as stage_name,
                MAX(tbl_productions_orders_items_stages.final_stage) as final_stage,
                MAX(tbl_productions_orders_items_stages.number) as number,
                MAX(tbl_productions_orders_items_stages.date_active) as date_active
            FROM tbl_productions_orders_items_stages
            INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
            WHERE tbl_productions_orders_items_stages.active = 1 AND tbl_productions_orders_items_stages.final_stage = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
            ORDER BY tbl_productions_orders_items_stages.active DESC, MAX(tbl_productions_orders_items_stages.number) DESC
        ) tb_status";

        $tbOrders = "(
            SELECT
                tbl_orders.id as object_id,
                tbl_orders.reference_no as reference_no,
                tblclients.company as company,
                tbl_orders.note as note,
                tbl_orders.date as date
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $priceMaterial = "(
            SELECT
                tbl_suggest_exporting.productions_orders_details_id as pod_id,
                SUM(tbl_suggest_exporting.grand_total) as grand_total
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.reference_stock IS NOT NULL AND tbl_suggest_exporting.grand_total > 0 
            GROUP BY tbl_suggest_exporting.productions_orders_details_id
        ) tb_price_material";

        $payslips = "(
            SELECT
                tblother_payslips.vouchers_id as pod_id,
                SUM(tblother_payslips.total) as grand_total
            FROM tblother_payslips
            WHERE tblother_payslips.type_vouchers = 9
            GROUP BY tblother_payslips.vouchers_id
        ) tb_payslips";

        $purchaseInternal = "(
            SELECT
                tbl_purchase_internal.pod_id as pod_id,
                SUM(tbl_purchase_internal.grand_total) as grand_total
            FROM tbl_purchase_internal
            WHERE tbl_purchase_internal.grand_total > 0
            GROUP BY tbl_purchase_internal.pod_id
        ) tb_purchase_internal";

        $selectCost = "(COALESCE(tb_price_material.grand_total, 0) + COALESCE(tb_payslips.grand_total, 0) - COALESCE(tb_purchase_internal.grand_total, 0))";
        if (!$isAdmin) {
            $selectCost = 0;
        }

        $aColumns = [
            'tbl_productions_orders_details.id as id',
            '"0" as numbers',
            'tbl_productions_orders.reference_no as reference_no_order',
            'tbl_productions_orders_details.reference_no as reference_no',
            'CONCAT(COALESCE(tbl_products.images, ""), "||", tbl_products.code) as item_name',
            'tbl_productions_orders_details.quantity_warehoused as quantity_finished',
            'tb_status.stage_name as status',
            '"" as time',
            'tb_orders.note as note_orders',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders_details';
        $where        = [

        ];
        $filter = [];
        
        $join = [
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id',
            'INNER JOIN '.$tbStatus.' ON tb_status.productions_orders_items_id = tbl_productions_orders_items.id',
            'LEFT JOIN tblbranch ON tbl_productions_orders.location_id  = tblbranch.id',
            'LEFT JOIN '.$tbOrders.' ON tb_orders.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"',
            'LEFT JOIN '.$tbBusinessPlan.' ON tb_business_plan.object_id  = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"',
        ];

        if ($isAdmin) {
            // array_push($join, "LEFT JOIN $priceMaterial ON tb_price_material.pod_id = tbl_productions_orders_details.id");
            // array_push($join, "LEFT JOIN $payslips ON tb_payslips.pod_id = tbl_productions_orders_details.id");
            // array_push($join, "LEFT JOIN $purchaseInternal ON tb_purchase_internal.pod_id = tbl_productions_orders_details.id");
        }
      
        $items_search = $this->input->post('items_search');
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            array_push($where, 'AND tbl_productions_orders_items.items_id = "'.$item_id.'"');
        }

        $start_date_search = $this->input->post('start_date_search');
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_productions_orders_details.date_created >= '$start_date_search'");
        }

        $end_date_search = $this->input->post('end_date_search');
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tbl_productions_orders_details.date_created <= '$end_date_search'");
        }

        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
                array_push($where, "AND tb_orders.object_id = '$orders_search'");
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
                array_push($where, "AND tb_business_plan.object_id = '$business_plan_search'");
            }
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblbranch.name as branch_name',
            'tbl_productions_orders_details.object_type as object_type',
            'tb_orders.reference_no as reference_no_orders',
            'tb_orders.company as company',
            'tb_orders.object_id as order_id',
            'tb_business_plan.object_id as business_plan_id',
            'tb_business_plan.reference_no as reference_no_business_plan',
            'tbl_productions_orders_items.quantity as quantity',
            'tb_status.final_stage as final_stage',
            'tb_status.number as number',
            'tbl_productions_orders_items.id as poi_id',
            'tbl_productions_orders.date date_po',
            'tb_status.date_active as date_active'
        ], '', []);
        

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $totalQuantityFinished = 0;
        $totalCost = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $poi_id = $aRow['poi_id'];
            $maxStages = "(
                SELECT
                    MAX(tbl_productions_orders_items_stages.number) as number
                FROM tbl_productions_orders_items_stages
                INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                WHERE tbl_productions_orders_items_stages.productions_orders_items_id = '$poi_id'
            )";
            $max = $this->db->query($maxStages)->row_array()['number'];
            $number = $aRow['number'];
            $percent = 0;
            if ($max > 0) {
                $percent = $number/$max *100;
            }
            $txtReferenceObject = '';
            if ($aRow['object_type'] == "orders") {
                $txtReferenceObject = '<div class="bold">'.$aRow['reference_no_orders'].' - '.$aRow['company'].'</div>';
            } else if ($aRow['object_type'] == "business_plan") {
                $txtReferenceObject = '<div class="bold">'.$aRow['reference_no_business_plan'].'</div>';
            }

            $productions_orders_details_id = $aRow['id'];
            $row[0] = $productions_orders_details_id;

            $reference_no_orders = $aRow['reference_no_orders'];
            $company = $aRow['company'];
            $reference_no_business_plan = $aRow['reference_no_business_plan'];

            $txtRefereceOrders = $reference_no_orders.'<br>('.$company.')';
            if ($reference_no_business_plan) {
                $txtRefereceOrders = $reference_no_business_plan;
            }

            $row[1] = '<div class="text-left">'.$txtRefereceOrders.'</div>';
            $row[2] = $aRow['reference_no_order'];
            $row[3] = $aRow['reference_no'].'<div class="italic" style="color: #323a45;">'.$aRow['branch_name'].'</div>';
            $row[4] = $aRow['item_name'].'___'.$txtReferenceObject.'___'.$aRow['quantity'].'___'.$percent;
            $row[5] = $aRow['quantity_finished'];

            $final_stage = $aRow['final_stage'];
            $divFinised = '';
            if ($final_stage) {
                $divFinised = '<div class="panel-finished" style="margin: auto;">
                    <div class="">'.lang('tnh_finished_production').'</div>
                </div>';
            }
            $row[6] = !empty($aRow['status']) ? '
                <div class="text-left" style="width: 180px; position: relative;">
                    <span class="dot-cs"></span>
                    <div style="margin-left: 10px;">'.$aRow['status'].'</div>
                    '.$divFinised.'
                </div>' : '<div class="text-danger text-left italic tag-not-stage" style="width: 180px;">'.lang('Chưa thực hiện sản xuất').'</div>';

            $date_active = $aRow['date_active'];
            $date_po = $aRow['date_po'];
            $strTime = '';
            if (!empty($date_active)) {
                $dateStart = new DateTime($date_po);
                $dateEnd = new DateTime($date_active);
                $diff = $dateStart->diff($dateEnd);
                $h = $diff->h;
                $days = $diff->days;

                $strTime = '<div class="text-center">'.$days.' Ngày '.$h.' giờ</div>';
            }
            $row[7] = $strTime;
            $row[8] = $aRow['note_orders'];

            $totalQuantity+= $aRow['quantity'];
            $totalQuantityFinished+= $aRow['quantity_finished'];
            $output['aaData'][] = $row;
        }

        $output['totalQuantity']= $totalQuantity;
        $output['totalQuantityFinished']= $totalQuantityFinished;
        $output['totalCost']= $totalCost;
        echo json_encode($output);
    }

    public function getUsageMaterial()
    {
        if (!$this->perViewUsageMaterial) {
            accessDenied($js = true);
        }

        $materials_search = $this->input->post('materials_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $conditionSuggestExporting = "(
            SELECT COUNT(*)
            FROM tbl_suggest_exporting
            WHERE tbl_suggest_exporting.productions_orders_details_id = tbl_productions_orders_details.id AND tbl_suggest_exporting.status_stock IS NOT NULL
            LIMIT 1
        )";

        $tbOrders = "(
            SELECT
                tbl_orders.id as object_id,
                CONCAT(tbl_orders.reference_no, '(', tblclients.company, ')') as reference_no,
                tblclients.company as company,
                tbl_orders.note as note,
                tbl_orders.date as date
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $this->datatables->select("
            tbl_productions_orders_details.id as id,
            concat(coalesce(tb_orders.reference_no, ''), ',', coalesce(tb_business_plan.reference_no, '')) as reference_order,
            tb_orders.note as note_orders,
            tbl_productions_orders_details.reference_no as reference_no,
            tbl_productions_orders_details.date_created as date,
            '' as item_code,
            '' as item_name,
            '' as type_item,
            '' as unit_name,
            '' as quantity_export,
            '' as quantity_reenter,
            '' as quantity_used,
        ", FALSE)
        ->from('tbl_productions_orders_details')
        ->join($tbOrders, 'tb_orders.object_id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left')
        ->join($tbBusinessPlan, 'tb_business_plan.object_id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            $this->datatables->where('tbl_productions_orders_details.date_created >=', $start_date_search);
        } 
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            $this->datatables->where('tbl_productions_orders_details.date_created <=', $end_date_search);
        }

        if (!empty($materials_search)) {
            $arr_items = explode(',', $materials_search);
            if (!empty($arr_items)) {
                $query = [];
                foreach ($arr_items as $key => $value) {
                    $arrItem = explode('__', $value);
                    $item_id = $arrItem[0];
                    $item_type = $arrItem[1];
                    if ($item_type == "products") {
                        $item_type = "'semi_products','products'";
                    }


                    $query[]= "EXISTS (
                        SELECT tbl_suggest_exporting.id
                        FROM tbl_suggest_exporting
                        INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                        WHERE tbl_suggest_exporting_items.type_item IN (".$item_type.") AND tbl_suggest_exporting_items.item_id = $item_id AND tbl_suggest_exporting.productions_orders_details_id = tbl_productions_orders_details.id
                    )";
                }

                if (!empty($query)) {
                    $strQuery = implode(' OR ', $query);
                    $this->datatables->where("($strQuery)");
                }
            }
        }

        $this->datatables->where("$conditionSuggestExporting > 0");

        $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        $index = 0;
        foreach ($data->aaData as $key => $value) {
            $pod_id = $value[0];
            $numbers = ++$iDisplayStart;

            $data->aaData[$index][0] = $numbers;
            $data->aaData[$index][1] = $value[1];
            $data->aaData[$index][2] = $value[2];
            $data->aaData[$index][3] = $value[3];
            $data->aaData[$index][4] = $value[4];
            $data->aaData[$index][5] = $value[5];
            $data->aaData[$index][6] = $value[6];
            $data->aaData[$index][7] = $value[7];
            $data->aaData[$index][8] = $value[8];
            $data->aaData[$index][9] = $value[9];
            $data->aaData[$index][10] = $value[10];
            $data->aaData[$index][11] = $value[11];

            $reEnterQuantity = "(
                SELECT SUM(tbl_purchase_internal_items.quantity)
                FROM tbl_purchase_internal
                INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal_items.purchase_internal_id = tbl_purchase_internal.id
                WHERE tbl_purchase_internal.pod_id = '$pod_id' AND tbl_purchase_internal_items.type_item = tbl_suggest_exporting_items.type_item AND tbl_purchase_internal_items.item_id = tbl_suggest_exporting_items.item_id AND tbl_purchase_internal_items.unit_id = tbl_suggest_exporting_items.unit_parent_id
            )";

            $tableThuHoi = "(
                SELECT
                    tbl_purchase_internal_items.type_item as type_item,
                    tbl_purchase_internal_items.item_id as item_id,
                    SUM(tbl_purchase_internal_items.quantity) as quantity, 
                    SUM(tbl_purchase_internal_items.amount) as amount
                FROM tbl_purchase_internal
                INNER JOIN tbl_purchase_internal_items ON tbl_purchase_internal.id = tbl_purchase_internal_items.purchase_internal_id
                WHERE tbl_purchase_internal.pod_id = $pod_id
                GROUP BY tbl_purchase_internal_items.type_item, tbl_purchase_internal_items.item_id
            ) as tbthuhoi";

            $this->db->select("
                tbl_suggest_exporting_items.type_item,
                tbl_suggest_exporting_items.item_id,
                tbl_suggest_exporting_items.type_item as type_item,
                tbl_suggest_exporting_items.item_id as item_id,
                tbl_suggest_exporting_items.item_code as item_code,
                tbl_suggest_exporting_items.item_name as item_name,
                tblunits.unit as unit_name,
                SUM(tbl_suggest_exporting_items.quantity_exchange) as quantity_export,
                COALESCE(tbthuhoi.quantity, 0) as quantity_reenter
            ", false);
            $this->db->from('tbl_suggest_exporting');
            $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_exporting_items.unit_parent_id', 'left');
            $this->db->join($tableThuHoi, 'tbthuhoi.type_item = tbl_suggest_exporting_items.type_item AND tbthuhoi.item_id = tbl_suggest_exporting_items.item_id', 'left');
            $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
            $this->db->where('tbl_suggest_exporting.status_stock IS NOT NULL');

            $this->db->group_by('tbl_suggest_exporting_items.type_item, tbl_suggest_exporting_items.item_id');
            $items = $this->db->get()->result_array();
            if (!empty($items)) {
                foreach ($items as $k => $val) {
                    if ($k != 0) {
                        $index++;
                    }
                    $data->aaData[$index][0] = $numbers;
                    $data->aaData[$index][1] = $value[1];
                    $data->aaData[$index][2] = $value[2];
                    $data->aaData[$index][3] = $value[3];
                    $data->aaData[$index][4] = $value[4];
                    $data->aaData[$index][5] = $val['item_code'];
                    $data->aaData[$index][6] = $val['item_name'];
                    $data->aaData[$index][7] = $val['type_item'];
                    $data->aaData[$index][8] = $val['unit_name'];
                    $data->aaData[$index][9] = $val['quantity_export'];
                    $data->aaData[$index][10] = $val['quantity_reenter'];
                    $data->aaData[$index][11] = $val['quantity_export'] - $val['quantity_reenter'];
                }
            } else {
                // print_arrays($data->aaData[$index]);
                // $data->aaData[$index][0] = 0;
            }


            $index++;
        }
        $data->aaData = array_values($data->aaData);
        $data->{'title_excel'} = [handlingTitleExcel()['title']];
        // print_arrays($data);
        echo json_encode($data);
    }

    public function getProductionDetailed()
    {
        if (!$this->perViewProductionDetailed) {
            accessDenied($js = true);
        }

        $searchProduct = $this->input->post('products');
        $searchPurchaseProducts = $this->input->post('purchase_products');
        $searchStartDate = $this->input->post('start_date');
        $searchEndDate = $this->input->post('end_date');

        $products = "(
            SELECT tbl_products.id as id, tbl_products.code as item_code, tbl_products.name as item_name, tblunits.unit as unit_name
            FROM tbl_products
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
        ) as products";

        $tbOrders = "(
            SELECT
                tbl_productions_orders_details.id as object_id,
                CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') as reference_no,
                tblclients.company as company,
                tbl_orders.note as note,
                tbl_orders.date as date
            FROM tbl_orders
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.object_id = tbl_orders.id AND tbl_productions_orders_details.object_type = 'orders'
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan.id as object_id,
                tbl_business_plan.reference_no reference_no
            FROM tbl_business_plan
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.object_id = tbl_business_plan.id AND tbl_productions_orders_details.object_type = 'business_plan'
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $this->datatables->select("
            tbl_purchase_products.id as id,
            CONCAT(COALESCE(tb_orders.reference_no, ''), ' ', COALESCE(tb_business_plan.reference_no, '')) as reference_order,
            tb_orders.note as note_orders,
            tbl_purchase_products.reference_no as reference_no,
            tbl_purchase_products.date as date,
            products.item_code as item_code,
            products.item_name as item_name,
            products.unit_name as unit_name,
            tbl_purchase_product_items.quantity as quantity,
        ", FALSE)
        ->from('tbl_purchase_products')
        ->join('tbl_purchase_product_items', 'tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id', 'inner')
        ->join($tbOrders, 'tbl_purchase_products.productions_orders_details_id = tb_orders.object_id', 'left')
        
        ->join($tbBusinessPlan, 'tbl_purchase_products.productions_orders_details_id = tb_business_plan.object_id', 'left')
        ->join($products, 'products.id = tbl_purchase_product_items.item_id AND tbl_purchase_product_items.type_item = "products"', 'left');

        if (!empty($searchPurchaseProducts)) {
            $this->datatables->where('tbl_purchase_products.reference_no', $searchPurchaseProducts);
        }

        if (!empty($searchProduct)) {
            $searchProduct = str_replace('__products', '', $searchProduct);
            $searchProduct = str_replace(',', "','", $searchProduct);
            $this->datatables->where("tbl_purchase_product_items.item_id IN ('".$searchProduct."')");
        }

        if (!empty($searchStartDate)) {
            $this->datatables->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") >=', to_sql_date($searchStartDate));
        }
        if (!empty($searchEndDate)) {
            $this->datatables->where('DATE_FORMAT(tbl_purchase_products.date, "%Y-%m-%d") <=', to_sql_date($searchEndDate));
        }
        // print_arrays($this->db->get_compiled_select('tbl_products'), FALSE);

        $data = json_decode($this->datatables->generate());

        $data->title_excel = [handlingTitleExcel()['title']];
        echo json_encode($data);
    }

    public function getGeneralProduction()
    {
        if (!$this->perViewGeneralProduction) {
            accessDenied($js = true);
        }

        $products = $this->input->post('products');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $tbOrders = "(
            SELECT
                tbl_order_items.id as object_id,
                CONCAT(COALESCE(tbl_orders.reference_no, ''), '(', COALESCE(tblclients.company, ''),')') as reference_no,
                tblclients.company as company,
                tbl_orders.note as note,
                tbl_orders.date as date
            FROM tbl_order_items
            INNER JOIN tbl_orders ON tbl_order_items.order_id = tbl_orders.id 
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_orders.status_productions_orders != 0
        ) tb_orders";

        $tbBusinessPlan = "(
            SELECT
                tbl_business_plan_items.id as object_id,
                tbl_business_plan.reference_no reference_no

            FROM tbl_business_plan_items
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
            WHERE tbl_business_plan.status_productions_orders != 0
        ) tb_business_plan";

        $this->datatables->select("
            tbl_products.id as id,
            GROUP_CONCAT(
                distinct CONCAT(
                    COALESCE(tb_orders.reference_no, ''), ' ', COALESCE(tb_business_plan.reference_no, '')
                )
            ) as reference_order,
            GROUP_CONCAT(distinct tb_orders.note) as note_orders,
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tblunits.unit as unit_name,
            SUM(tbl_productions_orders_items.quantity) as quantity
        ", FALSE)
        ->from('tbl_productions_orders')
        ->join('tbl_productions_orders_items', 'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id', 'inner')
        ->join($tbOrders, 'tb_orders.object_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = "orders"', 'left')
        ->join($tbBusinessPlan, 'tb_business_plan.object_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = "business_plan"', 'left')
        ->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner')
        ->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');

        $this->datatables->group_by('tbl_products.id');

        if (!empty($products)) {
            $products = str_replace('__products', '', $products);
            $products = str_replace(',', "','", $products);
            $this->datatables->where("tbl_products.id IN ('".$products."')");
        }

        if (!empty($start_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") >=', to_sql_date($start_date));
        }

        if (!empty($end_date)) {
            $this->datatables->where('DATE_FORMAT(tbl_productions_orders.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }

        $custom[] = ['index' => 4, 'select' => 'quantity'];
        $custom_select[6] = 'SUM(tbl_productions_orders_items)';
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        // print_arrays($this->db->get_compiled_select('tbl_productions_orders'));
        // $this->datatables->where('tbl_productions_orders.id', 10);
        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // $index = 0;
        // foreach ($data->aaData as $key => $value) {
        // }
        echo json_encode($data);
    }

    public function searchItemsCustom() {
        $data = [];

        $q = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('CONCAT(tbl_products.id, "__products") as id, CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text, tbl_products.name as item_name, IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, tblunits.unit as unit_name, tbl_products.price_sell as price_sell, tbl_products.info as info, CONCAT(tbl_products.category_id, "__products") as category_id', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products', 'semi_products');
        $this->db->limit($limit);
        $products = $this->db->get()->result_array();

        
        $this->db->select('CONCAT(tbl_materials.id, "__materials") as id, CONCAT(tbl_materials.code, "(", tbl_materials.name, ")") as text, tbl_materials.name as item_name', false);
        $this->db->from('tbl_materials');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $items = $this->db->get()->result_array();
        $data['results'] = [
            [
                'text' => lang('semi_products'), 'children' => $products
            ],
            [
                'text' => lang('materials'), 'children' => $items
            ]
        ];
        echo json_encode($data);
    }

    function getProductionsProducts() {
        if (!$this->perViewUseMlAcProductionOrders) {
            accessDenied($js = true);
        }

        $stages_search = $this->input->post('stages_search') ? $this->input->post('stages_search') : 0;
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $aColumns = [
            'tbl_purchase_product_items.item_id as item_id',
            'tbl_products.images as images',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            '"" as id4',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_purchase_products';
        $where        = [
            'AND tbl_purchase_product_items.type_item = "products"',
            'AND tbl_products.type_products = "products"',
            'AND tbl_productions_orders_items_stages.stage_id = "'.$stages_search.'"',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_purchase_products.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tbl_purchase_products.date <= '$end_date_search'");
        }

        $filter = [];
        $join = [
            'INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_purchase_product_items.item_id',
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'SUM(tbl_purchase_product_items.quantity) as quantity',
            'SUM(TIMESTAMPDIFF(SECOND, tbl_productions_orders.date, tbl_purchase_products.date)) as date_diff'
        ], 'GROUP BY tbl_purchase_product_items.item_id', []);
        

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
       
        foreach ($rResult as $key => $aRow) {

            $images = base_url('assets/images/tnh/no_image.png');
            if (!empty($aRow['images'])) {
                $images = base_url('uploads/products/'.$aRow['images']);
            }

            $tdImages = '<div class="td-image">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:35px; margin: auto;">
                            <a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class="">
                                    <img src="'.$images.'" style="border-radius: 50%">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>';

            $quantity = $aRow['quantity'];
            $date_diff = $aRow['date_diff'];
            $time_average = $date_diff/$quantity;

            // $s = $ss%60;
            $m = floor(($time_average%3600)/60);
            $h = floor(($time_average%86400)/3600);
            $d = floor(($time_average%2592000)/86400);
            // $M = floor($ss/2592000);

            $row[0] = $aRow['item_id'];
            $row[1] = $tdImages;
            $row[2] = $aRow['item_code'];
            $row[3] = $aRow['item_name'];
            $row[4] = '<div class="text-center">'.$d .' ngày '. $h . ' giờ'.' '.$m.' phút</div>';
            $output['aaData'][] = $row;
        }
      
        echo json_encode($output);
    }

    public function getProductionsCategory() {
        if (!$this->perViewUseMlAcProductionOrders) {
            accessDenied($js = true);
        }

        $stages_search = $this->input->post('stages_search') ? $this->input->post('stages_search') : 0;
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $aColumns = [
            'tbl_category_products.id as category_id',
            'tbl_category_products.code as item_code',
            'tbl_category_products.name as item_name',
            '"" as id4',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_purchase_products';
        $where        = [
            'AND tbl_purchase_product_items.type_item = "products"',
            'AND tbl_products.type_products = "products"',
            'AND tbl_productions_orders_items_stages.stage_id = "'.$stages_search.'"',
        ];

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where, "AND tbl_purchase_products.date >= '$start_date_search'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where, "AND tbl_purchase_products.date <= '$end_date_search'");
        }

        $filter = [];
        $join = [
            'INNER JOIN tbl_purchase_product_items ON tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id',
            'INNER JOIN tbl_products ON tbl_products.id = tbl_purchase_product_items.item_id',
            'INNER JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'SUM(tbl_purchase_product_items.quantity) as quantity',
            'SUM(TIMESTAMPDIFF(SECOND, tbl_productions_orders.date, tbl_purchase_products.date)) as date_diff'
        ], 'GROUP BY tbl_category_products.id', []);
        

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
       
        foreach ($rResult as $key => $aRow) {


            $quantity = $aRow['quantity'];
            $date_diff = $aRow['date_diff'];
            $time_average = $date_diff/$quantity;

            // $s = $ss%60;
            $m = floor(($time_average%3600)/60);
            $h = floor(($time_average%86400)/3600);
            $d = floor(($time_average%2592000)/86400);
            // $M = floor($ss/2592000);

            $row[0] = $aRow['category_id'];
            $row[1] = $aRow['item_code'];
            $row[2] = $aRow['item_name'];
            $row[3] = '<div class="text-center">'.$d .' ngày '. $h . ' giờ'.' '.$m.' phút</div>';
            $output['aaData'][] = $row;
        }
      
        echo json_encode($output);
    }

    public function searchDelivery($id = false){
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $this->db->select('tbl_deliveries.id as id, CONCAT(tbl_deliveries.reference_no, "(", IF(tbl_deliveries.customer_name IS NOT NULL,tbl_deliveries.customer_name,""), ")") as text', false);
        $this->db->from('tbl_deliveries');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_deliveries.reference_no', $term);
            $this->db->or_like('tbl_deliveries.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $result =  $this->db->get()->result_array();
        $data['results'] = $result;
        if ($id) {
            $delivery = get_table_where('tbl_deliveries',['id' => $id],'','row_array');
            $data['row'] = ['id' => $delivery['id'], 'text' => $delivery['reference_no']];
        }
        echo json_encode($data);
    }

    public function getDetailDeliveries(){

        $customer_select = $this->input->post('customer_select');
        $customer_search = $this->input->post('customer_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $items_search = $this->input->post('items_search');
        $orders_search = $this->input->post('orders_search');
        $delivery_search = $this->input->post('delivery_search');

        $tb_tamp = '(
            SELECT 
                (tb_tamp.delivery_id) as delivery_id,
                (tb_tamp.delivery_item_id) as delivery_item_id,
                (tb_tamp.order_code) as order_code,
                (tb_tamp.command) as command,
                SUM(tb_tamp.quantity_put) as quantity_put,
                SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                SUM(tb_tamp.quantity_loss) as quantity_loss
            FROM (
                SELECT
                    counter_items_number as counter_items_number,
                    delivery_id as delivery_id,
                    delivery_item_id as delivery_item_id,
                    MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                FROM `tbl_delivery_items_columns` 
                GROUP BY counter_items_number,delivery_id,delivery_item_id
            ) tb_tamp
            GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
        ) as tb_tamp';

        $tb_customer_group = "(
            SELECT 
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tblcustomers_groups
            JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group";

        // $aColumns = [
        //     'tbl_deliveries.reference_no as reference_no',
        //     'tbl_deliveries.date_created as date_created',
        //     'tbl_deliveries.date as date',
        //     'tblclients.zcode as zcode',
        //     'tb_customer_group.name_group as name_group',
        //     'tblclients.company as customer_name',
        //     'tbl_orders.reference_no as reference_no_order',
        //     'tb_tamp.order_code as code',
        //     'tb_tamp.command as command',
        //     'tbl_products.code as item_code',
        //     'tbl_order_items.product_name_customer as item_name',
        //     '"" as mode',
        //     '"" as unit',
        //     'SUM(tb_tamp.quantity_put) as quantity',
        //     'tbl_delivery_items.price as price',
        // ];

        $aColumns = [
            'tbl_deliveries.reference_no as reference_no',
            'tbl_deliveries.date_created as date_created',
            'tbl_deliveries.date as date',
            'tblclients.zcode as zcode',
            'tb_customer_group.name_group as name_group',
            'tblclients.company as customer_name',
            'tbl_orders.reference_no as reference_no_order',
            '"" as code',
            '"" as command',
            '"" as item_code',
            '"" as item_name',
            '"" as mode',
            '"" as unit',
            '"" as quantity',
            '"" as price',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_deliveries';
        $where = [
            'AND tbl_orders.type_orders != 2'
        ];
        $filter = [];
        $join = [
            // "INNER JOIN tbl_delivery_items ON tbl_delivery_items.delivery_id = tbl_deliveries.id",
            "INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id",
            "INNER JOIN tblclients ON tblclients.userid = tbl_deliveries.customer_id",
            "LEFT JOIN $tb_customer_group ON tb_customer_group.customer_id = tblclients.userid",
            // "INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_delivery_items.order_item_id",
            // "INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id",
            // "INNER JOIN $tb_tamp ON tb_tamp.delivery_item_id = tbl_delivery_items.id"
        ];

        if (!empty($customer_search)){
            array_push($where,'AND tbl_deliveries.customer_id = '.$customer_search.'');
        }
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            array_push($where,'AND tbl_deliveries.date >= "'.$start_date_search.'"');
        }
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            array_push($where,'AND tbl_deliveries.date <= "'.$end_date_search.'"');
        }
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            array_push($where,'AND exists (
                SELECT tbl_delivery_items.id
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.item_id = '.$item_id.' AND tbl_delivery_items.delivery_id = tbl_deliveries.id
                
            )');
            // array_push($where,'AND tbl_delivery_items.item_id = '.$item_id.'');
        }
        if (!empty($orders_search)){
            array_push($where,'AND tbl_deliveries.order_id = '.$orders_search.'');
        }
        if (!empty($delivery_search)){
            array_push($where,'AND tbl_deliveries.id = '.$delivery_search.'');
        }

        // $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [

        //     'tbl_deliveries.tax_name',
        //     'tbl_deliveries.tax_rate',
        //     'tbl_delivery_items.type_item',
        //     'tbl_delivery_items.item_id',
        //     'tbl_delivery_items.order_item_id',
        // ], 'GROUP BY type_item, item_id, tb_tamp.order_code, tb_tamp.command, tbl_deliveries.id', [], []);

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_deliveries.tax_name',
            'tbl_deliveries.tax_rate',
            'tbl_deliveries.id as id_delivery',
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        $fotter = [
            'total_quantity' => 0,
        ];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $start++;

            // $type_item = $aRow['type_item'];
            // $items_id = $aRow['item_id'];
            // if ($type_item == "products") {
            //     $info = $this->products_model->rowProduct($items_id);
            //     $unit = $this->unit_model->rowUnit($info['unit_id']);
            // } else if ($type_item == "items") {
            //     $info = $this->items_model->rowItems($items_id);
            //     $unit = $this->unit_model->rowUnit($info['unit']);
            // } else if ($type_item == "materials") {
            //     $info = $this->items_model->rowMaterial($items_id);
            //     $unit = $this->unit_model->rowUnit($info['unit_id']);
            // }
            $id_delivery = $aRow['id_delivery'];
            // $tb_tamp = '(
            //     SELECT 
            //         (tb_tamp.delivery_item_id) as delivery_item_id,
            //         (tb_tamp.order_code) as order_code,
            //         (tb_tamp.command) as command,
            //         SUM(tb_tamp.quantity_put) as quantity_put,
            //         SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
            //         SUM(tb_tamp.quantity_loss) as quantity_loss
            //     FROM (
            //         SELECT
            //             counter_items_number as counter_items_number,
            //             delivery_item_id as delivery_item_id,
            //             MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
            //             MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
            //             MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
            //             MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
            //             MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
            //         FROM `tbl_delivery_items_columns`
            //         WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
            //         GROUP BY counter_items_number,delivery_item_id
            //     ) tb_tamp
            //     GROUP BY tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
            // ) as tb_tamp';

            $tb_tamp = '(
                SELECT 
                    (tb_tamp.delivery_id) as delivery_id,
                    (tb_tamp.delivery_item_id) as delivery_item_id,
                    (tb_tamp.order_code) as order_code,
                    (tb_tamp.command) as command,
                    SUM(tb_tamp.quantity_put) as quantity_put,
                    SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                    SUM(tb_tamp.quantity_loss) as quantity_loss
                FROM (
                    SELECT
                        counter_items_number as counter_items_number,
                        delivery_id as delivery_id,
                        delivery_item_id as delivery_item_id,
                        MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                        MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                    FROM `tbl_delivery_items_columns` 
                    WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                    GROUP BY counter_items_number,delivery_id,delivery_item_id
                ) tb_tamp
                GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
            ) as tb_tamp';

            $this->db->select('
                tbl_delivery_items.item_id as item_id,
                tbl_delivery_items.item_code as item_code,
                tb_tamp.order_code as code,
                tb_tamp.command as command,
                tbl_order_items.product_name_customer as item_name,
                tbl_delivery_items.price as price,
                SUM(tb_tamp.quantity_put) as quantity,
                tbl_products.code as item_code,
                tbl_products.mode as mode,
                tblunits.unit as unit
            ');
            $this->db->from('tbl_delivery_items');
            $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
            $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
            $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
            $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command, tb_tamp.order_code');
            $delivery_items = $this->db->get()->result_array();

            if (!empty($delivery_items)) {
                foreach ($delivery_items as $key => $item) {
                    $row[0] = '<div>'.($aRow['reference_no']).'</div>';
                    $row[1] = '<div>'._dhau($aRow['date_created']).'</div>';
                    $row[2] = '<div>'._dhau($aRow['date']).'</div>';
                    $row[3] = '<div>'.($aRow['zcode']).'</div>';
                    $row[4] = '<div>'.($aRow['name_group']).'</div>';
                    $row[5] = '<div>'.($aRow['customer_name']).'</div>';
                    $row[6] = '<div>'.($aRow['reference_no_order']).'</div>';
                    $row[7] = '<div>'.($item['code']).'</div>';
                    $row[8] = '<div>'.($item['command']).'</div>';
                    $row[9] = '<div>'.($item['item_code']).'</div>';
                    $row[10] = '<div>'.($item['item_name']).'</div>';
                    $row[11] = '<div>'.($item['mode']).'</div>';
                    $row[12] = '<div class="text-center">'.($item['unit']).'</div>';
                    $row[13] = '<div class="text-center" >'.formatNumber($item['quantity']).'</div>';
                    $row[14] = '<div class="text-right" >'.formatMoney($item['price']).'</div>';
                    $fotter['total_quantity'] += $item['quantity'];
                    $output['aaData'][] = $row;
                }
            }

            // $row[0] = '<div>'.($aRow['reference_no']).'</div>';
            // $row[1] = '<div>'._dhau($aRow['date_created']).'</div>';
            // $row[2] = '<div>'._dhau($aRow['date']).'</div>';
            // $row[3] = '<div>'.($aRow['zcode']).'</div>';
            // $row[4] = '<div>'.($aRow['name_group']).'</div>';
            // $row[5] = '<div>'.($aRow['customer_name']).'</div>';
            // $row[6] = '<div>'.($aRow['reference_no_order']).'</div>';
            // $row[7] = '<div>'.($aRow['code']).'</div>';
            // $row[8] = '<div>'.($aRow['command']).'</div>';
            // $row[9] = '<div>'.($aRow['item_code']).'</div>';
            // $row[10] = '<div>'.($aRow['item_name']).'</div>';
            // $row[11] = '<div>'.($info['mode']).'</div>';
            // $row[12] = '<div class="text-center">'.($unit['unit']).'</div>';
            // $row[13] = '<div class="text-center" >'.formatNumber($aRow['quantity']).'</div>';
            // $row[14] = '<div class="text-right" >'.formatMoney($aRow['price']).'</div>';
            // $fotter['total_quantity'] += $aRow['quantity'];
        }
        $fotter['total_quantity'] = formatNumber($fotter['total_quantity']);
        $output['fotter'] = $fotter;
        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getDetailDeliveriesOld(){

        $customer_select = $this->input->post('customer_select');
        $customer_search = $this->input->post('customer_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $items_search = $this->input->post('items_search');
        $orders_search = $this->input->post('orders_search');
        $delivery_search = $this->input->post('delivery_search');


        $this->db->select('
            tbl_delivery_items_columns.*
            ');
        $this->db->from('tbl_deliveries');
        $this->db->join('tbl_delivery_items','tbl_delivery_items.delivery_id = tbl_deliveries.id');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_deliveries.order_id');
        $this->db->join('tblclients','tblclients.userid = tbl_deliveries.customer_id');
        $this->db->join('tbl_delivery_items_columns','tbl_delivery_items_columns.delivery_item_id = tbl_delivery_items.id');
        $this->db->where('tbl_orders.type_orders != 2');
        if (!empty($customer_search)){
            $this->db->where('tbl_deliveries.customer_id',$customer_search);
        }
        if (!empty($start_date_search)) {
            $start_date_search_new = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where('tbl_deliveries.date >=',$start_date_search_new);
        }
        if (!empty($end_date_search)) {
            $end_date_search_new = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where('tbl_deliveries.date <=',$end_date_search_new);
        }
        if (!empty($items_search)) {
            $arrItemNew = explode('__', $items_search);
            $item_id_new = $arrItemNew[0];
            $this->db->where('tbl_delivery_items.item_id',$item_id_new);
        }
        if (!empty($orders_search)){
            $this->db->where('tbl_deliveries.order_id',$orders_search);
        }
        if (!empty($delivery_search)){
            $this->db->where('tbl_deliveries.id',$delivery_search);
        }
        $itemsNew = $this->db->get()->result_array();
        $dtOrderItemsColumns = [];
        if (!empty($itemsNew)){
            foreach ($itemsNew as $key => $value){
                $check_key = $value['delivery_item_id'];
                $dtOrderItemsColumns[$check_key][] = $value;
            }
        }


        $this->db->select('
            tbl_columns_detail.*,
            tbl_products.id as product_id
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tbl_products_columns', 'tbl_products_columns.product_id = tbl_products.id');
        $this->db->join('tbl_columns', 'tbl_columns.id = tbl_products_columns.columns_id');
        $this->db->join('tbl_columns_detail', 'tbl_columns_detail.columns_id = tbl_columns.id');
        $dtProductsColumns = $this->db->get()->result_array();
        $dtProductsColumnsNew = [];
        if (!empty($dtProductsColumns)){
            foreach ($dtProductsColumns as $key => $value){
                $check_key = $value['product_id'];
                $dtProductsColumnsNew[$check_key][] = $value;
            }
        }
        $this->db->select('
            tbl_order_items.ct_counter_item as ct_counter_item,
            tbl_order_items.product_name_customer as product_name_customer,
            tbl_deliveries.id as delivery_id,
            tbl_deliveries.date_created as date_created,
            tbl_deliveries.date as date,
            tbl_deliveries.reference_no as reference_no,
            tbl_orders.reference_no as reference_no_order,
            tblclients.zcode as zcode,
            tblclients.company as customer_name,
            tbl_deliveries.tax_rate as tax_rate,
            tbl_deliveries.tax_name as tax_name,
            tbl_delivery_items.id as id,
            tbl_delivery_items.order_item_id as order_item_id,
            tbl_delivery_items.type_item as type_item,
            tbl_delivery_items.item_id as item_id,
            tbl_delivery_items.quantity as quantity,
            tbl_delivery_items.price as price,
            tbl_delivery_items.amount as amount
            ');
        $this->db->from('tbl_deliveries');
        $this->db->join('tbl_delivery_items','tbl_delivery_items.delivery_id = tbl_deliveries.id');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_deliveries.order_id');
        $this->db->join('tblclients','tblclients.userid = tbl_deliveries.customer_id');
        $this->db->join('tbl_order_items','tbl_order_items.id = tbl_delivery_items.order_item_id');
        $this->db->where('tbl_orders.type_orders != 2');
        if (!empty($customer_search)){
            $this->db->where('tbl_deliveries.customer_id',$customer_search);
        }
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where('tbl_deliveries.date >=',$start_date_search);
        }
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where('tbl_deliveries.date <=',$end_date_search);
        }
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            $this->db->where('tbl_delivery_items.item_id',$item_id);
        }
        if (!empty($orders_search)){
            $this->db->where('tbl_deliveries.order_id',$orders_search);
        }
        if (!empty($delivery_search)){
            $this->db->where('tbl_deliveries.id',$delivery_search);
        }
        $items = $this->db->get()->result_array();


        $orderItemsColumnsNewVs1= [];
        if (!empty($items)){
            foreach ($items as $key => $value){
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $delivery_id = $value['delivery_id'];
                $order_item_id = $value['order_item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                } else if ($type_item == "materials") {
                    $info = $this->items_model->rowMaterial($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                }
                if ($type_item == "products") {
                    $thSub = '';
                    $trHtmlChild = '';
                    $ct_counter_item = $value['ct_counter_item'];
                    $productsColumns = !empty($dtProductsColumnsNew[$value['item_id']]) ? $dtProductsColumnsNew[$value['item_id']] : '';;
                    $orderItemsColumns = !empty($dtOrderItemsColumns[$value['id']]) ? $dtOrderItemsColumns[$value['id']] : [];
                    $orderItemsColumnsNew = [];
                    if ($ct_counter_item > 0) {
                        for ($i = 0; $i < $ct_counter_item; $i++) {
                            $arrNew = [];
                            foreach ($orderItemsColumns as $kO => $vO) {
                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                    $order_code = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['code'] = $order_code;
                                    continue;
                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                    $command = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['command'] = $command;
                                    continue;
                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                    $quantity_put = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_put'] = $quantity_put;
                                    continue;
                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                    $quantity_loss = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['quantity_loss'] = $quantity_loss;
                                    continue;
                                } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                                    $sample_quantity_item = $vO['columns_name'];
                                    $orderItemsColumnsNew[$i]['sample_quantity_item'] = $sample_quantity_item;
                                    continue;
                                }
                            }
                        }
                    }

                    if (!empty($orderItemsColumnsNew)) {
                        foreach ($orderItemsColumnsNew as $kkk => $vvv) {
                            if (empty($vvv)){
                                continue;
                            }
                            $check_key = $vvv['code'].'__'.$vvv['command'].'__'.$items_id.'__'.$type_item.'__'.$delivery_id;
                            if (!empty($orderItemsColumnsNewVs1[$check_key])) {
                                $orderItemsColumnsNewVs1[$check_key]['quantity_put'] += $vvv['quantity_put'];
                                $orderItemsColumnsNewVs1[$check_key]['quantity_loss'] += $vvv['quantity_loss'];
                                $orderItemsColumnsNewVs1[$check_key]['sample_quantity_item'] += $vvv['sample_quantity_item'];
                            } else {
                                $orderItemsColumnsNewVs1[$check_key] = $vvv;
                                $orderItemsColumnsNewVs1[$check_key]['item_id'] = $items_id;
                                $orderItemsColumnsNewVs1[$check_key]['type_item'] = $type_item;
                                $orderItemsColumnsNewVs1[$check_key]['reference_no'] = $value['reference_no'];
                                $orderItemsColumnsNewVs1[$check_key]['reference_no_order'] = $value['reference_no_order'];
                                $orderItemsColumnsNewVs1[$check_key]['customer_name'] = $value['customer_name'];
                                $orderItemsColumnsNewVs1[$check_key]['zcode'] = $value['zcode'];
                                $orderItemsColumnsNewVs1[$check_key]['date'] = $value['date'];
                                $orderItemsColumnsNewVs1[$check_key]['date_created'] = $value['date_created'];
                                $orderItemsColumnsNewVs1[$check_key]['price'] = $value['price'];
                                $orderItemsColumnsNewVs1[$check_key]['item_name'] = $value['product_name_customer'];
                                $orderItemsColumnsNewVs1[$check_key]['item_code'] = $info['code'];
                                $orderItemsColumnsNewVs1[$check_key]['mode'] = $info['mode'];
                                $orderItemsColumnsNewVs1[$check_key]['unit'] = $unit['unit'];
                                $orderItemsColumnsNewVs1[$check_key]['id'] = $value['id'];
                                $orderItemsColumnsNewVs1[$check_key]['tax_name'] = $value['tax_name'];
                                $orderItemsColumnsNewVs1[$check_key]['tax_rate'] = $value['tax_rate'];
                            }
                        }
                    }
                }
            }
        }
        $tableAllItemsNew = '';
        $tableAllItemsNew .= "(";
        $tableAllItemsNew .= "( 
            SELECT 0 as id,
            '' as zcode, 
            '' as customer_name, 
            '' as code, 
            '' as command, 
            0 as item_id, 
            '' as type_item, 
            '' as quantity, 
            '' as reference_no, 
            '' as reference_no_order, 
            '' as date, 
            '' as date_created, 
            '' as item_name, 
            '' as item_code, 
            '' as mode, 
            '' as unit, 
            0 as tax_rate, 
            '' as tax_name, 
            0 as price) UNION ALL";

        if (!empty($orderItemsColumnsNewVs1)){
            foreach ($orderItemsColumnsNewVs1 as $key => $value){
                $id = $value['id'];
                $zcode = $value['zcode'];
                $customer_name = $value['customer_name'];
                $code = $value['code'];
                $code = str_replace('"', '', $code);
                $code = str_replace("'", '', $code);
                $command = $value['command'];
                $command = str_replace('"', '', $command);
                $command = str_replace("'", '', $command);
                $item_id = $value['item_id'];
                $type_item = $value['type_item'];
                $quantity = $value['quantity_put'];
                $reference_no = $value['reference_no'];
                $reference_no_order = $value['reference_no_order'];
                $date = $value['date'];
                $date_created = $value['date_created'];
                $item_name = $value['item_name'];
                $item_name = str_replace('"', '', $item_name);
                $item_name = str_replace("'", '', $item_name);
                $item_code = $value['item_code'];
                $item_code = str_replace('"', '', $item_code);
                $item_code = str_replace("'", '', $item_code);
                $mode = $value['mode'];
                $unit = $value['unit'];
                $tax_rate = $value['tax_rate'];
                $tax_name = $value['tax_name'];
                $price = $value['price'];
                $tableAllItemsNew .= "( SELECT 
                                    '$id' as id,
                                    '$zcode' as zcode,
                                    '$customer_name' as customer_name,
                                    '$code' as code,
                                    '$command' as command,
                                    '$item_id' as item_id,
                                    '$type_item' as type_item,
                                    '$quantity' as quantity,
                                    '$reference_no' as reference_no,
                                    '$reference_no_order' as reference_no_order,
                                    '$date' as date,
                                    '$date_created' as date_created,
                                    '$item_name' as item_name,
                                    '$item_code' as item_code,
                                    '$mode' as mode,
                                    '$unit' as unit,
                                    '$tax_rate' as tax_rate,
                                    '$tax_name' as tax_name,
                                    '$price' as price
                                    ) UNION ALL";
            }
        }

        $tableAllItemsNew = trim($tableAllItemsNew, 'UNION ALL');
        $tableAllItemsNew .= ') table_all_item_new';

        $tableAllItems = "(
            SELECT 
                table_all_item_new.id as id,
                table_all_item_new.zcode as zcode,
                table_all_item_new.customer_name as customer_name,
                table_all_item_new.code as code,
                (command) as command,
                (item_id) as item_id,
                (type_item) as type_item,
                quantity as quantity,
                reference_no as reference_no,
                reference_no_order as reference_no_order,
                date as date,
                date_created as date_created,
                item_name as item_name,
                item_code as item_code,
                mode as mode,
                unit as unit,
                tax_rate as tax_rate,
                tax_name as tax_name,
                price as price
            FROM $tableAllItemsNew
        ) table_all_item";
//        $query = $this->db->query($tableAllItems)->result_array();
//        print_arrays($query);

        $aColumns = [
            'table_all_item.reference_no as reference_no',
            'table_all_item.date_created as date_created',
            'table_all_item.date as date',
            'table_all_item.zcode as zcode',
            'table_all_item.customer_name as customer_name',
            'table_all_item.reference_no_order as reference_no_order',
            'table_all_item.code as code',
            'table_all_item.command as command',
            'table_all_item.item_name as item_name',
            'table_all_item.mode as mode',
            'table_all_item.unit as unit',
            'table_all_item.quantity as quantity',
            'table_all_item.price as price',
        ];
        $sIndexColumn = 'table_all_item.id';
        $sTable = $tableAllItems;
        $where = [
            'AND table_all_item.id != 0'
        ];
        $filter = [];
        $join = [
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'table_all_item.id',
            'table_all_item.tax_rate',
        ], '', [], ['union_all' => true]);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        $fotter = [
            'total_quantity' => 0,
        ];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $start++;

            $row[0] = '<div>'.($aRow['reference_no']).'</div>';
            $row[1] = '<div>'._dhau($aRow['date_created']).'</div>';
            $row[2] = '<div>'._dhau($aRow['date']).'</div>';
            $row[3] = '<div>'.($aRow['zcode']).'</div>';
            $row[4] = '<div>'.($aRow['customer_name']).'</div>';
            $row[5] = '<div>'.($aRow['reference_no_order']).'</div>';
            $row[6] = '<div>'.($aRow['code']).'</div>';
            $row[7] = '<div>'.($aRow['command']).'</div>';
            $row[8] = '<div>'.($aRow['item_name']).'</div>';
            $row[9] = '<div>'.($aRow['mode']).'</div>';
            $row[10] = '<div class="text-center">'.($aRow['unit']).'</div>';
            $row[11] = '<div class="text-center" >'.formatNumber($aRow['quantity']).'</div>';
            $row[12] = '<div class="text-right" >'.formatMoney($aRow['price']).'</div>';
            $fotter['total_quantity'] += $aRow['quantity'];
            $output['aaData'][] = $row;
        }
        $fotter['total_quantity'] = formatNumber($fotter['total_quantity']);
        $output['fotter'] = $fotter;
        echo json_encode($output);
    }

    public function excel_detail_delivery(){
        ini_set('memory_limit', '3500M');
        ob_end_clean();
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();
        $objPHPExcel = new PHPExcel();

        $customer_search = $this->input->get('customer_search');
        $start_date_search = $this->input->get('start_date_search');
        $end_date_search = $this->input->get('end_date_search');
        $items_search = $this->input->get('items_search');
        $orders_search = $this->input->get('orders_search');
        $delivery_search = $this->input->get('delivery_search');

        $str_customer = lang('all');
        if (!empty($customer_search)) {
            $dtCustomer = get_table_where('tblclients', ['userid' => $customer_search], '', 'row_array', '', 'company');
            $str_customer = $dtCustomer['company'];
        }

        $str_start_date = lang('all');
        if (!empty($start_date_search)) {
            $str_start_date = $start_date_search;
        }

        $str_end_date = lang('all');
        if (!empty($start_date_search)) {
            $str_end_date = $start_date_search;
        }


        $tb_tamp = '(
            SELECT 
                (tb_tamp.delivery_id) as delivery_id,
                (tb_tamp.delivery_item_id) as delivery_item_id,
                (tb_tamp.order_code) as order_code,
                (tb_tamp.command) as command,
                SUM(tb_tamp.quantity_put) as quantity_put,
                SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                SUM(tb_tamp.quantity_loss) as quantity_loss
            FROM (
                SELECT
                    counter_items_number as counter_items_number,
                    delivery_id as delivery_id,
                    delivery_item_id as delivery_item_id,
                    MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                    MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                FROM `tbl_delivery_items_columns` 
                GROUP BY counter_items_number,delivery_id,delivery_item_id
            ) tb_tamp
            GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
        ) as tb_tamp';


        $tb_customer_group = "(
            SELECT 
                tblcustomer_groups.customer_id as customer_id,
                GROUP_CONCAT(tblcustomers_groups.name) as name_group
            FROM tblcustomers_groups
            JOIN tblcustomer_groups ON tblcustomer_groups.groupid = tblcustomers_groups.id
            GROUP BY tblcustomer_groups.customer_id
        ) tb_customer_group";

        // $this->db->select('
        //     tbl_deliveries.reference_no as reference_no,
        //     tbl_deliveries.date_created as date_created,
        //     tbl_deliveries.date as date,
        //     tblclients.zcode as zcode,
        //     tblclients.company as customer_name,
        //     tbl_orders.reference_no as reference_no_order,
        //     tb_tamp.order_code as code,
        //     tb_tamp.command as command,
        //     tbl_order_items.product_name_customer as item_name,
        //     tbl_deliveries.tax_name as tax_name,
        //     tbl_deliveries.tax_rate as tax_rate,
        //     tbl_delivery_items.type_item as type_item,
        //     tbl_delivery_items.item_id as item_id,
        //     tbl_delivery_items.order_item_id as order_item_id,
        //     tblunits.unit as unit,
        //     tbl_products.mode as mode,
        //     SUM(tb_tamp.quantity_put) as quantity,
        //     tbl_delivery_items.price as price,
        //     tb_customer_group.name_group as name_group,
        //     tbl_products.code as item_code
        //     '
        // );
        $this->db->select('
            tbl_deliveries.reference_no as reference_no,
            tbl_deliveries.date_created as date_created,
            tbl_deliveries.date as date,
            tblclients.zcode as zcode,
            tblclients.company as customer_name,
            tbl_orders.reference_no as reference_no_order,
            "" as code,
            "" as command,
            "" as item_name,
            tbl_deliveries.tax_name as tax_name,
            tbl_deliveries.tax_rate as tax_rate,
            "" as type_item,
            "" as item_id,
            "" as order_item_id,
            "" as unit,
            "" as mode,
            "" as quantity,
            "" as price,
            tb_customer_group.name_group as name_group,
            "" as item_code,
            tbl_deliveries.id as id_delivery,
            '
        );
        $this->db->from('tbl_deliveries');
        // $this->db->join('tbl_delivery_items','tbl_delivery_items.delivery_id = tbl_deliveries.id');
        $this->db->join('tbl_orders','tbl_orders.id = tbl_deliveries.order_id');
        $this->db->join('tblclients','tblclients.userid = tbl_deliveries.customer_id');
        // $this->db->join('tbl_order_items','tbl_order_items.id = tbl_delivery_items.order_item_id');
        // $this->db->join($tb_tamp,'tb_tamp.delivery_item_id = tbl_delivery_items.id');
        $this->db->join($tb_customer_group, 'tb_customer_group.customer_id = tblclients.userid', 'left');
        // $this->db->join('tbl_products','tbl_products.id = tbl_delivery_items.item_id');
        // $this->db->join('tblunits','tblunits.unitid = tbl_products.unit_id','left');
        $this->db->where('tbl_orders.type_orders != 2');

        if (!empty($customer_search)){
            $this->db->where('tbl_deliveries.customer_id = '.$customer_search.'');
        }
        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where('tbl_deliveries.date >= "'.$start_date_search.'"');
        }
        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where('tbl_deliveries.date <= "'.$end_date_search.'"');
        }
        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
            $this->db->where(' exists (
                SELECT tbl_delivery_items.id
                FROM tbl_delivery_items
                WHERE tbl_delivery_items.item_id = '.$item_id.' AND tbl_delivery_items.delivery_id = tbl_deliveries.id
            )', false, false);
            // $this->db->where('tbl_delivery_items.item_id = '.$item_id.'');
        }
        if (!empty($orders_search)){
            $this->db->where('tbl_deliveries.order_id = '.$orders_search.'');
        }
        if (!empty($delivery_search)){
            $this->db->where('tbl_deliveries.id = '.$delivery_search.'');
        }

        // $this->db->group_by('type_item,item_id,tb_tamp.order_code,tb_tamp.command,tbl_deliveries.id');

        $rs = $this->db->get()->result_array();

        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'CHI TIẾT GIAO HÀNG');
        $objPHPExcel->getActiveSheet()->mergeCells("B1:N1");

        $objPHPExcel->getActiveSheet()->getStyle("B1")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 20,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Khách hàng: ');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', $str_customer);

        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Ngày bắt đầu: ');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', $str_start_date);

        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Ngày kết thúc: ');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', $str_end_date);

        $row_head = 6;

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_head, 'STT');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row_head, 'Số phiếu');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row_head, 'Ngày lập');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row_head, 'Ngày giao thực tế');
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row_head, 'Mã KH');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$row_head, 'Brand');
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$row_head, 'Khách hàng');
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$row_head, 'Mã ĐĐH');
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$row_head, 'Mã đơn đặt');
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$row_head, 'Chỉ lệnh');
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$row_head, 'Mã TP');
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$row_head, 'Tên TP');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$row_head, 'Quy cách');
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$row_head, 'ĐVT');
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$row_head, 'Tổng SL');
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$row_head, 'Đơn giá');


        $objPHPExcel->getActiveSheet()->getStyle("B1:P7")->applyFromArray([
            'font' => array(
                'bold' => false,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A6:P6")->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $row = 6;
        $group = '';
        $start = 0;
        $quantity_not_delivery = 0;
        $quantity_orders = 0;
        $quantity_delivery = 0;
        $quantity_rest = 0;
        if (!empty($rs)) {
            foreach ($rs as $key => $aRow) {
                $id_delivery = $aRow['id_delivery'];
                $tb_tamp = '(
                    SELECT 
                        (tb_tamp.delivery_id) as delivery_id,
                        (tb_tamp.delivery_item_id) as delivery_item_id,
                        (tb_tamp.order_code) as order_code,
                        (tb_tamp.command) as command,
                        SUM(tb_tamp.quantity_put) as quantity_put,
                        SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                        SUM(tb_tamp.quantity_loss) as quantity_loss
                    FROM (
                        SELECT
                            counter_items_number as counter_items_number,
                            delivery_id as delivery_id,
                            delivery_item_id as delivery_item_id,
                            MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                        FROM `tbl_delivery_items_columns` 
                        WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                        GROUP BY counter_items_number,delivery_id,delivery_item_id
                    ) tb_tamp
                    GROUP BY tb_tamp.delivery_id,tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
                ) as tb_tamp';
    
                $this->db->select('
                    tbl_delivery_items.item_id as item_id,
                    tbl_delivery_items.item_code as item_code,
                    tb_tamp.order_code as code,
                    tb_tamp.command as command,
                    tbl_order_items.product_name_customer as item_name,
                    tbl_delivery_items.price as price,
                    SUM(tb_tamp.quantity_put) as quantity,
                    tbl_products.code as item_code,
                    tbl_products.mode as mode,
                    tblunits.unit as unit
                ');
                $this->db->from('tbl_delivery_items');
                $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id');
                $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
                $this->db->where('tbl_delivery_items.delivery_id', $id_delivery);
                $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command, tb_tamp.order_code');
                $delivery_items = $this->db->get()->result_array();

                if (!empty($delivery_items)) {
                    foreach ($delivery_items as $key => $item) {
                        $row++;
                        $start++;

                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $start);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, ($aRow['reference_no']));
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, _dhau($aRow['date_created']));
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, _dhau($aRow['date']));
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, ($aRow['zcode']));
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, ($aRow['name_group']));
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $aRow['customer_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $aRow['reference_no_order']);
                        // echo '<pre>';
                        // print_r($item['code']);
                        // echo '</pre>';
                        //Xóa dấu = phía đầu
                        $item['code'] = ltrim($item['code'], '=');
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $item['code']);
                        $objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $item['command']);
                        $objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $item['item_code']);
                        $objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $item['item_name'])->getStyle("L$row")->getAlignment()->setWrapText(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('M' . $row, $item['mode']);
                        $objPHPExcel->getActiveSheet()->setCellValue('N' . $row, $item['unit']);
                        $objPHPExcel->getActiveSheet()->setCellValue('O' . $row, $item['quantity'])->getStyle("O$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($item['quantity']));
                        $objPHPExcel->getActiveSheet()->setCellValue('P' . $row, $item['price'])->getStyle("P$row")->getNumberFormat()->setFormatCode(formatMoneyExcel($item['price']));
                    }
                }

            }
        }

        // die;

        $objPHPExcel->getActiveSheet()->getStyle("A$row:P$row")->applyFromArray([
            'font' => array(
                'bold' => false,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $filename = lang('chi_tiet_giao_hang') . '.xls';

        $objPHPExcel->getActiveSheet()->getStyle("A6:P$row")->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->getStyle("A1:K$row")->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->freezePane('A1');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();
    }

    public function getGeneralProductionsNew() {

        if (!$this->perViewGeneralProduction) {
            accessDenied($js = true);
        }

        $isAdmin = is_admin();
        $productions_orders_search = $this->input->post('productions_orders_search');
        $orders_search = $this->input->post('orders_search');
        $business_plan_search = $this->input->post('business_plan_search');
        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $items_search = $this->input->post('items_search');
        $customer_search = $this->input->post('customer_search');
        $type_print_search = $this->input->post('type_print_search');

        if (!empty($orders_and_business_plan)) {
            $orders_and_business_plan = explode('__', $orders_and_business_plan);
            if ($orders_and_business_plan[0] == "orders") {
                $orders_search = $orders_and_business_plan[1];
            } else if ($orders_and_business_plan[0] == "business_plan") {
                $business_plan_search = $orders_and_business_plan[1];
            }
        }

        if (!empty($items_search)) {
            $arrItem = explode('__', $items_search);
            $item_id = $arrItem[0];
        }



        $tbDateDelivery = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.items_id as items_id,
                MIN(tbl_productions_plan_details.date) as date_shipping
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
            WHERE tbl_productions_plan_items.is_preventive = 0
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_date_delivery";

        $tbPurchasesErrors = "(
            SELECT
                tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            WHERE tbl_purchase_products.is_errors = 1
            GROUP BY tbl_purchase_products.productions_orders_details_id
        ) tb_purchases_errors";

        $tbStages = "(
            SELECT
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                GROUP_CONCAT(distinct tbl_stages.name SEPARATOR ',') as stage_name
            FROM tbl_productions_orders_items_stages
            INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
            INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
            WHERE tbl_category_stages.is_in = 1
            GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
            ORDER BY tbl_productions_orders_items_stages.number ASC
        ) tb_stages";

        // $tbProductionsOrderItems = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         tb_date_delivery.date_shipping as date_shipping,
        //         tbl_productions_plan.note as note_plan,
        //         tbl_productions_orders_items.items_id as items_id,
        //         tbl_productions_orders_items.items_name as items_name,
        //         tbl_productions_orders_items.items_code as items_code,
        //         SUM(IF (tbl_productions_orders_items.object_item_type = 'orders', tbl_productions_orders_items.quantity, 0)) as quantity_order,
        //         SUM(tbl_productions_orders_items.quantity) as quantity,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        //         SUM(tb_purchases_errors.quantity_errors) as quantity_errors,
        //         tbl_productions_orders_items.plan_id as plan_id,
        //         tb_stages.stage_name as stage_name

        //     FROM tbl_productions_orders_items
        //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        //     LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id AND tb_date_delivery.items_id = tbl_productions_orders_items.items_id
        //     LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
        //     LEFT JOIN $tbStages ON tb_stages.productions_orders_items_id = tbl_productions_orders_items.id 
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_production_order_item";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                GROUP_CONCAT(tbl_productions_orders_items.id) as _poi_id,
                GROUP_CONCAT(tbl_productions_orders_details.id) as _pod_id,
                '' as date_shipping,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                SUM(IF (tbl_productions_orders_items.object_item_type = 'orders', tbl_productions_orders_items.quantity, 0)) as quantity_order,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                SUM(0) as quantity_errors,
                tbl_productions_orders_items.plan_id as plan_id,
                '' as stage_name

            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tb_production_order_item.date_shipping as date_shipping',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            'tb_production_order_item.quantity_order as quantity_order',
            'tb_production_order_item.quantity as quantity_manufactures',
            '"" as number_child_sheet',
            'tb_production_order_item.quantity_warehoused as quantity_warehoused',
            'tb_production_order_item.quantity_errors as quantity_errors',
            'IF(COALESCE(tb_production_order_item.quantity, 0) = (COALESCE(tb_production_order_item.quantity_warehoused, 0) + COALESCE(tb_production_order_item.quantity_errors, 0)), 1, 0) as status_new',
            'tb_production_order_item.stage_name as stage_name',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_productions_orders';
        $where        = [

        ];

        $filter = [];

        $join = [
            'INNER JOIN '.$tbProductionsOrderItems.' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id',
        ];

        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id = ".$productions_orders_search);
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

        if (!empty($item_id)) {
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

        if(!empty($type_print_search)) {
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

        // $customer_search = $this->input->post('customer_search');
        // if (!empty($customer_search)) {
        //     array_push($where, 'AND tb_orders.customer_id = '.$customer_search.'');
        // }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_production_order_item.items_id as items_id',
            'tb_production_order_item.plan_id as plan_id',
            'tb_production_order_item._poi_id as _poi_id',
            'tb_production_order_item._pod_id as _pod_id',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $group_id = 0;

        $_quantity_order = 0;
        $_quantity_manufactures = 0;
        $_quantity_warehoused = 0;
        $_quantity_errors = 0;
        $_quantity_pager = 0;

        foreach ($rResult as $key => $aRow) {
            $start++;

            $productions_orders_id = $aRow['id'];
            $items_id = $aRow['items_id'];
            $plan_id = $aRow['plan_id'];

            $_poi_id = $aRow['_poi_id'];
            $_pod_id = $aRow['_pod_id'];

            $tbDateExport = "(
                SELECT
                    tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                    tbl_productions_orders_items_stages.date_active as date_active
                FROM tbl_productions_orders_items_stages
                WHERE tbl_productions_orders_items_stages.stage_id = '".STAGES_MATERIAL."' AND tbl_productions_orders_items_stages.date_active IS NOT NULL
                AND tbl_productions_orders_items_stages.productions_orders_items_id IN ($_poi_id)
            )";
            $_query = $this->db->query($tbDateExport)->row_array();
            $aRow['date_export'] = !empty($_query['date_active']) ? $_query['date_active'] : null;

            $tbPurchasesErrors = "(
                SELECT
                    tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                    SUM(tbl_purchase_products.total_quantity) as quantity_errors
                FROM tbl_purchase_products
                WHERE tbl_purchase_products.is_errors = 1 AND tbl_purchase_products.productions_orders_details_id IN ($_pod_id)
            )";
            $_query = $this->db->query($tbPurchasesErrors)->row_array();
            $aRow['quantity_errors'] = !empty($_query['quantity_errors']) ? $_query['quantity_errors'] : 0;

            $tbDateDelivery = "(
                SELECT
                    tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                    tbl_productions_orders_items.items_id as items_id,
                    MIN(tbl_productions_plan_details.date) as date_shipping
                FROM tbl_productions_plan_items
                INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
                JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
                WHERE tbl_productions_plan_items.is_preventive = 0 AND tbl_productions_orders_items.items_id = $items_id AND tbl_productions_orders_items.productions_orders_id = $productions_orders_id
            )";
            $_query = $this->db->query($tbDateDelivery)->row_array();
            $aRow['date_shipping'] = !empty($_query['date_shipping']) ? $_query['date_shipping'] : false;

            if ($aRow['quantity_manufactures'] <= (float)$aRow['quantity_errors'] + (float)$aRow['quantity_warehoused']) {
                $aRow['status_new'] = 1;
            }

            $tbStages = "(
                SELECT
                    GROUP_CONCAT(distinct tbl_stages.name SEPARATOR ',') as stage_name
                FROM tbl_productions_orders_items_stages
                INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id
                INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages
                WHERE tbl_category_stages.is_in = 1 AND tbl_productions_orders_items_stages.productions_orders_items_id IN ($_poi_id)
                ORDER BY tbl_productions_orders_items_stages.number ASC
            )";
            $_query = $this->db->query($tbStages)->row_array();
            $aRow['stage_name'] = !empty($_query['stage_name']) ? $_query['stage_name'] : false;

            $flagGroup = false;
            if ($group_id != $productions_orders_id) {
                $group_id = $productions_orders_id;
                $flagGroup = true;
            }

            $row[0] = '<div class="text-center">'.$start.'</div>';
            $row[1] = '<div class="text-center">'.date_format(date_create($aRow['date']), 'd/m/Y').'</div>';
            $row[2] = '<div class="text-center">'._d($aRow['date_shipping']).'</div>';
            $row[3] = '<div class="text-center">'.($aRow['reference_no']).'</div>';
            $row[4] = '<div class="text-center">'.$aRow['item_code'].'</div>';
            $row[5] = '<div class="text-center">'.$aRow['item_name'].'</div>';
            $row[6] = '<div class="text-center">'.formatNumber($aRow['quantity_order']).'</div>';
            $row[7] = '<div class="text-center">'.formatNumber($aRow['quantity_manufactures']).'</div>';

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

            $row[8] = '<div class="text-center">'.formatNumber($quantityNew,0).'</div>';
            $row[9] = '<div class="text-center">'.formatNumber($aRow['quantity_warehoused']).'</div>';
            $row[10] = '<div class="text-center">'.formatNumber($aRow['quantity_errors']).'</div>';

            $status_new = '';
            if ($aRow['status_new'] == 1){
                $status_new = '<div class="label label-success">Hoàn thành</div>';
            } else {
                $status_new = '<div class="label label-info">Đang sản xuất</div>';
            }

            $row[11] = $status_new;

            $stage_name = $aRow['stage_name'];
            $htmlProcessin = '';
            if (!empty($stage_name)) {
                $stage_name = array_unique(explode(',', $stage_name));
                $htmlProcessin = implode('</br>', $stage_name);
            }
            $row[12] = $htmlProcessin;

            $_quantity_order+= $aRow['quantity_order'];
            $_quantity_manufactures+= $aRow['quantity_manufactures'];
            $_quantity_pager+= $quantityNew;
            $_quantity_warehoused+= $aRow['quantity_warehoused'];
            $_quantity_errors+= $aRow['quantity_errors'];
            // $totalQuantity+= $aRow['quantity'];
            $output['aaData'][] = $row;
        }

        $output['_quantity_order']= $_quantity_order;
        $output['_quantity_manufactures']= $_quantity_manufactures;
        $output['_quantity_pager']= $_quantity_pager;
        $output['_quantity_warehoused']= $_quantity_warehoused;
        $output['_quantity_errors']= $_quantity_errors;

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }

    public function getSalesReport() {

        $year_search = $this->input->post('year_search');
        //lấy ngày bắt đầu và kết thúc của year_search
        $start_date = $year_search . '-01-01 00:00:00';
        $end_date = $year_search . '-12-31 23:59:59';

        $customers = $this->input->post('customers');

        $tbOrders = "(
            SELECT
                tbl_deliveries.customer_id as customer_id,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '01', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_01,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '02', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_02,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '03', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_03,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '04', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_04,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '05', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_05,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '06', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_06,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '07', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_07,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '08', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_08,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '09', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_09,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '10', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_10,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '11', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_11,
                SUM(IF (DATE_FORMAT(tbl_deliveries.date, '%m') = '12', tbl_deliveries.grand_total * tbl_orders.amount_to_vnd, 0)) as total_12
            FROM tbl_deliveries
            JOIN `tbl_orders` ON `tbl_orders`.`id` = tbl_deliveries.`order_id`
            WHERE tbl_deliveries.date >= '$start_date' AND tbl_deliveries.date <= '$end_date' AND  EXISTS (
                    SELECT 1
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
            )
            GROUP BY tbl_deliveries.customer_id
        ) tb_orders";

        // print_arrays($tbOrders);
        $aColumns = [
            'tblclients.userid as userid',
            'tblclients.company as company',
            'tb_orders.total_01 as total_01',
            'tb_orders.total_02 as total_02',
            'tb_orders.total_03 as total_03',
            'tb_orders.total_04 as total_04',
            'tb_orders.total_05 as total_05',
            'tb_orders.total_06 as total_06',
            'tb_orders.total_07 as total_07',
            'tb_orders.total_08 as total_08',
            'tb_orders.total_09 as total_09',
            'tb_orders.total_10 as total_10',
            'tb_orders.total_11 as total_11',
            'tb_orders.total_12 as total_12',
        ];
        $sIndexColumn = 'userid';
        $sTable       = 'tblclients';
        $where        = [

        ];

        if (!empty($customers)) {
            array_push($where, ' AND tblclients.userid = '.$customers.'');
        }

        $filter = [];
        $join = [
            'INNER JOIN '.$tbOrders.' ON tb_orders.customer_id = tblclients.userid'
        ];
       
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);
        
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');

        $_total_01 = 0;
        $_total_02 = 0;
        $_total_03 = 0;
        $_total_04 = 0;
        $_total_05 = 0;
        $_total_06 = 0;
        $_total_07 = 0;
        $_total_08 = 0;
        $_total_09 = 0;
        $_total_10 = 0;
        $_total_11 = 0;
        $_total_12 = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = [];
            $row[0] = '<div class="text-center">'.$start.'</div>';
            $row[1] = $aRow['company'];
            $row[2] = '<div class="text-right">'.formatMoney($aRow['total_01']).'</div>';
            $row[3] = '<div class="text-right">'.formatMoney($aRow['total_02']).'</div>';
            $row[4] = '<div class="text-right">'.formatMoney($aRow['total_03']).'</div>';
            $row[5] = '<div class="text-right">'.formatMoney($aRow['total_04']).'</div>';
            $row[6] = '<div class="text-right">'.formatMoney($aRow['total_05']).'</div>';
            $row[7] = '<div class="text-right">'.formatMoney($aRow['total_06']).'</div>';
            $row[8] = '<div class="text-right">'.formatMoney($aRow['total_07']).'</div>';
            $row[9] = '<div class="text-right">'.formatMoney($aRow['total_08']).'</div>';
            $row[10] = '<div class="text-right">'.formatMoney($aRow['total_09']).'</div>';
            $row[11] = '<div class="text-right">'.formatMoney($aRow['total_10']).'</div>';
            $row[12] = '<div class="text-right">'.formatMoney($aRow['total_11']).'</div>';
            $row[13] = '<div class="text-right">'.formatMoney($aRow['total_12']).'</div>';

            $_total_01+= $aRow['total_01'];
            $_total_02+= $aRow['total_02'];
            $_total_03+= $aRow['total_03'];
            $_total_04+= $aRow['total_04'];
            $_total_05+= $aRow['total_05'];
            $_total_06+= $aRow['total_06'];
            $_total_07+= $aRow['total_07'];
            $_total_08+= $aRow['total_08'];
            $_total_09+= $aRow['total_09'];
            $_total_10+= $aRow['total_10'];
            $_total_11+= $aRow['total_11'];
            $_total_12+= $aRow['total_12'];
            $output['aaData'][] = $row;
        }

        $output['_total_01'] = $_total_01;
        $output['_total_02'] = $_total_02;
        $output['_total_03'] = $_total_03;
        $output['_total_04'] = $_total_04;
        $output['_total_05'] = $_total_05;
        $output['_total_06'] = $_total_06;
        $output['_total_07'] = $_total_07;
        $output['_total_08'] = $_total_08;
        $output['_total_09'] = $_total_09;
        $output['_total_10'] = $_total_10;
        $output['_total_11'] = $_total_11;
        $output['_total_12'] = $_total_12;

        $output['title_excel'] = [handlingTitleExcel()['title']];
        echo json_encode($output);
    }
}