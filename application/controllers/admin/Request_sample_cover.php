<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_sample_cover extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('request_sample_cover_model');
    }

    public function index() {
        $data = [];
        $data['title'] = lang('Phiếu yêu cầu bìa mẫu sản xuất');
        $data['status'] = 1;
        $this->load->view('admin/request_sample_cover/index', $data);
    }

    public function getRequestSampleCover()
    {
        $status_search = $this->input->post('status_search');

        $aColumns = [
            'tbl_request_sample_cover.id as id',
            'tbl_request_sample_cover.code as code',
            'tbl_request_sample_cover.date as date',
            'tbl_products.code as product_code',
            'tbl_products.name as product_name',
            'tblclients.company as company',
            'tbllist_other.standard as standard_code',
            'tbllist_other.standard as standard_name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_request_sample_cover';
        $where        = [];
        $filter = [];

        $join = [
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_request_sample_cover.product_id',
            'LEFT JOIN tblclients ON tblclients.userid = tbl_request_sample_cover.customer_id',
            'LEFT JOIN tbllist_other ON tbllist_other.id = tbl_request_sample_cover.sample_cover_id',
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/request_sample_cover/handlingRequestSampleCover/' . $id . '/' . $status_search) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_sample_cover/deleteRequestSampleCover/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . '</a>';

            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';


            $row = [];
            foreach ($aColumns as $k => $v) {
                $_data = $aRow[$v];
                if ($v == 'actions') {
                    $_data = $actions;
                } else if ($v == 'id') {
                    $_data = '<div class="text-center">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="id[]" id="check-item' . $id . '" value="' . $id . '">
                            <label for="check-item' . $id . '"></label>
                        </div>
                    </div>';
                } else {
                    $_data = '<div class="text-left">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function handlingRequestSampleCover($id = 0, $status = 0)
    {
        $data = [];
        $request_sample_cover = $id ? $this->request_sample_cover_model->getRequestSampleCover($id) : [];
        if ($this->input->post('save')) {
            if (empty($request_sample_cover) || (!empty($request_sample_cover) && (mb_strtolower($request_sample_cover['code']) != mb_strtolower($this->input->post('code'))))) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_request_sample_cover.code]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('product_id', lang("Sản phẩm"), 'required');
            $this->form_validation->set_rules('customer_id', lang("Khách hàng"), 'required');
            $this->form_validation->set_rules('sample_cover_id', lang("Bìa mẫu"), 'required');
            if ($this->form_validation->run() == true) {
                $code = _string($this->input->post('code'));
                $date = to_sql_Date($this->input->post('date'), true);
                $product_id = $this->input->post('product_id');
                $customer_id = $this->input->post('customer_id');
                $sample_cover_id = $this->input->post('sample_cover_id');

                $option = [
                    'code' => $code,
                    'date' => $date,
                    'product_id' => $product_id,
                    'customer_id' => $customer_id,
                    'sample_cover_id' => $sample_cover_id,
                ];

                if ($id) {
                    $ins = $this->request_sample_cover_model->updateRequestSampleCover($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->request_sample_cover_model->insertRequestSampleCover($option);
                    $_id = $ins;
                }

                if (!empty($_id)) {
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

        $data['id'] = $id;
        $data['status'] = $status;
        $data['request_sample_cover'] = $request_sample_cover;
        $title = '';
        $title = $id ? lang('Sửa phiếu yêu cầu bìa mẫu sản xuất') : lang('Thêm phiếu yêu cầu bìa mẫu sản xuất');

        $data['title'] = $title;
        $this->load->view('admin/request_sample_cover/handling', $data);
    }

    public function deleteRequestSampleCover($id)
    {
        $data = [];
        // Relate
        if ($this->request_sample_cover_model->deleteRequestSampleCover($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    function searchStandardSampleCode($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');

        $this->db->select('
            tbllist_other.id as id, 
            tbllist_other.standard as text, 
        ', false);
        $this->db->from('tbllist_other');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbllist_other.standard', $term);
            $this->db->group_end();
        }
        $this->db->where('type', 'standard_sample_code');
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();

        if ($id) {
            $standard_sample_code = get_table_where('tbllist_other', ['id' => $id], '', 'row_array', '');
            $data['row'] = ['id' => $standard_sample_code['id'], 'text' => $standard_sample_code['standard']];
        }
        echo json_encode($data);
    }
}