<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchases_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_targert($id)
    {
        $this->db->select('tblevaluation_criteria.*')->distinct();
        $this->db->from('tblevaluation_criteria');
        $targert = $this->db->get()->result_array();
        foreach ($targert as $key => $value) {
            $targert[$key]['targert'] = get_table_where('tblevaluation_criteria_children', array('id_evaluation' => $value['id']));
            foreach ($targert[$key]['targert'] as $k => $v) {
                $targert[$key]['targert'][$k]['point'] = get_table_where('tblevaluate_suppliers', array('id_rfq_ask_price' => $id, 'id_evaluation_criteria' => $value['id'], 'id_evaluation_criteria_children' => $v['id']));
            }
        }
        foreach ($targert as $key => $value) {
            if (empty($value['targert'])) {
                unset($targert[$key]);
            }
        }
        return $targert;
    }
    public function get_ask_price($id = '')
    {
        $this->db->select('tblrfq_ask_price.*')->distinct();
        $this->db->from('tblrfq_ask_price');
        $this->db->where('id_purchases', $id);
        $ask_price = $this->db->get()->row();
        $ask_price->items = $this->get_items_ask_price($ask_price->id);
        return $ask_price;
    }
    public function get_items_order($id, $type, $id_purchases, $id_supplier)
    {
        if ($type == 'items') {
            $this->db->select('tblitems.*,tblunits.unit as unit_name,tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all,tblpurchases_items.id_plan,tblpurchases_items.id as id_purchases_items,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide')->distinct();
            $this->db->from('tblpurchases_items');

            $this->db->order_by('tblitems.id', 'desc');
            if (!empty($id_purchases)) {
                $this->db->join('tblitems', 'tblitems.id = tblpurchases_items.product_id');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblpurchases_items.purchases_id', $id_purchases);
                $this->db->where('tblpurchases_items.type', $type);
            }
            if (is_numeric($id)) {

                $this->db->where('tblitems.id', $id);
            }
            $item = $this->db->get()->row();
            if (!empty($id_supplier)) {
                $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $id_supplier, 'id_items' => $id, 'type' => $type), '', 'row');
                if (!empty($ktr_supp)) {
                    $item->price_ch =   $ktr_supp->price;
                } else {
                    $item->price_ch =   0;
                }
            } else {
                $item->price_ch =   0;
            }

            $item->quantity_net = $item->quantity_net - $item->quantity_create - $item->quantity_create_all;
            return $item;
        } elseif ($type == 'nvl') {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all,tblpurchases_items.id_plan,tblpurchases_items.id as id_purchases_items,tblunits.unit as unit_name,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide')->distinct();
            $this->db->from('tblpurchases_items');
            $this->db->order_by($table . '.id', 'desc');
            if (!empty($id_purchases)) {
                $this->db->join($table, $table . '.id = tblpurchases_items.product_id ');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where('tblpurchases_items.purchases_id', $id_purchases);
                $this->db->where('tblpurchases_items.type', $type);
            }
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                if (!empty($id_supplier)) {
                    $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $id_supplier, 'id_items' => $id, 'type' => $type), '', 'row');
                    if (!empty($ktr_supp)) {
                        $item->price_ch =   $ktr_supp->price;
                    } else {
                        $item->price_ch =   0;
                    }
                } else {
                    $item->price_ch =   0;
                }
                $item->quantity_net = $item->quantity_net - $item->quantity_create - $item->quantity_create_all;
                return $item;
            }
        } else {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all,tblpurchases_items.id_plan,tblpurchases_items.id as id_purchases_items,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide')->distinct();
            $this->db->from('tblpurchases_items');
            $this->db->order_by($table . '.id', 'desc');
            if (!empty($id_purchases)) {
                $this->db->join($table, $table . '.id = tblpurchases_items.product_id ');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where('tblpurchases_items.purchases_id', $id_purchases);
                $this->db->where('tblpurchases_items.type', $type);
            }
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                if (!empty($id_supplier)) {
                    $ktr_supp = get_table_where('tblmainstream_goods', array('id_suppliers' => $id_supplier, 'id_items' => $id, 'type' => $type), '', 'row');
                    if (!empty($ktr_supp)) {
                        $item->price_ch =   $ktr_supp->price;
                    } else {
                        $item->price_ch =   0;
                    }
                } else {
                    $item->price_ch =   0;
                }
                $item->quantity_net = $item->quantity_net - $item->quantity_create - $item->quantity_create_all;
                return $item;
            }
        }
    }
    public function get_items_ask_price_suppliers($id, $id_suppliers)
    {

        $this->db->select('tblrfq_ask_price_items.*,tblsupplier_quote_items.unit_cost as unit_cost')->distinct();
        $this->db->from('tblrfq_ask_price_items');
        $this->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id_ask_price=tblrfq_ask_price_items.id_rfq_ask_price', 'left');
        $this->db->join('tblsupplier_quote_items', 'tblsupplier_quote_items.id_supplier_quotes=tblsupplier_quotes.id AND tblsupplier_quote_items.product_id = tblrfq_ask_price_items.product_id', 'left');
        $this->db->where('tblrfq_ask_price_items.id_rfq_ask_price', $id);
        $this->db->where('tblrfq_ask_price_items.suppliers_id', $id_suppliers);
        $this->db->group_by('tblrfq_ask_price_items.id');
        $items = $this->db->get()->result_array();
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
            }
        }
        return $items;
    }
    public function get_items_ask_price($id)
    {

        $this->db->select('tblrfq_ask_price_items.*,tblsupplier_quote_items.unit_cost as unit_cost')->distinct();
        $this->db->from('tblrfq_ask_price_items');
        $this->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id_ask_price=tblrfq_ask_price_items.id_rfq_ask_price', 'left');
        $this->db->join('tblsupplier_quote_items', 'tblsupplier_quote_items.id_supplier_quotes=tblsupplier_quotes.id AND tblsupplier_quote_items.product_id = tblrfq_ask_price_items.product_id AND tblsupplier_quote_items.type = tblrfq_ask_price_items.type AND tblsupplier_quotes.suppliers_id = tblrfq_ask_price_items.suppliers_id', 'left');
        $this->db->where('tblrfq_ask_price_items.id_rfq_ask_price', $id);
        $this->db->group_by('tblrfq_ask_price_items.id');
        $items = $this->db->get()->result_array();
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $items[$key] = array_merge($items[$key], $this->db->get()->row_array());
            }
        }
        return $items;
    }
    public function get_items_ch()
    {
        $this->db->select('tblitems.*')->distinct();
        $this->db->from('tblitems');
        $this->db->order_by('tblitems.id', 'desc');
        return $this->db->get()->result_array();
    }
    public function get_items_purchases_v2($id, $id_ask = '')
    {
        $items = get_table_where('tblpurchases_items', array('purchases_id' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);


                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);

                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $data['check'] = 1;
                if (!empty($id_ask)) {
                    $this->db->where('tblsupplier_quotes.id_ask_price', $id_ask);
                    $this->db->where('tblsupplier_quote_items.product_id', $value['product_id']);
                    $this->db->where('tblsupplier_quote_items.type', $value['type']);
                    $this->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id = tblsupplier_quote_items.id_supplier_quotes', 'left');
                    $items_ask = $this->db->get('tblsupplier_quote_items')->row();
                    if (!empty($items_ask)) {
                        $data['check'] = 2;
                    }
                }
                $items[$key] = array_merge($items[$key], $data);
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item,tblunits.unit as unit')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);

                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);
                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));

                $data['check'] = 1;
                if (!empty($id_ask)) {
                    $this->db->where('tblsupplier_quotes.id_ask_price', $id_ask);
                    $this->db->where('tblsupplier_quote_items.product_id', $value['product_id']);
                    $this->db->where('tblsupplier_quote_items.type', $value['type']);
                    $this->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id = tblsupplier_quote_items.id_supplier_quotes', 'left');
                    $items_ask = $this->db->get('tblsupplier_quote_items')->row();
                    if (!empty($items_ask)) {
                        $data['check'] = 2;
                    }
                }
                $items[$key] = array_merge($items[$key], $data);
            }
        }
        return $items;
    }
    public function get_items_purchases($id)
    {
        $items = get_table_where('tblpurchases_items', array('purchases_id' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,tblunits.unit as unit_name,"" as company_supp,0 as id_supp')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);


                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);

                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key] = array_merge($items[$key], $data);
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit,exchange_unit,exchange_standard_unit,exchange_unit_payment,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide,tblunits.unit as unit_name')->distinct();
                $this->db->from($table);
                // $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);

                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);
                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $supp = [];
                $supp['company_supp'] = '';
                $supp['id_supp'] = 0; 
                $this->db->select('tblsuppliers.company as company_supp,COALESCE(tblsuppliers.id,0) as id_supp');
                $this->db->where('tbl_material_suppliers.material_id',$value['product_id']);
                $this->db->join('tblsuppliers', 'tblsuppliers.id=tbl_material_suppliers.supplier_id', 'left');
                $this->db->order_by('tbl_material_suppliers.id asc');
                $this->db->limit(1);
                $material_suppliers = $this->db->get('tbl_material_suppliers')->row_array();
                if(!empty($material_suppliers)){
                    $supp['company_supp'] = $material_suppliers['company_supp'];
                    $supp['id_supp'] = $material_suppliers['id_supp'] ; 
                }
                $data = array_merge($data, $supp);

                $items[$key] = array_merge($items[$key], $data);
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,tblunits.unit as unit_name,"" as company_supp,0 as id_supp')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);

                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);
                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key] = array_merge($items[$key], $data);
            }
        }
        return $items;
    }
    public function get_items_supplier($id, $supplier_id)
    {
        // $this->db->select('tblitems.id,tblitems.type_items,tblitems.name,tblitems.code,tblpurchases_items.quantity_net')->distinct();
        $this->db->from('tblpurchases_items');
        $this->db->where('tblpurchases_items.product_id IN(select tblmainstream_goods.id_items from tblmainstream_goods where id_suppliers=' . $supplier_id . ')');
        $this->db->where('purchases_id', $id);
        $items = $this->db->get()->result_array();;
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['product_id']);
                $data = $this->db->get()->row_array();
                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);
                $items[$key] = array_merge($items[$key], $data);
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['product_id']);
                $data = $this->db->get()->row_array();
                $data['html'] = format_item_color($value['product_id'], $value['type'], 1);
                $items[$key] = array_merge($items[$key], $data);
            }
        }
        return $items;
    }
    public function get($id = '')
    {
        $this->db->select('tblpurchases.*')->distinct();
        $this->db->from('tblpurchases');
        $this->db->where('id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_purchases($id);
        return $purchases;
    }
    public function get_create_purchase_order($id = '', $id_order = '')
    {
        $this->db->select('tblpurchases.*')->distinct();
        $this->db->from('tblpurchases');
        $this->db->where('id', $id);
        $purchases = $this->db->get()->row();
        $purchases->items = $this->get_items_purchase_order($id, $id_order);
        return $purchases;
    }
    public function get_items_order_quantity($id, $type, $id_purchases)
    {
        if ($type == 'items') {
            $this->db->select('tblitems.*,tblunits.unit as unit_name,tblpurchases_items.quantity_net')->distinct();
            $this->db->from('tblpurchases_items');

            $this->db->order_by('tblitems.id', 'desc');
            if (!empty($id_purchases)) {
                $this->db->join('tblitems', 'tblitems.id = tblpurchases_items.product_id');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblpurchases_items.purchases_id', $id_purchases);
                $this->db->where('tblpurchases_items.type', $type);
            }
            if (is_numeric($id)) {

                $this->db->where('tblitems.id', $id);
            }
            $item = $this->db->get()->row();

            $quantity = sum_quantity_order_purchases($type, $id_purchases, $id);
            if (empty($quantity)) {
                $quantity = 0;
            }
            $item->quantity_net = $item->quantity_net - $quantity;
            return $item;
        } else {
            $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
            $this->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name,tblpurchases_items.quantity_net')->distinct();
            $this->db->from('tblpurchases_items');
            $this->db->order_by($table . '.id', 'desc');
            if (!empty($id_purchases)) {
                $this->db->join($table, $table . '.id = tblpurchases_items.product_id ');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where('tblpurchases_items.purchases_id', $id_purchases);
                $this->db->where('tblpurchases_items.type', $type);
            }
            if (is_numeric($id)) {

                $this->db->where($table . '.id', $id);
                $item = $this->db->get()->row();
                $quantity = sum_quantity_order_purchases($type, $id_purchases, $id);
                if (empty($quantity)) {
                    $quantity = 0;
                }
                $item->quantity_net = $item->quantity_net - $quantity;
                return $item;
            }
        }
    }
    public function get_items_purchase_order($id_purchases, $id_order = '')
    {

        $result['result'] = array();
        $table = get_table_where('tbltype_items');
        $temp = '';
        $dem_temp = 0;
        foreach ($table as $key => $value) {
            $result['result'][$dem_temp] = array('id' => 'h', 'name' => $value['name'], 'type_items' => $value['type']);
            $dem_temp++;
            if (!empty($id_purchases)) {
                if ($value['type'] == 'tools') {
                    $this->db->select($value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name as name,' . $value['id_type'] . ' as type,tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all,tblpurchases_items.id as id_purchases_items');
                } else if ($value['type'] == 'items') {
                    $this->db->select($value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name as name,' . $value['id_type'] . ' as type,tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all,tblpurchases_items.id as id_purchases_items');
                } else {
                    $this->db->select($value['table'] . '.mode as mode,' . $value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name as name,' . $value['id_type'] . ' as type,tblpurchases_items.quantity_net,tblpurchases_items.quantity_create,tblpurchases_items.quantity_create_all,tblpurchases_items.id as id_purchases_items');
                }
                $this->db->from('tblpurchases_items');
            }
            $this->db->group_by($value['table'] . '.id');
            $this->db->order_by($value['table'] . '.id', 'ASC');
            if (!empty($id_purchases)) {
                $this->db->join($value['table'], $value['table'] . '.id = tblpurchases_items.product_id');
                $this->db->where('tblpurchases_items.purchases_id', $id_purchases);
                $this->db->where('tblpurchases_items.type', $value['type']);
            }
            $data = $this->db->get()->result_array();


            foreach ($data as $key_data => $value_data) {
                $value_data = array_merge($value_data, array('type_items' => $value['type']));
                $quantity_order = 0;
                if (!empty($id_purchases)) {
                    if (!empty($id_order)) {
                        $quantity_orderss = get_table_where('tblpurchase_order_items', array('id_purchase_order' => $id_order, 'product_id' => $value_data['id'], 'type' => $value_data['type_items']), '', 'row');
                        if (!empty($quantity_orderss)) {
                            $quantity_order = $quantity_orderss->quantity_suppliers;
                        }
                    }
                    $quantity_net = $value_data['quantity_net'] - $value_data['quantity_create'] - $value_data['quantity_create_all'];
                    $value_data['quantity_net'] = $quantity_net + $quantity_order;


                    if ($value_data['quantity_net'] <= 0) {
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
    public function add_rfq($data = array(), $idd = '')
    {
        $id_purchase = get_table_where('tblpurchases', array('id' => $idd), '', 'row');
        $suppliers_id = '';
        $count = 0;
        $purchase = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblrfq_ask_price') + 1),
            'prefix' => get_option('prefix_rfq_ask_price'),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 1,
            'id_purchases' => $idd,
            'type_plan' => $id_purchase->type_plan,
        );
        if ($this->db->insert('tblrfq_ask_price', $purchase)) {
            $id = $this->db->insert_id();
            $this->db->update('tblpurchases', array('process' => ('1|' . $id)), array('id' => $idd));
            log_activity('Ask price Insert [ID: ' . $id . ']');
            $count = 0;
        }
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $item) {
                $suppliers_id = $key . ',' . $suppliers_id;
                foreach ($item as $k => $v) {
                    $items = array(
                        'id_rfq_ask_price' => $id,
                        'suppliers_id' => $key,
                        'product_id' => $v['product_id'],
                        'quantity' => $v['quantity_net'],
                        'type' => $v['type'],
                    );
                    if ($this->db->insert('tblrfq_ask_price_items', $items)) {
                        log_activity('Ask price items insert [ID Ask price: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                    } else {
                        exit("error");
                    }
                }
            }
        }
        if ($count > 0) {
            $suppliers_id = trim($suppliers_id, ',');
            $this->db->update('tblrfq_ask_price', array('suppliers_id' => $suppliers_id), array('id' => $id));
            return $id;
        }

        return false;
    }
    public function update_rfq($data = array(), $id = '')
    {

        $suppliers_id = '';
        $count = 0;
        $purchase_id = $data['id'];
        $array = array();

        log_activity('Ask price updateted [ID: ' . $purchase_id . ']');
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $item) {
                $suppliers_id = $key . ',' . $suppliers_id;
                foreach ($item as $k => $v) {
                    $items = array(
                        'id_rfq_ask_price' => $purchase_id,
                        'suppliers_id' => $key,
                        'product_id' => $v['product_id'],
                        'quantity' => $v['quantity_net'],
                        'type' => $v['type'],
                    );
                    $ktr = get_table_where('tblrfq_ask_price_items', array('id_rfq_ask_price' => $purchase_id, 'product_id' => $v['product_id'], 'suppliers_id' => $key), '', 'row');

                    if (!$ktr) {
                        if ($this->db->insert('tblrfq_ask_price_items', $items)) {
                            $array[] = $this->db->insert_id();

                            log_activity('Ask price items insert [ID Ask price: ' . $purchase_id . ', ID Product: ' . $items['product_id'] . ']');
                            $count++;
                        } else {
                            exit("error");
                        }
                    } else {
                        $array[] = $ktr->id;
                    }
                }
                if (!empty($array)) {

                    $this->db->where('id_rfq_ask_price', $purchase_id);
                    $this->db->where_not_in('id', $array);
                    $this->db->delete('tblrfq_ask_price_items');
                }
            }
        }
        if (!empty($array)) {

            $this->db->where('id_rfq_ask_price', $purchase_id);
            $this->db->where_not_in('id', $array);
            $this->db->delete('tblrfq_ask_price_items');
        }
        if ($count > 0) {

            $suppliers_id = trim($suppliers_id, ',');
            $this->db->update('tblrfq_ask_price', array('suppliers_id' => $suppliers_id), array('id' => $purchase_id));
            return $purchase_id;
        }

        return false;
    }
    public function add($data, $expense = false)
    {
        $purchase = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
            'prefix' => get_option('prefix_purchase'),
            'name_purchase' => $data['name'],
            'explanation' => $data['reason'],
            'date' => to_sql_date($data['date'], true),
            'delivery_date' => to_sql_date($data['delivery_date'], true),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 2,
            'type' => 1,
            'id_plan' => 0,
            'type_plan' => 0,
        );
        if ($this->db->insert('tblpurchases', $purchase)) {
            $id = $this->db->insert_id();
            log_activity('Purchase Insert [ID: ' . $id . ']');
            $count = 0;
        }

        $itemss = $data['items'];

        if ($id) {
            foreach ($itemss as $key => $item) {
                if (!empty($item['id'])) {
                    $items = array(
                        'purchases_id' => $id,
                        'product_id' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'type' => $item['type'],
                        'note' => $item['note'],
                    );
                    if ($this->db->insert('tblpurchases_items', $items)) {

                        log_activity('Purchase items insert [ID Purchase: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                    } else {
                        exit("error");
                    }
                }
            }
        }

        if ($id) {
            create_purchase($id);
            return $id;
        }

        return false;
    }

    public function convertCapactityToPurchase($data, $expense = false)
    {
        $purchase = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
            'prefix' => get_option('prefix_purchase'),
            'name_purchase' => $data['name'],
            'explanation' => $data['reason'],
            'date_need' => to_sql_date($data['date_need']),
            'date' => to_sql_date($data['date'], true),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 2,
            'type' => 1,
            'id_plan' => $data['id_plan'],
            'type_plan' => 1,
            'is_plans' => $data['is_plans'],
        );
        if ($this->db->insert('tblpurchases', $purchase)) {
            $id = $this->db->insert_id();
            log_activity('Purchase Insert [ID: ' . $id . ']');
            $count = 0;
        }

        $itemss = $data['items'];

        if ($id) {
            foreach ($itemss as $key => $item) {
                if (!empty($item['id'])) {
                    $items = array(
                        'purchases_id' => $id,
                        'product_id' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'id_plan' => $data['id_plan'],
                    );
                    if ($this->db->insert('tblpurchases_items', $items)) {

                        log_activity('Purchase items insert [ID Purchase: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                    } else {
                        exit("error");
                    }
                }
            }
        }

        if ($id) {
            return $id;
        }

        return false;
    }
    public function convertWarningWarehousePurchase($data)
    {
        $purchase = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
            'prefix' => get_option('prefix_purchase'),
            'name_purchase' => $data['name'],
            'explanation' => $data['reason'],
            'date' => to_sql_date($data['date'], true),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 2,
            'type' => 1,
            'id_plan' => 0,
            'type_plan' => 0,
            'type_warning_warehouse' => $data['type_warning_warehouse'],
        );
        if ($this->db->insert('tblpurchases', $purchase)) {
            $id = $this->db->insert_id();
            log_activity('Purchase Insert [ID: ' . $id . ']');
            $count = 0;
        }

        $itemss = $data['items'];

        if ($id) {
            foreach ($itemss as $key => $item) {
                if (!empty($item['id'])) {
                    $items = array(
                        'purchases_id' => $id,
                        'product_id' => $item['id'],
                        'quantity' => str_replace(',', '', $item['quantity']),
                        'quantity_net' => str_replace(',', '', $item['quantity_net']),
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'id_plan' => 0,
                    );
                    if ($this->db->insert('tblpurchases_items', $items)) {

                        log_activity('Purchase items insert [ID Purchase: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                    } else {
                        exit("error");
                    }
                }
            }
        }

        if ($id) {
            return $id;
        }

        return false;
    }

    public function update($data = NULL, $id = '')
    {
        $purchase = array(
            'name_purchase' => $data['name'],
            'explanation' => $data['reason'],
            'date' => to_sql_date($data['date'], true),
            'delivery_date' => to_sql_date($data['delivery_date'], true),
        );
        $this->db->where('id', $id);
        if ($this->db->update('tblpurchases', $purchase)) {
            log_activity('Purchase updateted [ID: ' . $id . ']');
            $count = true;
            $counts = 0;
        }

        $items = $data['items'];

        if ($count) {
            $affected_id = array();
            // for ($i=0; $i < count($items); $i++) {
            foreach ($items as $key => $item) {
                if (!empty($item['id'])) {
                    if (isset($item))
                        $affected_id[] = $item['id'];

                    $it = get_table_where('tblpurchases_items', array('purchases_id' => $id, 'product_id' => $item['id'], 'type' => $item['type']), '', 'row');
                    if (!empty($it)) {
                        $itemd = array(
                            'quantity' => str_replace(',', '', $item['quantity']),
                            'quantity_net' => str_replace(',', '', $item['quantity_net']),
                            'note' => $item['note'],
                        );
                        $this->db->update('tblpurchases_items', $itemd, array('id' => $it->id));
                        if ($this->db->affected_rows()) {
                            logActivity('Purchase items updateted [ID Purchase: ' . $id . ', ID Product: ' . $it->product_id . ']');
                            $counts++;
                        }
                    } else {

                        $itemd = array(
                            'purchases_id' => $id,
                            'product_id' => $item['id'],
                            'quantity' => str_replace(',', '', $item['quantity']),
                            'quantity_net' => str_replace(',', '', $item['quantity_net']),
                            'type' => $item['type'],
                            'note' => $item['note'],
                        );
                        if ($this->db->insert('tblpurchases_items', $itemd)) {

                            log_activity('Purchase items insert [ID Purchase: ' . $id . ', ID Product: ' . $itemd['product_id'] . ']');
                            $counts++;
                        }
                    }
                }
                if (empty($affected_id)) {
                    $this->db->where('purchases_id', $id);
                    $this->db->delete('tblpurchases_items');
                } else {
                    $this->db->where('purchases_id', $id);
                    $this->db->where_not_in('product_id', $affected_id);
                    $this->db->delete('tblpurchases_items');
                }
            }
        }

        if ($counts > 0) {
            return true;
        }

        return false;
    }
    public function delete_purchases($id = '')
    {
        $this->db->where('id', $id);
        $this->db->delete('tblpurchases');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('purchases_id', $id);
            $this->db->delete('tblpurchases_items');

            $this->deletePurchasePlans($id);
            log_activity('Purchases Deleted [ID:' . $id . ']');
            return true;
        }

        return false;
    }
    public function delete_rfq($id = '')
    {
        $rfq = get_table_where('tblrfq_ask_price', array('id' => $id), '', 'row');
        $this->db->where('id', $id);
        $this->db->delete('tblrfq_ask_price');
        if ($this->db->affected_rows() > 0) {

            $this->db->where('id_rfq_ask_price', $id);
            $this->db->delete('tblrfq_ask_price_items');
            log_activity('Ask price Deleted [ID:' . $id . ']');
            if (!empty($rfq->id_purchases)) {
                $this->db->update('tblpurchases', array('process' => ''), array('id' => $rfq->id_purchases));
            }
            return true;
        }

        return false;
    }
    public function update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tblpurchases', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function change_purchases_type($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblpurchases', [
            'type' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Ask price Type Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }
    //tạo đơn hàng tổng
    public function get_items_purchase_order_all($id = '')
    {
        $id_purchases = explode(',', trim($id, ','));
        $result['result'] = array();
        $table = get_table_where('tbltype_items');
        $temp = '';
        $dem_temp = 0;
        foreach ($table as $key => $value) {
            if ($value['type'] == 'items') {
                if (!empty($id_purchases)) {
                    $this->db->select($value['table'] . '.price as price,' . $value['table'] . '.avatar as avatar,' . $value['table'] . '.code as code_item,tblunits.unit as unit_name,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all');
                    $this->db->from('tblpurchases_items');
                }
                $this->db->group_by($value['table'] . '.id');
                $this->db->order_by($value['table'] . '.id', 'ASC');
                if (!empty($id_purchases)) {
                    $this->db->join($value['table'], $value['table'] . '.id = tblpurchases_items.product_id');
                    $this->db->join('tblunits', 'tblunits.unitid=' . $value['table'] . '.unit', 'left');
                    $this->db->where_in('tblpurchases_items.purchases_id', $id_purchases);
                    $this->db->where('tblpurchases_items.type', $value['type']);
                }
                $data = $this->db->get()->result_array();


                foreach ($data as $key_data => $value_data) {
                    $value_data = array_merge($value_data, array('type_items' => $value['type']));

                    $quantity_net = $value_data['quantity_net'] - $value_data['quantity_create'] - $value_data['quantity_create_all'];
                    $value_data['quantity_net'] = $quantity_net;
                    if ($quantity_net <= 0) {
                    } else {
                        $value_data['avatar'] = (!empty($value_data['avatar']) ? (file_exists($value_data['avatar']) ? base_url($value_data['avatar']) : (file_exists('uploads/materials/' . $value_data['avatar']) ? base_url('uploads/materials/' . $value_data['avatar']) : (file_exists('uploads/products/' . $value_data['avatar']) ? base_url('uploads/products/' . $value_data['avatar']) : (file_exists('uploads/tools_supplies/' . $value_data['avatar']) ? base_url('uploads/tools_supplies/' . $value_data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
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
            } else {
                if (!empty($id_purchases)) {
                    $this->db->select($value['table'] . '.price_sell as price,' . $value['table'] . '.images as avatar,' . $value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,tblunits.unit as unit_name,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all');
                    $this->db->from('tblpurchases_items');
                }
                $this->db->group_by($value['table'] . '.id');
                $this->db->order_by($value['table'] . '.id', 'ASC');
                if (!empty($id_purchases)) {
                    $this->db->join($value['table'], $value['table'] . '.id = tblpurchases_items.product_id');
                    $this->db->join('tblunits', 'tblunits.unitid=' . $value['table'] . '.unit_id', 'left');

                    $this->db->where_in('tblpurchases_items.purchases_id', $id_purchases);
                    $this->db->where('tblpurchases_items.type', $value['type']);
                }
                $data = $this->db->get()->result_array();


                foreach ($data as $key_data => $value_data) {
                    $value_data = array_merge($value_data, array('type_items' => $value['type']));

                    $quantity_net = $value_data['quantity_net'] - $value_data['quantity_create'] - $value_data['quantity_create_all'];
                    $value_data['quantity_net'] = $quantity_net;
                    if ($quantity_net <= 0) {
                    } else {
                        $value_data['avatar'] = (!empty($value_data['avatar']) ? (file_exists($value_data['avatar']) ? base_url($value_data['avatar']) : (file_exists('uploads/materials/' . $value_data['avatar']) ? base_url('uploads/materials/' . $value_data['avatar']) : (file_exists('uploads/products/' . $value_data['avatar']) ? base_url('uploads/products/' . $value_data['avatar']) : (file_exists('uploads/tools_supplies/' . $value_data['avatar']) ? base_url('uploads/tools_supplies/' . $value_data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
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
        }
        return $result['result'];
    }
    public function get_items_purchase_order_all_plan($id = '')
    {
        $id_purchases = explode(',', trim($id, ','));
        $result['result'] = array();
        $table = get_table_where('tbltype_items');
        $temp = '';
        $dem_temp = 0;
        foreach ($table as $key => $value) {
            $result['result'][$dem_temp] = array('id' => 'h', 'name' => $value['name'], 'type_items' => $value['type']);
            $dem_temp++;
            if ($value['type'] == 'items') {
                if (!empty($id_purchases)) {
                    $this->db->select($value['table'] . '.price as price,' . $value['table'] . '.avatar as avatar,' . $value['table'] . '.code as code_item,tblunits.unit as unit_name,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all,tblpurchases_items.id_plan as id_plan,tbl_productions_plan.reference_no as code_plan,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as recipe,1 as paper,1 as longs,1 as wide');
                    $this->db->from('tblpurchases_items');
                }
                $this->db->group_by($value['table'] . '.id,tblpurchases_items.id_plan');
                $this->db->order_by($value['table'] . '.id', 'ASC');
                if (!empty($id_purchases)) {
                    $this->db->join($value['table'], $value['table'] . '.id = tblpurchases_items.product_id');
                    $this->db->join('tblunits', 'tblunits.unitid=' . $value['table'] . '.unit', 'left');
                    $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id=tblpurchases_items.id_plan', 'left');
                    $this->db->where_in('tblpurchases_items.purchases_id', $id_purchases);
                    $this->db->where('tblpurchases_items.type', $value['type']);
                }
                $data = $this->db->get()->result_array();


                foreach ($data as $key_data => $value_data) {
                    $value_data = array_merge($value_data, array('type_items' => $value['type']));

                    $quantity_net = $value_data['quantity_net'] - $value_data['quantity_create'] - $value_data['quantity_create_all'];
                    $value_data['quantity_net'] = $quantity_net;
                    if ($quantity_net <= 0) {
                    } else {
                        $value_data['avatar'] = (!empty($value_data['avatar']) ? (file_exists($value_data['avatar']) ? base_url($value_data['avatar']) : (file_exists('uploads/materials/' . $value_data['avatar']) ? base_url('uploads/materials/' . $value_data['avatar']) : (file_exists('uploads/products/' . $value_data['avatar']) ? base_url('uploads/products/' . $value_data['avatar']) : (file_exists('uploads/tools_supplies/' . $value_data['avatar']) ? base_url('uploads/tools_supplies/' . $value_data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
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
            } else {
                if (!empty($id_purchases)) {
                    // $this->db->select($value['table'] . '.price_sell as price,' . $value['table'] . '.images as avatar,' . $value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,tblunits.unit as unit_name,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all,tblpurchases_items.id_plan as id_plan,tbl_productions_plan.reference_no as code_plan,tblpurchases_items.id as idd');
                    if ($value['type'] == 'tools') {
                        $this->db->select($value['table'] . '.price_sell as price,' . $value['table'] . '.images as avatar,' . $value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,tblunits.unit as unit_name,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all,tblpurchases_items.id_plan as id_plan,tbl_productions_plan.reference_no as code_plan,tblpurchases_items.id as idd,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as recipe,1 as paper,1 as longs,1 as wide');
                    } elseif ($value['type'] == 'nvl') {
                        $this->db->select($value['table'] . '.price_sell as price,' . $value['table'] . '.images as avatar,' . $value['table'] . '.mode as mode,' . $value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,tblunits.unit as unit_name,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all,tblpurchases_items.id_plan as id_plan,tbl_productions_plan.reference_no as code_plan,tblpurchases_items.id as idd,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide');
                    } else {
                        $this->db->select($value['table'] . '.price_sell as price,' . $value['table'] . '.images as avatar,' . $value['table'] . '.mode as mode,' . $value['table'] . '.code as code_item,' . $value['table'] . '.id,' . $value['table'] . '.name,' . $value['id_type'] . ' as type,tblunits.unit as unit_name,SUM(tblpurchases_items.quantity_net) as quantity_net,SUM(tblpurchases_items.quantity_create) as quantity_create,SUM(tblpurchases_items.quantity_create_all) as quantity_create_all,tblpurchases_items.id_plan as id_plan,tbl_productions_plan.reference_no as code_plan,tblpurchases_items.id as idd,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,1 as recipe,1 as paper,1 as longs,1 as wide');
                    }
                    $this->db->from('tblpurchases_items');
                }
                $this->db->group_by($value['table'] . '.id,tblpurchases_items.id_plan');
                $this->db->order_by($value['table'] . '.id', 'ASC');
                if (!empty($id_purchases)) {
                    $this->db->join($value['table'], $value['table'] . '.id = tblpurchases_items.product_id');
                    $this->db->join('tblunits', 'tblunits.unitid=' . $value['table'] . '.unit_id', 'left');
                    if ($value['type'] == 'nvl') {
                        $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $value['table'] . '.unit_payment', 'left');
                        $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $value['table'] . '.standard_unit', 'left');
                    }
                    $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id=tblpurchases_items.id_plan', 'left');
                    $this->db->where_in('tblpurchases_items.purchases_id', $id_purchases);
                    $this->db->where('tblpurchases_items.type', $value['type']);
                }
                $data = $this->db->get()->result_array();

                foreach ($data as $key_data => $value_data) {
                    $value_data = array_merge($value_data, array('type_items' => $value['type']));

                    $quantity_net = $value_data['quantity_net'] - $value_data['quantity_create'] - $value_data['quantity_create_all'];
                    $value_data['quantity_net'] = $quantity_net;
                    if ($quantity_net <= 0) {
                    } else {
                        $value_data['avatar'] = (!empty($value_data['avatar']) ? (file_exists($value_data['avatar']) ? base_url($value_data['avatar']) : (file_exists('uploads/materials/' . $value_data['avatar']) ? base_url('uploads/materials/' . $value_data['avatar']) : (file_exists('uploads/products/' . $value_data['avatar']) ? base_url('uploads/products/' . $value_data['avatar']) : (file_exists('uploads/tools_supplies/' . $value_data['avatar']) ? base_url('uploads/tools_supplies/' . $value_data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
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
        }
        return $result['result'];
    }

    //
    public function insertBatchPurchasePlans($data)
    {
        return $this->db->insert_batch('tbl_purchases_plans', $data);
    }

    public function deletePurchasePlans($purchases_id)
    {
        $this->db->where('tbl_purchases_plans.purchases_id', $purchases_id);
        return $this->db->delete('tbl_purchases_plans');
    }


    public function get_items_internal_proposal($id)
    {
        $items = get_table_where('tbl_internal_proposal_purchase_items', array('id_internal_proposal' => $id));
        foreach ($items as $key => $value) {
            if ($value['type'] == 'items') {
                $this->db->select('tblitems.name as name_item,tblitems.avatar,tblitems.code as code_item,tblunits.unit as unit,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,tblunits.unit as unit_name')->distinct();
                $this->db->from('tblitems');
                $this->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
                $this->db->where('tblitems.id', $value['id_items']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);


                $data['html'] = format_item_color($value['id_items'], $value['type'], 1);

                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key] = array_merge($items[$key], $data);
            } elseif ($value['type'] == 'nvl') {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit,exchange_unit,exchange_standard_unit,exchange_unit_payment,payment_unit.unit as unit_name_payment,stock_unit.unit as unit_name_stock,exchange_unit,exchange_standard_unit,exchange_unit_payment,recipe,paper,longs,wide,tblunits.unit as unit_name')->distinct();
                $this->db->from($table);
                // $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
                $this->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
                $this->db->where($table . '.id', $value['id_items']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);

                $data['html'] = format_item_color($value['id_items'], $value['type'], 1);
                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key] = array_merge($items[$key], $data);
            } else {
                $table = get_table_where('tbltype_items', array('type' => $value['type']), '', 'row')->table;
                $this->db->select($table . '.images as avatar,' . $table . '.name as name_item,' . $table . '.code as code_item,tblunits.unit as unit,tblunits.unit as unit,1 as exchange_unit,1 as exchange_standard_unit,1 as exchange_unit_payment,1 as recipe,1 as paper,1 as longs,1 as wide,tblunits.unit as unit_name_payment,tblunits.unit as unit_name_stock,tblunits.unit as unit_name')->distinct();
                $this->db->from($table);
                $this->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
                $this->db->where($table . '.id', $value['id_items']);
                $data = $this->db->get()->row_array();

                $data['name_item'] = str_replace('"', '', $data['name_item']);
                $data['code_item'] = str_replace('"', '', $data['code_item']);
                $data['name_item'] = str_replace("'", '', $data['name_item']);
                $data['code_item'] = str_replace("'", '', $data['code_item']);

                $data['html'] = format_item_color($value['id_items'], $value['type'], 1);
                $data['avatar_1'] = (!empty($data['avatar']) ? (file_exists($data['avatar']) ? base_url($data['avatar']) : (file_exists('uploads/materials/' . $data['avatar']) ? base_url('uploads/materials/' . $data['avatar']) : (file_exists('uploads/products/' . $data['avatar']) ? base_url('uploads/products/' . $data['avatar']) : (file_exists('uploads/tools_supplies/' . $data['avatar']) ? base_url('uploads/tools_supplies/' . $data['avatar']) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
                $items[$key] = array_merge($items[$key], $data);
            }
        }
        return $items;
    }
}
