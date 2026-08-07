<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'tblcosts.id',
    'tblcosts.costs_parent',
    'tblcosts.code',
    'tblcosts.name',
    '11',
    '1',
    '2',
    '12',
    );
$join=array();
$where = array('AND tblcosts.lever = 1');
// array_push($where, 'AND tblcosts.id > 0');
$nam = $this->_instance->input->post('year_fin');
$id_new = array();
if($this->_instance->input->post('id_new')) {
    $id_new = explode(',', $this->_instance->input->post('id_new'));
    array_push($where, 'AND tblcosts.id = "'.$id_new[0].'"');
}

$sIndexColumn = "id";
$sTable       = 'tblcosts';
$group_by = "GROUP BY tblcosts.id"; 
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,$join,$where,array('tblcosts.id'),'',$group_by);
$output  = $result['output'];
$rResult = $result['rResult'];
$rows=array();
$thang =$this->_instance->input->post('month_fin');
foreach ($rResult as $key => $aRow) {
    $row = array();
    $id = $aRow['id'];
    $financial_control_detail = get_table_where('tblfinancial_control_detail',array('id_financial_control'=>$aRow['id'],'nam'=>$nam),'','row');
    for ($i = 0; $i < count($aColumns); $i++) {
       
        $_data = $aRow[ $aColumns[$i] ];
        if ($aColumns[$i] == '11') {
            $total = sum_fin($aRow['id'],$nam,0,$thang);
            $_data = text_align(number_format($total),'right');
        }
        if($aColumns[$i] == 'tblcosts.costs_parent')
        {
            $_data = '';
        }
        if($aColumns[$i] == '1')
        {
            $thu = get_table_where_sum('tblother_payslips_coupon',array('month(date)'=>$thang,'year(date)'=>$nam,'id_costs'=>$aRow['id']),'total');
            $chi = get_table_where_sum('tblother_payslips',array('month(date)'=>$thang,'year(date)'=>$nam,'id_costs'=>$aRow['id']),'total');
            $chi_c = get_table_where_sum('tblpay_slip',array('month(date)'=>$thang,'year(date)'=>$nam,'id_costs'=>$aRow['id']),'payment');
            $tu = get_table_where_sum('tbladvance_payment',array('month(date)'=>$thang,'year(date)'=>$nam,'id_costs'=>$aRow['id']),'total');
            $thuchi = sum_fin_other_operations($aRow['id'],$nam,0,$thang)+$thu+$chi+$chi_c+$tu;
            $_data = text_align(number_format($thuchi),'right');
        }
        if ($aColumns[$i] == '2') {
            $avg = $thuchi-$total;
            if($avg <= 0)
            {
                $_data =text_align(number_format(abs($avg)).' <i class="fa fa-long-arrow-up" aria-hidden="true" style="color:#379a31;"></i>','right');
            }else
            {
                $_data =text_align(number_format(abs($avg)).' <i style="color:red;" class="fa fa-long-arrow-down" aria-hidden="true"></i>','right');
            }
            
        }
        if ($aColumns[$i] == '12') {
            if($thuchi == 0)
            {   
                $_data='0%';
            }else
            {
                $_data=(($thuchi/$thuchi)*100).'%';
            }
        }
        $row[] = $_data;
        $row['DT_RowClass'] = 'alert-header bold danger';

    }
    $rows[] = $row;
    $ktr = get_table_where('tblcosts',array('costs_parent'=>$id),'','row');
    if($ktr)
    {
        $rows = array_merge($rows, get_tale_fin_retport($id,$nam,$id_new,(1),$thuchi,$thang));
    }
}
foreach ($rows as $key => $rowss) {
      $output['aaData'][] = $rowss;
}
$output['title_excel'] = ['Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') . ' - ' . (!empty($endMonth) ? _dt($endMonth) : ''),];