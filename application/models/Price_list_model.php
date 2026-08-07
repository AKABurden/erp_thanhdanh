<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Price_list_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRowPriceList($year, $category_products_id, $customers_groups_id) {
        $this->db->select('*');
        $this->db->from('tbl_price_list');
        $this->db->where('tbl_price_list.year', $year);
        $this->db->where('tbl_price_list.category_products_id', $category_products_id);
        $this->db->where('tbl_price_list.customers_groups_id', $customers_groups_id);
        return $this->db->get()->row_array();
    }

    public function insertPriceList($data) {
        $this->db->insert('tbl_price_list', $data);
        return $this->db->insert_id();
    }

    public function updatePriceList($id, $data) {
        $this->db->where('tbl_price_list.id', $id);
        return $this->db->update('tbl_price_list', $data);
    }

    public function showPrice($item_id, $customers_price_list, $year) {
        $data = [];
        $arr_item_id = explode('__', $item_id);
        $arr_customers_price_list = explode('__', $customers_price_list);
        $year = !empty($year) ? date_format(date_create(to_sql_date($year, true)), 'Y') : date('Y');
        $item_id = $arr_item_id[0];
        $type_item = $arr_item_id[1];
        if ($type_item != 'products' || empty($customers_price_list)) {
            $data['result'] = 0;
            $data['price'] = 0;
        } else {
            $customer_id = $arr_customers_price_list[1];

            $this->db->select('
                tbl_products.category_id as category_id,
                tbl_products.price_sell as price_sell
            ', false);
            $this->db->from('tbl_products');
            $this->db->where('tbl_products.id', $item_id);
            $products = $this->db->get()->row_array();
            $category_id = $products['category_id'];

            $this->db->select('*');
            $this->db->from('tblcustomer_groups');
            $this->db->where('tblcustomer_groups.customer_id', $customer_id);
            $customer_groups = $this->db->get()->result_array();
            $arrGroupId = [];
            if (!empty($customer_groups)) {
                foreach ($customer_groups as $key => $value) {
                    $arrGroupId[] = $value['groupid'];
                }
            } else {
                $arrGroupId = [0];
            }

            $this->db->select('tbl_price_list.*');
            $this->db->from('tbl_price_list');
            $this->db->where('tbl_price_list.category_products_id', $category_id);
            $this->db->where_in('tbl_price_list.customers_groups_id', $arrGroupId);
            $this->db->where_in('tbl_price_list.year', $year);
            $this->db->order_by('tbl_price_list.price desc');
            $this->db->limit(1);
            $price_list = $this->db->get()->row_array();
            $price = $products['price_sell'];
            if (!empty($price_list)) {
                $price = $price_list['price'];
            }

            $data['result'] = 1;
            $data['price'] = $price;
        }
        return $data;
    }
}