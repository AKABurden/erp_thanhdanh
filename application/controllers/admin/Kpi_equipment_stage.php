<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_equipment_stage extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('kpi_equipment_stage_model');
    }

    /**
     * List page - Trang danh sách KPI thiết bị công đoạn
     */
    public function index()
    {
        if (!has_permission('kpi_equipment_stage', '', 'view')) {
            access_denied('kpi_equipment_stage');
        }
        $data['title'] = 'KPI Thiết Bị Công Đoạn';
        $this->load->view('admin/kpi_equipment_stage/kpi_equipment_stage', $data);
    }

    /**
     * Server-side DataTable AJAX
     * Lấy dữ liệu DataTable server-side
     */
    public function get_list()
    {
        if (!has_permission('kpi_equipment_stage', '', 'view')) {
            echo json_encode(['aaData' => []]);
            return;
        }
        $perEdit   = has_permission('kpi_equipment_stage', '', 'edit');
        $perDelete = has_permission('kpi_equipment_stage', '', 'delete');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_kpi_equipment_stage.id as id',
            'tbl_kpi_equipment_stage.group_stage as group_stage',
            'tbl_kpi_equipment_stage.stage_code as stage_code',
            'tbl_kpi_equipment_stage.stage_name as stage_name',
            'tbl_kpi_equipment_stage.equipment_code as equipment_code',
            'tbl_kpi_equipment_stage.equipment_name as equipment_name',
            'tbl_kpi_equipment_stage.equipment_status as equipment_status',
            'tbl_kpi_equipment_stage.downtime_minutes as downtime_minutes',
            'tbl_kpi_equipment_stage.downtime_reason as downtime_reason',
            'tbl_kpi_equipment_stage.repair_count as repair_count',
            'tbl_kpi_equipment_stage.repair_minutes as repair_minutes',
            'tbl_kpi_equipment_stage.periodic_maintenance as periodic_maintenance',
            'tbl_kpi_equipment_stage.last_maintenance_date as last_maintenance_date',
            'tbl_kpi_equipment_stage.calibration as calibration',
            'tbl_kpi_equipment_stage.last_calibration_date as last_calibration_date',
            'tbl_kpi_equipment_stage.npl_warning_pct as npl_warning_pct',
            'tbl_kpi_equipment_stage.defect_count as defect_count',
            'tbl_kpi_equipment_stage.defect_rate_pct as defect_rate_pct',
            'tbl_kpi_equipment_stage.planned_output as planned_output',
            'tbl_kpi_equipment_stage.actual_output as actual_output',
            'tbl_kpi_equipment_stage.target_achievement_pct as target_achievement_pct',
            'tbl_kpi_equipment_stage.equipment_budget as equipment_budget',
            'tbl_kpi_equipment_stage.repair_cost as repair_cost',
            'tbl_kpi_equipment_stage.maintenance_cost as maintenance_cost',
            'tbl_kpi_equipment_stage.total_cost as total_cost',
            'tbl_kpi_equipment_stage.warning_status as warning_status',
            'tbl_kpi_equipment_stage.note as note',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_kpi_equipment_stage';
        $join         = [];
        $where        = [];

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $key => $aRow) {
            $row   = [];
            $row[] = '<div class="text-center"><input type="checkbox" class="row-check" value="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '"></div>';
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['group_stage']    . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['stage_code']     . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['stage_name']     . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['equipment_code'] . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['equipment_name'] . '</div>';

            // Equipment status label
            $st = $aRow['equipment_status'];
            $stClass = 'label-default';
            if (stripos($st, 'hoạt động') !== false || stripos($st, 'hoat dong') !== false || strtolower($st) === 'active') {
                $stClass = 'label-success';
            } elseif (stripos($st, 'ngừng') !== false || stripos($st, 'ngung') !== false) {
                $stClass = 'label-danger';
            } elseif (stripos($st, 'bảo trì') !== false || stripos($st, 'bao tri') !== false) {
                $stClass = 'label-warning';
            }
            $row[] = '<div class="text-center"><span class="label ' . $stClass . '">' . $st . '</span></div>';

            $row[] = '<div class="text-right">' . $aRow['downtime_minutes'] . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['downtime_reason']  . '</div>';
            $row[] = '<div class="text-right">' . $aRow['repair_count']     . '</div>';
            $row[] = '<div class="text-right">' . $aRow['repair_minutes']   . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['periodic_maintenance'] ? '<span class="label label-success">Y</span>' : '<span class="label label-default">N</span>') . '</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['last_maintenance_date']) ? _d($aRow['last_maintenance_date']) : '') . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['calibration'] ? '<span class="label label-success">Y</span>' : '<span class="label label-default">N</span>') . '</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['last_calibration_date']) ? _d($aRow['last_calibration_date']) : '') . '</div>';
            $row[] = '<div class="text-right">' . $aRow['npl_warning_pct']        . '%</div>';
            $row[] = '<div class="text-right">' . $aRow['defect_count']           . '</div>';
            $row[] = '<div class="text-right">' . $aRow['defect_rate_pct']        . '%</div>';
            $row[] = '<div class="text-right">' . number_format($aRow['planned_output'], 2) . '</div>';
            $row[] = '<div class="text-right">' . number_format($aRow['actual_output'], 2)  . '</div>';
            $row[] = '<div class="text-right">' . $aRow['target_achievement_pct'] . '%</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['equipment_budget'])   . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['repair_cost'])         . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['maintenance_cost'])    . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['total_cost'])          . '</div>';

            // Warning status label
            $ws = $aRow['warning_status'];
            $wsHtml = '';
            if (!empty($ws)) {
                $wsClass = stripos($ws, 'nguy') !== false ? 'label-danger' : (stripos($ws, 'cảnh') !== false || stripos($ws, 'canh') !== false ? 'label-warning' : 'label-default');
                $wsHtml  = '<span class="label ' . $wsClass . '">' . $ws . '</span>';
            }
            $row[] = '<div class="text-center">' . $wsHtml . '</div>';
            $row[] = '<div class="text-left">'   . $aRow['note'] . '</div>';
            // Actions
            $edit   = $perEdit   ? '<a class="tnh-modal" href="' . base_url('admin/kpi_equipment_stage/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>' : '';
            $delete = $perDelete ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi_equipment_stage/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenuKpi" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenuKpi" style="width: 180px;">
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';

            $row[] = '<div>' . $actions . '</div>';
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    /**
     * Add / Edit record
     * Thêm mới / Sửa bản ghi
     */
    public function detail($id = 0)
    {
        if (!empty($id)) {
            if (!has_permission('kpi_equipment_stage', '', 'edit')) {
                access_denied('kpi_equipment_stage');
            }
        } else {
            if (!has_permission('kpi_equipment_stage', '', 'create')) {
                access_denied('kpi_equipment_stage');
            }
        }

        $data = [];
        $this->db->select('tbl_kpi_equipment_stage.*');
        $this->db->from('tbl_kpi_equipment_stage');
        $this->db->where('tbl_kpi_equipment_stage.id', $id);
        $dtData = $this->db->get()->row_array();

        if ($this->input->post()) {
            $this->form_validation->set_rules('stage_code',     lang('Stage Code'),     'required');
            $this->form_validation->set_rules('equipment_code', lang('Equipment Code'), 'required');
            $this->form_validation->set_rules('equipment_name', lang('Equipment Name'), 'required');

            if (empty($id)) {
                // Insert
                if ($this->form_validation->run() == true) {
                    $fields = $this->_buildFields();
                    $fields['created_by']   = get_staff_user_id();
                    $fields['date_created'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_kpi_equipment_stage', $fields);
                    $newId = $this->db->insert_id();

                    if ($newId) {
                        insertActivityLog([
                            'type_parent_obj' => 'kpi_equipment_stage',
                            'table_obj'       => 'tbl_kpi_equipment_stage',
                            'id_obj'          => $newId,
                            'name_obj'        => $fields['equipment_code'],
                            'content'         => lang('Add KPI Equipment Stage') . ' [' . $fields['equipment_code'] . ']',
                            'actions'         => 'add',
                        ]);
                        $data['result']  = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result']  = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result']  = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            } else {
                // Update
                if ($this->form_validation->run() == true) {
                    $fields = $this->_buildFields();

                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_kpi_equipment_stage', $fields);

                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'kpi_equipment_stage',
                            'table_obj'       => 'tbl_kpi_equipment_stage',
                            'id_obj'          => $id,
                            'name_obj'        => $dtData['equipment_code'],
                            'content'         => lang('Edit KPI Equipment Stage') . ' [' . $dtData['equipment_code'] . ']',
                            'actions'         => 'edit',
                        ]);
                        $data['result']  = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result']  = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result']  = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                $data['title'] = 'Thêm KPI Thiết Bị Công Đoạn';
            } else {
                $data['dtData'] = $dtData;
                $data['title'] = 'Sửa KPI Thiết Bị Công Đoạn';
            }
        }

        $data['id'] = $id;
        $this->load->view('admin/kpi_equipment_stage/detail_kpi_equipment_stage', $data);
    }

    /**
     * Delete record
     * Xóa bản ghi
     */
    public function delete($id)
    {
        if (!has_permission('kpi_equipment_stage', '', 'delete')) {
            $data['result']  = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $this->db->select('tbl_kpi_equipment_stage.*');
        $this->db->from('tbl_kpi_equipment_stage');
        $this->db->where('tbl_kpi_equipment_stage.id', $id);
        $dtData = $this->db->get()->row_array();

        if (empty($dtData)) {
            $data['result']  = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_kpi_equipment_stage');

        if ($success) {
            insertActivityLog([
                'type_parent_obj' => 'kpi_equipment_stage',
                'table_obj'       => 'tbl_kpi_equipment_stage',
                'id_obj'          => $id,
                'name_obj'        => $dtData['equipment_code'],
                'content'         => lang('Delete KPI Equipment Stage') . ' [' . $dtData['equipment_code'] . ']',
                'actions'         => 'delete',
            ]);
            $data['result']  = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result']  = 0;
            $data['message'] = lang('Xóa thất bại');
        }

        echo json_encode($data);
    }

    /**
     * Import from Excel file (Upsert by stage_code)
     * Nếu stage_code đã tồn tại → UPDATE, chưa tồn tại → INSERT
     */
    public function import_excel()
    {
        if (!has_permission('kpi_equipment_stage', '', 'create')) {
            access_denied('kpi_equipment_stage');
        }

        $data = [];
        if (!empty($_FILES)) {
            ini_set('max_execution_time', 800);
            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

            $tmpFile = $_FILES['file']['tmp_name'];
            $ext     = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['XLS', 'XLSX'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid file. Only XLS or XLSX accepted.']);
                die();
            }

            $excel      = PHPExcel_IOFactory::load($tmpFile);
            $sheet      = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $insertCount = 0;
            $updateCount = 0;
            $errors      = [];

            for ($row = 3; $row <= $highestRow; $row++) {
                // Excel columns:
                // A=STT, B=NhomCongDoan, C=MaCongDoan, D=TenCongDoan,
                // E=MaThietBi, F=TenThietBi, G=TrangThaiThietBi,
                // H=ThoiGianNgungMay, I=NguyenNhanNgungMay, J=SoLanSuaChua,
                // K=ThoiGianSuaChua, L=BaoTriDinhKy(Y/N), M=LanBaoTriGanNhat,
                // N=HieuChuan(Y/N), O=LanHieuChuanGanNhat, P=NPLCanhBao(%),
                // Q=SoLoi, R=TyLeLoi(%), S=NangSuatDinhMuc, T=SanLuongThucTe,
                // U=TyLeDatKH(%), V=NganSachThietBi, W=ChiPhiSuaChua,
                // X=ChiPhiBaoTri, Y=TongChiPhi, Z=TrangThaiCanhBao, AA=GhiChu

                $stage_code     = trim($sheet->getCell("C$row")->getCalculatedValue());
                $equipment_code = trim($sheet->getCell("E$row")->getCalculatedValue());

                // Skip empty rows
                if (empty($stage_code) && empty($equipment_code)) {
                    continue;
                }

                // Parse date columns (dùng getCalculatedValue để đọc đúng nếu ô chứa công thức)
                $last_maintenance = $sheet->getCell("M$row")->getCalculatedValue();
                $last_calibration = $sheet->getCell("O$row")->getCalculatedValue();

                if (is_numeric($last_maintenance) && !empty($last_maintenance)) {
                    $last_maintenance = date('Y-m-d', ($last_maintenance - 25569) * 86400);
                } elseif (!empty($last_maintenance)) {
                    $last_maintenance = to_sql_date($last_maintenance, true);
                } else {
                    $last_maintenance = null;
                }

                if (is_numeric($last_calibration) && !empty($last_calibration)) {
                    $last_calibration = date('Y-m-d', ($last_calibration - 25569) * 86400);
                } elseif (!empty($last_calibration)) {
                    $last_calibration = to_sql_date($last_calibration, true);
                } else {
                    $last_calibration = null;
                }

                // Y/N → 1/0
                $periodic_maintenance = (strtoupper(trim($sheet->getCell("L$row")->getCalculatedValue())) === 'Y') ? 1 : 0;
                $calibration          = (strtoupper(trim($sheet->getCell("N$row")->getCalculatedValue())) === 'Y') ? 1 : 0;

                $rowData = [
                    'group_stage'            => trim($sheet->getCell("B$row")->getCalculatedValue()),
                    'stage_code'             => $stage_code,
                    'stage_name'             => trim($sheet->getCell("D$row")->getCalculatedValue()),
                    'equipment_code'         => $equipment_code,
                    'equipment_name'         => trim($sheet->getCell("F$row")->getCalculatedValue()),
                    'equipment_status'       => trim($sheet->getCell("G$row")->getCalculatedValue()),
                    'downtime_minutes'       => (float) str_replace(',', '', $sheet->getCell("H$row")->getCalculatedValue()),
                    'downtime_reason'        => trim($sheet->getCell("I$row")->getCalculatedValue()),
                    'repair_count'           => (int)   $sheet->getCell("J$row")->getCalculatedValue(),
                    'repair_minutes'         => (float) str_replace(',', '', $sheet->getCell("K$row")->getCalculatedValue()),
                    'periodic_maintenance'   => $periodic_maintenance,
                    'last_maintenance_date'  => $last_maintenance,
                    'calibration'            => $calibration,
                    'last_calibration_date'  => $last_calibration,
                    'npl_warning_pct'        => (float) str_replace(',', '', $sheet->getCell("P$row")->getCalculatedValue()),
                    'defect_count'           => (int)   $sheet->getCell("Q$row")->getCalculatedValue(),
                    'defect_rate_pct'        => (float) str_replace(',', '', $sheet->getCell("R$row")->getCalculatedValue()),
                    'planned_output'         => (float) str_replace(',', '', $sheet->getCell("S$row")->getCalculatedValue()),
                    'actual_output'          => (float) str_replace(',', '', $sheet->getCell("T$row")->getCalculatedValue()),
                    'target_achievement_pct' => (float) str_replace(',', '', $sheet->getCell("U$row")->getCalculatedValue()),
                    'equipment_budget'       => (float) str_replace(',', '', $sheet->getCell("V$row")->getCalculatedValue()),
                    'repair_cost'            => (float) str_replace(',', '', $sheet->getCell("W$row")->getCalculatedValue()),
                    'maintenance_cost'       => (float) str_replace(',', '', $sheet->getCell("X$row")->getCalculatedValue()),
                    'total_cost'             => (float) str_replace(',', '', $sheet->getCell("Y$row")->getCalculatedValue()),
                    'warning_status'         => trim($sheet->getCell("Z$row")->getCalculatedValue()),
                    'note'                   => trim($sheet->getCell("AA$row")->getCalculatedValue()),
                ];

                // Kiểm tra equipment_code đã tồn tại chưa
                $existing = $this->db->get_where('tbl_kpi_equipment_stage', ['equipment_code' => $equipment_code])->row_array();

                if (!empty($existing)) {
                    // Cập nhật (UPDATE)
                    $this->db->where('equipment_code', $equipment_code);
                    if ($this->db->update('tbl_kpi_equipment_stage', $rowData)) {
                        $updateCount++;
                    }
                } else {
                    // Thêm mới (INSERT)
                    $rowData['created_by']   = get_staff_user_id();
                    $rowData['date_created'] = date('Y-m-d H:i:s');
                    if ($this->db->insert('tbl_kpi_equipment_stage', $rowData)) {
                        $insertCount++;
                    }
                }
            }

            $msg  = 'Import thành công: ' . $insertCount . ' thêm mới';
            $msg .= ($updateCount > 0) ? ', ' . $updateCount . ' cập nhật.' : '.';

            echo json_encode([
                'success' => true,
                'message' => $msg,
                'errors'  => !empty($errors) ? implode('<br>', $errors) : '',
            ]);
            die();
        }

        $data['title'] = 'Import KPI Thiết Bị Công Đoạn';
        $this->load->view('admin/kpi_equipment_stage/import_kpi_equipment_stage', $data);
    }

    /**
     * Export records to Excel (same format as import template)
     * POST ids[] = danh sách id cần xuất, nếu rỗng → xuất tất cả
     */
    public function export_excel()
    {
        if (!has_permission('kpi_equipment_stage', '', 'view')) {
            access_denied('kpi_equipment_stage');
        }

        ini_set('max_execution_time', 800);
        require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

        // Lấy dữ liệu
        $ids = $this->input->post('ids');
        if (!empty($ids) && is_array($ids)) {
            $this->db->where_in('id', $ids);
        }
        $this->db->order_by('stage_code', 'ASC');
        $records = $this->db->get('tbl_kpi_equipment_stage')->result_array();

        // Tạo workbook
        $objPHPExcel = new PHPExcel();
        $sheet       = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('KPI_Equipment_Stage');

        // Cột header
        $headers = [
            'STT',
            'Nhóm Công Đoạn',
            'Mã Công Đoạn',
            'Tên Công Đoạn',
            'Mã Thiết Bị',
            'Tên Thiết Bị',
            'Trạng Thái Thiết Bị',
            'Thời Gian Ngừng Máy (phút)',
            'Nguyên Nhân Ngừng Máy',
            'Số Lần Sửa Chữa',
            'Thời Gian Sửa Chữa (phút)',
            'Bảo Trì Định Kỳ (Y/N)',
            'Lần Bảo Trì Gần Nhất',
            'Hiệu Chuẩn (Y/N)',
            'Lần Hiệu Chuẩn Gần Nhất',
            'NPL Cảnh Báo (%)',
            'Số Lỗi',
            'Tỷ Lệ Lỗi (%)',
            'Năng Suất Định Mức',
            'Sản Lượng Thực Tế',
            'Tỷ Lệ Đạt KH (%)',
            'Ngân Sách Thiết Bị',
            'Chi Phí Sửa Chữa',
            'Chi Phí Bảo Trì',
            'Tổng Chi Phí',
            'Trạng Thái Cảnh Báo',
            'Ghi Chú',
        ];

        // Style header
        $headerStyle = [
            'font'      => ['bold' => true],
            // 'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E75B6']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true],
            'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
        ];

        // Row 1: cột header
        foreach ($headers as $idx => $label) {
            $sheet->setCellValueByColumnAndRow($idx, 1, $label);
        }
        $sheet->getStyle('A1:AA1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Row 2: gợi ý (giống file mẫu)
        foreach ($headers as $idx => $label) {
            $sheet->setCellValueByColumnAndRow($idx, 2, $idx + 1);
        }
        $sheet->getStyle('A2:AA2')->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'C6E0B4']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
        ]);

        // Ghi dữ liệu từ row 3
        $rowNum = 3;
        $stt    = 1;
        foreach ($records as $rec) {
            $sheet->setCellValueByColumnAndRow(0,  $rowNum, $stt++);
            $sheet->setCellValueByColumnAndRow(1,  $rowNum, $rec['group_stage']);
            $sheet->setCellValueByColumnAndRow(2,  $rowNum, $rec['stage_code']);
            $sheet->setCellValueByColumnAndRow(3,  $rowNum, $rec['stage_name']);
            $sheet->setCellValueByColumnAndRow(4,  $rowNum, $rec['equipment_code']);
            $sheet->setCellValueByColumnAndRow(5,  $rowNum, $rec['equipment_name']);
            $sheet->setCellValueByColumnAndRow(6,  $rowNum, $rec['equipment_status']);
            $sheet->setCellValueByColumnAndRow(7,  $rowNum, $rec['downtime_minutes']);
            $sheet->setCellValueByColumnAndRow(8,  $rowNum, $rec['downtime_reason']);
            $sheet->setCellValueByColumnAndRow(9,  $rowNum, $rec['repair_count']);
            $sheet->setCellValueByColumnAndRow(10, $rowNum, $rec['repair_minutes']);
            $sheet->setCellValueByColumnAndRow(11, $rowNum, $rec['periodic_maintenance'] ? 'Y' : 'N');
            $sheet->setCellValueByColumnAndRow(12, $rowNum, !empty($rec['last_maintenance_date']) ? $rec['last_maintenance_date'] : '');
            $sheet->setCellValueByColumnAndRow(13, $rowNum, $rec['calibration'] ? 'Y' : 'N');
            $sheet->setCellValueByColumnAndRow(14, $rowNum, !empty($rec['last_calibration_date']) ? $rec['last_calibration_date'] : '');
            $sheet->setCellValueByColumnAndRow(15, $rowNum, $rec['npl_warning_pct']);
            $sheet->setCellValueByColumnAndRow(16, $rowNum, $rec['defect_count']);
            $sheet->setCellValueByColumnAndRow(17, $rowNum, $rec['defect_rate_pct']);
            $sheet->setCellValueByColumnAndRow(18, $rowNum, $rec['planned_output']);
            $sheet->setCellValueByColumnAndRow(19, $rowNum, $rec['actual_output']);
            $sheet->setCellValueByColumnAndRow(20, $rowNum, $rec['target_achievement_pct']);
            $sheet->setCellValueByColumnAndRow(21, $rowNum, $rec['equipment_budget']);
            $sheet->setCellValueByColumnAndRow(22, $rowNum, $rec['repair_cost']);
            $sheet->setCellValueByColumnAndRow(23, $rowNum, $rec['maintenance_cost']);
            $sheet->setCellValueByColumnAndRow(24, $rowNum, $rec['total_cost']);
            $sheet->setCellValueByColumnAndRow(25, $rowNum, $rec['warning_status']);
            $sheet->setCellValueByColumnAndRow(26, $rowNum, $rec['note']);
            $rowNum++;
        }

        $sheet->getStyle('A3:AA' . $rowNum)->applyFromArray([
            'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
        ]);

        // Auto-width các cột
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);

        // Xuất file download
        $filename = 'KPI_Equipment_Stage_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    /**
     * Build fields array from POST data
     * Xây dựng mảng fields từ dữ liệu POST
     */
    private function _buildFields()
    {
        return [
            'group_stage'            => $this->input->post('group_stage'),
            'stage_code'             => $this->input->post('stage_code'),
            'stage_name'             => $this->input->post('stage_name'),
            'equipment_code'         => $this->input->post('equipment_code'),
            'equipment_name'         => $this->input->post('equipment_name'),
            'equipment_status'       => $this->input->post('equipment_status'),
            'downtime_minutes'       => (float) str_replace(',', '', $this->input->post('downtime_minutes')),
            'downtime_reason'        => $this->input->post('downtime_reason'),
            'repair_count'           => (int)   $this->input->post('repair_count'),
            'repair_minutes'         => (float) str_replace(',', '', $this->input->post('repair_minutes')),
            'periodic_maintenance'   => $this->input->post('periodic_maintenance') ? 1 : 0,
            'last_maintenance_date'  => $this->input->post('last_maintenance_date') ? to_sql_date($this->input->post('last_maintenance_date')) : null,
            'calibration'            => $this->input->post('calibration') ? 1 : 0,
            'last_calibration_date'  => $this->input->post('last_calibration_date') ? to_sql_date($this->input->post('last_calibration_date')) : null,
            'npl_warning_pct'        => (float) str_replace(',', '', $this->input->post('npl_warning_pct')),
            'defect_count'           => (int)   $this->input->post('defect_count'),
            'defect_rate_pct'        => (float) str_replace(',', '', $this->input->post('defect_rate_pct')),
            'planned_output'         => (float) str_replace(',', '', $this->input->post('planned_output')),
            'actual_output'          => (float) str_replace(',', '', $this->input->post('actual_output')),
            'target_achievement_pct' => (float) str_replace(',', '', $this->input->post('target_achievement_pct')),
            'equipment_budget'       => (float) str_replace(',', '', $this->input->post('equipment_budget')),
            'repair_cost'            => (float) str_replace(',', '', $this->input->post('repair_cost')),
            'maintenance_cost'       => (float) str_replace(',', '', $this->input->post('maintenance_cost')),
            'total_cost'             => (float) str_replace(',', '', $this->input->post('total_cost')),
            'warning_status'         => $this->input->post('warning_status'),
            'note'                   => $this->input->post('note'),
        ];
    }
}
