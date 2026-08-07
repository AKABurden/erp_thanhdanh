<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardKpi extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index($active_tab = 'dashboard')
    {
        $permission_map = [
            'dashboard'         => 'DashboardKpi',
            'import_phong_ban'  => 'DashboardKpi_Import',
            'department_budget' => 'DashboardKpi_Import',
            'import_khach_hang' => 'DashboardKpi_Import',
            'import_ncc'        => 'DashboardKpi_Import',
            'import_thiet_bi'   => 'DashboardKpi_Import',
            'cong_viec'         => 'DashboardKpi_CongViec',
            'production_report' => 'DashboardKpi_ProductionReport',
            'ky_danh_gia'       => 'DashboardKpi_KyDanhGia',
            'phieu_danh_gia'    => 'DashboardKpi_PhieuDanhGia',
            'vi_pham'           => 'DashboardKpi_ViPham',
            'phe_duyet'         => 'DashboardKpi_PheDuyet',
            'form_in'           => 'DashboardKpi_FormIn',
            'tong_hop'          => 'DashboardKpi_TongHop',
        ];

        $permission_key = $permission_map[$active_tab] ?? 'DashboardKpi';

        if (!has_permission($permission_key, '', 'view')) {
            // Thử chuyển về tab đầu tiên có quyền
            foreach ($permission_map as $tab => $key) {
                if (has_permission($key, '', 'view')) {
                    redirect(admin_url('DashboardKpi/index/' . $tab));
                }
            }
            access_denied($permission_key);
        }
        $valid_tabs = ['dashboard', 'kpi_import', 'nhan_su', 'danh_gia', 'ky_danh_gia', 'phieu_danh_gia', 'phieu_danh_gia_detail', 'cong_viec', 'production_report', 'vi_pham', 'dong_gop', 'tong_hop', 'form_in', 'phe_duyet', 'import_phong_ban', 'department_budget', 'import_khach_hang', 'import_ncc', 'import_thiet_bi'];
        if (!in_array($active_tab, $valid_tabs)) $active_tab = 'dashboard';
        $data['active_tab'] = $active_tab;

        // Import danh mục tabs — dùng view chung, truyền config riêng
        $importConfigs = [
            'import_phong_ban'  => ['title' => 'Phòng ban',    'table' => 'tbldepartments',        'fields' => ['name' => 'Tên phòng ban', 'short_name' => 'Tên viết tắt']],
            'import_khach_hang' => ['title' => 'Khách hàng',   'table' => 'tblclients',             'fields' => ['company' => 'Tên công ty', 'city' => 'Thành phố', 'country' => 'Quốc gia']],
            'import_ncc'        => ['title' => 'Nhà cung cấp', 'table' => 'tbl_vendors',            'fields' => ['name' => 'Tên NCC', 'phone' => 'Điện thoại', 'email' => 'Email']],
            'import_thiet_bi'   => ['title' => 'Thiết bị',     'table' => 'tbl_assets',             'fields' => ['name' => 'Tên thiết bị', 'serial_number' => 'Số serial', 'status' => 'Trạng thái']],
        ];
        if (isset($importConfigs[$active_tab])) {
            $data['import_config'] = $importConfigs[$active_tab];
        }
        if ($active_tab === 'import_phong_ban' || $active_tab === 'department_budget') {
            $data['dtDepartment'] = get_table_where('tbldepartments', ['room_id !=' => 0]);
            if ($active_tab === 'import_phong_ban') {
                $data['dtRoom'] = $this->db->select('id, name, code')->order_by('name', 'ASC')->get('tbl_room')->result_array();
            }
        }
        $data['active_tab'] = $active_tab;

        if ($active_tab === 'dashboard') $data['stats'] = $this->_get_stats();

        // Tab KPI Import (giống KpiDanhGia)
        if ($active_tab === 'kpi_import') {
            $data['kpi_import_list'] = $this->db->order_by('created_at', 'DESC')->get('tbl_kpi_import')->result_array();
            $this->db->distinct()->select('muc_tieu_kpi');
            $data['unique_muc_tieu'] = $this->db->get('tbl_kpi_import')->result_array();
        }

        // Tab Nhân sự
        if ($active_tab === 'nhan_su') {
            $this->db->select("s.staffid as id, CONCAT(s.firstname,' ',s.lastname) as ho_ten, s.email, s.active, s.status_work, d.name as ten_phong_ban");
            $this->db->from('tblstaff s');
            $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left');
            $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
            $this->db->group_by('s.staffid')->order_by('s.firstname', 'ASC');
            $data['nhan_su_list'] = $this->db->get()->result_array();
        }

        // Tab Đánh giá — Hồ sơ KPI cá nhân theo nhân sự
        if ($active_tab === 'danh_gia') {
            $data['staff_list']       = $this->_get_staff_list();
            $data['selected_staff']   = null;
            $data['staff_forms']      = [];
            $data['staff_violations'] = [];
            $data['staff_contribs']   = [];

            $sid = (int)$this->input->get('staff_id');
            if ($sid > 0) {
                // Thông tin nhân sự
                $this->db->select("s.staffid as id, CONCAT(s.firstname,' ',s.lastname) as ho_ten, s.email, d.name as ten_phong_ban");
                $this->db->from('tblstaff s');
                $this->db->join('tblstaff_departments sd', 'sd.staffid=s.staffid', 'left');
                $this->db->join('tbldepartments d', 'd.departmentid=sd.departmentid', 'left');
                $this->db->where('s.staffid', $sid)->group_by('s.staffid');
                $data['selected_staff'] = $this->db->get()->row_array();

                // Tất cả phiếu của nhân sự này
                $this->db->select("f.*, p.name as period_name, p.period_type, p.date_start, p.date_end");
                $this->db->from('tbl_kpi_forms f');
                $this->db->join('tbl_kpi_periods p', 'p.id = f.period_id', 'left');
                $this->db->where('f.staff_id', $sid)->order_by('f.id', 'DESC');
                $data['staff_forms'] = $this->db->get()->result_array();

                // Vi phạm của nhân sự
                $this->db->select("v.*, p.name as period_name");
                $this->db->from('tbl_kpi_violations v');
                $this->db->join('tbl_kpi_periods p', 'p.id=v.period_id', 'left');
                $this->db->where('v.staff_id', $sid)->order_by('v.id', 'DESC');
                $data['staff_violations'] = $this->db->get()->result_array();

                // Đóng góp của nhân sự
                $this->db->select("c.*, p.name as period_name");
                $this->db->from('tbl_kpi_contributions c');
                $this->db->join('tbl_kpi_periods p', 'p.id=c.period_id', 'left');
                $this->db->where('c.staff_id', $sid)->order_by('c.id', 'DESC');
                $data['staff_contribs'] = $this->db->get()->result_array();
            }
        }

        if ($active_tab === 'ky_danh_gia') $data['periods'] = $this->db->order_by('id', 'DESC')->get('tbl_kpi_periods')->result_array();

        if ($active_tab === 'phieu_danh_gia') {
            // Data loaded via AJAX - only init lightweight data here
            $data['forms'] = []; // empty, loaded by JS
            $data['room_list'] = $this->db->select('id, name, code')->order_by('name', 'ASC')->get('tbl_room')->result_array();
            $data['result_checklist'] = get_table_where('tbl_result_checklist');
        }

        if ($active_tab === 'phieu_danh_gia_detail') {
            $id = (int)$this->input->get('id');
            $type = 2; // Always 2 for CT
            $data['id'] = $id;
            $data['type'] = $type;
            $data['url_year'] = $this->input->get('year');
            $data['url_month'] = $this->input->get('month');
            $data['url_ky'] = $this->input->get('ky');

            if ($id > 0) {
                $this->db->select('tbl_probationary_assessment.*, 
                    COALESCE(CONCAT(tblstaff.firstname, " ", tblstaff.lastname), tbl_hr_eprofile.full_name) as staff_name,
                    tblroles.name as name_role, tbl_room.name as name_room,
                    CONCAT(COALESCE(s_hcns.firstname,"")," ",COALESCE(s_hcns.lastname,"")) as hcns_name,
                    CONCAT(COALESCE(s_ktnb.firstname,"")," ",COALESCE(s_ktnb.lastname,"")) as ktnb_name,
                    CONCAT(COALESCE(s_ksrr.firstname,"")," ",COALESCE(s_ksrr.lastname,"")) as ksrr_name,
                    CONCAT(COALESCE(s_bod.firstname,"")," ",COALESCE(s_bod.lastname,"")) as bod_name
                ', false);
                $this->db->from('tbl_probationary_assessment');
                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_probationary_assessment.staff_id', 'left');
                $this->db->join('tbl_hr_eprofile', 'tbl_hr_eprofile.id = tbl_probationary_assessment.staff_id', 'left');
                $this->db->join('tblroles', 'tblroles.roleid = tbl_probationary_assessment.role_id', 'left');
                $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
                $this->db->join('tblstaff s_hcns', 's_hcns.staffid = tbl_probationary_assessment.hcns_approve_by', 'left');
                $this->db->join('tblstaff s_ktnb', 's_ktnb.staffid = tbl_probationary_assessment.ktnb_approve_by', 'left');
                $this->db->join('tblstaff s_ksrr', 's_ksrr.staffid = tbl_probationary_assessment.ksrr_approve_by', 'left');
                $this->db->join('tblstaff s_bod', 's_bod.staffid = tbl_probationary_assessment.bod_approve_by', 'left');
                $this->db->where('tbl_probationary_assessment.id', $id);
                $data['dtData'] = $this->db->get()->row_array();

                $this->db->from('tbl_probationary_assessment_item');
                $this->db->where('probationary_assessment_id', $id);
                $dtDataItems = $this->db->get()->result_array();
                $checkListItems = [];
                foreach ($dtDataItems as $row) {
                    $checkListItems[$row['type_check_list']][] = $row;
                }
                $mappedItems = [];
                foreach ($checkListItems as $ctype => $items) {
                    foreach ($items as $item) {
                        $mappedItems[$ctype][$item['check_list_id']] = $item;
                    }
                }
                $data['checkListItems'] = $mappedItems;
            } else {
                $id_hr = (int)$this->input->get('id_hr');
                if ($id_hr > 0) {
                    $this->db->select('tbl_hr_eprofile.id as staff_id, tbl_hr_eprofile.full_name as staff_name, tbl_hr_eprofile.role_id, tbl_hr_eprofile.role_level_id');
                    $this->db->from('tbl_hr_eprofile');
                    $this->db->where('id', $id_hr);
                    $data['dtData'] = $this->db->get()->row_array();
                    if ($data['dtData']) {
                        $data['dtData']['type'] = 2; // Candidate
                    }
                } else {
                    $data['dtData'] = null;
                }
                $data['checkListItems'] = null;
            }

            $this->db->from('tbl_checklist_probationary_assessment');
            $dtChecklist = $this->db->get()->result_array();
            $checkList = [];
            foreach ($dtChecklist as $row) {
                $checkList[$row['type']][] = $row;
            }
            $data['checkList'] = $checkList;
            $data['levelChecklist'] = get_table_where('tbl_level_checklist');
            $data['resultChecklist'] = get_table_where('tbl_result_checklist');
            $data['code'] = getReference('probationary_assessment_ct');
            if ($id > 0 && !empty($data['dtData']['code'])) {
                $data['code'] = $data['dtData']['code'];
            }
            $data['staff_list'] = $this->_get_staff_list();

            // Compute ky_danh_gia label
            $ky_label = '';
            $staff_id_for_ky = $data['dtData']['staff_id'] ?? 0;
            $year_for_ky = date('Y');
            if (!empty($data['dtData']['date_start'])) {
                $year_for_ky = date('Y', strtotime($data['dtData']['date_start']));
            }
            if (!empty($staff_id_for_ky)) {
                $ky_map = $this->_compute_ky_map($year_for_ky);
                if ($id > 0 && isset($ky_map[$id])) {
                    $ky_label = $ky_map[$id];
                } else {
                    $cnt = $this->db->where('type', 2)->where('staff_id', $staff_id_for_ky)
                        ->where('YEAR(COALESCE(date_start, date_created)) = ' . (int)$year_for_ky, null, false)
                        ->count_all_results('tbl_probationary_assessment');
                    $next_index = $cnt + 1;
                    if ($next_index <= 12) $ky_label = '3 tháng';
                    elseif ($next_index <= 24) $ky_label = '6 tháng';
                    elseif ($next_index <= 36) $ky_label = '9 tháng';
                    else $ky_label = '12 tháng';
                }
            }
            $data['ky_danh_gia'] = $ky_label;

            $ky_label_for_date = !empty($data['url_ky']) ? $data['url_ky'] : $ky_label;
            $year_val = (int)($data['url_year'] ?: date('Y'));
            $month_val = (int)($data['url_month'] ?: date('m'));
            if ($month_val <= 0) $month_val = (int)date('m');
            $m_str = sprintf('%02d', $month_val);

            // 1. Determine End Date
            if (!empty($data['dtData']['date_end'])) {
                $ky_date_end = date('Y-m-d', strtotime($data['dtData']['date_end']));
            } else {
                if (preg_match('/Tuần|Tuan/i', $ky_label_for_date)) {
                    preg_match('/\d+/', $ky_label_for_date, $matches);
                    $week_num = isset($matches[0]) ? (int)$matches[0] : 1;
                    if ($week_num == 1) $ky_date_end = "$year_val-$m_str-07";
                    elseif ($week_num == 2) $ky_date_end = "$year_val-$m_str-14";
                    elseif ($week_num == 3) $ky_date_end = "$year_val-$m_str-21";
                    else $ky_date_end = date('Y-m-t', strtotime("$year_val-$m_str-01"));
                } else {
                    $ky_date_end = date('Y-m-t', strtotime("$year_val-$m_str-01"));
                }
            }

            // 2. Calculate Start Date by subtracting from End Date
            if (preg_match('/Tuần|Tuan/i', $ky_label_for_date)) {
                $ky_date_start = date('Y-m-d', strtotime('-6 days', strtotime($ky_date_end)));
            } elseif (strpos($ky_label_for_date, 'tháng') !== false) {
                $months_to_sub = (int)$ky_label_for_date;
                if ($months_to_sub <= 0) $months_to_sub = 3;
                $ky_date_start = date('Y-m-d', strtotime('-' . $months_to_sub . ' months +1 day', strtotime($ky_date_end)));
            } else {
                $ky_date_start = date('Y-m-d', strtotime('-7 days', strtotime($ky_date_end)));
            }

            $data['ky_date_start'] = $ky_date_start;
            $data['ky_date_end'] = $ky_date_end;
        }

        if ($active_tab === 'cong_viec') $data = array_merge($data, $this->_get_cong_viec_data());
        if ($active_tab === 'production_report') $data = array_merge($data, $this->_get_production_report_data());
        if ($active_tab === 'vi_pham') {
            $year_search = $this->input->get('year') ?? date('Y');

            $this->db->select('
                tblproduction_report.id_departments AS id_room,
                tbl_room.name as room_name,
                LPAD(MONTH(date), 2, "0") as month,
                tblproduction_report.violate,
                COUNT(*) AS total
            ');
            $this->db->from('tblproduction_report');
            $this->db->join('tbl_room', 'tbl_room.id = tblproduction_report.id_departments', 'left');
            $this->db->where('YEAR(date)', $year_search);
            $this->db->group_by([
                'tblproduction_report.id_departments',
                'MONTH(date)',
                'tblproduction_report.violate'
            ]);

            $dtListReport = $this->db->get()->result_array();

            $report_data = [];
            foreach ($dtListReport as $r) {
                if (empty($r['id_room'])) continue; // skip null
                $room = $r['room_name'] ?? 'Chưa xác định';
                if (!isset($report_data[$room])) {
                    $report_data[$room] = [];
                }
                // violate: 0 (không phù hợp), 1 (vi phạm)
                $report_data[$room][$r['month']][$r['violate']] = $r['total'];
            }

            $data['violate_year'] = $year_search;
            $data['synthetic_reports'] = $report_data;
            // Dữ liệu cũ (nếu frontend có form)
            $data['staff_list'] = $this->_get_staff_list();
        }
        if ($active_tab === 'dong_gop') {
            $this->db->select("c.*, CONCAT(s.firstname,' ',s.lastname) as ho_ten, p.name as period_name");
            $this->db->from('tbl_kpi_contributions c');
            $this->db->join('tblstaff s', 's.staffid=c.staff_id', 'left');
            $this->db->join('tbl_kpi_periods p', 'p.id=c.period_id', 'left');
            $this->db->order_by('c.id', 'DESC');
            $data['contributions'] = $this->db->get()->result_array();
            $data['periods'] = $this->db->get('tbl_kpi_periods')->result_array();
            $data['staff_list'] = $this->_get_staff_list();
        }

        // Tab Tổng hợp — dùng giao diện cũ kpi_evaluation
        if ($active_tab === 'tong_hop') {
            $data['dtDepartment'] = get_table_where('tbldepartments', ['room_id !=' => 0]);

            $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as firstname');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $data['staffs'] = $this->db->get()->result_array();
        }

        // Tab In phiếu
        if ($active_tab === 'form_in') {
            // Lấy tất cả phiếu đã được duyệt hết (approval_status >= 4) từ tbl_probationary_assessment
            $this->db->select("
                pa.id, pa.code, pa.date, pa.staff_id, pa.role_id,
                pa.date_start, pa.date_end, pa.point, pa.point_b, pa.point_c, pa.point_d,
                pa.rating, pa.rating_list, pa.note, pa.level_target, pa.level_achieved,
                pa.type, pa.date_created, pa.created_by, pa.approval_status,
                CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,'')) as staff_name,
                r.name as role_name,
                rm.name as room_name,
                rc.name as rating_name,
                rc.color as rating_color
            ", false);
            $this->db->from('tbl_probationary_assessment pa');
            $this->db->join('tblstaff s', 's.staffid = pa.staff_id', 'left');
            $this->db->join('tblroles r', 'r.roleid = pa.role_id', 'left');
            $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
            $this->db->join('tbl_result_checklist rc', 'rc.id = pa.rating_list', 'left');
            $this->db->where('pa.type', 2);
            $this->db->where('pa.approval_status >=', 4);
            $this->db->order_by('pa.id', 'DESC');
            $all_forms = $this->db->get()->result_array();

            // Tính kỳ đánh giá (3 tháng, 6 tháng...) — đếm phiếu theo nhân sự/năm
            $forms_asc = $all_forms;
            usort($forms_asc, function ($a, $b) {
                $ta = strtotime($a['date_start'] ?: $a['date_created']);
                $tb = strtotime($b['date_start'] ?: $b['date_created']);
                return $ta == $tb ? $a['id'] <=> $b['id'] : $ta <=> $tb;
            });
            $staff_year_counts = [];
            $ky_map = [];
            foreach ($forms_asc as $f) {
                $year = date('Y', strtotime($f['date_start'] ?: $f['date_created']));
                $sid  = $f['staff_id'];
                if (!isset($staff_year_counts[$sid][$year])) $staff_year_counts[$sid][$year] = 0;
                $staff_year_counts[$sid][$year]++;
                $cnt = $staff_year_counts[$sid][$year];
                if ($cnt <= 12) $ky_name = '3 tháng';
                elseif ($cnt <= 24) $ky_name = '6 tháng';
                elseif ($cnt <= 36) $ky_name = '9 tháng';
                else $ky_name = '12 tháng';
                $ky_map[$f['id']] = $ky_name;
            }
            foreach ($all_forms as &$f) {
                $f['ky_danh_gia'] = $ky_map[$f['id']] ?? '-';
                // Tính tuần trong tháng
                $ds = $f['date_start'] ?: $f['date_created'];
                if (!empty($ds)) {
                    $day = (int)date('j', strtotime($ds));
                    if ($day <= 7) $f['ky_tuan'] = 'Tuần 1';
                    elseif ($day <= 14) $f['ky_tuan'] = 'Tuần 2';
                    elseif ($day <= 21) $f['ky_tuan'] = 'Tuần 3';
                    else $f['ky_tuan'] = 'Tuần 4';
                } else {
                    $f['ky_tuan'] = '-';
                }
            }
            unset($f);
            $data['forms'] = $all_forms;

            // Chi tiết phiếu khi chọn
            $data['selected'] = null;
            $id = $this->input->get('id');
            // Nếu không truyền id, tự động chọn phiếu đầu tiên
            if (!$id && !empty($all_forms)) {
                $id = $all_forms[0]['id'];
            }
            if ($id) {
                $this->db->select("
                    pa.id, pa.code, pa.date, pa.staff_id, pa.role_id,
                    pa.date_start, pa.date_end, pa.point, pa.point_b, pa.point_c, pa.point_d,
                    pa.rating, pa.rating_list, pa.note, pa.level_target, pa.level_achieved,
                    pa.type, pa.date_created, pa.created_by, pa.approval_status,
                    CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,'')) as staff_name,
                    r.name as role_name,
                    rm.name as room_name,
                    rc.name as rating_name,
                    rc.color as rating_color
                ", false);
                $this->db->from('tbl_probationary_assessment pa');
                $this->db->join('tblstaff s', 's.staffid = pa.staff_id', 'left');
                $this->db->join('tblroles r', 'r.roleid = pa.role_id', 'left');
                $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
                $this->db->join('tbl_result_checklist rc', 'rc.id = pa.rating_list', 'left');
                $this->db->where('pa.id', (int)$id);
                $data['selected'] = $this->db->get()->row_array();
                if ($data['selected']) {
                    $data['selected']['ky_danh_gia'] = $ky_map[(int)$id] ?? '-';
                }

                $this->db->from('tbl_checklist_probationary_assessment');
                $dtChecklist = $this->db->get()->result_array();
                $checkList = [];
                foreach ($dtChecklist as $row) {
                    $checkList[$row['type']][] = $row;
                }
                $data['checkList'] = $checkList;

                $this->db->from('tbl_probationary_assessment_item');
                $this->db->where('probationary_assessment_id', $id);
                $dtDataItems = $this->db->get()->result_array();
                $checkListItems = [];
                foreach ($dtDataItems as $row) {
                    $checkListItems[$row['type_check_list']][] = $row;
                }
                $mappedItems = [];
                foreach ($checkListItems as $ctype => $items) {
                    foreach ($items as $item) {
                        $mappedItems[$ctype][$item['check_list_id']] = $item;
                    }
                }
                $data['checkListItems'] = $mappedItems;
            }
        }

        if ($active_tab === 'phe_duyet') {
            if (!$this->db->field_exists('audit_id', 'tbl_probationary_assessment')) {
                $this->db->query("ALTER TABLE `tbl_probationary_assessment` ADD `audit_id` INT NULL");
            }

            $this->db->select("p.*, CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,'')) as ho_ten, rm.id as room_id, rm.name as room_name, r.name as role_name, rc.color as rating_color, au.audit_code", false);
            $this->db->from('tbl_probationary_assessment p');
            $this->db->join('tblstaff s', 's.staffid=p.staff_id', 'left');
            $this->db->join('tblroles r', 'r.roleid = p.role_id', 'left');
            $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
            $this->db->join('tbl_result_checklist rc', 'rc.id = p.rating_list', 'left');
            $this->db->join('tbl_audit au', 'au.id = p.audit_id', 'left');
            $this->db->where('p.type', 2);
            $this->db->order_by('p.id', 'DESC');
            $all_approvals = $this->db->get()->result_array();

            // Lấy kỳ đánh giá + tuần từ DB (type_ki và ki)
            foreach ($all_approvals as &$f) {
                $tk = (int)$f['type_ki'];
                $ki = (int)$f['ki'];
                if ($tk === 1 && $ki) {
                    $f['ky_tuan'] = 'Tuần ' . $ki;
                    $f['ky_danh_gia'] = 'Tuần ' . $ki;
                } elseif ($tk === 2 && $ki) {
                    $f['ky_tuan'] = '-';
                    $f['ky_danh_gia'] = $ki . ' tháng'; // Trong JS lọc Kỳ 3 tháng thì tabVal là '3 tháng'
                } else {
                    $f['ky_tuan'] = '-';
                    $f['ky_danh_gia'] = '-';
                }
            }
            unset($f);
            $data['approvals'] = $all_approvals;

            // Room list cho filter
            $data['pd_room_list'] = $this->db->select('id, name, code')->order_by('name', 'ASC')->get('tbl_room')->result_array();

            // Staff list cho modal Audit
            $this->db->select('staffid as id, CONCAT(firstname," ",lastname) as ho_ten');
            $this->db->where('active', 1);
            $data['staff_list'] = $this->db->get('tblstaff')->result_array();
        }
        $this->load->view('admin/dashboard_kpi/index', $data);
    }
    // === AJAX: Phiếu đánh giá - Phân trang ===
    public function ajax_pdg_list()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $page     = max(1, (int)$this->input->get('page'));
        $per_page = max(5, min(100, (int)($this->input->get('per_page') ?: 20)));
        $search   = trim($this->input->get('search') ?? '');
        $room     = trim($this->input->get('room') ?? '');
        $status   = $this->input->get('status');
        $year     = trim($this->input->get('year') ?? '');
        $month    = trim($this->input->get('month') ?? '');
        $ky       = trim($this->input->get('ky') ?? '');

        if ($ky !== '') {
            $staff_res = $this->_get_staff_assessments($search, $room, $status, $year, $month, $ky, $page, $per_page);
            $forms = $staff_res['data'];
            $total_filtered = $staff_res['total'];
            $status_counts = $staff_res['status_counts'];
        } else {
            $status_counts = $this->_pdg_status_counts($search, $room, $year, $month, $ky);

            $this->db->select("
                pa.id, pa.code, pa.date, pa.staff_id, pa.role_id,
                pa.date_start, pa.date_end, pa.point, pa.point_b, pa.point_c, pa.point_d,
                pa.rating, pa.rating_list, pa.note, pa.level_target, pa.level_achieved,
                pa.type, pa.date_created, pa.created_by, pa.approval_status, pa.type_ki, pa.ki,
                CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,'')) as staff_name,
                s.email as staff_email,
                r.name as role_name,
                rm.name as room_name,
                rm.id as room_id,
                rc.name as rating_name,
                rc.color as rating_color,
                pa.audit_id,
                au.audit_code,
                pa.hcns_approve_by, pa.hcns_approve_date,
                pa.ktnb_approve_by, pa.ktnb_approve_date,
                pa.ksrr_approve_by, pa.ksrr_approve_date,
                pa.bod_approve_by, pa.bod_approve_date,
                CONCAT(COALESCE(s_hcns.firstname,''),' ',COALESCE(s_hcns.lastname,'')) as hcns_name,
                CONCAT(COALESCE(s_ktnb.firstname,''),' ',COALESCE(s_ktnb.lastname,'')) as ktnb_name,
                CONCAT(COALESCE(s_ksrr.firstname,''),' ',COALESCE(s_ksrr.lastname,'')) as ksrr_name,
                CONCAT(COALESCE(s_bod.firstname,''),' ',COALESCE(s_bod.lastname,'')) as bod_name
            ", false);
            $this->db->from('tbl_probationary_assessment pa');
            $this->db->join('tblstaff s', 's.staffid = pa.staff_id', 'left');
            $this->db->join('tblroles r', 'r.roleid = pa.role_id', 'left');
            $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
            $this->db->join('tbl_result_checklist rc', 'rc.id = pa.rating_list', 'left');
            $this->db->join('tbl_audit au', 'au.id = pa.audit_id', 'left');
            $this->db->join('tblstaff s_hcns', 's_hcns.staffid = pa.hcns_approve_by', 'left');
            $this->db->join('tblstaff s_ktnb', 's_ktnb.staffid = pa.ktnb_approve_by', 'left');
            $this->db->join('tblstaff s_ksrr', 's_ksrr.staffid = pa.ksrr_approve_by', 'left');
            $this->db->join('tblstaff s_bod', 's_bod.staffid = pa.bod_approve_by', 'left');
            $this->db->where('pa.type', 2);

            $this->_apply_pdg_filters($this->db, $search, $room, $year, $month, $ky);

            if ($status !== null && $status !== '' && $status !== 'all') {
                $st = (int)$status;
                if ($st >= 4) {
                    $this->db->where('pa.approval_status >=', 4);
                } else {
                    $this->db->where('pa.approval_status', $st);
                }
            }

            $clone_db = clone $this->db;
            $total_filtered = $clone_db->count_all_results();

            $this->db->order_by('pa.id', 'DESC');
            $this->db->limit($per_page, ($page - 1) * $per_page);
            $forms = $this->db->get()->result_array();
        }

        foreach ($forms as &$f) {
            $ds_calc = $f['calc_date_start'] ?? ($f['date_start'] ?: ($f['date_created'] ?? ''));
            $de_calc = $f['calc_date_end'] ?? ($f['date_end'] ?? '');

            if (!empty($ds_calc) && !empty($de_calc)) {
                $stats = $this->_get_stats_for_phieu($f['staff_id'], $ds_calc, $de_calc);
                $f = array_merge($f, $stats);
            } else {
                $f['total_task'] = $f['count_bckph_old'] = $f['count_bckph'] = $f['violate_old'] = $f['violate'] = $f['vuot'] = 0;
                $f['violation_p1'] = $f['violation_p2'] = $f['violation_p3'] = $f['weight_p2'] = $f['weight_p3'] = 0;
                $f['kpi_point'] = $f['kpi_rating'] = $f['kpi_color'] = '';
                $f['kpi_bonus'] = $f['kpi_discipline'] = [];
                $f['check_p3'] = 'Không';
            }
        }
        unset($f);

        echo json_encode([
            'success' => true,
            'data' => $forms,
            'total' => $total_filtered,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total_filtered / $per_page),
            'status_counts' => $status_counts,
        ]);
    }

    private function _get_staff_assessments($search, $room, $status, $year, $month, $ky, $page, $per_page)
    {
        $type_ki = (strpos($ky, 'Tuần') !== false) ? 1 : 2;
        $ki_val = (int)preg_replace('/[^0-9]/', '', $ky);
        $m_val = ($month !== '') ? (int)$month : (int)date('m');
        $y_val = ($year !== '') ? (int)$year : (int)date('Y');

        $this->db->select("s.staffid, CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,'')) as staff_name, s.email as staff_email, r.name as role_name, rm.name as room_name, rm.id as room_id");
        $this->db->from('tblstaff s');
        $this->db->join('tblroles r', 'r.roleid = s.role', 'left');
        $this->db->join('tbl_role_level rl', 'rl.id = s.role_level_id', 'left');
        $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
        $this->db->where('s.check_salary', 0);
        $this->db->where('s.status_work', 1);
        $this->db->where('r.day_evaluate != 0', null, false);

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like("CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,''))", $search, 'both', false);
            $this->db->or_like('rm.name', $search);
            $this->db->or_like('r.name', $search);
            $this->db->group_end();
        }
        if ($room !== '') $this->db->where('rm.name', $room);

        $staff_list = $this->db->get()->result_array();
        $staff_ids = array_column($staff_list, 'staffid');

        $ass_by_staff = [];
        if (!empty($staff_ids)) {
            $this->db->select('pa.*, rc.name as rating_name, rc.color as rating_color, au.audit_code,
                CONCAT(COALESCE(s_hcns.firstname,"")," ",COALESCE(s_hcns.lastname,"")) as hcns_name,
                CONCAT(COALESCE(s_ktnb.firstname,"")," ",COALESCE(s_ktnb.lastname,"")) as ktnb_name,
                CONCAT(COALESCE(s_ksrr.firstname,"")," ",COALESCE(s_ksrr.lastname,"")) as ksrr_name,
                CONCAT(COALESCE(s_bod.firstname,"")," ",COALESCE(s_bod.lastname,"")) as bod_name
            ');
            $this->db->from('tbl_probationary_assessment pa');
            $this->db->join('tbl_result_checklist rc', 'rc.id = pa.rating_list', 'left');
            $this->db->join('tbl_audit au', 'au.id = pa.audit_id', 'left');
            $this->db->join('tblstaff s_hcns', 's_hcns.staffid = pa.hcns_approve_by', 'left');
            $this->db->join('tblstaff s_ktnb', 's_ktnb.staffid = pa.ktnb_approve_by', 'left');
            $this->db->join('tblstaff s_ksrr', 's_ksrr.staffid = pa.ksrr_approve_by', 'left');
            $this->db->join('tblstaff s_bod', 's_bod.staffid = pa.bod_approve_by', 'left');
            $this->db->where_in('pa.staff_id', $staff_ids);
            $this->db->where('pa.type', 2);
            $this->db->order_by('pa.date_start', 'DESC');
            $this->db->order_by('pa.id', 'DESC');
            $all_assessments = $this->db->get()->result_array();
            foreach ($all_assessments as $a) {
                $ass_by_staff[$a['staff_id']][] = $a;
            }
        }

        $results = [];
        $status_counts = ['all' => 0, '0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '-1' => 0];

        foreach ($staff_list as $s) {
            $exact_match = null;
            $fallback = null;
            if (isset($ass_by_staff[$s['staffid']])) {
                foreach ($ass_by_staff[$s['staffid']] as $a) {
                    $a_y = (int)date('Y', strtotime($a['date_start'] ?: $a['date_created']));
                    $a_m = (int)date('m', strtotime($a['date_start'] ?: $a['date_created']));
                    $a_type = (int)$a['type_ki'];
                    $a_ki = (int)$a['ki'];

                    $is_exact = false;
                    $is_before = false;

                    if ($type_ki == 1) {
                        if ($a_type == 1 && $a_ki == $ki_val && $a_y == $y_val && $a_m == $m_val) {
                            $is_exact = true;
                        } else {
                            if ($a_y < $y_val) $is_before = true;
                            elseif ($a_y == $y_val) {
                                if ($a_m < $m_val) $is_before = true;
                                elseif ($a_m == $m_val && $a_type == 1 && $a_ki < $ki_val) $is_before = true;
                            }
                        }
                    } else {
                        if ($a_type == 2 && $a_ki == $ki_val && $a_y == $y_val) {
                            $is_exact = true;
                        } else {
                            if ($a_y < $y_val) $is_before = true;
                            elseif ($a_y == $y_val) {
                                if ($a_type == 2 && $a_ki < $ki_val) $is_before = true;
                                elseif ($a_type == 1 && $a_m <= $ki_val) $is_before = true;
                            }
                        }
                    }

                    if ($is_exact) {
                        $exact_match = $a;
                        break;
                    }
                    if ($is_before && !$fallback) {
                        $fallback = $a;
                    }
                }
            }

            $res = $exact_match ?: $fallback;
            $is_fb = !$exact_match;

            $row = $s;
            $row['staff_id'] = $s['staffid'];
            if ($res) {
                $row['id'] = $res['id'];
                $row['code'] = $res['code'];
                $row['point'] = $res['point'];
                $row['rating_color'] = $res['rating_color'];
                $row['rating_name'] = $res['rating_name'];
                $row['type'] = 2;
                $row['type_ki'] = $type_ki;
                $row['ki'] = $ki_val;
                $row['date_start'] = $res['date_start'];
                $row['date_end'] = $res['date_end'];
                $row['calc_date_start'] = $res['date_start'];
                $row['calc_date_end'] = $res['date_end'];
                $row['approval_status'] = $res['approval_status'];
                $row['audit_id'] = $res['audit_id'];
                $row['audit_code'] = $res['audit_code'];
                $row['hcns_name'] = $res['hcns_name'];
                $row['ktnb_name'] = $res['ktnb_name'];
                $row['ksrr_name'] = $res['ksrr_name'];
                $row['bod_name'] = $res['bod_name'];
                $row['hcns_approve_date'] = $res['hcns_approve_date'];
                $row['ktnb_approve_date'] = $res['ktnb_approve_date'];
                $row['ksrr_approve_date'] = $res['ksrr_approve_date'];
                $row['bod_approve_date'] = $res['bod_approve_date'];
                $row['note'] = $res['note'];
            } else {
                $row['id'] = 0;
                $row['code'] = '';
                $row['point'] = 0;
                $row['rating_color'] = '';
                $row['rating_name'] = '';
                $row['type'] = 2;
                $row['type_ki'] = $type_ki;
                $row['ki'] = $ki_val;
                $row['date_start'] = '';
                $row['date_end'] = '';
                $row['calc_date_start'] = '';
                $row['calc_date_end'] = '';
                $row['approval_status'] = 0;
                $row['audit_id'] = 0;
                $row['audit_code'] = '';
            }

            $st = (int)$row['approval_status'];
            $status_counts['all']++;
            if ($st >= 4) $status_counts['4']++;
            elseif ($st == -1) $status_counts['-1']++;
            else $status_counts[$st]++;

            if ($status !== null && $status !== '' && $status !== 'all') {
                $st_f = (int)$status;
                if ($st_f >= 4 && $st < 4) continue;
                if ($st_f < 4 && $st !== $st_f) continue;
            }

            $results[] = $row;
        }

        $total = count($results);
        if ($page !== null && $per_page !== null) {
            $offset = ($page - 1) * $per_page;
            $results = array_slice($results, $offset, $per_page);
        }

        return ['data' => $results, 'total' => $total, 'status_counts' => $status_counts];
    }

    private function _pdg_status_counts($search, $room, $year, $month, $ky)
    {
        $this->db->select("
            SUM(1) as total_all,
            SUM(CASE WHEN pa.approval_status = 0 THEN 1 ELSE 0 END) as s0,
            SUM(CASE WHEN pa.approval_status = 1 THEN 1 ELSE 0 END) as s1,
            SUM(CASE WHEN pa.approval_status = 2 THEN 1 ELSE 0 END) as s2,
            SUM(CASE WHEN pa.approval_status = 3 THEN 1 ELSE 0 END) as s3,
            SUM(CASE WHEN pa.approval_status >= 4 THEN 1 ELSE 0 END) as s4,
            SUM(CASE WHEN pa.approval_status = -1 THEN 1 ELSE 0 END) as sn1
        ", false);
        $this->db->from('tbl_probationary_assessment pa');
        $this->db->join('tblstaff s', 's.staffid = pa.staff_id', 'left');
        $this->db->join('tblroles r', 'r.roleid = pa.role_id', 'left');
        $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
        $this->db->where('pa.type', 2);

        $this->_apply_pdg_filters($this->db, $search, $room, $year, $month, $ky);

        $row = $this->db->get()->row_array();
        return [
            'all' => (int)($row['total_all'] ?? 0),
            '0'   => (int)($row['s0'] ?? 0),
            '1'   => (int)($row['s1'] ?? 0),
            '2'   => (int)($row['s2'] ?? 0),
            '3'   => (int)($row['s3'] ?? 0),
            '4'   => (int)($row['s4'] ?? 0),
            '-1'  => (int)($row['sn1'] ?? 0),
        ];
    }



    private function _apply_pdg_filters($db, $search, $room, $year, $month, $ky)
    {
        if ($search !== '') {
            $db->group_start();
            $db->like('pa.code', $search);
            $db->or_like("CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,''))", $search, 'both', false);
            $db->or_like('rm.name', $search);
            $db->or_like('r.name', $search);
            $db->group_end();
        }
        if ($room !== '') $db->where('rm.name', $room);
        if ($year !== '') $db->where('YEAR(pa.date_start) = ' . (int)$year, null, false);
        if ($month !== '' && (strpos($ky, 'Tuần') !== false)) {
            $db->where('MONTH(pa.date_start) = ' . (int)$month, null, false);
        }
        if ($ky !== '') {
            if (strpos($ky, 'Tuần') !== false) {
                $week_num = (int)str_replace('Tuần ', '', $ky);
                $db->where('pa.type_ki', 1);
                $db->where('pa.ki', $week_num);
            } else {
                $month_num = (int)str_replace(' tháng', '', $ky);
                $db->where('pa.type_ki', 2);
                $db->where('pa.ki', $month_num);
            }
        }
        if ($ky == '') {
            if ($month !== '') {
                $db->where('MONTH(pa.date_start) = ' . (int)$month, null, false);
            }
        }
    }

    private function _compute_ky_map($year)
    {
        $this->db->select('id, staff_id, date_start, date_created');
        $this->db->from('tbl_probationary_assessment');
        $this->db->where('type', 2);
        $this->db->where('YEAR(COALESCE(date_start, date_created)) = ' . (int)$year, null, false);
        $this->db->order_by('COALESCE(date_start, date_created) ASC, id ASC');
        $all = $this->db->get()->result_array();

        $staff_counts = [];
        $ky_map = [];
        foreach ($all as $f) {
            $sid = $f['staff_id'];
            if (!isset($staff_counts[$sid])) $staff_counts[$sid] = 0;
            $staff_counts[$sid]++;
            $cnt = $staff_counts[$sid];
            if ($cnt <= 12) $ky = '3 tháng';
            elseif ($cnt <= 24) $ky = '6 tháng';
            elseif ($cnt <= 36) $ky = '9 tháng';
            else $ky = '12 tháng';
            $ky_map[$f['id']] = $ky;
        }
        return $ky_map;
    }

    public function ajax_get_ky_label()
    {
        $staff_id = $this->input->post('staff_id');
        $date = $this->input->post('date') ?: date('Y-m-d');
        $id = (int)$this->input->post('id');

        if (empty($staff_id)) {
            echo json_encode(['success' => false, 'label' => '-']);
            return;
        }

        $year = date('Y', strtotime($date));
        $this->db->select('id, staff_id');
        $this->db->from('tbl_probationary_assessment');
        $this->db->where('type', 2);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('YEAR(COALESCE(date_start, date_created)) = ' . (int)$year, null, false);
        $this->db->order_by('COALESCE(date_start, date_created) ASC, id ASC');
        $records = $this->db->get()->result_array();

        $label = '3 tháng';
        $index = 1;
        foreach ($records as $r) {
            if ($id > 0 && $r['id'] == $id) break;
            if ($id == 0) {
                // If it's a new record, we are essentially looking for the "next" index
                $index = count($records) + 1;
                break;
            }
            $index++;
        }

        if ($index <= 12) $label = '3 tháng';
        elseif ($index <= 24) $label = '6 tháng';
        elseif ($index <= 36) $label = '9 tháng';
        else $label = '12 tháng';

        echo json_encode(['success' => true, 'label' => $label, 'index' => $index]);
    }

    // === AJAX CRUD: Kỳ đánh giá ===
    public function save_period()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        $row = [
            'name' => $this->input->post('name', true),
            'period_type' => $this->input->post('period_type', true),
            'date_start' => $this->input->post('date_start'),
            'date_end' => $this->input->post('date_end'),
            'status' => $this->input->post('status', true) ?: 'draft',
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_periods', $row);
        } else {
            $row['created_by'] = get_staff_user_id();
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_kpi_periods', $row);
            $id = $this->db->insert_id();
        }
        echo json_encode(['success' => true, 'id' => $id]);
    }

    public function delete_period()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $this->db->where('id', (int)$this->input->post('id'))->delete('tbl_kpi_periods');
        echo json_encode(['success' => true]);
    }

    // === AJAX: Tạo phiếu đánh giá & tính điểm ===
    public function save_form()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        $period_id = (int)$this->input->post('period_id');
        $staff_id = (int)$this->input->post('staff_id');
        $eval_type = $this->input->post('evaluation_type', true);

        // Lấy period
        $period = $this->db->where('id', $period_id)->get('tbl_kpi_periods')->row_array();
        if (empty($period)) {
            echo json_encode(['success' => false, 'message' => 'Kỳ không tồn tại']);
            return;
        }

        // Tính Gate 1
        $staff = $this->db->where('staffid', $staff_id)->get('tblstaff')->row_array();
        $has_dept = !empty($staff);
        $has_tasks = $this->db->where("id IN (SELECT taskid FROM tbltask_assigned WHERE staffid=$staff_id)")->where('startdate >=', $period['date_start'])->where('startdate <=', $period['date_end'])->count_all_results('tbltasks') > 0;
        $gate1 = ($has_dept && $has_tasks) ? 'pass' : 'fail';

        // Tính Gate 2 - Performance
        $this->db->select("COUNT(id) as total, SUM(CASE WHEN status=5 THEN 1 ELSE 0 END) as done, SUM(CASE WHEN status=5 AND datefinished IS NOT NULL AND duedate IS NOT NULL AND datefinished<=duedate THEN 1 ELSE 0 END) as ontime, SUM(CASE WHEN duedate IS NOT NULL AND ((status!=5 AND duedate<NOW()) OR (status=5 AND datefinished>duedate)) THEN 1 ELSE 0 END) as overdue");
        $this->db->where("id IN (SELECT taskid FROM tbltask_assigned WHERE staffid=$staff_id)");
        $this->db->where('startdate >=', $period['date_start']);
        $this->db->where('startdate <=', $period['date_end']);
        $ts = $this->db->get('tbltasks')->row_array();

        $total = (int)$ts['total'];
        $done = (int)$ts['done'];
        $ontime = (int)$ts['ontime'];
        $overdue = (int)$ts['overdue'];
        $comp_rate = $total > 0 ? $done / $total : 0;
        $ontime_rate = $done > 0 ? $ontime / $done : 0;
        $gate2_raw = $comp_rate * 40 + $ontime_rate * 30 + 30; // process+quality placeholder
        $p2 = min(60, max(0, $gate2_raw * 60 / 100));

        // Tính Gate 3 - Compliance
        $penalty = 0;
        $hard_fail = 0;
        if ($id > 0) {
            $violations = $this->db->where('kpi_form_id', $id)->get('tbl_kpi_violations')->result_array();
        } else {
            $violations = $this->db->where('period_id', $period_id)->where('staff_id', $staff_id)->get('tbl_kpi_violations')->result_array();
        }
        foreach ($violations as $v) {
            $penalty += (float)$v['penalty_score'];
            if ($v['is_hard_fail']) $hard_fail = 1;
        }
        $gate3 = max(0, 20 - $penalty);
        if ($hard_fail) $gate3 = 0;

        // Gate 4 + P3
        $gate4 = (float)$this->input->post('gate4_capability') ?: 16;
        $contrib_bonus = (float)$this->input->post('contribution_bonus') ?: 0;
        $p3 = min(20, $gate4 + $contrib_bonus);
        if ($gate3 < 15) $p3 = min($p3, 10);
        if ($gate3 < 10 || $hard_fail) $p3 = 0;

        $total_score = $p2 + $gate3 + $p3;

        // Rating & Override
        $rating = $this->_calc_rating($total_score);
        if ($gate1 === 'fail' && in_array($rating, ['excellent', 'good'])) $rating = 'passed';
        if ($hard_fail) $rating = 'failed';
        if ($gate3 < 10 && $rating !== 'failed') $rating = 'need_monitoring';

        $warning = 'none';
        if ($total_score < 60) $warning = 'red';
        elseif ($total_score < 70) $warning = 'orange';
        elseif ($total_score < 80) $warning = 'yellow';

        $row = [
            'period_id' => $period_id,
            'staff_id' => $staff_id,
            'evaluation_type' => $eval_type,
            'gate1_result' => $gate1,
            'gate1_note' => $gate1 === 'fail' ? 'Thiếu dữ liệu đầu vào' : 'Đủ điều kiện',
            'total_tasks' => $total,
            'completed_tasks' => $done,
            'ontime_tasks' => $ontime,
            'overdue_tasks' => $overdue,
            'completion_rate' => round($comp_rate * 100, 2),
            'ontime_rate' => round($ontime_rate * 100, 2),
            'p2_performance' => round($p2, 2),
            'gate3_compliance' => round($gate3, 2),
            'gate4_capability' => round($gate4, 2),
            'contribution_bonus' => round($contrib_bonus, 2),
            'p3_final' => round($p3, 2),
            'total_score' => round($total_score, 2),
            'hard_fail' => $hard_fail,
            'warning_level' => $warning,
            'final_rating' => $rating,
            'status' => 'draft',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_forms', $row);
        } else {
            $row['created_by'] = get_staff_user_id();
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_kpi_forms', $row);
            $id = $this->db->insert_id();
        }
        $this->_write_log($id, 'calculate', null, $row);
        echo json_encode(['success' => true, 'id' => $id, 'data' => $row]);
    }

    public function delete_form()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        $this->db->where('id', $id)->delete('tbl_kpi_forms');
        $this->db->where('kpi_form_id', $id)->delete('tbl_kpi_approvals');
        echo json_encode(['success' => true]);
    }

    public function bulk_create_forms()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $staff_ids = $this->input->post('staff_ids');
        $period_id = (int)$this->input->post('period_id');
        $eval_type = $this->input->post('evaluation_type', true) ?: 'monthly';
        $gate4     = (float)$this->input->post('gate4_capability') ?: 16;

        if (empty($staff_ids) || !is_array($staff_ids)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn ít nhất 1 nhân sự.']);
            return;
        }
        if (!$period_id) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn kỳ đánh giá.']);
            return;
        }

        $period = $this->db->where('id', $period_id)->get('tbl_kpi_periods')->row_array();
        if (!$period) {
            echo json_encode(['success' => false, 'message' => 'Kỳ đánh giá không tồn tại.']);
            return;
        }

        $created = 0;
        $skipped = 0;
        foreach ($staff_ids as $sid) {
            $sid = (int)$sid;
            if (!$sid) continue;

            // Kiểm tra đã có phiếu chưa
            $exists = $this->db->where('staff_id', $sid)->where('period_id', $period_id)->count_all_results('tbl_kpi_forms');
            if ($exists > 0) {
                $skipped++;
                continue;
            }

            // Lấy thông tin nhân sự
            $staff = $this->db->where('staffid', $sid)->get('tblstaff')->row_array();
            if (!$staff) continue;

            // Tính Gate 2 (task performance)
            $this->db->select("COUNT(id) as total, SUM(CASE WHEN status=5 THEN 1 ELSE 0 END) as done, SUM(CASE WHEN status=5 AND datefinished IS NOT NULL AND duedate IS NOT NULL AND datefinished<=duedate THEN 1 ELSE 0 END) as ontime, SUM(CASE WHEN duedate IS NOT NULL AND ((status!=5 AND duedate<NOW()) OR (status=5 AND datefinished>duedate)) THEN 1 ELSE 0 END) as overdue");
            $this->db->where("id IN (SELECT taskid FROM tbltask_assigned WHERE staffid=$sid)");
            $this->db->where('startdate >=', $period['date_start'])->where('startdate <=', $period['date_end']);
            $ts = $this->db->get('tbltasks')->row_array();

            $total = (int)($ts['total'] ?? 0);
            $done = (int)($ts['done'] ?? 0);
            $ontime = (int)($ts['ontime'] ?? 0);
            $overdue = (int)($ts['overdue'] ?? 0);
            $comp_rate   = $total > 0 ? $done / $total : 0;
            $ontime_rate = $done > 0 ? $ontime / $done : 0;
            $gate2_raw   = $comp_rate * 40 + $ontime_rate * 30 + 30;
            $p2 = min(60, max(0, $gate2_raw * 60 / 100));

            // Gate 1
            $has_tasks = $this->db->where("id IN (SELECT taskid FROM tbltask_assigned WHERE staffid=$sid)")->where('startdate >=', $period['date_start'])->where('startdate <=', $period['date_end'])->count_all_results('tbltasks') > 0;
            $gate1 = $has_tasks ? 'pass' : 'fail';

            // Gate 3 compliance
            $violations = $this->db->where('period_id', $period_id)->where('staff_id', $sid)->get('tbl_kpi_violations')->result_array();
            $penalty = 0;
            $hard_fail = 0;
            foreach ($violations as $v) {
                $penalty += (float)$v['penalty_score'];
                if ($v['is_hard_fail']) $hard_fail = 1;
            }
            $gate3 = max(0, 20 - $penalty);
            if ($hard_fail) $gate3 = 0;

            // P3
            $contrib_bonus = 0;
            $contribs = $this->db->where('period_id', $period_id)->where('staff_id', $sid)->where('status', 'approved')->get('tbl_kpi_contributions')->result_array();
            foreach ($contribs as $c) $contrib_bonus += (float)$c['bonus_score'];
            $p3 = min(20, $gate4 + $contrib_bonus);
            if ($gate3 < 15) $p3 = min($p3, 10);
            if ($gate3 < 10 || $hard_fail) $p3 = 0;

            $total_score = $p2 + $gate3 + $p3;
            $rating = $this->_calc_rating($total_score);
            if ($gate1 === 'fail' && in_array($rating, ['excellent', 'good'])) $rating = 'passed';
            if ($hard_fail) $rating = 'failed';
            $warning = 'none';
            if ($total_score < 60) $warning = 'red';
            elseif ($total_score < 70) $warning = 'orange';
            elseif ($total_score < 80) $warning = 'yellow';

            $row = [
                'period_id' => $period_id,
                'staff_id' => $sid,
                'evaluation_type' => $eval_type,
                'gate1_result' => $gate1,
                'gate1_note' => $gate1 === 'pass' ? 'Đủ điều kiện' : 'Thiếu dữ liệu đầu vào',
                'total_tasks' => $total,
                'completed_tasks' => $done,
                'ontime_tasks' => $ontime,
                'overdue_tasks' => $overdue,
                'completion_rate' => round($comp_rate * 100, 2),
                'ontime_rate' => round($ontime_rate * 100, 2),
                'p2_performance' => round($p2, 2),
                'gate3_compliance' => round($gate3, 2),
                'gate4_capability' => round($gate4, 2),
                'contribution_bonus' => round($contrib_bonus, 2),
                'p3_final' => round($p3, 2),
                'total_score' => round($total_score, 2),
                'hard_fail' => $hard_fail,
                'warning_level' => $warning,
                'final_rating' => $rating,
                'status' => 'draft',
                'created_by' => get_staff_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('tbl_kpi_forms', $row);
            $created++;
        }

        $msg = "Đã tạo $created phiếu thành công.";
        if ($skipped > 0) $msg .= " ($skipped nhân sự đã có phiếu trong kỳ này, bỏ qua)";
        echo json_encode(['success' => true, 'message' => $msg, 'created' => $created, 'skipped' => $skipped]);
    }

    // === AJAX: Vi phạm ===
    public function save_violation()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        $row = [
            'period_id' => (int)$this->input->post('period_id'),
            'staff_id' => (int)$this->input->post('staff_id'),
            'kpi_form_id' => (int)$this->input->post('kpi_form_id') ?: null,
            'task_id' => (int)$this->input->post('task_id') ?: null,
            'violation_code' => $this->input->post('violation_code', true),
            'violation_name' => $this->input->post('violation_name', true),
            'severity' => $this->input->post('severity', true) ?: 'minor',
            'penalty_score' => (float)$this->input->post('penalty_score'),
            'is_hard_fail' => (int)$this->input->post('is_hard_fail'),
            'description' => $this->input->post('description', true),
        ];
        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_violations', $row);
        } else {
            $row['created_by'] = get_staff_user_id();
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_kpi_violations', $row);
        }
        echo json_encode(['success' => true]);
    }

    public function delete_violation()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $this->db->where('id', (int)$this->input->post('id'))->delete('tbl_kpi_violations');
        echo json_encode(['success' => true]);
    }

    // === AJAX: Đóng góp ===
    public function save_contribution()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        $row = [
            'period_id' => (int)$this->input->post('period_id'),
            'staff_id' => (int)$this->input->post('staff_id'),
            'contribution_type' => $this->input->post('contribution_type', true),
            'title' => $this->input->post('title', true),
            'description' => $this->input->post('description', true),
            'bonus_score' => (float)$this->input->post('bonus_score'),
        ];
        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_contributions', $row);
        } else {
            $row['created_by'] = get_staff_user_id();
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['status'] = 'pending';
            $this->db->insert('tbl_kpi_contributions', $row);
        }
        echo json_encode(['success' => true]);
    }

    public function delete_contribution()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $this->db->where('id', (int)$this->input->post('id'))->delete('tbl_kpi_contributions');
        echo json_encode(['success' => true]);
    }

    // === AJAX: Phê duyệt ===
    public function approve_form()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $approval_id = (int)$this->input->post('approval_id');
        $action = $this->input->post('action'); // approved | rejected
        $note = $this->input->post('note', true);
        $this->db->where('id', $approval_id)->update('tbl_kpi_approvals', [
            'status' => $action,
            'note' => $note,
            'approver_id' => get_staff_user_id(),
            'approved_at' => date('Y-m-d H:i:s')
        ]);
        // Check if all approved
        $approval = $this->db->where('id', $approval_id)->get('tbl_kpi_approvals')->row_array();
        if ($approval && $action === 'approved') {
            $pending = $this->db->where('kpi_form_id', $approval['kpi_form_id'])->where('status', 'waiting')->count_all_results('tbl_kpi_approvals');
            if ($pending == 0) {
                $this->db->where('id', $approval['kpi_form_id'])->update('tbl_kpi_forms', ['status' => 'approved', 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }
        if ($action === 'rejected') {
            $this->db->where('id', $approval['kpi_form_id'])->update('tbl_kpi_forms', ['status' => 'rejected', 'updated_at' => date('Y-m-d H:i:s')]);
        }
        echo json_encode(['success' => true]);
    }

    public function link_audit_to_assessment()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $assessment_id = (int)$this->input->post('assessment_id');
        $audit_id = (int)$this->input->post('audit_id');
        if ($assessment_id > 0 && $audit_id > 0) {
            $this->db->where('id', $assessment_id)->update('tbl_probationary_assessment', ['audit_id' => $audit_id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    // public function approve_probationary()
    // {
    //     if (!$this->input->is_ajax_request()) show_404();
    //     $id = (int)$this->input->post('approval_id');
    //     $action = $this->input->post('action'); // approved | rejected
    //     $note = $this->input->post('note', true);

    //     $p = $this->db->where('id', $id)->get('tbl_probationary_assessment')->row_array();
    //     if (!$p) {
    //         echo json_encode(['success' => false, 'message' => 'Phiếu không tồn tại']);
    //         return;
    //     }

    //     $current_status = (int)$p['approval_status'];
    //     $staff_id = get_staff_user_id();
    //     $date = date('Y-m-d H:i:s');
    //     $update_data = [];

    //     if ($action === 'rejected') {
    //         $update_data['approval_status'] = -1;
    //         // Lưu note nếu cần, tạm thời bỏ qua note
    //     } elseif ($action === 'approved') {
    //         if ($current_status == 0) {
    //             $update_data['approval_status'] = 1;
    //             $update_data['hcns_approve_by'] = $staff_id;
    //             $update_data['hcns_approve_date'] = $date;
    //         } elseif ($current_status == 1) {
    //             $update_data['approval_status'] = 2;
    //             $update_data['ktnb_approve_by'] = $staff_id;
    //             $update_data['ktnb_approve_date'] = $date;
    //         } elseif ($current_status == 2) {
    //             $update_data['approval_status'] = 3;
    //             $update_data['ksrr_approve_by'] = $staff_id;
    //             $update_data['ksrr_approve_date'] = $date;
    //         } elseif ($current_status == 3) {
    //             $update_data['approval_status'] = 4;
    //             $update_data['bod_approve_by'] = $staff_id;
    //             $update_data['bod_approve_date'] = $date;
    //         } else {
    //             echo json_encode(['success' => false, 'message' => 'Phiếu này đã hoàn tất duyệt.']);
    //             return;
    //         }
    //     }

    //     if (!empty($update_data)) {
    //         $this->db->where('id', $id)->update('tbl_probationary_assessment', $update_data);
    //     }

    //     echo json_encode(['success' => true]);
    // }
    public function approve_probationary()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('approval_id');
        $action = $this->input->post('action'); // approved | rejected
        $note = $this->input->post('note', true);

        $p = $this->db->where('id', $id)->get('tbl_probationary_assessment')->row_array();
        if (!$p) {
            echo json_encode(['success' => false, 'message' => 'Phiếu không tồn tại']);
            return;
        }

        $current_status = (int)$p['approval_status'];
        $staff_id = get_staff_user_id();
        $date = date('Y-m-d H:i:s');
        $update_data = [];

        // Kiểm tra quyền duyệt/từ chối theo từng cấp
        if ($current_status == 0) {
            if (!has_permission('DashboardKpi_PhieuDanhGia', '', 'approve_lbc') && !has_permission('DashboardKpi_PheDuyet', '', 'approve_lbc')) {
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền duyệt cấp HCNS']);
                return;
            }
        } elseif ($current_status == 1) {
            if (!has_permission('DashboardKpi_PhieuDanhGia', '', 'approve_ncnxl') && !has_permission('DashboardKpi_PheDuyet', '', 'approve_ncnxl')) {
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền duyệt cấp KTNB']);
                return;
            }
        } elseif ($current_status == 2) {
            if (!has_permission('DashboardKpi_PhieuDanhGia', '', 'approve_gspn') && !has_permission('DashboardKpi_PheDuyet', '', 'approve_gspn')) {
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền duyệt cấp KSRR']);
                return;
            }
        } elseif ($current_status == 3) {
            if (!has_permission('DashboardKpi_PhieuDanhGia', '', 'approve_dg') && !has_permission('DashboardKpi_PheDuyet', '', 'approve_dg')) {
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền duyệt cấp BOD']);
                return;
            }
        }

        if ($action === 'rejected') {
            $update_data['approval_status'] = -1;
            // Bạn có thể lưu thêm lý do từ chối vào cột note nếu bảng có cột này
            // $update_data['reject_note'] = $note;
        } elseif ($action === 'approved') {
            if ($current_status == 0) {
                $update_data['approval_status'] = 1;
                $update_data['hcns_approve_by'] = $staff_id;
                $update_data['hcns_approve_date'] = $date;
            } elseif ($current_status == 1) {
                $update_data['approval_status'] = 2;
                $update_data['ktnb_approve_by'] = $staff_id;
                $update_data['ktnb_approve_date'] = $date;
            } elseif ($current_status == 2) {
                $update_data['approval_status'] = 3;
                $update_data['ksrr_approve_by'] = $staff_id;
                $update_data['ksrr_approve_date'] = $date;
            } elseif ($current_status == 3) {
                $update_data['approval_status'] = 4;
                $update_data['bod_approve_by'] = $staff_id;
                $update_data['bod_approve_date'] = $date;
            } else {
                echo json_encode(['success' => false, 'message' => 'Phiếu này đã hoàn tất duyệt.']);
                return;
            }
        }

        if (!empty($update_data)) {
            $this->db->where('id', $id)->update('tbl_probationary_assessment', $update_data);
        }

        echo json_encode(['success' => true]);
    }


    public function submit_for_approval()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $form_id = (int)$this->input->post('form_id');
        $form = $this->db->where('id', $form_id)->get('tbl_kpi_forms')->row_array();
        if (!$form) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy']);
            return;
        }

        // Tạo route duyệt theo spec
        $steps = [['step' => 1, 'role' => 'leader'], ['step' => 2, 'role' => 'hr']];
        if ($form['warning_level'] !== 'none') $steps[] = ['step' => 3, 'role' => 'internal_audit'];
        if ($form['hard_fail']) {
            $steps[] = ['step' => 4, 'role' => 'risk_control'];
            $steps[] = ['step' => 5, 'role' => 'bod'];
        }

        $this->db->where('kpi_form_id', $form_id)->delete('tbl_kpi_approvals');
        foreach ($steps as $s) {
            $this->db->insert('tbl_kpi_approvals', [
                'kpi_form_id' => $form_id,
                'step_order' => $s['step'],
                'approver_role' => $s['role'],
                'status' => 'waiting',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        $this->db->where('id', $form_id)->update('tbl_kpi_forms', ['status' => 'waiting_approval', 'updated_at' => date('Y-m-d H:i:s')]);
        $this->_write_log($form_id, 'submit_approval');
        echo json_encode(['success' => true]);
    }

    // === AJAX CRUD: KPI Import ===
    public function save_kpi_import()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = (int)$this->input->post('id');
        $row = [
            'muc_tieu_kpi'       => $this->input->post('muc_tieu_kpi', true),
            'ma_phong_ban'       => $this->input->post('ma_phong_ban', true),
            'ten_phong_ban'      => $this->input->post('ten_phong_ban', true),
            'ma_vi_tri'          => $this->input->post('ma_vi_tri', true),
            'chuc_vu'            => $this->input->post('chuc_vu', true),
            'ma_cong_viec'       => $this->input->post('ma_cong_viec', true) ?: null,
            'ten_cong_viec'      => $this->input->post('ten_cong_viec', true) ?: null,
            'ma_vi_pham'         => $this->input->post('ma_vi_pham', true) ?: null,
            'loai_vi_pham'       => $this->input->post('loai_vi_pham', true) ?: null,
            'loai_kpi'           => $this->input->post('loai_kpi', true) ?: 'P2',
            'diem_chuan'         => (float)$this->input->post('diem_chuan'),
            'diem_sau_xu_ly'     => (float)$this->input->post('diem_sau_xu_ly'),
            'kpi_tien_chuan'     => (float)$this->input->post('kpi_tien_chuan'),
            'kpi_tien_thuc_nhan' => (float)$this->input->post('kpi_tien_thuc_nhan'),
            'ty_le_huong_kpi'    => (float)$this->input->post('ty_le_huong_kpi'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        if (empty($row['muc_tieu_kpi']) || empty($row['ma_phong_ban']) || empty($row['ma_vi_tri'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ các trường bắt buộc.']);
            return;
        }

        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_import', $row);
        } else {
            $row['created_by'] = get_staff_user_id();
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_kpi_import', $row);
            $id = $this->db->insert_id();
        }

        echo json_encode(['success' => true, 'id' => $id]);
    }

    public function delete_kpi_import()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
            return;
        }
        $this->db->where('id', $id)->delete('tbl_kpi_import');
        echo json_encode(['success' => true]);
    }

    public function import_from_db()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $room_codes  = $this->input->post('room_codes');   // array of ma_phong_ban
        $role_codes  = $this->input->post('role_codes');   // array of ma_vi_tri
        $task_codes  = $this->input->post('task_codes');   // array or empty
        $viol_codes  = $this->input->post('viol_codes');   // array or empty
        $muc_tieu    = $this->input->post('muc_tieu_kpi', true);
        $loai_kpi    = in_array($this->input->post('loai_kpi'), ['P2', 'P3']) ? $this->input->post('loai_kpi') : 'P2';
        $diem_chuan  = (float)$this->input->post('diem_chuan') ?: 100;
        $diem_sau    = (float)$this->input->post('diem_sau_xu_ly') ?: 100;
        $tien_chuan  = (float)$this->input->post('kpi_tien_chuan');
        $tien_thuc   = (float)$this->input->post('kpi_tien_thuc_nhan');
        $ty_le       = (float)$this->input->post('ty_le_huong_kpi') ?: 1;

        if (empty($room_codes) || empty($role_codes)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn ít nhất 1 phòng ban và 1 vị trí.']);
            return;
        }

        // Fetch room details
        $rooms = [];
        $this->db->where_in('code', $room_codes)->where('status', 1);
        foreach ($this->db->get('tbl_room')->result_array() as $r) {
            $rooms[$r['code']] = $r['name'];
        }

        // Fetch role details
        $roles = [];
        $this->db->where_in('code_role', $role_codes);
        foreach ($this->db->get('tblroles')->result_array() as $r) {
            $roles[$r['code_role']] = $r['name'];
        }

        // Fetch task details (optional)
        $tasks = [null]; // default: no task
        if (!empty($task_codes) && is_array($task_codes)) {
            $tasks = [];
            $this->db->where_in('code', $task_codes)->where('hide', 0);
            foreach ($this->db->get('tblcategory_tasks')->result_array() as $t) {
                $tasks[] = ['code' => $t['code'], 'name' => $t['content']];
            }
            if (empty($tasks)) $tasks = [null];
        }

        // Fetch violation details (optional)
        $viols = [null]; // default: no violation
        if (!empty($viol_codes) && is_array($viol_codes)) {
            $viols = [];
            $this->db->where_in('code', $viol_codes)->where('active', 1);
            foreach ($this->db->get('tbl_violation_group')->result_array() as $v) {
                $viols[] = ['code' => $v['code'], 'name' => $v['name']];
            }
            if (empty($viols)) $viols = [null];
        }

        $now        = date('Y-m-d H:i:s');
        $created_by = get_staff_user_id();
        $inserted   = 0;

        foreach ($rooms as $r_code => $r_name) {
            foreach ($roles as $role_code => $role_name) {
                foreach ($tasks as $task) {
                    foreach ($viols as $viol) {
                        $this->db->insert('tbl_kpi_import', [
                            'muc_tieu_kpi'       => $muc_tieu ?: ($r_name . ' - ' . $role_name),
                            'ma_phong_ban'       => $r_code,
                            'ten_phong_ban'      => $r_name,
                            'ma_vi_tri'          => $role_code,
                            'chuc_vu'            => $role_name,
                            'ma_cong_viec'       => $task ? $task['code'] : null,
                            'ten_cong_viec'      => $task ? $task['name'] : null,
                            'ma_vi_pham'         => $viol ? $viol['code'] : null,
                            'loai_vi_pham'       => $viol ? $viol['name'] : null,
                            'loai_kpi'           => $loai_kpi,
                            'diem_chuan'         => $diem_chuan,
                            'diem_sau_xu_ly'     => $diem_sau,
                            'kpi_tien_chuan'     => $tien_chuan,
                            'kpi_tien_thuc_nhan' => $tien_thuc,
                            'ty_le_huong_kpi'    => $ty_le,
                            'created_by'         => $created_by,
                            'created_at'         => $now,
                            'updated_at'         => $now,
                        ]);
                        $inserted++;
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'inserted' => $inserted,
            'message' => "Đã tạo thành công <strong>$inserted</strong> bản ghi KPI."
        ]);
    }

    // === AJAX Select2 cho KPI Import ===
    public function ajax_search_rooms()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $q = $this->input->get('q', true);
        if ($q) {
            $this->db->group_start()->like('code', $q)->or_like('name', $q)->group_end();
        }
        $this->db->where('status', 1);
        $this->db->limit(30);
        $rooms = $this->db->get('tbl_room')->result_array();

        $results = [];
        foreach ($rooms as $r) {
            $results[] = [
                'id' => $r['code'],
                'text' => $r['code'] . ' - ' . $r['name'],
                'name' => $r['name']
            ];
        }
        echo json_encode(['results' => $results]);
    }

    public function ajax_search_roles()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $q = $this->input->get('q', true);
        if ($q) {
            $this->db->group_start()->like('code_role', $q)->or_like('name', $q)->group_end();
        }
        $this->db->limit(30);
        $roles = $this->db->get('tblroles')->result_array();

        $results = [];
        foreach ($roles as $r) {
            $results[] = [
                'id' => $r['code_role'],
                'text' => $r['code_role'] . ' - ' . $r['name'],
                'name' => $r['name']
            ];
        }
        echo json_encode(['results' => $results]);
    }

    public function ajax_search_tasks()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $q = $this->input->get('q', true);
        if ($q) {
            $this->db->group_start()->like('code', $q)->or_like('content', $q)->group_end();
        }
        $this->db->where('hide', 0);
        $this->db->limit(30);
        $tasks = $this->db->get('tblcategory_tasks')->result_array();

        $results = [];
        foreach ($tasks as $t) {
            $results[] = [
                'id' => $t['code'],
                'text' => $t['code'] . ' - ' . $t['content'],
                'name' => $t['content']
            ];
        }
        echo json_encode(['results' => $results]);
    }

    public function ajax_search_violations()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $q = $this->input->get('q', true);
        if ($q) {
            $this->db->group_start()->like('code', $q)->or_like('name', $q)->group_end();
        }
        $this->db->where('active', 1);
        $this->db->limit(30);
        $violations = $this->db->get('tbl_violation_group')->result_array();

        $results = [];
        foreach ($violations as $v) {
            $results[] = [
                'id' => $v['code'],
                'text' => $v['code'] . ' - ' . $v['name'],
                'name' => $v['name']
            ];
        }
        echo json_encode(['results' => $results]);
    }

    public function ajax_search_staff()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $q = $this->input->get('q', true);
        $this->db->select("staffid as id, CONCAT(firstname,' ',lastname) as text, email");
        if ($q) {
            $this->db->group_start()
                ->like('firstname', $q)->or_like('lastname', $q)
                ->or_like('email', $q)->or_like('code', $q)
                ->group_end();
        }
        $this->db->where('active', 1)->limit(30);
        $rows = $this->db->get('tblstaff')->result_array();
        $results = [];
        foreach ($rows as $r) {
            $results[] = ['id' => $r['id'], 'text' => $r['text'], 'email' => $r['email']];
        }
        echo json_encode(['results' => $results]);
    }

    public function ajax_search_periods()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $q = $this->input->get('q', true);
        if ($q) $this->db->like('name', $q);
        $this->db->order_by('id', 'DESC')->limit(30);
        $rows = $this->db->get('tbl_kpi_periods')->result_array();
        $results = [];
        foreach ($rows as $r) {
            $results[] = ['id' => $r['id'], 'text' => $r['name'] . ' (' . $r['period_type'] . ')', 'name' => $r['name']];
        }
        echo json_encode(['results' => $results]);
    }

    public function get_form_detail()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->get('id');
        if (!$id) {
            echo json_encode(['success' => false]);
            return;
        }

        $this->db->select("f.*, CONCAT(s.firstname,' ',s.lastname) as ho_ten, s.email, p.name as period_name, p.period_type, p.date_start, p.date_end");
        $this->db->from('tbl_kpi_forms f');
        $this->db->join('tblstaff s', 's.staffid = f.staff_id', 'left');
        $this->db->join('tbl_kpi_periods p', 'p.id = f.period_id', 'left');
        $this->db->where('f.id', $id);
        $form = $this->db->get()->row_array();
        if (!$form) {
            echo json_encode(['success' => false]);
            return;
        }

        $form['violations']   = $this->db->where('kpi_form_id', $id)->get('tbl_kpi_violations')->result_array();
        $form['contributions'] = $this->db->where('kpi_form_id', $id)->get('tbl_kpi_contributions')->result_array();
        $this->db->select("a.*, CONCAT(ap.firstname,' ',ap.lastname) as approver_name");
        $this->db->from('tbl_kpi_approvals a');
        $this->db->join('tblstaff ap', 'ap.staffid=a.approver_id', 'left');
        $this->db->where('a.kpi_form_id', $id)->order_by('a.step_order', 'ASC');
        $form['approvals'] = $this->db->get()->result_array();

        echo json_encode(['success' => true, 'data' => $form]);
    }

    // === Private helpers ===
    private function _get_staff_list()
    {
        $this->db->select("s.staffid as id, CONCAT(s.firstname,' ',s.lastname) as ho_ten");
        $this->db->from('tblstaff s')->where('s.active', 1)->order_by('s.firstname', 'ASC');
        return $this->db->get()->result_array();
    }

    private function _get_stats()
    {
        $s = [];
        $now = date('Y-m-d');
        $month = date('m');
        $year  = date('Y');

        $date_condition = "YEAR({{FIELD}}) = {$year} AND MONTH({{FIELD}}) = {$month}";
        $month_old = $month - 1;
        $year_old  = $year;
        if ($month_old == 0) {
            $month_old = 12;
            $year_old  = $year - 1;
        }
        $date_condition_old = "YEAR({{FIELD}}) = {$year_old} AND MONTH({{FIELD}}) = {$month_old}";

        $cond_start     = str_replace('{{FIELD}}', 'startdate',  $date_condition);
        $cond_start_old = str_replace('{{FIELD}}', 'startdate',  $date_condition_old);
        $cond_date      = str_replace('{{FIELD}}', 'date',       $date_condition);
        $cond_date_old  = str_replace('{{FIELD}}', 'date',       $date_condition_old);
        $cond_dateadded     = str_replace('{{FIELD}}', 'dateadded', $date_condition);
        $cond_dateadded_old = str_replace('{{FIELD}}', 'dateadded', $date_condition_old);
        $cond_audit     = str_replace('{{FIELD}}', 'audit_date', $date_condition);
        $cond_audit_old = str_replace('{{FIELD}}', 'audit_date', $date_condition_old);

        $s['filter_mode'] = 'month';
        $s['filter_label'] = 'Tháng ' . date('m/Y');
        $s['filter_label_old'] = 'Tháng trước';

        // 1. Tasks completed process
        $s['tasks_completed_process'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start} AND EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id) AND NOT EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.finished = 0)")->row()->total;
        $s['tasks_completed_process_old'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start_old} AND EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id) AND NOT EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.finished = 0)")->row()->total;

        // 2. Production report
        $s['production_report'] = $this->db->query("SELECT COUNT(*) as total FROM tblproduction_report WHERE {$cond_date}")->row()->total;
        $s['production_report_old'] = $this->db->query("SELECT COUNT(*) as total FROM tblproduction_report WHERE {$cond_date_old}")->row()->total;
        $s['production_report_violate_old'] = $this->db->query("SELECT COUNT(*) as total FROM tblproduction_report WHERE violate = 1 AND {$cond_date_old}")->row()->total;

        // 3. Tasks in progress
        $s['tasks_in_progress'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start} AND status != 5")->row()->total;
        $s['tasks_in_progress_old'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start_old} AND status != 5")->row()->total;

        // 4. Tasks incomplete process
        $s['tasks_incomplete_process'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start} AND EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.finished = 0)")->row()->total;
        $s['tasks_incomplete_process_old'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start_old} AND EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.finished = 0)")->row()->total;

        // 5. Tasks no check
        $s['tasks_no_check'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start} AND EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id) AND NOT EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.finished = 1)")->row()->total;
        $s['tasks_no_check_old'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start_old} AND EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id) AND NOT EXISTS (SELECT 1 FROM tbltask_checklist_items WHERE tbltask_checklist_items.taskid = tbltasks.id AND tbltask_checklist_items.finished = 1)")->row()->total;

        // 6. Tasks overdue
        $s['tasks_overdue'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start} AND status != 5 AND duedate IS NOT NULL AND duedate < NOW()")->row()->total;
        $s['tasks_overdue_old'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE {$cond_start_old} AND status != 5 AND duedate IS NOT NULL AND duedate < NOW()")->row()->total;

        // 7. Types
        $s['p3_type2_count'] = $this->db->query("SELECT COUNT(*) as total FROM tblproduction_report WHERE violate = 1 AND {$cond_date}")->row()->total;
        $s['p3_type1_count'] = $this->db->query("SELECT COUNT(*) as total FROM tblproduction_report WHERE id != 0 AND {$cond_date} AND EXISTS (SELECT 1 FROM tbl_process_production_report WHERE tbl_process_production_report.production_report_id = tblproduction_report.id AND tbl_process_production_report.staff_process = 0)")->row()->total;
        $s['p3_type3_count'] = $this->db->query("SELECT COUNT(*) as total FROM tbltasks WHERE status != 5 AND {$cond_dateadded}")->row()->total;
        $s['p3_type4_count'] = $this->db->query("SELECT COUNT(*) as total FROM tbl_audit WHERE {$cond_audit} AND EXISTS (SELECT 1 FROM tbl_audit_checklist WHERE tbl_audit_checklist.audit_id = tbl_audit.id AND tbl_audit_checklist.status = 'no')")->row()->total;

        // 8. Top 5
        $cond_date_pr  = str_replace('{{FIELD}}', 'pr.date',       $date_condition);
        $cond_date_t   = str_replace('{{FIELD}}', 't.dateadded',   $date_condition);
        $cond_audit_a  = str_replace('{{FIELD}}', 'a.audit_date',  $date_condition);

        $s['top5_type2'] = $this->db->query("SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total FROM tblproduction_report pr LEFT JOIN tblstaff s ON s.staffid = pr.staff_responsible WHERE pr.violate = 1 AND {$cond_date_pr} GROUP BY pr.staff_responsible ORDER BY total DESC LIMIT 5")->result_array();
        $s['top5_type1'] = $this->db->query("SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total FROM tblproduction_report pr LEFT JOIN tblstaff s ON s.staffid = pr.staff_responsible WHERE pr.id != 0 AND {$cond_date_pr} AND EXISTS (SELECT 1 FROM tbl_process_production_report WHERE tbl_process_production_report.production_report_id = pr.id AND tbl_process_production_report.staff_process = 0) GROUP BY pr.staff_responsible ORDER BY total DESC LIMIT 5")->result_array();
        $s['top5_type3'] = $this->db->query("SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total FROM tbltasks t LEFT JOIN tblstaff s ON s.staffid = t.addedfrom WHERE t.status != 5 AND {$cond_date_t} GROUP BY t.addedfrom ORDER BY total DESC LIMIT 5")->result_array();
        $s['top5_type4'] = $this->db->query("SELECT CONCAT(s.firstname, ' ', s.lastname) AS staff_name, COUNT(*) AS total FROM tbl_audit a JOIN tbl_room ON tbl_room.id = a.dept_id JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid LEFT JOIN tblstaff s ON s.staffid = tblstaff_departments.staffid WHERE {$cond_audit_a} AND EXISTS (SELECT 1 FROM tbl_audit_checklist WHERE tbl_audit_checklist.audit_id = a.id AND tbl_audit_checklist.status = 'no') GROUP BY tblstaff_departments.staffid ORDER BY total DESC LIMIT 5")->result_array();

        // 9. Eval list
        $current_year = date('Y');
        $eval_rows = $this->db->query("
            SELECT e.id, e.code, e.staff_id, e.point, e.rating, e.rating_list, e.date, CONCAT(s.firstname, ' ', s.lastname) AS staff_name, r.name AS name_role, '' AS code_role_level,
            (SELECT COUNT(*) FROM tbl_probationary_assessment e2 WHERE e2.staff_id = e.staff_id AND e2.type = 2 AND YEAR(e2.date) = {$current_year} AND (e.rating_list IS NULL OR e.rating_list = 0) AND e2.date <= e.date) AS phieu_so
            FROM tbl_probationary_assessment e LEFT JOIN tblstaff s ON s.staffid = e.staff_id LEFT JOIN tblroles r ON r.roleid = e.role_id
            WHERE e.type = 2 AND check_salary = 0 AND YEAR(e.date) = {$current_year} AND (e.rating_list IS NULL OR e.rating_list = 0) ORDER BY e.staff_id ASC, e.date ASC
        ")->result_array();
        $milestone_map = [1 => 3, 2 => 6, 3 => 9, 4 => 12];
        foreach ($eval_rows as &$row) {
            $so = (int)$row['phieu_so'];
            $row['milestone_month'] = $milestone_map[$so] ?? ($so * 3);
        }
        unset($row);
        $s['eval_list'] = $eval_rows;

        // 10. Big risk
        $cond_eval_date = str_replace('{{FIELD}}', 'e.date', $date_condition);
        $s['big_risk_list'] = $this->db->query("SELECT e.id, e.code, e.staff_id, e.big_risk, e.date, CONCAT(s.firstname, ' ', s.lastname) AS staff_name, r.name AS name_role, '' AS code_role_level FROM tbl_probationary_assessment e LEFT JOIN tblstaff s ON s.staffid = e.staff_id LEFT JOIN tblroles r ON r.roleid = e.role_id WHERE e.type = 2 AND e.big_risk > 0 AND (e.rating_list IS NULL OR e.rating_list = 0) AND {$cond_eval_date} ORDER BY e.big_risk DESC, e.date ASC")->result_array();

        // 11. KPI targets 2026
        $kpi_year = 2026;
        $client_rows = $this->db->query("SELECT t.id, t.SoComplain, t.DonHangCo, (SELECT COUNT(*) FROM tbl_orders WHERE customer_id = t.id_client AND YEAR(date) = {$kpi_year} AND status = 'approved' AND type_orders != 13) as DonHangCoTT, (SELECT COUNT(*) FROM tblproduction_report JOIN tbl_orders ON tbl_orders.id = tblproduction_report.id_orders WHERE tbl_orders.customer_id = t.id_client AND YEAR(tblproduction_report.date) = {$kpi_year}) as SoComplainTT FROM tbl_kpi_targets_clients t")->result_array();
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
        $s['kpi_client_status'] = $kpi_client_status;

        $supplier_rows = $this->db->query("SELECT t.id, (SELECT COUNT(*) FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id AND YEAR(date) = {$kpi_year} AND delivery_date IS NOT NULL AND EXISTS (SELECT 1 FROM tblimport WHERE tblimport.id_order = tblpurchase_order.id AND DATE(tblimport.date) <= DATE(tblpurchase_order.delivery_date))) as GiaoHangDungHanTT, (SELECT COUNT(*) FROM tbl_suggest_evaluate WHERE object_id = tblsuppliers.id AND object_type = 'supplier' AND YEAR(date) = {$kpi_year} AND status = 1) as SoLanComplainTT FROM tbl_kpi_targets_supplier t JOIN tblsuppliers ON tblsuppliers.id = t.id_supplier")->result_array();
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
        $s['kpi_supplier_status'] = $kpi_supplier_status;

        $budget_rows = $this->db->query("SELECT t.id, t.ngan_sach_duoc_cap, ((SELECT COALESCE(SUM(op1.total),0) FROM tblother_payslips op1 WHERE op1.id_costs = t.cost_id AND YEAR(op1.date) = {$kpi_year}) + (SELECT COALESCE(SUM(opc.total),0) FROM tblother_payslip_cost opc INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id WHERE opc.cost_id = t.cost_id AND YEAR(op2.date) = {$kpi_year})) AS chi_phi_thuc_te FROM tbl_department_budget t")->result_array();
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
        $s['kpi_budget_status'] = $kpi_budget_status;

        return $s;
    }

    private function _get_cong_viec_data()
    {
        $data = [];
        $staff_id = $this->input->get('staff_id');
        $status_filter = $this->input->get('status');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['staff_list'] = $this->_get_staff_list();

        $this->db->select("t.id, t.name as task_name, t.status, t.startdate, t.duedate, t.datefinished, t.priority, t.addedfrom");
        $this->db->from('tbltasks t');
        if (!empty($staff_id)) $this->db->where("t.id IN (SELECT taskid FROM tbltask_assigned WHERE staffid=" . (int)$staff_id . ")");
        if (!empty($status_filter) && is_numeric($status_filter)) $this->db->where('t.status', (int)$status_filter);
        if (!empty($date_from)) $this->db->where('t.startdate >=', $date_from);
        if (!empty($date_to)) $this->db->where('t.startdate <=', $date_to);
        // $this->db->order_by('t.id', 'DESC');
        // $tasks = $this->db->get()->result_array();
        $tasks = []; // Tasks are now loaded via AJAX

        $task_ids = array_column($tasks, 'id');
        $checklist_by_task = [];
        if (!empty($task_ids)) {
            $this->db->select('ci.*, COUNT(p.id) as count_process, COUNT(CASE WHEN p.isCheck IS NULL AND p.isCheckNot IS NULL THEN 1 END) as count_not_process');
            $this->db->from('tbltask_checklist_items ci');
            $this->db->join('tbl_tasks_inspection_criteria_process as p', 'p.process_id=ci.process_id AND p.tasks=ci.taskid', 'left');
            $this->db->where_in('ci.taskid', $task_ids)->group_by('ci.id')->order_by('ci.list_order', 'ASC');
            foreach ($this->db->get()->result_array() as $c) $checklist_by_task[$c['taskid']][] = $c;
        }

        // Prefetch assignees
        $assignees_by_task = [];
        if (!empty($task_ids)) {
            $this->db->select("ta.taskid, CONCAT(s.firstname,' ',s.lastname) as fullname");
            $this->db->from('tbltask_assigned ta')->join('tblstaff s', 's.staffid=ta.staffid', 'left');
            $this->db->where_in('ta.taskid', $task_ids);
            foreach ($this->db->get()->result_array() as $a) $assignees_by_task[$a['taskid']][] = $a['fullname'];
        }

        foreach ($tasks as &$t) {
            $t['checklist'] = isset($checklist_by_task[$t['id']]) ? $checklist_by_task[$t['id']] : [];
            $t['assignee_names'] = isset($assignees_by_task[$t['id']]) ? implode(', ', $assignees_by_task[$t['id']]) : '';
            $total_steps = count($t['checklist']);
            $done_steps = 0;
            foreach ($t['checklist'] as $cl) {
                if (!empty($cl['finished'])) $done_steps++;
            }
            $t['total_steps'] = $total_steps;
            $t['done_steps'] = $done_steps;
            $t['process_rate'] = $total_steps > 0 ? round(($done_steps / $total_steps) * 100, 1) : 0;
            $t['is_overdue'] = false;
            if (!empty($t['duedate'])) {
                if ($t['status'] != 5 && $t['duedate'] < date('Y-m-d H:i:s')) $t['is_overdue'] = true;
                if ($t['status'] == 5 && !empty($t['datefinished']) && $t['datefinished'] > $t['duedate']) $t['is_overdue'] = true;
            }
        }
        unset($t);
        $data['tasks'] = $tasks;
        $data['filters'] = ['staff_id' => $staff_id, 'status' => $status_filter, 'date_from' => $date_from, 'date_to' => $date_to];
        return $data;
    }

    private function _calc_rating($score)
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 80) return 'good';
        if ($score >= 70) return 'passed';
        if ($score >= 60) return 'need_monitoring';
        return 'failed';
    }

    private function _apply_cong_viec_filters($db, $staff_id, $status_filter, $date_from, $date_to, $year, $month, $ky, $q)
    {
        if (!empty($staff_id)) $db->where("t.id IN (SELECT taskid FROM tbltask_assigned WHERE staffid=" . (int)$staff_id . ")");
        if (!empty($status_filter) && is_numeric($status_filter)) $db->where('t.status', (int)$status_filter);
        if (!empty($date_from)) $db->where('t.startdate >=', $date_from);
        if (!empty($date_to)) $db->where('t.startdate <=', $date_to);

        if (!empty($year)) {
            $db->where('YEAR(t.startdate)', $year);
            $is_quarter = (!empty($ky) && strpos($ky, 'tháng') !== false);
            $is_all = empty($ky);
            if (!empty($month) && !$is_quarter && !$is_all) {
                $db->where('MONTH(t.startdate)', $month);
            }
        }
        if (!empty($ky)) {
            $is_quarter = strpos($ky, 'tháng') !== false;
            if (!$is_quarter && !empty($month)) {
                if ($ky === '1') $db->where('DAY(t.startdate) <=', 7);
                elseif ($ky === '2') $db->where('DAY(t.startdate) >', 7)->where('DAY(t.startdate) <=', 14);
                elseif ($ky === '3') $db->where('DAY(t.startdate) >', 14)->where('DAY(t.startdate) <=', 21);
                elseif ($ky === '4') $db->where('DAY(t.startdate) >', 21);
            } else {
                if ($ky === '3 tháng') {
                    $db->where('MONTH(t.startdate) >=', 1)->where('MONTH(t.startdate) <=', 3);
                } elseif ($ky === '6 tháng') {
                    $db->where('MONTH(t.startdate) >=', 4)->where('MONTH(t.startdate) <=', 6);
                } elseif ($ky === '9 tháng') {
                    $db->where('MONTH(t.startdate) >=', 7)->where('MONTH(t.startdate) <=', 9);
                } elseif ($ky === '12 tháng') {
                    $db->where('MONTH(t.startdate) >=', 10)->where('MONTH(t.startdate) <=', 12);
                }
            }
        }
        if (!empty($q)) {
            $db->group_start();
            $db->like('t.name', $q);
            $db->or_like('t.id', $q);
            $db->or_where("t.id IN (SELECT taskid FROM tbltask_assigned ta JOIN tblstaff s ON s.staffid=ta.staffid WHERE s.firstname LIKE '%" . $db->escape_like_str($q) . "%' OR s.lastname LIKE '%" . $db->escape_like_str($q) . "%')", NULL, FALSE);
            $db->group_end();
        }
    }

    public function ajax_cong_viec()
    {
        $staff_id = $this->input->post('staff_id');
        $status_filter = $this->input->post('status');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $ky = $this->input->post('ky');
        $q = $this->input->post('q');
        $page = max(1, (int)$this->input->post('page'));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // 1. Get stats
        $this->db->select("COUNT(t.id) as total, 
                           SUM(CASE WHEN t.status = 5 THEN 1 ELSE 0 END) as done, 
                           SUM(CASE WHEN (t.status != 5 AND t.duedate < NOW()) OR (t.status = 5 AND t.datefinished > t.duedate) THEN 1 ELSE 0 END) as overdue,
                           SUM(CASE WHEN (SELECT COUNT(id) FROM tbltask_checklist_items WHERE taskid = t.id) > 0 THEN 1 ELSE 0 END) as has_process", false);
        $this->db->from('tbltasks t');
        $this->_apply_cong_viec_filters($this->db, $staff_id, $status_filter, $date_from, $date_to, $year, $month, $ky, $q);
        $stats = $this->db->get()->row_array();

        // 2. Get data
        $this->db->select("t.id, t.name as task_name, t.status, t.startdate, t.duedate, t.datefinished, t.priority, t.addedfrom, '' as task_code, '' as task_category_name"); // Using empty strings for code/category if not joined
        $this->db->from('tbltasks t');
        $this->_apply_cong_viec_filters($this->db, $staff_id, $status_filter, $date_from, $date_to, $year, $month, $ky, $q);
        $this->db->order_by('t.id', 'DESC');
        $this->db->limit($limit, $offset);
        $tasks = $this->db->get()->result_array();

        // 3. Prefetch nested data
        $task_ids = array_column($tasks, 'id');
        $checklist_by_task = [];
        if (!empty($task_ids)) {
            $this->db->select('ci.*, COUNT(p.id) as count_process, COUNT(CASE WHEN p.isCheck IS NULL AND p.isCheckNot IS NULL THEN 1 END) as count_not_process');
            $this->db->from('tbltask_checklist_items ci');
            $this->db->join('tbl_tasks_inspection_criteria_process as p', 'p.process_id=ci.process_id AND p.tasks=ci.taskid', 'left');
            $this->db->where_in('ci.taskid', $task_ids)->group_by('ci.id')->order_by('ci.list_order', 'ASC');
            foreach ($this->db->get()->result_array() as $c) $checklist_by_task[$c['taskid']][] = $c;
        }

        $assignees_by_task = [];
        if (!empty($task_ids)) {
            $this->db->select("ta.taskid, CONCAT(s.firstname,' ',s.lastname) as fullname");
            $this->db->from('tbltask_assigned ta')->join('tblstaff s', 's.staffid=ta.staffid', 'left');
            $this->db->where_in('ta.taskid', $task_ids);
            foreach ($this->db->get()->result_array() as $a) $assignees_by_task[$a['taskid']][] = $a['fullname'];
        }

        foreach ($tasks as &$t) {
            $t['checklist'] = isset($checklist_by_task[$t['id']]) ? $checklist_by_task[$t['id']] : [];
            $t['assignee_names'] = isset($assignees_by_task[$t['id']]) ? implode(', ', $assignees_by_task[$t['id']]) : '';
            $t['assignees'] = $t['assignee_names']; // To match original variable name
            $total_steps = count($t['checklist']);
            $done_steps = 0;
            foreach ($t['checklist'] as $cl) {
                if (!empty($cl['finished'])) $done_steps++;
            }
            $t['total_steps'] = $total_steps;
            $t['done_steps'] = $done_steps;
            $t['process_rate'] = $total_steps > 0 ? round(($done_steps / $total_steps) * 100, 1) : 0;
            $t['is_overdue'] = false;
            if (!empty($t['duedate'])) {
                if ($t['status'] != 5 && $t['duedate'] < date('Y-m-d H:i:s')) $t['is_overdue'] = true;
                if ($t['status'] == 5 && !empty($t['datefinished']) && $t['datefinished'] > $t['duedate']) $t['is_overdue'] = true;
            }
        }
        unset($t);

        // 4. Render HTML
        $html = $this->load->view('admin/dashboard_kpi/tabs/cong_viec_rows', ['tasks' => $tasks], true);

        echo json_encode([
            'html' => $html,
            'stats' => [
                'total' => (int)($stats['total'] ?? 0),
                'done' => (int)($stats['done'] ?? 0),
                'overdue' => (int)($stats['overdue'] ?? 0),
                'has_process' => (int)($stats['has_process'] ?? 0)
            ],
            'page' => $page,
            'limit' => $limit,
            'has_more' => ($offset + $limit) < (int)($stats['total'] ?? 0)
        ]);
        exit;
    }

    private function _apply_pr_filters($db, $date_from, $date_to, $type_report, $room_id, $recommend_group, $year, $month, $ky, $q)
    {
        if (!empty($date_from)) $db->where('pr.date >=', $date_from . ' 00:00:00');
        if (!empty($date_to)) $db->where('pr.date <=', $date_to . ' 23:59:59');
        if (!empty($type_report)) $db->where('pr.type_report', $type_report);
        if (!empty($room_id)) $db->where('pr.id_departments', $room_id);
        if (!empty($recommend_group)) $db->where('pr.recommended_list_group_id', $recommend_group);

        if (!empty($year)) {
            $db->where('YEAR(pr.date)', $year);
            $is_quarter = (!empty($ky) && strpos($ky, 'tháng') !== false);
            $is_all = empty($ky);
            if (!empty($month) && !$is_quarter && !$is_all) {
                $db->where('MONTH(pr.date)', $month);
            }
        }
        if (!empty($ky)) {
            $is_quarter = strpos($ky, 'tháng') !== false;
            if (!$is_quarter && !empty($month)) {
                if ($ky === '1') $db->where('DAY(pr.date) <=', 7);
                elseif ($ky === '2') $db->where('DAY(pr.date) >', 7)->where('DAY(pr.date) <=', 14);
                elseif ($ky === '3') $db->where('DAY(pr.date) >', 14)->where('DAY(pr.date) <=', 21);
                elseif ($ky === '4') $db->where('DAY(pr.date) >', 21);
            } else {
                if ($ky === '3 tháng') {
                    $db->where('MONTH(pr.date) >=', 1)->where('MONTH(pr.date) <=', 3);
                } elseif ($ky === '6 tháng') {
                    $db->where('MONTH(pr.date) >=', 4)->where('MONTH(pr.date) <=', 6);
                } elseif ($ky === '9 tháng') {
                    $db->where('MONTH(pr.date) >=', 7)->where('MONTH(pr.date) <=', 9);
                } elseif ($ky === '12 tháng') {
                    $db->where('MONTH(pr.date) >=', 10)->where('MONTH(pr.date) <=', 12);
                }
            }
        }
        if (!empty($q)) {
            $db->group_start();
            $db->like('pr.reference_no', $q);
            $db->or_like('pr.name_report', $q);
            $db->or_where("pr.id_trouble IN (SELECT id FROM tbltrouble WHERE name LIKE '%" . $db->escape_like_str($q) . "%')", NULL, FALSE);
            $db->or_where("pr.violation_group IN (SELECT id FROM tbl_violation_group WHERE name LIKE '%" . $db->escape_like_str($q) . "%')", NULL, FALSE);
            $db->group_end();
        }
    }

    public function ajax_production_report()
    {
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $type_report = $this->input->post('type_report');
        $room_id = $this->input->post('room_id');
        $recommend_group = $this->input->post('recommend_group');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $ky = $this->input->post('ky');
        $q = $this->input->post('q');

        $page = max(1, (int)$this->input->post('page'));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // 1. Get stats
        $this->db->select("COUNT(pr.id) as total, 
                           SUM(CASE WHEN pr.type_report = 1 THEN 1 ELSE 0 END) as count_kph,
                           SUM(CASE WHEN pr.type_report = 4 THEN 1 ELSE 0 END) as count_vp,
                           SUM(CASE WHEN pr.type_report = 3 THEN 1 ELSE 0 END) as count_ct,
                           SUM(CASE WHEN (SELECT COUNT(id) FROM tbl_process_production_report WHERE production_report_id = pr.id AND staff_process IS NULL) = 0 
                                     AND (SELECT COUNT(id) FROM tbl_process_production_report WHERE production_report_id = pr.id) > 0 THEN 1 ELSE 0 END) as fully_approved", false);
        $this->db->from('tblproduction_report pr');
        $this->_apply_pr_filters($this->db, $date_from, $date_to, $type_report, $room_id, $recommend_group, $year, $month, $ky, $q);
        $stats = $this->db->get()->row_array();

        // 2. Get data
        $this->db->select("
            pr.id, pr.reference_no, pr.name_report, pr.date, pr.type_report, pr.quantity, pr.described, pr.time_of_recording,
            pr.create_by, pr.staff_responsible, pr.staff_evaluate, pr.violate, pr.trouble_violation_point,
            pr.recommended_list_group_id, pr.recommended_list_id, pr.id_departments, pr.id_trouble,
            pr.category_tasks, pr.violation_group, pr.big_risk,
            branch.name as branch_name, room.name as room_name, trouble.code as trouble_code, trouble.name as trouble_name,
            grp.name as group_name, relate.name as relate_name, vg.code as vg_code, vg.name as vg_name,
            ct.code as ct_code, ct.content as ct_name, tvp.name as violation_level_name,
            CONCAT(COALESCE(sc.firstname,''),' ',COALESCE(sc.lastname,'')) as creator_name,
            CONCAT(COALESCE(sr.firstname,''),' ',COALESCE(sr.lastname,'')) as responsible_name,
            CONCAT(COALESCE(se.firstname,''),' ',COALESCE(se.lastname,'')) as evaluator_name
        ", false);
        $this->db->from('tblproduction_report pr');
        $this->db->join('tblbranch branch', 'branch.id = pr.id_branch', 'left');
        $this->db->join('tbl_room room', 'room.id = pr.id_departments', 'left');
        $this->db->join('tbltrouble trouble', 'trouble.id = pr.id_trouble', 'left');
        $this->db->join('tbl_relate grp', 'grp.id = pr.recommended_list_group_id', 'left');
        $this->db->join('tbl_relate relate', 'relate.id = pr.recommended_list_id', 'left');
        $this->db->join('tbl_violation_group vg', 'vg.id = pr.violation_group', 'left');
        $this->db->join('tblcategory_tasks ct', 'ct.id = pr.category_tasks', 'left');
        $this->db->join('tbltrouble_violation_point tvp', 'tvp.id = pr.trouble_violation_point_id', 'left');
        $this->db->join('tblstaff sc', 'sc.staffid = pr.create_by', 'left');
        $this->db->join('tblstaff sr', 'sr.staffid = pr.staff_responsible', 'left');
        $this->db->join('tblstaff se', 'se.staffid = pr.staff_evaluate', 'left');

        $this->_apply_pr_filters($this->db, $date_from, $date_to, $type_report, $room_id, $recommend_group, $year, $month, $ky, $q);
        $this->db->order_by('pr.date', 'DESC');
        $this->db->limit($limit, $offset);
        $reports = $this->db->get()->result_array();

        // 3. Prefetch nested data
        foreach ($reports as &$r) {
            $this->db->select('ppr.*, ppr.process_id as id');
            $this->db->from('tbl_process_production_report ppr');
            $this->db->where('ppr.production_report_id', $r['id']);
            $this->db->order_by('ppr.process_id', 'asc');
            $r['processes'] = $this->db->get()->result_array();
            $r['total_steps'] = count($r['processes']);
            $r['done_steps'] = 0;
            foreach ($r['processes'] as $p) {
                if (!empty($p['staff_process'])) $r['done_steps']++;
            }
            $r['process_rate'] = $r['total_steps'] > 0 ? round(($r['done_steps'] / $r['total_steps']) * 100) : 0;
        }
        unset($r);

        // Required data for view
        $type_labels = [
            1 => ['name' => 'KPH', 'color' => '#16a34a', 'bg' => '#dcfce7'],
            2 => ['name' => 'Bất thường', 'color' => '#d97706', 'bg' => '#fef3c7'],
            3 => ['name' => 'Cải tiến', 'color' => '#0284c7', 'bg' => '#e0f2fe'],
            4 => ['name' => 'Vi phạm', 'color' => '#dc2626', 'bg' => '#fee2e2'],
        ];

        // 4. Render HTML
        $html = $this->load->view('admin/dashboard_kpi/tabs/production_report_rows', ['reports' => $reports, 'type_labels' => $type_labels], true);

        echo json_encode([
            'html' => $html,
            'stats' => [
                'total' => (int)($stats['total'] ?? 0),
                'kph' => (int)($stats['count_kph'] ?? 0),
                'vp' => (int)($stats['count_vp'] ?? 0),
                'ct' => (int)($stats['count_ct'] ?? 0),
                'approved' => (int)($stats['fully_approved'] ?? 0)
            ],
            'page' => $page,
            'limit' => $limit,
            'has_more' => ($offset + $limit) < (int)($stats['total'] ?? 0)
        ]);
        exit;
    }

    private function _write_log($form_id, $action, $old = null, $new = null)
    {
        $this->db->insert('tbl_kpi_logs', [
            'kpi_form_id' => $form_id,
            'action' => $action,
            'old_data' => $old ? json_encode($old) : null,
            'new_data' => $new ? json_encode($new) : null,
            'created_by' => get_staff_user_id(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // === AJAX: Tiêu chí KPI theo phòng ban (self-contained) ===
    public function ajax_criteria_department()
    {
        $dept_id = (int)$this->input->get('department_id');

        $this->db->select('kpi.*,
            parent.code as code_parent, parent.name as name_parent, parent.id as id_parent,
            parent_new.code as code_parent_new, parent_new.name as name_parent_new, parent_new.id as id_parent_new,
            d.code as code_department, d.name as name_department,
            r.code_role, r.name_position,
            ct.code as name_tasks,
            cl.zcode as name_clients,
            sp.code as name_suppliers,
            tp.name as name_tasks_process,
            m.code as name_machines');
        $this->db->from('tbl_kpi_list_criteria_department kpi');
        $this->db->where('kpi.department_id', $dept_id);
        $this->db->where('kpi.violate IS NOT NULL', null, false);
        $this->db->where("TRIM(kpi.violate) != ''", null, false);
        $this->db->join('tbldepartments d', 'd.departmentid = kpi.department_id', 'inner');
        $this->db->join('tbl_kpi_list_criteria_department parent', 'parent.id = kpi.parent_id', 'left');
        $this->db->join('tbl_kpi_list_criteria_department parent_new', 'parent_new.id = parent.parent_id', 'left');
        $this->db->join('tblroles r', 'r.roleid = kpi.role_id', 'left');
        $this->db->join('tblcategory_tasks ct', 'ct.id = kpi.id_tasks', 'left');
        $this->db->join('tblclients cl', 'cl.userid = kpi.id_clients', 'left');
        $this->db->join('tblsuppliers sp', 'sp.id = kpi.id_suppliers', 'left');
        $this->db->join('tbl_machines m', 'm.id = kpi.id_machines', 'left');
        $this->db->join('tblcategory_tasks_process_child tp', 'tp.id = kpi.id_task_procedure', 'left');

        $rows = $this->db->get()->result_array();

        echo json_encode(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    // === AJAX: KPI mục tiêu khách hàng ===
    public function ajax_kpi_targets_clients()
    {
        $year = $this->input->get('year');
        if (empty($year)) $year = date('Y');

        $tableGroupClient = "(SELECT GROUP_CONCAT(tblcustomers_groups.name) as list_name_group, tblcustomer_groups.customer_id
            FROM tblcustomer_groups JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid
            GROUP BY tblcustomer_groups.customer_id)";

        $this->db->select("
            tbl_kpi_targets_clients.id,
            tblclients.zcode, tblclients.company,
            g.list_name_group,
            tbl_kpi_targets_clients.SoBaoGia, tbl_kpi_targets_clients.BaoGiaDaDuyet, tbl_kpi_targets_clients.BaoGiaChuaDuyet,
            tbl_kpi_targets_clients.DonHangCo, tbl_kpi_targets_clients.DonHangKhongCo,
            tbl_kpi_targets_clients.PTMCoDon, tbl_kpi_targets_clients.PTMKhongDon,
            tbl_kpi_targets_clients.SoComplain, tbl_kpi_targets_clients.MauLan1, tbl_kpi_targets_clients.MauLan2,
            tbl_kpi_targets_clients.DiemCong, tbl_kpi_targets_clients.DiemTru, tbl_kpi_targets_clients.TongDiem,
            (SELECT COUNT(*) FROM tbl_quotes WHERE customer_id = tblclients.userid AND YEAR(date) = '$year') as SoBaoGiaTT,
            (SELECT COUNT(*) FROM tbl_quotes WHERE customer_id = tblclients.userid AND YEAR(date) = '$year' AND status='approved') as BaoGiaDaDuyetTT,
            (SELECT COUNT(*) FROM tbl_quotes WHERE customer_id = tblclients.userid AND YEAR(date) = '$year' AND status='un_approved') as BaoGiaChuaDuyetTT,
            (SELECT COUNT(*) FROM tbl_orders WHERE customer_id = tblclients.userid AND YEAR(date) = '$year' AND status='approved' AND type_orders!=13) as DonHangCoTT,
            (SELECT COUNT(*) FROM tbl_orders WHERE customer_id = tblclients.userid AND YEAR(date) = '$year' AND status='un_approved' AND type_orders!=13) as DonHangKhongCoTT,
            (SELECT COUNT(*) FROM tbl_orders WHERE customer_id = tblclients.userid AND YEAR(date) = '$year' AND type_orders=13 AND status='approved') as PTMCoDonTT,
            (SELECT COUNT(*) FROM tbl_orders WHERE customer_id = tblclients.userid AND YEAR(date) = '$year' AND type_orders=13 AND status='un_approved') as PTMKhongDonTT,
            (SELECT COUNT(*) FROM tblproduction_report JOIN tbl_orders ON tbl_orders.id = tblproduction_report.id_orders WHERE tbl_orders.customer_id = tblclients.userid AND YEAR(tblproduction_report.date) = '$year') as SoComplainTT
        ", false);
        $this->db->from('tbl_kpi_targets_clients');
        $this->db->join('tblclients', 'tblclients.userid = tbl_kpi_targets_clients.id_client');
        $this->db->join($tableGroupClient . ' g', 'g.customer_id = tbl_kpi_targets_clients.id_client', 'left');

        $rows = $this->db->get()->result_array();
        echo json_encode(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    // === AJAX: KPI mục tiêu Nhà cung cấp ===
    public function ajax_kpi_targets_supplier()
    {
        $year = $this->input->get('year');
        if (empty($year)) $year = date('Y');

        $this->db->select("
            tbl_kpi_targets_supplier.*,
            tblsuppliers.code as code_supplier, tblsuppliers.company,
            tblsuppliers_groups.name as list_name_group,
            (SELECT COUNT(*) FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id AND YEAR(date) = '$year') as SoDonHangTT,
            (SELECT COUNT(*) FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id AND YEAR(date) = '$year' AND delivery_date IS NOT NULL AND EXISTS (SELECT 1 FROM tblimport WHERE tblimport.id_order = tblpurchase_order.id AND DATE(tblimport.date) <= DATE(tblpurchase_order.delivery_date))) as GiaoHangDungHanTT,
            (SELECT COUNT(*) FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id AND YEAR(date) = '$year' AND delivery_date IS NOT NULL AND EXISTS (SELECT 1 FROM tblimport WHERE tblimport.id_order = tblpurchase_order.id AND DATE(tblimport.date) > DATE(tblpurchase_order.delivery_date))) as GiaoHangTreTT,
            (SELECT COUNT(*) FROM tblreturn_suppliers WHERE suppliers_id = tblsuppliers.id AND YEAR(date) = '$year') as SoLanLoiChatLuongTT,
            (SELECT COUNT(*) FROM tbl_suggest_evaluate WHERE object_id = tblsuppliers.id AND object_type = 'supplier' AND YEAR(date) = '$year' AND status = 1) as SoLanComplainTT
        ", false);
        $this->db->from('tbl_kpi_targets_supplier');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_kpi_targets_supplier.id_supplier');
        $this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in', 'left');

        $rows = $this->db->get()->result_array();
        echo json_encode(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    // === AJAX: KPI Thiết bị công đoạn ===
    public function ajax_kpi_equipment_stage()
    {
        $this->db->select('*');
        $this->db->from('tbl_kpi_equipment_stage');
        $rows = $this->db->get()->result_array();
        echo json_encode(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    // === AJAX: Ngân sách phòng ban ===
    public function ajax_department_budget()
    {
        $currentYear = date('Y');
        $department_id = $this->input->get('department_id');

        // Subqueries copied from Department_budget controller for consistency
        $subSrc1 = "(SELECT COALESCE(SUM(op1.total), 0) FROM tblother_payslips op1 WHERE op1.id_costs = tbl_department_budget.cost_id AND YEAR(op1.date) = $currentYear)";
        $subSrc2 = "(SELECT COALESCE(SUM(opc.total), 0) FROM tblother_payslip_cost opc INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id WHERE opc.cost_id = tbl_department_budget.cost_id AND YEAR(op2.date) = $currentYear)";

        $this->db->select("
            tbl_department_budget.*,
            tbldepartments.code as ma_phong_ban, tbldepartments.name as ten_phong_ban,
            tblcosts.code as ma_loai_chi_phi, tblcosts.name as ten_loai_chi_phi,
            ($subSrc1 + $subSrc2) as chi_phi_thuc_te
        ", false);
        $this->db->from('tbl_department_budget');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_department_budget.department_id', 'left');
        $this->db->join('tblcosts', 'tblcosts.id = tbl_department_budget.cost_id', 'left');

        if (!empty($department_id)) {
            $this->db->where('tbl_department_budget.department_id', $department_id);
        }

        $rows = $this->db->get()->result_array();
        echo json_encode(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    }

    // === Xử lý xóa dữ liệu ===
    public function delete_kpi_entry($tab, $id)
    {
        $tables = [
            'import_phong_ban'  => 'tbl_kpi_list_criteria_department',
            'import_khach_hang' => 'tbl_kpi_targets_clients',
            'import_ncc'        => 'tbl_kpi_targets_supplier',
            'import_thiet_bi'   => 'tbl_kpi_equipment_stage',
            'department_budget' => 'tbl_department_budget',
        ];

        $table = $tables[$tab] ?? null;
        if (!$table) {
            echo json_encode(['success' => false, 'message' => 'Tab không hợp lệ.']);
            return;
        }

        // Xử lý riêng cho từng bảng nếu cần cascade
        if ($tab == 'import_phong_ban') {
            $this->db->where('kpi_list_criteria_department_id', $id);
            $this->db->delete('tbl_kpi_list_criteria_department_violate');
        }

        $this->db->where('id', $id);
        if ($this->db->delete($table)) {
            echo json_encode(['success' => true, 'message' => 'Đã xóa dữ liệu thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống không thể xóa.']);
        }
    }

    // === Xử lý lưu dữ liệu Import từ SheetJS ===
    public function import_category_data()
    {
        $tab  = $this->input->post('tab');
        $rows = json_decode($this->input->post('rows'), true);
        if (!$rows) {
            echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
            return;
        }

        $importConfigs = [
            'import_phong_ban'  => [
                'table' => 'tbl_kpi_list_criteria_department',
                'mapping' => [
                    'Mã mục tiêu cha' => 'parent_id', // Cần xử lý tìm ID từ mã
                    'Mã mục tiêu' => 'code',
                    'Tên mục tiêu' => 'evaluation_criteria',
                    'KPI phòng ban' => 'evaluation_criteria',
                    'Trọng số' => 'weight'
                ]
            ],
            'import_khach_hang' => ['table' => 'tbl_kpi_targets_clients'],
            'import_ncc'        => ['table' => 'tbl_kpi_targets_supplier'],
            'import_thiet_bi'   => ['table' => 'tbl_kpi_equipment_stage'],
            'department_budget' => ['table' => 'tbl_department_budget'],
        ];

        $config = $importConfigs[$tab] ?? null;
        if (!$config) {
            echo json_encode(['success' => false, 'message' => 'Cấu hình tab không hợp lệ']);
            return;
        }

        $table = $config['table'];
        $count = 0;

        foreach ($rows as $row) {
            // Clean data
            $data = [];
            foreach ($row as $key => $val) {
                // Thử map nếu có cấu hình mapping, nếu không giữ nguyên key
                $dbKey = $config['mapping'][$key] ?? $key;
                $data[$dbKey] = $val;
            }

            // Xử lý riêng cho từng bảng nếu cần (ví dụ tìm ID từ mã)
            if ($tab == 'import_phong_ban') {
                // Ở đây cần logic phức tạp hơn như trong Kpi.php, tạm thời insert đơn giản
                if (empty($data['evaluation_criteria'])) continue;
                $data['department_id'] = $this->input->post('department_id') ?: 0;
            }

            if ($this->db->insert($table, $data)) {
                $count++;
            }
        }

        echo json_encode(['success' => true, 'message' => "Đã nhập thành công $count dòng"]);
    }

    public function download_import_template($tab)
    {
        $templates = [
            'import_phong_ban'  => 'uploads/import_dt/mau_import_muc_tieu_kpi_phong_ban_vs1.xlsx',
            'import_khach_hang' => 'uploads/import_c/mau_import_kpi_khach_hang.xlsx',
            'import_ncc'        => 'uploads/import_c/mau_import_kpi_nha_cung_cap.xlsx',
            'import_thiet_bi'   => 'uploads/import_c/import_kpi_thietbi_congdoan.xlsx',
            'department_budget' => 'uploads/import_dt/KPI_NganSach_PhongBan.xlsx',
        ];

        $file = $templates[$tab] ?? null;
        if ($file && file_exists(FCPATH . $file)) {
            $this->load->helper('download');
            force_download(FCPATH . $file, NULL);
        } else {
            set_alert('danger', 'Không tìm thấy file mẫu gốc.');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    // === Load view import cho Modal ===
    public function load_import_view($active_tab)
    {
        $importConfigs = [
            'import_phong_ban'  => ['title' => 'Phòng ban',    'table' => 'tbldepartments', 'fields' => ['name' => 'Tên phòng ban', 'short_name' => 'Tên viết tắt']],
            'import_khach_hang' => ['title' => 'Khách hàng',   'table' => 'tblclients',      'fields' => ['company' => 'Tên công ty', 'zcode' => 'Mã KH']],
            'import_ncc'        => ['title' => 'Nhà cung cấp', 'table' => 'tbl_vendors',     'fields' => ['company' => 'Tên NCC', 'code_supplier' => 'Mã NCC']],
            'import_thiet_bi'   => ['title' => 'Thiết bị',     'table' => 'tbl_assets',      'fields' => ['equipment_name' => 'Tên máy', 'equipment_code' => 'Mã máy']],
            'department_budget' => ['title' => 'Ngân sách PB', 'table' => 'tbl_department_budget', 'fields' => ['ngan_sach_duoc_cap' => 'Ngân sách']],
        ];
        $data['import_config'] = $importConfigs[$active_tab] ?? null;
        $data['active_tab'] = $active_tab;
        $this->load->view('admin/dashboard_kpi/tabs/import_danh_muc', $data);
    }

    /**
     * Lấy dữ liệu phiếu báo cáo vi phạm cho tab production_report
     */
    private function _get_production_report_data()
    {
        $data = [];
        $filters = $this->input->get();
        $data['filters'] = $filters;

        // Staff list cho bộ lọc
        $data['staff_list'] = $this->_get_staff_list();

        // Danh sách nhóm recommended (loại phiếu)
        $this->load->model('recommended_list_model');
        $data['recommended_list'] = $this->recommended_list_model->getRecommendedListParent([0], 1);

        // Danh sách phòng/bộ phận
        $data['room_list'] = $this->db->select('id, name, code')->order_by('name', 'ASC')->get('tbl_room')->result_array();

        // Query dữ liệu production report
        $this->db->select("
            pr.id,
            pr.reference_no,
            pr.name_report,
            pr.date,
            pr.type_report,
            pr.quantity,
            pr.described,
            pr.time_of_recording,
            pr.create_by,
            pr.staff_responsible,
            pr.staff_evaluate,
            pr.violate,
            pr.trouble_violation_point,
            pr.recommended_list_group_id,
            pr.recommended_list_id,
            pr.id_departments,
            pr.id_trouble,
            pr.category_tasks,
            pr.violation_group,
            pr.big_risk,
            branch.name as branch_name,
            room.name as room_name,
            trouble.code as trouble_code,
            trouble.name as trouble_name,
            grp.name as group_name,
            relate.name as relate_name,
            vg.code as vg_code,
            vg.name as vg_name,
            ct.code as ct_code,
            ct.content as ct_name,
            tvp.name as violation_level_name,
            CONCAT(COALESCE(sc.firstname,''),' ',COALESCE(sc.lastname,'')) as creator_name,
            CONCAT(COALESCE(sr.firstname,''),' ',COALESCE(sr.lastname,'')) as responsible_name,
            CONCAT(COALESCE(se.firstname,''),' ',COALESCE(se.lastname,'')) as evaluator_name
        ", false);
        $this->db->from('tblproduction_report pr');
        $this->db->join('tblbranch branch', 'branch.id = pr.id_branch', 'left');
        $this->db->join('tbl_room room', 'room.id = pr.id_departments', 'left');
        $this->db->join('tbltrouble trouble', 'trouble.id = pr.id_trouble', 'left');
        $this->db->join('tbl_relate grp', 'grp.id = pr.recommended_list_group_id', 'left');
        $this->db->join('tbl_relate relate', 'relate.id = pr.recommended_list_id', 'left');
        $this->db->join('tbl_violation_group vg', 'vg.id = pr.violation_group', 'left');
        $this->db->join('tblcategory_tasks ct', 'ct.id = pr.category_tasks', 'left');
        $this->db->join('tbltrouble_violation_point tvp', 'tvp.id = pr.trouble_violation_point_id', 'left');
        $this->db->join('tblstaff sc', 'sc.staffid = pr.create_by', 'left');
        $this->db->join('tblstaff sr', 'sr.staffid = pr.staff_responsible', 'left');
        $this->db->join('tblstaff se', 'se.staffid = pr.staff_evaluate', 'left');

        // Filters
        if (!empty($filters['date_from'])) {
            $this->db->where('pr.date >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('pr.date <=', $filters['date_to'] . ' 23:59:59');
        }
        if (!empty($filters['type_report'])) {
            $this->db->where('pr.type_report', $filters['type_report']);
        }
        if (!empty($filters['room_id'])) {
            $this->db->where('pr.id_departments', $filters['room_id']);
        }
        if (!empty($filters['recommend_group'])) {
            $this->db->where('pr.recommended_list_group_id', $filters['recommend_group']);
        }

        // $this->db->order_by('pr.date', 'DESC');
        // $this->db->limit(200);
        // $data['reports'] = $this->db->get()->result_array();
        $data['reports'] = [];

        return $data;
    }

    public function get_staff_by_department()
    {
        $department_search = $this->input->post('department_search');
        if (!empty($department_search)) {
            $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as firstname');
            $this->db->from('tblstaff');
            $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
            $this->db->where('tblstaff_departments.departmentid', $department_search);
            $this->db->where('active', 1);
            $staffs = $this->db->get()->result_array();
        } else {
            $this->db->select('tblstaff.staffid as staffid, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as firstname');
            $this->db->from('tblstaff');
            $this->db->where('active', 1);
            $staffs = $this->db->get()->result_array();
        }
        echo json_encode(['staffs' => $staffs]);
    }

    public function view_kpi_evaluation()
    {
        $month = $this->input->post('filter_month');
        $year = $this->input->post('year');
        $staff = $this->input->post('staff');
        $department_search = $this->input->post('department_search');
        $precious = $this->input->post('precious');
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        $whereDateTask = '';
        $whereDateOld = '';
        $whereDateTaskOld = '';
        $whereDateDecision = '';
        $whereDateAudit = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") = "' . $month_year . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") = "' . $month_year . '"';
        }

        $tb_tamp_audit = "(
            SELECT
                tblstaff_departments.staffid as staff_id,
                COUNT(tbl_audit.id) as total_audit
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            $whereDateAudit 
            GROUP BY tblstaff_departments.staffid
        ) tb_tamp_audit";

        $tb_tamp_task_process = "(
            SELECT 
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task_process";

        $tb_tamp_task = "(
            SELECT 
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task";

        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";

        $tb_tamp_vuot = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as vuot
            FROM tblproduction_report
            WHERE tblproduction_report.type_report = 2 AND kpi_list_criteria_department_id != 0
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_vuot";

        $tb_tamp_report = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report";

        $tb_tamp_report_process = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report_process";

        $tb_tamp_old = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDateOld
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_old";

        $tb_tamp_report_old = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDateOld
            GROUP BY staff_responsible
        ) tb_tamp_report_old";

        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            COALESCE(tb_tamp_task.total_task,0) as total_task,
            COALESCE(tb_tamp_report_old.count_bckph,0) as count_bckph_old,
            COALESCE(tb_tamp_report.count_bckph,0) as count_bckph,
            COALESCE(tb_tamp_old.violate,0) as violate_old,
            COALESCE(tb_tamp.violate,0) as violate,
            COALESCE(tb_tamp_vuot.vuot,0) as vuot,
            COALESCE(tb_tamp_report.violation_p1,0) as violation_p1,
            COALESCE(tb_tamp_report.violation_p2,0) as violation_p2,
            COALESCE(tb_tamp_report.violation_p3,0) as violation_p3,
            tb_tamp.kpi_list_criteria_department_id,
            tb_tamp_vuot.kpi_list_criteria_department_id as kpi_list_criteria_department_id_vuot,
            "" as rating,
            COALESCE(tb_tamp_report.weight_p2,0) as weight_p2,
            COALESCE(tb_tamp_report.weight_p3,0) as weight_p3,
            COALESCE(tb_tamp_task_process.total_task,0) as total_task_process,
            COALESCE(tb_tamp_report_process.count_bckph,0) as count_bckph_process,
            COALESCE(tb_tamp_audit.total_audit,0) as total_audit
        ');
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if (!empty($department_search)) {
            $this->db->where('EXISTS (
                SELECT 1
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = ' . $department_search . '
            )');
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $this->db->join($tb_tamp_vuot, 'tblstaff.staffid = tb_tamp_vuot.staff_id', 'left');
        $this->db->join($tb_tamp_report, 'tblstaff.staffid = tb_tamp_report.staff_id', 'left');
        $this->db->join($tb_tamp_old, 'tblstaff.staffid = tb_tamp_old.staff_id', 'left');
        $this->db->join($tb_tamp_report_old, 'tblstaff.staffid = tb_tamp_report_old.staff_id', 'left');
        $this->db->join($tb_tamp_task, 'tblstaff.staffid = tb_tamp_task.staff_id', 'left');
        $this->db->join($tb_tamp_task_process, 'tblstaff.staffid = tb_tamp_task_process.staff_id', 'left');
        $this->db->join($tb_tamp_report_process, 'tblstaff.staffid = tb_tamp_report_process.staff_id', 'left');
        $this->db->join($tb_tamp_audit, 'tblstaff.staffid = tb_tamp_audit.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();

        $dtCriteriaDepartmentViolateNew = [];
        $dtCriteriaDepartmentViolateNewVuot = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;


                $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
                $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
                if (!empty($kpi_list_criteria_department_id_vuot)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id_vuot);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolateVuot = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolateVuot)) {
                    $dtCriteriaDepartmentViolateVuot = array_reduce($dtCriteriaDepartmentViolateVuot, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNewVuot[$staffid] = $dtCriteriaDepartmentViolateVuot;
            }
        }
        $html = '';
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $pointMax = 100;
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                $countedArray = [];
                if (!empty($kpi_list_criteria_department_id[0])) {
                    $countedArray = array_count_values($kpi_list_criteria_department_id);
                }
                $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
                $point = 0;
                if (!empty($countedArray)) {
                    foreach ($countedArray as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point'];
                            }
                        }
                        $maxViolations = !empty($violations) ? max($violations) : 0;
                        if ($v <= $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                if ($violationsToPoint[$v] == -1) {
                                    $point += $violationsToPoint[$v - 1];
                                } else {
                                    $point += $violationsToPoint[$v];
                                }
                            }
                        } else {
                            if (!empty($violationsToPoint)) $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }

                //vuot
                $pointNew = 0;
                $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
                $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
                $countedArrayVuot = [];
                if (!empty($kpi_list_criteria_department_id_vuot[0])) {
                    $countedArrayVuot = array_count_values($kpi_list_criteria_department_id_vuot);
                }
                $dtCriteriaDepartmentViolateVuot = !empty($dtCriteriaDepartmentViolateNewVuot[$value['staffid']]) ? $dtCriteriaDepartmentViolateNewVuot[$value['staffid']] : [];

                if (!empty($countedArrayVuot)) {
                    foreach ($countedArrayVuot as $k => $v) {
                        $dtData = !empty($dtCriteriaDepartmentViolateVuot[$k]) ? $dtCriteriaDepartmentViolateVuot[$k] : [];
                        $violations = array_column($dtData, 'violations');
                        $violationsToPoint = [];
                        if (!empty($dtData)) {
                            foreach ($dtData as $item) {
                                $violationsToPoint[$item['violations']] = $item['point_new'];
                            }
                        }
                        $maxViolations = !empty($violations) ? max($violations) : 0;
                        if ($v < $maxViolations) {
                            if (array_key_exists($v, $violationsToPoint)) {
                                $pointNew += $violationsToPoint[$v];
                            }
                        } else {
                            if (!empty($violationsToPoint)) $pointNew += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }

                $pointCurrent = $pointMax - $point + $pointNew;
                if ($pointCurrent <= 0) {
                    $pointCurrent = 1;
                }
                if ($pointCurrent > 100) {
                    $pointCurrent = 100;
                }

                $this->db->from('tbl_decision_bonus_discipline');
                $this->db->where('tbl_decision_bonus_discipline.type_quota_bonus_discipline_id', 1);
                $this->db->where('tbl_decision_bonus_discipline.object_id', $value['staffid']);
                $this->db->where('tbl_decision_bonus_discipline.object_type = "staff" ' . $whereDateDecision . '');
                $dtCountDecision = $this->db->count_all_results();

                $dtRating = ratingKpiDepartment($pointCurrent);

                if (!empty($dtRating)) {
                    if ($dtRating[0]['id'] == 1 && empty($dtCountDecision)) {
                        $dtRating = ratingKpiDepartment(-1, 2);
                    }
                }

                $bonus = !empty($dtRating) ? $dtRating[0]['bonus'] : [];
                $discipline = !empty($dtRating) ? $dtRating[0]['discipline'] : [];
                $htmlBouns = '';
                $htmlDiscipline = '';
                if (!empty($bonus)) {
                    foreach ($bonus as $k => $v) {
                        $htmlBouns .= '<div>' . $v['name'] . '</div>';
                    }
                }
                if (!empty($discipline)) {
                    foreach ($discipline as $k => $v) {
                        $htmlDiscipline .= '<div>' . $v['name'] . '</div>';
                    }
                }
                $avatar = '<a href="' . admin_url('staff/profile/' . $value['staffid']) . '" class="shrink-0">' . staff_profile_image(
                    $value['staffid'],
                    [
                        'staff-profile-image-small',
                        'rounded-full',
                        'w-8',
                        'h-8',
                        'object-cover'
                    ]
                ) . '</a>';
                $check_p3 = 'Không';
                if ($value['total_task_process'] == 0 && $value['count_bckph_process'] == 0 && $value['violate'] == 0 && $value['total_audit'] == 0) {
                    $check_p3 = 'Có';
                }
                $url_eval = admin_url("DashboardKpi/modal_kpi_evaluation_staff/{$value['staffid']}/$month/$year/$precious");
                $url_violate = admin_url("DashboardKpi/modal_detail_production_report/{$value['staffid']}/$month/$year/$precious/violate");
                $url_vuot = admin_url("DashboardKpi/modal_detail_production_report/{$value['staffid']}/$month/$year/$precious/vuot");
                $url_p2 = admin_url("DashboardKpi/modal_detail_p2/{$value['staffid']}/$month/$year/$precious");
                $url_p3 = admin_url("DashboardKpi/modal_detail_p3/{$value['staffid']}/$month/$year/$precious");

                $html .= '<tr>
                     <td class="text-center stt_all border-r border-slate-200">' . (++$key) . '</td>
                     <td class="border-r border-slate-200">
                        <div class="flex items-center gap-2">
                            ' . $avatar . '
                            <a href="javascript:void(0)" onclick="openDetailModal(\'Chi tiết KPI - ' . $value['fullname'] . '\', \'' . $url_eval . '\')" class="font-medium text-slate-800 hover:text-blue-600 transition-colors line-clamp-2" title="' . $value['fullname'] . '">' . $value['fullname'] . '</a>
                        </div>
                     </td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['total_task'] != 0 ? $value['total_task'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['count_bckph_old'] != 0 ? $value['count_bckph_old'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['count_bckph'] != 0 ? $value['count_bckph'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['violate_old'] != 0 ? $value['violate_old'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px"><a href="javascript:void(0)" onclick="openDetailModal(\'Phiếu vi phạm - ' . $value['fullname'] . '\', \'' . $url_violate . '\')" class="hover:underline">' . $value['violate'] . '</a>
                        <input type="hidden" class="violate_input" value="' . $value['violate'] . '">
                     </td>
                      <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px"><a href="javascript:void(0)" onclick="openDetailModal(\'Phiếu vượt - ' . $value['fullname'] . '\', \'' . $url_vuot . '\')" class="hover:underline">' . $value['vuot'] . '</a>
                        <input type="hidden" class="vuot_input" value="' . $value['vuot'] . '">
                     </td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['violation_p1'] != 0 ? $value['violation_p1'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['violation_p2'] != 0 ? $value['violation_p2'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . ($value['violation_p3'] != 0 ? $value['violation_p3'] : '-') . '</td>
                     <td class="text-center border-r border-slate-200" style="font-weight: bold;font-size: 15px">' . $pointCurrent . '</td>
                     <td class="text-center border-r border-slate-200" style="background-color: ' . (!empty($dtRating) ? $dtRating[0]['color'] : '') . '">' . (!empty($dtRating) ? $dtRating[0]['title'] : '') . '</td>
                     <td class="text-left border-r border-slate-200">
                          <div style="">
                            <div class="content-text text-xs text-slate-600">
                                <div>Thưởng : ' . $htmlBouns . '</div>
                                <div>Kỷ luật : ' . $htmlDiscipline . '</div>
                            </div>
                            <a onclick="toggleContent(this)" class="text-blue-500 cursor-pointer hover:underline text-[10px]">Xem thêm</a>
                          </div>
                    </td>
                    <td class="text-center border-r border-slate-200" style="background-color: #3d69ef;color: white"><a href="javascript:void(0)" onclick="openDetailModal(\'Chi tiết % P2 OKR - ' . $value['fullname'] . '\', \'' . $url_p2 . '\')" class="hover:underline" style="color:white">' . ((100 - $value['weight_p2']) > 0 ? (100 - $value['weight_p2']) . ' %' : '-') . '</a></td>
                    <td class="text-center" style="background-color: #047857;"><a href="javascript:void(0)" onclick="openDetailModal(\'Chi tiết Mở P3 - ' . $value['fullname'] . '\', \'' . $url_p3 . '\')" class="hover:underline" style="color:white">' . $check_p3 . '</a></td>
                </tr>';
            }
        }
        $data['html'] = $html;
        echo json_encode($data);
    }

    public function export_excel_kpi_evaluation()
    {
        ini_set('memory_limit', '3500M');
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        // Lay tham so loc
        $month = $this->input->post('filter_month');
        $year = $this->input->post('year');
        $staff = $this->input->post('staff');
        $department_search = $this->input->post('department_search');
        $precious = $this->input->post('precious');

        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;

        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }

        $whereDate = '';
        $whereDateTask = '';
        $whereDateOld = '';
        $whereDateTaskOld = '';
        $whereDateDecision = '';
        $whereDateAudit = '';

        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year_start . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") = "' . $month_year . '"';
            $whereDateOld = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") < "' . $month_year . '"';
            $whereDateTaskOld = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") < "' . $month_year . '"';
            $whereDateDecision = 'AND DATE_FORMAT(tbl_decision_bonus_discipline.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") = "' . $month_year . '"';
        }

        // Subquery: so lan audit co checklist khong dat
        $tb_tamp_audit = "(
            SELECT
                tblstaff_departments.staffid as staff_id,
                COUNT(tbl_audit.id) as total_audit
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            $whereDateAudit
            GROUP BY tblstaff_departments.staffid
        ) tb_tamp_audit";

        // Subquery: tong so cong viec (chua hoan thanh)
        $tb_tamp_task_process = "(
            SELECT
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task_process";

        // Subquery: tong so cong viec (tat ca)
        $tb_tamp_task = "(
            SELECT
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 $whereDateTask
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task";

        // Subquery: so phieu vi pham (hien tai)
        $tb_tamp = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp";

        // Subquery: so phieu vuot (hien tai)
        $tb_tamp_vuot = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as vuot
            FROM tblproduction_report
            WHERE tblproduction_report.type_report = 2 AND kpi_list_criteria_department_id != 0
            $whereDate
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_vuot";

        // Subquery: thong ke phieu theo loai vi pham
        $tb_tamp_report = "(
            SELECT
               tblproduction_report.staff_responsible as staff_id,
               COUNT(tblproduction_report.id) as count_bckph,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p1,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p2,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report";

        // Subquery: phieu chua xu ly
        $tb_tamp_report_process = "(
            SELECT
               tblproduction_report.staff_responsible as staff_id,
               COUNT(tblproduction_report.id) as count_bckph
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1
                FROM tbl_process_production_report
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )  $whereDate
            GROUP BY staff_responsible
        ) tb_tamp_report_process";

        // Subquery: so phieu vi pham (ky truoc)
        $tb_tamp_old = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDateOld
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_old";

        // Subquery: thong ke phieu ky truoc
        $tb_tamp_report_old = "(
            SELECT
               tblproduction_report.staff_responsible as staff_id,
               COUNT(tblproduction_report.id) as count_bckph,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
               SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0  $whereDateOld
            GROUP BY staff_responsible
        ) tb_tamp_report_old";

        // Truy van chinh lay du lieu nhan vien
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            tblroles.name as position_name,
            GROUP_CONCAT(DISTINCT tbldepartments.name SEPARATOR ", ") as department_name,
            COALESCE(tb_tamp_task.total_task,0) as total_task,
            COALESCE(tb_tamp_report_old.count_bckph,0) as count_bckph_old,
            COALESCE(tb_tamp_report.count_bckph,0) as count_bckph,
            COALESCE(tb_tamp_old.violate,0) as violate_old,
            COALESCE(tb_tamp.violate,0) as violate,
            COALESCE(tb_tamp_vuot.vuot,0) as vuot,
            COALESCE(tb_tamp_report.violation_p1,0) as violation_p1,
            COALESCE(tb_tamp_report.violation_p2,0) as violation_p2,
            COALESCE(tb_tamp_report.violation_p3,0) as violation_p3,
            tb_tamp.kpi_list_criteria_department_id,
            tb_tamp_vuot.kpi_list_criteria_department_id as kpi_list_criteria_department_id_vuot,
            "" as rating,
            COALESCE(tb_tamp_report.weight_p2,0) as weight_p2,
            COALESCE(tb_tamp_report.weight_p3,0) as weight_p3,
            COALESCE(tb_tamp_task_process.total_task,0) as total_task_process,
            COALESCE(tb_tamp_report_process.count_bckph,0) as count_bckph_process,
            COALESCE(tb_tamp_audit.total_audit,0) as total_audit
        ');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
        $this->db->group_by('tblstaff.staffid');
        $this->db->where('active', 1);
        if (!empty($staff)) {
            $this->db->where('tblstaff.staffid', $staff);
        }
        if (!empty($department_search)) {
            $this->db->where('tblstaff_departments.departmentid', $department_search);
        }
        $this->db->join($tb_tamp, 'tblstaff.staffid = tb_tamp.staff_id', 'left');
        $this->db->join($tb_tamp_vuot, 'tblstaff.staffid = tb_tamp_vuot.staff_id', 'left');
        $this->db->join($tb_tamp_report, 'tblstaff.staffid = tb_tamp_report.staff_id', 'left');
        $this->db->join($tb_tamp_old, 'tblstaff.staffid = tb_tamp_old.staff_id', 'left');
        $this->db->join($tb_tamp_report_old, 'tblstaff.staffid = tb_tamp_report_old.staff_id', 'left');
        $this->db->join($tb_tamp_task, 'tblstaff.staffid = tb_tamp_task.staff_id', 'left');
        $this->db->join($tb_tamp_task_process, 'tblstaff.staffid = tb_tamp_task_process.staff_id', 'left');
        $this->db->join($tb_tamp_report_process, 'tblstaff.staffid = tb_tamp_report_process.staff_id', 'left');
        $this->db->join($tb_tamp_audit, 'tblstaff.staffid = tb_tamp_audit.staff_id', 'left');
        $dtStaff = $this->db->get()->result_array();

        // Lay du lieu vi pham KPI
        $dtCriteriaDepartmentViolateNew = [];
        $dtCriteriaDepartmentViolateNewVuot = [];
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];
                $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
                $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
                if (!empty($kpi_list_criteria_department_id)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolate = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolate)) {
                    $dtCriteriaDepartmentViolate = array_reduce($dtCriteriaDepartmentViolate, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNew[$staffid] = $dtCriteriaDepartmentViolate;

                // Vuot
                $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
                $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
                if (!empty($kpi_list_criteria_department_id_vuot)) {
                    $this->db->where_in('kpi_list_criteria_department_id', $kpi_list_criteria_department_id_vuot);
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $dtCriteriaDepartmentViolateVuot = $this->db->get()->result_array();
                }
                if (!empty($dtCriteriaDepartmentViolateVuot)) {
                    $dtCriteriaDepartmentViolateVuot = array_reduce($dtCriteriaDepartmentViolateVuot, function ($carry, $item) {
                        $carry[$item['kpi_list_criteria_department_id']][] = $item;
                        return $carry;
                    });
                }
                $dtCriteriaDepartmentViolateNewVuot[$staffid] = $dtCriteriaDepartmentViolateVuot;
            }
        }

        // === Bat dau tao Excel ===
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Font mac dinh
        $objPHPExcel->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Times New Roman'
            ],
        ]);

        // Tieu de bao cao
        $periodLabel = !empty($month_year_start) ? ('Quý ' . $precious . ' Năm ' . $year) : ('Tháng ' . $month . ' Năm ' . $year);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'BÁO CÁO ĐÁNH GIÁ KPI - ' . strtoupper($periodLabel));
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ]
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);

        // Header row
        $headers = [
            'STT',
            'Phòng ban',
            'Vị trí',
            'Nhân viên',
            'Tổng công việc',
            'Số BCKPH kỳ trước',
            'Số BCKPH kỳ này',
            'Vi phạm kỳ trước',
            'Vi phạm kỳ này',
            'Vượt mức',
            'Vi phạm P1',
            'Vi phạm P2',
            'Vi phạm P3',
            'Điểm KPI',
            'Xếp loại',
            'Kết quả (Thưởng/Kỷ luật)',
            'P2 (%)',
            'P3',
        ];

        $colWidths = [6, 25, 25, 20, 12, 14, 14, 14, 25, 25, 10, 10, 10, 10, 14, 30, 10, 8];

        $sttRow = 3;
        $colIndex = 'A';
        foreach ($headers as $idx => $header) {
            $col = chr(65 + $idx); // A, B, C, ...
            $objPHPExcel->getActiveSheet()->setCellValue($col . $sttRow, $header);
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($colWidths[$idx]);
        }
        $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'name' => 'Times New Roman'
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => '92D050'],
            ],
        ]);
        // Cột Phòng ban, Vị trí căn trái + wrap text
        $objPHPExcel->getActiveSheet()->getStyle("B$sttRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle("C$sttRow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getRowDimension($sttRow)->setRowHeight(35);

        // Data rows
        $rowNum = $sttRow + 1;
        $stt = 1;

        foreach ($dtStaff as $value) {
            // --- Chi tiet phieu vi pham ---
            $violate_detail = [];
            if ($value['violate'] > 0) {
                $this->db->select('
                    tblproduction_report.id,
                    tblproduction_report.reference_no,
                    tblproduction_report.date,
                    tblproduction_report.name_report,
                    tblproduction_report.kpi_list_criteria_department_violate,
                    tbl_kpi_dept_main.name as kpi_name,
                    tbl_kpi_dept_child.evaluation_criteria as kpi_child_criteria,
                    tbl_kpi_dept_child.name as kpi_child_name,
                    tbl_kpi_dept_childd.evaluation_criteria as kpi_grand_criteria
                ');
                $this->db->from('tblproduction_report');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_main', 'tbl_kpi_dept_main.id = tblproduction_report.kpi_list_criteria_department_id', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_child', 'tbl_kpi_dept_child.id = tblproduction_report.kpi_list_criteria_department_id_child', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_childd', 'tbl_kpi_dept_childd.id = tblproduction_report.kpi_list_criteria_department_id_childd', 'left');
                $this->db->where('tblproduction_report.staff_responsible', $value['staffid']);
                $this->db->where('tblproduction_report.kpi_list_criteria_department_id !=', 0);
                $this->db->where('tblproduction_report.violate', 1);
                $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') " . (!empty($month_year_start) ? "BETWEEN '$month_year_start' AND '$month_year_end'" : "= '$month_year'"), NULL, FALSE);
                $this->db->order_by('tblproduction_report.date', 'desc');
                $lstViolate = $this->db->get()->result_array();
                foreach ($lstViolate as $vp) {
                    $kpiInfo = '';
                    if (!empty($vp['kpi_name'])) {
                        $kpiInfo .= '+' . $vp['kpi_name'];
                    }
                    if (!empty($vp['kpi_child_criteria'])) {
                        $kpiInfo .= ' - ' . $vp['kpi_child_criteria'];
                    } elseif (!empty($vp['kpi_child_name'])) {
                        $kpiInfo .= ' - ' . $vp['kpi_child_name'];
                    }
                    if (!empty($vp['kpi_grand_criteria'])) {
                        $kpiInfo .= ' - ' . $vp['kpi_grand_criteria'];
                    }
                    $violateText = !empty($vp['kpi_list_criteria_department_violate']) ? $vp['kpi_list_criteria_department_violate'] : '';
                    $violate_detail[] = $vp['reference_no'] . ' ' . $kpiInfo . (!empty($violateText) ? ' [' . $violateText . ']' : '');
                }
            }

            // --- Chi tiet phieu vuot ---
            $vuot_detail = [];
            if ($value['vuot'] > 0) {
                $this->db->select('
                    tblproduction_report.id,
                    tblproduction_report.reference_no,
                    tblproduction_report.date,
                    tblproduction_report.name_report,
                    tblproduction_report.point_kpi,
                    tblproduction_report.kpi_list_criteria_department_violate,
                    tbl_kpi_dept_main.name as kpi_name,
                    tbl_kpi_dept_child.evaluation_criteria as kpi_child_criteria,
                    tbl_kpi_dept_child.name as kpi_child_name,
                    tbl_kpi_dept_childd.evaluation_criteria as kpi_grand_criteria
                ');
                $this->db->from('tblproduction_report');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_main', 'tbl_kpi_dept_main.id = tblproduction_report.kpi_list_criteria_department_id', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_child', 'tbl_kpi_dept_child.id = tblproduction_report.kpi_list_criteria_department_id_child', 'left');
                $this->db->join('tbl_kpi_list_criteria_department as tbl_kpi_dept_childd', 'tbl_kpi_dept_childd.id = tblproduction_report.kpi_list_criteria_department_id_childd', 'left');
                $this->db->where('tblproduction_report.staff_responsible', $value['staffid']);
                $this->db->where('tblproduction_report.kpi_list_criteria_department_id !=', 0);
                $this->db->where('tblproduction_report.type_report', 2);
                $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') " . (!empty($month_year_start) ? "BETWEEN '$month_year_start' AND '$month_year_end'" : "= '$month_year'"), NULL, FALSE);
                $this->db->order_by('tblproduction_report.date', 'desc');
                $lstVuot = $this->db->get()->result_array();
                foreach ($lstVuot as $vt) {
                    $kpiInfo = '';
                    if (!empty($vt['kpi_name'])) {
                        $kpiInfo .= '+' . $vt['kpi_name'];
                    }
                    if (!empty($vt['kpi_child_criteria'])) {
                        $kpiInfo .= ' - ' . $vt['kpi_child_criteria'];
                    } elseif (!empty($vt['kpi_child_name'])) {
                        $kpiInfo .= ' - ' . $vt['kpi_child_name'];
                    }
                    if (!empty($vt['kpi_grand_criteria'])) {
                        $kpiInfo .= ' - ' . $vt['kpi_grand_criteria'];
                    }
                    $vuot_detail[] = $vt['reference_no'] . ' ' . $kpiInfo . (!empty($vt['kpi_list_criteria_department_violate']) ? ' [' . $vt['kpi_list_criteria_department_violate'] . ']' : '');
                }
            }

            // Tinh diem KPI (cung logic nhu view_kpi_evaluation)
            $pointMax = 100;
            $kpi_list_criteria_department_id_db = $value['kpi_list_criteria_department_id'];
            $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
            $countedArray = [];
            if (!empty($kpi_list_criteria_department_id[0])) {
                $countedArray = array_count_values($kpi_list_criteria_department_id);
            }
            $dtCriteriaDepartmentViolate = !empty($dtCriteriaDepartmentViolateNew[$value['staffid']]) ? $dtCriteriaDepartmentViolateNew[$value['staffid']] : [];
            $point = 0;
            if (!empty($countedArray)) {
                foreach ($countedArray as $k => $v) {
                    $dtData = !empty($dtCriteriaDepartmentViolate[$k]) ? $dtCriteriaDepartmentViolate[$k] : [];
                    $violations = array_column($dtData, 'violations');
                    $violationsToPoint = [];
                    if (!empty($dtData)) {
                        foreach ($dtData as $item) {
                            $violationsToPoint[$item['violations']] = $item['point'];
                        }
                    }
                    $maxViolations = !empty($violations) ? max($violations) : 0;
                    if ($v <= $maxViolations) {
                        if (array_key_exists($v, $violationsToPoint)) {
                            if ($violationsToPoint[$v] == -1) {
                                $point += $violationsToPoint[$v - 1];
                            } else {
                                $point += $violationsToPoint[$v];
                            }
                        }
                    } else {
                        if ($maxViolations > 0) {
                            $point += $violationsToPoint[$maxViolations - 1];
                        }
                    }
                }
            }

            // Diem vuot
            $pointNew = 0;
            $kpi_list_criteria_department_id_db_vuot = $value['kpi_list_criteria_department_id_vuot'];
            $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot);
            $countedArrayVuot = [];
            if (!empty($kpi_list_criteria_department_id_vuot[0])) {
                $countedArrayVuot = array_count_values($kpi_list_criteria_department_id_vuot);
            }
            $dtCriteriaDepartmentViolateVuot = !empty($dtCriteriaDepartmentViolateNewVuot[$value['staffid']]) ? $dtCriteriaDepartmentViolateNewVuot[$value['staffid']] : [];
            if (!empty($countedArrayVuot)) {
                foreach ($countedArrayVuot as $k => $v) {
                    $dtDataVuot = !empty($dtCriteriaDepartmentViolateVuot[$k]) ? $dtCriteriaDepartmentViolateVuot[$k] : [];
                    $violationsVuot = array_column($dtDataVuot, 'violations');
                    $violationsToPointVuot = [];
                    if (!empty($dtDataVuot)) {
                        foreach ($dtDataVuot as $item) {
                            $violationsToPointVuot[$item['violations']] = $item['point_new'];
                        }
                    }
                    $maxViolationsVuot = !empty($violationsVuot) ? max($violationsVuot) : 0;
                    if ($v < $maxViolationsVuot) {
                        if (array_key_exists($v, $violationsToPointVuot)) {
                            $pointNew += $violationsToPointVuot[$v];
                        }
                    } else {
                        if ($maxViolationsVuot > 0) {
                            $pointNew += $violationsToPointVuot[$maxViolationsVuot - 1];
                        }
                    }
                }
            }

            $pointCurrent = $pointMax - $point + $pointNew;
            if ($pointCurrent <= 0) {
                $pointCurrent = 1;
            }
            if ($pointCurrent > 100) {
                $pointCurrent = 100;
            }

            // Kiem tra quyet dinh khen thuong
            $this->db->from('tbl_decision_bonus_discipline');
            $this->db->where('tbl_decision_bonus_discipline.type_quota_bonus_discipline_id', 1);
            $this->db->where('tbl_decision_bonus_discipline.object_id', $value['staffid']);
            $this->db->where('tbl_decision_bonus_discipline.object_type = "staff" ' . $whereDateDecision . '');
            $dtCountDecision = $this->db->count_all_results();

            // Lay xep loai KPI
            $dtRating = ratingKpiDepartment($pointCurrent);
            if (!empty($dtRating)) {
                if ($dtRating[0]['id'] == 1 && empty($dtCountDecision)) {
                    $dtRating = ratingKpiDepartment(-1, 2);
                }
            }

            // Thuong / Ky luat
            $bonusNames = [];
            $disciplineNames = [];
            $bonus = !empty($dtRating) ? $dtRating[0]['bonus'] : [];
            $discipline = !empty($dtRating) ? $dtRating[0]['discipline'] : [];
            if (!empty($bonus)) {
                foreach ($bonus as $k => $v) {
                    $bonusNames[] = $v['name'];
                }
            }
            if (!empty($discipline)) {
                foreach ($discipline as $k => $v) {
                    $disciplineNames[] = $v['name'];
                }
            }
            $resultText = '';
            if (!empty($bonusNames)) {
                $resultText .= 'Thưởng: ' . implode(', ', $bonusNames);
            }
            if (!empty($disciplineNames)) {
                if (!empty($resultText)) $resultText .= "\n";
                $resultText .= 'Kỷ luật: ' . implode(', ', $disciplineNames);
            }

            $ratingTitle = !empty($dtRating) ? $dtRating[0]['title'] : '';
            $ratingColor = !empty($dtRating) ? $dtRating[0]['color'] : '';
            $check_p3 = 'Không';
            if ($value['total_task_process'] == 0 && $value['count_bckph_process'] == 0 && $value['violate'] == 0 && $value['total_audit'] == 0) {
                $check_p3 = 'Có';
            }
            $p2_percent = (100 - $value['weight_p2']) > 0 ? (100 - $value['weight_p2']) . ' %' : '-';

            // Ghi dong du lieu
            $violateTextDisplay = !empty($violate_detail) ? " - " . implode("\n - ", $violate_detail) : ($value['violate'] != 0 ? $value['violate'] : '-');
            $vuotTextDisplay = !empty($vuot_detail) ? " - " . implode("\n - ", $vuot_detail) : ($value['vuot'] != 0 ? $value['vuot'] : '-');

            $dataRow = [
                $stt,
                $value['department_name'],
                $value['position_name'],
                $value['fullname'],
                ($value['total_task'] != 0 ? $value['total_task'] : '-'),
                ($value['count_bckph_old'] != 0 ? $value['count_bckph_old'] : '-'),
                ($value['count_bckph'] != 0 ? $value['count_bckph'] : '-'),
                ($value['violate_old'] != 0 ? $value['violate_old'] : '-'),
                $violateTextDisplay,
                $vuotTextDisplay,
                ($value['violation_p1'] != 0 ? $value['violation_p1'] : '-'),
                ($value['violation_p2'] != 0 ? $value['violation_p2'] : '-'),
                ($value['violation_p3'] != 0 ? $value['violation_p3'] : '-'),
                $pointCurrent,
                $ratingTitle,
                $resultText,
                $p2_percent,
                $check_p3,
            ];

            for ($i = 0; $i < count($dataRow); $i++) {
                $col = chr(65 + $i);
                $objPHPExcel->getActiveSheet()->setCellValue($col . $rowNum, $dataRow[$i]);
            }
            // Wrap text cho cot vi pham, vuot, ket qua
            $objPHPExcel->getActiveSheet()->getStyle("I$rowNum")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("J$rowNum")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("P$rowNum")->getAlignment()->setWrapText(true);
            // Chinh do cao dong cho nhung dong co noi dung dai
            $maxLines = max(count($violate_detail), count($vuot_detail));
            if ($maxLines > 1) {
                $objPHPExcel->getActiveSheet()->getRowDimension($rowNum)->setRowHeight(15 * ($maxLines + 1));
            }

            // To mau xep loai
            if (!empty($ratingColor)) {
                $objPHPExcel->getActiveSheet()->getStyle('O' . $rowNum)->applyFromArray([
                    'fill' => [
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => ['rgb' => $ratingColor],
                    ],
                ]);
            }

            // Border
            $objPHPExcel->getActiveSheet()->getStyle("A$rowNum:R$rowNum")->applyFromArray([
                'borders' => [
                    'allborders' => [
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    ]
                ],
                'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'size' => 10,
                    'name' => 'Times New Roman'
                ],
            ]);

            // Canh trai + wrap text cho cot phong ban, vi tri, vi pham, vuot, ket qua
            $objPHPExcel->getActiveSheet()->getStyle("B$rowNum")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("C$rowNum")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("I$rowNum")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("J$rowNum")->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->getStyle("P$rowNum")->getAlignment()->setWrapText(true);

            $rowNum++;
            $stt++;
        }

        // Border cho header
        $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->getFont()->setBold(true);

        // Freeze pane
        $objPHPExcel->getActiveSheet()->freezePane('A' . ($sttRow + 1));

        // Output file
        $filename = 'BaoCaoDanhGiaKPI_' . ($month_year_start ?: $month_year) . '_' . date('Ymd_His') . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }

    public function modal_kpi_evaluation_staff($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }
        $whereDate = '';
        if (!empty($month_year_start)) {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDate = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
        }
        $tb_tamp = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            $whereDate
            AND tblproduction_report.staff_responsible = $staff_id
        )";
        $query = $this->db->query($tb_tamp)->row_array();
        $html = '';
        if (!empty($query)) {
            $kpi_list_criteria_department_id_db = $query['kpi_list_criteria_department_id'];
            $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db);
            $dtKpiCriteriaDepartment = [];
            $arrId = [0];
            if (!empty($kpi_list_criteria_department_id[0])) {
                $kpi_list_criteria_department_id = array_unique($kpi_list_criteria_department_id);
                foreach ($kpi_list_criteria_department_id as $key => $value) {
                    $arrId = array_merge($arrId, get_child_kpi_department($value));
                }
            }
            $this->db->where_in('tbl_kpi_list_criteria_department.id', $arrId);
            $this->db->from('tbl_kpi_list_criteria_department');
            $dtKpiCriteriaDepartment = $this->db->get()->result_array();
            $dtData = recursiveListCriteriaDepartment($dtKpiCriteriaDepartment);
            if (!empty($dtData)) {
                $stt = 1;
                foreach ($dtData as $key => $value) {
                    $children = $value['children'];
                    $this->db->select('tbl_kpi_list_criteria_department_violate.*');
                    $this->db->from('tbl_kpi_list_criteria_department_violate');
                    $this->db->where('tbl_kpi_list_criteria_department_violate.kpi_list_criteria_department_id', $value['id']);
                    $dtDataListCriteriaDepartmentViolate = $this->db->get()->result_array();
                    $children = flatten_children($children);
                    $maxCount = max(count($dtDataListCriteriaDepartmentViolate) - 1, count($children));
                    $html .= '<tr class="bg-amber-50">';
                    $html .= '<td class="text-center font-bold px-3 py-2 text-xs border-b border-slate-200">' . intToRoman($stt) . '</td>';
                    $html .= '<td class="text-left font-bold px-3 py-2 text-xs border-b border-slate-200">' . $value['name'] . '</td>';
                    $html .= '<td class="px-3 py-2 border-b border-slate-200"></td><td class="px-3 py-2 border-b border-slate-200"></td><td class="px-3 py-2 border-b border-slate-200"></td><td class="px-3 py-2 border-b border-slate-200"></td><td class="px-3 py-2 border-b border-slate-200"></td>';
                    $html .= '</tr>';
                    $stt++;
                    $level = 1;
                    $sttChild = 0;
                    $sttChildNew = 1;
                    if ($maxCount) {
                        for ($i = 0; $i < $maxCount; $i++) {
                            $v = !empty($children[$i]) ? $children[$i] : [];
                            if (!empty($v)) {
                                $violate = 0;
                                if ($v['level'] == 1) {
                                    $sttChild++;
                                    $tb_tamp = "(
                                        SELECT COUNT(tblproduction_report.id) as violate
                                        FROM tblproduction_report
                                        WHERE kpi_list_criteria_department_id_child = " . $v['id'] . " AND tblproduction_report.violate = 1
                                        $whereDate
                                        AND tblproduction_report.staff_responsible = " . $staff_id . "
                                    )";
                                    $violate = $this->db->query($tb_tamp)->row_array()['violate'];
                                }
                                if ($v['level'] == 2) {
                                    $sttChildNew++;
                                    if ($level != $v['level']) {
                                        $sttChildNew = 1;
                                    }
                                    $tb_tamp = "(
                                        SELECT COUNT(tblproduction_report.id) as violate
                                        FROM tblproduction_report
                                        WHERE kpi_list_criteria_department_id_childd = " . $v['id'] . " AND tblproduction_report.violate = 1
                                        $whereDate
                                        AND tblproduction_report.staff_responsible = " . $staff_id . "
                                    )";
                                    $violate = $this->db->query($tb_tamp)->row_array()['violate'];
                                }
                                $level = $v['level'];
                            }
                            $html .= '<tr class="hover:bg-slate-50">';
                            if (!empty($v)) {
                                if ($v['level'] == 1) {
                                    $html .= '<td class="text-center px-3 py-2 text-xs border-b border-slate-100 text-slate-500">1.' . $sttChild . '</td>';
                                    $html .= '<td class="text-left font-semibold px-3 py-2 text-xs border-b border-slate-100 text-slate-700">' . (!empty($v) ? $v['name'] : '') . '</td>';
                                } else {
                                    $html .= '<td class="text-center px-3 py-2 text-xs border-b border-slate-100 text-slate-400">' . $sttChildNew . '</td>';
                                    $html .= '<td class="px-3 py-2 border-b border-slate-100"></td>';
                                }
                            } else {
                                $html .= '<td class="px-3 py-2 border-b border-slate-100"></td><td class="px-3 py-2 border-b border-slate-100"></td>';
                            }
                            $html .= '<td class="text-left px-3 py-2 text-xs border-b border-slate-100 text-slate-600">' . (!empty($v) ? $v['evaluation_criteria'] : '') . '</td>';
                            $html .= '<td class="text-center px-3 py-2 text-xs border-b border-slate-100 text-slate-500">' . (!empty($v) ? $v['violate'] : '') . '</td>';
                            if (!empty($v)) {
                                if ($v['level'] == 1) {
                                    $html .= '<td class="text-center font-medium px-3 py-2 text-xs border-b border-slate-100 text-red-600">' . (!empty($violate) && (!empty($v) && !empty($v['violate'])) ? $violate : '') . '</td>';
                                } else {
                                    $html .= '<td class="text-center font-medium px-3 py-2 text-xs border-b border-slate-100 text-red-600">' . (!empty($violate) ? $violate : '') . '</td>';
                                }
                            } else {
                                $html .= '<td class="px-3 py-2 border-b border-slate-100"></td>';
                            }
                            $html .= '<td class="text-left px-3 py-2 text-xs border-b border-slate-100 text-slate-500">' . (!empty($dtDataListCriteriaDepartmentViolate[$i]) ? $dtDataListCriteriaDepartmentViolate[$i]['violations_text'] : "") . '</td>';
                            $html .= '<td class="text-left font-medium px-3 py-2 text-xs border-b border-slate-100 ' . (!empty($dtDataListCriteriaDepartmentViolate[$i]) && $dtDataListCriteriaDepartmentViolate[$i]['point_text'] != '0' ? 'text-red-500' : 'text-slate-400') . '">' . (!empty($dtDataListCriteriaDepartmentViolate[$i]) ? $dtDataListCriteriaDepartmentViolate[$i]['point_text'] : "") . '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
            }
        }

        echo '<div class="overflow-x-auto"><table class="w-full text-left border-collapse">
            <thead class="bg-slate-100 border-b border-slate-200">
                <tr>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center">STT</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Tiêu chí đánh giá</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Cách đánh giá</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center">Lỗi vi phạm</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center">Thực tế</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Quy định vi phạm</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Điểm trừ</th>
                </tr>
            </thead>
            <tbody>' . $html . '</tbody>
        </table></div>';
    }

    public function modal_detail_production_report($staff_id = 0, $month = 0, $year = 0, $precious = 0, $type = 'violate')
    {
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }

        $this->db->select('tblproduction_report.id, tblproduction_report.date, tblproduction_report.reference_no, tblproduction_report.name_report, tblproduction_report.kpi_list_criteria_department_id, tblproduction_report.kpi_list_criteria_department_id_child, tblproduction_report.kpi_list_criteria_department_id_childd, tblproduction_report.kpi_list_criteria_department_violate');
        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.staff_responsible', $staff_id);
        $this->db->where('tblproduction_report.kpi_list_criteria_department_id !=', 0);
        if ($type == 'vuot') {
            $this->db->where('tblproduction_report.type_report', 2);
        } else {
            $this->db->where('tblproduction_report.violate', 1);
        }

        if (!empty($month_year_start)) {
            $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') >= ", $month_year_start);
            $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') <= ", $month_year_end);
        } else {
            $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') = ", $month_year);
        }
        $this->db->order_by('tblproduction_report.date', 'DESC');
        $reports = $this->db->get()->result_array();

        $html = '<div class="overflow-x-auto"><table class="w-full text-left border-collapse">
            <thead class="bg-slate-100 border-b border-slate-200">
                <tr>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center w-12">#</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Ngày</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Số phiếu</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Tên phiếu</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">' . ($type == 'vuot' ? 'Vượt' : 'Vi phạm') . '</th>
                </tr>
            </thead>
            <tbody>';

        if (empty($reports)) {
            $html .= '<tr><td colspan="5" class="text-center py-8 text-slate-400">Không có dữ liệu</td></tr>';
        } else {
            $stt = 1;
            foreach ($reports as $row) {
                $this->db->from('tbl_kpi_list_criteria_department')->where('id', $row['kpi_list_criteria_department_id']);
                $dtDataKpi = $this->db->get()->row_array();

                $this->db->from('tbl_kpi_list_criteria_department')->where('id', $row['kpi_list_criteria_department_id_child']);
                $dtDataKpiDetail = $this->db->get()->row_array();

                $this->db->from('tbl_kpi_list_criteria_department')->where('id', $row['kpi_list_criteria_department_id_childd']);
                $dtDataKpiDetailNew = $this->db->get()->row_array();

                $name_kpi_department = !empty($dtDataKpi) ? '+' . $dtDataKpi['name'] : '';
                $name_kpi_department_detail = (!empty($dtDataKpiDetail) ? '+' . (!empty($dtDataKpiDetail['evaluation_criteria']) ? $dtDataKpiDetail['evaluation_criteria'] : $dtDataKpiDetail['name']) : '') . '-' . (!empty($dtDataKpiDetailNew) ? $dtDataKpiDetailNew['evaluation_criteria'] : '');

                $html .= '<tr class="border-b border-slate-100 hover:bg-slate-50">';
                $html .= '<td class="px-3 py-2 text-center text-xs text-slate-500">' . $stt++ . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-600 whitespace-nowrap">' . _dt($row['date']) . '</td>';
                $html .= '<td class="px-3 py-2 text-xs font-medium text-blue-600">' . $row['reference_no'] . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-800">' . $row['name_report'] . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-600"><div class="text-slate-800 font-medium">' . $name_kpi_department . '</div><div class="text-slate-500 mt-1">' . $name_kpi_department_detail . '</div><div class="text-red-500 font-semibold mt-1">+' . $row['kpi_list_criteria_department_violate'] . '</div></td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table></div>';
        echo $html;
    }

    public function modal_detail_p2($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }

        $this->db->select('tblproduction_report.id, tblproduction_report.date, tblproduction_report.reference_no, tblproduction_report.name_report, tbl_kpi_list_criteria_department.weight as weight');
        $this->db->from('tblproduction_report');
        $this->db->join('tbl_kpi_list_criteria_department', 'tbl_kpi_list_criteria_department.id = COALESCE(NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0), tblproduction_report.kpi_list_criteria_department_id_child)', 'left');
        $this->db->where('tblproduction_report.staff_responsible', $staff_id);
        $this->db->where('tbl_kpi_list_criteria_department.type_p', 2);

        if (!empty($month_year_start)) {
            $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') >= ", $month_year_start);
            $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') <= ", $month_year_end);
        } else {
            $this->db->where("DATE_FORMAT(tblproduction_report.date, '%Y-%m') = ", $month_year);
        }
        $this->db->order_by('tblproduction_report.date', 'DESC');
        $reports = $this->db->get()->result_array();

        $html = '<div class="overflow-x-auto"><table class="w-full text-left border-collapse">
            <thead class="bg-slate-100 border-b border-slate-200">
                <tr>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center w-12">#</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Ngày</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Số phiếu</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Tên phiếu</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center">Bị trừ</th>
                </tr>
            </thead>
            <tbody>';

        if (empty($reports)) {
            $html .= '<tr><td colspan="5" class="text-center py-8 text-slate-400">Không có dữ liệu</td></tr>';
        } else {
            $stt = 1;
            foreach ($reports as $row) {
                $html .= '<tr class="border-b border-slate-100 hover:bg-slate-50">';
                $html .= '<td class="px-3 py-2 text-center text-xs text-slate-500">' . $stt++ . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-600 whitespace-nowrap">' . _dt($row['date']) . '</td>';
                $html .= '<td class="px-3 py-2 text-xs font-medium text-blue-600">' . $row['reference_no'] . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-800">' . $row['name_report'] . '</td>';
                $html .= '<td class="px-3 py-2 text-xs font-bold text-red-500 text-center"> -' . $row['weight'] . '%</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table></div>';
        echo $html;
    }

    public function modal_detail_p3($staff_id = 0, $month = 0, $year = 0, $precious = 0)
    {
        $month_year = $year . '-' . $month;
        $month_year_start = null;
        $month_year_end = null;
        if (!empty($precious)) {
            if ($precious == 1) {
                $month_year_start = $year . '-01';
                $month_year_end = $year . '-03';
            } elseif ($precious == 2) {
                $month_year_start = $year . '-04';
                $month_year_end = $year . '-06';
            } elseif ($precious == 3) {
                $month_year_start = $year . '-07';
                $month_year_end = $year . '-09';
            } elseif ($precious == 4) {
                $month_year_start = $year . '-10';
                $month_year_end = $year . '-12';
            }
        }

        $whereDateTask = '';
        $whereDateReport = '';
        $whereDateAudit = '';
        if (!empty($month_year_start)) {
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateReport = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") <= "' . $month_year_end . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") >= "' . $month_year_start . '" AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") <= "' . $month_year_end . '"';
        } else {
            $whereDateTask = 'AND DATE_FORMAT(tbltasks.dateadded, "%Y-%m") = "' . $month_year . '"';
            $whereDateReport = 'AND DATE_FORMAT(tblproduction_report.date, "%Y-%m") = "' . $month_year . '"';
            $whereDateAudit = 'AND DATE_FORMAT(tbl_audit.audit_date, "%Y-%m") = "' . $month_year . '"';
        }
        $this->db->dbprefix = '';
        $tb_tamp = "(
            SELECT
                tblproduction_report.id,
                tblproduction_report.date,
                tblproduction_report.reference_no,
                tblproduction_report.name_report,
                0 as status,
                1 as type
            FROM tblproduction_report
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             ) AND tblproduction_report.staff_responsible = $staff_id $whereDateReport
             
            UNION ALL
            
            SELECT
                tblproduction_report.id,
                tblproduction_report.date,
                tblproduction_report.reference_no,
                tblproduction_report.name_report,
                0 as status,
                2 as type
            FROM tblproduction_report
            WHERE tblproduction_report.id != 0 AND tblproduction_report.violate = 1 
            AND tblproduction_report.staff_responsible = $staff_id $whereDateReport
            
            UNION ALL 
             
            SELECT 
                tbltasks.id,
                tbltasks.dateadded as date,
                tblcategory_tasks.code COLLATE utf8_unicode_ci AS reference_no,
                tbltasks.name COLLATE utf8_unicode_ci AS name_report,
                0 as status,
                3 as type
            FROM tbltasks
            LEFT JOIN tblcategory_tasks ON tblcategory_tasks.id = tbltasks.category_tasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5 
            AND tbltask_assigned.staffid = $staff_id $whereDateTask    
             
            UNION ALL 
             
            SELECT 
                tbl_audit.id,
                tbl_audit.audit_date as date,
                tbl_audit.audit_code COLLATE utf8_unicode_ci AS reference_no,
                tbl_audit.audit_code COLLATE utf8_unicode_ci AS name_report,
                0 as status,
                4 as type
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            AND tblstaff_departments.staffid = $staff_id $whereDateAudit 
        ) tb_tamp";

        $query = "SELECT * FROM " . $tb_tamp . " ORDER BY date DESC";
        $reports = $this->db->query($query)->result_array();

        $html = '<div class="overflow-x-auto"><table class="w-full text-left border-collapse">
            <thead class="bg-slate-100 border-b border-slate-200">
                <tr>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider text-center w-12">#</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Ngày</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Số phiếu</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Tên phiếu</th>
                    <th class="px-3 py-2 text-[11px] font-bold text-slate-600 uppercase tracking-wider">Phân loại</th>
                </tr>
            </thead>
            <tbody>';

        if (empty($reports)) {
            $html .= '<tr><td colspan="5" class="text-center py-8 text-slate-400">Không có dữ liệu</td></tr>';
        } else {
            $stt = 1;
            foreach ($reports as $row) {
                $htmlType = '';
                $htmlTypeNew = '';
                if ($row['type'] == 2) {
                    $htmlType = 'Phiếu vi phạm';
                    $htmlTypeNew  = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-800 ml-2">Vi phạm</span>';
                } elseif ($row['type'] == 1) {
                    $htmlType = 'BCKPH chưa hoàn thành';
                    $htmlTypeNew  = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 ml-2">BCKPH</span>';
                } elseif ($row['type'] == 3) {
                    $htmlType = 'Công việc chưa hoàn thành';
                    $htmlTypeNew  = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 ml-2">Công việc</span>';
                } elseif ($row['type'] == 4) {
                    $htmlType = 'Audit fail';
                    $htmlTypeNew  = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 ml-2">Audit</span>';
                }

                $html .= '<tr class="border-b border-slate-100 hover:bg-slate-50">';
                $html .= '<td class="px-3 py-2 text-center text-xs text-slate-500">' . $stt++ . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-600 whitespace-nowrap">' . (!empty($row['date']) ? _dt($row['date']) : '') . '</td>';
                $html .= '<td class="px-3 py-2 text-xs font-medium text-slate-800">' . $row['reference_no'] . $htmlTypeNew . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-600">' . $row['name_report'] . '</td>';
                $html .= '<td class="px-3 py-2 text-xs text-slate-600">' . $htmlType . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</tbody></table></div>';
        echo $html;
    }
    public function taodatamau()
    {
        // 1. Khách hàng (import_khach_hang) -> tbl_kpi_targets_clients
        $this->db->query("TRUNCATE TABLE tbl_kpi_targets_clients");
        $clients = $this->db->select('userid')->get('tblclients')->result_array();
        $count = 0;
        foreach ($clients as $c) {
            if ($count++ >= 20) break;
            $data = [
                'id_client' => $c['userid']
            ];
            $fields = $this->db->list_fields('tbl_kpi_targets_clients');
            if (in_array('SoBaoGia', $fields)) $data['SoBaoGia'] = rand(10, 50);
            if (in_array('BaoGiaDaDuyet', $fields)) $data['BaoGiaDaDuyet'] = rand(5, 25);
            if (in_array('BaoGiaChuaDuyet', $fields)) $data['BaoGiaChuaDuyet'] = rand(0, 5);
            if (in_array('DonHangCo', $fields)) $data['DonHangCo'] = rand(20, 100);
            if (in_array('DonHangKhongCo', $fields)) $data['DonHangKhongCo'] = rand(0, 10);
            if (in_array('PTMCoDon', $fields)) $data['PTMCoDon'] = rand(1, 5);
            if (in_array('PTMKhongDon', $fields)) $data['PTMKhongDon'] = rand(0, 2);
            if (in_array('SoComplain', $fields)) $data['SoComplain'] = rand(0, 3);
            if (in_array('MauLan1', $fields)) $data['MauLan1'] = rand(5, 20);
            if (in_array('MauLan2', $fields)) $data['MauLan2'] = rand(0, 5);
            if (in_array('DiemCong', $fields)) $data['DiemCong'] = 100;
            if (in_array('DiemTru', $fields)) $data['DiemTru'] = rand(0, 20);
            if (in_array('TongDiem', $fields)) $data['TongDiem'] = rand(70, 100);
            $this->db->insert('tbl_kpi_targets_clients', $data);
        }

        // 2. Nhà cung cấp (import_ncc) -> tbl_kpi_targets_supplier
        $this->db->query("TRUNCATE TABLE tbl_kpi_targets_supplier");
        $suppliers = $this->db->select('id')->get('tblsuppliers')->result_array();
        $count = 0;
        foreach ($suppliers as $s) {
            if ($count++ >= 20) break;
            $data = [
                'id_supplier' => $s['id']
            ];
            $fields = $this->db->list_fields('tbl_kpi_targets_supplier');
            if (in_array('SoDonHang', $fields)) $data['SoDonHang'] = rand(5, 30);
            if (in_array('GiaoHangDungHan', $fields)) $data['GiaoHangDungHan'] = rand(4, 25);
            if (in_array('GiaoHangTreHan', $fields)) $data['GiaoHangTreHan'] = rand(0, 5);
            if (in_array('SoLanLoiChatLuong', $fields)) $data['SoLanLoiChatLuong'] = rand(0, 2);
            if (in_array('SoLanComplain', $fields)) $data['SoLanComplain'] = rand(0, 3);
            if (in_array('DiemCong', $fields)) $data['DiemCong'] = 100;
            if (in_array('DiemTru', $fields)) $data['DiemTru'] = rand(0, 30);
            if (in_array('TongDiem', $fields)) $data['TongDiem'] = rand(60, 100);
            $this->db->insert('tbl_kpi_targets_supplier', $data);
        }

        // 3. Thiết bị (import_thiet_bi) -> tbl_kpi_equipment_stage
        $this->db->query("TRUNCATE TABLE tbl_kpi_equipment_stage");
        $stages = ['CĐ Cắt', 'CĐ May', 'CĐ Đóng gói', 'CĐ Ép', 'CĐ Sơn'];
        for ($i = 1; $i <= 20; $i++) {
            $data = [];
            $fields = $this->db->list_fields('tbl_kpi_equipment_stage');
            if (in_array('group_stage', $fields)) $data['group_stage'] = $stages[array_rand($stages)];
            if (in_array('stage_code', $fields)) $data['stage_code'] = 'CD00' . rand(1, 5);
            if (in_array('equipment_code', $fields)) $data['equipment_code'] = 'TB-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            if (in_array('equipment_name', $fields)) $data['equipment_name'] = 'Máy sản xuất ' . $i;
            if (in_array('equipment_status', $fields)) $data['equipment_status'] = ['Hoạt động', 'Dừng', 'Bảo trì'][rand(0, 2)];
            if (in_array('downtime_minutes', $fields)) $data['downtime_minutes'] = rand(0, 120);
            if (in_array('planned_output', $fields)) $data['planned_output'] = rand(5000, 10000);
            if (in_array('actual_output', $fields)) $data['actual_output'] = rand(4000, 9500);
            if (in_array('target_achievement_pct', $fields)) $data['target_achievement_pct'] = rand(70, 100);
            if (in_array('repair_cost', $fields)) $data['repair_cost'] = rand(0, 5000000);
            if (in_array('maintenance_cost', $fields)) $data['maintenance_cost'] = rand(100000, 2000000);
            if (in_array('total_cost', $fields)) $data['total_cost'] = rand(100000, 7000000);
            if (in_array('warning_status', $fields)) $data['warning_status'] = ['Bình thường', 'Cần kiểm tra', 'Nguy hiểm'][rand(0, 2)];

            $this->db->insert('tbl_kpi_equipment_stage', $data);
        }

        // 4. Ngân sách phòng ban (department_budget) -> tbl_department_budget
        $this->db->query("TRUNCATE TABLE tbl_department_budget");
        $departments = $this->db->select('departmentid')->get('tbldepartments')->result_array();
        $costs = $this->db->select('id')->get('tblcosts')->result_array();

        $count = 0;
        if (!empty($departments) && !empty($costs)) {
            foreach ($departments as $d) {
                if ($count++ >= 20) break;
                // Random 1-3 chi phí cho mỗi phòng ban
                $rand_costs = (array) array_rand($costs, rand(1, min(3, count($costs))));
                foreach ($rand_costs as $c_idx) {
                    $c = $costs[$c_idx];
                    $data = [
                        'department_id' => $d['departmentid'],
                        'cost_id' => $c['id']
                    ];
                    $fields = $this->db->list_fields('tbl_department_budget');
                    if (in_array('ngan_sach_duoc_cap', $fields)) $data['ngan_sach_duoc_cap'] = rand(10, 500) * 1000000;
                    if (in_array('ghi_chu', $fields)) $data['ghi_chu'] = 'Ngân sách cấp đầu năm';
                    $this->db->insert('tbl_department_budget', $data);
                }
            }
        }

        echo "Đã tạo dữ liệu mẫu thành công cho 4 bảng!";
    }

    private function _get_stats_for_phieu($staff_id, $date_start, $date_end)
    {
        $d_start = date('Y-m-d', strtotime($date_start));
        $d_end = date('Y-m-d', strtotime($date_end));

        $total_task = $this->db->query("SELECT COUNT(t.id) as total FROM tbltasks t JOIN tbltask_assigned ta ON ta.taskid = t.id WHERE ta.staffid = ? AND t.id != 0 AND DATE(t.dateadded) >= ? AND DATE(t.dateadded) <= ?", [$staff_id, $d_start, $d_end])->row()->total ?? 0;
        $total_task_process = $this->db->query("SELECT COUNT(t.id) as total FROM tbltasks t JOIN tbltask_assigned ta ON ta.taskid = t.id WHERE ta.staffid = ? AND t.id != 0 AND t.status != 5 AND DATE(t.dateadded) >= ? AND DATE(t.dateadded) <= ?", [$staff_id, $d_start, $d_end])->row()->total ?? 0;

        $count_bckph = $this->db->query("SELECT COUNT(id) as count FROM tblproduction_report WHERE staff_responsible = ? AND id != 0 AND DATE(date) >= ? AND DATE(date) <= ?", [$staff_id, $d_start, $d_end])->row()->count ?? 0;
        $count_bckph_old = $this->db->query("SELECT COUNT(id) as count FROM tblproduction_report WHERE staff_responsible = ? AND id != 0 AND DATE(date) < ?", [$staff_id, $d_start])->row()->count ?? 0;

        $violate = $this->db->query("SELECT COUNT(id) as count FROM tblproduction_report WHERE staff_responsible = ? AND kpi_list_criteria_department_id != 0 AND violate = 1 AND DATE(date) >= ? AND DATE(date) <= ?", [$staff_id, $d_start, $d_end])->row()->count ?? 0;
        $violate_old = $this->db->query("SELECT COUNT(id) as count FROM tblproduction_report WHERE staff_responsible = ? AND kpi_list_criteria_department_id != 0 AND violate = 1 AND DATE(date) < ?", [$staff_id, $d_start])->row()->count ?? 0;

        $vuot = $this->db->query("SELECT COUNT(id) as count FROM tblproduction_report WHERE staff_responsible = ? AND kpi_list_criteria_department_id != 0 AND type_report = 2 AND DATE(date) >= ? AND DATE(date) <= ?", [$staff_id, $d_start, $d_end])->row()->count ?? 0;

        $p_stats = $this->db->query("
            SELECT 
                SUM(CASE WHEN k.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN k.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN k.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3,
                SUM(CASE WHEN k.type_p = 1 THEN k.weight ELSE 0 END) AS weight_p1,
                SUM(CASE WHEN k.type_p = 2 THEN k.weight ELSE 0 END) AS weight_p2,
                SUM(CASE WHEN k.type_p = 3 THEN k.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report pr
            LEFT JOIN tbl_kpi_list_criteria_department k
            ON k.id = COALESCE(NULLIF(pr.kpi_list_criteria_department_id_childd, 0), pr.kpi_list_criteria_department_id_child)
            WHERE pr.staff_responsible = ? AND pr.id != 0 AND DATE(pr.date) >= ? AND DATE(pr.date) <= ?
        ", [$staff_id, $d_start, $d_end])->row();

        $total_audit = $this->db->query("
            SELECT COUNT(a.id) as count FROM tbl_audit a
            JOIN tbl_room r ON r.id = a.dept_id
            JOIN tbldepartments d ON d.room_id = r.id
            JOIN tblstaff_departments sd ON sd.departmentid = d.departmentid
            WHERE sd.staffid = ? AND EXISTS (SELECT 1 FROM tbl_audit_checklist ac WHERE ac.audit_id = a.id AND ac.status = 'no')
            AND DATE(a.audit_date) >= ? AND DATE(a.audit_date) <= ?
        ", [$staff_id, $d_start, $d_end])->row()->count ?? 0;

        $count_bckph_process = $this->db->query("
            SELECT COUNT(pr.id) as count FROM tblproduction_report pr
            WHERE pr.staff_responsible = ? AND pr.id != 0 AND EXISTS (SELECT 1 FROM tbl_process_production_report ppr WHERE ppr.production_report_id = pr.id AND ppr.staff_process = 0)
            AND DATE(pr.date) >= ? AND DATE(pr.date) <= ?
        ", [$staff_id, $d_start, $d_end])->row()->count ?? 0;

        $check_p3 = 'Không';
        if ($total_task_process == 0 && $count_bckph_process == 0 && $violate == 0 && $total_audit == 0) {
            $check_p3 = 'Có';
        }

        // Calculate KPI Points
        $pointMax = 100;
        $kpi_list_criteria_department_id_db = $this->db->query("SELECT GROUP_CONCAT(kpi_list_criteria_department_id SEPARATOR ',') as ids FROM tblproduction_report WHERE staff_responsible = ? AND kpi_list_criteria_department_id != 0 AND violate = 1 AND DATE(date) >= ? AND DATE(date) <= ?", [$staff_id, $d_start, $d_end])->row()->ids;
        $kpi_list_criteria_department_id = explode(',', $kpi_list_criteria_department_id_db ?: '');
        $countedArray = !empty($kpi_list_criteria_department_id[0]) ? array_count_values($kpi_list_criteria_department_id) : [];

        $point = 0;
        if (!empty($countedArray)) {
            $dtCriteriaDepartmentViolate = $this->db->where_in('kpi_list_criteria_department_id', array_keys($countedArray))->get('tbl_kpi_list_criteria_department_violate')->result_array();
            $groupedViols = [];
            foreach ($dtCriteriaDepartmentViolate as $v) $groupedViols[$v['kpi_list_criteria_department_id']][] = $v;
            foreach ($countedArray as $k => $v) {
                $dtData = $groupedViols[$k] ?? [];
                $violationsToPoint = [];
                foreach ($dtData as $item) $violationsToPoint[$item['violations']] = $item['point'];
                $maxViolations = !empty($dtData) ? max(array_column($dtData, 'violations')) : 0;
                if ($v <= $maxViolations) {
                    if (array_key_exists($v, $violationsToPoint)) {
                        $point += ($violationsToPoint[$v] == -1 && isset($violationsToPoint[$v - 1])) ? $violationsToPoint[$v - 1] : $violationsToPoint[$v];
                    }
                } else {
                    if (!empty($violationsToPoint) && isset($violationsToPoint[$maxViolations - 1])) $point += $violationsToPoint[$maxViolations - 1];
                }
            }
        }

        $kpi_list_criteria_department_id_db_vuot = $this->db->query("SELECT GROUP_CONCAT(kpi_list_criteria_department_id SEPARATOR ',') as ids FROM tblproduction_report WHERE staff_responsible = ? AND kpi_list_criteria_department_id != 0 AND type_report = 2 AND DATE(date) >= ? AND DATE(date) <= ?", [$staff_id, $d_start, $d_end])->row()->ids;
        $kpi_list_criteria_department_id_vuot = explode(',', $kpi_list_criteria_department_id_db_vuot ?: '');
        $countedArrayVuot = !empty($kpi_list_criteria_department_id_vuot[0]) ? array_count_values($kpi_list_criteria_department_id_vuot) : [];

        $pointNew = 0;
        if (!empty($countedArrayVuot)) {
            $dtCriteriaDepartmentViolateVuot = $this->db->where_in('kpi_list_criteria_department_id', array_keys($countedArrayVuot))->get('tbl_kpi_list_criteria_department_violate')->result_array();
            $groupedViolsVuot = [];
            foreach ($dtCriteriaDepartmentViolateVuot as $v) $groupedViolsVuot[$v['kpi_list_criteria_department_id']][] = $v;
            foreach ($countedArrayVuot as $k => $v) {
                $dtData = $groupedViolsVuot[$k] ?? [];
                $violationsToPoint = [];
                foreach ($dtData as $item) $violationsToPoint[$item['violations']] = $item['point_new'];
                $maxViolations = !empty($dtData) ? max(array_column($dtData, 'violations')) : 0;
                if ($v < $maxViolations) {
                    if (array_key_exists($v, $violationsToPoint)) $pointNew += $violationsToPoint[$v];
                } else {
                    if (!empty($violationsToPoint) && isset($violationsToPoint[$maxViolations - 1])) $pointNew += $violationsToPoint[$maxViolations - 1];
                }
            }
        }

        $pointCurrent = $pointMax - $point + $pointNew;
        $pointCurrent = max(1, min(100, $pointCurrent));

        $dtCountDecision = $this->db->where('type_quota_bonus_discipline_id', 1)->where('object_id', $staff_id)->where('object_type', 'staff')->where("DATE(date) >= '$d_start' AND DATE(date) <= '$d_end'")->count_all_results('tbl_decision_bonus_discipline');

        $dtRating = [];
        if (function_exists('ratingKpiDepartment')) {
            $dtRating = ratingKpiDepartment($pointCurrent);
            if (!empty($dtRating) && $dtRating[0]['id'] == 1 && empty($dtCountDecision)) {
                $dtRating = ratingKpiDepartment(-1, 2);
            }
        }

        return [
            'total_task' => $total_task,
            'count_bckph' => $count_bckph,
            'count_bckph_old' => $count_bckph_old,
            'violate' => $violate,
            'violate_old' => $violate_old,
            'vuot' => $vuot,
            'violation_p1' => $p_stats->violation_p1 ?? 0,
            'violation_p2' => $p_stats->violation_p2 ?? 0,
            'violation_p3' => $p_stats->violation_p3 ?? 0,
            'weight_p2' => $p_stats->weight_p2 ?? 0,
            'weight_p3' => $p_stats->weight_p3 ?? 0,
            'check_p3' => $check_p3,
            'kpi_point' => $pointCurrent,
            'kpi_rating' => $dtRating[0]['title'] ?? '',
            'kpi_color' => $dtRating[0]['color'] ?? '',
            'kpi_bonus' => $dtRating[0]['bonus'] ?? [],
            'kpi_discipline' => $dtRating[0]['discipline'] ?? [],
        ];
    }

    public function print_compact($id)
    {
        $this->db->select("
            pa.id, pa.code, pa.date, pa.staff_id, pa.role_id,
            pa.date_start, pa.date_end, pa.point, pa.point_b, pa.point_c, pa.point_d,
            pa.rating, pa.rating_list, pa.note, pa.level_target, pa.level_achieved,
            pa.type, pa.date_created, pa.created_by, pa.approval_status,
            CONCAT(COALESCE(s.firstname,''),' ',COALESCE(s.lastname,'')) as staff_name,
            r.name as role_name,
            rm.name as room_name,
            rc.name as rating_name,
            rc.color as rating_color
        ", false);
        $this->db->from('tbl_probationary_assessment pa');
        $this->db->join('tblstaff s', 's.staffid = pa.staff_id', 'left');
        $this->db->join('tblroles r', 'r.roleid = pa.role_id', 'left');
        $this->db->join('tbl_room rm', 'rm.id = r.id_room', 'left');
        $this->db->join('tbl_result_checklist rc', 'rc.id = pa.rating_list', 'left');
        $this->db->where('pa.id', (int)$id);
        $data['selected'] = $this->db->get()->row_array();

        if (!$data['selected']) {
            show_404();
        }

        $year_for_ky = date('Y');
        if (!empty($data['selected']['date_start'])) {
            $year_for_ky = date('Y', strtotime($data['selected']['date_start']));
        }
        $ky_map = $this->_compute_ky_map($year_for_ky);
        $data['selected']['ky_danh_gia'] = $ky_map[(int)$id] ?? '-';
        $data['f'] = $data['selected'];

        $this->db->from('tbl_checklist_probationary_assessment');
        $dtChecklist = $this->db->get()->result_array();
        $checkList = [];
        foreach ($dtChecklist as $row) {
            $checkList[$row['type']][] = $row;
        }
        $data['checkList'] = $checkList;

        $this->db->from('tbl_probationary_assessment_item');
        $this->db->where('probationary_assessment_id', $id);
        $dtDataItems = $this->db->get()->result_array();
        $checkListItems = [];
        foreach ($dtDataItems as $row) {
            $checkListItems[$row['type_check_list']][] = $row;
        }
        $mappedItems = [];
        foreach ($checkListItems as $ctype => $items) {
            foreach ($items as $item) {
                $mappedItems[$ctype][$item['check_list_id']] = $item;
            }
        }
        $data['checkListItems'] = $mappedItems;

        $data['is_print_compact'] = true;
        $this->load->view('admin/dashboard_kpi/tabs/form_in', $data);
    }

    public function ajax_get_real_stats()
    {
        $staff_id = $this->input->post('staff_id');
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');

        if (empty($staff_id) || empty($date_start) || empty($date_end)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu: Nhân viên hoặc thời gian không xác định']);
            exit;
        }

        $start = ($date_start);
        $end = ($date_end);

        // 1. Task Stats (Hoàn thành công việc)
        $this->db->select("COUNT(t.id) as total, SUM(CASE WHEN t.status = 5 THEN 1 ELSE 0 END) as done");
        $this->db->from('tbltasks t');
        $this->db->join('tbltask_assigned ta', 'ta.taskid = t.id');
        $this->db->where('ta.staffid', $staff_id);
        $this->db->where('t.startdate >=', $start . ' 00:00:00');
        $this->db->where('t.startdate <=', $end . ' 23:59:59');
        $task_stats = $this->db->get()->row_array();

        $task_percent = 0;
        if (!empty($task_stats['total'])) {
            $task_percent = round(($task_stats['done'] / $task_stats['total']) * 100, 1);
        }

        // 2. Production Stats (Chất lượng / Vi phạm)
        $this->db->select("
            SUM(CASE WHEN type_report = 1 THEN 1 ELSE 0 END) as count_kph,
            SUM(CASE WHEN type_report = 4 THEN 1 ELSE 0 END) as count_vp,
            SUM(CASE WHEN type_report = 2 THEN 1 ELSE 0 END) as count_lap_lai
        ");
        $this->db->from('tblproduction_report');
        $this->db->where('staff_responsible', $staff_id);
        $this->db->where('date >=', $start . ' 00:00:00');
        $this->db->where('date <=', $end . ' 23:59:59');
        $prod_stats = $this->db->get()->row_array();

        $task_total = (int)($task_stats['total'] ?? 0);
        $count_kph = (int)($prod_stats['count_kph'] ?? 0);

        $qa_percent = 100;
        if ($task_total > 0) {
            $qa_dat = max(0, $task_total - $count_kph);
            $qa_percent = round(($qa_dat / $task_total) * 100, 1);
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'task_total' => $task_total,
                'task_done' => (int)($task_stats['done'] ?? 0),
                'task_percent' => $task_percent,
                'count_kph' => $count_kph,
                'count_vp' => (int)($prod_stats['count_vp'] ?? 0),
                'count_lap_lai' => (int)($prod_stats['count_lap_lai'] ?? 0),
                'qa_percent' => $qa_percent
            ]
        ]);
        exit;
    }
    public function updateki()
    {
        // $this->db->where('id', 311);
        $assessments = $this->db->get('tbl_probationary_assessment')->result_array();
        $count = 0;
        foreach ($assessments as $item) {
            if (!empty($item['date_end'])) {
                $day = date('j', strtotime($item['date_end'])) - 1;
                $week = ceil($day / 7);
                $date_start = date('Y-m-d', strtotime($item['date_end'] . ' -7 days'));
                $this->db->where('id', $item['id']);
                $this->db->update('tbl_probationary_assessment', [
                    'type_ki' => 1,
                    'ki' => $week,
                    'date_start' => $date_start
                ]);
                $count++;
            }
        }
        echo "Đã cập nhật thành công type_ki, ki và date_start cho $count bản ghi!";
    }
    public function script_sync_all_assessments()
    {
        // Lấy tất cả các phiếu
        $this->db->where('run', 0);
        $assessments = $this->db->get('tbl_probationary_assessment')->result_array();

        // Lấy danh mục checklist
        $this->db->from('tbl_checklist_probationary_assessment');
        $checkList = $this->db->get()->result_array();

        // Lấy danh mục xếp loại
        $this->db->from('tbl_result_checklist');
        $ratings = $this->db->get()->result_array();

        $count = 0;
        foreach ($assessments as $pa) {
            $id = $pa['id'];
            $staff_id = $pa['staff_id'];

            // Dùng date_start/date_end lưu trong phiếu
            $start = $pa['date_start'];
            $end = $pa['date_end'];

            // 1. Task Stats
            $this->db->select("COUNT(t.id) as total, SUM(CASE WHEN t.status = 5 THEN 1 ELSE 0 END) as done");
            $this->db->from('tbltasks t');
            $this->db->join('tbltask_assigned ta', 'ta.taskid = t.id');
            $this->db->where('ta.staffid', $staff_id);
            $this->db->where('t.startdate >=', $start . ' 00:00:00');
            $this->db->where('t.startdate <=', $end . ' 23:59:59');
            $task_stats = $this->db->get()->row_array();



            $task_percent = !empty($task_stats['total']) ? round(($task_stats['done'] / $task_stats['total']) * 100, 1) : 0;

            // 2. Production Stats
            $this->db->select("SUM(CASE WHEN type_report = 1 THEN 1 ELSE 0 END) as count_kph, SUM(CASE WHEN type_report = 4 THEN 1 ELSE 0 END) as count_vp, SUM(CASE WHEN type_report = 2 THEN 1 ELSE 0 END) as count_lap_lai");
            $this->db->from('tblproduction_report');
            $this->db->where('staff_responsible', $staff_id);
            $this->db->where('date >=', $start . ' 00:00:00');
            $this->db->where('date <=', $end . ' 23:59:59');
            $prod_stats = $this->db->get()->row_array();
            $count_kph = (int)($prod_stats['count_kph'] ?? 0);
            $count_vp = (int)($prod_stats['count_vp'] ?? 0);
            $count_lap_lai = (int)($prod_stats['count_lap_lai'] ?? 0);

            $task_total = (int)($task_stats['total'] ?? 0);
            $qa_percent = 100;
            if ($task_total > 0) {
                $qa_dat = max(0, $task_total - $count_kph);
                $qa_percent = round(($qa_dat / $task_total) * 100, 1);
            }

            // Fetch existing items
            $existing_items = $this->db->get_where('tbl_probationary_assessment_item', ['probationary_assessment_id' => $id])->result_array();
            $items_by_checklist = [];
            foreach ($existing_items as $ei) {
                $items_by_checklist[$ei['check_list_id']] = $ei;
            }

            $point_b = 0;
            $point_c = 0;
            $point_d = 0;
            $hasGateFail = false;

            foreach ($checkList as $cl) {
                $cid = $cl['id'];
                $type = $cl['type'];
                $name = mb_strtolower($cl['name'], 'UTF-8');
                $max_point = (float)$cl['point'];
                $condition = trim($cl['conditions']);

                $is_new = false;
                if (isset($items_by_checklist[$cid])) {
                    $item = $items_by_checklist[$cid];
                } else {
                    $is_new = true;
                    $item = [
                        'probationary_assessment_id' => $id,
                        'check_list_id' => $cid,
                        'type_check_list' => $type,
                        'gate' => ($type == 'A') ? 1 : 0, // Mặc định pass gate
                        'percent' => 0,
                        'point' => 0,
                        'note' => ''
                    ];
                }

                if ($type == 'D') {
                    $item['point'] = $max_point;
                    $point_d += $item['point'];
                } elseif ($type == 'B' || $type == 'C') {
                    // 1. Xác định giá trị thực tế
                    if (strpos($name, 'hoàn thành công việc') !== false) {
                        $item['percent'] = $task_percent;
                    } elseif (strpos($name, 'chất lượng') !== false || strpos($name, 'qa') !== false) {
                        $item['percent'] = $qa_percent;
                    } elseif (strpos($name, 'lặp lại') !== false) {
                        $item['percent'] = $count_lap_lai;
                    } elseif (strpos($name, 'kỷ luật') !== false || strpos($name, 'vi phạm') !== false) {
                        $item['percent'] = $count_vp;
                    } elseif (strpos($name, 'tuân thủ nội quy') !== false) {
                        $item['percent'] = 100;
                    } elseif (strpos($name, 'tuân thủ sop') !== false || strpos($name, 'hồ sơ') !== false || strpos($name, 'báo cáo') !== false) {
                        $item['percent'] = 0; // Thực tế 0 → full điểm
                    } else {
                        $item['percent'] = 0;
                    }

                    // 2. Tính điểm dựa trên thực tế và chuẩn
                    if (strpos($name, 'chất lượng') !== false || strpos($name, 'qa') !== false || strpos($name, 'hoàn thành công việc') !== false || strpos($name, 'tuân thủ nội quy') !== false) {
                        $item['point'] = round(($item['percent'] * $max_point / 100), 1);
                    } elseif (strpos($name, 'tuân thủ sop') !== false || strpos($name, 'hồ sơ') !== false || strpos($name, 'báo cáo') !== false) {
                        // Tuân thủ SOP, Hồ sơ & báo cáo đầy đủ: thực tế = 0 → full điểm
                        if ($item['percent'] == 0) {
                            $item['point'] = $max_point;
                        } else {
                            $standard_num = (int)$condition;
                            $item['point'] = ($item['percent'] <= $standard_num) ? $max_point : 0;
                        }
                    } else {
                        // Kỷ luật, vi phạm, lặp lại... (các lỗi đếm được)
                        $standard_num = (int)$condition;
                        if ($item['percent'] <= $standard_num) {
                            $item['point'] = $max_point;
                        } else {
                            $item['point'] = 0;
                        }
                    }

                    if ($item['point'] > $max_point) $item['point'] = $max_point;
                    if ($item['point'] < 0) $item['point'] = 0;

                    if ($type == 'B') $point_b += $item['point'];
                    if ($type == 'C') $point_c += $item['point'];
                }

                if ($type == 'A' && $item['gate'] == '0') {
                    $hasGateFail = true;
                }

                if ($is_new) {
                    $this->db->insert('tbl_probationary_assessment_item', $item);
                } else {
                    $this->db->where('id', $item['id'])->update('tbl_probationary_assessment_item', [
                        'percent' => $item['percent'],
                        'point' => $item['point']
                    ]);
                }
            }

            $total_point = $point_b + $point_c + $point_d;

            $rating_list = 0;
            $rating_name = 'Chưa xếp loại';
            if ($hasGateFail) {
                foreach ($ratings as $rt) {
                    if (stripos($rt['name'], 'chấm dứt') !== false || $rt['id'] == 1) {
                        $rating_list = $rt['id'];
                        $rating_name = "KHÔNG ĐẠT (VI PHẠM GATE)";
                        break;
                    }
                }
            } else {
                foreach ($ratings as $rt) {
                    if ($total_point >= $rt['point_start'] && $total_point <= $rt['point_end']) {
                        $rating_list = $rt['id'];
                        $rating_name = mb_strtoupper($rt['name'], 'UTF-8');
                        break;
                    }
                }
            }
            $this->db->where('id', $id)->update('tbl_probationary_assessment', [
                'point_b' => $point_b,
                'point_c' => $point_c,
                'point_d' => $point_d,
                'point' => $total_point,
                'rating_list' => $rating_list,
                'rating' => $rating_name,
                'run' => 1,
            ]);

            $count++;
        }

        echo "Đã đồng bộ thành công $count phiếu đánh giá!";
    }
    public function detaildanhgia($id = 0)
    {
        if ($this->input->post()) {
            $dataPost = $this->input->post();
            $code = $dataPost['code'] ?? null;
            $staff_id = $dataPost['staff_id'] ?? 0;
            $level_target = $dataPost['level_target'] ?? 0;
            $level_achieved = $dataPost['level_achieved'] ?? 0;
            $rating_list = $dataPost['final_decision'] ?? 0;
            $date_start = $dataPost['date_start'] ?? null;
            $date_end = $dataPost['date_end'] ?? null;
            if (empty($code)) {
                $data['result'] = false;
                $data['message'] = lang('Vui lòng nhập mã phiếu');
                echo json_encode($data);
                die();
            }

            $this->db->where('code', $code);
            $this->db->where('id !=', $id);
            $this->db->from('tbl_probationary_assessment');
            $checkExists = $this->db->count_all_results();
            if (!empty($checkExists)) {
                $data['result'] = false;
                $data['message'] = lang('Mã phiếu đã tồn tại!');
                echo json_encode($data);
                die();
            }

            if (empty($staff_id)) {
                $data['result'] = false;
                $data['message'] = lang('Vui lòng chọn nhân viên');
                echo json_encode($data);
                die();
            }
            $this->db->from('tblstaff');
            $this->db->where('staffid', $staff_id);
            $dtStaff = $this->db->get()->row_array();
            if (empty($dtStaff)) {
                $data['result'] = false;
                $data['message'] = lang('Nhân viên không tồn tại!');
                echo json_encode($data);
                die();
            }

            $type = $dataPost['type'] ?? 1;
            $gate = $dataPost['gate'] ?? [];

            $point_b = 0;
            $point_c = 0;
            $point_d = 0;

            $arrItems = [];
            if (!empty($gate)) {
                foreach ($gate as $k => $v) {
                    $note = $dataPost['note_a'][$k] ?? null;
                    $arrItems[] = [
                        'type_check_list' => 'A',
                        'check_list_id' => $k,
                        'gate' => $v,
                        'note' => $note,
                    ];
                }
            }
            $percent_b = $dataPost['percent_b'] ?? [];
            if (!empty($percent_b)) {
                foreach ($percent_b as $k => $v) {
                    $point = !empty($dataPost['point_b'][$k]) ? $dataPost['point_b'][$k] : 0;
                    $point_b += $point;
                    $arrItems[] = [
                        'type_check_list' => 'B',
                        'check_list_id' => $k,
                        'percent' => ($v),
                        'point' => $point,
                    ];
                }
            }

            $percent_c = $dataPost['percent_c'] ?? [];
            if (!empty($percent_c)) {
                foreach ($percent_c as $k => $v) {
                    $point = !empty($dataPost['point_c'][$k]) ? $dataPost['point_c'][$k] : 0;
                    $point_c += $point;
                    $arrItems[] = [
                        'type_check_list' => 'C',
                        'check_list_id' => $k,
                        'percent' => ($v),
                        'point' => $point,
                    ];
                }
            }

            $point_d_post = $dataPost['point_d'] ?? [];
            if (!empty($point_d_post)) {
                foreach ($point_d_post as $k => $v) {
                    $point_d += !empty($v) ? $v : 0;
                    $arrItems[] = [
                        'type_check_list' => 'D',
                        'check_list_id' => $k,
                        'point' => ($v),
                    ];
                }
            }
            $point = (float)$point_b + (float)$point_c + (float)$point_d;

            // Recalculate rating if missing or to ensure consistency
            if (empty($rating_list)) {
                $this->db->from('tbl_result_checklist');
                $allRatings = $this->db->get()->result_array();

                // Check gate failure first
                $hasGateFail = false;
                if (!empty($gate)) {
                    foreach ($gate as $v) {
                        if ($v == '0') {
                            $hasGateFail = true;
                            break;
                        }
                    }
                }

                if ($hasGateFail) {
                    foreach ($allRatings as $r) {
                        if (stripos($r['name'], 'chấm dứt') !== false || stripos($r['name'], 'không đạt') !== false || $r['id'] == 1) {
                            $rating_list = $r['id'];
                            break;
                        }
                    }
                } else {
                    foreach ($allRatings as $r) {
                        if ($point >= (float)$r['point_start'] && $point <= (float)$r['point_end']) {
                            $rating_list = $r['id'];
                            break;
                        }
                    }
                }
            }

            $dtRating = get_table_where('tbl_result_checklist', ['id' => $rating_list], '', 'row_array');

            if (empty($id)) {
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staff_id,
                    'role_id' => $dtStaff['role'] ?? 0,
                    'note' => null,
                    'level_target' => $level_target,
                    'level_achieved' => $level_achieved,
                    'date_start' => ($date_start),
                    'date_end' => ($date_end),
                    'point_b' => $point_b,
                    'point_c' => $point_c,
                    'point_d' => $point_d,
                    'point' => $point,
                    'type' => $type,
                    'rating_list' => $rating_list,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];
            } else {
                $option = [
                    'code' => $code,
                    'staff_id' => $staff_id,
                    'role_id' => $dtStaff['role'] ?? 0,
                    'note' => null,
                    'level_target' => $level_target,
                    'level_achieved' => $level_achieved,
                    'date_start' => ($date_start),
                    'date_end' => ($date_end),
                    'point_b' => $point_b,
                    'point_c' => $point_c,
                    'point_d' => $point_d,
                    'point' => $point,
                    'type' => $type,
                    'rating_list' => $rating_list,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                ];
            }

            if (empty($id)) {
                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
            } else {
                $this->db->where('id', $id);
                $this->db->update('tbl_probationary_assessment', $option);
                $insert_id = $id;
            }
            if ($insert_id) {
                $this->db->where('tbl_probationary_assessment_item.probationary_assessment_id', $id);
                $this->db->delete('tbl_probationary_assessment_item');

                if (!empty($arrItems)) {
                    foreach ($arrItems as $k => $v) {
                        $v['probationary_assessment_id'] = $insert_id;
                        $this->db->insert('tbl_probationary_assessment_item', $v);
                    }
                }
                if ($type == 1) {
                    updateReference('probationary_assessment');
                } else {
                    updateReference('probationary_assessment_ct');
                }
                $data['result'] = true;
                if (empty($id)) {
                    $data['message'] = lang('Thêm mới thành công');
                } else {
                    $data['message'] = lang('Cập nhập thành công');
                }
                $data['type'] = $type;
            } else {
                $data['result'] = false;
                if (empty($id)) {
                    $data['message'] = lang('Thêm mới thất bại');
                } else {
                    $data['message'] = lang('Cập nhập thất bại');
                }
            }
            echo json_encode($data);
            die();
        }
        if (empty($id)) {
            if (!$this->preAddProbationaryAssessment) {
                accessDenied($js = true);
            }

            if ($this->type == 1) {
                $data['title'] = _l('Tạo mới phiếu đánh giá nhân viên (TV)');
            } else {
                $data['title'] = _l('Tạo mới phiếu đánh giá nhân viên (CT)');
            }
        } else {
            if (!$this->preEditProbationaryAssessment) {
                accessDenied($js = true);
            }
            if ($this->type == 1) {
                $data['title'] = _l('Chỉnh sửa phiếu đánh giá nhân viên (TV)');
            } else {
                $data['title'] = _l('Chỉnh sửa phiếu đánh giá nhân viên (CT)');
            }

            $this->db->select('tbl_probationary_assessment.*,tblroles.name as name_role,tbl_room.name as name_room');
            $this->db->from('tbl_probationary_assessment');
            $this->db->join('tblroles', 'tblroles.roleid = tbl_probationary_assessment.role_id', 'left');
            $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
            $this->db->where('tbl_probationary_assessment.id', $id);
            $dtData = $this->db->get()->row_array();

            $this->db->from('tbl_probationary_assessment_item');
            $this->db->where('probationary_assessment_id', $id);
            $dtDataItems = $this->db->get()->result_array();
            $checkListItems = [];
            foreach ($dtDataItems as $row) {
                $checkListItems[$row['type_check_list']][] = $row;
            }

            $mappedItems = [];

            foreach ($checkListItems as $type => $items) {
                foreach ($items as $item) {
                    $mappedItems[$type][$item['check_list_id']] = $item;
                }
            }
        }
        $this->db->from('tbl_checklist_probationary_assessment');
        $dtChecklist = $this->db->get()->result_array();
        $checkList = [];
        foreach ($dtChecklist as $row) {
            $checkList[$row['type']][] = $row;
        }
        $data['checkList'] = $checkList;
        $data['levelChecklist'] = get_table_where('tbl_level_checklist');
        $data['resultChecklist'] = get_table_where('tbl_result_checklist');
        $data['id'] = $id;
        $data['dtData'] = $dtData ?? null;
        $data['checkListItems'] = $mappedItems ?? null;
        $data['type'] = $this->type;
        if ($this->type == 1) {
            $code = getReference('probationary_assessment');
        } else {
            $code = getReference('probationary_assessment_ct');
        }
        $data['code'] = $code;

        // Compute ky_danh_gia label
        $ky_label = '';
        $staff_id_for_ky = $dtData['staff_id'] ?? 0;
        $year_for_ky = date('Y');
        if (!empty($dtData['date_start'])) {
            $year_for_ky = date('Y', strtotime($dtData['date_start']));
        }

        if (!empty($staff_id_for_ky)) {
            $ky_map = $this->_compute_ky_map($year_for_ky);
            if ($id > 0 && isset($ky_map[$id])) {
                $ky_label = $ky_map[$id];
            } else {
                $cnt = $this->db->where('type', 2)->where('staff_id', $staff_id_for_ky)
                    ->where('YEAR(COALESCE(date_start, date_created)) = ' . (int)$year_for_ky, null, false)
                    ->count_all_results('tbl_probationary_assessment');
                $next_index = $cnt + 1;
                if ($next_index <= 12) $ky_label = '3 tháng';
                elseif ($next_index <= 24) $ky_label = '6 tháng';
                elseif ($next_index <= 36) $ky_label = '9 tháng';
                else $ky_label = '12 tháng';
            }
        }

        // Date range: mỗi phiếu = 7 ngày, date_end = hôm nay, date_start = trừ 7 ngày
        $ky_date_end = date('Y-m-d');
        $ky_date_start = date('Y-m-d', strtotime('-7 days'));

        $data['ky_danh_gia'] = $ky_label;
        $data['ky_date_start'] = $ky_date_start;
        $data['ky_date_end'] = $ky_date_end;

        $this->load->view('admin/probationary_assessment/detail', $data);
    }

    public function cronEvaluateCTTuan()
    {
        $today = date('Y-m-d');
        // $today = '2026-05-11';
        if (date('N', strtotime($today)) != 1) {
            return;
        }
        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $this->db->where('tblstaff.check_salary', 0);
        $this->db->where('tblstaff.status_work', 1);
        // $this->db->where('tblroles.day_evaluate != 0', null, false);
        $dtStaff = $this->db->get()->result_array();
        $type_ki = 1;

        $date_start = date('Y-m-d', strtotime(date('Y-m-d') . ' -7 days'));
        $date_end = date('Y-m-d');
        $ki = 0;
        $day = date('j', strtotime($date_end));
        $week = ceil($day / 7);
        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $arrItems = [];
                $staffid = $value['staffid'];
                $this->db->where('staff_id', $staffid);
                $this->db->where('date_end', $date_end);
                $this->db->where('type_ki', 1);
                $check_data = $this->db->get('tbl_probationary_assessment')->row_array();
                if (!empty($check_data)) {
                    continue;
                }
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' => $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'ki' => $week,
                    'type_ki' => $type_ki,
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => 0,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                    $count++;
                }
            }
        }
        echo $count;
    }
    public function cronEvaluateCTThang()
    {
        $today = date('Y-m-d');
        $md = date('m-d', strtotime($today));
        if (!in_array($md, ['03-31', '06-30', '09-30', '12-31'])) {
            return;
        }
        $this->db->select('tblstaff.*, tblroles.day_evaluate');
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $this->db->where('tblstaff.check_salary', 0);
        $this->db->where('tblstaff.status_work', 1);
        $this->db->where('tblroles.day_evaluate != 0', null, false);
        $dtStaff = $this->db->get()->result_array();
        $type_ki = 2;
        $month = date('m');
        $date_end = date('Y-m-d'); // Khai báo date_end còn thiếu
        $date_start = date('Y-m-d', strtotime(date('Y-m-d') . ' -' . $month . ' month'));
        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $staffid = $value['staffid'];

                // Kiểm tra nếu đã tồn tại phiếu cùng ngày kết thúc
                $this->db->where('staff_id', $staffid);
                $this->db->where('date_end', $date_end);
                $this->db->where('type_ki', 2);
                $check_data = $this->db->get('tbl_probationary_assessment')->row_array();
                if (!empty($check_data)) {
                    continue;
                }

                $arrItems = [];
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' => $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'ki' => $month,
                    'type_ki' => $type_ki,
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => 0,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                    $count++;
                }
            }
        }
        echo $count;
    }
}
