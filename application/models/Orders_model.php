<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
		$this->is_branch = true;
    }

    public function add($data = false)
    {
        if (!empty($data)) {
            $items = !empty($data['items']) ? $data['items'] : [];
            unset($data['items']);
            if(empty($items)) {
                return false;
            }
            $data['prefix'] = get_option('prefix_orders');
            $data['create_by'] = get_staff_user_id();
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['date'] = to_sql_date($data['date']);
            $data['guest_giving'] = C_initNumber($data['guest_giving']);

            if(!empty($data['shipping']))
            {
                $this->db->where('id', $data['shipping']);
                $shipping_client = $this->db->get('tblshipping_client')->row();
                if(!empty($shipping_client))
                {
                    $data['address_shipping'] = $shipping_client->address;
                }
            }


            $this->db->insert(db_prefix() . 'orders', $data);
            if (!empty($this->db->insert_id())) {
                $id = $this->db->insert_id();
                if(!empty($data['id_quotes_orders']))
                {
                    $this->db->where('id', $data['id_quotes_orders']);
                    $this->db->update('tblquotes_orders', ['status' => 1]);
                }
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
                        if(!empty($vItem['shipping']))
                        {
                            $Shipping_orders =  $vItem['shipping'];
                            unset($vItem['shipping']);
                        }

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
                        $vItem['id_orders'] = $id;

                        if ($vItem['type_items'] == "items")
                        {
                            $this->db->where('id', $vItem['id_product']);
                            $inItems = $this->db->get(db_prefix().'items')->row();
                            $vItem['name_product'] = $inItems->name;
                            $vItem['code_product'] = $inItems->code;
                        } else if ($vItem['type_items'] == "products") {
                            $inItems = $this->products_model->rowProduct($vItem['id_product']);
                            $vItem['name_product'] = $inItems['name'];
                            $vItem['code_product'] = $inItems['code'];
                        }

                        if (empty($inItems)) continue;

                        $this->db->insert(db_prefix() . 'orders_items', $vItem);
                        if(!empty($this->db->insert_id()))
                        {
                            //Thêm chi tiết giao hàng dự kiến
                            if(!empty($Shipping_orders))
                            {
                                $id_detail = $this->db->insert_id();
                                foreach($Shipping_orders as $key => $value)
                                {
                                    if(!empty($value['date_shipping']) && !empty($value['quantity_shipping']))
                                    {
                                        $this->db->insert('tblorders_detail_shipping', [
                                            'id_orders' => $id,
                                            'id_detail' => $id_detail,
                                            'id_product' => $vItem['id_product'],
                                            'date_shipping' => to_sql_date($value['date_shipping']),
                                            'quantity_shipping' => $value['quantity_shipping']
                                        ]);
                                    }
                                }
                            }
                            $assInsert++;
                        }
                    }

                }
                $_data['code']   =   sprintf("%06s", $id);
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'orders', $_data);
                if(!empty($id) && $assInsert > 0)
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
            $data['guest_giving'] = C_initNumber($data['guest_giving']);

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
            if($this->db->update(db_prefix() . 'orders', $data)) {

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

                        $Shipping_orders = $vItem['shipping'];
                        unset($vItem['shipping']);
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
                        $vItem['id_orders'] = $id;

                        if ($vItem['type_items'] == "items")
                        {
                            $this->db->where('id', $vItem['id_product']);
                            $inItems = $this->db->get(db_prefix().'items')->row();
                            $vItem['name_product'] = $inItems->name;
                            $vItem['code_product'] = $inItems->code;
                        } else if ($vItem['type_items'] == "products") {
                            $inItems = $this->products_model->rowProduct($vItem['id_product']);
                            $vItem['name_product'] = $inItems['name'];
                            $vItem['code_product'] = $inItems['code'];
                        }
                        if (empty($inItems)) continue;

                        $this->db->insert(db_prefix() . 'orders_items', $vItem);
                        if ($this->db->insert_id()) {
                            $items_not_delete[] = $this->db->insert_id();
                            //Thêm chi tiết giao hàng dự kiến
                            if(!empty($Shipping_orders))
                            {
                                $id_detail = $this->db->insert_id();
                                foreach($Shipping_orders as $key => $value)
                                {
                                    if(!empty($value['date_shipping']) && !empty($value['quantity_shipping']))
                                    {
                                        $this->db->insert('tblorders_detail_shipping', [
                                            'id_orders' => $id,
                                            'id_detail' => $id_detail,
                                            'id_product' => $vItem['id_product'],
                                            'date_shipping' => to_sql_date($value['date_shipping']),
                                            'quantity_shipping' => $value['quantity_shipping']
                                        ]);
                                    }
                                }
                            }
                            $assInsert++;
                        }
                    }

                }

                foreach ($items_update as $kUpdate => $vUpdate) {
                    if(!empty($vUpdate['id_product'])) {
                        if (empty($vUpdate['id'])) {
                            continue;
                        }
                        $Shipping_orders = $vUpdate['shipping'];
                        unset($vUpdate['shipping']);

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
                        $vUpdate['id_orders'] = $id;

                        $this->db->where('id', $vUpdate['id']);
                        if ($this->db->update(db_prefix() . 'orders_items', $vUpdate)) {
                            $items_not_delete[] = $vUpdate['id'];
                            //Thêm mới chi tiết giao hàng dự kiến
                            if(!empty($Shipping_orders))
                            {
                                $array_delete_shipping = [];
                                $id_detail = $vUpdate['id'];
                                foreach($Shipping_orders as $key => $value)
                                {
                                    if(!empty($value['date_shipping']) && !empty($value['quantity_shipping']))
                                    {
                                        if(!empty($value['id']))
                                        {
                                            $this->db->where('id', $value['id']);
                                            $updateShipping = $this->db->update('tblorders_detail_shipping', [
                                                'id_product' => $vUpdate['id_product'],
                                                'date_shipping' => to_sql_date($value['date_shipping']),
                                                'quantity_shipping' => $value['quantity_shipping']
                                            ]);
                                            if(!empty($updateShipping))
                                            {
                                                $array_delete_shipping[] =  $value['id'];
                                            }
                                        }
                                        else
                                        {
                                            $this->db->insert('tblorders_detail_shipping', [
                                                'id_detail' => $id_detail,
                                                'id_orders' => $id,
                                                'id_product' => $vUpdate['id_product'],
                                                'date_shipping' => to_sql_date($value['date_shipping']),
                                                'quantity_shipping' => $value['quantity_shipping']
                                            ]);
                                            $insertShipping = $this->db->insert_id();
                                            if(!empty($insertShipping))
                                            {
                                                $array_delete_shipping[] =  $insertShipping;
                                            }
                                        }
                                        //Xóa các shipping không tìm thấy trong cập nhật
                                        $this->db->where('id_detail', $id_detail);
                                        $this->db->where('id_orders', $id);
                                        if(!empty($array_delete_shipping))
                                        {
                                            $this->db->where_not_in('id', $array_delete_shipping);
                                        }
                                        $this->db->delete('tblorders_detail_shipping');
                                    }
                                }
                            }
                            $assInsert++;
                        }
                    }
                }

                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'orders', $_data);
                if(!empty($items_not_delete))
                {
                    $this->db->where_not_in('id', $items_not_delete);
                }
                $this->db->where('id_orders', $id);
                $this->db->delete(db_prefix() . 'orders_items');

                $this->db->where('id_orders', $id);
                if(!empty($items_not_delete))
                {
                    $this->db->where_not_in('id_detail', $items_not_delete);
                }
                $this->db->delete('tblorders_detail_shipping');

                if($assInsert >0)
                {
                    return true;
                }
            }
        }
        return false;
    }


    public function get($id = "")
    {
        if(!empty($id))
        {
            $this->db->where('id', $id);
            $orders = $this->db->get(db_prefix().'orders')->row();
            if(!empty($orders))
            {
                $this->db->select(
                    db_prefix().'orders_items.*,
                    IF(tblorders_items.type_items = "items", '.db_prefix().'items.name, tbl_products.name) as name,
                    IF(tblorders_items.type_items = "items", tblitems.avatar, CONCAT("uploads/products/", "", tbl_products.images, "")) as avatar,
                    IF(tblorders_items.type_items = "items", '.db_prefix().'items.code, tbl_products.code) as code_items
                ');
                $this->db->where('id_orders', $id);
                $this->db->join(db_prefix().'items', db_prefix().'items.id = '.db_prefix().'orders_items.id_product AND tblorders_items.type_items = "items"', 'left');

                $this->db->join('tbl_products', 'tbl_products.id = '.db_prefix().'orders_items.id_product AND tblorders_items.type_items = "products"', 'left');
                $orders->detail = $this->db->get(db_prefix().'orders_items')->result();
                foreach($orders->detail as $key => $value)
                {
                    $this->db->where('id_detail', $value->id);
                    $orders->detail[$key]->shipping = $this->db->get('tblorders_detail_shipping')->result();
                }
                return $orders;
            }
        }
        return false;
    }
    public function get_view($id = "")
    {
        if(!empty($id))
        {
            $this->db->select(db_prefix().'orders.*,'.db_prefix().'clients.company_short as company');
            $this->db->where('id', $id);
            $this->db->join(db_prefix().'clients', db_prefix().'clients.userid = '.db_prefix().'orders.client');
            $orders = $this->db->get(db_prefix().'orders')->row();
            if(!empty($orders))
            {
                $this->db->select(
                    db_prefix().'orders_items.*,
                    IF(tblorders_items.type_items = "items", '.db_prefix().'items.name, tbl_products.name) as name,
                    IF(tblorders_items.type_items = "items", tblitems.avatar, CONCAT("uploads/products/", "", tbl_products.images, "")) as avatar,
                    IF(tblorders_items.type_items = "items", '.db_prefix().'items.code, tbl_products.code) as code_items
                ');
                $this->db->where('id_orders', $id);
                $this->db->join(db_prefix().'items', db_prefix().'items.id = '.db_prefix().'orders_items.id_product AND tblorders_items.type_items = "items"', 'left');

                $this->db->join('tbl_products', 'tbl_products.id = '.db_prefix().'orders_items.id_product AND tblorders_items.type_items = "products"', 'left');
                $orders->detail = $this->db->get(db_prefix().'orders_items')->result();
                foreach($orders->detail as $key => $value)
                {
                    $this->db->where('id_detail', $value->id);
                    $orders->detail[$key]->shipping = $this->db->get('tblorders_detail_shipping')->result();
                }
                return $orders;
            }
        }
        return false;
    }
    public function getClientOrder($client = "")
    {
        if(!empty($client))
        {
            $this->db->select(db_prefix().'orders.*,'.db_prefix().'clients.company_short as company');
            $this->db->where('client', $client);
            $this->db->where('status != -3');
            $this->db->join(db_prefix().'clients', db_prefix().'clients.userid = '.db_prefix().'orders.client');
            $orders = $this->db->get(db_prefix().'orders')->result();
            if(!empty($orders))
            {
                foreach($orders as $Korder => $Vorder)
                {

                    $this->db->select(
                        db_prefix().'orders_items.*,
                        IF(tblorders_items.type_items = "items", '.db_prefix().'items.name, tbl_products.name) as name,
                        IF(tblorders_items.type_items = "items", tblitems.avatar, CONCAT("uploads/products/", "", tbl_products.images, "")) as avatar,
                        IF(tblorders_items.type_items = "items", '.db_prefix().'items.code, tbl_products.code) as code_items
                    ');
                    $this->db->where('id_orders', $Vorder->id);
                    $this->db->join(db_prefix().'items', db_prefix().'items.id = '.db_prefix().'orders_items.id_product AND tblorders_items.type_items = "items"', 'left');
                    $this->db->join('tbl_products', 'tbl_products.id = '.db_prefix().'orders_items.id_product AND tblorders_items.type_items = "products"', 'left');
                    $orders[$Korder]->detail = $this->db->get(db_prefix().'orders_items')->result();
                }
            }
            return $orders;
        }
        return false;
    }

    public function get_status_orders($id = NULL, $status = 0)
    {
        $name_status = "";
        if($status > 0)
        {
            $this->db->select('id_procedure, name_procedure, name');
            $this->db->where('id_procedure', $status);
            $this->db->where('id_orders', $id);
            $this->db->join('tblprocedure_client_detail', 'tblprocedure_client_detail.id = tblorders_step.id_procedure', 'left');
            $orders_step = $this->db->get(db_prefix().'orders_step')->row();
            if(!empty($orders_step))
            {
                $name_status = !empty($orders_step->name) ? $orders_step->name : $orders_step->name_procedure.'('._l('cong_not_found').')';
            }
            else
            {
                $name_status = _l('cong_not_found');
            }
        }
        else
        {
            if($status == 0)
            {
                $name_status = _l('cong_orders_warning');
            }
            else if($status == -1)
            {
                $name_status = _l('cong_orders_success');
            }
            else if($status == -2)
            {
                $name_status = _l('cong_orders_delay');
            }
            else if($status == -3)
            {
                $name_status = _l('cong_orders_cancel');
            }
        }
        return $name_status;
    }

    public function updateOrders($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tblorders', $data);
    }

    public function updateOrdersByProductionsPlan($productions_plan_id, $data)
    {
        $this->db->where('productions_plan_id', $productions_plan_id);
        return $this->db->update('tblorders', $data);
    }

    public function updateOrdersByProductionsPlanNew($productions_plan_id, $data)
    {
        $this->db->where('productions_plan_id', $productions_plan_id);
        return $this->db->update('tbl_orders', $data);
    }

    public function insertOrders($data) {
        $this->db->insert('tblorders', $data);
        return $this->db->insert_id();
    }

    public function insertOrdersItems($data) {
        $this->db->insert('tblorders_items', $data);
        return $this->db->insert_id();
    }

    public function insertOrdersDetailShipping($data) {
        $this->db->insert('tblorders_detail_shipping', $data);
        return $this->db->insert_id();
    }

    public function insertOrdersNew($data)
    {
        $this->db->insert('tbl_orders', $data);
        $id = $this->db->insert_id();
        if (!empty($id)) {
            $staffId = $this->site_model->getAllStaffId()['staffid'];
            $this->updateOrdersNew($id, ['list_users' => $staffId]);
        }
        return $id;
    }

    public function updateOrdersNew($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_orders', $data);
    }

    public function insertOrderItemsNew($data)
    {
        $this->db->insert('tbl_order_items', $data);
        return $this->db->insert_id();
    }

    public function insertOrderItemShippingsNew($data)
    {
        $this->db->insert('tbl_order_item_shippings', $data);
        return $this->db->insert_id();
    }

    public function checkExistOrders($reference_no) {
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function deleteOrdersById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_orders');
    }

    public function deleteOrdersItemsByOrderId($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->delete('tbl_order_items');
    }

    public function deleteOrdersItemsByOrderIdNotGift($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('type_gift !=', 1);
        return $this->db->delete('tbl_order_items');
    }

    public function deleteOrderItemShippings($order_item_id)
    {
        $this->db->where('order_item_id', $order_item_id);
        return $this->db->delete('tbl_order_item_shippings');
    }

    public function rowOrderById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_orders');
        $this->db->where('id', $id);
        $order =  $this->db->get()->row_array();
		return $order;
    }

    public function getOrderItemsByOrderId($order_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_order_items');
        $this->db->where('order_id', $order_id);
        $this->db->where('type_gift !=', 1);
        return $this->db->get()->result_array();
    }

    public function getOrderItemsByOrderIdView($order_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_order_items');
        $this->db->where('order_id', $order_id);
        return $this->db->get()->result_array();
    }

    public function getOrderItemShippingsByOrderItemId($order_item_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_order_item_shippings');
        $this->db->where('tbl_order_item_shippings.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }

    public function rowOrderItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function updateOrderItemNew($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_order_items', $data);
    }

    public function getOrderItemsDelivery($order_id)
    {
        $tbDelivery = "(
            SELECT 
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity_loss) as quantity_delivery_loss,
                SUM(tbl_delivery_items.quantity_sample) as quantity_sample
            FROM tbl_delivery_items
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery";
        $this->db->select('*,COALESCE(tb_delivery.quantity_delivery_loss,0) as quantity_delivery_loss,COALESCE(tb_delivery.quantity_sample,0) as quantity_sample');
        $this->db->from('tbl_order_items');
        $this->db->join("$tbDelivery",'tb_delivery.order_item_id = tbl_order_items.id','left');
        $this->db->where('order_id', $order_id);
        $this->db->where('(tbl_order_items.total_quantity_item - (tbl_order_items.quantity_delivery + COALESCE(tb_delivery.quantity_delivery_loss,0) + COALESCE(tb_delivery.quantity_sample,0))) >', 0);
        return $this->db->get()->result_array();
    }

    public function rowOrderItemsByIdForDelivery($id)
    {
        $tbDelivery = "(
            SELECT 
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity_loss) as quantity_delivery_loss,
                SUM(tbl_delivery_items.quantity_sample) as quantity_sample
            FROM tbl_delivery_items
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_delivery";
        $this->db->select('*,COALESCE(tb_delivery.quantity_delivery_loss,0) as quantity_delivery_loss,COALESCE(tb_delivery.quantity_sample,0) as quantity_sample');
        $this->db->from('tbl_order_items');
        $this->db->join("$tbDelivery",'tb_delivery.order_item_id = tbl_order_items.id','left');
        $this->db->where('tbl_order_items.id', $id);
        $this->db->where('(tbl_order_items.total_quantity_item - (tbl_order_items.quantity_delivery + COALESCE(tb_delivery.quantity_delivery_loss,0) + COALESCE(tb_delivery.quantity_sample,0))) >', 0);
        return $this->db->get()->row_array();
    }

    public function updateQuantityOrderItems($id, $quantity, $option_id = 0)
    {
        $this->db->where(['tbl_order_items.id' => $id]);

        if ($option_id == 0)
        {
            $this->db->set('quantity_delivery', 'COALESCE(quantity_delivery, 0)+'.$quantity, FALSE);
        }
        if ($option_id == 1)
        {
            $this->db->set('quantity_delivery', 'COALESCE(quantity_delivery, 0)-'.$quantity, FALSE);
        }

        return $this->db->update('tbl_order_items');
    }

    public function updateQuantityOrder($id, $quantity, $option_id = 0)
    {
        $this->db->where(['tbl_orders.id' => $id]);
        if ($option_id == 0)
        {
            $this->db->set('total_quantity_had_delivery', 'COALESCE(total_quantity_had_delivery, 0)+'.$quantity, FALSE);
            $this->db->set('count_delivery', 'COALESCE(count_delivery, 0)+1', FALSE);
        }
        if ($option_id == 1)
        {
            $this->db->set('total_quantity_had_delivery', 'COALESCE(total_quantity_had_delivery, 0)-'.$quantity, FALSE);
            $this->db->set('count_delivery', 'COALESCE(count_delivery, 0)-1', FALSE);
        }
        return $this->db->update('tbl_orders');
    }

    public function updateQuantityOutsourceOrder($id, $quantity, $option_id = 0)
    {
        $this->db->where(['tbl_orders.id' => $id]);
        if ($option_id == 0)
        {
            $this->db->set('total_quantity_had_outsource', 'COALESCE(total_quantity_had_outsource, 0)+'.$quantity, FALSE);
            $this->db->set('count_outsource', 'COALESCE(count_outsource, 0)+1', FALSE);
        }
        if ($option_id == 1)
        {
            $this->db->set('total_quantity_had_outsource', 'COALESCE(total_quantity_had_outsource, 0)-'.$quantity, FALSE);
            $this->db->set('count_outsource', 'COALESCE(count_outsource, 0)-1', FALSE);
        }
        return $this->db->update('tbl_orders');
    }

    public function updateQuantityOutsourceOrderItems($id, $quantity, $option_id = 0)
    {
        $this->db->where(['tbl_order_items.id' => $id]);

        if ($option_id == 0)
        {
            $this->db->set('quantity_outsource', 'COALESCE(quantity_outsource, 0)+'.$quantity, FALSE);
        }
        if ($option_id == 1)
        {
            $this->db->set('quantity_outsource', 'COALESCE(quantity_outsource, 0)-'.$quantity, FALSE);
        }

        return $this->db->update('tbl_order_items');
    }

    public function rowOrderByOrderItemId($order_item_id)
    {
        $this->db->select('tbl_orders.reference_no');
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_order_items.order_id');
        $this->db->where('tbl_order_items.id', $order_item_id);
        return $this->db->get()->row_array();
    }

    public function searchTablePrices($term, $limit, $customer_id)
    {
        $date = date('Y-m-d');

        $this->db->select('tblcustomer_groups.*');
        $this->db->from('tblcustomer_groups');
        $this->db->where('tblcustomer_groups.customer_id', $customer_id);
        $customerGroups = $this->db->get()->result_array();

        $this->db->select('tbl_set_prices.id as id,  tbl_set_prices.name as text', false);
        $this->db->from('tbl_set_prices');
        $this->db->group_start();
        $this->db->like('tbl_set_prices.name', $term);
        $this->db->group_end();

        $this->db->where('tbl_set_prices.status', 1);
        $this->db->where('(tbl_set_prices.date_start = "0000-00-00" OR (tbl_set_prices.date_start <= "'.$date.'" AND tbl_set_prices.date_end >= "'.$date.'"))');


        $this->db->group_start();
        // $this->db->or_where('tbl_set_prices.type_customer', 1);
        $this->db->where('tbl_set_prices.type_customer', 1);
        if (!empty($customerGroups)) {
            foreach ($customerGroups as $key => $value) {
                $this->db->or_where("(FIND_IN_SET(".$value['groupid'].", tbl_set_prices.id_groups)) > 0");
            }
        }
        $this->db->group_end();
        // print_arrays($this->db->get_compiled_select(), FALSE);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }
    public function decreaseWarehouse($id)
    {
        if(is_numeric($id))
        {
            $export = get_table_where('tbl_export_warehouses',array('id'=>$id),'','row');
            $items = get_table_where('tbl_export_warehous_items',array('export_warehouse_id'=>$id));
            foreach ($items as $key => $value) {
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['location_id'];
                $product_id = $value['item_id'];
                if($value['type_item'] == 'products')
                {
                    $value['type_item'] = 'product';
                }
                $type_items = $value['type_item'];
                $quantity = $value['quantity'];
                $date_export = explode(' ', $export->date);
                export_WarehuseQuantity($value['warehouse_id'],$id,$date_warehouse,$date_export[0],$product_id,$quantity,$localtion,$type_items);
                decreaseexport_WarehuseQuantity($value['warehouse_id'],$value['id'],$product_id,$quantity,$localtion,$type_items);
                //trừ kho tổng
                decreaseWarehuseQuantity($value['warehouse_id'],$localtion,$product_id,$quantity,$type_items);
            }
        }
        return true;
    }
    public function increaseadWarehouse($id='',$data='')
    {
        if(is_numeric($id)&&!empty($data))
        {
            //tăng kho khi xóa
            foreach ($data as $key => $value) {

                $import = explode('|', trim($value['id_import'],'|'));
                foreach ($import as $k => $v) {
                    $id_import = explode('-', $v);
                $quantity = get_table_where('tblwarehouse_product',array('id'=>$id_import[0]),'','row');
                $quantity_net =$id_import[1];

                $id_export =  str_replace('XKBH-'.$value['id'].'|', '', $quantity->id_export);
                $this->db->where('id',$id_import[0]);
                $this->db->update('tblwarehouse_product',array('quantity_export'=>($quantity->quantity_export - $quantity_net),'quantity_left'=>($quantity->quantity_left + $quantity_net),'id_export'=>$id_export));
                }
                $this->db->delete('tblwarehouse_export',array('export_id'=>$id,'type_export'=>16));
                if($value['type_item'] == 'products')
                {
                    $value['type_item'] = 'product';
                }
                increaseWarehuseQuantity($value['warehouse_id'],$value['location_id'],$value['item_id'],$value['quantity'],$value['type_item']);

            }
        }
            return true;
    }

    public function getOrderItemsForGift($order_id)
    {
        $this->db->select('
            tbl_order_items.type_item as type_item,
            tbl_order_items.item_id as item_id,
            SUM(tbl_order_items.quantity) as total_quantity
        ');
        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.order_id', $order_id);
        $this->db->where('tbl_order_items.type_gift !=', 1);
        $this->db->group_by('tbl_order_items.type_item', 'tbl_order_items.item_id');
        return $this->db->get()->result_array();
    }

    public function searchOrders($q, $limit = 50)
    {
        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $this->db->select('tbl_orders.id as id, CONCAT(tbl_orders.reference_no, "(", IF(tbl_orders.customer_name IS NOT NULL,tbl_orders.customer_name,""), ")") as text', false);
        $this->db->from('tbl_orders');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $q);
            $this->db->or_like('tbl_orders.reference_no', $q);
            $this->db->or_like('tbl_orders.note', $q);
            $this->db->group_end();
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            $this->db->where('tbl_orders.id_branch IN ('.implode(',', $branch_staff).')', false, false);
        }

        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchOrdersByParams($q, $limit = 50, $params = [])
    {
        $this->db->select('tbl_orders.id as id, CONCAT(tbl_orders.reference_no, "(", tbl_orders.customer_name, ")") as text', false);
        $this->db->from('tbl_orders');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $q);
            $this->db->or_like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }

        if (isset($params['customer_id'])) {
            $params['customer_id'] = str_replace('customers__', '', $params['customer_id']);
            $params['customer_id'] = str_replace('leads__', '', $params['customer_id']);
            $this->db->where('tbl_orders.customer_id', $params['customer_id']);
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function countOrdersStatus($status_table)
    {

        $perViewOrders = has_permission('orders', '', 'view');
        $perViewOwnOrders = has_permission('orders', '', 'view_own');
        if (empty($perViewOrders) && empty($perViewOwnOrders)) return 0;


		if(!empty($this->is_branch)) {
			if (!is_admin()) {
				$list_branch = get_array_branch_staff();
				if (!empty($list_branch)) {
					$this->db->group_start();
					$this->db->where_in('tbl_orders.id_branch', $list_branch);
					$this->db->group_end();
				} else {
					$this->db->where('tbl_orders.id = 0', false, false);
				}
			}
		}

        $khsx = "(
            SELECT count(*)
            FROM tbl_productions_plan_items
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
            LIMIT 1
        )";

        $lsxct = "(
            SELECT count(*)
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_orders ON tbl_productions_plan_items.productions_plan_id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_plan_orders.productions_order_id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
            LIMIT 1
        )";

        $sxx = "(
            SELECT count(*)
            FROM tbl_productions_plan_items
            INNER JOIN tbl_productions_plan_orders ON tbl_productions_plan_items.productions_plan_id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.productions_orders_id = tbl_productions_plan_orders.productions_order_id
            INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
            WHERE tbl_productions_plan_items.object_id = tbl_orders.id AND tbl_productions_plan_items.type_object = 'orders'
            LIMIT 1
        )";

        $xkgh = "(
            SELECT COUNT(*)
            FROM tbl_orders_deliveries
            INNER JOIN tbl_deliveries ON tbl_deliveries.id = tbl_orders_deliveries.delivery_id
            WHERE tbl_orders_deliveries.order_id = tbl_orders.id AND tbl_deliveries.count_export_warehouse = 1
            LIMIT 1
        )";

        $xgc = "(
            SELECT COUNT(*)
            FROM tbl_outsource
            INNER JOIN tbl_orders_outsource ON tbl_outsource.id = tbl_orders_outsource.outsource_id
            WHERE tbl_orders_outsource.order_id = tbl_orders.id AND workflow > 0
            LIMIT 1
        )";

        $ngc = "(
            SELECT COUNT(*)
            FROM tbl_outsource
            INNER JOIN tbl_orders_outsource ON tbl_outsource.id = tbl_orders_outsource.outsource_id
            WHERE tbl_orders_outsource.order_id = tbl_orders.id AND workflow > 1
            LIMIT 1
        )";

        $this->db->from('tbl_orders');
        if ($status_table != 'all') {
            if ($status_table == "un_approved" || $status_table == "approved")
            {
                $this->db->where('tbl_orders.status', $status_table);
            } else {
                if ($status_table == "lkhsx") {
                    $isPlan = "(
                        SELECT
                            tbl_productions_plan.id as id
                        FROM tbl_productions_plan_items
                        INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id
                        WHERE tbl_productions_plan_items.type_object = 'orders' AND tbl_productions_plan_items.object_id = tbl_orders.id
                    )";
                    $this->db->where("(exists ($isPlan))");

                } else if ($status_table == "dsxtcty") {
                    $isPo = "(
                        SELECT
                            tbl_productions_plan_orders.id
                        FROM tbl_productions_plan_orders
                        INNER JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_plan_orders.productions_order_id
                        WHERE tbl_productions_plan_orders.productions_plan_id = tbl_orders.id AND tbl_productions_plan_orders.object_type = 'orders'
                    )";
                    $this->db->where("(exists ($isPo))");

                } else if ($status_table == "sxx") {
                    $isPP = "(
                        SELECT
                            tbl_purchase_products.id as id
                        FROM tbl_productions_orders_details
                        INNER JOIN tbl_purchase_products ON tbl_purchase_products.productions_orders_details_id = tbl_productions_orders_details.id
                        WHERE tbl_productions_orders_details.object_id = tbl_orders.id AND tbl_productions_orders_details.object_type = 'orders' AND tbl_purchase_products.final_stage = 1
                    )";
                    $this->db->where("(exists ($isPP))");

                } else if ($status_table == "xgcn") {
                    $isOutsource = "(
                        SELECT
                            tbl_outsource.id as id, 
                            tbl_outsource.reference_no
                        FROM tbl_outsource_items
                        INNER JOIN tbl_outsource ON tbl_outsource.id = tbl_outsource_items.outsource_id
                        WHERE tbl_outsource_items.object_type = 'orders' AND tbl_outsource_items.order_id = tbl_orders.id
                    )";
                    $this->db->where("(exists ($isOutsource))");
                } else if ($status_table == "ngcn") {
                    $isImportOutsource = "(
                        SELECT
                            tbl_import_outsource.id as id, 
                            tbl_import_outsource.reference_no
                        FROM tbl_import_outsource_items
                        INNER JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id
                        WHERE tbl_import_outsource_items.object_type = 'orders' AND tbl_import_outsource_items.order_id = tbl_orders.id
                    )";
                    $this->db->where("(exists ($isImportOutsource))");

                } else if ($status_table == "ghc") {
                    $ghc = "(
                        SELECT
                            tbl_tranfer_business_item.id as id
                        FROM tbl_tranfer_business_item
                        WHERE tbl_tranfer_business_item.order_id = tbl_orders.id
                    )";
                    $this->db->where("(exists ($ghc))");

                } else if ($status_table == "gh") {
                    $this->db->where("tbl_orders.count_delivery >", 0);

                } else if ($status_table == "xkgh") {
                    $this->db->where("$xkgh >", 0);

                }
            }
        }

        if (!$perViewOrders) {
            $this->db->where('tbl_orders.created_by', get_staff_user_id());
        }

        return $this->db->count_all_results();
    }

    public function convertOrdersToPurchase($data, $expense = false)
    {
        $purchase = array(
            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
            'prefix' => get_option('prefix_purchase'),
            'name_purchase' => $data['name'],
            'explanation' => $data['reason'],
            'date' => to_sql_date($data['date'],true),
            'staff_create' => get_staff_user_id(),
            'date_create' => date('Y:m:d H:i:s'),
            'status' => 2,
            'type' => 1,
            'id_plan' => 0,
            'type_plan' => 0,
            'order_id' => $data['order_id'],
        );
        if($this->db->insert('tblpurchases',$purchase))
        {
            $id = $this->db->insert_id();
            log_activity('Purchase Insert [ID: ' . $id . ']');
            $count = 0;
        }

        $items = $data['items'];

        if($id)
        {
            foreach ($items as $key => $item) {
                if(!empty($item['id']))
                {
                    $items = array(
                        'purchases_id' => $id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'quantity_net' => $item['quantity_net'],
                        'type' => $item['type'],
                        'note' => $item['note'],
                        'order_item_id' => $item['order_item_id'],
                    );
                    if($this->db->insert('tblpurchases_items',$items))
                    {

                        log_activity('Purchase items insert [ID Purchase: ' . $id . ', ID Product: ' . $items['product_id'] . ']');
                        $count++;
                    }
                    else {
                        exit("error");
                    }
                }
            }

        }

        if($id)
        {
            return $id;
        }

        return false;
    }

    public function upOrderItemQuantityPurchase($order_item_id, $quantity, $option = 1)
    {
        $this->db->where('tbl_order_items.id', $order_item_id);

        if ($option == 1)
        {
            $this->db->set('quantity_purchase', 'COALESCE(quantity_purchase, 0)+'.$quantity, FALSE);
        }
        if ($option == 2)
        {
            $this->db->set('quantity_purchase', 'COALESCE(quantity_purchase, 0)-'.$quantity, FALSE);
        }
        return $this->db->update('tbl_order_items');
    }

    public function getPurchasesByOrders($order_id)
    {
        $this->db->select("
            tblpurchases.date as date,
            tblpurchases.prefix as prefix,
            tblpurchases.code as code,
            tblpurchases.name_purchase as name_purchase,
            tblpurchases.date_create as date_create,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as staff_create,
            tblpurchases_items.product_id as product_id,
            tblitems.code as code_item,
            tblitems.name as name_item,
            tblunits.unit as name_unit,
            tblpurchases_items.quantity as quantity,
            tblpurchases_items.quantity_net as quantity_net,
            tblpurchases_items.type as type,
        ");
        $this->db->from('tblpurchases');
        $this->db->join('tblpurchases_items', 'tblpurchases_items.purchases_id = tblpurchases.id');
        $this->db->join('tblitems', 'tblitems.id = tblpurchases_items.product_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tblitems.unit', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblpurchases.staff_create', 'left');
        $this->db->where('tblpurchases.order_id', $order_id);
        $this->db->where('tblpurchases_items.type', 'product');
        $items = $this->db->get()->result_array();

        $this->db->select("
            tblpurchases.date as date,
            tblpurchases.prefix as prefix,
            tblpurchases.code as code,
            tblpurchases.name_purchase as name_purchase,
            tblpurchases.date_create as date_create,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as staff_create,
            tblpurchases_items.product_id as product_id,
            tbl_materials.code as code_item,
            tbl_materials.name as name_item,
            tblunits.unit as name_unit,
            tblpurchases_items.quantity as quantity,
            tblpurchases_items.quantity_net as quantity_net,
            tblpurchases_items.type as type,
        ");
        $this->db->from('tblpurchases');
        $this->db->join('tblpurchases_items', 'tblpurchases_items.purchases_id = tblpurchases.id');
        $this->db->join('tbl_materials', 'tbl_materials.id = tblpurchases_items.product_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblpurchases.staff_create', 'left');
        $this->db->where('tblpurchases.order_id', $order_id);
        $this->db->where('tblpurchases_items.type', 'nvl');
        $materials = $this->db->get()->result_array();

        return array_merge($items, $materials);
        // return $this->db->get()->result_array();
    }

    public function countPurchaseOrders($order_id)
    {
        $this->db->from('tblpurchases');
        $this->db->where('order_id', $order_id);
        return $this->db->get()->num_rows();
    }

    public function getDeliveriesByOrderId($order_id)
    {
        $this->db->select("
            tbl_deliveries.date as date,
            tbl_deliveries.reference_no as reference_no,
            tblshipping_client.address as address_delivery,
            tbl_orders_deliveries.total_quantity as total_quantity,
            tbl_deliveries.date_created as date_created,
            tbl_deliveries.note as note,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
            CONCAT(emp.firstname, ' ', emp.lastname, '') as name_employee,
        ");
        $this->db->from('tbl_orders_deliveries');
        $this->db->join('tbl_deliveries', 'tbl_deliveries.id = tbl_orders_deliveries.delivery_id');
        $this->db->join('tblshipping_client', 'tblshipping_client.id = tbl_deliveries.address_delivery_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_deliveries.created_by', 'left');
        $this->db->join('tblstaff emp', 'emp.staffid = tbl_deliveries.employee_id', 'left');
        $this->db->where('tbl_orders_deliveries.order_id', $order_id);
        return $this->db->get()->result_array();
    }

    public function getPurchaseById($id)
    {
        $this->db->select('*');
        $this->db->from('tblpurchases');
        $this->db->where('tblpurchases.id', $id);
        return $this->db->get()->row_array();
    }

    //
    public function insertOrderItemExchange($data)
    {
        $this->db->insert('tbl_order_item_exchange', $data);
        return $this->db->insert_id();
    }

    public function deleteOrderItemExchange($order_item_id)
    {
        $this->db->where('order_item_id', $order_item_id);
        return $this->db->delete('tbl_order_item_exchange');
    }

    public function getOrderItemExchange($order_item_id)
    {
        $this->db->select('tbl_order_item_exchange.*, tblunits.unit as unit_name');
        $this->db->from('tbl_order_item_exchange');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_order_item_exchange.unit_id', 'left');
        $this->db->where('tbl_order_item_exchange.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }

    public function getOrderItemExchangeView($order_item_id)
    {
        $this->db->select('tbl_order_item_exchange.*, tblunits.unit as unit_name');
        $this->db->from('tbl_order_item_exchange');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_order_item_exchange.unit_id', 'left');
        $this->db->where('tbl_order_item_exchange.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }

    public function getOrderItemExchangeBox($order_item_id)
    {
        $this->db->select('tbl_order_item_exchange.*');
        $this->db->from('tbl_order_item_exchange');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_order_item_exchange.unit_id');
        $this->db->where('LOWER(tblunits.unit)', 'thùng');
        $this->db->where('tbl_order_item_exchange.order_item_id', $order_item_id);
        return $this->db->get()->row_array();
    }

    public function getTotalOrdersReturn($order_id)
    {
        $this->db->select('COALESCE(SUM(tbl_returned_goods.grand_total), 0) as total_return', false);
        $this->db->from('tbl_returned_goods');
        $this->db->where('tbl_returned_goods.order_id', $order_id);
        $this->db->where('tbl_returned_goods.handling_solution', 'debt_reduction');
        return $this->db->get()->row_array();
    }

    //
    public function isOrdersItemsDate($id)
    {
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
        $this->db->where('tbl_order_items.order_id', $id);
        return $this->db->get()->num_rows();
    }

    public function getMaxMinOrders($id)
    {
        $this->db->select("
            min(tbl_order_item_shippings.date_shipping) as date_start,
            max(tbl_order_item_shippings.date_shipping) as date_end,
        ", false);
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
        $this->db->where('tbl_order_items.order_id', $id);
        return $this->db->get()->row_array();
    }

    public function getItemsProductionsOrders($id) {

        $warehouses = "(
            SELECT
                tblwarehouse_items.id_items,
                SUM(tblwarehouse_items.product_quantity) as quantity_warehouses
            FROM tblwarehouse_items
            WHERE tblwarehouse_items.type_items = 'product'
            GROUP BY tblwarehouse_items.id_items
        ) warehouses";

        $this->db->select('
            tbl_order_items.*,
            tbl_products.code as items_code,
            tbl_products.name as items_name,
            tbl_products.quantity_minimum as quantity_minimum,
            COALESCE(warehouses.quantity_warehouses, 0) as quantity_warehouses,
        ', false);
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
        $this->db->join($warehouses, 'warehouses.id_items = tbl_order_items.item_id', 'left');
        $this->db->where('tbl_order_items.order_id', $id);
        $this->db->where('tbl_order_items.type_item', 'products');
        $this->db->group_start();
        $this->db->where('tbl_products.type_products', 'products');
        $this->db->or_where('tbl_products.type_products', 'semi_products');
        $this->db->group_end();
        $items = $this->db->get()->result_array();
        $arrItems = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $order_item_id = $value['id'];
                $this->db->select('*');
                $this->db->from('tbl_order_item_shippings');
                $this->db->where('tbl_order_item_shippings.order_item_id', $order_item_id);
                $this->db->where('(tbl_order_item_shippings.quantity_shipping > tbl_order_item_shippings.quantity_plan_item)');
                $orders_items_date = $this->db->get()->result_array();
                if (empty($orders_items_date)) continue;

                $arrItems[$key] = $value;
                $arrItems[$key]['arrDate'] = $orders_items_date;
            }
        }

        return $arrItems;
    }

    public function deleteOrderItemsStages($order_id) {
        $this->db->where('tbl_order_items_stages.order_id', $order_id);
        return $this->db->delete('tbl_order_items_stages');
    }

    public function handlingStagesOrders($order_id) {
        $this->deleteOrderItemsStages($order_id);
        $this->db->select('
            tbl_order_items.id as order_item_id,
            tbl_order_items.item_id as item_id,
            tbl_products.versions_stage as versions_stage
        ', false);
        $this->db->from('tbl_order_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id');
        $this->db->where('tbl_order_items.type_item', 'products');
        $this->db->where('tbl_order_items.order_id', $order_id);
        $order_items = $this->db->get()->result_array();
        if (!empty($order_items)) {
            $arrayItems = [];
            $arrOrderItems = [];
            foreach ($order_items as $key => $value) {
                $versions_stage = $value['versions_stage'];
                $item_id = $value['item_id'];
                if (empty($versions_stage)) continue;
                $arrOrderItems[] = [
                    'id' => $value['order_item_id'],
                    'versions_stage' => $versions_stage
                ];

                $this->db->select('tbl_product_stages.id');
                $this->db->from('tbl_product_stages');
                $this->db->where('tbl_product_stages.product_id', $item_id);
                $this->db->where('tbl_product_stages.versions', $versions_stage);
                $product_stages = $this->db->get()->row_array();
                if (!empty($product_stages)) {
                    $product_stages_id = $product_stages['id'];
                    $this->db->select('*');
                    $this->db->from('tbl_product_stages_versions');
                    $this->db->where('tbl_product_stages_versions.version_id', $product_stages_id);
                    $product_stages_versions = $this->db->get()->result_array();
                    if (!empty($product_stages_versions)) {
                        foreach ($product_stages_versions as $k => $val) {
                            $arrayItems[] = [
                                'order_id' => $order_id,
                                'order_item_id' => $value['order_item_id'],
                                'stage_id' => $val['stage_id'],
                                'number' => $val['number'],
                                'number_hours' => $val['number_hours'],
                                'final_stage' => $val['final_stage'],
                            ];
                        }
                    }
                }
            }

            if (!empty($arrayItems)) {
                $this->db->insert_batch('tbl_order_items_stages', $arrayItems);
            }

            if (!empty($arrOrderItems)) {
                $this->db->update_batch('tbl_order_items', $arrOrderItems, 'id');
            }
        }

        return true;
    }

    public function getOrderItemsStages($order_item_id) {
        $this->db->select('tbl_order_items_stages.*, tbl_stages.name as stage_name');
        $this->db->from('tbl_order_items_stages');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_order_items_stages.stage_id');
        $this->db->where('tbl_order_items_stages.order_item_id', $order_item_id);
        $this->db->order_by('tbl_order_items_stages.number ASC');
        return $this->db->get()->result_array();
    }

    public function getDeliveriesItemsByOrderId($order_id)
    {
        $this->db->select("
            tbl_deliveries.date as date,
            tbl_deliveries.reference_no as reference_no,
            tbl_delivery_items.*
        ");

        $this->db->from('tbl_order_items');
        $this->db->join('tbl_delivery_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
        $this->db->join('tbl_deliveries', 'tbl_deliveries.id = tbl_delivery_items.delivery_id');
        $this->db->where('tbl_order_items.order_id', $order_id);
        return $this->db->get()->result_array();
    }

    public function handlingStageProductionsWhenPrintBarcode($order_item_id) {
        $this->db->select('tbl_order_items.id, tbl_order_items.active, tbl_order_items.staff_active, tbl_order_items.date_active', false);
        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.id', $order_item_id);
        $order_item = $this->db->get()->row_array();
        if (!empty($order_item)) {
            $active = $order_item['active'];
            $staff_active = $order_item['staff_active'];
            $date_active = $order_item['date_active'];

            $this->db->where('tbl_productions_orders_items_stages.object_type', 'orders');
            $this->db->where('tbl_productions_orders_items_stages.object_item_id', $order_item_id);
            $this->db->where('tbl_productions_orders_items_stages.stage_id', STAGE_PRINT_BARCODE);
            return $this->db->update('tbl_productions_orders_items_stages', [
                'active' => $active,
                'staff_active' => $staff_active,
                'date_active' => $date_active,
            ]);
        }
        return false;
    }

    public function updateOrdersDelivery($order_id) {
        $query = "UPDATE tbl_order_items 
        INNER JOIN (
            SELECT 
                tbl_delivery_items.order_item_id as order_item_id,
                SUM(tbl_delivery_items.quantity) quantity_delivery
            FROM tbl_delivery_items
            GROUP BY tbl_delivery_items.order_item_id
        ) tb_d ON tb_d.order_item_id = tbl_order_items.id
        SET tbl_order_items.quantity_delivery = tb_d.quantity_delivery
        WHERE tbl_order_items.id = tb_d.order_item_id AND tbl_order_items.order_id = ".$order_id."";
        $this->db->query($query);

        $query = "UPDATE tbl_orders 
        INNER JOIN (
            SELECT 
                tbl_order_items.order_id as order_id,
                SUM(tbl_order_items.quantity_delivery) quantity_delivery
            FROM tbl_order_items
            GROUP BY tbl_order_items.order_id
        ) tb_order_item ON tb_order_item.order_id = tbl_orders.id
        SET total_quantity_had_delivery = tb_order_item.quantity_delivery
        WHERE tb_order_item.order_id = tbl_orders.id AND tbl_orders.id = ".$order_id."";
        $this->db->query($query);
    }

    public function insertBatchOrderItemsSize($data) {
        return $this->db->insert_batch('tbl_order_items_size', $data);
    }

    public function deleteOrderItemsSizeByOrderId($order_id) {
        $this->db->where('tbl_order_items_size.order_id', $order_id);
        return $this->db->delete('tbl_order_items_size');
    }

    public function deleteOrderItemsSizeByOrderItemId($order_item_id) {
        $this->db->where('tbl_order_items_size.order_item_id', $order_item_id);
        return $this->db->delete('tbl_order_items_size');
    }

    public function getOrderItemsSize($order_item_id) {
        $this->db->select('*');
        $this->db->from('tbl_order_items_size');
        $this->db->where('tbl_order_items_size.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }
    
    public function insertBatchOrderItemsChangeSize($data) {
        return $this->db->insert_batch('tbl_order_items_change_size', $data);
    }

    public function deleteBatchOrderItemsChangeSize($order_id) {
        $this->db->where('tbl_order_items_change_size.order_id', $order_id);
        return $this->db->delete('tbl_order_items_change_size');
    }

    public function getOrderItemsChangeSizeByOrderItemId($order_item_id) {
        $this->db->select('*');
        $this->db->from('tbl_order_items_change_size');
        $this->db->where('tbl_order_items_change_size.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }

    public function getOrderItemsSizeView($order_item_id) {
        $this->db->select('
            tbl_order_items_size.*,
            tblsize.name as name_size,
            tbl_colors.name as name_color,
        ', false);
        $this->db->from('tbl_order_items_size');
        $this->db->join('tblsize', 'tblsize.id = tbl_order_items_size.size', 'left');
        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_order_items_size.color', 'left');
        $this->db->where('tbl_order_items_size.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }

    public function checkOrdersBusiness($order_id) {
        $this->db->from('tbl_business_plan');
        $this->db->where('tbl_business_plan.order_id', $order_id);
        return $this->db->count_all_results();
    }

    public function getOrdersBusiness($order_id) {
        $this->db->select('tbl_business_plan.*');
        $this->db->from('tbl_business_plan');
        $this->db->where('tbl_business_plan.order_id', $order_id);
        return $this->db->get()->row_array();
    }

    public function insertBatchOrderItemsColumns($data) {
        return $this->db->insert_batch('tbl_order_items_columns', $data);
    }

    public function deleteOrderItemsColumns($order_id) {
        $this->db->where('tbl_order_items_columns.order_id', $order_id);
        return $this->db->delete('tbl_order_items_columns');
    }

    public function getOrderItemsColumnsByOrderItemId($order_item_id) {
        $this->db->select('tbl_order_items_columns.*');
        $this->db->from('tbl_order_items_columns');
        $this->db->where('tbl_order_items_columns.order_item_id', $order_item_id);
        return $this->db->get()->result_array();
    }

    public function getPricesOfQuotes($item_id, $type_item, $customer_id, $quantity) {
        $this->db->select('
            tbl_quote_items.unit_price as unit_price
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_quote_items', 'tbl_quote_items.quote_id = tbl_quotes.id');
        $this->db->where('tbl_quotes.customer_id', $customer_id);
        $this->db->where('tbl_quote_items.item_id', $item_id);
        $this->db->where('tbl_quote_items.type_item', $type_item);
        $this->db->where('tbl_quote_items.moq <=', $quantity);
        $this->db->where('tbl_quote_items.moq_to >=', $quantity);
        $this->db->where('tbl_quote_items.unit_price > 0');
        $this->db->order_by('tbl_quotes.date DESC');
        $this->db->where('tbl_quotes.status', 'approved');
        // print_arrays($this->db->get_compiled_select());
        return $this->db->get()->row_array();
    }

    public function handlingTaxOrdersAndDelivery($id, $actions = 'add') {
        $this->db->select('
            tbl_invoices.tax_id as tax_id,
            tbl_invoices.tax_name as tax_name,
            tbl_invoices.tax_rate as tax_rate,
        ', false);
        $this->db->from('tbl_invoices');
        $this->db->where('tbl_invoices.id', $id);
        $invoices = $this->db->get()->row_array();

        $arrUpdateOrders = [];
        $arrUpdateDelivery = [];
        if (!empty($invoices)) {

            $tax_id = $invoices['tax_id'];
            $tax_name = $invoices['tax_name'];
            $tax_rate = $invoices['tax_rate'];

            $this->db->select('tbl_invoice_items.*');
            $this->db->from('tbl_invoice_items');
            $this->db->where('tbl_invoice_items.invoice_id', $id);
            $invoice_items = $this->db->get()->result_array();
            if (!empty($invoice_items)) {
                foreach ($invoice_items as $key => $value) {
                    // $order = $this->orders_model->rowOrderById($value);
                    $dtDelivery = get_table_where('tbl_deliveries', ['order_id' => $value['object_id']]);
                    $tax_id_old = $value['tax_id_old'];
                    $tax_name_old = $value['tax_name_old'];
                    $tax_rate_old = $value['tax_rate_old'];
                    $total_item = $value['total_item'];
                    $cost_delivery_item = $value['cost_delivery_item'];

                    if ($actions == 'add') {
                        $total_tax_item = 0;
                        if ($tax_rate > 0) {
                            $total_tax_item = $total_item * ($tax_rate / 100);
                        }
                        $grand_total_item = $total_item + $total_tax_item + $cost_delivery_item;

                        $arrUpdateOrders[] = [
                            'id' => $value['object_id'],
                            'type_bills' => 1,
                            'tax_id' => $tax_id,
                            'tax_name' => $tax_name,
                            'tax_rate' => $tax_rate,
                            'total_tax' => $total_tax_item,
                            'grand_total' => $grand_total_item,
                        ];

                        if (!empty($dtDelivery)) {
                            foreach ($dtDelivery as $k => $val) {
                                $grand_total_items_delivery = $val['grand_total_items'];
                                $total_tax_item_delivery = 0;
                                if ($tax_rate > 0) {
                                    $total_tax_item_delivery = $grand_total_items_delivery * ($tax_rate / 100);
                                }
                                $grand_total_delivery = $grand_total_items_delivery + $total_tax_item_delivery;

                                $arrUpdateDelivery[] = [
                                    'id' => $val['id'],
                                    'tax_id' => $tax_id,
                                    'tax_name' => $tax_name,
                                    'tax_rate' => $tax_rate,
                                    'total_tax' => $total_tax_item_delivery,
                                    'grand_total' => $grand_total_delivery,
                                ];
                            }
                        }
                    } else if ($actions == 'delete') {
                        $total_tax_item = 0;
                        if ($tax_rate_old > 0) {
                            $total_tax_item = $total_item * ($tax_rate_old / 100);
                        }
                        $grand_total_item = $total_item + $total_tax_item + $cost_delivery_item;

                        $arrUpdateOrders[] = [
                            'id' => $value['object_id'],
                            'type_bills' => 0,
                            'tax_id' => $tax_id_old,
                            'tax_name' => $tax_name_old,
                            'tax_rate' => $tax_rate_old,
                            'total_tax' => $total_tax_item,
                            'grand_total' => $grand_total_item,
                        ];

                        if (!empty($dtDelivery)) {
                            foreach ($dtDelivery as $k => $val) {
                                $grand_total_items_delivery = $val['grand_total_items'];
                                $total_tax_item_delivery = 0;
                                if ($tax_rate_old > 0) {
                                    $total_tax_item_delivery = $grand_total_items_delivery * ($tax_rate_old / 100);
                                }
                                $grand_total_delivery = $grand_total_items_delivery + $total_tax_item_delivery;

                                $arrUpdateDelivery[] = [
                                    'id' => $val['id'],
                                    'tax_id' => $tax_id_old,
                                    'tax_name' => $tax_name_old,
                                    'tax_rate' => $tax_rate_old,
                                    'total_tax' => $total_tax_item_delivery,
                                    'grand_total' => $grand_total_delivery,
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (!empty($arrUpdateOrders)) {
            $this->db->update_Batch('tbl_orders', $arrUpdateOrders, 'id');
        }

        if (!empty($arrUpdateDelivery)) {
            $this->db->update_Batch('tbl_deliveries', $arrUpdateDelivery, 'id');
        }
        return true;
    }

    public function handlingTaxDelivery($id, $actions = 'add') {
        $this->db->select('
            tbl_invoices.tax_id as tax_id,
            tbl_invoices.tax_name as tax_name,
            tbl_invoices.tax_rate as tax_rate,
        ', false);
        $this->db->from('tbl_invoices');
        $this->db->where('tbl_invoices.id', $id);
        $invoices = $this->db->get()->row_array();

        $arrUpdateOrders = [];
        $arrUpdateDelivery = [];
        if (!empty($invoices)) {

            $tax_id = $invoices['tax_id'];
            $tax_name = $invoices['tax_name'];
            $tax_rate = $invoices['tax_rate'];

            $this->db->select('tbl_invoice_items.*');
            $this->db->from('tbl_invoice_items');
            $this->db->where('tbl_invoice_items.invoice_id', $id);
            $invoice_items = $this->db->get()->result_array();
            if (!empty($invoice_items)) {
                foreach ($invoice_items as $key => $value) {
                    // $order = $this->orders_model->rowOrderById($value);
                    // $dtDelivery = get_table_where('tbl_deliveries', ['order_id' => $value['object_id']]);
                    $tax_id_old = $value['tax_id_old'];
                    $tax_name_old = $value['tax_name_old'];
                    $tax_rate_old = $value['tax_rate_old'];
                    $total_item = $value['total_item'];
                    $cost_delivery_item = $value['cost_delivery_item'];
                    $additional_costs = $value['additional_cost'];

                    if ($actions == 'add') {
                        $total_tax_item = 0;
                        if ($tax_rate > 0) {
                            $total_tax_item = $total_item * ($tax_rate / 100);
                        }
                        $grand_total_item = $total_item + $total_tax_item + $cost_delivery_item + $additional_costs;

                        $arrUpdateOrders[] = [
                            'id' => $value['object_id'],
                            'type_bills' => 1,
                            'tax_id' => $tax_id,
                            'tax_name' => $tax_name,
                            'tax_rate' => $tax_rate,
                            'total_tax' => $total_tax_item,
                            'grand_total' => $grand_total_item,
                        ];
                        
                    } else if ($actions == 'delete') {
                        $total_tax_item = 0;
                        if ($tax_rate_old > 0) {
                            $total_tax_item = $total_item * ($tax_rate_old / 100);
                        }
                        $grand_total_item = $total_item + $total_tax_item + $cost_delivery_item + $additional_costs;

                        $arrUpdateOrders[] = [
                            'id' => $value['object_id'],
                            'type_bills' => 0,
                            'tax_id' => $tax_id_old,
                            'tax_name' => $tax_name_old,
                            'tax_rate' => $tax_rate_old,
                            'total_tax' => $total_tax_item,
                            'grand_total' => $grand_total_item,
                        ];
                    }
                }
            }
        }

        if (!empty($arrUpdateOrders)) {
            // $this->db->update_Batch('tbl_orders', $arrUpdateOrders, 'id');
            $this->db->update_Batch('tbl_deliveries', $arrUpdateOrders, 'id');
        }

        // if (!empty($arrUpdateDelivery)) {
        //     $this->db->update_Batch('tbl_deliveries', $arrUpdateDelivery, 'id');
        // }
        return true;
    }

    public function getOrdersSubById($order_id) {
        $this->db->select('tbl_orders_sub.*');
        $this->db->from('tbl_orders_sub');
        $this->db->where('tbl_orders_sub.order_id', $order_id);
        return $this->db->get()->row_array();
    }

    public function handlingOrdersSub($order_id, $data = []) {
        $orders_sub = $this->getOrdersSubById($order_id);

        if (!empty($orders_sub)) {
            $this->db->where('order_id', $order_id);
            $this->db->update('tbl_orders_sub', $data);
        } else {
            $this->db->insert('tbl_orders_sub', $data);
        }
        return true;
    }

    public function getOrdersRelationshipById($order_id) {
        $this->db->select('tbl_orders_relationship.*');
        $this->db->from('tbl_orders_relationship');
        $this->db->where('tbl_orders_relationship.order_id', $order_id);
        return $this->db->get()->row_array();
    }

    public function handlingOrdersRelationship($order_id, $data = []) {
        $this->db->where('order_id', $order_id);
        $this->db->delete('tbl_orders_relationship');
        $this->db->insert_batch('tbl_orders_relationship', $data);
        return true;
    }

    public function deleteOrdersRelationship($order_id) {
        $this->db->where('order_id', $order_id);
        return $this->db->delete('tbl_orders_relationship');
    }

    public function deleteOrdersSub($order_id) {
        $this->db->where('order_id', $order_id);
        return $this->db->delete('tbl_orders_sub');
    }

    public function getGroupPriceCustomer($customer_id) {
        $this->db->select('tblgroup_price.*');
        $this->db->from('tblgroup_price');
        $this->db->where('tblgroup_price.client', $customer_id);
        $this->db->order_by('tblgroup_price.id DESC');
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getPriceCustomer($table_price_id, $customer_id, $product_id, $type, $moq) {
        $priceItem = 0;
		$query = "(
			SELECT
				tblgroup_price_discount.group_price_id as group_price_id,
				tblgroup_price_discount.discount as discount
			FROM tblgroup_price_discount
			WHERE tblgroup_price_discount.group_price_id = $table_price_id AND tblgroup_price_discount.client = $customer_id
			LIMIT 1
		) tb_group_price_discount";
		$this->db->select('
			(tblgroup_price_detail.price - (tblgroup_price_detail.price * coalesce(tb_group_price_discount.discount, 0)/100)) as price,
		', false);
		$this->db->from('tblgroup_price_detail');
		$this->db->join($query, 'tb_group_price_discount.group_price_id = tblgroup_price_detail.group_price_id', 'left');
		$this->db->where('tblgroup_price_detail.group_price_id', $table_price_id);
		$this->db->where('tblgroup_price_detail.product_id', $product_id);
		$this->db->where('tblgroup_price_detail.product_type', $type);
		$this->db->where('(
			(tblgroup_price_detail.money_start <= ' . $moq . ' AND tblgroup_price_detail.money_end >= ' . $moq . ')
			OR
			(tblgroup_price_detail.money_start = 0 AND tblgroup_price_detail.money_end = 0)
			OR
			(tblgroup_price_detail.money_start <= ' . $moq . ' AND tblgroup_price_detail.money_end = 0)
			OR
			(tblgroup_price_detail.money_end >= ' . $moq . ' AND tblgroup_price_detail.money_start = 0)
		)', false, false);
		$this->db->order_by('tblgroup_price_detail.money_start DESC');
		$rs = $this->db->get()->row_array();

		if (!empty($rs)) {
			$priceItem = $rs['price'];
		}
		return $priceItem;
    }
}