<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_system_control extends AdminController
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

        $this->preViewRequestSystemControl = true;
        $this->preViewOwnRequestSystemControl = true;
        $this->preAddRequestSystemControl = true;
        $this->preEditRequestSystemControl = true;
        $this->preApproveRequestSystemControl = true;
        $this->preDeleteRequestSystemControl = true;
    }

    public function index()
    {
        if (!$this->preViewRequestSystemControl && !$this->preViewOwnRequestSystemControl) {
            access_denied();
        }
        $data['title'] = _l('ch_request_system_control');
        $this->load->view('admin/request_system_control/index', $data);
    }

    public function getRequestSystemControl()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_request_system_control.id as id',
            'tbl_request_system_control.reference_no as reference_no',
            'tbl_request_system_control.date as date',
            'tbl_type_system.name as type_system',
            'tbl_request_system_control.subsystem as subsystem',
            'tbl_system.name as system',
            'tbl_category_system.name as category_system',
            'tbl_request_system_control.detail_system as detail_system',
            'tbl_request_system_control.staff_request as staff_request',
            'tbl_request_system_control.staff_tn as staff_tn',
            'tbl_request_system_control.staff_ht as staff_ht',
            'tbl_result.name as name_result',
            '(SELECT GROUP_CONCAT(CONCAT(tblproduction_report.name_report,"__",tblproduction_report.id) SEPARATOR "||")
                FROM tblproduction_report
                WHERE tblproduction_report.object_id = tbl_request_system_control.id AND tblproduction_report.object_type = "request_system_control"
            ) as name_report',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_request_system_control';
        $where = [];
        $filter = [];

        $join = [
            'left JOIN tbl_result ON tbl_result.id = tbl_request_system_control.result_id',
            'left JOIN tbl_system ON tbl_system.id = tbl_request_system_control.system',
            'left JOIN tbl_category_system ON tbl_category_system.id = tbl_request_system_control.category_system',
            'left JOIN tbl_type_system ON tbl_type_system.id = tbl_request_system_control.type_system',
        ];

        if (!$this->preViewRequestSystemControl) {
            array_push($where, 'AND tbl_request_system_control.created_by =', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_request_system_control.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_request_system_control.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_system_control/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['type_system']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['subsystem']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['system']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['category_system']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['detail_system']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_request']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_tn']) . '</div>';
            $row[] = '<div class="text-left">' . get_staff_full_name($aRow['staff_ht']) . '</div>';
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

            $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=request_system_control') . '" class="btn btn-info">Tạo phiếu báo cáo không phù hợp</a></div>
                <div style="margin-top: 5px">' . $htmlReport . '</div>
            ';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/request_system_control/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditRequestSystemControl ? '<a class="tnh-modal" href="' . base_url('admin/request_system_control/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteRequestSystemControl ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/request_system_control/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_request_system_control.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('category_system', lang("Nhóm hệ thống"), 'required');
                $this->form_validation->set_rules('type_system', lang("Loại hệ thống"), 'required');
                $this->form_validation->set_rules('system', lang("Danh mục hệ thống"), 'required');
                $this->form_validation->set_rules('staff_request', lang("Người yêu cầu"), 'required');
                $this->form_validation->set_rules('staff_tn', lang("Người tiếp nhận"), 'required');
                $this->form_validation->set_rules('staff_ht', lang("Người hoàn thành"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('request_system_control');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $type_system = $this->input->post('type_system');
                    $subsystem = ($this->input->post('subsystem'));
                    $system = $this->input->post('system');
                    $category_system = $this->input->post('category_system');
                    $detail_system = ($this->input->post('detail_system'));
                    $staff_request = ($this->input->post('staff_request'));
                    $staff_tn = ($this->input->post('staff_tn'));
                    $staff_ht = ($this->input->post('staff_ht'));
                    $result_id = ($this->input->post('result_id'));
                    $result_id = ($this->input->post('result_id'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'type_system' => $type_system,
                        'subsystem' => $subsystem,
                        'system' => $system,
                        'category_system' => $category_system,
                        'staff_tn' => $staff_tn,
                        'detail_system' => $detail_system,
                        'staff_request' => $staff_request,
                        'staff_ht' => $staff_ht,
                        'result_id' => $result_id,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_request_system_control', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('request_system_control') == $reference_no) {
                            updateReference('request_system_control');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'request_system_control',
                            'table_obj' => 'tbl_request_system_control',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu kiểm soát hệ thống') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_request_system_control.*');
                $this->db->from('tbl_request_system_control');
                $this->db->where('tbl_request_system_control.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_request_system_control.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                $this->form_validation->set_rules('category_system', lang("Nhóm hệ thống"), 'required');
                $this->form_validation->set_rules('type_system', lang("Loại hệ thống"), 'required');
                $this->form_validation->set_rules('system', lang("Danh mục hệ thống"), 'required');
                $this->form_validation->set_rules('staff_request', lang("Người yêu cầu"), 'required');
                $this->form_validation->set_rules('staff_tn', lang("Người tiếp nhận"), 'required');
                $this->form_validation->set_rules('staff_ht', lang("Người hoàn thành"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $type_system = $this->input->post('type_system');
                    $subsystem = ($this->input->post('subsystem'));
                    $system = $this->input->post('system');
                    $category_system = $this->input->post('category_system');
                    $detail_system = ($this->input->post('detail_system'));
                    $staff_request = ($this->input->post('staff_request'));
                    $staff_tn = ($this->input->post('staff_tn'));
                    $staff_ht = ($this->input->post('staff_ht'));
                    $result_id = ($this->input->post('result_id'));
                    $result_id = ($this->input->post('result_id'));
                    $fields = [
                        'date' => $date,
                        'type_system' => $type_system,
                        'subsystem' => $subsystem,
                        'system' => $system,
                        'category_system' => $category_system,
                        'staff_tn' => $staff_tn,
                        'detail_system' => $detail_system,
                        'staff_request' => $staff_request,
                        'staff_ht' => $staff_ht,
                        'result_id' => $result_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_request_system_control', $fields);
                    if ($success) {
                        insertActivityLog([
                            'type_parent_obj' => 'request_system_control',
                            'table_obj' => 'tbl_request_system_control',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu kiểmsoát hệ thống') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddRequestSystemControl) {
                    accessDenied(true);
                }
                $data['title'] = lang('ch_add_request_system_control');
            } else {
                if (!$this->preEditRequestSystemControl) {
                    accessDenied(true);
                }
                $this->db->select('tbl_request_system_control.*');
                $this->db->from('tbl_request_system_control');
                $this->db->where('tbl_request_system_control.id', $id);
                $dtData = $this->db->get()->row_array();
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_system_control');
                $this->db->where('tblproduction_report.object_id', $id);
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)) {
                    refererModel(lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !'));
                }
                $data['title'] = lang('ch_edit_request_system_control');
            }
        }
        $data['dtData'] = $dtData;
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('request_system_control');
        $data['dtTyleSystem'] = get_table_where('tbl_type_system');
        $data['dtSystem'] = get_table_where('tbl_system', array('type' => 1));
        $data['dtCateforySystem'] = get_table_where('tbl_category_system');
        $data['brand'] = get_table_where('tbl_brand');
        $data['dtResult'] = get_table_where('tbl_result');
        $this->load->view('admin/request_system_control/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('ch_view_request_system_control');

        $this->db->select('
            tbl_request_system_control.*,
            tbl_request_system_control.reference_no as reference_no,
            tbl_request_system_control.date as date,
            tbl_type_system.name as type_system,
            tbl_request_system_control.subsystem as subsystem,
            tbl_system.name as system,
            tbl_category_system.name as category_system,
            tbl_request_system_control.detail_system as detail_system,
            tbl_request_system_control.staff_request as staff_request,
            tbl_request_system_control.staff_tn as staff_tn,
            tbl_request_system_control.staff_ht as staff_ht,
            tbl_result.name as name_result
        ');
        $this->db->from('tbl_request_system_control');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_request_system_control.result_id', 'left');
        $this->db->join('tbl_system', 'tbl_system.id = tbl_request_system_control.system', 'left');
        $this->db->join('tbl_category_system', 'tbl_category_system.id = tbl_request_system_control.category_system', 'left');
        $this->db->join('tbl_type_system', 'tbl_type_system.id = tbl_request_system_control.type_system', 'left');
        $this->db->where('tbl_request_system_control.id', $id);
        $dtData = $this->db->get()->row_array();


        $data['dtData'] = $dtData;
        $this->load->view('admin/request_system_control/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveRequestSystemControl) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_request_system_control.*');
        $this->db->from('tbl_request_system_control');
        $this->db->where('tbl_request_system_control.id', $suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if ($status == 0) {
                $this->db->from('tblproduction_report');
                $this->db->where('tblproduction_report.object_type', 'request_system_control');
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
            $up = $this->db->update('tbl_request_system_control', $options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'request_system_control',
                    'table_obj' => 'tbl_request_system_control',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu kisoát hệ thống') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteRequestSystemControl) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_request_system_control.*');
        $this->db->from('tbl_request_system_control');
        $this->db->where('tbl_request_system_control.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->from('tblproduction_report');
        $this->db->where('tblproduction_report.object_type', 'request_system_control');
        $this->db->where('tblproduction_report.object_id', $id);
        $checkExists = $this->db->count_all_results();
        if (!empty($checkExists)) {
            $data['result'] = 0;
            $data['message'] = lang('Đã tạo phiếu báo cáo không phù hợp liên quan vui lòng xóa trước! !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_request_system_control');
        if ($success) {

            insertActivityLog([
                'type_parent_obj' => 'request_system_control',
                'table_obj' => 'tbl_request_system_control',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu kiểmsoát hệ thống') . ' [' . $dtData['reference_no'] . ']',
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
            $inputFileName = 'uploads/import_ch/Phieu_yeu_cau_kiem_soat_he_thong.xlsx';
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
                tbl_request_system_control.*,
                tbl_request_system_control.reference_no as reference_no,
                tbl_request_system_control.date as date,
                tbl_type_system.name as type_system,
                tbl_request_system_control.subsystem as subsystem,
                tbl_system.name as system,
                tbl_category_system.name as category_system,
                tbl_request_system_control.detail_system as detail_system,
                tbl_request_system_control.staff_request as staff_request,
                tbl_request_system_control.staff_tn as staff_tn,
                tbl_request_system_control.staff_ht as staff_ht,
                tbl_result.name as name_result
            ');
            $this->db->from('tbl_request_system_control');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_request_system_control.result_id', 'left');
            $this->db->join('tbl_system', 'tbl_system.id = tbl_request_system_control.system', 'left');
            $this->db->join('tbl_category_system', 'tbl_category_system.id = tbl_request_system_control.category_system', 'left');
            $this->db->join('tbl_type_system', 'tbl_type_system.id = tbl_request_system_control.type_system', 'left');
            if (!$this->preViewRequestSystemControl) {
                $this->db->where('(tbl_request_system_control.created_by = ' . $staff_id . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_request_system_control.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_request_system_control.date <= '" . $end_date_search . "'");
            }
            $this->db->order_by('tbl_request_system_control.id asc');
            $items = $this->db->get()->result_array();
            $dem = 0;
            $this->load->library('ciqrcode');

            foreach ($items as $key => $value) {
                $row++;
                $dem++;
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[0] . $row, $dem);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[1] . $row, $value['reference_no'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[2] . $row, _d($value['date']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[3] . $row, ($value['type_system']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[4] . $row, ($value['subsystem']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[5] . $row, ($value['system']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[6] . $row, ($value['category_system']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[7] . $row, ($value['detail_system']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[8] . $row, get_staff_full_name($value['staff_request']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[9] . $row, get_staff_full_name($value['staff_tn']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[10] . $row, get_staff_full_name($value['staff_ht']), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[11] . $row, ($value['name_result']), PHPExcel_Cell_DataType::TYPE_STRING);
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
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($columsExcel[12] . $row, ($htmlReport), PHPExcel_Cell_DataType::TYPE_STRING);

                if (!empty($value['barcode'])) {
                    $code = $value['barcode'];
                } else {
                    $code = 'request_system_control||' . $value['id'];
                    $this->db->where('id', $value['id']);
                    $this->db->update('tbl_request_system_control', ['barcode' => $code]);
                }
                $qr = vn_to_str(str_replace('||', '__', $code));
                $folder = FCPATH . 'uploads/request_system_control/';
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
                    $objDrawing1->setCoordinates($columsExcel[13] . $row);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(60);
                $objPHPExcel->getActiveSheet()->setCellValue($columsExcel[13] . $row, '')->getStyle($columsExcel[13] . $row)->applyFromArray($BStylenumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A4:N' . $row)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A4:N' . $row)->applyFromArray([
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
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[5])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[6])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[7])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[8])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columsExcel[9])->setWidth(20);
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
            $filename = lang('Phieu_yeu_cau_kiem_soat_he_thong') . '.xls';
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
