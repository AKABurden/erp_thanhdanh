<?php

defined('BASEPATH') or exit('No direct script access allowed');
$beginMonth =  '';
$endMonth   =  '';
$months_report = $this->ci->input->post('report_months');
$warehouse_id_array = $this->ci->input->post('warehouse_id_array');
if(empty($warehouse_id_array) || !is_array($warehouse_id_array) || count($warehouse_id_array) == 0){
    $warehouse_id_array = [-1];
}
if ($months_report != '') {
    $custom_date_select = '';
    if (is_numeric($months_report)) {
        // Last month
        if ($months_report == '1') {
            $beginMonth = date('Y-m-01', strtotime('first day of last month'));
            $endMonth   = date('Y-m-t', strtotime('last day of last month'));
        } else {
            $months_report = (int) $months_report;
            $months_report--;
            $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
            $endMonth   = date('Y-m-t');
        }
    } elseif ($months_report == 'this_month') {
        $beginMonth = date('Y-m-01');
        $endMonth   = date('Y-m-t');
    } elseif ($months_report == 'this_year') {
        $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
        $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
    } elseif ($months_report == 'last_year') {
        $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
        $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
    } elseif ($months_report == 'custom') {
        $from_date = to_sql_date($this->ci->input->post('report_from'));
        $to_date   = to_sql_date($this->ci->input->post('report_to'));
        if ($from_date == $to_date) {
            $beginMonth =  $to_date;
            $endMonth   =  $to_date;
        } else {
            $beginMonth =  $from_date;
            $endMonth   =  $to_date;
        }
    }
    // $this->db->where($custom_date_select);
}
$beginMonth = !empty($beginMonth) ? $beginMonth : '0000-00-00';
$endMonth   = !empty($endMonth) ? $endMonth : '9999-12-31';

$warehouse_id = $this->ci->input->post('warehouse_id_array');
$custom_item_select = $this->ci->input->post('custom_item_select_inventory');
$type_items = $this->ci->input->post('type_items_new');
$type_itemss = $this->ci->input->post('type_itemss');
$category_search = $this->ci->input->post('category_search_new');
if(empty($warehouse_id) || !is_array($warehouse_id) || count($warehouse_id) == 0){
    $warehouse_id = [-1];
}
// $aColumns = [
//     '11',
//     'tblwarehouse_product.id',
//     'tblwarehouse_product.type_items',
//     '1',
//     '2',
//     '4',
//     '5',
//     '6',
//     '7',
//     '8',
//     '9',
//     '10',
// ];
// $sIndexColumn = 'id';
// $sTable       = db_prefix() . 'warehouse_product';
$where = array();

if ($type_itemss == 'product') {
    $aColumns = [
        'cp.name as category_name',
        'tbl_products.code as item_code',
        'tbl_products.name as item_name',
        'u.unit as unit_name_stock',
        '(mv.sl_dk - mv.sl_dk_x) AS sl_dk',
        '(mv.gt_dk - mv.gt_dk_x) AS gt_dk',
        'mv.sl_nhap',
        'mv.gt_nhap',
        'mv.sl_xuat',
        'mv.gt_xuat',
        '((mv.sl_dk - mv.sl_dk_x) + mv.sl_nhap - mv.sl_xuat) AS sl_ck',
        '((mv.gt_dk - mv.gt_dk_x) + mv.gt_nhap - mv.gt_xuat) AS gt_ck'
    ];

    $sIndexColumn = 'id';
    $sTable = 'tbl_products';
    if (!empty($custom_item_select)) {
        array_push($where, 'AND tbl_products.id =', $custom_item_select);
    }
} elseif ($type_itemss == 'nvl') {
    $aColumns = [
        'ci.name as category_name',
        'tbl_materials.code as item_code',
        'tbl_materials.name as item_name',
        'u.unit AS unit_name_stock',
        '(mv.sl_dk - mv.sl_dk_x) AS sl_dk',
        '(mv.gt_dk - mv.gt_dk_x) AS gt_dk',
        'mv.sl_nhap',
        'mv.gt_nhap',
        'mv.sl_xuat',
        'mv.gt_xuat',
        '((mv.sl_dk - mv.sl_dk_x) + mv.sl_nhap - mv.sl_xuat) AS sl_ck',
        '((mv.gt_dk - mv.gt_dk_x) + mv.gt_nhap - mv.gt_xuat) AS gt_ck'
    ];

    $sIndexColumn = 'id';
    $sTable = 'tbl_materials';
    if (!empty($custom_item_select)) {
        array_push($where, 'AND tbl_materials.id =', $custom_item_select);
    }
}
if ($warehouse_id == -1) {
    $warehouse_id = 0;
}



// if (!empty($category_search)) {
//     $category_search_nvl = array();
//     $category_search_tools = array();
//     foreach ($category_search as $key => $value) {
//         $check_value = explode('_', $value);
//         if ($check_value[0] == 'nvl') {
//             $category_search_nvl[] = $check_value[1];
//         }
//         if ($check_value[0] == 'product') {
//             $category_search_tools[] = $check_value[1];
//         }
//     }

//     if (!empty($category_search_nvl) && !empty($category_search_tools)) {
//         array_push($where, 'AND (tbl_materials.category_id IN (' . implode(',', $category_search_nvl) . ') or tbl_products.category_id IN (' . implode(',', $category_search_tools) . '))');
//     } else
//     if (!empty($category_search_nvl) && empty($category_search_tools)) {
//         array_push($where, 'AND (tbl_materials.category_id IN (' . implode(',', $category_search_nvl) . '))');
//     } elseif (empty($category_search_nvl) && !empty($category_search_tools)) {
//         array_push($where, 'AND (tbl_products.category_id IN (' . implode(',', $category_search_tools) . '))');
//     }
// }

// Chuẩn hóa danh sách kho (nếu có)
$whList = '';
if (!empty($warehouse_id) && is_array($warehouse_id)) {
    $whList = implode(',', array_map('intval', $warehouse_id));
} elseif (is_numeric($warehouse_id) && (int)$warehouse_id > 0) {
    $whList = (string)intval($warehouse_id);
}

// Điều kiện kho theo từng bảng/alias
$whImport            = $whList ? " AND i.warehouse_id IN ($whList) "                  : '';
$whAdjustOut            = $whList ? " AND a.warehouse_id IN ($whList) "                  : '';
$whAdjustIn            = $whList ? " AND a.warehouse_id IN ($whList) "                  : '';
$whTransferIn           = $whList ? " AND twd.warehouses_to IN ($whList) "                : '';
$whTransferOut          = $whList ? " AND twd.warehouses_id IN ($whList) "                : '';
$whPurchaseProducts  = $whList ? " AND pp.warehouse_id IN ($whList) "                  : '';
$whPurchaseInternal  = $whList ? " AND pi.warehouse_id IN ($whList) "                  : '';
$whReturnedGoods     = $whList ? " AND rgi.warehouse_id IN ($whList) "                 : ''; // items table
$whDelivery          = $whList ? " AND di.warehouse_id IN ($whList) "                 : ''; // items table
$whReturnSuppliers   = $whList ? " AND rs.warehouse_id IN ($whList) "                  : '';
$whSuggestExporting     = $whList ? " AND sei.warehouse_item_id IN ($whList) "            : '';
$whExportDifferent   = $whList ? " AND edi.warehouses_id IN ($whList) "                : '';

// $subQuery = "
//     SELECT product_id, type_items,
//            SUM(sl_dk)   AS sl_dk,
//            SUM(gt_dk)   AS gt_dk,
//            SUM(sl_dk_x) AS sl_dk_x,
//            SUM(gt_dk_x) AS gt_dk_x,
//            SUM(sl_nhap) AS sl_nhap,
//            SUM(gt_nhap) AS gt_nhap,
//            SUM(sl_xuat) AS sl_xuat,
//            SUM(gt_xuat) AS gt_xuat
//     FROM (
//         /* ===================== IMPORT ===================== */

//         /* Nhập mua */
//         SELECT ii.product_id, ii.type AS type_items,
//                SUM(CASE WHEN i.warehouseman_date <  '{$beginMonth}' THEN ii.quantity_stock ELSE 0 END)                         AS sl_dk,
//                SUM(CASE WHEN i.warehouseman_date <  '{$beginMonth}' THEN ii.quantity_payment * ii.price ELSE 0 END)              AS gt_dk,
//                0 AS sl_dk_x, 0 AS gt_dk_x,
//                SUM(CASE WHEN i.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ii.quantity_stock ELSE 0 END)  AS sl_nhap,
//                SUM(CASE WHEN i.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ii.quantity_payment * ii.price ELSE 0 END) AS gt_nhap,
//                0 AS sl_xuat, 0 AS gt_xuat
//         FROM tblimport_items ii
//         JOIN tblimport i ON i.id = ii.id_import AND i.warehouseman_id != 0
//         WHERE 1=1 {$whImport}
//         GROUP BY ii.product_id, ii.type

//         UNION ALL
//         /* Điều chỉnh tăng */
//         SELECT ai.product_id, ai.type AS type_items,
//                SUM(CASE WHEN a.date_create <  '{$beginMonth}' THEN ai.quantity_net ELSE 0 END)                   AS sl_dk,
//                SUM(CASE WHEN a.date_create <  '{$beginMonth}' THEN ai.quantity_net * ai.price ELSE 0 END)        AS gt_dk,
//                0, 0,
//                SUM(CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net ELSE 0 END)             AS sl_nhap,
//                SUM(CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net * ai.price ELSE 0 END)  AS gt_nhap,
//                0, 0
//         FROM tbladjusted_items ai
//         JOIN tbladjusted a ON a.id = ai.id_adjusted AND a.type = 1
//         WHERE 1=1 {$whAdjust}
//         GROUP BY ai.product_id, ai.type

//         UNION ALL
//         /* Chuyển kho nhận */
//         SELECT ti.id_items AS product_id, ti.type AS type_items,
//                SUM(CASE WHEN t.warehouseman_date <  '{$beginMonth}' THEN ti.quantity_net ELSE 0 END),
//                SUM(CASE WHEN t.warehouseman_date <  '{$beginMonth}' THEN ti.quantity_net * ti.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN t.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ti.quantity_net ELSE 0 END),
//                SUM(CASE WHEN t.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ti.quantity_net * ti.price ELSE 0 END),
//                0, 0
//         FROM tbltransfer_warehouse_detail ti
//         JOIN tbltransfer_warehouse t ON t.id = ti.id_transfer AND t.warehouseman_id != 0
//         WHERE 1=1 {$whTransIn}
//         GROUP BY ti.id_items, ti.type

//         UNION ALL
//         /* Nhập kho thành phẩm */
//         SELECT pi.item_id AS product_id,
//          CASE pi.type_item
//             WHEN 'products'       THEN 'product'
//             WHEN 'materials'      THEN 'nvl'
//             WHEN 'tools_supplies' THEN 'tools'
//         END AS type_items,
//                SUM(CASE WHEN p.date_warehouseman <  '{$beginMonth}' THEN pi.quantity ELSE 0 END),
//                SUM(CASE WHEN p.date_warehouseman <  '{$beginMonth}' THEN pi.quantity * pi.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN p.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN pi.quantity ELSE 0 END),
//                SUM(CASE WHEN p.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN pi.quantity * pi.price ELSE 0 END),
//                0, 0
//         FROM tbl_purchase_product_items pi
//         JOIN tbl_purchase_products p ON p.id = pi.purchase_product_id AND p.warehouseman_id != 0
//         WHERE 1=1 {$whPurchaseProducts}
//         GROUP BY pi.item_id, pi.type_item

//         UNION ALL
//         /* Nhập kho phế liệu (internal) */
//         SELECT ii.item_id AS product_id, 
//                 CASE ii.type_item
//                     WHEN 'products'       THEN 'product'
//                     WHEN 'materials'      THEN 'nvl'
//                     WHEN 'tools_supplies' THEN 'tools'
//                 END AS type_items,
//                SUM(CASE WHEN i.date_warehouseman <  '{$beginMonth}' THEN ii.quantity ELSE 0 END),
//                SUM(CASE WHEN i.date_warehouseman <  '{$beginMonth}' THEN ii.quantity * ii.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN i.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ii.quantity ELSE 0 END),
//                SUM(CASE WHEN i.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ii.quantity * ii.price ELSE 0 END),
//                0, 0
//         FROM tbl_purchase_internal_items ii
//         JOIN tbl_purchase_internal i ON i.id = ii.purchase_internal_id AND i.warehouseman_id != 0
//         WHERE 1=1 {$whPurchaseInternal}
//         GROUP BY ii.item_id, ii.type_item

//         UNION ALL
//         /* Hàng khách trả lại */
//         SELECT ri.item_id AS product_id, 
//                 CASE ri.type_item
//                     WHEN 'products'       THEN 'product'
//                     WHEN 'materials'      THEN 'nvl'
//                     WHEN 'tools_supplies' THEN 'tools'
//                 END AS type_items,
//                SUM(CASE WHEN r.warehouseman_date <  '{$beginMonth}' THEN ri.quantity ELSE 0 END),
//                SUM(CASE WHEN r.warehouseman_date <  '{$beginMonth}' THEN ri.quantity * ri.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN r.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ri.quantity ELSE 0 END),
//                SUM(CASE WHEN r.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ri.quantity * ri.price ELSE 0 END),
//                0, 0
//         FROM tbl_returned_goods_items ri
//         JOIN tbl_returned_goods r ON r.id = ri.returned_goods_id AND r.warehouseman_id != 0
//         WHERE 1=1 {$whReturnedGoods}
//         GROUP BY ri.item_id, ri.type_item

//         /* ===================== EXPORT ===================== */

//         UNION ALL
//         /* Xuất kho bán / xuất khác chuẩn (delivery) */
//         SELECT di.item_id AS product_id, 
//                 CASE di.type_item
//                     WHEN 'products'       THEN 'product'
//                     WHEN 'materials'      THEN 'nvl'
//                     WHEN 'tools_supplies' THEN 'tools'
//                 END AS type_items,
//                0, 0,
//                SUM(CASE WHEN d.date_warehouseman <  '{$beginMonth}' THEN di.quantity_stock ELSE 0 END),
//                SUM(CASE WHEN d.date_warehouseman <  '{$beginMonth}' THEN di.quantity_stock * di.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN d.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN di.quantity_stock ELSE 0 END),
//                SUM(CASE WHEN d.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN di.quantity_stock * di.price ELSE 0 END)
//         FROM tbl_delivery_items di
//         JOIN tbl_deliveries d ON d.id = di.delivery_id AND d.warehouseman_id != 0
//         WHERE 1=1 {$whDelivery}
//         GROUP BY di.item_id, di.type_item

//         UNION ALL
//         /* Trả hàng NCC (export ra khỏi kho) */
//         SELECT ri.product_id, ri.type AS type_items,
//                0, 0,
//                SUM(CASE WHEN r.data_warehouseman <  '{$beginMonth}' THEN ri.quantity_net ELSE 0 END),
//                SUM(CASE WHEN r.data_warehouseman <  '{$beginMonth}' THEN ri.quantity_net * ri.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN r.data_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ri.quantity_net ELSE 0 END),
//                SUM(CASE WHEN r.data_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ri.quantity_net * ri.price ELSE 0 END)
//         FROM tblreturn_suppliers_items ri
//         JOIN tblreturn_suppliers r ON r.id = ri.id_return AND r.warehouseman_id != 0
//         WHERE 1=1 {$whReturnSuppliers}
//         GROUP BY ri.product_id, ri.type

//         UNION ALL
//         /* Điều chỉnh giảm */
//         SELECT ai.product_id, ai.type AS type_items,
//                0, 0,
//                SUM(CASE WHEN a.date_create <  '{$beginMonth}' THEN ai.quantity_net ELSE 0 END),
//                SUM(CASE WHEN a.date_create <  '{$beginMonth}' THEN ai.quantity_net * ai.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net ELSE 0 END),
//                SUM(CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net * ai.price ELSE 0 END)
//         FROM tbladjusted_items ai
//         JOIN tbladjusted a ON a.id = ai.id_adjusted AND a.type = 2
//         WHERE 1=1 {$whAdjust}
//         GROUP BY ai.product_id, ai.type

//         UNION ALL
//         /* Chuyển kho đi */
//         SELECT ti.id_items AS product_id, ti.type AS type_items,
//                0, 0,
//                SUM(CASE WHEN t.warehouseman_date <  '{$beginMonth}' THEN ti.quantity_net ELSE 0 END),
//                SUM(CASE WHEN t.warehouseman_date <  '{$beginMonth}' THEN ti.quantity_net * ti.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN t.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ti.quantity_net ELSE 0 END),
//                SUM(CASE WHEN t.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ti.quantity_net * ti.price ELSE 0 END)
//         FROM tbltransfer_warehouse_detail ti
//         JOIN tbltransfer_warehouse t ON t.id = ti.id_transfer AND t.warehouseman_id != 0
//         WHERE 1=1 {$whTransOut}
//         GROUP BY ti.id_items, ti.type

//         UNION ALL
//         /* Xuất kho sản xuất */
//         SELECT si.item_id AS product_id, 
//                 CASE si.type_item
//                     WHEN 'products'       THEN 'product'
//                     WHEN 'materials'      THEN 'nvl'
//                     WHEN 'tools_supplies' THEN 'tools'
//                 END AS type_items,
//                0, 0,
//                SUM(CASE WHEN s.date_warehouseman <  '{$beginMonth}' THEN si.quantity_warehouse ELSE 0 END),
//                SUM(CASE WHEN s.date_warehouseman <  '{$beginMonth}' THEN si.quantity_warehouse * si.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN s.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN si.quantity_warehouse ELSE 0 END),
//                SUM(CASE WHEN s.date_warehouseman BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN si.quantity_warehouse * si.price ELSE 0 END)
//         FROM tbl_suggest_exporting_items si
//         JOIN tbl_suggest_exporting s ON s.id = si.suggest_exporting_id AND s.warehouseman_id != 0
//         WHERE 1=1 {$whSuggestExport}
//         GROUP BY si.item_id, si.type_item

//         UNION ALL
//         /* Xuất khác (export_different) */
//         SELECT ei.product_id, ei.type AS type_items,
//                0, 0,
//                SUM(CASE WHEN e.warehouseman_date <  '{$beginMonth}' THEN ei.quantity_net ELSE 0 END),
//                SUM(CASE WHEN e.warehouseman_date <  '{$beginMonth}' THEN ei.quantity_net * ei.price ELSE 0 END),
//                0, 0,
//                SUM(CASE WHEN e.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ei.quantity_net ELSE 0 END),
//                SUM(CASE WHEN e.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ei.quantity_net * ei.price ELSE 0 END)
//         FROM tbltblexport_different_items ei
//         JOIN tblexport_different e ON e.id = ei.id_export_different AND e.warehouseman_id != 0
//         WHERE 1=1 {$whExportDifferent}
//         GROUP BY ei.product_id, ei.type
//     ) AS all_mv
//     GROUP BY product_id, type_items
// ";
$subQuery = "
SELECT product_id, type_items,
       SUM(sl_dk) AS sl_dk,
       SUM(gt_dk) AS gt_dk,
       SUM(sl_dk_x) AS sl_dk_x,
       SUM(gt_dk_x) AS gt_dk_x,
       SUM(sl_nhap) AS sl_nhap,
       SUM(gt_nhap) AS gt_nhap,
       SUM(sl_xuat) AS sl_xuat,
       SUM(gt_xuat) AS gt_xuat
FROM (
    /* ================== NHẬP ================== */
    SELECT product_id, '{$type_itemss}' AS type_items,
           SUM(sl_dk) AS sl_dk, SUM(gt_dk) AS gt_dk,
           0 AS sl_dk_x, 0 AS gt_dk_x,
           SUM(sl_nhap) AS sl_nhap, SUM(gt_nhap) AS gt_nhap,
           0 AS sl_xuat, 0 AS gt_xuat
    FROM (
        /* Nhập mua */
        SELECT ii.product_id,
               CASE WHEN i.warehouseman_date < '{$beginMonth}' THEN ii.quantity_stock ELSE 0 END AS sl_dk,
               CASE WHEN i.warehouseman_date < '{$beginMonth}' THEN ii.quantity_payment * ii.price ELSE 0 END AS gt_dk,
               CASE WHEN i.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ii.quantity_stock ELSE 0 END AS sl_nhap,
               CASE WHEN i.warehouseman_date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ii.quantity_payment * ii.price ELSE 0 END AS gt_nhap
        FROM tblimport i
        STRAIGHT_JOIN tblimport_items ii ON i.id = ii.id_import
        WHERE i.warehouseman_id != 0
          {$whImport}
          AND ii.type = '{$type_itemss}'

        UNION ALL

        /* Điều chỉnh tăng */
        SELECT ai.product_id,
               CASE WHEN a.date_create < '{$beginMonth}' THEN ai.quantity_net ELSE 0 END,
               CASE WHEN a.date_create < '{$beginMonth}' THEN ai.quantity_net * ai.price ELSE 0 END,
               CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net ELSE 0 END,
               CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net * ai.price ELSE 0 END
        FROM tbladjusted a
        STRAIGHT_JOIN tbladjusted_items ai ON a.id = ai.id_adjusted
        WHERE a.type = 1
          {$whAdjustIn}
          AND ai.type = '{$type_itemss}'

        UNION ALL

        /* Chuyển kho nhận */
        SELECT twd.id_items AS product_id,
               CASE WHEN tw.date < '{$beginMonth}' THEN twd.quantity_net ELSE 0 END,
               CASE WHEN tw.date < '{$beginMonth}' THEN twd.quantity_net * twd.price ELSE 0 END,
               CASE WHEN tw.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN twd.quantity_net ELSE 0 END,
               CASE WHEN tw.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN twd.quantity_net * twd.price ELSE 0 END
        FROM tbltransfer_warehouse tw
        STRAIGHT_JOIN tbltransfer_warehouse_detail twd ON tw.id = twd.id_transfer
        WHERE tw.warehouseman_id != 0 AND twd.type = '{$type_itemss}'
          {$whTransferIn}

        UNION ALL

        /* Nhập thành phẩm */
        SELECT ppi.item_id AS product_id,
               CASE WHEN pp.date < '{$beginMonth}' THEN ppi.quantity ELSE 0 END,
               CASE WHEN pp.date < '{$beginMonth}' THEN ppi.quantity * ppi.price ELSE 0 END,
               CASE WHEN pp.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ppi.quantity ELSE 0 END,
               CASE WHEN pp.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ppi.quantity * ppi.price ELSE 0 END
        FROM tbl_purchase_products pp
        STRAIGHT_JOIN tbl_purchase_product_items ppi ON pp.id = ppi.purchase_product_id
        WHERE pp.warehouseman_id != 0 AND ppi.type_item = IF('{$type_itemss}' = 'product', 'products', 'materials')
          {$whPurchaseProducts}

        UNION ALL

        /* Nhập phế liệu */
        SELECT pii.item_id AS product_id,
               CASE WHEN pi.date < '{$beginMonth}' THEN pii.quantity ELSE 0 END,
               CASE WHEN pi.date < '{$beginMonth}' THEN pii.quantity * pii.price ELSE 0 END,
               CASE WHEN pi.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN pii.quantity ELSE 0 END,
               CASE WHEN pi.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN pii.quantity * pii.price ELSE 0 END
        FROM tbl_purchase_internal pi
        STRAIGHT_JOIN tbl_purchase_internal_items pii ON pi.id = pii.purchase_internal_id
        WHERE pi.warehouseman_id != 0 AND  pii.type_item = IF('{$type_itemss}' = 'product', 'products', 'materials')
          {$whPurchaseInternal}

        UNION ALL

        /* Khách trả hàng */
        SELECT rgi.item_id AS product_id,
               CASE WHEN rg.date < '{$beginMonth}' THEN rgi.quantity ELSE 0 END,
               CASE WHEN rg.date < '{$beginMonth}' THEN rgi.quantity * rgi.price ELSE 0 END,
               CASE WHEN rg.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN rgi.quantity ELSE 0 END,
               CASE WHEN rg.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN rgi.quantity * rgi.price ELSE 0 END
        FROM tbl_returned_goods rg
        STRAIGHT_JOIN tbl_returned_goods_items rgi ON rg.id = rgi.returned_goods_id
        WHERE rg.warehouseman_id != 0 AND   rgi.type_item = IF('{$type_itemss}' = 'product', 'products', 'materials')
          {$whReturnedGoods}
    ) AS in_all
    GROUP BY product_id

    UNION ALL

    /* ================== XUẤT ================== */
    SELECT product_id, '{$type_itemss}' AS type_items,
           0 AS sl_dk, 0 AS gt_dk,
           SUM(sl_dk_x) AS sl_dk_x, SUM(gt_dk_x) AS gt_dk_x,
           0 AS sl_nhap, 0 AS gt_nhap,
           SUM(sl_xuat) AS sl_xuat, SUM(gt_xuat) AS gt_xuat
    FROM (
        /* Xuất bán */
        SELECT di.item_id AS product_id,
               CASE WHEN d.date < '{$beginMonth}' THEN di.quantity_stock ELSE 0 END AS sl_dk_x,
               CASE WHEN d.date < '{$beginMonth}' THEN di.quantity_stock * di.price ELSE 0 END AS gt_dk_x,
               CASE WHEN d.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN di.quantity_stock ELSE 0 END AS sl_xuat,
               CASE WHEN d.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN di.quantity_stock * di.price ELSE 0 END AS gt_xuat
        FROM tbl_deliveries d
        STRAIGHT_JOIN tbl_delivery_items di ON d.id = di.delivery_id
        WHERE d.warehouseman_id != 0 AND di.type_item = IF('{$type_itemss}' = 'product', 'products', 'materials')
          {$whDelivery}

        UNION ALL

        /* Điều chỉnh giảm */
        SELECT ai.product_id,
               CASE WHEN a.date_create < '{$beginMonth}' THEN ai.quantity_net ELSE 0 END,
               CASE WHEN a.date_create < '{$beginMonth}' THEN ai.quantity_net * ai.price ELSE 0 END,
               CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net ELSE 0 END,
               CASE WHEN a.date_create BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN ai.quantity_net * ai.price ELSE 0 END
        FROM tbladjusted a
        STRAIGHT_JOIN tbladjusted_items ai ON a.id = ai.id_adjusted
        WHERE a.type = 2
          {$whAdjustOut}
          AND ai.type = '{$type_itemss}'

        UNION ALL

        /* Trả NCC */
        SELECT rsi.product_id AS product_id,
               CASE WHEN rs.date < '{$beginMonth}' THEN rsi.quantity_net ELSE 0 END,
               CASE WHEN rs.date < '{$beginMonth}' THEN rsi.quantity_net * rsi.price ELSE 0 END,
               CASE WHEN rs.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN rsi.quantity_net ELSE 0 END,
               CASE WHEN rs.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN rsi.quantity_net * rsi.price ELSE 0 END
        FROM tblreturn_suppliers rs
        STRAIGHT_JOIN tblreturn_suppliers_items rsi ON rs.id = rsi.id_return
        WHERE rs.warehouseman_id != 0 AND  rsi.type = '{$type_itemss}'
          {$whReturnSuppliers}

        UNION ALL

        /* Chuyển kho đi */
        SELECT twd.id_items AS product_id,
               CASE WHEN tw.date < '{$beginMonth}' THEN twd.quantity_net ELSE 0 END,
               CASE WHEN tw.date < '{$beginMonth}' THEN twd.quantity_net * twd.price ELSE 0 END,
               CASE WHEN tw.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN twd.quantity_net ELSE 0 END,
               CASE WHEN tw.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN twd.quantity_net * twd.price ELSE 0 END
        FROM tbltransfer_warehouse tw
        STRAIGHT_JOIN tbltransfer_warehouse_detail twd ON tw.id = twd.id_transfer
        WHERE tw.warehouseman_id != 0  AND twd.type = '{$type_itemss}'
          {$whTransferOut}

        UNION ALL

        /* Xuất sản xuất */
        SELECT sei.item_id AS product_id,
               CASE WHEN se.date < '{$beginMonth}' THEN sei.quantity_warehouse ELSE 0 END,
               CASE WHEN se.date < '{$beginMonth}' THEN sei.quantity_warehouse * sei.price ELSE 0 END,
               CASE WHEN se.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN sei.quantity_warehouse ELSE 0 END,
               CASE WHEN se.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN sei.quantity_warehouse * sei.price ELSE 0 END
        FROM tbl_suggest_exporting se
        STRAIGHT_JOIN tbl_suggest_exporting_items sei ON se.id = sei.suggest_exporting_id
        WHERE se.warehouseman_id != 0  AND sei.type_item  = IF('{$type_itemss}' = 'product', 'products', 'materials')
          {$whSuggestExporting}

        UNION ALL

        /* Xuất khác */
        SELECT edi.product_id AS product_id,
               CASE WHEN ed.date < '{$beginMonth}' THEN edi.quantity_net ELSE 0 END,
               CASE WHEN ed.date < '{$beginMonth}' THEN edi.quantity_net * edi.price ELSE 0 END,
               CASE WHEN ed.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN edi.quantity_net ELSE 0 END,
               CASE WHEN ed.date BETWEEN '{$beginMonth}' AND '{$endMonth}' THEN edi.quantity_net * edi.price ELSE 0 END
        FROM tblexport_different ed
        STRAIGHT_JOIN tbltblexport_different_items edi ON ed.id = edi.id_export_different
        WHERE ed.warehouseman_id != 0  AND  edi.type = '{$type_itemss}'
          {$whExportDifferent}
    ) AS out_all
    GROUP BY product_id
) AS all_mv
GROUP BY product_id, type_items
";

$whListKho = '';
if (!empty($whList)) {
    $whListKho = " AND wi.warehouse_id IN ($whList) ";
}
if ($type_itemss == 'product') {
    $join = [
        "LEFT JOIN tbl_category_products cp ON cp.id = tbl_products.category_id",
        "LEFT JOIN tblunits u ON u.unitid = tbl_products.conversion_unit",
        "INNER JOIN ({$subQuery}) AS mv ON mv.product_id = tbl_products.id AND mv.type_items = 'product'",
        'INNER JOIN (
            SELECT DISTINCT wi.id_items
            FROM tblwarehouse_items wi WHERE wi.type_items = "product" ' . $whListKho . '
        ) had_import ON had_import.id_items = tbl_products.id',
    ];

    $group_by = "";
    // $group_by = "GROUP BY tblwarehouse_product.product_id, tblwarehouse_product.type_items";

    $result = data_tables_init(
        $aColumns,
        $sIndexColumn,
        $sTable,
        $join,
        $where,
        ['tbl_products.id as product_id', 'CONCAT("product") as type_items'],
        $group_by
    );
} elseif ($type_itemss == 'nvl') {

    $join = [
        "LEFT JOIN tbl_category_items ci ON ci.id = tbl_materials.category_id",
        "LEFT JOIN tblunits u ON u.unitid = tbl_materials.standard_unit",
        "INNER JOIN ({$subQuery}) AS mv ON mv.product_id = tbl_materials.id AND mv.type_items = 'nvl'",
        'INNER JOIN (
            SELECT DISTINCT wi.id_items
            FROM tblwarehouse_items wi WHERE wi.type_items = "nvl" ' . $whListKho . '
        ) had_import ON had_import.id_items = tbl_materials.id ',
    ];

    $group_by = "";
    $result = data_tables_init(
        $aColumns,
        $sIndexColumn,
        $sTable,
        $join,
        $where,
        ['tbl_materials.id  as product_id', 'CONCAT("nvl") as type_items'],
        $group_by
    );
}
$output  = $result['output'];
$rResult = $result['rResult'];

// usort($rResult, ch_make_cmp(['tblwarehouse_product.type_items' => "desc", 'product_id' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
$footer_data = array(
    'sldk' => 0,
    'gtdk' => 0,
    'slnk' => 0,
    'gtnk' => 0,
    'slxk' => 0,
    'gtxk' => 0,
    'slck' => 0,
    'gtck' => 0,
);
$itemsToCost = []; // ['product' => [id1, id2,...], 'nvl' => [...]]
foreach ($rResult as $row) {
    $t = $row['type_items']; // 'product' | 'nvl'
    $id = (int)$row['product_id'];
    if ($id > 0) {
        $itemsToCost[$t][$id] = true; // dùng set để unique
    }
}
foreach ($itemsToCost as $t => $set) {
    $itemsToCost[$t] = array_keys($set);
}
$costMap = computeCostValuesBatch(
    $itemsToCost,
    $type_itemss,                          // 'product' hoặc 'nvl' (đang lọc 1 loại ở màn hình)
    $beginMonth,
    $endMonth,
    (array)$warehouse_id_array             // mảng kho được chọn (có thể rỗng = tất cả)
);
foreach ($rResult as $r => $aRow) {
    // ======= 3.1 Ghi đè gt_* từ costMap =======
    $t = $aRow['type_items'];           // 'product' | 'nvl'
    $id = (int)$aRow['product_id'];

    $sl_dk   = (float)$aRow['sl_dk'];
    $sl_nhap = (float)$aRow['sl_nhap'];
    $sl_xuat = (float)$aRow['sl_xuat'];

    $gt_dk = $aRow['gt_dk'] ?? 0;
    $gt_nhap = $aRow['gt_nhap'] ?? 0;
    $gt_xuat = $aRow['gt_xuat'] ?? 0;

    if (isset($costMap[$t][$id])) {
        $gt_dk   = (float)$costMap[$t][$id]['gt_dk'];
        $gt_nhap = (float)$costMap[$t][$id]['gt_nhap'];
        $gt_xuat = (float)$costMap[$t][$id]['gt_xuat'];
    }

    // Tính gt_ck theo công thức chuẩn
    $gt_ck = $gt_dk + $gt_nhap - $gt_xuat;
    $sl_ck = $sl_dk + $sl_nhap - $sl_xuat;

    // ======= 3.2 Bơm lại vào $aRow để formatter cột dùng =======
    $aRow['gt_dk']  = $gt_dk;
    $aRow['gt_nhap'] = $gt_nhap;
    $aRow['gt_xuat'] = $gt_xuat;
    $aRow['gt_ck']  = $gt_ck;

    // ======= 3.3 (nếu muốn) cập nhật footer sums =======
    $footer_data['sldk'] += $sl_dk;
    $footer_data['gtdk'] += $gt_dk;
    $footer_data['slnk'] += $sl_nhap;
    $footer_data['gtnk'] += $gt_nhap;
    $footer_data['slxk'] += $sl_xuat;
    $footer_data['gtxk'] += $gt_xuat;
    $footer_data['gtck'] += $gt_ck;
    $footer_data['slck'] += $sl_ck;
    // $footer_data = array(
    //     'sldk' => 0,
    //     'gtdk' => 0,
    //     'slnk' => 0,
    //     'gtnk' => 0,
    //     'slxk' => 0,
    //     'gtxk' => 0,
    //     'slck' => 0,
    //     'gtck' => 0,
    // );
    // ======= 3.4 Render như code cũ (phần formatter cột của bạn vẫn y nguyên) =======
    $row = [];
    foreach ($aColumns as $col) {
        // Lấy alias nếu có AS
        if (stripos($col, ' as ') !== false) {
            $alias = trim(substr($col, stripos($col, ' as ') + 4));
            $_data = $aRow[$alias] ?? null;
        } else {
            $_data = $aRow[$col] ?? null;
        }

        if ($col == '(mv.sl_dk - mv.sl_dk_x) AS sl_dk') {
            $_data = '<div class="text-center">' . formatNumber($aRow['sl_dk']) . '<div>';
        }
        if ($col == '(mv.gt_dk - mv.gt_dk_x) AS gt_dk') {
            $_data = '<div class="text-right">' . formatNumber($aRow['gt_dk']) . '<div>';
        }
        if ($col == 'mv.sl_nhap') {
            $_data = '<div class="text-center"><a onclick="viewinventorywarehouse(1,' . $aRow['product_id'] . ',\'' . $aRow["type_items"] . '\'); return false">' . formatNumber($aRow['sl_nhap']) . '</a><div>';
        }
        if ($col == 'mv.gt_nhap') {
            $_data = '<div class="text-right">' . formatNumber($aRow['gt_nhap']) . '<div>';
        }
        if ($col == 'mv.sl_xuat') {
            // $_data = formatNumber($aRow['sl_xuat']);
            $_data = '<div class="text-center"><a onclick="viewinventorywarehouse(2,' . $aRow['product_id'] . ',\'' . $aRow["type_items"] . '\'); return false">' . formatNumber($aRow['sl_xuat']) . '</a><div>';
        }
        if ($col == 'mv.gt_xuat') {
            $_data = '<div class="text-right">' . formatNumber($aRow['gt_xuat']);
        }
        if ($col == '((mv.sl_dk - mv.sl_dk_x) + mv.sl_nhap - mv.sl_xuat) AS sl_ck') {
            $_data = '<div class="text-center">' . formatNumber($aRow['sl_ck']) . '<div>';
        }
        if ($col == '((mv.gt_dk - mv.gt_dk_x) + mv.gt_nhap - mv.gt_xuat) AS gt_ck') {
            $_data = '<div class="text-right">' . formatNumber($aRow['gt_ck']) . '<div>';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
// $aColumns = [
//     'CASE 
//         WHEN tblwarehouse_product.type_items = "nvl" THEN ci.name
//         WHEN tblwarehouse_product.type_items = "product" THEN cp.name
//         ELSE NULL
//      END AS category_name',
//     'CASE 
//     WHEN tblwarehouse_product.type_items = "nvl" THEN m.code
//     WHEN tblwarehouse_product.type_items = "product" THEN pr.code
//     WHEN tblwarehouse_product.type_items = "tools" THEN t.code
// END AS item_code',
//     'CASE 
//     WHEN tblwarehouse_product.type_items = "nvl" THEN m.name
//     WHEN tblwarehouse_product.type_items = "product" THEN pr.name
//     WHEN tblwarehouse_product.type_items = "tools" THEN t.name
// END AS item_name',
//     'u.unit AS unit_name_stock',
//     '(mv.sl_dk - mv.sl_dk_x) AS sl_dk',
//     '(mv.gt_dk - mv.gt_dk_x) AS gt_dk',
//     'mv.sl_nhap',
//     'mv.gt_nhap',
//     'mv.sl_xuat',
//     'mv.gt_xuat',
//     '((mv.sl_dk - mv.sl_dk_x) + mv.sl_nhap - mv.sl_xuat) AS sl_ck',
//     '((mv.gt_dk - mv.gt_dk_x) + mv.gt_nhap - mv.gt_xuat) AS gt_ck'
// ];
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;
$output['title_excel'] = ['Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') . ' - ' . (!empty($endMonth) ? _dt($endMonth) : ''), 'KHO HÀNG: TẤT CẢ'];
if (!empty($warehouse_id_array)) {
    $this->ci->db->where_in('id', $warehouse_id_array);
    $listWarehouseSearch = $this->ci->db->get('tblwarehouse')->result_array();
    if (!empty($listWarehouseSearch)) {
        $output['title_excel'][1] = 'KHO HÀNG: ';
        foreach ($listWarehouseSearch as $key => $value) {
            $output['title_excel'][1] .= $value['name'] . ', ';
        }
        $output['title_excel'][1] = trim($output['title_excel'][1], ', ');
    }
}



// ALTER TABLE tblwarehouse_product 
//     ADD INDEX idx_type_items_product (type_items, product_id, warehouse_id);

// ALTER TABLE tblimport_items 
//     ADD INDEX idx_import_items (id_import, type, product_id, quantity_stock, price);

// ALTER TABLE tblimport 
//     ADD INDEX idx_import_date (id, warehouseman_id, warehouseman_date);

// ALTER TABLE tbladjusted_items 
//     ADD INDEX idx_adjusted_items (id_adjusted, type, product_id, quantity_net, price);

// ALTER TABLE tbladjusted 
//     ADD INDEX idx_adjusted_date (id, type, date_create);

// ALTER TABLE tbltransfer_warehouse_detail 
//     ADD INDEX idx_transfer_detail (id_transfer, type, id_items, quantity_net, price);

// ALTER TABLE tbltransfer_warehouse 
//     ADD INDEX idx_transfer_date (id, warehouseman_id, warehouseman_date);

// ALTER TABLE tbl_purchase_product_items 
//     ADD INDEX idx_purchase_product_items (purchase_product_id, type_item, item_id, quantity, price);

// ALTER TABLE tbl_purchase_products 
//     ADD INDEX idx_purchase_products (id, warehouseman_id, date_warehouseman);

// ALTER TABLE tbl_purchase_internal_items 
//     ADD INDEX idx_purchase_internal_items (purchase_internal_id, type_item, item_id, quantity, price);

// ALTER TABLE tbl_purchase_internal 
//     ADD INDEX idx_purchase_internal (id, warehouseman_id, date_warehouseman);

// ALTER TABLE tbl_returned_goods_items 
//     ADD INDEX idx_returned_goods_items (returned_goods_id, type_item, item_id, quantity, price);

// ALTER TABLE tbl_returned_goods 
//     ADD INDEX idx_returned_goods (id, warehouseman_id, warehouseman_date);

// ALTER TABLE tbl_delivery_items 
//     ADD INDEX idx_delivery_items (delivery_id, type_item, item_id, quantity_stock, price);

// ALTER TABLE tbl_deliveries 
//     ADD INDEX idx_deliveries (id, warehouseman_id, date_warehouseman);

// ALTER TABLE tblreturn_suppliers_items 
//     ADD INDEX idx_return_suppliers_items (id_return, type, product_id, quantity_net, price);

// ALTER TABLE tblreturn_suppliers 
//     ADD INDEX idx_return_suppliers (id, warehouseman_id, data_warehouseman);

// ALTER TABLE tbl_suggest_exporting_items 
//     ADD INDEX idx_suggest_exporting_items (suggest_exporting_id, type_item, item_id, quantity_warehouse, price);

// ALTER TABLE tbl_suggest_exporting 
//     ADD INDEX idx_suggest_exporting (id, warehouseman_id, date_warehouseman);

// ALTER TABLE tbltblexport_different_items 
//     ADD INDEX idx_export_different_items (id_export_different, type, product_id, quantity_net, price);

// ALTER TABLE tblexport_different 
//     ADD INDEX idx_export_different (id, warehouseman_id, warehouseman_date);


// ALTER TABLE tblimport_items 
// ADD INDEX idx_type_date_product (type, quantity_stock, product_id);

// ALTER TABLE tbladjusted_items 
// ADD INDEX idx_type_date_product (type, quantity_net, product_id);

// ALTER TABLE tbltransfer_warehouse_detail 
// ADD INDEX idx_type_date_product (type, quantity_net, id_items);

// ALTER TABLE tbl_purchase_product_items 
// ADD INDEX idx_type_date_product (type_item, quantity, item_id);

// ALTER TABLE tbl_delivery_items 
// ADD INDEX idx_type_date_product (type_item, quantity_stock, item_id);