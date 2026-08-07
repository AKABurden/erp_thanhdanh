<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_payslips_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getInternalProposalBySuggestPayslips($arrId, $key = false)
    {
        if (empty($arrId)) return null;

        $this->db->select('
            tblinternal_proposal.id as id,
            tblinternal_proposal.suggest_id as suggest_id,
            tblinternal_proposal.code as code,
        ', false);
        $this->db->from('tblinternal_proposal');
        $this->db->where('tblinternal_proposal.category_recommended_id', CR_SUGGEST_PAYSLIPS_ID);
        $this->db->where_in('tblinternal_proposal.suggest_id', $arrId);
        $internal_proposal = $this->db->get()->result_array();

        if ($internal_proposal && $key) {
            $internal_proposal = array_reduce($internal_proposal, function ($carry, $item) {
                $carry[$item['suggest_id']][] = $item;
                return $carry;
            });
        }

        return $internal_proposal;
    }

    public function getOptionSuggestPayslips($id = 0)
    {
        $options[0] = [
            'id' => 0,
            'name' => lang('Tất cả'),
        ];

        $options[1] = [
            'id' => 1,
            'name' => lang('Chưa duyệt'),
        ];

        $options[2] = [
            'id' => 2,
            'name' => lang('Đã duyệt'),
        ];

        $options[3] = [
            'id' => 3,
            'name' => lang('Chưa ĐXNB'),
        ];

        $options[4] = [
            'id' => 4,
            'name' => lang('Đã ĐXNB'),
        ];
        $options[5] = [
            'id' => 5,
            'name' => lang('Chưa xử lý'),
        ];
        if ($id) return $options[$id];
        return $options;
    }
}
