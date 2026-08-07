<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_repair extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('purchases_model');
        $this->load->model('business_plan_model');
        $this->load->model('orders_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('transfer_model');

        $this->preViewRequestRepair = true;
        $this->preViewOwnRequestRepair = true;
        $this->preAddRequestRepair = true;
        $this->preEditRequestRepair = true;
        $this->preApproveRequestRepair = true;
        $this->preDeleteRequestRepair = true;


        $this->type_repair = [
            1 => [
                'id' => 1,
                'name' => 'Sửa chữa'
            ],
            2 => [
                'id' => 2,
                'name' => 'Bảo dưỡng'
            ]
        ];

        $this->list_priority = [
            '1' => [
                'id' => 1,
                'name' => 'Thường',
                'class' => 'default'
            ],
            '2' => [
                'id' => 2,
                'name' => 'Cao',
                'class' => 'danger',
            ],
            '3' => [
                'id' => 3,
                'name' => 'Khẩn',
                'class' => 'warning',
            ],
        ];

        $this->list_step = [
            '1' => [
                'id' => 1,
                'name' => 'Kiểm tra đánh giá',
            ],
            '2' => [
                'id' => 2,
                'name' => 'Xử lý',
            ],
            '3' => [
                'id' => 3,
                'name' => 'Nghiệm thu',
            ],
        ];

        $this->type_processing = [
            '1' => [
                'id' => 1,
                'name' => 'Nội bộ',
            ],
            '2' => [
                'id' => 2,
                'name' => 'Thuê ngoài',
            ]
        ];

        $this->list_result = [
            '1' => [
                'id' => 1,
                'name' => 'Chưa xong',
            ],
            '2' => [
                'id' => 2,
                'name' => 'Hoàn thành',
            ],
            '3' => [
                'id' => 3,
                'name' => 'Cần hỗ trợ',
            ]
        ];

        $this->list_result_acceptance = [
            '1' => [
                'id' => 1,
                'name' => 'Không đạt',
            ],
            '2' => [
                'id' => 2,
                'name' => 'Đạt',
            ],
            '3' => [
                'id' => 3,
                'name' => 'Đạt có điều kiện',
            ]
        ];
    }

    public function index()
    {
        if (!$this->preViewRequestRepair && !$this->preViewOwnRequestRepair) {
            access_denied();
        }
        $data['title'] = _l('ch_request_repair');
        $this->load->view('admin/request_repair/index', $data);
    }

    public function getRequestRepair()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_repair.id as id',
            'tbl_request_repair.reference_no as reference_no',
            'tbl_request_repair.date as date',
//            'tbl_request_repair.unit_repair as unit_repair',
//            'tbl_request_repair.quantity as quantity',
//            'tbl_request_repair.price as price',
//            'tbl_request_repair.amount as amount',
//            'tbl_category_maintenance.name as category_maintenance',
//            'tbl_request_repair.bp_maintenance as bp_maintenance',
//            'tbl_request_repair.detail_maintenance as detail_maintenance',
            '(SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
                    FROM tbl_machines_stage 
                    JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
                    WHERE tbl_machines_stage.machines_id = tbl_machines.id
                ) as name_stage',
            'tbl_machines.code as code_machines',
            'tbl_machines.name as name_machines',
            'tbl_request_repair.type_repair as type_repair',
//            'tbl_result.name as name_result',
//            'tbl_request_repair.test_records as test_records',
//            'tbl_request_repair.payment as payment',
//            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.reference_no,"__",tblproduction_report.id) SEPARATOR "||")
//             FROM tblproduction_report
//             WHERE tblproduction_report.object_id = tbl_request_repair.id AND tblproduction_report.object_type = "request_repair"
//            ) as name_report',
//            '(
//                SELECT tbltasks.id
//                FROM tbltasks
//                WHERE tbltasks.suggest_id = tbl_request_repair.id AND tbltasks.category_recommended_id = "40"
//            ) as id_tasks',
//            'tbl_request_repair.evaluate as evaluate',
            'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as fullname',
            'tbl_request_repair.status as status',
             'tbl_request_repair.status_finish as status_finish',
//            'tbl_request_repair.standard as standard',
//            'tblsuppliers.company as company_supp',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_repair';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_machines ON tbl_machines.id = tbl_request_repair.machines_id',
            'LEFT JOIN tbl_result ON tbl_result.id = tbl_request_repair.result_id',
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_request_repair.employees',
            'INNER JOIN tbl_category_maintenance ON tbl_category_maintenance.id = tbl_request_repair.category_maintenance',
            'LEFT JOIN tblcosts ON tblcosts.id = tbl_request_repair.cost_id',
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_request_repair.supplier_id',
            'LEFT JOIN tblproduction_report pr ON pr.id = tbl_request_repair.production_report_id',
        ];

        if (!$this->preViewRequestRepair) {
            array_push($where, 'AND tbl_request_repair.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_repair.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_repair.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tblcosts.name as name_cost',
            'tbl_request_repair.date_status',
            'tbl_request_repair.staff_status',
            'tbl_request_repair.date_status_finish',
            'tbl_request_repair.staff_status_finish',
            'pr.reference_no as reference_no_pr',
            'tbl_request_repair.production_report_id as production_report_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
//            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_repair/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 120px">' . _dt($aRow['date']) . '</div>';
//            $row[] = '<div class="text-left" style="width: 130px">' . ($aRow['unit_repair']) . '</div>';
//            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity']) . '</div>';
//            $row[] = '<div class="text-right">' . formatMoney($aRow['price']) . '</div>';
//            $row[] = '<div class="text-right">' . formatMoney($aRow['amount']) . '</div>';
//            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['category_maintenance']) . '</div>';
//            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['bp_maintenance']) . '</div>';
//            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['detail_maintenance']) . '</div>';
            $row[] = '<div class="text-right" style="width: 120px">' . ($aRow['name_stage']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['code_machines']) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px">' . ($aRow['name_machines'].'('.$aRow['name_cost'].'') . '</div>';
            $row[] = '<div class="text-center" >' . (!empty($aRow['type_repair']) ? $this->type_repair[$aRow['type_repair']]['name'] : '') . '</div>';
//            $row[] = '<div class="text-left" >' . $aRow['name_result'] . '</div>';
//            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['test_records'] . '</div>';
//            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['payment'] . '</div>';
//            $arrReport = $aRow['name_report'];
//            $htmlReport = '';
//            if (!empty($arrReport)) {
//                $arrReport = explode('||', $arrReport);
//                if (!empty($arrReport)) {
//                    foreach ($arrReport as $kk => $vv) {
//                        $vv = explode('__', $vv);
//                        $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
//                    }
//                }
//            }
//
//            if ($aRow['production_report_id']) {
//                $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $aRow['production_report_id'])) . '">' . $aRow['reference_no_pr'] . '</a>';
//            }
//
//            if ($aRow['status'] == 1) {
//                // <div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=request_repair') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
//                $row['report'] = '<div style="margin-top: 5px">' . $htmlReport . '</div>
//            ';
//            } else {
//                $row['report'] = $htmlReport ? '<div style="margin-top: 5px">' . $htmlReport . '</div>' : '';
//            }

            $btn_tasks = '';
//            if(!empty($aRow['id_tasks'])) {
//                $btn_tasks = '<a onclick="init_task_modal('.$aRow['id_tasks'].')"><span class="label label-info">Phiếu công việc: '.$aRow['id_tasks'].'</span></a>';
//            }
//            else {
//                if(!empty($aRow['status']) && $aRow['status'] == 1) {
//                    $linkCreate = admin_url('tasks/task?suggest_id=' . $aRow['id'] . '&category_recommended_id=' . (id_category_request_repair ?? 40));
//                    $btn_tasks = '<a class="btn btn-info btn-icon mbot5" onclick="new_task(\'' . $linkCreate . '\')">Tạo phiếu công việc</a>';
//                }
//            }
//            $row[] = $btn_tasks;
//            $row[] = '<div class="text-left">' . ($aRow['evaluate']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';

            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';

            if ($aRow['status_finish'] == 0) {
                $_data_finish = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agreeFinish(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('Hoàn thành') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa hoành thành') . '</span></div>';
            } elseif ($aRow['status_finish'] == 1) {
                $_data_finish = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agreeFinish(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy hoàn thành') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã hoàn thành') . '</span></div>';
                $_data_finish .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status_finish']) . '</div>';
            } else {
                $_data_finish = '';
            }
            $row[] = '<div class="text-left">' . $_data_finish . '</div>';

//            $row[] = '<div class="text-left">' . $aRow['standard'] . '</div>';
//            $row[] = '<div class="text-left">' . $aRow['company_supp'] . '</div>';
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_repair/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestRepair ? '<a class="tnh-modal" href="' . base_url('admin/request_repair/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestRepair ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_repair/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $view . '</li>
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div>' . $actions . '</div>';


            $row['detail_html'] = $this->viewStep($aRow['id']);


            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }


    private function viewStep($id_request_repair = 0) {
        $list_step = $this->db->get_where('tbl_request_repair_step', ['id_request_repair' => $id_request_repair])->result_array();
        if(!empty($list_step)) {
            $liStep = [];
            $liName = [];
            foreach ($list_step as $key => $value) {
                $liStep[] = '<li class="initli" style="list-style-type: none;width: 110px;float: left;font-size: 12px;position: relative;text-align: center;color: #7d7d7d;z-index: 0;font-size: 9px;"></li>';
                $viewEventCreate = '';
                $classStep = '';
                if(empty($value['status'])) {
//                    $viewEventCreate = 'onclick="new_task(\''.admin_url('tasks/task?suggest_id='.$value['id_request_repair'].'&rel_append_id='.$value['id'].'&category_recommended_id=' .  (id_category_request_repair ?? 40)).'\')"';
                    $viewEventCreate = 'href="'.admin_url('request_repair/view_step/'.$value['id'].'/'.$id_request_repair).'" data-toggle="modal" data-target="#myModal" class="tnh-modal" data-tnh="modal"';
                }
                else {
                    $classStep = 'active';
                    $viewEventCreate = 'href="'.admin_url('request_repair/view_step/'.$value['id'].'/'.$id_request_repair).'" data-toggle="modal" data-target="#myModal" class="tnh-modal" data-tnh="modal"';
                }
                $liName[] = '<li style="" class="pointer '.$classStep.'">
								'.$value['name'].'
								'.(!empty($value['id_tasks']) ? ('<a onclick="init_task_modal('.$value['id_tasks'].')"><span class="label label-info">Phiếu công việc</span></a>') : '').'
								<div class="wrap-title-process" style="">
								    <div class="text-center" style="font-size: 18px; cursor: pointer;">
								        <i style="" class="wrap-icon-check fa fa-check-circle-o c_modal" '.$viewEventCreate.'></i></a>
								    </div>
                                </div>
							</li>';
            }
            $liName = implode('', $liName);
            $liStep = implode('', $liStep);

            $data = '<div class="display: table; justify-content: center;">
                        <ul class="progressbar" style="display: flex;">' . $liStep . '</ul>
                        <ul class="progressbar" style="display: flex;">' . $liName . '</ul>
                 </div>';
        }
        else {
            $data = '<b class="text-danger">Chưa thiết lập quy trình.</b>';
        }
        return $data;
    }

    public function view_step($id = 0, $id_request_repair = 0)
    {
        $this->db->where('id', $id);
        $repair_step = $this->db->get('tbl_request_repair_step')->row_array();

        $data['request_repair'] = $this->db->get_where('tbl_request_repair', ['id' => $id_request_repair])->row_array();
        if(!empty($repair_step)) {
            $data['id'] = $id;
            $data['id_request_repair'] = $id_request_repair;
            $data['employees'] = $this->manufactures_model->getAllStaff();
            if($repair_step['step'] == 1) {
                $data['title'] = $repair_step['name'];
                $this->load->view('admin/request_repair/view_step/step_two', $data);
            }
            else if($repair_step['step'] == 2) {

                $this->db->where('hide', 0);
                $data['category_tasks'] = $this->db->get('tblcategory_tasks')->result_array();

                $data['title'] = $repair_step['name'];
                if($data['request_repair']['type_processing'] == 1) {
                    $this->load->view('admin/request_repair/view_step/step_three_one', $data);
                }
                else {
                    $data['title'] = $repair_step['name'];
                    $this->load->view('admin/request_repair/view_step/step_three_two', $data);
                }
            }
            else if($repair_step['step'] == 3) {
                $data['costs'] = $this->db->get_where('tblcosts', ['type_cost' => 2])->result_array();
                $data['title'] = $repair_step['name'];

                $this->load->view('admin/request_repair/view_step/step_four', $data);
            }
        }
    }

    public function detail_step($id = '', $id_request_repair = 0) {
        if($this->input->post()) {
            $data = $this->input->post();
            $this->db->where('id', $id);
            $repair_step = $this->db->get('tbl_request_repair_step')->row_array();
            if($repair_step['step'] == 1) {
                $dataStep = [
                    'staff_inspector' => $data['staff_inspector'],
                    'date_inspector' => !empty($data['date_inspector']) ? to_sql_date($data['date_inspector'], true) : NULL,
                    'incident' => $data['incident'],
                    'type_processing' => $data['type_processing'] ?? 1,
                    'reason' => $data['reason'] ?? NULL,
                    'detail_repair' => $data['detail_repair'] ?? 0,
                    'date_start' => !empty($data['date_start']) ? to_sql_date($data['date_start'], true) : NULL,
                    'date_end' => !empty($data['date_end']) ? to_sql_date($data['date_end'], true) : NULL,
                    'number_components' => number_format_data($data['number_components'] ?? 0, false),
                    'expense' => number_format_data($data['expense'] ?? 0, false),
                ];
                $success = $this->db->update('tbl_request_repair', $dataStep, ['id' => $id_request_repair]);
                if(!empty($success)) {
                    $this->db->where('id_request_repair', $id_request_repair);
                    $this->db->where('step', 1);
                    $this->db->update('tbl_request_repair_step', [
                        'status' => 1,
                    ]);

                    if($dataStep['type_processing'] == 1) {
                        $this->db->where('id_request_repair', $id_request_repair);
                        $this->db->where('step', 2);
                        $this->db->update('tbl_request_repair_step', [
                            'name' => 'Xử lý (Nội bộ)',
                        ]);
                    }
                    else {
                        $this->db->where('id_request_repair', $id_request_repair);
                        $this->db->where('step', 2);
                        $this->db->update('tbl_request_repair_step', [
                            'name' => 'Xử lý (Bên ngoài)',
                        ]);
                    }
                    $id_tasks = $this->createTaskAuto($id_request_repair, 1);
                    echo json_encode([
                        'success' => true,
                        'result' => true,
                        'alert_type' => 'success',
                        'message' => 'Cập nhật thành công',
                        'id_tasks' => $id_tasks ?? 0
                    ]);die();
                }
                echo json_encode(['success' => false, 'result' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật không thành công']);die();
            }
            else if($repair_step['step'] == 2) {
                $dataUpdate = [
                    'staff_performing' => $data['staff_performing'] ?? 0,
                    'date_performing' => !empty($data['date_performing']) ? to_sql_date($data['date_performing'], true) : NULL,
                    'date_success' => !empty($data['date_success']) ? to_sql_date($data['date_success'], true) : NULL,
                    'is_result' => $data['is_result'] ?? 0,
                    'category_tasks' => $data['category_tasks'] ?? 0,
                    'supplier_id' => $data['supplier_id'] ?? NULL,
                    'code_purchase_order' => $data['code_purchase_order'] ?? NULL,
                    'date_contract' => !empty($data['date_contract']) ? to_sql_date($data['date_contract'], true) : NULL,
                    'date_expected' => !empty($data['date_expected']) ? to_sql_date($data['date_expected'], true) : NULL,
                    'unit_repair' => !empty($data['unit_repair']) ? $data['unit_repair'] : NULL,
                ];

                $quantity = number_format_data($data['quantity'] ?? 0, false);
                $price = number_format_data($data['price'] ?? 0, false);
                if(empty($price) && !empty($data['amount'])) {
                    $amount = number_format_data($data['amount'] ?? 0, false);
                    $price = $amount/$quantity;
                }
                else {
                    $amount = $quantity * $price;
                }
                $dataUpdate['quantity'] = $quantity;
                $dataUpdate['price'] = $price;
                $dataUpdate['amount'] = $amount;


                $this->db->where('id', $id_request_repair);
                $success = $this->db->update('tbl_request_repair', $dataUpdate);
                if(!empty($success)) {
                    $this->db->where('id_request_repair', $id_request_repair);
                    $this->db->where('step', 2);
                    $this->db->update('tbl_request_repair_step', [
                        'status' => 1,
                    ]);
                    $id_tasks = $this->createTaskAuto($id_request_repair, 2);
                    echo json_encode([
                        'success' => true,
                        'result' => true,
                        'alert_type' => 'success',
                        'message' => 'Cập nhật thành công',
                        'id_tasks' => $id_tasks
                    ]);die();
                }
                echo json_encode(['success' => false, 'result' => false, 'alert_type' => 'danger', 'message' => 'Cập nhật không thành công']);die();

            }
        }
    }


    public function detail_append($id_request_repair = 0) {
        if($this->input->post()) {
            $data = $this->input->post();
            $this->db->where('id', $id_request_repair);
            $request_repair = $this->db->get('tbl_request_repair')->row_array();
            if(!empty($request_repair)) {
                $dataStepUpdate = [
                    'staff_acceptance' => $data['staff_acceptance'] ?? 0,
                    'date_acceptance' => !empty($data['date_acceptance']) ? to_sql_date($data['date_acceptance'], true) : NULL,
                    'result_acceptance' => !empty($data['result_acceptance']) ? $data['result_acceptance'] : NULL,
                    'star' => !empty($data['star']) ? $data['star'] : NULL,
                    'star_unit_repair' => !empty($data['star_unit_repair']) ? $data['star_unit_repair'] : NULL,
                    'payment' => !empty($data['payment']) ? $data['payment'] : 0,
                    'costs' => !empty($data['costs']) ? $data['costs'] : NULL,
                    'employees_costs' => !empty($data['employees_costs']) ? $data['employees_costs'] : NULL,
                ];
                $this->db->where('id', $id_request_repair);
                $success = $this->db->update('tbl_request_repair', $dataStepUpdate);
                if(!empty($success)) {
                    $this->db->where('id_request_repair', $id_request_repair);
                    $this->db->where('step', 3);
                    $this->db->update('tbl_request_repair_step', [
                        'status' => 1
                    ]);

                    $id_tasks = $this->createTaskAuto($id_request_repair, 3);

                    echo json_encode([
                        'success' => true,
                        'result' => true,
                        'alert_type' => 'success',
                        'message' => 'nghiệm thu thành công',
                        'id_tasks' => $id_tasks ?? 0
                    ]);die();
                }
                else {
                    echo json_encode(['success' => false,'result' => false, 'alert_type' => 'danger', 'message' => 'nghiệm thu không thành công']);die();
                }
            }
        }
        echo json_encode(['success' => false,'result' => false, 'alert_type' => 'danger', 'message' => 'Đã có lỗi xãy ra vui lòng kiểm tra lại']);die();
    }

    public function detail($id = 0)
    {
        $data = [];
        $dtData = [];
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_repair.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
//                $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                $this->form_validation->set_rules('category_maintenance', lang("Nhóm bảo dưỡng"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_repair');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $dtMachines = explode('__',$machines_id);
                    $machines_id = $dtMachines[0];
                    $cost_id = $dtMachines[1];
                    $result_id = $this->input->post('result_id');
                    $category_maintenance = ($this->input->post('category_maintenance'));
                    $bp_maintenance = ($this->input->post('bp_maintenance'));
                    $unit_repair = ($this->input->post('unit_repair'));
                    $detail_maintenance = ($this->input->post('detail_maintenance'));
                    $employees = ($this->input->post('employees'));
                    $test_records = ($this->input->post('test_records'));
                    $evaluate = ($this->input->post('evaluate'));
                    $payment = ($this->input->post('payment'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $price = number_unformat($this->input->post('price'));
                    $supplier_id = ($this->input->post('supplier_id'));
                    $production_report_id = ($this->input->post('production_report_id'));


                    $type_repair = $this->input->post('type_repair') ?? 1;
                    $priority = $this->input->post('priority') ?? 1;
                    $completion_date = ($this->input->post('completion_date'));
                    if(!empty($completion_date)) {
                        $completion_date = to_sql_date($completion_date);
                    }
                    else {
                        $completion_date = NULL;
                    }
                    $detailed = $this->input->post('detailed') ?? '';

                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'cost_id' => $cost_id,
                        'result_id' => $result_id,
                        'category_maintenance' => $category_maintenance,
                        'unit_repair' => $unit_repair,
                        'detail_maintenance' => $detail_maintenance,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $quantity * $price,
                        'branch_id' => $branch_id,
                        'bp_maintenance' => $bp_maintenance,
                        'employees' => $employees,
                        'test_records' => $test_records,
                        'evaluate' => $evaluate,
                        'payment' => $payment,
                        'supplier_id' => $supplier_id,
                        'production_report_id' => $production_report_id,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'type_repair' => $type_repair,
                        'priority' => $priority,
                        'completion_date' => $completion_date,
                        'detailed' => $detailed,
                    ];
                    $this->db->insert('tbl_request_repair', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_repair') == $reference_no) {
                            updateReference('request_repair');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_repair',
                            'table_obj' => 'tbl_request_repair',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu bảo dưỡng') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Thêm thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            } else {
                $this->db->select('tbl_request_repair.*');
                $this->db->from('tbl_request_repair');
                $this->db->where('tbl_request_repair.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_repair.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('machines_id', lang("Thiết bị"), 'required');
//                $this->form_validation->set_rules('result_id', lang("Kết quả"), 'required');
                $this->form_validation->set_rules('category_maintenance', lang("Nhóm bảo dưỡng"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $machines_id = $this->input->post('machines_id');
                    $dtMachines = explode('__',$machines_id);
                    $machines_id = $dtMachines[0];
                    $cost_id = $dtMachines[1];
                    $result_id = $this->input->post('result_id');
                    $category_maintenance = ($this->input->post('category_maintenance'));
                    $bp_maintenance = ($this->input->post('bp_maintenance'));
                    $unit_repair = ($this->input->post('unit_repair'));
                    $detail_maintenance = ($this->input->post('detail_maintenance'));
                    $employees = ($this->input->post('employees'));
                    $test_records = ($this->input->post('test_records'));
                    $evaluate = ($this->input->post('evaluate'));
                    $payment = ($this->input->post('payment'));
                    $quantity = number_unformat($this->input->post('quantity'));
                    $price = number_unformat($this->input->post('price'));
                    $supplier_id = ($this->input->post('supplier_id'));
                    $production_report_id = ($this->input->post('production_report_id'));

                    $type_repair = $this->input->post('type_repair') ?? 1;
                    $priority = $this->input->post('priority') ?? 1;
                    $completion_date = ($this->input->post('completion_date'));
                    if(!empty($completion_date)) {
                        $completion_date = to_sql_date($completion_date);
                    }
                    else {
                        $completion_date = NULL;
                    }
                    $detailed = $this->input->post('detailed') ?? '';

                    $fields = [
                        'date' => $date,
                        'machines_id' => $machines_id,
                        'cost_id' => $cost_id,
                        'result_id' => $result_id,
                        'category_maintenance' => $category_maintenance,
                        'unit_repair' => $unit_repair,
                        'detail_maintenance' => $detail_maintenance,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $quantity * $price,
                        'branch_id' => $branch_id,
                        'bp_maintenance' => $bp_maintenance,
                        'employees' => $employees,
                        'test_records' => $test_records,
                        'evaluate' => $evaluate,
                        'payment' => $payment,
                        'supplier_id' => $supplier_id,
                        'production_report_id' => $production_report_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'type_repair' => $type_repair,
                        'priority' => $priority,
                        'completion_date' => $completion_date,
                        'detailed' => $detailed,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_repair', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'request_repair',
                            'table_obj' => 'tbl_request_repair',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['result'] = 1;
                        $data['message'] = lang('Sửa thành công');
                    } else {
                        $data['result'] = 0;
                        $data['message'] = lang('Sửa thất bại');
                    }
                } else {
                    $data['result'] = 0;
                    $data['message'] = validation_errors();
                }
                echo json_encode($data);
                die();
            }
        } else {
            if (empty($id)) {
                if (!$this->preAddRequestRepair) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_request_repair');
            } else {
                if (!$this->preEditRequestRepair) {
                    accessDenied(true);
                }
                $this->db->select('tbl_request_repair.*,
                    tblcosts.id as cost_id,
                    tblcosts.name as name_cost,
                ');
                $this->db->from('tbl_request_repair');
                $this->db->join('tblcosts','tblcosts.id = tbl_request_repair.cost_id','left');
                $this->db->where('tbl_request_repair.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['status'] == 1) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $data['title'] = lang('ch_edit_request_repair');
            }
        }
        $data['dtData'] = $dtData;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_repair');
        $data['dtCategorymaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/request_repair/detail', $data);
    }

    public function searchProductionReport($id = 0) {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;

        $this->db->select('tblproduction_report.id as id, COALESCE(tblproduction_report.reference_no, "") as text', false);
        $this->db->from('tblproduction_report');
        if ($term) {
            $this->db->group_start();
            $this->db->like('tblproduction_report.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->where('(tblproduction_report.reference_no != "" AND tblproduction_report.reference_no IS NOT NULL)', false, false);
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();;
        if ($id) {
            $production_report = get_table_where('tblproduction_report', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $production_report['id'], 'text' => $production_report['reference_no']];
        }
        echo json_encode($data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_request_repair');

        $this->db->select('tbl_request_repair.*,
           tbl_result.name as name_result,
           tbl_machines.status as status_machines,
           tbl_machines.code as code_machines,
           tbl_machines.name as name_machines,
           tblcosts.name as name_cost,
           tbl_category_maintenance.name as category_maintenance,
           (SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
            FROM tbl_machines_stage 
            JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
            WHERE tbl_machines_stage.machines_id = tbl_machines.id
           ) as name_stage,
            tblsuppliers.company as company_supp
        ');
        $this->db->from('tbl_request_repair');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_repair.machines_id', 'inner');
        $this->db->join('tblcosts', 'tblcosts.id = tbl_request_repair.cost_id', 'left');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_request_repair.result_id', 'left');
        $this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_request_repair.category_maintenance', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_request_repair.supplier_id', 'left');
        $this->db->where('tbl_request_repair.id', $id);
        $dtData = $this->db->get()->row_array();

        $dtData['name_priority'] = $this->list_priority[$dtData['priority']]['name'] ?? '';
        $dtData['name_type_repair'] = (!empty($dtData['type_repair']) ? $this->type_repair[$dtData['type_repair']]['name'] : '');
        $dtData['name_type_processing'] = (!empty($dtData['type_processing']) ? $this->type_processing[$dtData['type_processing']]['name'] : '');
        $dtData['name_is_result'] = (!empty($dtData['is_result']) ? $this->list_result[$dtData['is_result']]['name'] : '');
        $dtData['name_result_acceptance'] = (!empty($dtData['result_acceptance']) ? $this->list_result_acceptance[$dtData['result_acceptance']]['name'] : '');
        if(!empty($dtData['category_tasks'])) {
            $dtData['code_category_tasks'] = $this->db->get_where('tblcategory_tasks', ['id' => $dtData['category_tasks']])->row('code');
        }
        if(!empty($dtData['costs'])) {
            $dtData['code_costs'] = $this->db->get_where('tblcosts', ['id' => $dtData['costs']])->row('code');
        }

        $step = $this->db->get_where('tbl_request_repair_step', ['id_request_repair' => $dtData['id']])->result_array();
        if(!empty($step)) {
            foreach ($step as $key => $value) {
                $dtData['step'][$value['step']] = $value['status'];
            }
        }

        $data['dtData'] = $dtData;
        $this->load->view('admin/request_repair/view_detail', $data);
    }

    public function get_purchases()
    {
        $supplier_id = $this->input->get('supplier_id');
        if(!empty($supplier_id)) {
            $this->db->select('CONCAT(prefix,"-", code) as fullcode, id');
            $this->db->where('suppliers_id', $supplier_id);
            $purchase_order = $this->db->get('tblpurchase_order')->result_array();
            echo json_encode([
                'data' => $purchase_order
            ]);die();
        }
        echo json_encode([
            'data' => []
        ]);die();
    }

    public function agree()
    {
        if (!$this->preApproveRequestRepair) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_request_repair.*');
        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                if ($dtData['status_finish'] == 1) {
                    $data['result'] = 0;
                    $data['message'] = lang('Phiếu đã hoàn thành không thể hủy duyệt !');
                    echo json_encode($data);
                    die();
                }

                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_repair');
                $this->db->where('tblproduction_report.object_id', $suggest_id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
                    echo json_encode($data);
                    die();
                }
            }

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data);
                return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_request_repair', $options);
            if ($up) {
                if(!empty($status)) {
                    foreach($this->list_step as $key => $value) {
                        $this->db->insert('tbl_request_repair_step', [
                            'name' => $value['name'],
                            'id_request_repair' => $suggest_id,
                            'step' => $value['id'],
                            'create_by' => get_staff_user_id(),
                        ]);
                    }
                }

                insertActivityLog([
                    'type_parent_obj' => 'request_repair',
                    'table_obj' => 'tbl_request_repair',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'approved'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function agreeFinish()
    {
        if (!$this->preApproveRequestRepair) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_request_repair.*');
        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 1) {
                if ($dtData['status'] == 0) {
                    $data['result'] = 0;
                    $data['message'] = lang('Phiếu chưa duyệt không thể hoàn thành !');
                    echo json_encode($data);
                    die();
                }
            }

            if (($dtData['status_finish'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data);
                return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status_finish' => $status,
                'date_status_finish' => $date_status,
                'staff_status_finish' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_request_repair', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'request_repair',
                    'table_obj' => 'tbl_request_repair',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt hoành thành phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
                    'actions' => 'approved_finish'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }


    public function createTaskAuto($id = '', $step = '')
    {
        $this->db->where('id', $id);
        $request_repair = $this->db->get('tbl_request_repair')->row();
        $name = '';
        if (!empty($request_repair)) {
            $this->db->where('id_request_repair', $id);
            $this->db->where('step', $step);
            $ktIdTasks = $this->db->get('tbl_request_repair_step')->row('id_tasks');
            if(!empty($ktIdTasks)) {
                $getTasks = $this->db->get_where('tbltasks', ['id' => $ktIdTasks])->row();
                if(empty($getTasks)) {
                    $this->db->where('id_request_repair', $id);
                    $this->db->where('step', $step);
                    $this->db->update('tbl_request_repair_step', [
                        'id_tasks' => NULL,
                    ]);
                }
                else {
                    return false;
                }
            }


            if (!empty($request_repair->category_tasks)) {
                $this->db->where('id', $request_repair->category_tasks);
                $category_tasks = $this->db->get('tblcategory_tasks')->row();
                $staff_department = !empty($category_tasks) ? $category_tasks->departments : null;
                $name = !empty($category_tasks) ? $category_tasks->content : null;
            }
            $duedate = null;
            $_data = [
                'name' => $name,
                'hourly_rate' => 0,
                'category_tasks' => $request_repair->category_tasks,
                'startdate' => $request_repair->date,
                'duedate' => $duedate,
                'priority' => 2,
                'rel_type' => 'request_repair',
                'category_recommended_id' => '40',
                'rel_id' => $id,
                'description' => $request_repair->detail_maintenance,
                'department_id' => !empty($staff_department) ? explode(',', $staff_department) : [],
                'id_branch' => $request_repair->branch_id,
            ];
            $id_tasks = $this->tasks_model->add($_data, false, true);
            if (!empty($id_tasks)) {
                $this->db->where('id_request_repair', $id);
                $this->db->where('step', $step);
                $this->db->update('tbl_request_repair_step', [
                    'id_tasks' => $id_tasks,
                ]);

                if($step == 1) {
                    $this->db->insert('tbltask_assigned', [
                        'staffid' => $request_repair->staff_inspector ?? get_staff_user_id(),
                        'taskid' => $id_tasks,
                        'assigned_from' => get_staff_user_id(),
                    ]);
                }
                if($step == 2) {
                    $this->db->insert('tbltask_assigned', [
                        'staffid' => $request_repair->staff_performing ?? get_staff_user_id(),
                        'taskid' => $id_tasks,
                        'assigned_from' => get_staff_user_id(),
                    ]);
                }
                if($step == 3) {
                    $this->db->insert('tbltask_assigned', [
                        'staffid' => $request_repair->staff_acceptance ?? get_staff_user_id(),
                        'taskid' => $id_tasks,
                        'assigned_from' => get_staff_user_id(),
                    ]);
                }
                return $id_tasks;
            }
        }
        return false;
    }



    public function delete($id)
    {
        if (!$this->preDeleteRequestRepair) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_repair.*');
        $this->db->from('tbl_request_repair');
        $this->db->where('tbl_request_repair.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_repair');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'request_repair',
                'table_obj' => 'tbl_request_repair',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu bảo dưỡng') . ' [' . $dtData['reference_no'] . ']',
                'actions' => 'delete'
            ]);
            $data['result'] = 1;
            $data['message'] = lang('Xóa thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Xóa thất bại');
        }
        echo json_encode($data);
    }

    public function exportExcelBK()
    {
        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];
        if ($this->input->post('export_excel')) {

            ini_set('memory_limit', '3500M');
            require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
            $this->load->library('PHPExcel');
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_sua_chua.xlsx';
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $BStylenumber = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '111112'),
                    'size'  => 11,
                    'name'  => 'Times New Roman'
                ),
                'alignment' => array(
                    'horizontal' => 'center',
                ),
            );
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestRow = $objWorksheet->getHighestRow();
            $check_key = array_search($highestColumn, $columsExcel);
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $row = 3;
            $staff_id = get_staff_user_id();
            $this->db->select('tbl_request_repair.*,
                tbl_result.name as name_result,
                tbl_machines.status as status_machines,
                tbl_machines.code as code_machines,
                tbl_machines.name as name_machines,
                tblcosts.name as name_cost,
                tbl_category_maintenance.name as category_maintenance,
                (SELECT GROUP_CONCAT(tbl_category_stages.name) as name_stage
                    FROM tbl_machines_stage 
                    JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
                    WHERE tbl_machines_stage.machines_id = tbl_machines.id
                ) as name_stage,
                (SELECT GROUP_CONCAT(CONCAT(tblproduction_report.reference_no,"__",tblproduction_report.id) SEPARATOR "||")
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_request_repair.id AND tblproduction_report.object_type = "request_repair"
                ) as name_report,
                tblsuppliers.company as company_supp,
                pr.reference_no as reference_no_pr,
                tbl_request_repair.production_report_id as production_report_id
            ');
            $this->db->from('tbl_request_repair');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_repair.machines_id', 'inner');
            $this->db->join('tblcosts', 'tblcosts.id = tbl_request_repair.cost_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_request_repair.result_id', 'left');
            $this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_request_repair.category_maintenance', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_request_repair.supplier_id', 'left');
            $this->db->join('tblproduction_report pr', 'pr.id = tbl_request_repair.production_report_id', 'left');
            if (!$this->preViewRequestRepair) {
                $this->db->where('(tbl_request_repair.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_repair.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_repair.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_repair.id asc');
            $items = $this->db->get()->result_array();

            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['unit_repair']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[4] . $row, $value['price']);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[5] . $row, $value['amount']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['category_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['bp_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['detail_maintenance']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[9] . $row, $value['quantity']);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['name_stage']), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['code_machines']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['name_machines'].'('.$value['name_cost'].')'), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($value['test_records']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[15] . $row, ($value['payment']), PHPExcel_Cell_DataType::TYPE_STRING);
                $arrReport = $value['name_report'];
                $htmlReport = '';
                if (!empty($arrReport)) {
                    $arrReport = explode('||', $arrReport);
                    if (!empty($arrReport)) {
                        foreach ($arrReport as $kk => $vv) {
                            $vv = explode('__', $vv);
                            $htmlReport .= $vv[0] . ',';
                        }
                    }
                }

                if ($value['production_report_id']) {
                    $htmlReport .= $value['reference_no_pr'];
                }

                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[16] . $row, ($htmlReport), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[17] . $row, ($value['evaluate']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[18] . $row, get_staff_full_name($value['employees']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[19] . $row, (!empty($value['staff_status']) ? get_staff_full_name($value['staff_status']) : ''), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[20] . $row, ($value['standard']), PHPExcel_Cell_DataType::TYPE_STRING);

                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_repair||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_repair', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_repair/';
                if (!file_exists($folder)) {
                    mkdir($folder);
                    fopen($folder . 'index.html', 'w');
                }
                if (!file_exists($folder . 'qrcode' . '/')) {
                    mkdir($folder . 'qrcode' . '/');
                    fopen($folder . 'qrcode' . '/' . 'index.html', 'w');
                }
                $params['data'] = $code;
                $params['level'] = 'H';
                $params['size'] = 40;
                $params['savename'] = $folder . 'qrcode/' . $qr . '.png';
                $this->ciqrcode->generate($params);
                $img = ($folder . 'qrcode/' . $qr . '.png');
                if (!empty($img)) {
                    $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                    $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                    $objDrawing1->setPath($img);
                    $objDrawing1->setWidth(90);
                    $objDrawing1->setHeight(65);
                    $objDrawing1->setOffsetX(20);
                    $objDrawing1->setOffsetY(2);
                    $objDrawing1->setCoordinates($columsExcel[21] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[21] . $row, '')->getStyle($columsExcel[21] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[22] . $row, ($value['company_supp']), PHPExcel_Cell_DataType::TYPE_STRING);

            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:V' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:V' . $row)->applyFromArray([
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
            ]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[0])->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[1])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[2])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[3])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[4])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[10])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[11])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[12])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[13])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[14])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[15])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[16])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[17])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[18])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[19])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[20])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[21])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[22])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[23])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[24])->setWidth(17);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '8MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $filename = lang('Phieu_yeu_cau_sua_chua') . '.xls';
            ob_start();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="$filename"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $response = array(
                'result' => 1,
                'filename' => $filename,
                'message' => lang('success'),
                'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
            );
            die(json_encode($response));
        }
    }

    public function exportExcel()
    {
        if (!$this->input->post('export_excel')) {
            return;
        }

        ini_set('memory_limit', '2048M');
        require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');
        $this->load->library('PHPExcel');

        $columsExcel = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'
        ];

        // ===== LOAD TEMPLATE =====
        $template = 'uploads/import_ch/Phieu_yeu_cau_sua_chua.xlsx';
        $excel = PHPExcel_IOFactory::load($template);
        $sheet = $excel->setActiveSheetIndex(0);

        // ===== STYLE =====
        $headerStyle = [
            'font' => [
                'bold' => true,
                'name' => 'Times New Roman',
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true
            ],
            'borders' => [
                'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
            ]
        ];

        $cellStyle = [
            'font' => [
                'name' => 'Times New Roman',
                'size' => 11
            ],
            'alignment' => [
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            ],
            'borders' => [
                'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
            ]
        ];

        // ===== HEADER (ROW 3) =====
        $headerRow = 3;
        $colIndex = 0; // A = 1

        // PHẦN 1
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'STT');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Số phiếu');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Ngày yêu cầu');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Người đề xuất');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Mức độ ưu tiên');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Mã thiết bị');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Tên thiết bị');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Loại bảo trì');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Công đoạn');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Chi tiết sự cố');

        // PHẦN 2
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Người kiểm tra');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Ngày kiểm tra');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Đánh giá sự cố');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Lý do');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Chi tiết sửa chữa');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Thời gian ảnh hưởng');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Số linh kiện dự tính');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Chi phí dự tính');

        // PHẦN 3
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Người thực hiện');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Hạng mục công việc');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Ngày bắt đầu');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Hoàn thành dự kiến');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Hoàn thành thực tế');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Chi phí thực tế');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Số lượng thực tế');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Nhà cung cấp');

        // PHẦN 4
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Người nghiệm thu');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Ngày nghiệm thu');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Kết quả nghiệm thu');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Đánh giá số sao');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Đánh giá đơn vị');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Mã ngân sách');
        $sheet->setCellValue($columsExcel[$colIndex++].$headerRow, 'Thanh toán');

        $lastCol = $columsExcel[$colIndex - 1];
        $sheet->getStyle('A'.$headerRow.':'.$lastCol.$headerRow)->applyFromArray($headerStyle);
        $sheet->getRowDimension($headerRow)->setRowHeight(45);

        // ===== QUERY (GIỮ NGUYÊN CỦA BẠN) =====
        $this->db->select('
        tbl_request_repair.*,
        tbl_result.name AS name_result,
        tbl_machines.status AS status_machines,
        tbl_machines.code AS code_machines,
        tbl_machines.name AS name_machines,
        tbl_category_maintenance.name AS category_maintenance,
        (
            SELECT GROUP_CONCAT(tbl_category_stages.name)
            FROM tbl_machines_stage
            JOIN tbl_category_stages ON tbl_category_stages.id = tbl_machines_stage.category_stage_id
            WHERE tbl_machines_stage.machines_id = tbl_machines.id
        ) AS name_stage,
        tblsuppliers.company AS company_supp,
        tblcategory_tasks.code AS code_category_tasks,
        tblcosts.code AS code_costs
    ');
        $this->db->from('tbl_request_repair');
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_request_repair.machines_id', 'inner');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_request_repair.result_id', 'left');
        $this->db->join('tbl_category_maintenance', 'tbl_category_maintenance.id = tbl_request_repair.category_maintenance', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_request_repair.supplier_id', 'left');
        $this->db->join('tblcategory_tasks', 'tblcategory_tasks.id = tbl_request_repair.category_tasks', 'left');
        $this->db->join('tblcosts', 'tblcosts.id = tbl_request_repair.costs', 'left');
        $this->db->order_by('tbl_request_repair.id', 'desc');
        $items = $this->db->get()->result_array();

        // ===== DATA =====
        $row = $headerRow;
        $stt = 0;

        foreach ($items as $it) {


            $it['name_priority'] = $this->list_priority[$it['priority']]['name'] ?? '';
            $it['name_type_repair'] = (!empty($it['type_repair']) ? $this->type_repair[$it['type_repair']]['name'] : '');
            $it['name_type_processing'] = (!empty($it['type_processing']) ? $this->type_processing[$it['type_processing']]['name'] : '');
            $it['name_is_result'] = (!empty($it['is_result']) ? $this->list_result[$it['is_result']]['name'] : '');
            $it['name_result_acceptance'] = (!empty($it['result_acceptance']) ? $this->list_result_acceptance[$it['result_acceptance']]['name'] : '');


            $row++;
            $stt++;
            $col = 'A';
            $colIndex = 0;

            $sheet->setCellValue($columsExcel[$colIndex++].$row, $stt);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['reference_no']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, get_staff_full_name($it['employees']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['name_priority']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['code_machines']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['name_machines']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['category_maintenance']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['name_stage']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['detailed']);

            $sheet->setCellValue($columsExcel[$colIndex++].$row, get_staff_full_name($it['staff_inspector']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date_inspector']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['incident']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['reason']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['detail_repair']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date_start']).' - '._dt($it['date_end']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, number_format_data($it['expense']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, number_format_data($it['number_components']));

            $sheet->setCellValue($columsExcel[$colIndex++].$row, get_staff_full_name($it['staff_performing']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['code_category_tasks']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date_performing']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date_expected']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date_success']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, number_format_data($it['amount']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, number_format_data($it['quantity']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['company_supp']);

            $sheet->setCellValue($columsExcel[$colIndex++].$row, get_staff_full_name($it['staff_acceptance']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, _dt($it['date_acceptance']));
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['name_result']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['star']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['star_unit_repair']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, $it['code_costs']);
            $sheet->setCellValue($columsExcel[$colIndex++].$row, !empty($it['payment']) ? 'Đã thanh toán' : 'Chưa thanh toán');

            $sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray($cellStyle);
            $sheet->getRowDimension($row)->setRowHeight(45);
        }
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '8MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $filename = lang('Phieu_yeu_cau_sua_chua') . '.xls';
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="$filename"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $response = array(
            'result' => 1,
            'filename' => $filename,
            'message' => lang('success'),
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        die(json_encode($response));
    }

    public function searchMachines($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            CONCAT(tbl_machines.id,"__",tblcosts.id) as id, 
            CONCAT(tbl_machines.name,"(",tblcosts.name,")") as text,
            tbl_machines.code as code,
            tbl_machines.name as name,
        ', false);
        $this->db->from('tbl_machines');
        $this->db->join('tblcosts','tbl_machines.id = tblcosts.object_id AND tblcosts.type_cost = 2');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)){
            $dtId = explode('__',$id);
            $dtMachines = get_table_where('tbl_machines',['id' => $dtId[0]],'','row_array');
            $dtCost = get_table_where('tblcosts',['id' => $dtId[1]],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'].'__'.$dtCost['id'], 'text' => $dtMachines['name'].'('.$dtCost['name'].')'];
        }
        echo json_encode($data);
    }
}
