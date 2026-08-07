<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Probationary_assessment extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewProbationaryAssessment = true;
        $this->preAddProbationaryAssessment = true;
        $this->preEditProbationaryAssessment = true;
        $this->preDeleteProbationaryAssessment = true;
        $this->type = 1;
        if (isset($_GET['type'])) {
            $this->type = $_GET['type'];
        }
    }

    public function index()
    {
        if (!$this->preViewProbationaryAssessment) {
            access_denied('probationary_assessment');
        }
        if ($this->type == 1) {
            $data['title'] = _l('Phiếu đánh giá nhân viên (TV)');
        } else {
            $data['title'] = _l('Phiếu đánh giá nhân viên (CT)');
        }
        $this->db->from('tbl_checklist_probationary_assessment');
        $dtChecklist = $this->db->get()->result_array();
        $checkList = [];
        foreach ($dtChecklist as $row) {
            $checkList[$row['type']][] = $row;
        }
        $data['checkList'] = $checkList;
        $data['levelChecklist'] = get_table_where('tbl_level_checklist');
        $data['resultChecklist'] = get_table_where('tbl_result_checklist');
        $data['dtRoom'] = get_table_where('tbl_room');
        $data['type'] = $this->type;
        $this->load->view('admin/probationary_assessment/index', $data);
    }

    public function getProbationaryAssessment()
    {
        $status_table = $this->input->post('status_table');
        $type = $this->input->post('type') ?? 1;
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $aColumns = [
            'tbl_probationary_assessment.id as id',
            'tbl_probationary_assessment.code as code',
            'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as staff',
            'tblroles.name as code_role',
            'tbl_room.name as name_room',
            'tbl_probationary_assessment.date_start as date_start',
            'tbl_probationary_assessment.date_end as date_end',
            'tbl_probationary_assessment.point as point',
            'tbl_probationary_assessment.rating as rating',
            'tbl_probationary_assessment.note as note',
            'tbl_result_checklist.color as color',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_probationary_assessment';
        $where = [];
        $filter = [];
        $join = [
            'LEFT JOIN tblstaff ON tblstaff.staffid = tbl_probationary_assessment.staff_id',
            'LEFT JOIN tblroles ON tblroles.roleid = tbl_probationary_assessment.role_id',
            'LEFT JOIN tbl_room ON tbl_room.id = tblroles.id_room',
            'LEFT JOIN tbl_result_checklist ON tbl_result_checklist.id = tbl_probationary_assessment.rating_list',
        ];

        if (!empty($role_id_search)) {
            $where[] = 'AND tbl_probationary_assessment.role_id = ' . $role_id_search . '';
        }

        if ($status_table != 'all') {
            $where[] = 'AND tbl_room.id = ' . $status_table . '';
        }

        $where[] = 'AND tbl_probationary_assessment.type = ' . $type . '';

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_result_checklist.color',
            'tbl_probationary_assessment.type'
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-center"><a target="_blank" href="' . base_url('admin/probationary_assessment/detail/' . $aRow['id'] . '') . '?type=' . $aRow['type'] . '">' . $aRow['code'] . '</a></div>';
            $row[] = '<div class="text-center">' . $aRow['staff'] . '</div>';
            $row[] = '<div class="text-center">' . $aRow['code_role'] . '</div>';
            $row[] = '<div class="text-left">' . $aRow['name_room'] . '</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['date_start']) ? _dhau($aRow['date_start']) : '') . '</div>';
            $row[] = '<div class="text-center">' . (!empty($aRow['date_end']) ? _dhau($aRow['date_end']) : '') . '</div>';
            $row[] = '<div class="text-center">' . $aRow['point'] . '</div>';
            $row[] = '<div class="text-left" style="color: white">' . ($aRow['rating']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['note']) . '</div>';
            $row[] = '<div class="text-left">' . ($aRow['color']) . '</div>';

            $print = '<a target="_blank" href="' . base_url('admin/probationary_assessment/print/' . $aRow['id'] . '?type=' . $type . '') . '"><i class="fa fa-print width-icon-actions"></i> ' . lang('In phiếu') . '</a>';
            $edit = '<a href="' . base_url('admin/probationary_assessment/detail/' . $aRow['id'] . '?type=' . $type . '') . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/probationary_assessment/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
            $actions = '
            <div class="dropdown text-center">
                <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                ' . lang('actions') . '
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 180px;">
                    <li>' . $print . '</li>
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
        if ($this->input->post()) {
            $dataPost = $this->input->post();
            $code = $dataPost['code'] ?? null;
            $staff_id = $dataPost['staff_id'] ?? 0;
            $level_target = $dataPost['level_target'] ?? 0;
            $level_achieved = $dataPost['level_achieved'] ?? 0;
            $rating_list = $dataPost['final_decision'] ?? 0;
            $date_start = $dataPost['date_start'] ?? null;
            $date_end = $dataPost['date_end'] ?? null;
            if (empty($code)) {
                $data['result'] = false;
                $data['message'] = lang('Vui lòng nhập mã phiếu');
                echo json_encode($data);
                die();
            }

            $this->db->where('code', $code);
            $this->db->where('id !=', $id);
            $this->db->from('tbl_probationary_assessment');
            $checkExists = $this->db->count_all_results();
            if (!empty($checkExists)) {
                $data['result'] = false;
                $data['message'] = lang('Mã phiếu đã tồn tại!');
                echo json_encode($data);
                die();
            }

            if (empty($staff_id)) {
                $data['result'] = false;
                $data['message'] = lang('Vui lòng chọn nhân viên');
                echo json_encode($data);
                die();
            }
            $this->db->from('tblstaff');
            $this->db->where('staffid', $staff_id);
            $dtStaff = $this->db->get()->row_array();
            if (empty($dtStaff)) {
                $data['result'] = false;
                $data['message'] = lang('Nhân viên không tồn tại!');
                echo json_encode($data);
                die();
            }

            $type = $dataPost['type'] ?? 1;
            $gate = $dataPost['gate'] ?? [];

            $point_b = 0;
            $point_c = 0;
            $point_d = 0;

            $arrItems = [];
            if (!empty($gate)) {
                foreach ($gate as $k => $v) {
                    $note = $dataPost['note_a'][$k] ?? null;
                    $arrItems[] = [
                        'type_check_list' => 'A',
                        'check_list_id' => $k,
                        'gate' => $v,
                        'note' => $note,
                    ];
                }
            }
            $percent_b = $dataPost['percent_b'] ?? [];
            if (!empty($percent_b)) {
                foreach ($percent_b as $k => $v) {
                    $point = !empty($dataPost['point_b'][$k]) ? $dataPost['point_b'][$k] : 0;
                    $point_b += $point;
                    $arrItems[] = [
                        'type_check_list' => 'B',
                        'check_list_id' => $k,
                        'percent' => ($v),
                        'point' => $point,
                    ];
                }
            }

            $percent_c = $dataPost['percent_c'] ?? [];
            if (!empty($percent_c)) {
                foreach ($percent_c as $k => $v) {
                    $point = !empty($dataPost['point_c'][$k]) ? $dataPost['point_c'][$k] : 0;
                    $point_c += $point;
                    $arrItems[] = [
                        'type_check_list' => 'C',
                        'check_list_id' => $k,
                        'percent' => ($v),
                        'point' => $point,
                    ];
                }
            }

            $point_d_post = $dataPost['point_d'] ?? [];
            if (!empty($point_d_post)) {
                foreach ($point_d_post as $k => $v) {
                    $point_d += !empty($v) ? $v : 0;
                    $arrItems[] = [
                        'type_check_list' => 'D',
                        'check_list_id' => $k,
                        'point' => ($v),
                    ];
                }
            }
            $point = (float)$point_b + (float)$point_c + (float)$point_d;

            // Recalculate rating if missing or to ensure consistency
            if (empty($rating_list)) {
                $this->db->from('tbl_result_checklist');
                $allRatings = $this->db->get()->result_array();
                
                // Check gate failure first
                $hasGateFail = false;
                if (!empty($gate)) {
                    foreach ($gate as $v) {
                        if ($v == '0') { $hasGateFail = true; break; }
                    }
                }

                if ($hasGateFail) {
                    foreach ($allRatings as $r) {
                        if (stripos($r['name'], 'chấm dứt') !== false || stripos($r['name'], 'không đạt') !== false || $r['id'] == 1) {
                            $rating_list = $r['id'];
                            break;
                        }
                    }
                } else {
                    foreach ($allRatings as $r) {
                        if ($point >= (float)$r['point_start'] && $point <= (float)$r['point_end']) {
                            $rating_list = $r['id'];
                            break;
                        }
                    }
                }
            }

            $dtRating = get_table_where('tbl_result_checklist', ['id' => $rating_list], '', 'row_array');

            if (empty($id)) {
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staff_id,
                    'role_id' => $dtStaff['role'] ?? 0,
                    'note' => null,
                    'level_target' => $level_target,
                    'level_achieved' => $level_achieved,
                    'date_start' => to_sql_date($date_start),
                    'date_end' => to_sql_date($date_end),
                    'point_b' => $point_b,
                    'point_c' => $point_c,
                    'point_d' => $point_d,
                    'point' => $point,
                    'type' => $type,
                    'rating_list' => $rating_list,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];
            } else {
                $option = [
                    'code' => $code,
                    'staff_id' => $staff_id,
                    'role_id' => $dtStaff['role'] ?? 0,
                    'note' => null,
                    'level_target' => $level_target,
                    'level_achieved' => $level_achieved,
                    'date_start' => to_sql_date($date_start),
                    'date_end' => to_sql_date($date_end),
                    'point_b' => $point_b,
                    'point_c' => $point_c,
                    'point_d' => $point_d,
                    'point' => $point,
                    'type' => $type,
                    'rating_list' => $rating_list,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                ];
            }

            if (empty($id)) {
                $this->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $this->db->insert_id();
            } else {
                $this->db->where('id', $id);
                $this->db->update('tbl_probationary_assessment', $option);
                $insert_id = $id;
            }
            if ($insert_id) {
                $this->db->where('tbl_probationary_assessment_item.probationary_assessment_id', $id);
                $this->db->delete('tbl_probationary_assessment_item');

                if (!empty($arrItems)) {
                    foreach ($arrItems as $k => $v) {
                        $v['probationary_assessment_id'] = $insert_id;
                        $this->db->insert('tbl_probationary_assessment_item', $v);
                    }
                }
                if ($type == 1) {
                    updateReference('probationary_assessment');
                } else {
                    updateReference('probationary_assessment_ct');
                }
                $data['result'] = true;
                if (empty($id)) {
                    $data['message'] = lang('Thêm mới thành công');
                } else {
                    $data['message'] = lang('Cập nhập thành công');
                }
                $data['type'] = $type;
            } else {
                $data['result'] = false;
                if (empty($id)) {
                    $data['message'] = lang('Thêm mới thất bại');
                } else {
                    $data['message'] = lang('Cập nhập thất bại');
                }
            }
            echo json_encode($data);
            die();
        }
        if (empty($id)) {
            if (!$this->preAddProbationaryAssessment) {
                accessDenied($js = true);
            }

            if ($this->type == 1) {
                $data['title'] = _l('Tạo mới phiếu đánh giá nhân viên (TV)');
            } else {
                $data['title'] = _l('Tạo mới phiếu đánh giá nhân viên (CT)');
            }
        } else {
            if (!$this->preEditProbationaryAssessment) {
                accessDenied($js = true);
            }
            if ($this->type == 1) {
                $data['title'] = _l('Chỉnh sửa phiếu đánh giá nhân viên (TV)');
            } else {
                $data['title'] = _l('Chỉnh sửa phiếu đánh giá nhân viên (CT)');
            }

            $this->db->select('tbl_probationary_assessment.*,tblroles.name as name_role,tbl_room.name as name_room');
            $this->db->from('tbl_probationary_assessment');
            $this->db->join('tblroles', 'tblroles.roleid = tbl_probationary_assessment.role_id', 'left');
            $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
            $this->db->where('tbl_probationary_assessment.id', $id);
            $dtData = $this->db->get()->row_array();

            $this->db->from('tbl_probationary_assessment_item');
            $this->db->where('probationary_assessment_id', $id);
            $dtDataItems = $this->db->get()->result_array();
            $checkListItems = [];
            foreach ($dtDataItems as $row) {
                $checkListItems[$row['type_check_list']][] = $row;
            }

            $mappedItems = [];

            foreach ($checkListItems as $type => $items) {
                foreach ($items as $item) {
                    $mappedItems[$type][$item['check_list_id']] = $item;
                }
            }
        }
        $this->db->from('tbl_checklist_probationary_assessment');
        $dtChecklist = $this->db->get()->result_array();
        $checkList = [];
        foreach ($dtChecklist as $row) {
            $checkList[$row['type']][] = $row;
        }
        $data['checkList'] = $checkList;
        $data['levelChecklist'] = get_table_where('tbl_level_checklist');
        $data['resultChecklist'] = get_table_where('tbl_result_checklist');
        $data['id'] = $id;
        $data['dtData'] = $dtData ?? null;
        $data['checkListItems'] = $mappedItems ?? null;
        $data['type'] = $this->type;
        if ($this->type == 1) {
            $code = getReference('probationary_assessment');
        } else {
            $code = getReference('probationary_assessment_ct');
        }
        $data['code'] = $code;
        $this->load->view('admin/probationary_assessment/detail', $data);
    }

    public function print($id)
    {
        $this->db->select('tbl_probationary_assessment.*,tblroles.name as name_role,tbl_room.name as name_room,tblstaff.firstname,tblstaff.lastname');
        $this->db->from('tbl_probationary_assessment');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_probationary_assessment.staff_id', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbl_probationary_assessment.role_id', 'left');
        $this->db->join('tbl_room', 'tbl_room.id = tblroles.id_room', 'left');
        $this->db->where('tbl_probationary_assessment.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->from('tbl_probationary_assessment_item');
        $this->db->where('probationary_assessment_id', $id);
        $items = $this->db->get()->result_array();

        $mappedItems = [];
        foreach ($items as $item) {
            $mappedItems[$item['type_check_list']][$item['check_list_id']] = $item;
        }

        $checkList = [];
        foreach (get_table_where('tbl_checklist_probationary_assessment') as $row) {
            $checkList[$row['type']][] = $row;
        }

        // ===== DATA CHO VIEW =====
        $data = [
            'dtData'          => $dtData,
            'checkList'       => $checkList,
            'mappedItems'     => $mappedItems,
            'levelChecklist'  => get_table_where('tbl_level_checklist'),
            'resultChecklist' => get_table_where('tbl_result_checklist'),
            'type' => $this->type
        ];

        // ===== RENDER HTML =====
        $html = $this->renderPrintHtml($data);

        // ===== TCPDF =====
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false, false, 'data', '', 'show', '');
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 45, 10);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('phieu_danh_gia_thu_viec.pdf', 'I');
    }

    private function renderPrintHtml($data)
    {
        return $this->load->view(
            'admin/probationary_assessment/print_html',
            $data,
            true
        );
    }

    public function delete($id)
    {
        if (!$this->preDeleteProbationaryAssessment) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_probationary_assessment.*,tblstaff.firstname,tblstaff.lastname');
        $this->db->from('tbl_probationary_assessment');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_probationary_assessment.staff_id');
        $this->db->where('tbl_probationary_assessment.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_probationary_assessment');
        if ($success) {

            $this->db->where('probationary_assessment_id', $id);
            $this->db->delete('tbl_probationary_assessment_item');


            insertActivityLog([
                'type_parent_obj' => 'probationary_assessment',
                'table_obj' => 'tbl_probationary_assessment',
                'id_obj' => $id,
                'name_obj' => $dtData['firstname'] . ' ' . $dtData['lastname'],
                'content' => lang('Xóa phiếu đánh giá nhân viên TV') . ' [' . $dtData['firstname'] . ' ' . $dtData['lastname'] . ']',
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
}
