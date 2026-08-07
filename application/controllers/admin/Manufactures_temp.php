<?php

// header('Content-Type: text/html; charset=utf-8');

use function GuzzleHttp\json_encode;

defined('BASEPATH') or exit('No direct script access allowed');

class Manufactures_temp extends AdminController
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
        $this->load->model('quality_control_model');
        $this->load->model('transfer_model');
        $this->load->model('check_warehouses_model');
        $this->load->library('ciqrcode');
        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('products');
        $this->datetime_now = time();
        $this->tnh = true;
        $this->perAddProductionsPlan = has_permission('manufactures_productions_plan', '', 'create');
    }

    public function getManufacturesPlan()
    {
        // die;
        $dateNow = date('Y-m-d');
        $is_type = 2;
        $this->db->simple_query('SET SESSION group_concat_max_len=18446744073709551615');

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $customer_search_manufactures = $this->input->post('customer_search_manufactures');
        $start_date_search_manufactures = $this->input->post('start_date_search_manufactures');
        $end_date_search_manufactures = $this->input->post('end_date_search_manufactures');
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

        $tbOrdersItems = "(
            SELECT
                tb_items.item_id as item_id,
                SUM(tb_items.total_quantity_item) as total_quantity_item,
                SUM(tb_items.quantity_plan) as quantity_plan,
                GROUP_CONCAT(tb_items.order_detail SEPARATOR 'AAA') as order_detail,
                SUM(tb_items.quantity_keep_trans) as quantity_keep_trans
            FROM (
                (
                    SELECT
                        tbl_order_items.item_id as item_id,
                        SUM(IF((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - COALESCE(tb_keep.quantity_net, 0) - COALESCE(tb_keep_trans.quantity, 0) > 0, (tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - COALESCE(tb_keep.quantity_net, 0) - COALESCE(tb_keep_trans.quantity, 0), 0)) as total_quantity_item,

                        SUM(tbl_order_items.quantity_plan) as quantity_plan,
                        GROUP_CONCAT(tbl_orders.reference_no,'|||',((tbl_order_item_shippings.quantity_shipping * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_item_shippings.quantity_plan_item - (COALESCE(tb_keep.quantity_net, 0)) - COALESCE(tb_keep_trans.quantity, 0))  SEPARATOR 'FF') as order_detail,
                        SUM(COALESCE(tb_keep_trans.quantity, 0)) as quantity_keep_trans
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
                        GROUP_CONCAT(tbl_business_plan.reference_no,'|||',(tbl_business_plan_items_date.quantity - tbl_business_plan_items_date.quantity_plan_item)  SEPARATOR 'FF') as order_detail,
                        0 as quantity_keep_trans
                    FROM tbl_business_plan
                    INNER JOIN tbl_business_plan_items ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
                    INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
                    WHERE tbl_business_plan_items.quantity > 0 AND tbl_business_plan.productions_plan_preventive_id = 0 AND tbl_business_plan_items_date.date >= '$start_date_search_manufactures' AND tbl_business_plan_items_date.date <= '$end_date_search_manufactures' AND tbl_business_plan.status = 'approved' $whereBusinessPlan
                    GROUP BY tbl_business_plan_items.items_id
                )
            ) tb_items
            GROUP BY tb_items.item_id
        ) tb_sum_items";

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

        //more
        if ($is_type == 2) {
            $quantityW = "(
                SELECT
                    0 as id_items,
                    0 as quantity_warehouse
            ) tb_quantity_warehouse";

            $tbPreventive = "(
                SELECT
                    0 as item_id,
                    0 as total_quantity_item
            ) tb_perventive";

            $tbProductionsOrdersDetailNotPreventive = '(
                SELECT
                    0 as items_id,
                    0 as quantity_po
            ) tb_productions_orders_detail';

            $tbProductionsOrdersDetailPreventive = '(
                SELECT
                    0 as items_id,
                    0 as quantity_po
            ) tb_productions_orders_detail_perventive';
        }

        // print_arrays($quantityW);
        // '(coalesce(tb_sum_items.total_quantity_item, 0) - coalesce(tb_sum_items.quantity_plan, 0) - coalesce(tb_perventive.total_quantity_item, 0) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
        $aColumns = [
            'tbl_products.id as id',
            'tbl_products.images as images',
            'tbl_products.code as code',
            'tbl_products.name as name',
            '0 as actions',
            'tb_sum_items.order_detail as order_detail',
            'tb_sum_items.total_quantity_item as quantity_orders',
            // 'tb_sum_items.quantity_plan - COALESCE(tb_productions_orders_detail.quantity_po, 0) as quantity_manufactures',
            'IF(COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0, COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0), 0) as quantity_preventive',
            'tb_quantity_warehouse.quantity_warehouse as quantity_warehouses',
            // '(tb_sum_items.total_quantity_item - (coalesce(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0)) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
            '(tb_sum_items.total_quantity_item - (IF(COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0, COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0), 0)) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
            '0 as ton_tong',
            '0 as ton_thuc_te',
            'tbl_products.allowable as ton_cho_phep',
            '0 as ton_da_mo_lenh',
            '0 as ton_san_nhap_kho_chua_duyet',
            '0 as ton_san_da_nhap_kho',
            '0 as ton_tren_truyen',
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

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_sum_items.quantity_plan as quantity_plan',
            'tb_sum_items.quantity_keep_trans as quantity_keep_trans',
            'tbl_products.total_business_plan as total_business_plan',
            'tbl_products.total_transfer_business as total_transfer_business',
        ], 'ORDER BY quantity_need_manufactures DESC', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity_orders = 0;
        $total_quantity_manufactures = 0;
        $total_quantity_preventive = 0;
        $total_quantity_warehouses = 0;
        $total_quantity_need_manufactures = 0;

        $total_ton_tong = 0;
        $total_ton_thuc_te = 0;
        $total_ton_cho_phep = 0;
        $total_ton_da_mo_lenh = 0;
        $total_ton_san_nhap_kho_chua_duyet = 0;
        $total_ton_san_da_nhap_kho = 0;
        $total_ton_tren_truyen = 0;

        if (!empty($rResult) && $is_type == 2) {
            $arrProductId = array_column($rResult, 'id');
            if (!empty($arrProductId)) {
                $arrProductId = array_unique($arrProductId);

                $quantityW = "
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
                    AND tblwarehouse_items.id_items IN (".implode(',', $arrProductId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listQuantityW = $this->db->query($quantityW)->result_array();
                if (!empty($listQuantityW)) {
                    $listQuantityW = array_reduce($listQuantityW, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }

                //
                $tbPreventive = "
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
                    AND tbl_business_plan_items.items_id IN (".implode(',', $arrProductId).")
                    GROUP BY tbl_business_plan_items.items_id
                ";
                $listPreventive = $this->db->query($tbPreventive)->result_array();
                if (!empty($listPreventive)) {
                    $listPreventive = array_reduce($listPreventive, function($carry, $item) {
                        $carry[$item['item_id']] = $item;
                        return $carry;
                    });
                }

                //
                $tbProductionsOrdersDetailNotPreventive = '
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
                    AND tbl_productions_orders_items.items_id IN ('.implode(",", $arrProductId).')
                    GROUP BY tbl_productions_orders_items.items_id
                ';
                $listPONotPreventive = $this->db->query($tbProductionsOrdersDetailNotPreventive)->result_array();
                if (!empty($listPONotPreventive)) {
                    $listPONotPreventive = array_reduce($listPONotPreventive, function($carry, $item) {
                        $carry[$item['items_id']] = $item;
                        return $carry;
                    });
                }
        
                $tbProductionsOrdersDetailPreventive = '
                    SELECT
                        tbl_productions_orders_items.items_id as items_id,
                        SUM(tbl_purchase_products.total_quantity) as quantity_po
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
                    INNER JOIN  tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                    INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_orders_details.object_id
                    WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0 AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1) AND tbl_purchase_products.warehouseman_id > 0
                    AND tbl_productions_orders_items.items_id IN ('.implode(",", $arrProductId).')
                    GROUP BY tbl_productions_orders_items.items_id 
                ';
                $listPOPreventive = $this->db->query($tbProductionsOrdersDetailPreventive)->result_array();
                if (!empty($listPOPreventive)) {
                    $listPOPreventive = array_reduce($listPOPreventive, function($carry, $item) {
                        $carry[$item['items_id']] = $item;
                        return $carry;
                    });
                }

                //tồn thực tế
                $quantityWTon = "
                    SELECT
                        tblwarehouse_items.id_items as id_items,
                        SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
                    FROM tblwarehouse_items
                    INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                    WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_HOLD . ") AND tbllocaltion_warehouses.stage_id = 0 
                    AND tblwarehouse_items.id_items IN (".implode(',', $arrProductId).")
                    GROUP BY tblwarehouse_items.id_items
                ";
                $listQuantityWTon = $this->db->query($quantityWTon)->result_array();
                if (!empty($listQuantityWTon)) {
                    $listQuantityWTon = array_reduce($listQuantityWTon, function($carry, $item) {
                        $carry[$item['id_items']] = $item;
                        return $carry;
                    });
                }

                //trên truyền
                $this->db->select('
                    tbl_order_items_transfer.item_id as item_id,
                    SUM(tbl_order_items_transfer.quantity) as quantity
                ', false);
                $this->db->from('tbl_order_items_transfer');
                $this->db->where_in('tbl_order_items_transfer.item_id', $arrProductId, false);
                $this->db->group_by('tbl_order_items_transfer.item_id');
                $listOrderItemsTransfer = $this->db->get()->result_array();
                if (!empty($listOrderItemsTransfer)) {
                    $listOrderItemsTransfer = array_reduce($listOrderItemsTransfer, function($carry, $item) {
                        $carry[$item['item_id']] = $item;
                        return $carry;
                    });
                }

                //tồn sẳn nhập kho chưa duyệt
                $this->db->select('
                    tbl_purchase_product_items.item_id as item_id,
                    SUM(tbl_purchase_product_items.quantity) as quantity
                ', false);
                $this->db->from('tbl_purchase_products');
                $this->db->join('tbl_purchase_product_items', 'tbl_purchase_product_items.purchase_product_id = tbl_purchase_products.id');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id');
                $this->db->where_in('tbl_purchase_product_items.item_id', $arrProductId, false);
                $this->db->where('tbl_purchase_products.type_business_plan', 0);
                $this->db->where('tbl_purchase_products.is_errors', 0);
                $this->db->where('tbl_purchase_products.warehouseman_id', 0);
                $this->db->where('tbl_productions_orders_details.object_type', 'business_plan');
                $this->db->group_by('tbl_purchase_product_items.item_id');
                $listPurchaseProducts = $this->db->get()->result_array();
                if (!empty($listPurchaseProducts)) {
                    $listPurchaseProducts = array_reduce($listPurchaseProducts, function($carry, $item) {
                        $carry[$item['item_id']] = $item;
                        return $carry;
                    });
                }

                //Tồn sẳn đã nhập kho
                // $queryStockAvailable = "
                //     SELECT
                //         tblwarehouse_items.id_items as id_items,
                //         SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
                //     FROM tblwarehouse_items
                //     INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                //     WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_HOLD . ", ".WAREHOUSES_ERRORS.", ".WAREHOUSES_CAPACITY.") AND tbllocaltion_warehouses.stage_id = 0 AND EXISTS (
                //         SELECT 1
                //         FROM tbl_productions_orders_details
                //         WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = 'business_plan'
                //     )
                //     AND tblwarehouse_items.id_items IN (".implode(',', $arrProductId).")
                //     GROUP BY tblwarehouse_items.id_items
                // ";
                // $listStockAvailable = $this->db->query($queryStockAvailable)->result_array();
                // if (!empty($listStockAvailable)) {
                //     $listStockAvailable = array_reduce($listStockAvailable, function($carry, $item) {
                //         $carry[$item['id_items']] = $item;
                //         return $carry;
                //     });
                // }
                $listStockAvailable = listStockAvailable($arrProductId);

                //tồn sãn đã duyệt kho
                $listApprovedStock = listApprovedStock($arrProductId);
            }
        }
        
        foreach ($rResult as $key => $aRow) {
            $start++;
            $product_id = $aRow['id'];

            if ($is_type == 2) {
                $dtQuantityW = $listQuantityW[$product_id] ?? null;
                $dtPreventive = $listPreventive[$product_id] ?? null;
                $dtPONotPreventive = $listPONotPreventive[$product_id] ?? null;
                $dtPOPreventive = $listPOPreventive[$product_id] ?? null;

                $_QuantityW = $dtQuantityW['quantity_warehouse'] ?? 0;
                $_Preventive = $dtPreventive['total_quantity_item'] ?? 0;
                $_PONotPreventive = $dtPONotPreventive['quantity_po'] ?? 0;
                $_POPreventive = $dtPOPreventive['quantity_po'] ?? 0;

                $quantity_plan = $aRow['quantity_plan'];
                $quantity_orders = $aRow['quantity_orders'];

                $quantity_manufactures = $quantity_plan - $_PONotPreventive;
                $quantity_preventive = ($_Preventive - $_POPreventive) > 0 ? $_Preventive - $_POPreventive : 0;
                $quantity_warehouses = $_QuantityW;
                $quantity_need_manufactures = $quantity_orders - $quantity_preventive - $quantity_warehouses;

                $aRow['quantity_manufactures'] = $quantity_manufactures;
                $aRow['quantity_preventive'] = $quantity_preventive;
                $aRow['quantity_warehouses'] = $quantity_warehouses;
                $aRow['quantity_need_manufactures'] = $quantity_need_manufactures;
            }

            $dtQuantityWTon = $listQuantityWTon[$product_id] ?? null;
            $dtOrderItemsTransfer = $listOrderItemsTransfer[$product_id] ?? null;
            $dtPurchaseProducts = $listPurchaseProducts[$product_id] ?? null;
            $dtStockAvailable = $listStockAvailable[$product_id] ?? null; // tồn sẳn đã nhập kho
            $dtApprovedStock = $listApprovedStock[$product_id] ?? null; // tồn đã nhập kho duyệt tồn sẳn

            $aRow['ton_thuc_te'] = $dtQuantityWTon['quantity_warehouse'] ?? 0;
            // $aRow['ton_da_mo_lenh'] = $aRow['quantity_keep_trans'] ?? 0;
            $aRow['ton_da_mo_lenh'] = $dtOrderItemsTransfer['quantity'] ?? 0;
            $aRow['ton_tong'] = $aRow['ton_thuc_te'] + $aRow['ton_da_mo_lenh'];
            $aRow['ton_san_nhap_kho_chua_duyet'] = $dtPurchaseProducts['quantity'] ?? 0;
            $aRow['ton_san_da_nhap_kho'] = $dtStockAvailable['quantity_warehouse'] ?? 0;
            $ton_tren_truyen =  $aRow['total_business_plan'] - $aRow['total_transfer_business'] - ($dtApprovedStock['quantity'] ?? 0);
            $ton_tren_truyen = $ton_tren_truyen < 0 ? 0 : $ton_tren_truyen;
            $aRow['ton_tren_truyen'] = $ton_tren_truyen;

            $is_check = 1;
            if ($aRow['ton_cho_phep'] > 0) {
                if (($aRow['ton_tren_truyen'] + $aRow['ton_san_da_nhap_kho']) > $aRow['ton_cho_phep']) {
                    $is_check = 0;
                }
            }

            $images = base_url('assets/images/tnh/no_image.png');
            if ($aRow['images']) {
                $images = base_url('uploads/products/' . $aRow['images']);
            }

            if ($is_check == 1) {
                $row[0] = '<div class="text-center">
                    <div class="">
                        <input name="product_id[]" type="checkbox" onchange="changeProductCb(this)" id="product_id_' . $product_id . '" value="' . $product_id . '">
                        <label for="product_id_' . $product_id . '"></label>
                    </div>
                </div>';
            } else {
                $row[0] = '';
            }
            
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

            // //Số lượng đơn hàng
            // $row[6] = '<div class="text-center">' . formatNumber($aRow['quantity_orders']) . '</div>';
            // //Số lượng đang SX theo đơn
            // $row[7] = '<div class="text-center">' . formatNumber($aRow['quantity_manufactures']) . '</div>';
            // //Số lượng đang SX dự trù
            // $row[8] = '<div class="text-center">' . formatNumber($aRow['quantity_preventive']) . '</div>';
            // //Số lượng hàng sẳn trong kho
            // $row[9] = '<div class="text-center">
            //     <a href="'.base_url('admin/manufactures_temp/show_warehouses_plan/'.$product_id).'" class="tnh-modal" data-target="#myModal" data-toggle="modal">' . formatNumber($aRow['quantity_warehouses']) . '</a>
            // </div>';
            // //Số lượng cần sản xuất
            // $row[10] = '<div class="text-center">' . ($aRow['quantity_need_manufactures'] > 0 ? formatNumber($aRow['quantity_need_manufactures']) : '') . '</div>';

            // //Số lượng tồn tổng
            // $row[11] = '<div class="text-center">' . formatNumber($aRow['ton_tong']) . '</div>';
            // $row[12] = '<div class="text-center">' . formatNumber($aRow['ton_thuc_te']) . '</div>';
            // $row[13] = '<div class="text-center">' . formatNumber($aRow['ton_cho_phep']) . '</div>';
            // $row[14] = '<div class="text-center">' . formatNumber($aRow['ton_da_mo_lenh']) . '</div>';
            // $row[15] = '<div class="text-center">' . formatNumber($aRow['ton_san_nhap_kho_chua_duyet']) . '</div>';
            // $row[16] = '<div class="text-center">' . formatNumber($aRow['ton_san_da_nhap_kho']) . '</div>';
            // $row[17] = '<div class="text-center">' . formatNumber($aRow['ton_tren_truyen']) . '</div>';

            //Số lượng đơn hàng
            $row[6] = '<div class="text-center">' . formatNumber($aRow['quantity_orders']) . '</div>';

            //Số lượng cần sản xuất
            $row[7] = '<div class="text-center">' . ($aRow['quantity_need_manufactures'] > 0 ? formatNumber($aRow['quantity_need_manufactures']) : '') . '</div>';

            //Số lượng hàng sẳn trong kho
            $row[8] = '<div class="text-center">
                <a href="'.base_url('admin/manufactures_temp/show_warehouses_plan/'.$product_id).'" class="tnh-modal" data-target="#myModal" data-toggle="modal">' . formatNumber($aRow['quantity_warehouses']) . '</a>
            </div>';

            //số lượng tồn trên truyền
            $row[9] = '<div class="text-center">' . formatNumber($aRow['ton_tren_truyen']) . '</div>';

            //số lượng tồn sẵn nhập kho chưa duyệt
            $row[10] = '<div class="text-center">' . formatNumber($aRow['ton_san_nhap_kho_chua_duyet']) . '</div>';

            //Số lượng tồn tổng
            $row[11] = '<div class="text-center">' . formatNumber($aRow['ton_tong']) . '</div>';

            $row[12] = '<div class="text-center">' . formatNumber($aRow['ton_cho_phep']) . '</div>';

            $row[13] = '<div class="text-center">' . formatNumber($aRow['ton_da_mo_lenh']) . '</div>';
            //Số lượng đang SX dự trù
            $row[14] = '<div class="text-center">' . formatNumber($aRow['quantity_preventive']) . '</div>';

            $row[15] = '<div class="text-center">' . formatNumber($aRow['ton_thuc_te']) . '</div>';
            $row[16] = '<div class="text-center">' . formatNumber($aRow['ton_san_da_nhap_kho']) . '</div>';

            $total_quantity_orders += $aRow['quantity_orders'];
            $total_quantity_manufactures += $aRow['quantity_manufactures'];
            $total_quantity_preventive += $aRow['quantity_preventive'];
            $total_quantity_warehouses += $aRow['quantity_warehouses'];
            $total_quantity_need_manufactures += ($aRow['quantity_need_manufactures'] > 0 ? ($aRow['quantity_need_manufactures']) : 0);
            $total_ton_san_nhap_kho_chua_duyet+= ($aRow['ton_san_nhap_kho_chua_duyet'] > 0 ? ($aRow['ton_san_nhap_kho_chua_duyet']) : 0);
            $total_ton_san_da_nhap_kho+= ($aRow['ton_san_da_nhap_kho'] > 0 ? ($aRow['ton_san_da_nhap_kho']) : 0);
            $total_ton_tren_truyen+= ($aRow['ton_tren_truyen'] > 0 ? ($aRow['ton_tren_truyen']) : 0);
            
            $total_ton_tong = $aRow['ton_tong'];
            $total_ton_thuc_te = $aRow['ton_thuc_te'];
            $total_ton_cho_phep = $aRow['ton_cho_phep'];
            $total_ton_da_mo_lenh = $aRow['ton_da_mo_lenh'];
            $output['aaData'][] = $row;
        }

        $output['total_quantity_orders'] = $total_quantity_orders;
        $output['total_quantity_manufactures'] = $total_quantity_manufactures;
        $output['total_quantity_preventive'] = $total_quantity_preventive;
        $output['total_quantity_warehouses'] = $total_quantity_warehouses;
        $output['total_quantity_need_manufactures'] = $total_quantity_need_manufactures;
        $output['total_ton_tong'] = $total_ton_tong;
        $output['total_ton_thuc_te'] = $total_ton_thuc_te;
        $output['total_ton_cho_phep'] = $total_ton_cho_phep;
        $output['total_ton_da_mo_lenh'] = $total_ton_da_mo_lenh;
        $output['total_ton_san_nhap_kho_chua_duyet'] = $total_ton_san_nhap_kho_chua_duyet;
        $output['total_ton_san_da_nhap_kho'] = $total_ton_san_da_nhap_kho;
        $output['total_ton_tren_truyen'] = $total_ton_tren_truyen;
        echo json_encode($output);
    }

    public function getManufacturesPlanNN()
    {
        // die;
        $dateNow = date('Y-m-d');
        $this->db->simple_query('SET SESSION group_concat_max_len=18446744073709551615');

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $customer_search_manufactures = $this->input->post('customer_search_manufactures');
        $start_date_search_manufactures = $this->input->post('start_date_search_manufactures');
        $end_date_search_manufactures = $this->input->post('end_date_search_manufactures');
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

        // $tbOrdersItems = "(
        //     SELECT
        //         tb_items.item_id as item_id,
        //         SUM(tb_items.total_quantity_item) as total_quantity_item,
        //         SUM(tb_items.quantity_plan) as quantity_plan,
        //         GROUP_CONCAT(tb_items.order_detail SEPARATOR 'AAA') as order_detail
        //     FROM (
        //         (
        //             SELECT
        //                 tbl_order_items.item_id as item_id,
        //                 SUM(IF((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - $slKeep - $keepTranferBusinessItem > 0, (tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - $slKeep - $keepTranferBusinessItem, 0)) as total_quantity_item,

        //                 SUM(tbl_order_items.quantity_plan) as quantity_plan,
        //                 GROUP_CONCAT(tbl_orders.reference_no,'|||',((tbl_order_item_shippings.quantity_shipping * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_item_shippings.quantity_plan_item - ($slKeep) - $keepTranferBusinessItem)  SEPARATOR 'FF') as order_detail
        //             FROM tbl_orders
        //             INNER JOIN tbl_order_items ON tbl_order_items.order_id = tbl_orders.id
        //             INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id
        //             INNER JOIN tbl_order_item_shippings ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
        //             WHERE tbl_order_items.type_item = 'products' AND tbl_order_items.total_quantity_item > 0 AND tbl_order_item_shippings.date_shipping >= '$start_date_search_manufactures' AND tbl_order_item_shippings.date_shipping <= '$end_date_search_manufactures' AND tbl_orders.status = 'approved' $whereOrders AND tbl_orders.is_cancel = 0
        //             GROUP BY tbl_order_items.item_id
        //         )
        //         UNION ALL
        //         (
        //             SELECT
        //                 tbl_business_plan_items.items_id as item_id,
        //                 SUM(tbl_business_plan_items.quantity - tbl_business_plan_items.quantity_plan) as total_quantity_item,
        //                 SUM(tbl_business_plan_items.quantity_plan) as quantity_plan,
        //                 GROUP_CONCAT(tbl_business_plan.reference_no,'|||',(tbl_business_plan_items_date.quantity - tbl_business_plan_items_date.quantity_plan_item)  SEPARATOR 'FF') as order_detail
        //             FROM tbl_business_plan
        //             INNER JOIN tbl_business_plan_items ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
        //             INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
        //             WHERE tbl_business_plan_items.quantity > 0 AND tbl_business_plan.productions_plan_preventive_id = 0 AND tbl_business_plan_items_date.date >= '$start_date_search_manufactures' AND tbl_business_plan_items_date.date <= '$end_date_search_manufactures' AND tbl_business_plan.status = 'approved' $whereBusinessPlan
        //             GROUP BY tbl_business_plan_items.items_id
        //         )
        //     ) tb_items
        //     GROUP BY tb_items.item_id
        // ) tb_sum_items";
        // print_arrays($tbOrdersItems);

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

        // print_arrays($quantityW);

         // '(coalesce(tb_sum_items.total_quantity_item, 0) - coalesce(tb_sum_items.quantity_plan, 0) - coalesce(tb_perventive.total_quantity_item, 0) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
        $aColumns = [
            'tbl_products.id as id',
            'tbl_products.images as images',
            'tbl_products.code as code',
            'tbl_products.name as name',
            '0 as actions',
            'tb_sum_items.order_detail as order_detail',
            'tb_sum_items.total_quantity_item as quantity_orders',
            // 'tb_sum_items.quantity_plan - COALESCE(tb_productions_orders_detail.quantity_po, 0) as quantity_manufactures',
            // 'IF(COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0, COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0), 0) as quantity_preventive',
            // 'tb_quantity_warehouse.quantity_warehouse as quantity_warehouses',
            // // '(tb_sum_items.total_quantity_item - (coalesce(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0)) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
            // '(tb_sum_items.total_quantity_item - (IF(COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0, COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0), 0)) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',

            'tb_sum_items.quantity_plan  as quantity_manufactures',
            '0 as quantity_preventive',
            'tb_quantity_warehouse.quantity_warehouse as quantity_warehouses',
            '(tb_sum_items.total_quantity_item - 0 - 0 - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
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
            // 'LEFT JOIN ' . $tbProductionsOrdersDetailNotPreventive . ' ON tb_productions_orders_detail.items_id = tbl_products.id',
            // 'LEFT JOIN ' . $tbProductionsOrdersDetailPreventive . ' ON tb_productions_orders_detail_perventive.items_id = tbl_products.id',
        ];

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            array_push($where, 'AND tbl_products.id_branch IN ('.implode(',', $branch_staff).')');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'COALESCE(tb_perventive.total_quantity_item, 0) as _total_quantity_item',
        ], 'ORDER BY quantity_need_manufactures DESC', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity_orders = 0;
        $total_quantity_manufactures = 0;
        $total_quantity_preventive = 0;
        $total_quantity_warehouses = 0;
        $total_quantity_need_manufactures = 0;

        $arrProductId = !empty($rResult) ? array_column($rResult, 'id') : null;
        if ($arrProductId) {
            $tbProductionsOrdersDetailNotPreventive = '
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
                ) AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1) AND tbl_purchase_products.warehouseman_id > 0 AND tbl_productions_orders_items.items_id IN ('.implode(',', $arrProductId).')
                GROUP BY tbl_productions_orders_items.items_id
            ';
            $dtProductionsOrdersDetailNotPreventive = $this->db->query($tbProductionsOrdersDetailNotPreventive)->result_array();
            if ($dtProductionsOrdersDetailNotPreventive) {
                $dtProductionsOrdersDetailNotPreventive = array_reduce($dtProductionsOrdersDetailNotPreventive, function($carry, $item) {
                    $carry[$item['items_id']] = $item;
                    return $carry;
                });
            }
    
            $tbProductionsOrdersDetailPreventive = '
                SELECT
                    tbl_productions_orders_items.items_id as items_id,
                    SUM(tbl_purchase_products.total_quantity) as quantity_po
                FROM tbl_productions_orders_details
                INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
                INNER JOIN  tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_orders_details.object_id
                WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0 AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1) AND tbl_purchase_products.warehouseman_id > 0 AND tbl_productions_orders_items.items_id IN ('.implode(',', $arrProductId).')
                GROUP BY tbl_productions_orders_items.items_id 
            ';
            $dtProductionsOrdersDetailPreventive = $this->db->query($tbProductionsOrdersDetailPreventive)->result_array();
            if ($dtProductionsOrdersDetailPreventive) {
                $dtProductionsOrdersDetailPreventive = array_reduce($dtProductionsOrdersDetailPreventive, function($carry, $item) {
                    $carry[$item['items_id']] = $item;
                    return $carry;
                });
            }
        }


        foreach ($rResult as $key => $aRow) {
            $start++;
            $product_id = $aRow['id'];

            $dataPODNotP = $dtProductionsOrdersDetailNotPreventive[$product_id] ?? null;
            $dataPODP = $tbProductionsOrdersDetailPreventive[$product_id] ?? null;

            $quantity_po_PODNotP = !empty($dataPODNotP['quantity_po']) ? $dataPODNotP['quantity_po'] : 0;
            $quantity_po_PODP = !empty($dataPODP['quantity_po']) ? $dataPODP['quantity_po'] : 0;
            $aRow['quantity_manufactures'] = $aRow['quantity_manufactures'] - $quantity_po_PODNotP;
            $_total_quantity_item = $aRow['_total_quantity_item'];

            $quantity_preventive = 0;
            if (($_total_quantity_item - $quantity_po_PODP) > 0) {
                $quantity_preventive = $_total_quantity_item - $quantity_po_PODP;
            } 
            $aRow['quantity_preventive'] = $quantity_preventive;
            $aRow['quantity_need_manufactures'] = $aRow['quantity_need_manufactures'] - $quantity_preventive;
            //


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

    public function modalDetailPlan()
    {
        $data = [];

        $product_id = $this->input->post('product_id');
        if (!empty($product_id)) {
            $this->db->select('id, code, name, allowable, total_business_plan, total_transfer_business');
            $this->db->from('tbl_products');
            $this->db->where_in('id', $product_id);
            $products = $this->db->get()->result_array();

            if (!empty($products)) {
                $listStockAvailable = listStockAvailable($product_id);
                $listApprovedStock = listApprovedStock($product_id);
                $error = '';
                foreach ($products as $key => $value) {
                    $code = $value['code'];

                    $dtStockAvailable = $listStockAvailable[$value['id']] ?? null; // tồn sẳn đã nhập kho
                    $dtApprovedStock = $listApprovedStock[$value['id']] ?? null; // tồn đã nhập kho duyệt tồn sẳn

                    $ton_san_da_nhap_kho = $dtStockAvailable['quantity_warehouse'] ?? 0;
                    $ton_tren_truyen =  $value['total_business_plan'] - $value['total_transfer_business'] - ($dtApprovedStock['quantity'] ?? 0);
                    $ton_tren_truyen = $ton_tren_truyen < 0 ? 0 : $ton_tren_truyen;
                    $ton_cho_phep = $value['allowable'];
                    if ($ton_cho_phep > 0) {
                        if (($ton_tren_truyen + $ton_san_da_nhap_kho) > $ton_cho_phep) {
                            $error.= '<div>Mặt hàng ['.$code.'] đã vượt số lượng tồn cho phép ['.formatNumber($ton_cho_phep).']</div>';
                        }
                    }
                }

                if (!empty($error)) {
                    refererModel($error); die;
                }
            }
        }

        $this->load->view('admin/manufactures/modal_detail_plan', $data);
    }

    public function getShowDetailManufactures()
    {
        $start_date_search = !empty($this->input->post('start_date_search')) ? ($this->input->post('start_date_search')) : date('Y-m-d');
        $end_date_search = !empty($this->input->post('end_date_search')) ? ($this->input->post('end_date_search')) : date('Y-m-d');

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $customer_search = $this->input->post('customer_search');
        $product_id = $this->input->post('product_id');

        $type_orders_search = $this->input->post('type_orders_search');
        $search_date_order = $this->input->post('search_date_order');

        $slKeep = "COALESCE((
            SELECT SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id AND tbltransfer_warehouse_detail.tranfer_business_item_id = 0
        ), 0)";

        $keepTranferBusinessItem = 'COALESCE((
            SELECT
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            WHERE tbl_tranfer_business_item.order_item_id = tbl_order_items.id
        ), 0)';

        $tbQuantity = "(
            SELECT
                tbl_order_items.order_id as order_id,
                tbl_order_items.item_id,
                tbl_order_item_shippings.date_shipping,
                SUM((tbl_order_item_shippings.quantity_shipping * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_item_shippings.quantity_plan_item - ($slKeep) - $keepTranferBusinessItem) as quantity_delivery
            FROM tbl_order_items
            INNER JOIN tbl_order_item_shippings ON tbl_order_items.id = tbl_order_item_shippings.order_item_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id
            WHERE tbl_order_items.type_item = 'products' AND tbl_order_items.item_id IN (" . $product_id . ") AND tbl_order_item_shippings.date_shipping >= '$start_date_search' AND tbl_order_item_shippings.date_shipping <= '$end_date_search'
            GROUP BY tbl_order_items.order_id, tbl_order_items.item_id
        ) tb_quantity";


        $aColumns = [
            'tbl_orders.id as id',
            'tbl_products.code as code',
            'tbl_products.name as name',
            'tbl_products.sample_cover_code as sample_cover_code',
            'tb_quantity.date_shipping as date_shipping',
            'tbl_orders.date as date',
            'tbl_type_orders.name as name_type_orders',
            'tblclients.company as company',
            'tbl_orders.reference_no as reference_no',
            'tb_quantity.quantity_delivery as quantity_delivery',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_orders';
        $where        = [];
        $filter = [];
        $join = [
            'INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id',
            'INNER JOIN ' . $tbQuantity . ' ON tbl_orders.id = tb_quantity.order_id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_quantity.item_id',
            'LEFT JOIN tbl_type_orders ON tbl_type_orders.id = tbl_orders.type_orders'
        ];

        if (!empty($customer_search)) {
            array_push($where, ' AND tbl_orders.customer_id =' . $customer_search);
        }

        if (!empty($type_orders_search)) {
            array_push($where, ' AND tbl_orders.type_orders IN (' . $type_orders_search . ')');
        }

        if (!empty($search_date_order)) {
            $search_date_order = explode(' - ', $search_date_order);
            $search_date_order_start = to_sql_date($search_date_order[0]) . ' 00:00:00';
            $search_date_order_end = to_sql_date($search_date_order[1]) . ' 23:59:59';

            array_push($where, ' AND tbl_orders.date >= "' . $search_date_order_start . '" AND tbl_orders.date <= "' . $search_date_order_end . '" ');
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            array_push($where, 'AND tbl_products.id_branch IN ('.implode(',', $branch_staff).')');
        }

        array_push($where, ' AND tb_quantity.quantity_delivery > 0 AND tbl_orders.status = "approved" AND tbl_orders.is_cancel = 0');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_type_orders.color as color_type_orders'
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $order_id = $aRow['id'];
            $row[0] = '<div class="text-center">
                <div class="">
                    <input type="checkbox" name="object_id[]" onchange="changeCheckboxObject(this)" class="object_id" id="object_id_' . $order_id . '" value="orders__' . $order_id  . '"><label for="object_id_' . $order_id . '"></label>
                </div>
            </div>';
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];
            $row[3] = $aRow['sample_cover_code'];
            $row[4] = !empty($aRow['date_shipping']) ? _dhau($aRow['date_shipping']) : '';
            $row[5] = '<div class="text-center">' . _d($aRow['date']) . '</div>';
            $color_type_orders = $aRow['color_type_orders'];
            $row[6] = '<div class="text-center"><span class="btn" style="background: ' . $color_type_orders . '; color: white; cursor: auto;">' . $aRow['name_type_orders'] . '<span></div>';
            $row[7] = $aRow['company'];
            $row[8] = $aRow['reference_no'];
            $row[9] = '<div class="text-center">' . formatNumber($aRow['quantity_delivery']) . '</div>';

            $totalQuantity += $aRow['quantity_delivery'];
            $output['aaData'][] = $row;
        }

        $output['totalQuantity'] = $totalQuantity;
        echo json_encode($output);
    }

    public function getShowDetailManufacturesBusinessPlan()
    {
        $start_date_search = !empty($this->input->post('start_date_search')) ? ($this->input->post('start_date_search')) : date('Y-m-d');
        $end_date_search = !empty($this->input->post('end_date_search')) ? ($this->input->post('end_date_search')) : date('Y-m-d');
        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $product_id = $this->input->post('product_id');
        $slKeep = 0;

        $tbQuantity = "(
            SELECT
                tbl_business_plan_items.items_id as item_id,
                tbl_business_plan_items.business_plan_id as business_plan_id,
                tbl_business_plan_items_date.date as date_delivery,
                SUM(tbl_business_plan_items_date.quantity - tbl_business_plan_items_date.quantity_plan_item) as quantity_delivery
            FROM tbl_business_plan_items
            INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
            WHERE tbl_business_plan_items.type_items = 'products' AND tbl_business_plan_items.items_id IN (" . $product_id . ") AND tbl_business_plan_items_date.date >= '$start_date_search' AND tbl_business_plan_items_date.date <= '$end_date_search'
            GROUP BY tbl_business_plan_items.business_plan_id, tbl_business_plan_items.items_id
        ) tb_quantity";

        $aColumns = [
            'tbl_business_plan.id as id',
            'tbl_products.code as code',
            'tbl_products.name as name',
            'tbl_products.sample_cover_code as sample_cover_code',
            'tb_quantity.date_delivery as date_delivery',
            'tbl_business_plan.date as date',
            'tbl_business_plan.reference_no as reference_no',
            'tbl_business_plan.plan_name as plan_name',
            'tb_quantity.quantity_delivery as quantity_delivery',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_business_plan';
        $where        = [
            ' AND tbl_business_plan.productions_plan_preventive_id = 0'
        ];
        $filter = [];
        $join = [
            'INNER JOIN ' . $tbQuantity . ' ON tbl_business_plan.id = tb_quantity.business_plan_id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_quantity.item_id',
        ];

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            array_push($where, 'AND tbl_products.id_branch IN ('.implode(',', $branch_staff).')');
        }

        array_push($where, ' AND tb_quantity.quantity_delivery > 0 AND tbl_business_plan.status = "approved"');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $business_plan_id = $aRow['id'];
            $row[0] = '<div class="text-center">
                <div class="">
                    <input type="checkbox" name="object_id[]" onchange="changeCheckboxObject(this)" class="object_id" id="object_id_' . $business_plan_id . '" value="business_plan__' . $business_plan_id  . '"><label for="object_id_' . $business_plan_id . '"></label>
                </div>
            </div>';
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];
            $row[3] = $aRow['sample_cover_code'];
            $row[4] = !empty($aRow['date_delivery']) ? _dhau($aRow['date_delivery']) : '';
            $row[5] = '<div class="text-center">' . _d($aRow['date']) . '</div>';
            $row[6] = $aRow['reference_no'];
            $row[7] = $aRow['plan_name'];
            $row[8] = '<div class="text-center">' . formatNumber($aRow['quantity_delivery']) . '</div>';

            $totalQuantity += $aRow['quantity_delivery'];
            $output['aaData'][] = $row;
        }

        $output['totalQuantity'] = $totalQuantity;
        echo json_encode($output);
    }

    public function getPurchasePlan()
    {
        $productions_plan_search = $this->input->post('productions_plan_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');

        $wherePL = '';
        $wherePurchase = '';
        $wherePQ = '';
        $whereTransfer = '';
        $whereImport = '';
        $wherePurchaseOrder = '';

        if (!empty($productions_plan_search)) {
            $wherePL .= ' AND tbl_productions_plan_bom.productions_plan_id IN (' . implode(',', $productions_plan_search) . ')';
            $wherePQ .= 'WHERE tbl_productions_plan_compensation.productions_plan_id IN (' . implode(',', $productions_plan_search) . ')';
            $whereTransfer .= ' AND tbltransfer_warehouse.productions_capacity_id IN (' . implode(',', $productions_plan_search) . ')';
            $wherePurchase .= ' AND exists (
                SELECT
                    tbl_purchases_plans.purchases_id
                FROM tbl_purchases_plans
                WHERE tbl_purchases_plans.purchases_id = tblpurchases.id AND tbl_purchases_plans.productions_plan_id IN (' . implode(',', $productions_plan_search) . ')
            )';
        }

        if (!empty($start_date_search) && empty($end_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            $wherePL.= ' AND exists (
                SELECT tbl_productions_plan.id
                FROM tbl_productions_plan
                WHERE tbl_productions_plan.date >= "'.$start_date_search.' 00:00:00" AND tbl_productions_plan_bom.productions_plan_id = tbl_productions_plan.id
            )';

            $wherePQ.= (!empty($wherePQ) ? ' AND ' : ' WHERE ').' exists (
                SELECT tbl_productions_plan.id
                FROM tbl_productions_plan
                WHERE tbl_productions_plan.date >= "'.$start_date_search.' 00:00:00" AND tbl_productions_plan_compensation.productions_plan_id = tbl_productions_plan.id
            )';

            $whereTransfer.= ' AND tbltransfer_warehouse.date >= "'.$start_date_search.'"';
            $wherePurchase.= ' AND tblpurchases.date >= "'.$start_date_search.' 00:00:00"';
            $whereImport.= ' AND tblimport.date >= "'.$start_date_search.'"';
            $wherePurchaseOrder.= ' AND tblpurchase_order.date >= "'.$start_date_search.'"';
        }

        if (!empty($end_date_search) && empty($start_date_search)) {
            $end_date_search = to_sql_date($end_date_search);

            $wherePL.= ' AND exists (
                SELECT tbl_productions_plan.id
                FROM tbl_productions_plan
                WHERE tbl_productions_plan.date <= "'.$end_date_search.' 23:59:59" AND tbl_productions_plan_bom.productions_plan_id = tbl_productions_plan.id
            )';

            $wherePQ.= (!empty($wherePQ) ? ' AND ' : ' WHERE ').' exists (
                SELECT tbl_productions_plan.id
                FROM tbl_productions_plan
                WHERE tbl_productions_plan.date <= "'.$end_date_search.' 23:59:59" AND tbl_productions_plan_compensation.productions_plan_id = tbl_productions_plan.id
            )';

            $whereTransfer.= ' AND tbltransfer_warehouse.date <= "'.$end_date_search.'"';
            $wherePurchase.= ' AND tblpurchases.date <= "'.$end_date_search.' 23:59:59"';
            $whereImport.= ' AND tblimport.date <= "'.$end_date_search.'"';
            $wherePurchaseOrder.= ' AND tblpurchase_order.date <= "'.$end_date_search.'"';
        }

        if (!empty($end_date_search) && !empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            $end_date_search = to_sql_date($end_date_search);

            $wherePL.= ' AND exists (
                SELECT tbl_productions_plan.id
                FROM tbl_productions_plan
                WHERE tbl_productions_plan.date >= "'.$start_date_search.' 00:00:00" AND tbl_productions_plan.date <= "'.$end_date_search.' 23:59:59" AND tbl_productions_plan_bom.productions_plan_id = tbl_productions_plan.id
            )';

            $wherePQ.= (!empty($wherePQ) ? ' AND ' : ' WHERE ').' exists (
                SELECT tbl_productions_plan.id
                FROM tbl_productions_plan
                WHERE tbl_productions_plan.date >= "'.$start_date_search.' 00:00:00" AND tbl_productions_plan.date <= "'.$end_date_search.' 23:59:59" AND tbl_productions_plan_compensation.productions_plan_id = tbl_productions_plan.id
            )';

            $whereTransfer.= ' AND tbltransfer_warehouse.date >= "'.$start_date_search.'" AND tbltransfer_warehouse.date <= "'.$end_date_search.'"';
            $wherePurchase.= ' AND tblpurchases.date >= "'.$start_date_search.' 00:00:00" AND tblpurchases.date <= "'.$end_date_search.' 23:59:59"';

            $whereImport.= ' AND tblimport.date >= "'.$start_date_search.'" AND tblimport.date <= "'.$end_date_search.'"';
            $wherePurchaseOrder.= ' AND tblpurchase_order.date >= "'.$start_date_search.'" AND tblpurchase_order.date <= "'.$end_date_search.'"';
        }


        $tbWarehouseProduct = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity,
                SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . "
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

        $tbWarehouseMaterials = "(
            SELECT
                tblwarehouse_items.id_items as id_items,
                SUM(tblwarehouse_items.product_quantity) as product_quantity,
                SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . "
            GROUP BY tblwarehouse_items.id_items
        ) tb_quantity_warehouse";

        // SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity
        $tbTransfer = "(
            SELECT
                tbltransfer_warehouse.productions_capacity_id,
                tbltransfer_warehouse_detail.type as type, 
                tbltransfer_warehouse_detail.id_items as id_items,
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity_unit
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse.productions_capacity_id > 0 $whereTransfer
            GROUP BY tbltransfer_warehouse_detail.type, tbltransfer_warehouse_detail.id_items
        ) tb_transfer";

        $tbImport = "(
            SELECT
                tblimport_items.type as type, 
                tblimport_items.product_id as id_items,
                SUM(tblimport_items.quantity_net) as quantity,
                SUM(tblimport_items.quantity_unit) as quantity_unit,
                SUM(tblimport_items.quantity_stock) as quantity_stock
            FROM tblimport
            INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
            WHERE tblimport.type_plan > 0 AND tblimport.warehouseman_id > 0 $whereImport 
            GROUP BY tblimport_items.product_id,tblimport_items.type
        ) tb_import";

        $tbProductionsPlanCompensation = "(
            SELECT
                tbl_productions_plan_compensation.item_id, 
                tbl_productions_plan_compensation.item_type,
                SUM(tbl_productions_plan_compensation.quantity_primary) as quantity_primary,
                SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
            FROM tbl_productions_plan_compensation
            $wherePQ
            GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type
        ) tb_productions_plan_compensation";

        //15-01-2025
        $tbWarehouseProduct = "(
            SELECT
                0 as id_items,
                0 as product_quantity,
                0 as product_quantity_unit
        ) tb_quantity_warehouse";

        $tbWarehouseMaterials = "(
            SELECT
                0 as id_items,
                0 as product_quantity,
                0 as product_quantity_unit
        ) tb_quantity_warehouse";

        $tbTransfer = "(
            SELECT
                0 as productions_capacity_id,
                0 as type, 
                0 as id_items,
                0 as quantity,
                0 as quantity_unit
        ) tb_transfer";

        $tbImport = "(
            SELECT
                0 as type, 
                0 as id_items,
                0 as quantity,
                0 as quantity_unit,
                0 as quantity_stock
        ) tb_import";

        $tbProductionsPlanCompensation = "(
            SELECT
                0 as item_id, 
                0 as item_type,
                0 as quantity_primary,
                0 as quantity_compensation
        ) tb_productions_plan_compensation";
        //

        // SUM(tbl_productions_plan_bom.quantity_primary + tbl_productions_plan_bom.quantity_compensation_primary + tbl_productions_plan_bom.quantity_compensation_sm_primary) as quantity_primary,
        // SUM(tbl_productions_plan_bom.quantity + tbl_productions_plan_bom.quantity_compensation + tbl_productions_plan_bom.quantity_compensation_sm) as quantity,
        // SUM((tbl_productions_plan_bom.quantity_primary + tbl_productions_plan_bom.quantity_compensation_primary + tbl_productions_plan_bom.quantity_compensation_sm_primary) * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit) as quantity_primary,
        $tbProductionsPlanBom = "(
            (
                SELECT 
                    tbl_productions_plan_bom.item_id as item_id,
                    tbl_productions_plan_bom.id as id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_productions_plan_bom.item_type as item_type,
                    SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0) as quantity_primary,
                    SUM(tbl_productions_plan_bom.quantity) as quantity,
                    tblunits.unit as unit_name,
                    unit_primary.unit as unit_primary_name,
                    tb_quantity_warehouse.product_quantity as quantity_inventory,
                    tb_transfer.quantity as quantity_transfer,
                    1 as exchange_standard_unit,
                    1 as exchange_unit,
                    tbl_products.allowable as allowable,
                    tb_import.quantity_stock as quantity_stock
                FROM tbl_productions_plan_bom
                INNER JOIN tbl_products ON tbl_products.id = tbl_productions_plan_bom.item_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                LEFT JOIN $tbWarehouseProduct ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_products.id AND tb_transfer.type = 'product'
                LEFT JOIN $tbImport ON tb_import.id_items = tbl_products.id AND tb_import.type = 'product'
                LEFT JOIN $tbProductionsPlanCompensation ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
                WHERE tbl_productions_plan_bom.item_type IN ('semi_products_outside') $wherePL
                GROUP BY tbl_productions_plan_bom.item_id
            )
            UNION ALL
            (
                SELECT 
                    tbl_productions_plan_bom.item_id as item_id,
                    tbl_productions_plan_bom.id as id,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_productions_plan_bom.item_type as item_type,
                    ((SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0)) * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit) as quantity_primary,
                    SUM(tbl_productions_plan_bom.quantity + tbl_productions_plan_bom.quantity_compensation + tbl_productions_plan_bom.quantity_compensation_sm) as quantity,
                    tblunits.unit as unit_name,
                    unit_stock.unit as unit_primary_name,
                    tb_quantity_warehouse.product_quantity as quantity_inventory,
                    tb_transfer.quantity as quantity_transfer,
                    tbl_materials.exchange_standard_unit as exchange_standard_unit,
                    tbl_materials.exchange_unit as exchange_unit,
                    tbl_materials.allowable as allowable,
                    tb_import.quantity_stock as quantity_stock
                FROM tbl_productions_plan_bom
                INNER JOIN tbl_materials ON tbl_materials.id = tbl_productions_plan_bom.item_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                LEFT JOIN tblunits unit_stock ON unit_stock.unitid = tbl_materials.standard_unit
                LEFT JOIN $tbWarehouseMaterials ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_materials.id AND tb_transfer.type = 'nvl'
                LEFT JOIN $tbImport ON tb_import.id_items = tbl_materials.id AND tb_import.type = 'nvl'
                LEFT JOIN $tbProductionsPlanCompensation ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
                WHERE tbl_productions_plan_bom.item_type IN ('materials') $wherePL
                GROUP BY tbl_productions_plan_bom.item_id
            )
        ) tb_cs";

        // print_arrays($tbProductionsPlanBom);
        $materials_search = $this->input->post('materials_search');

        $tbPurchase = "(
            SELECT
                IF(tblpurchases_items.type = 'nvl', 'materials', 'products') as type_item, 
                tblpurchases_items.type as type,
                tblpurchases_items.product_id as product_id,
                SUM(tblpurchases_items.quantity_net) as quantity_net
            FROM tblpurchases
            INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
            WHERE tblpurchases.is_plans = 1 AND tblpurchases_items.type IN ('product', 'nvl') $wherePurchase
            GROUP BY tblpurchases_items.type, tblpurchases_items.product_id
        ) tb_purchase";

        $aColumns = [
            'CONCAT(tb_cs.item_type, "__", tb_cs.item_id) as item_id',
            'tb_cs.item_code as item_code',
            'tb_cs.item_name as item_name',
            'tb_cs.unit_primary_name as unit_primary_name',
            'tb_cs.quantity_primary as quantity_primary',
            'tb_cs.quantity_inventory as quantity_inventory',
            'tb_cs.quantity_transfer as quantity_transfer',
            'coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) as quantity_purchase',
            'tb_cs.quantity_stock as quantity_stock',
            // '(coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_stock, 0)) - coalesce(tb_cs.quantity_transfer, 0)) as quantity_rest',
            '(coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (
                IF (coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_stock, 0) > 0, coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_stock, 0), 0)
            ) - coalesce(tb_cs.quantity_transfer, 0)) as quantity_rest',
            // '(coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_stock, 0) - coalesce(tb_cs.quantity_transfer, 0)) as quantity_rest',
            '0 as ton_tong',
            '0 as ton_thuc_te',
            'tb_cs.allowable as ton_cho_phep',
            '0 as ton_da_mua',
        ];
        $sIndexColumn = 'tb_cs.item_id';
        $sTable       = $tbProductionsPlanBom;
        $where        = [
            // " AND (coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_transfer, 0)) > 0"
            // " AND (coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_transfer, 0)) > 0"
        ];

        if (!empty($materials_search)) {
            array_push($where, ' AND (tb_cs.item_code like "%' . $materials_search . '%" OR tb_cs.item_name like "%' . $materials_search . '%")');
        }

        $filter = [];
        $join = [
            'LEFT JOIN ' . $tbPurchase . ' ON tb_purchase.type_item = tb_cs.item_type AND tb_purchase.product_id = tb_cs.item_id'
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tb_cs.item_type as _item_type',
            'tb_cs.item_id as _item_id',
            'tb_cs.exchange_unit as exchange_unit',
            'tb_cs.exchange_standard_unit as exchange_standard_unit',

        ], '', [], ['union_all' => true]);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantityPlan = 0;
        $totalQuantityWarehouse = 0;
        $totalQuantityPurchase = 0;
        $totalQuantityRest = 0;
        $totalQuantitytransfer = 0;
        $totalQuantityimport = 0;

        $totalTonTong = 0;
        $totalTonThucTe = 0;
        $totalTonChoPhep = 0;
        $totalTonDaMua = 0;

        if (!empty($rResult)) {
            $arrItemsId = [];
            $arrProductsId = [];
            foreach ($rResult as $key => $value) {
                $_item_type = $value['_item_type'];
                $_item_id = $value['_item_id'];
                if ($_item_type == 'materials') {
                    $arrItemsId[] = $_item_id;
                } else {
                    $arrProductsId[] = $_item_id;
                }
            }

            $whereWarehouses = [];
            if (!empty($arrItemsId)) {
                $whereWarehouses[] = " (tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.id_items IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereWarehouses[] = " (tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.id_items IN (".implode(',', $arrProductsId).")) ";
            }

            $whereWarehouses = ' AND ('.implode(' OR ', $whereWarehouses).')';

            $tbWarehouses = "
                SELECT
                    tblwarehouse_items.id_items as id_items,
                    tblwarehouse_items.type_items as type_items,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity,
                    SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
                FROM tblwarehouse_items
                WHERE tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . " $whereWarehouses
                GROUP BY tblwarehouse_items.id_items, tblwarehouse_items.type_items
            ";
            $dtWarehouses = $this->db->query($tbWarehouses)->result_array();
            if (!empty($dtWarehouses)) {
                $dtWarehouses = array_reduce($dtWarehouses, function($carry, $item) {
                    $carry[$item['type_items'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            $tbWarehousesCapacity = "
                SELECT
                    tblwarehouse_items.id_items as id_items,
                    tblwarehouse_items.type_items as type_items,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity,
                    SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
                FROM tblwarehouse_items
                WHERE tblwarehouse_items.warehouse_id = " . WAREHOUSES_CAPACITY . " $whereWarehouses
                GROUP BY tblwarehouse_items.id_items, tblwarehouse_items.type_items
            ";
            $dtWarehousesCapacity = $this->db->query($tbWarehousesCapacity)->result_array();
            if (!empty($dtWarehousesCapacity)) {
                $dtWarehousesCapacity = array_reduce($dtWarehousesCapacity, function($carry, $item) {
                    $carry[$item['type_items'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            //transfer

            $whereTr = [];
            if (!empty($arrItemsId)) {
                $whereTr[] = " (tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse_detail.id_items IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereTr[] = " (tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse_detail.id_items IN (".implode(',', $arrProductsId).")) ";
            }

            $whereTr = ' AND ('.implode(' OR ', $whereTr).')';

            $tbTransfer = "
                SELECT
                    tbltransfer_warehouse.productions_capacity_id,
                    tbltransfer_warehouse_detail.type as type, 
                    tbltransfer_warehouse_detail.id_items as id_items,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity_unit
                FROM tbltransfer_warehouse
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
                WHERE tbltransfer_warehouse.productions_capacity_id > 0 $whereTransfer $whereTr
                GROUP BY tbltransfer_warehouse_detail.type, tbltransfer_warehouse_detail.id_items
            ";
            $dtTransfer = $this->db->query($tbTransfer)->result_array();
            if (!empty($dtTransfer)) {
                $dtTransfer = array_reduce($dtTransfer, function($carry, $item) {
                    $carry[$item['type'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            //import
            $whereIm = [];
            if (!empty($arrItemsId)) {
                $whereIm[] = " (tblimport_items.type = 'nvl' AND tblimport_items.product_id IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereIm[] = " (tblimport_items.type = 'product' AND tblimport_items.product_id IN (".implode(',', $arrProductsId).")) ";
            }

            $whereIm = ' AND ('.implode(' OR ', $whereIm).')';

            $tbImport = "
                SELECT
                    tblimport_items.type as type, 
                    tblimport_items.product_id as id_items,
                    SUM(tblimport_items.quantity_net) as quantity,
                    SUM(tblimport_items.quantity_unit) as quantity_unit,
                    SUM(tblimport_items.quantity_stock) as quantity_stock
                FROM tblimport
                INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
                WHERE tblimport.type_plan > 0 AND tblimport.warehouseman_id > 0 $whereImport $whereIm
                GROUP BY tblimport_items.product_id, tblimport_items.type
            ";
            $dtImport = $this->db->query($tbImport)->result_array();
            if (!empty($dtImport)) {
                $dtImport = array_reduce($dtImport, function($carry, $item) {
                    $carry[$item['type'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            //plan compensation
            $whereCom = [];
            if (!empty($arrItemsId)) {
                $whereCom[] = " (tbl_productions_plan_compensation.item_type = 'materials' AND tbl_productions_plan_compensation.item_id IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereCom[] = " (tbl_productions_plan_compensation.item_type = 'semi_products' AND tbl_productions_plan_compensation.item_id IN (".implode(',', $arrProductsId).")) ";
            }

            $whereCom = ' AND ('.implode(' OR ', $whereCom).')';
            if (empty($wherePQ)) $wherePQ = "WHERE 1 ";

            $tbProductionsPlanCompensation = "
                SELECT
                    tbl_productions_plan_compensation.item_id, 
                    tbl_productions_plan_compensation.item_type,
                    SUM(tbl_productions_plan_compensation.quantity_primary) as quantity_primary,
                    SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
                FROM tbl_productions_plan_compensation
                $wherePQ
                GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type
            ";
            $dtProductionsPlanCompensation = $this->db->query($tbProductionsPlanCompensation)->result_array();
            if (!empty($dtProductionsPlanCompensation)) {
                $dtProductionsPlanCompensation = array_reduce($dtProductionsPlanCompensation, function($carry, $item) {
                    $carry[$item['item_type'].'__'.$item['item_id']] = $item;
                    return $carry;
                });
            }

            //purchase order

            $wherePo = [];
            if (!empty($arrItemsId)) {
                $wherePo[] = " (tblpurchase_order_items.type = 'nvl' AND tblpurchase_order_items.product_id IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $wherePo[] = " (tblpurchase_order_items.type = 'product' AND tblpurchase_order_items.product_id IN (".implode(',', $arrProductsId).")) ";
            }

            $wherePo = ' AND ('.implode(' OR ', $wherePo).')';

            // tblpurchase_order.cancel = 0
            $tbPurchaseOrder = "
                SELECT
                    tblpurchase_order_items.type as type, 
                    tblpurchase_order_items.product_id as id_items,
                    SUM(tblpurchase_order_items.quantity - coalesce((
                        SELECT
                            SUM(tblimport_items.quantity_net)
                        FROM tblimport_items
                        WHERE tblimport_items.id_purchase_order_items = tblpurchase_order_items.id
                    ), 0)) as quantity
                FROM tblpurchase_order
                INNER JOIN tblpurchase_order_items ON tblpurchase_order_items.id_purchase_order = tblpurchase_order.id
                WHERE tblpurchase_order.is_end = 0 $wherePurchaseOrder $wherePo
                GROUP BY tblpurchase_order_items.product_id, tblpurchase_order_items.type
            ";
            $dtPurchaseOrder = $this->db->query($tbPurchaseOrder)->result_array();
            if (!empty($dtPurchaseOrder)) {
                $dtPurchaseOrder = array_reduce($dtPurchaseOrder, function($carry, $item) {
                    $carry[$item['type'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }
        }

        foreach ($rResult as $key => $aRow) {
            $start++;
            $item_id = $aRow['item_id'];

            $_item_type = $aRow['_item_type'];
            $_item_id = $aRow['_item_id'];
            $item_id_hau = 'product__'.$_item_id;
            if ($_item_type == 'materials') {
                $item_id_hau = 'nvl__'.$_item_id;
            }

            $exchange_unit = $aRow['exchange_unit'];
            $exchange_standard_unit = $aRow['exchange_standard_unit'];
            $_dtWarehouses = $dtWarehouses[$item_id_hau] ?? null;
            $_dtTransfer = $dtTransfer[$item_id_hau] ?? null;
            $_dtImport = $dtImport[$item_id_hau] ?? null;
            $_dtProductionsPlanCompensation = $dtProductionsPlanCompensation[$item_id] ?? null;
            $quantity_inventory = $_dtWarehouses['product_quantity'] ?? 0;
            $quantity_transfer = $_dtTransfer['quantity'] ?? 0;
            $quantity_stock = $_dtImport['quantity_stock'] ?? 0;
            $quantity_plan_com = $_dtProductionsPlanCompensation['quantity_primary'] ?? 0;
            $quantity_plan_com = $quantity_plan_com * $exchange_unit/$exchange_standard_unit;

            $_dtWarehousesCapacity = $dtWarehousesCapacity[$item_id_hau] ?? null;
            $quantity_inventory_capacity = $_dtWarehousesCapacity['product_quantity'] ?? 0;

            $_dtPurchaseOrder = $dtPurchaseOrder[$item_id_hau] ?? null;
            $quantity_purchase_order = $_dtPurchaseOrder['quantity'] ?? 0;

            $aRow['ton_tong'] = $quantity_inventory + $quantity_inventory_capacity;
            $aRow['ton_thuc_te'] = $quantity_inventory;
            $aRow['ton_da_mua'] = $quantity_purchase_order < 0 ? 0 : $quantity_purchase_order;

            $aRow['quantity_inventory'] = $quantity_inventory;
            $aRow['quantity_transfer'] = $quantity_transfer;
            $aRow['quantity_stock'] = $quantity_stock;
            $aRow['quantity_primary'] = $aRow['quantity_primary'] + $quantity_plan_com;

            $quantity_rest = $aRow['quantity_primary'] - $aRow['quantity_inventory'];
            if (($aRow['quantity_purchase'] - $quantity_stock) > 0) {
                $quantity_rest-= $aRow['quantity_purchase'] - $quantity_stock;
            }
            $quantity_rest-= $aRow['quantity_transfer'];
            $aRow['quantity_rest']= $quantity_rest;

            $aRow['quantity_primary'] = ceil($aRow['quantity_primary']);
            $aRow['quantity_rest'] = ceil($aRow['quantity_rest']);

            $row[0] = '
                <div class="text-left">
                    <div class="">
                        <input type="checkbox" name="item_id[]" onchange="changeCheckboxItem(this)" class="item_id" id=item_id_' . $item_id . '" value="' . $item_id  . '"><label for="item_id_' . $item_id . '"></label>
                    </div>
                </div>
            ';

            $row[1] = '<div><a onclick="view_modal_manufactures("\'.$aRow["item_id"].\'")" data-tnh="modal" style="" class="tnh-modal" href="' . admin_url('manufactures_temp/view_modal/') . $aRow['item_id'] . '" data-toggle="modal" data-target="#myModal">' . $aRow['item_code'] . '</a></div>';
            $row[2] = '<div>' . $aRow['item_name'] . '</div>';
            $row[3] = '<div class="text-center">' . $aRow['unit_primary_name'] . '</div>';
            $row[4] = '<div class="text-center">' . formatNumber($aRow['quantity_primary'], 0) . '</div>';
            $row[5] = '<div class="text-center">' . formatNumber($aRow['quantity_inventory']) . '</div>';
            $row[6] = '<div class="text-center">' . formatNumber($aRow['quantity_transfer']) . '</div>';
            $row[7] = '<div class="text-center"><a class="tnh-modal" href="'.base_url('admin/manufactures_temp/show_view_purchase/'.$item_id).'">' . formatNumber($aRow['quantity_purchase']) . '</a></div>';
            $row[8] = '<div class="text-center">' . formatNumber($aRow['quantity_stock']) . '</div>';
            $quantity_rest = $aRow['quantity_rest'] > 0 ? $aRow['quantity_rest'] : 0;
            $row[9] = '<div class="text-center">' . ($quantity_rest > 0 ? formatNumber($quantity_rest, 0) : '') . '</div>';

            $row[10] = '<div class="text-center">' . formatNumber($aRow['ton_tong']) . '</div>';
            $row[11] = '<div class="text-center">' . formatNumber($aRow['ton_thuc_te']) . '</div>';
            $row[12] = '<div class="text-center">' . formatNumber($aRow['ton_cho_phep']) . '</div>';
            $row[13] = '<div class="text-center">' . formatNumber($aRow['ton_da_mua']) . '</div>';

            $totalQuantityPlan += roundNumberFormat($aRow['quantity_primary'], 0);
            $totalQuantityWarehouse += $aRow['quantity_inventory'];
            $totalQuantityPurchase += $aRow['quantity_purchase'];
            $totalQuantitytransfer += $aRow['quantity_transfer'];
            $totalQuantityimport += $aRow['quantity_stock'];
            $totalQuantityRest += roundNumberFormat($quantity_rest, 0);

            $totalTonTong+= $aRow['ton_tong'];
            $totalTonThucTe+= $aRow['ton_thuc_te'];
            $totalTonChoPhep+= $aRow['ton_cho_phep'];
            $totalTonDaMua+= $aRow['ton_da_mua'];
            $output['aaData'][] = $row;
        }

        $output['totalQuantityPlan'] = $totalQuantityPlan;
        $output['totalQuantityWarehouse'] = $totalQuantityWarehouse;
        $output['totalQuantityPurchase'] = $totalQuantityPurchase;
        $output['totalQuantityRest'] = $totalQuantityRest;
        $output['totalQuantitytransfer'] = $totalQuantitytransfer;
        $output['totalQuantityimport'] = $totalQuantityimport;

        $output['totalTonTong'] = $totalTonTong;
        $output['totalTonThucTe'] = $totalTonThucTe;
        $output['totalTonChoPhep'] = $totalTonChoPhep;
        $output['totalTonDaMua'] = $totalTonDaMua;
        echo json_encode($output);
    }

    function searchProductionsPlan()
    {
        $data = [];
        if ($this->input->get()) {
            $q = $this->input->get('q');
            $limit = 50;
            $this->db->select('tbl_productions_plan.id as id, CONCAT(tbl_productions_plan.reference_no, "(", DATE_FORMAT(tbl_productions_plan.date, "%d/%m/%Y"),")") as name', false);
            $this->db->from('tbl_productions_plan');
            if (!empty($q)) {
                $this->db->group_start();
                $this->db->like('tbl_productions_plan.reference_no', $q);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $data = $this->db->get()->result_array();
        }
        echo json_encode($data);
    }

    function addPurchasePlan()
    {
        $data = [];
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true) {
                $date = $this->input->post('date', true);
                $date_need = $this->input->post('date_need');
                $name = $this->input->post('name');
                $note = $this->input->post('note', false);
                $counter = $this->input->post('counter');
                $arr_id = [];
                $errors = '';
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items')[$value];
                        if (empty($items_id)) continue;
                        $arr = explode("__", $items_id);
                        $type = $arr[0];
                        $itemId = $arr[1];
                        if ($type == "materials") {
                            $type = "nvl";
                        } else if ($type == "semi_products" || $type == "semi_products_outside") {
                            $type = "product";
                        } else if ($type == "tools_supplies") {
                            $type = "tools";
                        }

                        $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $index = array_search($items_id, $arr_id);
                        if ($index === false) {
                            $arr_id[] = $items_id;
                        } else {
                            $errors .= 'Có mặt hàng bị trùng vui lòng xóa';
                        }

                        $items[] = [
                            'id' => $itemId,
                            'quantity' => $quantity,
                            'quantity_net' => $quantity,
                            'type' => $type,
                            'note' => ''
                        ];
                    }
                }

                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('un_not_items_purchase');
                    echo json_encode($data);
                    die;
                }

                $arrProductionsPlanId = $this->input->post('arrProductionsPlanId');
                $arrProductionsPlan = [];
                if (!empty($arrProductionsPlanId)) {
                    $arrProductionsPlanId = explode(',', $arrProductionsPlanId);
                    foreach ($arrProductionsPlanId as $key => $value) {
                        $arrProductionsPlan[] = [
                            'productions_plan_id' => $value
                        ];
                    }
                }

                $fields = [
                    'id_plan' => 0,
                    'name' => $name,
                    'reason' => $note,
                    'date' => $date,
                    'date_need' => $date_need,
                    'items' => $items,
                    'is_plans' => 1,
                ];

                $purchases_id = $this->purchases_model->convertCapactityToPurchase($fields);
                if ($purchases_id > 0) {
                    if (!empty($arrProductionsPlan)) {
                        foreach ($arrProductionsPlan as $key => $value) {
                            $arrProductionsPlan[$key]['purchases_id'] = $purchases_id;
                        }
                        $this->purchases_model->insertBatchPurchasePlans($arrProductionsPlan);
                    }

                    $get_code = get_table_where('tblpurchases', array('id' => $purchases_id), '', 'row');
                    activity_log_v2('purchase', 'tblpurchases', $purchases_id, $get_code->prefix . $get_code->code, 'Thêm mới yêu cầu mua hàng [' . $get_code->prefix . $get_code->code . ']');

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }
        $this->load->view('admin/manufactures/add_purchase_plan', $data);
    }



    public function handlingPrepareMaterials()
    {
        $data = [];
        if ($this->input->post('save')) {
            $sp_actions = $this->input->post('sp_actions');
            if ($sp_actions == "view") {
                $data['result'] = 0;
                $data['message'] = lang('Chỉ được xem không thể lưu');
                echo json_encode($data);
                die;
            }

            $data['result'] = 0;
            $data['message'] = lang('Chức năng này không còn được sử dụng');
            echo json_encode($data);
            die;

            $isWarehouses = $this->input->post('isWarehouses');
            $finished_productions = $this->input->post('finished_productions');
            $sp_type = $this->input->post('sp_type');
            $sp_pod_id = $this->input->post('sp_pod_id');
            $sp_pois_id = $this->input->post('sp_pois_id');
            $warehouses_semi_product = $this->input->post('warehouses_semi_product');
            $use_productions_plan = $this->input->post('use_productions_plan');
            $sp_cqi_id = $this->input->post('sp_cqi_id');
            $sp_cqis_id = $this->input->post('sp_cqis_id');
            if (empty($sp_cqi_id)) $sp_cqi_id = 0;
            if (empty($sp_cqis_id)) $sp_cqis_id = 0;

            $typeSuggestExporting = 8;
            $dtPois = get_table_where('tbl_productions_orders_items_stages', ['id' => $sp_pois_id], "", "row_array", 'stage_id, active, productions_orders_items_id, number');
            $qc = $this->manufactures_model->isQCPre(0, $dtPois['productions_orders_items_id'], 0, $dtPois['number']);
            if ($qc == 2 || $qc == 3) {
                $data['result'] = 0;
                $data['message'] = lang('Có mặt hàng chưa QC công đoạn trước hoặc chưa đạt');
                echo json_encode($data);
                die;
            }

            $stage_id = $dtPois['stage_id'];
            if ($sp_actions == 'qc') {
                $dtQcRemake = $this->manufactures_model->getCheckQualityItemsStageById($sp_cqis_id);
                if (!empty($dtQcRemake) && $dtQcRemake['status']) {
                    $data['result'] = 0;
                    $data['message'] = lang('Giai đoạn sản xuất lại này đã được hoàn thành');
                    echo json_encode($data);
                    die;
                }
                $qc_remake_id = $dtQcRemake['id'];
                $typeSuggestExporting = 13;
            } else {
                $active = $dtPois['active'];
                if ($active) {
                    $data['result'] = 0;
                    $data['message'] = lang('Giai đoạn này đã được hoàn thành');
                    echo json_encode($data);
                    die;
                }
            }

            $active = $dtPois['active'];
            if ($active) {
                $data['result'] = 0;
                $data['message'] = lang('Giai đoạn này đã được hoàn thành');
                echo json_encode($data);
                die;
            }

            $total_quantity = 0;
            $total_quantity_exchange = 0;
            $count_quantity = 0;
            $total_quantity_warehouse = 0;
            $total_quantity_payment = 0;
            $items = $this->input->post('items');
            $arrSumMaterials = [];
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $quantity_exchange = $value['quantity_exchange'];
                    $quantity_single = $value['quantity_single'];
                    $unit_id = $value['unit_id'];
                    $unit_parent_id = $value['unit_parent_id'];
                    $item_cs_id = $value['item_cs_id'];
                    $is_single_use = $value['is_single_use'];

                    $arr_item_cs_id_mt = explode('__', $value['item_cs_id']);
                    $item_id_mt = $arr_item_cs_id_mt[1];
                    $item_type_mt = $arr_item_cs_id_mt[0];

                    $quantity_warehouse = 0;
                    $unit_warehouse = 0;
                    $exchange_warehouse = 1;
                    $quantity_payment = 0;
                    $unit_payment = 0;
                    $exchange_payment = 1;
                    $exchange_unit = 1;
                    $recipe = 1;

                    $longs = 1;
                    $wide = 1;
                    $paper = 1;

                    if ($item_type_mt == "materials") {
                        $info_mt = $this->items_model->rowMaterial($item_id_mt);
                        $unit_warehouse = $info_mt['standard_unit'];
                        $exchange_warehouse = !empty($info_mt['exchange_standard_unit']) ? $info_mt['exchange_standard_unit'] : 1;

                        $unit_payment = $info_mt['unit_payment'];
                        $exchange_payment = !empty($info_mt['exchange_unit_payment']) ? $info_mt['exchange_unit_payment'] : 1;

                        $exchange_unit = $info_mt['exchange_unit'];

                        $longs = $info_mt['longs'];
                        $wide = $info_mt['wide'];
                        $paper = $info_mt['paper'];
                        $recipe = $info_mt['recipe'];
                    } else if ($item_type_mt == "tools_supplies") {
                        $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                        $unit_warehouse = $info_mt['unit_id'];
                        $unit_payment = $info_mt['unit_id'];
                    } else {
                        $info_mt = $this->products_model->rowProduct($item_id_mt);
                        $unit_warehouse = $info_mt['unit_id'];
                        $unit_payment = $info_mt['unit_id'];
                    }

                    $materials = !empty($value['materials']) ? $value['materials'] : NULL;
                    if (!empty($materials)) {
                        $isErrors = false;
                        foreach ($materials as $k => $v) {
                            $warehouses_items_mt = $v['warehouses_items'];
                            if (empty($warehouses_items_mt)) {
                                $data['result'] = 0;
                                $data['message'] = lang('Xin vui lòng chọn kho NPL');
                                echo json_encode($data);
                                die;
                            } else if ($warehouses_items_mt) {
                                foreach ($warehouses_items_mt as $kW => $vW) {
                                    if (empty($vW)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Xin vui lòng chọn kho NPL');
                                        echo json_encode($data);
                                        die;
                                    }

                                    $vW = explode('__', $vW);
                                    $warehouse_item_id = $vW[0];
                                    $location_id = $vW[1];

                                    $lot_code = $vW[2];
                                    if (empty($lot_code) || $lot_code === 'NULL' || $lot_code === 'null' || $lot_code == null) {
                                        $lot_code = NULL;
                                    }

                                    $date_sx = $vW[3];
                                    if (empty($date_sx) || $date_sx === 'NULL' || $date_sx === 'null' || $date_sx == null) {
                                        $date_sx = NULL;
                                    }

                                    $date_sd = $vW[4];
                                    if (empty($date_sd) || $date_sd === 'NULL' || $date_sd === 'null' || $date_sd == null) {
                                        $date_sd = NULL;
                                    }

                                    $date_use = $vW[5];
                                    if (empty($date_use) || $date_use === 'NULL' || $date_use === 'null' || $date_use == null) {
                                        $date_use = NULL;
                                    }

                                    $quantity_items = !empty($v['quantity_items'][$kW]) ? number_unformat($v['quantity_items'][$kW]) : 0;
                                    $quantity_exchange_se =  $quantity_items / $quantity_exchange;

                                    $quantity_warehouse = $quantity_exchange_se / $exchange_warehouse * $exchange_unit;
                                    if ($recipe == 1) {
                                        $quantity_payment = $quantity_exchange_se / $exchange_payment * $exchange_unit;
                                    } else if ($recipe == 2) {
                                        $quantity_payment = $quantity_exchange_se / $exchange_payment * $exchange_unit * $paper / 100;
                                    } else if ($recipe == 3) {
                                        $quantity_payment = $quantity_exchange_se / $exchange_payment * $exchange_unit * $longs * $wide / 10000;
                                    }

                                    $arrMaterials[] = [
                                        'type_item' => $item_type_mt,
                                        'item_id' => $item_id_mt,
                                        'item_code' => $info_mt['code'],
                                        'item_name' => $info_mt['name'],
                                        'unit_id' => $unit_id,
                                        'quantity_export' => $quantity_items,
                                        'unit_parent_id' => $unit_parent_id,
                                        'number_exchange' => $quantity_exchange,
                                        'quantity_exchange' => $quantity_exchange_se,
                                        'location_id' => $location_id,
                                        'warehouse_item_id' => $warehouse_item_id,
                                        'lot_code' => $lot_code,
                                        'date_sx' => $date_sx,
                                        'date_sd' => $date_sd,
                                        'date_use' => $date_use,

                                        'quantity_warehouse' => $quantity_warehouse,
                                        'unit_warehouse' => $unit_warehouse,
                                        'exchange_warehouse' => $exchange_warehouse,
                                        'quantity_payment' => $quantity_payment,
                                        'unit_payment' => $unit_payment,
                                        'exchange_payment' => $exchange_payment,
                                        'is_single_use' => $is_single_use
                                    ];

                                    $total_quantity += $quantity_items;
                                    $total_quantity_exchange += $quantity_exchange_se;
                                    $count_quantity++;
                                    $total_quantity_warehouse += $quantity_warehouse;
                                    $total_quantity_payment += $quantity_payment;

                                    $item_ccs_id = $item_id_mt . '__' . $item_type_mt . '__' . $warehouse_item_id . '__' . $location_id . '__' . $lot_code . '__' . $date_sx . '__' . $date_sd . '__' . $date_use;
                                    if (empty($arrSumMaterials[$item_ccs_id])) {
                                        $arrSumMaterials[$item_ccs_id] = [
                                            'item_ccs_id' => $item_ccs_id,
                                            'item_cs_id' => $item_cs_id,
                                            'item_id_mt' => $item_id_mt,
                                            'item_type' => $item_type_mt,
                                            'item_code' => $info_mt['code'],
                                            'item_name' => $info_mt['name'],
                                            'location_id' => $location_id,
                                            'warehouse_item_id' => $warehouse_item_id,
                                            'lot_code' => $lot_code,
                                            'date_sx' => $date_sx,
                                            'date_sd' => $date_sd,
                                            'date_use' => $date_use,
                                            'quantity_warehouse' => $quantity_warehouse,
                                        ];
                                    } else {
                                        $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] = $arrSumMaterials[$item_cs_id]['quantity_warehouse'] + $quantity_warehouse;
                                    }
                                }
                            }
                        }
                    }
                }
            }


            if (!empty($arrSumMaterials)) {
                foreach ($arrSumMaterials as $k => $val) {
                    $item_id_mt = $val['item_id_mt'];
                    $item_type_mt = $val['item_type'];
                    $location_id = $val['location_id'];
                    $warehouse_item_id = $val['warehouse_item_id'];
                    $lot_code = !empty($val['lot_code']) ? $val['lot_code'] : NULL;
                    $date_sx = !empty($val['date_sx']) ? $val['date_sx'] : NULL;
                    $date_sd = !empty($val['date_sd']) ? $val['date_sd'] : NULL;
                    $date_use = !empty($val['date_use']) ? $val['date_use'] : NULL;
                    $quantity_warehouse = $val['quantity_warehouse'];
                    $code = $val['item_code'];

                    $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id, $lot_code, $date_sx, $date_sd, $date_use);
                    if ($quantity_warehouse > $quantityW['total_quantity']) {
                        $data['result'] = 0;
                        $data['message'] = 'Mã ' . $code . ' không đủ số lượng trong kho để xuất';
                        echo json_encode($data);
                        die;
                    }
                }
            }

            if (empty($arrMaterials)) {
                $data['result'] = 0;
                $data['message'] = lang('Không có nguyện phụ liệu để xuất');
                echo json_encode($data);
                die;
            }

            $save_and_warehouse = 1;
            $export_name = 'Xuất kho NVL';
            $reference_suggest_exporting = getReference('stock');
            $dateGerenal = date('Y-m-d H:i:s');
            $staffGerenal = get_staff_user_id();

            $suggest_exporting = [
                'productions_orders_details_id' => $sp_pod_id,
                'reference_no' => null,
                'reference_stock' => $reference_suggest_exporting,
                'date' => $dateGerenal,
                'export_name' => $export_name,
                'note' => '',
                'status' => 'un_approved',
                'total_quantity' => $total_quantity,
                'count_items' => $count_quantity,
                'total_quantity_exchange' => $total_quantity_exchange,
                'total_quantity_warehouse' => $total_quantity_warehouse,
                'total_quantity_payment' => $total_quantity_payment,
                'created_by' => $staffGerenal,
                'date_created' => $dateGerenal,
                'convert_stock_by' => $staffGerenal,
                'date_convert_stock' => $dateGerenal,
                'status_stock' => 'approved',
                'date_stock' => $dateGerenal,
                'user_stock' => $staffGerenal,
                'type' => $typeSuggestExporting,
                'date_convert_stock' => $dateGerenal,
                'warehouse_id' => 0,
                'save_and_warehouse' => $save_and_warehouse,
                'pois_id' => $sp_pois_id,
                'use_productions_plan' => $use_productions_plan,
                'cqi_id' => $sp_cqis_id
            ];

            $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
            if ($suggest_exporting_id) {
                updateReference('stock');

                if (!empty($arrMaterials)) {
                    foreach ($arrMaterials as $k => $v) {
                        $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                    }
                    $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                }

                //suggest exporting
                $errors = '';
                if (!empty($save_and_warehouse)) {
                    $id = $suggest_exporting_id;
                    $_data = array(
                        'warehouseman_id' => get_staff_user_id(),
                        'date_warehouseman' => date('Y-m-d H:i:s')
                    );

                    if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                        $errors .= '<div>' . lang('test_quantyti_time_return') . '</div>';
                    } else {
                        $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                        if ($success) {
                            log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                            $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                            $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                            $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                            $content = lang('tnh_his_warehouse_exporting_producion');
                            $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                            $content = str_replace('{$2}', $pod['reference_no'], $content);

                            insertActivityLog([
                                'type_parent_obj' => 'exporting_producion',
                                'table_obj' => 'tbl_suggest_exporting',
                                'id_obj' => $id,
                                'name_obj' => $suggest_exporting['reference_stock'],
                                'content' => $content,
                                'actions' => 'warehouse'
                            ]);
                        }
                    }
                }

                $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                $content = lang('tnh_his_add_exporting_producion');
                $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                $content = str_replace('{$2}', $pod['reference_no'], $content);

                insertActivityLog([
                    'type_parent_obj' => 'exporting_producion',
                    'table_obj' => 'tbl_suggest_exporting',
                    'id_obj' => $suggest_exporting_id,
                    'name_obj' => $reference_suggest_exporting,
                    'content' => $content,
                    'actions' => 'add'
                ]);

                if ($sp_actions != 'qc') {
                    if ($sp_actions != "enter_semi_products" || ($sp_actions == "enter_semi_products" && !empty($finished_productions))) {
                        $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                            'active' => 1,
                            'staff_active' => $staffGerenal,
                            'date_active' => $dateGerenal,
                        ]);
                    }
                } else {
                    if (!empty($finished_productions)) {
                        $op = [
                            'active' => 1,
                            'staff_active' => $staffGerenal,
                            'date_active' => $dateGerenal,
                        ];
                        $this->manufactures_model->updateCheckQualityItemsStage($sp_cqis_id, $op);
                    }
                }

                $data['process'] = showProcessDetailProductions($pod['productions_orders_item_id'])['process'];
                $data['result'] = 1;
                $data['message'] = lang('success') . '' . $errors;
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            echo json_encode($data);
            die;
        } else {

            $data['cqi_id'] = $this->input->post('cqi_id');
            $data['quantity'] = $this->input->post('quantity');
            $data['cqis_id'] = $this->input->post('cqis_id');
            $data['actions'] = $this->input->post('actions');
            $data['pod_id'] = $this->input->post('pod_id');
            $data['type'] = $this->input->post('type');
            $data['pois_id'] = $this->input->post('pois_id');
            $data['dtPois'] = $this->manufactures_model->getProductionsOrdersItemsStagesById($data['pois_id']);
            $data['warehouses'] = $this->stock_model->getWarehouses(false, [WAREHOUSES_CAPACITY, WAREHOUSES_HOLD, WAREHOUSES_ERRORS]);
            $data['productions_plan'] = $this->manufactures_model->getProductionsPlanPOD($data['pod_id']);
            $data['title'] = $data['actions'] != 'products' ? lang('tnh_finished_productions') : lang('tnh_enter_products');
            $this->load->view('admin/manufactures/handling_prepare_materials', $data);
        }
    }

    public function loadALLPrepareMaterials()
    {
        $pod_id = $this->input->post('pod_id');
        $pois_id = $this->input->post('pois_id');
        $type = $this->input->post('type');
        $production_plan_id = $this->input->post('production_plan_id');
        $quantity = number_unformat($this->input->post('quantity'));
        $actions = $this->input->post('actions');
        $productions_orders_items = $this->manufactures_model->loadDataPrepareMaterials($pod_id, $production_plan_id, $quantity, $actions, $pois_id);
        $data['items'] = $productions_orders_items;
        echo json_encode($data);
    }


    /////////


    public function add()
    {
        // if (!$this->perAdd) {
        // 	accessDenied($js = true);
        // }
        if ($this->input->post('add')) {
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required|is_unique[tbl_manufactures.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = getReference('manufacture');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note', false);
                $count_items = 0;
                $total_quantity = 0;
                $items = [];
                $items_id = $this->input->post('items_id');
                $id_production_detail = $this->input->post('id_production_detail');

                if (empty($id_production_detail)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng nhập phiếu lệnh sản xuất chi tiết');
                    echo json_encode($data);
                    die;
                }


                if (!empty($items_id)) {
                    $index = 0;
                    foreach ($items_id as $key => $value) {
                        $arrs = explode('__', $value);
                        $item_id = $arrs[0];
                        $type_item = $arrs[1];
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $warehouses = $this->input->post('warehouses')[$key];
                        // if (empty($warehouses)) {
                        // 	$data['result'] = 0;
                        // 	$data['message'] = lang('Vui lòng chọn kho mặt hàng');
                        // 	echo json_encode($data);
                        // 	die;
                        // }

                        $note_items = $this->input->post('note_items')[$key];
                        $warehouses = explode('__', $warehouses);
                        $warehouse_ids = WAREHOUSES_CAPACITY; //de a hoang gan
                        $location_ids = LOCATIONS_DEFAULT_MANUFACTURES; //de a hoang gan
                        $item_id_bom = !empty($this->input->post('item_id_bom')[$key]) ? $this->input->post('item_id_bom')[$key] : '';
                        $arrBOM = [];
                        $totalBOM = 0;
                        $quantity = 0;
                        if (!empty($item_id_bom)) {
                            foreach ($item_id_bom as $k => $val) {
                                $arrs1 = explode('__', $val);
                                $item_id1 = $arrs1[0];
                                $type_item1 = $arrs1[1];
                                $warehouses_items = $this->input->post('warehouses_items')[$key][$k];
                                $quantity_bom = number_unformat($this->input->post('quantity_bom')[$key][$k]);
                                if (empty($warehouses_items)) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Vui lòng chọn kho mặt hàng BOM');
                                    echo json_encode($data);
                                    die;
                                }

                                $quantity_multiples = number_unformat($this->input->post('quantity_multiples')[$key][$k]);
                                $total_quantity_item = $quantity_bom * $quantity_multiples;
                                $quantity += $total_quantity_item;
                                // $warehouses = explode('__', $warehouses_items);
                                $warehouses_id = get_table_where('tblwarehouse_items', array('id' => $warehouses_items), '', 'row');
                                $warehouse_id = $warehouses_id->warehouse_id;
                                $location_id = $warehouses_id->localtion;
                                // $dtWProduct = $this->manufacture_model->getWarehouseProductById($warehouses_items);
                                $warehouse_item_id = $warehouse_id;
                                $location_item_id = $location_id;
                                $quantity_stock =  $quantity_bom;
                                if ($type_item1 == 'materials') {
                                    $data_items = get_items($item_id1, 'nvl');
                                    $recipe = $data_items->recipe;
                                    $paper = $data_items->paper;
                                    $longs = $data_items->longs;
                                    $wide = $data_items->wide;
                                    $exchange_unit = $data_items->exchange_unit;    //chuan
                                    $exchange_standard_unit = $data_items->exchange_standard_unit; //kho
                                    $exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
                                    $quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
                                    if ($recipe == 1) {
                                        $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_standard_unit;
                                    } elseif ($recipe == 2) {
                                        $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
                                    } elseif ($recipe == 3) {
                                        $quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
                                    }
                                } else {
                                    $recipe = 1;
                                    $paper = 1;
                                    $longs = 1;
                                    $wide = 1;
                                    $exchange_unit = 1;    //chuan
                                    $exchange_standard_unit = 1; //kho
                                    $exchange_unit_payment = 1; //thanh toan
                                    $quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
                                    $quantity_payment = ($quantity_unit / $exchange_unit_payment) / $exchange_standard_unit;
                                }
                                $info_items = array();
                                $info_items['recipe'] = $recipe;
                                $info_items['paper'] = $paper;
                                $info_items['longs'] = $longs;
                                $info_items['wide'] = $wide;
                                $info_items_text = json_encode($info_items);
                                $arrBOM[] = [
                                    'item_id' => $item_id1,
                                    'type_items' => $type_item1,
                                    'warehouse_item_id' => $warehouse_item_id,
                                    'location_item_id' => $location_item_id,
                                    'quantity_item' => $quantity_bom,
                                    'warehouse_product_id' => $warehouses_items,
                                    'quantity_stock' => $quantity_stock,
                                    'quantity_unit' => $quantity_unit,
                                    'quantity_payment' => $quantity_payment,
                                    'info_items' => $info_items_text,
                                    'lot_code' => $warehouses_id->lot_code,
                                    'date_sx' => $warehouses_id->date_sx,
                                    'date_sd' => $warehouses_id->date_sd,
                                    'date_use' => $warehouses_id->date_use,
                                ];
                                $totalBOM += $quantity_bom;
                            }
                        } else {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn NPL để xã khổ');
                            echo json_encode($data);
                            die;
                        }

                        if ($quantity < 1) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng kiểm tra số lượng nhập');
                            echo json_encode($data);
                            die;
                        }

                        $quantity_stock_main =  $quantity;
                        if ($type_item == 'materials') {
                            $data_items = get_items($item_id, 'nvl');
                            $recipe_main = $data_items->recipe;
                            $paper_main = $data_items->paper;
                            $longs_main = $data_items->longs;
                            $wide_main = $data_items->wide;
                            $exchange_unit_main = $data_items->exchange_unit;    //chuan
                            $exchange_standard_unit_main = $data_items->exchange_standard_unit; //kho
                            $exchange_unit_payment_main = $data_items->exchange_unit_payment; //thanh toan
                            $quantity_unit_main = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
                            if ($recipe == 1) {
                                $quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $exchange_standard_unit_main;
                            } elseif ($recipe == 2) {
                                $quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * $paper_main / 100;
                            } elseif ($recipe == 3) {
                                $quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) * ($longs_main  * $wide_main) / 10000;
                            }
                        } else {
                            $recipe_main = 1;
                            $paper_main = 1;
                            $longs_main = 1;
                            $wide_main = 1;
                            $exchange_unit_main = 1;    //chuan
                            $exchange_standard_unit_main = 1; //kho
                            $exchange_unit_payment_main = 1; //thanh toan
                            $quantity_unit_main = ($quantity_stock_main * $exchange_standard_unit_main) / $exchange_unit_main;
                            $quantity_payment_main = ($quantity_unit_main / $exchange_unit_payment_main) / $exchange_standard_unit_main;
                        }
                        $info_items_main = array();
                        $info_items_main['recipe'] = $recipe_main;
                        $info_items_main['paper'] = $paper_main;
                        $info_items_main['longs'] = $longs_main;
                        $info_items_main['wide'] = $wide_main;
                        $info_items_text_main = json_encode($info_items_main);

                        $items[$index] = [
                            'item_id' => $item_id,
                            'type_items' => $type_item,
                            'warehouse_id' => $warehouse_ids,
                            'location_id' => $location_ids,
                            'quantity' => $quantity,
                            'note_item' => $note_items,
                            'arrBOM' => $arrBOM,
                            'quantity_stock' => $quantity_stock_main,
                            'quantity_unit' => $quantity_unit_main,
                            'quantity_payment' => $quantity_payment_main,
                            'info_items' => $info_items_text_main,
                        ];
                        $count_items++;
                        $total_quantity += $quantity;
                        $index++;
                    }
                }
                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }
                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'status' => 1,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                    'note' => $note,
                    'id_production_detail' => $id_production_detail
                ];

                print_arrays($items);
                $manufactures_id = $this->manufacture_model->insertManufactures($options);
                if ($manufactures_id) {
                    updateReference('manufacture');
                    $arrManufacturesItemsBOM = [];
                    foreach ($items as $key => $value) {
                        $arrBOM = $value['arrBOM'];
                        $value['manufactures_id'] = $manufactures_id;
                        unset($value['arrBOM']);
                        $manufactures_items_id = $this->manufacture_model->insertManufacturesItems($value);
                        if (!empty($manufactures_items_id)) {
                            if (!empty($arrBOM)) {
                                foreach ($arrBOM as $k => $v) {
                                    $v['manufactures_id'] = $manufactures_id;
                                    $v['manufactures_items_id'] = $manufactures_items_id;
                                    $arrManufacturesItemsBOM[] = $v;
                                }
                            }
                        }
                    }
                    if (!empty($arrManufacturesItemsBOM)) {
                        $this->manufacture_model->insertManufacturesItemsBOMBatch($arrManufacturesItemsBOM);
                    }
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['title'] = lang('add_manufacture');
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/manufacture'),
                    'page' => lang('manufacture'),
                ),
                array('link' => '#', 'page' => lang('add_manufacture')),
            ];

            $this->db->select(['id', 'reference_no']);
            $data['productions_detail'] = $this->db->get_where('tbl_productions_orders_details')->result_array();

            $this->load->view('admin/manufacture/add_1', $data);
        }
    }

    public function getItemsById()
    {
        $item_id = $this->input->post('item_id');
        $item = [];
        if (!empty($item_id)) {
            $this->db->select('
                tbl_materials.id as id,
                CONCAT(tbl_materials.id, "__materials") as item_id,
                tbl_materials.code as code,
                tbl_materials.name as name,
                tbl_materials.images as images,
            ', false);
            $this->db->from('tbl_materials');
            $this->db->where('tbl_materials.id', $item_id);
            $materials = $this->db->get()->row_array();
            if (!empty($materials)) {
                if (!empty($item['images'])) {
                    $images = base_url('uploads/materials/' . $materials['images']);
                    $materials['items'] = $images;
                }
            }
            $item = $materials;
        }

        // $t_item_id = $item_id;
        // $type = $this->input->post('type');
        // $data = [];
        // $item = false;
        // if (!empty($item_id)) {
        // 	$arrItem = explode('__', $item_id);
        // 	$item_id = $arrItem[0];
        // 	$item_type = $arrItem[1];
        // 	$images = base_url('assets/images/tnh/no_image.png');
        // 	//            $products_accessary = [];
        // 	if ($item_type == "materials") {
        // 		$type_items = 'nvl';
        // 		$this->db->select('
        // 			tbl_materials.id as id,
        // 			tbl_materials.code as item_code,
        // 			tbl_materials.name as item_name,
        // 			tblunits.unit as unit_name,
        // 			tbl_materials.images as images,
        // 			tbl_materials.price_sell as price_sell,
        //     	');
        // 		$this->db->from('tbl_materials');
        // 		$this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id');
        // 		$this->db->where('tbl_materials.id', $item_id);
        // 		$item = $this->db->get()->row_array();
        // 		if (!empty($item['images'])) {
        // 			$images = base_url('uploads/materials/' . $item['images']);
        // 		}
        // 		$item['item_id'] = $t_item_id;
        // 		$data['item'] = $item;
        // 	}
        // 	$warehouses = $this->manufacture_model->getWarehouse();
        // 	$option = '<option value=""></option>';
        // 	foreach ($warehouses as $key => $v) {
        // 		if ($item_type == 'materials') {
        // 			$type = 'nvl';
        // 			// $quantity_warehouse = $this->manufacture_model->getTotalQuantityWarehousesv2($value['id'], $item_id, $type_items);
        // 			// $option .= '<optgroup label="' . $value['name'] . '">';
        // 			// foreach ($quantity_warehouse as $k => $v) {
        // 			// 	$option .= '<option data-quantity="' . $v['product_quantity'] . '" value="' . $value['id'] . '__' . $v['localtion'] . '">' . $v['name'] . ' (' . formatNumber($v['product_quantity']) . ')</option>';
        // 			// }
        // 			// $option .= '</optgroup>';
        // 			$this->db->select('tbllocaltion_warehouses.*,product_quantity,tblwarehouse_items.type_items,tblwarehouse_items.lot_code,tblwarehouse_items.date_sx,tblwarehouse_items.date_sd,tblwarehouse_items.date_use,tblwarehouse_items.id as idd');
        // 			$this->db->where(array('id_items' => $item_id, 'type_items' => $type));
        // 			$this->db->where('warehouse', $v['id']);
        // 			$this->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id');
        // 			$this->db->where('product_quantity >= 0');
        // 			$localtion = $this->db->get('tbllocaltion_warehouses')->result_array();
        // 			if (!empty($localtion)) {
        // 				$option .= '<optgroup data-check ="1" data-text ="' . $v['name'] . '" label="' . $v['name'] . '">';
        // 				foreach ($localtion as $key => $value) {
        // 					if (!empty($value['id'])) {
        // 						$name = get_listname_localtion_warehouse($value['id']);
        // 						$option .= '<option data-check ="0" data-type= "' . $value['type_items'] . '" data-text ="' . $name . '(' . $value['product_quantity'] . ')" data-lot = "' . $value['lot_code'] . '"  data-date_sx = "' . _d($value['date_sx']) . '"  data-date_sd = "' . _d($value['date_sd']) . '"  data-date_use = "' . _d($value['date_use']) . '"  quantity-id="' . $value['product_quantity'] . '" data-content="' . $name . '(' . $value['product_quantity'] . ')" content="' . $name . '" value="' . $value['idd'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . '>' . $name . '(' . $value['product_quantity'] . ')</option>';
        // 					}
        // 				}
        // 				$option .= '</optgroup>';
        // 			}
        // 		}
        // 	}
        // 	$item['option_warehouses'] = $option;
        // }
        // $item['item_id'] = $t_item_id;
        $data['item'] = $item;
        echo json_encode($data);
    }

    public function searchMaterialPOD()
    {

        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');

        $id_production_detail = !empty($params['id_production_detail']) ? $params['id_production_detail'] : 0;
        $this->db->select('
            tbl_productions_orders_items_sub.item_id as item_id,
            CONCAT(tbl_materials.id, "__materials") as id, 
            CONCAT(tbl_materials.name, "(", tbl_materials.code, ")") as text
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_orders_items_sub.item_id');
        $this->db->where('tbl_productions_orders_items_sub.type', 'materials');
        $this->db->where('tbl_productions_orders_details.id', $id_production_detail);
        $this->db->group_by('tbl_productions_orders_items_sub.item_id');
        $this->db->limit($limit);
        $items = $this->db->get()->result_array();
        $data['results'] = $items;
        echo json_encode($data);
    }

    public function keepWarehouseOrders()
    {
        $data = [];
        $this->load->view('admin/manufactures/keep_warehouse_orders', $data);
    }

    public function saveKeepWarehouseOrders()
    {
        $data = [];

        if ($this->input->post()) {
            $items = $this->input->post('items');
            $arrOrder = [];
            $arrSumMaterials = [];

            $statusTransfer = 2;
            $staffIdTransfer = get_staff_user_id();
            $dateTransfer = date('Y-m-d H:i:s');
            $history_status = '|' . $staffIdTransfer . ',' . $dateTransfer;
            $time = time();

            $errors = '';
            $stt = 1;
            foreach ($items as $key => $value) {
                $order_id = $key;
                $order_item_id = !empty($value['order_item_id']) ? $value['order_item_id'] : null;
                $product_id = !empty($value['product_id']) ? $value['product_id'] : null;
                $quantity_delivery = !empty($value['quantity_delivery']) ? $value['quantity_delivery'] : 0;
                $item_type = 'product';

                $locations = get_table_where('tbllocaltion_warehouses', array(
                    'warehouse' => WAREHOUSES_HOLD,
                    'order_id' => $order_id,
                    'tranfer_business_id' => 0,
                ), '', 'row');
                if (empty($locations)) {
                    $orderCheck = get_table_where(
                        'tbl_orders',
                        array('id' => $order_id),
                        '',
                        'row'
                    );
                    $in_local = array();
                    $in_local['name'] = $orderCheck->reference_no;
                    $in_local['code'] = $orderCheck->reference_no;
                    $in_local['name_parent'] = $orderCheck->reference_no;
                    $in_local['warehouse'] = WAREHOUSES_HOLD;
                    $in_local['child'] = 1;
                    $in_local['create_by'] = get_staff_user_id();
                    $in_local['date_create'] = date('Y-m-d H:i:s');
                    $in_local['status'] = 0;
                    $in_local['lever'] = 1;
                    $in_local['productions_plan_id'] = 0;
                    $in_local['pod_id'] = 0;
                    $in_local['stage_id'] = 0;
                    $in_local['stage_id_import_outsource'] = 0;
                    $in_local['order_item_id'] = 0;
                    $in_local['order_id'] = $order_id;
                    $this->db->insert('tbllocaltion_warehouses', $in_local);
                    $location_to = $this->db->insert_id();
                } else {
                    $location_to = $locations->id;
                }

                $get_item = get_items($product_id, $item_type);
                $totalWarehouse = 0;
                $total = 0;
                $totalQuantityCorrdinator = 0;
                $tranferItems = [];
                if (!empty($order_item_id) && !empty($product_id)) {
                    $info = $this->products_model->rowProduct($product_id);
                    $order_item = get_table_where('tbl_order_items', ['id' => $order_item_id], '', 'row_array');
                    if (empty($order_item)) {
                        continue;
                    }

                    $unit_id = $order_item['unit_id'];
                    $exchange_unit = 1;
                    $exchange_stock = $info['conversion_quantity_unit'];
                    $exchange_payment = 1;

                    $arr_tick = !empty($value['tick']) ? $value['tick'] : null;
                    $arr_quantity_coordinator = !empty($value['quantity_coordinator']) ? $value['quantity_coordinator'] : null;
                    $arr_lot_code = !empty($value['lot_code']) ? $value['lot_code'] : null;
                    $arr_date_sx = !empty($value['date_sx']) ? $value['date_sx'] : null;
                    $arr_date_sd = !empty($value['date_sd']) ? $value['date_sd'] : null;
                    $arr_date_use = !empty($value['date_use']) ? $value['date_use'] : null;

                    if ($arr_tick) {
                        foreach ($arr_tick as $k => $val) {
                            $arrW = explode('__', $val);
                            $warehouse_id = $arrW[0];
                            $location_id = $arrW[1];

                            $quantity_coordinator = !empty($arr_quantity_coordinator) ? number_unformat($arr_quantity_coordinator[$k]) : 0;
                            $lot_code = !empty($arr_lot_code) ? ($arr_lot_code[$k]) : NULL;
                            $date_sx = !empty($arr_date_sx) ? ($arr_date_sx[$k]) : NULL;
                            $date_sd = !empty($arr_date_sd) ? ($arr_date_sd[$k]) : NULL;
                            $date_use = !empty($arr_date_use) ? ($arr_date_use[$k]) : NULL;

                            if (empty($quantity_coordinator)) {
                                $data['result'] = 0;
                                $data['message'] = lang('Số lượng giữ phải lớn hơn 0');
                                echo json_encode($data);
                                die;
                                continue;
                            }
                            $amountTranfer = $get_item->price * $quantity_coordinator;

                            $quantity_unit = 0;
                            $quantity_stock = 0;
                            if ($unit_id == $info['unit_id']) {
                                // $quantity_unit = $quantity_coordinator;
                                // $quantity_stock = roundNumberFormat($quantity_coordinator * $exchange_stock);
                                $quantity_stock = $quantity_coordinator;
                                $quantity_unit = roundNumberFormat($quantity_stock / $exchange_stock, 0);
                            } else {
                                // $quantity_unit = roundNumberFormat($quantity_coordinator / $exchange_stock);
                                // $quantity_stock = $quantity_coordinator;
                                $quantity_stock = $quantity_coordinator;
                                $quantity_unit = roundNumberFormat($quantity_stock / $exchange_stock, 0);
                            }

                            
                            $tranferItems[] = array(
                                'order_id_item' => $order_item_id,
                                'id_items' => $product_id,
                                'quantity' => $quantity_coordinator,
                                'quantity_net' => $quantity_coordinator,
                                'type' => $item_type,
                                'note' => '',
                                'warehouses_to' => WAREHOUSES_HOLD,
                                'warehouses_id' => $warehouse_id,
                                'localtion_id' => $location_id,
                                'localtion_to' => $location_to,
                                'price' => $get_item->price,
                                'amount' => $amountTranfer,
                                'quantity_unit' => $quantity_unit,
                                'quantity_stock' => $quantity_stock,
                                'quantity_payment' => $quantity_coordinator,
                                'exchange_unit' => $exchange_unit,
                                'exchange_stock' => $exchange_stock,
                                'exchange_payment' => $exchange_payment,
                                'date_sx' => !empty($date_sx) ? to_sql_date($date_sx) : null,
                                'date_sd' => !empty($date_sd) ? to_sql_date($date_sd) : null,
                                'date_use' => !empty($date_use) ? $date_use : null,
                                'lot_code' => !empty($lot_code) ? $lot_code : null,
                                'unit_id' => $unit_id,
                            );
                            $totalQuantityCorrdinator += $quantity_coordinator;
                            $totalWarehouse += $quantity_coordinator;
                            $total += $amountTranfer;

                            $item_ccs_id = $product_id . '__' . $item_type . '__' . $warehouse_id . '__' . $location_id . '__' . $lot_code . '__' . $date_sx . '__' . $date_sd . '__' . $date_use;

                            if (empty($arrSumMaterials[$item_ccs_id])) {
                                $arrSumMaterials[$item_ccs_id] = [
                                    'item_ccs_id' => $item_ccs_id,
                                    'item_id_mt' => $product_id,
                                    'item_type' => $item_type,
                                    'item_code' => $info['code'],
                                    'item_name' => $info['name'],
                                    'location_id' => $location_id,
                                    'warehouse_item_id' => $warehouse_id,
                                    'lot_code' => $lot_code,
                                    'date_sx' => $date_sx,
                                    'date_sd' => $date_sd,
                                    'date_use' => $date_use,
                                    // 'quantity_warehouse' => $quantity_coordinator,
                                    'quantity_warehouse' => $quantity_stock,
                                ];
                            } else {
                                // $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] = $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] + $quantity_coordinator;
                                $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] = $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] + $quantity_stock;
                            }
                        }
                    }

                    //Kiểm tra số lượng cần giữ hàng
                    $quantityNeedHold = 0;
                    if ($totalQuantityCorrdinator > $quantity_delivery) {
                        $errors .= '<div>' . lang('Thành phẩm') . ' ' . $get_item->name . ' số lượng giữ hàng phải <= ' . formatNumber($quantity_delivery) . '</div>';
                        continue;
                    }

                    if (empty($tranferItems)) continue;
                    $arrOrder[$key] = [
                        'code' => sprintf('%06d', ch_getMaxID('id', 'tbltransfer_warehouse') + $stt),
                        'prefix' => get_option('prefix_transfer'),
                        'note' => '',
                        'warehouse_id' => 0,
                        'warehouse_to' => 0,
                        'date' => date('Y-m-d'),
                        'staff_id' => $staffIdTransfer,
                        'date_create' => $dateTransfer,
                        'status' => 2,
                        'history_status' => $history_status,
                        'total' => $total,
                        'order_id_new' => $order_id,
                        'order_id' => $order_id,
                        'tranferItems' => $tranferItems,
                        'id_auto' => $time
                    ];
                    $stt++;
                }
            }
        }

        if (!empty($errors)) {
            $data['result'] = 0;
            $data['message'] = $errors;
            echo json_encode($data);
            die;
        }
        if (!empty($arrSumMaterials)) {
            foreach ($arrSumMaterials as $k => $val) {
                $item_id_mt = $val['item_id_mt'];
                $item_type_mt = $val['item_type'];
                $location_id = $val['location_id'];
                $warehouse_item_id = $val['warehouse_item_id'];
                $lot_code = !empty($val['lot_code']) ? $val['lot_code'] : NULL;
                $date_sx = !empty($val['date_sx']) ? to_sql_date($val['date_sx']) : NULL;
                $date_sd = !empty($val['date_sd']) ? to_sql_date($val['date_sd']) : NULL;
                $date_use = !empty($val['date_use']) ? $val['date_use'] : NULL;
                $quantity_warehouse = $val['quantity_warehouse'];
                $code = $val['item_code'];

                $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id, $lot_code, $date_sx, $date_sd, $date_use);
                if ($quantity_warehouse > $quantityW['total_quantity']) {
                    $data['result'] = 0;
                    $data['message'] = 'Mã ' . $code . ' không đủ số lượng để giữ kho';
                    echo json_encode($data);
                    die;
                }
            }
        }

        if (empty($arrOrder)) {
            $data['result'] = 0;
            $data['message'] = 'Không có dữ liệu để tạo phiếu chuyển kho';
            echo json_encode($data);
            die;
        }

        $flagSuccess = false;
        foreach ($arrOrder as $key => $value) {
            $tranferItems = $value['tranferItems'];
            unset($value['tranferItems']);
            $this->db->insert('tbltransfer_warehouse', $value);
            $transfer_id = $this->db->insert_id();
            if ($transfer_id) {
                foreach ($tranferItems as $k => $v) {
                    $v['id_transfer'] = $transfer_id;
                    $this->db->insert('tbltransfer_warehouse_detail', $v);
                    $ins = $this->db->insert_id();
                    if ($ins) {
                        $order_item = get_table_where('tbl_order_items', array(
                            'id' => $v['order_id_item'],
                        ), '', 'row');
                        $quantity_hold = $order_item->quantity_condition + $v['quantity_net'];
                        $this->db->update(
                            'tbl_order_items',
                            array('quantity_condition' => $quantity_hold),
                            array('id' => $order_item->id)
                        );
                    }
                }


                if (!test_quantity_tranfer($transfer_id)) {
                } else {
                    $dataTransfer = array(
                        'warehouseman_id' => $staffIdTransfer,
                        'warehouseman_date' => $dateTransfer,
                    );
                    $this->db->update('tbltransfer_warehouse', $dataTransfer, array('id' => $transfer_id));
                    $this->transfer_model->increaseTranfersWarehouse($transfer_id);
                }

                $order = get_table_where('tbl_orders', ['id' => $value['order_id']], '', 'row_array');
                insertActivityLog([
                    'type_parent_obj' => 'orders',
                    'table_obj' => 'tbl_orders',
                    'id_obj' => $value['order_id'],
                    'name_obj' => $order['reference_no'],
                    'content' => lang('Giữ kho ') . ' [' . $order['reference_no'] . ']',
                    'actions' => 'keep_stock_orders',
                ]);

                $flagSuccess = true;
            }
        }

        if ($flagSuccess) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 1;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function loadBOMPP()
    {
        $data = [];
        $trItems = '';
        if ($this->input->post()) {
            $item_object_id = $this->input->post('item_object_id');
            $dataPOST = $this->input->post();
            $arrItems = [];
            if (!empty($item_object_id)) {
                foreach ($item_object_id as $key => $value) {
                    $versions = !empty($dataPOST['versions'][$key]) ? $dataPOST['versions'][$key] : '';
                    $quantity = !empty($dataPOST['quantity'][$key]) ? number_unformat($dataPOST['quantity'][$key]) : 0;
                    $product_id = !empty($dataPOST['poduct_id_css'][$key]) ? $dataPOST['poduct_id_css'][$key] : '';
                    if (!empty($versions) && !empty($quantity)) {
                        $arrItems[] = [
                            'product_id' => $product_id,
                            'versions' => $versions,
                            'quantity' => $quantity,
                        ];
                    }
                }
            }

            if (!empty($dataPOST['product_id_preventive'])) {
                foreach ($dataPOST['product_id_preventive'] as $key => $value) {
                    $versions = !empty($dataPOST['versions_perventive'][$key]) ? $dataPOST['versions_perventive'][$key] : '';
                    $quantity = !empty($dataPOST['quantity_preventive'][$key]) ? number_unformat($dataPOST['quantity_preventive'][$key]) : 0;
                    $product_id = !empty($dataPOST['product_id_preventive'][$key]) ? $dataPOST['product_id_preventive'][$key] : '';

                    if (!empty($versions) && !empty($quantity)) {
                        $arrItems[] = [
                            'product_id' => $product_id,
                            'versions' => $versions,
                            'quantity' => $quantity,
                        ];
                    }
                }
            }

            $arrBOMMaterial = [];
            if (!empty($arrItems)) {
                foreach ($arrItems as $key => $value) {
                    $this->manufactures_model->handlingBomPP($arrBOMMaterial, $value['product_id'], $value['versions'], $value['quantity'], 0, $value['quantity'], 0);
                }

                $arrSumMaterial = [];
                foreach ($arrBOMMaterial as $key => $value) {
                    $str_item_id = $value['item_type'] . '__' . $value['item_id'];
                    if (empty($arrSumMaterial[$str_item_id])) {
                        $arrSumMaterial[$str_item_id] = $value;
                    } else {
                        if ($value['quantity_compensation'] > $arrSumMaterial[$str_item_id]['quantity_compensation']) {
                            $arrSumMaterial[$str_item_id]['quantity_compensation'] = $value['quantity_compensation'];
                        }
                        $arrSumMaterial[$str_item_id]['quantity'] = $arrSumMaterial[$str_item_id]['quantity'] + $value['quantity'];
                    }
                }

                $index = 0;
                if (!empty($arrSumMaterial)) {
                    foreach ($arrSumMaterial as $key => $value) {
                        $item_type = $value['item_type'];
                        $item_id = $value['item_id'];
                        $unit_id = $value['unit_id'];

                        $conversion_quantity_unit = 1;
                        if ($item_type == "materials") {
                            $typeW = 'nvl';
                            // $info = $this->items_model->rowMaterial($item_id);
                        } else {
                            $typeW = 'product';
                            $info = $this->products_model->rowProduct($item_id);
                            $conversion_quantity_unit = $info['conversion_quantity_unit'];
                        }

                        $is_zinc = !empty($value['is_zinc']) ? $value['is_zinc'] : 0;
                        $quantity = $value['quantity'];
                        $quantity_compensation = $value['quantity_compensation'];
                        $quantity_compensation_sm = $value['quantity_compensation_sm'];
                        $standard_unit = $value['standard_unit'];
                        $exchange_standard_unit = $value['exchange_standard_unit'];
                        $exchange_unit = $value['exchange_unit'];
                        $quantity_exchange = $value['quantity_exchange'];

                        $unit = $this->unit_model->rowUnit($standard_unit);

                        $this->db->select('
                            SUM(tblwarehouse_items.product_quantity) as product_quantity,
                        ', false);
                        $this->db->from('tblwarehouse_items');
                        $this->db->where_not_in('tblwarehouse_items.warehouse_id', [WAREHOUSES_CAPACITY]);
                        $this->db->where('tblwarehouse_items.type_items', $typeW);
                        $this->db->where('tblwarehouse_items.id_items', $item_id);
                        $this->db->where('tblwarehouse_items.product_quantity >', 0);
                        $dtW = $this->db->get()->row_array();

                        $quantityNeed = $quantity + $quantity_compensation;
                        $quantity_primary = $quantityNeed * $quantity_exchange / $exchange_unit;
                        if ($item_type == "materials") {
                            // $quantity_convert_warehouse = roundNumberFormat($quantity_primary / $exchange_standard_unit * $exchange_unit, 0);
                            $quantity_convert_warehouse = ceil($quantity_primary / $exchange_standard_unit * $exchange_unit);
                        } else {
                            // $quantity_convert_warehouse = roundNumberFormat($quantity_primary * $conversion_quantity_unit, 0);
                            $quantity_convert_warehouse = ceil($quantity_primary * $conversion_quantity_unit);
                        }

                        $quantity_warehouse = $dtW['product_quantity'];

                        $strStatus = '';
                        if ($quantity_convert_warehouse > $quantity_warehouse) {
                            $strStatus = '<span class="label label-danger">' . lang('Chưa đủ kho') . '</span>';
                        } else {
                            $strStatus = '<span class="label label-success">' . lang('Đã đủ kho') . '</span>';
                        }

                        // <td class="text-center">' . formatNumber($quantity, 0) . '</td>
                        $trItems .= '<tr>
                            <td class="text-center">' . $value['item_code'] . '</td>
                            <td class="text-center">' . $value['item_name'] . '</td>
                            <td class="text-center"><span class="label label-' . ($item_type == 'materials' ? 'success' : 'danger') . '">' . lang($item_type) . '</span></td>
                            <td class="text-center">' . $unit['unit'] . '</td>
                            <td class="text-center">' . ceil($quantity) . '</td>
                            <td>
                                <input type="hidden" name="itemBOMM[' . $index . '][item_id]" class="form-control item_id" value="' . $item_id . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][item_type]" class="form-control item_type" value="' . $item_type . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][standard_unit]" class="form-control standard_unit" value="' . $standard_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][exchange_standard_unit]" class="form-control exchange_standard_unit" value="' . $exchange_standard_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity_exchange]" class="form-control quantity_exchange" value="' . $quantity_exchange . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][exchange_unit]" class="form-control exchange_unit" value="' . $exchange_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity]" class="form-control quantity" value="' . $quantity . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity_warehouse]" class="form-control quantity_warehouse" value="' . $quantity_warehouse . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity_compensation_sm]" class="form-control quantity_compensation_sm" value="' . $quantity_compensation_sm . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][conversion_quantity_unit]" class="form-control conversion_quantity_unit" value="' . $conversion_quantity_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][is_zinc]" class="form-control is_zinc" value="' . $is_zinc . '">
                                <input type="text" onchange="isStatusW()" name="itemBOMM[' . $index . '][quantity_compensation]" class="form-control quantity_compensation number-format" value="' . formatNumber($quantity_compensation) . '">
                            </td>
                            <td class="text-center quantity_convert_warehouse">
                                ' . formatNumber($quantity_convert_warehouse) . '
                            </td>
                            <td class="text-center quantity_warehouse">
                                ' . formatNumber($quantity_warehouse) . '
                            </td>
                            <td class="td-status text-center">
                                ' . $strStatus . '
                            </td>
                        </tr>';
                        $index++;
                    }
                }
            }
        }

        $data['trItems'] = $trItems;
        echo json_encode($data);
    }

    public function updateProductionsPlanCompensation()
    {
        $this->db->select('
            tbl_productions_plan.id as id
        ', false);
        $this->db->from('tbl_productions_plan');
        $productions_plan_bom = $this->db->get()->result_array();

        if (!empty($productions_plan_bom)) {
            foreach ($productions_plan_bom as $key => $value) {
                $id = $value['id'];
                $arrProductionsPlanCompensation = [];

                $this->db->select('
                    tbl_productions_plan_bom.item_type as item_type,
                    tbl_productions_plan_bom.item_id as item_id,
                    MAX(tbl_productions_plan_bom.quantity_compensation) as quantity_compensation,
                    tbl_materials.standard_unit as standard_unit,
                    tbl_materials.exchange_standard_unit as exchange_standard_unit,
                    tbl_productions_plan_bom.quantity_exchange as quantity_exchange,
                    tbl_materials.exchange_unit as exchange_unit,
                ', false);
                $this->db->from('tbl_productions_plan_bom');
                $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id');
                $this->db->where('tbl_productions_plan_bom.productions_plan_id', $id);
                $this->db->where('tbl_productions_plan_bom.item_type', 'materials');
                $this->db->group_by('tbl_productions_plan_bom.item_type, tbl_productions_plan_bom.item_id');
                $productions_plan_bom = $this->db->get()->result_array();
                if (!empty($productions_plan_bom)) {
                    foreach ($productions_plan_bom as $k => $val) {
                        $quantity_compensation = $val['quantity_compensation'];
                        $standard_unit = $val['standard_unit'];
                        $exchange_standard_unit = $val['exchange_standard_unit'];
                        $quantity_exchange = $val['quantity_exchange'];
                        $exchange_unit = $val['exchange_unit'];

                        $quantity_primary = $val['quantity_compensation'] * $val['quantity_exchange'] / $val['exchange_unit'];
                        $quantity_convert_warehouses = roundNumberFormat($quantity_primary / $val['exchange_standard_unit'] * $val['exchange_unit'], 0);

                        $arrProductionsPlanCompensation[] = [
                            'productions_plan_id' => $id,
                            'item_type' => $val['item_type'],
                            'item_id' => $val['item_id'],
                            'quantity_compensation' => $val['quantity_compensation'],
                            'quantity_primary' => $quantity_primary,
                            'quantity_convert_warehouses' => $quantity_convert_warehouses,
                        ];
                    }
                }

                $this->db->where('tbl_productions_plan_compensation.productions_plan_id', $id);
                $this->db->delete('tbl_productions_plan_compensation');
                $this->db->insert_batch('tbl_productions_plan_compensation', $arrProductionsPlanCompensation);
            }
        }
    }

    public function prepare_materials($id)
    {
        $data = [];

        $productions_orders = $this->manufactures_model->rowProductionsOrdersById($id);
        $productions_plan = $this->manufactures_model->getProductionsPlanPO($id);

        if ($this->input->post('save')) {
            $branch_id = $productions_orders['location_id'];
            $po_id = $id;

            $typeSuggestExporting = 9;
            $stage_id = STAGES_MATERIAL;

            $total_quantity = 0;
            $total_quantity_exchange = 0;
            $count_quantity = 0;
            $total_quantity_warehouse = 0;
            $total_quantity_payment = 0;
            $items = $this->input->post('items');
            $arrSumMaterials = [];
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $quantity_exchange = $value['quantity_exchange'];
                    $quantity_single = $value['quantity_single'];
                    $unit_id = $value['unit_id'];
                    $unit_parent_id = $value['unit_parent_id'];
                    $item_cs_id = $value['item_cs_id'];
                    $is_single_use = $value['is_single_use'];

                    $arr_item_cs_id_mt = explode('__', $value['item_cs_id']);
                    $item_id_mt = $arr_item_cs_id_mt[1];
                    $item_type_mt = $arr_item_cs_id_mt[0];

                    $quantity_warehouse = 0;
                    $unit_warehouse = 0;
                    $exchange_warehouse = 1;
                    $quantity_payment = 0;
                    $unit_payment = 0;
                    $exchange_payment = 1;
                    $exchange_unit = 1;
                    $recipe = 1;

                    $longs = 1;
                    $wide = 1;
                    $paper = 1;

                    if ($item_type_mt == "materials") {
                        $info_mt = $this->items_model->rowMaterial($item_id_mt);
                        $unit_warehouse = $info_mt['standard_unit'];
                        $exchange_warehouse = !empty($info_mt['exchange_standard_unit']) ? $info_mt['exchange_standard_unit'] : 1;

                        $unit_payment = $info_mt['unit_payment'];
                        $exchange_payment = !empty($info_mt['exchange_unit_payment']) ? $info_mt['exchange_unit_payment'] : 1;

                        $exchange_unit = $info_mt['exchange_unit'];

                        $longs = $info_mt['longs'];
                        $wide = $info_mt['wide'];
                        $paper = $info_mt['paper'];
                        $recipe = $info_mt['recipe'];
                    } else if ($item_type_mt == "tools_supplies") {
                        $info_mt = $this->tools_supplies_model->rowToolsSupplies($item_id_mt);
                        $unit_warehouse = $info_mt['unit_id'];
                        $unit_payment = $info_mt['unit_id'];
                    } else {
                        $info_mt = $this->products_model->rowProduct($item_id_mt);
                        // $unit_warehouse = $info_mt['unit_id'];
                        $unit_warehouse = $info_mt['conversion_unit'];
                        $exchange_warehouse = $info_mt['conversion_quantity_unit'];
                        $unit_payment = $info_mt['unit_id'];
                    }

                    $materials = !empty($value['materials']) ? $value['materials'] : NULL;
                    if (!empty($materials)) {
                        $isErrors = false;
                        foreach ($materials as $k => $v) {
                            $warehouses_items_mt = $v['warehouses_items'];
                            if (empty($warehouses_items_mt)) {
                                $data['result'] = 0;
                                $data['message'] = lang('Xin vui lòng chọn kho xuất');
                                echo json_encode($data);
                                die;
                            } else if ($warehouses_items_mt) {
                                foreach ($warehouses_items_mt as $kW => $vW) {
                                    if (empty($vW)) {
                                        $data['result'] = 0;
                                        $data['message'] = lang('Xin vui lòng chọn kho NPL');
                                        echo json_encode($data);
                                        die;
                                    }

                                    $vW = explode('__', $vW);
                                    $warehouse_item_id = $vW[0];
                                    $location_id = $vW[1];

                                    $lot_code = $vW[2];
                                    if (empty($lot_code) || $lot_code === 'NULL' || $lot_code === 'null' || $lot_code == null) {
                                        $lot_code = NULL;
                                    }

                                    $date_sx = $vW[3];
                                    if (empty($date_sx) || $date_sx === 'NULL' || $date_sx === 'null' || $date_sx == null) {
                                        $date_sx = NULL;
                                    }

                                    $date_sd = $vW[4];
                                    if (empty($date_sd) || $date_sd === 'NULL' || $date_sd === 'null' || $date_sd == null) {
                                        $date_sd = NULL;
                                    }

                                    $date_use = $vW[5];
                                    if (empty($date_use) || $date_use === 'NULL' || $date_use === 'null' || $date_use == null) {
                                        $date_use = NULL;
                                    }

                                    if ($item_type_mt == "semi_products_outside" || $item_type_mt == "semi_products" || $item_type_mt == "products") {
                                        $quantity_items = !empty($v['quantity_items'][$kW]) ? number_unformat($v['quantity_items'][$kW]) : 0;
                                        $quantity_primary = $quantity_items;
                                        $quantity_warehouse = roundNumberFormat($quantity_primary * $exchange_warehouse, 0);
                                        // $quantity_payment = $quantity_items;
                                        $quantity_payment = 0;
                                    } else {
                                        $quantity_items = !empty($v['quantity_items'][$kW]) ? number_unformat($v['quantity_items'][$kW]) : 0;
                                        $quantity_primary =  roundNumberFormat($quantity_items * $quantity_exchange / $exchange_unit);
                                        $quantity_warehouse = roundNumberFormat($quantity_primary / $exchange_warehouse * $exchange_unit, 0);

                                        if ($recipe == 1) {
                                            $quantity_payment = $quantity_primary / $exchange_payment * $exchange_unit;
                                        } else if ($recipe == 2) {
                                            $quantity_payment = $quantity_primary / $exchange_payment * $exchange_unit * $paper / 100;
                                        } else if ($recipe == 3) {
                                            $quantity_payment = $quantity_primary / $exchange_payment * $exchange_unit * $longs * $wide / 10000;
                                        }
                                    }


                                    $arrMaterials[] = [
                                        'type_item' => $item_type_mt,
                                        'item_id' => $item_id_mt,
                                        'item_code' => $info_mt['code'],
                                        'item_name' => $info_mt['name'],
                                        'unit_id' => $unit_id,
                                        'quantity_export' => $quantity_items,
                                        'unit_parent_id' => $unit_parent_id,
                                        'number_exchange' => $quantity_exchange,
                                        'quantity_exchange' => $quantity_primary,
                                        'location_id' => $location_id,
                                        'warehouse_item_id' => $warehouse_item_id,
                                        'lot_code' => $lot_code,
                                        'date_sx' => $date_sx,
                                        'date_sd' => $date_sd,
                                        'date_use' => $date_use,

                                        'quantity_warehouse' => $quantity_warehouse,
                                        'unit_warehouse' => $unit_warehouse,
                                        'exchange_warehouse' => $exchange_warehouse,
                                        'quantity_payment' => $quantity_payment,
                                        'unit_payment' => $unit_payment,
                                        'exchange_payment' => $exchange_payment,
                                        'is_single_use' => $is_single_use
                                    ];

                                    $total_quantity += $quantity_items;
                                    $total_quantity_exchange += $quantity_primary;
                                    $count_quantity++;
                                    $total_quantity_warehouse += $quantity_warehouse;
                                    $total_quantity_payment += $quantity_payment;

                                    $item_ccs_id = $item_id_mt . '__' . $item_type_mt . '__' . $warehouse_item_id . '__' . $location_id . '__' . $lot_code . '__' . $date_sx . '__' . $date_sd . '__' . $date_use;
                                    if (empty($arrSumMaterials[$item_ccs_id])) {
                                        $arrSumMaterials[$item_ccs_id] = [
                                            'item_ccs_id' => $item_ccs_id,
                                            'item_cs_id' => $item_cs_id,
                                            'item_id_mt' => $item_id_mt,
                                            'item_type' => $item_type_mt,
                                            'item_code' => $info_mt['code'],
                                            'item_name' => $info_mt['name'],
                                            'location_id' => $location_id,
                                            'warehouse_item_id' => $warehouse_item_id,
                                            'lot_code' => $lot_code,
                                            'date_sx' => $date_sx,
                                            'date_sd' => $date_sd,
                                            'date_use' => $date_use,
                                            'quantity_warehouse' => $quantity_warehouse,
                                        ];
                                    } else {
                                        $arrSumMaterials[$item_ccs_id]['quantity_warehouse'] = $arrSumMaterials[$item_cs_id]['quantity_warehouse'] + $quantity_warehouse;
                                    }
                                }
                            }
                        }
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Xin vui lòng chọn kho xuất');
                        echo json_encode($data);
                        die;
                    }
                }
            }

            if (!empty($arrSumMaterials)) {
                foreach ($arrSumMaterials as $k => $val) {
                    $item_id_mt = $val['item_id_mt'];
                    $item_type_mt = $val['item_type'];
                    $location_id = $val['location_id'];
                    $warehouse_item_id = $val['warehouse_item_id'];
                    $lot_code = !empty($val['lot_code']) ? $val['lot_code'] : NULL;
                    $date_sx = !empty($val['date_sx']) ? $val['date_sx'] : NULL;
                    $date_sd = !empty($val['date_sd']) ? $val['date_sd'] : NULL;
                    $date_use = !empty($val['date_use']) ? $val['date_use'] : NULL;
                    $quantity_warehouse = $val['quantity_warehouse'];
                    $code = $val['item_code'];

                    $quantityW = $this->manufactures_model->getTotalQuantitW($item_id_mt, $item_type_mt, $location_id, $warehouse_item_id, $lot_code, $date_sx, $date_sd, $date_use);
                    if ($quantity_warehouse > $quantityW['total_quantity']) {
                        $data['result'] = 0;
                        $data['message'] = 'Mã ' . $code . ' không đủ số lượng trong kho để xuất';
                        echo json_encode($data);
                        die;
                    }
                }
            }

            if (empty($arrMaterials)) {
                $data['result'] = 0;
                $data['message'] = lang('Không có nguyện phụ liệu để xuất');
                echo json_encode($data);
                die;
            }

            // print_arrays($arrMaterials);
            // print_arrays($this->input->post());

            $save_and_warehouse = 1;
            $export_name = 'Xuất kho NVL';
            $reference_suggest_exporting = getReference('stock');
            $dateGerenal = date('Y-m-d H:i:s');
            $staffGerenal = get_staff_user_id();

            $suggest_exporting = [
                'po_id' => $po_id,
                'productions_orders_details_id' => 0,
                'reference_no' => null,
                'reference_stock' => $reference_suggest_exporting,
                'date' => $dateGerenal,
                'export_name' => $export_name,
                'note' => '',
                'status' => 'un_approved',
                'total_quantity' => $total_quantity,
                'count_items' => $count_quantity,
                'total_quantity_exchange' => $total_quantity_exchange,
                'total_quantity_warehouse' => $total_quantity_warehouse,
                'total_quantity_payment' => $total_quantity_payment,
                'created_by' => $staffGerenal,
                'date_created' => $dateGerenal,
                'convert_stock_by' => $staffGerenal,
                'date_convert_stock' => $dateGerenal,
                'status_stock' => 'approved',
                'date_stock' => $dateGerenal,
                'user_stock' => $staffGerenal,
                'type' => $typeSuggestExporting,
                'date_convert_stock' => $dateGerenal,
                'warehouse_id' => 0,
                'save_and_warehouse' => $save_and_warehouse,
                'pois_id' => 0,
                'use_productions_plan' => 1,
                'cqi_id' => 0,
                'stage_id' => $stage_id,
                'branch_id' => $branch_id
            ];

            $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
            if ($suggest_exporting_id) {
                updateReference('stock');

                if (!empty($arrMaterials)) {
                    foreach ($arrMaterials as $k => $v) {
                        $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                    }
                    $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                }

                //suggest exporting
                $errors = '';
                if (!empty($save_and_warehouse)) {
                    $id = $suggest_exporting_id;
                    $_data = array(
                        'warehouseman_id' => get_staff_user_id(),
                        'date_warehouseman' => date('Y-m-d H:i:s')
                    );

                    if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                        $errors .= '<div>' . lang('test_quantyti_time_return') . '</div>';
                    } else {
                        $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                        if ($success) {
                            log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                            $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                            $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);

                            $content = lang('tnh_his_warehouse_exporting_producion_total');
                            $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                            $content = str_replace('{$2}', $productions_orders['reference_no'], $content);

                            insertActivityLog([
                                'type_parent_obj' => 'exporting_producion',
                                'table_obj' => 'tbl_suggest_exporting',
                                'id_obj' => $id,
                                'name_obj' => $suggest_exporting['reference_stock'],
                                'content' => $content,
                                'actions' => 'warehouse'
                            ]);
                        }
                    }
                }

                $content = lang('tnh_his_add_exporting_producion_total');
                $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                $content = str_replace('{$2}', $productions_orders['reference_no'], $content);

                insertActivityLog([
                    'type_parent_obj' => 'exporting_producion',
                    'table_obj' => 'tbl_suggest_exporting',
                    'id_obj' => $suggest_exporting_id,
                    'name_obj' => $reference_suggest_exporting,
                    'content' => $content,
                    'actions' => 'add'
                ]);


                $op = [
                    'active' => 1,
                    'staff_active' => $staffGerenal,
                    'date_active' => $dateGerenal,
                ];
                $this->manufactures_model->updateProductionsOrdersItemsStagesPO($po_id, $stage_id, $op);

                //check
                $this->manufactures_model->checkPrepareMaterialsTotal(['po_id' => $po_id]);

                $data['result'] = 1;
                $data['message'] = lang('success') . '' . $errors;
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            echo json_encode($data);
            die;
        }

        $data['id'] = $id;
        $data['productions_orders'] = $productions_orders;
        $data['productions_plan'] = $productions_plan;
        $data['title'] = lang('tnh_exporting_stock_producion');
        $this->load->view('admin/manufactures/handling_prepare_materials_total', $data);
    }

    public function loadALLPrepareMaterialsTotal()
    {
        $data = [];
        $po_id = $this->input->post('po_id');
        $production_plan_id = $this->input->post('production_plan_id');
        if (empty($production_plan_id)) $production_plan_id = 0;

        $tbProductionsPlanCompensation = "(
            SELECT
                tbl_productions_plan_compensation.item_id, 
                tbl_productions_plan_compensation.item_type,
                tbl_productions_plan_compensation.quantity_primary as quantity_primary,
                tbl_productions_plan_compensation.quantity_compensation as quantity_compensation
            FROM tbl_productions_plan_compensation
            WHERE tbl_productions_plan_compensation.productions_plan_id = $production_plan_id
            GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type
        ) tb_productions_plan_compensation";

        $this->db->select('
            tbl_productions_orders_items_sub.type as type,
            tbl_productions_orders_items_sub.item_id as item_id,
            CONCAT(tbl_productions_orders_items_sub.type, "__", tbl_productions_orders_items_sub.item_id) as item_cs_id,
            tbl_productions_orders_items_sub.unit_id as unit_id_manufactures,
            tblunits.unit as unit_name_manufactures,
            SUM(tbl_productions_orders_items_sub.quantity) + tb_productions_plan_compensation.quantity_compensation as quantity,
            tbl_productions_orders_items_sub.quantity_exchange as quantity_exchange,
            tb_productions_plan_compensation.quantity_compensation as quantity_compensation,
            tbl_productions_orders_items_sub.is_single_use as is_single_use,
            tbl_productions_orders_items_sub.unit_id as unit_id,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            tbl_productions_orders_items_sub.quantity_single as quantity_single,
        ', false);
        $this->db->from('tbl_productions_orders_items_sub');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->join($tbProductionsPlanCompensation, 'tb_productions_plan_compensation.item_id = tbl_productions_orders_items_sub.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_orders_items_sub.type', 'left');
        $this->db->where('tbl_productions_orders_items_sub.productions_orders_id', $po_id);
        $this->db->where('tbl_productions_orders_items_sub.type !=', 'element');
        $this->db->group_by('tbl_productions_orders_items_sub.type, tbl_productions_orders_items_sub.item_id');
        $itemsBOM = $this->db->get()->result_array();
        if (!empty($itemsBOM)) {
            foreach ($itemsBOM as $key => $value) {
                $item_type = $value['type'];
                $item_id = $value['item_id'];

                $images = '';
                $conversion_quantity_unit = 1;
                if ($item_type == "materials") {
                    $info = $this->items_model->rowMaterial($item_id);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/materials/' . $info['images']);
                    }
                } else {
                    $info = $this->products_model->rowProduct($item_id);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                    $conversion_quantity_unit = $info['conversion_quantity_unit'];
                }

                $isWarehouses = $this->manufactures_model->isWarehouses($item_id, $item_type);
                $warehouses_manufactures = $this->manufactures_model->getWarehouseManufacturesNew($item_type, $item_id, 0, 0, $production_plan_id);

                $this->db->select('
                    SUM(tbl_suggest_exporting_items.quantity_export) as quantity_export
                ', false);
                $this->db->from('tbl_suggest_exporting');
                $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
                $this->db->where('tbl_suggest_exporting.po_id', $po_id);
                $this->db->where('tbl_suggest_exporting.stage_id', STAGES_MATERIAL);
                $this->db->where('tbl_suggest_exporting_items.type_item', $item_type);
                $this->db->where('tbl_suggest_exporting_items.item_id', $item_id);
                $suggest_exporting = $this->db->get()->row_array();


                $itemsBOM[$key]['item_code'] = $info['code'];
                $itemsBOM[$key]['item_name'] = $info['name'];
                $itemsBOM[$key]['images'] = $images;
                $itemsBOM[$key]['warehousePlan'] = json_encode($warehouses_manufactures);;
                $itemsBOM[$key]['isWarehouses'] = $isWarehouses;
                $itemsBOM[$key]['quantity_export'] = (float)$suggest_exporting['quantity_export'];
                $itemsBOM[$key]['item_type_name'] = lang($item_type);
                $itemsBOM[$key]['conversion_quantity_unit'] = $conversion_quantity_unit;
            }
        }

        $data['items'] = $itemsBOM;
        echo json_encode($data);
    }

    function searchWarehousers()
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $item_cs_id = !empty($params['item_cs_id']) ? $params['item_cs_id'] : null;
        $production_plan_id = !empty($params['production_plan_id']) ? $params['production_plan_id'] : null;

        $item_id = 0;
        $item_type = '';
        if (!empty($item_cs_id)) {
            $item_cs_id = explode('__', $item_cs_id);
            $item_type = $item_cs_id[0];
            $item_id = $item_cs_id[1];
            if ($item_type == "materials") {
                $item_type = "nvl";
            } else if ($item_type == "tools_supplies") {
                $item_type = "tools";
            } else {
                $item_type = "product";
            }
        }

        $results = [];
        $this->db->select('tblwarehouse.id, tblwarehouse.name');
        $this->db->from('tblwarehouse');
        // if (!empty($production_plan_id)) {
            $this->db->where('tblwarehouse.id', WAREHOUSES_CAPACITY);
        // }
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
                    WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '" AND tblwarehouse_items.warehouse_id = "' . $warehouse_id . '"
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
                if (!empty($production_plan_id)) {
                    $this->db->group_start();
                    $this->db->where('tbllocaltion_warehouses.productions_plan_id', $production_plan_id);
                    $this->db->or_where('tbllocaltion_warehouses.productions_plan_id', 0);
                    $this->db->group_end();
                }
                if ($term) {
                    $this->db->group_start();
                    $this->db->like('tbllocaltion_warehouses.name', $term);
                    $this->db->group_end();
                }
                $this->db->limit($limit);
                $location_warehouses = $this->db->get()->result_array();
                if (!empty($location_warehouses)) {
                    foreach ($location_warehouses as $k => $val) {
                        $name_location = '';

                        $product_quantity = $val['product_quantity'];
                        $lot_code = $val['lot_code'] ? ' - Lot: ' . $val['lot_code'] : '';
                        $date_sx = $val['date_sx'] ? ' - Ngày SX: ' . _d($val['date_sx']) : '';
                        $date_sd = $val['date_sd'] ? ' - Ngày SD: ' . _d($val['date_sd']) : '';

                        $location_warehouses[$k]['text'] = $val['text'] . $lot_code . $date_sx . $date_sd;
                    }
                }

                $results[] = ['text' => $value['name'], 'children' => $location_warehouses];
            }
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function view_modal($id = '')
    {
        $data = array();
        $data['id'] = $id;
        $this->load->view('admin/manufactures/view_modal', $data);
    }

    public function detail_productions_plan_purchase($id = 0)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('detail_productions_plan_purchase', array(
                'id' => $id,
            ));
        }
    }

    public function finished_stages($po_id)
    {
        $data = [];

        $productions_orders = $this->manufactures_model->rowProductionsOrdersById($po_id);
        $productions_plan = $this->manufactures_model->getProductionsPlanPO($po_id);

		$this->load->model('hand_over_model');
        if ($this->input->post('save')) {
            $branch_id = $productions_orders['location_id'];

			$id_delivery_records = $this->input->post('id_delivery_records');
			$hand_over_task_id = $this->input->post('hand_over_task_id');
			$category_hand = $this->input->post('category_hand');
			$task_hand_over_qualified = $this->input->post('task_hand_over_qualified');





            $dataPOST = $this->input->post();
            $warehouses_products = $dataPOST['warehouses_products'];
            if (empty($warehouses_products)) {
                $data['result'] = 0;
                $data['message'] = lang('Vui lòng chọn kho thành phẩm');
                echo json_encode($data);
                die;
            }

            $save_and_warehouse = 1;
            $export_name = 'Xuất kho NVL';

            if (!empty($dataPOST['items'])) {
                $items = $dataPOST['items'];
                $arrItem = [];
                $dateGerenal = date('Y-m-d H:i:s');
                $staffGerenal = get_staff_user_id();
				$total_quantity_err = 0;
                foreach ($items as $key => $value) {
                    $type = $value['type'];
                    if ($type == 2) {
                        $pod_id = $value['pod_id'];
                        $pois_id = $value['pois_id'];
                        $stage_id = $value['stage_id'];
                        $sp_cqi_id = 0;
                        $sp_cqis_id = 0;
                        $qc_remake_id = 0;
                        $typePurchase = 3;
                        $typeSuggestExporting = 6;
                        $final_stage = $value['final_stage'];

                        $stage_id_w = $stage_id;
                        if ($final_stage) {
                            $stage_id_w = 0;
                        }

                        $localtion_semi_product = $this->site_model->getLocationPOD($pod_id, $warehouses_products, $stage_id_w);
                        $localtion_semi_product_errors = 0;
                        $dataItems = [];
                        $arrSaveBom = [];
                        $itemsErrors = [];

                        $total_quantity_purchases = 0;
                        $count_quantity_purchases = 0;

                        $total_quantity_se = 0;
                        $total_quantity_exchange_se = 0;
                        $count_quantity_se = 0;
                        $total_quantity_warehouse = 0;
                        $total_quantity_payment = 0;

                        $item_type_sp = $value['type_items'];
                        $items_id = $value['items_id'];

                        $item_code_sp = $value['item_code'];
                        $item_name_sp = $value['item_name'];

                        $quantity_exchange_sp = 1;
                        $quantity_single_sp = 1;
                        $quantity_semi_product = number_unformat($value['quantity_input']);
                        $unit_id_sp = $value['unit_id'];
                        $unit_parent_id_sp = $value['unit_id'];
                        $quantity_errors = number_unformat($value['quantity_error']);
                        $total_quantity_errors = 0;
                        $typePurchase = 3;
                        $typeSuggestExporting = 6;

                        if (empty($quantity_semi_product) && empty($quantity_errors)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng nhập số lượng lớn hơn 0');
                            echo json_encode($data);
                            die;
                        }

                        $info = $this->products_model->rowProduct($items_id);
                        $_unit_id = $info['conversion_unit'];
                        $_conversion_quantity_unit = $info['conversion_quantity_unit'];

                        $quantity_stock = $quantity_semi_product;
                        $quantity_unit = roundNumberFormat($quantity_stock/$_conversion_quantity_unit, 4);

                        $dataItems[] = [
                            'productions_orders_details_id' => $pod_id,
                            'type_item' => $item_type_sp,
                            'item_id' => $items_id,
                            'location_id' => $localtion_semi_product,
                            'item_code' => $item_code_sp,
                            'item_name' => $item_name_sp,
                            'quantity' => $quantity_semi_product,
                            'quantity_exchange' => $quantity_exchange_sp,
                            'quantity_single' => $quantity_single_sp,
                            'quantity_semi_product' => $quantity_semi_product,
                            'type_order' => $item_type_sp,
                            'quantity_stock' => $quantity_stock,
                            'quantity_unit' => $quantity_unit,
                            'unit_id' => $_unit_id,
                            'conversion_quantity_unit' => $_conversion_quantity_unit,
                        ];
                        $total_quantity_purchases += $quantity_semi_product;
                        $count_quantity_purchases++;

                        if (!empty($quantity_errors)) {
                            if (empty($localtion_semi_product_errors)) {
                                $localtion_semi_product_errors = $this->site_model->getLocationPOD($pod_id, WAREHOUSES_ERRORS, $stage_id_w);
                            }

                            $quantity_stock = $quantity_errors;
                            $quantity_unit = roundNumberFormat($quantity_errors/$_conversion_quantity_unit, 4);

                            $itemsErrors[] = [
                                'productions_orders_details_id' => $pod_id,
                                'type_item' => $item_type_sp,
                                'item_id' => $items_id,
                                'location_id' => $localtion_semi_product_errors,
                                'item_code' => $item_code_sp,
                                'item_name' => $item_name_sp,
                                'quantity' => $quantity_errors,
                                'quantity_exchange' => $quantity_exchange_sp,
                                'quantity_single' => $quantity_single_sp,
                                'quantity_semi_product' => $quantity_errors,
                                'quantity_stock' => $quantity_stock,
                                'quantity_unit' => $quantity_unit,
                                'unit_id' => $_unit_id,
                                'conversion_quantity_unit' => $_conversion_quantity_unit,
                            ];
                            $total_quantity_errors += $quantity_errors;
                        }

                        $purchases = [
                            'reference_no' => '',
                            'date' => $dateGerenal,
                            'productions_orders_details_id' => $pod_id,
                            'warehouse_id' => $warehouses_products,
                            'count_items' => $count_quantity_purchases,
                            'total_quantity' => $total_quantity_purchases,
                            'created_by' => $staffGerenal,
                            'date_created' => $dateGerenal,
                            'status' => 'un_approved',
                            'pois_id' => $pois_id,
                            'type' => $typePurchase,
                            'sp_type' => $type,
                            'cqi_id' => $sp_cqis_id,
                            'po_id' => $po_id,
                            'final_stage' => $final_stage,
                            'branch_id' => $branch_id,
                        ];

                        $arrItem[] = [
                            'pod_id' => $pod_id,
                            'type' => $type,
                            'dataItems' => $dataItems,
                            'itemsErrors' => $itemsErrors,
                            'purchases' => $purchases,
                            'total_quantity_errors' => $total_quantity_errors
                        ];
                    } else if ($type == 3) {
                        $pod_id = $value['pod_id'];
                        $pois_id = $value['pois_id'];
                        $stage_id = $value['stage_id'];
                        $sp_cqi_id = 0;
                        $sp_cqis_id = 0;
                        $qc_remake_id = 0;
                        $typePurchase = 4;
                        $typeSuggestExporting = 7;
                        $final_stage = $value['final_stage'];

                        $stage_id_w = $stage_id;
                        if ($final_stage) {
                            $stage_id_w = 0;
                        }

                        $localtion_semi_product = $this->site_model->getLocationPOD($pod_id, $warehouses_products, $stage_id_w);
                        $localtion_semi_product_errors = 0;
                        $dataItems = [];
                        $arrSaveBom = [];
                        $itemsErrors = [];

                        $total_quantity_purchases = 0;
                        $count_quantity_purchases = 0;

                        $total_quantity_se = 0;
                        $total_quantity_exchange_se = 0;
                        $count_quantity_se = 0;
                        $total_quantity_warehouse = 0;
                        $total_quantity_payment = 0;

                        $item_type_sp = $value['type_items'];
                        $items_id = $value['items_id'];

                        $item_code_sp = $value['item_code'];
                        $item_name_sp = $value['item_name'];

                        $quantity_exchange_sp = 1;
                        $quantity_single_sp = 1;
                        $quantity_semi_product = number_unformat($value['quantity_input']);
                        $unit_id_sp = $value['unit_id'];
                        $unit_parent_id_sp = $value['unit_id'];
                        $quantity_errors = number_unformat($value['quantity_error']);
                        $total_quantity_errors = 0;
                        $typePurchase = 3;
                        $typeSuggestExporting = 6;
                        $quantity_check_warehouses = 0;
                        if (empty($quantity_semi_product) && empty($quantity_errors)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng nhập số lượng lớn hơn 0');
                            echo json_encode($data);
                            die;
                        }

                        $infoC = $this->products_model->rowProduct($items_id);
                        $_unit_id = $infoC['conversion_unit'];
                        $_conversion_quantity_unit = $infoC['conversion_quantity_unit'];

                        $quantity_stock = $quantity_semi_product;
                        $quantity_unit = roundNumberFormat($quantity_stock/$_conversion_quantity_unit, 4);

                        $dataItems[] = [
                            'productions_orders_details_id' => $pod_id,
                            'type_item' => $item_type_sp,
                            'item_id' => $items_id,
                            'location_id' => $localtion_semi_product,
                            'item_code' => $item_code_sp,
                            'item_name' => $item_name_sp,
                            'quantity' => $quantity_semi_product,
                            'quantity_exchange' => $quantity_exchange_sp,
                            'quantity_single' => $quantity_single_sp,
                            'quantity_semi_product' => $quantity_semi_product,
                            'type_order' => $item_type_sp,
                            'quantity_stock' => $quantity_stock,
                            'quantity_unit' => $quantity_unit,
                            'unit_id' => $_unit_id,
                            'conversion_quantity_unit' => $_conversion_quantity_unit,
                        ];
                        $quantity_check_warehouses += $quantity_semi_product;
                        $total_quantity_purchases += $quantity_semi_product;
                        $count_quantity_purchases++;

                        if (!empty($quantity_errors)) {
                            if (empty($localtion_semi_product_errors)) {
                                $localtion_semi_product_errors = $this->site_model->getLocationPOD($pod_id, WAREHOUSES_ERRORS, $stage_id_w);
                            }

                            $quantity_stock = $quantity_errors;
                            $quantity_unit = roundNumberFormat($quantity_errors/$_conversion_quantity_unit, 4);

                            $itemsErrors[] = [
                                'productions_orders_details_id' => $pod_id,
                                'type_item' => $item_type_sp,
                                'item_id' => $items_id,
                                'location_id' => $localtion_semi_product_errors,
                                'item_code' => $item_code_sp,
                                'item_name' => $item_name_sp,
                                'quantity' => $quantity_errors,
                                'quantity_exchange' => $quantity_exchange_sp,
                                'quantity_single' => $quantity_single_sp,
                                'quantity_semi_product' => $quantity_errors,
                                'quantity_stock' => $quantity_stock,
                                'quantity_unit' => $quantity_unit,
                                'unit_id' => $_unit_id,
                                'conversion_quantity_unit' => $_conversion_quantity_unit,
                            ];
                            $quantity_check_warehouses += $quantity_errors;
                            $total_quantity_errors += $quantity_errors;
                        }

                        $purchases = [
                            'reference_no' => '',
                            'date' => $dateGerenal,
                            'productions_orders_details_id' => $pod_id,
                            'warehouse_id' => $warehouses_products,
                            'count_items' => $count_quantity_purchases,
                            'total_quantity' => $total_quantity_purchases,
                            'created_by' => $staffGerenal,
                            'date_created' => $dateGerenal,
                            'status' => 'un_approved',
                            'pois_id' => $pois_id,
                            'type' => $typePurchase,
                            'sp_type' => $type,
                            'cqi_id' => $sp_cqis_id,
                            'po_id' => $po_id,
                            'final_stage' => $final_stage,
                            'branch_id' => $branch_id,
                        ];

                        //
                        $arrMaterials = [];
                        $stage_id_pre = $value['stage_id_pre'];
                        $query = "(
                            SELECT
                                tblwarehouse_items.warehouse_id, 
                                tblwarehouse_items.localtion,
                                tblwarehouse_items.lot_code, 
                                tblwarehouse_items.date_sx, 
                                tblwarehouse_items.date_sd, 
                                tblwarehouse_items.date_use,
                                tblwarehouse_items.type_items as item_type,
                                tblwarehouse_items.id_items as item_id,
                                1 as quantity_singe_primary,
                                1 as quantity_exchange,
                                1 as quantity_single,
                                SUM(tblwarehouse_items.product_quantity) as quantity_primary,
                                SUM(tblwarehouse_items.product_quantity) as quantity,
                                0 as unit_id,
                                0 as unit_parent_id,
                                0 as is_single_use,
                                0 as quantity_order,
                                0 as quota_material_replace_t,
                                1 as landscape_print_size,
                                1 as vertical_print_size,
                                1 as number_children_size,
                                1 as paper_exchange
                            FROM tblwarehouse_items
                            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                            WHERE tblwarehouse_items.type_items = 'product' AND tbllocaltion_warehouses.pod_id = $pod_id AND tbllocaltion_warehouses.stage_id = $stage_id_pre AND tblwarehouse_items.warehouse_id 
                            != " . WAREHOUSES_ERRORS . " AND tblwarehouse_items.id_items = $items_id AND tblwarehouse_items.product_quantity > 0
                            GROUP BY tblwarehouse_items.warehouse_id, tblwarehouse_items.localtion, tblwarehouse_items.lot_code,tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use
                        )";
                        $dtW = $this->db->query($query)->result_array();
                        $quantityTotalW = 0;
                        $cQuantityNeedHold = $quantity_check_warehouses;
                        if ($dtW) {
                            foreach ($dtW as $k => $val) {
                                $qtyW = $val['quantity'];
                                $tempQuantity = $cQuantityNeedHold;
                                if ($tempQuantity > 0) {
                                    $cQuantityNeedHold = $cQuantityNeedHold - $qtyW;
                                    if ($cQuantityNeedHold > 0) {
                                        $quantityW = $qtyW;
                                    } else {
                                        $quantityW = $tempQuantity;
                                    }

                                    $info_mt = $this->products_model->rowProduct($items_id);
                                    // $unit_warehouse = $info_mt['conversion_unit'];
                                    $exchange_warehouse = $info_mt['conversion_quantity_unit'];
                                    $number_exchange = $info_mt['conversion_quantity_unit'];
                                    $quantity_exchange = roundNumberFormat($quantityW / $exchange_warehouse, 4);
                                    // $unit_id_mt = $unit_warehouse;

                                    if ($quantityW > 0) {
                                        $arrMaterials[] = [
                                            'type_item' => $item_type_sp,
                                            'item_id' => $items_id,
                                            'item_code' => $item_code_sp,
                                            'item_name' => $item_name_sp,
                                            'unit_id' => $unit_id_sp,
                                            'quantity_export' => $quantityW,
                                            'unit_parent_id' => $info_mt['unit_id'],
                                            'number_exchange' => $number_exchange,
                                            'quantity_exchange' => $quantity_exchange,
                                            'location_id' => $val['localtion'],
                                            'warehouse_item_id' => $val['warehouse_id'],
                                            'lot_code' => $val['lot_code'],
                                            'date_sx' => $val['date_sx'],
                                            'date_sd' => $val['date_sd'],
                                            'date_use' => $val['date_use'],

                                            'quantity_warehouse' => $quantityW,
                                            'unit_warehouse' => $unit_id_sp,
                                            'exchange_warehouse' => $exchange_warehouse,
                                            'quantity_payment' => $quantityW,
                                            'unit_payment' => $unit_id_sp,
                                            'exchange_payment' => 1,
                                            'is_single_use' => 0
                                        ];

                                        $total_quantity_se += $quantityW;
                                        $total_quantity_exchange_se += $quantity_exchange;
                                        $count_quantity_se++;
                                        $total_quantity_warehouse += $quantityW;
                                        $total_quantity_payment += $quantityW;
                                    }
                                }
                                $quantityTotalW += $val['quantity'];
                            }
                        }

                        if ($quantity_check_warehouses > $quantityTotalW) {
                            $data['result'] = 0;
                            $data['message'] = lang('Mặt hàng [' . $item_code_sp . '] không đủ số lượng để xuất công đoạn trước');
                            echo json_encode($data);
                            die;
                        }

                        $suggest_exporting = [
                            'productions_orders_details_id' => $pod_id,
                            'reference_no' => null,
                            'date' => $dateGerenal,
                            'export_name' => $export_name,
                            'note' => '',
                            'status' => 'un_approved',
                            'total_quantity' => $total_quantity_se,
                            'count_items' => $count_quantity_se,
                            'total_quantity_exchange' => $total_quantity_exchange_se,
                            'total_quantity_warehouse' => $total_quantity_warehouse,
                            'total_quantity_payment' => $total_quantity_payment,
                            'created_by' => $staffGerenal,
                            'date_created' => $dateGerenal,
                            'convert_stock_by' => $staffGerenal,
                            'date_convert_stock' => $dateGerenal,
                            'status_stock' => 'approved',
                            'date_stock' => $dateGerenal,
                            'user_stock' => $staffGerenal,
                            'type' => $typeSuggestExporting,
                            'date_convert_stock' => $dateGerenal,
                            'warehouse_id' => 0,
                            'save_and_warehouse' => $save_and_warehouse,
                            'pois_id' => $pois_id,
                            'use_productions_plan' => 0,
                            'cqi_id' => $sp_cqis_id,
                            'po_id' => $po_id,
                            'branch_id' => $branch_id,
                        ];

                        $arrItem[] = [
                            'pod_id' => $pod_id,
                            'type' => $type,
                            'dataItems' => $dataItems,
                            'itemsErrors' => $itemsErrors,
                            'purchases' => $purchases,
                            'suggest_exporting' => $suggest_exporting,
                            'arrMaterials' => $arrMaterials,
                            'total_quantity_errors' => $total_quantity_errors,
                        ];
                    } else if ($type == 0) {
                        $pois_id = $value['pois_id'];
                        $pod_id = $value['pod_id'];

                        $arrItem[] = [
                            'pod_id' => $pod_id,
                            'pois_id' => $pois_id,
                            'type' => $type,
                        ];
                    }
					$total_quantity_err += $total_quantity_errors;
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Không có dữ liệu để lưu');
                echo json_encode($data);
                die;
            }



			//công làm
			if(!empty($category_hand) && !empty($hand_over_task_id)) {
				if (empty($id_delivery_records)) {
					$delivery_records = [
						'reference_no' => get_option('prefix_delivery_records') . sprintf('%06d', ch_getMaxID('id', 'tbl_delivery_records') + 1),
						'date' => date('Y-m-d H:i:s'),
						'staff' => get_staff_user_id(),
						'type_object' => 'productions_orders',
						'category_hand' => $category_hand,
						'type_create' => 'productions_orders',
						'id_create' => $po_id,
                        'id_branch' => $branch_id
					];
					$delivery_records['created_by'] = get_staff_user_id();
					$delivery_records['date_created'] = date('Y-m-d H:i:s');
					$id_delivery_records = $this->hand_over_model->insertDeliveryRecords($delivery_records);
					if (!empty($id_delivery_records)) {
						$this->db->insert('tbl_delivery_records_object', [
							'id_delivery_records' => $id_delivery_records,
							'id_object' => $po_id
						]);
					}
				}
			}
			$qualified = true;
			$count_task_hand_over = 0;
			$id_delivery_records_detail = 0;
			if(!empty($id_delivery_records)) {
				if(!empty($hand_over_task_id)) {
					foreach ($hand_over_task_id as $key => $value) {
						if($task_hand_over_qualified[$key] == 2) {
							$qualified = false;
							$count_task_hand_over++;
						}

						$arrayInsert = [
							'delivery_records_id' => $id_delivery_records,
							'hand_over_task_id' => $value,
							'task_hand_over_qualified' => !empty($task_hand_over_qualified[$key]) ? $task_hand_over_qualified[$key] : 0,
						];

						$this->db->where('delivery_records_id', $id_delivery_records);
						$this->db->where('hand_over_task_id', $value);
						$delivery_records_task = $this->db->get('tbl_delivery_records_task')->row();
						if(!empty($delivery_records_task)) {
							$this->db->where('id', $delivery_records_task->id);
							$this->db->update('tbl_delivery_records_task', $arrayInsert);
							if($task_hand_over_qualified[$key] == 2) {
								$id_delivery_records_detail = $delivery_records_task->id;
							}
						}
						else {
							if (!empty($arrayInsert['task_hand_over_qualified'])) {
								$arrayInsert['staff_id'] = get_staff_user_id();
								$arrayInsert['date_check'] = date('Y-m-d H:i:s');
							}
							$this->db->insert('tbl_delivery_records_task', $arrayInsert);
							if($task_hand_over_qualified[$key] == 2) {
								$id_delivery_records_detail = $this->db->insert_id();
							}
						}
					}
					$data['id_delivery_records'] = $id_delivery_records;
				}
			}

			if(empty($qualified)) {
				$data['success'] = true;
				$data['alert_type'] = 'warning';
				$data['id_delivery_records'] = $id_delivery_records;
				$data['href'] = admin_url('production_report/detail?id_delivery_records='.$id_delivery_records .'&quantity_err=' . $total_quantity_err . ($count_task_hand_over == 1 ? ('&id_delivery_records_detail=' . $id_delivery_records_detail) : ''));
				$data['message'] = lang('Tạo biên bản bàn giao thành công');
				echo json_encode($data);
				die;
			}
			//end công làm



            if (!empty($arrItem)) {
                foreach ($arrItem as $kAI => $vAI) {
                    $pod_id = $vAI['pod_id'];
                    $type = $vAI['type'];
                    $dataItems = !empty($vAI['dataItems']) ? $vAI['dataItems'] : null;
                    $itemsErrors = !empty($vAI['itemsErrors']) ? $vAI['itemsErrors'] : null;
                    $purchases = !empty($vAI['purchases']) ? $vAI['purchases'] : null;

                    $dtPodC = get_table_where('tbl_productions_orders_details', ['id' => $pod_id], "", "row_array");
                    $object_type_c = $dtPodC['object_type'];

                    if ($type == 2 && !empty($purchases)) {
                        $sp_pod_id = $purchases['productions_orders_details_id'];
                        $sp_pois_id = $purchases['pois_id'];
                        $sp_type = $purchases['sp_type'];
                        $final_stage = $purchases['final_stage'];
                        $save_and_warehouse = 1;

                        //
                        $type_business_plan = 0;
                        if ($final_stage && $object_type_c == 'business_plan') {
                            $isTranferBusinessItem = $this->manufactures_model->isTranferBusinessItem($dtPodC['productions_orders_item_id']);
                            if ($isTranferBusinessItem) {
                                $business_plan_item_id = $isTranferBusinessItem;
                                $dtDataQuantityTrans = $this->manufactures_model->getDataTransferItemAndPurchase($business_plan_item_id, $sp_pod_id);
        
                                $quantity_tranfer_business_item = $dtDataQuantityTrans['quantity_tranfer_business_item'];
                                $quantity_purchase_products = $dtDataQuantityTrans['quantity_purchase_products'];
        
                                if (!empty($dataItems)) {
                                    $quantityNeedBusiness = $quantity_tranfer_business_item - $quantity_purchase_products;
                                    if ($quantityNeedBusiness > 0) {
                                        foreach ($dataItems as $key => $value) {
                                            $quantityE = $value['quantity'];
                                            $tempQuantity = $quantityNeedBusiness;
        
                                            if ($quantityNeedBusiness > 0) {
                                                $quantityNeedBusiness = $quantityNeedBusiness - $quantityE;
                                                if ($quantityNeedBusiness > 0) {
                                                    $quantityP = $quantityE;
                                                } else {
                                                    $quantityP = $tempQuantity;
                                                }
                                            }
        
                                            if ($quantityE <= $quantityP) {
                                                $type_business_plan = 1;
                                            } else {
                                                $dataItems[$key]['quantity'] = $dataItems[$key]['quantity'] - $quantityP;
                                                $dataItems[$key]['quantity_semi_product'] = $dataItems[$key]['quantity'];
        
                                                $quantity_stock = $dataItems[$key]['quantity'];
                                                $_conversion_quantity_unit = $value['conversion_quantity_unit'];
                                                $quantity_unit = roundNumberFormat($quantity_stock/$_conversion_quantity_unit, 4);
                                                $dataItems[$key]['quantity_stock'] = $quantity_stock;
                                                $dataItems[$key]['quantity_unit'] = $quantity_unit;
                                                // $total_quantity_purchases-= $quantityP;
                                                $purchases['total_quantity'] = $purchases['total_quantity'] - $quantityP;
                                                //
                                                $_quantity_semi_product = $quantityP;
                                                $_quantity_stock = $quantityP;
                                                $_quantity_unit = roundNumberFormat($_quantity_stock/$_conversion_quantity_unit, 4);
        
                                                $dataItems[$key]['purchase_transfer'] = [
                                                    'date' => $dateGerenal,
                                                    'productions_orders_details_id' => $purchases['productions_orders_details_id'],
                                                    'warehouse_id' => $purchases['warehouse_id'],
                                                    'count_items' => 1,
                                                    'total_quantity' => $_quantity_stock,
                                                    'created_by' => $staffGerenal,
                                                    'date_created' => $dateGerenal,
                                                    'status' => 'un_approved',
                                                    'pois_id' => $purchases['pois_id'],
                                                    'type' => $purchases['type'],
                                                    'sp_type' => $purchases['sp_type'],
                                                    'final_stage' => $final_stage,
                                                    'cqi_id' => $purchases['cqi_id'],
                                                    'type_business_plan' => 1,
                                                    'purchase_transfer_items' => [
                                                        [
                                                            'productions_orders_details_id' => $value['productions_orders_details_id'],
                                                            'type_item' => $value['type_item'],
                                                            'item_id' => $value['item_id'],
                                                            'location_id' => $value['location_id'],
                                                            'item_code' => $value['item_code'],
                                                            'item_name' => $value['item_name'],
                                                            'quantity' => $_quantity_semi_product,
                                                            'quantity_exchange' => $value['quantity_exchange'],
                                                            'quantity_single' => $value['quantity_single'],
                                                            'quantity_semi_product' => $_quantity_semi_product,
                                                            'type_order' => $value['type_order'],
                                                            'quantity_stock' => $_quantity_stock,
                                                            'quantity_unit' => $_quantity_unit,
                                                            'unit_id' => $value['unit_id'],
                                                            'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                                                        ]
                                                    ]
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $purchases['type_business_plan'] = $type_business_plan;
                        //


                        $reference_purchase = getReference('purchase_products');
                        $purchases['reference_no'] = $reference_purchase;
                        // print_arrays($purchases);
                        // print_arrays($dataItems);
                        $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
                        if (!empty($purchase_product_id)) {
                            updateReference('purchase_products');
                            $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                                'active' => 1,
                                'staff_active' => $staffGerenal,
                                'date_active' => $dateGerenal,
                            ]);

                            if (!empty($dataItems)) {
                                foreach ($dataItems as $key => $value) {
                                    $value['purchase_product_id'] = $purchase_product_id;
                                    $purchase_transfer = !empty($value['purchase_transfer']) ? $value['purchase_transfer'] : NULL;
                                    if (!empty($value['purchase_transfer'])) {
                                        unset($value['purchase_transfer']);
                                    }

                                    $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);

                                    //
                                    if (!empty($purchase_transfer)) {
                                        $reference_purchase_tr = getReference('purchase_products');
                                        $purchases_tr = [
                                            'reference_no' => $reference_purchase_tr,
                                            'date' => $purchase_transfer['date'],
                                            'productions_orders_details_id' => $purchase_transfer['productions_orders_details_id'],
                                            'warehouse_id' => $purchase_transfer['warehouse_id'],
                                            'count_items' => $purchase_transfer['count_items'],
                                            'total_quantity' => $purchase_transfer['total_quantity'],
                                            'created_by' => $purchase_transfer['created_by'],
                                            'date_created' => $purchase_transfer['date_created'],
                                            'status' => 'un_approved',
                                            'pois_id' => $purchase_transfer['pois_id'],
                                            'type' => $purchase_transfer['type'],
                                            'sp_type' => $purchase_transfer['sp_type'],
                                            'cqi_id' => $purchase_transfer['cqi_id'],
                                            'final_stage' => $final_stage,
                                            'parent_id' => $purchase_product_id,
                                            'type_business_plan' => $purchase_transfer['type_business_plan'],
                                            'branch_id' => $branch_id,
                                        ];
                                        $purchase_product_id_transfer = $this->stock_model->insertPurchaseProducts($purchases_tr);
                                        if ($purchase_product_id_transfer) {
                                            updateReference('purchase_products');
                                            $purchase_transfer_items = $purchase_transfer['purchase_transfer_items'];

                                            if (!empty($purchase_transfer_items)) {
                                                foreach ($purchase_transfer_items as $kPT => $vPT) {
                                                    $vPT['purchase_product_id'] = $purchase_product_id_transfer;
                                                    // print_arrays($vPT);
                                                    $this->stock_model->insertPurchaseProductItems($vPT);
                                                }
                                            }

                                            if ($final_stage) {
                                                $itemsPurchasesTr = $this->stock_model->getPurchaseProductItems($purchase_product_id_transfer);
                                                foreach ($itemsPurchasesTr as $k => $val) {
                                                    $this->manufactures_model->updateQuantityWarehoused($val['productions_orders_details_id'], $val['quantity'], $plus = 0);
                                                }
                                            }

                                            $content = lang('tnh_his_add_purchase_product');
                                            $content = str_replace('{$1}', $reference_purchase_tr, $content);
                                            insertActivityLog([
                                                'type_parent_obj' => 'purchase_products',
                                                'table_obj' => 'tbl_purchase_products',
                                                'id_obj' => $purchase_product_id_transfer,
                                                'name_obj' => $reference_purchase_tr,
                                                'content' => $content,
                                                'actions' => 'add'
                                            ]);
                                        }
                                    }
                                    //
                                }
                            }

                            if ($final_stage && $object_type_c == 'business_plan') {
                            } else if (!empty($save_and_warehouse)) {
                                $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                                $_data = array(
                                    'status' => 'approved',
                                    'warehouseman_id' => get_staff_user_id(),
                                    'date_warehouseman' => date('Y-m-d H:i:s')
                                );
                                $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                                if ($success) {
                                    log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                                    $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                                    //activity log
                                    if (!empty($purchaseProduct['productions_orders_details_id'])) {
                                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                        $content = lang('tnh_his_warehouse_purchase_product_pod');
                                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                                    } else {
                                        $content = lang('tnh_his_warehouse_purchase_product');
                                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                    }
                                    insertActivityLog([
                                        'type_parent_obj' => 'purchase_products',
                                        'table_obj' => 'tbl_purchase_products',
                                        'id_obj' => $purchase_product_id,
                                        'name_obj' => $purchaseProduct['reference_no'],
                                        'content' => $content,
                                        'actions' => 'warehouse'
                                    ]);
                                    //end activity log
                                }
                            }

                            if ($final_stage) {
                                $itemsPurchases = $this->stock_model->getPurchaseProductItems($purchase_product_id);
                                foreach ($itemsPurchases as $k => $val) {
                                    $this->manufactures_model->updateQuantityWarehoused($val['productions_orders_details_id'], $val['quantity'], $plus = 0);
                                }
                            }

                            $content = lang('tnh_his_add_purchase_product');
                            $content = str_replace('{$1}', $reference_purchase, $content);
                            insertActivityLog([
                                'type_parent_obj' => 'purchase_products',
                                'table_obj' => 'tbl_purchase_products',
                                'id_obj' => $purchase_product_id,
                                'name_obj' => $reference_purchase,
                                'content' => $content,
                                'actions' => 'add'
                            ]);

                            if (!empty($itemsErrors)) {
                                $reference_purchase = getReference('purchase_products');
                                $purchases = [
                                    'reference_no' => $reference_purchase,
                                    'date' => $dateGerenal,
                                    'productions_orders_details_id' => $sp_pod_id,
                                    'warehouse_id' => WAREHOUSES_ERRORS,
                                    'count_items' => count($itemsErrors),
                                    'total_quantity' => $vAI['total_quantity_errors'],
                                    'created_by' => $staffGerenal,
                                    'date_created' => $dateGerenal,
                                    'status' => 'un_approved',
                                    'pois_id' => $sp_pois_id,
                                    'type' => $typePurchase,
                                    'sp_type' => $sp_type,
                                    'cqi_id' => $sp_cqis_id,
                                    'parent_id' => $purchase_product_id,
                                    'is_errors' => 1,
                                    'branch_id' => $branch_id,
                                ];

                                $purchase_product_errors_id = $this->stock_model->insertPurchaseProducts($purchases);
                                if (!empty($purchase_product_errors_id)) {
                                    updateReference('purchase_products');
                                    if (!empty($itemsErrors)) {
                                        foreach ($itemsErrors as $key => $value) {
                                            $value['purchase_product_id'] = $purchase_product_errors_id;
                                            $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                                        }
                                    }

                                    if ($final_stage && $object_type_c == 'business_plan') {
                                    } else if (!empty($save_and_warehouse)) {
                                        $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_errors_id);
                                        $_data = array(
                                            'status' => 'approved',
                                            'warehouseman_id' => get_staff_user_id(),
                                            'date_warehouseman' => date('Y-m-d H:i:s')
                                        );
                                        $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_errors_id));
                                        if ($success) {
                                            log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_errors_id);
                                            $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_errors_id);

                                            //activity log
                                            if (!empty($purchaseProduct['productions_orders_details_id'])) {
                                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                                $content = lang('tnh_his_warehouse_purchase_product_pod');
                                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                                $content = str_replace('{$2}', $pod['reference_no'], $content);
                                            } else {
                                                $content = lang('tnh_his_warehouse_purchase_product');
                                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                            }
                                            insertActivityLog([
                                                'type_parent_obj' => 'purchase_products',
                                                'table_obj' => 'tbl_purchase_products',
                                                'id_obj' => $purchase_product_errors_id,
                                                'name_obj' => $purchaseProduct['reference_no'],
                                                'content' => $content,
                                                'actions' => 'warehouse'
                                            ]);
                                            //end activity log
                                        }
                                    }
                                }
                            }
                        }
                    } else if ($type == 0) {
                        $pois_id = $vAI['pois_id'];
                        $up = $this->manufactures_model->updateProductionsOrderItemsStages($pois_id, [
                            'active' => 1,
                            'staff_active' => $staffGerenal,
                            'date_active' => $dateGerenal,
                        ]);
                    } else if ($type == 3 && !empty($purchases)) {
                        $suggest_exporting = !empty($vAI['suggest_exporting']) ? $vAI['suggest_exporting'] : null;
                        $arrMaterials = !empty($vAI['arrMaterials']) ? $vAI['arrMaterials'] : null;

                        $sp_pod_id = $purchases['productions_orders_details_id'];
                        $sp_pois_id = $purchases['pois_id'];
                        $sp_type = $purchases['sp_type'];
                        $final_stage = $purchases['final_stage'];
                        $save_and_warehouse = 1;

                        //
                        $type_business_plan = 0;
                        if ($final_stage && $object_type_c == 'business_plan') {
                            $isTranferBusinessItem = $this->manufactures_model->isTranferBusinessItem($dtPodC['productions_orders_item_id']);
                            if ($isTranferBusinessItem) {
                                $business_plan_item_id = $isTranferBusinessItem;
                                $dtDataQuantityTrans = $this->manufactures_model->getDataTransferItemAndPurchase($business_plan_item_id, $sp_pod_id);
        
                                $quantity_tranfer_business_item = $dtDataQuantityTrans['quantity_tranfer_business_item'];
                                $quantity_purchase_products = $dtDataQuantityTrans['quantity_purchase_products'];
        
                                if (!empty($dataItems)) {
                                    $quantityNeedBusiness = $quantity_tranfer_business_item - $quantity_purchase_products;
                                    if ($quantityNeedBusiness > 0) {
                                        foreach ($dataItems as $key => $value) {
                                            $quantityE = $value['quantity'];
                                            $tempQuantity = $quantityNeedBusiness;
        
                                            if ($quantityNeedBusiness > 0) {
                                                $quantityNeedBusiness = $quantityNeedBusiness - $quantityE;
                                                if ($quantityNeedBusiness > 0) {
                                                    $quantityP = $quantityE;
                                                } else {
                                                    $quantityP = $tempQuantity;
                                                }
                                            }
        
                                            if ($quantityE <= $quantityP) {
                                                $type_business_plan = 1;
                                            } else {
                                                $dataItems[$key]['quantity'] = $dataItems[$key]['quantity'] - $quantityP;
                                                $dataItems[$key]['quantity_semi_product'] = $dataItems[$key]['quantity'];
        
                                                $quantity_stock = $dataItems[$key]['quantity'];
                                                $_conversion_quantity_unit = $value['conversion_quantity_unit'];
                                                $quantity_unit = roundNumberFormat($quantity_stock/$_conversion_quantity_unit, 4);
                                                $dataItems[$key]['quantity_stock'] = $quantity_stock;
                                                $dataItems[$key]['quantity_unit'] = $quantity_unit;
                                                // $total_quantity_purchases-= $quantityP;
                                                $purchases['total_quantity'] = $purchases['total_quantity'] - $quantityP;
                                                //
                                                $_quantity_semi_product = $quantityP;
                                                $_quantity_stock = $quantityP;
                                                $_quantity_unit = roundNumberFormat($_quantity_stock/$_conversion_quantity_unit, 4);
        
                                                $dataItems[$key]['purchase_transfer'] = [
                                                    'date' => $dateGerenal,
                                                    'productions_orders_details_id' => $purchases['productions_orders_details_id'],
                                                    'warehouse_id' => $purchases['warehouse_id'],
                                                    'count_items' => 1,
                                                    'total_quantity' => $_quantity_stock,
                                                    'created_by' => $staffGerenal,
                                                    'date_created' => $dateGerenal,
                                                    'status' => 'un_approved',
                                                    'pois_id' => $purchases['pois_id'],
                                                    'type' => $purchases['type'],
                                                    'sp_type' => $purchases['sp_type'],
                                                    'final_stage' => $final_stage,
                                                    'cqi_id' => $purchases['cqi_id'],
                                                    'type_business_plan' => 1,
                                                    'purchase_transfer_items' => [
                                                        [
                                                            'productions_orders_details_id' => $value['productions_orders_details_id'],
                                                            'type_item' => $value['type_item'],
                                                            'item_id' => $value['item_id'],
                                                            'location_id' => $value['location_id'],
                                                            'item_code' => $value['item_code'],
                                                            'item_name' => $value['item_name'],
                                                            'quantity' => $_quantity_semi_product,
                                                            'quantity_exchange' => $value['quantity_exchange'],
                                                            'quantity_single' => $value['quantity_single'],
                                                            'quantity_semi_product' => $_quantity_semi_product,
                                                            'type_order' => $value['type_order'],
                                                            'quantity_stock' => $_quantity_stock,
                                                            'quantity_unit' => $_quantity_unit,
                                                            'unit_id' => $value['unit_id'],
                                                            'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                                                        ]
                                                    ]
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $purchases['type_business_plan'] = $type_business_plan;
                        //
                        // print_arrays($dataItems, $purchases);

                        $reference_purchase = getReference('purchase_products');
                        $purchases['reference_no'] = $reference_purchase;
                        $purchase_product_id = $this->stock_model->insertPurchaseProducts($purchases);
                        if (!empty($purchase_product_id)) {
                            updateReference('purchase_products');
                            $up = $this->manufactures_model->updateProductionsOrderItemsStages($sp_pois_id, [
                                'active' => 1,
                                'staff_active' => $staffGerenal,
                                'date_active' => $dateGerenal,
                            ]);

                            $suggest_exporting['purchase_product_id'] = $purchase_product_id;
                            $reference_suggest_exporting = getReference('stock');
                            $suggest_exporting['reference_no'] = $reference_suggest_exporting;
                            $suggest_exporting['reference_stock'] = $reference_suggest_exporting;
                            $suggest_exporting_id = $this->manufactures_model->insertSuggestExporting($suggest_exporting);
                            if ($suggest_exporting_id) {
                                updateReference('stock');
                            }

                            if (!empty($dataItems)) {
                                foreach ($dataItems as $key => $value) {
                                    $value['purchase_product_id'] = $purchase_product_id;
                                    $purchase_transfer = !empty($value['purchase_transfer']) ? $value['purchase_transfer'] : NULL;
                                    if (!empty($value['purchase_transfer'])) {
                                        unset($value['purchase_transfer']);
                                    }

                                    $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                                    if ($purchase_product_item_id) {
                                        if (!empty($arrMaterials)) {
                                            foreach ($arrMaterials as $k => $v) {
                                                $arrMaterials[$k]['suggest_exporting_id'] = $suggest_exporting_id;
                                                $arrMaterials[$k]['purchase_product_items_id'] = $purchase_product_item_id;
                                            }
                                        }

                                        if (!empty($arrMaterials)) {
                                            $this->manufactures_model->insertBatchSuggestExportingItems($arrMaterials);
                                        }
                                    }

                                    //
                                    if (!empty($purchase_transfer)) {
                                        $reference_purchase_tr = getReference('purchase_products');
                                        $purchases_tr = [
                                            'reference_no' => $reference_purchase_tr,
                                            'date' => $purchase_transfer['date'],
                                            'productions_orders_details_id' => $purchase_transfer['productions_orders_details_id'],
                                            'warehouse_id' => $purchase_transfer['warehouse_id'],
                                            'count_items' => $purchase_transfer['count_items'],
                                            'total_quantity' => $purchase_transfer['total_quantity'],
                                            'created_by' => $purchase_transfer['created_by'],
                                            'date_created' => $purchase_transfer['date_created'],
                                            'status' => 'un_approved',
                                            'pois_id' => $purchase_transfer['pois_id'],
                                            'type' => $purchase_transfer['type'],
                                            'sp_type' => $purchase_transfer['sp_type'],
                                            'cqi_id' => $purchase_transfer['cqi_id'],
                                            'final_stage' => $final_stage,
                                            'parent_id' => $purchase_product_id,
                                            'type_business_plan' => $purchase_transfer['type_business_plan'],
                                            'branch_id' => $branch_id,
                                        ];
                                        $purchase_product_id_transfer = $this->stock_model->insertPurchaseProducts($purchases_tr);
                                        if ($purchase_product_id_transfer) {
                                            updateReference('purchase_products');
                                            $purchase_transfer_items = $purchase_transfer['purchase_transfer_items'];

                                            if (!empty($purchase_transfer_items)) {
                                                foreach ($purchase_transfer_items as $kPT => $vPT) {
                                                    $vPT['purchase_product_id'] = $purchase_product_id_transfer;
                                                    // print_arrays($vPT);
                                                    $this->stock_model->insertPurchaseProductItems($vPT);
                                                }
                                            }

                                            if ($final_stage) {
                                                $itemsPurchasesTr = $this->stock_model->getPurchaseProductItems($purchase_product_id_transfer);
                                                foreach ($itemsPurchasesTr as $k => $val) {
                                                    $this->manufactures_model->updateQuantityWarehoused($val['productions_orders_details_id'], $val['quantity'], $plus = 0);
                                                }
                                            }

                                            $content = lang('tnh_his_add_purchase_product');
                                            $content = str_replace('{$1}', $reference_purchase_tr, $content);
                                            insertActivityLog([
                                                'type_parent_obj' => 'purchase_products',
                                                'table_obj' => 'tbl_purchase_products',
                                                'id_obj' => $purchase_product_id_transfer,
                                                'name_obj' => $reference_purchase_tr,
                                                'content' => $content,
                                                'actions' => 'add'
                                            ]);
                                        }
                                    }
                                    //
                                }
                            }

                            //suggest exporting
                            if (!empty($save_and_warehouse) && $suggest_exporting_id) {
                                $id = $suggest_exporting_id;
                                $_data = array(
                                    'warehouseman_id' => get_staff_user_id(),
                                    'date_warehouseman' => date('Y-m-d H:i:s')
                                );

                                if (!test_quantity_exporting_producion_warehouses($suggest_exporting_id)) {
                                } else {
                                    $success = $this->db->update('tbl_suggest_exporting', $_data, array('id' => $suggest_exporting_id));
                                    if ($success) {
                                        log_activity('Export Warehouses items approved [ID export_warehouses: ' . $suggest_exporting_id);
                                        $this->stock_model->decreaseWarehouse($suggest_exporting_id);

                                        $suggest_exporting = $this->manufactures_model->rowSuggestExporting($suggest_exporting_id);
                                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($suggest_exporting['productions_orders_details_id']);
                                        $content = lang('tnh_his_warehouse_exporting_producion');
                                        $content = str_replace('{$1}', $suggest_exporting['reference_stock'], $content);
                                        $content = str_replace('{$2}', $pod['reference_no'], $content);

                                        insertActivityLog([
                                            'type_parent_obj' => 'exporting_producion',
                                            'table_obj' => 'tbl_suggest_exporting',
                                            'id_obj' => $id,
                                            'name_obj' => $suggest_exporting['reference_stock'],
                                            'content' => $content,
                                            'actions' => 'warehouse'
                                        ]);
                                    }
                                }
                            }

                            if ($final_stage) {
                                $itemsPurchases = $this->stock_model->getPurchaseProductItems($purchase_product_id);
                                foreach ($itemsPurchases as $k => $val) {
                                    $this->manufactures_model->updateQuantityWarehoused($val['productions_orders_details_id'], $val['quantity'], $plus = 0);
                                }
                            }

                            if ($suggest_exporting_id) {
                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($sp_pod_id);
                                $content = lang('tnh_his_add_exporting_producion');
                                $content = str_replace('{$1}', $reference_suggest_exporting, $content);
                                $content = str_replace('{$2}', $pod['reference_no'], $content);

                                insertActivityLog([
                                    'type_parent_obj' => 'exporting_producion',
                                    'table_obj' => 'tbl_suggest_exporting',
                                    'id_obj' => $suggest_exporting_id,
                                    'name_obj' => $reference_suggest_exporting,
                                    'content' => $content,
                                    'actions' => 'add'
                                ]);
                            }

                            if ($final_stage && $object_type_c == 'business_plan') {
                            } else if (!empty($save_and_warehouse)) {
                                $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_id);
                                $_data = array(
                                    'status' => 'approved',
                                    'warehouseman_id' => get_staff_user_id(),
                                    'date_warehouseman' => date('Y-m-d H:i:s')
                                );
                                $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_id));
                                if ($success) {
                                    log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_id);
                                    $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_id);

                                    //activity log
                                    if (!empty($purchaseProduct['productions_orders_details_id'])) {
                                        $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                        $content = lang('tnh_his_warehouse_purchase_product_pod');
                                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                        $content = str_replace('{$2}', $pod['reference_no'], $content);
                                    } else {
                                        $content = lang('tnh_his_warehouse_purchase_product');
                                        $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                    }
                                    insertActivityLog([
                                        'type_parent_obj' => 'purchase_products',
                                        'table_obj' => 'tbl_purchase_products',
                                        'id_obj' => $purchase_product_id,
                                        'name_obj' => $purchaseProduct['reference_no'],
                                        'content' => $content,
                                        'actions' => 'warehouse'
                                    ]);
                                    //end activity log
                                }
                            }

                            $content = lang('tnh_his_add_purchase_product');
                            $content = str_replace('{$1}', $reference_purchase, $content);
                            insertActivityLog([
                                'type_parent_obj' => 'purchase_products',
                                'table_obj' => 'tbl_purchase_products',
                                'id_obj' => $purchase_product_id,
                                'name_obj' => $reference_purchase,
                                'content' => $content,
                                'actions' => 'add'
                            ]);

                            if (!empty($itemsErrors)) {
                                $reference_purchase = getReference('purchase_products');
                                $purchases = [
                                    'reference_no' => $reference_purchase,
                                    'date' => $dateGerenal,
                                    'productions_orders_details_id' => $sp_pod_id,
                                    'warehouse_id' => WAREHOUSES_ERRORS,
                                    'count_items' => count($itemsErrors),
                                    'total_quantity' => $vAI['total_quantity_errors'],
                                    'created_by' => $staffGerenal,
                                    'date_created' => $dateGerenal,
                                    'status' => 'un_approved',
                                    'pois_id' => $sp_pois_id,
                                    'type' => $typePurchase,
                                    'sp_type' => $sp_type,
                                    'cqi_id' => $sp_cqis_id,
                                    'parent_id' => $purchase_product_id,
                                    'is_errors' => 1,
                                    'branch_id' => $branch_id,
                                ];

                                $purchase_product_errors_id = $this->stock_model->insertPurchaseProducts($purchases);
                                if (!empty($purchase_product_errors_id)) {
                                    updateReference('purchase_products');
                                    if (!empty($itemsErrors)) {
                                        foreach ($itemsErrors as $key => $value) {
                                            $value['purchase_product_id'] = $purchase_product_errors_id;
                                            $purchase_product_item_id = $this->stock_model->insertPurchaseProductItems($value);
                                        }
                                    }

                                    if ($final_stage && $object_type_c == 'business_plan') {
                                    } else if (!empty($save_and_warehouse)) {
                                        $purchaseProduct = $this->stock_model->rowPurchaseProducts($purchase_product_errors_id);
                                        $_data = array(
                                            'status' => 'approved',
                                            'warehouseman_id' => get_staff_user_id(),
                                            'date_warehouseman' => date('Y-m-d H:i:s')
                                        );
                                        $success = $this->db->update('tbl_purchase_products', $_data, array('id' => $purchase_product_errors_id));
                                        if ($success) {
                                            log_activity('Import Warehouses items approved [ID export_warehouses: ' . $purchase_product_errors_id);
                                            $this->stock_model->decreaseWarehouse_purchase_products($purchase_product_errors_id);

                                            //activity log
                                            if (!empty($purchaseProduct['productions_orders_details_id'])) {
                                                $pod = $this->manufactures_model->rowProductionsOrdersDetais($purchaseProduct['productions_orders_details_id']);
                                                $content = lang('tnh_his_warehouse_purchase_product_pod');
                                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                                $content = str_replace('{$2}', $pod['reference_no'], $content);
                                            } else {
                                                $content = lang('tnh_his_warehouse_purchase_product');
                                                $content = str_replace('{$1}', $purchaseProduct['reference_no'], $content);
                                            }
                                            insertActivityLog([
                                                'type_parent_obj' => 'purchase_products',
                                                'table_obj' => 'tbl_purchase_products',
                                                'id_obj' => $purchase_product_errors_id,
                                                'name_obj' => $purchaseProduct['reference_no'],
                                                'content' => $content,
                                                'actions' => 'warehouse'
                                            ]);
                                            //end activity log
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }






            $data['result'] = 1;
            $data['message'] = lang('success');
            echo json_encode($data);
            die;
        }

        $data['warehouses'] = $this->stock_model->getWarehouses(false, [WAREHOUSES_CAPACITY]);
        $data['po_id'] = $po_id;
        $data['productions_orders'] = $productions_orders;
        $data['productions_plan'] = $productions_plan;
        $data['title'] = lang('tnh_finished_stages');
        $this->load->view('admin/manufactures/finished_stages', $data);
    }

    public function activeStages()
    {
        $data = [];
        $this->load->view('admin/manufactures/active_stages_po', $data);
    }

    public function view_sugg($id) {
        $data['id'] = $id;
        $productions_plan = $this->manufactures_model->rowProductionsPlan($id);
        $this->db->select('productions_orders_id');
        $this->db->from('tbl_productions_orders_items');
        $this->db->where('tbl_productions_orders_items.plan_id',$id);
        $this->db->limit(1);
        $dtProductionOrder = $this->db->get()->row_array();
        $productions_orders_id = !empty($dtProductionOrder) ? $dtProductionOrder['productions_orders_id'] : 0;

        $data['productions_orders_id'] = $productions_orders_id;
        $data['productions_plan'] = $productions_plan;
        $this->load->view('admin/manufactures/view_sugg', $data);
    }

    public function getViewSugg()
    {
        $productions_orders_id_s = $this->db->escape($this->input->post('productions_orders_id_s'));

        $aColumns = [
            'tbl_suggest_exporting.id as id',
            'tbl_suggest_exporting.date as date',
            'tbl_suggest_exporting.reference_stock as reference_stock',
            'IF(tbl_suggest_exporting_items.type_item = "materials", tbl_materials.code, tbl_products.code) as item_code',
            'IF(tbl_suggest_exporting_items.type_item = "materials", tbl_materials.name, tbl_products.name) as item_name',
            'tbl_suggest_exporting_items.quantity_export as quantity',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_suggest_exporting';
        $where        = [
            'AND tbl_suggest_exporting.po_id = '.$productions_orders_id_s.' AND tbl_suggest_exporting.po_id != 0 AND tbl_suggest_exporting.type = 2' 
        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_suggest_exporting_items ON tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id',
            'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "materials"',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item IN ("products", "semi_products", "semi_products_outside")',
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $totalQuantityPrimary = 0;
        $totalQuantityNeed = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $suggest_exporting_id = $aRow['id'];

            $row[0] = '<div class="text-center"></div>';
            $row[1] = _d($aRow['date']);
            $row[2] = $aRow['reference_stock'];
            $row[3] = $aRow['item_code'];
            $row[4] = $aRow['item_name'];
            $row[5] = '<div class="text-center">'.formatNumber($aRow['quantity']).'</div>';
           
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function keep_stock_products($id){
        if ($this->input->post()) {
            $data = [];
            $productions_plan = $this->manufactures_model->rowProductionsPlan($id);
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true)
            {
                $dataPost = $this->input->post();
                $date = to_sql_date($dataPost['date']);
                $note = $this->input->post('note', false);
                $branch_id = $dataPost['branch_id'];
                if (empty($branch_id)) {
                    $data['result'] = 0;
                    $data['message'] = 'Vui lòng chọn chi nhánh';
                    echo json_encode($data);
                    die;
                }

                $arrItemId = [];
                if (!empty($dataPost['tick'])) {
                    $counter = $this->input->post('counter');
                    $errors = '';
                    $totalWarehouse = 0;
                    $total = 0;
                    $tranferItems = [];
                    $arr_id = [];
                    $arr_info = [];
                    foreach ($counter as $key => $value) {
                        $order_item_id = $this->input->post('order_item_id')[$value];
                        $item_id = $this->input->post('item_id')[$value];
                        // $plan_id_item  = $this->input->post('plan_id_item')[$value];
                        $plan_id_item  = 0;
                        // $plan_id  = $this->input->post('plan_id')[$value];
                        $plan_id  = 0;
                        $order_id  = $this->input->post('order_id')[$value];
                        $quantity  = $this->input->post('quantity')[$value];
                        if (empty($order_item_id) || empty($item_id)) {
                            continue;
                        }
                        $order_item = get_table_where(
                            'tbl_order_items',
                            ['id' => $order_item_id],
                            '',
                            'row_array'
                        );
                        if (empty($order_item)) {
                            continue;
                        }

                        $this->db->select('SUM(tbl_tranfer_business_item.quantity) as quantity_hold');
                        $this->db->from('tbl_tranfer_business_item');
                        // $this->db->where('tbl_tranfer_business_item.plan_id_item', $plan_id_item);
                        $this->db->where('tbl_tranfer_business_item.order_item_id', $order_item_id);
                        $dtTranferItemW = $this->db->get()->row_array();

                        $quantity_hold = 0;
                        if (!empty($dtTranferItemW)) {
                            $quantity_hold = $dtTranferItemW['quantity_hold'];
                        }
                        $quantityNeedHold = $quantity  - $quantity_hold;
                        if ($quantityNeedHold < 0) {
                            continue;
                        }

                        $this->db->select('tbl_orders.reference_no,tbl_order_items.item_name');
                        $this->db->from('tbl_order_items');
                        $this->db->join('tbl_orders','tbl_orders.id = tbl_order_items.order_id');
                        $this->db->where('tbl_order_items.id',$order_item_id);
                        $dtOrderItem = $this->db->get()->row_array();

                        $tick = !empty($this->input->post('tick')[$value]) ? $this->input->post('tick')[$value] : null;

                        if (!empty($tick)) {
                            $totalQuantityCorrdinator = 0;
                            foreach ($tick as $k => $val) {
                                $warehousesLocation = explode('__', $val);
                                $id_business_plan = $warehousesLocation[0];
                                $business_plan_item_id = $warehousesLocation[1];
                                $quantityCoordinator = number_unformat($this->input->post('quantity_coordinator')[$value][$k]);

                                $this->db->select("
                                    tbl_business_plan_items.items_name as items_name,
                                    tbl_business_plan.reference_no as reference_no,
                                    tbl_business_plan_items.quantity as quantity");
                                $this->db->from('tbl_business_plan_items');
                                $this->db->join(
                                    'tbl_business_plan',
                                    'tbl_business_plan.id = tbl_business_plan_items.business_plan_id','inner'
                                );
                                $this->db->where('tbl_business_plan_items.id',$business_plan_item_id);
                                $this->db->where('tbl_business_plan.id',$id_business_plan);
                                $dtBusiness = $this->db->get()->row_array();
                                if (empty($dtBusiness)){
                                    $data['result'] = 0;
                                    $data['message'] = lang('Không tồn tại KHKD');
                                    echo json_encode($data); die;
                                }

                                $this->db->select('
                                    SUM(tbl_tranfer_business_item.quantity) as product_quantity
                                ', false);
                                $this->db->from('tbl_tranfer_business_item');
                                $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $business_plan_item_id);
                                $this->db->where('tbl_tranfer_business_item.id_business_plan', $id_business_plan);
                                $dtWarehouse = $this->db->get()->row_array();


                                $quantityWarehouse = $dtWarehouse['product_quantity'];

                                $tb_purchase_product = "(
                                    SELECT 
                                        tbl_productions_orders_items.production_plan_item_id as business_plan_item_id,
                                        SUM(tbl_purchase_product_items.quantity) as quantity
                                    FROM tbl_purchase_product_items
                                    JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id
                                    JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_product_items.productions_orders_details_id
                                    JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'
                                    WHERE tbl_productions_orders_items.production_plan_item_id != 0 AND tbl_purchase_products.type_business_plan = 0
                                    AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1)
                                    AND tbl_productions_orders_items.production_plan_item_id = $business_plan_item_id
                                )";

                                $dtPurchaseProduct = $this->db->query($tb_purchase_product)->row_array();


                                if (empty($quantityCoordinator)) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Số lượng giữ phải lớn hơn 0');
                                    echo json_encode($data); die;
                                }

                                if ($quantityCoordinator > ($dtBusiness['quantity'] - $quantityWarehouse - (float)$dtPurchaseProduct['quantity'])) {
                                    $errors .= '<div>KHKD [' . $dtBusiness['reference_no'] . '] mặt hàng [' . $dtBusiness['items_name'] . ']] số lượng  giữ kho nhỏ hơn [' . ($dtBusiness['quantity'] - $quantityWarehouse - (float)$dtPurchaseProduct['quantity']). ']</div>';
                                    continue;
                                }


                                $str_item_id = $item_id . '__' . $id_business_plan . '__' . $business_plan_item_id;
                                if (!empty($arr_info[$str_item_id])) {
                                    $arr_info[$str_item_id]['quantity'] = $arr_info[$str_item_id]['quantity'] + $quantityCoordinator;
                                } else {
                                    $arr_info[$str_item_id] = [
                                        'item_id' => $item_id,
                                        'id_business_plan' => $id_business_plan,
                                        'business_plan_item_id' => $business_plan_item_id,
                                        'item_name' => $dtOrderItem['item_name'],
                                        'quantity' => $quantityCoordinator,
                                    ];
                                }

                                $tranferItems[] = array(
                                    'item_id' => $item_id,
                                    'id_business_plan' => $id_business_plan,
                                    'business_plan_item_id' => $business_plan_item_id,
                                    'order_item_id' => $order_item_id,
                                    'plan_id_item' => $plan_id_item,
                                    'quantity' => $quantityCoordinator,
                                    'plan_id' => $plan_id,
                                    'order_id' => $order_id,
                                );


                                $totalQuantityCorrdinator += $quantityCoordinator;
                            }

                            //Kiểm tra số lượng cần giữ hàng
                            if ($totalQuantityCorrdinator > $quantityNeedHold) {
                                $errors .= '<div>' . lang('Đơn hàng') . ' ' . $dtOrderItem['reference_no'] . ' mặt hàng '.$dtOrderItem['item_name'].' số lượng giữ hàng phải <= ' . formatNumber($quantityNeedHold) . '</div>';
                                continue;
                            }
                        }

                        $arrItemId[] = $item_id;
                    }


                    foreach ($arr_info as $key => $value) {
                        $this->db->select("
                                    tbl_business_plan_items.items_name as items_name,
                                    tbl_business_plan.reference_no as reference_no,
                                    tbl_business_plan_items.quantity as quantity");
                        $this->db->from('tbl_business_plan_items');
                        $this->db->join(
                            'tbl_business_plan',
                            'tbl_business_plan.id = tbl_business_plan_items.business_plan_id','inner'
                        );
                        $this->db->where('tbl_business_plan_items.id',$value['business_plan_item_id']);
                        $this->db->where('tbl_business_plan.id',$value['id_business_plan']);
                        $dtBusiness = $this->db->get()->row_array();
                        if (empty($dtBusiness)){
                            $data['result'] = 0;
                            $data['message'] = lang('Không tồn tại KHKD');
                            echo json_encode($data); die;
                        }

                        $this->db->select('
                                    SUM(tbl_tranfer_business_item.quantity) as product_quantity
                                ', false);
                        $this->db->from('tbl_tranfer_business_item');
                        $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $value['business_plan_item_id']);
                        $this->db->where('tbl_tranfer_business_item.id_business_plan', $value['id_business_plan']);
                        $dtWarehouse = $this->db->get()->row_array();

                        $quantity_warehouse = $dtBusiness['quantity'] - $dtWarehouse['product_quantity'];
                        $quantity = $value['quantity'];
                        if ($quantity > $quantity_warehouse) {
                            $errors .= '<div>' . lang('Đơn hàng') . ' ' . $dtOrderItem['reference_no'] . ' mặt hàng '.$dtOrderItem['item_name'].' không đủ số lượng </div>';;
                            continue;
                        }
                    }

                    if (!empty($errors)) {
                        $data['result'] = 0;
                        $data['message'] = $errors;
                        echo json_encode($data);
                        die;
                    }

                    if (empty($tranferItems)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có mặt hàng để thêm');
                        echo json_encode($data);
                        die;
                    }

                    $reference_no = getReference('tranfer_tp');
                    $statusTransfer = 1;
                    $staffIdTransfer = get_staff_user_id();
                    $dateTransfer = date('Y-m-d H:i:s');


                    $transfer = array(
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'note' => $note,
                        'status' => $statusTransfer,
                        'staff_status' => $staffIdTransfer,
                        'date_status' => $dateTransfer,
                        'created_by' => $staffIdTransfer,
                        'date_created' => $dateTransfer,
                        // 'id_plan' => $id
                        'id_plan' => 0,
                        'branch_id' => $branch_id
                    );
                    // print_arrays($tranferItems);

                    $this->db->insert('tbl_tranfer_business', $transfer);
                    $transfer_id = $this->db->insert_id();
                    if ($transfer_id) {
                        updateReference('tranfer_tp');
                        foreach ($tranferItems as $k => $v) {
                            $v['tranfer_business_id'] = $transfer_id;
                            $this->db->insert('tbl_tranfer_business_item', $v);
                            $ins = $this->db->insert_id();
                        }

                        // insertActivityLog([
                        //     'type_parent_obj' => 'productions_plan',
                        //     'table_obj' => 'tbl_productions_plan',
                        //     'id_obj' => $id,
                        //     'name_obj' => $productions_plan['reference_no'],
                        //     'content' => lang('Giữ kho ') . ' [' . $productions_plan['reference_no'] . ']',
                        //     'actions' => 'keep_stock_products',
                        // ]);

                        if (!empty($arrItemId)) {
                            totalTransferBusinessItem($arrItemId);
                        }

                        $data['result'] = 1;
                        $data['message'] = lang('success');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thất bại !');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn dòng giữ kho ?');
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data); die;
        } else {
            $data['id'] = $id;
            $this->load->view('admin/manufactures/keep_stock_products', $data);
        }
    }

    public function keep_stock_products_old($id){
        if ($this->input->post()) {
            $data = [];
            $productions_plan = $this->manufactures_model->rowProductionsPlan($id);
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true)
            {
                $dataPost = $this->input->post();
                $date = to_sql_date($dataPost['date']);
                $note = $this->input->post('note', false);
                $branch_id = $dataPost['branch_id'];
                if (empty($branch_id)) {
                    $data['result'] = 0;
                    $data['message'] = 'Vui lòng chọn chi nhánh';
                    echo json_encode($data);
                    die;
                }

                if (!empty($dataPost['tick'])) {
                    $counter = $this->input->post('counter');
                    $errors = '';
                    $totalWarehouse = 0;
                    $total = 0;
                    $tranferItems = [];
                    $arr_id = [];
                    $arr_info = [];
                    foreach ($counter as $key => $value) {
                        $order_item_id = $this->input->post('order_item_id')[$value];
                        $item_id = $this->input->post('item_id')[$value];
                        // $plan_id_item  = $this->input->post('plan_id_item')[$value];
                        $plan_id_item  = 0;
                        // $plan_id  = $this->input->post('plan_id')[$value];
                        $plan_id  = 0;
                        $order_id  = $this->input->post('order_id')[$value];
                        $quantity  = $this->input->post('quantity')[$value];
                        if (empty($order_item_id) || empty($item_id)) {
                            continue;
                        }
                        $order_item = get_table_where(
                            'tbl_order_items',
                            ['id' => $order_item_id],
                            '',
                            'row_array'
                        );
                        if (empty($order_item)) {
                            continue;
                        }

                        $this->db->select('SUM(tbl_tranfer_business_item.quantity) as quantity_hold');
                        $this->db->from('tbl_tranfer_business_item');
                        // $this->db->where('tbl_tranfer_business_item.plan_id_item', $plan_id_item);
                        $this->db->where('tbl_tranfer_business_item.order_item_id', $order_item_id);
                        $dtTranferItemW = $this->db->get()->row_array();

                        $quantity_hold = 0;
                        if (!empty($dtTranferItemW)) {
                            $quantity_hold = $dtTranferItemW['quantity_hold'];
                        }
                        $quantityNeedHold = $quantity  - $quantity_hold;
                        if ($quantityNeedHold < 0) {
                            continue;
                        }

                        $this->db->select('tbl_orders.reference_no,tbl_order_items.item_name');
                        $this->db->from('tbl_order_items');
                        $this->db->join('tbl_orders','tbl_orders.id = tbl_order_items.order_id');
                        $this->db->where('tbl_order_items.id',$order_item_id);
                        $dtOrderItem = $this->db->get()->row_array();

                        $tick = !empty($this->input->post('tick')[$value]) ? $this->input->post('tick')[$value] : null;

                        if (!empty($tick)) {
                            $totalQuantityCorrdinator = 0;
                            foreach ($tick as $k => $val) {
                                $warehousesLocation = explode('__', $val);
                                $id_business_plan = $warehousesLocation[0];
                                $business_plan_item_id = $warehousesLocation[1];
                                $quantityCoordinator = number_unformat($this->input->post('quantity_coordinator')[$value][$k]);

                                $this->db->select("
                                    tbl_business_plan_items.items_name as items_name,
                                    tbl_business_plan.reference_no as reference_no,
                                    tbl_business_plan_items.quantity as quantity");
                                $this->db->from('tbl_business_plan_items');
                                $this->db->join(
                                    'tbl_business_plan',
                                    'tbl_business_plan.id = tbl_business_plan_items.business_plan_id','inner'
                                );
                                $this->db->where('tbl_business_plan_items.id',$business_plan_item_id);
                                $this->db->where('tbl_business_plan.id',$id_business_plan);
                                $dtBusiness = $this->db->get()->row_array();
                                if (empty($dtBusiness)){
                                    $data['result'] = 0;
                                    $data['message'] = lang('Không tồn tại KHKD');
                                    echo json_encode($data); die;
                                }

                                $this->db->select('
                                    SUM(tbl_tranfer_business_item.quantity) as product_quantity
                                ', false);
                                $this->db->from('tbl_tranfer_business_item');
                                $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $business_plan_item_id);
                                $this->db->where('tbl_tranfer_business_item.id_business_plan', $id_business_plan);
                                $dtWarehouse = $this->db->get()->row_array();


                                $quantityWarehouse = $dtWarehouse['product_quantity'];

                                if (empty($quantityCoordinator)) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Số lượng giữ phải lớn hơn 0');
                                    echo json_encode($data); die;
                                }

                                if ($quantityCoordinator > ($dtBusiness['quantity'] - $quantityWarehouse)) {
                                    $errors .= '<div>KHKD [' . $dtBusiness['reference_no'] . '] mặt hàng [' . $dtBusiness['items_name'] . ']] số lượng  giữ kho nhỏ hơn [' . ($dtBusiness['quantity'] - $quantityWarehouse) . ']</div>';
                                    continue;
                                }


                                $str_item_id = $item_id . '__' . $id_business_plan . '__' . $business_plan_item_id;
                                if (!empty($arr_info[$str_item_id])) {
                                    $arr_info[$str_item_id]['quantity'] = $arr_info[$str_item_id]['quantity'] + $quantityCoordinator;
                                } else {
                                    $arr_info[$str_item_id] = [
                                        'item_id' => $item_id,
                                        'id_business_plan' => $id_business_plan,
                                        'business_plan_item_id' => $business_plan_item_id,
                                        'item_name' => $dtOrderItem['item_name'],
                                        'quantity' => $quantityCoordinator,
                                    ];
                                }

                                $tranferItems[] = array(
                                    'item_id' => $item_id,
                                    'id_business_plan' => $id_business_plan,
                                    'business_plan_item_id' => $business_plan_item_id,
                                    'order_item_id' => $order_item_id,
                                    'plan_id_item' => $plan_id_item,
                                    'quantity' => $quantityCoordinator,
                                    'plan_id' => $plan_id,
                                    'order_id' => $order_id,
                                );


                                $totalQuantityCorrdinator += $quantityCoordinator;
                            }

                            //Kiểm tra số lượng cần giữ hàng
                            if ($totalQuantityCorrdinator > $quantityNeedHold) {
                                $errors .= '<div>' . lang('Đơn hàng') . ' ' . $dtOrderItem['reference_no'] . ' mặt hàng '.$dtOrderItem['item_name'].' số lượng giữ hàng phải <= ' . formatNumber($quantityNeedHold) . '</div>';
                                continue;
                            }
                        }
                    }

                    if (empty($tranferItems)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có mặt hàng để thêm');
                        echo json_encode($data);
                        die;
                    }

                    foreach ($arr_info as $key => $value) {
                        $this->db->select("
                                    tbl_business_plan_items.items_name as items_name,
                                    tbl_business_plan.reference_no as reference_no,
                                    tbl_business_plan_items.quantity as quantity");
                        $this->db->from('tbl_business_plan_items');
                        $this->db->join(
                            'tbl_business_plan',
                            'tbl_business_plan.id = tbl_business_plan_items.business_plan_id','inner'
                        );
                        $this->db->where('tbl_business_plan_items.id',$value['business_plan_item_id']);
                        $this->db->where('tbl_business_plan.id',$value['id_business_plan']);
                        $dtBusiness = $this->db->get()->row_array();
                        if (empty($dtBusiness)){
                            $data['result'] = 0;
                            $data['message'] = lang('Không tồn tại KHKD');
                            echo json_encode($data); die;
                        }

                        $this->db->select('
                                    SUM(tbl_tranfer_business_item.quantity) as product_quantity
                                ', false);
                        $this->db->from('tbl_tranfer_business_item');
                        $this->db->where('tbl_tranfer_business_item.business_plan_item_id', $value['business_plan_item_id']);
                        $this->db->where('tbl_tranfer_business_item.id_business_plan', $value['id_business_plan']);
                        $dtWarehouse = $this->db->get()->row_array();

                        $quantity_warehouse = $dtBusiness['quantity'] - $dtWarehouse['product_quantity'];
                        $quantity = $value['quantity'];
                        if ($quantity > $quantity_warehouse) {
                            $errors .= '<div>' . lang('Đơn hàng') . ' ' . $dtOrderItem['reference_no'] . ' mặt hàng '.$dtOrderItem['item_name'].' không đủ số lượng </div>';;
                            continue;
                        }
                    }

                    if (!empty($errors)) {
                        $data['result'] = 0;
                        $data['message'] = $errors;
                        echo json_encode($data);
                        die;
                    }


                    $reference_no = getReference('tranfer_tp');
                    $statusTransfer = 1;
                    $staffIdTransfer = get_staff_user_id();
                    $dateTransfer = date('Y-m-d H:i:s');


                    $transfer = array(
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'note' => $note,
                        'status' => $statusTransfer,
                        'staff_status' => $staffIdTransfer,
                        'date_status' => $dateTransfer,
                        'created_by' => $staffIdTransfer,
                        'date_created' => $dateTransfer,
                        // 'id_plan' => $id
                        'id_plan' => 0,
                        'branch_id' => $branch_id
                    );
                    // print_arrays($tranferItems);

                    $this->db->insert('tbl_tranfer_business', $transfer);
                    $transfer_id = $this->db->insert_id();
                    if ($transfer_id) {
                        updateReference('tranfer_tp');
                        foreach ($tranferItems as $k => $v) {
                            $v['tranfer_business_id'] = $transfer_id;
                            $this->db->insert('tbl_tranfer_business_item', $v);
                            $ins = $this->db->insert_id();
                        }

                        // insertActivityLog([
                        //     'type_parent_obj' => 'productions_plan',
                        //     'table_obj' => 'tbl_productions_plan',
                        //     'id_obj' => $id,
                        //     'name_obj' => $productions_plan['reference_no'],
                        //     'content' => lang('Giữ kho ') . ' [' . $productions_plan['reference_no'] . ']',
                        //     'actions' => 'keep_stock_products',
                        // ]);

                        $data['result'] = 1;
                        $data['message'] = lang('success');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thất bại !');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('Vui lòng chọn dòng giữ kho ?');
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data); die;
        } else {
            $data['id'] = $id;
            $this->load->view('admin/manufactures/keep_stock_products', $data);
        }
    }

    public function loadAllKeepStockProducts(){
        $productions_plan_id = $this->input->post('productions_plan_id');
        $item_id = $this->input->post('item_id');

        $data = [];
        $tbTranfer = "(
            SELECT 
                tbl_tranfer_business_item.plan_id_item as plan_id_item,
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            WHERE tbl_tranfer_business_item.plan_id_item != 0
            GROUP BY tbl_tranfer_business_item.plan_id_item
        ) tb_tranfer";

        $tbTranfer = "(
            SELECT 
                tbl_tranfer_business_item.order_item_id as order_item_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            WHERE tbl_tranfer_business_item.order_item_id != 0
            GROUP BY tbl_tranfer_business_item.order_item_id
        ) tb_tranfer";

        $tbTranferNew = "(
            SELECT 
                tbl_tranfer_business_item.business_plan_item_id as business_plan_item_id,
                SUM(tbl_tranfer_business_item.quantity) as quantity
            FROM tbl_tranfer_business_item
            WHERE tbl_tranfer_business_item.business_plan_item_id != 0
            GROUP BY tbl_tranfer_business_item.business_plan_item_id
        ) tb_tranfer_new";

        $tb_purchase_product = "(
            SELECT 
                tbl_productions_orders_items.production_plan_item_id as business_plan_item_id,
                SUM(tbl_purchase_product_items.quantity) as quantity
            FROM tbl_purchase_product_items
            JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_product_items.productions_orders_details_id
            JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id AND tbl_productions_orders_items.object_item_type = 'business_plan'
            WHERE tbl_productions_orders_items.production_plan_item_id != 0 AND tbl_purchase_products.type_business_plan = 0
            AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1)
            GROUP BY tbl_productions_orders_items.production_plan_item_id
        ) tb_purchase_product";

        // $this->db->select('
        //     tbl_productions_plan_items.productions_plan_id as plan_id,
        //     tbl_productions_plan_items.id as plan_id_item,
        //     tbl_orders.id as order_id,
        //     tbl_order_items.id as order_item_id,
        //     tbl_orders.reference_no as reference_no,
        //     tbl_products.code as code,
        //     tbl_products.name as name,
        //     tbl_productions_plan_items.product_id as item_id,
        //     tbl_productions_plan_items.quantity_total_details as quantity,
        //     COALESCE(tb_tranfer.quantity,0) as quantity_hold,
        // ');
        // $this->db->from('tbl_productions_plan_items');
        // $this->db->join('tbl_orders','tbl_orders.id = tbl_productions_plan_items.object_id');
        // $this->db->join('tbl_order_items','tbl_order_items.id = tbl_productions_plan_items.item_object_id');
        // $this->db->join('tbl_products','tbl_products.id = tbl_productions_plan_items.product_id');
        // $this->db->join($tbTranfer,'tb_tranfer.plan_id_item = tbl_productions_plan_items.id','left');
        // $this->db->where('tbl_productions_plan_items.productions_plan_id',$productions_plan_id);
        // $this->db->where('tbl_productions_plan_items.type_object','orders');
        // $this->db->where('tbl_productions_plan_items.quantity_total_details - COALESCE(tb_tranfer.quantity,0) > 0');
        // $dtOrder = $this->db->get()->result_array();

        $slKeep = "COALESCE((
            SELECT SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse_detail
            WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id AND tbltransfer_warehouse_detail.tranfer_business_item_id = 0
        ), 0)";

        $this->db->select('
            tbl_orders.id as order_id,
            tbl_order_items.id as order_item_id,
            tbl_orders.reference_no as reference_no,
            tbl_products.code as code,
            tbl_products.name as name,
            tbl_products.id as item_id,
            ((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - '.$slKeep.') as quantity,
            tb_tranfer.quantity as quantity_hold,
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
        $this->db->join($tbTranfer,'tb_tranfer.order_item_id = tbl_order_items.id','left');
        $this->db->where('tbl_products.id', $item_id);
        $this->db->where('(
            (((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - '.$slKeep.') - COALESCE(tb_tranfer.quantity, 0)) > 0
        )', false, false);
        $this->db->where('tbl_orders.status', 'approved');
        $this->db->where('tbl_orders.is_cancel', 0);
        $dtOrder = $this->db->get()->result_array();

        if (!empty($dtOrder)){
            foreach ($dtOrder as $key => $value){
                $item_id = $value['item_id'];
                $tbProcessStageActive = "(
                    SELECT 
                        tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                        tbl_productions_orders_items_stages.active as active
                    FROM tbl_productions_orders_items_stages
                    WHERE tbl_productions_orders_items_stages.final_stage = 1
                    GROUP BY tbl_productions_orders_items_stages.productions_orders_items_id
                ) tb_process_stage_active";

                $this->db->select("
                        tbl_business_plan_items.id as business_plan_item_id,
                        tbl_business_plan.id as id_business_plan,
                        tbl_business_plan.reference_no as reference_no,
                        tbl_productions_orders.reference_no as reference_no_production,
                        tbl_business_plan_items.items_name as items_name,
                        (tbl_business_plan_items.quantity - COALESCE(tb_tranfer_new.quantity,0) - COALESCE(tb_purchase_product.quantity,0)) as product_quantity,
                    ");
                $this->db->join(
                    'tbl_business_plan',
                    'tbl_business_plan.id = tbl_business_plan_items.business_plan_id','inner'
                );
                $this->db->join(
                    'tbl_productions_orders_items',
                    'tbl_productions_orders_items.production_plan_item_id = tbl_business_plan_items.id AND tbl_productions_orders_items.object_item_type = "business_plan"','left'
                );
                $this->db->join(
                    'tbl_productions_orders',
                    'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id','left'
                );
                $this->db->join('tbl_products', 'tbl_products.id = tbl_business_plan_items.items_id', 'inner');
                $this->db->join($tbTranferNew, 'tb_tranfer_new.business_plan_item_id = tbl_business_plan_items.id', 'left');
                $this->db->join($tb_purchase_product, 'tb_purchase_product.business_plan_item_id = tbl_business_plan_items.id', 'left');
                $this->db->where('(tbl_business_plan_items.quantity - COALESCE(tb_tranfer_new.quantity,0) - COALESCE(tb_purchase_product.quantity,0)) > 0');
                $this->db->where('tbl_business_plan_items.items_id',$item_id);
                $this->db->where('not exists(
                    SELECT tbl_productions_orders_items.production_plan_item_id
                    FROM tbl_productions_orders_items
                    JOIN '.$tbProcessStageActive.' ON tb_process_stage_active.productions_orders_items_id = tbl_productions_orders_items.id
                    WHERE tbl_productions_orders_items.production_plan_item_id = tbl_business_plan_items.id AND tbl_productions_orders_items.object_item_type = "business_plan"
                    AND tb_process_stage_active.active = 1
                )', false, false);
                $warehouse = $this->db->get('tbl_business_plan_items')->result_array();
                $dtOrder[$key]['warehouse'] = $warehouse;
            }
        }
        $data['dtOrder'] = $dtOrder;
        echo json_encode($data);
    }

    public function view_tranfer_business($id) {
        $data['id'] = $id;

        $this->load->view('admin/manufactures/view_tranfer_business', $data);
    }

    public function getKeepStockProducts()
    {
        $productions_plan_id = $this->db->escape($this->input->post('view_productions_plan_id'));
        $aColumns = [
            'tbl_tranfer_business.id as id',
            'tbl_tranfer_business.date as date',
            'tbl_tranfer_business.reference_no as reference_no',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name',
            'tbl_tranfer_business.status as status',
            '5 as actions'
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_tranfer_business';
        $where        = [
            'AND tbl_tranfer_business.id_plan = '.$productions_plan_id.''
        ];
        $filter = [];
        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_tranfer_business.created_by'
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $totalQuantity = 0;
        $totalQuantityPrimary = 0;
        $totalQuantityNeed = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $transfer_id = $aRow['id'];
            $row[0] = '<div class="text-center"><a class="fa fa-caret-right font-size-20" onclick="rowChildKeepStock(this, \''.$transfer_id.'\')" href="javascript:void(0)"></a></div>';
            $row[1] = _d($aRow['date']);
            $row[2] = $aRow['reference_no'];

            $strStatus = '';
            if ($aRow['status'] == 1) {
                $strStatus = '<span class="label label-success">'.lang('Đã duyệt').'</span>';
            } else {
                $strStatus = '<span class="label label-warning">'.lang('Chưa duyệt').'</span>';
            }

            $styleDelete = '';
            $this->db->from('tbl_tranfer_business_item');
            $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$transfer_id);
            $this->db->where('EXISTS (
                SELECT tbl_deliveries.order_id
                FROM tbl_deliveries
                WHERE tbl_deliveries.order_id = tbl_tranfer_business_item.order_id
            )');
            $test_quantity = $this->db->get()->result_array();
            if (!empty($test_quantity)) {
                $strStatus = '<span class="inline-block label label-danger" task-status-table="">Đã tạo giao hàng</span>';
                $styleDelete = 'pointer-events: none; opacity: 0.5;';
            }

            $row[3] = '<div class="text-center">'.$aRow['staff_name'].'</div>';
            $row[4] = '<div class="text-center">'.$strStatus.'</div>';
            $row[5] = '<div class="text-center"><span class="btn btn-danger fa fa-trash" onclick="deleteTransferToPlan('.$transfer_id.')" style="'.$styleDelete.'"></span></div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function loadKeepStockProducts() {
        $transfer_id = $this->input->get('transfer_id');
        $data['transfer_id'] = $transfer_id;
        $this->load->view('admin/manufactures/load_keep_stock_products', $data);
    }

    public function deleteTransferToPlanProducts() {

        $data['result'] = 0;
        $data['message'] = lang('fail');

        $transfer_id = $this->input->post('transfer_id');

        $this->db->from('tbl_tranfer_business_item');
        $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$transfer_id);
        $this->db->where('EXISTS (
                SELECT tbl_deliveries.order_id
                FROM tbl_deliveries
                WHERE tbl_deliveries.order_id = tbl_tranfer_business_item.order_id
            )');
        $test_quantity = $this->db->get()->result_array();
        if (!empty($test_quantity)){
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo giao hàng');
            echo json_encode($data);die();
        }

        $success = false;
        if ($transfer_id){
            $this->db->where('tbl_tranfer_business.id',$transfer_id);
            $success = $this->db->delete('tbl_tranfer_business');

            $this->db->where('tbl_tranfer_business_item.tranfer_business_id',$transfer_id);
            $this->db->delete('tbl_tranfer_business_item');

            if ($success){
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function edit_productions_plan($id) {
        if (!$this->perAddProductionsPlan) {
            accessDenied($js = true);
        }
        $data = [];
        $dtProductionsPlan = $this->manufactures_model->rowProductionsPlan($id);

        if (empty($dtProductionsPlan)) {
            refererModel(lang('Không tìm thấy dữ liệu')); die;
        }

        $isEditProductionsPlan = $this->manufactures_model->isEditProductionsPlan($id);
        if (empty($isEditProductionsPlan['result'])) {
            refererModel(lang($isEditProductionsPlan['message'])); die;
        }

        if ($this->input->post('save')) {

            $date = to_sql_date($this->input->post('date'), true);
            $id_branch = $this->input->post('id_branch');
            $note = $this->input->post('note', false);
            $productions_plan_id = $id;

            if (empty($date)) {
                $data['result'] = 0;
                $data['message'] = 'Vui lòng nhập ngày';
                echo json_encode($data); die;
            }

            if (empty($id_branch)) {
                $data['result'] = 0;
                $data['message'] = 'Vui lòng chọn chi nhánh xưởng';
                echo json_encode($data); die;
            }

            $productions_plan_items = $this->input->post('productions_plan_items');
            $arr_productions_plan_items = [];
            $arrProductionsPlanObject = [];

            if ($productions_plan_items) {
                foreach ($productions_plan_items as $key => $value) {
                    $productions_plan_items_id = $value;
                    $type_object = $this->input->post('type_object')[$key];
                    $object_id = $this->input->post('object_id')[$key];
                    $temp_str_object = $key;
                    $item_object_id = $this->input->post('item_object_id')[$key];

                    $quantity_total_details = number_unformat($this->input->post('quantity')[$key]);
                    $versions = $this->input->post('versions')[$key];
                    $versions_stage = $this->input->post('versions_stage')[$key];
                    $product_id = $this->input->post('cs_product_id')[$key];

                    if (empty($quantity_total_details) || $quantity_total_details <= 0) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng nhập số lượng lớn hơn 0');
                        echo json_encode($data);
                        die;
                    }

                    if (empty($versions)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn định mức BOM');
                        echo json_encode($data);
                        die;
                    }

                    if (empty($versions_stage)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn công đoạn');
                        echo json_encode($data);
                        die;
                    }

                    $arrDate = [];
                    if ($type_object == 'orders') {
                        $this->db->select('tbl_order_item_shippings.date_shipping');
                        $this->db->from('tbl_order_item_shippings');
                        $this->db->where('tbl_order_item_shippings.order_item_id', $item_object_id);
                        $order_item_shippings = $this->db->get()->row_array();
                        $arrDate[] = $order_item_shippings['date_shipping'];

                        if (empty($arrProductionsPlanObject[$temp_str_object])) {
                            $arrProductionsPlanObject[$temp_str_object] = [
                                'object_id' => $object_id,
                                'object_type' => 'orders',
                                'is_preventive' => 0,
                            ];
                        }
                        

                    } else if ($type_object == 'business_plan') {
                        $this->db->select('tbl_business_plan_items_date.date');
                        $this->db->from('tbl_business_plan_items_date');
                        $this->db->where('tbl_business_plan_items_date.business_plan_items_id', $item_object_id);
                        $business_plan_items_date = $this->db->get()->row_array();
                        $arrDate[] = $business_plan_items_date['date'];

                        if (empty($arrProductionsPlanObject[$temp_str_object])) {
                            $arrProductionsPlanObject[$temp_str_object] = [
                                'object_id' => $object_id,
                                'object_type' => 'business_plan',
                                'is_preventive' => 0,
                            ];
                        }
                    }

                    $arr_productions_plan_items[] = [
                        'id' => $productions_plan_items_id,
                        'productions_plan_id' => $productions_plan_id,
                        'type_object' => $type_object,
                        'object_id' => $object_id,
                        'item_object_id' => $item_object_id,
                        'product_id' => $product_id,
                        'quantity_minimum' => 0,
                        'quantity_warehouses' => 0,
                        'status' => 'not',
                        'quantity_reserve' => 0,
                        'quantity_total_details' => $quantity_total_details,
                        'versions' => $versions,
                        'versions_stage' => $versions_stage,
                        'arrDate' => $arrDate,
                    ];

                }
            }

            $business_plan_preventive_id = 0;
            $dt_business_plan_preventive = [];
            $arrItemsPerventive = [];
            $product_id_preventive = $this->input->post('product_id_preventive');
            if (!empty($product_id_preventive)) {
                foreach ($product_id_preventive as $k => $val) {
                    $productions_plan_items_id_preventive = !empty($this->input->post('productions_plan_items_id_preventive')[$k]) ? $this->input->post('productions_plan_items_id_preventive')[$k] : 0;
                    $productions_plan_items_id_item_object_id_preventive = !empty($this->input->post('productions_plan_items_id_item_object_id_preventive')[$k]) ? $this->input->post('productions_plan_items_id_item_object_id_preventive')[$k] : 0;
                    $versions_perventive = !empty($this->input->post('versions_perventive')[$k]) ? $this->input->post('versions_perventive')[$k] : '';
                    $versions_stages_perventive = !empty($this->input->post('versions_stages_perventive')[$k]) ? $this->input->post('versions_stages_perventive')[$k] : '';
                    $is_no_stock = !empty($this->input->post('is_no_stock')[$k]) ? $this->input->post('is_no_stock')[$k] : 0;
                    $quantity_preventive = !empty($this->input->post('quantity_preventive')[$k]) ? number_unformat($this->input->post('quantity_preventive')[$k]) : '';
                    if($is_no_stock == 1){
                        $quantity_preventive = 0;
                    }

                    if ($productions_plan_items_id_preventive) {
                        $this->db->select('tbl_productions_plan_items.object_id');
                        $this->db->from('tbl_productions_plan_items');
                        $this->db->where('tbl_productions_plan_items.id', $productions_plan_items_id_preventive);
                        $dtPPlan = $this->db->get()->row_array();
                        if ($dtPPlan) {
                            $business_plan_preventive_id = $dtPPlan['object_id'];
                            
                            $this->db->select('tbl_business_plan.*');
                            $this->db->from('tbl_business_plan');
                            $this->db->where('tbl_business_plan.id', $business_plan_preventive_id);
                            $dt_business_plan_preventive = $this->db->get()->row_array();
                        }
                    }

                    if (empty($quantity_preventive)) continue;
                    if (empty($versions_perventive)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn định mức BOM dự phòng');
                        echo json_encode($data);
                        die;
                    }

                    if (empty($versions_stages_perventive)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn định mức giai đoạn dự phòng');
                        echo json_encode($data);
                        die;
                    }

                    $_product = $this->products_model->rowProduct($val);
                    $quantity_max = $_product['quantity_max'] ?? 0;
                    if ($quantity_preventive > $quantity_max) {
                        $data['result'] = 0;
                        $data['message'] = lang('SL dự phòng vượt quá số lượng tối đa của thành phẩm');
                        echo json_encode($data);
                        die;
                    }

                    $arrItemsPerventive[] = [
                        'id' => $productions_plan_items_id_preventive,
                        'productions_plan_items_id_item_object_id_preventive' => $productions_plan_items_id_item_object_id_preventive,
                        'product_id' => $val,
                        'versions_perventive' => $versions_perventive,
                        'versions_stages_perventive' => $versions_stages_perventive,
                        'quantity_preventive' => $quantity_preventive,
                    ];
                }
            }

            if (empty($arr_productions_plan_items)) {
                $data['result'] = 0;
                $data['message'] = lang('Không có mặt hàng để lưu');
                echo json_encode($data);
            }

            $itemBOMM = $this->input->post('itemBOMM');
            $arrItemBOMCompensation = [];
            if (!empty($itemBOMM)) {
                foreach ($itemBOMM as $key => $value) {
                    $value['quantity_compensation'] = number_unformat($value['quantity_compensation'], false);
                    $quantity_primary = $value['quantity_compensation'] * $value['quantity_exchange'] / $value['exchange_unit'];

                    if ($value['item_type'] == "materials") {
                        $quantity_convert_warehouses = roundNumberFormat($quantity_primary / $value['exchange_standard_unit'] * $value['exchange_unit'], 0);
                    } else {
                        $quantity_convert_warehouses = roundNumberFormat($quantity_primary * $value['conversion_quantity_unit'], 0);
                    }
                    
                    $arrItemBOMCompensation[] = [
                        'item_id' => $value['item_id'],
                        'item_type' => $value['item_type'],
                        'standard_unit' => $value['standard_unit'],
                        'exchange_standard_unit' => $value['exchange_standard_unit'],
                        'quantity_exchange' => $value['quantity_exchange'],
                        'exchange_unit' => $value['exchange_unit'],
                        'quantity' => $value['quantity'],
                        'quantity_compensation' => $value['quantity_compensation'],
                        'quantity_warehouse' => $value['quantity_warehouse'],
                        'quantity_compensation_sm' => $value['quantity_compensation_sm'],
                        'quantity_primary' => $quantity_primary,
                        'quantity_convert_warehouses' => $quantity_convert_warehouses,
                        'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                    ];
                }
            }

            
            $staff_id = get_staff_user_id();
            $options = [
                'date' => $date,
                'id_branch' => $id_branch,
                'note' => $note,
                'date_updated' => date('Y-m-d H:i:s'),
                'updated_by' => $staff_id,
            ];

            $productions_plan = $this->manufactures_model->rowProductionsPlan($id);
            $dt_productions_plan_object = $this->manufactures_model->getProductionsPlanObject($id);
            $itemsPlan = $this->manufactures_model->getProductionsPlanById($id);
            foreach ($itemsPlan as $key => $value) {
                $planDate = $this->manufactures_model->getProductionsPlanDetails($value['id']);
                $itemsPlan[$key]['planDate'] = $planDate;
            }

            // print_arrays($arrProductionsPlanObject);
            $up = $this->manufactures_model->updateProductionsPlan($id, $options);
            if ($up) {

                $this->db->select('tbl_productions_orders.*');
                $this->db->from('tbl_productions_orders_items');
                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id');
                $this->db->where('tbl_productions_orders_items.plan_id', $id);
                $this->db->limit(1);
                $dtProductionsOrders = $this->db->get()->row_array();
                //delete productions orders
                if (!empty($dtProductionsOrders)) {
                    $productions_orders_id = $dtProductionsOrders['id'];
                    $productions_orders = $this->manufactures_model->rowProductionsOrdersById($productions_orders_id);

                    $items = $this->manufactures_model->getProductionsOrdersItems($productions_orders_id);
                    $itemsSub = $this->manufactures_model->getProductionsOrdersItemsSub($productions_orders_id);
                    $productions_plan_orders = $this->manufactures_model->getProductionsPlanOrders($productions_orders_id);
                    $productionsOD = $this->manufactures_model->getProductionsOrdersDetails($productions_orders_id);
                    if ($this->manufactures_model->deleteProductionsOrders($productions_orders_id)) {

                        foreach ($items as $key => $value) {
                            $this->manufactures_model->updateQuantityProductionOrders($value['production_plan_item_id'], $value['quantity'], $minus = 2, $value['object_item_type']);
    
                            if (!empty($productions_orders['productions_plan_id'])) {
                                $this->manufactures_model->updateQuantityPCI($value['productions_capacity_items_id'], $value['quantity'], $minus = 2, $value['object_item_type']);
                            }
                        }
    
                        if (!empty($productions_plan_orders)) {
                            foreach ($productions_plan_orders as $key => $value) {
                                $statusPL = 0;
                                if ($this->manufactures_model->checkPlanQtyByOrder($value['productions_plan_id'], $value['object_type'])) $statusPL = 1;
                                if ($value['object_type'] == "orders") {
                                    $this->orders_model->updateOrdersNew($value['productions_plan_id'], ['status_productions_orders' => $statusPL]);
                                } else if ($value['object_type'] == "business_plan") {
                                    $this->business_plan_model->updateBusinessPlan($value['productions_plan_id'], ['status_productions_orders' => $statusPL]);
                                }
                            }
                        }
    
                        $this->manufactures_model->deleteProductionsOrdersItems($productions_orders_id);
                        $this->manufactures_model->deleteProductionsPlanOrders($productions_orders_id);
                        $this->manufactures_model->deleteProductionsOrdersItemsSub($productions_orders_id);
                        $this->manufactures_model->deleteProductionsOrdersItemsStages($productions_orders_id);
    
                        $this->manufactures_model->deleteProductionsOdersDetail($productions_orders_id);
                        foreach ($productionsOD as $key => $value) {
                            $this->manufactures_model->deleteUpdateInfoStageByProductionODId($value['id']);
                            $this->manufactures_model->deleteQuantityFinishedPreByProductionODId($value['id']);
                            $this->manufactures_model->deletePODEmployeesByPODId($value['id']);
                            $this->manufactures_model->deletePODDepartments($value['id']);
    
                            //handling taks
                            $tasks = $this->site_model->getTasksByRelTypeAndRelId($value['id'], 'order_production_details');
                            if (!empty($tasks)) {
                                $this->site_model->deleteTasksByRelIdAndRelType($value['id'], 'order_production_details');
                                foreach ($tasks as $k => $val) {
                                    $this->site_model->deleteTaskAssigned($val['id']);
                                }
                            }
                            //
                        }
                    }
                }

                //delete productions plan
                foreach ($itemsPlan as $key => $value) {
                    $this->manufactures_model->updateQuantityPlan((float)$value['quantity_total_details'] + (float)$value['quantity_reserve'], $minus = 1, $value['item_object_id'], $value['type_object']);
                    $planDate = $value['planDate'];
                    if (!empty($planDate)) {
                        foreach ($planDate as $k => $val) {
                            $this->manufactures_model->updateQuantityPlanShippings($val['quantity'], $minus = 1, $value['item_object_id'], $value['type_object'], $val['date']);
                        }
                    }
                }

                if (!empty($dt_productions_plan_object)) {
                    foreach ($dt_productions_plan_object as $key => $value) {
                        if ($value['object_type'] == "orders") {
                            $this->orders_model->updateOrdersNew($value['object_id'], ['productions_plan_id' => 0]);
                        } else if ($value['object_type'] == "business_plan") {
                            $this->business_plan_model->updateBusinessPlan($value['object_id'], ['productions_plan_id' => 0]);
                        }
                    }
                }

                $this->manufactures_model->deleteProductionsPlanItems($id);
                $this->manufactures_model->deleteProductionsPlanBom($id);
                $this->manufactures_model->deleteProductionsPlanWarehouses($id);
                $this->manufactures_model->deleteProductionsPlanObject($id);
                $this->manufactures_model->deleteProductionsPlanCompensation($id);

                $business_plan_preventive = $this->manufactures_model->getBusinessPlanByProductionsPlanPreventiveId($id);
                if (!empty($business_plan_preventive)) {
                    $items = $this->business_plan_model->getBusinessPlanItems($business_plan_preventive['id']);
                    if ($this->business_plan_model->deleteBusinessPlan($business_plan_preventive['id'])) {
                        foreach ($items as $key => $value) {
                            if ($this->business_plan_model->deleteBusinessPlanItems($value['id'])) {
                                $this->business_plan_model->deleteBusinessPlanItemsDateBusinessPlanItemsId($value['id']);
                            }
                        }
        
                        $this->business_plan_model->deleteBusinessPlanItemsStages($business_plan_preventive['id']);
                    }
                }

                //add new
                foreach ($arr_productions_plan_items as $key => $value) {
                    $op_productions_plan_items = [
                        'id' => $value['id'],
                        'productions_plan_id' => $value['productions_plan_id'],
                        'type_object' => $value['type_object'],
                        'object_id' => $value['object_id'],
                        'item_object_id' => $value['item_object_id'],
                        'product_id' => $value['product_id'],
                        'quantity_minimum' => $value['quantity_minimum'],
                        'quantity_warehouses' => $value['quantity_warehouses'],
                        'status' => 'not',
                        'quantity_reserve' => $value['quantity_reserve'],
                        'quantity_total_details' => $value['quantity_total_details'],
                        'versions' => $value['versions'],
                        'versions_stage' => $value['versions_stage'],
                    ];
                    $productions_plan_item_id = $this->manufactures_model->insertProductionsPlanItems($op_productions_plan_items);

                    if ($value['arrDate']) {
                        foreach ($value['arrDate'] as $k => $val) {
                            $productions_plan_details = [
                                'productions_plan_item_id' => $productions_plan_item_id,
                                'date' => $val,
                                'quantity' => $value['quantity_total_details']
                            ];

                            $this->manufactures_model->insertProductionsPlanDetails($productions_plan_details);
                            $this->manufactures_model->updateQuantityPlanShippings($value['quantity_total_details'], $plus = 0, $value['item_object_id'], $value['type_object'], $val);
                        }
                    }

                    $this->manufactures_model->updateQuantityPlan($value['quantity_total_details'], $plus = 0, $value['item_object_id'], $value['type_object']);
                }

                if (!empty($dt_business_plan_preventive)) {
                    if (!empty($arrItemsPerventive)) {
                        $rs_preventive = $this->manufactures_model->handlingPreventiveUpdate($productions_plan_id, $arrItemsPerventive, $dt_business_plan_preventive);
                        if (!empty($rs_preventive)) {
                            $arrProductionsPlanObject[] = [
                                'object_id' => $rs_preventive,
                                'object_type' => 'business_plan',
                                'is_preventive' => 1,
                            ];
                        }
                    }
                } else if (!empty($arrItemsPerventive)) {
                    $rs_preventive = $this->manufactures_model->handlingPreventive($productions_plan_id, $arrItemsPerventive);
                    if (!empty($rs_preventive)) {
                        $arrProductionsPlanObject[] = [
                            'object_id' => $rs_preventive,
                            'object_type' => 'business_plan',
                            'is_preventive' => 1,
                        ];
                    }
                }

                if (!empty($arrProductionsPlanObject)){
                    foreach ($arrProductionsPlanObject as $key => $value) {
                        $arrProductionsPlanObject[$key]['productions_plan_id'] = $productions_plan_id;

                        $isFinished = $this->manufactures_model->checkFinishedPlan($value['object_id'], $value['object_type']);
                        if (empty($isFinished) && $value['object_type'] == "orders") {
                            $this->orders_model->updateOrdersNew($value['object_id'], ['productions_plan_id' => 1]);
                        } else if (empty($isFinished) && $value['object_type'] == "business_plan") {
                            $this->business_plan_model->updateBusinessPlan($value['object_id'], ['productions_plan_id' => 1]);
                        }
                    }
                    $this->manufactures_model->insertBatchProductionsPlanObject($arrProductionsPlanObject);
                }

                //handling BOM
                $this->manufactures_model->handlingBomProductionsPlan($productions_plan_id);

                //
                if (empty($dtProductionsOrders)) {
                    $this->manufactures_model->autoProductionsOrders($productions_plan_id);
                } else {
                    //update productions orders
                    $this->manufactures_model->autoProductionsOrdersUpdate($productions_plan_id, $dtProductionsOrders);
                }
                //

                if (!empty($arrItemBOMCompensation)) {
                    foreach ($arrItemBOMCompensation as $key => $value) {
                        $arrItemBOMCompensation[$key]['productions_plan_id'] = $productions_plan_id;
                    }
                    $this->manufactures_model->insertBatchProductionsPlanCompensation($arrItemBOMCompensation);
                }

                insertActivityLog([
                    'type_parent_obj' => 'productions_plan',
                    'table_obj' => 'tbl_productions_plan',
                    'id_obj' => $productions_plan_id,
                    'name_obj' => $productions_plan['reference_no'],
                    'content' => lang('Cập nhật kế hoạch NPL') . ' [' . $productions_plan['reference_no'] . ']',
                    'actions' => 'edit'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
                echo json_encode($data); die;
            }
        } else {
            $data['branch'] = $this->site_model->getBranch($arr_id_not = [BRANCH_DEFAULT]);
            $data['dtProductionsPlan'] = $dtProductionsPlan;
            $data['id'] = $id;
            $this->load->view('admin/manufactures/edit_productions_plan', $data);
        }

    }

    public function loadProductsPreventiveEdit() {
        $data = [];
        $dataPOST = $this->input->post();
        if (!empty($dataPOST)) {
            $cs_product_id = $this->input->post('cs_product_id');
            $productions_plan_id = $this->input->post('productions_plan_id');
            $arrProducts = [];

            if (!empty($cs_product_id)) {
                $cs_product_id = array_unique($cs_product_id);
                $this->db->select('
                    tbl_products.id as product_id,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.versions as versions,
                    tbl_products.versions_stage as versions_stage,
                    tblunits.unit as unit_name,
                    tbl_products.is_no_stock as is_no_stock,
                    tbl_products.quantity_max as quantity_max,
                ', false);
                $this->db->from('tbl_products');
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.conversion_unit', 'left');
                $this->db->where_in('tbl_products.id', $cs_product_id);
                $dtProducts = $this->db->get()->result_array();
                if (!empty($dtProducts)) {
                    foreach ($dtProducts as $key => $value) {

                        $this->db->select('
                            tbl_productions_plan_items.*
                        ', false);
                        $this->db->from('tbl_productions_plan_items');
                        $this->db->where('tbl_productions_plan_items.productions_plan_id', $productions_plan_id);
                        $this->db->where('tbl_productions_plan_items.is_preventive', 1);
                        $this->db->where('tbl_productions_plan_items.product_id', $value['product_id']);
                        $preventive = $this->db->get()->row_array();
                        if (!empty($preventive)) {
                            $versions = $preventive['versions'];
                            $versions_stage = $preventive['versions_stage'];

                            $dtProducts[$key]['id'] = $preventive['id'];
                            $dtProducts[$key]['item_object_id'] = $preventive['item_object_id'];
                            $quantity = $preventive['quantity_total_details'];
                        } else {
                            $dtProducts[$key]['id'] = 0;
                            $dtProducts[$key]['item_object_id'] = 0;
                            $versions = $value['versions'];
                            $versions_stage = $value['versions_stage'];
                            $quantity = 0;
                        }

                        $dtProducts[$key]['quantity'] = $quantity;

                        $this->db->select('tbl_product_versions.versions as versions');
                        $this->db->from('tbl_product_versions');
                        $this->db->where_in('tbl_product_versions.product_id', $value['product_id']);
                        $product_verions = $this->db->get()->result_array();
                        $optionsVersions = "<option></option>";
                        if (!empty($product_verions)) {
                            foreach ($product_verions as $k => $val) {
                                $selected = ($versions == $val['versions']) ? 'selected' : '';
                                $optionsVersions.= '<option '.$selected.' value="'.$val['versions'].'">'.$val['versions'].'</option>';
                            }
                        }
                        $dtProducts[$key]['optionsVersions'] = $optionsVersions;

                        //stages
                        $this->db->select('tbl_product_stages.versions as versions');
                        $this->db->from('tbl_product_stages');
                        $this->db->where_in('tbl_product_stages.product_id', $value['product_id']);
                        $product_verions_stages = $this->db->get()->result_array();
                        $optionsVersionsStages = "<option></option>";
                        if (!empty($product_verions_stages)) {
                            foreach ($product_verions_stages as $k => $val) {
                                $selected = ($versions_stage == $val['versions']) ? 'selected' : '';
                                $optionsVersionsStages.= '<option '.$selected.' value="'.$val['versions'].'">'.$val['versions'].'</option>';
                            }
                        }
                        $dtProducts[$key]['optionsVersionsStages'] = $optionsVersionsStages;

                        
                    }
                }
                $arrProducts = $dtProducts;
            }
            $data['arrProducts'] = $arrProducts;
        }
        echo json_encode($data);
    }

    public function loadBOMPPEdit()
    {
        $data = [];
        $trItems = '';
        if ($this->input->post()) {
            $item_object_id = $this->input->post('item_object_id');
            $dataPOST = $this->input->post();
            $arrItems = [];
            if (!empty($item_object_id)) {
                foreach ($item_object_id as $key => $value) {
                    $versions = !empty($dataPOST['versions'][$key]) ? $dataPOST['versions'][$key] : '';
                    $quantity = !empty($dataPOST['quantity'][$key]) ? number_unformat($dataPOST['quantity'][$key]) : 0;
                    $product_id = !empty($dataPOST['poduct_id_css'][$key]) ? $dataPOST['poduct_id_css'][$key] : '';
                    if (!empty($versions) && !empty($quantity)) {
                        $arrItems[] = [
                            'product_id' => $product_id,
                            'versions' => $versions,
                            'quantity' => $quantity,
                        ];
                    }
                }
            }
            if (!empty($dataPOST['product_id_preventive'])) {
                foreach ($dataPOST['product_id_preventive'] as $key => $value) {
                    $versions = !empty($dataPOST['versions_perventive'][$key]) ? $dataPOST['versions_perventive'][$key] : '';
                    $quantity = !empty($dataPOST['quantity_preventive'][$key]) ? number_unformat($dataPOST['quantity_preventive'][$key]) : 0;
                    $product_id = !empty($dataPOST['product_id_preventive'][$key]) ? $dataPOST['product_id_preventive'][$key] : '';

                    if (!empty($versions) && !empty($quantity)) {
                        $arrItems[] = [
                            'product_id' => $product_id,
                            'versions' => $versions,
                            'quantity' => $quantity,
                        ];
                    }
                }
            }

            $productions_plan_id = $this->input->post('productions_plan_id');
            $arrBOMMaterial = [];
            if (!empty($arrItems)) {
                foreach ($arrItems as $key => $value) {
                    $this->manufactures_model->handlingBomPP($arrBOMMaterial, $value['product_id'], $value['versions'], $value['quantity'], 0, $value['quantity'], 0);
                }

                // print_arrays($arrBOMMaterial);
                $arrSumMaterial = [];
                foreach ($arrBOMMaterial as $key => $value) {
                    $str_item_id = $value['item_type'] . '__' . $value['item_id'];
                    if (empty($arrSumMaterial[$str_item_id])) {
                        $arrSumMaterial[$str_item_id] = $value;
                    } else {
                        if ($value['quantity_compensation'] > $arrSumMaterial[$str_item_id]['quantity_compensation']) {
                            $arrSumMaterial[$str_item_id]['quantity_compensation'] = $value['quantity_compensation'];
                        }
                        $arrSumMaterial[$str_item_id]['quantity'] = $arrSumMaterial[$str_item_id]['quantity'] + $value['quantity'];
                    }
                }

                $index = 0;
                if (!empty($arrSumMaterial)) {
                    foreach ($arrSumMaterial as $key => $value) {
                        $item_type = $value['item_type'];
                        $item_id = $value['item_id'];
                        $unit_id = $value['unit_id'];

                        $conversion_quantity_unit = 1;
                        if ($item_type == "materials") {
                            $typeW = 'nvl';
                        } else {
                            $typeW = 'product';
                            $info = $this->products_model->rowProduct($item_id);
                            $conversion_quantity_unit = $info['conversion_quantity_unit'];
                        }

                        $quantity = $value['quantity'];
                        $quantity_compensation = $value['quantity_compensation'];
                        $quantity_compensation_sm = $value['quantity_compensation_sm'];

                        if (isset($dataPOST[$item_type.'__'.$item_id])) {
                            $quantity_compensation = (float)$dataPOST[$item_type.'__'.$item_id];
                        } else if ($productions_plan_id) {
                            $this->db->select('
                                tbl_productions_plan_compensation.quantity_compensation
                            ', false);
                            $this->db->from('tbl_productions_plan_compensation');
                            $this->db->where('tbl_productions_plan_compensation.item_type', $item_type);
                            $this->db->where('tbl_productions_plan_compensation.item_id', $item_id);
                            $this->db->where('tbl_productions_plan_compensation.productions_plan_id', $productions_plan_id);
                            $productions_plan_compensation = $this->db->get()->row_array();
                            if (!empty($productions_plan_compensation)) {
                                $quantity_compensation = $productions_plan_compensation['quantity_compensation'];
                            }
                        }

                        $standard_unit = $value['standard_unit'];
                        $exchange_standard_unit = $value['exchange_standard_unit'];
                        $exchange_unit = $value['exchange_unit'];
                        $quantity_exchange = $value['quantity_exchange'];

                        $unit = $this->unit_model->rowUnit($standard_unit);

                        $this->db->select('
                            SUM(tblwarehouse_items.product_quantity) as product_quantity,
                        ', false);
                        $this->db->from('tblwarehouse_items');
                        $this->db->where_not_in('tblwarehouse_items.warehouse_id', [WAREHOUSES_CAPACITY]);
                        $this->db->where('tblwarehouse_items.type_items', $typeW);
                        $this->db->where('tblwarehouse_items.id_items', $item_id);
                        $this->db->where('tblwarehouse_items.product_quantity >', 0);
                        $dtW = $this->db->get()->row_array();

                        $quantityNeed = $quantity + $quantity_compensation;
                        $quantity_primary = $quantityNeed * $quantity_exchange / $exchange_unit;
                        if ($item_type == "materials") {
                            // $quantity_convert_warehouse = roundNumberFormat($quantity_primary / $exchange_standard_unit * $exchange_unit, 0);
                            $quantity_convert_warehouse = ceil($quantity_primary / $exchange_standard_unit * $exchange_unit);
                        } else {
                            // $quantity_convert_warehouse = roundNumberFormat($quantity_primary * $conversion_quantity_unit, 0);
                            $quantity_convert_warehouse = ceil($quantity_primary * $conversion_quantity_unit);
                        }
                        $quantity_warehouse = $dtW['product_quantity'];

                        $strStatus = '';
                        if ($quantity_convert_warehouse > $quantity_warehouse) {
                            $strStatus = '<span class="label label-danger">' . lang('Chưa đủ kho') . '</span>';
                        } else {
                            $strStatus = '<span class="label label-success">' . lang('Đã đủ kho') . '</span>';
                        }
                        $trItems .= '<tr>
                            <td class="text-center">' . $value['item_code'] . '</td>
                            <td class="text-center">' . $value['item_name'] . '</td>
                            <td class="text-center"><span class="label label-' . ($item_type == 'materials' ? 'success' : 'danger') . '">' . lang($item_type) . '</span></td>
                            <td class="text-center">' . $unit['unit'] . '</td>
                            <td class="text-center">' . ceil($quantity) . '</td>
                            <td>
                                <input type="hidden" name="itemBOMM[' . $index . '][item_id]" class="form-control item_id" value="' . $item_id . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][item_type]" class="form-control item_type" value="' . $item_type . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][standard_unit]" class="form-control standard_unit" value="' . $standard_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][exchange_standard_unit]" class="form-control exchange_standard_unit" value="' . $exchange_standard_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity_exchange]" class="form-control quantity_exchange" value="' . $quantity_exchange . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][exchange_unit]" class="form-control exchange_unit" value="' . $exchange_unit . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity]" class="form-control quantity" value="' . $quantity . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity_warehouse]" class="form-control quantity_warehouse" value="' . $quantity_warehouse . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][quantity_compensation_sm]" class="form-control quantity_compensation_sm" value="' . $quantity_compensation_sm . '">
                                <input type="hidden" name="itemBOMM[' . $index . '][conversion_quantity_unit]" class="form-control conversion_quantity_unit" value="' . $conversion_quantity_unit . '">
                                <input type="text" onchange="changeQuantityCompensation(this)" name="itemBOMM[' . $index . '][quantity_compensation]" class="form-control quantity_compensation number-format" value="' . formatNumber($quantity_compensation) . '">
                            </td>
                            <td class="text-center quantity_convert_warehouse">
                                ' . formatNumber($quantity_convert_warehouse) . '
                            </td>
                            <td class="text-center quantity_warehouse">
                                ' . formatNumber($quantity_warehouse) . '
                            </td>
                            <td class="td-status text-center">
                                ' . $strStatus . '
                            </td>
                        </tr>';
                        $index++;
                    }
                }
            }
        }

        $data['trItems'] = $trItems;
        echo json_encode($data);
    }

    public function show_warehouses_plan($product_id)
    {
        $data = [];

        $data['product_id'] = $product_id;
        $this->load->view('admin/manufactures/show_warehouses_plan', $data);
    }

    public function getWarehousesPlan()
    {
        $product_id = $this->input->post('wp_product_id');


        $orders = '(
            SELECT
                CONCAT(tbl_orders.reference_no, "(ĐHB)")
            FROM tbl_orders
            WHERE tbl_orders.id = tbl_productions_orders_details.object_id
        )';

        $business_plan = '(
            SELECT
                tbl_business_plan.reference_no
            FROM tbl_business_plan
            WHERE tbl_business_plan.id = tbl_productions_orders_details.object_id
        )';
        $subQuery = '(
            SELECT
                IF (tbl_productions_orders_details.object_type = "orders", '.$orders.', '.$business_plan.')
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
        )';

        $aColumns = [
            'tbllocaltion_warehouses.id as id',
            'tbl_products.code as item_code',
            'tbl_products.name as item_name',
            ''.$subQuery.' as reference_no',
            'tblwarehouse.name as name_warehouse',
            'tbllocaltion_warehouses.name as locations_name',
            'tblwarehouse_items.product_quantity as product_quantity',
        ];
        
        $sIndexColumn = 'id';
        $sTable       = 'tblwarehouse_items';
        $where        = ['
            AND tblwarehouse_items.product_quantity > 0 AND tblwarehouse_items.id_items = '.$product_id.' AND tblwarehouse_items.type_items = "product" AND tblwarehouse_items.warehouse_id NOT IN (' . WAREHOUSES_CAPACITY . ', ' . WAREHOUSES_HOLD . ', '.WAREHOUSES_ERRORS.', '.WAREHOUSES_TAMP.') AND tbllocaltion_warehouses.stage_id = 0 AND (
                tbllocaltion_warehouses.pod_id = 0 OR exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "business_plan" 
                )
                OR exists (
                    SELECT tbl_productions_orders_details.id
                    FROM tbl_productions_orders_details
                    INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id
                    WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "orders" AND tbl_orders.type_orders = 4
                )
            )
        '];

        $filter = [];
        $join = [
            'INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion',
            'INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id',
            'INNER JOIN tbl_products ON tbl_products.id = tblwarehouse_items.id_items',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            // $transfer_id = $aRow['id'];
            $row[0] = '<div class="text-center">'.$start.'</div>';
            $row[1] = $aRow['item_code'];
            $row[2] = $aRow['item_name'];
            $row[3] = $aRow['reference_no'];
            $row[4] = '<div class="text-center">'.$aRow['name_warehouse'].'</div>';
            $row[5] = '<div class="text-center">'.$aRow['locations_name'].'</div>';
            $row[6] = '<div class="text-center">'.formatNumber($aRow['product_quantity']).'</div>';

            $total_quantity+= $aRow['product_quantity'];
            $output['aaData'][] = $row;
        }
        $output['total_quantity'] = $total_quantity;
        echo json_encode($output);
    }

    public function updateProductionsOrdersItemsStages($limit, $offset) {
        $this->db->select('tbl_productions_orders_items.id, tbl_productions_orders_items.versions_stage as versions_stage, tbl_productions_orders_items.items_id as items_id');
        $this->db->from('tbl_productions_orders_items');
        $this->db->limit($limit, $offset);
        $productions_orders_items = $this->db->get()->result_array();
        // print_arrays($productions_orders_items);
        $arrProductionsOrdersItemsStages = [];
        if (!empty($productions_orders_items)) {
            foreach ($productions_orders_items as $key => $value) {
                $productions_orders_items_id = $value['id'];
                $versions_stage = $value['versions_stage'];
                $items_id = $value['items_id'];

                $this->db->select('
                    tbl_product_stages_versions.*
                ', false);
                $this->db->from('tbl_product_stages');
                $this->db->join('tbl_product_stages_versions', ' tbl_product_stages_versions.version_id = tbl_product_stages.id');
                $this->db->where('tbl_product_stages.product_id', $items_id);
                $this->db->where('tbl_product_stages.versions', $versions_stage);
                $product_stages_versions = $this->db->get()->result_array();
                if (empty($product_stages_versions)) {
                    continue;
                }

                $this->db->select('
                    tbl_productions_orders_items_stages.*
                ', false);
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_items_id);
                $productions_orders_items_stages = $this->db->get()->result_array();
                if (!empty($productions_orders_items_stages)) {
                    foreach ($productions_orders_items_stages as $k => $val) {
                        $stage_id = $val['stage_id'];
                        foreach ($product_stages_versions as $kP => $vP) {
                            if ($stage_id == $vP['stage_id']) {
                                $arrProductionsOrdersItemsStages[] = [
                                    'id' => $val['id'],
                                    'face' => $vP['face'],
                                    'face_after' => $vP['face_after'],
                                ];
                                break;
                            }
                        }
                        
                    }
                }
            }
        }

        // print_arrays($arrProductionsOrdersItemsStages);
        if (!empty($arrProductionsOrdersItemsStages)) {
            $this->db->update_batch('tbl_productions_orders_items_stages', $arrProductionsOrdersItemsStages, 'id');
        }

        // print_arrays($arrProductionsOrdersItemsStages);
    }

    public function show_view_purchase($id) {
        $data = [];
        $data['id'] = $id;

        $this->load->view('admin/manufactures/show_view_purchase', $data);
    }

    public function getPurchase()
    {
        $item_id = $this->input->post('_item_id');
        $arr_item = explode('__', $item_id);
        $item_id = $arr_item[1];
        $item_type = $arr_item[0];

        if ($item_type == "materials") {
            $item_type = 'nvl';
        } else {
            $item_type = 'product';
        }

        $productions_plan_search = $this->input->post('productions_plan_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');


        $aColumns = [
            'tblpurchases.date as date',
            'CONCAT(tblpurchases.prefix, "", tblpurchases.code) as reference_no',
            'tblpurchases_items.quantity_net as quantity_net',
            'tblpurchases.status as status',
            'tblpurchases.type as type',
        ];
        
        $sIndexColumn = 'id';
        $sTable       = 'tblpurchases';
        $where        = ['
            AND tblpurchases.is_plans = 1 AND tblpurchases_items.type = "'.$item_type.'" AND tblpurchases_items.product_id = '.$item_id.'
        '];

        $filter = [];
        $join = [
            'INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id',
        ];

        if (!empty($productions_plan_search)) {
            array_push($where, ' AND exists (
                SELECT
                    tbl_purchases_plans.purchases_id
                FROM tbl_purchases_plans
                WHERE tbl_purchases_plans.purchases_id = tblpurchases.id AND tbl_purchases_plans.productions_plan_id IN (' . implode(',', $productions_plan_search) . ')
            )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search);
            array_push($where, ' AND tblpurchases.date >= "'.$start_date_search.' 00:00:00"');
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search);
            array_push($where, ' AND tblpurchases.date <= "'.$end_date_search.' 23:59:59"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblpurchases.history_status as history_status',
            'tblpurchases.note_cancel as note_cancel',
            'tblpurchases.id as id',
            'tblpurchases_items.type as type',
            'tblpurchases_items.product_id as product_id',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $total_quantity = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;




            $row[0] = _d($aRow['date']);
            $row[1] = $aRow['reference_no'];
            $row[2] = '<div class="text-center">'.formatNumber($aRow['quantity_net']).'</div>';

            $_status = $aRow['status'];
            $_history_status = $aRow['history_status'];
            if ($_status == 1) {
                $type = 'warning';
                $status = _l('dont_confirm');
            } elseif ($_status == 2) {
                $type = 'info';
                $status = _l('dont_approve');
            } elseif ($_status == 3) {
                $type = 'success';
                $status = _l('ch_confirm_22');
            } else {
                $type = 'danger';
                $status = _l('ch_cancel');
            }
            $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $_status . '">' . $status . '</a></span><br>';

            $history_status = explode('|', $_history_status);
            $str_history = '';
            foreach ($history_status as $key => $value) {
                $data = explode(',', $value);
                if (is_numeric($data[0])) {
                    if ($key == 1) {
                        $name_status = _l('ch_confirm') . ': ';
                    } elseif ($key == 2) {
                        $name_status = _l('ch_confirm_2') . ': ';
                    } elseif ($key == 3) {
                        $name_status = _l('ch_cancel') . ': ';
                    }
                    $str_history .= $name_status . staff_profile_image($data[0], array('staff-profile-image-small mright5'), 'small',
                            array(
                                'data-toggle' => 'tooltip',
                                'data-title' => _l('ch_time') . ': ' . _dt($data[1])
                            )) . get_staff_full_name($data[0]) . '<br>';
                }
                $note_cancel = false;
                if ($key == 3 && ($aRow['note_cancel'] != '')) {
                    $note_cancel = true;
                    $str_history .= '<b style="color:red">' . _l('ch_note_cancel') . ': ' . $aRow['note_cancel'] . '</b><br>';
                }
            }
            $row[3] = $status.$str_history;

            $purchase_check = get_items_purchase_new($aRow['id']);
            $purchase_order_check = get_items_purchase_check($aRow['id']);
            $this->db->where('tblpurchases_items.purchases_id', $aRow['id']);
            $this->db->from('tblpurchases_items');
            $count_items = $this->db->count_all_results();
            $data_check = '';
            $_outputStatus_check = '';
            $count_check = 0;
            $_data_check_count = '';
            $div = '';

            // $order_check = get_table_where('tblpurchase_order', array('id_purchases' => $aRow['id']), '', 'row');
            $this->db->select('tblpurchase_order.*', false);
            $this->db->from('tblpurchase_order');
            $this->db->join('tblpurchase_order_items', 'tblpurchase_order_items.id_purchase_order = tblpurchase_order.id');
            $this->db->where('tblpurchase_order.id_purchases', $aRow['id']);
            $this->db->where('tblpurchase_order_items.type', $aRow['type']);
            $this->db->where('tblpurchase_order_items.product_id', $aRow['product_id']);
            $order_check = $this->db->get()->row();
            if (!empty($order_check)) {
                // $purchase_order_check_count = get_table_where('tblpurchase_order',
                //     array('id_purchases' => $order_check->id_purchases));

                $this->db->select('tblpurchase_order.*', false);
                $this->db->from('tblpurchase_order');
                $this->db->join('tblpurchase_order_items', 'tblpurchase_order_items.id_purchase_order = tblpurchase_order.id');
                $this->db->where('tblpurchase_order.id_purchases', $aRow['id']);
                $this->db->where('tblpurchase_order_items.type', $aRow['type']);
                $this->db->where('tblpurchase_order_items.product_id', $aRow['product_id']);
                $purchase_order_check_count = $this->db->get()->result_array();
                $count_check = count($purchase_order_check_count);
                foreach ($purchase_order_check_count as $k => $v) {
                    // $_data_check_count .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                    $_data_check_count .= '<li class="hoang"><a href="javascript:void(0)" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                    $div.= '<div class="hoang text-center"><a href="javascript:void(0)" >' . $v['prefix'] . '-' . $v['code'] . '</a></div>';
                }
            }

            if ($purchase_order_check == $count_items) {
                $data_check = '<div class="label label-danger">Chưa đặt</div>';
            } else {
                if ($purchase_check <= 0) {
                    $_outputStatus_check = '<div class="dropdown" style="text-align: center;margin-top:10px">
                        <button class="dropdown-toggle no_background label label-info" type="button" data-toggle="dropdown">Đã đặt (' . count_number_PO_ch($count_check) . ')
                            </button>
                            <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
                    // $_outputStatus_check .= $_data_check_count;
                    $_outputStatus_check .= '</ul></div>';
                    $data_check = $_outputStatus_check;
                    // $data_check = '<div class="label label-info">Đã tạo đơn hàng </div>'.$_outputStatus_check;
                } elseif ($purchase_check > 0) {
                    $_outputStatus_check = '<div class="dropdown" style="text-align: center;margin-top:10px">
                        <button class="dropdown-toggle no_background label label-warning" type="button" data-toggle="dropdown">Đặt 1 phần (' . count_number_PO_ch($count_check) . ')
                            </button>
                            <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
                    // $_outputStatus_check .= $_data_check_count;
                    $_outputStatus_check .= '</ul></div>';
                    $data_check = $_outputStatus_check;
                    // $data_check = '<div class="label label-warning">Tạo 1 phần đơn hàng</div>'.$_outputStatus_check;
                }
            }

            // $row[4] = $data_check.$div;
            $row[4] = $div;

            $total_quantity+= $aRow['quantity_net'];
            $output['aaData'][] = $row;
        }
        $output['total_quantity'] = $total_quantity;
        echo json_encode($output);
    }

    public function exportExcelPOTotal() {
        if ($this->input->post('export_excel')) {
			ini_set('memory_limit', '3500M');
			include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
			$this->load->library('PHPExcel');
            $this->perViewProductionsOrders = has_permission('manufactures_productions_orders', '', 'view');

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
			$objPHPExcel->getDefaultStyle()->applyFromArray([
				'font' => array(
					'name'  => 'Times New Roman'
				),
			]);

			insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

			$excel = cloumns_excel();
			$objPHPExcel->getActiveSheet()->setCellValue(
				'A5',
				('LỆNH SẢN XUẤT TỔNG HỢP')
			)->getStyle("A5")->applyFromArray([
				'font' => array(
					'bold' => true,
					'size' => 16,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			]);
			$objPHPExcel->getActiveSheet()->mergeCells('A5:L5');

			$rowBegin = 6;
			$iExcel = -1;
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'STT');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ngày mở đơn');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ngày lập lệnh SX');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lệnh sản xuất tổng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Hình ảnh');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Mã SP');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Tên SP');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ngày DK giao hàng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ngày giữ');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Các NPL đã xuất');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Khổ in ngang x dọc = cm');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng đặt');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng sx');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng giữ hàng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng tờ in');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng hoàn thành');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng lỗi');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Trạng thái');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Nhóm công đoạn');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Công đoạn sản phẩm');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ghi chú');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Chi nhánh xưởng');
			
			$iExcelEnd = $iExcel;
			$objPHPExcel->getActiveSheet()->getStyle($excel[0].($rowBegin - 1).':'.$excel[$iExcel].($rowBegin))->applyFromArray([
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'font' => array(
					'bold' => true,
				),
			]);

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
            $status_search = $this->input->post('status_search');
            $status_search_order = $this->input->post('status_search_order');
            $branch_search = $this->input->post('branch_search');
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

            if (empty($start_date_search) || empty($end_date_search)) {
				$response = array(
					'result' => 0,
					'message' => lang('Vui lòng lọc ngày bắt đầu và kết thúc'),
				);
				die(json_encode($response));
			}

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
                    SUM(tbl_productions_orders_items.quantity) as quantity,
                    SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused,
                    SUM(0) as quantity_errors,
                    tbl_productions_orders_items.plan_id as plan_id
                FROM tbl_productions_orders_items
                INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
                GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
            ) tb_production_order_item";
            
			$aColumns = [
                'tbl_productions_orders.id as id',
                'tbl_productions_orders.date as date',
                'tbl_productions_orders.date_created as date_created',
                'tbl_productions_orders.reference_no as reference_no',
                'tbl_productions_orders_items.items_id',
                'tbl_productions_orders_items.productions_orders_id',
                'GROUP_CONCAT(tbl_productions_orders_items.id) as _poi_id',
                'GROUP_CONCAT(tbl_productions_orders_details.id) as _pod_id',
                'tbl_productions_orders.total_quantity as quantity_sx',
                'SUM(tbl_productions_orders_items.quantity) as quantity',
                'SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_finished',
                'tbl_productions_orders_items.plan_id as plan_id',
                'tbl_productions_orders.location_id as location_id',
                'tbl_productions_plan.note as note',
            ];

			$where = [];
			if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
                array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
            }
    
            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
                array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
            }
    
            if (!$this->perViewProductionsOrders) {
                array_push($where, "AND tbl_productions_orders.created_by =".get_staff_user_id());
            }

			$where = stringWhere($where);
			$this->db->select(implode(',', $aColumns), false);
			$this->db->from('tbl_productions_orders');

            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.productions_orders_id = tbl_productions_orders.id');
            $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id');
            $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_orders_items.plan_id');
			$this->db->where($where, false, false);
            $this->db->group_by('tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id');
            $this->db->order_by('tbl_productions_orders.id DESC');
			$productions_orders = $this->db->get()->result_array();

			if (!empty($productions_orders)) {
				$arrPOId = [];
                $arrProductId = [];
                $arrPlanId = [];
                $arrLocationId = [];
				foreach ($productions_orders as $key => $value) {
					$arrPOId[] = $value['id'];
                    $arrProductId[] = $value['items_id'];
                    $arrPlanId[] = $value['plan_id'];
                    $arrLocationId[] = $value['location_id'];
				}

                if (!empty($arrLocationId)) {
                    $arrLocationId = array_unique($arrLocationId);
                    $this->db->select('tblbranch.id, tblbranch.name', false);
                    $this->db->from('tblbranch');
                    $this->db->where_in('tblbranch.id', $arrLocationId);
                    $listBranch = $this->db->get()->result_array();
                    $listBranch = array_reduce($listBranch, function ($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    });
                }

                //list product
				if (!empty($arrProductId)) {
                    $arrProductId = array_unique($arrProductId);

					$this->db->select('
                        tbl_products.id,
                        tbl_products.images,
                        tbl_products.code,
                        tbl_products.name,
                    ', false);
                    $this->db->from('tbl_products');
                    $this->db->where_in('tbl_products.id', $arrProductId);
                    $dtProducts = $this->db->get()->result_array();
                    if (!empty($dtProducts)) {
                        $dtProducts = array_reduce($dtProducts, function($carry, $item) {
                            $carry[$item['id']] = $item;
                            return $carry;
                        });
                    }
				}

                //list plan
                if (!empty($arrPlanId)) {
                    $arrPlanId = array_unique($arrPlanId);
                    $this->db->select('
                        tbl_productions_plan_items.productions_plan_id,
                        tbl_productions_plan_items.product_id,
                        MIN(tbl_productions_plan_details.date) as date_shipping
                    ', false);
                    $this->db->from('tbl_productions_plan_items');
                    $this->db->join('tbl_productions_plan_details', 'tbl_productions_plan_details.productions_plan_item_id = tbl_productions_plan_items.id');
                    $this->db->where('tbl_productions_plan_items.is_preventive = 0', false, false);
                    $this->db->where_in('tbl_productions_plan_items.productions_plan_id', $arrPlanId, false);
                    $this->db->group_by('tbl_productions_plan_items.product_id, tbl_productions_plan_items.productions_plan_id');
                    $dtPlanMin = $this->db->get()->result_array();
                    if (!empty($dtPlanMin)) {
                        $dtPlanMin = array_reduce($dtPlanMin, function($carry, $item) {
                            $carry[$item['productions_plan_id'].'__'.$item['product_id']] = $item;
                            return $carry;
                        });
                    }

                    //transfer
                    $this->db->select('
                        tbltransfer_warehouse.productions_capacity_id as productions_capacity_id,
                        group_concat(distinct DATE_FORMAT(tbltransfer_warehouse.date, "%d/%m/%Y") SEPARATOR "\n") as date,
                        group_concat(distinct IF (tbl_materials.code IS NOT NULL, tbl_materials.code, tbl_products.code) SEPARATOR "\n") as item_code,
                        group_concat(distinct IF (tbl_materials.name IS NOT NULL, tbl_materials.name, tbl_products.name) SEPARATOR "\n") as item_name,
                    ', false);
                    $this->db->from('tbltransfer_warehouse');
                    $this->db->join('tbltransfer_warehouse_detail', 'tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id', 'inner');
                    $this->db->join('tbl_products', 'tbl_products.id = tbltransfer_warehouse_detail.id_items AND tbltransfer_warehouse_detail.type = "product"', 'left');
                    $this->db->join('tbl_materials', 'tbl_materials.id = tbltransfer_warehouse_detail.id_items AND tbltransfer_warehouse_detail.type = "nvl"', 'left');
                    $this->db->where_in('tbltransfer_warehouse.productions_capacity_id', $arrPlanId, false);
                    $this->db->group_by('tbltransfer_warehouse.productions_capacity_id');
                    $transfer_warehouse = $this->db->get()->result_array();
                    if (!empty($transfer_warehouse)) {
                        $transfer_warehouse = array_reduce($transfer_warehouse, function($carry, $item) {
                            $carry[$item['productions_capacity_id']] = $item;
                            return $carry;
                        });
                    }
                }

                //list dp
                if (!empty($arrProductId) && !empty($arrPOId)) {
                    $this->db->select('
                        productions_orders_id,
                        items_id,
                        SUM(quantity) as quantity
                    ');
                    $this->db->from('tbl_productions_orders_items');
                    $this->db->where_in('productions_orders_id', $arrPOId, false);
                    $this->db->where_in('items_id', $arrProductId, false);
                    $this->db->where('object_item_type', 'business_plan');
                    $this->db->group_by('productions_orders_id, items_id');
                    $listQuantityDp = $this->db->get()->result_array();
                    if (!empty($listQuantityDp)) {
                        $listQuantityDp = array_reduce($listQuantityDp, function($carry, $item) {
                            $carry[$item['productions_orders_id'].'__'.$item['items_id']] = $item;
                            return $carry;
                        });
                    }
                }

                //transfers
                if (!empty($arrProductId)) {
                    $this->db->select('
                        tbltransfer_warehouse_detail.id_items,
                        SUM(quantity_net) as quantity
                    ');
                    $this->db->from('tbltransfer_warehouse');
                    $this->db->join('tbltransfer_warehouse_detail','tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id');
                    $this->db->where_in('tbltransfer_warehouse_detail.id_items', $arrProductId);
                    $this->db->where('tbltransfer_warehouse.order_id_new != ',0);
                    $this->db->where('tbltransfer_warehouse_detail.type','product');
                    $this->db->group_by('tbltransfer_warehouse_detail.id_items');
                    $listQuantityHold = $this->db->get()->result_array();
                    if (!empty($listQuantityHold)) {
                        $listQuantityHold = array_reduce($listQuantityHold, function($carry, $item) {
                            $carry[$item['id_items']] = $item;
                            return $carry;
                        });
                    }
                }
                

				$start = 0;
                $group_id = 0;
				foreach ($productions_orders as $key => $aRow) {
					$iExcel = -1;
					$start++;
					$rowBegin++;
                    $productions_orders_id = $aRow['id'];
					$items_id = $aRow['items_id'];
					$plan_id = $aRow['plan_id'];
                    $location_id = $aRow['location_id'];

					$product = $dtProducts[$items_id] ?? null;
                    $image = $product['images'] ?? null;
                    $plan = $dtPlanMin[$plan_id.'__'.$items_id] ?? null;
                    $date_delivery = !empty($plan['date_shipping']) ? _d($plan['date_shipping']) : '';
                    $branch = $listBranch[$location_id] ?? null;

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
                    $date_export = !empty($_query['date_active']) ? _d($_query['date_active']) : '';

                    $tbPurchasesErrors = "(
                        SELECT
                            tbl_purchase_products.productions_orders_details_id as productions_orders_details_id,
                            SUM(tbl_purchase_products.total_quantity) as quantity_errors
                        FROM tbl_purchase_products
                        WHERE tbl_purchase_products.is_errors = 1 AND tbl_purchase_products.productions_orders_details_id IN ($_pod_id)
                    )";
                    $_query = $this->db->query($tbPurchasesErrors)->row_array();
                    $aRow['quantity_errors'] = !empty($_query['quantity_errors']) ? $_query['quantity_errors'] : 0;

                    //transfer
                    $dtTransferWarehouse = $transfer_warehouse[$plan_id] ?? null;
                    $strDateItem = $dtTransferWarehouse['date'] ?? '';
                    $strItem = $dtTransferWarehouse['item_code'] ?? '';

                    $this->db->select('
                        tbl_products.code as product_code,
                        tbl_products.name as product_name,
                        GROUP_CONCAT(DISTINCT ppb_materials.landscape_print_size SEPARATOR "\n") as landscape_print_size,
                        SUM(ppb_materials.paper_exchange) as paper_exchange,
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
                    $dtQuantityNew = $this->db->get()->row_array();

                    //dp
                    $index_dp = $productions_orders_id.'__'.$items_id;
                    $dtListQuantityDp = $listQuantityDp[$index_dp] ?? null;
                    $quantityDp = $dtListQuantityDp['quantity'] ?? 0;

                    //transfer
                    $dtQuantityHold = $listQuantityHold[$items_id] ?? null;
                    $quantityHold = $dtQuantityHold['quantity'] ?? 0;

                    //
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

                    $flagGroup = false;
                    if ($group_id != $productions_orders_id) {
                        $group_id = $productions_orders_id;
                        $flagGroup = true;
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

                            // $quantity = roundNumberFormat($vB['quantity'], 0);
                            $quantity = ceil(round($vB['quantity'], 3));
                            $quantity_single = $vB['quantity_single'];
                            $quantity_need = $quantity + $quantity_compensation;
                            // $paper_exchange = $quantity_single > 0 ? roundNumberFormat($quantity_need/$quantity_single, 0) : 0;
                            $paper_exchange = $quantity_single > 0 ? ceil($quantity_need/$quantity_single) : 0;
                            $total_paper_exchange+= $paper_exchange;
                        }
                    }
                    $quantityNew = $total_paper_exchange; 
                    // $aRow['quantity_errors'] = 0;
                    // $status_new = 0;
                    // if ($aRow['quantity'] <= (float)$aRow['quantity_finished']) {
                    //     $status_new = 1;
                    // }
                    // $aRow['status_new'] = $status_new;
                    if ($aRow['quantity'] <= (float)$aRow['quantity_errors'] + (float)$aRow['quantity_finished']) {
                        $aRow['status_new'] = 1;
                    }

                    $status_new = '';
                    if ($aRow['status_new'] == 1){
                        $status_new = 'Hoàn thành';
                    } else {
                        if ($aRow['quantity_finished'] + $aRow['quantity_errors'] > 0){
                            $status_new = 'Đang sản xuất';
                        } else {
                            $status_new = 'Chưa sản xuất';
                        }
                    }

                    $arrProcess = [];
                    $this->db->select('
                        tbl_productions_orders_items_stages.id as id,
                        tbl_productions_orders_items_stages.stage_id as stage_id,
                        '.$items_id.' as item_id,
                        tbl_stages.name as stage_name,
                        tbl_category_stages.is_in as is_in,
                        tbl_category_stages.name as name_category_stage,
                    ', false);
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                    $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages','left');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_id', $productions_orders_id);
                    $this->db->order_by('tbl_productions_orders_items_stages.number ASC');
                    $process = $this->db->get()->result_array();
                    foreach ($process as $kk => $vv){
                        $checkKey = $vv['stage_id'].'__'.$vv['item_id'];
                        if (empty($arrProcess[$checkKey])){
                            $arrProcess[$checkKey] = $vv;
                        }
                    }

                    $htmlProcess = '';
                    $htmlProcessin = '';
                    if (!empty($arrProcess)){
                        $iKey = 1;
                        foreach ($arrProcess as $k => $v){
                            if($v['is_in'] == 1){
                                $htmlProcessin .= $v['name_category_stage'].",\n";
                            }
                            $htmlProcess .= $v['stage_name']."\n";
                            $iKey++;
                        }
                    }

					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $start);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, date_format(date_create($aRow['date']), 'd/m/Y'));
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, date_format(date_create($aRow['date_created']), 'd/m/Y'));
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $aRow['reference_no']);
                    $images = get_upload_path_by_type('products') . $image;
                    if (!empty($image) && file_exists($images)) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName($image);
                        $objDrawing->setDescription('Image');
                        $objDrawing->setPath($images);
                        list($originalWidth, $originalHeight) = getimagesize($images);
                        $maxWidth = 30;  // Chiều rộng tối đa của ô
                        $maxHeight = 30; // Chiều cao tối đa của ô
                        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                        $scaledWidth = $originalWidth * $scale;
                        $scaledHeight = $originalHeight * $scale;
                        $objDrawing->setWidth($scaledWidth);
                        $objDrawing->setHeight($scaledHeight);
                        $offsetX = ($maxWidth - $scaledWidth) / 2;
                        $offsetY = ($maxHeight - $scaledHeight) / 2;
                        $objDrawing->setOffsetX($offsetX + 2);
                        $objDrawing->setOffsetY($offsetY + 2);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(30);
                        $objDrawing->setCoordinates($excel[++$iExcel].$rowBegin);
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, '');
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $product['code'] ?? '');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $product['name'] ?? '');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $date_delivery);
                    // $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $date_export);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $strDateItem);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $strItem); //các npl đã xuất
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $dtQuantityNew['landscape_print_size'] ?? '');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity'] - $quantityDp));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_sx']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($quantityHold));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($quantityNew, 0));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_finished']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_errors']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $status_new);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $htmlProcessin);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $htmlProcess);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $aRow['note']);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $branch['name'] ?? '');


				}
			}
            
            // print_arrays(123);
			$objPHPExcel->getActiveSheet()->getStyle("".$excel[0]."6:".$excel[$iExcelEnd]."$rowBegin")->applyFromArray([
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
			])->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("".$excel[0]."6:".$excel[$iExcelEnd]."$rowBegin")->getAlignment()->setWrapText(true); 

			$filename = lang('lenhsanxuattong') . '.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

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

    public function exportExcelPOTotalDetail() {
        if ($this->input->post('export_excel')) {
			ini_set('memory_limit', '3500M');
			include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
			$this->load->library('PHPExcel');

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
			$objPHPExcel->getDefaultStyle()->applyFromArray([
				'font' => array(
					'name'  => 'Times New Roman'
				),
			]);

			insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

			$excel = cloumns_excel();
			$objPHPExcel->getActiveSheet()->setCellValue(
				'A5',
				('LỆNH SẢN XUẤT CHI TIẾT')
			)->getStyle("A5")->applyFromArray([
				'font' => array(
					'bold' => true,
					'size' => 16,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			]);
			$objPHPExcel->getActiveSheet()->mergeCells('A5:L5');

			$rowBegin = 6;
			$iExcel = -1;
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'STT');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ngày mở đơn');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lệnh sản xuất tổng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Kế hoạch NPL');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Ghi chú');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Chi nhánh xưởng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Hình ảnh');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Mã thành phẩm');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Tên thành phẩm');
            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Đơn hàng bán/Kế hoạch BTP');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Khách hàng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Số lượng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Trạng thái');

			
			
			$iExcelEnd = $iExcel;
			$objPHPExcel->getActiveSheet()->getStyle($excel[0].($rowBegin - 1).':'.$excel[$iExcel].($rowBegin))->applyFromArray([
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'font' => array(
					'bold' => true,
				),
			]);

            $branch_staff = get_array_branch_staff();
            $is_admin = is_admin();

            $status_table = $this->input->post('status_table');
            $productions_orders_search = $this->input->post('productions_orders_search');
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $orders_search = $this->input->post('orders_search');
            $type_print_search = $this->input->post('type_print_search');
            $business_plan_search = $this->input->post('business_plan_search');
            $orders_and_business_plan = $this->input->post('orders_and_business_plan');
            $customer_search = $this->input->post('customer_search');
            $branch_search = $this->input->post('branch_search');
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

			$aColumns = [
                'tbl_productions_orders.id as id',
                'tbl_productions_orders.date as date',
                'tbl_productions_orders.reference_no as reference_no',
                'tbl_productions_orders.note as note',
                'tbl_productions_orders.location_id as location_id'
            ];

			$where = [];
			if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search). ' 00:00:00';
                array_push($where, "AND tbl_productions_orders.date >= '$start_date_search'");
            }
    
            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search). ' 23:59:59';
                array_push($where, "AND tbl_productions_orders.date <= '$end_date_search'");
            }
    
            if (!empty($customer_search)) {
                array_push($where, "AND EXISTS (
                     SELECT tbl_productions_orders_details.productions_orders_id
                     FROM tbl_productions_orders_details
                     JOIN tbl_orders ON tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = 'orders'
                     WHERE tbl_productions_orders_details.productions_orders_id = tbl_productions_orders.id
                     AND tbl_orders.customer_id = $customer_search
                 )");
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

            if (!has_permission('manufactures_productions_orders', '', 'view')) {
                array_push($where, "AND tbl_productions_orders.created_by =".get_staff_user_id());
            }
    
            if (!$is_admin) {
                if (empty($branch_staff)) $branch_staff = [0];
                array_push($where, 'AND tbl_productions_orders.location_id IN ('.implode(',', $branch_staff).')');
            }
            if (!empty($branch_search)) {
                array_push($where, "AND tbl_productions_orders.location_id =".$branch_search);
            }

			$where = stringWhere($where);
			$this->db->select(implode(',', $aColumns), false);
			$this->db->from('tbl_productions_orders');
			$this->db->where($where, false, false);
            $this->db->order_by('tbl_productions_orders.id DESC');
			$productions_orders = $this->db->get()->result_array();

			if (!empty($productions_orders)) {
				$arrPOId = [];
                $arrLocationId = [];
				foreach ($productions_orders as $key => $value) {
					$arrPOId[] = $value['id'];
					$arrLocationId[] = $value['location_id'];
				}

				if (!empty($arrPOId)) {

                    $arrOrderItemsId = [];
                    $arrBusinessItemsId = [];

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
                        tbl_productions_orders_items.versions_stage as versions_stage,
                        tbl_productions_plan.reference_no as reference_no_plan
                    ');
                    $this->db->from('tbl_productions_orders_items');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
                    $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_orders_items.plan_id');
                    $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id', 'left');
                    $this->db->where_in('tbl_productions_orders_items.productions_orders_id', $arrPOId, false);
                    $listProductionsOrdersItems = $this->db->get()->result_array();

                    $listProductionsOrdersItems = array_reduce($listProductionsOrdersItems, function ($carry, $item) use (&$arrOrderItemsId, &$arrBusinessItemsId) {
                        $object_item_type = $item['object_item_type'];
                        $object_item_id = $item['production_plan_item_id'];
                        if ($object_item_type == 'orders') {
                            $arrOrderItemsId[] = $object_item_id;
                        } else if ($object_item_type == 'business_plan') {
                            $arrBusinessItemsId[] = $object_item_id;
                        }

                        $carry[$item['productions_orders_id']][] = $item;
                        return $carry;
                    });

                    //process
                    $this->db->select('
                        tbl_productions_orders_items_stages.productions_orders_items_id as poi_id,
                        tbl_productions_orders_items_stages.active as active,
                        tbl_productions_orders_items_stages.final_stage as final_stage
                    ', false);
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->where_in('tbl_productions_orders_items_stages.productions_orders_id', $arrPOId, false);
                    $process = $this->db->get()->result_array();
                    $process = array_reduce($process, function ($carry, $item) {
                        $carry[$item['poi_id']][] = $item;
                        return $carry;
                    });
				}

                if (!empty($arrLocationId)) {
                    $arrLocationId = array_unique($arrLocationId);
                    $this->db->select('tblbranch.id, tblbranch.name', false);
                    $this->db->from('tblbranch');
                    $this->db->where_in('tblbranch.id', $arrLocationId);
                    $listBranch = $this->db->get()->result_array();
                    $listBranch = array_reduce($listBranch, function ($carry, $item) {
                        $carry[$item['id']] = $item;
                        return $carry;
                    });
                }

                if (!empty($arrOrderItemsId)) {
                    $arrOrderItemsId = array_unique($arrOrderItemsId);
                    $this->db->select('
                        tbl_order_items.id as id,
                        tbl_orders.reference_no as reference_no,
                        tblclients.company as company,
                        tbl_order_items.note_item as note_item,
                        tbl_order_item_shippings.date_shipping as date_shipping
                    ');
                    $this->db->from('tbl_order_items');
                    $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
                    $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id');
                    $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
                    $this->db->where_in('tbl_order_items.id', $arrOrderItemsId, false);
                    $list_order_items = $this->db->get()->result_array();
                    if (!empty($list_order_items)) {
                        $list_order_items = array_reduce($list_order_items, function($carry, $item) {
                            $carry[$item['id']] = $item;
                            return $carry;
                        });
                    }
                }
    
                if (!empty($arrBusinessItemsId)) {
                    $arrBusinessItemsId = array_unique($arrBusinessItemsId);
                    $this->db->select('
                        tbl_business_plan_items.id as id,
                        tbl_business_plan.reference_no as reference_no,
                        tbl_business_plan_items.note_items as note_item,
                        tbl_business_plan_items_date.date as date_shipping,
                    ');
                    $this->db->from('tbl_business_plan_items');
                    $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_business_plan_items.business_plan_id');
                    $this->db->join('tbl_business_plan_items_date', 'tbl_business_plan_items_date.business_plan_items_id = tbl_business_plan_items.id');
                    $this->db->where_in('tbl_business_plan_items.id', $arrBusinessItemsId, false);
                    $list_business_plan_items = $this->db->get()->result_array();
                    if (!empty($list_business_plan_items)) {
                        $list_business_plan_items = array_reduce($list_business_plan_items, function($carry, $item) {
                            $carry[$item['id']] = $item;
                            return $carry;
                        });
                    }
                }

				$start = 0;
                // print_arrays($productions_orders);
				foreach ($productions_orders as $key => $aRow) {
					$po_id = $aRow['id'];
                    $location_id = $aRow['location_id'];

					$productionsOrdersItems = $listProductionsOrdersItems[$po_id] ?? null;
					$branch = $listBranch[$location_id] ?? null;

                    if (!empty($productionsOrdersItems)) {
                        foreach ($productionsOrdersItems as $k => $v) {
					        $iExcel = -1;
					        $start++;
                            $rowBegin++;
                            $production_plan_item_id = $v['production_plan_item_id'];
                            $object_item_type = $v['object_item_type'];
                            $reference_no_object = '';
                            $company = '';
                            if ($object_item_type == "orders") {
                                $order = $list_order_items[$production_plan_item_id] ?? null;
                                if (!empty($order)) {
                                    $reference_no_object = $order['reference_no'];
                                    $company = $order['company'];
                                }
                            } else if ($object_item_type == "business_plan") {
                                $business_plan = $list_business_plan_items[$production_plan_item_id] ?? null;
                                if (!empty($business_plan)) {
                                    $reference_no_object = $business_plan['reference_no'];
                                }
                            }

                            $dtProcess = $process[$v['id']] ?? null;
                            $strProcess = 'Chưa thực hiện sản xuất';
                            if (!empty($dtProcess)) {
                                $arrayEnd = end($dtProcess);
                                if (!empty($arrayEnd['active'])) {
                                    $strProcess = 'Hoàn thành';
                                } else {
                                    foreach ($dtProcess as $p) {
                                        if ($p['active'] == 1) {
                                            $strProcess = 'Đang sản xuất';
                                            break;
                                        }
                                    }
                                }
                            }

                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $start);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, _dt($aRow['date']));
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $aRow['reference_no']);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $v['reference_no_plan']);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $aRow['note']);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $branch['name'] ?? '');

                            $images = get_upload_path_by_type('products') . $v['images'];
                            if (!empty($v['images']) && file_exists($images)) {
                                $objDrawing = new PHPExcel_Worksheet_Drawing();
                                $objDrawing->setName($v['images']);
                                $objDrawing->setDescription('Image');
                                $objDrawing->setPath($images);
                                list($originalWidth, $originalHeight) = getimagesize($images);
                                $maxWidth = 30;  // Chiều rộng tối đa của ô
                                $maxHeight = 30; // Chiều cao tối đa của ô
                                $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                                $scaledWidth = $originalWidth * $scale;
                                $scaledHeight = $originalHeight * $scale;
                                $objDrawing->setWidth($scaledWidth);
                                $objDrawing->setHeight($scaledHeight);
                                $offsetX = ($maxWidth - $scaledWidth) / 2;
                                $offsetY = ($maxHeight - $scaledHeight) / 2;
                                $objDrawing->setOffsetX($offsetX + 2);
                                $objDrawing->setOffsetY($offsetY + 2);
                                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                                $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(30);
                                $objDrawing->setCoordinates($excel[++$iExcel].$rowBegin);
                            } else {
                                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, '');
                            }

                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $v['item_code']);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $v['item_name']);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $reference_no_object);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $company);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $v['quantity']);
                            $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $strProcess);
                        }
                    }
				}
			}

			$objPHPExcel->getActiveSheet()->getStyle("".$excel[0]."6:".$excel[$iExcelEnd]."$rowBegin")->applyFromArray([
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
			])->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("".$excel[0]."6:".$excel[$iExcelEnd]."$rowBegin")->getAlignment()->setWrapText(true); 

			$filename = lang('lenhsanxuattong') . '.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

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

    public function exportExcelPlan() {
        if ($this->input->post('export_excel')) {
			ini_set('memory_limit', '3500M');
			include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
			$this->load->library('PHPExcel');
            $this->perViewProductionsOrders = has_permission('manufactures_productions_orders', '', 'view');
            if (!$this->perViewProductionsOrders) {
                $response = array(
					'result' => 0,
					'message' => lang('Từ chối truy cập'),
				);
				die(json_encode($response));
            }

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
			$objPHPExcel->getDefaultStyle()->applyFromArray([
				'font' => array(
					'name'  => 'Times New Roman'
				),
			]);

			insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

			$excel = cloumns_excel();
			$objPHPExcel->getActiveSheet()->setCellValue(
				'A5',
				('KẾ HOẠCH SẢN XUẤT')
			)->getStyle("A5")->applyFromArray([
				'font' => array(
					'bold' => true,
					'size' => 16,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			]);
			$objPHPExcel->getActiveSheet()->mergeCells('A5:L5');

			$rowBegin = 6;
			$iExcel = -1;
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'STT');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Hình ảnh');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Mã SP');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Tên SP');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'Đơn hàng chi tiết');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL đơn hàng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL đang SX theo đơn');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL đang SX dự trụ');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL hàng sẳn trong kho');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL cần sản xuất');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL tồn tổng');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL tồn thực tế');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL tồn cho phép');
			$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin . '', 'SL tồn đã mở lệnh(ngày SX)');
			
			
			$iExcelEnd = $iExcel;
			$objPHPExcel->getActiveSheet()->getStyle($excel[0].($rowBegin - 1).':'.$excel[$iExcel].($rowBegin))->applyFromArray([
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
				'font' => array(
					'bold' => true,
				),
			]);

            $dateNow = date('Y-m-d');
            $is_type = 2;
            $this->db->simple_query('SET SESSION group_concat_max_len=18446744073709551615');

            $branch_staff = get_array_branch_staff();
            $is_admin = is_admin();

            $customer_search_manufactures = $this->input->post('customer_search');
            $start_date_search_manufactures = $this->input->post('start_date_search');
            $end_date_search_manufactures = $this->input->post('end_date_search');
            $products_search_manufactures = $this->input->post('products_search');
            $category_product_search_manufactures = $this->input->post('category_product_search');
            $type_orders_search_manufactures = $this->input->post('type_orders_search');
            $search_date_order_manufactures = $this->input->post('search_date_order');
            $type_view_search_manufactures = $this->input->post('type_view_search');
            $products_text_search_manufactures = $this->input->post('products_text_search');

            if (empty($start_date_search_manufactures) || empty($end_date_search_manufactures)) {
				$response = array(
					'result' => 0,
					'message' => lang('Vui lòng lọc ngày bắt đầu và kết thúc'),
				);
				die(json_encode($response));
			}

            $start_date_search_manufactures = to_sql_date($start_date_search_manufactures);
            $end_date_search_manufactures = to_sql_date($end_date_search_manufactures);

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

            $slKeep = "COALESCE((
                SELECT SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
                FROM tbltransfer_warehouse_detail
                WHERE tbltransfer_warehouse_detail.order_id_item = tbl_order_items.id AND tbltransfer_warehouse_detail.tranfer_business_item_id = 0
            ), 0)";

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
    
            $tbOrdersItems = "(
                SELECT
                    tb_items.item_id as item_id,
                    SUM(tb_items.total_quantity_item) as total_quantity_item,
                    SUM(tb_items.quantity_plan) as quantity_plan,
                    GROUP_CONCAT(tb_items.order_detail SEPARATOR 'AAA') as order_detail,
                    SUM(tb_items.quantity_keep_trans) as quantity_keep_trans
                FROM (
                    (
                        SELECT
                            tbl_order_items.item_id as item_id,
                            SUM(IF((tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - COALESCE(tb_keep.quantity_net, 0) - COALESCE(tb_keep_trans.quantity, 0) > 0, (tbl_order_items.total_quantity_item * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_items.quantity_plan - COALESCE(tb_keep.quantity_net, 0) - COALESCE(tb_keep_trans.quantity, 0), 0)) as total_quantity_item,
    
                            SUM(tbl_order_items.quantity_plan) as quantity_plan,
                            GROUP_CONCAT(tbl_orders.reference_no,'|||',((tbl_order_item_shippings.quantity_shipping * IF (tbl_products.unit_id = tbl_order_items.unit_id, tbl_products.conversion_quantity_unit, 1)) - tbl_order_item_shippings.quantity_plan_item - (COALESCE(tb_keep.quantity_net, 0)) - COALESCE(tb_keep_trans.quantity, 0))  SEPARATOR 'FF') as order_detail,
                            SUM(COALESCE(tb_keep_trans.quantity, 0)) as quantity_keep_trans
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
                            GROUP_CONCAT(tbl_business_plan.reference_no,'|||',(tbl_business_plan_items_date.quantity - tbl_business_plan_items_date.quantity_plan_item)  SEPARATOR 'FF') as order_detail,
                            0 as quantity_keep_trans
                        FROM tbl_business_plan
                        INNER JOIN tbl_business_plan_items ON tbl_business_plan.id = tbl_business_plan_items.business_plan_id
                        INNER JOIN tbl_business_plan_items_date ON tbl_business_plan_items.id = tbl_business_plan_items_date.business_plan_items_id
                        WHERE tbl_business_plan_items.quantity > 0 AND tbl_business_plan.productions_plan_preventive_id = 0 AND tbl_business_plan_items_date.date >= '$start_date_search_manufactures' AND tbl_business_plan_items_date.date <= '$end_date_search_manufactures' AND tbl_business_plan.status = 'approved' $whereBusinessPlan
                        GROUP BY tbl_business_plan_items.items_id
                    )
                ) tb_items
                GROUP BY tb_items.item_id
            ) tb_sum_items";

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

            if ($is_type == 2) {
                $quantityW = "(
                    SELECT
                        0 as id_items,
                        0 as quantity_warehouse
                ) tb_quantity_warehouse";
    
                $tbPreventive = "(
                    SELECT
                        0 as item_id,
                        0 as total_quantity_item
                ) tb_perventive";
    
                $tbProductionsOrdersDetailNotPreventive = '(
                    SELECT
                        0 as items_id,
                        0 as quantity_po
                ) tb_productions_orders_detail';
    
                $tbProductionsOrdersDetailPreventive = '(
                    SELECT
                        0 as items_id,
                        0 as quantity_po
                ) tb_productions_orders_detail_perventive';
            }
            
			$aColumns = [
                'tbl_products.id as id',
                'tbl_products.images as images',
                'tbl_products.code as code',
                'tbl_products.name as name',
                '0 as actions',
                'tb_sum_items.order_detail as order_detail',
                'tb_sum_items.total_quantity_item as quantity_orders',
                'tb_sum_items.quantity_plan - COALESCE(tb_productions_orders_detail.quantity_po, 0) as quantity_manufactures',
                'IF(COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0, COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0), 0) as quantity_preventive',
                'tb_quantity_warehouse.quantity_warehouse as quantity_warehouses',
                '(tb_sum_items.total_quantity_item - (IF(COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0) > 0, COALESCE(tb_perventive.total_quantity_item, 0) - COALESCE(tb_productions_orders_detail_perventive.quantity_po, 0), 0)) - coalesce(tb_quantity_warehouse.quantity_warehouse, 0)) as quantity_need_manufactures',
                '0 as ton_tong',
                '0 as ton_thuc_te',
                'tbl_products.allowable as ton_cho_phep',
                '0 as ton_da_mo_lenh',
                'tb_sum_items.quantity_plan as quantity_plan',
                'tb_sum_items.quantity_keep_trans as quantity_keep_trans'
            ];

			$where = [' AND tbl_products.type_products  IN ("products", "semi_products")'];
			if (!empty($products_search_manufactures)) {
                array_push($where, ' AND tbl_products.id = ' . $products_search_manufactures);
            }
    
            if (!empty($products_text_search_manufactures)) {
                array_push($where, ' AND (tbl_products.name like "%' . $products_text_search_manufactures . '%" OR tbl_products.code like "%' . $products_text_search_manufactures . '%")');
            }
    
            if (!empty($category_product_search_manufactures)) {
                $category_product_search_manufactures = is_array($category_product_search_manufactures) ? implode(',', $category_product_search_manufactures) : $category_product_search_manufactures;
                array_push($where, ' AND tbl_products.category_id IN (' . $category_product_search_manufactures . ')');
            }

			$where = stringWhere($where);
			$this->db->select(implode(',', $aColumns), false);
			$this->db->from('tbl_products');

            $this->db->join($tbOrdersItems, 'tb_sum_items.item_id = tbl_products.id');
            $this->db->join($quantityW, 'tb_quantity_warehouse.id_items = tbl_products.id', 'left');
            $this->db->join($tbPreventive, 'tb_perventive.item_id = tbl_products.id', 'left');
            $this->db->join($tbProductionsOrdersDetailNotPreventive, 'tb_productions_orders_detail.items_id = tbl_products.id', 'left');
            $this->db->join($tbProductionsOrdersDetailPreventive, 'tb_productions_orders_detail_perventive.items_id = tbl_products.id', 'left');

			$this->db->where($where, false, false);
            $this->db->order_by('quantity_need_manufactures DESC');
			$rResult = $this->db->get()->result_array();

			if (!empty($rResult)) {
				if (!empty($rResult) && $is_type == 2) {
                    $arrProductId = array_column($rResult, 'id');
                    if (!empty($arrProductId)) {
                        $arrProductId = array_unique($arrProductId);
        
                        $quantityW = "
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
                            AND tblwarehouse_items.id_items IN (".implode(',', $arrProductId).")
                            GROUP BY tblwarehouse_items.id_items
                        ";
                        $listQuantityW = $this->db->query($quantityW)->result_array();
                        if (!empty($listQuantityW)) {
                            $listQuantityW = array_reduce($listQuantityW, function($carry, $item) {
                                $carry[$item['id_items']] = $item;
                                return $carry;
                            });
                        }
        
                        //
                        $tbPreventive = "
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
                            AND tbl_business_plan_items.items_id IN (".implode(',', $arrProductId).")
                            GROUP BY tbl_business_plan_items.items_id
                        ";
                        $listPreventive = $this->db->query($tbPreventive)->result_array();
                        if (!empty($listPreventive)) {
                            $listPreventive = array_reduce($listPreventive, function($carry, $item) {
                                $carry[$item['item_id']] = $item;
                                return $carry;
                            });
                        }
        
                        //
                        $tbProductionsOrdersDetailNotPreventive = '
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
                            AND tbl_productions_orders_items.items_id IN ('.implode(",", $arrProductId).')
                            GROUP BY tbl_productions_orders_items.items_id
                        ';
                        $listPONotPreventive = $this->db->query($tbProductionsOrdersDetailNotPreventive)->result_array();
                        if (!empty($listPONotPreventive)) {
                            $listPONotPreventive = array_reduce($listPONotPreventive, function($carry, $item) {
                                $carry[$item['items_id']] = $item;
                                return $carry;
                            });
                        }
                
                        $tbProductionsOrdersDetailPreventive = '
                            SELECT
                                tbl_productions_orders_items.items_id as items_id,
                                SUM(tbl_purchase_products.total_quantity) as quantity_po
                            FROM tbl_productions_orders_details
                            INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id
                            INNER JOIN  tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_orders_details.object_id
                            WHERE tbl_productions_orders_details.object_type = "business_plan" AND tbl_business_plan.productions_plan_preventive_id != 0 AND (tbl_purchase_products.final_stage = 1 OR tbl_purchase_products.is_errors = 1) AND tbl_purchase_products.warehouseman_id > 0
                            AND tbl_productions_orders_items.items_id IN ('.implode(",", $arrProductId).')
                            GROUP BY tbl_productions_orders_items.items_id 
                        ';
                        $listPOPreventive = $this->db->query($tbProductionsOrdersDetailPreventive)->result_array();
                        if (!empty($listPOPreventive)) {
                            $listPOPreventive = array_reduce($listPOPreventive, function($carry, $item) {
                                $carry[$item['items_id']] = $item;
                                return $carry;
                            });
                        }
        
                        //tồn thực tế
                        $quantityWTon = "
                            SELECT
                                tblwarehouse_items.id_items as id_items,
                                SUM(tblwarehouse_items.product_quantity) as quantity_warehouse
                            FROM tblwarehouse_items
                            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                            WHERE tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.warehouse_id NOT IN (" . WAREHOUSES_HOLD . ") AND tbllocaltion_warehouses.stage_id = 0 
                            AND tblwarehouse_items.id_items IN (".implode(',', $arrProductId).")
                            GROUP BY tblwarehouse_items.id_items
                        ";
                        $listQuantityWTon = $this->db->query($quantityWTon)->result_array();
                        if (!empty($listQuantityWTon)) {
                            $listQuantityWTon = array_reduce($listQuantityWTon, function($carry, $item) {
                                $carry[$item['id_items']] = $item;
                                return $carry;
                            });
                        }
                    }
                }

				$start = 0;
				foreach ($rResult as $key => $aRow) {
					$iExcel = -1;
					$start++;
					$rowBegin++;
                    $product_id = $aRow['id'];

                    if ($is_type == 2) {
                        $dtQuantityW = $listQuantityW[$product_id] ?? null;
                        $dtPreventive = $listPreventive[$product_id] ?? null;
                        $dtPONotPreventive = $listPONotPreventive[$product_id] ?? null;
                        $dtPOPreventive = $listPOPreventive[$product_id] ?? null;
        
                        $_QuantityW = $dtQuantityW['quantity_warehouse'] ?? 0;
                        $_Preventive = $dtPreventive['total_quantity_item'] ?? 0;
                        $_PONotPreventive = $dtPONotPreventive['quantity_po'] ?? 0;
                        $_POPreventive = $dtPOPreventive['quantity_po'] ?? 0;
        
                        $quantity_plan = $aRow['quantity_plan'];
                        $quantity_orders = $aRow['quantity_orders'];
        
                        $quantity_manufactures = $quantity_plan - $_PONotPreventive;
                        $quantity_preventive = ($_Preventive - $_POPreventive) > 0 ? $_Preventive - $_POPreventive : 0;
                        $quantity_warehouses = $_QuantityW;
                        $quantity_need_manufactures = $quantity_orders - $quantity_preventive - $quantity_warehouses;
        
                        $aRow['quantity_manufactures'] = $quantity_manufactures;
                        $aRow['quantity_preventive'] = $quantity_preventive;
                        $aRow['quantity_warehouses'] = $quantity_warehouses;
                        $aRow['quantity_need_manufactures'] = $quantity_need_manufactures;
                    }

                    $dtQuantityWTon = $listQuantityWTon[$product_id] ?? null;
                    $aRow['ton_thuc_te'] = $dtQuantityWTon['quantity_warehouse'] ?? 0;
                    $aRow['ton_da_mo_lenh'] = $aRow['quantity_keep_trans'] ?? 0;
                    $aRow['ton_tong'] = $aRow['ton_thuc_te'] + $aRow['ton_da_mo_lenh'];
                    if ($aRow['quantity_manufactures'] < 0) $aRow['quantity_manufactures'] = 0;

					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $start);
                    $image = $aRow['images'];
                    $images = get_upload_path_by_type('products') . $image;
                    if (!empty($image) && file_exists($images)) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName($image);
                        $objDrawing->setDescription('Image');
                        $objDrawing->setPath($images);
                        list($originalWidth, $originalHeight) = getimagesize($images);
                        $maxWidth = 30;  // Chiều rộng tối đa của ô
                        $maxHeight = 30; // Chiều cao tối đa của ô
                        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                        $scaledWidth = $originalWidth * $scale;
                        $scaledHeight = $originalHeight * $scale;
                        $objDrawing->setWidth($scaledWidth);
                        $objDrawing->setHeight($scaledHeight);
                        $offsetX = ($maxWidth - $scaledWidth) / 2;
                        $offsetY = ($maxHeight - $scaledHeight) / 2;
                        $objDrawing->setOffsetX($offsetX + 2);
                        $objDrawing->setOffsetY($offsetY + 2);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                        $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(30);
                        $objDrawing->setCoordinates($excel[++$iExcel].$rowBegin);
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, '');
                    }

					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $aRow['code']);
					$objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $aRow['name']);
                    $order_detail = explode('AAA', trim($aRow['order_detail'], 'AAA'));
                    $order_detail_text = '';
                    foreach ($order_detail as $k => $v) {
                        $v = explode('FF', $v);
                        foreach ($v as $k1 => $v1) {
                            $v1 = explode('|||', $v1);
                            if ($v1[1] <= 0) continue;
                            $order_detail_text .= "- " . $v1[0] . " (" . formatNumber($v1[1]) . ")\n";
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, $order_detail_text);
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_orders']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_manufactures']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_preventive']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['quantity_warehouses']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, ($aRow['quantity_need_manufactures'] > 0 ? formatNumber($aRow['quantity_need_manufactures']) : ''));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['ton_tong']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['ton_thuc_te']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['ton_cho_phep']));
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel]. $rowBegin, formatNumber($aRow['ton_da_mo_lenh']));
				}
			}
            
			$objPHPExcel->getActiveSheet()->getStyle("".$excel[0]."6:".$excel[$iExcelEnd]."$rowBegin")->applyFromArray([
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				),
			])->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle("".$excel[0]."6:".$excel[$iExcelEnd]."$rowBegin")->getAlignment()->setWrapText(true); 

			$filename = lang('kehoachsanxuat') . '.xls';
			$objPHPExcel->getActiveSheet()->freezePane('A1');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

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

    public function moderation_criteria($po_id) {
        $production_order = $this->manufactures_model->rowProductionsOrdersById($po_id);
        if ($this->input->post('save')) {
            $is_ptm = $this->input->post('is_ptm') ? 1 : 0;
            $is_color = $this->input->post('is_color') ? 1 : 0;
            $is_layout = $this->input->post('is_layout') ? 1 : 0;
            $is_sewing = $this->input->post('is_sewing') ? 1 : 0;
            $is_npl = $this->input->post('is_npl') ? 1 : 0;
            $is_material = $this->input->post('is_material') ? 1 : 0;
            $is_cutting = $this->input->post('is_cutting') ? 1 : 0;
            $date_npl = !empty($this->input->post('date_npl')) ? to_sql_date($this->input->post('date_npl')) : null;
            $is_number_printed = $this->input->post('is_number_printed') ? 1 : 0;

            $option = [
                'is_ptm' => $is_ptm,
                'is_color' => $is_color,
                'is_layout' => $is_layout,
                'is_sewing' => $is_sewing,
                'is_npl' => $is_npl,
                'is_material' => $is_material,
                'is_cutting' => $is_cutting,
                'date_npl' => $date_npl,
                'is_number_printed' => $is_number_printed
            ];
            $this->db->where('tbl_productions_orders.id', $po_id);
            $up = $this->db->update('tbl_productions_orders', $option);
            if ($up) {
                $data['result'] = 1;
                $data['message'] = lang('Cập nhật tiêu chí thành công');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Cập nhật tiêu chí thất bại');
            }

            echo json_encode($data); return;
        } else {
            $data['title'] = "Tiêu chí công đoạn";
            $data['po_id'] = $po_id;
            $data['production_order'] = $production_order;
            $this->load->view('admin/manufactures/moderation_criteria', $data);
        }

    }
}
