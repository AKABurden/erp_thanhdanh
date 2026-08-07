<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Returned_goods_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrdersReturnedGoods($order_id, $returned_goods_id = 0)
    {
        $paymentDebt = "COALESCE((
            SELECT
                SUM(tbl_returned_goods.grand_total)
            FROM tbl_returned_goods
            WHERE tbl_orders.id = tbl_returned_goods.order_id AND tbl_returned_goods.handling_solution = 'debt_reduction' AND tbl_returned_goods.id != $returned_goods_id
        ), 0)";

        $totalChiOrder = "COALESCE((
            SELECT SUM(tblother_payslips.total)
            FROM tblother_payslips
            WHERE tblother_payslips.objects = 1 AND tblother_payslips.type_vouchers = 5 AND tblother_payslips.vouchers_id = tbl_orders.id AND tblother_payslips.status = 1
        ), 0)";

        $this->db->select("tbl_orders.id as id, tbl_orders.reference_no as text, tbl_orders.tax_id as tax_id, tbl_orders.discount_percent as discount_percent, tbl_orders.total_discount_direct as total_discount_direct, 0 as total_rest, 0 as payment_debt", false);
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.id', $order_id);
        $this->db->where('tbl_orders.status', 'approved');
        return $this->db->get()->row_array();
    }

    public function searchOrdersGiveReturnedGoods($q, $limit = 50, $customer_id)
    {

        $quantityDelivery = "COALESCE((
            SELECT SUM(tbl_order_items.quantity_delivery)
            FROM tbl_order_items
            WHERE tbl_order_items.order_id = tbl_orders.id
        ), 0)";

        $this->db->select("
            tbl_orders.id as id, 
            tbl_orders.reference_no as text, 
            tbl_orders.tax_id as tax_id, 
            tbl_orders.discount_percent as discount_percent, 
            tbl_orders.total_discount_direct as total_discount_direct, 
        ", false);
        $this->db->from('tbl_orders');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $q);
            $this->db->or_like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_orders.customer_id', $customer_id);
        $this->db->where('tbl_orders.status', 'approved');
        $this->db->where("$quantityDelivery > 0");
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertReturnedGoods($data)
    {
        $this->db->insert('tbl_returned_goods', $data);
        return $this->db->insert_id();
    }

    public function insertReturnedGoodsItems($data)
    {
        $this->db->insert('tbl_returned_goods_items', $data);
        return $this->db->insert_id();
    }

    public function insertBatchReturnedGoodsItems($data)
    {
        return $this->db->insert_batch('tbl_returned_goods_items', $data);
    }

    public function checkExistReturnedGoods($reference_no)
    {
        $this->db->from('tbl_returned_goods');
        $this->db->where('tbl_returned_goods.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function deleteReturnedGoodsById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_returned_goods');
    }

    public function deleteReturnedGoodsItemsByRGId($returned_goods_id)
    {
        $this->db->where('returned_goods_id', $returned_goods_id);
        return $this->db->delete('tbl_returned_goods_items');
    }

    public function searchProductsSelect2RG($q, $limit = 50)
    {
        $this->db->select('
            CONCAT(tbl_products.id, "__products") as id, 
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text, 
            tbl_products.name as item_name, 
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, tblunits.unit as unit_name, 
            tbl_products.price_sell as price_sell, 
            tbl_products.info as info, 
            CONCAT(tbl_products.category_id, "__products") as category_id
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        // $this->db->where('type_products', 'semi_products');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchItemsSelect2RG($q, $limit = 50)
    {
        $this->db->select('
            CONCAT(tblitems.id, "__items") as id, 
            CONCAT(tblitems.code, "(", tblitems.name, ")") as text, 
            tblitems.name as item_name, 
            tblitems.avatar as images, 
            tblunits.unit as unit_name, 
            tblitems.price as price_sell, 
            tblitems.info as info, 
            CONCAT(tblitems.category_id, "__items") as category_id
        ', false);
        $this->db->from('tblitems');
        $this->db->join('tblunits', 'tblitems.unit = tblunits.unitid', 'left');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tblitems.code', $q);
            $this->db->or_like('tblitems.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowReturnedGoodsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_returned_goods');
        $this->db->where('tbl_returned_goods.id', $id);
        return $this->db->get()->row_array();
    }

    public function getReturnedGoodsItems($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_returned_goods_items');
        $this->db->where('tbl_returned_goods_items.returned_goods_id', $id);
        return $this->db->get()->result_array();
    }

    public function updateReturnedGoods($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_returned_goods', $data);
    }

    public function getReturnedGoodsByOrder($order_id)
    {
        $this->db->select('
            tbl_returned_goods.id as id,
            tbl_returned_goods.date as date,
            tbl_returned_goods.reference_no as reference_no,
            tbl_returned_goods_items.item_code as item_code,
            tbl_returned_goods_items.item_name as item_name,
            tbl_returned_goods_items.quantity as quantity,
            tbl_returned_goods_items.price as price,
            tbl_returned_goods_items.amount as amount,
            tbl_returned_goods_items.note_item as note_item,
        ');
        $this->db->from('tbl_returned_goods');
        $this->db->join('tbl_returned_goods_items', 'tbl_returned_goods_items.returned_goods_id = tbl_returned_goods.id');
        $this->db->where('tbl_returned_goods.order_id', $order_id);
        return $this->db->get()->result_array();
    }
    public function increaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $items = get_table_where('tbl_returned_goods_items', array('returned_goods_id' => $id));
            $main = get_table_where('tbl_returned_goods', array('id' => $id), '', 'row');
            $count = 0;
            if ($main) {
                $date_warehouse = date('Y-m-d H:i:s');
                $date_import = $main->date;
                foreach ($items as $key => $value) {
                    $get_items = get_items($value['item_id'], $value['type_item']);
                    $localtion =  $value['localtion_id'];

                    $warehouse_id =  $value['warehouse_id'];
                    $product_id = $value['item_id'];
                    if ($value['type_item'] == 'items') {
                        $value['type_item'] = 'items';
                    } else
                    if ($value['type_item'] == 'materials') {
                        $value['type_item'] = 'nvl';
                    } else
                    if ($value['type_item'] == 'tools_supplies') {
                        $value['type_item'] = 'tools';
                    } else {
                        $value['type_item'] = 'product';
                    }
                    $type_items = $value['type_item'];
                    $pirce = $get_items->price_import;
                    $quantity = $value['quantity_stock'];
                    $quantity_unit = $value['quantity_unit'];
                    $quantity_payment = 0;
                    $lot_code = $value['lot_code'];
                    $date_sx = $value['date_sx'];
                    $date_sd = $value['date_sd'];
                    $date_use = $value['date_use'];
                    
                    $count = increaseProductQuantity_returned_bh($warehouse_id, $id, $date_warehouse, $date_import, $product_id, $quantity, $localtion, $type_items, $pirce, $value['id'],$lot_code,$date_sx,$date_sd,$date_use,$quantity_unit,$quantity_payment);
                    $this->db->update('tbl_returned_goods_items', array('id_import' => $count), array('id' => $value['id']));
                    //tăng kho tổng
                    increaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity, $type_items,$lot_code,$date_sx,$date_sd,$date_use,$quantity_unit,$quantity_payment);
                }
            }
            if ($count) {
                return true;
            }
        }
        return false;
    }
    //giảm kho phiếu nhập xóa dữ liệu trong kho
    public function decreaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $warehouse_product = get_table_where("tblwarehouse_product", array('import_id' => $id, 'type_export' => 1324));
            $this->db->delete('tblwarehouse_product', array('import_id' => $id, 'type_export' => 1324));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'], $value['localtion'], $value['product_id'], $value['quantity'], $value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'], $value['product_quantity_unit'], $value['product_quantity_payment']);
            }
        }
        return true;
    }
}
