<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_order_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_items_purchase($type = '', $id = '', $id_array = array())
    {
        // $this->db->select('tblpurchases_items.*, tblitems.price as price, tblitems.avatar as avatar, tblitems.name as name_item, tblitems.code as code_item, tblunits.unit as unit_name')->distinct();
        // $this->db->from('tblpurchases_items');
        // $this->db->join('tblitems','tblitems.id=tblpurchases_items.product_id AND tblitems.type_items= tblpurchases_items.type','left');
        // $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        // if(!empty($type))
        // {
        // $this->db->where('tblpurchases_items.type',$type);
        // }
        // $this->db->where('purchases_id',$id);  
        // $item = $this->db->get()->result_array();


        if (!empty($id_array)) {
            $this->db->select('tblpurchases_items.*')->distinct();
            $this->db->from('tblpurchases_items');
            $this->db->where_in('tblpurchases_items.id', $id_array);
            $this->db->where('purchases_id', $id);
            $items = $this->db->get()->result_array();
        } else {
            $items = get_table_where('tblpurchases_items', array('purchases_id' => $id));
        }
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.price as price,tblitems.code as code_item,tblunits.unit as unit_name,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.price_sell as price,' . $table . '.code as code_item,tblunits.unit as unit_name,exchange_unit,exchange_standard_unit,exchange_unit_payment,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide')->distinct();
                
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.price_sell as price,' . $table . '.code as code_item,tblunits.unit as unit_name,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            }
        }
        foreach ($items as $key => $value) {
            $quantity = $this->sum_quantity($value['type'], $id, $value['product_id']);
            if (empty($quantity)) {
                $quantity = 0;
            }
            $quantity_net = $value['quantity_net'] - $quantity;
            $items[$key]['quantity_net'] = $quantity_net;
            if ($quantity_net == 0) {
                unset($items[$key]);
            } else {
                $html = '<option value=""></option>';
                foreach (get_options_search_cbo('items', $value['product_id'], $value['type']) as $k => $v) {
                    $html .= '<option selected value="' . $v['id'] . '">' . $v['name'] . '</option>';
                }
                $items[$key]['html'] = $html;
            }
        }

        return $items;
    }
    public function sum_quantity($type = '', $id = '', $id_product)
    {
        $this->db->select('SUM(tblpurchase_order_items.quantity_suppliers) as quantity');
        $this->db->from('tblpurchase_order_items');
        $this->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
        $this->db->where('tblpurchase_order.id_purchases', $id);
        $this->db->where('product_id', $id_product);
        $this->db->where('type', $type);
        return $this->db->get()->row()->quantity;
    }
    public function get_items_purchases_item($type = '', $id = '', $id_product)
    {
        $this->db->select('tblpurchases_items.*, tblitems.name as name_item, tblitems.price as price, tblitems.avatar as avatar, tblitems.code as code_item, tblunits.unit as unit_name')->distinct();
        $this->db->from('tblpurchases_items');
        $this->db->join('tblitems', 'tblitems.id=tblpurchases_items.product_id AND tblitems.type_items= tblpurchases_items.type', 'left');
        $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
        $this->db->where('tblpurchases_items.type', $type);
        $this->db->where('purchases_id', $id);
        $this->db->where('product_id', $id_product);
        $item = $this->db->get()->row();

        $quantity = $this->sum_quantity($type, $id, $id_product);
        if (empty($quantity)) {
            $quantity = 0;
        }
        $item->quantity_net = $item->quantity_net - $quantity;
        return $item;
    }
    public function get_items_quote($type = '', $id = '')
    {
        $this->db->select('tblsupplier_quote_items.*,tblitems.name as name_item,tblitems.code as code_item')->distinct();
        $this->db->from('tblsupplier_quote_items');
        $this->db->join('tblitems', 'tblitems.id=tblsupplier_quote_items.product_id AND tblitems.type_items= tblsupplier_quote_items.type', 'left');
        $this->db->where('tblsupplier_quote_items.type', $type);
        $this->db->where('id_supplier_quotes', $id);
        $item = $this->db->get()->result_array();
        return $item;
    }

    public function test_quantity($type = '', $id = '', $id_product = '')
    {
        $this->db->select('tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all')->distinct();
        $this->db->from('tblpurchases_items');
        $this->db->where('tblpurchases_items.type', $type);
        $this->db->where('purchases_id', $id);
        $this->db->where('product_id', $id_product);
        $item = $this->db->get()->row();
        if (empty($item)) {
            $quantity_net = 0;
        } else {
            $quantity_net = $item->quantity_net - $item->quantity_create - $item->quantity_create_all;
        }
        return $quantity_net;
    }
    public function test_quantity_all($type = '', $id = '', $id_product = '')
    {
        $this->db->select('SUM(tblpurchases_items.quantity_net) as quantity_net,Sum(tblpurchases_items.quantity_create) as quantity_create,Sum(tblpurchases_items.quantity_create_all) as quantity_create_all')->distinct();
        $this->db->from('tblpurchases_items');
        $this->db->group_by('tblpurchases_items.product_id');
        $this->db->where('tblpurchases_items.type', $type);
        $this->db->where_in('purchases_id', explode(',', trim($id, ',')));
        $this->db->where('product_id', $id_product);
        $item = $this->db->get()->row();
        if (empty($item)) {
            $quantity_net = 0;
        } else {
            $quantity_net = $item->quantity_net - $item->quantity_create - $item->quantity_create_all;
        }
        return $quantity_net;
    }
    public function get_create_purchase_order_import($id = '', $import_id = '')
    {
        $this->db->select('tblpurchase_order.*')->distinct();
        $this->db->from('tblpurchase_order');
        $this->db->where('id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_purchase_order_import($id, $import_id);
        return $purchases;
    }
    public function get_items_purchase_order_import_v2($id_order, $import_id = '')
    {
        $result['result'] = array();
        $table = get_table_where('tbltype_items');
        $temp = '';
        $dem_temp = 0;
        foreach ($table as $key => $value) {

            if (!empty($id_order)) {
                $this->db->select($value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,tblpurchase_order_items.quantity_suppliers');
                $this->db->from('tblpurchase_order_items');
            }
            $this->db->group_by($value['table'] . '.id');
            $this->db->order_by($value['table'] . '.id', 'ASC');
            if (!empty($id_order)) {
                $this->db->join($value['table'], $value['table'] . '.id = tblpurchase_order_items.product_id');
                $this->db->where('tblpurchase_order_items.id_purchase_order', $id_order);
                $this->db->where('tblpurchase_order_items.type', $value['type']);
            }
            $data = $this->db->get()->result_array();
            $dem_temp++;

            foreach ($data as $key_data => $value_data) {
                $value_data = array_merge($value_data, array('type_items' => $value['type']));
                $quantity = sum_quantity_import($value['type'], $id_order, $value_data['id']);
                if (empty($quantity)) {
                    $quantity = 0;
                }
                $ktr_quantity = 0;
                if ($import_id) {
                    $import_items = get_table_where_select('quantity_net', 'tblimport_items', array('id_import' => $import_id, 'product_id' => $value_data['id'], 'type' => $value['type']), '', 'row');
                    if (!empty($import_items)) {
                        $ktr_quantity =  $import_items->quantity_net;
                    }
                }
                $value_data['quantity_suppliers'] = $value_data['quantity_suppliers'] - $quantity + $ktr_quantity;
                if ($value_data['quantity_suppliers'] <= 0) {
                } else {
                    $whereJoin = array();
                    $whereJoin['where'] = array(
                        'id_items ' => $value_data['id'],
                        'type_items ' => $value_data['type_items'],
                    );
                    $whereJoin['join'] = array();
                    $whereJoin['field'] = 'product_quantity';
                    $quantity_warehoue = sum_from_table_join('tblwarehouse_items', $whereJoin);
                    $value_data['quantity_warehoue'] = $quantity_warehoue;
                    $result['result'][$dem_temp] = $value_data;
                    $dem_temp++;
                }
            }
        }
        return $result['result'];
    }
    public function get_items_purchase_order_import($id_order, $import_id = '')
    {
        $result['result'] = array();
        $table = get_table_where('tbltype_items',array('type'=>'nvl'));
        $temp = '';
        $dem_temp = 0;
        foreach ($table as $key => $value) {
            $result['result'][$dem_temp] = array('id' => 'h', 'name' => $value['name'], 'type_items' => $value['type']);
            $dem_temp++;
            if (!empty($id_order)) {
                if ($value['type'] == 'nvl' || $value['type'] == 'product') {
                    $this->db->select($value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.mode,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,SUM(tblpurchase_order_items.quantity_suppliers) as quantity_suppliers,tblpurchase_order_items.plan_id,tbl_productions_plan.reference_no as code_plan,tblpurchase_order_items.id as idd');
                } else {
                    $this->db->select($value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,SUM(tblpurchase_order_items.quantity_suppliers) as quantity_suppliers,tblpurchase_order_items.plan_id,tbl_productions_plan.reference_no as code_plan,tblpurchase_order_items.id as idd');
                }
                $this->db->from('tblpurchase_order_items');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tblpurchase_order_items.plan_id', 'left');
            }
            // $this->db->group_by($value['table'] . '.id,tblpurchase_order_items.plan_id');
            $this->db->order_by($value['table'] . '.id', 'ASC');
            if (!empty($id_order)) {
                $this->db->join($value['table'], $value['table'] . '.id = tblpurchase_order_items.product_id');
                $this->db->where('tblpurchase_order_items.id_purchase_order', $id_order);
                $this->db->where('tblpurchase_order_items.type', $value['type']);
            }
            $this->db->group_by('tblpurchase_order_items.id');

            $data = $this->db->get()->result_array();
            $dem_temp++;

            foreach ($data as $key_data => $value_data) {
                $value_data = array_merge($value_data, array('type_items' => $value['type']));
                $quantity = sum_quantity_import($value['type'], $id_order, $value_data['id'], $value_data['plan_id'], $value_data['idd']);
                if (empty($quantity)) {
                    $quantity = 0;
                }
                $ktr_quantity = 0;
                if ($import_id) {
                    $import_items = get_table_where_select('quantity_net', 'tblimport_items', array('id_import' => $import_id, 'product_id' => $value_data['id'], 'type' => $value['type']), '', 'row');
                    if (!empty($import_items)) {
                        $ktr_quantity =  $import_items->quantity_net;
                    }
                }
                $value_data['quantity_suppliers'] = $value_data['quantity_suppliers'] - $quantity + $ktr_quantity;
                if ($value_data['quantity_suppliers'] <= 0) {
                } else {
                    $whereJoin = array();
                    $whereJoin['where'] = array(
                        'id_items ' => $value_data['id'],
                        'type_items ' => $value_data['type_items'],
                    );
                    $whereJoin['join'] = array();
                    $whereJoin['field'] = 'product_quantity';
                    $quantity_warehoue = sum_from_table_join('tblwarehouse_items', $whereJoin);
                    $value_data['quantity_warehoue'] = $quantity_warehoue;
                    $result['result'][$dem_temp] = $value_data;
                    $dem_temp++;
                }
            }
        }
        return $result['result'];
    }
    public function get_purchase_order($id = '')
    {
        $this->db->select('tblpurchase_order.*')->distinct();
        $this->db->from('tblpurchase_order');
        $this->db->where('id', $id);
        $purchase_order = $this->db->get()->row();
        return $purchase_order;
    }

    public function get_order_import($id = '')
    {
        $this->db->select('tblpurchase_order.*')->distinct();
        $this->db->from('tblpurchase_order');
        $this->db->where('id', $id);
        $purchase_order = $this->db->get()->row();
        $purchase_order->items = $this->get_items_purchase_order_import_ch($id);
        return $purchase_order;
    }

    public function get($id = '')
    {
        $this->db->select('tblpurchase_order.*')->distinct();
        $this->db->from('tblpurchase_order');
        $this->db->where('id', $id);
        $purchase_order = $this->db->get()->row();
        $purchase_order->items = $this->get_items_purchase_order($id);
        return $purchase_order;
    }
    public function get_items_purchase_order_import_ch($id)
    {
        $items = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.price as price,tblitems.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key]['quantity_import'] = sum_quantity_import($value['type'], $id, $value['product_id']);
                if (empty($items[$key]['quantity_import'])) {
                    $items[$key]['quantity_import'] = 0;
                }
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.price_sell as price,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key]['quantity_import'] = sum_quantity_import($value['type'], $id, $value['product_id']);
                if (empty($items[$key]['quantity_import'])) {
                    $items[$key]['quantity_import'] = 0;
                }
            }
        }
        return $items;
    }
    public function get_items_purchase_order($id)
    {
        // $this->db->select('tblpurchase_order_items.*,tblitems.name as name_item, tblitems.avatar as avatar, tblitems.code as code_item,tblunits.unit as unit')->distinct();
        // $this->db->from('tblpurchase_order_items');
        // $this->db->join('tblitems','tblitems.id=tblpurchase_order_items.product_id AND tblitems.type_items= tblpurchase_order_items.type','left');
        // $this->db->join('tblunits','tblunits.unitid=tblitems.unit','left');
        // $this->db->where('id_purchase_order',$id);
        // return $this->db->get()->result_array();


        $items = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.price as price,tblitems.code as code_item,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.price_sell as price,' . $table . '.code as code_item,tblunits.unit as unit,exchange_unit,exchange_standard_unit,exchange_unit_payment,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.price_sell as price,' . $table . '.code as code_item,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            }
        }
        return $items;
    }

    public function add($data = array())
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $plan_id_text = '';
        $plan_id_check = 0;
        $amount_to_vnd = str_replace(',', '', $data['amount_to_vnd']);
        $quotes = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1),
            'prefix' => get_option('prefix_purchase_order'),
            'staff_create' => get_staff_user_id(),
            'date' => to_sql_date($data['date']),
            'delivery_date' => to_sql_date($data['delivery_date']),
            'date_create' => date('Y:m:d H:i:s'),
            'suppliers_id' => $data['suppliers_id'],
            'currency' => $data['currency_ch'],
            'amount_to_vnd' => $amount_to_vnd,
            'type_items' => $data['type_items'],
            'type_order' => $data['type_order'],
            'tax_all' => $data['tax_all'],
            'status' => 3,
            'note' => $data['note'],
            'history_status' => get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s'),
            'delivery_cost' => str_replace(',', '', $data['delivery_cost']),
            'reduce_cost' => str_replace(',', '', $data['reduce_cost']),
        );
        $delivery_cost = str_replace(',', '', $data['delivery_cost']);
        $reduce_cost = str_replace(',', '', $data['reduce_cost']);

        if (!empty($data['id_purchases'])) {
            $id_purchase = get_table_where('tblpurchases', array('id' => $data['id_purchases']), '', 'row');
            $quotes['type_plan'] = $id_purchase->type_plan;
            $quotes['id_purchases'] = $data['id_purchases'];
        }
        if (!empty($data['id_quotes'])) {
            $id_quotes = get_table_where('tblsupplier_quotes', array('id' => $data['id_quotes']), '', 'row');
            $quotes['type_plan'] = $id_quotes->type_plan;
            $quotes['id_purchase_proce'] = $id_quotes->id_purchase_proce;
            $quotes['id_quotes'] = $data['id_quotes'];
        }
        if ($this->db->insert('tblpurchase_order', $quotes)) {
            $id = $this->db->insert_id();
            if (!empty($data['id_purchases'])) {
                $this->db->update('tblpurchases', array('process' => ('3|' . $id)), array('id' => $data['id_purchases']));
            }
            if (!empty($data['id_quotes'])) {
                $this->db->update('tblsupplier_quotes', array('process' => ('3|' . $id)), array('id' => $data['id_quotes']));
            }
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                unset($custom_fields);
                $custom_fields['purchase_order'] = $_custom_fields['purchase_order'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Purchase order Insert [ID: ' . $id . ']');
            $count = 1;
        }
        $items = $data['items'];
        if ($id) {
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;

            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {

                    $info_items = array();
                    $info_items['recipe'] = $item['recipe'];
                    $info_items['paper'] = $item['paper'];
                    $info_items['longs'] = $item['longs'];
                    $info_items['wide'] = $item['wide'];
                    $info_items_text = json_encode($info_items);


                    $item['quantity'] = str_replace(',', '', $item['quantity']);
                    $item['quantity_suppliers'] = str_replace(',', '', $item['quantity_suppliers']);
                    $item['price_expected'] = str_replace(',', '', $item['price_expected']);
                    $item['price_suppliers'] = str_replace(',', '', $item['price_suppliers']);
                    $item['promotion_expected'] = str_replace(',', '', $item['promotion_expected']);

                    $item['exchange_standard_unit'] = str_replace(',', '', $item['exchange_standard_unit']);
                    $item['quantity_stock'] = str_replace(',', '', $item['quantity_stock']);
                    $item['exchange_stock'] = str_replace(',', '', $item['exchange_stock']);
                    $item['quantity_payment'] = str_replace(',', '', $item['quantity_payment']);
                    $item['exchange_payment'] = str_replace(',', '', $item['exchange_payment']);
                    if(($item['quantity_stock'] == 'NaN')){
                        $item['quantity_stock'] = 0;
                    }
                    if(($item['quantity_payment'] == 'NaN')){
                        $item['quantity_payment'] = 0;
                    }

                    $total_expected = $item['quantity_payment'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = (($item['quantity_payment'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100)))  - $item['promotion_expected']);
                    $total_novats = ($item['quantity_payment'] * $item['price_suppliers']);
                    $promotion_expecteds += $item['promotion_expected'];
                    if (empty($item['plan_id'])) {
                        $plan_id = 0;
                    } else {
                        $plan_id = $item['plan_id'];
                        if ($item['plan_id'] != $plan_id_check) {
                            $plan_id_text .= $item['plan_id'] . ',';
                            $plan_id_check = $item['plan_id'];
                        }
                    }
                    $items = array(
                        'id_purchase_order' => $id,
                        'info_items' => $info_items_text,
                        'product_id' => $item['id'],
                        'type' => $item['type'],
                        'quantity' => $item['quantity'],
                        'tax_id' => $item['tax_id'],
                        'tax_rate' => $item['tax_rate'],
                        'quantity_suppliers' => $item['quantity_suppliers'],
                        'price_expected' => $item['price_expected'],
                        'price_suppliers' => $item['price_suppliers'],
                        'promotion_expected' => $item['promotion_expected'],
                        'quantity_unit' => $item['quantity_suppliers'],
                        'exchange_unit' => $item['exchange_standard_unit'],
                        'quantity_stock' => $item['quantity_stock'],
                        'exchange_stock' => $item['exchange_stock'],
                        'quantity_payment' => $item['quantity_payment'],
                        'exchange_payment' => $item['exchange_payment'],
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                        'plan_id' => $plan_id,
                    );
                    if ($this->db->insert('tblpurchase_order_items', $items)) {
                        $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $data['suppliers_id'], 'id_items' => $item['id'], 'type' => $item['type']), '', 'row');

                        if (!empty($ktr_supp)) {
                            $this->db->update('tblmainstream_goods', array('amount_to_vnd' => $amount_to_vnd, 'price' => $item['price_suppliers']), array('id' => $ktr_supp->id));
                        } else {
                            $_mainstream = array(
                                'id_suppliers' => $data['suppliers_id'],
                                'id_items' => $item['id'],
                                'type' => $item['type'],
                                'price' => $item['price_suppliers'],
                                'amount_to_vnd' => $amount_to_vnd,
                            );
                            $this->db->insert('tblmainstream_goods', $_mainstream);
                        }
                        if (!empty($data['id_purchases'])) {
                            $purchases_items = get_table_where('tblpurchases_items', array('product_id' => $item['id'], 'type' => $item['type'], 'purchases_id' => $data['id_purchases']), '', 'row');
                            $this->db->update('tblpurchases_items', array('quantity_create' => ($item['quantity_suppliers'] + $purchases_items->quantity_create)), array('id' => $purchases_items->id));
                        }
                        log_activity('Purchase order insert [ID Supplier quotes: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                        $total_expected_all += $total_expected;
                        $total_suppliers_all += $total_suppliers;
                        $total_novat += $total_novats;
                    } else {
                        exit("error");
                    }
                }
            }
            $price_expected = 0;
            $price_suppliers = 0;

            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = str_replace(',', '', $data['discount_percent_suppliers']);
            $sub_expected = 0;

            $price_expected = $total_expected_all - $sub_expected;

            if ($data['valtype_check_suppliers'] == 1) {
                $sub_suppliers = $total_suppliers_all * $data['discount_percent_suppliers'] / 100;
            } else if ($data['valtype_check_suppliers'] == 2) {
                $sub_suppliers = $data['discount_percent_suppliers'];
            }
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
            $total_dqd = $price_suppliers * $amount_to_vnd;
            $total_novat_dqd = $total_novat * $amount_to_vnd;
            $plan_id_text = trim($plan_id_text, ',');
            $_items =  array(
                'valtype_check_expected' => $data['valtype_check_expected'],
                'valtype_check_suppliers' => $data['valtype_check_suppliers'],
                'discount_percent_expected' => $data['discount_percent_expected'],
                'discount_percent_suppliers' => $data['discount_percent_suppliers'],
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'total_cqd' => $price_suppliers,
                'total_novat' => $total_novat,
                'promotion_expected' => $promotion_expecteds,
                'total_dqd' => $price_suppliers * $amount_to_vnd,
                'plan_id' => $plan_id_text,
            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
        }
        if ($count > 0) {
            if (!empty($data['id_purchases'])) {
                create_purchase_to_order($id);
            }
            return $id;
        }
        return false;
    }
    public function update($data = array(), $id = '')
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        $amount_to_vnd = str_replace(',', '', $data['amount_to_vnd']);
        
        $plan_id_text = '';
        $plan_id_check = 0;
        $quotes = array(
            'note' => $data['note'],
            'tax_all' => $data['tax_all'],
            'suppliers_id' => $data['suppliers_id'],
            'delivery_date' => to_sql_date($data['delivery_date']),
            'delivery_cost' => str_replace(',', '', $data['delivery_cost']),
            'reduce_cost' => str_replace(',', '', $data['reduce_cost']),
        );
        if($order->amount_to_vnd != $amount_to_vnd){
            $quotes['amount_to_vnd'] = $amount_to_vnd;
        }
        $delivery_cost = str_replace(',', '', $data['delivery_cost']);
        $reduce_cost = str_replace(',', '', $data['reduce_cost']);
        if ($this->db->update('tblpurchase_order', $quotes, array('id' => $id))) {
            log_activity('Purchase order updateted [ID: ' . $id . ']');
            $count = 1;
        }
        $items = $data['items'];
        if ($id) {
          
            if (!empty($order->id_purchases)) {
                $order_items = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
                foreach ($order_items as $key => $value) {
                    $purchases_items = get_table_where('tblpurchases_items', array('product_id' => $value['product_id'], 'type' => $value['type'], 'purchases_id' => $order->id_purchases), '', 'row');
                    $this->db->update('tblpurchases_items', array('quantity_create' => ($purchases_items->quantity_create - $value['quantity_suppliers'])), array('id' => $purchases_items->id));
                }
            }
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['purchase_order'] = $_custom_fields['purchase_order'];
                handle_custom_fields_post($id, $custom_fields);
            }
            $array = array();
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;

            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $info_items = array();
                    $info_items['recipe'] = $item['recipe'];
                    $info_items['paper'] = $item['paper'];
                    $info_items['longs'] = $item['longs'];
                    $info_items['wide'] = $item['wide'];
                    $info_items_text = json_encode($info_items);

                    $item['quantity'] = str_replace(',', '', $item['quantity']);
                    $item['quantity_suppliers'] = str_replace(',', '', $item['quantity_suppliers']);
                    $item['price_expected'] = str_replace(',', '', $item['price_expected']);
                    $item['price_suppliers'] = str_replace(',', '', $item['price_suppliers']);
                    $item['promotion_expected'] = str_replace(',', '', $item['promotion_expected']);


                    $item['exchange_standard_unit'] = str_replace(',', '', $item['exchange_standard_unit']);
                    $item['quantity_stock'] = str_replace(',', '', $item['quantity_stock']);
                    $item['exchange_stock'] = str_replace(',', '', $item['exchange_stock']);
                    $item['quantity_payment'] = str_replace(',', '', $item['quantity_payment']);
                    $item['exchange_payment'] = str_replace(',', '', $item['exchange_payment']);


                    $total_expected = $item['quantity_payment'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = ($item['quantity_payment'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100))) - $item['promotion_expected'];
                    $total_novats = $item['quantity_payment'] * $item['price_suppliers'];
                    $promotion_expecteds += $item['promotion_expected'];
                    if (empty($item['plan_id'])) {
                        $plan_id = 0;
                    } else {
                        $plan_id = $item['plan_id'];
                        if ($item['plan_id'] != $plan_id_check) {
                            $plan_id_text .= $item['plan_id'] . ',';
                            $plan_id_check = $item['plan_id'];
                        }
                    }
                    $items = array(
                        'id_purchase_order' => $id,
                        'info_items' => $info_items_text,
                        'product_id' => $item['id'],
                        'type' => $item['type'],
                        'quantity' => $item['quantity'],
                        'tax_id' => $item['tax_id'],
                        'tax_rate' => $item['tax_rate'],
                        'quantity_suppliers' => $item['quantity_suppliers'],
                        'price_expected' => $item['price_expected'],
                        'price_suppliers' => $item['price_suppliers'],
                        'promotion_expected' => $item['promotion_expected'],
                        'quantity_unit' => $item['quantity_suppliers'],
                        'exchange_unit' => $item['exchange_standard_unit'],
                        'quantity_stock' => $item['quantity_stock'],
                        'exchange_stock' => $item['exchange_stock'],
                        'quantity_payment' => $item['quantity_payment'],
                        'exchange_payment' => $item['exchange_payment'],
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                        'plan_id' => $plan_id,
                    );
                    $ktr = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id, 'product_id' => $item['id'], 'type' => $item['type']), '', 'row');
                    if ($ktr) {
                        $this->db->update('tblpurchase_order_items', $items, array('id' => $ktr->id));
                        $array[] = $ktr->id;
                    } else {
                        $this->db->insert('tblpurchase_order_items', $items);
                        $array[] = $this->db->insert_id();
                    }
                    if (!empty($order->id_purchases)) {
                        $purchases_items = get_table_where('tblpurchases_items', array('product_id' => $item['id'], 'type' => $item['type'], 'purchases_id' => $order->id_purchases), '', 'row');
                        $this->db->update('tblpurchases_items', array('quantity_create' => ($item['quantity_suppliers'] + $purchases_items->quantity_create)), array('id' => $purchases_items->id));
                    }
                    log_activity('Purchase order updateted [ID Supplier quotes: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                    $count++;
                    $total_expected_all += $total_expected;
                    $total_suppliers_all += $total_suppliers;
                    $total_novat += $total_novats;
                }
            }
            $price_expected = 0;
            $price_suppliers = 0;
            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = str_replace(',', '', $data['discount_percent_suppliers']);
            $sub_expected = 0;

            $price_expected = $total_expected_all - $sub_expected;

            if ($data['valtype_check_suppliers'] == 1) {
                $sub_suppliers = $total_suppliers_all * $data['discount_percent_suppliers'] / 100;
            } else if ($data['valtype_check_suppliers'] == 2) {
                $sub_suppliers = $data['discount_percent_suppliers'];
            }
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
            $plan_id_text = trim($plan_id_text, ',');
            $_items =  array(
                'valtype_check_expected' => $data['valtype_check_expected'],
                'valtype_check_suppliers' => $data['valtype_check_suppliers'],
                'discount_percent_expected' => $data['discount_percent_expected'],
                'discount_percent_suppliers' => $data['discount_percent_suppliers'],
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'total_novat' => $total_novat,
                'total_dqd' => $price_suppliers * $amount_to_vnd,
                'promotion_expected' => $promotion_expecteds,
                'plan_id' => $plan_id_text,

            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
            if (!empty($array)) {
                $this->db->where('id_purchase_order', $id);
                $this->db->where_not_in('id', $array);
                $this->db->delete('tblpurchase_order_items');
            }
        }
        if ($count > 0) {
            if (!empty($order->id_purchase_proce)) {
                set_status_purchse_order($id);
            }
            return $id;
        }
        return false;
    }
    public function delete_purchase_order($id = '')
    {
        $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');

        $this->db->where('id', $id);
        $this->db->delete('tblpurchase_order');
        if ($this->db->affected_rows() > 0) {
            if (!empty($purchase_order->id_purchases) && $purchase_order->check_purchase_all == 0) {
                $order_items = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
                foreach ($order_items as $key => $value) {
                    if($value['purchase_items_id']){
                        $purchases_items = get_table_where('tblpurchases_items', array('id' => $value['purchase_items_id']), '', 'row');;
                    }else{
                        $purchases_items = get_table_where('tblpurchases_items', array('product_id' => $value['product_id'], 'type' => $value['type'], 'purchases_id' => $purchase_order->id_purchases), '', 'row');;
                    }
                    $this->db->update('tblpurchases_items', array('quantity_create' => ($purchases_items->quantity_create - $value['quantity_suppliers'])), array('id' => $purchases_items->id));
                }
                if (!empty($purchase_order->id_purchases)) {
                    $ktr_purchases = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
                    if ($ktr_purchases->status == 4) {
                        $cance = explode('|', $ktr_purchases->history_status);
                        $cances = explode(',', $cance[3]);
                        if ($cances[0] == '1foso') {
                            $history_statuss = '';
                            $history_status = $ktr_purchases->history_status;
                            $history = explode('|', $history_status);
                            foreach ($history as $key => $value) {
                                if ($key > 0) {
                                    if ($key < 3) {
                                        $history_statuss .= '|' . $value;
                                    }
                                }
                            }
                            $history_status = trim($history_status, '|');
                            $in = array(
                                'history_status' => $history_statuss,
                                'note_cancel' => '',
                                'status' => 3,
                            );
                            $this->db->where('id', $purchase_order->id_purchases);
                            $this->db->update('tblpurchases', $in);
                        }
                    }

                    $purchase_orders = get_table_where('tblpurchase_order', array('id_purchases' => $purchase_order->id_purchases), '', 'row');

                    if (empty($purchase_orders)) {
                        $this->db->update('tblpurchases', array('process' => ''), array('id' => $purchase_order->id_purchases));
                    }
                }
            }
            if (!empty($purchase_order->id_purchases) && $purchase_order->check_purchase_all == 1) {
                $id_purchases = explode(',', trim($purchase_order->id_purchases));
                foreach ($id_purchases as $kch => $vch) {
                    $order_items = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id));
                    foreach ($order_items as $key => $value) {
                        // $purchases_itemss = get_table_where('tblpurchases_items', array('product_id' => $value['product_id'], 'type' => $value['type'], 'purchases_id' => $vch), '', 'row');
                        if($value['purchase_items_id']){
                            $purchases_itemss = get_table_where('tblpurchases_items', array('id' => $value['purchase_items_id']), '', 'row');;
                        }else{
                            $purchases_itemss = get_table_where('tblpurchases_items', array('product_id' => $value['product_id'], 'type' => $value['type'], 'purchases_id' => $vch), '', 'row');
                        }
                        if (!empty($purchases_itemss)) {
                            $quantity_create_all = get_table_where('tblpurchase_to_order_items', array('id_items' => $value['product_id'], 'type' => $value['type'], 'id_purchase' => $vch, 'id_purchase_order' => $id, 'id_purchase_items' => $purchases_itemss->id, 'id_purchase_order_items' => $value['id']), '', 'row');
                            if (!empty($quantity_create_all)) {
                                $this->db->update('tblpurchases_items', array('quantity_create_all' => ($purchases_itemss->quantity_create_all - $quantity_create_all->quantity)), array('id' => $purchases_itemss->id));
                                $this->db->update('tblpurchases', array('id_order' => 0), array('id' => $vch));
                                $this->db->delete('tblpurchase_to_order_items', array('id' => $quantity_create_all->id));
                                $ktr_purchases = get_table_where('tblpurchases', array('id' => $vch), '', 'row');
                                $history_statuss = '';
                                $history_status = $ktr_purchases->history_status;
                                $history = explode('|', $history_status);
                                foreach ($history as $key => $value) {
                                    if ($key > 0) {
                                        if ($key < 3) {
                                            $history_statuss .= '|' . $value;
                                        }
                                    }
                                }
                                $history_status = trim($history_status, '|');
                                $in = array(
                                    'history_status' => $history_statuss,
                                    'note_cancel' => '',
                                    'status' => 3,
                                );
                                $this->db->where('id', $vch);
                                $this->db->update('tblpurchases', $in);
                            }
                        }
                    }
                }
            }

            $this->db->where('id_purchase_order', $id);
            $this->db->delete('tblpurchase_order_items');
            $this->db->delete('tblpurchase_to_order', array('id_purchases_order' => $id));
            log_activity('Purchase order Deleted [ID:' . $id . ']');

            if (!empty($purchase_order->id_quotes)) {
                $purchase_orders = get_table_where('tblpurchase_order', array('id_quotes' => $purchase_order->id_quotes), '', 'row');
                if (empty($purchase_orders)) {
                    $this->db->update('tblsupplier_quotes', array('process' => ''), array('id' => $purchase_order->id_quotes));
                }
            }

            if (!empty($purchase_order->id_purchase_proce)) {
                $ktr_purchases = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchase_proce), '', 'row');
                if ($ktr_purchases->status == 4) {
                    $cance = explode('|', $ktr_purchases->history_status);
                    $cances = explode(',', $cance[3]);
                    if ($cances[0] == '1foso') {
                        $history_statuss = '';
                        $history_status = $ktr_purchases->history_status;
                        $history = explode('|', $history_status);
                        foreach ($history as $key => $value) {
                            if ($key > 0) {
                                if ($key < 3) {
                                    $history_statuss .= '|' . $value;
                                }
                            }
                        }
                        $history_status = trim($history_status, '|');
                        $in = array(
                            'history_status' => $history_statuss,
                            'note_cancel' => '',
                            'status' => 3,
                        );
                        $this->db->where('id', $purchase_order->id_purchase_proce);
                        $this->db->update('tblpurchases', $in);
                    }
                }
            }
            return true;
        }

        return false;
    }
    public function update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblpurchase_order', $data);
        if ($this->db->affected_rows() > 0) {
            if ($data['status'] == 4) {
                $this->db->set('id_tickets_priorities', NULL);
                $this->db->where('id', $id);
                $this->db->update('tblpurchase_order');
            }
            return true;
        }
        return false;
    }
    public function get_items_import_order($id, $type, $id_order, $plan_id, $idd = '')
    {

        if ($type == 'items') {
            $this->db->select('tblitems.*,tblunits.unit as unit_name,tblpurchase_order_items.quantity_suppliers,tblpurchase_order_items.price_suppliers,tblpurchase_order_items.tax_id,tblpurchase_order_items.tax_rate,tblpurchase_order_items.promotion_expected,tblpurchase_order_items.quantity as quantity_purchase_order,tblpurchase_order_items.plan_id as plan_id,tbl_productions_plan.reference_no as code_plan,tblpurchase_order_items.id as idd,tblpurchase_order.suppliers_id,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,info_items,tblpurchase_order.type_plan')->distinct();
            $this->db->from('tblpurchase_order_items');

            $this->db->order_by('tblitems.id', 'desc');
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
            if (!empty($id_order)) {
                $this->db->join('tblitems', 'tblitems.id = tblpurchase_order_items.product_id');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id=tblpurchase_order_items.plan_id', 'left');
                $this->db->where('tblpurchase_order_items.id_purchase_order', $id_order);
                $this->db->where('tblpurchase_order_items.type', $type);
                $this->db->where('tblpurchase_order_items.plan_id', $plan_id);
            }
            if (is_numeric($id)) {

                $this->db->where('tblitems.id', $id);
            }
            if (is_numeric($idd)) {

                $this->db->where('tblpurchase_order_items.id', $idd);
            }
            $item = $this->db->get()->row();
            $item->color = format_item_color($id, $type);
            $quantity = $this->sum_quantity_import($type, $id_order, $id, $plan_id, $idd);
            if (empty($quantity)) {
                $quantity = 0;
            }
            $item->info_items = json_decode($item->info_items);
            $item->quantity_suppliers = $item->quantity_suppliers - $quantity;
            return $item;
        } elseif ($type == 'nvl') {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblpurchase_order_items.quantity_suppliers,tblpurchase_order_items.price_suppliers,tblpurchase_order_items.tax_id,tblpurchase_order_items.tax_rate,tblpurchase_order_items.promotion_expected,tblpurchase_order_items.quantity as quantity_purchase_order,tblpurchase_order_items.plan_id as plan_id,tbl_productions_plan.reference_no as code_plan,tblpurchase_order_items.id as idd,tblpurchase_order.suppliers_id,tblpurchase_order_items.exchange_stock as exchange_standard_unit,tblpurchase_order_items.exchange_payment,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,tblpurchase_order_items.exchange_unit,recipe,paper,longs,wide,info_items,tblpurchase_order.type_plan')->distinct();
            $this->db->from('tblpurchase_order_items');
            $this->db->order_by($table . '.id', 'desc');
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
            if (!empty($id_order)) {
                $this->db->join($table, $table . '.id = tblpurchase_order_items.product_id ');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id=tblpurchase_order_items.plan_id', 'left');
                $this->db->where('tblpurchase_order_items.id_purchase_order', $id_order);
                $this->db->where('tblpurchase_order_items.type', $type);
                $this->db->where('tblpurchase_order_items.plan_id', $plan_id);
            }
            if (is_numeric($idd)) {

                $this->db->where('tblpurchase_order_items.id', $idd);
            }
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                $quantity = $this->sum_quantity_import($type, $id_order, $id, $plan_id, $idd);
                if (empty($quantity)) {
                    $quantity = 0;
                }
                $item->color = format_item_color($id, $type);
                $item->info_items = json_decode($item->info_items);
                $item->quantity_suppliers = $item->quantity_suppliers - $quantity;
                return $item;
            }
        } else {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblpurchase_order_items.quantity_suppliers,tblpurchase_order_items.price_suppliers,tblpurchase_order_items.tax_id,tblpurchase_order_items.tax_rate,tblpurchase_order_items.promotion_expected,tblpurchase_order_items.quantity as quantity_purchase_order,tblpurchase_order_items.plan_id as plan_id,tbl_productions_plan.reference_no as code_plan,tblpurchase_order_items.id as idd,tblpurchase_order.suppliers_id,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,info_items,tblpurchase_order.type_plan')->distinct();
            $this->db->from('tblpurchase_order_items');
            $this->db->order_by($table . '.id', 'desc');
            $this->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
            if (!empty($id_order)) {
                $this->db->join($table, $table . '.id = tblpurchase_order_items.product_id ');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id=tblpurchase_order_items.plan_id', 'left');
                $this->db->where('tblpurchase_order_items.id_purchase_order', $id_order);
                $this->db->where('tblpurchase_order_items.type', $type);
                $this->db->where('tblpurchase_order_items.plan_id', $plan_id);
            }
            if (is_numeric($idd)) {

                $this->db->where('tblpurchase_order_items.id', $idd);
            }
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                $quantity = $this->sum_quantity_import($type, $id_order, $id, $plan_id, $idd);
                if (empty($quantity)) {
                    $quantity = 0;
                }
                $item->color = format_item_color($id, $type);
                $item->info_items = json_decode($item->info_items);
                $item->quantity_suppliers = $item->quantity_suppliers - $quantity;

                return $item;
            }
        }
    }
    public function sum_quantity_import($type = '', $id = '', $id_product, $plan_id = 0, $idd = '')
    {
        $this->db->select('SUM(tblimport_items.quantity_net) as quantity_net');
        $this->db->from('tblimport_items');
        $this->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        $this->db->where('tblimport.id_order', $id);
        $this->db->where('product_id', $id_product);
        if (is_numeric($idd)) {

            $this->db->where('tblimport_items.id_purchase_order_items', $idd);
        }
        $this->db->where('type', $type);
        $this->db->where('tblimport_items.plan_id', $plan_id);
        return $this->db->get()->row()->quantity_net;
    }
    public function add_all($data = array())
    {

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $plan_id_text = '';
        $plan_id_check = 0;
        $amount_to_vnd = str_replace(',', '', $data['amount_to_vnd']);

        $quotes = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1),
            'prefix' => get_option('prefix_purchase_order'),
            'staff_create' => get_staff_user_id(),
            'date' => to_sql_date($data['date']),
            'delivery_date' => to_sql_date($data['delivery_date']),
            'date_create' => date('Y:m:d H:i:s'),
            'suppliers_id' => $data['suppliers_id'],
            'type_items' => $data['type_items'],
            'currency' => $data['currency_ch'],
            'amount_to_vnd' => $amount_to_vnd,
            'type_order' => $data['type_order'],
            'status' => 3,
            'type_plan' => $data['type_plan'],
            'note' => $data['note'],
            'check_purchase_all' => 1,
            'tax_all' => $data['tax_all'],
            'history_status' => get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s'),
            'delivery_cost' => str_replace(',', '', $data['delivery_cost']),
            'reduce_cost' => str_replace(',', '', $data['reduce_cost']),
        );
        $delivery_cost = str_replace(',', '', $data['delivery_cost']);
        $reduce_cost = str_replace(',', '', $data['reduce_cost']);
        if ($quotes['type_plan'] == 2) {
            $quotes['type_plan'] == 0;
        }
        $quotes['id_purchases'] = $data['id_purchases'];
        if ($this->db->insert('tblpurchase_order', $quotes)) {
            $id = $this->db->insert_id();

            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                unset($custom_fields);
                $custom_fields['purchase_order'] = $_custom_fields['purchase_order'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Purchase order Insert [ID: ' . $id . ']');
            $count = 1;
        }
        $items = $data['items'];
        if ($id) {
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;

            foreach ($items as $key => $item) {
                $item['quantity'] = str_replace(',', '', $item['quantity']);
                $info_items = array();
                $info_items['recipe'] = $item['recipe'];
                $info_items['paper'] = $item['paper'];
                $info_items['longs'] = $item['longs'];
                $info_items['wide'] = $item['wide'];
                $info_items_text = json_encode($info_items);
                if (!empty($item['id']) && $item['quantity'] > 0) {
                    $item['exchange_standard_unit'] = str_replace(',', '', $item['exchange_standard_unit']);
                    $item['quantity_stock'] = str_replace(',', '', $item['quantity_stock']);
                    $item['exchange_stock'] = str_replace(',', '', $item['exchange_stock']);
                    $item['quantity_payment'] = str_replace(',', '', $item['quantity_payment']);
                    $item['exchange_payment'] = str_replace(',', '', $item['exchange_payment']);

                    $item['quantity_suppliers'] = str_replace(',', '', $item['quantity_suppliers']);
                    $item['price_expected'] = str_replace(',', '', $item['price_expected']);
                    $item['price_suppliers'] = str_replace(',', '', $item['price_suppliers']);
                    $item['promotion_expected'] = str_replace(',', '', $item['promotion_expected']);
                    $total_expected = $item['quantity_payment'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = (($item['quantity_payment'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100)))  - $item['promotion_expected']);
                    $total_novats = ($item['quantity_payment'] * $item['price_suppliers']);
                    $promotion_expecteds += $item['promotion_expected'];
                    if (empty($item['plan_id'])) {
                        $plan_id = 0;
                    } else {
                        $plan_id = $item['plan_id'];
                        if ($item['plan_id'] != $plan_id_check) {
                            $plan_id_text .= $item['plan_id'] . ',';
                            $plan_id_check = $item['plan_id'];
                        }
                    }

                    $items = array(
                        'id_purchase_order' => $id,
                        'info_items' => $info_items_text,
                        'product_id' => $item['id'],
                        'type' => $item['type'],
                        'quantity' => $item['quantity'],
                        'tax_id' => $item['tax_id'],
                        'tax_rate' => $item['tax_rate'],
                        'quantity_suppliers' => $item['quantity_suppliers'],
                        'price_expected' => $item['price_expected'],
                        'price_suppliers' => $item['price_suppliers'],
                        'promotion_expected' => $item['promotion_expected'],
                        'quantity_unit' => $item['quantity_suppliers'],
                        'exchange_unit' => $item['exchange_standard_unit'],
                        'quantity_stock' => $item['quantity_stock'],
                        'exchange_stock' => $item['exchange_stock'],
                        'quantity_payment' => $item['quantity_payment'],
                        'exchange_payment' => $item['exchange_payment'],
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                        'plan_id' => $plan_id,

                    );
                    if ($this->db->insert('tblpurchase_order_items', $items)) {
                        $id_items_order = $this->db->insert_id();
                        $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $data['suppliers_id'], 'id_items' => $item['id'], 'type' => $item['type']), '', 'row');

                        if (!empty($ktr_supp)) {
                            $this->db->update('tblmainstream_goods', array('amount_to_vnd' => $amount_to_vnd, 'price' => $item['price_suppliers']), array('id' => $ktr_supp->id));
                        } else {
                            $_mainstream = array(
                                'id_suppliers' => $data['suppliers_id'],
                                'id_items' => $item['id'],
                                'type' => $item['type'],
                                'price' => $item['price_suppliers'],
                                'amount_to_vnd' => $amount_to_vnd,
                            );
                            $this->db->insert('tblmainstream_goods', $_mainstream);
                        }
                        if (!empty($data['id_purchases'])) {
                            $id_purchases = explode(',', trim($data['id_purchases'], ','));
                            $quantity = $item['quantity_suppliers'];
                            foreach ($id_purchases as $kch => $vch) {
                                $purchases_items = get_table_where('tblpurchases_items', array('product_id' => $item['id'], 'type' => $item['type'], 'purchases_id' => $vch), '', 'row');
                                if (!empty($purchases_items)) {
                                    $quantity_all = $purchases_items->quantity_net - $purchases_items->quantity_create_all - $purchases_items->quantity_create;
                                    if (!empty($purchases_items) && ($quantity > 0) && ($quantity_all > 0)) {
                                        $quantity_create_all = $purchases_items->quantity_net - $purchases_items->quantity_create_all - $purchases_items->quantity_create - $quantity;
                                        if ($quantity > 0) {
                                            if ($quantity_create_all < 0) {
                                                $this->db->update('tblpurchases_items', array('quantity_create_all' => ($purchases_items->quantity_net - $purchases_items->quantity_create)), array('id' => $purchases_items->id));
                                                $quantity = $quantity - ($purchases_items->quantity_net - $purchases_items->quantity_create  - $purchases_items->quantity_create_all);

                                                $purchase_to_order_items = array(
                                                    'id_items' => $item['id'],
                                                    'type' => $item['type'],
                                                    'quantity' => $purchases_items->quantity_net - $purchases_items->quantity_create - $purchases_items->quantity_create_all,
                                                    'id_purchase' => $vch,
                                                    'id_purchase_order' => $id,
                                                    'id_purchase_items' => $purchases_items->id,
                                                    'id_purchase_order_items' => $id_items_order,
                                                );

                                                $this->db->insert('tblpurchase_to_order_items', $purchase_to_order_items);
                                            } else {
                                                $this->db->update('tblpurchases_items', array('quantity_create_all' => ($purchases_items->quantity_create_all + $quantity)), array('id' => $purchases_items->id));
                                                $purchase_to_order_items = array(
                                                    'id_items' => $item['id'],
                                                    'type' => $item['type'],
                                                    'quantity' => $quantity,
                                                    'id_purchase' => $vch,
                                                    'id_purchase_order' => $id,
                                                    'id_purchase_items' => $purchases_items->id,
                                                    'id_purchase_order_items' => $id_items_order,
                                                );
                                                $this->db->insert('tblpurchase_to_order_items', $purchase_to_order_items);
                                                $quantity = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        log_activity('Purchase order insert [ID purchases: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                        $total_expected_all += $total_expected;
                        $total_suppliers_all += $total_suppliers;
                        $total_novat += $total_novats;
                    } else {
                        exit("error");
                    }
                }
            }
            foreach ($id_purchases as $kch => $vch) {
                $this->db->select('quantity_net,quantity_create_all,quantity_create');
                $this->db->where('purchases_id', $vch);
                $this->db->having('(quantity_net - quantity_create_all - quantity_create) >', 0);
                $items_purchase = $this->db->get('tblpurchases_items')->row();
                if (empty($items_purchase)) {
                    $purchases = get_table_where('tblpurchases', array('id' => $vch), '', 'row');
                    $staff_id = '1foso';
                    $date = date('Y-m-d H:i:s');
                    $history_status = $purchases->history_status;
                    $history_status .= '|' . $staff_id . ',' . $date;
                    $in = array(
                        'history_status' => $history_status,
                        'note_cancel' => '',
                        'status' => 4,
                    );
                    $this->db->where('id', $vch);
                    $this->db->update('tblpurchases', $in);
                }
                $this->db->insert('tblpurchase_to_order', array('id_purchases' => $vch, 'id_purchases_order' => $id));
            }
            $price_expected = 0;
            $price_suppliers = 0;

            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = str_replace(',', '', $data['discount_percent_suppliers']);
            $sub_expected = 0;

            $price_expected = $total_expected_all - $sub_expected;

            if ($data['valtype_check_suppliers'] == 1) {
                $sub_suppliers = $total_suppliers_all * $data['discount_percent_suppliers'] / 100;
            } else if ($data['valtype_check_suppliers'] == 2) {
                $sub_suppliers = $data['discount_percent_suppliers'];
            }
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
            $plan_id_text = trim($plan_id_text, ',');
            $_items =  array(
                'valtype_check_expected' => $data['valtype_check_expected'],
                'valtype_check_suppliers' => $data['valtype_check_suppliers'],
                'discount_percent_expected' => $data['discount_percent_expected'],
                'discount_percent_suppliers' => $data['discount_percent_suppliers'],
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'total_cqd' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'total_dqd' => $price_suppliers * $amount_to_vnd,
                'total_novat' => $total_novat,
                'promotion_expected' => $promotion_expecteds,
                'plan_id' => $plan_id_text,

            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
        }
        if ($count > 0) {
            return $id;
        }
        return false;
    }
    public function update_all($data = array(), $id = '')
    {
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $quotes = array(
            'note' => $data['note'],
            'tax_all' => $data['tax_all'],
            'suppliers_id' => $data['suppliers_id'],
            'delivery_date' => to_sql_date($data['delivery_date']),
            'delivery_cost' => str_replace(',', '', $data['delivery_cost']),
            'reduce_cost' => str_replace(',', '', $data['reduce_cost']),
        );
        $delivery_cost = str_replace(',', '', $data['delivery_cost']);
        $reduce_cost = str_replace(',', '', $data['reduce_cost']);
        if ($this->db->update('tblpurchase_order', $quotes, array('id' => $id))) {
            log_activity('Purchase order updateted [ID: ' . $id . ']');
            $count = 1;
        }
        $items = $data['items'];
        if ($id) {
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                unset($custom_fields);
                $custom_fields['purchase_order'] = $_custom_fields['purchase_order'];
                handle_custom_fields_post($id, $custom_fields);
            }
            $array = array();
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novats = 0;
            $promotion_expecteds = 0;
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $item['quantity'] = str_replace(',', '', $item['quantity']);
                    $item['quantity_suppliers'] = str_replace(',', '', $item['quantity_suppliers']);
                    $item['price_expected'] = str_replace(',', '', $item['price_expected']);
                    $item['price_suppliers'] = str_replace(',', '', $item['price_suppliers']);
                    $item['promotion_expected'] = str_replace(',', '', $item['promotion_expected']);
                    $total_expected = $item['quantity'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = ($item['quantity_suppliers'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100))) - $item['promotion_expected'];
                    $total_novats = $item['quantity_suppliers'] * $item['price_suppliers'];
                    $promotion_expecteds += $item['promotion_expected'];
                    $items = array(
                        'id_purchase_order' => $id,
                        'product_id' => $item['id'],
                        'type' => $item['type'],
                        'quantity' => $item['quantity'],
                        'tax_id' => $item['tax_id'],
                        'tax_rate' => $item['tax_rate'],
                        'quantity_suppliers' => $item['quantity_suppliers'],
                        'price_expected' => $item['price_expected'],
                        'price_suppliers' => $item['price_suppliers'],
                        'promotion_expected' => $item['promotion_expected'],
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                    );
                    $ktr = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id, 'product_id' => $item['id'], 'type' => $item['type']), '', 'row');
                    if ($ktr) {
                        $this->db->update('tblpurchase_order_items', $items, array('id' => $ktr->id));
                        $array[] = $ktr->id;
                    }
                    log_activity('Purchase order updateted [ID Supplier quotes: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                    $count++;
                    $total_expected_all += $total_expected;
                    $total_suppliers_all += $total_suppliers;
                    $total_novat += $total_novats;
                }
            }
            $price_expected = 0;
            $price_suppliers = 0;
            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = str_replace(',', '', $data['discount_percent_suppliers']);
            $sub_expected = $total_expected_all * $data['discount_percent_expected'] / 100;

            $price_expected = $total_expected_all - $sub_expected;

            if ($data['valtype_check_suppliers'] == 1) {
                $sub_suppliers = $total_suppliers_all * $data['discount_percent_suppliers'] / 100;
            } else if ($data['valtype_check_suppliers'] == 2) {
                $sub_suppliers = $data['discount_percent_suppliers'];
            }
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;

            $_items =  array(
                'valtype_check_expected' => $data['valtype_check_expected'],
                'valtype_check_suppliers' => $data['valtype_check_suppliers'],
                'discount_percent_expected' => $data['discount_percent_expected'],
                'discount_percent_suppliers' => $data['discount_percent_suppliers'],
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'promotion_expected' => $promotion_expecteds,
            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
        }
        if ($count > 0) {
            return $id;
        }
        return false;
    }

    public function get_items_purchase_order_plan($id)
    {
        $this->db->select('tblpurchase_order_items.*,SUM(tblpurchase_order_items.quantity_suppliers) as quantity_suppliers,SUM(tblpurchase_order_items.promotion_expected) as promotion_expected,SUM(tblpurchase_order_items.total_suppliers) as total_suppliers')->distinct();
        $this->db->from('tblpurchase_order_items');
        $this->db->where('id_purchase_order', $id);
        $this->db->group_by('tblpurchase_order_items.product_id,tblpurchase_order_items.type');
        $items = $this->db->get()->result_array();

        // $items = get_table_where('tblpurchase_order_items',array('id_purchase_order'=>$id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.price as price,tblitems.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.name as name_item,' . $table . '.images as avatar,' . $table . '.price_sell as price,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
                $items[$key]['avatar'] = (!empty($items[$key]['avatar']) ? (file_exists($items[$key]['avatar']) ? base_url($items[$key]['avatar']) : (file_exists('uploads/materials/' . $items[$key]['avatar']) ? base_url('uploads/materials/' . $items[$key]['avatar']) : (file_exists('uploads/products/' . $items[$key]['avatar']) ? base_url('uploads/products/' . $items[$key]['avatar']) : (file_exists('uploads/tools_supplies/' . $items[$key]['avatar']) ? base_url('uploads/tools_supplies/' . $items[$key]['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            }
        }
        return $items;
    }

    public function convertWarningWarehousePurchaseOrder($data = array())
    {

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $plan_id_text = '';
        $plan_id_check = 0;
        $quotes = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchase_order') + 1),
            'prefix' => get_option('prefix_purchase_order'),
            'staff_create' => get_staff_user_id(),
            'date' => to_sql_date($data['date']),
            'delivery_date' => to_sql_date($data['delivery_date']),
            'date_create' => date('Y:m:d H:i:s'),
            'suppliers_id' => $data['suppliers_id'],
            'type_items' => $data['type_items'],
            'tax_all' => $data['tax_all'],
            'status' => 3,
            'note' => $data['note'],
            'history_status' => get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s') . '|' . get_staff_user_id() . ',' . date('Y:m:d H:i:s'),
            'delivery_cost' => str_replace(',', '', $data['delivery_cost']),
            'reduce_cost' => str_replace(',', '', $data['reduce_cost']),
        );
        $delivery_cost = str_replace(',', '', $data['delivery_cost']);
        $reduce_cost = str_replace(',', '', $data['reduce_cost']);

        if (!empty($data['id_purchases'])) {
            $id_purchase = get_table_where('tblpurchases', array('id' => $data['id_purchases']), '', 'row');
            $quotes['type_plan'] = $id_purchase->type_plan;
            $quotes['id_purchases'] = $data['id_purchases'];
        }
        if (!empty($data['id_quotes'])) {
            $id_quotes = get_table_where('tblsupplier_quotes', array('id' => $data['id_quotes']), '', 'row');
            $quotes['type_plan'] = $id_quotes->type_plan;
            $quotes['id_purchase_proce'] = $id_quotes->id_purchase_proce;
            $quotes['id_quotes'] = $data['id_quotes'];
        }
        if ($this->db->insert('tblpurchase_order', $quotes)) {
            $id = $this->db->insert_id();
            if (!empty($data['id_purchases'])) {
                $this->db->update('tblpurchases', array('process' => ('3|' . $id)), array('id' => $data['id_purchases']));
            }
            if (!empty($data['id_quotes'])) {
                $this->db->update('tblsupplier_quotes', array('process' => ('3|' . $id)), array('id' => $data['id_quotes']));
            }
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                unset($custom_fields);
                $custom_fields['purchase_order'] = $_custom_fields['purchase_order'];
                handle_custom_fields_post($id, $custom_fields);
            }
            log_activity('Purchase order Insert [ID: ' . $id . ']');
            $count = 1;
        }
        $items = $data['items'];
        if ($id) {
            $total_expected_all = 0;
            $total_suppliers_all = 0;
            $total_novat = 0;
            $promotion_expecteds = 0;

            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    $item['quantity'] = str_replace(',', '', $item['quantity']);
                    $item['quantity_suppliers'] = str_replace(',', '', $item['quantity_suppliers']);
                    $item['price_expected'] = str_replace(',', '', $item['price_expected']);
                    $item['price_suppliers'] = str_replace(',', '', $item['price_suppliers']);
                    $item['promotion_expected'] = str_replace(',', '', $item['promotion_expected']);
                    $total_expected = $item['quantity'] * $item['price_expected'] * (1 + ($item['tax_rate'] / 100));
                    $total_suppliers = (($item['quantity_suppliers'] * $item['price_suppliers'] * (1 + ($item['tax_rate'] / 100)))  - $item['promotion_expected']);
                    $total_novats = ($item['quantity_suppliers'] * $item['price_suppliers']);
                    $promotion_expecteds += $item['promotion_expected'];
                    if (empty($item['plan_id'])) {
                        $plan_id = 0;
                    } else {
                        $plan_id = $item['plan_id'];
                        if ($item['plan_id'] != $plan_id_check) {
                            $plan_id_text .= $item['plan_id'] . ',';
                            $plan_id_check = $item['plan_id'];
                        }
                    }
                    $items = array(
                        'id_purchase_order' => $id,
                        'product_id' => $item['id'],
                        'type' => $item['type'],
                        'quantity' => $item['quantity'],
                        'tax_id' => $item['tax_id'],
                        'tax_rate' => $item['tax_rate'],
                        'quantity_suppliers' => $item['quantity_suppliers'],
                        'price_expected' => $item['price_expected'],
                        'price_suppliers' => $item['price_suppliers'],
                        'promotion_expected' => $item['promotion_expected'],
                        'total_expected' => $total_expected,
                        'total_suppliers' => $total_suppliers,
                        'note' => $item['note'],
                        'plan_id' => $plan_id,
                    );
                    if ($this->db->insert('tblpurchase_order_items', $items)) {
                        $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $data['suppliers_id'], 'id_items' => $item['id'], 'type' => $item['type']), '', 'row');

                        if (!empty($ktr_supp)) {
                            $this->db->update('tblmainstream_goods', array('price' => $item['price_suppliers']), array('id' => $ktr_supp->id));
                        } else {
                            $_mainstream = array(
                                'id_suppliers' => $data['suppliers_id'],
                                'id_items' => $item['id'],
                                'type' => $item['type'],
                                'price' => $item['price_suppliers'],
                            );
                            $this->db->insert('tblmainstream_goods', $_mainstream);
                        }
                        if (!empty($data['id_purchases'])) {
                            $purchases_items = get_table_where('tblpurchases_items', array('product_id' => $item['id'], 'type' => $item['type'], 'purchases_id' => $data['id_purchases']), '', 'row');
                            $this->db->update('tblpurchases_items', array('quantity_create' => ($item['quantity_suppliers'] + $purchases_items->quantity_create)), array('id' => $purchases_items->id));
                        }
                        log_activity('Purchase order insert [ID Supplier quotes: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                        $total_expected_all += $total_expected;
                        $total_suppliers_all += $total_suppliers;
                        $total_novat += $total_novats;
                    } else {
                        exit("error");
                    }
                }
            }
            $price_expected = 0;
            $price_suppliers = 0;

            $data['discount_percent_expected'] = 0;
            $data['discount_percent_suppliers'] = str_replace(',', '', $data['discount_percent_suppliers']);
            $sub_expected = 0;

            $price_expected = $total_expected_all - $sub_expected;

            if ($data['valtype_check_suppliers'] == 1) {
                $sub_suppliers = $total_suppliers_all * $data['discount_percent_suppliers'] / 100;
            } else if ($data['valtype_check_suppliers'] == 2) {
                $sub_suppliers = $data['discount_percent_suppliers'];
            }
            $price_suppliers = $total_suppliers_all - $sub_suppliers + $delivery_cost - $reduce_cost;
            $plan_id_text = trim($plan_id_text, ',');
            $_items =  array(
                'valtype_check_expected' => $data['valtype_check_expected'],
                'valtype_check_suppliers' => $data['valtype_check_suppliers'],
                'discount_percent_expected' => $data['discount_percent_expected'],
                'discount_percent_suppliers' => $data['discount_percent_suppliers'],
                'totalAll_expected' => $total_expected_all,
                'totalAll_suppliers' => $price_suppliers,
                'price_expected' => $price_expected,
                'price_suppliers' => $price_suppliers,
                'total_novat' => $total_novat,
                'promotion_expected' => $promotion_expecteds,
                'plan_id' => $plan_id_text,
            );
            $this->db->update('tblpurchase_order', $_items, array('id' => $id));
        }
        if ($count > 0) {
            if (!empty($data['id_purchases'])) {
                create_purchase_to_order($id);
            }
            return $id;
        }
        return false;
    }
    public function checkExistOrders($code)
    {
        $this->db->from('tblpurchase_order');
        $this->db->where('tblpurchase_order.code', $code);
        return $this->db->get()->num_rows();
    }
    public function getSuppliertByZcodeOrCompany($customer)
    {
        $this->db->select('tblsuppliers.id, tblsuppliers.company');
        $this->db->from('tblsuppliers');
        $this->db->group_start();
        $this->db->where('tblsuppliers.company', $customer);
        $this->db->or_where('tblsuppliers.code', $customer);
        $this->db->group_end();
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
    public function getProductsByCode($code)
    {
        $this->db->select('tbl_products.id, tbl_products.code, tbl_products.name');
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
    public function getNVLByCode($code)
    {
        $this->db->select('tbl_materials.id, tbl_materials.code, tbl_materials.name');
        $this->db->from('tbl_materials');
        $this->db->where('tbl_materials.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
    public function getToolsByCode($code)
    {
        $this->db->select('tbl_tools_supplies.id, tbl_tools_supplies.code, tbl_tools_supplies.name');
        $this->db->from('tbl_tools_supplies');
        $this->db->where('tbl_tools_supplies.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }
}
