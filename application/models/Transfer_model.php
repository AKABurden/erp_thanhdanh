<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transfer_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function add($data = '')
    {
        $transfer = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tbltransfer_warehouse') + 1),
            'prefix' => get_option('prefix_transfer'),
            'note' => $data['note'],
            'warehouse_id' => 0,
            'warehouse_to' => 0,
            'date' => to_sql_date($data['date']),
            'staff_id' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 1,
        );
        if ($transfer['note'] == NULL) {
            $transfer['note'] = '';
        }
        if ($this->db->insert('tbltransfer_warehouse', $transfer)) {
            $id = $this->db->insert_id();

            log_activity('Transfer Warehouse Insert [ID: ' . $id . ']');
            $count = 0;
            $items = $data['items'];
            $total = 0;
            $total_amount = 0;
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $warehouses_id = get_table_where('tblwarehouse_items', array('id' => $item['localtion_id']), '', 'row');
                    $warehouses_to = get_table_where('tbllocaltion_warehouses', array('id' => $item['localtion_to']), '', 'row')->warehouse;
                    $data_items = get_items($item['id'], $item['type']);
                    $quantity_stock =  str_replace(',', '', $item['quantity_net']);
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
                        $quantity_payment = ($quantity_unit / $exchange_unit_payment) / $exchange_unit;
                    }
                    $info_items = array();
                    $info_items['recipe'] = $recipe;
                    $info_items['paper'] = $paper;
                    $info_items['longs'] = $longs;
                    $info_items['wide'] = $wide;
                    $info_items_text = json_encode($info_items);
                    $itema = array(
                        'id_transfer' => $id,
                        'id_items' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'warehouses_to' => $warehouses_to,
                        'warehouses_id' => $warehouses_id->warehouse_id,
                        'localtion_id' => $warehouses_id->localtion,
                        'lot_code' => $warehouses_id->lot_code,
                        'date_sx' => $warehouses_id->date_sx,
                        'date_sd' => $warehouses_id->date_sd,
                        'date_use' => $warehouses_id->date_use,
                        'localtion_to' => $item['localtion_to'],
                        'price' => str_replace(',', '', $item['price']),
                        'info_items' => $info_items_text,
                        'quantity_unit' => $quantity_unit,
                        'quantity_stock' => $quantity_stock,
                        'quantity_payment' => $quantity_payment,
                        'exchange_unit' => $exchange_unit,
                        'exchange_stock' => $exchange_standard_unit,
                        'exchange_payment' => $exchange_unit_payment,
                    );
                    if ($itema['note'] == NULL) {
                        $itema['note'] = '';
                    }
                    $amount = $itema['price'] * $itema['quantity_net'];
                    $total += $amount;
                    $itema['amount'] = $amount;
                    if ($this->db->insert('tbltransfer_warehouse_detail', $itema)) {
                        $count++;
                        log_activity('Transfer Warehouse insert [ID Import: ' . $id . ', ID Product: ' . $item['id'] . ']');
                    } else {
                        exit("error");
                    }
                }
            }
        }
        if ($count > 0) {
            $this->db->update('tbltransfer_warehouse', array('total' => $total), array('id' => $id));
            return $id;
        }

        return false;
    }
    public function update($data = NULL, $id = '')
    {
        $transfer = array(
            'note' => $data['note'],
            'date' => to_sql_date($data['date']),
        );
        if ($transfer['note'] == NULL) {
            $transfer['note'] = '';
        }
        $this->db->where('id', $id);
        if ($this->db->update('tbltransfer_warehouse', $transfer)) {
            $this->db->delete('tbltransfer_warehouse_detail', array('id_transfer' => $id));
            log_activity('Transfer Warehouse updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 0;
            $count = 0;
            $items = $data['items'];
            $total = 0;
            $total_amount = 0;
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $warehouses_id = get_table_where('tbllocaltion_warehouses', array('id' => $item['localtion_id']), '', 'row')->warehouse;
                    $warehouses_to = get_table_where('tbllocaltion_warehouses', array('id' => $item['localtion_to']), '', 'row')->warehouse;
                    $itema = array(
                        'id_transfer' => $id,
                        'id_items' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'warehouses_to' => $warehouses_to,
                        'warehouses_id' => $warehouses_id,
                        'localtion_id' => $item['localtion_id'],
                        'localtion_to' => $item['localtion_to'],
                        'price' => str_replace(',', '', $item['price']),

                    );
                    if ($itema['note'] == NULL) {
                        $itema['note'] = '';
                    }
                    $amount = $itema['price'] * $itema['quantity_net'];
                    $total += $amount;
                    $itema['amount'] = $amount;
                    if ($this->db->insert('tbltransfer_warehouse_detail', $itema)) {
                        $count++;
                        log_activity('Transfer Warehouse insert [ID Import: ' . $id . ', ID Product: ' . $item['id'] . ']');
                    } else {
                        exit("error");
                    }
                }
            }
        }
        if ($counts > 0) {
            $this->db->update('tbltransfer_warehouse', array('total' => $total), array('id' => $id));
            return true;
        }

        return false;
    }
    public function delete($id = '')
    {
        $transfer = get_table_where('tbltransfer_warehouse', array('id' => $id), '', 'row');
        $items = get_table_where('tbltransfer_warehouse_detail', array('id_transfer' => $id));
        $this->db->where('id', $id);
        $this->db->delete('tbltransfer_warehouse');
        if ($this->db->affected_rows() > 0) {
            if (!empty($transfer->warehouseman_id) || ($transfer->warehouseman_id != 0)) {
                $this->decreaseWarehouse($id, $transfer->warehouse_id, $items);
            }
            $this->db->where('id_transfer', $id);
            $this->db->delete('tbltransfer_warehouse_detail');
            log_activity('Transfer Warehouse Deleted [ID:' . $id . ']');
            return true;
        }

        return false;
    }
    public function update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbltransfer_warehouse', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function get($id = '')
    {
        $this->db->select('tbltransfer_warehouse.*,wareid.name as namewareid,wareto.name as namewareto')->distinct();
        $this->db->from('tbltransfer_warehouse');
        $this->db->join('tblwarehouse wareid', 'wareid.id = tbltransfer_warehouse.warehouse_id', 'left');
        $this->db->join('tblwarehouse wareto', 'wareto.id = tbltransfer_warehouse.warehouse_to', 'left');
        $this->db->where('tbltransfer_warehouse.id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_transfer($id);
        return $purchases;
    }
    public function get_items_transfer($id)
    {
        $items = get_table_where('tbltransfer_warehouse_detail', array('id_transfer' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_stock, "" as height, "" as wide')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['id_items']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_listname_localtion_warehouse($value['localtion_id']);
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
                $localton_to = get_listname_localtion_warehouse($value['localtion_to']);
                $items[$key] = array_merge($items[$key], array('localtion_name_to' => $localton_to));
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit_name,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock, '.$table.'.height as height, '.$table.'.wide as wide')->distinct();
                $this->db->from($table);
                // $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.standard_unit', 'left');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['id_items']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_id']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
                $localton_to = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_to']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_to' => $localton_to));
            } elseif ($value['type'] == 'product') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_payment,stock_unit.unit as unit_name_stock, '.$table.'.height as height, '.$table.'.wide as wide')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.conversion_unit', 'left');
                $this->db->where($table . '.id', $value['id_items']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_id']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
                $localton_to = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_to']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_to' => $localton_to));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_stock, "" as height, "" as wide')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['id_items']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton_id = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_id']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_id' => $localton_id));
                $localton_to = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_to']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name_to' => $localton_to));
            }
            $items[$key] = array_merge($items[$key], array('warehouse_name_to' => get_table_where('tblwarehouse', array('id' => $value['warehouses_to']), '', 'row')->name));
            $items[$key] = array_merge($items[$key], array('warehouse_name_id' => get_table_where('tblwarehouse', array('id' => $value['warehouses_id']), '', 'row')->name));
        }
        return $items;
    }
    public function increaseTranfersWarehouse($id, $id_plan = 0)
    {
        $Tranfers = $this->get($id);
        $count = 0;
        if ($Tranfers) {
            $date_warehouse = date('Y-m-d H:i:s');
            $date_import = $Tranfers->date;
            $dem = 0;
            foreach ($Tranfers->items as $key => $value) {

                $localtion  =  $value['localtion_id'];
                $product_id = $value['id_items'];
                $type_items = $value['type'];
                $quantity = $value['quantity_net'];
                $warehouse_id = $value['warehouses_id'];

                $lot_code = $value['lot_code'];
                $date_sx = $value['date_sx'];
                $date_sd = $value['date_sd'];
                $date_use = $value['date_use'];

                $quantity_unit = $value['quantity_unit'];
                $quantity_payment = $value['quantity_payment'];

                $count = decreaseTransferWarehuseQuantity($warehouse_id, $value['id'], $product_id, $quantity, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                //trừ kho tổng
                decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);

                $warehouse_to = $value['warehouses_to'];
                //tăng kho
                $localtion_to  =  $value['localtion_to'];
                $itemss = get_table_where('tbltransfer_warehouse_detail', array('id' => $value['id']), '', 'row')->id_import;
                $array = explode('|', $itemss);
                foreach ($array as $k => $v) {
                    if (!empty($v)) {
                        $dem++;
                        $waretos = explode('-', $v);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;
                        $quantitys = $waretos[1];
                        $quantity_units = $waretos[2];
                        $quantity_payments = $waretos[3];
                        $count = dincreaseTransferWarehuseQuantity($warehouse_to, $id, $date_warehouse, $date_import, $product_id, $quantitys, $localtion_to, $type_items, $price, $dem, $id_plan, $lot_code, $date_sx, $date_sd, $date_use, $quantity_units, $quantity_payments);
                    }
                }

                increaseTransferWarehuseQuantity($warehouse_id, $id, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);

                //tăng kho tổng
                increaseWarehuseQuantity($warehouse_to, $localtion_to, $product_id, $quantity, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
            }
        }
        if ($count) {
            return true;
        }
        return false;
    }
    //giảm và tăng kho chuyển kho xóa dữ liệu trong kho
    public function decreaseWarehouse($id, $warehouse_id, $data)
    {
        if (is_numeric($id) && !empty($data)) {
            //tăng kho khi xóa chuyển
            foreach ($data as $key => $value) {

                $import = explode('|', trim($value['id_import'], '|'));
                foreach ($import as $k => $v) {
                    $id_import = explode('-', $v);
                    $quantity = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                    $quantity_net = $id_import[1];
                    $quantity_unit = $id_import[2];
                    $quantity_payment = $id_import[3];
                    if (empty($id_import[3]) && !empty($id_import[4])) {
                        $quantity_payment = $id_import[4];
                    }
                    if (empty($id_import[3]) && empty($id_import[4])) {
                        $quantity_payment = 0;
                    }
                    $id_export =  str_replace('CK-' . $value['id'] . '|', '', $quantity->id_export);
                    $this->db->where('id', $id_import[0]);
                    // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));
                    $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'product_quantity_unit_export' => ($quantity->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantity->product_quantity_unit_left + $quantity_unit), 'product_quantity_payment_export' => ($quantity->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantity->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                }
                $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 2));
                increaseWarehuseQuantity($value['warehouses_id'], $value['localtion_id'], $value['id_items'], $value['quantity_net'], $value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $value['quantity_unit'], $value['quantity_payment']);
            }
            //Giảm kho tổng
            $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 2));
            $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 2));

            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $value['product_quantity_unit'], $value['product_quantity_payment']);
            }
        }
        return true;
    }
}
