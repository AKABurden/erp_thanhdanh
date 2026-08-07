<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard_purchaser extends AdminController
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
        $this->load->view('admin/dashboard_purchaser/manage', $data);
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
            
            $whereJoin_order=array();
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_order['where']=array(
                  'tblpurchase_order.date >=' =>$beginMonth.' 00:00:00',
                  'tblpurchase_order.date <=' =>$endMonth.' 23:59:59',
                );
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tblpurchase_order.staff_create = '.$value.') or '.$where_or;   
                }
                $whereJoin_order['where_or'] = '( '.trim($where_or,'or ').' )';
            }

            $whereJoin_order['join']=array();
            $whereJoin_order['field']='totalAll_suppliers';
            $order = (sum_from_table_join('tblpurchase_order',$whereJoin_order));

            $whereJoin_import=array();
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_import['where']=array(
                  'tblimport.date >=' =>$beginMonth.' 00:00:00',
                  'tblimport.date <=' =>$endMonth.' 23:59:59',
                );
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tblimport.staff_create = '.$value.') or '.$where_or;   
                }
                $whereJoin_import['where_or'] = '( '.trim($where_or,'or ').' )';
            }
            $whereJoin_import['join']=array();
            $whereJoin_import['field']='total';
            $_import = (sum_from_table_join('tblimport',$whereJoin_import));


            $this->db->select('count(*) as count');
            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tblpurchases.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tblpurchases.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tblimport.staff_create = ',$value);
                }
            }
            $count_purchases = $this->db->get('tblpurchases')->row();
            $count_purchasess = 0 ;
            if(!empty($count_purchases))
            {
                $count_purchasess = $count_purchases->count;
            }

            $this->db->select('count(*) as count');
            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tblpurchase_order.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tblpurchase_order.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tblpurchase_order.staff_create = ',$value);
                }
            }
            $count_order = $this->db->get('tblpurchase_order')->row();
            $count_orders = 0 ;
            if(!empty($count_order))
            {
                $count_orders = $count_order->count;
            }

            $this->db->select('count(*) as count');
            if(!empty($beginMonth)&&$endMonth)
            {
                $this->db->where('tblimport.date >=',$beginMonth.' 00:00:00');
                $this->db->where('tblimport.date <=',$endMonth.' 23:59:59');
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $this->db->or_where('tblimport.staff_create = ',$value);
                }
            }
            $count_import = $this->db->get('tblimport')->row();
            $count_imports = 0 ;
            if(!empty($count_import))
            {
                $count_imports = $count_import->count;
            }
            $data['subtotal'] = number_format($count_purchasess).' Đơn';
            $data['subtotal1'] = number_format($count_orders).' Đơn ('.number_format($order).')';
            $data['subtotal2'] = number_format($count_imports).' Đơn ('.number_format($_import).')';
            $data['date_update'] = "Updated "._dt(date('Y-m-d H:i:s'));
            echo json_encode($data);
    }
    public function sum_purchaser_items($day='',$month='',$year='',$type_items='',$data=array(),$where_or='')
    {
        $whereJoin=array();
        $whereJoin['where']['type'] = $type_items;
        if(!empty($day))
        {
            $whereJoin['where']['day(tblpurchase_order.date) = '] = $day;
        }
        if(!empty($month))
        {
            $whereJoin['where']['month(tblpurchase_order.date) = '] = $month; 
        }
        if(!empty($year))
        {
            $whereJoin['where']['year(tblpurchase_order.date) = '] = $year; 
        }

        if(!empty($data['search_id_staff']))
        {   
            $whereJoin['where_or'] = '( '.trim($where_or,'or ').' )';
        }
        $whereJoin['join'] = array('tblpurchase_order,tblpurchase_order.id=tblpurchase_order_items.id_purchase_order,left');
        $whereJoin['field']='total_suppliers';
        $sum = (sum_from_table_join('tblpurchase_order_items',$whereJoin));
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
        if(!empty($data['search_id_staff']))
        {  
        foreach ($data['search_id_staff'] as $key => $v) {
            $where_or='(tblpurchase_order.staff_create = '.$v.') or '.$where_or;   
        }
        }
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
                $datas_payment[$key]= $this->sum_purchaser_items('',$key,date('Y'),'nvl',$data,$where_or);
                $datas_cost[$key] = $this->sum_purchaser_items('',$key,date('Y'),'product',$data,$where_or);
                $datas[$key] = $this->sum_purchaser_items('',$key,date('Y'),'tools',$data,$where_or);
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
                $datas_payment[$key]= $this->sum_purchaser_items('',$key,$prevyear,'nvl',$data,$where_or);
                $datas_cost[$key] = $this->sum_purchaser_items('',$key,$prevyear,'product',$data,$where_or);
                $datas[$key] = $this->sum_purchaser_items('',$key,$prevyear,'tools',$data,$where_or);
            }
        }elseif($data['months_report'] == 'this_month')
        {
            for ($i=1; $i <= last_day(date('m')) ; $i++) { 
                $labels[$i] =  _d(date(date('y').'-'.date('m').'-'.$i));
                $datas_payment[$i]= $this->sum_purchaser_items($i,date('m'),date('Y'),'nvl',$data,$where_or);
                $datas_cost[$i] = $this->sum_purchaser_items($i,date('m'),date('Y'),'product',$data,$where_or);
                $datas[$i] = $this->sum_purchaser_items($i,date('m'),date('Y'),'tools',$data,$where_or);
            }
        }elseif($data['months_report'] == '1')
        {
            $prevmonth = date('m', strtotime("last month"));
            $prevyear = date('Y', strtotime("last month"));
            $prevyeary = date('y', strtotime("last month"));
            for ($i=1; $i <= last_day($prevmonth) ; $i++) { 
                $labels[$i] =  _d(date($prevyeary.'-'.$prevmonth.'-'.$i));
                $datas_payment[$i]= $this->sum_purchaser_items($i,$prevmonth,$prevyear,'nvl',$data,$where_or);
                $datas_cost[$i] = $this->sum_purchaser_items($i,$prevmonth,$prevyear,'product',$data,$where_or);
                $datas[$i] = $this->sum_purchaser_items($i,$prevmonth,$prevyear,'tools',$data,$where_or);
            }
        }elseif($data['months_report'] == 'week')
        {
            $day_first = date('Y-m-d',strtotime('this week', time()));

            for ($i=0; $i <= 6 ; $i++) { 
                $week = strtotime(date("Y-m-d", strtotime($day_first)) . '+'.$i.' day');
                $week = strftime("%Y-%m-%d", $week);
                $labels[$i] =  _d($week);
                $datas_payment[$i]= $this->sum_purchaser_items(date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)),'nvl',$data,$where_or);
                $datas_cost[$i] = $this->sum_purchaser_items(date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)),'product',$data,$where_or);
                $datas[$i] = $this->sum_purchaser_items(date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)),'tools',$data,$where_or);
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
                            $datas_payment[$j]= $this->sum_purchaser_items($i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)),'nvl',$data,$where_or);
                            $datas_cost[$j] = $this->sum_purchaser_items($i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)),'product',$data,$where_or);
                            $datas[$j] = $this->sum_purchaser_items($i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)),'tools',$data,$where_or);
                            $j++;
                    }
                }else
                {
                    $j = 0;
                    for ($i=date('m', strtotime($beginMonth)); $i <= date('m', strtotime($endMonth)) ; $i++) { 
                        $labels[$j] =  'Tháng '.$i;
                        $datas_payment[$j]= $this->sum_purchaser_items('',$i,date('Y', strtotime($endMonth)),'nvl',$data,$where_or);
                        $datas_cost[$j] = $this->sum_purchaser_items('',$i,date('Y', strtotime($endMonth)),'product',$data,$where_or);
                        $datas[$j] = $this->sum_purchaser_items('',$i,date('Y', strtotime($endMonth)),'tools',$data,$where_or);
                        $j++;
                    }
                }
            }else
            {
                    $j = 0;
                    for ($i=date('Y', strtotime($beginMonth)); $i <= date('Y', strtotime($endMonth)) ; $i++) { 
                        $labels[$j] =  'Năm '.$i;
                        $datas_payment[$j]= $this->sum_purchaser_items('','',$i,'nvl',$data,$where_or);
                        $datas_cost[$j] = $this->sum_purchaser_items('','',$i,'product',$data,$where_or);
                        $datas[$j] = $this->sum_purchaser_items('','',$i,'tools',$data,$where_or);
                        $j++;
                    }
            }
        }
        else
        {
            foreach ($this->getYears() as $key => $value) {
                $labels[$key] =  $value['year'];
                $datas_payment[$key]= $this->sum_purchaser_items('','',$value['year'],'nvl',$data,$where_or);
                $datas_cost[$key] = $this->sum_purchaser_items('','',$value['year'],'product',$data,$where_or);
                $datas[$key] = $this->sum_purchaser_items('','',$value['year'],'tools',$data,$where_or);
            }
        }
        $_data['labels'] = $labels;
        $_data['data'] = $datas;
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
            $whereJoin_order_hd=array();
            $whereJoin_order_hd['where']['tblpurchase_order.type_plan !='] = 0; 
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_order_hd['where']['tblpurchase_order.date >='] = $beginMonth.' 00:00:00'; 
                $whereJoin_order_hd['where']['tblpurchase_order.date <='] = $endMonth.' 23:59:59'; 
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tblpurchase_order.staff_create = '.$value.') or '.$where_or;   
                }
                $whereJoin_order_hd['where_or'] = '( '.trim($where_or,'or ').' )';
            }

            $whereJoin_order_hd['join']=array();
            $whereJoin_order_hd['field']='totalAll_suppliers';
            $order_hd = (sum_from_table_join('tblpurchase_order',$whereJoin_order_hd));
            if(empty($order_hd))
            {
                $order_hd = 0;
            }
            $whereJoin_order=array();
            $whereJoin_order['where']['tblpurchase_order.type_plan ='] = 0; 
            if(!empty($beginMonth)&&$endMonth)
            {
                $whereJoin_order['where']['tblpurchase_order.date >='] = $beginMonth.' 00:00:00'; 
                $whereJoin_order['where']['tblpurchase_order.date <='] = $endMonth.' 23:59:59'; 
            }
            if(!empty($data['search_id_staff']))
            {   
                $where_or = '';
                foreach ($data['search_id_staff'] as $key => $value) {
                    $where_or='(tblpurchase_order.staff_create = '.$value.') or '.$where_or;   
                }
                $whereJoin_order['where_or'] = '( '.trim($where_or,'or ').' )';
            }

            $whereJoin_order['join']=array();
            $whereJoin_order['field']='totalAll_suppliers';
            $order = (sum_from_table_join('tblpurchase_order',$whereJoin_order));    
            if(empty($order))
            {
                $order = 0;
            }
            $labels[0] = 'LƯỢNG MUA SẢN XUẤT';
            $labels[1] = 'LƯỢNG MUA DỮ TRỮ';
            $_data[0] = $order_hd;
            $_data[1] = $order;
            $backgroundColor[0] = '#3e95cd';
            $backgroundColor[1] = '#8e5ea2';
                $max = $order_hd;
                if($order > $order_hd)
                {
                    $max = $order;
                }
                $__data['max'] = $max;
                $__data['data'] = $_data;
                $__data['labels'] = $labels;
                $__data['backgroundColor'] = $backgroundColor;
                echo json_encode($__data);die;
        }
}