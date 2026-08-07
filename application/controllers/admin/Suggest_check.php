<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_check extends AdminController
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

        $this->preViewSuggestCheck = has_permission('suggest_check', '', 'view');
        $this->preViewOwnSuggestCheck = has_permission('suggest_check', '', 'view_own');
        $this->preAddSuggestCheck = has_permission('suggest_check', '', 'create');;
        $this->preEditSuggestCheck = has_permission('suggest_check', '', 'edit');;
        $this->preApproveSuggestCheck = true;
        $this->preDeleteSuggestCheck = has_permission('suggest_check', '', 'delete');
    }

    public function index()
    {
        if (!$this->preViewSuggestCheck && !$this->preViewOwnSuggestCheck) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_check');
        $this->load->view('admin/suggest_check/index', $data);
    }

    public function getSuggestChecks()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_suggest_check.id as id',
            'tbl_suggest_check.reference_no as reference_no',
            'tbl_suggest_check.date as date',
            '"" as name_machines',
            'tbl_suggest_maintenance.reference_no as code_suggest_maintenance',
            'tbl_suggest_check.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_check';

        $where = [];

        $join = [
            'LEFT JOIN tbl_suggest_maintenance ON tbl_suggest_maintenance.id = tbl_suggest_check.suggest_maintenance_id',
        ];

        if (!$this->preViewSuggestCheck) {
            $where[] = 'AND (
					EXISTS (
						SELECT 1
						FROM tbl_suggest_check_item
						WHERE tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id
						AND staff_check = ' . get_staff_user_id() . ' OR staff_manager = ' . get_staff_user_id() . '
						LIMIT 1
					)
					OR tbl_suggest_check.created_by =' . get_staff_user_id() . '
				)';
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_check.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_check.date <= '" . $end_date_search . "'");
        }
        array_push($where, "AND tbl_suggest_check.ballot_type = 0");

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'suggest_maintenance_id'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $this->db->select('tbl_suggest_check_item.item_type, tbl_suggest_check_item.item_id, if(tbl_suggest_check_item.item_type = "machines", tbl_machines.name, tbl_cleaning.name) as name_machines');
            $this->db->select('GROUP_CONCAT(CONCAT(" - ", if(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.note, tbl_cleaning_detail.note)) SEPARATOR "<br>") as list_name');
            $this->db->join('tbl_machines_5s', 'tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
            $this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');

            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
            $this->db->join('tbl_cleaning', 'tbl_cleaning.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');

            //			$this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');
            $this->db->where('suggest_check_id', $aRow['id']);
            $this->db->group_by('tbl_suggest_check_item.item_type, tbl_suggest_check_item.item_id');
            $suggest_check_item = $this->db->get_where('tbl_suggest_check_item')->result_array();
            //			print_arrays($suggest_check_item);
            $view_machines = '';
            if (!empty($suggest_check_item)) {
                foreach ($suggest_check_item as $k => $v) {
                    $view_machines .= ($v['item_type'] == 'machines' ? '<b>Thiết bị: ' . $v['name_machines'] . '</b><br/>' : '<b>Khu Vực: ' . $v['name_machines'] . '</b><br/>') . $v['list_name'] . '<br/>';
                }
            }

            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_check/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';

            $content = ($view_machines);
            if (mb_strlen($content, 'UTF-8') >= 100) {
                $content = '<span class="show_more pointer mbot5"><t>' . mb_substr(($content), 0, 100, 'UTF-8') . '... </t><a class="c_more" text-data="' . htmlentities($content) . '">' . _l('more') . '</a></span>';
            }


            $row[] = '<div style="width: 400px!important;white-space: break-spaces;">' . $content . '</div>';


            //			if(mb_strlen($content_machines, 'UTF-8') >= 20) {
            //				$content_machines = '<span class="show_more pointer mbot5"><t>'.mb_substr($content_machines, 0, 150, 'UTF-8').'... </t></span>';
            //			}


            //            $row[] = '<div class="text-left">' . $content_machines . '</div>';
            if(!empty($aRow['code_suggest_maintenance'])) {
                $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="'.admin_url('suggest_maintenance/view/' . $aRow['suggest_maintenance_id']).'" data-toggle="modal" data-target="#myModal">' . $aRow['code_suggest_maintenance'] . '</a></div>';
            } else {
                $row[] = '<div class="text-left"></div>';
            }
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_check/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestCheck ? '<a class="tnh-modal" href="' . base_url('admin/suggest_check/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestCheck ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_check/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_check.*');
        $this->db->from('tbl_suggest_check');
        $this->db->where('tbl_suggest_check.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_check.reference_no]');
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_check.reference_no]');
                }
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_check');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $id_category_task = $this->input->post('id_category_task');
                    $suggest_maintenance_id = $this->input->post('suggest_maintenance_id');
                    $note = $this->input->post('note');
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $machines_maintenance_id = $this->input->post('machines_maintenance_id')[$value];
                            $item_type = $this->input->post('item_type')[$value];
                            $item_id = $this->input->post('item_id')[$value];
                            if (empty($machines_maintenance_id) || empty($item_type)) {
                                continue;
                            }
                            $current_status = $this->input->post('current_status')[$value];
                            $date_check = !empty($this->input->post('date_check')[$value]) ? to_sql_date($this->input->post('date_check')[$value]) : null;
                            $staff_check = $this->input->post('staff_check')[$value];
                            $result_id = $this->input->post('result_id')[$value];
                            $evaluate = $this->input->post('evaluate')[$value];
                            $regulation_5s = $this->input->post('regulation_5s')[$value];
                            $staff_manager = !empty($this->input->post('staff_manager')[$value]) ? $this->input->post('staff_manager')[$value] : 0;

                            $items[] = [
                                'item_type' => $item_type,
                                'item_id' => $item_id,
                                'machines_maintenance_id' => $machines_maintenance_id,
                                'current_status' => $current_status,
                                'date_check' => $date_check,
                                'staff_check' => $staff_check,
                                'result_id' => $result_id,
                                'evaluate' => $evaluate,
                                'regulation_5s' => $regulation_5s,
                                'staff_manager' => $staff_manager,
                            ];
                        }
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'id_category_task' => $id_category_task,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                        'suggest_maintenance_id' => $suggest_maintenance_id,
                    ];
                    $success = $this->db->insert('tbl_suggest_check', $fields);

                    if (!empty($success)) {
                        $id = $this->db->insert_id();
                        if (getReference('suggest_check') == $reference_no) {
                            updateReference('suggest_check');
                        }
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                $value['suggest_check_id'] = $id;
                                $this->db->insert('tbl_suggest_check_item', $value);
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_check',
                            'table_obj' => 'tbl_suggest_check',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kiểm tra') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);

                        $_data_task = [
                            'name' => $reference_no,
                            'hourly_rate' => 0,
                            'category_tasks' => !empty($id_category_task) ? $id_category_task : '',
                            'startdate' => $date,
                            'duedate' => NULL,
                            'priority' => 2,
                            'repeat_every_custom' => 1,
                            'repeat_type_custom' => 'day',
                            'rel_type' => 'suggest_check',
                            'rel_id' => $id,
                            'id_branch' => !empty($branch_id) ? $branch_id : NULL,
                            //							'department_id' => !empty($data['id_departments']) ? [$data['id_departments']] : NULL,
                            'description' => !empty($note) ? $note : NULL,
                        ];


                        $attachments = $this->db->get_where('tblfiles', ['rel_id' => $id, 'rel_type' => 'suggest_check'])->result_array();
                        if (!empty($attachments)) {
                            $_data_task['copy_attachments'] = $attachments;
                        }
                        $id_tasks = $this->tasks_model->add($_data_task, false, true);
                        if (!empty($id_tasks)) {
                            $this->db->where('suggest_check_id', $id);
                            $suggest_check_item = $this->db->get('tbl_suggest_check_item')->result_array();
                            if (!empty($suggest_check_item)) {
                                $listAssigned = [];
                                $listFollowers = [];
                                foreach ($suggest_check_item as $key => $value) {
                                    $listAssigned[] = $value['staff_check'];
                                    $listFollowers[] = $value['staff_manager'];
                                }
                                if (!empty($listAssigned)) {
                                    $listAssigned = array_unique($listAssigned);
                                    foreach ($listAssigned as $key => $value) {
                                        $this->db->insert('tbltask_assigned', [
                                            'staffid' => $value,
                                            'taskid' => $id_tasks
                                        ]);
                                    }
                                }
                                if (!empty($listFollowers)) {
                                    $listFollowers = array_unique($listFollowers);
                                    foreach ($listFollowers as $key => $value) {
                                        $this->db->insert('tbltask_followers', [
                                            'staffid' => $value,
                                            'taskid' => $id_tasks
                                        ]);
                                    }
                                }
                            }
                        }
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
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $id_category_task = $this->input->post('id_category_task');
                    $suggest_maintenance_id = $this->input->post('suggest_maintenance_id');
                    $note = $this->input->post('note');
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $machines_maintenance_id = $this->input->post('machines_maintenance_id')[$value];
                            $item_type = $this->input->post('item_type')[$value];
                            $item_id = $this->input->post('item_id')[$value];
                            if (empty($machines_maintenance_id) || empty($item_type)) {
                                continue;
                            }
                            $suggest_check_item_id = !empty($this->input->post('suggest_check_item_id')[$value]) ? $this->input->post('suggest_check_item_id')[$value] : 0;
                            $current_status = $this->input->post('current_status')[$value];
                            $date_check = !empty($this->input->post('date_check')[$value]) ? to_sql_date($this->input->post('date_check')[$value]) : null;
                            $staff_check = $this->input->post('staff_check')[$value];
                            $result_id = $this->input->post('result_id')[$value];
                            $evaluate = $this->input->post('evaluate')[$value];
                            $regulation_5s = $this->input->post('regulation_5s')[$value];
                            $staff_manager = !empty($this->input->post('staff_manager')[$value]) ? $this->input->post('staff_manager')[$value] : 0;

                            $arrayItem = [
                                'id' => $suggest_check_item_id,
                                'machines_maintenance_id' => $machines_maintenance_id,
                                'item_type' => $item_type,
                                'item_id' => $item_id,
                                'current_status' => $current_status,
                                'date_check' => $date_check,
                                'staff_check' => $staff_check,
                                'result_id' => $result_id,
                                'evaluate' => $evaluate,
                                'regulation_5s' => $regulation_5s,
                                'staff_manager' => $staff_manager,
                            ];
                            if (empty($arrayItem['id'])) {
                                $arrayItem['suggest_check_id'] = $id;
                            }
                            $items[] = $arrayItem;
                        }
                    }
                    $fields = [
                        'date' => $date,
                        'id_category_task' => $id_category_task,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'suggest_maintenance_id' => $suggest_maintenance_id,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_check', $fields);
                    if ($success) {
                        $arrayNotDelete = [];
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                if (empty($value['id'])) {
                                    unset($value['id']);
                                    $success_items = $this->db->insert('tbl_suggest_check_item', $value);
                                    if (!empty($success_items)) {
                                        $arrayNotDelete[] = $this->db->insert_id();
                                    }
                                } else {
                                    $this->db->where('id', $value['id']);
                                    $this->db->update('tbl_suggest_check_item', $value);
                                    $arrayNotDelete[] = $value['id'];
                                }
                            }
                        }
                        if (!empty($arrayNotDelete)) {
                            $this->db->where_not_in('id', $arrayNotDelete);
                        }
                        $this->db->where('suggest_check_id', $id);
                        $this->db->delete('tbl_suggest_check_item');

                        insertActivityLog([
                            'type_parent_obj' => 'suggest_check',
                            'table_obj' => 'tbl_suggest_check',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kiểm tra') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestCheck) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_check');
            }
            else {
                if (!$this->preEditSuggestCheck) {
                    accessDenied(true);
                }

                $this->db->select('
						tbl_suggest_check_item.item_type,
						tbl_suggest_check_item.item_id
				');
                $this->db->select('if(tbl_suggest_check_item.item_type = "machines", tbl_machines.name, tbl_cleaning.name) as name');
                $this->db->from('tbl_suggest_check_item');
                $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
                $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
                $this->db->join('tbl_cleaning', 'tbl_cleaning.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');
                $this->db->group_by('tbl_suggest_check_item.item_type, tbl_suggest_check_item.item_id');
                $dtItems = $this->db->get()->result_array();
                if (!empty($dtItems)) {
                    foreach ($dtItems as $key => $value) {
                        if ($value['item_type'] == 'machines') {
                            $this->db->select('
								tbl_suggest_check_item.*,
								tbl_machines_5s.name as name_machines_maintenance,
								CONCAT("' . base_url() . '", tbl_machines_5s.img) as img,
							');
                            $this->db->join('tbl_machines_5s', 'tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id', 'left');
                            $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
                            $this->db->where('item_type', $value['item_type']);
                            $this->db->where('item_id', $value['item_id']);
                            $dtItems[$key]['detail'] = $this->db->get_where('tbl_suggest_check_item')->result_array();
                        } else {
                            $this->db->select('
								tbl_suggest_check_item.*,
								tbl_cleaning_detail.name as name_machines_maintenance,
								CONCAT("' . base_url() . '", tbl_cleaning_detail.img) as img,
							');
                            $this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id', 'left');
                            $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
                            $this->db->where('item_type', $value['item_type']);
                            $this->db->where('item_id', $value['item_id']);
                            $dtItems[$key]['detail'] = $this->db->get_where('tbl_suggest_check_item')->result_array();
                        }
                    }
                }



                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_check');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_check');
        $data['dtResult'] = get_table_where('tbl_result');

        $this->db->select(['id', 'code', 'content']);
        $data['category_task'] = $this->db->get_where('tblcategory_tasks', ['hide' => 0])->result_array();

        $data['suggest_maintenance_id'] = $this->db->get_where('tbl_suggest_maintenance')->result_array();
        $this->load->view('admin/suggest_check/detail', $data);
    }

    public function view($id)
    {
        if (!$this->preViewSuggestCheck) {
            $this->db->group_start();
            $this->db->where('tbl_suggest_check.created_by', get_staff_user_id());
            $this->db->or_where('EXISTS (
						SELECT 1
						FROM tbl_suggest_check_item
						WHERE tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id
						AND staff_check = ' . get_staff_user_id() . ' OR staff_manager = ' . get_staff_user_id() . '
						LIMIT 1
					)
					OR tbl_suggest_check.created_by =' . get_staff_user_id(), false, false);
            $this->db->group_end();
            $this->db->where('tbl_suggest_check.id', $id);
            $ktPer = $this->db->get('tbl_suggest_check')->row();
            if (empty($ktPer)) {
                accessDenied(true);
            }
        }

        $data = [];
        $data['title'] = lang('dt_view_suggest_check');

        $this->db->select('
            tbl_suggest_check.*,
        ');
        $this->db->from('tbl_suggest_check');
        $this->db->where('tbl_suggest_check.id', $id);
        $dtData = $this->db->get()->row_array();

        //		if($dtData['type'] == 'machines') {
        //			$this->db->select('tbl_suggest_check_item.*,
        //            	tbl_result.name as name_result,
        //            	tbl_machines_5s.name as name_machines,
        //            	CONCAT("'.base_url().'", tbl_machines_5s.img) as img,
        //			');
        //			$this->db->from('tbl_suggest_check_item');
        //			$this->db->join('tbl_machines_5s', 'tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id');
        //			$this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_check_item.result_id', 'left');
        //			$this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
        //			$dtItems = $this->db->get()->result_array();
        //		}
        //		else {
        //			$this->db->select('tbl_suggest_check_item.*,
        //            	tbl_result.name as name_result,
        //            	tbl_cleaning_detail.name as name_machines,
        //            	CONCAT("'.base_url().'", tbl_cleaning_detail.img) as img,
        //			');
        //			$this->db->from('tbl_suggest_check_item');
        //			$this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id');
        //			$this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_check_item.result_id', 'left');
        //			$this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
        //			$dtItems = $this->db->get()->result_array();
        //		}

        $this->db->select('
						tbl_suggest_check_item.item_type,
						tbl_suggest_check_item.item_id
				');
        $this->db->select('if(tbl_suggest_check_item.item_type = "machines", tbl_machines.name, tbl_cleaning.name) as name');
        $this->db->from('tbl_suggest_check_item');
        $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
        $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
        $this->db->join('tbl_cleaning', 'tbl_cleaning.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');
        $this->db->group_by('tbl_suggest_check_item.item_type, tbl_suggest_check_item.item_id');
        $dtItems = $this->db->get()->result_array();
        if (!empty($dtItems)) {
            foreach ($dtItems as $key => $value) {
                if ($value['item_type'] == 'machines') {
                    $this->db->select('
								tbl_suggest_check_item.*,
								tbl_machines_5s.name as name_machines_maintenance,
								CONCAT("' . base_url() . '", tbl_machines_5s.img) as img,
							');
                    $this->db->join('tbl_machines_5s', 'tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id', 'left');
                    $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
                    $this->db->where('item_type', $value['item_type']);
                    $this->db->where('item_id', $value['item_id']);
                    $dtItems[$key]['detail'] = $this->db->get_where('tbl_suggest_check_item')->result_array();
                } else {
                    $this->db->select('
								tbl_suggest_check_item.*,
								tbl_cleaning_detail.name as name_machines_maintenance,
								CONCAT("' . base_url() . '", tbl_cleaning_detail.img) as img,
							');
                    $this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id', 'left');
                    $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
                    $this->db->where('item_type', $value['item_type']);
                    $this->db->where('item_id', $value['item_id']);
                    $dtItems[$key]['detail'] = $this->db->get_where('tbl_suggest_check_item')->result_array();
                }
            }
        }

        $data['dtData'] = $dtData;
        $data['dtItems'] = $dtItems;
        $this->load->view('admin/suggest_check/view', $data);
    }

    public function search_suggest_repalce($string_id = '')
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
			CONCAT("machines_", tbl_machines.id) as id,
			CONCAT(tbl_machines.name, " (", tbl_machines.code, ")") as text,
			tbl_machines.code as code,
			tbl_machines.name as name,
		', false);
        $this->db->from('tbl_machines');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->where('EXISTS (SELECT 1 FROM tbl_machines_5s WHERE tbl_machines_5s.machines_id = tbl_machines.id AND tbl_machines_5s.ballot_type = 0 LIMIT 1)', false, false);
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];

        $this->db->select('
			CONCAT("cleaning_", tbl_cleaning.id) as id,
			CONCAT(tbl_cleaning.name, " (", tbl_cleaning.code_group, ")") as text,
			tbl_cleaning.code_group as code,
			tbl_cleaning.name as name,
		', false);
        $this->db->from('tbl_cleaning');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_cleaning.name', $term);
            $this->db->or_like('tbl_cleaning.code_group', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_cleaning.ballot_type', 0);
        $this->db->where('EXISTS (SELECT 1 FROM tbl_cleaning_detail WHERE tbl_cleaning_detail.id_cleaning = tbl_cleaning.id LIMIT 1)', false, false);
        $this->db->limit($limit);
        $cleaning = $this->db->get()->result_array();

        $data['results'][] = [
            'text' => lang('Khu vực'),
            'children' => $cleaning
        ];

        if (!empty($string_id)) {
            $string_id = explode('_', $string_id);
            $id = $string_id[1];
            $type = $string_id[0];
            if ($type == 'cleaning') {
                $dtCleaning = get_table_where('tbl_cleaning', [
                    'id' => $id
                ], '', 'row_array');
                $data['row'] = [
                    'id' => ('cleaning_' . $dtCleaning['id']),
                    'text' => $dtCleaning['name'],
                    'type' => 'cleaning'
                ];
            } else if ($type == 'machines') {
                $dtMachines = get_table_where('tbl_machines', [
                    'id' => $id
                ], '', 'row_array');
                $data['row'] = [
                    'id' => ('machines_' . $dtMachines['id']),
                    'text' => $dtMachines['name'],
                    'type' => 'machines'
                ];
            }
        }
        echo json_encode($data);
        die();
    }

    public function getMaintenaceMachinesDetail()
    {
        $data = [];
        $is_tring = $string_id = $this->input->post('string_id');
        $string_id = explode('_', $string_id);
        $id = $string_id[1];
        $type = $string_id[0];

        if ($type == 'cleaning') {
            $this->db->select('
				tbl_cleaning_detail.id,
				tbl_cleaning_detail.name,
				tbl_cleaning_detail.note as note,
				CONCAT("' . base_url() . '", tbl_cleaning_detail.img) as img,
			');
            $this->db->from('tbl_cleaning_detail');
            $this->db->where('tbl_cleaning_detail.id_cleaning', $id);
            $dtData = $this->db->get()->result_array();

            $this->db->select('tbl_cleaning.*');
            $this->db->select('"' . $type . '" as type');
            $main_data = $this->db->get_where('tbl_cleaning', ['id' => $id])->row();
        } else if ($type == 'machines') {
            $this->db->select('
				tbl_machines_5s.id,
				tbl_machines_5s.name,
				tbl_machines_5s.note,
				CONCAT("' . base_url() . '", tbl_machines_5s.img) as img,
			');
            $this->db->from('tbl_machines_5s');
            $this->db->where('tbl_machines_5s.machines_id', $id);
            $this->db->where('tbl_machines_5s.ballot_type', 0);
            $dtData = $this->db->get()->result_array();

            $this->db->select('tbl_machines.*');
            $this->db->select('"' . $type . '" as type');
            $main_data = $this->db->get_where('tbl_machines', ['id' => $id])->row();
        }

        $data['dtMaintenaceMachines'] = $dtData;
        $data['dtMaintenace'] = $main_data;



        echo json_encode($data);
    }


    public function delete($id)
    {
        if (!$this->preDeleteSuggestCheck) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $ktTask = $this->db->get_where('tbltasks', ['rel_id' => $id, 'rel_type' => 'suggest_check'])->row();
        if (!empty($ktTask)) {
            $data['result'] = 0;
            $data['message'] = lang('Đang tồn tại phiếu công việc nên không thể xóa');
            echo json_encode($data);
            die();
        }


        $data = [];
        $this->db->select('tbl_suggest_check.*');
        $this->db->from('tbl_suggest_check');
        $this->db->where('tbl_suggest_check.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_check');
        if ($success) {

            $this->db->where('tbl_suggest_check_item.suggest_check_id', $id);
            $this->db->delete('tbl_suggest_check_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_check',
                'table_obj' => 'tbl_suggest_check',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kiểm tra') . ' [' . $dtData['reference_no'] . ']',
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

    public function getMaintenaceMachines()
    {
        $data = [];
        $machines_id = $this->input->post('machines_id');
        $this->db->select('tbl_machines_maintenance.*');
        $this->db->from('tbl_machines_maintenance');
        $this->db->where('tbl_machines_maintenance.machines_id', $machines_id);
        $dtData = $this->db->get()->result_array();
        $data['dtMaintenaceMachines'] = $dtData;
        echo json_encode($data);
    }

    public function exportExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
               tbl_suggest_check.id as id,
               tbl_suggest_check.reference_no as reference_no,
               tbl_suggest_check.date as date,
               IF(tbl_suggest_check_item.item_type = "machines", tbl_machines.name, tbl_cleaning.name) as name_machines,
               IF(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.img, tbl_cleaning_detail.img) as img,
               IF(tbl_suggest_check_item.item_type = "machines", tbl_machines_5s.name, tbl_cleaning_detail.name) as name_machines_detail,
               tbl_suggest_check_item.current_status as current_status,
               tbl_suggest_check_item.date_check as date_check,
               tbl_suggest_check_item.staff_check as staff_check,
               tbl_result.name as name_result,
               tbl_suggest_check_item.evaluate as evaluate,
               tbl_suggest_check_item.regulation_5s as regulation_5s,
               tbl_suggest_check_item.staff_manager as staff_manager,
               tbl_suggest_check_item.item_type as item_type,
               tbl_suggest_check_item.item_id as item_id,
            ');
            $this->db->from('tbl_suggest_check');
            $this->db->join('tbl_suggest_check_item', 'tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id', 'left');
            $this->db->join('tblbranch', 'tblbranch.id = tbl_suggest_check.branch_id', 'inner');

            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
            $this->db->join('tbl_cleaning', 'tbl_cleaning.id = tbl_suggest_check_item.item_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');

            $this->db->join('tbl_machines_5s', 'tbl_machines_5s.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "machines"', 'left');
            $this->db->join('tbl_cleaning_detail', 'tbl_cleaning_detail.id = tbl_suggest_check_item.machines_maintenance_id AND tbl_suggest_check_item.item_type = "cleaning"', 'left');

            //            $this->db->join('tbl_machines_maintenance','tbl_machines_maintenance.id = tbl_suggest_check_item.machines_maintenance_id','left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_check_item.result_id', 'left');


            if (!$this->preViewSuggestCheck) {
                $this->db->group_start();
                $this->db->where('tbl_suggest_check.created_by', get_staff_user_id());
                $this->db->or_where('EXISTS (
						SELECT 1
						FROM tbl_suggest_check_item
						WHERE tbl_suggest_check_item.suggest_check_id = tbl_suggest_check.id
						AND staff_check = ' . get_staff_user_id() . ' OR staff_manager = ' . get_staff_user_id() . '
						LIMIT 1
					)
					OR tbl_suggest_check.created_by =' . get_staff_user_id(), false, false);
                $this->db->group_end();
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_check.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_check.date <= '" . $end_date_search . "'");
            }
            $this->db->where("tbl_suggest_check.ballot_type", 0);

            $this->db->order_by('tbl_suggest_check.id desc');
            $dtData = $this->db->get()->result_array();






            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
            $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
                ->setWidth(20);
            $decimals_money = get_option('decimals_money');
            $decimals_number = get_option('decimals_number');
            $number_excel_money = '#,##0' . ($decimals_money > 0 ? '.' . sprintf("%0" . $decimals_money . "s", 0) : '');
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf(
                "%0" . $decimals_number . "s",
                0
            ) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue(
                'A1',
                ('PHIẾU YÊU CẦU KIỂM TRA VSATLĐ-5S')
            )->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Khu Vực/Thiết Bị');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Tên Khu vực/ Tên Thiết bị')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Danh Mục Kiểm Tra')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Quy Định 5S')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Hình Ảnh')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Người Kiểm Tra')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Kết Quả')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Đánh Giá')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Quản Lý Khu Vực/Thiết Bị')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'QR')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:M$sttRow")->applyFromArray([
                'font' => array(
                    'size' => 12,
                    'name'  => 'Times New Roman'
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '92D050'),
                ),
            ]);
            $this->load->library('ciqrcode');
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['item_type'] == 'machines' ? 'Thiết Bị' : ($value['item_type'] == 'cleaning' ? 'Khu Vực' : '')))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['name_machines_detail'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['name_machines'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['regulation_5s'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['img'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", !empty($value['staff_check']) ? get_staff_full_name($value['staff_check']) : '')->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['name_result'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['evaluate'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", !empty($value['staff_manager']) ? get_staff_full_name($value['staff_manager']) : '')->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])) {
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_check||' . $value['id'];
                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_suggest_check', ['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_check/';
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
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('M' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", '')->getStyle("M$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



                    $img = $value['img'];
                    if (!empty($img)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($img);
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('H' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", '')->getStyle("H$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:M$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_kiem_tra') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);;
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

    public function check_result()
    {
        $id = $this->input->get('id');
        $result = $this->input->get('result');

        if (empty($result)) {
            $result = 0;
        }
        $messaAdd = 'Duyệt';
        if ($result == 0) {
            $messaAdd = 'Bỏ Duyệt';
        }

        $this->db->where('id', $id);
        $success = $this->db->update('tbl_suggest_check_item', [
            'result_id' => $result
        ]);
        if (!empty($success)) {

            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Check ' . $messaAdd . ' kết quả thành công']);
            die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Check ' . $messaAdd . ' kết quả không thành công']);
        die();
    }
}
