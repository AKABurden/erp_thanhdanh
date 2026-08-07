<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_recruitment extends AdminController
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

        $this->preViewSuggestRecruitment = has_permission('suggest_recruitment','','view');
        $this->preViewOwnSuggestRecruitment = has_permission('suggest_recruitment','','view_own');
        $this->preAddSuggestRecruitment = has_permission('suggest_recruitment','','create');
        $this->preEditSuggestRecruitment = has_permission('suggest_recruitment','','edit');
        $this->preApproveSuggestRecruitment = has_permission('suggest_recruitment','','approve');
        $this->preDeleteSuggestRecruitment= has_permission('suggest_recruitment','','delete');
    }

    public function index()
    {
        if (!$this->preViewSuggestRecruitment && !$this->preViewOwnSuggestRecruitment) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_recruitment');
        $this->load->view('admin/suggest_recruitment/index', $data);
    }

    public function getSuggestRecruitment()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_recruitment.id as id',
            'tbl_suggest_recruitment.reference_no as reference_no',
            'tbl_suggest_recruitment.date as date',
            'tblroles.name as position_recruitment',
            'tbl_suggest_recruitment.content_work as content_work',
            'tbl_suggest_recruitment.kpis as kpis',
            'tbl_suggest_recruitment.note as note',
            'tbl_suggest_recruitment.quantity as quantity',
            'tbl_suggest_recruitment.time_work as time_work',
            'tbl_suggest_recruitment.gender as gender',
            'tbl_suggest_recruitment.completion_time_limit as completion_time_limit',
            'tbl_suggest_recruitment.standard as standard',
            'tbl_suggest_recruitment.status as status',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_recruitment';
        $where = [

        ];
        $filter = [];

        $join = [
            'LEFT JOIN tblroles ON tblroles.roleid = tbl_suggest_recruitment.position_recruitment'
        ];

        if (!$this->preViewSuggestRecruitment) {
            array_push($where, 'AND (tbl_suggest_recruitment.created_by = '.get_staff_user_id().')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_recruitment.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_recruitment.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_recruitment.date_status',
            'tbl_suggest_recruitment.staff_status'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_recruitment/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left">' . $aRow['position_recruitment'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['content_work'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['kpis'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['note'] . '</div>';
            $row[] = '<div class="text-center">' . formatNumber($aRow['quantity']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['time_work']) . '</div>';
            $row[] = '<div class="text-center">' . ($aRow['gender']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['completion_time_limit']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['standard']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } elseif ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left">' . $_data . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_recruitment/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestRecruitment ? '<a class="tnh-modal" href="' . base_url('admin/suggest_recruitment/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestRecruitment ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_recruitment/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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

    public function detail($id = 0){
        $data = [];
        $this->db->select('tbl_suggest_recruitment.*');
        $this->db->from('tbl_suggest_recruitment');
        $this->db->where('tbl_suggest_recruitment.id',$id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()){
            if (!empty($id)){
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_recruitment.reference_no]');
                }
            } else {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_recruitment.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            if (empty($id)){
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_recruitment');
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $position_recruitment = $this->input->post('position_recruitment');
                    $quantity = number_unformat($this->input->post('quantity'));
                    $kpis = $this->input->post('kpis');
                    $time_work = $this->input->post('time_work');
                    $gender = $this->input->post('gender');
                    $completion_time_limit = $this->input->post('completion_time_limit');
                    $standard = $this->input->post('standard');
                    $content_work = ($this->input->post('content_work'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'position_recruitment' => $position_recruitment,
                        'quantity' => $quantity,
                        'kpis' => $kpis,
                        'time_work' => $time_work,
                        'gender' => $gender,
                        'completion_time_limit' => $completion_time_limit,
                        'standard' => $standard,
                        'content_work' => $content_work,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_recruitment',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_recruitment') == $reference_no) {
                            updateReference('suggest_recruitment');
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_recruitment',
                            'table_obj' => 'tbl_suggest_recruitment',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu tuyển dụng') . ' [' . $reference_no . ']',
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
                echo json_encode($data);die();
            } else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $position_recruitment = $this->input->post('position_recruitment');
                    $quantity = number_unformat($this->input->post('quantity'));
                    $kpis = $this->input->post('kpis');
                    $time_work = $this->input->post('time_work');
                    $gender = $this->input->post('gender');
                    $completion_time_limit = $this->input->post('completion_time_limit');
                    $standard = $this->input->post('standard');
                    $content_work = ($this->input->post('content_work'));
                    $note = ($this->input->post('note'));
                    $fields = [
                        'date' => $date,
                        'position_recruitment' => $position_recruitment,
                        'quantity' => $quantity,
                        'kpis' => $kpis,
                        'time_work' => $time_work,
                        'gender' => $gender,
                        'completion_time_limit' => $completion_time_limit,
                        'standard' => $standard,
                        'content_work' => $content_work,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_recruitment',$fields);
                    if ($success){
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_recruitment',
                            'table_obj' => 'tbl_suggest_recruitment',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu tuyển dụng') . ' [' . $dtData['reference_no'] . ']',
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
                echo json_encode($data);die();
            }
        } else {
            if (empty($id)){
                if (!$this->preAddSuggestRecruitment){
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_recruitment');
            } else {
                if (!$this->preEditSuggestRecruitment){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_recruitment.*');
                $this->db->from('tbl_suggest_recruitment');
                $this->db->where('tbl_suggest_recruitment.id',$id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1){
                    refererModel(lang('Phiếu đã duyệt không thể sửa !'));

                }
                $data['dtData'] = $dtData;
                $data['title'] = lang('dt_edit_suggest_recruitment');
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_recruitment');
        $data['roles'] = [];
        $this->get_parent(0, $data['roles']);
        $this->load->view('admin/suggest_recruitment/detail',$data);
    }

    public function get_parent($id_parent = 0, &$array_category = [], $level = 0)
    {
        if (is_numeric($level)) {
            $this->db->where(array('roles_parent' => $id_parent));
            $this->db->where('active_role',1);
            $this->db->where('tblroles.type',0);
            $current_level = $this->db->get('tblroles')->result_array();
            if ($current_level) {
                foreach ($current_level as $key => $value) {
                    $sub = "";
                    for ($i = 0; $i < $level; $i++) {
                        $sub .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    $sub .= "&#10154;";
                    $current_level[$key]['name'] = $sub . " " . $current_level[$key]['name'];
                    array_push($array_category, $current_level[$key]);
                    $this->get_parent($value['roleid'], $array_category, $level + 1);
                }
            } else {
                return;
            }
        }
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_recruitment');

        $this->db->select('tbl_suggest_recruitment.*');
        $this->db->from('tbl_suggest_recruitment');
        $this->db->where('tbl_suggest_recruitment.id',$id);
        $dtData = $this->db->get()->row_array();

        $data['dtData'] = $dtData;
        $this->load->view('admin/suggest_recruitment/view',$data);
    }

    public function agree(){
        if (!$this->preApproveSuggestRecruitment) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_recruitment.*');
        $this->db->from('tbl_suggest_recruitment');
        $this->db->where('tbl_suggest_recruitment.id',$suggest_id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
        } else {

            if (($dtData['status'] == $status)) {
                $data['result'] = 0;
                $data['message'] = lang('Trạng thái đã được cập nhật vui lòng làm mới danh sách');
                echo responseData($data); return;
            }

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id',$suggest_id);
            $up = $this->db->update('tbl_suggest_recruitment',$options);
            if ($up) {

                insertActivityLog([
                    'type_parent_obj' => 'suggest_recruitment',
                    'table_obj' => 'tbl_suggest_recruitment',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu tuyển dụng') . ' [' . $dtData['reference_no'] . ']',
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

    public function delete($id){
        if (!$this->preDeleteSuggestRecruitment){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_recruitment.*');
        $this->db->from('tbl_suggest_recruitment');
        $this->db->where('tbl_suggest_recruitment.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        if ($dtData['status'] == 1){
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_recruitment');
        if ($success){

            $this->db->where('tbl_moderation_recruitment.suggest_recruitment_id',$id);
            $this->db->delete('tbl_moderation_recruitment');

            insertActivityLog([
                'type_parent_obj' => 'suggest_recruitment',
                'table_obj' => 'tbl_suggest_recruitment',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu tuyển dụng') . ' [' . $dtData['reference_no'] . ']',
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

    public function exportExcel(){
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
               tbl_suggest_recruitment.id as id,
               tbl_suggest_recruitment.reference_no as reference_no,
               tbl_suggest_recruitment.date as date,
               tblroles.name as position_recruitment,
               tbl_suggest_recruitment.content_work as content_work,
               tbl_suggest_recruitment.kpis as kpis,
               tbl_suggest_recruitment.note as note,
               tbl_suggest_recruitment.quantity as quantity,
               tbl_suggest_recruitment.time_work as time_work,
               tbl_suggest_recruitment.gender as gender,
               tbl_suggest_recruitment.completion_time_limit as completion_time_limit,
               tbl_suggest_recruitment.standard as standard,
               tbl_suggest_recruitment.barcode as barcode,
            ');
            $this->db->from('tbl_suggest_recruitment');
            $this->db->join('tblroles','tblroles.roleid = tbl_suggest_recruitment.position_recruitment','left');

            if (!$this->preViewSuggestRecruitment) {
                $this->db->where('(tbl_suggest_recruitment.created_by = '.get_staff_user_id().')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_recruitment.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_recruitment.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_recruitment.id desc');
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
            $number_excel_number = '#,##0' . ($decimals_number > 0 ? '.' . sprintf("%0" . $decimals_number . "s",
                        0) : '');
            $company = get_option('invoice_company_name');
            $address = get_option('invoice_company_address');
            $company_vat = get_option('company_vat');
            $objPHPExcel->getDefaultStyle()->applyFromArray([
                'font' => array(
                    'name'  => 'Times New Roman'
                ),
            ]);
            $objPHPExcel->getActiveSheet()->setCellValue('A1',
                ('PHIẾU YÊU CẦU TUYỂN DỤNG'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Ngày Lập Phiếu YCTD')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Vị Trí Tuyển Dụng')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Mô Tả Công Việc')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'KPIs')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Lý Do')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Số Lượng')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Thời Gian Làm Việc')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Giới Tính')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Định Mức Thời Gian Hoàn Thành)')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Tiêu Chuẩn/ Quy Định')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'QR')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['position_recruitment'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['content_work'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['kpis'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",$value['note'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['quantity'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['time_work'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    if ($value['gender'] == "male" ){
                        $htmlGender = 'Nam';
                    } elseif ($value['gender'] == "female" ){
                        $htmlGender = 'Nữ';
                    } elseif ($value['gender'] == "other" ){
                        $htmlGender = 'Khác';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $htmlGender)->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['completion_time_limit'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['standard'])->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);

                    if (!empty($value['barcode'])){
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_recruitment||'.$value['id'];
                        $this->db->where('id',$value['id']);
                        $this->db->update('tbl_suggest_recruitment',['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_recruitment/';
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
                    $params['savename'] = $folder.'qrcode/'. $qr . '.png';
                    $this->ciqrcode->generate($params);
                    $img = ($folder.'qrcode/'. $qr . '.png');
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
                    $objPHPExcel->getActiveSheet()->getStyle("H$rowBegin:J$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_tuyen_dung') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
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

    public function changePositionRecruiment(){
        $position_recruiment = $this->input->post('position_recruiment');
        $this->db->select('
            tbl_detail_task_detail.*,
            tbl_category_detail_task.name as name_category,
        ');
        $this->db->from('tbl_detail_task_detail');
        $this->db->join('tbl_category_detail_task','tbl_category_detail_task.id = tbl_detail_task_detail.category_id');
        $this->db->join('tbl_detail_task','tbl_detail_task.id = tbl_detail_task_detail.detail_task_id');
        $this->db->where('tbl_detail_task.role_id',$position_recruiment);
        $dtDataItems = $this->db->get()->result_array();
        $arrItems = [];
        if (!empty($dtDataItems)){
            foreach ($dtDataItems as $key => $value){
                if (!empty($arrItems[$value['name_category']])){
                    $arrItems[$value['name_category']][] = $value;
                } else {
                    $arrItems[$value['name_category']][] = $value;
                }
            }
        }
        $html = '<table><tbody>';
            $stt = 1; if (!empty($arrItems)){
               foreach ($arrItems as $key => $value){
                  $html .='<tr>
                            <td class="text-center">'.$stt.'</td>
                            <td class="bold" style="width: 180px">'.$key.'</td>
                            <td></td>
                        </tr>';
                         $sttNew = 1; if (!empty($value)){
                            foreach ($value as $kk => $vv){
                                $html .='<tr>
                                    <td></td>
                                    <td class="text-right">'.$stt.'.'.$sttNew.'</td>
                                    <td>'.$vv['note'].'</td>
                                </tr>';
                         $sttNew++;
                            }
                         }
                 $stt++;
               }
            }
        $html .'</tbody>';

        $data['html'] = $html;
        echo json_encode($data);
    }
}