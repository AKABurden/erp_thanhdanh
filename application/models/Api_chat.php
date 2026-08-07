<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_chat extends App_Model
{
    public function getUsers($staffid='')
    {
        $this->db->select('staffid, firstname, lastname, profile_image, last_login');
        $this->db->where('staffid !=',$staffid);
        $users = $this->db->get(db_prefix() . 'staff')->result_array();
        if ($users) {
            foreach ($users as $key => $value) {
                $users[$key]['messages'] = $this->getMessages($staffid,$value['staffid'],1,0);
                if (!empty($value['profile_image'])) {

                    $users[$key]['profile_image'] = base_url('uploads/staff_profile_images/') . $value['staffid'] . '/' . $value['profile_image'];
                } else {
                    $users[$key]['profile_image'] = base_url('/assets/images/user-placeholder.jpg');
                }
                if(empty($users[$key]['messages']))
                {
                    unset($users[$key]);
                }
            }
            return $users;
        }
        return false;
    }
    public function getMessages($from, $to, $limit, $offset)
    {
        $sql = "SELECT * FROM ".db_prefix()."chatmessages WHERE (sender_id = {$to} AND reciever_id = {$from}) OR (sender_id = {$from} AND reciever_id = {$to}) ORDER BY id DESC LIMIT {$offset}, {$limit}";
        $query = $this->db->query($sql)->result();
        if ($query) {
            return $query;
        }

        return false;
    }
    public function getUserImage($id)
    {
        $CI = & get_instance();
        $CI->db->from(db_prefix().'staff');
        $CI->db->where('staffid', $id);
        $data = $CI->db->get()->row('profile_image');

        if ($data) {
            return $data;
        }

        return false;
    }
}
