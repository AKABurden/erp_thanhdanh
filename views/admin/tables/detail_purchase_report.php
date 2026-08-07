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
        }


        $warehouse_id = $this->ci->input->post('warehouse_id');
        $custom_item_select = $this->ci->input->post('custom_item_select');
        $type_items = $this->ci->input->post('type_items');
        $select = array(
            'tblpurchase_order.date',
            'tblimport.date',
            '6',
            'concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as po_code',
            'concat(tblimport.prefix,"-",tblimport.code) as code',
            'tblsuppliers.company',
            '5',
            'tblimport.invoice_id',
            '1',
            '2',
            '3',
            'tblimport_items.quantity_payment',
            'tblimport_items.price',
            '4',
            'tblimport_items.tax_rate',
            'tblimport_items.promotion_suppliers',
            'tblimport_items.amount',
        );
        $where= array(
            'AND tblimport.warehouseman_id != 0',
        );
        if(!empty($type_items))
        {
            array_push($where, 'AND tblimport_items.product_id =',$custom_item_select);   
            array_push($where, 'AND tblimport_items.type = "'.$type_items.'"');
        }    
        if(!empty($beginMonth)&&!empty($endMonth))
        {
            array_push($where, 'AND tblimport.date >='.'"'.$beginMonth.' 00:00:00"');  
            array_push($where, 'AND tblimport.date <='.'"'.$endMonth.' 23:59:59"');
        }
        $aColumns     = $select;
        $sIndexColumn = "id";
        $sTable       = 'tblimport_items';
        $join         = array(
            'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',
            'LEFT JOIN tblpurchase_order ON tblpurchase_order.id = tblimport.id_order',
            'LEFT JOIN tblpurchase_invoice ON tblpurchase_invoice.id = tblimport.red_invoice',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tblimport.suppliers_id',
        );

        $order_byimport='order by product_id asc';
        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblimport.id as id_main,tblimport_items.product_id as product_id,tblimport_items.type as type,1 as exists_quantity,tblpurchase_order.id as idpuer'));

        $output  = $result['output'];
        $rResult = $result['rResult'];
        $footer_data['total_quantity'] = 0; //so luong
        $footer_data['subtotals'] = 0; // tong tien
        $footer_data['amount'] = 0; // tong tien
        $footer_data['tax'] = 0; // tong tien
        $footer_data['pro'] = 0; // tong tien

        $currentPage=$this->ci->input->post('start');
        $sumFExistsQ = 0;
            foreach ($rResult as $key => $aRow) {
            $row = [];
            $get_items = get_items($aRow['product_id'],$aRow['type']);
            $footer_data['total_quantity']+=$aRow['tblimport_items.quantity_payment'];
            $footer_data['subtotals']+=$aRow['tblimport_items.amount'];
            $footer_data['amount']+=$aRow['tblimport_items.quantity_payment']*$aRow['tblimport_items.price'];
            $footer_data['tax']+=($aRow['tblimport_items.tax_rate']/100)*$aRow['tblimport_items.price']*$aRow['tblimport_items.quantity_payment'];
            $footer_data['pro']+=$aRow['tblimport_items.promotion_suppliers'];


            for ($i = 0 ; $i < count($aColumns) ; $i++) {
                if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
                    $_data = $aRow[ strafter($aColumns[$i],'as ')];
                } else {
                    $_data = $aRow[ $aColumns[$i] ];
                }
                if($aColumns[$i]=='tblpurchase_order.date')
                {
                    $_data = _d($aRow['tblpurchase_order.date']);
                }
                if($aColumns[$i]=='tblimport.date')
                {
                    $_data = _d($aRow['tblimport.date']);
                }
                if($aColumns[$i]=='tblimport.invoice_id')
                {
                    $arrInvoice_id = explode(',', $aRow['tblimport.invoice_id']);
                    $tagInvoice = '';
                    foreach ($arrInvoice_id as $invoice_id) {
                        $invoice = get_table_where('tblpurchase_invoice', ['id' => $invoice_id], '', 'row', '', 'code_invoice');
                        if (!empty($invoice)) {
                            $tagInvoice .= $invoice->code_invoice;
                        }
                    }
                    $_data = $tagInvoice;
                }
                if($aColumns[$i]=='5')
                {
                    $arrInvoice_id = explode(',', $aRow['tblimport.invoice_id']);
                    $tagInvoice = '';
                    foreach ($arrInvoice_id as $invoice_id) {
                        $invoice = get_table_where('tblpurchase_invoice', ['id' => $invoice_id], '', 'row', '', 'date_invoice');
                        if (!empty($invoice)) {
                            $tagInvoice .= _d($invoice->date_invoice);
                        }
                    }
                    $_data = $tagInvoice;
                }
                // if($aColumns[$i]=='concat(tblpurchase_order.prefix,"-",tblpurchase_order.code) as po_code')
                // {
                //     $_data = $_data;
                // }
                if($aColumns[$i]=='concat(tblimport.prefix,"-",tblimport.code) as code')
                {
                    $_data = '<a href="#" onclick="view_import('.$aRow['id_main'].'); return false;" >' . $_data . '</a>';
                }
                if($aColumns[$i]=='1')
                {
                    $_data = $get_items->code;//.'<br>'.format_item_purchases($aRow['type']);
                }
                if($aColumns[$i]=='6')
                {
                    $_data = format_purchase_order($aRow['idpuer']);
                }
                if($aColumns[$i]=='2')
                {
                    $_data = $get_items->name;
                }
                if($aColumns[$i]=='3')
                {
                    $_data = $get_items->unit_name_payment;
                }  
                if($aColumns[$i]=='4')
                {
                    $_data = formatNumber($aRow['tblimport_items.price']*$aRow['tblimport_items.quantity_payment']);

                }                 
                if($aColumns[$i]=='tblimport_items.price')
                {
                    $_data = formatNumber($aRow['tblimport_items.price']);
                }
                if($aColumns[$i]=='tblimport_items.quantity_payment')
                {
                    $_data = formatNumber($aRow['tblimport_items.quantity_payment']);
                }
                if($aColumns[$i]=='tblimport_items.tax_rate')
                {
                    $_data = number_format(($aRow['tblimport_items.tax_rate']/100)*$aRow['tblimport_items.price']*$aRow['tblimport_items.quantity_payment']);
                }
                if($aColumns[$i]=='tblimport_items.promotion_suppliers')
                {
                    $_data = number_format($aRow['tblimport_items.promotion_suppliers']);
                }
                if($aColumns[$i]=='tblimport_items.amount')
                {
                    $_data = number_format($aRow['tblimport_items.amount']);
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }
        foreach ($footer_data as $key => $total) {
            $footer_data[$key]=number_format($total);
        }
        $output['sums']              = $footer_data;