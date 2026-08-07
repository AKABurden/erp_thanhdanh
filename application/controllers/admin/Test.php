<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test extends AdminController
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
    }

    public function phpinfo() {
        echo phpinfo();
    }

    public function index() {

        $quantity_put = "(
            SELECT

            FROM tbl_delivery_items_columns
            WHERE tbl_delivery_items_columns.columns_value = 'quantity_put' AND tbl_delivery_items_columns.delivery_item_id = tbl_delivery_items.id 
        )";

        $this->db->select('
            tbl_deliveries.id as id_delivery,
            tbl_deliveries.date as date,
            tbl_deliveries.customer_name as customer_name
        ', false);
        $this->db->from('tbl_deliveries');
        // $this->db->join('tbl_delivery_items', 'tbl_delivery_items.delivery_id = tbl_deliveries.id');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_deliveries.order_id');
        // $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
        $this->db->where_in('tbl_orders.type_orders', [2, 4, 11]);
        $this->db->where('tbl_deliveries.date >=', '2022-12-01 00:00:00');
        $this->db->where('tbl_deliveries.date <=', '2022-12-26 23:59:59');
        $this->db->limit(40);
        $items = $this->db->get()->result_array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $id_delivery = $value['id_delivery'];
                $tb_tamp = '(
                    SELECT 
                        (tb_tamp.delivery_item_id) as delivery_item_id,
                        (tb_tamp.order_code) as order_code,
                        (tb_tamp.command) as command,
                        SUM(tb_tamp.quantity_put) as quantity_put,
                        SUM(tb_tamp.sample_quantity_item) as sample_quantity_item,
                        SUM(tb_tamp.quantity_loss) as quantity_loss
                    FROM (
                        SELECT
                            counter_items_number as counter_items_number,
                            delivery_item_id as delivery_item_id,
                            MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_loss",tbl_delivery_items_columns.columns_name,"")) as "quantity_loss",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "sample_quantity_item",tbl_delivery_items_columns.columns_name,"")) as "sample_quantity_item",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "quantity_put",tbl_delivery_items_columns.columns_name,"")) as "quantity_put",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "order_code",tbl_delivery_items_columns.columns_name,"")) as "order_code",
                            MAX(IF(tbl_delivery_items_columns.columns_value = "command",tbl_delivery_items_columns.columns_name,"")) as "command"
                        FROM `tbl_delivery_items_columns`
                        WHERE tbl_delivery_items_columns.delivery_id = '.$id_delivery.'
                        GROUP BY counter_items_number,delivery_item_id
                    ) tb_tamp
                    GROUP BY tb_tamp.order_code,tb_tamp.command,tb_tamp.delivery_item_id  
                ) as tb_tamp';

                $this->db->select('
                    tbl_delivery_items.item_id as item_id,
                    tbl_delivery_items.item_code as item_code,
                    tb_tamp.order_code as code,
                    tb_tamp.command as command,
                    tbl_order_items.product_name_customer as item_name,
                    tbl_delivery_items.price as price,
                    SUM(tb_tamp.quantity_put) as quantity
                ');
                $this->db->from('tbl_delivery_items');
                $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
                $this->db->join($tb_tamp, 'tb_tamp.delivery_item_id = tbl_delivery_items.id');
                $this->db->where('tbl_delivery_items.delivery_id', $value['id_delivery']);
                $this->db->group_by('tbl_delivery_items.item_id, tb_tamp.command, tb_tamp.order_code');
                $delivery_items = $this->db->get()->result_array();
                // print_arrays($this->db->last_query());

                $items[$key]['delivery_items'] = $delivery_items;
            }
        }
        print_arrays($items);
    }

    public function getManufacturesPlan()
    {

        // die;
        $dateNow = date('Y-m-d');
        $this->db->simple_query('SET SESSION group_concat_max_len=18446744073709551615');

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $customer_search_manufactures = $this->input->post('customer_search_manufactures');
        $start_date_search_manufactures = $this->input->post('start_date_search_manufactures');
        $end_date_search_manufactures = $this->input->post('end_date_search_manufactures');

        $start_date_search_manufactures = '2022-10-20';
        $end_date_search_manufactures = '2024-04-20';

        $products_search_manufactures = $this->input->post('products_search_manufactures');
        $category_product_search_manufactures = $this->input->post('category_product_search_manufactures');
        $type_orders_search_manufactures = $this->input->post('type_orders_search_manufactures');
        $search_date_order_manufactures = $this->input->post('search_date_order_manufactures');
        $type_view_search_manufactures = $this->input->post('type_view_search_manufactures');
        $products_text_search_manufactures = $this->input->post('products_text_search_manufactures');

        $whereOrders = '';
        $whereBusinessPlan = '';
        if ($customer_search_manufactures) {
            $whereOrders .= ' AND tbl_orders.customer_id = ' . $customer_search_manufactures;
            $whereBusinessPlan .= ' AND tbl_business_plan.id = 0';
        }

        if (!empty($type_orders_search_manufactures)) {
            $whereOrders .= ' AND tbl_orders.type_orders IN (' . $type_orders_search_manufactures . ')';
        }

        if (!empty($search_date_order_manufactures)) {
            $search_date_order_manufactures = explode(' - ', $search_date_order_manufactures);
            $search_date_order_manufactures_start = to_sql_date($search_date_order_manufactures[0]) . ' 00:00:00';
            $search_date_order_manufactures_end = to_sql_date($search_date_order_manufactures[1]) . ' 23:59:59';

            $whereOrders .= ' AND tbl_orders.date >= "' . $search_date_order_manufactures_start . '" AND tbl_orders.date <= "' . $search_date_order_manufactures_end . '" ';
        }

        // AND (tbl_order_item_shippings.quantity_shipping - tbl_order_item_shippings.quantity_plan_item - tbl_order_items.quantity_delivery) >= 0

        $slKeep = "COALESCE((
            SELECT SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id AND tbltransfer_warehouse_detail.tranfer_business_item_id = 0
        ), 0)";

        // SUM((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - $slKeep) as total_quantity_item,

        // $tbProductionsOrdersDetailOrders = '(
        //     SELECT
        //         tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_po
        //     FROM tbl_productions_orders_details
        //     INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        //     WHERE tbl_productions_orders_details.object_type = "orders" AND tbl_productions_orders_details.quantity_warehoused > 0
        //     GROUP BY tbl_productions_orders_items.production_plan_item_id
        // ) tb_productions_orders_detail';

        // $tbProductionsOrdersDetailBusiness = '(
        //     SELECT
        //         tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_po
        //     FROM tbl_productions_orders_details
        //     INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        //     WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_productions_orders_details.quantity_warehoused > 0
        //     GROUP BY tbl_productions_orders_items.production_plan_item_id
        // ) tb_productions_orders_detail';

        // AND NOT exists (
        //     SELECT tbl_orders.id
        //     FROM tbl_orders
        //     WHERE tbl_productions_orders_details.object_type = "orders" AND tbl_orders.is_cancel = 0 AND tbl_productions_orders_details.object_id = tbl_orders.id
        // )
            
        // $tbProductionsOrdersDetailNotPreventive = '(
        //     SELECT
        //         tbl_productions_orders_items.items_id as items_id,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_po
        //     FROM tbl_productions_orders_details
        //     INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        //     WHERE tbl_productions_orders_details.quantity_warehoused > 0 AND NOT exists (
        //         SELECT tbl_business_plan.id
        //         FROM tbl_business_plan
        //         WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0 AND tbl_productions_orders_details.object_id = tbl_business_plan.id
        //     )
        //     GROUP BY tbl_productions_orders_items.items_id
        // ) tb_productions_orders_detail';

        // $tbProductionsOrdersDetailPreventive = '(
        //     SELECT
        //         tbl_productions_orders_items.items_id as items_id,
        //         SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_po
        //     FROM tbl_productions_orders_details
        //     INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        //     INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_orders_details.object_id
        //     WHERE tbl_productions_orders_details.quantity_warehoused > 0 AND tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0
        //     GROUP BY tbl_productions_orders_items.items_id
        // ) tb_productions_orders_detail_perventive';

        $tbProductionsOrdersDetailNotPreventive = '(
            SELECT
                tbl_productions_orders_items.items_id as items_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_po
            FROM tbl_productions_orders_details
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
            INNER JOIN  tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            WHERE NOT exists (
                SELECT tbl_business_plan.id
                FROM tbl_business_plan
                WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0 AND tbl_productions_orders_details.object_id = tbl_business_plan.id
            ) AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1) AND tbl_purchase_products.warehouseman_id > 0
            GROUP BY tbl_productions_orders_items.items_id
        ) tb_productions_orders_detail';

        $tbProductionsOrdersDetailPreventive = '(
            SELECT
                tbl_productions_orders_items.items_id as items_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_po
            FROM tbl_productions_orders_details
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
            INNER JOIN  tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_orders_details.object_id
            WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0 AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1) AND tbl_purchase_products.warehouseman_id > 0
            GROUP BY tbl_productions_orders_items.items_id 
        ) tb_productions_orders_detail_perventive';

        $keepTranferBusinessItem = 'COALESCE((
            SELECT
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            WHERE tbl_tranfer_business_item.order_item_id = tbl_order_items.id
        ), 0)';

        $tbSlKeep = "(
            SELECT 
                tbltransfer_warehouse_detail.order_id_item as order_id_item,
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.tranfer_business_item_id = 0
            GROUP BY tbltransfer_warehouse_detail.order_id_item
        ) as tb_keep";

        $tbkeepTranferBusinessItem = '(
            SELECT
                tbl_tranfer_business_item.order_item_id as order_item_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            GROUP BY tbl_tranfer_business_item.order_item_id
        ) as tb_keep_trans';

        // $slKeep
        //$keepTranferBusinessItem
        $tbOrdersItems = "(
            SELECT
                tb_items.item_id as item_id,
                SUM(tb_items.total_quantity_item) as total_quantity_item,
                SUM(tb_items.quantity_plan) as quantity_plan,
                GROUP_CONCAT(tb_items.order_detail SEPARATOR 'AAA') as order_detail
            FROM (
                (
                    SELECT
                        tbl_order_items.item_id as item_id,
                        SUM(IF((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - COALESCE(tb_keep.quantity_net, 0) - COALESCE(tb_keep_trans.quantity, 0) > 0, (tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - COALESCE(tb_keep.quantity_net, 0) - COALESCE(tb_keep_trans.quantity, 0), 0)) as total_quantity_item,

                        SUM(tbl_order_items.quantity_plan) as quantity_plan,
                        GROUP_CONCAT(tbl_orders.reference_no,'|||',((tbl_order_item_shippings.quantity_shipping * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_item_shippings.quantity_plan_item - (COALESCE(tb_keep.quantity_net, 0)) - COALESCE(tb_keep_trans.quantity, 0))  SEPARATOR 'FF') as order_detail
                    FROM tbl_orders
                    INNER JOIN tbl_order_items ON tbl_order_items.order_id = tbl_orders.id
                    INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id
                    INNER JOIN tbl_order_item_shippings ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
                    LEFT JOIN $tbSlKeep ON tb_keep.order_id_item = tbl_order_items.id
                    LEFT JOIN $tbkeepTranferBusinessItem ON tb_keep_trans.order_item_id = tbl_order_items.id
                    WHERE tbl_order_items.type_item = 'products' AND tbl_order_items.total_quantity_item > 0 AND tbl_order_item_shippings.date_shipping >= '$start_date_search_manufactures' AND tbl_order_item_shippings.date_shipping <= '$end_date_search_manufactures' AND tbl_orders.status = 'approved' $whereOrders AND tbl_orders.is_cancel = 0
                    GROUP BY tbl_order_items.item_id
                )
                UNION ALL
                (
                    SELECT
                        tbl_business_plan_items.items_id as item_id,
                        SUM(tbl_business_plan_items.quantity - tbl_business_plan_items.quantity_plan) as total_quantity_item,
                        SUM(tbl_business_plan_items.quantity_plan) as quantity_plan,
                        GROUP_CONCAT(tbl_business_plan.reference_no,'|||',(tbl_business_plan_items_date.quantity - tbl_business_plan_items_date.quantity_plan_item)  SEPARATOR 'FF') as order_detail
                    FROM tbl_business_plan
                    INNER JOIN tbl_business_plan_items ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
                    INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
                    WHERE tbl_business_plan_items.quantity > 0 AND tbl_business_plan.productions_plan_preventive_id = 0 AND tbl_business_plan_items_date.date >= '$start_date_search_manufactures' AND tbl_business_plan_items_date.date <= '$end_date_search_manufactures' AND tbl_business_plan.status = 'approved' $whereBusinessPlan
                    GROUP BY tbl_business_plan_items.items_id
                )
            ) tb_items
            GROUP BY tb_items.item_id
        ) tb_sum_items";
        print_arrays($tbOrdersItems);

        $tbPreventive = "(
            SELECT
                tbl_business_plan_items.items_id as item_id,
                SUM(tbl_business_plan_items.quantity) as total_quantity_item
            FROM tbl_business_plan
            INNER JOIN tbl_business_plan_items ON tbl_business_plan_items.business_plan_id = tbl_business_plan.id
            WHERE tbl_business_plan.productions_plan_preventive_id > 0 AND (
                exists (
                    SELECT 
                        tbl_productions_plan_object.object_id
                    FROM tbl_productions_plan_object
                    INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_object.object_id
                    INNER JOIN tbl_order_items ON tbl_order_items.order_id = tbl_orders.id
                    INNER JOIN tbl_order_item_shippings ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
                    WHERE tbl_productions_plan_object.productions_plan_id = tbl_business_plan.productions_plan_preventive_id AND tbl_productions_plan_object.object_type = 'orders' AND tbl_order_items.type_item = 'products' AND tbl_order_items.total_quantity_item > 0 AND tbl_order_item_shippings.date_shipping >= '$start_date_search_manufactures' AND tbl_order_item_shippings.date_shipping <= '$end_date_search_manufactures'  AND tbl_orders.status = 'approved' AND tbl_order_items.item_id = tbl_business_plan_items.items_id
                )
                OR exists (
                    SELECT 
                        tbl_productions_plan_object.object_id
                    FROM tbl_productions_plan_object
                    INNER JOIN tbl_business_plan bp ON bp.id = tbl_productions_plan_object.object_id
                    INNER JOIN tbl_business_plan_items bpi ON bp.id = bpi.business_plan_id
                    INNER JOIN tbl_business_plan_items_date bpid ON bpi.id = bpid.business_plan_items_id
                    WHERE tbl_productions_plan_object.productions_plan_id = tbl_business_plan.productions_plan_preventive_id AND tbl_productions_plan_object.object_type = 'business_plan' AND bpid.date >= '$start_date_search_manufactures' AND bpid.date <= '$end_date_search_manufactures' AND bp.status = 'approved' AND bpi.items_id = tbl_business_plan_items.items_id
                )
            )
            GROUP BY tbl_business_plan_items.items_id
        ) tb_perventive";

        // AND NOT EXISTS (
        //     SELECT 1
        //     FROM tbl_productions_orders_items
        //     JOIN tbl_tranfer_business_item ON tbl_tranfer_business_item.business_plan_item_id = tbl_productions_orders_items.production_plan_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'
        //     WHERE tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
        // )

        $tranfer_business_plan = "(
            SELECT
                tbl_productions_orders_details.id as pod_id,
                tbl_productions_orders_items.items_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity_business
            FROM tbl_tranfer_business_item
            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.production_plan_item_id = tbl_tranfer_business_item.business_plan_item_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            WHERE tbl_productions_orders_items.object_item_type = 'business_plan'
            GROUP BY tbl_productions_orders_details.id, tbl_productions_orders_items.items_id
        ) tb_tranfer_business_plan";

        $sumW = "(
            SELECT
                tb_css.id_items as id_items,
                SUM(tb_css.product_quantity) as product_quantity,
                SUM(tb_tranfer_business_plan.quantity_business) as quantity_business
            FROM (
                SELECT
                    tbllocaltion_warehouses.pod_id as pod_id,
                    tblwarehouse_items.id_items as id_items,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity
                FROM tbllocaltion_warehouses
                INNER JOIN tblwarehouse_items ON tblwarehouse_items.localtion = tbllocaltion_warehouses.id
                WHERE tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_CAPACITY . ", " . WAREHOUSES_HOLD . ", ".WAREHOUSES_ERRORS.") AND tblwarehouse_items.product_quantity > 0 AND tblwarehouse_items.type_items = 'product'
                GROUP BY tbllocaltion_warehouses.pod_id, tblwarehouse_items.id_items
            ) tb_css
            INNER JOIN $tranfer_business_plan ON tb_tranfer_business_plan.pod_id = tb_css.pod_id
            GROUP BY tb_css.id_items
        )";

        // $quantityW = "(
        //     SELECT
        //         tblwarehouse_items.id_items as id_items,
        //         SUM(IF (tblwarehouse_items.product_quantity - coalesce(tb_tranfer_business_plan.quantity_business, 0) >= 0, tblwarehouse_items.product_quantity - coalesce(tb_tranfer_business_plan.quantity_business, 0), tblwarehouse_items.product_quantity)) as quantity_warehouse
        //     FROM tblwarehouse_items
        //     INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
        //     LEFT JOIN $tranfer_business_plan ON tb_tranfer_business_plan.pod_id = tbllocaltion_warehouses.pod_id AND tblwarehouse_items.id_items = tb_tranfer_business_plan.items_id

        //     WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_CAPACITY . ", " . WAREHOUSES_HOLD . ", ".WAREHOUSES_ERRORS.") AND tbllocaltion_warehouses.stage_id = 0 AND (
        //         tbllocaltion_warehouses.pod_id = 0 OR exists (
        //             SELECT tbl_productions_orders_details.id
        //             FROM tbl_productions_orders_details
        //             WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = 'business_plan' 
        //         )
        //         OR exists (
        //             SELECT tbl_productions_orders_details.id
        //             FROM tbl_productions_orders_details
        //             INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
        //             WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_orders.type_orders = 4
        //         )
        //     )
        //     GROUP BY tblwarehouse_items.id_items
        // ) tb_quantity_warehouse";

        $quantityW = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
            FROM tblwarehouse_items
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_CAPACITY . ", " . WAREHOUSES_HOLD . ", ".WAREHOUSES_ERRORS.") AND tbllocaltion_warehouses.stage_id = 0 AND (
                tbllocaltion_warehouses.pod_id = 0 OR exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = 'business_plan' 
                )
                OR exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
                    WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_orders.type_orders = 4
                )
            )
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

        print_arrays($quantityW);

         // '(coalesce(tb_sum_items.total_quantity_item, 0) - coalesce(tb_sum_items.quantity_plan, 0) - coalesce(tb_perventive.total_quantity_item, 0) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
        $aColumns = [
            'tbl_products.id as id',
            'tbl_products.images as images',
            'tbl_products.code as code',
            'tbl_products.name as name',
            '0 as actions',
            'tb_sum_items.order_detail as order_detail',
            'tb_sum_items.total_quantity_item as quantity_orders',
            'tb_sum_items.quantity_plan - COALESCE(tb_productions_orders_detail.quantity_po, 0) as quantity_manufactures',
            'COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) as quantity_preventive',
            'tb_quantity_warehouse.quantity_warehouse as quantity_warehouses',
            '(tb_sum_items.total_quantity_item - (coalesce(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0)) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_products';
        $where        = [
            ' AND tbl_products.type_products  IN ("products", "semi_products")'
        ];

        if (!empty($products_search_manufactures)) {
            array_push($where, ' AND tbl_products.id = ' . $products_search_manufactures);
        }

        if (!empty($products_text_search_manufactures)) {
            array_push($where, ' AND (tbl_products.name like "%' . $products_text_search_manufactures . '%" OR tbl_products.code like "%' . $products_text_search_manufactures . '%")');
        }

        if (!empty($category_product_search_manufactures)) {
            array_push($where, ' AND tbl_products.category_id IN (' . $category_product_search_manufactures . ')');
        }

        $filter = [];
        $join = [
            'INNER JOIN ' . $tbOrdersItems . ' ON tb_sum_items.item_id = tbl_products.id',
            'LEFT JOIN ' . $quantityW . ' ON tb_quantity_warehouse.id_items = tbl_products.id',
            'LEFT JOIN ' . $tbPreventive . ' ON tb_perventive.item_id = tbl_products.id',
            'LEFT JOIN ' . $tbProductionsOrdersDetailNotPreventive . ' ON tb_productions_orders_detail.items_id = tbl_products.id',
            'LEFT JOIN ' . $tbProductionsOrdersDetailPreventive . ' ON tb_productions_orders_detail_perventive.items_id = tbl_products.id',
        ];

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            array_push($where, 'AND tbl_products.id_branch IN ('.implode(',', $branch_staff).')');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'ORDER BY quantity_need_manufactures DESC', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity_orders = 0;
        $total_quantity_manufactures = 0;
        $total_quantity_preventive = 0;
        $total_quantity_warehouses = 0;
        $total_quantity_need_manufactures = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $product_id = $aRow['id'];
            $images = base_url('assets/images/tnh/no_image.png');
            if ($aRow['images']) {
                $images = base_url('uploads/products/' . $aRow['images']);
            }

            $row[0] = '<div class="text-center">
                <div class="">
                    <input name="product_id[]" type="checkbox" onchange="changeProductCb(this)" id="product_id_' . $product_id . '" value="' . $product_id . '">
                    <label for="product_id_' . $product_id . '"></label>
                </div>
            </div>';
            $row[1] = '<div class="text-center">
                <div class="td-image mright5" style="width: 50px; margin: auto;">
                    <div class="preview_image" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img">
                            <div style="width:45px;">
                                <a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">
                                    <div class=""><img src="' . $images . '" style="border-radius: 50%"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
            // $row[2] = '<div><a href="javascript:void(0)" onclick="modalDetailPlan('.$product_id.')">'.$aRow['items_code'].'</a></div>';
            // \''.$start_date_search_manufactures.'\', \''.$end_date_search_manufactures.'\', \''.$customer_search_manufactures.'\', 
            $row[2] = '<div>' . $aRow['code'] . '</div>';
            $row[3] = '<div>' . $aRow['name'] . '</div>';
            $order_detail = explode('AAA', trim($aRow['order_detail'], 'AAA'));
            $order_detail_text = '';
            foreach ($order_detail as $k => $v) {
                $v = explode('FF', $v);
                foreach ($v as $k1 => $v1) {
                    $v1 = explode('|||', $v1);
                    if ($v1[1] <= 0) continue;
                    $order_detail_text .= '- ' . $v1[0] . ' (' . formatNumber($v1[1]) . ')<br>';
                }
            }
            $row[4] = '<div style="width: 180px;">' . $order_detail_text . '</div>';

            $keepStock = $this->perAddProductionsPlan ? '<a class="tnh-modal add-keep-stock-product btn btn-warning mtop5" data-tnh="modal" data-toggle="modal" data-target="#myModal" title="' . lang('Giữ kho(TC)') . '" href="' . base_url('admin/manufactures_temp/keep_stock_products/'.$product_id.'') . '"><i class="fa fa-plus width-icon-actions"></i> ' . lang('Giữ kho(TC)') . '</a>' : '';

            $row[5] = '<div class="text-center">
                <a class="btn btn-primary" href="javascript:void(0)" onclick="keepWarehouseOrders(\'' . $product_id . '\')">' . lang('Giữ kho') . '</a>
                '.$keepStock.'
            </div>';

            if ($aRow['quantity_manufactures'] < 0) $aRow['quantity_manufactures'] = 0;

            $row[6] = '<div class="text-center">' . formatNumber($aRow['quantity_orders']) . '</div>';
            $row[7] = '<div class="text-center">' . formatNumber($aRow['quantity_manufactures']) . '</div>';
            $row[8] = '<div class="text-center">' . formatNumber($aRow['quantity_preventive']) . '</div>';
            $row[9] = '<div class="text-center">
                <a href="'.base_url('admin/manufactures_temp/show_warehouses_plan/'.$product_id).'" class="tnh-modal" data-target="#myModal" data-toggle="modal">' . formatNumber($aRow['quantity_warehouses']) . '</a>
            </div>';
            $row[10] = '<div class="text-center">' . ($aRow['quantity_need_manufactures'] > 0 ? formatNumber($aRow['quantity_need_manufactures']) : '') . '</div>';

            $total_quantity_orders += $aRow['quantity_orders'];
            $total_quantity_manufactures += $aRow['quantity_manufactures'];
            $total_quantity_preventive += $aRow['quantity_preventive'];
            $total_quantity_warehouses += $aRow['quantity_warehouses'];
            $total_quantity_need_manufactures += ($aRow['quantity_need_manufactures'] > 0 ? ($aRow['quantity_need_manufactures']) : 0);
            $output['aaData'][] = $row;
        }

        $output['total_quantity_orders'] = $total_quantity_orders;
        $output['total_quantity_manufactures'] = $total_quantity_manufactures;
        $output['total_quantity_preventive'] = $total_quantity_preventive;
        $output['total_quantity_warehouses'] = $total_quantity_warehouses;
        $output['total_quantity_need_manufactures'] = $total_quantity_need_manufactures;
        echo json_encode($output);
    }

    public function testQuantity() {
        $flagGroup = true;
        // $productions_orders_id = 5068;
        // $items_id = 6086;
        // $plan_id = 4756;

        $productions_orders_id = 6070;
        $items_id = 6086;
        $plan_id = 5641;
        $items_id = 6148;


        $this->db->select('
            tbl_productions_orders_items_sub.type, 
            tbl_productions_orders_items_sub.item_id, 
            tbl_productions_orders_items_sub.landscape_print_size, 
            tbl_productions_orders_items_sub.number_children_size,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            MAX(tbl_productions_orders_items_sub.quantity_compensation) as quantity_compensation, 
            SUM(tbl_productions_orders_items_sub.quantity) as quantity,
            tbl_productions_orders_items_sub.quantity_single as quantity_single,
            tblunits.unit as unit_name_parent,
            unit_b.unit as unit_bom
        ', false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
        $this->db->join('tblunits unit_b', 'unit_b.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $productions_orders_id);
        $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.landscape_print_size, tbl_productions_orders_items_sub.number_children_size, tbl_productions_orders_items_sub.unit_parent_id, tbl_productions_orders_items_sub.quantity_single');
        $bom = $this->db->get()->result_array();

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

            // print_arrays($bom);
            $total_paper_exchange = 0;
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    if ($flagGroup == true) {
                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                        $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                        $quantity_compensation = 0;
                    } else {
                        $quantity_compensation = 0;
                    }

                    // $quantity = roundNumberFormat($vB['quantity'], 0);
                    $quantity = ceil(round($vB['quantity'], 4));
                    print_arrays($quantity);

                    // print_arrays($quantity);
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    // $paper_exchange = $quantity_single > 0 ? roundNumberFormat($quantity_need/$quantity_single, 0) : 0;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;
                    print_arrays($paper_exchange , '<br>', $quantity_single, '<br>', $quantity_need, '<br>', $vB['quantity']);
                    $total_paper_exchange+= $paper_exchange;
                }
            }
            $quantityNew = $total_paper_exchange; 
    }

    public function print_productions_orders_new($id) {
        ob_end_clean();
        $data = [];

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
        $this->load->model('quality_control_model');
        $this->load->model('transfer_model');
        $this->load->model('check_warehouses_model');
        $this->load->library('ciqrcode');
        $this->load->model('type_orders_model');

        $data['title'] = lang('productions_orders');
        $data['type'] = 'P';
        $data['img'] = '';
        $message = "";

        $this->db->select('
            tbl_productions_orders.id as id,
            tbl_productions_orders.reference_no as reference_no,
            tbl_productions_orders.date as date,
            tbl_productions_orders.productions_plan_reference_no as productions_plan_reference_no,
        ', false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.id', $id);
        $productions_orders = $this->db->get()->row_array();

        $tbProductionsPlan = "(
            SELECT
                tbl_productions_plan.id as id,
                tbl_productions_plan.note
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            WHERE tbl_productions_orders_items.productions_orders_id = $id
        )";
        $dtProductionsPlan = $this->db->query($tbProductionsPlan)->row_array();

        $this->db->select('
            GROUP_CONCAT(distinct tblclients.company_short) as company_short
        ', false);
        $this->db->from('tbl_productions_plan_orders');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_plan_orders.productions_plan_id');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
        $this->db->where('tbl_productions_plan_orders.productions_order_id', $id);
        $this->db->where('tbl_productions_plan_orders.object_type', 'orders');
        $customer = $this->db->get()->row_array();

        $this->db->select('
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_products.quantity_child_sheet as quantity_child_sheet,
            tbl_products.quantity_child_molds as quantity_child_molds,
            tbl_productions_orders_items.quantity as quantity,
            tbl_category_products.name as name_category,
            tblunits.unit as unit_name,
            tbl_products.sample_cover_code as sample_cover_code,
            tbl_products.mold_code as mold_code,
            tbl_productions_orders_items.object_item_type as object_item_type,
            tbl_productions_orders_items.production_plan_item_id as production_plan_item_id,
            tbl_productions_plan_items.is_preventive as is_preventive,
            tbl_products.color_formula as color_formula,
            tbl_products.ball_formula as ball_formula,
            tbl_products.id as product_id
        ', false);
        $this->db->from('tbl_productions_orders_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = tbl_productions_orders_items.plan_item_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left');
        $this->db->where('tbl_productions_orders_items.productions_orders_id', $id);
        $this->db->order_by('tbl_productions_plan_items.is_preventive ASC');
        $items = $this->db->get()->result_array();

        $arrStatusOrder = [];
        $arrCategory = [];
        $arrUnit = [];
        $arrFormulaItems = [];
        $trItems = '';
        $totalQuantitySx = 0;
        if (!empty($items)) {
            $max_date_delvery = '';
            foreach ($items as $key => $value) {
                if (!in_array($value['name_category'], $arrCategory)) {
                    $arrCategory[] = $value['name_category'];
                }

                if (!in_array($value['unit_name'], $arrUnit)) {
                    $arrUnit[] = $value['unit_name'];
                }

                $object_item_type = $value['object_item_type'];
                $object_item_id = $value['production_plan_item_id'];
                $date_start = '';
                $date_delivery = '';
                $code_order = '';
                $status_order = '';
                if ($object_item_type == 'orders') {
                    $this->db->select('
                        tbl_orders.date,
                        tbl_orders.reference_no,
                        tbl_order_item_shippings.date_shipping,
                        tbl_status_orders.name as status_orders,
                        tbl_status_orders.id as id_status_orders
                    ', false);
                    $this->db->from('tbl_order_items');
                    $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
                    $this->db->join('tbl_status_orders', 'tbl_status_orders.id = tbl_orders.status_orders','left');
                    $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
                    $this->db->where('tbl_order_items.id', $object_item_id);
                    $dtObject = $this->db->get()->row_array();
                    if ($dtObject) {
                        $date_start = date_format(date_create($dtObject['date']), 'd/m/Y');
                        $date_delivery = _d($dtObject['date_shipping']);
                        $code_order = $dtObject['reference_no'];
                        $status_order = $dtObject['status_orders'];
                        if (!in_array($dtObject['id_status_orders'], $arrStatusOrder)) {
                            $arrStatusOrder[] = $dtObject['id_status_orders'];
                        }
                    }
                } else {
                    $this->db->select('
                        tbl_business_plan.reference_no,
                        tbl_business_plan.date,
                        tbl_business_plan_items_date.date as date_shipping,
                    ', false);
                    $this->db->from('tbl_business_plan_items');
                    $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_business_plan_items.business_plan_id');
                    $this->db->join('tbl_business_plan_items_date', 'tbl_business_plan_items_date.business_plan_items_id = tbl_business_plan_items.id');
                    $this->db->where('tbl_business_plan_items.id', $object_item_id);
                    $dtObject = $this->db->get()->row_array();
                    if ($dtObject) {
                        $date_start = date_format(date_create($dtObject['date']), 'd/m/Y');
                        $date_delivery = _d($dtObject['date_shipping']);
                        $code_order = $dtObject['reference_no'];
                    }
                }

                $is_preventive = $value['is_preventive'];
                if (empty($is_preventive)) {
                    // if (empty($max_date_delvery) || (strtotime($date_delivery) > strtotime($max_date_delvery))) {
                    if (empty($max_date_delvery) || (strtotime($date_delivery) < strtotime($max_date_delvery))) {
                        $max_date_delvery = $date_delivery;
                    }
                } else {
                    $date_delivery = $max_date_delvery;
                }

                $trItems.= '<tr nobr="true">
                    <td class="text-center" style="width: 6%;">'.(++$key).'</td>
                    <td style="width: 19%; text-align: left;">'.$value['item_code'].'</td>
                    <td style="width: 17%; text-align: left;">'.$value['item_name'].'</td>
                    <td style="width: 6%;" class="text-center">'.$value['unit_name'].'</td>
                    <td style="width: 10%;" class="text-center">'.formatNumber($value['quantity']).'</td>
                    <td style="width: 11%; text-align: center;">'.$date_start.'</td>
                    <td style="width: 11%; text-align: center;">'.$date_delivery.'</td>
                    <td colspan="2" style="width: 20%; text-align: left;"><span>-Mã bìa mẫu:'.$value['sample_cover_code'].'</span><br><span>-Mã khuôn:'.$value['mold_code'].'</span><br><span>-Số con/ tờ:'.formatNumber($value['quantity_child_sheet']).'</span><br><span>-Số con/ khuôn bế:'.formatNumber($value['quantity_child_molds']).'</span><br><span>CT:'.$code_order.'</span>
                    </td>
                </tr>';
                $totalQuantitySx += $value['quantity'];

                if (empty($arrFormulaItems[$value['product_id']])) {
                    $arrFormulaItems[$value['product_id']] = [
                        'product_id' => $value['product_id'],
                        'item_name' => $value['item_name'],
                        'item_code' => $value['item_code'],
                        'quantity' => $value['quantity'],
                        'color_formula' => $value['color_formula'],
                        'ball_formula' => $value['ball_formula'],
                    ];
                }
            }
        }

        $status_order_all = '';
        if (!empty($arrStatusOrder)){
            if (in_array(1,$arrStatusOrder)){
                $status_order_all = 'Gấp';
            } elseif(in_array(5,$arrStatusOrder)){
                $status_order_all = 'Khẩn';
            } else {
                $status_order_all = 'Bình thường';
            }
        }

        //NPL
        $trBom = '';
        // SUM(tbl_productions_orders_items_sub.quantity_compensation + tbl_productions_orders_items_sub.quantity_compensation_sm) as quantity_compensation,
        $this->db->select('
            tbl_productions_orders_items_sub.type, 
            tbl_productions_orders_items_sub.item_id, 
            tbl_productions_orders_items_sub.landscape_print_size, 
            tbl_productions_orders_items_sub.number_children_size,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            MAX(tbl_productions_orders_items_sub.quantity_compensation) as quantity_compensation, 
            SUM(tbl_productions_orders_items_sub.quantity) as quantity,
            tbl_productions_orders_items_sub.quantity_single as quantity_single,
            tblunits.unit as unit_name_parent,
            unit_b.unit as unit_bom
        ', false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
        $this->db->join('tblunits unit_b', 'unit_b.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $id);
        $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.landscape_print_size, tbl_productions_orders_items_sub.number_children_size, tbl_productions_orders_items_sub.unit_parent_id, tbl_productions_orders_items_sub.quantity_single');
        $bom = $this->db->get()->result_array();
        // print_arrays($bom);

        if (!empty($bom)) {
            foreach ($bom as $key => $value) {
                $item_id = $value['item_id'];
                $type = $value['type'];
                $height = 0;
                $mode = '';

                if ($type == "materials") {
                    $info = $this->items_model->rowMaterial($item_id);

                    $this->db->select('
                        tbl_mode_materials.code as code,
                        tbl_mode_materials.name as name,
                    ', false);
                    $this->db->from('tbl_mode_materials');
                    $this->db->where('tbl_mode_materials.id', $info['mode_id']);
                    $dtMode = $this->db->get()->row_array();

                    $mode = $dtMode['name'];
                    $unitBOM = $this->unit_model->rowUnit($info['standard_unit']);
                } else if ($type == "tools_supplies") {
                    $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                    $unitBOM = $this->unit_model->rowUnit($info['unit_id']);
                } else {
                    $info = $this->products_model->rowProduct($item_id);
                    $unitBOM = $this->unit_model->rowUnit($info['unit_id']);
                }

                $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($dtProductionsPlan['id'], $value['item_id'], $value['type']);

                $quantity_compensation_bom = $value['quantity_compensation'];
                $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];
                // $quantity = roundNumberFormat($value['quantity'], 0);
                $quantity = ceil(round($value['quantity'], 3));
                $quantity_single = $value['quantity_single'];
                $quantity_need = $quantity + $quantity_compensation;
                // $paper_exchange = $quantity_single > 0 ? roundNumberFormat($quantity_need/$quantity_single, 0) : 0;
                $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;

                $trBom.= '<tr nobr="true">
                    <td class="text-center" style="width: 5%;">'.(++$key).'</td>
                    <td style="width: 15%; text-align: left;"><span>'.($info['code']).'</span><br><span style="font-size: 9px;">'.$info['name'].'</span></td>
                    <td style="width: 9%; text-align: left;">'.$mode.'</td>
                    <td style="width: 9%;" class="text-center">'.formatNumber($quantity).'</td>
                    <td style="width: 7%;" class="text-center">'.formatNumber($quantity_compensation_bom).'</td>
                    <td style="width: 7%;" class="text-center">'.formatNumber($quantity_compensation).'</td>
                    <td style="width: 10%;" class="text-center">'.formatNumber($quantity_need).'</td>
                    <td style="width: 8%;" class="text-center">'.(formatNumber($quantity_single)).'</td>
                    <td style="width: 8%;" class="text-center">'.$value['landscape_print_size'].'</td>
                    <td style="width: 8%;" class="text-center">'.$value['number_children_size'].'</td>
                    <td style="width: 9%;" class="text-center">'.formatNumber($paper_exchange).'</td>
                    <td style="width: 5%;" class="text-center">'.$value['unit_bom'].'</td>
                </tr>';
            }
        }

        //stages
        // $this->db->select('
        //     tbl_productions_orders_items_stages.stage_id as stage_id,
        //     tbl_stages.name as stage_name,
        //     GROUP_CONCAT(tbl_productions_orders_items_stages.face) as face,
        //     GROUP_CONCAT(tbl_productions_orders_items_stages.face_after) as face_after,
        // ', false);
        $this->db->select('
            tbl_productions_orders_items_stages.stage_id as stage_id,
            tbl_stages.name as stage_name,
            "" as face,
            "" as face_after,
        ', false);
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $id);
        $this->db->group_by('tbl_productions_orders_items_stages.stage_id');
        // $this->db->group_by('tbl_productions_orders_items_stages.stage_id, tbl_productions_orders_items_stages.face, tbl_productions_orders_items_stages.face_after');
        // $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
        $this->db->order_by('tbl_productions_orders_items_stages.final_stage ASC, tbl_productions_orders_items_stages.number ASC');
        $stages = $this->db->get()->result_array();
        // print_arrays($this->db->last_query());
        $trStages = '';
        if (!empty($stages)) {
            foreach ($stages as $key => $value) {

                $this->db->select('
                    GROUP_CONCAT(tbl_productions_orders_items_stages.face) as face,
                    GROUP_CONCAT(tbl_productions_orders_items_stages.face_after) as face_after,
                ', false);
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->where('tbl_productions_orders_items_stages.stage_id', $value['stage_id']);
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $id);
                $pois = $this->db->get()->row_array();
                if (!empty($pois)) {
                    $value['face'] = $pois['face'];
                    $value['face_after'] = $pois['face_after'];
                }

                // $this->db->select('
                //     tbl_productions_orders_items_sub.type as type, 
                //     tbl_productions_orders_items_sub.item_id as item_id,

                // ', false);
                // $this->db->from('tbl_productions_orders_items_sub');
                // $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $id);
                // $this->db->where('tbl_productions_orders_items_sub.stage_item_id', $value['stage_id']);
                // $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id');
                // $items_bom_stages = $this->db->get()->result_array();
                // $trItemsBom = '';
                // if (!empty($items_bom_stages)) {
                //     foreach ($items_bom_stages as $k => $v) {
                //         $item_id = $v['item_id'];
                //         $type = $v['type'];
                //         if ($type == "materials") {
                //             $info = $this->items_model->rowMaterial($item_id);
                //         } else if ($type == "tools_supplies") {
                //             $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                //         } else {
                //             $info = $this->products_model->rowProduct($item_id);
                //         }
                //         $trItemsBom.= $info['code'].'<br>';
                //     }
                //     $trItemsBom = rtrim($trItemsBom, '<br>');
                // }

                // <td style="width: 50%;">'.$trItemsBom.'</td>

                $face = array_unique(explode(',', $value['face']));
                $face_after = array_unique(explode(',', $value['face_after']));
                $strFace = '';
                if (in_array(1, $face)) {
                    $strFace.= lang('Mặt trước');
                }

                if (in_array(2, $face_after)) {
                    $strFace.= !empty($strFace) ? ', '.lang('Mặt sau') : lang('Mặt sau');
                }
                if (!empty($strFace)) {
                    $strFace = '<br><span style="color: red;">'.$strFace.'</span>';
                }

                $trStages.= '<tr nobr="true">
                    <td style="width: 100%;" class="text-center">'.$value['stage_name'].''.$strFace.'</td>
                </tr>';
            }
        }

        $trFormula = '';
        if (!empty($arrFormulaItems)) {
            foreach ($arrFormulaItems as $key => $value) {
                if (empty($value['color_formula']) && empty($value['ball_formula'])) continue;
                $trFormula.= '<tr nobr="true">
                    <td style="text-align: left;">'.$value['item_code'].'</td>
                    <td style="text-align: left;">'.$value['color_formula'].'</td>
                    <td style="text-align: left;">'.$value['ball_formula'].'</td>
                </tr>';
            }
        }

        ob_start();
        stylePdf();

        echo '
            <h1 class="text-center uppercase">' . lang('productions_orders') . '</h1>
            <table cellspacing="0" cellpadding="3" style="width: 100%;">
                <tr nobr="true">
                    <td class="bold" style="width: 15%;">' . _l('Mã LSX :') . '</td>
                    <td class="bold" style="width: 60%;">'.$productions_orders['reference_no'].'</td>
                    <td class="bold" style="width: 10%;">' . _l('ĐVT:') . '</td>
                    <td class="bold" style="width: 15%;">'.(!empty($arrUnit) ? implode(', ', $arrUnit) : '').'</td>
                </tr>
                <tr nobr="true">
                    <td class="bold" style="width: 15%;">' . _l('Mã nhóm TP :') . '</td>
                    <td class="bold" style="width: 85%;">'.(!empty($arrCategory) ? implode(', ', $arrCategory) : '').'</td>
                </tr>
                <tr nobr="true">
                    <td class="bold">' . _l('CT kèm theo:') . '</td>
                    <td class="bold">'.$productions_orders['productions_plan_reference_no'].'</td>
                </tr>
                <tr nobr="true">
                    <td class="bold" style="width: 15%;">' . _l('Tên công ty:') . '</td>
                    <td class="bold" style="width: 65%;">'.$customer['company_short'].'</td>
                    <td class="bold" style="width: 20%;">'.$status_order_all.'</td>
                </tr>
                <tr nobr="true">
                    <td class="bold">' . _l('Ghi chú:') . '</td>
                    <td class="bold">'.$dtProductionsPlan['note'].'</td>
                </tr>
            </table><br><br><table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr nobr="true">
                        <th class="bold uppercase text-center" colspan="99" style="width: 100%;">' . _l('tnh_product_productions') . '</th>
                    </tr>
                    <tr nobr="true">
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 19%;">' . _l('tnh_product_code') . '</th>
                        <th class="bold text-center" style="width: 17%;">' . _l('tnh_product_name') . '</th>
                        <th class="bold text-center" style="width: 6%;">' . _l('tnh_dvt') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('SL sản xuất') . '</th>
                        <th class="bold text-center" style="width: 11%;">' . _l('Ngày mở đơn') . '</th>
                        <th class="bold text-center" style="width: 11%;">' . _l('Ngày giao hàng') . '</th>
                        <th colspan="2" class="bold text-center" style="width: 20%;">' . _l('Thông tin thành phẩm') . '</th>
                    </tr>
                </thead>
                <tbody>
                    '.$trItems.'
                    <tr>
                        <td colspan="4" class="bold">Tổng SL sản xuất</td>
                         <td class="text-center bold">'.formatNumber($totalQuantitySx).'</td>
                        <td></td>
                        <td></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table><br><br><table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr nobr="true">
                        <th class="bold uppercase text-center" colspan="99" style="width: 100%;">' . _l('NPL CẦN SẢN XUẤT') . '</th>
                    </tr>
                    <tr nobr="true">
                        <th class="bold text-center" style="width: 5%;">' . _l('tnh_numbers') . '</th>
                        <th class="bold text-center" style="width: 15%;">' . _l('Mã NPL') . '</th>
                        <th class="bold text-center" style="width: 9%;">' . _l('Quy cách') . '</th>
                        <th class="bold text-center" style="width: 9%;">' . _l('S.Lượng NPL') . '</th>
                        <th class="bold text-center" style="width: 7%;">' . _l('SL bù hao theo BOM') . '</th>
                        <th class="bold text-center" style="width: 7%;">' . _l('SL bù hao thực tế') . '</th>
                        <th class="bold text-center" style="width: 10%;">' . _l('Tổng SL NPL') . '</th>
                        <th class="bold text-center" style="width: 8%;">' . _l('Số lần xả/Tờ') . '</th>
                        <th class="bold text-center" style="width: 8%;">' . _l('Khổ xả') . '</th>
                        <th class="bold text-center" style="width: 8%;">' . _l('Số con/Tờ') . '</th>
                        <th class="bold text-center" style="width: 9%;">' . _l('Tổng số tờ in') . '</th>
                        <th class="bold text-center" style="width: 5%;">' . _l('ĐVT') . '</th>
                    </tr>
                </thead>
                <tbody>
                    '.$trBom.'
                </tbody>
                </table><br><br><table class="table-items" cellspacing="0" cellpadding="5" border="1">
                    <thead>
                        <tr nobr="true">
                            <th class="bold uppercase text-center" colspan="99" style="width: 100%;">' . _l('CÔNG ĐOẠN') . '</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$trStages.'
                    </tbody>
                </table><br><br><table style="width: 100%">
                <tr>
                    <td class="text-center">
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('Thời gian in : '.(date('d/m/Y H:i:s')).'') . '('.get_staff_full_name().')</span>
                    </td>
                </tr>
            </table><br><br><table class="table-items" nobr="true" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr nobr="true">
                        <th class="bold uppercase text-center" colspan="99" style="width: 100%;">' . _l('MẪU') . '</th>
                    </tr>
                </thead>
                <tbody>
                    <tr nobr="true"><td style="width: 100%;"><br><br><br><br><br><br><br></td></tr>
                </tbody>
            </table>'.($trFormula ? '<br><br><table class="table-items" nobr="true" cellspacing="0" cellpadding="5" border="1">
                <tr nobr="true">
                    <td class="bold uppercase text-center" style="width: 33.33%;">' . _l('tnh_product_code') . '</td>
                    <td class="bold uppercase text-center" style="width: 33.33%;">' . _l('tnh_color_formula') . '</td>
                    <td class="bold uppercase text-center" style="width: 33.33%;">' . _l('tnh_ball_formula') . '</td>
                </tr>
                '.$trFormula.'
            </table>' : '').'
        ';

        $content = ob_get_contents();
        ob_end_clean();
        // echo $content;
        // die;
        $data['type_print'] = '';
        $data['content'] = $content;
        $pdf = print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function rowLocationWarehouseNew_warehouse()
    {
        $data = [];
        $results = [];
        // if ($this->input->post()) {
            $item = 'materials__8514';
            $warehouse_id = $this->input->post('warehouse_id');
            if (!empty($item)) {
                $item = explode('__', $item);
                $type_item = $item[0];
                $item_id = $item[1];

                $item_type = $type_item;
                if ($item_type == "materials") {
                    $item_type = "nvl";
                } else if ($item_type == "tools_supplies") {
                    $item_type = "tools";
                } else {
                    $item_type = "product";
                }

                $this->db->select('tblwarehouse.id, tblwarehouse.name');
                $this->db->from('tblwarehouse');
                $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
                if (!empty($item_id)) {
                    $this->db->where('(
                        EXISTS (
                            SELECT tblwarehouse_items.id
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse.id = tblwarehouse_items.warehouse_id
                        )
                    )');
                }
                $warehouses = $this->db->get()->result_array();

                if (!empty($warehouses)) {
                    foreach ($warehouses as $key => $value) {
                        $warehouse_id = $value['id'];

                        $tbQuantityWarehouses = '(
                            SELECT
                                tblwarehouse_items.localtion as localtion_id,
                                tblwarehouse_items.lot_code, 
                                tblwarehouse_items.date_sx, 
                                tblwarehouse_items.date_sd, 
                                tblwarehouse_items.date_use,
                                SUM(tblwarehouse_items.product_quantity) as product_quantity
                            FROM tblwarehouse_items
                            WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '" AND tblwarehouse_items.product_quantity > 0
                            GROUP BY tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use
                        ) tb_quantity_warehouses';

                        $this->db->select('
                            CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id, "__", coalesce(tb_quantity_warehouses.lot_code, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sx, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sd, "NULL"), "__", coalesce(tb_quantity_warehouses.date_use, "NULL")) as id,
                            CONCAT(tbllocaltion_warehouses.name, , "(SL kho: ", tb_quantity_warehouses.product_quantity,")") as text,
                            tb_quantity_warehouses.lot_code as lot_code,
                            tb_quantity_warehouses.date_sx as date_sx,
                            tb_quantity_warehouses.date_sd as date_sd,
                            tb_quantity_warehouses.date_use as date_use,
                            tbllocaltion_warehouses.name as name,
                            tb_quantity_warehouses.product_quantity as product_quantity
                        ', false);
                        $this->db->from('tbllocaltion_warehouses');
                        $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
                        $this->db->where('tbllocaltion_warehouses.warehouse', $warehouse_id);
                        $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
                        // $this->db->group_start();
                        // $this->db->where('tbllocaltion_warehouses.pod_id', 0);
                        // $this->db->or_where('exists (
                        //     SELECT tbl_productions_orders_details.id
                        //     FROM tbl_productions_orders_details
                        //     WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan"
                        // )', false, false);
                        // $this->db->group_end();
                        $location_warehouses = $this->db->get()->result_array();
                        if (!empty($location_warehouses)) {
                            print_arrays($this->db->last_query());
                            foreach ($location_warehouses as $k => $val) {
                                $name_location = '';

                                $product_quantity = $val['product_quantity'];
                                $lot_code = $val['lot_code'] ? ' - Lot: ' . $val['lot_code'] : '';
                                $date_sx = $val['date_sx'] ? ' - Ngày SX: ' . _d($val['date_sx']) : '';
                                $date_sd = $val['date_sd'] ? ' - Ngày SD: ' . _d($val['date_sd']) : '';

                                // $location_warehouses[$k]['text'] = $value['name'].' - '.$val['text'].$lot_code.$date_sx.$date_sd;
                                $location_warehouses[$k]['text'] = $val['text'] . $lot_code . $date_sx . $date_sd;
                            }
                        }

                        $results[] = ['text' => $value['name'], 'children' => $location_warehouses];
                    }
                }
            }
        // }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function testView() {
        $this->load->view('admin/orders/test');
    }

    public function order_items() {
        $this->load->model('orders_model');
        $this->load->model('products_model');

        $htmlOrderColumns = '';
        $productsColumns = $this->products_model->getProductsColumns(7021);
        $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId(21720);
        // $orderItemsColumnsTemp = $orderItemsColumns;
        $value = $this->orders_model->rowOrderItemsById(21720);
        $ct_counter_item = $value['ct_counter_item'];
        // $ct_counter_item = 50;
        $trHtmlChild = '';
        $thSub = '';
        $trHtmlChild = '';
        $trHtmlColumns = '';

        $thSub = '';
        if (!empty($productsColumns)) {
            foreach ($productsColumns as $k => $v) {
                $thSub .= '<th class="text-center" style="width:80px;">' . $v['name'] . '</th>';
            }
        }

        $orderItemsMap = [];
        $orderItemsMap1 = [];
        foreach ($orderItemsColumns as $item) {
            $orderItemsMap[$item['counter_items_number']][$item['columns_value']] = $item['columns_name'];
            $orderItemsMap1[$item['counter_items_number']][$item['columns_id']] = $item['columns_name'];
        }

        if ($ct_counter_item > 0) {
            for ($i = 0; $i < $ct_counter_item; $i++) {

                $trHtmlColumns = '';
                foreach ($productsColumns as $k => $v) {
                    $columns_name = '';
                    // foreach ($orderItemsColumns as $kO => $vO) {
                    //     if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                    //         $columns_name = $vO['columns_name'];
                    //         break;
                    //     }
                    // }

                    $columns_name = isset($orderItemsMap1[$i][$v['id']]) ? $orderItemsMap1[$i][$v['id']] : '';
                    $trHtmlColumns .= '
                        <td class="text-center">
                            ' . $columns_name . '
                        </td>
                    ';
                }
                
                // Lấy dữ liệu từ danh sách dự phòng thay vì lặp qua $orderItemsColumns
                $order_code = isset($orderItemsMap[$i]['order_code']) ? $orderItemsMap[$i]['order_code'] : '';
                $command = isset($orderItemsMap[$i]['command']) ? $orderItemsMap[$i]['command'] : '';
                $quantity_put = isset($orderItemsMap[$i]['quantity_put']) ? formatNumber($orderItemsMap[$i]['quantity_put']) : '';
                $quantity_loss = isset($orderItemsMap[$i]['quantity_loss']) ? formatNumber($orderItemsMap[$i]['quantity_loss']) : '';
                $sample_quantity_item = isset($orderItemsMap[$i]['sample_quantity_item']) ? formatNumber($orderItemsMap[$i]['sample_quantity_item']) : '';
        
                // Tạo các ô TD một cách tối ưu
                $tdOrderCode = '<td class="text-center">' . $order_code . '</td>';
                $tdCommand = '<td class="text-center">' . $command . '</td>';
                $tdQuantityPut = '<td class="text-center">' . $quantity_put . '</td>';
                $tdQuantityLoss = '<td class="text-center">' . $quantity_loss . '</td>';
                $tdSampleQuantityItem = '<td class="text-center">' . $sample_quantity_item . '</td>';
        
                if (empty($order_code)) continue;
        
                $stt = $i + 1;
                $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
        
                $trHtmlChild .= '<tr class="not-tr">
                    ' . $tdNumberChild . '
                    ' . $tdOrderCode . '
                    ' . $tdCommand . '
                    ' . $tdQuantityPut . '
                    ' . $tdQuantityLoss . '
                    ' . $tdSampleQuantityItem . '
                    ' . $trHtmlColumns . '
                </tr>';
            }

            $htmlOrderColumns .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
            <thead>
                <tr class="not-tr">
                    <th class="text-center" style="width: 50px;">
                        ' . lang('tnh_numbers') . '
                    </th>
                    <th class="text-center" style="width: 100px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 100px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_sample_quantity') . '<small class="req text-danger">*</small></th>
                    ' . $thSub . '
                </tr>
            </thead>
                <tbody class="child">
                    ' . $trHtmlChild . '
                </tbody>
            </table>
            ';
        }

        print_arrays($htmlOrderColumns);

        $trHtmlChild = '';
        $trHtmlColumns = '';
        if ($ct_counter_item > 0) {
            for ($i = 0; $i < $ct_counter_item; $i++) {
                $trHtmlColumns = '';
                foreach ($productsColumns as $k => $v) {
                    $columns_name = '';
                    foreach ($orderItemsColumns as $kO => $vO) {
                        if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                            $columns_name = $vO['columns_name'];
                            break;
                        }
                    }

                    $trHtmlColumns .= '
                        <td class="text-center">
                            ' . $columns_name . '
                        </td>
                    ';
                }

                $order_code = '';
                $command = '';
                $quantity_put = '';
                $quantity_loss = '';
                $sample_quantity_item = '';
                foreach ($orderItemsColumns as $kO => $vO) {
                    if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                        $order_code = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                        $command = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                        $quantity_put = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                        $quantity_loss = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                        $sample_quantity_item = $vO['columns_name'];
                        continue;
                    }
                }

                $tdOrderCode = '<td class="text-center">
                    ' . $order_code . '
                </td>';

                $tdCommand = '<td class="text-center">
                    ' . $command . '
                </td>';

                $tdQuantityPut = '<td class="text-center">
                    ' . formatNumber($quantity_put) . '
                </td>';

                $tdQuantityLoss = '<td class="text-center">
                    ' . formatNumber($quantity_loss) . '
                </td>';

                $tdSampleQuantityItem = '<td class="text-center">
                    ' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '
                </td>';


                if (empty($trHtmlColumns) && empty($order_code)) continue;
                $stt = $i + 1;
                $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                $trHtmlChild .= '<tr class="not-tr">
                    ' . $tdNumberChild . '
                    ' . $tdOrderCode . '
                    ' . $tdCommand . '
                    ' . $tdQuantityPut . '
                    ' . $tdQuantityLoss . '
                    ' . $tdSampleQuantityItem . '
                    ' . $trHtmlColumns . '
                </tr>';
            }

            $htmlOrderColumns .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
            <thead>
                <tr class="not-tr">
                    <th class="text-center" style="width: 50px;">
                        ' . lang('tnh_numbers') . '
                    </th>
                    <th class="text-center" style="width: 100px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 100px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_sample_quantity') . '<small class="req text-danger">*</small></th>
                    ' . $thSub . '
                </tr>
            </thead>
                <tbody class="child">
                    ' . $trHtmlChild . '
                </tbody>
            </table>
            ';
        }

        echo $htmlOrderColumns;
        // print_arrays($orderItemsColumnsTemp);
    }

    public function order_items1() {
        $this->load->model('orders_model');
        $this->load->model('products_model');

        $htmlOrderColumns = '';
        $productsColumns = $this->products_model->getProductsColumns(7021);
        $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId(21720);
        // $orderItemsColumnsTemp = $orderItemsColumns;
        $value = $this->orders_model->rowOrderItemsById(21720);
        $ct_counter_item = $value['ct_counter_item'];
        // $ct_counter_item = 50;
        $trHtmlChild = '';
        $thSub = '';
        $trHtmlChild = '';
        $trHtmlColumns = '';

        $thSub = '';
        if (!empty($productsColumns)) {
            foreach ($productsColumns as $k => $v) {
                $thSub .= '<th class="text-center" style="width:80px;">' . $v['name'] . '</th>';
            }
        }

        $trHtmlChild = '';
        $trHtmlColumns = '';
        if ($ct_counter_item > 0) {
            for ($i = 0; $i < $ct_counter_item; $i++) {
                $trHtmlColumns = '';
                foreach ($productsColumns as $k => $v) {
                    $columns_name = '';
                    foreach ($orderItemsColumns as $kO => $vO) {
                        if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                            $columns_name = $vO['columns_name'];
                            break;
                        }
                    }

                    $trHtmlColumns .= '
                        <td class="text-center">
                            ' . $columns_name . '
                        </td>
                    ';
                }

                $order_code = '';
                $command = '';
                $quantity_put = '';
                $quantity_loss = '';
                $sample_quantity_item = '';
                foreach ($orderItemsColumns as $kO => $vO) {
                    if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                        $order_code = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                        $command = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                        $quantity_put = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                        $quantity_loss = $vO['columns_name'];
                        continue;
                    } else if ($vO['columns_value'] == 'sample_quantity_item' && $i == $vO['counter_items_number']) {
                        $sample_quantity_item = $vO['columns_name'];
                        continue;
                    }
                }

                $tdOrderCode = '<td class="text-center">
                    ' . $order_code . '
                </td>';

                $tdCommand = '<td class="text-center">
                    ' . $command . '
                </td>';

                $tdQuantityPut = '<td class="text-center">
                    ' . formatNumber($quantity_put) . '
                </td>';

                $tdQuantityLoss = '<td class="text-center">
                    ' . formatNumber($quantity_loss) . '
                </td>';

                $tdSampleQuantityItem = '<td class="text-center">
                    ' . (!empty($sample_quantity_item) ? formatNumber($sample_quantity_item) : '') . '
                </td>';


                if (empty($trHtmlColumns) && empty($order_code)) continue;
                $stt = $i + 1;
                $tdNumberChild = '<td class="text-center">' . $stt . '</td>';
                $trHtmlChild .= '<tr class="not-tr">
                    ' . $tdNumberChild . '
                    ' . $tdOrderCode . '
                    ' . $tdCommand . '
                    ' . $tdQuantityPut . '
                    ' . $tdQuantityLoss . '
                    ' . $tdSampleQuantityItem . '
                    ' . $trHtmlColumns . '
                </tr>';
            }

            $htmlOrderColumns .= '<table class="table table-child" style="width: auto; margin-left: 50px !important;">
            <thead>
                <tr class="not-tr">
                    <th class="text-center" style="width: 50px;">
                        ' . lang('tnh_numbers') . '
                    </th>
                    <th class="text-center" style="width: 100px;">' . lang('tnh_order_code') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 100px;">' . lang('tnh_command') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_put') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_quantity_loss') . '<small class="req text-danger">*</small></th>
                    <th class="text-center" style="width: 80px;">' . lang('tnh_sample_quantity') . '<small class="req text-danger">*</small></th>
                    ' . $thSub . '
                </tr>
            </thead>
                <tbody class="child">
                    ' . $trHtmlChild . '
                </tbody>
            </table>
            ';
        }

        echo $htmlOrderColumns;
    }

    public function get_print_tem()
    {
        $this->load->model('orders_model');
        $this->load->model('products_model');
        $this->load->model('clients_model');
        $this->load->model('unit_model');
        ob_end_clean();

        $order_id = 20863;
        $p_id = 21720;
        $type_print = 3;
        $vt1 = $this->input->post('vt1');
        $vt2 = $this->input->post('vt2');
        $vt3 = $this->input->post('vt3');
        $vt4 = $this->input->post('vt4');

        $order = $this->orders_model->rowOrderById($order_id);
        $customer = $this->clients_model->rowCustomer($order['customer_id']);
        $tableTem = '';
        if (!empty($p_id)) {
            $vt1_ar = array();
            $vt1_id = array();
            if(!empty($vt1)){
                $vt1_ar = explode('_____',$vt1);
                foreach ($vt1_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt1_id[$vas[0]] = $vas[1];
                }
            }
            $vt2_ar = array();
            $vt2_id = array();
            if(!empty($vt2)){
                $vt2_ar = explode('_____',$vt2);
                foreach ($vt2_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt2_id[$vas[0]] = $vas[1];
                }
            }

            $vt3_ar = array();
            $vt3_id = array();
            if(!empty($vt3)){
                $vt3_ar = explode('_____',$vt3);
                foreach ($vt3_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt3_id[$vas[0]] = $vas[1];
                }
            }
            $vt4_ar = array();
            $vt4_id = array();
            if(!empty($vt4)){
                $vt4_ar = explode('_____',$vt4);
                foreach ($vt4_ar as $ka => $va) {
                    $vas = explode('|_|',$va);
                    $vt4_id[$vas[0]] = $vas[1];
                }
            }
            $p_id = explode(',', $p_id);
            $this->db->select('tbl_orders.*, tblclients.company as company,tblclients.company_short as company_short, tblclients.zcode as code_customer,tblclients.is_separate_guest as is_separate_guest', false);
            $this->db->from('tbl_orders');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
            $this->db->where('tbl_orders.id', $order_id);
            $order = $this->db->get()->row_array();


            $this->db->select('
                tbl_order_items.*
            ', false);
            $this->db->from('tbl_order_items');
            $this->db->where('tbl_order_items.order_id', $order_id);
            $this->db->where_in('tbl_order_items.id', $p_id);
            $order_items = $this->db->get()->result_array();

            if (!empty($order_items)) {
                foreach ($order_items as $key => $value) {
                    $order_item_id = $value['id'];
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];

                    $product_name_customer =  '';
                    $mode = '';
                    $quantity_child_sheet = 0;
                    $lost = '';

                    $even_quantity = '';
                    $odd_quantity = '';

                    $even_quantity_bale = '';
                    $odd_quantity_bale = '';

                    $item_code = '';
                    $color_size = '';
                    $gw = '';
                    $carton_size = '';

                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($value['product_name_customer'])) {
                            $product_name_customer = $value['product_name_customer'];
                        } else if (!empty($info['product_name_customer'])) {
                            $product_name_customer = $info['product_name_customer'];
                        } else {
                            $product_name_customer = $info['name'] . ' ' . $info['code'];
                        }

                        $mode = $info['mode_product'];
                        $lost = $info['loss'];
                        $quantity_child_sheet = $info['quantity_child_sheet'];
                        $quantity_sheet_bale = $info['quantity_sheet_bale'];
                        if(empty($quantity_child_sheet)){
                            $quantity_child_sheet = 1;
                        }
                        if(empty($quantity_sheet_bale)){
                            $quantity_sheet_bale = 1;
                        }
                        if ($quantity_child_sheet > 0) {
                            $quantity_sheet = $value['quantity'] / $quantity_child_sheet;
                            $even_quantity = floor($quantity_sheet);
                            $quantity_ceil = ceil($quantity_sheet);
                            $odd_quantity = $quantity_ceil - $even_quantity;
                        }

                        $quantity_bale = 0;
                        $quantity_ceil_bale_chan = 0;
                        if ($quantity_sheet_bale > 0) {
                            $quantity_bale = $value['quantity'] / $quantity_sheet_bale;
                            $even_quantity_bale = floor($quantity_bale);
                            $quantity_ceil_bale = ceil($quantity_bale);
                            $quantity_ceil_bale_chan = floor($quantity_bale);
                            $odd_quantity_bale = $quantity_ceil_bale - $even_quantity_bale;
                        }

                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }

                        $item_code = $info['code'];
                        $color_size = $info['color_size'];
                        $gw = $info['gw'];
                        $carton_size = $info['carton_size'];
                    } else if ($type_item == "materials") {
                        $info = $this->items_model->rowMaterial($items_id);

                        if (!empty($value['product_name_customer'])) {
                            $product_name_customer = $value['product_name_customer'];
                        } else if (!empty($info['name_customer'])) {
                            $product_name_customer = $info['name_customer'];
                        } else {
                            $product_name_customer = $info['name'] . ' ' . $info['code'];
                        }

                        $mode = $info['mode'];

                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/materials/' . $info['images']);
                        }

                        $item_code = $info['code'];
                    }
                    $font = 'dejavuserifcondensed';
                    if(preg_match("/\p{Han}+/u", $product_name_customer)){
                        $font = 'kozgopromedium';
                    }
                    // if ($order['type_orders'] == ORDER_DEFAULT || $order['type_orders'] == ORDER_CHANGE) {

                    if ($order['type_orders'] != ORDER_CHANGE && $order['type_orders'] != ORDER_CHANGE_SIZE) {
                        if ($type_print == 1) {
                            if (!empty($quantity_ceil_bale)) {
                                for ($i = 0; $i < $quantity_ceil_bale; $i++) {
                                    $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Mã KH</td>
                                            <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                            <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                            <td class="" style="width: 15%;">Qui Cách</td>
                                            <td class="text-center" style="width: 10%;">ĐVT</td>
                                            <td class="" style="width: 15%;">Ghi Chú</td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Mã ĐĐH</td>
                                            <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                            <td class="text-center" rowspan="2" colspan="3" style="width: 35%; line-height: 45px;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                            <td class="" style="width: 15%;">' . $mode . '</td>
                                            <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                            <td class="" style="width: 15%;">' . $value['note_item'] . '</td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="" style="width: 10%;">Số lượng giao</td>
                                            <td class="text-right" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>
                                            <td class="" style="width: 15%;">SL Con/Tờ</td>
                                            <td class="text-center" style="width: 10%;">' . formatNumber($quantity_child_sheet) . '</td>
                                            <td class="" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">Tờ Chẵn</td>
                                            <td class="text-center" style="width: 15%;">Tờ Lẻ</td>
                                            <td class="text-center" style="width: 15%;">Kiện Chẵn</td>
                                            <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;">Lost</td>
                                            <td class="text-center" style="width: 10%;">' . $lost . '</td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">' . $even_quantity . '</td>
                                            <td class="text-center" style="width: 15%;">' . $odd_quantity . '</td>
                                            <td class="text-center" style="width: 15%;">' . $quantity_sheet_bale . '</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                        <tr nobr="true" style="">
                                            <td class="text-center" style="width: 10%;">QC Kiểm</td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 15%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;">Ngày giao</td>
                                            <td class="text-center" style="width: 10%;"></td>
                                            <td class="text-left" style="width: 15%;"></td>
                                        </tr>
                                    </table><div style="line-height: 0.0000000001em;"></div>';
                                }
                            }


                            if (!empty($odd_quantity_bale)) {
                                $quantity_odd = $value['quantity'] - $quantity_sheet_bale * $even_quantity_bale;
                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Mã KH</td>
                                        <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                        <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                        <td class="" style="width: 15%;">Qui Cách</td>
                                        <td class="text-center" style="width: 10%;">ĐVT</td>
                                        <td class="" style="width: 15%;">Ghi Chú</td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Mã ĐĐH</td>
                                        <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                        <td class="text-center" rowspan="2" colspan="3" style="width: 35%; line-height: 45px;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                        <td class="" style="width: 15%;">' . $mode . '</td>
                                        <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                        <td class="" style="width: 15%;">' . $value['note_item'] . '</td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="" style="width: 10%;">Số lượng giao</td>
                                        <td class="text-right" style="width: 15%;">' . formatNumber($value['quantity']) . '</td>
                                        <td class="" style="width: 15%;">SL Con/Tờ</td>
                                        <td class="text-center" style="width: 10%;">' . formatNumber($quantity_child_sheet) . '</td>
                                        <td class="" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">Tờ Chẵn</td>
                                        <td class="text-center" style="width: 15%;">Tờ Lẻ</td>
                                        <td class="text-center" style="width: 15%;">Kiện Chẵn</td>
                                        <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;">Lost</td>
                                        <td class="text-center" style="width: 10%;">' . $lost . '</td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">' . $even_quantity . '</td>
                                        <td class="text-center" style="width: 15%;">' . $odd_quantity . '</td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;">' . $quantity_odd . '</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                    <tr nobr="true" style="">
                                        <td class="text-center" style="width: 10%;">QC Kiểm</td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 15%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;">Ngày giao</td>
                                        <td class="text-center" style="width: 10%;"></td>
                                        <td class="text-left" style="width: 15%;"></td>
                                    </tr>
                                </table><div style="line-height: 0.0000000001em;"></div>';
                            }
                        } elseif ($type_print == 3) {
                            if ($quantity_child_sheet > 0) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    // $ct_counter_item = 1;
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    if ($ct_counter_item > 0) {
                                        //
                                        $orderItemsMap = [];
                                        $orderItemsMap1 = [];
                                        foreach ($orderItemsColumns as $item) {
                                            $orderItemsMap[$item['counter_items_number']][$item['columns_value']] = $item['columns_name'];
                                            $orderItemsMap1[$item['counter_items_number']][$item['columns_id']] = $item['columns_name'];
                                        }
                                        //

                                        $check_key = 0;
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                // foreach ($orderItemsColumns as $kO => $vO) {
                                                //     if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                //         $columns_name = $vO['columns_name'];
                                                //         break;
                                                //     }
                                                // }

                                                $columns_name = isset($orderItemsMap1[$i][$v['id']]) ? $orderItemsMap1[$i][$v['id']] : '';
                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            // foreach ($orderItemsColumns as $kO => $vO) {
                                            //     if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                            //         $order_code = $vO['columns_name'];
                                            //         continue;
                                            //     } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                            //         $command = $vO['columns_name'];
                                            //         continue;
                                            //     } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                            //         $quantity_put = $vO['columns_name'];
                                            //         continue;
                                            //     } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                            //         $quantity_loss = $vO['columns_name'];
                                            //         continue;
                                            //     }
                                            // }

                                            $order_code = isset($orderItemsMap[$i]['order_code']) ? $orderItemsMap[$i]['order_code'] : '';
                                            $command = isset($orderItemsMap[$i]['command']) ? $orderItemsMap[$i]['command'] : '';
                                            $quantity_put = isset($orderItemsMap[$i]['quantity_put']) ? formatNumber($orderItemsMap[$i]['quantity_put']) : '';
                                            $quantity_loss = isset($orderItemsMap[$i]['quantity_loss']) ? formatNumber($orderItemsMap[$i]['quantity_loss']) : '';
                                            $sample_quantity_item = isset($orderItemsMap[$i]['sample_quantity_item']) ? formatNumber($orderItemsMap[$i]['sample_quantity_item']) : '';

                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $quantity_colum = floor($quantity_put / $quantity_child_sheet);
                                            $quantity_odd = $quantity_put - $quantity_child_sheet * $quantity_colum;
                                            if ($check_key == 0) {
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0"><tr nobr="true" style="">';
                                            }

                                            $tableTem .= '<td class="" style="width: 50%;"><table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr nobr="true" style="">
                                                <td class=""  colspan="2">K/H: ' . $order['company_short'] . '</td>
                                                <td colspan="4" >M/Đ: ' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="6" style="font-family: '.$font.';font-size:11px" >T/T: ' . $product_name_customer . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3"  >CL: ' . $command . '</td>
                                                <td class=""  >SL: </td>
                                                <td class="text-right" >' . formatNumber($quantity_put) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3"  >QC</td>
                                                <td class="" style="border-right: none;border-bottom: none;">Tờ: </td>
                                                <td class="text-right" style="border-left: none;border-right: none;border-bottom: none;">' . $quantity_colum . '</td>
                                                <td class="" style="border-left: none;border-bottom: none;"></td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td colspan="3">N/Giao: ' . $dateDelivery . '</td>
                                                <td class="" style="border-right: none;border-top: none;border-right: none;">Lẻ: </td>
                                                <td class="text-right" style="border-left: none;border-right: none;border-top: none;">' . formatNumber($quantity_odd) . '</td>
                                                <td class="" style="border-left: none;border-top: none;"></td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class="text-center" colspan="3">' . $order['reference_no'] . '</td>
                                                <td class="" >SL Thêm: </td>
                                                <td class="text-right" >' . formatNumber($quantity_loss) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                            </tr>
                                        </table></td>';
                                            if ($check_key == 1) {
                                                $tableTem .= '</tr></table><div style="line-height: 0.0000000001em;"></div>';
                                                $check_key = 0;
                                            } else {
                                                if (($i + 1) == ($ct_counter_item)) {
                                                    $tableTem .= '</tr></table><div style="line-height: 0.0000000001em;"></div>';
                                                }
                                                $check_key++;
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($type_print == 4) {
                            if ($quantity_sheet_bale > 0) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    if ($ct_counter_item > 0) {
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            // $quantity_sheet_bale = 2;
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;

                                            if(!isset($value_size[$order_code.$command])){

                                                $value_size[$order_code.$command]['order_code'] = $order_code;
                                                $value_size[$order_code.$command]['command'] = $command;
                                                $value_size[$order_code.$command]['quantity_put_sum'] = $quantity_put;
                                                $value_size[$order_code.$command]['quantity_put'] = $quantity_put;
                                                $value_size[$order_code.$command]['quantity_loss'] = $quantity_loss;


                                            }else{
                                                $value_size[$order_code.$command]['quantity_put_sum'] += $quantity_put;
                                                $value_size[$order_code.$command]['quantity_put'] += $quantity_put;
                                                $value_size[$order_code.$command]['quantity_loss'] += $quantity_loss;
                                            }
                                            // $__data['order_code'] = $order_code;
                                            // $__data['command'] = $command;
                                            // $__data['quantity_put_sum'] = $quantity_put;
                                            // $__data['quantity_put'] = $quantity_put;
                                            // $__data['quantity_loss'] = $quantity_loss;
                                            // $value_size[$order_code][] = $__data;
                                        }

                                        foreach ($value_size as $kk => $vv) {
                                            $order_code = $vv['order_code'];
                                            $command = $vv['command'];
                                            $quantity_put = $vv['quantity_put'];
                                            $quantity_loss = $vv['quantity_loss'];

                                            $quantity_colum = floor($quantity_put / $quantity_sheet_bale);
                                            $quantity_odd = $quantity_put - $quantity_sheet_bale * $quantity_colum;
                                            $loss = $quantity_odd + $quantity_put * ($lost / 100);
                                            $quantity_colum_show = $quantity_sheet_bale;
                                            $quantity_colum_loss_show = $quantity_sheet_bale;
                                            if($quantity_sheet_bale == 1){
                                                $quantity_colum = 1;
                                                $quantity_colum_show = $quantity_put;
                                                $quantity_colum_loss_show = $quantity_put+$quantity_loss;
                                            }
                                            $limits = $quantity_colum;
                                            $page = 0;
                                            if($quantity_odd > 0){
                                                $limits++;
                                            }
                                            for ($j = 0; $j < $quantity_colum; $j++) {
                                                if($quantity_odd == 0){
                                                    if(($quantity_colum - 1) == $j){
                                                        if($quantity_sheet_bale != 1){
                                                            $quantity_colum_loss_show = $quantity_colum_loss_show+$quantity_loss;
                                                        }
                                                    }
                                                }
                                                $page++;
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr  nobr="true" style="">
                                                <td style="width: 18%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 40%;font-family: '.$font.';font-size:11px" class="text-left;white-space: unset;" colspan="4">' . $product_name_customer . '</td>
                                                <td style="width: 9%;">PO#</td>
                                                <td style="width: 25%;" colspan="3">' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 10%;" class=""  >SL: </td>
                                                <td style="width: 8%;" class="text-right" >' . formatNumber($quantity_colum_show) . '</td>
                                                <td style="width: 8%;" class="" >' . $unit['unit'] . '</td>
                                                <td style="width: 8%;" class="">1 kiện: </td>
                                                <td style="width: 10%;" class="">' . formatNumber($quantity_sheet_bale) . '</td>
                                                <td style="width: 10%;" class="">Số kiện: </td>
                                                <td style="width: 12%;" >'.$page.'/'.$limits.'</td>
                                                <td style="width: 9%;" class="" >CL: </td>
                                                <td style="width: 25%;" colspan="3" >' . $command . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class=""  >SL+Loss: </td>
                                                <td class="text-right" >' . formatNumber($quantity_colum_loss_show) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                                <td class=""  colspan="2">' . $order['reference_no'] . '</td>
                                                <td class=""  >Lẻ</td>
                                                <td ></td>
                                                <td class="" style="width: 9%;" >N/GIAO:</td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                                <td style="width: 5%;" class="" >QC: </td>
                                                <td style="width: 8%;"></td>
                                            </tr>
                                        </table><div style="line-height: 0.0000000001em;"></div>';
                                            }
                                            if ($quantity_odd > 0) {
                                                $page++;
                                                $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                            <tr  nobr="true" style="">
                                                <td style="width: 18%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 40%;font-family: '.$font.';font-size:11px" class="text-left;white-space: unset;" colspan="4">' . $product_name_customer . '</td>
                                                <td style="width: 9%;">PO#</td>
                                                <td style="width: 25%;" colspan="3">' . $order_code . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 10%;" class=""  >SL: </td>
                                                <td style="width: 8%;" class="text-right" >' . formatNumber($quantity_odd) . '</td>
                                                <td style="width: 8%;" class="" >' . $unit['unit'] . '</td>
                                                <td style="width: 8%;" class="">1 kiện: </td>
                                                <td style="width: 10%;" class="">' . formatNumber($quantity_sheet_bale) . '</td>
                                                <td style="width: 10%;" class="">Số kiện: </td>
                                                <td style="width: 12%;" >'.$page.'/'.$limits.'</td>
                                                <td style="width: 9%;" class="" >CL: </td>
                                                <td style="width: 25%;" colspan="3" >' . $command . '</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td class=""  >SL+Loss: </td>
                                                <td class="text-right" >' . number_format($loss) . '</td>
                                                <td class="" >' . $unit['unit'] . '</td>
                                                <td class=""  colspan="2">' . $order['reference_no'] . '</td>
                                                <td class=""  >Lẻ</td>
                                                <td ></td>
                                                <td class="" style="width: 9%;" >N/GIAO:</td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                                <td style="width: 5%;" class="" >QC: </td>
                                                <td style="width: 8%;"></td>
                                            </tr>
                                        </table><div style="line-height: 0.0000000001em;"></div>';
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($type_print == 5) {

                            if ($quantity_child_sheet > 0 && !empty($vt2_id)) {
                                $order_items_column = get_table_where('tbl_order_items_columns', array('order_id' => $order_id, 'order_item_id' => $order_item_id));
                                if ($type_item == "products") {
                                    $productsColumns = $this->products_model->getProductsColumns($items_id);
                                    $trHtmlChild = '';
                                    $thSub = '';
                                    $orderItemsColumns = $this->orders_model->getOrderItemsColumnsByOrderItemId($value['id']);
                                    $ct_counter_item = $value['ct_counter_item'];
                                    $trHtmlChild = '';
                                    $trHtmlColumns = '';
                                    $dtDateDelivery = get_table_where('tbl_order_item_shippings', ['order_item_id' => $value['id']], '', 'row_array');
                                    $dateDelivery = '';
                                    if (!empty($dtDateDelivery)) {
                                        $dateDelivery = _dhau($dtDateDelivery['date_shipping']);
                                    }
                                    $value_size = array();
                                    if ($ct_counter_item > 0) {
                                        for ($i = 0; $i < $ct_counter_item; $i++) {
                                            $trHtmlColumns = '';
                                            foreach ($productsColumns as $k => $v) {
                                                $columns_name = '';
                                                foreach ($orderItemsColumns as $kO => $vO) {
                                                    if ($vO['counter_items_number'] == $i && $vO['columns_id'] == $v['id']) {
                                                        $columns_name = $vO['columns_name'];
                                                        break;
                                                    }
                                                }

                                                $trHtmlColumns .= '
                                                <td class="text-center">
                                                    ' . $columns_name . '
                                                </td>
                                            ';
                                            }

                                            $order_code = '';
                                            $command = '';
                                            $quantity_put = '';
                                            $quantity_loss = '';
                                            $Size = '';
                                            $vt1_text_show = '';
                                            $vt3_text_show = '';
                                            $vt4_text_show = '';

                                            $text_size = '';
                                            $tv1_text = '';
                                            $tv3_text = '';
                                            $tv4_text = '';
                                            $text_size = '';
                                            if(!empty($vt1_id[$value['id']])){
                                                $tv1_text = $vt1_id[$value['id']];
                                            }
                                            if(!empty($vt3_id[$value['id']])){
                                                $tv3_text = $vt3_id[$value['id']];
                                            }
                                            if(!empty($vt4_id[$value['id']])){
                                                $tv4_text = $vt4_id[$value['id']];
                                            }

                                            if(!empty($vt2_id[$value['id']])){
                                                $text_size = $vt2_id[$value['id']];
                                            }else{
                                                continue;
                                            }

                                            if ($value['type_item'] == 'products') {
                                                $this->db->where('id', $value['item_id']);
                                                $this->db->update('tbl_products', [
                                                    'colum_vt1' => $tv1_text,
                                                    'colum_vt2' => $text_size,
                                                    'colum_vt3' => $tv3_text,
                                                    'colum_vt4' => $tv4_text,
                                                ]);
                                            }

                                            foreach ($orderItemsColumns as $kO => $vO) {
                                                if ($vO['columns_value'] == 'order_code' && $i == $vO['counter_items_number']) {
                                                    $order_code = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'command' && $i == $vO['counter_items_number']) {
                                                    $command = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_put' && $i == $vO['counter_items_number']) {
                                                    $quantity_put = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == 'quantity_loss' && $i == $vO['counter_items_number']) {
                                                    $quantity_loss = $vO['columns_name'];
                                                    continue;
                                                } else if ($vO['columns_value'] == $text_size && $i == $vO['counter_items_number']) {
                                                    $Size = $vO['columns_name'];
                                                    continue;
                                                }
                                                else if ($vO['columns_value'] == $tv1_text && $i == $vO['counter_items_number']) {
                                                    $vt1_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                                else if ($vO['columns_value'] == $tv3_text && $i == $vO['counter_items_number']) {
                                                    $vt3_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                                else if ($vO['columns_value'] == $tv4_text && $i == $vO['counter_items_number']) {
                                                    $vt4_text_show = $vO['columns_name'];
                                                    continue;
                                                }
                                            }
                                            if (empty($trHtmlColumns) && empty($order_code)) continue;
                                            $__data['order_code'] = $order_code;
                                            $__data['command'] = $command;
                                            $__data['quantity_put_sum'] = $quantity_put;
                                            $__data['quantity_put'] = $quantity_put;
                                            $__data['quantity_loss'] = $quantity_loss;
                                            $__data['Size'] = $Size;
                                            $__data['vt1_text_show'] = $vt1_text_show;
                                            $__data['vt3_text_show'] = $vt3_text_show;
                                            $__data['vt4_text_show'] = $vt4_text_show;
                                            $value_size[$order_code . $command][] = $__data;
                                        }

                                        foreach ($value_size as $h => $hv) {
                                            $total_quanliti = 0;
                                            foreach ($hv as $ks => $vs) {
                                                $total_quanliti+=$vs['quantity_put_sum'];
                                            }
                                            // $quantity_colum = floor($quantity_put / $quantity_sheet_bale);
                                            // $quantity_odd = $quantity_put - $quantity_sheet_bale * $quantity_colum;
                                            // $loss = $quantity_odd + $quantity_put * ($lost / 100);
                                            $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1" style="font-size:8px">
                                            <tr nobr="true" style="">
                                                <td style="width: 6%;">K/H:</td>
                                                <td style="width: 12%;" class=""  colspan="2">' . $order['company_short'] . '</td>
                                                <td style="width: 8%;">T/T</td>
                                                <td style="width: 36%;font-family: '.$font.';font-size:11px" colspan="6">' . $product_name_customer . '</td>
                                                <td style="width: 4%;">SL</td>
                                                <td style="width: 16%;" class="text-right" colspan="2">' . formatNumber($total_quanliti)  . '</td>
                                                <td style="width: 8%;">' . $unit['unit'] . '</td>
                                                <td style="width: 12%;" colspan="2">QC</td>
                                            </tr>
                                            <tr nobr="true" style="">
                                                <td style="width: 6%;">MĐ: </td>
                                                <td style="width: 12%;"> ' . $hv[0]['order_code'] . '</td>
                                                <td style="width: 8%;" class="">C/L</td>
                                                <td style="width: 12%;" colspan="2">' . $hv[0]['command'] . '</td>
                                                <td style="width: 12%;font-family: '.$font.';font-size:11px" colspan="2" class="">' . $hv[0]['vt3_text_show'] . '</td>
                                                <td style="width: 12%;font-family: '.$font.';font-size:11px" colspan="2" class="">' . $hv[0]['vt4_text_show'] . '</td>
                                                <td style="width: 20%;" >' . $order['reference_no'] . '</td>
                                                <td style="width: 8%;" class="" >N/GIAO </td>
                                                <td style="width: 12%;">' . $dateDelivery . '</td>
                                            </tr>';
                                            $tableTem .= '<tr nobr="true" style="">
                                                <td style="width: 6%;"></td>';
                                                $font_size = '8px';

                                                if(count($hv) > 32){
                                                    $width_size = '2%';
                                                    $font_size = '6px';
                                                }elseif(count($hv) > 24){
                                                    $width_size = '3%';
                                                }elseif(count($hv) > 16){
                                                    $width_size = '4%';
                                                }else{
                                                    $width_size = '6%';
                                                }
                                                $width_size_main = '6%';
                                                foreach ($hv as $ks => $vs) {
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . $vs['vt1_text_show'] . ' </td>';
                                                }
                                                $tableTem .= '</tr><tr nobr="true" style="">
                                                    <td style="width: 6%;">Size: </td>';
                                                $width_size_main = '6%';
                                                foreach ($hv as $ks => $vs) {
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . $vs['Size'] . ' </td>';
                                                }
                                                $tableTem .= '</tr>
                                                <tr nobr="true" style="">
                                                    <td style="width: ' . $width_size_main . ';">SL: </td>';
                                                foreach ($hv as $ks => $vs) {
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'"  class="text-center">' . $vs['quantity_put'] . ' </td>';
                                                }
                                                $tableTem .= '</tr>
                                                <tr nobr="true" style="">
                                                    <td style="width: ' . $width_size_main . ';">SX: </td>';
                                                foreach ($hv as $ks => $vs) {
                                                    $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 0 ? ($vs['quantity_put'] * $lost/100)  : 0));
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . number_format($clost) . ' </td>';
                                                }
                                                $tableTem .= '</tr>
                                                <tr nobr="true" style="">
                                                    <td style="width: ' . $width_size_main . ';">Tờ: </td>';
                                                foreach ($hv as $ks => $vs) {
                                                    $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 0 ? ($vs['quantity_put'] * $lost/100)  : 0));
                                                    $quantity_colum = floor($clost / $quantity_child_sheet);
                                                    $tableTem .= '<td style="width: ' . $width_size . ';font-size:'.$font_size.'" class="text-center">' . number_format($quantity_colum) . ' </td>';
                                                }
                                            $tableTem .= '</tr>
                                            <tr nobr="true" style="">
                                                <td style="width: ' . $width_size_main . ';">Lẻ: </td>';
                                            foreach ($hv as $ks => $vs) {
                                                $clost = ($vs['quantity_put'] + ($vs['quantity_put'] >= 0 ? ($vs['quantity_put'] * $lost/100)  : 0));
                                                $quantity_colum = floor($clost / $quantity_child_sheet);
                                                $quantity_odd = $clost - $quantity_child_sheet * $quantity_colum;
                                                $tableTem .= '<td style="width: ' . $width_size . ';font-size:8px" class="text-center">' . number_format($quantity_odd) . ' </td>';
                                            }
                                            $tableTem .= '</tr>
                                        </table><div style="line-height: 0.0000000001em;"></div>';
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->load->library('ciqrcode');
                            if (!empty($quantity_ceil_bale_chan)) {

                            	$quantity_odd = $value['quantity'] - $quantity_sheet_bale * $quantity_ceil_bale_chan;
                                if($quantity_odd > 0){
                                	$quantity_ceil_bale_chan += 1;
                                }
                                if($quantity_odd == 0){
                                    $quantity_odd = $value['quantity'] - $quantity_sheet_bale * ($quantity_ceil_bale_chan - 1);
                                }
                                $quantity_double = ceil($quantity_ceil_bale_chan / 2);
                                // print_arrays($quantity_ceil_bale);

                                $sttQ = 0;
                                $isBreak = false;
                                for ($i = 0; $i < $quantity_double; $i++) {
                                    if ($i == 100) break;
                                    $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0"><tr nobr="true" style="">';
                                    for ($j = 0; $j < 2; $j++) {
                                        $sttQ++;
                                        if ($quantity_ceil_bale_chan == $sttQ) {
                                        	$quantity_sheet_bale = $quantity_odd;
										}
                                        $qr = $order['so'] . '-' . $sttQ;

                                        $params['data'] = $qr;
                                        $params['level'] = 'H';
                                        $params['size'] = 20;
                                        $params['savename'] = FCPATH . 'uploads/orders/qrcode/' . $qr . '.png';
                                        $this->ciqrcode->generate($params);
                                        $img = file_get_contents(FCPATH . 'uploads/orders/qrcode/' . $qr . '.png');
                                        $is_separate_guest = '';
                                        if($order['is_separate_guest'] == 1){
                                            $is_separate_guest = '<tr><td  colspan="3">Made in Viet Nam</td></tr>';
                                        }
                                        $tableTem .= '<td class="" style="width: 50%;">
                                            <table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                                                <tr>
                                                    <td colspan="3" class="text-center" style="width: 100%;">' . $customer['company'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold" style="width: 30%;">Vendor code:</td>
                                                    <td style="width: 40%;"></td>
                                                    <td rowspan="5" style="width: 30%;" class="text-center">
                                                        <img width="80" src="data:image/png;base64,' . base64_encode($img) . '"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">SO#</td>
                                                    <td>' . $order['so'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">PI#:</td>
                                                    <td>' . $order['pi'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">PO#/Style:</td>
                                                    <td class="text-left">' . $order['po_style'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Item code:</td>
                                                    <td class="text-left">' . $order['item_code'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Color/ Size:</td>
                                                    <td>' . $color_size . '</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Q\'ty:</td>
                                                    <td>' . formatNumber($quantity_sheet_bale) . '</td>
                                                    <td>pcs</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">G.W:</td>
                                                    <td>' . $gw . '</td>
                                                    <td>kg</td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Carton:</td>
                                                    <td>' . ($sttQ . ' of ' . $quantity_ceil_bale_chan) . '</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Carton size:</td>
                                                    <td>' . $carton_size . '</td>
                                                    <td>cm</td>
                                                </tr>
                                                '.$is_separate_guest.'
                                            </table>
                                        </td>';

                                        if ($quantity_ceil_bale_chan == $sttQ) {
                                            $isBreak = true;
                                            break;
                                        }
                                    }
                                    $tableTem .= '</tr></table><div style="line-height: 0.0000000001em;"></div>';
                                    if ($isBreak) break;
                                }
                            }
                        }
                    } else if ($order['type_orders'] == ORDER_CHANGE) {

                        $arrSize = [1];
                        $trSize = '<td style="width: 10%;"></td>';
                        $trQuantityChild = '';
                        $trEvenQuantityChild = '';
                        $trOddQuantityChild = '';
                        $trEvenQuantityBaleChild = '';
                        $trOddQuantityBaleChild = '';

                        $this->db->select('
                            tbl_order_items_size.*,
                            tblsize.name as name_size,
                            tbl_colors.name as name_color,
                        ', false);
                        $this->db->from('tbl_order_items_size');
                        $this->db->join('tblsize', 'tblsize.id = tbl_order_items_size.size', 'left');
                        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_order_items_size.color', 'left');
                        $this->db->where('tbl_order_items_size.order_item_id', $order_item_id);
                        $this->db->order_by('tblsize.name ASC');
                        $order_items_size = $this->db->get()->result_array();
                        if (!empty($order_items_size)) {
                            foreach ($order_items_size as $kS => $vS) {
                                // if (!empty($vS['size']) && !in_array($vS['size'], $arrSize)) {
                                $arrSize[] = $vS['size'];
                                $trSize .= '<td class="text-center">' . $vS['name_size'] . '</td>';
                                // }

                                $quantity_child = $vS['quantity'];
                                $even_quantity_child = '';
                                $odd_quantity_child = '';

                                $even_quantity_bale_child = '';
                                $odd_quantity_bale_child = '';

                                if ($quantity_child_sheet > 0) {
                                    $quantity_sheet = $quantity_child / $quantity_child_sheet;
                                    $even_quantity_child = floor($quantity_sheet);
                                    $quantity_ceil = ceil($quantity_sheet);
                                    $odd_quantity_child = $quantity_ceil - $even_quantity_child;
                                }


                                if ($quantity_sheet_bale > 0) {
                                    $quantity_bale = $value['quantity'] / $quantity_sheet_bale;
                                    $even_quantity_bale_child = floor($quantity_bale);
                                    $quantity_ceil_bale = ceil($quantity_bale);
                                    $odd_quantity_bale_child = $quantity_ceil_bale - $even_quantity_bale_child;
                                }

                                $trQuantityChild .= '<td class="text-center">' . $quantity_child . '</td>';
                                $trEvenQuantityChild .= '<td class="text-center">' . $even_quantity_child . '</td>';
                                $trOddQuantityChild .= '<td class="text-center">' . $odd_quantity_child . '</td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center">' . $even_quantity_bale_child . '</td>';
                                $trOddQuantityBaleChild .= '<td class="text-center">' . $odd_quantity_bale_child . '</td>';
                            }
                        }

                        if (count($arrSize) < 10) {
                            $nS = 10 - count($arrSize);
                            for ($i = 0; $i < $nS; $i++) {
                                $trSize .= '<td class="text-center" style="width: 10%;"></td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                            }
                        }

                        $tableTem .= '<table nobr="true" class="" cellspacing="0" cellpadding="5" border="1">
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã KH</td>
                                <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                <td class="" style="width: 10%;">Qui Cách</td>
                                <td class="text-center" style="width: 10%;">ĐVT</td>
                                <td class="text-center" style="width: 10%;">Mã Đơn</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã ĐĐH</td>
                                <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                <td class="" style="width: 10%;">' . $mode . '</td>
                                <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                <td class="text-center" style="width: 10%;">Chỉ Lệnh</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="text-center" style="width: 10%;">Size SP</td>
                                <td class="text-center" style="width: 10%;">Size ĐC</td>
                                <td class="text-center" style="width: 10%;">Style Number</td>
                                <td class="text-center" style="width: 10%;">Color Name</td>
                                <td class="text-center" style="width: 10%;">Số Lượng</td>
                                <td class="text-center" style="width: 10%;">SL Tờ Chẵn</td>
                                <td class="text-center" style="width: 10%;">SL Tờ Lẻ</td>
                                <td class="text-center" style="width: 10%;">Kiện Chẵn</td>
                                <td class="text-center" style="width: 10%;">Kiện Lẻ</td>
                                <td class="text-center" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" style="">
                                ' . $trSize . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Số Lượng</td>
                                ' . $trQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Tờ Chẵn</td>
                                ' . $trEvenQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Tờ Lẻ</td>
                                ' . $trOddQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Kiện Chặn</td>
                                ' . $trEvenQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td style="width: 10%;">Kiện Lẻ</td>
                                ' . $trOddQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td style="width: 10%;">QC Kiểm</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;">Ngày Giao</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>
                        </table><div style="line-height: 0.0000000001em;"></div>';
                    } else if ($order['type_orders'] == ORDER_CHANGE_SIZE) {

                        $this->db->select('tbl_order_items_change_size.*');
                        $this->db->from('tbl_order_items_change_size');
                        $this->db->where('tbl_order_items_change_size.order_item_id', $order_item_id);
                        $this->db->order_by('tbl_order_items_change_size.number_size ASC');
                        $order_items_change_size = $this->db->get()->result_array();

                        $trSizeChild = '';
                        $trQuantityChild = '';
                        $trEvenQuantityChild = '';
                        $trOddQuantityChild = '';
                        $trEvenQuantityBaleChild = '';
                        $trOddQuantityBaleChild = '';

                        $arrSize = [];
                        if (!empty($order_items_change_size)) {
                            foreach ($order_items_change_size as $kS => $vS) {
                                $arrSize[] = $vS['number_size'];

                                $quantity_child = $vS['quantity'];
                                $even_quantity_child = $vS['even_sheet'];
                                $odd_quantity_child = $vS['odd_sheet'];
                                $even_quantity_bale_child = $vS['even_bale'];
                                $odd_quantity_bale_child = $vS['odd_bale'];

                                $trSizeChild .= '<td class="text-center" style="width: 10%;">' . $vS['number_size'] . '</td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;">' . $quantity_child . '</td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;">' . $even_quantity_child . '</td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;">' . $odd_quantity_child . '</td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;">' . $even_quantity_bale_child . '</td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;">' . $odd_quantity_bale_child . '</td>';
                            }
                        }

                        if (count($arrSize) < 10) {
                            $nS = 10 - count($arrSize);
                            for ($i = 1; $i < $nS; $i++) {
                                $trSizeChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trEvenQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                                $trOddQuantityBaleChild .= '<td class="text-center" style="width: 10%;"></td>';
                            }
                        }

                        $tableTem .= '<table nobr="true" class="" cellspacing="0" cellpadding="5" border="1">
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã KH</td>
                                <td class="" style="width: 15%;">' . $order['code_customer'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;">Tên Gọi Của Khách Hàng</td>
                                <td class="" style="width: 10%;">Qui Cách</td>
                                <td class="text-center" style="width: 10%;">ĐVT</td>
                                <td class="text-center" style="width: 10%;">Mã Đơn</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Mã ĐĐH</td>
                                <td class="" style="width: 15%;">' . $order['reference_no'] . '</td>
                                <td class="text-center" colspan="3" style="width: 35%;font-family: '.$font.';font-size:11px">' . $product_name_customer . '</td>
                                <td class="" style="width: 10%;">' . $mode . '</td>
                                <td class="text-center" style="width: 10%;">' . $unit['unit'] . '</td>
                                <td class="text-center" style="width: 10%;">Chỉ Lệnh</td>
                                <td class="" style="width: 10%;"></td>
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td class="" style="width: 10%;">Size Đối Chiếu</td>
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Số Size</td>
                                ' . $trSizeChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Số Lượng</td>
                                ' . $trQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Tờ Chẵn</td>
                                ' . $trEvenQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Tờ Lẻ</td>
                                ' . $trOddQuantityChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Kiện Chẵn</td>
                                ' . $trEvenQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" style="">
                                <td class="" style="width: 10%;">Kiện Lẻ</td>
                                ' . $trOddQuantityBaleChild . '
                            </tr>
                            <tr nobr="true" class="bold" style="">
                                <td style="width: 10%;">QC Kiểm</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;">Ngày Giao</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>
                        </table><div style="line-height: 0.0000000001em;"></div>';
                    }
                }
            }
        }

        $data = [];
        $data['title'] = lang('tnh_print_tem');
        if ($type_print == 1) {
            $data['type'] = 'L';
        }  else {
            $data['type'] = 'P';
        }

        $data['img'] = '';

        // print_arrays($tableTem);
        ob_start();
        stylePdf();
        echo $tableTem;
        $content = ob_get_contents();
        ob_end_clean();

        $data['showHeader'] = 'hide';
        $data['content'] = $content;
        $pdf = @print_pdf_tem($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }
}