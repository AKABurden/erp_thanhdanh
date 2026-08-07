<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->perViewPayrollSalary = has_permission('payroll_salary', '', 'view');
        $this->perViewOwnPayrollSalary = has_permission('payroll_salary', '', 'view_own');
        $this->perAddPayrollSalary = has_permission('payroll_salary', '', 'create');
        $this->perEditOwnPayrollSalary = has_permission('payroll_salary', '', 'edit');
        $this->perDeletePayrollSalary = has_permission('payroll_salary', '', 'delete');

        $this->perViewPayrollPayment = has_permission('payroll_payment', '', 'view');
        $this->perViewOwnPayrollPayment = has_permission('payroll_payment', '', 'view_own');
        $this->perAddPayrollPayment = has_permission('payroll_payment', '', 'create');
        $this->perEditPayrollPayment = has_permission('payroll_payment', '', 'edit');
        $this->perDeletePayrollPayment = has_permission('payroll_payment', '', 'delete');

        $this->isAdmin = is_admin();
        $this->cong_fix = 1;
    }

    public function payroll_salary()
    {
        if (!$this->perViewPayrollSalary && !$this->perViewOwnPayrollSalary) {
            accessDenied();
        }
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('BẢNG LƯƠNG');
        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $this->db->where_not_in('tbl_allowance_reduce.id',[ALLOWANCE_CHUYENCAN,ALLOWANCE_THAMNIEN]);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        $colspanAllowance = 3 + count($dtAllowance);
        $colspanReduce = 5 + count($dtReduce);
        $data['dtAllowance'] = $dtAllowance;
        $data['dtReduce'] = $dtReduce;
        $data['colspanAllowance'] = $colspanAllowance;
        $data['colspanReduce'] = $colspanReduce;
        $data['branch'] = getListBranch();
        $this->load->view('admin/payroll/index_payroll_salary', $data);
    }

    public function getPayroll()
    {
        $arrIDStaff = employee_manage_staff();
        $arrBranch = get_branch_staff();
        $staff_search = $this->input->post('staff');
        $month_search = $this->input->post('month');
        $year_search = $this->input->post('year');
        $department_search = $this->input->post('department');
        $branch_search = $this->input->post('branch_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $footer_total_allowance = '';
        $footer_total_reduce = '';
        $arrFooter = [];

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $this->db->where_not_in('tbl_allowance_reduce.id',[ALLOWANCE_CHUYENCAN,ALLOWANCE_THAMNIEN]);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll',
                    ['category_id' => $value['id'], 'type' => 1], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtAllowance[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_allowance_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);

            }
        }

        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll',
                    ['category_id' => $value['id'], 'type' => 2], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtReduce[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_reduce_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);
            }
        }

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $aColumns = [
            'tbl_payroll_item.id as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
            'tblroles.name as role',
            'tblstaff.day_in as day_in',
            'tblstaff.status_work as status_work',
            'tbl_payroll_item.salary_kpi as salary_kpi',
            'tblstaff.coefficient_responsibility as coefficient_responsibility',
            'tblstaff.coefficient_position as coefficient_position',
            'tbl_payroll_item.salary_bhxh as salary_bhxh',
            'tbl_payroll_item.salary_p2 as salary_p2',
            'tbl_payroll_item.salary_p3 as salary_p3',
            'tbl_payroll_item.tham_nien as tham_nien',
            'tbl_payroll_item.diligence_salary as diligence_salary',
            'tbl_payroll_item.salary_p3_new as salary_p3_new',
            'tbl_payroll_item.salary as salary',
            'tbl_payroll_item.day_number as day_number',
            'tbl_payroll_item.day_number_new as day_number_new',
            'tbl_payroll_item.day_holiday as day_holiday',
            'tbl_payroll_item.day_lt as day_lt',
            'tbl_payroll_item.day_ch as day_ch',
            'tbl_payroll_item.day_ch as day_ch',
            'tbl_payroll_item.day_number_off as day_number_off',
            'tbl_payroll_item.total_day_number as total_day_number',
            'tbl_payroll_item.salary_income as salary_income',
            'tbl_payroll_item.weight_p2 as weight_p2',
            'tbl_payroll_item.salary_p2_real as salary_p2_real',
            'tbl_payroll_item.diligence as diligence',
            'tbl_payroll_item.check_p3 as check_p3',
            'tbl_payroll_item.salary_p3_real as salary_p3_real',
            'tbl_payroll_item.salary_kpi_real as salary_kpi_real',
            'tbl_payroll_item.hour_late as hour_late',
            'tbl_payroll_item.money_hour_late as money_hour_late',
            'tbl_payroll_item.deduct_bhxh as deduct_bhxh',
            'tbl_payroll_item.deduct_bhyt as deduct_bhyt',
            'tbl_payroll_item.deduct_bhtn as deduct_bhtn',
            'tbl_payroll_item.deduct_union as deduct_union',
            'tbl_payroll_item.deduct_advance as deduct_advance',
            'tbl_payroll_item.total_allowance_other as total_allowance_other',
            'tbl_payroll_item.total as total',
            'tbl_payroll_item.total_real as total_real',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_payroll_item';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_payroll ON tbl_payroll.id = tbl_payroll_item.payroll_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_payroll_item.staff_id',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            ];

        if (!empty($department_search)) {
            array_push($where, "AND EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department_search
                )");
        }

        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_payroll_item.staff_id IN ('.implode(',', $staff_search).'))');
        }
        if (!empty($month_search)) {
            array_push($where,
                'AND ( tbl_payroll.month = '.$month_search.')');
        }
        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_payroll.year = '.$year_search.')');
        }

        if (!$this->isAdmin && $this->perViewOwnPayrollSalary) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                array_push($where, 'AND tbl_payroll_item.staff_id IN ('.$coverStr.')');
            }
        }

        if (!empty($branch_search)) {
            array_push($where,
                'AND ( tblstaff.branch_salary = '.$branch_search.')');
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                array_push($where, 'AND tblstaff.branch_salary IN ('.$coverStrBranch.')');
            } else {
                array_push($where,
                    'AND (tbl_payroll_item.id = 0)');
            }
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblstaff.staffid as staff_id',
            'tbl_payroll_item.allowance_business_fee as allowance_business_fee',
            'tbl_payroll_item.total_reduce_other as total_reduce_other',
            'tbl_payroll_item.total_weekday',
            'tbl_payroll_item.total_sunday',
            'tbl_payroll_item.total_holiday',
            'tbl_payroll_item.total_weekday_night',
            'tbl_payroll_item.total_sunday_night',
            'tbl_payroll_item.bonus_holiday as bonus_holiday',
            'tbl_payroll_item.allowance_responsibility as allowance_responsibility',
            'tbl_payroll_item.allowance_other as allowance_other',
            'tbl_payroll_item.day_number_new as day_number_new',
            'tbl_payroll_item.allowance_rice_tc as allowance_rice_tc',
            'tbl_payroll_item.day_holiday as day_holiday',
            'tbl_payroll_item.day_lt as day_lt',
            'tbl_payroll_item.salary_responsibility as salary_responsibility',
            'tbl_payroll_item.salary_position as salary_position',
            'tbl_payroll_item.responsibility_salary as responsibility_salary',
            'tbl_payroll_item.day_ch as day_ch',
            'tbl_payroll_item.day_number_off as day_number_off',
            'tbl_payroll_item.day_number_off_new as day_number_off_new',
            'tbl_payroll_item.salary_off as salary_off',
            'tbl_payroll_item.hour_late as hour_late',
            'tbl_payroll_item.money_hour_late as money_hour_late',
            'tbl_payroll_item.sales as sales',
            'tbl_payroll_item.gasonline_cars as gasonline_cars',
            'tbl_payroll_item.phone as phone',
            'tbl_payroll_item.motel as motel',
            'tbl_payroll_item.concurrently as concurrently',
            'tbl_payroll_item.business_fee_staff as business_fee_staff',
            'tbl_payroll_item.number_reduce as number_reduce',
            'tbl_payroll_item.business_fee_difference as business_fee_difference',
            'tbl_payroll_item.allowance_diff as allowance_diff',
            'tbl_payroll_item.total_vat as total_vat',
            'tbl_payroll_item.seniority as seniority',
            'tbl_payroll_item.complete_permission as complete_permission',
            'tbl_payroll_item.salary_compensation as salary_compensation',
            'tbl_payroll_item.tax_exemption as tax_exemption',
            'tbl_payroll_item.taxable_income as taxable_income',
            'tbl_payroll_item.grand_total_kt as grand_total_kt',
            'tbl_payroll_item.grand_total_kl as grand_total_kl',
            'tbl_payroll_item.weight_p2 as weight_p2',
            'tbl_payroll_item.weight_p3 as weight_p3',
            'tbl_payroll_item.percent_vat as percent_vat',
            'tbl_payroll_item.family_deduction as family_deduction',
            'tbl_payroll_item.tax_exemption as tax_exemption',
            'tbl_payroll_item.taxable_income as taxable_income',
            'tbl_payroll_item.tax_collection as tax_collection',
            'tbl_payroll_item.total_vat as total_vat',
            'tbl_payroll_item.other_adjustments as other_adjustments',
            'tbl_payroll_item.allowance_rice as allowance_rice',
            'tbl_payroll_item.allowance_rice_tc as allowance_rice_tc',
            'tbl_payroll_item.allowance_rice_money as allowance_rice_money',
            'tbl_payroll_item.bhxh_company as bhxh_company',
            'tbl_payroll_item.salary_3p_id as salary_3p_id',
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        $footer_salary_kpi = 0;
        $footer_salary = 0;
        $footer_salary_bhxh = 0;
        $footer_salary_p2 = 0;
        $footer_salary_p3 = 0;
        $footer_tham_nien = 0;
        $footer_diligence_salary = 0;
        $footer_salary_p3_new = 0;
        $footer_diligence = 0;
        $footer_salary_kpi_real= 0;
        $footer_salary_p2_real= 0;
        $footer_salary_p3_real= 0;
        $footer_family_deduction = 0;
        $footer_salary_responsibility = 0;
        $footer_salary_position = 0;
        $footer_responsibility_salary = 0;
        $footer_sales = 0;
        $footer_gasonline_cars = 0;
        $footer_phone = 0;
        $footer_motel = 0;
        $footer_concurrently = 0;
        $footer_business_fee_staff = 0;
        $footer_seniority = 0;
        $footer_total_vat = 0;
        $footer_allowance = 0;
        $footer_total_salary_new = 0;
        $footer_day_number = 0;
        $footer_day_number_new = 0;
        $footer_day_number_holiday = 0;
        $footer_day_number_lt = 0;
        $footer_day_number_ch = 0;
        $footer_day_number_off = 0;
        $footer_day_number_off_new = 0;
        $footer_salary_off = 0;
        $footer_salary_income = 0;
        $footer_allowance_responsibility = 0;
        $footer_allowance_other = 0;
        $footer_allowance_manu = 0;
        $footer_allowance_western = 0;
        $footer_allowance_business_fee = 0;
        $footer_allowance_rice = 0;
        $footer_allowance_rice_tc = 0;
        $footer_allowance_rice_money = 0;
        $footer_bonus_holiday = 0;
        $footer_deduct_bhxh = 0;
        $footer_deduct_bhyt = 0;
        $footer_deduct_bhtn = 0;
        $footer_deduct_union = 0;
        $footer_total_allowance_other = 0;
        $footer_total_reduce_other = 0;
        $footer_deduct_advance = 0;
        $footer_total = 0;
        $footer_total_real = 0;

        $footer_hour_late = 0;
        $footer_money_hour_late = 0;

        $footer_business_fee_difference = 0;
        $footer_salary_compensation = 0;
        $footer_tax_exemption = 0;
        $footer_complete_permission = 0;
        $footer_taxable_income = 0;
        $footer_grand_total_kt = 0;
        $footer_grand_total_kl = 0;
        $footer_total_day_number = 0;
        $footer_diligence = 0;
        $footer_tax_collection = 0;
        $footer_other_adjustments = 0;
        $footer_salary_real_p1_p2 = 0;
        $footer_salary_real_p3 = 0;
        $footer_bhxh_company = 0;

        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $staff_id = $aRow['staff_id'];
            $status_work = '';
            if ($aRow['status_work'] == 1) {
                $status_work = 'CT';
            } elseif ($aRow['status_work'] == 0){
                $status_work = 'TV';
            }
            $row[0] = '<div class="text-center checkbox" style="width: 40px"><input type="checkbox" name="items[]" id="check-item'.$aRow['id'].'" value="'.$aRow['id'].'"><label for="check-item'.$aRow['id'].'"></label></div>';
            $row[1] = '<div style="width: 120px">'.$aRow['code'].'</div>';
            $row[2] = '<div style="width: 200px">'.$aRow['fullname'].'</div>';
            $row[3] = '<div style="width: 150px" class="text-left">'.$aRow['role'].'</div>';
            $row[4] = '<div style="width: 100px" class="text-left">'.(!empty($aRow['day_in']) ? _dhau($aRow['day_in']) : '').'</div>';
            $row[5] = '<div style="width: 100px" class="text-center">'.$status_work.'</div>';
            $row[6] = '<div style="text-align: right">'.(!empty($aRow['salary_kpi']) ? formatMoneyPayroll($aRow['salary_kpi']) : '').'</div>';
            $row[7] = '<div style="width: 100px;text-align: right">'.(!empty($aRow['coefficient_responsibility']) ? ($aRow['coefficient_responsibility']) : '').'</div>';
            $row[8] = '<div style="width: 80px;text-align: right">'.(!empty($aRow['coefficient_position']) ? ($aRow['coefficient_position']) : '').'</div>';
            $row[9] = '<div style="text-align: right"><a class="tnh-modal" href="'.base_url("admin/salary3P/view/".$aRow['salary_3p_id']."").'">'.(!empty($aRow['salary_bhxh']) ? formatMoneyPayroll($aRow['salary_bhxh']) : '').'</a></div>';
            $row[10] = '<div style="text-align: right"><a class="tnh-modal" href="'.base_url("admin/salary3P/view/".$aRow['salary_3p_id']."").'">'.(!empty($aRow['salary_p2']) ? formatMoneyPayroll($aRow['salary_p2']) : '').'</a></div>';
            $row[11] = '<div style="text-align: right"><a class="tnh-modal" href="'.base_url("admin/salary3P/view/".$aRow['salary_3p_id']."").'">'.(!empty($aRow['salary_p3']) ? formatMoneyPayroll($aRow['salary_p3']) : '').'</a></div>';
            $row[12] = '<div style="text-align: right">'.(!empty($aRow['tham_nien']) ? formatMoneyPayroll($aRow['tham_nien']) : '').'</div>';
            $row[13] = '<div style="text-align: right">'.(!empty($aRow['diligence_salary']) ? formatMoneyPayroll($aRow['diligence_salary']) : '').'</div>';
            $row[14] = '<div style="text-align: right">'.(!empty($aRow['salary_p3_new']) ? formatMoneyPayroll($aRow['salary_p3_new']) : '').'</div>';
            $row[15] = '<div style="text-align: right">'.(!empty($aRow['salary']) ? formatMoneyPayroll($aRow['salary']) : '').'</div>';
            $row[16] = '<div style="text-align: center"><a target="_blank" href="'.base_url("admin/salary/timekeeping?staff_id=".$staff_id."&month=".$month_search."&year=".$year_search."").'">'.(!empty($aRow['day_number']) ? ($aRow['day_number']) : '').'</a></div>';
            $row[17] = '<div style="text-align: center">'.($aRow['day_number_new'] > 0 ? ($aRow['day_number_new']) : '').'</div>';
            $row[18] = '<div style="text-align: center">'.($aRow['day_holiday'] > 0 ? ($aRow['day_holiday']) : '').'</div>';
            $row[19] = '<div style="text-align: center">'.($aRow['day_lt'] > 0 ? ($aRow['day_lt']) : '').'</div>';
            $row[20] = '<div style="text-align: center">'.($aRow['day_ch'] > 0 ? ($aRow['day_ch']) : '').'</div>';
            $row[21] = '<div style="text-align: center">'.($aRow['day_number_off'] > 0 ? ($aRow['day_number_off']) : '').'</div>';
            $row[22] = '<div style="text-align: center">'.($aRow['total_day_number'] > 0 ? ($aRow['total_day_number']) : '').'</div>';
            $row[23] = '<div style="text-align: center">'.($aRow['salary_income'] > 0 ? formatMoneyPayroll($aRow['salary_income']) : '').'</div>';
            $row[24] = '<div style="text-align: center"><a target="_blank" href="'.base_url("admin/kpi/staff_kpi_evaluation?type=list&staff_id=".$staff_id."&month=".$month_search."&year=".$year_search."").'">'.($aRow['weight_p2'] > 0 ? ($aRow['weight_p2']).' %' : '').'</a></div>';
            $row[25] = '<div style="text-align: right">'.($aRow['salary_p2_real'] > 0 ? formatMoneyPayroll($aRow['salary_p2_real']) : '').'</div>';
            $row[26] = '<div style="text-align: right">'.($aRow['diligence'] > 0 ? formatMoneyPayroll($aRow['diligence']) : '').'</div>';
            $row[27] = ($aRow['check_p3'] == 1 ? 'X__yellow__'.$staff_id.'__'.$month_search.'__'.$year_search : '');
            $row[28] = '<div style="text-align: right">'.($aRow['salary_p3_real'] > 0 ? formatMoneyPayroll($aRow['salary_p3_real']) : '').'</div>';
            $row[29] = '<div style="text-align: right">'.($aRow['salary_kpi_real'] > 0 ? formatMoneyPayroll($aRow['salary_kpi_real']) : '').'</div>';
            $row[30] = '<div style="text-align: center"><a target="_blank" href="'.base_url("admin/salary/timekeeping?staff_id=".$staff_id."&month=".$month_search."&year=".$year_search."").'">'.($aRow['hour_late'] > 0 ? ($aRow['hour_late']) : '').'</a></div>';
            $row[31] = '<div style="text-align: right">'.($aRow['money_hour_late'] > 0 ? formatMoneyPayroll($aRow['money_hour_late']) : '').'</div>';

            $keyNew = 31;
            if (!empty($dtAllowance)) {
                foreach ($dtAllowance as $kk => $vv) {
                    $items = $vv['items'];
                    $checkKey = $aRow['staff_id'].'_'.$aRow['id'];
                    $keyNew++;
                    $row[$keyNew] = '<div style="text-align: right">'.(!empty($items[$checkKey]['amount']) ? formatMoneyPayroll($items[$checkKey]['amount']) : '').'</div>';
                    $keyCheck = 'footer_total_allowance_'.$vv['id'];
                    $arrFooter[$keyCheck] += (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : 0);
                }
            }
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.($aRow['allowance_rice'] > 0 ? formatNumber($aRow['allowance_rice']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.($aRow['allowance_rice_tc'] > 0 ? formatNumber($aRow['allowance_rice_tc']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['allowance_rice_money'] > 0 ? formatMoneyPayroll($aRow['allowance_rice_money']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['total_allowance_other'] > 0 ? formatMoneyPayroll($aRow['total_allowance_other']) : '').'</div>';
            $keyNew++;

            $row[$keyNew] = '<div style="text-align: center">'.(!empty($aRow['total_weekday']) ? formatMoneyPayroll($aRow['total_weekday']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.(!empty($aRow['total_sunday']) ? formatMoneyPayroll($aRow['total_sunday']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.(!empty($aRow['total_holiday']) ? formatMoneyPayroll($aRow['total_holiday']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.(!empty($aRow['total_weekday_night']) ? formatMoneyPayroll($aRow['total_weekday_night']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.(!empty($aRow['total_sunday_night']) ? formatMoneyPayroll($aRow['total_sunday_night']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.(!empty($aRow['allowance_business_fee']) ? formatMoneyPayroll($aRow['allowance_business_fee']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.(!empty($aRow['bhxh_company']) ? formatMoneyPayroll($aRow['bhxh_company']) : '').'</div>';


            if (!empty($dtReduce)) {
                foreach ($dtReduce as $kk => $vv) {
                    $items = $vv['items'];
                    $checkKey = $aRow['staff_id'].'_'.$aRow['id'];
                    $keyNew++;
                    $row[$keyNew] = '<div style="text-align: right">'.(!empty($items[$checkKey]['amount']) ? formatMoneyPayroll($items[$checkKey]['amount']) : '').'</div>';
                    $keyCheck = 'footer_total_reduce_'.$vv['id'];
                    $arrFooter[$keyCheck] += (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : 0);
                }
            }

            $total_salary_p1_p2_real = $aRow['total_real'] - $aRow['deduct_advance'] - $aRow['salary_p3_real'];
            $total_salary_p3_real = $aRow['salary_p3_real'];
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_bhxh'] > 0 ? formatMoneyPayroll($aRow['deduct_bhxh']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_bhyt'] > 0 ? formatMoneyPayroll($aRow['deduct_bhyt']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_bhtn'] > 0 ? formatMoneyPayroll($aRow['deduct_bhtn']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_union'] > 0 ? formatMoneyPayroll($aRow['deduct_union']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_reduce_other'] != 0 ? formatMoneyPayroll($aRow['total_reduce_other']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center;width: 120px">'.($aRow['percent_vat'] != 0 ? ($aRow['percent_vat']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['family_deduction'] != 0 ? formatMoneyPayroll($aRow['family_deduction']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['tax_exemption'] != 0 ? formatMoneyPayroll($aRow['tax_exemption']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['taxable_income'] != 0 ? formatMoneyPayroll($aRow['taxable_income']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['tax_collection'] != 0 ? formatMoneyPayroll($aRow['tax_collection']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_vat'] != 0 ? formatMoneyPayroll($aRow['total_vat']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['grand_total_kt'] > 0 ? formatMoneyPayroll($aRow['grand_total_kt']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['grand_total_kl'] > 0 ? formatMoneyPayroll($aRow['grand_total_kl']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['salary_compensation'] != 0 ? formatMoneyPayroll($aRow['salary_compensation']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['other_adjustments'] != 0 ? formatMoneyPayroll($aRow['other_adjustments']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['complete_permission'] != 0 ? formatMoneyPayroll($aRow['complete_permission']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_real'] != 0 ? formatMoneyPayroll($aRow['total_real']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['deduct_advance'] != 0 ? formatMoneyPayroll($aRow['deduct_advance']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($total_salary_p1_p2_real != 0 ? formatMoneyPayroll($total_salary_p1_p2_real) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($total_salary_p3_real != 0 ? formatMoneyPayroll($total_salary_p3_real) : '').'</div>';

            $footer_salary_kpi += $aRow['salary_kpi'];
            $footer_salary_p2 += $aRow['salary_p2'];
            $footer_salary_p3 += $aRow['salary_p3'];
            $footer_tham_nien += $aRow['tham_nien'];
            $footer_diligence_salary += $aRow['diligence_salary'];
            $footer_salary_p3_new += $aRow['salary_p3_new'];
            $footer_salary_p2_real += $aRow['salary_p2_real'];
            $footer_salary_p3_real += $aRow['salary_p3_real'];
            $footer_salary_kpi_real += $aRow['salary_kpi_real'];
            $footer_family_deduction += $aRow['family_deduction'];
            $footer_total_day_number += $aRow['total_day_number'];
            $footer_diligence += $aRow['diligence'];
            $footer_salary += $aRow['salary'];
            $footer_salary_bhxh += $aRow['salary_bhxh'];
            $footer_salary_responsibility += $aRow['salary_responsibility'];
            $footer_salary_position += $aRow['salary_position'];
            $footer_responsibility_salary += $aRow['responsibility_salary'];
            $footer_sales += $aRow['sales'];
            $footer_gasonline_cars += $aRow['gasonline_cars'];
            $footer_phone += $aRow['phone'];
            $footer_motel += $aRow['motel'];
            $footer_concurrently += $aRow['concurrently'];
            $footer_business_fee_staff += $aRow['business_fee_staff'];
            $footer_total_vat += $aRow['total_vat'];
            $footer_business_fee_difference += $aRow['business_fee_difference'];
            $footer_total_salary_new += ($aRow['salary_bhxh']);
            $footer_day_number += $aRow['day_number'];
            $footer_day_number_new += $aRow['day_number_new'];
            $footer_day_number_holiday += $aRow['day_holiday'];
            $footer_day_number_lt += $aRow['day_lt'];
            $footer_day_number_ch += $aRow['day_ch'];
            $footer_day_number_off += $aRow['day_number_off'];
            $footer_day_number_off_new += $aRow['day_number_off_new'];

            $footer_money_hour_late += $aRow['money_hour_late'];
            $footer_hour_late += $aRow['hour_late'];
            $footer_seniority += $aRow['seniority'];
            $footer_salary_off += $aRow['salary_off'];
            $footer_salary_income += $aRow['salary_income'];
            $footer_allowance_responsibility += $aRow['allowance_responsibility'];
            $footer_allowance_other += $aRow['allowance_other'];
            $footer_allowance_business_fee += $aRow['allowance_business_fee'];
            $footer_allowance_rice += $aRow['allowance_rice'];
            $footer_allowance_rice_tc += $aRow['allowance_rice_tc'];
            $footer_allowance_rice_money += $aRow['allowance_rice_money'];
            $footer_bonus_holiday += $aRow['bonus_holiday'];
            $footer_deduct_bhxh += $aRow['deduct_bhxh'];
            $footer_deduct_bhyt += $aRow['deduct_bhyt'];
            $footer_deduct_bhtn += $aRow['deduct_bhtn'];
            $footer_deduct_union += $aRow['deduct_union'];
            $footer_total_allowance_other += $aRow['total_allowance_other'];
            $footer_total_reduce_other += $aRow['total_reduce_other'];
            $footer_deduct_advance += $aRow['deduct_advance'];
            $footer_total += $aRow['total'];
            $footer_total_real += $aRow['total_real'];
            $footer_salary_compensation += $aRow['salary_compensation'];
            $footer_tax_exemption += $aRow['tax_exemption'];
            $footer_complete_permission += $aRow['complete_permission'];
            $footer_taxable_income += $aRow['taxable_income'];
            $footer_grand_total_kt += $aRow['grand_total_kt'];
            $footer_grand_total_kl += $aRow['grand_total_kl'];
            $footer_tax_collection += $aRow['tax_collection'];
            $footer_other_adjustments += $aRow['other_adjustments'];
            $footer_salary_real_p1_p2 += $total_salary_p1_p2_real;
            $footer_salary_real_p3 += $total_salary_p3_real;
            $footer_bhxh_company += $aRow['bhxh_company'];

            $output['aaData'][] = $row;
            $stt++;

        }
        $output['footer_salary'] = $footer_salary;
        $output['footer_salary_bhxh'] = $footer_salary_bhxh;
        $output['footer_salary_responsibility'] = $footer_salary_responsibility;
        $output['footer_salary_position'] = $footer_salary_position;
        $output['footer_responsibility_salary'] = $footer_responsibility_salary;
        $output['footer_sales'] = $footer_sales;
        $output['footer_gasonline_cars'] = $footer_gasonline_cars;
        $output['footer_phone'] = $footer_phone;
        $output['footer_motel'] = $footer_motel;
        $output['footer_concurrently'] = $footer_concurrently;
        $output['footer_business_fee_staff'] = $footer_business_fee_staff;
        $output['footer_seniority'] = $footer_seniority;
        $output['footer_total_vat'] = $footer_total_vat;
        $output['footer_business_fee_difference'] = $footer_business_fee_difference;
        $output['footer_allowance'] = $footer_allowance;
        $output['footer_total_salary_new'] = $footer_total_salary_new;
        $output['footer_day_number'] = $footer_day_number;
        $output['footer_day_number_new'] = $footer_day_number_new;
        $output['footer_day_number_holiday'] = $footer_day_number_holiday;
        $output['footer_day_number_lt'] = $footer_day_number_lt;
        $output['footer_day_number_ch'] = $footer_day_number_ch;
        $output['footer_day_number_off'] = $footer_day_number_off;
        $output['footer_money_hour_late'] = $footer_money_hour_late;
        $output['footer_hour_late'] = $footer_hour_late;
        $output['footer_day_number_off_new'] = $footer_day_number_off_new;
        $output['footer_salary_off'] = $footer_salary_off;
        $output['footer_salary_income'] = $footer_salary_income;
        $output['footer_allowance_responsibility'] = $footer_allowance_responsibility;
        $output['footer_allowance_other'] = $footer_allowance_other;
        $output['footer_allowance_manu'] = $footer_allowance_manu;
        $output['footer_allowance_western'] = $footer_allowance_western;
        $output['footer_allowance_business_fee'] = $footer_allowance_business_fee;
        $output['footer_allowance_rice'] = $footer_allowance_rice;
        $output['footer_allowance_rice_tc'] = $footer_allowance_rice_tc;
        $output['footer_allowance_rice_money'] = $footer_allowance_rice_money;
        $output['footer_bonus_holiday'] = $footer_bonus_holiday;
        $output['footer_deduct_bhxh'] = $footer_deduct_bhxh;
        $output['footer_deduct_bhyt'] = $footer_deduct_bhyt;
        $output['footer_deduct_bhtn'] = $footer_deduct_bhtn;
        $output['footer_deduct_union'] = $footer_deduct_union;
        $output['footer_salary_compensation'] = $footer_salary_compensation;
        $output['footer_tax_exemption'] = $footer_tax_exemption;
        $output['footer_complete_permission'] = $footer_complete_permission;
        $output['footer_taxable_income'] = $footer_taxable_income;
        $output['footer_grand_total_kt'] = $footer_grand_total_kt;
        $output['footer_grand_total_kl'] = $footer_grand_total_kl;
        $output['footer_total_allowance_other'] = $footer_total_allowance_other;
        $output['footer_total_reduce_other'] = $footer_total_reduce_other;
        $output['footer_deduct_advance'] = $footer_deduct_advance;
        $output['footer_total'] = $footer_total;
        $output['footer_total_real'] = $footer_total_real;
        $output['footer_total_day_number'] = $footer_total_day_number;
        $output['footer_salary_kpi'] = $footer_salary_kpi;
        $output['footer_salary_p2'] = $footer_salary_p2;
        $output['footer_salary_p3'] = $footer_salary_p3;
        $output['footer_tham_nien'] = $footer_tham_nien;
        $output['footer_diligence_salary'] = $footer_diligence_salary;
        $output['footer_salary_p3_new'] = $footer_salary_p3_new;
        $output['footer_salary_p2_real'] = $footer_salary_p2_real;
        $output['footer_salary_p3_real'] = $footer_salary_p3_real;
        $output['footer_salary_kpi_real'] = $footer_salary_kpi_real;
        $output['footer_family_deduction'] = $footer_family_deduction;
        $output['footer_diligence'] = $footer_diligence;
        $output['footer_tax_collection'] = $footer_tax_collection;
        $output['footer_other_adjustments'] = $footer_other_adjustments;
        $output['footer_salary_real_p1_p2'] = $footer_salary_real_p1_p2;
        $output['footer_salary_real_p3'] = $footer_salary_real_p3;
        $output['footer_bhxh_company'] = $footer_bhxh_company;
        $output['arrFooter'] = $arrFooter;
        echo json_encode($output);
    }

    public function add_payroll_salary()
    {
        if (!$this->perAddPayrollSalary) {
            accessDenied();
        }
        $dtAllowance = get_table_where('tbl_allowance_reduce', ['type' => 1]);
        $dtReduce = get_table_where('tbl_allowance_reduce', ['type' => 2]);
        $rice_money = get_option('rice_money');
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('month', lang("month"), 'required');
            $this->form_validation->set_rules('year', lang("year"), 'required');
            if ($this->form_validation->run() == true) {
//                print_arrays($this->input->post());
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $counter = $this->input->post('counter');
                $this->db->select('*');
                $this->db->from('tbl_timekeeping');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $timekeeping = $this->db->get()->row_array();
                if (empty($timekeeping)) {
                    $data['result'] = 1;
                    $data['message'] = 'Vui lòng chấm công tháng '.$month.' năm '.$year;
                    echo json_encode($data);
                    die;
                }
                $arrPayrollItem = [];
                if (!empty($counter)) {
                    $salary_3p_id_post = $this->input->post('salary_3p_id');
                    $weight_p2_post = $this->input->post('weight_p2');
                    $weight_p3_post = $this->input->post('weight_p3');
                    $salary_bhxh_post = $this->input->post('salary_bhxh');
                    $salary_bhxh_new_post = $this->input->post('salary_bhxh_new');
                    $allowance_responsibility_post = $this->input->post('allowance_responsibility');
                    $allowance_other_post = $this->input->post('allowance_other');
                    $allowance_manu_post = $this->input->post('allowance_manu');
                    $allowance_western_post = $this->input->post('allowance_western');
                    $allowance_rice_post = $this->input->post('allowance_rice');
                    $allowance_rice_tc_post = $this->input->post('allowance_rice_tc');
                    $bonus_holiday_post = $this->input->post('bonus_holiday');
                    $day_number_post = $this->input->post('day_number');
                    $staff_id_post = $this->input->post('staff_id');
                    $total_date_post = $this->input->post('total_date');
                    $number_day_bhxh_post = $this->input->post('number_day_bhxh');
                    $total_number_day_holiday_post = $this->input->post('total_number_day_holiday');
                    $total_number_day_lt_post = $this->input->post('total_number_day_lt');
                    $total_number_day_ch_post = $this->input->post('total_number_day_ch');
                    $salary_responsibility_post = $this->input->post('salary_responsibility');
                    $salary_position_post = $this->input->post('salary_position');
                    $responsibility_salary_post = $this->input->post('responsibility_salary');
                    $day_number_off_post = $this->input->post('day_number_off');
                    $hour_late_post = $this->input->post('hour_late');
                    $business_fee_difference_post = $this->input->post('business_fee_difference');
                    $complete_permission_post = $this->input->post('complete_permission');
                    $sales_post = $this->input->post('sales');
                    $phone_post = $this->input->post('phone');
                    $gasonline_cars_post = $this->input->post('gasonline_cars');
                    $motel_post = $this->input->post('motel');
                    $concurrently_post = $this->input->post('concurrently');
                    $business_fee_staff_post = $this->input->post('business_fee_staff');
                    $seniority_post = $this->input->post('seniority');
                    $number_reduce_post = $this->input->post('number_reduce');
                    $allowance_diff_post = $this->input->post('allowance_diff');

                    $grand_total_kt_post = $this->input->post('grand_total_kt');
                    $grand_total_kl_post = $this->input->post('grand_total_kl');
                    $salary_compensation_post = $this->input->post('salary_compensation');
                    $check_p3_post = $this->input->post('check_p3');
                    $salary_p2_post = $this->input->post('salary_p2');
                    $salary_p3_post = $this->input->post('salary_p3');
                    $tham_nien_post = $this->input->post('tham_nien');
                    $diligence_post = $this->input->post('diligence');
                    $diligence_salary_post = $this->input->post('diligence_salary');
                    $other_adjustments_post = $this->input->post('other_adjustments');
                    $total_weekday_post = $this->input->post('total_weekday');
                    $total_sunday_post = $this->input->post('total_sunday');
                    $total_holiday_post = $this->input->post('total_holiday');
                    $total_weekday_night_post = $this->input->post('total_weekday_night');
                    $total_sunday_night_post = $this->input->post('total_sunday_night');
                    $total_date_post = $this->input->post('total_date');
                    $number_hour_post = $this->input->post('number_hour');

                    $arrAll = [];
                    $arrRedu = [];
                    if (!empty($dtAllowance)) {
                        foreach ($dtAllowance as $kk => $vv) {
                            $arrAll['allowance_other_'.$vv['id']] = $this->input->post('allowance_other_'.$vv['id']);
                        }
                    }
                    if (!empty($dtReduce)) {
                        foreach ($dtReduce as $kk => $vv) {
                            $arrRedu['reduce_other_'.$vv['id']] = $this->input->post('reduce_other_'.$vv['id']);
                        }
                    }
                    $data_json_payment_post = $this->input->post('data_json_payment');
                    foreach ($counter as $key => $value) {
                        $allowance_responsibility = number_unformat($allowance_responsibility_post[$key]);
                        $salary_bhxh = number_unformat($salary_bhxh_post[$key]);
                        $salary_bhxh_new = number_unformat($salary_bhxh_new_post[$key]);
                        $allowance_other = number_unformat($allowance_other_post[$key]);
                        $allowance_manu = number_unformat($allowance_manu_post[$key]);
                        $allowance_western = number_unformat($allowance_western_post[$key]);
                        $allowance_rice = number_unformat($allowance_rice_post[$key]);
                        $allowance_rice_tc = number_unformat($allowance_rice_tc_post[$key]);
                        $bonus_holiday = number_unformat($bonus_holiday_post[$key]);
                        $day_number = number_unformat($day_number_post[$key]);
                        $staff_id = number_unformat($staff_id_post[$key]);
                        $totalDate = number_unformat($total_date_post[$key]);
                        $number_day_bhxh = number_unformat($number_day_bhxh_post[$key]);
                        $total_number_day_holiday = number_unformat($total_number_day_holiday_post[$key]);
                        $total_number_day_lt = number_unformat($total_number_day_lt_post[$key]);
                        $salary_responsibility = number_unformat($salary_responsibility_post[$key]);
                        $salary_position = number_unformat($salary_position_post[$key]);
                        $total_number_day_ch = number_unformat($total_number_day_ch_post[$key]);
                        $day_number_off = number_unformat($day_number_off_post[$key]);
                        $hour_late = number_unformat($hour_late_post[$key]);
                        $business_fee_difference = number_unformat($business_fee_difference_post[$key]);
                        $sales = number_unformat($sales_post[$key]);
                        $gasonline_cars = number_unformat($gasonline_cars_post[$key]);
                        $phone = number_unformat($phone_post[$key]);
                        $motel = number_unformat($motel_post[$key]);
                        $seniority = number_unformat($seniority_post[$key]);
                        $concurrently = number_unformat($concurrently_post[$key]);
                        $business_fee_staff = number_unformat($business_fee_staff_post[$key]);
                        $number_reduce = number_unformat($number_reduce_post[$key]);
                        $allowance_diff = number_unformat($allowance_diff_post[$key]);
                        $grand_total_kt = number_unformat($grand_total_kt_post[$key]);
                        $grand_total_kl = number_unformat($grand_total_kl_post[$key]);

                        $salary_3p_id = ($salary_3p_id_post[$key]) ?? 0;
                        $weight_p2 = ($weight_p2_post[$key]) ?? 0;
                        $weight_p3 = ($weight_p3_post[$key]) ?? 0;
                        $salary_p3 = ($salary_p3_post[$key]) ?? 0;
                        $salary_p2 = ($salary_p2_post[$key]) ?? 0;

                        $other_adjustments =  !empty($other_adjustments_post[$key]) ? $other_adjustments_post[$key] : 0;
                        $diligence = number_unformat($diligence_post[$key]) ?? 0;
                        $tham_nien = number_unformat($tham_nien_post[$key]) ?? 0;
                        $total_weekday = ($total_weekday_post[$key]) ?? 0;
                        $total_sunday = ($total_sunday_post[$key]) ?? 0;
                        $total_holiday = ($total_holiday_post[$key]) ?? 0;
                        $total_weekday_night = !empty($total_weekday_night_post[$key]) ? $total_weekday_night_post[$key] : 0;
                        $total_sunday_night = !empty(($total_sunday_night_post[$key])) ? ($total_sunday_night_post[$key]) : 0;
                        $diligence_salary = !empty(($diligence_salary_post[$key])) ? ($diligence_salary_post[$key]) : 0;


                        $complete_permission = number_unformat(!empty($complete_permission_post[$key]) ? $complete_permission_post[$key] : 0);
                        $salary_compensation = number_unformat(!empty($salary_compensation_post[$key]) ? $salary_compensation_post[$key] : 0);
                        $check_p3 = number_unformat($check_p3_post[$key]);
                        $totalDate = number_unformat($total_date_post[$key]);
                        $number_hour = number_unformat($number_hour_post[$key]);

                        if($salary_bhxh <= 0){
                            continue;
                        }

                        $money_hour_late =  (($salary_bhxh) / $totalDate / $number_hour) * $hour_late;

                        $personnel = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');

                        $total_day_number_new = $day_number + ($total_number_day_holiday * $number_hour) + ($total_number_day_lt * $number_hour) + ($total_number_day_ch * $number_hour);
                        $salary_income_day = ($salary_bhxh) / $totalDate / $number_hour;
                        $salary_income = $total_day_number_new * $salary_income_day;

                        $day_number_new = ($day_number / $number_hour);
                        $total_day_number = $total_day_number_new / $number_hour;

                        $salary_p3_new = $salary_p3 + $tham_nien + $diligence_salary;

                        $salary_p2_real = ($total_day_number_new * ($salary_p2 / $totalDate / $number_hour)) * $weight_p2 / 100;
                        $salary_p3_real = 0;
                        if ($check_p3 == 1){
                            $salary_p3_real = ($total_day_number_new * (($salary_p3 + $tham_nien) / $totalDate / $number_hour));
                            $salary_p3_real = $salary_p3_real + $diligence;
                        }
                        $salary_kpi_real = $salary_income + $salary_p2_real + $salary_p3_real;


                        $check_bhxh = $personnel['check_bhxh'];
                        $check_union = $personnel['check_union'];
                        $deduct_bhxh = 0;
                        $deduct_bhyt = 0;
                        $deduct_bhtn = 0;
                        $deduct_union = 0;
                        if ($number_day_bhxh >= 0) {
                            if ($check_bhxh == 1) {
                                $deduct_bhxh = ($salary_bhxh * DEDUCT_BHXH) / 100;
                                $deduct_bhyt = ($salary_bhxh * DEDUCT_BHYT) / 100;
                                $deduct_bhtn = ($salary_bhxh * DEDUCT_BHTN) / 100;
                            } else {
                                $deduct_bhxh = 0;
                                $deduct_bhyt = 0;
                                $deduct_bhtn = 0;
                            }
                        }

                        if ($check_union == 1) {
                            $deduct_union = $salary_bhxh * (UNION / 100);
                        } else {
                            $deduct_union = 0;
                        }

                        $bhxh_company = ($salary_bhxh * BHDN) / 100;

                        $total_allowance_other = 0;
                        $total_reduce_other = 0;

                        $arrAllowance = [];
                        if (!empty($dtAllowance)) {
                            foreach ($dtAllowance as $kk => $vv) {
                                if (isset($arrAll['allowance_other_'.$vv['id']][$value.'_'.$staff_id])) {
                                    $allowance_other_new = number_unformat($arrAll['allowance_other_'.$vv['id']][$value.'_'.$staff_id]);
                                    $total_allowance_other += $allowance_other_new;
                                    $arrAllowance[] = [
                                        'category_id' => $vv['id'],
                                        'staff_id' => $staff_id,
                                        'amount' => $allowance_other_new,
                                        'type' => 1,
                                    ];
                                }
                            }
                        }
                        $total_allowance_new_vat = $total_allowance_other;
                        $arrReduce = [];
                        if (!empty($dtReduce)) {
                            foreach ($dtReduce as $kk => $vv) {
                                if (isset($arrRedu['reduce_other_'.$vv['id']][$value.'_'.$staff_id])) {
                                    $allowance_reduce = number_unformat($arrRedu['reduce_other_'.$vv['id']][$value.'_'.$staff_id]);
                                    $total_reduce_other += $allowance_reduce;
                                    $arrReduce[] = [
                                        'category_id' => $vv['id'],
                                        'staff_id' => $staff_id,
                                        'amount' => $allowance_reduce,
                                        'type' => 2,
                                    ];
                                }
                            }
                        }

                        if (isset($data_json_payment_post[$value])) {
                            $payrollPayment = $data_json_payment_post[$value];
                        } else {
                            $payrollPayment = null;
                        }
                        $payrollPaymentJson = json_decode($payrollPayment);
                        $total_payment = 0;
                        $arr_payment = [];
                        if (!empty($payrollPaymentJson)) {
                            foreach ($payrollPaymentJson as $k => $v) {
                                $total_payment += $v->total_sub;
                                $arr_payment [] = [
                                    'id' => $v->payrollPayment,
                                    'total_sub' => $v->total_sub,
                                ];
                            }
                        }
                        $deduct_advance = $total_payment;

                        $allowance_rice_money = ($allowance_rice * $rice_money);

                        $allowance_rice_money_tc = $allowance_rice_tc * $rice_money;
                        $allowance_rice_money_all = $allowance_rice_money + $allowance_rice_money_tc;

                        $allowance_business_fee  = (($salary_bhxh / $totalDate / $number_hour) * $total_weekday * get_option('coefficient')) + (($salary_bhxh / $totalDate / $number_hour) * $total_sunday * get_option('coefficient_sunday')) + (($salary_bhxh / $totalDate / $number_hour) * $total_holiday * get_option('coefficient_holiday'))
                            + (($salary_bhxh / $totalDate / $number_hour) * $total_weekday_night * get_option('coefficient_default_night')) + (($salary_bhxh / $totalDate / $number_hour) * $total_sunday_night * get_option('coefficient_sunday_night'));

                        $money_vat = get_option('money_vat');
                        $money_reduce = get_option('money_reduce');
                        $rice_money_max = get_option('rice_money_max');

                        if (($allowance_rice_money_all) > $rice_money_max){
                            $rice_money_max_new = $rice_money_max;
                        } else {
                            $rice_money_max_new = $allowance_rice_money_all;
                        }

                        $allowance_rice_money_diff = ($rice_money_max_new - $rice_money_max);
                        if ($allowance_rice_money_diff < 0){
                            $allowance_rice_money_diff = 0;
                        }

                        $family_deduction = ($number_reduce * $money_reduce) + $money_vat;


                        $totalMoneyTc = (($salary_bhxh / $totalDate / $number_hour) * $total_weekday * get_option('coefficient')) * 50 / 150;
                        $totalMoneyCN = (($salary_bhxh / $totalDate / $number_hour) * $total_sunday * get_option('coefficient_sunday')) * 100 / 200;
                        $totalMoneyHoliday = (($salary_bhxh / $totalDate / $number_hour) * $total_holiday * get_option('coefficient_holiday')) * 200 / 300;
                        $totalMoneyTcNight = (($salary_bhxh / $totalDate / $number_hour) * $total_weekday_night * get_option('coefficient_default_night')) * 110 / 210;
                        $totalMoneyCNNight = (($salary_bhxh / $totalDate / $number_hour) * $total_sunday_night * get_option('coefficient_sunday_night')) * 170 / 270;

                        $tax_exemption = $rice_money_max_new + $totalMoneyTc + $totalMoneyCN + $totalMoneyHoliday + $totalMoneyTcNight + $totalMoneyCNNight;

                        //chịu thuế tc
                        $baseHourlyRate = ($salary_bhxh / $totalDate / $number_hour);
                        $totalHours = $total_weekday + $total_sunday + $total_holiday + $total_weekday_night + $total_sunday_night;
                        $taxableMoneyTC = $baseHourlyRate * $totalHours;

                        $taxable_income = ($salary_kpi_real + $total_allowance_new_vat + $allowance_rice_money_diff + $taxableMoneyTC);

                        $tax_collection = $taxable_income - $family_deduction - $deduct_bhxh - $deduct_bhyt - $deduct_bhtn;

                        $total_money_vat_check = $tax_collection;
                        if ($total_money_vat_check < 0) {
                            $total_money_vat_check = 0;
                        }
                        $tax_collection = $tax_collection < 0 ? 0 : $tax_collection;

                        $percent_vat = 0;
                        $total_reduce_vat = 0;
                        if ($total_money_vat_check <= 10000000) {
                            $percent_vat = 5;
                            $total_reduce_vat = 0;
                        } elseif ($total_money_vat_check <= 30000000) {
                            $percent_vat = 10;
                            $total_reduce_vat = 500000;
                        } elseif ($total_money_vat_check <= 60000000) {
                            $percent_vat = 20;
                            $total_reduce_vat = 3500000;
                        } elseif ($total_money_vat_check <= 100000000) {
                            $percent_vat = 30;
                            $total_reduce_vat = 9500000;
                        } else {
                            $percent_vat = 35;
                            $total_reduce_vat = 14500000;
                        }

                        $total_vat = 0;
                        $total_vat = ($total_money_vat_check * $percent_vat / 100) - $total_reduce_vat;

                        $total = $grand_total_kt - $grand_total_kl + $salary_kpi_real + $allowance_business_fee + $total_allowance_other + $allowance_rice_money + $allowance_rice_money_tc + $complete_permission + $salary_compensation + $other_adjustments - $money_hour_late - $deduct_bhxh - $deduct_bhyt - $deduct_bhtn - $deduct_union - $total_reduce_other - $total_vat;
                        $total_allowance_other += $allowance_rice_money;
                        $total_allowance_other += $allowance_rice_money_tc;
                        $total_reduce_other += $deduct_bhxh + $deduct_bhyt + $deduct_bhtn + $deduct_union;

                        $full_name = vn_to_str($personnel['firstname'].' '.$personnel['lastname']);
                        $code_name = $personnel['code'];
                        $code = 'BL_'.$code_name.'_'.$full_name.'_'.$month.$year;
                        $arrPayrollItem[] = [
                            'code' => $code,
                            'staff_id' => $staff_id,
                            'day_number' => $day_number,
                            'day_number_new' => $day_number_new,
                            'day_holiday' => $total_number_day_holiday,
                            'day_lt' => $total_number_day_lt,
                            'day_ch' => $total_number_day_ch,
                            'total_day_number' => $total_day_number,
                            'salary' => ($salary_bhxh + $salary_p2 + $salary_p3),
                            'salary_kpi' => ($salary_bhxh + $salary_p2 + $salary_p3_new),
                            'salary_bhxh' => $salary_bhxh,
                            'salary_bhxh_new' => $salary_bhxh_new,
                            'salary_responsibility' => $salary_responsibility,
                            'salary_position' => $salary_position,
                            'responsibility_salary' => 0,
                            'sales' => $sales,
                            'gasonline_cars' => $gasonline_cars,
                            'phone' => $phone,
                            'motel' => $motel,
                            'concurrently' => $concurrently,
                            'seniority' => $seniority,
                            'business_fee_staff' => $business_fee_staff,
                            'number_reduce' => $number_reduce,
                            'business_fee_difference' => $business_fee_difference,
                            'allowance_diff' => $allowance_diff,
                            'complete_permission' => $complete_permission,
                            'salary_compensation' => $salary_compensation,
                            'other_adjustments' => $other_adjustments,
                            'day_number_off' => $day_number_off,
                            'day_number_off_new' => 0,
                            'hour_late' => $hour_late,
                            'salary_off' => 0,
                            'allowance' => $personnel['allowance'],
                            'salary_income' => $salary_income,
                            'money_hour_late' => $money_hour_late,
                            'allowance_responsibility' => $allowance_responsibility,
                            'allowance_other' => $allowance_other,
                            'allowance_manu' => $allowance_manu,
                            'allowance_western' => $allowance_western,
                            'allowance_business_fee' => !empty($allowance_business_fee) ? $allowance_business_fee : 0,
                            'allowance_rice' => $allowance_rice,
                            'allowance_rice_tc' => $allowance_rice_tc,
                            'allowance_rice_money' => $allowance_rice_money,
                            'bonus_holiday' => $bonus_holiday,
                            'deduct_bhxh' => $deduct_bhxh,
                            'deduct_bhyt' => $deduct_bhyt,
                            'deduct_bhtn' => $deduct_bhtn,
                            'deduct_union' => $deduct_union,
                            'deduct_advance' => $deduct_advance,
                            'total_reduce_other' => $total_reduce_other,
                            'total_allowance_other' => $total_allowance_other,
                            'total' => $total,
                            'total_real' => $total,
                            'business_fee_boiler_calculate_item_id' => !empty($business_fee_boiler_calculate_item_id) ? $business_fee_boiler_calculate_item_id : 0,
                            'data_json_payment' => $payrollPayment,
                            'arr_payment' => $arr_payment,
                            'arrAllowance' => $arrAllowance,
                            'arrReduce' => $arrReduce,
                            'grand_total_kt' => $grand_total_kt,
                            'grand_total_kl' => $grand_total_kl,
                            'salary_3p_id' => $salary_3p_id,
                            'weight_p2' => $weight_p2,
                            'weight_p3' => $weight_p3,
                            'percent_vat' => $percent_vat,
                            'salary_p2' => $salary_p2,
                            'salary_p3' => $salary_p3,
                            'salary_p3_new' => $salary_p3_new,
                            'salary_kpi_real' => $salary_kpi_real,
                            'tham_nien' => $tham_nien,
                            'diligence' => $diligence,
                            'salary_p2_real' => $salary_p2_real,
                            'salary_p3_real' => $salary_p3_real,
                            'total_vat' => $total_vat,
                            'tax_exemption' => $tax_exemption,
                            'taxable_income' => $taxable_income,
                            'family_deduction' => $family_deduction,
                            'tax_collection' => $tax_collection,
                            'total_weekday' => $total_weekday,
                            'total_sunday' => $total_sunday,
                            'total_holiday' => $total_holiday,
                            'total_weekday_night' => $total_weekday_night,
                            'total_sunday_night' => $total_sunday_night,
                            'check_p3' => $check_p3,
                            'diligence_salary' => $diligence_salary,
                            'bhxh_company' => $bhxh_company,
                            'number_hour' => $number_hour,
                            'total_date' => $totalDate,
                        ];
                    }
                }
                if (empty($arrPayrollItem)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có dữ liệu');
                    echo json_encode($data);
                    die;
                }
                

                $Idpayroll = 0;
                $this->db->select('*');
                $this->db->from('tbl_payroll');
                $this->db->where('tbl_payroll.month', $month);
                $this->db->where('tbl_payroll.year', $year);
                $payroll = $this->db->get()->row_array();
                if (!empty($payroll)) {
                    $Idpayroll = $payroll['id'];
                } else {
                    $this->db->insert('tbl_payroll', [
                        'month' => $month,
                        'year' => $year,
                        'date_created' => date('Y-m-d H:i'),
                        'staff_id' => get_staff_user_id(),
                    ]);
                    $Idpayroll = $this->db->insert_id();
                }
                if ($Idpayroll) {

                    foreach ($arrPayrollItem as $key => $value) {
                        $paymentArr = $value['arr_payment'];
                        $arrAllowance = $value['arrAllowance'];
                        $arrReduce = $value['arrReduce'];
                        unset($value['arr_payment']);
                        unset($value['arrAllowance']);
                        unset($value['arrReduce']);
                        $value['payroll_id'] = $Idpayroll;
                        $this->db->insert('tbl_payroll_item', $value);
                        $payroll_item_id = $this->db->insert_id();
                        if ($payroll_item_id) {
                            if (!empty($paymentArr)) {
                                foreach ($paymentArr as $kk => $vv) {
                                    $this->db->insert('tbl_payroll_payment_item', [
                                        'payroll_item_id' => $payroll_item_id,
                                        'payroll_id' => $Idpayroll,
                                        'payroll_payment_id' => $vv['id'],
                                        'total' => $vv['total_sub'],
                                    ]);
                                }
                            }

                            if (!empty($arrAllowance)) {
                                foreach ($arrAllowance as $kk => $vv) {
                                    $arrAllowance[$kk]['payroll_item_id'] = $payroll_item_id;
                                    $arrAllowance[$kk]['payroll_id'] = $Idpayroll;
                                }
                                $this->db->insert_batch('tbl_allowance_reduce_payroll', $arrAllowance);
                            }

                            if (!empty($arrReduce)) {
                                foreach ($arrReduce as $kk => $vv) {
                                    $arrReduce[$kk]['payroll_item_id'] = $payroll_item_id;
                                    $arrReduce[$kk]['payroll_id'] = $Idpayroll;
                                }
                                $this->db->insert_batch('tbl_allowance_reduce_payroll', $arrReduce);
                            }


                        }
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
            $data['title'] = lang('Tạo tính lương');
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/payroll/payroll_salary'),
                    'page' => lang('Bảng lương'),
                ),
                array('link' => '#', 'page' => $data['title']),
            ];
            $this->load->view('admin/payroll/payroll_salary', $data);
        }
    }

    public function loadPayrollSalary()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $branch_search = $this->input->get('branch_search');

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $this->db->where_not_in('tbl_allowance_reduce.id',[ALLOWANCE_CHUYENCAN,ALLOWANCE_THAMNIEN]);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();

        $countAllowance = 3 + count($dtAllowance);
        $countReduce = 5 + count($dtReduce);

        $salary_minimum_new = number_unformat(get_option('salary_minimum_new'));
        $rice_money = get_option('rice_money');

        $tHead = '';
        $html = '';
        $tHead = '<tr>
            <th rowspan="2" class="text-center" style="min-width: 50px;">'.lang('tnh_numbers').'</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">'.lang('Mã NV').'</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">'.lang('Họ Tên').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Chức vụ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Ngày vào làm').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Trạng thái').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng Mức Thu Nhập Theo KPI (P1+P2+P3)').'</th>
            <th colspan="3" class="text-center" style="min-width: 100px;">'.lang('Mức P1 (BHXH Theo Qui Chế Vùng)').'</th>
            <th colspan="1" class="text-center" style="min-width: 100px;">'.lang('Mức P2 (Phụ Cấp Năng Lực Theo KPI)').'</th>
            <th colspan="4" class="text-center" style="min-width: 100px;">'.lang('Mức P3 (Thu Nhập Cống Hiến Theo KPI)').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng Thu Nhập (Thỏa thuận)').'</th>
            <th colspan="2" class="text-center" style="min-width: 100px;">'.lang('Giờ Làm Thực Tế').'</th>
            <th colspan="4" class="text-center" style="min-width: 100px;">'.lang('Ngày nghỉ').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng ngày công hưởng lương').'</th>';
        $tHead .= '<th colspan="1" class="text-center" style="min-width: 100px;">'.lang('P1').'</th>';
        $tHead .= '<th colspan="2" class="text-center" style="min-width: 100px;">'.lang('P2').'</th>';
        $tHead .= '<th colspan="3" class="text-center" style="min-width: 100px;">'.lang('P3').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng Thu Nhập Thực Tế Theo KPI (P1+P2+P3)').'</th>';
        $tHead .= '<th colspan="2" class="text-center" style="min-width: 100px;">'.lang('Trừ đi trễ về sớm').'</th>';
        $tHead .= '
            <th colspan="'.$countAllowance.'" class="text-center" style="min-width: 80px;">'.lang('Phụ cấp khác').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng phụ cấp').'</th>
            <th colspan="6" class="text-center" style="min-width: 80px;">'.lang('Tăng ca').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('BHDN ('.BHDN.' %)').'</th>
            <th colspan="'.$countReduce.'" class="text-center" style="min-width: 80px;">'.lang('Khấu trừ').'</th>
            <th colspan="6" class="text-center" style="min-width: 80px;">'.lang('Khấu trừ thuế TNCN').'</th>
            <th colspan="2" class="text-center" style="min-width: 80px;">'.lang('Khen thưởng kỷ luật').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Bù lương').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Điều chỉnh khác').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Hoàn phép năm').'</th>
            <th colspan="4" class="text-center" style="min-width: 80px;">'.lang('Thực lãnh').'</th>
        </tr>';
        $tHead .= '<tr>
            <th class="text-center" style="min-width: 80px;">'.lang('Hệ số lương vị trí').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Hệ số lương chức vụ').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Mức lương vị trí (LCB)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Mức P2 (Theo năng lực)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thu Nhập Cống Hiến').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thâm niên').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Mức chuyên cần').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Mức P3').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Số giờ công').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Số ngày công').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Phép năm').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('VR hưởng lương (hiếu hỉ)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Nghỉ không hưởng lương (không lương/không phép)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lương P1').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Điểm KPI').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thu nhập P2 thực tế').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Chuyên cần thực tế').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Mở P3').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thu nhập P3 thực tế').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Số giờ').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Số tiền').'</th>';
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $tHead .= '<th class="text-center" style="min-width: 80px;">'.$value['name'].'</th>';
            }
        }
        $tHead .= '<th class="text-center" style="min-width: 80px;">'.lang('Ngày cơm hành chánh').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Ngày cơm tăng ca').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tổng Tiền cơm').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Giờ TC ngày thường(1.5)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Giờ TC chủ nhật(2.0)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Giờ TC lễ tết(3.0)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Giờ TC đêm thường('.get_option('coefficient_default_night').')').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Giờ TC đêm chủ nhật('.get_option('coefficient_sunday_night').')').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lương tăng ca').'</th>';
        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $tHead .= '<th class="text-center" style="min-width: 80px;">'.$value['name'].'</th>';
            }
        }
        $tHead .= '<th class="text-center" style="min-width: 80px;">'.lang('8% BHXH').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1.5% BHYT').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1% BHTN').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang(''.UNION.'% Đoàn phí').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tổng khấu trừ BHXH + Đoàn phí + Khấu trừ').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thuế suất').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Giảm trừ gia cảnh').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thu nhập miễn thuế').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thu nhập chịu thuế').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thu nhập tính thuế').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thuế TNCN').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Khen thưởng KPIs').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Kỷ luật KPIs').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tổng thực lãnh').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Đã tạm ứng').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thực lãnh P1+P2').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Thực lãnh P3').'</th>
        </tr>';

        $isPayroll = "(
            SELECT COUNT(*)
            FROM tbl_payroll
            LEFT JOIN tbl_payroll_item on tbl_payroll_item.payroll_id = tbl_payroll.id
            WHERE tbl_payroll.month = '$month' AND tbl_payroll.year = '$year' AND tblstaff.staffid = tbl_payroll_item.staff_id
        )";

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        $countPaidHolidayLT = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE (type = 'TDL' OR type = 'TAL' OR type = 'NCT' OR type = 'QTLĐ' OR type = 'QK' OR type = 'GTHV') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_lt";

        $countPaidHolidayCH = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'CH') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_ch";

        $countPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                SUM(IF((type = 'TDL' OR type = 'TAL' OR type = 'NCT' OR type = 'QTLĐ' OR type = 'QK' OR type = 'GTHV'),1,COALESCE((tbl_paid_holiday_leave_detail.number_date),0))) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            LEFT JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'AL' OR type = 'TDL' OR type = 'TAL' OR type = 'NCT' OR type = 'QTLĐ' OR type = 'QK' OR type = 'GTHV' OR type = 'CH') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday";

        $countPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'AL/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_new";

        $countNotPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'UP' OR type = 'TS' OR type = 'OD' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday";

        $countNotPaidHolidayKP = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'KP') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_kp";

        $countNotPaidHolidayUP = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'UP') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_up";

        $countNotPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'UP/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_new";

        $countNumberDay = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type != 'X' OR (type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day";

        $countNumberDayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(number_day),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type = 'X' AND number_day = '0.5' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day_new";

        $countHour = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(count_hour_late_new + count_hour_late_checkout),0) as count_hour_late,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count
            FROM tbl_timekeeping_detail
            WHERE ((type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_hour";

        $countHourBhxh = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count
            FROM tbl_timekeeping_detail
            WHERE (count_hour - count_hour_overtime) >= 4 AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_hour_bhxh";

        
        $GetTotalSuggestBonuskt = "(
            SELECT 
                tbl_decision_bonus_discipline.object_id as staff_id,
                SUM(grand_total) as grand_total
            FROM tbl_decision_bonus_discipline
            INNER JOIN tblinternal_proposal ON tblinternal_proposal.decision_bonus_discipline_id = tbl_decision_bonus_discipline.id
            INNER JOIN tbl_internal_proposal_process ON tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 1
            WHERE tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 1 AND object_type = 'staff' AND tbl_decision_bonus_discipline.status = 1 AND MONTH(tbl_decision_bonus_discipline.date) = $month AND YEAR(tbl_decision_bonus_discipline.date) = $year
            GROUP BY tbl_decision_bonus_discipline.object_id,object_type
        ) GetTotalSuggestBonuskt";
        // AND tbl_decision_bonus_discipline.branch_id = $branch_search 
        $GetTotalSuggestBonuskl = "(
            SELECT 
                tbl_decision_bonus_discipline.object_id as staff_id,
                SUM(grand_total) as grand_total
            FROM tbl_decision_bonus_discipline
            INNER JOIN tblinternal_proposal ON tblinternal_proposal.decision_bonus_discipline_id = tbl_decision_bonus_discipline.id
            INNER JOIN tbl_internal_proposal_process ON tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 1
            WHERE tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 2 AND object_type = 'staff' AND tbl_decision_bonus_discipline.status = 1 AND MONTH(tbl_decision_bonus_discipline.date) = $month AND YEAR(tbl_decision_bonus_discipline.date) = $year
            GROUP BY tbl_decision_bonus_discipline.object_id,object_type
        ) GetTotalSuggestBonuskl";
        // AND tbl_decision_bonus_discipline.branch_id = $branch_search

        $tb_tamp_report = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND MONTH(tblproduction_report.date) = $month AND YEAR(tblproduction_report.date) = $year
            GROUP BY staff_responsible
        ) tb_tamp_report";

        //p3
        $tb_tamp_audit = "(
            SELECT
                tblstaff_departments.staffid as staff_id,
                COUNT(tbl_audit.id) as total_audit
            FROM tbl_audit
            JOIN tbl_room ON tbl_room.id = tbl_audit.dept_id
            JOIN tbldepartments ON tbldepartments.room_id = tbl_room.id
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            WHERE EXISTS (
                SELECT 1
                FROM tbl_audit_checklist
                WHERE tbl_audit_checklist.audit_id = tbl_audit.id
                AND tbl_audit_checklist.status = 'no'
            )
            AND MONTH(tbl_audit.audit_date) = $month AND YEAR(tbl_audit.audit_date) = $year 
            GROUP BY tblstaff_departments.staffid
        ) tb_tamp_audit";

        $tb_tamp_task_process = "(
            SELECT 
                tbltask_assigned.staffid as staff_id,
                COUNT(tbltasks.id) as total_task
            FROM tbltasks
            JOIN tbltask_assigned ON tbltask_assigned.taskid = tbltasks.id
            WHERE tbltasks.id != 0 AND tbltasks.status != 5  AND MONTH(tbltasks.dateadded) = $month AND YEAR(tbltasks.dateadded) = $year 
            GROUP BY tbltask_assigned.staffid
        ) tb_tamp_task_process";

        $tb_tamp_report_process = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                COUNT(tblproduction_report.id) as count_bckph
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND EXISTS (
                SELECT 1 
                FROM tbl_process_production_report 
                WHERE tbl_process_production_report.production_report_id = tblproduction_report.id
                AND tbl_process_production_report.staff_process = 0
             )  AND MONTH(tblproduction_report.date) = $month AND YEAR(tblproduction_report.date) = $year 
            GROUP BY staff_responsible
        ) tb_tamp_report_process";

        $tb_tamp_vi_pham = "(
            SELECT 
                tblproduction_report.staff_responsible as staff_id,
                GROUP_CONCAT((tblproduction_report.kpi_list_criteria_department_id) SEPARATOR ',') as kpi_list_criteria_department_id,
                COUNT(tblproduction_report.id) as violate
            FROM tblproduction_report
            WHERE kpi_list_criteria_department_id != 0 AND tblproduction_report.violate = 1
            AND MONTH(tblproduction_report.date) = $month AND YEAR(tblproduction_report.date) = $year 
            GROUP BY tblproduction_report.staff_responsible
        ) tb_tamp_vi_pham";

        $this->db->select("
            tblstaff.staffid as staffid,
            COALESCE(tbl_setup_shift.number_hour,0) as number_hour,
            COALESCE(tbl_setup_shift.total_date,0) as total_date,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as name,
            tblstaff.code as code,
            tblstaff.day_in as day_in,
            tblstaff.status_work as status_work,
            tblstaff.salary_bhxh as salary_bhxh,
            tblstaff.salary_bhxh_new as salary_bhxh_new,
            tblstaff.allowance as allowance,
            tblstaff.check_bhxh as check_bhxh,
            tblstaff.check_union as check_union,
            tblstaff.coefficient_responsibility as coefficient_responsibility,
            tblstaff.coefficient_position as coefficient_position,
            tblstaff.responsibility_salary as responsibility_salary,
            tblstaff.sales as sales,
            tblstaff.phone as phone,
            tblstaff.gasonline_cars as gasonline_cars,
            tblstaff.motel as motel,
            tblstaff.concurrently as concurrently,
            tblstaff.business_fee_staff as business_fee_staff,
            tblstaff.seniority as seniority,
            tblstaff.number_reduce as number_reduce,
            tblstaff.role_level_id as role_level_id,
            tblroles.name as name_role,
            tblroles.roleid as roleid,
            COALESCE(tb_count_paid_holiday.count,0) + (COALESCE(tb_count_paid_holiday_new.count,0) * 0.5 ) as totalHoliday, 
            COALESCE(tb_count_not_paid_holiday.count,0) + (COALESCE(tb_count_not_paid_holiday_new.count,0) * 0.5 ) as totalNotHoliday, 
            COALESCE(tb_count_number_day.count,0) as number_day, 
            COALESCE(tb_count_number_day_new.count,0) as number_day_new, 
            COALESCE(tb_count_hour.count_hour_late,0) as count_hour_late,
            COALESCE(tb_count_hour.count,0) as count_hour,
            COALESCE(tb_count_paid_holiday_new.count_hour,0) + COALESCE(tb_count_paid_holiday.count_hour,0) as count_hour_phep, 
            COALESCE(tb_count_not_paid_holiday_new.count_hour,0) + COALESCE(tb_count_not_paid_holiday.count_hour,0) as count_hour_kphep, 
            COALESCE(tb_count_not_paid_holiday_kp.count,0) as number_day_kp, 
            COALESCE(tb_count_not_paid_holiday_up.count,0) as number_day_up, 
            (COALESCE(tb_count_paid_holiday_new.count,0) * 0.5 ) as number_day_al_new,
            (COALESCE(tb_count_not_paid_holiday_new.count,0) * 0.5 ) as number_day_up_new,
            COALESCE(tb_count_paid_holiday_lt.count,0) as number_day_lt, 
            COALESCE(tb_count_paid_holiday_ch.count,0) as number_day_ch, 
            COALESCE(tb_count_hour_bhxh.count,0) as number_day_bhxh, 
            COALESCE(GetTotalSuggestBonuskt.grand_total,0) as grand_total_kt, 
            COALESCE(GetTotalSuggestBonuskl.grand_total,0) as grand_total_kl, 
            COALESCE(tb_tamp_report.weight_p2,0) as weight_p2, 
            COALESCE(tb_tamp_report.weight_p3,0) as weight_p3, 
            ROUND(DATEDIFF(CURDATE(),tblstaff.day_in) / 365,2) as seniority_staff,
            tb_tamp_audit.total_audit,
            tb_tamp_task_process.total_task,
            tb_tamp_report_process.count_bckph,
            tb_tamp_vi_pham.violate,
            ", false);
        $this->db->from('tblstaff');
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'inner');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'inner');
        $this->db->join('tbl_setup_shift', 'tbl_setup_shift.id = tblstaff.setup_shift_id', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join($tb_tamp_report, 'tb_tamp_report.staff_id = tblstaff.staffid', 'left');
        $this->db->join($tb_tamp_audit, 'tb_tamp_audit.staff_id = tblstaff.staffid', 'left');
        $this->db->join($tb_tamp_task_process, 'tb_tamp_task_process.staff_id = tblstaff.staffid', 'left');
        $this->db->join($tb_tamp_report_process, 'tb_tamp_report_process.staff_id = tblstaff.staffid', 'left');
        $this->db->join($tb_tamp_vi_pham, 'tb_tamp_vi_pham.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countPaidHoliday",
            'tb_count_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$GetTotalSuggestBonuskt",
            'GetTotalSuggestBonuskt.staff_id = tblstaff.staffid',
            'left');    
        $this->db->join("$GetTotalSuggestBonuskl",
            'GetTotalSuggestBonuskl.staff_id = tblstaff.staffid',
            'left');        
        $this->db->join("$countPaidHolidayNew",
            'tb_count_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_new.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHoliday",
            'tb_count_not_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayNew",
            'tb_count_not_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_new.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayKP",
            'tb_count_not_paid_holiday_kp.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_kp.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayUP",
            'tb_count_not_paid_holiday_up.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_up.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNumberDay",
            'tb_count_number_day.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNumberDayNew",
            'tb_count_number_day_new.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day_new.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countHour",
            'tb_count_hour.timekeeping_id = tbl_timekeeping.id AND tb_count_hour.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countPaidHolidayLT",
            'tb_count_paid_holiday_lt.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_lt.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countPaidHolidayCH",
            'tb_count_paid_holiday_ch.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_ch.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countHourBhxh",
            'tb_count_hour_bhxh.timekeeping_id = tbl_timekeeping.id AND tb_count_hour_bhxh.staff_id = tblstaff.staffid',
            'left');
        $this->db->where('(tblstaff.check_salary = 0 AND tblstaff.status_work != 2)');
        $this->db->where('tblstaff.branch_salary', $branch_search);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $this->db->where("($isPayroll = 0)");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        if($this->input->get('test')) {
            $this->db->where('tblstaff.staffid', 391);
        }
        $personnel = $this->db->get()->result_array();
//        print_arrays($personnel);

        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
        $this->db->where('tbl_timekeeping_detail.check_sun', 0);
        $this->db->group_by('tbl_timekeeping_detail.day');
        $totalDate = $this->db->count_all_results();

        $totalDate = get_option('day_work');
        $hour_day = get_option('hour_day');

        $this->db->select('
            tbl_business_fee_boiler_overtime_detail.*,
            tbl_business_fee_boiler_overtime.staff_id as staff_id
        ');
        $this->db->from('tbl_business_fee_boiler_overtime');
        $this->db->join('tbl_business_fee_boiler_overtime_detail',
            'tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id');
        $this->db->where('tbl_business_fee_boiler_overtime.month', $month);
        $this->db->where('tbl_business_fee_boiler_overtime.year', $year);
        $this->db->where('tbl_business_fee_boiler_overtime_detail.status', 1);
        $this->db->where('(tbl_business_fee_boiler_overtime_detail.weekday != 0 OR tbl_business_fee_boiler_overtime_detail.sunday != 0 OR tbl_business_fee_boiler_overtime_detail.holiday != 0)');
        $dtOvertimeNew = $this->db->get()->result_array();
        $arrOvertime = [];
        if (!empty($dtOvertimeNew)){
            foreach ($dtOvertimeNew as $key => $value){
                $arrOvertime[$value['staff_id']][] = $value;
            }
        }

        //Lấy hợp đồng để check có P3 không
        if (!empty($personnel)) {
            $staff_ids = array_column($personnel, 'staffid');
            //Lấy ngày kết thúc của tháng và năm hiện tại
            $end_date = date("Y-m-t", strtotime("$year-$month-01"));
            $this->db->select('tbl_contract_labor.staff_id');
            $this->db->from('tbl_contract_labor');
            $this->db->where('tbl_contract_labor.status', 1);
            $this->db->where_in('tbl_contract_labor.staff_id', $staff_ids);
            $this->db->where('tbl_contract_labor.date_start <=', $end_date);
            $this->db->group_start();
            $this->db->where('(tbl_contract_labor.date_end >= "'.$end_date.'" OR tbl_contract_labor.date_end IS NULL)');
            $this->db->or_where('tbl_contract_labor.date_sign >=', $end_date);
            $this->db->group_end();
            $contracts = $this->db->get()->result_array();

            // Tạo mảng để lưu trữ hợp đồng theo staff_id
            $contract_by_staff = [];
            if (!empty($contracts)) {
                foreach ($contracts as $contract) {
                    $staff_id = $contract['staff_id'];
                    if (!isset($contract_by_staff[$staff_id])) {
                        $contract_by_staff[$staff_id] = 1;
                    }
                }
            }
        }

        $index = 0;
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {
                $staffid = $value['staffid'];
                $totalDate = $value['total_date'];
                $number_hour = $value['number_hour'];

                $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                $tdCode = '<div class="td-code">
                    '.$value['code'].'
                </div>';
                $tdNameStaff = '<div class="td-name-staff">
                    '.$value['name'].'
                </div>';
                $tdRole = '<div class="td-role">
                    '.$value['name_role'].'
                </div>';
                $tdDate = '<div class="td-date">
                    '.(!empty($value['day_in']) ? _dhau($value['day_in']) : '').'
                </div>';
                $htmlStatusWork = '';
                if ($value['status_work'] == 0){
                    $htmlStatusWork = 'TV';
                } elseif ($value['status_work'] == 1){
                    $htmlStatusWork = 'CT';
                }
                $tdStatus = '<div class="td-status-work">
                    '.$htmlStatusWork.'
                </div>';

                $totalMonth = (!empty($value['day_in']) ? getDiffDayMonth(_dhau($value['day_in']),date('d/m/Y')) : 0);

                $this->db->select('tbl_salary_3p.salary_p1,tbl_salary_3p.salary_p2,tbl_salary_3p.salary_p3,tbl_salary_3p.id as salary_3p_id');
                $this->db->from('tbl_salary_3p');
                $this->db->join('tbl_grade','tbl_grade.id = tbl_salary_3p.grade_id');
                $this->db->where('tbl_salary_3p.role_id',$value['roleid']);
                $this->db->where('tbl_salary_3p.role_level_id',$value['role_level_id']);
                $this->db->where('tbl_grade.seniority_from_month <=',$totalMonth);
                $this->db->where('tbl_grade.seniority_to_month >=',$totalMonth);
                $this->db->where('tbl_salary_3p.effective_from <=',date('Y-m-d'));
                $this->db->where('tbl_salary_3p.effective_to >=',date('Y-m-d'));
                $this->db->where('tbl_salary_3p.status',1);
                $dtSalary3P = $this->db->get()->row_array();

                $salary_3p_id = $dtSalary3P['salary_3p_id'] ?? 0;
                $salary_bhxh = $dtSalary3P['salary_p1'] ?? 0;
                $salary_p2 = $dtSalary3P['salary_p2'] ?? 0;
                $salary_p3 = $dtSalary3P['salary_p3'] ?? 0;
                $weight_p2 = (100 - $value['weight_p2']) >= 0 ? (100 - $value['weight_p2']) : 0;
                $weight_p3 = (100 - $value['weight_p3']) >= 0 ? (100 - $value['weight_p3']) : 0;


                $number_day_bhxh = number_unformat($value['number_day_bhxh']);
                $count_hour_late = number_unformat($value['count_hour_late']);
                $countHourNew = number_unformat($value['count_hour']);
                $count_hour_phep = number_unformat($value['count_hour_phep']);
                $count_hour_kphep = number_unformat($value['count_hour_kphep']);
                $number_day_kp = number_unformat($value['number_day_kp']);
                $number_day_up = number_unformat($value['number_day_up']);
                $number_day_lt = number_unformat($value['number_day_lt']);
                $number_day_ch = number_unformat($value['number_day_ch']);
                $number_day_al_new = number_unformat($value['number_day_al_new']);
                $number_day_up_new = number_unformat($value['number_day_up_new']);
                $totalHoliday = number_unformat($value['totalHoliday']);
                $totalNotHoliday = number_unformat($value['totalNotHoliday']);
                $number_day_new = number_unformat($value['number_day']) - number_unformat($value['number_day_new']);
                $sales = number_unformat($value['sales']);
                $phone = number_unformat($value['phone']);
                $gasonline_cars = number_unformat($value['gasonline_cars']);
                $motel = number_unformat($value['motel']);
                $concurrently = number_unformat($value['concurrently']);
                $business_fee_staff = number_unformat($value['business_fee_staff']);
                $seniority = number_unformat($value['seniority']);

                $total_number_day = $number_day_new - $totalHoliday - $totalNotHoliday;
                $total_number_day = $total_number_day > 0 ? $total_number_day : 0;

                $countHourNew = $countHourNew + $count_hour_phep + $count_hour_kphep;
                $countHourNew = $countHourNew > 0 ? $countHourNew : 0;
                $countHourNew = $countHourNew + $count_hour_late;

                $total_number_day_salary = $countHourNew + ($totalHoliday * 8);
                if($this->cong_fix == 1) {
                    $total_number_day_salary = ($countHourNew + ($count_hour_late ?? 0)) + ($totalHoliday * 8);
                }

                $total_number_day_salary = $total_number_day_salary > 0 ? $total_number_day_salary : 0;


                $total_number_day_off = ($totalNotHoliday + $number_day_kp) * 8;

                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;height:50px">'.$tdNumber.'</td>';

                $html .= '<td style="min-width: 100px;">'.$tdCode.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdNameStaff.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdRole.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdDate.'</td>';
                $html .= '<td style="min-width: 150px;">'.$tdStatus.'</td>';

                $coefficient_responsibility = $value['coefficient_responsibility'];
                $coefficient_position = $value['coefficient_position'];
                $salary_responsibility = $salary_minimum_new * $coefficient_responsibility;
                $salary_position = $salary_minimum_new * $coefficient_position;

                $salary_responsibility = $salary_p3;
                $salary_position = $salary_p2;

                $dtAllowanceStaffTN = get_table_where('tbl_staff_allowance',
                    ['category_id' => ALLOWANCE_THAMNIEN, 'staff_id' => $staffid], '', 'row_array');
                $tham_nien = !empty($dtAllowanceStaffTN['amount']) ? ($dtAllowanceStaffTN['amount']) : 0;

                $dtAllowanceStaffCC = get_table_where('tbl_staff_allowance',
                    ['category_id' => ALLOWANCE_CHUYENCAN, 'staff_id' => $staffid], '', 'row_array');
                $chuyen_can = !empty($dtAllowanceStaffCC['amount']) ? ($dtAllowanceStaffCC['amount']) : 0;

                $salary_p3_new = $salary_p3 + $tham_nien + $chuyen_can;

                $salaryKPI = $salary_bhxh + $salary_p2 + $salary_p3_new;

                $salary = ($salary_bhxh + $salary_p2 +  $salary_p3);

                if ($salaryKPI == 0) {
                    $html .= '<td style="min-width: 150px;text-align: right"></td>';
                } else {
                    $html .= '<td style="min-width: 150px;text-align: right">'.formatMoney($salaryKPI).'</td>';
                }

                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($coefficient_responsibility) > 0 ? ($coefficient_responsibility) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align: right">'.(!empty($coefficient_position) > 0 ? ($coefficient_position) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($salary_bhxh > 0 ? formatMoney($salary_bhxh) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($salary_p2 > 0 ? formatMoney($salary_p2) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($salary_p3 > 0 ? formatMoney($salary_p3) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($tham_nien > 0 ? formatMoney($tham_nien) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($chuyen_can > 0 ? formatMoney($chuyen_can) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($salary_p3_new > 0 ? formatMoney($salary_p3_new) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($salary > 0 ? formatMoney($salary) : '').'</td>';

                $salary_income_day = ($salary_bhxh) / $totalDate / $number_hour;

                $salary_p2_real = (($salary_p2 / $totalDate / $number_hour) * $total_number_day_salary * $weight_p2) /100;


                $salary_p3_real = 0;
                $check_p3 = 0;
                if ($value['total_audit'] == 0 && $value['total_task'] == 0 && $value['count_bckph'] == 0 && $value['violate'] == 0){
                    $check_p3 = 1;
                }

                //Bổ sung check hợp đồng thỏa điều kiện mới tính P3
                if (empty($contract_by_staff[$staffid])) {
                    $check_p3 = 0;
                }

                if ($check_p3 == 1){
                    $salary_p3_real = (($salary_p3 + $tham_nien) / $totalDate / $number_hour ) * $total_number_day_salary;
                    $salary_p3_real = $salary_p3_real + $chuyen_can;
                }

                $salary_income = $total_number_day_salary * $salary_income_day;
                $check_bhxh = $value['check_bhxh'];
                $deduct_bhxh = 0;
                $deduct_bhyt = 0;
                $deduct_bhtn = 0;
                if ($number_day_bhxh >= 0) {
                    if ($check_bhxh == 1) {
                        $deduct_bhxh = ($salary_bhxh * DEDUCT_BHXH) / 100;
                        $deduct_bhyt = ($salary_bhxh * DEDUCT_BHYT) / 100;
                        $deduct_bhtn = ($salary_bhxh * DEDUCT_BHTN) / 100;
                    } else {
                        $deduct_bhxh = 0;
                        $deduct_bhyt = 0;
                        $deduct_bhtn = 0;
                    }
                }

                if ($value['check_union'] == 1) {
                    $union_salary = $salary_bhxh * (UNION / 100);
                } else {
                    $union_salary = 0;
                }

                $bhxh_company = ($salary_bhxh * BHDN) / 100;

                $deduct_advance = 0;

                $total_number_day_salary_new = ($countHourNew / $number_hour);

                $dtArrOvertime = !empty($arrOvertime[$staffid]) ? $arrOvertime[$staffid] : [];
                $totalOverTime = 0;
                $totalOverTimeCN = 0;
                $totalOverTimeHoliday = 0;

                if (!empty($dtArrOvertime)){
                    foreach ($dtArrOvertime as $kk => $vv){
                        $date = $vv['date'];
                        $totalOverTime += $vv['weekday'];
                        $totalOverTimeCN += $vv['sunday'];
                        $totalOverTimeHoliday += $vv['holiday'];
                    }
                }


                $salary_overtime = ($salary_bhxh / ($totalDate * $number_hour) * $totalOverTime * get_option('coefficient'));
                $salary_overtime_cn = ($salary_bhxh / ($totalDate * $number_hour) * $totalOverTimeCN * get_option('coefficient_sunday'));
                $salary_overtime_holiday = ($salary_bhxh / ($totalDate * $number_hour) * $totalOverTimeHoliday * get_option('coefficient_holiday'));

                $total_rice = ceil($total_number_day_salary_new);

                $total_rice_tc = (($totalOverTime / 3) + ($totalOverTimeCN / 8));

                $total_number_day_holiday = $totalHoliday - $number_day_lt - $number_day_ch;
                $total_number_day_lt = $number_day_lt;

                $total_number_day_ch = $number_day_ch;

                $salary_p1_real = $salary_income;
                $salary_kpi_real = $salary_p1_real + $salary_p2_real + $salary_p3_real;

                $total_day_salary = $total_number_day_salary_new + $total_number_day_holiday + $total_number_day_lt + $total_number_day_ch;

                $day_number_off = $totalNotHoliday + $number_day_kp;
//                if($this->input->get('test')) {
//                    print_arrays($day_number_off);
//                }

                $html .= '<td style="min-width: 120px;text-align:center">
                <input type="text" class="form-control day_number number-format" name="day_number[]" style="width: 120px" value="'.($countHourNew > 0 ? ($countHourNew) : '').'"></td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="total_number_day_new">'.($total_number_day_salary_new > 0 ? $total_number_day_salary_new : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($total_number_day_holiday > 0 ? $total_number_day_holiday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($total_number_day_lt > 0 ? $total_number_day_lt : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($total_number_day_ch > 0 ? $total_number_day_ch : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="day_number_off_new">'.(($totalNotHoliday + $number_day_kp) > 0 ? ($totalNotHoliday + $number_day_kp) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total_day_salary">'.($total_day_salary > 0 ? formatMoneyPayroll($total_day_salary) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_income_html">'.($salary_p1_real > 0 ? formatMoneyPayroll($salary_p1_real) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="weight_p2">'.($weight_p2 > 0 ? formatMoneyPayroll($weight_p2) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_p2_real">'.($salary_p2_real > 0 ? formatMoneyPayroll($salary_p2_real) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control diligence number-format" name="diligence[]" style="width: 120px" value="'.($chuyen_can > 0 ? formatMoneyPayroll($chuyen_can) : '').'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center; '.($check_p3 == 1 ? 'background:yellow' : "").'" class="check_p3">'.($check_p3 == 1 ? 'x' : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_p3_real">'.($salary_p3_real > 0 ? formatMoneyPayroll($salary_p3_real) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_kpi_real">'.($salary_kpi_real > 0 ? formatMoneyPayroll($salary_kpi_real) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">
                <input type="text" class="form-control hour_late number-format" name="hour_late[]" style="width: 120px" value="'.($count_hour_late > 0 ? ($count_hour_late) : '').'"
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_off"></td>';


                $number_day_kp_check = $number_day_kp + $number_day_up;


                $number_day_cp_check = ($totalNotHoliday - $number_day_up - $number_day_up_new);


                $allowance_diff = 0;
                if (!empty($dtAllowance)) {
                    foreach ($dtAllowance as $kk => $vv) {
                        $dtAllowanceStaff = get_table_where('tbl_staff_allowance',
                            ['category_id' => $vv['id'], 'staff_id' => $staffid], '', 'row_array');
                        $allowance_new = !empty($dtAllowanceStaff['amount']) ? ($dtAllowanceStaff['amount']) : 0;

                        if ($vv['type_check'] == 1) {
                            $allowance_new = $allowance_new - ($number_day_kp_check * get_option('diligence_kp'));

                            $allowance_new = $allowance_new - ($number_day_cp_check * get_option('diligence_cp'));
                        }
                        if ($vv['type_check'] == 3) {
                            $allowance_diff += !empty($dtAllowanceStaff['amount']) ? ($dtAllowanceStaff['amount']) : 0;
                        }
                        $allowance_new = $allowance_new < 0 ? 0 : $allowance_new;
                        if ($vv['id'] == ALLOWANCE_THAMNIEN){
                            $allowance_new = $value['seniority_staff'] * 100000;
                        }
                        $html .= '<td style="min-width: 120px;text-align:left">
                            <input type="text" data-id="'.$vv['id'].'" data-staff-id="'.$staffid.'" class="form-control allowance_other_new allowance_other_'.$vv['id'].'_'.$staffid.' number-format" name="allowance_other_'.$vv['id'].'['.$index.'_'.$staffid.']" style="width: 120px" value="'.(!empty($allowance_new) ? formatMoney($allowance_new) : '').'">
                        </td>';
                    }
                }

                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control allowance_rice number-format" name="allowance_rice[]" style="width: 120px" value="'.($total_rice > 0 ? $total_rice : '').'">
                    <input type="hidden" class="form-control allowance_diff number-format" name="allowance_diff[]" style="width: 120px" value="'.($allowance_diff).'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control allowance_rice_tc number-format" name="allowance_rice_tc[]" style="width: 120px" value="'.($total_rice_tc > 0 ? formatMoneyPayroll($total_rice_tc) : '').'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="allowance_rice_money">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <div class="total_allowance text-right"></div>
                </td>';

                $allowance_business_fee = $salary_overtime + $salary_overtime_cn + $salary_overtime_holiday;

                $html .= '<td style="min-width: 120px;text-align:center">'.($totalOverTime > 0 ? $totalOverTime : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($totalOverTimeCN > 0 ? $totalOverTimeCN : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($totalOverTimeHoliday > 0 ? $totalOverTimeHoliday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">
                     <input type="text" class="form-control total_weekday_night number-format" name="total_weekday_night[]" style="width: 120px" value="">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center">
                    <input type="text" class="form-control total_sunday_night number-format" name="total_sunday_night[]" style="width: 120px" value="">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="allowance_business_fee">
                    '.($allowance_business_fee > 0 ? formatMoneyPayroll($allowance_business_fee) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="bhxh_company">
                    '.($bhxh_company > 0 ? formatMoneyPayroll($bhxh_company) : '').'
                </td>';
                if (!empty($dtReduce)) {
                    foreach ($dtReduce as $kk => $vv) {
                        $dtReduceStaff = get_table_where('tbl_staff_reduce',
                            ['category_id' => $vv['id'], 'staff_id' => $staffid], '', 'row_array');
                        $html .= '<td style="min-width: 120px;text-align:left">
                            <input type="text" class="form-control reduce_other_'.$vv['id'].'_'.$staffid.' number-format reduce_other" name="reduce_other_'.$vv['id'].'['.$index.'_'.$staffid.']" style="width: 120px" value="'.(!empty($dtReduceStaff['amount']) ? formatMoney($dtReduceStaff['amount']) : '').'">
                        </td>';
                    }
                }

                //advance payment

                $end_date = '';
                $start_date = '';
                if (!empty($month) && !empty($year)) {
                    $listDate = getAllDateInMonth($month, $year, 'd/m/Y');
                    $end_date = array_pop($listDate);
                    $start_date = reset($listDate);
                }
                $paymentPayroll = '
                COALESCE(
                (SELECT SUM(tbl_payroll_payment_item.total) 
                FROM tbl_payroll_payment_item 
                WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id ),0)
                ';
                $paymentOther = '
                COALESCE(
                (SELECT SUM(tblother_payslips_coupon.total) 
                FROM tblother_payslips_coupon 
                WHERE tblother_payslips_coupon.vouchers_id = tbl_payroll_payment.id AND tblother_payslips_coupon.type_vouchers = 333),0) 
                ';
                $this->db->select("
                    tbl_payroll_payment.id as id,
                    tbl_payroll_payment.code as code,
                    DATE_FORMAT(tbl_payroll_payment.date, '%d-%m-%Y')as date,
                    (tbl_payroll_payment.amount - $paymentOther) as amount,
                    $paymentPayroll as quantity_net,
                ");
                $this->db->from('tbl_payroll_payment');
                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_payment.staff_id');
                $this->db->where('tblstaff.staffid', $staffid);
                if (!empty($start_date)) {
                    // $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=', to_sql_date($start_date));
                }
                if (!empty($end_date)) {
                    $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date));
                }
                $this->db->having('(amount-quantity_net) > 0');
                $payrollPayments = $this->db->get()->result_array();
                $data_json_payment = [];
                if (!empty($payrollPayments)){
                    foreach ($payrollPayments as $k => $v){
                        $paymentPayRoll = get_table_where('tbl_payroll_payment', ['id' => $v['id']], '', 'row_array');
                        $data_json_payment[] = [
                            'payrollPayment' => $v['id'],
                            'total_sub' => ($v['amount'] - $v['quantity_net']),
                            'cal_id' => null,
                            'staff_id' => $staffid,
                            'paymentPayRoll' => $paymentPayRoll,
                        ];
                    }
                }

                $data_json_payment = !empty($data_json_payment) ? json_encode($data_json_payment) : null;


                //end

                $html .= '<td style="min-width: 120px;text-align:right;">'.($deduct_bhxh > 0 ? formatMoneyPayroll($deduct_bhxh) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhyt > 0 ? formatMoneyPayroll($deduct_bhyt) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhyt > 0 ? formatMoneyPayroll($deduct_bhtn) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($union_salary > 0 ? formatMoneyPayroll($union_salary) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total"></td>';

                $html .= '<td style="min-width: 120px;text-align: right" class="percent_vat">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="family_deduction">
                </td>';

                $html .= '<td style="min-width: 120px;text-align: right" class="tax_exemption">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="taxable_income">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="tax_collection">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total_vat"></td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="grand_total_kt">
                    <input style="width:120px" type="hidden" name="grand_total_kt[]" class="form-control grand_total_kt" value="'.$value['grand_total_kt'].'">
                    '.($value['grand_total_kt'] > 0 ? formatMoneyPayroll($value['grand_total_kt']) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="grand_total_kl">
                    <input style="width:120px" type="hidden" name="grand_total_kl[]" class="form-control grand_total_kl" value="'.$value['grand_total_kl'].'">
                    '.($value['grand_total_kl'] > 0 ? formatMoneyPayroll($value['grand_total_kl']) : '').'
                </td>';

                $html .= '<td style="min-width: 120px;">
                      <input type="text" class="form-control salary_compensation number-format" name="salary_compensation[]" style="width: 120px" value="">
                </td>';
                $html .= '<td style="min-width: 120px;">
                      <input type="text" class="form-control other_adjustments number-format" name="other_adjustments[]" style="width: 120px" value="">
                </td>';
                $html .= '<td style="min-width: 120px;">
                      <input type="text" class="form-control complete_permission number-format" name="complete_permission[]" style="width: 120px" value="">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right"><span class="total_real"></span></td>';
                $html .= '<td style="min-width: 120px;">
                    <div class="td-payment">
                        <div class="sub"></div>
                        <div class="" style="display: flex;justify-content: flex-end;"><a onclick="addPayrollPayment(this,'.$index.')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Thêm tạm ứng</a></div>
                        <div class="show_payment" style="margin-top: 5px;"></div>
                        <input type="hidden" name="data_json_payment['.$index.']" class="form-control data_json_payment" value="'.tnh_htmlentities($data_json_payment).'">
                        <div class="text-error" style="color: red"></div>
                    </div>
                </td>';

                $html .= '<td style="min-width: 120px;text-align:right"><span class="total_real_p1_p2"></span></td>';
                $html .= '<td style="min-width: 120px;text-align:right"><span class="total_real_p3"></span>
                <input style="width:100px" type="hidden" class="form-control salary_3p_id" value="'.($salary_3p_id).'" name="salary_3p_id[]">
                <input style="width:100px" type="hidden" class="form-control number_reduce" value="'.($value['number_reduce']).'" name="number_reduce[]">
                <input style="width:100px" type="hidden" class="form-control salary_bhxh" name="salary_bhxh[]" value="'.($salary_bhxh).'">
                <input style="width:100px" type="hidden" class="form-control tham_nien_input" name="tham_nien[]" value="'.$tham_nien.'">
                <input style="width:100px" type="hidden" class="form-control diligence_salary" name="diligence_salary[]" value="'.$chuyen_can.'">
                <input style="width:100px" type="hidden" class="form-control allowance" value="'.$value['allowance'].'">
                <input style="width:100px" type="hidden" class="form-control salary_bhxh_new" name="salary_bhxh_new[]" value="'.$value['salary_bhxh_new'].'">
                <input style="width:100px" type="hidden" name="salary_responsibility[]" class="form-control salary_responsibility" value="'.$salary_responsibility.'">
                <input style="width:100px" type="hidden" name="salary_position[]" class="form-control salary_position" value="'.$salary_position.'">
                <input style="width:100px" type="hidden" name="weight_p2[]" class="form-control weight_p2_input" value="'.$weight_p2.'">
                <input style="width:100px" type="hidden" name="weight_p3[]" class="form-control weight_p3_input" value="'.$weight_p3.'">
                <input style="width:100px" type="hidden" name="salary_p2[]" class="form-control salary_p2_input" value="'.$salary_p2.'">
                <input style="width:100px" type="hidden" name="salary_p3[]" class="form-control salary_p3_input" value="'.$salary_p3.'">
                <input style="width:100px" type="hidden" name="check_p3[]" class="form-control check_p3_input" value="'.$check_p3.'">
                <input style="width:100px" type="hidden" name="total_weekday[]" class="form-control total_weekday_input" value="'.$totalOverTime.'">
                <input style="width:100px" type="hidden" name="total_sunday[]" class="form-control total_sunday_input" value="'.$totalOverTimeCN.'">
                <input style="width:100px" type="hidden" name="total_holiday[]" class="form-control total_holiday_input" value="'.$totalOverTimeHoliday.'">
                <input style="width:100px" type="hidden" name="sales[]" class="form-control sales" value="'.$sales.'">
                <input style="width:100px" type="hidden" name="phone[]" class="form-control phone" value="'.$phone.'">
                <input style="width:100px" type="hidden" name="gasonline_cars[]" class="form-control gasonline_cars" value="'.$gasonline_cars.'">
                <input style="width:100px" type="hidden" name="motel[]" class="form-control motel" value="'.$motel.'">
                <input style="width:100px" type="hidden" name="concurrently[]" class="form-control concurrently" value="'.$concurrently.'">
                <input style="width:100px" type="hidden" name="business_fee_staff[]" class="form-control business_fee_staff" value="'.$business_fee_staff.'">
                <input style="width:100px" type="hidden" name="seniority[]" class="form-control seniority" value="'.$seniority.'">
                <input style="width:100px" name="total_date[]" type="hidden" class="form-control total_date" value="'.$totalDate.'">
                <input style="width:100px" name="number_hour[]" type="hidden" class="form-control number_hour" value="'.$number_hour.'">
                <input style="width:100px" name="total_number_day_holiday[]" type="hidden" class="form-control total_number_day_holiday" value="'.$total_number_day_holiday.'">
                <input style="width:100px" name="total_number_day_lt[]" type="hidden" class="form-control total_number_day_lt" value="'.$total_number_day_lt.'">
                <input style="width:100px" name="total_number_day_ch[]" type="hidden" class="form-control total_number_day_ch" value="'.$total_number_day_ch.'">
                <input style="width:100px" name="day_number_off[]" type="hidden" class="form-control day_number_off" value="'.$day_number_off.'">
                <input style="width:100px" name="number_day_bhxh[]" type="hidden" class="form-control number_day_bhxh" value="'.$number_day_bhxh.'">
                <input style="width:100px" type="hidden" class="form-control money_vat" name="money_vat[]" value="'.get_option('money_vat').'">
                <input style="width:100px" type="hidden" class="form-control salary_income" value="'.$salary_income.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhxh" value="'.$deduct_bhxh.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhyt" value="'.$deduct_bhyt.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhtn" value="'.$deduct_bhtn.'">
                <input style="width:100px" type="hidden" class="form-control deduct_union" value="'.$union_salary.'">
                <input style="width:100px" type="hidden" class="form-control deduct_advance" value="'.$deduct_advance.'">
                <input style="width:100px" type="hidden" class="form-control allowance_business_fee" value="'.$allowance_business_fee.'">
                <input type="hidden" name="counter[]" class="form-control counter" value="'.$index.'">
                <input type="hidden" name="staff_id[]" class="form-control staff_id" value="'.$value['staffid'].'">
                </td>';


                $html .= '</tr>';
                $index++;
            }
        }

        $tfoot = '';
        if (empty($personnel)) {

            $this->db->select("tblstaff.staffid as staffid,CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as name,tblroles.name as name_role",
                false);
            $this->db->from('tblstaff');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'inner');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->where('(tblstaff.check_salary = 0 AND tblstaff.status_work != 2)');
            $this->db->where('tblstaff.branch_salary', $branch_search);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where("($isPayroll = 0)");
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $personnelCheck = $this->db->get()->result_array();

            $this->db->from('tbl_timekeeping');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $personnelCheckTime = $this->db->get()->row_array();
            if (empty($personnelCheckTime)) {
                $data['month'] = $month;
                $data['year'] = $year;
                $data['check'] = 1;
                $this->load->view('admin/payroll/load_view_empty', $data);
            } else {
                if (empty($personnelCheck)) {
                    $data['month'] = $month;
                    $data['year'] = $year;
                    $data['branch_search'] = $branch_search;
                    $data['check'] = 2;
                    $this->load->view('admin/payroll/load_view_empty', $data);
                } else {
                    $data['tHead'] = $tHead;
                    $data['tfoot'] = $tfoot;
                    $data['html'] = $html;
                    $data['dtAllowance'] = $dtAllowance;
                    $data['dtReduce'] = $dtReduce;
                    $this->load->view('admin/payroll/load_add_payroll_salary', $data);
                }
            }

        } else {
            $data['tHead'] = $tHead;
            $data['tfoot'] = $tfoot;
            $data['html'] = $html;
            $data['dtAllowance'] = $dtAllowance;
            $data['dtReduce'] = $dtReduce;
            $this->load->view('admin/payroll/load_add_payroll_salary', $data);
        }
    }

    public function load_view_edit_chose()
    {
        if (!$this->perEditOwnPayrollSalary) {
            accessDenied($js = true);
        }
        $this->load->view('admin/payroll/load_view_chose_edit_payroll');
    }

    public function editPayroll()
    {
        if (!$this->perEditOwnPayrollSalary) {
            accessDenied();
        }
        $dtAllowance = get_table_where('tbl_allowance_reduce', ['type' => 1]);
        $dtReduce = get_table_where('tbl_allowance_reduce', ['type' => 2]);
        $rice_money = get_option('rice_money');
        $data = [];
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('month', lang("month"), 'required');
            $this->form_validation->set_rules('year', lang("year"), 'required');
            if ($this->form_validation->run() == true) {
//                print_arrays($this->input->post());
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $counter = $this->input->post('counter');
                $this->db->select('*');
                $this->db->from('tbl_timekeeping');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $timekeeping = $this->db->get()->row_array();
                if (empty($timekeeping)) {
                    $data['result'] = 1;
                    $data['message'] = 'Vui lòng chấm công tháng '.$month.' năm '.$year;
                    echo json_encode($data);
                    die;
                }
                $arrPayrollItem = [];
                if (!empty($counter)) {
                    $salary_3p_id_post = $this->input->post('salary_3p_id');
                    $weight_p2_post = $this->input->post('weight_p2');
                    $weight_p3_post = $this->input->post('weight_p3');
                    $salary_bhxh_post = $this->input->post('salary_bhxh');
                    $salary_bhxh_new_post = $this->input->post('salary_bhxh_new');
                    $allowance_responsibility_post = $this->input->post('allowance_responsibility');
                    $allowance_other_post = $this->input->post('allowance_other');
                    $allowance_manu_post = $this->input->post('allowance_manu');
                    $allowance_western_post = $this->input->post('allowance_western');
                    $allowance_rice_post = $this->input->post('allowance_rice');
                    $allowance_rice_tc_post = $this->input->post('allowance_rice_tc');
                    $bonus_holiday_post = $this->input->post('bonus_holiday');
                    $day_number_post = $this->input->post('day_number');
                    $staff_id_post = $this->input->post('staff_id');
                    $id_post = $this->input->post('id');
                    $total_date_post = $this->input->post('total_date');
                    $number_day_bhxh_post = $this->input->post('number_day_bhxh');
                    $total_number_day_holiday_post = $this->input->post('total_number_day_holiday');
                    $total_number_day_lt_post = $this->input->post('total_number_day_lt');
                    $total_number_day_ch_post = $this->input->post('total_number_day_ch');
                    $salary_responsibility_post = $this->input->post('salary_responsibility');
                    $salary_position_post = $this->input->post('salary_position');
                    $responsibility_salary_post = $this->input->post('responsibility_salary');
                    $day_number_off_post = $this->input->post('day_number_off');
                    $hour_late_post = $this->input->post('hour_late');
                    $business_fee_difference_post = $this->input->post('business_fee_difference');
                    $complete_permission_post = $this->input->post('complete_permission');
                    $sales_post = $this->input->post('sales');
                    $phone_post = $this->input->post('phone');
                    $gasonline_cars_post = $this->input->post('gasonline_cars');
                    $motel_post = $this->input->post('motel');
                    $concurrently_post = $this->input->post('concurrently');
                    $business_fee_staff_post = $this->input->post('business_fee_staff');
                    $seniority_post = $this->input->post('seniority');
                    $number_reduce_post = $this->input->post('number_reduce');
                    $allowance_diff_post = $this->input->post('allowance_diff');

                    $grand_total_kt_post = $this->input->post('grand_total_kt');
                    $grand_total_kl_post = $this->input->post('grand_total_kl');


                    $arrAll = [];
                    $arrRedu = [];
                    if (!empty($dtAllowance)) {
                        foreach ($dtAllowance as $kk => $vv) {
                            $arrAll['allowance_other_'.$vv['id']] = $this->input->post('allowance_other_'.$vv['id']);
                        }
                    }
                    if (!empty($dtReduce)) {
                        foreach ($dtReduce as $kk => $vv) {
                            $arrRedu['reduce_other_'.$vv['id']] = $this->input->post('reduce_other_'.$vv['id']);
                        }
                    }
                    $data_json_payment_post = $this->input->post('data_json_payment');
                    foreach ($counter as $key => $value) {
                        $allowance_responsibility = number_unformat($allowance_responsibility_post[$key]);
                        $salary_bhxh = number_unformat($salary_bhxh_post[$key]);
                        $salary_bhxh_new = number_unformat($salary_bhxh_new_post[$key]);
                        $allowance_other = number_unformat($allowance_other_post[$key]);
                        $allowance_manu = number_unformat($allowance_manu_post[$key]);
                        $allowance_western = number_unformat($allowance_western_post[$key]);
                        $allowance_rice = number_unformat($allowance_rice_post[$key]);
                        $allowance_rice_tc = number_unformat($allowance_rice_tc_post[$key]);
                        $bonus_holiday = number_unformat($bonus_holiday_post[$key]);
                        $day_number = number_unformat($day_number_post[$key]);
                        $staff_id = number_unformat($staff_id_post[$key]);
                        $id = number_unformat($id_post[$key]);
                        $totalDate = number_unformat($total_date_post[$key]);
                        $number_day_bhxh = number_unformat($number_day_bhxh_post[$key]);
                        $total_number_day_holiday = number_unformat($total_number_day_holiday_post[$key]);
                        $total_number_day_lt = number_unformat($total_number_day_lt_post[$key]);
                        $salary_responsibility = number_unformat($salary_responsibility_post[$key]);
                        $salary_position = number_unformat($salary_position_post[$key]);
                        $total_number_day_ch = number_unformat($total_number_day_ch_post[$key]);
                        $responsibility_salary = number_unformat($responsibility_salary_post[$key]);
                        $day_number_off = number_unformat($day_number_off_post[$key]);
                        $hour_late = number_unformat($hour_late_post[$key]);
                        $business_fee_difference = number_unformat($business_fee_difference_post[$key]);
                        $sales = number_unformat($sales_post[$key]);
                        $gasonline_cars = number_unformat($gasonline_cars_post[$key]);
                        $phone = number_unformat($phone_post[$key]);
                        $motel = number_unformat($motel_post[$key]);
                        $seniority = number_unformat($seniority_post[$key]);
                        $concurrently = number_unformat($concurrently_post[$key]);
                        $business_fee_staff = number_unformat($business_fee_staff_post[$key]);
                        $number_reduce = number_unformat($number_reduce_post[$key]);
                        $allowance_diff = number_unformat($allowance_diff_post[$key]);
                        $grand_total_kt = number_unformat($grand_total_kt_post[$key]);
                        $grand_total_kl = number_unformat($grand_total_kl_post[$key]);

                        $salary_3p_id = ($salary_3p_id_post[$key]) ?? 0;
                        $weight_p2 = ($weight_p2_post[$key]) ?? 0;
                        $weight_p3 = ($weight_p3_post[$key]) ?? 0;

                        $complete_permission = number_unformat($complete_permission_post[$key]);

                        $money_hour_late =  (($salary_bhxh + $salary_responsibility + $salary_position + $sales) / $totalDate / HOUR_DAY) * $hour_late;

                        $day_number_off_new = $day_number_off / HOUR_DAY;
                        $salary_compensation = (($business_fee_staff + $phone + $gasonline_cars + $motel) / $totalDate / HOUR_DAY) * $day_number_off;

                        $this->db->from('tbl_payroll');
                        $this->db->join('tbl_payroll_item', 'tbl_payroll_item.payroll_id = tbl_payroll.id', 'inner');
                        $this->db->where('tbl_payroll.month', $month);
                        $this->db->where('tbl_payroll.year', $year);
                        $this->db->where('tbl_payroll_item.staff_id', $staff_id);
                        $this->db->where('tbl_payroll_item.id', $id);
                        $payRollItem = $this->db->get()->row_array();

                        $day_number_off_new = $day_number_off / HOUR_DAY;


                        $personnel = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');
                        if (empty($personnel)) {
                            continue;
                        }

                        $salary_income_day = ($payRollItem['salary_bhxh'] + $salary_responsibility + $salary_position + $sales + $gasonline_cars + $phone + $motel + $concurrently + $business_fee_staff + $seniority) / $totalDate / HOUR_DAY;
                        $salary_income = $day_number * $salary_income_day;

                        $salary_income = $salary_income;

                        $check_bhxh = $personnel['check_bhxh'];
                        $check_union = $personnel['check_union'];
                        $deduct_bhxh = 0;
                        $deduct_bhyt = 0;
                        $deduct_bhtn = 0;
                        $deduct_union = 0;
                        if ($number_day_bhxh >= 14) {
                            if ($check_bhxh == 1) {
                                $deduct_bhxh = ($payRollItem['salary_bhxh_new'] * DEDUCT_BHXH) / 100;
                                $deduct_bhyt = ($payRollItem['salary_bhxh_new'] * DEDUCT_BHYT) / 100;
                                $deduct_bhtn = ($payRollItem['salary_bhxh_new'] * DEDUCT_BHTN) / 100;
                            } else {
                                $deduct_bhxh = 0;
                                $deduct_bhyt = 0;
                                $deduct_bhtn = 0;
                            }
                        }

                        if ($check_union == 1) {
                            $deduct_union = $payRollItem['salary_bhxh_new'] * (1 / 100);
                        } else {
                            $deduct_union = 0;
                        }

                        $day_number_new = ($day_number / HOUR_DAY) - ($total_number_day_holiday + $total_number_day_lt);

                        $total_allowance_other = 0;
                        $total_reduce_other = 0;

                        $arrAllowance = [];
                        if (!empty($dtAllowance)) {
                            foreach ($dtAllowance as $kk => $vv) {
                                if (isset($arrAll['allowance_other_'.$vv['id']][$value.'_'.$staff_id])) {
                                    $allowance_other_new = number_unformat($arrAll['allowance_other_'.$vv['id']][$value.'_'.$staff_id]);
                                    $total_allowance_other += $allowance_other_new;
                                    $arrAllowance[] = [
                                        'category_id' => $vv['id'],
                                        'staff_id' => $staff_id,
                                        'amount' => $allowance_other_new,
                                        'type' => 1,
                                    ];
                                }
                            }
                        }

                        $arrReduce = [];
                        if (!empty($dtReduce)) {
                            foreach ($dtReduce as $kk => $vv) {
                                if (isset($arrRedu['reduce_other_'.$vv['id']][$value.'_'.$staff_id])) {
                                    $allowance_reduce = number_unformat($arrRedu['reduce_other_'.$vv['id']][$value.'_'.$staff_id]);
                                    $total_reduce_other += $allowance_reduce;
                                    $arrReduce[] = [
                                        'category_id' => $vv['id'],
                                        'staff_id' => $staff_id,
                                        'amount' => $allowance_reduce,
                                        'type' => 2,
                                    ];
                                }
                            }
                        }

                        if (isset($data_json_payment_post[$value])) {
                            $payrollPayment = $data_json_payment_post[$value];
                        } else {
                            $payrollPayment = [];
                        }
                        $payrollPaymentJson = json_decode($payrollPayment);
                        $total_payment = 0;
                        $arr_payment = [];
                        if (!empty($payrollPaymentJson)) {
                            foreach ($payrollPaymentJson as $k => $v) {
                                $total_payment += $v->total_sub;
                                $arr_payment [] = [
                                    'id' => $v->payrollPayment,
                                    'total_sub' => $v->total_sub,
                                ];
                            }
                        }

                        $deduct_advance = $total_payment;

                        $allowance_rice_money = ($allowance_rice * $rice_money);

                        $allowance_rice_money_tc = $allowance_rice_tc;

                        $this->db->select('tbl_business_fee_boiler_calculate_item.total as total,tbl_business_fee_boiler_calculate_item.id as id');
                        $this->db->from('tbl_business_fee_boiler_calculate');
                        $this->db->join('tbl_business_fee_boiler_calculate_item',
                            'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id');
                        $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
                        $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
                        $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staff_id);;
                        $allowance_business_fee_db = $this->db->get()->row_array();
                        $allowance_business_fee = $allowance_business_fee_db['total'];
                        $business_fee_boiler_calculate_item_id = $allowance_business_fee_db['id'];

                        $money_vat = get_option('money_vat');
                        $money_reduce = get_option('money_reduce');
                        $rice_money_max = get_option('rice_money_max');

                        if (($allowance_rice_money + $allowance_rice_money_tc) > $rice_money_max){
                            $rice_money_max_new = $rice_money_max;
                        } else {
                            $rice_money_max_new = $allowance_rice_money + $allowance_rice_money_tc;
                        }

                        $tax_exemption = $business_fee_difference + $rice_money_max_new;

                        $salary_income_vat = ($grand_total_kt - $grand_total_kl + $salary_income + $business_fee_difference + $allowance_diff + $allowance_rice_money + $allowance_rice_money_tc + $salary_compensation + $complete_permission) - $money_hour_late - $tax_exemption - ($deduct_bhxh + $deduct_bhyt + $deduct_bhtn + $deduct_union) - $total_reduce_other - $money_vat - ($number_reduce * $money_reduce) - ((($business_fee_staff + $phone + $gasonline_cars) / $totalDate / HOUR_DAY) * $day_number);
                        if ($salary_income_vat < 0){
                            $salary_income_vat = 0;
                        }
                        $taxable_income = $salary_income_vat;
                        $total_money_vat_check = $salary_income_vat;

                        if ($total_money_vat_check < 0) {
                            $total_money_vat_check = 0;
                        }

                        $percent_vat = 0;
                        $total_reduce_vat = 0;
                        if ($total_money_vat_check <= 5000000) {
                            $percent_vat = 5;
                            $total_reduce_vat = 0;
                        } elseif ($total_money_vat_check > 5000000 && $total_money_vat_check <= 10000000) {
                            $percent_vat = 10;
                            $total_reduce_vat = 250000;
                        } elseif ($total_money_vat_check > 10000000 && $total_money_vat_check <= 18000000) {
                            $percent_vat = 15;
                            $total_reduce_vat = 750000;
                        } elseif ($total_money_vat_check > 18000000 && $total_money_vat_check <= 32000000) {
                            $percent_vat = 20;
                            $total_reduce_vat = 1650000;
                        } elseif ($total_money_vat_check > 32000000 && $total_money_vat_check <= 52000000) {
                            $percent_vat = 25;
                            $total_reduce_vat = 3250000;
                        } elseif ($total_money_vat_check > 52000000 && $total_money_vat_check <= 80000000) {
                            $percent_vat = 30;
                            $total_reduce_vat = 5850000;
                        } elseif ($total_money_vat_check > 80000000) {
                            $percent_vat = 35;
                            $total_reduce_vat = 9850000;
                        }

                        $total_vat = 0;
                        $total_vat = ($total_money_vat_check * $percent_vat / 100) - $total_reduce_vat;

                        $total = $grand_total_kt - $grand_total_kl + $salary_income + $allowance_business_fee + $total_allowance_other + $allowance_rice_money + $allowance_rice_money_tc + $complete_permission + $salary_compensation - $money_hour_late - $deduct_bhxh - $deduct_bhyt - $deduct_bhtn - $deduct_union - $total_reduce_other - $deduct_advance - $total_vat;

                        $total_allowance_other += $allowance_rice_money;
                        $total_allowance_other += $allowance_rice_money_tc;
                        $total_reduce_other += $deduct_bhxh + $deduct_bhyt + $deduct_bhtn + $deduct_union + $deduct_advance;

                        if (!empty($payRollItem)) {
                            $arrPayrollItem[] = [
                                'id' => $id,
                                'code' => $payRollItem['code'],
                                'staff_id' => $staff_id,
                                'day_number' => $day_number,
                                'day_number_new' => $day_number_new,
                                'day_holiday' => $total_number_day_holiday,
                                'day_lt' => $total_number_day_lt,
                                'day_ch' => $total_number_day_ch,
                                'salary' => ($payRollItem['salary_bhxh'] + $salary_responsibility + $salary_position + $sales + $gasonline_cars + $phone + $motel + $concurrently + $business_fee_staff + $seniority),
                                'salary_bhxh' => $payRollItem['salary_bhxh'],
                                'salary_bhxh_new' => $payRollItem['salary_bhxh_new'],
                                'salary_responsibility' => $salary_responsibility,
                                'salary_position' => $salary_position,
                                'responsibility_salary' => 0,
                                'sales' => $sales,
                                'gasonline_cars' => $gasonline_cars,
                                'phone' => $phone,
                                'motel' => $motel,
                                'concurrently' => $concurrently,
                                'business_fee_staff' => $business_fee_staff,
                                'seniority' => $seniority,
                                'number_reduce' => $number_reduce,
                                'business_fee_difference' => $business_fee_difference,
                                'allowance_diff' => $allowance_diff,
                                'complete_permission' => $complete_permission,
                                'salary_compensation' => $salary_compensation,
                                'tax_exemption' => $tax_exemption,
                                'taxable_income' => $taxable_income,
                                'total_vat' => $total_vat,
                                'day_number_off' => $day_number_off,
                                'day_number_off_new' => $day_number_off_new,
                                'salary_off' => 0,
                                'hour_late' => $hour_late,
                                'money_hour_late' => $money_hour_late,
                                'allowance' => $payRollItem['allowance'],
                                'salary_income' => $salary_income,
                                'allowance_responsibility' => $allowance_responsibility,
                                'allowance_other' => $allowance_other,
                                'allowance_manu' => $allowance_manu,
                                'allowance_western' => $allowance_western,
                                'allowance_business_fee' => !empty($allowance_business_fee) ? $allowance_business_fee : 0,
                                'allowance_rice' => $allowance_rice,
                                'allowance_rice_tc' => $allowance_rice_tc,
                                'allowance_rice_money' => $allowance_rice_money,
                                'bonus_holiday' => $bonus_holiday,
                                'deduct_bhxh' => $deduct_bhxh,
                                'deduct_bhyt' => $deduct_bhyt,
                                'deduct_bhtn' => $deduct_bhtn,
                                'deduct_union' => $deduct_union,
                                'deduct_advance' => $deduct_advance,
                                'total_reduce_other' => $total_reduce_other,
                                'total_allowance_other' => $total_allowance_other,
                                'total' => $total,
                                'total_real' => $total,
                                'payroll_id' => $payRollItem['payroll_id'],
                                'business_fee_boiler_calculate_item_id' => !empty($business_fee_boiler_calculate_item_id) ? $business_fee_boiler_calculate_item_id : 0,
                                'data_json_payment' => $payrollPayment,
                                'arr_payment' => $arr_payment,
                                'arrAllowance' => $arrAllowance,
                                'arrReduce' => $arrReduce,
                                'grand_total_kt' => $grand_total_kt,
                                'grand_total_kl' => $grand_total_kl,
                                'salary_3p_id' => $salary_3p_id,
                                'weight_p2' => $weight_p2,
                                'weight_p3' => $weight_p3,
                            ];
                        }
                    }
                }
                if (empty($arrPayrollItem)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có dữ liệu');
                    echo json_encode($data);
                    die;
                }
                $success = false;
                foreach ($arrPayrollItem as $key => $value) {
                    $paymentArr = $value['arr_payment'];
                    $arrAllowance = $value['arrAllowance'];
                    $arrReduce = $value['arrReduce'];
                    unset($value['arr_payment']);
                    unset($value['arrAllowance']);
                    unset($value['arrReduce']);
                    $this->db->where('id', $value['id']);
                    $success = $this->db->update('tbl_payroll_item', $value);

                    if ($success) {
                        if (!empty($paymentArr)) {
                            $this->db->where('payroll_item_id', $value['id']);
                            $this->db->delete('tbl_payroll_payment_item');
                            foreach ($paymentArr as $kk => $vv) {
                                $this->db->insert('tbl_payroll_payment_item', [
                                    'payroll_item_id' => $value['id'],
                                    'payroll_id' => $value['payroll_id'],
                                    'payroll_payment_id' => $vv['id'],
                                    'total' => $vv['total_sub'],
                                ]);
                            }
                        } else {
                            $this->db->select('tbl_payroll_payment_item.*');
                            $this->db->from('tbl_payroll_payment_item');
                            $this->db->where('payroll_item_id', $value['id']);
                            $payroll_payment_items = $this->db->get()->result_array();
                            if (!empty($payroll_payment_items)) {
                                foreach ($payroll_payment_items as $kkk => $vvv) {
                                    $this->db->where('id', $vvv['id']);
                                    $this->db->delete('tbl_payroll_payment_item');
                                }
                            }
                        }

                        if (!empty($arrAllowance)) {
                            $this->db->where('payroll_item_id', $value['id']);
                            $this->db->where('type', 1);
                            $this->db->delete('tbl_allowance_reduce_payroll');
                            foreach ($arrAllowance as $kk => $vv) {
                                $arrAllowance[$kk]['payroll_item_id'] = $value['id'];
                                $arrAllowance[$kk]['payroll_id'] = $value['payroll_id'];
//                                $this->db->insert('tbl_allowance_reduce_payroll', [
//                                    'payroll_item_id' => $value['id'],
//                                    'payroll_id' => $value['payroll_id'],
//                                    'amount' => $vv['amount'],
//                                    'category_id' => $vv['category_id'],
//                                    'staff_id' => $vv['staff_id'],
//                                    'type' => $vv['type'],
//                                ]);
                            }
                            $this->db->insert_batch('tbl_allowance_reduce_payroll', $arrAllowance);
                        }

                        if (!empty($arrReduce)) {
                            $this->db->where('payroll_item_id', $value['id']);
                            $this->db->where('type', 2);
                            $this->db->delete('tbl_allowance_reduce_payroll');
                            foreach ($arrReduce as $kk => $vv) {
                                $arrReduce[$kk]['payroll_item_id'] = $value['id'];
                                $arrReduce[$kk]['payroll_id'] = $value['payroll_id'];
//                                $this->db->insert('tbl_allowance_reduce_payroll', [
//                                    'payroll_item_id' => $value['id'],
//                                    'payroll_id' => $value['payroll_id'],
//                                    'amount' => $vv['amount'],
//                                    'category_id' => $vv['category_id'],
//                                    'staff_id' => $vv['staff_id'],
//                                    'type' => $vv['type'],
//                                ]);
                            }
                            $this->db->insert_batch('tbl_allowance_reduce_payroll', $arrReduce);
                        }
                    }
                }
                if ($success) {

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
            $year = $this->input->get('year');
            $month = $this->input->get('month');
            $branch_new = $this->input->get('branch');

            $ids = trim($this->input->get('ids'), ',');
            if (!$year || !$month || !$branch_new) {
                redirect(admin_url('payroll/payroll_salary'));
            }
            $ids = explode(',', $ids);
            $result = [];
            $this->db->select('tbl_payroll_item.*');
            $this->db->from('tbl_payroll_item');
            $this->db->join('tbl_payroll', 'tbl_payroll.id = tbl_payroll_item.payroll_id', 'inner');
            $this->db->join('tblstaff',
                'tblstaff.staffid = tbl_payroll_item.staff_id',
                'inner');
            $this->db->where('tblstaff.branch_salary', $branch_new);
            $this->db->where('tbl_payroll.month', $month);
            $this->db->where('tbl_payroll.year', $year);
            $payrollItems = $this->db->get()->result_array();
            $idss = [];
            if (!empty($payrollItems)) {
                foreach ($payrollItems as $key => $value) {
                    $idss [] = $value['id'];
                }
            }
            $data['month'] = $month;
            $data['year'] = $year;
            $data['branch_new'] = $branch_new;
            $idss = implode(',', $idss);
            $data['ids'] = $idss;
            $data['payroll'] = $result;
            $data['branch'] = getListBranch();
            $data['title'] = lang('Sửa tính lương');
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/payroll/payroll_salary'),
                    'page' => lang('Bảng lương'),
                ),
                array('link' => '#', 'page' => $data['title']),
            ];
            $this->load->view('admin/payroll/edit_payroll_salary', $data);
        }
    }

    public function loadPayrollSalaryEdit()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff_search = $this->input->get('staff_search');
        $ids = $this->input->get('ids');

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();

        $countAllowance = 3 + count($dtAllowance);
        $countReduce = 4 + count($dtReduce);

        $tHead = '';
        $html = '';
        $tHead = '<tr>
            <th rowspan="2" class="text-center" style="min-width: 50px;">'.lang('tnh_numbers').'</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">'.lang('Mã NV').'</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">'.lang('Họ Tên').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Chức vụ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Ngày vào làm').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng lương').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Lương P1').'</th>
            <th colspan="2" class="text-center" style="min-width: 100px;">'.lang('Lương năng lực').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Doanh số').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Điện thoại').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Xăng đi lại').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Nhà trọ').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Kiêm nhiệm').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Công tác phí').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Thâm niên').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số giờ công').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số ngày công').'</th>';
        $tHead .= '<th colspan="3" class="text-center" style="min-width: 100px;">'.lang('Ngày nghỉ có lương').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số giờ nghỉ').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số ngày nghỉ').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số giờ đi trễ về sớm').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số tiền đi trễ về sớm').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Thu nhập').'</th>
            <th colspan="'.$countAllowance.'" class="text-center" style="min-width: 80px;">'.lang('Phụ cấp').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng phụ cấp').'</th>
            <th colspan="5" class="text-center" style="min-width: 80px;">'.lang('Số tiếng tăng ca').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng tiền tăng ca').'</th>
            <th colspan="'.$countReduce.'" class="text-center" style="min-width: 80px;">'.lang('Khấu trừ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Khấu trừ khác(tạm ứng)').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng khấu trừ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng khấu trừ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Khen thưởng KPIs').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Lương ngoài giờ miễn thuế').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Bù lương').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Khác khoản miễn thuế').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Hoàn phép năm').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Thu nhập tính thuế').'</th>
             <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Thuế TNCN').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng thực lãnh').'</th>
        </tr>';
        $tHead .= '<tr>
            <th class="text-center" style="min-width: 80px;">'.lang('Lương P2').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lưởng P3').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Phép năm').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('VR hưởng lương (hiếu hỉ)').'</th>';
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $tHead .= '<th class="text-center" style="min-width: 80px;">'.$value['name'].'</th>';
            }
        }
        $tHead .= '<th class="text-center" style="min-width: 80px;">'.lang('Ngày cơm hành chính').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tiền ăn tăng ca').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tiền ăn hành chính').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Ngày thường(1.5)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Chủ nhật(2.0)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết(3.0)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Đêm thường('.get_option('coefficient_default_night').')').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Đêm chủ nhật('.get_option('coefficient_sunday_night').')').'</th>';
        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $tHead .= '<th class="text-center" style="min-width: 80px;">'.$value['name'].'</th>';
            }
        }
        $tHead .= '<th class="text-center" style="min-width: 80px;">'.lang('8% BHXH').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1.5% BHYT').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1% BHTN').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1% Đoàn phí').'</th>
        </tr>';

        $ids = explode(',', $ids);
        $this->db->select("
            tbl_payroll_item.*,
            tbl_payroll_item.staff_id as staffid,
            tblstaff.code as code,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as fullname,
            tblroles.name as role,
            tblstaff.day_in as day_in,
            tblstaff.check_bhxh as check_bhxh,
            tblstaff.check_union as check_union,
            ", false);
        $this->db->from('tbl_payroll_item');
        $this->db->join('tbl_payroll', 'tbl_payroll.id = tbl_payroll_item.payroll_id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item.staff_id', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->where_in('tbl_payroll_item.id', $ids);
        $payRollItems = $this->db->get()->result_array();

        // $this->db->from('tbl_timekeeping_detail');
        // $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
        // $this->db->where('tbl_timekeeping_detail.check_sun', 0);
        // $this->db->group_by('tbl_timekeeping_detail.day');
        // $totalDate = $this->db->count_all_results();

        $totalDate = get_option('day_work');
        $hour_day = get_option('hour_day');
        $index = 0;
        if (!empty($payRollItems)) {
            foreach ($payRollItems as $key => $value) {
                $staffid = $value['staffid'];

                $tdNumber = '<div class="text-center td-number">'.(++$key).'</div>';
                $tdCode = '<div class="td-code">
                    '.$value['code'].'
                </div>';
                $tdNameStaff = '<div class="td-name-staff">
                    '.$value['fullname'].'
                </div>';
                $tdRole = '<div class="td-role">
                    '.$value['role'].'
                </div>';
                $tdDate = '<div class="td-date">
                    '.(!empty($value['day_in']) ? _dhau($value['day_in']) : '').'
                </div>';

                $this->db->select('COALESCE(COUNT(id),0) as count,COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("(type = 'AL' OR type = 'LT') AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countPaidHolidayVs1 = $this->db->get()->row_array();
                $countPaidHoliday = $countPaidHolidayVs1['count'];
                $countPaidHolidayHour = $countPaidHolidayVs1['count_hour'];

                $this->db->select('COALESCE(COUNT(id),0) as count,COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("type = 'AL/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countPaidHolidayNewVs1 = $this->db->get()->row_array();
                $countPaidHolidayNew = $countPaidHolidayNewVs1['count'];
                $countPaidHolidayNewHour = $countPaidHolidayNewVs1['count_hour'];

                $this->db->select('COALESCE(COUNT(id),0) as count,COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("(type = 'UP' OR type = 'TS' OR type = 'OD' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countNotPaidHolidayVs1 = $this->db->get()->row_array();
                $countNotPaidHoliday = $countNotPaidHolidayVs1['count'];
                $countNotPaidHolidayHour = $countNotPaidHolidayVs1['count_hour'];

                $this->db->select('COALESCE(COUNT(id),0) as count,COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("type = 'UP/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countNotPaidHolidayNewVs1 = $this->db->get()->row_array();
                $countNotPaidHolidayNew = $countNotPaidHolidayNewVs1['count'];
                $countNotPaidHolidayNewHour = $countNotPaidHolidayNewVs1['count_hour'];

                $this->db->select('COALESCE(COUNT(id),0) as count');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("(type != 'X' OR (type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countNumberDay = $this->db->get()->row_array()['count'];

                $this->db->select('COALESCE(SUM(number_day),0) as count');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("(type = 'X' AND number_day = '0.5' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countNumberDayNew = $this->db->get()->row_array()['count'];

                $this->db->select('COALESCE(SUM(count_hour - count_hour_overtime),0) as count');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("((type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countHour = $this->db->get()->row_array()['count'];


                $this->db->select('COALESCE(COUNT(id),0) as count');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->where("(count_hour - count_hour_overtime) >= 4 AND check_sun = 0 AND timekeeping_id = $timekeepingId");
                $this->db->where('staff_id', $staffid);
                $countHourBhxh = $this->db->get()->row_array()['count'];


                $count_hour_phep = $countPaidHolidayHour + $countPaidHolidayNewHour;
                $count_hour_kphep = $countNotPaidHolidayHour + $countNotPaidHolidayNewHour;
                $countHourNew = $countHour;

                $totalHoliday = number_unformat($countPaidHoliday + ($countPaidHolidayNew * 0.5));
                $totalNotHoliday = number_unformat($countNotPaidHoliday + ($countNotPaidHolidayNew * 0.5));
                $number_day_new = number_unformat($countNumberDay) - number_unformat($countNumberDayNew);

                $total_number_day = $number_day_new - $totalHoliday - $totalNotHoliday;
                $total_number_day = $total_number_day > 0 ? $total_number_day : 0;

                $countHourNew = $countHourNew + $count_hour_phep + $count_hour_kphep;
                $countHourNew = $countHourNew > 0 ? $countHourNew : 0;

                $total_number_day_salary = $countHourNew + ($totalHoliday * 8);
                if($this->cong_fix == 1) {
                    $total_number_day_salary = ($countHourNew + ($value['hour_late'] ?? 0)) + ($totalHoliday * 8);
                }
                $total_number_day_salary = $total_number_day_salary > 0 ? $total_number_day_salary : 0;

                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;height:50px">'.$tdNumber.'</td>';

                $html .= '<td style="min-width: 100px;">'.$tdCode.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdNameStaff.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdRole.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdDate.'</td>';

                $day_number = $value['day_number'];
                $allowance_business_fee_db = $value['allowance_business_fee'];
                $business_fee_difference_db = $value['business_fee_difference'];
                $total_rice_db = $value['allowance_rice'];

                if ($value['salary'] == 0) {
                    $html .= '<td style="min-width: 120px;text-align: right"></td>';
                } else {
                    $html .= '<td style="min-width: 120px;text-align: right">'.formatMoney($value['salary']).'</td>';
                }

                if ($value['salary_bhxh'] == 0) {
                    $html .= '<td style="min-width: 120px;text-align: right"></td>';
                } else {
                    $html .= '<td style="min-width: 120px;text-align: right">'.formatMoney($value['salary_bhxh']).'</td>';
                }
                $html .= '<td style="min-width: 120px;text-align: right">'.(!empty($value['salary_position']) > 0 ? formatMoney($value['salary_position']) : '').'<i class="fa fa-info-circle" title="'.$value['weight_p2'].' %"></i></td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['salary_responsibility']) > 0 ? formatMoney($value['salary_responsibility']) : '').'<i class="fa fa-info-circle" title="'.$value['weight_p3'].' %"></i></td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['sales']) > 0 ? formatMoney($value['sales']) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['phone']) > 0 ? formatMoney($value['phone']) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['gasonline_cars']) > 0 ? formatMoney($value['gasonline_cars']) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['motel']) > 0 ? formatMoney($value['motel']) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['concurrently']) > 0 ? formatMoney($value['concurrently']) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['business_fee_staff']) > 0 ? formatMoney($value['business_fee_staff']) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($value['seniority']) > 0 ? formatMoney($value['seniority']) : '').'</td>';

                if ($day_number != $total_number_day_salary) {
//                    $day_number = $day_number > $total_number_day_salary ? $day_number : $total_number_day_salary;
                    $day_number = $day_number;
                }
                $salary_income_day = ($value['salary_bhxh'] + $value['salary_responsibility'] + $value['salary_position'] + $value['sales'] + $value['phone'] + $value['gasonline_cars'] + $value['motel'] + $value['concurrently'] + $value['business_fee_staff'] + $value['seniority']) / $totalDate / HOUR_DAY;
                $salary_income = $day_number * $salary_income_day;
                $check_bhxh = $value['check_bhxh'];
                $deduct_bhxh = 0;
                $deduct_bhyt = 0;
                $deduct_bhtn = 0;
                if ($countHourBhxh >= 14) {
                    if ($check_bhxh == 1) {
                        $deduct_bhxh = ($value['salary_bhxh_new'] * DEDUCT_BHXH) / 100;
                        $deduct_bhyt = ($value['salary_bhxh_new'] * DEDUCT_BHYT) / 100;
                        $deduct_bhtn = ($value['salary_bhxh_new'] * DEDUCT_BHTN) / 100;
                    } else {
                        $deduct_bhxh = 0;
                        $deduct_bhyt = 0;
                        $deduct_bhtn = 0;
                    }
                }
                if ($value['check_union'] == 1) {
                    $union_salary = $value['salary_bhxh_new'] * (1 / 100);
                } else {
                    $union_salary = 0;
                }

                $this->db->select('tbl_business_fee_boiler_calculate_item.total as total,total_weekday,total_sunday,total_holiday,salary,total_weekday_night,total_sunday_night');
                $this->db->from('tbl_business_fee_boiler_calculate');
                $this->db->join('tbl_business_fee_boiler_calculate_item',
                    'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id');
                $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
                $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
                $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staffid);
                $dtBusinessFee = $this->db->get()->row_array();
                $allowance_business_fee = $dtBusinessFee['total'];
                $total_weekday = $dtBusinessFee['total_weekday'];
                $total_sunday = $dtBusinessFee['total_sunday'];
                $total_holiday = $dtBusinessFee['total_holiday'];
                $total_weekday_night = $dtBusinessFee['total_weekday_night'];
                $total_sunday_night = $dtBusinessFee['total_sunday_night'];
                $salaryFee = $dtBusinessFee['salary'];

                $salaryFeeDiff = (($salaryFee / $totalDate / $hour_day) * $total_weekday * 0.5) + (($salaryFee / $totalDate / $hour_day) * $total_sunday * 1) + (($salaryFee / $totalDate / $hour_day) * $total_holiday * 2);

                $business_fee_difference = $salaryFeeDiff;

                if ($allowance_business_fee_db != $allowance_business_fee) {
                    $allowance_business_fee_db = $allowance_business_fee;
                }

                $this->db->select('COUNT(tbl_business_fee_boiler_overtime_detail.id) as total');
                $this->db->from('tbl_business_fee_boiler_overtime');
                $this->db->join('tbl_business_fee_boiler_overtime_detail',
                    'tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id');
                $this->db->where('tbl_business_fee_boiler_overtime.month', $month);
                $this->db->where('tbl_business_fee_boiler_overtime.year', $year);
                $this->db->where('tbl_business_fee_boiler_overtime.staff_id', $staffid);
                $this->db->where('tbl_business_fee_boiler_overtime_detail.status', 1);
                $this->db->where('(tbl_business_fee_boiler_overtime_detail.weekday != 0 OR tbl_business_fee_boiler_overtime_detail.sunday != 0 OR tbl_business_fee_boiler_overtime_detail.holiday != 0)');
                $totalOverTime = $this->db->get()->row_array()['total'];

                $total_rice = floor($total_number_day) + (!empty($totalOverTime) ? $totalOverTime : 0);
                if ($total_rice_db != $total_rice) {
//                    $total_rice_db = $total_rice_db > $total_rice ? $total_rice_db : $total_rice;
                    $total_rice_db = $total_rice_db;
                }

                $deduct_advance = $value['deduct_advance'];

                $day_number_new = $value['day_number_new'];
                $day_holiday = $value['day_holiday'];
                $day_lt = $value['day_lt'];
                $day_ch = $value['day_ch'];

                $day_number_off = $value['day_number_off'];
                $day_number_off_new = $value['day_number_off_new'];
                $salary_off = $value['money_hour_late'];
                $hour_late = $value['hour_late'];

                $total_rice_db = $total_rice_db;
                $total_rice_tc = $value['allowance_rice_tc'];

                $html .= '<td style="min-width: 120px;text-align:center">
                <input style="width:100px" type="text" class="form-control day_number" name="day_number[]" value="'.($day_number > 0 ? ($day_number) : '').'"></td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="total_number_day_new">'.($day_number_new > 0 ? $day_number_new : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($day_holiday > 0 ? $day_holiday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($day_lt > 0 ? $day_lt : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($day_ch > 0 ? $day_ch : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">
                <input type="text" class="form-control day_number_off number-format" name="day_number_off[]" style="width: 120px" value="'.($day_number_off > 0 ? ($day_number_off) : '').'"
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="day_number_off_new">'.($day_number_off_new > 0 ? ($day_number_off_new) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">
                <input type="text" class="form-control hour_late number-format" name="hour_late[]" style="width: 120px" value="'.($hour_late > 0 ? ($hour_late) : '').'"
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="salary_off">'.($salary_off > 0 ? formatMoney($salary_off) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_income_html">'.($salary_income > 0 ? formatMoney($salary_income) : '').'</td>';

//                $html .= '<td style="min-width: 50px;text-align:right" class="money_hour_late_html"></td>';
                if (!empty($dtAllowance)) {
                    foreach ($dtAllowance as $kk => $vv) {
                        $dtAllowanceStaff = get_table_where('tbl_allowance_reduce_payroll', [
                            'category_id' => $vv['id'],
                            'staff_id' => $staffid,
                            'payroll_item_id' => $value['id'],
                            'type' => 1,
                        ], '', 'row_array');
                        $html .= '<td style="min-width: 120px;text-align:left">
                            <input type="text" data-id="'.$vv['id'].'" data-staff-id="'.$staffid.'" class="form-control allowance_other_new allowance_other_'.$vv['id'].'_'.$staffid.' number-format" name="allowance_other_'.$vv['id'].'['.$index.'_'.$staffid.']" style="width: 120px" value="'.(!empty($dtAllowanceStaff['amount']) ? formatMoney($dtAllowanceStaff['amount']) : '').'">
                        </td>';
                    }
                }

                //advance payment

                $end_date = '';
                $start_date = '';
                if (!empty($month) && !empty($year)) {
                    $listDate = getAllDateInMonth($month, $year, 'd/m/Y');
                    $end_date = array_pop($listDate);
                    $start_date = reset($listDate);
                }
                $paymentPayroll = '
                COALESCE(
                (SELECT SUM(tbl_payroll_payment_item.total) 
                FROM tbl_payroll_payment_item 
                WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id ),0)
                ';
                $paymentOther = '
                COALESCE(
                (SELECT SUM(tblother_payslips_coupon.total) 
                FROM tblother_payslips_coupon 
                WHERE tblother_payslips_coupon.vouchers_id = tbl_payroll_payment.id AND tblother_payslips_coupon.type_vouchers = 333),0) 
                ';
                $this->db->select("
                    tbl_payroll_payment.id as id,
                    tbl_payroll_payment.code as code,
                    DATE_FORMAT(tbl_payroll_payment.date, '%d-%m-%Y')as date,
                    (tbl_payroll_payment.amount - $paymentOther) as amount,
                    $paymentPayroll as quantity_net,
                ");
                $this->db->from('tbl_payroll_payment');
                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_payment.staff_id');
                $this->db->where('tblstaff.staffid', $staffid);
                if (!empty($start_date)) {
                    // $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=', to_sql_date($start_date));
                }
                if (!empty($end_date)) {
                    $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date));
                }
                $this->db->having('(amount-quantity_net) > 0');
                $payrollPayments = $this->db->get()->result_array();
                $data_json_payment = [];
                if (!empty($payrollPayments)){
                    foreach ($payrollPayments as $k => $v){
                        $paymentPayRoll = get_table_where('tbl_payroll_payment', ['id' => $v['id']], '', 'row_array');
                        $data_json_payment[] = [
                            'payrollPayment' => $v['id'],
                            'total_sub' => ($v['amount'] - $v['quantity_net']),
                            'cal_id' => null,
                            'staff_id' => $staffid,
                            'paymentPayRoll' => $paymentPayRoll,
                        ];
                    }
                }
                $data_json_payment = ($value['data_json_payment'] != '' && $value['data_json_payment'] != '[]') ? $value['data_json_payment'] : (!empty($data_json_payment) ? json_encode($data_json_payment) : null);
                //end

                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control allowance_rice number-format" name="allowance_rice[]" style="width: 120px" value="'.($total_rice_db > 0 ? formatNumber($total_rice_db) : '').'">
                    <input type="hidden" class="form-control allowance_diff number-format" name="allowance_diff[]" style="width: 120px" value="'.($value['allowance_diff']).'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control allowance_rice_tc number-format" name="allowance_rice_tc[]" style="width: 120px" value="'.($total_rice_tc > 0 ? formatMoney($total_rice_tc) : '').'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="allowance_rice_money">
                    '.($value['allowance_rice_money'] > 0 ? formatMoney($value['allowance_rice_money']) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <div class="total_allowance text-right"></div>
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($total_weekday > 0 ? $total_weekday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($total_sunday > 0 ? $total_sunday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($total_holiday > 0 ? $total_holiday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($total_weekday_night > 0 ? $total_weekday_night : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($total_sunday_night > 0 ? $total_sunday_night : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">
                    '.($allowance_business_fee > 0 ? formatMoney($allowance_business_fee) : '').'
                </td>';
                if (!empty($dtReduce)) {
                    foreach ($dtReduce as $kk => $vv) {
                        $dtReduceStaff = get_table_where('tbl_allowance_reduce_payroll', [
                            'category_id' => $vv['id'],
                            'staff_id' => $staffid,
                            'payroll_item_id' => $value['id'],
                            'type' => 2,
                        ], '', 'row_array');
                        $html .= '<td style="min-width: 120px;text-align:left">
                            <input type="text" class="form-control reduce_other_'.$vv['id'].'_'.$staffid.' number-format reduce_other" name="reduce_other_'.$vv['id'].'['.$index.'_'.$staffid.']" style="width: 120px" value="'.(!empty($dtReduceStaff['amount']) ? formatMoney($dtReduceStaff['amount']) : '').'">
                        </td>';
                    }
                }
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhxh > 0 ? formatMoney($deduct_bhxh) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhyt > 0 ? formatMoney($deduct_bhyt) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhtn > 0 ? formatMoney($deduct_bhtn) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($union_salary > 0 ? formatMoney($union_salary) : '').'</td>';
                $html .= '<td style="min-width: 150px;">
                    <div class="td-payment">
                        <div class="sub"></div>
                        <div class="" style="display: flex;justify-content: flex-end;"><a onclick="addPayrollPayment(this,'.$index.')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Sửa tạm ứng</a></div>
                        <div class="show_payment" style="margin-top: 5px;"></div>
                        <input type="hidden" name="data_json_payment['.$index.']" class="form-control data_json_payment" value="'.tnh_htmlentities($data_json_payment).'">
                        <div class="text-error" style="color: red"></div>
                    </div>
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total"></td>';
                $this->db->select('COALESCE(SUM(grand_total),0) as grand_total');
                $this->db->from('tbl_decision_bonus_discipline');
                $this->db->where("tbl_decision_bonus_discipline.type_quota_bonus_discipline_id",1);
                $this->db->where("tbl_decision_bonus_discipline.object_type","staff");
                $this->db->where("tbl_decision_bonus_discipline.status",1);
                $this->db->where("MONTH(tbl_decision_bonus_discipline.date)",$month);
                $this->db->where("YEAR(tbl_decision_bonus_discipline.date)",$year);
                $this->db->where('tbl_decision_bonus_discipline.object_id', $staffid);
                $this->db->group_by('object_id,object_type');
                $GetTotalSuggestBonuskt = $this->db->get()->row_array()['grand_total'];

                $this->db->select('COALESCE(SUM(grand_total),0) as grand_total');
                $this->db->from('tbl_decision_bonus_discipline');
                $this->db->where("tbl_decision_bonus_discipline.type_quota_bonus_discipline_id",2);
                $this->db->where("tbl_decision_bonus_discipline.object_type","staff");
                $this->db->where("tbl_decision_bonus_discipline.status",1);
                $this->db->where("MONTH(tbl_decision_bonus_discipline.date)",$month);
                $this->db->where("YEAR(tbl_decision_bonus_discipline.date)",$year);
                $this->db->where('tbl_decision_bonus_discipline.object_id', $staffid);
                $this->db->group_by('object_id,object_type');
                $GetTotalSuggestBonuskl = $this->db->get()->row_array()['grand_total'];
                $html .= '<td style="min-width: 120px;text-align:right" class="grand_total_kt">
                    <input style="width:100px" type="hidden" name="grand_total_kt[]" class="form-control grand_total_kt" value="'.$value['grand_total_kt'].'">
                    '.($GetTotalSuggestBonuskt > 0 ? formatMoney($GetTotalSuggestBonuskt) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="grand_total_kl">
                    <input style="width:100px" type="hidden" name="grand_total_kl[]" class="form-control grand_total_kl" value="'.$value['grand_total_kl'].'">
                    '.($GetTotalSuggestBonuskl > 0 ? formatMoney($GetTotalSuggestBonuskl) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="business_fee_difference">
                    '.($business_fee_difference > 0 ? formatMoney($business_fee_difference) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="salary_compensation">
                    '.($value['salary_compensation'] > 0 ? formatMoney($value['salary_compensation']) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="tax_exemption">
                    '.($value['tax_exemption'] > 0 ? formatMoney($value['tax_exemption']) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;">
                      <input type="text" class="form-control complete_permission number-format" name="complete_permission[]" style="width: 120px" value="'.(!empty($value['complete_permission']) ? formatMoney($value['complete_permission']) : '').'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="taxable_income">
                    '.($value['taxable_income'] > 0 ? formatMoney($value['taxable_income']) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total_vat"></td>';

                

                $html .= '<td style="min-width: 120px;text-align:right"><span class="total_real"></span>
                <input style="width:100px" type="hidden" class="form-control salary_3p_id" value="'.($value['salary_3p_id']).'" name="salary_3p_id[]">
                <input style="width:100px" type="hidden" class="form-control number_reduce" value="'.($value['number_reduce']).'" name="number_reduce[]">
                <input style="width:100px" type="hidden" class="form-control salary_bhxh" name="salary_bhxh[]" value="'.($value['salary_bhxh']).'">
                <input style="width:100px" type="hidden" class="form-control allowance"value="'.$value['allowance'].'">
                <input style="width:100px" type="hidden" class="form-control salary_bhxh_new" name="salary_bhxh_new[]" value="'.$value['salary_bhxh_new'].'">
                <input style="width:100px" type="hidden" class="form-control" name="business_fee_difference[]" value="'.$business_fee_difference.'">
                <input style="width:100px" type="hidden" name="salary_responsibility[]" class="form-control salary_responsibility"value="'.$value['salary_responsibility'].'">
                <input style="width:100px" type="hidden" name="salary_position[]" class="form-control salary_position"value="'.$value['salary_position'].'">
                <input style="width:100px" type="hidden" name="weight_p2[]" class="form-control weight_p2"value="'.$value['weight_p2'].'">
                <input style="width:100px" type="hidden" name="weight_p3[]" class="form-control weight_p3"value="'.$value['weight_p3'].'">
                <input style="width:100px" type="hidden" name="sales[]" class="form-control sales"value="'.$value['sales'].'">
                <input style="width:100px" type="hidden" name="phone[]" class="form-control phone"value="'.$value['phone'].'">
                <input style="width:100px" type="hidden" name="gasonline_cars[]" class="form-control gasonline_cars"value="'.$value['gasonline_cars'].'">
                <input style="width:100px" type="hidden" name="motel[]" class="form-control motel"value="'.$value['motel'].'">
                <input style="width:100px" type="hidden" name="concurrently[]" class="form-control concurrently"value="'.$value['concurrently'].'">
                <input style="width:100px" type="hidden" name="business_fee_staff[]" class="form-control business_fee_staff"value="'.$value['business_fee_staff'].'">
                <input style="width:100px" type="hidden" name="seniority[]" class="form-control seniority"value="'.$value['seniority'].'">
                <input style="width:100px" type="hidden" class="form-control salary_income" value="'.$salary_income.'">
                <input style="width:100px" name="total_date[]" type="hidden" class="form-control total_date" value="'.$totalDate.'">
                 <input style="width:100px" name="total_number_day_holiday[]" type="hidden" class="form-control total_number_day_holiday" value="'.$day_holiday.'">
                <input style="width:100px" name="total_number_day_lt[]" type="hidden" class="form-control total_number_day_lt" value="'.$day_lt.'">
                <input style="width:100px" name="total_number_day_ch[]" type="hidden" class="form-control total_number_day_ch" value="'.$day_ch.'">
                <input style="width:100px" name="number_day_bhxh[]" type="hidden" class="form-control number_day_bhxh" value="'.$countHourBhxh.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhxh" value="'.$deduct_bhxh.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhyt" value="'.$deduct_bhyt.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhtn" value="'.$deduct_bhtn.'">
                <input style="width:100px" type="hidden" class="form-control deduct_union" value="'.$union_salary.'">
                <input style="width:100px" type="hidden" class="form-control deduct_advance" value="'.$deduct_advance.'">
                <input style="width:100px" type="hidden" class="form-control allowance_business_fee" value="'.$allowance_business_fee_db.'">
                <input type="hidden" name="counter[]" class="form-control counter" value="'.$index.'">
                <input type="hidden" name="id[]" class="form-control id" value="'.$value['id'].'">
                <input type="hidden" name="staff_id[]" class="form-control staff_id" value="'.$value['staffid'].'">
                </td>';
                $index++;
            }
        }

        $tfoot = '';

        $data['tHead'] = $tHead;
        $data['tfoot'] = $tfoot;
        $data['html'] = $html;
        $data['dtAllowance'] = $dtAllowance;
        $data['dtReduce'] = $dtReduce;
        $this->load->view('admin/payroll/load_edit_payroll_salary', $data);

    }

    public function deletePayroll()
    {
        if (!$this->perDeletePayrollSalary) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die;
        }
        $data = [];

        if ($this->input->post()) {
            $ids = trim($this->input->post('ids'), ',');
            if (!$ids) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data);

                return;
            }
            $errors = '';
            $count = 0;
            $ids = explode(',', $ids);
            $ids = array_unique($ids);
            if (!empty($ids)) {
                foreach ($ids as $key => $id) {
                    $payroll = get_table_where('tbl_payroll_item', ['id' => $id], '', 'row_array');
                    $this->db->where('id', $id);
                    $success = $this->db->delete('tbl_payroll_item');
                    if ($success) {

                        $this->db->where('payroll_item_id', $id);
                        $this->db->delete('tbl_allowance_reduce_payroll');

                        $this->db->select('tbl_payroll_payment_item.*');
                        $this->db->from('tbl_payroll_payment_item');
                        $this->db->where('payroll_item_id', $payroll['id']);
                        $payroll_payment_items = $this->db->get()->result_array();

                        if (!empty($payroll_payment_items)) {
                            foreach ($payroll_payment_items as $kkk => $vvv) {

                                $this->db->where('id', $vvv['id']);
                                $this->db->delete('tbl_payroll_payment_item');

                            }
                        }

                        $count++;
                    }
                }
            }
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            $data['errors'] = $errors;
            echo json_encode($data);

            return;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function callPayment()
    {
        $data = [];

        $cId = $this->input->post('cId');
        $cStaffId = $this->input->post('cStaffId');
        $data_json = $this->input->post('data_json');
        $month = $this->input->post('month');
        $year = $this->input->post('year');

        $end_date = '';
        $start_date = '';
        if (!empty($month) && !empty($year)) {
            $listDate = getAllDateInMonth($month, $year, 'd/m/Y');
            $end_date = array_pop($listDate);
            $start_date = reset($listDate);
        }

        if (!empty($data_json)) {
            $data_json = json_decode($data_json, true);
        }

        if (!empty($cId)) {
            $paymentPayroll = '
            COALESCE(
            (SELECT SUM(tbl_payroll_payment_item.total) 
            FROM tbl_payroll_payment_item 
            WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id AND tbl_payroll_payment_item.payroll_item_id != '.$cId.' ),0)
            ';
        } else {
            $paymentPayroll = '
            COALESCE(
            (SELECT SUM(tbl_payroll_payment_item.total) 
            FROM tbl_payroll_payment_item 
            WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id ),0)
            ';
        }
        $this->db->select("
            tbl_payroll_payment.id as id,
            tbl_payroll_payment.code as code,
            DATE_FORMAT(tbl_payroll_payment.date, '%d-%m-%Y')as date,
            tbl_payroll_payment.amount as amount,
            $paymentPayroll as quantity_net,
        ");
        $this->db->from('tbl_payroll_payment');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_payment.staff_id');
        $this->db->where('tblstaff.staffid', $cStaffId);
        if (!empty($end_date)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date));
        }
        $this->db->having('(amount-quantity_net) > 0');
        $payrollPayments = $this->db->get()->result_array();


        $data['data_json'] = $data_json;
        $data['cId'] = $cId;
        $data['cStaffId'] = $cStaffId;
        $data['payrollPayments'] = $payrollPayments;
        $data['title'] = 'Thêm phiếu tạm ứng';

        $this->load->view('admin/payroll/call_payroll_payment', $data);
    }

    public function handlingCalPayment()
    {
        $data = [];
        $dataPost = $this->input->post();
        if (!empty($dataPost)) {
            //reason
            $arrPayment = [];
            $payrollPayment = !empty($dataPost['payrollPayment']) ? $dataPost['payrollPayment'] : null;
            $cal_id = !empty($dataPost['cal_id']) ? $dataPost['cal_id'] : null;
            $staff_id = !empty($dataPost['staff_id']) ? $dataPost['staff_id'] : null;
            if (!empty($payrollPayment)) {
                foreach ($payrollPayment as $key => $value) {
                    $payrollPayment = $dataPost['payrollPayment'][$key];
                    $total_sub = number_unformat($dataPost['total_sub'][$key]);

                    $paymentPayRoll = get_table_where('tbladvance_payment', ['id' => $payrollPayment], '', 'row_array');

                    if (!empty($payrollPayment)) {
                        $arrPayment[] = [
                            'payrollPayment' => $payrollPayment,
                            'total_sub' => $total_sub,
                            'cal_id' => $cal_id,
                            'staff_id' => $staff_id,
                            'paymentPayRoll' => $paymentPayRoll,
                        ];
                    }
                }
            }
        }
        $data['dataJSonPayment'] = json_encode($arrPayment, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);

        echo json_encode($data);
    }

    public function print_pdf_payroll_salary()
    {
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');
        ob_end_clean();
        $data = [];


        $data['title'] = lang('print').' '.lang('BẢNG LƯƠNG THÁNG ').$month.' NĂM '.$year;
        $data['type'] = 'P';
        $data['img'] = '';

        $font_size = '7px';
        $font_size_new = '7px';
        $width = 83 / 17;

        $this->db->select("
                tbl_payroll_item.id as id,
                tblstaff.code as code,
                CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname,
                tblroles.name as role,
                tblstaff.day_in as day_in,
                tbl_payroll_item.salary_bhxh as salary_bhxh,
                tbl_payroll_item.allowance as allowance,
                tbl_payroll_item.day_number as day_number,
                tbl_payroll_item.salary_income as salary_income,
                tbl_payroll_item.allowance_responsibility as allowance_responsibility,
                tbl_payroll_item.allowance_other as allowance_other,
                tbl_payroll_item.allowance_manu as allowance_manu,
                tbl_payroll_item.allowance_western as allowance_western,
                tbl_payroll_item.allowance_business_fee as allowance_business_fee,
                tbl_payroll_item.allowance_rice as allowance_rice,
                tbl_payroll_item.allowance_rice_money as allowance_rice_money,
                tbl_payroll_item.bonus_holiday as bonus_holiday,
                tbl_payroll_item.deduct_bhxh as deduct_bhxh,
                tbl_payroll_item.deduct_bhyt as deduct_bhyt,
                tbl_payroll_item.deduct_bhtn as deduct_bhtn,
                tbl_payroll_item.deduct_advance as deduct_advance,
                tbl_payroll_item.total as total,
                tbl_payroll_item.total_real as total_real,
            ");
        $this->db->from('tbl_payroll');
        $this->db->join('tbl_payroll_item', 'tbl_payroll_item.payroll_id = tbl_payroll.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item.staff_id', 'inner');;
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        if (!empty($month)) {
            $this->db->where('tbl_payroll.month', $month);
        }

        if (!empty($year)) {
            $this->db->where('tbl_payroll.year', $year);
        }

        if (!empty($staff)) {
            $staff = explode(',', $staff);
            $this->db->where_in('tbl_payroll_item.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where('tbl_personnel.departments', $department);
        }

        $personnel = $this->db->get()->result_array();

        $bodyItems = '';
        $footer_salary_bhxh = 0;
        $footer_allowance = 0;
        $footer_total_salary_new = 0;
        $footer_day_number = 0;
        $footer_salary_income = 0;
        $footer_allowance_responsibility = 0;
        $footer_allowance_other = 0;
        $footer_allowance_manu = 0;
        $footer_allowance_western = 0;
        $footer_allowance_business_fee = 0;
        $footer_allowance_rice = 0;
        $footer_allowance_rice_money = 0;
        $footer_bonus_holiday = 0;
        $footer_deduct_bhxh = 0;
        $footer_deduct_bhyt = 0;
        $footer_deduct_bhtn = 0;
        $footer_deduct_advance = 0;
        $footer_total = 0;
        $footer_total_real = 0;
        if (!empty($personnel)) {
            foreach ($personnel as $key => $value) {

                $footer_salary_bhxh += $value['salary_bhxh'];
                $footer_allowance += $value['allowance'];
                $footer_total_salary_new += ($value['salary_bhxh'] + $value['allowance']);
                $footer_day_number += $value['day_number'];
                $footer_salary_income += $value['salary_income'];
                $footer_allowance_responsibility += $value['allowance_responsibility'];
                $footer_allowance_other += $value['allowance_other'];
                $footer_allowance_manu += $value['allowance_manu'];
                $footer_allowance_western += $value['allowance_western'];
                $footer_allowance_business_fee += $value['allowance_business_fee'];
                $footer_allowance_rice += $value['allowance_rice'];
                $footer_allowance_rice_money += $value['allowance_rice_money'];
                $footer_bonus_holiday += $value['bonus_holiday'];
                $footer_deduct_bhxh += $value['deduct_bhxh'];
                $footer_deduct_bhyt += $value['deduct_bhyt'];
                $footer_deduct_bhtn += $value['deduct_bhtn'];
                $footer_deduct_advance += $value['deduct_advance'];
                $footer_total += $value['total'];
                $footer_total_real += $value['total_real'];

                $tdNumber = '<td style="font-size:'.$font_size_new.';width:2%" class="text-center">'.(++$key).'</td>';
                $tdCodeName = '<td style="text-align:left;font-size:'.$font_size_new.';width:5%"><span style="font-weight:bold;">'.$value['code'].'</span></td>';
                $tdName = '<td style="font-size:'.$font_size_new.';width:5%"><span style="font-weight:bold;">'.$value['fullname'].'</span></td>';
                $tdRole = '<td style="font-size:'.$font_size_new.';width:5%"><span style="font-weight:bold;">'.$value['role'].'</span></td>';
                $tdDayIn = '<td style="font-size:'.$font_size_new.';width:5%"><span style="font-weight:bold;">'.(!empty($value['day_in']) ? _dhau($value['day_in']) : '').'</span></td>';
                $tdSalary = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['salary_bhxh'] > 0 ? formatMoney($value['salary_bhxh']) : '').'</td>';
                $tdAllowance = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['allowance'] > 0 ? formatMoney($value['allowance']) : '').'</td>';
                $tdSalaryNew = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['salary_bhxh'] + $value['allowance'] > 0 ? formatMoney($value['salary_bhxh'] + $value['allowance']) : '').'</td>';
                $tdDayNumber = '<td style="font-size:'.$font_size_new.';width:'.($width - 2).'%" class="text-center">'.($value['day_number'] != 0 ? formatNumber($value['day_number']) : '').'</td>';
                $tdSalaryIncom = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['salary_income'] != 0 ? formatMoney($value['salary_income']) : '').'</td>';
                $tdAllowanceResponsibility = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['allowance_responsibility'] != 0 ? formatMoney($value['allowance_responsibility']) : '').'</td>';
                $tdAllowanceOther = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['allowance_other'] != 0 ? formatMoney($value['allowance_other']) : '').'</td>';
                $tdAllowanceManu = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['allowance_manu'] != 0 ? formatMoney($value['allowance_manu']) : '').'</td>';
                $tdAllowanceWestern = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['allowance_western'] != 0 ? formatMoney($value['allowance_western']) : '').'</td>';
                $tdAllowanceBusinessFee = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['allowance_business_fee'] != 0 ? formatMoney($value['allowance_business_fee']) : '').'</td>';
                $tdAllowanceRice = '<td style="font-size:'.$font_size_new.';width:'.($width - 2).'%" class="text-center">'.($value['allowance_rice'] != 0 ? formatNumber($value['allowance_rice']) : '').'</td>';
                $tdAllowanceRiceMoney = '<td style="font-size:'.$font_size_new.';width:'.($width - 1).'%" class="text-right">'.($value['allowance_rice_money'] != 0 ? formatMoney($value['allowance_rice_money']) : '').'</td>';
                $tdBonusHoliday = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['bonus_holiday'] != 0 ? formatMoney($value['bonus_holiday']) : '').'</td>';
                $tdDeductBhxh = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['deduct_bhxh'] != 0 ? formatMoney($value['deduct_bhxh']) : '').'</td>';
                $tdDeductBhyt = '<td style="font-size:'.$font_size_new.';width:'.($width - 1).'%" class="text-right">'.($value['deduct_bhyt'] != 0 ? formatMoney($value['deduct_bhyt']) : '').'</td>';
                $tdDeductBhtn = '<td style="font-size:'.$font_size_new.';width:'.($width - 1).'%" class="text-right">'.($value['deduct_bhtn'] != 0 ? formatMoney($value['deduct_bhtn']) : '').'</td>';
                $tdDeductAdvance = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right">'.($value['deduct_advance'] != 0 ? formatMoney($value['deduct_advance']) : '').'</td>';
                $tdTotal = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['total'] != 0 ? formatMoney($value['total']) : '').'</td>';
                $tdTotalReal = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right">'.($value['total_real'] != 0 ? formatMoney($value['total_real']) : '').'</td>';


                $bodyItems .= '<tr nobr="true">
                    '.$tdNumber.'
                    '.$tdName.'
                    '.$tdRole.'
                    '.$tdDayIn.'
                    '.$tdSalary.'
                    '.$tdAllowance.'
                    '.$tdSalaryNew.'
                    '.$tdDayNumber.'
                    '.$tdSalaryIncom.'
                    '.$tdAllowanceResponsibility.'
                    '.$tdAllowanceOther.'
                    '.$tdAllowanceBusinessFee.'
                    '.$tdAllowanceRice.'
                    '.$tdAllowanceRiceMoney.'
                    '.$tdBonusHoliday.'
                    '.$tdDeductBhxh.'
                    '.$tdDeductBhyt.'
                    '.$tdDeductBhtn.'
                    '.$tdDeductAdvance.'
                    '.$tdTotal.'
                    '.$tdTotalReal.'
                </tr>';
            }
        }

        ob_start();
        stylePdf();

        $thPC = '<th colspan="5" style="font-size: '.$font_size.';width:'.($width * 5 - 2).'%" class="bold text-center" >'._l('Phụ cấp').'</th>';
        $thPCChild = '<th style="font-size: '.$font_size.';width:'.($width).'%" class="text-center bold">'.lang('Trách nhiệm').'</th>
                    <th style="font-size: '.$font_size.';width:'.($width).'%" class="text-center bold">'.lang('PC khác').'</th>
                    <th style="font-size: '.$font_size.';width:'.($width + 1).'%" class="text-center bold">'.lang('Tăng ca').'</th>
                    <th style="font-size: '.$font_size.';width:'.($width - 2).'%" class="text-center bold">'.lang('Ngày cơm').'</th>
                    <th style="font-size: '.$font_size.';width:'.($width - 1).'%" class="text-center bold">'.lang('Tổng Pc').'</th>';
        $thGT = '<th colspan="3" style="font-size: '.$font_size.';width:'.($width * 3 - 2).'%" class="bold text-center" >'._l('Giảm trừ').'</th>';
        $thGTChild = '<th style="font-size: '.$font_size.';width:'.($width).'%" class="text-center bold">'.lang('8% BHXH').'</th>
                    <th style="font-size: '.$font_size.';width:'.($width - 1).'%" class="text-center bold">'.lang('1,5% BHYT').'</th>
                    <th style="font-size: '.$font_size.';width:'.($width - 1).'%" class="text-center bold">'.lang('1% BHTN').'</th>';


        $font_size_foot = '7px';
        $bodyItemsFooter = '';
        $tdTitle = '<td style="font-size:'.$font_size_foot.';width:'.(17).'%" colspan="4" class="text-center bold">Tổng cộng</td>';
        $tdSalary = '<td style="font-size:'.$font_size_foot.';width:'.($width + 1).'%" class="text-right bold">'.formatMoney($footer_salary_bhxh).'</td>';
        $tdAllowance = '<td style="font-size:'.$font_size_foot.';width:'.($width + 1).'%" class="text-right bold">'.formatMoney($footer_allowance).'</td>';
        $tdSalaryNew = '<td style="font-size:'.$font_size_foot.';width:'.($width + 1).'%" class="text-right bold">'.($footer_total_salary_new != 0 ? formatMoney($footer_total_salary_new) : '').'</td>';
        $tdDayNumber = '<td style="font-size:'.$font_size_foot.';width:'.($width - 2).'%" class="text-center bold">'.($footer_day_number != 0 ? formatNumber($footer_day_number) : '').'</td>';
        $tdSalaryIncom = '<td style="font-size:'.$font_size_foot.';width:'.($width + 1).'%" class="text-right bold">'.($footer_salary_income != 0 ? formatMoney($footer_salary_income) : '').'</td>';
        $tdAllowanceResponsibility = '<td style="font-size:'.$font_size_foot.';width:'.($width).'%" class="text-right bold">'.($footer_allowance_responsibility != 0 ? formatMoney($footer_allowance_responsibility) : '').'</td>';
        $tdAllowanceOther = '<td style="font-size:'.$font_size_foot.';width:'.($width).'%" class="text-right bold">'.($footer_allowance_other != 0 ? formatMoney($footer_allowance_other) : '').'</td>';
        $tdAllowanceManu = '<td style="font-size:'.$font_size_foot.';width:'.($width).'%" class="text-right bold">'.($footer_allowance_manu != 0 ? formatMoney($footer_allowance_manu) : '').'</td>';
        $tdAllowanceWestern = '<td style="font-size:'.$font_size_foot.';width:'.($width).'%" class="text-right bold">'.($footer_allowance_western != 0 ? formatMoney($footer_allowance_western) : '').'</td>';
        $tdAllowanceBusinessFee = '<td style="font-size:'.$font_size_foot.';width:'.($width + 1).'%" class="text-right bold">'.($footer_allowance_business_fee != 0 ? formatMoney($footer_allowance_business_fee) : '').'</td>';
        $tdAllowanceRice = '<td style="font-size:'.$font_size_foot.';width:'.($width - 2).'%" class="text-center bold">'.($footer_allowance_rice != 0 ? formatNumber($footer_allowance_rice) : '').'</td>';
        $tdAllowanceRiceMoney = '<td style="font-size:'.$font_size_foot.';width:'.($width - 1).'%" class="text-right bold">'.($footer_allowance_rice_money != 0 ? formatMoney($footer_allowance_rice_money) : '').'</td>';
        $tdBonusHoliday = '<td style="font-size:'.$font_size_foot.';width:'.($width).'%" class="text-right bold">'.($footer_bonus_holiday != 0 ? formatMoney($footer_bonus_holiday) : '').'</td>';
        $tdDeductBhxh = '<td style="font-size:'.$font_size_foot.';width:'.($width).'%" class="text-right bold">'.($footer_deduct_bhxh != 0 ? formatMoney($footer_deduct_bhxh) : '').'</td>';
        $tdDeductBhyt = '<td style="font-size:'.$font_size_foot.';width:'.($width - 1).'%" class="text-right bold">'.($footer_deduct_bhyt != 0 ? formatMoney($footer_deduct_bhyt) : '').'</td>';
        $tdDeductBhtn = '<td style="font-size:'.$font_size_new.';width:'.($width - 1).'%" class="text-right bold">'.($footer_deduct_bhtn != 0 ? formatMoney($footer_deduct_bhtn) : '').'</td>';
        $tdDeductAdvance = '<td style="font-size:'.$font_size_new.';width:'.($width).'%" class="text-right bold">'.($footer_deduct_advance != 0 ? formatMoney($footer_deduct_advance) : '').'</td>';
        $tdTotal = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right bold">'.($footer_total != 0 ? formatMoney($footer_total) : '').'</td>';
        $tdTotalReal = '<td style="font-size:'.$font_size_new.';width:'.($width + 1).'%" class="text-right bold">'.($footer_total_real != 0 ? formatMoney($footer_total_real) : '').'</td>';

        $bodyItemsFooter .= '<tr nobr="true">
                    '.$tdTitle.'
                    '.$tdSalary.'
                    '.$tdAllowance.'
                    '.$tdSalaryNew.'
                    '.$tdDayNumber.'
                    '.$tdSalaryIncom.'
                    '.$tdAllowanceResponsibility.'
                    '.$tdAllowanceOther.'
                    '.$tdAllowanceBusinessFee.'
                    '.$tdAllowanceRice.'
                    '.$tdAllowanceRiceMoney.'
                    '.$tdBonusHoliday.'
                    '.$tdDeductBhxh.'
                    '.$tdDeductBhyt.'
                    '.$tdDeductBhtn.'
                    '.$tdDeductAdvance.'
                    '.$tdTotal.'
                    '.$tdTotalReal.'
                </tr>';

        echo '
            <br>
            <h1 class="text-center uppercase" style="color:#134490">'.lang('BẢNG LƯƠNG THÁNG ').$month.' NĂM '.$year.'</h1>
            <table class="table" cellspacing="0" cellpadding="0" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" style="font-size: '.$font_size.';width:2%" class="bold text-center" >'._l('tnh_numbers').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:5%" class="bold text-center" >'._l('Họ Tên').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:5%" class="bold text-center" >'._l('Chức vụ').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:5%" class="bold text-center" >'._l('Ngày vào làm').'</th>
                        <th colspan="3" style="font-size: '.$font_size.';width:'.($width * 3 + 3).'%" class="bold text-center" >'._l('Lương').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:'.($width - 2).'%" class="bold text-center" >'._l('Số NC').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:'.($width + 1).'%" class="bold text-center" >'._l('Thu nhập').'</th>
                        '.$thPC.'
                        <th rowspan="2" style="font-size: '.$font_size.';width:'.($width).'%" class="bold text-center" >'._l('Thưởng lễ').'</th>
                        '.$thGT.'
                        <th rowspan="2" style="font-size: '.$font_size.';width:'.($width).'%" class="bold text-center" >'._l('Khấu trừ khác(tạm ứng)').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:'.($width + 1).'%" class="bold text-center" >'._l('Tổng thu nhập').'</th>
                        <th rowspan="2" style="font-size: '.$font_size.';width:'.($width + 1).'%" class="bold text-center" >'._l('Tổng thực lãnh').'</th>
                    </tr>
                    <tr>
                        <th style="font-size: '.$font_size.'; width:'.($width + 1).'%" class="text-center bold">'.lang('Lương').'</th>
                        <th style="font-size: '.$font_size.';width:'.($width + 1).'%" class="text-center bold">'.lang('Phụ cấp').'</th>
                        <th style="font-size: '.$font_size.';width:'.($width + 1).'%" class="text-center bold">'.lang('Tổng LCB').'</th>
                        '.$thPCChild.'
                        '.$thGTChild.'
                    </tr>
                </thead>
                <tbody>
                    '.$bodyItems.'
                </tbody>
                <tfoot>
                    '.$bodyItemsFooter.'
                </tfoot>
            </table><br/><br/>
        ';
        footerPdf();

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'orders_detail';
        $pdf = @print_pdf_dt_L($data);
        $type = 'I';
        $pdf->Output(slug_it('bảng lương tháng '.$month.' năm '.$year.'').'.pdf', $type);
    }

    public function export_excel()
    {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $staff = $this->input->get('staff');
        $branch_search = $this->input->get('branch_search');
        $department = $this->input->get('department');

        $arrFooter = [];
        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $this->db->where_not_in('tbl_allowance_reduce.id',[ALLOWANCE_CHUYENCAN,ALLOWANCE_THAMNIEN]);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll',
                    ['category_id' => $value['id'], 'type' => 1], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtAllowance[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_allowance_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);

            }
        }
        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll',
                    ['category_id' => $value['id'], 'type' => 2], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtReduce[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_reduce_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);
            }
        }

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select("
                tbl_payroll_item.id as id,
                tblstaff.code as code,
                CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname,
                tblroles.name as role,
                tblstaff.day_in as day_in,
                tblstaff.status_work as status_work,
                tbl_payroll_item.salary_kpi as salary_kpi,
                tblstaff.coefficient_responsibility as coefficient_responsibility,
                tblstaff.coefficient_position as coefficient_position,
                tbl_payroll_item.salary_bhxh as salary_bhxh,
                tbl_payroll_item.salary_p2 as salary_p2,
                tbl_payroll_item.salary_p3 as salary_p3,
                tbl_payroll_item.tham_nien as tham_nien,
                tbl_payroll_item.diligence_salary as diligence_salary,
                tbl_payroll_item.salary_p3_new as salary_p3_new,
                tbl_payroll_item.salary as salary,
                tbl_payroll_item.day_number as day_number,
                tbl_payroll_item.day_number_new as day_number_new,
                tbl_payroll_item.day_holiday as day_holiday,
                tbl_payroll_item.day_lt as day_lt,
                tbl_payroll_item.day_ch as day_ch,
                tbl_payroll_item.day_ch as day_ch,
                tbl_payroll_item.day_number_off as day_number_off,
                tbl_payroll_item.total_day_number as total_day_number,
                tbl_payroll_item.salary_income as salary_income,
                tbl_payroll_item.weight_p2 as weight_p2,
                tbl_payroll_item.salary_p2_real as salary_p2_real,
                tbl_payroll_item.diligence as diligence,
                tbl_payroll_item.check_p3 as check_p3,
                tbl_payroll_item.salary_p3_real as salary_p3_real,
                tbl_payroll_item.salary_kpi_real as salary_kpi_real,
                tbl_payroll_item.hour_late as hour_late,
                tbl_payroll_item.money_hour_late as money_hour_late,
                tbl_payroll_item.deduct_bhxh as deduct_bhxh,
                tbl_payroll_item.deduct_bhyt as deduct_bhyt,
                tbl_payroll_item.deduct_bhtn as deduct_bhtn,
                tbl_payroll_item.deduct_union as deduct_union,
                tbl_payroll_item.deduct_advance as deduct_advance,
                tbl_payroll_item.allowance_business_fee as allowance_business_fee,
                tbl_payroll_item.total_allowance_other as total_allowance_other,
                tbl_payroll_item.total_reduce_other as total_reduce_other,
                tbl_payroll_item.total as total,
                tbl_payroll_item.total_real as total_real,
                tbl_payroll_item.total_weekday,
                tbl_payroll_item.total_sunday,
                tbl_payroll_item.total_holiday,
                tbl_payroll_item.total_weekday_night,
                tbl_payroll_item.total_sunday_night,
                tbl_payroll_item.grand_total_kt,
                tbl_payroll_item.grand_total_kl,
                tbl_payroll_item.business_fee_difference,
                tbl_payroll_item.salary_compensation,
                tbl_payroll_item.tax_exemption,
                tbl_payroll_item.complete_permission,
                tbl_payroll_item.taxable_income,
                tbl_payroll_item.total_vat,
                tbl_payroll_item.salary,
                tbl_payroll_item.weight_p2 as weight_p2,
                tbl_payroll_item.weight_p3 as weight_p3,
                tbl_payroll_item.percent_vat as percent_vat,
                tbl_payroll_item.family_deduction as family_deduction,
                tbl_payroll_item.tax_collection as tax_collection,
                tbl_payroll_item.other_adjustments as other_adjustments,
                tbl_payroll_item.allowance_rice as allowance_rice,
                tbl_payroll_item.allowance_rice_tc as allowance_rice_tc,
                tbl_payroll_item.allowance_rice_money as allowance_rice_money,
                tbl_payroll_item.staff_id as staff_id,
                tbl_payroll_item.bhxh_company as bhxh_company,
            ");
        $this->db->from('tbl_payroll');
        $this->db->join('tbl_payroll_item', 'tbl_payroll_item.payroll_id = tbl_payroll.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item.staff_id', 'inner');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        if (!empty($month)) {
            $this->db->where('tbl_payroll.month', $month);
        }

        if (!empty($year)) {
            $this->db->where('tbl_payroll.year', $year);
        }

        if (!empty($branch_search)) {
            $this->db->where('tblstaff.branch_salary', $branch_search);
        }

        if (!empty($staff)) {
            $staff = explode(',', $staff);
            $this->db->where_in('tbl_payroll_item.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where('EXISTS (
                SELECT tblstaff_departments.staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = '.$department.'
            )');
        }

        $personnel = $this->db->get()->result_array();

        $c_excel = [
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
            'DZ',
        ];
        ini_set('memory_limit', '3500M');
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        ob_end_clean();
        $data = [];

        $timekeepingId = 0;

        $styleTh = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        ];
        $styleTd = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];
        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];
        $styleTd_right = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(25);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(20);

        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0'.($decimals_money > 0 ? '.'.sprintf("%0".$decimals_money."s", 0) : '');
        $number_excel_number = '#,##0'.($decimals_number > 0 ? '.'.sprintf("%0".$decimals_number."s", 0) : '');

        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $phonenumber = get_option('invoice_company_phonenumber');
        $styleNone = [
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman',
            ),
        ];

        $company_logo = get_option('company_logo');
        if (file_exists('uploads/company/'.$company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('uploads/company/'.$company_logo);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getStyle("A1");
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setResizeProportional(false);

            $objDrawing->setWidth(55);
            $objDrawing->setHeight(55);
        }

        $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
        $objPHPExcel->getActiveSheet()->setCellValue('B3',
            'SĐT: '.$phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'BẢNG LƯƠNG')->getStyle('A5:Q5')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 25,
                'name' => 'Times New Roman',
                'color' => array('rgb' => 'ff0202'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('A6:Q6');
        $objPHPExcel->getActiveSheet()->setCellValue('A6',
            ('THÁNG '.$month.' NĂM '.$year))->getStyle("A6:Q6")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);
        $rowBegin = 8;
        $rowBeginNext = 9;

        $sttC = 3;
        $stt = 0;
        $sttNew = 1;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'STT'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Mã NV'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Họ Tên'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Chức Vụ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Ngày vào làm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Trạng thái'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng Mức Thu Nhập Theo KPI (P1+P2+P3)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Mức P1 (BHXH Theo Qui Chế Vùng)')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Hệ số lương vị trí'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Hệ số lương chức vụ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Mức lương vị trí (LCB)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Mức P2 (Phụ Cấp Năng Lực Theo KPI)')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Mức P2(Theo năng lực)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Mức P3 (Thu Nhập Cống Hiến Theo KPI)')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thu nhập cống hiến'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thâm niên'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Mức chuyên cần'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Mức P3'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng Thu Nhập (Thỏa thuận)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Giờ Làm Thực Tế')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Số giờ công'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Số ngày công'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Ngày nghỉ')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Phép năm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Lễ tết'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'VR hưởng lương (hiếu hỉ)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Nghỉ không hưởng lương(không lương/không phép)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng ngày công hưởng lương'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'P1')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Lương P1'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'P2')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Điểm KPI'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thu nhập P2 thực tế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'P3')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Chuyên cần thực tế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Mở P3'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thu nhập P3 thực tế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng Thu Nhập Thực Tế Theo KPI (P1+P2+P3)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Trừ đi trễ về sớm')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].($rowBegin))->applyFromArray($styleTh);

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Số giờ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Số tiền'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2 + (count($dtAllowance))].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Phụ cấp')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2 + (count($dtAllowance))].($rowBegin))->applyFromArray($styleTh);

        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
                    $vv['name']."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
                $stt++;
                $sttNew++;
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Ngày cơm hành chính'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Ngày cơm tăng ca'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Tiền ăn hành chính'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng phụ cấp'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 5].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số tiếng tăng ca')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 5].($rowBegin))->applyFromArray($styleTh);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Giờ TC ngày thường(1.5)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Giờ TC chủ nhật(2.0)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Giờ TC lễ tết(3.0)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Giờ TC đêm thường('.get_option('coefficient_default_night').')'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Giờ TC đêm chủ nhật('.get_option('coefficient_sunday_night').')'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBeginNext",
            'Tổng tiền tăng ca'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'BHDN ('.BHDN.' %)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 4 + (count($dtReduce))].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Khấu trừ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 4 + (count($dtReduce))].($rowBegin))->applyFromArray($styleTh);

        if (!empty($dtReduce)) {
            foreach ($dtReduce as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
                    $vv['name']."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
                $stt++;
                $sttNew++;
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '8% BHXH'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '1,5% BHYT'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '1% BHTN'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '0.5% Đoàn phí'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Tổng khấu trừ BHXH + Đoàn phí + Khấu trừ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 5].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Khấu trừ thuế TNCN')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 5].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thuế suất'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Giảm trừ gia cảnh'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thu nhập miễn thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thu nhập chịu thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thu nhập tính thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thuế TNCN'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Khen thưởng KPIs'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Kỹ luật KPIs'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Bù lương'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Điều chỉnh khác'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Hoàn phép năm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Thực lãnh')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Tổng thực lãnh'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Đã tạm ứng'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thực lãnh P1 + P2'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Thực lãnh P3'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        $rowBegin++;
        $rowBegin++;
        if (!empty($personnel)) {
            $iSTT = 1;
            $countStart = $rowBegin;
            foreach ($personnel as $key => $value) {
                $status_work = '';
                if ($value['status_work'] == 1) {
                    $status_work = 'CT';
                } elseif ($value['status_work'] == 0){
                    $status_work = 'TV';
                }
                $stt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $iSTT)->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_center);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $value['code'])->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $value['fullname'])->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $value['role'])->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['day_in']) ? _dhau($value['day_in']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $status_work)->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_kpi']) ? ($value['salary_kpi']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['coefficient_responsibility']) ? ($value['coefficient_responsibility']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right);
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['coefficient_position']) ? ($value['coefficient_position']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_bhxh']) ? ($value['salary_bhxh']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_p2']) ? ($value['salary_p2']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_p3']) ? ($value['salary_p3']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['tham_nien']) ? ($value['tham_nien']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['diligence_salary']) ? ($value['diligence_salary']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_p3_new']) ? ($value['salary_p3_new']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary']) ? ($value['salary']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_number']) ? ($value['day_number']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_number_new']) ? ($value['day_number_new']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_holiday']) ? ($value['day_holiday']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_lt']) ? ($value['day_lt']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_ch']) ? ($value['day_ch']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_number_off']) ? ($value['day_number_off']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_day_number']) ? ($value['total_day_number']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['salary_income']) ? ($value['salary_income']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['weight_p2']) ? ($value['weight_p2'].' %') : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['salary_p2_real']) ? ($value['salary_p2_real']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['diligence']) ? ($value['diligence']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    $value['check_p3'] == 1 ? 'X' : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['salary_p3_real']) ? ($value['salary_p3_real']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['salary_kpi_real']) ? ($value['salary_kpi_real']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['hour_late']) ? ($value['hour_late']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['money_hour_late']) ? ($value['money_hour_late']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                if (!empty($dtAllowance)) {
                    foreach ($dtAllowance as $kk => $vv) {
                        $items = $vv['items'];
                        $checkKey = $value['staff_id'].'_'.$value['id'];
                        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                            (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : ''))->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                        $stt++;
                    }
                }

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_rice']) ? $value['allowance_rice'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_rice_tc']) ? $value['allowance_rice_tc'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_rice_money']) ? $value['allowance_rice_money'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_allowance_other']) ? $value['total_allowance_other'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;


                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_weekday']) ? $value['total_weekday'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_sunday']) ? $value['total_sunday'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_holiday']) ? $value['total_holiday'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_weekday_night']) ? $value['total_weekday_night'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_sunday_night']) ? $value['total_sunday_night'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_business_fee']) ? $value['allowance_business_fee'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['bhxh_company']) ? $value['bhxh_company'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                if (!empty($dtReduce)) {
                    foreach ($dtReduce as $kk => $vv) {
                        $items = $vv['items'];
                        $checkKey = $value['staff_id'].'_'.$value['id'];
                        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                            (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : ''))->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                        $stt++;
                    }
                }

                $total_salary_p1_p2_real = $value['total_real'] - $value['deduct_advance'] - $value['salary_p3_real'];
                $total_salary_p3_real = $value['salary_p3_real'];

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['deduct_bhxh']) ? $value['deduct_bhxh'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_bhyt']) ? $value['deduct_bhyt'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_bhtn']) ? $value['deduct_bhtn'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_union']) ? $value['deduct_union'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['total_reduce_other']) ? $value['total_reduce_other'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['percent_vat']) ? $value['percent_vat']. ' %' : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right);

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['family_deduction']) ? $value['family_deduction'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['tax_exemption']) ? $value['tax_exemption'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['taxable_income']) ? $value['taxable_income'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['tax_collection']) ? $value['tax_collection'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['total_vat']) ? $value['total_vat'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['grand_total_kt']) ? $value['grand_total_kt'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['grand_total_kl']) ? $value['grand_total_kl'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_compensation']) ? $value['salary_compensation'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['other_adjustments']) ? $value['other_adjustments'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['complete_permission']) ? $value['complete_permission'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['total_real']) ? $value['total_real'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_advance']) ? $value['deduct_advance'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $total_salary_p1_p2_real)->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $total_salary_p3_real)->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');;

                $rowBegin++;
                $iSTT++;
            }
            $countEnd = $rowBegin - 1;


            $styleTd_center['font']['color'] = array('rgb' => 'ff0202');
            $styleTd_right['font']['color'] = array('rgb' => 'ff0202');


            $stt = 0;
            $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 5].$rowBegin);
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                'TỔNG')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 5].$rowBegin)->applyFromArray($styleTd_center);
            $stt++;
            $stt++;
            $stt++;
            $stt++;
            $stt++;
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right);
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right);
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;

            if (!empty($dtAllowance)) {
                foreach ($dtAllowance as $kk => $vv) {
                    $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                        "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                    $stt++;
                }
            }

            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;

            if (!empty($dtReduce)) {
                foreach ($dtReduce as $kk => $vv) {
                    $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                        "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
                    $stt++;
                }
            }

            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');
            $stt++;
            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##');

            $rowBegin++;
        }
        $rowBegin++;
        $year = date('Y');
        $objPHPExcel->getActiveSheet()->mergeCells("BT$rowBegin".':'."BW$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("BT$rowBegin",
            'Ngày.....Tháng.....Năm '.$year.'')->getStyle("BT$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ])->getFont()->setItalic(true);
        $rowBegin++;
        $objPHPExcel->getActiveSheet()->mergeCells("B$rowBegin".':'."E$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
            'NGƯỜI LẬP')->getStyle("B$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells("AA$rowBegin".':'."AD$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", 'KẾ TOÁN')->getStyle("AD$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);
        $objPHPExcel->getActiveSheet()->mergeCells("BT$rowBegin".':'."BW$rowBegin");
        $objPHPExcel->getActiveSheet()->setCellValue("BT$rowBegin",
            'GIÁM ĐỐC')->getStyle("BT$rowBegin")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);


        $objPHPExcel->getActiveSheet()->freezePane('A1');
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="BANG_LUONG_THANG_'.$month.'_NAM_'.$year.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }

    public function payroll_payment()
    {
        if (!$this->perViewPayrollPayment && !$this->perViewOwnPayrollPayment) {
            accessDenied();
        }
        $data['tnh'] = true;
        $data['title'] = _l('Phiếu tạm ứng lương');
        $this->load->view('admin/payroll/index_payroll_payment', $data);
    }

    public function getPayrollPayment()
    {

        $staff = $this->input->post('staff');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $status_table = $this->input->post('status_table');

        $referencePayrollItem = "(
            SELECT
                GROUP_CONCAT(CONCAT(tbl_payroll_item.code,'__',tbl_payroll_payment_item.total  )SEPARATOR '||')
            FROM tbl_payroll_payment_item
            INNER JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_payroll_payment_item.payroll_item_id
            WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id
        )";
        $payrollPayment = "(
            SELECT COUNT(*)
            FROM tbl_payroll_payment_item
            INNER JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_payroll_payment_item.payroll_item_id
            WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id
            LIMIT 1
        )";
        $this->datatables->select("
            tbl_payroll_payment.id as id,
            tbl_payroll_payment.code as code,
            tbl_payroll_payment.date as date,
            CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as staff_name,
            tbl_payroll_payment.amount as amount,
            $referencePayrollItem as code_payroll_item,
            tbl_payroll_payment.note as note,
            ", false)
            ->from('tbl_payroll_payment')
            ->join('tblstaff', 'tblstaff.staffid = tbl_payroll_payment.staff_id', 'left')
            ->group_by('tbl_payroll_payment.id');


        if ($status_table != 'all') {
            if ($status_table == "un_approved") {
                $this->datatables->where("$payrollPayment =", 0);
            } elseif ($status_table == "approved") {
                $this->datatables->where("$payrollPayment >", 0);
            }
        }

        if (!empty($staff)) {
            $this->datatables->where('tbl_payroll_payment.staff_id', $staff);
        }

        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=',
                to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=',
                to_sql_date($end_date_search));
        }

        $edit = $this->perEditPayrollPayment ? '<a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/payroll/edit_payroll_payment/$1').'"><i class="fa fa-edit"></i> '.lang('edit').' '.lang('phiếu').'</a>' : '';

        $delete = $this->perDeletePayrollPayment ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\''.base_url('admin/payroll/deletePayrollPayment/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
         "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').' '.lang('phiếu').'</a>' : '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            '.lang('actions').'
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>'.$edit.'</li>
                <li class="not-outside">'.$delete.'</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $date = $value[2];

            $data->aaData[$key][2] = _dhau($date);
        }
        echo json_encode($data);
    }

    public function searchStaffPayment($id = '')
    {
        $data = [];
        $term = $this->input->get('term');

        $limit = 50;
        $this->db->select('tblstaff.staffid as id,  CONCAT(tblstaff.firstname," ",tblstaff.lastname) as text');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.active', 1);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('CONCAT(tblstaff.firstname," ",tblstaff.lastname)', $term);
            $this->db->or_like('tblstaff.code', $term);
            $this->db->group_end();
        }
        $staffs = $this->db->get()->result_array();
        $data['results'] = $staffs;
        if ($id) {
            $this->db->select('tblstaff.staffid as id, CONCAT(tblstaff.firstname," ",tblstaff.lastname) as text');
            $this->db->from('tblstaff');
            $this->db->where('tblstaff.active', 1);
            $this->db->where('tblstaff.staffid', $id);
            $staff = $this->db->get()->row_array();
            $data['row'] = [
                'id' => $staff['id'],
                'text' => $staff['text'],
            ];

        }

        echo json_encode($data);
    }

    public function add_payroll_payment()
    {
        if (!$this->perAddPayrollPayment) {
            accessDenied($js = true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_payroll_payment.code]');
            $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
            $this->form_validation->set_rules('amount', lang("Số tiền"), 'required');
            if ($this->form_validation->run() == true) {
                $code = get_option('prefix_payroll_payment').sprintf('%06d',
                        ch_getMaxID('id', 'tbl_payroll_payment') + 1);
                $date = to_sql_date($this->input->post('date'));
                $staff_id = $this->input->post('staff_id');
                $amount = number_unformat($this->input->post('amount'));
                $note = $this->input->post('note');


                $options = [
                    'code' => $code,
                    'date' => $date,
                    'staff_id' => $staff_id,
                    'amount' => $amount,
                    'note' => $note,
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                ];
                $this->db->insert('tbl_payroll_payment', $options);
                $id = $this->db->insert_id();
                if ($id) {
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

            return;
        } else {
            $this->load->view('admin/payroll/add_payroll_payment', $data);
        }
    }

    public function edit_payroll_payment($id)
    {
        if (!$this->perEditPayrollPayment) {
            accessDenied($js = true);
        }
        $data = [];
        $payrollPayment = get_table_where('tbl_payroll_payment', ['id' => $id], '', 'row_array');
        if ($this->input->post()) {
            $this->db->select('tbl_payroll_payment.*');
            $this->db->join('tbl_payroll_payment_item',
                'tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id', 'left');
            $this->db->where('tbl_payroll_payment.id', $id);
            $this->db->where('tbl_payroll_payment_item.payroll_id !=', 0);
            $this->db->where('tbl_payroll_payment_item.payroll_item_id !=', 0);
            $result = $this->db->get('tbl_payroll_payment')->num_rows();
            if ($result) {
                $data['result'] = 0;
                $data['message'] = lang('Đã áp dụng vào bảng lương không thể sửa');
                echo json_encode($data);

                return;
            }
            if ($payrollPayment['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_payroll_payment.code]');
            }
            $this->form_validation->set_rules('staff_id', lang("Nhân viên"), 'required');
            $this->form_validation->set_rules('amount', lang("Số tiền"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $date = to_sql_date($this->input->post('date'));
                $staff_id = $this->input->post('staff_id');
                $amount = number_unformat($this->input->post('amount'));
                $note = $this->input->post('note');


                $options = [
                    'code' => $code,
                    'date' => $date,
                    'staff_id' => $staff_id,
                    'amount' => $amount,
                    'note' => $note,
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tbl_payroll_payment', $options);
                if ($success) {
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

            return;
        } else {
            $data['payrollPayment'] = $payrollPayment;
            $data['id'] = $id;
            $this->load->view('admin/payroll/edit_payroll_payment', $data);
        }
    }

    public function deletePayrollPayment($id)
    {
        if (!$this->perDeletePayrollPayment) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die();
        }
        $data = [];
        if ($id) {

            $this->db->select('tbl_payroll_payment.*');
            $this->db->join('tbl_payroll_payment_item',
                'tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id', 'left');
            $this->db->where('tbl_payroll_payment.id', $id);
            $this->db->where('tbl_payroll_payment_item.payroll_id !=', 0);
            $this->db->where('tbl_payroll_payment_item.payroll_item_id !=', 0);
            $result = $this->db->get('tbl_payroll_payment')->num_rows();
            if ($result) {
                $data['result'] = 0;
                $data['message'] = lang('Đã áp dụng vào bảng lương không thể xoá');
                echo json_encode($data);

                return;
            }

            $this->db->where('id', $id);
            $success = $this->db->delete('tbl_payroll_payment');
            if ($success) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function count_all_payroll_payment()
    {
        $data = [];
        $staff = $this->input->post('staff');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');


        $this->db->select('count(*) as all1');
        if (!empty($staff)) {
            $this->db->where('tbl_payroll_payment.staff_id', $staff);
        }
        if (!empty($start_date_search)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }
        if (!empty($end_date_search)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }

        $all = $this->db->get('tbl_payroll_payment')->row_array();

        $payrollPayment = "(
            SELECT COUNT(*)
            FROM tbl_payroll_payment_item
            INNER JOIN tbl_payroll_item ON tbl_payroll_item.id = tbl_payroll_payment_item.payroll_item_id
            WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id
            LIMIT 1
        )";
        $this->db->select('count(*) as approved');
        $this->db->where("$payrollPayment >", 0);
        if (!empty($staff)) {
            $this->db->where('tbl_payroll_payment.staff_id', $staff);
        }
        if (!empty($start_date_search)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }
        if (!empty($end_date_search)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }
        $approved = $this->db->get('tbl_payroll_payment')->row_array();

        $this->db->select('count(*) as un_approved');
        $this->db->where("$payrollPayment =", 0);
        if (!empty($staff)) {
            $this->db->where('tbl_payroll_payment.staff_id', $staff);
        }
        if (!empty($start_date_search)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }
        if (!empty($end_date_search)) {
            $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }
        $un_approved = $this->db->get('tbl_payroll_payment')->row_array();


        $data['all'] = $all['all1'];
        $data['approved'] = $approved['approved'];
        $data['un_approved'] = $un_approved['un_approved'];


        echo json_encode($data);
    }

    public function print_pdf_payroll_salary_new()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');
        $branch_search = $this->input->get('branch_search');
        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select("
            tbl_payroll_item.id as id,
            tblstaff.code as code,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as fullname,
            tblroles.name as role,
            tb_department.name_department as name_department,
            tblstaff.day_in as day_in,
            tbl_payroll_item.salary_bhxh as salary_bhxh,
            tbl_payroll_item.staff_id as staff_id,
            tbl_payroll_item.salary_responsibility as salary_responsibility,
            tbl_payroll_item.salary_position as salary_position,
            tbl_payroll_item.allowance as allowance,
            tbl_payroll_item.day_number as day_number,
            tbl_payroll_item.day_number_new as day_number_new,
            tbl_payroll_item.day_holiday as day_holiday,
            tbl_payroll_item.day_lt as day_lt,
            tbl_payroll_item.salary_income as salary_income,
            tbl_payroll_item.allowance_responsibility as allowance_responsibility,
            tbl_payroll_item.allowance_other as allowance_other,
            tbl_payroll_item.allowance_manu as allowance_manu,
            tbl_payroll_item.allowance_western as allowance_western,
            tbl_payroll_item.allowance_business_fee as allowance_business_fee,
            tbl_payroll_item.allowance_rice as allowance_rice,
            tbl_payroll_item.allowance_rice_tc as allowance_rice_tc,
            tbl_payroll_item.allowance_rice_money as allowance_rice_money,
            tbl_payroll_item.bonus_holiday as bonus_holiday,
            tbl_payroll_item.deduct_bhxh as deduct_bhxh,
            tbl_payroll_item.deduct_bhyt as deduct_bhyt,
            tbl_payroll_item.deduct_bhtn as deduct_bhtn,
            tbl_payroll_item.deduct_union as deduct_union,
            tbl_payroll_item.deduct_advance as deduct_advance,
            tbl_payroll_item.total_allowance_other as total_allowance_other,
            tbl_payroll_item.total_reduce_other as total_reduce_other,
            tbl_payroll_item.total as total,
            tbl_payroll_item.total_real as total_real,
            tbl_payroll_item.total_vat as total_vat,
            tbl_business_fee_boiler_calculate_item.total_weekday,
            tbl_business_fee_boiler_calculate_item.total_sunday,
            tbl_business_fee_boiler_calculate_item.total_holiday,
            tbl_business_fee_boiler_calculate_item.total_weekday_night,
            tbl_business_fee_boiler_calculate_item.total_sunday_night,
        ");
        $this->db->from('tbl_payroll');
        $this->db->join('tbl_payroll_item', 'tbl_payroll_item.payroll_id = tbl_payroll.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item.staff_id', 'inner');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_business_fee_boiler_calculate_item',
            'tbl_business_fee_boiler_calculate_item.id = tbl_payroll_item.business_fee_boiler_calculate_item_id',
            'left');
        if (!empty($month)) {
            $this->db->where('tbl_payroll.month', $month);
        }

        if (!empty($year)) {
            $this->db->where('tbl_payroll.year', $year);
        }

        if (!empty($branch_search)) {
            $this->db->where('tblstaff.branch_salary', $branch_search);
        }

        if (!empty($staff)) {
            $staff = explode(',', $staff);
            $this->db->where_in('tbl_payroll_item.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where('EXISTS (
                SELECT tblstaff_departments.staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = '.$department.'
            )');
        }

        $personnel = $this->db->get()->result_array();
        $data['personnel'] = $personnel;
        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        $data['dtAllowance'] = $dtAllowance;
        $data['dtReduce'] = $dtReduce;
        $this->load->view('admin/payroll/print_payroll', $data);
    }

    public function callAllowanceStaff()
    {
        $data = [];

        $cId = $this->input->post('cId');
        $cStaffId = $this->input->post('cStaffId');
        $data_json = $this->input->post('data_json');
        if (!empty($data_json)) {
            $data_json = json_decode($data_json, true);
        }

        $this->db->select("
            tbl_staff_allowance.id as id,
            tbl_allowance_reduce.name as name,
            tbl_allowance_reduce.amount as amount,
        ");
        $this->db->from('tbl_staff_allowance');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_staff_allowance.staff_id');
        $this->db->join('tbl_allowance_reduce', 'tbl_allowance_reduce.id = tbl_staff_allowance.category_id');
        $this->db->where('tblstaff.staffid', $cStaffId);
        $payrollAllowance = $this->db->get()->result_array();


        $data['data_json'] = $data_json;
        $data['cId'] = $cId;
        $data['cStaffId'] = $cStaffId;
        $data['payrollPayments'] = $payrollAllowance;
        $data['title'] = 'Sửa phụ cấp';

        $this->load->view('admin/payroll/call_allowance', $data);
    }

    public function print_pdf_payroll_salary_audit()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $staff = $this->input->get('staff');
        $department = $this->input->get('department');
        $branch_search = $this->input->get('branch_search');
        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select("
            tbl_payroll_item_audit.id as id,
            tblstaff.code as code,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as fullname,
            tblroles.name as role,
            tb_department.name_department as name_department,
            tblstaff.day_in as day_in,
            tbl_payroll_item_audit.salary_bhxh as salary_bhxh,
            tbl_payroll_item_audit.staff_id as staff_id,
            tbl_payroll_item_audit.salary_responsibility as salary_responsibility,
            tbl_payroll_item_audit.salary_position as salary_position,
            tbl_payroll_item_audit.allowance as allowance,
            tbl_payroll_item_audit.day_number as day_number,
            tbl_payroll_item_audit.day_number_new as day_number_new,
            tbl_payroll_item_audit.day_holiday as day_holiday,
            tbl_payroll_item_audit.day_lt as day_lt,
            tbl_payroll_item_audit.salary_income as salary_income,
            tbl_payroll_item_audit.allowance_responsibility as allowance_responsibility,
            tbl_payroll_item_audit.allowance_other as allowance_other,
            tbl_payroll_item_audit.allowance_manu as allowance_manu,
            tbl_payroll_item_audit.allowance_western as allowance_western,
            tbl_payroll_item_audit.allowance_business_fee as allowance_business_fee,
            tbl_payroll_item_audit.allowance_rice as allowance_rice,
            tbl_payroll_item_audit.allowance_rice_tc as allowance_rice_tc,
            tbl_payroll_item_audit.allowance_rice_money as allowance_rice_money,
            tbl_payroll_item_audit.bonus_holiday as bonus_holiday,
            tbl_payroll_item_audit.deduct_bhxh as deduct_bhxh,
            tbl_payroll_item_audit.deduct_bhyt as deduct_bhyt,
            tbl_payroll_item_audit.deduct_bhtn as deduct_bhtn,
            tbl_payroll_item_audit.deduct_union as deduct_union,
            tbl_payroll_item_audit.deduct_advance as deduct_advance,
            tbl_payroll_item_audit.total_allowance_other as total_allowance_other,
            tbl_payroll_item_audit.total_reduce_other as total_reduce_other,
            tbl_payroll_item_audit.total as total,
            tbl_payroll_item_audit.total_real as total_real,
            tbl_payroll_item_audit.total_vat as total_vat,
            tbl_payroll_item_audit.total_weekday,
            tbl_payroll_item_audit.total_sunday,
            tbl_payroll_item_audit.total_holiday,
            tbl_payroll_item_audit.total_weekday_night,
            tbl_payroll_item_audit.total_sunday_night,
        ");
        $this->db->from('tbl_payroll_audit');
        $this->db->join('tbl_payroll_item_audit', 'tbl_payroll_item_audit.payroll_id = tbl_payroll_audit.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item_audit.staff_id', 'inner');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_business_fee_boiler_calculate_item',
            'tbl_business_fee_boiler_calculate_item.id = tbl_payroll_item_audit.business_fee_boiler_calculate_item_id',
            'left');
        if (!empty($month)) {
            $this->db->where('tbl_payroll_audit.month', $month);
        }

        if (!empty($year)) {
            $this->db->where('tbl_payroll_audit.year', $year);
        }

        if (!empty($branch_search)) {
            $this->db->where('tblstaff.branch_salary', $branch_search);
        }

        if (!empty($staff)) {
            $staff = explode(',', $staff);
            $this->db->where_in('tbl_payroll_item_audit.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where('EXISTS (
                SELECT tblstaff_departments.staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = '.$department.'
            )');
        }

        $personnel = $this->db->get()->result_array();
        $data['personnel'] = $personnel;
        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        $data['dtAllowance'] = $dtAllowance;
        $data['dtReduce'] = $dtReduce;
        $this->load->view('admin/payroll/print_payroll_audit', $data);
    }


    public function payroll_salary_audit(){
        if (!$this->perViewPayrollSalary && !$this->perViewOwnPayrollSalary) {
            accessDenied();
        }
        $data['staff'] = getPersonDeparmentdt(0);
        $data['title'] = lang('BẢNG LƯƠNG AUDIT');
        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        $colspanAllowance = 3 + count($dtAllowance);
        $colspanReduce = 1 + count($dtReduce);
        $data['dtAllowance'] = $dtAllowance;
        $data['dtReduce'] = $dtReduce;
        $data['colspanAllowance'] = $colspanAllowance;
        $data['colspanReduce'] = $colspanReduce;
        $data['branch'] = getListBranch();
        $this->load->view('admin/payroll/index_payroll_salary_audit', $data);
    }

    public function getPayrollAudit()
    {
        $arrIDStaff = employee_manage_staff();
        $arrBranch = get_branch_staff();
        $staff_search = $this->input->post('staff');
        $month_search = $this->input->post('month');
        $year_search = $this->input->post('year');
        $department_search = $this->input->post('department');
        $branch_search = $this->input->post('branch_search');
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $footer_total_allowance = '';
        $footer_total_reduce = '';
        $arrFooter = [];

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll_audit',
                    ['category_id' => $value['id'], 'type' => 1], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtAllowance[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_allowance_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);

            }
        }

        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll_audit',
                    ['category_id' => $value['id'], 'type' => 2], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtReduce[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_reduce_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);
            }
        }

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";

        $aColumns = [
            'tbl_payroll_item_audit.id as id',
            'tblstaff.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as fullname',
            'tblroles.name as role',
            'tblstaff.day_in as day_in',
            'tbl_payroll_item_audit.salary as salary',
            'tbl_payroll_item_audit.salary_bhxh as salary_bhxh',
            'tbl_payroll_item_audit.allowance as allowance',
            'tbl_payroll_item_audit.day_number as day_number',
            'tbl_payroll_item_audit.salary_income as salary_income',
            'tbl_payroll_item_audit.allowance_rice as allowance_rice',
            'tbl_payroll_item_audit.allowance_rice_money as allowance_rice_money',
            'tbl_payroll_item_audit.deduct_bhxh as deduct_bhxh',
            'tbl_payroll_item_audit.deduct_bhyt as deduct_bhyt',
            'tbl_payroll_item_audit.deduct_bhtn as deduct_bhtn',
            'tbl_payroll_item_audit.deduct_union as deduct_union',
            'tbl_payroll_item_audit.deduct_advance as deduct_advance',
            'tbl_payroll_item_audit.total_allowance_other as total_allowance_other',
            'tbl_payroll_item_audit.total as total',
            'tbl_payroll_item_audit.total_real as total_real',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_payroll_item_audit';
        $where = [
        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_payroll_audit ON tbl_payroll_audit.id = tbl_payroll_item_audit.payroll_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_payroll_item_audit.staff_id',
            'LEFT JOIN '.$tbDepartment.' ON tb_department.staffid = tblstaff.staffid',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN tbl_business_fee_boiler_calculate_item ON tbl_business_fee_boiler_calculate_item.id = tbl_payroll_item_audit.business_fee_boiler_calculate_item_id',
        ];

        if (!empty($department_search)) {
            array_push($where, "AND EXISTS (
                    SELECT tblstaff_departments.staffid 
                    FROM tblstaff_departments 
                    WHERE tblstaff_departments.staffid = tblstaff.staffid
                    AND tblstaff_departments.departmentid = $department_search
                )");
        }

        if (!empty($staff_search)) {
            array_push($where,
                'AND ( tbl_payroll_item_audit.staff_id IN ('.implode(',', $staff_search).'))');
        }
        if (!empty($month_search)) {
            array_push($where,
                'AND ( tbl_payroll_audit.month = '.$month_search.')');
        }
        if (!empty($year_search)) {
            array_push($where,
                'AND ( tbl_payroll_audit.year = '.$year_search.')');
        }

        if (!$this->isAdmin && $this->perViewOwnPayrollSalary) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                array_push($where, 'AND tbl_payroll_item_audit.staff_id IN ('.$coverStr.')');
            }
        }

        if (!empty($branch_search)) {
            array_push($where,
                'AND ( tblstaff.branch_salary = '.$branch_search.')');
        }

        if (!$this->isAdmin) {
            if (!empty($arrBranch)) {
                $coverStrBranch = implode(",", $arrBranch);
                array_push($where, 'AND tblstaff.branch_salary IN ('.$coverStrBranch.')');
            } else {
                array_push($where,
                    'AND (tbl_payroll_item_audit.id = 0)');
            }
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblstaff.staffid as staff_id',
            'tbl_payroll_item_audit.allowance_business_fee as allowance_business_fee',
            'tbl_payroll_item_audit.total_reduce_other as total_reduce_other',
            'tbl_payroll_item_audit.total_weekday',
            'tbl_payroll_item_audit.total_sunday',
            'tbl_payroll_item_audit.total_holiday',
            'tbl_payroll_item_audit.total_weekday_night',
            'tbl_payroll_item_audit.total_sunday_night',
            'tbl_payroll_item_audit.bonus_holiday as bonus_holiday',
            'tbl_payroll_item_audit.allowance_responsibility as allowance_responsibility',
            'tbl_payroll_item_audit.allowance_other as allowance_other',
            'tbl_payroll_item_audit.day_number_new as day_number_new',
            'tbl_payroll_item_audit.allowance_rice_tc as allowance_rice_tc',
            'tbl_payroll_item_audit.day_holiday as day_holiday',
            'tbl_payroll_item_audit.day_lt as day_lt',
            'tbl_payroll_item_audit.salary_responsibility as salary_responsibility',
            'tbl_payroll_item_audit.salary_position as salary_position',
            'tbl_payroll_item_audit.responsibility_salary as responsibility_salary',
            'tbl_payroll_item_audit.day_ch as day_ch',
            'tbl_payroll_item_audit.day_number_off as day_number_off',
            'tbl_payroll_item_audit.day_number_off_new as day_number_off_new',
            'tbl_payroll_item_audit.salary_off as salary_off',
            'tbl_payroll_item_audit.hour_late as hour_late',
            'tbl_payroll_item_audit.money_hour_late as money_hour_late',
            'tbl_payroll_item_audit.sales as sales',
            'tbl_payroll_item_audit.gasonline_cars as gasonline_cars',
            'tbl_payroll_item_audit.phone as phone',
            'tbl_payroll_item_audit.motel as motel',
            'tbl_payroll_item_audit.concurrently as concurrently',
            'tbl_payroll_item_audit.business_fee_staff as business_fee_staff',
            'tbl_payroll_item_audit.number_reduce as number_reduce',
            'tbl_payroll_item_audit.business_fee_difference as business_fee_difference',
            'tbl_payroll_item_audit.allowance_diff as allowance_diff',
            'tbl_payroll_item_audit.total_vat as total_vat',
            'tbl_payroll_item_audit.seniority as seniority',
            'tbl_payroll_item_audit.complete_permission as complete_permission',
            'tbl_payroll_item_audit.salary_compensation as salary_compensation',
            'tbl_payroll_item_audit.tax_exemption as tax_exemption',
            'tbl_payroll_item_audit.taxable_income as taxable_income',
            'tbl_payroll_item_audit.income_taxes as income_taxes',
            'tbl_payroll_item_audit.total_reduce_bhxh as total_reduce_bhxh',
            'tbl_payroll_item_audit.allowance_family as allowance_family',
            'tbl_payroll_item_audit.total_number_day_kp as total_number_day_kp',
            'tbl_payroll_item_audit.total_number_day_od as total_number_day_od',
            'tbl_payroll_item_audit.grand_total_kt as grand_total_kt',
            'tbl_payroll_item_audit.grand_total_kl as grand_total_kl',
        ], '', [], []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        $footer_salary = 0;
        $footer_salary_bhxh = 0;
        $footer_salary_responsibility = 0;
        $footer_salary_position = 0;
        $footer_responsibility_salary = 0;
        $footer_sales = 0;
        $footer_gasonline_cars = 0;
        $footer_phone = 0;
        $footer_motel = 0;
        $footer_concurrently = 0;
        $footer_business_fee_staff = 0;
        $footer_seniority = 0;
        $footer_tong_phu_cap = 0;
        $footer_total_vat = 0;
        $footer_allowance = 0;
        $footer_total_salary_new = 0;
        $footer_day_number = 0;
        $footer_day_number_new = 0;
        $footer_day_number_holiday = 0;
        $footer_day_number_lt = 0;
        $footer_day_number_ch = 0;
        $footer_day_number_kp = 0;
        $footer_day_number_od = 0;
        $footer_total_number_day = 0;
        $footer_day_number_off = 0;
        $footer_day_number_off_new = 0;
        $footer_salary_off = 0;
        $footer_salary_income = 0;
        $footer_allowance_responsibility = 0;
        $footer_allowance_other = 0;
        $footer_allowance_manu = 0;
        $footer_allowance_western = 0;
        $footer_allowance_business_fee = 0;
        $footer_allowance_rice = 0;
        $footer_allowance_rice_tc = 0;
        $footer_allowance_rice_money = 0;
        $footer_bonus_holiday = 0;
        $footer_deduct_bhxh = 0;
        $footer_deduct_bhyt = 0;
        $footer_deduct_bhtn = 0;
        $footer_deduct_union = 0;
        $footer_total_allowance_other = 0;
        $footer_total_reduce_other = 0;
        $footer_deduct_advance = 0;
        $footer_total = 0;
        $footer_total_real = 0;

        $footer_grand_total_kt = 0;
        $footer_grand_total_kl = 0;

        $footer_hour_late = 0;
        $footer_money_hour_late = 0;

        $footer_business_fee_difference = 0;
        $footer_salary_compensation = 0;
        $footer_tax_exemption = 0;
        $footer_complete_permission = 0;
        $footer_taxable_income = 0;
        $footer_total_reduce_bhxh = 0;
        $footer_allowance_family = 0;
        $footer_income_taxes = 0;
        foreach ($rResult as $key => $aRow) {
            $start++;

            $row = array();

            $row[0] = '<div class="text-center checkbox" style="width: 40px"><input type="checkbox" name="items[]" id="check-item'.$aRow['id'].'" value="'.$aRow['id'].'"><label for="check-item'.$aRow['id'].'"></label></div>';
            $row[1] = '<div style="width: 120px">'.$aRow['code'].'</div>';
            $row[2] = '<div style="width: 200px">'.$aRow['fullname'].'</div>';
            $row[3] = '<div style="width: 150px" class="text-left">'.$aRow['role'].'</div>';
            $row[4] = '<div style="width: 100px" class="text-left">'.(!empty($aRow['day_in']) ? _dhau($aRow['day_in']) : '').'</div>';
            $row[5] = '<div style="width: 100px;text-align: right">'.(!empty($aRow['salary_bhxh']) ? formatMoney($aRow['salary_bhxh']) : '').'</div>';
            $row[6] = '<div style="width: 80px;text-align: right">'.(!empty($aRow['salary_position']) ? formatMoney($aRow['salary_position']) : '').'</div>';
            $row[7] = '<div style="text-align: right">'.(!empty($aRow['salary_responsibility']) ? formatMoney($aRow['salary_responsibility']) : '').'</div>';
            $salary = $aRow['salary_bhxh'] + $aRow['salary_position'] + $aRow['salary_responsibility'];
            $row[8] = '<div style="width: 100px;text-align: right">'.(!empty($salary) ? formatMoney($salary) : '').'</div>';
            $row[9] = '<div style="text-align: right">'.(!empty($aRow['concurrently']) ? formatMoney($aRow['concurrently']) : '').'</div>';
            $row[10] = '<div style="text-align: right">'.(!empty($aRow['sales']) ? formatMoney($aRow['sales']) : '').'</div>';
            $row[11] = '<div style="text-align: right">'.(!empty($aRow['business_fee_staff']) ? formatMoney($aRow['business_fee_staff']) : '').'</div>';
            $row[12] = '<div style="text-align: right">'.(!empty($aRow['phone']) ? formatMoney($aRow['phone']) : '').'</div>';
            $row[13] = '<div style="text-align: right">'.(!empty($aRow['gasonline_cars']) ? formatMoney($aRow['gasonline_cars']) : '').'</div>';
            $row[14] = '<div style="text-align: right">'.(!empty($aRow['motel']) ? formatMoney($aRow['motel']) : '').'</div>';
            $totalPhuCap = $aRow['concurrently'] + $aRow['sales'] + $aRow['business_fee_staff'] + $aRow['phone'] + $aRow['gasonline_cars'] + $aRow['motel'];
            $row[15] = '<div style="text-align: right">'.(!empty($totalPhuCap) ? formatMoney($totalPhuCap) : '').'</div>';
            $row[16] = '<div style="text-align: center">'.($aRow['seniority'] > 0 ? formatMoney($aRow['seniority']) : '').'</div>';
            $row[17] = '<div style="text-align: center">'.($aRow['day_number'] > 0 ? ($aRow['day_number']) : '').'</div>';
            $row[18] = '<div style="text-align: center">'.($aRow['day_number_new'] > 0 ? ($aRow['day_number_new']) : '').'</div>';
            $row[19] = '<div style="text-align: center">'.($aRow['day_holiday'] > 0 ? ($aRow['day_holiday']) : '').'</div>';
            $row[20] = '<div style="text-align: center">'.($aRow['day_lt'] > 0 ? ($aRow['day_lt']) : '').'</div>';
            $row[21] = '<div style="text-align: center">'.($aRow['day_ch'] > 0 ? ($aRow['day_ch']) : '').'</div>';
            $row[22] = '<div style="text-align: center">'.($aRow['total_number_day_kp'] > 0 ? ($aRow['total_number_day_kp']) : '').'</div>';
            $row[23] = '<div style="text-align: center">'.($aRow['total_number_day_od'] > 0 ? ($aRow['total_number_day_od']) : '').'</div>';
            $totalNumberDay = $aRow['day_number_new'] + $aRow['day_holiday'] + $aRow['day_lt'] + $aRow['day_ch'];
            $row[24] = '<div style="text-align: center">'.($totalNumberDay > 0 ? $totalNumberDay : '').'</div>';
            $row[25] = '<div style="text-align: right">'.($aRow['salary_income'] > 0 ? formatMoney($aRow['salary_income']) : '').'</div>';
            $row[26] = '<div style="text-align: center">'.(!empty($aRow['total_weekday']) ? formatMoney($aRow['total_weekday']) : '').'</div>';
            $row[27] = '<div style="text-align: center">'.(!empty($aRow['total_sunday']) ? formatMoney($aRow['total_sunday']) : '').'</div>';
            $row[28] = '<div style="text-align: center">'.(!empty($aRow['total_holiday']) ? formatMoney($aRow['total_holiday']) : '').'</div>';
            $row[29] = '<div style="text-align: center">'.(!empty($aRow['total_weekday_night']) ? formatMoney($aRow['total_weekday_night']) : '').'</div>';
            $row[30] = '<div style="text-align: center">'.(!empty($aRow['total_sunday_night']) ? formatMoney($aRow['total_sunday_night']) : '').'</div>';
            $row[31] = '<div style="text-align: right">'.(!empty($aRow['allowance_business_fee']) ? formatMoney($aRow['allowance_business_fee']) : '').'</div>';
            $keyNew = 31;
            if (!empty($dtAllowance)) {
                foreach ($dtAllowance as $kk => $vv) {
                    $items = $vv['items'];
                    $checkKey = $aRow['staff_id'].'_'.$aRow['id'];
                    $keyNew++;
                    $row[$keyNew] = '<div style="text-align: right">'.(!empty($items[$checkKey]['amount']) ? formatMoney($items[$checkKey]['amount']) : '').'</div>';
                    $keyCheck = 'footer_total_allowance_'.$vv['id'];
                    $arrFooter[$keyCheck] += (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : 0);
                }
            }
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.($aRow['allowance_rice'] > 0 ? formatNumber($aRow['allowance_rice']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: center">'.($aRow['allowance_rice_tc'] > 0 ? formatNumber($aRow['allowance_rice_tc']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['allowance_rice_money'] > 0 ? formatMoney($aRow['allowance_rice_money']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['salary_compensation'] > 0 ? formatMoney($aRow['salary_compensation']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['total_allowance_other'] > 0 ? formatMoney($aRow['total_allowance_other']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['total'] > 0 ? formatMoney($aRow['total']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right">'.($aRow['deduct_advance'] > 0 ? formatMoney($aRow['deduct_advance']) : '').'</div>';
            if (!empty($dtReduce)) {
                foreach ($dtReduce as $kk => $vv) {
                    $items = $vv['items'];
                    $checkKey = $aRow['staff_id'].'_'.$aRow['id'];
                    $keyNew++;
                    $row[$keyNew] = '<div style="text-align: right">'.(!empty($items[$checkKey]['amount']) ? formatMoney($items[$checkKey]['amount']) : '').'</div>';
                    $keyCheck = 'footer_total_reduce_'.$vv['id'];
                    $arrFooter[$keyCheck] += (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : 0);
                }
            }
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_reduce_other'] != 0 ? formatMoney($aRow['total_reduce_other']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_bhxh'] > 0 ? formatMoney($aRow['deduct_bhxh']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_bhyt'] > 0 ? formatMoney($aRow['deduct_bhyt']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_bhtn'] > 0 ? formatMoney($aRow['deduct_bhtn']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 80px">'.($aRow['deduct_union'] > 0 ? formatMoney($aRow['deduct_union']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_reduce_bhxh'] != 0 ? formatMoney($aRow['total_reduce_bhxh']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['grand_total_kt'] != 0 ? formatMoney($aRow['grand_total_kt']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['grand_total_kl'] != 0 ? formatMoney($aRow['grand_total_kl']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['allowance_family'] != 0 ? formatMoney($aRow['allowance_family']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['business_fee_difference'] != 0 ? formatMoney($aRow['business_fee_difference']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['tax_exemption'] != 0 ? formatMoney($aRow['tax_exemption']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['complete_permission'] != 0 ? formatMoney($aRow['complete_permission']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['income_taxes'] != 0 ? formatMoney($aRow['income_taxes']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['taxable_income'] != 0 ? formatMoney($aRow['taxable_income']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_vat'] != 0 ? formatMoney($aRow['total_vat']) : '').'</div>';
            $keyNew++;
            $row[$keyNew] = '<div style="text-align: right;width: 120px">'.($aRow['total_real'] != 0 ? formatMoney($aRow['total_real']) : '').'</div>';

            $footer_salary += $salary;
            $footer_salary_bhxh += $aRow['salary_bhxh'];
            $footer_salary_responsibility += $aRow['salary_responsibility'];
            $footer_salary_position += $aRow['salary_position'];
            $footer_responsibility_salary += $aRow['responsibility_salary'];
            $footer_sales += $aRow['sales'];
            $footer_gasonline_cars += $aRow['gasonline_cars'];
            $footer_phone += $aRow['phone'];
            $footer_motel += $aRow['motel'];
            $footer_concurrently += $aRow['concurrently'];
            $footer_business_fee_staff += $aRow['business_fee_staff'];
            $footer_tong_phu_cap += $totalPhuCap;
            $footer_total_vat += $aRow['total_vat'];
            $footer_business_fee_difference += $aRow['business_fee_difference'];
            $footer_allowance += $aRow['allowance'];
            $footer_total_salary_new += ($aRow['salary_bhxh'] + $aRow['allowance']);
            $footer_day_number += $aRow['day_number'];
            $footer_day_number_new += $aRow['day_number_new'];
            $footer_day_number_holiday += $aRow['day_holiday'];
            $footer_day_number_lt += $aRow['day_lt'];
            $footer_day_number_ch += $aRow['day_ch'];
            $footer_day_number_kp += $aRow['total_number_day_kp'];
            $footer_day_number_od += $aRow['total_number_day_od'];
            $footer_total_number_day += $totalNumberDay;
            $footer_day_number_off += $aRow['day_number_off'];
            $footer_day_number_off_new += $aRow['day_number_off_new'];
            $footer_money_hour_late += $aRow['money_hour_late'];
            $footer_hour_late += $aRow['hour_late'];
            $footer_seniority += $aRow['seniority'];
            $footer_salary_off += $aRow['salary_off'];
            $footer_salary_income += $aRow['salary_income'];
            $footer_allowance_responsibility += $aRow['allowance_responsibility'];
            $footer_allowance_other += $aRow['allowance_other'];
            $footer_allowance_business_fee += $aRow['allowance_business_fee'];
            $footer_allowance_rice += $aRow['allowance_rice'];
            $footer_allowance_rice_tc += $aRow['allowance_rice_tc'];
            $footer_allowance_rice_money += $aRow['allowance_rice_money'];
            $footer_bonus_holiday += $aRow['bonus_holiday'];
            $footer_deduct_bhxh += $aRow['deduct_bhxh'];
            $footer_deduct_bhyt += $aRow['deduct_bhyt'];
            $footer_deduct_bhtn += $aRow['deduct_bhtn'];
            $footer_deduct_union += $aRow['deduct_union'];
            $footer_total_allowance_other += $aRow['total_allowance_other'];
            $footer_total_reduce_other += $aRow['total_reduce_other'];
            $footer_deduct_advance += $aRow['deduct_advance'];
            $footer_total += $aRow['total'];
            $footer_total_real += $aRow['total_real'];
            $footer_salary_compensation += $aRow['salary_compensation'];
            $footer_tax_exemption += $aRow['tax_exemption'];
            $footer_complete_permission += $aRow['complete_permission'];
            $footer_taxable_income += $aRow['taxable_income'];
            $footer_total_reduce_bhxh += $aRow['total_reduce_bhxh'];
            $footer_allowance_family += $aRow['allowance_family'];
            $footer_income_taxes += $aRow['income_taxes'];

            $footer_grand_total_kt += $aRow['grand_total_kt'];
            $footer_grand_total_kl += $aRow['grand_total_kl'];

            $output['aaData'][] = $row;
            $stt++;

        }
        $output['footer_salary'] = $footer_salary;
        $output['footer_salary_bhxh'] = $footer_salary_bhxh;
        $output['footer_salary_responsibility'] = $footer_salary_responsibility;
        $output['footer_salary_position'] = $footer_salary_position;
        $output['footer_responsibility_salary'] = $footer_responsibility_salary;
        $output['footer_sales'] = $footer_sales;
        $output['footer_gasonline_cars'] = $footer_gasonline_cars;
        $output['footer_phone'] = $footer_phone;
        $output['footer_motel'] = $footer_motel;
        $output['footer_concurrently'] = $footer_concurrently;
        $output['footer_business_fee_staff'] = $footer_business_fee_staff;
        $output['footer_seniority'] = $footer_seniority;
        $output['footer_tong_phu_cap'] = $footer_tong_phu_cap;
        $output['footer_total_vat'] = $footer_total_vat;
        $output['footer_business_fee_difference'] = $footer_business_fee_difference;
        $output['footer_allowance'] = $footer_allowance;
        $output['footer_total_salary_new'] = $footer_total_salary_new;
        $output['footer_day_number'] = $footer_day_number;
        $output['footer_day_number_new'] = $footer_day_number_new;
        $output['footer_day_number_holiday'] = $footer_day_number_holiday;
        $output['footer_day_number_lt'] = $footer_day_number_lt;
        $output['footer_day_number_ch'] = $footer_day_number_ch;
        $output['footer_total_number_day_kp'] = $footer_day_number_kp;
        $output['footer_total_number_day_od'] = $footer_day_number_od;
        $output['footer_total_number_day'] = $footer_total_number_day;
        $output['footer_day_number_off'] = $footer_day_number_off;
        $output['footer_money_hour_late'] = $footer_money_hour_late;
        $output['footer_hour_late'] = $footer_hour_late;
        $output['footer_day_number_off_new'] = $footer_day_number_off_new;
        $output['footer_salary_off'] = $footer_salary_off;
        $output['footer_salary_income'] = $footer_salary_income;
        $output['footer_allowance_responsibility'] = $footer_allowance_responsibility;
        $output['footer_allowance_other'] = $footer_allowance_other;
        $output['footer_allowance_manu'] = $footer_allowance_manu;
        $output['footer_allowance_western'] = $footer_allowance_western;
        $output['footer_allowance_business_fee'] = $footer_allowance_business_fee;
        $output['footer_allowance_rice'] = $footer_allowance_rice;
        $output['footer_allowance_rice_tc'] = $footer_allowance_rice_tc;
        $output['footer_allowance_rice_money'] = $footer_allowance_rice_money;
        $output['footer_bonus_holiday'] = $footer_bonus_holiday;
        $output['footer_deduct_bhxh'] = $footer_deduct_bhxh;
        $output['footer_deduct_bhyt'] = $footer_deduct_bhyt;
        $output['footer_deduct_bhtn'] = $footer_deduct_bhtn;
        $output['footer_deduct_union'] = $footer_deduct_union;
        $output['footer_salary_compensation'] = $footer_salary_compensation;
        $output['footer_tax_exemption'] = $footer_tax_exemption;
        $output['footer_complete_permission'] = $footer_complete_permission;
        $output['footer_taxable_income'] = $footer_taxable_income;
        $output['footer_total_allowance_other'] = $footer_total_allowance_other;
        $output['footer_total_reduce_other'] = $footer_total_reduce_other;
        $output['footer_total_reduce_bhxh'] = $footer_total_reduce_bhxh;
        $output['footer_deduct_advance'] = $footer_deduct_advance;
        $output['footer_grand_total_kt'] = $footer_grand_total_kt;
        $output['footer_grand_total_kl'] = $footer_grand_total_kl;
        $output['footer_total'] = $footer_total;
        $output['footer_total_real'] = $footer_total_real;
        $output['footer_allowance_family'] = $footer_allowance_family;
        $output['footer_income_taxes'] = $footer_income_taxes;
        $output['arrFooter'] = $arrFooter;
        echo json_encode($output);
    }

    public function add_payroll_salary_audit()
    {
        if (!$this->perAddPayrollSalary) {
            accessDenied();
        }
        $dtAllowance = get_table_where('tbl_allowance_reduce', ['type' => 1]);
        $dtReduce = get_table_where('tbl_allowance_reduce', ['type' => 2]);
        $rice_money = get_option('rice_money_audit');
        if ($this->input->post('save')) {
            $data = [];
            $this->form_validation->set_rules('month', lang("month"), 'required');
            $this->form_validation->set_rules('year', lang("year"), 'required');
            if ($this->form_validation->run() == true) {
//                print_arrays($this->input->post());
                $month = $this->input->post('month');
                $year = $this->input->post('year');
                $counter = $this->input->post('counter');
                $this->db->select('*');
                $this->db->from('tbl_timekeeping');
                $this->db->where('tbl_timekeeping.month', $month);
                $this->db->where('tbl_timekeeping.year', $year);
                $timekeeping = $this->db->get()->row_array();
                if (empty($timekeeping)) {
                    $data['result'] = 1;
                    $data['message'] = 'Vui lòng chấm công tháng '.$month.' năm '.$year;
                    echo json_encode($data);
                    die;
                }
                $arrPayrollItem = [];
                if (!empty($counter)) {
                    $salary_3p_id_post = $this->input->post('salary_3p_id');
                    $weight_p2_post = $this->input->post('weight_p2');
                    $weight_p3_post = $this->input->post('weight_p3');
                    $salary_bhxh_post = $this->input->post('salary_bhxh');
                    $salary_bhxh_new_post = $this->input->post('salary_bhxh_new');
                    $allowance_responsibility_post = $this->input->post('allowance_responsibility');
                    $allowance_other_post = $this->input->post('allowance_other');
                    $allowance_manu_post = $this->input->post('allowance_manu');
                    $allowance_western_post = $this->input->post('allowance_western');
                    $allowance_rice_post = $this->input->post('allowance_rice');
                    $allowance_rice_tc_post = $this->input->post('allowance_rice_tc');
                    $bonus_holiday_post = $this->input->post('bonus_holiday');
                    $day_number_post = $this->input->post('day_number');
                    $staff_id_post = $this->input->post('staff_id');
                    $total_date_post = $this->input->post('total_date');
                    $number_day_bhxh_post = $this->input->post('number_day_bhxh');
                    $total_number_day_holiday_post = $this->input->post('total_number_day_holiday');
                    $total_number_day_lt_post = $this->input->post('total_number_day_lt');
                    $total_number_day_ch_post = $this->input->post('total_number_day_ch');
                    $salary_responsibility_post = $this->input->post('salary_responsibility');
                    $salary_position_post = $this->input->post('salary_position');
                    $responsibility_salary_post = $this->input->post('responsibility_salary');
                    $day_number_off_post = $this->input->post('day_number_off');
                    $hour_late_post = $this->input->post('hour_late');
                    $business_fee_difference_post = $this->input->post('business_fee_difference');
                    $complete_permission_post = $this->input->post('complete_permission');
                    $sales_post = $this->input->post('sales');
                    $phone_post = $this->input->post('phone');
                    $gasonline_cars_post = $this->input->post('gasonline_cars');
                    $motel_post = $this->input->post('motel');
                    $concurrently_post = $this->input->post('concurrently');
                    $business_fee_staff_post = $this->input->post('business_fee_staff');
                    $seniority_post = $this->input->post('seniority');
                    $number_reduce_post = $this->input->post('number_reduce');
                    $allowance_diff_post = $this->input->post('allowance_diff');
                    $allowance_family_post = $this->input->post('allowance_family');
                    $total_number_day_kp_new_post = $this->input->post('total_number_day_kp_new');
                    $total_number_day_od_post = $this->input->post('total_number_day_od');
                    $total_weekday_post = $this->input->post('total_weekday');
                    $total_sunday_post = $this->input->post('total_sunday');
                    $total_holiday_post = $this->input->post('total_holiday');
                    $total_weekday_night_post = $this->input->post('total_weekday_night');
                    $total_sunday_night_post = $this->input->post('total_sunday_night');
                    $allowance_business_fee_post = $this->input->post('allowance_business_fee');
                    $grand_total_kt_post = $this->input->post('grand_total_kt');
                    $grand_total_kl_post = $this->input->post('grand_total_kl');
                    $arrAll = [];
                    $arrRedu = [];
                    if (!empty($dtAllowance)) {
                        foreach ($dtAllowance as $kk => $vv) {
                            $arrAll['allowance_other_'.$vv['id']] = $this->input->post('allowance_other_'.$vv['id']);
                        }
                    }
                    if (!empty($dtReduce)) {
                        foreach ($dtReduce as $kk => $vv) {
                            $arrRedu['reduce_other_'.$vv['id']] = $this->input->post('reduce_other_'.$vv['id']);
                        }
                    }
                    $data_json_payment_post = $this->input->post('data_json_payment');
                    foreach ($counter as $key => $value) {
                        $allowance_responsibility = number_unformat($allowance_responsibility_post[$key]);
                        $salary_bhxh = number_unformat($salary_bhxh_post[$key]);
                        $salary_bhxh_new = number_unformat($salary_bhxh_new_post[$key]);
                        $allowance_other = number_unformat($allowance_other_post[$key]);
                        $allowance_manu = number_unformat($allowance_manu_post[$key]);
                        $allowance_western = number_unformat($allowance_western_post[$key]);
                        $allowance_rice = number_unformat($allowance_rice_post[$key]);
                        $allowance_rice_tc = number_unformat($allowance_rice_tc_post[$key]);
                        $bonus_holiday = number_unformat($bonus_holiday_post[$key]);
                        $day_number = number_unformat($day_number_post[$key]);
                        $staff_id = number_unformat($staff_id_post[$key]);
                        $totalDate = number_unformat($total_date_post[$key]);
                        $number_day_bhxh = number_unformat($number_day_bhxh_post[$key]);
                        $total_number_day_holiday = number_unformat($total_number_day_holiday_post[$key]);
                        $total_number_day_lt = number_unformat($total_number_day_lt_post[$key]);
                        $salary_responsibility = number_unformat($salary_responsibility_post[$key]);
                        $salary_position = number_unformat($salary_position_post[$key]);
                        $total_number_day_ch = number_unformat($total_number_day_ch_post[$key]);
                        $day_number_off = number_unformat($day_number_off_post[$key]);
                        $hour_late = number_unformat($hour_late_post[$key]);
                        $business_fee_difference = number_unformat($business_fee_difference_post[$key]);
                        $sales = number_unformat($sales_post[$key]);
                        $gasonline_cars = number_unformat($gasonline_cars_post[$key]);
                        $phone = number_unformat($phone_post[$key]);
                        $motel = number_unformat($motel_post[$key]);
                        $seniority = number_unformat($seniority_post[$key]);
                        $concurrently = number_unformat($concurrently_post[$key]);
                        $business_fee_staff = number_unformat($business_fee_staff_post[$key]);
                        $number_reduce = number_unformat($number_reduce_post[$key]);
                        $allowance_diff = number_unformat($allowance_diff_post[$key]);
                        $complete_permission = number_unformat($complete_permission_post[$key]);
                        $allowance_family = number_unformat($allowance_family_post[$key]);
                        $total_number_day_kp = number_unformat($total_number_day_kp_new_post[$key]);
                        $total_number_day_od = number_unformat($total_number_day_od_post[$key]);
                        $total_weekday = number_unformat($total_weekday_post[$key]);
                        $total_sunday = number_unformat($total_sunday_post[$key]);
                        $total_holiday = number_unformat($total_holiday_post[$key]);
                        $allowance_business_fee = number_unformat($allowance_business_fee_post[$key]);
                        $total_weekday_night = number_unformat($total_weekday_night_post[$key]);
                        $total_sunday_night = number_unformat($total_sunday_night_post[$key]);
                        $grand_total_kt = number_unformat($grand_total_kt_post[$key]);
                        $grand_total_kl = number_unformat($grand_total_kl_post[$key]);

                        $salary_3p_id = ($salary_3p_id_post[$key]) ?? 0;
                        $weight_p2 = ($weight_p2_post[$key]) ?? 0;
                        $weight_p3 = ($weight_p3_post[$key]) ?? 0;


                        $money_hour_late =  (($salary_bhxh + $salary_responsibility + $salary_position + $sales) / $totalDate / HOUR_DAY) * $hour_late;

                        $day_number_off_new = $day_number_off / HOUR_DAY;
                        $salary_compensation = (($business_fee_staff + $phone + $gasonline_cars + $motel) / $totalDate / HOUR_DAY) * $day_number_off;

                        $personnel = get_table_where('tblstaff', ['staffid' => $staff_id], '', 'row_array');

                        $salary_income_day = ($salary_bhxh + $salary_responsibility + $salary_position + $sales + $gasonline_cars + $phone + $motel + $concurrently + $business_fee_staff + $seniority) / $totalDate / HOUR_DAY;
                        $salary_income = $day_number * $salary_income_day;

                        $salary_income = $salary_income;

                        $check_bhxh = $personnel['check_bhxh'];
                        $check_union = $personnel['check_union'];
                        $deduct_bhxh = 0;
                        $deduct_bhyt = 0;
                        $deduct_bhtn = 0;
                        $deduct_union = 0;
                        if ($number_day_bhxh >= 14) {
                            if ($check_bhxh == 1) {
                                $deduct_bhxh = ($salary_bhxh_new * DEDUCT_BHXH) / 100;
                                $deduct_bhyt = ($salary_bhxh_new * DEDUCT_BHYT) / 100;
                                $deduct_bhtn = ($salary_bhxh_new * DEDUCT_BHTN) / 100;
                            } else {
                                $deduct_bhxh = 0;
                                $deduct_bhyt = 0;
                                $deduct_bhtn = 0;
                            }
                        }

                        if ($check_union == 1) {
                            $deduct_union = $salary_bhxh_new * (1 / 100);
                        } else {
                            $deduct_union = 0;
                        }

                        $day_number_new = ($day_number / HOUR_DAY) - ($total_number_day_holiday + $total_number_day_lt);

                        $total_allowance_other = 0;
                        $total_reduce_other = 0;

                        $arrAllowance = [];
                        if (!empty($dtAllowance)) {
                            foreach ($dtAllowance as $kk => $vv) {
                                if (isset($arrAll['allowance_other_'.$vv['id']][$value.'_'.$staff_id])) {
                                    $allowance_other_new = number_unformat($arrAll['allowance_other_'.$vv['id']][$value.'_'.$staff_id]);
                                    $total_allowance_other += $allowance_other_new;
                                    $arrAllowance[] = [
                                        'category_id' => $vv['id'],
                                        'staff_id' => $staff_id,
                                        'amount' => $allowance_other_new,
                                        'type' => 1,
                                    ];
                                }
                            }
                        }
                        $arrReduce = [];
                        if (!empty($dtReduce)) {
                            foreach ($dtReduce as $kk => $vv) {
                                if (isset($arrRedu['reduce_other_'.$vv['id']][$value.'_'.$staff_id])) {
                                    $allowance_reduce = number_unformat($arrRedu['reduce_other_'.$vv['id']][$value.'_'.$staff_id]);
                                    $total_reduce_other += $allowance_reduce;
                                    $arrReduce[] = [
                                        'category_id' => $vv['id'],
                                        'staff_id' => $staff_id,
                                        'amount' => $allowance_reduce,
                                        'type' => 2,
                                    ];
                                }
                            }
                        }

                        if (isset($data_json_payment_post[$value])) {
                            $payrollPayment = $data_json_payment_post[$value];
                        } else {
                            $payrollPayment = null;
                        }
                        $payrollPaymentJson = json_decode($payrollPayment);
                        $total_payment = 0;
                        $arr_payment = [];
                        if (!empty($payrollPaymentJson)) {
                            foreach ($payrollPaymentJson as $k => $v) {
                                $total_payment += $v->total_sub;
                                $arr_payment [] = [
                                    'id' => $v->payrollPayment,
                                    'total_sub' => $v->total_sub,
                                ];
                            }
                        }
                        $deduct_advance = $total_payment;

                        $allowance_rice_money = ($allowance_rice * $rice_money);

                        $allowance_rice_money_tc = $allowance_rice_tc;

                        $this->db->select('tbl_business_fee_boiler_calculate_item.total as total,tbl_business_fee_boiler_calculate_item.id as id');
                        $this->db->from('tbl_business_fee_boiler_calculate');
                        $this->db->join('tbl_business_fee_boiler_calculate_item',
                            'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id');
                        $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
                        $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
                        $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staff_id);;
                        $allowance_business_fee_db = $this->db->get()->row_array();
//                        $allowance_business_fee = $allowance_business_fee_db['total'];
//                        $business_fee_boiler_calculate_item_id = $allowance_business_fee_db['id'];

                        $money_vat = get_option('money_vat');
                        $money_reduce = get_option('money_reduce');
                        $rice_money_max = get_option('rice_money_max');

                        if (($allowance_rice_money + $allowance_rice_money_tc) > $rice_money_max){
                            $rice_money_max_new = $rice_money_max;
                        } else {
                            $rice_money_max_new = $allowance_rice_money + $allowance_rice_money_tc;
                        }

                        $tax_exemption = $business_fee_difference + $rice_money_max_new;


                        $salary_income_vat = ($grand_total_kt - $grand_total_kl + $salary_income + $business_fee_difference + $allowance_diff + $allowance_rice_money + $allowance_rice_money_tc + $salary_compensation + $complete_permission) - $money_hour_late - $tax_exemption - ($deduct_bhxh + $deduct_bhyt + $deduct_bhtn) - $total_reduce_other - $money_vat - ($number_reduce * $money_reduce) - ((($business_fee_staff + $phone + $gasonline_cars) / $totalDate / HOUR_DAY) * $day_number);
                        $income_taxes = ($grand_total_kt - $grand_total_kl + $salary_income + $business_fee_difference + $allowance_diff + $allowance_rice_money + $allowance_rice_money_tc + $salary_compensation + $complete_permission) - $money_hour_late - $tax_exemption - ($deduct_bhxh + $deduct_bhyt + $deduct_bhtn + $deduct_union) - $total_reduce_other - ((($business_fee_staff + $phone + $gasonline_cars) / $totalDate / HOUR_DAY) * $day_number);

                        if ($salary_income_vat < 0){
                            $salary_income_vat = 0;
                        }
                        $taxable_income = $salary_income_vat;
                        $total_money_vat_check = $salary_income_vat;
                        if ($total_money_vat_check < 0) {
                            $total_money_vat_check = 0;
                        }

                        $percent_vat = 0;
                        $total_reduce_vat = 0;
                        if ($total_money_vat_check <= 5000000) {
                            $percent_vat = 5;
                            $total_reduce_vat = 0;
                        } elseif ($total_money_vat_check > 5000000 && $total_money_vat_check <= 10000000) {
                            $percent_vat = 10;
                            $total_reduce_vat = 250000;
                        } elseif ($total_money_vat_check > 10000000 && $total_money_vat_check <= 18000000) {
                            $percent_vat = 15;
                            $total_reduce_vat = 750000;
                        } elseif ($total_money_vat_check > 18000000 && $total_money_vat_check <= 32000000) {
                            $percent_vat = 20;
                            $total_reduce_vat = 1650000;
                        } elseif ($total_money_vat_check > 32000000 && $total_money_vat_check <= 52000000) {
                            $percent_vat = 25;
                            $total_reduce_vat = 3250000;
                        } elseif ($total_money_vat_check > 52000000 && $total_money_vat_check <= 80000000) {
                            $percent_vat = 30;
                            $total_reduce_vat = 5850000;
                        } elseif ($total_money_vat_check > 80000000) {
                            $percent_vat = 35;
                            $total_reduce_vat = 9850000;
                        }

                        $total_vat = 0;
                        $total_vat = ($total_money_vat_check * $percent_vat / 100) - $total_reduce_vat;

                        $total = $grand_total_kt - $grand_total_kl + $salary_income + $allowance_business_fee + $total_allowance_other + $allowance_rice_money + $allowance_rice_money_tc + $complete_permission + $salary_compensation - $money_hour_late - $deduct_bhxh - $deduct_bhyt - $deduct_bhtn - $deduct_union - $total_reduce_other - $deduct_advance - $total_vat;

                        $total_allowance_other += $allowance_rice_money;
                        $total_allowance_other += $allowance_rice_money_tc;
                        $total_reduce_other += $deduct_advance;
                        $total_reduce_bhxh = $deduct_bhxh + $deduct_bhyt + $deduct_bhtn + $deduct_union;

                        $total_new = $salary_income + $allowance_business_fee + $total_allowance_other + $salary_compensation;

                        $full_name = vn_to_str($personnel['firstname'].' '.$personnel['lastname']);
                        $code_name = $personnel['code'];
                        $code = 'BL_'.$code_name.'_'.$full_name.'_'.$month.$year;
                        $arrPayrollItem[] = [
                            'code' => $code,
                            'staff_id' => $staff_id,
                            'day_number' => $day_number,
                            'day_number_new' => $day_number_new,
                            'day_holiday' => $total_number_day_holiday,
                            'day_lt' => $total_number_day_lt,
                            'day_ch' => $total_number_day_ch,
                            'salary' => ($salary_bhxh + $salary_responsibility + $salary_position + $sales + $gasonline_cars + $phone + $motel + $concurrently + $business_fee_staff + $seniority),
                            'salary_bhxh' => $salary_bhxh,
                            'salary_bhxh_new' => $salary_bhxh_new,
                            'salary_responsibility' => $salary_responsibility,
                            'salary_position' => $salary_position,
                            'responsibility_salary' => 0,
                            'sales' => $sales,
                            'gasonline_cars' => $gasonline_cars,
                            'phone' => $phone,
                            'motel' => $motel,
                            'concurrently' => $concurrently,
                            'seniority' => $seniority,
                            'business_fee_staff' => $business_fee_staff,
                            'number_reduce' => $number_reduce,
                            'business_fee_difference' => $business_fee_difference,
                            'allowance_diff' => $allowance_diff,
                            'complete_permission' => $complete_permission,
                            'salary_compensation' => $salary_compensation,
                            'tax_exemption' => $tax_exemption,
                            'taxable_income' => $taxable_income,
                            'total_vat' => $total_vat,
                            'day_number_off' => $day_number_off,
                            'day_number_off_new' => $day_number_off_new,
                            'hour_late' => $hour_late,
                            'salary_off' => 0,
                            'allowance' => $personnel['allowance'],
                            'salary_income' => $salary_income,
                            'money_hour_late' => $money_hour_late,
                            'allowance_responsibility' => $allowance_responsibility,
                            'allowance_other' => $allowance_other,
                            'allowance_manu' => $allowance_manu,
                            'allowance_western' => $allowance_western,
                            'allowance_business_fee' => !empty($allowance_business_fee) ? $allowance_business_fee : 0,
                            'allowance_rice' => $allowance_rice,
                            'allowance_rice_tc' => $allowance_rice_tc,
                            'allowance_rice_money' => $allowance_rice_money,
                            'bonus_holiday' => $bonus_holiday,
                            'deduct_bhxh' => $deduct_bhxh,
                            'deduct_bhyt' => $deduct_bhyt,
                            'deduct_bhtn' => $deduct_bhtn,
                            'deduct_union' => $deduct_union,
                            'deduct_advance' => $deduct_advance,
                            'total_reduce_other' => $total_reduce_other,
                            'total_reduce_bhxh' => $total_reduce_bhxh,
                            'allowance_family' => $allowance_family,
                            'income_taxes' => $income_taxes,
                            'total_weekday' => $total_weekday,
                            'total_sunday' => $total_sunday,
                            'total_holiday' => $total_holiday,
                            'total_weekday_night' => $total_weekday_night,
                            'total_sunday_night' => $total_sunday_night,
                            'total_allowance_other' => $total_allowance_other,
                            'total_number_day_kp' => $total_number_day_kp,
                            'total_number_day_od' => $total_number_day_od,
                            'total' => $total_new,
                            'total_real' => $total,
                            'business_fee_boiler_calculate_item_id' => !empty($business_fee_boiler_calculate_item_id) ? $business_fee_boiler_calculate_item_id : 0,
                            'data_json_payment' => $payrollPayment,
                            'arr_payment' => $arr_payment,
                            'arrAllowance' => $arrAllowance,
                            'arrReduce' => $arrReduce,
                            'grand_total_kt' => $grand_total_kt,
                            'grand_total_kl' => $grand_total_kl,
                            'salary_3p_id' => $salary_3p_id,
                            'weight_p2' => $weight_p2,
                            'weight_p3' => $weight_p3,
                        ];
                    }
                }

                if (empty($arrPayrollItem)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có dữ liệu');
                    echo json_encode($data);
                    die;
                }


                $Idpayroll = 0;
                $this->db->select('*');
                $this->db->from('tbl_payroll_audit');
                $this->db->where('tbl_payroll_audit.month', $month);
                $this->db->where('tbl_payroll_audit.year', $year);
                $payroll = $this->db->get()->row_array();
                if (!empty($payroll)) {
                    $Idpayroll = $payroll['id'];
                } else {
                    $this->db->insert('tbl_payroll_audit', [
                        'month' => $month,
                        'year' => $year,
                        'date_created' => date('Y-m-d H:i'),
                        'staff_id' => get_staff_user_id(),
                    ]);
                    $Idpayroll = $this->db->insert_id();
                }
                if ($Idpayroll) {

                    foreach ($arrPayrollItem as $key => $value) {
                        $paymentArr = $value['arr_payment'];
                        $arrAllowance = $value['arrAllowance'];
                        $arrReduce = $value['arrReduce'];
                        unset($value['arr_payment']);
                        unset($value['arrAllowance']);
                        unset($value['arrReduce']);
                        $value['payroll_id'] = $Idpayroll;
                        $this->db->insert('tbl_payroll_item_audit', $value);
                        $payroll_item_id = $this->db->insert_id();
                        if ($payroll_item_id) {
                            if (!empty($paymentArr)) {
                                foreach ($paymentArr as $kk => $vv) {
                                    $this->db->insert('tbl_payroll_payment_item_audit', [
                                        'payroll_item_id' => $payroll_item_id,
                                        'payroll_id' => $Idpayroll,
                                        'payroll_payment_id' => $vv['id'],
                                        'total' => $vv['total_sub'],
                                    ]);
                                }
                            }

                            if (!empty($arrAllowance)) {
                                foreach ($arrAllowance as $kk => $vv) {
                                    $arrAllowance[$kk]['payroll_item_id'] = $payroll_item_id;
                                    $arrAllowance[$kk]['payroll_id'] = $Idpayroll;
                                }
                                $this->db->insert_batch('tbl_allowance_reduce_payroll_audit', $arrAllowance);
                            }

                            if (!empty($arrReduce)) {
                                foreach ($arrReduce as $kk => $vv) {
                                    $arrReduce[$kk]['payroll_item_id'] = $payroll_item_id;
                                    $arrReduce[$kk]['payroll_id'] = $Idpayroll;
                                }
                                $this->db->insert_batch('tbl_allowance_reduce_payroll_audit', $arrReduce);
                            }


                        }
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
            $data['title'] = lang('Tạo tính lương audit');
            $data['breadcrumb'] = [
                array(
                    'link' => base_url('admin/payroll/payroll_salary_audit'),
                    'page' => lang('Bảng lương audit'),
                ),
                array('link' => '#', 'page' => $data['title']),
            ];
            $this->load->view('admin/payroll/payroll_salary_audit', $data);
        }
    }

    public function loadPayrollSalaryAudit()
    {
        $data = [];
        $month = $this->input->get('month');
        $year = $this->input->get('year');
        $branch_search = $this->input->get('branch_search');

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();

        $countAllowance = 3 + count($dtAllowance);
        $countReduce = 1 + count($dtReduce);

        $salary_minimum_new = number_unformat(get_option('salary_minimum_new'));
        $rice_money = get_option('rice_money_audit');

        $tHead = '';
        $html = '';
        $tHead = '<tr>
            <th rowspan="2" class="text-center" style="min-width: 50px;">'.lang('tnh_numbers').'</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">'.lang('Mã NV').'</th>
            <th rowspan="2" class="text-center" style="min-width: 150px;">'.lang('Họ Tên').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Chức vụ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Ngày vào làm').'</th>
            <th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Lương P1').'</th>
            <th colspan="2" class="text-center" style="min-width: 100px;">'.lang('Lương năng lực').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Lương đóng BHXH').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Kiêm nhiệm').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Doanh số').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Công tác phí').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Điện thoại').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Xăng đi lại').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Nhà trọ').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng phụ cấp').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Thâm niên').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số giờ công').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Số ngày công').'</th>';
        $tHead .= '<th colspan="3" class="text-center" style="min-width: 100px;">'.lang('Ngày nghỉ có lương').'</th>';
        $tHead .= '<th colspan="2" class="text-center" style="min-width: 100px;">'.lang('Nghỉ không lương').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Tổng ngày công tính lương').'</th>';
        $tHead .= '<th rowspan="2" class="text-center" style="min-width: 100px;">'.lang('Thu nhập').'</th>
            <th colspan="5" class="text-center" style="min-width: 80px;">'.lang('Số tiếng tăng ca').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng tiền tăng ca').'</th>
            <th colspan="'.$countAllowance.'" class="text-center" style="min-width: 80px;">'.lang('Các khoản phải trả').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Bù lương').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng các khoản phải trả').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng thu nhập').'</th>
            <th colspan="'.$countReduce.'" class="text-center" style="min-width: 80px;">'.lang('Các khoản trừ').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng các khoản trừ').'</th>
            <th colspan="4" class="text-center" style="min-width: 80px;">'.lang('Khoản trừ BHXH').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng khấu trừ BHXH').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Khen thưởng KPIs').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Kỹ luật KPIs').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Giảm trừ gia cảnh').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Lương ngoài giờ miễn thuế').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Khác khoản miễn thuế').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Hoàn phép năm').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Thu nhập chịu thuế').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Thu nhập tính thuế').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Thuế TNCN').'</th>
            <th rowspan="2" class="text-center" style="min-width: 80px;">'.lang('Tổng thực lãnh').'</th>
        </tr>';
        $tHead .= '<tr>
            <th class="text-center" style="min-width: 80px;">'.lang('Lương P2').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lương P3').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Phép năm').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('VR hưởng lương (hiếu hỉ)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Nghỉ việc riêng').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Ốm đau').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Ngày thường(1.5)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Chủ nhật(2.0)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết(3.0)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết('.get_option('coefficient_default_night').')').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Lễ tết('.get_option('coefficient_sunday_night').')').'</th>
            ';
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $tHead .= '<th class="text-center" style="min-width: 80px;">'.$value['name'].'</th>';
            }
        }
        $tHead .= '<th class="text-center" style="min-width: 80px;">'.lang('Ngày cơm hành chánh').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tiền ăn hành chính').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('Tiền ăn tăng ca').'</th>';
        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $tHead .= '<th class="text-center" style="min-width: 80px;">'.$value['name'].'</th>';
            }
        }
        $tHead .= '<th class="text-center" style="min-width: 80px;">'.lang('Khấu trừ khác(tạm ứng)').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('8% BHXH').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1.5% BHYT').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1% BHTN').'</th>
            <th class="text-center" style="min-width: 80px;">'.lang('1% Đoàn phí').'</th>
        </tr>';

        $isPayroll = "(
            SELECT COUNT(*)
            FROM tbl_payroll_audit
            LEFT JOIN tbl_payroll_item_audit on tbl_payroll_item_audit.payroll_id = tbl_payroll_audit.id
            WHERE tbl_payroll_audit.month = '$month' AND tbl_payroll_audit.year = '$year' AND tblstaff.staffid = tbl_payroll_item_audit.staff_id
        )";

        $timekeepingId = 0;
        $this->db->select('*');
        $this->db->from('tbl_timekeeping');
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $timekeeping = $this->db->get()->row_array();
        if (!empty($timekeeping)) {
            $timekeepingId = $timekeeping['id'];
        }

        $countPaidHolidayLT = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE (type = 'TDL' OR type = 'TAL' OR type = 'NCT' OR type = 'QTLĐ' OR type = 'QK' OR type = 'GTHV') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_lt";

        $countPaidHolidayCH = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'CH') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_ch";

        $countPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                SUM(IF((type = 'TDL' OR type = 'TAL' OR type = 'NCT' OR type = 'QTLĐ' OR type = 'QK' OR type = 'GTHV'),1,COALESCE((tbl_paid_holiday_leave_detail.number_date),0))) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            LEFT JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'AL' OR type = 'TDL' OR type = 'TAL' OR type = 'NCT' OR type = 'QTLĐ' OR type = 'QK' OR type = 'GTHV' OR type = 'CH') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday";

        $countPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'AL/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_paid_holiday_new";

        $countNotPaidHoliday = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'UP' OR type = 'TS' OR type = 'OD' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday";

        $countNotPaidHolidayOD = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'OD' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_od";

        $countNotPaidHolidayKP = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'KP') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_kp";

        $countNotPaidHolidayUP = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(tbl_paid_holiday_leave_detail.number_date),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            INNER JOIN tbl_paid_holiday_leave_detail ON tbl_paid_holiday_leave_detail.id = tbl_timekeeping_detail.paid_holiday_detail_id
            WHERE (type = 'UP') AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_up";

        $countNotPaidHolidayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count_hour
            FROM tbl_timekeeping_detail
            WHERE type = 'UP/2' AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_not_paid_holiday_new";

        $countNumberDay = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type != 'X' OR (type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day";

        $countNumberDayNew = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(number_day),0) as count
            FROM tbl_timekeeping_detail
            WHERE (type = 'X' AND number_day = '0.5' ) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_number_day_new";

        $countHour = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(SUM(count_hour_late_new + count_hour_late_checkout),0) as count_hour_late,
                COALESCE(SUM(count_hour - count_hour_overtime),0) as count
            FROM tbl_timekeeping_detail
            WHERE ((type = 'X' AND number_day > 0 )) AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_hour";

        $countHourBhxh = "(
            SELECT 
                tbl_timekeeping_detail.timekeeping_id as timekeeping_id,
                tbl_timekeeping_detail.staff_id as staff_id,
                COALESCE(COUNT(id),0) as count
            FROM tbl_timekeeping_detail
            WHERE (count_hour - count_hour_overtime) >= 4 AND check_sun = 0 AND timekeeping_id = $timekeepingId
            GROUP BY timekeeping_id,staff_id
        ) tb_count_hour_bhxh";

        $GetTotalSuggestBonuskt = "(
            SELECT 
                tbl_decision_bonus_discipline.object_id as staff_id,
                SUM(grand_total) as grand_total
            FROM tbl_decision_bonus_discipline
            INNER JOIN tblinternal_proposal ON tblinternal_proposal.decision_bonus_discipline_id = tbl_decision_bonus_discipline.id
            INNER JOIN tbl_internal_proposal_process ON tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 1
            WHERE tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 1 AND object_type = 'staff' AND tbl_decision_bonus_discipline.status = 1 AND MONTH(tbl_decision_bonus_discipline.date) = $month AND YEAR(tbl_decision_bonus_discipline.date) = $year
            GROUP BY tbl_decision_bonus_discipline.object_id,object_type
        ) GetTotalSuggestBonuskt";

        $GetTotalSuggestBonuskl = "(
            SELECT 
                tbl_decision_bonus_discipline.object_id as staff_id,
                SUM(grand_total) as grand_total
            FROM tbl_decision_bonus_discipline
            INNER JOIN tblinternal_proposal ON tblinternal_proposal.decision_bonus_discipline_id = tbl_decision_bonus_discipline.id
            INNER JOIN tbl_internal_proposal_process ON tbl_internal_proposal_process.id_internal_proposal = tblinternal_proposal.id AND tbl_internal_proposal_process.bod = 1 AND tbl_internal_proposal_process.status = 1
            WHERE tbl_decision_bonus_discipline.type_quota_bonus_discipline_id = 2 AND object_type = 'staff' AND tbl_decision_bonus_discipline.status = 1 AND MONTH(tbl_decision_bonus_discipline.date) = $month AND YEAR(tbl_decision_bonus_discipline.date) = $year
            GROUP BY tbl_decision_bonus_discipline.object_id,object_type
        ) GetTotalSuggestBonuskl";


        $tb_tamp_report = "(
             SELECT
                tblproduction_report.staff_responsible as staff_id,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 1 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p1,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 2 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p2,
                SUM(CASE WHEN tbl_kpi_list_criteria_department.type_p = 3 THEN tbl_kpi_list_criteria_department.weight ELSE 0 END) AS weight_p3
            FROM tblproduction_report
            LEFT JOIN tbl_kpi_list_criteria_department
            ON tbl_kpi_list_criteria_department.id =
               COALESCE(
                   NULLIF(tblproduction_report.kpi_list_criteria_department_id_childd, 0),
                   tblproduction_report.kpi_list_criteria_department_id_child
               )
            WHERE tblproduction_report.id != 0 AND MONTH(tblproduction_report.date) = $month AND YEAR(tblproduction_report.date) = $year
            GROUP BY staff_responsible
        ) tb_tamp_report";

        $this->db->select("
            tblstaff.staffid as staffid,
            CONCAT(TRIM(tblstaff.firstname),' ',TRIM(tblstaff.lastname)) as name,
            tblstaff.code as code,
            tblstaff.day_in as day_in,
            tblstaff.salary_bhxh as salary_bhxh,
            tblstaff.salary_bhxh_new as salary_bhxh_new,
            tblstaff.allowance as allowance,
            tblstaff.check_bhxh as check_bhxh,
            tblstaff.check_union as check_union,
            tblstaff.coefficient_responsibility as coefficient_responsibility,
            tblstaff.coefficient_position as coefficient_position,
            tblstaff.responsibility_salary as responsibility_salary,
            tblstaff.sales as sales,
            tblstaff.phone as phone,
            tblstaff.gasonline_cars as gasonline_cars,
            tblstaff.motel as motel,
            tblstaff.concurrently as concurrently,
            tblstaff.business_fee_staff as business_fee_staff,
            tblstaff.seniority as seniority,
            tblstaff.number_reduce as number_reduce,
            tblroles.name as name_role,
            tblroles.roleid as roleid,
            COALESCE(tb_count_paid_holiday.count,0) + (COALESCE(tb_count_paid_holiday_new.count,0) * 0.5 ) as totalHoliday, 
            COALESCE(tb_count_not_paid_holiday.count,0) + (COALESCE(tb_count_not_paid_holiday_new.count,0) * 0.5 ) as totalNotHoliday, 
            COALESCE(tb_count_number_day.count,0) as number_day, 
            COALESCE(tb_count_number_day_new.count,0) as number_day_new, 
            COALESCE(tb_count_hour.count_hour_late,0) as count_hour_late,
            COALESCE(tb_count_hour.count,0) as count_hour,
            COALESCE(tb_count_paid_holiday_new.count_hour,0) + COALESCE(tb_count_paid_holiday.count_hour,0) as count_hour_phep, 
            COALESCE(tb_count_not_paid_holiday_new.count_hour,0) + COALESCE(tb_count_not_paid_holiday.count_hour,0) as count_hour_kphep, 
            COALESCE(tb_count_not_paid_holiday_kp.count,0) as number_day_kp, 
            COALESCE(tb_count_not_paid_holiday_up.count,0) as number_day_up, 
            (COALESCE(tb_count_paid_holiday_new.count,0) * 0.5 ) as number_day_al_new,
            (COALESCE(tb_count_not_paid_holiday_new.count,0) * 0.5 ) as number_day_up_new,
            COALESCE(tb_count_paid_holiday_lt.count,0) as number_day_lt, 
            COALESCE(tb_count_paid_holiday_ch.count,0) as number_day_ch, 
            COALESCE(tb_count_hour_bhxh.count,0) as number_day_bhxh, 
            COALESCE(tb_count_not_paid_holiday_od.count,0) as number_day_od, 
            COALESCE(GetTotalSuggestBonuskt.grand_total,0) as grand_total_kt, 
            COALESCE(GetTotalSuggestBonuskl.grand_total,0) as grand_total_kl,
            COALESCE(tb_tamp_report.weight_p2,0) as weight_p2, 
            COALESCE(tb_tamp_report.weight_p3,0) as weight_p3, 
            ROUND(DATEDIFF(CURDATE(),tblstaff.day_in) / 365,2) as seniority_staff
            ", false);
        $this->db->from('tblstaff');
        $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'inner');
        $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'inner');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join($tb_tamp_report, 'tb_tamp_report.staff_id = tblstaff.staffid', 'left');
        $this->db->join("$countPaidHoliday",
            'tb_count_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$GetTotalSuggestBonuskt",
            'GetTotalSuggestBonuskt.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$GetTotalSuggestBonuskl",
            'GetTotalSuggestBonuskl.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countPaidHolidayNew",
            'tb_count_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_new.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHoliday",
            'tb_count_not_paid_holiday.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayOD",
            'tb_count_not_paid_holiday_od.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_od.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayNew",
            'tb_count_not_paid_holiday_new.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_new.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayKP",
            'tb_count_not_paid_holiday_kp.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_kp.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNotPaidHolidayUP",
            'tb_count_not_paid_holiday_up.timekeeping_id = tbl_timekeeping.id AND tb_count_not_paid_holiday_up.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNumberDay",
            'tb_count_number_day.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countNumberDayNew",
            'tb_count_number_day_new.timekeeping_id = tbl_timekeeping.id AND tb_count_number_day_new.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countHour",
            'tb_count_hour.timekeeping_id = tbl_timekeeping.id AND tb_count_hour.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countPaidHolidayLT",
            'tb_count_paid_holiday_lt.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_lt.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countPaidHolidayCH",
            'tb_count_paid_holiday_ch.timekeeping_id = tbl_timekeeping.id AND tb_count_paid_holiday_ch.staff_id = tblstaff.staffid',
            'left');
        $this->db->join("$countHourBhxh",
            'tb_count_hour_bhxh.timekeeping_id = tbl_timekeeping.id AND tb_count_hour_bhxh.staff_id = tblstaff.staffid',
            'left');
        $this->db->where('(tblstaff.check_salary = 0 AND tblstaff.status_work != 2)');
        $this->db->where('tblstaff.branch_salary', $branch_search);
        $this->db->where('tbl_timekeeping.month', $month);
        $this->db->where('tbl_timekeeping.year', $year);
        $this->db->where("($isPayroll = 0)");
        $this->db->group_by('tbl_timekeeping_detail.staff_id');
        $personnel = $this->db->get()->result_array();

        $this->db->from('tbl_timekeeping_detail');
        $this->db->where('tbl_timekeeping_detail.timekeeping_id', $timekeepingId);
        $this->db->where('tbl_timekeeping_detail.check_sun', 0);
        $this->db->group_by('tbl_timekeeping_detail.day');
        $totalDate = $this->db->count_all_results();

        $totalDate = get_option('day_work');
        $hour_day = get_option('hour_day');

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
                $tdRole = '<div class="td-role">
                    '.$value['name_role'].'
                </div>';
                $tdDate = '<div class="td-date">
                    '.(!empty($value['day_in']) ? _dhau($value['day_in']) : '').'
                </div>';

                $totalMonth = (!empty($value['day_in']) ? getDiffDayMonth(_dhau($value['day_in']),date('d/m/Y')) : 0);

                $this->db->select('tbl_salary_3p.salary_p1,tbl_salary_3p.salary_p2,tbl_salary_3p.salary_p3,tbl_salary_3p.id as salary_3p_id');
                $this->db->from('tbl_salary_3p');
                $this->db->join('tbl_grade','tbl_grade.id = tbl_salary_3p.grade_id');
                $this->db->where('tbl_salary_3p.role_id',$value['roleid']);
                $this->db->where('tbl_grade.seniority_from_month <=',$totalMonth);
                $this->db->where('tbl_grade.seniority_to_month >=',$totalMonth);
                $this->db->where('tbl_salary_3p.status',1);
                $dtSalary3P = $this->db->get()->row_array();

                $salary_3p_id = $dtSalary3P['id'] ?? 0;
                $salary_bhxh = $dtSalary3P['salary_p1'] ?? 0;
                $salary_p2 = $dtSalary3P['salary_p2'] ?? 0;
                $salary_p3 = $dtSalary3P['salary_p3'] ?? 0;
                $weight_p2 = (100 - $value['weight_p2']) >= 0 ? (100 - $value['weight_p2']) : 0;
                $weight_p3 = (100 - $value['weight_p3']) >= 0 ? (100 - $value['weight_p3']) : 0;
                $salary_p2 = ($salary_p2 * $weight_p2) /100;
                $salary_p3 = ($salary_p3 * $weight_p3) /100;

                $number_day_bhxh = number_unformat($value['number_day_bhxh']);
                $count_hour_late = number_unformat($value['count_hour_late']);
                $countHourNew = number_unformat($value['count_hour']);
                $count_hour_phep = number_unformat($value['count_hour_phep']);
                $count_hour_kphep = number_unformat($value['count_hour_kphep']);
                $number_day_kp = number_unformat($value['number_day_kp']);
                $number_day_up = number_unformat($value['number_day_up']);
                $number_day_lt = number_unformat($value['number_day_lt']);
                $number_day_ch = number_unformat($value['number_day_ch']);
                $number_day_od = number_unformat($value['number_day_od']);
                $number_day_al_new = number_unformat($value['number_day_al_new']);
                $number_day_up_new = number_unformat($value['number_day_up_new']);
                $totalHoliday = number_unformat($value['totalHoliday']);
                $totalNotHoliday = number_unformat($value['totalNotHoliday']);
                $number_day_new = number_unformat($value['number_day']) - number_unformat($value['number_day_new']);
                $sales = number_unformat($value['sales']);
                $phone = number_unformat($value['phone']);
                $gasonline_cars = number_unformat($value['gasonline_cars']);
                $motel = number_unformat($value['motel']);
                $concurrently = number_unformat($value['concurrently']);
                $business_fee_staff = number_unformat($value['business_fee_staff']);
                $seniority = number_unformat($value['seniority']);

                $total_number_day = $number_day_new - $totalHoliday - $totalNotHoliday;
                $total_number_day = $total_number_day > 0 ? $total_number_day : 0;

                $countHourNew = $countHourNew + $count_hour_phep + $count_hour_kphep;
                $countHourNew = $countHourNew > 0 ? $countHourNew : 0;

                $total_number_day_salary = $countHourNew + ($totalHoliday * 8);
                if($this->cong_fix == 1) {
                    $total_number_day_salary = ($countHourNew + ($count_hour_late ?? 0)) + ($totalHoliday * 8);
                }
                $total_number_day_salary = $total_number_day_salary > 0 ? $total_number_day_salary : 0;

                $total_number_day_off = ($totalNotHoliday + $number_day_kp) * 8;

                $html .= '<tr>';
                $html .= '<td style="min-width: 50px;height:50px">'.$tdNumber.'</td>';

                $html .= '<td style="min-width: 100px;">'.$tdCode.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdNameStaff.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdRole.'</td>';
                $html .= '<td style="min-width: 100px;">'.$tdDate.'</td>';

                $coefficient_responsibility = $value['coefficient_responsibility'];
                $coefficient_position = $value['coefficient_position'];
                $salary_responsibility = $salary_minimum_new * $coefficient_responsibility;
                $salary_position = $salary_minimum_new * $coefficient_position;

                $salary_responsibility = $salary_p3;
                $salary_position = $salary_p2;

                $salary = ($salary_bhxh + $salary_responsibility + $salary_position);


                if ($salary_bhxh == 0) {
                    $html .= '<td style="min-width: 120px;text-align: right"></td>';
                } else {
                    $html .= '<td style="min-width: 120px;text-align: right">'.formatMoney($salary_bhxh).'</td>';
                }

                $html .= '<td style="min-width: 120px;text-align:right">'.(!empty($salary_position) > 0 ? formatMoney($salary_position) : '').'<i class="fa fa-info-circle" title="'.$weight_p2.' %"></i></td>';
                $html .= '<td style="min-width: 120px;text-align: right">'.(!empty($salary_responsibility) > 0 ? formatMoney($salary_responsibility) : '').'<i class="fa fa-info-circle" title="'.$weight_p3.' %"></i></td>';
                if ($salary == 0) {
                    $html .= '<td style="min-width: 120px;text-align: right"></td>';
                } else {
                    $html .= '<td style="min-width: 120px;text-align: right">'.formatMoney($salary).'</td>';
                }
                $html .= '<td style="min-width: 120px;text-align:center">'.($concurrently > 0 ? formatMoney($concurrently) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($sales > 0 ? formatMoney($sales) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($business_fee_staff > 0 ? formatMoney($business_fee_staff) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($phone > 0 ? formatMoney($phone) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($gasonline_cars > 0 ? formatMoney($gasonline_cars) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($motel > 0 ? formatMoney($motel) : '').'</td>';
                $totalPhuCap = $concurrently + $sales + $business_fee_staff + $phone + $gasonline_cars + $motel;
                $html .= '<td style="min-width: 120px;text-align:center">'.($totalPhuCap > 0 ? formatMoney($totalPhuCap) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center">'.($seniority > 0 ? formatMoney($seniority) : '').'</td>';

                $salary_income_day = ($value['salary_bhxh'] + $salary_responsibility + $salary_position + $sales + $phone + $gasonline_cars + $motel + $concurrently + $business_fee_staff + $seniority) / $totalDate / HOUR_DAY;

                $salary_income = $total_number_day_salary * $salary_income_day;
                $check_bhxh = $value['check_bhxh'];
                $deduct_bhxh = 0;
                $deduct_bhyt = 0;
                $deduct_bhtn = 0;
                if ($number_day_bhxh >= 14) {
                    if ($check_bhxh == 1) {
                        $deduct_bhxh = ($value['salary_bhxh_new'] * DEDUCT_BHXH) / 100;
                        $deduct_bhyt = ($value['salary_bhxh_new'] * DEDUCT_BHYT) / 100;
                        $deduct_bhtn = ($value['salary_bhxh_new'] * DEDUCT_BHTN) / 100;
                    } else {
                        $deduct_bhxh = 0;
                        $deduct_bhyt = 0;
                        $deduct_bhtn = 0;
                    }
                }

                if ($value['check_union'] == 1) {
                    $union_salary = $value['salary_bhxh_new'] * (1 / 100);
                } else {
                    $union_salary = 0;
                }

                $deduct_advance = 0;

                $this->db->select('tbl_business_fee_boiler_calculate_item.total as total,total_weekday,total_sunday,total_holiday,salary,total_weekday_night,total_sunday_night');
                $this->db->from('tbl_business_fee_boiler_calculate');
                $this->db->join('tbl_business_fee_boiler_calculate_item',
                    'tbl_business_fee_boiler_calculate_item.business_fee_boiler_calculate_id = tbl_business_fee_boiler_calculate.id');
                $this->db->where('tbl_business_fee_boiler_calculate.month', $month);
                $this->db->where('tbl_business_fee_boiler_calculate.year', $year);
                $this->db->where('tbl_business_fee_boiler_calculate_item.staff_id', $staffid);;
                $dtBusinessFee = $this->db->get()->row_array();
                $allowance_business_fee = $dtBusinessFee['total'];
                $total_weekday = $dtBusinessFee['total_weekday'];
                $total_sunday = $dtBusinessFee['total_sunday'];
                $total_holiday = $dtBusinessFee['total_holiday'];
                $total_weekday_night = $dtBusinessFee['total_weekday_night'];
                $total_sunday_night = $dtBusinessFee['total_sunday_night'];
                $salaryFee = $dtBusinessFee['salary'];

                $salaryFeeDiff = (($salaryFee / $totalDate / $hour_day) * $total_weekday * 0.5) + (($salaryFee / $totalDate / $hour_day) * $total_sunday * 1) + (($salaryFee / $totalDate / $hour_day) * $total_holiday * 2);

                $this->db->select('COUNT(tbl_business_fee_boiler_overtime_detail.id) as total');
                $this->db->from('tbl_business_fee_boiler_overtime');
                $this->db->join('tbl_business_fee_boiler_overtime_detail',
                    'tbl_business_fee_boiler_overtime_detail.business_fee_boiler_overtime_id = tbl_business_fee_boiler_overtime.id');
                $this->db->where('tbl_business_fee_boiler_overtime.month', $month);
                $this->db->where('tbl_business_fee_boiler_overtime.year', $year);
                $this->db->where('tbl_business_fee_boiler_overtime.staff_id', $staffid);
                $this->db->where('tbl_business_fee_boiler_overtime_detail.status', 1);
                $this->db->where('(tbl_business_fee_boiler_overtime_detail.weekday != 0 OR tbl_business_fee_boiler_overtime_detail.sunday != 0 OR tbl_business_fee_boiler_overtime_detail.holiday != 0)');
                $this->db->where('(tbl_business_fee_boiler_overtime_detail.weekday >= "2" OR tbl_business_fee_boiler_overtime_detail.sunday >= "4" OR tbl_business_fee_boiler_overtime_detail.holiday >= "2")');
                $totalOverTime = $this->db->get()->row_array()['total'];


                $total_number_day_salary_new = ($countHourNew / HOUR_DAY);

                $total_rice = ceil($total_number_day_salary_new);

//                $total_rice_tc = (!empty($totalOverTime) ? $totalOverTime : 0);
                $total_rice_tc = (($total_weekday / 3) + ($total_sunday / 8)) * $rice_money;

                $total_number_day_holiday = $totalHoliday - $number_day_lt - $number_day_ch;
                $total_number_day_lt = $number_day_lt;

                $total_number_day_ch = $number_day_ch;



                $salary_income = $salary_income;

                $number_day_kp_new = $totalNotHoliday - $number_day_od;

                $html .= '<td style="min-width: 120px;text-align:center">
                <input type="text" class="form-control day_number number-format" name="day_number[]" style="width: 100px" value="'.($total_number_day_salary > 0 ? ($total_number_day_salary) : '').'"></td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="total_number_day_new">'.($total_number_day_salary_new > 0 ? $total_number_day_salary_new : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($total_number_day_holiday > 0 ? $total_number_day_holiday : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($total_number_day_lt > 0 ? $total_number_day_lt : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($total_number_day_ch > 0 ? $total_number_day_ch : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($number_day_kp_new > 0 ? $number_day_kp_new : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:center" class="">'.($number_day_od > 0 ? $number_day_od : '').'</td>';
                $totalNumberDay = $total_number_day_salary_new + $totalHoliday;
                $html .= '<td style="min-width: 120px;text-align:center" class="total_number_day">'.($totalNumberDay > 0 ? formatMoney($totalNumberDay) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="salary_income_html">'.($salary_income > 0 ? formatMoney($salary_income) : '').'</td>';

                $html .= '<td style="min-width: 120px;text-align:center">
                    <input type="text" class="form-control total_weekday number-format" name="total_weekday[]" style="width: 120px" value="'.$total_weekday.'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center">
                    <input type="text" class="form-control total_sunday number-format" name="total_sunday[]" style="width: 100px" value="'.$total_sunday.'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center">
                    <input type="text" class="form-control total_holiday number-format" name="total_holiday[]" style="width: 120px" value="'.$total_holiday.'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center">
                    <input type="text" class="form-control total_weekday_night number-format" name="total_weekday_night[]" style="width: 120px" value="'.$total_weekday_night.'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:center">
                    <input type="text" class="form-control total_sunday_night number-format" name="total_sunday_night[]" style="width: 120px" value="'.$total_sunday_night.'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="allowance_business_fee_html">
                    '.($allowance_business_fee > 0 ? formatMoney($allowance_business_fee) : '').'
                </td>';
                $number_day_kp_check = $number_day_kp + $number_day_up;


                $number_day_cp_check = ($totalNotHoliday - $number_day_up - $number_day_up_new);

//                $html .= '<td style="min-width: 50px;text-align:center" class="">
//                    <div class="money_hour_late_html text-right"></div>
//                </td>';

                $allowance_diff = 0;
                if (!empty($dtAllowance)) {
                    foreach ($dtAllowance as $kk => $vv) {
                        $dtAllowanceStaff = get_table_where('tbl_staff_allowance',
                            ['category_id' => $vv['id'], 'staff_id' => $staffid], '', 'row_array');
                        $allowance_new = !empty($dtAllowanceStaff['amount']) ? ($dtAllowanceStaff['amount']) : 0;

                        if ($vv['type_check'] == 1) {
                            $allowance_new = $allowance_new - ($number_day_kp_check * get_option('diligence_kp'));

                            $allowance_new = $allowance_new - ($number_day_cp_check * get_option('diligence_cp'));
                        }
                        if ($vv['type_check'] == 3) {
                            $allowance_diff += !empty($dtAllowanceStaff['amount']) ? ($dtAllowanceStaff['amount']) : 0;
                        }
                        $allowance_new = $allowance_new < 0 ? 0 : $allowance_new;
                        if ($vv['id'] == ALLOWANCE_THAMNIEN){
                            $allowance_new = $value['seniority_staff'] * 100000;
                        }
                        $html .= '<td style="min-width: 120px;text-align:left">
                            <input type="text" data-id="'.$vv['id'].'" data-staff-id="'.$staffid.'" class="form-control allowance_other_new allowance_other_'.$vv['id'].'_'.$staffid.' number-format" name="allowance_other_'.$vv['id'].'['.$index.'_'.$staffid.']" style="width: 120px" value="'.(!empty($allowance_new) ? formatMoney($allowance_new) : '').'">
                        </td>';
                    }
                }

                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control allowance_rice number-format" name="allowance_rice[]" style="width: 120px" value="'.($total_rice > 0 ? $total_rice : '').'">
                    <input type="hidden" class="form-control allowance_diff number-format" name="allowance_diff[]" style="width: 120px" value="'.($allowance_diff).'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="allowance_rice_money">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <input type="text" class="form-control allowance_rice_tc number-format" name="allowance_rice_tc[]" style="width: 120px" value="'.($total_rice_tc > 0 ? formatMoney($total_rice_tc) : '').'">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="salary_compensation">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:left">
                    <div class="total_allowance text-right"></div>
                    <input type="text" class="form-control bonus_holiday hide number-format" name="bonus_holiday[]" style="width: 120px" value="0">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total"></td>';

                if (!empty($dtReduce)) {
                    foreach ($dtReduce as $kk => $vv) {
                        $dtReduceStaff = get_table_where('tbl_staff_reduce',
                            ['category_id' => $vv['id'], 'staff_id' => $staffid], '', 'row_array');
                        $html .= '<td style="min-width: 120px;text-align:left">
                            <input type="text" class="form-control reduce_other_'.$vv['id'].'_'.$staffid.' number-format reduce_other" name="reduce_other_'.$vv['id'].'['.$index.'_'.$staffid.']" style="width: 120px" value="'.(!empty($dtReduceStaff['amount']) ? formatMoney($dtReduceStaff['amount']) : '').'">
                        </td>';
                    }
                }

                //advance payment

                $end_date = '';
                $start_date = '';
                if (!empty($month) && !empty($year)) {
                    $listDate = getAllDateInMonth($month, $year, 'd/m/Y');
                    $end_date = array_pop($listDate);
                    $start_date = reset($listDate);
                }
                $paymentPayroll = '
                COALESCE(
                (SELECT SUM(tbl_payroll_payment_item.total) 
                FROM tbl_payroll_payment_item 
                WHERE tbl_payroll_payment_item.payroll_payment_id = tbl_payroll_payment.id ),0)
                ';
                $paymentOther = '
                COALESCE(
                (SELECT SUM(tblother_payslips_coupon.total) 
                FROM tblother_payslips_coupon 
                WHERE tblother_payslips_coupon.vouchers_id = tbl_payroll_payment.id AND tblother_payslips_coupon.type_vouchers = 333),0) 
                ';
                $this->db->select("
                    tbl_payroll_payment.id as id,
                    tbl_payroll_payment.code as code,
                    DATE_FORMAT(tbl_payroll_payment.date, '%d-%m-%Y')as date,
                    (tbl_payroll_payment.amount - $paymentOther) as amount,
                    $paymentPayroll as quantity_net,
                ");
                $this->db->from('tbl_payroll_payment');
                $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_payment.staff_id');
                $this->db->where('tblstaff.staffid', $staffid);
                if (!empty($start_date)) {
                    // $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") >=', to_sql_date($start_date));
                }
                if (!empty($end_date)) {
                    $this->db->where('DATE_FORMAT(tbl_payroll_payment.date, "%Y-%m-%d") <=', to_sql_date($end_date));
                }
                $this->db->having('(amount-quantity_net) > 0');
                $payrollPayments = $this->db->get()->result_array();
                $data_json_payment = [];
                if (!empty($payrollPayments)){
                    foreach ($payrollPayments as $k => $v){
                        $paymentPayRoll = get_table_where('tbl_payroll_payment', ['id' => $v['id']], '', 'row_array');
                        $data_json_payment[] = [
                            'payrollPayment' => $v['id'],
                            'total_sub' => ($v['amount'] - $v['quantity_net']),
                            'cal_id' => null,
                            'staff_id' => $staffid,
                            'paymentPayRoll' => $paymentPayRoll,
                        ];
                    }
                }

                $data_json_payment = !empty($data_json_payment) ? json_encode($data_json_payment) : null;


                //end
                $html .= '<td style="min-width: 150px;">
                    <div class="td-payment">
                        <div class="sub"></div>
                        <div class="" style="display: flex;justify-content: flex-end;"><a onclick="addPayrollPayment(this,'.$index.')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Thêm tạm ứng</a></div>
                        <div class="show_payment" style="margin-top: 5px;"></div>
                        <input type="hidden" name="data_json_payment['.$index.']" class="form-control data_json_payment" value="'.tnh_htmlentities($data_json_payment).'">
                        <div class="text-error" style="color: red"></div>
                    </div>
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total_reduce_other"></td>';
                $html .= '<td style="min-width: 120px;text-align:right;">'.($deduct_bhxh > 0 ? formatMoney($deduct_bhxh) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhyt > 0 ? formatMoney($deduct_bhyt) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($deduct_bhyt > 0 ? formatMoney($deduct_bhtn) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right">'.($union_salary > 0 ? formatMoney($union_salary) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total_reduce_bhxh"></td>';

                $html .= '<td style="min-width: 120px;text-align:right" class="grand_total_kt">
                    <input style="width:120px" type="hidden" name="grand_total_kt[]" class="form-control grand_total_kt" value="'.$value['grand_total_kt'].'">
                    '.($value['grand_total_kt'] > 0 ? formatMoney($value['grand_total_kt']) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="grand_total_kl">
                    <input style="width:120px" type="hidden" name="grand_total_kl[]" class="form-control grand_total_kl" value="'.$value['grand_total_kl'].'">
                    '.($value['grand_total_kl'] > 0 ? formatMoney($value['grand_total_kl']) : '').'
                </td>';

                $allowance_family = get_option('money_vat') + (get_option('money_reduce') * $value['number_reduce']);
                $html .= '<td style="min-width: 120px;text-align:right" class="allowance_family">'.($allowance_family > 0 ? formatMoney($allowance_family) : '').'</td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="business_fee_difference">
                    '.($salaryFeeDiff > 0 ? formatMoney($salaryFeeDiff) : '').'
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="tax_exemption">
                </td>';
                $html .= '<td style="min-width: 120px;">
                      <input type="text" class="form-control complete_permission number-format" name="complete_permission[]" style="width: 120px" value="">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="income_taxes">
                </td>';
                $html .= '<td style="min-width: 120px;text-align: right" class="taxable_income">
                </td>';
                $html .= '<td style="min-width: 120px;text-align:right" class="total_vat"></td>';


                $html .= '<td style="min-width: 120px;text-align:right"><span class="total_real"></span>
                <input style="width:100px" type="hidden" class="form-control salary_3p_id" value="'.($salary_3p_id).'" name="salary_3p_id[]">
                <input style="width:100px" type="hidden" class="form-control number_reduce" value="'.($value['number_reduce']).'" name="number_reduce[]">
                <input style="width:100px" type="hidden" class="form-control salary_bhxh" name="salary_bhxh[]" value="'.($salary_bhxh).'">
                <input style="width:100px" type="hidden" class="form-control allowance"value="'.$value['allowance'].'">
                <input style="width:100px" type="hidden" class="form-control salary_bhxh_new" name="salary_bhxh_new[]" value="'.$value['salary_bhxh_new'].'">
                <input style="width:100px" type="hidden" class="form-control" name="business_fee_difference[]" value="'.$salaryFeeDiff.'">
                <input style="width:100px" type="hidden" name="salary_responsibility[]" class="form-control salary_responsibility"value="'.$salary_responsibility.'">
                <input style="width:100px" type="hidden" name="salary_position[]" class="form-control salary_position"value="'.$salary_position.'">
                <input style="width:100px" type="hidden" name="weight_p2[]" class="form-control weight_p2"value="'.$weight_p2.'">
                <input style="width:100px" type="hidden" name="weight_p3[]" class="form-control weight_p3"value="'.$weight_p3.'">
                <input style="width:100px" type="hidden" name="sales[]" class="form-control sales"value="'.$sales.'">
                <input style="width:100px" type="hidden" name="phone[]" class="form-control phone"value="'.$phone.'">
                <input style="width:100px" type="hidden" name="gasonline_cars[]" class="form-control gasonline_cars"value="'.$gasonline_cars.'">
                <input style="width:100px" type="hidden" name="motel[]" class="form-control motel"value="'.$motel.'">
                <input style="width:100px" type="hidden" name="concurrently[]" class="form-control concurrently"value="'.$concurrently.'">
                <input style="width:100px" type="hidden" name="business_fee_staff[]" class="form-control business_fee_staff"value="'.$business_fee_staff.'">
                <input style="width:100px" type="hidden" name="seniority[]" class="form-control seniority"value="'.$seniority.'">
                <input style="width:100px" name="total_date[]" type="hidden" class="form-control total_date" value="'.$totalDate.'">
                <input style="width:100px" name="total_number_day_holiday[]" type="hidden" class="form-control total_number_day_holiday" value="'.$total_number_day_holiday.'">
                <input style="width:100px" name="total_number_day_lt[]" type="hidden" class="form-control total_number_day_lt" value="'.$total_number_day_lt.'">
                <input style="width:100px" name="total_number_day_ch[]" type="hidden" class="form-control total_number_day_ch" value="'.$total_number_day_ch.'">
                <input style="width:100px" name="total_number_day_kp_new[]" type="hidden" class="form-control total_number_day_kp_new" value="'.$number_day_kp_new.'">
                <input style="width:100px" name="total_number_day_od[]" type="hidden" class="form-control total_number_day_od" value="'.$number_day_od.'">
                <input style="width:100px" name="number_day_bhxh[]" type="hidden" class="form-control number_day_bhxh" value="'.$number_day_bhxh.'">
                <input style="width:100px" type="hidden" class="form-control salary_income" value="'.$salary_income.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhxh" value="'.$deduct_bhxh.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhyt" value="'.$deduct_bhyt.'">
                <input style="width:100px" type="hidden" class="form-control deduct_bhtn" value="'.$deduct_bhtn.'">
                <input style="width:100px" type="hidden" class="form-control deduct_union" value="'.$union_salary.'">
                <input style="width:100px" type="hidden" class="form-control deduct_advance" value="'.$deduct_advance.'">
                <input style="width:100px" name="allowance_business_fee[]" type="hidden" class="form-control allowance_business_fee" value="'.$allowance_business_fee.'">
                <input style="width:100px" type="hidden" name="day_number_off[]" class="form-control day_number_off" value="'.$total_number_day_off.'">
                <input type="hidden" name="counter[]" class="form-control counter" value="'.$index.'">
                <input type="hidden" name="staff_id[]" class="form-control staff_id" value="'.$value['staffid'].'">
                <input type="hidden" name="allowance_family[]" class="form-control allowance_family" value="'.$allowance_family.'">
                </td>';


                $html .= '</tr>';
                $index++;
            }
        }

        $tfoot = '';
        if (empty($personnel)) {

            $this->db->select("tblstaff.staffid as staffid,CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as name,tblroles.name as name_role",
                false);
            $this->db->from('tblstaff');
            $this->db->join('tbl_timekeeping_detail', 'tbl_timekeeping_detail.staff_id= tblstaff.staffid', 'left');
            $this->db->join('tbl_timekeeping', 'tbl_timekeeping.id= tbl_timekeeping_detail.timekeeping_id', 'inner');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->where('(tblstaff.check_salary = 0 AND tblstaff.status_work != 2)');
            $this->db->where('tblstaff.branch_salary', $branch_search);
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $this->db->where("($isPayroll = 0)");
            $this->db->group_by('tbl_timekeeping_detail.staff_id');
            $personnelCheck = $this->db->get()->result_array();

            $this->db->from('tbl_timekeeping');
            $this->db->where('tbl_timekeeping.month', $month);
            $this->db->where('tbl_timekeeping.year', $year);
            $personnelCheckTime = $this->db->get()->row_array();
            if (empty($personnelCheckTime)) {
                $data['month'] = $month;
                $data['year'] = $year;
                $data['check'] = 1;
                $this->load->view('admin/payroll/load_view_empty_audit', $data);
            } else {
                if (empty($personnelCheck)) {
                    $data['month'] = $month;
                    $data['year'] = $year;
                    $data['branch_search'] = $branch_search;
                    $data['check'] = 2;
                    $this->load->view('admin/payroll/load_view_empty_audit', $data);
                } else {
                    $data['tHead'] = $tHead;
                    $data['tfoot'] = $tfoot;
                    $data['html'] = $html;
                    $data['dtAllowance'] = $dtAllowance;
                    $data['dtReduce'] = $dtReduce;
                    $this->load->view('admin/payroll/load_add_payroll_salary_audit', $data);
                }
            }

        } else {
            $data['tHead'] = $tHead;
            $data['tfoot'] = $tfoot;
            $data['html'] = $html;
            $data['dtAllowance'] = $dtAllowance;
            $data['dtReduce'] = $dtReduce;
            $this->load->view('admin/payroll/load_add_payroll_salary_audit', $data);
        }
    }

    public function export_excel_audit()
    {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $staff = $this->input->get('staff');
        $branch_search = $this->input->get('branch_search');
        $department = $this->input->get('department');

        $arrFooter = [];
        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_allowance','tbl_salary_allowance.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',1);
        $dtAllowance = $this->db->get()->result_array();

        $this->db->select('tbl_allowance_reduce.*');
        $this->db->from('tbl_allowance_reduce');
        $this->db->join('tbl_salary_reduce','tbl_salary_reduce.category_id = tbl_allowance_reduce.id');
        $this->db->where('tbl_allowance_reduce.type',2);
        $dtReduce = $this->db->get()->result_array();
        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll_audit',
                    ['category_id' => $value['id'], 'type' => 1], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtAllowance[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_allowance_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);

            }
        }
        if (!empty($dtReduce)) {
            foreach ($dtReduce as $key => $value) {
                $dtAllowanceReduce = get_table_where('tbl_allowance_reduce_payroll_audit',
                    ['category_id' => $value['id'], 'type' => 2], '', 'result_array');
                $arrNew = [];
                if (!empty($dtAllowanceReduce)) {
                    foreach ($dtAllowanceReduce as $kk => $vv) {
                        $arrNew[$vv['staff_id'].'_'.$vv['payroll_item_id']] = $vv;
                    }
                }
                $dtReduce[$key]['items'] = $arrNew;
                $arrFooterNew = [
                    'footer_total_reduce_'.$value['id'] => 0,
                ];
                $arrFooter = array_merge($arrFooter, $arrFooterNew);
            }
        }

        $tbDepartment = "(
            SELECT
                tblstaff_departments.staffid as staffid,
                GROUP_CONCAT(tbldepartments.name) as name_department
            FROM tbldepartments
            JOIN tblstaff_departments ON tblstaff_departments.departmentid = tbldepartments.departmentid
            GROUP BY tblstaff_departments.staffid
        ) tb_department";
        $this->db->select("
                tbl_payroll_item_audit.id as id,
                tblstaff.code as code,
                CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as fullname,
                tblroles.name as role,
                tblstaff.day_in as day_in,
                tbl_payroll_item_audit.salary_bhxh as salary_bhxh,
                tbl_payroll_item_audit.staff_id as staff_id,
                tbl_payroll_item_audit.salary_responsibility as salary_responsibility,
                tbl_payroll_item_audit.salary_position as salary_position,
                tbl_payroll_item_audit.concurrently as concurrently,
                tbl_payroll_item_audit.sales as sales,
                tbl_payroll_item_audit.business_fee_staff as business_fee_staff,
                tbl_payroll_item_audit.phone as phone,
                tbl_payroll_item_audit.gasonline_cars as gasonline_cars,
                tbl_payroll_item_audit.motel as motel,
                tbl_payroll_item_audit.day_ch as day_ch,
                tbl_payroll_item_audit.day_lt as day_lt,
                tbl_payroll_item_audit.allowance as allowance,
                tbl_payroll_item_audit.day_number as day_number,
                tbl_payroll_item_audit.day_number_new as day_number_new,
                tbl_payroll_item_audit.day_holiday as day_holiday,
                tbl_payroll_item_audit.day_lt as day_lt,
                tbl_payroll_item_audit.salary_income as salary_income,
                tbl_payroll_item_audit.allowance_responsibility as allowance_responsibility,
                tbl_payroll_item_audit.allowance_other as allowance_other,
                tbl_payroll_item_audit.allowance_manu as allowance_manu,
                tbl_payroll_item_audit.allowance_western as allowance_western,
                tbl_payroll_item_audit.allowance_business_fee as allowance_business_fee,
                tbl_payroll_item_audit.allowance_rice as allowance_rice,
                tbl_payroll_item_audit.allowance_rice_tc as allowance_rice_tc,
                tbl_payroll_item_audit.allowance_rice_money as allowance_rice_money,
                tbl_payroll_item_audit.bonus_holiday as bonus_holiday,
                tbl_payroll_item_audit.deduct_bhxh as deduct_bhxh,
                tbl_payroll_item_audit.deduct_bhyt as deduct_bhyt,
                tbl_payroll_item_audit.deduct_bhtn as deduct_bhtn,
                tbl_payroll_item_audit.deduct_union as deduct_union,
                tbl_payroll_item_audit.deduct_advance as deduct_advance,
                tbl_payroll_item_audit.total_allowance_other as total_allowance_other,
                tbl_payroll_item_audit.total_reduce_other as total_reduce_other,
                tbl_payroll_item_audit.total as total,
                tbl_payroll_item_audit.total_real as total_real,
                tbl_payroll_item_audit.total_weekday,
                tbl_payroll_item_audit.total_sunday,
                tbl_payroll_item_audit.total_holiday,
                tbl_payroll_item_audit.total_weekday_night,
                tbl_payroll_item_audit.total_sunday_night,
                tbl_payroll_item_audit.income_taxes as income_taxes,
                tbl_payroll_item_audit.total_reduce_bhxh as total_reduce_bhxh,
                tbl_payroll_item_audit.allowance_family as allowance_family,
                tbl_payroll_item_audit.total_number_day_kp as total_number_day_kp,
                tbl_payroll_item_audit.total_number_day_od as total_number_day_od,
                tbl_payroll_item_audit.salary_compensation as salary_compensation,
                tbl_payroll_item_audit.taxable_income as taxable_income,
                tbl_payroll_item_audit.tax_exemption as tax_exemption,
                tbl_payroll_item_audit.total_vat as total_vat,
                tbl_payroll_item_audit.complete_permission as complete_permission,
                tbl_payroll_item_audit.seniority as seniority,
                tbl_payroll_item_audit.grand_total_kt as grand_total_kt,
                tbl_payroll_item_audit.grand_total_kl as grand_total_kl,
            ");
        $this->db->from('tbl_payroll_audit');
        $this->db->join('tbl_payroll_item_audit', 'tbl_payroll_item_audit.payroll_id = tbl_payroll_audit.id', 'inner');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_payroll_item_audit.staff_id', 'inner');
        $this->db->join($tbDepartment, 'tb_department.staffid = tblstaff.staffid', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
        $this->db->join('tbl_business_fee_boiler_calculate_item',
            'tbl_business_fee_boiler_calculate_item.id = tbl_payroll_item_audit.business_fee_boiler_calculate_item_id',
            'left');
        if (!empty($month)) {
            $this->db->where('tbl_payroll_audit.month', $month);
        }

        if (!empty($year)) {
            $this->db->where('tbl_payroll_audit.year', $year);
        }

        if (!empty($branch_search)) {
            $this->db->where('tblstaff.branch_salary', $branch_search);
        }

        if (!empty($staff)) {
            $staff = explode(',', $staff);
            $this->db->where_in('tbl_payroll_item_audit.staff_id', $staff);
        }
        if (!empty($department)) {
            $this->db->where('EXISTS (
                SELECT tblstaff_departments.staffid
                FROM tblstaff_departments
                WHERE tblstaff_departments.staffid = tblstaff.staffid
                AND tblstaff_departments.departmentid = '.$department.'
            )');
        }

        $personnel = $this->db->get()->result_array();

        $c_excel = [
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
            'DZ',
        ];
        ini_set('memory_limit', '3500M');
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        ob_end_clean();
        $data = [];

        $timekeepingId = 0;

        $styleTh = [
            'font' => array(
                'bold' => true,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        ];
        $styleTd = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];
        $styleTd_center = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];
        $styleTd_left = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];
        $styleTd_right = [
            'font' => array(
                'bold' => false,
                'name' => 'Times New Roman',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ];


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
            ->setWidth(25);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(20);

        $decimals_money = get_option('decimals_money');
        $decimals_number = get_option('decimals_number');
        $number_excel_money = '#,##0'.($decimals_money > 0 ? '.'.sprintf("%0".$decimals_money."s", 0) : '');
        $number_excel_number = '#,##0'.($decimals_number > 0 ? '.'.sprintf("%0".$decimals_number."s", 0) : '');

        $company = get_option('invoice_company_name');
        $address = get_option('invoice_company_address');
        $phonenumber = get_option('invoice_company_phonenumber');
        $styleNone = [
            'font' => array(
                'size' => 13,
                'name' => 'Times New Roman',
            ),
        ];

        $company_logo = get_option('company_logo');
        if (file_exists('uploads/company/'.$company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('uploads/company/'.$company_logo);
            $objDrawing->setCoordinates('A1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getStyle("A1");
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setResizeProportional(false);

            $objDrawing->setWidth(55);
            $objDrawing->setHeight(55);
        }

        $objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $company)->getStyle('B1:I1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('B2:I2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', $address)->getStyle('B2:I2')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:I3');
        $objPHPExcel->getActiveSheet()->setCellValue('B3',
            'SĐT: '.$phonenumber)->getStyle('B3:I3')->applyFromArray($styleNone);

        $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
        $objPHPExcel->getActiveSheet()->setCellValue('A5', 'BẢNG LƯƠNG AUDIT')->getStyle('A5:Q5')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 25,
                'name' => 'Times New Roman',
                'color' => array('rgb' => 'ff0202'),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('A6:Q6');
        $objPHPExcel->getActiveSheet()->setCellValue('A6',
            ('THÁNG '.$month.' NĂM '.$year))->getStyle("A6:Q6")->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
                'name' => 'Times New Roman',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        ]);
        $rowBegin = 8;
        $rowBeginNext = 9;

        $sttC = 3;
        $stt = 0;
        $sttNew = 1;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'STT'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Họ Tên'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Chức Vụ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Ngày vào làm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Lương vị trí (LCB)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Lương năng lực')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Chức vụ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'T.Nhiệm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Lương đóng BHXH'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Kiêm nhiệm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Doanh số'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Công tác phí'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Điện thoại'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Xăng xe đi lại'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Nhà trọ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Tổng phụ cấp'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Thâm niên'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số giờ công'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số ngày công'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Ngày nghỉ có lương')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Phép năm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Lễ tết'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'VR hưởng lương (hiếu hỉ)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Nghỉ không lương')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 1].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Nghỉ việc riêng'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Ốm đau'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBegin,
            'Tổng ngày công tính lương'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].($rowBeginNext))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Thu nhập'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Số tiếng tăng ca')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 4].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Ngày thường(1.5)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Chủ nhật(2.0)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Lễ tết(3.0)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Đêm thường('.get_option('coefficient_default_night').')'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Đêm chủ nhật('.get_option('coefficient_sunday_night').')'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng tiền tăng ca'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2 + (count($dtAllowance))].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Các khoản phải trả'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 2 + (count($dtAllowance))].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        if (!empty($dtAllowance)) {
            foreach ($dtAllowance as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
                    $vv['name']."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
                $stt++;
                $sttNew ++;
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Ngày cơm hành chính'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Ngày cơm tăng ca'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Tiền ăn hành chính'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Bù lương'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng các khoản phải trả'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng thu nhập'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + (count($dtReduce))].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Khấu trừ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + (count($dtReduce))].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        if (!empty($dtReduce)) {
            foreach ($dtReduce as $kk => $vv) {
                $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
                    $vv['name']."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
                $stt++;
                $sttNew ++;
            }
        }
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            'Khấu trừ khác(tạm ứng)'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng các khoản trừ'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].($rowBegin));
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Khoản trừ BHXH'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].($rowBegin))->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '8% BHXH'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '1,5% BHYT'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '1% BHTN'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->setCellValue($c_excel[$stt].$rowBeginNext,
            '1% Đoàn phí'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;

        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng khấu trừ BHXH'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Khen thưởng KPIs'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Kỹ luật KPIs'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Giảm trừ gia cảnh'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Lương ngoài giờ miễn thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Các khoản miễn thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Hoàn phép năm'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Thu nhập chịu thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Thu nhập tính  thuế'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Thuế TNCN'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $stt++;
        $sttNew ++;
        $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",'')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBegin)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
            'Tổng thực lãnh'."\n ($sttNew)")->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt].$rowBeginNext)->applyFromArray($styleTh)->getAlignment()->setWrapText(true);

        $rowBegin++;
        $rowBegin++;
        if (!empty($personnel)) {
            $iSTT = 1;
            $countStart = $rowBegin;
            foreach ($personnel as $key => $value) {

                $stt = 0;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $iSTT)->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_center);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $value['fullname'])->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    $value['role'])->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['day_in']) ? _dhau($value['day_in']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_left);
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_bhxh']) ? ($value['salary_bhxh']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_position']) ? ($value['salary_position']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['salary_responsibility']) ? ($value['salary_responsibility']) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $salary = $value['salary_bhxh'] + $value['salary_position'] + $value['salary_responsibility'];
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($salary) ? ($salary) : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['concurrently']) ? ($value['concurrently']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['sales']) ? ($value['sales']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['business_fee_staff']) ? ($value['business_fee_staff']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['phone']) ? ($value['phone']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['gasonline_cars']) ? ($value['gasonline_cars']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['motel']) ? ($value['motel']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $totalPhuCap = $value['concurrently'] + $value['sales'] + $value['business_fee_staff'] + $value['phone'] + $value['gasonline_cars'] + $value['motel'];
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($totalPhuCap) ? ($totalPhuCap) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['seniority']) ? ($value['seniority']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_number']) ? ($value['day_number']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_number_new']) ? ($value['day_number_new']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_holiday']) ? ($value['day_holiday']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_lt']) ? ($value['day_lt']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['day_ch']) ? ($value['day_ch']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_number_day_kp']) ? ($value['total_number_day_kp']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_number_day_od']) ? ($value['total_number_day_od']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $totalNumberDay = $value['day_number_new'] + $value['day_holiday'] + $value['day_lt'] + $value['day_ch'];
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($totalNumberDay) ? ($totalNumberDay) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['salary_income']) ? ($value['salary_income']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_weekday']) ? ($value['total_weekday']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_sunday']) ? ($value['total_sunday']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_holiday']) ? ($value['total_holiday']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_weekday_night']) ? ($value['total_weekday_night']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_sunday_night']) ? ($value['total_sunday_night']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_business_fee']) ? ($value['allowance_business_fee']) : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');
                $stt++;

                if (!empty($dtAllowance)) {
                    foreach ($dtAllowance as $kk => $vv) {
                        $items = $vv['items'];
                        $checkKey = $value['staff_id'].'_'.$value['id'];
                        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                            (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : ''))->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                        $stt++;
                    }
                }

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_rice']) ? $value['allowance_rice'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_rice_tc']) ? $value['allowance_rice_tc'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['allowance_rice_money']) ? $value['allowance_rice_money'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['salary_compensation']) ? $value['salary_compensation'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_allowance_other']) ? $value['total_allowance_other'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total']) ? $value['total'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['deduct_advance']) ? $value['deduct_advance'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;

                if (!empty($dtReduce)) {
                    foreach ($dtReduce as $kk => $vv) {
                        $items = $vv['items'];
                        $checkKey = $value['staff_id'].'_'.$value['id'];
                        $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                            (!empty($items[$checkKey]['amount']) ? ($items[$checkKey]['amount']) : ''))->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                        $stt++;
                    }
                }

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['total_reduce_other']) ? $value['total_reduce_other'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
                    !empty($value['deduct_bhxh']) ? $value['deduct_bhxh'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_bhyt']) ? $value['deduct_bhyt'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_bhtn']) ? $value['deduct_bhtn'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['deduct_union']) ? $value['deduct_union'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;

                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['total_reduce_bhxh']) ? $value['total_reduce_bhxh'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['grand_total_kt']) ? $value['grand_total_kt'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['grand_total_kl']) ? $value['grand_total_kl'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['allowance_family']) ? $value['allowance_family'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['business_fee_difference']) ? $value['business_fee_difference'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['tax_exemption']) ? $value['tax_exemption'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['complete_permission']) ? $value['complete_permission'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['income_taxes']) ? $value['income_taxes'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['taxable_income']) ? $value['taxable_income'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['total_vat']) ? $value['total_vat'] : '')->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

                $stt++;
                $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
                    !empty($value['total_real']) ? $value['total_real'] : '')->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');;

                $rowBegin++;
                $iSTT++;
            }
            $countEnd = $rowBegin - 1;


            $styleTd_center['font']['color'] = array('rgb' => 'ff0202');
            $styleTd_right['font']['color'] = array('rgb' => 'ff0202');


            $stt = 0;
//            $objPHPExcel->getActiveSheet()->mergeCells($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin);
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                'TỔNG')->getStyle($c_excel[$stt].$rowBegin.':'.$c_excel[$stt + 3].$rowBegin)->applyFromArray($styleTd_center);
//            $stt++;
//            $stt++;
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0.00');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]".$rowBegin)->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
//                "")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]".$rowBegin,
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');
//            $stt++;
//
//            if (!empty($dtAllowance)) {
//                foreach ($dtAllowance as $kk => $vv) {
//                    $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                        "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//                    $stt++;
//                }
//            }
//
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_center)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//
//            if (!empty($dtReduce)) {
//                foreach ($dtReduce as $kk => $vv) {
//                    $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                        "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle("$c_excel[$stt]$rowBegin")->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//                    $stt++;
//                }
//            }
//
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');
//            $stt++;
//            $objPHPExcel->getActiveSheet()->setCellValue("$c_excel[$stt]$rowBegin",
//                "=SUM($c_excel[$stt]$countStart:$c_excel[$stt]$countEnd)")->getStyle($c_excel[$stt].$rowBegin)->applyFromArray($styleTd_right)->getNumberFormat()->setFormatCode('#,##0.00');

            $rowBegin++;
        }
//        $rowBegin++;
//        $year = date('Y');
//        $objPHPExcel->getActiveSheet()->mergeCells("AA$rowBegin".':'."AD$rowBegin");
//        $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin",
//            'Ngày.....Tháng.....Năm '.$year.'')->getStyle("AA$rowBegin")->applyFromArray([
//            'font' => array(
//                'bold' => true,
//                'size' => 12,
//                'name' => 'Times New Roman',
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            ),
//        ])->getFont()->setItalic(true);
//        $rowBegin++;
//        $objPHPExcel->getActiveSheet()->mergeCells("B$rowBegin".':'."E$rowBegin");
//        $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin",
//            'NGƯỜI LẬP')->getStyle("B$rowBegin")->applyFromArray([
//            'font' => array(
//                'bold' => true,
//                'size' => 13,
//                'name' => 'Times New Roman',
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            ),
//        ]);
//        $objPHPExcel->getActiveSheet()->mergeCells("P$rowBegin".':'."P$rowBegin");
//        $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", 'KẾ TOÁN')->getStyle("S$rowBegin")->applyFromArray([
//            'font' => array(
//                'bold' => true,
//                'size' => 13,
//                'name' => 'Times New Roman',
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            ),
//        ]);
//        $objPHPExcel->getActiveSheet()->mergeCells("AA$rowBegin".':'."AD$rowBegin");
//        $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin",
//            'GIÁM ĐỐC')->getStyle("AA$rowBegin")->applyFromArray([
//            'font' => array(
//                'bold' => true,
//                'size' => 13,
//                'name' => 'Times New Roman',
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            ),
//        ]);


        $objPHPExcel->getActiveSheet()->freezePane('A1');
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="BANG_LUONG_THANG_'.$month.'_NAM_'.$year.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }


    public function deletePayrollAudit()
    {
        if (!$this->perDeletePayrollSalary) {
            $data['result'] = 0;
            $data['message'] = lang('Truy cập bị từ chối');
            echo json_encode($data);
            die;
        }
        $data = [];

        if ($this->input->post()) {
            $ids = trim($this->input->post('ids'), ',');
            if (!$ids) {
                $data['result'] = 0;
                $data['message'] = lang('no_data_exists');
                echo json_encode($data);

                return;
            }
            $errors = '';
            $count = 0;
            $ids = explode(',', $ids);
            $ids = array_unique($ids);
            if (!empty($ids)) {
                foreach ($ids as $key => $id) {
                    $payroll = get_table_where('tbl_payroll_item_audit', ['id' => $id], '', 'row_array');
                    $this->db->where('id', $id);
                    $success = $this->db->delete('tbl_payroll_item_audit');
                    if ($success) {

                        $this->db->where('payroll_item_id', $id);
                        $this->db->delete('tbl_allowance_reduce_payroll_audit');

                        $this->db->select('tbl_payroll_payment_item_audit.*');
                        $this->db->from('tbl_payroll_payment_item_audit');
                        $this->db->where('payroll_item_id', $payroll['id']);
                        $payroll_payment_items = $this->db->get()->result_array();

                        if (!empty($payroll_payment_items)) {
                            foreach ($payroll_payment_items as $kkk => $vvv) {

                                $this->db->where('id', $vvv['id']);
                                $this->db->delete('tbl_payroll_payment_item_audit');

                            }
                        }

                        $count++;
                    }
                }
            }
            if ($count) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
            $data['errors'] = $errors;
            echo json_encode($data);

            return;
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}