<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KpiDanhGia extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    // ----------------------------------------------------------------
    // MAIN: Load layout + tab tương ứng
    // ----------------------------------------------------------------
    public function index($active_tab = 'dashboard')
    {
        $valid_tabs = ['dashboard', 'kpi_import', 'nhan_su', 'danh_gia', 'tong_hop', 'form_in'];

        if (!in_array($active_tab, $valid_tabs)) {
            $active_tab = 'dashboard';
        }

        $data['active_tab'] = $active_tab;

        // Tab Nhân sự: hiển thị danh sách từ tblstaff
        if ($active_tab === 'nhan_su') {
            $this->db->select("s.staffid as id, CONCAT(s.firstname,' ',s.lastname) as ho_ten, s.email, s.active, s.status_work, d.name as ten_phong_ban");
            $this->db->from('tblstaff s');
            $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left');
            $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
            $this->db->group_by('s.staffid');
            $this->db->order_by('s.firstname', 'ASC');
            $data['nhan_su_list'] = $this->db->get()->result_array();
        }

        if ($active_tab === 'kpi_import') {
            $data['kpi_import_list'] = $this->db->order_by('created_at', 'DESC')->get('tbl_kpi_import')->result_array();
            $this->db->distinct()->select('muc_tieu_kpi');
            $data['unique_muc_tieu'] = $this->db->get('tbl_kpi_import')->result_array();
        }

        if ($active_tab === 'danh_gia') {
            $this->db->select("dg.*, CONCAT(ns.firstname,' ',ns.lastname) as ho_ten, ns.staffid as ma_nhan_vien");
            $this->db->from('tbl_kpi_danh_gia dg');
            $this->db->join('tblstaff ns', 'ns.staffid = dg.nhan_su_id', 'left');
            $this->db->order_by('dg.created_at', 'DESC');
            $data['danh_gia_list'] = $this->db->get()->result_array();

            $this->db->select("s.staffid as id, CONCAT(s.firstname,' ',s.lastname) as ho_ten,s.code as ma_nhan_vien, s.email, s.active, s.status_work, d.name as ten_phong_ban");
            $this->db->from('tblstaff s');
            $this->db->join('tblstaff_departments sd', 'sd.staffid = s.staffid', 'left');
            $this->db->join('tbldepartments d', 'd.departmentid = sd.departmentid', 'left');
            $this->db->group_by('s.staffid');
            $this->db->order_by('s.firstname', 'ASC');
            $data['nhan_su_list'] = $this->db->get()->result_array();
        }

        if ($active_tab === 'tong_hop') {
            $this->db->select("dg.*, CONCAT(ns.firstname,' ',ns.lastname) as ho_ten, ns.staffid as ma_nhan_vien");
            $this->db->from('tbl_kpi_danh_gia dg');
            $this->db->join('tblstaff ns', 'ns.staffid = dg.nhan_su_id', 'left');
            $data['danh_gia_list']    = $this->db->get()->result_array();
            $data['thresholds']       = $this->db->get('tbl_kpi_nsph_tonghop')->result_array();
            $data['conversion_rules'] = $this->db->where('loai_quy_doi', 'phan_tram_diem')->get('tbl_kpi_dm_quy_doi')->result_array();
        }

        if ($active_tab === 'form_in') {
            $this->db->select("dg.*, CONCAT(ns.firstname,' ',ns.lastname) as ho_ten, ns.staffid as ma_nhan_vien");
            $this->db->from('tbl_kpi_danh_gia dg');
            $this->db->join('tblstaff ns', 'ns.staffid = dg.nhan_su_id', 'left');
            $this->db->order_by('dg.created_at', 'DESC');
            $data['danh_gia_list'] = $this->db->get()->result_array();
            $data['selected']      = null;

            $id = $this->input->get('id');
            if ($id) {
                $this->db->select("dg.*, CONCAT(ns.firstname,' ',ns.lastname) as ho_ten, ns.staffid as ma_nhan_vien");
                $this->db->from('tbl_kpi_danh_gia dg');
                $this->db->join('tblstaff ns', 'ns.staffid = dg.nhan_su_id', 'left');
                $this->db->where('dg.id', (int)$id);
                $data['selected'] = $this->db->get()->row_array();
            }
        }

        $this->load->view('admin/kpi_danh_gia/index', $data);
    }

    // ================================================================
    // AJAX: Dashboard stats
    // ================================================================
    public function get_dashboard_data()
    {
        $total_import   = $this->db->count_all('tbl_kpi_import');
        $total_nhan_su  = $this->db->where('active', 1)->count_all_results('tblstaff');
        $total_danh_gia = $this->db->count_all('tbl_kpi_danh_gia');

        $this->db->where('quyet_dinh', 'ĐẠT');
        $pass = $this->db->count_all_results('tbl_kpi_danh_gia');

        $this->db->where('quyet_dinh', 'FAIL');
        $fail = $this->db->count_all_results('tbl_kpi_danh_gia');

        $this->db->where('quyet_dinh', 'GIÁM SÁT');
        $giam_sat = $this->db->count_all_results('tbl_kpi_danh_gia');

        $thresholds = $this->db->get('tbl_kpi_nsph_tonghop')->result_array();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => [
                'total_import'   => $total_import,
                'total_nhan_su'  => $total_nhan_su,
                'total_danh_gia' => $total_danh_gia,
                'pass'           => $pass,
                'fail'           => $fail,
                'giam_sat'       => $giam_sat,
                'thresholds'     => $thresholds,
            ]
        ]);
    }

    // ================================================================
    // Download file mẫu XLSX cho KPI Import
    // ================================================================
    public function download_template_kpi()
    {
        $headers = array(
            'ma_phong_ban', 'ten_phong_ban', 'ma_vi_tri', 'chuc_vu',
            'muc_tieu_kpi', 'ma_cong_viec', 'ten_cong_viec',
            'ma_vi_pham', 'loai_vi_pham',
            'diem_chuan', 'diem_sau_xu_ly',
            'kpi_tien_chuan', 'kpi_tien_thuc_nhan', 'ty_le_huong_kpi', 'loai_kpi'
        );
        $example = array(
            'PB001', 'Phòng Kinh doanh', 'VT001', 'Trưởng phòng',
            'Doanh thu tháng', 'CV001', 'Theo dõi KH',
            '', '',
            '100', '100', '5000000', '5000000', '1', 'P2'
        );

        $title = 'Mẫu KPI Import';
        $filename = 'mau_kpi_import.xlsx';
        $instructions = array(
            'Hàng 1 là tiêu đề cột.',
            'Hàng 2 là dữ liệu ví dụ, có thể xóa trước khi import.',
            'Các cột mã nên giữ đúng định dạng text để không bị mất số 0 ở đầu.',
        );

        $this->download_xlsx_template($filename, $title, $headers, $example, $instructions);
    }

    // ================================================================
    // Download file mẫu XLSX cho Đánh giá KPI
    // ================================================================
    public function download_template_danh_gia()
    {
        $headers = array(
            'nhan_su_id', 'loai_danh_gia', 'ky_danh_gia',
            'ho_so_day_du', 'training_completed', 'sop_compliance',
            'p2_raw', 'compliance_raw', 'p3_raw', 'ghi_chu'
        );
        $example = array(
            '1', 'KPI tháng', '2024-M01',
            '1', '1', '1',
            '85', '90', '80', 'Ghi chú mẫu'
        );

        $title = 'Mẫu Đánh giá KPI';
        $filename = 'mau_danh_gia_kpi.xlsx';
        $instructions = array(
            'Hàng 1 là tiêu đề cột.',
            'Hàng 2 là dữ liệu ví dụ, có thể xóa trước khi import.',
            'Cột nhan_su_id phải là ID nhân sự hợp lệ trong hệ thống.',
        );

        $this->download_xlsx_template($filename, $title, $headers, $example, $instructions);
    }

    private function download_xlsx_template($filename, $title, array $headers, array $example, array $instructions = array())
    {
        if (!class_exists('ZipArchive')) {
            show_error('Máy chủ chưa bật ZipArchive để tạo file XLSX.', 500);
        }

        $tmp_dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'kpi_xlsx_' . uniqid();
        if (!mkdir($tmp_dir, 0777, true) && !is_dir($tmp_dir)) {
            show_error('Không thể tạo thư mục tạm để xuất file.', 500);
        }

        $files = array();
        try {
            $sheet_rows = array();
            $sheet_rows[] = $this->xlsx_row(array_merge(array($title), array_fill(1, count($headers) - 1, '')), 1, array('style' => 1));
            $sheet_rows[] = $this->xlsx_row($headers, 2, array('style' => 2));
            $sheet_rows[] = $this->xlsx_row($example, 3, array('style' => 3));

            $row_index = 4;
            if (!empty($instructions)) {
                $sheet_rows[] = $this->xlsx_row(array('Hướng dẫn sử dụng:'), $row_index++, array('style' => 4));
                foreach ($instructions as $instruction) {
                    $sheet_rows[] = $this->xlsx_row(array('• ' . $instruction), $row_index++, array('style' => 4));
                }
            }

            $sheet_xml = $this->xlsx_sheet_xml($sheet_rows, count($headers));
            $files['[Content_Types].xml'] = $this->xlsx_content_types();
            $files['_rels/.rels'] = $this->xlsx_rels();
            $files['docProps/app.xml'] = $this->xlsx_app_xml($title);
            $files['docProps/core.xml'] = $this->xlsx_core_xml($title);
            $files['xl/workbook.xml'] = $this->xlsx_workbook_xml();
            $files['xl/_rels/workbook.xml.rels'] = $this->xlsx_workbook_rels();
            $files['xl/styles.xml'] = $this->xlsx_styles_xml();
            $files['xl/worksheets/sheet1.xml'] = $sheet_xml;

            $zip_path = $tmp_dir . DIRECTORY_SEPARATOR . $filename;
            $zip = new ZipArchive();
            if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                show_error('Không thể tạo file XLSX.', 500);
            }

            foreach ($files as $path => $content) {
                $zip->addFromString($path, $content);
            }
            $zip->close();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($zip_path));
            readfile($zip_path);
        } finally {
            if (isset($zip) && $zip instanceof ZipArchive) {
                $zip->close();
            }
            foreach (glob($tmp_dir . DIRECTORY_SEPARATOR . '*') as $file) {
                @unlink($file);
            }
            @rmdir($tmp_dir);
        }
        exit;
    }

    private function xlsx_sheet_xml(array $rows, $max_col)
    {
        $dimension = 'A1:' . $this->xlsx_column_letter($max_col) . count($rows);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $xml .= '<sheetViews><sheetView tabSelected="1" workbookViewId="0"/></sheetViews>';
        $xml .= '<sheetFormatPr defaultRowHeight="20"/>';
        $xml .= '<cols>';
        for ($i = 1; $i <= $max_col; $i++) {
            $width = ($i === 1) ? 18 : (($i === 2) ? 24 : (($i === 5) ? 26 : 18));
            $xml .= '<col min="' . $i . '" max="' . $i . '" width="' . $width . '" customWidth="1"/>';
        }
        $xml .= '</cols>';
        $xml .= '<sheetData>' . implode('', $rows) . '</sheetData>';
        $xml .= '<autoFilter ref="A2:' . $this->xlsx_column_letter($max_col) . '3"/>';
        $xml .= '</worksheet>';
        return $xml;
    }

    private function xlsx_row(array $values, $row_num, array $options = array())
    {
        $style = isset($options['style']) ? (int)$options['style'] : 0;
        $cells = '';
        foreach ($values as $col => $value) {
            $cell_ref = $this->xlsx_column_letter($col + 1) . $row_num;
            $cells .= $this->xlsx_cell($cell_ref, $value, $style, is_numeric($value) && $style !== 4);
        }
        return '<row r="' . $row_num . '" spans="1:' . count($values) . '">' . $cells . '</row>';
    }

    private function xlsx_cell($ref, $value, $style = 0, $numeric = false)
    {
        $value = (string)$value;
        $escaped = htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        if ($numeric && $value !== '') {
            return '<c r="' . $ref . '" s="' . $style . '"><v>' . $escaped . '</v></c>';
        }
        return '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
    }

    private function xlsx_column_letter($number)
    {
        $letter = '';
        while ($number > 0) {
            $number--;
            $letter = chr(65 + ($number % 26)) . $letter;
            $number = (int)($number / 26);
        }
        return $letter;
    }

    private function xlsx_content_types()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';
    }

    private function xlsx_rels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function xlsx_workbook_xml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Template" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function xlsx_workbook_rels()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function xlsx_styles_xml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="5">'
            . '<font><sz val="12"/><name val="Calibri"/></font>'
            . '<font><b/><color rgb="FFFFFF"/><sz val="12"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="12"/><name val="Calibri"/></font>'
            . '<font><i/><color rgb="666666"/><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><color rgb="1F4E78"/><sz val="14"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="4">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="1F4E78"/><bgColor indexed="64"/></patternFill></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="D9EAF7"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border><left style="thin"><color rgb="B7C9D6"/></left><right style="thin"><color rgb="B7C9D6"/></right><top style="thin"><color rgb="B7C9D6"/></top><bottom style="thin"><color rgb="B7C9D6"/></bottom><diagonal/></border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="5">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyBorder="1"/>'
            . '<xf numFmtId="0" fontId="4" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>'
            . '<xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" alignment="center"/>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>'
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '</styleSheet>';
    }

    private function xlsx_app_xml($title)
    {
        $escaped = htmlspecialchars($title, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Microsoft Excel</Application>'
            . '<DocSecurity>0</DocSecurity>'
            . '<ScaleCrop>false</ScaleCrop>'
            . '<HeadingPairs><vt:vector size="2" baseType="variant"><vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant><vt:variant><vt:i4>1</vt:i4></vt:variant></vt:vector></HeadingPairs>'
            . '<TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>Template</vt:lpstr></vt:vector></TitlesOfParts>'
            . '<Company></Company><LinksUpToDate>false</LinksUpToDate><SharedDoc>false</SharedDoc><HyperlinksChanged>false</HyperlinksChanged>'
            . '<AppVersion>16.0000</AppVersion>'
            . '</Properties>';
    }

    private function xlsx_core_xml($title)
    {
        $escaped = htmlspecialchars($title, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $now = gmdate('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:title>' . $escaped . '</dc:title>'
            . '<dc:creator>KPI System</dc:creator>'
            . '<cp:lastModifiedBy>KPI System</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    // ================================================================
    // AJAX: Bulk import KPI Import từ JSON (SheetJS parse client-side)
    // ================================================================
    public function bulk_import_kpi()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $raw  = $this->input->post('rows');
        $rows = json_decode($raw, true);

        if (empty($rows) || !is_array($rows)) {
            echo json_encode(array('success' => false, 'message' => 'Không có dữ liệu hợp lệ'));
            return;
        }

        $inserted = 0;
        $errors   = array();
        $now      = date('Y-m-d H:i:s');
        $staff_id = get_staff_user_id();

        foreach ($rows as $i => $r) {
            if (empty($r['ma_phong_ban']) || empty($r['ten_phong_ban']) || empty($r['muc_tieu_kpi'])) {
                $errors[] = 'Dòng ' . ($i + 2) . ': Thiếu thông tin bắt buộc';
                continue;
            }
            $this->db->insert('tbl_kpi_import', array(
                'ma_phong_ban'       => trim($r['ma_phong_ban']),
                'ten_phong_ban'      => trim($r['ten_phong_ban']),
                'ma_vi_tri'          => trim($r['ma_vi_tri'] ?? ''),
                'chuc_vu'            => trim($r['chuc_vu'] ?? ''),
                'muc_tieu_kpi'       => trim($r['muc_tieu_kpi']),
                'ma_cong_viec'       => trim($r['ma_cong_viec'] ?? '') ?: null,
                'ten_cong_viec'      => trim($r['ten_cong_viec'] ?? '') ?: null,
                'ma_vi_pham'         => trim($r['ma_vi_pham'] ?? '') ?: null,
                'loai_vi_pham'       => trim($r['loai_vi_pham'] ?? '') ?: null,
                'diem_chuan'         => (float)($r['diem_chuan'] ?? 100),
                'diem_sau_xu_ly'     => (float)($r['diem_sau_xu_ly'] ?? 100),
                'kpi_tien_chuan'     => (float)($r['kpi_tien_chuan'] ?? 0),
                'kpi_tien_thuc_nhan' => (float)($r['kpi_tien_thuc_nhan'] ?? 0),
                'ty_le_huong_kpi'    => (float)($r['ty_le_huong_kpi'] ?? 1),
                'loai_kpi'           => trim($r['loai_kpi'] ?? 'P2'),
                'created_by'         => $staff_id,
                'created_at'         => $now,
            ));
            $inserted++;
        }

        echo json_encode(array(
            'success' => true,
            'message' => "Import thành công {$inserted} bản ghi!" . (count($errors) ? ' Lỗi: ' . implode('; ', $errors) : ''),
            'inserted' => $inserted,
            'errors'   => $errors,
        ));
    }

    // ================================================================
    // AJAX: Bulk import Đánh giá KPI từ JSON
    // ================================================================
    public function bulk_import_danh_gia()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $raw  = $this->input->post('rows');
        $rows = json_decode($raw, true);

        if (empty($rows) || !is_array($rows)) {
            echo json_encode(array('success' => false, 'message' => 'Không có dữ liệu hợp lệ'));
            return;
        }

        $inserted = 0;
        $errors   = array();
        $now      = date('Y-m-d H:i:s');
        $staff_id = get_staff_user_id();

        foreach ($rows as $i => $r) {
            $nhan_su_id = (int)($r['nhan_su_id'] ?? 0);
            if ($nhan_su_id <= 0 || empty($r['loai_danh_gia']) || empty($r['ky_danh_gia'])) {
                $errors[] = 'Dòng ' . ($i + 2) . ': Thiếu nhan_su_id / loai_danh_gia / ky_danh_gia';
                continue;
            }

            $ho_so    = !empty($r['ho_so_day_du'])       ? 1 : 0;
            $training = !empty($r['training_completed'])  ? 1 : 0;
            $sop      = !empty($r['sop_compliance'])      ? 1 : 0;
            $gate1    = ($ho_so && $training && $sop) ? 'PASS' : 'FAIL';

            $p2_raw   = (float)($r['p2_raw'] ?? 0);
            $comp_raw = (float)($r['compliance_raw'] ?? 0);
            $p3_raw   = (float)($r['p3_raw'] ?? 0);

            $p2_f   = min(60, max(0, $p2_raw   * 0.6));
            $comp_f = min(20, max(0, $comp_raw  * 0.2));
            $p3_f   = min(20, max(0, $p3_raw    * 0.2));
            $tot    = $p2_f + $comp_f + $p3_f;

            $this->db->insert('tbl_kpi_danh_gia', array(
                'nhan_su_id'         => $nhan_su_id,
                'loai_danh_gia'      => trim($r['loai_danh_gia']),
                'ky_danh_gia'        => trim($r['ky_danh_gia']),
                'ho_so_day_du'       => $ho_so,
                'training_completed' => $training,
                'sop_compliance'     => $sop,
                'gate_1_result'      => $gate1,
                'p2_raw'             => $p2_raw,
                'p2_final'           => $p2_f,
                'compliance_raw'     => $comp_raw,
                'compliance_final'   => $comp_f,
                'p3_raw'             => $p3_raw,
                'p3_final'           => $p3_f,
                'tong_diem'          => $tot,
                'xep_loai'           => $this->_calc_xep_loai($tot),
                'quyet_dinh'         => $this->_calc_quyet_dinh($gate1, $tot),
                'risk_level'         => $this->_calc_risk_level($tot),
                'ghi_chu'            => trim($r['ghi_chu'] ?? ''),
                'created_by'         => $staff_id,
                'created_at'         => $now,
            ));
            $inserted++;
        }

        echo json_encode(array(
            'success'  => true,
            'message'  => "Import thành công {$inserted} bản ghi!" . (count($errors) ? ' Lỗi: ' . implode('; ', $errors) : ''),
            'inserted' => $inserted,
            'errors'   => $errors,
        ));
    }


    // ================================================================
    // Dữ liệu giả lập (Fake Data) cho Dashboard KPI
    // ================================================================
    public function insert_data_ao()
    {
        // 1. Xoá dữ liệu cũ
        $this->db->empty_table('tbl_kpi_danh_gia');
        $this->db->empty_table('tbl_kpi_import');
        
        // 2. Fake Data cho Cột Nhập KPI
        $importData = [];
        $phong_ban = ['Kinh doanh', 'Kỹ thuật', 'Kế toán', 'Hành chính', 'Marketing'];
        for($i=1; $i<=20; $i++) {
            $pb = $phong_ban[array_rand($phong_ban)];
            $importData[] = [
                'ma_phong_ban' => 'PB' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'ten_phong_ban' => $pb,
                'muc_tieu_kpi' => 'KPI Trọng yếu số '.$i,
                'diem_chuan' => 100,
                'diem_sau_xu_ly' => mt_rand(50, 100),
                'kpi_tien_chuan' => 5000000,
                'kpi_tien_thuc_nhan' => mt_rand(2000, 5000) * 1000,
                'ty_le_huong_kpi' => mt_rand(50, 100) / 100,
                'loai_kpi' => 'P2',
                'created_by' => get_staff_user_id() ?: 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-'.mt_rand(1, 30).' days'))
            ];
        }
        $this->db->insert_batch('tbl_kpi_import', $importData);

        // 3. Fake Data cho Cột Đánh Giá KPI
        $this->db->select('staffid');
        $this->db->where('active', 1);
        $staffs = $this->db->get('tblstaff')->result_array();
        
        if(!empty($staffs)) {
            $danhGiaData = [];
            $ky_danh_gia_list = ['2023-M11', '2023-M12', '2024-M01', '2024-M02', '2024-Q1'];
            $loai_danh_gia_list = ['KPI tháng', 'KPI quý', 'Dự án', 'Định kỳ'];
            
            for($j=0; $j<80; $j++) {
                $staff = $staffs[array_rand($staffs)];
                $ho_so = mt_rand(0,10) > 2 ? 1 : 0; // 80% đầy đủ
                $training = mt_rand(0,10) > 2 ? 1 : 0;
                $sop = mt_rand(0,10) > 3 ? 1 : 0; 
                $gate1 = ($ho_so && $training && $sop) ? 'PASS' : 'FAIL';
                
                $p2_raw = mt_rand(40, 100);
                $comp_raw = mt_rand(50, 100);
                $p3_raw = mt_rand(40, 100);
                
                $p2_final = min(60, max(0, $p2_raw * 0.6));
                $comp_final = min(20, max(0, $comp_raw * 0.2));
                $p3_final = min(20, max(0, $p3_raw * 0.2));
                $tong_diem = $p2_final + $comp_final + $p3_final;
                
                $danhGiaData[] = [
                    'nhan_su_id' => $staff['staffid'],
                    'loai_danh_gia' => $loai_danh_gia_list[array_rand($loai_danh_gia_list)],
                    'ky_danh_gia' => $ky_danh_gia_list[array_rand($ky_danh_gia_list)],
                    'ho_so_day_du' => $ho_so,
                    'training_completed' => $training,
                    'sop_compliance' => $sop,
                    'gate_1_result' => $gate1,
                    'p2_raw' => $p2_raw,
                    'p2_final' => $p2_final,
                    'compliance_raw' => $comp_raw,
                    'compliance_final' => $comp_final,
                    'p3_raw' => $p3_raw,
                    'p3_final' => $p3_final,
                    'tong_diem' => $tong_diem,
                    'xep_loai' => $this->_calc_xep_loai($tong_diem),
                    'quyet_dinh' => $this->_calc_quyet_dinh($gate1, $tong_diem),
                    'risk_level' => $this->_calc_risk_level($tong_diem),
                    'created_by' => get_staff_user_id() ?: 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-'.mt_rand(1, 40).' days'))
                ];
            }
            $this->db->insert_batch('tbl_kpi_danh_gia', $danhGiaData);
        }
        
        echo "<h3 style='color:green;'>✅ Tạo thành công dữ liệu ảo cho KPI Dashboard (80 bản ghi đánh giá + 20 bản ghi Import)!</h3>";
        echo "<p>Dữ liệu cũ đã được xóa bỏ để chống rác. <a href='".admin_url('KpiDanhGia')."'>Quay lại Dashboard KPI</a></p>";
    }

    // ================================================================
    public function get_nhan_su_list()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $this->db->select("staffid as id, CONCAT(firstname,' ',lastname) as ho_ten, email, active, status_work");
        $this->db->where('active', 1);
        $this->db->order_by('firstname', 'ASC');
        $list = $this->db->get('tblstaff')->result_array();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $list]);
    }

    // ================================================================
    // AJAX: KPI Import CRUD
    // ================================================================
    public function save_kpi_import()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id  = (int)$this->input->post('id');
        $row = [
            'ma_phong_ban'       => $this->input->post('ma_phong_ban', true),
            'ten_phong_ban'      => $this->input->post('ten_phong_ban', true),
            'ma_vi_tri'          => $this->input->post('ma_vi_tri', true),
            'chuc_vu'            => $this->input->post('chuc_vu', true),
            'muc_tieu_kpi'       => $this->input->post('muc_tieu_kpi', true),
            'ma_cong_viec'       => $this->input->post('ma_cong_viec', true) ?: null,
            'ten_cong_viec'      => $this->input->post('ten_cong_viec', true) ?: null,
            'ma_vi_pham'         => $this->input->post('ma_vi_pham', true) ?: null,
            'loai_vi_pham'       => $this->input->post('loai_vi_pham', true) ?: null,
            'diem_chuan'         => (float)$this->input->post('diem_chuan'),
            'diem_sau_xu_ly'     => (float)$this->input->post('diem_sau_xu_ly'),
            'kpi_tien_chuan'     => (float)$this->input->post('kpi_tien_chuan'),
            'kpi_tien_thuc_nhan' => (float)$this->input->post('kpi_tien_thuc_nhan'),
            'ty_le_huong_kpi'    => (float)$this->input->post('ty_le_huong_kpi'),
            'loai_kpi'           => $this->input->post('loai_kpi', true) ?: 'P2',
            'created_by'         => get_staff_user_id(),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        if (empty($row['ma_phong_ban']) || empty($row['ten_phong_ban']) || empty($row['muc_tieu_kpi'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']); return;
        }

        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_import', $row);
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!', 'id' => $id]);
        } else {
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_kpi_import', $row);
            echo json_encode(['success' => true, 'message' => 'Thêm thành công!', 'id' => $this->db->insert_id()]);
        }
    }

    public function delete_kpi_import()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']); return; }
        $this->db->where('id', $id)->delete('tbl_kpi_import');
        echo json_encode(['success' => true, 'message' => 'Đã xoá!']);
    }

    // ================================================================
    // AJAX: KPI Đánh giá CRUD + tính điểm
    // ================================================================
    public function save_danh_gia()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id            = (int)$this->input->post('id');
        $ho_so         = (bool)$this->input->post('ho_so_day_du');
        $training      = (bool)$this->input->post('training_completed');
        $sop           = (bool)$this->input->post('sop_compliance');
        $p2_raw        = (float)$this->input->post('p2_raw');
        $comp_raw      = (float)$this->input->post('compliance_raw');
        $p3_raw        = (float)$this->input->post('p3_raw');

        // ---- Tính điểm ----
        $gate1         = ($ho_so && $training && $sop) ? 'PASS' : 'FAIL';
        $p2_final      = min(60, max(0, $p2_raw  * 0.6));
        $comp_final    = min(20, max(0, $comp_raw * 0.2));
        $p3_final      = min(20, max(0, $p3_raw  * 0.2));
        $tong_diem     = $p2_final + $comp_final + $p3_final;
        $xep_loai      = $this->_calc_xep_loai($tong_diem);
        $quyet_dinh    = $this->_calc_quyet_dinh($gate1, $tong_diem);
        $risk_level    = $this->_calc_risk_level($tong_diem);

        $row = [
            'nhan_su_id'         => (int)$this->input->post('nhan_su_id'),
            'loai_danh_gia'      => $this->input->post('loai_danh_gia', true),
            'ky_danh_gia'        => $this->input->post('ky_danh_gia', true),
            'ho_so_day_du'       => $ho_so   ? 1 : 0,
            'training_completed' => $training ? 1 : 0,
            'sop_compliance'     => $sop      ? 1 : 0,
            'gate_1_result'      => $gate1,
            'p2_raw'             => $p2_raw,
            'p2_final'           => $p2_final,
            'compliance_raw'     => $comp_raw,
            'compliance_final'   => $comp_final,
            'p3_raw'             => $p3_raw,
            'p3_final'           => $p3_final,
            'tong_diem'          => $tong_diem,
            'xep_loai'           => $xep_loai,
            'quyet_dinh'         => $quyet_dinh,
            'risk_level'         => $risk_level,
            'ghi_chu'            => $this->input->post('ghi_chu', true),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        if (empty($row['nhan_su_id']) || empty($row['loai_danh_gia']) || empty($row['ky_danh_gia'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']); return;
        }

        $row['created_by'] = get_staff_user_id();

        if ($id > 0) {
            $this->db->where('id', $id)->update('tbl_kpi_danh_gia', $row);
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!', 'id' => $id, 'tong_diem' => $tong_diem, 'xep_loai' => $xep_loai, 'quyet_dinh' => $quyet_dinh]);
        } else {
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('tbl_kpi_danh_gia', $row);
            echo json_encode(['success' => true, 'message' => 'Tạo đánh giá thành công!', 'id' => $this->db->insert_id(), 'tong_diem' => $tong_diem, 'xep_loai' => $xep_loai, 'quyet_dinh' => $quyet_dinh]);
        }
    }

    public function delete_danh_gia()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int)$this->input->post('id');
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']); return; }
        $this->db->where('id', $id)->delete('tbl_kpi_danh_gia');
        echo json_encode(['success' => true, 'message' => 'Đã xoá!']);
    }

    // ================================================================
    // AJAX: Tổng hợp data
    // ================================================================
    public function get_tong_hop_data()
    {
        $loai = $this->input->get('loai') ?: 'all';

        $this->db->select("dg.*, CONCAT(ns.firstname,' ',ns.lastname) as ho_ten, ns.staffid as ma_nhan_vien");
        $this->db->from('tbl_kpi_danh_gia dg');
        $this->db->join('tblstaff ns', 'ns.staffid = dg.nhan_su_id', 'left');
        if ($loai !== 'all') $this->db->where('dg.loai_danh_gia', $loai);
        $list = $this->db->get()->result_array();

        $total    = count($list);
        $pass     = 0;
        $fail     = 0;
        $giam_sat = 0;
        $tong_diem_sum = 0;
        foreach ($list as $r) {
            if ($r['quyet_dinh'] === 'ĐẠT')      $pass++;
            elseif ($r['quyet_dinh'] === 'FAIL')  $fail++;
            elseif ($r['quyet_dinh'] === 'GIÁM SÁT') $giam_sat++;
            $tong_diem_sum += (float)$r['tong_diem'];
        }
        $avg = $total > 0 ? round($tong_diem_sum / $total, 2) : 0;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => ['total' => $total, 'pass' => $pass, 'fail' => $fail, 'giam_sat' => $giam_sat, 'avg_score' => $avg, 'list' => $list],
        ]);
    }

    // ================================================================
    // Private: Hàm tính điểm (dùng nội bộ)
    // ================================================================
    private function _calc_xep_loai($diem)
    {
        if ($diem >= 90) return 'Xuất sắc';
        if ($diem >= 80) return 'Tốt';
        if ($diem >= 70) return 'Đạt';
        if ($diem >= 60) return 'Cần giám sát';
        return 'Không đạt';
    }

    private function _calc_quyet_dinh($gate1, $diem)
    {
        if ($gate1 === 'FAIL') return 'FAIL';
        if ($diem >= 70)       return 'ĐẠT';
        if ($diem >= 60)       return 'GIÁM SÁT';
        return 'FAIL';
    }

    private function _calc_risk_level($diem)
    {
        if ($diem >= 80) return 'Low';
        if ($diem >= 60) return 'Medium';
        return 'High';
    }
}
