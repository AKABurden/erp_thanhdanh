<?php

// header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Quality_control extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->tnh = true;
        $this->load->model('quality_control_model');
        $this->load->model('manufactures_model');
        $this->load->model('products_model');
        $this->load->model('unit_model');

        $this->perViewQC = has_permission('quality_control', '', 'view');
        $this->perViewOwnQC = has_permission('quality_control', '', 'view_own');
        $this->perAddQC = has_permission('quality_control', '', 'create');
        $this->perEditQC = has_permission('quality_control', '', 'edit');
        $this->perDeleteQC = has_permission('quality_control', '', 'delete');
        $this->perExportQC = has_permission('quality_control', '', 'export');
        $this->perPrintQC = has_permission('quality_control', '', 'print');
        $this->perNotification = has_permission('quality_control', '', 'notifications');
        $this->branchID = get_staff_user_id_branch();
        $this->isAdmin = is_admin();
    }

    public function category_errors()
    {
        if (!$this->perViewQC && !$this->perViewOwnQC) {
            accessDenied();
        }

        $data['tnh'] = true;
        $data['title'] = _l('category_errors');
        $this->load->view('admin/quality_control/category_errors', $data);
    }

    public function add_category_error()
    {
        if (!$this->perAddQC) {
            accessDenied(true);
        }

        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_errors.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->insertCategoryErrors($options);
                if ($id) {
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
        } else {
            $this->load->view('admin/quality_control/add_category_error', $data);
        }
    }

    public function getCategoryErrors()
    {
        $this->datatables->select("
            tbl_category_errors.id as id,
            0 as records,
            tbl_category_errors.code as code,
            tbl_category_errors.name as name,
            tbl_category_errors.note as note,
            '' as sub
            ", false)
            ->from('tbl_category_errors');
        $this->datatables->where('tbl_category_errors.parent_id', 0);

        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quality_control/edit_category_error/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/quality_control/delete_category_error/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        $result = json_decode($this->datatables->generate());
        foreach ($result->aaData as $key => $value) {
            $id = $value[0];
            $output = null;
            $result->aaData[$key][5] = $this->recursiveTableCategoryErrors($output, $id);
        }
        echo (json_encode($result));
    }

    function recursiveTableCategoryErrors(&$output = null, $parent_id = 0, $indent = null, $stt = 1)
    {

        $this->db->select('*');
        $this->db->from('tbl_category_errors');
        $this->db->where('tbl_category_errors.parent_id', $parent_id);
        $this->db->order_by('tbl_category_errors.parent_id');
        $query = $this->db->get()->result_array();

        foreach ($query as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $output .= '<tr>
                    <td>' . $indent . '' . $item['code'] . '</td>
                    <td>' . $item['name'] . '</td>
                    <td>' . $item['note'] . '</td>
                    <td>
                        <div>
                        <a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quality_control/edit_category_error/' . $item['id'] . '"><i class="fa fa-pencil"></i></a>
                        <button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="bottom" data-content="
                        <button href=\'' . base_url('admin/quality_control/delete_category_error/' . $item['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                        "><i class="fa fa-remove"></i></button>
                        </div>
                    </td>
                </tr>';
                $this->recursiveTableCategoryErrors($output, $item['id'], $indent . "|---", ++$stt);
            }
        }

        return $output;
    }

    public function edit_category_error($id)
    {
        if (!$this->perEditQC) {
            accessDenied(true);
        }

        $data = [];
        $category = $this->quality_control_model->rowCategoryErrors($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_errors.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $parent_id = $this->input->post('parent_id') ? $this->input->post('parent_id') : 0;

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'parent_id' => $parent_id,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->updateCategoryErrors($id, $options);
                if ($id) {
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
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $this->load->view('admin/quality_control/edit_category_error', $data);
        }
    }

    public function delete_category_error($id)
    {
        $data = [];
        if (!$this->perDeleteQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            if ($this->quality_control_model->checkExistCategoryError($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);

                return;
            }
            if ($this->quality_control_model->checkParentCategoryErrorId($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_remove_sub_items');
                echo json_encode($data);
                die;
            }
            if ($this->quality_control_model->deleteCategoryErrors($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function category_checks()
    {
        if (!$this->perViewQC && !$this->perViewOwnQC) {
            accessDenied();
        }

        $data['tnh'] = true;
        $data['title'] = _l('category_checks');
        $this->load->view('admin/quality_control/category_checks', $data);
    }

    public function add_category_check()
    {
        if (!$this->perAddQC) {
            accessDenied(true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_checks.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->insertCategoryChecks($options);
                if ($id) {
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
        } else {
            $this->load->view('admin/quality_control/add_category_check', $data);
        }
    }

    public function getCategoryChecks()
    {
        $this->datatables->select("
            tbl_category_checks.id as id,
            tbl_category_checks.code as code,
            tbl_category_checks.name as name,
            tbl_category_checks.note as note,
        ", false)
            ->from('tbl_category_checks');

        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quality_control/edit_category_check/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/quality_control/delete_category_check/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function edit_category_check($id)
    {
        if (!$this->perEditQC) {
            accessDenied(true);
        }

        $data = [];
        $category = $this->quality_control_model->rowCategoryChecks($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_checks.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->updateCategoryChecks($id, $options);
                if ($id) {
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
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $this->load->view('admin/quality_control/edit_category_check', $data);
        }
    }

    public function delete_category_check($id)
    {
        $data = [];
        if (!$this->perDeleteQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            if ($this->quality_control_model->checkExistCategoryChecks($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);

                return;
            }
            if ($this->quality_control_model->deleteCategoryChecks($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function category_cause_errors()
    {
        if (!$this->perViewQC && !$this->perViewOwnQC) {
            accessDenied();
        }

        $data['tnh'] = true;
        $data['title'] = _l('category_cause_errors');
        $this->load->view('admin/quality_control/category_cause_errors', $data);
    }

    public function add_category_cause_error()
    {
        if (!$this->perAddQC) {
            accessDenied(true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules(
                'code',
                lang("code"),
                'required|is_unique[tbl_category_cause_errors.code]'
            );
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->insertCategoryCauseErrors($options);
                if ($id) {
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
        } else {
            $this->load->view('admin/quality_control/add_category_cause_error', $data);
        }
    }

    public function getCategoryCauseErrors()
    {
        $this->datatables->select("
            tbl_category_cause_errors.id as id,
            tbl_category_cause_errors.code as code,
            tbl_category_cause_errors.name as name,
            tbl_category_cause_errors.note as note,
        ", false)
            ->from('tbl_category_cause_errors');

        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quality_control/edit_category_cause_error/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/quality_control/delete_category_cause_error/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function edit_category_cause_error($id)
    {
        if (!$this->perEditQC) {
            accessDenied(true);
        }

        $data = [];
        $category = $this->quality_control_model->rowCategoryCauseErrors($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules(
                    'code',
                    lang("code"),
                    'required|is_unique[tbl_category_cause_errors.code]'
                );
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->updateCategoryCauseErrors($id, $options);
                if ($id) {
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
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $this->load->view('admin/quality_control/edit_category_cause_error', $data);
        }
    }

    public function delete_category_cause_error($id)
    {
        $data = [];
        if (!$this->perDeleteQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            if ($this->quality_control_model->checkExistCategoryCauseErrors($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);

                return;
            }
            if ($this->quality_control_model->deleteCategoryCauseErrors($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function category_results()
    {
        if (!$this->perViewQC && !$this->perViewOwnQC) {
            accessDenied();
        }

        $data['tnh'] = true;
        $data['title'] = _l('category_results');
        $this->load->view('admin/quality_control/category_results', $data);
    }

    public function add_category_result()
    {
        if (!$this->perAddQC) {
            accessDenied(true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_category_results.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->insertCategoryResults($options);
                if ($id) {
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
        } else {
            $this->load->view('admin/quality_control/add_category_result', $data);
        }
    }

    public function getCategoryResults()
    {
        $this->datatables->select("
            tbl_category_results.id as id,
            tbl_category_results.code as code,
            tbl_category_results.name as name,
            tbl_category_results.note as note,
        ", false)
            ->from('tbl_category_results');

        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quality_control/edit_category_result/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/quality_control/delete_category_result/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function edit_category_result($id)
    {
        if (!$this->perEditQC) {
            accessDenied(true);
        }

        $data = [];
        $category = $this->quality_control_model->rowCategoryResults($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules(
                    'code',
                    lang("code"),
                    'required|is_unique[tbl_category_results.code]'
                );
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quality_control_model->updateCategoryResults($id, $options);
                if ($id) {
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
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $this->load->view('admin/quality_control/edit_category_result', $data);
        }
    }

    public function delete_category_result($id)
    {
        $data = [];
        if (!$this->perDeleteQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            if ($this->quality_control_model->checkExistCategoryResults($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);

                return;
            }
            if ($this->quality_control_model->deleteCategoryResults($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function detail_errors()
    {
        $data['tnh'] = true;
        $data['title'] = _l('tnh_detail_errors');
        $this->load->view('admin/quality_control/detail_errors', $data);
    }

    public function add_detail_error()
    {
        if (!$this->perAddQC) {
            accessDenied(true);
        }
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            $this->form_validation->set_rules('category_id', lang("category_errors"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_detail_errors.code]');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $category_id = $this->input->post('category_id');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'category_error_id' => $category_id,
                ];

                $id = $this->quality_control_model->insertDetailErrors($options);
                if ($id) {
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
        } else {
            $data['categories'] = $this->quality_control_model->getCategoryErrors();
            $this->load->view('admin/quality_control/add_detail_error', $data);
        }
    }

    public function getDetailErrors()
    {
        $this->datatables->select("
            tbl_detail_errors.id as id,
            tbl_category_errors.name as category_name_error,
            tbl_detail_errors.code as code,
            tbl_detail_errors.name as name,
            tbl_detail_errors.note as note,
        ", false)
            ->from('tbl_detail_errors')
            ->join('tbl_category_errors', 'tbl_category_errors.id = tbl_detail_errors.category_error_id', 'inner');

        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quality_control/edit_detail_error/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
        <button href=\'' . base_url('admin/quality_control/delete_detail_error/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function edit_detail_error($id)
    {
        if (!$this->perEditQC) {
            accessDenied(true);
        }

        $data = [];
        $category = $this->quality_control_model->rowDetailErrors($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('category_id', lang("category_errors"), 'required');
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_detail_errors.code]');
            }
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $code = $this->input->post('code');
                $note = $this->input->post('note');
                $category_id = $this->input->post('category_id');

                $options = [
                    'name' => $name,
                    'code' => $code,
                    'note' => $note,
                    'category_error_id' => $category_id,
                ];

                $id = $this->quality_control_model->updateDetailErrors($id, $options);
                if ($id) {
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
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $data['categories'] = $this->quality_control_model->getCategoryErrors();
            $this->load->view('admin/quality_control/edit_detail_error', $data);
        }
    }

    public function delete_detail_error($id)
    {
        $data = [];
        if (!$this->perDeleteQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            if ($this->quality_control_model->checkExistDetailErrors($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);

                return;
            }

            $this->db->from('tbl_check_quality_items_error');
            $this->db->where('tbl_check_quality_items_error.id_error',$id);
            $checkQua = $this->db->count_all_results();
            if ($checkQua){
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);die();
            }

            if ($this->quality_control_model->deleteDetailErrors($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function searchDetailErrors($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $category_error_id = $params['category_error_id'];
        $results = false;
        if (!empty($category_error_id)) {
            $results = $this->quality_control_model->searchDetailErrors($term, $limit, $category_error_id);
        }
        $data['results'] = $results;
        if ($id) {
            // $data['row'] = ['id' => $shipping['id'], 'text' => $shipping['address']];
        }
        echo json_encode($data);
    }

    public function checkExistCheckQualityReferenceNo($reference_no)
    {
        $this->db->from('tbl_check_quality');
        $this->db->where('tbl_check_quality.reference_no', $reference_no);

        return $this->db->get()->num_rows();
    }

    public function check_quality()
    {
        if (!$this->perViewQC && !$this->perViewOwnQC) {
            accessDenied();
        }
        $data['manager_approve'] = $this->quality_control_model->countQcByStatus('manager_approve');
        $data['gdx_approve'] = $this->quality_control_model->countQcByStatus('gdx_approve');
        $data['all'] = $this->quality_control_model->countQcByStatus('all');
        $data['tnh'] = $this->tnh;
        $data['title'] = _l('check_quality');
        $this->load->view('admin/quality_control/check_quality', $data);
    }

    public function getCheckQuality()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $arrIDStaff = employee_manage_staff();
        $arrBranch = get_branch_staff();
        $status_table = $this->input->post('status_table');
        $order_search = $this->input->post('order_search');
        $customer_search = $this->input->post('customer_search');
        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $QtyItemsRecy = "(
            SELECT
                SUM(tbl_check_quality_items.quantity_recycling) as quantity_recycling
            FROM tbl_check_quality_items
            WHERE tbl_check_quality_items.check_quality_id = tbl_check_quality.id
        )";
        $QtyItemsWaste = "(
            SELECT
                SUM(tbl_check_quality_items.quantity_waste) as quantity_waste
            FROM tbl_check_quality_items
            WHERE tbl_check_quality_items.check_quality_id = tbl_check_quality.id
        )";
        $QtyItemsSuccess = "(
            SELECT
                SUM(tbl_check_quality_items.quantity_success) as qty_item_success
            FROM tbl_check_quality_items
            WHERE tbl_check_quality_items.check_quality_id = tbl_check_quality.id
        )";

        $tb_tamp = "(
            SELECT 
                GROUP_CONCAT(DISTINCT tblclients.company SEPARATOR '<br>') as company,
                tbl_check_quality_items.check_quality_id as check_quality_id
            FROM tbl_check_quality_items
            LEFT JOIN tbl_orders ON tbl_orders.id = tbl_check_quality_items.order_id AND tbl_check_quality_items.order_id != 0
            LEFT JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_check_quality_items.order_id != 0
            GROUP BY tbl_check_quality_items.check_quality_id
        ) tb_tamp";

        $this->datatables->select("
            tbl_check_quality.id as id,
            tbl_check_quality.date as date,
            CONCAT(tbl_check_quality.reference_no,'__',coalesce(tblbranch.name, '')) as reference_no,
            tbl_check_quality.pod_id as pod_id,
            tb_tamp.company as company,
            CONCAT(tbl_check_quality.order_id,'||',tbl_check_quality.plan_id) as order_id,
            tbl_check_quality.quantity_qc as quantity_qc,
            COALESCE($QtyItemsRecy,0) + COALESCE($QtyItemsWaste,0) as qty_item_error,
            COALESCE($QtyItemsSuccess,0) as qty_item_success,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as created_by,
            tbl_check_quality.note as note,
        ", false)
            ->from('tbl_check_quality')
            ->join('tblstaff', 'tblstaff.staffid = tbl_check_quality.created_by', 'left')
            ->join($tb_tamp, 'tb_tamp.check_quality_id = tbl_check_quality.id', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_check_quality.id_branch', 'left');

        // if (!empty($status_table) && $status_table != 'all') {
        //     if ($status_table == "manager_approve") {
        //         $this->datatables->where('tbl_check_quality.status_process', 1);
        //     } elseif ($status_table == 'gdx_approve') {
        //         $this->datatables->where('tbl_check_quality.status_process', 0);
        //     }
        // }


        if (!empty($order_search)) {
            $this->datatables->where('EXISTS (
                SELECT 1
                FROM tbl_check_quality_items
                LEFT JOIN tbl_orders ON tbl_orders.id = tbl_check_quality_items.order_id AND tbl_check_quality_items.order_id != 0
                WHERE tbl_check_quality_items.check_quality_id = tbl_check_quality.id
                AND tbl_orders.id = '.$order_search.'
            )');
        }

        if (!empty($customer_search)){
            $this->datatables->where('EXISTS (
                SELECT 1
                FROM tbl_check_quality_items
                LEFT JOIN tbl_orders ON tbl_orders.id = tbl_check_quality_items.order_id AND tbl_check_quality_items.order_id != 0
                WHERE tbl_check_quality_items.check_quality_id = tbl_check_quality.id
                AND tbl_orders.customer_id = '.$customer_search.'
            )');
        }

        if (!empty($start_date_search)){
            $this->datatables->where('DATE_FORMAT(tbl_check_quality.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)){
            $this->datatables->where('DATE_FORMAT(tbl_check_quality.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }

        if (!$this->perViewQC) {
            if ($arrIDStaff != array()) {
                $coverStr = implode(",", $arrIDStaff);
                $this->datatables->where('tbl_check_quality.created_by IN (' . $coverStr . ')');
            }
        } else {
            if (!$this->isAdmin) {
                if (!empty($arrBranch)) {
                    $coverStrBranch = implode(",", $arrBranch);
                    $this->datatables->where('tbl_check_quality.id_branch IN (' . $coverStrBranch . ')');
                } else {
                    $this->datatables->where('tbl_check_quality.id',0);
                }
            }
        }


        $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quality_control/viewQualityControl/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu QC') . '</a>';
        $print = $this->perPrintQC ? '<a href="' . base_url('admin/quality_control/print_check_quality/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('phiếu QC') . '</a>' : '';
        $edit = $this->perEditQC ? '<a href="' . base_url('admin/quality_control/edit_check_quality/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu QC') . '</a>' : '';
        $delete = $this->perDeleteQC ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/quality_control/deleteCheckQuality/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu QC') . '</a>' : '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li>' . $print . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        foreach ($data->aaData as $key => $value) {
            $pod = $value[3];
            $object_type = explode('||', $value[5]);
            $order = $object_type[0];
            $plan = $object_type[1];
            $id = $value[0];

            $options = $value[3];
            $labelOptions = '';
            $countPod = 0;
            $labelOptionsOrder = '';
            $countOrder = 0;
            $labelOptionsPlan = '';
            $countPlan = 0;

            if (!empty($options)) {
                $options = explode(',', $options);
                foreach ($options as $k => $v) {
                    if ($v != 0) {
                        $countPod++;
                    }
                }
            }
            if ($countPod > 0) {
                $labelOptions = '<div><span data-toggle="tooltip" data-html="true" title="' . get_pod_news($pod) . '" class="label label-primary pointer">' . lang('LSXCT') . ' [' . $countPod . '] ' . lang('tnh_single') . '</span></div>';
                $labelOptionsPo = get_po_new($pod);
            }

            if (!empty($order)) {
                $order_array = explode(',', $order);
                foreach ($order_array as $k => $v) {
                    if ($v != 0) {
                        $countOrder++;
                    }
                }
            }
            if ($countOrder > 0) {
                $labelOptionsOrder = '<div><span data-toggle="tooltip" data-html="true" title="' . get_orders_news($order) . '" class="label label-primary pointer">' . lang('Đơn hàng bán') . ' [' . $countOrder . '] ' . lang('tnh_single') . '</span></div>';
            }

            if (!empty($plan)) {
                $plan_array = explode(',', $plan);
                foreach ($plan_array as $k => $v) {
                    if ($v != 0) {
                        $countPlan++;
                    }
                }
            }
            if ($countPlan > 0) {
                $labelOptionsPlan = '<div><span data-toggle="tooltip" data-html="true" title="' . get_plan_news($plan) . '" class="label label-warning pointer">' . lang('Kế hoạch BTP') . ' [' . $countPlan . '] ' . lang('tnh_single') . '</span></div>';
            }

            $data->aaData[$key][3] = $labelOptionsPo . '<br>';
            $data->aaData[$key][5] = $labelOptionsOrder . '<br>' . $labelOptionsPlan;

            // $data->aaData[$key][9] = text_align(process_qc_img($id).process_qc($id));


        }
        echo json_encode($data);
    }

    public function viewQualityControl($id)
    {
        $qualityControl = $this->quality_control_model->rowCheckQuality($id);
        $client = get_table_where('tblclients', ['userid' => $qualityControl['customer_id']], '', 'row_array');
        $pod = get_pods($qualityControl['pod_id']);
        $items = get_table_where('tbl_check_quality_items', ['check_quality_id' => $id], '', 'result_array');
        $branch = get_table_where('tblbranch', ['id' => $qualityControl['id_branch']], '', 'row_array');
        $branch_name = '';
        if (!empty($branch)) {
            $branch_name = $branch['name'];
        }

        $data['pod'] = $pod;
        $data['items'] = $items;
        $data['client'] = $client;
        $data['qualityControl'] = $qualityControl;
        $data['created_by'] = get_staff_full_name($qualityControl['created_by']);
        $data['branch'] = $branch_name;

        $this->db->where('id_check_quality', $id);
        $this->db->order_by('date_create', 'desc');
        $data['feedback'] = $this->db->get('tblcheck_quality_feedback')->result();
        foreach ($data['feedback'] as $key => $value) {
            $this->db->where('rel_id', $value->id);
            $this->db->where('rel_type', 'feedback_cq');
            $data['feedback'][$key]->file = $this->db->get('tblfiles')->result();
        }
        $this->load->view('admin/quality_control/view_quality_control', $data);
    }

    public function deleteCheckQuality($id)
    {
        $data = [];
        if (!$this->perDeleteQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            $qualityControl = $this->quality_control_model->rowCheckQuality($id);

            //tnh
            $isQCDelete = isQCDeleteEdit($id);
            if (!empty($isQCDelete)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã có phiếu xuất kho hoặc nhập thành phẩm cho qc này không được xóa');
                echo json_encode($data);
                die;
            }
            //
            $pod = get_pods($qualityControl['pod_id']);
            $CheckQualityItems = get_table_where(
                'tbl_check_quality_items',
                ['check_quality_id' => $id],
                '',
                'result_array'
            );

            $this->manufactures_model->handlingCheckQualityItemsStage($id, 'update');
            if ($this->quality_control_model->deleteCheckQuality($id)) {
                $this->quality_control_model->deleteCheckQualityItems($id);
                foreach ($CheckQualityItems as $key => $value) {
                    $productionsDetail = get_table_where(
                        'tbl_productions_orders_details',
                        ['id' => $value['pod_id']],
                        '',
                        'row_array'
                    );
                    // $this->manufactures_model->updateProductionsOrdersDetailsById($value['pod_id'],
                    //     ['qty_qc' => $productionsDetail['qty_qc'] - $value['quantity_qc']]);
                    $this->quality_control_model->deleteCheckQualityItemsError($value['id']);

                    if (!empty($value['images_multiple'])) {
                        $images_multiple = explode('||', $value['images_multiple']);
                        if (!empty($images_multiple)) {
                            foreach ($images_multiple as $k => $v) {
                                if (file_exists('uploads/check_quality/' . $v)) {
                                    @unlink('uploads/check_quality/' . $v);
                                }
                            }
                        }
                    }
                }

                $this->manufactures_model->handlingCheckQualityItemsStage($id);
                $content = lang('tnh_his_delete_check_quality_productions_orders_details');
                $content = str_replace('{$1}', $qualityControl['reference_no'], $content);
                $content = str_replace('{$2}', $pod, $content);
                insertActivityLog([
                    'type_parent_obj' => 'check_quality',
                    'table_obj' => 'tbl_check_quality',
                    'id_obj' => $id,
                    'name_obj' => $qualityControl['reference_no'],
                    'content' => $content,
                    'actions' => 'delete',
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

    public function agreeQualityControl()
    {
        $data = [];

        if (!$this->perApproveQC) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }

        if ($this->input->post()) {
            $quality_id = $this->input->post('quality_id');
            $status = $this->input->post('status');
            $qualityControl = $this->quality_control_model->rowCheckQuality($quality_id);
            $pod = $this->manufactures_model->rowProductionsOrdersDetais($qualityControl['pod_id']);
            if ($pod['quantity_warehoused'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_warehousing_not_un_change_qc');
                echo json_encode($data);
                die;
            }
            $date = date('Y-m-d H:i');
            $user_id = get_staff_user_id();
            if ($qualityControl['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }
            $up = $this->quality_control_model->updateCheckQuality($quality_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id,
            ]);
            if ($up) {

                $content = lang('tnh_his_agree_check_quality_productions_orders_details');
                $content = str_replace('{$1}', $$qualityControl['reference_no'], $content);
                $content = str_replace('{$2}', $pod['reference_no'], $content);
                insertActivityLog([
                    'type_parent_obj' => 'check_quality',
                    'table_obj' => 'tbl_check_quality',
                    'id_obj' => $quality_id,
                    'name_obj' => $qualityControl['reference_no'],
                    'content' => $content,
                    'actions' => 'agree',
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

    public function add_check_quality()
    {
        if (!$this->perAddQC) {
            accessDenied();
        }
        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules(
                'reference_no',
                lang("Số QC"),
                'required|is_unique[tbl_check_quality.reference_no]'
            );
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('id_branch', lang("Chi nhánh xưởng"), 'required');
            // $this->form_validation->set_rules('customer_id', lang("Khách hàng"), 'required');
            // $this->form_validation->set_rules('order_production_details', lang("order_production_details"), 'required');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note');
                $customer_id = $this->input->post('customer_id');
                $customer_id = explode('__', $customer_id);
                $count_items = 0;
                $total_quantity = 0;
                $counter = $this->input->post('counter');
                $id_branch = $this->input->post('id_branch');

                $order_id_text = '';
                $pod_id_text = '';
                $plan_id_text = '';
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $data_json_taiche = '';
                        $data_json_phe = '';
                        $item_id = $this->input->post('item_id')[$value];
                        if (empty($item_id)) {
                            continue;
                        }
                        $arrs = explode('__', $item_id);
                        $item_id = $arrs[1];
                        $type_item = $arrs[0];
                        $product = $this->products_model->rowProduct($item_id);
                        if (empty($product)) {
                            continue;
                        }

                        $quantity_qc = number_unformat($this->input->post('quantity_qc')[$value]);
                        $quantity_che = number_unformat($this->input->post('quantity_che')[$value]);
                        $quantity_phe = number_unformat($this->input->post('quantity_phe')[$value]);
                        $data_json_taiche = $this->input->post('data_json_taiche')[$value];
                        $data_json_phe = $this->input->post('data_json_phe')[$value];
                        $pod_id = $this->input->post('pod_id')[$value];
                        $id_stage = $this->input->post('id_stage')[$value];
                        $object_type = $this->input->post('object_type')[$value];
                        $order_id = $this->input->post('order_id')[$value];
                        $plan_id = $this->input->post('plan_id')[$value];
                        $result = $this->input->post('result')[$value];
                        $id_stage_again = $this->input->post('id_stage_again')[$value];
                        if (!empty($this->input->post('id_stage_again')[$value])) {
                            $id_stage_again = $this->input->post('id_stage_again')[$value];
                        } else {
                            $id_stage_again = 0;
                        }
                        if ($result == 1) {
                            $id_stage_again = 0;
                        }


                        $item_code = $product['code'];
                        $item_name = $product['name'];

                        $cqis_id = !empty($this->input->post('cqis_id')[$value]) ? $this->input->post('cqis_id')[$value] : 0;

                        $checkQualityItems[] = [
                            'type_item' => $type_item,
                            'item_id' => $item_id,
                            'item_code' => $item_code,
                            'item_name' => $item_name,
                            'quantity_qc' => $quantity_qc,
                            'quantity_recycling' => $quantity_che,
                            'quantity_waste' => $quantity_phe,
                            'quantity_success' => ($quantity_qc - ($quantity_che + $quantity_phe)),
                            'pod_id' => $pod_id,
                            'order_id' => $order_id,
                            'plan_id' => $plan_id,
                            'object_type' => $object_type,
                            'data_json_phe' => $data_json_phe,
                            'data_json_taiche' => $data_json_taiche,
                            'id_stage' => $id_stage,
                            'result' => $result,
                            'id_stage_again' => $id_stage_again,
                            'cqis_id' => $cqis_id,
                        ];
                        $order_id_text .= $order_id . ',';
                        $pod_id_text .= $pod_id . ',';
                        $plan_id_text .= $plan_id . ',';
                        $total_quantity += $quantity_qc;
                        $count_items++;
                    }
                }

                if (empty($checkQualityItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }

                $order_id_text = trim($order_id_text, ',');
                $pod_id_text = trim($pod_id_text, ',');
                $plan_id_text = trim($plan_id_text, ',');
                $fields = [
                    'reference_no' => $reference_no,
                    'date' => $date,
                    // 'customer_id' => $customer_id[1],
                    'count_items' => $count_items,
                    'quantity_qc' => $total_quantity,
                    'id_branch' => $id_branch,
                    'note' => $note,
                    'pod_id' => $pod_id_text,
                    'order_id' => $order_id_text,
                    'plan_id' => $plan_id_text,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];
                //    print_arrays($checkQualityItems, $fields);
                $id = $this->quality_control_model->insertCheckQuality($fields);
                $check_noti = false;
                $dataHtml = '';
                if ($id) {
                    if (getReference('checkQuality') == $reference_no) {
                        updateReference('checkQuality');
                    }
                    foreach ($checkQualityItems as $key => $value) {
                        $value['check_quality_id'] = $id;
                        $data_json_phe = [];
                        $data_json_taiche = [];
                        if (!empty($value['data_json_phe'])) {
                            $data_json_phe = json_decode($value['data_json_phe']);
                        }
                        if (!empty($value['data_json_taiche'])) {
                            $data_json_taiche = json_decode($value['data_json_taiche']);
                        }
                        $check_quality_item_id = $this->quality_control_model->insertCheckQualityItem($value);

                        if ($check_quality_item_id) {
                            if (!empty($data_json_phe)) {
                                foreach ($data_json_phe as $k => $v) {
                                    $item_phe = [
                                        'id_check_quality_item' => $check_quality_item_id,
                                        'id_error' => $v->reason_id,
                                        'quantity' => $v->quantity_quote,
                                        'type' => $v->type,
                                    ];
                                    $this->quality_control_model->insertCheckQualityItemError($item_phe);
                                }
                            }
                            if (!empty($data_json_taiche)) {
                                foreach ($data_json_taiche as $kk => $vv) {
                                    $item_che = [
                                        'id_check_quality_item' => $check_quality_item_id,
                                        'id_error' => $vv->reason_id,
                                        'quantity' => $vv->quantity_quote,
                                        'type' => $vv->type,
                                    ];
                                    $this->quality_control_model->insertCheckQualityItemError($item_che);
                                }
                            }

                            // $item_productions_check_quality = [
                            //     'pod_id' => $value['pod_id'],
                            //     'check_quality_id' => $value['check_quality_id'],
                            //     'check_quality_item_id' => $check_quality_item_id,
                            //     'stage_id' => $value['id_stage'],
                            //     'qty_qc'=>$value['quantity_qc']
                            // ];
                            // $this->db->insert('tbl_check_quanlity_production_details_item',$item_productions_check_quality);

                        }

                        // $pod = get_table_where('tbl_productions_orders_details', ['id' => $value['pod_id']], '',
                        //     'row_array');
                        // $this->db->where('id', $value['pod_id']);
                        // $this->db->update('tbl_productions_orders_details',
                        //     ['qty_qc' => $pod['qty_qc'] + $value['quantity_qc']]);
                    }

                    //tnh
                    $this->manufactures_model->handlingCheckQualityItemsStage($id);
                    //tnh

                    //noti
                    $notifiedUsers = [];
                    $getAllStaff = get_table_where('tblstaff', ['active' => 1], '', 'result_array');
                    $arrPod = [];
                    foreach ($checkQualityItems as $key => $value) {
                        if ($value['result'] == 2) {
                            notificationQCNotAchieved($id, get_staff_user_id(), $value);
                            $pod = get_table_where('tbl_productions_orders_details', ['id' => $value['pod_id']], '', 'row_array');
                            $stage = get_table_where('tbl_stages', ['id' => $value['id_stage']], '', 'row_array');
                            $dataHtml = 'Sản phẩm ' . $value['item_name'] . ' (' . $pod['reference_no'] . ') Không đạt chất lượng tại công đoạn ' . $stage['name'] . ' cần sản xuất lại! Vui lòng kiểm tra ' . $reference_no;
                            if (!empty($getAllStaff)) {
                                foreach ($getAllStaff as $key => $val) {
                                    if (has_permission('quality_control', $val['staffid'], 'notifications')) {
                                        $branchID = get_staff_user_id_branch_app($val['staffid']);
                                        if ($branchID == 1) {
                                            $notification_data = [
                                                'description' => "<a target='_blank' href='" . base_url('admin/manufactures/detail_productions/' . $value['pod_id']) . "'> " . $dataHtml . '</a>',
                                                'touserid' => $val['staffid'],
                                                'link' => '',
                                                'type' => 1,

                                            ];
                                            if (add_notification($notification_data)) {
                                                array_push($notifiedUsers, $val['staffid']);
                                            }
                                        } else {
                                            if ($id_branch == $branchID) {
                                                $notification_data = [
                                                    'description' => "<a target='_blank' href='" . base_url('admin/manufactures/detail_productions/' . $value['pod_id']) . "'> " . $dataHtml . '</a>',
                                                    'touserid' => $val['staffid'],
                                                    'link' => '',
                                                    'type' => 1,
                                                ];
                                                if (add_notification($notification_data)) {
                                                    array_push($notifiedUsers, $val['staffid']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $arrPod[] = $value['pod_id'];
                        }
                    }

                    pusher_trigger_notification($notifiedUsers);
                    //end

                    if (!empty($arrPod)) {
                        $pod_id_achieved = implode(',', $arrPod);
                        $po_id = get_po_id($pod_id_achieved);
                        if (!empty($po_id)) {
                            $array = explode(',', $po_id);
                            foreach ($array as $kk => $vv) {
                                $arrPodNew = [];
                                $arrStage = [];
                                $this->db->select('tbl_productions_orders_details.id as pod_id,tbl_check_quality_items.id_stage as id_stage,tbl_productions_orders.id as po_id');
                                $this->db->from('tbl_productions_orders_details');
                                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id');
                                $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.pod_id = tbl_productions_orders_details.id');
                                $this->db->where_in('tbl_productions_orders_details.id', $arrPod);
                                $this->db->where('tbl_productions_orders.id', $vv);
                                $this->db->where('tbl_check_quality_items.check_quality_id', $id);
                                $pods = $this->db->get()->result_array();
                                if (!empty($pods)) {
                                    foreach ($pods as $k => $v) {
                                        $arrPodNew[] = $v['pod_id'];
                                        $arrStage[] = $v['id_stage'];
                                    }
                                }
                                notificationQCAchieved($id, get_staff_user_id(), $vv, $arrStage, $arrPodNew);
                            }
                        }
                    }

                    @pusherTNHNotfication();
                    $content = lang('tạo mới QC');
                    $content = str_replace('{$1}', $reference_no, $content);
                    insertActivityLog([
                        'type_parent_obj' => 'check_quality',
                        'table_obj' => 'tbl_check_quality',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => $content,
                        'actions' => 'add',
                    ]);


                    $data['result'] = 1;
                    $data['actions'] = 'add';
                    $data['message'] = lang('success');
                    set_alert('success', lang('success'));
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        // if ($this->input->post()) {
        //     print_arrays($this->input->post());
        // }
        $pois = $this->input->post('arrPOIS');
        $product = [];
        $cKey = 0;
        if (!empty($pois)) {
            // $arrPOIS = explode(',', $pois);
            $arrPOIS = $pois;
            $pois_id = $arrPOIS[0];
            $this->db->select('
                tbl_productions_orders_items_stages.id as id,
                tbl_productions_orders_items_stages.productions_orders_items_id as productions_orders_items_id,
                tbl_productions_orders_items_stages.stage_id as stage_id,
                tbl_productions_orders_items_stages.number as number,
                tbl_productions_orders.location_id,
            ');
            $this->db->from('tbl_productions_orders_items_stages');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_items_stages.productions_orders_id');
            $this->db->where('tbl_productions_orders_items_stages.id', $pois_id);
            $pois = $this->db->get()->row_array();
            if (!empty($pois)) {
                $data['location_id'] = $pois['location_id'];
            }

            if (!empty($arrPOIS)) {
                $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
                ';
                $this->db->select(
                    '
                CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                tbl_products.name as name,
                tbl_products.code as code,
                tbl_products.unit_id as unit_id,
                tblunits.unit as unit_name,
                tbl_products.price_import as price_import,
                tbl_products.images as images,
                CONCAT(tbl_orders.reference_no) as reference_no,
                CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                tbl_productions_orders_details.reference_no as reference_no_production_detail,
                COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
                ' . $qtQC . ' as qty_qc,
                tbl_productions_orders_details.id as pod_id,
                tbl_colors.name as name_color,
                tbl_productions_orders_details.object_type as object_type,
                tbl_business_plan.id as plan_id,
                tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                tbl_productions_orders_items_stages.number as number,
                tbl_productions_orders_items_stages.id as pois_id,
                tbl_orders.id as idd',
                    false
                );

                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join(
                    'tbl_productions_orders_details',
                    'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id',
                    'left'
                );

                $this->db->join(
                    'tbl_productions_orders_items',
                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                    'left'
                );
                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                $this->db->join(
                    'tbl_products',
                    'tbl_products.id = tbl_productions_orders_items.items_id ',
                    'left'
                );
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
                $this->db->where_in('tbl_productions_orders_items_stages.id', $arrPOIS);

                $this->db->order_by('tbl_products.name', 'DESC');
                $this->db->group_by('tbl_productions_orders_details.id');
                $product = $this->db->get()->result_array();
                if (!empty($product)) {
                    foreach ($product as $key => $value) {
                        $items = [];

                        $stage_item = get_table_where('tbl_productions_orders_items_stages', ['productions_orders_items_id' => $value['productions_orders_item_id'], 'number' => ($value['number'] - 1)], '', 'row_array');

                        $this->db->select('tbl_check_quality_items_stage.id, tbl_check_quality_items_stage.status_result');
                        $this->db->from('tbl_check_quality_items_stage');
                        $this->db->where('tbl_check_quality_items_stage.pois_id', $stage_item['id']);
                        $this->db->where('tbl_check_quality_items_stage.number', $stage_item['number']);
                        $this->db->order_by('tbl_check_quality_items_stage.id DESC');
                        $dtCQIS = $this->db->get()->row_array();
                        if (!empty($dtCQIS)) {
                            if ($dtCQIS['status_result'] != 0) {
                                continue;
                            } else {
                                $cqis_id = $dtCQIS['id'];
                                $this->db->select('
                                    tbl_check_quality_items_stage.id as cqis_id,
                                    CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                                    CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                                    tbl_products.name as name,
                                    tbl_products.code as code,
                                    tbl_products.unit_id as unit_id,
                                    tblunits.unit as unit_name,
                                    tbl_products.price_import as price_import,
                                    tbl_products.images as images,
                                    CONCAT(tbl_orders.reference_no) as reference_no,
                                    CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                                    tbl_productions_orders_details.reference_no as reference_no_production_detail,
                                    tbl_check_quality_items.quantity_recycling as total_qty,
                                    tbl_check_quality_items.quantity_recycling as qty_qc,
                                    tbl_productions_orders_details.id as pod_id,
                                    tbl_colors.name as name_color,
                                    tbl_productions_orders_details.object_type as object_type,
                                    tbl_business_plan.id as plan_id,
                                    tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                                    tbl_productions_orders_items_stages.number as number,
                                    tbl_orders.id as idd,
                                    tbl_productions_orders.location_id as location_id,
                                ', false);
                                $this->db->from('tbl_check_quality_items_stage');
                                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_check_quality_items_stage.po_id');
                                $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.id = tbl_check_quality_items_stage.check_quality_items_id');
                                $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_check_quality_items_stage.pois_id');
                                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'inner');
                                $this->db->join(
                                    'tbl_productions_orders_items',
                                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                                    'inner'
                                );
                                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                                $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id ', 'inner');
                                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                                $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                                $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id AND tbl_products_colors.color_id != 0', 'left');
                                $this->db->order_by('tbl_products.name', 'DESC');
                                $this->db->group_by('tbl_productions_orders_details.id');
                                $this->db->where('tbl_check_quality_items_stage.id', $cqis_id);
                                $product_return = $this->db->get()->row_array();
                                if (!empty($product_return)) {
                                    $product[$cKey] = $product_return;
                                }
                            }
                        }

                        $item_id = explode('__', $value['id']);
                        $item_id = $item_id[1];
                        $type_item = $item_id[0];
                        $info = $this->products_model->rowProduct($item_id);
                        $this->db->select('*');
                        $this->db->from('tbl_product_stages');
                        $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                        $this->db->where('tbl_product_stages.product_id', $item_id);
                        $stages =  $this->db->get()->row_array();
                        if (!empty($stages)) {
                            $productions_orders_item_id = $value['productions_orders_item_id'];
                            $stageProduction = "(
                                SELECT active
                                FROM tbl_productions_orders_items_stages
                                WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                                LIMIT 1
                            )";
                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->where('tbl_stages.status_qc', 1);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items = $this->db->get()->result_array();

                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items_stage = $this->db->get()->result_array();
                        }

                        // $stage_item = get_table_where('tbl_productions_orders_items_stages',['productions_orders_items_id'=>$value['productions_orders_item_id'],'number'=>($value['number'] - 1)],'','row_array');
                        if (!empty($stage_item)) {
                            $product[$cKey]['stages_default'] = $stage_item['stage_id'];
                        } else {
                            $product[$cKey]['stages_default'] = 0;
                        }
                        $product[$cKey]['stages'] = $items;
                        $product[$cKey]['stages_again'] = $items_stage;
                        $cKey++;
                    }
                }
            }
        }

        $arrCQIS = $this->input->post('arrCQIS');
        if (!empty($arrCQIS)) {
            foreach ($arrCQIS as $kCQIS => $vCQIS) {
                $this->db->select('tbl_check_quality_items_stage.check_quality_id, tbl_check_quality_items_stage.check_quality_items_id, tbl_check_quality_items_stage.number');
                $this->db->from('tbl_check_quality_items_stage');
                $this->db->where('tbl_check_quality_items_stage.id', $vCQIS);
                $dtCQIS = $this->db->get()->row_array();

                $this->db->select('tbl_check_quality_items_stage.id');
                $this->db->from('tbl_check_quality_items_stage');
                $this->db->where('tbl_check_quality_items_stage.check_quality_items_id', $dtCQIS['check_quality_items_id']);
                $this->db->where('tbl_check_quality_items_stage.number', $dtCQIS['number'] - 1);
                $dtCQISPre = $this->db->get()->row_array();
                $cqis_id = $dtCQISPre['id'];

                $this->db->select('
                    tbl_check_quality_items_stage.id as cqis_id,
                    CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                    CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                    tbl_products.name as name,
                    tbl_products.code as code,
                    tbl_products.unit_id as unit_id,
                    tblunits.unit as unit_name,
                    tbl_products.price_import as price_import,
                    tbl_products.images as images,
                    CONCAT(tbl_orders.reference_no) as reference_no,
                    CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                    tbl_productions_orders_details.reference_no as reference_no_production_detail,
                    tbl_check_quality_items.quantity_recycling as total_qty,
                    tbl_check_quality_items.quantity_recycling as qty_qc,
                    tbl_productions_orders_details.id as pod_id,
                    tbl_colors.name as name_color,
                    tbl_productions_orders_details.object_type as object_type,
                    tbl_business_plan.id as plan_id,
                    tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                    tbl_productions_orders_items_stages.number as number,
                    tbl_orders.id as idd,
                    tbl_productions_orders.location_id as location_id,
                ', false);
                $this->db->from('tbl_check_quality_items_stage');
                $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_check_quality_items_stage.po_id');
                $this->db->join('tbl_check_quality_items', 'tbl_check_quality_items.id = tbl_check_quality_items_stage.check_quality_items_id');
                $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.id = tbl_check_quality_items_stage.pois_id');
                $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items_stages.productions_orders_items_id', 'inner');
                $this->db->join(
                    'tbl_productions_orders_items',
                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                    'inner'
                );
                $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
                $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
                $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id ', 'inner');
                $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
                $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
                $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id AND tbl_products_colors.color_id != 0', 'left');
                $this->db->order_by('tbl_products.name', 'DESC');
                $this->db->group_by('tbl_productions_orders_details.id');
                $this->db->where('tbl_check_quality_items_stage.id', $cqis_id);
                $product_return = $this->db->get()->result_array();
                if (!empty($product_return)) {
                    foreach ($product_return as $kP => $vP) {
                        $temp = $vP;
                        if (empty($data['location_id'])) {
                            $data['location_id'] = $vP['location_id'];
                        }

                        $items = [];
                        $item_id = explode('__', $vP['id']);
                        $item_id = $item_id[1];
                        $type_item = $item_id[0];
                        $info = $this->products_model->rowProduct($item_id);
                        $this->db->select('*');
                        $this->db->from('tbl_product_stages');
                        $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                        $this->db->where('tbl_product_stages.product_id', $item_id);
                        $stages =  $this->db->get()->row_array();
                        if (!empty($stages)) {
                            $productions_orders_item_id = $vP['productions_orders_item_id'];
                            $stageProduction = "(
                                SELECT active
                                FROM tbl_productions_orders_items_stages
                                WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                                LIMIT 1
                            )";
                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->where('tbl_stages.status_qc', 1);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items = $this->db->get()->result_array();

                            $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                            $this->db->from('tbl_product_stages_versions');
                            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                            $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                            $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                            $items_stage = $this->db->get()->result_array();
                        }

                        $stage_item = get_table_where('tbl_productions_orders_items_stages', ['productions_orders_items_id' => $vP['productions_orders_item_id'], 'number' => ($vP['number'])], '', 'row_array');
                        if (!empty($stage_item)) {
                            $temp['stages_default'] = $stage_item['stage_id'];
                        } else {
                            $temp['stages_default'] = 0;
                        }
                        $temp['stages'] = $items;
                        $temp['stages_again'] = $items_stage;
                        $product[$cKey] = $temp;
                        $cKey++;
                    }
                }
            }
        }
        $data['product_manu'] = $product;

        $pod_id_detail = $this->input->post('pod_id');
        $stage_id_detail = $this->input->post('stage_id');
        $product_manu_detail = [];
        $type_qc = 0;
        if (!empty($pod_id_detail) && !empty($stage_id_detail)) {
            $stageDb = get_table_where('tbl_stages', ['id' => $stage_id_detail], '', 'row_array');
            $this->db->select('
                tbl_productions_orders.location_id,
            ');
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id');
            $this->db->where('tbl_productions_orders_details.id', $pod_id_detail);
            $proDetail = $this->db->get()->row_array();
            if (!empty($proDetail)) {
                $data['location_id'] = $proDetail['location_id'];
            }

            $qtQC = '
            COALESCE(
            (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
            ';
            $this->db->select(
                '
                CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
                CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
                tbl_products.name as name,
                tbl_products.code as code,
                tbl_products.unit_id as unit_id,
                tblunits.unit as unit_name,
                tbl_products.price_import as price_import,
                tbl_products.images as images,
                CONCAT(tbl_orders.reference_no) as reference_no,
                CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
                tbl_productions_orders_details.reference_no as reference_no_production_detail,
                COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
                ' . $qtQC . ' as qty_qc,
                tbl_productions_orders_details.id as pod_id,
                tbl_colors.name as name_color,
                tbl_productions_orders_details.object_type as object_type,
                tbl_business_plan.id as plan_id,
                tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
                tbl_orders.id as idd,
                tbl_orders.note as note_order',
                false
            );

            $this->db->from('tbl_productions_orders_details');
            $this->db->join(
                'tbl_productions_orders_items',
                'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                'left'
            );
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
            $this->db->join(
                'tbl_products',
                'tbl_products.id = tbl_productions_orders_items.items_id '
            );
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
            $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
            $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
            $this->db->where('tbl_productions_orders_details.id', $pod_id_detail);
            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->group_by('tbl_productions_orders_details.id');
            $product_manu_detail = $this->db->get()->result_array();
            if (!empty($product_manu_detail)) {
                foreach ($product_manu_detail as $key => $value) {
                    $items = [];
                    $items_stage = [];
                    $item_id = explode('__', $value['id']);
                    $item_id = $item_id[1];
                    $type_item = $item_id[0];
                    $info = $this->products_model->rowProduct($item_id);
                    $this->db->select('*');
                    $this->db->from('tbl_product_stages');
                    $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                    $this->db->where('tbl_product_stages.product_id', $item_id);
                    $stages =  $this->db->get()->row_array();
                    //                    if(!empty($stages)){
                    $productions_orders_item_id = $value['productions_orders_item_id'];
                    $stageProduction = "(
                            SELECT active
                            FROM tbl_productions_orders_items_stages
                            WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                            LIMIT 1
                        )";
                    $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
//                    $this->db->where('tbl_stages.status_qc', 1);
                    $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                    $items = $this->db->get()->result_array();

                    $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
                    $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                    $items_stage = $this->db->get()->result_array();
                    //                    }
                    $product_manu_detail[$key]['stages'] = $items;
                    $product_manu_detail[$key]['stages_again'] = $items_stage;
                    $product_manu_detail[$key]['stages_default'] = $stage_id_detail;
                }
            }
        }
        $data['product_manu_detail'] = $product_manu_detail;

        $data['tnh'] = $this->tnh;
        $data['reference_no'] = getReference('checkQuality');
        $data['title'] = lang('Thêm phiếu QC');
        $data['breadcrumb'] = [
            array(
                'link' => base_url('admin/quality_control/check_quality'),
                'page' => lang('QC'),
            ),
            array('link' => '#', 'page' => lang('Thêm phiếu QC')),
        ];
        $this->load->view('admin/quality_control/add_check_quality', $data);
    }

    public function edit_check_quality($id)
    {
        if (!$this->perEditQC) {
            accessDenied();
        }
        $checkQuality = get_table_where('tbl_check_quality', ['id' => $id], '', 'row_array');
        if ($this->input->post('edit')) {
            $data = [];
            if ($checkQuality['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_orders"), 'trim|required|is_unique[tbl_check_quality.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('id_branch', lang("Chi nhánh xưởng"), 'required');
            if ($this->form_validation->run() == true) {
                // print_arrays($this->input->post());
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note');
                $customer_id = $this->input->post('customer_id');
                $customer_id = explode('__', $customer_id);
                $count_items = 0;
                $total_quantity = 0;
                $counter = $this->input->post('counter');
                $id_branch = $this->input->post('id_branch');

                $order_id_text = '';
                $pod_id_text = '';
                $plan_id_text = '';
                $arr_id_not = [];
                if (!empty($counter)) {
                    foreach ($counter as $key => $value) {
                        $data_json_taiche = '';
                        $data_json_phe = '';
                        $item_id = $this->input->post('item_id')[$value];
                        if (empty($item_id)) {
                            continue;
                        }
                        $arrs = explode('__', $item_id);
                        $item_id = $arrs[1];
                        $type_item = $arrs[0];
                        $product = $this->products_model->rowProduct($item_id);
                        if (empty($product)) {
                            continue;
                        }

                        $quantity_qc = number_unformat($this->input->post('quantity_qc')[$value]);
                        $quantity_che = number_unformat($this->input->post('quantity_che')[$value]);
                        $quantity_phe = number_unformat($this->input->post('quantity_phe')[$value]);
                        $data_json_taiche = $this->input->post('data_json_taiche')[$value];
                        $data_json_phe = $this->input->post('data_json_phe')[$value];
                        $pod_id = $this->input->post('pod_id')[$value];
                        $id_stage = $this->input->post('id_stage')[$value];
                        $object_type = $this->input->post('object_type')[$value];
                        $order_id = $this->input->post('order_id')[$value];
                        $plan_id = $this->input->post('plan_id')[$value];
                        $result = $this->input->post('result')[$value];
                        $id_stage_again = $this->input->post('id_stage_again')[$value];

                        if (!empty($this->input->post('id_old')[$value])) {
                            $id_old = $this->input->post('id_old')[$value];
                        } else {
                            $id_old = 0;
                        }
                        if (!empty($this->input->post('id_stage_again')[$value])) {
                            $id_stage_again = $this->input->post('id_stage_again')[$value];
                        } else {
                            $id_stage_again = 0;
                        }
                        if ($result == 1) {
                            $id_stage_again = 0;
                        }


                        $cqis_id = !empty($this->input->post('cqis_id')[$value]) ? $this->input->post('cqis_id')[$value] : 0;

                        $item_code = $product['code'];
                        $item_name = $product['name'];


                        if (!empty($id_old)) {
                            $arr_id_not[] = $id_old;
                            $checkQualityItems[] = [
                                'id' => $id_old,
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'item_code' => $item_code,
                                'item_name' => $item_name,
                                'quantity_qc' => $quantity_qc,
                                'quantity_recycling' => $quantity_che,
                                'quantity_waste' => $quantity_phe,
                                'quantity_success' => ($quantity_qc - ($quantity_che + $quantity_phe)),
                                'pod_id' => $pod_id,
                                'order_id' => $order_id,
                                'plan_id' => $plan_id,
                                'object_type' => $object_type,
                                'data_json_phe' => $data_json_phe,
                                'data_json_taiche' => $data_json_taiche,
                                'id_stage' => $id_stage,
                                'result' => $result,
                                'id_stage_again' => $id_stage_again,
                                'cqis_id' => $cqis_id,
                            ];
                        } else {
                            $checkQualityItems[] = [
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'item_code' => $item_code,
                                'item_name' => $item_name,
                                'quantity_qc' => $quantity_qc,
                                'quantity_recycling' => $quantity_che,
                                'quantity_waste' => $quantity_phe,
                                'quantity_success' => ($quantity_qc - ($quantity_che + $quantity_phe)),
                                'pod_id' => $pod_id,
                                'order_id' => $order_id,
                                'plan_id' => $plan_id,
                                'object_type' => $object_type,
                                'data_json_phe' => $data_json_phe,
                                'data_json_taiche' => $data_json_taiche,
                                'id_stage' => $id_stage,
                                'result' => $result,
                                'id_stage_again' => $id_stage_again,
                                'cqis_id' => $cqis_id,
                            ];
                        }
                        $order_id_text .= $order_id . ',';
                        $pod_id_text .= $pod_id . ',';
                        $plan_id_text .= $plan_id . ',';
                        $total_quantity += $quantity_qc;
                        $count_items++;
                    }
                }

                if (empty($checkQualityItems)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_no_items');
                    echo json_encode($data);
                    die;
                }

                $order_id_text = trim($order_id_text, ',');
                $pod_id_text = trim($pod_id_text, ',');
                $plan_id_text = trim($plan_id_text, ',');
                $fields = [
                    'reference_no' => $reference_no,
                    'date' => $date,
                    'count_items' => $count_items,
                    'quantity_qc' => $total_quantity,
                    'id_branch' => $id_branch,
                    'note' => $note,
                    'pod_id' => $pod_id_text,
                    'order_id' => $order_id_text,
                    'plan_id' => $plan_id_text,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s'),
                ];

                $isQCDeleteEdit = isQCDeleteEdit($id, $arr_id_not);
                if (!empty($isQCDeleteEdit)) {
                    $data['result'] = 0;
                    $data['message'] = lang('Có mặt hàng qc này đã tạo xuất kho sản xuất hoặc nhập kho thành phẩm không thể xóa');
                    echo json_encode($data);
                    die;
                }
                //print_arrays($checkQualityItems, $fields);
                $op = $this->quality_control_model->updateCheckQuality($id, $fields);
                if ($op) {
                    $CheckQualityItemsOld = get_table_where(
                        'tbl_check_quality_items',
                        ['check_quality_id' => $id],
                        '',
                        'result_array'
                    );
                    $this->quality_control_model->deleteCheckQualityItems($id);
                    foreach ($CheckQualityItemsOld as $kk => $vv) {
                        $productionsDetail = get_table_where(
                            'tbl_productions_orders_details',
                            ['id' => $vv['pod_id']],
                            '',
                            'row_array'
                        );
                        // $this->manufactures_model->updateProductionsOrdersDetailsById($vv['pod_id'],
                        //     ['qty_qc' => $productionsDetail['qty_qc'] - $vv['quantity_qc']]);
                        $this->quality_control_model->deleteCheckQualityItemsError($vv['id']);
                    }
                    foreach ($checkQualityItems as $key => $value) {
                        $value['check_quality_id'] = $id;
                        $data_json_phe = [];
                        $data_json_taiche = [];
                        if (!empty($value['data_json_phe'])) {
                            $data_json_phe = json_decode($value['data_json_phe']);
                        }
                        if (!empty($value['data_json_taiche'])) {
                            $data_json_taiche = json_decode($value['data_json_taiche']);
                        }
                        $check_quality_item_id = $this->quality_control_model->insertCheckQualityItem($value);

                        if ($check_quality_item_id) {
                            if (!empty($data_json_phe)) {
                                foreach ($data_json_phe as $k => $v) {
                                    $item_phe = [
                                        'id_check_quality_item' => $check_quality_item_id,
                                        'id_error' => $v->reason_id,
                                        'quantity' => $v->quantity_quote,
                                        'type' => $v->type,
                                    ];
                                    $this->quality_control_model->insertCheckQualityItemError($item_phe);
                                }
                            }
                            if (!empty($data_json_taiche)) {
                                foreach ($data_json_taiche as $kk => $vv) {
                                    $item_che = [
                                        'id_check_quality_item' => $check_quality_item_id,
                                        'id_error' => $vv->reason_id,
                                        'quantity' => $vv->quantity_quote,
                                        'type' => $vv->type,
                                    ];
                                    $this->quality_control_model->insertCheckQualityItemError($item_che);
                                }
                            }
                        }

                        // $pod = get_table_where('tbl_productions_orders_details', ['id' => $value['pod_id']], '',
                        //     'row_array');
                        // $this->db->where('id', $value['pod_id']);
                        // $this->db->update('tbl_productions_orders_details',
                        //     ['qty_qc' => $pod['qty_qc'] + $value['quantity_qc']]);
                    }


                    $this->manufactures_model->handlingCheckQualityItemsStage($id);

                    @pusherTNHNotfication();
                    $content = lang('Sửa  QC');
                    $content = str_replace('{$1}', $reference_no, $content);
                    insertActivityLog([
                        'type_parent_obj' => 'check_quality',
                        'table_obj' => 'tbl_check_quality',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => $content,
                        'actions' => 'add',
                    ]);


                    $data['result'] = 1;
                    $data['actions'] = 'edit';
                    $data['message'] = lang('success');
                    set_alert('success', lang('success'));
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $items = get_table_where('tbl_check_quality_items', ['check_quality_id' => $id], '', 'result_array');
        $bodyItems = '';
        $counter = 0;
        $stage_text = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $stage_text .= $value['id_stage'] . ',';
                $images = '';
                $info = null;

                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
                if (!empty($info['images'])) {
                    $images = base_url('uploads/products/' . $info['images']);
                }

                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }
                $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_check_quality_items.quantity_qc) 
                FROM tbl_check_quality_items
                WHERE tbl_check_quality_items.id_stage = ' . $value['id_stage'] . ' AND tbl_check_quality_items.pod_id = ' . $value['pod_id'] . ' AND tbl_check_quality_items.id != ' . $value['id'] . ' ),0)
                ';
                $this->db->select('tbl_productions_orders_items.quantity,' . $qtQC . ' as qty_qc');
                $this->db->from('tbl_productions_orders_details');
                $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
                $this->db->where('tbl_productions_orders_details.id', $value['pod_id']);
                $result = $this->db->get()->row_array();
                $productionOrderDetail = get_table_where('tbl_productions_orders_details', ['id' => $value['pod_id']], '', 'row_array');

                $items = [];
                $this->db->select('*');
                $this->db->from('tbl_product_stages');
                $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                $this->db->where('tbl_product_stages.product_id', $items_id);
                $stages =  $this->db->get()->row_array();
                if (!empty($stages)) {
                    $productions_orders_item_id = $productionOrderDetail['productions_orders_item_id'];
                    $stageProduction = "(
                        SELECT active
                        FROM tbl_productions_orders_items_stages
                        WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                        LIMIT 1
                    )";
                    $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
//                    $this->db->where('tbl_stages.status_qc', 1);
                    $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                    $items = $this->db->get()->result_array();

                    $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
                    $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                    $items_stage = $this->db->get()->result_array();
                }
                $optionStage = '<option value=""></option>';
                if (!empty($items)) {
                    foreach ($items as $k => $val) {
                        $disabled = '';
                        if ($val['active'] == 0) {
                            $disabled = 'disabled';
                        }
                        $selected = $val['stage_id'] == $value['id_stage'] ? 'selected' : '';
                        $optionStage .= '<option ' . $disabled . ' ' . $selected . ' value="' . $val['stage_id'] . '">' . $val['stage_name'] . '</option>';
                    }
                }
                $optionStageAgain = '<option value=""></option>';
                if (!empty($items_stage)) {
                    foreach ($items_stage as $k => $val) {
                        $selected = $val['stage_id'] == $value['id_stage_again'] ? 'selected' : '';
                        $optionStageAgain .= '<option ' . $selected . ' value="' . $val['stage_id'] . '">' . $val['stage_name'] . '</option>';
                    }
                }
                $data_item = json_encode($items);
                $td_detail = '';
                $title = '';
                $reference_no = '';
                if ($value['object_type'] == "business_plan") {
                    $title = ' KHKD';
                    $order = get_table_where('tbl_business_plan', ['id' => $value['plan_id']], '', 'row_array');
                    $reference_no = $order['reference_no'];
                } elseif ($value['object_type'] == "orders") {
                    $title = 'Đơn hàng';
                    $order = get_table_where('tbl_orders', ['id' => $value['order_id']], '', 'row_array');
                    $reference_no = $order['reference_no'];
                }
                $td_detail = '' .
                    '<div class="bold" style="font-size: 12px;"></div>' .
                    '<div>Lệnh SXCT: ' . $productionOrderDetail['reference_no'] . ' - ' . $title . ': ' . $reference_no . '</div>' .
                    '';

                $tdNumber = '<div class="stt text-center">' . (++$key) . '</div>';
                $tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter[' . $counter . ']" id="counter" class="form-control counter" value="' . $counter . '">
                        <input type="hidden" name="cqis_id[' . $counter . ']" id="cqis_id" class="cqis_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $value['cqis_id'] . '">
                        <input type="hidden" name="item_id[' . $counter . ']" id="item_id" class="item_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $type_item . '__' . $items_id . '">
                        <input type="hidden" name="sum_qty[' . $counter . ']" id="sum_qty" class="sum_qty" style="width: 100%;"  value="' . $value['quantity_qc'] . '">
                        <input type="hidden" name="pod_id[' . $counter . ']" id="pod_id" class="pod_id" style="width: 100%;"  value="' . $value['pod_id'] . '">
                        <input type="hidden" name="order_id[' . $counter . ']" id="order_id" class="order_id" style="width: 100%;"  value="' . $value['order_id'] . '">
                        <input type="hidden" name="object_type[' . $counter . ']" id="object_type" class="object_type" style="width: 100%;"  value="' . $value['object_type'] . '">
                        <input type="hidden" name="plan_id[' . $counter . ']" id="plan_id" class="plan_id" style="width: 100%;"  value="' . $value['plan_id'] . '">
                        <input type="hidden" name="id_old[' . $counter . ']" id="id_old" class="id_old" style="width: 100%;"  value="' . $value['id'] . '">
                        <input type="hidden" name="check_quality_item_id[' . $counter . ']" id="check_quality_item_id" class="check_quality_item_id" style="width: 100%;"  value="' . $value['id'] . '">
                        <input type="hidden" class="check_item" value="' . $type_item . '__' . $items_id . '__' . $value['pod_id'] . '">' . $value['item_name'] . '(' . $value['item_code'] . ')
                        <input type="hidden" class="data_stage" value="' . tnh_htmlentities($data_item) . '"/></div>' .
                    '<div style="color:red;text-transform:uppercase" class="hide"> SL có thể QC : <span class="qty_qc">' . formatNumber($result['quantity'] - $result['qty_qc']) . '</span></div>' .
                    '<div><div class="row-options"><a href="javascript:void(0)" onclick="removeRow(this)"class="text-danger delete-remind remove-row">' . lang('delete') . '</a></div></div>';
                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                    '<div class="display-block contract-attachment-wrapper img">' .
                    '<div style="width:45px;">' .
                    '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                    '<div class="">' .
                    '<img src="' . $images . '" style="border-radius: 50%">' .
                    '</div>' .
                    '</a>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>';

                $tdColor = '<div class="td-item-color"><br><div style="font-style: italic;font-size: 13px">' . $td_detail . '</div></div>';
                $tdUnit = '<div class="td-unit text-center">' . $unit['unit'] . '</div>';
                $tdStage = '<div class="td-stage" style="width:200px"><select required class="id_stage" style="width: 100%; height: 30px" id="id_stage_' . $counter . '" name="id_stage[' . $counter . ']">' . $optionStage . '</select></div>';
                $tdQuantity = '<div class="td-sum-qty text-center"><input style="width: 100%;" type="text" name="quantity_qc[' . $counter . ']" id="quantity_qc[]" onchange="totalCheckQuality()" class="form-control quantity_qc number-format" value="' . $value['quantity_qc'] . '"><div class="show-error-item-qc text-danger"></div><div class="show-error-item text-danger"></div></div>';
                $hideChe = '';
                $hidePhe = '';
                $qty_json_taiche = 0;
                $qty_json_phe = 0;
                if ($value['quantity_recycling'] > 0) {
                    $hideChe = '';
                } else {
                    $hideChe = 'hide';
                }
                if ($value['quantity_waste'] > 0) {
                    $hidePhe = '';
                } else {
                    $hidePhe = 'hide';
                }
                $this->db->select_sum('quantity');
                $this->db->from('tbl_check_quality_items_error');
                $this->db->where('type', 1);
                $this->db->where('id_check_quality_item', $value['id']);
                $qty_json_taiche = $this->db->get()->row()->quantity;

                $this->db->select_sum('quantity');
                $this->db->from('tbl_check_quality_items_error');
                $this->db->where('type', 2);
                $this->db->where('id_check_quality_item', $value['id']);
                $qty_json_phe = $this->db->get()->row()->quantity;

                $tdquantityChe = '<div style="width:100px" class="td-quantity-che"><input style="width: 100%;" type="text" name="quantity_che[' . $counter . ']" id="quantity_che[]" onchange="totalCheckQuality()" class="form-control quantity_che number-format" value="' . formatNumber($value['quantity_recycling']) . '"><div class="mtop5"><i onclick="addListReason(this,1)" class="btn btn-primary ' . $hideChe . ' tai_che">Chi tiết lỗi</i></div><div class="show-error-tai-che text-danger"></div>' .
                    ' <input type="hidden" name="data_json_taiche[' . $counter . ']" class="form-control data_json_taiche" value="' . tnh_htmlentities($value['data_json_taiche']) . '">' .
                    ' <input type="hidden" class="form-control qty_json_taiche" value="' . $qty_json_taiche . '"></div>';
                $tdquantityPhe = '<div style="width:100px" class="td-quantity-phe"><input style="width: 100%;" type="text" name="quantity_phe[' . $counter . ']" id="quantity_phe[]" onchange="totalCheckQuality()" class="form-control quantity_phe number-format" value="' . formatNumber($value['quantity_waste']) . '"><div class="mtop5"><i onclick="addListReason(this,2)" class="btn btn-primary ' . $hidePhe . ' phe">Chi tiết lỗi</i></div><div class="show-error-phe text-danger"></div>' .
                    '<input type="hidden" name="data_json_phe[' . $counter . ']" class="form-control data_json_phe" value="' . tnh_htmlentities($value['data_json_phe']) . '">' .
                    '<input type="hidden" class="form-control qty_json_phe" value="' . $qty_json_phe . '"></div>';
                $tdQtyDat = '<div class="td-qty-dat text-center">' . formatNumber($value['quantity_qc'] - ($value['quantity_recycling'] + $value['quantity_waste'])) . '</div>';

                $checkRadio = '';
                $checkRadio2 = '';
                $classes = 'hide';
                if ($value['result'] == 1) {
                    $checkRadio = 'checked';
                } elseif ($value['result'] == 2) {
                    $checkRadio2 = 'checked';
                    $classes = '';
                }
                $tdResult = '<div style="width:200px"  class="td-result">
                <div class="radio radio-primary">
                    <input type="radio" value="1" ' . $checkRadio . '  id="result1' . $counter . '" class="result" checked name="result[' . $counter . ']"><label for="result1' . $counter . '">Đạt</label>
                </div>
            
                <div class="radio radio-primary">
                    <input type="radio" value="2" ' . $checkRadio2 . ' id="result2' . $counter . '"  class="result" name="result[' . $counter . ']"><label for="result2' . $counter . '">Không Đạt</label>
                </div>
                <div class="hide">
                <select class="id_stage_again ' . $classes . '" style="width: 100%; height: 30px" id="id_stage_again_' . $counter . '" name="id_stage_again[' . $counter . ']">' . $optionStageAgain . '</select>
                </div>
                </div>';
                $tdKhongDat = '<div class="td-khong-dat text-center">' . formatNumber(($value['quantity_recycling'] + $value['quantity_waste']) * 100 / $value['quantity_qc']) . '</div>';
                $tdDat = '<div class="td-dat text-center">' . formatNumber(($value['quantity_qc'] - ($value['quantity_recycling'] + $value['quantity_waste'])) * 100 / $value['quantity_qc']) . '</div>';
                $tdActions = '<div class="text-center"><a onclick="removeRow(this)"><i class="fa fa-remove btn btn-danger remove-row"></i></a></div>';

                $bodyItems .= '<tr class="chonse-tr">
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdImage . '</td>
                    <td>' . $tdCode . '</td>
                    <td>' . $tdColor . '</td>
                    <td>' . $tdStage . '</td>
                    <td>' . $tdQuantity . '</td>
                    <td>' . $tdquantityChe . '</td>
                    <td>' . $tdQtyDat . '</td>
                    <td>' . $tdResult . '</td>
                    <td>' . $tdKhongDat . '</td>
                    <td>' . $tdDat . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';
                $counter++;
            }
        }
        $stage_text = trim($stage_text, ',');
        $data['counter'] = $counter;
        $data['bodyItems'] = $bodyItems;
        $data['tnh'] = $this->tnh;
        $data['checkQuality'] = $checkQuality;
        $data['stage_text'] = $stage_text;
        $data['id'] = $id;
        $data['title'] = lang('Sửa phiếu QC');
        $data['breadcrumb'] = [
            array(
                'link' => base_url('admin/quality_control/check_quality'),
                'page' => lang('QC'),
            ),
            array('link' => '#', 'page' => lang('Sửa phiếu QC')),
        ];
        $this->load->view('admin/quality_control/edit_check_quality', $data);
    }

    public function searchProductionsOrdersDetail($id = false, $customer_ids = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $id_customer = 0;
        if (!empty($customer_ids)) {
            $customer_id = $customer_ids;
        } else {
            $customer_id = $this->input->get('customer_id');
            if (!empty($customer_id)) {
                $customer_id = explode('__', $customer_id);
                $id_customer = $customer_id[1];
            }
        }
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders_details.id as id, 
            CONCAT(tbl_productions_orders_details.reference_no, "(", CONCAT(tbl_productions_orders_items.items_code," ( ",tbl_productions_orders_items.items_name," ) "), ")") as text,
            tbl_orders.reference_no as reference_orders,
            tblclients.company as company,
            tbl_orders.id as order_id,
        ', false);
        $this->db->from('tbl_productions_orders_details');
        $this->db->join(
            'tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'left'
        );
        $this->db->join(
            'tbl_productions_plan_quota_item',
            'tbl_productions_plan_quota_item.id = tbl_productions_orders_items.production_plan_item_id',
            'left'
        );
        $this->db->join(
            'tbl_productions_plan_quota',
            'tbl_productions_plan_quota.id = tbl_productions_plan_quota_item.productions_plan_quota_id',
            'left'
        );
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_plan_quota.order_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_productions_plan_quota.customer_id', 'left');
        $this->db->where('tbl_productions_plan_quota.customer_id', $id_customer);

        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders_details.reference_no', $term);
            $this->db->or_like('tbl_orders.reference_no', $term);
            $this->db->or_like('tblclients.company', $term);
            $this->db->or_like('tbl_productions_orders_items.items_name', $term);
            $this->db->or_like('tbl_productions_orders_items.items_code', $term);
            $this->db->group_end();
        }
        $this->db->order_by('tbl_productions_orders_details.date_created', 'DESC');
        $this->db->limit($limit);

        $results = $this->db->get()->result_array();
        $data['results'] = $results;
        if ($id) {
            $this->db->select('
                tbl_productions_orders_details.id as id, 
                CONCAT(tbl_productions_orders_details.reference_no, "(", tbl_productions_orders_items.items_name, ")", "(" , tbl_orders.reference_no, ")", "(" , tblclients.company, ")") as text,
                tbl_orders.reference_no as reference_orders,
                tblclients.company as company
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join(
                'tbl_productions_orders_items',
                'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                'left'
            );
            $this->db->join(
                'tbl_productions_plan_quota_item',
                'tbl_productions_plan_quota_item.id = tbl_productions_orders_items.production_plan_item_id',
                'left'
            );
            $this->db->join(
                'tbl_productions_plan_quota',
                'tbl_productions_plan_quota.id = tbl_productions_plan_quota_item.productions_plan_quota_id',
                'left'
            );
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_plan_quota.order_id', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_productions_plan_quota.customer_id', 'left');

            $this->db->where('tbl_productions_orders_details.id', $id);
            $result = $this->db->get()->row_array();
            if ($result) {
                $data['row'] = ['id' => $result['id'], 'text' => $result['text']];
            }
        }
        echo json_encode($data);
    }

    public function searchProductByProduction($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $id_branch = $this->input->get('id_branch');
        if (!empty($params['checkQualityId'])) {
            $checkQualityId = $params['checkQualityId'];
            $stage_id_text = $params['stage_id'];
        }
        $id_customer = 0;
        if (!empty($customer_ids)) {
            $customer_id = $customer_ids;
        } else {
            $customer_id = $this->input->get('customer_id');
            if (!empty($customer_id)) {
                $customer_id = explode('__', $customer_id);
                $id_customer = $customer_id[1];
            }
        }

        $product = false;
        // if (!empty($id_customer)) {
        if (!empty($checkQualityId)) {
            $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_productions_orders_details.qty_qc) 
                WHERE tbl_productions_orders_details.id NOT IN (' . $checkQualityId . ') ),0)
                ';
            $tbQC = "(
                    SELECT
                    tbl_check_quality_items.pod_id as pod_id,
                        GROUP_CONCAT(CONCAT(tbl_stages.name,'__',tbl_check_quality_items.quantity_qc) SEPARATOR 'FF') as name_stages
                    FROM tbl_check_quality_items
                    LEFT JOIN tbl_stages on  tbl_stages.id = tbl_check_quality_items.id_stage
                    WHERE tbl_check_quality_items.id_stage  NOT IN ('$stage_id_text')
                ) tb_pod";
        } else {
            $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
                ';
            $tbQC = "(
                    SELECT
                    tbl_check_quality_items.pod_id as pod_id,
                        GROUP_CONCAT(CONCAT(tbl_stages.name,'__',tbl_check_quality_items.quantity_qc) SEPARATOR 'FF') as name_stages
                    FROM tbl_check_quality_items
                    LEFT JOIN tbl_stages on  tbl_stages.id = tbl_check_quality_items.id_stage
                ) tb_pod";
        }
        $this->db->select(
            '
            CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_products.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_products.price_import as price_import,
            tbl_products.images as images,
            CONCAT(tbl_orders.reference_no) as reference_no,
            CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
            tbl_productions_orders_details.reference_no as reference_no_production_detail,
            COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
            ' . $qtQC . ' as qty_qc,
            tbl_productions_orders_details.id as pod_id,
            tbl_colors.name as name_color,
            tbl_productions_orders_details.object_type as object_type,
            tbl_business_plan.id as plan_id,
            COALESCE(tb_pod.name_stages, "") as name_stages,
            tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
            tbl_orders.id as idd,
            tbl_orders.note as note_order',
            false
        );

        $this->db->from('tbl_productions_orders_details');
        $this->db->join(
            'tbl_productions_orders_items',
            'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
            'left'
        );
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
        $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        $this->db->join(
            'tbl_products',
            'tbl_products.id = tbl_productions_orders_items.items_id ',
            'left'
        );
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
        $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
        $this->db->join($tbQC, 'tb_pod.pod_id = tbl_productions_orders_details.id', 'left');
        // $this->db->where('tbl_orders.customer_id', $id_customer);
        // $this->db->having('(total_qty-qty_qc) > 0');
        if ($id_branch != 1) {
            $this->db->where('tbl_productions_orders.location_id', $id_branch);
        }
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->where('tbl_productions_orders_details.reference_no like "%' . $term . '%"');
            $this->db->or_where('tbl_orders.reference_no like "%' . $term . '%"');
            $this->db->or_where('tbl_products.name like "%' . $term . '%"');
            $this->db->or_where('tbl_products.code like "%' . $term . '%"');
            $this->db->or_where('tbl_orders.note like "%' . $term . '%"');
            $this->db->group_end();
        }
        $this->db->order_by('tbl_products.name', 'DESC');
        $this->db->group_by('tbl_productions_orders_details.id');
        $product = $this->db->get()->result_array();
        if (!empty($product)) {
            foreach ($product as $key => $value) {
                $items = [];
                $item_id = explode('__', $value['id']);
                $item_id = $item_id[1];
                $type_item = $item_id[0];
                $info = $this->products_model->rowProduct($item_id);
                $this->db->select('*');
                $this->db->from('tbl_product_stages');
                $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                $this->db->where('tbl_product_stages.product_id', $item_id);
                $stages =  $this->db->get()->row_array();
                //                    if(!empty($stages)){
                $productions_orders_item_id = $value['productions_orders_item_id'];
                $stageProduction = "(
                            SELECT active
                            FROM tbl_productions_orders_items_stages
                            WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                            LIMIT 1
                        )";
                //                        $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,'.$stageProduction.' as active');
                //                        $this->db->from('tbl_product_stages_versions');
                //                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                //                        $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                //                        $this->db->where('tbl_stages.status_qc', 1);
                //                        $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                //                        $items = $this->db->get()->result_array();

                $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
//                $this->db->where('tbl_stages.status_qc', 1);
                $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                $items = $this->db->get()->result_array();

                $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                $this->db->from('tbl_productions_orders_items_stages');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
                $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                $items_stage = $this->db->get()->result_array();
                //                    }
                $product[$key]['stages'] = $items;
                $product[$key]['stages_again'] = $items_stage;
            }
        }
        // }

        $results = [];
        if (!empty($product)) {
            $results[] = ['text' => lang('products'), 'children' => $product];
        }
        $data['results'] = $results;
        if ($id) {
            $arr = explode('__', $id);
            $type_item = $arr[0];
            $item_id = $arr[1];
            if ($type_item == "semi_products_outside" || $type_item == "semi_products") {
                $info = $this->products_model->rowProduct($item_id);
            } elseif ($type_item == "materials") {
                $info = $this->items_model->rowMaterial($item_id);
            } elseif ($type_item == "tools_supplies") {
                $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
            }
            $data['row'] = ['id' => $info['id'], 'text' => $info['code']];
        }
        echo json_encode($data);
    }

    public function searchProduction($id = false)
    {
        $data = [];
        $term = $this->input->get('term', true);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $id_branch = $this->input->get('id_branch');
        $id_customer = 0;
        if (!empty($customer_ids)) {
            $customer_id = $customer_ids;
        } else {
            $customer_id = $this->input->get('customer_id');
            if (!empty($customer_id)) {
                $customer_id = explode('__', $customer_id);
                $id_customer = $customer_id[1];
            }
        }
        $product = false;
        $tbProductionsPlanOrdersByOrders = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(CONCAT(tbl_orders.reference_no, '(', tblclients.company,')') SEPARATOR '|||') reference_no_orders
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_productions_plan_orders.productions_plan_id
            INNER JOIN tblclients ON tblclients.userid = tbl_orders.customer_id
            WHERE tbl_productions_plan_orders.object_type = 'orders'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_orders";

        $tbProductionsPlanOrdersByBusinessPlan = "(
            SELECT
                tbl_productions_plan_orders.productions_order_id as productions_order_id,
                GROUP_CONCAT(tbl_business_plan.reference_no SEPARATOR '|||') reference_no_business_plan
            FROM tbl_productions_plan_orders
            INNER JOIN tbl_business_plan ON tbl_business_plan.id = tbl_productions_plan_orders.productions_plan_id
            WHERE tbl_productions_plan_orders.object_type = 'business_plan'
            GROUP BY tbl_productions_plan_orders.productions_order_id
        ) tb_business_plan";
        $this->db->select(
            '
        tbl_productions_orders.id as id,
        tbl_productions_orders.reference_no as text,
        tb_orders.reference_no_orders as reference_no_orders,
        tb_business_plan.reference_no_business_plan as reference_no_business_plan',
            false
        );

        $this->db->from('tbl_productions_orders');
        $this->db->join("$tbProductionsPlanOrdersByOrders", "tb_orders.productions_order_id = tbl_productions_orders.id", "left");
        $this->db->join("$tbProductionsPlanOrdersByBusinessPlan", "tb_business_plan.productions_order_id = tbl_productions_orders.id", "left");
        if ($id_branch != 1) {
            $this->db->where('tbl_productions_orders.location_id', $id_branch);
        }
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->where('tbl_productions_orders.reference_no like "%' . $term . '%"');
            $this->db->or_where('tb_business_plan.reference_no_business_plan like "%' . $term . '%"');
            $this->db->or_where('tb_orders.reference_no_orders like "%' . $term . '%"');
            $this->db->group_end();
        }
        $this->db->order_by('tbl_productions_orders.id desc');
        $production = $this->db->get()->result_array();

        $results = [];
        if (!empty($production)) {
            $results[] = ['text' => lang('Lệnh sản xuất'), 'children' => $production];
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function searchProductbyProductions()
    {
        $data = [];
        $production_id = $this->input->post('production_id');

        $product = false;
        if (!empty($production_id)) {

            $qtQC = '
            COALESCE(
            (SELECT SUM(tbl_productions_orders_details.qty_qc) ),0)
            ';
            $this->db->select(
                '
            CONCAT(tbl_products.type_products, "__", tbl_products.id) as id,
            CONCAT(tbl_products.code, "(", tbl_products.name, ")") as text,
            tbl_products.name as name,
            tbl_products.code as code,
            tbl_products.unit_id as unit_id,
            tblunits.unit as unit_name,
            tbl_products.price_import as price_import,
            tbl_products.images as images,
            CONCAT(tbl_orders.reference_no) as reference_no,
            CONCAT(tbl_business_plan.reference_no) as reference_no_plan,
            tbl_productions_orders_details.reference_no as reference_no_production_detail,
            COALESCE(SUM(tbl_productions_orders_items.quantity),0) as total_qty,
            ' . $qtQC . ' as qty_qc,
            tbl_productions_orders_details.id as pod_id,
            tbl_colors.name as name_color,
            tbl_productions_orders_details.object_type as object_type,
            tbl_business_plan.id as plan_id,
            tbl_productions_orders_details.productions_orders_item_id as productions_orders_item_id,
            tbl_orders.id as idd',
                false
            );

            $this->db->from('tbl_productions_orders_details');
            $this->db->join(
                'tbl_productions_orders_items',
                'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id',
                'left'
            );
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id ', 'left');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join('tbl_business_plan', 'tbl_business_plan.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "business_plan"', 'left');
            $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
            $this->db->join(
                'tbl_products',
                'tbl_products.id = tbl_productions_orders_items.items_id ',
                'left'
            );
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
            $this->db->join('tbl_products_colors', 'tbl_products_colors.product_id = tbl_products.id', 'left');
            $this->db->join('tbl_colors', 'tbl_colors.id = tbl_products_colors.color_id', 'left');
            $this->db->where('tbl_productions_orders.id', $production_id);

            $this->db->order_by('tbl_products.name', 'DESC');
            $this->db->group_by('tbl_productions_orders_details.id');
            $product = $this->db->get()->result_array();
            if (!empty($product)) {
                foreach ($product as $key => $value) {
                    $items = [];
                    $item_id = explode('__', $value['id']);
                    $item_id = $item_id[1];
                    $type_item = $item_id[0];
                    $info = $this->products_model->rowProduct($item_id);
                    $this->db->select('*');
                    $this->db->from('tbl_product_stages');
                    $this->db->where('tbl_product_stages.versions', $info['versions_stage']);
                    $this->db->where('tbl_product_stages.product_id', $item_id);
                    $stages =  $this->db->get()->row_array();
                    //                    if(!empty($stages)){
                    $productions_orders_item_id = $value['productions_orders_item_id'];
                    $stageProduction = "(
                            SELECT active
                            FROM tbl_productions_orders_items_stages
                            WHERE tbl_productions_orders_items_stages.stage_id = tbl_stages.id AND tbl_productions_orders_items_stages.productions_orders_items_id = $productions_orders_item_id
                            LIMIT 1
                        )";
                    //                        $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,'.$stageProduction.' as active');
                    //                        $this->db->from('tbl_product_stages_versions');
                    //                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                    //                        $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                    //                        $this->db->where('tbl_stages.status_qc', 1);
                    //                        $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                    //                        $items = $this->db->get()->result_array();
                    //
                    //                        $this->db->select('tbl_product_stages_versions.*, tbl_stages.name as stage_name, tbl_stages.code as stage_code,'.$stageProduction.' as active');
                    //                        $this->db->from('tbl_product_stages_versions');
                    //                        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_product_stages_versions.stage_id', 'left');
                    //                        $this->db->where('tbl_product_stages_versions.version_id', $stages['id']);
                    //                        $this->db->order_by('tbl_product_stages_versions.number', 'ASC');
                    //                        $items_stage = $this->db->get()->result_array();

                    $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
//                    $this->db->where('tbl_stages.status_qc', 1);
                    $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                    $items = $this->db->get()->result_array();

                    $this->db->select('tbl_stages.id as stage_id, tbl_stages.name as stage_name, tbl_stages.code as stage_code,' . $stageProduction . ' as active');
                    $this->db->from('tbl_productions_orders_items_stages');
                    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
                    $this->db->where('tbl_productions_orders_items_stages.productions_orders_items_id', $productions_orders_item_id);
                    $this->db->order_by('tbl_productions_orders_items_stages.number', 'ASC');
                    $items_stage = $this->db->get()->result_array();
                    //                    }
                    $product[$key]['stages'] = $items;
                    $product[$key]['stages_again'] = $items_stage;
                }
            }
        }

        $results = [];
        if (!empty($product)) {
            $results = $product;
        }
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function calReasonQC()
    {
        $data = [];
        $cQuantity = number_unformat($this->input->post('cQuantity'));
        $cItemsId = $this->input->post('cItemsId');
        $arrItem = explode('__', $cItemsId);

        $item_id = $arrItem[0];
        $type_item = $arrItem[1];
        $check_quality_item = $this->input->post('check_quality_item');
        $data_json = $this->input->post('data_json');
        $type = $this->input->post('type');
        if (!empty($data_json)) {
            $data_json = json_decode($data_json, true);
        }

        $reasons = get_table_where('tbl_detail_errors');


        $data['data_json'] = $data_json;
        $data['item_id'] = $item_id;
        $data['quantity'] = $cQuantity;
        $data['type'] = $type;
        $data['reasons'] = $reasons;

        $this->load->view('admin/quality_control/call_reason_qc', $data);
    }

    public function handlingCalReason()
    {
        $data = [];
        $dataPost = $this->input->post();
        if (!empty($dataPost)) {
            //reason
            $arrReson = [];
            $reason_id = !empty($dataPost['reason_id']) ? $dataPost['reason_id'] : null;
            $type = !empty($dataPost['type']) ? $dataPost['type'] : null;
            $quantity_check = !empty($dataPost['quantity_check']) ? $dataPost['quantity_check'] : null;
            if (!empty($reason_id)) {
                foreach ($reason_id as $key => $value) {
                    $reason_id = $dataPost['reason_id'][$key];
                    $quantity_quote = number_unformat($dataPost['quantity_quote'][$key]);
                    $reason_name = $dataPost['reason_name'][$key];


                    $arrReson[] = [
                        'reason_id' => $reason_id,
                        'quantity_quote' => $quantity_quote,
                        'reason_name' => $reason_name,
                        'type' => $type,
                        'quantity_check' => $quantity_check,
                    ];
                }
            }
        }

        $data['dataJSonReason'] = json_encode($arrReson, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);

        echo json_encode($data);
    }

    public function view_reason($id, $type)
    {
        $data = [];
        $check_quality_item = get_table_where(
            'tbl_check_quality_items',
            ['id' => $id],
            '',
            'row_array'
        );
        if ($type == 1) {
            $data_json = $check_quality_item['data_json_taiche'];
            $quantity = $check_quality_item['quantity_recycling'];
        } elseif ($type == 2) {
            $data_json = $check_quality_item['data_json_phe'];
            $quantity = $check_quality_item['quantity_waste'];
        }
        if (!empty($data_json)) {
            $data_json = json_decode($data_json, true);
        }

        $data['data_json'] = $data_json;
        $data['id'] = $id;
        $data['quantity'] = $quantity;
        $data['type'] = $type;
        $this->load->view('admin/quality_control/view_reason', $data);
    }

    public function update_status($value = '')
    {

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            $check_quality = get_table_where('tbl_check_quality', array('id' => $id), '', 'row');
            if ($status == 0) {
                if (!has_permission('quality_control', '', 'approve_warehouse')) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_approve_not'),
                    ));
                    die;
                }
            }
            if ($status == 1) {
                if (!has_permission('quality_control', '', 'approve')) {
                    echo json_encode(array(
                        'alert_type' => 'warning',
                        'message' => _l('ch_approve_not'),
                    ));
                    die;
                }
            }
            $checkStatus = get_table_where('tbl_check_quality', array('id' => $id), '', 'row');
            if ($status == 1) {
                if ($checkStatus->status_process < 1) {
                    echo json_encode(array(
                        'success' => false,
                        'alert_type' => 'danger',
                        'message' => _l('Giám đốc phân xưởng chưa duyệt'),
                    ));
                    die;
                }
            }

            $success = false;
            $status = $this->input->post('status');

            $check_quality = get_table_where('tbl_check_quality', array('id' => $id), '', 'row');

            $staff_id = get_staff_user_id();
            $date = date('Y-m-d H:i:s');
            $history_status = $check_quality->history_status;
            if (($status == 0)) {
                $history_status .= $staff_id . ',' . $date;
            } else {
                $history_status .= '|' . $staff_id . ',' . $date;
            }
            $history_status = trim($history_status, '|');
            $data = array(
                'history_status' => $history_status,
                'status_process' => ($status + 1),
            );
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_check_quality', $data);
        }
        if ($success) {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'success',
                'message' => _l('Duyệt đơn thành công'),
            ));
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('Không thể cập nhật dữ liệu'),
            ));
        }
        die;
    }

    public function print_check_quality($id)
    {
        if (!$this->perPrintQC) {
            accessDenied();
        }

        ob_end_clean();
        $data = [];
        $checkQuality = get_table_where('tbl_check_quality', ['id' => $id], '', 'row_array');
        $client = get_table_where('tblclients', ['userid' => $checkQuality['customer_id']], '', 'row_array');
        $items = get_table_where('tbl_check_quality_items', ['check_quality_id' => $id], '', 'result_array');
        $detail_errors = get_table_where('tbl_detail_errors', [], 'id asc', 'result_array');
        $branch = get_table_where('tblbranch', ['id' => $checkQuality['id_branch']], '', 'row_array');
        $arr_reason = [];
        foreach ($detail_errors as $kkk => $vvv) {
            $arr_reason[] = $vvv['id'];
        }

        $data['title'] = lang('print') . ' ' . lang('Phiếu QC');
        $data['type'] = 'P';
        $data['img'] = '';
        $rowTrHead = '';


        $bodyItems = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $rowTdHead = '';
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);
                if ($value['object_type'] == 'orders') {
                    $order = get_table_where('tbl_orders', ['id' => $value['order_id']], '', 'row_array');
                } elseif ($value['object_type'] == 'business_plan') {
                    $order = get_table_where('tbl_business_plan', ['id' => $value['plan_id']], '', 'row_array');
                }

                $tdNumber = '<td class="text-center" >' . (++$key) . '</td>';
                $tdOrder = '<td >' . $order['reference_no'] . '</td>';
                $tdName = '<td >' . $info['name'] . '</td>';
                $tdQc = '<td class="text-center">' . formatNumber($value['quantity_qc']) . '</td>';
                $tdPhe = '<td class="text-center">' . formatNumber($value['quantity_recycling']) . '</td>';
                $tdChe = '<td class="text-center">' . formatNumber($value['quantity_waste']) . '</td>';
                $tdsuccess = '<td class="text-center" >' . formatNumber($value['quantity_success']) . '</td>';
                $tdKdat = '<td class="text-center">' . formatNumber(($value['quantity_recycling'] + $value['quantity_waste']) * 100 / $value['quantity_qc']) . '</td>';
                $tdDat = '<td class="text-center" >' . formatNumber(($value['quantity_success']) * 100 / $value['quantity_qc']) . '</td>';

                $reasons = get_table_where(
                    'tbl_check_quality_items_error',
                    ['id_check_quality_item' => $value['id']],
                    'id_error asc',
                    'result_array'
                );
                $index_parent = 0;
                $ref = '';
                $qty = 0;
                $dataReason = [];
                foreach ($reasons as $k => $v) {
                    if ($v['id_check_quality_item'] . '_' . $v['id_error'] != $ref) {
                        $dataReason[$index_parent]['id_check_quality_item'] = $v['id_check_quality_item'];
                        $dataReason[$index_parent]['id_error'] = $v['id_error'];

                        $parent_current = $index_parent;
                        $ref = $v['id_check_quality_item'] . '_' . $v['id_error'];
                        $index_parent++;
                        $qty = 0;
                    }
                    $qty += $v['quantity'];
                    $dataReason[$parent_current]['quantity'] = $qty;
                }
                if (!empty($dataReason)) {
                    foreach ($dataReason as $kk => $vv) {
                        if (in_array($vv['id_error'], $arr_reason)) {
                            $rowTdHead .= '<td style="text-align: center">' . formatNumber($vv['quantity']) . '</td>';
                        }
                    }
                } else {
                    foreach ($detail_errors as $kk => $vv) {
                        $rowTdHead .= '<td style="text-align: center">0</td>';
                    }
                }

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdOrder . '
                    ' . $tdName . '
                    ' . $tdQc . '
                    ' . $tdPhe . '
                    ' . $tdChe . '
                    ' . $tdsuccess . '
                    ' . $tdKdat . '
                    ' . $tdDat . '
                    ' . $rowTdHead . '
                </tr>';
            }
        }

        $day = date_format(date_create($checkQuality['date']), 'd');
        $month = date_format(date_create($checkQuality['date']), 'm');
        $year = date_format(date_create($checkQuality['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        foreach ($detail_errors as $key => $value) {
            $rowTrHead .= '<td style="text-align: center"> ' . $value['name'] . ' </td>';
        }

        $imgnl = '';
        $staff = get_table_where('tblstaff', array('staffid' => $checkQuality['created_by']), '', 'row');
        $imgnl = (!empty($staff->signature) ? '<img  width="100" height="50" src="' . staff_signature_pdf(
            $checkQuality['created_by'],
            array('img', 'img-responsive', ''),
            'thumb'
        ) . '">' : '<br><br><br><br>');


        $name_room = '';
        $imgnl_room = '';

        $name_gdx = '';
        $img_gdx = '';
        if (!empty($checkQuality['history_status'])) {
            $history = explode('|', $checkQuality['history_status']);
            foreach ($history as $key => $value) {
                $arr = explode(',', $value);
                if ($key == 0) {
                    $arr = explode(',', $value);
                    $staff_gdx = get_table_where('tblstaff', array('staffid' => $arr[0]), '', 'row');
                    $img_gdx = (!empty($staff_gdx->signature) ? '<img  width="100" height="50" src="' . staff_signature_pdf(
                        $arr[0],
                        array('img', 'img-responsive', ''),
                        'thumb'
                    ) . '">' : '<br><br><br><br>');
                    $name_gdx = '<span>' . $staff_gdx->firstname . ' ' . $staff_gdx->lastname . '</span>';
                } elseif ($key == 1) {
                    $arr = explode(',', $value);
                    $staff_room = get_table_where('tblstaff', array('staffid' => $arr[0]), '', 'row');
                    $imgnl_room = (!empty($staff_room->signature) ? '<img  width="100" height="50" src="' . staff_signature_pdf(
                        $arr[0],
                        array('img', 'img-responsive', ''),
                        'thumb'
                    ) . '">' : '<br><br><br><br>');
                    $name_room = '<span>' . $staff_room->firstname . ' ' . $staff_room->lastname . '</span>';
                }
            }
        }

        echo '
            <br>
            <h1 class="text-center uppercase">' . lang('Biên bản kiểm tra thành phẩm') . '</h1>
            <span class="text-right">
                <span class="italic">' . _l('Mã phiếu QC') . ': ' . $checkQuality['reference_no'] . '</span><br>
                <span class="italic">' . _l('date') . ': ' . _d($checkQuality['date'], true) . '</span>
            </span>
            <p>
                <span>' . _l('Phân xưởng') . ': <span class="bold">' . $branch['name'] . '</span></span><br>
            </p>
            <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" class="bold text-center" >' . _l('tnh_numbers') . '</th>
                        <th rowspan="2" class="bold text-center">' . _l('Đơn hàng/Kế hoạch BTP') . '</th>
                        <th rowspan="2" class="bold text-center">' . _l('Thành phẩm') . '</th>
                        <th rowspan="2" class="bold text-center">' . _l('SL K.Tra') . '</th>
                        <th rowspan="2" class="bold text-center" >' . _l('SL tái chế') . '</th>
                        <th rowspan="2" class="bold text-center">' . _l('SL phế') . '</th>
                        <th rowspan="2" class="bold text-center" >' . _l('SL đạt') . '</th>
                        <th rowspan="2" class="bold text-center" >' . _l('Tỉ lệ % không đạt') . '</th>
                        <th rowspan="2" class="bold text-center" >' . _l('Tỉ lệ % đạt') . '</th>
                        <th colspan="' . count($detail_errors) . '" class="bold text-center">' . _l('Nguyên nhân') . '</th>
                    </tr>
                    <tr>
                        ' . $rowTrHead . '
                    </tr>
                </thead>
                <tbody>
                    ' . $bodyItems . '
                </tbody>
            </table>
            <p class="text-right"><span>Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span></p>
            <table style="width: 100%">
                <tr>
                    <td class="text-center">
                        <span class="bold">' . _l('Phòng QC') . '</span><br>
                         ' . $imgnl_room . ' ' . $name_room . '
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('Ban QĐ Phân Xưởng') . '</span><br>
                        ' . $img_gdx . ' ' . $name_gdx . '
                    </td>
                    <td class="text-center">
                        <span class="bold">' . _l('Lập biểu') . '</span><br>
                        ' . $imgnl . '<span>' . $staff->firstname . ' ' . $staff->lastname . '</span>
                    </td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();

        $data['content'] = $content;
        $data['pageCustome'] = 'check_quality';
        $pdf = @print_pdf_dt_L($data);
        $type = 'I';
        $pdf->Output(slug_it('123') . '.pdf', $type);
    }

    public function searchOrders($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $this->db->select(
            'tbl_orders.id as id,CONCAT(tbl_orders.reference_no," - ",tblclients.company) as text',
            false
        );
        $this->db->from('tbl_orders');
        $this->db->join('tblclients', 'tblclients.userid = tbl_orders.customer_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_orders.reference_no', $term);
            $this->db->or_like('tbl_orders.reference_no', $term);
            $this->db->or_like('tblclients.company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $orders = $this->db->get()->result_array();
        $data['results'] = $orders;
        if ($id) {
            $order = $this->orders_model->rowOrderById($id);
            $data['row'] = ['id' => $order['id'], 'text' => $order['reference_no']];
        }
        echo json_encode($data);
    }

    public function view_reason_production($pod_id)
    {
        $data = [];

        $this->db->select('tbl_check_quality_items.id,tbl_check_quality_items_error.type,tbl_check_quality_items_error.id_error,tbl_check_quality_items_error.quantity');
        $this->db->from('tbl_check_quality_items');
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->where('tbl_check_quality_items.pod_id', $pod_id);
        $this->db->where('tbl_check_quality_items_error.type', 1);
        $this->db->order_by('tbl_check_quality_items_error.id_error asc');
        $check_quality_item_phes = $this->db->get()->result_array();

        $this->db->select('tbl_check_quality_items.id,tbl_check_quality_items_error.type,tbl_check_quality_items_error.id_error,tbl_check_quality_items_error.quantity');
        $this->db->from('tbl_check_quality_items');
        $this->db->join(
            'tbl_check_quality_items_error',
            'tbl_check_quality_items_error.id_check_quality_item = tbl_check_quality_items.id',
            'left'
        );
        $this->db->where('tbl_check_quality_items.pod_id', $pod_id);
        $this->db->where('tbl_check_quality_items_error.type', 2);
        $this->db->order_by('tbl_check_quality_items_error.id_error asc');
        $check_quality_item_ches = $this->db->get()->result_array();

        $data['check_quality_item_phes'] = $check_quality_item_phes;
        $data['check_quality_item_ches'] = $check_quality_item_ches;

        $this->load->view('admin/quality_control/view_reason_production', $data);
    }

    public function getQuantityQc()
    {
        $data = [];
        $id_stage = $this->input->get('id_stage');
        $pod_id = $this->input->get('pod_id');
        $check_quality_item_id = $this->input->get('check_quality_item_id');
        if (!empty($id_stage) && !empty($pod_id)) {
            if ($check_quality_item_id != 0) {
                $qtQC = '
                COALESCE(
                (SELECT SUM(tbl_check_quality_items.quantity_qc) 
                FROM tbl_check_quality_items
                WHERE tbl_check_quality_items.id_stage = ' . $id_stage . ' AND tbl_check_quality_items.pod_id = ' . $pod_id . ' AND tbl_check_quality_items.id != ' . $check_quality_item_id . ' ),0)
            ';
            } else {
                $qtQC = '
                    COALESCE(
                    (SELECT SUM(tbl_check_quality_items.quantity_qc) 
                    FROM tbl_check_quality_items
                    WHERE tbl_check_quality_items.id_stage = ' . $id_stage . ' AND tbl_check_quality_items.pod_id = ' . $pod_id . ' ),0)
                ';
            }
            $this->db->select('tbl_productions_orders_items.quantity,' . $qtQC . ' as qty_qc');
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'left');
            $this->db->where('tbl_productions_orders_details.id', $pod_id);
            $result = $this->db->get()->row_array();
            if (!empty($result)) {
                $data['result'] = $result;
            }
        }
        echo json_encode($data);
    }

    public function getStageAgain()
    {
        $data = [];
        $id_stage = $this->input->post('id_stage');
        $stages = [];
        if (!empty($id_stage)) {
            $stage = get_table_where('tbl_stages', ['id' => $id_stage], '', 'row_array');
            $this->db->select('tbl_stages.*');
            $this->db->where('tbl_stages.id', $stage['stage_again']);
            $stages = $this->db->get('tbl_stages')->result_array();
        }
        if (!empty($stages)) {
            $data['stages'] = $stages;
            $data['selected'] = $stage['stage_again'];
        } else {
            $data['stages'] = [];
        }
        echo json_encode($data);
    }

    // yct start
    // Danh mục lỗi
    public function modal_excel_import_category_errors()
    {
        $data['title'] = _l('tnh_import_excel') . ' ' . _l('category_errors');
        $this->load->view('admin/quality_control/excel_import_category_errors', $data);
    }

    public function excel_import_category_errors()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('D');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            foreach ($arraydata as $key => $value) {
                // 0: code
                // 1: name
                // 2: note
                // 3: parent_code
                $code = $value[0];
                $name = $value[1];
                $note = $value[2];
                $parent_code = $value[3];

                if (empty($code) || empty($name)) {
                    continue;
                }

                if ($this->quality_control_model->isExist_CategoryError_byCode($code)) {
                    continue;
                }
                if (!empty($parent_code)) {
                    $parent = $this->quality_control_model->getCategoryError_byCode($parent_code);
                    if (empty($parent)) {
                        continue;
                    } else {
                        $parent_id = $parent->id;
                    }
                } else {
                    $parent_id = '';
                }
                
                $dataInsert = [
                    'code' => $code,
                    'name' => $name,
                    'note' => $note,
                    'parent_id' => $parent_id,
                ];

                $rs = $this->quality_control_model->insertCategoryErrors($dataInsert);
                if ($rs) {
                    $count++;
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]
        );
        die();
    }

    // Chi tiết lỗi 
    public function modal_excel_import_detail_errors()
    {
        $data['title'] = _l('tnh_import_excel') . ' ' . _l('tnh_detail_errors');
        $this->load->view('admin/quality_control/excel_import_detail_errors', $data);
    }

    public function excel_import_detail_errors()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $data = [];
        if (!empty($_FILES['file'])) {
            $fullfile = $_FILES['file']['tmp_name'];
            $nameFile = $_FILES['file']['name'];
            $extension = strtoupper(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }
            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            // $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('D');
            $arraydata          = array();

            $fields = $this->input->post('fields');
            for ($row = 2; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value      = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 2][$col] = $value;
                }
            }

            foreach ($arraydata as $key => $value) {
                // 0: category_error_code
                // 1: code
                // 2: name
                // 3: note
                $category_error_code = $value[0];
                $code = $value[1];
                $name = $value[2];
                $note = $value[3];

                if (empty($category_error_code) || empty($code) || empty($name)) {
                    continue;
                }
                if ($this->quality_control_model->isExist_DetailError_byCode($code)) {
                    continue;
                }

                $category_error = $this->quality_control_model->getCategoryError_byCode($category_error_code);
                if (!empty($category_error)) {
                    $category_error_id = $category_error->id;
                } else {
                    continue;
                }
                
                $dataInsert = [
                    'category_error_id' => $category_error_id,
                    'code' => $code,
                    'name' => $name,
                    'note' => $note,
                ];

                $rs = $this->quality_control_model->insertDetailErrors($dataInsert);
                if ($rs) {
                    $count++;
                }
            }
        }
        echo json_encode(
            [
                'success' => true,
                'alert_type' => 'success',
                'message' => 'Import thành công ' . $count . ' dòng',
            ]
        );
        die();
    }
    // yct end
}
