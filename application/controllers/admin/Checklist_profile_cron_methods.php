<?php
/**
 * Thêm các method này vào Controller Checklist_profile.php
 * Để chạy daily cron job thay thế MySQL Event Scheduler
 */

/**
 * Cron job - Update contract renewal alerts daily
 * Chạy bằng cron: 0 0 * * * php /path/to/index.php checklist_profile/cronContractRenewalCheck
 * Hoặc từ trình duyệt: admin/checklist_profile/cronContractRenewalCheck?cron_key=YOUR_SECRET_KEY
 */
public function cronContractRenewalCheck()
{
    // Security check - chỉ cho phép chạy từ CLI hoặc có key
    $cron_key = $this->input->get('cron_key');
    $expected_key = get_option('cron_secret_key') ?: 'your-secret-key-here';
    
    if (!is_cli() && $cron_key !== $expected_key) {
        show_error('Unauthorized access', 403);
        return;
    }
    
    $this->load->helper('checklist_profile');
    
    // Chạy update
    $updated_count = update_contract_renewal_alerts();
    
    // Get contracts expiring in 30 days
    $critical_contracts = get_expiring_contracts(30);
    
    // Send notification if có critical cases
    if (!empty($critical_contracts)) {
        $this->sendContractExpiryNotification($critical_contracts);
    }
    
    $message = sprintf(
        'Contract Renewal Check Completed: %d records updated, %d critical alerts',
        $updated_count,
        count($critical_contracts)
    );
    
    log_activity($message);
    
    if (is_cli()) {
        echo $message . PHP_EOL;
    } else {
        echo json_encode([
            'result' => 1,
            'updated' => $updated_count,
            'critical' => count($critical_contracts),
            'message' => $message
        ]);
    }
}

/**
 * Send email notification for expiring contracts
 */
private function sendContractExpiryNotification($contracts)
{
    $this->load->library('email');
    
    $message = '<h3>Cảnh Báo Hợp Đồng Lao Động Sắp Hết Hạn</h3>';
    $message .= '<p>Có <strong>' . count($contracts) . '</strong> hợp đồng lao động sắp hết hạn trong 30 ngày:</p>';
    $message .= '<table border="1" cellpadding="5" style="border-collapse: collapse;">';
    $message .= '<tr><th>Mã NV</th><th>Họ tên</th><th>Ngày hết hạn</th><th>Còn lại</th><th>Trạng thái</th></tr>';
    
    foreach ($contracts as $contract) {
        $full_name = trim($contract->firstname . ' ' . $contract->lastname);
        $renewal = get_contract_renewal_status($contract);
        $badge_color = get_renewal_badge_color($renewal['level']);
        
        $message .= sprintf(
            '<tr><td>%s</td><td>%s</td><td>%s</td><td>%d ngày</td><td style="background: %s; color: white;">%s</td></tr>',
            $contract->staff_code,
            $full_name,
            date('d/m/Y', strtotime($contract->contract_end_date)),
            $contract->days_left,
            $this->getBadgeColorHex($badge_color),
            $renewal['status']
        );
    }
    
    $message .= '</table>';
    $message .= '<p><small>Email tự động từ hệ thống HR - ' . date('d/m/Y H:i:s') . '</small></p>';
    
    // Get HR emails from settings or use default
    $hr_emails = get_option('hr_notification_emails');
    if (!$hr_emails) {
        $hr_emails = 'hr@company.com'; // Default email
    }
    
    $this->email->from(get_option('smtp_email') ?: 'system@company.com', 'HR System');
    $this->email->to($hr_emails);
    $this->email->subject('[URGENT] Cảnh báo ' . count($contracts) . ' hợp đồng sắp hết hạn');
    $this->email->message($message);
    
    return $this->email->send();
}

/**
 * Get hex color for badge
 */
private function getBadgeColorHex($color)
{
    $colors = [
        'danger' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#17a2b8',
        'success' => '#28a745',
        'dark' => '#343a40'
    ];
    
    return $colors[$color] ?? '#6c757d';
}

/**
 * Dashboard widget - Get expiring contracts summary
 * API: /admin/checklist_profile/getExpiringContractsSummary
 */
public function getExpiringContractsSummary()
{
    $this->load->helper('checklist_profile');
    
    $contracts_30 = get_expiring_contracts(30);
    $contracts_60 = get_expiring_contracts(60);
    $contracts_90 = get_expiring_contracts(90);
    
    $summary = [
        'critical' => count($contracts_30),  // <= 30 days
        'warning' => count($contracts_60) - count($contracts_30), // 31-60 days
        'info' => count($contracts_90) - count($contracts_60), // 61-90 days
        'total' => count($contracts_90),
        'contracts' => [
            'critical' => $contracts_30,
            'warning' => array_slice($contracts_60, count($contracts_30)),
            'info' => array_slice($contracts_90, count($contracts_60))
        ]
    ];
    
    echo json_encode([
        'result' => 1,
        'data' => $summary
    ]);
}

/**
 * Get full checklist data with all related info
 * API: /admin/checklist_profile/getFullData/{id}
 */
public function getFullData($checklist_id)
{
    $this->load->helper('checklist_profile');
    
    $data = get_checklist_full_data($checklist_id);
    
    if ($data) {
        // Add verification summary
        $data->verification_summary = get_verification_summary($checklist_id);
        
        // Add contract renewal status
        if ($data->contract_end_date) {
            $data->renewal_status = get_contract_renewal_status($data);
        }
        
        echo json_encode([
            'result' => 1,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'result' => 0,
            'message' => 'Không tìm thấy checklist'
        ]);
    }
}
