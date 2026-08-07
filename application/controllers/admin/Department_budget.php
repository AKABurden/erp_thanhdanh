<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Department_budget extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('department_budget_model');
        $this->view   = has_permission('department_budget', '', 'view');
        $this->edit   = has_permission('department_budget', '', 'edit');
        $this->delete = has_permission('department_budget', '', 'delete');
        $this->create = has_permission('department_budget', '', 'create');
        $this->view   = true;
        $this->edit   = true;
        $this->delete = true;
        $this->create = true;
    }

    /**
     * List page - Trang danh sách KPI Ngân Sách Phòng Ban
     */
    public function index()
    {
        if (!$this->view) {
            access_denied('department_budget');
        }
        $data['title']         = 'KPI Ngân Sách Phòng Ban';
        $data['dtDepartments'] = $this->db->get_where('tbldepartments', ['room_id !=' => 0])->result_array();
        $this->load->view('admin/department_budget/department_budget', $data);
    }

    /**
     * Server-side DataTable AJAX
     * Lấy dữ liệu DataTable server-side
     */
    public function get_list()
    {
        if (!$this->view) {
            echo json_encode(['aaData' => []]);
            return;
        }
        $perEdit   = $this->edit;
        $perDelete = $this->delete;

        $currentYear = date('Y');

        // Subquery tính chi_phi_thuc_te tự nguồn 1: tblother_payslips.id_costs trực tiếp
        $subSrc1 = "(SELECT COALESCE(SUM(op1.total), 0)
                     FROM tblother_payslips op1
                     WHERE op1.id_costs = tbl_department_budget.cost_id
                     AND YEAR(op1.date) = {$currentYear})";

        // Subquery tính chi_phi_thuc_te từ nguồn 2: tblother_payslip_cost (nhiều cost/phiếu)
        $subSrc2 = "(SELECT COALESCE(SUM(opc.total), 0)
                     FROM tblother_payslip_cost opc
                     INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id
                     WHERE opc.cost_id = tbl_department_budget.cost_id
                     AND YEAR(op2.date) = {$currentYear})";

        // Lấy filter
        $department_id = $this->input->post('department_id');
        $whereSql = '1=1';
        if (!empty($department_id)) {
            $whereSql .= ' AND tbl_department_budget.department_id = ' . (int) $department_id;
        }

        // Lấy tổng số bản ghi (cho DataTable)
        $totalQuery = "
            SELECT COUNT(*) as cnt
            FROM tbl_department_budget
            LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_department_budget.department_id
            LEFT JOIN tblcosts ON tblcosts.id = tbl_department_budget.cost_id
            WHERE {$whereSql}
        ";
        $totalRow = $this->db->query($totalQuery)->row();
        $totalRecords = !empty($totalRow) ? (int) $totalRow->cnt : 0;

        // Lấy dữ liệu
        $mainQuery = "
            SELECT
                tbl_department_budget.id,
                tbl_department_budget.department_id,
                tbl_department_budget.cost_id,
                tbl_department_budget.ngan_sach_duoc_cap,
                tbl_department_budget.ghi_chu,
                tbldepartments.code  AS ma_phong_ban,
                tbldepartments.name  AS ten_phong_ban,
                tblcosts.code        AS ma_loai_chi_phi,
                tblcosts.name        AS ten_loai_chi_phi,
                ({$subSrc1} + {$subSrc2}) AS chi_phi_thuc_te
            FROM tbl_department_budget
            LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_department_budget.department_id
            LEFT JOIN tblcosts ON tblcosts.id = tbl_department_budget.cost_id
            WHERE {$whereSql}
            ORDER BY tbldepartments.code ASC, tblcosts.code ASC
        ";
        $records = $this->db->query($mainQuery)->result_array();

        $output = [
            'sEcho'                => (int) $this->input->post('sEcho'),
            'iTotalRecords'        => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'aaData'               => [],
        ];

        $key = 0;
        foreach ($records as $aRow) {
            $row = [];

            // Tính toán KPI trực tiếp
            $ngan_sach       = (float) $aRow['ngan_sach_duoc_cap'];
            $chi_phi         = (float) $aRow['chi_phi_thuc_te'];
            $chenh_lech      = $chi_phi - $ngan_sach;
            $ty_le           = ($ngan_sach > 0) ? round($chi_phi / $ngan_sach * 100, 2) : 0;
            list($tt, $diem) = $this->_getKPIStatus($ty_le);

            $row[] = '<div class="text-center"><input type="checkbox" class="row-check" value="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '"></div>';
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['ma_phong_ban']     . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['ten_phong_ban']    . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['ma_loai_chi_phi']  . '</div>';
            $row[] = '<div class="text-left">'  . $aRow['ten_loai_chi_phi'] . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($ngan_sach)   . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($chi_phi)     . '</div>';

            // Chênh lệch: dương (vượt) → đỏ, âm (tiết kiệm) → xanh
            $clClass = $chenh_lech > 0 ? 'text-danger' : 'text-success';
            $row[] = '<div class="text-right ' . $clClass . '">' . formatMoney($chenh_lech) . '</div>';

            // Tỷ lệ sử dụng
            $tyleClass = 'label-success';
            if ($ty_le > 110) {
                $tyleClass = 'label-danger';
            } elseif ($ty_le > 100) {
                $tyleClass = 'label-warning';
            } elseif ($ty_le > 90) {
                $tyleClass = 'label-info';
            }
            $row[] = '<div class="text-center"><span class="label ' . $tyleClass . '">' . number_format($ty_le, 2) . '%</span></div>';

            // Trạng thái ngân sách
            $ttMap   = ['Tốt' => 'label-success', 'Đạt' => 'label-info', 'Cảnh báo' => 'label-warning', 'Vượt' => 'label-danger'];
            $ttClass = $ttMap[$tt] ?? 'label-default';
            $row[] = '<div class="text-center"><span class="label ' . $ttClass . '">' . $tt . '</span></div>';

            // Điểm KPI
            $diemClass = 'label-success';
            if ($diem <= 50) {
                $diemClass = 'label-danger';
            } elseif ($diem <= 70) {
                $diemClass = 'label-warning';
            } elseif ($diem <= 90) {
                $diemClass = 'label-info';
            }
            $row[] = '<div class="text-center"><span class="label ' . $diemClass . '">' . $diem . '</span></div>';

            $row[] = '<div class="text-left">' . $aRow['ghi_chu'] . '</div>';

            // Actions
            $edit   = $perEdit   ? '<a class="tnh-modal" href="' . base_url('admin/department_budget/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . '</a>' : '';
            $delete = $perDelete ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/department_budget/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>' : '';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenuBudget" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenuBudget" style="width: 180px;">
                    <li class="not-outside">' . $edit . '</li>
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
            if (!$this->edit) {
                access_denied('department_budget');
            }
        } else {
            if (!$this->create) {
                access_denied('department_budget');
            }
        }

        $data = [];
        $this->db->select('tbl_department_budget.*, tbldepartments.name as ten_phong_ban, tbldepartments.code as ma_phong_ban, tblcosts.name as ten_loai_chi_phi, tblcosts.code as ma_loai_chi_phi');
        $this->db->from('tbl_department_budget');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_department_budget.department_id', 'left');
        $this->db->join('tblcosts', 'tblcosts.id = tbl_department_budget.cost_id', 'left');
        $this->db->where('tbl_department_budget.id', $id);
        $dtData = $this->db->get()->row_array();

        // Lấy danh sách phòng ban
        $data['dtDepartments'] = $this->db->get_where('tbldepartments', ['room_id !=' => 0])->result_array();

        // Lấy danh sách loại chi phí (chỉ lấy cost con - có costs_parent > 0)
        $this->db->select('tblcosts.*, tblcosts_parent.name as ten_cha, tblcosts_parent.code as ma_cha');
        $this->db->from('tblcosts');
        $this->db->join('tblcosts tblcosts_parent', 'tblcosts_parent.id = tblcosts.costs_parent', 'left');
        $this->db->where('tblcosts.costs_parent >', 0);
        $this->db->order_by('tblcosts.code', 'ASC');
        $data['dtCosts'] = $this->db->get()->result_array();

        // Nếu đang sửa: tính chi_phi_thuc_te trực tiếp từ phiếu chi trong năm hiện tại
        if (!empty($dtData)) {
            $currentYear = date('Y');
            $cost_id     = $dtData['cost_id'];
            $sql = "
                SELECT
                    COALESCE((
                        SELECT SUM(op1.total)
                        FROM tblother_payslips op1
                        WHERE op1.id_costs = {$cost_id}
                        AND YEAR(op1.date) = {$currentYear}
                    ), 0)
                    +
                    COALESCE((
                        SELECT SUM(opc.total)
                        FROM tblother_payslip_cost opc
                        INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id
                        WHERE opc.cost_id = {$cost_id}
                        AND YEAR(op2.date) = {$currentYear}
                    ), 0) AS chi_phi_thuc_te
            ";
            $row = $this->db->query($sql)->row();
            $dtData['chi_phi_thuc_te'] = !empty($row) ? (float) $row->chi_phi_thuc_te : 0;

            // Tính KPI để hiển thị trên form
            $ngan_sach = (float) $dtData['ngan_sach_duoc_cap'];
            $chi_phi   = $dtData['chi_phi_thuc_te'];
            $dtData['chenh_lech']    = $chi_phi - $ngan_sach;
            $dtData['ty_le_su_dung'] = ($ngan_sach > 0) ? round($chi_phi / $ngan_sach * 100, 2) : 0;
            list($dtData['trang_thai_ngan_sach'], $dtData['diem_kpi']) = $this->_getKPIStatus($dtData['ty_le_su_dung']);
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('department_id', lang('Phòng ban'),   'required');
            $this->form_validation->set_rules('cost_id',       lang('Loại chi phí'), 'required');
            $this->form_validation->set_rules('ngan_sach_duoc_cap', lang('Ngân sách được cấp'), 'required');

            if (empty($id)) {
                // Insert
                if ($this->form_validation->run() == true) {
                    $fields = $this->_buildFields();
                    $fields['created_by']   = get_staff_user_id();
                    $fields['date_created'] = date('Y-m-d H:i:s');

                    $this->db->insert('tbl_department_budget', $fields);
                    $newId = $this->db->insert_id();

                    if ($newId) {
                        insertActivityLog([
                            'type_parent_obj' => 'department_budget',
                            'table_obj'       => 'tbl_department_budget',
                            'id_obj'          => $newId,
                            'name_obj'        => 'Phòng ban #' . $fields['department_id'],
                            'content'         => lang('Thêm KPI Ngân Sách Phòng Ban') . ' [#' . $newId . ']',
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
                    $success = $this->db->update('tbl_department_budget', $fields);

                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'department_budget',
                            'table_obj'       => 'tbl_department_budget',
                            'id_obj'          => $id,
                            'name_obj'        => 'Phòng ban #' . $fields['department_id'],
                            'content'         => lang('Sửa KPI Ngân Sách Phòng Ban') . ' [#' . $id . ']',
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
                $data['title'] = 'Thêm KPI Ngân Sách Phòng Ban';
            } else {
                $data['dtData'] = $dtData;
                $data['title']  = 'Sửa KPI Ngân Sách Phòng Ban';
            }
        }

        $data['id'] = $id;
        $this->load->view('admin/department_budget/detail_department_budget', $data);
    }

    /**
     * Delete record
     * Xóa bản ghi
     */
    public function delete($id)
    {
        if (!$this->delete) {
            $data['result']  = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data   = [];
        $dtData = $this->db->get_where('tbl_department_budget', ['id' => $id])->row_array();

        if (empty($dtData)) {
            $data['result']  = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_department_budget');

        if ($success) {
            insertActivityLog([
                'type_parent_obj' => 'department_budget',
                'table_obj'       => 'tbl_department_budget',
                'id_obj'          => $id,
                'name_obj'        => 'Phòng ban #' . $dtData['department_id'],
                'content'         => lang('Xóa KPI Ngân Sách Phòng Ban') . ' [#' . $id . ']',
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
     * Import from Excel file (Upsert by department_id + cost_id)
     * Nếu (department_id + cost_id) đã tồn tại → UPDATE, chưa tồn tại → INSERT
     * Excel columns:
     *   A=STT, B=MaPhongBan, C=TenPhongBan, D=LoaiChiPhi(MaChiPhi),
     *   E=NganSachDuocCap, F=ChiPhiThucTe, G=ChenhLech, H=TyLeSuDung(%),
     *   I=TrangThaiNganSach, J=DiemKPI, K=GhiChu
     */
    public function import_excel()
    {
        if (!$this->create) {
            access_denied('department_budget');
        }

        $data = [];
        if (!empty($_FILES)) {
            ini_set('max_execution_time', 800);
            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

            $tmpFile = $_FILES['file']['tmp_name'];
            $ext     = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['XLS', 'XLSX'])) {
                echo json_encode(['success' => false, 'message' => 'File không hợp lệ. Chỉ chấp nhận XLS hoặc XLSX.']);
                die();
            }

            $excel      = PHPExcel_IOFactory::load($tmpFile);
            $sheet      = $excel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $insertCount = 0;
            $updateCount = 0;
            $errors      = [];

            // Bắt đầu từ row 3 (row 1=header tên cột, row 2=số thứ tự cột)
            for ($row = 3; $row <= $highestRow; $row++) {
                // Lấy mã phòng ban (cột B)
                $ma_phong_ban = trim($sheet->getCell("B{$row}")->getValue());
                // Lấy mã loại chi phí (cột D)
                $ma_loai_chi_phi = trim($sheet->getCell("D{$row}")->getValue());

                // Bỏ qua dòng trống
                if (empty($ma_phong_ban) && empty($ma_loai_chi_phi)) {
                    continue;
                }

                // Tìm department_id theo mã phòng ban
                $department = $this->db->get_where('tbldepartments', ['code' => $ma_phong_ban])->row_array();
                if (empty($department)) {
                    $errors[] = "Dòng {$row}: Không tìm thấy phòng ban mã '{$ma_phong_ban}'";
                    continue;
                }
                $department_id = $department['departmentid'];

                // Tìm cost_id theo mã loại chi phí
                $cost = $this->db->get_where('tblcosts', ['code' => $ma_loai_chi_phi])->row_array();
                if (empty($cost)) {
                    $errors[] = "Dòng {$row}: Không tìm thấy loại chi phí mã '{$ma_loai_chi_phi}'";
                    continue;
                }
                $cost_id = $cost['id'];

                // Kiểm tra cost có được gán cho phòng ban này không (tblcost_department)
                $costDeptLink = $this->db->get_where('tblcost_department', [
                    'cost_id'       => $cost_id,
                    'department_id' => $department_id,
                ])->row_array();
                if (empty($costDeptLink)) {
                    $errors[] = "Dòng {$row}: Loại chi phí '{$ma_loai_chi_phi}' chưa được gán cho phòng ban '{$ma_phong_ban}'. Vui lòng kiểm tra cấu hình loại chi phí.";
                    continue;
                }

                $ngan_sach_duoc_cap = (float) str_replace(',', '', $sheet->getCell("E{$row}")->getValue());

                $rowData = [
                    'department_id'      => $department_id,
                    'cost_id'            => $cost_id,
                    'ngan_sach_duoc_cap' => $ngan_sach_duoc_cap,
                    'ghi_chu'            => trim($sheet->getCell("K{$row}")->getValue()),
                ];

                // Kiểm tra đã tồn tại chưa (theo department_id + cost_id)
                $existing = $this->db->get_where('tbl_department_budget', [
                    'department_id' => $department_id,
                    'cost_id'       => $cost_id,
                ])->row_array();

                if (!empty($existing)) {
                    // Cập nhật (UPDATE)
                    $this->db->where('id', $existing['id']);
                    if ($this->db->update('tbl_department_budget', $rowData)) {
                        $updateCount++;
                    }
                } else {
                    // Thêm mới (INSERT)
                    $rowData['created_by']   = get_staff_user_id();
                    $rowData['date_created'] = date('Y-m-d H:i:s');
                    if ($this->db->insert('tbl_department_budget', $rowData)) {
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

        $data['title'] = 'Import KPI Ngân Sách Phòng Ban';
        $this->load->view('admin/department_budget/import_department_budget', $data);
    }

    /**
     * Export records to Excel (same format as import template)
     * POST ids[] = danh sách id cần xuất, nếu rỗng → xuất tất cả
     */
    public function export_excel()
    {
        if (!$this->view) {
            access_denied('department_budget');
        }

        ini_set('max_execution_time', 800);
        require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');

        // Lấy dữ liệu
        $ids = $this->input->post('ids');
        $currentYear = date('Y');

        // Subquery tính chi_phi_thuc_te tự nguồn 1: tblother_payslips.id_costs trực tiếp
        $subSrc1 = "(SELECT COALESCE(SUM(op1.total), 0)
                     FROM tblother_payslips op1
                     WHERE op1.id_costs = tbl_department_budget.cost_id
                     AND YEAR(op1.date) = {$currentYear})";

        // Subquery tính chi_phi_thuc_te từ nguồn 2: tblother_payslip_cost (nhiều cost/phiếu)
        $subSrc2 = "(SELECT COALESCE(SUM(opc.total), 0)
                     FROM tblother_payslip_cost opc
                     INNER JOIN tblother_payslips op2 ON op2.id = opc.other_payslip_id
                     WHERE opc.cost_id = tbl_department_budget.cost_id
                     AND YEAR(op2.date) = {$currentYear})";

        $mainQuery = "
            SELECT
                tbl_department_budget.*,
                tbldepartments.code AS ma_phong_ban,
                tbldepartments.name AS ten_phong_ban,
                tblcosts.code       AS ma_loai_chi_phi,
                tblcosts.name       AS ten_loai_chi_phi,
                ({$subSrc1} + {$subSrc2}) AS chi_phi_thuc_te
            FROM tbl_department_budget
            LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbl_department_budget.department_id
            LEFT JOIN tblcosts ON tblcosts.id = tbl_department_budget.cost_id
            WHERE 1=1
        ";

        if (!empty($ids) && is_array($ids)) {
            $mainQuery .= " AND tbl_department_budget.id IN (" . implode(',', array_map('intval', $ids)) . ")";
        }
        $mainQuery .= " ORDER BY tbldepartments.code ASC, tblcosts.code ASC";

        $records = $this->db->query($mainQuery)->result_array();

        // Tạo workbook
        $objPHPExcel = new PHPExcel();
        $sheet       = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('KPI_NganSach_PhongBan');

        // Header style
        $headerStyle = [
            'font'      => ['bold' => true],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap'       => true,
            ],
            'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
            'fill'    => [
                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E75B6'],
            ],
        ];
        $dataStyle = [
            'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
        ];

        // Row 1: Tiêu đề
        $headers = [
            'STT',
            'Mã Phòng Ban',
            'Tên Phòng Ban',
            'Loại Chi Phí(Mã Chi Phí)',
            'Ngân Sách Được Cấp',
            'Chi Phí Thực Tế',
            'Chênh Lệch',
            'Tỷ Lệ Sử Dụng(%)',
            'Trạng Thái Ngân Sách',
            'Điểm KPI',
            'Ghi Chú',
        ];

        foreach ($headers as $idx => $label) {
            $sheet->setCellValueByColumnAndRow($idx, 1, $label);
        }
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Row 2: Số thứ tự cột (gợi ý)
        foreach ($headers as $idx => $label) {
            $sheet->setCellValueByColumnAndRow($idx, 2, $idx + 1);
        }
        $sheet->getStyle('A2:K2')->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'C6E0B4']],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
        ]);

        // Ghi dữ liệu từ row 3
        $rowNum = 3;
        $stt    = 1;
        foreach ($records as $rec) {
            // Tính toán KPI trực tiếp
            $ngan_sach   = (float) $rec['ngan_sach_duoc_cap'];
            $chi_phi     = (float) $rec['chi_phi_thuc_te'];
            $chenh_lech  = $chi_phi - $ngan_sach;
            $ty_le       = ($ngan_sach > 0) ? round($chi_phi / $ngan_sach * 100, 2) : 0;
            list($tt, $diem) = $this->_getKPIStatus($ty_le);

            $sheet->setCellValueByColumnAndRow(0,  $rowNum, $stt++);
            $sheet->setCellValueByColumnAndRow(1,  $rowNum, $rec['ma_phong_ban']);
            $sheet->setCellValueByColumnAndRow(2,  $rowNum, $rec['ten_phong_ban']);
            $sheet->setCellValueByColumnAndRow(3,  $rowNum, $rec['ma_loai_chi_phi']);
            $sheet->setCellValueByColumnAndRow(4,  $rowNum, $ngan_sach);
            $sheet->setCellValueByColumnAndRow(5,  $rowNum, $chi_phi);
            $sheet->setCellValueByColumnAndRow(6,  $rowNum, $chenh_lech);
            $sheet->setCellValueByColumnAndRow(7,  $rowNum, $ty_le);
            $sheet->setCellValueByColumnAndRow(8,  $rowNum, $tt);
            $sheet->setCellValueByColumnAndRow(9,  $rowNum, $diem);
            $sheet->setCellValueByColumnAndRow(10, $rowNum, $rec['ghi_chu']);
            $rowNum++;
        }

        if ($rowNum > 3) {
            $sheet->getStyle('A3:K' . ($rowNum - 1))->applyFromArray($dataStyle);
        }

        // Auto-width cột
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Xuất file
        $filename = 'KPI_NganSach_PhongBan_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    /**
     * API: Lấy danh sách phòng ban cho dropdown/select2
     */
    public function get_departments()
    {
        $search = $this->input->get('q');
        $this->db->select('departmentid as id, name, code');
        $this->db->from('tbldepartments');
        $this->db->where('room_id !=', 0);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('code', 'ASC');
        $result = $this->db->get()->result_array();
        echo json_encode($result);
    }

    /**
     * API: Lấy danh sách loại chi phí cho dropdown/select2
     */
    public function get_costs()
    {
        $search = $this->input->get('q');
        $this->db->select('tblcosts.id, tblcosts.code, tblcosts.name, tblcosts_parent.name as ten_cha');
        $this->db->from('tblcosts');
        $this->db->join('tblcosts tblcosts_parent', 'tblcosts_parent.id = tblcosts.costs_parent', 'left');
        $this->db->where('tblcosts.costs_parent >', 0);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('tblcosts.name', $search);
            $this->db->or_like('tblcosts.code', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tblcosts.code', 'ASC');
        $result = $this->db->get()->result_array();
        echo json_encode($result);
    }

    /**
     * Build fields array from POST data
     * Chỉ lưu ngân sách được cấp, các giá trị khác tính trực tiếp từ phiếu chi
     */
    private function _buildFields()
    {
        return [
            'department_id'      => (int)   $this->input->post('department_id'),
            'cost_id'            => (int)   $this->input->post('cost_id'),
            'ngan_sach_duoc_cap' => (float) str_replace(',', '', $this->input->post('ngan_sach_duoc_cap')),
            'ghi_chu'            => $this->input->post('ghi_chu'),
        ];
    }

    /**
     * Tính toán KPI tự động từ ngân sách và chi phí thực tế
     */
    private function _calculateKPI(array $fields)
    {
        $ngan_sach = $fields['ngan_sach_duoc_cap'];
        $chi_phi   = $fields['chi_phi_thuc_te'];

        $chenh_lech    = $chi_phi - $ngan_sach;
        $ty_le_su_dung = ($ngan_sach > 0) ? round($chi_phi / $ngan_sach * 100, 2) : 0;

        list($trang_thai, $diem_kpi) = $this->_getKPIStatus($ty_le_su_dung);

        $fields['chenh_lech']           = $chenh_lech;
        $fields['ty_le_su_dung']        = $ty_le_su_dung;
        $fields['trang_thai_ngan_sach'] = $trang_thai;
        $fields['diem_kpi']             = $diem_kpi;

        return $fields;
    }

    /**
     * Xác định trạng thái ngân sách và điểm KPI theo tỷ lệ sử dụng
     */
    private function _getKPIStatus($ty_le)
    {
        if ($ty_le <= 90) {
            return ['Tốt', 100];
        } elseif ($ty_le <= 100) {
            return ['Đạt', 90];
        } elseif ($ty_le <= 110) {
            return ['Cảnh báo', 70];
        } else {
            return ['Vượt', 50];
        }
    }
}
