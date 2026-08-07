<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_task extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('vietnamese', 'vietnamese');
    }

    public function getListTask()
    {
        $this->db->select('
            tbltasks.id as id,
            tbltasks.name as name_task,
            tbltasks.category_tasks as category_tasks,
            tbltasks.status as status,
            tbltasks.priority as priority,
            DATE_FORMAT(tbltasks.startdate, "%Y-%m-%d") as startdate,
            (SELECT COALESCE(FLOOR(SUM(TIMESTAMPDIFF(SECOND, DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.start_time), "%Y-%m-%d %H:%i:%s"), DATE_FORMAT(FROM_UNIXTIME(tbltaskstimers.end_time), "%Y-%m-%d %H:%i:%s")))/60),0)
                FROM tbltaskstimers 
                WHERE tbltaskstimers.task_id = tbltasks.id
            ) as _minute,
            IF(tbltasks.rel_type = "internal_proposal",1,IF(tbltasks.rel_type = "production_report",2,0)) as type_task,
            (SELECT tblcategory_tasks.time FROM tblcategory_tasks WHERE tblcategory_tasks.id = tbltasks.category_tasks LIMIT 1) as minute_limit
        ');
        $task = $this->db->get('tbltasks')->result_array();
        $this->response($task, REST_Controller::HTTP_OK);
    }

    public function getListStatusTask(){
        $status = $this->tasks_model->get_statuses();
        $this->response($status, REST_Controller::HTTP_OK);
    }

    public function getListPriorityTask(){
        $priority = get_tasks_priorities();
        $this->response($priority, REST_Controller::HTTP_OK);
    }

    public function getListTypeTask()
    {
        $arr = [
            [
                'id' => 1,
                'name' => 'Đề xuất nội bộ'
            ],
            [
                'id' => 2,
                'name' => 'Phiếu báo cáo'
            ],
            [
                'id' => 0,
                'name' => 'Tự tạo'
            ]
        ];
        $this->response($arr, REST_Controller::HTTP_OK);
    }

    public function getListResultTask()
    {
        $arr = [
            [
                'id' => 1,
                'name' => 'Chưa tính giờ'
            ],
            [
                'id' => 2,
                'name' => 'Chưa đạt'
            ],
            [
                'id' => 3,
                'name' => 'Đạt'
            ],
            [
                'id' => 4,
                'name' => 'Vượt KPI'
            ]
        ];
        $this->response($arr, REST_Controller::HTTP_OK);
    }

    public function getListDepartment()
    {
        $this->db->select('
            tbldepartments.departmentid as department_id,
            tbldepartments.name as name,
            tbldepartments.code as code,
        ');
        $this->db->from('tbldepartments');
        $deparment = $this->db->get()->result_array();
        $this->response($deparment, REST_Controller::HTTP_OK);
    }

    public function getListDepartmentTask()
    {
        $this->db->select('
            tbltask_department.department_id as department_id,
            tbltask_department.task_id as task_id,
        ');
        $this->db->from('tbltask_department');
        $deparmentTask = $this->db->get()->result_array();
        $this->response($deparmentTask, REST_Controller::HTTP_OK);
    }

    public function getListCategoryTask()
    {
        $this->db->select('
            tblcategory_tasks.id as category_task_id,
            tblcategory_tasks.code as code,
            tblcategory_tasks.content as content,
        ');
        $this->db->from('tblcategory_tasks');
        $categoryTask = $this->db->get()->result_array();
        $this->response($categoryTask, REST_Controller::HTTP_OK);
    }

}