<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Deliveries_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertDeliveries($data = [])
    {
        $this->db->insert('tbl_deliveries', $data);
        $id = $this->db->insert_id();
        if (!empty($id)) {
            $staffId = $this->site_model->getAllStaffId()['staffid'];
            $this->updateDeliveriesById($id, ['list_users' => $staffId]);
        }
        return $id;
    }

    public function insertDeliveryItems($data = [])
    {
        $this->db->insert('tbl_delivery_items', $data);
        return $this->db->insert_id();
    }

    public function insertOrdersDeliveries($data = [])
    {
        $this->db->insert('tbl_orders_deliveries', $data);
        return $this->db->insert_id();
    }

    public function rowDeliveriesById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_deliveries');
        $this->db->where('tbl_deliveries.id', $id);
        return $this->db->get()->row_array();
    }

    public function getOrdersDeliveries($delivery_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_orders_deliveries');
        $this->db->where('tbl_orders_deliveries.delivery_id', $delivery_id);
        return $this->db->get()->result_array();
    }

    public function getDeliveryItems($delivery_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_delivery_items');
        $this->db->where('tbl_delivery_items.delivery_id', $delivery_id);
        return $this->db->get()->result_array();
    }

    public function deleteDeliveriesById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_deliveries');
    }

    public function deleteDeliveryItemsById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_delivery_items');
    }

    public function deleteDeliveryItemsByDeliveryID($delivery_id)
    {
        $this->db->where('delivery_id', $delivery_id);
        return $this->db->delete('tbl_delivery_items');
    }

    public function deleteOrdersDeliveriesByDeliveryId($delivery_id)
    {
        $this->db->where('tbl_orders_deliveries.delivery_id', $delivery_id);
        return $this->db->delete('tbl_orders_deliveries');
    }

    public function rowRefereceNoOrderByDeliveryId($delivery_id)
    {
        $query = "SELECT
        GROUP_CONCAT(tbl_orders.reference_no SEPARATOR '</br>') as reference_order
        FROM tbl_orders_deliveries
        INNER JOIN tbl_orders ON tbl_orders_deliveries.order_id = tbl_orders.id
        WHERE tbl_orders_deliveries.delivery_id = $delivery_id";

        return $this->db->query($query)->row_array();
    }

    public function searchOrderByCustomerForDeliveries($term, $limit, $customer_id)
    {
        $this->db->select('tbl_orders.id as id, tbl_orders.reference_no as text', false);
        $this->db->from('tbl_orders');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_orders.customer_id', $customer_id);
        $this->db->where('(tbl_orders.total_quantity > tbl_orders.total_quantity_had_delivery)');
        $this->db->where('tbl_orders.status', 'approved');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchOrderItemForDelivery($term, $limit, $orders_id = [], $edit = 0, $delivery_id = 0)
    {
        $this->db->select("tbl_order_items.id as id, CONCAT(tbl_order_items.item_name, '(',tbl_order_items.item_code, ')', '(', tbl_orders.reference_no, ')') as text", false);
        $this->db->from('tbl_orders');
        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('tbl_order_items.item_code', $q);
            $this->db->or_like('tbl_order_items.item_name', $q);
            $this->db->or_like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        if ($edit == 0) {
            $this->db->where('(tbl_order_items.quantity > tbl_order_items.quantity_delivery)');
        } else if ($delivery_id > 0) {
            $dv = "(
                SELECT SUM(tbl_deliveries.quantity)
                FROM tbl_deliveries
                INNER JOIN tbl_delivery_items ON tbl_deliveries.id = tbl_delivery_items.delivery_id
                WHERE tbl_delivery_items.order_item_id = tbl_order_items.id
            )";
            $this->db->where("(tbl_order_items.quantity > (tbl_order_items.quantity_delivery - $dv))");
        }
        $this->db->where_in('tbl_orders.id', $orders_id);
        $this->db->where('tbl_orders.status', 'approved');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function rowDeliveryItemByOrderItemId($order_item_id, $delivery_id)
    {
        $this->db->select('SUM(tbl_delivery_items.quantity) as quantity_delivery', false);
        $this->db->from('tbl_delivery_items');
        $this->db->where('tbl_delivery_items.delivery_id', $delivery_id);
        $this->db->where('tbl_delivery_items.order_item_id', $order_item_id);
        return $this->db->get()->row_array();
    }

    public function getOrderItemForDelivery($orders_id = [])
    {
        $this->db->select("tbl_order_items.*, tbl_orders.reference_no", false);
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        $this->db->where('(tbl_order_items.quantity > tbl_order_items.quantity_delivery)');
        $this->db->where_in('tbl_orders.id', $orders_id);
        $this->db->where('tbl_orders.status', 'approved');
        return $this->db->get()->result_array();
    }

    public function updateDeliveriesById($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_deliveries', $data);
    }

    public function getDeliveryItemsTotal($delivery_id)
    {
        // SUM(tbl_delivery_items.quantity) as quantity
        $this->db->select('
            tbl_orders.reference_no as reference_order,
            tbl_order_items.id as order_item_id,
            tbl_delivery_items.id as delivery_item_id,
            tbl_delivery_items.type_item as type_item,
            tbl_delivery_items.item_id as item_id,
            tbl_delivery_items.item_code as item_code,
            tbl_delivery_items.item_name as item_name,
            tbl_delivery_items.quantity as quantity
            ', false);
        $this->db->from('tbl_delivery_items');
        $this->db->join('tbl_order_items', 'tbl_order_items.id = tbl_delivery_items.order_item_id');
        $this->db->join('tbl_orders', 'tbl_order_items.order_id = tbl_orders.id');
        $this->db->where('tbl_delivery_items.delivery_id', $delivery_id);
        // $this->db->group_by('tbl_delivery_items.type_item, tbl_delivery_items.item_id');
        return $this->db->get()->result_array();
    }

    public function insertExportWarehouses($data)
    {
        $this->db->insert('tbl_export_warehouses', $data);
        return $this->db->insert_id();
    }

    public function insertExportWarehouseItems($data)
    {
        $this->db->insert('tbl_export_warehous_items', $data);
        return $this->db->insert_id();
    }

    public function insertBatchExportWarehouseItems($data)
    {
        $this->db->insert_batch('tbl_export_warehous_items', $data);
        return $this->db->insert_id();
    }

    public function updateCountDeliveries($options = 0)
    {
        $this->db->where('id', $id);
        if ($options == 0) {
        }
        return $this->db->update('tbl_deliveries');
    }

    public function rowExportWarehouses($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_export_warehouses');
        $this->db->where('tbl_export_warehouses.id', $id);
        return $this->db->get()->row_array();
    }

    public function getExportWarehouseItems($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_export_warehous_items');
        $this->db->where('tbl_export_warehous_items.export_warehouse_id', $id);
        return $this->db->get()->result_array();
    }

    public function deleteExportWarehouses($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_export_warehouses');
    }

    public function deleteExportWarehousesItems($export_warehouse_id)
    {
        $this->db->where('export_warehouse_id', $export_warehouse_id);
        return $this->db->delete('tbl_export_warehous_items');
    }

    public function insertBatchExWarehouseObject($data = [])
    {
        return $this->db->insert_batch('tbl_ex_warehouse_object', $data);
    }

    public function updateBatchExWarehouseObject($data = [])
    {
        return $this->db->update_batch('tbl_ex_warehouse_object', $data, 'id');
    }

    public function deleteExWarehouseObject($type, $object_id)
    {
        $this->db->where('tbl_ex_warehouse_object.type', $type);
        $this->db->where('tbl_ex_warehouse_object.object_id', $object_id);
        return $this->db->delete('tbl_ex_warehouse_object');
    }

    public function getTotalExWarehouseObject($type, $object_id, $group_object_id_more = false)
    {
        // $this->db->select('tbl_ex_warehouse_object.object_id_more, SUM(tbl_ex_warehouse_object.total) as total');
        // $this->db->from('tbl_ex_warehouse_object');
        // $this->db->where('tbl_ex_warehouse_object.type', $type);
        // $this->db->where('tbl_ex_warehouse_object.object_id', $object_id);
        // if ($group_object_id_more) {
        //     $this->db->group_by('tbl_ex_warehouse_object.object_id_more');
        // } else {

        // }

        $this->db->select('tbl_ex_warehouse_object.object_id_more, SUM(tbl_ex_warehouse_object.total) as total');
        $this->db->from('tbl_ex_warehouse_object');
        $this->db->where('tbl_ex_warehouse_object.type', $type);
        $this->db->where('tbl_ex_warehouse_object.object_id', $object_id);
        if ($group_object_id_more) {
            $this->db->group_by('tbl_ex_warehouse_object.object_id_more');
        } else {
        }

        return $this->db->get()->result_array();
    }

    public function countDeliveriesStatus($status)
    {
        $this->perViewDeliveries = has_permission('releases_deliveries', '', 'view');
        $this->perViewOwnDeliveries = has_permission('releases_deliveries', '', 'view_own');
        $this->db->from('tbl_deliveries');
        if (!$this->perViewDeliveries) {
            $this->db->where('tbl_deliveries.created_by', get_staff_user_id());
        }
        if (!empty($status) && $status != 'all') {
            if ($status == 'status_undelivery') {
                $this->db->where('tbl_deliveries.warehouseman_id', 0);
            } elseif ($status == 'status_delivery') {
                $this->db->where('tbl_deliveries.warehouseman_id >', 0);
            } elseif ($status == 'received_certificate'){
                $this->db->where('tbl_deliveries.received_certificate', 1);
            } elseif ($status == 'not_received_certificate'){
                $this->db->where('tbl_deliveries.received_certificate', 0);
            } elseif ($status == 'invoice'){
                // JOIN tbl_invoices ON tbl_invoices.id = tbl_invoice_items.invoice_id
                $this->db->where('EXISTS (
                    SELECT tbl_invoice_items.object_id
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
                )');
            } elseif ($status == 'not_invoice'){
                // JOIN tbl_invoices ON tbl_invoices.id = tbl_invoice_items.invoice_id
                $this->db->where('NOT EXISTS (
                    SELECT tbl_invoice_items.object_id
                    FROM tbl_invoice_items
                    WHERE tbl_invoice_items.object_id = tbl_deliveries.id
                )');
            }
        }
        // return $this->db->get()->num_rows();
        return $this->db->count_all_results();
    }

    public function countExportWarehousesStatus($status)
    {
        $this->db->from('tbl_export_warehouses');
        if (!empty($status) && $status != 'all') {
            if ($status == 'un_approved_ws_stock') {
                $this->db->where('tbl_export_warehouses.warehouseman_id', 0);
            } else if ($status == 'approved_ws_stock') {
                $this->db->where('tbl_export_warehouses.warehouseman_id >', 0);
            }
        }
        return $this->db->get()->num_rows();
    }
    public function decreaseWarehouse($id)
    {
        if (is_numeric($id)) {
            $export = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
            $items = get_table_where('tbl_delivery_items', array('delivery_id' => $id));
            $main = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
            foreach ($items as $key => $value) {
                $date_warehouse = date('Y-m-d H:i:s');
                $localtion  =  $value['location_id'];
                $product_id = $value['item_id'];
                $warehouse_id = $value['warehouse_id'];
                if ($value['type_item'] == 'products') {
                    $value['type_item'] = 'product';
                }
                if ($value['type_item'] == 'materials') {
                    $value['type_item'] = 'nvaluel';
                }
                if ($value['type_item'] == 'tools_supplies') {
                    $value['type_item'] = 'tools';
                }
                $type_items = $value['type_item'];
                $quantity_stock = $value['quantity_stock'];
                $quantity_unit = $value['quantity_unit'];
                $quantity_payment = $value['quantity_payment'];
                $date_export = explode(' ', $export->date);
                $lot_code = $value['lot_code'];
                $date_sx = $value['date_sx'];
                $date_sd = $value['date_sd'];
                $date_use = $value['date_use'];

                deliveries_WarehuseQuantity($warehouse_id, $id, $date_warehouse, $date_export[0], $product_id, $quantity_stock, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                decreasedeliveries_WarehuseQuantity($warehouse_id, $value['id'], $product_id, $quantity_stock, $localtion, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);
                //trừ kho tổng
                decreaseWarehuseQuantity($warehouse_id, $localtion, $product_id, $quantity_stock, $type_items, $lot_code, $date_sx, $date_sd, $date_use, $quantity_unit, $quantity_payment);

                // export_WarehuseQuantity($main->warehouse_id,$id,$date_warehouse,$date_export[0],$product_id,$quantity,$localtion,$type_items);
                // decreaseexport_WarehuseQuantity($value['warehouse_id'],$value['id'],$product_id,$quantity,$localtion,$type_items);
                // //trừ kho tổng
                // decreaseWarehuseQuantity($value['warehouse_id'],$localtion,$product_id,$quantity,$type_items);
            }
        }
        return true;
    }
    public function increaseadWarehouse($id = '')
    {
        if (is_numeric($id)) {
            $export = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
            $items = get_table_where('tbl_delivery_items', array('delivery_id' => $id));
            $main = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
            //tăng kho khi xóa
            foreach ($items as $key => $value) {
                $warehouse_id = $value['warehouse_id'];
                $import = explode('|', trim($value['id_import'], '|'));
                foreach ($import as $k => $v) {
                    $id_import = explode('-', $v);
                    $quantity = get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                    // $quantity_net =$id_import[1];
                    $quantity_net = $id_import[1];
                    $quantity_unit = $id_import[2];
                    $quantity_payment = $id_import[3];

                    $id_export =  str_replace('XKGH-' . $value['id'] . '|', '', $quantity->id_export);
                    $this->db->where('id', $id_import[0]);
                    // $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net), 'id_export' => $id_export));
                    $this->db->update('tblwarehouse_product', array('quantity_export' => ($quantity->quantity_export - $quantity_net), 'quantity_left' => ($quantity->quantity_left + $quantity_net),'product_quantity_unit_export' => ($quantity->product_quantity_unit_export - $quantity_unit), 'product_quantity_unit_left' => ($quantity->product_quantity_unit_left + $quantity_unit),'product_quantity_payment_export' => ($quantity->product_quantity_payment_export - $quantity_payment), 'product_quantity_payment_left' => ($quantity->product_quantity_payment_left + $quantity_payment), 'id_export' => $id_export));
                }
                $this->db->delete('tblwarehouse_export', array('export_id' => $id, 'type_export' => 38));
                if ($value['type_item'] == 'products') {
                    $value['type_item'] = 'product';
                }
          
                increaseWarehuseQuantity($warehouse_id, $value['location_id'], $value['item_id'], $value['quantity_stock'], $value['type_item'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use'],$value['quantity_unit'],$value['quantity_payment']);
            }
        }
        return true;
    }

    public function updateBatchDeliveryItems($data) {
        return $this->db->update_batch('tbl_delivery_items', $data, 'id');
    }

    public function checkDeliveryInvoice($delivery_id) {
        $data = [];

        $this->db->from('tbl_invoice_items');
        $this->db->where('tbl_invoice_items.object_id', $delivery_id);
        $this->db->limit(1);
        $rs = $this->db->get()->num_rows();
        if ($rs) {
            $data['result'] = 1;
            $data['message'] = lang('Đã tạo hóa đơn không thể chỉnh chức năng này');
            return $data;
        }

        return $data;
    }
}
