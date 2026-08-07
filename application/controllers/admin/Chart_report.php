<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Chart_report extends AdminController
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('suppliers_model');
        $this->load->model('currencies_model');
        $this->load->model('dashboard_model');
        $this->load->model('purchases_model');
    }
    public function count_all_chart()
    {
        $data = $this->input->post();
        $suppliers_id = $data['suppliers_ids'];
       
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
        		//Don Po
        		
        	$subtotal = $this->sum_debt_suppliert('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$beginMonth,$endMonth,$suppliers_id);
        	$amount_paid = $this->sum_debt_suppliert('amount_paid','amount_paid','amount_paid','',$beginMonth,$endMonth,$suppliers_id);
        	$price_other_expenses = $this->sum_debt_suppliert('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$beginMonth,$endMonth,$suppliers_id);
            $data['subtotal'] = number_format($subtotal);
            $data['subtotal1'] = number_format($amount_paid + $price_other_expenses);
            $data['subtotal2'] = number_format($subtotal - $amount_paid - $price_other_expenses);
            $data['date_update'] = "Updated "._dt(date('Y-m-d H:i:s'));
            echo json_encode($data);
    }
    public function sum_debt_suppliert($field_purchase_order='',$field_purchase_invoice='',$field_outsource='',$field_delivery='',$beginMonth='',$endMonth='',$suppliers_id='')
    {			
    			$subtotal_purchase_order = 0; 
    			$subtotal_purchase_invoice = 0; 
    			$subtotal_outsource = 0; 
    			$subtotal_delivery = 0; 
    			if(!empty($field_purchase_order))
    			{
	    			$whereJoin_purchase_order=array();
	                $whereJoin_purchase_order['where']=array(
	                  
	                );
	                if(!empty($beginMonth)&&$endMonth)
		            {
		                $whereJoin_purchase_order['where'][] = array('tblpurchase_order.date >=' => $beginMonth.' 00:00:00');
		                $whereJoin_purchase_order['where'][] = array('tblpurchase_order.date <=' => $endMonth.' 23:59:59');
		            }
	                if(!empty($suppliers_id))
		            {
		                $whereJoin_purchase_order['where'][] = array('suppliers_id = ' =>$suppliers_id);
		            }
		            $whereJoin_purchase_order['where'][] = 'tblpurchase_order.id IN(select id_order from tblimport)';
		            $whereJoin_purchase_order['where'][] = array('tblpurchase_order.red_invoice = ' => 0);
	                $whereJoin_purchase_order['join'] = array('tblsuppliers,tblsuppliers.id=tblpurchase_order.suppliers_id,left');
	                // $whereJoin_purchase_order['field'] = 'totalAll_suppliers';
	                $whereJoin_purchase_order['field'] = $field_purchase_order;
	                $subtotal_purchase_order = (sum_from_table_join('tblpurchase_order',$whereJoin_purchase_order));
	            }
                //Hóa đơn
                if(!empty($field_purchase_invoice))
    			{
	                $whereJoin_purchase_invoice=array();
	                $whereJoin_purchase_invoice['where']=array(
	                  
	                );
	                if(!empty($beginMonth)&&$endMonth)
		            {
		                $whereJoin_purchase_invoice['where'][] = array('tblpurchase_invoice.date_invoice >=' => $beginMonth.' 00:00:00');
		                $whereJoin_purchase_invoice['where'][] = array('tblpurchase_invoice.date_invoice <=' => $endMonth.' 23:59:59');
		            }
	                if(!empty($suppliers_id))
		            {
		                $whereJoin_purchase_invoice['where'][] = array('id_supplier = ' =>$suppliers_id);
		            }
                    $whereJoin_purchase_invoice['where'][] = array('tblpurchase_order.red_invoice != ' => 0);

                    $whereJoin_purchase_invoice['where'][] = 'tblpurchase_order.id IN(select id_order from tblimport)';
		            $whereJoin_purchase_invoice['where'][] = array('tblsuppliers.type = ' => 0);
	                $whereJoin_purchase_invoice['join'][] = 'tblsuppliers,tblsuppliers.id=tblpurchase_invoice.id_supplier,left';
                    $whereJoin_purchase_invoice['join'][] = 'tblpurchase_order,tblpurchase_order.red_invoice=tblpurchase_invoice.id,left';

	                // $whereJoin_purchase_invoice['field'] = 'total_price_befor_vat';
	                $whereJoin_purchase_invoice['field'] = 'tblpurchase_invoice.'.$field_purchase_invoice;
	                $subtotal_purchase_invoice = (sum_from_table_join('tblpurchase_invoice',$whereJoin_purchase_invoice));

	            }

                // Xuất gia công
                if(!empty($field_outsource))
    			{
	                $whereJoin_outsource=array();
	                $whereJoin_outsource['where']=array(
	                  
	                );
	                if(!empty($beginMonth)&&$endMonth)
		            {
		                $whereJoin_outsource['where'][] = array('tbl_outsource.date >=' => $beginMonth.' 00:00:00');
		                $whereJoin_outsource['where'][] = array('tbl_outsource.date <=' => $endMonth.' 23:59:59');
		            }
	                if(!empty($suppliers_id))
		            {
		                $whereJoin_outsource['where'][] = array('supplier_id = ' =>$suppliers_id);
		            }
		            $whereJoin_outsource['where'][] = array('tblsuppliers.type = ' => 0);
		            $whereJoin_outsource['where'][] = array('tbl_outsource.status = ' => "approved");
	                $whereJoin_outsource['join'] = array('tblsuppliers,tblsuppliers.id=tbl_outsource.supplier_id,left');
	                // $whereJoin_outsource['field'] = 'grand_total';
	                $whereJoin_outsource['field'] = $field_outsource;
	                $subtotal_outsource = (sum_from_table_join('tbl_outsource',$whereJoin_outsource));
	            }

                // Xuất gia công
                if(!empty($field_delivery))
    			{
	                $whereJoin_delivery=array();
	                $whereJoin_delivery['where']=array(
	                  
	                );
	                if(!empty($beginMonth)&&$endMonth)
		            {
		                $whereJoin_delivery['where'][] = array('tbl_orders.date >=' => $beginMonth.' 00:00:00');
		                $whereJoin_delivery['where'][] = array('tbl_orders.date <=' => $endMonth.' 23:59:59');
		            }
	                if(!empty($suppliers_id))
		            {
		                $whereJoin_delivery['where'][] = array('transporter_id = ' =>$suppliers_id);
		            }
		            $whereJoin_delivery['where'][] = array('tblsuppliers.type = ' => 1);
		            $whereJoin_delivery['where'][] = array('tbl_orders.status = ' => "approved");
	                $whereJoin_delivery['join'] = array('tblsuppliers,tblsuppliers.id=tbl_orders.transporter_id,left');
	                // $whereJoin_delivery['field'] = 'cost_delivery';
	                $whereJoin_delivery['field'] = $field_delivery;
	                $subtotal_delivery = (sum_from_table_join('tbl_orders',$whereJoin_delivery));
	            }
                $subtotal = $subtotal_purchase_order + $subtotal_purchase_invoice + $subtotal_outsource + $subtotal_delivery;
                return $subtotal;
    }
    public function dashboard_report_cot()
        {
            $data = $this->input->post();
            $suppliers_id = $data['suppliers_ids'];
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
            $items = $this->debt_suppliersss($beginMonth,$endMonth,$suppliers_id);
            
            $max = 0;
            $_data =array();
            $labels =array();
            foreach ($items as $key => $value) {
                if($value['total_import'] > $max)
                {
                    $max = $value['total_import'];
                }
               
                $labels[] = $value['company'];
                $_data[$key] = $value['total_import'];
            }
            $__data['max'] = $max;
            $__data['data'] = $_data;
            $__data['labels'] = $labels;
            echo json_encode($__data);die;
        }
    public function debt_suppliersss($beginMonth='',$endMonth='',$suppliers_id='')
    {
        $aColumns = [
        'tblsuppliers.id',
        'tblsuppliers.company as company',
        '(COALESCE(SUM(tblpurchase_order.totalAll_suppliers),0)+COALESCE((select sum(tbl_outsource.grand_total) from tbl_outsource where tbl_outsource.supplier_id=tblsuppliers.id AND tbl_outsource.status_pay < 2 ),0)) as total_import',
        '(COALESCE(SUM(tblpurchase_order.amount_paid),0)+ COALESCE(SUM(tblpurchase_order.price_other_expenses),0) + COALESCE(SUM(tblpurchase_invoice.amount_paid),0)+COALESCE((select sum(tbl_outsource.amount_paid) from tbl_outsource where tbl_outsource.supplier_id=tblsuppliers.id AND tbl_outsource.status_pay < 2 ),0))  as amount_paid_import',
        'SUM(tblpurchase_order.price_other_expenses) as price_other_expenses_import',
        '7',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblsuppliers';
    $where        = [];
    $having = '';
    $filter = [];
    $join = [
        'LEFT JOIN tblpurchase_order ON tblpurchase_order.suppliers_id=tblsuppliers.id',
        'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id=tblpurchase_order.red_invoice',
    ];
    array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
    array_push($where, 'AND tblsuppliers.type = 0');
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where, 'AND tblpurchase_order.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where, 'AND tblpurchase_order.date <='.'"'.$endMonth.' 23:59:59"');
    }
    if(!empty($suppliers_id))
    {
        array_push($where, 'AND tblsuppliers.id ='.$suppliers_id);  
    }
    $group_by = 'GROUP BY tblsuppliers.id';
    $result = data_tables_init_having_v2($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblsuppliers.type',

    ],'',$group_by,$having);
    $output  = $result['output'];
    $rResult = $result['rResult'];


    $aColumns_order = [
        'tblsuppliers.id',
        'tblsuppliers.company as company',
        'SUM(tbl_orders.cost_delivery) as total_import',
        '0 as amount_paid_import',
        'SUM(tbl_orders.price_other_expenses_delivery) as price_other_expenses_import',
        '7',
    ];
    $sIndexColumn_order = 'id';
    $sTable_order       = 'tblsuppliers';
    $where_order        = [];
    $having_order = '';
    $filter_order = [];
    $join_order = [
        'LEFT JOIN tbl_orders ON tbl_orders.transporter_id=tblsuppliers.id',
    ];
    if(!empty($suppliers_id))
    {
        array_push($where_order, 'AND tblsuppliers.id ='.$suppliers_id);  
    }
    array_push($where_order, 'AND tbl_orders.status = "approved"');
    array_push($where_order, 'AND tblsuppliers.type = 1');
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($where_order, 'AND tbl_orders.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($where_order, 'AND tbl_orders.date <='.'"'.$endMonth.' 23:59:59"');
    }
    $group_by_order = 'GROUP BY tblsuppliers.id';
    $result_order = data_tables_init_having_v2($aColumns_order, $sIndexColumn_order, $sTable_order, $join_order, $where_order, ['tblsuppliers.type',

    ],'',$group_by_order,$having_order);
    $output_order  = $result_order['output'];
    $rResult_order = $result_order['rResult'];


    $aColumnsoutsource = [
        'tblsuppliers.id',
        'tblsuppliers.company  as company',
        'SUM(tbl_outsource.grand_total) as total_import',
        '(COALESCE(SUM(tbl_outsource.amount_paid),0)) as amount_paid_import',
        '0 as price_other_expenses_import',
        '7',
    ];
    $sIndexColumnoutsource = 'id';
    $sTableoutsource       = 'tblsuppliers';
    $whereoutsource        = [];
    $havingoutsource = '';
    $filteroutsource = [];
    $joinoutsource = [
        'LEFT JOIN tbl_outsource ON tbl_outsource.supplier_id=tblsuppliers.id',
    ];
    if(!empty($suppliers_id))
    {
        array_push($whereoutsource, 'AND tblsuppliers.id ='.$suppliers_id);  
    }
    array_push($whereoutsource, 'AND tbl_outsource.status = "approved"');
    array_push($whereoutsource, 'AND tblsuppliers.type = 0');
    if(!empty($beginMonth)&&!empty($endMonth))
    {
        array_push($whereoutsource, 'AND tbl_outsource.date >='.'"'.$beginMonth.' 00:00:00"');  
        array_push($whereoutsource, 'AND tbl_outsource.date <='.'"'.$endMonth.' 23:59:59"');
    }
    $group_byoutsource = 'GROUP BY tblsuppliers.id';
    $resultoutsource = data_tables_init_having_v2($aColumnsoutsource, $sIndexColumnoutsource, $sTableoutsource, $joinoutsource, $whereoutsource, ['tblsuppliers.type',

    ],'',$group_byoutsource,$havingoutsource);
    $outputoutsource  = $resultoutsource['output'];
    $rResultoutsource = $resultoutsource['rResult'];

    $debt_total = 0;
    if(empty($rResult)){
        $rResult=array_merge($rResult,$rResultoutsource);
    }
    $rResult=array_merge($rResult,$rResult_order);

    usort($rResult, ch_make_cmp(['total_import' => "desc"]));
   
    return $rResult;
}
    public function sum_debt_suppliert_order($field_purchase_order='',$field_purchase_invoice='',$field_outsource='',$field_delivery='',$suppliers_id='',$day='',$month='',$year='')
    {           
                $subtotal_purchase_order = 0; 
                $subtotal_purchase_invoice = 0; 
                $subtotal_outsource = 0; 
                $subtotal_delivery = 0; 
                if(!empty($field_purchase_order))
                {
                    $whereJoin_purchase_order=array();
                    $whereJoin_purchase_order['where']=array(
                      
                    );
                    if(!empty($day))
                    {
                        $whereJoin_purchase_order['where']['day(tblpurchase_order.date) = '] = $day;
                    }
                    if(!empty($month))
                    {
                        $whereJoin_purchase_order['where']['month(tblpurchase_order.date) = '] = $month; 
                    }
                    if(!empty($year))
                    {
                        $whereJoin_purchase_order['where']['year(tblpurchase_order.date) = '] = $year; 
                    }
                    if(!empty($suppliers_id))
                    {
                        $whereJoin_purchase_order['where'][] = array('suppliers_id = ' =>$suppliers_id);
                    }
                    $whereJoin_purchase_order['where'][] = 'tblpurchase_order.id IN(select id_order from tblimport)';
                    $whereJoin_purchase_order['where'][] = array('tblpurchase_order.red_invoice = ' => 0);
                    $whereJoin_purchase_order['join'] = array('tblsuppliers,tblsuppliers.id=tblpurchase_order.suppliers_id,left');
                    // $whereJoin_purchase_order['field'] = 'totalAll_suppliers';
                    $whereJoin_purchase_order['field'] = $field_purchase_order;
                    $subtotal_purchase_order = (sum_from_table_join('tblpurchase_order',$whereJoin_purchase_order));
                }
                //Hóa đơn
                if(!empty($field_purchase_invoice))
                {
                    $whereJoin_purchase_invoice=array();
                    $whereJoin_purchase_invoice['where']=array(
                      
                    );
                    if(!empty($day))
                    {
                        $whereJoin_purchase_invoice['where']['day(tblpurchase_invoice.date_invoice) = '] = $day;
                    }
                    if(!empty($month))
                    {
                        $whereJoin_purchase_invoice['where']['month(tblpurchase_invoice.date_invoice) = '] = $month; 
                    }
                    if(!empty($year))
                    {
                        $whereJoin_purchase_invoice['where']['year(tblpurchase_invoice.date_invoice) = '] = $year; 
                    }
                    if(!empty($suppliers_id))
                    {
                        $whereJoin_purchase_invoice['where'][] = array('id_supplier = ' =>$suppliers_id);
                    }
                    $whereJoin_purchase_invoice['where'][] = array('tblpurchase_order.red_invoice != ' => 0);

                    $whereJoin_purchase_invoice['where'][] = 'tblpurchase_order.id IN(select id_order from tblimport)';
                    $whereJoin_purchase_invoice['where'][] = array('tblsuppliers.type = ' => 0);
                    $whereJoin_purchase_invoice['join'][] = 'tblsuppliers,tblsuppliers.id=tblpurchase_invoice.id_supplier,left';
                    $whereJoin_purchase_invoice['join'][] = 'tblpurchase_order,tblpurchase_order.red_invoice=tblpurchase_invoice.id,left';

                    // $whereJoin_purchase_invoice['field'] = 'total_price_befor_vat';
                    $whereJoin_purchase_invoice['field'] = 'tblpurchase_invoice.'.$field_purchase_invoice;
                    $subtotal_purchase_invoice = (sum_from_table_join('tblpurchase_invoice',$whereJoin_purchase_invoice));

                }

                // Xuất gia công
                if(!empty($field_outsource))
                {
                    $whereJoin_outsource=array();
                    $whereJoin_outsource['where']=array(
                      
                    );
                    if(!empty($day))
                    {
                        $whereJoin_outsource['where']['day(tbl_outsource.date) = '] = $day;
                    }
                    if(!empty($month))
                    {
                        $whereJoin_outsource['where']['month(tbl_outsource.date) = '] = $month; 
                    }
                    if(!empty($year))
                    {
                        $whereJoin_outsource['where']['year(tbl_outsource.date) = '] = $year; 
                    }
                    if(!empty($suppliers_id))
                    {
                        $whereJoin_outsource['where'][] = array('supplier_id = ' =>$suppliers_id);
                    }
                    $whereJoin_outsource['where'][] = array('tblsuppliers.type = ' => 0);
                    $whereJoin_outsource['where'][] = array('tbl_outsource.status = ' => "approved");
                    $whereJoin_outsource['join'] = array('tblsuppliers,tblsuppliers.id=tbl_outsource.supplier_id,left');
                    // $whereJoin_outsource['field'] = 'grand_total';
                    $whereJoin_outsource['field'] = $field_outsource;
                    $subtotal_outsource = (sum_from_table_join('tbl_outsource',$whereJoin_outsource));
                }

                // Xuất gia công
                if(!empty($field_delivery))
                {
                    $whereJoin_delivery=array();
                    $whereJoin_delivery['where']=array(
                      
                    );
                    if(!empty($day))
                    {
                        $whereJoin_delivery['where']['day(tbl_orders.date) = '] = $day;
                    }
                    if(!empty($month))
                    {
                        $whereJoin_delivery['where']['month(tbl_orders.date) = '] = $month; 
                    }
                    if(!empty($year))
                    {
                        $whereJoin_delivery['where']['year(tbl_orders.date) = '] = $year; 
                    }
                    if(!empty($suppliers_id))
                    {
                        $whereJoin_delivery['where'][] = array('transporter_id = ' =>$suppliers_id);
                    }
                    $whereJoin_delivery['where'][] = array('tblsuppliers.type = ' => 1);
                    $whereJoin_delivery['where'][] = array('tbl_orders.status = ' => "approved");
                    $whereJoin_delivery['join'] = array('tblsuppliers,tblsuppliers.id=tbl_orders.transporter_id,left');
                    // $whereJoin_delivery['field'] = 'cost_delivery';
                    $whereJoin_delivery['field'] = $field_delivery;
                    $subtotal_delivery = (sum_from_table_join('tbl_orders',$whereJoin_delivery));
                }
                $subtotal = $subtotal_purchase_order + $subtotal_purchase_invoice + $subtotal_outsource + $subtotal_delivery;
                return $subtotal;
    }
    public function dashboard_report($value='')
    {
        $data = $this->input->post();
        $suppliers_id = $data['suppliers_ids'];
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
        $where_or_return_suppliers = '';
        if(!empty($data['search_id_staff']))
        {  
        foreach ($data['search_id_staff'] as $key => $v) {
            $where_or_return_suppliers='(tblreturn_suppliers.staff_create = '.$v.') or '.$where_or_return_suppliers;   
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

                $datas_total[$key] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,'',$key,date('Y'));
                $datas_payment[$key] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,'',$key,date('Y')) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,'',$key,date('Y'));
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
                $datas_total[$key] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,'',$key,$prevyear);
                $datas_payment[$key] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,'',$key,$prevyear) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,'',$key,$prevyear);
            }
        }elseif($data['months_report'] == 'this_month')
        {
            for ($i=1; $i <= last_day(date('m')) ; $i++) { 
                $labels[$i] =  _d(date(date('y').'-'.date('m').'-'.$i));
                $datas_total[$i] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,$i,date('m'),date('Y'));
                $datas_payment[$i] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,$i,date('m'),date('Y')) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,$i,date('m'),date('Y'));
            }
        }elseif($data['months_report'] == '1')
        {
            $prevmonth = date('m', strtotime("last month"));
            $prevyear = date('Y', strtotime("last month"));
            $prevyeary = date('y', strtotime("last month"));
            for ($i=1; $i <= last_day($prevmonth) ; $i++) { 
                $labels[$i] =  _d(date($prevyeary.'-'.$prevmonth.'-'.$i));

                $datas_total[$i] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,$i,$prevmonth,$prevyear);
                $datas_payment[$i] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,$i,$prevmonth,$prevyear) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,$i,$prevmonth,$prevyear);
            }
        }elseif($data['months_report'] == 'week')
        {
            $day_first = date('Y-m-d',strtotime('this week', time()));

            for ($i=0; $i <= 6 ; $i++) { 
                $week = strtotime(date("Y-m-d", strtotime($day_first)) . '+'.$i.' day');
                $week = strftime("%Y-%m-%d", $week);
                $labels[$i] =  _d($week);

                $datas_total[$i] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)));
                $datas_payment[$i] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week))) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,date('d', strtotime($week)),date('m', strtotime($week)),date('Y', strtotime($week)));
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

                            $datas_total[$j] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,$i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)));
                            $datas_payment[$j] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,$i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth))) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,$i,date('m', strtotime($endMonth)),date('Y', strtotime($endMonth)));
                            $j++;
                    }
                }else
                {
                    $j = 0;
                    for ($i=date('m', strtotime($beginMonth)); $i <= date('m', strtotime($endMonth)) ; $i++) { 
                        $labels[$j] =  'Tháng '.$i;
                        $datas_total[$j] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,'',$i,date('Y', strtotime($endMonth)));
                        $datas_payment[$j] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,'',$i,date('Y', strtotime($endMonth))) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,'',$i,date('Y', strtotime($endMonth)));
                        $j++;
                    }
                }
            }else
            {
                    $j = 0;
                    for ($i=date('Y', strtotime($beginMonth)); $i <= date('Y', strtotime($endMonth)) ; $i++) { 
                        $labels[$j] =  'Năm '.$i;

                        $datas_total[$key] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,'','',$i);
                        $datas_payment[$key] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,'','',$i) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,'','',$i);
                        $j++;
                    }
            }
        }
        else
        {
            foreach ($this->getYears() as $key => $value) {
                $labels[$key] =  $value['year'];

                $datas_total[$key] = $this->sum_debt_suppliert_order('totalAll_suppliers','total_price_befor_vat','grand_total','cost_delivery',$suppliers_id,'','',$value['year']);
                $datas_payment[$key] = $this->sum_debt_suppliert_order('amount_paid','amount_paid','amount_paid','',$suppliers_id,'','',$value['year']) + $this->sum_debt_suppliert_order('price_other_expenses','price_other_expenses','','price_other_expenses_delivery',$suppliers_id,'','',$value['year']);

            }
        }
        $_data['labels'] = $labels;
        $_data['datas_payment'] = $datas_payment;
        $_data['datas_total'] = $datas_total;
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
}