<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_contract_appendix extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        //phân quyền
        if (!has_permission('dashboard_contract_appendix', '', 'view')) {
            access_denied('dashboard_contract_appendix');
            die;
        }
    }

    /**
     * Trang dashboard phụ lục hợp đồng
     */
    public function index(){
        $perViewDashboardContractAppendix = true;
        if (!$perViewDashboardContractAppendix){
            access_denied('dashboard_contract_appendix');
        }
        $data['title'] = _l('Dashboard Phụ lục hợp đồng');
        $this->load->view('admin/dashboard_contract_appendix/dashboard_contract_appendix', $data);
    }

    /**
     * Lấy thống kê tổng quan phụ lục
     */
    public function get_overview_statistics(){
        $data = [];
        
        // Tổng số phụ lục
        $this->db->from('tbl_contract_appendix');
        $data['total_appendix'] = $this->db->count_all_results();
        
        // Phụ lục chưa duyệt
        $this->db->from('tbl_contract_appendix');
        $this->db->where('status', 0);
        $data['pending_appendix'] = $this->db->count_all_results();
        
        // Phụ lục đã duyệt
        $this->db->from('tbl_contract_appendix');
        $this->db->where('status', 1);
        $data['approved_appendix'] = $this->db->count_all_results();
        
        // Phụ lục không duyệt
        $this->db->from('tbl_contract_appendix');
        $this->db->where('status', 2);
        $data['rejected_appendix'] = $this->db->count_all_results();
        
        // Phụ lục được tạo tháng này
        $this->db->from('tbl_contract_appendix');
        $this->db->where('DATE_FORMAT(date_created, "%Y-%m") =', date('Y-m'));
        $data['appendix_this_month'] = $this->db->count_all_results();
        
        // Tổng giá trị tăng lương (phụ lục đã duyệt)
        $this->db->select('SUM(salary) as total_salary_increase');
        $this->db->from('tbl_contract_appendix');
        $this->db->where('status', 1);
        $this->db->where('salary > 0');
        $result = $this->db->get()->row_array();
        $data['total_salary_increase'] = !empty($result['total_salary_increase']) ? $result['total_salary_increase'] : 0;
        
        // Tổng giá trị tăng lương vị trí (phụ lục đã duyệt)
        $this->db->select('SUM(salary_position) as total_position_increase');
        $this->db->from('tbl_contract_appendix');
        $this->db->where('status', 1);
        $this->db->where('salary_position > 0');
        $result = $this->db->get()->row_array();
        $data['total_position_increase'] = !empty($result['total_position_increase']) ? $result['total_position_increase'] : 0;
        
        echo json_encode($data);
    }

    /**
     * Thống kê phụ lục theo trạng thái
     */
    public function get_appendix_by_status(){
        $statusLabels = [
            0 => 'Chưa duyệt',
            1 => 'Đã duyệt',
            2 => 'Không duyệt'
        ];
        
        $this->db->select('status, COUNT(id) as total');
        $this->db->from('tbl_contract_appendix');
        $this->db->group_by('status');
        $result = $this->db->get()->result_array();
        
        $data = [
            'labels' => [],
            'values' => [],
            'colors' => []
        ];
        
        $colors = [
            0 => '#f39c12', // warning - chưa duyệt
            1 => '#00a65a', // success - đã duyệt
            2 => '#dd4b39'  // danger - không duyệt
        ];
        
        foreach ($result as $row) {
            $data['labels'][] = $statusLabels[$row['status']];
            $data['values'][] = (int)$row['total'];
            $data['colors'][] = $colors[$row['status']];
        }
        
        echo json_encode($data);
    }

    /**
     * Thống kê phụ lục theo tháng (12 tháng gần nhất)
     */
    public function get_appendix_by_month(){
        $data = [
            'labels' => [],
            'created' => [],
            'approved' => []
        ];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthLabel = date('m/Y', strtotime("-$i months"));
            
            // Phụ lục được tạo
            $this->db->from('tbl_contract_appendix');
            $this->db->where('DATE_FORMAT(date_created, "%Y-%m") =', $month);
            $totalCreated = $this->db->count_all_results();
            
            // Phụ lục được duyệt
            $this->db->from('tbl_contract_appendix');
            $this->db->where('DATE_FORMAT(date_status, "%Y-%m") =', $month);
            $this->db->where('status', 1);
            $totalApproved = $this->db->count_all_results();
            
            $data['labels'][] = $monthLabel;
            $data['created'][] = $totalCreated;
            $data['approved'][] = $totalApproved;
        }
        
        echo json_encode($data);
    }

    /**
     * Top 10 hợp đồng có nhiều phụ lục nhất
     */
    public function get_top_contracts_with_appendix(){
        $this->db->select('tbl_contract_labor.code as contract_code, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, COUNT(tbl_contract_appendix.id) as appendix_count');
        $this->db->from('tbl_contract_appendix');
        $this->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tbl_contract_appendix.contract_labor_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->group_by('tbl_contract_appendix.contract_labor_id');
        $this->db->order_by('appendix_count', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        echo json_encode($result);
    }

    /**
     * Danh sách phụ lục cần duyệt
     */
    public function get_pending_appendix(){
        $this->db->select('tbl_contract_appendix.*, tbl_contract_labor.code as contract_code, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name');
        $this->db->from('tbl_contract_appendix');
        $this->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tbl_contract_appendix.contract_labor_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->where('tbl_contract_appendix.status', 0);
        $this->db->order_by('tbl_contract_appendix.date_created', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        echo json_encode($result);
    }

    /**
     * Danh sách phụ lục đã duyệt gần đây
     */
    public function get_recent_approved_appendix(){
        $this->db->select('tbl_contract_appendix.*, tbl_contract_labor.code as contract_code, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name');
        $this->db->from('tbl_contract_appendix');
        $this->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tbl_contract_appendix.contract_labor_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->where('tbl_contract_appendix.status', 1);
        $this->db->order_by('tbl_contract_appendix.date_status', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        echo json_encode($result);
    }

    /**
     * Biểu đồ biến động lương theo tháng (phụ lục đã duyệt)
     */
    public function get_salary_changes_by_month(){
        $data = [
            'labels' => [],
            'salary_basic' => [],
            'salary_position' => []
        ];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthLabel = date('m/Y', strtotime("-$i months"));
            
            // Tổng thay đổi lương cơ bản
            $this->db->select('SUM(salary) as total_salary');
            $this->db->from('tbl_contract_appendix');
            $this->db->where('DATE_FORMAT(date_status, "%Y-%m") =', $month);
            $this->db->where('status', 1);
            $result = $this->db->get()->row_array();
            $totalSalary = !empty($result['total_salary']) ? $result['total_salary'] : 0;
            
            // Tổng thay đổi lương vị trí
            $this->db->select('SUM(salary_position) as total_position');
            $this->db->from('tbl_contract_appendix');
            $this->db->where('DATE_FORMAT(date_status, "%Y-%m") =', $month);
            $this->db->where('status', 1);
            $result = $this->db->get()->row_array();
            $totalPosition = !empty($result['total_position']) ? $result['total_position'] : 0;
            
            $data['labels'][] = $monthLabel;
            $data['salary_basic'][] = $totalSalary;
            $data['salary_position'][] = $totalPosition;
        }
        
        echo json_encode($data);
    }

    /**
     * Top 10 nhân viên có tổng tăng lương cao nhất
     */
    public function get_top_salary_increase(){
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tbl_contract_labor.code as contract_code, COUNT(tbl_contract_appendix.id) as appendix_count, SUM(tbl_contract_appendix.salary) as total_salary_increase, SUM(tbl_contract_appendix.salary_position) as total_position_increase');
        $this->db->from('tbl_contract_appendix');
        $this->db->join('tbl_contract_labor', 'tbl_contract_labor.id = tbl_contract_appendix.contract_labor_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->where('tbl_contract_appendix.status', 1);
        $this->db->group_by('tbl_contract_appendix.contract_labor_id');
        $this->db->order_by('(total_salary_increase + total_position_increase)', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        $data = [];
        foreach ($result as $row) {
            $row['total_increase'] = $row['total_salary_increase'] + $row['total_position_increase'];
            $data[] = $row;
        }
        
        echo json_encode($data);
    }

    /**
     * Phân bổ phụ lục theo loại thay đổi
     */
    public function get_appendix_change_distribution(){
        $data = [
            'labels' => [],
            'values' => []
        ];
        
        // Chỉ thay đổi lương cơ bản
        $this->db->from('tbl_contract_appendix');
        $this->db->where('salary > 0');
        $this->db->where('(salary_position IS NULL OR salary_position = 0)');
        $salaryOnly = $this->db->count_all_results();
        
        // Chỉ thay đổi lương vị trí
        $this->db->from('tbl_contract_appendix');
        $this->db->where('salary_position > 0');
        $this->db->where('(salary IS NULL OR salary = 0)');
        $positionOnly = $this->db->count_all_results();
        
        // Thay đổi cả hai
        $this->db->from('tbl_contract_appendix');
        $this->db->where('salary > 0');
        $this->db->where('salary_position > 0');
        $both = $this->db->count_all_results();
        
        // Không thay đổi lương (chỉ thay đổi thông tin khác)
        $this->db->from('tbl_contract_appendix');
        $this->db->where('(salary IS NULL OR salary = 0)');
        $this->db->where('(salary_position IS NULL OR salary_position = 0)');
        $none = $this->db->count_all_results();
        
        $data['labels'] = ['Chỉ lương cơ bản', 'Chỉ lương vị trí', 'Cả hai', 'Không thay đổi lương'];
        $data['values'] = [$salaryOnly, $positionOnly, $both, $none];
        
        echo json_encode($data);
    }

    /**
     * Tỷ lệ duyệt phụ lục theo người duyệt
     */
    public function get_approval_rate_by_user(){
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as user_name, 
            SUM(CASE WHEN tbl_contract_appendix.status = 1 THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN tbl_contract_appendix.status = 2 THEN 1 ELSE 0 END) as rejected,
            COUNT(tbl_contract_appendix.id) as total');
        $this->db->from('tbl_contract_appendix');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_appendix.user_status', 'inner');
        $this->db->where('tbl_contract_appendix.status !=', 0);
        $this->db->group_by('tbl_contract_appendix.user_status');
        $this->db->order_by('total', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        $data = [];
        foreach ($result as $row) {
            $row['approval_rate'] = $row['total'] > 0 ? round(($row['approved'] / $row['total']) * 100, 2) : 0;
            $data[] = $row;
        }
        
        echo json_encode($data);
    }
}
