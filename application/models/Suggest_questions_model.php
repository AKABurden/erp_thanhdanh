<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_questions_model extends App_Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'suggest_questions';
    }

    public function get($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
}
