<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Export_different_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get($id = '')
    {
        $this->db->select('tblexport_different.*')->distinct();
        $this->db->from('tblexport_different');
        $this->db->where('tblexport_different.id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_Export_different($id);
        return $purchases;
    }
    public function get_items_Export_different($id)
    {
        $items = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $quantity_warehoue = get_table_where('tblwarehouse_items', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'warehouse_id' => $value['warehouses_id'], 'localtion' => $value['localtion_warehouses_id']), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            } else if ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());

                $quantity_warehoue = get_table_where('tblwarehouse_items', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'warehouse_id' => $value['warehouses_id'], 'localtion' => $value['localtion_warehouses_id']), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            }else if ($value['type'] == 'product') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_payment,stock_unit.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.conversion_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());

                $quantity_warehoue = get_table_where('tblwarehouse_items', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'warehouse_id' => $value['warehouses_id'], 'localtion' => $value['localtion_warehouses_id']), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());

                $quantity_warehoue = get_table_where('tblwarehouse_items', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'warehouse_id' => $value['warehouses_id'], 'localtion' => $value['localtion_warehouses_id']), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            }
            $localton = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row')->name_parent;
            $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
            $warehouse_name = get_table_where('tblwarehouse', array('id' => $value['warehouses_id']), '', 'row')->name;
            $items[$key] = array_merge($items[$key], array('warehouse_name' => $warehouse_name));
        }
        return $items;
    }
    public function add($data)
    {
        if ($data) {
            $items = $data['items'];
            $total = 0;
            if (isset($data['note'])) nl2br($data['note']);
            $export_different = array(
                'code' => sprintf('%06d', ch_getMaxID('id', 'tblexport_different') + 1),
                'note' => $data['note'],
                'object' => $data['object'],
                'id_object' => $data['id_object'],
                'object_text' => $data['object_text'],
                'prefix' => get_option('prefix_export_different'),
                'date' => to_sql_date($data['date'], true),
                'staff_id' => get_staff_user_id(),
                'date_create' => date('Y:m:d H:i:s'),
                'status' => 0,
                'id_branch' => $data['id_branch'],
                'po_id' => $data['po_id'] ?? 0,
                'type_po' => $data['type_po'] ?? 0,
            );
            $this->db->insert('tblexport_different', $export_different);
            $id = $this->db->insert_id();
            if ($id) {
                foreach ($items as $key => $item) {
                    if (!empty($item['id'])) {
                        $quantity_stock =  str_replace(',', '', $item['quantity_net']);
                        $data_items = get_items($item['id'], $item['type']);
                        if ($item['type'] == 'nvl') {
                            $recipe = $data_items->recipe;
                            $paper = $data_items->paper;
                            $longs = $data_items->longs;
                            $wide = $data_items->wide;
                            $exchange_unit = $data_items->exchange_unit;    //chuan
                            $exchange_standard_unit = $data_items->exchange_standard_unit; //kho
                            $exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
                            $quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
                            if ($recipe == 1) {
                                $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_unit;
                            } elseif ($recipe == 2) {
                                $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
                            } elseif ($recipe == 3) {
                                $quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
                            }
                        } elseif ($item['type'] == 'product') {
                            $recipe = 1;
                            $paper = 1;
                            $longs = 1;
                            $wide = 1;
                            $exchange_unit = 0;    //chuan
                            $exchange_standard_unit = $data_items->conversion_quantity_unit; //kho
                            $exchange_unit_payment = 0; //thanh toan
                            $quantity_unit = ($quantity_stock / $exchange_standard_unit);
                            $quantity_payment = 0;
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

                        $warehouses_id = get_table_where('tblwarehouse_items', array('id' => $item['warehouses_id']), '', 'row');
                        $_item['id_export_different'] = $id;
                        $_item['product_id'] = $item['id'];
                        $_item['id_warehouse_items'] = $item['warehouses_id'];
                        $_item['price'] = str_replace(',', '', $item['price']);
                        // $_item['warehouses_id'] = $item['warehouses_id'];
                        // $_item['localtion_warehouses_id'] = $item['localtion_warehouses_id'];
                        $_item['warehouses_id'] = $warehouses_id->warehouse_id;
                        $_item['localtion_warehouses_id'] = $warehouses_id->localtion;
                        $_item['lot_code'] = $warehouses_id->lot_code;
                        $_item['date_sx'] = $warehouses_id->date_sx;
                        $_item['date_sd'] = $warehouses_id->date_sd;
                        $_item['date_use'] = $warehouses_id->date_use;
                        $_item['quantity_unit'] = $quantity_unit;
                        $_item['quantity_stock'] = $quantity_stock;
                        $_item['quantity_payment'] = $quantity_payment;
                        $_item['exchange_unit'] = $exchange_unit;
                        $_item['exchange_stock'] = $exchange_standard_unit;
                        $_item['exchange_payment'] = $exchange_unit_payment;
                        $_item['type'] = $item['type'];
                        $_item['note'] = $item['note'];
                        $_item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                        $amount = ($_item['quantity_payment'] * $_item['price']);
                        $_item['amount'] = $amount;

                        $_item['po_id'] = $item['po_id'] ?? 0;

                        $total += $amount;
                        $this->db->insert('tbltblexport_different_items', $_item);
                        $idd = $this->db->insert_id();
                    }
                }
                $this->db->update('tblexport_different', array('subtotal' => $total), array('id' => $id));
                log_activity('Export Different add [ID: ' . $id . ']');
                return $id;
            }
        }
        return false;
    }
    public function update($data = NULL, $id = '')
    {
        $total = 0;
        $items = $data['items'];
        if (isset($data['note'])) nl2br($data['note']);
        $inventory = array(
            'note' => $data['note'],
            'object' => $data['object'],
            'id_object' => $data['id_object'],
            'object_text' => $data['object_text'],
            'id_branch' => $data['id_branch'],
            'po_id' => $data['po_id'] ?? 0,
            'type_po' => $data['type_po'] ?? 0,
        );
        $this->db->where('id', $id);
        if ($this->db->update('tblexport_different', $inventory)) {
            log_activity('Export Different updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 0;
        }
        $total = 0;
        $total_amount = 0;
        $items = $data['items'];
        if ($count) {
            $affected_id = array();
            $this->db->where('id_export_different', $id);
            $this->db->delete('tbltblexport_different_items');
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                	$quantity_stock =  str_replace(',', '', $item['quantity_net']);
                        $data_items = get_items($item['id'], $item['type']);
                        if ($item['type'] == 'nvl') {
                            $recipe = $data_items->recipe;
                            $paper = $data_items->paper;
                            $longs = $data_items->longs;
                            $wide = $data_items->wide;
                            $exchange_unit = $data_items->exchange_unit;    //chuan
                            $exchange_standard_unit = $data_items->exchange_standard_unit; //kho
                            $exchange_unit_payment = $data_items->exchange_unit_payment; //thanh toan
                            $quantity_unit = ($quantity_stock * $exchange_standard_unit) / $exchange_unit;
                            if ($recipe == 1) {
                                $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $exchange_unit;
                            } elseif ($recipe == 2) {
                                $quantity_payment = ($quantity_unit / $exchange_unit_payment) * $paper / 100;
                            } elseif ($recipe == 3) {
                                $quantity_payment = ($quantity_unit / $exchange_unit_payment) * ($longs  * $wide) / 10000;
                            }
                        } elseif ($item['type'] == 'product') {
                            $recipe = 1;
                            $paper = 1;
                            $longs = 1;
                            $wide = 1;
                            $exchange_unit = 0;    //chuan
                            $exchange_standard_unit = $data_items->conversion_quantity_unit; //kho
                            $exchange_unit_payment = 0; //thanh toan
                            $quantity_unit = ($quantity_stock / $exchange_standard_unit);
                            $quantity_payment = 0;
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

                    $warehouses_id = get_table_where('tblwarehouse_items', array('id' => $item['warehouses_id']), '', 'row');
                    $_item['id_export_different'] = $id;
                    $_item['product_id'] = $item['id'];
                    $_item['id_warehouse_items'] = $item['warehouses_id'];
                    $_item['price'] = str_replace(',', '', $item['price']);
                    $_item['warehouses_id'] = $warehouses_id->warehouse_id;
                    $_item['localtion_warehouses_id'] = $warehouses_id->localtion;
                    $_item['lot_code'] = $warehouses_id->lot_code;
                    $_item['date_sx'] = $warehouses_id->date_sx;
                    $_item['date_sd'] = $warehouses_id->date_sd;
                    $_item['date_use'] = $warehouses_id->date_use;
                    $_item['type'] = $item['type'];
                    $_item['note'] = $item['note'];
                    $_item['quantity_unit'] = $quantity_unit;
                    $_item['quantity_stock'] = $quantity_stock;
                    $_item['quantity_payment'] = $quantity_payment;
                    $_item['exchange_unit'] = $exchange_unit;
                    $_item['exchange_stock'] = $exchange_standard_unit;
                    $_item['exchange_payment'] = $exchange_unit_payment;
                    $_item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                    $amount = ($_item['quantity_payment'] * $_item['price']);
                    $_item['amount'] = $amount;
                    $_item['po_id'] = $item['po_id'] ?? 0;
                    
                    $total += $amount;
                    $this->db->insert('tbltblexport_different_items', $_item);
                    $idd = $this->db->insert_id();
                }
            }
        }

        if ($counts > 0) {
            $this->db->update('tblexport_different', array('subtotal' => $total), array('id' => $id));
            return true;
        }

        return false;
    }
    public function update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblexport_different', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function delete($id = '')
    {
        $export_differe = get_table_where('tblexport_different', array('id' => $id), '', 'row');
        $export_differe_items = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
        $this->db->where('id', $id);
        $this->db->delete('tblexport_different');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id_export_different', $id);
            $this->db->delete('tbltblexport_different_items');
            if (!empty($export_differe->warehouseman_id)) {
                $this->increaseadWarehouse($id, $export_differe_items, $export_differe->id_import_outsource);
            }
            log_activity('Inventory Deleted [ID:' . $id . ']');
            return true;
        }
        return false;
    }
    public function get_full_item($id = '', $type = '')
    {
        if ($type == 'items') {
            $this->db->select('tblitems.price as price')->distinct();
            $this->db->from('tblitems');
            $this->db->order_by('tblitems.id', 'desc');
            if (is_numeric($id)) {

                $this->db->where('tblitems.id', $id);
                $item = $this->db->get()->row();
                return $item;
            }
        } else {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.price_sell as price')->distinct();
            $this->db->from($table);
            $this->db->order_by($table . '.id', 'desc');
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                return $item;
            }
        }
    }
    //tăng kho khi xóa
    public function increaseadWarehouse($id = '', $data = '', $main = '')
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

                    // $id_export =  str_replace('PKX-' . $value['id'] . '|', '', $quantity->id_export);
                    // $this->db->where('id', $id_import[0]);
                    // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));
                    $id_export =  str_replace('PKX-' . $value['id'] . '|', '', $quantity->id_export);
                    $this->db->where('id', $id_import[0]);
                    // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));
                    $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net),'product_quantity_unit_export' => ($quantity->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantity->product_quantity_unit_left + $quantity_unit),'product_quantity_payment_export' => ($quantity->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantity->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                }
                $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 19));

                increaseWarehuseQuantity($value['warehouses_id'], $value['localtion_warehouses_id'], $value['product_id'], $value['quantity_net'], $value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['quantity_unit'],$value['quantity_payment']);
            }
            $itemss = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
            foreach ($itemss as $key => $value) {
                $this->db->where('id', $value['id']);
                $this->db->update('tbltblexport_different_items', array('price' => 0, 'amount' => 0));
            }
            if (!empty($main)) {
                $this->db->where('import_outsource_id', $main);
                $this->db->update('tbl_import_outsource_items', array('price_import' => 0));
            }
            $this->db->update('tblexport_different', array('subtotal' => 0), array('id' => $id));
        }
        return true;
    }
    public function decreaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $export = get_table_where('tblexport_different', array('id' => $id), '', 'row');
            $items = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
            foreach ($items as $key => $value) {
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['localtion_warehouses_id'];
                $product_id = $value['product_id'];
                $type_items = $value['type'];
                $quantity = $value['quantity_net'];
                $warehouse_id = $value['warehouses_id'];
                $lot_code = $value['lot_code'];
                $date_sx = $value['date_sx'];
                $date_sd = $value['date_sd'];
                $date_use = $value['date_use'];
                $date_export = $export->date;
                $quantity_unit = $value['quantity_unit'];
                $quantity_payment = $value['quantity_payment'];
                export_different_WarehuseQuantity($warehouse_id, $id, $date_warehouse, $date_export, $product_id, $quantity, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
                decreaseexexport_different_WarehuseQuantity($warehouse_id, $value['id'], $product_id, $quantity, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit,$quantity_payment);
                //trừ kho tổng
                decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use,$quantity_unit,$quantity_payment);
            }
            $itemss = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
            $subtotal = 0;
            foreach ($itemss as $key => $value) {
                $subtotal += $value['amount'];
            }
            if (!empty($export->id_import_outsource)) {
                $items_get = get_table_where('tbl_import_outsource_items', array('import_outsource_id' => $export->id_import_outsource), '', 'row');
                $this->db->where('import_outsource_id', $export->id_import_outsource);
                $this->db->update('tbl_import_outsource_items', array('price_import' => ($subtotal / $items_get->quantity)));
            }
            $this->db->update('tblexport_different', array('subtotal' => $subtotal), array('id' => $id));
        }
        return true;
    }
}
