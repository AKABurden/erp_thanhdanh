<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_full_detail($id='') {
        $categories = array();
        if($id == '') {
            $this->db->where('category_parent', '0');
            $categories = $this->db->get('tblcategories')->result_array();
        }
        else {
            $this->db->where('category_parent', $id);
            $categories = $this->db->get('tblcategories')->result_array();
        }
        return $categories;
    }
    public function update_category($data_vestion,$id)
    {
        if (is_admin()) {
            // var_dump($data_vestion);die();
            $this->db->where('id',$id);
            $this->db->update('tblcategories',$data_vestion);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function delete_categories($data='')
    {
        // if($data['id'] != 0)
        // {
        $this->db->where('category_parent',$data['id']);
        $this->db->update('tblcategories',array('category_parent'=>$data['id_new']));
        $this->db->where('category_id',$data['id']);
        $this->db->update('tblitems',array('category_id'=>$data['id_new']));
        $this->db->delete('tblcategories',array('id'=>$data['id']));
        if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        // }
        // return false;
    }
    public function add_category($data)
    {
        if (is_admin()) {
        	$data['staff_create'] = get_staff_user_id();
        	$data['date_create'] = date('Y-m-d H:i:s');
            $this->db->insert('tblcategories',$data);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function get_by_id($id_parent=0,&$array_category=[], $level=0) {
        if(is_numeric($level)) {
            $this->db->where(array('category_parent' => $id_parent));
            $current_level = $this->db->get('tblcategories')->result_array();
            if($current_level)
            {
            foreach($current_level as $key=>$value) {
                $sub = "";
                for($i=0;$i<$level;$i++){
                    $sub.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                $sub.= "&#10154;";
                $current_level[$key]['category'] = $sub . " " .$current_level[$key]['category'];
                array_push($array_category, $current_level[$key]);
                $this->get_by_id($value['id'], $array_category, $level+1);
            }
            }else
            {
               return ;
            }
        }
    }
    // public function get_by_id($id_parent=0,&$array_category=[], $level=0) {
    //     if(is_numeric($level)) {
    //         $this->db->where(array('category_parent' => $id_parent));
    //         $current_level = $this->db->get('tblcategories')->result_array();

    //         foreach($current_level as $key=>$value) {
    //             $sub = "";
    //             for($i=0;$i<$level;$i++){
    //                 $sub.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //             }
    //             $sub.= "&#10154;";
    //             $current_level[$key]['category'] = $sub . " " .$current_level[$key]['category'];
    //             array_push($array_category, $current_level[$key]);
    //             if($level< 3)
    //                 $this->get_by_id($value['id'], $array_category, $level+1);
    //         }
    //     }
    // }
    //

    public function insertCapacity($data)
    {
        $this->db->insert('tbl_capacity', $data);
        return $this->db->insert_id();
    }

    public function rowCapacity($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_capacity');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getCapacity()
    {
        $this->db->select('*');
        $this->db->from('tbl_capacity');
        return $this->db->get()->result_array();
    }

    public function updateCapacity($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_capacity', $data);
    }

    public function deleteCapacity($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_capacity');
    }

    public function searchCapacity($q, $limit = 50)
    {
        $this->db->select('tbl_capacity.id as id, tbl_capacity.name as name', false);
        $this->db->from('tbl_capacity');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_capacity.code', $q);
            $this->db->or_like('tbl_capacity.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertMachines($data)
    {
        $this->db->insert('tbl_machines', $data);
        return $this->db->insert_id();
    }

    public function rowMachines($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_machines');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getMachinesByArrId($arr_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_machines');
        $this->db->where_in('tbl_machines.id', $arr_id);
        return $this->db->get()->result_array();
    }

    public function updateMachines($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_machines', $data);
    }

    public function deleteMachines($id)
    {
		$this->db->where('tbl_machines_process.machines_id', $id);
		$machines_process = $this->db->get('tbl_machines_process')->result_array();
		if(!empty($machines_process)) {
			foreach ($machines_process as $key => $value) {
				$this->db->where('tblfiles.rel_id', $value['id']);
				$this->db->where('tblfiles.rel_type', 'rel_process');
				$files = $this->db->get('tblfiles')->row();
				if (!empty($files)) {
					$link = FCPATH . 'uploads/machines/' . $files->id . '/' . $files->file_name;
					@unlink($link);
					$this->db->where('id', $files->id);
					$this->db->delete('tblfiles');
				}
			}
		}
		$this->db->where('tbl_machines_process.machines_id', $id);
		$this->db->delete('tbl_machines_process');

		$this->db->where('tbl_machines_maintenance.machines_id', $id);
		$this->db->delete('tbl_machines_maintenance');


        $this->db->where('id', $id);
        return $this->db->delete('tbl_machines');
    }

    public function searchMachines($q, $limit = 50, $stage_id = 0)
    {   
        $category_stage_id = 0;
        if (!empty($stage_id)) {
            $this->db->select('tbl_stages.category_stages as category_stages');
            $this->db->from('tbl_stages');
            $this->db->where('tbl_stages.id', $stage_id);
            $dtStage = $this->db->get()->row_array();
            if (!empty($dtStage)) {
                $category_stage_id = $dtStage['category_stages'];
            }
        }

        $this->db->select('tbl_machines.id as id, CONCAT(tbl_machines.code, " (", tbl_machines.name, ")") as name', false);
        $this->db->from('tbl_machines');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_machines.code', $q);
            $this->db->or_like('tbl_machines.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);

        if (!empty($stage_id)) {
            $this->db->where(' exists (
                SELECT 1
                FROM tbl_machines_stage
                WHERE tbl_machines_stage.machines_id = tbl_machines.id AND tbl_machines_stage.category_stage_id = '.$category_stage_id.'
            )', false, false);
        }

        return $this->db->get()->result_array();
    }

    public function insertPackaging($data)
    {
        $this->db->insert('tbl_packaging', $data);
        return $this->db->insert_id();
    }

    public function rowPackaging($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_packaging');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function updatePackaging($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_packaging', $data);
    }

    public function deletePackaging($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_packaging');
    }

    public function searchPackaging($q, $limit = 50)
    {
        $this->db->select('tbl_packaging.id as id, tbl_packaging.name as name', false);
        $this->db->from('tbl_packaging');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_packaging.code', $q);
            $this->db->or_like('tbl_packaging.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertLocations($data)
    {
        $this->db->insert('tbl_locations', $data);
        return $this->db->insert_id();
    }

    public function rowLocations($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_locations');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getLocations()
    {
        $this->db->select('*');
        $this->db->from('tbl_locations');
        return $this->db->get()->result_array();
    }

    public function updateLocations($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_locations', $data);
    }

    public function deleteLocations($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_locations');
    }

    public function insertWorkplace($data)
    {
        $this->db->insert('tbl_workplace', $data);
        return $this->db->insert_id();
    }

    public function rowWorkplace($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_workplace');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getWorkplace()
    {
        $this->db->select('*');
        $this->db->from('tbl_workplace');
        return $this->db->get()->result_array();
    }

    public function updateWorkplace($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_workplace', $data);
    }

    public function deleteWorkplace($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_workplace');
    }

    public function getDeparments()
    {
        $this->db->select('*');
        $this->db->from('tbldepartments');
        return $this->db->get()->result_array();
    }

    public function getRole()
    {
        $this->db->select('*');
        $this->db->from('tblroles');
        return $this->db->get()->result_array();
    }

    public function insertAllowance($data)
    {
        $this->db->insert('tbl_allowance', $data);
        return $this->db->insert_id();
    }

    public function rowAllowance($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_allowance');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getAllowance()
    {
        $this->db->select('*');
        $this->db->from('tbl_allowance');
        return $this->db->get()->result_array();
    }

    public function updateAllowance($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_allowance', $data);
    }

    public function deleteAllowance($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_allowance');
    }

    public function insertSalaryForm($data)
    {
        $this->db->insert('tbl_salary_form', $data);
        return $this->db->insert_id();
    }

    public function rowSalaryForm($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_salary_form');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getSalaryForm()
    {
        $this->db->select('*');
        $this->db->from('tbl_salary_form');
        return $this->db->get()->result_array();
    }

    public function updateSalaryForm($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_salary_form', $data);
    }

    public function deleteSalaryForm($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_salary_form');
    }

    public function insertInsurrance($data)
    {
        $this->db->insert('tbl_insurrance', $data);
        return $this->db->insert_id();
    }

    public function rowInsurrance($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_insurrance');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getInsurrance()
    {
        $this->db->select('*');
        $this->db->from('tbl_insurrance');
        return $this->db->get()->result_array();
    }

    public function updateInsurrance($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_insurrance', $data);
    }

    public function deleteInsurrance($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_insurrance');
    }

    public function searchInsurrance($q, $limit = 50, $form)
    {
        $this->db->select('
            tbl_insurrance.id as id,
            tbl_insurrance.name as text,
            tbl_insurrance.code as code,
            tbl_insurrance.money as money,
            tbl_insurrance.rate_company as rate_company,
            tbl_insurrance.rate_worker as rate_worker,
        ', false);
        $this->db->from('tbl_insurrance');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_insurrance.code', $q);
            $this->db->or_like('tbl_insurrance.name', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_insurrance.form', $form);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function getProvinceLevel()
    {
        $this->db->select('*');
        $this->db->from('tbl_province_level');
        return $this->db->get()->result_array();
    }

    public function searchHospitalInsurrance($q, $limit = 50, $province_id)
    {
        $this->db->select('
            tbl_hospital_insurrance.id as id,
            tbl_hospital_insurrance.name as text,
            tbl_hospital_insurrance.address as address,
            tbl_hospital_insurrance.phone as phone,
        ', false);
        $this->db->from('tbl_hospital_insurrance');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_hospital_insurrance.name', $q);
            $this->db->or_like('tbl_hospital_insurrance.address', $q);
            $this->db->group_end();
        }
        $this->db->where('tbl_hospital_insurrance.province_id', $province_id);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function insertBatchMachinesProcess($data) {
        return $this->db->insert_batch('tbl_machines_process', $data);
    }

    public function deleteMachinesProcess($machines_id) {
        $this->db->where('tbl_machines_process.machines_id', $machines_id);
        $machines_process = $this->db->delete('tbl_machines_process')->result_array();
		if(!empty($machines_process)) {
			foreach ($machines_process as $key => $value) {
				$this->db->where('tblfiles.rel_id', $value['id']);
				$this->db->where('tblfiles.rel_type', 'rel_process');
				$files = $this->db->get('tblfiles')->row();
				if (!empty($files)) {
					$link = FCPATH . 'uploads/machines/' . $files->id . '/' . $files->file_name;
					@unlink($link);
					$this->db->where('id', $files->id);
					$this->db->delete('tblfiles');
				}
			}
		}

		$this->db->where('tbl_machines_process.machines_id', $machines_id);
        return $this->db->delete('tbl_machines_process');
    }

    public function getMachinesProcess($machines_id) {
        $this->db->select('tbl_machines_process.*', false);
        $this->db->from('tbl_machines_process');
        $this->db->where('tbl_machines_process.machines_id', $machines_id);
        return $this->db->get()->result_array();
    }

    //
    public function insertModeMaterials($data)
    {
        $this->db->insert('tbl_mode_materials', $data);
        return $this->db->insert_id();
    }

    public function rowModeMaterials($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_mode_materials');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function rowModeMaterialsByCode($code)
    {
        $this->db->select('*');
        $this->db->from('tbl_mode_materials');
        $this->db->where('code', $code);
        return $this->db->get()->row_array();
    }

    public function getModeMaterials()
    {
        $this->db->select('*');
        $this->db->from('tbl_mode_materials');
        return $this->db->get()->result_array();
    }

    public function updateModeMaterials($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_mode_materials', $data);
    }

    public function deleteModeMaterials($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_mode_materials');
    }

    public function checkExistCategoryModeMaterials($id) {
        $this->db->from('tbl_mode_materials');
        $this->db->where('tbl_mode_materials.id', $id);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    public function checkCodeExistCategoryModeMaterials($code) {
        $this->db->from('tbl_mode_materials');
        $this->db->where('tbl_mode_materials.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }

    // yct start
    public function checkCodeExistCapacity($code) {
        $this->db->from('tbl_capacity');
        $this->db->where('tbl_capacity.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }
    public function getModeMaterialsIdByCode($code)
    {
        $this->db->select('tbl_mode_materials.id');
        $this->db->from('tbl_mode_materials');
        $this->db->where('tbl_mode_materials.code', $code);
        $result = $this->db->get()->row();
        if (empty($result)) {
            return 0;
        } else {
            return $result->id;
        }
    }
    // yct end

    public function insertTransportationVehicles($data)
    {
        $this->db->insert('tbl_transportation_vehicles', $data);
        return $this->db->insert_id();
    }

    public function rowTransportationVehicles($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_transportation_vehicles');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getTransportationVehicles()
    {
        $this->db->select('*');
        $this->db->from('tbl_transportation_vehicles');
        return $this->db->get()->result_array();
    }

    public function updateTransportationVehicles($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_transportation_vehicles', $data);
    }

    public function deleteTransportationVehicles($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_transportation_vehicles');
    }

    public function checkCodeExistTransportationVehicles($code) {
        $this->db->from('tbl_transportation_vehicles');
        $this->db->where('tbl_transportation_vehicles.code', $code);
        $this->db->limit(1);
        return $this->db->get()->num_rows();
    }
}