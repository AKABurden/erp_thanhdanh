<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quota_bonus_discipline extends AdminController
{
    public function __construct()
    {
        parent::__construct();


        $this->type = $this->input->get('type');
        if ($this->type == 1){
            $this->preViewQuotaBonusDeiscipline = has_permission('quota_bonus','','view');
            $this->preEditQuotaBonusDeiscipline = has_permission('quota_bonus','','edit');
        } else {
            $this->preViewQuotaBonusDeiscipline = has_permission('quota_discipline','','view');
            $this->preEditQuotaBonusDeiscipline = has_permission('quota_discipline','','edit');
        }
    }

    public function index()
    {
        if (!$this->preViewQuotaBonusDeiscipline) {
            access_denied();
        }
        if ($this->type != 1 && $this->type != 2) {
            access_denied();
        }
        if ($this->type == 1) {
            $data['title'] = lang('dt_quota_bonus_discipline_kt');
            $data['text_title'] = 'khen thưởng';
        } else {
            $data['title'] = lang('dt_quota_bonus_discipline_kl');
            $data['text_title'] = 'kỷ luật';
        }
        $data['type'] = $this->type;
        $data['dtPrecious'] = get_table_where('tbl_precious');
        $this->load->view('admin/quota_bonus_discipline/index', $data);
    }

    public function loadDataQuotaBonusDisciplines()
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $type = $this->input->post('type');
        // $type_bonus_kt = type_bonus(1);
        // $type_bonus_kl = type_bonus(2);
        $aColumns = [
            'tbl_quota_bonus_discipline.id as id',
            'tbl_quota_bonus_discipline.code as code',
            'tbl_quota_bonus_discipline.name as name',
            'tbl_quota_bonus_discipline.subjects as subjects',
            'tbl_quota_bonus_discipline.type_of_reward as type_of_reward',
            'tbl_quota_bonus_discipline.point as point',
            'tbl_quota_bonus_discipline.recipe as recipe',
            'tbl_quota_bonus_discipline.times_of_reward as times_of_reward',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_quota_bonus_discipline';
        $where = [' AND tbl_quota_bonus_discipline.type = ' . $type];
        $filter = [];

        $join = [
            'INNER JOIN tbl_type_bonus_discipline ON tbl_type_bonus_discipline.id = tbl_quota_bonus_discipline.type'
        ];


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_quota_bonus_discipline.type as type,tbl_type_bonus_discipline.name as name_type,tbl_quota_bonus_discipline.default_new'
        ], 'ORDER BY tbl_quota_bonus_discipline.type asc,tbl_quota_bonus_discipline.id asc', []);

        // Applicable Subjects
        $output = $result['output'];
        $rResult = $result['rResult'];
        $name_type = '';
        $stt = 0;
        $dtPrecious = get_table_where('tbl_precious', array('id' => 1));
        $dtType = get_table_where('tbl_type_bonus_discipline');
        $dtQuotaPre = get_table_where('tbl_quota_precious');
        $arrQuotaPre = [];
        if (!empty($dtQuotaPre)) {
            foreach ($dtQuotaPre as $kk => $vv) {
                $arrQuotaPre[$vv['quota_id']][$vv['precious_id']] = $vv;
            }
        }
        foreach ($rResult as $key => $aRow) {
            // if ($aRow['type'] == 1) {
            //     $type_bonus = $type_bonus_kt;
            // } elseif ($aRow['type'] == 2) {
            //     $type_bonus = $type_bonus_kl;
            // }
            $quota_id = $aRow['id'];
            $optionType = '';
            foreach ($dtType as $kk => $vv) {
                $optionType .= '<option ' . ($aRow['type'] == $vv['id'] ? 'selected' : '') . ' value="' . $vv['id'] . '">' . $vv['name'] . '</option>';
            }
            // if ($aRow['name_type'] != $name_type) {
            //     $row = array(
            //         '',
            //         '',
            //         $aRow['name_type'],
            //         '',
            //         '',
            //         '',
            //         '',
            //         '',
            //         '',
            //     );
            //     $name_type = $aRow['name_type'];
            //     $row['DT_RowClass'] = 'alert-header bold danger';
            //     $output['aaData'][] = $row;
            //     $stt = 0;
            // }
            $stt++;
            $row = array();
            $readonly = '';
            if (!empty($aRow['default_new'])) {
                $readonly = 'readonly';
            }
            $row[] = '<div class="text-center">' . $stt . '</div>';
            $row[] = '<div><input style="width: 100%" type="text" class="form-control code" onchange="updateQuota(this,' . $quota_id . ',\'code\')" value="' . (!empty($aRow['code']) ? $aRow['code'] : '') . '"></div>';

            $row[] = '<div><input ' . $readonly . ' style="width: 100%" type="text" class="form-control name" onchange="updateQuota(this,' . $quota_id . ',\'name\')" value="' . (!empty($aRow['name']) ? $aRow['name'] : '') . '"></div>';

            $row[] = '<div><input style="width: 100%" type="text" class="form-control subjects" onchange="updateQuota(this,' . $quota_id . ',\'subjects\')" value="' . (!empty($aRow['subjects']) ? $aRow['subjects'] : '') . '"></div>';
            $row[] = '<div><input style="width: 100%" type="text" class="form-control type_of_reward" onchange="updateQuota(this,' . $quota_id . ',\'type_of_reward\')" value="' . (!empty($aRow['type_of_reward']) ? $aRow['type_of_reward'] : '') . '"></div>';
            $row[] = '<div><input style="width: 100%" type="text" class="form-control point" onchange="updateQuota(this,' . $quota_id . ',\'point\')" value="' . (!empty($aRow['point']) ? $aRow['point'] : '') . '"></div>';
            $totalValue = 0;
            foreach ($dtPrecious as  $kk => $vv) {
                $form = !empty($arrQuotaPre[$quota_id][$vv['id']]['form']) ? $arrQuotaPre[$quota_id][$vv['id']]['form'] : null;
                $value = !empty($arrQuotaPre[$quota_id][$vv['id']]['value']) ? ($arrQuotaPre[$quota_id][$vv['id']]['value']) : 0;
                $totalValue += $value;
                $row[] = '<div style="width: 200px">
                <textarea id="form" class="form-control form" onchange="updateQuotaNew(this,' . $quota_id . ',' . $vv['id'] . ',\'form\')" cols="3" rows="4">' . $form . '</textarea></div>';
                if (!empty($aRow['default_new'])) {
                    $row[] = '<div  style="width: 100%"><input ' . $readonly . ' style="width: 100%" type="text" class="form-control value" value="Công thức"></div>';
                } else {
                    $row[] = '<div  style="width: 100%"><input ' . $readonly . ' style="width: 100%" type="text" class="form-control value" onchange="updateQuotaNew(this,' . $quota_id . ',' . $vv['id'] . ',\'value\')" value="' . formatMoney($value) . '"></div>';
                }
            }
            $row[] = '<div class="">' . $aRow['recipe'] . '</div>';

            $row[] = '<div><input style="width: 100%" type="text" class="form-control times_of_reward" onchange="updateQuota(this,' . $quota_id . ',\'times_of_reward\')" value="' . (!empty($aRow['times_of_reward']) ? $aRow['times_of_reward'] : '') . '"></div>';
            // $row[] = '<div class="text-left">
            //     <select class="type form-control"  onchange="updateQuota(this,' . $quota_id . ',\'type\')" style="width: 100%" name="type">
            //            ' . $optionType . '
            //     </select>
            // </div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }

    public function detail()
    {
        $data = [];
        $dtPrecious = get_table_where('tbl_precious');
        if ($this->input->post()) {
            $this->form_validation->set_rules('name', lang("name"), 'required');
            if ($this->form_validation->run() == true) {
                $dataPost = $this->input->post();
                $name = $dataPost['name'];
                $type = $dataPost['type'];
                $arrQuotaPre = [];
                foreach ($dtPrecious as $kk => $vv) {
                    $form = !empty($dataPost['form_' . $vv['id']]) ? $dataPost['form_' . $vv['id']] : null;
                    $value = !empty($dataPost['value_' . $vv['id']]) ? number_unformat($dataPost['value_' . $vv['id']]) : 0;
                    $arrQuotaPre[] = [
                        'precious_id' => $vv['id'],
                        'form' => $form,
                        'value' => $value,
                    ];
                }

                $this->db->insert('tbl_quota_bonus_discipline', [
                    'name' => $name,
                    'type' => $type,
                ]);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    if (!empty($arrQuotaPre)) {
                        foreach ($arrQuotaPre as $key => $value) {
                            $value['quota_id'] = $insert_id;
                            $this->db->insert('tbl_quota_precious', $value);
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
        }
        $data['title'] = lang('Thêm mới định mức');
        $dtType = get_table_where('tbl_type_bonus_discipline');
        $data['dtType'] = $dtType;
        $data['dtPrecious'] = $dtPrecious;
        $this->load->view('admin/quota_bonus_discipline/detail', $data);
    }

    public function updateQuota()
    {
        $data = [];
        if (!$this->preEditQuotaBonusDeiscipline) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($name == 'value_q1' || $name == 'value_q2' || $name == 'value_q3' || $name == 'value_q4') {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_quota_bonus_discipline');
        $this->db->where('tbl_quota_bonus_discipline.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('Không tồn tại');
            echo json_encode($data);
            die();
        }
        if ($name != 'code') {
            $this->db->where('tbl_quota_bonus_discipline.id', $dtData['id']);
            $success = $this->db->update('tbl_quota_bonus_discipline', [
                $name => $value
            ]);
        } else {
            $this->db->from('tbl_quota_bonus_discipline');
            $this->db->where('tbl_quota_bonus_discipline.id !=', $id);
            $this->db->where('tbl_quota_bonus_discipline.type', $dtData['type']);
            $this->db->where('tbl_quota_bonus_discipline.code', $value);
            $dtCheckData = $this->db->get()->row_array();
            if (empty($dtCheckData)) {
                $this->db->where('tbl_quota_bonus_discipline.id', $dtData['id']);
                $success = $this->db->update('tbl_quota_bonus_discipline', [
                    $name => $value
                ]);
            } else {
                $data['result'] = 0;
                $data['message'] = lang('Mã trùng với định mức khác');
                echo json_encode($data);
                die;
            }
        }
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }

    public function updateQuotaNew()
    {
        $data = [];
        if (!$this->preEditQuotaBonusDeiscipline) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $id = $this->input->post('id');
        $precious_id = $this->input->post('precious_id');
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        if ($name == 'value') {
            if (!empty($value)) {
                $value = number_unformat($value);
            } else {
                $value = 0;
            }
        }

        $this->db->from('tbl_quota_precious');
        $this->db->where('tbl_quota_precious.quota_id', $id);
        $this->db->where('tbl_quota_precious.precious_id', $precious_id);
        $dtData = $this->db->get()->row_array();
        if (!empty($dtData)) {
            $this->db->where('tbl_quota_precious.id', $dtData['id']);
            $success = $this->db->update('tbl_quota_precious', [
                $name => $value,
            ]);
        } else {
            $success = $this->db->insert('tbl_quota_precious', [
                $name => $value,
                'precious_id' => $precious_id,
                'quota_id' => $id,
            ]);
        }
        if ($success) {
            $data['result'] = 1;
            $data['message'] = lang('Thành công');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Thất bại');
        }
        echo json_encode($data);
    }
    public function modal_excel_import($type = 1)
    {
        if (!$this->preEditQuotaBonusDeiscipline){
            accessDenied(true);
        }
        $data['type'] = $type;
        $data['title'] = _l('Import định mức khen thưởng  bằng File Excel');
        $this->load->view('admin/quota_bonus_discipline/excel_import', $data);
    }
    public function excel_import()
    {
        ob_end_clean();
        ini_set('max_execution_time', 800);
        $dataPost = $this->input->post();
        $type = $dataPost['type'];
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $count = 0;
        $errors = '';
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

            $allSheetName = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            // $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('W');
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('K');
            $arraydata = array();

            $fields = $this->input->post('fields');
            for ($row = 3; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $arraydata[$row - 4][$col] = $value;
                }
            }

            $keyCode = '';
            $list_data = [];
            foreach ($arraydata as $key => $row) {
                if (empty($row[1])) {
                    $errors .= 'Dòng[' . $key . '] Không tìm thấy mã';
                    break;
                }
                $list_data[] = [
                    'code' => $row[1],
                    'name' => $row[2],
                    'subjects' => $row[3],
                    'type_of_reward' => $row[4],
                    'point' => $row[5],
                    'price' => $row[6],
                    'name_detail' => $row[7],
                    'times_of_reward' => $row[8],
                ];
            }
            $dataUpdateParent = [];
            $dataUpdateChild = [];
            $dataInsertChild = [];
            $count_insert = 0;
            $count_update = 0;
            $type_bonus_kt = type_bonus(1);
            $type_bonus_kl = type_bonus(2);

            foreach ($list_data as $key => $value) {

                $this->db->where('code', $value['code']);
                $this->db->where('type', $type);
                $quota_bonus_discipline = $this->db->get('tbl_quota_bonus_discipline')->row();
                if (!empty($quota_bonus_discipline)) {
                    $value['id'] = $quota_bonus_discipline->id;
                    if ($quota_bonus_discipline->default_new == 1) {
                        $items = [
                            'form' => $value['name_detail']
                        ];
                        $dataUpdateChild = [
                            'point' => $value['point'],
                            'type_of_reward' => $value['type_of_reward'],
                            'subjects' => $value['subjects'],
                            'times_of_reward' => $value['times_of_reward'],
                        ];
                        $this->db->where('id', $quota_bonus_discipline->id);
                        $this->db->update('tbl_quota_bonus_discipline', $dataUpdateChild);
                        $this->db->where('quota_id', $quota_bonus_discipline->id);
                        $this->db->where('precious_id', 1);
                        $this->db->update('tbl_quota_precious', $items);
                        $count_update++;
                    } else {
                        $items = [
                            'form' => $value['name_detail'],
                            'value' => $value['price']
                        ];
                        $dataUpdateChild = [
                            'name' => $value['name'],
                            'point' => $value['point'],
                            'type_of_reward' => $value['type_of_reward'],
                            'subjects' => $value['subjects'],
                            'times_of_reward' => $value['times_of_reward'],
                        ];
                        $this->db->where('id', $quota_bonus_discipline->id);
                        $this->db->update('tbl_quota_bonus_discipline', $dataUpdateChild);
                        $this->db->where('quota_id', $quota_bonus_discipline->id);
                        $this->db->where('precious_id', 1);
                        $this->db->update('tbl_quota_precious', $items);
                        $count_update++;
                    }
                } else {
                    // if (in_array($value['code'], $type_bonus)) {
                    //     $dataInsertChild = [
                    //         'code' => $value['code'],
                    //         'type' => $type,
                    //         'name' => $value['name'],
                    //         'point' => $value['point'],
                    //         'type_of_reward' => $value['type_of_reward'],
                    //         'subjects' => $value['subjects'],
                    //         'times_of_reward' => $value['times_of_reward'],
                    //     ];
                    //     $this->db->insert('tbl_quota_bonus_discipline', $dataInsertChild);
                    //     $insert_id = $this->db->insert_id();
                    //     $items = [
                    //         'quota_id' => $insert_id,
                    //         'precious_id' => 1,
                    //         'form' => $value['name_detail'],
                    //         'value' => 0
                    //     ];
                    //     $this->db->insert('tbl_quota_precious', $items);
                    // } else {
                    $dataInsertChild = [
                        'code' => $value['code'],
                        'type' => $type,
                        'name' => $value['name'],
                        'point' => $value['point'],
                        'type_of_reward' => $value['type_of_reward'],
                        'subjects' => $value['subjects'],
                        'times_of_reward' => $value['times_of_reward'],
                    ];
                    $this->db->insert('tbl_quota_bonus_discipline', $dataInsertChild);
                    $insert_id = $this->db->insert_id();
                    $items = [
                        'quota_id' => $insert_id,
                        'precious_id' => 1,
                        'form' => $value['name_detail'],
                        'value' => $value['price']
                    ];
                    $this->db->insert('tbl_quota_precious', $items);
                    // }
                    $count_update++;
                }
            }

            $viewSuccess = [];
            if (!empty($count_update)) {
                $viewSuccess[] = "Thêm mới " . $count_update . " mục ";
            }
            if (!empty($count_update)) {
                $viewSuccess[] = "Thêm mới " . $count_update . " mục ";
            }

            if (empty($viewSuccess)) {
                $viewSuccess[] = " Không có dữ liệu được thay đổi";
            }

            echo json_encode(
                [
                    'success' => true,
                    'errors' => $errors,
                    'alert_type' => 'success',
                    'message' => implode('Và ', $viewSuccess),
                ]
            );
            die();
        }
        echo json_encode([
            'success' => true,
            'errors' => $errors,
            'alert_type' => 'success',
            'message' => 'Import thành công ' . $count . ' dòng',
        ]);
        die();
    }
}
