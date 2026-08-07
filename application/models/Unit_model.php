<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Unit_model extends App_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get task by id
     * @param  mixed $id task id
     * @return object
     */
    public function get_roles()
    {
        $is_admin = is_admin();
        $roles = $this->db->get('tblroles')->result_array();
        return $roles;
    }
    public function add_unit($data)
    {
//        if (is_admin()) {
            $this->db->insert('tblunits', $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
//        }
        return false;
    }
	
    public function update_unit($data_vestion, $id)
    {
//        if (is_admin()) {
            // var_dump($data_vestion);die();
            $this->db->where('unitid', $id);
            $this->db->update('tblunits', $data_vestion);
            if ($this->db->affected_rows() > 0) {
				log_activity('Cập nhật đơn vị tính [ID:' . $id . ', Name: ' . $data_vestion['unit'] . ']');
                return true;
            }
            return false;
//        }
        return false;
    }
    public function delete_unit($id)
    {
//        if (is_admin()) {
            $this->db->where('unitid', $id);
            $this->db->delete('tblunits');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
//        }
        return false;
    }
    public function get_row_unit($id)
    {
//        if (is_admin()) {
            $this->db->select('tblunits.*');
            $this->db->where('tblunits.unitid', $id);
            return $this->db->get('tblunits')->row();
//        }
    }

    //    public function get_call_logs_and_staff($id, $where = array())
    //    {
    //        $this->db->where('c.ID', $id);
    //        $this->db->join('tblstaff s', 'c.assigned = s.staffid', 'left');
    //        $this->db->select('s.*');
    //        $this->db->from('tblcall_logs c');
    ////        $task = $this->db->get('tblcall_logs')->row();
    ////        $task->assignees       = $this->get_task_assignees($id);
    //        return $this->db->get()->result_array();
    //    }
    //    public function get_task_assignees($id)
    //    {
    //        $this->db->select('id,tblstafftaskassignees.staffid as assigneeid,assigned_from,firstname,lastname');
    //        $this->db->from('tblstafftaskassignees');
    //        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
    //        $this->db->where('taskid', $id);
    //        return $this->db->get()->result_array();
    //    }
    //    public function get_all_assignees()
    //    {
    //        $this->db->select('*');
    //        $this->db->from('tblstaff');
    ////        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
    ////        $this->db->where('taskid', $id);
    //        return $this->db->get()->result_array();
    //    }
    //

    //
    //
    //    public function remove_assignee($id, $taskid)
    //    {
    //        $task = $this->get($taskid);
    //        $this->db->where('id', $id);
    //        $assignee_data = $this->db->get('tblstafftaskassignees')->row();
    //        $this->db->where('id', $id);
    //        $this->db->delete('tblstafftaskassignees');
    //        if ($this->db->affected_rows() > 0) {
    //            if ($task->rel_type == 'project') {
    //                $this->projects_model->log_activity($task->rel_id, 'project_activity_task_assignee_removed', $task->name . ' - ' . get_staff_full_name($assignee_data->staffid), $task->visible_to_client);
    //            }
    //            return true;
    //        }
    //        return false;
    //    }
    //
    //
    //
    //    public function update($data,$id)
    //    {
    //        if (is_admin()) {
    //            $this->db->where('ID', $id);
    //            if(!isset($data['checkout']))
    //            {
    //                $data['checkout']=0;
    //            }
    //            $data['assigned']=implode(',',$data['assigned']);
    //            $this->db->update('tblcall_logs',$data);
    //            if ($this->db->affected_rows() >0) {
    ////                logActivity('Reminder Deleted [' . ucfirst($reminder->rel_type) . 'ID: ' . $reminder->id . ' Description: ' . $reminder->description . ']');
    //                return true;
    //            }
    //            return false;
    //        }
    //        return false;
    //    }
    //    public function add($data,$idlead)
    //    {
    //        if (is_admin()) {
    //            if(!isset($data['checkout']))
    //            {
    //                $data['checkout']=0;
    //            }
    //            $data['id_lead']=$idlead;
    //            $data['assigned']=implode(',',$data['assigned']);
    //            $this->db->insert('tblcall_logs',$data);
    //            if ($this->db->affected_rows() >0) {
    //                return true;
    //            }
    //            return false;
    //        }
    //        return false;
    //    }
    //

    function getUnits()
    {
        $this->db->select('*');
        $this->db->from('tblunits');
        return $this->db->get()->result_array();
    }

    function rowUnit($id)
    {
        $this->db->select('*');
        $this->db->from('tblunits');
        $this->db->where('unitid', $id);
        return $this->db->get()->row_array();
    }

    function insertUnit($data)
    {
        $this->db->insert('tblunits', $data);
        return $this->db->insert_id();
    }

    public function rowUnitByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tblunits');
        if ($option == "like") {
            $this->db->like('tblunits.unit', trim($code));
        } else if ($option == "where") {
            $this->db->where('tblunits.unit', trim($code));
        }
        return $this->db->get()->row_array();
    }

    //brand
    public function rowBrandByCode($code, $select, $option)
    {
        $this->db->select($select);
        $this->db->from('tbl_brand');
        if ($option == "like") {
            $this->db->like('tbl_brand.code', $code);
        } else if ($option == "where") {
            $this->db->where('tbl_brand.code', $code);
        }
        return $this->db->get()->row_array();
    }
    function insertBrand($data)
    {
        $this->db->insert('tbl_brand', $data);
        return $this->db->insert_id();
    }
}