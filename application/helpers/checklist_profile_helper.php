<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Checklist Profile Helper Functions
 * Thay thế VIEW và Stored Procedure bằng PHP code
 */

/**
 * Get full checklist profile data with all related info
 * Thay thế VIEW view_checklist_profile_full
 * 
 * @param int $checklist_id
 * @return object|null
 */
function get_checklist_full_data($checklist_id)
{
    $CI =& get_instance();
    
    $CI->db->select('
        cp.*,
        po.ma_offer,
        po.vi_tri_offer,
        po.phong_ban_offer,
        po.luong_p1,
        po.luong_p2,
        po.phu_cap,
        ep.full_name as candidate_name,
        ep.email as candidate_email,
        ep.phone_number as candidate_phone,
        cl.code as contract_code,
        cl.date_sign as actual_contract_sign_date,
        cl.date_start as actual_contract_start_date,
        cl.date_end as actual_contract_end_date,
        cl.salary_basic,
        cl.salary_position,
        ee.point as probation_point,
        ee.rating as probation_rating,
        ee.warning as probation_warning,
        ee.note as probation_note
    ');
    
    $CI->db->from('tbl_checklist_profile cp');
    $CI->db->join('tbl_propose_offer po', 'po.id = cp.offer_id', 'left');
    $CI->db->join('tbl_hr_eprofile ep', 'ep.id = po.kqpv_id', 'left');
    $CI->db->join('tbl_contract_labor cl', 'cl.id = cp.contract_labor_id', 'left');
    $CI->db->join('tbl_evaluation_employee ee', 'ee.id = cp.probation_evaluation_id', 'left');
    $CI->db->where('cp.id', $checklist_id);
    
    $result = $CI->db->get()->row();
    
    // Tính toán contract renewal status
    if ($result && $result->actual_contract_end_date) {
        $end_date = strtotime($result->actual_contract_end_date);
        $today = time();
        $days_until_end = floor(($end_date - $today) / 86400);
        
        $result->days_until_contract_end = $days_until_end;
        
        if ($days_until_end <= 30) {
            $result->contract_renewal_status = 'CRITICAL';
        } elseif ($days_until_end <= 60) {
            $result->contract_renewal_status = 'WARNING';
        } elseif ($days_until_end <= 90) {
            $result->contract_renewal_status = 'INFO';
        } else {
            $result->contract_renewal_status = 'OK';
        }
    }
    
    return $result;
}

/**
 * Update contract renewal alerts for all checklists
 * Thay thế Stored Procedure sp_update_contract_renewal_alerts()
 * 
 * @return int Number of updated records
 */
function update_contract_renewal_alerts()
{
    $CI =& get_instance();
    
    // Lấy tất cả checklist có contract_end_date và status S8/S9
    $CI->db->where('contract_end_date IS NOT NULL');
    $CI->db->where_in('status', ['S8', 'S9']);
    $checklists = $CI->db->get('tbl_checklist_profile')->result();
    
    $updated = 0;
    
    foreach ($checklists as $checklist) {
        // Tính ngày cảnh báo = ngày kết thúc - 90 ngày
        $end_date = strtotime($checklist->contract_end_date);
        $alert_date = date('Y-m-d', strtotime('-90 days', $end_date));
        
        // Update
        $CI->db->where('id', $checklist->id);
        $CI->db->update('tbl_checklist_profile', [
            'contract_renewal_alert_date' => $alert_date
        ]);
        
        if ($CI->db->affected_rows() > 0) {
            $updated++;
        }
    }
    
    return $updated;
}

/**
 * Get contract renewal status for a checklist
 * 
 * @param object $checklist
 * @return array
 */
function get_contract_renewal_status($checklist)
{
    if (!$checklist || !$checklist->contract_end_date) {
        return [
            'status' => 'NO_CONTRACT',
            'level' => 'INFO',
            'days_left' => null,
            'message' => 'Chưa có thông tin hợp đồng'
        ];
    }
    
    $end_date = strtotime($checklist->contract_end_date);
    $today = time();
    $days_left = floor(($end_date - $today) / 86400);
    
    if ($days_left < 0) {
        return [
            'status' => 'EXPIRED',
            'level' => 'CRITICAL',
            'days_left' => $days_left,
            'message' => 'Hợp đồng đã hết hạn ' . abs($days_left) . ' ngày trước'
        ];
    } elseif ($days_left <= 30) {
        return [
            'status' => 'CRITICAL',
            'level' => 'CRITICAL',
            'days_left' => $days_left,
            'message' => '⚠️ KHẨN CẤP: Hợp đồng sắp hết hạn trong ' . $days_left . ' ngày!'
        ];
    } elseif ($days_left <= 60) {
        return [
            'status' => 'WARNING',
            'level' => 'WARNING',
            'days_left' => $days_left,
            'message' => '⚠️ Cần chuẩn bị tái ký: Hợp đồng còn ' . $days_left . ' ngày'
        ];
    } elseif ($days_left <= 90) {
        return [
            'status' => 'INFO',
            'level' => 'INFO',
            'days_left' => $days_left,
            'message' => 'ℹ️ Lưu ý: Hợp đồng còn ' . $days_left . ' ngày'
        ];
    } else {
        return [
            'status' => 'OK',
            'level' => 'OK',
            'days_left' => $days_left,
            'message' => 'Hợp đồng còn hiệu lực (' . $days_left . ' ngày)'
        ];
    }
}

/**
 * Get checklists with contracts expiring soon
 * 
 * @param int $days_threshold Default 90 days
 * @return array
 */
function get_expiring_contracts($days_threshold = 90)
{
    $CI =& get_instance();
    
    $CI->db->select('
        cp.*,
        po.ma_offer,
        s.firstname,
        s.lastname,
        s.code as staff_code,
        DATEDIFF(cp.contract_end_date, CURDATE()) as days_left
    ');
    $CI->db->from('tbl_checklist_profile cp');
    $CI->db->join('tbl_propose_offer po', 'po.id = cp.offer_id', 'left');
    $CI->db->join('tblstaff s', 's.staffid = cp.staff_id', 'left');
    $CI->db->where('cp.contract_end_date IS NOT NULL');
    $CI->db->where('DATEDIFF(cp.contract_end_date, CURDATE()) <=', $days_threshold);
    $CI->db->where('cp.status', 'S9'); // Chỉ nhân viên chính thức
    $CI->db->order_by('days_left', 'ASC');
    
    $contracts = $CI->db->get()->result();
    
    // Add renewal status to each contract
    foreach ($contracts as &$contract) {
        $contract->renewal_status = get_contract_renewal_status($contract);
    }
    
    return $contracts;
}

/**
 * Check if all checklist items are verified (auto-check)
 * 
 * @param array $verification_results
 * @return bool
 */
function all_checklist_items_verified($verification_results)
{
    if (empty($verification_results)) {
        return false;
    }
    
    foreach ($verification_results as $item => $result) {
        if (!isset($result['status']) || $result['status'] !== true) {
            return false;
        }
    }
    
    return true;
}

/**
 * Get badge color for contract renewal status
 * 
 * @param string $level CRITICAL, WARNING, INFO, OK
 * @return string Bootstrap color class
 */
function get_renewal_badge_color($level)
{
    $colors = [
        'CRITICAL' => 'danger',
        'WARNING' => 'warning',
        'INFO' => 'info',
        'OK' => 'success',
        'EXPIRED' => 'dark'
    ];
    
    return $colors[$level] ?? 'default';
}

/**
 * Format contract renewal message with icon
 * 
 * @param array $renewal_status
 * @return string HTML formatted message
 */
function format_renewal_message($renewal_status)
{
    $icons = [
        'CRITICAL' => '<i class="fa fa-exclamation-triangle"></i>',
        'WARNING' => '<i class="fa fa-exclamation-circle"></i>',
        'INFO' => '<i class="fa fa-info-circle"></i>',
        'OK' => '<i class="fa fa-check-circle"></i>',
        'EXPIRED' => '<i class="fa fa-ban"></i>'
    ];
    
    $icon = $icons[$renewal_status['status']] ?? '';
    
    return $icon . ' ' . $renewal_status['message'];
}

/**
 * Run daily contract renewal check (call from cron job)
 * Usage: php index.php checklist_profile/cronContractRenewalCheck
 * 
 * @return void
 */
function run_daily_contract_renewal_check()
{
    $updated = update_contract_renewal_alerts();
    
    // Log activity
    log_activity('Cron: Updated contract renewal alerts for ' . $updated . ' checklists');
    
    // Optionally send notification emails for critical cases
    $critical_contracts = get_expiring_contracts(30);
    
    if (!empty($critical_contracts)) {
        // Send email notification to HR
        $CI =& get_instance();
        $CI->load->library('email');
        
        $message = 'Có ' . count($critical_contracts) . ' hợp đồng lao động sắp hết hạn trong 30 ngày:\n\n';
        
        foreach ($critical_contracts as $contract) {
            $message .= '- ' . ($contract->firstname . ' ' . $contract->lastname) . 
                       ' (' . $contract->staff_code . ')' . 
                       ' - Còn ' . $contract->days_left . ' ngày\n';
        }
        
        // Send to HR email (config trong settings)
        $hr_email = get_option('hr_notification_email') ?: 'hr@company.com';
        
        $CI->email->from('system@company.com', 'HR System');
        $CI->email->to($hr_email);
        $CI->email->subject('[URGENT] Cảnh báo hợp đồng lao động sắp hết hạn');
        $CI->email->message($message);
        $CI->email->send();
    }
    
    return $updated;
}

/**
 * Get verification summary for checklist
 * 
 * @param int $checklist_id
 * @return array
 */
function get_verification_summary($checklist_id)
{
    $CI =& get_instance();
    
    $checklist = $CI->db->get_where('tbl_checklist_profile', ['id' => $checklist_id])->row();
    
    if (!$checklist || !$checklist->auto_verification_results) {
        return [
            'total' => 5,
            'passed' => 0,
            'failed' => 5,
            'percentage' => 0
        ];
    }
    
    $results = json_decode($checklist->auto_verification_results, true);
    
    $total = count($results);
    $passed = 0;
    
    foreach ($results as $item => $result) {
        if (isset($result['status']) && $result['status'] === true) {
            $passed++;
        }
    }
    
    return [
        'total' => $total,
        'passed' => $passed,
        'failed' => $total - $passed,
        'percentage' => $total > 0 ? round(($passed / $total) * 100) : 0
    ];
}
