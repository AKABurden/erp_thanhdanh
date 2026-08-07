<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suggest_plan_purchase extends AdminController
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

        $this->type = 1;
        if (!empty($this->input->get('type'))) {
            $this->type = $this->input->get('type');
        }

        $this->preViewSuggestPlanPurchase = true;
        $this->preViewOwnSuggestPlanPurchase = true;
        $this->preAddSuggestPlanPurchase = true;
        $this->preEditSuggestPlanPurchase = true;
        $this->preApproveSuggestPlanPurchase = true;
        $this->preDeleteSuggestPlanPurchase = true;
    }

    public function index()
    {
        if (!$this->preViewSuggestPlanPurchase && !$this->preViewOwnSuggestPlanPurchase) {
            access_denied();
        }
        if ($this->type == 1) {
            $data['title'] = _l('suggest_plan_purchase_nvl');
        } elseif ($this->type == 2) {
            $data['title'] = _l('suggest_plan_purchase_vt');
        } elseif ($this->type == 3) {
            $data['title'] = _l('suggest_plan_purchase_machines');
        }
        $data['type'] = $this->type;
        $this->load->view('admin/suggest_plan_purchase/index', $data);
    }

    public function getSuggestPlanPurchases()
    {
        $end_date_search = $this->input->post('end_date_search');
        $start_date_search = $this->input->post('start_date_search');
        $type = $this->input->post('type');

        $this->db->simple_query('SET SESSION group_concat_max_len=15000000');

        $tb_items = "(
            SELECT
                tbl_suggest_plan_purchase_item.suggest_plan_purchase_id,
                SUM(tbl_suggest_plan_purchase_item.amount) as grand_total
            FROM tbl_suggest_plan_purchase_item
            GROUP BY tbl_suggest_plan_purchase_item.suggest_plan_purchase_id
        ) tb_items";

        $aColumns = [
            'tbl_suggest_plan_purchase.id as id',
            'tbl_suggest_plan_purchase.reference_no as reference_no',
            'tbl_suggest_plan_purchase.date as date',
            'tbl_suggest_plan_purchase.staff_id as staff_id',
            'tbl_suggest_plan_purchase.time_finish as time_finish',
            'tbl_category_plan_time.name as name_category_plan_time',
            'tb_items.grand_total as grand_total',
            'tbl_suggest_plan_purchase.status as status',
            'tbl_suggest_plan_purchase.created_by as created_by',
            '1'
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_suggest_plan_purchase';
        $where = [];
        $filter = [];

        $join = [
            'INNER JOIN tbl_category_plan_time ON tbl_category_plan_time.id = tbl_suggest_plan_purchase.category_plan',
            'INNER JOIN ' . $tb_items . ' ON tb_items.suggest_plan_purchase_id = tbl_suggest_plan_purchase.id',
        ];

        array_push($where, 'AND tbl_suggest_plan_purchase.type =', $type);

        if (!$this->preViewSuggestPlanPurchase) {
            array_push($where, 'AND (tbl_suggest_plan_purchase.created_by = ' . get_staff_user_id() . ' OR tbl_suggest_plan_purchase.staff_id = ' . get_staff_user_id() . ')');
        }

        if (!empty($start_date_search)) {
            $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
            array_push($where, "AND tbl_suggest_plan_purchase.date >= '" . $start_date_search . "'");
        }

        if (!empty($end_date_search)) {
            $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
            array_push($where, "AND tbl_suggest_plan_purchase.date <= '" . $end_date_search . "'");
        }


        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
            'tbl_suggest_plan_purchase.type',
            'tbl_suggest_plan_purchase.date_status',
            'tbl_suggest_plan_purchase.staff_status',
        ], '', []);


        $output = $result['output'];
        $rResult = $result['rResult'];
        $grand_total = 0;
        foreach ($rResult as $key => $aRow) {
            $row = array();
            $row[] = '<div class="text-center" style="width: 40px">' . (++$key) . '</div>';
            $row[] = '<div class="text-left" style="width: 120px"><a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_purchase/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal">' . $aRow['reference_no'] . '</a></div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['date']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . get_staff_full_name($aRow['staff_id']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . _dt($aRow['time_finish']) . '</div>';
            $row[] = '<div class="text-left" style="width: 110px">' . ($aRow['name_category_plan_time']) . '</div>';
            $row[] = '<div class="text-right" >' . formatMoney($aRow['grand_total']) . '</div>';
            if ($aRow['status'] == 0) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 1)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'1\' class=\'btn btn-success\'>' . lang('tnh_agree') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-danger po">' . lang('Chưa duyệt') . '</span></div>';
            } else if ($aRow['status'] == 1) {
                $_data = '<div class="text-center"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="' . lang('tnh_agree') . '" data-content="<p><a onclick=\'agree(this, ' . $aRow['id'] . ', 0)\' id=\'agree\' suggest_id=\'' . $aRow['id'] . '\' value=\'0\' class=\'btn btn-danger\'>' . lang('Hủy duyệt') . '</a><button class=\'btn po-close\'>' . lang('close') . '</button></p>" class="label label-success po">' . lang('Đã duyệt') . '</span></div>';
                $_data .= '<div style="margin-top: 5px"> Người duyệt: ' . get_staff_full_name($aRow['staff_status']) . '</div>';
            } else {
                $_data = '';
            }
            $row[] = '<div class="text-left" style="width: 100px">' . $_data . '</div>';
            $row[] = '<div class="text-left" style="width: 100px">' . get_staff_full_name($aRow['created_by']) . '</div>';

            $this->db->select('CONCAT(tblpurchases.prefix,"",tblpurchases.code) as code_purchases,tblpurchases.id as id_purchases');
            $this->db->from('tblpurchases');
            $this->db->where('tblpurchases.suggest_plan_purchase_id',$aRow['id']);
            $dtPurchase = $this->db->get()->result_array();
            $purchases = '';
            if(!empty($dtPurchase)){
                foreach ($dtPurchase as $k => $v){
                    $purchases .= '<a href="#" onclick="view_purchases(' . $v['id_purchases'] . '); return false;" >' . $v['code_purchases'] . '</a><br>';
                }
            }
            $isPerson = false;
            $row[] = '<div class="text-left"><a target="_blank"  onclick=\'create_purchase(' . $aRow['id'] . ')\' class="btn btn-info">Tạo YCMH</a></div>
                <div style="margin-top: 5px">' . $purchases . '</div>';

            $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/suggest_plan_purchase/view/' . $aRow['id']) . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('phiếu') . '</a>';

            $edit = $this->preEditSuggestPlanPurchase ? '<a href="' . base_url('admin/suggest_plan_purchase/detail/' . $aRow['id'] . '?type=' . $aRow['type'] . '') . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('phiếu') . '</a>' : '';

            $delete = $this->preDeleteSuggestPlanPurchase ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                <button href=\'' . base_url('admin/suggest_plan_purchase/delete/' . $aRow['id']) . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
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
            $row[] = '<div style="text-align: center">' . $actions . '</div>';
            $grand_total += $aRow['grand_total'];
            $output['aaData'][] = $row;
        }
        $output['grand_total'] = $grand_total;
        echo json_encode($output);
    }

    public function detail($id = 0)
    {
        $data = [];
        if ($this->input->post()) {
            if (empty($id)) {
                $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'required|is_unique[tbl_suggest_plan_purchase.reference_no]');
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Người lập kế thời"), 'required');
                $this->form_validation->set_rules('category_plan', lang("Mã nhóm kế hoạch"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    if ($this->type == 1) {
                        $reference_no = getReference('purchase_plan_purchase_npl');
                    } elseif ($this->type == 2) {
                        $reference_no = getReference('purchase_plan_purchase_vt');
                    } elseif ($this->type == 3) {
                        $reference_no = getReference('purchase_plan_purchase_machines');
                    }
                    $date = to_sql_date($this->input->post('date'), true);
                    // $date = to_sql_date($this->input->post('date'), true);
                    $time_finish = !empty($this->input->post('time_finish')) ? to_sql_date($this->input->post('time_finish'), true) : null;

                    $branch_id = $this->input->post('branch_id');
                    $staff_id = !empty($this->input->post('staff_id')) ? $this->input->post('staff_id') : 0;
                    $category_plan = !empty($this->input->post('category_plan')) ? $this->input->post('category_plan') : 0;
                    $note = ($this->input->post('note'));
                    $total_quantity = 0;
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $quantity_inventory = 0;
                            if ($this->type == 3) {
                                $machines_id = $this->input->post('machines_id')[$value];
                                $type_item = null;
                                $item_id = 0;
                                if (empty($machines_id)) {
                                    continue;
                                }
                            } else {
                                $machines_id = 0;
                                $items_id = $this->input->post('item_id')[$value];
                                $arr_item = explode('__', $items_id);
                                $type_item = $arr_item[0];
                                $item_id = $arr_item[1];
                                $item_type = null;
                                if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                                    $info_item = $this->products_model->rowProduct($item_id);
                                    $item_type = 'product';
                                } elseif ($type_item == "materials") {
                                    $info_item = $this->items_model->rowMaterial($item_id);
                                    $item_type = 'nvl';
                                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                    $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                    $item_type = 'tools';
                                }
                                if (empty($info_item)) {
                                    continue;
                                }
                                $quantityInventory = "(
                                    SELECT
                                        COALESCE(SUM(tblwarehouse_items.product_quantity),0) as product_quantity
                                    FROM tblwarehouse_items
                                    INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                                    WHERE tblwarehouse_items.id_items = $item_id AND tblwarehouse_items.type_items = '".$item_type."' AND tblwarehouse.supplier_id = 0
                                )";
                                $quantity_inventory = $this->db->query($quantityInventory)->row_array()['product_quantity'];
                            }

                            $suppliers_id = !empty($this->input->post('suppliers_id')[$value]) ? $this->input->post('suppliers_id')[$value] : 0;
                            $plan_id = !empty($this->input->post('plan_id')[$value]) ? $this->input->post('plan_id')[$value] : 0;
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $amount = $quantity * $price;
                            $items[] = [
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'machines_id' => $machines_id,
                                'quantity' => $quantity,
                                'suppliers_id' => $suppliers_id,
                                'price' => $price,
                                'amount' => $amount,
                                'quantity_inventory' => $quantity_inventory,
                                'plan_id' => $plan_id,
                            ];

                            $total_quantity += $quantity;
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
                        'category_plan' => $category_plan,
                        'staff_id' => $staff_id,
                        'time_finish' => $time_finish,
                        'type' => $this->type,
                        'note' => $note,
                        'total_quantity' => $total_quantity,
                        'created_by' => get_staff_user_id(),
                        'date_created' => date('Y-m-d H:i:s'),
                        'branch_id' => $branch_id,
                    ];
                    $this->db->insert('tbl_suggest_plan_purchase', $fields);
                    $id = $this->db->insert_id();
                    if ($id) {
                        if ($this->type == 1) {
                            if (getReference('purchase_plan_purchase_npl') == $reference_no) {
                                updateReference('purchase_plan_purchase_npl');
                            }
                        } elseif ($this->type == 2) {
                            if (getReference('purchase_plan_purchase_vt') == $reference_no) {
                                updateReference('purchase_plan_purchase_vt');
                            }
                        } elseif ($this->type == 3) {
                            if (getReference('purchase_plan_purchase_machines') == $reference_no) {
                                updateReference('purchase_plan_purchase_machines');
                            }
                        }

                        foreach ($items as $key => $value) {
                            $value['suggest_plan_purchase_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_purchase_item', $value);
                        }

                        if ($this->type == 1) {
                            $type_parent_obj = 'suggest_plan_purchase_nvl';
                            $content = lang('Thêm mới yêu cầu kế hoạch mua NPL');
                        } elseif ($this->type == 2) {
                            $type_parent_obj = 'suggest_plan_purchase_vt';
                            $content = lang('Thêm mới yêu cầu kế hoạch mua vật tư');
                        } elseif ($this->type == 3) {
                            $type_parent_obj = 'suggest_plan_purchase_machines';
                            $content = lang('Thêm mới yêu cầu kế hoạch mua thiết bị');
                        }

                        insertActivityLog([
                            'type_parent_obj' => $type_parent_obj,
                            'table_obj' => 'tbl_suggest_plan_purchase',
                            'id_obj' => $id,
                            'name_obj' => $reference_no,
                            'content' => $content . ' [' . $reference_no . ']',
                            'actions' => 'add'
                        ]);
                        $data['link'] = base_url('admin/suggest_plan_purchase?type=' . $this->type . '');
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
                $this->db->select('tbl_suggest_plan_purchase.*');
                $this->db->from('tbl_suggest_plan_purchase');
                $this->db->where('tbl_suggest_plan_purchase.id', $id);
                $dtData = $this->db->get()->row_array();
                if ($dtData['reference_no'] != $this->input->post('reference_no')) {
                    $this->form_validation->set_rules('reference_no', lang("Mã Phiếu"), 'trim|required|is_unique[tbl_suggest_plan_purchase.reference_no]');
                }
                $this->form_validation->set_rules('date', lang("date"), 'required');
                $this->form_validation->set_rules('staff_id', lang("Người lập kế thời"), 'required');
                $this->form_validation->set_rules('category_plan', lang("Mã nhóm kế hoạch"), 'required');
                $this->form_validation->set_rules('branch_id', lang("Chi nhánh"), 'required');
                if ($this->form_validation->run() == true) {
                    $date = to_sql_date($this->input->post('date'), true);
                    $branch_id = $this->input->post('branch_id');
                    $staff_id = !empty($this->input->post('staff_id')) ? $this->input->post('staff_id') : 0;
                    $category_plan = !empty($this->input->post('category_plan')) ? $this->input->post('category_plan') : 0;
                    // $time_finish = !empty($this->input->post('time_finish')) ? $this->input->post('time_finish') : null;
                    $time_finish = !empty($this->input->post('time_finish')) ? to_sql_date($this->input->post('time_finish'), true) : null;
                    $note = ($this->input->post('note'));
                    $total_quantity = 0;
                    $counter = $this->input->post('counter');
                    $items = [];
                    if (!empty($counter)) {
                        foreach ($counter as $key => $value) {
                            $quantity_inventory = 0;
                            if ($this->type == 3) {
                                $machines_id = $this->input->post('machines_id')[$value];
                                $type_item = null;
                                $item_id = 0;
                                if (empty($machines_id)) {
                                    continue;
                                }
                            } else {
                                $machines_id = 0;
                                $items_id = $this->input->post('item_id')[$value];
                                $arr_item = explode('__', $items_id);
                                $type_item = $arr_item[0];
                                $item_id = $arr_item[1];
                                $item_type = null;
                                if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                                    $info_item = $this->products_model->rowProduct($item_id);
                                    $item_type = 'product';
                                } elseif ($type_item == "materials") {
                                    $info_item = $this->items_model->rowMaterial($item_id);
                                    $item_type = 'nvl';
                                } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                                    $info_item = $this->tools_supplies_model->rowToolsSupplies($item_id);
                                    $item_type = 'tools';
                                }
                                if (empty($info_item)) {
                                    continue;
                                }

                                $quantityInventory = "(
                                    SELECT
                                        COALESCE(SUM(tblwarehouse_items.product_quantity),0) as product_quantity
                                    FROM tblwarehouse_items
                                    INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
                                    WHERE tblwarehouse_items.id_items = $item_id AND tblwarehouse_items.type_items = '".$item_type."' AND tblwarehouse.supplier_id = 0
                                )";
                                $quantity_inventory = $this->db->query($quantityInventory)->row_array()['product_quantity'];
                            }

                            $suggest_plan_purchase_item_id = !empty($this->input->post('suggest_plan_purchase_item_id')[$value]) ? $this->input->post('suggest_plan_purchase_item_id')[$value] : 0;
                            $suppliers_id = !empty($this->input->post('suppliers_id')[$value]) ? $this->input->post('suppliers_id')[$value] : 0;
                            $plan_id = !empty($this->input->post('plan_id')[$value]) ? $this->input->post('plan_id')[$value] : 0;
                            $quantity = number_unformat($this->input->post('quantity')[$value]);
                            $price = number_unformat($this->input->post('price')[$value]);
                            $amount = $quantity * $price;

                            $items[] = [
                                'id' => $suggest_plan_purchase_item_id,
                                'type_item' => $type_item,
                                'item_id' => $item_id,
                                'machines_id' => $machines_id,
                                'quantity' => $quantity,
                                'suppliers_id' => $suppliers_id,
                                'price' => $price,
                                'amount' => $amount,
                                'quantity_inventory' => $quantity_inventory,
                                'plan_id' => $plan_id,
                            ];

                            $total_quantity += $quantity;
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
                        'category_plan' => $category_plan,
                        'staff_id' => $staff_id,
                        'time_finish' => $time_finish,
                        'type' => $this->type,
                        'note' => $note,
                        'total_quantity' => $total_quantity,
                        'branch_id' => $branch_id,
                        'updated_by' => get_staff_user_id(),
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                    $this->db->where('id', $id);
                    $success = $this->db->update('tbl_suggest_plan_purchase', $fields);
                    if ($success) {
                        $this->db->where('suggest_plan_purchase_id', $id);
                        $this->db->delete('tbl_suggest_plan_purchase_item');
                        foreach ($items as $key => $value) {
                            $value['suggest_plan_purchase_id'] = $id;
                            $this->db->insert('tbl_suggest_plan_purchase_item', $value);
                        }
                        if ($this->type == 1) {
                            $type_parent_obj = 'suggest_plan_purchase_nvl';
                            $content = lang('Sửa phiếu yêu cầu kế hoạch mua NPL');
                        } elseif ($this->type == 2) {
                            $type_parent_obj = 'suggest_plan_purchase_vt';
                            $content = lang('Sửa phiếu yêu cầu kế hoạch mua vật tư');
                        } elseif ($this->type == 3) {
                            $type_parent_obj = 'suggest_plan_purchase_machines';
                            $content = lang('Sửa phiếu yêu cầu kế hoạch mua thiết bị');
                        }
                        insertActivityLog([
                            'type_parent_obj' => $type_parent_obj,
                            'table_obj' => 'tbl_suggest_plan_purchase',
                            'id_obj' => $id,
                            'name_obj' => $dtData['reference_no'],
                            'content' => $content . ' [' . $dtData['reference_no'] . ']',
                            'actions' => 'edit'
                        ]);
                        $data['link'] = base_url('admin/suggest_plan_purchase?type=' . $this->type . '');
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
                if (!$this->preAddSuggestPlanPurchase) {
                    accessDenied(true);
                }
                if ($this->type == 1) {
                    $data['titleItem'] = lang('Nguyên Phụ Liệu');
                    $data['title'] = lang('dt_add_suggest_plan_purchase_nvl');
                    $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_purchase?type=1'), 'page' => lang('suggest_plan_purchase_nvl')), array('link' => '#', 'page' => lang('dt_add_suggest_plan_purchase_nvl'))];
                } elseif ($this->type == 2) {
                    $data['titleItem'] = lang('Vật Tư');
                    $data['title'] = lang('dt_add_suggest_plan_purchase_vt');
                    $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_purchase?type=2'), 'page' => lang('suggest_plan_purchase_vt')), array('link' => '#', 'page' => lang('dt_add_suggest_plan_purchase_vt'))];
                } elseif ($this->type == 3) {
                    $data['titleItem'] = lang('Thiết bị');
                    $data['title'] = lang('dt_add_suggest_plan_purchase_machines');
                    $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_purchase?type=3'), 'page' => lang('dt_suggest_plan_purchase_machines')), array('link' => '#', 'page' => lang('dt_add_suggest_plan_purchase_machines'))];
                }
            } else {
                if (!$this->preEditSuggestPlanPurchase) {
                    accessDenied(true);
                }
                $this->db->select('tbl_suggest_plan_purchase.*');
                $this->db->from('tbl_suggest_plan_purchase');
                $this->db->where('tbl_suggest_plan_purchase.id', $id);
                $dtData = $this->db->get()->row_array();

                if ($dtData['status'] == 1) {
                    set_alert('danger',  'Phiếu đã duyệt không thể sửa !');
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $this->db->select('tbl_suggest_plan_purchase_item.*,tbl_productions_plan.reference_no');
                $this->db->from('tbl_suggest_plan_purchase_item');
                $this->db->join('tbl_productions_plan','tbl_productions_plan.id = tbl_suggest_plan_purchase_item.plan_id','left');
                $this->db->where('tbl_suggest_plan_purchase_item.suggest_plan_purchase_id', $id);
                $dtItems = $this->db->get()->result_array();

                $data['dtData'] = $dtData;
                $data['dtData']['plan_id'] = $this->db->select('GROUP_CONCAT(plan_id) as list_plan_id')->get_where('tbl_suggest_plan_purchase_item', ['suggest_plan_purchase_id' => $id])->row('list_plan_id');
                $data['dtItems'] = $dtItems;
                if ($this->type == 1) {
                    $data['titleItem'] = lang('Nguyên Phụ Liệu');
                    $data['title'] = lang('dt_edit_suggest_plan_purchase_nvl');
                    $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_purchase?type=1'), 'page' => lang('suggest_plan_purchase_nvl')), array('link' => '#', 'page' => lang('dt_edit_suggest_plan_purchase_nvl'))];
                } elseif ($this->type == 2) {
                    $data['titleItem'] = lang('Vật Tư');
                    $data['title'] = lang('dt_edit_suggest_plan_purchase_vt');
                    $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_purchase?type=2'), 'page' => lang('suggest_plan_purchase_vt')), array('link' => '#', 'page' => lang('dt_edit_suggest_plan_purchase_vt'))];
                } elseif ($this->type == 3) {
                    $data['titleItem'] = lang('Thiết bị');
                    $data['title'] = lang('dt_edit_suggest_plan_purchase_machines');
                    $data['breadcrumb'] = [array('link' => base_url('admin/suggest_plan_purchase?type=3'), 'page' => lang('dt_suggest_plan_purchase_machines')), array('link' => '#', 'page' => lang('dt_edit_suggest_plan_purchase_machines'))];
                }
            }
        }
        $data['employees'] = $this->manufactures_model->getAllStaff();
        $data['id'] = $id;
        $data['type'] = $this->type;
        if ($this->type == 1) {
            $data['reference_no'] = getReference('purchase_plan_purchase_npl');
        } elseif ($this->type == 2) {
            $data['reference_no'] = getReference('purchase_plan_purchase_vt');
        } elseif ($this->type == 3) {
            $data['reference_no'] = getReference('purchase_plan_purchase_machines');
        }
        $data['dtCategoryPlanTime'] = get_table_where('tbl_category_plan_time');
        $this->load->view('admin/suggest_plan_purchase/detail', $data);
    }

    public function view($id)
    {
        $data = [];

        $this->db->select('tbl_suggest_plan_purchase.*');
        $this->db->from('tbl_suggest_plan_purchase');
        $this->db->where('tbl_suggest_plan_purchase.id', $id);
        $dtData = $this->db->get()->row_array();
        if ($dtData['type'] == 1) {
            $data['title'] = lang('dt_view_suggest_plan_purchase_nvl');
        } elseif ($dtData['type'] == 2) {
            $data['title'] = lang('dt_view_suggest_plan_purchase_vt');
        } elseif ($dtData['type'] == 3) {
            $data['title'] = lang('dt_view_suggest_plan_purchase_machines');
        }

        $this->db->select('tbl_suggest_plan_purchase_item.*,
            tblsuppliers.company as company,
            tbl_productions_plan.reference_no as reference_no
        ');
        $this->db->from('tbl_suggest_plan_purchase_item');
        $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_plan_purchase_item.suppliers_id', 'left');
        $this->db->join('tbl_productions_plan','tbl_productions_plan.id = tbl_suggest_plan_purchase_item.plan_id','left');
        $this->db->where('tbl_suggest_plan_purchase_item.suggest_plan_purchase_id', $id);
        $dtDataItems = $this->db->get()->result_array();

        $data['dtData'] = $dtData;
        $data['dtDataItems'] = $dtDataItems;
        $this->load->view('admin/suggest_plan_purchase/view', $data);
    }

    public function agree()
    {
        if (!$this->preApproveSuggestPlanPurchase) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }

        $data = [];
        $suggest_id = $this->input->post('suggest_id');
        $status = $this->input->post('status');

        $this->db->select('tbl_suggest_plan_purchase.*');
        $this->db->from('tbl_suggest_plan_purchase');
        $this->db->where('tbl_suggest_plan_purchase.id', $suggest_id);
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
            $up = $this->db->update('tbl_suggest_plan_purchase', $options);
            if ($up) {

                if ($dtData['type'] == 1) {
                    $type_parent_obj = 'suggest_plan_purchase_nvl';
                    $content = lang('Duyệt phiếu yêu cầu kế hoạch mua NPL');
                } elseif ($dtData['type'] == 2) {
                    $type_parent_obj = 'suggest_plan_purchase_vt';
                    $content = lang('Duyệt phiếu yêu cầu kế hoạch mua vật tư');
                } elseif ($dtData['type'] == 3) {
                    $type_parent_obj = 'suggest_plan_purchase_machines';
                    $content = lang('Duyệt phiếu yêu cầu kế hoạch mua thiết bị');
                }
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
        if (!$this->preDeleteSuggestPlanPurchase) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die();
        }
        $data_purchase = get_table_where('tblpurchases', array('suggest_plan_purchase_id' => $id), '', 'row_array');
        if (!empty($data_purchase)) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu này đã tạo YCMH rồi, Không xóa');
            echo responseData($data);
            return;
        }
        $data = [];
        $this->db->select('tbl_suggest_plan_purchase.*');
        $this->db->from('tbl_suggest_plan_purchase');
        $this->db->where('tbl_suggest_plan_purchase.id', $id);
        $dtData = $this->db->get()->row_array();
        if (empty($dtData)) {
            $data['result'] = 0;
            $data['message'] = lang('not_data_exists');
            echo json_encode($data);
            die();
        }

        if ($dtData['status'] == 1) {
            $data['result'] = 0;
            $data['message'] = lang('Phiếu đã duyệt không thể xóa !');
            echo json_encode($data);
            die();
        }

        $this->db->where('id', $id);
        $success = $this->db->delete('tbl_suggest_plan_purchase');
        if ($success) {
            $this->db->where('tbl_suggest_plan_purchase_item.suggest_plan_purchase_id', $id);
            $this->db->delete('tbl_suggest_plan_purchase_item');

            if ($dtData['type'] == 1) {
                $type_parent_obj = 'suggest_plan_purchase_nvl';
                $content = lang('Xóa phiếu yêu cầu kế hoạch mua NPL');
            } elseif ($dtData['type'] == 2) {
                $type_parent_obj = 'suggest_plan_purchase_vt';
                $content = lang('Xóa phiếu yêu cầu kế hoạch mua vật tư');
            } elseif ($dtData['type'] == 3) {
                $type_parent_obj = 'suggest_plan_purchase_machines';
                $content = lang('Xóa phiếu yêu cầu kế hoạch mua thiết bị');
            }
            insertActivityLog([
                'type_parent_obj' => $type_parent_obj,
                'table_obj' => 'tbl_suggest_plan_purchase',
                'id_obj' => $id,
                'name_obj' => $dtData['reference_no'],
                'content' => $content . ' [' . $dtData['reference_no'] . ']',
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

    public function searchMachines($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_machines.id as id, 
            tbl_machines.name as text
        ', false);
        $this->db->from('tbl_machines');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_machines.name', $term);
            $this->db->or_like('tbl_machines.code', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tbl_machines', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
        }
        echo json_encode($data);
    }

    public function searchProductionReports($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblproduction_report.id as id, 
            tblproduction_report.name_report as text
        ', false);
        $this->db->from('tblproduction_report');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblproduction_report.name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Phiếu báo cáo không phù hợp'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tblproduction_report', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name_report']];
        }
        echo json_encode($data);
    }

    public function searchSuppliers($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tblsuppliers.id as id, 
            tblsuppliers.company as text
        ', false);
        $this->db->from('tblsuppliers');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tblsuppliers.company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Nhà cung cấp'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tblsuppliers', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['company']];
        }
        echo json_encode($data);
    }

    public function searchDeliveryRecords($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_delivery_records.id as id, 
            tbl_delivery_records.reference_no as text
        ', false);
        $this->db->from('tbl_delivery_records');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_delivery_records.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Biên bản nghiệm thu'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tbl_delivery_records', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['reference_no']];
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
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
                tbl_suggest_plan_purchase.id as id,
                tbl_suggest_plan_purchase.reference_no as reference_no,
                tbl_suggest_plan_purchase.date as date,
                tbl_suggest_plan_purchase.staff_id as staff_id,
                tbl_suggest_plan_purchase.time_finish as time_finish,
                tbl_category_plan_time.name as name_category_plan,
                tblsuppliers.code as supplier_code,
                tblsuppliers.company as company,
                tbl_suggest_plan_purchase_item.quantity as quantity,
                tbl_suggest_plan_purchase_item.item_id as item_id,
                tbl_suggest_plan_purchase_item.type_item as type_item,
                tbl_suggest_plan_purchase_item.price as price,
                tbl_suggest_plan_purchase_item.amount as amount,
            ');
            $this->db->from('tbl_suggest_plan_purchase');
            $this->db->join('tbl_suggest_plan_purchase_item', 'tbl_suggest_plan_purchase_item.suggest_plan_purchase_id = tbl_suggest_plan_purchase.id');
            $this->db->join('tbl_category_plan_time', 'tbl_category_plan_time.id = tbl_suggest_plan_purchase.category_plan', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_plan_purchase_item.suppliers_id');


            $this->db->where('tbl_suggest_plan_purchase.type =', $type);

            if (!$this->preViewSuggestPlanPurchase) {
                $this->db->where('(tbl_suggest_plan_purchase.created_by = ' . get_staff_user_id() . ' OR tbl_suggest_plan_purchase.staff_id = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_purchase.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_purchase.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_plan_purchase.id desc');
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
                $type == 1 ? ('PHIẾU YÊU CẦU KẾ HOẠCH MUA NPL') : ('PHIẾU YÊU CẦU KẾ HOẠCH MUA VẬT TƯ')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Người Lập Kế Thời')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Thời Gian Hoàn Thành')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Mã Nhóm Kế Hoạch')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', $type == 1 ? 'Mã NPL' : 'Mã Vật Tư')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', $type == 1 ? 'Tên NPL' : 'Tên Vật Tư')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', $type == 1 ? 'Nhóm NPL' : 'Nhóm Vật Tư')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', $type == 1 ? 'Tên Mã Chủng Loại NPL' : 'Tên Mã Chủng Loại Vật Tư')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Mã NCC');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Nhà Cung Cấp')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Số Lượng')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Đơn Giá')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Thành Tiền')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:P$sttRow")->applyFromArray([
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
                    $item_id = $value['item_id'];
                    $type_item = $value['type_item'];
                    $info = null;
                    if ($type_item == "products" || $type_item == 'semi_products' || $type_item == 'semi_products_outside') {
                        $info = $this->products_model->rowProduct($item_id);
                        $dtCategory = get_table_where('tbl_category_products', ['id' => $info['category_id']], '', 'row_array');
                        $dtSpecies = get_table_where('tbl_species', ['id' => $info['species']], '', 'row_array');
                    } else if ($type_item == "materials") {
                        $info = $this->items_model->rowMaterial($item_id);
                        $dtCategory = get_table_where('tbl_category_items', ['id' => $info['category_id']], '', 'row_array');
                        $dtSpecies = get_table_where('tbl_species', ['id' => $info['species']], '', 'row_array');
                    } elseif ($type_item == "tools_supplies" || $type_item == 'supplies') {
                        $info = $this->tools_supplies_model->rowToolsSupplies($item_id);
                        $dtCategory = get_table_where('tbl_category_tools_supplies', ['id' => $info['category_id']], '', 'row_array');
                        $dtSpecies = null;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue("A$rowBegin", (++$key));
                    $objPHPExcel->getActiveSheet()->setCellValue("B$rowBegin", $value['reference_no']);
                    $objPHPExcel->getActiveSheet()->setCellValue("C$rowBegin", _dt($value['date']));
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['time_finish']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_category_plan']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $info['code'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $info['name'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", $dtCategory['name'])->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin", !empty($dtSpecies) ? $dtSpecies['name'] : '')->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['supplier_code'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['company']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['quantity']))->getStyle("M$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity']));
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['price']))->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($value['amount']))->getStyle("O$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['amount']));


                    if (!empty($value['barcode'])) {
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_plan_purchase||' . $value['id'];
                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_suggest_plan_purchase', ['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_plan_purchase/';
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
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('P' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", '')->getStyle("P$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:P$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("M$rowBegin:M$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = $type == 1 ? lang('phieu_yeu_cau_ke_hoach_mua_npl') : lang('phieu_yeu_cau_ke_hoach_mua_vat_tu') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
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

    public function exportExcelNew()
    {
        if ($this->input->post('export_excel')) {
            ini_set('memory_limit', '3500M');
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');
            // print_arrays($this->input->post());
            $start_date_search = $this->input->post('start_date_search');
            $end_date_search = $this->input->post('end_date_search');
            $type = $this->input->post('type');
            $style_excel = style_excel();
            $cloumns_excel = cloumns_excel();

            $this->db->select('
                tbl_suggest_plan_purchase.id as id,
                tbl_suggest_plan_purchase.reference_no as reference_no,
                tbl_suggest_plan_purchase.date as date,
                tbl_suggest_plan_purchase.staff_id as staff_id,
                tbl_suggest_plan_purchase.time_finish as time_finish,
                tbl_category_plan_time.name as name_category_plan,
                tblsuppliers.code as supplier_code,
                tblsuppliers.company as company,
                tbl_suggest_plan_purchase_item.quantity as quantity,
                tbl_machines.code as code_machines,
                tbl_machines.name as name_machines,
                tbl_suggest_plan_purchase_item.price as price,
                tbl_suggest_plan_purchase_item.amount as amount,
            ');
            $this->db->from('tbl_suggest_plan_purchase');
            $this->db->join('tbl_suggest_plan_purchase_item', 'tbl_suggest_plan_purchase_item.suggest_plan_purchase_id = tbl_suggest_plan_purchase.id');
            $this->db->join('tbl_category_plan_time', 'tbl_category_plan_time.id = tbl_suggest_plan_purchase.category_plan', 'left');
            $this->db->join('tbl_machines', 'tbl_machines.id = tbl_suggest_plan_purchase_item.machines_id', 'left');
            $this->db->join('tblsuppliers', 'tblsuppliers.id = tbl_suggest_plan_purchase_item.suppliers_id');


            $this->db->where('tbl_suggest_plan_purchase.type = 3');

            if (!$this->preViewSuggestPlanPurchase) {
                $this->db->where('(tbl_suggest_plan_purchase.created_by = ' . get_staff_user_id() . ' OR tbl_suggest_plan_purchase.staff_id = ' . get_staff_user_id() . ')');
            }

            if (!empty($start_date_search)) {
                $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
                $this->db->where("tbl_suggest_plan_purchase.date >= '" . $start_date_search . "'");
            }

            if (!empty($end_date_search)) {
                $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
                $this->db->where("tbl_suggest_plan_purchase.date <= '" . $end_date_search . "'");
            }

            $this->db->order_by('tbl_suggest_plan_purchase.id desc');
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
                ('PHIẾU YÊU CẦU KẾ HOẠCH MUA THIẾT BỊ')
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
            $sttRow = 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $sttRow . '', 'STT');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $sttRow . '', 'Mã Số Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $sttRow . '', 'Ngày Lập Phiếu');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $sttRow . '', 'Người Lập Kế Thời')->getStyle("D$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $sttRow . '', 'Thời Gian Hoàn Thành')->getStyle("E$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $sttRow . '', 'Mã Nhóm Kế Hoạch')->getStyle("F$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $sttRow . '', 'Mã Thiết Bị')->getStyle("G$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $sttRow . '', 'Tên Thiết Bị')->getStyle("H$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $sttRow . '', 'Nhóm Thiết Bị')->getStyle("I$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $sttRow . '', 'Tên Mã Chủng Loại Thiết Bị')->getStyle("J$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $sttRow . '', 'Mã NCC');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $sttRow . '', 'Nhà Cung Cấp')->getStyle("L$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $sttRow . '', 'Số Lượng')->getStyle("M$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $sttRow . '', 'Đơn Giá')->getStyle("N$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $sttRow . '', 'Thành Tiền')->getStyle("O$sttRow")->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $sttRow . '', 'QR');
            $objPHPExcel->getActiveSheet()->getStyle("A$sttRow:P$sttRow")->applyFromArray([
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
                    $objPHPExcel->getActiveSheet()->setCellValue("D$rowBegin", get_staff_full_name($value['staff_id']))->getStyle("D$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("E$rowBegin", ($value['time_finish']))->getStyle("E$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("F$rowBegin", ($value['name_category_plan']))->getStyle("F$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("G$rowBegin", $value['code_machines'])->getStyle("G$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("H$rowBegin", $value['name_machines'])->getStyle("H$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("I$rowBegin", '')->getStyle("I$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("J$rowBegin",  '')->getStyle("J$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("K$rowBegin", $value['supplier_code'])->getStyle("K$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("L$rowBegin", ($value['company']))->getStyle("L$rowBegin")->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue("M$rowBegin", ($value['quantity']))->getStyle("M$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['quantity']));
                    $objPHPExcel->getActiveSheet()->setCellValue("N$rowBegin", ($value['price']))->getStyle("N$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['price']));
                    $objPHPExcel->getActiveSheet()->setCellValue("O$rowBegin", ($value['amount']))->getStyle("O$rowBegin")->getNumberFormat()->setFormatCode(formatMoneyExcel($value['amount']));


                    if (!empty($value['barcode'])) {
                        $code = $value['barcode'];
                    } else {
                        $code = 'suggest_plan_purchase||' . $value['id'];
                        $this->db->where('id', $value['id']);
                        $this->db->update('tbl_suggest_plan_purchase', ['barcode' => $code]);
                    }
                    $qr = vn_to_str(str_replace('||', '__', $code));
                    $folder = FCPATH . 'uploads/suggest_plan_purchase/';
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
                        $objDrawing1->setWidth(80);
                        $objDrawing1->setHeight(53);
                        $objDrawing1->setOffsetX(3);
                        $objDrawing1->setOffsetY(2);
                        $objDrawing1->setCoordinates('P' . ($rowBegin));
                    }
                    $objPHPExcel->getActiveSheet()->getRowDimension($rowBegin)->setRowHeight(42);
                    $objPHPExcel->getActiveSheet()->setCellValue("P$rowBegin", '')->getStyle("P$rowBegin")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:P$rowBegin")->applyFromArray([
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("A$rowBegin:A$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                    $objPHPExcel->getActiveSheet()->getStyle("M$rowBegin:M$rowBegin")->applyFromArray([
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ]);
                }
            }
            $filename = lang('phieu_yeu_cau_ke_hoach_mua_thiet_bi') . '.xls';
            $objPHPExcel->getActiveSheet()->freezePane('A1');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
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
    function create_purchase($id = '')
    {
        if (!empty($id)) {
            $data_purchase = get_table_where('tblpurchases', array('suggest_plan_purchase_id' => $id), '', 'row_array');
            if (!empty($data_purchase)) {
                $data['result'] = 0;
                $data['message'] = lang('Phiếu này đã tạo YCMH rồi, Không thể tạo lại');
                echo responseData($data);
                return;
            }
            $main = get_table_where('tbl_suggest_plan_purchase', ['id' => $id], '', 'row_array');
            if (($main['status'] == 0)) {
                $data['result'] = 0;
                $data['message'] = lang('Phiếu chưa duyệt, Không thể tạo YCMH');
                echo responseData($data);
                return;
            }
            if ($main['type'] != 3) {
                $itemss = get_table_where('tbl_suggest_plan_purchase_item', ['suggest_plan_purchase_id' => $id]);
                $itemData = [];
                if (!empty($itemss)){
                    foreach ($itemss as $k => $v){
                        $itemData[$v['plan_id']][] = $v;
                    }
                }
                if (!empty($itemData)){
                    foreach ($itemData as $key => $value){
                        $itemss = $value;
                        $purchase = array(
                            'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
                            'prefix' => get_option('prefix_purchase'),
                            'name_purchase' => 'YÊU CẦU KẾ HOẠCH MUA NPL',
                            'explanation' => '',
                            'date' => date('Y-m-d'),
                            'delivery_date' => date('Y-m-d'),
                            'staff_create' => get_staff_user_id(),
                            'date_create' => date('Y:m:d H:i:s'),
                            'status' => 2,
                            'type' => 1,
                            'id_plan' => $key,
                            'type_plan' => 1,
                            'suggest_plan_purchase_id' => $id,
                        );
                        if ($this->db->insert('tblpurchases', $purchase)) {
                            $idd = $this->db->insert_id();
                            log_activity('Purchase Insert [ID: ' . $id . ']');
                            $count = 0;
                        }
                        if ($idd) {
                            foreach ($itemss as $kk => $item) {
                                if (!empty($item['id'])) {
                                    $type = $item['type_item'];
                                    if ($type == 'products') {
                                        $type = 'product';
                                    } elseif ($type == 'materials') {
                                        $type = 'nvl';
                                    } elseif ($type == 'tools_supplies') {
                                        $type = 'tools';
                                    } elseif ($type == 'semi_products') {
                                        $type = 'product';
                                    }
                                    $items = array(
                                        'purchases_id' => $idd,
                                        'product_id' => $item['item_id'],
                                        'quantity' => $item['quantity'],
                                        'quantity_net' => $item['quantity'],
                                        'type' => $type,
                                        'id_plan' => $key,
                                        'note' => '',
                                    );
                                    if ($this->db->insert('tblpurchases_items', $items)) {

                                        log_activity('Purchase items insert [ID Purchase: ' . $idd . ', ID Product: ' . $items['product_id'] . ']');
                                        $count++;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $itemss = get_table_where('tbl_suggest_plan_purchase_item', ['suggest_plan_purchase_id' => $id]);
                $purchase = array(
                    'code' => sprintf('%06d', ch_getMaxID('id', 'tblpurchases') + 1),
                    'prefix' => get_option('prefix_purchase'),
                    'name_purchase' => 'YÊU CẦU KẾ HOẠCH MUA NPL',
                    'explanation' => '',
                    'date' => date('Y-m-d'),
                    'delivery_date' => date('Y-m-d'),
                    'staff_create' => get_staff_user_id(),
                    'date_create' => date('Y:m:d H:i:s'),
                    'status' => 2,
                    'type' => 1,
                    'id_plan' => 0,
                    'type_plan' => 0,
                    'suggest_plan_purchase_id' => $id,
                );
                if ($this->db->insert('tblpurchases', $purchase)) {
                    $idd = $this->db->insert_id();
                    log_activity('Purchase Insert [ID: ' . $id . ']');
                    $count = 0;
                }
                if ($idd) {
                    foreach ($itemss as $key => $item) {
                        if (!empty($item['id'])) {
                            $type = 'nvl';
                            $items = array(
                                'purchases_id' => $idd,
                                'product_id' => $item['machines_id'],
                                'quantity' => $item['quantity'],
                                'quantity_net' => $item['quantity'],
                                'type' => $type,
                                'note' => '',
                            );
                            if ($this->db->insert('tblpurchases_items', $items)) {

                                log_activity('Purchase items insert [ID Purchase: ' . $idd . ', ID Product: ' . $items['product_id'] . ']');
                                $count++;
                            }
                        }
                    }
                }
            }

            if ($idd) {
                $data['result'] = 1;
                $data['message'] = lang('Tạo thành công');
                echo responseData($data);
                return;
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('Tạo không thành công');
            echo responseData($data);
            return;
        }
    }
    function get_price()
    {
        $suppliers_id = $this->input->post('suppliers_id');
        $item_id = $this->input->post('item_id');
        $machines_id = $this->input->post('machines_id');
        $price = 0;
        if (!empty($item_id) && !empty($suppliers_id)) {
            $this->db->where('tblsuppliers_price.supplier_id', $suppliers_id);
            $this->db->limit(1);
            $this->db->order_by('tblsuppliers_price.id DESC');
            $suppliers_price = $this->db->get('tblsuppliers_price')->row();
            if (!empty($suppliers_price)) {
                $item_id = explode('__', $item_id);
                $type_items = $item_id[0];
                if ($type_items == 'products') {
                    $type_items = 'product';
                } else
                    if ($type_items == 'materials') {
                    $type_items = 'nvl';
                } else
                    if ($type_items == 'tools_supplies') {
                    $type_items = 'tools';
                } else
                    if ($type_items == 'semi_products') {
                    $type_items = 'product';
                }
                $this->db->where('tblsuppliers_price_detail.supplier_price_id', $suppliers_price->id);
                $this->db->where('tblsuppliers_price_detail.product_id', $item_id[1]);
                $this->db->where('tblsuppliers_price_detail.product_type', $type_items);
                $table_price = $this->db->get('tblsuppliers_price_detail')->row_array();
                if (!empty($table_price)) {
                    $price = $table_price['price'];
                }
            }
        } elseif (!empty($machines_id) && !empty($suppliers_id)) {
            $this->db->where('tblsuppliers_price.supplier_id', $suppliers_id);
            $this->db->limit(1);
            $this->db->order_by('tblsuppliers_price.id DESC');
            $suppliers_price = $this->db->get('tblsuppliers_price')->row();
            if (!empty($suppliers_price)) {
                $this->db->where('tblsuppliers_price_detail.supplier_price_id', $suppliers_price->id);
                $this->db->where('tblsuppliers_price_detail.product_id', $machines_id);
                $this->db->where('tblsuppliers_price_detail.product_type', 'nvl');
                $table_price = $this->db->get('tblsuppliers_price_detail')->row_array();
                if (!empty($table_price)) {
                    $price = $table_price['price'];
                }
            }
        }
        echo $price;
    }
    public function searchMachinesItems($id = 0)
    {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select('
            tbl_materials.id as id, 
            tbl_materials.name as text,
            tbl_materials.code as code,
            tbl_materials.name as name,
        ', false);
        $this->db->from('tbl_materials');
        $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id', 'INNER');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_materials.name', $term);
            $this->db->or_like('tbl_materials.code', $term);
            $this->db->group_end();
        }
        $this->db->where('tbl_category_items.is_machines', 1);
        $this->db->limit($limit);
        $pod = $this->db->get()->result_array();

        $data['results'][] = ['text' => lang('Thiết bị'), 'children' => $pod];
        if (!empty($id)) {
            $dtMachines = get_table_where('tbl_materials', ['id' => $id], '', 'row_array');
            $data['row'] = ['id' => $dtMachines['id'], 'text' => $dtMachines['name']];
        }
        echo json_encode($data);
    }

    public function searchPlans($id = 0) {
        $term = $this->input->get('term');
        $limit = get_option('select2_limit');
        $this->db->select(['tbl_productions_plan.id as id', 'CONCAT(tbl_productions_plan.reference_no) as text'], false);
        $this->db->from('tbl_productions_plan');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tbl_productions_plan.reference_no', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $dtResult = $this->db->get()->result_array();
        $data['results'][] = ['text' => lang('Kế hoạch sản xuất'), 'children' => $dtResult];
        if (!empty($id)) {
            $id = explode('_', $id);
            $this->db->where_in('id', $id);
            $dtData = $this->db->get('tbl_productions_plan')->result_array();
            $data['row'] = [];
            foreach ($dtData as $key => $value) {
                $data['row'][] = ['id' => $value['id'], 'text' => $value['reference_no']];
            }
        }
        echo json_encode($data);die();
    }

    public function searchMaterialByPlan()
    {
        $term = $this->input->get('term');
        $plan_id = !empty($this->input->get('plan_id')) ? $this->input->get('plan_id') : 0;
        $limit = get_option('select2_limit');

        $tbTransferWarehouse = "(
            SELECT
                tbltransfer_warehouse_detail.id_items as id_items,
                tbltransfer_warehouse.productions_capacity_id as plan_id,
                SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity_net
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse.productions_capacity_id IN (".$plan_id.")
            GROUP BY tbltransfer_warehouse_detail.id_items,tbltransfer_warehouse.productions_capacity_id
        ) tb_transfer_detail";

        $tbPurchases = "(
            SELECT
                tblpurchases_items.product_id as product_id,
                tblpurchases.id_plan as plan_id,
                SUM(tblpurchases_items.quantity) as quantity
            FROM tblpurchases
            INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
            WHERE tblpurchases.id_plan IN (".$plan_id.") AND tblpurchases_items.type = 'product'
            GROUP BY tblpurchases_items.product_id,tblpurchases.id_plan
        ) tb_purchases";

        $tbProductionsPlanCompensation = "(
            SELECT
                tbl_productions_plan_compensation.item_id, 
                tbl_productions_plan_compensation.item_type,
                tbl_productions_plan_compensation.productions_plan_id as plan_id,
                tbl_productions_plan_compensation.quantity_primary as quantity_primary
            FROM tbl_productions_plan_compensation
            WHERE tbl_productions_plan_compensation.productions_plan_id IN (".$plan_id.")
            GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type,tbl_productions_plan_compensation.productions_plan_id
        ) tb_productions_plan_compensation";

        $this->db->select('
            tbl_productions_plan_bom.item_id as item_id,
            tbl_productions_plan_bom.productions_plan_id as plan_id,
            tbl_productions_plan.reference_no as reference_no,
            "products" as item_type,
            tbl_productions_plan_bom.item_type as item_type_root,
            CONCAT("products__", tbl_products.id) as id,
            CONCAT(tbl_products.name, "(", tbl_products.code, ")") as text,
            tbl_products.name as item_name,
            tbl_products.code as item_code,
            SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0) as quantity_primary,
            tb_transfer_detail.quantity_net as quantity_net,
            tblunits.unit as unit_name,
            tb_purchases.quantity as quantity_purchase,
            tbl_productions_plan_bom.unit_parent_id as unit_parent_id,
            unit_parent.unit as unit_parent_name,
            tbl_productions_plan_bom.quantity_exchange as quantity_exchange,
            tblunits.unit as unit_name_stock,
            1 as exchange_standard_unit,
            1 as exchange_unit,
            tbl_products.conversion_quantity_unit as conversion_quantity_unit
        ', false);
        $this->db->from('tbl_productions_plan_bom');
        $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_plan_bom.productions_plan_id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_productions_plan_bom.item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_productions_plan_bom.unit_parent_id', 'left');
        $this->db->join($tbTransferWarehouse, 'tb_transfer_detail.id_items = tbl_productions_plan_bom.item_id', 'left');
        $this->db->join($tbPurchases, 'tb_purchases.product_id = tbl_productions_plan_bom.item_id', 'left');
        $this->db->join($tbProductionsPlanCompensation, 'tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type', 'left');
        $this->db->where('tbl_productions_plan_bom.productions_plan_id IN ('.$plan_id.')');
        $this->db->where_in('tbl_productions_plan_bom.item_type', ['semi_products_outside']);
        $this->db->group_by('tbl_productions_plan_bom.item_id,tbl_productions_plan_bom.productions_plan_id');
        $semi_product_outside = $this->db->get()->result_array();

        $tbTransferWarehouse = "(
            SELECT
                tbltransfer_warehouse_detail.id_items as id_items,
                tbltransfer_warehouse.productions_capacity_id as plan_id,
                SUM(tbltransfer_warehouse_detail.quantity_net) as quantity_net
            FROM tbltransfer_warehouse
            INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
            WHERE tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse.productions_capacity_id IN (".$plan_id.")
            GROUP BY tbltransfer_warehouse_detail.id_items,tbltransfer_warehouse.productions_capacity_id
        ) tb_transfer_detail";

        $tbPurchases = "(
            SELECT
                tblpurchases_items.product_id as product_id,
                tblpurchases.id_plan as plan_id,
                SUM(tblpurchases_items.quantity) as quantity
            FROM tblpurchases
            INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
            WHERE tblpurchases.id_plan IN(".$plan_id.") AND tblpurchases_items.type = 'nvl'
            GROUP BY tblpurchases_items.product_id,tblpurchases.id_plan
        ) tb_purchases";

        $this->db->select('
            tbl_productions_plan_bom.item_id as item_id,
            tbl_productions_plan_bom.productions_plan_id as plan_id,
            tbl_productions_plan.reference_no as reference_no,
            "materials" as item_type,
            tbl_productions_plan_bom.item_type as item_type_root,
            CONCAT("materials__", tbl_materials.id) as id,
            CONCAT(tbl_materials.name, "(", tbl_materials.code, ")") as text,
            tbl_materials.name as item_name,
            tbl_materials.code as item_code,
            SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0) as quantity_primary,
            tb_transfer_detail.quantity_net as quantity_net,
            tblunits.unit as unit_name,
            tb_purchases.quantity as quantity_purchase,
            tbl_productions_plan_bom.unit_parent_id as unit_parent_id,
            unit_parent.unit as unit_parent_name,
            tbl_productions_plan_bom.quantity_exchange as quantity_exchange,
            unit_stock.unit as unit_name_stock,
            tbl_materials.exchange_standard_unit as exchange_standard_unit,
            tbl_materials.exchange_unit as exchange_unit,
            1 as conversion_quantity_unit
        ', false);
        $this->db->from('tbl_productions_plan_bom');
        $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_plan_bom.productions_plan_id');
        $this->db->join('tbl_materials', 'tbl_materials.id = tbl_productions_plan_bom.item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        $this->db->join('tblunits unit_parent', 'unit_parent.unitid = tbl_productions_plan_bom.unit_parent_id', 'left');
        $this->db->join('tblunits unit_stock', 'unit_stock.unitid = tbl_materials.standard_unit', 'left');
        $this->db->join($tbTransferWarehouse, 'tb_transfer_detail.id_items = tbl_productions_plan_bom.item_id', 'left');
        $this->db->join($tbPurchases, 'tb_purchases.product_id = tbl_productions_plan_bom.item_id', 'left');
        $this->db->join($tbProductionsPlanCompensation, 'tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type', 'left');
        $this->db->where('tbl_productions_plan_bom.productions_plan_id IN ('.$plan_id.')');
        $this->db->where_in('tbl_productions_plan_bom.item_type', ['materials']);
        $this->db->group_by('tbl_productions_plan_bom.item_id,tbl_productions_plan_bom.productions_plan_id');
        $materials = $this->db->get()->result_array();
        $arrayItems = array_merge($semi_product_outside, $materials);
        if (!empty($arrayItems)){
            foreach ($arrayItems as $key => $value){
                $arrayItems[$key]['quantity_primary'] = round($arrayItems[$key]['quantity_primary'], 4);
            }
        }
        $arrayItems = array_filter($arrayItems, function($value, $key) use ($term) {
            if (!empty($term)) {
                if (!empty($term) && (strpos(strtolower($value['item_name']), $term) !== false || strpos(strtolower($value['item_code']),
                            $term) !== false)) {
                    return true;
                }
            } else {
                return true;
            }
            return false;
        }, ARRAY_FILTER_USE_BOTH);

        $results = $arrayItems;
        $data = [];
        $resultsNew = [];
        if (!empty($results)) {
            foreach ($results as $key => $value) {
                if (!empty($resultsNew[$value['reference_no']])) {
                    $resultsNew[$value['reference_no']]['items'][] = $value;
                } else {
                    $resultsNew[$value['reference_no']]['items'][] = $value;
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
    public function searchMaterialByPlanOld()
    {
        $term = $this->input->get('term');
        $plan_id = !empty($this->input->get('plan_id')) ? $this->input->get('plan_id') : 0;
        $limit = get_option('select2_limit');
        $wherePL = "";
        $whereTransfer = "";
        $wherePurchase = "";
        $whereImport = '';
        $wherePurchaseOrder = '';
        $wherePL .= ' AND tbl_productions_plan_bom.productions_plan_id IN (' . $plan_id . ')';
        $wherePurchase .= ' AND exists (
                SELECT
                    tbl_purchases_plans.purchases_id
                FROM tbl_purchases_plans
                WHERE tbl_purchases_plans.purchases_id = tblpurchases.id AND tbl_purchases_plans.productions_plan_id IN (' . $plan_id . ')
            )';
        $whereTransfer .= ' AND tbltransfer_warehouse.productions_capacity_id IN (' . $plan_id . ')';
        $wherePurchase .= ' AND exists (
                SELECT
                    tbl_purchases_plans.purchases_id
                FROM tbl_purchases_plans
                WHERE tbl_purchases_plans.purchases_id = tblpurchases.id AND tbl_purchases_plans.productions_plan_id IN (' . $plan_id . ')
            )';
        $tbWarehouseProduct = "(
            SELECT
                0 as id_items,
                0 as product_quantity,
                0 as product_quantity_unit
        ) tb_quantity_warehouse";

        $tbWarehouseMaterials = "(
            SELECT
                0 as id_items,
                0 as product_quantity,
                0 as product_quantity_unit
        ) tb_quantity_warehouse";

        $tbTransfer = "(
            SELECT
                0 as productions_capacity_id,
                0 as type, 
                0 as id_items,
                0 as quantity,
                0 as quantity_unit
        ) tb_transfer";

        $tbImport = "(
            SELECT
                0 as type, 
                0 as id_items,
                0 as quantity,
                0 as quantity_unit,
                0 as quantity_stock
        ) tb_import";

        $tbProductionsPlanCompensation = "(
            SELECT
                0 as item_id, 
                0 as item_type,
                0 as quantity_primary,
                0 as quantity_compensation
        ) tb_productions_plan_compensation";
        $tbProductionsPlanBom = "(
            (
                SELECT 
                    tbl_productions_plan_bom.item_id as item_id,
                    tbl_productions_plan.id as plan_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tbl_products.code as item_code,
                    tbl_products.name as item_name,
                    tbl_productions_plan_bom.item_type as item_type,
                    SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0) as quantity_primary,
                    SUM(tbl_productions_plan_bom.quantity) as quantity,
                    tblunits.unit as unit_name,
                    unit_primary.unit as unit_primary_name,
                    tb_quantity_warehouse.product_quantity as quantity_inventory,
                    tb_transfer.quantity as quantity_transfer,
                    1 as exchange_standard_unit,
                    1 as exchange_unit,
                    tbl_products.allowable as allowable,
                    tb_import.quantity_stock as quantity_stock
                FROM tbl_productions_plan_bom
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_bom.productions_plan_id
                INNER JOIN tbl_products ON tbl_products.id = tbl_productions_plan_bom.item_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                LEFT JOIN $tbWarehouseProduct ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_products.id AND tb_transfer.type = 'product'
                LEFT JOIN $tbImport ON tb_import.id_items = tbl_products.id AND tb_import.type = 'product'
                LEFT JOIN $tbProductionsPlanCompensation ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
                WHERE tbl_productions_plan_bom.item_type IN ('semi_products_outside') $wherePL
                GROUP BY tbl_productions_plan_bom.item_id,tbl_productions_plan_bom.productions_plan_id
            )
            UNION ALL
            (
                SELECT 
                    tbl_productions_plan_bom.item_id as item_id,
                    tbl_productions_plan.id as plan_id,
                    tbl_productions_plan.reference_no as reference_no,
                    tbl_materials.code as item_code,
                    tbl_materials.name as item_name,
                    tbl_productions_plan_bom.item_type as item_type,
                    ((SUM(tbl_productions_plan_bom.quantity_primary) + coalesce(tb_productions_plan_compensation.quantity_primary, 0)) * tbl_materials.exchange_unit / tbl_materials.exchange_standard_unit) as quantity_primary,
                    SUM(tbl_productions_plan_bom.quantity + tbl_productions_plan_bom.quantity_compensation + tbl_productions_plan_bom.quantity_compensation_sm) as quantity,
                    tblunits.unit as unit_name,
                    unit_stock.unit as unit_primary_name,
                    tb_quantity_warehouse.product_quantity as quantity_inventory,
                    tb_transfer.quantity as quantity_transfer,
                    tbl_materials.exchange_standard_unit as exchange_standard_unit,
                    tbl_materials.exchange_unit as exchange_unit,
                    tbl_materials.allowable as allowable,
                    tb_import.quantity_stock as quantity_stock
                FROM tbl_productions_plan_bom
                INNER JOIN tbl_productions_plan ON tbl_productions_plan.id = tbl_productions_plan_bom.productions_plan_id
                INNER JOIN tbl_materials ON tbl_materials.id = tbl_productions_plan_bom.item_id
                LEFT JOIN tblunits ON tblunits.unitid = tbl_productions_plan_bom.unit_id
                LEFT JOIN tblunits unit_primary ON unit_primary.unitid = tbl_productions_plan_bom.unit_parent_id
                LEFT JOIN tblunits unit_stock ON unit_stock.unitid = tbl_materials.standard_unit
                LEFT JOIN $tbWarehouseMaterials ON tb_quantity_warehouse.id_items = tbl_productions_plan_bom.item_id
                LEFT JOIN $tbTransfer ON tb_transfer.id_items = tbl_materials.id AND tb_transfer.type = 'nvl'
                LEFT JOIN $tbImport ON tb_import.id_items = tbl_materials.id AND tb_import.type = 'nvl'
                LEFT JOIN $tbProductionsPlanCompensation ON tb_productions_plan_compensation.item_id = tbl_productions_plan_bom.item_id AND tb_productions_plan_compensation.item_type = tbl_productions_plan_bom.item_type
                WHERE tbl_productions_plan_bom.item_type IN ('materials') $wherePL
                GROUP BY tbl_productions_plan_bom.item_id,tbl_productions_plan_bom.productions_plan_id
            )
        ) tb_cs";
        $tbPurchase = "(
            SELECT
                IF(tblpurchases_items.type = 'nvl', 'materials', 'products') as type_item, 
                tblpurchases_items.type as type,
                tblpurchases_items.product_id as product_id,
                SUM(tblpurchases_items.quantity_net) as quantity_net
            FROM tblpurchases
            INNER JOIN tblpurchases_items ON tblpurchases_items.purchases_id = tblpurchases.id
            WHERE tblpurchases.id_plan IN (".$plan_id.") AND tblpurchases.is_plans = 1 AND tblpurchases_items.type IN ('product', 'nvl') $wherePurchase
            GROUP BY tblpurchases_items.type, tblpurchases_items.product_id
        ) tb_purchase";
        $this->db->dbprefix = '';
        $this->db->select(['
             CONCAT(tb_cs.plan_id,"__",tb_cs.item_id) as id,
             CONCAT(tb_cs.item_name,"-",tb_cs.item_name) as text,
             tb_cs.item_id as item_id,
             tb_cs.item_code as item_code,
             tb_cs.item_name as item_name,
             tb_cs.reference_no as reference_no,
             tb_cs.unit_primary_name as unit_primary_name',
            'tb_cs.quantity_primary as quantity_primary',
            'tb_cs.quantity_inventory as quantity_inventory',
            'tb_cs.quantity_transfer as quantity_transfer',
            'coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) as quantity_purchase',
            'tb_cs.quantity_stock as quantity_stock',
            '(coalesce(tb_cs.quantity_primary, 0) - coalesce(tb_cs.quantity_inventory, 0) - (
                IF (coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_stock, 0) > 0, coalesce(tb_purchase.quantity_net * tb_cs.exchange_unit / tb_cs.exchange_standard_unit, 0) - coalesce(tb_cs.quantity_stock, 0), 0)
            ) - coalesce(tb_cs.quantity_transfer, 0)) as quantity_rest',
            '0 as ton_tong',
            '0 as ton_thuc_te',
            'tb_cs.allowable as ton_cho_phep',
            '0 as ton_da_mua',
            'tb_cs.item_type as _item_type',
            'tb_cs.item_id as _item_id',
            'tb_cs.exchange_unit as exchange_unit',
            'tb_cs.exchange_standard_unit as exchange_standard_unit',
            'tb_cs.unit_name as unit_name',
            'tb_cs.plan_id as plan_id',
        ], false);
        $this->db->from($tbProductionsPlanBom);
        $this->db->join($tbPurchase, 'tb_purchase.type_item = tb_cs.item_type AND tb_purchase.product_id = tb_cs.item_id', 'left');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('tb_cs.item_code', $term);
            $this->db->or_like('tb_cs.item_name', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $results = $this->db->get()->result_array();
        if (!empty($results)) {
            $arrItemsId = [];
            $arrProductsId = [];
            foreach ($results as $key => $value) {
                $_item_type = $value['_item_type'];
                $_item_id = $value['_item_id'];
                if ($_item_type == 'materials') {
                    $arrItemsId[] = $_item_id;
                } else {
                    $arrProductsId[] = $_item_id;
                }
            }

            $whereWarehouses = [];
            if (!empty($arrItemsId)) {
                $whereWarehouses[] = " (tblwarehouse_items.type_items = 'nvl' AND tblwarehouse_items.id_items IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereWarehouses[] = " (tblwarehouse_items.type_items = 'product' AND tblwarehouse_items.id_items IN (".implode(',', $arrProductsId).")) ";
            }

            $whereWarehouses = ' AND ('.implode(' OR ', $whereWarehouses).')';

            $tbWarehouses = "
                SELECT
                    tblwarehouse_items.id_items as id_items,
                    tblwarehouse_items.type_items as type_items,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity,
                    SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
                FROM tblwarehouse_items
                WHERE tblwarehouse_items.warehouse_id != " . WAREHOUSES_CAPACITY . " $whereWarehouses
                GROUP BY tblwarehouse_items.id_items, tblwarehouse_items.type_items
            ";
            $dtWarehouses = $this->db->query($tbWarehouses)->result_array();
            if (!empty($dtWarehouses)) {
                $dtWarehouses = array_reduce($dtWarehouses, function($carry, $item) {
                    $carry[$item['type_items'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            $tbWarehousesCapacity = "
                SELECT
                    tblwarehouse_items.id_items as id_items,
                    tblwarehouse_items.type_items as type_items,
                    SUM(tblwarehouse_items.product_quantity) as product_quantity,
                    SUM(tblwarehouse_items.product_quantity_unit) as product_quantity_unit
                FROM tblwarehouse_items
                WHERE tblwarehouse_items.warehouse_id = " . WAREHOUSES_CAPACITY . " $whereWarehouses
                GROUP BY tblwarehouse_items.id_items, tblwarehouse_items.type_items
            ";
            $dtWarehousesCapacity = $this->db->query($tbWarehousesCapacity)->result_array();
            if (!empty($dtWarehousesCapacity)) {
                $dtWarehousesCapacity = array_reduce($dtWarehousesCapacity, function($carry, $item) {
                    $carry[$item['type_items'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            //transfer

            $whereTr = [];
            if (!empty($arrItemsId)) {
                $whereTr[] = " (tbltransfer_warehouse_detail.type = 'nvl' AND tbltransfer_warehouse_detail.id_items IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereTr[] = " (tbltransfer_warehouse_detail.type = 'product' AND tbltransfer_warehouse_detail.id_items IN (".implode(',', $arrProductsId).")) ";
            }

            $whereTr = ' AND ('.implode(' OR ', $whereTr).')';

            $tbTransfer = "
                SELECT
                    tbltransfer_warehouse.productions_capacity_id,
                    tbltransfer_warehouse_detail.type as type, 
                    tbltransfer_warehouse_detail.id_items as id_items,
                    SUM(tbltransfer_warehouse_detail.quantity_net) as quantity,
                    SUM(tbltransfer_warehouse_detail.quantity_unit) as quantity_unit
                FROM tbltransfer_warehouse
                INNER JOIN tbltransfer_warehouse_detail ON tbltransfer_warehouse_detail.id_transfer = tbltransfer_warehouse.id
                WHERE tbltransfer_warehouse.productions_capacity_id IN (".$plan_id.") $whereTransfer $whereTr
                GROUP BY tbltransfer_warehouse_detail.type, tbltransfer_warehouse_detail.id_items
            ";
            $dtTransfer = $this->db->query($tbTransfer)->result_array();
            if (!empty($dtTransfer)) {
                $dtTransfer = array_reduce($dtTransfer, function($carry, $item) {
                    $carry[$item['type'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            //import
            $whereIm = [];
            if (!empty($arrItemsId)) {
                $whereIm[] = " (tblimport_items.type = 'nvl' AND tblimport_items.product_id IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereIm[] = " (tblimport_items.type = 'product' AND tblimport_items.product_id IN (".implode(',', $arrProductsId).")) ";
            }

            $whereIm = ' AND ('.implode(' OR ', $whereIm).')';

            $tbImport = "
                SELECT
                    tblimport_items.type as type, 
                    tblimport_items.product_id as id_items,
                    SUM(tblimport_items.quantity_net) as quantity,
                    SUM(tblimport_items.quantity_unit) as quantity_unit,
                    SUM(tblimport_items.quantity_stock) as quantity_stock
                FROM tblimport
                INNER JOIN tblimport_items ON tblimport_items.id_import = tblimport.id
                WHERE tblimport.plan_id IN (".$plan_id.") AND tblimport.type_plan > 0 AND tblimport.warehouseman_id > 0 $whereImport $whereIm
                GROUP BY tblimport_items.product_id, tblimport_items.type
            ";
            $dtImport = $this->db->query($tbImport)->result_array();
            if (!empty($dtImport)) {
                $dtImport = array_reduce($dtImport, function($carry, $item) {
                    $carry[$item['type'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }

            //plan compensation
            $whereCom = [];
            if (!empty($arrItemsId)) {
                $whereCom[] = " (tbl_productions_plan_compensation.item_type = 'materials' AND tbl_productions_plan_compensation.item_id IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $whereCom[] = " (tbl_productions_plan_compensation.item_type = 'semi_products' AND tbl_productions_plan_compensation.item_id IN (".implode(',', $arrProductsId).")) ";
            }

            $whereCom = ' AND ('.implode(' OR ', $whereCom).')';
            if (empty($wherePQ)) $wherePQ = "WHERE 1 ";

            $tbProductionsPlanCompensation = "
                SELECT
                    tbl_productions_plan_compensation.item_id, 
                    tbl_productions_plan_compensation.item_type,
                    SUM(tbl_productions_plan_compensation.quantity_primary) as quantity_primary,
                    SUM(tbl_productions_plan_compensation.quantity_compensation) as quantity_compensation
                FROM tbl_productions_plan_compensation
                $wherePQ
                GROUP BY tbl_productions_plan_compensation.item_id, tbl_productions_plan_compensation.item_type
            ";
            $dtProductionsPlanCompensation = $this->db->query($tbProductionsPlanCompensation)->result_array();
            if (!empty($dtProductionsPlanCompensation)) {
                $dtProductionsPlanCompensation = array_reduce($dtProductionsPlanCompensation, function($carry, $item) {
                    $carry[$item['item_type'].'__'.$item['item_id']] = $item;
                    return $carry;
                });
            }

            //purchase order

            $wherePo = [];
            if (!empty($arrItemsId)) {
                $wherePo[] = " (tblpurchase_order_items.type = 'nvl' AND tblpurchase_order_items.product_id IN (".implode(',', $arrItemsId).")) ";
            }

            if (!empty($arrProductsId)) {
                $wherePo[] = " (tblpurchase_order_items.type = 'product' AND tblpurchase_order_items.product_id IN (".implode(',', $arrProductsId).")) ";
            }

            $wherePo = ' AND ('.implode(' OR ', $wherePo).')';

            // tblpurchase_order.cancel = 0
            $tbPurchaseOrder = "
                SELECT
                    tblpurchase_order_items.type as type, 
                    tblpurchase_order_items.product_id as id_items,
                    SUM(tblpurchase_order_items.quantity - coalesce((
                        SELECT
                            SUM(tblimport_items.quantity_net)
                        FROM tblimport_items
                        WHERE tblimport_items.id_purchase_order_items = tblpurchase_order_items.id
                    ), 0)) as quantity
                FROM tblpurchase_order
                INNER JOIN tblpurchase_order_items ON tblpurchase_order_items.id_purchase_order = tblpurchase_order.id
                WHERE tblpurchase_order.is_end = 0 $wherePurchaseOrder $wherePo
                GROUP BY tblpurchase_order_items.product_id, tblpurchase_order_items.type
            ";
            $dtPurchaseOrder = $this->db->query($tbPurchaseOrder)->result_array();
            if (!empty($dtPurchaseOrder)) {
                $dtPurchaseOrder = array_reduce($dtPurchaseOrder, function($carry, $item) {
                    $carry[$item['type'].'__'.$item['id_items']] = $item;
                    return $carry;
                });
            }
        }
        if (!empty($results)){
            foreach ($results as $key => $aRow){
                $item_id = $aRow['item_id'];

                $_item_type = $aRow['_item_type'];
                $_item_id = $aRow['_item_id'];
                $item_id_hau = 'product__'.$_item_id;
                if ($_item_type == 'materials') {
                    $item_id_hau = 'nvl__'.$_item_id;
                }

                $exchange_unit = $aRow['exchange_unit'];
                $exchange_standard_unit = $aRow['exchange_standard_unit'];
                $_dtWarehouses = $dtWarehouses[$item_id_hau] ?? null;
                $_dtTransfer = $dtTransfer[$item_id_hau] ?? null;
                $_dtImport = $dtImport[$item_id_hau] ?? null;
                $_dtProductionsPlanCompensation = $dtProductionsPlanCompensation[$item_id] ?? null;
                $quantity_inventory = $_dtWarehouses['product_quantity'] ?? 0;
                $quantity_transfer = $_dtTransfer['quantity'] ?? 0;
                $quantity_stock = $_dtImport['quantity_stock'] ?? 0;
                $quantity_plan_com = $_dtProductionsPlanCompensation['quantity_primary'] ?? 0;
                $quantity_plan_com = $quantity_plan_com * $exchange_unit/$exchange_standard_unit;

                $_dtWarehousesCapacity = $dtWarehousesCapacity[$item_id_hau] ?? null;
                $quantity_inventory_capacity = $_dtWarehousesCapacity['product_quantity'] ?? 0;

                $_dtPurchaseOrder = $dtPurchaseOrder[$item_id_hau] ?? null;
                $quantity_purchase_order = $_dtPurchaseOrder['quantity'] ?? 0;

                $aRow['ton_tong'] = $quantity_inventory + $quantity_inventory_capacity;
                $aRow['ton_thuc_te'] = $quantity_inventory;

                $aRow['quantity_inventory'] = $quantity_inventory;
                $aRow['quantity_transfer'] = $quantity_transfer;
                $aRow['quantity_stock'] = $quantity_stock;
                $aRow['quantity_primary'] = $aRow['quantity_primary'] + $quantity_plan_com;

                $quantity_rest = $aRow['quantity_primary'] - $aRow['quantity_inventory'];
                if (($aRow['quantity_purchase'] - $quantity_stock) > 0) {
                    $quantity_rest-= $aRow['quantity_purchase'] - $quantity_stock;
                }
                $quantity_rest-= $aRow['quantity_transfer'];
                $aRow['quantity_rest']= $quantity_rest;

                $aRow['quantity_primary'] = ceil($aRow['quantity_primary']);
                $quantity_rest = ceil($aRow['quantity_rest']);
                $aRow['quantity_rest'] = $quantity_rest > 0 ? $quantity_rest : 0;
                $results[$key] = $aRow;
            }
        }
        $data = [];
        $resultsNew = [];
        if (!empty($results)) {
            foreach ($results as $key => $value) {
                if (!empty($resultsNew[$value['reference_no']])) {
                    $resultsNew[$value['reference_no']]['items'][] = $value;
                } else {
                    $resultsNew[$value['reference_no']]['items'][] = $value;
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
}
