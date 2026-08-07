<?php

defined('BASEPATH') or exit('No direct script access allowed');

class return_suppliers_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function add($data = '')
    {
        $return = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblreturn_suppliers') + 1),
            'prefix' => get_option('prefix_return_suppliers'),
            'note' => $data['reason'],
            'suppliers_id' => $data['suppliers_idd'],
            'treatment_methods' => $data['treatment_methods'],
            'warehouse_id' => $data['warehouse_id'],
            'date' => to_sql_date($data['date'], true),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 1,
        );
        if ($this->db->insert('tblreturn_suppliers', $return)) {
            $id = $this->db->insert_id();
            log_activity('Return Suppliers Insert [ID: ' . $id . ']');
            $count = 0;
            $items = $data['items'];
            $total = 0;
            $total_amount = 0;
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $dtLocation = explode('__', $item['localtion_warehouses_id']);
                    $location = $dtLocation[1];
                    $lot_code = $dtLocation[2];
                    if (empty($lot_code) || $lot_code === 'NULL' || $lot_code === 'null' || $lot_code == null) {
                        $lot_code = NULL;
                    }
                    $date_sx = $dtLocation[3];
                    if (empty($date_sx) || $date_sx === 'NULL' || $date_sx === 'null' || $date_sx == null) {
                        $date_sx = NULL;
                    }
                    $date_sd = $dtLocation[4];
                    if (empty($date_sd) || $date_sd === 'NULL' || $date_sd === 'null' || $date_sd == null) {
                        $date_sd = NULL;
                    }
                    $date_use = $dtLocation[5];
                    if (empty($date_use) || $date_use === 'NULL' || $date_use === 'null' || $date_use == null) {
                        $date_use = NULL;
                    }
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
                    $itemss = array(
                        'id_return' => $id,
                        'product_id' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'tax_id' => str_replace(',', '', $item['tax_id']),
                        'tax_rate' => $item['tax_rate'],
                        'localtion_warehouses_id' => $location,
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'price' => str_replace(',', '', $item['price']),
                        'lot_code' => $lot_code,
                        'date_sx' => $date_sx,
                        'date_sd' => $date_sd,
                        'date_use' => $date_use,
                    );
                    $itemss['quantity_unit'] = $quantity_unit;
                    $itemss['quantity_stock'] = $quantity_stock;
                    $itemss['quantity_payment'] = $quantity_payment;
                    $itemss['exchange_unit'] = $exchange_unit;
                    $itemss['exchange_stock'] = $exchange_standard_unit;
                    $itemss['exchange_payment'] = $exchange_unit_payment;

                    $amount = ($itemss['price'] * $itemss['quantity_payment']) * ($itemss['tax_rate'] / 100) + ($itemss['price'] * $itemss['quantity_payment']);
                    $itemss['amount'] = $amount;
                    $total_amount += $amount;
                    if ($this->db->insert('tblreturn_suppliers_items', $itemss)) {
                        $count++;
                        log_activity('Return Suppliers items insert [ID Return Suppliers: ' . $id . ', ID Product: ' . $itemss['product_id'] . ']');
                    } else {
                        exit("error");
                    }
                }
            }
        }
        if ($count > 0) {
            $id_pay = 0;
            if ($data['treatment_methods'] == 1) {
                $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblother_payslips_coupon') + 1);
                $_data['date'] = date('Y-m-d');
                $_data['staff_id'] = get_staff_user_id();
                $_data['total'] = $total_amount;
                $_data['date_create'] = date('Y-m-d H:i:s');
                $_data['prefix'] = get_option('prefix_other_payslips_coupon');
                $_data['objects'] = 2;
                $_data['type_vouchers'] = 8;
                $_data['objects_id'] = $data['suppliers_idd'];
                $_data['vouchers_id'] = $id;
                $this->db->insert('tblother_payslips_coupon', $_data);
                $id_pay = $this->db->insert_id();
            }
            $this->db->update('tblreturn_suppliers', array('total' => $total_amount, 'other_payslips' => $id_pay), array('id' => $id));
            return $id;
        }

        return false;
    }
    public function update($data = NULL, $id = '')
    {
        $return_ch = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
        if ($return_ch->treatment_methods == 1) {
            $this->db->where('id', $return_ch->other_payslips);
            $this->db->delete('tblother_payslips_coupon');
        }
        $return = array(
            'note' => $data['reason'],
            'suppliers_id' => $data['suppliers_idd'],
            'warehouse_id' => $data['warehouse_id'],
            'treatment_methods' => $data['treatment_methods'],
            'date' => to_sql_date($data['date'], true),
        );
        $this->db->where('id', $id);
        if ($this->db->update('tblreturn_suppliers', $return)) {
            log_activity('Return Supplier updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 1;
        }
        $total = 0;
        $total_amount = 0;
        $items = $data['items'];

        if ($count) {
            $affected_id = array();
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    if (isset($item))
                        $affected_id[] = $item['id'];
                    $it = get_table_where('tblreturn_suppliers_items', array('id_return' => $id, 'type' => $item['type'], 'product_id' => $item['id']), '', 'row');
                    if (!empty($it)) {
                        $items = array(
                            'quantity' => str_replace(',', '', $item['quantity']),
                            'quantity_net' => str_replace(',', '', $item['quantity_net']),
                            'tax_id' => str_replace(',', '', $item['tax_id']),
                            'tax_rate' => $item['tax_rate'],
                            'localtion_warehouses_id' => $item['localtion_warehouses_id'],
                            'type' => $item['type'],
                            'note' => $item['note'],
                            'price' => str_replace(',', '', $item['price']),
                        );
                        $total += $items['price'] * $items['quantity_net'] * ($item['tax_rate'] / 100) + $items['price'] * $items['quantity_net'];
                        $items['amount'] = $items['price'] * $items['quantity_net'] * ($item['tax_rate'] / 100) + $items['price'] * $items['quantity_net'];
                        $this->db->update('tblreturn_suppliers_items', $items, array('id' => $it->id));
                        if ($this->db->affected_rows()) {
                            logActivity('Return Suppliers items updateted [ID Return Suppliers: ' . $id . ', ID Product: ' . $it->product_id . ']');
                            $counts++;
                        }
                    } else {
                        $items = array(
                            'id_return' => $id,
                            'product_id' => $item['id'],
                            'quantity' => str_replace(',', '', $item['quantity']),
                            'quantity_net' => str_replace(',', '', $item['quantity_net']),
                            'tax_id' => str_replace(',', '', $item['tax_id']),
                            'tax_rate' => $item['tax_rate'],
                            'localtion_warehouses_id' => $item['localtion_warehouses_id'],
                            'type' => $item['type'],
                            'note' => $item['note'],
                            'price' => str_replace(',', '', $item['price'])
                        );
                        $total += $items['price'] * $items['quantity_net'] * ($item['tax_rate'] / 100) + $items['price'] * $items['quantity_net'];
                        $items['amount'] = $items['price'] * $items['quantity_net'] * ($item['tax_rate'] / 100) + $items['price'] * $items['quantity_net'];
                        if ($this->db->insert('tblreturn_suppliers_items', $items)) {

                            log_activity('Return Suppliers items insert [ID Return Suppliers: ' . $id . ', ID Product: ' . $item['id'] . ']');
                            $counts++;
                        }
                    }
                    if (empty($affected_id)) {
                        $this->db->where('id_return', $id);
                        $this->db->delete('tblreturn_suppliers_items');
                    } else {
                        $this->db->where('id_return', $id);
                        $this->db->where_not_in('product_id', $affected_id);
                        $this->db->delete('tblreturn_suppliers_items');
                    }
                }
            }
        }

        if ($counts > 0) {
            $id_pay = 0;
            if ($data['treatment_methods'] == 1) {
                $_data['code'] = sprintf('%06d', ch_getMaxID('id', 'tblother_payslips_coupon') + 1);
                $_data['date'] = date('Y-m-d');
                $_data['staff_id'] = get_staff_user_id();
                $_data['total'] = $total;
                $_data['date_create'] = date('Y-m-d H:i:s');
                $_data['prefix'] = get_option('prefix_other_payslips_coupon');
                $_data['objects'] = 2;
                $_data['type_vouchers'] = 8;
                $_data['objects_id'] = $data['suppliers_idd'];
                $_data['vouchers_id'] = $id;
                $this->db->insert('tblother_payslips_coupon', $_data);
                $id_pay = $this->db->insert_id();
            }
            $this->db->update('tblreturn_suppliers', array('total' => $total, 'other_payslips' => $id_pay), array('id' => $id));
            return true;
        }

        return false;
    }
    public function update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblreturn_suppliers', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function get($id = '')
    {
        $this->db->select('tblreturn_suppliers.*')->distinct();
        $this->db->from('tblreturn_suppliers');
        $this->db->where('id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_return_suppliers($id, $purchases->suppliers_id);
        return $purchases;
    }
    public function get_items_return_suppliers($id, $suppliers_id = '')
    {
        $items = get_table_where('tblreturn_suppliers_items', array('id_return' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row')->name_parent;
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            } elseif($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_listname_localtion_warehouse($value['localtion_warehouses_id']);
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit,tblunits.unit as unit_name_payment,stock_unit.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $localton = get_listname_localtion_warehouse($value['localtion_warehouses_id']);
                $items[$key] = array_merge($items[$key], array('localtion_name' => $localton));
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $value['product_id'], 'type_items' => $value['type'], 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $quantity_warehoues = $quantity_warehoue->product_quantity;
                } else {
                    $quantity_warehoues = 0;
                }
                $items[$key] = array_merge($items[$key], array('quantity_warehoue' => $quantity_warehoues));
            }
        }
        return $items;
    }
    public function get_full_item($id = '', $type = '', $suppliers_id = '')
    {
        if ($type == 'items') {
            $this->db->select('tblitems.*,tblunits.unit as unit_name,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
            $this->db->from('tblitems');
            $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
            $this->db->order_by('tblitems.id', 'desc');
            if (is_numeric($id)) {

                $this->db->where('tblitems.id', $id);
                $item = $this->db->get()->row();
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $id, 'type_items' => $type, 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $item->quantity_warehoue = $quantity_warehoue->product_quantity;
                } else {
                    $item->quantity_warehoue = 0;
                }
                $item->color = format_item_color($id, $type);
                $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
                return $item;
            }
        } elseif ($type == 'nvl') {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide')->distinct();
            $this->db->from($table);
            $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
            $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
            $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
            $this->db->order_by($table . '.id', 'desc');
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $id, 'type_items' => $type, 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $item->quantity_warehoue = $quantity_warehoue->product_quantity;
                } else {
                    $item->quantity_warehoue = 0;
                }
                $item->color = format_item_color($id, $type);
                $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
                return $item;
            }
        } elseif ($type == 'product') {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblunits.unit as unit_name_payment,stock_unit.unit as unit_name_stock')->distinct();
            $this->db->from($table);
            $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
            $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.conversion_unit', 'left');
            $this->db->order_by($table . '.id', 'desc');
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $id, 'type_items' => $type, 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $item->quantity_warehoue = $quantity_warehoue->product_quantity;
                } else {
                    $item->quantity_warehoue = 0;
                }
                $item->color = format_item_color($id, $type);
                $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
                return $item;
            }
        } else {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
            $this->db->from($table);
            $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
            $this->db->order_by($table . '.id', 'desc');
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                $quantity_warehoue = get_table_where('tblwarehouse_suppliers', array('id_items' => $id, 'type_items' => $type, 'suppliers_id' => $suppliers_id), '', 'row');
                if (!empty($quantity_warehoue)) {
                    $item->quantity_warehoue = $quantity_warehoue->product_quantity;
                } else {
                    $item->quantity_warehoue = 0;
                }
                $item->color = format_item_color($id, $type);
                $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
                return $item;
            }
        }
    }
    public function delete($id = '')
    {
        $return_suppliers = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
        $items = get_table_where('tblreturn_suppliers_items', array('id_return' => $id));
        $this->db->where('id', $id);
        $this->db->delete('tblreturn_suppliers');
        if ($this->db->affected_rows() > 0) {
            if (!empty($return_suppliers->warehouseman_id) || ($return_suppliers->warehouseman_id != 0)) {
                $this->increaseadWarehouse($id, $return_suppliers->warehouse_id, $items, $return_suppliers->suppliers_id);
            }
            $this->db->where('id_return', $id);
            $this->db->delete('tblreturn_suppliers_items');
            log_activity('Return Suppliers Deleted [ID:' . $id . ']');
            return true;
        }

        return false;
    }
    public function decreaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $return_suppliers = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
            $items = get_table_where('tblreturn_suppliers_items', array('id_return' => $id));
            foreach ($items as $key => $value) {
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['localtion_warehouses_id'];
                $product_id = $value['product_id'];
                $type_items = $value['type'];
                $quantity = $value['quantity_net'];

                $quantity_unit = $value['quantity_unit'];
                $quantity_payment = $value['quantity_payment'];

                export_RetrunWarehuseQuantity($return_suppliers->warehouse_id, $id, $date_warehouse, $return_suppliers->date, $product_id, $quantity, $localtion, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);
                $count = decreaseRetrunWarehuseQuantity($return_suppliers->warehouse_id, $value['id'], $product_id, $quantity, $localtion, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);
                //trừ kho tổng
                decreaseWarehuseQuantity($return_suppliers->warehouse_id, $localtion, $product_id, $quantity, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);
                decreaseSuppliersQuantity($return_suppliers->suppliers_id, 1, $product_id, $quantity, $type_items, $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$quantity_unit,$quantity_payment);
            }
        }
        return true;
    }
    public function increaseadWarehouse($id, $warehouse_id, $data, $suppliers_id = '')
    {
        if (is_numeric($id) && !empty($data)) {
            //tăng kho khi xóa
            foreach ($data as $key => $value) {

                $import = explode('|', trim($value['id_import'], '|'));
                foreach ($import as $k => $v) {
                    // $id_import = explode('-', $v);
                    // $quantity = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                    // $quantity_net = $id_import[1];

                    // $id_export =  str_replace('TH-' . $value['id'] . '|', '', $quantity->id_export);
                    // $this->db->where('id', $id_import[0]);
                    // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));


                    $id_import = explode('-', $v);
                    $quantity = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                    $quantity_net = $id_import[1];
                    $quantity_unit = $id_import[2];
                    $quantity_payment = $id_import[3];
                    $id_export =  str_replace('TH-' . $value['id'] . '|', '', $quantity->id_export);
                    $this->db->where('id', $id_import[0]);
                    $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net),'product_quantity_unit_export' => ($quantity->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantity->product_quantity_unit_left + $quantity_unit),'product_quantity_payment_export' => ($quantity->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantity->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                }
                $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 15));
                increaseWarehuseQuantity($warehouse_id, $value['localtion_warehouses_id'], $value['product_id'], $value['quantity_net'], $value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['quantity_unit'],$value['quantity_payment']);
                increaseSuppliertsQuantity($suppliers_id, 1, $value['product_id'], $value['quantity_net'], $value['type'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['quantity_unit'],$value['quantity_payment']);
            }
        }
        return true;
    }
}
