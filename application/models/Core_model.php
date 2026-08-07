<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Core_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProductionsOrdersItemsAndPriceArrByPOId($arrId = [], $key = '', $is_multiple = false) {
        if (empty($arrId)) return false;
        $strPOId = implode(',', $arrId);

        $query = "
            SELECT 
                tbl_productions_orders_items.productions_orders_id as po_id,
                tbl_products.code as code,
                tbl_products.name as name,
                tbl_order_items.price as price

            FROM tbl_productions_orders_items
            INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            WHERE tbl_productions_orders_items.productions_orders_id IN ($strPOId) AND tbl_productions_orders_items.object_item_type = 'orders'

            UNION ALL

            SELECT 
                tbl_productions_orders_items.productions_orders_id as po_id,
                tbl_products.code as code,
                tbl_products.name as name,
                (
                    SELECT
                        MAX(tblgroup_price_detail.price)
                    FROM tblgroup_price_detail
                    WHERE tblgroup_price_detail.product_type = 'product' AND tblgroup_price_detail.product_id = tbl_productions_orders_items.items_id
                ) as price

            FROM tbl_productions_orders_items
            INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
            WHERE tbl_productions_orders_items.productions_orders_id IN ($strPOId) AND tbl_productions_orders_items.object_item_type = 'business_plan'
        ";
        $items = $this->db->query($query)->result_array();
        if (!empty($key)) {
            $items = array_reduce($items, function ($carry, $item) use ($key, $is_multiple) {
                $id = $item[$key];
                if ($is_multiple) {
                    $carry[$id][] = $item;
                } else {
                    $carry[$id] = $item;
                }
                return $carry;
            }, []);
        }

        return $items;
    }

    public function getProductionsOrdersItemsAndPriceArrByPOIdCs($arrId = [], $key = '', $is_multiple = false) {
        if (empty($arrId)) return false;
        $strPOId = implode(',', $arrId);

        $query = "
            SELECT
                tb_cs.po_id, 
                tb_cs.items_id as items_id,
                tb_cs.code as code,
                tb_cs.name as name,
                MAX(tb_cs.price) as price
            FROM (
                SELECT 
                    tbl_productions_orders_items.productions_orders_id as po_id,
                    tbl_productions_orders_items.items_id as items_id,
                    tbl_products.code as code,
                    tbl_products.name as name,
                    tbl_order_items.price as price

                FROM tbl_productions_orders_items
                INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id
                INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
                WHERE tbl_productions_orders_items.productions_orders_id IN ($strPOId) AND tbl_productions_orders_items.object_item_type = 'orders'

                UNION ALL

                SELECT 
                    tbl_productions_orders_items.productions_orders_id as po_id,
                    tbl_productions_orders_items.items_id as items_id,
                    tbl_products.code as code,
                    tbl_products.name as name,
                    (
                        SELECT
                            MAX(tblgroup_price_detail.price)
                        FROM tblgroup_price_detail
                        WHERE tblgroup_price_detail.product_type = 'product' AND tblgroup_price_detail.product_id = tbl_productions_orders_items.items_id
                    ) as price

                FROM tbl_productions_orders_items
                INNER JOIN tbl_products ON tbl_products.id = tbl_productions_orders_items.items_id
                WHERE tbl_productions_orders_items.productions_orders_id IN ($strPOId) AND tbl_productions_orders_items.object_item_type = 'business_plan'
            ) tb_cs
            GROUP BY tb_cs.po_id, tb_cs.items_id
        ";
        $items = $this->db->query($query)->result_array();
        if (!empty($key)) {
            $items = array_reduce($items, function ($carry, $item) use ($key, $is_multiple) {
                $id = $item[$key];
                if ($is_multiple) {
                    $carry[$id][] = $item;
                } else {
                    $carry[$id] = $item;
                }
                return $carry;
            }, []);
        }

        return $items;
    }
}