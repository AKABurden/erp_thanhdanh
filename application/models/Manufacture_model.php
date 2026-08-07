<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Manufacture_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getWarehouseProductById($id)
    {
        $this->db->select('*');
        $this->db->from('tblwarehouse_product');
        $this->db->where('tblwarehouse_product.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertManufactures($data)
    {
        $this->db->insert('tbl_manufactures', $data);
        return $this->db->insert_id();
    }

    public function insertManufacturesItems($data)
    {
        $this->db->insert('tbl_manufactures_items', $data);
        return $this->db->insert_id();
    }

    public function insertManufacturesItemsBOM($data)
    {
        $this->db->insert('tbl_manufactures_items_bom', $data);
        return $this->db->insert_id();
    }

    public function insertManufacturesItemsBOMBatch($data)
    {
        return $this->db->insert_batch('tbl_manufactures_items_bom', $data);
    }

    public function getManufacturesItems($manufactures_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_manufactures_items');
        $this->db->where('tbl_manufactures_items.manufactures_id', $manufactures_id);
        return $this->db->get()->result_array();
    }

    public function getManufacturesItemsBOM($manufactures_items_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_manufactures_items_bom');
        $this->db->where('tbl_manufactures_items_bom.manufactures_items_id', $manufactures_items_id);
        return $this->db->get()->result_array();
    }

    public function getManufactures($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_manufactures');
        $this->db->where('tbl_manufactures.id', $id);
        return $this->db->get()->row_array();
    }

    public function updateManufactures($id, $data)
    {
        $this->db->where('tbl_manufactures.id', $id);
        return $this->db->update('tbl_manufactures', $data);
    }

    public function deleteManufacturesItems($manufactures_id)
    {
        $this->db->where('tbl_manufactures_items.manufactures_id', $manufactures_id);
        return $this->db->delete('tbl_manufactures_items');
    }

    public function deleteManufacturesItemsBOM($manufactures_id)
    {
        $this->db->where('tbl_manufactures_items_bom.manufactures_id', $manufactures_id);
        return $this->db->delete('tbl_manufactures_items_bom');
    }

    public function deleteManufactures($id)
    {
        $this->db->where('tbl_manufactures.id', $id);
        return $this->db->delete('tbl_manufactures');
    }

    public function getWarehouseProductViewId($id)
    {
        $this->db->select('
            tblwarehouse_product.*,
            tblwarehouse.name as warehouse_name,
            tbllocaltion_warehouses.name as location_name,
        ');
        $this->db->from('tblwarehouse_product');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tblwarehouse_product.warehouse_id');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_product.localtion');
        $this->db->where('tblwarehouse_product.id', $id);
        return $this->db->get()->row_array();
    }
    public function decreaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $export = get_table_where('tbl_manufactures', array('id' => $id), '', 'row');
            $items = get_table_where('tbl_manufactures_items', array('manufactures_id' => $id));
            $j = 0;
            foreach ($items as $key => $value) {
                $items_bom = get_table_where('tbl_manufactures_items_bom', array('manufactures_items_id' => $value['id']));
                foreach ($items_bom as $k => $v) {
                    $j++;
                    if ($v['type_items'] == 'products') {
                        $v['type_items'] = 'product';
                    } else
                        if ($v['type_items'] == 'materials') {
                        $v['type_items'] = 'nvl';
                    } else
                        if ($v['type_items'] == 'tools_supplies') {
                        $v['type_items'] = 'tools';
                    } else
                        if ($v['type_items'] == 'semi_products') {
                        $v['type_items'] = 'product';
                    }
                    // $warehouse_product = get_table_where('tblwarehouse_product', array('id' => $v['warehouse_product_id']), '', 'row');
                    $price = 0;
                    // if (!empty($warehouse_product)) {
                    //     $price = $warehouse_product->price;
                    // }
                    $date_warehouse = date('Y-m-d H:i:s');
                    $localtion  =  $v['location_item_id'];
                    $product_id = $v['item_id'];
                    $quantity = $v['quantity_item'];
                    $warehouse_id = $v['warehouse_item_id'];
                    $type_items = $v['type_items'];
                    $wareho_items_id = $v['warehouse_product_id'];
                    $date_export = explode(' ', $export->date);
                    $lot_code = $v['lot_code'];
                    $date_sx = $v['date_sx'];
                    $date_sd = $v['date_sd'];
                    $date_use = $v['date_use'];
                    $quantity_unit = $v['quantity_unit'];
                    $quantity_payment = $v['quantity_payment'];

                    // giảm
                    // manufacture_WarehuseQuantity($warehouse_id, $id, $date_warehouse, $date_export[0], $product_id, $quantity, $localtion, $type_items, $price, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                    // decreaseManufacture_WarehuseQuantity_v2($warehouse_id, $v['id'], $product_id, $quantity, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                    // decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                }
                if ($value['type_items'] == 'products') {
                    $value['type_items'] = 'product';
                } else
                    if ($value['type_items'] == 'materials') {
                    $value['type_items'] = 'nvl';
                } else
                    if ($value['type_items'] == 'tools_supplies') {
                    $value['type_items'] = 'tools';
                } else
                    if ($value['type_items'] == 'semi_products') {
                    $value['type_items'] = 'product';
                }
                $type_itemss = $value['type_items'];

                //tang
                increaseProductQuantityManufacture($value['warehouse_id'], $id, $date_warehouse, $date_export[0], $value['item_id'], $value['quantity'], $value['location_id'], $type_itemss, $price, $j, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                increaseWarehuseQuantity($value['warehouse_id'], $value['location_id'], $value['item_id'], $value['quantity'], $type_itemss, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
            }
        }
        return true;
    }
    public function increaseWarehouse($id)
    {
        $export = get_table_where('tbl_manufactures', array('id' => $id), '', 'row');
        $items = get_table_where('tbl_manufactures_items', array('manufactures_id' => $id));
        $count = 0;
        if ($export) {
            $date_warehouse = date('Y-m-d H:i:s');
            foreach ($items as $key => $value) {
                $items_bom = get_table_where('tbl_manufactures_items_bom', array('manufactures_items_id' => $value['id']));
                foreach ($items_bom as $k => $v) {
                    $localtion  =  $v['location_item_id'];
                    $quantity = $v['quantity_item'];
                    $quantitys_unit = $v['quantity_unit'];
                    $quantitys_payment = $v['quantity_payment'];
                    $lot_code = $v['lot_code'];
                    $date_sx = $v['date_sx'];
                    $date_sd = $v['date_sd'];
                    $date_use = $v['date_use'];

                    $warehouse_id = $v['warehouse_item_id'];
                    if ($v['type_items'] == 'products') {
                        $v['type_items'] = 'product';
                    } else
                        if ($v['type_items'] == 'materials') {
                        $v['type_items'] = 'nvl';
                    } else
                        if ($v['type_items'] == 'tools_supplies') {
                        $v['type_items'] = 'tools';
                    } else
                        if ($v['type_items'] == 'semi_products') {
                        $v['type_items'] = 'product';
                    }
                    $type_items = $v['type_items'];
                    $import = explode('|', trim($v['id_import'], '|'));
                    // foreach ($import as $ka => $va) {
                    //     $id_import = explode('-', $va);
                    //     $quantitya = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                    //     // $quantity_net = $id_import[1];
                    //     $quantity_net = $id_import[1];
                    //     $quantity_unit = $id_import[2];
                    //     $quantity_payment = $id_import[3];
                    //     $id_export =  str_replace('XKGH-' . $v['id'] . '|', '', $quantitya->id_export);
                    //     $this->db->where('id', $id_import[0]);
                    //     // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantitya->quantity_export - $quantity_net), 'quantity_left' => ($quantitya->quantity_left + $quantity_net), 'id_export' => $id_export));
                    //     $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantitya->quantity_export - $quantity_net), 'quantity_left' => ($quantitya->quantity_left + $quantity_net),'product_quantity_unit_export' => ($quantitya->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantitya->product_quantity_unit_left + $quantity_unit),'product_quantity_payment_export' => ($quantitya->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantitya->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                    // }
                    // $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 3888));
                    // increaseWarehuseQuantity($warehouse_id, $localtion, $v['item_id'], $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                }
            }
            $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 3888));
            $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 3888));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['product_quantity_unit'],$value['product_quantity_payment']);
            }
        }
        if ($count) {
            return true;
        }
        return false;
    }
    public function getWarehouse($not_arr_id = [], $is_keep_manufactures = NULL)
    {
        $this->db->select('tblwarehouse.*');
        $this->db->from('tblwarehouse');
        //        $this->db->where('tblwarehouse.id !=', WAREHOUSES_CAPACITY);
        //        $this->db->where('tblwarehouse.id !=', WAREHOUSES_SOLD);
        //        $this->db->where('tblwarehouse.id !=', WAREHOUSE_WARRANTY);
        //        $this->db->where('tblwarehouse.id !=', WAREHOUSE_SUPPLIES_TASK);
        if (!is_admin()) {
            $staff_id = get_staff_user_id();
            $this->db->join("tblwarehouse_staff", "tblwarehouse_staff.warehouse_id = tblwarehouse.id AND tblwarehouse_staff.staff_id = $staff_id");
        }
        if (!empty($not_arr_id)) {
            $this->db->where_not_in('tblwarehouse.id', $not_arr_id);
        }

        if ($is_keep_manufactures !== NULL) {
            $this->db->where('tblwarehouse.is_keep_manufactures', $is_keep_manufactures);
        }

        return $this->db->get()->result_array();
    }
    public function getTotalQuantityWarehousesv2($warehouse_id, $id_items, $type_items)
    {
        $this->db->select('tblwarehouse_items.*,tbllocaltion_warehouses.name', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.product_quantity >', 0);
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        return $this->db->get()->result_array();
    }
    public function getTotalQuantityWarehouses($warehouse_id, $id_items, $type_items)
    {
        $this->db->select('SUM(tblwarehouse_items.product_quantity) as total_quantity', false);
        $this->db->from('tblwarehouse_items');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_items.localtion');
        $this->db->where('tblwarehouse_items.id_items', $id_items);
        $this->db->where('tblwarehouse_items.type_items', $type_items);
        $this->db->where('tblwarehouse_items.warehouse_id', $warehouse_id);
        // $this->db->where('tbllocaltion_warehouses.status_default', 1);
        return $this->db->get()->row_array();
    }
    public function getTotalQuantityWarehousesDetail($warehouse_id, $id_items, $type_items)
    {
        $this->db->select('tblwarehouse_product.*,tbllocaltion_warehouses.name', false);
        $this->db->from('tblwarehouse_product');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tblwarehouse_product.localtion');
        $this->db->where('tblwarehouse_product.product_id', $id_items);
        $this->db->where('tblwarehouse_product.quantity_left >', 0);
        $this->db->where('tblwarehouse_product.type_items', $type_items);
        $this->db->where('tblwarehouse_product.warehouse_id', $warehouse_id);
        // $this->db->where('tbllocaltion_warehouses.status_default', 1);
        return $this->db->get()->result_array();
    }

    public function getProductionsOrdersItemsSub($id_production_detail, $item_type, $item_id) {
        $this->db->select('
            tbl_productions_orders_items_sub.*
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_productions_orders_items_sub', 'tbl_productions_orders_items_sub.productions_orders_items_id = tbl_productions_orders_items.id');
        $this->db->where('tbl_productions_orders_details.id', $id_production_detail);
        $this->db->where('tbl_productions_orders_items_sub.type', $item_type);
        $this->db->where('tbl_productions_orders_items_sub.item_id', $item_id);
        return $this->db->get()->row_array();
    }

    public function getProductionsOrdersDetail($pod_id) {
        $this->db->select('
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_productions_orders_items.quantity as quantity
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id');
        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        return $this->db->get()->row_array();
    }

    public function insertManufacturesMaterials($data) {
        $this->db->insert('tbl_manufactures_materials', $data);
        return $this->db->insert_id();
    }

    public function insertManufacturesMaterialsDetail($data) {
        $this->db->insert('tbl_manufactures_materials_detail', $data);
        return $this->db->insert_id();
    }

    public function deleteManufacturesMaterials($manufactures_id) {
        $this->db->where('tbl_manufactures_materials.manufactures_id', $manufactures_id);
        return $this->db->delete('tbl_manufactures_materials');
    }

    public function deleteManufacturesMaterialsDetail($manufactures_id) {
        $this->db->where('tbl_manufactures_materials_detail.manufactures_id', $manufactures_id);
        return $this->db->delete('tbl_manufactures_materials_detail');
    }
}
