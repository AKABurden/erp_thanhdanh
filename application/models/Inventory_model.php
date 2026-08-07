<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inventory_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get($id = '')
    {
        $this->db->select('tblinventory.*,wareid.name as namewareide')->distinct();
        $this->db->from('tblinventory');
        $this->db->join('tblwarehouse wareid', 'wareid.id = tblinventory.warehouse_id', 'left');
        $this->db->where('tblinventory.id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_inventory($id);
        return $purchases;
    }
    public function get_items_inventory($id)
    {
        $items = get_table_where('tblinventory_items', array('inventory_id' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_listname_localtion_warehouse($value['localtion']);
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            } elseif ($value['type'] == 'tools') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.mode as mode,' . $table . '.code as code_item,tblunits.unit as unit,stock_unit.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.mode as mode,' . $table . '.code as code_item,tblunits.unit as unit,stock_unit.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.conversion_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            }
        }
        return $items;
    }
    public function add($data)
    {
        if ($data) {
            $items = $data['items'];
            if (isset($data['note'])) nl2br($data['note']);
            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = '';
            $history_status .= '|' . $staff_id . ',' . $date;
            $inventory = array(
                'code' => sprintf('%06d', ch_getMaxID('id', 'tblinventory') + 1),
                'prefix' => get_option('prefix_inventory'),
                'note' => $data['note'],
                'warehouse_id' => $data['warehouse_idd'],
                'date' => to_sql_date($data['date']),
                'staff_id' => get_staff_user_id(),
                'date_create' => date('Y:m:d H:i:s'),
                'status' => 1,
                'history_status' => $history_status
            );
            $warehouse_id = $data['warehouse_idd'];
            $this->db->insert('tblinventory', $inventory);
            $id = $this->db->insert_id();
            // $id = 1;
            $amount_sub = 0;
            if ($id) {
                // unit_id
                foreach ($items as $key => $item) {
                    if (($item['quantity_diff'] !== '')) {
                        $lot_code = (!empty($item['lot_code']) ? $item['lot_code'] : NULL);
                        $date_sx = (!empty($item['date_sx'] && ($item['type'] != 'tools')) ? to_sql_date($item['date_sx']) : NULL);
                        $date_sd = (!empty($item['date_sd'] && ($item['type'] != 'tools')) ? to_sql_date($item['date_sd']) : NULL);
                        $date_use = (!empty($item['date_use'] && ($item['type'] != 'tools')) ? $item['date_use'] : NULL);
                        $quantity = $this->get_localtion_model($item['id'], $inventory['date'], $inventory['warehouse_id'], $item['localtion'], $item['type'], $lot_code, $date_sx, $date_sd, $date_use);
                     
                        $quantity_stock =  abs(str_replace(',', '', $item['quantity_diff']));
                        if ($quantity != str_replace(',', '', $item['quantity'])) {
                            $item['quantity'] = $quantity;
                            // $item['quantity_diff'] = ($item['quantity_net'] - $quantity);
                            $item['quantity_diff'] = (str_replace(',', '', $item['quantity_net']) - $quantity);
                        }
                        if ($item['type'] == 'nvl') {
                            $data_items = get_items($item['id'], $item['type']);
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
                            $data_items = get_items($item['id'], $item['type']);
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
                        $info_items = array();
                        $info_items['recipe'] = $recipe;
                        $info_items['paper'] = $paper;
                        $info_items['longs'] = $longs;
                        $info_items['wide'] = $wide;
                        $info_items_text = json_encode($info_items);
                        $price = $this->get_full_item($item['id'], $item['type']);
                        $_item['inventory_id'] = $id;
                        $_item['product_id'] = $item['id'];
                        $_item['unit_cost'] = $price->price;
                        $_item['warehouse_id'] = $warehouse_id;
                        $_item['localtion'] = $item['localtion'];
                        $_item['type'] = $item['type'];
                        $_item['lot_code'] = (!empty($item['lot_code']) ? $item['lot_code'] : NULL);
                        $_item['date_sx'] = (!empty($item['date_sx'] && ($item['type'] != 'tools')) ? to_sql_date($item['date_sx']) : NULL);
                        $_item['date_sd'] = (!empty($item['date_sd'] && ($item['type'] != 'tools')) ? to_sql_date($item['date_sd']) : NULL);
                        $_item['date_use'] = (!empty($item['date_use'] && ($item['type'] != 'tools')) ? $item['date_use'] : NULL);
                        $_item['quantity'] = str_replace(',', '', $item['quantity']);
                        $_item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                        $_item['quantity_diff'] = str_replace(',', '', $item['quantity_diff']);
                        $_item['handling'] = $item['handling'];
                        $_item['price'] = str_replace(',', '', $item['price']);
                        $_item['amount'] = $_item['price'] * abs($_item['quantity_diff']);
                        $_item['info_items'] = $info_items_text;
                        $_item['quantity_unit'] = $quantity_unit;
                        $_item['quantity_stock'] = $quantity_stock;
                        $_item['quantity_payment'] = $quantity_payment;
                        $_item['exchange_unit'] = $exchange_unit;
                        $_item['exchange_stock'] = $exchange_standard_unit;
                        $_item['exchange_payment'] = $exchange_unit_payment;
                        $amount_sub += $_item['amount'];
                        $this->db->insert('tblinventory_items', $_item);
                    }
                }
                log_activity('Inventory updateted [ID: ' . $id . ']');
                $this->db->update('tblinventory', array('subtotal' => $amount_sub), array('id' => $id));
                return $id;
            }
        }
        return false;
    }
    public function update($data = NULL, $id = '')
    {
        $items = $data['items'];
        if (isset($data['note'])) nl2br($data['note']);
        $inventory = array(
            'note' => $data['note'],
        );
        $warehouse_id = $data['warehouse_idd'];
        $this->db->where('id', $id);
        if ($this->db->update('tblinventory', $inventory)) {
            log_activity('Inventory updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 0;
        }
        $total = 0;
        $total_amount = 0;
        $items = $data['items'];

        if ($count) {
            $affected_id = array();
            $amount_sub = 0;
            foreach ($items as $key => $item) {
                if (!empty($item['quantity_diff'])) {
                    $quantity_stock =  abs(str_replace(',', '', $item['quantity_diff']));
                    if ($item['type'] == 'nvl') {
                        $data_items = get_items($item['id'], $item['type']);
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
                    $it = get_table_where('tblinventory_items', array('id' => $item['idd']), '', 'row');
                    if (!empty($it)) {
                        $affected_id[] = $item['idd'];
                        $_item['quantity'] = str_replace(',', '', $item['quantity']);
                        $_item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                        $_item['quantity_diff'] = str_replace(',', '', $item['quantity_diff']);
                        $_item['handling'] = $item['handling'];
                        $_item['price'] = str_replace(',', '', $item['price']);
                        $_item['amount'] = $_item['price'] * abs($_item['quantity_diff']);
                        $_item['info_items'] = $info_items_text;
                        $_item['quantity_unit'] = $quantity_unit;
                        $_item['quantity_stock'] = $quantity_stock;
                        $_item['quantity_payment'] = $quantity_payment;
                        $_item['exchange_unit'] = $exchange_unit;
                        $_item['exchange_stock'] = $exchange_standard_unit;
                        $_item['exchange_payment'] = $exchange_unit_payment;
                        $amount_sub += $_item['amount'];
                        $this->db->update('tblinventory_items', $_item, array('id' => $it->id));
                        if ($this->db->affected_rows()) {
                            logActivity('Inventory items updateted [ID Purchase: ' . $id . ', ID Product: ' . $it->product_id . ']');
                            $counts++;
                        }
                    } else {
                        $price = $this->get_full_item($item['id'], $item['type']);
                        $__item['inventory_id'] = $id;
                        $__item['product_id'] = $item['id'];
                        $__item['unit_cost'] = $price->price;
                        $__item['warehouse_id'] = $warehouse_id;
                        $__item['localtion'] = $item['localtion'];
                        $__item['type'] = $item['type'];
                        $__item['quantity'] = str_replace(',', '', $item['quantity']);
                        $__item['quantity_net'] = str_replace(',', '', $item['quantity_net']);
                        $__item['quantity_diff'] = str_replace(',', '', $item['quantity_diff']);

                        $__item['handling'] = $item['handling'];
                        $__item['price'] = str_replace(',', '', $item['price']);
                        $__item['amount'] = $__item['price'] * abs($__item['quantity_diff']);

                        $__item['info_items'] = $info_items_text;
                        $__item['quantity_unit'] = $quantity_unit;
                        $__item['quantity_stock'] = $quantity_stock;
                        $__item['quantity_payment'] = $quantity_payment;
                        $__item['exchange_unit'] = $exchange_unit;
                        $__item['exchange_stock'] = $exchange_standard_unit;
                        $__item['exchange_payment'] = $exchange_unit_payment;
                        $amount_sub += $__item['amount'];
                        if ($this->db->insert('tblinventory_items', $__item)) {
                            $affected_id[] = $this->db->insert_id();
                            log_activity('Inventory items insert [ID Purchase: ' . $id . ', ID Product: ' . $__item['product_id'] . ']');
                            $counts++;
                        }
                    }
                }
            }
            $this->db->update('tblinventory', array('subtotal' => $amount_sub), array('id' => $id));
            if (empty($affected_id)) {
                $this->db->where('inventory_id', $id);
                $this->db->delete('tblinventory_items');
            } else {
                $this->db->where_not_in('id', $affected_id);
                $this->db->where('inventory_id', $id);
                $this->db->delete('tblinventory_items');
            }
        }

        if ($counts > 0) {
            return true;
        }

        return false;
    }
    public function delete($id = '')
    {
        $this->db->where('id', $id);
        $this->db->delete('tblinventory');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('inventory_id', $id);
            $this->db->delete('tblinventory_items');
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
    public function get_localtion_model($id, $date, $warehouses, $localtion, $type, $lot_code = NULL, $date_sx = NULL, $date_sd = NULL, $date_use = NULL)
    {
        $data = array();
        if ($date != date('Y-m-d')) {

            $whereJoin = array();
            $whereJoin['where'] = array(
                'date_warehouse <= ' => $date . ' 23:59:59',
                'product_id ' => $id,
                'localtion ' => $localtion,
                'type_items ' => $type,
                'lot_code ' => $lot_code,
                'date_sx ' => $date_sx,
                'date_sd ' => $date_sd,
                'date_use ' => $date_use,
            );
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'quantity';
            $get_quantity_import = sum_from_table_join('tblwarehouse_product', $whereJoin);
            $whereJoin_export = array();
            $whereJoin_export['where'] = array(
                'date_warehouse <= ' => $date . ' 23:59:59',
                'product_id ' => $id,
                'localtion ' => $localtion,
                'type_items ' => $type,
                'lot_code ' => $lot_code,
                'date_sx ' => $date_sx,
                'date_sd ' => $date_sd,
                'date_use ' => $date_use,
            );
            $whereJoin_export['join'] = array();
            $whereJoin_export['field'] = 'quantity';
            $get_quantity_export = sum_from_table_join('tblwarehouse_export', $whereJoin_export);
            if (empty($get_quantity_export)) {
                $get_quantity_export = 0;
            }
            if (empty($get_quantity_import)) {
                $get_quantity_import = 0;
            }
        } else {
            $localtion = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouses, 'id_items' => $id, 'type_items' => $type, 'lot_code' => $lot_code, 'date_sx' => $date_sx, 'date_sd' => $date_sd, 'date_use' => $date_use, 'localtion' => $localtion), '', 'row');
            $get_quantity_export = 0;
            $get_quantity_import = 0;
            if (!empty($localtion)) {
                $get_quantity_import = $localtion->product_quantity;
            }
        }
        $get_quantity_import = $get_quantity_import - $get_quantity_export;
        return $get_quantity_import;
    }
}
