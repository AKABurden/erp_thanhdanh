<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_order extends AdminController
{
    public function __construct()
    {
        parent::__construct();
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
        $data['title'] = _l('ch_dashboard_purchaser');
        $this->load->view('admin/dashboard_order/manage', $data);
    }
    public function count_all()
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
            $data['customers_ch'] = trim($data['customers_ch'],'customers__'); 
            $whereJoin_quote=array();
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_quote['where']=array(
                  'tbl_quotes.date >=' =>$beginMonth.' 00:00:00',
                  'tbl_quotes.date <=' =>$endMonth.' 23:59:59',
                );
            }
            if(!empty($data['customers_ch']))
            {   
                $whereJoin_quote['where'][] = 'tbl_quotes.customer_id = '.$data['customers_ch'];
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tbl_quotes.created_by = '.$value.') or '.$where_or;   
                }
                $whereJoin_quote['where_or'] = '( '.trim($where_or,'or ').' )';
            }
            $whereJoin_quote['join']=array();
            $whereJoin_quote['field']='grand_total';
            $_quote = (sum_from_table_join('tbl_quotes',$whereJoin_quote));

            $this->db->select('count(*) as count');
            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tbl_quotes.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tbl_quotes.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['customers_ch']))
            {   
                $this->db->where('tbl_quotes.customer_id ',$data['customers_ch']);
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tbl_quotes.created_by = ',$value);
                }
            }
            $count_quote = $this->db->get('tbl_quotes')->row();
            $count_quotes = 0 ;
            if(!empty($count_quote))
            {
                $count_quotes = $count_quote->count;
            }


            $whereJoin_order=array();
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_order['where']=array(
                  'tbl_orders.date >=' =>$beginMonth.' 00:00:00',
                  'tbl_orders.date <=' =>$endMonth.' 23:59:59',
                );
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tbl_orders.created_by = '.$value.') or '.$where_or;   
                }
                $whereJoin_order['where_or'] = '( '.trim($where_or,'or ').' )';
            }
            if(!empty($data['customers_ch']))
            {   
                $whereJoin_order['where'][] = 'tbl_orders.customer_id = '.$data['customers_ch'];
            }
            $whereJoin_order['join']=array();
            $whereJoin_order['field']='grand_total';
            $order = (sum_from_table_join('tbl_orders',$whereJoin_order));

            $this->db->select('count(*) as count');
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
            $count_order = $this->db->get('tbl_orders')->row();
            $count_orders = 0 ;
            if(!empty($count_order))
            {
                $count_orders = $count_order->count;
            }

            $whereJoin_return=array();
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_return['where']=array(
                  'tbl_returned_goods.date >=' =>$beginMonth.' 00:00:00',
                  'tbl_returned_goods.date <=' =>$endMonth.' 23:59:59',
                );
            }
            if(!empty($data['customers_ch']))
            {   
                $whereJoin_return['where'][] = 'tbl_returned_goods.customer_id = '.$data['customers_ch'];
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tbl_returned_goods.created_by = '.$value.') or '.$where_or;   
                }
                $whereJoin_return['where_or'] = '( '.trim($where_or,'or ').' )';
            }
            if(!empty($data['customers_ch']))
            {   
                $this->db->where('tbl_returned_goods.customer_id ',$data['customers_ch']);
            }
            $whereJoin_return['join']=array();
            $whereJoin_return['field']='grand_total';
            $return = (sum_from_table_join('tbl_returned_goods',$whereJoin_return));

            $this->db->select('count(*) as count');
            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tbl_returned_goods.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tbl_returned_goods.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tbl_returned_goods.created_by = ',$value);
                }
            }
            $count_return = $this->db->get('tbl_returned_goods')->row();
            $count_returns = 0 ;
            if(!empty($count_return))
            {
                $count_returns = $count_return->count;
            }

            $data['quote'] = number_format($count_quotes).' Đơn ('.number_format($_quote).')';
            $data['orders'] = number_format($count_orders).' Đơn ('.number_format($order).')';
            $data['returns'] = number_format($count_returns).' Đơn ('.number_format($return).')';
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
}