<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Status_orders extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('status_orders_model');
        $this->load->model('misc_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('status_orders');
        $this->datetime_now = time();
    }

    public function index()
    {
        $data['title'] = lang('tnh_status_orders');
        $this->load->view('admin/status_orders/index', $data);
    }

    public function add($id = 0) {
        $data = [];
        $status_orders = $id ? $this->status_orders_model->getStatusOrdersById($id) : [];
        if ($this->input->post('save')) {
            if ((!empty($status_orders) && $status_orders['code'] != $this->input->post('code')) || empty($status_orders['code'])) {
                $this->form_validation->set_rules('code', lang("tnh_code_status_orders"), 'required|is_unique[tbl_status_orders.code]');
            }
            $this->form_validation->set_rules('name', lang("tnh_name_status_orders"), 'required');
            if ($this->form_validation->run() == true)
            {
                $code = $this->input->post('code');
                $name = $this->input->post('name');
                $time = $this->input->post('time');
                $color = $this->input->post('color');

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'time' => $time,
                    'color' => $color,
                ];

                if ($id) {
                    $ins = $this->status_orders_model->updateStatusOrders($id, $option);
                    $status_orders_id = $id;
                } else {
                    $ins = $this->status_orders_model->insertStatusOrders($option);
                    $status_orders_id = $ins;
                }

                if (!empty($ins)) {
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

        $data['status_orders'] = $status_orders;
        $data['id'] = $id;
        $data['title'] = $id ? lang('tnh_edit_status_orders') : lang('tnh_add_status_orders');
        $this->load->view('admin/status_orders/add', $data);
    }

    public function getStatusOrders() {

        $aColumns = [
            'tbl_status_orders.id as id',
            'tbl_status_orders.code as code',
            'tbl_status_orders.name as name',
            'tbl_status_orders.time as time',
            'tbl_status_orders.color as color',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_status_orders';
        $where        = [
        ];
        $filter = [];
        
        $join = [
        ];

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        $start = $this->input->post('start');
        foreach ($rResult as $key => $aRow) {
            $start++;
            $id = $aRow['id'];

            $row[0] = $id;
            $row[1] = $aRow['code'];
            $row[2] = $aRow['name'];
            $row[3] = '<div>'.$aRow['time'].'</div>';
            $row[4] = '<div style="background:'.$aRow['color'].';">'.$aRow['color'].'</div>';

            $edit = '<a class="tnh-modal" href="' . base_url('admin/status_orders/add/'.$id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_status_orders') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/status_orders/delete/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_status_orders') . '</a>';

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

            $row[5] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete($id) {
        $data = [];
        $isUseStatusOrders = $this->status_orders_model->isUseStatusOrders($id);
        if (!empty($isUseStatusOrders)) {
            $data['result'] = 0;
            $data['message'] = lang('tnh_exist_not_delete');
            echo json_encode($data); die;
        }

        if ($this->status_orders_model->deleteStatusOrders($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}