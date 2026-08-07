<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_paid_holidays extends AdminController
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

        $this->preViewSuggestPaidHolidays = true;
        $this->preViewOwnSuggestPaidHolidays = true;
        $this->preAddSuggestPaidHolidays = true;
        $this->preEditSuggestPaidHolidays = true;
        $this->preApproveSuggestPaidHolidays = true;
        $this->preDeleteSuggestPaidHolidays = true;
    }

    public function index(){
        if (!$this->preViewSuggestPaidHolidays && !$this->preViewOwnSuggestPaidHolidays){
            access_denied();
        }
        $data['title'] = _l('dt_suggest_paid_holidays');
        $this->load->view('admin/suggest_paid_holidays/index', $data);
    }

    public function getSuggestPaidHolidays(){
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_paid_holiday.id as id',
            'tbl_suggest_paid_holiday.reference_no as reference_no',
            'tbl_suggest_paid_holiday.date as date',
            'tbl_suggest_paid_holiday.staff_id as staff_id',
            'tblroles.code_role as code_role',
            'tbl_suggest_paid_holiday.regulations as regulations',
            'tbl_suggest_paid_holiday.created_by as created_by',
            'tbl_suggest_paid_holiday.staff_agree as staff_agree',
            'tblproduction_report.name_report as name_report',
            'tbl_suggest_paid_holiday.staff_reciever as staff_reciever',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_paid_holiday';
        $where = [

        ];
        $filter = [];

        $join = [
            'INNER JOIN tblstaff ON tblstaff.staffid = tbl_suggest_paid_holiday.staff_id',
            'LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role',
            'LEFT JOIN tblproduction_report ON tblproduction_report.id = tbl_suggest_paid_holiday.production_report_id',
        ];

        if (!$this->preViewSuggestPaidHolidays) {
            array_push($where,'AND tbl_suggest_paid_holiday.created_by', get_staff_user_id());
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search).' 00:00:00';
            array_push($where, "AND tbl_suggest_paid_holiday.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search).' 23:59:59';
            array_push($where, "AND tbl_suggest_paid_holiday.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 50px"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" id="rows-child-' . $aRow['id'] . '" class="rows-child fa fa-caret-right"></a></div>';
            $row[] = '<div class="text-left" style="width: 120px">' . $aRow['reference_no'] . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">'.get_staff_full_name($aRow['staff_id']).'</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['code_role']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['regulations']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['created_by']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_agree']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_report']) . '</div>';
            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['staff_reciever']) . '</div>';

            $edit = $this->preEditSuggestPaidHolidays ? '<a href="' . base_url('admin/suggest_paid_holidays/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPaidHolidays ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_paid_holidays/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('phiếu') . '</a>' : '';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $edit . '</li>
                    <li class="not-outside">' . $delete . '</li>
                </ul>
            </div>';
            $row[] = '<div style="width: 120px">'.$actions.'</div>';

            $trItems = '';
            $this->db->select('
                tbl_suggest_paid_holiday_item.date_start as date_start,
                tbl_suggest_paid_holiday_item.date_end as date_end,
                tbl_suggest_paid_holiday_item.number_date as number_date,
                tbl_suggest_paid_holiday_item.day_work as day_work,
                tbl_suggest_paid_holiday_item.note as note,
                tbl_type_magic.name as name_type_magic,
                tbl_type_magic.id as id_type_magic,
                tbl_suggest_paid_holiday_item.id as id,
                tbl_suggest_paid_holiday_item.date_status as date_status,
                tbl_suggest_paid_holiday_item.status as status,
                tbl_suggest_paid_holiday_item.staff_status as staff_status,
                tbl_suggest_paid_holiday_item.note_status as note_status,
            ');
            $this->db->from('tbl_suggest_paid_holiday_item');
            $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_suggest_paid_holiday_item.type_magic',
                'left');
            $this->db->where('tbl_suggest_paid_holiday_item.suggest_paid_holiday_id', $aRow['id']);
            $paidHplidayDetail = $this->db->get()->result_array();
            $countStatus = 0;
            foreach ($paidHplidayDetail as $k => $v) {

                $name_type = '';
                if ($v['id_type_magic'] == 1){
                    $name_type = ' (CP , P 1/2)';
                } elseif($v['id_type_magic'] == 2){
                    $name_type = ' (OD)';
                } elseif($v['id_type_magic'] == 3){
                    $name_type = ' (CH)';
                } elseif($v['id_type_magic'] == 4){
                    $name_type = ' (TS)';
                } elseif($v['id_type_magic'] == 5){
                    $name_type = ' (PKL , PKL 1/2)';
                } elseif($v['id_type_magic'] == 6){
                    $name_type = ' (F)';
                }

                $user_status = $v['staff_status'];
                if (!empty($v['date_status'])) {
                    $date_status = _d($v['date_status']);
                }
                $full_name = get_staff_full_name($user_status);
                $strApproveHtml = '';
                if (!empty($user_status)) {
                    $strApproveHtml = '<a class="mright5 mtop5" data-toggle="tooltip" data-title="' . $full_name . '" href="' . admin_url('profile/' . $user_status) . '">' . staff_profile_image(
                            $user_status,
                            ['staff-profile-image-small mbot5']
                        ) . '</a> <span>' . $full_name . '<br/><i style="font-size: 9px;">' . $date_status . '</i>';
                }

                $strApprove = '';
                $strNote = '';
                if ($v['status'] == 0) {
                    $countStatus ++;
                    $html = "<p><a id='agree_child' value='1' data-id='" . $v['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                             <a id='agree_child' data-id= '" . $v['id'] . "' value='2' class='btn btn-danger label not_approve'>Không duyệt</a><br><label style='margin-top:10px' class='label-note hide'>Ghi chú</label><textarea class='form-control hide note_approve_task' name='note_approve_task' rows='3' placeholder=' nhập ghi chú '></textarea>
                             <button style='margin-top:5px;margin-left:5px' class='btn btn-info hide po-save'>Lưu</button>
                             <button class='btn po-close hide btn-icon'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-warning po" data-original-title="Duyệt">Chưa duyệt</span></div>';
                } elseif ($v['status'] == 1) {
                    $html = "<p><a id='agree_child' value='0' data-id='" . $v['id'] . "' class='btn btn-warning btn-icon'>Bỏ duyệt</a>
                             <a id='agree_child' data-id= '" . $v['id'] . "' value='2' class='btn btn-danger label not_approve'>Không duyệt</a><br><label style='margin-top:10px' class='label-note hide'>Ghi chú</label><textarea class='form-control hide note_approve_task' name='note_approve_task' rows='3' placeholder=' nhập ghi chú '></textarea>
                            <button style='margin-top:5px;margin-left:5px' class='btn btn-info hide po-save'>Lưu</button>
                            <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-success po" data-original-title="Duyệt">Đã duyệt</span></div>';
                } elseif ($v['status'] == 2) {
                    $html = "<p>
                            <a id='agree_child' value='1' data-id='" . $v['id'] . "' class='btn btn-success btn-icon'>Duyệt</a>
                            <a id='agree_child' value='0' data-id='" . $v['id'] . "' class='btn btn-danger btn-icon'>Bỏ duyệt</a>
                            <button class='btn po-close  btn-icon hide'>Thoát</button></p>";
                    $strApprove = '<div class="text-center mbot5"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="" data-content="' . $html . '" class="label label-danger po" data-original-title="Duyệt">Không duyệt</span></div>';
                    $strNote = '<div>'.$v['note_status'].'</div>';
                }

                $trItems .= '<tr>
                        <td class="text-center">' . (++$k) . '</td>
                        <td class="text-left">' . $v['name_type_magic'] . $name_type. '</td>
                        <td class="text-left">' . _dhau($v['date_start']) . '</td>
                        <td class="text-left">' . _dhau($v['date_end']) . '</td>
                        <td class="text-center">' . ($v['number_date']) . '</td>
                        <td class="text-left" style="width: 90px;">' . _dhau($v['day_work']) . '</td>
                        <td class="text-left" style="width: 150px;">' . $v['note'] . '</td>
                        <td class="text-left" style="width: 150px;">' . $strApprove .$strNote. $strApproveHtml . '</td>
                    </tr>';
            }
            $_data = '
                <div class="scrolling-stone pr-4 position-absolute h-100 w-100 overflow-auto max-height">
                    <div class="">
                         <div class="col-md-10">
                            <table class="table" style="margin-top: 0px;">
                                <thead>
                                    <tr>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 50px;">STT</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 100px;">' . lang('Loại phép') . '</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 120px;">' . lang('Từ ngày') . '</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 120px;">' . lang('Đến ngày') . '</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 100px;">' . lang('Số ngày nghỉ') . '</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 80px;">' . lang('Ngày đi làm lại') . '</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 150px;">' . lang('Ghi chú') . '</th>
                                        <th style="background: #FEF7E2 !important; border: 1px solid #d5d5d5 !important; color: black !important;" class="text-center" style="width: 150px;">' . lang('Trạng thái') . '</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $trItems . '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            ';
            $row[] = '<div class="text-left">' . $_data . '</div>';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail($id = 0){
        $data = [];
        if ($this->input->post()){
            if (empty($id)){
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_paid_holiday.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Người đề xuất"), 'required');
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_paid_holiday');
                    $date = to_sql_date($this->input->post('date'), true);
                    $staff_id = $this->input->post('staff_id');
                    $staff_reciever = !empty($this->input->post('staff_reciever')) ? $this->input->post('staff_reciever') : 0;
                    $staff_agree = !empty($this->input->post('staff_agree')) ? $this->input->post('staff_agree') : 0;
                    $production_report_id = !empty($this->input->post('production_report_id')) ? $this->input->post('production_report_id') : 0;
                    $regulations = !empty($this->input->post('regulations')) ? $this->input->post('regulations') : null;
                    $counter = $this->input->post('conter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $type_magic = $this->input->post('type_magic')[$value];
                            if (empty($type_magic)) continue;

                            $month_sub = !empty($this->input->post('month_sub')[$value]) ? $this->input->post('month_sub')[$value] : false;
                            $total_quantity_sub = 0;
                            if (!empty($month_sub)) {
                                foreach ($month_sub as $k => $val) {
                                    if (empty($val)) {
                                        continue;
                                    }
                                    $quantity_sub = $this->input->post('quantity_sub')[$value][$k];
                                    $total_quantity_sub += $quantity_sub;
                                }
                            }
                            if ($total_quantity_sub <= 0) {
                                $data['result'] = 0;
                                $data['message'] = 'Vui lòng nhập số ngày nghỉ !';
                                echo json_encode($data);
                                die();
                            }
                            $date_start = !empty($this->input->post('date_start')[$value]) ? $this->input->post('date_start')[$value] : null;
                            $date_end = !empty($this->input->post('date_end')[$value]) ? $this->input->post('date_end')[$value] : null;
                            $day_work = !empty($this->input->post('day_work')[$value]) ? $this->input->post('day_work')[$value] : null;
                            $note = !empty($this->input->post('note')[$value]) ? $this->input->post('note')[$value] : null;
                            $items[] = [
                                'type_magic' => $type_magic,
                                'date_start' => to_sql_date($date_start),
                                'date_end' => to_sql_date($date_end),
                                'number_date' => $total_quantity_sub,
                                'day_work' => to_sql_date($day_work),
                                'note' => $note,
                            ];

                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = 'Không có dữ liệu chi tiết';
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'production_report_id' => $production_report_id,
                        'staff_id' => $staff_id,
                        'staff_agree' => $staff_agree,
                        'staff_reciever' => $staff_reciever,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'regulations' => $regulations,
                    ];
                    $this->db->insert('tbl_suggest_paid_holiday',$fields);
                    $id = $this->db->insert_id();
                    if ($id){
                        if (getReference('suggest_paid_holiday') == $reference_no) {
                            updateReference('suggest_paid_holiday');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_paid_holiday_id'] = $id;
                            $this->db->insert('tbl_suggest_paid_holiday_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_paid_holiday',
                            'table_obj' => 'tbl_suggest_paid_holiday',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu nghỉ phép') . ' [' . $reference_no . ']',
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
                $this->db->select('tbl_suggest_paid_holiday.*');
                $this->db->from('tbl_suggest_paid_holiday');
                $this->db->where('tbl_suggest_paid_holiday.id',$id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_paid_holiday.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Người đề xuất"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $staff_id = $this->input->post('staff_id');
                    $staff_reciever = !empty($this->input->post('staff_reciever')) ? $this->input->post('staff_reciever') : 0;
                    $staff_agree = !empty($this->input->post('staff_agree')) ? $this->input->post('staff_agree') : 0;
                    $production_report_id = !empty($this->input->post('production_report_id')) ? $this->input->post('production_report_id') : 0;
                    $regulations = !empty($this->input->post('regulations')) ? $this->input->post('regulations') : null;
                    $counter = $this->input->post('conter');
                    $items = [];
                    if (!empty($counter)){
                        foreach ($counter as $key => $value){
                            $type_magic = $this->input->post('type_magic')[$value];
                            if (empty($type_magic)) continue;

                            $month_sub = !empty($this->input->post('month_sub')[$value]) ? $this->input->post('month_sub')[$value] : false;
                            $total_quantity_sub = 0;
                            if (!empty($month_sub)) {
                                foreach ($month_sub as $k => $val) {
                                    if (empty($val)) {
                                        continue;
                                    }
                                    $quantity_sub = $this->input->post('quantity_sub')[$value][$k];
                                    $total_quantity_sub += $quantity_sub;
                                }
                            }
                            if ($total_quantity_sub <= 0) {
                                $data['result'] = 0;
                                $data['message'] = 'Vui lòng nhập số ngày nghỉ !';
                                echo json_encode($data);
                                die();
                            }
                            $date_start = !empty($this->input->post('date_start')[$value]) ? $this->input->post('date_start')[$value] : null;
                            $date_end = !empty($this->input->post('date_end')[$value]) ? $this->input->post('date_end')[$value] : null;
                            $day_work = !empty($this->input->post('day_work')[$value]) ? $this->input->post('day_work')[$value] : null;
                            $note = !empty($this->input->post('note')[$value]) ? $this->input->post('note')[$value] : null;

                            $suggest_paid_holiday_item_id = !empty($this->input->post('suggest_paid_holiday_item_id')[$value]) ? $this->input->post('suggest_paid_holiday_item_id')[$value] : 0;
                            $items[] = [
                                'id' => $suggest_paid_holiday_item_id,
                                'type_magic' => $type_magic,
                                'date_start' => to_sql_date($date_start),
                                'date_end' => to_sql_date($date_end),
                                'number_date' => $total_quantity_sub,
                                'day_work' => to_sql_date($day_work),
                                'note' => $note,
                            ];

                        }
                    }
                    if (empty($items)){
                        $data['result'] = 0;
                        $data['message'] = 'Không có dữ liệu chi tiết';
                        echo json_encode($data);
                        die();
                    }
                    $fields = [
                        'date' => $date,
                        'production_report_id' => $production_report_id,
                        'staff_id' => $staff_id,
                        'staff_agree' => $staff_agree,
                        'staff_reciever' => $staff_reciever,
                        'regulations' => $regulations,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id',$id);
                    $success = $this->db->update('tbl_suggest_paid_holiday',$fields);
                    if ($success){
                        $this->db->where('suggest_paid_holiday_id',$id);
                        $this->db->delete('tbl_suggest_paid_holiday_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_paid_holiday_id'] = $id;
                            $this->db->insert('tbl_suggest_paid_holiday_item',$value);
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_paid_holiday',
                            'table_obj' => 'tbl_suggest_paid_holiday',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu nghỉ phép') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestPaidHolidays){
                    accessDenied(true);
                }
                $year = date('Y');
                $month = date('m');
                $allDate = createDateRangeArray($month,$year);
                $allDateNew = [];
                foreach ($allDate as $key => $value){
                    $timestamp = strtotime($value);
                    $day = date('D', $timestamp);
                    $month = date('M', $timestamp);
                    $date = date('d', $timestamp);
                    $allDateNew[$key]= [
                        'date' => _dhau($value),
                        'day' => $day,
                        'month' => $month,
                        'date_new' => $date,
                    ];
                }
                $data['allDateNew'] = $allDateNew;
                $data['title'] = lang('dt_add_suggest_paid_holidays');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_paid_holidays'), 'page' => lang('dt_suggest_paid_holidays')), array('link' => '#', 'page' => lang('dt_add_suggest_paid_holidays'))];
            } else {
                if (!$this->preEditSuggestPaidHolidays){
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_paid_holiday.*');
                $this->db->from('tbl_suggest_paid_holiday');
                $this->db->where('tbl_suggest_paid_holiday.id',$id);
                $dtData = $this->db->get()->row_array();


                $this->db->select('tbl_suggest_paid_holiday_item.*');
                $this->db->from('tbl_suggest_paid_holiday_item');
                $this->db->where('tbl_suggest_paid_holiday_item.suggest_paid_holiday_id',$id);
                $dtItems = $this->db->get()->result_array();

                $this->db->where('suggest_paid_holiday_id', $id);
                $this->db->group_start();
                $this->db->where('status', 1);
                $this->db->or_where('status', 2);
                $this->db->group_end();
                $paidHoliday = $this->db->get('tbl_suggest_paid_holiday_item')->row_array();

                if (!empty($paidHoliday)) {
                    set_alert('danger',  'Có chi tiết phiếu yêu cầu phép đã được duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;

                $year = date('Y');
                $month = date('m');
                $allDate = createDateRangeArray($month,$year);
                $allDateNew = [];
                foreach ($allDate as $key => $value){
                    $timestamp = strtotime($value);
                    $day = date('D', $timestamp);
                    $month = date('M', $timestamp);
                    $date = date('d', $timestamp);
                    $allDateNew[$key]= [
                        'date' => _dhau($value),
                        'day' => $day,
                        'month' => $month,
                        'date_new' => $date,
                    ];
                }
                $data['allDateNew'] = $allDateNew;

                $data['title'] = lang('dt_edit_suggest_paid_holidays');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_paid_holidays'), 'page' => lang('dt_suggest_paid_holidays')), array('link' => '#', 'page' => lang('dt_edit_suggest_paid_holidays'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['typeMagic'] = get_table_where('tbl_type_magic', [], 'id ASC', 'result_array');
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_paid_holiday');
        $this->load->view('admin/suggest_paid_holidays/detail',$data);
    }

    public function view($id){
        $data = [];
        $data['title'] = lang('dt_view_suggest_replace');

        $this->db->select('tbl_suggest_repalce.*');
        $this->db->from('tbl_suggest_repalce');
        $this->db->where('tbl_suggest_repalce.id',$id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_replace_item.*,
            tbl_result.name as name_result,
            tblsuppliers.company as company,
        ');
        $this->db->from('tbl_suggest_replace_item');
        $this->db->join('tblsuppliers','tblsuppliers.id = tbl_suggest_replace_item.suppliers_id','left');
        $this->db->join('tbl_result','tbl_result.id = tbl_suggest_replace_item.result','left');
        $this->db->where('tbl_suggest_replace_item.suggest_replace_id',$id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_repalce/view',$data);
    }


    public function delete($id){
        if (!$this->preDeleteSuggestPaidHolidays){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_suggest_paid_holiday.*');
        $this->db->from('tbl_suggest_paid_holiday');
        $this->db->where('tbl_suggest_paid_holiday.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }


        $this->db->where('suggest_paid_holiday_id', $id);
        $this->db->group_start();
        $this->db->where('status', 1);
        $this->db->or_where('status', 2);
        $this->db->group_end();
        $paidHoliday = $this->db->get('tbl_suggest_paid_holiday_item')->row_array();

        if (!empty($paidHoliday)) {
            $data['result'] = 0;
            $data['message'] = lang('Có chi tiết phiếu yêu cầu phép đã được duyệt không thể xóa !');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_suggest_paid_holiday');
        if ($success){
            $this->db->where('tbl_suggest_paid_holiday_item.suggest_paid_holiday_id',$id);
            $this->db->delete('tbl_suggest_paid_holiday_item');

            insertActivityLog([
                'type_parent_obj' => 'suggest_paid_holiday',
                'table_obj' => 'tbl_suggest_paid_holiday',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu vật nghỉ phép') . ' [' . $dtData['reference_no'] . ']',
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


    public function update_status_child()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $note = $this->input->post('note');

        if (!$this->preApproveSuggestPaidHolidays){
            echo json_encode([
                'success' => false,
                'message' => _l('Bạn không có quyền duyệt')
            ]);
            die();
        }

        $suggest_paid_holiday_id = 0;
        if (!empty($id)) {

            $this->db->where('id', $id);
            $paidHoliday = $this->db->get('tbl_suggest_paid_holiday_item')->row_array();
            if ($paidHoliday['status'] == $status) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('Loại phép này đang ở trạng thái này không thể duyệt được nữa')
                ]);
                die();
            }

            $suggest_paid_holiday_id = $paidHoliday['suggest_paid_holiday_id'];

            $dtSuggestPaidHoliday = get_table_where('tbl_suggest_paid_holiday',
                array('id' => $suggest_paid_holiday_id), '', 'row_array');
            $staff_id = $dtSuggestPaidHoliday['staff_id'];

            $year_search = date('Y');
            $year_search_old = date('Y') - 1;
            $data_update = ['status' => $status];
            $optionPaid = [];
            $items = [];
            $total_date_phep = 0;
            if (!empty($status)) {
                $data_update['staff_status'] = get_staff_user_id();
                $data_update['date_status'] = date('Y-m-d H:i:s');
                $data_update['note_status'] = $note;

                $this->db->where('id',$suggest_paid_holiday_id);
                $this->db->from('tbl_suggest_paid_holiday');
                $dtSuggestPaid = $this->db->get()->row_array();
                if (!empty($dtSuggestPaid)){
                    $optionPaid = [
                        'name' => $dtSuggestPaid['reference_no'],
                        'staff_id' => $dtSuggestPaid['staff_id'],
                        'staff_agree' => $dtSuggestPaid['staff_agree'],
                        'suggest_paid_holiday_id' => $dtSuggestPaid['id'],
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                    ];
                }

                if (!empty($paidHoliday)){
                    $items = [
                        'type_magic_id' => $paidHoliday['type_magic'],
                        'date_start' => $paidHoliday['date_start'],
                        'date_end' => $paidHoliday['date_end'],
                        'number_date' => $paidHoliday['number_date'],
                        'day_work' => $paidHoliday['day_work'],
                        'note' => $paidHoliday['note'],
                        'status' => $status,
                        'staff_status' =>  get_staff_user_id(),
                        'date_status' =>  date('Y-m-d H:i:s'),
                        'note_status' =>  $note,
                        'suggest_paid_holiday_item_id' =>  $paidHoliday['id'],
                    ];
                    $total_date_phep += $paidHoliday['number_date'];
                }
                if (!empty($items)){

                    $this->db->where('suggest_paid_holiday_id',$optionPaid['suggest_paid_holiday_id']);
                    $this->db->where('staff_id',$optionPaid['staff_id']);
                    $this->db->from('tbl_paid_holiday_leave');
                    $dtCheckPaidHoliday = $this->db->get()->row_array();
                    if (!empty($dtCheckPaidHoliday)) {

                        $this->db->where('date_start', $items['date_start']);
                        $this->db->where('paid_holiday_leave_id', $dtCheckPaidHoliday['id']);
                        $this->db->where('suggest_paid_holiday_item_id', 0);
                        $this->db->from('tbl_paid_holiday_leave_detail');
                        $dtCheckPaidHolidayItem = $this->db->get()->row_array();
                        if (!empty($dtCheckPaidHolidayItem)) {
                            echo json_encode([
                                'result' => false,
                                'id' => $paidHoliday['suggest_paid_holiday_id'],
                                'message' => _l('Ngày này đã tồn tại trong đơn xin nghỉ phép vui lòng kiểm tra lại!')
                            ]);
                            die();
                        }
                    }
                }

                $this->db->select('tbl_setup_paid_holiday_staff.number_day,number_day_now,number_day_old');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$staff_id);
                $paid_year = $this->db->get()->row_array();

                $this->db->select('tbl_setup_paid_holiday_staff.number_day,number_day_now,number_day_old');
                $this->db->from('tbl_setup_paid_holiday');
                $this->db->join('tbl_setup_paid_holiday_staff','tbl_setup_paid_holiday_staff.id_setup_paid_holiday = tbl_setup_paid_holiday.id');
                $this->db->where('tbl_setup_paid_holiday.year',$year_search_old);
                $this->db->where('tbl_setup_paid_holiday_staff.staff_id',$staff_id);
                $paid_year_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search");
                $number_date_new = $this->db->get()->row_array();

                $number_date_phep = $number_date['number_date'] + ($number_date_new['number_date'] * 0.5);

//                $number_date_phep = (!empty($paid_year) && !empty($paid_year['number_day'])) ? $paid_year['number_day'] - $number_date_phep : 0;
//                $number_date_phep = $number_date_phep < 0 ? 0 : $number_date_phep;

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_old = $this->db->get()->row_array();

                $this->db->select('COUNT(tbl_timekeeping_detail.id) as number_date');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave',
                    'tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_paid_holiday_leave_detail_month',
                    'tbl_paid_holiday_leave_detail_month.paid_holiday_leave_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->join('tbl_timekeeping_detail',
                    'tbl_timekeeping_detail.paid_holiday_detail_id = tbl_paid_holiday_leave_detail.id');
                $this->db->where('tbl_paid_holiday_leave_detail.status', 1);
                $this->db->where('tbl_paid_holiday_leave_detail.type_magic_id', 1);
                $this->db->where('tbl_paid_holiday_leave.staff_id', $staff_id);
                $this->db->where('tbl_timekeeping_detail.type', 'AL/2');
                $this->db->where("DATE_FORMAT(tbl_paid_holiday_leave_detail.date_start, \"%Y\") >= $year_search_old AND DATE_FORMAT(tbl_paid_holiday_leave_detail.date_end, \"%Y\") <= $year_search_old");
                $number_date_new_old = $this->db->get()->row_array();

                $number_date_phep_old = $number_date_old['number_date'] + ($number_date_new_old['number_date'] * 0.5);

//                $number_date_phep_old = (!empty($paid_year_old) && !empty($paid_year_old['number_day'])) ? $paid_year_old['number_day'] - $number_date_phep_old : 0;
//                $number_date_phep_old = $number_date_phep_old < 0 ? 0 : $number_date_phep_old;

                $month = 3;
                $day = 31;
                $date_check = date('Y-'.$month.'-'.$day.'');
                $number_day_old = !empty($paid_year['number_day_old']) ? $paid_year['number_day_old'] : 0;
                if (strtotime(date('Y-m-d')) > strtotime($date_check)){
                    $number_date_phep_old = 0;
                }
                $number_date_phep = (!empty($paid_year) && !empty($paid_year['number_day_now'] + $number_day_old ) ? ($paid_year['number_day_now'] + $number_day_old) - $number_date_phep : 0);
                $number_date_phep = $number_date_phep < 0 ? 0 : $number_date_phep;
                if ($total_date_phep > ($number_date_phep)){
                    $data['result'] = false;
                    $data['id'] = $paidHoliday['suggest_paid_holiday_id'];
                    $data['message'] = 'Số lượng phép năm không đủ';
                    echo json_encode($data);
                    die();
                }

            } else {
                $this->db->where('suggest_paid_holiday_item_id',$id);
                $this->db->from('tbl_paid_holiday_leave_detail');
                $dtCheckPaidHolidayItem = $this->db->get()->row_array();
                $this->db->select('
                    tbl_type_magic.name as name_magic,
                    date_start,
                    date_end,
                    tbl_type_magic.id as type_magic_id,
                    paid_holiday_leave_id,
                    tbl_paid_holiday_leave_detail.id as id,
                    tbl_paid_holiday_leave.staff_id
                    ');
                $this->db->from('tbl_paid_holiday_leave_detail');
                $this->db->join('tbl_paid_holiday_leave','tbl_paid_holiday_leave.id = tbl_paid_holiday_leave_detail.paid_holiday_leave_id');
                $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id');
                $this->db->where('tbl_paid_holiday_leave_detail.id', $dtCheckPaidHolidayItem['id']);
                $dtPaidHolidayDetail = $this->db->get()->row_array();

                $paid_holiday_id = $dtPaidHolidayDetail['paid_holiday_leave_id'];
                $paid_holiday_detail_id = $dtPaidHolidayDetail['id'];
                $date_start = $dtPaidHolidayDetail['date_start'];
                $date_end = $dtPaidHolidayDetail['date_end'];
                $this->db->select('tbl_timekeeping_detail.id,tbl_timekeeping_detail.date,tbl_timekeeping_detail.staff_id,tbl_timekeeping.month,tbl_timekeeping.year');
                $this->db->from('tbl_timekeeping_detail');
                $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
                $this->db->where('date >=',$date_start);
                $this->db->where('date <=',$date_end);
                $this->db->where('staff_id',$dtPaidHolidayDetail['staff_id']);
                $timeDetails = $this->db->get()->result_array();
                if (!empty($timeDetails)){
                    foreach ($timeDetails as $kk => $vv){
                        $this->db->from('tbl_payroll_item');
                        $this->db->join('tbl_payroll',
                            'tbl_payroll.id = tbl_payroll_item.payroll_id');
                        $this->db->where('tbl_payroll.month', $vv['month']);
                        $this->db->where('tbl_payroll.year', $vv['year']);
                        $this->db->where('tbl_payroll_item.staff_id', $vv['staff_id']);
                        $payrollItem = $this->db->get()->row_array();
                        if (!empty($payrollItem)) {
                            echo json_encode([
                                'result' => false,
                                'id' => $suggest_paid_holiday_id,
                                'message' => _l('Loại phép này đã ap dung bên chấm công và đã tính bảng lương không thể bỏ duyệt !')
                            ]);
                            die();
                        }
                    }
                }

                $suggest_paid_holiday_id = $paidHoliday['suggest_paid_holiday_id'];
                $this->db->where('id',$suggest_paid_holiday_id);
                $this->db->from('tbl_suggest_paid_holiday');
                $dtSuggestPaid = $this->db->get()->row_array();
                if (!empty($dtSuggestPaid)){
                    $optionPaid = [
                        'name' => $dtSuggestPaid['reference_no'],
                        'staff_id' => $dtSuggestPaid['staff_id'],
                        'staff_agree' => $dtSuggestPaid['staff_agree'],
                        'suggest_paid_holiday_id' => $dtSuggestPaid['id'],
                    ];
                }

                if (!empty($paidHoliday)){
                    $items = [
                        'type_magic_id' => $paidHoliday['type_magic'],
                        'date_start' => $paidHoliday['date_start'],
                        'date_end' => $paidHoliday['date_end'],
                        'number_date' => $paidHoliday['number_date'],
                        'day_work' => $paidHoliday['day_work'],
                        'note' => $paidHoliday['note'],
                        'status' => $status,
                        'note_status' =>  $note,
                        'suggest_paid_holiday_item_id' => $paidHoliday['id'],
                    ];
                }

                $data_update['staff_status'] = null;
                $data_update['date_status'] = null;
                $data_update['status'] = 0;
                $data_update['note_status'] = null;
            }
            $this->db->where('id', $id);
            $success = $this->db->update('tbl_suggest_paid_holiday_item', $data_update);
            if (!empty($success)) {
                $get_code = get_table_where('tbl_suggest_paid_holiday',
                    array('id' => $paidHoliday['suggest_paid_holiday_id']), '', 'row');
                $this->db->select('
                    tbl_type_magic.name as name_magic,
                    date_start,
                    date_end,
                    tbl_type_magic.id as type_magic_id,
                    suggest_paid_holiday_id,
                    tbl_suggest_paid_holiday_item.id as id
                    ');
                $this->db->from('tbl_suggest_paid_holiday_item');
                $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_suggest_paid_holiday_item.type_magic');
                $this->db->where('tbl_suggest_paid_holiday_item.id', $id);
                $get_code_child = $this->db->get()->row_array();

                activity_log_v2('status_suggest_paid_holiday_child', 'tbl_suggest_paid_holiday_item', $id,
                    $get_code->reference_no,
                    'Duyệt phiếu yêu cầu nghỉ phép [' . $get_code->reference_no . '][' . $get_code_child['name_magic'] . ']');

                if (!empty($optionPaid)){
                    $this->db->where('suggest_paid_holiday_id',$optionPaid['suggest_paid_holiday_id']);
                    $this->db->where('staff_id',$optionPaid['staff_id']);
                    $this->db->from('tbl_paid_holiday_leave');
                    $dtCheckPaidHoliday = $this->db->get()->row_array();


                    if (!empty($dtCheckPaidHoliday)){
                        $paid_holiday_id = $dtCheckPaidHoliday['id'];
                        $this->db->where('date_start',$items['date_start']);
                        $this->db->where('suggest_paid_holiday_item_id',$items['suggest_paid_holiday_item_id']);
                        $this->db->where('paid_holiday_leave_id',$paid_holiday_id);
                        $this->db->from('tbl_paid_holiday_leave_detail');
                        $dtCheckPaidHolidayItem = $this->db->get()->row_array();
                        if (!empty($paid_holiday_id)){
                            $items['paid_holiday_leave_id'] = $paid_holiday_id;
                            if (!empty($dtCheckPaidHolidayItem)){
                                $this->db->where('id',$dtCheckPaidHolidayItem['id']);
                                $this->db->update('tbl_paid_holiday_leave_detail', $items);
                                $paid_holiday_item_id = $dtCheckPaidHolidayItem['id'];
                            } else {
                                $this->db->insert('tbl_paid_holiday_leave_detail', $items);
                                $paid_holiday_item_id = $this->db->insert_id();
                                $date_start = explode('-',$items['date_start']);
                                $month = $date_start[1];
                                $this->db->insert('tbl_paid_holiday_leave_detail_month', [
                                    'paid_holiday_leave_detail_id' => $paid_holiday_item_id,
                                    'month' => $month,
                                    'number_day' => $items['number_date'],
                                    'suggest_paid_holiday_item_id' => $items['suggest_paid_holiday_item_id'],
                                ]);
                            }
                        }
                    } else {
                        $this->db->insert('tbl_paid_holiday_leave',$optionPaid);
                        $paid_holiday_id = $this->db->insert_id();

                        $this->db->where('date_start',$items['date_start']);
                        $this->db->where('suggest_paid_holiday_item_id',$items['suggest_paid_holiday_item_id']);
                        $this->db->where('paid_holiday_leave_id',$paid_holiday_id);
                        $this->db->from('tbl_paid_holiday_leave_detail');
                        $dtCheckPaidHolidayItem = $this->db->get()->row_array();
                        if (!empty($paid_holiday_id)){
                            $items['paid_holiday_leave_id'] = $paid_holiday_id;
                            if (!empty($dtCheckPaidHolidayItem)){
                                $this->db->where('id',$dtCheckPaidHolidayItem['id']);
                                $this->db->update('tbl_paid_holiday_leave_detail', $items);
                                $paid_holiday_item_id = $dtCheckPaidHolidayItem['id'];
                            } else {
                                $this->db->insert('tbl_paid_holiday_leave_detail', $items);
                                $paid_holiday_item_id = $this->db->insert_id();
                                $date_start = explode('-',$items['date_start']);
                                $month = $date_start[1];
                                $this->db->insert('tbl_paid_holiday_leave_detail_month', [
                                    'paid_holiday_leave_detail_id' => $paid_holiday_item_id,
                                    'month' => $month,
                                    'number_day' => $items['number_date'],
                                    'suggest_paid_holiday_item_id' => $items['suggest_paid_holiday_item_id'],
                                ]);
                            }
                        }
                    }
                    $this->db->where('id', $paid_holiday_item_id);
                    $paidHoliday = $this->db->get('tbl_paid_holiday_leave_detail')->row_array();
                    $get_code = get_table_where('tbl_paid_holiday_leave',
                        array('id' => $paidHoliday['paid_holiday_leave_id']), '', 'row');
                    $this->db->select('
                    tbl_type_magic.name as name_magic,
                    date_start,
                    date_end,
                    tbl_type_magic.id as type_magic_id,
                    paid_holiday_leave_id,
                    tbl_paid_holiday_leave_detail.id as id
                    ');
                    $this->db->from('tbl_paid_holiday_leave_detail');
                    $this->db->join('tbl_type_magic', 'tbl_type_magic.id = tbl_paid_holiday_leave_detail.type_magic_id');
                    $this->db->where('tbl_paid_holiday_leave_detail.id', $paid_holiday_item_id);
                    $get_code_child = $this->db->get()->row_array();

                    $paidMonth = get_table_where('tbl_paid_holiday_leave_detail_month',['paid_holiday_leave_detail_id' => $get_code_child['id']],'','row_array');


                    $type = 'X';
                    $typeCheck = $get_code_child['type_magic_id'];
                    if ($typeCheck == 1){
                        if ($paidMonth['number_day'] == '0.5'){
                            $type = 'AL/2';
                        } else {
                            $type = 'AL';
                        }
                    } elseif ($typeCheck == 5){
                        if ($paidMonth['number_day'] == '0.5'){
                            $type = 'UP/2';
                        } else {
                            $type = 'UP';
                        }
                    } elseif ($typeCheck == 3){
                        $type = 'CH';
                    } elseif ($typeCheck == 4){
                        $type = 'TS';
                    } elseif ($typeCheck == 2){
                        $type = 'OD';
                    } elseif ($typeCheck == 6){
                        $type = 'F';
                    }
                    $paid_holiday_id = $get_code_child['paid_holiday_leave_id'];
                    $paid_holiday_detail_id = $get_code_child['id'];
                    $date_start = $get_code_child['date_start'];
                    $date_end = $get_code_child['date_end'];
                    $this->db->select('tbl_timekeeping_detail.id,tbl_timekeeping_detail.date,tbl_timekeeping_detail.staff_id,tbl_timekeeping.month,tbl_timekeeping.year');
                    $this->db->from('tbl_timekeeping_detail');
                    $this->db->join('tbl_timekeeping','tbl_timekeeping.id = tbl_timekeeping_detail.timekeeping_id');
                    $this->db->where('date >=',$date_start);
                    $this->db->where('date <=',$date_end);
                    $this->db->where('staff_id',$get_code->staff_id);
                    $timeDetails = $this->db->get()->result_array();
                    if (!empty($timeDetails)){
                        foreach ($timeDetails as $kk => $vv){
                            if ($status == 1) {
                                $this->db->where('id', $vv['id']);
                                $this->db->update('tbl_timekeeping_detail', [
                                    'type' => $type,
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'updated_by' => get_staff_user_id(),
                                    'paid_holiday_id' => $paid_holiday_id,
                                    'paid_holiday_detail_id' => $paid_holiday_detail_id,
                                ]);
                                notificationAgreePaidHoliday($paid_holiday_id,$paid_holiday_detail_id,get_staff_user_id(),1);
                            } elseif ($status == 2){
                                $this->db->where('id', $vv['id']);
                                $this->db->update('tbl_timekeeping_detail', [
                                    'type' => 'KP',
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'updated_by' => get_staff_user_id(),
                                    'paid_holiday_id' => $paid_holiday_id,
                                    'paid_holiday_detail_id' => $paid_holiday_detail_id,
                                ]);
                                notificationAgreePaidHoliday($paid_holiday_id,$paid_holiday_detail_id,get_staff_user_id(),2);
                            } else {
                                $this->db->where('id', $vv['id']);
                                $this->db->update('tbl_timekeeping_detail', [
                                    'type' => 'X',
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'updated_by' => get_staff_user_id(),
                                    'paid_holiday_id' => 0,
                                    'paid_holiday_detail_id' => 0,
                                ]);
                            }
                        }
                    }

                    activity_log_v2('status_paid_holiday_leave_child', 'tbl_paid_holiday_leave_detail', $paid_holiday_item_id,
                        $get_code->name,
                        'Duyệt đơn xin nghỉ phép [' . $get_code->name . '][' . $get_code_child['name_magic'] . ']');
                }
                echo json_encode([
                    'result' => $success,
                    'id' => $suggest_paid_holiday_id,
                    'message' => _l('cong_update_true')
                ]);
                die();
            }
        }
        echo json_encode([
            'result' => false,
            'message' => _l('cong_update_false')
        ]);
        die();
    }

    public function searchMachines($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_machines.id as id, 
            tbl_machines.name as text
        ', false);
        $this->db->from('tbl_machines');
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
            $dtMachines = get_table_where('tbl_machines',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
        }
        echo json_encode($data);
    }

    public function searchProductionReports($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblproduction_report.id as id, 
            tblproduction_report.name_report as text
        ', false);
        $this->db->from('tblproduction_report');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblproduction_report.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu báo cáo không phù hợp'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tblproduction_report',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name_report']];
        }
        echo json_encode($data);
    }

    public function searchSuppliers($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblsuppliers.id as id, 
            tblsuppliers.company as text
        ', false);
        $this->db->from('tblsuppliers');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Nhà cung cấp'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tblsuppliers',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['company']];
        }
        echo json_encode($data);
    }

    public function searchDeliveryRecords($id = 0){
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_delivery_records.id as id, 
            tbl_delivery_records.reference_no as text
        ', false);
        $this->db->from('tbl_delivery_records');
        if (!empty($term))
        {
            $this->db->group_start();
            $this->db->like('tbl_delivery_records.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Biên bản nghiệm thu'), 'children' => $pod];
        if (!empty($id)){
            $dtMachines = get_table_where('tbl_delivery_records',['id' => $id],'','row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['reference_no']];
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
                tbl_suggest_paid_holiday.id as id,
                tbl_suggest_paid_holiday.reference_no as reference_no,
                tbl_suggest_paid_holiday.date as date,
                tbl_suggest_paid_holiday.staff_id as staff_id,
                tbl_suggest_paid_holiday_item.note as note,
                tbl_type_magic.name as name_type_magic,
                tblroles.code_role as code_role,
                tbl_suggest_paid_holiday.regulations as regulations,
                tbl_suggest_paid_holiday.created_by as created_by,
                tbl_suggest_paid_holiday.staff_agree as staff_agree,
                tbl_suggest_paid_holiday_item.status as status,
                tblproduction_report.name_report as name_report,
                tbl_suggest_paid_holiday_item.number_date as number_date,
                tbl_suggest_paid_holiday_item.date_start as date_start,
                tbl_suggest_paid_holiday_item.day_work as day_work,
                tbl_suggest_paid_holiday.staff_reciever as staff_reciever,
            ');
            $this->db->from('tbl_suggest_paid_holiday');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbl_suggest_paid_holiday.staff_id', 'inner');
            $this->db->join('tblroles', 'tblroles.roleid = tblstaff.role', 'left');
            $this->db->join('tblproduction_report', 'tblproduction_report.id = tbl_suggest_paid_holiday.production_report_id', 'left');
            $this->db->join('tbl_suggest_paid_holiday_item','tbl_suggest_paid_holiday_item.suggest_paid_holiday_id = tbl_suggest_paid_holiday.id');
            $this->db->join('tbl_type_magic','tbl_type_magic.id = tbl_suggest_paid_holiday_item.type_magic');


            if (!$this->preViewSuggestPaidHolidays) {
                $this->db->where('tbl_suggest_paid_holiday.created_by = '.get_staff_user_id().'');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_paid_holiday.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_paid_holiday.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_paid_holiday.id desc');
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
                ('PHIẾU YÊU CẦU VẬT NGHỈ PHÉP'))->getStyle("A1")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'Mã Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Ngày Lập ĐX Phép')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Người Đề Xuất');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Lý Do Nghỉ');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Loại Phép');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Mã Vị Trí');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Số Ngày Đã Nghỉ')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Số Phép Còn Lại')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Quy Định')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Người Tạo(Trưởng Bộ Phân Tạo)')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Người Duyệt(Hành Chính Nhân Sự)')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Trạng Thái')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$sttRow.'', 'Báo Cáo Không Phù Hợp')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$sttRow.'', 'Tổng Số Ngày Đề Xuất')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$sttRow.'', 'Ngày Bắt Đầu Nghỉ')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$sttRow.'', 'Này Đi Làm Lại')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$sttRow.'', 'Người Tiếp Nhận Tạm Thời')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$sttRow.'', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:R$sttRow")->applyFromArray([
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
            $rowBegin = $sttRow;
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("C$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['note'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['name_type_magic']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['code_role']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", '')->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",'')->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $value['regulations'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", get_staff_full_name($value['created_by']))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", get_staff_full_name($value['staff_agree']))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    if ($value['status'] == 0){
                        $htmlStatus = 'Chưa Duyệt';
                    } elseif ($value['status'] == 1){
                        $htmlStatus = 'Đã Duyệt';
                    } elseif ($value['status'] == 2){
                        $htmlStatus = 'Không Duyệt';
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $htmlStatus)->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['name_report']))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin",($value['number_date']))->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", _dhau($value['date_start']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", _dhau($value['day_work']))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", get_staff_full_name($value['staff_reciever']))->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", '');

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:R$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("N$rowBegin:N$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_nghi_phep') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(30);
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
}