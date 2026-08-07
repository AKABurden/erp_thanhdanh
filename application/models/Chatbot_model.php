<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Chatbot_model extends App_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function create_session($userid = '')
    {
        if (empty($userid)) {
            $userid = get_staff_user_id();
        }
        $this->db->insert('chat_sessions', ['created_at' => date('Y-m-d H:i:s'), 'userid' => $userid]);
        return $this->db->insert_id();
    }

    public function save_message($session_id, $sender, $message)
    {
        $this->db->insert('chat_messages', [
            'session_id' => $session_id,
            'sender' => $sender,
            'message' => $message
        ]);
    }
    public function get_all_sessions()
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->get('chat_sessions')
            ->result();
    }
    public function get_messages($session_id)
    {
        return $this->db
            ->where('session_id', $session_id)
            ->order_by('created_at', 'ASC')
            ->get('chat_messages')
            ->result();
    }
    public function get_all_sessions_with_messages($userid = '')
    {
        return $this->db
            ->select('chat_sessions.id, chat_sessions.created_at')
            ->from('chat_sessions')
            ->where('userid', $userid)
            ->join('chat_messages', 'chat_messages.session_id = chat_sessions.id')
            ->group_by('chat_sessions.id')
            ->order_by('chat_sessions.created_at', 'DESC')
            ->get()
            ->result();
    }
    public function check_session_has_messages($session_id)
    {
        return $this->db
            ->where('session_id', $session_id)
            ->limit(1)
            ->count_all_results('chat_messages') > 0;
    }
    public function get_modules()
    {
        return $this->db->get('suggest_modules')->result();
    }

    public function get_groups_with_questions($module_id)
    {
        $groups = $this->db
            ->select('suggest_groups.*')
            ->where('module_id', $module_id)
            ->where('tbl_list_modules.active', 1)
            ->join('tbl_list_modules', 'tbl_list_modules.id = suggest_groups.id_list', 'left')
            ->get('suggest_groups')
            ->result();

        foreach ($groups as &$g) {
            $g->questions = $this->db
                ->where('group_id', $g->id)
                ->get('suggest_questions')
                ->result();
        }

        return $groups;
    }

    public function is_question_require_staff($question_id)
    {
        return $this->db
            ->where('id', $question_id)
            ->get('suggest_questions')
            ->row()->require_staff;
    }

    public function get_all_staff()
    {
        return $this->db->select('staffid, firstname, lastname')->get('tblstaff')->result();
    }
}
