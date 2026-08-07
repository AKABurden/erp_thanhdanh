<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns        = [ 'firstname'];
$aColumns = array_merge($aColumns, [
    'tblcontacts_lead.email as email',
    'tblleads.company as company',
    'tblcontacts_lead.phonenumber as phonenumber',
    'tblcontacts_lead.title as title',
    'tblcontacts_lead.birtday as birtday',
    'tblcontacts_lead.is_primary as is_primary',
    'tblcontacts_lead.note as note'
]);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'contacts_lead';
$join         = ['JOIN ' . db_prefix() . 'leads ON ' . db_prefix() . 'leads.id = tblcontacts_lead.id_lead'];

$where = ['AND tblleads.status != 1'];
if(!empty($idlead))
{
    $where[] = 'AND tblleads.id = '.$idlead;
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'tblcontacts_lead.id as id',
    'id_lead',
    'is_primary'
]);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow)
{
    $row = [];

    $rowName = $aRow['firstname'];
    $rowName .= '<div class="row-options">';

    $rowName .= '<a href="#" onclick="contactLead(' . $aRow['id_lead'] . ',' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';

    $rowName .= ' | <a href="' . admin_url('leads/delete_contact_lead/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';

    $rowName .= '</div>';

    $row[] = $rowName;

    $row[] = '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>';
    if (!empty($aRow['company'])) {
        $row[] = '<a href="' . admin_url('leads/index/' . $aRow['id_lead']) . '">' . $aRow['company'] . '</a>';
    } else {
        $row[] = '';
    }
    $row[] = '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>';
    $row[] = $aRow['title'];
    $row[] = _d($aRow['birtday']);
    $row[] = !empty($aRow['is_primary']) ? _l('cong_contacts_is_primary') : '';
    $row[] = $aRow['note'];

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
