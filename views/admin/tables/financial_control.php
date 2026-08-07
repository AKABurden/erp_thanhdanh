<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'tblcosts.id',
    'tblcosts.costs_parent',
    'tblcosts.code',
    'tblcosts.name',
    'tblcosts.lever',
    '1',
    );
$months=get_months();
foreach ($months as $key => $month) 
{
    array_push($aColumns, $key);
}
$join=array();
$where = array('AND tblcosts.lever = 1');
$id_new = array();
if($this->_instance->input->post('id_new')) {
    $id_new = explode(',', $this->_instance->input->post('id_new'));
    array_push($where, 'AND tblcosts.id = "'.$id_new[0].'"');
}
array_push($join, 'LEFT JOIN tblfinancial_control_detail ON tblfinancial_control_detail.id_financial_control=tblcosts.id');
$sIndexColumn = "id";
$sTable       = 'tblcosts';
$group_by = "GROUP BY tblcosts.id"; 
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,$join,$where,array('tblcosts.id'),'',$group_by);
// var_dump($result);die;
$output  = $result['output'];
$rResult = $result['rResult'];
$nam =$this->_instance->input->post('year_sales_ss');
$rows=array();
foreach ($rResult as $key => $aRow) {
    $row = array();
    $id = $aRow['id'];
    $ktr = get_table_where('tblcosts',array('costs_parent'=>$id),'','row');
    $financial_control_detail = get_table_where('tblfinancial_control_detail',array('id_financial_control'=>$aRow['id'],'nam'=>$this->_instance->input->post('year_sales_ss')),'','row');
    for ($i = 0; $i < count($aColumns); $i++) {
       
        $_data = $aRow[ $aColumns[$i] ];
        if ($i >5 && $i <count($aColumns)) {
            if(empty($ktr))
            {
                if(!empty($financial_control_detail)){
                    $text = $aColumns[$i];
                $_data= '<div class="type_v1">'.ch_EditColumSelectInput(number_format($financial_control_detail->$text), $aRow['tblcosts.id'], '', '<a class="pointer" id="check_'.$aRow['tblcosts.id'].'" target="_blank" >'.number_format($financial_control_detail->$text).'</a>','', admin_url('costs/price_items/'.$nam.'/'.$aColumns[$i]),'class="formUpdateDataTable"').'</div><div class="type_v2 hide" data-id="'.$aRow['tblcosts.id'].'" class="price_items_input"><input onkeyup="formatNumBerKeyUp(this)" type="text" name="price_items" id="price_items" class="height_auto  price_items H_input align_right" value="'.number_format($financial_control_detail->$text).'"></div>';
                }else
                {
                $_data= '<div class="type_v1">'.ch_EditColumSelectInput(number_format(0), $aRow['tblcosts.id'], '', '<a class="pointer" id="check_'.$aRow['tblcosts.id'].'" target="_blank" >'.number_format(0).'</a>','', admin_url('costs/price_items/'.$nam.'/'.$aColumns[$i]),'class="formUpdateDataTable"').'</div><div class="type_v2 hide" data-id="'.$aRow['tblcosts.id'].'" class="price_items_input"><input onkeyup="formatNumBerKeyUp(this)" type="text" name="price_items" id="price_items" class="height_auto  price_items H_input align_right" value="'.number_format(0).'"></div>';    
                }
            }
        }
        if($aColumns[$i] == 'tblcosts.id')
        {
            $_data = $key + 1;
        }
        if($aColumns[$i] == 'tblcosts.costs_parent')
        {
            $_data = '';
        }
        if($aColumns[$i] == '1')
        {
            $_data = $this->_instance->input->post('year_sales_ss');
        }
        if($aColumns[$i] == 'tblcosts.lever')
        {
            $_data = 'Cấp: '.$aRow[ $aColumns[$i] ];
        }
        $row[] = $_data;
    }
    $rows[] = $row;
    if($ktr)
    {
        $rows = array_merge($rows, get_tale_fin($id,$this->_instance->input->post('year_sales_ss'),$id_new,(1)));
    }
}
foreach ($rows as $key => $rowss) {
      $output['aaData'][] = $rowss;
}