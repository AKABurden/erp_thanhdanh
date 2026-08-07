<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Categories_maintenance_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertCategoriesMaintenance($data)
    {
        $this->db->insert('tbl_categories_maintenance', $data);
        return $this->db->insert_id();
    }

    public function updateCategoriesMaintenance($id, $data)
    {
        $this->db->where('tbl_categories_maintenance.id', $id);
        return $this->db->update('tbl_categories_maintenance', $data);
    }

    public function deleteCategoriesMaintenance($id) {
        $this->db->where('tbl_categories_maintenance.id', $id);
        return $this->db->delete('tbl_categories_maintenance');
    }

    public function getCategoriesMaintenanceById($id) {
        $this->db->select('*');
        $this->db->from('tbl_categories_maintenance');
        $this->db->where('tbl_categories_maintenance.id', $id);
        return $this->db->get()->row_array();
    }
    //
    public function insertEquipmentConsumption($data)
    {
        $this->db->insert('tbl_equipment_consumption', $data);
        return $this->db->insert_id();
    }

    public function updateEquipmentConsumption($id, $data)
    {
        $this->db->where('tbl_equipment_consumption.id', $id);
        return $this->db->update('tbl_equipment_consumption', $data);
    }

    public function deleteEquipmentConsumption($id) {
        $this->db->where('tbl_equipment_consumption.id', $id);
        return $this->db->delete('tbl_equipment_consumption');
    }

    public function getEquipmentConsumptionById($id) {
        $this->db->select('*');
        $this->db->from('tbl_equipment_consumption');
        $this->db->where('tbl_equipment_consumption.id', $id);
        return $this->db->get()->row_array();
    }
    //
    public function insertImportedExport($data)
    {
        $this->db->insert('tbl_import_export', $data);
        return $this->db->insert_id();
    }

    public function updateImportedExport($id, $data)
    {
        $this->db->where('tbl_import_export.id', $id);
        return $this->db->update('tbl_import_export', $data);
    }

    public function deleteImportedExport($id) {
        $this->db->where('tbl_import_export.id', $id);
        return $this->db->delete('tbl_import_export');
    }

    public function getImportedExportById($id) {
        $this->db->select('*');
        $this->db->from('tbl_import_export');
        $this->db->where('tbl_import_export.id', $id);
        return $this->db->get()->row_array();
    }
}