<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stock_out_request_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function submit($formData, $id = null)
    {
        if(empty($formData['item_id'])) {
            $response['submitId'] =  null;
            $response['message'] =  'Lưu thất bại. Sản phẩm khổng thể trống';
            return $response;
        }

        $arrField = [
            'code',
            'date_create',
            'date',
            'note',
            'create_by',
            'production_order_id',
        ];
        $formData['date'] = (!empty($formData['date']) ? to_sql_date($formData['date'], true) : null);
        if (empty($id)) { //insert
            $formData['code'] = getReference('stock_out_request');
            // $formData['code'] = get_option('prefix_spending_plan') . '-' . sprintf('%06d', ch_getMaxID('id', 'tblspending_plan') + 1);
            $formData['create_by'] = get_staff_user_id();
        } else { //update
            unset($arrField['code']);
            unset($arrField['create_by']);
        }

        $submitData = [];
        foreach ($arrField as $field) {
            if (isset($formData[$field])) {
                $submitData[$field] = $formData[$field];
            }
        }

        if (empty($id)) { // insert
            $this->db->insert('tblstock_out_request', $submitData);
            $submitId = $this->db->insert_id();

            if (getReference('stock_out_request') == $formData['code']) {
                updateReference('stock_out_request');
            }
        } else { //update
            if ($this->db->update('tblstock_out_request', $submitData, ['id' => $id])) {
                $submitId = $id;
            } else {
                $submitId = false;
            }
        }

        if (!empty($submitId)) {
            // Thêm bảng chi tiết
            $submitItemData = [];
            $arrItemId = $formData['item_row_id'] ?? [];
            $arrItems = $formData['item_id'];
            $arrOrder = $formData['order_id'];
            $arrProductionQuantity = $formData['production_quantity'];
            $arrProductionRequireQuantity = $formData['production_require_quantity'];
            $arrPurchaseRequireQuantity = $formData['purchase_require_quantity'];
            $ignoreDeleteId = [0];
            foreach($arrItems as $itemIndex => $itemValue) {
                if (empty($itemValue)) continue;
                $item = explode('__', $itemValue);
                $itemId = $item[0];
                $itemType = $item[1];
                $orderId = $arrOrder[$itemIndex] ?? null;
                $productionQuantity = $arrProductionQuantity[$itemIndex] ?? 0;
                $productionRequireQuantity = $arrProductionRequireQuantity[$itemIndex] ?? 0;
                $purchaseRequireQuantity = $arrPurchaseRequireQuantity[$itemIndex] ?? 0;

                $submitItemData = [
                    'id' => $arrItemId[$itemIndex] ?? null,
                    'stock_out_request_id' => $submitId,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'order_id' => $orderId,
                    'production_quantity' => number_unformat($productionQuantity),
                    'production_require_quantity' => number_unformat($productionRequireQuantity),
                    'purchase_require_quantity' => number_unformat($purchaseRequireQuantity),
                ];

                if (empty($submitItemData['id'])) { // insert
                    unset($submitItemData['id']);
                    $this->db->insert('tblstock_out_request_item', $submitItemData);
                    $insertedId = $this->db->insert_id();
                    $ignoreDeleteId[] = $insertedId;
                } else { // update
                    $itemRowId = $submitItemData['id'];
                    unset($submitItemData['id']);
                    if ($this->db->update('tblstock_out_request_item', $submitItemData, ['id' => $itemRowId])) {
                        $ignoreDeleteId[] = $itemRowId;
                    }
                }
            }

            // Xóa bảng chi tiết
            $this->db->where('stock_out_request_id', $submitId);
            $this->db->where_not_in('id', $ignoreDeleteId);
            $this->db->delete('tblstock_out_request_item');
        }

        $response['submitId'] =  $submitId;
        $response['message'] =  (($submitId) ? 'Thành công' : 'Thất bại');
        return $response;
    }

    public function get($id) {
        $result = get_table_where('tblstock_out_request', ['id' => $id], '', 'row_array');
        $result['items'] = $this->getItems($id);
        return $result;
    }

    public function getItems($id) {
        $this->db->select('
            tblstock_out_request_item.*,
            IF(tbl_materials.images IS NOT NULL && tbl_materials.images != "", CONCAT("uploads/products/", "", tbl_materials.images, ""), "") as image,
            tbl_materials.code as item_code,
            tbl_materials.name as item_name,
            tbl_materials.height as height,
            tbl_materials.wide as wide,
            tblunits.unit as unit_name,
            tbl_orders.reference_no as order_code
        ');
        $this->db->from('tblstock_out_request_item');
        $this->db->join('tbl_materials', 'tbl_materials.id = tblstock_out_request_item.item_id AND tblstock_out_request_item.item_type = "materials"', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tblstock_out_request_item.order_id', 'left');
        // $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tblunits', 'tbl_materials.unit_id = tblunits.unitid', 'left');
        // $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->where('tblstock_out_request_item.stock_out_request_id', $id);
        $this->db->group_by('tblstock_out_request_item.id');
        $result = $this->db->get()->result_array();
        foreach($result as $key => $value) {
            $filter['item'] = $value['item_id'].'__nvl';
            $result[$key]['stock_quantity'] = $this->getItemStockQuantity($filter)['stock_quantity'] ?? 0;
        }
        
        return $result;
    }

    public function delete($id) {
        $this->db->delete('tblstock_out_request', ['id' => $id]);
        $this->db->delete('tblstock_out_request_item', ['stock_out_request_id' => $id]);

        $result['result'] = 1;
        $result['message'] = 'Xóa thành công';
        return $result;
    }

    public function getOrder($filter = []) {
        $this->db->select('
            tbl_orders.id as id,
            tbl_orders.reference_no as text,
        ');
        $this->db->from('tbl_orders');
        
        if (!empty($filter['production_order_id'])) {
            $this->db->join('tbl_productions_orders', 'FIND_IN_SET(CONCAT("orders__", tbl_orders.id), tbl_productions_orders.productions_plan_id) > 0', 'left');
            $this->db->where('tbl_productions_orders.id', $filter['production_order_id']);
        }
        if (!empty($filter['limit'])) {
            $this->db->limit($filter['limit']);
        }

        if (!empty($filter['id'])){
            $this->db->where('tbl_orders.id',$filter['id']);
        }
        $this->db->group_by('tbl_orders.id');
        if (!empty($filter['id'])){
            $result = $this->db->get()->row_array();
        } else {
            $result = $this->db->get()->result_array();
        }

        return $result;
    }
    
    public function getItemData($filter = []) {
        $this->db->select('
            tbl_productions_orders_details.id,
            tbl_productions_orders_details.productions_orders_item_id
        ');
        if (!empty($filter['production_order_id'])) {
            $this->db->where('tbl_productions_orders_details.productions_orders_id', $filter['production_order_id']);
        }
        if (!empty($filter['order_id'])) {
            $this->db->where('tbl_productions_orders_details.object_type', 'orders');
            $this->db->where('tbl_productions_orders_details.object_id', $filter['order_id']);
        }
        $arrProductions_orders_details = $this->db->get('tbl_productions_orders_details')->result_array();

        $arrProductionOrderDetailId = [];
        $arrProductionOrderItemId = [];
        foreach ($arrProductions_orders_details as $value) {
            $arrProductionOrderDetailId[] = $value["id"];
            $arrProductionOrderItemId[] = $value["productions_orders_item_id"];
        }
        $strProductionOrderDetailId = implode(', ', $arrProductionOrderDetailId);
        $strProductionOrderItemId = implode(', ', $arrProductionOrderItemId);
        // var_dump($arrProductions_orders_details);die;

        $tbSuggestExportMaterial = "(
            SELECT
                sei_1.item_id as item_id,
                SUM(sei_1.quantity_export) as quantity_export
            FROM tbl_suggest_exporting se_1
            INNER JOIN tbl_suggest_exporting_items sei_1 ON sei_1.suggest_exporting_id = se_1.id
            WHERE se_1.warehouseman_id > 0 AND se_1.productions_orders_details_id IN ($strProductionOrderDetailId) AND sei_1.type_item = 'materials'
            GROUP BY sei_1.item_id
        ) tb_se";

        $tbProductionsOrdersItemsSub = "(
            (
                SELECT 
                    pois.item_id as item_id,
                    pois.type as item_type,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_materials.images as image,
                    tblunits.unit as unit_name,
                    SUM(COALESCE(pois.quantity, 0) + COALESCE(pois.quantity_compensation_sm, 0) + COALESCE(pois.quantity_compensation, 0)) as quantity,
                    SUM(COALESCE(tb_se.quantity_export, 0)) as quantity_export
                FROM tbl_productions_orders_items_sub pois
                INNER JOIN tbl_materials ON tbl_materials.id = pois.item_id
                LEFT JOIN $tbSuggestExportMaterial ON tb_se.item_id = pois.item_id
                LEFT JOIN tblunits ON tblunits.unitid = pois.unit_id
                WHERE pois.type = 'materials' AND pois.productions_orders_items_id IN ($strProductionOrderItemId)
                GROUP BY pois.item_id
            )
            UNION ALL
            (
                SELECT
                    sei.item_id as item_id,
                    sei.type_item as item_type,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_materials.images as image,
                    tblunits.unit as unit_name,
                    0 as quantity,
                    SUM(COALESCE(sei.quantity_export, 0)) as quantity_export
                FROM tbl_suggest_exporting se
                INNER JOIN tbl_suggest_exporting_items sei ON sei.suggest_exporting_id = se.id
                INNER JOIN tbl_materials ON tbl_materials.id = sei.item_id
                LEFT JOIN tblunits ON tblunits.unitid = sei.unit_id
                WHERE se.warehouseman_id > 0 AND se.productions_orders_details_id IN ($strProductionOrderDetailId) AND sei.type_item = 'materials' AND se.reference_stock IS NOT NULL AND NOT EXISTS (
                    SELECT pois.id
                    FROM tbl_productions_orders_items_sub pois
                    WHERE pois.type = 'materials' AND pois.productions_orders_items_id IN ($strProductionOrderItemId)
                )
                GROUP BY sei.item_id
            )
        ) tb_productions_orders_items_sub";
        // echo '<pre>';var_dump($tbProductionsOrdersItemsSub);
        // $tbProductionsOrdersItemsSub = str_replace(array("\n", "\r"), "", $tbProductionsOrdersItemsSub);
        // $tbProductionsOrdersItemsSub = preg_replace('/\s+/', ' ', $tbProductionsOrdersItemsSub);;
        // echo '<pre>';var_dump($tbProductionsOrdersItemsSub);die;
        $_aColumns = [
            'CONCAT(tb_productions_orders_items_sub.item_id, , "__", tb_productions_orders_items_sub.item_type) as id',
            'tb_productions_orders_items_sub.item_code as text',
            'tb_productions_orders_items_sub.item_name as item_name',
            'tb_productions_orders_items_sub.image as image',
            'tb_productions_orders_items_sub.unit_name as unit_name',
            'tb_productions_orders_items_sub.quantity as quantity',
            'tb_productions_orders_items_sub.quantity_export as quantity_export',
            '(tb_productions_orders_items_sub.quantity - tb_productions_orders_items_sub.quantity_export) as quantity_rest',
        ];
        
        $sTable = $tbProductionsOrdersItemsSub;
        $join = '';
        $where = '';
        if (!empty($filter['id'])){
            $items_id = $filter['id'];
            $items_id = explode('__',$items_id);
            $where .= 'WHERE tb_productions_orders_items_sub.item_id = '.$items_id[0].'';
        }
        $sGroupBy = '';
        // $sGroupBy = 'GROUP BY tb_productions_orders_items_sub.item_id';
        $sHaving = '';
        $sOrder = '';
        $sLimit = '';
        if (!empty($filter['limit'])) {
            $sLimit = 'LIMIT '. $filter['limit'];
        }

        $sQuery = '
            SELECT ' . str_replace(' , ', ' ', implode(', ', $_aColumns)) . "
            FROM $sTable
            " . $join . "
            " . $where . "
            $sGroupBy
            $sHaving
            $sOrder
            $sLimit
            ";
        if (!empty($filter['id'])){
            $result = $this->db->query($sQuery)->row_array();
        } else {
            $result = $this->db->query($sQuery)->result_array();
        }
        // var_dump($this->db->last_query());die;
        return $result;
    }

    function getItemStockQuantity($filter = []) {
        $result = [];
        if (!empty($filter['item'])) {
            $item = explode('__', $filter['item']);
            $item_id = $item[0];
            $item_type = $item[1];
            
            // $tbQuantityWarehouses = '(
            //     SELECT
            //         tblwarehouse_items.localtion as localtion_id,
            //         tblwarehouse_items.lot_code, 
            //         tblwarehouse_items.date_sx, 
            //         tblwarehouse_items.date_sd, 
            //         tblwarehouse_items.date_use,
            //         SUM(tblwarehouse_items.product_quantity) as product_quantity
            //     FROM tblwarehouse_items
            //     WHERE tblwarehouse_items.type_items = "' . $item_type . '" AND tblwarehouse_items.id_items = "' . $item_id . '"'.(!empty($filter['warehouse_id']) ? ' AND tblwarehouse_items.warehouse_id = ' . $filter['warehouse_id'] : '').'
            //     GROUP BY tblwarehouse_items.localtion, tblwarehouse_items.lot_code, tblwarehouse_items.date_sx, tblwarehouse_items.date_sd, tblwarehouse_items.date_use
            // ) tb_quantity_warehouses';
    
            // $this->db->select('
            //     CONCAT(tbllocaltion_warehouses.warehouse, "__", tbllocaltion_warehouses.id, "__", coalesce(tb_quantity_warehouses.lot_code, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sx, "NULL"), "__", coalesce(tb_quantity_warehouses.date_sd, "NULL"), "__", coalesce(tb_quantity_warehouses.date_use, "NULL")) as id,
            //     CONCAT(tbllocaltion_warehouses.name, , "(SL kho: ", tb_quantity_warehouses.product_quantity,")") as text,
            //     tb_quantity_warehouses.lot_code as lot_code,
            //     tb_quantity_warehouses.date_sx as date_sx,
            //     tb_quantity_warehouses.date_sd as date_sd,
            //     tb_quantity_warehouses.date_use as date_use,
            //     tbllocaltion_warehouses.name as name,
            //     SUM(COALESCE(tb_quantity_warehouses.product_quantity, 0)) as stock_quantity
            // ', false);
            // $this->db->from('tbllocaltion_warehouses');
            // $this->db->join($tbQuantityWarehouses, 'tb_quantity_warehouses.localtion_id = tbllocaltion_warehouses.id');
            // if (!empty($filter['warehouse_id'])) {
            //     $this->db->where('tbllocaltion_warehouses.warehouse', $filter['warehouse_id']);
            // }
            // $this->db->where('tb_quantity_warehouses.product_quantity >', 0);
            // $this->db->group_start();
            // $this->db->where('tbllocaltion_warehouses.pod_id', 0);
            // $this->db->or_where('exists (
            //     SELECT tbl_productions_orders_details.id
            //     FROM tbl_productions_orders_details
            //     WHERE tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id AND tbl_productions_orders_details.object_type = "orders"
            // )', false, false);
            // $this->db->group_end();

            $this->db->select('SUM(tblwarehouse_items.product_quantity) as stock_quantity');
            $this->db->where('tblwarehouse_items.type_items', $item_type);
            $this->db->where('tblwarehouse_items.id_items', $item_id);
            $this->db->from('tblwarehouse_items');
            $result = $this->db->get()->row_array();
        }

        return $result;
    }
}