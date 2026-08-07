<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Roadmap_advancement extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preViewAdvancement = true;
        $this->preAddAdvancement = true;
        $this->preEditAdvancement = true;
        $this->preDeleteAdvancement = true;
    }

    public function index()
    {
        if (!$this->preViewAdvancement) {
            access_denied('advancement');
        }
        $data['title'] = _l('Lộ trình thăng tiến');
        $data['dtRoom'] = get_table_where('tbl_room');
        $this->load->view('admin/advancement/index', $data);
    }

    public function getAdvancement()
    {
        $room_search = $this->input->post('room_search') ?? '';
        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');


        $aColumns = [
            'tbl_advancement.id as id',
            'tbl_advancement.code as code',
            'tbl_room.name as name_room',
            '"" position_from',
            '"" position_to',
            '"" role_from',
            '"" role_to',
            '"" as min_time_month',
            '"" as competency',
            '"" as kpi',
            'tbl_advancement.link_tranning as link_tranning',
            'tbl_advancement.note as note',
            '"" as actions',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_advancement';
        $where = [

        ];
        $filter = [];
        $join = [
            'INNER JOIN tbl_room ON tbl_room.id = tbl_advancement.room_id',
        ];

        if (!empty($room_search)){
            $where[] = 'AND tbl_advancement.room_id = '.$room_search.'';
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $arrId = [];
        foreach ($rResult as $key => $value){
            $arrId[] = $value['id'];
        }
        if (!empty($arrId)){
            $tb_item_child = "(
                SELECT 
                    GROUP_CONCAT(tbl_advancement_item_child.name) as name_child,
                    tbl_advancement_item_child.advancement_item_id
                FROM tbl_advancement_item_child
                WHERE tbl_advancement_item_child.type = 1
                AND tbl_advancement_item_child.advancement_id IN (".implode(',', $arrId).")
                GROUP BY tbl_advancement_item_child.advancement_item_id
            ) tb_item_child";

            $tb_item_child_kpi = "(
                SELECT 
                    GROUP_CONCAT(tbl_advancement_item_child.name) as name_child,
                    tbl_advancement_item_child.advancement_item_id
                FROM tbl_advancement_item_child
                WHERE tbl_advancement_item_child.type = 2
                AND tbl_advancement_item_child.advancement_id IN (".implode(',', $arrId).")
                GROUP BY tbl_advancement_item_child.advancement_item_id
            ) tb_item_child_kpi";

            $this->db->select([
                'tbl_advancement_item.*',
                'role_from.name_position as name_position_from',
                'role_to.name_position as name_position_to',
                'role_from.code_role as code_role_from',
                'role_to.code_role as code_role_to',
                'tb_item_child.name_child as name_child',
                'tb_item_child_kpi.name_child as name_child_kpi',
            ]);
            $this->db->where_in('advancement_id',$arrId);
            $this->db->from('tbl_advancement_item');
            $this->db->join('tblroles role_from','tbl_advancement_item.role_id_from = role_from.roleid','inner');
            $this->db->join('tblroles role_to','tbl_advancement_item.role_id_to = role_to.roleid','inner');
            $this->db->join($tb_item_child,'tb_item_child.advancement_item_id = tbl_advancement_item.id','left');
            $this->db->join($tb_item_child_kpi,'tb_item_child_kpi.advancement_item_id = tbl_advancement_item.id','left');
            $dtItems = $this->db->get()->result_array();
            $dtItems = array_reduce($dtItems, function ($acc, $item) {
                $acc[$item['advancement_id']][] = $item;
                return $acc;
            });
        }
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $items = $dtItems[$aRow['id']] ?? [];
            $row[] = '<div class="text-center">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 100px"><a href="' . base_url('admin/roadmap_advancement/view/' . $aRow['id']) . '">'.$aRow['code'].'</a></div>';
            $row[] = '<div class="text-left"  style="width: 120px">'.$aRow['name_room'].'</div>';
            $row[] = '<div></div>';
            $row[] = '<div></div>';
            $row[] = '<div></div>';
            $row[] = '<div></div>';
            $row[] = '<div class="text-center"></div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left"></div>';
            $row[] = '<div class="text-left">'.$aRow['link_tranning'].'</div>';
            $row[] = '<div class="text-left">'.($aRow['note']).'</div>';


            $view = '<a href="' . base_url('admin/roadmap_advancement/view/' . $aRow['id']) . '"><i class="fa fa-eye width-icon-actions"></i> ' . lang('Xem chi tiết') . '</a>';
            $edit = '<a href="' . base_url('admin/roadmap_advancement/detail/' . $aRow['id']) . '"><i class="fa fa-edit width-icon-actions"></i> ' . lang('Chỉnh sửa') . '</a>';
            $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/roadmap_advancement/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
                <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
            "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete')  . '</a>';
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
            $row['DT_RowClass'] = 'row-parent';
            $output['aaData'][] = $row;
            if (!empty($items)){
                foreach ($items as $item){
                    $row = [];
                    $row[] = '<div></div>';
                    $row[] = '<div></div>';
                    $row[] = '<div></div>';
                    $row[] = '<div>'.$item['name_position_from'].'</div>';
                    $row[] = '<div>'.$item['name_position_to'].'</div>';
                    $row[] = '<div>'.$item['code_role_from'].'</div>';
                    $row[] = '<div>'.$item['code_role_to'].'</div>';
                    $row[] = '<div class="text-center">'.$item['min_time_month'].' / Tháng</div>';
                    $row[] = '<div class="text-left">'.$item['name_child'].'</div>';
                    $row[] = '<div class="text-left">'.$item['name_child_kpi'].'</div>';
                    $row[] = '<div class="text-left"></div>';
                    $row[] = '<div class="text-left"></div>';
                    $row[] = '<div class="text-left"></div>';
                    $row['DT_RowClass'] = 'row-child';
                    $output['aaData'][] = $row;
                }
            }
        }
        echo json_encode($output);
    }

    public function detail($id = 0)
    {
        if ($this->input->post()){
            $this->form_validation->set_rules('code', lang("Mã lộ trình"), 'required');
            $this->form_validation->set_rules('room_id', lang("Phòng ban"), 'required');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $room_id = $this->input->post('room_id');
                $link_tranning = $this->input->post('link_tranning');
                $note = $this->input->post('note');

                $this->db->where('code',$code);
                $this->db->where('id !=',$id);
                $this->db->from('tbl_advancement');
                $checkExists = $this->db->count_all_results();
                if (!empty($checkExists)){
                    $data['result'] = false;
                    $data['message'] = lang('Mã lộ trình đã tồn tại');
                    echo json_encode($data);die();
                }

                $dataPost = $this->input->post();
                $view_detail = $this->input->post('view_detail') ?? 0;
                if (!empty($view_detail)){
                    $arrItem = [];
                    $advancement_item = $this->input->post('advancement_item') ?? [];
                    if (!empty($advancement_item)){
                        foreach ($advancement_item as $key => $value){
                            $name_child = $this->input->post('name_child')[$value] ?? [];
                            $name_child_kpi = $this->input->post('name_child_kpi')[$value] ?? [];
                            $arrItemChild = [];
                            if (!empty($name_child)){
                                foreach ($name_child as $k => $v){
                                    if (empty($v)){
                                        continue;
                                    }
                                    $advancement_item_child_id = $dataPost['advancement_item_child_id'][$value][$k] ?? 0;
                                    $type_child = $dataPost['type_child'][$value][$k] ?? 1;
                                    $arrItemChild[] = [
                                        'id' => $advancement_item_child_id,
                                        'name' => $v,
                                        'type' => $type_child,
                                    ];
                                }
                            }
                            $arrItemChildKpi = [];
                            if (!empty($name_child_kpi)){
                                foreach ($name_child_kpi as $k => $v){
                                    $advancement_item_child_kpi_id = $dataPost['advancement_item_child_kpi_id'][$value][$k] ?? 0;
                                    $type_child_kpi = $dataPost['type_child_kpi'][$value][$k] ?? 2;
                                    $arrItemChildKpi[] = [
                                        'id' => $advancement_item_child_kpi_id,
                                        'name' => $v,
                                        'type' => $type_child_kpi,
                                    ];
                                }
                            }
                            $arrItem[$value]['id'] = $value;
                            $arrItem[$value]['child'] = $arrItemChild;
                            $arrItem[$value]['child_kpi'] = $arrItemChildKpi;
                        }
                    }
                } else {
                    $counter = $this->input->post('counter') ?? [];
                    $arrItem = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $advancement_item_id = $dataPost['advancement_item_id'][$value] ?? 0;
                            $role_id_from = $dataPost['role_id_from'][$value] ?? 0;
                            $role_id_to = $dataPost['role_id_to'][$value] ?? 0;
                            $min_time_month = $dataPost['min_time_month'][$value] ?? 0;
                            if (empty($role_id_from) || empty($role_id_to)) {
                                continue;
                            }
                            $arrItem[] = [
                                'id' => $advancement_item_id,
                                'role_id_from' => $role_id_from,
                                'role_id_to' => $role_id_to,
                                'min_time_month' => $min_time_month,
                            ];
                        }
                    }
                    if (empty($arrItem)) {
                        $data['result'] = false;
                        $data['message'] = lang('Không có thông tin chi tiết');
                        echo json_encode($data);
                        die();
                    }
                }
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'room_id' => $room_id,
                    'link_tranning' => $link_tranning,
                    'note' => $note,
                ];
                if (empty($id)){
                    $option['created_by'] = get_staff_user_id();
                    $option['date_created'] = date('Y-m-d H:i:s');
                }

                if (empty($id)) {
                    $this->db->insert('tbl_advancement', $option);
                    $advancement_id = $this->db->insert_id();
                } else {
                    $this->db->where('id',$id);
                    $this->db->update('tbl_advancement',$option);
                    $advancement_id = $id;
                }
                if ($advancement_id){
                    if (empty($view_detail)) {
                        $this->db->where('advancement_id', $advancement_id);
                        $this->db->delete('tbl_advancement_item');

                        $arrItemId = [];
                        foreach ($arrItem as $key => $value) {
                            $arrItemId[] = $value['id'];
                            $value['advancement_id'] = $advancement_id;
                            $this->db->insert('tbl_advancement_item', $value);
                        }
                        $this->db->where_not_in('advancement_item_id',$arrItemId);
                        $this->db->where('advancement_id',$advancement_id);
                        $this->db->delete('tbl_advancement_item_child');
                    } else {
                        foreach ($arrItem as $key => $value){
                            $this->db->where('tbl_advancement_item_child.advancement_item_id',$key);
                            $this->db->delete('tbl_advancement_item_child');
                            if (!empty($value['child'])){
                                foreach ($value['child'] as $k => $v){
                                    $v['advancement_item_id'] = $key;
                                    $v['advancement_id'] = $advancement_id;
                                    $this->db->insert('tbl_advancement_item_child', $v);
                                }
                            }
                            if (!empty($value['child_kpi'])){
                                foreach ($value['child_kpi'] as $k => $v){
                                    $v['advancement_item_id'] = $key;
                                    $v['advancement_id'] = $advancement_id;
                                    $this->db->insert('tbl_advancement_item_child', $v);
                                }
                            }
                        }
                    }
                    if (empty($id)){
                        $data['message'] = lang('Thêm thành công');
                    } else {
                        $data['message'] = lang('Chỉnh sửa thành công');
                    }
                    $data['result'] = 1;

                } else {
                    $data['result'] = 0;
                    if (empty($id)) {
                        $data['message'] = lang('Thêm thất bị');
                    } else {
                        $data['message'] = lang('Chỉnh sửa thất bại');
                    }
                }
                echo json_encode($data);die();

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);die();
        }
        if (empty($id)) {
            if (!$this->preAddAdvancement){
                access_denied('advancement');
            }
            $data['title'] = _l('Thêm lộ trình thăng tiến');
        } else {
            if (!$this->preEditAdvancement){
                access_denied('advancement');
            }
            $data['title'] = _l('Chỉnh sửa lộ trình thăng tiến');
            $dtData = get_table_where('tbl_advancement',['id' => $id],'','row_array');
        }
        $this->db->select([
            'tbl_advancement_item.*',
            'CONCAT(role_from.name_position,"(",role_from.code_role,")") as name_position_from',
            'CONCAT(role_to.name_position,"(",role_to.code_role,")") as name_position_to',
        ]);
        $this->db->from('tbl_advancement_item');
        $this->db->join('tblroles role_from','tbl_advancement_item.role_id_from = role_from.roleid','inner');
        $this->db->join('tblroles role_to','tbl_advancement_item.role_id_to = role_to.roleid','inner');
        $this->db->where('tbl_advancement_item.advancement_id',$id);
        $dtItems = $this->db->get()->result_array();
        $data['id'] = $id;
        $data['dtData'] = $dtData ?? null;
        $data['dtRoom'] = get_table_where('tbl_room');
        $data['dtItems'] = $dtItems ?? null;
        $this->load->view('admin/advancement/detail', $data);
    }

    public function delete($id)
    {
        if (!$this->preDeleteAdvancement){
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die();
        }
        $data = [];
        $this->db->select('tbl_advancement.*');
        $this->db->from('tbl_advancement');
        $this->db->where('tbl_advancement.id',$id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);die();
        }

        $this->db->where('id',$id);
        $success = $this->db->delete('tbl_advancement');

        $this->db->where('advancement_id',$id);
        $this->db->from('tbl_advancement_item');
        $dtDataItem = $this->db->get()->result_array();
        if ($success){

            $this->db->where('advancement_id',$id);
            $this->db->delete('tbl_advancement_item');

            if (!empty($dtDataItem)){
                foreach ($dtDataItem as $key => $value){
                    $this->db->where('advancement_item_id',$value['id']);
                    $this->db->delete('tbl_advancement_item_child');
                }
            }

            insertActivityLog([
                'type_parent_obj' => 'advancement',
                'table_obj' => 'tbl_advancement',
                'id_obj' => $id,
                'name_obj' => $dtData['code'],
                'content' => lang('Xóa lộ trình thăng tiến') . ' [' . $dtData['code'] . ']',
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

    public function view($id)
    {
        $title = lang('Xem lộ trình thăng tiến');
        $dtData = get_table_where('tbl_advancement',['id' => $id],'','row_array');
        $this->db->select([
            'tbl_advancement_item.*',
            'CONCAT(role_from.name_position,"(",role_from.code_role,")") as name_position_from',
            'CONCAT(role_to.name_position,"(",role_to.code_role,")") as name_position_to',
        ]);
        $this->db->from('tbl_advancement_item');
        $this->db->join('tblroles role_from','tbl_advancement_item.role_id_from = role_from.roleid','inner');
        $this->db->join('tblroles role_to','tbl_advancement_item.role_id_to = role_to.roleid','inner');
        $this->db->where('tbl_advancement_item.advancement_id',$id);
        $dtItems = $this->db->get()->result_array();
        $arrItemId = array_column($dtItems,'id');

        if (!empty($arrItemId)){
            $this->db->where_in('advancement_item_id',$arrItemId);
            $this->db->where('type',1);
            $dtItemsChild = $this->db->get('tbl_advancement_item_child')->result_array();
            $dtItemsChild = array_reduce($dtItemsChild, function ($carry, $item) {
                $carry[$item['advancement_item_id']][] = $item;
                return $carry;
            }, []);

            $this->db->where_in('advancement_item_id',$arrItemId);
            $this->db->where('type',2);
            $dtItemsChildKpi = $this->db->get('tbl_advancement_item_child')->result_array();
            $dtItemsChildKpi = array_reduce($dtItemsChildKpi, function ($carry, $item) {
                $carry[$item['advancement_item_id']][] = $item;
                return $carry;
            }, []);
        }

        $data['id'] = $id;
        $data['dtData'] = $dtData ?? null;
        $data['dtRoom'] = get_table_where('tbl_room');
        $data['dtItems'] = $dtItems ?? null;
        $data['dtItemsChild'] = $dtItemsChild ?? null;
        $data['dtItemsChildKpi'] = $dtItemsChildKpi ?? null;
        $data['title'] = $title;
        $this->load->view('admin/advancement/view_detail', $data);
    }

    public function searchRoleByRoom()
    {
        $term = $this->input->get('term');
        $room_id = $this->input->get('room_id') ?? -1;
        if (empty($room_id)){
            $room_id = -1;
        }
        $limit = get_option('select2_limit');
        $this->db->select(['
            tblroles.roleid as id',
            'CONCAT(tblroles.name_position,"(",tblroles.code_role,")") as text',
        ], false);
        $this->db->from('tblroles');
        $this->db->where('tblroles.active_role',1);
        $this->db->where('tblroles.type',0);
        $this->db->where('tblroles.id_room',$room_id);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblroles.code_role', $term);
            $this->db->or_like('tblroles.name', $term);
            $this->db->or_like('tblroles.name_position', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();
        $data = [];
        $data['results'] = $results;
        echo json_encode($data);
    }

    public function exportExcel()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            $room_search = $this->input->post('room_search');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $staff_id = get_staff_user_id();

            $tb_item_child = "(
                SELECT 
                    GROUP_CONCAT(tbl_advancement_item_child.name) as name_child,
                    tbl_advancement_item_child.advancement_item_id
                FROM tbl_advancement_item_child
                WHERE tbl_advancement_item_child.type = 1
                GROUP BY tbl_advancement_item_child.advancement_item_id
            ) tb_item_child";

            $tb_item_child_kpi = "(
                SELECT 
                    GROUP_CONCAT(tbl_advancement_item_child.name) as name_child,
                    tbl_advancement_item_child.advancement_item_id
                FROM tbl_advancement_item_child
                WHERE tbl_advancement_item_child.type = 2
                GROUP BY tbl_advancement_item_child.advancement_item_id
            ) tb_item_child_kpi";

            $this->db->select('
                tbl_advancement.id as id,
                tbl_advancement.code as code,
                tbl_room.code as code_room,
                role_from.name_position as position_from,
                role_to.name_position as position_to,
                role_from.code_role as role_from,
                role_to.code_role as role_to,
                tbl_advancement_item.min_time_month as min_time_month,
                tb_item_child.name_child as competency,
                tb_item_child_kpi.name_child as kpi,
                tbl_advancement.link_tranning as link_tranning,
                tbl_advancement.note as note
            ');
            $this->db->from('tbl_advancement');
            $this->db->join('tbl_room','tbl_room.id = tbl_advancement.room_id','inner');
            $this->db->join('tbl_advancement_item','tbl_advancement_item.advancement_id = tbl_advancement.id','inner');
            $this->db->join('tblroles role_from','tbl_advancement_item.role_id_from = role_from.roleid','inner');
            $this->db->join('tblroles role_to','tbl_advancement_item.role_id_to = role_to.roleid','inner');
            $this->db->join($tb_item_child,'tb_item_child.advancement_item_id = tbl_advancement_item.id','left');
            $this->db->join($tb_item_child_kpi,'tb_item_child_kpi.advancement_item_id = tbl_advancement_item.id','left');


            if (!empty($room_search)) {
                $this->db->where("tbl_advancement.room_id = $room_search");
            }

            $this->db->order_by('tbl_advancement.id desc');
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

            insertCompanyInfo($objPHPExcel, 'C1:L2', 'A1');

            $objPHPExcel->getActiveSheet()->setCellValue('A5',
                ('PHIẾU LỘ TRÌNH THĂNG TIẾN'))->getStyle("A5")->applyFromArray([
                'font' => array(
                    'bold' => true,
                    'size' => 18,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            ]);
            $objPHPExcel->getActiveSheet()->mergeCells('A5:L5');
            $sttRow = 2 + 4;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$sttRow.'', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$sttRow.'', 'Mã lộ trình')->getStyle("B$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$sttRow.'', 'Mã phòng ban');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$sttRow.'', 'Từ vị trí');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$sttRow.'', 'Lên vị trí')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$sttRow.'', 'Vị trí từ')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$sttRow.'', 'Vị trí đến')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$sttRow.'', 'Thời gian tối thiếu (tháng)')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$sttRow.'', 'Điều kiện năng lực')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$sttRow.'', 'Điều kiện KPI')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$sttRow.'', 'Link đào tạo')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$sttRow.'', 'Ghi chú')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:L$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['code']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", ($value['code_room']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", $value['position_from'])->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", $value['position_to'])->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", $value['role_from'])->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin",$value['role_to'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin",$value['min_time_month'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin",$value['competency'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['kpi'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['link_tranning'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", $value['note'] )->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:L$rowBegin")->applyFromArray([
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
                }
            }
            $filename = lang('phieu_lo_trinh_thang_tien') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
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

    public function import()
    {
        $data = [];
        if (!empty($_FILES)){
            ini_set('max_execution_time', 800);
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
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('L');
                $arraydata = array();

                $fields = $this->input->post('fields');
                for ($row = 2; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                        $arraydata[$row - 2][$col] = $value;
                    }
                }
                $dataArray = [];
                $arrData = [];
                $count = 0;
                $index_parent = 0;
                $ref = '';
                $dataImport = [];
                foreach ($arraydata as $key => $value) {
                    $code = (preg_replace('/\s+/', ' ', trim($value[1])));
                    $code_room = (preg_replace('/\s+/', ' ', trim($value[2])));
                    $role_id_from = (preg_replace('/\s+/', ' ', trim($value[5])));
                    $role_id_to = (preg_replace('/\s+/', ' ', trim($value[6])));
                    $min_time_month = (preg_replace('/\s+/', ' ', trim($value[7])));
                    $competency = (preg_replace('/\s+/', ' ', trim($value[8])));
                    $kpi = (preg_replace('/\s+/', ' ', trim($value[9])));
                    $link_tranning = (preg_replace('/\s+/', ' ', trim($value[10])));
                    $note = (preg_replace('/\s+/', ' ', trim($value[11])));
                    if (!empty($code) && $code != $ref) {
                        $dataImport[$index_parent]['code'] = $code;
                        $dataImport[$index_parent]['code_room'] = $code_room;
                        $dataImport[$index_parent]['link_tranning'] = $link_tranning;
                        $dataImport[$index_parent]['note'] = $note;

                        $parent_current = $index_parent;
                        $ref = $code;
                        $index_parent++;
                    }

                    $dataImport[$parent_current]['items'][] = [
                        'role_id_from' => $role_id_from,
                        'role_id_to' => $role_id_to,
                        'min_time_month' => $min_time_month,
                        'competency' => $competency,
                        'kpi' => $kpi,
                    ];
                }
                if (!empty($dataImport)){
                    foreach ($dataImport as $key => $value){
                        $code = $value['code'];
                        $code_room = $value['code_room'];
                        $link_tranning = $value['link_tranning'];
                        $note = $value['note'];
                        $items = $value['items'];
                        $this->db->from('tbl_room');
                        $this->db->where('tbl_room.code',$code_room);
                        $dtRoom = $this->db->get()->row_array();
                        if (empty($dtRoom)){
                            $errors .= '<div>'.lang('Lộ trình '.$code.' không thêm được vì mã '.$code_room.' phòng ban không tồn tại').'</div>';
                            continue;
                        }
                        if (empty($items)){
                            $errors .= '<div>'.lang('Lộ trình '.$code.' không thêm được vì không có thông tin chi tiết').'</div>';
                            continue;
                        }
                        $arrItem = [];
                        foreach ($items as $k => $v) {
                            $role_id_from = $v['role_id_from'];
                            $role_id_to = $v['role_id_to'];
                            $min_time_month = $v['min_time_month'];
                            $competency = $v['competency'];
                            $kpi = $v['kpi'];
                            $this->db->from('tblroles');
                            $this->db->where('code_role',$role_id_from);
                            $dtRole = $this->db->get()->row_array();
                            if (empty($dtRole)){
                                $errors .= '<div>'.lang('Lộ trình '.$code.' mã '.$role_id_from.' vị trí từ không tồn tại').'</div>';
                                continue;
                            }
                            $this->db->from('tblroles');
                            $this->db->where('code_role',$role_id_to);
                            $dtRoleTo = $this->db->get()->row_array();
                            if (empty($dtRoleTo)){
                                $errors .= '<div>'.lang('Lộ trình '.$code.' mã '.$role_id_to.' vị trí đến không tồn tại').'</div>';
                                continue;
                            }

                            $this->db->from('tblroles');
                            $this->db->where('roleid',$dtRole['roleid']);
                            $this->db->where('id_room',$dtRoom['id']);
                            $dtRoleCheck = $this->db->get()->row_array();
                            if (empty($dtRoleCheck)){
                                $errors .= '<div>'.lang('Lộ trình '.$code.' mã '.$role_id_from.' vị trí từ không tồn tại trong phòng ban hiện tại').'</div>';
                                continue;
                            }

                            $this->db->from('tblroles');
                            $this->db->where('roleid',$dtRoleTo['roleid']);
                            $this->db->where('id_room',$dtRoom['id']);
                            $dtRoleCheck = $this->db->get()->row_array();
                            if (empty($dtRoleCheck)){
                                $errors .= '<div>'.lang('Lộ trình '.$code.' mã '.$role_id_from.' vị trí đến không tồn tại trong phòng ban hiện tại').'</div>';
                                continue;
                            }

                            $arrItemChild = [];
                            if (!empty($competency)){
                                $competency = explode(',',$competency);
                                if (!empty($competency)){
                                    foreach ($competency as $kk => $vv){
                                        $arrItemChild[] = [
                                            'name' => $vv,
                                            'type' => 1,
                                        ];
                                    }
                                }
                            }
                            $arrItemChildKpi = [];
                            if (!empty($kpi)){
                                $kpi = explode(',',$kpi);
                                if (!empty($kpi)){
                                    foreach ($kpi as $kk => $vv){
                                        $arrItemChildKpi[] = [
                                            'name' => $vv,
                                            'type' => 2,
                                        ];
                                    }
                                }
                            }
                            $arrItem[] = [
                                'role_id_from' => $dtRole['roleid'],
                                'role_id_to' => $dtRoleTo['roleid'],
                                'min_time_month' => $min_time_month,
                                'child' => $arrItemChild,
                                'child_kpi' => $arrItemChildKpi,
                            ];
                        }
                        if (empty($arrItem)){
                            $errors .= '<div>'.lang('Lộ trình '.$code.' không thêm được vì không có thông tin chi tiết').'</div>';
                            continue;
                        }
                        $this->db->where('tbl_advancement.code',$code);
                        $this->db->from('tbl_advancement');
                        $checkExist = $this->db->get()->row_array();
                        if (empty($checkExist)) {
                            $option = [
                                'date' => date('Y-m-d H:i:s'),
                                'code' => $code,
                                'room_id' => $dtRoom['id'],
                                'link_tranning' => $link_tranning,
                                'note' => $note,
                                'created_by' => get_staff_user_id(),
                                'date_created' => date('Y-m-d H:i:s')
                            ];
                        } else {
                            $option = [
                                'room_id' => $dtRoom['id'],
                                'link_tranning' => $link_tranning,
                                'note' => $note,
                            ];
                        }
                        if (empty($checkExist)) {
                            $this->db->insert('tbl_advancement', $option);
                            $advancement_id = $this->db->insert_id();
                        } else {
                            $this->db->where('tbl_advancement.id',$checkExist['id']);
                            $this->db->update('tbl_advancement',$option);
                            $advancement_id = $checkExist['id'];
                        }
                        if ($advancement_id){
                            $this->db->where('advancement_id',$advancement_id);
                            $this->db->delete('tbl_advancement_item');

                            $this->db->where('advancement_id',$advancement_id);
                            $this->db->delete('tbl_advancement_item_child');
                            if(!empty($arrItem)){
                                foreach ($arrItem as $kItem => $vItem){
                                    $child = $vItem['child'];
                                    $child_kpi = $vItem['child_kpi'];
                                    unset($vItem['child']);
                                    unset($vItem['child_kpi']);
                                    $vItem['advancement_id'] = $advancement_id;
                                    $this->db->insert('tbl_advancement_item',$vItem);
                                    $advancement_item_id = $this->db->insert_id();
                                    if (!empty($child)){
                                        foreach ($child as $kChild => $vChild){
                                            $child[$kChild]['advancement_item_id'] = $advancement_item_id;
                                            $child[$kChild]['advancement_id'] = $advancement_id;
                                        }
                                        $this->db->insert_batch('tbl_advancement_item_child',$child);
                                    }
                                    if (!empty($child_kpi)){
                                        foreach ($child_kpi as $kChild => $vChild){
                                            $child_kpi[$kChild]['advancement_item_id'] = $advancement_item_id;
                                            $child_kpi[$kChild]['advancement_id'] = $advancement_id;
                                        }
                                        $this->db->insert_batch('tbl_advancement_item_child',$child_kpi);
                                    }
                                }
                            }
                            $count ++;
                        }
                    }
                }
                echo json_encode(
                    [
                        'success' => true,
                        'errors' => $errors,
                        'alert_type' => 'success',
                        'message' => 'Thêm mới thành công ' . $count . ' lộ trình thăng tiến',
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
        $data['title'] = _l('Import lộ trình thăng tiến');
        $this->load->view('admin/advancement/import', $data);
    }
}