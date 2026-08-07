<?php
/**
 * Checklist Profile Extended Functions
 * File này chứa các hàm mở rộng cần thêm vào Controller Checklist_profile.php
 * Copy các hàm này vào cuối class Checklist_profile (trước dấu đóng class)
 */

/**
 * Auto verify checklist data from real database
 * API endpoint: /admin/checklist_profile/autoVerify/{id}
 */
public function autoVerify($checklist_id)
{
    $checklist = $this->checklist_profile_model->getById($checklist_id);
    
    if (!$checklist) {
        echo json_encode(['result' => 0, 'message' => 'Checklist không tồn tại']);
        return;
    }
    
    $offer = $this->propose_offer_model->getProposeOfferById($checklist->offer_id);
    
    $verification_results = [
        'ho_so_phap_ly_day_du' => $this->verifyLegalDocuments($offer->kqpv_id),
        'bang_cap_cong_chung' => $this->verifyEducationCertificates($offer->kqpv_id),
        'bhxh_detail_valid' => $this->verifySocialInsurance($checklist->employee_id),
        'tai_khoan_ngan_hang_exist' => $this->verifyBankAccount($checklist->employee_id),
        'luong_p1_p2_valid' => $this->verifySalary($checklist->employee_id, $offer)
    ];
    
    // Lưu kết quả verify vào database
    $this->checklist_profile_model->update($checklist_id, [
        'auto_verification_results' => json_encode($verification_results),
        'real_data_verification' => json_encode([
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => get_staff_user_id()
        ])
    ]);
    
    echo json_encode([
        'result' => 1,
        'data' => $verification_results,
        'message' => 'Đã kiểm tra tự động'
    ]);
}

/**
 * Verify legal documents (CMND/CCCD)
 */
private function verifyLegalDocuments($candidate_id)
{
    $candidate = $this->db->get_where('tbl_hr_eprofile', ['id' => $candidate_id])->row();
    
    $has_id = !empty($candidate->id_card);
    $has_issue_date = !empty($candidate->date_of_issue);
    
    // Check trong tblfiles xem có file upload không
    $files_count = $this->db->where('rel_id', $candidate_id)
        ->where('rel_type', 'eprofile')
        ->where('filetype', 'cmnd_cccd')
        ->count_all_results('tblfiles');
    
    $status = $has_id && $has_issue_date && ($files_count > 0);
    
    return [
        'status' => $status,
        'details' => [
            'id_card' => $candidate->id_card ?? '',
            'date_of_issue' => $candidate->date_of_issue ?? '',
            'has_file' => $files_count > 0,
            'files_count' => $files_count,
            'checked_at' => date('Y-m-d H:i:s')
        ],
        'message' => $status ? 'Đầy đủ' : 'Thiếu thông tin CMND/CCCD hoặc file scan'
    ];
}

/**
 * Verify education certificates
 */
private function verifyEducationCertificates($candidate_id)
{
    // Check if có file bằng cấp trong tblfiles
    $files = $this->db->where('rel_id', $candidate_id)
        ->where('rel_type', 'eprofile')
        ->like('filetype', 'bang_cap')
        ->get('tblfiles')
        ->result_array();
    
    $status = count($files) > 0;
    
    return [
        'status' => $status,
        'details' => [
            'count' => count($files),
            'files' => array_map(function($f) {
                return [
                    'name' => $f['file_name'],
                    'type' => $f['filetype'],
                    'uploaded_at' => $f['dateadded']
                ];
            }, $files),
            'checked_at' => date('Y-m-d H:i:s')
        ],
        'message' => $status ? 'Có ' . count($files) . ' file' : 'Chưa có file bằng cấp công chứng'
    ];
}

/**
 * Verify social insurance info
 */
private function verifySocialInsurance($employee_id)
{
    if (!$employee_id) {
        return [
            'status' => false,
            'details' => ['message' => 'Chưa tạo mã NV'],
            'message' => 'Chưa tạo mã nhân viên'
        ];
    }
    
    $staff = $this->db->get_where('tblstaff', ['staffid' => $employee_id])->row();
    
    if (!$staff) {
        return [
            'status' => false,
            'details' => ['message' => 'Không tìm thấy nhân viên'],
            'message' => 'Không tìm thấy thông tin nhân viên'
        ];
    }
    
    // Check các trường BHXH (tùy vào cấu trúc DB của bạn)
    $has_social_insurance = !empty($staff->social_insurance_number ?? '');
    $has_health_insurance = !empty($staff->health_insurance_number ?? '');
    
    $status = $has_social_insurance || $has_health_insurance;
    
    return [
        'status' => $status,
        'details' => [
            'social_insurance_number' => $staff->social_insurance_number ?? '',
            'health_insurance_number' => $staff->health_insurance_number ?? '',
            'checked_at' => date('Y-m-d H:i:s')
        ],
        'message' => $status ? 'Đã có thông tin BHXH' : 'Chưa nhập thông tin BHXH/BHYT'
    ];
}

/**
 * Verify bank account
 */
private function verifyBankAccount($employee_id)
{
    if (!$employee_id) {
        return [
            'status' => false,
            'details' => ['message' => 'Chưa tạo mã NV'],
            'message' => 'Chưa tạo mã nhân viên'
        ];
    }
    
    $staff = $this->db->get_where('tblstaff', ['staffid' => $employee_id])->row();
    
    if (!$staff) {
        return [
            'status' => false,
            'details' => ['message' => 'Không tìm thấy nhân viên'],
            'message' => 'Không tìm thấy thông tin nhân viên'
        ];
    }
    
    // Kiểm tra bank account (tùy vào cấu trúc DB)
    $has_bank_account = !empty($staff->bank_account ?? '');
    $has_bank_name = !empty($staff->bank_name ?? '');
    
    $status = $has_bank_account && $has_bank_name;
    
    return [
        'status' => $status,
        'details' => [
            'bank_account' => $staff->bank_account ?? '',
            'bank_name' => $staff->bank_name ?? '',
            'checked_at' => date('Y-m-d H:i:s')
        ],
        'message' => $status ? 'Đã có TK ngân hàng' : 'Chưa nhập thông tin TK ngân hàng'
    ];
}

/**
 * Verify salary matches offer
 */
private function verifySalary($employee_id, $offer)
{
    if (!$employee_id) {
        return [
            'status' => false,
            'details' => ['message' => 'Chưa tạo mã NV'],
            'message' => 'Chưa tạo mã nhân viên'
        ];
    }
    
    // Lấy thông tin lương từ tbl_contract_labor
    $contract = $this->db->where('staff_id', $employee_id)
        ->order_by('id', 'DESC')
        ->get('tbl_contract_labor')
        ->row();
    
    if (!$contract) {
        return [
            'status' => false,
            'details' => ['message' => 'Chưa có hợp đồng lao động'],
            'message' => 'Chưa tạo hợp đồng lao động'
        ];
    }
    
    // So sánh với offer (cho phép sai số nhỏ ±100đ)
    $p1_match = abs(($contract->salary_position ?? 0) - $offer->luong_p1) < 100;
    $p2_match = abs(($contract->salary_basic ?? 0) - $offer->luong_p2) < 100;
    
    $status = $p1_match && $p2_match;
    
    return [
        'status' => $status,
        'details' => [
            'offer' => [
                'p1' => $offer->luong_p1,
                'p2' => $offer->luong_p2
            ],
            'contract' => [
                'p1' => $contract->salary_position ?? 0,
                'p2' => $contract->salary_basic ?? 0
            ],
            'match' => [
                'p1' => $p1_match,
                'p2' => $p2_match
            ],
            'checked_at' => date('Y-m-d H:i:s')
        ],
        'message' => $status ? 'Lương đúng với Offer' : 'Lương không khớp với Offer'
    ];
}

/**
 * Get probation evaluation for employee
 * API endpoint: /admin/checklist_profile/getProbationEvaluation/{employee_id}
 */
public function getProbationEvaluation($employee_id)
{
    $this->db->select('
        tbl_evaluation_employee.*,
        tbl_evaluation_employee.point,
        tbl_evaluation_employee.rating,
        tbl_evaluation_employee.warning
    ');
    $this->db->from('tbl_evaluation_employee');
    $this->db->where('staff_id', $employee_id);
    $this->db->where('type', 1); // type = 1 là nhân viên chính thức (đã tạo trong hệ thống)
    $this->db->where('evaluation_type', 'PROBATION'); // Loại đánh giá thử việc
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    
    $evaluation = $this->db->get()->row();
    
    if ($this->input->is_ajax_request()) {
        echo json_encode([
            'result' => $evaluation ? 1 : 0,
            'data' => $evaluation,
            'message' => $evaluation ? 'Đã có đánh giá' : 'Chưa có đánh giá thử việc'
        ]);
        return;
    }
    
    return $evaluation;
}

/**
 * Check if employee passed probation evaluation
 * API endpoint: /admin/checklist_profile/checkProbationPass/{employee_id}
 */
public function checkProbationPass($employee_id)
{
    $evaluation = $this->getProbationEvaluation($employee_id);
    
    if (!$evaluation) {
        $result = [
            'status' => 'NO_EVALUATION',
            'can_convert' => false,
            'message' => 'Chưa có đánh giá thử việc. Vui lòng tạo phiếu đánh giá trước.',
            'action_required' => 'CREATE_EVALUATION'
        ];
    } else {
        // Điều kiện PASS: 
        // - Điểm >= 7.0 
        // - Không có warning nghiêm trọng
        $pass = ($evaluation->point >= 7.0) && 
                !in_array($evaluation->warning, ['CRITICAL', 'HIGH_RISK', 'FAIL']);
        
        $result = [
            'status' => $pass ? 'PASS' : 'FAIL',
            'can_convert' => $pass,
            'evaluation' => $evaluation,
            'message' => $pass 
                ? 'Đạt yêu cầu thử việc (Điểm: ' . $evaluation->point . '/10)'
                : 'Chưa đạt yêu cầu thử việc (Điểm: ' . $evaluation->point . '/10)',
            'action_required' => $pass ? null : 'IMPROVE_OR_TERMINATE'
        ];
    }
    
    if ($this->input->is_ajax_request()) {
        echo json_encode($result);
        return;
    }
    
    return $result;
}

/**
 * Create contract labor when converting to full-time
 * API endpoint: POST /admin/checklist_profile/createContractLabor/{checklist_id}
 */
public function createContractLabor($checklist_id)
{
    $checklist = $this->checklist_profile_model->getById($checklist_id);
    
    if (!$checklist) {
        echo json_encode(['result' => 0, 'message' => 'Checklist không tồn tại']);
        return;
    }
    
    if (!$checklist->staff_id) {
        echo json_encode(['result' => 0, 'message' => 'Chưa tạo mã nhân viên']);
        return;
    }
    
    $offer = $this->propose_offer_model->getProposeOfferById($checklist->offer_id);
    
    // Check probation evaluation first
    $probation_check = $this->checkProbationPass($checklist->staff_id);
    
    if (!$probation_check['can_convert']) {
        echo json_encode([
            'result' => 0,
            'message' => $probation_check['message'],
            'evaluation_required' => true
        ]);
        return;
    }
    
    // Check if contract already exists
    $existing_contract = $this->db->where('staff_id', $checklist->staff_id)
        ->where('type_contract_id', 1) // Hợp đồng chính thức
        ->get('tbl_contract_labor')
        ->row();
    
    if ($existing_contract) {
        echo json_encode([
            'result' => 0,
            'message' => 'Nhân viên đã có hợp đồng chính thức',
            'contract_id' => $existing_contract->id
        ]);
        return;
    }
    
    // Create contract labor
    $contract_code = 'HDLD' . date('Ymd') . str_pad($checklist->staff_id, 4, '0', STR_PAD_LEFT);
    
    $contract_data = [
        'code' => $contract_code,
        'staff_id' => $checklist->staff_id,
        'type_contract_id' => 1, // Loại hợp đồng chính thức (có thể lấy từ config)
        'salary_basic' => $offer->luong_p2, // P2 - Lương năng lực
        'salary_position' => $offer->luong_p1, // P1 - Lương vị trí
        'date_sign' => date('Y-m-d'),
        'date_start' => date('Y-m-d'),
        'date_end' => date('Y-m-d', strtotime('+2 years')), // HĐLĐ 2 năm
        'date_sign_contract' => date('Y-m-d'),
        'date_probation' => null, // Đã hết thử việc
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => get_staff_user_id()
    ];
    
    $this->db->insert('tbl_contract_labor', $contract_data);
    $contract_id = $this->db->insert_id();
    
    if (!$contract_id) {
        echo json_encode(['result' => 0, 'message' => 'Không thể tạo hợp đồng lao động']);
        return;
    }
    
    // Update checklist
    $this->checklist_profile_model->update($checklist_id, [
        'contract_labor_id' => $contract_id,
        'contract_sign_date' => $contract_data['date_sign'],
        'contract_start_date' => $contract_data['date_start'],
        'contract_end_date' => $contract_data['date_end'],
        'contract_type' => 'CHINH_THUC',
        'salary_applied_date' => date('Y-m-d'),
        'probation_evaluation_id' => $probation_check['evaluation']->id,
        'probation_evaluation_point' => $probation_check['evaluation']->point,
        'probation_evaluation_status' => 'PASS'
    ]);
    
    // Update staff status to official
    $this->db->where('staffid', $checklist->staff_id);
    $this->db->update('tblstaff', [
        'status_work' => 1, // Chính thức
        'date_status_work' => date('Y-m-d')
    ]);
    
    log_activity('Tạo hợp đồng lao động chính thức [Contract ID: ' . $contract_id . ', Staff ID: ' . $checklist->staff_id . ']');
    
    echo json_encode([
        'result' => 1,
        'contract_id' => $contract_id,
        'contract_code' => $contract_code,
        'message' => 'Tạo hợp đồng lao động thành công! Nhân viên đã chuyển sang chính thức.'
    ]);
}

/**
 * Check contract renewal warning
 * API endpoint: /admin/checklist_profile/checkContractRenewal/{checklist_id}
 */
public function checkContractRenewal($checklist_id)
{
    $checklist = $this->checklist_profile_model->getById($checklist_id);
    
    if (!$checklist || !$checklist->contract_end_date) {
        echo json_encode([
            'result' => 0,
            'need_renewal' => false,
            'message' => 'Không có thông tin hợp đồng'
        ]);
        return;
    }
    
    $end_date = strtotime($checklist->contract_end_date);
    $today = time();
    $days_left = floor(($end_date - $today) / 86400);
    
    $need_renewal = false;
    $warning_level = 'OK';
    $message = '';
    
    if ($days_left < 0) {
        $need_renewal = true;
        $warning_level = 'EXPIRED';
        $message = 'Hợp đồng đã hết hạn ' . abs($days_left) . ' ngày trước';
    } elseif ($days_left <= 30) {
        $need_renewal = true;
        $warning_level = 'CRITICAL';
        $message = "⚠️ KHẨN CẤP: Hợp đồng sắp hết hạn trong {$days_left} ngày!";
    } elseif ($days_left <= 60) {
        $need_renewal = true;
        $warning_level = 'WARNING';
        $message = "⚠️ Cần chuẩn bị tái ký: Hợp đồng còn {$days_left} ngày";
    } elseif ($days_left <= 90) {
        $need_renewal = true;
        $warning_level = 'INFO';
        $message = "ℹ️ Lưu ý: Hợp đồng còn {$days_left} ngày";
    } else {
        $message = "Hợp đồng còn hiệu lực ({$days_left} ngày)";
    }
    
    echo json_encode([
        'result' => 1,
        'need_renewal' => $need_renewal,
        'warning_level' => $warning_level,
        'days_left' => $days_left,
        'end_date' => $checklist->contract_end_date,
        'message' => $message,
        'suggested_action' => $need_renewal ? 'Liên hệ HR để chuẩn bị tái ký hợp đồng' : null
    ]);
}

/**
 * Get all contracts expiring soon (for dashboard widget)
 * API endpoint: /admin/checklist_profile/getExpiringContracts
 */
public function getExpiringContracts()
{
    $days_threshold = $this->input->get('days') ?? 90;
    
    $this->db->select('
        tbl_checklist_profile.*,
        tbl_propose_offer.ma_offer,
        tblstaff.firstname,
        tblstaff.lastname,
        tblstaff.code,
        DATEDIFF(tbl_checklist_profile.contract_end_date, CURDATE()) as days_left
    ');
    $this->db->from('tbl_checklist_profile');
    $this->db->join('tbl_propose_offer', 'tbl_propose_offer.id = tbl_checklist_profile.offer_id', 'left');
    $this->db->join('tblstaff', 'tblstaff.staffid = tbl_checklist_profile.staff_id', 'left');
    $this->db->where('tbl_checklist_profile.contract_end_date IS NOT NULL');
    $this->db->where('DATEDIFF(tbl_checklist_profile.contract_end_date, CURDATE()) <=', $days_threshold);
    $this->db->where('tbl_checklist_profile.status', 'S9'); // Chỉ lấy nhân viên chính thức
    $this->db->order_by('days_left', 'ASC');
    
    $contracts = $this->db->get()->result();
    
    echo json_encode([
        'result' => 1,
        'data' => $contracts,
        'count' => count($contracts)
    ]);
}
