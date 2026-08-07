<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports_leave_day extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();

        $this->ci = &get_instance();
        $this->preViewSyntheticTask = true;
    }
    public function index()
    {
        if (!$this->preViewSyntheticTask) {
            access_denied();
        }
        $data = [];
        $title = lang('Báo Cáo Tổng Hợp Phép Năm');
        $data['title'] = $title;
        $data['staff'] = getPersonDeparmentdt(0);

        $this->load->view('admin/reports_leave_day/manage', $data);
    }
    public function loadPaidHolidayFollows()
    {
        $data = [];
        $year_search = $this->input->get('year_search');
        $staff_search = $this->input->get('staff_search');
        $year_search_old = $year_search - 1;

        $tHead = '';
        $html = '';
        $tfoot = '';
        $is_admin = is_admin();
        $arrIDStaff = employee_manage_staff();
        $arrPaidHoliday = [];

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $this->db->select('
            tblstaff.staffid as id,
            tblstaff.profile_image as profile_image,
            tblstaff.code as code,
            tblstaff.day_in as day_in,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            coalesce(tb_department.name_department,"Khác") as name_department,
            tblroles.name as name_role,
            tbl_setup_paid_holiday_staff.number_day_old as number_day_old,
            tbl_setup_paid_holiday_staff.number_day_now as number_day_now,
            tbl_setup_paid_holiday_staff.number_day as number_day,
            tbl_setup_paid_holiday_staff.id_setup_paid_holiday as id_setup_paid_holiday,
            tbl_setup_paid_holiday.year as year,
        ');
        $this->db->from('tblstaff');
        $this->db->join(
            'tbl_setup_paid_holiday_staff',
            'tbl_setup_paid_holiday_staff.staff_id = tblstaff.staffid',
            'inner'
        );
        $this->db->join(
            'tbl_setup_paid_holiday',
            'tbl_setup_paid_holiday.id = tbl_setup_paid_holiday_staff.id_setup_paid_holiday',
            'inner'
        );
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');

        if (!empty($year_search)) {
            $this->db->where('tbl_setup_paid_holiday.year', $year_search);
        }
        if (!empty($staff_search)) {
            $this->db->where('tblstaff.staffid IN (' . implode(',', $staff_search) . ')');
        }
        $this->db->where('tblstaff.active', 1);
        $arrPaidHoliday = $this->db->get()->result_array();


        $tHead = '<tr>
            <th class="text-center" rowspan="2" style="width: 15px;">' . lang('STT') . '</th>
            <th class="text-center" rowspan="2" style="width: 15px;">' . lang('Avatar') . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('MSNV') . '</th>
            <th class="text-center" rowspan="2" style="width: 120px;">' . lang('Họ & Tên') . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('Ngày Vào Làm') . '</th>
            <th class="text-center" rowspan="2" style="width: 20px;">' . lang('Phép Năm ') . $year_search_old . '</th>
            <th class="text-center" rowspan="2" style="width: 20px;">' . lang('Phép Năm ') . $year_search . '</th>
            <th class="text-center" colspan="12" style="width: 50px;">' . lang('Phép Năm ') . $year_search . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('Tổng Phép Đã Nghĩ') . '</th>
            <th class="text-center" rowspan="2" style="width: 80px;">' . lang('Ngày Phép Còn Lại') . '</th>
        ';

        $thMonth = '';
        $hmtlNew = '';
        foreach (getMonth() as $key => $value) {
            if (empty($key)) {
                continue;
            }
            $thMonth .= ' <th class="text-center" style="width: 60px;">' . $value . '</th>';
            $hmtlNew .= ' <td class="text-center" style="width: 60px;"></td>';
        }
        $tHead .= '<tr>
             ' . $thMonth . '
        </tr>';

        $checkExist = '';
        if (!empty($arrPaidHoliday)) {
            foreach ($arrPaidHoliday as $key => $value) {
                $staff_id = $value['id'];
                $html .= '<tr>';
                $html .= '<td class="text-center">' . (++$key) . '</td>';
                $html .= '<td>' . staff_profile_image(
                    $value['id'],
                    [
                        'staff-profile-image-small',
                    ]
                ) . '</td>';
                $html .= '<td>' . ($value['code']) . '</td>';
                $html .= '<td>' . ($value['fullname']) . '</td>';
                $html .= '<td>' . (!empty($value['day_in']) ? _dhau($value['day_in']) : '') . '</td>';
                $html .= '<td class="text-center" style="color: red">' . ($value['number_day_old'] > 0 ? $value['number_day_old'] : '') . '</td>';
                $html .= '<td class="text-center" style="color: red">' . ($value['number_day_now']) . '</td>';
                $totalPaid = 0;
                foreach (getMonth() as $kk => $vv) {
                    if (empty($kk)) {
                        continue;
                    }
                    $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                    $this->db->from('tbl_paid_holiday_leave_detail');
                    $this->db->join(
                        'tbl_paid_holiday_leave',
                        'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id'
                    );
                    $this->db->join(
                        'tbl_paid_holiday_leave_detail_month',
                        'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id'
                    );
                    $this->db->join(
                        'tbl_timekeeping_detail',
                        'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id'
                    );
                    $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                    $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                    $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                    $this->db->where('tbl_timekeeping_detail.type', 'AL');
                    $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                    $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $vv AND tbl_paid_holiday_leave_detail_month.month <= $vv");
                    $number_date = $this->db->get()->row_array();

                    $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                    $this->db->from('tbl_paid_holiday_leave_detail');
                    $this->db->join(
                        'tbl_paid_holiday_leave',
                        'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id'
                    );
                    $this->db->join(
                        'tbl_paid_holiday_leave_detail_month',
                        'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id'
                    );
                    $this->db->join(
                        'tbl_timekeeping_detail',
                        'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id'
                    );
                    $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                    $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                    $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                    $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                    $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                    $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $vv AND tbl_paid_holiday_leave_detail_month.month <= $vv");
                    $number_date_new = $this->db->get()->row_array();

                    $html .= ' <td class="text-center" style="width: 60px;">' . (($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) > 0 ? ($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) : '') . '</td>';
                    $totalPaid += !empty(($number_date['number_date'] + ($number_date_new['number_date'] * 0.5))) ? ($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) : 0;
                }
                $totalOld = $value['number_day'] - $totalPaid;
                $html .= '<td class="text-center">' . ($totalPaid > 0 ? ($totalPaid) : '') . '</td>';
                $html .= '<td class="text-center">' . ($totalOld > 0 ? ($totalOld) : '') . '</td>';
                $html .= '</tr>';
            }
        }


        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $this->load->view('admin/paid_holidays/load_view_paid_holidays_follows', $data);
    }
    public function exportExcelOrders()
    {
        $columsExcel = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD',
            'AE',
            'AF',
            'AG',
            'AH',
            'AI',
            'AJ',
            'AK',
            'AL',
            'AM',
            'AN',
            'AO',
            'AP',
            'AQ',
            'AR',
            'AS',
            'AT',
            'AU',
            'AV',
            'AW',
            'AX',
            'AY',
            'AZ',
            'BA',
            'BB',
            'BC',
            'BD',
            'BE',
            'BF',
            'BG',
            'BH',
            'BI',
            'BJ',
            'BK',
            'BL',
            'BM',
            'BN',
            'BO',
            'BP',
            'BQ',
            'BR',
            'BS',
            'BT',
            'BU',
            'BV',
            'BW',
            'BX',
            'BY',
            'BZ',
            'CA',
            'CB',
            'CC',
            'CD',
            'CE',
            'CF',
            'CG',
            'CH',
            'CI',
            'CJ',
            'CK',
            'CL',
            'CM',
            'CN',
            'CO',
            'CP',
            'CQ',
            'CR',
            'CS',
            'CT',
            'CU',
            'CV',
            'CW',
            'CX',
            'CY',
            'CZ',
            'DA',
            'DB',
            'DC',
            'DD',
            'DE',
            'DF',
            'DG',
            'DH',
            'DI',
            'DJ',
            'DK',
            'DL',
            'DM',
            'DN',
            'DO',
            'DP',
            'DQ',
            'DR',
            'DS',
            'DT',
            'DU',
            'DV',
            'DW',
            'DX',
            'DY',
            'DZ'
        ];
        if ($this->input->post()) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/bao_cao_tong_hop_phep_nam.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $year_search = $this->input->post('year_search');
            $staff_search = $this->input->post('staff_search');
            $row = 3;
            $dem = 0;
            $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

            $this->db->select('
            tblstaff.staffid as id,
            tblstaff.profile_image as profile_image,
            tblstaff.code as code,
            tblstaff.day_in as day_in,
            CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname,
            coalesce(tb_department.name_department,"Khác") as name_department,
            tblroles.name as name_role,
            tbl_setup_paid_holiday_staff.number_day_old as number_day_old,
            tbl_setup_paid_holiday_staff.number_day_now as number_day_now,
            tbl_setup_paid_holiday_staff.number_day as number_day,
            tbl_setup_paid_holiday_staff.id_setup_paid_holiday as id_setup_paid_holiday,
            tbl_setup_paid_holiday.year as year,
        ');
            $this->db->from('tblstaff');
            $this->db->join(
                'tbl_setup_paid_holiday_staff',
                'tbl_setup_paid_holiday_staff.staff_id = tblstaff.staffid',
                'inner'
            );
            $this->db->join(
                'tbl_setup_paid_holiday',
                'tbl_setup_paid_holiday.id = tbl_setup_paid_holiday_staff.id_setup_paid_holiday',
                'inner'
            );
            $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');

            if (!empty($year_search)) {
                $this->db->where('tbl_setup_paid_holiday.year', $year_search);
            }
            if (!empty($staff_search)) {
                $this->db->where('tblstaff.staffid IN (' . implode(',', $staff_search) . ')');
            }
            $this->db->where('tblstaff.active', 1);
            $items = $this->db->get()->result_array();
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $staff_id = $value['id'];
                    $row++;
                    $dem++;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[0] . $row, $dem, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['code'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, $value['fullname'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, _d($value['day_in']), PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, $value['number_day_old'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, $value['number_day_now'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $totalPaid = 0;
                    $rowsa = 5;
                    foreach (getMonth() as $kk => $vv) {
                        if (empty($kk)) {
                            continue;
                        }
                        $rowsa++;
                        $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                        $this->db->from('tbl_paid_holiday_leave_detail');
                        $this->db->join(
                            'tbl_paid_holiday_leave',
                            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id'
                        );
                        $this->db->join(
                            'tbl_paid_holiday_leave_detail_month',
                            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id'
                        );
                        $this->db->join(
                            'tbl_timekeeping_detail',
                            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id'
                        );
                        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                        $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                        $this->db->where('tbl_timekeeping_detail.type', 'AL');
                        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $vv AND tbl_paid_holiday_leave_detail_month.month <= $vv");
                        $number_date = $this->db->get()->row_array();

                        $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                        $this->db->from('tbl_paid_holiday_leave_detail');
                        $this->db->join(
                            'tbl_paid_holiday_leave',
                            'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id'
                        );
                        $this->db->join(
                            'tbl_paid_holiday_leave_detail_month',
                            'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id'
                        );
                        $this->db->join(
                            'tbl_timekeeping_detail',
                            'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id'
                        );
                        $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                        $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                        $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                        $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                        $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                        $this->db->where("tbl_paid_holiday_leave_detail_month.month >= $vv AND tbl_paid_holiday_leave_detail_month.month <= $vv");
                        $number_date_new = $this->db->get()->row_array();
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[$rowsa] . $row, (($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) > 0 ? ($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                        $totalPaid += !empty(($number_date['number_date'] + ($number_date_new['number_date'] * 0.5))) ? ($number_date['number_date'] + ($number_date_new['number_date'] * 0.5)) : 0;
                    }
                    $totalOld = $value['number_day'] - $totalPaid;
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[($rowsa + 1)] . $row, ($totalPaid > 0 ? ($totalPaid) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[($rowsa + 2)] . $row, ($totalOld > 0 ? ($totalOld) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:T' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:T' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(20);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('bao_cao_tong_hop_phep_nam_') . $year_search . '.xls';
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }
}
