<?php defined('BASEPATH') or exit('No direct script access allowed');

class Export_data extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Export_data_model');
    }

    /**
     * Trang view xuất Excel quy trình sản phẩm
     */
    public function export_product_stages_excel_view()
    {
        $data = [];
        $data['title'] = 'Xuất Excel quy trình sản phẩm';

        $this->load->view('admin/export_data/export_product_stages_excel', $data);
    }

    /**
     * Khởi tạo file export (tạo file .xls với header)
     */
    public function init_export_product_stages_excel()
    {
        $export_id = 'product_stages_' . date('YmdHis') . '_' . rand(1000, 9999);

        $folder = FCPATH . 'uploads/exports/';

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_path = $folder . $export_id . '.xls';

        $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <!--[if gte mso 9]><xml>
                 <x:ExcelWorkbook>
                  <x:ExcelWorksheets>
                   <x:ExcelWorksheet>
                    <x:Name>Sheet1</x:Name>
                    <x:WorksheetOptions>
                     <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                   </x:ExcelWorksheet>
                  </x:ExcelWorksheets>
                 </x:ExcelWorkbook>
                </xml><![endif]-->
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-family: "Times New Roman", Times, serif;
                        font-size: 12pt;
                    }

                    th, td {
                        border: 1px solid #000;
                        padding: 5px;
                        vertical-align: top;
                        font-family: "Times New Roman", Times, serif;
                        mso-number-format:"\@";
                    }

                    th {
                        font-weight: bold;
                        background: #f2f2f2;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Mã danh mục</th>
                            <th>Loài</th>
                            <th>Mã công đoạn</th>
                            <th>Tên công đoạn</th>
                            <th>Mã máy</th>
                            <th>Thứ tự công đoạn</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

        file_put_contents($file_path, "\xEF\xBB\xBF" . $html);

        $filters = $this->getProductStagesExportFilters();

        $total = $this->Export_data_model->countProductsForExport($filters);

        echo json_encode([
            'success' => true,
            'export_id' => $export_id,
            'total' => $total,
        ]);
    }

    /**
     * Xuất từng chunk dữ liệu vào file .xls
     */
    public function export_product_stages_excel_chunk()
    {
        $export_id = trim((string) $this->input->post('export_id'));
        $offset = (int) $this->input->post('offset');
        $limit = (int) $this->input->post('limit');

        if ($limit <= 0) {
            $limit = 100;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        if (empty($export_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu export_id',
            ]);
            return;
        }

        $folder = FCPATH . 'uploads/exports/';
        $file_path = $folder . $export_id . '.xls';

        if (!file_exists($file_path)) {
            echo json_encode([
                'success' => false,
                'message' => 'File export không tồn tại',
            ]);
            return;
        }

        $filters = $this->getProductStagesExportFilters();

        $total = $this->Export_data_model->countProductsForExport($filters);

        $products = $this->Export_data_model->getProductExportChunk($limit, $offset, $filters);

        $html = '';

        foreach ($products as $index => $product) {
            $stt = $offset + $index + 1;

            $product_code = $product['code'] ?? '';
            $product_name = $product['name'] ?? '';
            $category_name = $product['category_name'] ?? '';
            $category_code = $product['category_code'] ?? '';
            $species_name = $product['species_name'] ?? '';

            $has_row = false;

            if (!empty($product['stages'])) {
                foreach ($product['stages'] as $stage) {
                    if (!empty($stage['versions_list'])) {
                        foreach ($stage['versions_list'] as $version) {
                            $html .= '<tr>';
                            $html .= '<td>' . $this->xlsCell($stt) . '</td>';
                            $html .= '<td>' . $this->xlsCell($product_code) . '</td>';
                            $html .= '<td>' . $this->xlsCell($product_name) . '</td>';
                            $html .= '<td>' . $this->xlsCell($category_name) . '</td>';
                            $html .= '<td>' . $this->xlsCell($category_code) . '</td>';
                            $html .= '<td>' . $this->xlsCell($species_name) . '</td>';
                            $html .= '<td>' . $this->xlsCell($version['stage_code'] ?? '') . '</td>';
                            $html .= '<td>' . $this->xlsCell($version['stage_name'] ?? '') . '</td>';
                            $html .= '<td>' . $this->xlsCell($version['machine_code'] ?? '') . '</td>';
                            $html .= '<td>' . $this->xlsCell($version['number'] ?? '') . '</td>';
                            $html .= '</tr>';

                            $has_row = true;
                        }
                    }
                }
            }

            if (!$has_row) {
                $html .= '<tr>';
                $html .= '<td>' . $this->xlsCell($stt) . '</td>';
                $html .= '<td>' . $this->xlsCell($product_code) . '</td>';
                $html .= '<td>' . $this->xlsCell($product_name) . '</td>';
                $html .= '<td>' . $this->xlsCell($category_name) . '</td>';
                $html .= '<td>' . $this->xlsCell($category_code) . '</td>';
                $html .= '<td>' . $this->xlsCell($species_name) . '</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        }

        file_put_contents($file_path, $html, FILE_APPEND);

        $next_offset = $offset + $limit;
        $loaded = min($next_offset, $total);
        $done = $next_offset >= $total;

        if ($done) {
            file_put_contents($file_path, '
                    </tbody>
                </table>
            </body>
            </html>
            ', FILE_APPEND);
        }

        echo json_encode([
            'success' => true,
            'offset' => $offset,
            'next_offset' => $next_offset,
            'loaded' => $loaded,
            'total' => $total,
            'done' => $done,
        ]);
    }

    /**
     * Tải file Excel đã xuất
     */
    public function download_product_stages_excel()
    {
        $export_id = trim((string) $this->input->get('export_id'));

        if (empty($export_id)) {
            show_error('Thiếu export_id');
            return;
        }

        $file_path = FCPATH . 'uploads/exports/' . $export_id . '.xls';

        if (!file_exists($file_path)) {
            show_error('File không tồn tại');
            return;
        }

        $file_name = 'quy_trinh_san_pham_' . date('YmdHis') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit;
    }

    // ========================================================================
    // XUẤT CÔNG ĐOẠN SẢN PHẨM (export_product_stage)
    // ========================================================================

    /**
     * Trang view xuất Excel công đoạn sản phẩm
     */
    public function export_product_stage_view()
    {
        $data = [];
        $data['title'] = 'Xuất Excel công đoạn sản phẩm';

        $this->load->view('admin/export_data/export_product_stage', $data);
    }

    /**
     * Khởi tạo file export công đoạn sản phẩm
     */
    public function init_export_product_stage()
    {
        $export_id = 'product_stage_' . date('YmdHis') . '_' . rand(1000, 9999);

        $folder = FCPATH . 'uploads/exports/';

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_path = $folder . $export_id . '.xls';

        $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <!--[if gte mso 9]><xml>
                 <x:ExcelWorkbook>
                  <x:ExcelWorksheets>
                   <x:ExcelWorksheet>
                    <x:Name>Sheet1</x:Name>
                    <x:WorksheetOptions>
                     <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                   </x:ExcelWorksheet>
                  </x:ExcelWorksheets>
                 </x:ExcelWorkbook>
                </xml><![endif]-->
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-family: "Times New Roman", Times, serif;
                        font-size: 12pt;
                    }

                    th, td {
                        border: 1px solid #000;
                        padding: 5px;
                        vertical-align: top;
                        font-family: "Times New Roman", Times, serif;
                        mso-number-format:"\@";
                    }

                    th {
                        font-weight: bold;
                        background: #f2f2f2;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <table>
                    <thead>
                        <tr>
                            <th>Mã thành phẩm</th>
                            <th>Tên thành phẩm</th>
                            <th>Phiên bản</th>
                            <th>Mã giai đoạn</th>
                            <th>Đánh dấu công đoạn</th>
                            <th>Giai đoạn cuối</th>
                            <th>Máy móc</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

        file_put_contents($file_path, "\xEF\xBB\xBF" . $html);

        $filters = $this->getProductStageExportFilters();

        $total = $this->Export_data_model->countProductsForExport($filters);

        echo json_encode([
            'success' => true,
            'export_id' => $export_id,
            'total' => $total,
        ]);
    }

    /**
     * Xuất từng chunk dữ liệu công đoạn sản phẩm
     */
    public function export_product_stage_chunk()
    {
        $export_id = trim((string) $this->input->post('export_id'));
        $offset = (int) $this->input->post('offset');
        $limit = (int) $this->input->post('limit');

        if ($limit <= 0) {
            $limit = 100;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        if (empty($export_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu export_id',
            ]);
            return;
        }

        $folder = FCPATH . 'uploads/exports/';
        $file_path = $folder . $export_id . '.xls';

        if (!file_exists($file_path)) {
            echo json_encode([
                'success' => false,
                'message' => 'File export không tồn tại',
            ]);
            return;
        }

        $filters = $this->getProductStageExportFilters();

        $total = $this->Export_data_model->countProductsForExport($filters);

        $products = $this->Export_data_model->getProductExportChunk($limit, $offset, $filters);

        $html = '';

        foreach ($products as $index => $product) {
            $product_code = $product['code'] ?? '';
            $product_name = $product['name'] ?? '';

            $has_row = false;

            if (!empty($product['stages'])) {
                foreach ($product['stages'] as $stage) {
                    $stage_versions_label = $stage['versions'] ?? '';

                    if (!empty($stage['versions_list'])) {
                        foreach ($stage['versions_list'] as $version) {
                            $html .= '<tr>';
                            $html .= '<td>' . $this->xlsCell($product_code) . '</td>';
                            $html .= '<td>' . $this->xlsCell($product_name) . '</td>';
                            $html .= '<td>' . $this->xlsCell($stage_versions_label) . '</td>';
                            $html .= '<td>' . $this->xlsCell($version['stage_code'] ?? '') . '</td>';
                            $html .= '<td>' . $this->xlsCell((!empty($version['type']) && $version['type'] == 2) ? '2' : '') . '</td>';
                            $html .= '<td>' . $this->xlsCell((!empty($version['final_stage']) && $version['final_stage'] == 1) ? '1' : '') . '</td>';
                            $html .= '<td>' . $this->xlsCell($version['machine_code'] ?? '') . '</td>';
                            $html .= '</tr>';

                            $has_row = true;
                        }
                    }
                }
            }

            if (!$has_row) {
                $html .= '<tr>';
                $html .= '<td>' . $this->xlsCell($product_code) . '</td>';
                $html .= '<td>' . $this->xlsCell($product_name) . '</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        }

        file_put_contents($file_path, $html, FILE_APPEND);

        $next_offset = $offset + $limit;
        $loaded = min($next_offset, $total);
        $done = $next_offset >= $total;

        if ($done) {
            file_put_contents($file_path, '
                    </tbody>
                </table>
            </body>
            </html>
            ', FILE_APPEND);
        }

        echo json_encode([
            'success' => true,
            'offset' => $offset,
            'next_offset' => $next_offset,
            'loaded' => $loaded,
            'total' => $total,
            'done' => $done,
        ]);
    }

    /**
     * Tải file Excel công đoạn sản phẩm
     */
    public function download_product_stage_excel()
    {
        $export_id = trim((string) $this->input->get('export_id'));

        if (empty($export_id)) {
            show_error('Thiếu export_id');
            return;
        }

        $file_path = FCPATH . 'uploads/exports/' . $export_id . '.xls';

        if (!file_exists($file_path)) {
            show_error('File không tồn tại');
            return;
        }

        $file_name = 'cong_doan_san_pham_' . date('YmdHis') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit;
    }

    // ========================================================================
    // XUẤT CÔNG ĐOẠN SẢN PHẨM BOM (export_product_stage_bom)
    // ========================================================================

    /**
     * Trang view xuất Excel công đoạn BOM
     */
    public function export_product_stage_bom_view()
    {
        $data = [];
        $data['title'] = 'Xuất Excel công đoạn BOM sản phẩm';

        $this->load->view('admin/export_data/export_product_stage_bom', $data);
    }

    /**
     * Khởi tạo file export công đoạn BOM
     */
    public function init_export_product_stage_bom()
    {
        $export_id = 'product_stage_bom_' . date('YmdHis') . '_' . rand(1000, 9999);

        $folder = FCPATH . 'uploads/exports/';

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_path = $folder . $export_id . '.xls';

        $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <!--[if gte mso 9]><xml>
                 <x:ExcelWorkbook>
                  <x:ExcelWorksheets>
                   <x:ExcelWorksheet>
                    <x:Name>Sheet1</x:Name>
                    <x:WorksheetOptions>
                     <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                   </x:ExcelWorksheet>
                  </x:ExcelWorksheets>
                 </x:ExcelWorkbook>
                </xml><![endif]-->
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                        font-family: "Times New Roman", Times, serif;
                        font-size: 12pt;
                    }

                    th, td {
                        border: 1px solid #000;
                        padding: 5px;
                        vertical-align: top;
                        font-family: "Times New Roman", Times, serif;
                        mso-number-format:"\@";
                    }

                    th {
                        font-weight: bold;
                        background: #f2f2f2;
                        text-align: center;
                    }

                    .bg-blue { background-color: #5B9BD5; }
                    .bg-light-blue { background-color: #B4C6E7; }
                    .bg-yellow { background-color: #FFFF00; }
                </style>
            </head>
            <body>
                <table>
                    <thead>
                        <tr>
                            <th colspan="2" class="bg-blue">THÀNH PHẨM</th>
                            <th colspan="6" class="bg-light-blue">CÔNG ĐOẠN</th>
                            <th colspan="11" class="bg-yellow">ĐỊNH MỨC BOM</th>
                        </tr>
                        <tr>
                            <th>Mã thành phẩm</th>
                            <th>Tên thành phẩm</th>
                            <th>Phiên bản</th>
                            <th>Mã Công Đoạn</th>
                            <th>Tên Công đoạn</th>
                            <th>Đánh dấu công đoạn</th>
                            <th>Giai đoạn cuối</th>
                            <th>Máy móc</th>
                            <th>Phiên bản BOM</th>
                            <th>Thành phần</th>
                            <th>Mã nguyên phụ liệu</th>
                            <th>Tên nguyên phụ liệu</th>
                            <th>Loại</th>
                            <th>Đơn vị</th>
                            <th>Khổ in ngang - dọc (tờ) - cm</th>
                            <th>SL con/ Khổ in</th>
                            <th>Giá trị quy đổi (tính trên tờ in)</th>
                            <th>Số tờ quy đổi</th>
                            <th>Số lượng bù hao (khổ liệu)</th>
                        </tr>
                    </thead>
                    <tbody>
        ';

        file_put_contents($file_path, "\xEF\xBB\xBF" . $html);

        $filters = $this->getProductStageExportFilters();

        $total = $this->Export_data_model->countProductsForExport($filters);

        echo json_encode([
            'success' => true,
            'export_id' => $export_id,
            'total' => $total,
        ]);
    }

    /**
     * Xuất từng chunk dữ liệu công đoạn BOM
     */
    public function export_product_stage_bom_chunk()
    {
        $export_id = trim((string) $this->input->post('export_id'));
        $offset = (int) $this->input->post('offset');
        $limit = (int) $this->input->post('limit');

        if ($limit <= 0) {
            $limit = 100;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        if (empty($export_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu export_id',
            ]);
            return;
        }

        $folder = FCPATH . 'uploads/exports/';
        $file_path = $folder . $export_id . '.xls';

        if (!file_exists($file_path)) {
            echo json_encode([
                'success' => false,
                'message' => 'File export không tồn tại',
            ]);
            return;
        }

        $filters = $this->getProductStageExportFilters();

        $total = $this->Export_data_model->countProductsForExport($filters);

        $products = $this->Export_data_model->getProductExportChunk($limit, $offset, $filters);

        $html = '';

        foreach ($products as $index => $product) {
            $product_code = $product['code'] ?? '';
            $product_name = $product['name'] ?? '';

            $stages = $product['stages'] ?? [];
            $norms = $product['norms'] ?? [];

            $stages_data = [];
            foreach ($stages as $stage) {
                $versions_list = $stage['versions_list'] ?? [];
                if (!empty($versions_list)) {
                    foreach ($versions_list as $version) {
                        $stages_data[] = [
                            'versions' => $stage['versions'] ?? '',
                            'stage_code' => $version['stage_code'] ?? '',
                            'stage_name' => $version['stage_name'] ?? '',
                            'type' => (!empty($version['type']) && $version['type'] == 2) ? '2' : '',
                            'final_stage' => (!empty($version['final_stage']) && $version['final_stage'] == 1) ? '1' : '',
                            'machine_code' => $version['machine_code'] ?? '',
                        ];
                    }
                }
            }

            $maxRows = max(count($stages_data), count($norms));

            if ($maxRows == 0) {
                // If there are neither stages nor norms, write an empty row
                $html .= '<tr>';
                $html .= '<td>' . $this->xlsCell($product_code) . '</td>';
                $html .= '<td>' . $this->xlsCell($product_name) . '</td>';
                $html .= '<td></td><td></td><td></td><td></td><td></td><td></td>';
                $html .= '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
                $html .= '</tr>';
                continue;
            }

            for ($i = 0; $i < $maxRows; $i++) {
                $stage_row = $stages_data[$i] ?? null;
                $norm_row = $norms[$i] ?? null;

                $code_name_parts = [];
                if (!empty($norm_row['code_name'])) {
                    $code_name_parts = explode('|||', $norm_row['code_name']);
                }
                $norm_code = $code_name_parts[0] ?? '';
                $norm_name = $code_name_parts[1] ?? '';

                $norm_type = !empty($norm_row['type']) ? _l($norm_row['type']) : '';

                $html .= '<tr>';
                $html .= '<td>' . $this->xlsCell($product_code) . '</td>';
                $html .= '<td>' . $this->xlsCell($product_name) . '</td>';
                
                // Stage Data
                $html .= '<td>' . $this->xlsCell($stage_row ? $stage_row['versions'] : ($norms[0]['versions'] ?? '')) . '</td>'; // The old logic uses val['versions'] for stage. If stage is empty, let's keep it empty, but the old code kept printing versions.
                $html .= '<td>' . $this->xlsCell($stage_row['stage_code'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($stage_row['stage_name'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($stage_row['type'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($stage_row['final_stage'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($stage_row['machine_code'] ?? '') . '</td>';

                // Norm Data
                $html .= '<td>' . $this->xlsCell($norm_row['versions'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['element_name'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_code) . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_name) . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_type) . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['unit_name'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['landscape_print_size'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['number_children_size'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['quantity'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['paper_exchange'] ?? '') . '</td>';
                $html .= '<td>' . $this->xlsCell($norm_row['quantity_compensation'] ?? '') . '</td>';

                $html .= '</tr>';
            }
        }

        file_put_contents($file_path, $html, FILE_APPEND);

        $next_offset = $offset + $limit;
        $loaded = min($next_offset, $total);
        $done = $next_offset >= $total;

        if ($done) {
            file_put_contents($file_path, '
                    </tbody>
                </table>
            </body>
            </html>
            ', FILE_APPEND);
        }

        echo json_encode([
            'success' => true,
            'offset' => $offset,
            'next_offset' => $next_offset,
            'loaded' => $loaded,
            'total' => $total,
            'done' => $done,
        ]);
    }

    /**
     * Tải file Excel công đoạn BOM
     */
    public function download_product_stage_bom_excel()
    {
        $export_id = trim((string) $this->input->get('export_id'));

        if (empty($export_id)) {
            show_error('Thiếu export_id');
            return;
        }

        $file_path = FCPATH . 'uploads/exports/' . $export_id . '.xls';

        if (!file_exists($file_path)) {
            show_error('File không tồn tại');
            return;
        }

        $file_name = 'cong_doan_bom_san_pham_' . date('YmdHis') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit;
    }

    // ========================================================================
    // PRIVATE HELPERS
    // ========================================================================

    /**
     * Lấy bộ lọc từ POST (quy trình sản phẩm)
     */
    private function getProductStagesExportFilters()
    {
        $category_id = $this->input->post('category_id');
        if (is_array($category_id)) {
            $category_id = array_map('trim', $category_id);
            $category_id = array_filter($category_id);
        } else {
            $category_id = trim((string) $category_id);
        }

        return [
            'keyword' => trim((string) $this->input->post('keyword')),
            'category_id' => $category_id,
        ];
    }

    /**
     * Lấy bộ lọc từ POST (công đoạn sản phẩm)
     */
    private function getProductStageExportFilters()
    {
        $category_id = $this->input->post('category_id');
        if (is_array($category_id)) {
            $category_id = array_map('trim', $category_id);
            $category_id = array_filter($category_id);
        } else {
            $category_id = trim((string) $category_id);
        }

        return [
            'keyword' => trim((string) $this->input->post('keyword')),
            'category_id' => $category_id,
        ];
    }

    /**
     * Format giá trị cell cho file .xls (chống Excel tự chuyển format)
     */
    private function xlsCell($value)
    {
        $value = (string) $value;
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        return '<span style="mso-number-format:\'\@\';">' . $value . '</span>';
    }
}
