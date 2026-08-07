<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Work_plan_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
    }

    public function insertWorkPlan($data)
    {
        $this->db->insert('tbl_work_plan', $data);
        return $this->db->insert_id();
    }

    public function updateWorkPlan($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_work_plan', $data);
    }

    public function insertWorkPlanItems($data)
    {
        $this->db->insert('tbl_work_plan_items', $data);
        return $this->db->insert_id();
    }

    public function deleteWorkPlanItems($work_plan_id)
    {
        $this->db->where('work_plan_id', $work_plan_id);
        return $this->db->delete('tbl_work_plan_items');
    }

    public function deleteWorkPlanITask($work_plan_id)
    {
        $this->db->where('work_plan_id', $work_plan_id);
        return $this->db->delete('tbl_work_plan_task');
    }

    public function insertBatchWorkPlanItemsStaffs($data)
    {
        return $this->db->insert_Batch('tbl_work_plan_items_staffs', $data);
    }

    public function deleteWorkPlanItemsStaffs($work_plan_id)
    {
        $this->db->where('work_plan_id', $work_plan_id);
        return $this->db->delete('tbl_work_plan_items_staffs');
    }

    public function getWorkPlanById($id)
    {
        $this->db->select('
            tbl_work_plan.*
        ', false);
        $this->db->from('tbl_work_plan');
        $this->db->where('tbl_work_plan.id', $id);
        return $this->db->get()->row_array();
    }

    public function getWorkPlanItems($work_plan_id)
    {
        $this->db->select('
            tbl_work_plan_items.*
        ', false);
        $this->db->from('tbl_work_plan_items');
        $this->db->where('tbl_work_plan_items.work_plan_id', $work_plan_id);
        // $this->db->order_by('tbl_work_plan_items.id ASC, tbl_work_plan_items.type ASC');
        $this->db->order_by('tbl_work_plan_items.type ASC, tbl_work_plan_items.number ASC');
        return $this->db->get()->result_array();
    }

    public function getWorkPlanItemsStaffs($work_plan_items_id, $type_staff)
    {
        $this->db->select('
            tbl_work_plan_items_staffs.*
        ', false);
        $this->db->from('tbl_work_plan_items_staffs');
        $this->db->where('tbl_work_plan_items_staffs.work_plan_items_id', $work_plan_items_id);
        $this->db->where('tbl_work_plan_items_staffs.type_staff', $type_staff);
        return $this->db->get()->result_array();
    }

    public function getWorkPlanByMonthYear($month, $year)
    {
        $this->db->select('tbl_work_plan.*', false);
        $this->db->from('tbl_work_plan');
        $this->db->where('tbl_work_plan.month', $month);
        $this->db->where('tbl_work_plan.year', $year);
        return $this->db->get()->row_array();
    }

    /**
     * Công việc liên quan
     */
    public function getTaskRel($workPlanTaskId)
    {
        $this->db->select(
            'tbltasks.name as task_name,tbltasks.id as task_id'
        );
        $this->db->where('tbltasks.rel_type', 'work_plan_task');
        $this->db->where('tbltasks.rel_id', $workPlanTaskId);
        $result = $this->db->get('tbltasks')->result_array();
        return $result;
    }

    public function createTask($workPlanTaskId)
    {
        $this->db->where('id', $workPlanTaskId);
        $work_plan_task = $this->db->get('tbl_work_plan_task')->row();
        $name = '';
        if (!empty($work_plan_task)) {
            $date_start = $work_plan_task->date_start;
            $date_end = $work_plan_task->date_end;


            if (!empty($work_plan_task->category_task_id)) {
                $this->db->where('id', $work_plan_task->category_task_id);
                $category_tasks = $this->db->get('tblcategory_tasks')->row();
                $staff_department = !empty($category_tasks) ? $category_tasks->departments : null;
                $name = !empty($category_tasks) ? $category_tasks->content : null;
            }
            if (!empty($work_plan_task->work_plan_id)) {
                $work_plan = get_table_where('tbl_work_plan', ['id' => $work_plan_task->work_plan_id], '', 'row_array');
                if (empty($date_start)) {
                    $date_start = $work_plan['year'] . '-' . $work_plan['month'] . '-01';
                }
                $name .= ' ' . date('m/Y', strtotime($date_start));
            }
            $_data = [
                'name' => $name,
                'hourly_rate' => 0,
                'category_tasks' => $work_plan_task->category_task_id,
                'startdate' => $date_start,
                'duedate' => !empty($date_end) ? $date_end : NULL,
                'priority' => 2,
                'rel_type' => 'work_plan_task',
                'rel_id' => $workPlanTaskId,
                'description' => $work_plan_task->content,
                'department_id' => !empty($staff_department) ? explode(',', $staff_department) : [],
                'id_branch' => $work_plan_task->branch_id,
                '_addedfrom' => $work_plan_task->staff_assigner
            ];
            $id_tasks = $this->tasks_model->add($_data, false, true);
            if (!empty($id_tasks)) {
                if (!empty($work_plan_task->staff_assigned)) {
                    $arr_staff_assigned = explode(',', $work_plan_task->staff_assigned);
                    $data['taskid'] = $id_tasks;
                    foreach ($arr_staff_assigned as $key => $staff_assigned_id) {
                        $data['assignee'] = $staff_assigned_id;
                        $result = $this->tasks_model->add_task_assignees($data);
                        // var_dump($result);die;
                    }
                }
                if (!empty($work_plan_task->staff_monitor)) {
                    $data['taskid'] = $id_tasks;
                    $data['follower'] = $work_plan_task->staff_monitor;
                    $result = $this->tasks_model->add_task_followers($data);
                    // var_dump($result);die;
                }
                return $id_tasks;
            }
        }
        return false;
    }
}
