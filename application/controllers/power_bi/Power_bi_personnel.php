<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_personnel extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListPersonel()
    {
        $this->db->select('
            tblstaff.staffid as id,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name,
            tblstaff.birthday as birthday,
            TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") as old,
            IF(TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") < 30,CONCAT("Dưới 30"),
            IF((TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") >= 30 AND TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") <= 40),CONCAT("Từ 30 đến 40"),
            IF((TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") >= 41 and TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") < 50),CONCAT("Từ 41 đến 50"),
            IF((TIMESTAMPDIFF(YEAR,birthday, "' . date('Y-m-d') . '") >= 50 ),CONCAT("Trên 50"),
            ,"Độ tuổi khác")))) as type_old,
            tblstaff.status_work as status_work,
            IF(tblstaff.status_work != 2,tblstaff.day_in,tblstaff.date_status_work) as date_status_work,
            IF(tblstaff.status_work != 2,1,2) as status_work_new,
        ');
        $personnel = $this->db->get('tblstaff')->result_array();
        $this->response($personnel, REST_Controller::HTTP_OK);
    }

    public function getListPersonelViolation()
    {
        $this->db->select('
            tblviolation_records.id as id,
            tblviolation_records.staff_id as staff_id,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            tbllist_protocol.name as name_protocol,
            tbllist_protocol.id as id_list_protocol,
            tblviolation_records.date as date

        ');
        $this->db->join('tbllist_protocol', 'tbllist_protocol.id = tblviolation_records.id_list_protocol');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblviolation_records.staff_id');
        $this->db->where('tblviolation_records.status', 1);
        $this->db->where('tblviolation_records.status_staff', 1);
        $personnel = $this->db->get('tblviolation_records')->result_array();
        $this->response($personnel, REST_Controller::HTTP_OK);
    }

    public function getListPersonelKpi()
    {
        $this->db->select('
            tbl_kpi.id as id,
            tbl_kpi.staff as staff_id,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as name_staff,
            tbl_kpi.point_kpi as point_kpi,
            tbl_kpi.result_kpi as result_kpi,
            DATE_FORMAT(tbl_kpi.start_date, "%Y") as year,
            DATE_FORMAT(tbl_kpi.start_date, "%m") as month
        ');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_kpi.staff');
        $personnel = $this->db->get('tbl_kpi')->result_array();
        $this->response($personnel, REST_Controller::HTTP_OK);
    }

    public function getListPersonelSalary()
    {
        $this->db->select('
            tbl_payroll_item.id as id,
            tbl_payroll_item.staff_id as staff_id,
            tblbranch.id as branch_id,
            tblbranch.name as branch_name,
            COALESCE(tbldepartments.name,"Không xác định") as name_department,
            COALESCE(tbldepartments.departmentid,0) as department_id,
            tbl_payroll_item.salary_income as Thu nhập,
            tbl_payroll_item.total_allowance_other as Phụ cấp,
            tbl_payroll_item.allowance_business_fee as Tăng ca,
            tbl_payroll_item.total_reduce_other as Khấu trừ,
            tbl_payroll_item.total_real as Tổng lương,
            tbl_payroll.month as month,
            tbl_payroll.year as year,
        ');
        $this->db->join('tbl_payroll', 'tbl_payroll.id = tbl_payroll_item.payroll_id');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item.staff_id');
        $this->db->join('tblbranch', 'tblbranch.id = tblstaff.branch_salary');
        $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
        $this->db->where('tblstaff.status_work !=', 2);
        $personnel = $this->db->get('tbl_payroll_item')->result_array();
        $this->response($personnel, REST_Controller::HTTP_OK);
    }

    public function getListPersonelTimekeeping()
    {
        $this->db->dbprefix = '';
        $this->db->select('
            tbl_timekeeping_detail.id as id,
            tbl_timekeeping_detail.staff_id as staff_id,
            COALESCE(tbldepartments.name,"Không xác định") as name_department,
            COALESCE(tbldepartments.departmentid,0) as department_id,
            (tbl_timekeeping_detail.count_hour - tbl_timekeeping_detail.count_hour_overtime) as count_hour, 
            IF(tbl_timekeeping_detail.check_sun = 1,tbl_timekeeping_detail.count_hour,0) as count_hour_overtime_new,
            (tbl_timekeeping_detail.count_hour_overtime) as count_hour_overtime,
            tbl_timekeeping.month as month,
            tbl_timekeeping.year as year,
        ');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_timekeeping_detail.staff_id');
        $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
        $this->db->where('tblstaff.status_work !=', 2);
        $personnel = $this->db->get('tbl_timekeeping_detail')->result_array();
        $this->response($personnel, REST_Controller::HTTP_OK);
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

    public function getListSetupPaidHoliday()
    {
        $this->db->select('
            tbl_setup_paid_holiday.id as id,
            tbl_setup_paid_holiday.year as year,
        ');
        $this->db->from('tbl_setup_paid_holiday');
        $setupPaidHoliday = $this->db->get()->result_array();
        $this->response($setupPaidHoliday, REST_Controller::HTTP_OK);
    }

    public function getListSetupPaidHolidayDetail()
    {
        $this->db->select('
            tbl_setup_paid_holiday_staff.id_setup_paid_holiday as id_setup_paid_holiday,
            tbl_setup_paid_holiday_staff.staff_id as staff_id,
            tbl_setup_paid_holiday_staff.number_day as number_day,
        ');
        $this->db->from('tbl_setup_paid_holiday_staff');
        $setupPaidHolidayStaff = $this->db->get()->result_array();
        $this->response($setupPaidHolidayStaff, REST_Controller::HTTP_OK);
    }

    public function getListPaidHoliday()
    {
        $this->db->select('
            tbl_paid_holiday_leave.id as id_paid_holiday,
            tbl_paid_holiday_leave.staff_id as staff_id,
            tbl_paid_holiday_leave.name as name,
        ');
        $this->db->from('tbl_paid_holiday_leave');
        $paidHoliday = $this->db->get()->result_array();
        $this->response($paidHoliday, REST_Controller::HTTP_OK);
    }

    public function getListPaidHolidayDetail()
    {

        $tb_tamp = "
            SELECT
                tbl_paid_holiday_leave_detail.id as id_paid_holiday_detail,
                tbl_paid_holiday_leave_detail.paid_holiday_leave_id as paid_holiday_leave_id,
                tbl_paid_holiday_leave.staff_id as staff_id,
                tbl_paid_holiday_leave_detail.type_magic_id as type_magic_id,
                IF(tbl_paid_holiday_leave_detail.status = 1 AND paid_holiday_detail_id IS NULL,0,tbl_paid_holiday_leave_detail.number_date) as number_date,
                DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, '%Y') as year,
                DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, '%m') as month,
                tbl_paid_holiday_leave_detail.status as status,
                IF(tbl_paid_holiday_leave_detail.status = 2,7,
                      CASE
                        WHEN tbl_paid_holiday_leave_detail.status = 2 THEN 7
                        WHEN tbl_paid_holiday_leave_detail.type_magic_id = 1 THEN 1
                        WHEN tbl_paid_holiday_leave_detail.type_magic_id = 3 THEN 2
                        WHEN tbl_paid_holiday_leave_detail.type_magic_id = 2 THEN 4
                        WHEN tbl_paid_holiday_leave_detail.type_magic_id = 4 THEN 5
                        WHEN tbl_paid_holiday_leave_detail.type_magic_id = 5 THEN 6
                    END
                )  as type_check,
                CASE
                    WHEN tbl_paid_holiday_leave_detail.type_magic_id = 1 THEN 1
                    WHEN tbl_paid_holiday_leave_detail.type_magic_id = 3 THEN 1
                    ELSE 2                  
                END as type_salary,
                1 as type
            FROM tbl_paid_holiday_leave_detail
            INNER JOIN tbl_paid_holiday_leave ON tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id        
            LEFT JOIN tbl_timekeeping_detail ON tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id        
            
            UNION ALL
            
             SELECT
                0 as id_paid_holiday_detail,
                0 as paid_holiday_leave_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                10 as type_magic_id,
                1 as number_date,
                tbl_timekeeping.year as year,
                tbl_timekeeping.month as month,
                1 as status,
                3 as type_check,
                1 as type_salary,
                2 as type
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_timekeeping ON tbl_timekeeping.id  = tbl_timekeeping_detail.timekeeping_id
            WHERE tbl_timekeeping_detail.type = 'LT'
        ";

        $result = $this->db->query($tb_tamp)->result_array();
        $this->response($result, REST_Controller::HTTP_OK);

    }

    public function getListTypeMagic(){
        $arr = [
            [
                'id' => 1,
                'name' => 'Phép năm',
            ],
            [
                'id' => 2,
                'name' => 'Hiếu hỉ',
            ],
            [
                'id' => 3,
                'name' => 'Lễ tết',
            ],
            [
                'id' => 4,
                'name' => 'Ốm đâu',
            ],
            [
                'id' => 5,
                'name' => 'Thai sản',
            ],
            [
                'id' => 6,
                'name' => 'Nghỉ không hưởng lương',
            ],
            [
                'id' => 7,
                'name' => 'Nghỉ không phép',
            ],
        ];
        $this->response($arr, REST_Controller::HTTP_OK);
    }

    public function getListPaidHolidayDetailMonth()
    {
        $this->db->select('
            tbl_paid_holiday_leave_detail_month.id as id,
            tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id as paid_holiday_leave_detail_id,
            tbl_paid_holiday_leave_detail_month.month as month,
        ');
        $this->db->from('tbl_paid_holiday_leave_detail_month');
        $paidHolidayDetailMonth = $this->db->get()->result_array();
        $this->response($paidHolidayDetailMonth, REST_Controller::HTTP_OK);
    }

    public function getListDeparmentStaff()
    {
        $this->db->select('
            tblstaff_departments.staffid as staff_id,
            tblstaff_departments.departmentid department_id,
        ');
        $this->db->from('tblstaff_departments');
        $staffDeparment = $this->db->get()->result_array();
        $this->response($staffDeparment, REST_Controller::HTTP_OK);
    }


    public function getListTypePaidHoliday()
    {
        $arr = [
            [
                'id' => 0,
                'name' => 'Chưa duyệt'
            ],
            [
                'id' => 1,
                'name' => 'Đã duyệt'
            ],
            [
                'id' => 2,
                'name' => 'Không duyệt'
            ]
        ];
        $this->response($arr, REST_Controller::HTTP_OK);
    }

}