<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Synthetic_stage extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('tools_supplies_model');
        $this->perViewProductionsOrders = has_permission('manufactures_productions_orders', '', 'view');
        $this->perViewOwnProductionsOrders = has_permission('manufactures_productions_orders', '', 'view_own');
        $this->perAddProductionsOrders = has_permission('manufactures_productions_orders', '', 'create');
        $this->perEditProductionsOrders = has_permission('manufactures_productions_orders', '', 'edit');
        $this->perDeleteProductionsOrders = has_permission('manufactures_productions_orders', '', 'delete');
        $this->perApproveProductionsOrders = has_permission('manufactures_productions_orders', '', 'approve');
    }

    public function index(){
        $data['type_print'] = $this->products_model->getTypePrint();
        $data['title'] = lang('Lệnh sản xuất theo công đoạn');
        // $data['CategoryStages'] = recursiveCategoryStages();
        // $data['stage'] = get_table_where('tbl_stages',['id !=' => STAGES_MATERIAL]);
        $this->load->view('admin/synthetic_stage/index', $data);
    }

    public function getSyntheticstage(){
        if (!$this->perViewProductionsOrders && !$this->perViewOwnProductionsOrders) {
            accessDenied();
        }

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
        $type_stage = $this->input->post('type_stage');

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


        $tbDateDelivery = "(
           SELECT
               tbl_productions_orders_items.productions_orders_id as productions_orders_id,
               tbl_productions_orders_items.items_id as items_id,
               MIN(tbl_order_item_shippings.date_shipping) as date_shipping
           FROM tbl_productions_orders_items
           LEFT JOIN tbl_order_item_shippings ON tbl_order_item_shippings.order_item_id = tbl_productions_orders_items.production_plan_item_id AND object_item_type = 'orders'
           LEFT JOIN tbl_business_plan_items_date ON tbl_business_plan_items_date.business_plan_items_id = tbl_productions_orders_items.production_plan_item_id AND object_item_type = 'business_plan'
           GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_date_delivery";

        $tbDateExport = "(
           SELECT
               tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
               tbl_productions_orders_items_stages.date_active as date_active
           FROM tbl_productions_orders_items_stages
           WHERE tbl_productions_orders_items_stages.stage_id = '".STAGES_MATERIAL."' AND tbl_productions_orders_items_stages.date_active IS NOT NULL
           GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
        ) tb_date_export";

        // $tbDateDelivery = "(
        //    SELECT
        //        tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //        tbl_productions_orders_items.items_id as items_id,
        //        MIN(tbl_order_item_shippings.date_shipping) as date_shipping
        //    FROM tbl_order_item_shippings
        //    JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_order_item_shippings.order_item_id AND object_item_type = 'orders'
        //    GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_date_delivery";

        // $tbDateDelivery = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         tbl_productions_orders_items.items_id as items_id,
        //         MIN(tbl_productions_plan_details.date) as date_shipping
        //     FROM tbl_productions_plan_items
        //     INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
        //     JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
        //     WHERE tbl_productions_plan_items.is_preventive = 0
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        //  ) tb_date_delivery";

         $tbDateDelivery = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                MIN(tbl_productions_plan_details.date) as date_shipping
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_details ON tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id 
            WHERE tbl_productions_plan_items.is_preventive = 0
            GROUP BY tbl_productions_orders_items.productions_orders_id
         ) tb_date_delivery";

        // LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id AND tb_date_delivery.items_id = tbl_productions_orders_items.items_id
        // $tbProductionsOrderItems = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         tb_date_delivery.date_shipping as date_shipping,
        //         tb_date_export.date_active as date_export,
        //         tbl_productions_plan.note as note_plan,
        //         tbl_productions_orders_items.items_id as items_id,
        //         tbl_productions_orders_items.items_name as items_name,
        //         tbl_productions_orders_items.items_code as items_code,
        //         tbl_products.quantity_child_molds as quantity_child_molds,
        //         SUM(tbl_productions_orders_items.quantity) as quantity,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        //         tbl_productions_orders_items.plan_id as plan_id,
        //         SUM(tb_purchases_errors.quantity_errors) as quantity_errors
        //     FROM tbl_productions_orders_items
        //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        //     INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
        //     LEFT JOIN $tbDateExport ON tb_date_export.productions_orders_items_id = tbl_productions_orders_items.id 
        //     LEFT JOIN $tbPurchasesErrors ON tb_purchases_errors.productions_orders_details_id = tbl_productions_orders_details.id 
        //     LEFT JOIN $tbDateDelivery ON tb_date_delivery.productions_orders_id = tbl_productions_orders_items.productions_orders_id
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_production_order_item";

        // $tbProductionsOrderItems = "(
        //     SELECT
        //         tbl_productions_orders_items.productions_orders_id as productions_orders_id,
        //         GROUP_CONCAT(tbl_productions_orders_items.id) as _poi_id,
        //         GROUP_CONCAT(tbl_productions_orders_details.id) as _pod_id,
        //         '' as date_shipping,
        //         '' as date_export,
        //         tbl_productions_plan.note as note_plan,
        //         tbl_productions_orders_items.items_id as items_id,
        //         tbl_productions_orders_items.items_name as items_name,
        //         tbl_productions_orders_items.items_code as items_code,
        //         tbl_products.quantity_child_molds as quantity_child_molds,
        //         SUM(tbl_productions_orders_items.quantity) as quantity,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
        //         tbl_productions_orders_items.plan_id as plan_id,
        //         SUM(0) as quantity_errors
        //     FROM tbl_productions_orders_items
        //     INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
        //     INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
        //     INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
        //     GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        // ) tb_production_order_item";

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                GROUP_CONCAT(tbl_productions_orders_items.id) as _poi_id,
                GROUP_CONCAT(tbl_productions_orders_details.id) as _pod_id,
                '' as date_shipping,
                '' as date_export,
                tbl_productions_plan.note as note_plan,
                tbl_productions_orders_items.items_id as items_id,
                tbl_productions_orders_items.items_name as items_name,
                tbl_productions_orders_items.items_code as items_code,
                tbl_products.quantity_child_molds as quantity_child_molds,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                tbl_productions_orders_items.plan_id as plan_id,
                SUM(0) as quantity_errors
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            WHERE tbl_productions_orders.date >= '".to_sql_date($start_date_search)." 00:00:00' AND tbl_productions_orders.date <= '".to_sql_date($end_date_search)." 23:59:59'
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as reference_no',
            'tbl_products.code as items_code',
            'tbl_products.name as items_name',
            'tb_production_order_item.date_shipping as date_delivery',
            'tb_production_order_item.date_export as date_export',
            '"" as materials',
            '"" as vertical',
            '"" as number_child_sheet',
            'tb_production_order_item.quantity_child_molds as quantity_child_molds',
            '(tb_production_order_item.quantity) as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity as quantity',
            'tb_production_order_item.quantity_warehoused as quantity_finished',
            'tb_production_order_item.quantity_errors as quantity_errors',
            'tb_production_order_item.quantity_errors as quantity_errors',
            '"" as status',
            'tbl_category_stages.name as name_category_stage',
            'tbl_stages.name as name_tagert',
            'tb_production_order_item.note_plan as note'
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
            // 'LEFT JOIN '.$tbProductionsPlanOrdersByOrders.' ON tb_orders.productions_order_id = tbl_productions_orders.id',
            // 'LEFT JOIN '.$tbProductionsPlanOrdersByBusinessPlan.' ON tb_business_plan.productions_order_id = tbl_productions_orders.id',
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_category_stages ON tbl_category_stages.id = tbl_stages.category_stages',
        ];
        if (!$this->perViewProductionsOrders) {
            array_push($where, "AND tbl_productions_orders.created_by =".get_staff_user_id());
        }
        if (!empty($productions_orders_search)) {
            array_push($where, "AND tbl_productions_orders.id = ".$this->db->escape($productions_orders_search));
        }
        array_push($where, "AND tbl_stages.id != ".STAGES_MATERIAL);
        if (!empty($customer_search)){
            array_push($where, "AND EXISTS (
                 SELECT tbl_productions_orders_details.productions_orders_id
                 FROM tbl_productions_orders_details
                 JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = 'orders'
                 WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
                 AND tbl_orders.customer_id = $customer_search
             )");
        }
        if (!empty($type_stage)){
            array_push($where, "AND tbl_stages.id IN(".implode(',',$type_stage).")");
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
            // 'tb_orders.reference_no_orders as reference_no_orders',
            // 'tb_business_plan.reference_no_business_plan as reference_no_business_plan',
            'tbl_productions_orders.total_quantity as total_quantity',
            'tb_production_order_item.items_id as items_id',
            // 'tb_production_order_item.items_code as items_code',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by',
            'tbl_productions_orders.status_orders as status_orders',
            'tb_production_order_item.plan_id as plan_id',
            'tbl_category_stages.is_in as is_in',
            'GROUP_CONCAT(DISTINCT tb_production_order_item.items_id) as items_id',
            'tb_production_order_item._poi_id as _poi_id',
            'tb_production_order_item._pod_id as _pod_id',
        ], 'GROUP BY tbl_productions_orders.id,tbl_productions_orders_items_stages.stage_id', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        $total_quantity_sx = 0;
        $total_quantity_hold = 0;
        $total_quantity_new = 0;
        $total_quantity_finished = 0;
        $total_quantity_errors = 0;
        $_total_quantity_compensation = 0;
        $group_id = 0;

        $keyPodId = array_column($rResult, 'id');
        $keyPodId = array_unique($keyPodId);
        if (!empty($keyPodId)) {
            $query = '
                SELECT
                    tb_custom.po_id as po_id,
                    tb_custom.type_item as type_item,
                    tb_custom.item_id as item_id,
                    tb_custom.item_code as item_code,
                    tb_custom.item_name as item_name
                FROM (
                    SELECT
                        tbl_suggest_exporting.po_id as po_id,
                        tbl_suggest_exporting_items.type_item as type_item,
                        tbl_suggest_exporting_items.item_id as item_id,
                        IF (tbl_materials.code IS NOT NULL, tbl_materials.code, tbl_products.code) as item_code,
                        IF (tbl_materials.name IS NOT NULL, tbl_materials.name, tbl_products.name) as item_name
                    FROM tbl_suggest_exporting
                    INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                    LEFT JOIN tbl_products ON tbl_products.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "semi_products"
                    LEFT JOIN tbl_materials ON tbl_materials.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "materials"
                    WHERE tbl_suggest_exporting.po_id IN ('.implode(',', $keyPodId).')

                    UNION ALL

                    SELECT
                        tbl_productions_orders_details.productions_orders_id as po_id,
                        tbl_suggest_exporting_items.type_item as type_item,
                        tbl_suggest_exporting_items.item_id as item_id,
                        IF (tbl_materials.code IS NOT NULL, tbl_materials.code, tbl_products.code) as item_code,
                        IF (tbl_materials.name IS NOT NULL, tbl_materials.name, tbl_products.name) as item_name
                    FROM tbl_suggest_exporting
                    INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id
                    INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id
                    LEFT JOIN tbl_products ON tbl_products.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "semi_products"
                    LEFT JOIN tbl_materials ON tbl_materials.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "materials"
                    WHERE tbl_productions_orders_details.productions_orders_id IN ('.implode(',', $keyPodId).')
                ) tb_custom
                WHERE tb_custom.item_name != ""
                GROUP BY tb_custom.po_id, tb_custom.type_item, tb_custom.item_id
            ';
            $sugItems = $this->db->query($query)->result_array();
            $sugItems = array_reduce($sugItems, function ($carry, $item) {
                $id = $item['po_id'];
                if (!isset($carry[$id])) {
                    $carry[$id] = [];
                }
                $carry[$id][] = $item;
                return $carry;
            }, []);
        }

        // print_arrays($keyPodId);

        foreach ($rResult as $key => $aRow) {
            $start++;
            $productions_orders_id = $aRow['id'];
            $items_id = $aRow['items_id'];
            $status_details = $aRow['status_details'];
            $row[0] = '<div class="text-center">'.$start.'</div>';
            $row[1] = _dhau($aRow['date']);
            $row[2] = '<div class="bold"><a target="_blank" href="'.base_url('admin/manufactures/detail_productions_orders/'.$productions_orders_id).'">'.$aRow['reference_no'].'</a></div>';

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
                WHERE tbl_productions_plan_items.is_preventive = 0 AND tbl_productions_orders_items.items_id IN ($items_id) AND tbl_productions_orders_items.productions_orders_id = $productions_orders_id
            )";
            $_query = $this->db->query($tbDateDelivery)->row_array();
            $aRow['date_delivery'] = !empty($_query['date_shipping']) ? $_query['date_shipping'] : false;

            $flagGroup = false;
            if ($group_id != $productions_orders_id && $aRow['is_in']) {
                $group_id = $productions_orders_id;
                $flagGroup = true;
            }

            $this->db->select('
                tbl_products.code as product_code,
                tbl_products.name as product_name,
                GROUP_CONCAT(DISTINCT ppb_materials.landscape_print_size SEPARATOR "<br>") as landscape_print_size,
                GROUP_CONCAT(DISTINCT ppb_materials.number_children_size SEPARATOR "<br>") as number_children_size,
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
            // $quantityNew = $dtQuantityNew['paper_exchange'];

            $plan_id = $aRow['plan_id'];
            if($aRow['is_in'] == 1){
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
            } else {
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
            }
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            // $this->db->where('(ppb_materials.item_type)', 'materials');
            $this->db->where('(tbl_productions_orders_items.items_id IN ('.$items_id.'))');

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
            $total_quantity_compensation = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
//                    if ($flagGroup == true) {
//                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
//                        $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
//                    } else {
//                        $quantity_compensation = 0;
//                    }
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];

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

                    $quantity_compensation = $quantity_compensation > 0 ? ceil($quantity_compensation/$quantity_single) : 0;
                    $total_quantity_compensation+= $quantity_compensation;
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
            $row[5] = '<div class="text-left">'.(!empty($aRow['date_delivery']) ? _dhau($aRow['date_delivery']) : '').'</div>';
            $row[6] = '<div class="text-left">'.(!empty($aRow['date_export']) ? _dhau($aRow['date_export']) : '').'</div>';

            // $this->db->select('
            //     tbl_suggest_exporting_items.type_item as type_item,
            //     tbl_suggest_exporting_items.item_id as item_id,
            //     IF (tbl_materials.code IS NOT NULL, tbl_materials.code, tbl_products.code) as item_code,
            //     IF (tbl_materials.name IS NOT NULL, tbl_materials.name, tbl_products.name) as item_name,
            // ', false);
            // $this->db->from('tbl_suggest_exporting');
            // $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
            // $this->db->join('tbl_products', 'tbl_products.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "semi_products"', 'left');
            // $this->db->join('tbl_materials', 'tbl_materials.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "materials"', 'left');
            // $this->db->where('(
            //     tbl_suggest_exporting.po_id = '.$productions_orders_id.'
            //     OR exists (
            //         SELECT tbl_productions_orders_details.id
            //         FROM tbl_productions_orders_details
            //         WHERE tbl_productions_orders_details.id = tbl_suggest_exporting.productions_orders_details_id AND tbl_productions_orders_details.productions_orders_id = '.$productions_orders_id.'
            //     )
            // )', false, false);
            // $this->db->where_in('tbl_suggest_exporting_items.type_item', ['semi_products', 'materials']);
            // $this->db->group_by('tbl_suggest_exporting_items.type_item, tbl_suggest_exporting_items.item_id');
            // $sugItems = $this->db->get()->result_array();
            

            $strItem = '';
            $sugItemsTemp = !empty($sugItems[$productions_orders_id]) ? $sugItems[$productions_orders_id] : null;
            // if (!empty($sugItems)) {
            //     foreach ($sugItems as $kS => $vS) {
            //         $strItem.= '<div>'.$vS['item_code'].', </div>';
            //     }
            // }

            if (!empty($sugItemsTemp)) {
                foreach ($sugItemsTemp as $kS => $vS) {
                    $strItem.= '<div>'.$vS['item_code'].', </div>';
                }
            }
            $row[7] = $strItem;
            $row[8] = '<div class="text-center">'.($dtQuantityNew['landscape_print_size']).'</div>';
            $row[9] = '<div class="text-center">'.($dtQuantityNew['number_children_size']).'</div>';

            $row[10] = '<div class="text-center">'.($aRow['quantity_child_molds']).'</div>';

            $this->db->select('SUM(quantity) as quantity');
            $this->db->from('tbl_productions_orders_items');
            $this->db->where('productions_orders_id',$productions_orders_id);
            $this->db->where('items_id IN ('.$items_id.')');
            $this->db->where('object_item_type','business_plan');
            $quantityDp = $this->db->get()->row_array()['quantity'];


            $this->db->select('SUM(quantity) as quantity,SUM(quantity_warehoused) as quantity_warehoused');
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_productions_orders_details','tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
            $this->db->where('tbl_productions_orders_items.productions_orders_id',$productions_orders_id);
            $this->db->where('tbl_productions_orders_items.items_id IN ('.$items_id.')');
            $this->db->where('object_item_type','orders');
            $quantityAll = $this->db->get()->row_array();

            $row[11] = '<div class="text-center">'.formatNumber($quantityAll['quantity']).'</div>';

            $row[12] = '<div class="text-center">'.formatNumber($quantityAll['quantity'] + $quantityDp).'</div>';

            $this->db->select('SUM(quantity_net) as quantity');
            $this->db->from('tbltransfer_warehouse');
            $this->db->join('tbltransfer_warehouse_detail','tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id');
            $this->db->where('tbltransfer_warehouse_detail.id_items IN ('.$items_id.')');
            $this->db->where('tbltransfer_warehouse.order_id_new != ',0);
            $this->db->where('tbltransfer_warehouse_detail.type','product');
            $quantityHold = $this->db->get()->row_array()['quantity'];

            $row[13] = '<div class="text-center">'.formatNumber($quantityHold).'</div>';



            $row[14] = '<div class="text-center">'.formatNumber($quantityNew,0).'</div>';

            $row[15] = '<div class="text-center">'.formatNumber($total_quantity_compensation).'</div>';

            $row[16] = '<div class="text-center">'.formatNumber($quantityAll['quantity_warehoused']).'</div>';


            $this->db->select('SUM(tbl_purchase_products.total_quantity) as quantity');
            $this->db->from('tbl_purchase_products');
            $this->db->join('tbl_productions_orders_details','tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id');
            $this->db->join('tbl_productions_orders_items','tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.productions_orders_id',$productions_orders_id);
            $this->db->where('tbl_productions_orders_items.items_id IN ('.$items_id.')');
            $this->db->where('tbl_purchase_products.is_errors',1);
            $quantityErrors = $this->db->get()->row_array()['quantity'];


            $row[17] = '<div class="text-center">'.formatNumber($quantityErrors).'</div>';
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
            $row[19] = trim($htmlProcessin,',<br>');
            $row[20] = $aRow['name_category_stage'];
            $row[21] = $aRow['name_tagert'];
			$row[22] = '<div class="italic">'.$aRow['location_name'].'</div>';
            $row[23] = $aRow['note'];

            //items
            // $productions_orders_id
            $statusAuto = 1;
            $isNotProducedYet = 0;
            $isProduction = 0;
            $isFinished = 0;
            $isCancel = 0;
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
                tbl_productions_orders_details.price_costing as price_costing
            ');
            $this->db->from('tbl_productions_orders_items');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id', 'left');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
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
                        }
                    }
                }
            }
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
            $row[18] = '<div class="text-center">'.$strStatus.'</div>';
            $output['aaData'][] = $row;
            $total_quantity += $aRow['quantity'] - $quantityDp;
            $total_quantity_sx += $aRow['quantity'];
            $total_quantity_hold += $quantityHold;
            $total_quantity_new += $quantityNew;
            $total_quantity_finished+= $aRow['quantity_finished'];
            $total_quantity_errors+= $aRow['quantity_errors'];
            $_total_quantity_compensation+= $total_quantity_compensation;
        }
        $output['total_quantity'] = $total_quantity;
        $output['total_quantity_sx'] = $total_quantity_sx;
        $output['total_quantity_hold'] = $total_quantity_hold;
        $output['total_quantity_new'] = formatNumber($total_quantity_new,0);
        $output['total_quantity_finished'] = $total_quantity_finished;
        $output['total_quantity_errors'] = $total_quantity_errors;
        $output['_total_quantity_compensation'] = $_total_quantity_compensation;
        echo json_encode($output);
    }

    public function loadTypePrintAndStages() {
        $data = [];

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $productions_orders_search = $this->input->post('productions_orders_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $orders_search = $this->input->post('orders_search');
        $business_plan_search = $this->input->post('business_plan_search');
        $orders_and_business_plan = $this->input->post('orders_and_business_plan');
        $type_print_search = $this->input->post('type_print_search');
        $customer_search = $this->input->post('customer_search');
        $type_stage = $this->input->post('type_stage');
        $this->db->simple_query('SET SESSION group_concat_max_len=18446744073709551615');

        $period_time = $this->input->post('period_time');
        if (!empty($period_time)) {
            $period_time = explode('-', $period_time);
            $start_date_search = trim($period_time[0]);
            $end_date_search = trim($period_time[1]);
        }

        $this->db->select('
            GROUP_CONCAT(distinct tbl_stages.category_stages) as category_stages,
            GROUP_CONCAT(distinct tbl_productions_orders_items_stages.stage_id) as stage_id
        ');
        $this->db->from('tbl_productions_orders');
        $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id', 'inner');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');

        if (!$this->perViewProductionsOrders) {
            $this->db->where("tbl_productions_orders.created_by =".get_staff_user_id(), false, false);
        }

        if (!empty($productions_orders_search)) {
            $this->db->where("tbl_productions_orders.id = ".$productions_orders_search, false, false);
        }

        $this->db->where("tbl_stages.id != ".STAGES_MATERIAL, false, false);

        if (!empty($customer_search)){
            $this->db->where("EXISTS (
                 SELECT tbl_productions_orders_details.productions_orders_id
                 FROM tbl_productions_orders_details
                 JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = 'orders'
                 WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
                 AND tbl_orders.customer_id = $customer_search
             )", false, false);
        }

        if (!empty($item_id)) {
            $item_id = $this->db->escape($item_id);
            $this->db->where("EXISTS (
                SELECT tbl_productions_orders_items.id
                FROM tbl_productions_orders_items
                WHERE tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id AND tbl_productions_orders_items.items_id = $item_id
            )", false, false);
        }

        if (!empty($orders_search)) {
            $orders_search = $this->db->escape($orders_search);
            $this->db->where("EXISTS (
                SELECT tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.productions_plan_id = $orders_search AND tbl_productions_plan_orders.object_type = 'orders'
            )", false, false);
        }

        if (!empty($business_plan_search)) {
            $business_plan_search = $this->db->escape($business_plan_search);
            $this->db->where("EXISTS (
                SELECT tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.productions_plan_id = $business_plan_search AND tbl_productions_plan_orders.object_type = 'business_plan'
            )", false, false);
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
            $this->db->where("tbl_productions_orders.date >= '$start_date_search'", false, false);
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
            $this->db->where("tbl_productions_orders.date <= '$end_date_search'", false, false);
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            $this->db->where('tbl_productions_orders.location_id IN ('.implode(',', $branch_staff).')', false, false);
        }
        // $this->db->group_by('tbl_stages.category_stages, tbl_productions_orders_items_stages.stage_id');
        // print_arrays($this->db->get_compiled_select());
        $rs = $this->db->get()->row_array();
        // print_arrays($rs);

        $option_stages = '';
        $option_type_print = '';
        if (!empty($rs)) {
            $category_stages = explode(',', $rs['category_stages']);
            $stage_id = explode(',', $rs['stage_id']);

            $this->db->select('
                tbl_stages.id as id,
                tbl_stages.name as name,
            ', false);
            $this->db->from('tbl_stages');
            $this->db->where_in('tbl_stages.id', $stage_id);
            $stages = $this->db->get()->result_array();

            if (!empty($stages)) {
                foreach ($stages as $key => $value) {
                    $option_stages.= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
                }
            }

            $type_print = recursiveCategoryStages($category_stages);
            $option_type_print = $type_print;
        }

        $data['option_stages'] = $option_stages;
        $data['option_type_print'] = $option_type_print;
        echo json_encode($data);
    }
}