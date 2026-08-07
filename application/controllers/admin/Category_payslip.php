<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_payslip extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = _l('dt_category_payslip');
        $this->load->view('admin/category_payslip/index', $data);
    }

    public function detail($id = 0){
        $data = [];
        $dtData = get_table_where('tbl_category_payslip',['id'=>$id],'','row_array');
        if ($this->input->post()){
            if ((!empty($dtData) && $dtData['code'] != $this->input->post('code'))) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_payslip.code]');
            }
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $code = _string($this->input->post('code'));

                $option = [
                    'name' => $name,
                    'code' => $code,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];

                if ($id) {
                    $this->db->where('id',$id);
                    $ins = $this->db->update('tbl_category_payslip',$option);
                    $category_payslip_id = $id;
                } else {
                    $ins = $this->db->insert('tbl_category_payslip',$option);
                    $category_payslip_id = $ins;
                }

                if (!empty($category_payslip_id)) {
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            return;
        }
        if (empty($id)) {
            $data['title'] = _l('dt_add_category_payslip');
        } else {
            $data['title'] = _l('dt_edit_category_payslip');
        }
        $data['dtData'] = $dtData;
        $data['id'] = $id;
        $this->load->view('admin/category_payslip/detail',$data);
    }


    public function delete_category_payslip($id)
    {
        $data = [];
        $this->db->from('tbl_suggest_payslips_items');
        $this->db->where('tbl_suggest_payslips_items.category_payslip',$id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)){
            $data['result'] = 0;
            $data['message'] = lang('Danh mục chi đã được sử dụng !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_category_payslip');
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function get_exsit($id = '')
    {
        $items = get_table_where('tblpay_slip', array('id_costs' => $id), '', 'row');
        $itemss = get_table_where('tblother_payslips', array('id_costs' => $id), '', 'row');
        if (!empty($items) || !empty($itemss)) {
            echo json_encode(true);
            die;
        } else {
            $parent = get_table_where('tblcosts', array('costs_parent' => $id), '', 'row');
            if (!empty($parent)) {
                echo json_encode(true);
                die;
            }
            $success = $this->db->delete('tblcosts', array('id' => $id));
            if ($success) {
                $success = true;
                $message = _l('ch_delete_successfuly');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
            die;
        }
    }

    public function getCategoryPayslip()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_category_payslip.id as id',
            'tbl_category_payslip.code as code',
            'tbl_category_payslip.name as name',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tbl_category_payslip';
        $where        = [
        ];
        $filter = [];
        $join = [];
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        $stt = 1;
        foreach ($rResult as $key => $aRow) {
            $start++;
            $category_payslip_id = $aRow['id'];
            $row = array();
            $row[] = '<div class="text-center">'.$stt.'</div>';
            $row[] = $aRow['code'];
            $row[] = $aRow['name'];
            $html = '<div><a class="tnh-modal btn btn-default btn-icon" href="'.base_url('admin/category_payslip/detail/'.$category_payslip_id.'').'"><i class="fa fa-pencil"></i></a>';
            $html .= ' <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/category_payslip/delete_category_payslip/'.$category_payslip_id.'') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                    "><i class="fa fa-remove"></i></button></div>';
            $row[] = $html;
            $output['aaData'][] = $row;
            $stt ++;
        }

        echo json_encode($output);
    }
}
