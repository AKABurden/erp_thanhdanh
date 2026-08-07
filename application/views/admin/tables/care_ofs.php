<?php

defined('BASEPATH') or exit('No direct script access allowed');


if(!empty($this->ci->input->post('date_now')))
{
    $where        = [
        'AND DATE_FORMAT(birtday,"%m-%d") = "'.date('m-d').'"'
    ];
}
else
{
    $where   = [];
    if($this->ci->input->post('date_start'))
    {
        $date_start = to_sql_date($this->ci->input->post('date_start'));
        $time = getdate(strtotime($date_start));
        $where[] =  'AND DATE_FORMAT(birtday,"%m-%d") >= "'.(sprintf("%02s", $time["mon"]).'-'.$time["mday"]).'"';
    }
    if($this->ci->input->post('date_end'))
    {
        $date_end = to_sql_date($this->ci->input->post('date_end'));
        $time = getdate(strtotime($date_end));
        $where[] =  'AND DATE_FORMAT(birtday,"%m-%d") <= "'.(sprintf("%02s", $time["mon"]).'-'.$time["mday"]).'"';
    }

}

if(!empty($this->ci->input->post('search')))
{
    $search = $this->ci->input->post('search');
    $where_client = '';
    $where_contact = '';
    if(!empty($search['value']))
    {
        $where_contact .= ' (';
        $where_contact .=  'lastname like "%'.$search['value'].'%"';
        $where_contact .=  ' or phonenumber like "%'.$search['value'].'%"';
        $where_contact .=  ' or birtday like "%'.$search['value'].'%"';
        $where_contact .=  ' or email like "%'.$search['value'].'%"';
        $where_contact .= ')';
    }
}

$start = $this->ci->input->post('start');
$length = $this->ci->input->post('length');


$new_where = trim(implode(' ',$where), 'AND');



// lấy tất cả khách hàng và liên hệ thỏa điều kiện
$All_data = $this->ci->db->query('
    SELECT
        0 as company,
        concat(lastname," ",firstname) as fullname,
        phonenumber,
        birtday,
        email as email_client,
        userid,
        id
    FROM '.db_prefix().'contacts '.(!empty($where) ? (' where '.$new_where.(!empty($where_contact) ? ' AND '.$where_contact : '') ) : (!empty($where_contact) ? ('where '.$where_contact) : '') )
)->result_array();

// lấy giới hạn khách hàng và liên hệ
$limit_data = $this->ci->db->query('
    SELECT 
        0 as company,
        concat(lastname," ",firstname) as fullname,
        phonenumber,
        birtday,
        email as email_client,
        userid,
        id
    FROM '.db_prefix().'contacts '.(!empty($where) ? (' where '.$new_where.(!empty($where_contact) ? ' AND '.$where_contact : '') ) : (!empty($where_contact) ? ('where '.$where_contact) : '') ).'  limit '.$start.','.$length
)->result_array();

$draw = $this->ci->input->post('draw');
$result = array(
    'rResult' => $limit_data,
    'output' => array(
                    "draw" => $draw,
                    "iTotalRecords" => count($All_data),
                    "iTotalDisplayRecords" => count($All_data),
                    "aaData" => array()
    )

);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $key => $aRow) {
    $row = [];

    $row[] = '<div class="checkbox"><input type="checkbox" email="'.(!empty($aRow['email_client']) ? $aRow['email_client'] : '').'" class="'.(!empty($aRow['id']) ? 'check_contact data-contact-'.$aRow['id'] : 'check_client data-client-'.$aRow['userid']).'" value="'.(!empty($aRow['id']) ? $aRow['id'] : $aRow['userid']).'"><label></label></div>';

    $getContact = get_table_where('tblcontacts',array('id'=>$aRow['id']),'','row');
    $row[]    = (!empty($getContact->firstname) ? $getContact->firstname : '') . (!empty($getContact->lastname) ? $getContact->lastname : '');

    $getCompany = get_table_where('tblclients',array('userid'=>$aRow['userid']),'','row');
    if(!empty($aRow['id']))
    {
        $row[]    = '<a href="'.admin_url('clients/client/'.$aRow['userid'].'?group=contacts&contactid='.$aRow['id']).'" class="'.(!empty($aRow['id'])?'text-danger ':'').'">'.$getCompany->company.'</a>';
    }
    else
    {
        $row[]    = '<a href="'.admin_url('clients/client/'.$aRow['userid']).'?group=profile">'.$getCompany->company.'</a>';

    }
    $row[]    = !empty($aRow['birtday']) ? _d($aRow['birtday']) : '';
    $row[]    = !empty($aRow['phonenumber'])? $aRow['phonenumber'] : '';
    $row[]    = !empty($aRow['email_client']) ? $aRow['email_client'] : '';



    //1 là liên hệ
    //0 là khách hàng

    $option   = '<button type="button" class="btn btn-icon btn-info" onclick="SendEmail('.(!empty($aRow['id']) ? $aRow['id'] : $aRow['userid']).', '.(!empty($aRow['id']) ? '1':'0').')" title="'._l('cong_send_email').'"><i class="lnr lnr-envelope"></i></button>';
    $option   .= '<button type="button" class="btn btn-icon btn-info" onclick="SendSMS('.(!empty($aRow['id']) ? $aRow['id'] : $aRow['userid']).', '.(!empty($aRow['id']) ? '1':'0').')" title="'._l('cong_send_sms').'"><i class="lnr lnr-inbox"></i></button>';
    $row[]    = $option;

    $this->ci->db->select('count(id) as count_send');
    $this->ci->db->where('email', $aRow['email_client']);
    $this->ci->db->where('type', 'care_ofs');
    if(!empty($this->ci->input->post('date_now'))) {
        $where[] = 'AND DATE_FORMAT(date_create,"%Y-%m-%d") = "' . date('Y-m-d') . '"';
    }
    else
    {
        if($this->ci->input->post('date_start'))
        {
            $date_start = to_sql_date($this->ci->input->post('date_start'));
            $where[] =  'AND DATE_FORMAT(date_create,"%Y-%m-%d") >= "'.$date_start.'"';
        }
        if($this->ci->input->post('date_end'))
        {
            $date_end = to_sql_date($this->ci->input->post('date_end'));
            $time = getdate(strtotime($date_end));
            $where[] =  'AND DATE_FORMAT(date_create,"%Y-%m-%d") <= "'.$date_end.'"';
        }
    }

    $count_Send = $this->ci->db->get('tbllog_send_email')->row();
    if(!empty($count_Send->count_send))
    {
        $row['DT_RowClass'] = 'alert-success';
    }

    $output['aaData'][] = $row;
}
