<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Defined styling areas for the theme style feature
 * Those string are not translated to keep the language file neat
 * @param  string $type
 * @return array
 */

function getFullDataTag()
{
    $CI = &get_instance();
    $taggables = $CI->db->get('tbltagsfb')->result_array();
    $arrayData = [
        'id' => [],
        'name' => [],
        'color' => [],
        'background_color' => []
    ];
    if(!empty($taggables))
    {
        foreach($taggables as $key => $value)
        {
            $arrayData['id'][] = $value['id'];
            $arrayData['name'][] = $value['name'];
            $arrayData['color'][] = $value['color'];
            $arrayData['background_color'][] = $value['background_color'];
        }
    }
    return $arrayData;
}

function get_tagsFB_table()
{
    $CI = &get_instance();
    $CI->db->order_by('id', 'asc');
    $tags = $CI->db->get('tbltagsfb')->result_array();

    return $tags;
}

function GetDataTag($rel_id = "", $rel_type = "")
{
    $CI = &get_instance();
    $CI->db->select('group_concat(name) as rowName');
    $CI->db->where('rel_id', $rel_id)->where('rel_type', $rel_type);
    $CI->db->join(db_prefix().'tagsfb t', 't.id = tbltaggablesfb.tag_id');
    $CI->db->group_by('rel_id');
    $taggables = $CI->db->get('tbltaggablesfb')->row();
    if(!empty($taggables))
    {
        return  $taggables->rowName;
    }
    return  '';
}

function getAssignedLead($lead = "")
{
    if(!empty($lead))
    {
        $CI = &get_instance();
        $CI->db->select('group_concat(staff) as list_staff');
        $CI->db->where('id_lead', $lead);
        $CI->db->group_by('id_lead');
        $lead_assigned = $CI->db->get(db_prefix().'lead_assigned')->row();
        if(!empty($lead_assigned))
        {
            return $lead_assigned;
        }
    }
    return false;
}

function getAssignedClient($client = "")
{
    if(!empty($client))
    {
        $CI = &get_instance();
        $CI->db->select('group_concat(staff_id) as list_staff');
        $CI->db->where('customer_id', $client);
        $client_assigned = $CI->db->get(db_prefix().'customer_admins')->row();
        if(!empty($client_assigned))
        {
            return $client_assigned;
        }
    }
    return false;
}

function getAssignedListFb($listid = "")
{
    if(!empty($listid))
    {
        $CI = &get_instance();
        $CI->db->select('group_concat(staff) as list_staff');
        $CI->db->where('id_listfb', $listid);
        $CI->db->group_by('id_listfb');
        $list_assigned = $CI->db->get(db_prefix().'listfb_assigned')->row();
        if(!empty($list_assigned))
        {
            return $list_assigned;
        }
    }
    return false;
}

function getInfoTagFacebook($id_facebook = "")
{
    $CI = &get_instance();
    if(!empty($id_facebook))
    {
        $CI->db->where('id_facebook', $id_facebook);
        $client = $CI->db->get(db_prefix().'clients')->row();
        if(!empty($client))
        {
            $CI->db->where('rel_id', $client->userid);
            $CI->db->where('rel_type', 'client');
            $CI->db->join(db_prefix().'tagsfb', db_prefix().'tagsfb.id = '.db_prefix().'taggablesfb.tag_id');
            $tag = $CI->db->get(db_prefix().'taggablesfb')->result_array();
            return $tag;
        }
        else
        {
            $CI->db->where('id_facebook', $id_facebook);
            $lead = $CI->db->get(db_prefix().'leads')->row();
            if(!empty($lead))
            {
                $CI->db->where('rel_id', $lead->id);
                $CI->db->where('rel_type', 'lead');
                $CI->db->join(db_prefix().'tagsfb', db_prefix().'tagsfb.id = '.db_prefix().'taggablesfb.tag_id');
                $tag = $CI->db->get(db_prefix().'taggablesfb')->result_array();
                return $tag;
            }
            else
            {
                $CI->db->where('id_facebook', $id_facebook);
                $listfb = $CI->db->get(db_prefix().'list_fb')->row();
                if(!empty($listfb))
                {
                    $CI->db->where('rel_id', $listfb->id);
                    $CI->db->where('rel_type', 'listfb');
                    $CI->db->join(db_prefix().'tagsfb', db_prefix().'tagsfb.id = '.db_prefix().'taggablesfb.tag_id');
                    $tag = $CI->db->get(db_prefix().'taggablesfb')->result_array();
                    return $tag;
                }
            }
        }
    }
    return false;
}

function getInfoIdFacebook($id_facebook = "")
{
    $CI = &get_instance();
    $RTArray = [
        'phone' => '',
        'orders' => '',
        'assigned' => '',
    ];
    $CI->db->where('id_facebook', $id_facebook);
    $client = $CI->db->get('tblclients')->row();
    if(!empty($client))
    {
        $RTArray['phone'] = $client->phonenumber;
        $CI->db->where('client', $client->userid);
        $orders = $CI->db->get('tblorders')->row();
        if(!empty($orders))
        {
            $RTArray['orders'] = 1;
        }

        $CI->db->select('group_concat(staff_id) as assigned');
        $CI->db->where('customer_id', $client->userid);
        $CI->db->group_by('customer_id');
        $assigned = $CI->db->get('tblcustomer_admins')->row();
        if(!empty($assigned->assigned)) {
            $RTArray['assigned'] = $assigned->assigned;
        }
    }
    else
    {
        $CI->db->where('id_facebook', $id_facebook);
        $Lead = $CI->db->get('tblleads')->row();
        if(!empty($Lead))
        {
            $RTArray['phone'] = $Lead->phonenumber;
            $CI->db->select('group_concat(staff) assigned');
            $CI->db->where('id_lead', $Lead->id);
            $CI->db->group_by('id_lead');
            $assigned = $CI->db->get('tbllead_assigned')->row();
            if(!empty($assigned->assigned))
            {
                $RTArray['assigned'] = $assigned->assigned;
            }
        }
        else
        {
            $CI->db->where('id_facebook', $id_facebook);
            $ListFB = $CI->db->get('tbllist_fb')->row();
            if(!empty($ListFB))
            {
                $RTArray['phone'] = $ListFB->phonenumber;
                $CI->db->select('group_concat(staff) assigned');
                $CI->db->where('id_listfb', $ListFB->id);
                $CI->db->group_by('id_listfb');
                $assigned = $CI->db->get('tbllistfb_assigned')->row();
                if(!empty($assigned->assigned))
                {
                    $RTArray['assigned'] = $assigned->assigned;
                }
            }
        }
    }
    return $RTArray;
}
