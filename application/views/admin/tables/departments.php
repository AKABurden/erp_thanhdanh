<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'departmentid',
    'code',
    'name',
    'email',
    'calendar_id',
    ];
$sIndexColumn = 'departmentid';
$sTable       = db_prefix().'departments';
$where = [];
if($this->ci->input->post('hide_not_active')) {
	$where[] = 'AND active_departments = "1"';
}
$where[] = 'AND tbldepartments.type = 0';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, [
	'email',
	'hidefromclient',
	'host',
	'encryption',
	'password',
	'delete_after_import',
	'imap_username'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $ps    = '';
        if (!empty($aRow['password'])) {
            $ps = $this->ci->encryption->decrypt($aRow['password']);
        }
        if ($aColumns[$i] == 'departmentid') {
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['departmentid'] . '"><label></label></div>';
        }
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_department(this,' . $aRow['departmentid'] . '); return false" data-name="' . $aRow['name'] . '" data-code="' . $aRow['code'] . '" data-calendar-id="' . $aRow['calendar_id'] . '" data-email="' . $aRow['email'] . '" data-hide-from-client="' . $aRow['hidefromclient'] . '" data-host="' . $aRow['host'] . '" data-password="' . $ps . '" data-imap_username="' . $aRow['imap_username'] . '" data-encryption="' . $aRow['encryption'] . '" data-delete-after-import="' . $aRow['delete_after_import'] . '">' . $_data . '</a>';
        }
        $row[] = $_data;
    }

    $options = icon_btn('departments/department/' . $aRow['departmentid'], 'pencil-square-o', 'btn-default', [
        'onclick' => 'edit_department(this,' . $aRow['departmentid'] . '); return false', 'data-name' => $aRow['name'], 'data-code' => $aRow['code'], 'data-calendar-id' => $aRow['calendar_id'], 'data-email' => $aRow['email'], 'data-hide-from-client' => $aRow['hidefromclient'], 'data-host' => $aRow['host'], 'data-password' => $ps, 'data-encryption' => $aRow['encryption'], 'data-imap_username' => $aRow['imap_username'], 'data-delete-after-import' => $aRow['delete_after_import'],
        ]);
    $row[] = $options .= icon_btn('departments/delete/' . $aRow['departmentid'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
