<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_revenue extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('rest');

        // $headers = $_SERVER;
        // $key_web = !empty($headers['HTTP_KW']) ? $headers['HTTP_KW'] : '';
        // if ($key_web != $this->config->item('api_key_web')) {
        //     return $this->response([], REST_Controller::HTTP_OK);
        //     die;
        // }
    }


    public function index_get()
	{
        $this->db->select('
            tbl_category_products.*,
        ');
        $this->db->from('tbl_category_products');
        $category_products = $this->db->get()->result();
        $this->response($category_products, REST_Controller::HTTP_OK);

        // if(!empty($id)){
        //     $data = $this->db->get_where("items", ['id' => $id])->row_array();
        // }else{
        //     $data = $this->db->get("items")->result();
        // }
     
        // $this->response($data, REST_Controller::HTTP_OK);
	}

    public function index_1_get() {
        echo 123;
    }

    public function getCategoryProducts() {

        $order_items = "(
            SELECT
                tbl_products.category_id as category_id,
                SUM(tbl_order_items.total_amount) as total_amount
            FROM tbl_order_items
            INNER JOIN tbl_products ON tbl_products.id = tbl_order_items.item_id
            GROUP BY tbl_products.category_id
            LIMIT 1000
        ) tb_order_items";

        $this->db->select('
            tbl_category_products.*,
            tb_order_items.total_amount as total_amount
        ');
        $this->db->from('tbl_category_products');
        $this->db->join($order_items, 'tb_order_items.category_id = tbl_category_products.id');
        $this->db->where('tb_order_items.total_amount >', 0);
        $this->db->limit(200);
        $category_products = $this->db->get()->result_array();

        $body = '';
        foreach ($category_products as $key => $value) {
            $body.= '<tr>
                <td>'.$value['id'].'</td>
                <td>'.$value['code'].'</td>
                <td>'.$value['name'].'</td>
                <td>'.$value['note'].'</td>
                <td>'.$value['total_amount'].'</td>
            </tr>';
        }

        $html = '<table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>code</th>
                    <th>name</th>
                    <th>note</th>
                    <th>total_amount</th>
                </tr>
            </thead>
            <tbody>'.$body.'</tbody>
        </table>';
        echo $html;
        die;
    }

    public function getRevenueStaff() {

        $order = "(
            SELECT
                tbl_orders.created_by as staff_id,
                SUM(tbl_orders.grand_total) as grand_total,
                SUM(tbl_orders.total_tax) as total_tax,
                COUNT(tbl_orders.id) as count_order
            FROM tbl_orders
            GROUP BY tbl_orders.created_by
        ) tb_order";

        $this->db->select('
            tblstaff.staffid as staffid,
            tblstaff.code as code,
            tblstaff.firstname as firstname,
            tblstaff.lastname as lastname,
            tb_order.grand_total as grand_total,
            tb_order.total_tax as total_tax,
            tb_order.count_order as count_order,
        ', false);
        $this->db->from('tblstaff');
        $this->db->join($order, 'tb_order.staff_id = tblstaff.staffid', 'inner');
        $this->db->limit(200);
        $staffs = $this->db->get()->result_array();

        $body = '';
        foreach ($staffs as $key => $value) {
            $body.= '<tr>
                <td>'.$value['staffid'].'</td>
                <td>'.$value['code'].'</td>
                <td>'.$value['firstname'].' '.$value['lastname'].'</td>
                <td>'.$value['grand_total'].'</td>
                <td>'.$value['total_tax'].'</td>
                <td>'.$value['grand_total'] - $value['total_tax'].'</td>
                <td>'.$value['count_order'].'</td>
            </tr>';
        }

        $html = '<table>
            <thead>
                <tr>
                    <th>tblstaff.id</th>
                    <th>tblstaff.code</th>
                    <th>tblstaff.fullname</th>
                    <th>tblstaff.grand_total</th>
                    <th>tblstaff.total_tax</th>
                    <th>tblstaff.total</th>
                    <th>tblstaff.count_order</th>
                </tr>
            </thead>
            <tbody>'.$body.'</tbody>
        </table>';
        echo $html;
        die;
    }

    public function getTotalRevenue() {
        // $this->db->select('
        //     COUNT(tbl_orders.id) as count_orders,
        //     SUM(tbl_orders.grand_total) as grand_total,
        //     SUM(tbl_orders.total_tax) as total_tax,
        // ', false);
        // $this->db->from('tbl_orders');
        // $orders = $this->db->get()->row_array();        
    }

    public function getCategoryProductsNew() {
        $this->db->select('
            tbl_category_products.*,
        ');
        $this->db->from('tbl_category_products');
        $category_products = $this->db->get()->result();
        $this->response($category_products, REST_Controller::HTTP_OK);
    }

    public function getStaffs() {
        $this->db->select('
            tblstaff.staffid as staffid,
            tblstaff.firstname as firstname,
            tblstaff.lastname as lastname,
            CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname
        ');
        $this->db->from('tblstaff');
        $staffs = $this->db->get()->result();
        $this->response($staffs, REST_Controller::HTTP_OK);
    }

    public function getDataDeliveryItems() {
        $this->db->select('
            tbl_deliveries.id as id_delivery,
            tbl_delivery_items.total_amount as total_amount,
            tbl_products.id as id_product,
            tbl_products.category_id as id_category,
            tbl_deliveries.created_by as created_by,
            tbl_deliveries.customer_id as customer_id,
            tbl_deliveries.order_id as order_id,
            tbl_deliveries.date as date_delivery
        ');
        $this->db->from('tbl_deliveries');
        $this->db->join('tbl_delivery_items', 'tbl_delivery_items.delivery_id = tbl_deliveries.id', 'inner');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_delivery_items.item_id', 'inner');
        // $this->db->limit(2000);
        $delivery_items = $this->db->get()->result();
        $this->response($delivery_items, REST_Controller::HTTP_OK);
    }

    public function getClients() {
        $this->db->select('
            tblclients.userid as userid,
            tblclients.zcode as zcode,
            tblclients.company as company,
            tblclients.vat as vat
        ');
        $this->db->from('tblclients');
        $clients = $this->db->get()->result_array();
        $this->response($clients, REST_Controller::HTTP_OK);
    }

    public function getOrders() {
        $this->db->select('
            tbl_orders.id as order_id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            tbl_orders.employee_id as employee_id,
            tbl_orders.grand_total as grand_total,
            tbl_orders.total_tax as total_tax,
            tbl_orders.type_orders as type_orders,
            tbl_orders.is_cancel as is_cancel,
            tbl_orders.customer_id as customer_id,
            tbl_orders.status_orders as status_orders,
            tbl_orders.total_quantity as total_quantity,
            tbl_orders.type_items as type_items
        ', false);
        $this->db->from('tbl_orders');
        $orders = $this->db->get()->result();
        $this->response($orders, REST_Controller::HTTP_OK);
    }

    public function getOrdersReturn() {
        $this->db->select('
            tbl_orders.id as order_id,
            tbl_orders.grand_total as grand_total
        ', false);
        $this->db->from('tbl_orders');
        $this->db->where(' exists (
            SELECT
                tbl_returned_goods.id
            FROM tbl_returned_goods
            WHERE tbl_returned_goods.order_id = tbl_orders.id
        )', false, false);
        $orders_return = $this->db->get()->result();
        $this->response($orders_return, REST_Controller::HTTP_OK);
    }

    public function getReturnedGoods() {

        $this->db->select('
            tbl_returned_goods.id as returned_goods_id,
            tbl_returned_goods.date as date_return,
            tbl_returned_goods.reference_no as reference_no,
            tbl_returned_goods.customer_id as customer_id,
            tbl_returned_goods.employee_id as employee_id,
            tbl_returned_goods.handling_solution as handling_solution,
            tbl_returned_goods.count_items as count_items,
            tbl_returned_goods.total_quantity as total_quantity,
            tbl_returned_goods.grand_total as grand_total,
            tbl_returned_goods.total_tax as total_tax,
            tbl_returned_goods.order_id as order_id,
            tbl_orders.grand_total as grand_total_order
        ', false);
        $this->db->from('tbl_returned_goods');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_returned_goods.order_id');
        $returned_goods = $this->db->get()->result();
        $this->response($returned_goods, REST_Controller::HTTP_OK);
    }

    public function getReturnedGoodsItems() {
        $this->db->select('
            tbl_returned_goods_items.id as returned_goods_items_id,
            tbl_returned_goods_items.returned_goods_id as returned_goods_id,  
            tbl_returned_goods_items.order_item_id as order_item_id,  
            tbl_returned_goods_items.item_id as item_id,  
            tbl_returned_goods_items.quantity as quantity,  
            tbl_returned_goods_items.total_amount as total_amount,  
        ', false);
        $this->db->from('tbl_returned_goods_items');
        $returned_goods_items = $this->db->get()->result();
        $this->response($returned_goods_items, REST_Controller::HTTP_OK);
    }

    public function getQuotes() {

        $tbQuotesSubOrdersSample = "(
            SELECT
                tbl_orders_sub.quote_id_chonse as quote_id_chonse,
                COUNT(tbl_orders_sub.order_id) as count_develop
            FROM tbl_orders_sub
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
            WHERE tbl_orders.type_orders = ".TYPE_SAMPLE_ORDER."
            GROUP BY tbl_orders_sub.quote_id_chonse
        ) tb_order_sub";

        // $tbQuotesSubOrdersKH = "(
        //     SELECT

        //     FROM tbl_orders_sub
        //     INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
        //     WHERE tbl_orders.type_orders = 11 AND tbl_orders_sub.
        //     GROUP BY tbl_orders_sub.quote_id_chonse
        // ) tb_order_sub_kh";

        // $maudatCDDonHang = "(
        //     SELECT
        //         tbl_orders_sub.quote_id_chonse as quote_id_chonse,
        //         1 as is_mau_dat_cd_don_hang
        //     FROM tbl_orders_sub
        //     INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
        //     INNER JOIN tbl_orders order_sub ON order_sub.quotes_id = tbl_orders_sub.quote_id_chonse
        //     WHERE tbl_orders.type_orders = 11 AND tbl_orders_sub.quote_id_chonse != 0 AND order_sub.type_orders = 1
        //     GROUP BY tbl_orders_sub.quote_id_chonse
        // ) tbl_mau_1";

        $maudatCDDonHang = "(
            SELECT
                tbl_orders_sub.quote_id_chonse as quote_id_chonse,
                1 as is_mau_dat_cd_don_hang
            FROM tbl_orders_sub
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
            WHERE tbl_orders.type_orders = ".TYPE_KH_ORDER." AND tbl_orders_sub.quote_id_chonse != 0
            GROUP BY tbl_orders_sub.quote_id_chonse
        ) tbl_mau_1";

        $maudatKoCDDonHang = "(
            SELECT
                tbl_orders_sub.quote_id_chonse as quote_id_chonse,
                1 as is_mau_dat_ko_cd_don_hang
            FROM tbl_orders_sub
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_orders_sub.order_id
            WHERE tbl_orders.type_orders = 11 AND tbl_orders_sub.quote_id_chonse != 0 AND (
                not exists (
                    SELECT tbl_orders.id
                    FROM tbl_orders
                    WHERE tbl_orders.quotes_id = tbl_orders_sub.quote_id_chonse AND tbl_orders.type_orders = 1
                )
            )
            GROUP BY tbl_orders_sub.quote_id_chonse
        ) tbl_mau_2";
        
        // COALESCE(tbl_mau_1.is_mau_dat_cd_don_hang, 0) as maudatCDDonHang,
        // COALESCE(tbl_mau_2.is_mau_dat_ko_cd_don_hang, 0) as maudatKoCDDonHang,
        $this->db->select('
            tbl_quotes.id as quote_id,
            tbl_quotes.customer_id as customer_id,
            tbl_quotes.grand_total as grand_total,
            tbl_quotes.total_tax as total_tax,
            tbl_quotes.created_by as created_by,
            tbl_quotes.order_id as order_id,
            tbl_quotes.status as status,
            tbl_quotes.date as date,
            COALESCE(tb_order_sub.count_develop, 0) as count_develop,
            IF (coalesce(tbl_orders_sub.is_pass_fail, 0) = 1 AND COALESCE(tbl_mau_1.is_mau_dat_cd_don_hang, 0) = 1, 1, 0) as maudatCDDonHang,
            IF (coalesce(tbl_orders_sub.is_pass_fail, 0) = 1 AND COALESCE(tbl_mau_1.is_mau_dat_cd_don_hang, 0) = 0, 1, 0) as maudatKoCDDonHang,
            COALESCE(tbl_mau_1.is_mau_dat_cd_don_hang, 0) as is_ddh
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_orders', 'tbl_orders.quotes_id = tbl_quotes.id', 'left');
        $this->db->join('tbl_orders_sub', 'tbl_orders_sub.order_id = tbl_orders.id', 'left');
        $this->db->join($tbQuotesSubOrdersSample, 'tb_order_sub.quote_id_chonse = tbl_quotes.id', 'left');
        $this->db->join($maudatCDDonHang, 'tbl_mau_1.quote_id_chonse = tbl_quotes.id', 'left');
        // $this->db->join($maudatKoCDDonHang, 'tbl_mau_2.quote_id_chonse = tbl_quotes.id', 'left');
        $quotes = $this->db->get()->result();
        $this->response($quotes, REST_Controller::HTTP_OK);
    }

    public function getOrdersQuotes() {
        $this->db->select('
            tbl_orders.id as order_id,
            tbl_orders.date as date,
            tbl_orders.reference_no as reference_no,
            tbl_orders.employee_id as employee_id,
            tbl_orders.grand_total as grand_total,
            tbl_orders.total_tax as total_tax,
            tbl_orders.quotes_id as quotes_id,
            tbl_orders.type_orders as type_orders,
            tbl_orders.status_orders as status_orders,
            coalesce(tbl_orders_sub.is_pass_fail, 0) as is_pass_fail
        ', false);
        $this->db->from('tbl_orders');
        $this->db->join('tbl_orders_sub', 'tbl_orders_sub.order_id = tbl_orders.id', 'left');
        $this->db->where('tbl_orders.quotes_id !=', 0);
        $quotes_orders = $this->db->get()->result();
        $this->response($quotes_orders, REST_Controller::HTTP_OK);
    }

    public function getQuoteItems() {
        $this->db->select('
            tbl_quote_items.id as quote_item_id,
            tbl_quote_items.quote_id as quote_id,
            tbl_quote_items.item_id as item_id,
            tbl_quote_items.unit_price as unit_price,
            tbl_quote_items.moq as moq,
            tbl_quote_items.moq_to as moq_to,
            tbl_quote_items.quantity as quantity,
        ', false);
        $this->db->from('tbl_quote_items');
        $quote_items = $this->db->get()->result();
        $this->response($quote_items, REST_Controller::HTTP_OK);
    }

    public function getOrderItems() {
        $this->db->select('
            tbl_order_items.id as order_item_id,
            tbl_order_items.order_id as order_id,
            tbl_order_items.item_id as item_id,
            tbl_order_items.quantity as quantity,
            tbl_order_items.price as price,
            tbl_order_items.amount as amount,
            tbl_order_items.total_amount as total_amount,
            tbl_order_items.quantity_loss as quantity_loss,
            tbl_order_items.sample_quantity as sample_quantity,
            tbl_order_items.total_quantity_item as total_quantity_item,
            tbl_order_items.quantity_delivery as quantity_delivery,
            tbl_order_item_shippings.date_shipping as date_shipping
        ', false);
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
        $quote_items = $this->db->get()->result();
        $this->response($quote_items, REST_Controller::HTTP_OK);
    }

    public function getStatusOrders() {
        $this->db->select('
            tbl_status_orders.id as id,
            tbl_status_orders.code as code,
            tbl_status_orders.name as name,
        ');
        $this->db->from('tbl_status_orders');
        $status_orders = $this->db->get()->result();
        $this->response($status_orders, REST_Controller::HTTP_OK);
    }

    public function getTypeItems() {
        $data = [
            [
                'id' => 0,
                'name' => 'Chưa xác định',
            ],
            [
                'id' => 1,
                'name' => 'Cố định',
            ],
            [
                'id' => 2,
                'name' => 'Thay đổi',
            ]
        ];
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function getOrdersRelationshipManu() {
        $this->db->select('
            tbl_orders_relationship.*,
            tbl_productions_orders.reference_no as reference_no
        ', false);
        $this->db->from('tbl_orders_relationship');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_orders_relationship.object_id');
        $this->db->where('tbl_orders_relationship.type_relationship', 1);
        $orders_relationship_manu = $this->db->get()->result();
        if (empty($orders_relationship_manu)) {
            $orders_relationship_manu[] = [
                'order_id' => "0",
                'type_relationship' => "1",
                'object_id' => "0",
                'reference_no' => '',
            ];
        }
        $this->response($orders_relationship_manu, REST_Controller::HTTP_OK);
    }

    public function getOrdersRelationshipOrder() {
        $this->db->select('
            tbl_orders_relationship.*,
            tbl_orders.reference_no as reference_no
        ', false);
        $this->db->from('tbl_orders_relationship');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_orders_relationship.object_id');
        $this->db->where('tbl_orders_relationship.type_relationship', 2);
        $orders_relationship_order = $this->db->get()->result();
        if (empty($orders_relationship_order)) {
            $orders_relationship_order[] = [
                'order_id' => "0",
                'type_relationship' => "2",
                'object_id' => "0",
                'reference_no' => "",
            ];
        }
        $this->response($orders_relationship_order, REST_Controller::HTTP_OK);
    }

    public function getTrouble() {
        $this->db->select('
            tbltrouble.id as id,
            tbltrouble.code as code,
            tbltrouble.name as name
        ');
        $this->db->from('tbltrouble');
        $trouble = $this->db->get()->result();
        $this->response($trouble, REST_Controller::HTTP_OK);
    }

    public function getProductionReportOrders() {
        $this->db->select('
            tblproduction_report.*
        ', false);
        $this->db->from('tblproduction_report');
        $this->db->group_start();
        $this->db->where('tblproduction_report.id_orders !=', 0);
        $this->db->or_where(' exists (
            SELECT tbl_productions_orders_items.id
            FROM tbl_productions_orders_items
            WHERE tbl_productions_orders_items.productions_orders_id = tblproduction_report.id_production_orders AND tbl_productions_orders_items.object_item_type = "orders"
        )', false, false);
        $this->db->or_where(' exists (
            SELECT tbl_productions_orders_details.id
            FROM tbl_productions_orders_details
            WHERE tbl_productions_orders_details.id = tblproduction_report.id_production_detail AND tbl_productions_orders_details.object_type = "orders"
        )', false, false);
        $this->db->group_end();
        $production_report = $this->db->get()->result();
        $this->response($production_report, REST_Controller::HTTP_OK);
    }
}