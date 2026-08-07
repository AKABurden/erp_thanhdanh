<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hr_lifecycle_dashboard extends AdminController
{
  function __construct()
  {
    parent::__construct();
  }
  public function index()
  {
    $data['title'] = _l('Dashboard Quản trị & Kiểm soát Nội bộ');

    // ===== FILTER MODE: day | month (default: day) =====
    $filter_mode = $this->input->get('filter') === 'month' ? 'month' : 'day';
    $data['filter_mode'] = $filter_mode;

    $today = date('Y-m-d');
    $month = date('m');
    $year  = date('Y');
    $day   = date('d');

    // Tạo điều kiện WHERE động theo mode
    if ($filter_mode === 'day') {
      $date_condition       = "DATE({{FIELD}}) = '{$today}'";
      $data['filter_label'] = 'Ngày ' . date('d/m/Y');
    } else {
      $date_condition       = "YEAR({{FIELD}}) = {$year} AND MONTH({{FIELD}}) = {$month}";
      $data['filter_label'] = 'Tháng ' . date('m/Y');
    }

    // Điều kiện cho kỳ trước (so sánh)
    $month_old = $month - 1;
    $year_old  = $year;
    if ($month_old == 0) {
      $month_old = 12;
      $year_old  = $year - 1;
    }
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    if ($filter_mode === 'day') {
      $date_condition_old       = "DATE({{FIELD}}) = '{$yesterday}'";
      $data['filter_label_old'] = 'Hôm qua';
    } else {
      $date_condition_old       = "YEAR({{FIELD}}) = {$year_old} AND MONTH({{FIELD}}) = {$month_old}";
      $data['filter_label_old'] = 'Tháng trước';
    }
    $cond_start     = str_replace('{{FIELD}}', 'startdate',  $date_condition);
    $cond_start_old = str_replace('{{FIELD}}', 'startdate',  $date_condition_old);
    $cond_date      = str_replace('{{FIELD}}', 'date',       $date_condition);
    $cond_date_old  = str_replace('{{FIELD}}', 'date',       $date_condition_old);
    $cond_dateadded     = str_replace('{{FIELD}}', 'dateadded', $date_condition);
    $cond_dateadded_old = str_replace('{{FIELD}}', 'dateadded', $date_condition_old);
    $cond_audit     = str_replace('{{FIELD}}', 'audit_date', $date_condition);
    $cond_audit_old = str_replace('{{FIELD}}', 'audit_date', $date_condition_old);

    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
    $tasks_completed_process = $this->db->query($sql)->row()->total;
    $data['tasks_completed_process'] = $tasks_completed_process;


    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start_old}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
    $tasks_completed_process_old = $this->db->query($sql)->row()->total;
    $data['tasks_completed_process_old'] = $tasks_completed_process_old;


    $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE {$cond_date}";
    $production_report = $this->db->query($sql)->row()->total;
    $data['production_report'] = $production_report;

    $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE {$cond_date_old}";
    $production_report_old = $this->db->query($sql)->row()->total;
    $data['production_report_old'] = $production_report_old;

    // Số vi phạm (violate=1) kỳ trước — để tính % chất lượng delta
    $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE violate = 1
                  AND {$cond_date_old}";
    $data['production_report_violate_old'] = $this->db->query($sql)->row()->total;

    // Task đang làm (status != 5 = chưa hoàn thành)
    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start}
                  AND status != 5";
    $tasks_in_progress = $this->db->query($sql)->row()->total;
    $data['tasks_in_progress'] = $tasks_in_progress;

    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start_old}
                  AND status != 5";
    $tasks_in_progress_old = $this->db->query($sql)->row()->total;
    $data['tasks_in_progress_old'] = $tasks_in_progress_old;

    // Task có quy trình chưa check hết
    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
    $tasks_incomplete_process = $this->db->query($sql)->row()->total;
    $data['tasks_incomplete_process'] = $tasks_incomplete_process;

    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start_old}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 0
                  )";
    $tasks_incomplete_process_old = $this->db->query($sql)->row()->total;
    $data['tasks_incomplete_process_old'] = $tasks_incomplete_process_old;

    // Task có quy trình nhưng chưa check được cái nào
    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 1
                  )";
    $tasks_no_check = $this->db->query($sql)->row()->total;
    $data['tasks_no_check'] = $tasks_no_check;

    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start_old}
                  AND EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM tbltask_checklist_items
                      WHERE tbltask_checklist_items.taskid = tbltasks.id
                        AND tbltask_checklist_items.finished = 1
                  )";
    $tasks_no_check_old = $this->db->query($sql)->row()->total;
    $data['tasks_no_check_old'] = $tasks_no_check_old;

    // Task trễ hạn (duedate < now VÀ status != 5)
    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start}
                  AND status != 5
                  AND duedate IS NOT NULL
                  AND duedate < NOW()";
    $tasks_overdue = $this->db->query($sql)->row()->total;
    $data['tasks_overdue'] = $tasks_overdue;

    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE {$cond_start_old}
                  AND status != 5
                  AND duedate IS NOT NULL
                  AND duedate < NOW()";
    $tasks_overdue_old = $this->db->query($sql)->row()->total;
    $data['tasks_overdue_old'] = $tasks_overdue_old;




    // Type 2: Phiếu vi phạm
    $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE violate = 1
                  AND {$cond_date}";
    $data['p3_type2_count'] = $this->db->query($sql)->row()->total;

    // Type 1: BCKPH chưa hoàn thành
    $sql = "SELECT COUNT(*) as total
                FROM tblproduction_report
                WHERE id != 0
                  AND {$cond_date}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_process_production_report
                      WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                        AND tbl_process_production_report.staff_process = 0
                  )";
    $data['p3_type1_count'] = $this->db->query($sql)->row()->total;

    // Type 3: Công việc chưa hoàn thành
    $sql = "SELECT COUNT(*) as total
                FROM tbltasks
                WHERE status != 5
                  AND {$cond_dateadded}";
    $data['p3_type3_count'] = $this->db->query($sql)->row()->total;

    // Type 4: Audit fail
    $sql = "SELECT COUNT(*) as total
                FROM tbl_audit
                WHERE {$cond_audit}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_audit_checklist
                      WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                        AND tbl_audit_checklist.status = 'no'
                  )";
    $data['p3_type4_count'] = $this->db->query($sql)->row()->total;

    // ===== TOP 5 NHÂN VIÊN NHIỀU NHẤT THEO TỪNG LOẠI =====

    // Top 5 nhân viên có nhiều phiếu vi phạm nhất (Type 2: violate = 1)
    $cond_date_pr  = str_replace('{{FIELD}}', 'pr.date',       $date_condition);
    $cond_date_t   = str_replace('{{FIELD}}', 't.dateadded',   $date_condition);
    $cond_audit_a  = str_replace('{{FIELD}}', 'a.audit_date',  $date_condition);

    $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tblproduction_report pr
                LEFT JOIN tblstaff s ON s.staffid = pr.staff_responsible
                WHERE pr.violate = 1
                  AND {$cond_date_pr}
                GROUP BY pr.staff_responsible
                ORDER BY total DESC
                LIMIT 5";
    $data['top5_type2'] = $this->db->query($sql)->result_array();

    // Top 5 nhân viên có nhiều BCKPH chưa hoàn thành nhất
    $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tblproduction_report pr
                LEFT JOIN tblstaff s ON s.staffid = pr.staff_responsible
                WHERE pr.id != 0
                  AND {$cond_date_pr}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_process_production_report
                      WHERE tbl_process_production_report.production_report_id = pr.id
                        AND tbl_process_production_report.staff_process = 0
                  )
                GROUP BY pr.staff_responsible
                ORDER BY total DESC
                LIMIT 5";
    $data['top5_type1'] = $this->db->query($sql)->result_array();

    // Top 5 nhân viên có nhiều công việc chưa hoàn thành nhất
    $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tbltasks t
                LEFT JOIN tblstaff s ON s.staffid = t.addedfrom
                WHERE t.status != 5
                  AND {$cond_date_t}
                GROUP BY t.addedfrom
                ORDER BY total DESC
                LIMIT 5";
    $data['top5_type3'] = $this->db->query($sql)->result_array();

    // Top 5 nhân viên có nhiều audit fail nhất
    $sql = "SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total
                FROM tbl_audit a
                JOIN tbl_room ON tbl_room.id = a.dept_id
                JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
                JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
                LEFT JOIN tblstaff s ON s.staffid = tblstaff_departments.staffid
                WHERE {$cond_audit_a}
                  AND EXISTS (
                      SELECT 1
                      FROM tbl_audit_checklist
                      WHERE tbl_audit_checklist.audit_id = a.id
                        AND tbl_audit_checklist.status = 'no'
                  )
                GROUP BY tblstaff_departments.staffid
                ORDER BY total DESC
                LIMIT 5";
    $data['top5_type4'] = $this->db->query($sql)->result_array();

    // ===== DANH SÁCH ĐÁNH GIÁ NHÂN SỰ (Cột mốc 3/6/9/12 tháng) =====
    // Lấy tất cả phiếu đánh giá type=1 (nhân viên CT) trong năm hiện tại
    $current_year = date('Y');
    $sql_eval = "
        SELECT
            e.id,
            e.code,
            e.staff_id,
            e.point,
            e.rating,
            e.rating_list,
            e.date,
            CONCAT(s.firstname, ' ', s.lastname) AS staff_name,
            r.name AS name_role,
            '' AS code_role_level,
            (
                SELECT COUNT(*)
                FROM tbl_probationary_assessment e2
                WHERE e2.staff_id = e.staff_id
                  AND e2.type = 2
                  AND YEAR(e2.date) = {$current_year}
                  AND (e.rating_list IS NULL OR e.rating_list = 0)
                  AND e2.date <= e.date
            ) AS phieu_so
        FROM tbl_probationary_assessment e
        LEFT JOIN tblstaff s ON s.staffid = e.staff_id
        LEFT JOIN tblroles r ON r.roleid = e.role_id
        WHERE e.type = 2
          AND check_salary = 0
          AND YEAR(e.date) = {$current_year}
          AND (e.rating_list IS NULL OR e.rating_list = 0)
        ORDER BY e.staff_id ASC, e.date ASC
    ";
    $eval_rows = $this->db->query($sql_eval)->result_array();

    // Map thứ tự phiếu sang cột mốc tháng
    $milestone_map = [1 => 3, 2 => 6, 3 => 9, 4 => 12];
    foreach ($eval_rows as &$row) {
      $so = (int)$row['phieu_so'];
      $row['milestone_month'] = $milestone_map[$so] ?? ($so * 3);
    }
    unset($row);
    $data['eval_list'] = $eval_rows;

    // ===== DANH SÁCH CẦN ĐÁNH GIÁ RỦI RO (big_risk > 0 AND rating IS NULL) =====
    // Lọc theo ngày/tháng hiện tại (dùng lại $date_condition đã build ở trên)
    $cond_eval_date = str_replace('{{FIELD}}', 'e.date', $date_condition);

    $sql_risk = "
        SELECT
            e.id,
            e.code,
            e.staff_id,
            e.big_risk,
            e.date,
            CONCAT(s.firstname, ' ', s.lastname) AS staff_name,
            r.name AS name_role,
            '' AS code_role_level
        FROM tbl_probationary_assessment e
        LEFT JOIN tblstaff s ON s.staffid = e.staff_id
        LEFT JOIN tblroles r ON r.roleid = e.role_id
        WHERE e.type = 2
          AND e.big_risk > 0
          AND (e.rating_list IS NULL OR e.rating_list = 0)
          AND {$cond_eval_date}
        ORDER BY e.big_risk DESC, e.date ASC
    ";
    $data['big_risk_list'] = $this->db->query($sql_risk)->result_array();

    // ===== KPI CHARTS: Thống kê trạng thái năm 2026 =====
    $kpi_year = 2026;

    // --- KPI Khách hàng: tính TongDiem động, phân loại trạng thái ---
    $sql_clients = "
        SELECT
            tbl_kpi_targets_clients.id,
            tbl_kpi_targets_clients.SoComplain,
            tbl_kpi_targets_clients.DonHangCo,
            (
                SELECT COUNT(*) FROM tbl_orders
                WHERE customer_id = tbl_kpi_targets_clients.id_client
                AND YEAR(date) = {$kpi_year}
                AND status = 'approved'
                AND type_orders != 13
            ) as DonHangCoTT,
            (
                SELECT COUNT(*) FROM tblproduction_report
                JOIN tbl_orders ON tbl_orders.id = tblproduction_report.id_orders
                WHERE tbl_orders.customer_id = tbl_kpi_targets_clients.id_client
                AND YEAR(tblproduction_report.date) = {$kpi_year}
            ) as SoComplainTT
        FROM tbl_kpi_targets_clients
    ";
    $client_rows = $this->db->query($sql_clients)->result_array();

    $kpi_client_status = ['Khách Tốt' => 0, 'Bình Thường' => 0, 'Cảnh Báo' => 0, 'Nguy Cơ Mất Khách' => 0];
    foreach ($client_rows as $cr) {
        $DiemCong = (int)$cr['DonHangCoTT'];
        $DiemTru = 0;
        if ($cr['SoComplainTT'] == 1) $DiemTru = 3;
        elseif ($cr['SoComplainTT'] == 2) $DiemTru = 5;
        elseif ($cr['SoComplainTT'] > 2) $DiemTru = (10 * ($cr['SoComplainTT'] - 2)) + 8;
        $TongDiem = $DiemCong - $DiemTru;
        if ($TongDiem >= 80) $kpi_client_status['Khách Tốt']++;
        elseif ($TongDiem >= 60) $kpi_client_status['Bình Thường']++;
        elseif ($TongDiem >= 40) $kpi_client_status['Cảnh Báo']++;
        else $kpi_client_status['Nguy Cơ Mất Khách']++;
    }
    $data['kpi_client_status'] = $kpi_client_status;

    // --- KPI Nhà cung cấp: tính TongDiem động ---
    $sql_suppliers = "
        SELECT
            tbl_kpi_targets_supplier.id,
            (
                SELECT COUNT(*) FROM tblpurchase_order
                WHERE suppliers_id = tblsuppliers.id
                AND YEAR(date) = {$kpi_year}
                AND delivery_date IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM tblimport
                    WHERE tblimport.id_order = tblpurchase_order.id
                    AND DATE(tblimport.date) <= DATE(tblpurchase_order.delivery_date)
                )
            ) as GiaoHangDungHanTT,
            (
                SELECT COUNT(*) FROM tbl_suggest_evaluate
                WHERE object_id = tblsuppliers.id AND object_type = 'supplier'
                AND YEAR(date) = {$kpi_year} AND status = 1
            ) as SoLanComplainTT
        FROM tbl_kpi_targets_supplier
        JOIN tblsuppliers ON tblsuppliers.id = tbl_kpi_targets_supplier.id_supplier
    ";
    $supplier_rows = $this->db->query($sql_suppliers)->result_array();

    $kpi_supplier_status = ['Nhà cung cấp tốt' => 0, 'Bình thường' => 0, 'Cảnh báo' => 0, 'Cần xem xét thay thế' => 0];
    foreach ($supplier_rows as $sr) {
        $DiemCong = (int)$sr['GiaoHangDungHanTT'];
        $DiemTru = 0;
        if ($sr['SoLanComplainTT'] == 1) $DiemTru = 3;
        elseif ($sr['SoLanComplainTT'] == 2) $DiemTru = 5;
        elseif ($sr['SoLanComplainTT'] > 2) $DiemTru = 10;
        $TongDiem = $DiemCong - $DiemTru;
        if ($TongDiem >= 80) $kpi_supplier_status['Nhà cung cấp tốt']++;
        elseif ($TongDiem >= 60) $kpi_supplier_status['Bình thường']++;
        elseif ($TongDiem >= 40) $kpi_supplier_status['Cảnh báo']++;
        else $kpi_supplier_status['Cần xem xét thay thế']++;
    }
    $data['kpi_supplier_status'] = $kpi_supplier_status;

    // --- KPI Ngân sách phòng ban: tính trạng thái từ tỷ lệ sử dụng ---
    $sql_budget = "
        SELECT
            tbl_department_budget.id,
            tbl_department_budget.ngan_sach_duoc_cap,
            (
                (SELECT COALESCE(SUM(op1.total),0) FROM tblother_payslips op1
                 WHERE op1.id_costs = tbl_department_budget.cost_id AND YEAR(op1.date) = {$kpi_year})
                +
                (SELECT COALESCE(SUM(opc.total),0) FROM tblother_payslip_cost opc
                 INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id
                 WHERE opc.cost_id = tbl_department_budget.cost_id AND YEAR(op2.date) = {$kpi_year})
            ) AS chi_phi_thuc_te
        FROM tbl_department_budget
    ";
    $budget_rows = $this->db->query($sql_budget)->result_array();

    $kpi_budget_status = ['Tốt' => 0, 'Đạt' => 0, 'Cảnh báo' => 0, 'Vượt' => 0];
    foreach ($budget_rows as $br) {
        $ngan_sach = (float)$br['ngan_sach_duoc_cap'];
        $chi_phi   = (float)$br['chi_phi_thuc_te'];
        $ty_le     = $ngan_sach > 0 ? round($chi_phi / $ngan_sach * 100, 2) : 0;
        if ($ty_le <= 90)       $kpi_budget_status['Tốt']++;
        elseif ($ty_le <= 100)  $kpi_budget_status['Đạt']++;
        elseif ($ty_le <= 110)  $kpi_budget_status['Cảnh báo']++;
        else                    $kpi_budget_status['Vượt']++;
    }
    $data['kpi_budget_status'] = $kpi_budget_status;

    $this->load->view('admin/hr_lifecycle_dashboard/index', $data);
  }

  // ===== AJAX: Lấy data vi phạm (violate) theo từng tháng cho Line Chart =====
  public function get_kpi_chart_data()
  {
    $range = $this->input->get('range'); // '6month' | 'year'
    $now   = new DateTime();

    if ($range === 'year') {
      // 12 tháng của năm hiện tại: Tháng 1 -> Tháng 12
      $year   = (int)$now->format('Y');
      $months = [];
      for ($m = 1; $m <= 12; $m++) {
        $months[] = ['year' => $year, 'month' => $m];
      }
    } else {
      // Mặc định: 6 tháng gần nhất (bao gồm tháng hiện tại)
      $months = [];
      for ($i = 5; $i >= 0; $i--) {
        $dt = clone $now;
        $dt->modify("-{$i} month");
        $months[] = ['year' => (int)$dt->format('Y'), 'month' => (int)$dt->format('n')];
      }
    }

    // Build month-key map để lookup nhanh
    $result_map = [];

    // Query violate count GROUP BY year-month trong khoảng cần thiết
    $first = $months[0];
    $last  = end($months);
    $date_from = sprintf('%04d-%02d-01', $first['year'], $first['month']);
    $date_to   = sprintf('%04d-%02d-31', $last['year'],  $last['month']);

    $sql = "SELECT
                YEAR(date)  AS yr,
                MONTH(date) AS mo,
                COUNT(*)    AS total
            FROM tblproduction_report
            WHERE violate = 1
              AND date >= '{$date_from}'
              AND date <= '{$date_to}'
            GROUP BY YEAR(date), MONTH(date)
            ORDER BY yr ASC, mo ASC";

    $rows = $this->db->query($sql)->result_array();
    foreach ($rows as $row) {
      $key = $row['yr'] . '-' . $row['mo'];
      $result_map[$key] = (int)$row['total'];
    }

    // Xây dựng labels + data theo thứ tự
    $vi_labels = [
      1  => 'Th 1',
      2  => 'Th 2',
      3  => 'Th 3',
      4  => 'Th 4',
      5  => 'Th 5',
      6  => 'Th 6',
      7  => 'Th 7',
      8  => 'Th 8',
      9  => 'Th 9',
      10 => 'Th 10',
      11 => 'Th 11',
      12 => 'Th 12',
    ];

    $labels = [];
    $data   = [];
    foreach ($months as $m) {
      $labels[] = $vi_labels[$m['month']];
      $key      = $m['year'] . '-' . $m['month'];
      $data[]   = isset($result_map[$key]) ? $result_map[$key] : 0;
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);
  }
}
