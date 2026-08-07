<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->db->where('type', 'lead');
$procedure_lead = $this->ci->db->get(db_prefix().'procedure_client')->row();

$this->ci->db->where('id_detail', $procedure_lead->id);
$this->ci->db->order_by('orders', 'asc');
$procedure_detail = $this->ci->db->get(db_prefix().'procedure_client_detail')->result_array();
$aColumns = [
    'concat(prefix,code) as fullcode',
    'date',
    'product_other_buy',
    'address_other_buy',
    db_prefix().'advisory_lead.date_create as date_create',
    db_prefix().'advisory_lead.create_by as create_by'
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'advisory_lead';
$where        = [];
if(!empty($id_lead))
{
    $where[] = 'AND lead = '.$id_lead;
}

$filter = [];

$join[] = 'LEFT JOIN '.db_prefix().'staff cby on cby.staffid = '.db_prefix().'advisory_lead.create_by';
$join[] = 'LEFT JOIN '.db_prefix().'leads clead on clead.id = '.db_prefix().'advisory_lead.lead';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
    db_prefix().'advisory_lead.id',
    'lead',
    'cby.firstname as cbyfirstname',
    'cby.lastname as cbylastname',
    'date',
    'clead.name as name_lead',
    'status_break',
    'date_expected',
    db_prefix().'advisory_lead.status_first as status_first'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
foreach ($rResult as $aRow) {
    $row = [];

    $options = '<div class="row-options">';
    $options .= '<a href="#" class="text-success" onclick="restore_advisory_lead('.$aRow['id'].', this); return false;">'._l('cong_restore').'</a> | ';
    $options .= '<a href="#" class="text-warning" onclick="BreakAdvisory('.$aRow['id'].', '.($aRow['status_break'] == 1 ? 0 : 1).', this); return false;">'.($aRow['status_break'] == 1 ? _l('cong_restore_break_advisory') : _l('cong_break_advisory')).'</a> | ';
    $options .= '</div>';

    $row[] = '<p class="one-control pointer">'.$aRow['fullcode'].'</p>'.$options;
    $row[] = _d($aRow['date']);
    $row[] = $aRow['product_other_buy'];
    $row[] = $aRow['address_other_buy'];
    $row[] = _dt($aRow['date_create']);
    $fullname_CREATE = $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'];
    $profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $profile_CREATE .= '<span class="hide">' . $fullname_CREATE . '</span>';
    $row[] = $profile_CREATE;

    if($aRow['status_break'] == 1)
    {
        $row['DT_RowClass'] = 'bg-danger';
    }


    $string_Row = '<ul class="progressbar">';
    $status_first = 0;
    $date_expected = $aRow['date_expected'];
    $content_dk = ""; //Điều kiện để chạy tiếp theo
    foreach($procedure_detail as $key => $value)
    {

        $color_text = "";
        if($status_first == 0 && $value['id'] == $aRow['status_first']) {
            $status_first = 1;
        }

        if($status_first == 0) {
            continue;
        }

        $action_advisory = get_table_where(db_prefix().'procedure_advisory_lead', [
            'id_advisory' => $aRow['id'],
            'status_procedure' => $value['id']
        ], '', 'row');

        if($status_first == 1)
        {
            $title_data_finish = "";
            if(!empty($action_advisory->date_create))
            {
                $title_data_finish = _l('cong_Finish').' ('._d($action_advisory->date_create).')';
                $date_expected = $action_advisory->date_create;
                $color_text = 'text-success';
            }
            else
            {
                $leadtime = $value['leadtime'];
                if($key > 0)
                {
                    $date_expected = date("Y-m-d", strtotime("$date_expected +$leadtime day"));
                }
                else
                {
                    $date_expected = $aRow['date_expected'];
                }
                if(strtotime($date_expected) <= strtotime(date('Y-m-d')))
                {
                    $title_data_finish = _l('cong_time_have_action').' ('._d($date_expected).')';
                    if(empty($content_dk))
                    {
                        $color_text = 'text-danger';
                        $content_dk = 1;
                    }
                }
                else
                {
                    $title_data_finish = _l('cong_date_expected').' ('._d($date_expected).')';
                }
            }
            $date_from = (!empty($color_text) ? ('status-procedure="'.$value['id'].'" id-data="'.$aRow['id'].'"') : '');
            $string_Row .= '<li '.(!empty($action_advisory) == 1 ? 'class="active"' : '').'>';
            $string_Row .= '    <a class="pointer '.(!empty($color_text) ? 'update_status_lead ' : '').$color_text.'"  title="'.$title_data_finish.'"  '.$date_from.'>';
            $string_Row .=          mb_convert_case($value['name'], MB_CASE_TITLE, "UTF-8").'</br>'.'<p class="'.$color_text.'">'.$title_data_finish.'</p>';
            $string_Row .= '    </a>';
            $string_Row .='</li>';
        }
    }
    $string_Row.='</ul>';
    $row[] = $string_Row;
    $output['aaData'][] = $row;
}
