<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertKpiCriteria($data)
    {
        $this->db->insert('tbl_kpi_criteria', $data);
        return $this->db->insert_id();
    }

    public function updateKpiCriteria($id, $data)
    {
        $this->db->where('tbl_kpi_criteria.id', $id);
        return $this->db->update('tbl_kpi_criteria', $data);
    }

    public function deleteKpiCriteria($id) {
        $this->db->where('tbl_kpi_criteria.id', $id);
        return $this->db->delete('tbl_kpi_criteria');
    }

    public function getKpiCriteriaById($id) {
        $this->db->select('*');
        $this->db->from('tbl_kpi_criteria');
        $this->db->where('tbl_kpi_criteria.id', $id);
        return $this->db->get()->row_array();
    }

    public function getKpiCriteria() {
        $this->db->select('*');
        $this->db->from('tbl_kpi_criteria');
        return $this->db->get()->result_array();
    }

    public function insertBatchKpiCriteriaDepartment($data) {
        return $this->db->insert_batch('tbl_kpi_criteria_department', $data);
    }

    public function insertBatchKpiCriteriaRoles($data) {
        return $this->db->insert_batch('tbl_kpi_criteria_roles', $data);
    }

    public function deleteKpiCriteriaDepartment($kpi_criteria_id) {
        $this->db->where('tbl_kpi_criteria_department.kpi_criteria_id', $kpi_criteria_id);
        return $this->db->delete('tbl_kpi_criteria_department');
    }

    public function deleteKpiCriteriaRoles($kpi_criteria_id) {
        $this->db->where('tbl_kpi_criteria_roles.kpi_criteria_id', $kpi_criteria_id);
        return $this->db->delete('tbl_kpi_criteria_roles');
    }

    public function getDepartments() {
        $this->db->select('*');
        $this->db->from('tbldepartments');
        $this->db->where('active_departments', 1);
        return $this->db->get()->result_array();
    }

    public function getRoles() {
        $this->db->select('*');
        $this->db->from('tblroles');
        return $this->db->get()->result_array();
    }

    public function getKpiCriteriaDepartment($kpi_criteria_id) {
        $this->db->select('tbl_kpi_criteria_department.*');
        $this->db->from('tbl_kpi_criteria_department');
        $this->db->where('tbl_kpi_criteria_department.kpi_criteria_id', $kpi_criteria_id);
        return $this->db->get()->result_array();
    }

    public function getKpiCriteriaRoles($kpi_criteria_id) {
        $this->db->select('tbl_kpi_criteria_roles.*');
        $this->db->from('tbl_kpi_criteria_roles');
        $this->db->where('tbl_kpi_criteria_roles.kpi_criteria_id', $kpi_criteria_id);
        return $this->db->get()->result_array();
    }

    public function insertKpi($data) {
        $this->db->insert('tbl_kpi', $data);
        return $this->db->insert_id();
    }

    public function updateKpi($id, $data) {
        $this->db->where('tbl_kpi.id', $id);
        return $this->db->update('tbl_kpi', $data);
    }

    public function insertBatchKpiItems($data) {
        return $this->db->insert_batch('tbl_kpi_items', $data);
    }

    public function deleteKpi($id) {
        $this->db->where('tbl_kpi.id', $id);
        return $this->db->delete('tbl_kpi');
    }

    public function deleteKpiItems($kpi_id) {
        $this->db->where('tbl_kpi_items.kpi_id', $kpi_id);
        return $this->db->delete('tbl_kpi_items');
    }

    public function isKpiCriteria($id) {
        $this->db->from('tbl_kpi_items');
        $this->db->where('tbl_kpi_items.kpi_criteria_id', $id);
        $rs = $this->db->count_all_results();
        if ($rs) {
            return $rs;
        }
        return false;
    }

    public function getKpiById($id, $where = []) {
        $this->db->select('*');
        $this->db->from('tbl_kpi');
        $this->db->where('tbl_kpi.id', $id);
        if (!empty($where)) {
            $this->db->where($where, false, false);
        }
        return $this->db->get()->row_array();
    }

    public function getKpiItems($kpi_id, $kpi_criteria_id) {
        $this->db->select('*');
        $this->db->from('tbl_kpi_items');
        $this->db->where('tbl_kpi_items.kpi_id', $kpi_id);
        $this->db->where('tbl_kpi_items.kpi_criteria_id', $kpi_criteria_id);
        return $this->db->get()->row_array();
    }

    public function getViolationRecords($kpi_criteria, $month, $year) {
        $month_year = $year.'-'.$month;

        $this->db->select('
            COUNT(tblviolation_records.id) as count_violation_records
        ', false);
        $this->db->from('tblviolation_records');
        $this->db->where('tblviolation_records.kpi_criteria', $kpi_criteria);
        $this->db->where('DATE_FORMAT(tblviolation_records.date, "%Y-%m") = "'.$month_year.'"', false, false);
        $this->db->where('tblviolation_records.cal_kpi', 1);
        $this->db->where('tblviolation_records.status', 1);
        $this->db->where('tblviolation_records.status_staff', 1);
        return $this->db->get()->row_array();
    }

    public function getRoleDepartment($departments_id = []) {
        $this->db->select('tblroles.*');
        $this->db->from('tblroles');
        if (!empty($departments_id)) {
            $this->db->where_in('tblroles.departments_id', $departments_id);
        } else {
            $this->db->where('tblroles.roleid', 0);
        }
        return $this->db->get()->result_array();
    }

    public function getWhereKpiCriteria($where = [], $stringSql = false) {

        $staff_id = get_staff_user_id();

        array_push($where, ' AND (tbl_kpi_criteria.created_by = '.$staff_id.') ');
        if ($stringSql) {
            $where = stringWhere($where);
        }

        return $where;
    }

    public function getWhereKpi($where = [], $stringSql = false) {

        $arrIDStaff = employee_manage_staff();
        $staff_id = get_staff_user_id();
        $this->db->select('
            GROUP_CONCAT(distinct tblstaff_departments.departmentid) as departmentid
        ', false);
        $this->db->from('tblstaff_departments');
        $this->db->where('tblstaff_departments.staffid', $staff_id);
        $staff_departments = $this->db->get()->row_array();
        $departmentid = $staff_departments['departmentid'];
        $whereStaffDepartments = '';
        if ($departmentid) {
            $whereStaffDepartments.= ' OR (tbl_kpi.staff IN ('.$departmentid.') AND tbl_kpi.type_kpi = 2)';
        }

        $whereManageStaff = '';
        if (!empty($arrIDStaff)) {
            $coverStr = implode(",", $arrIDStaff);
            $whereManageStaff = ' OR (tbl_kpi.staff IN ('.$coverStr.') AND tbl_kpi.type_kpi = 1) OR tbl_kpi.created_by IN ('.$coverStr.') ';
        }

        array_push($where, ' AND ((tbl_kpi.staff = '.$staff_id.' AND tbl_kpi.type_kpi = 1) OR tbl_kpi.created_by = '.$staff_id.' '.$whereStaffDepartments.' '.$whereManageStaff.') ');
        if ($stringSql) {
            $where = stringWhere($where);
        }

        return $where;
    }

    public function getStaffById($id) {
        $staffDepartments = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department 
            FROM tblstaff_departments
            INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_staff_departments";
        
        $tbStaff = "(
            SELECT 
                tblstaff.staffid as staffid,
                tblstaff.firstname as firstname,
                tblstaff.lastname as lastname,
                CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as fullname,
                tblroles.name as name_role,
                tb_staff_departments.name_department as name_department
            FROM tblstaff
            LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role
            LEFT JOIN $staffDepartments ON tb_staff_departments.staffid = $id
            WHERE tblstaff.staffid = $id
        )";

        return $this->db->query($tbStaff)->row_array();
    }

    public function getDepartmentsById($departmentid) {
        $this->db->select('tbldepartments.*', false);
        $this->db->from('tbldepartments');
        $this->db->where('tbldepartments.departmentid', $departmentid);
        return $this->db->get()->row_array();
    }

    public function insertKpiTroubleViolation($data) {
        $this->db->insert('tbl_kpi_trouble_violation', $data);
        return $this->db->insert_id();
    }

    public function insertBatchKpiTroubleViolationItems($data) {
        return $this->db->insert_batch('tbl_kpi_trouble_violation_items', $data);
    }

    public function deleteKpiTroubleViolation($kpi_id) {
        $this->db->where('tbl_kpi_trouble_violation.kpi_id', $kpi_id);
        return $this->db->delete('tbl_kpi_trouble_violation');
    }

    public function deleteKpiTroubleViolationItems($kpi_id) {
        $this->db->where('tbl_kpi_trouble_violation_items.kpi_id', $kpi_id);
        return $this->db->delete('tbl_kpi_trouble_violation_items');
    }

    public function insertBatchKpiBonus($data) {
        return $this->db->insert_batch('tbl_kpi_bonus', $data);
    }

    public function deleteKpiBonus($kpi_id) {
        $this->db->where('tbl_kpi_bonus.kpi_id', $kpi_id);
        return $this->db->delete('tbl_kpi_bonus');
    }

    public function getKpiBonus($kpi_id) {
        $this->db->select('tbl_kpi_bonus.*');
        $this->db->from('tbl_kpi_bonus');
        $this->db->where('tbl_kpi_bonus.kpi_id', $kpi_id);
        return $this->db->get()->result_array();
    }

    public function getStaffDepartmentsByStaff($staffid) {
        $this->db->select('
            GROUP_CONCAT(distinct tblstaff_departments.departmentid) as departmentid
        ', false);
        $this->db->from('tblstaff_departments');
        $this->db->where('tblstaff_departments.staffid', $staffid);
        $staff_departments = $this->db->get()->row_array();
        return $staff_departments;
    }
}