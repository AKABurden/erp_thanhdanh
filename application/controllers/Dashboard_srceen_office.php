<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_srceen_office extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    function loadpage()
    {
        sendSocket(['data' => 123], [], 'update_dashboard');
    }

    function index()
    {
        $data = [];
        // Ngày giờ
        $weekdayMap = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $w = (int)date('w');
        $dateStr = $weekdayMap[$w] . ', ' . date('d/m/Y');
        $timeStr = date('g:i A');
        $link_connect_socket = get_option('link_connect_socket');

        $data = [
            'dateStr' => $dateStr,
            'timeStr' => $timeStr,
            'dbname' => APP_DB_NAME,
            'link_connect_socket' => $link_connect_socket,
        ];
        $this->load->view('admin/dashboard_srceen_office/dashboard_orchestrator', $data);
    }

    function countQuotes()
    {
        $this->db->select("
            COUNT(*) as total,
            SUM(status = 'approved') as approved,
            SUM(status = 'un_approved') as unapproved,
            SUM(order_id > 0) as has_order,
            SUM(order_id = 0 OR order_id IS NULL) as no_order
        ");
        $this->db->where(
            'tbl_quotes.date >=',
            get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $quotes = $this->db->get('tbl_quotes')->row_array();
        $stats = [
            'total' => (int)$quotes['total'],
            'approved' => (int)$quotes['approved'],
            'unapproved' => (int)$quotes['unapproved'],
            'has_order' => (int)$quotes['has_order'],
            'no_order' => (int)$quotes['no_order']
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function countsample()
    {
        $this->db->select("
            COUNT(*) as total,
            SUM(status = 'approved') as approved,
            SUM(status = 'unapproved') as unapproved,
            SUM(order_id > 0) as has_order,
            SUM(order_id = 0 OR order_id IS NULL) as no_order
        ");
        $this->db->where(
            'tbl_request_template.date >',
            get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00'
        );
        $this->db->join('tbl_quotes', 'tbl_quotes.id=tbl_request_template.id_quotes', 'left');
        $sample = $this->db->get('tbl_request_template')->row_array();

        $stats = [
            'total' => (int)$sample['total'],
            'approved' => (int)$sample['approved'],
            'unapproved' => (int)$sample['unapproved'],
            'has_order' => (int)$sample['has_order'],
            'no_order' => (int)$sample['no_order']
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function countorder_plan()
    {
        // Tính tổng số đơn approved, un_approved, đã giao, chưa giao
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            SUM(CASE WHEN tbl_orders.status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
            SUM(CASE WHEN tbl_orders.status = 'un_approved' THEN 1 ELSE 0 END) AS total_un_approved,
            SUM(CASE WHEN tbl_orders.count_delivery > 0 THEN 1 ELSE 0 END) AS total_delivered,
            SUM(CASE WHEN tbl_orders.count_delivery = 0 THEN 1 ELSE 0 END) AS total_not_delivered
        ");
        // $this->db->where('YEARWEEK(tbl_orders.date, 1) = YEARWEEK(CURDATE(), 1)');
        $this->db->where('tbl_orders.date >=', $date_dashboard_srceen_sales);
        $stats = $this->db->get('tbl_orders')->row_array();


        $this->db->select("
            SUM(CASE WHEN tbl_business_plan.status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
            SUM(CASE WHEN tbl_business_plan.status = 'un_approved' THEN 1 ELSE 0 END) AS total_un_approved
        ");
        // $this->db->where('YEARWEEK(tbl_orders.date, 1) = YEARWEEK(CURDATE(), 1)');
        $this->db->where('tbl_business_plan.date >=', $date_dashboard_srceen_sales);
        $business_plan = $this->db->get('tbl_business_plan')->row_array();

        $stats['business_plan_total_approved'] = $business_plan['total_approved'];
        $stats['business_plan_total_un_approved'] = $business_plan['total_un_approved'];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_open_production()
    {
        // Tính tổng số đơn approved, un_approved, đã giao, chưa giao
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.date >', $date_dashboard_srceen_sales);
        $this->db->where('NOT EXISTS (
    SELECT 1 FROM tbl_productions_orders_details 
    WHERE tbl_productions_orders_details.object_id = tbl_orders.id 
      AND tbl_productions_orders_details.object_type = "orders"
)', null, false);
        $total_no_production_order = $this->db->count_all_results();
        // Đếm đơn đã có lệnh sản xuất
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.date >', $date_dashboard_srceen_sales);
        $this->db->where('EXISTS (
    SELECT 1 FROM tbl_productions_orders_details 
    WHERE tbl_productions_orders_details.object_id = tbl_orders.id 
      AND tbl_productions_orders_details.object_type = "orders"
)', null, false);
        $total_has_production_order = $this->db->count_all_results();

        $stats['total_no_production_order'] = $total_no_production_order;
        $stats['total_has_production_order'] = $total_has_production_order;

        // $where = " AND tbl_orders.status = 'approved' AND tbl_orders.is_cancel = 0";

        // // Nếu có ngày lọc
        // $where .= ' AND tbl_orders.date >= "' . $date_dashboard_srceen_sales . '" ';

        // // Chỉ tính những đơn hàng có sản phẩm cần sản xuất thêm
        // $sql = "
        //     SELECT COUNT(DISTINCT tbl_orders.id) AS total_need_manufacture
        //     FROM tbl_orders
        //     INNER JOIN tbl_order_items 
        //         ON tbl_order_items.order_id = tbl_orders.id
        //     INNER JOIN tbl_products 
        //         ON tbl_products.id = tbl_order_items.item_id
        //     LEFT JOIN (
        //         SELECT 
        //             order_id_item,
        //             SUM(quantity_net) AS quantity_keep
        //         FROM tbltransfer_warehouse_detail
        //         WHERE tranfer_business_item_id = 0
        //         GROUP BY order_id_item
        //     ) tb_keep ON tb_keep.order_id_item = tbl_order_items.id
        //     LEFT JOIN (
        //         SELECT 
        //             order_item_id,
        //             SUM(quantity) AS quantity_transfer
        //         FROM tbl_tranfer_business_item
        //         GROUP BY order_item_id
        //     ) tb_transfer ON tb_transfer.order_item_id = tbl_order_items.id
        //     WHERE 
        //         tbl_order_items.type_item = 'products'
        //         AND tbl_order_items.total_quantity_item > 0
        //         AND tbl_orders.status = 'approved'
        //         AND tbl_orders.is_cancel = 0
        //         AND (
        //             (tbl_order_items.total_quantity_item * 
        //                 IF(tbl_products.unit_id = tbl_order_items.unit_id, 
        //                     tbl_products.conversion_quantity_unit, 1
        //                 )
        //             ) 
        //             - tbl_order_items.quantity_plan 
        //             - COALESCE(tb_keep.quantity_keep, 0)
        //             - COALESCE(tb_transfer.quantity_transfer, 0)
        //         ) > 0
        //         $where
        // ";

        // $result = $this->db->query($sql)->row_array();
        $whereOrders = " AND tbl_orders.status = 'approved' AND tbl_orders.is_cancel = 0";
        if (!empty($date_dashboard_srceen_sales)) {
            $whereOrders .= ' AND tbl_orders.date >= "' . $date_dashboard_srceen_sales . '" ';
        }

        $sqlOrders = "
    SELECT COUNT(DISTINCT tbl_orders.id) AS total_need_orders
    FROM tbl_orders
    INNER JOIN tbl_order_items 
        ON tbl_order_items.order_id = tbl_orders.id
    INNER JOIN tbl_products 
        ON tbl_products.id = tbl_order_items.item_id
    LEFT JOIN (
        SELECT 
            order_id_item,
            SUM(quantity_net) AS quantity_keep
        FROM tbltransfer_warehouse_detail
        WHERE tranfer_business_item_id = 0
        GROUP BY order_id_item
    ) tb_keep ON tb_keep.order_id_item = tbl_order_items.id
    LEFT JOIN (
        SELECT 
            order_item_id,
            SUM(quantity) AS quantity_transfer
        FROM tbl_tranfer_business_item
        GROUP BY order_item_id
    ) tb_transfer ON tb_transfer.order_item_id = tbl_order_items.id
    WHERE 
        tbl_order_items.type_item = 'products'
        AND tbl_order_items.total_quantity_item > 0
        AND (
            (tbl_order_items.total_quantity_item *
                IF(tbl_products.unit_id = tbl_order_items.unit_id, 
                    tbl_products.conversion_quantity_unit, 1
                )
            )
            - tbl_order_items.quantity_plan 
            - COALESCE(tb_keep.quantity_keep, 0)
            - COALESCE(tb_transfer.quantity_transfer, 0)
        ) > 0
        $whereOrders
        AND NOT EXISTS (
            SELECT 1 
            FROM tbl_productions_orders_details pod
            WHERE pod.object_id = tbl_orders.id
              AND pod.object_type = 'orders'
        )
";

        $resultOrders = $this->db->query($sqlOrders)->row();
        $total_need_orders = (int)($resultOrders->total_need_orders ?? 0);

        // ====== PHẦN 2: KẾ HOẠCH KINH DOANH CẦN SẢN XUẤT ======
        $whereBusiness = " AND tbl_business_plan.status = 'approved' AND tbl_business_plan.productions_plan_preventive_id = 0";
        if (!empty($date_dashboard_srceen_sales)) {
            $whereBusiness .= ' AND tbl_business_plan.date >= "' . $date_dashboard_srceen_sales . '" ';
        }

        $sqlBusiness = "
        SELECT COUNT(DISTINCT tbl_business_plan.id) AS total_need_business
        FROM tbl_business_plan
        INNER JOIN tbl_business_plan_items 
            ON tbl_business_plan_items.business_plan_id = tbl_business_plan.id
        INNER JOIN tbl_products 
            ON tbl_products.id = tbl_business_plan_items.items_id
        WHERE 
            tbl_business_plan_items.quantity > 0
            AND (tbl_business_plan_items.quantity - tbl_business_plan_items.quantity_plan) > 0
            $whereBusiness
    ";
        $resultBusiness = $this->db->query($sqlBusiness)->row();
        $total_need_business = (int)($resultBusiness->total_need_business ?? 0);
        $stats['total_no_production_order'] = $total_need_orders + $total_need_business;

        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_be', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $query = $this->db->get();
        $stats["total_be"] = $query->num_rows();

        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->join(
            'tbltblexport_different_items',
            'tbltblexport_different_items.po_id = tbl_productions_orders_details.productions_orders_id',
            'left'
        );
        $this->db->join(
            'tblexport_different',
            'tblexport_different.id = tbltblexport_different_items.id_export_different',
            'left'
        );
        $this->db->where('tblexport_different.type_po', 1);
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_be', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $stats["total_export_be"] = $this->db->get()->num_rows();

        //--- NPL và VTSX ----//
        $this->db->select('
            SUM(IF(tbl_productions_orders.is_export_npl = 1, 1, 0)) AS npl_ready,
            SUM(IF(tbl_productions_orders.is_export_npl = 0, 1, 0)) AS npl_not_ready,
            SUM(IF(tbl_productions_orders.is_export_vtsx = 1, 1, 0)) AS vtsx_ready,
            SUM(IF(tbl_productions_orders.is_export_vtsx = 0, 1, 0)) AS vtsx_not_ready
        ');
        $this->db->from('tbl_productions_orders');
        $this->db->where('tbl_productions_orders.date >=', $date_dashboard_srceen_sales);
        $productions_orders = $this->db->get()->row_array();

        $stats['npl_ready'] = $productions_orders['npl_ready'] ?? 0;
        $stats['npl_not_ready'] = $productions_orders['npl_not_ready'] ?? 0;
        $stats['vtsx_ready'] = $productions_orders['vtsx_ready'] ?? 0;
        $stats['vtsx_not_ready'] = $productions_orders['vtsx_not_ready'] ?? 0;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_ghep_size()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_ghepsize', 1);
        $this->db->where('tbl_productions_orders_items_stages.active', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $query = $this->db->get();
        $stats["total_ghep_size_approved"] = $query->num_rows();

        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_ghepsize', 1);
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $query = $this->db->get();
        $stats["total_ghep_size_pending"] = $query->num_rows();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_dan_trang()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_dantrang', 1);
        $this->db->where('tbl_productions_orders_items_stages.active', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $query = $this->db->get();
        $stats["total_dan_trang_approved"] = $query->num_rows();

        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_dantrang', 1);
        $this->db->where('tbl_productions_orders_items_stages.active', 0);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_productions_orders_items_stages.id');
        $query = $this->db->get();
        $stats["total_dan_trang_pending"] = $query->num_rows();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_ghi_kem()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_ghikem', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $query = $this->db->get();
        $stats["total_ghikem"] = $query->num_rows();

        $this->db->select('DISTINCT (tbl_productions_orders_details.productions_orders_id)');
        $this->db->from('tbl_productions_orders_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->join(
            'tbl_productions_orders_details',
            'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
            'left'
        );
        $this->db->join(
            'tbltblexport_different_items',
            'tbltblexport_different_items.po_id = tbl_productions_orders_details.productions_orders_id',
            'left'
        );
        $this->db->join(
            'tblexport_different',
            'tblexport_different.id = tbltblexport_different_items.id_export_different',
            'left'
        );
        $this->db->where('tblexport_different.type_po', 2);
        $this->db->where('tbl_category_stages.type_use', 0);
        $this->db->where('tbl_stages.is_ghikem', 1);
        $this->db->where('tbl_productions_orders_details.date_created >=', $date_dashboard_srceen_sales);
        $stats["total_export_ghikem"] = $this->db->get()->num_rows();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_export()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse.warehouseman_id > 0) THEN 1 ELSE 0 END) AS transfer_nvl_total_dx,
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse.warehouseman_id = 0) THEN 1 ELSE 0 END) AS transfer_nvl_total_cx,
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse.warehouseman_id > 0) THEN 1 ELSE 0 END) AS transfer_product_total_dx,
            SUM(CASE WHEN (tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse.warehouseman_id = 0) THEN 1 ELSE 0 END) AS transfer_product_total_cx
        ");
        $this->db->where('tbltransfer_warehouse.date >=', $date_dashboard_srceen_sales);
        $this->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        $this->db->group_by('tbltransfer_warehouse.id');
        $export = $this->db->get('tbltransfer_warehouse_detail')->result_array();

        // Đếm DISTINCT phiếu theo từng loại
        $transfer_nvl_total_dx = 0;
        $transfer_nvl_total_cx = 0;
        $transfer_product_total_dx = 0;
        $transfer_product_total_cx = 0;

        foreach ($export as $row) {
            if ($row['transfer_nvl_total_dx'] > 0) {
                $transfer_nvl_total_dx++;
            }
            if ($row['transfer_nvl_total_cx'] > 0) {
                $transfer_nvl_total_cx++;
            }
            if ($row['transfer_product_total_dx'] > 0) {
                $transfer_product_total_dx++;
            }
            if ($row['transfer_product_total_cx'] > 0) {
                $transfer_product_total_cx++;
            }
        }

        $this->db->select("
            SUM(CASE WHEN (tbltblexport_different_items.type = 'nvl' AND tblexport_different.warehouseman_id > 0) THEN 1 ELSE 0 END) AS diff_nvl_total_dx,
            SUM(CASE WHEN (tbltblexport_different_items.type = 'nvl' AND tblexport_different.warehouseman_id = 0) THEN 1 ELSE 0 END) AS diff_nvl_total_cx,
            SUM(CASE WHEN (tbltblexport_different_items.type = 'product' AND tblexport_different.warehouseman_id > 0) THEN 1 ELSE 0 END) AS diff_product_total_dx,
            SUM(CASE WHEN (tbltblexport_different_items.type = 'product' AND tblexport_different.warehouseman_id = 0) THEN 1 ELSE 0 END) AS diff_product_total_cx
        ");
        $this->db->where('tblexport_different.date >=', $date_dashboard_srceen_sales);
        $this->db->join(
            'tblexport_different',
            'tblexport_different.id=tbltblexport_different_items.id_export_different',
            'left'
        );
        $this->db->group_by('tblexport_different.id');
        $export_diff = $this->db->get('tbltblexport_different_items')->result_array();

        // Đếm DISTINCT phiếu theo từng loại
        $diff_nvl_total_dx = 0;
        $diff_nvl_total_cx = 0;
        $diff_product_total_dx = 0;
        $diff_product_total_cx = 0;

        foreach ($export_diff as $row) {
            if ($row['diff_nvl_total_dx'] > 0) {
                $diff_nvl_total_dx++;
            }
            if ($row['diff_nvl_total_cx'] > 0) {
                $diff_nvl_total_cx++;
            }
            if ($row['diff_product_total_dx'] > 0) {
                $diff_product_total_dx++;
            }
            if ($row['diff_product_total_cx'] > 0) {
                $diff_product_total_cx++;
            }
        }


        $stats = [
            'nvl_total_dx' => (float)$transfer_nvl_total_dx + (float)$diff_nvl_total_dx,
            'product_total_dx' => (float)$transfer_product_total_dx + (float)$diff_product_total_dx,
            'nvl_total_cx' => (float)$transfer_nvl_total_cx + (float)$diff_nvl_total_cx,
            'product_total_cx' => (float)$transfer_product_total_cx + (float)$diff_product_total_cx,
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_purchases()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            SUM(
            CASE 
                WHEN tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 0 THEN 1
                WHEN (tbl_internal_proposal_process.bod IS NULL) THEN 1
                ELSE 0
            END
            ) AS internal_proposal_purchases_pending,
            SUM(
            CASE 
                WHEN tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status > 0 THEN 1
                ELSE 0
            END
            ) AS internal_proposal_purchases_approved
        ");
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('EXISTS (SELECT 1 FROM tblinternal_proposal_purchase WHERE tblinternal_proposal_purchase.id_internal_proposal = tblinternal_proposal.id)');
        $this->db->join(
            'tbl_internal_proposal_process',
            'tbl_internal_proposal_process.id_internal_proposal=tblinternal_proposal.id',
            'left'
        );
        $internal_proposal_purchases = $this->db->get('tblinternal_proposal')->row_array();
        $stats = [
            'internal_proposal_purchases_pending' => (float)$internal_proposal_purchases['internal_proposal_purchases_pending'],
            'internal_proposal_purchases_approved' => (float)$internal_proposal_purchases['internal_proposal_purchases_approved'],
        ];
        $this->db->select("
            SUM(CASE WHEN (tbl_suggest_plan_purchase.status > 0) THEN 1 ELSE 0 END) AS suggest_plan_purchase_approved,
            SUM(CASE WHEN (tbl_suggest_plan_purchase.status = 0) THEN 1 ELSE 0 END) AS suggest_plan_purchase_pending
        ");
        $this->db->where('tbl_suggest_plan_purchase.date >=', $date_dashboard_srceen_sales);
        $suggest_plan_purchase = $this->db->get('tbl_suggest_plan_purchase')->row_array();
        $stats['suggest_plan_purchase_approved'] = (float)$suggest_plan_purchase['suggest_plan_purchase_approved'];
        $stats['suggest_plan_purchase_pending'] = (float)$suggest_plan_purchase['suggest_plan_purchase_pending'];

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_purchase_orders()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select('count(id) as full');
        $this->db->where('SUBSTRING(cancel, 1, 5) = "1foso"');
        $this->db->where('tblpurchase_order.is_end', 0);
        $this->db->where('tblpurchase_order.date >=', $date_dashboard_srceen_sales);
        $nhap_du = $this->db->get('tblpurchase_order')->row();

        $this->db->select('COALESCE(count(tblpurchase_order.id),0) as not_import');
        $this->db->join('tblimport', 'tblimport.id_order = tblpurchase_order.id', 'LEFT');
        $this->db->where('tblimport.id is NULL');
        $this->db->where('tblpurchase_order.is_end', 0);
        $this->db->where('tblpurchase_order.date >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tblimport.id_order');
        $chua_nhap = $this->db->get('tblpurchase_order')->row();


        $this->db->select('count(id) as full');
        $this->db->where('tblpurchase_order.is_end', 0);
        $this->db->where('tblpurchase_order.date >=', $date_dashboard_srceen_sales);
        $total_purchase_orders = $this->db->get('tblpurchase_order')->row();
        $stats = [
            'import_full' => (float)$nhap_du->full,
            'not_import' => (float)$chua_nhap->not_import,
            'part_import' => (float)$total_purchase_orders->full - (float)$chua_nhap->not_import - (float)$nhap_du->full,
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_purchase_products()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        // $this->db->select("
        //     COUNT(DISTINCT tbl_business_plan.id) AS total_orders,
        //     COUNT(DISTINCT CASE WHEN tbl_purchase_products.id IS NOT NULL THEN tbl_business_plan.id END) AS total_has_import,
        //     COUNT(DISTINCT CASE WHEN tbl_purchase_products.id IS NULL THEN tbl_business_plan.id END) AS total_no_import
        // ");
        // $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.object_id=tbl_business_plan.id AND object_type = "business_plan"', 'left');
        // $this->db->join('tbl_purchase_products', 'tbl_purchase_products.productions_orders_details_id=tbl_productions_orders_details.id and tbl_purchase_products.final_stage = 1', 'left');
        // $this->db->where('tbl_business_plan.date >', $date_dashboard_srceen_sales);
        // $stats = $this->db->get('tbl_business_plan')->row_array();


        // Đếm số kế hoạch đã nhập kho và đã duyệt
        $this->db->select("
    COUNT(*) AS total_orders,
    -- có ít nhất 1 purchase (final_stage=1)
    SUM(
        CASE WHEN EXISTS (
            SELECT 1 FROM tbl_productions_orders_details pd
            JOIN tbl_purchase_products p ON p.productions_orders_details_id = pd.id AND p.final_stage = 1
            WHERE pd.object_id = tbl_business_plan.id AND pd.object_type = 'business_plan'
        ) THEN 1 ELSE 0 END
    ) AS total_has_import,
    -- không có purchase nào
    SUM(
        CASE WHEN NOT EXISTS (
            SELECT 1 FROM tbl_productions_orders_details pd
            JOIN tbl_purchase_products p ON p.productions_orders_details_id = pd.id AND p.final_stage = 1
            WHERE pd.object_id = tbl_business_plan.id AND pd.object_type = 'business_plan'
        ) THEN 1 ELSE 0 END
    ) AS total_no_import,
    -- pending: ít nhất 1 purchase có warehouseman_id = 0
    SUM(
        CASE WHEN EXISTS (
            SELECT 1 FROM tbl_productions_orders_details pd
            JOIN tbl_purchase_products p ON p.productions_orders_details_id = pd.id AND p.final_stage = 1 AND p.warehouseman_id = 0
            WHERE pd.object_id = tbl_business_plan.id AND pd.object_type = 'business_plan'
        ) THEN 1 ELSE 0 END
    ) AS total_import_pending,
    -- approved: có purchase nhưng KHÔNG có purchase pending (vì pending ưu tiên)
    SUM(
        CASE WHEN EXISTS (
            SELECT 1 FROM tbl_productions_orders_details pd
            JOIN tbl_purchase_products p ON p.productions_orders_details_id = pd.id AND p.final_stage = 1 AND p.warehouseman_id > 0
            WHERE pd.object_id = tbl_business_plan.id AND pd.object_type = 'business_plan'
        ) AND NOT EXISTS (
            SELECT 1 FROM tbl_productions_orders_details pd2
            JOIN tbl_purchase_products p2 ON p2.productions_orders_details_id = pd2.id AND p2.final_stage = 1 AND p2.warehouseman_id = 0
            WHERE pd2.object_id = tbl_business_plan.id AND pd2.object_type = 'business_plan'
        ) THEN 1 ELSE 0 END
    ) AS total_import_approved
");
        $this->db->where('EXISTS (
    SELECT 1 FROM tbl_productions_orders_details pdx
    WHERE pdx.object_id = tbl_business_plan.id AND pdx.object_type = "business_plan"
)', null, false);

        $this->db->where('tbl_business_plan.date >', $date_dashboard_srceen_sales);
        $stats = $this->db->get('tbl_business_plan')->row_array();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats,
            'changed_id' => null
        ]);
    }

    function count_hcns()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $date_now = date('Y-m-d');
        $this->db->select("
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NULL
                          AND tbl_timekeeping_detail_hour.type = 1
                    ) THEN 1 ELSE 0 
                END
            ) AS total_not_checkin,
        
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NOT NULL
                          AND tbl_timekeeping_detail_hour.type = 1
                    ) THEN 1 ELSE 0 
                END
            ) AS total_checkin,
        
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NULL
                          AND tbl_timekeeping_detail_hour.type = 2
                    ) THEN 1 ELSE 0 
                END
            ) AS total_not_checkout,
        
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NOT NULL
                          AND tbl_timekeeping_detail_hour.type = 2
                    ) THEN 1 ELSE 0 
                END
            ) AS total_checkout
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_timekeeping_detail.type != 'X' THEN 1 ELSE 0 
                END
            ) AS total_paid_holiday
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_timekeeping_detail.count_hour_overtime > 0 THEN 1 ELSE 0 
                END
            ) AS total_overtime
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_timekeeping_detail.count_late > 0 THEN 1 ELSE 0 
                END
            ) AS total_late
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_timekeeping_detail.count_late_new > 0 THEN 1 ELSE 0 
                END
            ) AS total_late_new
        ");
        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('tbl_timekeeping_detail.date', $date_now);
        $tb_cham_cong = $this->db->get()->row_array();


        $this->db->from('tbl_contract_labor');
        $this->db->where('tbl_contract_labor.date_sign', $date_now);
        $total_signed = $this->db->count_all_results();


        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_evaluate.type = 'evaluate' THEN 1 ELSE 0 
                END
            ) AS total_evaluate
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_evaluate.type = 'certification' THEN 1 ELSE 0 
                END
            ) AS total_certification
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_evaluate.type = 'certificate' THEN 1 ELSE 0 
                END
            ) AS total_certificate
        ");
        $this->db->from('tbl_evaluate');
        $this->db->where('tbl_evaluate.date_sign', $date_now);
        $tb_evaluate = $this->db->get()->row_array();

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.items_id as items_id,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            WHERE tbl_productions_orders_details.object_type = 'orders'
            AND EXISTS (
                SELECT 1
                FROM tbl_orders
                WHERE tbl_orders.id = tbl_productions_orders_details.object_id
                AND tbl_orders.type_orders = 4
            )
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $tbPurchasesErrors = "(
            SELECT
                tbl_productions_orders_details.productions_orders_id as productions_orders_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            WHERE tbl_purchase_products.is_errors = 1 
            AND tbl_productions_orders_details.object_type = 'orders'
            AND EXISTS (
                SELECT 1
                FROM tbl_orders
                WHERE tbl_orders.id = tbl_productions_orders_details.object_id
                AND tbl_orders.type_orders = 4
            )
            GROUP BY tbl_productions_orders_details.productions_orders_id
        ) tb_error";

        $this->db->select("
            COUNT(tbl_productions_orders.id) AS count_all_production,
            SUM(
                CASE 
                    WHEN COALESCE(tb_production_order_item.quantity,0) = 
                         (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0)) 
                    THEN 1 ELSE 0 
                END
            ) AS count_approved_production,
            SUM(
                CASE 
                    WHEN COALESCE(tb_production_order_item.quantity,0) != 
                         (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0)) 
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve_production
        ");
        $this->db->from('tbl_productions_orders');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id', 'inner');
        $this->db->join($tbProductionsOrderItems,
            'tb_production_order_item.productions_orders_id = tbl_productions_orders.id', 'left');
        $this->db->join($tbPurchasesErrors, 'tb_error.productions_orders_id = tbl_productions_orders.id', 'left');
        $this->db->join('tbl_products', 'tbl_products.id = tb_production_order_item.items_id', 'inner');
        $this->db->where('tbl_productions_orders.date >=', $date_dashboard_srceen_sales);
        $dtProductionsOrders = $this->db->get()->row_array();


        $this->db->select("
            COUNT(tbl_suggest_plan_outsource.id) AS count_all,
            SUM(CASE WHEN tbl_suggest_plan_outsource.status = 1 THEN 1 ELSE 0 END) AS count_approved,
            SUM(CASE WHEN tbl_suggest_plan_outsource.status = 0 THEN 1 ELSE 0 END) AS count_un_approve,
          ", false);
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where('tbl_suggest_plan_outsource.date >=', $date_dashboard_srceen_sales);
        $tb_outsource = $this->db->get()->row_array();

        $this->db->select("
            SUM(CASE WHEN tblproduction_report.type_report = 4 THEN 1 ELSE 0 END) AS count_all_vi_pham,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 4
                     AND NOT EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_approved_vi_pham,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 4
                     AND EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve_vi_pham
        ", false);

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.date >=', $date_dashboard_srceen_sales);
        $tb_production = $this->db->get()->row_array();


        $this->db->from('tblinternal_proposal');
        $this->db->join('tblproduction_report', 'tblproduction_report.id_internal_proposal = tblinternal_proposal.id',
            'inner');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $total_internal_proposal_khan = $this->db->count_all_results();

        $data = [
            'total_not_checkin' => (float)$tb_cham_cong['total_not_checkin'],
            'total_checkin' => (float)$tb_cham_cong['total_checkin'],
            'total_not_checkout' => (float)$tb_cham_cong['total_not_checkout'],
            'total_checkout' => (float)$tb_cham_cong['total_checkout'],
            'total_paid_holiday' => (float)$tb_cham_cong['total_paid_holiday'],
            'total_overtime' => (float)$tb_cham_cong['total_overtime'],
            'total_late' => (float)$tb_cham_cong['total_late'],
            'total_late_new' => (float)$tb_cham_cong['total_late_new'],
            'total_signed' => (float)$total_signed,
            'total_evaluate' => (float)$tb_evaluate['total_evaluate'],
            'total_certification' => (float)$tb_evaluate['total_certification'],
            'total_certificate' => (float)$tb_evaluate['total_certificate'],
            'count_all_outsource_hcns' => (float)$tb_outsource['count_all'],
            'count_approved_outsource_hcns' => (float)$tb_outsource['count_approved'],
            'count_un_approve_outsource_hcns' => (float)$tb_outsource['count_un_approve'],
            'count_all_vi_pham_hcns' => (float)$tb_production['count_all_vi_pham'],
            'count_approved_vi_pham_hcns' => (float)$tb_production['count_approved_vi_pham'],
            'count_un_approve_vi_pham_hcns' => (float)$tb_production['count_un_approve_vi_pham'],
            'count_all_production_hcns' => (float)$dtProductionsOrders['count_all_production'],
            'count_approved_production_hcns' => (float)$dtProductionsOrders['count_approved_production'],
            'count_un_approve_production_hcns' => (float)$dtProductionsOrders['count_un_approve_production'],
            'total_internal_proposal_khan_hcns' => (float)$total_internal_proposal_khan
        ];


        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    function count_accounting()
    {

        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            SUM(
            CASE 
                WHEN tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 0 THEN 1
                WHEN (tbl_internal_proposal_process.bod IS NULL) THEN 1
                ELSE 0
            END
            ) AS internal_proposal_money_pending,
            SUM(
            CASE 
                WHEN tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status > 0 THEN 1
                ELSE 0
            END
            ) AS internal_proposal_money_approved
        ");
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.money >', 0);
        $this->db->join(
            'tbl_internal_proposal_process',
            'tbl_internal_proposal_process.id_internal_proposal=tblinternal_proposal.id',
            'left'
        );
        $internal_proposal_money = $this->db->get('tblinternal_proposal')->row_array();
        $stats = [
            'internal_proposal_money_pending' => (float)$internal_proposal_money['internal_proposal_money_pending'],
            'internal_proposal_money_approved' => (float)$internal_proposal_money['internal_proposal_money_approved'],
        ];

        $this->db->select("
            SUM(CASE WHEN ((tblsuggestion.payments + 0.1) <= tblsuggestion.price_total) THEN 1 ELSE 0 END) AS count_not_payed,
            SUM(CASE WHEN ((tblsuggestion.payments + 0.1) >= tblsuggestion.price_total) THEN 1 ELSE 0 END) AS count_payed,
        ");
        $this->db->where('tblsuggestion.date >=', $date_dashboard_srceen_sales);
        $suggestion = $this->db->get('tblsuggestion')->row_array();
        $stats['count_not_payed'] = (float)$suggestion['count_not_payed'];
        $stats['count_payed'] = (float)$suggestion['count_payed'];


        $this->db->select("
            SUM(CASE WHEN tbl_deliveries.code_custom IS NULL THEN 1 ELSE 0 END) AS count_code_custom_null,
            SUM(CASE WHEN tbl_deliveries.code_custom IS NOT NULL THEN 1 ELSE 0 END) AS count_code_custom_not_null
        ");
        $this->db->join('tblclients', 'tblclients.userid=tbl_deliveries.customer_id', 'left');
        $this->db->where('tblclients.declare_customs', 1);
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $deliveries = $this->db->get('tbl_deliveries')->row_array();
        $stats['count_code_custom_null'] = (float)$deliveries['count_code_custom_null'];
        $stats['count_code_custom_not_null'] = (float)$deliveries['count_code_custom_not_null'];


        $sql = "
        SELECT
            COUNT(DISTINCT i.id) AS total_imports,
            COUNT(DISTINCT CASE WHEN EXISTS (
                SELECT 1
                FROM tblimport_items ii
                JOIN tblpurchase_invoice_items pii ON pii.id_import_item = ii.id
                JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 0
                WHERE ii.id_import = i.id
            ) THEN i.id END) AS imports_with_invoice,
            COUNT(DISTINCT CASE WHEN NOT EXISTS (
                SELECT 1
                FROM tblimport_items ii
                JOIN tblpurchase_invoice_items pii ON pii.id_import_item = ii.id
                JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 0
                WHERE ii.id_import = i.id
            ) THEN i.id END) AS imports_without_invoice
        FROM tblimport i
        WHERE i.date >= ?
        ";

        $row = $this->db->query($sql, [$date_dashboard_srceen_sales])->row_array();

        // trả về hoặc gán vào $stats tuỳ chỗ bạn dùng
        $import = [
            'total_imports' => (int)($row['total_imports'] ?? 0),
            'imports_with_invoice' => (int)($row['imports_with_invoice'] ?? 0),
            'imports_without_invoice' => (int)($row['imports_without_invoice'] ?? 0),
        ];
        $stats['total_imports'] = (float)$import['total_imports'];
        $stats['imports_with_invoice'] = (float)$import['imports_with_invoice'];
        $stats['imports_without_invoice'] = (float)$import['imports_without_invoice'];


        $sqlsuggestion = "
        SELECT
            COUNT(DISTINCT s.id) AS total_suggestion,
            COUNT(DISTINCT CASE WHEN EXISTS (
            SELECT 1
            FROM tblpurchase_invoice_items pii
            JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 1
            WHERE pii.id_import_item = s.id
            ) THEN s.id END) AS suggestions_with_invoice,
            COUNT(DISTINCT CASE WHEN NOT EXISTS (
            SELECT 1
            FROM tblpurchase_invoice_items pii
            JOIN tblpurchase_invoice pi ON pi.id = pii.purchase_invoice_id AND pi.type_create = 1
            WHERE pii.id_import_item = s.id
            ) THEN s.id END) AS suggestions_without_invoice
        FROM tblsuggestion s
        WHERE s.date >= ?
        ";

        $rowsuggestion = $this->db->query($sqlsuggestion, [$date_dashboard_srceen_sales])->row_array();
        // trả về hoặc gán vào $stats tuỳ chỗ bạn dùng
        $suggestion = [
            'total_suggestion' => (int)($rowsuggestion['total_suggestion'] ?? 0),
            'suggestions_with_invoice' => (int)($rowsuggestion['suggestions_with_invoice'] ?? 0),
            'suggestions_without_invoice' => (int)($rowsuggestion['suggestions_without_invoice'] ?? 0),
        ];
        $stats['total_suggestion'] = (float)$suggestion['total_suggestion'];
        $stats['suggestions_with_invoice'] = (float)$suggestion['suggestions_with_invoice'];
        $stats['suggestions_without_invoice'] = (float)$suggestion['suggestions_without_invoice'];


        $this->db->select("
            COUNT(DISTINCT tbl_invoices.id) AS total_invoices,
            SUM(CASE WHEN tbl_invoices.status_payment < 2 THEN 1 ELSE 0 END) AS count_paymented_not,
            SUM(CASE WHEN tbl_invoices.status_payment = 2 THEN 1 ELSE 0 END) AS count_paymented
        ");
        $this->db->where('tbl_invoices.date >=', $date_dashboard_srceen_sales);
        $invoices = $this->db->get('tbl_invoices')->row_array();
        $stats['total_invoices'] = (float)$invoices['total_invoices'];
        $stats['count_paymented_not'] = (float)$invoices['count_paymented_not'];
        $stats['count_paymented'] = (float)$invoices['count_paymented'];


        $this->db->select("
            SUM(CASE WHEN tbl_deliveries.received_certificate = 1 THEN 1 ELSE 0 END) AS count_received_certificate,
            SUM(CASE WHEN tbl_deliveries.received_certificate = 0 THEN 1 ELSE 0 END) AS count_received_certificate_not
        ");
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $deliveries = $this->db->get('tbl_deliveries')->row_array();
        $stats['count_received_certificate'] = (float)$deliveries['count_received_certificate'];
        $stats['count_received_certificate_not'] = (float)$deliveries['count_received_certificate_not'];

        $this->db->select("
            SUM(CASE WHEN tbl_invoice_items.id IS NULL THEN 1 ELSE 0 END) AS count_not_invoice,
            SUM(CASE WHEN tbl_invoice_items.id IS NOT NULL THEN 1 ELSE 0 END) AS count_invoice
        ");
        $this->db->where('tbl_deliveries.date >=', $date_dashboard_srceen_sales);
        $this->db->join(
            'tbl_invoice_items',
            'tbl_invoice_items.object_id=tbl_deliveries.id',
            'left'
        );
        $deliveries_invoice = $this->db->get('tbl_deliveries')->row_array();
        $stats['count_not_invoice'] = (float)$deliveries_invoice['count_not_invoice'];
        $stats['count_invoice'] = (float)$deliveries_invoice['count_invoice'];

        // Count supplier contracts whose latest contract per supplier has already ended
        // Count supplier contracts: latest contract per supplier ended (need re-sign) and re-signed (still active)
        $sql = "
            SELECT
            SUM(CASE WHEN c.date_end < ? THEN 1 ELSE 0 END) AS cnt_ended,
            SUM(CASE WHEN c.date_end >= ? THEN 1 ELSE 0 END) AS cnt_active
            FROM (
            SELECT MAX(id) AS id
            FROM tbl_contracts_supplier
            WHERE date_end IS NOT NULL
            GROUP BY supplier_id
            ) t
            JOIN tbl_contracts_supplier c ON c.id = t.id
        ";
        $row = $this->db->query($sql, [date('Y-m-d'), date('Y-m-d')])->row_array();
        $stats['contract_ncc_ctk'] = (int)($row['cnt_ended'] ?? 0);      // cần tái kí (date_end < today)
        $stats['contract_ncc_dtk'] = (int)($row['cnt_active'] ?? 0); // đã tái kí (date_end >= today)

        // Count client contracts whose latest contract per customer has already ended
        $sql2 = "
            SELECT
            SUM(CASE WHEN c.date_end < ? THEN 1 ELSE 0 END) AS cnt_ended,
            SUM(CASE WHEN c.date_end >= ? THEN 1 ELSE 0 END) AS cnt_active
            FROM (
            SELECT MAX(id) AS id
            FROM tbl_contracts_sales
            WHERE date_end IS NOT NULL
            GROUP BY customer_id
            ) t
            JOIN tbl_contracts_sales c ON c.id = t.id
        ";
        $row2 = $this->db->query($sql2, [date('Y-m-d'), date('Y-m-d')])->row_array();
        $stats['contract_clients_ctk'] = (int)($row2['cnt_ended'] ?? 0); // cần tái kí
        $stats['contract_clients_dtk'] = (int)($row2['cnt_active'] ?? 0); // đã tái kí

        $this->db->select("
            COUNT(tbl_request_repair.id) AS count_all_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_repair,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_repair,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_repair
        ", false);

        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.date >=', $date_dashboard_srceen_sales);
        $tb_request_repair = $this->db->get()->row_array();

        $stats['count_approved_repair'] = (int)$tb_request_repair['count_approved_repair'];
        $stats['count_un_approve_repair'] = (int)$tb_request_repair['count_un_approve_repair'];
        $stats['count_approved_finish_repair'] = (int)$tb_request_repair['count_approved_finish_repair'];
        $stats['count_un_approve_finish_repair'] = (int)$tb_request_repair['count_un_approve_finish_repair'];


        $this->db->select("
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_hh,
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_chh
        ", false);

        $this->db->from('tblplan_propose');
        $this->db->where('tblplan_propose.type_plan_propose', 'pay_slip');
        $this->db->where('tblplan_propose.date >=', $date_dashboard_srceen_sales);
        $plan_propose_chi = $this->db->get()->row_array();
        $stats['count_plan_propose_chi_hh'] = (int)$plan_propose_chi['count_plan_propose_hh'];
        $stats['count_plan_propose_chi_chh'] = (int)$plan_propose_chi['count_plan_propose_chh'];

        $this->db->select("
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_hh,
            SUM(
                CASE 
                    WHEN tblplan_propose.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_plan_propose_chh
        ", false);

        $this->db->from('tblplan_propose');
        $this->db->where('tblplan_propose.type_plan_propose', 'vouchers_coupon');
        $this->db->where('tblplan_propose.date >=', $date_dashboard_srceen_sales);
        $plan_propose_thu = $this->db->get()->row_array();
        $stats['count_plan_propose_thu_hh'] = (int)$plan_propose_thu['count_plan_propose_hh'];
        $stats['count_plan_propose_thu_chh'] = (int)$plan_propose_thu['count_plan_propose_chh'];


        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.recommended_list_group_id', 6036);
        $recommended_list_group_dxmtkht = $this->db->get()->num_rows();
        $stats['recommended_list_group_dxmtkht'] = (int)$recommended_list_group_dxmtkht;


        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.recommended_list_group_id', 6038);
        $recommended_list_group_dxmnkht = $this->db->get()->num_rows();
        $stats['recommended_list_group_dxmnkht'] = (int)$recommended_list_group_dxmnkht;

        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.recommended_list_group_id', 6170);
        $recommended_list_group_dxdgmvkh = $this->db->get()->num_rows();
        $stats['recommended_list_group_dxdgmvkh'] = (int)$recommended_list_group_dxdgmvkh;


        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.recommended_list_group_id', 6136);
        $recommended_list_group_dxdgmtkh = $this->db->get()->num_rows();
        $stats['recommended_list_group_dxdgmtkh'] = (int)$recommended_list_group_dxdgmtkh;

        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $this->db->where('tblinternal_proposal.recommended_list_group_id', 6040);
        $recommended_list_group_dxtsdgmk = $this->db->get()->num_rows();
        $stats['recommended_list_group_dxtsdgmk'] = (int)$recommended_list_group_dxtsdgmk;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function countTask()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $department_id = $this->input->get('department_id');

        $this->db->select("
            COUNt(tbltasks.id) AS count_all
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbltasks.status = 5 THEN 1 ELSE 0 
                END
            ) AS count_finish
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbltasks.status != 5 THEN 1 ELSE 0 
                END
            ) AS count_procesing
        ");
        $this->db->from('tbltasks');
        $this->db->where('tbltasks.startdate >=', $date_dashboard_srceen_sales);
        if ($department_id != -1) {
            $this->db->where('EXISTS (
                SELECT 1 
                FROM tbltask_department 
                WHERE tbltask_department.task_id = tbltasks.id 
                AND tbltask_department.department_id = ' . $department_id . '
             )');
        }
        $tb_task = $this->db->get()->row_array();

        $data = [
            'count_all' => (float)$tb_task['count_all'],
            'count_finish' => (float)$tb_task['count_finish'],
            'count_procesing' => (float)$tb_task['count_procesing'],
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function count_ksnb()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $this->db->select("
            COUNt(tblinternal_proposal.id) AS count_all
        ");
        $this->db->select("
            SUM(
                CASE 
                   WHEN NOT EXISTS (
                        SELECT 1 
                        FROM tbl_internal_proposal_process 
                        WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                        AND tbl_internal_proposal_process.status = 0
                    ) 
                   AND EXISTS (
                        SELECT 1
                        FROM tbl_internal_proposal_process
                        WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                   )
                   THEN 1 ELSE 0 
                END
            ) AS count_approved
        ");

        $this->db->select("
            SUM(
                CASE 
                   WHEN  
                        EXISTS (
                            SELECT 1 
                            FROM tbl_internal_proposal_process 
                            WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                            AND tbl_internal_proposal_process.status = 0
                        )
                        OR NOT EXISTS (
                            SELECT 1
                            FROM tbl_internal_proposal_process
                            WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                        )
                   THEN 1 ELSE 0 
                END
            ) AS count_un_approve
        ");

        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $tb_noi_bo = $this->db->get()->row_array();

        $this->db->select("
            SUM(CASE WHEN tblproduction_report.type_report = 1 THEN 1 ELSE 0 END) AS count_all,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 1
                     AND NOT EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_approved,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 1
                     AND EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve,
        
            SUM(CASE WHEN tblproduction_report.type_report = 4 THEN 1 ELSE 0 END) AS count_all_vi_pham,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 4
                     AND NOT EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_approved_vi_pham,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 4
                     AND EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve_vi_pham,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 4
                    AND tblproduction_report.quantity > 0
                    THEN 1 ELSE 0 
                END
            ) AS count_error_vi_pham,
            SUM(
                CASE 
                    WHEN tblproduction_report.type_report = 1
                    AND tblproduction_report.quantity > 0
                    THEN 1 ELSE 0 
                END
            ) AS count_error_production
        ", false);

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.date >=', $date_dashboard_srceen_sales);
        $tb_production = $this->db->get()->row_array();


        $this->db->select("
            COUNT(tbl_request_overtime.id) AS count_all,
            SUM(
                CASE 
                    WHEN NOT EXISTS (
                        SELECT 1 
                        FROM tbl_request_overtime_item 
                        WHERE tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id
                        AND tbl_request_overtime_item.status = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_approved,
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_request_overtime_item 
                        WHERE tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id
                        AND tbl_request_overtime_item.status = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve,
          ", false);
        $this->db->from('tbl_request_overtime');
        $this->db->where('tbl_request_overtime.date >=', $date_dashboard_srceen_sales);
        $tb_overtime = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_suggest_plan_outsource.id) AS count_all,
            SUM(CASE WHEN tbl_suggest_plan_outsource.status = 1 THEN 1 ELSE 0 END) AS count_approved,
            SUM(CASE WHEN tbl_suggest_plan_outsource.status = 0 THEN 1 ELSE 0 END) AS count_un_approve,
          ", false);
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where('tbl_suggest_plan_outsource.date >=', $date_dashboard_srceen_sales);
        $tb_outsource = $this->db->get()->row_array();


        $month_year = date('Y-m');
        $whereDate = '';
        $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            COALESCE(violate,0) as violate,
            tb_tamp.kpi_list_criteria_department_id,
            "" as rating
        ');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if (!empty($department_search)) {
            $this->db->where('EXISTS (
                SELECT 1
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = ' . $department_search . '
            )');
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();
        $dtCriteriaDepartmentViolateNew = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;
            }
        }

        $countStaff = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $pointMax = 100;
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                $countedArray = [];
                if (!empty($kpi_list_criteria_department_id[0])) {
                    $countedArray = array_count_values($kpi_list_criteria_department_id);
                }
                $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
                $point = 0;
                if (!empty($countedArray)) {
                    foreach ($countedArray as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point'];
                            }
                        }
                        $maxViolations = max($violations);
                        if ($v < $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                $point += $violationsToPoint[$v];
                            }
                        } else {
                            $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }
                $pointCurrent = $pointMax - $point;
                if ($pointCurrent <= 0) {
                    $pointCurrent = 1;
                }

                if ($pointCurrent < 100) {
                    $countStaff++;
                }
            }
        }

        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.check_productivity', 1);
        $categoryStage = $this->db->get()->result_array();
        $arrCategoryStageId = $categoryStage ? array_column($categoryStage, 'id') : [];

        $this->db->select("
            GROUP_CONCAT(DISTINCT tbl_productions_orders.id) as po_id,
            tbl_category_stages.id as category_stages_id
        ");
        $this->db->from('tbl_productions_orders');
        $this->db->join('tbl_productions_orders_items_stages',
            'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->where_in('tbl_category_stages.id', $arrCategoryStageId);
        $this->db->where('tbl_productions_orders.date >=', $date_dashboard_srceen_sales);
        $this->db->group_by('tbl_category_stages.id');
        $dtProductionListsItems = $this->db->get()->result_array();
        if (!empty($dtProductionListsItems)) {
            foreach ($dtProductionListsItems as $key => $value) {
                $po_id = $value['po_id'];
                $arrPoId = explode(',', $po_id);
                $this->db->select("
                    SUM(tbl_purchase_products.total_quantity) AS total_quantity
                ");
                $this->db->from('tbl_purchase_products');
                $this->db->join('tbl_productions_orders_items_stages',
                    'tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
                $this->db->where('tbl_stages.category_stage_productivity', $value['category_stages_id']);
                $this->db->where('tbl_stages.check_productivity', 1);
                $this->db->where_in('tbl_productions_orders_items_stages.productions_orders_id', $arrPoId);
                $total_quantity = $this->db->get()->row_array();
                $dtProductionListsItems[$key]['total'] = (float)$total_quantity['total_quantity'];
            }
        }


        $date_now = date('Y-m-d');
        $this->db->select("
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NULL
                          AND tbl_timekeeping_detail_hour.type = 1
                    ) THEN 1 ELSE 0 
                END
            ) AS total_not_checkin,
        
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NOT NULL
                          AND tbl_timekeeping_detail_hour.type = 1
                    ) THEN 1 ELSE 0 
                END
            ) AS total_checkin,
        
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NULL
                          AND tbl_timekeeping_detail_hour.type = 2
                    ) THEN 1 ELSE 0 
                END
            ) AS total_not_checkout,
        
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_timekeeping_detail_hour 
                        WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                          AND tbl_timekeeping_detail_hour.hour IS NOT NULL
                          AND tbl_timekeeping_detail_hour.type = 2
                    ) THEN 1 ELSE 0 
                END
            ) AS total_checkout
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_timekeeping_detail.count_hour_overtime > 0 THEN 1 ELSE 0 
                END
            ) AS total_overtime
        ");
        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('tbl_timekeeping_detail.date', $date_now);
        $tb_cham_cong = $this->db->get()->row_array();


        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_evaluate.type = 'evaluate' THEN 1 ELSE 0 
                END
            ) AS total_evaluate
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_evaluate.type = 'certification' THEN 1 ELSE 0 
                END
            ) AS total_certification
        ");
        $this->db->select("
            SUM(
                CASE 
                    WHEN tbl_evaluate.type = 'certificate' THEN 1 ELSE 0 
                END
            ) AS total_certificate
        ");
        $this->db->from('tbl_evaluate');
        $this->db->where('tbl_evaluate.date_sign', $date_now);
        $tb_evaluate = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_suggest_rating_machines.id) AS count_all_rating,
            SUM(
                CASE 
                    WHEN tbl_suggest_rating_machines.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_rating,
            SUM(
                CASE 
                    WHEN tbl_suggest_rating_machines.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_rating,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_rating,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_rating
        ", false);

        $this->db->from('tbl_suggest_rating_machines');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_rating_machines.machines_id', 'inner');
        $this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.machines_id = tbl_machines.id', 'left');
        $this->db->where('tbl_suggest_rating_machines.date >=', $date_dashboard_srceen_sales);
        $tb_suggest_rating = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_suggest_maintenance.id) AS count_all_maintenance,
            SUM(
                CASE 
                    WHEN tbl_suggest_maintenance.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_maintenance,
            SUM(
                CASE 
                    WHEN tbl_suggest_maintenance.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_maintenance,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_maintenance,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_maintenance
        ", false);

        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.date >=', $date_dashboard_srceen_sales);
        $tb_suggest_maintenance = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_request_repair.id) AS count_all_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_repair,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_repair,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_repair
        ", false);

        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.date >=', $date_dashboard_srceen_sales);
        $tb_request_repair = $this->db->get()->row_array();

        $this->db->select('COUNT(DISTINCT tbl_productions_orders_items.productions_orders_id) AS total_production_items_errors');
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_productions_orders_details',
            'tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id', 'inner');
        $this->db->join('tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
        $this->db->where('tbl_purchase_products.is_errors', 1);
        $this->db->where('tbl_purchase_products.date >=', $date_dashboard_srceen_sales);
        $total_purchase_errors = $this->db->get()->row_array();


        $this->db->select("
            SUM(CASE WHEN tblwarehouse_items.type_items = 'nvl' THEN 1 ELSE 0 END) AS count_all_nvl,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'nvl' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    THEN 1 ELSE 0 
                END
            ) AS count_6_nvl,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'nvl' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    THEN 1 ELSE 0 
                END
            ) AS count_12_nvl,
            
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_all_btp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_6_btp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_12_btp,
             SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_all_tp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_6_tp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_12_tp,
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion',
            'inner');
        $this->db->join('tbl_productions_orders_details',
            'tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id', 'left');
        $this->db->join('tbl_products',
            'tbl_products.id = tblwarehouse_items.id_items AND tblwarehouse_items.type_items = "product"', 'left');
        $this->db->where('tblwarehouse_items.product_quantity >=', ' 0.0000000001');
        $this->db->where('tblwarehouse_items.warehouse_id NOT IN (' . WAREHOUSES_SYSTEM . ')');
        $tb_warehouse_items = $this->db->get()->row_array();


        $this->db->from('tblinventory');
        $this->db->where('tblinventory.date >=', $date_dashboard_srceen_sales);
        $count_inventory = $this->db->count_all_results();

        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.recommended_list_group_id', 7062);
        $this->db->where('tblinternal_proposal.date >=', $date_dashboard_srceen_sales);
        $count_internal_proposal_de_xuat = $this->db->count_all_results();

        $data = [
            'count_all_internal' => (float)$tb_noi_bo['count_all'],
            'count_approved_internal' => (float)$tb_noi_bo['count_approved'],
            'count_un_approve_internal' => (float)$tb_noi_bo['count_un_approve'],
            'count_all_production' => (float)$tb_production['count_all'],
            'count_approved_production' => (float)$tb_production['count_approved'],
            'count_un_approve_production' => (float)$tb_production['count_un_approve'],
            'count_all_vi_pham' => (float)$tb_production['count_all_vi_pham'],
            'count_approved_vi_pham' => (float)$tb_production['count_approved_vi_pham'],
            'count_un_approve_vi_pham' => (float)$tb_production['count_un_approve_vi_pham'],
            'count_all_overtime' => (float)$tb_overtime['count_all'],
            'count_approved_overtime' => (float)$tb_overtime['count_approved'],
            'count_un_approve_overtime' => (float)$tb_overtime['count_un_approve'],
            'count_all_outsource' => (float)$tb_outsource['count_all'],
            'count_approved_outsource' => (float)$tb_outsource['count_approved'],
            'count_un_approve_outsource' => (float)$tb_outsource['count_un_approve'],
            'count_staff_kpi' => (float)$countStaff,
            'dtProductionListsItems' => $dtProductionListsItems,
            'count_not_checkin_ksnb' => (float)$tb_cham_cong['total_not_checkin'],
            'count_checkin_ksnb' => (float)$tb_cham_cong['total_checkin'],
            'count_not_checkout_ksnb' => (float)$tb_cham_cong['total_not_checkout'],
            'count_checkout_ksnb' => (float)$tb_cham_cong['total_checkout'],
            'count_overtime_ksnb' => (float)$tb_cham_cong['total_overtime'],
            'count_evaluate_ksnb' => (float)$tb_evaluate['total_evaluate'],
            'count_certification_ksnb' => (float)$tb_evaluate['total_certification'],
            'count_certificate_ksnb' => (float)$tb_evaluate['total_certificate'],
            'count_all_rating_ksnb' => (float)$tb_suggest_rating['count_all_rating'],
            'count_approved_rating_ksnb' => (float)$tb_suggest_rating['count_approved_rating'],
            'count_un_approve_rating_ksnb' => (float)$tb_suggest_rating['count_un_approve_rating'],
            'count_approved_finish_rating_ksnb' => (float)$tb_suggest_rating['count_approved_finish_rating'],
            'count_un_approve_finish_rating_ksnb' => (float)$tb_suggest_rating['count_un_approve_finish_rating'],
            'count_all_maintenance_ksnb' => (float)$tb_suggest_maintenance['count_all_maintenance'],
            'count_approved_maintenance_ksnb' => (float)$tb_suggest_maintenance['count_approved_maintenance'],
            'count_un_approve_maintenance_ksnb' => (float)$tb_suggest_maintenance['count_un_approve_maintenance'],
            'count_approved_finish_maintenance_ksnb' => (float)$tb_suggest_maintenance['count_approved_finish_maintenance'],
            'count_un_approve_finish_maintenance_ksnb' => (float)$tb_suggest_maintenance['count_un_approve_finish_maintenance'],
            'count_all_repair_ksnb' => (float)$tb_request_repair['count_all_repair'],
            'count_approved_repair_ksnb' => (float)$tb_request_repair['count_approved_repair'],
            'count_un_approve_repair_ksnb' => (float)$tb_request_repair['count_un_approve_repair'],
            'count_approved_finish_repair_ksnb' => (float)$tb_request_repair['count_approved_finish_repair'],
            'count_un_approve_finish_repair_ksnb' => (float)$tb_request_repair['count_un_approve_finish_repair'],
            'total_purchase_errors_ksnb' => (float)$total_purchase_errors['total_production_items_errors'],
            'count_all_nvl_ksnb' => (float)$tb_warehouse_items['count_all_nvl'],
            'count_6_nvl_ksnb' => (float)$tb_warehouse_items['count_6_nvl'],
            'count_12_nvl_ksnb' => (float)$tb_warehouse_items['count_12_nvl'],
            'count_all_btp_ksnb' => (float)$tb_warehouse_items['count_all_btp'],
            'count_6_btp_ksnb' => (float)$tb_warehouse_items['count_6_btp'],
            'count_12_btp_ksnb' => (float)$tb_warehouse_items['count_12_btp'],
            'count_all_tp_ksnb' => (float)$tb_warehouse_items['count_all_tp'],
            'count_6_tp_ksnb' => (float)$tb_warehouse_items['count_6_tp'],
            'count_12_tp_ksnb' => (float)$tb_warehouse_items['count_12_tp'],
            'count_inventory_ksnb' => (float)$count_inventory,
            'count_internal_proposal_de_xuat_ksnb' => (float)$count_internal_proposal_de_xuat,
            'count_error_vi_pham' => (float)$tb_production['count_error_vi_pham'],
            'count_error_production' => (float)$tb_production['count_error_production']
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function count_foso()
    {
        $date = date('Y-m-d');

        $this->db->select("
             COUNT(id) AS count_add
          ", false);
        $this->db->from('tblactivity_log_v2');
        $this->db->where('DATE(date)', $date);
        $this->db->where('actions', 'add');
        $this->db->where_in('type_parent_obj', ['clients', 'suppliers', 'products', 'items']);
        $tb_add = $this->db->get()->row_array();

        $this->db->select("
            COUNT(id) AS count_edit
          ", false);
        $this->db->from('tblactivity_log_v2');
        $this->db->where('DATE(date)', $date);
        $this->db->where('actions', 'edit');
        $this->db->where_in('type_parent_obj', ['clients', 'suppliers', 'products', 'items']);
        $tb_edit = $this->db->get()->row_array();

        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.is_cancel', 1);
        $this->db->where('DATE(date_cancel)', $date);
        $total_orders_cancel = $this->db->count_all_results();


        $this->db->select("
            COUNT(id) AS count_delete
          ", false);
        $this->db->from('tblactivity_log_v2');
        $this->db->where('DATE(date)', $date);
        $this->db->where('actions', 'delete');
        $this->db->where_in('type_parent_obj', [
            'orders',
            'quotes',
            'deliveries',
            'returned_goods',
            'internal_proposal',
            'tasks',
            'production_report',
            'purchase',
            'purchase_order',
            'import',
            'return_suppliers',
            'productions_orders',
            'business_plan',
            'invoices',
            'vouchers_coupon',
            'other_payslips_coupon',
            'purchase_invoice',
            'other_payslips',
            'suggestion',
            'transfer_warehouse',
            'tranfer_business',
            'exporting_producion',
            'purchase_product',
            'purchase_internal',
            'export_different',
        ]);
        $tb_delete = $this->db->get()->row_array();

        $data = [
            'count_add' => (float)$tb_add['count_add'],
            'count_edit' => (float)$tb_edit['count_edit'],
            'count_cancel' => (float)$total_orders_cancel,
            'count_delete' => (float)$tb_delete['count_delete'],
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function count_technial()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $this->db->select("
            COUNT(id) AS count_all_report,
            SUM(
                CASE 
                    WHEN NOT EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_approved_report,
            SUM(
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM tbl_process_production_report 
                        WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                     )
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve_report,
        ", false);

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.machines_id != 0');
        $this->db->where('tblproduction_report.type_report = 1');
        $this->db->where('tblproduction_report.date >=', $date_dashboard_srceen_sales);
        $tb_production = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_suggest_rating_machines.id) AS count_all_rating,
            SUM(
                CASE 
                    WHEN tbl_suggest_rating_machines.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_rating,
            SUM(
                CASE 
                    WHEN tbl_suggest_rating_machines.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_rating,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_rating,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_rating
        ", false);

        $this->db->from('tbl_suggest_rating_machines');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_rating_machines.machines_id', 'inner');
        $this->db->join('tbl_machines_maintenance', 'tbl_machines_maintenance.machines_id = tbl_machines.id', 'left');
        $this->db->where('tbl_suggest_rating_machines.date >=', $date_dashboard_srceen_sales);
        $tb_suggest_rating = $this->db->get()->row_array();


        $this->db->select("
            COUNT(tbl_request_calibration.id) AS count_all_calibration,
            SUM(
                CASE 
                    WHEN tbl_request_calibration.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_calibration,
            SUM(
                CASE 
                    WHEN tbl_request_calibration.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_calibration,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_calibration,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_calibration
        ", false);

        $this->db->from('tbl_request_calibration');
        $this->db->where('tbl_request_calibration.date >=', $date_dashboard_srceen_sales);
        $tb_request_calibration = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_suggest_maintenance.id) AS count_all_maintenance,
            SUM(
                CASE 
                    WHEN tbl_suggest_maintenance.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_maintenance,
            SUM(
                CASE 
                    WHEN tbl_suggest_maintenance.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_maintenance,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_maintenance,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_maintenance
        ", false);

        $this->db->from('tbl_suggest_maintenance');
        $this->db->where('tbl_suggest_maintenance.date >=', $date_dashboard_srceen_sales);
        $tb_suggest_maintenance = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_request_repair.id) AS count_all_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_repair,
            SUM(
                CASE 
                    WHEN tbl_request_repair.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_repair,
            SUM(
                CASE 
                    WHEN status_finish = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_finish_repair,
            SUM(
                CASE 
                    WHEN status_finish = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_finish_repair
        ", false);

        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.date >=', $date_dashboard_srceen_sales);
        $tb_request_repair = $this->db->get()->row_array();

        $data = [
            'count_all_report' => (float)$tb_production['count_all_report'],
            'count_approved_report' => (float)$tb_production['count_approved_report'],
            'count_un_approve_report' => (float)$tb_production['count_un_approve_report'],
            'count_all_rating' => (float)$tb_suggest_rating['count_all_rating'],
            'count_approved_rating' => (float)$tb_suggest_rating['count_approved_rating'],
            'count_un_approve_rating' => (float)$tb_suggest_rating['count_un_approve_rating'],
            'count_approved_finish_rating' => (float)$tb_suggest_rating['count_approved_finish_rating'],
            'count_un_approve_finish_rating' => (float)$tb_suggest_rating['count_un_approve_finish_rating'],
            'count_all_calibration' => (float)$tb_request_calibration['count_all_calibration'],
            'count_approved_calibration' => (float)$tb_request_calibration['count_approved_calibration'],
            'count_un_approve_calibration' => (float)$tb_request_calibration['count_un_approve_calibration'],
            'count_approved_finish_calibration' => (float)$tb_request_calibration['count_approved_finish_calibration'],
            'count_un_approve_finish_calibration' => (float)$tb_request_calibration['count_un_approve_finish_calibration'],
            'count_all_maintenance' => (float)$tb_suggest_maintenance['count_all_maintenance'],
            'count_approved_maintenance' => (float)$tb_suggest_maintenance['count_approved_maintenance'],
            'count_un_approve_maintenance' => (float)$tb_suggest_maintenance['count_un_approve_maintenance'],
            'count_approved_finish_maintenance' => (float)$tb_suggest_maintenance['count_approved_finish_maintenance'],
            'count_un_approve_finish_maintenance' => (float)$tb_suggest_maintenance['count_un_approve_finish_maintenance'],
            'count_all_repair' => (float)$tb_request_repair['count_all_repair'],
            'count_approved_repair' => (float)$tb_request_repair['count_approved_repair'],
            'count_un_approve_repair' => (float)$tb_request_repair['count_un_approve_repair'],
            'count_approved_finish_repair' => (float)$tb_request_repair['count_approved_finish_repair'],
            'count_un_approve_finish_repair' => (float)$tb_request_repair['count_un_approve_finish_repair'],
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function count_qa()
    {
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.items_id as items_id,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $tbPurchasesErrors = "(
            SELECT
                tbl_productions_orders_details.productions_orders_id as productions_orders_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            WHERE tbl_purchase_products.is_errors = 1 
            GROUP BY tbl_productions_orders_details.productions_orders_id
        ) tb_error";

        $this->db->select("
            COUNT(tbl_productions_orders.id) AS count_all_production,
            SUM(
                CASE 
                    WHEN COALESCE(tb_production_order_item.quantity,0) = 
                         (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0)) 
                    THEN 1 ELSE 0 
                END
            ) AS count_approved_production,
            SUM(
                CASE 
                    WHEN COALESCE(tb_production_order_item.quantity,0) != 
                         (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0)) 
                    THEN 1 ELSE 0 
                END
            ) AS count_un_approve_production
        ");
        $this->db->from('tbl_productions_orders');
        $this->db->join('tblbranch', 'tblbranch.id = tbl_productions_orders.location_id', 'inner');
        $this->db->join($tbProductionsOrderItems,
            'tb_production_order_item.productions_orders_id = tbl_productions_orders.id', 'left');
        $this->db->join($tbPurchasesErrors, 'tb_error.productions_orders_id = tbl_productions_orders.id', 'left');
        $this->db->where('tbl_productions_orders.date >=', $date_dashboard_srceen_sales);
        $dtProductionsOrders = $this->db->get()->row_array();


        $this->db->select('COUNT(DISTINCT tbl_productions_orders_items.productions_orders_id) AS total_production_items_errors');
        $this->db->from('tbl_purchase_products');
        $this->db->join('tbl_productions_orders_details',
            'tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id', 'inner');
        $this->db->join('tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
        $this->db->where('tbl_purchase_products.is_errors', 1);
        $this->db->where('tbl_purchase_products.date >=', $date_dashboard_srceen_sales);
        $total_purchase_errors = $this->db->get()->row_array();

        $subquery = "
            SELECT 1
            FROM tbl_suggest_plan_outsource_item AS spoi
            JOIN tbl_productions_orders_items AS poi 
                ON poi.items_id = spoi.item_id
            JOIN tbl_productions_orders_items_stages AS stage
                ON stage.productions_orders_id = poi.productions_orders_id
                AND stage.productions_orders_items_id = poi.id
            WHERE spoi.suggest_plan_outsource_id = tbl_suggest_plan_outsource.id
            AND poi.productions_orders_id = tbl_suggest_plan_outsource.po_id
            AND stage.final_stage = 1
            AND stage.active = 0
        ";

        $this->db->select('COUNT(*) AS count_approved_outsource');
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where("NOT EXISTS ($subquery)", null, false);
        $this->db->where('tbl_suggest_plan_outsource.date >=', $date_dashboard_srceen_sales);

        $count_approved_outsource = $this->db->get()->row_array();

        $this->db->select('COUNT(*) AS count_un_approve_outsource');
        $this->db->from('tbl_suggest_plan_outsource');
        $this->db->where("EXISTS ($subquery)", null, false);
        $this->db->where('tbl_suggest_plan_outsource.date >=', $date_dashboard_srceen_sales);
        $count_un_approve_outsource = $this->db->get()->row_array();

        $this->db->select("
            COUNT(tbl_suggest_overtime_detail.id) AS count_all_overtime,
            SUM(
                CASE 
                    WHEN tbl_suggest_overtime_detail.status = 1 THEN 1 ELSE 0 
                END
            ) AS count_approved_overtime,
            SUM(
                CASE 
                    WHEN tbl_suggest_overtime_detail.status = 0 THEN 1 ELSE 0 
                END
            ) AS count_un_approve_overtime
        ", false);
        $this->db->from('tbl_suggest_overtime');
        $this->db->join('tbl_suggest_overtime_detail',
            'tbl_suggest_overtime_detail.suggest_overtime_id = tbl_suggest_overtime.id', 'inner');
        $this->db->where('tbl_suggest_overtime_detail.date >=', date('Y-m-d', strtotime($date_dashboard_srceen_sales)));
        $tb_suggest_overtime = $this->db->get()->row_array();

        $data = [
            'count_all_production' => (float)$dtProductionsOrders['count_all_production'],
            'count_approved_production' => (float)$dtProductionsOrders['count_approved_production'],
            'count_un_approve_production' => (float)$dtProductionsOrders['count_un_approve_production'],
            'total_production_items_errors' => (float)$total_purchase_errors['total_production_items_errors'],
            'count_all_outsource' => (float)($count_approved_outsource['count_approved_outsource'] + $count_un_approve_outsource['count_un_approve_outsource']),
            'count_approved_outsource' => (float)$count_approved_outsource['count_approved_outsource'],
            'count_un_approve_outsource' => (float)$count_un_approve_outsource['count_un_approve_outsource'],
            'count_all_overtime' => (float)$tb_suggest_overtime['count_all_overtime'],
            'count_approved_overtime' => (float)$tb_suggest_overtime['count_approved_overtime'],
            'count_un_approve_overtime' => (float)$tb_suggest_overtime['count_un_approve_overtime'],
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function modal_detail_hcns($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('NHÂN VIÊN CHƯA CHECK IN');
        } elseif ($type == 2) {
            $title = lang('NHÂN VIÊN CHƯA CHECK OUT');
        } elseif ($type == 3) {
            $title = lang('NHÂN VIÊN ĐÃ CHECK IN');
        } elseif ($type == 4) {
            $title = lang('NHÂN VIÊN ĐÃ CHECK OUT');
        } elseif ($type == 5) {
            $title = lang('NHÂN VIÊN NGHỈ PHÉP');
        } elseif ($type == 6) {
            $title = lang('NHÂN VIÊN TĂNG CA');
        } elseif ($type == 7) {
            $title = lang('NHÂN VIÊN ĐI TRỄ SAU 8H SÁNG');
        } elseif ($type == 8) {
            $title = lang('NHÂN VIÊN VỀ SỚM TRƯỚC 17H CHIỀU');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_hcns', ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalHcns()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_now = date('Y-m-d');
        $aColumns = [
            'tbl_timekeeping_detail.id as id',
            'tbl_timekeeping_detail.date as date',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_timekeeping_detail';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_timekeeping_detail.staff_id',
        ];
        array_push($where, "AND tbl_timekeeping_detail.date = '" . $date_now . "'");
        if ($type == 1) {
            array_push($where, "AND EXISTS (
                SELECT 1 
                FROM tbl_timekeeping_detail_hour 
                WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                  AND tbl_timekeeping_detail_hour.hour IS NULL
                  AND tbl_timekeeping_detail_hour.type = 1
            )");
        } elseif ($type == 2) {
            array_push($where, "AND EXISTS (
                SELECT 1 
                FROM tbl_timekeeping_detail_hour 
                WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                  AND tbl_timekeeping_detail_hour.hour IS NULL
                  AND tbl_timekeeping_detail_hour.type = 2
            )");
        } elseif ($type == 3) {
            array_push($where, "AND EXISTS (
                SELECT 1 
                FROM tbl_timekeeping_detail_hour 
                WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                  AND tbl_timekeeping_detail_hour.hour IS NOT NULL
                  AND tbl_timekeeping_detail_hour.type = 1
            )");
        } elseif ($type == 4) {
            array_push($where, "AND EXISTS (
                SELECT 1 
                FROM tbl_timekeeping_detail_hour 
                WHERE tbl_timekeeping_detail_hour.timekeeping_detail_id = tbl_timekeeping_detail.id
                  AND tbl_timekeeping_detail_hour.hour IS NOT NULL
                  AND tbl_timekeeping_detail_hour.type = 2
            )");
        } elseif ($type == 5) {
            array_push($where, "AND tbl_timekeeping_detail.type != 'X'");
        } elseif ($type == 6) {
            array_push($where, "AND tbl_timekeeping_detail.count_hour_overtime > 0");
        } elseif ($type == 7) {
            array_push($where, "AND tbl_timekeeping_detail.count_late > 0");
        } elseif ($type == 8) {
            array_push($where, "AND tbl_timekeeping_detail.count_late_new > 0");
        }
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['staff_name'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_evaluate_hcns($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('HỢP ĐỒNG NHÂN VIÊN CẦN TÁI KÝ HỢP ĐỒNG');
        } elseif ($type == 2) {
            $title = lang('ĐÁNH GIÁ CẦN TÁI ĐÁNH GIÁ');
        } elseif ($type == 3) {
            $title = lang('CHỨNG NHẬN CẦN TÁI ĐÁNH GIÁ');
        } elseif ($type == 4) {
            $title = lang('CHỨNG CHỈ CẦN TÁI ĐÁNH GIÁ');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_evaluate_hcns', [
            'type' => $type,
            'title' => $title
        ]);
    }

    public function getDetailModalEvaluateHcns()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_now = date('Y-m-d');
        if ($type == 1) {
            $aColumns = [
                'tbl_contract_labor.id as id',
                'tbl_contract_labor.date_sign as date',
                'tbl_contract_labor.code as code',
                'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name'
            ];
            $sIndexColumn = 'id';
            $sTable = 'tbl_contract_labor';
            $where = [];
            $filter = [];

            $join = [
                'INNER JOIN tblstaff ON tblstaff.staffid = tbl_contract_labor.staff_id',
            ];
            array_push($where, "AND tbl_contract_labor.date_sign = '" . $date_now . "'");

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');
        } else {
            $aColumns = [
                'tbl_evaluate.id as id',
                'tbl_evaluate.date_sign as date',
                'tbl_evaluate.code_evaluate as code',
            ];
            $sIndexColumn = 'id';
            $sTable = 'tbl_evaluate';
            $where = [];
            $filter = [];

            if ($type == 2) {
                array_push($where, "AND tbl_evaluate.type = 'evaluate'");
            } elseif ($type == 3) {
                array_push($where, "AND tbl_evaluate.type = 'certification'");
            } elseif ($type == 4) {
                array_push($where, "AND tbl_evaluate.type = 'certificate'");
            }

            $join = [
            ];
            array_push($where, "AND tbl_evaluate.date_sign = '" . $date_now . "'");

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');
        }
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . ($aRow['code']) . '</div>';
            if ($type == 1) {
                $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['staff_name'] . '</div>';
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_task($type = 1, $department_id = -1)
    {
        if ($type == 1) {
            $title = lang('CÔNG VIỆC CHƯA HOÀN THÀNH');
        } else {
            $title = lang('CÔNG VIỆC ĐÃ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_task', [
            'type' => $type,
            'department_id' => $department_id,
            'title' => $title
        ]);
    }

    public function getDetailModalTask()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $department_id = $this->input->post('department_id');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbltasks.id as id',
            'tbltasks.dateadded as date',
            'tblcategory_tasks.code as task_code',
            'tbltasks.name as name',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbltasks';
        $where = [];
        $filter = [];

        $join = [
            'LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tbltasks.category_tasks'
        ];
        array_push($where, "AND tbltasks.startdate >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
            array_push($where, "AND tbltasks.status != 5");
        } else {
            array_push($where, "AND tbltasks.status = 5");
        }
        if ($department_id != -1) {
            array_push($where, "AND EXISTS (
                SELECT 1 
                FROM tbltask_department 
                WHERE tbltask_department.task_id = tbltasks.id 
                AND tbltask_department.department_id = $department_id
            )");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['task_code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['name'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_internal($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('ĐỀ XUẤT NỘI BỘ');
        } elseif ($type == 2) {
            $title = lang('ĐỀ XUẤT NỘI BỘ CHƯA HOÀN THÀNH');
        } elseif ($type == 3) {
            $title = lang('ĐỀ XUẤT NỘI BỘ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_internal',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbInternal()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.date as date',
            'tblinternal_proposal.code as code',
            'coalesce(tbl_recommended_list.name, "") as name_recommended_group'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblinternal_proposal';
        $where = [];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id'
        ];
        array_push($where, "AND tblinternal_proposal.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND (EXISTS (
                    SELECT 1 
                    FROM tbl_internal_proposal_process 
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                    AND tbl_internal_proposal_process.status = 0
                )
                OR NOT EXISTS (
                    SELECT 1
                    FROM tbl_internal_proposal_process
                    WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                ))");
        } elseif ($type == 3) {
            array_push($where, 'AND NOT EXISTS (
                SELECT 1 
                FROM tbl_internal_proposal_process 
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
                AND tbl_internal_proposal_process.status = 0
            )
             AND EXISTS (
                SELECT 1
                FROM tbl_internal_proposal_process
                WHERE tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id
             )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['name_recommended_group'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_kph($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP');
        } elseif ($type == 2) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP CHƯA HOÀN THÀNH');
        } elseif ($type == 3) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP HOÀN THÀNH');
        } elseif ($type == 4) {
            $title = lang('BÁO CÁO VI PHẠM');
        } elseif ($type == 5) {
            $title = lang('BÁO CÁO VI PHẠM CHƯA HOÀN THÀNH');
        } elseif ($type == 6) {
            $title = lang('BÁO CÁO VI PHẠM HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_kph', ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbKPH()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tblproduction_report.id as id',
            'tblproduction_report.date as date',
            'tblproduction_report.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblproduction_report';
        $where = [];
        $filter = [];

        $join = [
        ];
        array_push($where, "AND tblproduction_report.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1 || $type == 2 || $type == 3) {
            array_push($where, 'AND tblproduction_report.type_report = 1');
        } elseif ($type == 4 || $type == 5 || $type == 6) {
            array_push($where, 'AND tblproduction_report.type_report = 4');
        }
        if ($type == 1 || $type == 4) {
        } elseif ($type == 2 || $type == 5) {
            array_push($where, "AND EXISTS (
                     SELECT 1 
                    FROM tbl_process_production_report 
                    WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                    AND tbl_process_production_report.staff_process = 0
                )");
        } elseif ($type == 3 || $type == 6) {
            array_push($where, 'AND NOT EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
            )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_overtime($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('PHIẾU YÊU CẦU TĂNG CA');
        } elseif ($type == 2) {
            $title = lang('PHIẾU YÊU CẦU TĂNG CA CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('PHIẾU YÊU CẦU TĂNG CA ĐÃ DUYỆT');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_overtime',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbOvertime()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbl_request_overtime.id as id',
            'tbl_request_overtime.date as date',
            'tbl_request_overtime.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_overtime';
        $where = [];
        $filter = [];

        $join = [
        ];
        array_push($where, "AND tbl_request_overtime.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND EXISTS (
                    SELECT 1 
                    FROM tbl_request_overtime_item 
                    WHERE tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id
                    AND tbl_request_overtime_item.status = 0
                )");
        } elseif ($type == 3) {
            array_push($where, 'AND NOT EXISTS (
                SELECT 1 
                FROM tbl_request_overtime_item 
                WHERE tbl_request_overtime_item.suggest_request_id = tbl_request_overtime.id
                AND tbl_request_overtime_item.status = 0
            )');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_outsource($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('PHIẾU KẾ HOẠCH GIA CÔNG');
        } elseif ($type == 2) {
            $title = lang('PHIẾU KẾ HOẠCH GIA CÔNG CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('PHIẾU KẾ HOẠCH GIA CÔNG ĐÃ DUYỆT');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_outsource',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbOutsource()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbl_suggest_plan_outsource.id as id',
            'tbl_suggest_plan_outsource.date as date',
            'tbl_suggest_plan_outsource.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_outsource';
        $where = [];
        $filter = [];

        $join = [
        ];
        array_push($where, "AND tbl_suggest_plan_outsource.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND tbl_suggest_plan_outsource.status = 0");
        } elseif ($type == 3) {
            array_push($where, 'AND tbl_suggest_plan_outsource.status = 1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_foso($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('DỮ LIỆU TẠO MỚI');
        } elseif ($type == 2) {
            $title = lang('DỮ LIỆU HỦY');
        } elseif ($type == 3) {
            $title = lang('DỮ LIỆU CHỈNH SỬA');
        } elseif ($type == 4) {
            $title = lang('DỮ LIỆU XÓA');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_foso', ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalFOSO()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date = date('Y-m-d');

        if ($type == 2) {
            $aColumns = [
                'tbl_orders.id as id',
                'tbl_orders.date as date',
                'tbl_orders.reference_no as code',
                '"" as content',
                'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name'
            ];
            $sIndexColumn = 'id';
            $sTable = 'tbl_orders';
            $where = [];
            $filter = [];

            $join = [
                'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_orders.user_cancel'
            ];
            array_push($where, "AND DATE(date_cancel) = '" . $date . "'");
            array_push($where, "AND tbl_orders.is_cancel = 1");

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');
        } else {
            $aColumns = [
                'tblactivity_log_v2.id as id',
                'tblactivity_log_v2.date as date',
                'tblactivity_log_v2.name_obj as code',
                'tblactivity_log_v2.content as content',
                'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name'
            ];
            $sIndexColumn = 'id';
            $sTable = 'tblactivity_log_v2';
            $where = [];
            $filter = [];

            $join = [
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblactivity_log_v2.staff_id'
            ];
            array_push($where, "AND DATE(date) = '" . $date . "'");
            if ($type == 1) {
                array_push($where, "AND tblactivity_log_v2.actions = 'add'");
                array_push($where,
                    "AND tblactivity_log_v2.type_parent_obj IN ('clients', 'suppliers', 'products', 'items')");
            } elseif ($type == 3) {
                array_push($where, "AND tblactivity_log_v2.actions = 'edit'");
                array_push($where,
                    "AND tblactivity_log_v2.type_parent_obj IN ('clients', 'suppliers', 'products', 'items')");
            } elseif ($type == 4) {
                array_push($where, "AND tblactivity_log_v2.actions = 'delete'");
                array_push($where, "AND tblactivity_log_v2.type_parent_obj IN (
                 'orders',
                'quotes',
                'deliveries',
                'returned_goods',
                'internal_proposal',
                'tasks',
                'production_report',
                'purchase',
                'purchase_order',
                'import',
                'return_suppliers',
                'productions_orders',
                'business_plan',
                'invoices',
                'vouchers_coupon',
                'other_payslips_coupon',
                'purchase_invoice',
                'other_payslips',
                'suggestion',
                'transfer_warehouse',
                'tranfer_business',
                'exporting_producion',
                'purchase_product',
                'purchase_internal',
                'export_different'
                )");
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        }
        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['content'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['staff_name'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_technial_rating_machines($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('ĐÁNH GIÁ THIẾT BỊ');
        } elseif ($type == 2) {
            $title = lang('ĐÁNH GIÁ THIẾT BỊ CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('ĐÁNH GIÁ THIẾT BỊ ĐÃ DUYỆT');
        } elseif ($type == 4) {
            $title = lang('ĐÁNH GIÁ THIẾT BỊ CHƯA HOÀNH THÀNH');
        } elseif ($type == 5) {
            $title = lang('ĐÁNH GIÁ THIẾT BỊ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_technial_rating_machines',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalTechnialRatingMachines()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbl_suggest_rating_machines.id as id',
            'tbl_suggest_rating_machines.date as date',
            'tbl_suggest_rating_machines.reference_no as code',
            'tbl_machines_maintenance.note_main as note_main'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_rating_machines';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_suggest_rating_machines.machines_id',
            'LEFT JOIN tbl_machines_maintenance ON tbl_machines_maintenance.machines_id = tbl_machines.id'
        ];
        array_push($where, "AND tbl_suggest_rating_machines.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND tbl_suggest_rating_machines.status = 0");
        } elseif ($type == 3) {
            array_push($where, 'AND tbl_suggest_rating_machines.status = 1');
        } elseif ($type == 4) {
            array_push($where, 'AND tbl_suggest_rating_machines.status_finish = 0');
        } elseif ($type == 5) {
            array_push($where, 'AND tbl_suggest_rating_machines.status_finish = 1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['note_main'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_technial_calibration($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('HIỆU CHUẨN MÁY MÓC THIẾT BỊ');
        } elseif ($type == 2) {
            $title = lang('HIỆU CHUẨN MÁY MÓC THIẾT BỊ CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('HIỆU CHUẨN MÁY MÓC THIẾT BỊ ĐÃ DUYỆT');
        } elseif ($type == 4) {
            $title = lang('HIỆU CHUẨN MÁY MÓC THIẾT BỊ CHƯA HOÀNH THÀNH');
        } elseif ($type == 5) {
            $title = lang('HIỆU CHUẨN MÁY MÓC THIẾT BỊ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_technial_calibration',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalTechnialCalibration()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbl_request_calibration.id as id',
            'tbl_request_calibration.date as date',
            'tbl_request_calibration.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_calibration';
        $where = [];
        $filter = [];
        $join = [
        ];
        array_push($where, "AND tbl_request_calibration.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND tbl_request_calibration.status = 0");
        } elseif ($type == 3) {
            array_push($where, 'AND tbl_request_calibration.status = 1');
        } elseif ($type == 4) {
            array_push($where, 'AND tbl_request_calibration.status_finish = 0');
        } elseif ($type == 5) {
            array_push($where, 'AND tbl_request_calibration.status_finish = 1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_technial_maintenance($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('BẢO DƯỠNG MÁY MÓC THIẾT BỊ');
        } elseif ($type == 2) {
            $title = lang('BẢO DƯỠNG MÁY MÓC THIẾT BỊ CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('BẢO DƯỠNG MÁY MÓC THIẾT BỊ ĐÃ DUYỆT');
        } elseif ($type == 4) {
            $title = lang('BẢO DƯỠNG MÁY MÓC THIẾT BỊ CHƯA HOÀNH THÀNH');
        } elseif ($type == 5) {
            $title = lang('BẢO DƯỠNG MÁY MÓC THIẾT BỊ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_technial_maintenance',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalTechnialMaintenance()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbl_suggest_maintenance.id as id',
            'tbl_suggest_maintenance.date as date',
            'tbl_suggest_maintenance.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_maintenance';
        $where = [];
        $filter = [];
        $join = [
        ];
        array_push($where, "AND tbl_suggest_maintenance.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND tbl_suggest_maintenance.status = 0");
        } elseif ($type == 3) {
            array_push($where, 'AND tbl_suggest_maintenance.status = 1');
        } elseif ($type == 4) {
            array_push($where, 'AND tbl_suggest_maintenance.status_finish = 0');
        } elseif ($type == 5) {
            array_push($where, 'AND tbl_suggest_maintenance.status_finish = 1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_technial_repair($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('SỬA CHỮA MÁY MÓC THIẾT BỊ');
        } elseif ($type == 2) {
            $title = lang('SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('SỬA CHỮA MÁY MÓC THIẾT BỊ ĐÃ DUYỆT');
        } elseif ($type == 4) {
            $title = lang('SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA HOÀNH THÀNH');
        } elseif ($type == 5) {
            $title = lang('SỬA CHỮA MÁY MÓC THIẾT BỊ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_technial_repair',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalTechnialRepair()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tbl_request_repair.id as id',
            'tbl_request_repair.date as date',
            'tbl_request_repair.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_repair';
        $where = [];
        $filter = [];
        $join = [
        ];
        array_push($where, "AND tbl_request_repair.date >= '" . $date_dashboard_srceen_sales . "'");
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, "AND tbl_request_repair.status = 0");
        } elseif ($type == 3) {
            array_push($where, 'AND tbl_request_repair.status = 1');
        } elseif ($type == 4) {
            array_push($where, 'AND tbl_request_repair.status_finish = 0');
        } elseif ($type == 5) {
            array_push($where, 'AND tbl_request_repair.status_finish = 1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_technial_bckph($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP THIẾT BỊ');
        } elseif ($type == 2) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP THIẾT BỊ CHƯA HOÀNH THÀNH');
        } elseif ($type == 3) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP THIẾT BỊ HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_technial_bckph',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalTechnialBCKPH()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
        $aColumns = [
            'tblproduction_report.id as id',
            'tblproduction_report.date as date',
            'tblproduction_report.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblproduction_report';
        $where = [];
        $filter = [];
        $join = [
        ];
        array_push($where, "AND tblproduction_report.date >= '" . $date_dashboard_srceen_sales . "'");
        array_push($where, 'AND tblproduction_report.type_report = 1');
        array_push($where, 'AND tblproduction_report.machines_id != 0');
        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, 'AND EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )');
        } elseif ($type == 3) {
            array_push($where, "AND NOT EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_qa_production($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('LỆNH SẢN XUẤT');
        } elseif ($type == 2) {
            $title = lang('LỆNH SẢN XUẤT CHƯA HOÀNH THÀNH');
        } elseif ($type == 3) {
            $title = lang('LỆNH SẢN XUẤT HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_qa_production',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalQaProduction()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.items_id as items_id,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $tbPurchasesErrors = "(
            SELECT
                tbl_productions_orders_details.productions_orders_id as productions_orders_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            WHERE tbl_purchase_products.is_errors = 1 
            GROUP BY tbl_productions_orders_details.productions_orders_id
        ) tb_error";
        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as code',
            'tbl_products.code as code_product',
            'tbl_products.name as name_product'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_productions_orders.location_id',
            'LEFT JOIN ' . $tbProductionsOrderItems . ' ON ' . 'tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'LEFT JOIN ' . $tbPurchasesErrors . ' ON ' . 'tb_error.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id'
        ];

        array_push($where, "AND tbl_productions_orders.date >= '" . $date_dashboard_srceen_sales . "'");

        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, 'AND COALESCE(tb_production_order_item.quantity,0) != 
                 (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0))');
        } elseif ($type == 3) {
            array_push($where, 'AND COALESCE(tb_production_order_item.quantity,0) = 
                 (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0))');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code_product'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['name_product'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_qa_outsource($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('GIA CÔNG');
        } elseif ($type == 2) {
            $title = lang('GIA CÔNG CHƯA HOÀNH THÀNH');
        } elseif ($type == 3) {
            $title = lang('GIA CÔNG XUẤT HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_qa_outsource',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalQaOutsource()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $subquery = "
            SELECT 1
            FROM tbl_suggest_plan_outsource_item AS spoi
            JOIN tbl_productions_orders_items AS poi 
                ON poi.items_id = spoi.item_id
            JOIN tbl_productions_orders_items_stages AS stage
                ON stage.productions_orders_id = poi.productions_orders_id
                AND stage.productions_orders_items_id = poi.id
            WHERE spoi.suggest_plan_outsource_id = tbl_suggest_plan_outsource.id
            AND poi.productions_orders_id = tbl_suggest_plan_outsource.po_id
            AND stage.final_stage = 1
            AND stage.active = 0
        ";
        $aColumns = [
            'tbl_suggest_plan_outsource.id as id',
            'tbl_suggest_plan_outsource.date as date',
            'tbl_suggest_plan_outsource.reference_no as code'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_outsource';
        $where = [];
        $filter = [];
        $join = [
        ];

        array_push($where, "AND tbl_suggest_plan_outsource.date >= '" . $date_dashboard_srceen_sales . "'");

        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, 'AND EXISTS (' . $subquery . ') ');
        } elseif ($type == 3) {
            array_push($where, 'AND NOT EXISTS (' . $subquery . ')');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_qa_overtime($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('ĐỀ XUẤT TĂNG CA');
        } elseif ($type == 2) {
            $title = lang('ĐỀ XUẤT TĂNG CA CHƯA DUYỆT');
        } elseif ($type == 3) {
            $title = lang('ĐỀ XUẤT TĂNG CA ĐÃ DUYỆT');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_qa_overtime',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalQaOvertime()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = [
            'tbl_suggest_overtime_detail.id as id',
            'tbl_suggest_overtime_detail.date as date',
            'tbl_suggest_overtime.name as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff_name'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_overtime';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_suggest_overtime_detail ON tbl_suggest_overtime_detail.suggest_overtime_id = tbl_suggest_overtime.id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_overtime.staff_id'
        ];

        array_push($where,
            'AND tbl_suggest_overtime_detail.date >= "' . date('Y-m-d', strtotime($date_dashboard_srceen_sales)) . '"');

        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, 'AND tbl_suggest_overtime_detail.status = 0');
        } elseif ($type == 3) {
            array_push($where, 'AND tbl_suggest_overtime_detail.status = 1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['staff_name'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_qa_production_error($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('LỆNH SẢN XUẤT CÓ PHẾ');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_qa_production_error',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalQaProductionError()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as code',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_purchase_products';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id',
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items.productions_orders_id'
        ];

        array_push($where, 'AND tbl_purchase_products.date >= "' . $date_dashboard_srceen_sales . '"');
        array_push($where, 'AND tbl_purchase_products.is_errors = 1');


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [],
            'GROUP BY tbl_productions_orders_items.productions_orders_id', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_hcns_production($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('LỆNH TÁI SẢN XUẤT');
        } elseif ($type == 2) {
            $title = lang('LỆNH TÁI SẢN XUẤT CHƯA HOÀNH THÀNH');
        } elseif ($type == 3) {
            $title = lang('LỆNH TÁI SẢN XUẤT HOÀN THÀNH');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_hcns_production',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalHcnsProduction()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $tbProductionsOrderItems = "(
            SELECT
                tbl_productions_orders_items.productions_orders_id as productions_orders_id,
                tbl_productions_orders_items.items_id as items_id,
                SUM(tbl_productions_orders_items.quantity) as quantity,
                SUM(tbl_productions_orders_details.quantity_warehoused) as quantity_warehoused
            FROM tbl_productions_orders_items
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id
            INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_orders_items.plan_id
            WHERE tbl_productions_orders_details.object_type = 'orders'
            AND EXISTS (
                SELECT 1
                FROM tbl_orders
                WHERE tbl_orders.id = tbl_productions_orders_details.object_id
                AND tbl_orders.type_orders = 4
            )
            GROUP BY tbl_productions_orders_items.items_id, tbl_productions_orders_items.productions_orders_id
        ) tb_production_order_item";

        $tbPurchasesErrors = "(
            SELECT
                tbl_productions_orders_details.productions_orders_id as productions_orders_id,
                SUM(tbl_purchase_products.total_quantity) as quantity_errors
            FROM tbl_purchase_products
            JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id
            WHERE tbl_purchase_products.is_errors = 1 
            AND tbl_productions_orders_details.object_type = 'orders'
            AND EXISTS (
                SELECT 1
                FROM tbl_orders
                WHERE tbl_orders.id = tbl_productions_orders_details.object_id
                AND tbl_orders.type_orders = 4
            )
            GROUP BY tbl_productions_orders_details.productions_orders_id
        ) tb_error";

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as code',
            'tbl_products.code as code_product',
            'tbl_products.name as name_product'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_productions_orders';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tblbranch ON tblbranch.id = tbl_productions_orders.location_id',
            'LEFT JOIN ' . $tbProductionsOrderItems . ' ON tb_production_order_item.productions_orders_id = tbl_productions_orders.id',
            'LEFT JOIN ' . $tbPurchasesErrors . ' ON tb_error.productions_orders_id = tbl_productions_orders.id',
            'INNER JOIN tbl_products ON tbl_products.id = tb_production_order_item.items_id'
        ];

        array_push($where, 'AND tbl_productions_orders.date >= "' . $date_dashboard_srceen_sales . '"');

        if ($type == 1) {
        } elseif ($type == 2) {
            array_push($where, 'AND COALESCE(tb_production_order_item.quantity,0) != 
                 (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0))');
        } elseif ($type == 3) {
            array_push($where, 'AND COALESCE(tb_production_order_item.quantity,0) = 
                 (COALESCE(tb_production_order_item.quantity_warehoused,0) + COALESCE(tb_error.quantity_errors,0))');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code_product'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['name_product'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_internal_proposal_khan_hcns($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('ĐỀ XUẤT KHẨN');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_hcns_internal_proposal_khan',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalHcnsInternalProposalKhan()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';


        $aColumns = [
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.date as date',
            'tblinternal_proposal.code as code',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblinternal_proposal';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tblproduction_report ON tblproduction_report.id_internal_proposal = tblinternal_proposal.id',
        ];

        array_push($where, 'AND tblinternal_proposal.date >= "' . $date_dashboard_srceen_sales . '"');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_inventory($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('KẾ HOẠCH KIỂM KÊ');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_inventory',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbInventory()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';


        $aColumns = [
            'tblinventory.id as id',
            'tblinventory.date as date',
            'CONCAT(tblinventory.prefix,"",tblinventory.code) as code',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblinventory';
        $where = [];
        $filter = [];
        $join = [
        ];

        array_push($where, 'AND tblinventory.date >= "' . $date_dashboard_srceen_sales . '"');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_internal_de_xuat($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('KẾ HOẠCH KIỂM TOÁN NỘI BỘ');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_internal_de_xuat',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbInternalDeXuat()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';


        $aColumns = [
            'tblinternal_proposal.id as id',
            'tblinternal_proposal.date as date',
            'tblinternal_proposal.code as code',
            'coalesce(tbl_recommended_list.name, "") as name_recommended_group'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblinternal_proposal';
        $where = [];
        $filter = [];
        $join = [
            'LEFT JOIN tbl_recommended_list ON tbl_recommended_list.id = tblinternal_proposal.recommended_list_group_id',
        ];

        array_push($where, 'AND tblinternal_proposal.recommended_list_group_id = 7062');
        array_push($where, 'AND tblinternal_proposal.date >= "' . $date_dashboard_srceen_sales . '"');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['name_recommended_group'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_production_error($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('BÁO CÁO KHÔNG PHÙ HỢP CÓ LỖI');
        } elseif ($type == 2) {
            $title = lang('BÁO CÁO VI PHẠM CÓ LỖI');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_production_error',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbProductionError()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';


        $aColumns = [
            'tblproduction_report.id as id',
            'tblproduction_report.date as date',
            'tblproduction_report.reference_no as code',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblproduction_report';
        $where = [];
        $filter = [];
        $join = [
        ];

        array_push($where, 'AND tblproduction_report.date >= "' . $date_dashboard_srceen_sales . '"');
        if ($type == 1) {
            array_push($where, 'AND tblproduction_report.type_report = 1
                    AND tblproduction_report.quantity > 0');
        } elseif ($type == 2) {
            array_push($where, 'AND tblproduction_report.type_report = 4
                    AND tblproduction_report.quantity > 0');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_warehouse($type = 1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('NPL TỒN KHO');
        } elseif ($type == 2) {
            $title = lang('NPL TỒN KHO QUÁ HẠN TRÊN 6 THÁNG');
        } elseif ($type == 3) {
            $title = lang('BTP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG');
        } elseif ($type == 4) {
            $title = lang('BTP TỒN KHO');
        } elseif ($type == 5) {
            $title = lang('BTP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG');
        } elseif ($type == 6) {
            $title = lang('BTP TỒN KHO QUÁ HẠN TRÊN 12 THÁNG');
        } elseif ($type == 7) {
            $title = lang('TP TỒN KHO');
        } elseif ($type == 8) {
            $title = lang('TP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG');
        } elseif ($type == 9) {
            $title = lang('TP TỒN KHO QUÁ HẠN TRÊN 12 THÁNG');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_warehouse',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbWarehouse()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');

        if ($type == 1 || $type == 2 || $type == 3) {
            $aColumns = [
                'tbl_materials.id as id',
                'tblwarehouse_items.date_sd as date',
                'IF(tblwarehouse_items.date_sd IS NOT NULL,IF(DATEDIFF(CURDATE(), tblwarehouse_items.date_sd) > 0,DATEDIFF(CURDATE(), tblwarehouse_items.date_sd),""),"") as qua_han',
                'tbl_materials.code as code',
                'tbl_materials.name as name',
                'tblwarehouse_items.product_quantity as product_quantity'
            ];
        } else {
            $aColumns = [
                'tbl_materials.id as id',
                'tblwarehouse_items.date_sd as date',
                'IF(tblwarehouse_items.date_sd IS NOT NULL,IF(DATEDIFF(CURDATE(), tblwarehouse_items.date_sd) > 0,DATEDIFF(CURDATE(), tblwarehouse_items.date_sd),""),"") as qua_han',
                'tbl_products.code as code',
                'tbl_products.name as name',
                'tblwarehouse_items.product_quantity as product_quantity'
            ];
        }
        $sIndexColumn = 'id';
        $sTable = 'tblwarehouse_items';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion',
            'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_items.id_items AND tblwarehouse_items.type_items = "product" ',
            'LEFT JOIN tbl_materials ON tbl_materials.id = tblwarehouse_items.id_items AND tblwarehouse_items.type_items = "nvl" ',
        ];

        array_push($where, 'AND tblwarehouse_items.product_quantity >= "0.0000000001"');
        array_push($where, 'AND tblwarehouse_items.warehouse_id NOT IN (' . WAREHOUSES_SYSTEM . ')');
        if ($type == 1) {
            array_push($where, 'AND tblwarehouse_items.type_items = "nvl"');
        } elseif ($type == 2) {
            array_push($where, 'AND tblwarehouse_items.type_items = "nvl" 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)');
        } elseif ($type == 3) {
            array_push($where, 'AND tblwarehouse_items.type_items = "nvl"
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)');
        } elseif ($type == 4) {
            array_push($where, 'AND tblwarehouse_items.type_items = "product" 
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != "products"');
        } elseif ($type == 5) {
            array_push($where, 'AND tblwarehouse_items.type_items = "product" 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != "products"');
        } elseif ($type == 6) {
            array_push($where, 'AND tblwarehouse_items.type_items = "product" 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != "products"');
        } elseif ($type == 7) {
            array_push($where, 'AND tblwarehouse_items.type_items = "product" 
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = "products"');
        } elseif ($type == 8) {
            array_push($where, 'AND tblwarehouse_items.type_items = "product" 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = "products"');
        } elseif ($type == 9) {
            array_push($where, 'AND tblwarehouse_items.type_items = "product" 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = "products"');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . (!empty($aRow['date']) ? _dhau($aRow['date']) : '') . '</div>';
            $row[] = '<div class="text-center" style="width: 80px">' . $aRow['qua_han'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['name'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . formatNumber($aRow['product_quantity']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function modal_detail_ksnb_nang_suat($type = 1)
    {
        $title = '';
        $this->db->select('tbl_category_stages.*');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.id', $type);
        $categoryStage = $this->db->get()->row_array();
        $title = $categoryStage['name'] ?? '';
        $this->load->view('admin/dashboard_srceen_office/modal/modal_ksnb_nang_suat',
            ['type' => $type, 'title' => $title]);
    }

    public function getDetailModalKsnbNangSuat()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $this->db->select("
            GROUP_CONCAT(DISTINCT tbl_productions_orders.id) as po_id,
            tbl_category_stages.id as category_stages_id
        ");
        $this->db->from('tbl_productions_orders');
        $this->db->join('tbl_productions_orders_items_stages',
            'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
        $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
        $this->db->where('tbl_category_stages.id', $type);
        $this->db->where('tbl_productions_orders.date >=', $date_dashboard_srceen_sales);
        $dtProductionListsItems = $this->db->get()->row_array();
        $po_id = $dtProductionListsItems['po_id'] ?? '0';

        $aColumns = [
            'tbl_productions_orders.id as id',
            'tbl_productions_orders.date as date',
            'tbl_productions_orders.reference_no as code',
            'tbl_productions_orders_items.items_code as items_code',
            'tbl_productions_orders_items.items_name as items_name',
            'tbl_purchase_products.total_quantity as total_quantity',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_purchase_products';
        $where = [];
        $filter = [];
        $join = [
            'INNER JOIN tbl_productions_orders_items_stages ON tbl_productions_orders_items_stages.id = tbl_purchase_products.pois_id',
            'INNER JOIN tbl_stages ON tbl_stages.id = tbl_productions_orders_items_stages.stage_id',
            'INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id',
            'INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id',
        ];

        array_push($where, 'AND tbl_productions_orders.date >= "' . $date_dashboard_srceen_sales . '"');
        array_push($where, 'AND tbl_productions_orders.id IN (' . $po_id . ')');
        array_push($where, 'AND tbl_stages.check_productivity = 1');
        array_push($where, 'AND tbl_stages.category_stage_productivity = ' . $type . '');

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', [], '', '');

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-center" style="width: 150px">' . _dhau($aRow['date']) . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['items_code'] . '</div>';
            $row[] = '<div class="text-center" style="min-width: 80px">' . $aRow['items_name'] . '</div>';
            $row[] = '<div class="text-center" style="width: 100px">' . formatNumber($aRow['total_quantity']) . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function count_kpi()
    {
        $department_search = $this->input->get('department_id');
        $type_search_kpi = get_option('type_search_kpi') ?? 1;
        $month = get_option('month_kpi_setting');
        $year = get_option('year_kpi_setting');
        if ($type_search_kpi == 1) {
            $precious = null;
        } else {
            $precious = get_option('precious_kpi_setting');
        }
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }

        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }
        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            COALESCE(violate,0) as violate,
            tb_tamp.kpi_list_criteria_department_id,
            "" as rating
        ');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if ($department_search != -1) {
            $this->db->where('EXISTS (
                SELECT 1
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = ' . $department_search . '
            )');
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();

        $dtCriteriaDepartmentViolateNew = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;
            }
        }

        $count_a = 0;
        $count_a_vs1 = 0;
        $count_b = 0;
        $count_c = 0;
        $count_d = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $pointMax = 100;
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                $countedArray = [];
                if (!empty($kpi_list_criteria_department_id[0])) {
                    $countedArray = array_count_values($kpi_list_criteria_department_id);
                }
                $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
                $point = 0;
                if (!empty($countedArray)) {
                    foreach ($countedArray as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point'];
                            }
                        }
                        $maxViolations = max($violations);
                        if ($v < $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                $point += $violationsToPoint[$v];
                            }
                        } else {
                            $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }
                $pointCurrent = $pointMax - $point;
                if ($pointCurrent <= 0) {
                    $pointCurrent = 1;
                }
                $dtRating = ratingKpiDepartment($pointCurrent);
                if (!empty($dtRating)) {
                    if ($dtRating[0]['id'] == 1) {
                        $count_a += 1;
                    } elseif ($dtRating[0]['id'] == 2) {
                        $count_a_vs1 += 1;
                    } elseif ($dtRating[0]['id'] == 3) {
                        $count_b += 1;
                    } elseif ($dtRating[0]['id'] == 4) {
                        $count_c += 1;
                    } elseif ($dtRating[0]['id'] == 5) {
                        $count_d += 1;
                    }
                }
            }
        }
        $data = [
            'count_a' => (float)$count_a,
            'count_a_vs1' => (float)$count_a_vs1,
            'count_b' => (float)$count_b,
            'count_c' => (float)$count_c,
            'count_d' => (float)$count_d
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function modal_detail_kpi($type = 1, $department_id = -1)
    {
        $title = '';
        if ($type == 1) {
            $title = lang('TỔNG XẾP LOẠI A+');
        } elseif ($type == 2) {
            $title = lang('TỔNG XẾP LOẠI A');
        } elseif ($type == 3) {
            $title = lang('TỔNG XẾP LOẠI B');
        } elseif ($type == 4) {
            $title = lang('TỔNG XẾP LOẠI C');
        } elseif ($type == 5) {
            $title = lang('TỔNG XẾP LOẠI D');
        }
        if ($department_id != -1) {
            $this->db->select('tbldepartments.name as department_name');
            $this->db->from('tbldepartments');
            $this->db->where('tbldepartments.departmentid', $department_id);
            $department = $this->db->get()->row_array();
            if (!empty($department)) {
                $title .= ' - ' . $department['department_name'];
            }
        } else {
            $title .= ' - ' . lang('Tất cả');
        }
        $this->load->view('admin/dashboard_srceen_office/modal/modal_kpi',
            [
                'type' => $type,
                'title' => $title,
                'department_id' => $department_id
            ]);
    }

    public function getDetailModalKPI()
    {
        $type = $this->input->post('type');
        $department_search = $this->input->post('department_id');
        $type_search_kpi = get_option('type_search_kpi') ?? 1;
        $month = get_option('month_kpi_setting');
        $year = get_option('year_kpi_setting');
        if ($type_search_kpi == 1) {
            $precious = null;
        } else {
            $precious = get_option('precious_kpi_setting');
        }
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        $htmlDate = '';
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
            $htmlDate = 'Qúy ' . $precious . '/' . $year;
        } else {
            $htmlDate = $month.'/'.$year;
        }

        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }
        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            COALESCE(violate,0) as violate,
            tb_tamp.kpi_list_criteria_department_id,
            "" as rating
        ');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if ($department_search != -1) {
            $this->db->where('EXISTS (
                SELECT 1
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = ' . $department_search . '
            )');
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();

        $dtCriteriaDepartmentViolateNew = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;
            }
        }
        $html = '';
        $stt = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $pointMax = 100;
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                $countedArray = [];
                if (!empty($kpi_list_criteria_department_id[0])) {
                    $countedArray = array_count_values($kpi_list_criteria_department_id);
                }
                $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
                $point = 0;
                if (!empty($countedArray)) {
                    foreach ($countedArray as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point'];
                            }
                        }
                        $maxViolations = max($violations);
                        if ($v < $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                $point += $violationsToPoint[$v];
                            }
                        } else {
                            $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }
                $pointCurrent = $pointMax - $point;
                if ($pointCurrent <= 0) {
                    $pointCurrent = 1;
                }
                $dtRating = ratingKpiDepartment($pointCurrent);
                if ($type != $dtRating[0]['id']) {
                    continue;
                }
                $stt++;
                $avatar = '<a href="' . admin_url('staff/profile/' . $value['staffid']) . '">' . staff_profile_image($value['staffid'],
                        [
                            'staff-profile-image-small',
                        ]) . '</a>';
                $html .= '<tr>
                     <td class="text-center stt_all">'.($stt).'</td>
                     <td width="100px">'.$htmlDate.'</td>
                     <td style="text-align: left !important;">'.$avatar.'<a href="'.base_url('admin/kpi/view_kpi_evaluation_staff/'.$value['staffid'].'/'.$month.'/'.$year.'/'.$precious).'" class="tnh-modal">'.$value['fullname'].'</a></td>
                     <td class="text-center" style="font-weight: bold;font-size: 15px">'.$pointCurrent.'</td>
                     <td class="text-center" style="background-color: '.(!empty($dtRating) ? $dtRating[0]['color'] : '').'">'.(!empty($dtRating) ? $dtRating[0]['name'] : '').'</td>
                </tr>';
            }
        }
        $data['html'] = $html;
        echo json_encode($data);
    }

    public function count_warehouse(){
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $date_dashboard_srceen_sales = get_option('date_dashboard_srceen_sales') ? get_option('date_dashboard_srceen_sales') . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';

        $this->db->select("
            SUM(CASE WHEN tblwarehouse_items.type_items = 'nvl' THEN 1 ELSE 0 END) AS count_all_nvl,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'nvl' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    THEN 1 ELSE 0 
                END
            ) AS count_6_nvl,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'nvl' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    THEN 1 ELSE 0 
                END
            ) AS count_12_nvl,
            
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_all_btp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_6_btp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products != 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_12_btp,
             SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_all_tp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_6_tp,
            SUM(
                CASE 
                    WHEN tblwarehouse_items.type_items = 'product' 
                    AND tblwarehouse_items.date_sd IS NOT NULL 
                    AND tblwarehouse_items.date_sd <= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != 'orders'  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))
                    AND tbl_products.type_products = 'products'
                    THEN 1 ELSE 0 
                END
            ) AS count_12_tp,
        ");
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion',
            'inner');
        $this->db->join('tbl_productions_orders_details',
            'tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id', 'left');
        $this->db->join('tbl_products',
            'tbl_products.id = tblwarehouse_items.id_items AND tblwarehouse_items.type_items = "product"', 'left');
        $this->db->where('tblwarehouse_items.product_quantity >=', ' 0.0000000001');
        $this->db->where('tblwarehouse_items.warehouse_id NOT IN (' . WAREHOUSES_SYSTEM . ')');
        $tb_warehouse_items = $this->db->get()->row_array();
        $data = [
            'count_all_nvl_ksnb' => (float)$tb_warehouse_items['count_all_nvl'],
            'count_6_nvl_ksnb' => (float)$tb_warehouse_items['count_6_nvl'],
            'count_12_nvl_ksnb' => (float)$tb_warehouse_items['count_12_nvl'],
            'count_all_btp_ksnb' => (float)$tb_warehouse_items['count_all_btp'],
            'count_6_btp_ksnb' => (float)$tb_warehouse_items['count_6_btp'],
            'count_12_btp_ksnb' => (float)$tb_warehouse_items['count_12_btp'],
            'count_all_tp_ksnb' => (float)$tb_warehouse_items['count_all_tp'],
            'count_6_tp_ksnb' => (float)$tb_warehouse_items['count_6_tp'],
            'count_12_tp_ksnb' => (float)$tb_warehouse_items['count_12_tp']
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    }
}
