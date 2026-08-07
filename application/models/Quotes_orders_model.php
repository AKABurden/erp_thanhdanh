<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Quotes_orders_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data = false)
    {
        if (!empty($data)) {
            $items = $data['items'];
            unset($data['items']);
            $data['prefix'] = get_option('prefix_quotes_orders');
            $data['create_by'] = get_staff_user_id();
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['date'] = to_sql_date($data['date']);

            if(!empty($data['shipping']))
            {
                $this->db->where('id', $data['shipping']);
                $shipping_client = $this->db->get('tblshipping_client')->row();
                if(!empty($shipping_client))
                {
                    $data['address_shipping'] = $shipping_client->address;
                }
            }
            $this->db->insert(db_prefix() . 'quotes_orders', $data);
            if (!empty($this->db->insert_id())) {

                $id = $this->db->insert_id();
                $assInsert = 0;
                $_data = [
                    'total' => 0, // tổng tiền gốc
                    'total_cost_trans' => 0, // tổng tiền vận chuyển
                    'total_item' => 0, // tổng số loại sản phẩm
                    'grand_total' => 0, // tổng giá trị sau chiết khấu
                    'total_quantity' => 0, // tổng số lượng sản phẩm
                    'total_discount_percent' => 0, // tổng tiền chiết khấu theo %
                    'total_discount_money' => 0, // tổng số tiền chiết khấu tiền thẳng
                ]; //Các trường tổng
                foreach ($items as $kItem => $vItem) {
                    if(!empty($vItem['id_product']) && !empty($vItem['type_items']))
                    {
                        $vItem['price'] = C_initNumber($vItem['price']);
                        $vItem['quantity'] = C_initNumber($vItem['quantity']);
                        $vItem['discount'] = C_initNumber($vItem['discount']);
                        $vItem['cost_trans'] = C_initNumber($vItem['cost_trans']);

                        $total = $vItem['price'] * $vItem['quantity'];
                        $total_cost_trans = $vItem['cost_trans'];
                        $total_quantity = $vItem['quantity'];

                        $total_discount_percent = 0;
                        $total_discount_money = 0;

                        if ($vItem['type_discount'] == 1) {
                            $total_discount_percent = ($vItem['quantity'] * $vItem['price']) * ($vItem['discount'] / 100);
                        } else if ($vItem['type_discount'] == 2) {
                            $total_discount_money = $vItem['discount'];
                        }
                        $grand_total = $total - $total_discount_percent - $total_discount_money;
                        $_data['total'] += $total;
                        $_data['total_cost_trans'] += $total_cost_trans;
                        $_data['grand_total'] += $grand_total;
                        $_data['total_quantity'] += $total_quantity;
                        $_data['total_discount_percent'] += $total_discount_percent;
                        $_data['total_discount_money'] += $total_discount_money;
                        $_data['total_item']++;

                        $vItem['total'] = $total;
                        $vItem['money_discount'] = $total_discount_money;
                        $vItem['grand_total'] = $grand_total;
                        $vItem['id_quotes_orders'] = $id;

                        $this->db->insert(db_prefix() . 'quotes_orders_items', $vItem);
                        if(!empty($this->db->insert_id()))
                        {
                            $assInsert++;
                        }
                    }

                }
                $_data['code']   =   sprintf("%06s", $id);
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'quotes_orders', $_data);

                if(!empty($id) && $assInsert >0)
                {
                    return true;
                }
            }
        }
        return false;
    }

    public function update($id, $data = false)
    {
        if (!empty($id) && !empty($data)) {
            $items = !empty($data['items']) ? $data['items'] : [];
            $items_update = !empty($data['items_update']) ? $data['items_update'] : [];
            unset($data['id']);
            unset($data['items']);
            unset($data['items_update']);

            $data['prefix'] = get_option('prefix_orders');
            $data['create_by'] = get_staff_user_id();
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['date'] = to_sql_date($data['date']);

            if(!empty($data['shipping']))
            {
                $this->db->where('id', $data['shipping']);
                $shipping_client = $this->db->get('tblshipping_client')->row();
                if(!empty($shipping_client))
                {
                    $data['address_shipping'] = $shipping_client->address;
                }
            }

            $this->db->where('id', $id);
            if($this->db->update(db_prefix() . 'quotes_orders', $data)) {

                $items_not_delete = [];
                $assInsert = 0;
                $_data = [
                    'total' => 0, // tổng tiền gốc
                    'total_cost_trans' => 0, // tổng tiền vận chuyển
                    'total_item' => 0, // tổng số loại sản phẩm
                    'grand_total' => 0, // tổng giá trị sau chiết khấu
                    'total_quantity' => 0, // tổng số lượng sản phẩm
                    'total_discount_percent' => 0, // tổng tiền chiết khấu theo %
                    'total_discount_money' => 0, // tổng số tiền chiết khấu tiền thẳng
                ]; //Các trường tổng

                foreach ($items as $kItem => $vItem) {
                    if(!empty($vItem['id_product']) && !empty($vItem['type_items'])) {
                        $vItem['price'] = C_initNumber($vItem['price']);
                        $vItem['quantity'] = C_initNumber($vItem['quantity']);
                        $vItem['discount'] = C_initNumber($vItem['discount']);
                        $vItem['cost_trans'] = C_initNumber($vItem['cost_trans']);

                        $total = $vItem['price'] * $vItem['quantity'];
                        $total_cost_trans = $vItem['cost_trans'];
                        $total_quantity = $vItem['quantity'];

                        $total_discount_percent = 0;
                        $total_discount_money = 0;

                        if ($vItem['type_discount'] == 1) {
                            $total_discount_percent = ($vItem['quantity'] * $vItem['price']) * ($vItem['discount'] / 100);
                        } else if ($vItem['type_discount'] == 2) {
                            $total_discount_money = $vItem['discount'];
                        }
                        $grand_total = $total - $total_discount_percent - $total_discount_money + $total_cost_trans;
                        $_data['total'] += $total;
                        $_data['total_cost_trans'] += $total_cost_trans;
                        $_data['grand_total'] += $grand_total;
                        $_data['total_quantity'] += $total_quantity;
                        $_data['total_discount_percent'] += $total_discount_percent;
                        $_data['total_discount_money'] += $total_discount_money;
                        $_data['total_item']++;

                        $vItem['total'] = $total;
                        $vItem['money_discount'] = $total_discount_money;
                        $vItem['grand_total'] = $grand_total;
                        $vItem['id_quotes_orders'] = $id;

                        $this->db->where('id', $vItem['id_product']);
                        $inItems = $this->db->get(db_prefix() . 'items')->row();
                        $vItem['name_product'] = $inItems->name;
                        $this->db->insert(db_prefix() . 'quotes_orders_items', $vItem);
                        if ($this->db->insert_id()) {
                            $items_not_delete[] = $this->db->insert_id();
                            $assInsert++;
                        }
                    }

                }

                foreach ($items_update as $kUpdate => $vUpdate) {
                    if(!empty($vUpdate['id_product'])) {
                        if (empty($vUpdate['id'])) {
                            continue;
                        }

                        $vUpdate['price'] = C_initNumber($vUpdate['price']);
                        $vUpdate['quantity'] = C_initNumber($vUpdate['quantity']);
                        $vUpdate['discount'] = C_initNumber($vUpdate['discount']);
                        $vUpdate['cost_trans'] = C_initNumber($vUpdate['cost_trans']);

                        $total = $vUpdate['price'] * $vUpdate['quantity'];
                        $total_cost_trans = $vUpdate['cost_trans'];
                        $total_quantity = $vUpdate['quantity'];
                        $total_discount_percent = 0;
                        $total_discount_money = 0;

                        if ($vUpdate['type_discount'] == 1) {
                            $total_discount_percent = ($vUpdate['quantity'] * $vUpdate['price']) * ($vUpdate['discount'] / 100);
                        } else if ($vUpdate['type_discount'] == 2) {
                            $total_discount_money = $vUpdate['discount'];
                        }
                        $grand_total = $total - $total_discount_percent - $total_discount_money + $total_cost_trans;

                        $_data['total'] += $total;
                        $_data['total_cost_trans'] += $total_cost_trans;
                        $_data['grand_total'] += $grand_total;
                        $_data['total_quantity'] += $total_quantity;
                        $_data['total_discount_percent'] += $total_discount_percent;
                        $_data['total_discount_money'] += $total_discount_money;
                        $_data['total_item']++;

                        $vUpdate['total'] = $total;
                        $vUpdate['money_discount'] = $total_discount_money;
                        $vUpdate['grand_total'] = $grand_total;
                        $vUpdate['id_quotes_orders'] = $id;

                        $this->db->where('id', $vUpdate['id']);
                        if ($this->db->update(db_prefix() . 'quotes_orders_items', $vUpdate)) {
                            $items_not_delete[] = $vUpdate['id'];
                            $assInsert++;
                        }
                    }
                }

                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'quotes_orders', $_data);
                if(!empty($items_not_delete))
                {
                    $this->db->where_not_in('id', $items_not_delete);
                }
                $this->db->where('id_quotes_orders', $id);
                $this->db->delete(db_prefix() . 'quotes_orders_items');

                if($assInsert >0)
                {
                    return true;
                }
            }
        }
        return false;
    }

    // lấy data
    public function get($id = "")
    {
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $quotes_orders = $this->db->get(db_prefix().'quotes_orders')->row();
            if(!empty($quotes_orders))
            {
                $this->db->select(
                    db_prefix().'quotes_orders_items.*,
                    IF(tblquotes_orders_items.type_items = "items", '.db_prefix().'items.name, tbl_products.name) as name,
                    IF(tblquotes_orders_items.type_items = "items", tblitems.avatar, CONCAT("uploads/products/", "", tbl_products.images, "")) as avatar,
                    IF(tblquotes_orders_items.type_items = "items", '.db_prefix().'items.code, tbl_products.code) as code_items
                ');
                $this->db->where('id_quotes_orders', $id);
                $this->db->join(db_prefix().'items', db_prefix().'items.id = '.db_prefix().'quotes_orders_items.id_product AND tblquotes_orders_items.type_items = "items"', 'left');

                $this->db->join('tbl_products', 'tbl_products.id = '.db_prefix().'quotes_orders_items.id_product AND tblquotes_orders_items.type_items = "products"', 'left');
                $quotes_orders->detail = $this->db->get(db_prefix().'quotes_orders_items')->result();
                return $quotes_orders;
            }
        }
        return false;
    }

    //Get dùng để view modal
    public function get_view($id = "")
    {
        if(!empty($id))
        {
            $this->db->select(db_prefix().'quotes_orders.*,'.db_prefix().'clients.company');
            $this->db->where('id', $id);
            $this->db->join(db_prefix().'clients', db_prefix().'clients.userid = '.db_prefix().'quotes_orders.client');
            $quotes_orders = $this->db->get(db_prefix().'quotes_orders')->row();
            if(!empty($quotes_orders))
            {
                $this->db->select(
                    db_prefix().'quotes_orders_items.*,
                    IF(tblquotes_orders_items.type_items = "items", '.db_prefix().'items.name, tbl_products.name) as name,
                    IF(tblquotes_orders_items.type_items = "items", tblitems.avatar, CONCAT("uploads/products/", "", tbl_products.images, "")) as avatar,
                    IF(tblquotes_orders_items.type_items = "items", '.db_prefix().'items.code, tbl_products.code) as code_items
                ');
                $this->db->where('id_quotes_orders', $id);
                $this->db->join(db_prefix().'items', db_prefix().'items.id = '.db_prefix().'quotes_orders_items.id_product AND tblquotes_orders_items.type_items = "items"', 'left');

                $this->db->join('tbl_products', 'tbl_products.id = '.db_prefix().'quotes_orders_items.id_product AND tblquotes_orders_items.type_items = "products"', 'left');
                $quotes_orders->detail = $this->db->get(db_prefix().'quotes_orders_items')->result();
                return $quotes_orders;
            }
        }
        return false;
    }

}
