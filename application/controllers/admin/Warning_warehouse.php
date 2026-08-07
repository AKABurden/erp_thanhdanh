<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Warning_warehouse extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchases_model');
        $this->load->model('purchase_order_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }
    public function index()
    {
        $data['title']          = _l('Cảnh báo tồn kho');
        $this->load->view('admin/warning_warehouse/index', $data);
    }

    public function getItemsProductMaterial($value,&$arrItems = array(),&$array_materials = array(),&$array_product_semi = array()){
        if($value['type_item'] != 'materials'){
            $arr = explode('__', $value['item_id']);
            $product_id =  $arr[0];
            $product = get_table_where('tbl_products', ['id' => $product_id], '', 'row_array', '', 'id, versions');
            $versions = $product['versions'];
            $quantity = $value['quantity'];
            if (!empty($versions)) {
                $version = $this->products_model->getBomByProductIdAndVersions($product_id, $versions);
                if (!empty($version)) {
                    $elements = $this->products_model->getVersionsElementByVersionId($version['id']);
                    if (!empty($elements)) {
                        foreach ($elements as $k => $val) {
                            $quantity_element = $val['quantity'];
                            $total_quantity_element = $quantity * $quantity_element;
    
                            $element_items = $this->products_model->getElementItemsByElementId($val['id']);
                            if (!empty($element_items)) {
                                foreach ($element_items as $i => $el) {
                                    $quantity_single = $el['quantity'];
                                    $total_quantity_item = $total_quantity_element * $quantity_single;
                                    $quantity_primary = 0;
                                    if ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                        $info = $this->products_model->rowProduct($el['item_id']);
                                        $unit_parent_id = $info['unit_id'];
                                        $quantity_exchange = 1;
                                        $quantity_primary = $total_quantity_item;
                                    } else {
                                        $info = $this->items_model->rowMaterial($el['item_id']);
                                        $unit_id = $el['unit_id'];
                                        $unit_parent_id = $info['unit_id'];
                                        $row_exchange = $this->products_model->rowExchangeItems($el['item_id'], $unit_id);
                                        $quantity_exchange = 1;
                                        if (!empty($row_exchange)) {
                                            $quantity_exchange = $row_exchange['number_exchange'];
                                        }
                                        if ($quantity_exchange != 0) {
                                            $quantity_primary = $total_quantity_item/$quantity_exchange;
                                        }
                                    }
                                    $item_id_key = $el['item_id'].'_'.$el['type'];
                                    if (!empty($arrItems[$item_id_key]))
                                    {
                                        $arrItems[$item_id_key]['quantity'] = $arrItems[$item_id_key]['quantity'] + $quantity_primary;
                                    } else {
                                        $arrItems[$item_id_key] = array(
                                            'item_id' => $el['item_id'].'__'.$el['type'],
                                            'type_item' => $el['type'],
                                            'quantity' => $quantity_primary
                                        );
                                        if($el['type'] == 'materials'){
                                            $array_materials [] = $el['item_id'];
                                        } elseif($el['type'] == "semi_products" || $el['type'] == "semi_products_outside"){
                                            $array_product_semi []= $el['item_id'];
                                        } 
                                    } 
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arrItems;
    }

    public function getItemsWarehouse() {
        
        $type_item = $this->input->post('type_item');
        $name_items = $this->input->post('name_items');
        $status_table = $this->input->post('status_table');
        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
        )";
        $quantityInventorySemi = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
            WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
            AND IF(tbl_products.type_products = 'semi_products_outside', tblwarehouse.id != 8, tbllocaltion_warehouses.pod_id = 0)
        )";
        $quantityInventoryTool = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
        )";

//        if ( !$cache_items = $this->cache->get('items')){
//            $items = [];
//            $this->db->select('tbl_order_items.*');
//            $this->db->from('tbl_orders');
//            $this->db->join('tbl_order_items','tbl_order_items.order_id = tbl_orders.id','left');
//            $this->db->where('(tbl_order_items.quantity - tbl_order_items.quantity_plan) > ',0);
//            $orders = $this->db->get()->result_array();
//            $array_materials = [0];
//            $array_product_semi = [0];
//            if(!empty($orders)){
//                foreach($orders as $key => $value){
//                    $this->getItemsProductMaterial($value,$items,$array_materials,$array_product_semi);
//                }
//            }
//
//            if(!empty($items)){
//                foreach($items as $key => $value){
//                    $this->getItemsProductMaterial($value,$items,$array_materials,$array_product_semi);
//                }
//            }
//
//            $cache_items = $items;
//
//            $this->cache->save('items', $cache_items, 120);
//            $this->cache->save('array_materials', $array_materials, 120);
//            $this->cache->save('array_product_semi', $array_product_semi, 120);
//
//        }
//        $items = $this->cache->get('items');
//        $array_materials = $this->cache->get('array_materials');
//        $array_product_semi = $this->cache->get('array_product_semi');

        $array_materials = [0];
        $array_product_semi = [0];

        $arr_materials = implode(",",$array_materials);
        $arr_product_semi = implode(",",$array_product_semi);
        $tableAllItems = "(
            (
                SELECT 
                    tbl_materials.id as id,
                    CONCAT('materials') as item_type,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_materials.images as images,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_materials.quantity_minimum as quantity,
                    COALESCE($quantityInventory, 0) as quantity_inventory,
                    0 as quantity_purchase

                FROM tbl_materials 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
                WHERE tbl_materials.quantity_minimum > 0
            )
            UNION ALL
            (
                SELECT
                    tbl_products.id as id,
                    tbl_products.type_products as item_type,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.images as images,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_products.quantity_minimum as quantity,
                    COALESCE($quantityInventorySemi, 0) as quantity_inventory,
                    0 as quantity_purchase
                FROM tbl_products 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                WHERE tbl_products.id  IN  ($arr_product_semi)
            )
            UNION ALL
            (
                SELECT
                    tbl_tools_supplies.id as id,
                    CONCAT('tools_supplies') as item_type,
                    tbl_tools_supplies.code as item_code,
                    tbl_tools_supplies.name as item_name,
                    tbl_tools_supplies.images as images,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_tools_supplies.quantity_minimum as quantity,
                    COALESCE($quantityInventoryTool, 0) as quantity_inventory,
                    0 as quantity_purchase

                FROM tbl_tools_supplies 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id
                WHERE tbl_tools_supplies.quantity_minimum > 0
            )
        ) table_all_item";

        $aColumns = [
           'table_all_item.id as id',
           'table_all_item.images as images',
           'CONCAT(table_all_item.item_name, " - ", table_all_item.item_code) as item_code',
           'table_all_item.unit_name as unit_name',
           'table_all_item.quantity_bom as quantity_bom',
           'table_all_item.quantity as quantity',
           'table_all_item.quantity_inventory as quantity_inventory',
           '0 as quantity_purchase',
        ];
        $sIndexColumn = 'table_all_item.id';
        $sTable       = $tableAllItems;
        $where        = [
			'AND table_all_item.quantity_inventory < table_all_item.quantity'
        ];
        if(!empty($type_item)){
            array_push($where, 'AND table_all_item.item_type = "'.$type_item.'"');
        }
        if(!empty($name_items)){
            array_push($where, 'AND (table_all_item.item_name LIKE "%'.$name_items.'%" OR table_all_item.item_code LIKE "%'.$name_items.'%"  )');
        }
        if(!empty($status_table)){
            if($status_table == 'purchase'){
                array_push($where, 'AND (table_all_item.item_type = "tools_supplies" OR table_all_item.item_type = "materials" OR table_all_item.item_type = "semi_products_outside" ) ');
            } elseif($status_table == 'manu'){
                array_push($where, 'AND table_all_item.item_type = "semi_products"');
            }
        }
        $filter = [];
        
        $join = [
            
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'table_all_item.item_type as item_type'
        ], '', [], ['union_all' => true]);
        

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $typeGroup = '';
        $totalQuantityBom = 0;
        $totalQuantity = 0;
        $totalQuantityInventory = 0;
        $totalQuantityPurchase = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $item_type = $aRow['item_type'];
            if ($typeGroup != $item_type) {
                $row[0] = 'group';
                $row[1] = lang($aRow['item_type']);
                $row[2] = '';
                $row[3] = '';
                $row[4] = '';
                $row[5] = '';
                $row[6] = '';
                $row[7] = '';
                $typeGroup = $item_type;
                $output['aaData'][] = $row;
            }

            $images = $aRow['images'];
            if (empty($images)) {
                $images = base_url('assets/images/tnh/no_image.png');
            } else if ($item_type == "materials") {
                $images = base_url('uploads/materials/'.$images);
            } else if ($item_type == "tools_supplies") {
                $images = base_url('uploads/tools_supplies/'.$images);
            } else {
                $images = base_url('uploads/products/'.$images);
            }

            $divImages = '<div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px; margin: auto;"><a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="'.$images.'" style="border-radius: 50%"></div></a></div></div></div></div>';
        
            $quantity = $aRow['quantity_bom'];
//            if(array_search($aRow['id'].'__'.$aRow['item_type'], array_column($items, 'item_id')) !== false) {
//                $quantity = array_column($items, 'quantity','item_id')[$aRow['id'].'__'.$aRow['item_type']];
//            } 

            if($aRow['item_type'] == 'semi_products'){
                $row[0] = '';
            } else{
                $row[0] = '<div class="checkbox"><input type="checkbox" name="items[]" id="check-item'.$aRow['id'].'" value="'.$aRow['id'].'__'.$aRow['item_type'].'__'.$quantity.'"><label for="check-item'.$aRow['id'].'"></label></div>';
            }
            $row[1] = $divImages;
            $row[2] = $aRow['item_code'];
            $row[3] = '<div class="text-center">'.$aRow['unit_name'].'</div>';
            $row[4] = '<div class="text-center">'.formatNumber($quantity).'</div>';
            $row[5] = '<div class="text-center">'.formatNumber($aRow['quantity']).'</div>';
            $row[6] = '<div class="text-center">'.formatNumber($aRow['quantity_inventory']).'</div>';
            if(($quantity + $aRow['quantity']) - $aRow['quantity_inventory'] < 0){
                $quantity_purchase = 0;
            } else {
                $quantity_purchase = ($quantity + $aRow['quantity'])  - $aRow['quantity_inventory'];
            }
            $row[7] = '<div class="text-center">'.formatNumber($quantity_purchase).'</div>';
            if($quantity_purchase == 0){
                $row['DT_RowClass'] = 'hide';
            } else {
                $row['DT_RowClass'] = '';
            }
            $output['aaData'][] = $row;

            $totalQuantityBom+= $quantity;
            $totalQuantity+= $aRow['quantity'];
            $totalQuantityInventory+= $aRow['quantity_inventory'];
            $totalQuantityPurchase+= $quantity_purchase;
        }
        $output['totalQuantityBom'] = $totalQuantityBom;
        $output['totalQuantity'] = $totalQuantity;
        $output['totalQuantityInventory'] = $totalQuantityInventory;
        $output['totalQuantityPurchase'] = $totalQuantityPurchase;
        echo json_encode($output);
    }

    public function count_all(){
        $type_item = $this->input->post('type_item');
        $name_items = $this->input->post('name_items');
        $quantityInventory = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0
        )";
        $quantityInventorySemi = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
        )";
        $quantityInventoryTool = "(
            SELECT
                SUM(tblwarehouse_items.product_quantity)
            FROM tblwarehouse_items
            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
            WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0
        )";
        
        $tableAllItems = "(
            (
                SELECT 
                    tbl_materials.id as id,
                    CONCAT('materials') as item_type,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_materials.images as images,
                    tblunits.unit as unit_name,
                    tbl_materials.quantity_minimum as quantity,
                    COALESCE($quantityInventory, 0) as quantity_inventory,
                    0 as quantity_purchase

                FROM tbl_materials 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
                WHERE tbl_materials.quantity_minimum > 0
                AND tbl_materials.quantity_minimum > COALESCE($quantityInventory, 0)
            )
            UNION ALL
            (
                SELECT
                    tbl_products.id as id,
                    tbl_products.type_products as item_type,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_products.images as images,
                    tblunits.unit as unit_name,
                    tbl_products.quantity_minimum as quantity,
                    COALESCE($quantityInventorySemi, 0) as quantity_inventory,
                    0 as quantity_purchase
                FROM tbl_products 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
                WHERE tbl_products.quantity_minimum > 0
                AND tbl_products.quantity_minimum > COALESCE($quantityInventorySemi, 0)
            )
            UNION ALL
            (
                SELECT
                    tbl_tools_supplies.id as id,
                    CONCAT('tools_supplies') as item_type,
                    tbl_tools_supplies.code as item_code,
                    tbl_tools_supplies.name as item_name,
                    tbl_tools_supplies.images as images,
                    tblunits.unit as unit_name,
                    tbl_tools_supplies.quantity_minimum as quantity,
                    COALESCE($quantityInventoryTool, 0) as quantity_inventory,
                    0 as quantity_purchase

                FROM tbl_tools_supplies 
                LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id
                WHERE tbl_tools_supplies.quantity_minimum > 0
                AND tbl_tools_supplies.quantity_minimum > COALESCE($quantityInventoryTool, 0)
            )
        ) table_all_item";



        $purchase = 0;
        $manu = 0;
        $conditionManu = '';
        $conditionPurchase = '';
        if(!empty($type_item)){ 
            $conditionPurchase = ' AND table_all_item.item_type = "'.$type_item.'"';
            $conditionManu = ' AND table_all_item.item_type = "'.$type_item.'"';
        }
        if(!empty($name_items)){
            $conditionPurchase = ' AND (table_all_item.item_name LIKE "%'.$name_items.'%" OR table_all_item.item_code LIKE "%'.$name_items.'%"  )';
            $conditionManu = ' AND (table_all_item.item_name LIKE "%'.$name_items.'%" OR table_all_item.item_code LIKE "%'.$name_items.'%"  )';
        }
        $sIndexColumn = 'table_all_item.id';
        $sQuery = '
        SELECT COUNT('. $sIndexColumn . ")
        FROM $tableAllItems 
        WHERE (table_all_item.item_type = 'tools_supplies' OR table_all_item.item_type = 'materials' OR table_all_item.item_type = 'semi_products_outside') $conditionPurchase";
        $query = $this->db->query($sQuery)->row_array();
        $purchase = $query['COUNT(' .$sIndexColumn. ')'];


        $sQuery = '
        SELECT COUNT('. $sIndexColumn . ")
        FROM $tableAllItems 
        WHERE (table_all_item.item_type = 'semi_products') $conditionManu";
        $query = $this->db->query($sQuery)->row_array();
        $manu = $query['COUNT(' .$sIndexColumn. ')'];

        $data['manu'] = $manu;
        $data['purchase'] = $purchase;

        echo json_encode($data);

        
    }

    public function createPurchase(){
        $data = [];
        $id = $this->input->post('ids');
        $id = trim($id,',');
        if($id){
            $data['id'] = $id;
        }
        $this->load->view('admin/warning_warehouse/create_purchase', $data);
    }
    public function loadItemsWarningWarehouse(){
        $id_item = $this->input->post('id_item');

        // $tbTransferWarehouse = "(
        //     SELECT
        //         tbltransfer_warehouse_detail.id_items as id_items,
        //         SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
        //     FROM tbltransfer_warehouse
        //     INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer AND tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse.productions_capacity_id = ".$this->db->escape($productions_plan_id)."
        //     GROUP BY tbltransfer_warehouse_detail.id_items
        // ) tb_transfer_detail";

        $array_new = [];
        $array_item_find = [];

        
        if(!empty($id_item)){
            $id_item = explode(",",$id_item);
            foreach ($id_item as $key => $value){
                $arr = explode("__",$value);
                if($arr[1] == 'semi_products_outside'){
                    $array_new[$arr[1]][] = $arr[0]; 
                    $array_item_find[$arr[0].'__'.$arr[1]] = [
                        'id_item'=> $arr[0].'__'.$arr[1],
                        'quantity_bom' => $arr[2],
                    ]; 
                } elseif($arr[1] == 'materials') {
                    $array_new[$arr[1]][] = $arr[0]; 
                    $array_item_find[$arr[0].'__'.$arr[1]] = [
                        'id_item'=> $arr[0].'__'.$arr[1],
                        'quantity_bom' => $arr[2],
                    ]; 
                } elseif($arr[1] == 'tools_supplies'){
                    $array_new[$arr[1]][] = $arr[0]; 
                    $array_item_find[$arr[0].'__'.$arr[1]] = [
                        'id_item'=> $arr[0].'__'.$arr[1],
                        'quantity_bom' => $arr[2],
                    ]; 
                }
            }
        }
        $arrayItems = [];
        if(!empty($array_new)){
            foreach($array_new as $key => $value){
                if($key == "semi_products_outside"){
                        $quantityInventorySemi = "(
                            SELECT
                                SUM(tblwarehouse_items.product_quantity)
                            FROM tblwarehouse_items
                            INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                            INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                            WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
                            AND IF(tbl_products.type_products = 'semi_products_outside', tblwarehouse.id != 8, tbllocaltion_warehouses.pod_id = 0)
                        )";
                        $this->db->select('
                        tbl_products.id as item_id,
                        "product" as item_type,
                        tbl_products.type_products as item_type_root,
                        CONCAT("product__", tbl_products.id) as id,
                        CONCAT(tbl_products.name, "(", tbl_products.code, ")") as text,
                        tblunits.unit as unit_name,
                        0 as quantity_bom,
                        tbl_products.quantity_minimum as quantity,
                        COALESCE('.$quantityInventorySemi.', 0) as quantity_inventory,
                    ', false);
                        $this->db->from('tbl_products');
                        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                        $this->db->where_in('tbl_products.id', $value);
                        $this->db->where('tbl_products.type_products', $key);
                        $semi_product_outside = $this->db->get()->result_array();
                        if(!empty($semi_product_outside)){
                            $arrayItems = array_merge($arrayItems, $semi_product_outside);
                        }
                } elseif($key == 'materials'){
                    $quantityInventory = "(
                        SELECT
                            SUM(tblwarehouse_items.product_quantity)
                        FROM tblwarehouse_items
                        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                        WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
                    )";
                    $this->db->select('
                    tbl_materials.id as item_id,
                    "nvl" as item_type,
                    "materials" as item_type_root,
                    CONCAT("nvl__", tbl_materials.id) as id,
                    CONCAT(tbl_materials.name, "(", tbl_materials.code, ")") as text,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_materials.quantity_minimum as quantity,
                    COALESCE('.$quantityInventory.', 0) as quantity_inventory,
                    ', false);
                    $this->db->from('tbl_materials');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                    $this->db->where_in('tbl_materials.id', $value);
                    $materials = $this->db->get()->result_array();
                    if(!empty($materials)){
                        $arrayItems = array_merge($arrayItems, $materials);
                    }
                } elseif($key == 'tools_supplies'){
                    $quantityInventoryTool = "(
                        SELECT
                            SUM(tblwarehouse_items.product_quantity)
                        FROM tblwarehouse_items
                        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                        WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
                    )";
                    $this->db->select('
                    tbl_tools_supplies.id as item_id,
                    "tools" as item_type,
                    "tools_supplies" as item_type_root,
                    CONCAT("tools__", tbl_tools_supplies.id) as id,
                    CONCAT(tbl_tools_supplies.name, "(", tbl_tools_supplies.code, ")") as text,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_tools_supplies.quantity_minimum as quantity,
                    COALESCE('.$quantityInventoryTool.', 0) as quantity_inventory,
                    ', false);
                    $this->db->from('tbl_tools_supplies');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_tools_supplies.unit_id', 'left');
                    $this->db->where_in('tbl_tools_supplies.id', $value);
                    $tools = $this->db->get()->result_array();
                    if(!empty($tools)){
                        $arrayItems = array_merge($arrayItems, $tools);
                    }
                }
            }
        }
        
        if(!empty($arrayItems)){
            foreach ($arrayItems as $key => $value){
                $quantity = $value['quantity_bom'];
                if(array_search($value['item_id'].'__'.$value['item_type_root'], array_column($array_item_find, 'id_item')) !== false) {
                    $quantity = array_column($array_item_find, 'quantity_bom','id_item')[$value['item_id'].'__'.$value['item_type_root']];
                } 
                $arrayItems[$key]['quantity_bom'] = $quantity;
            }
        }
        $data['items'] = $arrayItems;
        echo json_encode($data);
    }
    public function getItemsWarningWarehouse() {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $id_item = $params['id_item'];

        $array_new = [];
        $array_item_find = [];
        if(!empty($id_item)){
            $id_item = explode(",",$id_item);
            foreach ($id_item as $key => $value){
                $arr = explode("__",$value);
                if($arr[1] == 'semi_products_outside'){
                    $array_new[$arr[1]][] = $arr[0]; 
                    $array_item_find[$arr[0].'__'.$arr[1]] = [
                        'id_item'=> $arr[0].'__'.$arr[1],
                        'quantity_bom' => $arr[2],
                    ]; 
                } elseif($arr[1] == 'materials') {
                    $array_new[$arr[1]][] = $arr[0]; 
                    $array_item_find[$arr[0].'__'.$arr[1]] = [
                        'id_item'=> $arr[0].'__'.$arr[1],
                        'quantity_bom' => $arr[2],
                    ]; 
                } elseif($arr[1] == 'tools_supplies'){
                    $array_new[$arr[1]][] = $arr[0]; 
                    $array_item_find[$arr[0].'__'.$arr[1]] = [
                        'id_item'=> $arr[0].'__'.$arr[1],
                        'quantity_bom' => $arr[2],
                    ]; 
                }
            }
        }
        $arrayItems = [];
        if(!empty($array_new)){
            foreach($array_new as $key => $value){
                if($key == "semi_products_outside"){
                    $quantityInventorySemi = "(
                        SELECT
                            SUM(tblwarehouse_items.product_quantity)
                        FROM tblwarehouse_items
                        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                        INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
                        WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
                        AND IF(tbl_products.type_products = 'semi_products_outside', tblwarehouse.id != 8, tbllocaltion_warehouses.pod_id = 0)
                    )";
                    $this->db->select('
                    tbl_products.id as item_id,
                    "product" as item_type,
                    tbl_products.type_products as item_type_root,
                    CONCAT("product__", tbl_products.id) as id,
                    CONCAT(tbl_products.name, "(", tbl_products.code, ")") as text,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_products.quantity_minimum as quantity,
                    COALESCE('.$quantityInventorySemi.', 0) as quantity_inventory,
                    ', false);
                    $this->db->from('tbl_products');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                    $this->db->where_in('tbl_products.id', $value);
                    $this->db->where('tbl_products.type_products', $key);
                    if (!empty($term)) {
                        $this->db->group_start();
                        $this->db->like('tbl_products.code', $term);
                        $this->db->or_like('tbl_products.name', $term);
                        $this->db->group_end();
                    }
                    $semi_product_outside = $this->db->get()->result_array();
                } elseif($key == 'materials'){
                    $quantityInventory = "(
                        SELECT
                            SUM(tblwarehouse_items.product_quantity)
                        FROM tblwarehouse_items
                        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                        WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
                    )";
                    $this->db->select('
                    tbl_materials.id as item_id,
                    "nvl" as item_type,
                    "materials" as item_type_root,
                    CONCAT("nvl__", tbl_materials.id) as id,
                    CONCAT(tbl_materials.name, "(", tbl_materials.code, ")") as text,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_materials.quantity_minimum as quantity,
                    COALESCE('.$quantityInventory.', 0) as quantity_inventory,
                    ', false);
                    $this->db->from('tbl_materials');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                    $this->db->where_in('tbl_materials.id', $value);
                    if (!empty($term)) {
                        $this->db->group_start();
                        $this->db->like('tbl_materials.code', $term);
                        $this->db->or_like('tbl_materials.name', $term);
                        $this->db->group_end();
                    }
                    $materials = $this->db->get()->result_array();
                } elseif($key == 'tools_supplies'){
                    $quantityInventoryTool = "(
                        SELECT
                            SUM(tblwarehouse_items.product_quantity)
                        FROM tblwarehouse_items
                        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                        WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
                    )";
                    $this->db->select('
                    tbl_tools_supplies.id as item_id,
                    "tools" as item_type,
                    "tools_supplies" as item_type_root,
                    CONCAT("tools__", tbl_tools_supplies.id) as id,
                    CONCAT(tbl_tools_supplies.name, "(", tbl_tools_supplies.code, ")") as text,
                    tblunits.unit as unit_name,
                    0 as quantity_bom,
                    tbl_tools_supplies.quantity_minimum as quantity,
                    COALESCE('.$quantityInventoryTool.', 0) as quantity_inventory,
                    ', false);
                    $this->db->from('tbl_tools_supplies');
                    $this->db->join('tblunits', 'tblunits.unitid = tbl_tools_supplies.unit_id', 'left');
                    $this->db->where_in('tbl_tools_supplies.id', $value);
                    if (!empty($term)) {
                        $this->db->group_start();
                        $this->db->like('tbl_tools_supplies.code', $term);
                        $this->db->or_like('tbl_tools_supplies.name', $term);
                        $this->db->group_end();
                    }
                    $tools = $this->db->get()->result_array();
                }
            }
        }

        $results = [];
        if (!empty($materials))
        {
            foreach ($materials as $key => $value){
                $quantity = $value['quantity_bom'];
                if(array_search($value['item_id'].'__'.$value['item_type_root'], array_column($array_item_find, 'id_item')) !== false) {
                    $quantity = array_column($array_item_find, 'quantity_bom','id_item')[$value['item_id'].'__'.$value['item_type_root']];
                } 
                $materials[$key]['quantity_bom'] = $quantity;
            }
            $results[]= ['text' => lang('materials'), 'children' => $materials];
        }
        if (!empty($semi_product_outside)) {
            foreach ($semi_product_outside as $key => $value){
                $quantity = $value['quantity_bom'];
                if(array_search($value['item_id'].'__'.$value['item_type_root'], array_column($array_item_find, 'id_item')) !== false) {
                    $quantity = array_column($array_item_find, 'quantity_bom','id_item')[$value['item_id'].'__'.$value['item_type_root']];
                } 
                $semi_product_outside[$key]['quantity_bom'] = $quantity;
            }
            $results[]= ['text' => lang('semi_products_outside'), 'children' => $semi_product_outside];
        }
        if (!empty($tools)) {
            foreach ($tools as $key => $value){
                $quantity = $value['quantity_bom'];
                if(array_search($value['item_id'].'__'.$value['item_type_root'], array_column($array_item_find, 'id_item')) !== false) {
                    $quantity = array_column($array_item_find, 'quantity_bom','id_item')[$value['item_id'].'__'.$value['item_type_root']];
                } 
                $tools[$key]['quantity_bom'] = $quantity;
            }
            $results[]= ['text' => lang('Công cụ vật tư'), 'children' => $tools];
        }
        $data['results'] = $results;
        echo json_encode($data);
    }
    public function add_purchase(){
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('date', lang("date"), 'required');
            if ($this->form_validation->run() == true) {
                $date = $this->input->post('date', true);
                $name = $this->input->post('name');
                $note = $this->input->post('note');
                $counter = $this->input->post('counter');
                $arr_id = [];
                $errors = '';
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arr = explode("__", $items_id);
                        $type = $arr[0];
                        $itemId = $arr[1];
                     
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

                $fields = [
                    'type_warning_warehouse'=>1,
                    'name' => $name,
                    'reason' => $note,
                    'date' => $date,
                    'items' => $items
                ];
                $purchases_id = $this->purchases_model->convertWarningWarehousePurchase($fields);
                if ($purchases_id > 0) {
                    $get_code = get_table_where('tblpurchases', array('id' => $purchases_id), '', 'row');
                    insertActivityLog([
                        'type_parent_obj' => 'purchase',
                        'table_obj' => 'tblpurchases',
                        'id_obj' => $purchases_id,
                        'name_obj' => $get_code->prefix . $get_code->code,
                        'content' => lang('tnh_add_purchase') . ' [' . $get_code->prefix . $get_code->code . ']',
                        'actions' => 'add_purchase'
                    ]);

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
        }
    }
    public function createPurchaseOrder(){
        $data = [];
        $id = $this->input->post('ids');
        $id = trim($id,',');
        if($id){
            $data['id'] = $id;
        }
        $data['staff'] = get_table_where('tblstaff');
		$data['suppliers'] = get_table_where('tblsuppliers', array('type' => 0));
		$data['tax'] = get_table_where('tbltaxes');
		$data['taxes'] = get_taxes_dropdown_template_new('', 0);
        $this->load->view('admin/warning_warehouse/create_purchase_order', $data);
    }
    public function add_purchase_order(){
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('suppliers_id', lang("Nhà cung cấp"), 'required');
            $this->form_validation->set_rules('delivery_date', lang("Ngày dự kiến giao hàng"), 'required');
            if ($this->form_validation->run() == true) {
                $date = $this->input->post('date');
                $suppliers_id = $this->input->post('suppliers_id');
                $delivery_date = $this->input->post('delivery_date');
                $delivery_cost = number_unformat($this->input->post('delivery_cost'));
                $reduce_cost = number_unformat($this->input->post('reduce_cost'));
                $discount_percent_suppliers = number_unformat($this->input->post('discount_percent_suppliers'));
                $tax_all = $this->input->post('tax_all');
                $valtype_check_suppliers = $this->input->post('valtype_check_suppliers');
                $type_check_suppliers = $this->input->post('type_check_suppliers');
                $note = $this->input->post('note');
                $counter = $this->input->post('counter');
                $arr_id = [];
                $errors = '';
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $items_id = $this->input->post('items_id')[$value];
                        if (empty($items_id)) continue;
                        $arr = explode("__", $items_id);
                        $type = $arr[0];
                        $itemId = $arr[1];
                     
                        $quantity = number_unformat($this->input->post('quantity')[$value]);
                        $price = number_unformat($this->input->post('price')[$value]);
                        $promotion_expected = number_unformat($this->input->post('promotion_expected')[$value]);
                        $tax_rate = $this->input->post('tax_rate')[$value];
                        $tax_id = $this->input->post('tax_id')[$value];
                        $note_item = $this->input->post('note_item')[$value];
                        $index = array_search($items_id, $arr_id);
                        if ($index === false) {
                            $arr_id[] = $items_id;
                        } else {
                            $errors .= 'Có mặt hàng bị trùng vui lòng xóa';
                        }

                       
                        $items[] = [
                            'id' => $itemId,
                            'quantity' => $quantity,
                            'quantity_suppliers' => $quantity,
                            'tax_id' => $tax_id,
                            'tax_rate' => $tax_rate,
                            'type' => $type,
                            'price_expected' => $price,
                            'price_suppliers' => $price,
                            'promotion_expected' => $promotion_expected,
                            'note' => $note_item
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

                $fields = [
                    'type_warning_warehouse'=>1,
                    'delivery_date' => $delivery_date,
                    'suppliers_id' => $suppliers_id,
                    'type_items' => -1,
                    'tax_all' => $tax_all,
                    'note' => $note,
                    'date' => $date,
                    'delivery_cost' => $delivery_cost,
                    'reduce_cost' => $reduce_cost,
                    'discount_percent_suppliers' => $discount_percent_suppliers,
                    'valtype_check_suppliers' => $valtype_check_suppliers,
                    'type_check_suppliers' => $type_check_suppliers,
                    'valtype_check_expected'=>0,
                    'items' => $items
                ];
                $purchases_order_id = $this->purchase_order_model->convertWarningWarehousePurchaseOrder($fields);
                if ($purchases_order_id > 0) {
                    $get_code = get_table_where('tblpurchase_order', array('id' => $purchases_order_id), '', 'row');
                    insertActivityLog([
                        'type_parent_obj' => 'purchase_order',
                        'table_obj' => 'tblpurchase_order',
                        'id_obj' => $purchases_order_id,
                        'name_obj' => $get_code->prefix . $get_code->code,
                        'content' => lang('Thêm mới đơn đặt hàng') . ' [' . $get_code->prefix . $get_code->code . ']',
                        'actions' => 'add_purchase_order'
                    ]);

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
        }
    }
}