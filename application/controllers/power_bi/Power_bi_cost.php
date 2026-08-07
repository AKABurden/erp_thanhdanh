<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Power_bi_cost extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function ListDepartments()
    {
        $this->db->select('
            tbldepartments.departmentid as id,
            tbldepartments.code as code,
            tbldepartments.name as name 
        ');
        $departments = $this->db->get('tbldepartments')->result_array();
        $this->response($departments, REST_Controller::HTTP_OK);
    }
    public function ListStaff_departments()
    {
        $staff_departments = $this->db->get('tblstaff_departments')->result_array();
        $this->response($staff_departments, REST_Controller::HTTP_OK);
    }
    public function ListStaff()
    {
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(firstname," ",lastname) as full_name,
        ');
        $staff = $this->db->get('tblstaff')->result_array();
        $this->response($staff, REST_Controller::HTTP_OK);
    }
    public function ListClient()
    {
        $this->db->select('
            tblclients.userid as userid,
            tblclients.zcode as zcode,
            tblclients.company as company,
        ');
        $clients = $this->db->get('tblclients')->result_array();
        $this->response($clients, REST_Controller::HTTP_OK);
    }

    public function ListSuppliers()
    {
        $this->db->select('
            tblsuppliers.id as id,
            tblsuppliers.code as code,
            tblsuppliers.company as company,
        ');
        $suppliers = $this->db->get('tblsuppliers')->result_array();
        $this->response($suppliers, REST_Controller::HTTP_OK);
    }
    public function ListCost()
    {
        $this->db->select('
            tblcosts.id as id,
            tblcosts.name as name,
            tblcosts.code as code,
            tblcosts.costs_parent as costs_parent,
            tblcosts.lever as lever,
            tblcosts.type as type
            ');
        $costs = $this->db->get('tblcosts')->result_array();
        $this->response($costs, REST_Controller::HTTP_OK);
    }
    public function ListPaymentModes()
    {
        $this->db->select('
            tblpayment_modes.id as id,
            tblpayment_modes.name as name,
            tblpayment_modes.cash as cash,
            tblpayment_modes.bank as bank,
            tblpayment_modes.opening_balance as opening_balance,
            ');
        $this->db->where('active', 1);
        $payment_modes = $this->db->get('tblpayment_modes')->result_array();
        $this->response($payment_modes, REST_Controller::HTTP_OK);
    }
    public function Getcashflow()
    {
        $query = "
            SELECT
                tblvouchers_coupon.code_vouchers as code,
                tblvouchers_coupon.date_vouchers as date,
                tblvouchers_coupon.payment_mode as payment_mode,
                0 as id_costs,
                ROUND(tblvouchers_coupon.total, 0) as total,
                ROUND(tblvouchers_coupon.payment, 0) as payment,
                1 as type,
                tblvouchers_coupon.staff as staff,
                tblvouchers_coupon.customer as customer,
                0 as id_suppliers,
                0 as staffid,
                0 as other,
                tblvouchers_coupon.note as note
            FROM tblvouchers_coupon

            UNION ALL

            SELECT
                CONCAT(tblother_payslips_coupon.prefix,'-',tblother_payslips_coupon.code) as code,
                tblother_payslips_coupon.date as date,
                tblother_payslips_coupon.payment_modes as payment_mode,
                0 as id_costs,
                ROUND(tblother_payslips_coupon.total, 0) as total,
                ROUND(tblother_payslips_coupon.total, 0) as payment,
                2 as type,
                tblother_payslips_coupon.staff_id as staff,
                IF(objects = 1,objects_id,0) as customer,
                IF(objects = 2,objects_id,0) as id_suppliers,
                IF(objects = 3,objects_id,0) as staffid,
                IF(objects = 4,objects_id,0) as other,
                tblother_payslips_coupon.note as note
            FROM tblother_payslips_coupon

            UNION ALL

            SELECT
                CONCAT(tblpay_slip.prefix,'-',tblpay_slip.code) as code,
                tblpay_slip.day_vouchers as date,
                tblpay_slip.payment_mode as payment_mode,
                IF(tblcosts.costs_parent = 0,tblpay_slip.id_costs,tblcosts.costs_parent) id_costs,
                ROUND(tblpay_slip.total*tblpay_slip.amount_to_vnd,0) as total,
                ROUND(tblpay_slip.payment*tblpay_slip.amount_to_vnd,0) as payment,
                3 as type,
                tblpay_slip.staff_id as staff,
                0 as customer,
                tblpay_slip.id_supplierss as id_suppliers,
                0 as staffid,
                0 as other,
                tblpay_slip.note as note
            FROM tblpay_slip
            LEFT JOIN tblcosts ON tblcosts.id = tblpay_slip.id_costs

            UNION ALL

            SELECT
                CONCAT(tblother_payslips.prefix,'-',tblother_payslips.code) as code,
                tblother_payslips.date as date,
                tblother_payslips.payment_modes as payment_mode,
                IF(tblcosts.costs_parent = 0,tblother_payslips.id_costs,tblcosts.costs_parent) id_costs,
                ROUND(tblother_payslips.total,0) as total,
                ROUND(tblother_payslips.total,0) as payment,
                4 as type,
                tblother_payslips.staff_id as staff,
                IF(objects = 1,objects_id,0) as customer,
                IF(objects = 2,objects_id,0) as id_suppliers,
                IF(objects = 3,objects_id,0) as staffid,
                IF(objects = 4,objects_id,0) as other,
                tblother_payslips.note as note
            FROM tblother_payslips
            LEFT JOIN tblcosts ON tblcosts.id = tblother_payslips.id_costs
            WHERE is_advance = 0
            
            ORDER BY date ASC
        ";
        $result = $this->db->query($query)->result_array();
        $this->response($result, REST_Controller::HTTP_OK);
    }
    public function GetFinancialControlDetail()
    {
        $month = array(
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        );
        // tblfinancial_control_detail
        $query = "
            SELECT
            tblfinancial_control_detail.*,
            IF(tblcosts.costs_parent = 0,tblfinancial_control_detail.id_financial_control,tblcosts.costs_parent) id_financial_control
            FROM tblfinancial_control_detail
            LEFT JOIN tblcosts ON tblcosts.id = tblfinancial_control_detail.id_financial_control
        ";
        $result = $this->db->query($query)->result_array();
        $ins = array();
        $dem = 0;
        foreach ($result as $key => $value) {

            for ($i = 1; $i <= 12; $i++) {
                $ins[$dem]['id_financial_control'] = $value['id_financial_control'];
                $ins[$dem]['month'] = $i;
                $ins[$dem]['total'] = $value[$month[$i]];
                $ins[$dem]['year'] = $value['nam'];
                $ins[$dem]['map'] = $value['id_financial_control'] . '_' . $i . '_' . $value['nam'];
                $dem++;
            }
        }
        $this->response($ins, REST_Controller::HTTP_OK);
    }
    public function GetFinancialControlDetail_new()
    {
        $year = 2022;
        $month = array(
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        );
        $query = "
            SELECT
            tblcosts.*
            FROM tblcosts
            WHERE lever = 1
        ";
        $ins = array();
        $dem = 0;
        $result = $this->db->query($query)->result_array();
        for ($j = $year; $j <= date('Y'); $j++) {
            foreach ($result as $key => $value) {
                $cost_chil = get_table_where('tblcosts', array('costs_parent' => $value['id']));

                for ($i = 1; $i <= 12; $i++) {
                    $total = 0;
                    foreach ($cost_chil as $key => $value) {
                        $financial_control_detail = get_table_where('tblfinancial_control_detail', array('id_financial_control' => $value['id'], 'nam' => $j), '', 'row_array');
                        if (!empty($financial_control_detail)) {
                            $total += $financial_control_detail[$month[$i]];
                        }
                    }
                    $ins[$dem]['id_cost'] = $value['id'];
                    $ins[$dem]['month'] = $i;
                    $ins[$dem]['total'] = $total;
                    $thu = get_table_where_sum('tblother_payslips_coupon', array('month(date)' => $i, 'year(date)' => $j, 'id_costs' => $value['id']), 'total');
                    $chi = get_table_where_sum('tblother_payslips', array('month(date)' => $i, 'year(date)' => $j, 'id_costs' => $value['id']), 'total');
                    $chi_c = get_table_where_sum('tblpay_slip', array('month(date)' => $i, 'year(date)' => $j, 'id_costs' => $value['id']), 'total');
                    $tu = get_table_where_sum('tbladvance_payment', array('month(date)' => $i, 'year(date)' => $j, 'id_costs' => $value['id']), 'total');
                    $thuchi = sum_fin_other_operations($value['id'], $j, 0, $i) + $thu + $chi + $chi_c + $tu;
                    $ins[$dem]['thuchi'] = $thuchi;
                    $ins[$dem]['year'] = $j;
                    $dem++;
                }
            }
        }
        $this->response($ins, REST_Controller::HTTP_OK);
    }
    public function GetListTU()
    {
        $this->db->select('
                tblother_payslips.date as date,
                CONCAT(tblother_payslips.prefix,"-",tblother_payslips.code) as code,
                tblother_payslips.objects_id as objects_id,
                tblother_payslips.payment_modes as payment_modes,
                tblother_payslips.id_costs as id_costs,
                tblother_payslips.total as total,
                tblother_payslips.id_payment as id_payment,
                tblother_payslips.staff_id as staff_id,
                tblother_payslips.note as note,
                tblother_payslips.id_payment as id_payment,
            ');
        $this->db->where('tblother_payslips.is_advance', 1);
        $costs = $this->db->get('tblother_payslips')->result_array();
        $this->response($costs, REST_Controller::HTTP_OK);
    }
}
