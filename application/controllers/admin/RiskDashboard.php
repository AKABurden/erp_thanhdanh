<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RiskDashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        // $this->load->model('Risk_model'); // Load model của bạn ở đây
    }

    public function index($active_tab = 'so_sanh')
    {
        // Danh sách các tab hợp lệ
        $valid_tabs = [
            'so_sanh',
            'canh_bao',
            'sinh_phieu',
            'phan_quyen',
            'phong_ban',
            'audit',
            'ke_toan',
            'bod',
            'audit_trail',
            'quy_trinh',
            'tai_lieu'
        ];

        // Nếu tab không hợp lệ, đưa về tab mặc định
        if (!in_array($active_tab, $valid_tabs)) {
            $active_tab = 'so_sanh';
        }

        $data['active_tab'] = $active_tab;

        // Lấy dữ liệu từ Model (Mock data mẫu)
        $data['mock_data'] = $this->get_mock_data();

        // Lấy cấu hình sinh phiếu từ DB
        if ($active_tab === 'sinh_phieu') {
            $rows = $this->db
                ->where_in('config_key', ['sw_canh_bao_do', 'sw_canh_bao_lap', 'sw_vuot_ngan_sach'])
                ->get('tbl_risk_config')
                ->result_array();
            $cfg = [];
            foreach ($rows as $r) {
                $cfg[$r['config_key']] = $r['config_value'];
            }
            $data['sinh_phieu_config'] = $cfg;
        }

        // Lấy danh sách phòng ban cho tab phong_ban
        if ($active_tab === 'phong_ban') {
            // $data['departments'] = $this->db->get('tbldepartments')->result_array();
            $data['departments'] = $this->db->get('tbl_room')->result_array();
        }

        // Load view chính (layout bao gồm sidebar, header và content)
        $this->load->view('admin/risk_dashboard/index', $data);
    }

    /**
     * AJAX: Lưu 1 cấu hình sinh phiếu vào DB
     * POST: key, value
     */
    public function save_sinh_phieu_config()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $allowed_keys = ['sw_canh_bao_do', 'sw_canh_bao_lap', 'sw_vuot_ngan_sach'];
        $key   = $this->input->post('key');
        $value = (int) $this->input->post('value');

        if (!in_array($key, $allowed_keys)) {
            echo json_encode(['status' => 'error', 'message' => 'Key không hợp lệ!']);
            return;
        }

        // Upsert: nếu đã tồn tại thì update, chưa thì insert
        $exists = $this->db->where('config_key', $key)->count_all_results('tbl_risk_config');
        if ($exists) {
            $this->db->where('config_key', $key)->update('tbl_risk_config', [
                'config_value' => $value,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->insert('tbl_risk_config', [
                'config_key'   => $key,
                'config_value' => $value,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        echo json_encode(['status' => 'success', 'message' => 'Đã lưu cấu hình!']);
    }

    /**
     * AJAX: DataTable data - tblproduction_report có nguồn tự động
     * Lọc: audit_id > 0 OR id_tasks_process > 0 OR entrance_ticket_id > 0 OR id_quotes > 0
     */
    public function get_sinh_phieu_table()
    {
        $this->db->select('
            pr.id,
            pr.reference_no,
            DATE_FORMAT(pr.date, "%d/%m/%Y") as date,
            pr.detail_tasks,
            pr.audit_id,
            pr.id_tasks,
            pr.entrance_ticket_id,
            pr.id_quotes,
            pr.violate,
            pr.countViolate as iteration_count,
            CONCAT("REQ-", pr.reference_no) as assessment_request_no,
            DATE_ADD(pr.date, INTERVAL 3 DAY) as proposed_deadline,
            IF(pr.violate = 1, "red", IF(pr.id_tasks > 0, "yellow", "green")) as severity_level,
            1 as action_plan_confirmed,
            CONCAT(s.firstname, " ", s.lastname) as staff_name,
            a.audit_code,
            t.name as task_name,
            et.reference_no as entrance_reference_no
        ', FALSE);
        $this->db->from('tblproduction_report pr');
        $this->db->join('tblstaff s', 's.staffid = pr.staff_responsible', 'left');
        $this->db->join('tbl_audit a', 'a.id = pr.audit_id', 'left');
        $this->db->join('tbltasks t', 't.id = pr.id_tasks', 'left');
        $this->db->join('tbl_entrance_ticket et', 'et.id = pr.entrance_ticket_id', 'left');
        $this->db->group_start();
        $this->db->where('pr.audit_id >', 0);
        $this->db->or_where('pr.id_tasks >', 0);
        $this->db->or_where('pr.entrance_ticket_id >', 0);
        $this->db->or_where('pr.id_quotes >', 0);
        $this->db->group_end();
        $this->db->order_by('pr.id', 'DESC');

        $rows = $this->db->get()->result_array();

        $data = [];
        foreach ($rows as $row) {
            // Xác định nhãn nguồn kèm mã phiếu
            $sources = [];
            if (!empty($row['audit_id'])) {
                $code = !empty($row['audit_code']) ? $row['audit_code'] : '#' . $row['audit_id'];
                $sources[] = 'Audit ' . $code;
            }
            if (!empty($row['id_tasks'])) {
                $code = !empty($row['task_name']) ? $row['task_name'] : '#' . $row['id_tasks'];
                $sources[] = 'Công việc: ' . $code;
            }
            if (!empty($row['entrance_ticket_id'])) {
                $code = !empty($row['entrance_reference_no']) ? $row['entrance_reference_no'] : '#' . $row['entrance_ticket_id'];
                $sources[] = 'Phiếu cổng ' . $code;
            }
            if (!empty($row['id_quotes']))          $sources[] = 'Báo giá #' . $row['id_quotes'];
            $row['source_label'] = implode(', ', $sources);

            // Trạng thái từ tbl_process_production_report
            $pending = $this->db
                ->where('production_report_id', $row['id'])
                ->where('staff_process', 0)
                ->count_all_results('tbl_process_production_report');
            $total = $this->db
                ->where('production_report_id', $row['id'])
                ->count_all_results('tbl_process_production_report');

            if ($total == 0) {
                $row['status_label'] = 'Chưa xử lý';
            } elseif ($pending > 0) {
                $row['status_label'] = 'Đang xử lý';
            } else {
                $row['status_label'] = 'Hoàn thành';
            }

            // Định dạng ngày deadline
            if (!empty($row['proposed_deadline'])) {
                $row['proposed_deadline'] = date('d/m/Y', strtotime($row['proposed_deadline']));
            }

            $data[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    /**
     * AJAX: Đếm công việc theo phòng ban (dùng cho tab phong_ban)
     */
    public function countTask()
    {
        $department_id = $this->input->get('department_id');

        $this->db->select("
            COUNT(tbltasks.id) AS count_all,
            SUM(CASE WHEN tbltasks.status = 5 THEN 1 ELSE 0 END) AS count_finish,
            SUM(CASE WHEN tbltasks.status != 5 THEN 1 ELSE 0 END) AS count_procesing
        ");
        $this->db->from('tbltasks');
        if ($department_id != -1) {
            $this->db->where('EXISTS (
                SELECT 1 FROM tbltask_department 
                WHERE tbltask_department.task_id = tbltasks.id 
                AND tbltask_department.department_id = ' . (int)$department_id . '
            )');
        }
        $tb_task = $this->db->get()->row_array();

        $data = [
            'count_all'       => (float)$tb_task['count_all'],
            'count_finish'    => (float)$tb_task['count_finish'],
            'count_procesing' => (float)$tb_task['count_procesing'],
        ];
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * AJAX: Lấy danh sách nhân viên theo phòng ban với các chỉ số HR
     * GET: department_id, month (Y-m), optional
     */
    public function get_phong_ban_staff_data()
    {
        $department_id = $this->input->get('department_id');
        $month         = $this->input->get('month') ?: date('Y-m');
        $month_start   = $month . '-01';
        $month_end     = date('Y-m-t', strtotime($month_start));

        // Lấy danh sách nhân viên thuộc phòng ban
        $this->db->select('s.staffid, s.firstname, s.lastname, s.email, s.profile_image');
        $this->db->from('tblstaff s');
        if (!empty($department_id) && $department_id != -1) {
            $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'inner');
            $this->db->join('tbldepartments', 'tbldepartments.departmentid = sd.departmentid', 'inner');
            $this->db->where('tbldepartments.room_id', (int)$department_id);
        }
        $this->db->where('s.active', 1);
        $this->db->group_by('s.staffid');
        $this->db->order_by('s.lastname', 'ASC');
        $staffs = $this->db->get()->result_array();

        $result = [];
        foreach ($staffs as $st) {
            $sid = $st['staffid'];

            // 1. Thời gian làm việc (giờ) - từ tblattendance hoặc tbl_checkin
            $work_row = null;
            if ($this->db->table_exists('tblattendance')) {
                $this->db->select('SUM(TIMESTAMPDIFF(HOUR, checkin_time, checkout_time)) as total_hours');
                $this->db->where('staffid', $sid);
                $this->db->where('date >=', $month_start);
                $this->db->where('date <=', $month_end);
                $work_row = $this->db->get('tblattendance')->row();
            }
            $work_hours = $work_row ? (float)$work_row->total_hours : 0;

            // 2. Tăng ca (giờ OT)
            $ot_row = null;
            if ($this->db->table_exists('tblovertime')) {
                $this->db->select_sum('hours', 'ot_hours');
                $this->db->where('staffid', $sid);
                $this->db->where('date >=', $month_start);
                $this->db->where('date <=', $month_end);
                $this->db->where('status', 'approved');
                $ot_row = $this->db->get('tblovertime')->row();
            }
            $ot_hours = $ot_row ? (float)$ot_row->ot_hours : 0;

            // 3. Vắng mặt (ngày)
            $absent_row = null;
            if ($this->db->table_exists('tblleave')) {
                $this->db->select_sum('noofdays', 'absent_days');
                $this->db->where('staffid', $sid);
                $this->db->where('startdate >=', $month_start);
                $this->db->where('startdate <=', $month_end);
                $this->db->where('status', 'approved');
                $this->db->where('leave_type !=', 'annual'); // không phải phép năm
                $absent_row = $this->db->get('tblleave')->row();
            }
            $absent_days = $absent_row ? (float)$absent_row->absent_days : 0;

            // 4. Phép (ngày nghỉ phép được duyệt)
            $leave_row = null;
            if ($this->db->table_exists('tblleave')) {
                $this->db->select_sum('noofdays', 'leave_days');
                $this->db->where('staffid', $sid);
                $this->db->where('startdate >=', $month_start);
                $this->db->where('startdate <=', $month_end);
                $this->db->where('status', 'approved');
                $this->db->where('leave_type', 'annual');
                $leave_row = $this->db->get('tblleave')->row();
            }
            $leave_days = $leave_row ? (float)$leave_row->leave_days : 0;

            // 5. BCKPH - Tất cả production_report của nhân viên trong tháng
            //    (BCKPH = Báo Cáo Kết Quả Hoàn Phiếu - tất cả report, không phân biệt vi phạm)
            $bckph_total = (int)$this->db
                ->where('staff_responsible', $sid)
                ->where('date >=', $month_start)
                ->where('date <=', $month_end)
                ->count_all_results('tblproduction_report');

            // BCKPH chưa xử lý xong (còn bước chưa duyệt)
            $bckph_pending = (int)$this->db
                ->where('staff_responsible', $sid)
                ->where('date >=', $month_start)
                ->where('date <=', $month_end)
                ->where('EXISTS (
                    SELECT 1 FROM tbl_process_production_report ppr
                    WHERE ppr.production_report_id = tblproduction_report.id
                    AND ppr.staff_process = 0
                )')
                ->count_all_results('tblproduction_report');

            // 6. BC Vi phạm: violate = 1
            $violate_count = (int)$this->db
                ->where('staff_responsible', $sid)
                ->where('violate', 1)
                ->where('date >=', $month_start)
                ->where('date <=', $month_end)
                ->count_all_results('tblproduction_report');

            // Tổng điểm vi phạm (trouble_violation_point)
            $this->db->select('SUM(IFNULL(trouble_violation_point, 0)) as tong_diem_vi_pham');
            $this->db->where('staff_responsible', $sid);
            $this->db->where('violate', 1);
            $this->db->where('date >=', $month_start);
            $this->db->where('date <=', $month_end);
            $vp_row = $this->db->get('tblproduction_report')->row();
            $tong_diem_vi_pham = $vp_row ? (float)$vp_row->tong_diem_vi_pham : 0;

            // 7. Thiết hại: production_report có damage_cost > 0 hoặc violate=1
            //    Dùng đúng tên cột: damage_cost, production_stage (công đoạn)
            //    Đã xử lý: tất cả bước trong tbl_process_production_report đều có staff_process > 0
            $this->db->select('
                pr.id,
                pr.reference_no,
                pr.date,
                pr.damage_cost,
                pr.production_stage,
                pr.countViolate,
                pr.trouble_violation_point,
                (
                    SELECT COUNT(*) FROM tbl_process_production_report ppr2
                    WHERE ppr2.production_report_id = pr.id AND ppr2.staff_process = 0
                ) as pending_steps
            ');
            $this->db->from('tblproduction_report pr');
            $this->db->where('pr.staff_responsible', $sid);
            $this->db->where('pr.violate', 1);
            $this->db->where('pr.date >=', $month_start);
            $this->db->where('pr.date <=', $month_end);
            $damage_raw = $this->db->get()->result_array();

            $has_damage    = count($damage_raw) > 0;
            $damage_detail = [];
            foreach ($damage_raw as $dr) {
                $damage_detail[] = [
                    'ref_no'           => $dr['reference_no'] ?: '#' . $dr['id'],
                    'date'             => date('d/m/Y', strtotime($dr['date'])),
                    'damage_cost'      => (float)($dr['damage_cost'] ?? 0),           // Chi phí thiết hại
                    'production_stage' => $dr['production_stage'] ?? '',              // Công đoạn
                    'count_violate'    => (int)($dr['countViolate'] ?? 0),            // Số lần vi phạm
                    'violation_point'  => (float)($dr['trouble_violation_point'] ?? 0), // Điểm trừ
                    'da_xu_ly'         => (int)($dr['pending_steps'] ?? 1) === 0,    // true nếu xử lý xong
                ];
            }

            $result[] = [
                'staffid'            => $sid,
                'name'               => trim($st['firstname'] . ' ' . $st['lastname']),
                'work_hours'         => $work_hours,
                'ot_hours'           => $ot_hours,
                'absent_days'        => $absent_days,
                'leave_days'         => $leave_days,
                'bckph_total'        => $bckph_total,
                'bckph_pending'      => $bckph_pending,
                'violate_count'      => $violate_count,
                'tong_diem_vi_pham'  => $tong_diem_vi_pham,
                'has_damage'         => $has_damage,
                'damage_detail'      => $damage_detail,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $result,
            'month'   => $month,
        ]);
    }

    /**
     * AJAX: Audit tuần theo từng phòng ban
     * Trả về: số audit tuần này, tuần trước, tổng tháng, tỷ lệ tuân thủ, BC vi phạm, BC KPH
     */
    public function get_audit_weekly_dept()
    {
        $curr_week_start = date('Y-m-d', strtotime('monday this week'));
        $curr_week_end   = date('Y-m-d', strtotime('sunday this week'));
        $last_week_start = date('Y-m-d', strtotime('monday last week'));
        $last_week_end   = date('Y-m-d', strtotime('sunday last week'));
        $month_start     = date('Y-m-01');
        $month_end       = date('Y-m-t');

        // Lấy danh sách phòng ban (dùng tbl_room vì project dùng room_id)
        $rooms = $this->db->get('tbl_room')->result_array();

        $data   = [];
        $weekly_cats = [];

        foreach ($rooms as $room) {
            $room_id = $room['id'];
            $dept_name = $room['name'];

            // Hàm đếm audit theo room qua join tbl_audit -> tbl_room (qua dept)
            $count_audit = function ($date_start, $date_end) use ($room_id) {
                $this->db->select('COUNT(a.id) as cnt');
                $this->db->from('tbl_audit a');
                $this->db->join('tbl_room r', 'r.id = a.dept_id', 'left');
                $this->db->where('r.id', $room_id);
                $this->db->where('a.audit_date >=', $date_start);
                $this->db->where('a.audit_date <=', $date_end);
                $row = $this->db->get()->row();
                return $row ? (int)$row->cnt : 0;
            };

            $this_week  = $count_audit($curr_week_start, $curr_week_end);
            $last_week  = $count_audit($last_week_start, $last_week_end);
            $month_total = $count_audit($month_start, $month_end);

            // Tỷ lệ tuân thủ trung bình tháng
            $this->db->select_avg('result_percentage', 'avg_pct');
            $this->db->from('tbl_audit a');
            $this->db->join('tbl_room r', 'r.id = a.dept_id', 'left');
            $this->db->where('r.id', $room_id);
            $this->db->where('a.status', 'COMPLETED');
            $this->db->where('a.audit_date >=', $month_start);
            $comp_row = $this->db->get()->row();
            $compliance_pct = $comp_row ? round((float)$comp_row->avg_pct, 1) : 0;

            // BC Vi phạm trong tháng (tblproduction_report qua staff thuộc room)
            $this->db->select('COUNT(pr.id) as vp_count, SUM(IFNULL(pr.trouble_violation_point, 0)) as vp_point');
            $this->db->from('tblproduction_report pr');
            $this->db->join('tblstaff s', 's.staffid = pr.staff_responsible', 'left');
            $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left');
            $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
            $this->db->where('d.room_id', $room_id);
            $this->db->where('pr.violate', 1);
            $this->db->where('pr.date >=', $month_start);
            $this->db->where('pr.date <=', $month_end);
            $vp_row = $this->db->get()->row();
            $violate_count = $vp_row ? (int)$vp_row->vp_count : 0;
            $violate_point = $vp_row ? (float)$vp_row->vp_point : 0;

            // BC KPH trong tháng
            $this->db->select('COUNT(pr.id) as kph_total');
            $this->db->from('tblproduction_report pr');
            $this->db->join('tblstaff s', 's.staffid = pr.staff_responsible', 'left');
            $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left');
            $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
            $this->db->where('d.room_id', $room_id);
            $this->db->where('pr.date >=', $month_start);
            $this->db->where('pr.date <=', $month_end);
            $kph_row = $this->db->get()->row();
            $kph_total = $kph_row ? (int)$kph_row->kph_total : 0;

            // KPH chờ xử lý
            $kph_pending = (int)$this->db
                ->from('tblproduction_report pr')
                ->join('tblstaff s', 's.staffid = pr.staff_responsible', 'left')
                ->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left')
                ->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left')
                ->where('d.room_id', $room_id)
                ->where('pr.date >=', $month_start)
                ->where('pr.date <=', $month_end)
                ->where('EXISTS (
                    SELECT 1 FROM tbl_process_production_report ppr
                    WHERE ppr.production_report_id = pr.id AND ppr.staff_process = 0
                )')
                ->count_all_results();

            $data[] = [
                'department'     => $dept_name,
                'room_id'        => $room_id,
                'this_week'      => $this_week,
                'last_week'      => $last_week,
                'month_total'    => $month_total,
                'compliance_pct' => $compliance_pct,
                'violate_count'  => $violate_count,
                'violate_point'  => $violate_point,
                'kph_total'      => $kph_total,
                'kph_pending'    => $kph_pending,
            ];
        }

        // Danh mục audit tuần (nhóm theo department của audit_checklist)
        $this->db->select('ac.item_text as category,
            COUNT(ac.id) as week_count,
            SUM(CASE WHEN ac.status = "yes" THEN 1 ELSE 0 END) as yes_count,
            SUM(CASE WHEN ac.status = "no"  THEN 1 ELSE 0 END) as no_count');
        $this->db->from('tbl_audit_checklist ac');
        $this->db->join('tbl_audit a', 'a.id = ac.audit_id', 'left');
        $this->db->where('a.audit_date >=', $curr_week_start);
        $this->db->where('a.audit_date <=', $curr_week_end);
        $this->db->group_by('ac.item_text');
        $this->db->order_by('week_count', 'DESC');
        $this->db->limit(20);
        $weekly_cats = $this->db->get()->result_array();

        header('Content-Type: application/json');
        echo json_encode([
            'success'     => true,
            'data'        => $data,
            'weekly_cats' => $weekly_cats,
        ]);
    }

    /**
     * AJAX: Lấy danh sách Action chờ duyệt (tbl_audit_action_request)
     * Các loại: re_evaluate, transfer, resign, promote, salary
     * Nguồn: ktnb, ksrr, bod, audit
     */
    public function get_audit_action_pending()
    {
        // Nếu bảng chưa tồn tại → trả về mock rỗng
        if (!$this->db->table_exists('tbl_audit_action_request')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => []]);
            return;
        }

        $this->db->select('
            r.id, r.action_type, r.source, r.reason, r.status,
            DATE_FORMAT(r.created_at, "%d/%m/%Y") as created_date,
            CONCAT(s.firstname, " ", s.lastname) as staff_name
        ');
        $this->db->from('tbl_audit_action_request r');
        $this->db->join('tblstaff s', 's.staffid = r.staff_id', 'left');
        $this->db->order_by('r.id', 'DESC');
        $this->db->limit(50);
        $rows = $this->db->get()->result_array();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $rows]);
    }

    /**
     * AJAX: Duyệt / Từ chối action request
     * POST: id, status (approved|rejected)
     */
    public function approve_audit_action()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id     = (int)$this->input->post('id');
        $status = $this->input->post('status');

        if (!in_array($status, ['approved', 'rejected']) || $id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        if (!$this->db->table_exists('tbl_audit_action_request')) {
            echo json_encode(['success' => false, 'message' => 'Bảng chưa tồn tại']);
            return;
        }

        $this->db->where('id', $id);
        $this->db->update('tbl_audit_action_request', [
            'status'      => $status,
            'approved_by' => get_staff_user_id(),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['success' => true, 'message' => 'Đã cập nhật trạng thái']);
    }

    /**
     * AJAX: Lấy dữ liệu tổng hợp cho Dashboard Audit
     */
    public function get_audit_dashboard_data()
    {
        // 1. Summary cards
        $total_audits = $this->db->count_all_results('tbl_audit');

        $this->db->where('status', 'COMPLETED');
        $completed_audits = $this->db->count_all_results('tbl_audit');

        $this->db->where('status', 'IN_PROGRESS');
        $in_progress = $this->db->count_all_results('tbl_audit');

        // Tỷ lệ tuân thủ toàn công ty: trung bình result_percentage của audit COMPLETED
        $this->db->select_avg('result_percentage', 'avg_compliance');
        $this->db->where('status', 'COMPLETED');
        $this->db->where('result_percentage >', 0);
        $avg_row = $this->db->get('tbl_audit')->row();
        $avg_compliance = $avg_row ? round($avg_row->avg_compliance, 1) : 0;

        // Tổng critical issues (checklist status=no)
        $this->db->where('status', 'no');
        $critical_issues = $this->db->count_all_results('tbl_audit_checklist');

        // 2. Tỷ lệ tuân thủ theo phòng ban (department) - từ tbl_audit COMPLETED
        $this->db->select('department, AVG(result_percentage) as avg_pct, COUNT(*) as cnt');
        $this->db->from('tbl_audit');
        $this->db->where('status', 'COMPLETED');
        $this->db->where('result_percentage >', 0);
        $this->db->group_by('department');
        $this->db->order_by('avg_pct', 'ASC');
        $compliance_by_dept = $this->db->get()->result_array();

        // 3. Top issues: checklist items bị đánh NO nhiều nhất
        $this->db->select('item_text, COUNT(*) as count_no');
        $this->db->from('tbl_audit_checklist');
        $this->db->where('status', 'no');
        $this->db->group_by('item_text');
        $this->db->order_by('count_no', 'DESC');
        $this->db->limit(10);
        $top_issues = $this->db->get()->result_array();

        // 4. CAPA statistics
        $total_capa = $this->db->count_all_results('tbl_audit_capa');

        $this->db->where('status', 'OPEN');
        $capa_open = $this->db->count_all_results('tbl_audit_capa');

        $this->db->where('status', 'COMPLETED');
        $capa_completed = $this->db->count_all_results('tbl_audit_capa');

        // 5. Danh sách audit gần nhất
        $this->db->select('id, audit_code, department, audit_date, team_leader, result_percentage, status');
        $this->db->from('tbl_audit');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(10);
        $recent_audits = $this->db->get()->result_array();

        // 6. Override theo phòng ban: đếm audit items bị "no" group by department (từ tbl_audit)
        $this->db->select('a.department, COUNT(c.id) as no_count');
        $this->db->from('tbl_audit_checklist c');
        $this->db->join('tbl_audit a', 'a.id = c.audit_id', 'left');
        $this->db->where('c.status', 'no');
        $this->db->group_by('a.department');
        $this->db->order_by('no_count', 'DESC');
        $this->db->limit(10);
        $override_by_dept = $this->db->get()->result_array();

        // 7. Compliance chart data (yes vs no vs pending)
        $this->db->where('status', 'yes');
        $yes_count = $this->db->count_all_results('tbl_audit_checklist');

        $this->db->where('status', 'no');
        $no_count = $this->db->count_all_results('tbl_audit_checklist');

        $this->db->where('status IS NULL OR status = ""');
        $pending_count = $this->db->count_all_results('tbl_audit_checklist');

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_audits'     => (int)$total_audits,
                    'completed_audits' => (int)$completed_audits,
                    'in_progress'      => (int)$in_progress,
                    'avg_compliance'   => (float)$avg_compliance,
                    'critical_issues'  => (int)$critical_issues,
                ],
                'compliance_chart' => [
                    'yes'     => (int)$yes_count,
                    'no'      => (int)$no_count,
                    'pending' => (int)$pending_count,
                ],
                'compliance_by_dept' => $compliance_by_dept,
                'top_issues'         => $top_issues,
                'capa' => [
                    'total'     => (int)$total_capa,
                    'open'      => (int)$capa_open,
                    'completed' => (int)$capa_completed,
                ],
                'recent_audits'    => $recent_audits,
                'override_by_dept' => $override_by_dept,
            ]
        ]);
    }

    /**
     * AJAX: Lấy dữ liệu tổng hợp cho Dashboard Kế toán
     * - Chi phí thực tế: tblother_payslips (is_advance=0, type_vouchers!=1)
     * - Chi phí dự kiến: tblfinancial_control_detail + tblcosts
     */
    public function get_ke_toan_dashboard_data()
    {
        $year  = $this->input->get('year') ? (int)$this->input->get('year') : (int)date('Y');
        $month = $this->input->get('month') ? (int)$this->input->get('month') : (int)date('m');

        $months_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $months_vi = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $current_month_col = $months_en[$month - 1];

        // ========== 1. CHI PHÍ THỰC TẾ (tblother_payslips) ==========
        // Tháng hiện tại
        $month_start = sprintf('%04d-%02d-01', $year, $month);
        $month_end   = date('Y-m-t', strtotime($month_start));

        $this->db->select_sum('total');
        $this->db->where('is_advance', 0);
        $this->db->where('type_vouchers !=', 1);
        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $actual_row = $this->db->get('tblother_payslips')->row();
        $actual_month = $actual_row ? (float)$actual_row->total : 0;

        // Cả năm
        $year_start = $year . '-01-01';
        $year_end   = $year . '-12-31';

        $this->db->select_sum('total');
        $this->db->where('is_advance', 0);
        $this->db->where('type_vouchers !=', 1);
        $this->db->where('date >=', $year_start);
        $this->db->where('date <=', $year_end);
        $actual_year_row = $this->db->get('tblother_payslips')->row();
        $actual_year = $actual_year_row ? (float)$actual_year_row->total : 0;

        // ========== 2. CHI PHÍ DỰ KIẾN (tblfinancial_control_detail) ==========
        // Tháng hiện tại
        $this->db->select_sum($current_month_col, 'planned_total');
        $this->db->where('nam', $year);
        $planned_row = $this->db->get('tblfinancial_control_detail')->row();
        $planned_month = $planned_row ? (float)$planned_row->planned_total : 0;

        // Cả năm: tổng 12 tháng
        $sum_cols = implode(' + ', array_map(function ($c) {
            return "IFNULL(`$c`, 0)";
        }, $months_en));
        $this->db->select("SUM($sum_cols) as planned_year_total");
        $this->db->where('nam', $year);
        $planned_year_row = $this->db->get('tblfinancial_control_detail')->row();
        $planned_year = $planned_year_row ? (float)$planned_year_row->planned_year_total : 0;

        // Tỷ lệ sử dụng ngân sách
        $budget_usage = $planned_month > 0 ? round(($actual_month / $planned_month) * 100, 1) : 0;

        // ========== 3. TREND THEO THÁNG (12 tháng) ==========
        $monthly_actual  = [];
        $monthly_planned = [];

        for ($m = 1; $m <= 12; $m++) {
            $m_start = sprintf('%04d-%02d-01', $year, $m);
            $m_end   = date('Y-m-t', strtotime($m_start));

            $this->db->select_sum('total');
            $this->db->where('is_advance', 0);
            $this->db->where('type_vouchers !=', 1);
            $this->db->where('date >=', $m_start);
            $this->db->where('date <=', $m_end);
            $r = $this->db->get('tblother_payslips')->row();
            $monthly_actual[] = $r ? (float)$r->total : 0;

            // Planned for this month
            $col = $months_en[$m - 1];
            $this->db->select_sum($col, 'ptotal');
            $this->db->where('nam', $year);
            $pr = $this->db->get('tblfinancial_control_detail')->row();
            $monthly_planned[] = $pr ? (float)$pr->ptotal : 0;
        }

        // ========== 4. CHI PHÍ THEO LOẠI (top cost categories) ==========
        $this->db->select('c.name as cost_name, SUM(p.total) as sum_total');
        $this->db->from('tblother_payslips p');
        $this->db->join('tblcosts c', 'c.id = p.id_costs', 'left');
        $this->db->where('p.is_advance', 0);
        $this->db->where('p.type_vouchers !=', 1);
        $this->db->where('p.date >=', $month_start);
        $this->db->where('p.date <=', $month_end);
        $this->db->where('p.id_costs >', 0);
        $this->db->group_by('p.id_costs');
        $this->db->order_by('sum_total', 'DESC');
        $this->db->limit(10);
        $cost_by_category = $this->db->get()->result_array();

        // ========== 5. CHI PHÍ THEO ĐỐI TƯỢNG (objects: KH, NCC, NV, Khác) ==========
        $this->db->select('objects, SUM(total) as sum_total');
        $this->db->from('tblother_payslips');
        $this->db->where('is_advance', 0);
        $this->db->where('type_vouchers !=', 1);
        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $this->db->group_by('objects');
        $this->db->order_by('sum_total', 'DESC');
        $cost_by_object = $this->db->get()->result_array();

        $object_names = [1 => 'Khách hàng', 2 => 'Nhà cung cấp', 3 => 'Nhân viên', 4 => 'Khác', 5 => 'TSCĐ'];
        foreach ($cost_by_object as &$obj) {
            $obj['object_name'] = isset($object_names[(int)$obj['objects']]) ? $object_names[(int)$obj['objects']] : 'N/A';
        }

        // ========== 6. PHIẾU CHI GẦN NHẤT ==========
        $this->db->select('p.id, p.code, p.prefix, p.date, p.total, p.objects, p.objects_text, c.name as cost_name');
        $this->db->from('tblother_payslips p');
        $this->db->join('tblcosts c', 'c.id = p.id_costs', 'left');
        $this->db->where('p.is_advance', 0);
        $this->db->where('p.type_vouchers !=', 1);
        $this->db->order_by('p.id', 'DESC');
        $this->db->limit(10);
        $recent_payslips = $this->db->get()->result_array();

        // ========== 7. CẢNH BÁO ==========
        $warnings = [];

        // --- Cảnh báo ngân sách tổng ---
        if ($budget_usage > 90) {
            $warnings[] = [
                'level' => 'danger',
                'title' => 'Ngân sách tháng ' . $month . ' sắp cạn',
                'desc'  => 'Đã sử dụng ' . $budget_usage . '% ngân sách dự kiến. Cần kiểm soát chi tiêu.',
            ];
        } elseif ($budget_usage > 75) {
            $warnings[] = [
                'level' => 'warning',
                'title' => 'Ngân sách tháng ' . $month . ' đang cao',
                'desc'  => 'Đã sử dụng ' . $budget_usage . '% ngân sách dự kiến.',
            ];
        }

        // --- Cảnh báo VAT đầu vào / đầu ra ---
        // vat_dauvao = số tiền thuế đầu vào khai báo (số tiền, không phải %)
        $vat_dauvao_declared = (float)get_option('vat_dauvao');

        // Tính thuế thực tế từ purchase_order đã có phiếu chi trong tháng
        // Phiếu chi link tới purchase_order: objects=2 (NCC) + type_vouchers=1 (nhập hàng)
        // hoặc objects=1 (KH) + type_vouchers=1
        $this->db->dbprefix = '';
        $this->db->select('SUM(po.totalAll_suppliers - po.total_novat) as actual_tax');
        $this->db->from('tblpurchase_order po');
        $this->db->where('po.date >= "' . $month_start . '" AND po.date <= "' . $month_end . '"');
        // $this->db->where('EXISTS (
        //     SELECT 1 FROM tblother_payslips op
        //     WHERE op.vouchers_id = po.id
        //     AND op.type_vouchers = 1
        //     AND op.date >= "' . $month_start . '"
        //     AND op.date <= "' . $month_end . '"
        // )');
        $tax_row = $this->db->get()->row();
        $actual_tax = $tax_row ? (float)$tax_row->actual_tax : 0;

        if ($vat_dauvao_declared > 0 && $actual_tax > 0) {
            $vat_diff = abs($vat_dauvao_declared - $actual_tax);
            if ($vat_diff > 0) {
                $vat_diff_pct = round(($vat_diff / $vat_dauvao_declared) * 100, 1);
                if ($vat_diff_pct > 20) {
                    $warnings[] = [
                        'level' => 'danger',
                        'title' => 'Chênh lệch VAT đầu vào/đầu ra',
                        'desc'  => 'Phát hiện chênh lệch ' . number_format($vat_diff, 0, ',', '.') . ' VNĐ ('
                            . $vat_diff_pct . '%). Thuế khai báo: ' . number_format($vat_dauvao_declared, 0, ',', '.')
                            . ' VNĐ, Thuế thực tế từ đơn mua: ' . number_format($actual_tax, 0, ',', '.')
                            . ' VNĐ. Cần kiểm tra lại hóa đơn.',
                    ];
                } elseif ($vat_diff_pct > 10) {
                    $warnings[] = [
                        'level' => 'warning',
                        'title' => 'Biến động VAT đầu vào',
                        'desc'  => 'Chênh lệch ' . number_format($vat_diff, 0, ',', '.') . ' VNĐ ('
                            . $vat_diff_pct . '%) giữa thuế khai báo (' . number_format($vat_dauvao_declared, 0, ',', '.')
                            . ') và thuế thực tế từ đơn mua (' . number_format($actual_tax, 0, ',', '.') . ').',
                    ];
                }
            }
        } elseif ($actual_tax > 0 && $vat_dauvao_declared == 0) {
            $warnings[] = [
                'level' => 'warning',
                'title' => 'Chưa khai báo thuế đầu vào',
                'desc'  => 'Có ' . number_format($actual_tax, 0, ',', '.') . ' VNĐ thuế từ đơn mua đã thanh toán nhưng chưa cấu hình thuế đầu vào trong Settings.',
            ];
        }

        // --- Cảnh báo chi phí theo loại vượt dự kiến ---
        foreach ($cost_by_category as $cat) {
            $cat_actual = (float)$cat['sum_total'];
            if ($cat_actual <= 0) continue;

            // Tìm dự kiến cho loại chi phí này
            $this->db->select('c.id as cost_id, c.name');
            $this->db->from('tblcosts c');
            $this->db->where('c.name', $cat['cost_name']);
            $this->db->limit(1);
            $cost_row = $this->db->get()->row();

            if ($cost_row) {
                $this->db->select_sum($current_month_col, 'cat_planned');
                $this->db->where('id_financial_control', $cost_row->cost_id);
                $this->db->where('nam', $year);
                $cat_plan_row = $this->db->get('tblfinancial_control_detail')->row();
                $cat_planned = $cat_plan_row ? (float)$cat_plan_row->cat_planned : 0;

                if ($cat_planned > 0) {
                    $cat_usage = round(($cat_actual / $cat_planned) * 100, 1);
                    if ($cat_usage > 95) {
                        $warnings[] = [
                            'level' => 'danger',
                            'title' => ($cat['cost_name'] ?: 'Chi phí') . ' vượt định mức',
                            'desc'  => 'Đã sử dụng ' . $cat_usage . '% ngân sách dự kiến ('
                                . number_format($cat_actual, 0, ',', '.') . ' / '
                                . number_format($cat_planned, 0, ',', '.') . ' VNĐ).',
                        ];
                    } elseif ($cat_usage > 80) {
                        $warnings[] = [
                            'level' => 'warning',
                            'title' => ($cat['cost_name'] ?: 'Chi phí') . ' đang gần mức giới hạn',
                            'desc'  => 'Đã sử dụng ' . $cat_usage . '% ngân sách ('
                                . number_format($cat_actual, 0, ',', '.') . ' / '
                                . number_format($cat_planned, 0, ',', '.') . ' VNĐ).',
                        ];
                    }
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'summary' => [
                    'actual_month'   => $actual_month,
                    'planned_month'  => $planned_month,
                    'actual_year'    => $actual_year,
                    'planned_year'   => $planned_year,
                    'budget_usage'   => $budget_usage,
                ],
                'monthly_trend' => [
                    'labels'  => $months_vi,
                    'actual'  => $monthly_actual,
                    'planned' => $monthly_planned,
                ],
                'cost_by_category' => $cost_by_category,
                'cost_by_object'   => $cost_by_object,
                'recent_payslips'  => $recent_payslips,
                'warnings'         => $warnings,
                'special_costs'    => $this->_get_special_costs($month_start, $month_end),
                'salary_by_dept'   => $this->_get_salary_by_dept($month_start, $month_end),
                'filter' => [
                    'year'  => $year,
                    'month' => $month,
                ],
            ]
        ]);
    }

    /**
     * Tính các nhóm chi phí đặc biệt bằng text matching tên loại chi phí (tblcosts.name)
     * Mỗi nhóm: tổng chi thực tế trong kỳ, số phiếu, chi gấp/kế hoạch
     */
    private function _get_special_costs($month_start, $month_end)
    {
        // Định nghĩa các nhóm chi phí với từ khoá LIKE (không phân biệt hoa thường)
        $groups = [
            'tang_ca'    => ['label' => 'Chi Phí Tăng Ca',     'keywords' => ['tăng ca', 'tang ca', 'overtime', 'OT']],
            'gia_cong'   => ['label' => 'Chi Phí Gia Công',     'keywords' => ['gia công', 'gia cong', 'outsource']],
            'sua_chua'   => ['label' => 'Chi Phí Sửa Chữa',    'keywords' => ['sửa chữa', 'sua chua', 'bảo trì', 'bao tri', 'repair', 'maintenance']],
            'tuyen_dung' => ['label' => 'Chi Phí Tuyển Dụng',  'keywords' => ['tuyển dụng', 'tuyen dung', 'tuyển', 'recruit']],
            'dao_tao'    => ['label' => 'Chi Phí Đào Tạo',     'keywords' => ['đào tạo', 'dao tao', 'training', 'huấn luyện', 'huan luyen']],
            'gap_khan'   => ['label' => 'Chi Gấp Khẩn',        'keywords' => ['gấp', 'khẩn', 'gap', 'khan', 'urgent', 'emergency']],
            'ke_hoach'   => ['label' => 'Chi Theo Kế Hoạch',   'keywords' => ['kế hoạch', 'ke hoach', 'planned', 'plan']],
            'luong'      => ['label' => 'Chi Phí Lương',        'keywords' => ['lương', 'luong', 'salary', 'thưởng', 'thuong', 'BHXH', 'bảo hiểm', 'bao hiem']],
        ];

        $result = [];
        foreach ($groups as $key => $group) {
            // Build WHERE: c.name LIKE '%kw1%' OR c.name LIKE '%kw2%' ...
            $likes = [];
            foreach ($group['keywords'] as $kw) {
                $safe_kw = $this->db->escape_like_str($kw);
                $likes[] = "c.name LIKE '%{$safe_kw}%'";
            }
            $where_like = '(' . implode(' OR ', $likes) . ')';

            // Tổng chi thực tế
            $this->db->select('SUM(p.total) as sum_total, COUNT(p.id) as so_phieu');
            $this->db->from('tblother_payslips p');
            $this->db->join('tblcosts c', 'c.id = p.id_costs', 'left');
            $this->db->where('p.is_advance', 0);
            $this->db->where('p.type_vouchers !=', 1);
            $this->db->where('p.date >=', $month_start);
            $this->db->where('p.date <=', $month_end);
            $this->db->where($where_like, null, false);
            $row = $this->db->get()->row();

            // Phân loại gấp/kế hoạch dựa vào note hoặc tên cost
            $gap_keywords   = ['gấp', 'khẩn', 'gap', 'khan', 'urgent'];
            $glik = [];
            foreach ($group['keywords'] as $kw) {
                $sk = $this->db->escape_like_str($kw);
                $glik[] = "c.name LIKE '%{$sk}%'";
            }
            foreach ($gap_keywords as $gw) {
                $sk = $this->db->escape_like_str($gw);
                $glik[] = "p.note LIKE '%{$sk}%'";
            }
            $is_gap_where = '(' . implode(' OR ', $glik) . ')';

            // Chỉ tính chi gấp/khẩn nếu nhóm đó không phải nhóm 'gap_khan' chính
            $chi_gap = 0;
            if ($key !== 'gap_khan') {
                // Không tách thêm cho nhóm khác
            } else {
                // Nhóm gấp khẩn: lấy tổng là chính
            }

            $result[$key] = [
                'label'     => $group['label'],
                'sum_total' => $row ? (float)$row->sum_total : 0,
                'so_phieu'  => $row ? (int)$row->so_phieu : 0,
            ];
        }

        // Tách thêm: trong nhóm "luong" lấy tổng vs chi gấp/kế hoạch
        // Chi gấp: note chứa từ khoá "gấp/khẩn"
        $luong_glikwhere = [];
        foreach (['lương', 'luong', 'salary'] as $kw) {
            $sk = $this->db->escape_like_str($kw);
            $luong_glikwhere[] = "c.name LIKE '%{$sk}%'";
        }
        $luong_name_where = '(' . implode(' OR ', $luong_glikwhere) . ')';

        foreach (['gap_khan', 'ke_hoach'] as $cat) {
            $kw_list = $cat === 'gap_khan'
                ? ['gấp', 'khẩn', 'gap', 'khan', 'urgent']
                : ['kế hoạch', 'ke hoach', 'plan'];
            $note_likes = [];
            foreach ($kw_list as $kw) {
                $sk = $this->db->escape_like_str($kw);
                $note_likes[] = "p.note LIKE '%{$sk}%'";
                $note_likes[] = "c.name LIKE '%{$sk}%'";
            }
            $note_where = '(' . implode(' OR ', $note_likes) . ')';

            $this->db->select('SUM(p.total) as sum_total, COUNT(p.id) as so_phieu');
            $this->db->from('tblother_payslips p');
            $this->db->join('tblcosts c', 'c.id = p.id_costs', 'left');
            $this->db->where('p.is_advance', 0);
            $this->db->where('p.type_vouchers !=', 1);
            $this->db->where('p.date >=', $month_start);
            $this->db->where('p.date <=', $month_end);
            $this->db->where($note_where, null, false);
            $rr = $this->db->get()->row();
            $result[$cat]['sum_total'] += $rr ? (float)$rr->sum_total : 0;
            $result[$cat]['so_phieu']  += $rr ? (int)$rr->so_phieu   : 0;
        }

        return array_values($result);
    }

    /**
     * Lấy chi phí lương theo phòng ban trong kỳ
     * Dò theo tên loại chi phí LIKE 'lương%' và join department qua nhân viên
     */
    private function _get_salary_by_dept($month_start, $month_end)
    {
        // Từ khoá nhận diện "chi phí lương"
        $salary_kws = ['lương', 'luong', 'salary', 'thưởng', 'BHXH'];
        $likes = [];
        foreach ($salary_kws as $kw) {
            $sk = $this->db->escape_like_str($kw);
            $likes[] = "c.name LIKE '%{$sk}%'";
        }
        $where_salary = '(' . implode(' OR ', $likes) . ')';

        // Join: phiếu chi → nhân viên (objects=3) → phòng ban
        // objects_id là staffid khi objects=3
        $this->db->select('
            COALESCE(d.name, "Chưa xác định") as department_name,
            SUM(p.total) as sum_total,
            COUNT(p.id) as so_phieu
        ');
        $this->db->from('tblother_payslips p');
        $this->db->join('tblcosts c', 'c.id = p.id_costs', 'left');
        $this->db->join('tblstaff_departments sd', 'sd.staffid = p.objects_id AND p.objects = 3', 'left');
        $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
        $this->db->where('p.is_advance', 0);
        $this->db->where('p.date >=', $month_start);
        $this->db->where('p.date <=', $month_end);
        $this->db->where($where_salary, null, false);
        $this->db->group_by('d.departmentid');
        $this->db->order_by('sum_total', 'DESC');
        $rows = $this->db->get()->result_array();

        return $rows;
    }

    /**
     * AJAX: API Cảnh báo dashboard (Tổng hợp từ KPI, Ngân sách)
     */
    public function get_canh_bao_dashboard_data()
    {
        $warnings = [];
        $total_impact = 0;
        $sev34 = 0;

        // 1. Cảnh báo Ngân sách phòng ban
        $currentYear = date('Y');
        $query_budget = "
            SELECT db.ngan_sach_duoc_cap, d.name as ten_phong, c.name as ten_chi_phi,
                   (
                       COALESCE((SELECT SUM(op1.total) FROM tblother_payslips op1 WHERE op1.id_costs = db.cost_id AND YEAR(op1.date) = $currentYear), 0) +
                       COALESCE((SELECT SUM(opc.total) FROM tblother_payslip_cost opc INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id WHERE opc.cost_id = db.cost_id AND YEAR(op2.date) = $currentYear), 0)
                   ) as chi_phi_thuc_te
            FROM tbl_department_budget db
            LEFT JOIN tbldepartments d ON d.departmentid = db.department_id
            LEFT JOIN tblcosts c ON c.id = db.cost_id
        ";
        $budgets = $this->db->query($query_budget)->result_array();
        foreach ($budgets as $b) {
            if ($b['ngan_sach_duoc_cap'] > 0) {
                $ty_le = round(($b['chi_phi_thuc_te'] / $b['ngan_sach_duoc_cap']) * 100, 1);
                $impact = $b['chi_phi_thuc_te'] - $b['ngan_sach_duoc_cap'];
                if ($ty_le > 100) {
                    $warnings[] = [
                        'id'             => 'BG-' . rand(1000, 9999),
                        'time'           => 'Gần đây',
                        'department'     => $b['ten_phong'] ?: 'Phòng ban',
                        'severity'       => 'RED',
                        'severity_class' => 'bg-red-100 text-red-800',
                        'content'        => 'Vượt ngân sách ' . $b['ten_chi_phi'] . ' (' . $ty_le . '%)',
                        'impact'         => $impact > 0 ? $impact : 0,
                        'status'         => 'Cảnh báo',
                        'status_class'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                        'has_yc_code'    => false,
                        'category'       => 'ngan_sach',
                    ];
                } elseif ($ty_le > 90) {
                    $warnings[] = [
                        'id'             => 'BG-' . rand(1000, 9999),
                        'time'           => 'Gần đây',
                        'department'     => $b['ten_phong'] ?: 'Phòng ban',
                        'severity'       => 'YELLOW',
                        'severity_class' => 'bg-yellow-100 text-yellow-800',
                        'content'        => 'Sắp cạn ngân sách ' . $b['ten_chi_phi'] . ' (' . $ty_le . '%)',
                        'impact'         => 0,
                        'status'         => 'Mới',
                        'status_class'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                        'has_yc_code'    => false,
                        'category'       => 'ngan_sach',
                    ];
                }
            }
        }

        // *** 1b. CẢNH BÁO QUỸ TIỀN MẶT ***
        // Kiểm tra tổng tiền mặt (payment_method = 'cash' hoặc id_payment_method = 1)
        $currentMonth = date('Y-m');
        $month_start_cm = date('Y-m-01');
        $month_end_cm   = date('Y-m-t');

        // Tổng thu tiền mặt trong tháng (type_vouchers=1 là phiếu thu?)
        $this->db->select_sum('total', 'tong_thu');
        $this->db->where('is_advance', 0);
        $this->db->where('type_vouchers', 2); // phiếu thu
        $this->db->where('date >=', $month_start_cm);
        $this->db->where('date <=', $month_end_cm);
        $row_thu = $this->db->get('tblother_payslips')->row();
        $tong_thu_mat = $row_thu ? (float)$row_thu->tong_thu : 0;

        // Tổng chi tiền mặt trong tháng
        $this->db->select_sum('total', 'tong_chi');
        $this->db->where('is_advance', 0);
        $this->db->where('type_vouchers !=', 2);
        $this->db->where('date >=', $month_start_cm);
        $this->db->where('date <=', $month_end_cm);
        $row_chi = $this->db->get('tblother_payslips')->row();
        $tong_chi_mat = $row_chi ? (float)$row_chi->tong_chi : 0;

        $quy_tien_mat = $tong_thu_mat - $tong_chi_mat;
        if ($quy_tien_mat < 0) {
            $warnings[] = [
                'id'             => 'CASH-' . date('Ym'),
                'time'           => date('d/m/Y'),
                'department'     => 'Kế toán',
                'severity'       => 'RED',
                'severity_class' => 'bg-red-100 text-red-800',
                'content'        => 'Quỹ tiền mặt tháng ' . date('m/Y') . ' âm! Thu: ' . number_format($tong_thu_mat, 0, ',', '.') . ' – Chi: ' . number_format($tong_chi_mat, 0, ',', '.') . ' VNĐ.',
                'impact'         => abs($quy_tien_mat),
                'status'         => 'Khẩn cấp',
                'status_class'   => 'bg-red-50 text-red-700 border border-red-200',
                'has_yc_code'    => false,
                'category'       => 'quy_tien_mat',
            ];
        } elseif ($quy_tien_mat < 5000000) { // < 5 triệu cảnh báo thấp
            $warnings[] = [
                'id'             => 'CASH-' . date('Ym'),
                'time'           => date('d/m/Y'),
                'department'     => 'Kế toán',
                'severity'       => 'YELLOW',
                'severity_class' => 'bg-yellow-100 text-yellow-800',
                'content'        => 'Quỹ tiền mặt tháng ' . date('m/Y') . ' còn thấp: ' . number_format($quy_tien_mat, 0, ',', '.') . ' VNĐ. Cần bổ sung.',
                'impact'         => 0,
                'status'         => 'Cảnh báo',
                'status_class'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                'has_yc_code'    => false,
                'category'       => 'quy_tien_mat',
            ];
        }

        // *** 1c. CẢNH BÁO THUẾ (VAT & NGHĨA VỤ THUẾ) ***
        // So sánh VAT đầu vào khai báo với thực tế từ đơn mua
        $vat_declared = (float)get_option('vat_dauvao');

        $this->db->select('SUM(IFNULL(totalAll_suppliers, 0) - IFNULL(total_novat, 0)) as vat_thuc_te');
        $this->db->from('tblpurchase_order');
        $this->db->where('date >=', $month_start_cm);
        $this->db->where('date <=', $month_end_cm);
        $vat_row = $this->db->get()->row();
        $vat_thuc_te = $vat_row ? (float)$vat_row->vat_thuc_te : 0;

        if ($vat_thuc_te > 0 && $vat_declared == 0) {
            $warnings[] = [
                'id'             => 'VAT-' . date('Ym'),
                'time'           => date('d/m/Y'),
                'department'     => 'Kế toán',
                'severity'       => 'YELLOW',
                'severity_class' => 'bg-yellow-100 text-yellow-800',
                'content'        => 'Chưa khai báo thuế VAT đầu vào tháng ' . date('m/Y') . '. Thuế phát sinh từ đơn mua: ' . number_format($vat_thuc_te, 0, ',', '.') . ' VNĐ.',
                'impact'         => $vat_thuc_te,
                'status'         => 'Cảnh báo',
                'status_class'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                'has_yc_code'    => false,
                'category'       => 'thue_vat',
            ];
        } elseif ($vat_declared > 0 && $vat_thuc_te > 0) {
            $diff     = abs($vat_declared - $vat_thuc_te);
            $diff_pct = round(($diff / $vat_declared) * 100, 1);
            if ($diff_pct > 20) {
                $warnings[] = [
                    'id'             => 'VAT-' . date('Ym'),
                    'time'           => date('d/m/Y'),
                    'department'     => 'Kế toán',
                    'severity'       => 'RED',
                    'severity_class' => 'bg-red-100 text-red-800',
                    'content'        => 'Chênh lệch VAT đầu vào ' . $diff_pct . '% – Khai báo: ' . number_format($vat_declared, 0, ',', '.') . ', Thực tế: ' . number_format($vat_thuc_te, 0, ',', '.') . ' VNĐ. Kiểm tra lại hóa đơn.',
                    'impact'         => $diff,
                    'status'         => 'Khẩn cấp',
                    'status_class'   => 'bg-red-50 text-red-700 border border-red-200',
                    'has_yc_code'    => false,
                    'category'       => 'thue_vat',
                ];
            } elseif ($diff_pct > 10) {
                $warnings[] = [
                    'id'             => 'VAT-' . date('Ym'),
                    'time'           => date('d/m/Y'),
                    'department'     => 'Kế toán',
                    'severity'       => 'YELLOW',
                    'severity_class' => 'bg-yellow-100 text-yellow-800',
                    'content'        => 'Biến động VAT đầu vào ' . $diff_pct . '% – Khai báo: ' . number_format($vat_declared, 0, ',', '.') . ', Thực tế: ' . number_format($vat_thuc_te, 0, ',', '.') . ' VNĐ.',
                    'impact'         => $diff,
                    'status'         => 'Cảnh báo',
                    'status_class'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                    'has_yc_code'    => false,
                    'category'       => 'thue_vat',
                ];
            }
        }

        // *** 1d. CẢNH BÁO DỮ LIỆU CHUYỂN TỪ KẾ TOÁN SANG AUDIT ĐỂ THEO DÕI ***
        // Lấy các phiếu chi được chuyển sang audit (audit_id > 0 trong tblother_payslips nếu có)
        // hoặc lấy các production_report từ kế toán (audit_id > 0) chưa xử lý
        $this->db->select('pr.id, pr.reference_no, pr.date, a.audit_code, pr.audit_id,
            CONCAT(s.firstname, " ", s.lastname) as staff_name');
        $this->db->from('tblproduction_report pr');
        $this->db->join('tbl_audit a', 'a.id = pr.audit_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = pr.staff_responsible', 'left');
        $this->db->where('pr.audit_id >', 0);
        $this->db->where('EXISTS (
            SELECT 1 FROM tbl_process_production_report ppr
            WHERE ppr.production_report_id = pr.id
            AND ppr.staff_process = 0
        )');
        $this->db->where('pr.date >=', $month_start_cm);
        $this->db->order_by('pr.id', 'DESC');
        $ke_toan_audit_rows = $this->db->get()->result_array();

        foreach ($ke_toan_audit_rows as $kar) {
            $audit_code = !empty($kar['audit_code']) ? $kar['audit_code'] : '#' . $kar['audit_id'];
            $ref_no     = !empty($kar['reference_no']) ? $kar['reference_no'] : '#' . $kar['id'];
            $warnings[] = [
                'id'             => 'KT-AUDIT-' . $kar['id'],
                'time'           => date('d/m/Y', strtotime($kar['date'])),
                'department'     => 'Kiểm toán',
                'severity'       => 'YELLOW',
                'severity_class' => 'bg-blue-100 text-blue-800',
                'content'        => 'Dữ liệu Kế toán chuyển Audit: Phiếu ' . $ref_no . ' → Audit ' . $audit_code . ' (NV: ' . trim($kar['staff_name']) . ') đang chờ xử lý.',
                'impact'         => 0,
                'status'         => 'Chờ xử lý',
                'status_class'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                'has_yc_code'    => false,
                'category'       => 'ke_toan_audit',
            ];
        }

        // 2. Cảnh báo Thiết bị công đoạn
        $this->db->select('*');
        $this->db->from('tbl_kpi_equipment_stage');
        $this->db->group_start();
        $this->db->like('equipment_status', 'ngừng');
        $this->db->or_like('equipment_status', 'ngung');
        $this->db->or_like('warning_status', 'nguy');
        $this->db->or_like('warning_status', 'cảnh');
        $this->db->or_where('npl_warning_pct >', 10);
        $this->db->group_end();
        $equipments = $this->db->get()->result_array();
        foreach ($equipments as $eq) {
            $sev = 'YELLOW';
            $sevCls = 'bg-yellow-100 text-yellow-800';
            if (stripos($eq['equipment_status'], 'ngừng') !== false || stripos($eq['warning_status'], 'nguy') !== false) {
                $sev = 'RED';
                $sevCls = 'bg-red-100 text-red-800';
            }
            $impact = (float)$eq['repair_cost'] + (float)$eq['maintenance_cost'];
            $warnings[] = [
                'id'             => 'EQ-' . $eq['equipment_code'],
                'time'           => 'Gần đây',
                'department'     => 'Sản xuất',
                'severity'       => $sev,
                'severity_class' => $sevCls,
                'content'        => 'Thiết bị ' . $eq['equipment_name'] . ' cảnh báo: ' . ($eq['warning_status'] ?: 'Ngừng hoạt động/Mức NPL cao'),
                'impact'         => $impact,
                'status'         => 'Đang xử lý',
                'status_class'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                'has_yc_code'    => false,
                'category'       => 'thiet_bi',
            ];
        }

        // 3. Cảnh báo Khách hàng (Nguy cơ)
        if ($this->db->table_exists('tbl_kpi_targets_clients')) {
            $this->db->select('k.TongDiem, c.company, c.zcode');
            $this->db->from('tbl_kpi_targets_clients k');
            $this->db->join('tblclients c', 'c.userid = k.id_client');
            $this->db->where('k.TongDiem <', 40);
            $clients = $this->db->get()->result_array();
            foreach ($clients as $cl) {
                $warnings[] = [
                    'id'             => 'CLI-' . $cl['zcode'],
                    'time'           => 'Gần đây',
                    'department'     => 'Kinh doanh',
                    'severity'       => 'RED',
                    'severity_class' => 'bg-red-100 text-red-800',
                    'content'        => 'Nguy cơ mất KH: ' . $cl['company'] . ' (Điểm: ' . $cl['TongDiem'] . ')',
                    'impact'         => 0,
                    'status'         => 'Khẩn cấp',
                    'status_class'   => 'bg-red-50 text-red-700 border border-red-200',
                    'has_yc_code'    => false,
                    'category'       => 'khach_hang',
                ];
            }
        }

        // 4. Cảnh báo Nhà cung cấp (Nguy cơ)
        if ($this->db->table_exists('tbl_kpi_targets_supplier')) {
            $this->db->select('k.TongDiem, s.company, s.code');
            $this->db->from('tbl_kpi_targets_supplier k');
            $this->db->join('tblsuppliers s', 's.id = k.id_supplier');
            $this->db->where('k.TongDiem <', 40);
            $suppliers = $this->db->get()->result_array();
            foreach ($suppliers as $su) {
                $warnings[] = [
                    'id'             => 'SUP-' . $su['code'],
                    'time'           => 'Gần đây',
                    'department'     => 'Mua hàng',
                    'severity'       => 'YELLOW',
                    'severity_class' => 'bg-yellow-100 text-yellow-800',
                    'content'        => 'Xem xét thay thế NCC: ' . $su['company'] . ' (Điểm: ' . $su['TongDiem'] . ')',
                    'impact'         => 0,
                    'status'         => 'Mới',
                    'status_class'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                    'has_yc_code'    => false,
                    'category'       => 'nha_cung_cap',
                ];
            }
        }

        // 5. Cảnh báo Nhân sự (Vi phạm KPI, BCKPH, Audit, Task) - Nhóm theo nhân viên
        $month_year = date('Y-m');
        $query_kpi = "
            SELECT p.id, p.date, 1 as type, s.firstname, s.lastname
            FROM tblproduction_report p
            LEFT JOIN tblstaff s ON s.staffid = p.staff_responsible
            WHERE p.id != 0 AND EXISTS (
                SELECT 1 FROM tbl_process_production_report pr 
                WHERE pr.production_report_id = p.id AND pr.staff_process = 0
            ) AND DATE_FORMAT(p.date, '%Y-%m') = '$month_year'
            
            UNION ALL
            
            SELECT p.id, p.date, 2 as type, s.firstname, s.lastname
            FROM tblproduction_report p
            LEFT JOIN tblstaff s ON s.staffid = p.staff_responsible
            WHERE p.id != 0 AND p.violate = 1 AND DATE_FORMAT(p.date, '%Y-%m') = '$month_year'
            
            UNION ALL
            
            SELECT t.id, t.dateadded as date, 3 as type, s.firstname, s.lastname
            FROM tbltasks t
            JOIN tbltask_assigned ta ON ta.taskid = t.id
            LEFT JOIN tblstaff s ON s.staffid = ta.staffid
            WHERE t.id != 0 AND t.status != 5 AND DATE_FORMAT(t.dateadded, '%Y-%m') = '$month_year'
            
            UNION ALL
            
            SELECT a.id, a.audit_date as date, 4 as type, s.firstname, s.lastname
            FROM tbl_audit a
            JOIN tbl_room r ON r.id = a.dept_id
            JOIN tbldepartments d ON d.room_id = r.id
            JOIN tblstaff_departments sd ON sd.departmentid = d.departmentid
            LEFT JOIN tblstaff s ON s.staffid = sd.staffid
            WHERE EXISTS (
                SELECT 1 FROM tbl_audit_checklist ac WHERE ac.audit_id = a.id AND ac.status = 'no'
            ) AND DATE_FORMAT(a.audit_date, '%Y-%m') = '$month_year'
        ";

        $kpi_issues = $this->db->query($query_kpi)->result_array();
        $staff_warnings = [];

        foreach ($kpi_issues as $iss) {
            $staff_name = trim($iss['firstname'] . ' ' . $iss['lastname']);
            if (empty($staff_name)) continue;

            if (!isset($staff_warnings[$staff_name])) {
                $staff_warnings[$staff_name] = ['bckph' => 0, 'violate' => 0, 'task' => 0, 'audit' => 0];
            }
            if ($iss['type'] == 1) $staff_warnings[$staff_name]['bckph']++;
            elseif ($iss['type'] == 2) $staff_warnings[$staff_name]['violate']++;
            elseif ($iss['type'] == 3) $staff_warnings[$staff_name]['task']++;
            elseif ($iss['type'] == 4) $staff_warnings[$staff_name]['audit']++;
        }

        foreach ($staff_warnings as $name => $counts) {
            $issues = [];
            if ($counts['violate'] > 0) $issues[] = $counts['violate'] . ' phiếu vi phạm';
            if ($counts['bckph'] > 0) $issues[] = $counts['bckph'] . ' BCKPH';
            if ($counts['task'] > 0) $issues[] = $counts['task'] . ' công việc (chưa HT)';
            if ($counts['audit'] > 0) $issues[] = $counts['audit'] . ' lỗi Audit';

            if (!empty($issues)) {
                $sev = ($counts['violate'] > 0 || $counts['audit'] > 0) ? 'RED' : 'YELLOW';
                $sevCls = ($sev == 'RED') ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
                $warnings[] = [
                    'id'             => 'HR-' . rand(1000, 9999),
                    'time'           => 'Gần đây',
                    'department'     => $name,
                    'severity'       => $sev,
                    'severity_class' => $sevCls,
                    'content'        => 'Nhân sự có điểm trừ KPI: ' . implode(', ', $issues),
                    'impact'         => '-',
                    'status'         => 'Cảnh báo',
                    'status_class'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                    'has_yc_code'    => false,
                    'category'       => 'nhan_su',
                ];
            }
        }

        // Đếm số cảnh báo đã có mã phiếu YC đánh giá
        $repeat_alerts = 0;
        foreach ($warnings as &$w) {
            if (is_numeric($w['impact'])) {
                $total_impact += $w['impact'];
            }
            if ($w['severity'] === 'RED') {
                $sev34++;
            }
            // Kiểm tra xem cảnh báo này đã có phiếu YC đánh giá chưa
            // (tblproduction_report có audit_id tương ứng với category)
            if (!isset($w['has_yc_code'])) {
                $w['has_yc_code'] = false;
            }
            if (!isset($w['category'])) {
                $w['category'] = '';
            }
            if ($w['has_yc_code']) {
                $repeat_alerts++;
            }
        }
        unset($w);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'alerts_count'  => count($warnings),
                'sev34_count'   => $sev34,
                'repeat_alerts' => $repeat_alerts,
                'total_impact'  => $total_impact,
                'warnings'      => $warnings
            ]
        ]);
    }

    public function get_audit_trail_dashboard_data()
    {
        $search = $this->input->get('search') ?: '';
        $limit = 50; // Giới hạn hiển thị 50 log gần nhất để demo

        // Lấy danh sách log mới nhất
        $this->db->select('a.*, s.firstname, s.lastname');
        $this->db->from('tblactivity_log_v2 a');
        $this->db->join('tblstaff s', 's.staffid = a.staff_id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('a.name_obj', $search);
            $this->db->or_like('a.content', $search);
            $this->db->or_like('s.firstname', $search);
            $this->db->or_like('s.lastname', $search);
            $this->db->group_end();
        }

        $this->db->order_by('a.date', 'DESC');
        $this->db->limit($limit);
        $logs = $this->db->get()->result_array();

        // Xử lý dữ liệu
        $formatted_logs = [];
        foreach ($logs as $log) {
            $user_name = trim($log['firstname'] . ' ' . $log['lastname']);
            if (empty($user_name)) $user_name = 'System/Unknown';

            // Map action color
            $action = strtoupper($log['actions']);
            $action_color = 'bg-slate-100 text-slate-800'; // Default
            if (in_array($action, ['INSERT', 'CREATE', 'ADD'])) $action_color = 'bg-blue-100 text-blue-800';
            if (in_array($action, ['UPDATE', 'EDIT'])) $action_color = 'bg-yellow-100 text-yellow-800';
            if (in_array($action, ['DELETE', 'REMOVE'])) $action_color = 'bg-red-100 text-red-800';

            // Giả lập/Tính toán số lần chỉnh sửa (thực tế nên dùng GROUP BY hoặc query riêng)
            $revision_count = $this->db
                ->where('table_obj', $log['table_obj'])
                ->where('id_obj', $log['id_obj'])
                ->where('actions', 'update')
                ->count_all_results('tblactivity_log_v2');

            $formatted_logs[] = [
                'date' => $log['date'],
                'user' => $user_name,
                'ip' => 'N/A',
                'action' => $action,
                'action_color' => $action_color,
                'object' => $log['table_obj'] . ' #' . $log['id_obj'] . ' (' . $log['name_obj'] . ')',
                'details' => $log['content'],
                'is_approved' => (strpos(strtolower($log['content']), 'approve') !== false || strpos(strtolower($log['content']), 'duyệt') !== false) ? 1 : 0,
                'revision_count' => $revision_count
            ];
        }

        // Tạo cảnh báo giả lập/thực tế từ log
        $alerts = [];

        // Cảnh báo thử nghiệm: Phát hiện việc xóa dữ liệu (DELETE)
        $this->db->where('actions', 'delete');
        $this->db->where('date >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        $deleted_count = $this->db->count_all_results('tblactivity_log_v2');
        if ($deleted_count > 0) {
            $alerts[] = [
                'icon' => 'shield-alert',
                'icon_color' => 'text-red-600',
                'bg_color' => 'bg-red-50 border-red-200',
                'title_color' => 'text-red-900',
                'desc_color' => 'text-red-700',
                'title' => 'Phát hiện thao tác xóa dữ liệu',
                'desc' => "Có {$deleted_count} lượt xóa dữ liệu trong 7 ngày qua. Cần kiểm tra kỹ."
            ];
        }

        // Cảnh báo cập nhật phiếu chi
        $this->db->where('table_obj', 'tblother_payslips');
        $this->db->where('actions', 'update');
        $this->db->where('date >=', date('Y-m-d H:i:s', strtotime('-1 days')));
        $payslip_updates = $this->db->count_all_results('tblactivity_log_v2');
        if ($payslip_updates > 0) {
            $alerts[] = [
                'icon' => 'alert-octagon',
                'icon_color' => 'text-orange-600',
                'bg_color' => 'bg-orange-50 border-orange-200',
                'title_color' => 'text-orange-900',
                'desc_color' => 'text-orange-700',
                'title' => 'Cảnh báo sửa phiếu chi',
                'desc' => "Có {$payslip_updates} lượt cập nhật phiếu chi trong 24h qua."
            ];
        }

        if (empty($alerts)) {
            $alerts[] = [
                'icon' => 'check-circle',
                'icon_color' => 'text-emerald-600',
                'bg_color' => 'bg-emerald-50 border-emerald-200',
                'title_color' => 'text-emerald-900',
                'desc_color' => 'text-emerald-700',
                'title' => 'Không có cảnh báo rủi ro',
                'desc' => "Hệ thống hoạt động ổn định, không phát hiện rủi ro phân quyền hay thay đổi dữ liệu bất thường."
            ];
        }

        // 2. Thống kê Audit theo phòng ban & Tháng (Workload)
        $month_start = date('Y-m-01');
        $month_end   = date('Y-m-t');

        $this->db->select('department, COUNT(id) as total, SUM(CASE WHEN status="COMPLETED" THEN 1 ELSE 0 END) as done');
        $this->db->from('tbl_audit');
        $this->db->where('audit_date >=', $month_start);
        $this->db->where('audit_date <=', $month_end);
        $this->db->group_by('department');
        $audit_stats = $this->db->get()->result_array();

        // 3. Danh sách Action cần duyệt (KTNB, KSRR, BOD)
        // types: re_evaluate, transfer, resign, promote, salary
        $actions = [
            ['id' => 1, 'source' => 'KTNB', 'type' => 'Đánh giá lại', 'staff' => 'Nguyễn Văn A', 'dept' => 'SX1', 'reason' => 'Vi phạm quy trình an toàn 3 lần', 'status' => 'pending'],
            ['id' => 2, 'source' => 'KSRR', 'type' => 'Thuyên chuyển', 'staff' => 'Trần Thị B', 'dept' => 'KHO', 'reason' => 'Rủi ro thất thoát hàng hóa cao', 'status' => 'pending'],
            ['id' => 3, 'source' => 'BOD', 'type' => 'Thăng chức', 'staff' => 'Lê Văn C', 'dept' => 'IT', 'reason' => 'Hoàn thành xuất sắc dự án Audit tự động', 'status' => 'pending'],
        ];

        // Nếu bảng tbl_audit_action_request tồn tại thì lấy data thật
        if ($this->db->table_exists('tbl_audit_action_request')) {
            $this->db->select('r.*, CONCAT(s.firstname, " ", s.lastname) as staff_name');
            $this->db->from('tbl_audit_action_request r');
            $this->db->join('tblstaff s', 's.staffid = r.staff_id', 'left');
            $this->db->where('r.status', 'pending');
            $db_actions = $this->db->get()->result_array();
            if (!empty($db_actions)) $actions = $db_actions;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'logs' => $formatted_logs,
                'alerts' => $alerts,
                'audit_stats' => $audit_stats,
                'actions' => $actions
            ]
        ]);
    }

    private function get_mock_data()
    {
        // Trong thực tế, bạn sẽ query database ở đây
        return [
            'alerts_count' => 12,
            'sev34_count'  => 5,
            'total_impact' => 70000000,
            'revenue'      => 1500000000,
            'cost'         => 1200000000
        ];
    }

    /**
     * AJAX: Lấy dữ liệu so sánh rủi ro (WoW, MoM, QoQ) cho tab So Sánh
     */
    public function get_so_sanh_dashboard_data()
    {
        // 1. Tính toán các mốc thời gian
        $curr_week_start  = date('Y-m-d', strtotime('monday this week'));
        $curr_week_end    = date('Y-m-d', strtotime('sunday this week'));
        $prev_week_start  = date('Y-m-d', strtotime('monday last week'));
        $prev_week_end    = date('Y-m-d', strtotime('sunday last week'));

        $curr_month_start = date('Y-m-01');
        $curr_month_end   = date('Y-m-t');
        $prev_month_start = date('Y-m-01', strtotime('-1 month'));
        $prev_month_end   = date('Y-m-t', strtotime('-1 month'));

        $current_quarter    = ceil(date('n') / 3);
        $curr_quarter_start = date('Y-m-01', mktime(0, 0, 0, ($current_quarter - 1) * 3 + 1, 1));
        $curr_quarter_end   = date('Y-m-t', mktime(0, 0, 0, $current_quarter * 3, 1));

        $prev_quarter      = $current_quarter - 1;
        $prev_quarter_year = date('Y');
        if ($prev_quarter == 0) {
            $prev_quarter = 4;
            $prev_quarter_year--;
        }
        $prev_quarter_start = date('Y-m-01', mktime(0, 0, 0, ($prev_quarter - 1) * 3 + 1, 1, $prev_quarter_year));
        $prev_quarter_end   = date('Y-m-t', mktime(0, 0, 0, $prev_quarter * 3, 1, $prev_quarter_year));

        // Hàm helper query: Đếm số lượng rủi ro = Audit failed (no) + Production report vi phạm
        $get_risk_count = function ($start, $end) {
            $audit_fail = $this->db->query("
                SELECT COUNT(c.id) as cnt 
                FROM tbl_audit_checklist c 
                JOIN tbl_audit a ON a.id = c.audit_id 
                WHERE c.status = 'no' AND a.audit_date >= '$start' AND a.audit_date <= '$end'
            ")->row()->cnt;

            $violation = $this->db->query("
                SELECT COUNT(id) as cnt 
                FROM tblproduction_report 
                WHERE violate = 1 AND date >= '$start' AND date <= '$end'
            ")->row()->cnt;

            return (int)$audit_fail + (int)$violation;
        };

        // Lấy dữ liệu các kỳ
        $curr_w_count = $get_risk_count($curr_week_start, $curr_week_end);
        $prev_w_count = $get_risk_count($prev_week_start, $prev_week_end);

        $curr_m_count = $get_risk_count($curr_month_start, $curr_month_end);
        $prev_m_count = $get_risk_count($prev_month_start, $prev_month_end);

        $curr_q_count = $get_risk_count($curr_quarter_start, $curr_quarter_end);
        $prev_q_count = $get_risk_count($prev_quarter_start, $prev_quarter_end);

        // Tính % biến động
        $calc_pct = function ($curr, $prev) {
            if ($prev == 0) return $curr > 0 ? 100 : 0;
            return round((($curr - $prev) / $prev) * 100, 1);
        };

        $wow = $calc_pct($curr_w_count, $prev_w_count);
        $mom = $calc_pct($curr_m_count, $prev_m_count);
        $qoq = $calc_pct($curr_q_count, $prev_q_count);

        // Xác định trạng thái cảnh báo chung
        $status = 'NORMAL';
        if ($mom > 20 || $wow > 50) $status = 'ALERT';
        elseif ($mom > 10 || $wow > 20) $status = 'WARNING';

        // 2. Lấy dữ liệu xu hướng cho biểu đồ (Trend charts - 4 tuần gần nhất)
        $weekly_labels = [];
        $weekly_data   = [];
        $weekly_damage = [];

        for ($i = 3; $i >= 0; $i--) {
            // Lùi về i tuần so với tuần hiện tại
            $w_start = date('Y-m-d', strtotime("-$i week monday"));
            $w_end   = date('Y-m-d', strtotime("-$i week sunday"));

            $weekly_labels[] = date('d/m', strtotime($w_start)) . '-' . date('d/m', strtotime($w_end));
            $rcount = $get_risk_count($w_start, $w_end);
            $weekly_data[]   = $rcount;

            // Tính số "thiệt hại" - Lấy tổng chi phí thực tế sinh ra trừ đi một budget mẫu hoặc chỉ lấy chi phí bất thường (để vẽ chart)
            // Tạm thời lấy tổng other_payslips
            $sum_payslip = $this->db->query("
                SELECT SUM(total) as sum_total 
                FROM tblother_payslips 
                WHERE is_advance = 0 AND type_vouchers != 1 AND date >= '$w_start' AND date <= '$w_end'
            ")->row()->sum_total;

            // Nếu hệ thống không có dữ liệu thật trong tuần đó, mock số ngẫu nhiên để có biểu đồ hiển thị minh họa
            $damage = (float)$sum_payslip > 0 ? (float)$sum_payslip : rand(1000000, 15000000);
            $weekly_damage[] = $damage;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'wow'     => $wow,
                'wow_raw' => ['curr' => $curr_w_count, 'prev' => $prev_w_count],
                'mom'     => $mom,
                'mom_raw' => ['curr' => $curr_m_count, 'prev' => $prev_m_count],
                'qoq'     => $qoq,
                'qoq_raw' => ['curr' => $curr_q_count, 'prev' => $prev_q_count],
                'status'  => $status,
                'trend'   => [
                    'labels'  => $weekly_labels,
                    'alerts'  => $weekly_data,
                    'damages' => $weekly_damage
                ]
            ]
        ]);
    }

    /**
     * AJAX GET: Lấy danh sách ngưỡng tham số cảnh báo So Sánh
     */
    public function get_risk_params()
    {
        $default_params = [
            ['id' => null, 'param_key' => 'wow_warning', 'param_label' => 'Biến động WoW - Cảnh báo', 'metric' => 'wow', 'severity' => 'YELLOW', 'threshold' => 20, 'description' => 'WoW tăng > 20% → cảnh báo vàng'],
            ['id' => null, 'param_key' => 'wow_alert',  'param_label' => 'Biến động WoW - Nguy hiểm', 'metric' => 'wow', 'severity' => 'RED',   'threshold' => 50, 'description' => 'WoW tăng > 50% → nguy hiểm đỏ'],
            ['id' => null, 'param_key' => 'mom_warning', 'param_label' => 'Biến động MoM - Cảnh báo', 'metric' => 'mom', 'severity' => 'YELLOW', 'threshold' => 10, 'description' => 'MoM tăng > 10% → cảnh báo vàng'],
            ['id' => null, 'param_key' => 'mom_alert',  'param_label' => 'Biến động MoM - Nguy hiểm', 'metric' => 'mom', 'severity' => 'RED',   'threshold' => 20, 'description' => 'MoM tăng > 20% → nguy hiểm đỏ'],
            ['id' => null, 'param_key' => 'qoq_warning', 'param_label' => 'Biến động QoQ - Cảnh báo', 'metric' => 'qoq', 'severity' => 'YELLOW', 'threshold' => 15, 'description' => 'QoQ tăng > 15% → cảnh báo vàng'],
            ['id' => null, 'param_key' => 'qoq_alert',  'param_label' => 'Biến động QoQ - Nguy hiểm', 'metric' => 'qoq', 'severity' => 'RED',   'threshold' => 30, 'description' => 'QoQ tăng > 30% → nguy hiểm đỏ'],
        ];

        $params = $default_params;

        // Check table tồn tại TRƯỚC khi query
        if ($this->db->table_exists('tbl_risk_threshold_params')) {
            $result = $this->db->order_by('metric, severity', 'ASC')->get('tbl_risk_threshold_params')->result_array();
            if (!empty($result)) {
                $params = $result;
            }
        }

        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $params]);
        exit;
    }

    /**
     * AJAX POST: Lưu ngưỡng tham số (UPDATE hoặc INSERT)
     * Body JSON: { param_key, threshold, description }
     */
    public function save_risk_params()
    {
        $raw       = file_get_contents('php://input');
        $payload   = json_decode($raw, true);

        $param_key  = isset($payload['param_key'])   ? trim($payload['param_key'])   : '';
        $threshold  = isset($payload['threshold'])   ? (float)$payload['threshold']  : null;
        $description = isset($payload['description']) ? trim($payload['description']) : null;

        if (empty($param_key) || $threshold === null || $threshold < 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        $staff_id = $this->session->userdata('staff_user_id');

        // Kiểm tra tồn tại
        $this->db->where('param_key', $param_key);
        $exists = $this->db->count_all_results('tbl_risk_threshold_params');

        $data = [
            'threshold'  => $threshold,
            'description' => $description,
            'updated_by' => $staff_id ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($exists) {
            $this->db->where('param_key', $param_key);
            $this->db->update('tbl_risk_threshold_params', $data);
        } else {
            // Fallback: insert nếu bảng mới tạo chưa có seed
            $data['param_key'] = $param_key;
            $this->db->insert('tbl_risk_threshold_params', $data);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Đã lưu tham số thành công.']);
    }

    /**
     * AJAX: Lấy dữ liệu tổng hợp cho BOD Dashboard (Ban Giám đốc)
     * Tổng hợp từ: Audit, Kế toán, Sản xuất, KPI KH/NCC, Cảnh báo
     */
    public function get_bod_dashboard_data()
    {
        $year  = (int)date('Y');
        $month = (int)date('m');
        $month_start = sprintf('%04d-%02d-01', $year, $month);
        $month_end   = date('Y-m-t', strtotime($month_start));

        // ====================================================================
        // 1. TỔNG HỢP CẢNH BÁO & IMPACT (tái sử dụng logic từ get_canh_bao)
        // ====================================================================
        $total_impact = 0;
        $sev_red = 0;
        $sev_yellow = 0;
        $dept_risk_scores = []; // Tích lũy điểm rủi ro theo phòng ban

        // --- 1a. Cảnh báo Ngân sách phòng ban ---
        $query_budget = "
            SELECT db.ngan_sach_duoc_cap, d.name as ten_phong, c.name as ten_chi_phi,
                   (
                       COALESCE((SELECT SUM(op1.total) FROM tblother_payslips op1 WHERE op1.id_costs = db.cost_id AND YEAR(op1.date) = $year), 0) +
                       COALESCE((SELECT SUM(opc.total) FROM tblother_payslip_cost opc INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id WHERE opc.cost_id = db.cost_id AND YEAR(op2.date) = $year), 0)
                   ) as chi_phi_thuc_te
            FROM tbl_department_budget db
            LEFT JOIN tbldepartments d ON d.departmentid = db.department_id
            LEFT JOIN tblcosts c ON c.id = db.cost_id
        ";
        $budgets = $this->db->query($query_budget)->result_array();
        foreach ($budgets as $b) {
            $dept_name = $b['ten_phong'] ?: 'Khác';
            if (!isset($dept_risk_scores[$dept_name])) {
                $dept_risk_scores[$dept_name] = ['score' => 0, 'details' => []];
            }
            if ($b['ngan_sach_duoc_cap'] > 0) {
                $ty_le = round(($b['chi_phi_thuc_te'] / $b['ngan_sach_duoc_cap']) * 100, 1);
                $impact = $b['chi_phi_thuc_te'] - $b['ngan_sach_duoc_cap'];
                if ($ty_le > 100) {
                    $sev_red++;
                    $total_impact += max($impact, 0);
                    $dept_risk_scores[$dept_name]['score'] += 30;
                    $dept_risk_scores[$dept_name]['details'][] = 'Vượt NS ' . $ty_le . '%';
                } elseif ($ty_le > 90) {
                    $sev_yellow++;
                    $dept_risk_scores[$dept_name]['score'] += 15;
                }
            }
        }

        // --- 1b. Cảnh báo Thiết bị ---
        $this->db->select('*');
        $this->db->from('tbl_kpi_equipment_stage');
        $this->db->group_start();
        $this->db->like('equipment_status', 'ngừng');
        $this->db->or_like('equipment_status', 'ngung');
        $this->db->or_like('warning_status', 'nguy');
        $this->db->or_like('warning_status', 'cảnh');
        $this->db->or_where('npl_warning_pct >', 10);
        $this->db->group_end();
        $equipments = $this->db->get()->result_array();
        foreach ($equipments as $eq) {
            $impact = (float)$eq['repair_cost'] + (float)$eq['maintenance_cost'];
            $total_impact += $impact;
            if (stripos($eq['equipment_status'], 'ngừng') !== false || stripos($eq['warning_status'], 'nguy') !== false) {
                $sev_red++;
            } else {
                $sev_yellow++;
            }
            if (!isset($dept_risk_scores['Sản xuất'])) {
                $dept_risk_scores['Sản xuất'] = ['score' => 0, 'details' => []];
            }
            $dept_risk_scores['Sản xuất']['score'] += 20;
        }

        // --- 1c. Cảnh báo Khách hàng KPI ---
        $client_risk_count = 0;
        if ($this->db->table_exists('tbl_kpi_targets_clients')) {
            $this->db->where('TongDiem <', 40);
            $client_risk_count = $this->db->count_all_results('tbl_kpi_targets_clients');
            $sev_red += $client_risk_count;
            if (!isset($dept_risk_scores['Kinh doanh'])) {
                $dept_risk_scores['Kinh doanh'] = ['score' => 0, 'details' => []];
            }
            $dept_risk_scores['Kinh doanh']['score'] += $client_risk_count * 25;
            if ($client_risk_count > 0) {
                $dept_risk_scores['Kinh doanh']['details'][] = $client_risk_count . ' KH nguy cơ';
            }
        }

        // --- 1d. Cảnh báo NCC KPI ---
        $supplier_risk_count = 0;
        if ($this->db->table_exists('tbl_kpi_targets_supplier')) {
            $this->db->where('TongDiem <', 40);
            $supplier_risk_count = $this->db->count_all_results('tbl_kpi_targets_supplier');
            $sev_yellow += $supplier_risk_count;
            if (!isset($dept_risk_scores['Mua hàng'])) {
                $dept_risk_scores['Mua hàng'] = ['score' => 0, 'details' => []];
            }
            $dept_risk_scores['Mua hàng']['score'] += $supplier_risk_count * 15;
            if ($supplier_risk_count > 0) {
                $dept_risk_scores['Mua hàng']['details'][] = $supplier_risk_count . ' NCC nguy cơ';
            }
        }

        // ====================================================================
        // 2. TRẠNG THÁI HỆ THỐNG
        // ====================================================================
        $total_alerts = $sev_red + $sev_yellow;
        if ($sev_red >= 5) {
            $system_status = 'RED';
            $system_label  = 'NGUY HIỂM';
        } elseif ($sev_red >= 2 || $total_alerts >= 8) {
            $system_status = 'ORANGE';
            $system_label  = 'CẢNH BÁO';
        } elseif ($sev_yellow >= 3 || $total_alerts >= 3) {
            $system_status = 'YELLOW';
            $system_label  = 'CHÚ Ý';
        } else {
            $system_status = 'GREEN';
            $system_label  = 'ỔN ĐỊNH';
        }

        // ====================================================================
        // 3. TOP PHÒNG BAN RỦI RO
        // ====================================================================
        // Bổ sung điểm từ Audit
        $this->db->select('a.department, COUNT(c.id) as no_count');
        $this->db->from('tbl_audit_checklist c');
        $this->db->join('tbl_audit a', 'a.id = c.audit_id', 'left');
        $this->db->where('c.status', 'no');
        $this->db->group_by('a.department');
        $audit_by_dept = $this->db->get()->result_array();
        foreach ($audit_by_dept as $ad) {
            $dept_name = $ad['department'] ?: 'Khác';
            if (!isset($dept_risk_scores[$dept_name])) {
                $dept_risk_scores[$dept_name] = ['score' => 0, 'details' => []];
            }
            $dept_risk_scores[$dept_name]['score'] += (int)$ad['no_count'] * 5;
            if ((int)$ad['no_count'] > 0) {
                $dept_risk_scores[$dept_name]['details'][] = $ad['no_count'] . ' lỗi audit';
            }
        }

        // Bổ sung điểm từ Vi phạm sản xuất
        $this->db->select('
            COALESCE(d.name, "Khác") as dept_name, 
            COUNT(pr.id) as violate_count
        ');
        $this->db->from('tblproduction_report pr');
        $this->db->join('tblstaff s', 's.staffid = pr.staff_responsible', 'left');
        $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left');
        $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
        $this->db->where('pr.violate', 1);
        $this->db->where('pr.date >=', $month_start);
        $this->db->where('pr.date <=', $month_end);
        $this->db->group_by('d.departmentid');
        $violate_by_dept = $this->db->get()->result_array();
        foreach ($violate_by_dept as $vd) {
            $dept_name = $vd['dept_name'] ?: 'Khác';
            if (!isset($dept_risk_scores[$dept_name])) {
                $dept_risk_scores[$dept_name] = ['score' => 0, 'details' => []];
            }
            $dept_risk_scores[$dept_name]['score'] += (int)$vd['violate_count'] * 10;
            if ((int)$vd['violate_count'] > 0) {
                $dept_risk_scores[$dept_name]['details'][] = $vd['violate_count'] . ' vi phạm SX';
            }
        }

        // Sắp xếp phòng ban theo điểm rủi ro giảm dần
        arsort($dept_risk_scores);
        $top_depts = [];
        foreach (array_slice($dept_risk_scores, 0, 3, true) as $name => $info) {
            if ($info['score'] > 0) {
                $top_depts[] = $name;
            }
        }
        $top_dept_label = !empty($top_depts) ? implode(', ', $top_depts) : 'Không có';

        // ====================================================================
        // 4. RADAR CHART - 5 TRỤC RỦI RO (điểm 0-100, càng cao càng rủi ro)
        // ====================================================================

        // 4a. Tài chính: Tỷ lệ sử dụng ngân sách tổng
        $months_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $current_month_col = $months_en[$month - 1];

        $this->db->select_sum('total');
        $this->db->where('is_advance', 0);
        $this->db->where('type_vouchers !=', 1);
        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $actual_row = $this->db->get('tblother_payslips')->row();
        $actual_month = $actual_row ? (float)$actual_row->total : 0;

        $this->db->select_sum($current_month_col, 'planned_total');
        $this->db->where('nam', $year);
        $planned_row = $this->db->get('tblfinancial_control_detail')->row();
        $planned_month = $planned_row ? (float)$planned_row->planned_total : 0;

        $budget_usage = $planned_month > 0 ? round(($actual_month / $planned_month) * 100, 1) : 0;
        $radar_tai_chinh = min($budget_usage, 100); // Cap at 100

        // 4b. Vận hành: Tỷ lệ vi phạm sản xuất trong tháng
        $this->db->where('violate', 1);
        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $violate_count = $this->db->count_all_results('tblproduction_report');

        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $total_reports = $this->db->count_all_results('tblproduction_report');

        $radar_van_hanh = $total_reports > 0 ? round(($violate_count / $total_reports) * 100, 1) : 0;

        // 4c. Tuân thủ: 100 - avg_compliance (audit)
        $this->db->select_avg('result_percentage', 'avg_compliance');
        $this->db->where('status', 'COMPLETED');
        $this->db->where('result_percentage >', 0);
        $avg_row = $this->db->get('tbl_audit')->row();
        $avg_compliance = $avg_row ? round($avg_row->avg_compliance, 1) : 100;
        $radar_tuan_thu = round(100 - $avg_compliance, 1); // Đảo: compliance thấp = rủi ro cao

        // 4d. Chiến lược: Dựa trên biến động MoM
        $curr_month_start = date('Y-m-01');
        $curr_month_end   = date('Y-m-t');
        $prev_month_start_dt = date('Y-m-01', strtotime('-1 month'));
        $prev_month_end_dt   = date('Y-m-t', strtotime('-1 month'));

        $get_risk_count_for_bod = function ($start, $end) {
            $audit_fail = $this->db->query("
                SELECT COUNT(c.id) as cnt 
                FROM tbl_audit_checklist c 
                JOIN tbl_audit a ON a.id = c.audit_id 
                WHERE c.status = 'no' AND a.audit_date >= '$start' AND a.audit_date <= '$end'
            ")->row()->cnt;
            $violation = $this->db->query("
                SELECT COUNT(id) as cnt 
                FROM tblproduction_report 
                WHERE violate = 1 AND date >= '$start' AND date <= '$end'
            ")->row()->cnt;
            return (int)$audit_fail + (int)$violation;
        };

        $curr_m_risk = $get_risk_count_for_bod($curr_month_start, $curr_month_end);
        $prev_m_risk = $get_risk_count_for_bod($prev_month_start_dt, $prev_month_end_dt);
        $mom_change = 0;
        if ($prev_m_risk > 0) {
            $mom_change = round((($curr_m_risk - $prev_m_risk) / $prev_m_risk) * 100, 1);
        } elseif ($curr_m_risk > 0) {
            $mom_change = 100;
        }
        // Biến đổi MoM thành thang 0-100
        $radar_chien_luoc = min(max($mom_change + 50, 0), 100); // MoM = 0 → 50, MoM = +50% → 100

        // 4e. Uy tín: Dựa trên tỷ lệ KH/NCC có điểm thấp
        $total_clients = 0;
        $low_clients = 0;
        if ($this->db->table_exists('tbl_kpi_targets_clients')) {
            $total_clients = $this->db->count_all_results('tbl_kpi_targets_clients');
            $this->db->where('TongDiem <', 60);
            $low_clients = $this->db->count_all_results('tbl_kpi_targets_clients');
        }
        $total_suppliers = 0;
        $low_suppliers = 0;
        if ($this->db->table_exists('tbl_kpi_targets_supplier')) {
            $total_suppliers = $this->db->count_all_results('tbl_kpi_targets_supplier');
            $this->db->where('TongDiem <', 60);
            $low_suppliers = $this->db->count_all_results('tbl_kpi_targets_supplier');
        }
        $total_partners = $total_clients + $total_suppliers;
        $low_partners = $low_clients + $low_suppliers;
        $radar_uy_tin = $total_partners > 0 ? round(($low_partners / $total_partners) * 100, 1) : 0;

        $radar = [
            'tai_chinh'   => $radar_tai_chinh,
            'van_hanh'    => $radar_van_hanh,
            'tuan_thu'    => $radar_tuan_thu,
            'chien_luoc'  => $radar_chien_luoc,
            'uy_tin'      => $radar_uy_tin,
        ];

        // ====================================================================
        // 5. HEATMAP PHÒNG BAN (chuẩn hóa thang: Low, Med, Med-High, High)
        // ====================================================================
        $heatmap = [];
        $max_score = 1;
        foreach ($dept_risk_scores as $info) {
            if ($info['score'] > $max_score) $max_score = $info['score'];
        }
        foreach ($dept_risk_scores as $name => $info) {
            if ($info['score'] <= 0) continue;
            $normalized = round(($info['score'] / $max_score) * 100, 1);
            if ($normalized >= 75) {
                $level = 'High';
                $color = 'red';
            } elseif ($normalized >= 50) {
                $level = 'Med-High';
                $color = 'orange';
            } elseif ($normalized >= 25) {
                $level = 'Med';
                $color = 'yellow';
            } else {
                $level = 'Low';
                $color = 'green';
            }
            $heatmap[] = [
                'department' => $name,
                'score'      => $info['score'],
                'normalized' => $normalized,
                'level'      => $level,
                'color'      => $color,
                'details'    => $info['details'],
            ];
        }
        // Sắp theo score giảm dần
        usort($heatmap, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        // ====================================================================
        // 6. THỐNG KÊ NHANH BỔ SUNG
        // ====================================================================
        // Tổng audit tính từ đầu năm
        $this->db->where('audit_date >=', $year . '-01-01');
        $total_audits_ytd = $this->db->count_all_results('tbl_audit');

        // CAPA đang mở
        $this->db->where('status', 'OPEN');
        $capa_open = $this->db->count_all_results('tbl_audit_capa');

        // Tổng phiếu SX vi phạm tháng này
        $this->db->where('violate', 1);
        $this->db->where('date >=', $month_start);
        $this->db->where('date <=', $month_end);
        $violations_month = $this->db->count_all_results('tblproduction_report');

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_impact'    => $total_impact,
                    'system_status'   => $system_status,
                    'system_label'    => $system_label,
                    'top_dept_label'  => $top_dept_label,
                    'total_alerts'    => $total_alerts,
                    'sev_red'         => $sev_red,
                    'sev_yellow'      => $sev_yellow,
                ],
                'radar'   => $radar,
                'heatmap' => $heatmap,
                'quick_stats' => [
                    'budget_usage'     => $budget_usage,
                    'avg_compliance'   => $avg_compliance,
                    'total_audits_ytd' => $total_audits_ytd,
                    'capa_open'        => $capa_open,
                    'violations_month' => $violations_month,
                    'client_risk'      => $client_risk_count,
                    'supplier_risk'    => $supplier_risk_count,
                    'mom_change'       => $mom_change,
                ],
                'filter' => [
                    'year'  => $year,
                    'month' => $month,
                ],
            ]
        ]);
    }

    public function get_bod_capa_checklists()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $real_data = [];

        // 1. Lấy 5 phiếu audit gần nhất chưa đóng (bảng tbl_audit)
        // if ($this->db->table_exists('tbl_audit')) {
        //     $this->db->select('id, audit_code as code, "Kiểm tra an toàn định kỳ" as title, department as dept, status, "audit" as type');
        //     $this->db->from('tbl_audit');
        //     $this->db->where('status !=', 'COMPLETED');
        //     $this->db->order_by('id', 'DESC');
        //     $this->db->limit(6);
        //     $audits = $this->db->get()->result_array();
        //     foreach ($audits as $a) {
        //         if(empty($a['code'])) $a['code'] = 'AUD-UNKNOWN';
        //         $real_data[] = $a;
        //     }
        // }

        // 2. Lấy phiếu vi phạm sản xuất báo cáo ngày (tblproduction_report)
        if ($this->db->table_exists('tblproduction_report')) {
            $this->db->select('id, reference_no as code, detail_tasks as title, "Khối SX/VH" as dept, "PENDING" as status, "report" as type');
            $this->db->from('tblproduction_report');
            $this->db->where('violate', 1);
            $this->db->order_by('id', 'DESC');
            $this->db->limit(5);
            $violations = $this->db->get()->result_array();
            foreach ($violations as $v) {
                if (empty($v['code'])) $v['code'] = 'REQ-UNK';
                if (empty($v['title'])) $v['title'] = 'Vi phạm an toàn/vận hành';
                $real_data[] = $v;
            }
        }

        // Chuẩn hóa status
        foreach ($real_data as &$row) {
            $st = strtoupper($row['status']);
            if (strpos($st, 'PENDING') !== false || strpos($st, 'CHỜ') !== false) {
                $row['status'] = 'PENDING';
            } elseif (strpos($st, 'COMPLETED') !== false || strpos($st, 'ĐÃ') !== false) {
                $row['status'] = 'COMPLETED';
            } else {
                $row['status'] = 'IN_PROGRESS';
            }
        }
        unset($row);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $real_data
        ]);
    }

    /**
     * View riêng biệt cho Dashboard để hiển thị Checklist sạch sẽ
     */
    public function view_checklist_full($id, $type)
    {
        if ($type == 'audit') {
            $this->load->model('audit_management_model');
            $audit = $this->db->where('id', $id)->get('tbl_audit')->row();
            if ($audit) {
                // Lấy tên người thực hiện từ auditor_id
                $staff = $this->db->select('firstname, lastname')->where('staffid', $audit->auditor_id)->get('tblstaff')->row();
                $audit->created_user_name = $staff ? ($staff->firstname . ' ' . $staff->lastname) : $audit->team_leader;

                $audit->department_name = $audit->department;
                $audit->subject = $audit->audit_code;
                $audit->created_at = $audit->audit_date; // Cột đúng là audit_date
            }
            $data['audit'] = $audit;

            // Tên bảng đúng là tbl_audit_checklist
            $data['details'] = $this->db->where('audit_id', $id)->get('tbl_audit_checklist')->result_array();
            $this->load->view('admin/risk_dashboard/view_checklist_clean', $data);
        } else {
            // Xử lý cho production_report (báo cáo vi phạm) với dữ liệu ĐẦY ĐỦ
            $this->db->select('tblproduction_report.*, tblstaff.firstname, tblstaff.lastname');
            $this->db->from('tblproduction_report');
            $this->db->join('tblstaff', 'tblstaff.staffid = tblproduction_report.staff_responsible', 'left');
            $this->db->where('tblproduction_report.id', $id);
            $report = $this->db->get()->row();

            if ($report) {
                $report->title = $report->detail_tasks;
                $report->code = $report->reference_no;
                $report->staff_name = $report->firstname . ' ' . $report->lastname;
                $report->date_report = $report->date;
                $report->procedure = $this->db->get_where('tblproduction_report_items', [
                    'id_production_report' => $id,
                    'type' => 'procedure'
                ])->result_array();
                $report->fix = $this->db->get_where('tblproduction_report_items', [
                    'id_production_report' => $id,
                    'type' => 'fix'
                ])->result_array();
                $report->note_fix = $report->note_fix ?? '';

                $this->load->model('recommended_list_model');
                $dtReason = $this->recommended_list_model->getProductionReportReason($id, 'trouble');

                $report->dtReason = $dtReason;
                $report->material = $this->db->get_where('tblproduction_report_items', ['id_production_report' => $id, 'type' => 'material'])->result_array();
                $report->man = $this->db->get_where('tblproduction_report_items', ['id_production_report' => $id, 'type' => 'man'])->result_array();
                $report->machine = $this->db->get_where('tblproduction_report_items', ['id_production_report' => $id, 'type' => 'machine'])->result_array();
                $report->method = $this->db->get_where('tblproduction_report_items', ['id_production_report' => $id, 'type' => 'method'])->result_array();
                $report->environment = $this->db->get_where('tblproduction_report_items', ['id_production_report' => $id, 'type' => 'environment'])->result_array();


                // Lấy danh sách file đính kèm (hình ảnh minh chứng)
                $data['files'] = $this->db->where(['rel_id' => $id, 'rel_type' => 'production_report'])->get('tblfiles')->result_array();
            }
            $data['report'] = $report;
            $this->load->view('admin/risk_dashboard/view_report_clean', $data);
        }
    }
}
