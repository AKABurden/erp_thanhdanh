<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_client_complaints extends AdminController
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

        $this->preViewRequestClientComplaints = true;
        $this->preViewOwnRequestClientComplaints = true;
        $this->preAddRequestClientComplaints = true;
        $this->preEditRequestClientComplaints = true;
        $this->preApproveRequestClientComplaints = true;
        $this->preDeleteRequestClientComplaints = true;
    }

    public function index()
    {
        if (!$this->preViewRequestClientComplaints && !$this->preViewOwnRequestClientComplaints) {
            access_denied();
        }
        $data['title'] = _l('ch_request_client_complaints');
        $this->load->view('admin/request_client_complaints/index', $data);
    }

    public function getRequestClientComplaints()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_client_complaints.id as id',
            'tbl_request_client_complaints.reference_no as reference_no',
            'tbl_request_client_complaints.date as date',
            'tbl_brand.name as name_brand',
            'tblclients.company as company',
            'CONCAT(employees.firstname," ",employees.lastname) as fullname',
            'tbl_category_complaints.name as category_complaints',
            'tbl_request_client_complaints.detail_complaints as detail_complaints',
            'tbl_request_client_complaints.staff_tn as staff_tn',
            'tbl_request_client_complaints.timequota as timequota',
            'tbl_request_client_complaints.causal as causal',
            'tbl_request_client_complaints.processing_procedures as processing_procedures',
            'tbl_result.name as name_result',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_request_client_complaints.id AND tblproduction_report.object_type = "request_client_complaints"
            ) as name_report',
            'tbl_request_client_complaints.prevention_procedures as prevention_procedures',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_client_complaints';
        $where = [];
        $filter = [];

        $join = [
            'left JOIN tbl_result ON tbl_result.id = tbl_request_client_complaints.result_id',
            'INNER JOIN tblclients ON tblclients.userid = tbl_request_client_complaints.client_id',
            'INNER JOIN tblstaff employees ON employees.staffid = tbl_request_client_complaints.employees',
            'left JOIN tbl_brand ON tbl_brand.id = tbl_request_client_complaints.brand_id',
            'left JOIN tbl_category_complaints ON tbl_category_complaints.id = tbl_request_client_complaints.category_complaints',
        ];

        if (!$this->preViewRequestClientComplaints) {
            array_push($where, 'AND tbl_request_client_complaints.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_client_complaints.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_client_complaints.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_client_complaints/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['name_brand']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['company']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['fullname']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['category_complaints']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_complaints']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['staff_tn']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['timequota']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['causal']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['processing_procedures'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_result'] . '</div>';
            $arrReport = $aRow['name_report'];
            $htmlReport = '';
            if (!empty($arrReport)) {
                $arrReport = explode('||', $arrReport);
                if (!empty($arrReport)) {
                    foreach ($arrReport as $kk => $vv) {
                        $vv = explode('__', $vv);
                        $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
                    }
                }
            }

            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=request_client_complaints') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';
            $row[] = '<div class="text-left">' . $aRow['prevention_procedures'] . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_client_complaints/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestClientComplaints ? '<a class="tnh-modal" href="' . base_url('admin/request_client_complaints/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestClientComplaints ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_client_complaints/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
        $dtData = [];
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_client_complaints.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('employees', lang("Nhân viên đề xuất"), 'required');
                $this->form_validation->set_rules('category_complaints', lang("Nhóm khiếu nạii"), 'required');
                $this->form_validation->set_rules('client_id', lang("Khách hàng"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_client_complaints');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $brand_id = $this->input->post('brand_id');
                    $client_id = ($this->input->post('client_id'));
                    $employees = $this->input->post('employees');
                    $category_complaints = $this->input->post('category_complaints');
                    $detail_complaints = ($this->input->post('detail_complaints'));
                    $staff_tn = ($this->input->post('staff_tn'));
                    $timequota = ($this->input->post('timequota'));
                    $causal = ($this->input->post('causal'));
                    $processing_procedures = ($this->input->post('processing_procedures'));
                    $prevention_procedures = ($this->input->post('prevention_procedures'));
                    $result_id = ($this->input->post('result_id'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'brand_id' => $brand_id,
                        'category_complaints' => $category_complaints,
                        'employees' => $employees,
                        'detail_complaints' => $detail_complaints,
                        'staff_tn' => $staff_tn,
                        'client_id' => $client_id,
                        'timequota' => $timequota,
                        'causal' => $causal,
                        'processing_procedures' => $processing_procedures,
                        'prevention_procedures' => $prevention_procedures,
                        'result_id' => $result_id,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_request_client_complaints', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_client_complaints') == $reference_no) {
                            updateReference('request_client_complaints');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_client_complaints',
                            'table_obj' => 'tbl_request_client_complaints',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu xử lý khiếu nại KH') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_request_client_complaints.*');
                $this->db->from('tbl_request_client_complaints');
                $this->db->where('tbl_request_client_complaints.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_client_complaints.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('employees', lang("Nhân viên đề xuất"), 'required');
                $this->form_validation->set_rules('category_complaints', lang("Nhóm khiếu nạii"), 'required');
                $this->form_validation->set_rules('client_id', lang("Khách hàng"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $client_id = ($this->input->post('client_id'));
                    $employees = $this->input->post('employees');
                    $brand_id = $this->input->post('brand_id');
                    $category_complaints = $this->input->post('category_complaints');
                    $detail_complaints = ($this->input->post('detail_complaints'));
                    $staff_tn = ($this->input->post('staff_tn'));
                    $timequota = ($this->input->post('timequota'));
                    $causal = ($this->input->post('causal'));
                    $processing_procedures = ($this->input->post('processing_procedures'));
                    $prevention_procedures = ($this->input->post('prevention_procedures'));
                    $result_id = ($this->input->post('result_id'));
                    $fields = [
                        'date' => $date,
                        'brand_id' => $brand_id,
                        'category_complaints' => $category_complaints,
                        'employees' => $employees,
                        'detail_complaints' => $detail_complaints,
                        'staff_tn' => $staff_tn,
                        'client_id' => $client_id,
                        'timequota' => $timequota,
                        'causal' => $causal,
                        'processing_procedures' => $processing_procedures,
                        'prevention_procedures' => $prevention_procedures,
                        'result_id' => $result_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_client_complaints', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'request_client_complaints',
                            'table_obj' => 'tbl_request_client_complaints',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu xử lý khiếu nại KH') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddRequestClientComplaints) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_request_client_complaints');
            } else {
                if (!$this->preEditRequestClientComplaints) {
                    accessDenied(true);
                }
                $this->db->select('tbl_request_client_complaints.*');
                $this->db->from('tbl_request_client_complaints');
                $this->db->where('tbl_request_client_complaints.id', $id);
                $dtData = $this->db->get()->row_array();
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_client_complaints');
                $this->db->where('tblproduction_report.object_id', $id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    refererModel(lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !'));
                }
                $data['title'] = lang('ch_edit_request_client_complaints');
            }
        }
        $data['dtData'] = $dtData;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_client_complaints');
        $data['dtCategoryComplaints'] = get_table_where('tbl_category_complaints');
        $data['brand'] = get_table_where('tbl_brand');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/request_client_complaints/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_request_client_complaints');

        $this->db->select('
            tbl_request_client_complaints.*,
            tbl_request_client_complaints.id as id,
            tbl_request_client_complaints.reference_no as reference_no,
            tbl_request_client_complaints.date as date,
            tbl_brand.name as name_brand,
            tblclients.company as company,
            CONCAT(employees.firstname," ",employees.lastname) as fullname,
            tbl_category_complaints.name as category_complaints,
            tbl_request_client_complaints.detail_complaints as detail_complaints,
            tbl_request_client_complaints.staff_tn as staff_tn,
            tbl_request_client_complaints.timequota as timequota,
            tbl_request_client_complaints.causal as causal,
            tbl_request_client_complaints.processing_procedures as processing_procedures,
            tbl_result.name as name_result,
            tbl_request_client_complaints.prevention_procedures as prevention_procedures,
        ');
        $this->db->from('tbl_request_client_complaints');
        $this->db->join('tblclients', 'tblclients.userid = tbl_request_client_complaints.client_id', 'inner');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_request_client_complaints.result_id', 'left');
        $this->db->join('tblstaff employees', 'employees.staffid = tbl_request_client_complaints.employees', 'left');
        $this->db->join('tbl_brand', 'tbl_brand.id = tbl_request_client_complaints.brand_id', 'left');
        $this->db->join('tbl_category_complaints', 'tbl_category_complaints.id = tbl_request_client_complaints.category_complaints', 'left');
        $this->db->where('tbl_request_client_complaints.id', $id);
        $dtData = $this->db->get()->row_array();


        $data['dtData'] = $dtData;
        $this->load->view('admin/request_client_complaints/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveRequestClientComplaints) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_request_client_complaints.*');
        $this->db->from('tbl_request_client_complaints');
        $this->db->where('tbl_request_client_complaints.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_client_complaints');
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
            $up = $this->db->update('tbl_request_client_complaints', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'request_client_complaints',
                    'table_obj' => 'tbl_request_client_complaints',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu xử lý khiếu nại KH') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteRequestClientComplaints) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_client_complaints.*');
        $this->db->from('tbl_request_client_complaints');
        $this->db->where('tbl_request_client_complaints.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.object_type', 'request_client_complaints');
        $this->db->where('tblproduction_report.object_id', $id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_client_complaints');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'request_client_complaints',
                'table_obj' => 'tbl_request_client_complaints',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu xử lý khiếu nại KH') . ' [' . $dtData['reference_no'] . ']',
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

    public function exportExcel()
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
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_xu_ly_khieu_nai_kh.xlsx';
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
            $this->db->select('
                tbl_request_client_complaints.*,
                tbl_request_client_complaints.id as id,
                tbl_request_client_complaints.reference_no as reference_no,
                tbl_request_client_complaints.date as date,
                tbl_brand.name as name_brand,
                tblclients.company as company,
                CONCAT(employees.firstname," ",employees.lastname) as fullname,
                tbl_category_complaints.name as category_complaints,
                tbl_request_client_complaints.detail_complaints as detail_complaints,
                tbl_request_client_complaints.staff_tn as staff_tn,
                tbl_request_client_complaints.timequota as timequota,
                tbl_request_client_complaints.causal as causal,
                tbl_request_client_complaints.processing_procedures as processing_procedures,
                tbl_result.name as name_result,
                tbl_request_client_complaints.prevention_procedures as prevention_procedures,
                (SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
                    FROM tblproduction_report
                    WHERE tblproduction_report.object_id = tbl_request_client_complaints.id AND tblproduction_report.object_type = "request_client_complaints"
                ) as name_report
            ');
            $this->db->from('tbl_request_client_complaints');
            $this->db->join('tblclients', 'tblclients.userid = tbl_request_client_complaints.client_id', 'inner');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_request_client_complaints.result_id', 'left');
            $this->db->join('tblstaff employees', 'employees.staffid = tbl_request_client_complaints.employees', 'left');
            $this->db->join('tbl_brand', 'tbl_brand.id = tbl_request_client_complaints.brand_id', 'left');
            $this->db->join('tbl_category_complaints', 'tbl_category_complaints.id = tbl_request_client_complaints.category_complaints', 'left');
            if (!$this->preViewRequestClientComplaints) {
                $this->db->where('(tbl_request_client_complaints.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_client_complaints.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_client_complaints.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_client_complaints.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['name_brand']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['company']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['fullname']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['category_complaints']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['detail_complaints']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, ($value['staff_tn']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, ($value['timequota']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, ($value['causal']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['processing_procedures']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);
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
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[13] . $row, ($htmlReport), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[14] . $row, ($value['prevention_procedures']), PHPExcel_Cell_DataType::TYPE_STRING);

                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_client_complaints||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_client_complaints', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_client_complaints/';
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
                    $objDrawing1->setCoordinates($columsExcel[15] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[15] . $row, '')->getStyle($columsExcel[15] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:P' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:P' . $row)->applyFromArray([
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
            $filename = lang('Phieu_yeu_cau_xu_ly_khieu_nai_kh') . '.xls';
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
    public function searchClients($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblclients.userid as id, 
            tblclients.company as text,
            tblclients.address as address,
            tblclients.phonenumber as phonenumber,
        ', false);
        $this->db->from('tblclients');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblclients.company', $term);
            $this->db->or_like('tblclients.zcode', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tblclients', ['userid' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['userid'], 'text' => $dtMachines['company']];
        }
        echo json_encode($data);
    }
}
