<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Period_violation extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewPeriodViolation = true;
        $this->preAddPeriodViolation = true;
        $this->preEditPeriodViolation= true;
        $this->preDeletePeriodViolation = true;
    }

    public function index()
    {
        if (!$this->preViewPeriodViolation) {
            access_denied('period_violation');
        }
        $data['title'] = _l('Phiếu thống kê KPI');
        $this->load->view('admin/period_violation/index', $data);
    }

    public function getPeriodViolation()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $month = $this->input->get('month') ?? date('m');
        $year  = $this->input->get('year') ?? date('Y');

        $date = DateTime::createFromFormat('Y-n', "$year-$month");
        $date->modify('-1 month');

        $month_old = $date->format('n');
        $year_old  = $date->format('Y');

        $tb_production_report = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as violation,
                SUM(CASE WHEN tbltrouble.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN tbltrouble.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN tbltrouble.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3
            FROM tblproduction_report
            JOIN tbltrouble ON tbltrouble.id = tblproduction_report.id_trouble
            WHERE YEAR(tblproduction_report.date) = ".$year." AND MONTH(tblproduction_report.date) = ".$month."
            AND tblproduction_report.violate = 1
            GROUP BY staff_responsible
        ) tb_production_report";

        $tb_production_report_old = "(
            SELECT
                tbl_period_violation_item.staff_id as staff_id,
                SUM(violation_old + violation) as violation_old
            FROM tbl_period_violation
            JOIN tbl_period_violation_item ON tbl_period_violation_item.period_violation_id = tbl_period_violation.id
            WHERE tbl_period_violation.year = ".$year_old." AND tbl_period_violation.month = ".$month_old."
            GROUP BY tbl_period_violation_item.staff_id
        ) tb_production_report_old";

        $aColumns = [
            'tblstaff.staffid as id',
            'tblstaff.code as code_staff',
            'CONCAT(TRIM(tblstaff.firstname)," ",TRIM(tblstaff.lastname)) as name_staff',
            'tbl_period_violation_item.violation_old as violation_old',
            'COALESCE(tb_production_report_old.violation_old,0) as violation_old',
            'COALESCE(tb_production_report.violation,0) as violation',
            'COALESCE(tb_production_report.violation_p1,0) as violation_p1',
            'COALESCE(tb_production_report.violation_p2,0) as violation_p2',
            'COALESCE(tb_production_report.violation_p3,0) as violation_p3'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tblstaff';
        $where = [
        ];
        $filter = [];
        $join = [
        ];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrId = array_column($rResult,'id');
        if (!empty($arrId)){
        }
        $footData = [];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">'.$aRow['code_staff'].'</div>';
            $row[] = '<div class="text-left" style="width: 100px">'.$aRow['name_staff'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['violation_old'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['violation_old'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['violation'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['max_violation_p1'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['violation_p1'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['max_violation_p2'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['violation_p2'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['max_violation_p3'].'</div>';
            $row[] = '<div class="text-center">'.$aRow['violation_p3'].'</div>';
            $output['aaData'][] = $row;
        }
        $output['footData'] = $footData;
        echo json_encode($output);
    }

    public function add_period_violation()
    {
        if (!$this->preAddPeriodViolation) {
            accessDenied();
        }
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('month', lang("month"), 'required');
            $this->form_validation->set_rules('year', lang("year"), 'required');
            if ($this->form_validation->run() == true) {
//                print_arrays($this->input->post());
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $counter = $this->input->post('counter');
                $arrPeriodViolationItem = [];
                if (!empty($counter)) {
                    $violation_old_post = $this->input->post('violation_old');
                    $violation_post = $this->input->post('violation');
                    $max_violation_p1_post = $this->input->post('max_violation_p1');
                    $violation_p1_post = $this->input->post('violation_p1');
                    $max_violation_p2_post = $this->input->post('max_violation_p2');
                    $violation_p2_post = $this->input->post('violation_p2');
                    $max_violation_p3_post = $this->input->post('max_violation_p3');
                    $violation_p3_post = $this->input->post('violation_p3');
                    $staff_id_post = $this->input->post('staff_id');

                    foreach ($counter as $key => $value) {
                        $violation_old = number_unformat($violation_old_post[$key]);
                        $violation = number_unformat($violation_post[$key]);
                        $max_violation_p1 = number_unformat($max_violation_p1_post[$key]);
                        $violation_p1 = number_unformat($violation_p1_post[$key]);
                        $max_violation_p2 = number_unformat($max_violation_p2_post[$key]);
                        $violation_p2 = number_unformat($violation_p2_post[$key]);
                        $max_violation_p3 = number_unformat($max_violation_p3_post[$key]);
                        $violation_p3 = number_unformat($violation_p3_post[$key]);
                        $staff_id = number_unformat($staff_id_post[$key]);

                        $personnel = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');

                        $full_name = vn_to_str($personnel['firstname'].' '.$personnel['lastname']);
                        $code_name = $personnel['code'];
                        $code = 'KPI_'.$code_name.'_'.$full_name.'_'.$month.$year;
                        $arrPeriodViolationItem[] = [
                            'code' => $code,
                            'staff_id' => $staff_id,
                            'violation_old' => $violation_old,
                            'violation' => $violation,
                            'max_violation_p1' => $max_violation_p1,
                            'violation_p1' => $violation_p1,
                            'max_violation_p2' => $max_violation_p2,
                            'violation_p2' => $violation_p2,
                            'max_violation_p3' => $max_violation_p3,
                            'violation_p3' => $violation_p3,
                        ];
                    }
                }
                if (empty($arrPeriodViolationItem)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có dữ liệu');
                    echo json_encode($data);
                    die;
                }


                $Idpayroll = 0;
                $this->db->select('*');
                $this->db->from('tbl_period_violation');
                $this->db->where('tbl_period_violation.month', $month);
                $this->db->where('tbl_period_violation.year', $year);
                $payroll = $this->db->get()->row_array();
                if (!empty($payroll)) {
                    $Idpayroll = $payroll['id'];
                } else {
                    $this->db->insert('tbl_period_violation', [
                        'month' => $month,
                        'year' => $year,
                        'date_created' => date('Y-m-d H:i'),
                        'created_by' => get_staff_user_id(),
                    ]);
                    $Idpayroll = $this->db->insert_id();
                }
                if ($Idpayroll) {

                    foreach ($arrPeriodViolationItem as $key => $value) {
                        $value['period_violation_id'] = $Idpayroll;
                        $this->db->insert('tbl_period_violation_item', $value);
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        } else {
            $data['branch'] = getListBranch();
            $data['title'] = lang('Tạo kỳ vi phạm');
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/period_violation'),
                    'page' => lang('Kỳ vi phạm'),
                ),
                array('link' => '#', 'page' => $data['title']),
            ];
            $this->load->view('admin/period_violation/add_period_violation', $data);
        }
    }

    public function loadPeriodViolation()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $branch_search = $this->input->get('branch_search');

        $month = $this->input->get('month');
        $year  = $this->input->get('year');

        $date = DateTime::createFromFormat('Y-n', "$year-$month");
        $date->modify('-1 month');

        $month_old = $date->format('n');
        $year_old  = $date->format('Y');


        $tHead = '';
        $html = '';
        $tHead = '<tr>
            <th class="text-center" style="min-width: 50px;">'.lang('tnh_numbers').'</th>
            <th class="text-center" style="min-width: 150px;">'.lang('Mã NV').'</th>
            <th class="text-center" style="min-width: 150px;">'.lang('Tên NV').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần vi phạm đã có').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần vi phạm mới (kỳ này)').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần P1 tối đa').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần P1').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần P2 tối đa').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần P2').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần P3 tối đa').'</th>
            <th class="text-center" style="min-width: 100px;">'.lang('Số lần P3').'</th>
        </tr>';

        $isPayroll = "(
            SELECT COUNT(*)
            FROM tbl_period_violation
            LEFT JOIN tbl_period_violation_item on tbl_period_violation_item.period_violation_id = tbl_period_violation.id
            WHERE tbl_period_violation.month = '$month' AND tbl_period_violation.year = '$year' AND tblstaff.staffid = tbl_period_violation_item.staff_id
        )";

        $period_violatio_id = 0;
        $this->db->select('*');
        $this->db->from('tbl_period_violation');
        $this->db->where('tbl_period_violation.month', $month);
        $this->db->where('tbl_period_violation.year', $year);
        $periodViolatio = $this->db->get()->row_array();
        if (!empty($periodViolatio)) {
            $period_violatio_id = $periodViolatio['id'];
        }

        $tb_production_report = "(
            SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as violation,
                SUM(CASE WHEN tbltrouble.type_p = 1 THEN 1 ELSE 0 END) AS violation_p1,
                SUM(CASE WHEN tbltrouble.type_p = 2 THEN 1 ELSE 0 END) AS violation_p2,
                SUM(CASE WHEN tbltrouble.type_p = 3 THEN 1 ELSE 0 END) AS violation_p3
            FROM tblproduction_report
            JOIN tbltrouble ON tbltrouble.id = tblproduction_report.id_trouble
            WHERE YEAR(tblproduction_report.date) = ".$year." AND MONTH(tblproduction_report.date) = ".$month."
            AND tblproduction_report.violate = 1
            GROUP BY staff_responsible
        ) tb_production_report";

        $tb_production_report_old = "(
            SELECT
                tbl_period_violation_item.staff_id as staff_id,
                SUM(violation_old + violation) as violation_old
            FROM tbl_period_violation
            JOIN tbl_period_violation_item ON tbl_period_violation_item.period_violation_id = tbl_period_violation.id
            WHERE tbl_period_violation.year = ".$year_old." AND tbl_period_violation.month = ".$month_old."
            GROUP BY tbl_period_violation_item.staff_id
        ) tb_production_report_old";
        
        $this->db->select("
            tblstaff.staffid as staffid,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as name,
            tblstaff.code as code,
            COALESCE(tb_production_report_old.violation_old,0) as violation_old,
            COALESCE(tb_production_report.violation,0) as violation,
            COALESCE(tb_production_report.violation_p1,0) as violation_p1,
            COALESCE(tb_production_report.violation_p2,0) as violation_p2,
            COALESCE(tb_production_report.violation_p3,0) as violation_p3,
            ", false);
        $this->db->from('tblstaff');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join($tb_production_report, 'tb_production_report.staff_id = tblstaff.staffid', 'left');
        $this->db->join($tb_production_report_old, 'tb_production_report_old.staff_id = tblstaff.staffid', 'left');
        $this->db->where('(tblstaff.status_work != 2)');
        $this->db->where('tblstaff.branch_salary', $branch_search);
        $this->db->where("($isPayroll = 0)");
        $personnel = $this->db->get()->result_array();

        $index = 0;
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $staffid = $value['staffid'];

                $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                $tdCode = '<div class="td-code">
                    '.$value['code'].'
                </div>';
                $tdNameStaff = '<div class="td-name-staff">
                    '.$value['name'].'
                </div>';

                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;height:50px">'.$tdNumber.'</td>';

                $html .= '<td style="min-width: 100px;">'.$tdCode.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdNameStaff.'</td>';


                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control violation_old number-format" name="violation_old[]" style="width: 120px" value="'.$value['violation_old'].'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control violation number-format" name="violation[]" style="width: 120px" value="'.$value['violation'].'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control max_violation_p1 number-format" name="max_violation_p1[]" style="width: 120px" value="0">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control violation_p1 number-format" name="violation_p1[]" style="width: 120px" value="'.$value['violation_p1'].'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control max_violation_p2 number-format" name="max_violation_p2[]" style="width: 120px" value="0">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control violation_p2 number-format" name="violation_p2[]" style="width: 120px" value="'.$value['violation_p2'].'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control max_violation_p3 number-format" name="max_violation_p3[]" style="width: 120px" value="0">
                </td>';

                $html .= '<td style="min-width: 120px;text-align:right">
                <input type="text" class="form-control violation_p3 number-format" name="violation_p3[]" style="width: 120px" value="'.$value['violation_p3'].'">
                <input type="hidden" name="counter[]" class="form-control counter" value="'.$index.'">
                <input type="hidden" name="staff_id[]" class="form-control staff_id" value="'.$value['staffid'].'">
                </td>';


                $html .= '</tr>';
                $index++;
            }
        }

        $tfoot = '';
        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $this->load->view('admin/period_violation/load_add_period_violation', $data);
    }
}