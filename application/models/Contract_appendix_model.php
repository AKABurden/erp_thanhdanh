<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contract_appendix_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lấy thông tin phụ lục hợp đồng theo ID
     */
    public function get($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tbl_contract_appendix')->row();
        }
        return $this->db->get('tbl_contract_appendix')->result_array();
    }

    /**
     * Thêm phụ lục hợp đồng mới
     */
    public function add($data)
    {
        $this->db->insert('tbl_contract_appendix', $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('Thêm phụ lục hợp đồng [ID: ' . $insert_id . ']');
        }
        
        return $insert_id;
    }

    /**
     * Cập nhật phụ lục hợp đồng
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_contract_appendix', $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Cập nhật phụ lục hợp đồng [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Xóa phụ lục hợp đồng
     */
    public function delete($id)
    {
        $appendix = $this->get($id);
        
        if (!$appendix) {
            return false;
        }
        
        // Xóa file nếu có
        if (!empty($appendix->file_path) && file_exists($appendix->file_path)) {
            unlink($appendix->file_path);
        }
        
        $this->db->where('id', $id);
        $this->db->delete('tbl_contract_appendix');
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Xóa phụ lục hợp đồng [Code: ' . $appendix->code . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Thay đổi trạng thái phụ lục hợp đồng
     */
    public function change_status($id, $status, $user_id)
    {
        $data = [
            'status' => $status,
            'user_status' => $user_id,
            'date_status' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        $this->db->update('tbl_contract_appendix', $data);
        
        // Nếu duyệt phụ lục (status = 1), cập nhật lương trong hợp đồng
        if ($status == 1) {
            $appendix = $this->get($id);
            if ($appendix && $appendix->salary > 0) {
                $this->update_contract_salary($appendix->contract_labor_id, $appendix->salary);
            }
        }
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Thay đổi trạng thái phụ lục hợp đồng [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Cập nhật lương trong hợp đồng lao động
     */
    private function update_contract_salary($contract_id, $new_salary)
    {
        $data = [
            'salary_basic' => $new_salary
        ];
        
        $this->db->where('id', $contract_id);
        $this->db->update('tbl_contract_labor', $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Cập nhật lương hợp đồng [ID: ' . $contract_id . '] - Lương mới: ' . $new_salary);
            return true;
        }
        
        return false;
    }

    /**
     * Upload file phụ lục
     */
    public function handle_file_upload($contract_appendix_id)
    {
        if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {
            // Lấy thông tin file cũ để xóa
            $this->db->select('file_path');
            $this->db->where('id', $contract_appendix_id);
            $old_data = $this->db->get('tbl_contract_appendix')->row();
            
            // Debug: Lấy thông tin file để kiểm tra
            $file_name = $_FILES['file']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_mime = $_FILES['file']['type'];
            $file_size = $_FILES['file']['size'];
            
            $upload_path = FCPATH . 'uploads/contract_appendix/';
            
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png|gif';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = true;
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
                $file_path = 'uploads/contract_appendix/' . $upload_data['file_name'];
                
                // Xóa file cũ nếu có
                if ($old_data && !empty($old_data->file_path)) {
                    $old_file = FCPATH . $old_data->file_path;
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                
                // Cập nhật đường dẫn file mới vào database
                $this->db->where('id', $contract_appendix_id);
                $this->db->update('tbl_contract_appendix', ['file_path' => $file_path]);
                
                return [
                    'success' => true,
                    'file_path' => $file_path,
                    'file_name' => $upload_data['file_name']
                ];
            } else {
                $error_msg = $this->upload->display_errors('', '');
                // Thêm thông tin chi tiết về file bị reject
                $debug_info = "File: {$file_name} | Extension: .{$file_ext} | MIME Type: {$file_mime} | Size: " . round($file_size/1024, 2) . "KB";
                
                return [
                    'success' => false,
                    'error' => $error_msg . " - " . $debug_info
                ];
            }
        }
        
        return ['success' => true, 'message' => 'No file uploaded'];
    }
}
