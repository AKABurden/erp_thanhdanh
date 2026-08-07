<?php

defined('BASEPATH') or exit('No direct script access allowed');
$tagest = getTagest($id, $type);
$aColumns[] = '1';
$aColumns[] = 'tblwarehouse_product.type_items';
$warehse = '';
if ($this->ci->input->post('lot_code')) {
    $warehse = 'AND warehouse_product.lot_code = ' . $this->ci->input->post('lot_code');
}
$prices = '(SELECT t1.`price` as prices,t1.`product_id`,t1.`product_type`
                FROM tblgroup_price_detail t1
                INNER JOIN
                (
                    SELECT `product_id`,`product_type`, MAX(`price`) AS max_price
                    FROM tblgroup_price_detail
                    GROUP BY `product_id`,`product_type`
                ) t2
                    ON t1.`product_id` = t2.`product_id` AND t1.`product_type` = t2.`product_type` AND t1.price = t2.max_price GROUP BY `product_id`,`product_type`) prices';
$localtion_warehouses = ' (SELECT tbl_productions_orders_items.object_item_type,tbl_order_items.price as price_order,tbl_order_items.item_name,tbl_order_items.order_id,tbllocaltion_warehouses.id as localtion_warehousesid
FROM tbllocaltion_warehouses
INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id AND tbl_productions_orders_items.object_item_type = "orders"
INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id) localtion_warehouses';

$filterStatus_v2 = $this->ci->input->post('filterStatus_v2');
// if ($filterStatus_v2 == 2) {
//     $where_a = 'AND tbllocaltion_warehouses.order_id > 0';
// } elseif ($filterStatus_v2 == 1) {
//     $where_a = 'AND tbllocaltion_warehouses.order_id = 0';
// }
if ($filterStatus_v2 == 2) {
    $where_a = 'AND (tbllocaltion_warehouses.order_id > 0 OR tbl_productions_orders_details.object_type = "orders")';
} elseif ($filterStatus_v2 == 1) {
    $where_a = 'AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders") or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0))';
}

if ($type == 'product') {
    $selects = '(SELECT GROUP_CONCAT(CONCAT(tbllocaltion_warehouses.name,"AA",COALESCE(warehouse_product.quantity_left,""),"AA",COALESCE(warehouse_product.lot_code,""),"AA",COALESCE(warehouse_product.date_sx,""),"AA",COALESCE(warehouse_product.date_sd,""),"AA",COALESCE(warehouse_product.date_use,"")) SEPARATOR "FF") as name_local,SUM(warehouse_product.quantity_left) as quantity_lefts,warehouse_product.product_id,warehouse_product.type_items  FROM tblwarehouse_product  warehouse_product LEFT JOIN tbllocaltion_warehouses ON warehouse_product.localtion=tbllocaltion_warehouses.id LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id LEFT JOIN tbl_products ON tbl_products.id = warehouse_product.product_id and warehouse_product.type_items = "product" WHERE warehouse_product.warehouse_id = ' . $id . ' ' . $warehse    . ' AND tbllocaltion_warehouses.stage_id = 0 and  (tbl_products.type_products = "products" or tbl_products.type_products = "semi_products_outside") and warehouse_product.quantity_left > 0 ' . $where_a . ' group by warehouse_product.product_id) selects';
} else {
    $selects = '(SELECT GROUP_CONCAT(CONCAT(tbllocaltion_warehouses.name,"AA",COALESCE(warehouse_product.quantity_left,""),"AA",COALESCE(warehouse_product.lot_code,""),"AA",COALESCE(warehouse_product.date_sx,""),"AA",COALESCE(warehouse_product.date_sd,""),"AA",COALESCE(warehouse_product.date_use,"")) SEPARATOR "FF") as name_local,SUM(warehouse_product.quantity_left) as quantity_lefts,warehouse_product.product_id,warehouse_product.type_items  FROM tblwarehouse_product  warehouse_product LEFT JOIN tbllocaltion_warehouses ON warehouse_product.localtion=tbllocaltion_warehouses.id LEFT JOIN tbl_products ON tbl_products.id = warehouse_product.product_id and warehouse_product.type_items = "product" WHERE warehouse_product.warehouse_id = ' . $id . ' ' . $warehse    . ' AND tbllocaltion_warehouses.stage_id = 0 and  tbl_products.type_products = "semi_products" and warehouse_product.quantity_left > 0  group by warehouse_product.product_id) selects';
}
// $aColumns[] = 'tbllocaltion_warehouses.name_parent';                                         
$aColumns[] = 'selects.quantity_lefts';
$tagests = array();
foreach ($tagest as $key => $value) {
    if ($type == 'product') {
        $tagests_query = '(SELECT SUM(warehouse_product.quantity_left)  FROM tblwarehouse_product  warehouse_product LEFT JOIN tbllocaltion_warehouses ON warehouse_product.localtion=tbllocaltion_warehouses.id LEFT JOIN tbl_products ON tbl_products.id = warehouse_product.product_id and warehouse_product.type_items = "product"  WHERE warehouse_product.warehouse_id = ' . $id . '  ' . $warehse    . ' AND tbllocaltion_warehouses.stage_id = ' . $value['id'] . ' and (tbl_products.type_products = "products"  or tbl_products.type_products = "semi_products_outside") and warehouse_product.product_id = tblwarehouse_product.product_id group by tblwarehouse_product.product_id,tblwarehouse_product.type_items)  as quantity_hs_' . $key;

        $aColumns[] = $tagests_query;
        $tagests[$key] = $tagests_query;
    } else {
        $tagests_query = '(SELECT SUM(warehouse_product.quantity_left)  FROM tblwarehouse_product  warehouse_product LEFT JOIN tbllocaltion_warehouses ON warehouse_product.localtion=tbllocaltion_warehouses.id LEFT JOIN tbl_products ON tbl_products.id = warehouse_product.product_id and warehouse_product.type_items = "product"  WHERE warehouse_product.warehouse_id = ' . $id . '  ' . $warehse    . ' AND tbllocaltion_warehouses.stage_id = ' . $value['id'] . ' and ( tbl_products.type_products = "semi_products") and warehouse_product.product_id = tblwarehouse_product.product_id group by tblwarehouse_product.product_id,tblwarehouse_product.type_items)  as quantity_hs_' . $key;
        $aColumns[] = $tagests_query;
        $tagests[$key] = $tagests_query;
    }
}
$aColumns[] = '(IF(object_item_type = "orders",price_order,prices) * tblwarehouse_product.quantity_left) as price';
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'warehouse_product';

$join         = array(
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion',
    'LEFT JOIN tbltype_items ON tbltype_items.type = tblwarehouse_product.type_items',
    'LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_product.product_id',
    'LEFT JOIN ' . $selects . ' ON selects.type_items = tblwarehouse_product.type_items and selects.product_id = tblwarehouse_product.product_id',
    'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id',

    'LEFT JOIN ' . $prices . ' ON prices.product_id = tblwarehouse_product.product_id AND prices.product_type = "product"',
    'LEFT JOIN ' . $localtion_warehouses . ' ON localtion_warehouses.localtion_warehousesid = tblwarehouse_product.localtion',
);
$where = array();
if ($type) {
    array_push($where, 'AND tblwarehouse_product.type_items = "product"');
    if ($type == 'product') {
        array_push($where, 'AND (tbl_products.type_products = "products" or tbl_products.type_products = "semi_products_outside")');
    } else {
        array_push($where, 'AND (tbl_products.type_products = "semi_products")');
    }
}
if ($type == 'product') {
    if ($filterStatus_v2 == 2) {
        array_push($where, 'AND (tbllocaltion_warehouses.order_id > 0 OR tbl_productions_orders_details.object_type = "orders")');
    } elseif ($filterStatus_v2 == 1) {
        array_push($where, 'AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders") or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0))');
    }
}
// if ($filterStatus_v2 == 2) {
//     array_push($where, 'AND tbl_productions_orders_details.object_type = "orders"');
// } elseif ($filterStatus_v2 == 1) {
//     array_push($where, 'AND (tbl_productions_orders_details.object_type != "orders" or tbllocaltion_warehouses.pod_id = 0)');
// }
if ($this->ci->input->post('category_id')) {
    if ($this->ci->input->post('filterStatus') == 'items') {
        $join[] =  'LEFT JOIN tblitems ON tblitems.id = tblwarehouse_product.product_id and tblwarehouse_product.type_items = "items"';
        array_push($where, 'AND tblitems.category_id = "' . $this->ci->input->post('category_id') . '"');
    } elseif ($this->ci->input->post('filterStatus') == 'nvl') {
        $join[] =  'LEFT JOIN tbl_materials ON tbl_materials.id = tblwarehouse_product.product_id and tblwarehouse_product.type_items = "nvl"';
        array_push($where, 'AND tbl_materials.category_id = "' . $this->ci->input->post('category_id') . '"');
    } elseif ($this->ci->input->post('filterStatus') == 'product') {
        $join[] =  'LEFT JOIN tbl_products ON tbl_products.id = tblwarehouse_product.product_id and tblwarehouse_product.type_items = "product"';
        array_push($where, 'AND tbl_products.category_id = "' . $this->ci->input->post('category_id') . '"');
    } elseif ($this->ci->input->post('filterStatus') == 'tools') {
        $join[] =  'LEFT JOIN tbl_tools_supplies ON tbl_tools_supplies.id = tblwarehouse_product.product_id and tblwarehouse_product.type_items = "tools"';
        array_push($where, 'AND tbl_tools_supplies.category_id = "' . $this->ci->input->post('category_id') . '"');
    }
}
if ($this->ci->input->post('lot_code')) {
    array_push($where, 'AND tblwarehouse_product.lot_code = ' . $this->ci->input->post('lot_code'));
}
if (is_numeric($id)) {
    array_push($where, 'AND tblwarehouse_product.warehouse_id = ' . $id);
}
if ($this->ci->input->post('custom_item_select')) {
    array_push($where, 'AND tblwarehouse_product.product_id = ' . $this->ci->input->post('custom_item_select'));
}
if ($this->ci->input->post('localtion')) {
    $localtion = [];
    get_full_childs_id($this->ci->input->post('localtion'), $localtion);
    array_push($where, 'AND tblwarehouse_product.localtion IN(' . implode(',', $localtion) . ')');
}
$search = $this->ci->input->post('search')['value'];
if (!empty($search)) {
    array_push($where, 'AND ((tblwarehouse_product.product_id IN(select tblitems.id from tblitems where (tblitems.name like "%' . $search . '%") OR tblitems.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "items" AND tblwarehouse_product.warehouse_id = ' . $id . ') OR (tblwarehouse_product.product_id IN(select tbl_tools_supplies.id from tbl_tools_supplies where (tbl_tools_supplies.name like "%' . $search . '%") OR tbl_tools_supplies.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "tools"  AND tblwarehouse_product.warehouse_id = ' . $id . ') OR (tblwarehouse_product.product_id IN(select tbl_products.id from tbl_products where (tbl_products.name like "%' . $search . '%") OR tbl_products.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "product"  AND tblwarehouse_product.warehouse_id = ' . $id . ') OR (tblwarehouse_product.product_id IN(select tbl_materials.id from tbl_materials where (tbl_materials.name like "%' . $search . '%") OR tbl_materials.code like "%' . $search . '%") AND tblwarehouse_product.type_items = "nvl"  AND tblwarehouse_product.warehouse_id = ' . $id . ')) ');
}

$group_by = "GROUP BY tblwarehouse_product.product_id";
$having = "HAVING SUM(tblwarehouse_product.quantity_left) > 0";
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_product.product_id', 'tbltype_items.name as name_type', 'tbllocaltion_warehouses.id as id_localtion,tblwarehouse_product.id,SUM(tblwarehouse_product.quantity_left) as quantity,name_local'], $group_by, [], [], $having);
$output  = $result['output'];
$rResult = $result['rResult'];
usort($rResult, ch_make_cmp(['tblwarehouse_product.type_items' => "desc", 'product_id' => "desc", 'id_localtion' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
$footer_data = array(
    'slt' => 0,
    'gtt' => 0,
);
foreach ($rResult as $r => $aRow) {
    if ($aRow['quantity'] == 0) {
        continue;
    }
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['tblwarehouse_product.type_items']);

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            // $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblwarehouse_product.type_items') {
            $_data = '<div style="font-size:16px">' . $get_items->name . '</div>';
        }
        if ($aColumns[$i] == '1') {
            $_data = '<div style="font-size:16px">' . $get_items->code . '</div>';
        }
        if ($aColumns[$i] == '3') {
            $warehouse_detail = get_table_where('tblwarehouse_product', array('warehouse_id' => $id, 'product_id' => $aRow['product_id'], 'type_items' => $aRow['tblwarehouse_product.type_items'], 'localtion' => $aRow['id_localtion'], 'type_export' => 18, 'quantity_left >' => 0));
            $html = '';
            foreach ($warehouse_detail as $key => $value) {
                $purchase_products = get_table_where('tbl_purchase_products', array('id' => $value['import_id']), '', 'row');
                if (!empty($purchase_products->productions_orders_details_id)) {
                    $order_production_details = get_table_where('tbl_productions_orders_details', array('id' => $purchase_products->productions_orders_details_id), '', 'row');
                    if (!empty($order_production_details)) {
                        $html .= $order_production_details->reference_no . ' :SL( ' . $value['quantity_left'] . ' )<br>';
                    }
                }
            }
            $_data = $html;
        }
        if ($aColumns[$i] == '(IF(object_item_type = "orders",price_order,prices) * tblwarehouse_product.quantity_left) as price') {
            $_data = '<div class="text-right">' . formatNumber($aRow['price']) . '</div>';
            $footer_data['gtt'] += $aRow['price'];
        }
      
        foreach ($tagest as $key => $value) {
            // if ($aColumns[$i] == '(SELECT SUM(warehouse_product.quantity_left)  FROM tblwarehouse_product  warehouse_product LEFT JOIN tbllocaltion_warehouses ON warehouse_product.localtion=tbllocaltion_warehouses.id WHERE warehouse_product.warehouse_id = ' . $id . ' AND tbllocaltion_warehouses.stage_id = ' . $value['id'] . ' and warehouse_product.type_items = "product" and warehouse_product.product_id = tblwarehouse_product.product_id group by tblwarehouse_product.product_id,tblwarehouse_product.type_items)  as quantity_hs_' . $key) {
            //     $_data = '<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' . formatNumber($aRow['quantity_hs_' . $key]) . '</div>';
            // }
            if ($aColumns[$i] == $tagests[$key]) {
                $_data = '<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' . formatNumber($aRow['quantity_hs_' . $key]) . '</div>';
            }
        }
        if ($aColumns[$i] == 'selects.quantity_lefts') {
            $_data = '<div class="text-center" style="font-size:17px;color:red;font-weight: bold">' . formatNumber($aRow['quantity_lefts']) . '</div>';
            if ($aRow['quantity_lefts'] > 0) {

                $name_local = explode('FF', $aRow['name_local']);
                $_data .= '<hr class="mtop5 mbot5">
                            <table class="table dataTable ">
                                <tbody>';
                $aray = array();
                $dems = 0;

                foreach ($name_local as $key => $value) {
                    $name = explode('AA', $value);
                    $dems = 0;
                    if (count($name) > 3) {
                        $dems = vn_to_str($name[0] . $name[2] . $name[3] . $name[4] . $name[5]);
                    }

                    if (empty($aray[$dems])) {
                        $aray[$dems]['name'] = $name[0];
                        $aray[$dems]['sl'] = $name[1];
                        $aray[$dems]['lot_code'] = $name[2];
                        $aray[$dems]['date_sx'] = $name[3];
                        $aray[$dems]['date_sd'] = $name[4];
                        $aray[$dems]['date_use'] = $name[5];
                    } else {
                        $aray[$dems]['sl'] += $name[1];
                    }
                    // $dems++;
                }

                foreach ($aray as $key => $value) {
                    $_data .= '        <tr>
                                        <td class="pre hau">' . $value['name'] . '
                                        </td>
                                        <td class="pre hau" style="font-size: 10px;text-align: left;"><span>' . _l('Lot') . ':' . $value['lot_code'] . '</span><br>
                                        <span>' . _l('ch_date_of_manufacture') . ':' . _d($value['date_sx']) . '</span><br>
                                        <span>' . _l('ch_items_dateed') . ':' . _d($value['date_sd']) . '</span><br>
                                        <span>' . _l('ch_items_date_use') . ':' . $value['date_use'] . '</span>
                                        </td>
                                        <td class="hau text-center"> 
                                            ' . formatNumber($value['sl']) . '
                                        </td>
                                    </tr>';
                }
                $_data .= '     </tbody>
                            </table>';
            }
            $footer_data['slt'] += $aRow['quantity'];
        }
        if ($aColumns[$i] == '(select SUM(tblwarehouse_items.product_quantity) from tblwarehouse_items where warehouse_id = tblwarehouse_product.warehouse_id AND id_items = tblwarehouse_product.product_id AND type_items = tblwarehouse_product.type_items) as product_quantity') {
            $_data = '<div class="text-center" style="font-size:20px;color:red;font-weight: bold">' . formatNumber($aRow['product_quantity']) . '</div>';;
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums'] = $footer_data;
