<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_outsource extends AdminController
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

        $this->preViewSuggestOutsource = true;
        $this->preViewOwnSuggestOutsource = true;
        $this->preAddSuggestOutsource = true;
        $this->preEditSuggestOutsource = true;
        $this->preApproveSuggestnOutsource = true;
        $this->preDeleteSuggestOutsource = true;

        $this->list_step = [
            [
                'id' => 1,
                'name' => 'Phiếu công việc'
            ],
            [
                'id' => 2,
                'name' => 'Bàn giao'
            ]
        ];
    }

    public function index()
    {
        if (!$this->preViewSuggestOutsource && !$this->preViewOwnSuggestOutsource) {
            access_denied();
        }
        $data['title'] = _l('dt_suggest_outsource');
        $this->load->view('admin/suggest_outsource/index', $data);
    }

    public function getSuggestOutsource()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');
        $aColumns = [
            'tbl_suggest_outsource.id as id',
            'tbl_suggest_outsource.reference_no as reference_no',
            'tbl_suggest_outsource.date as date',
            'tbl_suggest_outsource.object_type as object_type',
            'tbl_suggest_outsource.staff_plan as staff_plan',
            'tbl_suggest_outsource.status as status',
            'tbl_suggest_outsource.created_by as created_by',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_outsource';
        $where = [];
        $filter = [];

        $join = [];

        if (!$this->preViewSuggestOutsource) {
            array_push($where, 'AND (tbl_suggest_outsource.created_by = ' . get_staff_user_id() . ' OR tbl_suggest_outsource.staff_plan = ' . get_staff_user_id() . ' )');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_outsource.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_outsource.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_outsource.staff_status',
            'tbl_suggest_outsource.date_status',
        ], '', []);

        $output = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $key => $aRow) {
            $row = array();
//            $row[] = '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
            $row[] = '<div>'.($key + 1).'</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_outsource/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $htmlObjectType = '';
            if ($aRow['object_type'] == 'po') {
                $htmlObjectType = '<div class="label label-success">Lệnh sản xuất</div>';
            } else {
                $htmlObjectType = '<div class="label label-danger">Đơn hàng</div>';
            }
            $row[] = '<div class="text-left" style="width: 110px">' . $htmlObjectType . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_plan']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">' . $_data . '</div>';

//            $arrReport = $aRow['name_report'];
//            $htmlReport = '';
//            if (!empty($arrReport)) {
//                $arrReport = explode('||', $arrReport);
//                if (!empty($arrReport)) {
//                    foreach ($arrReport as $kk => $vv) {
//                        $vv = explode('__', $vv);
//                        $htmlReport .= '<a class="c_modal" href="' . (admin_url('production_report/modal/' . $vv[1])) . '">' . $vv[0] . '</a>';
//                    }
//                }
//            }
//            if ($aRow['status'] == 1) {
//                $row[] = '<div class="text-left"><a target="_blank" href="' . base_url('admin/production_report/detail?object_id=' . $aRow['id'] . '&object_type=suggest_outsource') . '" class="btn btn-info">Báo cáo không phù hợp</a></div>
//                <div style="margin-top: 5px">' . $htmlReport . '</div>
//            ';
//            } else {
//                $row[] = '';
//            }


            $row[] = '<div class="text-left" style="width: 130px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $row[] = $this->viewStep($aRow['id']);

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_outsource/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestOutsource ? '<a href="' . base_url('admin/suggest_outsource/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestOutsource ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_outsource/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div style="min-width: 120px">' . $actions . '</div>';

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }


    private function viewStep($id = 0) {
        $list_step = $this->db->order_by('step', 'asc')->get_where('tbl_suggest_outsource_step', ['id_suggest_outsource' => $id])->result_array();
        if(!empty($list_step)) {

            $this->db->where('name_table', 'tbl_suggest_outsource');
            $category_recommended = $this->db->get_where('tbl_category_recommended')->row();
            $liStep = [];
            $liName = [];
            $ActiveOne = false;
            foreach ($list_step as $key => $value) {
                $liStep[] = '<li class="initli" style="list-style-type: none;width: 110px;float: left;font-size: 12px;position: relative;text-align: center;color: #7d7d7d;z-index: 0;font-size: 9px;"></li>';
                $viewEventCreate = '';
                $classStep = '';
                $classEvent = '';
                if($value['step'] == 1) {
                    if(empty($value['status'])) {
                        $viewEventCreate = 'onclick="new_task(\''.admin_url('tasks/task?suggest_id='.$id.'&rel_append_id='.$value['step'].'&category_recommended_id=' . $category_recommended->id).'\')"';
                    }
                    else {
                        $classStep = 'active';
                        $viewEventCreate = 'onclick="init_task_modal('.$value['id_tasks'].'); return false;"';
                        $kttasks = $this->db->get_where('tbltasks', ['id' => $value['id_tasks']])->num_rows();
                        if(empty($kttasks)) {
                            $this->db->where('id', $value['id']);
                            $this->db->update('tbl_suggest_outsource_step', [
                                'id_tasks' => $value['id'],
                                'status' => 0
                            ]);
                            $viewEventCreate = 'onclick="new_task(\''.admin_url('tasks/task?suggest_id='.$id.'&rel_append_id='.$value['step'].'&category_recommended_id=' . $category_recommended->id).'\')"';
                            $classStep = '';
                        }
                    }
                }

                if($value['step'] == 1 && $value['status'] == 1) {
                    $ActiveOne = 1;
                }

                if($value['step'] == 2 && !empty($ActiveOne)) {
                    $classEvent = 'c_modal';
                    $viewEventCreate = 'href="'.admin_url('suggest_outsource/get_table_delivery_records/'.$id).'"';
                }
                $liName[] = '<li style="" class="pointer '.$classStep.'">
								'.$value['name'].'
								<div class="wrap-title-process" style="">
								<div class="text-center" style="font-size: 18px; cursor: pointer;">
								    <i style="" class="wrap-icon-check fa fa-check-circle-o '.$classEvent.'" '.$viewEventCreate.'></i></a></div>
                                </div>
							</li>';
            }
            $liName = implode('', $liName);
            $liStep = implode('', $liStep);
            $data = '<div class="display: table; justify-content: center;">
                        <ul class="progressbar" style="display: flex;">' . $liStep . '</ul>
                        <ul class="progressbar" style="display: flex;">' . $liName . '</ul>
                 </div>';
        }
        else {
            $data = '<b class="text-danger">Chưa thiết lập quy trình.</b>';
        }
        return $data;
    }

    public function detail($id = 0)
    {
        $data = [];
        $this->db->select('tbl_suggest_outsource.*');
        $this->db->from('tbl_suggest_outsource');
        $this->db->where('tbl_suggest_outsource.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($this->input->post()) {
            if (!empty($dtData) && $dtData['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_outsource.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('object_id', lang("Đơn hàng/ Lệnh sản xuất"), 'required');
            $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
            $this->form_validation->set_rules('staff_plan', lang("Người lập kế hoạch"), 'required');
            if (empty($id)) {
                if ($this->form_validation->run() == true) {
                    $reference_no = getReference('suggest_outsource');
                    $date = to_sql_date($this->input->post('date'), true);

                    $date_request = to_sql_date($this->input->post('date_request'), true);
                    $date_delivery = to_sql_date($this->input->post('date_delivery'), true);
                    $date_go_expected = to_sql_date($this->input->post('date_go_expected'), true);
                    $date_satisfied_expected = to_sql_date($this->input->post('date_satisfied_expected'), true);

                    $object_type = $this->input->post('object_type');
                    $object_id = $this->input->post('object_id');
                    $branch_id = $this->input->post('branch_id');
                    $staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = !empty($this->input->post('pod_id')[$value]) ? $this->input->post('pod_id')[$value] : 0;
                            $order_id = $this->input->post('order_id')[$value];
                            $productions_orders_id = $this->input->post('productions_orders_id')[$value];
                            $plan_id = $this->input->post('plan_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            if ($object_type == 'po') {
                                $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
                                $this->db->from('tbl_productions_orders_details');
                                $this->db->join(
                                    'tbl_productions_orders_items',
                                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id'
                                );
                                $this->db->where('tbl_productions_orders_details.id', $order_item_id);
                                $dtObject = $this->db->get()->row_array();
                                if (empty($dtObject)) {
                                    continue;
                                }
                            } else {
                                $this->db->select('tbl_order_items.item_id as items_id,tbl_order_items.type_item as type_items');
                                $this->db->from('tbl_order_items');
                                $this->db->where('tbl_order_items.id', $order_item_id);
                                $dtObject = $this->db->get()->row_array();
                                if (empty($dtObject)) {
                                    continue;
                                }
                            }
                            $item_id = $dtObject['items_id'];
                            $type_item = $dtObject['type_items'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $supplier_id = ($this->input->post('suppliers_id')[$value]);
                            $stage_id = ($this->input->post('stage_id')[$value]);
                            $time_expected = number_unformat($this->input->post('time_expected')[$value] ?? 0);
                            $date_start_outsource = @to_sql_date($this->input->post('date_start_outsource')[$value]);
                            $date_end_outsource = @to_sql_date($this->input->post('date_end_outsource')[$value]);
                            $price = number_unformat($this->input->post('price')[$value] ?? 0);
                            $price_transport = number_unformat($this->input->post('price_transport')[$value] ?? 0);
                            $shipping_unit_outsource = ($this->input->post('shipping_unit_outsource')[$value] ?? NULL);
                            $transport_outsource = ($this->input->post('transport_outsource')[$value]);
                            $staff_id = 0;
                            $result_id = 0;
                            $tax_id = !empty($this->input->post('tax_id')[$value]) ? $this->input->post('tax_id')[$value] : 0;

                            $sltin = !empty($this->input->post('sltin')[$value]) ? $this->input->post('sltin')[$value] : 0;
                            $material = !empty($this->input->post('material')[$value]) ? $this->input->post('material')[$value] : 0;
                            $quantity_compensation = !empty($this->input->post('quantity_compensation')[$value]) ? $this->input->post('quantity_compensation')[$value] : 0;
                            $quantity_compensation_more = !empty($this->input->post('quantity_compensation_more')[$value]) ? $this->input->post('quantity_compensation_more')[$value] : 0;
                            $landscape_print_size = !empty($this->input->post('landscape_print_size')[$value]) ? $this->input->post('landscape_print_size')[$value] : '';
                            $print = !empty($this->input->post('print')[$value]) ? $this->input->post('print')[$value] : 0;
                            $number_of_printed_sides = !empty($this->input->post('number_of_printed_sides')[$value]) ? $this->input->post('number_of_printed_sides')[$value] : 0;
                            $color_number_a = !empty($this->input->post('color_number_a')[$value]) ? $this->input->post('color_number_a')[$value] : 0;
                            $color_number_b = !empty($this->input->post('color_number_b')[$value]) ? $this->input->post('color_number_b')[$value] : 0;
                            $zinc_number_a = !empty($this->input->post('zinc_number_a')[$value]) ? $this->input->post('zinc_number_a')[$value] : 0;
                            $zinc_number_b = !empty($this->input->post('zinc_number_b')[$value]) ? $this->input->post('zinc_number_b')[$value] : 0;
                            $grape = !empty($this->input->post('grape')[$value]) ? $this->input->post('grape')[$value] : 0;
                            $note_items = !empty($this->input->post('note_items')[$value]) ? $this->input->post('note_items')[$value] : '';
                            $note_detail = !empty($this->input->post('note_detail')[$value]) ? $this->input->post('note_detail')[$value] : '';

                            $tax_rate = 0;
                            $total_tax = 0;
                            if (!empty($tax_id)) {
                                $info_tax = $this->site_model->rowTax($tax_id);
                                if (!empty($info_tax)) {
                                    $tax_rate = $info_tax['taxrate'];
                                }
                            }
                            $amount = $quantity * $price;
                            $amount_transport = $price_transport;
                            $amount += $amount_transport;
                            if ($tax_rate > 0) {
                                $total_tax = $amount * ($tax_rate / 100);
                            }
                            $grand_total = $amount + $total_tax;
                            $type_material = '';
                            $id_material = '';
                            if (!empty($material)) {
                                $material = explode('__', $material);
                                $id_material = $material[1];
                                $type_material = $material[0];
                            }
                            $items[] = [
                                'order_item_id' => $order_item_id,
                                'order_id' => $order_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'supplier_id' => $supplier_id,
                                'price' => $price,
                                'amount' => $amount,
                                'shipping_unit_outsource' => $shipping_unit_outsource,
                                'transport_outsource' => $transport_outsource,
                                'stage_id' => $stage_id,
                                'staff_id' => $staff_id,
                                'time_expected' => $time_expected,
                                'tax_id' => $tax_id,
                                'total_tax' => $total_tax,
                                'grand_total' => $grand_total,
                                'price_transport' => $price_transport,
                                'amount_transport' => $amount_transport,
                                'date_start_outsource' => $date_start_outsource,
                                'date_end_outsource' => $date_end_outsource,
                                'result_id' => $result_id,
                                'object_type' => $object_type,
                                'sltin' => $sltin,
                                'material' => $id_material,
                                'type_material' => $type_material,
                                'quantity_compensation' => $quantity_compensation,
                                'quantity_compensation_more' => $quantity_compensation_more,
                                'landscape_print_size' => $landscape_print_size,
                                'print' => $print,
                                'number_of_printed_sides' => $number_of_printed_sides,
                                'color_number_a' => $color_number_a,
                                'color_number_b' => $color_number_b,
                                'zinc_number_a' => $zinc_number_a,
                                'zinc_number_b' => $zinc_number_b,
                                'grape' => $grape,
                                'note' => $note_items,
                                'productions_orders_id' => $productions_orders_id,
                                'plan_id' => $plan_id,
                                'note_detail' => $note_detail,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'reference_no' => $reference_no,
                        'date' => $date,
                        'date_request' => $date_request,
                        'date_delivery' => $date_delivery,
                        'date_go_expected' => $date_go_expected,
                        'date_satisfied_expected' => $date_satisfied_expected,
                        'object_id' => $object_id,
                        'object_type' => $object_type,
                        'staff_plan' => $staff_plan,
                        'note' => $note,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_outsource', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if (getReference('suggest_outsource') == $reference_no) {
                            updateReference('suggest_outsource');
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_plan_outsource_id'] = $id;
                            $this->db->insert('tbl_suggest_outsource_item', $value);
                            $suggest_outsource_item = $this->db->insert_id();
                            $path        = 'uploads/suggest_outsource/mucin/' . $suggest_outsource_item . '/';
                            if (isset($_FILES['image_mucin']['name'][$key]) && $_FILES['image_mucin']['name'][$key] != '') {
                                $tmpFilePath = $_FILES['image_mucin']['tmp_name'][$key];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {

                                    $path_parts         = pathinfo($_FILES["image_mucin"]["name"][$key]);
                                    $extension          = $path_parts['extension'];
                                    $extension = strtolower($extension);
                                    $allowed_extensions = array(
                                        'jpg',
                                        'jpeg',
                                        'png'
                                    );
                                    if (!in_array($extension, $allowed_extensions)) {
                                        set_alert('warning', _l('file_php_extension_blocked'));
                                        return false;
                                    }

                                    if (!file_exists($path)) {
                                        mkdir($path);
                                        fopen($path . '/index.html', 'w');
                                    }
                                    $filename    = unique_filename($path, vn_to_str($_FILES["image_mucin"]["name"][$key]));
                                    $newFilePath = $path . $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        $this->db->where('id', $suggest_outsource_item);
                                        $this->db->update('tbl_suggest_outsource_item', array(
                                            'image_mucin' => substr($path, strpos($path, 'uploads')) . $filename,
                                        ));
                                    }
                                }
                            }
                            $path        = 'uploads/suggest_outsource/bongmo/' . $suggest_outsource_item . '/';
                            if (isset($_FILES['image_bongmo']['name'][$key]) && $_FILES['image_bongmo']['name'][$key] != '') {
                                $tmpFilePath = $_FILES['image_bongmo']['tmp_name'][$key];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {

                                    $path_parts         = pathinfo($_FILES["image_bongmo"]["name"][$key]);
                                    $extension          = $path_parts['extension'];
                                    $extension = strtolower($extension);
                                    $allowed_extensions = array(
                                        'jpg',
                                        'jpeg',
                                        'png'
                                    );
                                    if (!in_array($extension, $allowed_extensions)) {
                                        set_alert('warning', _l('file_php_extension_blocked'));
                                        return false;
                                    }
                                    if (!file_exists($path)) {
                                        mkdir($path);
                                        fopen($path . '/index.html', 'w');
                                    }
                                    $filename    = unique_filename($path, vn_to_str($_FILES["image_bongmo"]["name"][$key]));
                                    $newFilePath = $path . $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        $this->db->where('id', $suggest_outsource_item);
                                        $this->db->update('tbl_suggest_outsource_item', array(
                                            'image_bongmo' => substr($path, strpos($path, 'uploads')) . $filename,
                                        ));
                                    }
                                }
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_outsource',
                            'table_obj' => 'tbl_suggest_outsource',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => lang('Thêm mới yêu cầu gia công') . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['result'] = 1;
                        $id_category_recomment = $this->db->get_where('tbl_category_recommended', ['name_table' => 'tbl_suggest_outsource'])->row()->id;
                        $data['id_category_recomment'] = $id_category_recomment;
                        $data['id'] = $id;
                        $data['message'] = lang('Thêm thành công');


                        foreach($this->list_step as $key => $value) {
                            $this->db->insert('tbl_suggest_outsource_step', [
                                'id_suggest_outsource' => $id,
                                'name' => $value['name'],
                                'step' => $value['id'],
                                'status' => 0,
                                'create_by' => get_staff_user_id()
                            ]);
                        }
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
            else {
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $date_request = to_sql_date($this->input->post('date_request'), true);
                    $date_delivery = to_sql_date($this->input->post('date_delivery'), true);
                    $date_go_expected = to_sql_date($this->input->post('date_go_expected'), true);
                    $date_satisfied_expected = to_sql_date($this->input->post('date_satisfied_expected'), true);
                    $object_type = $this->input->post('object_type');
                    $object_id = $this->input->post('object_id');
                    $branch_id = $this->input->post('branch_id');
                    $staff_plan = !empty($this->input->post('staff_plan')) ? $this->input->post('staff_plan') : 0;
                    $note = ($this->input->post('note'));
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $pod_id = !empty($this->input->post('pod_id')[$value]) ? $this->input->post('pod_id')[$value] : 0;
                            $order_id = $this->input->post('order_id')[$value];
                            $order_item_id = $this->input->post('order_item_id')[$value];
                            $productions_orders_id = $this->input->post('productions_orders_id')[$value];
                            $plan_id = $this->input->post('plan_id')[$value];

                            if ($object_type == 'po') {
                                $this->db->select('tbl_productions_orders_items.items_id as items_id,tbl_productions_orders_items.type_items');
                                $this->db->from('tbl_productions_orders_details');
                                $this->db->join(
                                    'tbl_productions_orders_items',
                                    'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id'
                                );
                                $this->db->where('tbl_productions_orders_details.id', $order_item_id);
                                $dtObject = $this->db->get()->row_array();
                                if (empty($dtObject)) {
                                    continue;
                                }
                            } else {
                                $this->db->select('tbl_order_items.item_id as items_id,tbl_order_items.type_item as type_items');
                                $this->db->from('tbl_order_items');
                                $this->db->where('tbl_order_items.id', $order_item_id);
                                $dtObject = $this->db->get()->row_array();
                                if (empty($dtObject)) {
                                    continue;
                                }
                            }
                            $item_id = $dtObject['items_id'];
                            $type_item = $dtObject['type_items'];
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $supplier_id = ($this->input->post('suppliers_id')[$value]);
                            $stage_id = ($this->input->post('stage_id')[$value]);
                            $time_expected = number_unformat($this->input->post('time_expected')[$value] ?? 0);
                            $date_start_outsource = to_sql_date($this->input->post('date_start_outsource')[$value] ?? NULL);
                            $date_end_outsource = to_sql_date($this->input->post('date_end_outsource')[$value] ?? NULL);
                            $price = number_unformat($this->input->post('price')[$value] ?? 0);
                            $price_transport = number_unformat($this->input->post('price_transport')[$value] ?? 0);
                            $shipping_unit_outsource = ($this->input->post('shipping_unit_outsource')[$value] ?? NULL);
                            $transport_outsource = ($this->input->post('transport_outsource')[$value] ?? NULL);
                            $staff_id = 0;
                            $result_id = 0;
                            $tax_id = !empty($this->input->post('tax_id')[$value]) ? $this->input->post('tax_id')[$value] : 0;
                            $id_suggest_outsource_item = !empty($this->input->post('id_suggest_outsource_item')[$value]) ? $this->input->post('id_suggest_outsource_item')[$value] : 0;

                            $sltin = !empty($this->input->post('sltin')[$value]) ? $this->input->post('sltin')[$value] : 0;
                            $material = !empty($this->input->post('material')[$value]) ? $this->input->post('material')[$value] : 0;
                            $quantity_compensation = !empty($this->input->post('quantity_compensation')[$value]) ? $this->input->post('quantity_compensation')[$value] : 0;
                            $quantity_compensation_more = !empty($this->input->post('quantity_compensation_more')[$value]) ? $this->input->post('quantity_compensation_more')[$value] : 0;
                            $landscape_print_size = !empty($this->input->post('landscape_print_size')[$value]) ? $this->input->post('landscape_print_size')[$value] : '';
                            $print = !empty($this->input->post('print')[$value]) ? $this->input->post('print')[$value] : 0;
                            $number_of_printed_sides = !empty($this->input->post('number_of_printed_sides')[$value]) ? $this->input->post('number_of_printed_sides')[$value] : 0;
                            $color_number_a = !empty($this->input->post('color_number_a')[$value]) ? $this->input->post('color_number_a')[$value] : 0;
                            $color_number_b = !empty($this->input->post('color_number_b')[$value]) ? $this->input->post('color_number_b')[$value] : 0;
                            $zinc_number_a = !empty($this->input->post('zinc_number_a')[$value]) ? $this->input->post('zinc_number_a')[$value] : 0;
                            $zinc_number_b = !empty($this->input->post('zinc_number_b')[$value]) ? $this->input->post('zinc_number_b')[$value] : 0;
                            $grape = !empty($this->input->post('grape')[$value]) ? $this->input->post('grape')[$value] : 0;
                            $note_items = !empty($this->input->post('note_items')[$value]) ? $this->input->post('note_items')[$value] : '';
                            $note_detail = !empty($this->input->post('note_detail')[$value]) ? $this->input->post('note_detail')[$value] : '';
                            $tax_rate = 0;
                            $total_tax = 0;
                            if (!empty($tax_id)) {
                                $info_tax = $this->site_model->rowTax($tax_id);
                                if (!empty($info_tax)) {
                                    $tax_rate = $info_tax['taxrate'];
                                }
                            }
                            $amount = $quantity * $price;
                            $amount_transport = $price_transport;
                            $amount += $amount_transport;
                            if ($tax_rate > 0) {
                                $total_tax = $amount * ($tax_rate / 100);
                            }
                            $grand_total = $amount + $total_tax;
                            $suggest_plan_outsource_item_id = !empty($this->input->post('suggest_plan_outsource_item_id')[$value]) ? $this->input->post('suggest_plan_outsource_item_id')[$value] : 0;
                            $image_mucin = '';
                            $image_bongmo = '';
                            if (!empty($suggest_plan_outsource_item_id)) {
                                $data_items = get_table_where('tbl_suggest_outsource_item', array('id' => $suggest_plan_outsource_item_id), '', 'row_array');
                                $image_mucin = $data_items['image_mucin'];
                                $image_bongmo = $data_items['image_bongmo'];
                            }
                            $type_material = '';
                            $id_material = '';
                            if (!empty($material)) {
                                $material = explode('__', $material);
                                $id_material = $material[1];
                                $type_material = $material[0];
                            }

                            $items[] = [
                                'id' => $suggest_plan_outsource_item_id,
                                'order_item_id' => $order_item_id,
                                'productions_orders_id' => $productions_orders_id,
                                'plan_id' => $plan_id,
                                'order_id' => $order_id,
                                'pod_id' => $pod_id,
                                'item_id' => $item_id,
                                'type_item' => $type_item,
                                'quantity' => $quantity,
                                'supplier_id' => $supplier_id,
                                'price' => $price,
                                'amount' => $amount,
                                'shipping_unit_outsource' => $shipping_unit_outsource,
                                'transport_outsource' => $transport_outsource,
                                'stage_id' => $stage_id,
                                'staff_id' => $staff_id,
                                'time_expected' => $time_expected,
                                'tax_id' => $tax_id,
                                'total_tax' => $total_tax,
                                'grand_total' => $grand_total,
                                'price_transport' => $price_transport,
                                'amount_transport' => $amount_transport,
                                'date_start_outsource' => $date_start_outsource,
                                'date_end_outsource' => $date_end_outsource,
                                'result_id' => $result_id,
                                'object_type' => $object_type,
                                'sltin' => $sltin,
                                'material' => $id_material,
                                'type_material' => $type_material,
                                'quantity_compensation' => $quantity_compensation,
                                'quantity_compensation_more' => $quantity_compensation_more,
                                'landscape_print_size' => $landscape_print_size,
                                'print' => $print,
                                'number_of_printed_sides' => $number_of_printed_sides,
                                'color_number_a' => $color_number_a,
                                'color_number_b' => $color_number_b,
                                'zinc_number_a' => $zinc_number_a,
                                'zinc_number_b' => $zinc_number_b,
                                'grape' => $grape,
                                'note' => $note_items,
                                'image_mucin' => $image_mucin,
                                'image_bongmo' => $image_bongmo,
                                'note_detail' => $note_detail,
                            ];
                        }
                    }
                    if (empty($items)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_no_items');
                        echo json_encode($data);
                        die;
                    }
                    $fields = [
                        'date' => $date,
                        'object_id' => $object_id,
                        'date_request' => $date_request,
                        'date_delivery' => $date_delivery,
                        'date_go_expected' => $date_go_expected,
                        'date_satisfied_expected' => $date_satisfied_expected,
                        'object_type' => $object_type,
                        'staff_plan' => $staff_plan,
                        'note' => $note,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_outsource', $fields);
                    if ($success) {
                        $this->db->where('suggest_plan_outsource_id', $id);
                        $this->db->delete('tbl_suggest_outsource_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_plan_outsource_id'] = $id;
                            $this->db->insert('tbl_suggest_outsource_item', $value);
                            $suggest_outsource_item = $this->db->insert_id();
                            $path        = 'uploads/suggest_outsource/mucin/' . $suggest_outsource_item . '/';
                            if (isset($_FILES['image_mucin']['name'][$key]) && $_FILES['image_mucin']['name'][$key] != '') {
                                $tmpFilePath = $_FILES['image_mucin']['tmp_name'][$key];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {

                                    $path_parts         = pathinfo($_FILES["image_mucin"]["name"][$key]);
                                    $extension          = $path_parts['extension'];
                                    $extension = strtolower($extension);
                                    $allowed_extensions = array(
                                        'jpg',
                                        'jpeg',
                                        'png'
                                    );
                                    if (!in_array($extension, $allowed_extensions)) {
                                        set_alert('warning', _l('file_php_extension_blocked'));
                                        return false;
                                    }

                                    if (!file_exists($path)) {
                                        mkdir($path);
                                        fopen($path . '/index.html', 'w');
                                    }
                                    $filename    = unique_filename($path, vn_to_str($_FILES["image_mucin"]["name"][$key]));
                                    $newFilePath = $path . $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        $this->db->where('id', $suggest_outsource_item);
                                        $this->db->update('tbl_suggest_outsource_item', array(
                                            'image_mucin' => substr($path, strpos($path, 'uploads')) . $filename,
                                        ));
                                    }
                                }
                            }
                            $path        = 'uploads/suggest_outsource/bongmo/' . $suggest_outsource_item . '/';
                            if (isset($_FILES['image_bongmo']['name'][$key]) && $_FILES['image_bongmo']['name'][$key] != '') {
                                $tmpFilePath = $_FILES['image_bongmo']['tmp_name'][$key];
                                if (!empty($tmpFilePath) && $tmpFilePath != '') {

                                    $path_parts         = pathinfo($_FILES["image_bongmo"]["name"][$key]);
                                    $extension          = $path_parts['extension'];
                                    $extension = strtolower($extension);
                                    $allowed_extensions = array(
                                        'jpg',
                                        'jpeg',
                                        'png'
                                    );
                                    if (!in_array($extension, $allowed_extensions)) {
                                        set_alert('warning', _l('file_php_extension_blocked'));
                                        return false;
                                    }
                                    if (!file_exists($path)) {
                                        mkdir($path);
                                        fopen($path . '/index.html', 'w');
                                    }
                                    $filename    = unique_filename($path, vn_to_str($_FILES["image_bongmo"]["name"][$key]));
                                    $newFilePath = $path . $filename;
                                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                                        $this->db->where('id', $suggest_outsource_item);
                                        $this->db->update('tbl_suggest_outsource_item', array(
                                            'image_bongmo' => substr($path, strpos($path, 'uploads')) . $filename,
                                        ));
                                    }
                                }
                            }
                        }
                        insertActivityLog([
                            'type_parent_obj' => 'suggest_plan_outsource',
                            'table_obj' => 'tbl_suggest_outsource',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => lang('Sửa phiếu yêu cầu gia công') . ' [' . $dtData['reference_no'] . ']',
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
                if (!$this->preAddSuggestOutsource) {
                    accessDenied(true);
                }
                $data['title'] = lang('dt_add_suggest_outsource');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_outsource'), 'page' => lang('dt_suggest_outsource')), array('link' => '#', 'page' => lang('dt_add_suggest_outsource'))];
            } else {
                if (!$this->preEditSuggestOutsource) {
                    accessDenied(true);
                }

                if ($dtData['status'] == 1) {
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_outsource_item.*,tbl_products.images as images');
                $this->db->from('tbl_suggest_outsource_item');
                $this->db->join('tbl_products', 'tbl_products.id = tbl_suggest_outsource_item.item_id', 'inner');
                $this->db->where('tbl_suggest_outsource_item.suggest_plan_outsource_id', $id);
                $dtItems = $this->db->get()->result_array();
                foreach ($dtItems as $key => $value) {
                    $items = [];
                    $this->db->select('
                        (ppb_materials.item_type) as type, 
                        (ppb_primary.id), 
                        (ppb_materials.item_id), 
                        (ppb_materials.landscape_print_size), 
                        (ppb_materials.number_children_size), 
                        (ppb_materials.unit_parent_id), 
                        (ppb_materials.quantity_single),
                        SUM(ppb_materials.quantity) as quantity,
                        (ppb_materials.quantity_single) as quantity_single,
                    ', false);
                    $this->db->from('tbl_productions_plan_bom ppb_primary');
                    $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
                    $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
                    $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
                    $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
                    $this->db->where('tbl_productions_orders_items.productions_orders_id', $value['productions_orders_id']);
                    $this->db->where('ppb_primary.parent_id', 0);
                    // $this->db->where('(ppb_materials.item_type)', 'materials');
                    $this->db->where('(tbl_productions_orders_items.items_id)', $value['item_id']);

                    $this->db->where('(
                        ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                        OR exists (
                            SELECT
                                tbl_materials.id
                            FROM tbl_materials
                            INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                            WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                        )
                    )', false, false);

                    $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
                    $bom = $this->db->get()->result_array();
                    $total_paper_exchange = 0;
                    if (FIX_QUANTITY_COMPENSATION) {
                        $arrCountItems = [];
                        if (!empty($bom)) {
                            foreach ($bom as $kB => $vB) {
                                $strKey = $vB['type'] . '__' . $vB['item_id'];
                                if (!empty($arrCountItems[$strKey])) {
                                    $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                                } else {
                                    $arrCountItems[$strKey]['count'] = 1;
                                    $arrCountItems[$strKey]['decimal'] = 0;
                                }
                            }
                        }
                    }
                    foreach ($bom as $k => $v) {
                        $item_id = $v['item_id'];
                        $type = $v['type'];
                        $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($value['plan_id'], $item_id, $type);
                        $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];


                        //fix quantity compensation
                        if (FIX_QUANTITY_COMPENSATION) {
                            $strKey = $vB['type'] . '__' . $vB['item_id'];
                            $count_item = $arrCountItems[$strKey]['count'];
                            $division = $quantity_compensation / $count_item;
                            if (is_decimal($division)) {
                                if ($arrCountItems[$strKey]['decimal']) {
                                    $quantity_compensation = floor($division);
                                } else {
                                    $arrCountItems[$strKey]['decimal'] = 1;
                                    $quantity_compensation = ceil($division);
                                }
                            } else {
                                $quantity_compensation = $division;
                            }
                        }
                        //

                        // $quantity = roundNumberFormat($vB['quantity'], 0);
                        $quantity = ceil(round($vB['quantity'], 3));
                        $quantity_single = $vB['quantity_single'];
                        $quantity_need = $quantity + $quantity_compensation;
                        // $paper_exchange = $quantity_single > 0 ? roundNumberFormat($quantity_need/$quantity_single, 0) : 0;
                        $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                        $total_paper_exchange += $paper_exchange;
                        if ($type == "materials") {
                            $info = $this->items_model->rowMaterial($item_id);

                            $this->db->select('
                                tbl_mode_materials.code as code,
                                tbl_mode_materials.name as name,
                            ', false);
                            $this->db->from('tbl_mode_materials');
                            $this->db->where('tbl_mode_materials.id', $info['mode_id']);
                            $dtMode = $this->db->get()->row_array();

                            $mode = $dtMode['name'];
                            $unitBOM = $this->unit_model->rowUnit($info['standard_unit']);
                        } else if ($type == "tools_supplies") {
                            $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                            $unitBOM = $this->unit_model->rowUnit($info['unit_id']);
                        } else {
                            $info = $this->products_model->rowProduct($item_id);
                            $unitBOM = $this->unit_model->rowUnit($info['unit_id']);
                        }
                        $items[$k] = $v;
                        $items[$k]['code_items'] = $info['code'];
                        $items[$k]['name_items'] = $info['name'];
                        $items[$k]['quantity_compensation'] = $quantity_compensation;
                    }
                    $dtItems[$key]['materials'] = $items;
                    $images = base_url('assets/images/tnh/no_image.png');
                    if ($value['images']) {
                        $images = base_url('uploads/products/' . $value['images']);
                    }
                    $dtItems[$key]['images'] = $images;
                }
                $arrObjectId = explode(',', $dtData['object_id']);
                $data['arrObjectId'] = $arrObjectId;
                $data['dtData'] = $dtData;
                $data['dtItems'] = $dtItems;
                $data['title'] = lang('dt_edit_suggest_outsource');
                $data['breadcrumb'] = [array('link' => base_url('admin/suggest_outsource'), 'page' => lang('dt_suggest_outsource')), array('link' => '#', 'page' => lang('dt_edit_suggest_outsource'))];
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['reference_no'] = getReference('suggest_outsource');
        $data['dtResult'] = get_table_where('tbl_result');
        $data['dtStaff'] = get_table_where('tblstaff', ['active' => 1]);
        $data['taxs'] = $this->site_model->getTaxs();
        $data['print'] = [
            [
                'id' => 1,
                'name' => 'In A-B',
            ],
            [
                'id' => 2,
                'name' => 'In Trở',
            ],
            [
                'id' => 3,
                'name' => 'In 1 mặt',
            ]
        ];

        $this->load->view('admin/suggest_outsource/detail', $data);
    }

    public function view($id)
    {
        $data = [];
        $data['title'] = lang('dt_view_suggest_outsource');

        $this->db->select('tbl_suggest_outsource.*');
        $this->db->from('tbl_suggest_outsource');
        $this->db->where('tbl_suggest_outsource.id', $id);
        $dtData = $this->db->get()->row_array();

        $this->db->select('tbl_suggest_outsource_item.*,
            tbl_result.name as name_result,
            tblsuppliers.company as company,
            IF(tbl_suggest_outsource_item.object_type="order",tbl_orders.reference_no,tbl_productions_orders.reference_no) as reference_no_order,
            tbl_stages.name as name_stage,
            tbltaxes.name as name_tax,
        ');
        $this->db->from('tbl_suggest_outsource_item');
        $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_outsource_item.result_id', 'left');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_outsource_item.supplier_id', 'inner');
        $this->db->join('tbl_orders', 'tbl_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type="order"', 'left');
        $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type="po"', 'left');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_suggest_outsource_item.stage_id', 'left');
        $this->db->join('tbltaxes', 'tbltaxes.id = tbl_suggest_outsource_item.tax_id', 'left');
        $this->db->where('tbl_suggest_outsource_item.suggest_plan_outsource_id', $id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_outsource/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveSuggestnOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_outsource.*');
        $this->db->from('tbl_suggest_outsource');
        $this->db->where('tbl_suggest_outsource.id', $suggest_id);
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

            $date_status = date('Y-m-d H:i:s');
            $staff_status = get_staff_user_id();

            $options = [
                'status' => $status,
                'date_status' => $date_status,
                'staff_status' => $staff_status,
            ];

            $this->db->where('id', $suggest_id);
            $up = $this->db->update('tbl_suggest_outsource', $options);
            if ($up) {
                insertActivityLog([
                    'type_parent_obj' => 'suggest_outsource',
                    'table_obj' => 'tbl_suggest_outsource',
                    'id_obj' => $suggest_id,
                    'name_obj' => $dtData['reference_no'],
                    'content' => lang('Duyệt phiếu yêu cầu gia công') . ' [' . $dtData['reference_no'] . ']',
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
        if (!$this->preDeleteSuggestOutsource) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data = [];
        $this->db->select('tbl_suggest_outsource.*');
        $this->db->from('tbl_suggest_outsource');
        $this->db->where('tbl_suggest_outsource.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }


        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã được duyệt không thể xóa !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_outsource');
        if ($success) {
            $this->db->where('tbl_suggest_outsource_item.suggest_plan_outsource_id', $id);
            $this->db->delete('tbl_suggest_outsource_item');

            $this->db->where('tbl_moderation_outsource.suggest_outsource_id', $id);
            $this->db->delete('tbl_moderation_outsource');

            insertActivityLog([
                'type_parent_obj' => 'suggest_outsource',
                'table_obj' => 'tbl_suggest_outsource',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => lang('Xóa phiếu yêu cầu gia công') . ' [' . $dtData['reference_no'] . ']',
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

    public function searchPo($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_productions_orders.id as id, 
            CONCAT(tbl_productions_orders.reference_no) as text,
         
        ', false);
        $this->db->from('tbl_productions_orders');
        $this->db->where('EXISTS(
            SELECT 1
            FROM tbl_productions_plan_orders
            WHERE tbl_productions_plan_orders.productions_order_id = tbl_productions_orders.id AND tbl_productions_plan_orders.object_type = "orders"
        )');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_orders.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Lệnh sản xuất'), 'children' => $dtResult];
        if (!empty($id)) {
            $dtData = get_table_where('tbl_productions_orders', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['reference_no']];
        }
        echo json_encode($data);
    }

    public function searchProductByOrders()
    {
        $term = $this->input->get('term');
        $object_id = !empty($this->input->get('object_id')) ? $this->input->get('object_id') : 0;
        $object_type = $this->input->get('object_type');
        $limit = get_option('select2_limit');
        if ($object_type == 'po') {
            $this->db->select('
                tbl_productions_orders_details.id as id, 
                tbl_productions_orders_details.id as order_item_id, 
                tbl_productions_orders_items.items_id as item_id, 
                tbl_productions_orders_items.quantity as total_quantity_item,
                CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
                tbl_products.code as code_item,
                tbl_products.name as name_item,
                tbl_products.name_customer as name_customer,
                tbl_products.mode as mode,
                tbl_products.images as images,
                tblunits.unit as unit_name,
                tbl_productions_orders.reference_no as reference_no_order,
                tbl_productions_orders.id as order_id,
                tbl_productions_orders_items.productions_orders_id,
                tbl_productions_orders_items.plan_id
            ', false);
            $this->db->from('tbl_productions_orders_details');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id', 'inner');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_productions_orders_details.object_id AND tbl_productions_orders_details.object_type = "orders"', 'left');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_orders_items.items_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->where('tbl_productions_orders_details.productions_orders_id IN (' . $object_id . ')');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_products.code', $term);
                $this->db->or_like('tbl_products.name', $term);
                $this->db->or_like('tbl_productions_orders.reference_no', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $results = $this->db->get()->result_array();
        } else {
            $this->db->select('
                tbl_order_items.id as id, 
                tbl_order_items.id as order_item_id, 
                tbl_order_items.item_id as item_id, 
                tbl_order_items.total_quantity_item as total_quantity_item,
                CONCAT(tbl_products.name,"(",tbl_products.code,")") as text,
                tbl_products.code as code_item,
                tbl_products.name as name_item,
                tbl_products.images as images,
                tbl_products.name_customer as name_customer,
                tbl_products.mode as mode,
                tblunits.unit as unit_name,
                tbl_orders.reference_no as reference_no_order,
                tbl_orders.id as order_id,
                0 as productions_orders_id,
            ', false);
            $this->db->from('tbl_orders');
            $this->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_order_items.item_id', 'inner');
            $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'inner');
            $this->db->where('tbl_orders.id IN (' . $object_id . ')');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_products.code', $term);
                $this->db->or_like('tbl_products.name', $term);
                $this->db->or_like('tbl_orders.reference_no', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $results = $this->db->get()->result_array();
        }

        $resultsNew = [];
        $data = [];
        if (!empty($results)) {
            foreach ($results as $key => $value) {
                $images = base_url('assets/images/tnh/no_image.png');
                if ($value['images']) {
                    $images = base_url('uploads/products/' . $value['images']);
                }
                $value['images'] = $images;
                if (!empty($resultsNew[$value['reference_no_order']])) {
                    $resultsNew[$value['reference_no_order']]['items'][] = $value;
                } else {
                    $resultsNew[$value['reference_no_order']]['items'][] = $value;
                }
            }
        }
        foreach ($resultsNew as $key => $value) {
            $data['results'][] =
                [
                    'text' => $key,
                    'children' => $value['items']
                ];
        }
        echo json_encode($data);
    }

    public function searchStage($id = 0)
    {
        $term = $this->input->get('term');
        $params = $this->input->get('params');
        $pod_id = !empty($params['pod_id']) ? $params['pod_id'] : 0;
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_stages.id as id, 
            tbl_stages.name as text,
        ', false);
        $this->db->from('tbl_stages');
//        $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.id = tbl_productions_orders_items_stages.productions_orders_items_id', 'inner');
//        $this->db->join('tbl_productions_orders_details', 'tbl_productions_orders_details.productions_orders_item_id = tbl_productions_orders_items.id', 'inner');
//        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id', 'inner');
//        $this->db->where('tbl_productions_orders_details.id', $pod_id);
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_stages.name', $term);
            $this->db->or_like('tbl_stages.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();

        $data['results'][] =
            [
                'text' => lang('Công đoạn'),
                'children' => $results
            ];
        if (!empty($id)) {
            $dtData = get_table_where('tbl_stages', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtData['id'], 'text' => $dtData['name']];
        }
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
                tbl_suggest_outsource.id as id,
                tbl_suggest_outsource.reference_no as reference_no,
                tbl_suggest_outsource.date as date,
                tbl_suggest_outsource.date_request as date_request,
                tbl_suggest_outsource.date_delivery as date_delivery,
                tbl_suggest_outsource.date_go_expected as date_go_expected,
                tbl_suggest_outsource.date_satisfied_expected as date_satisfied_expected,
                tbl_orders.reference_no as reference_no_order,
                tbl_productions_orders.reference_no as reference_no_po,
                tbl_stages.name as name_stage,
                tbl_suggest_outsource_item.quantity as quantity,
                tbl_suggest_outsource_item.time_expected as time_expected,
                tbl_suggest_outsource_item.date_start_outsource as date_start_outsource,
                tbl_suggest_outsource_item.date_end_outsource as date_end_outsource,
                tblsuppliers_groups.name as name_supplier_group,
                tblsuppliers.company as name_supplier,
                tbl_suggest_outsource_item.price as price,
                tbltaxes.name as name_tax,
                tbl_suggest_outsource_item.grand_total as grand_total,
                tbl_suggest_outsource_item.shipping_unit_outsource as shipping_unit_outsource,
                tbl_suggest_outsource_item.transport_outsource as transport_outsource,
                tbl_suggest_outsource_item.price_transport as price_transport,
                tbl_suggest_outsource_item.amount_transport as amount_transport,
                tbl_result.name as name_result,
                (SELECT GROUP_CONCAT(tblproduction_report.name_report)
                 FROM tblproduction_report
                 WHERE tblproduction_report.object_id = tbl_suggest_outsource.id AND tblproduction_report.object_type = "suggest_outsource"
                ) as name_report,
                tbl_suggest_outsource.staff_plan as staff_plan,
                tbl_suggest_outsource_item.staff_id as staff_id,
                tbl_suggest_outsource_item.item_id as item_id,
                tbl_suggest_outsource_item.type_item as type_item,
                tbl_suggest_outsource_item.sltin as sltin,
                tbl_suggest_outsource_item.quantity_compensation as quantity_compensation,
                tbl_suggest_outsource_item.quantity_compensation_more as quantity_compensation_more,
                tbl_suggest_outsource_item.landscape_print_size as landscape_print_size,

                tbl_suggest_outsource_item.print as print,
                tbl_suggest_outsource_item.number_of_printed_sides as number_of_printed_sides,
                tbl_suggest_outsource_item.color_number_a as color_number_a,
                tbl_suggest_outsource_item.color_number_b as color_number_b,
                tbl_suggest_outsource_item.zinc_number_a as zinc_number_a,
                tbl_suggest_outsource_item.zinc_number_b as zinc_number_b,
                tbl_suggest_outsource_item.grape as grape,
                tbl_suggest_outsource_item.image_mucin as image_mucin,
                tbl_suggest_outsource_item.image_bongmo as image_bongmo,
                tbl_suggest_outsource_item.note as note_items,
                tbl_suggest_outsource_item.type_material as type_material,
                tbl_suggest_outsource_item.material as material,
                tbl_suggest_outsource_item.amount as amount,
            ');

            $this->db->from('tbl_suggest_outsource');
            $this->db->join('tbl_suggest_outsource_item', 'tbl_suggest_outsource_item.suggest_plan_outsource_id = tbl_suggest_outsource.id');
            $this->db->join('tbl_orders', 'tbl_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type = "order"', 'left');
            $this->db->join('tbl_productions_orders', 'tbl_productions_orders.id = tbl_suggest_outsource_item.order_id AND tbl_suggest_outsource_item.object_type = "po"', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_outsource_item.supplier_id', 'left');
            $this->db->join('tblsuppliers_groups', 'tblsuppliers_groups.id = tblsuppliers.groups_in', 'left');
            $this->db->join('tbl_stages', 'tbl_stages.id = tbl_suggest_outsource_item.stage_id', 'left');
            $this->db->join('tbltaxes', 'tbltaxes.id = tbl_suggest_outsource_item.tax_id', 'left');
            $this->db->join('tbl_result', 'tbl_result.id = tbl_suggest_outsource_item.result_id', 'left');

            if (!$this->preViewSuggestOutsource) {
                $this->db->where('(tbl_suggest_outsource.created_by = ' . get_staff_user_id() . ' OR tbl_suggest_outsource.staff_plan = ' . get_staff_user_id() . ' )');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_outsource.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_outsource.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_outsource.id desc');
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
                ('PHIẾU YÊU CẦU GIA CÔNG')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:Z1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Nhà Gia Công');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Gửi Yêu Cầu Gia Công')->getStyle("C$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Ngày Giao Hàng');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Ngày Đưa Đi Dự Kiện')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Ngày Về Dự Kiến');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Lệnh Sàn Xuát')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Mã Sản Phầm')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Tên Sản Phẩm');

            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Quy Cách')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Đơn Vị Tính')->getStyle("K$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Chi Tiết Gia Công')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Đơn Vị Vận chuyển Gia Công')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Phương tiện vận chuyển gia công')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Chi Phí Vận Chuyển')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'Đơn Giá Gia Công')->getStyle("P$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $sttRow . '', 'Số lượng gia công')->getStyle("Q$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $sttRow . '', 'Thành tiền')->getStyle("R$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $sttRow . '', 'VAT (%)')->getStyle("S$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $sttRow . '', 'Tổng Sau VAT')->getStyle("T$sttRow")->getAlignment()->setWrapText(true);



            $objPHPExcel->getActiveSheet()->setCellValue('U' . $sttRow . '', 'Số Lượng Tờ In')->getStyle("U$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $sttRow . '', 'NVL In')->getStyle("V$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $sttRow . '', 'Số Lượng Bù Hao (Tờ In)')->getStyle("W$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $sttRow . '', 'Số Lượng Bù Hao Xuất Thêm (Tờ In)')->getStyle("X$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $sttRow . '', 'Khổ In (cm)')->getStyle("Y$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $sttRow . '', 'Hình Ảnh')->getStyle("Z$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $sttRow . '', 'Loại Hình Phủ')->getStyle("AA$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AB' . $sttRow . '', 'Cách In')->getStyle("AB$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $sttRow . '', 'Số Mặt In')->getStyle("AC$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AD' . $sttRow . '', 'Số Màu - Mặt A')->getStyle("AD$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AE' . $sttRow . '', 'Số Màu - Mặt B')->getStyle("AE$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $sttRow . '', 'Số Kẽm- Mặt A')->getStyle("AF$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AG' . $sttRow . '', 'Số Kẽm- Mặt B')->getStyle("AG$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AH' . $sttRow . '', 'Nhíp Kẽm')->getStyle("AH$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AI' . $sttRow . '', 'Hình ảnh mực in')->getStyle("AI$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $sttRow . '', 'Hình ảnh bóng phủ')->getStyle("AJ$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('AK' . $sttRow . '', 'Ghi Chú')->getStyle("AK$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:AK$sttRow")->applyFromArray([
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
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                    }
                    $images = ('assets/images/tnh/no_image.png');
                    if ($info['images']) {
                        $images = ('uploads/products/' . $info['images']);
                    }
                    $rowBegin++;
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['name_supplier'])->getStyle("B$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _d($value['date_request']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", _d($value['date_delivery']));
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", _d($value['date_go_expected']));
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", _d($value['date_satisfied_expected']));
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['reference_no_po'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $info['code'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $info['name'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);



                    $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowBegin, ($info['mode'] ?? ''))->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowBegin, ($unit['unit'] ?? ''))->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowBegin, ($value['note_detail'] ?? ''))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('M' . $rowBegin, ($value['shipping_unit_outsource'] ?? ''))->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('N' . $rowBegin, $value['transport_outsource'] )->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('O' . $rowBegin, number_format_data($value['price_transport'] ?? 0))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('P' . $rowBegin, number_format_data($value['price'] ?? 0))->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('Q' . $rowBegin, number_format_data($value['quantity'] ?? 0))->getStyle("Q$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('R' . $rowBegin, number_format_data($value['amount'] ?? 0))->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('S' . $rowBegin, $value['name_tax'] ?? '')->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('T' . $rowBegin, number_format_data($value['grand_total'] ?? 0))->getStyle("T$rowBegin")->getAlignment()->setWrapText(true);



                    $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", ($value['sltin']))->getStyle("U$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['sltin']));
                    $type_material = $value['type_material'];
                    if ($type_material == "materials") {
                        $info_material = $this->items_model->rowMaterial($value['material']);
                    } else if ($type_material == "tools_supplies") {
                        $info_material = $this->tools_supplies_model->rowToolsSupplies($item_id);
                    } else {
                        $info_material = $this->products_model->rowProduct($item_id);
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", ($info_material['name']))->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", ($value['quantity_compensation']))->getStyle("W$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity_compensation']));
                    $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", ($value['quantity_compensation_more']))->getStyle("X$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity_compensation_more']));
                    $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", $value['landscape_print_size']);
                    if ($images != '' && file_exists($images)) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($images);
                        $objDrawing1->setWidth(110);
                        $objDrawing1->setHeight(85);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('Z' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(80);
                    $objPHPExcel->getActiveSheet()->setCellValue("Z$rowBegin", ($value['name_stage']))->getStyle("Z$rowBegin")->getAlignment()->setWrapText(true);


                    $print_text = '';
                    $print = [
                        [
                            'id' => 1,
                            'name' => 'In A-B',
                        ],
                        [
                            'id' => 2,
                            'name' => 'In Trở',
                        ],
                        [
                            'id' => 3,
                            'name' => 'In 1 mặt',
                        ]
                    ];
                    foreach ($print as $kk => $vv) {
                        if ($vv['id'] == $value['print']) {
                            $print_text = $vv['name'];
                            break;
                        }
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("AA$rowBegin", ($print_text))->getStyle("AA$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->setCellValue("AB$rowBegin", ($value['number_of_printed_sides']))->getStyle("AB$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['number_of_printed_sides']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AC$rowBegin", ($value['color_number_a']))->getStyle("AC$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['color_number_a']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AD$rowBegin", ($value['color_number_b']))->getStyle("AD$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['color_number_b']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AE$rowBegin", ($value['zinc_number_a']))->getStyle("AE$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['zinc_number_a']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AF$rowBegin", ($value['zinc_number_b']))->getStyle("AF$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['zinc_number_b']));
                    $objPHPExcel->getActiveSheet()->setCellValue("AG$rowBegin", ($value['grape']))->getStyle("AG$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['grape']));
                    // $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", '');
                    if ($value['image_mucin'] != '' && file_exists($value['image_mucin'])) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($value['image_mucin']);
                        $objDrawing1->setWidth(110);
                        $objDrawing1->setHeight(85);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('AI' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(80);

                    if ($value['image_bongmo'] != '' && file_exists($value['image_bongmo'])) {
                        $objDrawing1 = new PHPExcel_Worksheet_Drawing();
                        $objDrawing1->setWorksheet($objPHPExcel->getActiveSheet());
                        $objDrawing1->setPath($value['image_bongmo']);
                        $objDrawing1->setWidth(110);
                        $objDrawing1->setHeight(85);
                        $objDrawing1->setOffsetX(20);
                        $objDrawing1->setOffsetY(5);
                        $objDrawing1->setCoordinates('AJ' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(80);
                    $objPHPExcel->getActiveSheet()->setCellValue("AK$rowBegin", $value['note_items']);


                    // $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['sltin']))->getStyle("I$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['sltin']));

                    // $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", ($value['reference_no_order']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['reference_no_po']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_stage']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);

                    // $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", ($value['quantity']))->getStyle("I$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity']));
                    // $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", $value['time_expected'])->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", !empty($value['date_start_outsource']) ? _dhau($value['date_start_outsource']) : '')->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", !empty($value['date_end_outsource']) ? _dhau($value['date_end_outsource']) : '')->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", $value['name_supplier_group'])->getStyle("M$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", $value['name_supplier'])->getStyle("N$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($value['price']))->getStyle("O$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", $value['name_tax'])->getStyle("P$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("Q$rowBegin", ($value['grand_total']))->getStyle("Q$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['grand_total']));
                    // $objPHPExcel->getActiveSheet()->setCellValue("R$rowBegin", $value['shipping_unit_outsource'])->getStyle("R$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("S$rowBegin", $value['transport_outsource'])->getStyle("S$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("T$rowBegin", ($value['price_transport']))->getStyle("T$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['price_transport']));
                    // $objPHPExcel->getActiveSheet()->setCellValue("U$rowBegin", ($value['amount_transport']))->getStyle("U$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['amount_transport']));
                    // $objPHPExcel->getActiveSheet()->setCellValue("V$rowBegin", ($value['name_result']))->getStyle("V$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("W$rowBegin", ($value['name_report']))->getStyle("W$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("X$rowBegin", get_staff_full_name($value['staff_plan']))->getStyle("X$rowBegin")->getAlignment()->setWrapText(true);
                    // $objPHPExcel->getActiveSheet()->setCellValue("Y$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("Y$rowBegin")->getAlignment()->setWrapText(true);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:AK$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:I$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("I$rowBegin:J$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("P$rowBegin:P$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_gia_cong') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);


            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(10);
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

    public function searchPoAndOrder($id = 0, $object_type = '')
    {
        $term = $this->input->get('term');
        if (empty($object_type)) {
            $object_type = $this->input->get('object_type');
        }
        $limit = get_option('select2_limit');
        if ($object_type == 'po') {
            $this->db->select('
                tbl_productions_orders.id as id, 
                CONCAT(tbl_productions_orders.reference_no) as text,
            ', false);
            $this->db->from('tbl_productions_orders');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_productions_orders.reference_no', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $dtResult = $this->db->get()->result_array();
        } else {
            $this->db->select('
                tbl_orders.id as id, 
                CONCAT(tbl_orders.reference_no) as text,
            ', false);
            $this->db->from('tbl_orders');
            $this->db->where('tbl_orders.status', 'approved');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('tbl_orders.reference_no', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            $dtResult = $this->db->get()->result_array();
        }

        $data['results'][] = ['text' => lang('Đơn hàng/Lệnh sản xuất'), 'children' => $dtResult];
        if (!empty($id)) {
            $id = explode('_', $id);
            if ($object_type == 'po') {
                $this->db->select('tbl_productions_orders.*');
                $this->db->from('tbl_productions_orders');
                $this->db->where_in('tbl_productions_orders.id', $id);
                $dtData = $this->db->get()->result_array();
            } else {
                $this->db->select('tbl_orders.*');
                $this->db->from('tbl_orders');
                $this->db->where_in('tbl_orders.id', $id);
                $dtData = $this->db->get()->result_array();
            }
            if (!empty($dtData)) {
                foreach ($dtData as $key => $value) {
                    $data['row'][] = ['id' => $value['id'], 'text' => $value['reference_no']];
                }
            }
        }
        echo json_encode($data);
    }
    function getFullDataItems($id = '', $productions_orders_id = '', $items_id = '', $plan_id = '', $type = '')
    {
        $data['sltin'] = 0;
        $items = [];
        if ($type == 'po') {

            $this->db->select('
                (ppb_materials.item_type) as type, 
                (ppb_primary.id), 
                (ppb_materials.item_id), 
                (ppb_materials.landscape_print_size), 
                (ppb_materials.number_children_size), 
                (ppb_materials.unit_parent_id), 
                (ppb_materials.quantity_single),
                SUM(ppb_materials.quantity) as quantity,
                (ppb_materials.quantity_single) as quantity_single,
            ', false);
            $this->db->from('tbl_productions_plan_bom ppb_primary');
            $this->db->join('tbl_productions_plan_bom ppb_materials ', 'ppb_primary.id = (ppb_materials.parent_id)', 'inner');
            $this->db->join('tbl_productions_plan_items', 'tbl_productions_plan_items.id = ppb_primary.productions_plan_items_id', 'inner');
            $this->db->join('tbl_productions_orders_items', 'tbl_productions_orders_items.plan_item_id = tbl_productions_plan_items.id', 'inner');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_items.product_id', 'inner');
            $this->db->where('tbl_productions_orders_items.productions_orders_id', $productions_orders_id);
            $this->db->where('ppb_primary.parent_id', 0);
            // $this->db->where('(ppb_materials.item_type)', 'materials');
            $this->db->where('(tbl_productions_orders_items.items_id)', $items_id);

            $this->db->where('(
                ppb_materials.item_type IN ("semi_products", "semi_products_outside")
                OR exists (
                    SELECT
                        tbl_materials.id
                    FROM tbl_materials
                    INNER JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id
                    WHERE ppb_materials.item_type = "materials" AND tbl_materials.id = ppb_materials.item_id AND tbl_category_items.is_primary = 1
                )
            )', false, false);

            $this->db->group_by('ppb_materials.item_type, ppb_materials.item_id, ppb_materials.landscape_print_size, ppb_materials.number_children_size, ppb_materials.unit_parent_id, ppb_materials.quantity_single', false);
            $bom = $this->db->get()->result_array();
            $total_paper_exchange = 0;
            if (FIX_QUANTITY_COMPENSATION) {
                $arrCountItems = [];
                if (!empty($bom)) {
                    foreach ($bom as $kB => $vB) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        if (!empty($arrCountItems[$strKey])) {
                            $arrCountItems[$strKey]['count'] = $arrCountItems[$strKey]['count'] + 1;
                        } else {
                            $arrCountItems[$strKey]['count'] = 1;
                            $arrCountItems[$strKey]['decimal'] = 0;
                        }
                    }
                }
            }
            if (!empty($bom)) {
                foreach ($bom as $kB => $vB) {
                    $item_id = $vB['item_id'];
                    $type = $vB['type'];
                    $productionsPlanCompensation = $this->manufactures_model->productionsPlanCompensation($plan_id, $item_id, $type);
                    $quantity_compensation = $productionsPlanCompensation['quantity_compensation'];


                    //fix quantity compensation
                    if (FIX_QUANTITY_COMPENSATION) {
                        $strKey = $vB['type'] . '__' . $vB['item_id'];
                        $count_item = $arrCountItems[$strKey]['count'];
                        $division = $quantity_compensation / $count_item;
                        if (is_decimal($division)) {
                            if ($arrCountItems[$strKey]['decimal']) {
                                $quantity_compensation = floor($division);
                            } else {
                                $arrCountItems[$strKey]['decimal'] = 1;
                                $quantity_compensation = ceil($division);
                            }
                        } else {
                            $quantity_compensation = $division;
                        }
                    }
                    //

                    // $quantity = roundNumberFormat($vB['quantity'], 0);
                    $quantity = ceil(round($vB['quantity'], 3));
                    $quantity_single = $vB['quantity_single'];
                    $quantity_need = $quantity + $quantity_compensation;
                    // $paper_exchange = $quantity_single > 0 ? roundNumberFormat($quantity_need/$quantity_single, 0) : 0;
                    $paper_exchange = $quantity_single > 0 ? ceil($quantity_need / $quantity_single) : 0;
                    $total_paper_exchange += $paper_exchange;
                    $items[$kB] = $vB;
                    if ($type == "materials") {
                        $info = $this->items_model->rowMaterial($item_id);

                        $this->db->select('
                            tbl_mode_materials.code as code,
                            tbl_mode_materials.name as name,
                        ', false);
                        $this->db->from('tbl_mode_materials');
                        $this->db->where('tbl_mode_materials.id', $info['mode_id']);
                        $dtMode = $this->db->get()->row_array();

                        $mode = $dtMode['name'];
                        $unitBOM = $this->unit_model->rowUnit($info['standard_unit']);
                    } else if ($type == "tools_supplies") {
                        $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        $unitBOM = $this->unit_model->rowUnit($info['unit_id']);
                    } else {
                        $info = $this->products_model->rowProduct($item_id);
                        $unitBOM = $this->unit_model->rowUnit($info['unit_id']);
                    }
                    $items[$kB]['code_items'] = $info['code'];
                    $items[$kB]['name_items'] = $info['name'];
                    $items[$kB]['quantity_compensation'] = $quantity_compensation;
                }
            }
            $data['sltin'] = $total_paper_exchange;
            $data['items'] = $items;
        }
        echo json_encode($data);
    }



    public function get_table_delivery_records($id) {

        $this->db->where('id', $id);
        $data['suggest_outsource'] = $this->db->get('tbl_suggest_outsource')->row();
        $this->db->where('suggest_plan_outsource_id', $id);
        $data['suggest_outsource_item'] = $this->db->get('tbl_suggest_outsource_item')->result_array();
        foreach($data['suggest_outsource_item'] as $key => $value) {
            $stage_id = $value['stage_id'];
            $this->db->select('tbl_category_hand_over.*');
            $this->db->group_start();
            $this->db->where('tbl_category_hand_over.code', CODE_HAND_OVER_CATEGORY);
            $this->db->or_like('tbl_category_hand_over.code', 'BGCD-');
            $this->db->group_end();
            $this->db->join('tbl_hand_over_task', 'tbl_hand_over_task.category_hand_over_id = tbl_category_hand_over.id');
            $this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
            $this->db->where('tbl_hand_over_task.type_hide', 0);
            $category_hand_over = $this->db->get('tbl_category_hand_over')->row();

            if (!empty($category_hand_over)) {
                $this->db->select('tbl_hand_over_task.*, tbl_stages.code as code_stage, tbl_packaging.code as standard');
                $this->db->join('tbl_stages', 'tbl_stages.id = tbl_hand_over_task.id_stage', 'left');
                $this->db->join('tbl_packaging', 'tbl_packaging.id = tbl_hand_over_task.standard', 'left');
                $this->db->where('tbl_hand_over_task.category_hand_over_id', $category_hand_over->id);
                $this->db->where_in('tbl_hand_over_task.id_stage', $stage_id);
                $this->db->where('tbl_hand_over_task.type_hide', 0);
                $hand_over_task = $this->db->get('tbl_hand_over_task')->result_array();
                $category_hand_over->task = $hand_over_task;
            }
            $data['suggest_outsource_item'][$key]['category_hand_over'] = $category_hand_over;
        }
        $this->load->view('admin/suggest_outsource/hand_over_task', $data);
    }

    public function update_hand_over_task()
    {
        if($this->input->post()) {
            $data = $this->input->post();
            $id_suggest_outsource = $data['id_suggest_outsource'];
            if(!empty($id_suggest_outsource)) {
                $id_suggest_outsource_item = $data['id_suggest_outsource_item'];
                $hand_over_task_id = $data['hand_over_task_id'];
                $task_hand_over_qualified = $data['task_hand_over_qualified'];
                $dataItemInsert = [];
                $dataItemUpdate = [];
                $staffid = get_staff_user_id();
                foreach ($id_suggest_outsource_item as $idItem => $item) {
                    foreach ($hand_over_task_id[$idItem] as $key => $value) {
                        $this->db->where('id_suggest_outsource', $id_suggest_outsource);
                        $this->db->where('id_suggest_outsource_item', $idItem);
                        $this->db->where('hand_over_task_id', $value);
                        $ktOver = $this->db->get('tbl_suggest_outsource_task')->row();
                        if (!empty($ktOver)) {
                            if ($task_hand_over_qualified[$idItem][$key] != $ktOver->task_hand_over_qualified) {
                                $dataItemUpdate[] = [
                                    'id' => $ktOver->id,
                                    'task_hand_over_qualified' => $task_hand_over_qualified[$idItem][$key] ?? 0,
                                    'staff_id' => $staffid,
                                    'date_check' => date('Y-m-d H:i:s'),
                                ];
                            }
                        } else {
                            $dataItemInsert[] = [
                                'id_suggest_outsource' => $id_suggest_outsource,
                                'id_suggest_outsource_item' => $idItem,
                                'hand_over_task_id' => $value,
                                'task_hand_over_qualified' => $task_hand_over_qualified[$idItem][$key] ?? 0,
                                'staff_id' => $staffid,
                                'date_check' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
                if (!empty($dataItemUpdate)) {
                    $this->db->update_batch('tbl_suggest_outsource_task', $dataItemUpdate, 'id');
                }
                if (!empty($dataItemInsert)) {
                    $this->db->insert_batch('tbl_suggest_outsource_task', $dataItemInsert);
                }

                $this->db->where('step', 2);
                $this->db->where('id_suggest_outsource', $id_suggest_outsource);
                $this->db->update('tbl_suggest_outsource_step', [
                    'status' => 1
                ]);

                echo json_encode([
                    'success' => true,
                    'result' => true,
                    'alert_type' => 'success',
                    'message' => 'Cập nhật bàn giao thành công',
                    'id' => $id_suggest_outsource
                ]);
                die();
            }
            else {
                echo json_encode([
                    'success' => false,
                    'result' => false,
                    'alert_type' => 'danger',
                    'message' => 'Cập nhật bàn giao không thành công',
                ]);
                die();
            }
        }
    }
}
