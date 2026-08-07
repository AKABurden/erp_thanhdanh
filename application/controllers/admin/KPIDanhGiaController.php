<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KPIDanhGiaController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('session'));
        $this->load->database();
    }

    public function index()
    {
        $data = array();
        $data['title'] = 'Đánh giá KPI';
        $data['nhan_su_list'] = $this->getNhanSuList();
        $data['danh_gia_list'] = $this->getDanhGiaList();
        $data['loai_danh_gia_list'] = array('KPI tháng', 'KPI quý', 'KPI năm');
        $data['use_sample_data'] = true;
        $this->load->view('admin/KPIDanhGia/index', $data);
    }

    public function store()
    {
        $input = $this->input->post(NULL, true);

        $nhanSuId = isset($input['nhan_su_id']) ? $input['nhan_su_id'] : '';
        $loaiDanhGia = isset($input['loai_danh_gia']) ? $input['loai_danh_gia'] : 'KPI tháng';
        $kyDanhGia = isset($input['ky_danh_gia']) ? $input['ky_danh_gia'] : '';
        $hoSoDayDu = $this->toBool(isset($input['ho_so_day_du']) ? $input['ho_so_day_du'] : false);
        $trainingCompleted = $this->toBool(isset($input['training_completed']) ? $input['training_completed'] : false);
        $sopCompliance = $this->toBool(isset($input['sop_compliance']) ? $input['sop_compliance'] : false);
        $p2Raw = floatval(isset($input['p2_raw']) ? $input['p2_raw'] : 0);
        $complianceRaw = floatval(isset($input['compliance_raw']) ? $input['compliance_raw'] : 0);
        $p3Raw = floatval(isset($input['p3_raw']) ? $input['p3_raw'] : 0);
        $ghiChu = isset($input['ghi_chu']) ? $input['ghi_chu'] : '';

        $gate1Result = $this->calculateGate1($hoSoDayDu, $trainingCompleted, $sopCompliance);
        $p2Final = $this->calculateP2Final($p2Raw);
        $complianceFinal = $this->calculateComplianceFinal($complianceRaw);
        $p3Final = $this->calculateP3Final($p3Raw);
        $tongDiem = $p2Final + $complianceFinal + $p3Final;

        $data = array(
            'nhan_su_id' => $nhanSuId,
            'loai_danh_gia' => $loaiDanhGia,
            'ky_danh_gia' => $kyDanhGia,
            'ho_so_day_du' => $hoSoDayDu ? 1 : 0,
            'training_completed' => $trainingCompleted ? 1 : 0,
            'sop_compliance' => $sopCompliance ? 1 : 0,
            'gate_1_result' => $gate1Result,
            'p2_raw' => $p2Raw,
            'p2_final' => $p2Final,
            'compliance_raw' => $complianceRaw,
            'compliance_final' => $complianceFinal,
            'p3_raw' => $p3Raw,
            'p3_final' => $p3Final,
            'tong_diem' => $tongDiem,
            'xep_loai' => $this->calculateXepLoai($tongDiem),
            'quyet_dinh' => $this->calculateQuyetDinh($gate1Result, $tongDiem),
            'risk_level' => $this->calculateRiskLevel($tongDiem),
            'ghi_chu' => $ghiChu,
            'created_at' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('kpi_danh_gia', $data);
        redirect('kpidanhgia');
    }

    public function download_template()
    {
        $headers = array(
            'nhan_su_id',
            'loai_danh_gia',
            'ky_danh_gia',
            'ho_so_day_du',
            'training_completed',
            'sop_compliance',
            'p2_raw',
            'compliance_raw',
            'p3_raw',
            'ghi_chu'
        );

        $sample = array(
            array('ns-1', 'KPI tháng', '2024-01', '1', '1', '1', '92', '90', '88', 'Đạt KPI tốt'),
            array('ns-2', 'KPI tháng', '2024-01', '1', '1', '1', '96', '94', '91', 'Kết quả rất tốt'),
        );

        $filename = 'kpi_danh_gia_template.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($sample as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function import()
    {
        if (empty($_FILES['file']['name'])) {
            $this->session->set_flashdata('error', 'Vui lòng chọn file import.');
            redirect('kpidanhgia');
        }

        $path = $_FILES['file']['tmp_name'];
        $rows = array_map('str_getcsv', file($path));
        if (count($rows) < 2) {
            $this->session->set_flashdata('error', 'File không có dữ liệu hợp lệ.');
            redirect('kpidanhgia');
        }

        $headers = array_map('strtolower', array_map('trim', array_shift($rows)));
        $count = 0;

        foreach ($rows as $row) {
            if (count(array_filter($row, 'strlen')) === 0) {
                continue;
            }

            $item = array();
            foreach ($headers as $index => $header) {
                $value = isset($row[$index]) ? trim($row[$index]) : '';
                $item[$header] = $value;
            }

            if (empty($item['nhan_su_id']) || empty($item['loai_danh_gia']) || empty($item['ky_danh_gia'])) {
                continue;
            }

            $hoSoDayDu = $this->toBool(isset($item['ho_so_day_du']) ? $item['ho_so_day_du'] : false);
            $trainingCompleted = $this->toBool(isset($item['training_completed']) ? $item['training_completed'] : false);
            $sopCompliance = $this->toBool(isset($item['sop_compliance']) ? $item['sop_compliance'] : false);
            $p2Raw = floatval(isset($item['p2_raw']) ? $item['p2_raw'] : 0);
            $complianceRaw = floatval(isset($item['compliance_raw']) ? $item['compliance_raw'] : 0);
            $p3Raw = floatval(isset($item['p3_raw']) ? $item['p3_raw'] : 0);
            $gate1Result = $this->calculateGate1($hoSoDayDu, $trainingCompleted, $sopCompliance);
            $p2Final = $this->calculateP2Final($p2Raw);
            $complianceFinal = $this->calculateComplianceFinal($complianceRaw);
            $p3Final = $this->calculateP3Final($p3Raw);
            $tongDiem = $p2Final + $complianceFinal + $p3Final;

            $this->db->insert('kpi_danh_gia', array(
                'nhan_su_id' => $item['nhan_su_id'],
                'loai_danh_gia' => $item['loai_danh_gia'],
                'ky_danh_gia' => $item['ky_danh_gia'],
                'ho_so_day_du' => $hoSoDayDu ? 1 : 0,
                'training_completed' => $trainingCompleted ? 1 : 0,
                'sop_compliance' => $sopCompliance ? 1 : 0,
                'gate_1_result' => $gate1Result,
                'p2_raw' => $p2Raw,
                'p2_final' => $p2Final,
                'compliance_raw' => $complianceRaw,
                'compliance_final' => $complianceFinal,
                'p3_raw' => $p3Raw,
                'p3_final' => $p3Final,
                'tong_diem' => $tongDiem,
                'xep_loai' => $this->calculateXepLoai($tongDiem),
                'quyet_dinh' => $this->calculateQuyetDinh($gate1Result, $tongDiem),
                'risk_level' => $this->calculateRiskLevel($tongDiem),
                'ghi_chu' => isset($item['ghi_chu']) ? $item['ghi_chu'] : '',
                'created_at' => date('Y-m-d H:i:s'),
            ));
            $count++;
        }

        $this->session->set_flashdata('success', 'Import thành công ' . $count . ' bản ghi.');
        redirect('kpidanhgia');
    }

    private function getNhanSuList()
    {
        return array(
            array('id' => 'ns-1', 'ho_ten' => 'Nguyễn Văn A', 'ma_nhan_vien' => 'NV001'),
            array('id' => 'ns-2', 'ho_ten' => 'Trần Thị B', 'ma_nhan_vien' => 'NV002'),
            array('id' => 'ns-3', 'ho_ten' => 'Lê Văn C', 'ma_nhan_vien' => 'NV003'),
            array('id' => 'ns-4', 'ho_ten' => 'Phạm Thị D', 'ma_nhan_vien' => 'NV004'),
        );
    }

    private function getDanhGiaList()
    {
        return array(
            array(
                'id' => 1,
                'nhan_su_id' => 'ns-1',
                'ho_ten' => 'Nguyễn Văn A',
                'ma_nhan_vien' => 'NV001',
                'loai_danh_gia' => 'KPI tháng',
                'ky_danh_gia' => '2024-01',
                'gate_1_result' => 'PASS',
                'tong_diem' => 87.4,
                'xep_loai' => 'Tốt',
                'quyet_dinh' => 'ĐẠT',
                'risk_level' => 'Low',
            ),
            array(
                'id' => 2,
                'nhan_su_id' => 'ns-2',
                'ho_ten' => 'Trần Thị B',
                'ma_nhan_vien' => 'NV002',
                'loai_danh_gia' => 'KPI tháng',
                'ky_danh_gia' => '2024-01',
                'gate_1_result' => 'PASS',
                'tong_diem' => 74.2,
                'xep_loai' => 'Đạt',
                'quyet_dinh' => 'ĐẠT',
                'risk_level' => 'Medium',
            ),
            array(
                'id' => 3,
                'nhan_su_id' => 'ns-3',
                'ho_ten' => 'Lê Văn C',
                'ma_nhan_vien' => 'NV003',
                'loai_danh_gia' => 'KPI quý',
                'ky_danh_gia' => '2024-Q1',
                'gate_1_result' => 'FAIL',
                'tong_diem' => 58.9,
                'xep_loai' => 'Cần giám sát',
                'quyet_dinh' => 'FAIL',
                'risk_level' => 'High',
            ),
        );
    }

    private function calculateGate1($hoSoDayDu, $training, $sop)
    {
        return ($hoSoDayDu && $training && $sop) ? 'PASS' : 'FAIL';
    }

    private function calculateP2Final($p2Raw)
    {
        return min(60, max(0, $p2Raw * 0.6));
    }

    private function calculateComplianceFinal($complianceRaw)
    {
        return min(20, max(0, $complianceRaw * 0.2));
    }

    private function calculateP3Final($p3Raw)
    {
        return min(20, max(0, $p3Raw * 0.2));
    }

    private function calculateXepLoai($tongDiem)
    {
        if ($tongDiem >= 90) return 'Xuất sắc';
        if ($tongDiem >= 80) return 'Tốt';
        if ($tongDiem >= 70) return 'Đạt';
        if ($tongDiem >= 60) return 'Cần giám sát';
        return 'Không đạt';
    }

    private function calculateQuyetDinh($gate1, $tongDiem)
    {
        if ($gate1 === 'FAIL') return 'FAIL';
        if ($tongDiem >= 70) return 'ĐẠT';
        if ($tongDiem >= 60) return 'GIÁM SÁT';
        return 'FAIL';
    }

    private function calculateRiskLevel($tongDiem)
    {
        if ($tongDiem >= 80) return 'Low';
        if ($tongDiem >= 60) return 'Medium';
        return 'High';
    }

    private function toBool($value)
    {
        if (is_bool($value)) return $value;
        $value = strtolower(trim((string) $value));
        return in_array($value, array('1', 'true', 'yes', 'on', 'co', 'có'), true);
    }
}
