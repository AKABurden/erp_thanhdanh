<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Kpi_targets_supplier extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            access_denied();
        }
    }

    public function index()
    {
        $data['title'] = _l('Mục tiêu KPI nhà cung cấp');
        $data['name'] = _l('Nội Quy');
        $data['type'] = 1;
        $this->load->view('admin/kpi_targets_supplier/manage', $data);
    }

    public function table()
    {
        $year = $this->input->post("year_search");

        $aColumns = [
            'tbl_kpi_targets_supplier.id as id',
            'tblsuppliers.code as code_supplier',
            'tblsuppliers.company as company',
            'tblsuppliers_groups.name as list_name_group',
            'tbl_kpi_targets_supplier.SoBaoGiaNhan as SoBaoGiaNhan',
            'tbl_kpi_targets_supplier.BaoGiaDaDuyet as BaoGiaDaDuyet',
            'tbl_kpi_targets_supplier.BaoGiaChuaDuyet as BaoGiaChuaDuyet',
            'tbl_kpi_targets_supplier.SoDonHang as SoDonHang',
            'tbl_kpi_targets_supplier.GiaoHangDungHan as GiaoHangDungHan',
            'tbl_kpi_targets_supplier.GiaoHangTre as GiaoHangTre',
            'tbl_kpi_targets_supplier.SoLanLoiChatLuong as SoLanLoiChatLuong',
            'tbl_kpi_targets_supplier.SoLanComplain as SoLanComplain',
            'tbl_kpi_targets_supplier.MauLan1 as MauLan1',
            'tbl_kpi_targets_supplier.MauLan2 as MauLan2',
            'tbl_kpi_targets_supplier.DiemCong as DiemCong',
            'tbl_kpi_targets_supplier.DiemTru as DiemTru',
            'tbl_kpi_targets_supplier.TongDiem as TongDiem',
            "(
                CASE 
                    WHEN tblsuppliers.active = 1 THEN 'Có'
                    WHEN tblsuppliers.active = 0 THEN 'Không'
                END
            ) as name_status",
            'tbl_kpi_targets_supplier.HanhDongXuLy as HanhDongXuLy',
        ];

        $sIndexColumn = 'id';
        $sTable = 'tbl_kpi_targets_supplier';
        $join = [
            'JOIN tblsuppliers ON tblsuppliers.id = tbl_kpi_targets_supplier.id_supplier',
            'LEFT JOIN tblsuppliers_groups ON tblsuppliers_groups.id = tblsuppliers.groups_in',
        ];
        $where = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            '(
                SELECT 
                COUNT(*) 
                FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
            ) as SoDonHangTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
                AND delivery_date IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM tblimport 
                    WHERE tblimport.id_order = tblpurchase_order.id 
                    AND DATE(tblimport.date) <= DATE(tblpurchase_order.delivery_date)
                )
            ) as GiaoHangDungHanTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
                AND delivery_date IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM tblimport 
                    WHERE tblimport.id_order = tblpurchase_order.id 
                    AND DATE(tblimport.date) > DATE(tblpurchase_order.delivery_date)
                )
            ) as GiaoHangTreTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tblreturn_suppliers WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
            ) as SoLanLoiChatLuongTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tbl_suggest_evaluate WHERE object_id = tblsuppliers.id and object_type = "supplier"
                AND YEAR(date) = "' . $year . '" and status = 1
            ) as SoLanComplainTT',
        ]);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $listStatus = [
            1 => '<span class="text-info">Nhà cung cấp tốt</span>',
            2 => '<span class="text-primary">Bình thường</span>',
            3 => '<span class="text-warning">Cảnh báo</span>',
            4 => '<span class="text-danger">Cần xem xét thay thế</span>',
        ];
        foreach ($rResult as $aRow) {
            $DiemCong = 0;
            $DiemTru = 0;
            $row = [];
            $row[] = $aRow['id'];
            $row[] = $aRow['code_supplier'];
            $row[] = $aRow['company'];
            $row[] = $aRow['list_name_group'] ?? '';
            // $row[] = '<div class="text-center">' . ($aRow['SoBaoGiaNhan'] ?? '');
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data(0) . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['SoBaoGiaNhan'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data(0) . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['BaoGiaDaDuyet'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data(0) . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['BaoGiaChuaDuyet'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            // $row[] = '<div class="text-center">' . ($aRow['SoDonHang'] ?? '');
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data($aRow['SoDonHangTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['SoDonHang'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            // $row[] = '<div class="text-center">' . ($aRow['GiaoHangDungHan'] ?? '');
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data($aRow['GiaoHangDungHanTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['GiaoHangDungHan'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            $DiemCong += $aRow['GiaoHangDungHanTT'];
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data($aRow['GiaoHangTreTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['GiaoHangTre'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data($aRow['SoLanLoiChatLuongTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['SoLanLoiChatLuong'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="' . $year . '">' . number_format_data($aRow['SoLanComplainTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['SoLanComplain'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            if ($aRow['SoLanComplainTT'] == 1) {
                $DiemTru = 3;
            } else if ($aRow['SoLanComplainTT'] == 2) {
                $DiemTru = 5;
            } else if ($aRow['SoLanComplainTT'] > 2) {
                $DiemTru = 10;
            }
            $TongDiem = $DiemCong - $DiemTru;
            if ($TongDiem >= 80) {
                $status = 1;
            } elseif ($TongDiem >= 60) {
                $status = 2;
            } elseif ($TongDiem >= 40) {
                $status = 3;
            } else {
                $status = 4;
            }
            $row[] = '<div class="text-center">0<div>';
            $row[] = '<div class="text-center">0<div>';
            $row[] = '<div class="text-center">' . ($DiemCong ?? '') . '<div>';
            $row[] = '<div class="text-center">' . ($DiemTru ?? '') . '<div>';
            $row[] = '<div class="text-center">' . number_format_data($TongDiem) . '<div>';
            $row[] = '<div class="text-center">' . $listStatus[$status] . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['HanhDongXuLy'] ?? '') . '</div>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi_targets_supplier/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

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
            $row[] = $actions;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }

    public function delete($id = '')
    {
        $this->db->where('id', $id);
        $successDelete = $this->db->delete('tbl_kpi_targets_supplier');
        if (!empty($successDelete)) {
            echo json_encode([
                'result' => true,
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Xóa dữ liệu thành công'
            ]);
            die();
        }
        echo json_encode([
            'result' => false,
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Xóa dữ liệu không thành công'
        ]);
        die();
    }

    public function modal_excel_import($type = '1')
    {

        $data['name'] = 'KPI Nhà Cung Cấp';
        $data['fileTemplate'] = base_url('uploads/import_c/mau_import_kpi_nha_cung_cap.xlsx?vs=0.2');
        $data['title'] = _l('Import ' . $data['name'] . ' bằng File Excel');
        $data['type'] = $type;
        $this->load->view('admin/kpi_targets_supplier/import', $data);
    }

    public function excel_import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('S');
            $arraydata = array();
            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    //						if ((isset($value)) && $value != "") {
                    //							if ($col == 9) {
                    //								if (gettype($value) == 'double' || gettype($value) == 'int') {
                    //									$dateTime = PHPExcel_Shared_Date::ExcelToPHP($value);
                    //									$days = floor($dateTime / 86400);
                    //									$time = round((($dateTime / 86400) - $days) * 86400);
                    //									$hours = round($time / 3600);
                    //									$minutes = round($time / 60) - ($hours * 60);
                    //									$seconds = round($time) - ($hours * 3600) - ($minutes * 60);
                    //									$dateObj = date_create('1-Jan-1970+' . $days . ' days');
                    //									$value = $dateObj->setTime($hours, $minutes, $seconds);
                    //									$value = $value->format('Y-m-d H:i:s');
                    //								}
                    //							}
                    //						}
                    $arraydata[$row][$col] = $value;
                }
            }


            $keyCode = '';
            $list_data = [];




            foreach ($arraydata as $key => $row) {
                $list_data[] = [
                    'code' => !empty($row[1]) ? $row[1] : NULL,
                    'SoBaoGiaNhan' => !empty($row[4]) ? $row[4] : NULL,
                    'BaoGiaDaDuyet' => !empty($row[5]) ? $row[5] : NULL,
                    'BaoGiaChuaDuyet' => !empty($row[6]) ? $row[6] : NULL,
                    'SoDonHang' => !empty($row[7]) ? $row[7] : NULL,
                    'GiaoHangDungHan' => !empty($row[8]) ? $row[8] : NULL,
                    'GiaoHangTre' => !empty($row[9]) ? $row[9] : NULL,
                    'SoLanLoiChatLuong' => !empty($row[10]) ? $row[10] : NULL,
                    'SoLanComplain' => !empty($row[11]) ? $row[11] : NULL,
                    'MauLan1' => !empty($row[12]) ? $row[12] : NULL,
                    'MauLan2' => !empty($row[13]) ? $row[13] : NULL,
                    'DiemCong' => !empty($row[14]) ? $row[14] : NULL,
                    'DiemTru' => !empty($row[15]) ? $row[15] : NULL,
                    'TongDiem' => !empty($row[16]) ? $row[16] : NULL,
                    'HanhDongXuLy' => !empty($row[18]) ? $row[18] : NULL,
                ];
            }

            $dataUpdate = [];
            $dataInsert = [];
            foreach ($list_data as $key => $value) {
                $this->db->where('code', $value['code']);
                $ktSupllier = $this->db->get('tblsuppliers')->row();

                if (!empty($ktSupllier)) {
                    $value['id_supplier'] = $ktSupllier->id;
                } else {
                    continue;
                }

                $ktKPI = $this->db->get_where('tbl_kpi_targets_supplier', ['id_supplier' => $value['id_supplier']])->row();
                if (empty($ktKPI)) {
                    $dataInsert[] = [
                        'id_supplier' => $value['id_supplier'],
                        'SoBaoGiaNhan' => $value['SoBaoGiaNhan'],
                        'BaoGiaDaDuyet' => !empty($value['BaoGiaDaDuyet']) ? $value['BaoGiaDaDuyet'] : NULL,
                        'BaoGiaChuaDuyet' => $value['BaoGiaChuaDuyet'] ?? 0,
                        'SoDonHang' => $value['SoDonHang'] ?? 0,
                        'GiaoHangDungHan' => $value['GiaoHangDungHan'] ?? 0,
                        'GiaoHangTre' => $value['GiaoHangTre'] ?? 0,
                        'SoLanLoiChatLuong' => $value['SoLanLoiChatLuong'] ?? 0,
                        'SoLanComplain' => $value['SoLanComplain'] ?? 0,
                        'MauLan1' => $value['MauLan1'] ?? 0,
                        'MauLan2' => $value['MauLan2'] ?? 0,
                        'DiemCong' => $value['DiemCong'] ?? 0,
                        'DiemTru' => $value['DiemTru'] ?? 0,
                        'TongDiem' => $value['TongDiem'] ?? 0,
                        'HanhDongXuLy' => $value['HanhDongXuLy'] ?? 0,
                        'create_by' => get_staff_user_id(),
                    ];
                } else {
                    $dataUpdate[] = [
                        'id' => $ktKPI->id,
                        'id_supplier' => $value['id_supplier'],
                        'SoBaoGiaNhan' => $value['SoBaoGiaNhan'],
                        'BaoGiaDaDuyet' => !empty($value['BaoGiaDaDuyet']) ? $value['BaoGiaDaDuyet'] : NULL,
                        'BaoGiaChuaDuyet' => $value['BaoGiaChuaDuyet'] ?? 0,
                        'SoDonHang' => $value['SoDonHang'] ?? 0,
                        'GiaoHangDungHan' => $value['GiaoHangDungHan'] ?? 0,
                        'GiaoHangTre' => $value['GiaoHangTre'] ?? 0,
                        'SoLanLoiChatLuong' => $value['SoLanLoiChatLuong'] ?? 0,
                        'SoLanComplain' => $value['SoLanComplain'] ?? 0,
                        'MauLan1' => $value['MauLan1'] ?? 0,
                        'MauLan2' => $value['MauLan2'] ?? 0,
                        'DiemCong' => $value['DiemCong'] ?? 0,
                        'DiemTru' => $value['DiemTru'] ?? 0,
                        'TongDiem' => $value['TongDiem'] ?? 0,
                        'HanhDongXuLy' => $value['HanhDongXuLy'] ?? NULL,
                    ];
                }
            }

            $viewSuccess = [];
            if (!empty($dataUpdate)) {
                $this->db->update_batch('tbl_kpi_targets_supplier', $dataUpdate, 'id');
                if ($this->db->affected_rows() > 0) {
                    $viewSuccess[] = " Cập nhật " . $this->db->affected_rows() . " thành công ";
                }
            }

            if (!empty($dataInsert)) {
                $this->db->insert_batch('tbl_kpi_targets_supplier', $dataInsert);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows = $this->db->affected_rows();
                    $viewSuccess[] = " Thêm mới " . $affected_rows . " dữ liệu ";
                }
            }

            if (empty($viewSuccess)) {
                $viewSuccess[] = " Không có dữ liệu được thay đổi";
            }

            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => implode('Và ', $viewSuccess),
                ]
            );
            die();
        }

        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }

    public function export_excel()
    {
        $year = $this->input->post('year_search') ?? date('Y');
        $name = 'Mục tiêu KPI nhà cung cấp';
        $nameFile = 'muc_tieu_kpi_nha_cung_cap_nam_' . $year;

        ini_set('memory_limit', '3500M');

        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(23);
        $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(30);

        $numberRow = 1;

        $headers = [
            'STT',
            'Mã Nhà Cung Cấp',
            'Tên Nhà Cung Cấp',
            'Nhóm Nhà Cung Cấp',
            'Số báo giá nhận',
            'Số báo giá nhận ' . $year,
            'Báo giá đã duyệt',
            'Báo giá đã duyệt ' . $year,
            'Báo giá chưa duyệt',
            'Báo giá chưa duyệt ' . $year,
            'Số đơn hàng',
            'Số đơn hàng ' . $year,
            'Giao hàng đúng hạn',
            'Giao hàng đúng hạn ' . $year,
            'Giao hàng trễ',
            'Giao hàng trễ ' . $year,
            'Số lần lỗi chất lượng',
            'Số lần lỗi chất lượng ' . $year,
            'Số lần complain',
            'Số lần complain ' . $year,
            'Mẫu lần 1',
            'Mẫu lần 2',
            'Điểm cộng',
            'Điểm trừ',
            'Tổng điểm',
            'Trạng thái NCC',
            'Hành động xử lý',
        ];

        foreach ($headers as $key => $value) {
            $col = $cloumns_excel[$key];
            $objPHPExcel->getActiveSheet()
                ->SetCellValue($col . $numberRow, $value)
                ->getStyle($col . $numberRow)
                ->applyFromArray($style_excel['Background_header']);
        }

        $numberRow++;

        foreach ($headers as $key => $value) {
            $col = $cloumns_excel[$key];
            $objPHPExcel->getActiveSheet()
                ->SetCellValue($col . $numberRow, ($key + 1))
                ->getStyle($col . $numberRow)
                ->applyFromArray($style_excel['c_th']);
        }

        $numberRow++;

        $this->db->select([
            'tblsuppliers.code',
            'tblsuppliers.company',
            'tblsuppliers_groups.name as group_name',
            'tbl_kpi_targets_supplier.SoBaoGiaNhan',
            'tbl_kpi_targets_supplier.BaoGiaDaDuyet',
            'tbl_kpi_targets_supplier.BaoGiaChuaDuyet',
            'tbl_kpi_targets_supplier.SoDonHang',
            'tbl_kpi_targets_supplier.GiaoHangDungHan',
            'tbl_kpi_targets_supplier.GiaoHangTre',
            'tbl_kpi_targets_supplier.SoLanLoiChatLuong',
            'tbl_kpi_targets_supplier.SoLanComplain',
            'tbl_kpi_targets_supplier.MauLan1',
            'tbl_kpi_targets_supplier.MauLan2',
            'tbl_kpi_targets_supplier.DiemCong',
            'tbl_kpi_targets_supplier.DiemTru',
            'tbl_kpi_targets_supplier.TongDiem',
            'tbl_kpi_targets_supplier.HanhDongXuLy',
            '(
                SELECT 
                COUNT(*) 
                FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
            ) as SoDonHangTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
                AND delivery_date IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM tblimport 
                    WHERE tblimport.id_order = tblpurchase_order.id 
                    AND DATE(tblimport.date) <= DATE(tblpurchase_order.delivery_date)
                )
            ) as GiaoHangDungHanTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tblpurchase_order WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
                AND delivery_date IS NOT NULL
                AND EXISTS (
                    SELECT 1 FROM tblimport 
                    WHERE tblimport.id_order = tblpurchase_order.id 
                    AND DATE(tblimport.date) > DATE(tblpurchase_order.delivery_date)
                )
            ) as GiaoHangTreTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tblreturn_suppliers WHERE suppliers_id = tblsuppliers.id 
                AND YEAR(date) = "' . $year . '"
            ) as SoLanLoiChatLuongTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tbl_suggest_evaluate WHERE object_id = tblsuppliers.id and object_type = "supplier"
                AND YEAR(date) = "' . $year . '" and status = 1
            ) as SoLanComplainTT',
        ]);

        $this->db->from('tbl_kpi_targets_supplier');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_kpi_targets_supplier.id_supplier');
        $this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in', 'left');

        $data_result = $this->db->get()->result_array();

        $listStatus = [
            1 => 'Nhà cung cấp tốt',
            2 => 'Bình thường',
            3 => 'Cảnh báo',
            4 => 'Cần xem xét thay thế',
        ];

        if (!empty($data_result)) {

            foreach ($data_result as $key => $value) {

                $DiemCong = 0;
                $DiemTru = 0;

                $DiemCong += $value['GiaoHangDungHanTT'];

                if ($value['SoLanComplainTT'] == 1) {
                    $DiemTru = 3;
                } else if ($value['SoLanComplainTT'] == 2) {
                    $DiemTru = 5;
                } else if ($value['SoLanComplainTT'] > 2) {
                    $DiemTru = 10;
                }

                $TongDiem = $DiemCong - $DiemTru;
                if ($TongDiem >= 80) {
                    $status = 1;
                } elseif ($TongDiem >= 60) {
                    $status = 2;
                } elseif ($TongDiem >= 40) {
                    $status = 3;
                } else {
                    $status = 4;
                }

                $row_data = [
                    $key + 1,
                    $value['code'],
                    $value['company'],
                    $value['group_name'],

                    $value['SoBaoGiaNhan'],
                    0,

                    $value['BaoGiaDaDuyet'],
                    0,

                    $value['BaoGiaChuaDuyet'],
                    0,

                    $value['SoDonHang'],
                    $value['SoDonHangTT'],

                    $value['GiaoHangDungHan'],
                    $value['GiaoHangDungHanTT'],

                    $value['GiaoHangTre'],
                    $value['GiaoHangTreTT'],

                    $value['SoLanLoiChatLuong'],
                    $value['SoLanLoiChatLuongTT'],

                    $value['SoLanComplain'],
                    $value['SoLanComplainTT'],

                    0, // MauLan1
                    0, // MauLan2
                    $DiemCong,
                    $DiemTru,
                    $TongDiem,
                    $listStatus[$status],
                    $value['HanhDongXuLy'],
                ];

                foreach ($row_data as $i => $cell) {

                    $col = $cloumns_excel[$i];

                    $objPHPExcel->getActiveSheet()
                        ->SetCellValue($col . $numberRow, $cell)
                        ->getStyle($col . $numberRow)
                        ->applyFromArray($style_excel['BStyle_center']);
                }

                $numberRow++;
            }
        }

        $filename = $nameFile . '.xls';

        ob_start();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $writer->save('php://output');

        $xlsData = ob_get_contents();
        ob_end_clean();

        echo json_encode([
            'result' => 1,
            'filename' => $filename,
            'message' => 'Xuất dữ liệu thành công!',
            'file' => 'data:application/vnd.ms-excel;base64,' . base64_encode($xlsData)
        ]);

        die;
    }
}
