<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_contract extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        //phân quyền
        if (!has_permission('dashboard_contract', '', 'view')) {
            access_denied('dashboard_contract');
            die;
        }
    }

    /**
     * Trang dashboard hợp đồng lao động
     */
    public function index(){
        $perViewDashboardContract = true;
        if (!$perViewDashboardContract){
            access_denied('dashboard_contract');
        }
        $data['title'] = _l('Dashboard Hợp đồng lao động');
        $this->load->view('admin/dashboard_contract/dashboard_contract', $data);
    }

    /**
     * Lấy thống kê tổng quan hợp đồng
     */
    public function get_overview_statistics(){
        $data = [];
        
        // Tổng số hợp đồng
        $this->db->from('tbl_contract_labor');
        $data['total_contracts'] = $this->db->count_all_results();
        
        // Hợp đồng chưa duyệt
        $this->db->from('tbl_contract_labor');
        $this->db->where('status', 0);
        $data['pending_contracts'] = $this->db->count_all_results();
        
        // Hợp đồng đã duyệt
        $this->db->from('tbl_contract_labor');
        $this->db->where('status', 1);
        $data['approved_contracts'] = $this->db->count_all_results();
        
        // Hợp đồng không duyệt
        $this->db->from('tbl_contract_labor');
        $this->db->where('status', 2);
        $data['rejected_contracts'] = $this->db->count_all_results();
        
        // Hợp đồng sắp hết hạn (trong 30 ngày)
        $this->db->from('tbl_contract_labor');
        $this->db->where('status', 1);
        $this->db->where('date_end IS NOT NULL');
        $this->db->where('date_end >=', date('Y-m-d'));
        $this->db->where('date_end <=', date('Y-m-d', strtotime('+30 days')));
        $data['expiring_contracts'] = $this->db->count_all_results();
        
        // Hợp đồng đã hết hạn
        $this->db->from('tbl_contract_labor');
        $this->db->where('date_end <', date('Y-m-d'));
        $data['expired_contracts'] = $this->db->count_all_results();
        
        // Lương cơ bản trung bình
        $this->db->select('AVG(salary_basic) as avg_salary');
        $this->db->from('tbl_contract_labor');
        $this->db->where('status', 1);
        $result = $this->db->get()->row_array();
        $data['avg_salary_basic'] = !empty($result['avg_salary']) ? round($result['avg_salary'], 0) : 0;
        
        // Lương vị trí trung bình
        $this->db->select('AVG(salary_position) as avg_salary_position');
        $this->db->from('tbl_contract_labor');
        $this->db->where('status', 1);
        $result = $this->db->get()->row_array();
        $data['avg_salary_position'] = !empty($result['avg_salary_position']) ? round($result['avg_salary_position'], 0) : 0;
        
        echo json_encode($data);
    }

    /**
     * Thống kê hợp đồng theo loại
     */
    public function get_contracts_by_type(){
        $this->db->select('tbl_type_contract.name as type_name, COUNT(tbl_contract_labor.id) as total');
        $this->db->from('tbl_contract_labor');
        $this->db->join('tbl_type_contract', 'tbl_type_contract.id = tbl_contract_labor.type_contract_id', 'inner');
        $this->db->group_by('tbl_contract_labor.type_contract_id');
        $result = $this->db->get()->result_array();
        
        $data = [
            'labels' => [],
            'values' => []
        ];
        
        foreach ($result as $row) {
            $data['labels'][] = $row['type_name'];
            $data['values'][] = (int)$row['total'];
        }
        
        echo json_encode($data);
    }

    /**
     * Thống kê hợp đồng theo trạng thái
     */
    public function get_contracts_by_status(){
        $statusLabels = [
            0 => 'Chưa duyệt',
            1 => 'Đã duyệt',
            2 => 'Không duyệt'
        ];
        
        $this->db->select('status, COUNT(id) as total');
        $this->db->from('tbl_contract_labor');
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
     * Thống kê hợp đồng theo tháng (12 tháng gần nhất)
     */
    public function get_contracts_by_month(){
        $data = [
            'labels' => [],
            'values' => []
        ];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthLabel = date('m/Y', strtotime("-$i months"));
            
            $this->db->from('tbl_contract_labor');
            $this->db->where('DATE_FORMAT(date_created, "%Y-%m") =', $month);
            $total = $this->db->count_all_results();
            
            $data['labels'][] = $monthLabel;
            $data['values'][] = $total;
        }
        
        echo json_encode($data);
    }

    /**
     * Danh sách hợp đồng sắp hết hạn
     */
    public function get_expiring_contracts(){
        $this->db->select('tbl_contract_labor.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tbl_type_contract.name as type_name');
        $this->db->from('tbl_contract_labor');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->join('tbl_type_contract', 'tbl_type_contract.id = tbl_contract_labor.type_contract_id', 'inner');
        $this->db->where('tbl_contract_labor.status', 1);
        $this->db->where('tbl_contract_labor.date_end IS NOT NULL');
        $this->db->where('tbl_contract_labor.date_end >=', date('Y-m-d'));
        $this->db->where('tbl_contract_labor.date_end <=', date('Y-m-d', strtotime('+30 days')));
        $this->db->order_by('tbl_contract_labor.date_end', 'ASC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        $data = [];
        foreach ($result as $row) {
            $daysLeft = floor((strtotime($row['date_end']) - time()) / (60 * 60 * 24));
            $data[] = [
                'id' => $row['id'],
                'code' => $row['code'],
                'staff_name' => $row['staff_name'],
                'type_name' => $row['type_name'],
                'date_end' => $row['date_end'],
                'days_left' => $daysLeft
            ];
        }
        
        echo json_encode($data);
    }

    /**
     * Danh sách hợp đồng cần duyệt
     */
    public function get_pending_contracts(){
        $this->db->select('tbl_contract_labor.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tbl_type_contract.name as type_name');
        $this->db->from('tbl_contract_labor');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->join('tbl_type_contract', 'tbl_type_contract.id = tbl_contract_labor.type_contract_id', 'inner');
        $this->db->where('tbl_contract_labor.status', 0);
        $this->db->order_by('tbl_contract_labor.date_created', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'id' => $row['id'],
                'code' => $row['code'],
                'staff_name' => $row['staff_name'],
                'type_name' => $row['type_name'],
                'salary_basic' => $row['salary_basic'],
                'salary_position' => $row['salary_position'],
                'date_created' => $row['date_created']
            ];
        }
        
        echo json_encode($data);
    }

    /**
     * Phân bổ lương theo khoảng
     */
    public function get_salary_distribution(){
        $ranges = [
            ['min' => 0, 'max' => 5000000, 'label' => 'Dưới 5 triệu'],
            ['min' => 5000000, 'max' => 10000000, 'label' => '5-10 triệu'],
            ['min' => 10000000, 'max' => 15000000, 'label' => '10-15 triệu'],
            ['min' => 15000000, 'max' => 20000000, 'label' => '15-20 triệu'],
            ['min' => 20000000, 'max' => 99999999999, 'label' => 'Trên 20 triệu']
        ];
        
        $data = [
            'labels' => [],
            'values' => []
        ];
        
        foreach ($ranges as $range) {
            $this->db->from('tbl_contract_labor');
            $this->db->where('status', 1);
            $this->db->where('salary_basic >=', $range['min']);
            $this->db->where('salary_basic <', $range['max']);
            $total = $this->db->count_all_results();
            
            $data['labels'][] = $range['label'];
            $data['values'][] = $total;
        }
        
        echo json_encode($data);
    }

    /**
     * Top 10 nhân viên có lương cao nhất
     */
    public function get_top_salary_staff(){
        $this->db->select('tbl_contract_labor.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, (tbl_contract_labor.salary_basic + tbl_contract_labor.salary_position) as total_salary');
        $this->db->from('tbl_contract_labor');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_contract_labor.staff_id', 'inner');
        $this->db->where('tbl_contract_labor.status', 1);
        $this->db->order_by('total_salary', 'DESC');
        $this->db->limit(10);
        $result = $this->db->get()->result_array();
        
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'staff_name' => $row['staff_name'],
                'code' => $row['code'],
                'salary_basic' => $row['salary_basic'],
                'salary_position' => $row['salary_position'],
                'total_salary' => $row['total_salary']
            ];
        }
        
        echo json_encode($data);
    }
}
