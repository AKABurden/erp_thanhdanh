<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Evaluate_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertEvaluate($data)
    {
        $this->db->insert('tbl_evaluate', $data);
        return $this->db->insert_id();
    }

    public function updateEvaluate($id, $data)
    {
        $this->db->where('tbl_evaluate.id', $id);
        return $this->db->update('tbl_evaluate', $data);
    }

    public function deleteEvaluate($id) {
        $this->db->where('tbl_evaluate.id', $id);
        return $this->db->delete('tbl_evaluate');
    }

    public function getEvaluateById($id) {
        $this->db->select('*');
        $this->db->from('tbl_evaluate');
        $this->db->where('tbl_evaluate.id', $id);
        return $this->db->get()->row_array();
    }

    public function remove_evaluate_attachment($id)
    {
        $comment_removed = false;
        $deleted         = false;
        // Get the attachment
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'files')->row();

        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath  = get_upload_path_by_type('evaluate') . $attachment->rel_id . '/';
                $fullPath = $relPath . $attachment->file_name;
                unlink($fullPath);
                $fname     = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext      = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath . $fname . '_thumb.' . $fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Evaluate Attachment Deleted [EvaluateID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('evaluate') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('evaluate') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('evaluate') . $attachment->rel_id);
                }
            }
        }

        if ($deleted) {
            
        }

        return ['success' => $deleted, 'comment_removed' => $comment_removed];
    }

    public function getFileEvaluate($evaluate_id) {
        $this->db->select('*');
        $this->db->from('tblfiles');
        $this->db->where('tblfiles.rel_id', $evaluate_id);
        $this->db->where('tblfiles.rel_type', 'evaluate');
        return $this->db->get()->result_array();
    }
}