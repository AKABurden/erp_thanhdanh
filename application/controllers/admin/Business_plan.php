<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Business_plan extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('unit_model');
        $this->load->model('category_model');
        $this->load->model('manufactures_model');
        $this->load->model('business_plan_model');
        $this->load->model('departments_model');
        $this->load->model('stock_model');
        $this->load->model('tools_supplies_model');
        $this->load->model('manufactures_model');
        $this->load->model('orders_model');

        // $this->lang->load('vietnamese/form_validation_lang');
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = get_upload_path_by_type('products');
        $this->datetime_now = time();
        $this->tnh = true;

        //permission business plan
        $this->perViewBusinessPlan = has_permission('business_plan', '', 'view');
        $this->perViewOwnBusinessPlan = has_permission('business_plan', '', 'view_own');
        $this->perAddBusinessPlan = has_permission('business_plan', '', 'create');
        $this->perEditBusinessPlan = has_permission('business_plan', '', 'edit');
        $this->perDeleteBusinessPlan = has_permission('business_plan', '', 'delete');
        $this->perApproveBusinessPlan = has_permission('business_plan', '', 'approve');
    }

    public function index()
    {
        if (!$this->perViewBusinessPlan && !$this->perViewOwnBusinessPlan) {
            accessDenied();
        }

    	$data['tnh'] = true;
        $data['title'] = lang('tnh_business_plan');
        $this->load->view('admin/business_plan/manage', $data);
    }

    public function add()
    {
        if (!$this->perAddBusinessPlan) {
            accessDenied();
        }

        $order_id = $this->input->get('order_id');
        $order = [];
        if (!empty($order_id)) {
            $checkOrdersBusiness = $this->orders_model->checkOrdersBusiness($order_id);
            if ($checkOrdersBusiness) {
                $this->session->set_flashdata('warning', 'Đơn này đã tạo sản xuất mẫu');
                redirect($_SERVER["HTTP_REFERER"]); die;
            }

            $order = $this->orders_model->rowOrderById($order_id);
        }

        if ($this->input->post('add'))
        {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_business_plan"), 'trim|required|is_unique[tbl_business_plan.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('plan_name', lang("tnh_plan_name"), 'required');
            $this->form_validation->set_rules('departments', lang("departments"), 'required');
            $this->form_validation->set_rules('id_branch', lang("tnh_branch"), 'required');
            if ($this->form_validation->run() == true)
            {
                // $reference_no = $this->input->post('reference_no');
                $reference_no = getReference('business_plan');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note');
                $plan_name = $this->input->post('plan_name');
                $departments = $this->input->post('departments');
                $total_quantity = 0;
                $count_items = 0;

                $order_id = $this->input->post('order_id');
                $id_branch = $this->input->post('id_branch');

                // $counter = $this->input->post('counter');
                $items_id = $this->input->post('items_id');
                foreach ($items_id as $key => $value) {
                    $items_id = $value;
                    $counter = $this->input->post('counter')[$key];
                    $type_items = 'products';
                    $unit_id = 0;
                    $conversion_quantity_unit = 1;
                    if ($type_items == "products") {
                        $info = $this->products_model->rowProduct($items_id);

                        $unit_id = $info['conversion_unit'];
                        $conversion_quantity_unit = $info['conversion_quantity_unit'];
                    }

                    if (empty($info)) {
                        continue;
                    }

                    if (empty($unit_id)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mặt hàng chưa có đơn vị tính');
                        echo json_encode($data); die;
                    }

                    $items_code = $info['code'];
                    $items_name = $info['name'];
                    $quantity = number_unformat($this->input->post('quantity')[$key]);
                    $note_items = $this->input->post('note_items')[$key];
                    $sub = [];

                    $order_item_id = !empty($this->input->post('order_item_id')[$key]) ? $this->input->post('order_item_id')[$key] : 0;

                    $date_sub = $this->input->post('date_sub')[$counter];
                    $total_quantity_sub = 0;
                    if (!empty($date_sub)) {

                        foreach ($date_sub as $k => $val) {
                            if (empty($val)) continue;
                            if ($k != 0) continue;
                            // $quantity_sub = number_unformat($this->input->post('quantity_sub')[$counter][$k]);
                            $quantity_sub = $quantity;
                            $sub[] = [
                                'date' => to_sql_date($val),
                                'quantity' => $quantity_sub
                            ];
                            $total_quantity_sub+= $quantity_sub;
                        }

                        if ($total_quantity_sub > $quantity) {
                            $data['result'] = 0;
                            $data['message'] = lang('tnh_check_date_enter');
                            echo json_encode($data);
                            die;
                        }
                    }

                    if (empty($sub)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng chọn ngày giao hàng dự kiến');
                        echo json_encode($data);
                        die;
                    }

                    $items[] = [
                        'type_items' => $type_items,
                        'items_id' => $items_id,
                        'items_code' => $items_code,
                        'items_name' => $items_name,
                        'quantity' => $quantity,
                        'note_items' => $note_items,
                        'order_item_id' => $order_item_id,
                        'sub' => $sub,
                        'unit_id' => $unit_id,
                        'conversion_quantity_unit' => $conversion_quantity_unit,
                    ];

                    $total_quantity+= $quantity;
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                $count_items = count($items);

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'plan_name' => $plan_name,
                    'departments_id' => $departments,
                    'total_quantity' => $total_quantity,
                    'count_items' => $count_items,
                    'note' => $note,
                    'status' => 'un_approved',
                    'productions_plan_id' => 0,
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                    'order_id' => $order_id,
                    'id_branch' => $id_branch,
                ];
                $business_plan_id = $this->business_plan_model->insertBusinessPlan($options);
                if ($business_plan_id) {
                    // if (getReference('business_plan') == $this->input->post('reference_no')) {
                        updateReference('business_plan');
                    // }
                    $arrItemId = [];
                    foreach ($items as $key => $value) {
                        $op = [
                            'business_plan_id' => $business_plan_id,
                            'type_items' => $value['type_items'],
                            'items_id' => $value['items_id'],
                            'items_code' => $value['items_code'],
                            'items_name' => $value['items_name'],
                            'quantity' => $value['quantity'],
                            'note_items' => $value['note_items'],
                            'order_item_id' => $value['order_item_id'],
                            'unit_id' => $value['unit_id'],
                            'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                        ];
                        $business_plan_item_id = $this->business_plan_model->insertBusinessPlanItems($op);
                        if ($business_plan_item_id) {
                            $sb = $value['sub'];
                            foreach ($sb as $k => $val) {
                                $sb[$k]['business_plan_items_id'] = $business_plan_item_id;
                            }
                            if (!empty($sb)) {
                                $this->business_plan_model->insertBatchBusinessPlanItemsDate($sb);
                            }
                        }

                        $arrItemId[] = $value['items_id'];
                    }

                    $this->business_plan_model->handlingStagesBusinessPlan($business_plan_id);

                    if (!empty($arrItemId)) {
                        totalBusinessPlan($arrItemId);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'business_plan',
                        'table_obj' => 'tbl_business_plan',
                        'id_obj' => $business_plan_id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_add_business_plan').' ['.$reference_no.']',
                        'actions' => 'add'
                    ]);

                    set_alert('success', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                    @pusherTNHNotfication();
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $data['branch'] = $this->site_model->getBranch();
        $data['order'] = $order;
        $data['departments'] = $this->departments_model->getDepartments();
        $data['reference_no'] = getReference('business_plan');
        $data['tnh'] = true;
        $data['title'] = lang('tnh_add_business_plan');
        $data['breadcrumb'] = [array('link' => base_url('admin/business_plan'), 'page' => lang('tnh_business_plan')), array('link' => '#', 'page' => lang('tnh_add_business_plan'))];
        $this->load->view('admin/business_plan/add', $data);
    }

    public function edit($id)
    {
        if (!$this->perEditBusinessPlan) {
            accessDenied();
        }
        $business_plan = $this->business_plan_model->rowBusinessPlanById($id);
        $business_plan_items = $this->business_plan_model->getBusinessPlanItemsByBusinessPlanId($id);
        if (empty($business_plan)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }
        if ($business_plan['status'] == "approved") {
            set_alert('danger', lang('browsed_cannot_be_edited'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if ($this->input->post('edit'))
        {
            $data = [];
            if ($business_plan['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_business_plan"), 'trim|required|is_unique[tbl_business_plan.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('plan_name', lang("tnh_plan_name"), 'required');
            $this->form_validation->set_rules('departments', lang("departments"), 'required');
            $this->form_validation->set_rules('id_branch', lang("tnh_branch"), 'required');
            if ($this->form_validation->run() == true)
            {
                $reference_no = $this->input->post('reference_no');
                $date = to_sql_date($this->input->post('date'), true);
                $note = $this->input->post('note');
                $plan_name = $this->input->post('plan_name');
                $departments = $this->input->post('departments');
                $id_branch = $this->input->post('id_branch');
                $total_quantity = 0;
                $count_items = 0;
                $i = 0;

                //update
                $business_plan_items_id = $this->input->post('business_plan_items_id');
                $arr_id_exists = [];
                if (!empty($business_plan_items_id)) {
                    foreach ($business_plan_items_id as $key => $value) {
                        $counter = $this->input->post('counter')[$i];
                        array_push($arr_id_exists, $value);

                        $quantity = number_unformat($this->input->post('quantity_edit')[$key]);
                        $note_items = $this->input->post('note_items_edit')[$key];
                        $sub = [];
                        $date_sub = $this->input->post('date_sub')[$counter];
                        $total_quantity_sub = 0;
                        if (!empty($date_sub)) {

                            foreach ($date_sub as $k => $val) {
                                if (empty($val)) continue;
                                if ($k != 0) continue;
                                // $quantity_sub = number_unformat($this->input->post('quantity_sub')[$counter][$k]);
                                $quantity_sub = $quantity;
                                $sub[] = [
                                    'business_plan_items_id' => $value,
                                    'date' => to_sql_date($val),
                                    'quantity' => $quantity_sub
                                ];
                                $total_quantity_sub+= $quantity_sub;
                            }

                            if ($total_quantity_sub > $quantity) {
                                $data['result'] = 0;
                                $data['message'] = lang('tnh_check_date_enter');
                                echo json_encode($data);
                                die;
                            }
                        }

                        if (empty($sub)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn ngày giao hàng dự kiến');
                            echo json_encode($data);
                            die;
                        }

                        $items_up[] = [
                            'id' => $value,
                            'quantity' => $quantity,
                            'note_items' => $note_items,
                            'sub' => $sub
                        ];

                        $total_quantity+= $quantity;
                        $i++;
                    }
                }

                //add
                $items_id = $this->input->post('items_id');
                if (!empty($items_id)) {
                    foreach ($items_id as $key => $value) {
                        $items_id = $value;
                        $counter = $this->input->post('counter')[$i];
                        $type_items = 'products';
                        $unit_id = 0;
                        $conversion_quantity_unit = 1;

                        if ($type_items == "products") {
                            $info = $this->products_model->rowProduct($items_id);

                            $unit_id = $info['conversion_unit'];
                            $conversion_quantity_unit = $info['conversion_quantity_unit'];
                        }
                        if (empty($info)) {
                            continue;
                        }

                        if (empty($unit_id)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Mặt hàng chưa có đơn vị tính');
                            echo json_encode($data); die;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $note_items = $this->input->post('note_items')[$key];
                        $sub = [];

                        $date_sub = $this->input->post('date_sub')[$counter];
                        $total_quantity_sub = 0;
                        if (!empty($date_sub)) {

                            foreach ($date_sub as $k => $val) {
                                if (empty($val)) continue;
                                if ($k != 0) continue;
                                // $quantity_sub = number_unformat($this->input->post('quantity_sub')[$counter][$k]);
                                $quantity_sub = $quantity;
                                $sub[] = [
                                    'date' => to_sql_date($val),
                                    'quantity' => $quantity_sub
                                ];
                                $total_quantity_sub+= $quantity_sub;
                            }

                            if ($total_quantity_sub > $quantity) {
                                $data['result'] = 0;
                                $data['message'] = lang('tnh_check_date_enter');
                                echo json_encode($data);
                                die;
                            }
                        }

                        if (empty($sub)) {
                            $data['result'] = 0;
                            $data['message'] = lang('Vui lòng chọn ngày giao hàng dự kiến');
                            echo json_encode($data);
                            die;
                        }

                        $items[] = [
                            'business_plan_id' => $id,
                            'type_items' => $type_items,
                            'items_id' => $items_id,
                            'items_code' => $items_code,
                            'items_name' => $items_name,
                            'quantity' => $quantity,
                            'note_items' => $note_items,
                            'sub' => $sub,
                            'unit_id' => $unit_id,
                            'conversion_quantity_unit' => $conversion_quantity_unit,
                        ];

                        $total_quantity+= $quantity;
                        $i++;
                    }
                }

                if (empty($items) && empty($items_up)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }
                // print_arrays($items_up, $items);

                $count_items = (!empty($items) ? count($items) : 0) + (!empty($items_up) ? count($items_up) : 0);

                $options = [
                    'date' => $date,
                    'reference_no' => $reference_no,
                    'total_quantity' => $total_quantity,
                    'plan_name' => $plan_name,
                    'departments_id' => $departments,
                    'count_items' => $count_items,
                    'note' => $note,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                    'id_branch' => $id_branch
                ];
                // print_arrays($items);
                $up = $this->business_plan_model->updateBusinessPlan($id, $options);
                if ($up) {
                    $delete = $this->business_plan_model->getBusinessPlanItemsByNotArrId($arr_id_exists, $id);
                    //update
                    if (!empty($items_up)) {
                        foreach ($items_up as $key => $value) {
                            $sb = $value['sub'];
                            unset($value['sub']);
                            $up_item = $this->business_plan_model->updateBusinessPlanItems($value['id'], $value);
                            if ($up_item) {
                                $this->business_plan_model->deleteBusinessPlanItemsDateBusinessPlanItemsId($value['id']);
                                if (!empty($sb)) {
                                    $this->business_plan_model->insertBatchBusinessPlanItemsDate($sb);
                                }
                            }
                        }
                    }

                    //add
                    if (!empty($items)) {
                        foreach ($items as $key => $value) {
                            $op = [
                                'business_plan_id' => $value['business_plan_id'],
                                'type_items' => $value['type_items'],
                                'items_id' => $value['items_id'],
                                'items_code' => $value['items_code'],
                                'items_name' => $value['items_name'],
                                'quantity' => $value['quantity'],
                                'note_items' => $value['note_items'],
                                'unit_id' => $value['unit_id'],
                                'conversion_quantity_unit' => $value['conversion_quantity_unit'],
                            ];
                            $business_plan_item_id = $this->business_plan_model->insertBusinessPlanItems($op);
                            if ($business_plan_item_id) {
                                $sb = $value['sub'];
                                foreach ($sb as $k => $val) {
                                    $sb[$k]['business_plan_items_id'] = $business_plan_item_id;
                                }
                                if (!empty($sb)) {
                                    $this->business_plan_model->insertBatchBusinessPlanItemsDate($sb);
                                }
                            }
                        }
                    }

                    //delete
                    if (!empty($delete)) {
                        foreach ($delete as $key => $value) {
                            if ($this->business_plan_model->deleteBusinessPlanItems($value['id'])) {
                                $this->business_plan_model->deleteBusinessPlanItemsDateBusinessPlanItemsId($value['id']);
                            }
                        }
                    }

                    $this->business_plan_model->handlingStagesBusinessPlan($id);

                    //items
                    if (!empty($id)) {
                        $this->db->select('
                            tbl_business_plan_items.items_id as items_id,
                        ', false);
                        $this->db->from('tbl_business_plan_items');
                        $this->db->where('tbl_business_plan_items.business_plan_id', $id);
                        $business_plan_items = $this->db->get()->result_array();
                        if (!empty($business_plan_items)) {
                            $arrItemsId = array_column($business_plan_items, 'items_id');
                            totalBusinessPlan($arrItemsId);
                        }
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'business_plan',
                        'table_obj' => 'tbl_business_plan',
                        'id_obj' => $id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_edit_business_plan').' ['.$reference_no.']',
                        'actions' => 'edit'
                    ]);

                    set_alert('success', lang('success'));
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }

            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $data['business_plan'] = $business_plan;
        // $data['business_plan_items'] = $business_plan_items;
        $counter = 0;
        $tr_html = '';
        if (!empty($business_plan_items)) {
            foreach ($business_plan_items as $key => $value) {
                $images = $value['images'];
                if (!empty($images)) {
                    $images = base_url().'uploads/products/'.$images;
                } else {
                    $images = base_url().'assets/images/tnh/no_image.png';
                }

                $product = $this->products_model->rowProduct($value['items_id']);
                $unit = $this->unit_model->rowUnit($product['conversion_unit']);

                $sub_date = $this->business_plan_model->getBusinessPlanItemsDateByBusinessPlanItemsId($value['id']);
                $html_sub_date = '';
                // '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>'.
                // '<div class="col-md-4" style="padding: 0px;"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" name="quantity_sub['.$counter.'][]" id="input" class="form-control quantity_sub" value="'.number_format($val['quantity']).'" title=""></div>'.
                if (!empty($sub_date)) {
                    foreach ($sub_date as $k => $val) {
                        $html_sub_date.= '<div class="sb">'.
                            '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub['.$counter.'][]" id="input" class="form-control datepicker date_sub" placeholder="'.lang('date').'" value="'._d($val['date']).'" style="width: 100%;" title=""></div>'.
                        '</div>';
                    }
                } else {
                    $html_sub_date.= '<div class="sb">'.
                        '<div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub['.$counter.'][]" id="input" class="form-control datepicker date_sub" placeholder="'.lang('date').'" value="" style="width: 100%;" title=""></div>'.
                    '</div>';
                }

                $td1 = '<div class="stt text-center">'.(++$key).'</div>';
                $td2 = '
                    <input type="hidden" name="business_plan_items_id[]" id="input" class="form-control" value="'.$value['id'].'">
                    <input type="hidden" name="order_item_id[]" class="form-control order_item_id" value="'.$value['order_item_id'].'">
                    <input type="hidden" name="counter[]" id="input" class="form-control" value="'.$counter.'">'.$value['items_code'];
                $td3 = '<div class="td-image">'.
                            '<div class="preview_image" style="width: auto;">'.
                                '<div class="display-block contract-attachment-wrapper img">'.
                                    '<div style="width:45px;">'.
                                        '<a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">'.
                                            '<div class="">'.
                                                '<img src="'.$images.'" style="border-radius: 50%">'.
                                            '</div>'.
                                        '</a>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                    '</div>';
                $td4 = '<div class="td-item-name">'.$value['items_name'].'</div>';
                $tdUnit = '<div class="td-unit">'.$unit['unit'].'</div>';
                $td5 = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity_edit[]" id="quantity[]" class="form-control quantity" value="'.number_format($value['quantity']).'"></div>';
                $td6 = '<div class="td-date">'.
                        '<div class="sub">'.$html_sub_date.'</div>'.
                        '<div class="text-danger show-errors"></div>'.
                    '</div>';
                $td7 = '<div class="td-note"><textarea name="note_items_edit[]" id="note_items[]" class="form-control" rows="3">'.$value['note_items'].'</textarea></div>';
                $td8 = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

                $tr_html.= '<tr>
                                <td>'.$td1.'</td>
                                <td>'.$td2.'</td>
                                <td>'.$td3.'</td>
                                <td>'.$td4.'</td>
                                <td>'.$tdUnit.'</td>
                                <td>'.$td5.'</td>
                                <td>'.$td6.'</td>
                                <td>'.$td7.'</td>
                                <td>'.$td8.'</td>
                            </tr>';
                $counter++;
            }
        }

        $data['branch'] = $this->site_model->getBranch();
        $data['departments'] = $this->departments_model->getDepartments();
        $data['tnh'] = true;
        $data['tr_html'] = $tr_html;
        $data['counter'] = $counter;
        $data['title'] = lang('tnh_edit_business_plan');
        $data['breadcrumb'] = [array('link' => base_url('admin/business_plan'), 'page' => lang('tnh_business_plan')), array('link' => '#', 'page' => lang('tnh_edit_business_plan'))];
        $this->load->view('admin/business_plan/edit', $data);
    }

    function refereshReference()
    {
        $data = [];
        if ($this->input->get('referesh'))
        {
            $reference_no = getReference('business_plan');
            if ($this->manufactures_model->checkExistProductionsPlanByReferenceNo($reference_no)) {
                $this->db->select('MAX(tbl_business_plan.reference_no) as reference_no', false);
                $this->db->from('tbl_business_plan');
                $rs = $this->db->get()->row_array();

                $max = $rs['reference_no'];
                $max = subReference($max);
                updateReferenceNormal('business_plan', $max);
                $reference_no = getReference('business_plan');
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    function getBusinessPlan() {
        if (!$this->perViewBusinessPlan && !$this->perViewOwnBusinessPlan) {
            accessDenied($js = true);
        }

        $branch_staff = get_array_branch_staff();
        $is_admin = is_admin();

        $products_search = $this->input->post('products_search');
        $productions_orders_search = $this->input->post('productions_orders_search');

        $this->datatables->select("
            tbl_business_plan.id as id,
            tbl_business_plan.date as date,
            CONCAT(tbl_business_plan.reference_no, '__', tbl_business_plan.productions_plan_preventive_id) as reference_no,
            tbl_business_plan.plan_name as plan_name,
            tbldepartments.name as departments_name,
            tbl_business_plan.note as note,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname,'') as created_by,
            tbl_business_plan.status as status,
            CONCAT(staff_status.firstname, ' ', staff_status.lastname,'') as user_status,
            tbl_business_plan.productions_plan_id as status_productions_plan,
            '' as items,
            '' as status_manufactures,
            tblbranch.name as name_branch,
            ", FALSE)
        ->from('tbl_business_plan')
        ->join('tbldepartments', 'tbldepartments.departmentid = tbl_business_plan.departments_id', 'left')
        ->join('tblstaff', 'tblstaff.staffid = tbl_business_plan.created_by', 'left')
        ->join('tblstaff staff_status', 'staff_status.staffid = tbl_business_plan.user_status', 'left')
        ->join('tblbranch', 'tblbranch.id = tbl_business_plan.id_branch', 'left');

        if (!$this->perViewBusinessPlan) {
            $this->datatables->where('tbl_business_plan.created_by', get_staff_user_id());
        }

        if (!empty($products_search)) {
            $arrProduct = explode('__', $products_search);
            $this->datatables->where('(
                SELECT 
                    tbl_business_plan_items.id
                FROM tbl_business_plan_items
                WHERE tbl_business_plan_items.type_items = "products" AND tbl_business_plan_items.business_plan_id = tbl_business_plan.id AND tbl_business_plan_items.items_id = '.$arrProduct[0].'
            )', false, false);
        }

        if (!empty($productions_orders_search)) {
            $this->datatables->where(' exists (
                SELECT 
                    tbl_productions_plan_orders.id
                FROM tbl_productions_plan_orders
                WHERE tbl_productions_plan_orders.object_type = "business_plan" AND tbl_productions_plan_orders.productions_order_id = '.$productions_orders_search.' AND tbl_productions_plan_orders.productions_plan_id = tbl_business_plan.id
            )', false, false);
        }

        if (!$is_admin) {
            if (empty($branch_staff)) $branch_staff = [0];
            $this->datatables->where('tbl_business_plan.id_branch IN ('.implode(',', $branch_staff).')', false, false);
        }

        // $iDisplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());
        // var_dump($data);
        // $itemsProcess = $this->site_model->getProductionsOrdersItemsStages('business_plan', $vItem['id']);
        foreach ($data->aaData as $key => $value) {
            // $data->aaData[$key][0] = ++$iDisplayStart;
            $cHtml = '';
            $business_plan_id = $value[0];


            $isNotProducedYet = 0;
            $isProduction = 0;
            $isFinished = 0;
            $isCancel = 0;
            $status_orders = 0;
            $this->db->select('
                tbl_business_plan_items.*,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_products.images as images,
            ', false);
            $this->db->from('tbl_business_plan_items');
            $this->db->where('tbl_business_plan_items.business_plan_id', $business_plan_id);
            $this->db->join('tbl_products', 'tbl_products.id = tbl_business_plan_items.items_id');
            $business_plan_items = $this->db->get()->result_array();
            if (!empty($business_plan_items)) {
                foreach ($business_plan_items as $kItem => $vItem) {
                    $images = base_url('assets/images/tnh/no_image.png');
                    if ($vItem['images']) {
                        $images = base_url('uploads/products/'.$vItem['images']);
                    }

                    $workflow = '';
                    $itemsProcess = $this->site_model->getProductionsOrdersItemsStages('business_plan', $vItem['id']);
                    if (!empty($itemsProcess)) {
                        foreach ($itemsProcess as $kk => $vv) {
                            $li = '';

                            $status_orders = $vv['status_orders'];
                            $process = $vv['process'];
                            if (!empty($process)) {
                                foreach ($process as $kkk => $vvv) {

                                    $li .= '<li ' . ($vvv['active'] ? 'class="active"' : '') . '>' . $vvv['stage_name'] .
                                        (!empty($vvv['staff_active']) ? ('<p class="active_poin">' . ('Được ' . get_staff_full_name($vvv['staff_active']) . ($vvv['date_active'] ? ' duyệt vào lúc: ' . _dt($vvv['date_active']) : '')) . '</p>') : '')
                                        . '</li>';
                                    
                                    if ($kkk == 1 && empty($vvv['active'])) {
                                        $isNotProducedYet = 1;
                                    }
        
                                    if (!empty($vvv['active'])) {
                                        $isProduction = 1;
                                    }
        
                                    if ($vvv['final_stage'] && empty($vvv['active'])) {
                                        $isFinished = 1;
                                    }
                                }
                            } else {
                                $isFinished = 1;
                            }

                            $workflow .= '<div style="display: table; justify-content: center;">
                                <div class="pull-left mtop20"><span class="label label-primary">'.$vv['reference_no'].' (SL: '.formatNumber($vv['quantity']).')</span></div>
                                <ul class="progressbar" style="display: flex;">
                                ' . $li . '
                                </ul>
                            </div>';
                        }
                    } else {
                        $isFinished = 1;
                        $workflow = '<div class="text-danger italic">'.lang('tnh_no_productions_orders_yet').'</div>';
                    }

                    $cHtml.= '<div class="row mbot5" style="margin-right: 0px; margin-left: 0px;">
                        <div class="col-md-4" style="padding-right: 0;">
                            <div class="flex-center">
                                <div class="td-image mright5" style="width: 50px;">
                                    <div class="preview_image" style="width: auto;">
                                        <div class="display-block contract-attachment-wrapper img">
                                            <div style="width:45px;">
                                                <a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">
                                                    <div class=""><img src="'.$images.'" style="border-radius: 50%"></div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="style="width: 80%;">
                                    <div class="text-bold">'.$vItem['item_name'].'('.$vItem['item_code'].')</div>
                                    <div class="">'.lang('quantity').': '.$vItem['quantity'].'</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8" style="padding-right: 0;">
                            '.$workflow.'
                        </div>
                    </div>';
                }
            }
            $data->aaData[$key][10] = '<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">'.$cHtml.'</div>';

            $strStatus = '';
            if (!empty($status_orders)) {
                $strStatus = '<span class="label label-danger">'.lang('Kết thúc sản xuất').'</span>';
            } else if (!$isFinished) {
                $strStatus = '<span class="label label-success">'.lang('Hoàn thành').'</span>';
            } else if (!empty($isProduction)) {
                $strStatus = '<span class="label label-primary">'.lang('Đang sản xuất').'</span>';
            } else {
                $strStatus = '<span class="label label-warning">'.lang('Chưa sản xuất').'</span>';
            }
            $data->aaData[$key][11] = '<div class="mbot10">'.$strStatus.'</div>';
        }
        echo json_encode($data);
    }

    public function view_business_plan($id) {
        if (!$this->perViewBusinessPlan && !$this->perViewOwnBusinessPlan) {
            accessDenied($js = true);
        }
        $business_plan = $this->business_plan_model->rowBusinessPlanById($id);
        if (!$this->perViewBusinessPlan) {
            checkMyData($business_plan['created_by'], true);
        }
        $items = $this->business_plan_model->getBusinessPlanItemsByBusinessPlanId($id);
        $tr_html = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $images = $value['images'];
                if (!empty($images)) {
                    $images = base_url().'uploads/products/'.$images;
                } else {
                    $images = base_url().'assets/images/tnh/no_image.png';
                }

                $unit = $this->unit_model->rowUnit($value['unit_id']);

                $sub_date = $this->business_plan_model->getBusinessPlanItemsDateByBusinessPlanItemsId($value['id']);
                $html_sub_date = '';
                if (!empty($sub_date)) {
                    foreach ($sub_date as $k => $val) {
                        $html_sub_date.= '<div class="">'._d($val['date']).' - '.number_format($val['quantity']).'</div>';
                    }
                }

                $td1 = '<div class="">'.(++$key).'</div>';
                $td2 = '<div class="td-image">'.
                            '<div class="preview_image" style="width: auto;">'.
                                '<div class="display-block contract-attachment-wrapper img">'.
                                    '<div style="width:45px; margin: auto;">'.
                                        '<a href="'.$images.'" data-lightbox="customer-profile" class="display-block mbot5">'.
                                            '<div class="">'.
                                                '<img src="'.$images.'" style="border-radius: 50%">'.
                                            '</div>'.
                                        '</a>'.
                                    '</div>'.
                                '</div>'.
                            '</div>'.
                        '</div>';
                $td3 = $value['items_code'];
                $td4 = $value['items_name'];
                $tdUnit = $unit['unit'];
                $td5 = '<div class="text-center">'.number_format($value['quantity']).'</div>';
                $td6 = $html_sub_date;
                $td7 = $value['note_items'];

                $type_item = $value['type_items'];
                $workflow = '';
                if ($type_item == "products" || $type_item == "semi_products") {
                    $itemsProcess = $this->site_model->getProductionsOrdersItemsStages('business_plan', $value['id']);
                    if (!empty($itemsProcess)) {
                        foreach ($itemsProcess as $kk => $vv) {
                            $li = '';
                            $process = $vv['process'];
                            if (!empty($process)) {
                                foreach ($process as $kkk => $vvv) {
                                    $li .= '<li ' . ($vvv['active'] ? 'class="active"' : '') . '>' . $vvv['stage_name'] .
                                        (!empty($vvv['staff_active']) ? ('<p class="active_poin">' . ('Được ' . get_staff_full_name($vvv['staff_active']) . ($vvv['date_active'] ? ' duyệt vào lúc: ' . _dt($vvv['date_active']) : '')) . '</p>') : '')
                                        . '</li>';
                                }
                            }

                            $workflow .= '<div style="display: table; justify-content: center;">
                                <div class="pull-left mtop20"><span class="label label-primary">'.$vv['reference_no'].' (SL: '.formatNumber($vv['quantity']).')</span></div>
                                <ul class="progressbar" style="display: flex;">
                                ' . $li . '
                                </ul>
                            </div>';
                        }
                    } else {
                        $workflow = '<div class="text-danger italic">'.lang('tnh_no_productions_orders_yet').'</div>';
                    }
                } else {
                    $workflow = '';
                }
                $tdWorkFlow = '<td class="hide">' . $workflow . '</td>';

                $tr_html.= '<tr>
                    <td class="details-control">'.$td1.'</td>
                    <td>'.$td2.'</td>
                    <td>'.$td3.'</td>
                    <td>'.$td4.'</td>
                    <td>'.$tdUnit.'</td>
                    <td>'.$td5.'</td>
                    <td>'.$td6.'</td>
                    <td>'.$td7.'</td>
                    '.$tdWorkFlow.'
                </tr>';
            }
        }

        $data['business_plan'] = $business_plan;
        $data['tr_html'] = $tr_html;
        $data['created_by'] = get_staff_full_name($business_plan['created_by']);
        $data['updated_by'] = get_staff_full_name($business_plan['updated_by']);
        $data['user_status'] = get_staff_full_name($business_plan['user_status']);
        $this->load->view('admin/business_plan/view_business_plan', $data);
    }

    function delete($id) {
        $data = [];
        if (!$this->perDeleteBusinessPlan) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die;
        }
        if ($this->input->get('delete')) {
            $business_plan = $this->business_plan_model->rowBusinessPlanById($id);

            if ($business_plan['productions_plan_id']) {

                $this->db->select('GROUP_CONCAT(tbl_productions_plan.reference_no) as reference_no', false);
                $this->db->from('tbl_productions_plan_items');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id');
                $this->db->where('tbl_productions_plan_items.type_object', 'business_plan');
                $this->db->where('tbl_productions_plan_items.object_id', $id);
                $dtPP = $this->db->get()->row_array();

                $data['result'] = 0;
                $data['message'] = lang('Đã lập kế hoạch NPL không thể xóa ['.$dtPP['reference_no'].']');
                echo json_encode($data); die;
            }

            $this->db->from('tbl_tranfer_business_item');
            $this->db->where('tbl_tranfer_business_item.id_business_plan', $id);
            $this->db->limit(1);
            $isTranferBI = $this->db->get()->num_rows();
            if (!empty($isTranferBI)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã tạo phiếu giữ kho trên truyền không thể xóa');
                echo json_encode($data); die;
            }

            $items = $this->business_plan_model->getBusinessPlanItems($id);
            if ($business_plan['status'] == "approved") {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
                echo json_encode($data); die;
            }
            if ($this->business_plan_model->deleteBusinessPlan($id)) {
                $arrItemsId = [];
                foreach ($items as $key => $value) {
                    if ($this->business_plan_model->deleteBusinessPlanItems($value['id'])) {
                        $this->business_plan_model->deleteBusinessPlanItemsDateBusinessPlanItemsId($value['id']);
                    }

                    $arrItemsId[] = $value['items_id'];
                }

                $this->business_plan_model->deleteBusinessPlanItemsStages($id);

                if (!empty($arrItemsId)) {
                    totalBusinessPlan($arrItemsId);
                }

                insertActivityLog([
                    'type_parent_obj' => 'business_plan',
                    'table_obj' => 'tbl_business_plan',
                    'id_obj' => $id,
                    'name_obj' => $business_plan['reference_no'],
                    'content' => lang('tnh_his_delete_business_plan').' ['.$business_plan['reference_no'].']',
                    'actions' => 'delete'
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

    function agree()
    {
        $data = [];
        if (!$this->perApproveBusinessPlan) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data); die;
        }
        if ($this->input->get())
        {
            $business_plan_id = $this->input->get('business_plan_id');

            $this->db->from('tbl_productions_plan');
            $this->db->where("(FIND_IN_SET($business_plan_id, tbl_productions_plan.options2_id) > 0)");
            $pl = $this->db->get()->num_rows();
            if (!empty($pl)) {
                $data['result'] = 0;
                $data['message'] = lang('Đã tạo hoạch định 1 phần không thể bỏ duyệt');
                echo json_encode($data); die;
            }

            $status = $this->input->get('status');
            $business_plan = $this->business_plan_model->rowBusinessPlanById($business_plan_id);
            if ($business_plan['productions_plan_id']) {

                $this->db->select('GROUP_CONCAT(tbl_productions_plan.reference_no) as reference_no', false);
                $this->db->from('tbl_productions_plan_items');
                $this->db->join('tbl_productions_plan', 'tbl_productions_plan.id = tbl_productions_plan_items.productions_plan_id');
                $this->db->where('tbl_productions_plan_items.type_object', 'business_plan');
                $this->db->where('tbl_productions_plan_items.object_id', $business_plan_id);
                $dtPP = $this->db->get()->row_array();

                $data['result'] = 0;
                $data['message'] = lang('Đã lập kế hoạch NPL không thể bỏ duyệt ['.$dtPP['reference_no'].']');
                echo json_encode($data); die;
            }


            $date = date('Y-m-d H:i');
            $user_id = get_staff_user_id();
            if ($business_plan['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data); die;
            }
            $up = $this->business_plan_model->updateBusinessPlan($business_plan_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                insertActivityLog([
                    'type_parent_obj' => 'business_plan',
                    'table_obj' => 'tbl_business_plan',
                    'id_obj' => $business_plan_id,
                    'name_obj' => $business_plan['reference_no'],
                    'content' => lang('tnh_his_agree_business_plan').' ['.$business_plan['reference_no'].']',
                    'actions' => 'agree'
                ]);
                $data['result'] = 1;
                $data['message'] = lang('success');
                @pusherTNHNotfication();
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }

    public function searchBusinessPlan($id = false)
    {
        $data = [];
        $term = $this->input->get('term');
        $limit = 50;
        $data['results'] = $this->business_plan_model->searchBusinessPlan($term, $limit);
        if ($id) {
            // $order = $this->orders_model->rowOrderById($id);
            // $data['row'] = ['id' => $order['id'], 'text' => $order['reference_no']];
        }
        echo json_encode($data);
    }

    // yct start
    public function pdf($id = '')
    {
        ob_end_clean();
        $data = [];

        $business_plan = $this->business_plan_model->rowBusinessPlanById($id);
        if (!$this->perViewBusinessPlan) {
            checkMyData($business_plan['created_by'], true);
        }
        $items = $this->business_plan_model->getBusinessPlanItemsByBusinessPlanId($id);
        // var_dump(get_staff_full_name($business_plan['user_status']));die;

        $data['title'] = lang('Kế hoạch thành phẩm');
        $data['type'] = 'P';
        $data['img'] = '';

        $widthNumber = '5%';
        $widthNameCode = '20%';
        $widthName = '25%';
        $widthUnit = '8%';
        $widthQuantity = '10%';
        $widthDate = '18%';
        $widthNote = '15%';


        $trHeadItems = '<tr>
                <th class="bold text-center" style="width: ' . $widthNumber . ';">' . _l('tnh_numbers') . '</th>
                <th class="bold text-center" style="width: ' . $widthNameCode . ';">' . _l('tnh_product_code') . '</th>
                <th class="bold text-center" style="width: ' . $widthName . ';">' . _l('tnh_product_name') . '</th>
                <th class="bold text-center" style="width: ' . $widthUnit . ';">' . _l('ĐVT') . '</th>
                <th class="bold text-center" style="width: ' . $widthQuantity . ';">' . _l('quantity') . '</th>
                <th class="bold text-center" style="width: ' . $widthDate . ';">' . _l('date') . '</th>
                <th class="bold text-center" style="width: ' . $widthNote . ';">' . _l('note') . '</th>
            </tr>';

        $trFotterItems = '';

        $bodyItems = '';
        $grand_total = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $sub_date = $this->business_plan_model->getBusinessPlanItemsDateByBusinessPlanItemsId($value['id']);
                $html_sub_date = '';
                if (!empty($sub_date)) {
                    foreach ($sub_date as $k => $val) {
                        $html_sub_date.= '<div class="">'._d($val['date']).' - '.number_format($val['quantity']).'</div>';
                    }
                }
                $unit = $this->unit_model->rowUnit($value['unit_id']);

                $tdNumber = '<td class="text-center" style="width: ' . $widthNumber . ';">' . (++$key) . '</td>';
                $tdCode = '<td style="width: ' . $widthNameCode . ';">' . $value['items_code'] . '</td>';
                $tdName = '<td style="width: ' . $widthName . '; text-align: left;">' . $value['items_name'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: ' . $widthUnit . ';">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: ' . $widthQuantity . ';">' . formatNumber($value['quantity']) . '</td>';
                $tdDate = '<td class="text-center" style="width: ' . $widthDate . ';">' . $html_sub_date . '</td>';
                $tdNote = '<td class="text-center" style="width: ' . $widthNote . ';">' . $value['note_items'] . '</td>';


                $bodyItems .= '<tr nobr="true">
                        ' . $tdNumber . '
                        ' . $tdCode . '
                        ' . $tdName . '
                        ' . $tdUnit . '
                        ' . $tdQuantity . '
                        ' . $tdDate . '
                        ' . $tdNote . '
                    </tr>';
            }
        }


        $day = date_format(date_create($business_plan['date']), 'd');
        $month = date_format(date_create($business_plan['date']), 'm');
        $year = date_format(date_create($business_plan['date']), 'Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
                <table class="" cellspacing="0" cellpadding="1" border="0">
                    <tr>
                        <td style="width: 100%;"><span style="font-size: 18px; font-weight: bold;" class="text-center uppercase">' . lang('KẾ HOẠCH THÀNH PHẨM') . '</span></td>
                    </tr>
                    <tr>
                        <td style="width: 70%;"><span class="text-right">Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                    </tr>
                </table>
                <table class="" cellspacing="0" cellpadding="1" border="0">
                    <tr>
                        <td style="width: 100%;"><span class="bold">' . _l('tnh_reference_business_plan') . ':</span> ' . $business_plan['reference_no'] . '</td>
                    </tr>
                    <tr>
                        <td style="width: 100%;"><span class="bold">' . _l('note') . ': </span>' . $business_plan['note'] . '</td>
                    </tr>
                </table>

                <table class="table-items" cellspacing="0" cellpadding="5" border="1">
                    <thead>
                        ' . $trHeadItems . '
                    </thead>
                    <tbody>
                        ' . $bodyItems . '
                    </tbody>
                    <tfoot>
                        ' . $trFotterItems . '
                    </tfoot>
                </table>
              
                <br><br><table style="width: 100%" class="">
                    <tr nobr="true">
                        <td class="text-center" style="width: 50%;">
                            <span class="bold">Người lập phiếu</span><br><br><br><br><br><br>
                            <span>'.get_staff_full_name($business_plan['created_by']).'</span>
                        </td>
                        <td class="text-center" style="width: 50%;">
                            <span class="bold">Người duyệt</span><br><br><br><br><br><br>
                            <span>'.get_staff_full_name($business_plan['user_status']).'</span>
                        </td>
                    </tr>
                </table>
            ';

        $content = ob_get_contents();
        ob_end_clean();

        $content = str_replace('font-size: 12pt;', '', $content);
        $data['content'] = $content;
        $data['type_page'] = 'deliveries';
        $pdf = @print_pdf_tnh($data);
        $type = 'I';
        $pdf->Output(slug_it('Ke_hoach_thanh_pham') . '.pdf', $type);
    }
}

?>