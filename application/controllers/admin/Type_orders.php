<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Type_orders extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('type_orders_model');
        $this->load->model('misc_model');
        $this->types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('type_orders');
        $this->datetime_now = time();
    }

    public function index()
    {
        $data['title'] = lang('tnh_type_orders');
        $this->load->view('admin/type_orders/index', $data);
    }

    public function add($id = 0) {
        $data = [];
        $type_orders = $id ? $this->type_orders_model->getTypeOrdersById($id) : [];
        if ($this->input->post('save')) {
            if ((!empty($type_orders) && $type_orders['code'] != $this->input->post('code')) || empty($type_orders['code'])) {
                $this->form_validation->set_rules('code', lang("tnh_code_type_orders"), 'required|is_unique[tbl_type_orders.code]');
            }
            $this->form_validation->set_rules('name', lang("tnh_name_type_orders"), 'required');
            $this->form_validation->set_rules('color', lang("tnh_colors"), 'required');
            if ($this->form_validation->run() == true)
            {
                $code = $this->input->post('code');
                $name = $this->input->post('name');
                $color = $this->input->post('color');

                $option = [
                    'code' => $code,
                    'name' => $name,
                    'color' => $color,
                ];

                if ($id) {
                    $ins = $this->type_orders_model->updateTypeOrders($id, $option);
                    $type_orders_id = $id;
                } else {
                    $ins = $this->type_orders_model->insertTypeOrders($option);
                    $type_orders_id = $ins;
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

        $data['type_orders'] = $type_orders;
        $data['id'] = $id;
        $data['title'] = $id ? lang('tnh_edit_type_orders') : lang('tnh_add_type_orders');
        $this->load->view('admin/type_orders/add', $data);
    }

    public function getTypeOrders() {

        $aColumns = [
            'tbl_type_orders.id as id',
            'tbl_type_orders.code as code',
            'tbl_type_orders.name as name',
            'tbl_type_orders.color as color',
            // 'tbl_type_orders.type as type',
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tbl_type_orders';
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
            $row[3] = '<div style="background:'.$aRow['color'].';">'.$aRow['color'].'</div>';

            // $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/type_orders/view/'.$id) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('tnh_view') . '</a>';
            $edit = '<a class="tnh-modal" href="' . base_url('admin/type_orders/add/'.$id) . '"><i class="fa fa-edit"></i> ' . lang('tnh_edit_type_orders') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/type_orders/delete/'.$id) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('tnh_delete_type_orders') . '</a>';
            // <li class="not-outside">' . $delete . '</li>

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

            $row[4] = $actions;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function delete($id) {
        $data = [];
        $isUseTypeOrders = $this->type_orders_model->isUseTypeOrders($id);
        if (!empty($isUseTypeOrders)) {
            $data['result'] = 0;
            $data['message'] = lang('tnh_exist_not_delete');
            echo json_encode($data); die;
        }

        if ($this->type_orders_model->deleteTypeOrders($id)) {
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }
}