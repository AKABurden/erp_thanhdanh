<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Prchat_model extends App_Model
{
    /**
     * Get chat staff members
     * @return mixed
     */
    public function getUsers()
    {
        $this->db->select('staffid, firstname, lastname, profile_image, last_login');
        $users = $this->db->get(db_prefix().'staff')->result_array();
        if ($users) {
            return $users;
        }
        return false;
    }

    /**
     * Get logged in staff profile image
     * @param  mixed
     * @return mixed
     */
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

    /**
     * Create message
     * @param  data
     * @return boolean
     */
    public function createMessage($data)
    {
        if ($this->db->insert(db_prefix().'chatmessages', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Get staff firstname and lastname
     * @param  mixed
     * @return mixed
     */
    public function getStaffInfo($id)
    {
        $this->db->select('firstname,lastname');
        $this->db->where('staffid', $id);
        $result = $this->db->get(db_prefix().'staff')->row();
        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * @param  $from sender
     * @param  $to receiver
     * @param  $limit limit messages
     * @param  $offet offet
     * @return mixed
     */
    public function getMessages($from, $to, $limit, $offset)
    {
        $sql = "SELECT * FROM ".db_prefix()."chatmessages WHERE (sender_id = {$to} AND reciever_id = {$from}) OR (sender_id = {$from} AND reciever_id = {$to}) ORDER BY id DESC LIMIT {$offset}, {$limit}";

        $query = $this->db->query($sql)->result();

        foreach ($query as &$chat) {
            $chat->message             =  pr_chat_convertLinkImageToString($chat->message, $chat->sender_id, $chat->reciever_id);
            $chat->message             =  check_for_links($chat->message);
            $chat->user_image          = $this->getUserImage($chat->sender_id);
            $chat->sender_fullname     = get_staff_full_name($chat->sender_id);
            $chat->time_sent_formatted = _dt($chat->time_sent);
        }

        if ($query) {
            return $query;
        }

        return false;
    }

    /**
     * Get unread messages for the logged in user
     */
    public function getUnread()
    {
        $unreadMessages            = array();
        $staff_id = get_staff_user_id();

        if(empty($staff_id)) {
            $staff_id = 0;
        }
        
        // $unreadMessages['success'] = true;
        $sql                 = "SELECT sender_id FROM ".db_prefix()."chatmessages WHERE(reciever_id = $staff_id AND viewed = 0)";

        $query = $this->db->query($sql);

        $result = $query->result_array();

        foreach ($result as $sender) {
            $sender_id = 'sender_id_' . $sender['sender_id'];
            if (array_key_exists($sender_id, $unreadMessages)) {
                $unreadMessages['' . $sender_id . '']['count_messages'] = $unreadMessages['' . $sender_id . '']['count_messages'] + 1;
            } else {
                $unreadMessages['' . $sender_id . ''] = array('sender_id' => $sender['sender_id'], 'count_messages' => 1);
            }
        }
        if ($result) {
            return $unreadMessages;
        }

        return false;
    }

    /**
     * Update unread for sender
     * @param  mixed $id sender id
     * @return mixed
     */
    public function updateUnread($id)
    {
        $staff_id = get_staff_user_id();
        $sql   = "UPDATE ".db_prefix()."chatmessages SET viewed = 1 WHERE (reciever_id = $staff_id AND sender_id = {$id})";
        $query = $this->db->query($sql);
        if ($query) {
            return $query;
        }
        return false;
    }

    /**
     * Set the chat color
     * @param mixed $id the staff id
     * @param string $color the color to set
     */
    public function setChatColor($id, $color)
    {
        if ($this->db->field_exists('value', db_prefix().'chatsettings')) {
            $this->db->where('user_id', $id);
            $name = 'chat_color';
            $exsists = $this->db->get(db_prefix().'chatsettings')->row();
            if (!$exsists == null) {
                $this->db->where('user_id', $id);
                $this->db->update(db_prefix().'chatsettings', array('name' => $name, 'value' => $color));
            } else {
                $this->db->insert(db_prefix().'chatsettings', array('name' => $name, 'value' => $color, 'user_id' => $id));
            }
            if ($this->db->affected_rows() != 0) {
                $message['success'] = $color;
                return $message;
            }
            $message['success'] = false;
            return $message;
        } else {
            $this->db->where('user_id', $id);
            $exsists = $this->db->get(db_prefix().'chatsettings')->row();
            if (!$exsists == null) {
                $this->db->where('user_id', $id);
                $this->db->update(db_prefix().'chatsettings', array('chat_color' => $color));
            } else {
                $this->db->insert(db_prefix().'chatsettings', array('chat_color' => $color, 'user_id' => $id));
            }
            if ($this->db->affected_rows() != 0) {
                $message['success'] = $color;
                return $message;
            }
            $message['success'] = false;
            return $message;
        }
    }

    /**
     * Delete chat messages
     * @param mixed $id the staff id
     * @return boolean
     */
    public function deleteMessage($id)
    {
        $staff_id = get_staff_user_id();
        $possible_image = $this->db->select()->where('id', $id)->get(db_prefix().'chatmessages')->row()->message;

        if (prchat_checkMessageIfImageExists($possible_image)) {
            $image_name = getImageFullName($possible_image);
            if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
                unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/' . $image_name);
            }
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'chatmessages', [
        'is_deleted'=>1,
        'message'=>''
    ]);

        return true;
    }
}

/* End of file PRChat_model.php */
/* Location: ./modules/prchat/models/perfex_chat/PRChat_model.php */
