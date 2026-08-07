<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi_targets_clients extends AdminController
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
        $data['title'] = _l('Mục tiêu KPI khách hàng');
        $data['name'] = _l('Nội Quy');
        $data['type'] = 1;
        $this->load->view('admin/kpi_targets_clients/manage', $data);
    }

    public function table()
    {
        $year = $this->input->post("year_search");
        $tableGroupClient = "(SELECT 
                                GROUP_CONCAT(tblcustomers_groups.name) as list_name_group, tblcustomer_groups.customer_id
                            FROM tblcustomer_groups 
                            JOIN tblcustomers_groups ON tblcustomers_groups.id = tblcustomer_groups.groupid 
                            GROUP BY tblcustomer_groups.customer_id)";
        $aColumns = [
            'tbl_kpi_targets_clients.id as id',
            'tblclients.zcode as zcode',
            'tblclients.company as company',
            'g.list_name_group as list_name_group',
            'tbl_kpi_targets_clients.SoBaoGia as SoBaoGia',
            'tbl_kpi_targets_clients.BaoGiaDaDuyet as BaoGiaDaDuyet',
            'tbl_kpi_targets_clients.BaoGiaChuaDuyet as BaoGiaChuaDuyet',
            'tbl_kpi_targets_clients.DonHangCo as DonHangCo',
            'tbl_kpi_targets_clients.DonHangKhongCo as DonHangKhongCo',
            'tbl_kpi_targets_clients.PTMCoDon as PTMCoDon',
            'tbl_kpi_targets_clients.PTMKhongDon as PTMKhongDon',
            'tbl_kpi_targets_clients.SoComplain as SoComplain',
            'tbl_kpi_targets_clients.MauLan1 as MauLan1',
            'tbl_kpi_targets_clients.MauLan2 as MauLan2',
            'tbl_kpi_targets_clients.DiemCong as DiemCong',
            'tbl_kpi_targets_clients.DiemTru as DiemTru',
            'tbl_kpi_targets_clients.TongDiem as TongDiem',
            '"" as name_status',
            "(SELECT 
                CASE 
                    WHEN method = 1 THEN 'Trực tiếp'
                    WHEN method = 2 THEN 'Điện thoại'
                    WHEN method = 3 THEN 'Email'
                END
                FROM tblcoupon_support 
                WHERE tblcoupon_support.customer_id = tbl_kpi_targets_clients.id_client
                LIMIT 1
            ) as HanhDongChamSoc",
        ];

        $sIndexColumn = 'id';
        $sTable = 'tbl_kpi_targets_clients';
        $join = [
            'JOIN tblclients ON tblclients.userid = tbl_kpi_targets_clients.id_client',
            'LEFT JOIN ' . $tableGroupClient . ' g ON g.customer_id = tbl_kpi_targets_clients.id_client',
        ];
        $where = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            '(
                SELECT 
                COUNT(*) 
                FROM tbl_quotes WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '"
            ) as SoBaoGiaTT',
            '(
                SELECT COUNT(*) 
                FROM tbl_quotes 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND tbl_quotes.status = "approved"
            ) as BaoGiaDaDuyetTT',
            '(
                SELECT COUNT(*) 
                FROM tbl_quotes 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND tbl_quotes.status = "un_approved"
            ) as BaoGiaChuaDuyetTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tbl_orders 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND tbl_orders.status = "approved" 
                AND tbl_orders.type_orders != 13
            ) as DonHangCoTT',
            '(
                SELECT 
                COUNT(*) 
                FROM tbl_orders 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND tbl_orders.status = "un_approved" 
                AND tbl_orders.type_orders != 13
            ) as DonHangKhongCoTT',
            '(
                SELECT 
                COUNT(*) FROM tbl_orders 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND tbl_orders.type_orders = 13
                AND tbl_orders.status = "approved"
            ) as PTMCoDonTT',
            '(
                SELECT COUNT(*) 
                FROM tbl_orders 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND tbl_orders.type_orders = 13
                AND tbl_orders.status = "un_approved"
            ) as PTMKhongDonTT',
            '(
                SELECT COUNT(*) 
                FROM tblproduction_report 
                JOIN tbl_orders ON tbl_orders.id = tblproduction_report.id_orders 
                WHERE tbl_orders.customer_id = tblclients.userid 
                AND YEAR(tblproduction_report.date) = "' . $year . '" 
            ) as SoComplainTT',
            'EXISTS (
                SELECT 1 
                FROM tbl_orders 
                WHERE customer_id = tblclients.userid 
                AND YEAR(date) = "' . $year . '" 
                AND quotes_id IS NOT NULL
                GROUP BY quotes_id
                HAVING COUNT(*) >= 2
            ) as MauLan2TT'
        ]);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $listStatus = [
            1 => '<span class="text-info">Khách Tốt</span>',
            2 => '<span class="text-primary">Bình Thường</span>',
            3 => '<span class="text-warning">Cảnh Báo</span>',
            4 => '<span class="text-danger">Nguy Cơ Mất Khách</span>',
        ];
        foreach ($rResult as $aRow) {
            $row = [];
            $DiemCong = 0;
            $DiemTru = 0;
            $row[] = $aRow['id'];
            $row[] = $aRow['zcode'];
            $row[] = $aRow['company'];
            $row[] = $aRow['list_name_group'] ?? '';
            $row[] = '<div class="text-center">
                            <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['SoBaoGiaTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['SoBaoGia'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['BaoGiaDaDuyetTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['BaoGiaDaDuyet'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';


//            $DiemCong += $aRow['BaoGiaDaDuyetTT'] * 2;


            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['BaoGiaChuaDuyetTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['BaoGiaChuaDuyet'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';

//            $DiemCong += $aRow['BaoGiaDaDuyetTT'];

            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['DonHangCoTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['DonHangCo'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';

            $DiemCong += $aRow['DonHangCoTT'];

            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['DonHangKhongCoTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['DonHangKhongCo'] ?? '') . '</span>
                            </div>
                        </div>
                     </div>';
            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['PTMCoDonTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['PTMCoDon'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';


            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['PTMKhongDonTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['PTMKhongDon'] ?? '') . '</span>
                            </div>
                        </div>
                     </div>';

            $row[] = '<div class="text-center">
                        <div class="fraction-group">
                            <span class="fraction-main" title="'.$year.'">' . number_format_data($aRow['SoComplainTT'] ?? '') . '</span>
                            <span class="fraction-separator"></span>
                            <div class="fraction-sub">
                                <span class="fraction-top">' . number_format_data($aRow['SoComplain'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>';
            if ($aRow['SoComplainTT'] == 1) {
                $DiemTru = 3;
            } else if ($aRow['SoComplainTT'] == 2) {
                $DiemTru = 5;
            } else if ($aRow['SoComplainTT'] > 2) {
                $DiemTru = (10 * ($aRow['SoComplainTT'] - 2) ) + 3 + 5;
            }

            if (!empty($aRow['PTMCoDonTT']) && empty($aRow['MauLan2TT'])) {
                $row[] = '<div class="text-center bg-warning td-warning"> </div>';
            } else {
                $row[] = '<div class="text-center"> </div>';
            }

            if (!empty($aRow['MauLan2TT'])) {
                $row[] = '<div class="text-center bg-danger td-danger"></div>';
            } else {
                $row[] = '<div class="text-center"> </div>';
            }

            $row[] = '<div class="text-center">' . number_format_data($DiemCong) . '</div>';
//
//
//            $row[] = '<div class="fraction-group">
//                            <span class="fraction-main">'.($aRow['DiemCong'] ?? '').'</span>
//                            <span class="fraction-separator"></span>
//                            <div class="fraction-sub">
//                                <span class="fraction-top">'.($aRow['DiemCongTT'] ?? '').'</span>
//                            </div>
//                        </div>';

            $TongDiem = $DiemCong - $DiemTru;
            $row[] = '<div class="text-center">' . ($DiemTru) . '</div>';
            $row[] = '<div class="text-center">' . number_format_data($TongDiem) . '</div>';

            if ($TongDiem >= 80) {
                $status = 1;
            } elseif ($TongDiem >= 60) {
                $status = 2;
            } elseif ($TongDiem >= 40) {
                $status = 3;
            } else {
                $status = 4;
            }

            $row[] = '<div class="text-center">' . $listStatus[$status] . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['HanhDongChamSoc'] ?? '') . '</div>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/kpi_targets_clients/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $successDelete = $this->db->delete('tbl_kpi_targets_clients');
        if (!empty($successDelete)) {
            echo json_encode([
                'result' => 1,
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Xóa dữ liệu thành công'
            ]);
            die();
        }
        echo json_encode([
            'result' => 0,
            'success' => false,
            'alert_type' => 'danger',
            'message' => 'Xóa dữ liệu không thành công'
        ]);
        die();
    }

    public function modal_excel_import($type = '1')
    {

        $data['name'] = 'KPI khách hàng';
        $data['fileTemplate'] = base_url('uploads/import_c/mau_import_kpi_khach_hang.xlsx?vs=0.3');
        $data['title'] = _l('Import ' . $data['name'] . ' bằng File Excel');
        $data['type'] = $type;
        $this->load->view('admin/kpi_targets_clients/import', $data);
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
                    'zcode' => !empty($row[1]) ? $row[1] : NULL,
                    'SoBaoGia' => !empty($row[4]) ? $row[4] : NULL,
                    'BaoGiaDaDuyet' => !empty($row[5]) ? $row[5] : NULL,
                    'BaoGiaChuaDuyet' => !empty($row[6]) ? $row[6] : NULL,
                    'DonHangCo' => !empty($row[7]) ? $row[7] : NULL,
                    'DonHangKhongCo' => !empty($row[8]) ? $row[8] : NULL,
                    'PTMCoDon' => !empty($row[9]) ? $row[9] : NULL,
                    'PTMKhongDon' => !empty($row[10]) ? $row[10] : NULL,
                    'SoComplain' => !empty($row[11]) ? $row[11] : NULL,
                    'MauLan1' => !empty($row[12]) ? $row[12] : NULL,
                    'MauLan2' => !empty($row[13]) ? $row[13] : NULL,
                    'DiemCong' => !empty($row[14]) ? $row[14] : NULL,
                    'DiemTru' => !empty($row[15]) ? $row[15] : NULL,
                    'TongDiem' => !empty($row[16]) ? $row[16] : NULL,
                ];
            }

            $dataUpdate = [];
            $dataInsert = [];
            foreach ($list_data as $key => $value) {
                $this->db->where('zcode', $value['zcode']);
                $ktClient = $this->db->get('tblclients')->row();
                if (!empty($ktClient)) {
                    $value['id_client'] = $ktClient->userid;
                } else {
                    continue;
                }
                $ktKPI = $this->db->get_where('tbl_kpi_targets_clients', ['id_client' => $value['id_client']])->row();
                if (empty($ktKPI)) {
                    $dataInsert[] = [
                        'id_client' => $value['id_client'],
                        'SoBaoGia' => $value['SoBaoGia'],
                        'BaoGiaDaDuyet' => !empty($value['BaoGiaDaDuyet']) ? $value['BaoGiaDaDuyet'] : NULL,
                        'BaoGiaChuaDuyet' => $value['BaoGiaChuaDuyet'] ?? 0,
                        'DonHangCo' => $value['DonHangCo'] ?? 0,
                        'DonHangKhongCo' => $value['DonHangKhongCo'] ?? 0,
                        'PTMCoDon' => $value['PTMCoDon'] ?? 0,
                        'PTMKhongDon' => $value['PTMKhongDon'] ?? 0,
                        'SoComplain' => $value['SoComplain'] ?? 0,
                        'MauLan1' => $value['MauLan1'] ?? 0,
                        'MauLan2' => $value['MauLan2'] ?? 0,
                        'DiemCong' => $value['DiemCong'] ?? 0,
                        'DiemTru' => $value['DiemTru'] ?? 0,
                        'TongDiem' => $value['TongDiem'] ?? 0,
                        'create_by' => get_staff_user_id(),
                    ];
                } else {
                    $dataUpdate[] = [
                        'id' => $ktKPI->id,
                        'SoBaoGia' => $value['SoBaoGia'],
                        'BaoGiaDaDuyet' => !empty($value['BaoGiaDaDuyet']) ? $value['BaoGiaDaDuyet'] : NULL,
                        'BaoGiaChuaDuyet' => $value['BaoGiaChuaDuyet'] ?? 0,
                        'DonHangCo' => $value['DonHangCo'] ?? 0,
                        'DonHangKhongCo' => $value['DonHangKhongCo'] ?? 0,
                        'PTMCoDon' => $value['PTMCoDon'] ?? 0,
                        'PTMKhongDon' => $value['PTMKhongDon'] ?? 0,
                        'SoComplain' => $value['SoComplain'] ?? 0,
                        'MauLan1' => $value['MauLan1'] ?? 0,
                        'MauLan2' => $value['MauLan2'] ?? 0,
                        'DiemCong' => $value['DiemCong'] ?? 0,
                        'DiemTru' => $value['DiemTru'] ?? 0,
                        'TongDiem' => $value['TongDiem'] ?? 0,
                    ];
                }
            }

            $viewSuccess = [];
            if (!empty($dataUpdate)) {
                $this->db->update_batch('tbl_kpi_targets_clients', $dataUpdate, 'id');
                if ($this->db->affected_rows() > 0) {
                    $viewSuccess[] = " Cập nhật " . $this->db->affected_rows() . " thành công ";
                }
            }

            if (!empty($dataInsert)) {
                $this->db->insert_batch('tbl_kpi_targets_clients', $dataInsert);
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

    public function export_excel2()
    {
        $name = 'Mục tiêu KPI khách hàng';
        $nameFile = 'muc_tieu_kpi_khach_hang';

        ini_set('memory_limit', '3500M');

        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $style_excel = style_excel();
        $cloumns_excel = cloumns_excel();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(8);   // STT
        $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(18);  // Mã khách hàng
        $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(35);  // Tên khách hàng
        $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(30);  // Nhóm khách hàng
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);  // Số báo giá
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(18);  // Báo giá đã duyệt
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);  // Báo giá chưa duyệt
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);  // Đơn hàng có
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(18);  // Đơn hàng không có
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);  // PTM có đơn
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(22);  // PTM không đơn
        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(18);  // Số complain
        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(12);  // Mẫu lần 1
        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(12);  // Mẫu lần 2
        $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(12);  // Điểm cộng
        $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(12);  // Điểm trừ
        $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(12);  // Tổng điểm
        $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(22);  // Trạng thái khách
        $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(22);  // Trạng thái khách


        $numberRow = 1;

        $headers = [
            'STT',
            'Mã khách hàng',
            'Tên khách hàng',
            'Nhóm khách hàng',
            'Số báo giá',
            'Báo giá đã duyệt',
            'Báo giá chưa duyệt',
            'Đơn hàng có',
            'Đơn hàng không có',
            'PTM có đơn',
            'PTM không đơn',
            'Số complain',
            'Mẫu lần 1',
            'Mẫu lần 2',
            'Điểm cộng',
            'Điểm trừ',
            'Tổng điểm',
            'Trạng thái khách',
            'Hành Động Chăm Sóc',
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

        $tableGroupClient = "(SELECT 
            GROUP_CONCAT(tblcustomers_groups.name) as list_name_group,
            tblcustomer_groups.customer_id
        FROM tblcustomer_groups 
        JOIN tblcustomers_groups 
        ON tblcustomers_groups.id = tblcustomer_groups.groupid
        GROUP BY tblcustomer_groups.customer_id)";

        $this->db->select([
            'tblclients.zcode',
            'tblclients.company',
            'g.list_name_group',
            'tbl_kpi_targets_clients.SoBaoGia',
            'tbl_kpi_targets_clients.BaoGiaDaDuyet',
            'tbl_kpi_targets_clients.BaoGiaChuaDuyet',
            'tbl_kpi_targets_clients.DonHangCo',
            'tbl_kpi_targets_clients.DonHangKhongCo',
            'tbl_kpi_targets_clients.PTMCoDon',
            'tbl_kpi_targets_clients.PTMKhongDon',
            'tbl_kpi_targets_clients.SoComplain',
            'tbl_kpi_targets_clients.MauLan1',
            'tbl_kpi_targets_clients.MauLan2',
            'tbl_kpi_targets_clients.DiemCong',
            'tbl_kpi_targets_clients.DiemTru',
            'tbl_kpi_targets_clients.TongDiem',
            'tblstatus_client.name as name_status',
            "(SELECT 
                CASE 
                    WHEN method = 1 THEN 'Trực tiếp'
                    WHEN method = 2 THEN 'Điện thoại'
                    WHEN method = 3 THEN 'Email'
                END
                FROM tblcoupon_support 
                WHERE tblcoupon_support.customer_id = tbl_kpi_targets_clients.id_client
                LIMIT 1
            ) as HanhDongChamSoc",
        ]);

        $this->db->from('tbl_kpi_targets_clients');
        $this->db->join('tblclients', 'tblclients.userid = tbl_kpi_targets_clients.id_client');
        $this->db->join('tblstatus_client', 'tblstatus_client.id = tblclients.status_clients', 'left');
        $this->db->join($tableGroupClient . ' g', 'g.customer_id = tbl_kpi_targets_clients.id_client', 'left');

        $data_result = $this->db->get()->result_array();

        if (!empty($data_result)) {

            foreach ($data_result as $key => $value) {
                $i = 0;
                $row_data = [
                    $key + 1,
                    $value['zcode'],
                    $value['company'],
                    $value['list_name_group'],
                    $value['SoBaoGia'],
                    $value['BaoGiaDaDuyet'],
                    $value['BaoGiaChuaDuyet'],
                    $value['DonHangCo'],
                    $value['DonHangKhongCo'],
                    $value['PTMCoDon'],
                    $value['PTMKhongDon'],
                    $value['SoComplain'],
                    $value['MauLan1'],
                    $value['MauLan2'],
                    $value['DiemCong'],
                    $value['DiemTru'],
                    $value['TongDiem'],
                    $value['name_status'],
                    $value['HanhDongChamSoc'],
                ];

                foreach ($row_data as $cell) {

                    $col = $cloumns_excel[$i];

                    $objPHPExcel->getActiveSheet()
                        ->SetCellValue($col . $numberRow, $cell)
                        ->getStyle($col . $numberRow)
                        ->applyFromArray($style_excel['BStyle_left']);

                    $i++;
                }

                $numberRow++;
            }
        }

        $filename = $nameFile . '.xls';
//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="' . $filename . '"');
//        header('Cache-Control: max-age=0');
//        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save('php://output');


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

    public function export_excel()
    {
        $year = $this->input->post('year_search') ?? date('Y');
        $nameFile = 'KPI Khách Hàng Năm ' . $year;

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
        $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("N")->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setWidth(22);
        $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setWidth(22);

        $headers = [
            'STT',
            'Mã khách hàng',
            'Tên khách hàng',
            'Nhóm khách hàng',

            'Số báo giá',
            'Số báo giá ' . $year,

            'Báo giá đã duyệt',
            'Báo giá đã duyệt ' . $year,

            'Báo giá chưa duyệt',
            'Báo giá chưa duyệt ' . $year,

            'Đơn hàng có',
            'Đơn hàng có ' . $year,

            'Đơn hàng không có',
            'Đơn hàng không có ' . $year,

            'PTM có đơn',
            'PTM có đơn ' . $year,

            'PTM không đơn',
            'PTM không đơn ' . $year,

            'Số complain',
            'Số complain ' . $year,

            'Mẫu lần 1',
            'Mẫu lần 2',

            'Điểm cộng',
            'Điểm trừ',
            'Tổng điểm',

            'Trạng thái khách',
            'Hành động chăm sóc'
        ];

        $rowIndex = 1;

        foreach ($headers as $key => $value) {

            $col = $cloumns_excel[$key];

            $objPHPExcel->getActiveSheet()
                ->SetCellValue($col . $rowIndex, $value)
                ->getStyle($col . $rowIndex)
                ->applyFromArray($style_excel['c_th']);
        }

        $rowIndex++;

        foreach ($headers as $key => $value) {

            $col = $cloumns_excel[$key];

            $objPHPExcel->getActiveSheet()
                ->SetCellValue($col . $rowIndex, ($key + 1))
                ->getStyle($col . $rowIndex)
                ->applyFromArray($style_excel['Background_header']);
        }

        $rowIndex++;

        $tableGroupClient = "(SELECT 
        GROUP_CONCAT(tblcustomers_groups.name) as list_name_group,
        tblcustomer_groups.customer_id
    FROM tblcustomer_groups
    JOIN tblcustomers_groups 
    ON tblcustomers_groups.id = tblcustomer_groups.groupid
    GROUP BY tblcustomer_groups.customer_id)";

        $this->db->select([
            'tblclients.userid',
            'tblclients.zcode',
            'tblclients.company',
            'g.list_name_group',

            'tbl_kpi_targets_clients.SoBaoGia',
            'tbl_kpi_targets_clients.BaoGiaDaDuyet',
            'tbl_kpi_targets_clients.BaoGiaChuaDuyet',
            'tbl_kpi_targets_clients.DonHangCo',
            'tbl_kpi_targets_clients.DonHangKhongCo',
            'tbl_kpi_targets_clients.PTMCoDon',
            'tbl_kpi_targets_clients.PTMKhongDon',
            'tbl_kpi_targets_clients.SoComplain',

            "(SELECT COUNT(*) FROM tbl_quotes 
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year') as SoBaoGiaTT",

            "(SELECT COUNT(*) FROM tbl_quotes
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year'
        AND status='approved') as BaoGiaDaDuyetTT",

            "(SELECT COUNT(*) FROM tbl_quotes
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year'
        AND status='un_approved') as BaoGiaChuaDuyetTT",

            "(SELECT COUNT(*) FROM tbl_orders
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year'
        AND status='approved'
        AND type_orders!=13) as DonHangCoTT",

            "(SELECT COUNT(*) FROM tbl_orders
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year'
        AND status='un_approved'
        AND type_orders!=13) as DonHangKhongCoTT",

            "(SELECT COUNT(*) FROM tbl_orders
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year'
        AND type_orders=13
        AND status='approved') as PTMCoDonTT",

            "(SELECT COUNT(*) FROM tbl_orders
        WHERE customer_id=tblclients.userid
        AND YEAR(date)='$year'
        AND type_orders=13
        AND status='un_approved') as PTMKhongDonTT",

            "(SELECT COUNT(*) FROM tblproduction_report
        JOIN tbl_orders ON tbl_orders.id=tblproduction_report.id_orders
        WHERE tbl_orders.customer_id=tblclients.userid
        AND YEAR(tblproduction_report.date)='$year') as SoComplainTT",

            "EXISTS(
            SELECT 1
            FROM tbl_orders
            WHERE customer_id=tblclients.userid
            AND YEAR(date)='$year'
            AND quotes_id IS NOT NULL
            GROUP BY quotes_id
            HAVING COUNT(*)>=2
        ) as MauLan2TT",

            "(SELECT 
            CASE 
                WHEN method = 1 THEN 'Trực tiếp'
                WHEN method = 2 THEN 'Điện thoại'
                WHEN method = 3 THEN 'Email'
            END
        FROM tblcoupon_support
        WHERE tblcoupon_support.customer_id = tblclients.userid
        LIMIT 1) as HanhDongChamSoc"
        ]);

        $this->db->from('tbl_kpi_targets_clients');
        $this->db->join('tblclients', 'tblclients.userid=tbl_kpi_targets_clients.id_client');
        $this->db->join($tableGroupClient . ' g', 'g.customer_id=tbl_kpi_targets_clients.id_client', 'left');
        $this->db->order_by('tbl_kpi_targets_clients.id', 'desc');
        $data = $this->db->get()->result_array();

        $listStatus = [
            1 => 'Khách Tốt',
            2 => 'Bình Thường',
            3 => 'Cảnh Báo',
            4 => 'Nguy Cơ Mất Khách'
        ];

        foreach ($data as $key => $value) {

            $DiemCong = 0;
            $DiemTru = 0;

            $DiemCong += $value['DonHangCoTT'];

            if ($value['SoComplainTT'] == 1) {
                $DiemTru = 3;
            } elseif ($value['SoComplainTT'] == 2) {
                $DiemTru = 5;
            } elseif ($value['SoComplainTT'] > 2) {
                $DiemTru = (10 * ($value['SoComplainTT'] - 2) ) + 3 + 5;
            }

            $MauLan1 = '';
            $MauLan2 = '';

            if (!empty($value['PTMCoDonTT']) && empty($value['MauLan2TT'])) {
                $MauLan1 = 1;
            }

            if (!empty($value['MauLan2TT'])) {
                $MauLan2 = 1;
                $MauLan1 = 0;
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

            $rowData = [
                $key + 1,
                $value['zcode'],
                $value['company'],
                $value['list_name_group'],

                $value['SoBaoGia'],
                $value['SoBaoGiaTT'],

                $value['BaoGiaDaDuyet'],
                $value['BaoGiaDaDuyetTT'],

                $value['BaoGiaChuaDuyet'],
                $value['BaoGiaChuaDuyetTT'],

                $value['DonHangCo'],
                $value['DonHangCoTT'],

                $value['DonHangKhongCo'],
                $value['DonHangKhongCoTT'],

                $value['PTMCoDon'],
                $value['PTMCoDonTT'],

                $value['PTMKhongDon'],
                $value['PTMKhongDonTT'],

                $value['SoComplain'],
                $value['SoComplainTT'],

                $MauLan1,
                $MauLan2,

                $DiemCong,
                $DiemTru,
                $TongDiem,

                $listStatus[$status],
                $value['HanhDongChamSoc']
            ];

            $i = 0;

            foreach ($rowData as $keyCell => $cell) {

                $col = $cloumns_excel[$i];

                $objPHPExcel->getActiveSheet()
                    ->SetCellValue($col . $rowIndex, $cell)
                    ->getStyle($col . $rowIndex)
                    ->applyFromArray($style_excel['BStyle_center']);
                    if($keyCell == 20 && $MauLan1 == 1) {
                        $styleMau1 = $style_excel['BStyle_center'];
                        $styleMau1['fill'] = array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'ffff00'),
                        );
                        $objPHPExcel->getActiveSheet() ->SetCellValue($col . $rowIndex, '')
                            ->getStyle($col . $rowIndex)
                            ->applyFromArray($styleMau1);
                    }
                    if($keyCell == 21 && $MauLan2 == 1) {
                        $styleMau2 = $style_excel['BStyle_center'];
                        $styleMau2['fill'] = array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'ff0000'),
                        );
                        $objPHPExcel->getActiveSheet() ->SetCellValue($col . $rowIndex, '')
                            ->getStyle($col . $rowIndex)
                            ->applyFromArray($styleMau2);
                    }
                $i++;
            }

            $rowIndex++;
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
            'file' => 'data:application/vnd.ms-excel;base64,' . base64_encode($xlsData)
        ]);

        die;
    }
}
