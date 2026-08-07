<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Adjusted_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get($id = '')
    {
        $this->db->select('tbladjusted.*,wareid.name as namewareide')->distinct();
        $this->db->from('tbladjusted');
        $this->db->join('tblwarehouse wareid', 'wareid.id = tbladjusted.warehouse_id', 'left');
        $this->db->where('tbladjusted.id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_inventory($id);
        return $purchases;
    }
    public function get_items_inventory($id)
    {
        $items = get_table_where('tbladjusted_items', array('id_adjusted' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_listname_localtion_warehouse($value['localtion']);
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            } elseif ($value['type'] == 'tools') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            } elseif($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.mode as mode,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
            }else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.mode as mode,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.conversion_unit', 'left');
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
            $inventory = array(
                'code' => sprintf('%06d', ch_getMaxID('id', 'tbladjusted') + 1),
                'note' => $data['note'],
                'warehouse_id' => $data['warehouse_idd'],
                'date' => to_sql_date($data['date'], true),
                'staff_id' => get_staff_user_id(),
                'date_create' => date('Y:m:d H:i:s'),
                'status' => 0,
                'type' => $data['type']
            );
            if ($data['type'] == 1) {
                $inventory['prefix'] = get_option('prefix_detail_up');
            } else {
                $inventory['prefix'] = get_option('prefix_detail_down');
            }
            $warehouse_id = $data['warehouse_idd'];
            $this->db->insert('tbladjusted', $inventory);
            $id = $this->db->insert_id();
            if ($id) {
                // unit_id
                $amount_sub = 0;
                foreach ($items as $key => $item) {
                    if (!empty($item['quantity_net']) && ($item['check'] == 0)) {
                        $price = $this->get_full_item($item['id'], $item['type']);
                        $_item['id_adjusted'] = $id;
                        $_item['product_id'] = $item['id'];
                        $_item['unit_cost'] = $price->price;
                        $_item['warehouse_id'] = $warehouse_id;
                        $_item['localtion'] = $item['localtion'];
                        $_item['type'] = $item['type'];
                        $_item['quantity'] = $item['quantity'];
                        $_item['quantity_net'] = $item['quantity_net'];
                        $_item['price'] = str_replace(',', '', $item['price']);
                        if ($data['type'] == 1) {
                            $_item['amount'] = $_item['price'] * abs($item['quantity_net'] - $item['quantity']);
                        } else {
                            $_item['amount'] = $_item['price'] * abs($item['quantity'] - $item['quantity_net']);
                        }
                        $amount_sub += $_item['amount'];
                        $this->db->insert('tbladjusted_items', $_item);
                        $idd = $this->db->insert_id();
                        $this->db->update('tbladjusted', array('subtotal' => $amount_sub), array('id' => $idd));
                        if ($data['type'] == 1) {
                            $date_warehouse = date('Y-m-d H:i:s');
                            $localtion =  $_item['localtion'];
                            $product_id = $_item['product_id'];
                            $type_items = $_item['type'];
                            $quantity = $_item['quantity_net'];
                            $pirce = $_item['price'];
                            $count = increaseadjuProductQuantity($warehouse_id, $id, $date_warehouse, $inventory['date'], $product_id, $quantity, $localtion, $type_items, $pirce);
                            //tăng kho tổng
                            increaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items);
                        }
                        if ($data['type'] == 2) {
                            $date_warehouse = date('Y-m-d H:i:s');
                            $localtion  =  $_item['localtion'];
                            $product_id = $_item['product_id'];
                            $type_items = $_item['type'];
                            $quantity = abs($_item['quantity_net']);
                            export_AdjuWarehuseQuantity($warehouse_id, $id, $date_warehouse, $inventory['date'], $product_id, $quantity, $localtion, $type_items);
                            $count = decreaseAdjuWarehuseQuantity($warehouse_id, $idd, $product_id, $quantity, $localtion, $type_items);
                            //trừ kho tổng
                            decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items);
                        }
                    }
                }
                log_activity('Adjusted add [ID: ' . $id . ']');
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
        if ($this->db->update('tbladjusted', $inventory)) {
            log_activity('Inventory updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 0;
        }
        $total = 0;
        $total_amount = 0;
        $items = $data['items'];

        if ($count) {
            $affected_id = array();
            foreach ($items as $key => $item) {
                if (!empty($item['quantity_net'])) {
                    $it = get_table_where('tbladjusted_items', array('id' => $item['idd']), '', 'row');
                    if (!empty($it)) {
                        $affected_id[] = $item['idd'];
                        $_item['quantity'] = $item['quantity'];
                        $_item['quantity_net'] = $item['quantity_net'];
                        $_item['quantity_diff'] = $item['quantity_diff'];
                        $_item['handling'] = $item['handling'];
                        $this->db->update('tbladjusted_items', $_item, array('id' => $it->id));
                        if ($this->db->affected_rows()) {
                            logActivity('Inventory items updateted [ID Purchase: ' . $id . ', ID Product: ' . $it->product_id . ']');
                            $counts++;
                        }
                    } else {
                        $price = $this->get_full_item($item['id'], $item['type']);
                        $__item['id_adjusted'] = $id;
                        $__item['product_id'] = $item['id'];
                        $__item['unit_cost'] = $price->price;
                        $__item['warehouse_id'] = $warehouse_id;
                        $__item['localtion'] = $item['localtion'];
                        $__item['type'] = $item['type'];
                        $__item['quantity'] = $item['quantity'];
                        $__item['quantity_net'] = $item['quantity_net'];
                        $__item['quantity_diff'] = $item['quantity_diff'];
                        $__item['handling'] = $item['handling'];
                        if ($this->db->insert('tbladjusted_items', $__item)) {
                            $affected_id[] = $this->db->insert_id();
                            log_activity('Inventory items insert [ID Purchase: ' . $id . ', ID Product: ' . $__item['product_id'] . ']');
                            $counts++;
                        }
                    }
                }
            }
            if (empty($affected_id)) {
                $this->db->where('id_adjusted', $id);
                $this->db->delete('tbladjusted_items');
            } else {
                $this->db->where_not_in('id', $affected_id);
                $this->db->delete('tbladjusted_items');
            }
        }

        if ($counts > 0) {
            return true;
        }

        return false;
    }
    public function delete($id = '')
    {
        $adjusted = get_table_where('tbladjusted', array('id' => $id), '', 'row');
        $adjusted_items = get_table_where('tbladjusted_items', array('id_adjusted' => $id));
        $this->db->where('id', $id);
        $this->db->delete('tbladjusted');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id_adjusted', $id);
            $this->db->delete('tbladjusted_items');
            $this->decreaseWarehouse($id, $adjusted->warehouse_id, $adjusted_items, $adjusted->type);
            log_activity('Inventory Deleted [ID:' . $id . ']');
            return true;
        }

        return false;
    }
    //giảm và tăng kho khi xóa dữ liệu trong kho
    public function decreaseWarehouse($id, $warehouse_id, $data, $type)
    {
        if (is_numeric($id) && !empty($data)) {
            if ($type == 2) {
                //tăng kho khi xóa
                foreach ($data as $key => $value) {

                    $import = explode('|', trim($value['id_import'], '|'));
                    foreach ($import as $k => $v) {
                        $id_import = explode('-', $v);
                        $quantity = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                        $quantity_net = $id_import[1];
                        $quantity_unit = $id_import[2];
                        $quantity_payment = $id_import[3];
                        $id_export =  str_replace('DC-' . $value['id'] . '|', '', $quantity->id_export);
                        $this->db->where('id', $id_import[0]);
                        // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));
                        $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net),'product_quantity_unit_export' => ($quantity->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantity->product_quantity_unit_left + $quantity_unit),'product_quantity_payment_export' => ($quantity->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantity->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                    }
                    $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 3));
                    increaseWarehuseQuantity($warehouse_id, $value['localtion'], $value['product_id'], abs($value['quantity_net']), $value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['quantity_unit'],$value['quantity_payment']);
                }
            } elseif ($type == 1) {
                //Giảm kho tổng
                $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 3));
                $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 3));

                foreach ($warehouse_product as $key => $value) {
                    decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['product_quantity_unit'],$value['product_quantity_payment']);
                }
            }
        }
        return true;
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
}
