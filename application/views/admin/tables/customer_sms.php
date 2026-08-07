<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['userid'];
$aColumns[] = 'company';

$sIndexColumn = 'userid';
$sTable       = db_prefix().'clients';
$join = array();
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [
    'phonenumber',
    'email_client',
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<div class="checkbox"><input type="checkbox" class="checkClient" value="' . $aRow['userid'] . '" name="'.$aRow['company'].' {' . $aRow['userid'] . '}"><label></label></div>';
    $numOne = '<p>'.$aRow['company'].'</p>';
    $numTwo = '<p>';
    $numTwo .= '    <i class="fa fa-envelope-o" aria-hidden="true"></i> '.$aRow['email_client'].'<br/>';
    $numTwo .= '    <i class="fa fa-mobile" aria-hidden="true"></i> '.$aRow['phonenumber'];
    $numTwo .='</p>';
    $row[] = $numOne;
    $row[] = $numTwo;

    $output['aaData'][] = $row;
}
