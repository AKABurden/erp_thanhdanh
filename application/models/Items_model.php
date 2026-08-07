<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Items_model extends App_Model
{
	private $contact_columns;

    public function __construct()
    {
        parent::__construct();

        // $this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);

        // $this->load->model(['client_vault_entries_model', 'client_groups_model', 'statement_model']);
    }

    public function insertCategoryItems($data)
    {
    	$this->db->insert('_category_items', $data);
    	return $this->db->insert_id();
    }

    public function rowCategoryItems($id)
    {
        $this->db->select('*');
        $this->db->from('_category_items');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function updateCategoryItems($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('_category_items', $data);
    }

    public function deleteCategoryItems($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('_category_items');
    }

    public function searchCategory($q, $limit = 50)
    {
        $this->db->select('tbl_category_items.id as id, tbl_category_items.name as name', false);
        $this->db->from('tbl_category_items');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_category_items.code', $q);
            $this->db->or_like('tbl_category_items.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertMaterials($data)
    {
        $this->db->insert('tbl_materials', $data);
        return $this->db->insert_id();
    }

    public function updateMaterials($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_materials', $data);
    }

    public function deleteMaterials($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_materials');
    }

    public function rowMaterial($id)
    {
        $this->db->select('tbl_materials.*, tbl_category_items.name as category_name, tbl_category_items.code as category_code,tbl_species.name as species_name', false);
        $this->db->from('tbl_materials');
        $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_materials.species', 'left');
        $this->db->where('tbl_materials.id', $id);
        return $this->db->get()->row_array();
    }

    public function checkExistCategory($id)
    {
        $this->db->from('tbl_materials');
        $this->db->where('category_id', $id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function searchMaterials($q, $limit = 50)
    {
        $this->db->select('tbl_materials.id as id, CONCAT(tbl_materials.name, "(", tbl_materials.code, ")") as name', false);
        $this->db->from('tbl_materials');
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

    public function searchSelect2Materials($q, $limit = 50, $params = [])
    {
        $this->db->select('tbl_materials.id as id, CONCAT(tbl_materials.name, "(", tbl_materials.code, ")") as text', false);
        $this->db->from('tbl_materials');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_materials.code', $q);
            $this->db->or_like('tbl_materials.name', $q);
            $this->db->group_end();
        }

        if (!empty($params)) {
            $category = !empty($params['category_id_search']) ? $params['category_id_search'] : 0;
            if (!empty($category)) {
                $this->db->where('tbl_materials.category_id', $category);
            }
        }

        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertExchangeItems($data)
    {
        return $this->db->insert_batch('tbl_exchange_items', $data);
        // return $this->db->insert_id();
    }

    public function getExchangeItemsByItemId($item_id)
    {
        $this->db->select('tbl_exchange_items.*');
        $this->db->from('tbl_exchange_items');
        $this->db->where('tbl_exchange_items.item_id', $item_id);
        return $this->db->get()->result_array();
    }

    public function deleteExchangeByItemId($item_id)
    {
        $this->db->where('item_id', $item_id);
        return $this->db->delete('tbl_exchange_items');
    }

    public function checkCategoryItemsByCode($code)
    {
        $this->db->from('tbl_category_items');
        $this->db->where('tbl_category_items.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function checkMaterialsByCode($code)
    {
        $this->db->from('tbl_materials');
        $this->db->where('tbl_materials.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function rowCategoryItemsByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_category_items');
        if ($option == "like") {
            $this->db->like('tbl_category_items.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_category_items.code', $code);
        }
        return $this->db->get()->row_array();
    }

    public function checkMaterialUse($item_id)
    {
        $this->db->from('tbl_element_items');
        $this->db->where('tbl_element_items.item_id', $item_id);
        $this->db->where('tbl_element_items.type', 'materials');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        $this->db->from('tbl_productions_capacity_items');
        $this->db->where('tbl_productions_capacity_items.items_id', $item_id);
        $this->db->where('tbl_productions_capacity_items.type_items', 'materials');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        $this->db->from('tblpurchase_order_items');
        $this->db->where('tblpurchase_order_items.product_id', $item_id);
        $this->db->where('tblpurchase_order_items.type', 'nvl');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        $this->db->from('tblimport_items');
        $this->db->where('tblimport_items.product_id', $item_id);
        $this->db->where('tblimport_items.type', 'nvl');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        $this->db->from('tblpurchases_items');
        $this->db->where('tblpurchases_items.product_id', $item_id);
        $this->db->where('tblpurchases_items.type', 'nvl');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        $this->db->from('tblinventory_items');
        $this->db->where('tblinventory_items.product_id', $item_id);
        $this->db->where('tblinventory_items.type', 'nvl');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        $this->db->from('tbl_order_items');
        $this->db->where('tbl_order_items.item_id', $item_id);
        $this->db->where('tbl_order_items.type_item', 'materials');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if ($q) {
            return $q;
        }

        $this->db->from('tbl_suggest_exporting_items');
        $this->db->where('tbl_suggest_exporting_items.item_id', $item_id);
        $this->db->where('tbl_suggest_exporting_items.type_item', 'materials');
        $this->db->limit(1);
        $q = $this->db->get()->num_rows();
        if (!empty($q)) {
            return $q;
        }

        return $q;
    }

    //tnh
    public function rowItems($id)
    {
        $this->db->select('*');
        $this->db->from('tblitems');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function checkParentId($id)
    {
        $this->db->from('tbl_category_items');
        $this->db->where('tbl_category_items.parent_id', $id);
        return $this->db->get()->num_rows();
    }

    public function getExchangeItemsViewByItemId($item_id)
    {
        $this->db->select('tbl_exchange_items.*, tblunits.unit as unit_name');
        $this->db->from('tbl_exchange_items');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_exchange_items.unit_id', 'left');
        $this->db->where('tbl_exchange_items.item_id', $item_id);
        return $this->db->get()->result_array();
    }

    public function insertBatchMaterialSuppliers($data)
    {
        return $this->db->insert_batch('tbl_material_suppliers', $data);
    }

    public function insertBatchMaterialWarehouse($data)
    {
        return $this->db->insert_batch('tbl_material_warehouse', $data);
    }

    public function deleteMaterialSuppliersByMaterialId($material_id)
    {
        $this->db->where('material_id', $material_id);
        return $this->db->delete('tbl_material_suppliers');
    }

    public function deleteMaterialWarehouseByMaterialId($material_id)
    {
        $this->db->where('material_id', $material_id);
        return $this->db->delete('tbl_material_warehouse');
    }

    public function getMaterialSuppliers($material_id)
    {
        $this->db->select('tbl_material_suppliers.*, tblsuppliers.company as supplier_company, tblprocedure_client_detail.name as procedure_detail_name', false);
        $this->db->from('tbl_material_suppliers');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_material_suppliers.supplier_id');
        $this->db->join('tblprocedure_client_detail', 'tblprocedure_client_detail.id = tbl_material_suppliers.procedure_id');
        $this->db->where('tbl_material_suppliers.material_id', $material_id);
        return $this->db->get()->result_array();
    }

    public function getGroupMaterialsuppliers($material_id) {
        $this->db->select('tblsuppliers.company as supplier_company, tbl_material_suppliers.supplier_id');
        $this->db->from('tbl_material_suppliers');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_material_suppliers.supplier_id');
        $this->db->where('tbl_material_suppliers.material_id', $material_id);
        $this->db->group_by('tbl_material_suppliers.supplier_id');
        $this->db->order_by('tbl_material_suppliers.id ASC');
        return $this->db->get()->result_array();
    }

    public function getMaterialSuppliersByMaterialAndSupplier($material_id, $supplier_id)
    {
        $this->db->select('tbl_material_suppliers.*, tblprocedure_client_detail.name as procedure_detail_name', false);
        $this->db->from('tbl_material_suppliers');
        $this->db->join('tblprocedure_client_detail', 'tblprocedure_client_detail.id = tbl_material_suppliers.procedure_id');
        $this->db->where('tbl_material_suppliers.material_id', $material_id);
        $this->db->where('tbl_material_suppliers.supplier_id', $supplier_id);
        $this->db->order_by('tbl_material_suppliers.sequence', 'asc');
        return $this->db->get()->result_array();
    }

    public function getMaterialWarehouse($material_id)
    {
        $this->db->select('tbl_material_warehouse.*, tblwarehouse.name as warehouse_name, tbllocaltion_warehouses.name as location_name');
        $this->db->from('tbl_material_warehouse');
        $this->db->join('tblwarehouse', 'tblwarehouse.id = tbl_material_warehouse.warehouse_id');
        $this->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.id = tbl_material_warehouse.location_id');
        $this->db->where('tbl_material_warehouse.material_id', $material_id);
        return $this->db->get()->result_array();
    }

    public function getMaterialSuppliersByMaterialId($material_id)
    {
        $this->db->select('tbl_material_suppliers.*', false);
        $this->db->from('tbl_material_suppliers');
        $this->db->where('tbl_material_suppliers.material_id', $material_id);
        return $this->db->get()->result_array();
    }

    public function isMaterialInBOM($type, $item_id)
    {
        $this->db->from('tbl_boms_element_items');
        $this->db->where('tbl_boms_element_items.type', $type);
        $this->db->where('tbl_boms_element_items.item_id', $item_id);
        $this->db->limit(1);
        $dk1 = $this->db->get()->num_rows();

        $this->db->from('tbl_boms_element_items_replace');
        $this->db->where('tbl_boms_element_items_replace.type_replace', $type);
        $this->db->where('tbl_boms_element_items_replace.item_id_replace', $item_id);
        $this->db->limit(1);
        $dk2 = $this->db->get()->num_rows();

        $this->db->from('tbl_element_items');
        $this->db->where('tbl_element_items.type', $type);
        $this->db->where('tbl_element_items.item_id', $item_id);
        $this->db->limit(1);
        $dk3 = $this->db->get()->num_rows();

        $this->db->from('tbl_element_items_replace');
        $this->db->where('tbl_element_items_replace.type_replace', $type);
        $this->db->where('tbl_element_items_replace.item_id_replace', $item_id);
        $this->db->limit(1);
        $dk4 = $this->db->get()->num_rows();

        return $dk1 + $dk2 + $dk3 + $dk4;
    }

    public function rowMaterialsByCode($code)
    {
        $this->db->select('tbl_materials.id, tbl_materials.code');
        $this->db->from('tbl_materials');
        $this->db->where('tbl_materials.code', $code);
        $this->db->limit(1);
        return $this->db->get()->row_array();
    }

    public function getSuppliersByCodeName($code) {
        $this->db->select();
        $this->db->from('tblsuppliers');
        $this->db->group_start();
        $this->db->where('tblsuppliers.code', $code);
        $this->db->or_where('tblsuppliers.company', $code);
        $this->db->group_end();
        return $this->db->get()->row_array();
    }
}