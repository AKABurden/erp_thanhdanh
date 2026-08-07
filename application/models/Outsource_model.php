<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Outsource_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function checkExistOutsource($reference_no) {
        $this->db->from('tbl_outsource');
        $this->db->where('tbl_outsource.reference_no', $reference_no);
        return $this->db->get()->num_rows();
    }

    public function searchOrderForOutsource($term, $limit)
    {
        $this->db->select('tbl_orders.id as id, tbl_orders.reference_no as text', false);
        $this->db->from('tbl_orders');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->where('(tbl_orders.total_quantity > tbl_orders.total_quantity_had_outsource)');
        $this->db->where('tbl_orders.status', 'approved');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchOrderItemForOutsource($term, $limit, $orders_id = [], $edit = 0, $outsource_id = 0)
    {
        $this->db->select("tbl_order_items.id as id, CONCAT(tbl_order_items.item_name, '(',tbl_order_items.item_code, ')', '(', tbl_orders.reference_no, ')') as text", false);
        $this->db->from('tbl_orders');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_order_items.item_code', $q);
            $this->db->or_like('tbl_order_items.item_name', $q);
            $this->db->or_like('tbl_orders.reference_no', $q);
            $this->db->group_end();
        }
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        if ($edit == 0) {
            $this->db->where('(tbl_order_items.quantity > tbl_order_items.quantity_outsource)');
        } else if ($outsource_id > 0) {
            // $dv = "(
            //     SELECT SUM(tbl_deliveries.quantity)
            //     FROM tbl_deliveries
            //     INNER JOIN tbl_delivery_items ON tbl_deliveries.id = tbl_delivery_items.delivery_id
            //     WHERE tbl_delivery_items.order_item_id = tbl_order_items.id
            // )";
            // $this->db->where("(tbl_order_items.quantity > (tbl_order_items.quantity_delivery - $dv))");
        }
        $this->db->where_in('tbl_orders.id', $orders_id);
        $this->db->where('tbl_orders.status', 'approved');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function getOrderItemForOutsource($orders_id = [])
    {
        $this->db->select("tbl_order_items.*, tbl_orders.reference_no", false);
        $this->db->from('tbl_orders');
        $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
        $this->db->where('(tbl_order_items.quantity > tbl_order_items.quantity_outsource)');
        $this->db->where_in('tbl_orders.id', $orders_id);
        $this->db->where('tbl_orders.status', 'approved');
        return $this->db->get()->result_array();
    }

    public function insertOutsource($data = [])
    {
        $this->db->insert('tbl_outsource', $data);
        return $this->db->insert_id();
    }

    public function updateOutsource($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_outsource', $data);
    }

    public function insertOutsourceItems($data = [])
    {
        $this->db->insert('tbl_outsource_items', $data);
        return $this->db->insert_id();
    }

    public function insertOrdersOutsource($data = [])
    {
        $this->db->insert('tbl_orders_outsource', $data);
        return $this->db->insert_id();
    }

    public function deleteOutsource($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_outsource');
    }

    public function deleteOutsourceItems($outsource_id)
    {
        $this->db->where('outsource_id', $outsource_id);
        return $this->db->delete('tbl_outsource_items');
    }

    public function deleteOrdersOutsource($outsource_id)
    {
        $this->db->where('outsource_id', $outsource_id);
        return $this->db->delete('tbl_orders_outsource');
    }

    public function rowOutSourceById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_outsource');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getOutSourceItemsByOutsourceId($outsource_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_outsource_items');
        $this->db->where('outsource_id', $outsource_id);
        return $this->db->get()->result_array();
    }

    public function rowOutsourceItemsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_outsource_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowOutsourceItemByOrderItemId($order_item_id, $outsource_id)
    {
        $this->db->select('SUM(tbl_outsource_items.quantity) as quantity_outsource', false);
        $this->db->from('tbl_outsource_items');
        $this->db->where('tbl_outsource_items.outsource_id', $outsource_id);
        $this->db->where('tbl_outsource_items.order_item_id', $order_item_id);
        return $this->db->get()->row_array();
    }

    public function getOrdersOutsource($outsource_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_orders_outsource');
        $this->db->where('outsource_id', $outsource_id);
        return $this->db->get()->result_array();
    }


    public function rowRefereceNoOrderByOutsourceId($outsource_id)
    {
        $query = "
            SELECT
                GROUP_CONCAT(tbl_orders.reference_no SEPARATOR '</br>') as reference_order
            FROM tbl_orders_outsource
            INNER JOIN tbl_orders ON tbl_orders_outsource.order_id = tbl_orders.id
            WHERE tbl_orders_outsource.outsource_id = $outsource_id
        ";

        return $this->db->query($query)->row_array();
    }

    public function getTransferWarehouseByOutsourceId($outsource_id)
    {
        $this->db->select('*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.outsource_id', $outsource_id);
        return $this->db->get()->row_array();
    }

    public function rowOutsourceItemsByIpOutsource($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_outsource_items');
        $this->db->where('tbl_outsource_items.id', $id);
        $this->db->where('(tbl_outsource_items.quantity - tbl_outsource_items.qty_ip_outsource) >', 0);
        return $this->db->get()->row_array();
    }

    public function getOutsourceItemsByIpOutsource($outsource_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_outsource_items');
        $this->db->where('tbl_outsource_items.outsource_id', $outsource_id);
        $this->db->where('(tbl_outsource_items.quantity - tbl_outsource_items.qty_ip_outsource) >', 0);
        return $this->db->get()->result_array();
    }

    public function insertImportOutsource($data = [])
    {
        $this->db->insert('tbl_import_outsource', $data);
        return $this->db->insert_id();
    }

    public function insertImportOutsourceItems($data = [])
    {
        $this->db->insert('tbl_import_outsource_items', $data);
        return $this->db->insert_id();
    }

    public function deleteImportOutsource($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_import_outsource');
    }

    public function deleteImportOutsourceItems($import_outsource_id)
    {
        $this->db->where('import_outsource_id', $import_outsource_id);
        return $this->db->delete('tbl_import_outsource_items');
    }

    public function updateQuantityOutsourceItems($id, $qtyIpOutsource, $option_id = 0)
    {
        $this->db->where('id', $id);
        if ($option_id == 0)
        {
            $this->db->set('qty_ip_outsource', 'COALESCE(qty_ip_outsource, 0)+'.$qtyIpOutsource, FALSE);
        }
        if ($option_id == 1)
        {
            $this->db->set('qty_ip_outsource', 'COALESCE(qty_ip_outsource, 0)-'.$qtyIpOutsource, FALSE);
        }
        return $this->db->update('tbl_outsource_items');
    }

    public function updateQuantityOutsource($id, $qtyIpOutsource, $workflow, $option_id = 0)
    {
        $this->db->where('id', $id);
        if ($option_id == 0)
        {
            $this->db->set('total_qty_ip_outsource', 'COALESCE(total_qty_ip_outsource, 0)+'.$qtyIpOutsource, FALSE);
        }
        if ($option_id == 1)
        {
            $this->db->set('total_qty_ip_outsource', 'COALESCE(total_qty_ip_outsource, 0)-'.$qtyIpOutsource, FALSE);
        }
        $this->db->set('workflow', $workflow);
        return $this->db->update('tbl_outsource');
    }

    public function rowImportOutsource($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_import_outsource');
        $this->db->where('tbl_import_outsource.id', $id);
        return $this->db->get()->row_array();
    }

    public function getImportOutsourceItems($import_outsource_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_import_outsource_items');
        $this->db->where('tbl_import_outsource_items.import_outsource_id', $import_outsource_id);
        return $this->db->get()->result_array();
    }

    public function checkExistsOutsourceInImport($outsource_id)
    {
        $this->db->from('tbl_import_outsource');
        $this->db->where('tbl_import_outsource.outsource_id', $outsource_id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function updateImportOutsource($id, $data = [])
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_import_outsource', $data);
    }

    public function searchProductAndSemiProductsAndUnit($q, $limit = 50, $type)
    {
        $this->db->select('
            CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_products.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_products.price_import as price_import,
            tbl_products.images as images
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products', $type);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchSemiProductsOutsideAndUnit($q, $limit = 50, $type = false)
    {
        $this->db->select('
            CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
            tbl_products.name as name,
            tbl_products.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_products.price_import as price_import
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products !=', 'products');
        if ($type) {
            $this->db->where_in('type_products', $type);
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchMaterialsAndUnit($q, $limit = 50)
    {
        $this->db->select('
            CONCAT("materials__", tbl_materials.id) as id,
            CONCAT(tbl_materials.code, "(", tbl_materials.name,")") as text,
            tbl_materials.name as name, tbl_materials.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_materials.price_import as price_import
            ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function searchToolsSuppliesAndUnit($term, $limit = 50)
    {
        $this->db->select('
            CONCAT("tools_supplies__", tbl_tools_supplies.id) as id,
            CONCAT(tbl_tools_supplies.code, "(", tbl_tools_supplies.name, ")") as text,
            tbl_tools_supplies.name as name,
            tbl_tools_supplies.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_tools_supplies.price_import as price_import
        ', false);
        $this->db->from('tbl_tools_supplies');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_tools_supplies.unit_id', 'left');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_tools_supplies.code', $term);
            $this->db->or_like('tbl_tools_supplies.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertOutsourceMaterial($data = [])
    {
        $this->db->insert('tbl_outsource_material', $data);
        return $this->db->insert_id();
    }

    public function deleteOutsourceMaterial($outsource_id)
    {
        $this->db->where('outsource_id', $outsource_id);
        return $this->db->delete('tbl_outsource_material');
    }

    public function getOutsourceMaterial($outsource_id)
    {
        $this->db->select('tbl_outsource_material.*');
        $this->db->from('tbl_outsource_material');
        $this->db->where('tbl_outsource_material.outsource_id', $outsource_id);
        return $this->db->get()->result_array();
    }

    public function rowTransferWarehouse($import_outsource_id)
    {
        $this->db->select('tbltransfer_warehouse.*');
        $this->db->from('tbltransfer_warehouse');
        $this->db->where('tbltransfer_warehouse.import_outsource_id', $import_outsource_id);
        return $this->db->get()->row_array();
    }

    public function countOutsourceByStatus($status_table)
    {
        $this->db->from('tbl_outsource');
        if ($status_table == "un_approved") {
            $this->db->where('tbl_outsource.status', 'un_approved');
        } else if ($status_table == "approved") {
            $this->db->where('tbl_outsource.status', 'approved');
        } else if ($status_table == "invoice_status_unpaid") {
            $this->db->where('tbl_outsource.status_pay', 0);
        } else if ($status_table == "invoice_status_not_paid_completely") {
            $this->db->where('tbl_outsource.status_pay', 1);
        } else if ($status_table == "invoice_status_paid") {
            $this->db->where('tbl_outsource.status_pay', 2);
        }
        return $this->db->get()->num_rows();
    }
    //tăng kho
    public function increaseWarehouse($id)
    {
        $outsource=get_table_where('tbl_import_outsource',array('id'=>$id),'','row');
        $this->db->select('tbl_import_outsource_items.*,SUM(quantity) as quantity_nets');
        $this->db->where('import_outsource_id',$id);
        $this->db->group_by('tbl_import_outsource_items.item_id,tbl_import_outsource_items.type_item,tbl_import_outsource_items.locaiton_to');
        $outsource->items = $this->db->get('tbl_import_outsource_items')->result_array();
        $count=0;
        if($outsource)
        {
            $date_warehouse = date('Y-m-d H:i:s');
            $warehouse_id = $outsource->warehouse_to;
            $date_import = $outsource->date;
            foreach ($outsource->items as $key => $value)
            {
                if($value['type_item'] == 'items')
                {
                    $value['type_item'] = 'items';
                }else
                if($value['type_item'] == 'materials')
                {
                    $value['type_item'] = 'nvl';
                }else
                if($value['type_item'] == 'tools_supplies')
                {
                    $value['type_item'] = 'tools';
                }else{
                    $value['type_item'] = 'product';
                }
                $localtion =  $value['locaiton_to'];
                $product_id = $value['item_id'];
                $type_items = $value['type_item'];
                $quantity = $value['quantity_nets'];
                $pirce = ($value['price']+$value['price_import']);
                $count=increaseProductQuantityimport_outsource($warehouse_id,$id,$date_warehouse,$date_import,$product_id,$quantity,$localtion,$type_items,$pirce);
                //tăng kho tổng
                increaseWarehuseQuantity($warehouse_id,$localtion,$product_id,$quantity,$type_items);
            }
        }
        if ($count) {
            return true;
        }
        return false;
    }
    //giảm kho phiếu nhập xóa dữ liệu trong kho
    public function decreaseWarehouse($id,$suppliers_id='')
    {
        if(is_numeric($id))
        {
            $warehouse_product = get_table_where("tblwarehouse_product",array('import_id'=>$id,'type_export'=>31));
            $this->db->delete('tblwarehouse_product',array('import_id'=>$id,'type_export'=>31));
            //Giảm kho tổng
            foreach ($warehouse_product as $key => $value) {
                decreaseWarehuseQuantity($value['warehouse_id'],$value['localtion'],$value['product_id'],$value['quantity'],$value['type_items']);
            }
        }
        return true;
    }

    public function updateNotFinishedStagesImportOutsourcing($items) {
      
        $arrUpdate = [];
        if (!empty($items)) {
            $pod_id = $items['pod_id'];
            $stage_id = $items['stage_id_default'];

            $this->db->select('
                tbl_productions_orders_items_stages.id as pois_id
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $this->db->where('tbl_productions_orders_items_stages.stage_id', $stage_id);
            $this->db->where('tbl_productions_orders_items_stages.active', 1);
            $pois = $this->db->get()->result_array();
            if (!empty($pois)) {
                foreach ($pois as $k => $val) {
                    $arrUpdate[] = [
                        'id' => $val['pois_id'],
                        'staff_active' => 0,
                        'date_active' => null,
                        'active' => 0,
                    ];
                }
            }
        }
        if (!empty($arrUpdate)) {
            $this->db->update_batch('tbl_productions_orders_items_stages', $arrUpdate, 'id');
        }
        return true;
    }

    public function checkExistOutsourceByPod($pod_id){
        $this->db->from('tbl_import_outsource_items');
        $this->db->where('tbl_import_outsource_items.pod_id', $pod_id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function checkExistOutsourcingByPod($pod_id, $id_stage = NULL){
        $this->db->from('tbl_outsource_items');
        $this->db->where('tbl_outsource_items.pod_id', $pod_id);
        if ($id_stage !== NULL) {
            $this->db->where('tbl_outsource_items.id_stage', $id_stage);
        }
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function updateNotFinishedStagesOutsourcing($items) {
      
        $arrUpdate = [];
        if (!empty($items)) {
            $pod_id = $items['pod_id'];
            $stage_id = $items['id_stage'];

            $this->db->select('
                tbl_productions_orders_items_stages.id as pois_id
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_items_id = tbl_productions_orders_details.productions_orders_item_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $this->db->where('tbl_productions_orders_items_stages.stage_id', $stage_id);
            $this->db->where('tbl_productions_orders_items_stages.active', 1);
            $pois = $this->db->get()->result_array();
            if (!empty($pois)) {
                foreach ($pois as $k => $val) {
                    $arrUpdate[] = [
                        'id' => $val['pois_id'],
                        'staff_active' => 0,
                        'date_active' => null,
                        'active' => 0,
                    ];
                }
            }
        }
        if (!empty($arrUpdate)) {
            $this->db->update_batch('tbl_productions_orders_items_stages', $arrUpdate, 'id');
        }
        return true;
    }

    public function getOutSourceItemsByOutsourceIdNew($outsource_id)
    {
        $this->db->select('
            tbl_outsource_items.*,
            CONCAT(tbl_orders.reference_no) as reference_no,
            CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
            tbl_orders.note as note_order
        ');
        $this->db->from('tbl_outsource_items');
        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.id = tbl_outsource_items.pod_id', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
        $this->db->where('outsource_id', $outsource_id);
        return $this->db->get()->result_array();
    }
}