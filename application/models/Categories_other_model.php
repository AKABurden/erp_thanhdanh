<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Categories_other_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertStandard($data)
    {
        $this->db->insert('tbl_standard', $data);
        return $this->db->insert_id();
    }

    public function updateStandard($id, $data)
    {
        $this->db->where('tbl_standard.id', $id);
        return $this->db->update('tbl_standard', $data);
    }

    public function deleteStandard($id) {
        $this->db->where('tbl_standard.id', $id);
        return $this->db->delete('tbl_standard');
    }

    public function getStandardById($id) {
        $this->db->select('*');
        $this->db->from('tbl_standard');
        $this->db->where('tbl_standard.id', $id);
        return $this->db->get()->row_array();
    }

    //certification
    public function insertCertification($data)
    {
        $this->db->insert('tbl_certification', $data);
        return $this->db->insert_id();
    }

    public function updateCertification($id, $data)
    {
        $this->db->where('tbl_certification.id', $id);
        return $this->db->update('tbl_certification', $data);
    }

    public function deleteCertification($id) {
        $this->db->where('tbl_certification.id', $id);
        return $this->db->delete('tbl_certification');
    }

    public function getCertificationById($id) {
        $this->db->select('*');
        $this->db->from('tbl_certification');
        $this->db->where('tbl_certification.id', $id);
        return $this->db->get()->row_array();
    }

    //discount
    public function insertDiscount($data)
    {
        $this->db->insert('tbl_discount', $data);
        return $this->db->insert_id();
    }

    public function updateDiscount($id, $data)
    {
        $this->db->where('tbl_discount.id', $id);
        return $this->db->update('tbl_discount', $data);
    }

    public function deleteDiscount($id) {
        $this->db->where('tbl_discount.id', $id);
        return $this->db->delete('tbl_discount');
    }

    public function getDiscountById($id) {
        $this->db->select('*');
        $this->db->from('tbl_discount');
        $this->db->where('tbl_discount.id', $id);
        return $this->db->get()->row_array();
    }

    //type_orders_items
    public function insertTypeOrdersItems($data)
    {
        $this->db->insert('tbltype_orders_items', $data);
        return $this->db->insert_id();
    }

    public function updateTypeOrdersItems($id, $data)
    {
        $this->db->where('tbltype_orders_items.id', $id);
        return $this->db->update('tbltype_orders_items', $data);
    }

    public function deleteTypeOrdersItems($id) {
        $this->db->where('tbltype_orders_items.id', $id);
        return $this->db->delete('tbltype_orders_items');
    }

    public function getTypeOrdersItemsById($id) {
        $this->db->select('*');
        $this->db->from('tbltype_orders_items');
        $this->db->where('tbltype_orders_items.id', $id);
        return $this->db->get()->row_array();
    }

    public function checkUseTypeOrdersItems($id) {
        $this->db->from('tbl_orders');
        $this->db->where('tbl_orders.type_items', $id);
        $this->db->limit(1);
        $isOrders = $this->db->get()->num_rows();
        if ($isOrders) {
            return true;
        }

        return false;
    }

    //classify
    public function insertClassify($data)
    {
        $this->db->insert('tbl_classify', $data);
        return $this->db->insert_id();
    }

    public function updateClassify($id, $data)
    {
        $this->db->where('tbl_classify.id', $id);
        return $this->db->update('tbl_classify', $data);
    }

    public function deleteClassify($id) {
        $this->db->where('tbl_classify.id', $id);
        return $this->db->delete('tbl_classify');
    }

    public function getClassifyById($id) {
        $this->db->select('*');
        $this->db->from('tbl_classify');
        $this->db->where('tbl_classify.id', $id);
        return $this->db->get()->row_array();
    }

    //planning group
    public function insertPlanningGroup($data)
    {
        $this->db->insert('tbl_planning_group', $data);
        return $this->db->insert_id();
    }

    public function updatePlanningGroup($id, $data)
    {
        $this->db->where('tbl_planning_group.id', $id);
        return $this->db->update('tbl_planning_group', $data);
    }

    public function deletePlanningGroup($id) {
        $this->db->where('tbl_planning_group.id', $id);
        return $this->db->delete('tbl_planning_group');
    }

    public function getPlanningGroupById($id) {
        $this->db->select('*');
        $this->db->from('tbl_planning_group');
        $this->db->where('tbl_planning_group.id', $id);
        return $this->db->get()->row_array();
    }

    //type plan
    public function insertTypePlan($data)
    {
        $this->db->insert('tbl_type_plan', $data);
        return $this->db->insert_id();
    }

    public function updateTypePlan($id, $data)
    {
        $this->db->where('tbl_type_plan.id', $id);
        return $this->db->update('tbl_type_plan', $data);
    }

    public function deleteTypePlan($id) {
        $this->db->where('tbl_type_plan.id', $id);
        return $this->db->delete('tbl_type_plan');
    }

    public function getTypePlanById($id) {
        $this->db->select('*');
        $this->db->from('tbl_type_plan');
        $this->db->where('tbl_type_plan.id', $id);
        return $this->db->get()->row_array();
    }

    //conversion_formula
    public function insertConversionFormula($data)
    {
        $this->db->insert('tbl_conversion_formula', $data);
        return $this->db->insert_id();
    }

    public function updateConversionFormula($id, $data)
    {
        $this->db->where('tbl_conversion_formula.id', $id);
        return $this->db->update('tbl_conversion_formula', $data);
    }

    public function deleteConversionFormula($id) {
        $this->db->where('tbl_conversion_formula.id', $id);
        return $this->db->delete('tbl_conversion_formula');
    }

    public function getConversionFormulaById($id) {
        $this->db->select('*');
        $this->db->from('tbl_conversion_formula');
        $this->db->where('tbl_conversion_formula.id', $id);
        return $this->db->get()->row_array();
    }

    //materials equipment
    public function insertMaterialsEquipment($data)
    {
        $this->db->insert('tbl_materials_equipment', $data);
        return $this->db->insert_id();
    }

    public function updateMaterialsEquipment($id, $data)
    {
        $this->db->where('tbl_materials_equipment.id', $id);
        return $this->db->update('tbl_materials_equipment', $data);
    }

    public function deleteMaterialsEquipment($id) {
        $this->db->where('tbl_materials_equipment.id', $id);
        return $this->db->delete('tbl_materials_equipment');
    }

    public function getMaterialsEquipmentById($id) {
        $this->db->select('*');
        $this->db->from('tbl_materials_equipment');
        $this->db->where('tbl_materials_equipment.id', $id);
        return $this->db->get()->row_array();
    }

    public function getRoles() {
        $dtStaff = "(
            SELECT
                tblstaff.role as role_id,
                GROUP_CONCAT(CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) SEPARATOR '__') as fullname
            FROM tblstaff
            WHERE tblstaff.active = 1 AND tblstaff.role != 0
            GROUP BY tblstaff.role
        ) tb_staffs";

        $this->db->select('
            tblroles.roleid as roleid,
            tblroles.code_role as code,
            tblroles.name as name,
            tb_staffs.fullname as fullname
        ', false);
        $this->db->from('tblroles');
        $this->db->join($dtStaff, 'tb_staffs.role_id = tblroles.roleid', 'left');
        $this->db->where('tblroles.active_role', 1);
        return $this->db->get()->result_array();
    }

    //materials special
    public function insertMaterialsSpecial($data)
    {
        $this->db->insert('tbl_materials_special', $data);
        return $this->db->insert_id();
    }

    public function updateMaterialsSpecial($id, $data)
    {
        $this->db->where('tbl_materials_special.id', $id);
        return $this->db->update('tbl_materials_special', $data);
    }

    public function deleteMaterialsSpecial($id) {
        $this->db->where('tbl_materials_special.id', $id);
        return $this->db->delete('tbl_materials_special');
    }

    public function getMaterialsSpecialById($id) {
        $this->db->select('*');
        $this->db->from('tbl_materials_special');
        $this->db->where('tbl_materials_special.id', $id);
        return $this->db->get()->row_array();
    }

    //storage time
    public function insertStorageTime($data)
    {
        $this->db->insert('tbl_storage_time', $data);
        return $this->db->insert_id();
    }

    public function updateStorageTime($id, $data)
    {
        $this->db->where('tbl_storage_time.id', $id);
        return $this->db->update('tbl_storage_time', $data);
    }

    public function deleteStorageTime($id) {
        $this->db->where('tbl_storage_time.id', $id);
        return $this->db->delete('tbl_storage_time');
    }

    public function getStorageTimeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_storage_time');
        $this->db->where('tbl_storage_time.id', $id);
        return $this->db->get()->row_array();
    }

    //unit warehouse
    public function insertUnitWarehouse($data)
    {
        $this->db->insert('tbl_unit_warehouse', $data);
        return $this->db->insert_id();
    }

    public function updateUnitWarehouse($id, $data)
    {
        $this->db->where('tbl_unit_warehouse.id', $id);
        return $this->db->update('tbl_unit_warehouse', $data);
    }

    public function deleteUnitWarehouse($id) {
        $this->db->where('tbl_unit_warehouse.id', $id);
        return $this->db->delete('tbl_unit_warehouse');
    }

    public function getUnitWarehouseById($id) {
        $this->db->select('*');
        $this->db->from('tbl_unit_warehouse');
        $this->db->where('tbl_unit_warehouse.id', $id);
        return $this->db->get()->row_array();
    }

    //inventory type
    public function insertInventoryType($data)
    {
        $this->db->insert('tbl_inventory_type', $data);
        return $this->db->insert_id();
    }

    public function updateInventoryType($id, $data)
    {
        $this->db->where('tbl_inventory_type.id', $id);
        return $this->db->update('tbl_inventory_type', $data);
    }

    public function deleteInventoryType($id) {
        $this->db->where('tbl_inventory_type.id', $id);
        return $this->db->delete('tbl_inventory_type');
    }

    public function getInventoryTypeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_inventory_type');
        $this->db->where('tbl_inventory_type.id', $id);
        return $this->db->get()->row_array();
    }

    //inventory group
    public function insertInventoryGroup($data)
    {
        $this->db->insert('tbl_inventory_group', $data);
        return $this->db->insert_id();
    }

    public function updateInventoryGroup($id, $data)
    {
        $this->db->where('tbl_inventory_group.id', $id);
        return $this->db->update('tbl_inventory_group', $data);
    }

    public function deleteInventoryGroup($id) {
        $this->db->where('tbl_inventory_group.id', $id);
        return $this->db->delete('tbl_inventory_group');
    }

    public function getInventoryGroupById($id) {
        $this->db->select('*');
        $this->db->from('tbl_inventory_group');
        $this->db->where('tbl_inventory_group.id', $id);
        return $this->db->get()->row_array();
    }

    //import export code
    public function insertImportExportCode($data)
    {
        $this->db->insert('tbl_import_export_code', $data);
        return $this->db->insert_id();
    }

    public function updateImportExportCode($id, $data)
    {
        $this->db->where('tbl_import_export_code.id', $id);
        return $this->db->update('tbl_import_export_code', $data);
    }

    public function deleteImportExportCode($id) {
        $this->db->where('tbl_import_export_code.id', $id);
        return $this->db->delete('tbl_import_export_code');
    }

    public function getImportExportCodeById($id) {
        $this->db->select('*');
        $this->db->from('tbl_import_export_code');
        $this->db->where('tbl_import_export_code.id', $id);
        return $this->db->get()->row_array();
    }

    //packaging standards
    public function insertPackagingStandards($data)
    {
        $this->db->insert('tbl_packaging_standards', $data);
        return $this->db->insert_id();
    }

    public function updatePackagingStandards($id, $data)
    {
        $this->db->where('tbl_packaging_standards.id', $id);
        return $this->db->update('tbl_packaging_standards', $data);
    }

    public function deletePackagingStandards($id) {
        $this->db->where('tbl_packaging_standards.id', $id);
        return $this->db->delete('tbl_packaging_standards');
    }

    public function getPackagingStandardsById($id) {
        $this->db->select('*');
        $this->db->from('tbl_packaging_standards');
        $this->db->where('tbl_packaging_standards.id', $id);
        return $this->db->get()->row_array();
    }

    //vehicle
    public function insertVehicle($data)
    {
        $this->db->insert('tbl_vehicle', $data);
        return $this->db->insert_id();
    }

    public function updateVehicle($id, $data)
    {
        $this->db->where('tbl_vehicle.id', $id);
        return $this->db->update('tbl_vehicle', $data);
    }

    public function deleteVehicle($id) {
        $this->db->where('tbl_vehicle.id', $id);
        return $this->db->delete('tbl_vehicle');
    }

    public function getVehicleById($id) {
        $this->db->select('*');
        $this->db->from('tbl_vehicle');
        $this->db->where('tbl_vehicle.id', $id);
        return $this->db->get()->row_array();
    }

    //cleaning
    public function insertCleaning($data)
    {
        $this->db->insert('tbl_cleaning', $data);
        return $this->db->insert_id();
    }

    public function updateCleaning($id, $data)
    {
        $this->db->where('tbl_cleaning.id', $id);
        return $this->db->update('tbl_cleaning', $data);
    }

    public function deleteCleaning($id) {
        $this->db->where('tbl_cleaning.id', $id);
        return $this->db->delete('tbl_cleaning');
    }

    public function getCleaningById($id) {
        $this->db->select('*');
        $this->db->from('tbl_cleaning');
        $this->db->where('tbl_cleaning.id', $id);
        return $this->db->get()->row_array();
    }

    //maintenance group
    public function insertMaintenanceGroup($data)
    {
        $this->db->insert('tbl_maintenance_group', $data);
        return $this->db->insert_id();
    }

    public function updateMaintenanceGroup($id, $data)
    {
        $this->db->where('tbl_maintenance_group.id', $id);
        return $this->db->update('tbl_maintenance_group', $data);
    }

    public function deleteMaintenanceGroup($id) {
        $this->db->where('tbl_maintenance_group.id', $id);
        return $this->db->delete('tbl_maintenance_group');
    }

    public function getMaintenanceGroupById($id) {
        $this->db->select('*');
        $this->db->from('tbl_maintenance_group');
        $this->db->where('tbl_maintenance_group.id', $id);
        return $this->db->get()->row_array();
    }

    //relate
    public function insertRelate($data)
    {
        $this->db->insert('tbl_relate', $data);
        return $this->db->insert_id();
    }

    public function updateRelate($id, $data)
    {
        $this->db->where('tbl_relate.id', $id);
        return $this->db->update('tbl_relate', $data);
    }

    public function deleteRelate($id) {
        $this->db->where('tbl_relate.id', $id);
        return $this->db->delete('tbl_relate');
    }

    public function getRelateById($id) {
        $this->db->select('*');
        $this->db->from('tbl_relate');
        $this->db->where('tbl_relate.id', $id);
        return $this->db->get()->row_array();
    }

    public function checkRelate($id) {
        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.recommended_list_group_id', $id);
        $this->db->limit(1);
        $isOrders = $this->db->get()->num_rows();
        if ($isOrders) {
            return true;
        }

        return false;
    }

    //Inspection_criteria
    public function insertInspection_criteria($data)
    {
        $this->db->insert('tbl_inspection_criteria', $data);
        return $this->db->insert_id();
    }

    public function updateInspection_criteria($id, $data)
    {
        $this->db->where('tbl_inspection_criteria.id', $id);
        return $this->db->update('tbl_inspection_criteria', $data);
    }

    public function deleteInspection_criteria($id) {
        $this->db->where('tbl_inspection_criteria.id', $id);
        return $this->db->delete('tbl_inspection_criteria');
    }

    public function getInspection_criteriaById($id) {
        $this->db->select('*');
        $this->db->from('tbl_inspection_criteria');
        $this->db->where('tbl_inspection_criteria.id', $id);
        return $this->db->get()->row_array();
    }

    //process catalog
    public function insertProcessCatalog($data)
    {
        $this->db->insert('tbl_process_catalog', $data);
        return $this->db->insert_id();
    }

    public function updateProcessCatalog($id, $data)
    {
        $this->db->where('tbl_process_catalog.id', $id);
        return $this->db->update('tbl_process_catalog', $data);
    }

    public function deleteProcessCatalog($id) {
        $this->db->where('tbl_process_catalog.id', $id);
        return $this->db->delete('tbl_process_catalog');
    }

    public function getProcessCatalogById($id) {
        $this->db->select('*');
        $this->db->from('tbl_process_catalog');
        $this->db->where('tbl_process_catalog.id', $id);
        return $this->db->get()->row_array();
    }
}