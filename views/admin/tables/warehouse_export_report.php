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


        $warehouse_id = $this->ci->input->post('warehouse_id');
        $custom_item_select = $this->ci->input->post('custom_item_select');
        $type_items = $this->ci->input->post('type_items');

        //Xuất kho
        $selectexport = array(
            'tbl_export_warehouses.date as date',
            'tbl_export_warehouses.reference_no as code',
            '1',
            'tbl_export_warehous_items.location_id as localtion_id',
            'tbl_export_warehous_items.quantity as quantity'
        );
        $whereexport= array(
            'AND tbl_export_warehous_items.warehouse_id ='.$warehouse_id,
            'AND tbl_export_warehouses.warehouseman_id != 0',
        );
        if(!empty($type_items))
        {
            array_push($whereexport, 'AND tbl_export_warehous_items.item_id =',$custom_item_select);   
            array_push($whereexport, 'AND tbl_export_warehous_items.type_item = "'.$type_items.'"');
        }  
        if(!empty($beginMonth)&&!empty($endMonth))
        {
            array_push($whereexport, 'AND tbl_export_warehouses.date >='.'"'.$beginMonth.' 00:00:00"');  
            array_push($whereexport, 'AND tbl_export_warehouses.date <='.'"'.$endMonth.' 23:59:59"');
        }
        $aColumnsexport     = $selectexport;
        $sIndexColumnexport = "id";
        $sTableexport       = 'tbl_export_warehous_items';
        $joinexport         = array(
            'LEFT JOIN tbl_export_warehouses ON tbl_export_warehouses.id = tbl_export_warehous_items.export_warehouse_id',
             
        );

        $order_byexport ='order by item_id asc';
        $resultexport   = data_tables_init($aColumnsexport , $sIndexColumnexport , $sTableexport , $joinexport , $whereexport , array('tbl_export_warehouses.id as id_main,tbl_export_warehous_items.item_id as product_id,tbl_export_warehous_items.type_item as type,1 as exists_quantity'));

        $outputexport  = $resultexport['output'];
        $rResultexport = $resultexport['rResult'];
        //trả hàng NCC
        // $select_return = array(
        //     'tblreturn_suppliers.date as date',
        //     'concat(tblreturn_suppliers.prefix,"",tblreturn_suppliers.code) as code',
        //     '1',
        //     'tblreturn_suppliers_items.localtion_warehouses_id as localtion_id',
        //     'tblreturn_suppliers_items.quantity_net as quantity'
        // );
        // $where_return= array(
        //     'AND tblreturn_suppliers.warehouse_id ='.$warehouse_id,
        //     'AND tblreturn_suppliers.warehouseman_id != 0',
        // );
        // if(!empty($type_items))
        // {
        //     array_push($where_return, 'AND tblreturn_suppliers_items.product_id =',$custom_item_select);   
        //     array_push($where_return, 'AND tblreturn_suppliers_items.type = "'.$type_items.'"');
        // }  
        // if(!empty($beginMonth)&&!empty($endMonth))
        // {
        //     array_push($where_return, 'AND tblreturn_suppliers.date >='.'"'.$beginMonth.' 00:00:00"');  
        //     array_push($where_return, 'AND tblreturn_suppliers.date <='.'"'.$endMonth.' 23:59:59"');
        // }
        // $aColumns_return     = $select_return;
        // $sIndexColumn_return = "id";
        // $sTable_return       = 'tblreturn_suppliers_items';
        // $join_return         = array(
        //     'LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return',
             
        // );

        // $order_by_return ='order by product_id asc';
        // $result_return   = data_tables_init($aColumns_return , $sIndexColumn_return , $sTable_return , $join_return , $where_return , array('tblreturn_suppliers.id as id_main,tblreturn_suppliers_items.product_id as product_id,tblreturn_suppliers_items.type as type,2 as exists_quantity'));

        // $output_return  = $result_return['output'];
        // $rResult_return = $result_return['rResult'];

        // //Điều chỉnh kho giảm
        // $select_adjustedG = array(
        //     'tbladjusted.date as date',
        //     'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
        //     '1',
        //     'tbladjusted_items.localtion as localtion_id',
        //     'tbladjusted_items.quantity_net as quantity'
        // );
        // $where_adjustedG= array(
        //     'AND tbladjusted.warehouse_id ='.$warehouse_id,
        //     'AND tbladjusted.type = 2',
        // );
        // if(!empty($type_items))
        // {
        //     array_push($where_adjustedG, 'AND tbladjusted_items.product_id =',$custom_item_select);   
        //     array_push($where_adjustedG, 'AND tbladjusted_items.type = "'.$type_items.'"');
        // }  
        // if(!empty($beginMonth)&&!empty($endMonth))
        // {
        //     array_push($where_adjustedG, 'AND tbladjusted.date >='.'"'.$beginMonth.' 00:00:00"');  
        //     array_push($where_adjustedG, 'AND tbladjusted.date <='.'"'.$endMonth.' 23:59:59"');
        // }
        // $aColumns_adjustedG     = $select_adjustedG;
        // $sIndexColumn_adjustedG = "id";
        // $sTable_adjustedG       = 'tbladjusted_items';
        // $join_adjustedG         = array(
        //     'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
             
        // );

        // $order_by_adjustedG ='order by product_id asc';
        // $result_adjustedG   = data_tables_init($aColumns_adjustedG , $sIndexColumn_adjustedG , $sTable_adjustedG , $join_adjustedG , $where_adjustedG , array('tbladjusted.id as id_main,tbladjusted_items.product_id as product_id,tbladjusted_items.type as type,3 as exists_quantity'));

        // $output_adjustedG  = $result_adjustedG['output'];
        // $rResult_adjustedG = $result_adjustedG['rResult'];

        
        // //Chuyển kho: di
        // $select_TranfersD = array(
        //     'tbltransfer_warehouse.date as date',
        //     'concat(tbltransfer_warehouse.prefix,"",tbltransfer_warehouse.code) as code',
        //     '1',
        //     'tbltransfer_warehouse_detail.localtion_id as localtion_id',
        //     'tbltransfer_warehouse_detail.quantity_net as quantity'
        // );
        // $where_TranfersD= array(
        //     'AND tbltransfer_warehouse.warehouse_id ='.$warehouse_id,
        //     'AND tbltransfer_warehouse.warehouseman_id != 0',
        // );
        // if(!empty($type_items))
        // {
        //     array_push($where_TranfersD, 'AND tbltransfer_warehouse_detail.id_items =',$custom_item_select);   
        //     array_push($where_TranfersD, 'AND tbltransfer_warehouse_detail.type = "'.$type_items.'"');
        // }
        // if(!empty($beginMonth)&&!empty($endMonth))
        // {
        //     array_push($where_TranfersD, 'AND tbltransfer_warehouse.date >='.'"'.$beginMonth.' 00:00:00"');  
        //     array_push($where_TranfersD, 'AND tbltransfer_warehouse.date <='.'"'.$endMonth.' 23:59:59"');
        // }
        // $aColumns_TranfersD     = $select_TranfersD;
        // $sIndexColumn_TranfersD = "id";
        // $sTable_TranfersD       = 'tbltransfer_warehouse_detail';
        // $join_TranfersD         = array(
        //     'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',
             
        // );

        // $order_by_TranfersD ='order by id_items asc';
        // $result_TranfersD   = data_tables_init($aColumns_TranfersD , $sIndexColumn_TranfersD , $sTable_TranfersD , $join_TranfersD , $where_TranfersD , array('tbltransfer_warehouse.id as id_main,tbltransfer_warehouse_detail.id_items as product_id,tbltransfer_warehouse_detail.type as type,4 as exists_quantity'));

        // $output_TranfersD  = $result_TranfersD['output'];
        // $rResult_TranfersD = $result_TranfersD['rResult'];   

        //Xuất kho sản xuất
        // $select_exportsx = array(
        //     'tbl_suggest_exporting.date as date',
        //     'tbl_suggest_exporting.reference_stock as code',
        //     '1',
        //     'tbl_suggest_exporting_items.location_id as localtion_id',
        //     'tbl_suggest_exporting_items.quantity_exchange as quantity'
        // );
        // $where_exportsx= array(
        //     'AND tbl_suggest_exporting.warehouse_id ='.$warehouse_id,
        //     // 'AND tbl_suggest_exporting_items.type_item = "materials"',
        //     'AND tbl_suggest_exporting.status_stock is not NULL',
        //     'AND tbl_suggest_exporting.warehouseman_id != 0',
        // );
        // if(!empty($type_items))
        // {
        //     array_push($where_exportsx, 'AND tbl_suggest_exporting_items.item_id =',$custom_item_select);   
        //     array_push($where_exportsx, 'AND tbl_suggest_exporting_items.type_item = "'.$type_items.'"');
        // }
        // if(!empty($beginMonth)&&!empty($endMonth))
        // {
        //     array_push($where_exportsx, 'AND tbl_suggest_exporting.date >='.'"'.$beginMonth.' 00:00:00"');  
        //     array_push($where_exportsx, 'AND tbl_suggest_exporting.date <='.'"'.$endMonth.' 23:59:59"');
        // }
        // $aColumns_exportsx     = $select_exportsx;
        // $sIndexColumn_exportsx = "id";
        // $sTable_exportsx       = 'tbl_suggest_exporting_items';
        // $join_exportsx         = array(
        //     'LEFT JOIN tbl_suggest_exporting ON tbl_suggest_exporting.id = tbl_suggest_exporting_items.suggest_exporting_id',
        // );

        // $order_by_exportsx ='order by item_id asc';
        // $result_exportsx   = data_tables_init($aColumns_exportsx , $sIndexColumn_exportsx , $sTable_exportsx , $join_exportsx , $where_exportsx , array('tbl_suggest_exporting.id as id_main,tbl_suggest_exporting_items.item_id as product_id,tbl_suggest_exporting_items.type_item as type,5 as exists_quantity'));

        // $output_exportsx  = $result_exportsx['output'];
        // $rResult_exportsx = $result_exportsx['rResult'];

        $aColumnsG=array(
            'date',
            'code',
            '1',
            'localtion_id',
            'quantity'
        );
        $rResultG = array();
        if(!empty($rResultexport))
        {
        $rResultG=array_merge($rResultG,$rResultexport);   
        }
        if(!empty($rResult_return))
        {
        $rResultG=array_merge($rResultG,$rResult_return);   
        }
        if(!empty($rResult_adjustedG))
        {
        $rResultG=array_merge($rResultG,$rResult_adjustedG);   
        }
        if(!empty($rResult_TranfersD))
        {
        $rResultG=array_merge($rResultG,$rResult_TranfersD);   
        }
        if(!empty($rResult_exportsx))
        {
        $rResultG=array_merge($rResultG,$rResult_exportsx);   
        }     
        if(!empty($rResultG))
        {
        // usort($rResultG, ch_make_cmp(['type' => "desc",'product_id' => "desc",'localtion_id' => "desc",'warehouseman_date' => "asc",'exists_quantity'=> "asc"]));
        }
        $output=$outputexport;
        // $output['iTotalRecords']=$outputexport['iTotalRecords'] +$output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords']  + $output_exportsx['iTotalRecords'] + $output_TranfersD['iTotalRecords'];
        // $output['iTotalDisplayRecords']=$outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords']  + $output_exportsx['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'];

        $output['iTotalRecords']=$outputexport['iTotalRecords'];
        $output['iTotalDisplayRecords']=$outputexport['iTotalDisplayRecords'];

        $currentPage=$this->ci->input->post('start');
        $sumFExistsQ = 0;
        // $row= array();
            foreach ($rResultG as $key => $aRow) {
            $row = [];
            $get_items = get_items($aRow['product_id'],$aRow['type']);
            for ($i = 0 ; $i < count($aColumnsG) ; $i++) {
                if(strpos($aColumnsG[$i],'as') !== false && !isset($aRow[ $aColumnsG[$i] ])){
                    $_data = $aRow[ strafter($aColumnsG[$i],'as ')];
                } else {
                    $_data = $aRow[ $aColumnsG[$i] ];
                }
                if($aColumnsG[$i]=='date')
                {
                    $_data = '<div class="text-center">'._d($aRow['date']).'<div>';
                } 
                if($aColumnsG[$i]=='quantity')
                {
                    $_data = '<div class="text-center">'.formatNumber($aRow['quantity']).'<div>';
                }
                if($aColumnsG[$i]=='1')
                {
                    $_data = $get_items->name.' ('.$get_items->code.') '.format_item_purchases($aRow['type']);
                }
                if($aColumnsG[$i]=='localtion_id')
                {
                    $_data = '<div class="text-center">'.get_listname_localtion_warehouse($aRow['localtion_id']).'<div>';
                }
                if($aColumnsG[$i]=='code')
                {
                    if($aRow['exists_quantity'] == 1)
                    {
                     $_data = '<a data-tnh="modal" class="tnh-modal" href="'.admin_url('releases/view_export_warehouse/'.$aRow['id_main']).'" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">'._l('tnh_export_warehouse_sales').'</span>';   
                    }elseif($aRow['exists_quantity'] == 2)
                    {
                     $_data = '<a href="#" onclick="view_return_suppliers('.$aRow['id_main'].'); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">'._l('ch_return_ncc').'</span>';   
                    }elseif($aRow['exists_quantity'] == 3)
                    {
                     $_data = '<a href="#" onclick="view_adjusted('.$aRow['id_main'].'); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">'._l('ch_adjustedG').'</span>';   
                    }elseif($aRow['exists_quantity'] == 4)
                    {
                     $_data = '<a href="#" onclick="view_transfer('.$aRow['id_main'].'); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">'._l('ch_transfer_D').'</span>';   
                    }elseif($aRow['exists_quantity'] == 5)
                    {
                     $_data = '<a class="tnh-modal" title="Xem" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.admin_url('stock/view_exporting_production/'.$aRow['id_main']).'">' . $_data . '</a>  <span class="inline-block label label-success">'._l('tnh_exporting_stock_producion').'</span>';    
                    }                    
                }
                $row[] = $_data;
            }
            $output['aaData'][] = $row;
        }