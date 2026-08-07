<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_management extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('audit_management_model');
    }

    /**
     * Dashboard - Main view
     */
    public function index()
    {
        $data = [];
        $data['title'] = 'Quản lý Audit - FOSO Quality Center';

        // Get statistics
        $data['stats'] = [
            'total_this_month' => $this->audit_management_model->getAuditCountByMonth(date('Y-m')),
            'critical_issues' => $this->audit_management_model->getCriticalIssuesCount(),
            'completion_rate' => $this->audit_management_model->getCompletionRate()
        ];
        $data['room'] = get_table_where('tbl_room');
        $data['staff_list_all'] = $this->site_model->getStaffAll();

        $this->load->view('admin/audit_management/dashboard', $data);
    }

    /**
     * Get Audit List (DataTables server-side)
     */
    public function getAuditList()
    {
        $aColumns = [
            'tbl_audit.id as id',
            'tbl_audit.audit_code as audit_code',
            'tbl_audit.department as department',
            'tbl_audit.audit_date as audit_date',
            'tbl_audit.team_leader as team_leader',
            'tbl_audit.result_percentage as result_percentage',
            '2 as has_issues',
            '1 as production_report',
            'tbl_audit.status as status',
            'tbl_audit.id as actions'
        ];

        $sIndexColumn = 'id';
        $sTable = 'tbl_audit';
        $where = [];
        $filter = [];

        // Get filters from request
        $search_filter = $this->input->post('search_filter');
        $status_filter = $this->input->post('status_filter');
        $department_filter = $this->input->post('department_filter');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        // Build where clause for filters
        if (!empty($search_filter)) {
            $where[] = 'AND (tbl_audit.audit_code LIKE "%' . $search_filter . '%" OR tbl_audit.team_leader LIKE "%' . $search_filter . '%")';
        }
        if (!empty($status_filter)) {
            $where[] = 'AND tbl_audit.status = "' . $status_filter . '"';
        }
        if (!empty($department_filter)) {
            $where[] = 'AND tbl_audit.dept_id = "' . $department_filter . '"';
        }
        if (!empty($date_from)) {
            $where[] = 'AND tbl_audit.audit_date >= "' . $date_from . '"';
        }
        if (!empty($date_to)) {
            $where[] = 'AND tbl_audit.audit_date <= "' . $date_to . '"';
        }

        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $filter, '', []);
        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];
            foreach ($aColumns as $v) {
                $this->db->dbprefix = '';
                $this->db->select('tblproduction_report.id,tblproduction_report.reference_no');
                $this->db->where('audit_id', $aRow['id']);
                $production_report = $this->db->get('tblproduction_report')->result_array();
                if ($v == 'production_report') {
                    $row[] = $production_report;
                } elseif ($v == 'has_issues') {
                    $row[] = count($production_report);
                } else {
                    $row[] = $aRow[$v];
                }
            }
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    /**
     * Create new audit session
     */
    public function create()
    {
        if ($this->input->post()) {
            // Validation
            $this->form_validation->set_rules('dept_id', 'Phòng ban', 'required');
            $this->form_validation->set_rules('auditor_id', 'Trưởng đoàn', 'required');

            if ($this->form_validation->run() == true) {
                // Get department info
                $dept_id = $this->input->post('dept_id');
                $dept_name = $this->getDepartmentName($dept_id);

                // Generate unique audit code
                $audit_code = 'AUD-' . date('Y') . '-' . str_pad($this->audit_management_model->getNextAuditNumber(), 3, '0', STR_PAD_LEFT);
                $team_leader = get_staff_full_name($this->input->post('auditor_id'));
                $postData = [
                    'audit_code' => $audit_code,
                    'dept_id' => $dept_id,
                    'department' => $dept_name,
                    'auditor_id' => $this->input->post('auditor_id'),
                    'team_leader' => $team_leader,
                    'audit_date' => date('Y-m-d'),
                    'status' => 'IN_PROGRESS',
                    'created_by_staff_id' => get_staff_user_id(),
                    'created_by' => get_staff_full_name(get_staff_user_id()),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $audit_id = $this->audit_management_model->insertAudit($postData);

                if ($audit_id > 0) {
                    // Get template and create checklist items
                    $template = $this->getAuditTemplate();
                    $this->createChecklistItems($audit_id, $dept_id, $template);

                    echo json_encode([
                        'result' => 1,
                        'message' => 'Tạo phiếu audit thành công!',
                        'audit_id' => $audit_id,
                        'redirect' => admin_url('audit_management/session/' . $audit_id)
                    ]);
                } else {
                    echo json_encode([
                        'result' => 0,
                        'message' => 'Có lỗi xảy ra khi tạo phiếu audit'
                    ]);
                }
            } else {
                echo json_encode([
                    'result' => 0,
                    'message' => validation_errors()
                ]);
            }
            return;
        }

        // Load form view
        $data['title'] = 'Tạo phiếu Audit mới';
        $data['departments'] = $this->getDepartments();
        $this->load->view('admin/audit_management/create', $data);
    }

    /**
     * Audit session view (perform audit)
     */
    public function session($audit_id)
    {
        $audit = $this->audit_management_model->getAuditById($audit_id);

        if (!$audit) {
            show_404();
            return;
        }

        $data['audit'] = $audit;
        $data['title'] = 'Phiếu Audit: ' . $audit->department;
        $data['checklist'] = $this->audit_management_model->getChecklistByAuditId($audit_id);
        $data['sections'] = $this->groupChecklistBySections($data['checklist']);
        $this->load->view('admin/audit_management/session', $data);
    }

    /**
     * Save audit item response
     */
    public function saveAuditItem()
    {
        $item_id = $this->input->post('item_id');
        $status = $this->input->post('status'); // 'yes' or 'no'
        $note = $this->input->post('note');

        // Get current item data for logging
        $this->db->where('id', $item_id);
        $current_item = $this->db->get('tbl_audit_checklist')->row();
        $old_status = $current_item ? $current_item->status : null;

        $updateData = [
            'status' => $status,
            'note' => $note,
            'updated_by_staff_id' => get_staff_user_id(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->audit_management_model->updateChecklistItem($item_id, $updateData);

        // Log the status change
        if ($success && $current_item) {
            $staff_id = get_staff_user_id();
            $staff_name = get_staff_full_name($staff_id);

            $action_description = "Chuyển trạng thái từ ";
            $action_description .= $old_status ? strtoupper($old_status) : "CHƯA CHECK";
            $action_description .= " sang " . strtoupper($status);
            $action_description .= " cho tiêu chí: " . $current_item->item_text;

            $log_data = [
                'audit_id' => $current_item->audit_id,
                'audit_item_id' => $item_id,
                'action_type' => 'STATUS_CHANGE',
                'action_description' => $action_description,
                'old_value' => json_encode(['status' => $old_status]),
                'new_value' => json_encode(['status' => $status]),
                'staff_id' => $staff_id,
                'staff_name' => $staff_name,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_audit_history_log', $log_data);
        }

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Lưu thành công' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Complete audit and calculate results
     */
    public function completeAudit()
    {
        $audit_id = $this->input->post('audit_id');

        // Calculate results
        $checklist = $this->audit_management_model->getChecklistByAuditId($audit_id);
        $total = count($checklist);
        $yes_count = 0;
        $no_count = 0;
        $critical_no_count = 0;

        foreach ($checklist as $item) {
            if ($item->status == 'yes') {
                $yes_count++;
            } elseif ($item->status == 'no') {
                $no_count++;
                if ($item->critical == 1) {
                    $critical_no_count++;
                }
            }
        }

        $completion_percentage = $total > 0 ? round(($yes_count / $total) * 100, 2) : 0;

        // Update audit
        $updateData = [
            'status' => 'COMPLETED',
            'result_percentage' => $completion_percentage,
            'total_items' => $total,
            'yes_count' => $yes_count,
            'no_count' => $no_count,
            'critical_no_count' => $critical_no_count,
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_by_staff_id' => get_staff_user_id(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->audit_management_model->updateAudit($audit_id, $updateData);

        // Create CAPA for critical NO items
        if ($critical_no_count > 0) {
            $this->createCapaForCriticalIssues($audit_id);
        }

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Hoàn thành phiếu audit thành công!' : 'Có lỗi xảy ra',
            'data' => [
                'completion_percentage' => $completion_percentage,
                'critical_issues' => $critical_no_count
            ]
        ]);
    }

    /**
     * Configuration - Template Management
     */
    public function config()
    {
        $data['title'] = 'Cấu hình Biểu mẫu Audit';
        $data['template'] = $this->getAuditTemplate();

        $data['room'] = get_table_where('tbl_room');
        $data['departments'] = get_table_where('tbldepartments', ['type' => 0]);
        $this->load->view('admin/audit_management/config', $data);
    }

    /**
     * Create CAPA form
     */
    public function createCapa()
    {
        $item_id = $this->input->get('item_id');
        $audit_id = $this->input->get('audit_id');

        // Get checklist item details
        $item = $this->db->where('id', $item_id)->get('tbl_audit_checklist')->row();
        if (!$item) {
            show_404();
            return;
        }

        // Get audit details
        $audit = $this->audit_management_model->getAuditById($audit_id);

        $data['item'] = $item;
        $data['audit'] = $audit;
        $data['title'] = 'Tạo phiếu CAPA';

        $this->load->view('admin/audit_management/create_capa', $data);
    }

    /**
     * Save CAPA
     */
    public function saveCapa()
    {
        $item_id = $this->input->post('item_id');
        $audit_id = $this->input->post('audit_id');

        // Generate CAPA code
        $capa_code = 'CAPA-' . date('Ymd') . '-' . str_pad($audit_id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($item_id, 3, '0', STR_PAD_LEFT);

        $capaData = [
            'capa_code' => $capa_code,
            'audit_id' => $audit_id,
            'id_audit_item' => $item_id,
            'issue_description' => $this->input->post('issue_description'),
            'root_cause' => $this->input->post('root_cause'),
            'corrective_action' => $this->input->post('corrective_action'),
            'preventive_action' => $this->input->post('preventive_action'),
            'assigned_to' => $this->input->post('assigned_to'),
            'due_date' => $this->input->post('due_date'),
            'status' => 'OPEN',
            'created_by_staff_id' => get_staff_user_id(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $insert = $this->db->insert('tbl_audit_capa', $capaData);

        if ($insert) {
            set_alert('success', 'Tạo phiếu CAPA thành công!');
            redirect(admin_url('audit_management/session/' . $audit_id));
        } else {
            set_alert('danger', 'Có lỗi xảy ra!');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * View CAPA
     */
    public function viewCapa($capa_id)
    {
        $capa = $this->db->where('id', $capa_id)->get('tbl_audit_capa')->row();
        if (!$capa) {
            show_404();
            return;
        }

        $data['capa'] = $capa;
        $data['title'] = 'Chi tiết CAPA: ' . $capa->capa_code;

        $this->load->view('admin/audit_management/view_capa', $data);
    }

    /**
     * Complete CAPA via AJAX
     */
    public function completeCapaAjax()
    {
        $capa_id = $this->input->post('capa_id');

        $updateData = [
            'status' => 'COMPLETED',
            'completed_at' => date('Y-m-d H:i:s'),
            'completed_by_staff_id' => get_staff_user_id()
        ];

        $success = $this->db->where('id', $capa_id)->update('tbl_audit_capa', $updateData);

        echo json_encode([
            'success' => $success
        ]);
    }

    /**
     * Get template from database or default
     */
    public function getTemplateData()
    {
        $template = $this->getAuditTemplate();
        echo json_encode([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Edit section
     */
    public function editSection($section_index)
    {
        $template = $this->getAuditTemplate();

        if (!isset($template[$section_index])) {
            show_404();
            return;
        }

        $data['title'] = 'Chỉnh sửa Phần - ' . $template[$section_index]['title'];
        $data['section'] = $template[$section_index];
        $data['section_index'] = $section_index;
        $this->load->view('admin/audit_management/edit_section', $data);
    }

    /**
     * Save section configuration
     */
    public function saveSection()
    {
        $section_index = $this->input->post('section_index');
        $section_data = $this->input->post('section_data'); // JSON string

        if ($section_index === null || empty($section_data)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
            return;
        }

        $template = $this->getAuditTemplate();
        $section_data_array = json_decode($section_data, true);

        if ($section_data_array === null) {
            echo json_encode([
                'result' => 0,
                'message' => 'JSON không hợp lệ'
            ]);
            return;
        }

        // Update template
        $template[$section_index] = $section_data_array;

        // Save to database
        $success = $this->saveAuditTemplate($template);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Lưu cấu hình thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Add new section to template
     */
    public function addSection()
    {
        $section_data = $this->input->post('section_data'); // JSON string

        if (empty($section_data)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
            return;
        }

        $section_data_array = json_decode($section_data, true);

        if ($section_data_array === null) {
            echo json_encode([
                'result' => 0,
                'message' => 'JSON không hợp lệ'
            ]);
            return;
        }

        // Validate required fields
        if (empty($section_data_array['title']) || empty($section_data_array['id'])) {
            echo json_encode([
                'result' => 0,
                'message' => 'Thiếu thông tin bắt buộc (title, id)'
            ]);
            return;
        }

        $template = $this->getAuditTemplate();

        // Check if ID already exists
        foreach ($template as $section) {
            if ($section['id'] === $section_data_array['id']) {
                echo json_encode([
                    'result' => 0,
                    'message' => 'Mã ID đã tồn tại! Vui lòng chọn mã khác.'
                ]);
                return;
            }
        }

        // Add new section to the end
        $template[] = $section_data_array;

        // Save to database
        $success = $this->saveAuditTemplate($template);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Thêm phần mới thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Check if section already exists
     */
    public function checkExistingSection()
    {
        $roman_type = $this->input->post('roman_type');
        $room = $this->input->post('room');
        $department = $this->input->post('department');

        $template = $this->getAuditTemplate();

        foreach ($template as $index => $section) {
            $section_roman = isset($section['romanType']) ? $section['romanType'] : (isset($section['id']) ? explode('.', $section['id'])[0] : '');

            if ($section_roman === $roman_type) {
                // For type II - check room match
                if ($roman_type === 'II' && !empty($room)) {
                    if (isset($section['room']) && $section['room'] == $room) {
                        echo json_encode([
                            'result' => 1,
                            'section' => $section,
                            'index' => $index
                        ]);
                        return;
                    }
                }
                // For type III - check department match
                else if ($roman_type === 'III' && !empty($department)) {
                    if (isset($section['department']) && $section['department'] == $department) {
                        echo json_encode([
                            'result' => 1,
                            'section' => $section,
                            'index' => $index
                        ]);
                        return;
                    }
                }
                // For type I, IV, V - just check roman type
                else if ($roman_type === 'I' || $roman_type === 'IV' || $roman_type === 'V') {
                    echo json_encode([
                        'result' => 1,
                        'section' => $section,
                        'index' => $index
                    ]);
                    return;
                }
            }
        }

        echo json_encode([
            'result' => 0,
            'message' => 'Không tìm thấy section'
        ]);
    }

    /**
     * Get section data by index
     */
    public function getSectionData()
    {
        $index = $this->input->post('index');

        if (!is_numeric($index)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Index không hợp lệ'
            ]);
            return;
        }

        $template = $this->getAuditTemplate();

        if (!isset($template[$index])) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không tìm thấy section'
            ]);
            return;
        }

        echo json_encode([
            'result' => 1,
            'section' => $template[$index]
        ]);
    }

    /**
     * Update existing section
     */
    public function updateSection()
    {
        $index = $this->input->post('index');
        $section_data = $this->input->post('section_data');

        if (!is_numeric($index) || empty($section_data)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
            return;
        }

        $section_data_array = json_decode($section_data, true);

        if ($section_data_array === null) {
            echo json_encode([
                'result' => 0,
                'message' => 'JSON không hợp lệ'
            ]);
            return;
        }

        $template = $this->getAuditTemplate();

        if (!isset($template[$index])) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không tìm thấy section để cập nhật'
            ]);
            return;
        }

        // Update section
        $template[$index] = $section_data_array;

        // Save to database
        $success = $this->saveAuditTemplate($template);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Cập nhật thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        // Load PHPExcel library
        $this->load->library('excel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Get room and department data for reference
        $room = $this->db->select('id, name')->get('tbl_room')->result_array();
        $departments = $this->db->select('departmentid, name')->get('tbldepartments')->result_array();

        // Set header
        $sheet->setCellValue('A1', 'Roman Type');
        $sheet->setCellValue('B1', 'Tên Ban');
        $sheet->setCellValue('C1', 'Tên Phòng');
        $sheet->setCellValue('D1', 'Nội dung tiêu chí');

        // Style header
        $styleArray = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '4a5568'))
        );
        $sheet->getStyle('A1:D1')->applyFromArray($styleArray);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(60);

        // Add comprehensive sample data
        $sampleData = [
            // Type I - CHECKLIST CHUNG
            ['I', '', '', 'Kiểm tra vệ sinh khu vực làm việc'],
            ['I', '', '', 'Kiểm tra thiết bị an toàn lao động'],
            ['I', '', '', 'Kiểm tra bảng chỉ dẫn an toàn'],
            ['I', '', '', 'Kiểm tra hệ thống phòng cháy chữa cháy'],
            ['I', '', '', 'Kiểm tra lối thoát hiểm'],

            // Type II - CHECKLIST THEO BAN
            ['II', !empty($room[0]['name']) ? $room[0]['name'] : 'Ban Sản xuất', '', 'Kiểm tra máy móc thiết bị sản xuất'],
            ['II', !empty($room[0]['name']) ? $room[0]['name'] : 'Ban Sản xuất', '', 'Kiểm tra nguyên vật liệu đầu vào'],
            ['II', !empty($room[0]['name']) ? $room[0]['name'] : 'Ban Sản xuất', '', 'Kiểm tra quy trình sản xuất'],
            ['II', !empty($room[1]['name']) ? $room[1]['name'] : 'Ban Kế hoạch', '', 'Kiểm tra kế hoạch sản xuất tháng'],
            ['II', !empty($room[1]['name']) ? $room[1]['name'] : 'Ban Kế hoạch', '', 'Kiểm tra báo cáo tiến độ'],

            // Type III - CHECKLIST THEO PHÒNG
            ['III', '', !empty($departments[0]['name']) ? $departments[0]['name'] : 'Phòng Kế toán', 'Kiểm tra chứng từ kế toán'],
            ['III', '', !empty($departments[0]['name']) ? $departments[0]['name'] : 'Phòng Kế toán', 'Kiểm tra sổ sách kế toán'],
            ['III', '', !empty($departments[0]['name']) ? $departments[0]['name'] : 'Phòng Kế toán', 'Kiểm tra báo cáo tài chính'],
            ['III', '', !empty($departments[1]['name']) ? $departments[1]['name'] : 'Phòng Nhân sự', 'Kiểm tra hồ sơ nhân viên'],
            ['III', '', !empty($departments[1]['name']) ? $departments[1]['name'] : 'Phòng Nhân sự', 'Kiểm tra hợp đồng lao động'],
            ['III', '', !empty($departments[1]['name']) ? $departments[1]['name'] : 'Phòng Nhân sự', 'Kiểm tra bảo hiểm xã hội'],

            // Type IV - KIỂM SOÁT HỐ SƠ CHUNG
            ['IV', '', '', 'Kiểm tra hồ sơ chất lượng sản phẩm'],
            ['IV', '', '', 'Kiểm tra hồ sơ kiểm định thiết bị'],
            ['IV', '', '', 'Kiểm tra hồ sơ bảo trì bảo dưỡng'],

            // Type V - KIỂM SOÁT HỐ SƠ CHUNG
            ['V', '', '', 'Kiểm tra hồ sơ ISO 9001'],
            ['V', '', '', 'Kiểm tra hồ sơ 5S'],
            ['V', '', '', 'Kiểm tra hồ sơ đào tạo nhân viên'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->setCellValue('C' . $row, $data[2]);
            $sheet->setCellValue('D' . $row, $data[3]);
            $row++;
        }

        // Add notes after sample data
        $noteRow = $row + 1;
        $sheet->setCellValue('A' . $noteRow, 'Ghi chú:');
        $sheet->setCellValue('A' . ($noteRow + 1), '- Roman Type: I, II, III, IV, V');
        $sheet->setCellValue('A' . ($noteRow + 2), '- Tên Ban: Nhập đúng tên ban từ Sheet 2 (chỉ dùng cho type II), để trống nếu không áp dụng');
        $sheet->setCellValue('A' . ($noteRow + 3), '- Tên Phòng: Nhập đúng tên phòng từ Sheet 3 (chỉ dùng cho type III), để trống nếu không áp dụng');
        $sheet->setCellValue('A' . ($noteRow + 4), '- Nội dung tiêu chí: Nội dung cần kiểm tra');
        $sheet->setCellValue('A' . ($noteRow + 5), '- Bạn có thể thêm, sửa, xóa các dòng dữ liệu mẫu theo nhu cầu');

        $sheet->getStyle('A' . $noteRow . ':A' . ($noteRow + 5))->getFont()->setItalic(true)->getColor()->setRGB('64748b');

        // Set sheet title
        $sheet->setTitle('Template Audit');

        // Create Sheet 2 - Danh sách Ban
        $objPHPExcel->createSheet(1);
        $sheet2 = $objPHPExcel->setActiveSheetIndex(1);
        $sheet2->setTitle('Danh sách Ban');

        // Header for room list
        $sheet2->setCellValue('A1', 'STT');
        $sheet2->setCellValue('B1', 'Tên Ban');
        $sheet2->getStyle('A1:B1')->applyFromArray($styleArray);
        $sheet2->getColumnDimension('A')->setWidth(10);
        $sheet2->getColumnDimension('B')->setWidth(40);

        // Add room data
        $row = 2;
        $stt = 1;
        foreach ($room as $r) {
            $sheet2->setCellValue('A' . $row, $stt);
            $sheet2->setCellValue('B' . $row, $r['name']);
            $row++;
            $stt++;
        }

        // Create Sheet 3 - Danh sách Phòng
        $objPHPExcel->createSheet(2);
        $sheet3 = $objPHPExcel->setActiveSheetIndex(2);
        $sheet3->setTitle('Danh sách Phòng');

        // Header for department list
        $sheet3->setCellValue('A1', 'STT');
        $sheet3->setCellValue('B1', 'Tên Phòng');
        $sheet3->getStyle('A1:B1')->applyFromArray($styleArray);
        $sheet3->getColumnDimension('A')->setWidth(10);
        $sheet3->getColumnDimension('B')->setWidth(40);

        // Add department data
        $row = 2;
        $stt = 1;
        foreach ($departments as $d) {
            $sheet3->setCellValue('A' . $row, $stt);
            $sheet3->setCellValue('B' . $row, $d['name']);
            $row++;
            $stt++;
        }

        // Set active sheet back to first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Output file
        $filename = 'Audit_Template_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Export current template data as sample Excel
     */
    public function exportSampleData()
    {
        // Load PHPExcel library
        $this->load->library('excel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Get current template data
        $template = $this->getAuditTemplate();

        // Get room and department data for lookup
        $room_data = $this->db->select('id, name')->get('tbl_room')->result_array();
        $dept_data = $this->db->select('departmentid, name')->get('tbldepartments')->result_array();

        // Create lookup maps
        $room_map = [];
        foreach ($room_data as $r) {
            $room_map[$r['id']] = $r['name'];
        }

        $dept_map = [];
        foreach ($dept_data as $d) {
            $dept_map[$d['departmentid']] = $d['name'];
        }

        // Set header
        $sheet->setCellValue('A1', 'Roman Type');
        $sheet->setCellValue('B1', 'Tên Ban');
        $sheet->setCellValue('C1', 'Tên Phòng');
        $sheet->setCellValue('D1', 'Nội dung tiêu chí');

        // Style header
        $styleArray = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '4a5568'))
        );
        $sheet->getStyle('A1:D1')->applyFromArray($styleArray);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(50);

        // Fill data from template
        $row = 2;
        foreach ($template as $section) {
            $romanType = isset($section['romanType']) ? $section['romanType'] : (isset($section['id']) ? explode('.', $section['id'])[0] : '');
            $roomId = isset($section['room']) ? $section['room'] : null;
            $deptId = isset($section['department']) ? $section['department'] : null;
            $items = isset($section['items']) ? $section['items'] : [];

            // Get room and department names
            $roomName = $roomId && isset($room_map[$roomId]) ? $room_map[$roomId] : '';
            $deptName = $deptId && isset($dept_map[$deptId]) ? $dept_map[$deptId] : '';

            // Add each item as a row
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $romanType);
                $sheet->setCellValue('B' . $row, $roomName);
                $sheet->setCellValue('C' . $row, $deptName);
                $sheet->setCellValue('D' . $row, isset($item['text']) ? $item['text'] : '');
                $row++;
            }
        }

        // Set sheet title
        $sheet->setTitle('Data Mẫu Audit');

        // Create Sheet 2 - Danh sách Ban
        $objPHPExcel->createSheet(1);
        $sheet2 = $objPHPExcel->setActiveSheetIndex(1);
        $sheet2->setTitle('Danh sách Ban');

        $sheet2->setCellValue('A1', 'STT');
        $sheet2->setCellValue('B1', 'Tên Ban');
        $sheet2->getStyle('A1:B1')->applyFromArray($styleArray);
        $sheet2->getColumnDimension('A')->setWidth(10);
        $sheet2->getColumnDimension('B')->setWidth(40);

        $row = 2;
        $stt = 1;
        foreach ($room_data as $r) {
            $sheet2->setCellValue('A' . $row, $stt);
            $sheet2->setCellValue('B' . $row, $r['name']);
            $row++;
            $stt++;
        }

        // Create Sheet 3 - Danh sách Phòng
        $objPHPExcel->createSheet(2);
        $sheet3 = $objPHPExcel->setActiveSheetIndex(2);
        $sheet3->setTitle('Danh sách Phòng');

        $sheet3->setCellValue('A1', 'STT');
        $sheet3->setCellValue('B1', 'Tên Phòng');
        $sheet3->getStyle('A1:B1')->applyFromArray($styleArray);
        $sheet3->getColumnDimension('A')->setWidth(10);
        $sheet3->getColumnDimension('B')->setWidth(40);

        $row = 2;
        $stt = 1;
        foreach ($dept_data as $d) {
            $sheet3->setCellValue('A' . $row, $stt);
            $sheet3->setCellValue('B' . $row, $d['name']);
            $row++;
            $stt++;
        }

        // Set active sheet back to first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Output file
        $filename = 'Audit_Sample_Data_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Import template from Excel
     */
    public function importTemplate()
    {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Vui lòng chọn file để upload!'
            ]);
            return;
        }

        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, ['xlsx', 'xls'])) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'File không đúng định dạng! Chỉ chấp nhận .xlsx hoặc .xls'
            ]);
            return;
        }

        // Load PHPExcel library
        $this->load->library('excel');

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file_tmp);
            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            // Get room and department data for lookup
            $room_data = $this->db->select('id, name')->get('tbl_room')->result_array();
            $dept_data = $this->db->select('departmentid, name')->get('tbldepartments')->result_array();

            // Create lookup maps
            $room_map = [];
            foreach ($room_data as $r) {
                $room_map[trim(strtolower($r['name']))] = $r['id'];
            }

            $dept_map = [];
            foreach ($dept_data as $d) {
                $dept_map[trim(strtolower($d['name']))] = $d['departmentid'];
            }

            $template = $this->getAuditTemplate();
            $imported_count = 0;
            $error_rows = [];

            // Group items by section (romanType + room/department)
            $sections_data = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $romanType = trim($sheet->getCell('A' . $row)->getValue());
                $roomName = trim($sheet->getCell('B' . $row)->getValue());
                $departmentName = trim($sheet->getCell('C' . $row)->getValue());
                $itemText = trim($sheet->getCell('D' . $row)->getValue());

                // Skip empty rows
                if (empty($romanType) && empty($itemText)) {
                    continue;
                }

                // Validate roman type
                if (!in_array($romanType, ['I', 'II', 'III', 'IV', 'V'])) {
                    $error_rows[] = "Dòng $row: Roman Type không hợp lệ";
                    continue;
                }

                // Lookup room ID from name
                $roomId = null;
                if (!empty($roomName)) {
                    $roomKey = trim(strtolower($roomName));
                    if (isset($room_map[$roomKey])) {
                        $roomId = $room_map[$roomKey];
                    } else {
                        $error_rows[] = "Dòng $row: Không tìm thấy ban '$roomName'";
                        continue;
                    }
                }

                // Lookup department ID from name
                $departmentId = null;
                if (!empty($departmentName)) {
                    $deptKey = trim(strtolower($departmentName));
                    if (isset($dept_map[$deptKey])) {
                        $departmentId = $dept_map[$deptKey];
                    } else {
                        $error_rows[] = "Dòng $row: Không tìm thấy phòng '$departmentName'";
                        continue;
                    }
                }

                // Validate required fields based on type
                if ($romanType == 'II' && empty($roomId)) {
                    $error_rows[] = "Dòng $row: Type II yêu cầu tên ban";
                    continue;
                }

                if ($romanType == 'III' && empty($departmentId)) {
                    $error_rows[] = "Dòng $row: Type III yêu cầu tên phòng";
                    continue;
                }

                if (empty($itemText)) {
                    $error_rows[] = "Dòng $row: Nội dung tiêu chí không được để trống";
                    continue;
                }

                // Create section key
                $section_key = $romanType . '_' . $roomId . '_' . $departmentId;

                if (!isset($sections_data[$section_key])) {
                    $sections_data[$section_key] = [
                        'romanType' => $romanType,
                        'room' => $roomId ?: null,
                        'department' => $departmentId ?: null,
                        'items' => []
                    ];
                }

                $sections_data[$section_key]['items'][] = ['text' => $itemText];
            }

            // Process each section
            foreach ($sections_data as $section_data) {
                $romanType = $section_data['romanType'];
                $room = $section_data['room'];
                $department = $section_data['department'];
                $items = $section_data['items'];

                // Check if section exists
                $section_index = null;
                foreach ($template as $index => $section) {
                    $section_roman = isset($section['romanType']) ? $section['romanType'] : (isset($section['id']) ? explode('.', $section['id'])[0] : '');

                    if ($section_roman === $romanType) {
                        // Check room/department match
                        if ($romanType == 'II' && isset($section['room']) && $section['room'] == $room) {
                            $section_index = $index;
                            break;
                        } elseif ($romanType == 'III' && isset($section['department']) && $section['department'] == $department) {
                            $section_index = $index;
                            break;
                        } elseif (in_array($romanType, ['I', 'IV', 'V']) && !isset($section['room']) && !isset($section['department'])) {
                            $section_index = $index;
                            break;
                        }
                    }
                }

                // Build section object
                $titleMap = [
                    'I' => 'CHECKLIST CHUNG (BẮT BUỘC)',
                    'II' => 'CHECKLIST THEO BAN',
                    'III' => 'CHECKLIST THEO PHÒNG',
                    'IV' => 'KIỂM SOÁT HỒ SƠ CHUNG',
                    'V' => 'KIỂM SOÁT HỒ SƠ CHUNG'
                ];

                $new_section = [
                    'title' => $romanType . '. ' . $titleMap[$romanType],
                    'id' => $romanType,
                    'displayCondition' => 'always',
                    'room' => $room,
                    'department' => $department,
                    'romanType' => $romanType,
                    'items' => $items
                ];

                if ($section_index !== null) {
                    // Update existing section - merge items
                    $existing_items = isset($template[$section_index]['items']) ? $template[$section_index]['items'] : [];
                    $template[$section_index]['items'] = array_merge($existing_items, $items);
                } else {
                    // Add new section
                    $template[] = $new_section;
                }

                $imported_count += count($items);
            }

            // Save template
            $success = $this->saveAuditTemplate($template);

            if ($success) {
                $message = "Import thành công $imported_count tiêu chí!";
                if (!empty($error_rows)) {
                    $message .= " Có " . count($error_rows) . " dòng lỗi.";
                }

                echo json_encode([
                    'success' => true,
                    'alert_type' => 'success',
                    'message' => $message,
                    'errors' => $error_rows
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'alert_type' => 'danger',
                    'message' => 'Có lỗi khi lưu template!'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'alert_type' => 'danger',
                'message' => 'Lỗi đọc file Excel: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete audit
     */
    public function delete($id)
    {
        $audit = $this->audit_management_model->getAuditById($id);

        if ($audit && $audit->status == 'COMPLETED') {
            echo json_encode([
                'result' => 0,
                'message' => 'Không thể xóa phiếu audit đã hoàn thành!'
            ]);
            return;
        }

        // Check if audit has production reports
        $this->db->where('audit_id', $id);
        $reports = $this->db->get('tblproduction_report')->result();

        if (!empty($reports)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không thể xóa vì đã có phiếu báo cáo liên quan!'
            ]);
            return;
        }

        // Delete checklist items first
        $this->audit_management_model->deleteChecklistByAuditId($id);

        // Reset link in probationary assessment
        $this->db->where('audit_id', $id);
        $this->db->update('tbl_probationary_assessment', ['audit_id' => null]);

        // Delete audit
        $success = $this->audit_management_model->deleteAudit($id);

        echo json_encode([
            'result' => $success ? 1 : 0,
            'message' => $success ? 'Xóa phiếu audit thành công!' : 'Có lỗi xảy ra'
        ]);
    }

    /**
     * Export audit report to PDF
     */
    public function exportPDF($audit_id)
    {
        $audit = $this->audit_management_model->getAuditById($audit_id);

        if (!$audit) {
            show_404();
            return;
        }

        // Check if audit is completed 100%
        if ($audit->status != 'COMPLETED' || $audit->result_percentage < 100) {
            set_alert('warning', 'Chỉ có thể in PDF cho phiếu audit đã hoàn thành 100%!');
            redirect(admin_url('audit_management'));
            return;
        }

        ob_start();
        $data = new stdClass();
        $data->title = 'PHIẾU AUDIT';
        $data->content = '';

        // Header
        $data->content .= '<div style="text-align: center; margin-bottom: 20px;">';
        $data->content .= '<h1 style="font-size: 24px; font-weight: bold; margin: 0;">PHIẾU AUDIT NỘI BỘ</h1>';
        $data->content .= '<p style="font-size: 14px; font-style: italic; margin: 5px 0;">Internal Audit Report</p>';
        $data->content .= '</div>';

        // Audit Information
        $data->content .= '<table style="width: 100%; margin-bottom: 20px; font-size: 13px;">';
        $data->content .= '<tr>';
        $data->content .= '<td style="width: 50%;"><strong>Mã phiếu:</strong> ' . htmlspecialchars($audit->audit_code) . '</td>';
        $data->content .= '<td style="width: 50%;"><strong>Ngày audit:</strong> ' . _d($audit->audit_date) . '</td>';
        $data->content .= '</tr>';
        $data->content .= '<tr>';
        $data->content .= '<td><strong>Phòng ban:</strong> ' . htmlspecialchars($audit->department) . '</td>';
        $data->content .= '<td><strong>Trưởng đoàn:</strong> ' . htmlspecialchars($audit->team_leader) . '</td>';
        $data->content .= '</tr>';
        $data->content .= '<tr>';
        $data->content .= '<td><strong>Kết quả:</strong> <span style="color: #059669; font-weight: bold;">' . $audit->result_percentage . '%</span></td>';
        $data->content .= '<td><strong>Trạng thái:</strong> <span style="color: #059669;">Hoàn thành</span></td>';
        $data->content .= '</tr>';
        $data->content .= '</table>';

        // Get checklist data
        $checklist = $this->audit_management_model->getChecklistByAuditId($audit_id);
        $sections = $this->groupChecklistBySections($checklist);

        // Checklist Table
        $data->content .= '<h3 style="font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; border-bottom: 2px solid #2d3748; padding-bottom: 5px;">DANH SÁCH KIỂM TRA</h3>';

        foreach ($sections as $section) {
            // Section Header
            $data->content .= '<div style="background: #f7fafc;line-height: 2; padding: 15px; margin: 15px 0 5px 0; font-weight: bold; font-size: 14px; ">';
            $data->content .= htmlspecialchars($section['title']);
            if (!empty($section['room'])) {
                $data->content .= ' <span style="font-size: 11px; font-weight: normal; color: #4a5568;">(' . htmlspecialchars($section['room']) . ')</span>';
            }
            if (!empty($section['department'])) {
                $data->content .= ' <span style="font-size: 11px; font-weight: normal; color: #4a5568;">(' . htmlspecialchars($section['department']) . ')</span>';
            }
            $data->content .= '</div>';

            // Items Table
            $table = '<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px;">';
            $table .= '<thead>';
            $table .= '<tr style="background: #f7fafc;">';
            $table .= '<th style="width: 5%; text-align: center;">STT</th>';
            $table .= '<th style="width: 60%; text-align: left;">Nội dung kiểm tra</th>';
            $table .= '<th style="width: 10%; text-align: center;">Kết quả</th>';
            $table .= '<th style="width: 25%; text-align: left;">Ghi chú</th>';
            $table .= '</tr>';
            $table .= '</thead>';
            $table .= '<tbody>';

            $stt = 1;

            // Direct items
            if (!empty($section['items'])) {
                foreach ($section['items'] as $item) {
                    $table .= '<tr nobr="true">';
                    $table .= '<td style="width: 5%;text-align: center;">' . $stt++ . '</td>';
                    $table .= '<td style="width: 60%;">';
                    if ($item->critical) {
                        $table .= '<span style="background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; margin-right: 5px;">CRITICAL</span>';
                    }
                    $table .= htmlspecialchars($item->item_text);
                    $table .= '</td>';

                    $statusHtml = '';
                    if ($item->status == 'yes') {
                        $statusHtml = '<span style="color: #059669; font-weight: bold;">YES</span>';
                    } elseif ($item->status == 'no') {
                        $statusHtml = '<span style="color: #dc2626; font-weight: bold;">NO</span>';
                    } else {
                        $statusHtml = '-';
                    }
                    $table .= '<td style="width: 10%;text-align: center;">' . $statusHtml . '</td>';

                    // Show production report reference for NO items
                    $noteContent = '';
                    if ($item->status == 'no') {
                        $this->db->select('reference_no');
                        $this->db->where('audit_id', $audit_id);
                        $this->db->where('id_audit_item', $item->id);
                        $report = $this->db->get('tblproduction_report')->row();
                        if ($report) {
                            $noteContent = '' . htmlspecialchars($report->reference_no);
                            if ($item->note) {
                                $noteContent .= '<br><strong>Ghi chú:</strong> ' . htmlspecialchars($item->note);
                            }
                        } else {
                            $noteContent = $item->note ? htmlspecialchars($item->note) : '';
                        }
                    } else {
                        $noteContent = $item->note ? htmlspecialchars($item->note) : '';
                    }
                    $table .= '<td>' . $noteContent . '</td>';
                    $table .= '</tr>';
                }
            }

            // Subsections
            if (!empty($section['subsections'])) {
                foreach ($section['subsections'] as $subsection) {
                    // Subsection header row
                    $table .= '<tr nobr="true">';
                    $table .= '<td colspan="4" style="background: #f8fafc; font-weight: bold; font-size: 11px; padding: 8px;">';
                    $table .= '<i class="fa fa-folder-o"></i> ' . htmlspecialchars($subsection['title']);
                    $table .= '</td>';
                    $table .= '</tr>';

                    foreach ($subsection['items'] as $item) {
                        $table .= '<tr nobr="true">';
                        $table .= '<td style="text-align: center;">' . $stt++ . '</td>';
                        $table .= '<td>';
                        if ($item->critical) {
                            $table .= '<span style="background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; margin-right: 5px;">CRITICAL</span>';
                        }
                        $table .= htmlspecialchars($item->item_text);
                        $table .= '</td>';

                        $statusHtml = '';
                        if ($item->status == 'yes') {
                            $statusHtml = '<span style="color: #059669; font-weight: bold;">✓ YES</span>';
                        } elseif ($item->status == 'no') {
                            $statusHtml = '<span style="color: #dc2626; font-weight: bold;">✗ NO</span>';
                        } else {
                            $statusHtml = '-';
                        }
                        $table .= '<td style="text-align: center;">' . $statusHtml . '</td>';

                        // Show production report reference for NO items
                        $noteContent = '';
                        if ($item->status == 'no') {
                            $this->db->select('reference_no');
                            $this->db->where('audit_id', $audit_id);
                            $this->db->where('id_audit_item', $item->id);
                            $report = $this->db->get('tblproduction_report')->row();
                            if ($report) {
                                $noteContent = '' . htmlspecialchars($report->reference_no);
                                if ($item->note) {
                                    $noteContent .= '<br><strong>Ghi chú:</strong> ' . htmlspecialchars($item->note);
                                }
                            } else {
                                $noteContent = $item->note ? htmlspecialchars($item->note) : '';
                            }
                        } else {
                            $noteContent = $item->note ? htmlspecialchars($item->note) : '';
                        }
                        $table .= '<td>' . $noteContent . '</td>';
                        $table .= '</tr>';
                    }
                }
            }

            $table .= '</tbody>';
            $table .= '</table>';

            $data->content .= $table;
        }

        // Summary
        $data->content .= '<h3 style="font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; border-bottom: 2px solid #2d3748; padding-bottom: 5px;">TỔNG KẾT</h3>';
        $data->content .= '<table style="width: 100%; margin-bottom: 20px; font-size: 13px;">';
        $data->content .= '<tr>';
        $data->content .= '<td style="width: 33%;"><strong>Tổng tiêu chí:</strong> ' . $audit->total_items . '</td>';
        $data->content .= '<td style="width: 33%;"><strong>Đạt (YES):</strong> <span style="color: #059669;">' . $audit->yes_count . '</span></td>';
        $data->content .= '<td style="width: 34%;"><strong>Không đạt (NO):</strong> <span style="color: #dc2626;">' . $audit->no_count . '</span></td>';
        $data->content .= '</tr>';
        if ($audit->critical_no_count > 0) {
            $data->content .= '<tr>';
            $data->content .= '<td colspan="3"><strong>Lỗi Critical:</strong> <span style="color: #dc2626; font-weight: bold;">' . $audit->critical_no_count . '</span></td>';
            $data->content .= '</tr>';
        }
        $data->content .= '<tr>';
        $data->content .= '<td colspan="3"><strong>Kết quả cuối cùng:</strong> <span style="color: #059669; font-size: 16px; font-weight: bold;">' . $audit->result_percentage . '%</span></td>';
        $data->content .= '</tr>';
        $data->content .= '</table>';

        // Signature Section
        $data->content .= '<table style="width: 100%; margin-top: 40px; font-size: 13px;">';
        $data->content .= '<tr>';
        $data->content .= '<td style="width: 34%; text-align: center; vertical-align: top;">';
        $data->content .= '<strong>Trưởng đoàn Audit</strong><br>';
        $data->content .= '<span style="font-size: 11px; font-style: italic;">(Ký, ghi rõ họ tên)</span>';
        $data->content .= '<div style="height: 60px;"></div>';
        $data->content .= '<div>' . htmlspecialchars($audit->team_leader) . '</div>';
        $data->content .= '</td>';
        $data->content .= '<td style="width: 33%; text-align: center; vertical-align: top;">';
        $data->content .= '<strong>Đơn vị</strong><br>';
        $data->content .= '<span style="font-size: 11px; font-style: italic;">(Ký, ghi rõ họ tên)</span>';
        $data->content .= '<div style="height: 80px;"></div>';
        $data->content .= '</td>';

        $data->content .= '<td style="width: 33%; text-align: center; vertical-align: top;">';
        $data->content .= '<strong>BOD</strong><br>';
        $data->content .= '<span style="font-size: 11px; font-style: italic;">(Ký, ghi rõ họ tên)</span>';
        $data->content .= '<div style="height: 80px;"></div>';
        $data->content .= '</td>';
        $data->content .= '</tr>';
        $data->content .= '</table>';

        // Footer
        $data->content .= '<div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #718096; text-align: center;">';
        $data->content .= 'Ngày in: ' . date('d/m/Y H:i') . ' - ' . get_staff_full_name(get_staff_user_id());
        $data->content .= '</div>';

        $pdf = print_pdf_audit($data);
        $type = 'I';
        $filename = 'Audit_' . $audit->audit_code . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, $type);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Get departments list
     */
    private function getDepartments()
    {
        $tbl_room = $this->db->get('tbl_room')->result_array();

        if (!empty($tbl_room)) {
            $departments = [];
            foreach ($tbl_room as $room) {
                $departments[] = [
                    'id' => $room['id'],
                    'name' => $room['name'],
                    'group' => 'OPERATIONS' // Assuming all rooms belong to OPERATIONS group
                ];
            }
            return $departments;
        }
        return [];
    }

    /**
     * Get department name by ID
     */
    private function getDepartmentName($dept_id)
    {
        $departments = $this->getDepartments();
        foreach ($departments as $dept) {
            if ($dept['id'] == $dept_id) {
                return $dept['name'];
            }
        }
        return '';
    }

    /**
     * Get department group by ID
     */
    private function getDepartmentGroup($dept_id)
    {
        $departments = $this->getDepartments();
        foreach ($departments as $dept) {
            if ($dept['id'] == $dept_id) {
                return $dept['group'];
            }
        }
        return '';
    }

    /**
     * Get audit template from database or return default
     */
    private function getAuditTemplate()
    {
        // Try to get from database
        $template_record = $this->db->get_where('tbl_audit_template', ['id' => 1])->row();
        if ($template_record) {
            return json_decode($template_record->template_data, true);
        } else {
            return json_decode("{}", true);
        }
    }

    /**
     * Save audit template to database
     */
    private function saveAuditTemplate($template)
    {
        $data = [
            'template_data' => json_encode($template, JSON_UNESCAPED_UNICODE),
            'updated_by_staff_id' => get_staff_user_id(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Check if exists
        $exists = $this->db->get_where('tbl_audit_template', ['id' => 1])->row();

        if ($exists) {
            $this->db->where('id', 1);
            return $this->db->update('tbl_audit_template', $data);
        } else {
            $data['id'] = 1;
            $data['created_by_staff_id'] = get_staff_user_id();
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('tbl_audit_template', $data);
        }
    }

    /**
     * Get default template structure
     */
    private function getDefaultTemplate()
    {
        return [
            [
                'id' => 'I',
                'title' => 'I. CHECKLIST CHUNG (BẮT BUỘC)',
                'displayCondition' => 'ALWAYS',
                'items' => [
                    ['id' => 'c1', 'text' => 'Có kế hoạch audit', 'critical' => false],
                    ['id' => 'c2', 'text' => 'Có họp mở & họp kết', 'critical' => false],
                    ['id' => 'c3', 'text' => 'Có biên bản + Action list', 'critical' => false],
                    ['id' => 'c4', 'text' => 'NO có BCKPH (Báo cáo khắc phục)', 'critical' => false],
                    ['id' => 'c5', 'text' => 'Lưu hồ sơ FOSO', 'critical' => false],
                ]
            ],
            [
                'id' => 'II',
                'title' => 'II. CHECKLIST THEO BAN (CHỨC NĂNG)',
                'displayCondition' => 'ALWAYS',
                'subsections' => [
                    [
                        'id' => 'II.A',
                        'title' => 'A. BAN VS5S – ATLD – PCCC',
                        'displayCondition' => 'ALWAYS',
                        'items' => [
                            ['id' => 'vs1', 'text' => 'Khu vực sạch, gọn (5S)', 'critical' => false],
                            ['id' => 'vs2', 'text' => 'Lối thoát hiểm thông thoáng', 'critical' => true],
                            ['id' => 'vs3', 'text' => 'Bình PCCC còn hạn', 'critical' => true],
                            ['id' => 'vs4', 'text' => 'Biển báo đầy đủ / PPE', 'critical' => false],
                            ['id' => 'vs5', 'text' => 'Tủ điện, dây dẫn an toàn', 'critical' => true],
                        ]
                    ],
                    [
                        'id' => 'II.B',
                        'title' => 'B. BAN ĐÁNH GIÁ – QUẢN TRỊ RỦI RO',
                        'displayCondition' => 'GROUP_OFFICE',
                        'note' => 'Dành cho khối Văn phòng',
                        'items' => [
                            ['id' => 'rr1', 'text' => 'Chứng nhận/giấy phép còn hiệu lực', 'critical' => false],
                            ['id' => 'rr2', 'text' => 'Tăng ca có phê duyệt', 'critical' => false],
                            ['id' => 'rr3', 'text' => 'Gia công có hợp đồng/PO + nghiệm thu', 'critical' => false],
                            ['id' => 'rr4', 'text' => 'Thuế (VAT, TNDN, TNCN) đúng hạn', 'critical' => true],
                            ['id' => 'rr5', 'text' => 'Lương – BHXH chi đúng – đủ', 'critical' => true],
                        ]
                    ]
                ]
            ],
            [
                'id' => 'III',
                'title' => 'III. CHECKLIST THEO PHÒNG (CHUYÊN MÔN)',
                'displayCondition' => 'DYNAMIC',
                'subsections' => [
                    [
                        'id' => 'III.1',
                        'title' => '1. BẢO VỆ',
                        'forDept' => 'SEC',
                        'items' => [
                            ['id' => 'bv1', 'text' => 'Cổng/cửa/niêm phong', 'critical' => false],
                            ['id' => 'bv2', 'text' => 'Camera hoạt động', 'critical' => false],
                            ['id' => 'bv3', 'text' => 'Nhật ký ra/vào & Tuần tra', 'critical' => false],
                        ]
                    ],
                    [
                        'id' => 'III.2',
                        'title' => '2. IT (Công nghệ thông tin)',
                        'forDept' => 'IT',
                        'items' => [
                            ['id' => 'it1', 'text' => 'Server/mạng hoạt động ổn định', 'critical' => true],
                            ['id' => 'it2', 'text' => 'Backup dữ liệu định kỳ', 'critical' => true],
                            ['id' => 'it3', 'text' => 'Phân quyền & Log truy cập', 'critical' => false],
                            ['id' => 'it4', 'text' => 'Kho thiết bị IT gọn (5S)', 'critical' => false],
                        ]
                    ],
                    [
                        'id' => 'III.5',
                        'title' => '5. KHO – LOGISTICS',
                        'forDept' => 'LOG',
                        'items' => [
                            ['id' => 'k1', 'text' => 'Che chắn, Pallet/kệ', 'critical' => false],
                            ['id' => 'k2', 'text' => 'FIFO/FEFO & Tem nhãn', 'critical' => false],
                            ['id' => 'k3', 'text' => 'Xe nâng & Phiếu NX', 'critical' => false],
                        ]
                    ],
                    [
                        'id' => 'III.6',
                        'title' => '6. MUA SẮM',
                        'forDept' => 'PUR',
                        'items' => [
                            ['id' => 'ms1', 'text' => 'RFQ & ≥ 3 báo giá', 'critical' => false],
                            ['id' => 'ms2', 'text' => 'So sánh & Phê duyệt', 'critical' => false],
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Create checklist items for an audit based on department and template
     */
    private function createChecklistItems($audit_id, $dept_id, $template)
    {
        $dept_group = $this->getDepartmentGroup($dept_id);
        $dept_name = $this->getDepartmentName($dept_id);

        // Get room info for the selected dept_id
        $room_info = $this->db->where('id', $dept_id)->get('tbl_room')->row();
        $selected_room_id = $room_info ? $room_info->id : null;

        // Get department info if dept_id is actually a department
        $dept_info = $this->db->where('departmentid', $dept_id)->get('tbldepartments')->row();
        $selected_dept_id = $dept_info ? $dept_info->departmentid : null;

        $items_to_insert = [];

        foreach ($template as $section) {
            $should_include = false;

            // Check new structure: room and department fields
            if (isset($section['room']) && !empty($section['room'])) {
                // Type II - Section theo ban: chỉ lấy đúng ban
                if ($section['room'] == $selected_room_id) {
                    $should_include = true;
                }
            } elseif (isset($section['department']) && !empty($section['department'])) {
                // Type III - Section theo phòng: lấy hết tất cả phòng
                $should_include = true;
            } elseif (isset($section['departments']) && !empty($section['departments'])) {
                // Old structure - backward compatibility
                if (in_array($dept_name, $section['departments'])) {
                    $should_include = true;
                }
            } else {
                // Type I, IV, V - Sections chung: lấy tất cả
                $should_include = true;
            }

            if (!$should_include) {
                continue;
            }

            // Check if section should be displayed
            if ($section['displayCondition'] == 'ALWAYS' || $section['displayCondition'] == 'always') {
                // Add direct items
                if (isset($section['items'])) {
                    foreach ($section['items'] as $item) {
                        $items_to_insert[] = [
                            'audit_id' => $audit_id,
                            'section_id' => isset($item['id']) ? $item['id'] : uniqid(),
                            'section_title' => $section['title'],
                            'subsection_id' => null,
                            'subsection_title' => null,
                            'room_id' => isset($section['room']) ? $section['room'] : null,
                            'department_id' => isset($section['department']) ? $section['department'] : null,
                            'roman_type' => isset($section['romanType']) ? $section['romanType'] : null,
                            'item_id' => isset($item['id']) ? $item['id'] : uniqid(),
                            'item_text' => $item['text'],
                            'critical' => (isset($item['critical']) && $item['critical']) ? 1 : 0,
                            'status' => null,
                            'note' => null
                        ];
                    }
                }

                // Add subsection items
                if (isset($section['subsections'])) {
                    foreach ($section['subsections'] as $subsection) {
                        // Check subsection condition
                        $should_include = false;

                        if (isset($subsection['displayCondition'])) {
                            if ($subsection['displayCondition'] == 'ALWAYS' || $subsection['displayCondition'] == 'always') {
                                $should_include = true;
                            } elseif ($subsection['displayCondition'] == 'GROUP_OFFICE' && $dept_group == 'OFFICE') {
                                $should_include = true;
                            }
                        } else {
                            // No condition = always include
                            $should_include = true;
                        }

                        if ($should_include && isset($subsection['items'])) {
                            foreach ($subsection['items'] as $item) {
                                $items_to_insert[] = [
                                    'audit_id' => $audit_id,
                                    'section_id' => $section['id'],
                                    'section_title' => $section['title'],
                                    'subsection_id' => isset($subsection['id']) ? $subsection['id'] : uniqid(),
                                    'subsection_title' => $subsection['title'],
                                    'room_id' => isset($section['room']) ? $section['room'] : null,
                                    'department_id' => isset($section['department']) ? $section['department'] : null,
                                    'roman_type' => isset($section['romanType']) ? $section['romanType'] : null,
                                    'item_id' => isset($item['id']) ? $item['id'] : uniqid(),
                                    'item_text' => $item['text'],
                                    'critical' => (isset($item['critical']) && $item['critical']) ? 1 : 0,
                                    'status' => null,
                                    'note' => null
                                ];
                            }
                        }
                    }
                }
            } elseif ($section['displayCondition'] == 'DYNAMIC' || $section['displayCondition'] == 'if_applicable' || $section['displayCondition'] == 'conditional') {
                // Only include subsections matching the department
                if (isset($section['subsections'])) {
                    foreach ($section['subsections'] as $subsection) {
                        $should_include = false;

                        // Check old forDept field for backward compatibility
                        if (isset($subsection['forDept']) && $subsection['forDept'] == $dept_id) {
                            $should_include = true;
                        }

                        // Check new departments array
                        if (isset($subsection['departments']) && !empty($subsection['departments'])) {
                            if (in_array($dept_name, $subsection['departments'])) {
                                $should_include = true;
                            }
                        } else if (!isset($subsection['departments']) && !isset($subsection['forDept'])) {
                            // No department restriction - include for all
                            $should_include = true;
                        }

                        if ($should_include && isset($subsection['items'])) {
                            foreach ($subsection['items'] as $item) {
                                $items_to_insert[] = [
                                    'audit_id' => $audit_id,
                                    'section_id' => $section['id'],
                                    'section_title' => $section['title'],
                                    'subsection_id' => $subsection['id'],
                                    'subsection_title' => $subsection['title'],
                                    'item_id' => $item['id'],
                                    'item_text' => $item['text'],
                                    'critical' => isset($item['critical']) && $item['critical'] ? 1 : 0,
                                    'status' => null,
                                    'note' => null
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Batch insert
        if (!empty($items_to_insert)) {
            $this->db->insert_batch('tbl_audit_checklist', $items_to_insert);
        }
    }

    /**
     * Group checklist items by sections
     */
    private function groupChecklistBySections($checklist)
    {
        $grouped = [];

        foreach ($checklist as $item) {
            // Group by section_title only
            $section_key = $item->section_title;

            $romanType = isset($item->roman_type) ? $item->roman_type : '';
            $roomName = isset($item->room_name) ? $item->room_name : '';
            $deptName = isset($item->department_name) ? $item->department_name : '';

            if (!isset($grouped[$section_key])) {
                $grouped[$section_key] = [
                    'id' => $item->section_id,
                    'title' => $item->section_title,
                    'romanType' => $romanType,
                    'room' => $roomName,
                    'department' => $deptName,
                    'items' => [],
                    'subsections' => []
                ];
            }

            if ($item->subsection_id) {
                // It's a subsection item
                $subsection_id = $item->subsection_id;

                if (!isset($grouped[$section_key]['subsections'][$subsection_id])) {
                    $grouped[$section_key]['subsections'][$subsection_id] = [
                        'id' => $subsection_id,
                        'title' => $item->subsection_title,
                        'items' => []
                    ];
                }

                $grouped[$section_key]['subsections'][$subsection_id]['items'][] = $item;
            } else {
                // Direct item
                $grouped[$section_key]['items'][] = $item;
            }
        }
        return array_values($grouped);
    }

    /**
     * Create CAPA for critical issues
     */
    private function createCapaForCriticalIssues($audit_id)
    {
        // Get all critical NO items
        $this->db->where('audit_id', $audit_id);
        $this->db->where('critical', 1);
        $this->db->where('status', 'no');
        $critical_items = $this->db->get('tbl_audit_checklist')->result();

        foreach ($critical_items as $item) {
            // Generate CAPA code
            $capa_code = 'CAPA-' . date('Ymd') . '-' . $audit_id . '-' . $item->id;

            $capa_data = [
                'capa_code' => $capa_code,
                'audit_id' => $audit_id,
                'id_audit_item' => $item->id,
                'issue_description' => $item->item_text,
                'status' => 'OPEN',
                'priority' => 'HIGH',
                'created_by_staff_id' => get_staff_user_id(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tbl_audit_capa', $capa_data);
        }
    }
    function updateCompletion()
    {

        $audits = $this->audit_management_model->getAllAudits();
        foreach ($audits as $audit) {
            $checklist = $this->audit_management_model->getChecklistByAuditId($audit->id);
            $total_items = count($checklist);
            $completed_items = 0;
            foreach ($checklist as $item) {
                if ($item->status == 'yes' || $item->status == 'no') {
                    $completed_items++;
                }
            }
            $result_percentage = $total_items > 0 ? ($completed_items / $total_items) * 100 : 0;
            $this->db->where('id', $audit->id);
            $this->db->update('tbl_audit', ['result_percentage' => $result_percentage]);
        }
        $_data['completion'] = $result_percentage;
        echo json_encode([
            'result' => 1,
            'message' => 'Cập nhật hoàn thành cho tất cả phiếu audit thành công!',
            'data' => $_data
        ]);
    }

    /**
     * Upload images for audit item
     */
    public function uploadAuditItemImages()
    {
        $item_id = $this->input->post('item_id');

        // Check if item already has status - prevent upload
        $this->db->where('id', $item_id);
        $item = $this->db->get('tbl_audit_checklist')->row();

        if (!$item) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không tìm thấy audit item!'
            ]);
            return;
        }

        // if (!empty($item->status)) {
        //     echo json_encode([
        //         'result' => 0,
        //         'message' => 'Không thể upload ảnh cho bước đã hoàn thành!'
        //     ]);
        //     return;
        // }

        // Create upload directory if not exists
        $upload_path = FCPATH . 'uploads/audit_items/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $uploaded_files = [];
        $failed_files = [];

        if (!empty($_FILES['images']['name'][0])) {
            $files_count = count($_FILES['images']['name']);

            for ($i = 0; $i < $files_count; $i++) {
                $_FILES['file']['name'] = $_FILES['images']['name'][$i];
                $_FILES['file']['type'] = $_FILES['images']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['images']['error'][$i];
                $_FILES['file']['size'] = $_FILES['images']['size'][$i];

                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 5120; // 5MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('file')) {
                    $upload_data = $this->upload->data();

                    // Save to database
                    $image_data = [
                        'audit_item_id' => $item_id,
                        'file_name' => $upload_data['file_name'],
                        'file_path' => 'uploads/audit_items/' . $upload_data['file_name'],
                        'file_size' => $upload_data['file_size'],
                        'uploaded_by' => get_staff_user_id(),
                        'uploaded_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tblaudit_item_images', $image_data);
                    $image_id = $this->db->insert_id();
                    $uploaded_files[] = $upload_data['file_name'];

                    // Log the image upload
                    $staff_id = get_staff_user_id();
                    $staff_name = get_staff_full_name($staff_id);

                    $log_data = [
                        'audit_id' => $item->audit_id,
                        'audit_item_id' => $item_id,
                        'action_type' => 'IMAGE_UPLOAD',
                        'action_description' => "Upload ảnh: " . $upload_data['file_name'] . " (Kích thước: " . round($upload_data['file_size'] / 1024, 2) . " MB)",
                        'image_id' => $image_id,
                        'image_filename' => $upload_data['file_name'],
                        'new_value' => json_encode([
                            'filename' => $upload_data['file_name'],
                            'size' => $upload_data['file_size']
                        ]),
                        'staff_id' => $staff_id,
                        'staff_name' => $staff_name,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_audit_history_log', $log_data);
                } else {
                    $failed_files[] = $_FILES['images']['name'][$i];
                }
            }
        }

        if (count($uploaded_files) > 0) {
            echo json_encode([
                'result' => 1,
                'message' => 'Upload thành công ' . count($uploaded_files) . ' ảnh!',
                'data' => $uploaded_files
            ]);
        } else {
            echo json_encode([
                'result' => 0,
                'message' => 'Không thể upload ảnh. Vui lòng kiểm tra định dạng file!'
            ]);
        }
    }

    /**
     * Get images for audit item
     */
    public function getAuditItemImages()
    {
        $item_id = $this->input->get('item_id');

        $this->db->where('audit_item_id', $item_id);
        $this->db->order_by('uploaded_at', 'ASC');
        $images = $this->db->get('tblaudit_item_images')->result();

        echo json_encode([
            'result' => 1,
            'data' => $images
        ]);
    }

    /**
     * Delete audit item image
     */
    public function deleteAuditItemImage()
    {
        $image_id = $this->input->post('image_id');

        // Get image info
        $this->db->where('id', $image_id);
        $image = $this->db->get('tblaudit_item_images')->row();

        if (!$image) {
            echo json_encode([
                'result' => 0,
                'message' => 'Không tìm thấy ảnh!'
            ]);
            return;
        }

        // Check if item already has status - prevent deletion
        $this->db->where('id', $image->audit_item_id);
        $item = $this->db->get('tbl_audit_checklist')->row();

        // if (!empty($item->status)) {
        //     echo json_encode([
        //         'result' => 0,
        //         'message' => 'Không thể xóa ảnh của bước đã hoàn thành!'
        //     ]);
        //     return;
        // }

        // Delete file from disk
        $file_path = FCPATH . 'uploads/audit_items/' . $image->file_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete from database
        $this->db->where('id', $image_id);
        $this->db->delete('tblaudit_item_images');

        // Log the image deletion
        $staff_id = get_staff_user_id();
        $staff_name = get_staff_full_name($staff_id);

        $log_data = [
            'audit_id' => $item->audit_id,
            'audit_item_id' => $image->audit_item_id,
            'action_type' => 'IMAGE_DELETE',
            'action_description' => "Xóa ảnh: " . $image->file_name,
            'image_id' => $image_id,
            'image_filename' => $image->file_name,
            'old_value' => json_encode([
                'filename' => $image->file_name,
                'size' => $image->file_size
            ]),
            'staff_id' => $staff_id,
            'staff_name' => $staff_name,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tbl_audit_history_log', $log_data);

        echo json_encode([
            'result' => 1,
            'message' => 'Xóa ảnh thành công!'
        ]);
    }

    public function print_pdf($id = '')
    {
        ob_start();
        $data = new stdClass();
        $data->title = lang('Phiếu nhập hàng');
        $dataField = get_table_where('tbl_field_pdf', array('parent_field' => 'import'), '', 'row');
        $dataMain = get_table_where('tblimport', array('id' => $id), '', 'row');
        $dataSub = get_table_where('tblimport_items', array('id_import' => $id));
        $supplier = get_table_where('tblsuppliers', array('id' => $dataMain->suppliers_id), '', 'row');
        $table = '';
        $data->content = '';
        // $data->content .= '<span style="text-align: center;">____________________________________________________________________________________________________________________________________________</span><br><br>';
        $data->content .= '<span style="text-align: center;font-size: 20px;font-weight: bold;">NHẬP HÀNG</span><br><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_code_p') . ': ' . $dataMain->prefix . '-' . $dataMain->code . '</span><br>';
        $data->content .= '<span style="text-align: right;font-style: italic;">' . _l('ch_date_p') . ': ' . _d($dataMain->date) . '</span><br><br>';
        $data->content .= '
            <span style="font-weight: bold;">' . _l('ch_staff_p') . ': </span><span>' . get_staff_full_name($dataMain->staff_create) . '</span><br>
            <span style="font-weight: bold;">' . _l('supplier') . ': </span><span>' . $supplier->company . '</span><br>';
        $purchase_order = format_purchase_order_father_in($dataMain->id_order);
        if ($purchase_order) {
            $data->content .= '<span style="font-weight: bold;">' . _l('Mã YC') . ': </span><span>' . $purchase_order . '</span><br>';
        }
        if (!empty($dataMain->id_order)) {
            $purchase_orders = get_table_where('tblpurchase_order', array('id' => $dataMain->id_order), '', 'row');
            $data->content .= '<span style="font-weight: bold;">' . _l('code_old_purchase') . ': </span><span>' . $purchase_orders->prefix . '-' . $purchase_orders->code . '</span><br>';
        }
        if (empty($dataMain->id_order)) {

            $purchase_order = format_purchase_order_father_all_in($dataMain->id_order);
        }

        $warehouse = get_table_where('tblwarehouse', array('id' => $dataMain->warehouse_id), '', 'row');
        $data->content .= '
        <span style="font-weight: bold;">' . _l('tblwarehouse') . ': </span><span>' . $warehouse->name . '</span><br>
        <span style="font-weight: bold;">' . _l('ch_note_t') . ': </span><span>' . $dataMain->note . '</span><br><br>
        ';

        $width1 = '';
        $width2 = '';
        $width3 = '';
        $width4 = '';
        $width5 = '';
        $width6 = '';
        $width7 = '';
        $width8 = '';
        $width9 = '';
        $width10 = '';
        $width11 = '';
        $dem_temp = 4;
        if (isset($dataField->arr_field)) {
            $arr = explode(',', $dataField->arr_field);
            foreach ($arr as $key => $value) {
                if ($value == 'item_warehouse_localtion_import') {
                    $item_warehouse_localtion_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_unit_import') {
                    $item_unit_import = true;
                    $dem_temp++;
                }
                if ($value == 'item_quantity_import') {
                    $item_quantity_import = true;
                }
                if ($value == 'item_quantity_confirm_import') {
                    $item_quantity_confirm_import = true;
                }
                if ($value == 'item_price_import') {
                    $item_price_import = true;
                }
                if ($value == 'item_promotion_suppliers_import') {
                    $item_promotion_suppliers_import = true;
                }
                if ($value == 'item_tax_import') {
                    $item_tax_import = true;
                }
                if ($value == 'item_invoice_total_import') {
                    $item_invoice_total_import = true;
                }
                if ($value == 'item_note_import') {
                    $item_note_import = true;
                }
            }
            if (!has_permission('import', '', 'view_price')) {
                unset($item_price_import);
                unset($item_promotion_suppliers_import);
                unset($item_tax_import);
                unset($item_invoice_total_import);
            }
            // if(isset($item_warehouse_localtion_import) && isset($item_unit_import) && isset($item_quantity_import) && isset($item_quantity_confirm_import) && isset($item_price_import) && isset($item_promotion_suppliers_import) && isset($item_tax_import) && isset($item_invoice_total_import) && isset($item_note_import)) {
            //     $width1 = 'width: 5%;';
            //     $width2 = 'width: 16%;';
            //     $width3 = 'width: 13%;';
            //     $width4 = 'width: 7%;';
            //     $width5 = 'width: 7%;';
            //     $width6 = 'width: 7%;';
            //     $width7 = 'width: 9%;';
            //     $width8 = 'width: 9%;';
            //     $width9 = 'width: 5%;';
            //     $width10 = 'width: 13%;';
            //     $width11 = 'width: 9%;';
            // }
        }
        $width1 = 'width: 6%;';
        $width2 = 'width: 20%;';
        $width12 = 'width: 10%;';
        $width3 = 'width: 12%;';
        $width4 = 'width: 8%;';
        // $width5 = 'width: 7%;';
        $width6 = 'width: 10%;';
        $width7 = 'width: 12%;';
        // $width8 = 'width: 9%;';
        // $width9 = 'width: 5%;';
        $width10 = 'width: 12%;';
        $width11 = 'width: 13%;';

        $table = '
            <table class="table table-bordered" border="1" width="100%">
                <thead>
                    <tr>
                        <td style="' . $width1 . 'text-align: center;font-weight: bold;">' . _l('STT') . '</td>
        ';
        $table .= '<td style="' . $width2 . 'text-align: center;font-weight: bold;">' . _l('ch_items_name_t') . '</td>';
        $table .= '<td style="' . $width12 . 'text-align: center;font-weight: bold;">' . _l('Lot') . '</td>';
        // if (isset($item_warehouse_localtion_import)) {
        $table .= '<td style="' . $width3 . 'text-align: center;font-weight: bold;">' . _l('warehouse_localtion') . '</td>';
        // }
        // if (isset($item_unit_import)) {
        $table .= '<td style="' . $width4 . 'text-align: center;font-weight: bold;">' . _l('tnh_dvt') . '</td>';
        // }
        // if (isset($item_quantity_import)) {
        // $table .= '<td style="' . $width5 . 'text-align: center;font-weight: bold;">' . _l('item_quantity_confirm') . '</td>';
        // }
        // if (isset($item_quantity_confirm_import)) {
        $table .= '<td style="' . $width6 . 'text-align: center;font-weight: bold;">' . _l('item_quantity') . '</td>';
        // }
        if (isset($item_price_import)) {
            $table .= '<td style="' . $width7 . 'text-align: center;font-weight: bold;">' . _l('tnh_price_import') . '</td>';
            // }
            // if (isset($item_promotion_suppliers_import)) {
            // $table .= '<td style="' . $width8 . 'text-align: center;font-weight: bold;">' . _l('promotion_suppliers') . '</td>';
            // }
            // if (isset($item_tax_import)) {
            // $table .= '<td style="' . $width9 . 'text-align: center;font-weight: bold;">' . _l('tax') . '</td>';
        }
        // if (isset($item_invoice_total_import)) {
        $table .= '<td style="' . $width10 . 'text-align: center;font-weight: bold;">' . _l('invoice_total') . '</td>';
        // }
        // if (isset($item_note_import)) {
        $table .= '<td style="' . $width11 . 'text-align: center;font-weight: bold;">' . _l('note') . '</td>';
        // }
        $table .= '</tr>
                </thead>
                <tbody>';
        $sum_quantity = 0;
        $sum_quantity_net = 0;
        $sum_price = 0;
        $sum_promotion_suppliers = 0;
        $sum_amount = 0;
        foreach ($dataSub as $key => $value) {
            $table .= '<tr nobr="true">';
            $dataItem = $this->invoice_items_model->get_full_item($value['product_id'], $value['type']);
            $dataLocaltion = get_table_where('tbllocaltion_warehouses', array('id' => $value['localtion_warehouses_id']), '', 'row');

            $table .= '<td style="' . $width1 . 'text-align: center;">' . ++$key . '</td>';
            $table .= '<td style="' . $width2 . 'text-align: left;">' . $dataItem->name . '(' . $dataItem->code . ')' . GetQuycach($value['product_id'], $value['type']) . '</td>';
            $table .= '<td style="' . $width12 . 'text-align: center;">' . $value['lot_code'] . '</td>';

            // if (isset($item_warehouse_localtion_import)) {
            if (!empty($dataLocaltion)) {
                // $name_parent = str_replace("<i class='fa fa-caret-right text-danger' aria-hidden='true'>","a",$dataLocaltion->name_parent);
                $table .= '<td style="' . $width3 . 'text-align: center;">' . $dataLocaltion->name_parent . '</td>';
            } else {
                $table .= '<td></td>';
            }
            // }
            // if (isset($item_unit_import)) {
            $table .= '<td style="' . $width4 . 'text-align: center;">' . $dataItem->unit_name . '</td>';
            // }
            // if (isset($item_quantity_import)) {
            // $table .= '<td style="' . $width5 . 'text-align: center;">' . formatNumber($value['quantity']) . '</td>';
            // $sum_quantity += $value['quantity'];
            // }
            // if (isset($item_quantity_confirm_import)) {
            $table .= '<td style="' . $width6 . 'text-align: center;">' . formatNumber($value['quantity_net']) . '</td>';
            $sum_quantity_net += $value['quantity_net'];
            // }
            // if (isset($item_price_import)) {
            $table .= '<td style="' . $width7 . 'text-align: right;">' . number_format($value['price']) . '</td>';
            $sum_price += $value['price'];
            // }
            // if (isset($item_promotion_suppliers_import)) {
            // $table .= '<td style="' . $width8 . 'text-align: right;">' . number_format($value['promotion_suppliers']) . '</td>';
            // $sum_promotion_suppliers += $value['promotion_suppliers'];
            // }
            // if (isset($item_tax_import)) {
            // $table .= '<td style="' . $width9 . 'text-align: center;">' . number_format($value['tax_rate']) . ' %</td>';
            // }
            // if (isset($item_invoice_total_import)) {
            $table .= '<td style="' . $width10 . 'text-align: right;">' . number_format($value['amount']) . '</td>';
            $sum_amount += $value['amount'];
            // }
            // if (isset($item_note_import)) {
            $table .= '<td style="' . $width11 . 'text-align: center;">' . $value['note'] . '</td>';
            // }
            $table .= '</tr>';
        }
        $table .= '<tr>
                <td colspan="' . $dem_temp . '" style="text-align: center;font-weight: bold;">' . _l('invoice_dt_table_heading_amount') . '</td>';
        // if (isset($item_quantity_import)) {
        // $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity) . '</td>';
        // }
        // if (isset($item_quantity_confirm_import)) {
        $table .= '<td style="text-align: center;">' . formatNumber($sum_quantity_net) . '</td>';
        // }
        // if (isset($item_price_import)) {
        $table .= '<td style="text-align: right;">' . number_format($sum_price) . '</td>';
        // }
        // if (isset($item_promotion_suppliers_import)) {
        // $table .= '<td style="text-align: right;">' . number_format($sum_promotion_suppliers) . '</td>';
        // }
        // if (isset($item_tax_import)) {
        // $table .= '<td></td>';
        // }
        // if (isset($item_invoice_total_import)) {
        $table .= '<td style="text-align: right;">' . number_format($sum_amount) . '</td>';
        // }
        // if (isset($item_note_import)) {
        $table .= '<td></td>';
        // }
        $table .= '</tr>';
        $table .= '</tbody>
            </table>';
        $data->content .= $table;


        $table = '<table class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Đề Nghị</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Giao</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Người Nhận</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-weight: bold;">Thủ Kho</span><br>
                            <span>(ký, ghi rõ họ tên)</span>
                        </td>
                    </tr>
                </tbody>
            </table>';
        $data->content .= $table;
        $pdf = print_pdf($data);
        $type = 'I';
        $pdf->Output(slug_it('') . '.pdf', $type);
    }

    /**
     * Get audit history log
     */
    public function getAuditHistoryLog()
    {
        $audit_id = $this->input->get('audit_id');
        $audit_item_id = $this->input->get('audit_item_id');

        $this->db->select('*');
        $this->db->from('tbl_audit_history_log');

        if (!empty($audit_id)) {
            $this->db->where('audit_id', $audit_id);
        }

        if (!empty($audit_item_id)) {
            $this->db->where('audit_item_id', $audit_item_id);
        }

        $this->db->order_by('created_at', 'DESC');
        $logs = $this->db->get()->result();

        echo json_encode([
            'result' => 1,
            'data' => $logs
        ]);
    }

    /**
     * View audit history log modal
     */
    public function viewHistoryLog($audit_id)
    {
        // Get audit info
        $this->db->where('id', $audit_id);
        $audit = $this->db->get('tbl_audit')->row();

        if (!$audit) {
            show_404();
            return;
        }

        // Get all history logs for this audit
        $this->db->where('audit_id', $audit_id);
        $this->db->order_by('created_at', 'DESC');
        $logs = $this->db->get('tbl_audit_history_log')->result();

        $data = [
            'title' => 'Lịch sử thao tác - ' . $audit->audit_code,
            'audit' => $audit,
            'logs' => $logs
        ];

        $this->load->view('admin/audit_management/history_log', $data);
    }
    function loadHistoryLog()
    {
        $audit_id = $this->input->get('audit_id');
        $this->db->where('id', $audit_id);
        $audit = $this->db->get('tbl_audit')->row();

        if (!$audit) {
            show_404();
            return;
        }

        // Get all history logs for this audit
        $this->db->where('audit_id', $audit_id);
        $this->db->order_by('created_at', 'DESC');
        $logs = $this->db->get('tbl_audit_history_log')->result();

        $data = [
            'title' => 'Lịch sử thao tác - ' . $audit->audit_code,
            'audit' => $audit,
            'logs' => $logs
        ];

        $this->load->view('admin/audit_management/data_history_log', $data);
    }
}
