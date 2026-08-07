<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stock_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function searchProductionsOrdersDetailsForStockNew($term, $limit)
    {
        $this->db->select('
            tbl_productions_orders_details.id as id, 
            CONCAT(tbl_productions_orders_details.reference_no, "(", tbl_productions_orders_items.items_name, ")") as text,
            tbl_orders.reference_no as reference_no_order,
            tbl_orders.customer_name as customer_name,
            tbl_productions_orders.reference_no as reference_no_production,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $term);
            $this->db->or_like('tbl_productions_orders.reference_no', $term);
            $this->db->or_like('tbl_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_productions_orders_details.date_created', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchProductionsOrdersDetailsForStock($term, $limit)
    {
        $this->db->select('tbl_productions_orders_details.id as id, CONCAT(tbl_productions_orders_details.reference_no, "(", tbl_productions_orders_items.items_name, ")") as text', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_productions_orders_details.date_created', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchMaterialProductionsOrders($term, $limit, $productions_orders_detail_id)
    {
        $this->db->select('
    		tbl_productions_orders_items_sub.unit_id as unit_id,
    		tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
    		tbl_productions_orders_items_sub.item_id as item_id,
    		CONCAT("materials__", tbl_productions_orders_items_sub.item_id) as id,
    		CONCAT(tbl_productions_orders_items_sub.item_code, "(", tbl_productions_orders_items_sub.item_name,")") as text,
    		tblunits.unit as unit_name,
    		unit_parent.unit as unit_parent_name,
    		tbl_productions_orders_items_sub.item_name as item_name,
    		tbl_productions_orders_items_sub.quantity_exchange as number_exchange,
            SUM(tbl_productions_orders_items_sub.quantity) as quantity
    		', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_items_sub.item_code', $term);
            $this->db->or_like('tbl_productions_orders_items_sub.item_name', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_productions_orders_details.id', $productions_orders_detail_id);
        $this->db->where('tbl_productions_orders_items_sub.type', 'materials');
        $this->db->group_by('tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id');
        $this->db->limit($limit);
        // print_arrays($this->db->get_compiled_select(), FALSE);
        return $this->db->get()->result_array();
    }

    public function searchSemiProductProductionsOrders($term, $limit, $productions_orders_detail_id)
    {
        $this->db->select('
    		tbl_productions_orders_items_sub.unit_id as unit_id,
    		tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
    		tbl_productions_orders_items_sub.item_id as item_id,
    		CONCAT("semi_products_outside__", tbl_productions_orders_items_sub.item_id) as id,
    		CONCAT(tbl_productions_orders_items_sub.item_code, "(", tbl_productions_orders_items_sub.item_name, ")") as text,
            tblunits.unit as unit_name,
            unit_parent.unit as unit_parent_name,
    		tbl_productions_orders_items_sub.item_name as item_name,
    		tbl_productions_orders_items_sub.quantity_exchange as number_exchange,
            SUM(tbl_productions_orders_items_sub.quantity) as quantity
    		', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_items_sub.item_code', $term);
            $this->db->or_like('tbl_productions_orders_items_sub.item_name', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_productions_orders_details.id', $productions_orders_detail_id);
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products_outside');
        $this->db->group_by('tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchSemiProductsProductionsOrders($term, $limit, $productions_orders_detail_id)
    {
        $this->db->select('
            tbl_productions_orders_items_sub.unit_id as unit_id,
            tbl_productions_orders_items_sub.unit_parent_id as unit_parent_id,
            tbl_productions_orders_items_sub.item_id as item_id,
            CONCAT("semi_products__", tbl_productions_orders_items_sub.item_id) as id,
            CONCAT(tbl_productions_orders_items_sub.item_code, "(", tbl_productions_orders_items_sub.item_name, ")", "(", tblunits.unit, ")") as text,
            tblunits.unit as unit_name,
            unit_parent.unit as unit_parent_name,
            tbl_productions_orders_items_sub.item_name as item_name,
            tbl_productions_orders_items_sub.quantity_exchange as number_exchange,
            SUM(tbl_productions_orders_items_sub.quantity) as quantity
            ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_productions_orders_items_sub.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_productions_orders_items_sub.unit_parent_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_items_sub.item_code', $term);
            $this->db->or_like('tbl_productions_orders_items_sub.item_name', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_productions_orders_details.id', $productions_orders_detail_id);
        $this->db->where('tbl_productions_orders_items_sub.type', 'semi_products');
        $this->db->group_by('tbl_productions_orders_items_sub.item_id, tbl_productions_orders_items_sub.unit_id');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function checkExistSuggestExportingReferenceStock($reference_no)
    {
        $this->db->from('tbl_suggest_exporting');
        $this->db->where('tbl_suggest_exporting.reference_stock', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function getSuggestExportingItemsForStock($suggest_exporting_id)
    {
        $this->db->select('tbl_suggest_exporting_items.*, tblunits.unit as unit_name');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_exporting_items.unit_id', 'left');
        $this->db->where('tbl_suggest_exporting_items.suggest_exporting_id', $suggest_exporting_id);
        return $this->db->get()->result_array();
    }

    public function rowSuggestExportingItems($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getWarehouses($supplier = false, $arr_not_id = [], $staff_id = 0)
    {
        $staff_id = !empty($staff_id) ? $staff_id : get_staff_user_id();
        $isAdmin = is_admin($staff_id);

        $this->db->select('tblwarehouse.*', false);
        $this->db->from('tblwarehouse');
        if (!$isAdmin) {
            // $this->db->join("tblwarehouse_staff", "tblwarehouse_staff.warehouse_id = tblwarehouse.id AND tblwarehouse_staff.staff_id = $staff_id");
        }
        if (empty($supplier)) {
            $this->db->where('tblwarehouse.supplier_id', 0);
        }

        if (!empty($arr_not_id)) {
            $this->db->where_not_in('tblwarehouse.id', $arr_not_id);
        }
        return $this->db->get()->result_array();
    }

    public function getWarehouseItemsByItemIdAndTypeAndWarehouse($id_items, $type_items, $warehouse_id)
    {
        $this->db->select('tblwarehouse_items.*');
        $this->db->from('tblwarehouse_items');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        // $type_items = ($type_items == "materials") ? 'nvl' : 'product';
        if ($type_items == "materials") {
            $type_items = "nvl";
        } else if ($type_items == "products") {
            $type_items = "product";
        } else if ($type_items == "semi_products" || $type_items == "semi_products_outside") {
            $type_items = "product";
        } else {
            $type_items = "tools";
        }
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        return $this->db->get()->result_array();
    }

    public function rowWarehouse($id)
    {
        $this->db->select('*');
        $this->db->from('tblwarehouse');
        $this->db->where('tblwarehouse.id', $id);
        return $this->db->get()->row_array();
    }

    public function searchSemiProductsOutside($q, $limit = 50)
    {
        $this->db->select('CONCAT("semi_products_outside__", tbl_products.id) as id, CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text, tbl_products.name as name, 
        tbl_products.conversion_unit as unit_id, 
        unit_stock.unit as unit_name,
        tbl_products.unit_id as unit_parent_id,
        tbl_products.id as item_id,
        tblunits.unit as unit_parent_name,
        tbl_products.name as item_name,
        tbl_products.code as item_code,
        tbl_products.conversion_quantity_unit as number_exchange,
        1 as quantity
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tblunits unit_stock', 'unit_stock.unitid = tbl_products.conversion_unit', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products', 'semi_products_outside');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchSemiProducts($q, $limit = 50)
    {
        $this->db->select('CONCAT("semi_products__", tbl_products.id) as id, CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text, tbl_products.name as name, 
        tbl_products.conversion_unit as unit_id, 
        unit_stock.unit as unit_name,
        tbl_products.unit_id as unit_parent_id,
        tbl_products.id as item_id,
        tblunits.unit as unit_parent_name,
        tbl_products.name as item_name,
        tbl_products.code as item_code,
        tbl_products.conversion_quantity_unit as number_exchange,
        1 as quantity', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tblunits unit_stock', 'unit_stock.unitid = tbl_products.conversion_unit', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products', 'semi_products');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchMaterials($q, $limit = 50)
    {
        $this->db->select('CONCAT("materials__", tbl_materials.id) as id, CONCAT(tbl_materials.code, "(", tbl_materials.name,")") as text, tbl_materials.name as name, tbl_materials.unit_id as unit_id, tblunits.unit as unit_name,
        tbl_materials.unit_id as unit_parent_id,
        tbl_materials.id as item_id,
        tblunits.unit as unit_parent_name,
        tbl_materials.name as item_name,
        1 as number_exchange,
        1 as quantity
        ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }
    
    public function searchMaterialsUnitWarehouse($q, $limit = 50)
    {
        $this->db->select('CONCAT("materials__", tbl_materials.id) as id, CONCAT(tbl_materials.code, "(", tbl_materials.name,")") as text, tbl_materials.name as name, tbl_materials.standard_unit as unit_id, tblunits.unit as unit_name,
        tbl_materials.standard_unit as unit_parent_id,
        tbl_materials.id as item_id,
        tblunits.unit as unit_parent_name,
        tbl_materials.name as item_name,
        tbl_materials.code as item_code,
        1 as number_exchange,
        1 as quantity
        ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.standard_unit', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }
    public function searchToolsSupplies($term, $limit = 50)
    {
        $this->db->select('
            CONCAT("tools_supplies__", tbl_tools_supplies.id) as id,
            CONCAT(tbl_tools_supplies.code, "(", tbl_tools_supplies.name, ")") as text,
            tbl_tools_supplies.name as name,
            tbl_tools_supplies.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_tools_supplies.name as item_name,
            tbl_tools_supplies.code as item_code,
            1 as number_exchange,
            tbl_tools_supplies.id as item_id,
            tbl_tools_supplies.unit_id as unit_parent_id,
            tblunits.unit as unit_parent_name,
        ', false);
        $this->db->from('tbl_tools_supplies');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_tools_supplies.unit_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_tools_supplies.code', $term);
            $this->db->or_like('tbl_tools_supplies.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function checkExistPurchaseProducts($reference_no)
    {
        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function insertPurchaseProducts($data)
    {
        $this->db->insert('tbl_purchase_products', $data);
        return $this->db->insert_id();
    }

    public function insertPurchaseProductItems($data)
    {
        $this->db->insert('tbl_purchase_product_items', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPurchaseProductItems($data)
    {
        return $this->db->insert_batch('tbl_purchase_product_items', $data);
    }

    public function rowPurchaseProducts($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_purchase_products');
        $this->db->where('tbl_purchase_products.id', $id);
        return $this->db->get()->row_array();
    }

    public function getPurchaseProductItems($purchase_product_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_purchase_product_items');
        $this->db->where('tbl_purchase_product_items.purchase_product_id', $purchase_product_id);
        return $this->db->get()->result_array();
    }

    public function deletePurchaseProducts($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_purchase_products');
    }

    public function deletePurchaseProductItems($purchase_product_id)
    {
        $this->db->where('purchase_product_id', $purchase_product_id);
        return $this->db->delete('tbl_purchase_product_items');
    }

    public function updatePurchaseProducts($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_purchase_products', $data);
    }
    public function increaseadWarehouse_purchase_internal($id)
    {
        //giảm kho phiếu nhập xóa dữ liệu trong kho
        if (is_numeric($id)) {
            $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 20));
            $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 20));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'],$value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['product_quantity_unit'],$value['product_quantity_payment']);
            }
        }
        return true;
    }
    public function decreaseWarehouse_purchase_internal($id)
    {
        if (is_numeric($id)) {
            $import = get_table_where('tbl_purchase_internal', array('id' => $id), '', 'row');
            $items = get_table_where('tbl_purchase_internal_items', array('purchase_internal_id' => $id));
            foreach ($items as $key => $value) {
                if ($value['type_item'] == 'products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'materials') {
                    $value['type_item'] = 'nvl';
                }
                if ($value['type_item'] == 'tools_supplies') {
                    $value['type_item'] = 'tools';
                }
                if ($value['type_item'] == 'semi_products') {
                    $value['type_item'] = 'product';
                }
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['location_id'];
                $product_id = $value['item_id'];
                $type_items = $value['type_item'];
                $quantity = $value['quantity'];
                $price = $value['price'];
                $quantity_unit = $value['quantity_unit'];
                $quantity_payment = $value['quantity_payment'];
                $lot_code = $value['lot_code'];
                $date_sx = $value['date_sx'];
                $date_sd = $value['date_sd'];
                $date_use = empty($value['date_use']) ? NULL : $value['date_use'];
                $date_import = explode(' ', $import->date);
                $count = increase_purchase_internalProductQuantity($import->warehouse_id, $id, $date_warehouse, $date_import[0], $product_id, $quantity, $localtion, $type_items, $price, $value['id'], $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
                //tăng kho tổng
                increaseWarehuseQuantity($import->warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
            }
        }
        return true;
    }
    public function decreaseWarehouse_purchase_products($id)
    {
        if (is_numeric($id)) {
            $import = get_table_where('tbl_purchase_products', array('id' => $id), '', 'row');
            $items = get_table_where('tbl_purchase_product_items', array('purchase_product_id' => $id));
            foreach ($items as $key => $value) {
                if ($value['type_item'] == 'products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'materials') {
                    $value['type_item'] = 'nvl';
                }
                if ($value['type_item'] == 'tools_supplies') {
                    $value['type_item'] = 'tools';
                }
                if ($value['type_item'] == 'semi_products') {
                    $value['type_item'] = 'product';
                }
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['location_id'];
                $product_id = $value['item_id'];
                $type_items = $value['type_item'];
                $quantity = $value['quantity_stock'];
                $quantity_unit = $value['quantity_unit'];
                $quantity_payment = $value['quantity_payment'];
                $date_import = explode(' ', $import->date);
                $count = increase_purchase_productsProductQuantity($import->warehouse_id, $id, $date_warehouse, $date_import[0], $product_id, $quantity, $localtion, $type_items,$quantity_unit,$quantity_payment);
                //tăng kho tổng
                increaseWarehuseQuantity($import->warehouse_id, $localtion, $product_id, $quantity, $type_items,NULL,NULL,NULL,NULL,$quantity_unit,$quantity_payment);
            }
        }
        return true;
    }

    public function decreaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $sub_total = 0;
            $export = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
            $items = get_table_where('tbl_suggest_exporting_items', array('suggest_exporting_id' => $id));
            foreach ($items as $key => $value) {
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['location_id'];
                $product_id = $value['item_id'];
                if ($value['type_item'] == 'products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'materials') {
                    $value['type_item'] = 'nvl';
                }
                if ($value['type_item'] == 'tools_supplies') {
                    $value['type_item'] = 'tools';
                }
                if ($value['type_item'] == 'semi_products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'semi_products_outside') {
                    $value['type_item'] = 'product';
                }
                $type_items = $value['type_item'];
                $quantity = $value['quantity_warehouse'];
                $warehouse_id = $value['warehouse_item_id'];

                $quantity_unit = $value['quantity_exchange'];
                $quantity_payment = $value['quantity_payment'];
                
                $date_export = explode(' ', $export->date);
                exporting_producion_WarehuseQuantity($warehouse_id, $id, $date_warehouse, $date_export[0], $product_id, $quantity, $localtion, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);
                decreaseexporting_producion_WarehuseQuantity($warehouse_id, $value['id'], $product_id, $quantity, $localtion, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);
                //trừ kho tổng
                decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);

                $itemss = get_table_where('tbl_suggest_exporting_items', array('id' => $value['id']), '', 'row')->id_import;
                $array = explode('|', $itemss);
                $amount = 0;
                foreach ($array as $k => $v) {
                    if (!empty($v)) {
                        $waretos = explode('-', $v);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;
                        $quantitys = $waretos[3];
                        if($quantitys == 0){
                            $quantitys = $waretos[1];
                        }
                        $amount += $quantitys * $price;
                        $__data['id_suggest_exporting'] = $id;
                        $__data['id_suggest_exporting_items'] = $value['id'];
                        $__data['quantity'] = $quantitys;
                        $__data['price'] = $price;
                        $this->db->insert('tbl_suggest_exporting_items_pirce', $__data);
                    }
                }
                $sub_total += $amount;
                $this->db->update('tbl_suggest_exporting_items', array('amount' => $amount), array('id' => $value['id']));
            }
            $this->db->update('tbl_suggest_exporting', array('grand_total' => $sub_total), array('id' => $id));
        }
        return true;
    }
    public function increaseadWarehouse($id = '', $data = '', $warehouse_id)
    {
        if (is_numeric($id) && !empty($data)) {
            //tăng kho khi xóa
            foreach ($data as $key => $value) {

                $import = explode('|', trim($value['id_import'], '|'));
                foreach ($import as $k => $v) {
                    $id_import = explode('-', $v);
                    $quantity = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                    $quantity_net = $id_import[1];
                    $quantity_unit = $id_import[2];
                    $quantity_payment = $id_import[3];

                    $id_export =  str_replace('XKSX-' . $value['id'] . '|', '', $quantity->id_export);
                    $this->db->where('id', $id_import[0]);
                    // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));
                    $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net),'product_quantity_unit_export' => ($quantity->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantity->product_quantity_unit_left + $quantity_unit),'product_quantity_payment_export' => ($quantity->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantity->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                }
                $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 17));
                if ($value['type_item'] == 'products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'materials') {
                    $value['type_item'] = 'nvl';
                }
                if ($value['type_item'] == 'tools_supplies') {
                    $value['type_item'] = 'tools';
                }
                if ($value['type_item'] == 'semi_products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'semi_products_outside') {
                    $value['type_item'] = 'product';
                }
                increaseWarehuseQuantity($value['warehouse_item_id'], $value['location_id'], $value['item_id'], $value['quantity_warehouse'], $value['type_item'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['quantity_exchange'],$value['quantity_payment']);
            }
            $this->db->delete('tbl_suggest_exporting_items_pirce', array('id_suggest_exporting' => $id));
            $this->db->update('tbl_suggest_exporting', array('grand_total' => 0), array('id' => $id));
            $this->db->update('tbl_suggest_exporting_items', array('amount' => 0), array('suggest_exporting_id' => $id));
        }
        return true;
    }
    public function increaseadWarehouse_purchase_products($id)
    {
        //giảm kho phiếu nhập xóa dữ liệu trong kho
        if (is_numeric($id)) {
            $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 18));
            $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 18));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'],$value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['product_quantity_unit'],$value['product_quantity_payment']);
            }
        }
        return true;
    }

    public function checkExistPurchaseInternal($reference_no)
    {
        $this->db->from('tbl_purchase_internal');
        $this->db->where('tbl_purchase_internal.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function insertPurchaseInternal($data = [])
    {
        $this->db->insert('tbl_purchase_internal', $data);
        return $this->db->insert_id();
    }

    public function insertPurchaseInternalItems($data = [])
    {
        $this->db->insert('tbl_purchase_internal_items', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPurchaseInternalItems($data = [])
    {
        return $this->db->insert_batch('tbl_purchase_internal_items', $data);
    }

    public function updatePurchaseInternal($id, $data = [])
    {
        $this->db->where('tbl_purchase_internal.id', $id);
        return $this->db->update('tbl_purchase_internal', $data);
    }

    public function deletePurchaseInternal($id)
    {
        $this->db->where('tbl_purchase_internal.id', $id);
        return $this->db->delete('tbl_purchase_internal');
    }

    public function deletePurchaseInternalItems($purchase_internal_id)
    {
        $this->db->where('tbl_purchase_internal_items.purchase_internal_id', $purchase_internal_id);
        return $this->db->delete('tbl_purchase_internal_items');
    }

    public function searchSemiProductsOutsideAndUnit($q, $limit = 50)
    {
        $this->db->select('
            CONCAT("semi_products_outside__", tbl_products.id) as id,
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
            tbl_products.name as name,
            tbl_products.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_products.price_import as price_import
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products', 'semi_products_outside');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchMaterialsAndUnit($q, $limit = 50)
    {
        $this->db->select('
            CONCAT("materials__", tbl_materials.id) as id,
            CONCAT(tbl_materials.code, "(", tbl_materials.name,")") as text,
            tbl_materials.name as name, tbl_materials.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_materials.price_import as price_import
            ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchToolsSuppliesAndUnit($term, $limit = 50)
    {
        $this->db->select('
            CONCAT("tools_supplies__", tbl_tools_supplies.id) as id,
            CONCAT(tbl_tools_supplies.code, "(", tbl_tools_supplies.name, ")") as text,
            tbl_tools_supplies.name as name,
            tbl_tools_supplies.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_tools_supplies.price_import as price_import
        ', false);
        $this->db->from('tbl_tools_supplies');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_tools_supplies.unit_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_tools_supplies.code', $term);
            $this->db->or_like('tbl_tools_supplies.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchMaterialsPODExportWarehouse($pod_id, $term, $limit, $type_item)
    {
        $this->db->select('
            CONCAT(tbl_suggest_exporting_items.type_item, "__", tbl_suggest_exporting_items.item_id) as id,
            CONCAT(tbl_suggest_exporting_items.item_code, "(", tbl_suggest_exporting_items.item_name, ")") as text,
            tbl_suggest_exporting_items.item_name as name,
            tbl_suggest_exporting_items.unit_parent_id as unit_id,
            tblunits.unit as unit_name,
            tbl_suggest_exporting_items.item_id,
            tbl_suggest_exporting_items.type_item,
            IF (tbl_suggest_exporting_items.type_item = "materials", tbl_materials.price_import, IF (tbl_suggest_exporting_items.type_item = "tools_supplies", tbl_tools_supplies.price_import, tbl_products.price_import)) as price_import
        ', false);
        $this->db->from('tbl_suggest_exporting');
        $this->db->join('tbl_suggest_exporting_items', 'tbl_suggest_exporting_items.suggest_exporting_id = tbl_suggest_exporting.id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_exporting_items.unit_parent_id', 'left');
        // if ($type_item == "materials") {
        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "materials"', 'left');
        // } else if ($type_item == "semi_products_outside") {
        $this->db->join('tbl_products', 'tbl_products.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "products"', 'left');
        // } else if ($type_item == "tools_supplies") {
        $this->db->join('tbl_tools_supplies', 'tbl_tools_supplies.id = tbl_suggest_exporting_items.item_id AND tbl_suggest_exporting_items.type_item = "tools_supplies"', 'left');
        // }
        $this->db->where('tbl_suggest_exporting.productions_orders_details_id', $pod_id);
        $this->db->where('tbl_suggest_exporting_items.type_item', $type_item);
        $this->db->group_by('tbl_suggest_exporting_items.item_id, tbl_suggest_exporting_items.type_item');
        return $this->db->get()->result_array();
    }

    public function rowPurchaseInternal($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_purchase_internal');
        $this->db->where('tbl_purchase_internal.id', $id);
        return $this->db->get()->row_array();
    }

    public function getPurchaseInternalItems($purchase_internal_id)
    {
        $this->db->select('
            tbl_purchase_internal_items.*,
            tblunits.unit as unit_name,
            ', false);
        $this->db->from('tbl_purchase_internal_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_purchase_internal_items.unit_id', 'left');
        $this->db->where('tbl_purchase_internal_items.purchase_internal_id', $purchase_internal_id);
        return $this->db->get()->result_array();
    }

    public function searchPurchaseProduct($term, $limit)
    {
        $this->db->select('tbl_purchase_products.id as id, tbl_purchase_products.reference_no as text', false);
        $this->db->from('tbl_purchase_products');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_purchase_products.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_purchase_products.reference_no', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    //
    public function insertPurchaseProductItemExchange($data)
    {
        $this->db->insert('tbl_purchase_product_item_exchange', $data);
        return $this->db->insert_id();
    }

    public function deletePurchaseProductItemExchange($purchase_product_items_id)
    {
        $this->db->where('tbl_purchase_product_item_exchange.purchase_product_items_id', $purchase_product_items_id);
        return $this->db->delete('tbl_purchase_product_item_exchange');
    }

    public function getPurchaseProductItemExchange($purchase_product_items_id)
    {
        $this->db->select('tbl_purchase_product_item_exchange.*, tblunits.unit as unit_name');
        $this->db->from('tbl_purchase_product_item_exchange');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_purchase_product_item_exchange.unit_id', 'left');
        $this->db->where('tbl_purchase_product_item_exchange.purchase_product_items_id', $purchase_product_items_id);
        return $this->db->get()->result_array();
    }

    public function getPurchaseProductItemExchangeView($purchase_product_items_id)
    {
        $this->db->select('tbl_purchase_product_item_exchange.*, tblunits.unit as unit_name');
        $this->db->from('tbl_purchase_product_item_exchange');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_purchase_product_item_exchange.unit_id', 'left');
        $this->db->where('tbl_purchase_product_item_exchange.purchase_product_items_id', $purchase_product_items_id);
        return $this->db->get()->result_array();
    }

    public function getPurchaseProductItemExchangeBox($purchase_product_items_id)
    {
        $this->db->select('tbl_purchase_product_item_exchange.*');
        $this->db->from('tbl_purchase_product_item_exchange');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_purchase_product_item_exchange.unit_id');
        $this->db->where('LOWER(tblunits.unit)', 'thùng');
        $this->db->where('tbl_purchase_product_item_exchange.purchase_product_items_id', $purchase_product_items_id);
        return $this->db->get()->row_array();
    }

    public function getWarehouseItems($id_items, $type_items)
    {
        $isAdmin = is_admin();
        $staff_id = get_staff_user_id();

        $this->db->select('tblwarehouse_items.*, tblwarehouse.name as name_warehouse');
        $this->db->from('tblwarehouse_items');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_items.warehouse_id');
        $this->db->where('tblwarehouse_items.id_items', $id_items);

        if ($type_items == "materials") {
            $type_items = "nvl";
        } else if ($type_items == "products") {
            $type_items = "product";
        } else if ($type_items == "semi_products" || $type_items == "semi_products_outside") {
            $type_items = "product";
        } else {
            $type_items = "tools";
        }

        if (!$isAdmin) {
            $this->db->join("tblwarehouse_staff", "tblwarehouse_staff.warehouse_id = tblwarehouse.id AND tblwarehouse_staff.staff_id = $staff_id");
        }

        $this->db->where('tblwarehouse_items.type_items', $type_items);
        return $this->db->get()->result_array();
    }
}
