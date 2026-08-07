<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_payslips extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('products_model');
        // $this->load->model('items_model');
        // $this->load->model('unit_model');
        // $this->load->model('category_model');
        // $this->load->model('manufactures_model');
        // $this->load->model('purchases_model');
        // $this->load->model('business_plan_model');
        // $this->load->model('orders_model');
        // $this->load->model('departments_model');
        // $this->load->model('stock_model');
        // $this->load->model('tools_supplies_model');
        $this->load->model('site_model');
        $this->load->model('suggest_payslips_model');

        $this->preViewSuggestPayslips = has_permission('suggest_payslips', '', 'view');
        $this->preViewOwnSuggestPayslips = has_permission('suggest_payslips', '', 'view_own');
        $this->preAddSuggestPayslips = has_permission('suggest_payslips', '', 'create');
        $this->preEditSuggestPayslips = has_permission('suggest_payslips', '', 'edit');
        $this->preApproveSuggestPayslips = has_permission('suggest_payslips', '', 'approve');
        $this->preDeleteSuggestPayslips = has_permission('suggest_payslips', '', 'delete');
    }

    public function index()
    {
        if (!$this->preViewSuggestPayslips && !$this->preViewOwnSuggestPayslips) {
            access_denied();
        }

        $option_suggest_payslips = $this->suggest_payslips_model->getOptionSuggestPayslips();
        $data['title'] = _l('ch_suggest_payslips');
        $data['option_suggest_payslips'] = $option_suggest_payslips;
        $this->load->view('admin/suggest_payslips/index', $data);
    }

    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_payslips.*');
        $this->db->from('tbl_suggest_payslips');
        $this->db->where('tbl_suggest_payslips.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_payslips.reference_no]');
            } else {
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_payslips.reference_no]');
                }
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_id', lang("Người lập phiếu"), 'required');
            $this->form_validation->set_rules('suppliers_id', lang("Nhà cung cấp"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_payslips');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $suppliers_id = $this->input->post('suppliers_id');
                    $counter = $this->input->post('counter');
                    $items = [];
                    $totalmain = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $category_payslip = $this->input->post('category_payslip')[$value];
                            if (empty($category_payslip)) {
                                continue;
                            }
                            $dtCategoryPayslip = explode('__', $category_payslip);
                            $category_payslip = $dtCategoryPayslip[0];
                            $id_cost = $dtCategoryPayslip[1];
                            $dtCategoryPaySlip = get_table_where('tbl_category_payslip', ['id' => $category_payslip], '', 'row_array');
                            if (empty($dtCategoryPaySlip)) {
                                continue;
                            }
                            $unit_id = $this->input->post('unit_id')[$value];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $tax_id = !empty($this->input->post('tax_id')[$value]) ? $this->input->post('tax_id')[$value] : 0;
                            $info_tax = $this->site_model->rowTax($tax_id);
                            $tax_rate_item = 0;
                            if (!empty($info_tax)) {
                                $tax_rate_item = $info_tax['taxrate'];
                            }
                            $amount = $quantity * $price;
                            $total = $amount + ($amount * ($tax_rate_item / 100));
                            $totalmain += $total;
                            $items[] = [
                                'category_payslip' => $category_payslip,
                                'cost_id' => $id_cost,
                                'note_item' => null,
                                'unit_id' => $unit_id,
                                'quantity' => $quantity,
                                'price' => $price,
                                'amount' => $amount,
                                'tax_id' => $tax_id,
                                'taxrate' => $tax_rate_item,
                                'total' => $total,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có chi tiết để thêm');
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'suppliers_id' => $suppliers_id,
                        'total' => $totalmain,
                        'branch_id' => $branch_id,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->insert('tbl_suggest_payslips', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_payslips') == $reference_no) {
                            updateReference('suggest_payslips');
                        }
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                $value['suggest_payslips_id'] = $id;
                                $this->db->insert('tbl_suggest_payslips_items', $value);
                            }
                        }
                        if (!empty($_FILES['files']) && !empty($_FILES['files']['size'])) {

                            $path = FCPATH . 'uploads/suggest_payslips/';
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path . 'index.html', 'w');
                            }

                            $path = FCPATH . 'uploads/suggest_payslips/' . $id . '/';
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path . 'index.html', 'w');
                            }


                            $uploadData = [];
                            $fileCount = count($_FILES['files']['name']);
                            for ($i = 0; $i < $fileCount; $i++) {
                                $name = $_FILES['files']['name'][$i];
                                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                                $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                                $tmpFilePath = $_FILES['file']['tmp_name'];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                                    $filename = $id . '_' . time() . '_' . rand(0, 1000)  . vn_to_str(unique_filename($path, $_FILES['file']['name']));
                                    $newFilePath = $path . $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        if (file_exists($newFilePath)) {
                                            $file_name = 'uploads/suggest_payslips/' . $id . '/' . $filename;
                                            $uploadData[$i] = $file_name;
                                        }
                                    }
                                }

                                if (!empty($uploadData)) {
                                    $this->db->where('id', $id);
                                    $this->db->update('tbl_suggest_payslips', ['list_files' => json_encode($uploadData)]);
                                }
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_payslips',
                            'table_obj' => 'tbl_suggest_payslips',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu chi') . ' [' . $reference_no . ']',
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
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = $this->input->post('staff_id');
                    $suppliers_id = $this->input->post('suppliers_id');
                    $counter = $this->input->post('counter');
                    $items = [];
                    $totalmain = 0;
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $category_payslip = $this->input->post('category_payslip')[$value];
                            $note_item = $this->input->post('note_item')[$value];
                            if (empty($note_item) && empty($category_payslip)) {
                                continue;
                            }
                            $dtCategoryPayslip = explode('__', $category_payslip);
                            $category_payslip = $dtCategoryPayslip[0];
                            $id_cost = $dtCategoryPayslip[1];
                            $unit_id = $this->input->post('unit_id')[$value];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $tax_id = !empty($this->input->post('tax_id')[$value]) ? $this->input->post('tax_id')[$value] : 0;
                            $info_tax = $this->site_model->rowTax($tax_id);
                            $tax_rate_item = 0;
                            if (!empty($info_tax)) {
                                $tax_rate_item = $info_tax['taxrate'];
                            }
                            $suggest_payslips_items_id = !empty($this->input->post('suggest_payslips_items_id')[$value]) ? $this->input->post('suggest_payslips_items_id')[$value] : 0;
                            $amount = $quantity * $price;
                            $total = $amount + ($amount * ($tax_rate_item / 100));
                            $totalmain += $total;
                            $items[] = [
                                'id' => $suggest_payslips_items_id,
                                'note_item' => $note_item,
                                'category_payslip' => $category_payslip,
                                'cost_id' => $id_cost,
                                'unit_id' => $unit_id,
                                'quantity' => $quantity,
                                'price' => $price,
                                'amount' => $amount,
                                'tax_id' => $tax_id,
                                'taxrate' => $tax_rate_item,
                                'total' => $total,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Không có chi tiết để thêm');
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'date' => $date,
                        'staff_id' => $staff_id,
                        'suppliers_id' => $suppliers_id,
                        'total' => $totalmain,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_payslips', $fields);
                    if ($success) {
                        $this->db->where('tbl_suggest_payslips_items.suggest_payslips_id', $id);
                        $this->db->delete('tbl_suggest_payslips_items');
                        if (!empty($items)) {
                            foreach ($items as $key => $value) {
                                $value['suggest_payslips_id'] = $id;
                                $this->db->insert('tbl_suggest_payslips_items', $value);
                            }
                        }
                        if (!empty($_FILES['files']) && !empty($_FILES['files']['size'])) {

                            $path = FCPATH . 'uploads/suggest_payslips/';
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path . 'index.html', 'w');
                            }

                            $path = FCPATH . 'uploads/suggest_payslips/' . $id . '/';
                            if (!file_exists($path)) {
                                mkdir($path);
                                fopen($path . 'index.html', 'w');
                            }


                            if (!empty($dtData['list_files'])) {
                                $uploadData = (array)json_decode($dtData['list_files']);
                            } else {
                                $uploadData = [];
                            }
                            $fileCount = count($_FILES['files']['name']);
                            for ($i = 0; $i < $fileCount; $i++) {
                                $name = $_FILES['files']['name'][$i];
                                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                                $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                                $tmpFilePath = $_FILES['file']['tmp_name'];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                                    $filename = $id . '_' . time() . '_' . rand(0, 1000)  . vn_to_str(unique_filename($path, $_FILES['file']['name']));
                                    $newFilePath = $path . $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        if (file_exists($newFilePath)) {
                                            $file_name = 'uploads/suggest_payslips/' . $id . '/' . $filename;
                                            $uploadData[] = $file_name;
                                        }
                                    }
                                }

                                if (!empty($uploadData)) {
                                    $this->db->where('id', $id);
                                    $this->db->update('tbl_suggest_payslips', ['list_files' => json_encode($uploadData)]);
                                }
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_payslips',
                            'table_obj' => 'tbl_suggest_payslips',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu chi') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPayslips) {
                    accessDenied(true);
                }

                $data['title'] = lang('ch_add_suggest_payslips');
            } else {
                if (!$this->preEditSuggestPayslips) {
                    accessDenied(true);
                }
                if (($dtData['status'] == 1)) {
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));
                }
                $this->db->select('
                    tbl_suggest_payslips_items.*,
                    tblcosts.id as cost_id,
                   CONCAT(tbl_category_payslip.name,"(",tblcosts.name,")") as name_category_payslip
                ');
                $this->db->from('tbl_suggest_payslips_items');
                $this->db->join('tbl_category_payslip', 'tbl_category_payslip.id = tbl_suggest_payslips_items.category_payslip', 'left');
                $this->db->join('tblcosts', 'tbl_suggest_payslips_items.cost_id = tblcosts.id');
                $this->db->where('tbl_suggest_payslips_items.suggest_payslips_id', $id);
                $dtItems = $this->db->get()->result_array();
                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('ch_edit_suggest_payslips');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_payslips');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtCategoryMaintenance'] = get_table_where('tbl_category_maintenance');
        $data['dtTypeMaintenance'] = get_table_where('tbl_type_maintenance');
        $data['dtDepartment'] = get_table_where('tbldepartments');
        $data['dtunits'] = get_table_where('tblunits');
        $data['dttaxes'] = get_table_where('tbltaxes');
        $this->load->view('admin/suggest_payslips/detail', $data);
    }
    public function view_file_suggest_payslips($id = '')
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
            $suggest_payslips = $this->db->get('tbl_suggest_payslips')->row();
            if (!empty($suggest_payslips)) {
                $data['items'] = $suggest_payslips;
                $data['list_files'] = json_decode($suggest_payslips->list_files);
                $data['title'] = 'Tập tin đính kèm phiếu: ' . $suggest_payslips->reference_no;
            }
        }
        $this->load->view('admin/suggest_payslips/view_file_suggest_payslips', $data);
    }
    public function remove_items_file()
    {
        $id = $this->input->post('id');
        $link_file = $this->input->post('link_file');
        $success = false;
        if (!empty($id)) {
            $this->db->where('id', $id);
            $suggest_payslips = $this->db->get('tbl_suggest_payslips')->row();
            if (!empty($suggest_payslips)) {
                $suggest_payslips->list_files = json_decode($suggest_payslips->list_files);
                $list_files = [];
                foreach ($suggest_payslips->list_files as $key => $value) {
                    if ($value == $link_file) {
                        $linkDelete = FCPATH . $link_file;
                        unset($linkDelete);
                        $success = true;
                    } else {
                        $list_files[] = $value;
                    }
                }

                if (!empty($list_files)) {
                    $this->db->where('id', $id);
                    $this->db->update('tbl_suggest_payslips', ['list_files' => json_encode($list_files)]);
                } else {
                    $this->db->where('id', $id);
                    $this->db->update('tbl_suggest_payslips', ['list_files' => NULL]);
                }
            }
            if (!empty($success)) {
                echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa File thành công']);
                die();
            }
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa File không thành công']);
        die();
    }
    public function addFile($id)
    {
        if (!empty($_FILES['files']) && !empty($id)) {
            $this->db->where('id', $id);
            $suggest_payslips = $this->db->get('tbl_suggest_payslips')->row();

            if (!empty($_FILES['files']) && !empty($_FILES['files']['size'])) {

                $path = FCPATH . 'uploads/suggest_payslips/';
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . 'index.html', 'w');
                }

                $path = FCPATH . 'uploads/suggest_payslips/' . $id . '/';
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . 'index.html', 'w');
                }


                if (!empty($suggest_payslips->list_files)) {
                    $uploadData = (array)json_decode($suggest_payslips->list_files);
                } else {
                    $uploadData = [];
                }
                $fileCount = count($_FILES['files']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    $name = $_FILES['files']['name'][$i];
                    $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                    $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                    $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                    $tmpFilePath = $_FILES['file']['tmp_name'];
                    if (!empty($tmpFilePath) && $tmpFilePath != '') {
                        $filename = $id . '_' . time() . '_' . rand(0, 1000)  . vn_to_str(unique_filename($path, $_FILES['file']['name']));
                        $newFilePath = $path . $filename;
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                            if (file_exists($newFilePath)) {
                                $file_name = 'uploads/suggest_payslips/' . $id . '/' . $filename;
                                $uploadData[] = $file_name;
                            }
                        }
                    }

                    if (!empty($uploadData)) {
                        $this->db->where('id', $id);
                        $this->db->update('tbl_suggest_payslips', ['list_files' => json_encode($uploadData)]);
                    }
                }
            }

            if (!empty($uploadData)) {
                echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Upload file đính kèm thành công']);
                die();
            }
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Upload file đính kèm không thành công']);
        die();
    }
    function getSuggestPayslips()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $internal_proposal_search = _string($this->input->post('internal_proposal_search'));
        $status_table = $this->input->post('status_table');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_payslips.id as id',
            'tbl_suggest_payslips.reference_no as reference_no',
            'tbl_suggest_payslips.date as date',
            'tbl_suggest_payslips.staff_id as staff_id',
            'tblsuppliers.company as company',
            'tbl_suggest_payslips.total as total',
            'tbl_suggest_payslips.status as status',
            'tbl_suggest_payslips.created_by as created_by',
            '"" as internal_proposal',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_payslips';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tblsuppliers ON tblsuppliers.id = tbl_suggest_payslips.suppliers_id',
        ];

        if (!$this->preViewSuggestPayslips) {
            array_push($where, 'AND tbl_suggest_payslips.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_payslips.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_payslips.date <= '" . $end_date_search . "'");
        }

        if (!empty($status_table)) {
            if ($status_table == 1) {
                array_push($where, " AND (tbl_suggest_payslips.status = 0 OR tbl_suggest_payslips.status IS NULL)");
            } else if ($status_table == 2) {
                array_push($where, " AND tbl_suggest_payslips.status = 1 ");
            } else if ($status_table == 3) {
                array_push($where, " AND NOT EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id
                )");
                array_push($where, " AND NOT EXISTS (
                    SELECT 1
                    FROM tbl_suggest_muti_id
                    WHERE tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id
                )");
            } else if ($status_table == 4) {
                array_push($where, " AND (EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id
                ) OR (EXISTS (
                    SELECT 1
                    FROM tbl_suggest_muti_id
                    WHERE tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id
                )))");
            } else if ($status_table == 5) {
                array_push($where, " AND EXISTS (
                    SELECT 1
                    FROM tblsuggestion
                    WHERE tblsuggestion.detail_suggest_muti_id = tbl_suggest_payslips.id
                    AND (tblsuggestion.payments = 0 OR tblsuggestion.id IS NULL)
                )");
            }
        }

        if (!empty($internal_proposal_search)) {
            // array_push($where, " AND EXISTS (
            //     SELECT 1
            //     FROM tblinternal_proposal
            //     WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id AND tblinternal_proposal.code like '%$internal_proposal_search%'
            // )");
            array_push($where, " AND ((EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id AND tblinternal_proposal.code like '%$internal_proposal_search%'
                )) OR (EXISTS (
                    SELECT 1
                    FROM tbl_suggest_muti_id
                    LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbl_suggest_muti_id.id_internal_proposal
                    WHERE tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id AND tblinternal_proposal.code like '%$internal_proposal_search%'
                ))) ");
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['status_staff', 'list_files'], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];

        $arrSugPayslipsId = array_column($rResult, 'id');
        $dtInternalProposal = $this->suggest_payslips_model->getInternalProposalBySuggestPayslips($arrSugPayslipsId, 1);

        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $list_files = $aRow['list_files'];
            if (!empty($list_files)) {
                $list_files = json_decode($list_files);
            } else {
                $list_files = [];
            }
            $viewCode = '<div><a class="c_modal text-danger" href="' . admin_url('suggest_payslips/view_file_suggest_payslips/' . $aRow['id']) . '"><i class="fa fa-folder-open" aria-hidden="true"></i> ' . count($list_files) . ' Tập tin</a></div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_payslips/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a>' . $viewCode . '</div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $staff_id = staff_profile_image($aRow['staff_id'], array('staff-profile-image-small mright5'), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => get_staff_full_name($aRow['staff_id'])
            )) . get_staff_full_name($aRow['staff_id']);
            $row[] = '<div class="text-left">' . ($staff_id) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['company']) . '</div>';
            $created_by = staff_profile_image($aRow['created_by'], array('staff-profile-image-small mright5'), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => get_staff_full_name($aRow['created_by'])
            )) . get_staff_full_name($aRow['created_by']);
            $row[] = '<div class="text-left">' . $created_by . '</div>';
            $row[] = '<div class="text-right">' . formatMoney($aRow['total']) . '</div>';
            // if($aRow['status'] == NULL){
            //     $aRow['status'] = 0;
            // }
            // if ($aRow['status'] == 0) {
            //     $type = 'warning';
            //     $status = _l('dont_approve');
            // } elseif ($aRow['status'] == 1) {
            //     $type = 'info';
            //     $status = _l('ch_confirm_22');
            // }

            // $status = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['status'] . '">' . $status . '';
            // if ($this->preApproveSuggestPayslips) {
            //     if ($aRow['status'] == 1) {
            //         $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['status'] . ',' . $aRow['id'] . '); return false">
            //             <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
            //     } else {
            //         $status .= '<a href="javacript:void(0)" data-loading-text=""  onclick="var_status(' . $aRow['status'] . ',' . $aRow['id'] . '); return false">
            //             <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" ></i>';
            //     }
            // }
            // $row[] = $status;
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px">' . staff_profile_image($aRow['status_staff'], array('staff-profile-image-small mright5'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => get_staff_full_name($aRow['status_staff'])
                )) . get_staff_full_name($aRow['status_staff']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = $_data;

            $listInternalProposal = $dtInternalProposal[$aRow['id']] ?? null;
            // $strInternalProposal = $listInternalProposal ? implode('</br>', array_column($listInternalProposal, 'code')) : '';
            $strInternalProposal = '';
            if ($listInternalProposal) {
                foreach ($listInternalProposal as $k => $v) {
                    $strInternalProposal .= '<div><a class="c_modal" href="' . base_url('admin/internal_proposal/view/' . $v['id']) . '">' . $v['code'] . '</a></div>';
                }
            }
            if ($listInternalProposal == null) {
                $suggest_muti_id = get_table_where('tbl_suggest_muti_id', ['suggest_id' => $aRow['id']]);
                foreach ($suggest_muti_id as $kk => $vvs) {
                    $dtSuggest = get_table_where('tblinternal_proposal', ['id' => $vvs['id_internal_proposal']], '', 'row_array');
                    if (!empty($dtSuggest)) {
                        $strInternalProposal .= '<div><a class="c_modal" href="' . base_url('admin/internal_proposal/view/' . $dtSuggest['id']) . '">' . $dtSuggest['code'] . '</a></div>';
                    }
                }
            }
            $row[] = $strInternalProposal;
            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_payslips/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPayslips ? '<a class="tnh-modal" href="' . base_url('admin/suggest_payslips/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPayslips ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_payslips/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_suggest_payslips');

        $this->db->select('
            tbl_suggest_payslips.*,
            tblsuppliers.company as company,
        ');
        $this->db->from('tbl_suggest_payslips');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_payslips.suppliers_id', 'inner');
        $this->db->where('tbl_suggest_payslips.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('
            tbl_suggest_payslips_items.*,
            tblunits.unit as unit,
            CONCAT(tbl_category_payslip.name,"(",COALESCE(tblcosts.name,""),")") as name_category_payslip
        ');
        $this->db->from('tbl_suggest_payslips_items');
        $this->db->join('tbl_category_payslip', 'tbl_category_payslip.id = tbl_suggest_payslips_items.category_payslip', 'left');
        $this->db->join('tblcosts', 'tbl_suggest_payslips_items.cost_id = tblcosts.id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_suggest_payslips_items.unit_id', 'left');
        $this->db->where('tbl_suggest_payslips_items.suggest_payslips_id', $id);
        $dtItems = $this->db->get()->result_array();
        $data['dtData'] = $dtData;
        $data['dtItems'] = $dtItems;
        $this->load->view('admin/suggest_payslips/view', $data);
    }
    public function update_status($value = '')
    {
        if (!$this->preApproveSuggestPayslips) {
            echo json_encode(array(
                'alert_type' => 'warning',
                'message' => _l('ch_approve_not')
            ));
            die;
        }
        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');
            if ($status == 0) {
                $import = get_table_where('tbl_suggest_payslips', array('id' => $id), '', 'row');
                if ($import->status == 1) {
                    die;
                }
                $staff_id = get_staff_user_id();
                $date = date('Y-m-d H:i:s');
                $data = array(
                    'status_staff' => $staff_id,
                    'status_date' => $date,
                    'status' => ($status + 1),
                );
                $success = $this->nodel_update_status($id, $data);
            } else {
                $import = get_table_where('tbl_suggest_payslips', array('id' => $id), '', 'row');
                if ($import->status == 0) {
                    die;
                }
                // if (!empty($import->warehouseman_id)) {
                //     echo json_encode(array(
                //         'success' => true,
                //         'alert_type' => 'danger',
                //         'message' => _l('Đã duyệt kho, Không thể bỏ duyệt')
                //     ));
                //     die;
                // }
                $staff_id = get_staff_user_id();
                $date = date('Y-m-d H:i:s');
                $history_status = NULL;
                $data = array(
                    'status_staff' => NULL,
                    'status_date' => NULL,
                    'status' => ($status + -1),
                );
                $success = $this->nodel_update_status($id, $data);
            }
        }
        if ($success) {
            if ($status == 1) {
                echo json_encode(array(
                    'success' => $success,
                    'alert_type' => 'success',
                    'message' => _l('ch_successful_approval')
                ));
            } else {
                echo json_encode(array(
                    'success' => $success,
                    'alert_type' => 'success',
                    'message' => _l('Bỏ duyệt thành công')
                ));
            }
        } else {
            echo json_encode(array(
                'success' => $success,
                'alert_type' => 'danger',
                'message' => _l('ch_no_successful_approval')
            ));
        }
        die;
    }
    public function nodel_update_status($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_suggest_payslips', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    public function agree()
    {
        if (!$this->preApproveSuggestPayslips) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_payslips.*');
        $this->db->from('tbl_suggest_payslips');
        $this->db->where('tbl_suggest_payslips.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {
            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data);
                return;
            }
            $this->db->select('tblinternal_proposal.id');
            $this->db->from('tblinternal_proposal');
            $this->db->where('tblinternal_proposal.suggest_id', $suggest_id);
            $this->db->where('tblinternal_proposal.category_recommended_id', 41);
            $dtinternal_proposal = $this->db->get()->row_array();
            if (!empty($dtinternal_proposal)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã tạo đề xuất nội bộ, Không thể bỏ duyệt');
                echo responseData($data);
                return;
            }
            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'status_date' => $date_status,
                'status_staff' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_suggest_payslips', $options);
            if ($up) {

                $type_parent_obj = 'suggest_payslips';
                $content = lang('Duyệt phiếu yêu cầu chi');

                insertActivityLog([
                    'type_parent_obj' => $type_parent_obj,
                    'table_obj' => 'tbl_suggest_plan_purchase',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => $content . ' [' . $dtData['reference_no'] . ']',
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
    public function delete($id)
    {
        if (!$this->preDeleteSuggestPayslips) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_suggest_payslips.*');
        $this->db->from('tbl_suggest_payslips');
        $this->db->where('tbl_suggest_payslips.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }
        if (($dtData['status'] == 1)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã duyệt không thể xóa');
            echo json_encode($data);
            die();
        }
        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_payslips');
        if ($success) {

            $this->db->where('tbl_suggest_payslips_items.suggest_payslips_id', $id);
            $this->db->delete('tbl_suggest_payslips_items');

            insertActivityLog([
                'type_parent_obj' => 'suggest_payslips',
                'table_obj' => 'tbl_suggest_payslips',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu chi') . ' [' . $dtData['reference_no'] . ']',
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

    public function countSuggestPayslips()
    {
        $data = [];
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $internal_proposal_search = _string($this->input->post('internal_proposal_search'));
        $option_suggest_payslips = $this->suggest_payslips_model->getOptionSuggestPayslips();
        $start_date_search = $start_date_search ? to_sql_date($start_date_search) . ' 00:00:00' : null;
        $end_date_search = $end_date_search ? to_sql_date($end_date_search) . ' 23:59:59' : null;
        $staff_user_id = get_staff_user_id();

        foreach ($option_suggest_payslips as $key => $value) {

            $this->db->from('tbl_suggest_payslips');
            $where = [];
            if (!$this->preViewSuggestPayslips) {
                array_push($where, 'AND tbl_suggest_payslips.created_by =', $staff_user_id);
            }

            if (!empty($start_date_search)) {
                array_push($where, "AND tbl_suggest_payslips.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                array_push($where, "AND tbl_suggest_payslips.date <= '" . $end_date_search . "'");
            }

            $status_table = $value['id'];
            if (!empty($status_table)) {
                if ($status_table == 1) {
                    array_push($where, " AND (tbl_suggest_payslips.status = 0 OR tbl_suggest_payslips.status IS NULL)");
                } else if ($status_table == 2) {
                    array_push($where, " AND tbl_suggest_payslips.status = 1 ");
                } else if ($status_table == 3) {
                    array_push($where, " AND NOT EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id
                )");
                    array_push($where, " AND NOT EXISTS (
                    SELECT 1
                    FROM tbl_suggest_muti_id
                    WHERE tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id
                )");
                } else if ($status_table == 4) {
                    array_push($where, " AND ((EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id
                )) OR (EXISTS (
                    SELECT 1
                    FROM tbl_suggest_muti_id
                    WHERE tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id
                )))");
                }
                else if ($status_table == 5) {
                array_push($where, " AND EXISTS (
                    SELECT 1
                    FROM tblsuggestion
                    WHERE tblsuggestion.detail_suggest_muti_id = tbl_suggest_payslips.id
                    AND (tblsuggestion.payments = 0 OR tblsuggestion.id IS NULL)
                )");
            }
            }

            if (!empty($internal_proposal_search)) {
                array_push($where, " AND ((EXISTS (
                    SELECT 1
                    FROM tblinternal_proposal
                    WHERE tblinternal_proposal.category_recommended_id = " . CR_SUGGEST_PAYSLIPS_ID . " AND tblinternal_proposal.suggest_id = tbl_suggest_payslips.id AND tblinternal_proposal.code like '%$internal_proposal_search%'
                )) OR (EXISTS (
                    SELECT 1
                    FROM tbl_suggest_muti_id
                    LEFT JOIN tblinternal_proposal ON tblinternal_proposal.id = tbl_suggest_muti_id.id_internal_proposal
                    WHERE tbl_suggest_muti_id.suggest_id = tbl_suggest_payslips.id AND tblinternal_proposal.code like '%$internal_proposal_search%'
                ))) ");
            }

            if ($where) {
                $where = stringWhere($where);
                $this->db->where($where, false, false);
            }
            $count = $this->db->count_all_results();

            $option_suggest_payslips[$key]['count'] = $count;
        }

        $data['option_suggest_payslips'] = $option_suggest_payslips;
        echo responseData($data);
    }

    public function searchCategoryPayslip()
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
                CONCAT(tbl_category_payslip.id,"__",tblcosts.id) as id, 
                CONCAT(tbl_category_payslip.name,"(",tblcosts.name,")") as text
            ', false);
        $this->db->from('tbl_category_payslip');
        $this->db->join('tblcosts', 'tbl_category_payslip.id = tblcosts.object_id AND tblcosts.type_cost = 3');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_category_payslip.code', $term);
            $this->db->or_like('tbl_category_payslip.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        $data = [];
        $data['results'][] =
            [
                'text' => 'Danh mục chi',
                'children' => $results
            ];
        echo json_encode($data);
    }
}
