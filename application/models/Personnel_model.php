<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Personnel_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPersonnel()
    {
    	$this->db->select('*');
    	$this->db->from('tbl_personnel');
    	return $this->db->get()->result_array();
    }

    public function searchPersonnel($q, $limit = 50)
    {
        $this->db->select('
            tbl_personnel.id as id,
            tbl_personnel.fullname as text,
            tblroles.name as name_role
        ', false);
        $this->db->from('tbl_personnel');
        $this->db->join('tblroles', 'role = tblroles.roleid', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_personnel.code', $q);
            $this->db->or_like('tbl_personnel.fullname', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertPersonnel($data)
    {
    	$this->db->insert('tbl_personnel', $data);
    	return $this->db->insert_id();
    }

    public function updatePersonnel($id, $data)
    {
        $this->db->where('tbl_personnel.id', $id);
        return $this->db->update('tbl_personnel', $data);
    }

    public function deletePersonnel($id)
    {
        $this->db->where('tbl_personnel.id', $id);
        return $this->db->delete('tbl_personnel');
    }

    public function insertPersonnelFamily($data)
    {
        $this->db->insert('tbl_personnel_family', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelFamily($data)
    {
        return $this->db->insert_batch('tbl_personnel_family', $data);
    }

    public function deletePersonnelFamily($personnel_id)
    {
        $this->db->where('tbl_personnel_family.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_family');
    }

    public function insertLiteracy($data)
    {
        $this->db->insert('tbl_personnel_literacy', $data);
        return $this->db->insert_id();
    }

    public function insertBatchLiteracy($data)
    {
        return $this->db->insert_batch('tbl_personnel_literacy', $data);
    }

    public function deleteLiteracy($personnel_id)
    {
        $this->db->where('tbl_personnel_literacy.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_literacy');
    }

    public function insertPersonnelConcurrently($data)
    {
        $this->db->insert('tbl_personnel_concurrently', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelConcurrently($data)
    {
        return $this->db->insert_batch('tbl_personnel_concurrently', $data);
    }

    public function deletePersonnelConcurrently($personnel_id)
    {
        $this->db->where('tbl_personnel_concurrently.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_concurrently');
    }

    public function insertPersonnelSalary($data)
    {
        $this->db->insert('tbl_personnel_salary', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelSalary($data)
    {
        return $this->db->insert_batch('tbl_personnel_salary', $data);
    }

    public function deletePersonnelSalary($personnel_id)
    {
        $this->db->where('tbl_personnel_salary.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_salary');
    }

    public function insertPersonnelSalaryAllowance($data)
    {
        $this->db->insert('tbl_personnel_salary_allowance', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelSalaryAllowance($data)
    {
        return $this->db->insert_batch('tbl_personnel_salary_allowance', $data);
    }

    public function deletePersonnelSalaryAllowance($personnel_salary_id)
    {
        $this->db->where('tbl_personnel_salary_allowance.personnel_salary_id', $personnel_salary_id);
        return $this->db->delete('tbl_personnel_salary_allowance');
    }

    public function insertPersonnelInsurrance($data)
    {
        $this->db->insert('tbl_personnel_insurrance', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelInsurrance($data)
    {
        return $this->db->insert_batch('tbl_personnel_insurrance', $data);
    }

    public function deletePersonnelInsurrance($personnel_id)
    {
        $this->db->where('tbl_personnel_insurrance.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_insurrance');
    }

    public function insertPersonnelAttachments($data)
    {
        $this->db->insert('tbl_personnel_attachments', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelAttachments($data)
    {
        return $this->db->insert_batch('tbl_personnel_attachments', $data);
    }

    public function deletePersonnelAttachments($personnel_id)
    {
        $this->db->where('tbl_personnel_attachments.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_attachments');
    }

    public function insertPersonnelReceive($data)
    {
        $this->db->insert('tbl_personnel_receive', $data);
        return $this->db->insert_id();
    }

    public function insertBatchPersonnelReceive($data)
    {
        return $this->db->insert_batch('tbl_personnel_receive', $data);
    }

    public function deletePersonnelReceive($personnel_id)
    {
        $this->db->where('tbl_personnel_receive.personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_receive');
    }

    public function countPersonnel($status = false)
    {
        $this->db->from('tbl_personnel');
        return $this->db->get()->num_rows();
    }

    public function getPersonnelById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_personnel');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getPersonnelFamilyById($personnel_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_personnel_family');
        $this->db->where('tbl_personnel_family.personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getPersonnelLiteracyById($personnel_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_personnel_literacy');
        $this->db->where('tbl_personnel_literacy.personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getDepartmentsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbldepartments');
        $this->db->where('tbldepartments.departmentid', $id);
        return $this->db->get()->row_array();
    }

    public function getLocationsById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_locations');
        $this->db->where('tbl_locations.id', $id);
        return $this->db->get()->row_array();
    }

    public function getRolesById($id)
    {
        $this->db->select('*');
        $this->db->from('tblroles');
        $this->db->where('tblroles.roleid', $id);
        return $this->db->get()->row_array();
    }

    public function getWorkplaceById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_workplace');
        $this->db->where('tbl_workplace.id', $id);
        return $this->db->get()->row_array();
    }

    public function getPersonnelConcurrently($personnel_id)
    {
        $this->db->select('
            tbldepartments.name as name_department,
            tbl_locations.name as name_location,
            tblroles.name as name_role,
            tbl_personnel_concurrently.deparments_concurrently as deparments_concurrently,
            tbl_personnel_concurrently.location_concurrently as location_concurrently,
            tbl_personnel_concurrently.role_concurrently as role_concurrently,
        ');
        $this->db->from('tbl_personnel_concurrently');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_personnel_concurrently.deparments_concurrently', 'left');
        $this->db->join('tbl_locations', 'tbl_locations.id = tbl_personnel_concurrently.location_concurrently', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_personnel_concurrently.role_concurrently', 'left');
        $this->db->where('tbl_personnel_concurrently.personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getPersonnelSalary($personnel_id)
    {
        $this->db->select('tbl_personnel_salary.*, tbl_salary_form.name as name_salary');
        $this->db->from('tbl_personnel_salary');
        $this->db->join('tbl_salary_form', 'tbl_salary_form.id = tbl_personnel_salary.salary_form');
        $this->db->where('tbl_personnel_salary.personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getPersonnelSalaryAllowance($personnel_salary_id)
    {
        $this->db->select('tbl_personnel_salary_allowance.*, tbl_allowance.name as name_allowance');
        $this->db->from('tbl_personnel_salary_allowance');
        $this->db->join('tbl_allowance', 'tbl_allowance.id = tbl_personnel_salary_allowance.salary_form_allowance');
        $this->db->where('tbl_personnel_salary_allowance.personnel_salary_id', $personnel_salary_id);
        return $this->db->get()->result_array();
    }

    public function getProvinceLevelById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_province_level');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getHospitalInsurranceById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_hospital_insurrance');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getPersonnelInsurranceById($personnel_id)
    {
        $this->db->select('tbl_personnel_insurrance.*, tbl_insurrance.name as name_insurrance');
        $this->db->from('tbl_personnel_insurrance');
        $this->db->join('tbl_insurrance', 'tbl_insurrance.id = tbl_personnel_insurrance.insurrance');
        $this->db->where('tbl_personnel_insurrance.personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getPersonnelReceiveById($personnel_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_personnel_receive');
        $this->db->where('personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getPersonnelAttachmentsById($personnel_id)
    {
        $this->db->select("tbl_personnel_attachments.*, CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as update_by");
        $this->db->from('tbl_personnel_attachments');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_personnel_attachments.update_by', 'left');
        $this->db->where('personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function getInsurranceById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_insurrance');
        $this->db->where('tbl_insurrance.id', $id);
        return $this->db->get()->row_array();
    }

    public function insertPersonnelHistoryJob($data)
    {
        $this->db->insert('tbl_personnel_history_job', $data);
        return $this->db->insert_id();
    }

    public function deletePersonnelHistoryJob($personnel_id)
    {
        $this->db->where('personnel_id', $personnel_id);
        return $this->db->delete('tbl_personnel_history_job');
    }

    public function getPersonnelHistoryJob($personnel_id)
    {
        $this->db->select('
            tbl_personnel_history_job.*,
            tbldepartments.name as name_department,
            tbl_locations.name as name_location,
            tblroles.name as name_role,
        ');
        $this->db->from('tbl_personnel_history_job');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_personnel_history_job.department_id', 'left');
        $this->db->join('tbl_locations', 'tbl_locations.id = tbl_personnel_history_job.location_id', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_personnel_history_job.role_id', 'left');
        $this->db->where('tbl_personnel_history_job.personnel_id', $personnel_id);
        return $this->db->get()->result_array();
    }

    public function isPersonnelHistoryJobChange($personnel_id, $status, $department_id, $location_id, $role_id)
    {
        $this->db->from('tbl_personnel_history_job');
        $this->db->where('tbl_personnel_history_job.personnel_id', $personnel_id);
        $this->db->where('tbl_personnel_history_job.status', $status);
        $this->db->where('tbl_personnel_history_job.department_id', $department_id);
        $this->db->where('tbl_personnel_history_job.location_id', $location_id);
        $this->db->where('tbl_personnel_history_job.role_id', $role_id);
        return $this->db->get()->num_rows();
    }
}