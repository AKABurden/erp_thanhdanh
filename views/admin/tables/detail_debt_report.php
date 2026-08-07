    <?php
    defined('BASEPATH') or exit('No direct script access allowed');
    $beginMonth =  '';
    $endMonth   =  '';
        $months_report = $this->ci->input->post('report_months');

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
                $from_date = to_sql_date($this->ci->input->post('report_from'));
                $to_date   = to_sql_date($this->ci->input->post('report_to'));
                if ($from_date == $to_date) {
                    $beginMonth =  $to_date;
                    $endMonth   =  $to_date;
                } else {
                    $beginMonth =  $from_date;
                    $endMonth   =  $to_date;
                }
            }
            // $this->db->where($custom_date_select);
        }

    $aColumns = [
        'tblpurchase_order.date',
        'concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code',
        'tblsuppliers.company',
        '3',
        'tblpurchase_order_items.quantity_payment',
        'tblpurchase_order_items.price_suppliers',
        '(tblpurchase_order_items.promotion_expected + tblpurchase_order_items.total_suppliers) as total_suppliers',
        'tblpurchase_order_items.promotion_expected',
        'tblpurchase_order.discount_percent_suppliers',
        '(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)',
        '10',
    ];
    $sIndexColumn = 'id';
    $sTable       = 'tblpurchase_order_items';
    $where        = [];

    $filter = [];
    $join = [
        'LEFT JOIN tblpurchase_order  ON tblpurchase_order.id=tblpurchase_order_items.id_purchase_order',
        'LEFT JOIN tblsuppliers  ON tblsuppliers.id=tblpurchase_order.suppliers_id'

    ];
    $custom_item_select = $this->ci->input->post('custom_item_select');
    if(is_numeric($custom_item_select))
    {
    array_push($where, 'AND tblpurchase_order_items.product_id ='.$custom_item_select); 
    }
    $search_id_suppliers = $this->ci->input->post('search_id_suppliers');
    if(!empty($search_id_suppliers))
    {
    array_push($where, 'AND tblpurchase_order.suppliers_id IN('.implode(',', $search_id_suppliers).')'); 
    }
    array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
    $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblpurchase_order.id as id_orders,tblpurchase_order_items.product_id as product_id,tblpurchase_order_items.type as type','tblpurchase_order.totalAll_suppliers','tblpurchase_order.promotion_expected',
    ]);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    $j=0;
    usort($rResult, ch_make_cmp(['id_orders'=> "asc"]));
foreach ($rResult as $key => $aRow) {
    $j++;
    $row = array();
    if($key == 0)
    {
            $data =  $aRow['id_orders'];
            for ($i = 0; $i < count($aColumns); $i++) {
            $_data = '';
            if($aColumns[$i]=='tblpurchase_order.date_create')
            {
                $_data = $aRow['tblpurchase_order.date_create'];
            }
            if($aColumns[$i]=='tblpurchase_order.date')
            {
                $_data = _d($aRow['tblpurchase_order.date']);
            }
            if($aColumns[$i]=='concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code')
            {
                $_data = '<a onclick="view_purchase_order('.$aRow['id_orders'].'); return false;" >' .$aRow['code'].'</a>';
            }
            if($aColumns[$i]=='tblsuppliers.company')
            {
                $_data = $aRow['tblsuppliers.company'];
            }
            if($aColumns[$i]=='tblpurchase_order.discount_percent_suppliers')
            {
                $_data = formatNumber($aRow['tblpurchase_order.discount_percent_suppliers']);
            }
            if($aColumns[$i]=='(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)')
            {
                $_data = formatNumber($aRow['(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)']);
            }
            if($aColumns[$i]=='10')
            {
                $_data = formatNumber($aRow['totalAll_suppliers']-$aRow['(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)']);
            }
            if($aColumns[$i]=='(tblpurchase_order_items.promotion_expected + tblpurchase_order_items.total_suppliers) as total_suppliers')
            {
                $_data = formatNumber($aRow['totalAll_suppliers'] + $aRow['promotion_expected']);
            }
            if($aColumns[$i]=='tblpurchase_order_items.promotion_expected')
            {
                $_data = formatNumber($aRow['promotion_expected']);
            }
            $row[] = $_data;
            }
            $row['DT_RowClass'] = 'alert-header bold warning';
            $output['aaData'][] = $row;
    }else
    {
        if($aRow['id_orders'] != $data)
        {
            $data =  $aRow['id_orders'];
            for ($i = 0; $i < count($aColumns); $i++) {
            $_data = '';
            if($aColumns[$i]=='tblpurchase_order.date_create')
            {
                $_data = $aRow['tblpurchase_order.date_create'];
            }
            if($aColumns[$i]=='tblpurchase_order.date')
            {
                $_data = _d($aRow['tblpurchase_order.date']);
            }
            if($aColumns[$i]=='concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code')
            {
                $_data = '<a onclick="view_purchase_order('.$aRow['id_orders'].'); return false;" >' .$aRow['code'].'</a>';
            }
            if($aColumns[$i]=='tblsuppliers.company')
            {
                $_data = $aRow['tblsuppliers.company'];
            }
            if($aColumns[$i]=='tblpurchase_order.discount_percent_suppliers')
            {
                $_data = formatNumber($aRow['tblpurchase_order.discount_percent_suppliers']);
            }
            if($aColumns[$i]=='(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)')
            {
                $_data = formatNumber($aRow['(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)']);
            }
            if($aColumns[$i]=='10')
            {
                $_data = formatNumber($aRow['totalAll_suppliers']-$aRow['(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)']);
            }
            if($aColumns[$i]=='(tblpurchase_order_items.promotion_expected + tblpurchase_order_items.total_suppliers) as total_suppliers')
            {
                $_data = formatNumber($aRow['totalAll_suppliers'] + $aRow['promotion_expected']);
            }
            if($aColumns[$i]=='tblpurchase_order_items.promotion_expected')
            {
                $_data = formatNumber($aRow['promotion_expected']);
            }
            $row[] = $_data;
            }
            $row['DT_RowClass'] = 'alert-header bold warning';
            $output['aaData'][] = $row;
        }

    }
    $row = array();
    $get_items = get_items($aRow['product_id'],$aRow['type']);
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        $data =  $aRow['id_orders'];
        if($aColumns[$i]=='tblpurchase_order.date_create')
        {
            $_data = '';
        }
        if($aColumns[$i]=='tblpurchase_order.date')
        {
            $_data = '';
        }
        if($aColumns[$i]=='tblpurchase_order_items.quantity_payment')
        {
            $_data = formatNumber($aRow['tblpurchase_order_items.quantity_payment']);
        }
        if($aColumns[$i]=='tblpurchase_order_items.price_suppliers')
        {
            $_data = formatNumber($aRow['tblpurchase_order_items.price_suppliers']);
        }
        if($aColumns[$i]=='tblpurchase_order_items.promotion_expected')
        {
            $_data = formatNumber($aRow['tblpurchase_order_items.promotion_expected']);
        }
        if($aColumns[$i]=='(tblpurchase_order_items.promotion_expected + tblpurchase_order_items.total_suppliers) as total_suppliers')
        {
            $_data = formatNumber($aRow['total_suppliers']);
        }
        if($aColumns[$i]=='concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as code')
        {
            $_data = '';
        }
        if($aColumns[$i]=='tblsuppliers.company')
        {
            $_data = $get_items->name.' ('.$get_items->code.') <br>'.format_item_purchases($aRow['type']);
        }
        if($aColumns[$i]=='3')
        {
            $_data = $get_items->unit_name;
        }
        if($aColumns[$i]=='tblpurchase_order.discount_percent_suppliers')
        {
            $_data ='';
        }
        if($aColumns[$i]=='(tblpurchase_order.amount_paid + tblpurchase_order.price_other_expenses)')
        {
            $_data = '';
        }
        if($aColumns[$i]=='10')
        {
            $_data = '';
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
