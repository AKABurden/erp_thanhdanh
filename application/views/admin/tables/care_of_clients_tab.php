<?php

defined('BASEPATH') or exit('No direct script access allowed');
$tasksPriorities     = get_care_of_clients_priorities();
$this->ci->db->where('type', 'client');
$procedure_client = $this->ci->db->get(db_prefix().'procedure_client')->row();

if(!empty($procedure_client)) {
    $this->ci->db->where('id_detail', $procedure_client->id);
    $this->ci->db->order_by('orders', 'asc');
    $procedure_detail = $this->ci->db->get(db_prefix().'procedure_client_detail')->result_array();
}
$data_vip_rating = [
    '1' => _l('cong_1_start'),
    '2' => _l('cong_2_start'),
    '3' => _l('cong_3_start'),
    '4' => _l('cong_4_start'),
    '5' => _l('cong_5_start')
];


$aColumns = [
    'concat('.db_prefix().'care_of_clients.prefix,'.db_prefix().'care_of_clients.code) as fullcode',
    'date',
    'priority', // mức độ ưu tiên
    'rating', // Xếp loại khách hàng
    'id_orders', // Xếp loại khách hàng
    'theme_of', // Chủ đề chăm sóc
    'event_care_of', //Dịp đặc biệt chăm sóc khách hàng
    'solution', // giải pháp chăm soóc
    'staff_success', // Nhân viên hoàn thành chăm sóc
    db_prefix().'care_of_clients.date_create as date_create',
    db_prefix().'care_of_clients.create_by as create_by',
    'tblcare_of_clients.date_contact as date_contact' // ngày khashc hàng liên hệ
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'care_of_clients';
$where        = [];
if(!empty($id_client))
{
    $where[] = 'AND client = '.$id_client;
}
$filter = [];


$join[] = 'LEFT JOIN '.db_prefix().'staff cby on cby.staffid = '.db_prefix().'care_of_clients.create_by';
$join[] = 'LEFT JOIN '.db_prefix().'staff ss on ss.staffid = '.db_prefix().'care_of_clients.staff_success';
$join[] = 'JOIN '.db_prefix().'clients c on c.userid = '.db_prefix().'care_of_clients.client';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[
    db_prefix().'care_of_clients.id',
    'client',
    'cby.firstname as cbyfirstname',
    'cby.lastname as cbylastname',

    'ss.firstname as ssfirstname',
    'ss.lastname as sslastname',
    'date',
    'c.company as company',
    'status_break',
    'date_expected',
    'concat(c.prefix_client,c.code_client) as fullcode_client',
    db_prefix().'care_of_clients.status_first as status_first'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$count_success = 0;
$all_count = count($rResult);
foreach ($rResult as $aRow) {
    $row = [];
    $options = '<div class="row-options">';
    if($aRow['status_break'] != 1)
    {
        $options .= '<a href="#" class="text-success" onclick="restore_Care_of_clients('.$aRow['id'].', this); return false;">'._l('cong_restore').'</a> | ';
    }
    $options .= '<br/><a href="#" class="text-warning" onclick="BreakCare_of('.$aRow['id'].', '.($aRow['status_break'] == 1 ? 0 : 1).', this); return false;">'.($aRow['status_break'] == 1 ? _l('cong_restore_break_advisory') : _l('cong_break_advisory')).'</a> | ';
    $options .= '<a href="#" class="text-danger" onclick="deleteCare_of('.$aRow['id'].', this); return false;">'._l('delete').'</a>';
    $options .= '</div>';


    $row[] = '<p class="one-control pointer">'.$aRow['fullcode'].'</p>'.$options;
    $row[] = _d($aRow['date']);

//    $row[] = $aRow['priority']; // mức độ ưu tiên
    $info_priority =  care_of_priority($aRow['priority']);
    $outputPriority = '<span style="color:' .$info_priority['id'] . ';" class="inline-block">' . $info_priority['name'];

        $outputPriority .= '<div class="dropdown inline-block mleft5 dropup">';
        $outputPriority .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskPriority-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputPriority .= '<span data-toggle="tooltip" title="' . _l('task_single_priority') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputPriority .= '</a>';

        $outputPriority .= '<ul class="dropdown-menu dropdown-menu-bottom" aria-labelledby="tableTaskPriority-' . $aRow['id'] . '" style="">';
        foreach ($tasksPriorities as $priority) {
            if ($aRow['priority'] != $priority['id']) {
                $outputPriority .= '<li>
                  <a href="#" onclick="care_of_change_priority(' . $priority['id'] . ',' . $aRow['id'] . ', this); return false;">
                     ' . $priority['name'] . '
                  </a>
               </li>';
            }
        }
        $outputPriority .= '</ul>';
        $outputPriority .= '</div>';

    $outputPriority .= '</span>';

    $row[] = $outputPriority;

    $row[] = $data_vip_rating[$aRow['rating']]; // Xếp loại khashc hàng
    $row[] = !empty($aRow['id_orders']) ? $aRow['id_orders'] : ''; // Đơn hàng

    $row[] = $aRow['theme_of']; // Chủ đề chăm sóc
    $row[] = $aRow['event_care_of']; //Dịp đặc biệt chăm sóc khách hàng
    $row[] = $aRow['solution']; // giải pháp chăm soóc
//    $row[] = $aRow['staff_success'];

    // Nhân viên hoàn thành chăm sóc
    $profile_Scussess = "";
    if(!empty($aRow['staff_success']))
    {
        $fullname_Scussess = $aRow['sslastname'] . ' ' . $aRow['ssfirstname'];
        $profile_Scussess = '<a data-toggle="tooltip" data-title="' . $fullname_Scussess . '" href="' . admin_url('profile/' . $aRow['staff_success']) . '">' . staff_profile_image($aRow['staff_success'], [
                'staff-profile-image-small',
            ]) . '</a>';
        $profile_Scussess .= '<span class="hide">' . $fullname_Scussess . '</span>';
    }
    $row[] = $profile_Scussess;


    $row[] = _dt($aRow['date_create']);
    $fullname_CREATE = $aRow['cbylastname'] . ' ' . $aRow['cbyfirstname'];
    $profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
            'staff-profile-image-small',
        ]) . '</a>';
    $profile_CREATE .= '<span class="hide">' . $fullname_CREATE . '</span>';
    $row[] = $profile_CREATE;
    $row[] = _dt($aRow['date_contact']); // ngày khashc hàng liên hệ

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

        $action_care_of = get_table_where(db_prefix().'procedure_care_of', [
            'id_care_of' => $aRow['id'],
            'status_procedure' => $value['id']
        ], '', 'row');

        if($status_first == 1)
        {
            $title_data_finish = "";
            if(!empty($action_care_of->date_create))
            {
                $title_data_finish = _l('cong_Finish').' ('._d($action_care_of->date_create).')';
                $date_expected = $action_care_of->date_create;
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

            $string_Row .= '<li '.(!empty($action_care_of) == 1 ? 'class="active"' : '').'>';
            $string_Row .= '    <a class="pointer '.(!empty($color_text) ? 'update_status_client ' : '').$color_text.'"  title="'.$title_data_finish.'"  status-procedure="'.$value['id'].'" id-data="'.$aRow['id'].'">';
            $string_Row .= '    <a class="pointer '.(!empty($color_text) ? 'update_status_client ' : '').$color_text.'"  title="'.$title_data_finish.'"  status-procedure="'.$value['id'].'" id-data="'.$aRow['id'].'">';
            $string_Row .=          mb_convert_case($value['name'], MB_CASE_TITLE, "UTF-8").'</br>'.'<p class="'.$color_text.'">'.$title_data_finish.'</p>';
            $string_Row .=      '</a>';
            $string_Row .='</li>';
        }
    }
    $string_Row.='</ul>';
    $row[] = $string_Row;
    $output['aaData'][] = $row;
    $output['count_detail'] = count($procedure_detail);
}
