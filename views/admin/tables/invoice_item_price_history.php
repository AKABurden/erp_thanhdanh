<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns     = array(
    'tblitem_price_history.date',
    'tblitem_price_history.price',
    'tblitem_price_history.new_price',
    'tblitem_price_history.staff',
    );

$sIndexColumn = "id";
$sTable       = 'tblitems';

$where = array();
$order_by = '';
if(isset($rel_id) && is_numeric($rel_id)) {
    $where[] = "and tblitem_price_history.item_id=".$rel_id;
}

$join             = array(
    'RIGHT JOIN (select tblitem_price_history.* from tblitem_price_history where tblitem_price_history.item_id='.$rel_id.') as tblitem_price_history on tblitem_price_history.item_id = tblitems.id',
    );
$additionalSelect = array(
    'tblitems.id',
    'tblitems.name',
    'tblitem_price_history.price',
    'tblitem_price_history.date',
    );

$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $format_number_column = ['tblitem_price_history.price','tblitem_price_history.new_price'];
        if(in_array($aColumns[$i], $format_number_column)) {
            $_data = number_format($_data,0,',','.');
        }
        if($aColumns[$i] == 'tblitem_price_history.date')
        {
          $_data=_dt($_data);
        }
        if($aColumns[$i] == 'tblitem_price_history.staff')
        {
          $_data=staff_profile_image($_data, array('staff-profile-image-small mright5'), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => get_staff_full_name($_data)
                    )).get_staff_full_name($_data);
        }
        $row[] = $_data;
    }
    
   $output['aaData'][] = $row;
}
