<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Process_production_report extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if (!is_admin()) {
            access_denied('customers');
        }
        $data['title'] = _l('Quy trình báo cáo không phù hợp');
        $data['process_production_report'] = get_table_where('tbl_process');
        $this->load->view('admin/process_production_report/manage', $data);
    }
    public function getProcess()
    {

        $aColumns = [
            'tbl_process.id as id',
            'tbl_process.name as name',
            '"" as actions',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_process';
        $where        = [];
        $filter = [];

        $join = [];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'ORDER BY tbl_process.id asc', []);

        $aColumns = handlingColumns($aColumns);
        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $row = [];
            $id = $aRow['id'];

            $edit = '<a class="tnh-modal" href="' . base_url('admin/process_production_report/handlingProcess/' . $id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/process_production_report/delete/' . $id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
                       ' . $start . '
                    </div>';
                } else {
                    $_data = '<div class="">' . $_data . '</div>';
                }

                $row[] = $_data;
            }


            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
    public function getProcessById($id)
    {
        $this->db->select('*');
        $this->db->from('tbl_process');
        $this->db->where('tbl_process.id', $id);
        return $this->db->get()->row_array();
    }
    public function insertProcess($data)
    {
        $this->db->insert('tbl_process', $data);
        return $this->db->insert_id();
    }

    public function updateProcess($id, $data)
    {
        $this->db->where('tbl_process.id', $id);
        return $this->db->update('tbl_process', $data);
    }

    public function deleteProcess($id)
    {
        $this->db->where('tbl_process.id', $id);
        return $this->db->delete('tbl_process');
    }
    function removeDiacritics($str)
    {
        $trans = array(
            'à' => 'a',
            'á' => 'a',
            'ạ' => 'a',
            'ả' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'ầ' => 'a',
            'ấ' => 'a',
            'ậ' => 'a',
            'ẩ' => 'a',
            'ẫ' => 'a',
            'ă' => 'a',
            'ằ' => 'a',
            'ắ' => 'a',
            'ặ' => 'a',
            'ẳ' => 'a',
            'ẵ' => 'a',
            'è' => 'e',
            'é' => 'e',
            'ẹ' => 'e',
            'ẻ' => 'e',
            'ẽ' => 'e',
            'ê' => 'e',
            'ề' => 'e',
            'ế' => 'e',
            'ệ' => 'e',
            'ể' => 'e',
            'ễ' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'ị' => 'i',
            'ỉ' => 'i',
            'ĩ' => 'i',
            'ò' => 'o',
            'ó' => 'o',
            'ọ' => 'o',
            'ỏ' => 'o',
            'õ' => 'o',
            'ô' => 'o',
            'ồ' => 'o',
            'ố' => 'o',
            'ộ' => 'o',
            'ổ' => 'o',
            'ỗ' => 'o',
            'ơ' => 'o',
            'ờ' => 'o',
            'ớ' => 'o',
            'ợ' => 'o',
            'ở' => 'o',
            'ỡ' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'ụ' => 'u',
            'ủ' => 'u',
            'ũ' => 'u',
            'ư' => 'u',
            'ừ' => 'u',
            'ứ' => 'u',
            'ự' => 'u',
            'ử' => 'u',
            'ữ' => 'u',
            'ỳ' => 'y',
            'ý' => 'y',
            'ỵ' => 'y',
            'ỷ' => 'y',
            'ỹ' => 'y',
            'đ' => 'd',
            'Đ' => 'd'
        );
        return strtr($str, $trans);
    }

    function cleanString($str)
    {
        // Loại bỏ dấu
        $str = $this->removeDiacritics($str);

        // Loại bỏ các ký tự đặc biệt và khoảng trắng
        $str = preg_replace('/[^a-zA-Z0-9]/', '', $str);

        return $str;
    }
    public function handlingProcess($id = 0, $status = 0)
    {
        $data = [];
        $process = $id ? $this->getProcessById($id) : [];
        if ($this->input->post('save')) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $name = _string($this->input->post('name'));
                $code = $this->removeDiacritics($name);
                $option = [
                    'code' => $code,
                    'name' => $name,
                ];

                if ($id) {
                    $ins = $this->updateProcess($id, $option);
                    $_id = $id;
                } else {
                    $ins = $this->insertProcess($option);
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
        $data['process'] = $process;
        $title = '';
        $title = $id ? lang('Sửa quy trình') : lang('Thêm quy trình');

        $data['title'] = $title;
        $this->load->view('admin/process_production_report/handling', $data);
    }
    function update_process_production_report()
    {
        die;
        $data = get_table_where('tbl_process_production_report');
        $process = get_table_where('tbl_process');
        $_process = [];
        foreach ($process as $key => $value) {
            $_process[$value['id']] = $value['name'];
        }
        foreach ($data as $key => $value) {
            $this->db->where('id', $value['id']);
            $this->db->update('tbl_process_production_report', ['name' => $_process[$value['process_id']]]);
        }
    }
    function add_process_production_report()
    {
        die;
        $query = 'SELECT COUNT(*) AS `dem`, `production_report_id` FROM `tbl_process_production_report` GROUP BY `production_report_id` HAVING dem < 9';
        $data = $this->db->query($query)->result_array();
        // $data = get_table_where('tblproduction_report');
        $process = get_table_where('tbl_process');
        // $_process = [];

        foreach ($data as $key => $value) {
            foreach ($process as $k => $v) {
                $process_production_report = get_table_where('tbl_process_production_report', array('production_report_id' => $value['production_report_id'], 'process_id' => $v['id']), '', 'row_array');
                if (empty($process_production_report)) {
                    $ins = [];
                    $ins['production_report_id'] = $value['production_report_id'];
                    $ins['process_id'] = $v['id'];
                    $ins['name'] = $v['name'];
                    $this->db->insert('tbl_process_production_report', $ins);
                }
            }
        }
    }
    function add_role_production_report()
    {
        die;
        $query = 'SELECT * FROM `tblproduction_report` WHERE NOT EXISTS (SELECT 1 FROM tbl_process_production_report WHERE tbl_process_production_report.production_report_id = tblproduction_report.id)';
        $data = $this->db->query($query)->result_array();
        $process = get_table_where('tbl_process');
        // $_process = [];
        foreach ($data as $key => $value) {
            foreach ($process as $k => $v) {
                $process_production_report = get_table_where('tbl_process_production_report', array('production_report_id' => $value['id'], 'process_id' => $v['id']), '', 'row_array');
                if (empty($process_production_report)) {
                    $ins = [];
                    $ins['production_report_id'] = $value['id'];
                    $ins['process_id'] = $v['id'];
                    $ins['name'] = $v['name'];
                    $this->db->insert('tbl_process_production_report', $ins);
                }
            }
        }
    }
    function new_role_production_report()
    {
        die;
        $data = get_table_where('tbl_setting_production_report');
        $production_report = get_table_where('tblproduction_report');
        // $_process = [];

        foreach ($production_report as $key => $value) {
            foreach ($data as $k => $v) {
                $ins = [];
                $ins['id_process'] = $v['id_process'];
                $ins['id_role'] = $v['id_role'];
                $ins['id_production_report'] = $value['id'];
                $this->db->insert('tbl_role_production_report', $ins);
            }
        }
    }
    // function process_production_report(): Returntype {}
    public function delete($id)
    {
        $data = [];
        if ($this->deleteProcess($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}
