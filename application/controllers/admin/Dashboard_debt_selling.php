<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_debt_selling extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->perViewQuotes = has_permission('quotes', '', 'view');
        $this->perViewOrders = has_permission('orders', '', 'view');
        $this->perViewReturned_goods = has_permission('returned_goods', '', 'view');
        $this->perViewownQuotes = has_permission('quotes', '', 'view_own');
        $this->perViewownOrders = has_permission('orders', '', 'view_own');
        $this->perViewownReturned_goods = has_permission('returned_goods', '', 'view_own');
        $this->perviewVouchers_coupon = has_permission('vouchers_coupon', '', 'view');
        $this->perViewownVouchers_coupon = has_permission('vouchers_coupon', '', 'view_own');
        $this->perviewDebt_clients = has_permission('debt_clients', '', 'view');
        $this->perViewownDebt_clients = has_permission('debt_clients', '', 'view_own');

        $this->ci = &get_instance();
        $this->load->model('reports_model');
        $this->load->model('dashboard_model');
    }
    public function index()
    {
        $this->db->select('tblactivity_log_v2.*');
        $this->db->where('tblactivity_log_v2.type_parent_obj', 'purchase');
        $this->db->order_by('tblactivity_log_v2.id DESC');
        $this->db->limit(10);
        $data['dataLog'] = $this->db->get('tblactivity_log_v2')->result_array();

        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.active', 1);
        $data['staff'] = $this->db->get()->result_array();

        $data['dataStaff'] = get_table_where('tblstaff');
        foreach ($data['dataStaff'] as $key => $value) {
            $data['dataStaff'][$key]['name'] = get_staff_full_name($value['staffid']);
        }
        $data['title'] = _l('sum_dashboard');
        $this->load->view('admin/dashboard_debt_selling/manage', $data);

    }
    public function count_all()
    {
        $arrIDStaff = employee_manage_staff();
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $begin = to_sql_date($data['report_from']);
            $end   = to_sql_date($data['report_to']);
            if((strtotime($end)-strtotime($begin)) < 0){
                $data['subtotal'] = 0;
                $data['subtotal1'] = 0;
                $data['subtotal2'] = 0;
                echo json_encode($data);die;
            }
        }
        $beginMonth =  '';
        $endMonth   =  '';
        // $months_report = $data['months_report'];
/* =========================begin $whereJoin_order ===================================================*/
        // $id_branch = get_staff_user_id_branch();
        $whereJoin_coupon_invoice=array();
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_coupon_invoice['where']=array(
                  'tbl_orders.date >=' =>$beginMonth.' 00:00:00',
                  'tbl_orders.date <=' =>$endMonth.' 23:59:59',
                );
            }
            // if(!$this->perViewOrders) {
            //     if($arrIDStaff != array()) {
            //         $coverStr = implode(",", $arrIDStaff);
            //         $whereJoin_coupon_invoice['where'][] = 'tbl_orders.created_by IN ('.$coverStr.') OR tbl_orders.employee_id IN ('.$coverStr.')';
            //     }
            // }
            // else if (!$this->perViewownQuotes){
            //     if($id_branch != 'main') {
            //         $whereJoin_coupon_invoice['where'][] = 'tbl_orders.id_branch = ('.$id_branch.') OR tbl_orders.employee_id IN ('.$coverStr.')';
            //     }
            // }
            if(!empty($data['search_id_staff']))
            {
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tbl_orders.created_by = '.$value.') or '.$where_or;
                }
                $whereJoin_coupon_invoice['where_or'] = '( '.trim($where_or,'or ').' )';
            }
            if(!empty($data['customers_ch']))
            {
                $whereJoin_coupon_invoice['where'][] = 'tbl_orders.customer_id = '.$data['customers_ch'];
            }
            $whereJoin_coupon_invoice['join']=array();
            $whereJoin_coupon_invoice['field']='grand_total';
            $order = (sum_from_table_join('tbl_orders',$whereJoin_coupon_invoice));

            $this->db->select('count(*) as count');

            if(!$this->perViewOrders) {
                if($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $this->db->where('tbl_orders.created_by IN ('.$coverStr.') OR tbl_orders.employee_id IN ('.$coverStr.')');
                }
            }
            // else if (!$this->perViewownQuotes){
            //     if($id_branch != 'main') {
            //         $this->db->where('tbl_orders.id_branch IN ('.$id_branch.') OR tbl_orders.employee_id IN ('.$coverStr.')');
            //     }
            // }

            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tbl_orders.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tbl_orders.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['customers_ch']))
            {
                $this->db->where('tbl_orders.customer_id ',$data['customers_ch']);
            }
            if(!empty($data['search_id_staff']))
            {
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tbl_orders.created_by = ',$value);
                }
            }
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_invoice_items.object_id', 'left');
            $count_order = $this->db->get('tbl_invoice_items')->row();
            $count_orders = 0 ;
            if(!empty($count_order))
            {
                $count_orders = $count_order->count;
            }
/* ========================= begin $whereJoin_vouchers_coupon =======================================*/
        $whereJoin_vouchers_coupon = array();
        if(!empty($customers_ch))
        {
            $whereJoin['where'][] = 'tblvouchers_coupon.customer = '.$customers_ch;
        }
        if(!empty($data['search_id_staff']))
        {
            $whereJoin['where_or'] = '( '.trim($where_or,'or ').' )';
        }
        $whereJoin_vouchers_coupon['join']=array();
        $whereJoin_vouchers_coupon['field']='payment';
        $vouchers_coupon = (sum_from_table_join('tblvouchers_coupon',$whereJoin_vouchers_coupon));
        $this->db->select('count(*) as count');

            if(!$this->perviewVouchers_coupon) {
                if($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $this->db->where('tblvouchers_coupon.staff_create IN ('.$coverStr.') OR tblvouchers_coupon.staff IN ('.$coverStr.')');
                }
            }
            // else if (!$this->perViewownVouchers_coupon){
            //     if($id_branch != 'main') {
            //         $this->db->where('tblvouchers_coupon.id_branch = '.$id_branch);
            //     }

            // }

            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tblvouchers_coupon.date_create >=',$beginMonth.' 00:00:00');
                $this->db->where('tblvouchers_coupon.date_create <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['customers_ch']))
            {
                $this->db->where('tblvouchers_coupon.customer ',$data['customers_ch']);
            }
            if(!empty($data['search_id_staff']))
            {
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tblvouchers_coupon.staff_create = ',$value);
                }
            }
            $count_vouchers_coupon = $this->db->get('tblvouchers_coupon')->row();
            $count_vouchers_coupons = 0 ;
            if(!empty($count_vouchers_coupon))
            {
                $count_vouchers_coupons = $count_vouchers_coupon->count;
            }
/* ========================= begin $whereJoin_debt_clients ===========================================*/
            $whereJoin=array();
            $whereJoin['join']=array();
            if(!$this->perviewDebt_clients) {
                if($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $whereJoin['where'] []=  'tbl_orders.created_by IN ('.$coverStr.') OR tbl_orders.employee_id IN ('.$coverStr.')';
                }
            }

            $whereJoin['field']='grand_total';
            $subtotal=sum_from_table_join('tbl_orders',$whereJoin);
            $whereJoin1=array();
            if(!$this->perviewDebt_clients) {
                if($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $whereJoin1['where'] []=  'tbl_orders.created_by IN ('.$coverStr.') OR tbl_orders.employee_id IN ('.$coverStr.')';
                }
            }
            $whereJoin1['join'] =array();
            $whereJoin1['field']='total_payment';
            $total_payment=sum_from_table_join('tbl_orders',$whereJoin1);
            $whereJoin2=array();
            if(!$this->perviewDebt_clients) {
                if($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $whereJoin2['where'] []=  'tbl_orders.created_by IN ('.$coverStr.') OR tbl_orders.employee_id IN ('.$coverStr.')';
                }
            }
            $whereJoin2['join'] =array();
            $whereJoin2['field']='price_other_expenses';
            $total_payment_invoice=sum_from_table_join('tbl_orders',$whereJoin2);
            $whereJoin3['join']=array();
            if(!$this->perviewDebt_clients) {
                if($arrIDStaff != array()) {
                    $coverStr = implode(",", $arrIDStaff);
                    $whereJoin3['where'] []=  'tbl_orders.created_by IN ('.$coverStr.') OR tbl_orders.employee_id IN ('.$coverStr.')';
                }
            }
            $whereJoin3['field']='COALESCE((SELECT SUM(tbl_returned_goods.grand_total) FROM tbl_returned_goods WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.order_id = tbl_orders.id),0)';
            $total_return = (sum_from_table_join_v2('tbl_orders',$whereJoin3));

            $debt_client =number_format($subtotal - $total_payment -$total_payment_invoice - $total_return);

        $data['orders'] = number_format($count_orders).' đơn';
        $data['vouchers_coupons'] = number_format($count_vouchers_coupons).' đơn';
        $data['debt_clients'] = $debt_client.' VNĐ';
        $data['date_update'] = "Updated "._dt(date('Y-m-d H:i:s'));
        echo json_encode($data);
    }
    public function sum_order_items($day='',$month='',$year='',$customers_ch='',$data=array(),$where_or='')
    {
        $whereJoin=array();
        if(!empty($day))
        {
            $whereJoin['where']['day(tbl_orders.date) = '] = $day;
        }
        if(!empty($month))
        {
            $whereJoin['where']['month(tbl_orders.date) = '] = $month;
        }
        if(!empty($year))
        {
            $whereJoin['where']['year(tbl_orders.date) = '] = $year;
        }
        if(!empty($customers_ch))
        {
            $whereJoin['where'][] = 'tbl_orders.customer_id = '.$customers_ch;
        }
        if(!empty($data['search_id_staff']))
        {
            $whereJoin['where_or'] = '( '.trim($where_or,'or ').' )';
        }
        $whereJoin['join'] = array();
        $whereJoin['field']='grand_total';
        $sum = (sum_from_table_join('tbl_orders',$whereJoin));
        return $sum;
    }
    public function sum_order_pay($day='',$month='',$year='',$customers_ch='',$data=array(),$where_or='')
    {
        $whereJoin=array();
        if(!empty($day))
        {
            $whereJoin['where']['day(tblvouchers_coupon.date_vouchers) = '] = $day;
        }
        if(!empty($month))
        {
            $whereJoin['where']['month(tblvouchers_coupon.date_vouchers) = '] = $month;
        }
        if(!empty($year))
        {
            $whereJoin['where']['year(tblvouchers_coupon.date_vouchers) = '] = $year;
        }
        if(!empty($customers_ch))
        {
            $whereJoin['where'][] = 'tblvouchers_coupon.customer = '.$customers_ch;
        }
        if(!empty($data['search_id_staff']))
        {
            $whereJoin['where_or'] = '( '.trim($where_or,'or ').' )';
        }
        $whereJoin['join'] = array();
        $whereJoin['field']='payment';
        $sum = (sum_from_table_join('tblvouchers_coupon',$whereJoin));
        return $sum;
    }
    public function sum_order_other($day='',$month='',$year='',$customers_ch='',$data=array(),$where_or='')
    {
        $whereJoin=array();
        if(!empty($day))
        {
            $whereJoin['where']['day(tblother_payslips_coupon.date) = '] = $day;
        }
        if(!empty($month))
        {
            $whereJoin['where']['month(tblother_payslips_coupon.date) = '] = $month;
        }
        if(!empty($year))
        {
            $whereJoin['where']['year(tblother_payslips_coupon.date) = '] = $year;
        }
        if(!empty($customers_ch))
        {
            $whereJoin['where'][] = 'tblother_payslips_coupon.objects_id = '.$customers_ch;
        }
        if(!empty($data['search_id_staff']))
        {
            $whereJoin['where_or'] = '( '.trim($where_or,'or ').' )';
        }
        $whereJoin['where']['objects = '] = 1;
        $whereJoin['where']['type_vouchers = '] = 5;
        $whereJoin['where']['objects_id != '] = 0;
        $whereJoin['join'] = array();
        $whereJoin['field']='total';
        $sum = (sum_from_table_join('tblother_payslips_coupon',$whereJoin));
        return $sum;
    }
    public function dashboard_report($value='')
    {
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $beginMonth = to_sql_date($data['report_from']);
            $endMonth   = to_sql_date($data['report_to']);
            if((strtotime($endMonth)-strtotime($beginMonth)) < 0){
                $_data['labels'] = '';
                $_data['data'] = '';
                $_data['datas_payment'] = '';
                $_data['datas_cost'] = '';
                echo json_encode($_data);die;
            }
        }
        $where_or = '';
        $where_or_pay = '';
        $where_or_other = '';
        if(!empty($data['search_id_staff']))
        {
            foreach ($data['search_id_staff'] as $key => $v) {
                $where_or='(tbl_orders.created_by = '.$v.') or '.$where_or;
                $where_or_pay='(tblvouchers_coupon.staff_create = '.$v.') or '.$where_or_pay;
                $where_or_other='(tblother_payslips_coupon.staff_id = '.$v.') or '.$where_or_other;
            }
        }
        $customers_ch = trim($data['customers_ch'],'customers__');
        if($data['months_report'] == 'this_year')
        {
            $labels[1] =  'Tháng 1';
            $labels[2] =  'Tháng 2';
            $labels[3] =  'Tháng 3';
            $labels[4] =  'Tháng 4';
            $labels[5] =  'Tháng 5';
            $labels[6] =  'Tháng 6';
            $labels[7] =  'Tháng 7';
            $labels[8] =  'Tháng 8';
            $labels[9] =  'Tháng 9';
            $labels[10] =  'Tháng 10';
            $labels[11] =  'Tháng 11';
            $labels[12] =  'Tháng 12';

            foreach ($labels as $key => $value) {
                $datas_payment[$key]= $this->sum_order_pay('',$key,date('Y'),$customers_ch,$data,$where_or_pay);
                $datas_cost[$key] = $this->sum_order_items('',$key,date('Y'),$customers_ch,$data,$where_or)+$this->sum_order_other('',$key,date('Y'),$customers_ch,$data,$where_or_other);
            }
        }elseif($data['months_report'] == 'last_year')
        {
            $labels[1] =  'Tháng 1';
            $labels[2] =  'Tháng 2';
            $labels[3] =  'Tháng 3';
            $labels[4] =  'Tháng 4';
            $labels[5] =  'Tháng 5';
            $labels[6] =  'Tháng 6';
            $labels[7] =  'Tháng 7';
            $labels[8] =  'Tháng 8';
            $labels[9] =  'Tháng 9';
            $labels[10] =  'Tháng 10';
            $labels[11] =  'Tháng 11';
            $labels[12] =  'Tháng 12';
            $prevyear = date('Y', strtotime("last year"));
            foreach ($labels as $key => $value) {
                $datas_payment[$key]= $this->sum_order_pay('',$key,$prevyear,$customers_ch,$data,$where_or_pay);
                $datas_cost[$key] = $this->sum_order_items('',$key,$prevyear,$customers_ch,$data,$where_or)+$this->sum_order_other('',$key,$prevyear,$customers_ch,$data,$where_or_other);
            }
        }elseif($data['months_report'] == 'this_month')
        {
            for ($i=1; $i <= last_day(date('m')) ; $i++) {
                $labels[$i] =  _d(date(date('y').'-'.date('m').'-'.$i));
                $datas_payment[$i]= $this->sum_order_pay($i,date('m'),date('Y'),$customers_ch,$data,$where_or_pay);
                $datas_cost[$i] = $this->sum_order_items($i,date('m'),date('Y'),$customers_ch,$data,$where_or)+$this->sum_order_other($i,date('m'),date('Y'),$customers_ch,$data,$where_or_other);
            }
        }elseif($data['months_report'] == '1')
        {
            $prevmonth = date('m', strtotime("last month"));
            $prevyear = date('Y', strtotime("last month"));
            $prevyeary = date('y', strtotime("last month"));
            for ($i=1; $i <= last_day($prevmonth) ; $i++) {
                $labels[$i] =  _d(date($prevyeary.'-'.$prevmonth.'-'.$i));
                $datas_payment[$i]= $this->sum_order_pay($i,$prevmonth,$prevyear,$customers_ch,$data,$where_or_pay);
                $datas_cost[$i] = $this->sum_order_items($i,$prevmonth,$prevyear,$customers_ch,$data,$where_or)+$this->sum_order_other($i,$prevmonth,$prevyear,$customers_ch,$data,$where_or_other);
            }
        }elseif($data['months_report'] == 'week')
        {
            $day_first = date('Y-m-d',strtotime('this week', time()));

            for ($i=0; $i <= 6 ; $i++) {
                $week = strtotime(date("Y-m-d", strtotime($day_first)) . '+'.$i.' day');
                $week = strftime("%Y-%m-%d", $week);
                $labels[$i] =  _d($week);
                $datas_payment[$i]= $this->sum_order_pay(date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)),$customers_ch,$data,$where_or_pay);
                $datas_cost[$i] = $this->sum_order_items(date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)),$customers_ch,$data,$where_or)+$this->sum_order_other(date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)),$customers_ch,$data,$where_or_other);
            }
        }elseif($data['months_report'] == 'custom')
        {
            $beginMonth = to_sql_date($data['report_from']);
            $endMonth   = to_sql_date($data['report_to']);
            if(date('Y', strtotime($beginMonth)) == date('Y', strtotime($endMonth)))
            {
                if(date('m', strtotime($beginMonth)) == date('m', strtotime($endMonth)))
                {
                        $j = 0;
                        for ($i=date('d', strtotime($beginMonth)); $i <= date('d', strtotime($endMonth)) ; $i++) {

                            $labels[$j] =  _d(date(date('y', strtotime($endMonth)).'-'.date('m', strtotime($endMonth)).'-'.$i));
                            $datas_payment[$j]= $this->sum_order_pay($i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)),$customers_ch,$data,$where_or_pay);
                            $datas_cost[$j] = $this->sum_order_items($i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)),$customers_ch,$data,$where_or)+$this->sum_order_other($i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)),$customers_ch,$data,$where_or_other);
                            $j++;
                    }
                }else
                {
                    $j = 0;
                    for ($i=date('m', strtotime($beginMonth)); $i <= date('m', strtotime($endMonth)) ; $i++) {
                        $labels[$j] =  'Tháng '.$i;
                        $datas_payment[$j]= $this->sum_order_pay('',$i,date('Y', strtotime($endMonth)),$customers_ch,$data,$where_or_pay);
                        $datas_cost[$j] = $this->sum_order_items('',$i,date('Y', strtotime($endMonth)),$customers_ch,$data,$where_or)+$this->sum_order_other('',$i,date('Y', strtotime($endMonth)),$customers_ch,$data,$where_or_other);
                        $j++;
                    }
                }
            }else
            {
                    $j = 0;
                    for ($i=date('Y', strtotime($beginMonth)); $i <= date('Y', strtotime($endMonth)) ; $i++) {
                        $labels[$j] =  'Năm '.$i;
                        $datas_payment[$j]= $this->sum_order_pay('','',$i,$customers_ch,$data,$where_or_pay);
                        $datas_cost[$j] = $this->sum_order_items('','',$i,$customers_ch,$data,$where_or)+$this->sum_order_other('','',$i,$customers_ch,$data,$where_or_other);
                        $j++;
                    }
            }
        }
        else
        {
            foreach ($this->getYears() as $key => $value) {
                $labels[$key] =  $value['year'];
                $datas_payment[$key]= $this->sum_order_pay('','',$value['year'],$customers_ch,$data,$where_or_pay);
                $datas_cost[$key] = $this->sum_order_items('','',$value['year'],$customers_ch,$data,$where_or)+$this->sum_order_other('','',$value['year'],$customers_ch,$data,$where_or_other);
            }
        }
        $_data['labels'] = $labels;
        $_data['datas_payment'] = $datas_payment;
        $_data['datas_cost'] = $datas_cost;
        echo json_encode($_data);
    }
    public function getYears()
    {
        $this->db->distinct();
        $this->db->select('YEAR(date) as year');
        $this->db->order_by('YEAR(date)');
        $this->db->from('tbl_orders');
        $q=$this->db->get();
        if($q->num_rows()>0)
            return $q->result_array();

        return false;
    }
    public function dashboard_report_cot()
    {
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $begin = to_sql_date($data['report_from']);
            $end   = to_sql_date($data['report_to']);
            if((strtotime($end)-strtotime($begin)) < 0){
                $data['subtotal'] = 0;
                $data['subtotal1'] = 0;
                $data['subtotal2'] = 0;
                echo json_encode($data);die;
            }
        }
        $beginMonth =  '';
        $endMonth   =  '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth   = date('Y-m-d');
            }   elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d',strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth   = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date   = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
        }
            $customers_ch = trim($data['customers_ch'],'customers__');
            $this->db->select('tblclients.company as company,SUM(tbl_orders.grand_total) as grand_totals');
            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tbl_orders.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tbl_orders.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['search_id_staff']))
            {
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tbl_orders.created_by = ',$value);
                }
            }
            if($customers_ch)
            {
                $this->db->where('tbl_orders.customer_id = ',$customers_ch);
            }
            $this->db->join('tblclients','tblclients.userid = tbl_orders.customer_id','left');
            $this->db->group_by('tbl_orders.customer_id');
            $this->db->limit(15);
            $order = $this->db->get('tbl_orders')->result_array();
            usort($order, ch_make_cmp(['grand_totals' => "desc"]));
            $max = 0;
            $_order = array();
            $labels = array();
            foreach ($order as $key => $value) {
                $labels[] = $value['company'];
                $_order[] = $value['grand_totals'];
                if($value['grand_totals'] > $max)
                {
                    $max = $value['grand_totals'];
                }

            }
            $__data['max'] = $max;
            $__data['data'] = $_order;
            $__data['labels'] = $labels;
            echo json_encode($__data);die;
    }
    // SUM ADDITIONAL
    public function top_materials()
    {
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $begin = to_sql_date($data['report_from']);
            $end   = to_sql_date($data['report_to']);
            if((strtotime($end)-strtotime($begin)) < 0){
                $data = '';
                echo json_encode($data);die;
            }
        }
        $beginMonth =  '';
        $endMonth   =  '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth   = date('Y-m-d');
            }   elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d',strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth   = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date   = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
        }
            $this->db->select('
            tbl_materials.id,
            tbl_materials.quantity_minimum as quantity_minimum,
            tbl_materials.name,
            COALESCE(SUM(tblwarehouse_items.product_quantity),0) as product_quantity,
            ');
            $this->db->group_by('tbl_materials.id');
            $this->db->join('tblwarehouse_items','tblwarehouse_items.id_items=tbl_materials.id','left');
            $this->db->having('product_quantity < quantity_minimum');
            $this->db->limit(5);

            $data_quantity = $this->db->get('tbl_materials')->result_array();
            $html ='';
            foreach ($data_quantity as $key => $value) {
                $name = $value['name'];
                $product_quantity = $value['product_quantity'];
                $quantity_minimum = $value['quantity_minimum'];
                $quantity_missing = $quantity_minimum - $product_quantity;
                // '.(strlen($name) > 30 ? ' ...' : "").'
                $html.='<div class="wrap_container">
                            <span style="float:left; width:70%; height: 23px; overflow: hidden;"><span class="wrap_number">'.($key + 1).'.</span> '.($name).'</span>
                            
                            <span style="color: #2e98ff;float: right; width: 30%; font-weight: 500;font-size: 15px; text-align: right;">'.number_format($quantity_missing).'</span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="wrap_line"></div>';
            }
            echo json_encode($html);

    }
    public function top_pay_slips()
    {
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $begin = to_sql_date($data['report_from']);
            $end   = to_sql_date($data['report_to']);
            if((strtotime($end)-strtotime($begin)) < 0){
                $data = '';
                echo json_encode($data);die;
            }
        }
        $beginMonth =  '';
        $endMonth   =  '';
        // $months_report = $data['months_report'];
        // if ($months_report != '') {
        //     $custom_date_select = '';
        //     if (is_numeric($months_report)) {
        //         // Last month
        //         if ($months_report == '1') {
        //             $beginMonth = date('Y-m-01', strtotime('first day of last month'));
        //             $endMonth   = date('Y-m-t', strtotime('last day of last month'));
        //         } else {
        //             $months_report = (int) $months_report;
        //             $months_report--;
        //             $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
        //             $endMonth   = date('Y-m-t');
        //         }
        //     } elseif ($months_report == 'day') {
        //         $beginMonth = date('Y-m-d');
        //         $endMonth   = date('Y-m-d');
        //     }   elseif ($months_report == 'week') {
        //         $beginMonth = date('Y-m-d',strtotime('this week', time()));
        //         $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
        //         $endMonth = strftime("%Y-%m-%d", $week);
        //     } elseif ($months_report == 'this_month') {
        //         $beginMonth = date('Y-m-01');
        //         $endMonth   = date('Y-m-t');
        //     } elseif ($months_report == 'this_year') {
        //         $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
        //         $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
        //     } elseif ($months_report == 'last_year') {
        //         $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
        //         $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
        //     } elseif ($months_report == 'custom') {
        //         $from_date = to_sql_date($data['report_from']);
        //         $to_date   = to_sql_date($data['report_to']);
        //         if ($from_date == $to_date) {
        //             $beginMonth =  $to_date;
        //             $endMonth   =  $to_date;
        //         } else {
        //             $beginMonth =  $from_date;
        //             $endMonth   =  $to_date;
        //         }
        //     }
        // }
        $this->db->select('
        tblpay_slip.id,
        tblpay_slip.payment as total,
        tblsuppliers.company as name,
        ');
        $this->db->group_by('tblpay_slip.id');
        $this->db->join('tblsuppliers','tblsuppliers.id=tblpay_slip.id_supplierss','left');
        $this->db->order_by('total','DESC');
        $this->db->limit(5);

        $data_quantity = $this->db->get('tblpay_slip')->result_array();
        $html ='';
        foreach ($data_quantity as $key => $value) {
            $total = $value['total'];
            $name = $value['name'];
            // '.(strlen($total) > 30 ? ' ...' : "").'
            $html.='<div class="wrap_container" >
                        <span style="float:left; height: 23px; width:70%; overflow: hidden;"><span class="number_top_'.($key + 1).'">Top '.($key + 1).': </span> '.($name).'</span>
                        
                        <span style="color: #2e98ff;float: right; width: 30%; font-weight: 500;font-size: 15px; text-align: right;">'.number_format($total).'</span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="wrap_line"></div>';
        }
        echo json_encode($html);
    }
    public function top_supplier_imports()
    {
        $data = $this->input->post();
        if(!empty($data['report_from'])&&!empty($data['report_from'])){
            $begin = to_sql_date($data['report_from']);
            $end   = to_sql_date($data['report_to']);
            if((strtotime($end)-strtotime($begin)) < 0){
                $data = '';
                echo json_encode($data);die;
            }
        }
        $beginMonth =  '';
        $endMonth   =  '';
        $months_report = $data['months_report'];
        if ($months_report != '') {
            $custom_date_select = '';
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }
            } elseif ($months_report == 'day') {
                $beginMonth = date('Y-m-d');
                $endMonth   = date('Y-m-d');
            }   elseif ($months_report == 'week') {
                $beginMonth = date('Y-m-d',strtotime('this week', time()));
                $week = strtotime(date("Y-m-d", strtotime($beginMonth)) . '+6 day');
                $endMonth = strftime("%Y-%m-%d", $week);
            } elseif ($months_report == 'this_month') {
                $beginMonth = date('Y-m-01');
                $endMonth   = date('Y-m-t');
            } elseif ($months_report == 'this_year') {
                $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
            } elseif ($months_report == 'last_year') {
                $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
                $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($data['report_from']);
                $to_date   = to_sql_date($data['report_to']);
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
        }
        $this->db->select('
        tblpurchase_order.id,
        tblsuppliers.company as name,
        COALESCE(SUM(tblpurchase_order.totalAll_suppliers),0) as totalcurrency,
        COALESCE(SUM(tblpurchase_order.total_dqd), 0) as total,
        ');
        $this->db->group_by('tblsuppliers.company');
        $this->db->join('tblsuppliers','tblsuppliers.id=tblpurchase_order.suppliers_id','left');
        $this->db->join('tblcurrencies','tblcurrencies.id=tblpurchase_order.currency','left');
        $this->db->order_by('total','DESC');
        $this->db->limit(5);
        $data_quantity = $this->db->get('tblpurchase_order')->result_array();
        $html ='';
        foreach ($data_quantity as $key => $value) {
            $total = $value['total'];
            $name = $value['name'];
            // '.(strlen($total) > 30 ? ' ...' : "").'
            $html.='<div class="wrap_container">
                        <span style="float:left; width:70%; height: 23px; overflow: hidden;"><span class="number_top_'.($key + 1).'">Top '.($key + 1).': </span> '.($name).'</span>
                        
                        <span style="color: #2e98ff;float: right; width: 30%; font-weight: 500;font-size: 15px; text-align: right;">'.number_format($total).'</span>
                        <div class="clearfix"></div>
                    </div>
                    <div class="wrap_line"></div>';
        }
        echo json_encode($html);
    }
}
