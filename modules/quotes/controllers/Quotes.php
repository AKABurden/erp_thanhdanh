<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotes extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
        $this->load->model('quotes_model');
        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('clients_model');
        $this->load->model('unit_model');
        $this->load->model('type_orders_model');
        $this->load->model('status_orders_model');
        $this->tnh = true;

        //permission quotes
        $this->perViewQuotes = has_permission('quotes', '', 'view');
        $this->perViewOwnQuotes = has_permission('quotes', '', 'view_own');
        $this->perAddQuotes = has_permission('quotes', '', 'create');
        $this->perEditQuotes = has_permission('quotes', '', 'edit');
        $this->perDeleteQuotes = has_permission('quotes', '', 'delete');
        $this->perExportQuotes = has_permission('quotes', '', 'export');
        $this->perApproveQuotes = has_permission('quotes', '', 'approve');
        $this->perPrintQuotes = has_permission('quotes', '', 'print');
        $this->is_branch = true;
        // print_arrays(ini_get('upload_tmp_dir'), sys_get_temp_dir());
    }

    public function index()
    {
        if (!$this->perViewQuotes && !$this->perViewOwnQuotes) {
            accessDenied();
        }

        $data['un_approved'] = $this->quotes_model->countQuotesStatus('un_approved');
        $data['approved'] = $this->quotes_model->countQuotesStatus('approved');
        $data['cancel'] = $this->quotes_model->countQuotesStatus('cancel');
        $data['un_created_an_order'] = $this->quotes_model->countQuotesStatus('un_created_an_order');
        $data['created_an_order'] = $this->quotes_model->countQuotesStatus('created_an_order');
        $data['all'] = $this->quotes_model->countQuotesStatus('all');

        $data['tnh'] = $this->tnh;
        $data['title'] = lang('tnh_quotes');
        $this->load->view('manage', $data);
    }

    public function add()
    {
        if (!$this->perAddQuotes) {
            accessDenied();
        }
        if ($this->input->post('add')) {
            $data = [];
            $this->form_validation->set_rules('reference_no', lang("tnh_reference_no_quote"), 'trim|required|is_unique[tbl_quotes.reference_no]');
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('id_branch', lang("id_branch"), 'required');
            // $this->form_validation->set_rules('person_contact', lang("tnh_person_contact"), 'required');
            if ($this->form_validation->run() == true) {
                // $reference_no = $this->input->post('reference_no');
                $reference_no = getReference('quotes');
                $pre_reference_no = $this->input->post('pre_reference_no');
                if (!empty($pre_reference_no)) {
                    $reference_no_current = $this->getReferenceByPreRef($pre_reference_no);
                    if ($reference_no != $reference_no_current) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_please_refresh_reference_no');
                        echo json_encode($data);
                        die;
                    }
                }


                $date = to_sql_date($this->input->post('date'), true);
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $validity = $this->input->post('validity') ? to_sql_date($this->input->post('validity'), true) : null;
                // $note = tnh_htmlentities($this->input->post('note', false));
                $note = $this->input->post('note', false);
                $note_internal = tnh_htmlentities($this->input->post('note_internal', false));
                $parts_origin = tnh_htmlentities($this->input->post('list_parts_origin', false));
                $delivery = tnh_htmlentities($this->input->post('delivery', false));
                $installation_cost = tnh_htmlentities($this->input->post('installation_cost', false));
                $update_on_items = $this->input->post('update_on_items') ? $this->input->post('update_on_items') : 0;
                $person_contact_id = $this->input->post('person_contact');
                $address_delivery_id = $this->input->post('address_delivery');
                $note_default_id = $this->input->post('note_default');
                $note_default_id = !empty($note_default_id) ? implode(',', $note_default_id) : null;
                $expiration_date = !empty($this->input->post('expiration_date')) ? to_sql_date($this->input->post('expiration_date')) : NULL;

                $currencies = $this->input->post('currencies');
                $amount_to_vnd = number_unformat($this->input->post('amount_to_vnd'));
                $delivery_term = $this->input->post('delivery_term');
                $ship_to = $this->input->post('ship_to');
                $payment_detail = $this->input->post('payment_detail');
                $payment_term = $this->input->post('payment_term');
                $bale_parameters = $this->input->post('bale_parameters');
                $id_branch = $this->input->post('id_branch');
                $quotation_request_id = $this->input->post('quotation_request_id');
                $is_quote_again = $this->input->post('is_quote_again');

                $total = 0;
                $total_quantity = 0;
                $grand_total = 0;
                $count_items = 0;
                $tax_id = $this->input->post('tax_id') ? $this->input->post('tax_id') : 0;
                $tax_name = 0;
                $tax_rate = 0;
                $total_tax = 0;

                $items_id = $this->input->post('items_id');
                if (!empty($items_id)) {
                    foreach ($items_id as $key => $value) {
                        if (empty($value)) continue;
                        $arrs = explode('__', $value);
                        $items_id = $arrs[0];
                        $type_item = $arrs[1];
                        $counter = $this->input->post('counterQuotes')[$key];

                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($items_id);
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($items_id);
                        }
                        if (empty($info)) {
                            continue;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];
                        $origin = $this->input->post('origin')[$key];
                        $note_item = $this->input->post('note_items')[$key];
                        $num = !empty($this->input->post('num')[$key]) ? $this->input->post('num')[$key] : '';
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $price = number_unformat($this->input->post('price')[$key]);
                        $info_html = tnh_htmlentities($this->input->post('info', false)[$key]);
                        $data_json = !empty($this->input->post('data_json', false)[$key]) ? $this->input->post('data_json', false)[$key] : null;
                        $technical_explanation = !empty($this->input->post('technical_explanation', false)[$key]) ? $this->input->post('technical_explanation', false)[$key] : '';
                        $discount_precent_item = !empty($this->input->post('discount_precent_item')[$key]) ? number_unformat($this->input->post('discount_precent_item')[$key]) : 0;
                        $leadtime = !empty($this->input->post('leadtime')[$key]) ? number_unformat($this->input->post('leadtime')[$key]) : '';
                        $moq = !empty($this->input->post('moq')[$key]) ? number_unformat($this->input->post('moq')[$key]) : '';
                        $moq_to = !empty($this->input->post('moq_to')[$key]) ? number_unformat($this->input->post('moq_to')[$key]) : '';
                        $quote_stage_id = !empty($this->input->post('quote_stage_id')[$key]) ? $this->input->post('quote_stage_id')[$key] : '';

                        $amount = $quantity * $price;

                        $items[] = [
                            'type_item' => $type_item,
                            'item_id' => $items_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'origin' => $origin,
                            'quantity' => $quantity,
                            'unit_price' => $price,
                            'total_amount' => $amount,
                            'note_item' => $note_item,
                            'lead_time' => $leadtime,
                            'info' => $info_html,
                            'num' => $num,
                            'technical_explanation' => $technical_explanation,
                            'discount_precent_item' => $discount_precent_item,
                            'moq' => $moq,
                            'moq_to' => $moq_to,
                            'quote_stage_id' => $quote_stage_id,
                            'data_price_json' => $data_json,
                        ];

                        $total_quantity += $quantity;
                        $total += $amount;
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                $total_quantity_charge = 0;
                $total_charge = 0;
                $name_charge = $this->input->post('name_charge');
                if (!empty($name_charge)) {
                    foreach ($name_charge as $key => $value) {
                        if (empty($value)) continue;
                        $quantity_charge = number_unformat($this->input->post('quantity_charge')[$key]);
                        $price_charge = number_unformat($this->input->post('price_charge')[$key]);
                        $amount_charge = $quantity_charge * $price_charge;

                        $items_charge[] = [
                            'name_charge' => $value,
                            'quantity_charge' => $quantity_charge,
                            'price_charge' => $price_charge,
                            'total_amount_charge' => $amount_charge,
                        ];
                        $total_quantity_charge += $quantity_charge;
                        $total_charge += $amount_charge;
                    }
                }

                $payment = $this->input->post('payment');

                // print_arrays($items);
                $count_items = count($items);
                $count_charge = 0;
                if (!empty($items_charge)) {
                    $count_charge = count($items_charge);
                }
                $grand_total = $total + $total_charge;

                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }
                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }
                $grand_total += $total_tax;

                $options = [
                    'date' => $date,
                    'parent_id' => $pre_reference_no,
                    'reference_no' => $reference_no,
                    'customer_id' => $customer_id,
                    'type_customer' => $type_customer,
                    'validity' => $validity,
                    'count_items' => $count_items,
                    'total' => $total,
                    'count_charge' => $count_charge,
                    'total_quantity' => $total_quantity,
                    'total_charge' => $total_charge,
                    'total_quantity_charge' => $total_quantity_charge,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'grand_total' => $grand_total,
                    'parts_origin' => $parts_origin,
                    'delivery' => $delivery,
                    'installation_cost' => $installation_cost,
                    'update_on_items' => $update_on_items,
                    'note' => $note,
                    'note_internal' => $note_internal,
                    'order_id' => 0,
                    'status' => 'un_approved',
                    'date_created' => date('Y-m-d H:i'),
                    'created_by' => get_staff_user_id(),
                    'person_contact_id' => $person_contact_id,
                    'address_delivery_id' => $address_delivery_id,
                    'note_default_id' => $note_default_id,
                    'currencies' => $currencies,
                    'amount_to_vnd' => $amount_to_vnd,
                    'delivery_term' => $delivery_term,
                    'ship_to' => $ship_to,
                    'payment_detail' => $payment_detail,
                    'payment_term' => $payment_term,
                    'bale_parameters' => $bale_parameters,
                    'expiration_date' => $expiration_date,
                    'id_branch' => $id_branch,
                    'quotation_request_id' => $quotation_request_id,
                    'is_quote_again' => $is_quote_again,
                ];
                // print_arrays($options);

                $quote_id = $this->quotes_model->insertQuotes($options);
                if ($quote_id) {

                    // if (getReference('quotes') == $this->input->post('reference_no')) {
                    updateReference('quotes');
                    // }

                    foreach ($items as $key => $value) {
                        $value['quote_id'] = $quote_id;
                        $type_item = $value['type_item'];
                        $item_id = $value['item_id'];
                        $info = $value['info'];
                        if ($this->quotes_model->insertQuoteItems($value)) {
                            if ($update_on_items) {
                                if ($type_item == "products") {
                                    $this->products_model->updateProducts($item_id, ['info' => $info]);
                                } else if ($type_item == "items") {
                                    $this->products_model->updateItems($item_id, ['info' => $info]);
                                }
                            }
                        }
                    }

                    if (!empty($bale_parameters)) {
                        $this->db->where('tblclients.userid', $customer_id);
                        $this->db->update('tblclients', ['bale_parameters' => $bale_parameters]);
                    }

                    noti_custom('create_quotes', $quote_id, get_staff_user_id(), 0, '', ['actions' => 'add']);

                    @pusherTNHNotfication();
                    insertActivityLog([
                        'type_parent_obj' => 'quotes',
                        'table_obj' => 'tbl_quotes',
                        'id_obj' => $quote_id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_add_quotes') . ' [' . $reference_no . ']',
                        'actions' => 'add'
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

        $data['currencies'] = $this->site_model->getCurrencies();
        $data['taxs'] = $this->site_model->getTaxs();
        $data['quotes_note_default'] = $this->site_model->getQuotesNoteDefault();
        $data['reference_no'] = getReference('quotes');
        $data['branch'] = $this->db->get('tblbranch')->result_array();
        $data['tnh'] = true;
        $data['title'] = lang('tnh_add_quote');
        $data['breadcrumb'] = [array('link' => base_url('admin/quotes'), 'page' => lang('tnh_quotes')), array('link' => '#', 'page' => lang('tnh_add_quote'))];
        $this->load->view('add', $data);
    }

    public function edit($id)
    {
        if (!$this->perEditQuotes) {
            accessDenied();
        }
        $quote = $this->quotes_model->rowQuotesById($id);
        if (empty($quote)) {
            set_alert('danger', lang('no_data_exists'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                $list_branch = get_array_branch_staff();
                if (!empty($list_branch)) {
                    $this->db->group_start();
                    $this->db->where_in('tbl_quotes.id_branch', $list_branch);
                    $this->db->group_end();
                } else {
                    $this->db->where('tbl_quotes.id = 0', false, false);
                }
                $this->db->where('id', $id);
                $ktQuote = $this->db->get('tbl_quotes')->row();
                if (empty($ktQuote)) {
                    accessDenied();
                }
            }
        }



        if ($quote['status'] == "approved" || $quote['status'] == "cancel") {
            set_alert('danger', lang('Đã duyệt và không đạt không thể sửa'));
            redirect($_SERVER["HTTP_REFERER"]);
            die;
        }

        $pre_quote = false;
        if ($quote['parent_id'] > 0) {
            $pre_quote = $this->quotes_model->rowQuotesById($id);
        }

        if ($this->input->post('edit')) {
            $data = [];
            if ($quote['reference_no'] != $this->input->post('reference_no')) {
                $this->form_validation->set_rules('reference_no', lang("tnh_reference_no_quote"), 'trim|required|is_unique[tbl_quotes.reference_no]');
            }
            $this->form_validation->set_rules('date', lang("date"), 'required');
            $this->form_validation->set_rules('customers', lang("customers"), 'required');
            $this->form_validation->set_rules('id_branch', lang("id_branch"), 'required');
            // $this->form_validation->set_rules('person_contact', lang("tnh_person_contact"), 'required');
            if ($this->form_validation->run() == true) {
                $reference_no = $this->input->post('reference_no');

                $date = to_sql_date($this->input->post('date'), true);
                // $customer = $this->input->post('customers');
                $customer = explode("__", $this->input->post('customers'));
                $type_customer = $customer[0];
                $customer_id = $customer[1];
                $validity = $this->input->post('validity') ? to_sql_date($this->input->post('validity'), true) : null;
                // $note = tnh_htmlentities($this->input->post('note', false));
                $note = $this->input->post('note', false);
                $note_internal = tnh_htmlentities($this->input->post('note_internal', false));
                $parts_origin = tnh_htmlentities($this->input->post('list_parts_origin', false));
                $delivery = tnh_htmlentities($this->input->post('delivery', false));
                $installation_cost = tnh_htmlentities($this->input->post('installation_cost', false));
                $update_on_items = $this->input->post('update_on_items') ? $this->input->post('update_on_items') : 0;
                $person_contact_id = $this->input->post('person_contact');
                $person_contact_id = str_replace('customers__', '', $person_contact_id);
                $person_contact_id = str_replace('leads__', '', $person_contact_id);
                $address_delivery_id = $this->input->post('address_delivery');
                $address_delivery_id = str_replace('customers__', '', $address_delivery_id);
                $expiration_date = !empty($this->input->post('expiration_date')) ? to_sql_date($this->input->post('expiration_date')) : NULL;

                $note_default_id = $this->input->post('note_default');
                $note_default_id = !empty($note_default_id) ? implode(',', $note_default_id) : null;

                $currencies = $this->input->post('currencies');
                $amount_to_vnd = number_unformat($this->input->post('amount_to_vnd'));
                $delivery_term = $this->input->post('delivery_term');
                $ship_to = $this->input->post('ship_to');
                $payment_detail = $this->input->post('payment_detail');
                $payment_term = $this->input->post('payment_term');
                $bale_parameters = $this->input->post('bale_parameters');
                $id_branch = $this->input->post('id_branch');
                $quotation_request_id = $this->input->post('quotation_request_id');
                $is_quote_again = $this->input->post('is_quote_again');

                $total = 0;
                $total_quantity = 0;
                $grand_total = 0;
                $count_items = 0;
                $tax_id = $this->input->post('tax_id') ? $this->input->post('tax_id') : 0;
                $tax_name = 0;
                $tax_rate = 0;
                $total_tax = 0;

                $items_id = $this->input->post('items_id');
                if (!empty($items_id)) {
                    foreach ($items_id as $key => $value) {
                        if (empty($value)) continue;
                        $arrs = explode('__', $value);
                        $items_id = $arrs[0];
                        $type_item = $arrs[1];
                        // $counter = $this->input->post('counter')[$key];

                        if ($type_item == "products") {
                            $info = $this->products_model->rowProduct($items_id);
                        } else if ($type_item == "items") {
                            $info = $this->items_model->rowItems($items_id);
                        }
                        if (empty($info)) {
                            continue;
                        }

                        $items_code = $info['code'];
                        $items_name = $info['name'];
                        $origin = $this->input->post('origin')[$key];
                        $note_item = $this->input->post('note_items')[$key];
                        $num = !empty($this->input->post('num')[$key]) ? $this->input->post('num')[$key] : '';
                        $quantity = number_unformat($this->input->post('quantity')[$key]);
                        $price = number_unformat($this->input->post('price')[$key]);
                        $info_html = tnh_htmlentities($this->input->post('info', false)[$key]);
                        $data_json = !empty($this->input->post('data_json', false)[$key]) ? $this->input->post('data_json', false)[$key] : null;

                        $technical_explanation = !empty($this->input->post('technical_explanation', false)[$key]) ? $this->input->post('technical_explanation', false)[$key] : '';
                        $discount_precent_item = !empty($this->input->post('discount_precent_item')[$key]) ? number_unformat($this->input->post('discount_precent_item')[$key]) : 0;
                        $leadtime = !empty($this->input->post('leadtime')[$key]) ? number_unformat($this->input->post('leadtime')[$key]) : '';
                        $moq = !empty($this->input->post('moq')[$key]) ? number_unformat($this->input->post('moq')[$key]) : '';
                        $moq_to = !empty($this->input->post('moq_to')[$key]) ? number_unformat($this->input->post('moq_to')[$key]) : '';
                        $quote_stage_id = !empty($this->input->post('quote_stage_id')[$key]) ? $this->input->post('quote_stage_id')[$key] : '';

                        $amount = $quantity * $price;

                        $items[] = [
                            'type_item' => $type_item,
                            'item_id' => $items_id,
                            'item_code' => $items_code,
                            'item_name' => $items_name,
                            'origin' => $origin,
                            'quantity' => $quantity,
                            'unit_price' => $price,
                            'total_amount' => $amount,
                            'note_item' => $note_item,
                            'lead_time' => $leadtime,
                            'info' => $info_html,
                            'num' => $num,
                            'data_price_json' => $data_json,
                            'technical_explanation' => $technical_explanation,
                            'discount_precent_item' => $discount_precent_item,
                            'moq' => $moq,
                            'moq_to' => $moq_to,
                            'quote_stage_id' => $quote_stage_id,
                        ];

                        $total_quantity += $quantity;
                        $total += $amount;
                    }
                }

                if (empty($items)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                $total_quantity_charge = 0;
                $total_charge = 0;
                $name_charge = $this->input->post('name_charge');
                if (!empty($name_charge)) {
                    foreach ($name_charge as $key => $value) {
                        if (empty($value)) continue;
                        $quantity_charge = number_unformat($this->input->post('quantity_charge')[$key]);
                        $price_charge = number_unformat($this->input->post('price_charge')[$key]);
                        $amount_charge = $quantity_charge * $price_charge;

                        $items_charge[] = [
                            'name_charge' => $value,
                            'quantity_charge' => $quantity_charge,
                            'price_charge' => $price_charge,
                            'total_amount_charge' => $amount_charge,
                        ];
                        $total_quantity_charge += $quantity_charge;
                        $total_charge += $amount_charge;
                    }
                }

                $payment = $this->input->post('payment');

                // print_arrays($items);
                $count_items = count($items);
                $count_charge = 0;
                if (!empty($items_charge)) {
                    $count_charge = count($items_charge);
                }
                $grand_total = $total + $total_charge;

                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }
                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }
                $grand_total += $total_tax;

                $options = [
                    'date' => $date,
                    // 'parent_id' => $pre_reference_no,
                    'reference_no' => $reference_no,
                    // 'customer_id' => $customer,
                    'customer_id' => $customer_id,
                    'type_customer' => $type_customer,
                    'validity' => $validity,
                    'count_items' => $count_items,
                    'total' => $total,
                    'count_charge' => $count_charge,
                    'total_quantity' => $total_quantity,
                    'total_charge' => $total_charge,
                    'total_quantity_charge' => $total_quantity_charge,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'grand_total' => $grand_total,
                    'parts_origin' => $parts_origin,
                    'delivery' => $delivery,
                    'installation_cost' => $installation_cost,
                    'update_on_items' => $update_on_items,
                    'note' => $note,
                    'note_internal' => $note_internal,
                    // 'status' => 'un_approved',
                    'date_updated' => date('Y-m-d H:i'),
                    'updated_by' => get_staff_user_id(),
                    'person_contact_id' => $person_contact_id,
                    'address_delivery_id' => $address_delivery_id,
                    'note_default_id' => $note_default_id,
                    'currencies' => $currencies,
                    'amount_to_vnd' => $amount_to_vnd,
                    'delivery_term' => $delivery_term,
                    'ship_to' => $ship_to,
                    'payment_detail' => $payment_detail,
                    'payment_term' => $payment_term,
                    'bale_parameters' => $bale_parameters,
                    'expiration_date' => $expiration_date,
                    'id_branch' => $id_branch,
                    'quotation_request_id' => $quotation_request_id,
                    'is_quote_again' => $is_quote_again,

                ];

                $up = $this->quotes_model->updateQuotes($id, $options);
                if ($up) {
                    $quote_id = $id;
                    if (getReference('quotes') == $this->input->post('reference_no')) {
                        updateReference('quotes');
                    }

                    $this->quotes_model->deleteQuoteItemsByQuoteId($quote_id);
                    $this->quotes_model->deleteQuotePaymentsQuoteId($quote_id);
                    $this->quotes_model->deleteQuoteChargesByQuoteId($quote_id);

                    foreach ($items as $key => $value) {
                        $value['quote_id'] = $quote_id;
                        $type_item = $value['type_item'];
                        $item_id = $value['item_id'];
                        $info = $value['info'];
                        if ($this->quotes_model->insertQuoteItems($value)) {
                            if ($update_on_items) {
                                if ($type_item == "products") {
                                    $this->products_model->updateProducts($item_id, ['info' => $info]);
                                } else if ($type_item == "items") {
                                    $this->products_model->updateItems($item_id, ['info' => $info]);
                                }
                            }
                        }
                    }

                    if (!empty($bale_parameters)) {
                        $this->db->where('tblclients.userid', $customer_id);
                        $this->db->update('tblclients', ['bale_parameters' => $bale_parameters]);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'quotes',
                        'table_obj' => 'tbl_quotes',
                        'id_obj' => $quote_id,
                        'name_obj' => $reference_no,
                        'content' => lang('tnh_his_edit_quotes') . ' [' . $reference_no . ']',
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

        $quote_charges = $this->quotes_model->getQuoteChargesByQuoteId($id);

        $items = $this->quotes_model->getQuoteItemsByQuoteId($id);
        $li = '';
        $tab_content = '';
        $body_items = '';
        $counter = 0;
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $quote_stage_id = $value['quote_stage_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $items_id = $items_id . '__' . $type_item;

                $tdNumber = '
                    <input type="hidden" name="quote_item_id[]" id="quote_item_id" class="form-control" value="' . $value['id'] . '">
                    <div class="stt text-center">' . (++$key) . '</div>';
                $tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter[' . $counter . ']" id="counter" class="form-control counter" value="' . $counter . '">
                        <input type="text" name="items_id[' . $counter . ']" id="items_' . $counter . '" class="items_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $items_id . '"></div>
                        <div class="type-item">' . (($type_item == "products") ? '<span class="label label-success">' . lang($type_item) . '</span>' : '<span class="label label-primary">' . lang($type_item) . '</span>') . '</div>';
                $tdImage = '<div class="td-image">' .
                    '<div class="preview_image" style="width: auto;">' .
                    '<div class="display-block contract-attachment-wrapper img">' .
                    '<div style="width:45px;">' .
                    '<a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5">' .
                    '<div class="">' .
                    '<img src="' . $images . '" style="border-radius: 50%">' .
                    '</div>' .
                    '</a>' .
                    '</div>' .
                    '</div>' .
                    '</div>' .
                    '</div>';
                $tdQuote_stage = '<div class="td-quote_stage mbot10">
									<input type="text" name="quote_stage_id[' . $counter . ']" id="quote_stage_id_' . $counter . '" class="quote_stage_id" style="width: 100%;" data-placeholder="' . lang('choose') . '" value="' . $quote_stage_id . '">
								</div>';
                $tdName = '<div class="td-item-name">' . $info['name'] . '</div>';

                $tdTechnicalExplanation = '<div class="td-technical-explanation">
                    <textarea name="technical_explanation[' . $counter . ']" class="form-control technical_explanation" placeholder="' . lang('tnh_technical_explanation') . '" rows="3">' . $value['technical_explanation'] . '</textarea>
                </div>';

                $tdNum = '<div class="td-num"><input type="text" placeholder="" name="num[' . $counter . ']" id="num[]" class="form-control num" style="width: 100%;" value="' . $value['num'] . '"></div>';
                $tdOrigin = '<div class="td-origin"><input type="text" placeholder="' . lang('origin') . '" name="origin[' . $counter . ']" id="origin[]" class="form-control origin" style="width: 100px;" value="' . $value['origin'] . '"></div>';
                $tdUnit = '<div class="td-unit">' . $unit['unit'] . '</div>';

                $tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[' . $counter . ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="' . formatNumber($value['quantity']) . '"></div>';

                // $tdMOQ = '<div class="td-quantity"><input type="text" name="moq['.$counter.']" class="form-control moq number-format" style="width: 100%;" value="'.formatNumber($value['moq']).'"></div>';

                $tdMOQ = '<div class="td-quantity" style="display: flex; align-items: center;">
                    <div style="width: 70px;">Từ</div>
                    <input type="text" name="moq[' . $counter . ']" class="form-control moq number-format" style="width: 100%;" value="' . formatNumber($value['moq']) . '">
                    <div style="width: 100px; margin-left: 5px; margin-right: 5px;"> - đến</div> 
                    <input type="text" name="moq_to[' . $counter . ']" class="form-control moq_to number-format" style="width: 100%;" value="' . formatNumber($value['moq_to']) . '">
                </div>';

                $tdPrice = '<div class="td-price">
                    <input type="hidden" name="data_json[' . $counter . ']" class="form-control data_json" value="' . tnh_htmlentities($value['data_price_json']) . '">
                    <input type="text" name="price[' . $counter . ']" id="price[]" class="form-control price money-format" style="width: 100%;" value="' . formatMoney($value['unit_price']) . '">
                    <div class="mtop5"><i onclick="addListPrice(this)" class="btn btn-primary addListPrice">Chi tiết tính giá</i></div>
                </div>';
                $tdTotalAmount = '<div class="td-total-amount text-right">' . formatMoney($value['total_amount']) . '</div>';

                $tdDiscountPrecent = '<div class="td-discount"><input type="text" name="discount_precent_item[' . $counter . ']" class="form-control discount_precent_item number-format" style="width: 100%;" value="' . $value['discount_precent_item'] . '"></div>';

                $tdLeadTime = '<div class="td-lead-time"><input type="number" name="leadtime[' . $counter . ']" class="form-control leadtime number-unformat" style="width: 100%;" value="' . $value['lead_time'] . '"></div>';

                $tdNote = '<div class="td-note"><textarea name="note_items[' . $counter . ']" id="note_items[]" class="form-control" rows="3">' . $value['note_item'] . '</textarea></div>';
                $tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

                $body_items .= '<tr>
                    <td>' . $tdNumber . '</td>
                    <td>' . $tdCode . '</td>
                    <td>' . $tdImage . '</td>
                    <td>' . $tdQuote_stage . '</td>
                    <td>' . $tdTechnicalExplanation . '</td>
                    <td>' . $tdUnit . '</td>
                    <td>' . $tdMOQ . '</td>
                    <td>' . $tdPrice . '</td>
                    <td>' . $tdDiscountPrecent . '</td>
                    <td>' . $tdLeadTime . '</td>
                    <td>' . $tdNote . '</td>
                    <td>' . $tdActions . '</td>
                </tr>';

                $li .= '<li role="presentation" class="' . ($key == 1 ? 'active' : '') . '">
                    <a href="#' . $counter . '" aria-controls="' . $counter . '" role="tab" data-toggle="tab">' . $info['code'] . '</a>
                </li>';

                $tab_content .= '<div role="tabpanel" class="tab-pane ' . ($key == 1 ? 'active' : '') . '" id="' . $counter . '"><textarea name="info[' . $counter . ']" id="info' . $counter . '" class="form-control info" rows="3">' . $value['info'] . '</textarea></div>';

                $counter++;
            }
        }

        $data['currencies'] = $this->site_model->getCurrencies();
        $quote_payments = $this->quotes_model->getQuotePayments($id);
        $data['taxs'] = $this->site_model->getTaxs();
        $data['quotes_note_default'] = $this->site_model->getQuotesNoteDefault();
        $data['quote'] = $quote;
        $data['pre_quote'] = $pre_quote;
        $data['quote_charges'] = $quote_charges;
        $data['quote_payments'] = $quote_payments;
        $data['body_items'] = $body_items;
        $data['li'] = $li;
        $data['tab_content'] = $tab_content;
        $data['counter'] = $counter;
        $data['branch'] = $this->db->get('tblbranch')->result_array();
        // $data['items'] = $items;
        $data['tnh'] = true;
        $data['title'] = lang('tnh_edit_quote');
        $data['breadcrumb'] = [array('link' => base_url('admin/quotes'), 'page' => lang('tnh_quotes')), array('link' => '#', 'page' => lang('tnh_edit_quote'))];
        $this->load->view('edit', $data);
    }

    public function getQuotes()
    {

        if (!$this->perViewQuotes && !$this->perViewOwnQuotes) {
            accessDenied();
        }
        if (!empty($this->is_branch)) {
            $is_admin = true;
            if (!is_admin()) {
                $list_branch = get_list_branch_staff();
                $is_admin = false;
            }
        }

        $start_date_search = $this->input->post('start_date_search');
        $end_date_search = $this->input->post('end_date_search');
        $quotes_search = $this->input->post('quotes_search');
        $customers_search = $this->input->post('customers_search');
        $status = $this->input->post('status_table');
        $products_search = $this->input->post('products_search');

        $pre_reference_no = "(
            SELECT qo.reference_no
            FROM tbl_quotes as qo
            WHERE tbl_quotes.parent_id = qo.id
        )";

        $customer = "(
            SELECT tblclients.company_short
            FROM tblclients
            WHERE tblclients.userid = tbl_quotes.customer_id
        )";

        $lead = "(
            SELECT tblleads.name
            FROM tblleads
            WHERE tblleads.id = tbl_quotes.customer_id
        )";

        // IF(tblclients.fullname IS NOT null, tblclients.fullname, CONCAT(tblclients.prefix_client, '', tblclients.code_client)) as customer,
        // CONCAT(tbl_contracts_sales.prefix, '-', tbl_contracts_sales.code) as status_contracts

        if (!empty($this->is_branch)) {
            if (!$is_admin) {
                if (!empty($list_branch)) {
                    $this->datatables->where('(tbl_quotes.id_branch IN (' . $list_branch . '))');
                } else {
                    $this->datatables->where('tbl_quotes.id_branch = 0', false, false);
                }
            }
        }

        $this->datatables->select("
            tbl_quotes.id as id,
            tbl_quotes.date as date,
            tbl_quotes.reference_no as reference_no,
            IF (tbl_quotes.type_customer = 'customers', $customer, $lead) as customer,
            tbl_quotes.grand_total as grand_total,
            tbl_quotes.note_internal as note,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname,'') as created_by,
            tbl_quotes.status as status,
            CONCAT(staff_status.firstname, ' ', staff_status.lastname,'') as user_status,
            tbl_orders.reference_no as status_order,
            tblbranch.name as name_branch,
            0 as production_report,
        ", FALSE)

            ->from('tbl_quotes')
            ->join('tbl_orders', 'tbl_orders.id = tbl_quotes.order_id', 'left')
            ->join('tblclients', 'tblclients.userid = tbl_quotes.customer_id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbl_quotes.created_by', 'left')
            ->join('tblstaff staff_status', 'staff_status.staffid = tbl_quotes.user_status', 'left')
            ->join('tblbranch', 'tblbranch.id = tbl_quotes.id_branch', 'left');

        if (!empty($quotes_search)) {
            $this->datatables->where('tbl_quotes.id', $quotes_search);
        }

        if (!empty($products_search)) {
            $products_search = str_replace('__products', '', $products_search);
            $this->datatables->where(' exists (
                SELECT 1
                FROM tbl_quote_items
                WHERE tbl_quote_items.quote_id = tbl_quotes.id AND tbl_quote_items.item_id = ' . $products_search . '
            )', false, false);
        }

        if (!empty($customers_search)) {
            $customer = explode('__', $customers_search);
            $this->datatables->where('tbl_quotes.customer_id', $customer[1]);
        }

        if (!empty($start_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_quotes.date, "%Y-%m-%d") >=', to_sql_date($start_date_search));
        }

        if (!empty($end_date_search)) {
            $this->datatables->where('DATE_FORMAT(tbl_quotes.date, "%Y-%m-%d") <=', to_sql_date($end_date_search));
        }

        if (!empty($status) && $status != 'all') {
            if ($status == 'un_approved') {
                $this->datatables->where('tbl_quotes.status', 'un_approved');
            } else if ($status == 'cancel') {
                $this->datatables->where('tbl_quotes.status', 'cancel');
            } else if ($status == 'approved') {
                $this->datatables->where('tbl_quotes.status', 'approved');
            } else if ($status == 'un_created_an_order') {
                $this->datatables->where('tbl_quotes.order_id', 0);
            } else if ($status == 'created_an_order') {
                $this->datatables->where('tbl_quotes.order_id >', 0);
            }
        }

        if (!$this->perViewQuotes) {
            $this->datatables->where('tbl_quotes.created_by', get_staff_user_id());
        }

        $custom[] = ['index' => 3, 'select' => 'customer'];
        $custom_select[3] = "IF (tbl_quotes.type_customer = 'customers', $customer, $lead)";
        $this->datatables->custom_ordering($custom);
        $this->datatables->custom_select($custom_select);

        $view = '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotes/view_quotes/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-file-text-o"></i> ' . lang('view') . ' ' . lang('quotes') . '</a>';

        // $print = $this->perPrintQuotes ? '<a href="'.base_url('admin/quotes/print_quotes/$1').'" target="_blank"><i class="fa fa-print"></i> '.lang('print').'</a>' : '';

        $print = $this->perPrintQuotes ? '<a href="' . base_url('admin/quotes/print_pdf/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('print') . ' ' . lang('quotes') . '</a>' : '';

        $edit = $this->perEditQuotes ? '<a href="' . base_url('admin/quotes/edit/$1') . '"><i class="fa fa-edit"></i> ' . lang('edit') . ' ' . lang('quotes') . '</a>' : '';

        $email = $this->perPrintQuotes ? '<a data-tnh="modal" class="tnh-modal" href="' . base_url('admin/quotes/email_quotes/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-envelope-o"></i> ' . lang('Email') . ' ' . lang('quotes') . '</a>' : '';

        $convert_order = $this->perAddQuotes ? '<a data-tnh="modal" class="tnh-modal cv" href="' . base_url('admin/quotes/convert_order/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-exchange"></i> ' . lang('tnh_convert_order') . '</a>' : '';

        $convert_order_sample = $this->perAddQuotes ? '<a data-tnh="modal" class="tnh-modal cv" href="' . base_url('admin/quotes/convert_order/$1/1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-exchange"></i> ' . lang('tnh_convert_order') . ' PTM</a>' : '';

        $convert_contract = $this->perAddQuotes ? '<a  data-tnh="modal" class="tnh-modal cvc" href="' . base_url('admin/quotes/convert_contract/$1') . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-exchange"></i> ' . lang('tnh_convert_contract') . '</a>' : '';

        $export_excel = $this->perPrintQuotes ? '<a href="' . base_url('admin/quotes/exportExcelQuotes/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('Excel') . ' ' . lang('quotes') . '</a>' : '';

        $export_excel_prices = $this->perPrintQuotes ? '<a href="' . base_url('admin/quotes/exportExcelPriceQuotes/$1') . '" target="_blank"><i class="fa fa-print"></i> ' . lang('Excel') . ' ' . lang('bảng giá') . '</a>' : '';

        $delete = $this->perAddQuotes ? '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/quotes/delete_quotes/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove width-icon-actions"></i> ' . lang('delete') . ' ' . lang('quotes') . '</a>' : '';

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            ' . lang('actions') . '
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>' . $view . '</li>
                <li>' . $edit . '</li>
                <li>' . $email . '</li>
                <li>' . $convert_order . '</li>
                <li>' . $convert_order_sample . '</li>
                <li>' . $print . '</li>
                <li>' . $export_excel . '</li>
                <li>' . $export_excel_prices . '</li>
                <li class="not-outside">' . $delete . '</li>
            </ul>
        </div>';

        // print_arrays($this->db->get_compiled_select(), FALSE);
        $this->datatables->add_column('actions', $actions, 'id');
        // $'iD'isplayStart = $this->input->post('iDisplayStart');
        $data = json_decode($this->datatables->generate());

        $arrQuoteId = !empty($data->aaData) ? array_column($data->aaData, 0) : [];
        if (!empty($arrQuoteId)) {
            $this->db->select('
                tblproduction_report.id_quotes as id_quotes,
                tblproduction_report.id as id_production_report,
                tblproduction_report.reference_no as reference_no,
            ', false);
            $this->db->from('tblproduction_report');
            $this->db->where_in('tblproduction_report.id_quotes', $arrQuoteId, false);
            $list_production_report = $this->db->get()->result_array();
            if (!empty($list_production_report)) {
                $list_production_report = array_reduce($list_production_report, function ($carry, $item) {
                    $carry[$item['id_quotes']][] = $item;
                    return $carry;
                });
            }
        }

        foreach ($data->aaData as $key => $value) {
            $quote_id = $value[0];
            $data->aaData[$key][7] = tnh_html_entity_decode($value[7]);
            $production_report = $list_production_report[$quote_id] ?? null;
            $data->aaData[$key][11] = $production_report;
        }
        echo json_encode($data);
    }

    public function view_quotes($id)
    {
        if (!$this->perViewQuotes && !$this->perViewOwnQuotes) {
            accessDenied($js = true);
        }

        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                $list_branch = get_array_branch_staff();
                if (!empty($list_branch)) {
                    $this->db->group_start();
                    $this->db->where_in('tbl_quotes.id_branch', $list_branch);
                    $this->db->group_end();
                } else {
                    $this->db->where('tbl_quotes.id = 0', false, false);
                }
                $this->db->where('id', $id);
                $ktQuote = $this->db->get('tbl_quotes')->row();
                if (empty($ktQuote)) {
                    accessDenied($js = true);
                }
            }
        }

        $quote = $this->quotes_model->rowQuotesById($id);
        if (!$this->perViewQuotes) {
            checkMyData($quote['created_by'], true);
        }
        if ($quote['type_customer'] == "customers") {
            $customer = $this->clients_model->rowCustomer($quote['customer_id']);
            $person_contact = $this->site_model->rowContact($quote['person_contact_id']);
            $address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
        } else if ($quote['type_customer'] == "leads") {
            $customer = $this->clients_model->rowLead($quote['customer_id']);
            $person_contact = $this->site_model->rowContactLead($quote['person_contact_id']);
            $address_delivery = $this->site_model->rowShippingLead($quote['address_delivery_id']);
        } else {
            $customer = false;
            $person_contact = false;
            $address_delivery = false;
        }

        $quote_payments = $this->quotes_model->getQuotePayments($id);
        $quote_charges = $this->quotes_model->getQuoteChargesByQuoteId($id);
        $items = $this->quotes_model->getQuoteItemsByQuoteId($id);
        if ($quote['parent_id'] > 0) {
            $pre_quote = $this->quotes_model->rowQuotesById($quote['parent_id'])['reference_no'];
            $data['pre_quote'] = $pre_quote;
        }

        $li = '';
        $tab_content = '';
        $body_items = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                $images = '';
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                    if (!empty($info['images'])) {
                        $images = base_url('uploads/products/' . $info['images']);
                    }
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                    if (!empty($info['avatar'])) {
                        $images = base_url($info['avatar']);
                    }
                }
                if (empty($images)) {
                    $images = base_url('assets/images/tnh/no_image.png');
                }

                $tdNumber = '<td class="text-center">' . (++$key) . '</td>';
                $tdImages = '<td>
                    <div class="td-image"><div class="preview_image" style="width: auto;"><div class="display-block contract-attachment-wrapper img"><div style="width:45px;"><a href="' . $images . '" data-lightbox="customer-profile" class="display-block mbot5"><div class=""><img src="' . $images . '" style="border-radius: 50%"></div></a></div></div></div></div>
                </td>';

                $tdCode = '<td>' . $info['name'] . '(' . $info['code'] . ')<div class="type-item">' . (($type_item == "products") ? '<span class="label label-success">' . lang($type_item) . '</span>' : '<span class="label label-primary">' . lang($type_item) . '</span>') . '</div></td>';

                $tdName = '<td>' . $info['name'] . '</td>';

                $stage_quote = '';
                if (!empty($value['quote_stage_id'])) {
                    $this->db->where('id', $value['quote_stage_id']);
                    $stage_quote = $this->db->get('tbl_stage_quote')->row();
                }

                $tdStage_quote = '<td>' . (!empty($stage_quote) ? ($stage_quote->name . '(' . $stage_quote->code . ')') : '') . '</td>';

                $tdNum = '<td>' . $value['num'] . '</td>';
                $tdOrigin = '<td>' . $value['origin'] . '</td>';
                $tdUnit = '<td>' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center">' . formatNumber($value['quantity']) . '</td>';

                $viewListPrice = '<a data-tnh="modal" class="tnh-modal2 btn btn-primary" href="' . base_url('admin/handling_price/view_price/' . $value['id'] . '/quotes') . '" data-toggle="modal" data-target="#myModal2"><i class="fa fa-file-text-o"></i> Xem chi tiết giá</a>';

                $tdUnitPrice = '<td class="text-right">
                    ' . formatMoney($value['unit_price']) . '
                    <div>' . $viewListPrice . '</div>
                </td>';
                $tdTotalAmount = '<td class="text-right">' . formatMoney($value['total_amount']) . '</td>';

                $tdMOQ = '<td class="td-center">' . formatNumber($value['moq']) . ' - ' . formatNumber($value['moq_to']) . '</td>';

                $tdTechnicalExplanation = '<td class="td-technical-explanation">
                    ' . $value['technical_explanation'] . '
                </td>';
                $tdDiscountPrecent = '<td class="text-center">' . $value['discount_precent_item'] . '</div>';
                $tdLeadtime = '<td class="text-center">' . $value['lead_time'] . '</td>';

                $tdNote = '<td>' . $value['note_item'] . '</td>';

                $body_items .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdImages . '
                    ' . $tdCode . '
                    ' . $tdStage_quote . '
                    ' . $tdTechnicalExplanation . '
                    ' . $tdUnit . '
                    ' . $tdMOQ . '
                    ' . $tdUnitPrice . '
                    ' . $tdDiscountPrecent . '
                    ' . $tdLeadtime . '
                    ' . $tdNote . '
                </tr>';

                $li .= '<li role="presentation" class="' . ($key == 1 ? 'active' : '') . '">
                    <a href="#' . $value['id'] . '" aria-controls="' . $value['id'] . '" role="tab" data-toggle="tab">' . $info['code'] . '</a>
                </li>';

                $tab_content .= '<div role="tabpanel" class="tab-pane ' . ($key == 1 ? 'active' : '') . '" id="' . $value['id'] . '">' . tnh_html_entity_decode($value['info']) . '</div>';
            }
        }

        $data['created_by'] = get_staff_full_name($quote['created_by']);
        if (!empty($quote['updated_by'])) {
            $data['updated_by'] = get_staff_full_name($quote['updated_by']);
        }
        if (!empty($quote['user_status'])) {
            $data['user_status'] = get_staff_full_name($quote['user_status']);
        } else {
            $data['user_status'] = '';
        }

        if (!empty($quote['note_default_id'])) {
            $data['quote_note_default'] = $this->quotes_model->getQuotesNoteDefaultText(explode(',', $quote['note_default_id']))['note_default'];
        }

        $data['currencies'] = $this->site_model->getCurrenciesById($quote['currencies']);
        $data['id'] = $id;
        $data['address_delivery'] = $address_delivery;
        $data['quote'] = $quote;
        $data['customer'] = $customer;
        $data['quote_payments'] = $quote_payments;
        $data['items'] = $items;
        $data['quote_charges'] = $quote_charges;
        $data['body_items'] = $body_items;
        $data['li'] = $li;
        $data['tab_content'] = $tab_content;
        $data['person_contact'] = $person_contact;
        $this->load->view('view_quotes', $data);
    }

    public function convert_order($id, $ptm = 0)
    {
        if (!$this->perAddQuotes) {
            accessDenied($js = true);
        }

        $quote = $this->quotes_model->rowQuotesById($id);
        $items = $this->quotes_model->getQuoteItemsByQuoteId($id);

        if ($quote['type_customer'] == "customers") {
            $customer = $this->clients_model->rowCustomer($quote['customer_id']);
        } else if ($quote['type_customer'] == "leads") {
            $customer = $this->site_model->rowClientsByLeadId($quote['customer_id']);
            if (!empty($customer)) {
                //converted from lead to customer
                $quote['type_customer'] = "customers";
            } else {
                $customer = $this->clients_model->rowLead($quote['customer_id']);
            }
        } else {
            $customer = false;
        }

        if ($this->input->post('save')) {
            $data = [];

            if ($quote['status'] != 'approved') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_approved');
                echo json_encode($data);
                die;
            }
            if ($quote['order_id'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_created_an_order');
                echo json_encode($data);
                die;
            }

            $this->form_validation->set_rules('date', lang("date"), 'required');
            // $this->form_validation->set_rules('address_delivery', lang("address_delivery"), 'required');
            $this->form_validation->set_rules('staff_admin', lang("staff_admin"), 'required');
            $this->form_validation->set_rules('currencies', lang("currencies"), 'required');
            $this->form_validation->set_rules('amount_to_vnd', lang("amount_to_vnd"), 'required');
            $this->form_validation->set_rules('type_orders', lang("type_orders"), 'required');
            if ($this->form_validation->run() == true) {
                $date = to_sql_date($this->input->post('date'), true);
                $type_customer = $quote['type_customer'];
                $customer_id = ($type_customer == "customers") ? $customer['userid'] : $customer['id'];
                $address_delivery = $this->input->post('address_delivery');
                $employees = $this->input->post('staff_admin');
                $note = $this->input->post('note');
                $type_orders = $this->input->post('type_orders');
                if ($type_orders != TYPE_SAMPLE_ORDER) {
                    $data['result'] = 0;
                    $data['message'] = 'Vui lòng chọn đơn mẫu';
                    echo json_encode($data);
                    die;
                }
                $status_orders = $this->input->post('status_orders');
                $type_items = $this->input->post('type_items');
                $id_branch = $quote['id_branch'];

                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $tax_id = $this->input->post('tax_id') ? $this->input->post('tax_id') : 0;
                $tax_name = 0;
                $tax_rate = 0;
                $total_tax = 0;
                $discount_percent = $this->input->post('discount_percent') ? $this->input->post('discount_percent') : 0;
                $total_discount_percent = 0;
                $total_discount_direct = $this->input->post('discount_direct') ? number_unformat($this->input->post('discount_direct')) : 0;
                $discount_percent = 0;
                $total_discount_direct = 0;
                $grand_total = 0;
                $status = 'un_approved';
                $currencies = $this->input->post('currencies');
                $amount_to_vnd = number_unformat($this->input->post('amount_to_vnd'));

                $quote_item_id = $this->input->post('quote_item_id');
                $itemsChild = $this->input->post('itemsChild');
                $itemsChildSize = $this->input->post('itemsChildSize');
                $itemsChildColumns = $this->input->post('itemsChildColumns');
                $counter_item = 0;
                $grand_total_quantity = 0;

                foreach ($quote_item_id as $key => $value) {
                    $quote_item = $this->quotes_model->rowQuoteItemsById($value);
                    if (empty($quote_item)) continue;
                    $type_item = $quote_item['type_item'];
                    $item_id = $quote_item['item_id'];
                    $items_name = $quote_item['item_name'];
                    $items_code = $quote_item['item_code'];
                    $quantity_child_sheet = 0;
                    $quantity_sheet_bale = 0;
                    $loss = 0;

                    $info = false;
                    $unit_id = 0;
                    $conversion_quantity_unit = 1;
                    $conversion_quantity_unit_default = 1;
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($item_id);
                        $quantity_child_sheet = $info['quantity_child_sheet'];
                        $quantity_sheet_bale = $info['quantity_sheet_bale'];
                        $loss = $info['loss'];

                        $unit_id = $info['unit_id'];
                        $conversion_quantity_unit_default = $info['conversion_quantity_unit'];
                    }

                    if (empty($unit_id)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Mặt hàng chưa có đơn vị tính');
                        echo json_encode($data);
                        die;
                    }

                    $counter = $this->input->post('counter')[$key];
                    $value = $counter;

                    $ct_counter_item = 0;
                    $arrItemsChildColumns = [];
                    $counter_items_number = 0;
                    $quantity = 0;
                    $total_quantity_loss = 0;
                    $total_quantity_sample = 0;
                    if ($type_orders == ORDER_CHANGE || $type_orders == ORDER_DEFAULT || $type_orders) {
                        if (!empty($itemsChildColumns[$value])) {
                            foreach ($itemsChildColumns[$value] as $kICC => $vICC) {
                                $columns_id = !empty($vICC['columns_id']) ? $vICC['columns_id'] : NULL;
                                $columns_value = !empty($vICC['columns_value']) ? $vICC['columns_value'] : NULL;
                                $columns_name = !empty($vICC['columns_name']) ? $vICC['columns_name'] : NULL;
                                if (!empty($columns_id)) {
                                    foreach ($columns_id as $kC => $vC) {
                                        $columns_name_item = !empty($columns_name[$kC]) ? $columns_name[$kC] : '';
                                        $columns_value_item = !empty($columns_value[$kC]) ? $columns_value[$kC] : '';
                                        if (empty($columns_name_item)) continue;
                                        $arrItemsChildColumns[] = [
                                            'counter_item' => $counter_item,
                                            'columns_id' => $vC,
                                            'columns_name' => $columns_name_item,
                                            'columns_value' => $columns_value_item,
                                            'counter_items_number' => $counter_items_number,
                                        ];
                                    }
                                }

                                //default
                                $columns_value_order_code = $vICC['columns_value_order_code'];
                                $order_code = $vICC['order_code'];
                                $columns_value_command = $vICC['columns_value_command'];
                                $command = $vICC['command'];

                                $columns_value_quantity_put = $vICC['columns_value_quantity_put'];
                                $quantity_put = number_unformat($vICC['quantity_put']);

                                $columns_value_quantity_loss = $vICC['columns_value_quantity_loss'];
                                $quantity_loss = number_unformat($vICC['quantity_loss']);

                                $columns_value_sample_quantity_item = $vICC['columns_value_sample_quantity_item'];
                                $sample_quantity_item = number_unformat($vICC['sample_quantity_item']);

                                if (empty($order_code)) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Vui lòng nhập mã đơn đặt');
                                    echo json_encode($data);
                                    die;
                                }

                                if (empty($command)) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Vui lòng nhập chỉ lệnh');
                                    echo json_encode($data);
                                    die;
                                }

                                if (empty($quantity_put)) {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Vui lòng nhập số lượng đặt > 0');
                                    echo json_encode($data);
                                    die;
                                }

                                if ($quantity_loss === '') {
                                    $data['result'] = 0;
                                    $data['message'] = lang('Vui lòng nhập số lượng loss');
                                    echo json_encode($data);
                                    die;
                                }

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $order_code,
                                    'columns_value' => $columns_value_order_code,
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $command,
                                    'columns_value' => $columns_value_command,
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $quantity_put,
                                    'columns_value' => $columns_value_quantity_put,
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $quantity_loss,
                                    'columns_value' => $columns_value_quantity_loss,
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $arrItemsChildColumns[] = [
                                    'counter_item' => $counter_item,
                                    'columns_id' => 0,
                                    'columns_name' => $sample_quantity_item,
                                    'columns_value' => $columns_value_sample_quantity_item,
                                    'counter_items_number' => $counter_items_number,
                                ];

                                $quantity += $quantity_put;
                                $total_quantity_loss += $quantity_loss;
                                $total_quantity_sample += $sample_quantity_item;

                                $counter_items_number++;
                                $counter_item++;
                            }
                        }
                    }

                    $ct_counter_item = $counter_items_number;

                    // $quantity = $quote_item['quantity'];
                    // $quantity = number_unformat($this->input->post('quantity')[$key]);
                    // if ($quantity <= 0) {
                    //     $data['result'] = 0;
                    //     $data['message'] = lang('Vui lòng nhập số lượng > 0');
                    //     echo json_encode($data); die;
                    // }

                    // $order_code = $this->input->post('order_code')[$key];
                    // $command = $this->input->post('command')[$key];
                    // $quantity_loss = number_unformat($this->input->post('quantity_loss')[$key]);


                    // $sample_quantity =  number_unformat($this->input->post('sample_quantity')[$key]);
                    $sample_quantity =  $total_quantity_sample;

                    $price = $quote_item['unit_price'];
                    // $amount = $quote_item['total_amount'];
                    $amount = $quantity * $price;
                    $total_quantity_item = $sample_quantity + $quantity + $total_quantity_loss;
                    $grand_total_quantity += $total_quantity_item;

                    if (empty($order_code)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng nhập mã đơn đặt');
                        echo json_encode($data);
                        die;
                    }

                    if (empty($command)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng nhập chỉ lệnh');
                        echo json_encode($data);
                        die;
                    }

                    if (empty($quantity)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng nhập số lượng đặt');
                        echo json_encode($data);
                        die;
                    }

                    if ($quantity_loss === '') {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng nhập số lượng loss');
                        echo json_encode($data);
                        die;
                    }

                    // $total_quantity_item = $sample_quantity + $quantity;
                    // $grand_total_quantity+= $total_quantity_item;


                    $note_item = $this->input->post('note_items')[$key];
                    $sub = [];
                    $date_sub = $this->input->post('date_sub')[$key];
                    $total_quantity_sub = 0;
                    if (!empty($date_sub)) {
                        foreach ($date_sub as $k => $val) {
                            if (empty($val)) continue;
                            // $quantity_sub = number_unformat($this->input->post('quantity_sub')[$key][$k]);
                            $quantity_sub = $total_quantity_item;
                            $sub[] = [
                                'date_shipping' => to_sql_date($val),
                                'quantity_shipping' => $quantity_sub
                            ];
                            $total_quantity_sub += $quantity_sub;
                        }

                        if ($total_quantity_sub > $total_quantity_item) {
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

                    $grand_total_item = $amount;
                    //tax item
                    // $tax_id_item = $this->input->post('tax_item')[$key] ? $this->input->post('tax_item')[$key] : 0;
                    $tax_id_item = 0;
                    $tax_name_item = "0%";
                    $tax_rate_item = 0;
                    $tax_amount_item = 0;
                    if (!empty($tax_id_item)) {
                        $info_tax = $this->site_model->rowTax($tax_id_item);
                        if (!empty($info_tax)) {
                            $tax_name_item = $info_tax['name'];
                            $tax_rate_item = $info_tax['taxrate'];
                        }
                    }

                    if ($tax_rate_item > 0) {
                        $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                        $total_tax_items += $tax_amount_item;
                        $grand_total_item += $tax_amount_item;
                    }

                    //end
                    //discount percent item
                    $discount_percent_item = number_unformat($this->input->post('discount_percent_item')[$key]);
                    $discount_percent_amount_item = 0;
                    if ($discount_percent_item > 0) {
                        $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                        $total_discount_percent_items += $discount_percent_amount_item;
                        $grand_total_item -= $discount_percent_amount_item;
                    }
                    //end
                    $discount_direct_amount_item = number_unformat($this->input->post('discount_direct_item')[$key]);

                    $total_discount_direct_items += $discount_direct_amount_item;
                    $grand_total_item -= $discount_direct_amount_item;

                    //exchange
                    $exchange = [];
                    if ($type_item == "products") {
                        $exchangeUnits = $this->products_model->getExchangeProductsByProductId($item_id);
                        if (!empty($exchangeUnits)) {
                            foreach ($exchangeUnits as $k => $val) {
                                if (empty($val)) continue;
                                $quantity_exchange = $val['number_exchange'];
                                $total_quantity_exchange = $quantity / $quantity_exchange;
                                $exchange[] = [
                                    'unit_id' => $val['unit_id'],
                                    'quantity_exchange' => $quantity_exchange,
                                    'total_quantity_exchange' => $total_quantity_exchange,
                                ];
                            }
                        }
                    }
                    //end exchange

                    $arrChange = [];
                    if ($type_orders == ORDER_CHANGE) {
                        if (!empty($itemsChild[$key])) {
                            foreach ($itemsChild[$key] as $kC => $vC) {
                                if (empty($vC['size'])) continue;
                                $arrChange[] = [
                                    'size ' => $vC['size'],
                                    'size_dc' => $vC['size_dc'],
                                    'style_number' => $vC['style_number'],
                                    'color' => $vC['color'],
                                    'quantity' => number_unformat($vC['quantity']),
                                ];
                            }
                        }
                    }

                    $arrChangeSize = [];
                    if ($type_orders == ORDER_CHANGE_SIZE) {
                        if (!empty($itemsChildSize[$key])) {
                            foreach ($itemsChildSize[$key] as $kC => $vC) {
                                if (empty($vC['number_size'])) continue;

                                $quantity_child = number_unformat($vC['quantity']);
                                $even_quantity = 0;
                                $odd_quantity = 0;
                                $quantity_sheet = 0;
                                if ($quantity_child_sheet > 0) {
                                    $quantity_sheet = $quantity_child / $quantity_child_sheet;
                                    $even_quantity = floor($quantity_sheet);
                                    $quantity_ceil = ceil($quantity_sheet);
                                    $odd_quantity = $quantity_ceil - $even_quantity;
                                }

                                $even_quantity_bale = 0;
                                $odd_quantity_bale = 0;
                                $quantity_bale = 0;
                                if ($quantity_sheet_bale > 0) {
                                    $quantity_bale = $quantity_child / $quantity_sheet_bale;
                                    $even_quantity_bale = floor($quantity_bale);
                                    $quantity_ceil_bale = ceil($quantity_bale);
                                    $odd_quantity_bale = $quantity_ceil_bale - $even_quantity_bale;
                                }

                                $arrChangeSize[] = [
                                    'number_size' => $vC['number_size'],
                                    'quantity' => $quantity_child,
                                    'even_sheet' => $even_quantity,
                                    'odd_sheet' => $odd_quantity,
                                    'even_bale' => $even_quantity_bale,
                                    'odd_bale' => $odd_quantity_bale,
                                    'quantity_sheet' => $quantity_sheet,
                                    'quantity_bale' => $quantity_bale,
                                ];
                            }
                        }
                    }

                    $product_name_customer = !empty($this->input->post('product_name_customer')[$key]) ? $this->input->post('product_name_customer')[$key] : '';
                    if (empty($product_name_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('Vui lòng nhập tên thành phẩm của khách hàng');
                        echo json_encode($data);
                        die;
                    }

                    $itemsOrders[] = [
                        // 'order_code' => $order_code,
                        // 'command' => $command,
                        'quantity_loss' => $total_quantity_loss,
                        'sample_quantity' => $sample_quantity,
                        'total_quantity_item' => $total_quantity_item,

                        'type_item' => $type_item,
                        'item_id' => $item_id,
                        'item_code' => $items_code,
                        'item_name' => $items_name,
                        'quantity' => $quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'tax_id_item' => $tax_id_item,
                        'tax_name_item' => $tax_name_item,
                        'tax_rate_item' => $tax_rate_item,
                        'tax_amount_item' => $tax_amount_item,
                        'discount_percent_item' => $discount_percent_item,
                        'discount_percent_amount_item' => $discount_percent_amount_item,
                        'discount_direct_amount_item' => $discount_direct_amount_item,
                        'total_amount' => $grand_total_item,
                        'note_item' => $note_item,
                        'sub' => $sub,
                        'exchange' => $exchange,
                        'arrChange' => $arrChange,
                        'arrChangeSize' => $arrChangeSize,
                        'quantity_child_sheet' => $quantity_child_sheet,
                        'quantity_sheet_bale' => $quantity_sheet_bale,
                        'arrItemsChildColumns' => $arrItemsChildColumns,
                        'ct_counter_item' => $ct_counter_item,
                        'loss' => $loss,
                        'product_name_customer' => $product_name_customer,
                        'unit_id' => $unit_id,
                        'conversion_quantity_unit' => $conversion_quantity_unit,
                        'conversion_quantity_unit_default' => $conversion_quantity_unit_default
                    ];

                    $total_quantity += $quantity;
                    $total_amount_items += $amount;
                    $grand_total_items += $grand_total_item;
                }

                if (empty($itemsOrders)) {
                    $data['result'] = 0;
                    $data['message'] = lang('tnh_not_items');
                    echo json_encode($data);
                    die;
                }

                // print_arrays($itemsOrders);

                $count_items = count($itemsOrders);
                $grand_total = $grand_total_items;
                // print_arrays($grand_total);
                if (!empty($tax_id)) {
                    $info_tax = $this->site_model->rowTax($tax_id);
                    if (!empty($info_tax)) {
                        $tax_name = $info_tax['name'];
                        $tax_rate = $info_tax['taxrate'];
                    }
                }

                if ($discount_percent > 0) {
                    $total_discount_percent = $grand_total * ($discount_percent / 100);
                }
                $grand_total -= $total_discount_percent;
                $grand_total -= $total_discount_direct;

                if ($tax_rate > 0) {
                    // $total_tax = $grand_total_items * ($tax_rate/100);
                    $total_tax = $grand_total * ($tax_rate / 100);
                }
                $grand_total += $total_tax;

                //handing customer
                if ($type_customer == "customers") {
                    $row_customer = $this->site_model->rowCustomer($customer_id);
                    if (empty($row_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_customer_not_exist');
                        echo json_encode($data);
                        die;
                    }
                    $customer_name = $row_customer['company_short'];
                    $address_delivery_id = $address_delivery;
                } else if ($type_customer == "leads") {
                    $lead_id = $customer_id;
                    $convert_customer = convertCustomerLeadToCustomer($customer_id, $address_delivery);
                    if (empty($convert_customer)) {
                        $data['result'] = 0;
                        $data['message'] = lang('tnh_customer_not_exist');
                        echo json_encode($data);
                        die;
                    }
                    $customer_id = $convert_customer['customer_id'];
                    $customer_name = $convert_customer['customer_name'];
                    $address_delivery_id = $convert_customer['address_delivery_id'];
                }
                //end
                $staff_general = get_staff_user_id();
                $date_general = date('Y-m-d H:i:s');

                $options = [
                    'date' => $date,
                    'reference_no' => getReference('orders'),
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery_id,
                    'employee_id' => $employees,
                    'note' => $note,
                    'count_items' => $count_items,
                    'total_quantity' => $total_quantity,
                    'total_amount_items' => $total_amount_items,
                    'total_tax_items' => $total_tax_items,
                    'total_discount_percent_items' => $total_discount_percent_items,
                    'total_discount_direct_items' => $total_discount_direct_items,
                    'grand_total_items' => $grand_total_items,
                    'tax_id' => $tax_id,
                    'tax_name' => $tax_name,
                    'tax_rate' => $tax_rate,
                    'total_tax' => $total_tax,
                    'discount_percent' => $discount_percent,
                    'total_discount_percent' => $total_discount_percent,
                    'total_discount_direct' => $total_discount_direct,
                    'grand_total' => $grand_total,
                    'status' => $status,
                    'date_created' => $date_general,
                    'created_by' => $staff_general,
                    'quotes_id' => $id,
                    'currencies' => $currencies,
                    'amount_to_vnd' => $amount_to_vnd,
                    'type_orders' => $type_orders,
                    'status_orders' => $status_orders,
                    'type_items' => $type_items,
                    'grand_total_quantity' => $grand_total_quantity,
                    'id_branch' => $id_branch,
                    'ptm' => $ptm,
                ];

                if (!empty($ptm)) {
                    $options['status'] = 'approved';
                    $options['user_status'] = $staff_general;
                    $options['date_status'] = $date_general;
                }

                $order_id = $this->orders_model->insertOrdersNew($options);
                if ($order_id) {
                    updateReference('orders');
                    $arrObjecOrderstId = [];
                    $arrProductId = [];
                    foreach ($itemsOrders as $key => $value) {
                        $value['order_id'] = $order_id;
                        $sub = $value['sub'];
                        $exchange = $value['exchange'];
                        $arrChange = $value['arrChange'];
                        $arrChangeSize = $value['arrChangeSize'];
                        $arrItemsChildColumns = $value['arrItemsChildColumns'];
                        unset($value['sub']);
                        unset($value['exchange']);
                        unset($value['arrChange']);
                        unset($value['arrChangeSize']);
                        unset($value['arrItemsChildColumns']);
                        $order_item_id = $this->orders_model->insertOrderItemsNew($value);
                        if ($order_item_id) {
                            if (!empty($sub)) {
                                foreach ($sub as $k => $val) {
                                    $val['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemShippingsNew($val);
                                }
                            }

                            if (!empty($exchange)) {
                                foreach ($exchange as $k => $val) {
                                    $val['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemExchange($val);
                                }
                            }

                            if (!empty($arrChange)) {
                                foreach ($arrChange as $kC => $vC) {
                                    $arrChange[$kC]['order_id'] = $order_id;
                                    $arrChange[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsSize($arrChange);
                            }

                            if (!empty($arrChangeSize)) {
                                foreach ($arrChangeSize as $kC => $vC) {
                                    $arrChangeSize[$kC]['order_id'] = $order_id;
                                    $arrChangeSize[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsChangeSize($arrChangeSize);
                            }

                            if (!empty($arrItemsChildColumns)) {
                                foreach ($arrItemsChildColumns as $kC => $vC) {
                                    $arrItemsChildColumns[$kC]['order_id'] = $id;
                                    $arrItemsChildColumns[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsColumns($arrItemsChildColumns);
                            }

                            $arrObjecOrderstId[] = $order_id;
                            $arrProductId[] = $value['item_id'];
                        }
                    }
                    $this->quotes_model->updateQuotes($id, ['order_id' => $order_id]);

                    //Nếu thêm đơn hàng thành công => chuyễn địa chĩ giao hàng
                    if (!empty($customer_id) && $type_customer == 'leads') {
                        ChangeObjectAssigned('lead', $lead_id, 'client', $customer_id);
                        createCodeNameSystem('client', $customer_id);
                    }

                    noti_custom('create_orders', $id, get_staff_user_id(), 0, '', ['actions' => 'add']);
                    insertActivityLog([
                        'type_parent_obj' => 'quotes',
                        'table_obj' => 'tbl_quotes',
                        'id_obj' => $id,
                        'name_obj' => $quote['reference_no'],
                        'content' => lang('tnh_his_convert_order_quotes') . ' [' . $quote['reference_no'] . ']',
                        'actions' => 'convert_order'
                    ]);

                    $data['result'] = 1;
                    $data['arrObjecOrderstId'] = !empty($arrObjecOrderstId) ? implode(',', array_unique($arrObjecOrderstId)) : '';
                    $data['arrProductId'] = !empty($arrProductId) ? implode(',', array_unique($arrProductId)) : '';
                    $data['id_branch'] = $id_branch;
                    $data['message'] = lang('success');
                } else {
                    //Nếu thêm đơn hàng không thành công => xóa khách hàng
                    if (!empty($customer_id)  && $type_customer == 'leads') {
                        $this->db->where('userid', $customer_id);
                        $this->db->delete('tblclients');
                    }

                    $data['result'] = 0;
                    $data['message'] = lang('errors');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }
        $data['size'] = $this->site_model->getSize();
        $data['colors'] = $this->site_model->getColors();
        $data['status_orders'] = $this->status_orders_model->getStatusOrders();

        $data['type_items'] = $this->db->get('tbltype_orders_items')->result_array();
        $data['type_orders'] = $this->type_orders_model->getTypeOrders();
        $data['currencies'] = $this->site_model->getCurrencies();
        $data['taxs'] = $this->site_model->getTaxs();
        $data['staff'] = get_table_where(db_prefix() . 'staff', ['active' => 1]);
        $data['items'] = $items;
        $data['quote'] = $quote;
        $data['customer'] = $customer;
        $data['customer_id'] = $quote['type_customer'] . '__' . $quote['customer_id'];
        $data['id'] = $id;
        $data['ptm'] = $ptm;
        $this->load->view('convert_order', $data);
    }

    public function print_quotes($id)
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }

        if (!empty($this->is_branch)) {
            if (!is_admin()) {
                $list_branch = get_array_branch_staff();
                if (!empty($list_branch)) {
                    $this->db->group_start();
                    $this->db->where_in('tbl_quotes.id_branch', $list_branch);
                    $this->db->group_end();
                } else {
                    $this->db->where('tbl_quotes.id = 0', false, false);
                }
                $this->db->where('id', $id);
                $ktQuote = $this->db->get('tbl_quotes')->row();
                if (empty($ktQuote)) {
                    accessDenied();
                }
            }
        }


        include APPPATH . 'third_party/NumbersWords/Numbers/Words.php';
        $words = new Numbers_Words();

        $quote = $this->quotes_model->rowQuotesById($id);
        $customer = false;
        $lead = false;
        if ($quote['type_customer'] == 'customers') {
            $customer = $this->clients_model->rowCustomer($quote['customer_id']);
        } else if ($quote['type_customer'] == 'leads') {
            $lead = $this->clients_model->rowLead($quote['customer_id']);
        }
        $quote_payments = $this->quotes_model->getQuotePayments($id);
        $quote_charges = $this->quotes_model->getQuoteChargesByQuoteId($id);
        $items = $this->quotes_model->getQuoteItemsByQuoteId($id);
        if ($quote['parent_id'] > 0) {
            $pre_quote = $this->quotes_model->rowQuotesById($quote['parent_id'])['reference_no'];
            $data['pre_quote'] = $pre_quote;
        }

        $body_items = '';
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $rowSpan = 1;
                $tr_rowspan = '';
                if (!empty($info['code'])) {
                    $rowSpan = 2;
                    $tr_rowspan = '<tr>
                        <td>' . $info['code'] . '</td>
                    </tr>';
                }

                $tdNumber = '<td rowspan=' . $rowSpan . ' class="text-center">' . (++$key) . '</td>';
                $tdCode = '<td rowspan="1">' . $info['code'] . '</td>';
                $tdName = '<td rowspan="1">' . $info['name'] . '</td>';
                $tdOrigin = '<td class="text-center" rowspan=' . $rowSpan . '>' . $value['origin'] . '</td>';
                $tdUnit = '<td class="text-center" rowspan=' . $rowSpan . '>' . $unit['unit'] . '</td>';
                $tdQuantity = '<td rowspan=' . $rowSpan . ' class="text-center">' . formatNumber($value['quantity']) . '</td>';
                $tdUnitPrice = '<td rowspan=' . $rowSpan . ' class="text-right">' . formatMoney($value['unit_price']) . '</td>';
                $tdTotalAmount = '<td rowspan=' . $rowSpan . ' class="text-right">' . formatMoney($value['total_amount']) . '</td>';
                $tdLeadtime = '<td rowspan=' . $rowSpan . ' class="text-center">' . $value['lead_time'] . '</td>';
                $tdNote = '<td rowspan=' . $rowSpan . '>' . $value['note_item'] . '</td>';

                $body_items .= '<tr>
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdOrigin . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdUnitPrice . '
                    ' . $tdTotalAmount . '
                    ' . $tdLeadtime . '
                </tr>';
                $body_items .= $tr_rowspan;
            }
        }

        $money_words = $words->toCurrency($quote['grand_total'], "en_US");
        $data['quote'] = $quote;
        $data['customer'] = $customer;
        $data['lead'] = $lead;
        $data['quote_payments'] = $quote_payments;
        $data['quote_charges'] = $quote_charges;
        $data['items'] = $items;
        $data['body_items'] = $body_items;
        $data['money_words'] = $money_words;
        $data['title']  = lang('print');
        $this->load->view('print_quotes', $data);
    }

    function delete_quotes($id)
    {
        $data = [];
        if (!$this->perDeleteQuotes) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($id) {
            $quote = $this->quotes_model->rowQuotesById($id);
            if (!checkMyDataTF($quote['created_by'])) {
                $data['result'] = 0;
                $data['message'] = lang('access_denied');
                echo json_encode($data);
                die;
            }
            if ($quote['status'] == "un_approved") {
                if ($this->quotes_model->deleteQuotesById($id)) {
                    $this->quotes_model->deleteQuoteItemsByQuoteId($id);
                    $this->quotes_model->deleteQuotePaymentsQuoteId($id);
                    $this->quotes_model->deleteQuoteChargesByQuoteId($id);

                    noti_custom('create_quotes', $id, get_staff_user_id(), 0, '', ['actions' => 'delete']);

                    insertActivityLog([
                        'type_parent_obj' => 'quotes',
                        'table_obj' => 'tbl_quotes',
                        'id_obj' => $id,
                        'name_obj' => $quote['reference_no'],
                        'content' => lang('tnh_his_delete_quotes') . ' [' . $quote['reference_no'] . ']',
                        'actions' => 'delete'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = lang('browsed_cannot_be_deleted');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    function searchPreReferenceNoQuotes($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $data['results'] = $this->quotes_model->searchPreReferenceNoQuotes($term, $limit);
        if ($id) {
            $quote = $this->quotes_model->rowQuotesById($id);
            $data['row'] = ['id' => $quote['id'], 'text' => $quote['reference_no']];
            // $product = $this->products_model->getProductId($id);
            // $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }

    function getReferenceByPreRef($id)
    {
        $quote = $this->quotes_model->rowQuotesById($id);
        $max = $this->quotes_model->countQuotesByParentId($id) + 1;
        $reference_no = $quote['reference_no'] . '.' . $max;
        return $reference_no;
    }

    function getReferenceQuotes()
    {
        $reference_no = getReference('quotes');
        if ($this->quotes_model->checkExistQuotesReferenceNo($reference_no)) {
            // $this->db->select('MAX(tbl_quotes.reference_no) as reference_no', false);
            $this->db->select("MAX(right(tbl_quotes.reference_no, char_length(tbl_quotes.reference_no) - locate('-', tbl_quotes.reference_no) - 8) + 0) as reference_no", false);
            $this->db->from('tbl_quotes');
            $rs = $this->db->get()->row_array();
            $max = ($rs['reference_no'] * 1) + 1;
            // $max = $rs['reference_no'];
            // $max = subReference($max);
            updateReferenceNormal('quotes', $max);
            $reference_no = getReference('quotes');
        }
        return $reference_no;
    }

    function getPreQuotes()
    {
        $data = [];
        if ($this->input->get()) {
            $pre_quote_id = $this->input->get('pre_quote_id');
            $pre_quote = false;
            $quote_payments = false;
            $html_payments = false;
            $items = false;
            $quote_charges = false;
            if ($pre_quote_id) {
                $reference_no = $this->getReferenceByPreRef($pre_quote_id);
                $pre_quote = $this->quotes_model->rowQuotesById($pre_quote_id);
                $pre_quote['delivery'] = tnh_html_entity_decode($pre_quote['delivery']);
                $pre_quote['installation_cost'] = tnh_html_entity_decode($pre_quote['installation_cost']);
                $pre_quote['note'] = tnh_html_entity_decode($pre_quote['note']);
                $pre_quote['parts_origin'] = tnh_html_entity_decode($pre_quote['parts_origin']);
                $quote_payments = $this->quotes_model->getQuotePayments($pre_quote_id);
                $items = $this->quotes_model->getQuoteItemsByQuoteId($pre_quote_id);
                if (!empty($quote_payments)) {
                    foreach ($quote_payments as $key => $value) {
                        $html_payments .= '<tr>
                            <td class="stt-payment text-center">' . (++$key) . '</td>
                            <td><input type="number" name="payment[]" id="payment[]" class="form-control payment" style="width: 100%;" placeholder="" value="' . $value['number'] . '"></td>
                            <td><input type="text" name="name_payment[]" id="name_payment[]" class="form-control name_payment" style="width: 100%;" placeholder="" value="' . $value['name'] . '"></td>
                            <td class="text-center"><i class="fa fa-remove btn btn-danger remove-row-payment"></i></td>
                        </tr>';
                    }
                }

                foreach ($items as $key => $value) {
                    $type_item = $value['type_item'];
                    $items_id = $value['item_id'];
                    if ($type_item == "products") {
                        $info = $this->products_model->rowProduct($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit_id']);
                        if (!empty($info['images'])) {
                            $images = base_url('uploads/products/' . $info['images']);
                        }
                    } else if ($type_item == "items") {
                        $info = $this->items_model->rowItems($items_id);
                        $unit = $this->unit_model->rowUnit($info['unit']);
                        if (!empty($info['avatar'])) {
                            $images = base_url($info['avatar']);
                        }
                    }
                    if (empty($images)) {
                        $images = base_url('assets/images/tnh/no_image.png');
                    }

                    $items_id = $items_id . '__' . $type_item;
                    $items[$key]['items_id'] = $items_id;
                    $items[$key]['code'] = $info['code'];
                    $items[$key]['name'] = $info['name'];
                    $items[$key]['unit'] = $unit['unit'];
                    $items[$key]['images'] = $images;
                }

                $quote_charges = $this->quotes_model->getQuoteChargesByQuoteId($pre_quote_id);
            } else {
                $reference_no = $this->getReferenceQuotes();
            }

            $data['items'] = $items;
            $data['html_payments'] = $html_payments;
            $data['reference_no'] = $reference_no;
            $data['pre_quote'] = $pre_quote;
            $data['quote_charges'] = $quote_charges;
        }
        echo json_encode($data);
    }

    function refereshReferenceQuotes()
    {
        $data = [];
        if ($this->input->get('referesh')) {
            $pre_quote_id = $this->input->get('pre_quote_id');
            if ($pre_quote_id) {
                $reference_no = $this->getReferenceByPreRef($pre_quote_id);
            } else {
                $reference_no = $this->getReferenceQuotes();
            }
            $data['reference_no'] = $reference_no;
            $data['message'] = lang('tnh_referesh_success');
        }
        echo json_encode($data);
    }

    function agree()
    {
        $data = [];
        if (!$this->perApproveQuotes) {
            $data['result'] = 0;
            $data['message'] = lang('access_denied');
            echo json_encode($data);
            die;
        }
        if ($this->input->get()) {
            $quote_id = $this->input->get('quote_id');
            $status = $this->input->get('status');
            $quote = $this->quotes_model->rowQuotesById($quote_id);
            $date = date('Y-m-d H:i');
            $user_id = get_staff_user_id();
            if ($quote['status'] == $status) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_please_referesh_table');
                echo json_encode($data);
                die;
            }

            if ($quote['order_id'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_created_an_order');
                echo json_encode($data);
                die;
            }
            if ($quote['contract_id'] > 0) {
                $data['result'] = 0;
                $data['message'] = lang('ch_created_an_contracts');
                echo json_encode($data);
                die;
            }
            $up = $this->quotes_model->updateQuotes($quote_id, [
                'status' => $status,
                'date_status' => $date,
                'user_status' => $user_id
            ]);
            if ($up) {
                @pusherTNHNotfication();
                insertActivityLog([
                    'type_parent_obj' => 'quotes',
                    'table_obj' => 'tbl_quotes',
                    'id_obj' => $quote_id,
                    'name_obj' => $quote['reference_no'],
                    'content' => lang('tnh_his_agree_quotes') . ' [' . $quote['reference_no'] . ']',
                    'actions' => 'agree'
                ]);

                handlingPricesCustomerGroup($quote_id);
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        }
        echo json_encode($data);
    }
    public function convert_contract($id = '')
    {
        if (!$this->perAddQuotes) {
            accessDenied($js = true);
        }

        $data['dataMain'] = get_table_where('tbl_orders', array('id' => $id), '', 'row');
        $data['id'] = $id;
        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['staff'] = $this->db->get()->result_array();
        $data['clients'] = get_options_search_cbo('customer', $data['dataMain']->customer_id);
        $this->load->view('convert_contract', $data);
    }

    public function quotes_note_default()
    {
        $data['tnh'] = true;
        $data['title'] = _l('quotes_note_default');
        $this->load->view('quotes_note_default', $data);
    }

    public function add_quote_note_default()
    {
        $data = [];
        if ($this->input->post()) {
            $this->form_validation->set_rules('note', lang("note"), 'required');
            $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_quotes_note_default.code]');
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quotes_model->insertQuotesNoteDefault($options);
                if ($id) {
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
            return;
        } else {
            $this->load->view('add_quote_note_default', $data);
        }
    }

    public function edit_quote_note_default($id)
    {
        $data = [];
        $category = $this->quotes_model->rowQuotesNoteDefault($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules('note', lang("note"), 'required');
            if ($category['code'] != $this->input->post('code')) {
                $this->form_validation->set_rules('code', lang("code"), 'required|is_unique[tbl_quotes_note_default.code]');
            }
            if ($this->form_validation->run() == true) {
                $code = $this->input->post('code');
                $note = $this->input->post('note');

                $options = [
                    'code' => $code,
                    'note' => $note,
                ];

                $id = $this->quotes_model->updateQuotesNoteDefault($id, $options);
                if ($id) {
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
            return;
        } else {
            $data['id'] = $id;
            $data['category'] = $category;
            $this->load->view('edit_quote_note_default', $data);
        }
    }

    public function getQuotesNoteDefault()
    {
        $this->datatables->select("
            tbl_quotes_note_default.id as id,
            tbl_quotes_note_default.code as code,
            tbl_quotes_note_default.note as note,
            ", FALSE)
            ->from('tbl_quotes_note_default');


        $edit = '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/quotes/edit_quote_note_default/$1"><i class="fa fa-pencil"></i></a>';

        $delete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\'' . base_url('admin/quotes/delete_quote_note_default/$1') . '\' class=\'btn btn-danger po-delete-json\'>' . lang('delete') . '</button>
            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
        "><i class="fa fa-remove"></i></button>';

        $this->datatables->add_column('actions', '
            <div>
                ' . $edit . '
                ' . $delete . '
            </div>
        ', 'id');
        echo $this->datatables->generate();
    }

    public function delete_quote_note_default($id)
    {
        $data = [];
        if ($id) {
            if ($this->quotes_model->checkQuotesNoteDefault($id)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_exist_not_delete');
                echo json_encode($data);
                return;
            }

            if ($this->quotes_model->deleteQuotesNoteDefault($id)) {
                $data['result'] = 1;
                $data['message'] = lang('success');
            } else {
                $data['result'] = 0;
                $data['message'] = lang('fail');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function print_pdf_old($id, $type_pdf = 'I')
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $quote = $this->quotes_model->rowQuotesById($id);
        $address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
        $area = $this->site_model->rowDeliveryArea($address_delivery['city_shipping'], $address_delivery['district_shipping']);
        $codeCustomer = '';
        $companyCustomer = '';
        $dvCompany = '';
        $addressCompany = '';
        $phoneCompany = '';
        $zaloCompany = '';
        $emailCompany = '';

        $personContact = '';
        $phoneContact = '';
        if ($quote['type_customer'] == "customers") {
            $customer = $this->clients_model->rowCustomer($quote['customer_id']);
            $codeCustomer = $customer['zcode'];
            $companyCustomer = $customer['company_short'];
            $fullname = $customer['fullname'];
            $addressCompany = $customer['address'];
            $phoneCompany = $customer['phonenumber'];
            $zaloCompany = $this->site_model->getZaloClient($quote['customer_id'])['zalo'];
            $emailCompany = $customer['email_client'];

            $contact = $this->site_model->rowContact($quote['person_contact_id']);
            $personContact = $contact['firstname'];
            $phoneContact = $contact['phonenumber'];
        } else if ($quote['type_customer'] == "leads") {
            $customer = $this->clients_model->rowLead($quote['customer_id']);
            $codeCustomer = $customer['zcode'];
            $companyCustomer = $customer['company'];
            $addressCompany = $customer['address'];
            $fullname = $customer['fullname'];
            $phoneCompany = $customer['phonenumber'];
            $zaloCompany = '';
            $emailCompany = $customer['email'];

            $contact = $this->site_model->rowContactLead($quote['person_contact_id']);
            $personContact = $contact['firstname'];
            $phoneContact = $contact['phonenumber'];
        }

        $quote_note_default = '';
        if (!empty($quote['note_default_id'])) {
            $quote_note_default = $this->quotes_model->getQuotesNoteDefaultText(explode(',', $quote['note_default_id']))['note_default'];
        }
        $created_by = get_staff_full_name($quote['created_by']);

        // $items = $this->orders_model->getOrderIYtemsByOrderId($id);
        $data['title'] = lang('tnh_quotes');
        $data['type'] = 'P';
        $data['img'] = '';

        $bodyItems = '';
        $items = $this->quotes_model->getQuoteItemsByQuoteId($id);
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];
                if ($type_item == "products") {
                    $info = $this->products_model->rowProduct($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit_id']);
                } else if ($type_item == "items") {
                    $info = $this->items_model->rowItems($items_id);
                    $unit = $this->unit_model->rowUnit($info['unit']);
                }

                $tdNumber = '<td class="text-center" style="width: 6%;">' . (++$key) . '</td>';
                $tdName = '<td style="width: 35%;">' . $info['name'] . ' (' . $info['code'] . ')</td>';
                $tdNote = '<td style="width: 15%;">' . $value['note_item'] . '</td>';
                $tdNum = '<td style="width: 15%;">' . $info['code'] . '</td>';
                $tdUnit = '<td class="text-center" style="width: 6%;">' . $unit['unit'] . '</td>';
                $tdQuantity = '<td class="text-center" style="width: 10%;">' . formatNumber($value['quantity']) . '</td>';
                $tdPrice = '<td class="text-center" style="width: 13%;">' . formatMoney($value['unit_price']) . '</td>';
                $tdSubtotal = '<td class="text-right" style="width: 15%;">' . formatMoney($value['total_amount']) . '</td>';

                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $tdQuantity . '
                    ' . $tdPrice . '
                    ' . $tdSubtotal . '
                    ' . $tdNote . '
                </tr>';
            }
        }

        // $day = date_format(date_create($quote['date']), 'd');
        // $month = date_format(date_create($quote['date']), 'm');
        // $year = date_format(date_create($quote['date']), 'Y');

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $message = "";
        ob_start();
        stylePdf();
        echo '
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="8"><h1 class="text-center uppercase" style="font-size: 20px;">' . _l('tnh_tb_quotes') . '</h1></td>
                </tr>
            </table>
            <br><br>
            <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td colspan="1"><span>' . _l('tnh_code_customer') . '</span>:</td>
                    <td colspan="1"><span>' . _l('tnh_ncustomer') . '</span>:</td>
                    <td colspan="1"><span>' . _l('Số BBG') . '</span>: </td>
                </tr>
                <tr nobr="true">
                    <td><b>' . $codeCustomer . '</b></td>
                    <td colspan="1"><b>' . $fullname . '</b></td>
                    <td><b>' . $quote['reference_no'] . '</b></td>
                </tr>
                <tr nobr="true">
                    <td colspan="1"><span>' . _l('tnh_unit') . '</span>: ' . $fullname . '</td>
                    <td colspan="1"><span>' . _l('tnh_address') . '</span>:</td>
                    <td></td>  
                </tr>
                <tr nobr="true">
                    <td colspan="1"><b>' . $companyCustomer . '</b></td>
                    <td colspan="1"><b>' . $addressCompany . '</b></td>
                    <td></td>
                </tr>
            </table>
            <br/><br/>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%; border-style: soild; border-color: black;">
                <tr nobr="true" class="text-center bold" style="background-color: #ddd;">
                    <td colspan="1" style="width: 6%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('tnh_numbers') . '</span></td>
                    <td colspan="1" style="width: 35%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('tnh_item_name') . '</span></td>
                    <td colspan="1" style="width: 6%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('tnh_dvt') . '</span></td>
                    <td colspan="1" style="width: 10%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('quantity') . '</span></td>
                    <td colspan="1" style="width: 13%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('tnh_prices') . '</span></td>
                    <td colspan="1" style="width: 15%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('tnh_subtotal') . '</span></td>
                    <td colspan="1" style="width: 15%; border-top-width: 2px; border-bottom-width: 1px;"><span>' . _l('tnh_note') . '</span></td>
                </tr>
                ' . $bodyItems . '
                <tr nobr="true" class="bold text-right" style="background-color: #ddd;">
                    <td colspan="5" style="border: none;">' . _l('tnh_subtotal') . ':</td>
                    <td colspan="1" style="border: none;">' . formatMoney($quote['total']) . '</td>
                    <td></td>
                </tr>
                ' . ($quote['total_tax'] > 0 ?
            '<tr nobr="true" class="bold text-right" style="background-color: #ddd;">
                        <td colspan="5">' . _l('Thuế VAT') . ':</td>
                        <td colspan="1">' . formatMoney($quote['total_tax']) . '</td>
                        <td></td>
                    </tr>
                    <tr nobr="true" class="bold text-right" style="background-color: #ddd;">
                        <td colspan="5">' . _l('tnh_grand_total') . ':</td>
                        <td colspan="1">' . formatMoney($quote['grand_total']) . '</td>
                        <td></td>
                    </tr>' : ''
        ) . '
            </table>
            <br/><br/>
            <table class="" cellspacing="0" cellpadding="5" border="0" style="width: 100%;">
                <tr nobr="true" class="bold">
                    <td class="" colspan="8">Thành tiền bằng chữ: ' . ucfirst(convert_number_to_words($quote['grand_total'])) . ' đồng chẵn</td>
                </tr>
                <tr nobr="true" class="bold">
                    <td class="" colspan="8">Ghi chú: ' . $quote['note'] . '</td>
                </tr>
                <tr nobr="true" class="bold">
                    <td class="" colspan="8"></td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td colspan="4"><span></span></td>
                    <td colspan="4"><span>TP.HCM, Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . '</span>: </td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td colspan="4"><span>Người lập biểu</span></td>
                    <td colspan="4"><span>Đại điện công ty TNHH Lê Trung Thiên</span></td>
                </tr>
                <tr nobr="true" class="text-center">
                    <td colspan="4"><span></span></td>
                    <td colspan="4"><span>(phê duyệt)</span></td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td colspan="4"><span class="uppercase"><br><br><br><br>' . $created_by . '</span></td>
                    <td colspan="4"><span class="uppercase"><br><br><br><br>LÊ TRUNG THIÊN</span></td>
                </tr>
            </table>
        ';

        $content = ob_get_contents();
        ob_end_clean();
        // echo $content;die;
        $barcode = file_get_contents(genBarcode($quote['reference_no']));
        $barcode = '<img style="width: 130px;" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';
        $data['content'] = $content;
        $data['barcode'] = $barcode;
        $pdf = print_pdf_tnh($data);
        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }

    public function print_pdf_backup($id, $type_pdf = 'I')
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $quote = $this->quotes_model->rowQuotesById($id);
        $address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
        $area = $this->site_model->rowDeliveryArea($address_delivery['city_shipping'], $address_delivery['district_shipping']);
        $companyCustomer = '';
        $addressCompany = '';
        $emailCompany = '';

        $personContact = '';
        $phoneContact = '';
        $customer = $this->clients_model->rowCustomer($quote['customer_id']);
        $codeCustomer = $customer['zcode'];
        $companyCustomer = $customer['company_short'];
        $addressCompany = $customer['address'];
        $phoneCompany = $customer['phonenumber'];
        $zaloCompany = $this->site_model->getZaloClient($quote['customer_id'])['zalo'];
        $emailCompany = $customer['email_client'];
        $tm_ck = $customer['tm_ck'] == 1 ? lang('tnh_tm') : ($customer['tm_ck'] == 2 ? lang('tnh_ck') : '');

        $contact = $this->site_model->rowContact($quote['person_contact_id']);
        $personContact = $contact['firstname'];
        $phoneContact = $contact['phonenumber'];
        $currencies = $this->site_model->getCurrenciesById($quote['currencies']);

        $quote_note_default = '';
        if (!empty($quote['note_default_id'])) {
            $quote_note_default = $this->quotes_model->getQuotesNoteDefaultText(explode(',', $quote['note_default_id']))['note_default'];
        }
        $created_by = get_staff_full_name($quote['created_by']);

        $employee = '';
        $emailEmp = '';
        $phoneEmp = '';
        $department = '';

        if (!empty($quote['employee_id'])) {
            $staff = $this->site_model->rowStaffById($quote['employee_id']);
            $employee = $staff['firstname'] . ' ' . $staff['lastname'];
            $emailEmp = $staff['email'];
            $phoneEmp = $staff['phonenumber'];
            $department = $this->site_model->getDepartmentsByStaff($quote['employee_id'])['name'];
        }

        $data['title'] = lang('tnh_quotes');
        $data['type'] = 'L';
        $data['img'] = '';


        $tHead = '<tr nobr="true" class="text-center bold">
            <th colspan="1" style="width: 6%;"><span>' . _l('No/STT') . '</span></th>
            <th colspan="1" style="width: 14%;"><span>Mã SP/<br>Product code</span></th>
            <th colspan="1" style="width: 10%;"><span>Item code</span></th>
            <th colspan="1" style="width: 19%;"><span>Diễn giải & thông số kỹ thuật /<br>Description & Specification</span></th>
            <th colspan="1" style="width: 10%;"><span>Nhãn hàng /<br>Brand</span></th>
            <th colspan="1" style="width: 5%;"><span>Đvt /<br>UoM</span></th>
            <th colspan="1" style="width: 8%;"><span>MOQ</span></th>
            <th colspan="1" style="width: 8%;"><span>Đơn giá /<br>Unit price</span></th>
            <th colspan="1" style="width: 10%;"><span>Chiết khấu /<br>Discount %</span></th>
            <th colspan="1" style="width: 10%;"><span>Thời gian giao hàng /<br>Leadtime</span></th>
        </tr>';

        $bodyItems = '';
        $items = $this->quotes_model->getQuoteItemsByQuoteId($id);
        if (!empty($items)) {
            $items_id_current = $items[0]['item_id'];
            $rowspan = 0;
            $arrItems = [];
            $stt = 1;
            $arrKey = 0;
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);

                if ($items_id_current != $items_id) {
                    foreach ($arrItems as $itemKey => $itemValue) {
                        if ($itemKey == 0) {
                            $bodyItems .= '<tr nobr="true">
                                ' . $tdNumber . '
                                ' . $tdCode . '
                                ' . $tdItemCode . '
                                ' . $tdDescription . '
                                ' . $tdBrand . '
                                ' . $tdUnit . '
                                ' . $itemValue['tdMOQ'] . '
                                ' . $itemValue['tdPrice'] . '
                                ' . $itemValue['tdDiscount'] . '
                                ' . $itemValue['tdLeadTime'] . '
                            </tr>';
                        } else {
                            $bodyItems .= '<tr nobr="true">
                                ' . $itemValue['tdMOQ'] . '
                                ' . $itemValue['tdPrice'] . '
                                ' . $itemValue['tdDiscount'] . '
                                ' . $itemValue['tdLeadTime'] . '
                            </tr>';
                        }
                    }
                    $arrItems = array();
                    $rowspan = 0;
                    $stt++;
                    $items_id_current = $items_id;
                    $arrKey = 0;
                }

                $rowspan++;
                $tdNumber = '<td rowspan="' . $rowspan . '" class="text-center" style="width: 6%;">' . ($stt) . '</td>';
                $tdCode = '<td rowspan="' . $rowspan . '" style="width: 14%;" class="text-center">' . $info['code'] . '</td>';
                $tdItemCode = '<td rowspan="' . $rowspan . '" style="width: 10%;" class="text-center"></td>';
                $tdDescription = '<td rowspan="' . $rowspan . '" style="width: 19%;" class="text-center">' . $value['technical_explanation'] . '</td>';
                $tdBrand = '<td rowspan="' . $rowspan . '" style="width: 10%;" class="text-center"></td>';
                $tdUnit = '<td rowspan="' . $rowspan . '" class="text-center" style="width: 5%;">' . $unit['unit'] . '</td>';

                $arrItems[$arrKey]['tdMOQ'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($value['moq']) . ' - ' . formatNumber($value['moq_to']) . '</td>';
                $arrItems[$arrKey]['tdPrice'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($value['unit_price']) . '</td>';
                $arrItems[$arrKey]['tdDiscount'] = '<td class="text-center" style="width: 10%; font-size: 11px;">' . formatNumber($value['discount_precent_item']) . '</td>';
                $arrItems[$arrKey]['tdLeadTime'] = '<td class="text-center" style="width: 10%; font-size: 11px;">' . formatNumber($value['lead_time']) . '</td>';
                $arrKey++;

                // $bodyItems .= '<tr nobr="true">
                //     ' . $tdNumber . '
                //     ' . $tdCode . '
                //     ' . $tdItemCode . '
                //     ' . $tdDescription . '
                //     ' . $tdBrand . '
                //     ' . $tdUnit . '
                //     ' . $tdMOQ . '
                //     ' . $tdPrice . '
                //     ' . $tdDiscount . '
                //     ' . $tdLeadTime . '
                // </tr>';
            }
        }
        foreach ($arrItems as $itemKey => $itemValue) {
            if ($itemKey == 0) {
                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . $tdItemCode . '
                    ' . $tdDescription . '
                    ' . $tdBrand . '
                    ' . $tdUnit . '
                    ' . $itemValue['tdMOQ'] . '
                    ' . $itemValue['tdPrice'] . '
                    ' . $itemValue['tdDiscount'] . '
                    ' . $itemValue['tdLeadTime'] . '
                </tr>';
            } else {
                $bodyItems .= '<tr nobr="true">
                    ' . $itemValue['tdMOQ'] . '
                    ' . $itemValue['tdPrice'] . '
                    ' . $itemValue['tdDiscount'] . '
                    ' . $itemValue['tdLeadTime'] . '
                </tr>';
            }
        }
        // var_dump($bodyItems);die;
        $trTotal = '<tr class="bold">
            <td colspan="2" class="text-center">TỔNG CỘNG</td>
            <td></td>
            <td class="text-center">' . formatNumber($quote['total_quantity']) . '</td>
            <td></td>
            <td></td>
            <td class="text-right"></td>
            <td></td>
        </tr>';

        $trVat = '<tr class="bold">
            <td colspan="2" class="text-center">THUẾ</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">' . formatMoney($quote['total_tax']) . ' VNĐ</td>
            <td></td>
        </tr>';


        $grandTotal = $quote['grand_total'];
        $trGrandTotal = '<tr class="bold">
            <td colspan="2" class="text-center">THÀNH TIỀN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">' . formatMoney($grandTotal) . ' VNĐ</td>
            <td></td>
            <td></td>
        </tr>';

        $trWordPayment =  '<tr class="bold">
            <td colspan="11">Số tiền bằng chữ: ' . ucfirst(convert_number_to_words($grandTotal)) . '</td>
        </tr>';

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $message = "";

        ob_start();
        stylePdf();
        $company_logo = get_option('company_logo');
        $img = base_url('uploads/company/' . $company_logo);

        // Thanh Danh 3D Printing Co.,Ltd
        echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td style="width: 100%;"><span class="bold">' . get_option('invoice_company_name') . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 30%;" rowspan="1"><span><img width="120" height="60" src="' . $img . '"></span></td>
                    <td style="width: 50%" class="text-center"><span class="bold">Thanh Danh 3D Printing Co.,Ltd</span><h1 style="color: red;">BẢNG BÁO GIÁ / QUOTATION</h1></td>
                    <td style="width: 20%;"></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Địa Chỉ:</span> ' . get_option('invoice_company_address') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">MÃ BÁO GIÁ</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['reference_no'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">MST:</span> ' . get_option('company_vat') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Ngày BÁO GIÁ/ Date: </span></td>
                    <td style="width: 20%;" class="text-right"><span>' . date_format(date_create($quote['date']), "d/m/Y") . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Tài Khoản Ngân Hàng:</span> ' . get_option('bank_account') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Ngày Hết Hạn: </span></td>
                    <td style="width: 20%;" class="text-right"><span>' . (!empty($quote['expiration_date']) ? _d($quote['expiration_date']) : '') . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Tên Ngân Hàng:</span> ' . get_option('bank_name') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Khách hàng / Customer: </span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $companyCustomer . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Người Lập Báo Giá:</span> ' . $created_by . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Mã KH / Customer Code:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $codeCustomer . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Thanh toán / Payment term:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['payment_term'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Điều kiện giao hàng / Delivery term:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['delivery_term'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Tiền tệ / Exchange:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $currencies['name'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Tỷ giá / Rate:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . formatMoney($quote['amount_to_vnd']) . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Người Phụ trách / Sales person:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $created_by . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Giao đến / Ship to:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['ship_to'] . '</span></td>
                </tr>
            </table>
            <br><br><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                <tbody>
            </table>
            </table><br><br><table class="" cellspacing="0" cellpadding="3" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td class="text-right" style="width: 100%;">
                         Month ……………..Date ………………………Year
                    </td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td style="width: 30%;"></td>
                    <td style="width: 45%;" class="text-center"><span class="bold">Khách hàng xác nhận / Vendor confirmed</span></td>
                    <td style="width: 25%;" class="text-center"><span class="bold">Ký tên / Authorized Signature</span></td>
                </tr>
            </table><br><br><br>
            <div><span class="bold">Lưu ý / Notes:</span><br>' . $quote['note'] . '</div>
        ';


        // echo '
        //     <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
        //         <tr nobr="true">
        //             <td style="width: 100%;"><h1 class="text-center uppercase" style="font-size: 23px;">BẢNG BÁO GIÁ / QUOTATION</h1></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;" class="text-center"><span class="italic">Phiếu số: ' . $quote['reference_no'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;" class="text-center"><span class="italic">Ngày / Date: ' . date_format(date_create($quote['date']), "d/m/Y") . '</span><br></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Khách hàng / Customer:</b> <span class="bold">' . $companyCustomer . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Mã KH / Customer Code:</b> <span class="bold">' . $codeCustomer . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Thông tin chung / General Information:</b> <span class="">' . $tm_ck . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Thanh toán / Payment term:</b> <span class="">' . $quote['payment_term'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 60%;"><b>Điều kiện giao hàng / Delivery term:</b> <span class="">' . $quote['delivery_term'] . '</span></td>
        //             <td style="width: 40%;"><b>Giao đến / Ship to:</b> <span class="">' . $quote['ship_to'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Chi tiết Thanh toán / Payment detail:</b> <span class="">' . $quote['payment_detail'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Tiền tệ / Exchange:</b> <span class="">' . $currencies['name'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Tỷ giá / Rate:</b> <span class="">' . $quote['amount_to_vnd'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Người y/c / Attn.:</b> <span class="">'.$personContact.'</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Người Phụ trách / Sales person:</b> <span class="">'.$created_by.'</span></td>
        //         </tr>
        //     </table><br><br><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
        //         <thead>
        //             ' . $tHead . '
        //         </thead>
        //         <tbody>
        //             ' . $bodyItems . '
        //         <tbody>
        //     </table><br><br><table class="" cellspacing="0" cellpadding="3" border="0" style="width: 100%;">
        //         <tr nobr="true">
        //             <td class="text-right" style="width: 100%;">
        //                 Month ……………..Date ………………………Year
        //             </td>
        //         </tr>
        //         <tr nobr="true" class="text-center bold">
        //             <td style="width: 33%;"></td>
        //             <td style="width: 33%;" class="text-center"><span class="bold">Khách hàng xác nhận / Vendor confirmed</span></td>
        //             <td style="width: 33%;" class="text-center"><span class="bold">Ký tên / Authorized Signature</span></td>
        //         </tr>
        //     </table><br><br><br>
        //     <div>Lưu ý / Notes:<br>'.$quote['note'].'</div>
        // ';

        $data['pageCustome'] = 'quotes';
        $content = ob_get_contents();
        ob_end_clean();

        $barcode = file_get_contents(genBarcode($quote['reference_no']));
        $barcode = '<img style="width: 130px;" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';

        $data['showHeader'] = 'hide';
        $data['type_print'] = 'quotes';
        $data['content'] = $content;
        $data['barcode'] = '';
        $pdf = @print_pdf_tnh($data);
        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }

    public function print_pdf($id, $type_pdf = 'I')
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $quote = $this->quotes_model->rowQuotesById($id);
        $address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
        $area = $this->site_model->rowDeliveryArea($address_delivery['city_shipping'], $address_delivery['district_shipping']);
        $companyCustomer = '';
        $addressCompany = '';
        $emailCompany = '';

        $personContact = '';
        $phoneContact = '';
        $emailContact = '';
        $customer = $this->clients_model->rowCustomer($quote['customer_id']);
        $codeCustomer = $customer['zcode'];
        $companyCustomer = $customer['company_short'];

        $contact = $this->site_model->rowContact($quote['person_contact_id']);
        // var_dump($contact);die;
        $personContact = $contact['firstname'];
        $phoneContact = $contact['phonenumber'];
        $emailContact = $contact['email'];

        $created_by = get_staff_full_name($quote['created_by']);
        $staff_create = get_table_where('tblstaff', ['staffid' => $quote['created_by']], '', 'row_array');


        if (!empty($quote['employee_id'])) {
            $staff = $this->site_model->rowStaffById($quote['employee_id']);
        }

        // $this->db->select('moq, moq_to');
        // $this->db->from('tbl_quote_items');
        // $this->db->where('quote_id', $id);
        // $this->db->order_by('moq', 'asc');
        // $this->db->order_by('moq_to', 'asc');
        // $this->db->group_by('moq, moq_to');
        // $moqThData =  $this->db->get()->result_array();

        $this->db->select('tbl_quote_items.moq, tbl_quote_items.moq_to');
        $this->db->from('tbl_quote_items');
        $this->db->where('tbl_quote_items.quote_id', $id);
        $this->db->order_by('tbl_quote_items.moq ASC, tbl_quote_items.moq_to ASC');
        $this->db->group_by('tbl_quote_items.moq, tbl_quote_items.moq_to');
        $moqThData =  $this->db->get()->result_array();
        $dtMoq = $moqThData;
        $moqTh = '';
        $moqTh2 = '';
        foreach ($moqThData as $key => $value) {
            if ($value['moq'] % 1000 == 0 && $value['moq'] >= 1000) {
                $moq = ($value['moq'] / 1000) . 'K';
            } else {
                $moq = $value['moq'];
            }

            if ($value['moq_to'] % 1000 == 0 && $value['moq_to'] >= 1000) {
                $moq_to = ($value['moq_to'] / 1000) . 'K';
            } else {
                $moq_to = $value['moq_to'];
            }

            $moqTh .= '<th style="font-weight: nomal" rowspan="1" colspan="3">MOQ ' . $moq . ' - ' . $moq_to . '</th>';
            $moqTh2 .= '<th>' . $codeCustomer . '</th><th>Thành Danh</th><th>%</th>';
        }

        $data['title'] = lang('tnh_quotes');
        $data['type'] = 'L';
        $data['img'] = '';

        $tHead = '<tr nobr="true" class="text-center bold" style="font-size: 10px">
            <th rowspan="2" style=""><span>' . _l('STT') . '</span></th>
            <th rowspan="2" style=""><span>BRAND<br>(Nhãn Hiệu)</span></th>
            <th rowspan="2" style=""><span>Tên Gọi Khách Hàng</span></th>
            <th rowspan="2" style=""><span>Item Code-Giá Khách Hàng</span></th>
            <th rowspan="2" style=""><span>Photo<br>(Hình ảnh)</span></th>
            <th rowspan="2" style=""><span>Flat Size<br>(Kích thước) (mm)</span></th>
            <th rowspan="2" style=""><span>Thanh Danh item code<br>(Mã Thành Danh)</span></th>
            <th rowspan="2" style=""><span>Thanh Danh item name<br>(Tên Thành Danh)</span></th>
            <th rowspan="2" style=""><span>UoM<br>(Đơn vị tính)</span></th>'
            . $moqTh .
            '<th rowspan="2" style=""><span>Leadtime<br>(thời gian xử lý)</span></th>
            <th rowspan="2" style=""><span>Yêu cầu đặc biệt</span></th>
        </tr>
        <tr nobr="true" class="text-center bold" style="font-size: 10px">
            ' . $moqTh2 . '
        </tr>
        ';

        $bodyItems = '';
        // $this->db->select('*');
        // $this->db->from('tbl_quote_items');
        // $this->db->where('quote_id', $id);
        // $this->db->order_by('type_item', 'desc');
        // $this->db->order_by('item_id', 'desc');
        // $this->db->group_by('item_id, type_item, technical_explanation, note_item');
        // $items =  $this->db->get()->result_array();
        // // echo '<pre>';var_dump($items);die;
        // $moqTd = '';
        // if (!empty($items)) {
        // 	$items_id_current = $items[0]['item_id'];
        // 	// $items_id_current = '';
        // 	$rowspan = 0;
        // 	$arrItems = [];
        // 	$stt = 1;
        // 	$arrKey = 0;
        // 	foreach ($items as $key => $value) {
        // 		$type_item = $value['type_item'];
        // 		$items_id = $value['item_id'];

        // 		$info = $this->products_model->rowProduct($items_id);
        //         // var_dump($info);die;
        // 		$unit = $this->unit_model->rowUnit($info['unit_id']);

        // 		if ($items_id_current != $items_id) {
        // 			foreach($arrItems as $itemKey => $itemValue) {
        // 				// if ($itemKey == 0) {
        //                     $moqTd = '';
        //                     if (!empty($moqThData)) {
        //                         foreach ($moqThData as $moqThDataValue) {
        //                             $this->db->select('*');
        //                             $this->db->from('tbl_quote_items');
        //                             $this->db->where('quote_id', $id);
        //                             $this->db->where('item_id', $value['item_id']);
        //                             $this->db->where('type_item', $value['type_item']);
        //                             $this->db->where('moq', $moqThDataValue['moq']);
        //                             $this->db->where('moq_to', $moqThDataValue['moq_to']);
        //                             $itemMoq =  $this->db->get()->row_array();

        //                             $arrItemMoq['tdMOQ'] = '<td class="text-center" style="width: 9%; font-size: 11px;">' . formatNumber($itemMoq['moq']) . ' - ' . formatNumber($itemMoq['moq_to']) . '</td>';

        //                             $arrItemMoq['tdPrice'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($itemMoq['unit_price']) . '</td>';

        //                             $unit_price_discount = $itemMoq['unit_price'];
        //                             if ($itemMoq['discount_precent_item'] > 0) {
        //                                 $unit_price_discount = $unit_price_discount - $unit_price_discount * $itemMoq['discount_precent_item']/100;
        //                             }
        //                             $arrItemMoq['tdPriceDiscount'] = '<td class="text-center" style="">' . formatNumber($unit_price_discount, 0) . '</td>';
        //                             $arrItemMoq['tdPriceCurrency'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($itemMoq['unit_price']/$quote['amount_to_vnd']) . '</td>';
        //                             $arrItemMoq['tdPriceCurrencyDiscount'] = '<td class="text-center" style="">' . formatNumber($unit_price_discount/$quote['amount_to_vnd']) . '</td>';

        //                             $arrItemMoq['tdDiscount'] = '<td class="text-center" style="">' . formatNumber($itemMoq['discount_precent_item']) . '</td>';

        //                             if (!empty(($unit_price_discount/$quote['amount_to_vnd']))) {
        //                                 $percent = ($unit_price_discount-($unit_price_discount/$quote['amount_to_vnd']))/($unit_price_discount/$quote['amount_to_vnd']);
        //                                 $percent = formatNumber($percent, 0);
        //                             } else {
        //                                 $percent = 0;
        //                             }

        //                             $arrItemMoq['percent'] = '<td class="text-center" style="">' . ($percent) . '</td>';

        //                             $moqTd .= $arrItemMoq['tdPriceDiscount'].$arrItemMoq['tdPriceCurrencyDiscount'].$arrItemMoq['percent'];
        //                         }
        //                     }

        //                         $bodyItems .= '<tr nobr="true" style="font-size: 10px">
        //                         ' . $tdNumber . '
        //                         ' . $tdBrand . '
        //                         <td></td>
        //                         <td></td>
        //                         '.$tdImages.'
        //                         '.$tdSize.'
        //                         ' . $tdCode . '
        //                         ' . $tdName . '
        //                         ' . $tdUnit . '
        //                         '.$moqTd.'
        //                         ' . $itemValue['tdLeadTime'] . '
        //                         ' . $tdDescription . '
        //                         '
        //                     .'</tr>';
        // 			}
        // 			$arrItems = array();
        // 			$rowspan = 0;
        // 			$stt++;
        // 			$items_id_current = $items_id;
        // 			$arrKey = 0;
        // 		}

        // 		// $rowspan++;
        // 		$tdNumber = '<td rowspan="'.$rowspan.'" class="text-center" style="">' . ($stt) . '</td>';
        // 		$tdCode = '<td rowspan="'.$rowspan.'" style="" class="text-left">' . $info['code'] . '</td>';
        // 		$tdName = '<td rowspan="'.$rowspan.'" style="" class="text-left">' . $info['name'] . '</td>';
        // 		$tdDescription = '<td rowspan="'.$rowspan.'" style="" class="text-center">'.$value['technical_explanation'].'</td>';
        // 		$tdBrand = '<td rowspan="'.$rowspan.'" style="" class="text-center"></td>';
        // 		$tdUnit = '<td rowspan="'.$rowspan.'" class="text-center" style="">' . $unit['unit'] . '</td>';

        // 		$arrItems[$arrKey]['tdMOQ'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['moq']) . ' - ' . formatNumber($value['moq_to']) . '</td>';

        // 		$arrItems[$arrKey]['tdPrice'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['unit_price']) . '</td>';

        //         if (!empty($info['images'])) {
        //             $imgSrc = base_url('uploads/products/'.$info['images']);
        //             if (!file_exists($imgSrc)){
        //                 $imgSrc = base_url('assets/images/tnh/no_image.png');
        //             }
        //             $tdImages = '<td><img src="'.$imgSrc.'"></td>';
        //         } else {
        //             $tdImages = '<td><img src="'.base_url('assets/images/tnh/no_image.png').'"></td>';
        //         }
        //         $tdSize = '<td class="text-center">'.$info['size'].'</td>';
        //         $unit_price_discount = $value['unit_price'];
        //         if ($value['discount_precent_item'] > 0) {
        //             $unit_price_discount = $unit_price_discount - $unit_price_discount * $value['discount_precent_item']/100;
        //         }
        //         $arrItems[$arrKey]['tdPriceDiscount'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($unit_price_discount, 0) . '</td>';
        // 		$arrItems[$arrKey]['tdPriceCurrency'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['unit_price']/$quote['amount_to_vnd']) . '</td>';
        // 		$arrItems[$arrKey]['tdPriceCurrencyDiscount'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($unit_price_discount/$quote['amount_to_vnd']) . '</td>';

        // 		$arrItems[$arrKey]['tdDiscount'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['discount_precent_item']) . '</td>';
        // 		$arrItems[$arrKey]['tdLeadTime'] = '<td class="text-center" style=" font-size: 11px;">' . formatNumber($value['lead_time']) . '</td>';
        // 		$arrKey++;

        // 	}
        // }

        // foreach($arrItems as $itemKey => $itemValue) {
        //         $bodyItems .= '<tr nobr="true" style="font-size: 10px">
        //                         ' . $tdNumber . '
        //                         ' . $tdBrand . '
        //                         <td></td>
        //                         <td></td>
        //                         '.$tdImages.'
        //                         '.$tdSize.'
        //                         ' . $tdCode . '
        //                         ' . $tdName . '
        //                         ' . $tdUnit . '
        //                         '.$moqTd.'
        //                         ' . $itemValue['tdLeadTime'] . '
        //                         ' . $tdDescription . '
        //                         '
        //                     .'</tr>';
        // }

        $this->db->select('
            tbl_quote_items.item_id, 
            tbl_quote_items.type_item, 
            tbl_quote_items.unit_price, 
            tbl_quote_items.technical_explanation, 
            tbl_quote_items.note_item,
            tbl_quote_items.lead_time,
            tbl_quote_items.unit_price as unit_price,
            tbl_quote_items.discount_precent_item as discount_precent_item,
            tbl_products.code as item_code, 
            tbl_products.name as item_name, 
            tbl_products.product_code_customer as product_code_customer,
            tbl_products.product_name_customer as product_name_customer,
            tbl_products.brand as brand,
            tblunits.unit as unit_name,
            tblsize.name as size_name,
            GROUP_CONCAT(distinct CONCAT(coalesce(tbl_quote_items.moq, 0), "__", coalesce(tbl_quote_items.moq_to, 0))) as moq_range,
            tbl_products.images as images
        ', false);
        $this->db->from('tbl_quote_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
        $this->db->where('tbl_quote_items.quote_id', $id);
        $this->db->where('tbl_quote_items.type_item', 'products');
        $this->db->group_by('tbl_quote_items.item_id, tbl_quote_items.type_item, tbl_quote_items.unit_price, tbl_quote_items.technical_explanation, tbl_quote_items.note_item, tbl_quote_items.lead_time');
        $this->db->order_by('tbl_quote_items.item_id ASC');
        $items = $this->db->get()->result_array();
        $stt = 0;
        if ($items) {
            foreach ($items as $key => $value) {
                $stt++;

                $imagesProduct = '';
                if (!empty($info['images'])) {
                    $imgSrc = base_url('uploads/products/' . $info['images']);
                    if (!file_exists($imgSrc)) {
                        $imgSrc = base_url('assets/images/tnh/no_image.png');
                    }
                    $imagesProduct = '<img style="width: 50px;" src="' . $imgSrc . '"></td>';
                } else {
                    $imagesProduct = '<img style="width: 50px;" src="' . base_url('assets/images/tnh/no_image.png') . '">';
                }

                $tdNumber = '<td class="text-center">' . $stt . '</td>';
                $tdBrand = '<td class="text-center">' . $value['brand'] . '</td>';
                $tdItemNameCustomer = '<td class="text-center">' . $value['product_name_customer'] . '</td>';
                $tdItemCodeCustomer = '<td class="text-center">' . $value['product_code_customer'] . '</td>';
                $tdImages = '<td class="text-center">' . $imagesProduct . '</td>';
                $tdSize = '<td class="text-center">' . $value['size_name'] . '</td>';
                $tdCode = '<td class="text-center">' . $value['item_code'] . '</td>';
                $tdName = '<td class="text-center">' . $value['item_name'] . '</td>';
                $tdUnit = '<td class="text-center">' . $value['unit_name'] . '</td>';

                $moqTd = '';
                $r_pac = $value['unit_price'];
                if ($value['discount_precent_item'] > 0) {
                    $r_pac = $r_pac - $r_pac * $value['discount_precent_item'] / 100;
                }

                $thanh_danh = 0;
                if ($quote['amount_to_vnd'] > 0) {
                    $thanh_danh = $r_pac / $quote['amount_to_vnd'];
                }
                $percent = 0;
                if ($thanh_danh > 0) {
                    $percent = $r_pac / $thanh_danh * 100;
                }

                $moq_range = $value['moq_range'];
                $arrMoqRange = [];
                if (!empty($moq_range)) {
                    $arrMoqRange = explode(',', $moq_range);
                    if (!empty($arrMoqRange)) {
                        foreach ($arrMoqRange as $kMR => $vMR) {
                            $arrVMR = explode('__', $vMR);
                            $arr = [];
                            $arr['moq'] = $arrVMR[0];
                            $arr['moq_to'] = $arrVMR[1];
                            $arrMoqRange[$kMR] = $arr;
                        }
                    }
                }

                foreach ($dtMoq as $kMoq => $vMoq) {
                    $flagCheck = false;
                    foreach ($arrMoqRange as $kMR => $vMR) {
                        if ($vMoq['moq'] == $vMR['moq'] && $vMoq['moq_to'] == $vMR['moq_to']) {
                            $flagCheck = true;
                        }
                    }

                    $temp_r_pac = 0;
                    $temp_thanh_danh = 0;
                    $temp_percent = 0;
                    if ($flagCheck) {
                        $temp_r_pac = $r_pac;
                        $temp_thanh_danh = $thanh_danh;
                        $temp_percent = $percent;
                    }
                    $moqTd .= '<td class="text-center">' . formatMoney($temp_r_pac) . '</td>';
                    $moqTd .= '<td class="text-center">' . formatMoney($temp_thanh_danh) . '</td>';
                    $moqTd .= '<td class="text-center">' . formatMoney($temp_percent) . '</td>';
                }

                $tdLeadTime = '<td class="text-center"></td>';
                $tdDescription = '<td style="text-align: left;"></td>';

                $bodyItems .= '<tr nobr="true" style="font-size: 10px">
                    ' . $tdNumber . '
                    ' . $tdBrand . '
                    ' . $tdItemNameCustomer . '
                    ' . $tdItemCodeCustomer . '
                    ' . $tdImages . '
                    ' . $tdSize . '
                    ' . $tdCode . '
                    ' . $tdName . '
                    ' . $tdUnit . '
                    ' . $moqTd . '
                    ' . $tdLeadTime . '
                    ' . $tdDescription . '
                    '
                    . '</tr>';
            }
        }

        ob_start();
        stylePdf();
        $company_logo = get_option('company_logo');
        $img = base_url('uploads/company/' . $company_logo);

        // <td style="width: 23.75%;"><span class="bold">Email: </span>'.$customer['email_client'].'</td>
        // <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>'.$customer['phonenumber'].'</td>
        // Thanh Danh 3D Printing Co.,Ltd
        $html = '<div style="text-align: left">';
        $html .= '<span style="font-weight: bold; font-size: 11px; color: black;">' . get_option('invoice_company_name') . '</span><br>';
        $html .= '<span style="font-size: 10px;">' . _l('Địa chỉ') . ' : ' . get_option('invoice_company_address') . '</span><br>';
        $html .= '<span style="font-size: 10px;">' . _l('Điện thoại') . ' : ' . get_option('invoice_company_phonenumber') . '</span> <span style="font-size: 9px;"> ' . _l('Fax') . ' : ' . get_option('fax_company') . '</span><br>';
        $html .= '<span style="font-size: 10px;">' . _l('Email') . ' : ' . get_option('email_company') . '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 9px;">' . _l('tnh_website') . ' : ' . get_option('company_website') . '</span><br>';
        $html .= '</div>';
        echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%; font-size: 10px">
                <tr nobr="true">
                    <td style="width: 15%;" rowspan="1" class="text-center"><span><img width="125" height="75" src="' . $img . '"></span><b class="text-center">Time for success</b></td>
                    <td style="width: 45%;" rowspan="1">' . $html . '</td>
                    <td style="width: 40%" class="text-center"><h1 style="color: black; font-size: 21px">BẢNG BÁO GIÁ / QUOTATION</h1>
                    <br>
                    <table border="1">
                        <tr class="text-center">
                            <td colspan="5">Loại Sản Phẩm</td>
                        </tr>
                        <tr class="text-center">
                            <td style="width: 20%">Hangtag</td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%">Thay đổi</td>
                            <td style="width: 20%">Cố định</td>
                            <td style="width: 20%"></td>
                        </tr>
                        <tr class="text-center">
                            <td style="width: 20%">Label</td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%"></td>
                            <td style="width: 20%"></td>
                        </tr>
                    </table>
                    </td>
                    <td style="width: 20%;"></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Khách hàng: </span></span></td>
                    <td style="width: 35%;">' . $companyCustomer . '</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 14%;"><span class="bold">MÃ BÁO GIÁ</span></td>
                    <td style="width: 30.5%;"><span>' . $quote['reference_no'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Người yêu cầu báo giá:</span></span></td>
                    <td style="width: 35%;">' . $personContact . '</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 14%;"><span class="bold">Người thực hiện báo giá: </span></td>
                    <td style="width: 30.5%;">' . $created_by . '</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 23.75%;"><span class="bold">Email: </span>' . $emailContact . '</td>
                    <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>' . $phoneContact . '</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 23.75%;"><span class="bold">Email: </span>' . $staff_create['email'] . '</td>
                    <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>' . $staff_create['phonenumber'] . '</td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Địa chỉ công ty:</span></span></td>
                    <td style="width: 35%;">' . $customer['address'] . '</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 14%;"><span class="bold">Ngày báo giá: </span></td>
                    <td style="width: 30.5%;"><span>' . _d($quote['date']) . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Địa điểm giao hàng:</span></span></td>
                    <td style="width: 35%;">' . $quote['ship_to'] . '</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 17%;"><span class="bold">Ngày hết hạn: </span></td>
                    <td style="width: 30.5%;"><span>' . (!empty($quote['expiration_date']) ? _d($quote['expiration_date']) : '') . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 12.5%;"><span><span class="bold">Người nhận hàng:</span> </span></td>
                    <td style="width: 35%;">' . $personContact . '</td>
                    <td style="width: 5%;"></td>
                    <td style="width: 17%;"><span class="bold">Hình thức thanh toán:</span></td>
                    <td style="width: 30.5%;"><span>' . $quote['payment_term'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 23.75%;"><span class="bold">Email: </span>' . $emailContact . '</td>
                    <td style="width: 23.75%;"><span class="bold">Số liên hệ: </span>' . $phoneContact . '</td>
                    <td style="width: 5%;"></td>
                </tr>
            </table>
            <br><br><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                <tbody>
            </table>
            </table><br><br><table class="" cellspacing="0" cellpadding="3" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td class="text-right" style="width: 100%;">
                         Month ……………..Date ………………………Year
                    </td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td style="width: 30%;"></td>
                    <td style="width: 45%;" class="text-center"><span class="bold">Khách hàng xác nhận / Vendor confirmed</span></td>
                    <td style="width: 25%;" class="text-center"><span class="bold">Ký tên / Authorized Signature</span></td>
                </tr>
            </table><br><br><br>
            <div><span class="bold">Lưu ý / Notes:</span><br>' . $quote['note'] . '</div>
        ';

        $data['pageCustome'] = 'quotes';
        $content = ob_get_contents();
        ob_end_clean();

        $barcode = file_get_contents(genBarcode($quote['reference_no']));
        $barcode = '<img style="width: 130px;" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';

        $data['showHeader'] = 'hide';
        $data['type_print'] = 'quotes';
        $data['content'] = $content;
        $data['barcode'] = '';

        $qrStyle = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $data['qrCode'] = [
            'code' => 'quotes||' . $id,
            'type' => 'QRCODE,Q',
            'x' => 260,
            'y' => 30,
            'width' => 70,
            'height' => 70,
            'style' => $qrStyle,
            'align' => 'N',
        ];
        $pdf = @print_pdf_tnh($data);

        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }

    public function print_pdf_old2($id, $type_pdf = 'I')
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }
        ob_end_clean();
        $data = [];
        $quote = $this->quotes_model->rowQuotesById($id);
        $address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
        $area = $this->site_model->rowDeliveryArea($address_delivery['city_shipping'], $address_delivery['district_shipping']);
        $companyCustomer = '';
        $addressCompany = '';
        $emailCompany = '';

        $personContact = '';
        $phoneContact = '';
        $customer = $this->clients_model->rowCustomer($quote['customer_id']);
        $codeCustomer = $customer['zcode'];
        $companyCustomer = $customer['company_short'];
        $addressCompany = $customer['address'];
        $phoneCompany = $customer['phonenumber'];
        $zaloCompany = $this->site_model->getZaloClient($quote['customer_id'])['zalo'];
        $emailCompany = $customer['email_client'];
        $tm_ck = $customer['tm_ck'] == 1 ? lang('tnh_tm') : ($customer['tm_ck'] == 2 ? lang('tnh_ck') : '');

        $contact = $this->site_model->rowContact($quote['person_contact_id']);
        $personContact = $contact['firstname'];
        $phoneContact = $contact['phonenumber'];
        $currencies = $this->site_model->getCurrenciesById($quote['currencies']);

        $quote_note_default = '';
        if (!empty($quote['note_default_id'])) {
            $quote_note_default = $this->quotes_model->getQuotesNoteDefaultText(explode(',', $quote['note_default_id']))['note_default'];
        }
        $created_by = get_staff_full_name($quote['created_by']);

        $employee = '';
        $emailEmp = '';
        $phoneEmp = '';
        $department = '';

        if (!empty($quote['employee_id'])) {
            $staff = $this->site_model->rowStaffById($quote['employee_id']);
            $employee = $staff['firstname'] . ' ' . $staff['lastname'];
            $emailEmp = $staff['email'];
            $phoneEmp = $staff['phonenumber'];
            $department = $this->site_model->getDepartmentsByStaff($quote['employee_id'])['name'];
        }

        $data['title'] = lang('tnh_quotes');
        $data['type'] = 'L';
        $data['img'] = '';


        $tHead = '<tr nobr="true" class="text-center bold">
            <th colspan="1" style="width: 3%;"><span>' . _l('No/STT') . '</span></th>
            <th colspan="1" style="width: 20%;"><span>Mã SP/<br>Product code</span></th>'
            . '<th colspan="1" style="width: 12%;"><span>Diễn giải & thông số kỹ thuật /<br>Description & Specification</span></th>
            <th colspan="1" style="width: 6%;"><span>Nhãn hàng /<br>Brand</span></th>
            <th colspan="1" style="width: 5%;"><span>Đvt /<br>UoM</span></th>
            <th colspan="1" style="width: 9%;"><span>MOQ</span></th>
            <th colspan="1" style="width: 8%;"><span>Đơn giá /<br>Unit price (VNĐ)<br>Sample</span></th>
            <th colspan="1" style="width: 8%;"><span>Đơn giá /<br>Unit price (VNĐ)<br>Production</span></th>
            <th colspan="1" style="width: 8%;"><span>Đơn giá /<br>Unit price (' . $currencies['name'] . ')<br>Sample</span></th>
            <th colspan="1" style="width: 8%;"><span>Đơn giá /<br>Unit price (' . $currencies['name'] . ')<br>Production</span></th>
            <th colspan="1" style="width: 7%;"><span>Chiết khấu /<br>Discount %</span></th>
            <th colspan="1" style="width: 8%;"><span>Thời gian giao hàng /<br>Leadtime</span></th>
        </tr>';

        $bodyItems = '';
        //$items = $this->quotes_model->getQuoteItemsByQuoteId($id);
        $this->db->select('*');
        $this->db->from('tbl_quote_items');
        $this->db->where('quote_id', $id);
        $this->db->order_by('type_item', 'desc');
        $this->db->order_by('item_id', 'desc');
        $items =  $this->db->get()->result_array();
        if (!empty($items)) {
            $items_id_current = $items[0]['item_id'];
            $rowspan = 0;
            $arrItems = [];
            $stt = 1;
            $arrKey = 0;
            foreach ($items as $key => $value) {
                $type_item = $value['type_item'];
                $items_id = $value['item_id'];

                $info = $this->products_model->rowProduct($items_id);
                $unit = $this->unit_model->rowUnit($info['unit_id']);

                if ($items_id_current != $items_id) {
                    foreach ($arrItems as $itemKey => $itemValue) {
                        if ($itemKey == 0) {
                            $bodyItems .= '<tr nobr="true">
                                ' . $tdNumber . '
                                ' . $tdCode . '
                                ' . ''/*$tdItemCode*/ . '
                                ' . $tdDescription . '
                                ' . $tdBrand . '
                                ' . $tdUnit . '
                                ' . $itemValue['tdMOQ'] . '
                                ' . $itemValue['tdPrice'] . '
                                ' . $itemValue['tdPriceDiscount'] . '
                                ' . $itemValue['tdPriceCurrency'] . '
                                ' . $itemValue['tdPriceCurrencyDiscount'] . '
                                ' . $itemValue['tdDiscount'] . '
                                ' . $itemValue['tdLeadTime'] . '
                            </tr>';
                        } else {
                            $bodyItems .= '<tr nobr="true">
                                ' . $itemValue['tdMOQ'] . '
                                ' . $itemValue['tdPrice'] . '
                                ' . $itemValue['tdPriceDiscount'] . '
                                ' . $itemValue['tdPriceCurrency'] . '
                                ' . $itemValue['tdPriceCurrencyDiscount'] . '
                                ' . $itemValue['tdDiscount'] . '
                                ' . $itemValue['tdLeadTime'] . '
                            </tr>';
                        }
                    }
                    $arrItems = array();
                    $rowspan = 0;
                    $stt++;
                    $items_id_current = $items_id;
                    $arrKey = 0;
                }

                $rowspan++;
                $tdNumber = '<td rowspan="' . $rowspan . '" class="text-center" style="width: 3%;">' . ($stt) . '</td>';
                $tdCode = '<td rowspan="' . $rowspan . '" style="width: 20%;" class="text-left">' . $info['code'] . '</td>';
                // $tdItemCode = '<td rowspan="'.$rowspan.'" style="width: 8%;" class="text-center"></td>';
                $tdDescription = '<td rowspan="' . $rowspan . '" style="width: 12%;" class="text-center">' . $value['technical_explanation'] . '</td>';
                $tdBrand = '<td rowspan="' . $rowspan . '" style="width: 6%;" class="text-center"></td>';
                $tdUnit = '<td rowspan="' . $rowspan . '" class="text-center" style="width: 5%;">' . $unit['unit'] . '</td>';

                $arrItems[$arrKey]['tdMOQ'] = '<td class="text-center" style="width: 9%; font-size: 11px;">' . formatNumber($value['moq']) . ' - ' . formatNumber($value['moq_to']) . '</td>';

                $arrItems[$arrKey]['tdPrice'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($value['unit_price']) . '</td>';

                $unit_price_discount = $value['unit_price'];
                if ($value['discount_precent_item'] > 0) {
                    $unit_price_discount = $unit_price_discount - $unit_price_discount * $value['discount_precent_item'] / 100;
                }
                $arrItems[$arrKey]['tdPriceDiscount'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($unit_price_discount, 0) . '</td>';
                $arrItems[$arrKey]['tdPriceCurrency'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($value['unit_price'] / $quote['amount_to_vnd']) . '</td>';
                $arrItems[$arrKey]['tdPriceCurrencyDiscount'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($unit_price_discount / $quote['amount_to_vnd']) . '</td>';

                $arrItems[$arrKey]['tdDiscount'] = '<td class="text-center" style="width: 7%; font-size: 11px;">' . formatNumber($value['discount_precent_item']) . '</td>';
                $arrItems[$arrKey]['tdLeadTime'] = '<td class="text-center" style="width: 8%; font-size: 11px;">' . formatNumber($value['lead_time']) . '</td>';
                $arrKey++;

                // $bodyItems .= '<tr nobr="true">
                //     ' . $tdNumber . '
                //     ' . $tdCode . '
                //     ' . $tdItemCode . '
                //     ' . $tdDescription . '
                //     ' . $tdBrand . '
                //     ' . $tdUnit . '
                //     ' . $tdMOQ . '
                //     ' . $tdPrice . '
                //     ' . $tdDiscount . '
                //     ' . $tdLeadTime . '
                // </tr>';
            }
        }
        foreach ($arrItems as $itemKey => $itemValue) {
            if ($itemKey == 0) {
                $bodyItems .= '<tr nobr="true">
                    ' . $tdNumber . '
                    ' . $tdCode . '
                    ' . ''/*$tdItemCode*/ . '
                    ' . $tdDescription . '
                    ' . $tdBrand . '
                    ' . $tdUnit . '
                    ' . $itemValue['tdMOQ'] . '
                    ' . $itemValue['tdPrice'] . '
                    ' . $itemValue['tdPriceDiscount'] . '
                    ' . $itemValue['tdPriceCurrency'] . '
                    ' . $itemValue['tdPriceCurrencyDiscount'] . '
                    ' . $itemValue['tdDiscount'] . '
                    ' . $itemValue['tdLeadTime'] . '
                </tr>';
            } else {
                $bodyItems .= '<tr nobr="true">
                    ' . $itemValue['tdMOQ'] . '
                    ' . $itemValue['tdPrice'] . '
                    ' . $itemValue['tdPriceDiscount'] . '
                    ' . $itemValue['tdPriceCurrency'] . '
                    ' . $itemValue['tdPriceCurrencyDiscount'] . '
                    ' . $itemValue['tdDiscount'] . '
                    ' . $itemValue['tdLeadTime'] . '
                </tr>';
            }
        }
        // var_dump($bodyItems);die;
        $trTotal = '<tr class="bold">
            <td colspan="2" class="text-center">TỔNG CỘNG</td>
            <td></td>
            <td class="text-center">' . formatNumber($quote['total_quantity']) . '</td>
            <td></td>
            <td></td>
            <td class="text-right"></td>
            <td></td>
        </tr>';

        $trVat = '<tr class="bold">
            <td colspan="2" class="text-center">THUẾ</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">' . formatMoney($quote['total_tax']) . ' VNĐ</td>
            <td></td>
        </tr>';


        $grandTotal = $quote['grand_total'];
        $trGrandTotal = '<tr class="bold">
            <td colspan="2" class="text-center">THÀNH TIỀN</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">' . formatMoney($grandTotal) . ' VNĐ</td>
            <td></td>
            <td></td>
        </tr>';

        $trWordPayment =  '<tr class="bold">
            <td colspan="11">Số tiền bằng chữ: ' . ucfirst(convert_number_to_words($grandTotal)) . '</td>
        </tr>';

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $message = "";

        ob_start();
        stylePdf();
        $company_logo = get_option('company_logo');
        $img = base_url('uploads/company/' . $company_logo);

        // Thanh Danh 3D Printing Co.,Ltd
        echo '<table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td style="width: 100%;"><span class="bold">' . get_option('invoice_company_name') . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 30%;" rowspan="1"><span><img width="125" height="75" src="' . $img . '"></span></td>
                    <td style="width: 50%" class="text-center"><span class="bold">Thanh Danh 3D Printing Co.,Ltd</span><h1 style="color: red;">BẢNG BÁO GIÁ / QUOTATION</h1></td>
                    <td style="width: 20%;"></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Địa Chỉ:</span> ' . get_option('invoice_company_address') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">MÃ BÁO GIÁ</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['reference_no'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">MST:</span> ' . get_option('company_vat') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Ngày BÁO GIÁ/ Date: </span></td>
                    <td style="width: 20%;" class="text-right"><span>' . date_format(date_create($quote['date']), "d/m/Y") . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Tài Khoản Ngân Hàng:</span> ' . get_option('bank_account') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Ngày Hết Hạn: </span></td>
                    <td style="width: 20%;" class="text-right"><span>' . (!empty($quote['expiration_date']) ? _d($quote['expiration_date']) : '') . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Tên Ngân Hàng:</span> ' . get_option('bank_name') . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Khách hàng / Customer: </span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $companyCustomer . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span><span class="bold">Người Lập Báo Giá:</span> ' . $created_by . '</span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Mã KH / Customer Code:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $codeCustomer . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Thanh toán / Payment term:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['payment_term'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Điều kiện giao hàng / Delivery term:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['delivery_term'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Tiền tệ / Exchange:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $currencies['name'] . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Tỷ giá / Rate:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . formatMoney($quote['amount_to_vnd']) . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Người Phụ trách / Sales person:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $created_by . '</span></td>
                </tr>
                <tr nobr="true">
                    <td style="width: 40%;"><span></span></td>
                    <td style="width: 20%;"></td>
                    <td style="width: 20%;"><span class="bold">Giao đến / Ship to:</span></td>
                    <td style="width: 20%;" class="text-right"><span>' . $quote['ship_to'] . '</span></td>
                </tr>
            </table>
            <br><br><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
                <thead>
                    ' . $tHead . '
                </thead>
                <tbody>
                    ' . $bodyItems . '
                <tbody>
            </table>
            </table><br><br><table class="" cellspacing="0" cellpadding="3" border="0" style="width: 100%;">
                <tr nobr="true">
                    <td class="text-right" style="width: 100%;">
                         Month ……………..Date ………………………Year
                    </td>
                </tr>
                <tr nobr="true" class="text-center bold">
                    <td style="width: 30%;"></td>
                    <td style="width: 45%;" class="text-center"><span class="bold">Khách hàng xác nhận / Vendor confirmed</span></td>
                    <td style="width: 25%;" class="text-center"><span class="bold">Ký tên / Authorized Signature</span></td>
                </tr>
            </table><br><br><br>
            <div><span class="bold">Lưu ý / Notes:</span><br>' . $quote['note'] . '</div>
        ';


        // echo '
        //     <table class="" cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
        //         <tr nobr="true">
        //             <td style="width: 100%;"><h1 class="text-center uppercase" style="font-size: 23px;">BẢNG BÁO GIÁ / QUOTATION</h1></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;" class="text-center"><span class="italic">Phiếu số: ' . $quote['reference_no'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;" class="text-center"><span class="italic">Ngày / Date: ' . date_format(date_create($quote['date']), "d/m/Y") . '</span><br></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Khách hàng / Customer:</b> <span class="bold">' . $companyCustomer . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Mã KH / Customer Code:</b> <span class="bold">' . $codeCustomer . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Thông tin chung / General Information:</b> <span class="">' . $tm_ck . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Thanh toán / Payment term:</b> <span class="">' . $quote['payment_term'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 60%;"><b>Điều kiện giao hàng / Delivery term:</b> <span class="">' . $quote['delivery_term'] . '</span></td>
        //             <td style="width: 40%;"><b>Giao đến / Ship to:</b> <span class="">' . $quote['ship_to'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Chi tiết Thanh toán / Payment detail:</b> <span class="">' . $quote['payment_detail'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Tiền tệ / Exchange:</b> <span class="">' . $currencies['name'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Tỷ giá / Rate:</b> <span class="">' . $quote['amount_to_vnd'] . '</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Người y/c / Attn.:</b> <span class="">'.$personContact.'</span></td>
        //         </tr>
        //         <tr nobr="true">
        //             <td style="width: 100%;"><b>Người Phụ trách / Sales person:</b> <span class="">'.$created_by.'</span></td>
        //         </tr>
        //     </table><br><br><table class="" cellspacing="0" cellpadding="5" border="1" style="width: 100%;">
        //         <thead>
        //             ' . $tHead . '
        //         </thead>
        //         <tbody>
        //             ' . $bodyItems . '
        //         <tbody>
        //     </table><br><br><table class="" cellspacing="0" cellpadding="3" border="0" style="width: 100%;">
        //         <tr nobr="true">
        //             <td class="text-right" style="width: 100%;">
        //                 Month ……………..Date ………………………Year
        //             </td>
        //         </tr>
        //         <tr nobr="true" class="text-center bold">
        //             <td style="width: 33%;"></td>
        //             <td style="width: 33%;" class="text-center"><span class="bold">Khách hàng xác nhận / Vendor confirmed</span></td>
        //             <td style="width: 33%;" class="text-center"><span class="bold">Ký tên / Authorized Signature</span></td>
        //         </tr>
        //     </table><br><br><br>
        //     <div>Lưu ý / Notes:<br>'.$quote['note'].'</div>
        // ';

        $data['pageCustome'] = 'quotes';
        $content = ob_get_contents();
        ob_end_clean();

        $barcode = file_get_contents(genBarcode($quote['reference_no']));
        $barcode = '<img style="width: 130px;" src="data:image/png;base64,' . base64_encode($barcode) . '"/>';

        $data['showHeader'] = 'hide';
        $data['type_print'] = 'quotes';
        $data['content'] = $content;
        $data['barcode'] = '';
        $pdf = @print_pdf_tnh($data);
        $type = $type_pdf;
        if ($type == "S") {
            return $pdf->Output(slug_it('quotes') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('quotes') . '.pdf', $type);
        }
    }

    public function email_quotes($id)
    {
        $quote = $this->quotes_model->rowQuotesById($id);
        if ($this->input->post('send')) {
            $data = [];
            $this->form_validation->set_rules('title', lang("tnh_title_email"), 'required');
            $this->form_validation->set_rules('email', lang("Email"), 'required');
            if ($this->form_validation->run() == true) {
                $title = $this->input->post('title');
                $email = $this->input->post('email');
                $content = $this->input->post('content');

                $attachment = [
                    'base64' => $this->print_pdf($id, $type_pdf = 'S'),
                    'type' => 'attachment',
                    'name' => lang('quotes') . ' ' . $quote['reference_no'],
                    'type_file' => 'application/pdf',
                ];

                $fields = [
                    'email' => $email,
                    'message' => $content,
                    'subject' => $title,
                    'attachment' => $attachment,
                ];

                $send = sendEmail($fields);
                if ($send) {
                    insertActivityLog([
                        'type_parent_obj' => 'quotes',
                        'table_obj' => 'tbl_quotes',
                        'id_obj' => $id,
                        'name_obj' => $quote['reference_no'],
                        'content' => lang('tnh_his_email_quotes') . ' [' . $quote['reference_no'] . ']',
                        'actions' => 'email'
                    ]);

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
        } else {
            $customer = $this->site_model->rowCustomer($quote['customer_id']);
            $data['quote'] = $quote;
            $data['customer'] = $customer;
            $data['id'] = $id;
            $this->load->view('email_quotes', $data);
        }
    }

    public function import_quotes_bk()
    {
        $data = [];
        if ($this->input->post()) {
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            if (empty($_FILES['file_import_quotes']['tmp_name'])) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $fullfile = $_FILES['file_import_quotes']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $extension = strtoupper(pathinfo($_FILES['file_import_quotes']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $arraydata          = array();

            $row_start = 3;
            $row_end = $highestRow < 2000 ? $highestRow : 2000;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    // $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $arraydata[$row - 1][$col] = $value;
                }
            }

            $pCode = '';
            $dataImport = [];
            $index_parent = 0;
            $row = 3;
            foreach ($arraydata as $key => $value) {
                $category = trim($value[0]);
                $product_code = trim($value[1]);
                $product_name = trim($value[2]);
                $unit = trim($value[3]);
                $height_qui_cach_sp_in = number_unformat($value[4]);
                $height_chua_bien_bo_goc = number_unformat($value[5]);
                $height_chua_bien_vuong_goc = number_unformat($value[6]);
                $height_chua_bien_vuong_tron = number_unformat($value[7]);

                $width_qui_cach_sp_in = number_unformat($value[8]);
                $width_chua_bien_bo_goc = number_unformat($value[9]);
                $width_chua_bien_vuong_goc = number_unformat($value[10]);
                $width_chua_bien_vuong_tron = number_unformat($value[11]);

                $dan_trang_height = number_unformat($value[12]);
                $dan_trang_heiht_chua_nhip_in = number_unformat($value[13]);
                $dan_trang_heiht_chua_boong_cat_be = number_unformat($value[14]);
                $dan_trang_width = number_unformat($value[15]);
                $dan_trang_weight_chua_nhip_in = number_unformat($value[16]);
                $dan_trang_weight_chua_boong_cat_be = number_unformat($value[17]);

                $cdt_item_code = trim($value[18]);
                $cdt_stages = trim($value[19]);
                $cdt_sl_mau_in = number_unformat($value[20]);

                $cdt_machine = trim($value[21]);
                $cdt_type_npl = trim($value[22]);
                $cdt_quota_bom = number_unformat($value[23]);

                $cdt_price_material = number_unformat($value[24]);

                $cdt_ms_item_code = trim($value[25]);
                $cdt_ms_stages = trim($value[26]);
                $cdt_ms_sl_mau_in = number_unformat($value[27]);

                $cdt_ms_machine = trim($value[28]);
                $cdt_ms_type_npl = trim($value[29]);
                $cdt_ms_quota_bom = number_unformat($value[30]);

                $cdt_ms_price_material = number_unformat($value[31]);

                $cdsi_stages = trim($value[32]);

                $cdsi_long_height = trim($value[33]);
                $cdsi_horizontal = trim($value[34]);

                $cdsi_so_lan_xa_van_hanh = number_unformat($value[35]);
                $cdsi_face_products = number_unformat($value[36]);
                $cdsi_don_gia_cd = number_unformat($value[37]);

                $gcvc_name = trim($value[38]);
                $gcvc_unit = trim($value[39]);
                $gcvc_price = number_unformat($value[40]);
                $gcvc_kg_con = number_unformat($value[41]);

                $chi_phi_brand = number_unformat($value[42]);
                $chi_phi_ql_nhan_cong = number_unformat($value[43]);
                $chi_phi_hp_cong_doan = number_unformat($value[44]);
                $loi_nhuan = number_unformat($value[45]);
                $ma_bang_gia_cong_doan = $value[46];

                if ((!empty($product_code) && $product_code != $pCode)) {
                    $dataImport[$index_parent]['category'] = $category;
                    $dataImport[$index_parent]['product_code'] = $product_code;
                    $dataImport[$index_parent]['product_name'] = $product_name;
                    $dataImport[$index_parent]['unit'] = $unit;
                    $dataImport[$index_parent]['height_qui_cach_sp_in'] = $height_qui_cach_sp_in;
                    $dataImport[$index_parent]['height_chua_bien_bo_goc'] = $height_chua_bien_bo_goc;
                    $dataImport[$index_parent]['height_chua_bien_vuong_goc'] = $height_chua_bien_vuong_goc;
                    $dataImport[$index_parent]['height_chua_bien_vuong_tron'] = $height_chua_bien_vuong_tron;

                    $dataImport[$index_parent]['width_qui_cach_sp_in'] = $width_qui_cach_sp_in;
                    $dataImport[$index_parent]['width_chua_bien_bo_goc'] = $width_chua_bien_bo_goc;
                    $dataImport[$index_parent]['width_chua_bien_vuong_goc'] = $width_chua_bien_vuong_goc;
                    $dataImport[$index_parent]['width_chua_bien_vuong_tron'] = $width_chua_bien_vuong_tron;

                    $dataImport[$index_parent]['dan_trang_height'] = $dan_trang_height;
                    $dataImport[$index_parent]['dan_trang_heiht_chua_nhip_in'] = $dan_trang_heiht_chua_nhip_in;
                    $dataImport[$index_parent]['dan_trang_heiht_chua_boong_cat_be'] = $dan_trang_heiht_chua_boong_cat_be;
                    $dataImport[$index_parent]['dan_trang_width'] = $dan_trang_width;
                    $dataImport[$index_parent]['dan_trang_weight_chua_nhip_in'] = $dan_trang_weight_chua_nhip_in;
                    $dataImport[$index_parent]['dan_trang_weight_chua_boong_cat_be'] = $dan_trang_weight_chua_boong_cat_be;

                    $dataImport[$index_parent]['chi_phi_brand'] = $chi_phi_brand;
                    $dataImport[$index_parent]['chi_phi_ql_nhan_cong'] = $chi_phi_ql_nhan_cong;
                    $dataImport[$index_parent]['chi_phi_hp_cong_doan'] = $chi_phi_hp_cong_doan;
                    $dataImport[$index_parent]['loi_nhuan'] = $loi_nhuan;
                    $dataImport[$index_parent]['row'] = $row;
                    $dataImport[$index_parent]['ma_bang_gia_cong_doan'] = trim($ma_bang_gia_cong_doan);

                    $parent_current = $index_parent;
                    $pCode = $product_code;
                    $index_parent++;
                }

                if (!empty($cdt_item_code)) {
                    $dataImport[$parent_current]['pre_print_stage'][] = [
                        'cdt_item_code' => $cdt_item_code,
                        'cdt_stages' => $cdt_stages,
                        'cdt_sl_mau_in' => $cdt_sl_mau_in,
                        'cdt_price_material' => $cdt_price_material,
                        'cdt_machine' => $cdt_machine,
                        'cdt_type_npl' => $cdt_type_npl,
                        'cdt_quota_bom' => $cdt_quota_bom,
                    ];
                }

                if (!empty($cdt_ms_item_code)) {
                    $dataImport[$parent_current]['pre_print_stage_backside'][] = [
                        'cdt_ms_item_code' => $cdt_ms_item_code,
                        'cdt_ms_stages' => $cdt_ms_stages,
                        'cdt_ms_sl_mau_in' => $cdt_ms_sl_mau_in,
                        'cdt_ms_price_material' => $cdt_ms_price_material,
                        'cdt_ms_machine' => $cdt_ms_machine,
                        'cdt_ms_type_npl' => $cdt_ms_type_npl,
                        'cdt_ms_quota_bom' => $cdt_ms_quota_bom,
                    ];
                }

                if (!empty($cdsi_stages)) {
                    $dataImport[$parent_current]['post_printing_stage'][] = [
                        'cdsi_stages' => $cdsi_stages,
                        'cdsi_so_lan_xa_van_hanh' => $cdsi_so_lan_xa_van_hanh,
                        'cdsi_don_gia_cd' => $cdsi_don_gia_cd,
                        'cdsi_long_height' => $cdsi_long_height,
                        'cdsi_horizontal' => $cdsi_horizontal,
                        'cdsi_face_products' => $cdsi_face_products,
                    ];
                }

                if (!empty($gcvc_name)) {
                    $dataImport[$parent_current]['outsourcing_shipping'][] = [
                        'gcvc_name' => $gcvc_name,
                        'gcvc_unit' => $gcvc_unit,
                        'gcvc_price' => $gcvc_price,
                        'gcvc_kg_con' => $gcvc_kg_con,
                    ];
                }

                $row++;
            }

            $id_customer = $this->input->post('customers');
            $id_customer = str_replace('customers__', '', $id_customer);

            $errors = '';
            $dataItems = [];
            if (!empty($dataImport)) {
                foreach ($dataImport as $key => $value) {
                    $category = $value['category'];
                    $product_code = $value['product_code'];
                    $product_name = $value['product_name'];
                    $unit = $value['unit'];
                    $_row = $value['row'];
                    $height_qui_cach_sp_in = $value['height_qui_cach_sp_in'];
                    $width_qui_cach_sp_in = $value['width_qui_cach_sp_in'];


                    $code_quote_stage = $value['ma_bang_gia_cong_doan'];

                    if (empty($product_code)) {
                        $errors .= '<div>Dòng [' . $_row . '] không có mã thành phẩm</div>';
                        continue;
                    }

                    $quote_stage_id = NULL;
                    if (!empty($code_quote_stage)) {
                        $this->db->where('code', $code_quote_stage);
                        $this->db->where('EXISTS (SELECT 1 FROM tbl_stage_quote_client WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote.id AND tbl_stage_quote_client.id_client = "' . $id_customer . '")');
                        $quote_stage_id = $this->db->get('tbl_stage_quote')->row('id');
                        if (empty($quote_stage_id)) {
                            $errors .= '<div>Dòng [' . $_row . '] không tìm thấy báo giá công đoạn của khách hàng</div>';
                        }
                    }

                    $dtProduct = $this->products_model->rowProductByCode($product_code);
                    if (empty($dtProduct)) {
                        if (empty($category)) {
                            $errors .= '<div>Dòng [' . $_row . '] không thêm được thành phẩm vì mã danh mục bỏ trống</div>';
                            continue;
                        }

                        if (empty($unit)) {
                            $errors .= '<div>Dòng [' . $_row . '] không thêm được thành phẩm vì đơn vị bỏ trống</div>';
                            continue;
                        }

                        if (empty($product_name)) {
                            $errors .= '<div>Dòng [' . $_row . '] không thêm được thành phẩm vì tên thành phẩm bỏ trống</div>';
                            continue;
                        }

                        $row_unit = $this->unit_model->rowUnitByCode($unit, 'unitid', 'where');
                        if (!empty($row_unit)) {
                            $unit_id = $row_unit['unitid'];
                        } else {
                            $unit_id = $this->unit_model->insertUnit([
                                'unit' => $unit
                            ]);
                        }

                        $row_category = $this->products_model->rowCategoryProductsByCode($category, 'id', 'where');
                        if (!empty($row_category)) {
                            $category_id = $row_category['id'];
                        } else {
                            $category_id = $this->products_model->insertCategoryProducts([
                                'code' => $category,
                                'name' => $category,
                            ]);
                        }

                        $optionProduct = [
                            'category_id' => $category_id,
                            'type_products' => 'products',
                            'name' => $product_name,
                            'code' => $product_code,
                            'unit_id' => $unit_id,
                            'conversion_unit' => $unit_id,
                            'conversion_quantity_unit' => 1,
                            'longs' => $height_qui_cach_sp_in * 10,
                            'wide' => $width_qui_cach_sp_in * 10,
                            'hand_input_code' => 1,
                            // 'quote_stage_id' => $quote_stage_id,
                        ];
                        $product_id = $this->products_model->insertProducts($optionProduct);
                        $dtProduct = $this->products_model->rowProduct($product_id);
                    } else {
                        $product_id = $dtProduct['id'];
                    }

                    $images = '';
                    if ($dtProduct['images']) {
                        $images = base_url('uploads/products/' . $dtProduct['images']);
                    }

                    $unit = $this->unit_model->rowUnit($dtProduct['unit_id']);

                    $height = $value['height_qui_cach_sp_in'];
                    $corner_boundary_height = $value['height_chua_bien_bo_goc'];
                    $perpendicular_border_height = $value['height_chua_bien_vuong_goc'];
                    $round_square_border_height = $value['height_chua_bien_vuong_tron'];
                    $product_calculation_height = $height + $corner_boundary_height + $perpendicular_border_height + $round_square_border_height;

                    $width = $value['width_qui_cach_sp_in'];
                    $corner_boundary_width = $value['width_chua_bien_bo_goc'];
                    $perpendicular_border_width = $value['width_chua_bien_vuong_goc'];
                    $round_square_border_width = $value['width_chua_bien_vuong_tron'];
                    $product_calculation_width = $width + $corner_boundary_width + $perpendicular_border_width + $round_square_border_width;

                    $product_calculation_height_width = $product_calculation_height . 'x' . $product_calculation_width . ' cm';

                    $height_layout = $value['dan_trang_height'];
                    $height_layout_print_tweezers = $value['dan_trang_heiht_chua_nhip_in'];
                    $height_layout_boong_cut = $value['dan_trang_heiht_chua_boong_cat_be'];
                    $height_layout_material_size = $height_layout - $height_layout_print_tweezers - $height_layout_boong_cut;
                    $height_layout_mode = $product_calculation_height;
                    $height_layout_quantity = 0;
                    if ($height_layout_material_size != 0) {
                        $height_layout_quantity = floor($height_layout_material_size / $height_layout_mode);
                    }

                    $width_layout = $value['dan_trang_width'];
                    $width_layout_print_tweezers = $value['dan_trang_weight_chua_nhip_in'];
                    $width_layout_boong_cut = $value['dan_trang_weight_chua_boong_cat_be'];
                    $width_layout_material_size = $width_layout - $width_layout_print_tweezers - $width_layout_boong_cut;

                    $width_layout_mode = $product_calculation_width;
                    $width_layout_quantity = 0;
                    if ($width_layout_material_size != 0) {
                        $width_layout_quantity = floor($width_layout_material_size / $width_layout_mode);
                    }

                    $height_layout_total_quantity = floor($height_layout_quantity * $width_layout_quantity);

                    $ItemsPrice = [];
                    $grandTotalSheet = 0;
                    $pre_print_stage = !empty($value['pre_print_stage']) ? $value['pre_print_stage'] : NULL;
                    if (!empty($pre_print_stage)) {
                        foreach ($pre_print_stage as $k => $val) {
                            $cdt_item_code = $val['cdt_item_code'];
                            $cdt_stages = $val['cdt_stages'];

                            $this->db->select('
                                tbl_materials.*,
                                tbl_category_items.recipe as recipe,
                                tblunits.unit as unit_name,
                            ');
                            $this->db->from('tbl_materials');
                            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id');
                            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                            $this->db->where('tbl_materials.code', $cdt_item_code);
                            $material = $this->db->get()->row_array();
                            if (empty($material)) {
                                $errors .= '<div>Dòng [' . $_row . '] NPL [' . $cdt_item_code . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $this->db->select('tbl_stages.*');
                            $this->db->from('tbl_stages');
                            $this->db->group_start();
                            $this->db->where('tbl_stages.code', $cdt_stages);
                            $this->db->or_where('tbl_stages.name', $cdt_stages);
                            $this->db->group_end();
                            $stages = $this->db->get()->row_array();
                            if (empty($stages)) {
                                $errors .= '<div>Dòng [' . $_row . '] giai đoạn [' . $cdt_stages . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $stage_id_price = $stages['id'];
                            $item_id_price = $material['id'];
                            $number_operate = 1;

                            $recipe = $material['recipe'];
                            $price_import = $material['price_import'];
                            if (!empty($val['cdt_price_material'])) {
                                $price_about = $val['cdt_price_material'];
                            } else if ($recipe) {
                                $price_sell = ($price_import * $height_layout * $width_layout) / 10000;
                                $price_about = $price_sell;
                            } else {
                                $price_about = 0;
                            }
                            $quantity_color = $val['cdt_sl_mau_in'];
                            $total_sheet = $number_operate * $price_about;

                            $machine = $val['cdt_machine'];
                            if (!empty($machine)) {
                                $this->db->select('tbl_machines.*');
                                $this->db->from('tbl_machines');
                                $this->db->group_start();
                                $this->db->where('tbl_machines.code', $machine);
                                $this->db->group_end();
                                $dtMachine = $this->db->get()->row_array();
                                if (empty($dtMachine)) {
                                    $errors .= '<div>Dòng [' . $_row . '] mã thiết bị [' . $machine . '] chưa có trong phần mềm</div>';
                                    continue;
                                }

                                $machine = $dtMachine['id'];
                            } else {
                                $machine = 0;
                            }

                            $type_npl = $val['cdt_type_npl'];
                            $quota_bom = $val['cdt_quota_bom'];

                            $ItemsPrice[] = [
                                'type_price' => 'materials',
                                'stage_id_price' => $stage_id_price,
                                'item_id_price' => $item_id_price,
                                'number_operate' => $number_operate,
                                'price_about' => $price_about,
                                'total_sheet' => $total_sheet,
                                'quantity_color' => $quantity_color,
                                'machine' => $machine,
                                'type_npl' => $type_npl,
                                'quota_bom' => $quota_bom,
                            ];

                            $grandTotalSheet += $total_sheet;
                        }
                    }

                    $itemsPriceBackside = [];
                    $pre_print_stage_backside = !empty($value['pre_print_stage_backside']) ? $value['pre_print_stage_backside'] : NULL;
                    $grandTotalSheetBackside = 0;
                    if (!empty($pre_print_stage_backside)) {
                        foreach ($pre_print_stage_backside as $k => $val) {

                            $cdt_ms_item_code = $val['cdt_ms_item_code'];
                            $cdt_ms_stages = $val['cdt_ms_stages'];

                            $this->db->select('
                                tbl_materials.*,
                                tbl_category_items.recipe as recipe,
                                tblunits.unit as unit_name,
                            ');
                            $this->db->from('tbl_materials');
                            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id');
                            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                            $this->db->where('tbl_materials.code', $cdt_ms_item_code);
                            $material = $this->db->get()->row_array();
                            if (empty($material)) {
                                $errors .= '<div>Dòng [' . $_row . '] NPL [' . $cdt_ms_item_code . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $this->db->select('tbl_stages.*');
                            $this->db->from('tbl_stages');
                            $this->db->group_start();
                            $this->db->where('tbl_stages.code', $cdt_ms_stages);
                            $this->db->or_where('tbl_stages.name', $cdt_ms_stages);
                            $this->db->group_end();
                            $stages = $this->db->get()->row_array();
                            if (empty($stages)) {
                                $errors .= '<div>Dòng [' . $_row . '] giai đoạn [' . $cdt_ms_stages . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $stage_id_price_backside = $stages['id'];
                            $item_id_price_backside = $material['id'];
                            $number_operate_backside = 1;

                            $recipe = $material['recipe'];
                            $price_import = $material['price_import'];
                            if (!empty($val['cdt_ms_price_material'])) {
                                $price_about_backside = $val['cdt_ms_price_material'];
                            } else if ($recipe) {
                                $price_sell = ($price_import * $height_layout * $width_layout) / 10000;
                                $price_about_backside = $price_sell;
                            } else {
                                $price_about_backside = 0;
                            }
                            $quantity_color_backside = $val['cdt_ms_sl_mau_in'];
                            $total_sheet_backside = $number_operate_backside * $price_about_backside;

                            $machine_backside = $val['cdt_ms_machine'];
                            if (!empty($machine_backside)) {
                                $this->db->select('tbl_machines.*');
                                $this->db->from('tbl_machines');
                                $this->db->group_start();
                                $this->db->where('tbl_machines.code', $machine_backside);
                                $this->db->group_end();
                                $dtMachine = $this->db->get()->row_array();
                                if (empty($dtMachine)) {
                                    $errors .= '<div>Dòng [' . $_row . '] mã thiết bị [' . $machine_backside . '] chưa có trong phần mềm</div>';
                                    continue;
                                }

                                $machine_backside = $dtMachine['id'];
                            } else {
                                $machine_backside = 0;
                            }

                            $type_npl_backside = $val['cdt_ms_type_npl'];
                            $quota_bom_backside = $val['cdt_ms_quota_bom'];

                            $itemsPriceBackside[] = [
                                'type_price_backside' => 'materials',
                                'stage_id_price_backside' => $stage_id_price_backside,
                                'item_id_price_backside' => $item_id_price_backside,
                                'number_operate_backside' => $number_operate_backside,
                                'price_about_backside' => $price_about_backside,
                                'total_sheet_backside' => $total_sheet_backside,
                                'quantity_color_backside' => $quantity_color_backside,
                                'machine_backside' => $machine_backside,
                                'type_npl_backside' => $type_npl_backside,
                                'quota_bom_backside' => $quota_bom_backside,
                            ];

                            $grandTotalSheetBackside += $total_sheet_backside;
                        }
                    }

                    $sum1 = $grandTotalSheetBackside + $grandTotalSheet;

                    $itemsStagesProducts = [];
                    $post_printing_stage = !empty($value['post_printing_stage']) ? $value['post_printing_stage'] : NULL;
                    $grandTotalProduct = 0;
                    if (!empty($post_printing_stage)) {
                        foreach ($post_printing_stage as $k => $val) {

                            $cdsi_stages = $val['cdsi_stages'];
                            $this->db->select('tbl_stages.*');
                            $this->db->from('tbl_stages');
                            $this->db->group_start();
                            $this->db->where('tbl_stages.code', $cdsi_stages);
                            $this->db->or_where('tbl_stages.name', $cdsi_stages);
                            $this->db->group_end();
                            $stages = $this->db->get()->row_array();
                            if (empty($stages)) {
                                $errors .= '<div>Dòng [' . $_row . '] giai đoạn [' . $cdsi_stages . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $stage_id_price_products = $stages['id'];
                            $number_operate_products = $val['cdsi_so_lan_xa_van_hanh'];
                            $price_about_products = $val['cdsi_don_gia_cd'];
                            if (empty($price_about_products)) {
                                $this->db->where('id_stage', $stage_id_price_products);
                                // $this->db->where('EXISTS (SELECT 1 FROM tbl_stage_quote_client WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote_detail.id_stage_quote AND tbl_stage_quote_client.id_client = "'.$id_customer.'")');
                                $this->db->where('id_stage_quote', $quote_stage_id);
                                $dtPrice['price_sell'] = $this->db->get('tbl_stage_quote_detail')->row('price');
                                $price_about_products = !empty($dtPrice['price_sell']) ? $dtPrice['price_sell'] : 0;

                                if ($stages['formula_m2']) {
                                    $price_about_products = ($price_about_products * $height_layout * $width_layout) / 10000;
                                }
                            }
                            $total_sheet_products = $number_operate_products * $price_about_products;
                            $long_height = $val['cdsi_long_height'];
                            $width_horizontal = $val['cdsi_horizontal'];
                            $face_products = $val['cdsi_face_products'];

                            $itemsStagesProducts[] = [
                                'type_price_products' => 'stages',
                                'stage_id_price_products' => $stage_id_price_products,
                                'item_id_price_products' => $stage_id_price_products,
                                'number_operate_products' => $number_operate_products,
                                'price_about_products' => $price_about_products,
                                'total_sheet_products' => $total_sheet_products,
                                'not_cpln' => 0,
                                'long_height' => $long_height,
                                'width_horizontal' => $width_horizontal,
                                'face_products' => $face_products,
                            ];

                            $grandTotalProduct += $total_sheet_products;
                        }
                    }


                    $sum2 = $grandTotalProduct;
                    $g1 = 0;
                    if ($height_layout_total_quantity > 0) {
                        $g1 = ($sum1 + $sum2) / $height_layout_total_quantity;
                    }

                    $total_price_child_gvc = 0;
                    $outsourcing_shipping = $value['outsourcing_shipping'];
                    $itemsGVC = [];
                    if (!empty($outsourcing_shipping)) {
                        foreach ($outsourcing_shipping as $k => $val) {

                            $unit_kg = $val['gcvc_unit'];
                            $price_gvc = $val['gcvc_price'];
                            $kg_child_gvc = $val['gcvc_kg_con'];
                            $price_child_gvc = $price_gvc * $kg_child_gvc;
                            $total_price_child_gvc += $price_child_gvc;

                            $itemsGVC[] = [
                                'type_vc' => $val['gcvc_name'],
                                'price_gvc' => $price_gvc,
                                'unit_kg' => $unit_kg,
                                'kg_child_gvc' => $kg_child_gvc,
                                'price_child_gvc' => $price_child_gvc,
                                'total_price_child_gvc' => $total_price_child_gvc,
                            ];
                        }
                    }

                    $g2 = $total_price_child_gvc;

                    $cost_of_brand = $value['chi_phi_brand'];
                    $labor_cost = $value['chi_phi_ql_nhan_cong'];
                    $loss_cost = $value['chi_phi_hp_cong_doan'];
                    $profit = $value['loi_nhuan'];

                    $total_precent = $cost_of_brand + $labor_cost + $loss_cost + $profit;
                    $g3 = ($g1 + $g2) * $total_precent / 100;
                    $g = $g1 + $g2 + $g3;

                    $dataJson = [
                        'product_quote_reference' => '',
                        'cItemsId' => $product_id . '__products',
                        'height' => $height,
                        'corner_boundary_height' => $corner_boundary_height,
                        'perpendicular_border_height' => $perpendicular_border_height,
                        'round_square_border_height' => $round_square_border_height,
                        'product_calculation_height' => $product_calculation_height,
                        'width' => $width,
                        'corner_boundary_width' => $corner_boundary_width,
                        'perpendicular_border_width' => $perpendicular_border_width,
                        'round_square_border_width' => $round_square_border_width,
                        'product_calculation_width' => $product_calculation_width,
                        'product_calculation_height_width' => $product_calculation_height_width,
                        'height_layout' => $height_layout,
                        'height_layout_print_tweezers' => $height_layout_print_tweezers,
                        'height_layout_boong_cut' => $height_layout_boong_cut,
                        'height_layout_material_size' => $height_layout_material_size,
                        'height_layout_mode' => $height_layout_mode,
                        'height_layout_quantity' => $height_layout_quantity,
                        'width_layout' => $width_layout,
                        'width_layout_print_tweezers' => $width_layout_print_tweezers,
                        'width_layout_boong_cut' => $width_layout_boong_cut,
                        'width_layout_material_size' => $width_layout_material_size,
                        'width_layout_mode' => $width_layout_mode,
                        'width_layout_quantity' => $width_layout_quantity,
                        'height_layout_total_quantity' => $height_layout_total_quantity,
                        'ItemsPrice' => $ItemsPrice,
                        'grandTotalSheet' => $grandTotalSheet,
                        'labor_cost' => $labor_cost,
                        'loss_cost' => $loss_cost,
                        'profit' => $profit,
                        'total_precent' => $total_precent,
                        'g3' => $g3,
                        'g' => $g,
                        'itemsPriceBackside' => $itemsPriceBackside,
                        'grandTotalSheetBackside' => $grandTotalSheetBackside,
                        'sum1' => $sum1,
                        'itemsStagesProducts' => $itemsStagesProducts,
                        'grandTotalProduct' => $grandTotalProduct,
                        'sum2' => $sum2,
                        'g1' => $g1,
                        'total_price_child_gvc' => $total_price_child_gvc,
                        'g2' => $g2,
                        'itemsGVC' => $itemsGVC,
                        'cost_of_brand' => $cost_of_brand
                    ];

                    // print_arrays($dataJson);

                    $dataItems[] = [
                        'items_id' => $product_id . '__products',
                        'json_item' => ['id' => $product_id . '__products', 'text' => $product_code],
                        'item_code' => $dtProduct['code'],
                        'item_name' => $dtProduct['name'],
                        'images' => $images,
                        'quote_stage_id' => $quote_stage_id,
                        'unit_name' => $unit['unit'],
                        'dataJson' => json_encode($dataJson, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        'g' => $g,
                    ];
                }
            }

            $data['result'] = 1;
            $data['message'] = lang('success');
            $data['errors'] = $errors;
            $data['dataItems'] = $dataItems;
            echo json_encode($data);
            die;
        }
    }



    function search_quote_stage($customer = '', $id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        //		$customer = $this->input->get('customer', TRUE);
        $limit = get_option('select2_limit');
        //		if(!empty($customer)) //Tạm đóng
        {
            $customer = explode('__', $customer);
            $this->db->select('id, code, name, CONCAT(name, " (", code, ")") as text');
            if (!empty($term)) {
                $this->db->group_start();
                $this->db->like('code', $term);
                $this->db->or_like('name', $term);
                $this->db->group_end();
            }
            $this->db->limit($limit);
            //			$this->db->where('EXISTS (
            //				SELECT 1
            //				FROM tbl_stage_quote_client
            //				WHERE id_stage_quote = tbl_stage_quote.id
            //				AND tbl_stage_quote_client.id_client = "'.$customer[1].'"
            //				LIMIT 1
            //			)', false, false); //Tạm đóng
            $items = $this->db->get('tbl_stage_quote')->result_array();
            $data['results'] = [
                [
                    'text' => lang('Bảng giá công đoạn'),
                    'children' => $items,
                ]
            ];
        }

        if ($id) {
            $this->db->where('id', $id);
            $items = $this->db->get('tbl_stage_quote')->row_array();
            $data['row'] = ['id' => $items['id'], 'text' => $items['name'] . ' (' . $items['code'] . ')'];
        }
        echo json_encode($data);
    }

    function exportExcelQuotes($id)
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }

        $excel = cloumns_excel();

        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $rowBegin = 1;
        $iExcel = -1;

        $where = getWhereQuotes([], true);
        $quote = $this->quotes_model->getQuotesById($id, $where);
        $address_delivery = $this->site_model->rowShippingClient($quote['address_delivery_id']);
        $companyCustomer = '';
        $addressCompany = '';
        $emailCompany = '';

        $personContact = '';
        $phoneContact = '';
        $emailContact = '';

        $customer = $this->clients_model->rowCustomer($quote['customer_id']);
        $codeCustomer = $customer['zcode'];
        $companyCustomer = $customer['company_short'];

        $contact = $this->site_model->rowContact($quote['person_contact_id']);
        $personContact = $contact['firstname'];
        $phoneContact = $contact['phonenumber'];
        $emailContact = $contact['email'];
        $created_by = get_staff_full_name($quote['created_by']);
        $staff_create = get_table_where('tblstaff', ['staffid' => $quote['created_by']], '', 'row_array');

        $rowBegin = 0;
        $rowBegin++;
        $iExcel = -1;

        $company_logo = get_option('company_logo');
        $invoice_company_name = get_option('invoice_company_name');
        $invoice_company_address = get_option('invoice_company_address');
        $invoice_company_phonenumber = get_option('invoice_company_phonenumber');
        $fax_company = get_option('fax_company');
        $email_company = get_option('email_company');
        $company_vat = get_option('company_vat');

        if (file_exists('./uploads/company/' . $company_logo)) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath('./uploads/company/' . $company_logo);
            $objDrawing->setCoordinates('P1');
            $objDrawing->setWidth(100);
            $objDrawing->setHeight(100);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            // $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(60);
        }

        $objPHPExcel->getActiveSheet()->setCellValue('P6', 'Time for success');
        $objPHPExcel->getActiveSheet()->setCellValue('AA1', $invoice_company_name);
        $objPHPExcel->getActiveSheet()->getStyle('AA1')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 14,
            )
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('AA2', 'Địa chỉ: ');
        $objPHPExcel->getActiveSheet()->setCellValue('AA3', 'MST: ');
        $objPHPExcel->getActiveSheet()->setCellValue('AA4', 'Tài Khoản NH (VNĐ): ');
        $objPHPExcel->getActiveSheet()->setCellValue('AA5', 'Tài Khoản NH (USD): ');
        $objPHPExcel->getActiveSheet()->getStyle('AA2:AA5')->applyFromArray([
            'font' => array(
                'bold' => true,
            )
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('AF2', $invoice_company_address);
        $objPHPExcel->getActiveSheet()->setCellValue('AF3', $company_vat);
        $account_vnd = '060 091 996 998 - Tại Ngân hàng Sacombank - PGD Lũy Bán Bích';
        $account_usd = '3111 0370 00 9201 - Ngân hàng TMCP Đầu Tư Và Phát Triển Việt Nam - Chi Nhánh Tây Sài Gòn';
        $objPHPExcel->getActiveSheet()->setCellValue('AF4', $account_vnd);
        $objPHPExcel->getActiveSheet()->setCellValue('AF5', $account_usd);

        $objPHPExcel->getActiveSheet()->setCellValue('W7', 'BẢNG BÁO GIÁ/ QUOTATION');
        $objPHPExcel->getActiveSheet()->getStyle('W7')->applyFromArray([
            'font' => array(
                'bold' => true,
                'size' => 16,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->mergeCells('W7:BA9');

        $objPHPExcel->getActiveSheet()->setCellValue('BD7', 'Loại Sản Phẩm');
        $objPHPExcel->getActiveSheet()->mergeCells('BD7:BP7');
        $objPHPExcel->getActiveSheet()->setCellValue('BD8', 'Hangtag');
        $objPHPExcel->getActiveSheet()->setCellValue('BG8', 'Thay đổi');
        $objPHPExcel->getActiveSheet()->mergeCells('BG8:BI8');
        $objPHPExcel->getActiveSheet()->setCellValue('BK8', 'Cố định');
        $objPHPExcel->getActiveSheet()->mergeCells('BK8:BM8');
        $objPHPExcel->getActiveSheet()->getStyle('BD7:BP9')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('B10', 'Khách hàng:');
        $objPHPExcel->getActiveSheet()->setCellValue('B11', 'Người yêu cầu báo giá:');
        $objPHPExcel->getActiveSheet()->setCellValue('B12', 'Địa chỉ công ty:');
        $objPHPExcel->getActiveSheet()->setCellValue('B13', 'Địa điểm giao hàng:');
        $objPHPExcel->getActiveSheet()->setCellValue('B14', 'Người nhận hàng:');
        $objPHPExcel->getActiveSheet()->getStyle('B10:B14')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('H10', $companyCustomer);
        // $objPHPExcel->getActiveSheet()->setCellValue('H11', $created_by);
        $objPHPExcel->getActiveSheet()->setCellValue('H11', $personContact);
        $objPHPExcel->getActiveSheet()->setCellValue('H12', $customer['address']);
        $objPHPExcel->getActiveSheet()->setCellValue('H13', $quote['ship_to']);
        $objPHPExcel->getActiveSheet()->setCellValue('H14', $personContact);

        $objPHPExcel->getActiveSheet()->setCellValue('L11', 'Email:');
        $objPHPExcel->getActiveSheet()->getStyle('L11')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);
        // $objPHPExcel->getActiveSheet()->setCellValue('O11', $staff_create['email']);
        $objPHPExcel->getActiveSheet()->setCellValue('O11', $emailContact);
        $objPHPExcel->getActiveSheet()->setCellValue('L14', 'Email:');
        $objPHPExcel->getActiveSheet()->getStyle('L14')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);
        // $objPHPExcel->getActiveSheet()->setCellValue('O14', $staff_create['email']);
        $objPHPExcel->getActiveSheet()->setCellValue('O14', $emailContact);

        $objPHPExcel->getActiveSheet()->setCellValue('V11', 'Số liên hệ :');
        $objPHPExcel->getActiveSheet()->getStyle('V11')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);
        // $objPHPExcel->getActiveSheet()->setCellValue('Y11', $staff_create['phonenumber']);
        $objPHPExcel->getActiveSheet()->setCellValue('Y11', $phoneContact);
        $objPHPExcel->getActiveSheet()->mergeCells('Y11:AC11');

        $objPHPExcel->getActiveSheet()->setCellValue('V14', 'Số liên hệ :');
        $objPHPExcel->getActiveSheet()->getStyle('V14')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);
        // $objPHPExcel->getActiveSheet()->setCellValue('Y14', $staff_create['phonenumber']);
        $objPHPExcel->getActiveSheet()->setCellValue('Y14', $phoneContact);
        $objPHPExcel->getActiveSheet()->mergeCells('Y14:AC14');

        $objPHPExcel->getActiveSheet()->setCellValue('AK10', 'Mã báo giá:');
        $objPHPExcel->getActiveSheet()->setCellValue('AK11', 'Người thực hiện báo giá:');
        $objPHPExcel->getActiveSheet()->setCellValue('AK12', 'Ngày báo giá:');
        $objPHPExcel->getActiveSheet()->setCellValue('AK13', 'Ngày hết hạn:');
        $objPHPExcel->getActiveSheet()->setCellValue('AK14', 'Hình thức thanh toán:');
        $objPHPExcel->getActiveSheet()->getStyle('AK10:AK14')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $objPHPExcel->getActiveSheet()->setCellValue('AQ10', $quote['reference_no']);
        $objPHPExcel->getActiveSheet()->setCellValue('AQ11', $created_by);
        $objPHPExcel->getActiveSheet()->setCellValue('AQ12', _d($quote['date']));
        $objPHPExcel->getActiveSheet()->setCellValue('AQ13', (!empty($quote['expiration_date']) ? _d($quote['expiration_date']) : ''));
        $objPHPExcel->getActiveSheet()->setCellValue('AQ14', $quote['payment_term']);

        $iExcel = 0;
        $rowBegin = 19;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('STT'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel] . ($rowBegin + 2));

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('BRAND(Nhãn Hiệu)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin + 2));

        $iExcel = $iExcel + 3;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Tên Gọi Khách Hàng'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin + 2));

        $iExcel = $iExcel + 3;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Item Code-Giá Khách Hàng'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin + 2));

        $iExcel = $iExcel + 3;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Photo(Hình ảnh)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin + 2));

        $iExcel = $iExcel + 3;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Flat Size(Kích thước)(mm)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin + 2));

        $iExcel = $iExcel + 3;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thanh Danh item code(Mã Thành Danh)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 6] . ($rowBegin + 2));

        $iExcel = $iExcel + 6;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Thanh Danh item name(Tên Thành Danh)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 6] . ($rowBegin + 2));

        $iExcel = $iExcel + 6;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('UoM(Đơn vị tính)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 1] . ($rowBegin + 2));
        $iExcel++;
        $this->db->select('tbl_quote_items.moq, tbl_quote_items.moq_to');
        $this->db->from('tbl_quote_items');
        $this->db->where('tbl_quote_items.quote_id', $id);
        $this->db->order_by('tbl_quote_items.moq ASC, tbl_quote_items.moq_to ASC');
        $this->db->group_by('tbl_quote_items.moq, tbl_quote_items.moq_to');
        $dtMoq =  $this->db->get()->result_array();
        // $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel].$rowBegin, 'acd');
        if (!empty($dtMoq)) {
            foreach ($dtMoq as $kMoq => $vMoq) {
                if ($vMoq['moq'] % 1000 == 0 && $vMoq['moq'] >= 1000) {
                    $moq_k = ($vMoq['moq'] / 1000) . 'K';
                } else {
                    $moq_k = $vMoq['moq'];
                }

                if ($vMoq['moq_to'] % 1000 == 0 && $vMoq['moq_to'] >= 1000) {
                    $moq_to_k = ($vMoq['moq_to'] / 1000) . 'K';
                } else {
                    $moq_to_k = $vMoq['moq_to'];
                }
                $strMoq = 'MOQ ' . $moq_k . ' - ' . $moq_to_k . '';

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $strMoq);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 8] . ($rowBegin + 1));

                $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . ($rowBegin + 2), $codeCustomer);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . ($rowBegin + 2) . ':' . $excel[$iExcel + 2] . ($rowBegin + 2));

                $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel + 3] . ($rowBegin + 2), lang('Thành Danh'));
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel + 3] . ($rowBegin + 2) . ':' . $excel[$iExcel + 5] . ($rowBegin + 2));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel + 6] . ($rowBegin + 2), lang('%'));
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel + 6] . ($rowBegin + 2) . ':' . $excel[$iExcel + 8] . ($rowBegin + 2));
                $iExcel = $iExcel + 8;
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Leadtime(thời gian xử lý)'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 1] . ($rowBegin + 2));

        $iExcel = $iExcel + 1;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, lang('Yêu cầu đặc biệt'));
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 4] . ($rowBegin + 2));
        $iExcel = $iExcel + 4;
        $objPHPExcel->getActiveSheet()->getStyle('B19:' . $excel[++$iExcel] . ($rowBegin + 2))->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);

        $this->db->select('
            tbl_quote_items.item_id, 
            tbl_quote_items.type_item, 
            tbl_quote_items.unit_price, 
            tbl_quote_items.technical_explanation, 
            tbl_quote_items.note_item,
            tbl_quote_items.lead_time,
            tbl_quote_items.unit_price as unit_price,
            tbl_quote_items.discount_precent_item as discount_precent_item,
            tbl_products.code as item_code, 
            tbl_products.name as item_name, 
            tbl_products.product_code_customer as product_code_customer,
            tbl_products.product_name_customer as product_name_customer,
            tbl_products.brand as brand,
            tblunits.unit as unit_name,
            tblsize.name as size_name,
            GROUP_CONCAT(distinct CONCAT(coalesce(tbl_quote_items.moq, 0), "__", coalesce(tbl_quote_items.moq_to, 0))) as moq_range,
            tbl_products.images as images
        ', false);
        $this->db->from('tbl_quote_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
        $this->db->where('tbl_quote_items.quote_id', $id);
        $this->db->where('tbl_quote_items.type_item', 'products');
        $this->db->group_by('tbl_quote_items.item_id, tbl_quote_items.type_item, tbl_quote_items.unit_price, tbl_quote_items.technical_explanation, tbl_quote_items.note_item, tbl_quote_items.lead_time');
        $this->db->order_by('tbl_quote_items.item_id ASC');
        $items = $this->db->get()->result_array();
        $rowBegin = $rowBegin + 2;
        $stt = 0;
        if ($items) {
            foreach ($items as $key => $value) {
                $rowBegin++;
                $stt++;

                $iExcel = 0;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $stt);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel] . ($rowBegin));

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['brand']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin));

                $iExcel = $iExcel + 3;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['product_name_customer']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin));

                $iExcel = $iExcel + 3;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['product_code_customer']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin));

                $iExcel = $iExcel + 3;

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, '');
                if ($value['images'] && file_exists('./uploads/products/' . $value['images'])) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath('./uploads/products/' . $value['images']);
                    $objDrawing->setCoordinates($excel[$iExcel] . $rowBegin);
                    $objDrawing->setWidth(50);
                    $objDrawing->setHeight(50);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin));

                $iExcel = $iExcel + 3;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['size_name']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 3] . ($rowBegin));

                $iExcel = $iExcel + 3;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['item_code']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 6] . ($rowBegin));

                $iExcel = $iExcel + 6;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['item_name']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 6] . ($rowBegin));

                $iExcel = $iExcel + 6;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['unit_name']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 1] . ($rowBegin));
                $iExcel++;

                $r_pac = $value['unit_price'];
                if ($value['discount_precent_item'] > 0) {
                    $r_pac = $r_pac - $r_pac * $value['discount_precent_item'] / 100;
                }

                $thanh_danh = 0;
                if ($quote['amount_to_vnd'] > 0) {
                    $thanh_danh = $r_pac / $quote['amount_to_vnd'];
                }
                $percent = 0;
                if ($thanh_danh > 0) {
                    $percent = $r_pac / $thanh_danh * 100;
                }

                $moq_range = $value['moq_range'];
                $arrMoqRange = [];
                if (!empty($moq_range)) {
                    $arrMoqRange = explode(',', $moq_range);
                    if (!empty($arrMoqRange)) {
                        foreach ($arrMoqRange as $kMR => $vMR) {
                            $arrVMR = explode('__', $vMR);
                            $arr = [];
                            $arr['moq'] = $arrVMR[0];
                            $arr['moq_to'] = $arrVMR[1];
                            $arrMoqRange[$kMR] = $arr;
                        }
                    }
                }
                // print_arrays($arrMoqRange, $dtMoq);
                foreach ($dtMoq as $kMoq => $vMoq) {
                    $flagCheck = false;
                    foreach ($arrMoqRange as $kMR => $vMR) {
                        if ($vMoq['moq'] == $vMR['moq'] && $vMoq['moq_to'] == $vMR['moq_to']) {
                            $flagCheck = true;
                        }
                    }

                    $temp_r_pac = 0;
                    $temp_thanh_danh = 0;
                    $temp_percent = 0;
                    if ($flagCheck) {
                        $temp_r_pac = $r_pac;
                        $temp_thanh_danh = $thanh_danh;
                        $temp_percent = $percent;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . ($rowBegin), $temp_r_pac)->getStyle($excel[$iExcel] . ($rowBegin))->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . ($rowBegin) . ':' . $excel[$iExcel + 2] . ($rowBegin));

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel + 3] . ($rowBegin), $temp_thanh_danh)->getStyle($excel[$iExcel + 3] . ($rowBegin))->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel + 3] . ($rowBegin) . ':' . $excel[$iExcel + 5] . ($rowBegin));

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel + 6] . ($rowBegin), $temp_percent)->getStyle($excel[$iExcel + 6] . ($rowBegin))->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel + 6] . ($rowBegin) . ':' . $excel[$iExcel + 8] . ($rowBegin));
                    $iExcel = $iExcel + 8;
                }

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['lead_time']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 1] . ($rowBegin));

                $iExcel = $iExcel + 1;
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowBegin, $value['technical_explanation']);
                $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . $rowBegin . ':' . $excel[$iExcel + 4] . ($rowBegin));
                $iExcel = $iExcel + 4;
            }
        }
        // print_arrays($items);
        $iExcel = $iExcel - 1;
        $objPHPExcel->getActiveSheet()->getStyle('B19:' . $excel[++$iExcel] . ($rowBegin))->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $objPHPExcel->getActiveSheet()->getStyle('B19:' . $excel[$iExcel] . ($rowBegin))->getAlignment()->setWrapText(true);

        $iExcel = $iExcel - 15;
        $iExcelSign = $iExcel;
        $rowBegin++;
        $rowBegin++;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . $rowBegin, 'Ngày');
        $iExcel = $iExcel + 4;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . $rowBegin, 'Tháng');
        $iExcel = $iExcel + 4;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . $rowBegin, 'Năm');

        $rowBegin++;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcelSign] . $rowBegin, 'Ký duyệt');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcelSign] . $rowBegin . ':' . $excel[$iExcelSign + 10] . ($rowBegin));
        $objPHPExcel->getActiveSheet()->getStyle($excel[$iExcelSign] . ($rowBegin - 1) . ':' . $excel[$iExcelSign + 10] . ($rowBegin))->applyFromArray([
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array(
                'bold' => true,
            ),
        ]);

        $rowBegin++;
        $rowBegin++;
        $rowBegin++;
        $rowBegin++;
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcelSign] . $rowBegin, 'Đại diện công ty Thành Danh');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcelSign] . $rowBegin . ':' . $excel[$iExcelSign + 10] . ($rowBegin));
        $objPHPExcel->getActiveSheet()->getStyle($excel[$iExcelSign] . $rowBegin)->applyFromArray([
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array(
                'bold' => true,
            ),
        ]);

        foreach ($excel as $key => $value) {
            if ($value == 'A') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(1);
            } else if ($value == 'BD') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(10);
            } else {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(4);
            }
        }

        $title = 'quotes';
        $filename = $title . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function searchQR()
    {
        $code = $this->input->post('code');
        $checkCode = explode('||', $code)[0];
        if ($checkCode != 'products') {
            $response['items'] = [];
            $response['result'] = 0;
            $response['message'] = lang('Chỉ có thể chọn thành phẩm');
            echo json_encode($response);
            die;
        }
        $response = $this->products_model->searchQR($code);
        echo json_encode($response);
    }

    public function import_quotes()
    {
        $data = [];
        if ($this->input->post()) {
            include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
            $this->load->library('PHPExcel');

            if (empty($_FILES['file_import_quotes']['tmp_name'])) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $fullfile = $_FILES['file_import_quotes']['tmp_name'];
            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $extension = strtoupper(pathinfo($_FILES['file_import_quotes']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS') {
                $data['result'] = 0;
                $data['message'] = lang('tnh_not_format_excel');
                echo json_encode($data);
                return;
            }

            if (empty($fullfile)) {
                $data['result'] = 0;
                $data['message'] = lang('tnh_file_not_required');
                echo json_encode($data);
                return;
            }

            $inputFileType  = PHPExcel_IOFactory::identify($fullfile);
            $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load("$fullfile");

            $total_sheets = $objPHPExcel->getSheetCount();

            $allSheetName       = $objPHPExcel->getSheetNames();
            $objWorksheet       = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow         = $objWorksheet->getHighestRow();
            $highestColumn      = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $arraydata          = array();

            $row_start = 4;
            $row_end = $highestRow < 2000 ? $highestRow : 2000;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    // $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $arraydata[$row - 1][$col] = $value;
                }
            }

            $arrStages = [];
            for ($row = 1; $row <= 1; ++$row) {
                for ($col = 128; $col < $highestColumnIndex; ++$col) {
                    $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                    $arrStages[$row - 1][$col] = $value;
                }
            }

            $errors = '';
            if (!empty($arrStages)) {
                $arrStages = $arrStages[0];
                foreach ($arrStages as $key => $value) {
                    if (empty($value)) continue;
                    $stage_code = $value;
                    $dtStage = $this->site_model->getStagesByCodeName($stage_code);
                    if (empty($dtStage)) {
                        $errors .= '<div class="text-danger">Công đoạn động [' . $stage_code . '] chưa có trong phần mềm</div>';
                        continue;
                    } else {
                        $arrStages[$key] = $dtStage;
                    }
                }
            }

            if (!empty($errors)) {
                $data['result'] = 1;
                $data['message'] = lang('Công đoạn động không tìm thấy trong phần mềm');
                $data['errors'] = $errors;
                echo json_encode($data);
                return;
            }

            $pCode = '';
            $dataImport = [];
            $index_parent = 0;
            $row = 4;
            foreach ($arraydata as $key => $value) {
                $category = trim($value[0]);
                $product_code = trim($value[1]);
                $product_name = trim($value[2]);
                $unit = trim($value[3]);
                $height_qui_cach_sp_in = number_unformat($value[4]);
                $height_chua_bien_bo_goc = number_unformat($value[5]);
                $height_chua_bien_vuong_goc = number_unformat($value[6]);
                $height_chua_bien_vuong_tron = number_unformat($value[7]);

                $width_qui_cach_sp_in = number_unformat($value[8]);
                $width_chua_bien_bo_goc = number_unformat($value[9]);
                $width_chua_bien_vuong_goc = number_unformat($value[10]);
                $width_chua_bien_vuong_tron = number_unformat($value[11]);

                $dan_trang_height = number_unformat($value[12]);
                $dan_trang_heiht_chua_nhip_in = number_unformat($value[13]);
                $dan_trang_heiht_chua_boong_cat_be = number_unformat($value[14]);
                $dan_trang_width = number_unformat($value[15]);
                $dan_trang_weight_chua_nhip_in = number_unformat($value[16]);
                $dan_trang_weight_chua_boong_cat_be = number_unformat($value[17]);

                $cdt_item_code = trim($value[18]);
                $cdt_stages = trim($value[19]);
                $cdt_sl_mau_in = number_unformat($value[20]);

                $cdt_machine = trim($value[21]);
                $cdt_type_npl = trim($value[22]);
                $cdt_quota_bom = number_unformat($value[23]);

                $cdt_price_material = number_unformat($value[24]);

                $cdt_ms_item_code = trim($value[25]);
                $cdt_ms_stages = trim($value[26]);
                $cdt_ms_sl_mau_in = number_unformat($value[27]);

                $cdt_ms_machine = trim($value[28]);
                $cdt_ms_type_npl = trim($value[29]);
                $cdt_ms_quota_bom = number_unformat($value[30]);

                $cdt_ms_price_material = number_unformat($value[31]);

                $cdsi_stages = trim($value[32]);

                $cdsi_long_height = trim($value[33]);
                $cdsi_horizontal = trim($value[34]);

                $cdsi_so_lan_xa_van_hanh = number_unformat($value[35]);
                $cdsi_face_products = number_unformat($value[36]);
                $cdsi_don_gia_cd = number_unformat($value[37]);

                $gcvc_name = trim($value[38]);
                $gcvc_unit = trim($value[39]);
                $gcvc_price = number_unformat($value[40]);
                $gcvc_kg_con = number_unformat($value[41]);

                // $chi_phi_brand = number_unformat($value[42]);
                // $chi_phi_ql_nhan_cong = number_unformat($value[43]);
                // $chi_phi_hp_cong_doan = number_unformat($value[44]);
                // $loi_nhuan = number_unformat($value[45]);
                // $ma_bang_gia_cong_doan = $value[46];

                //Thông tin NPL
                $npl_ma_npl = trim($value[42]);
                $npl_ten_npl = trim($value[43]);
                $npl_height_1 = number_unformat($value[44]);
                $npl_width_1 = number_unformat($value[45]);
                $npl_dv_do_san_pham_1 = trim($value[46]);
                $npl_dv_tinh_san_pham_1 = trim($value[47]);
                $npl_height_2 = number_unformat($value[48]);
                $npl_chua_bien_1 = number_unformat($value[49]);
                $npl_width_2 = number_unformat($value[50]);
                $npl_chua_bien_2 = number_unformat($value[51]);
                $npl_dv_do_san_pham_2 = trim($value[52]);
                $npl_dv_tinh_san_pham_2 = trim($value[53]);
                $npl_chua_nhip = number_unformat($value[54]);
                $npl_chua_xa_width = number_unformat($value[55]);
                $npl_chua_xa_height = number_unformat($value[56]);
                $npl_tong_so_con_width = number_unformat($value[57]);
                $npl_tong_so_con_height = number_unformat($value[58]);
                $npl_tong_so_con_to = number_unformat($value[59]);
                $npl_gia_to = number_unformat($value[60]);
                $npl_gia_xlt = number_unformat($value[61]);
                $npl_tong_tien_to = number_unformat($value[62]);

                //Dàn trang
                $dt_loai = number_unformat($value[63]);
                $dt_ma_thiet_bi_cong_doan = trim($value[64]);
                $dt_ten_thiet_bi_cong_doan = trim($value[65]);
                $dt_height = number_unformat($value[66]);
                $dt_width = number_unformat($value[67]);
                $dt_so_con_to_in_1 = number_unformat($value[68]);
                $dt_so_luong_mau_in_1 = number_unformat($value[69]);
                $dt_loai_npl_1 = trim($value[70]);
                $dt_so_luong_kem_1 = number_unformat($value[71]);
                $dt_so_lan_van_hanh_to_1 = number_unformat($value[72]);
                $dt_dinh_muc_kem_su_dung_1 = number_unformat($value[73]);
                $dt_dinh_muc_nang_suat_ctp_1 = number_unformat($value[74]);
                $dt_so_con_to_in_2 = number_unformat($value[75]);
                $dt_so_luong_mau_in_2 = number_unformat($value[76]);
                $dt_loai_npl_2 = trim($value[77]);
                $dt_so_luong_kem_2 = number_unformat($value[78]);
                $dt_so_lan_van_hanh_to_2 = number_unformat($value[79]);
                $dt_dinh_muc_kem_su_dung_2 = number_unformat($value[80]);
                $dt_dinh_muc_nang_suat_ctp_2 = number_unformat($value[81]);
                $dt_tong_so_lan_van_hanh_to = number_unformat($value[82]);
                $dt_tong_so_npl_to = number_unformat($value[83]);
                $dt_gia_to = number_unformat($value[84]);
                $dt_tong_gia_to = number_unformat($value[85]);

                //công đoạn kiểm
                $k_nhom_cong_doan = trim($value[86]);
                $k_height = number_unformat($value[87]);
                $k_width = number_unformat($value[88]);
                $k_don_vi_tinh = trim($value[89]);
                $k_loai_kiem_1 = trim($value[90]);
                $k_so_lan_van_hanh_mat_1 = number_unformat($value[91]);
                $k_dinh_muc_nang_suat_1 = number_unformat($value[92]);
                $k_loai_kiem_2 = trim($value[93]);
                $k_so_lan_van_hanh_mat_2 = number_unformat($value[94]);
                $k_dinh_muc_nang_suat_2 = number_unformat($value[95]);

                //Công đoạn phân đơn dán tem
                $pd_nhom_cong_doan = trim($value[96]);
                $pd_height = number_unformat($value[97]);
                $pd_width = number_unformat($value[98]);
                $pd_cao_day = number_unformat($value[99]);
                $pd_don_vi_tinh = trim($value[100]);
                $pd_so_con_kien = number_unformat($value[101]);
                $pd_dinh_muc_kien = number_unformat($value[102]);
                $pd_dinh_muc_nang_suat = number_unformat($value[103]);
                $pd_loai_bao_bi_dong_goi = trim($value[104]);
                $pd_loai_tem_dan = trim($value[105]);
                $pd_tong_so_tem_dan = number_unformat($value[106]);
                $pd_tong_so_kien_dan = number_unformat($value[107]);

                //Công đoạn mở phiếu giao hàng
                $gh_nhom_cong_doan = trim($value[108]);
                $gh_height = number_unformat($value[109]);
                $gh_width = number_unformat($value[110]);
                $gh_cao_day = number_unformat($value[111]);
                $gh_don_vi_tinh = trim($value[112]);
                $gh_so_con_kien = number_unformat($value[113]);
                $gh_dinh_muc_kien = number_unformat($value[114]);
                $gh_dinh_muc_nang_suat = number_unformat($value[115]);
                $gh_loai_bao_bi_dong_goi = trim($value[116]);
                $gh_loai_tem_dan = trim($value[117]);
                $gh_tong_so_tem_dan = number_unformat($value[118]);
                $gh_tong_so_kien_dan = number_unformat($value[119]);

                //Công đoạn điều xe
                $dx_nhom_cong_doan = trim($value[120]);
                $dx_loai_phuong_tien = trim($value[121]);
                $dx_don_vi_tinh = trim($value[122]);
                $dx_don_gia_giao_hang = trim($value[123]);
                $dx_tong_kien = number_unformat($value[124]);
                $dx_thanh_tien = number_unformat($value[125]);
                $dx_nha_cung_cap = trim($value[126]);
                $dx_dia_chi_giao_hàng = trim($value[127]);

                //
                if ((!empty($product_code) && $product_code != $pCode)) {
                    $dataImport[$index_parent]['category'] = $category;
                    $dataImport[$index_parent]['product_code'] = $product_code;
                    $dataImport[$index_parent]['product_name'] = $product_name;
                    $dataImport[$index_parent]['unit'] = $unit;
                    $dataImport[$index_parent]['height_qui_cach_sp_in'] = $height_qui_cach_sp_in;
                    $dataImport[$index_parent]['height_chua_bien_bo_goc'] = $height_chua_bien_bo_goc;
                    $dataImport[$index_parent]['height_chua_bien_vuong_goc'] = $height_chua_bien_vuong_goc;
                    $dataImport[$index_parent]['height_chua_bien_vuong_tron'] = $height_chua_bien_vuong_tron;

                    $dataImport[$index_parent]['width_qui_cach_sp_in'] = $width_qui_cach_sp_in;
                    $dataImport[$index_parent]['width_chua_bien_bo_goc'] = $width_chua_bien_bo_goc;
                    $dataImport[$index_parent]['width_chua_bien_vuong_goc'] = $width_chua_bien_vuong_goc;
                    $dataImport[$index_parent]['width_chua_bien_vuong_tron'] = $width_chua_bien_vuong_tron;

                    $dataImport[$index_parent]['dan_trang_height'] = $dan_trang_height;
                    $dataImport[$index_parent]['dan_trang_heiht_chua_nhip_in'] = $dan_trang_heiht_chua_nhip_in;
                    $dataImport[$index_parent]['dan_trang_heiht_chua_boong_cat_be'] = $dan_trang_heiht_chua_boong_cat_be;
                    $dataImport[$index_parent]['dan_trang_width'] = $dan_trang_width;
                    $dataImport[$index_parent]['dan_trang_weight_chua_nhip_in'] = $dan_trang_weight_chua_nhip_in;
                    $dataImport[$index_parent]['dan_trang_weight_chua_boong_cat_be'] = $dan_trang_weight_chua_boong_cat_be;

                    $dataImport[$index_parent]['chi_phi_brand'] = $chi_phi_brand;
                    $dataImport[$index_parent]['chi_phi_ql_nhan_cong'] = $chi_phi_ql_nhan_cong;
                    $dataImport[$index_parent]['chi_phi_hp_cong_doan'] = $chi_phi_hp_cong_doan;
                    $dataImport[$index_parent]['loi_nhuan'] = $loi_nhuan;
                    $dataImport[$index_parent]['row'] = $row;
                    $dataImport[$index_parent]['ma_bang_gia_cong_doan'] = trim($ma_bang_gia_cong_doan);

                    $parent_current = $index_parent;
                    $pCode = $product_code;
                    $index_parent++;
                }

                if (!empty($cdt_item_code)) {
                    $dataImport[$parent_current]['pre_print_stage'][] = [
                        'cdt_item_code' => $cdt_item_code,
                        'cdt_stages' => $cdt_stages,
                        'cdt_sl_mau_in' => $cdt_sl_mau_in,
                        'cdt_price_material' => $cdt_price_material,
                        'cdt_machine' => $cdt_machine,
                        'cdt_type_npl' => $cdt_type_npl,
                        'cdt_quota_bom' => $cdt_quota_bom,
                    ];
                }

                if (!empty($cdt_ms_item_code)) {
                    $dataImport[$parent_current]['pre_print_stage_backside'][] = [
                        'cdt_ms_item_code' => $cdt_ms_item_code,
                        'cdt_ms_stages' => $cdt_ms_stages,
                        'cdt_ms_sl_mau_in' => $cdt_ms_sl_mau_in,
                        'cdt_ms_price_material' => $cdt_ms_price_material,
                        'cdt_ms_machine' => $cdt_ms_machine,
                        'cdt_ms_type_npl' => $cdt_ms_type_npl,
                        'cdt_ms_quota_bom' => $cdt_ms_quota_bom,
                    ];
                }

                if (!empty($cdsi_stages)) {
                    $dataImport[$parent_current]['post_printing_stage'][] = [
                        'cdsi_stages' => $cdsi_stages,
                        'cdsi_so_lan_xa_van_hanh' => $cdsi_so_lan_xa_van_hanh,
                        'cdsi_don_gia_cd' => $cdsi_don_gia_cd,
                        'cdsi_long_height' => $cdsi_long_height,
                        'cdsi_horizontal' => $cdsi_horizontal,
                        'cdsi_face_products' => $cdsi_face_products,
                    ];
                }

                if (!empty($gcvc_name)) {
                    $dataImport[$parent_current]['outsourcing_shipping'][] = [
                        'gcvc_name' => $gcvc_name,
                        'gcvc_unit' => $gcvc_unit,
                        'gcvc_price' => $gcvc_price,
                        'gcvc_kg_con' => $gcvc_kg_con,
                    ];
                }

                //
                if (!empty($npl_ma_npl)) {
                    $dataImport[$parent_current]['items_npl'][] = [
                        'npl_ma_npl' => $npl_ma_npl,
                        'npl_ten_npl' => $npl_ten_npl,
                        'npl_height_1' => $npl_height_1,
                        'npl_width_1' => $npl_width_1,
                        'npl_dv_do_san_pham_1' => $npl_dv_do_san_pham_1,
                        'npl_dv_tinh_san_pham_1' => $npl_dv_tinh_san_pham_1,
                        'npl_height_2' => $npl_height_2,
                        'npl_chua_bien_1' => $npl_chua_bien_1,
                        'npl_width_2' => $npl_width_2,
                        'npl_chua_bien_2' => $npl_chua_bien_2,
                        'npl_dv_do_san_pham_2' => $npl_dv_do_san_pham_2,
                        'npl_dv_tinh_san_pham_2' => $npl_dv_tinh_san_pham_2,
                        'npl_chua_nhip' => $npl_chua_nhip,
                        'npl_chua_xa_width' => $npl_chua_xa_width,
                        'npl_chua_xa_height' => $npl_chua_xa_height,
                        'npl_tong_so_con_width' => $npl_tong_so_con_width,
                        'npl_tong_so_con_height' => $npl_tong_so_con_height,
                        'npl_tong_so_con_to' => $npl_tong_so_con_to,
                        'npl_gia_to' => $npl_gia_to,
                        'npl_gia_xlt' => $npl_gia_xlt,
                        'npl_tong_tien_to' => $npl_tong_tien_to,
                    ];
                }

                if (!empty($dt_loai) && !empty($dt_ma_thiet_bi_cong_doan)) {
                    $dataImport[$parent_current]['items_lstage'][] = [
                        'dt_loai' => $dt_loai,
                        'dt_ma_thiet_bi_cong_doan' => $dt_ma_thiet_bi_cong_doan,
                        'dt_ten_thiet_bi_cong_doan' => $dt_ten_thiet_bi_cong_doan,
                        'dt_height' => $dt_height,
                        'dt_width' => $dt_width,
                        'dt_so_con_to_in_1' => $dt_so_con_to_in_1,
                        'dt_so_luong_mau_in_1' => $dt_so_luong_mau_in_1,
                        'dt_loai_npl_1' => $dt_loai_npl_1,
                        'dt_so_luong_kem_1' => $dt_so_luong_kem_1,
                        'dt_so_lan_van_hanh_to_1' => $dt_so_lan_van_hanh_to_1,
                        'dt_dinh_muc_kem_su_dung_1' => $dt_dinh_muc_kem_su_dung_1,
                        'dt_dinh_muc_nang_suat_ctp_1' => $dt_dinh_muc_nang_suat_ctp_1,
                        'dt_so_con_to_in_2' => $dt_so_con_to_in_2,
                        'dt_so_luong_mau_in_2' => $dt_so_luong_mau_in_2,
                        'dt_loai_npl_2' => $dt_loai_npl_2,
                        'dt_so_luong_kem_2' => $dt_so_luong_kem_2,
                        'dt_so_lan_van_hanh_to_2' => $dt_so_lan_van_hanh_to_2,
                        'dt_dinh_muc_kem_su_dung_2' => $dt_dinh_muc_kem_su_dung_2,
                        'dt_dinh_muc_nang_suat_ctp_2' => $dt_dinh_muc_nang_suat_ctp_2,
                        'dt_tong_so_lan_van_hanh_to' => $dt_tong_so_lan_van_hanh_to,
                        'dt_tong_so_npl_to' => $dt_tong_so_npl_to,
                        'dt_gia_to' => $dt_gia_to,
                        'dt_tong_gia_to' => $dt_tong_gia_to,
                    ];
                }

                if (!empty($k_nhom_cong_doan)) {
                    $dataImport[$parent_current]['items_istage'][] = [
                        'k_nhom_cong_doan' => $k_nhom_cong_doan,
                        'k_height' => $k_height,
                        'k_width' => $k_width,
                        'k_don_vi_tinh' => $k_don_vi_tinh,
                        'k_loai_kiem_1' => $k_loai_kiem_1,
                        'k_so_lan_van_hanh_mat_1' => $k_so_lan_van_hanh_mat_1,
                        'k_dinh_muc_nang_suat_1' => $k_dinh_muc_nang_suat_1,
                        'k_loai_kiem_2' => $k_loai_kiem_2,
                        'k_so_lan_van_hanh_mat_2' => $k_so_lan_van_hanh_mat_2,
                        'k_dinh_muc_nang_suat_2' => $k_dinh_muc_nang_suat_2,
                    ];
                }

                if (!empty($pd_nhom_cong_doan)) {
                    $dataImport[$parent_current]['items_pstage'][] = [
                        'pd_nhom_cong_doan' => $pd_nhom_cong_doan,
                        'pd_height' => $pd_height,
                        'pd_width' => $pd_width,
                        'pd_cao_day' => $pd_cao_day,
                        'pd_don_vi_tinh' => $pd_don_vi_tinh,
                        'pd_so_con_kien' => $pd_so_con_kien,
                        'pd_dinh_muc_kien' => $pd_dinh_muc_kien,
                        'pd_dinh_muc_nang_suat' => $pd_dinh_muc_nang_suat,
                        'pd_loai_bao_bi_dong_goi' => $pd_loai_bao_bi_dong_goi,
                        'pd_loai_tem_dan' => $pd_loai_tem_dan,
                        'pd_tong_so_tem_dan' => $pd_tong_so_tem_dan,
                        'pd_tong_so_kien_dan' => $pd_tong_so_kien_dan,
                    ];
                }

                if (!empty($gh_nhom_cong_doan)) {
                    $dataImport[$parent_current]['items_dstage'][] = [
                        'gh_nhom_cong_doan' => $gh_nhom_cong_doan,
                        'gh_height' => $gh_height,
                        'gh_width' => $gh_width,
                        'gh_cao_day' => $gh_cao_day,
                        'gh_don_vi_tinh' => $gh_don_vi_tinh,
                        'gh_so_con_kien' => $gh_so_con_kien,
                        'gh_dinh_muc_kien' => $gh_dinh_muc_kien,
                        'gh_dinh_muc_nang_suat' => $gh_dinh_muc_nang_suat,
                        'gh_loai_bao_bi_dong_goi' => $gh_loai_bao_bi_dong_goi,
                        'gh_loai_tem_dan' => $gh_loai_tem_dan,
                        'gh_tong_so_tem_dan' => $gh_tong_so_tem_dan,
                        'gh_tong_so_kien_dan' => $gh_tong_so_kien_dan,
                    ];
                }

                if (!empty($dx_nhom_cong_doan)) {
                    $dataImport[$parent_current]['items_cstage'][] = [
                        'dx_nhom_cong_doan' => $dx_nhom_cong_doan,
                        'dx_loai_phuong_tien' => $dx_loai_phuong_tien,
                        'dx_don_vi_tinh' => $dx_don_vi_tinh,
                        'dx_don_gia_giao_hang' => $dx_don_gia_giao_hang,
                        'dx_tong_kien' => $dx_tong_kien,
                        'dx_thanh_tien' => $dx_thanh_tien,
                        'dx_nha_cung_cap' => $dx_nha_cung_cap,
                        'dx_dia_chi_giao_hàng' => $dx_dia_chi_giao_hàng,
                    ];
                }

                if (!empty($arrStages)) {
                    foreach ($arrStages as $kS => $vS) {
                        if (empty($vS)) continue;
                        $dataImport[$parent_current]['items_psstage'][$vS['id']][] = [
                            'dong_loai' => $value[$kS],
                            'dong_ma_thiet_bi_cong_doan' => trim($value[++$kS]),
                            'dong_ten_thiet_bi_cong_doan' => trim($value[++$kS]),
                            'dong_height' => number_unformat($value[++$kS]),
                            'dong_width' => number_unformat($value[++$kS]),
                            'dong_so_con_to_van_hanh_1' => number_unformat($value[++$kS]),
                            'dong_loai_npl_1' => trim($value[++$kS]),
                            'dong_so_lan_van_hanh_mat_1' => number_unformat($value[++$kS]),
                            'dong_dinh_muc_mun_in_1' => number_unformat($value[++$kS]),
                            'dong_dinh_muc_tg_canh_bai_1' => number_unformat($value[++$kS]),
                            'dong_dinh_muc_npl_canh_bai_1' => number_unformat($value[++$kS]),
                            'dong_tong_npl_1' => number_unformat($value[++$kS]),
                            'dong_tong_tg_canh_bai_1' => number_unformat($value[++$kS]),
                            'dong_so_con_to_van_hanh_2' => number_unformat($value[++$kS]),
                            'dong_loai_npl_2' => trim($value[++$kS]),
                            'dong_so_lan_van_hanh_mat_2' => number_unformat($value[++$kS]),
                            'dong_dinh_muc_mun_in_2' => number_unformat($value[++$kS]),
                            'dong_dinh_muc_tg_canh_bai_2' => number_unformat($value[++$kS]),
                            'dong_dinh_muc_npl_canh_bai_2' => number_unformat($value[++$kS]),
                            'dong_tong_npl_2' => number_unformat($value[++$kS]),
                            'dong_tong_tg_canh_bai_2' => number_unformat($value[++$kS]),
                            'dong_tong_npl_12' => number_unformat($value[++$kS]),
                            'dong_tong_tg_12' => number_unformat($value[++$kS]),
                            'dong_don_gia_lan_in' => number_unformat($value[++$kS]),
                            'dong_don_gia_to' => number_unformat($value[++$kS]),

                        ];
                    }
                }

                $row++;
            }

            $id_customer = $this->input->post('customers');
            $id_customer = str_replace('customers__', '', $id_customer);

            $errors = '';
            $dataItems = [];

            // print_arrays($dataImport);
            if (!empty($dataImport)) {
                foreach ($dataImport as $key => $value) {
                    $category = $value['category'];
                    $product_code = $value['product_code'];
                    $product_name = $value['product_name'];
                    $unit = $value['unit'];
                    $_row = $value['row'];
                    $height_qui_cach_sp_in = $value['height_qui_cach_sp_in'];
                    $width_qui_cach_sp_in = $value['width_qui_cach_sp_in'];


                    $code_quote_stage = $value['ma_bang_gia_cong_doan'];

                    if (empty($product_code)) {
                        $errors .= '<div>Dòng [' . $_row . '] không có mã thành phẩm</div>';
                        continue;
                    }

                    $quote_stage_id = NULL;
                    if (!empty($code_quote_stage)) {
                        $this->db->where('code', $code_quote_stage);
                        $this->db->where('EXISTS (SELECT 1 FROM tbl_stage_quote_client WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote.id AND tbl_stage_quote_client.id_client = "' . $id_customer . '")');
                        $quote_stage_id = $this->db->get('tbl_stage_quote')->row('id');
                        if (empty($quote_stage_id)) {
                            $errors .= '<div>Dòng [' . $_row . '] không tìm thấy báo giá công đoạn của khách hàng</div>';
                        }
                    }

                    $dtProduct = $this->products_model->rowProductByCode($product_code);
                    if (empty($dtProduct)) {
                        if (empty($category)) {
                            $errors .= '<div>Dòng [' . $_row . '] không thêm được thành phẩm vì mã danh mục bỏ trống</div>';
                            continue;
                        }

                        if (empty($unit)) {
                            $errors .= '<div>Dòng [' . $_row . '] không thêm được thành phẩm vì đơn vị bỏ trống</div>';
                            continue;
                        }

                        if (empty($product_name)) {
                            $errors .= '<div>Dòng [' . $_row . '] không thêm được thành phẩm vì tên thành phẩm bỏ trống</div>';
                            continue;
                        }

                        $row_unit = $this->unit_model->rowUnitByCode($unit, 'unitid', 'where');
                        if (!empty($row_unit)) {
                            $unit_id = $row_unit['unitid'];
                        } else {
                            $unit_id = $this->unit_model->insertUnit([
                                'unit' => $unit
                            ]);
                        }

                        $row_category = $this->products_model->rowCategoryProductsByCode($category, 'id', 'where');
                        if (!empty($row_category)) {
                            $category_id = $row_category['id'];
                        } else {
                            $category_id = $this->products_model->insertCategoryProducts([
                                'code' => $category,
                                'name' => $category,
                            ]);
                        }

                        $optionProduct = [
                            'category_id' => $category_id,
                            'type_products' => 'products',
                            'name' => $product_name,
                            'code' => $product_code,
                            'unit_id' => $unit_id,
                            'conversion_unit' => $unit_id,
                            'conversion_quantity_unit' => 1,
                            'longs' => $height_qui_cach_sp_in * 10,
                            'wide' => $width_qui_cach_sp_in * 10,
                            'hand_input_code' => 1,
                            // 'quote_stage_id' => $quote_stage_id,
                        ];
                        $product_id = $this->products_model->insertProducts($optionProduct);
                        $dtProduct = $this->products_model->rowProduct($product_id);
                    } else {
                        $product_id = $dtProduct['id'];
                    }

                    $images = '';
                    if ($dtProduct['images']) {
                        $images = base_url('uploads/products/' . $dtProduct['images']);
                    }

                    $unit = $this->unit_model->rowUnit($dtProduct['unit_id']);

                    $height = $value['height_qui_cach_sp_in'];
                    $corner_boundary_height = $value['height_chua_bien_bo_goc'];
                    $perpendicular_border_height = $value['height_chua_bien_vuong_goc'];
                    $round_square_border_height = $value['height_chua_bien_vuong_tron'];
                    $product_calculation_height = $height + $corner_boundary_height + $perpendicular_border_height + $round_square_border_height;

                    $width = $value['width_qui_cach_sp_in'];
                    $corner_boundary_width = $value['width_chua_bien_bo_goc'];
                    $perpendicular_border_width = $value['width_chua_bien_vuong_goc'];
                    $round_square_border_width = $value['width_chua_bien_vuong_tron'];
                    $product_calculation_width = $width + $corner_boundary_width + $perpendicular_border_width + $round_square_border_width;

                    $product_calculation_height_width = $product_calculation_height . 'x' . $product_calculation_width . ' cm';

                    $height_layout = $value['dan_trang_height'];
                    $height_layout_print_tweezers = $value['dan_trang_heiht_chua_nhip_in'];
                    $height_layout_boong_cut = $value['dan_trang_heiht_chua_boong_cat_be'];
                    $height_layout_material_size = $height_layout - $height_layout_print_tweezers - $height_layout_boong_cut;
                    $height_layout_mode = $product_calculation_height;
                    $height_layout_quantity = 0;
                    if ($height_layout_material_size != 0) {
                        $height_layout_quantity = floor($height_layout_material_size / $height_layout_mode);
                    }

                    $width_layout = $value['dan_trang_width'];
                    $width_layout_print_tweezers = $value['dan_trang_weight_chua_nhip_in'];
                    $width_layout_boong_cut = $value['dan_trang_weight_chua_boong_cat_be'];
                    $width_layout_material_size = $width_layout - $width_layout_print_tweezers - $width_layout_boong_cut;

                    $width_layout_mode = $product_calculation_width;
                    $width_layout_quantity = 0;
                    if ($width_layout_material_size != 0) {
                        $width_layout_quantity = floor($width_layout_material_size / $width_layout_mode);
                    }

                    $height_layout_total_quantity = floor($height_layout_quantity * $width_layout_quantity);

                    $ItemsPrice = [];
                    $grandTotalSheet = 0;
                    $pre_print_stage = !empty($value['pre_print_stage']) ? $value['pre_print_stage'] : NULL;
                    if (!empty($pre_print_stage)) {
                        foreach ($pre_print_stage as $k => $val) {
                            $cdt_item_code = $val['cdt_item_code'];
                            $cdt_stages = $val['cdt_stages'];

                            $this->db->select('
                                tbl_materials.*,
                                tbl_category_items.recipe as recipe,
                                tblunits.unit as unit_name,
                            ');
                            $this->db->from('tbl_materials');
                            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id');
                            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                            $this->db->where('tbl_materials.code', $cdt_item_code);
                            $material = $this->db->get()->row_array();
                            if (empty($material)) {
                                $errors .= '<div>Dòng [' . $_row . '] NPL [' . $cdt_item_code . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $this->db->select('tbl_stages.*');
                            $this->db->from('tbl_stages');
                            $this->db->group_start();
                            $this->db->where('tbl_stages.code', $cdt_stages);
                            $this->db->or_where('tbl_stages.name', $cdt_stages);
                            $this->db->group_end();
                            $stages = $this->db->get()->row_array();
                            if (empty($stages)) {
                                $errors .= '<div>Dòng [' . $_row . '] giai đoạn [' . $cdt_stages . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $stage_id_price = $stages['id'];
                            $item_id_price = $material['id'];
                            $number_operate = 1;

                            $recipe = $material['recipe'];
                            $price_import = $material['price_import'];
                            if (!empty($val['cdt_price_material'])) {
                                $price_about = $val['cdt_price_material'];
                            } else if ($recipe) {
                                $price_sell = ($price_import * $height_layout * $width_layout) / 10000;
                                $price_about = $price_sell;
                            } else {
                                $price_about = 0;
                            }
                            $quantity_color = $val['cdt_sl_mau_in'];
                            $total_sheet = $number_operate * $price_about;

                            $machine = $val['cdt_machine'];
                            if (!empty($machine)) {
                                $this->db->select('tbl_machines.*');
                                $this->db->from('tbl_machines');
                                $this->db->group_start();
                                $this->db->where('tbl_machines.code', $machine);
                                $this->db->group_end();
                                $dtMachine = $this->db->get()->row_array();
                                if (empty($dtMachine)) {
                                    $errors .= '<div>Dòng [' . $_row . '] mã thiết bị [' . $machine . '] chưa có trong phần mềm</div>';
                                    continue;
                                }

                                $machine = $dtMachine['id'];
                            } else {
                                $machine = 0;
                            }

                            $type_npl = $val['cdt_type_npl'];
                            $quota_bom = $val['cdt_quota_bom'];

                            $ItemsPrice[] = [
                                'type_price' => 'materials',
                                'stage_id_price' => $stage_id_price,
                                'item_id_price' => $item_id_price,
                                'number_operate' => $number_operate,
                                'price_about' => $price_about,
                                'total_sheet' => $total_sheet,
                                'quantity_color' => $quantity_color,
                                'machine' => $machine,
                                'type_npl' => $type_npl,
                                'quota_bom' => $quota_bom,
                            ];

                            $grandTotalSheet += $total_sheet;
                        }
                    }

                    $itemsPriceBackside = [];
                    $pre_print_stage_backside = !empty($value['pre_print_stage_backside']) ? $value['pre_print_stage_backside'] : NULL;
                    $grandTotalSheetBackside = 0;
                    if (!empty($pre_print_stage_backside)) {
                        foreach ($pre_print_stage_backside as $k => $val) {

                            $cdt_ms_item_code = $val['cdt_ms_item_code'];
                            $cdt_ms_stages = $val['cdt_ms_stages'];

                            $this->db->select('
                                tbl_materials.*,
                                tbl_category_items.recipe as recipe,
                                tblunits.unit as unit_name,
                            ');
                            $this->db->from('tbl_materials');
                            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id');
                            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
                            $this->db->where('tbl_materials.code', $cdt_ms_item_code);
                            $material = $this->db->get()->row_array();
                            if (empty($material)) {
                                $errors .= '<div>Dòng [' . $_row . '] NPL [' . $cdt_ms_item_code . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $this->db->select('tbl_stages.*');
                            $this->db->from('tbl_stages');
                            $this->db->group_start();
                            $this->db->where('tbl_stages.code', $cdt_ms_stages);
                            $this->db->or_where('tbl_stages.name', $cdt_ms_stages);
                            $this->db->group_end();
                            $stages = $this->db->get()->row_array();
                            if (empty($stages)) {
                                $errors .= '<div>Dòng [' . $_row . '] giai đoạn [' . $cdt_ms_stages . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $stage_id_price_backside = $stages['id'];
                            $item_id_price_backside = $material['id'];
                            $number_operate_backside = 1;

                            $recipe = $material['recipe'];
                            $price_import = $material['price_import'];
                            if (!empty($val['cdt_ms_price_material'])) {
                                $price_about_backside = $val['cdt_ms_price_material'];
                            } else if ($recipe) {
                                $price_sell = ($price_import * $height_layout * $width_layout) / 10000;
                                $price_about_backside = $price_sell;
                            } else {
                                $price_about_backside = 0;
                            }
                            $quantity_color_backside = $val['cdt_ms_sl_mau_in'];
                            $total_sheet_backside = $number_operate_backside * $price_about_backside;

                            $machine_backside = $val['cdt_ms_machine'];
                            if (!empty($machine_backside)) {
                                $this->db->select('tbl_machines.*');
                                $this->db->from('tbl_machines');
                                $this->db->group_start();
                                $this->db->where('tbl_machines.code', $machine_backside);
                                $this->db->group_end();
                                $dtMachine = $this->db->get()->row_array();
                                if (empty($dtMachine)) {
                                    $errors .= '<div>Dòng [' . $_row . '] mã thiết bị [' . $machine_backside . '] chưa có trong phần mềm</div>';
                                    continue;
                                }

                                $machine_backside = $dtMachine['id'];
                            } else {
                                $machine_backside = 0;
                            }

                            $type_npl_backside = $val['cdt_ms_type_npl'];
                            $quota_bom_backside = $val['cdt_ms_quota_bom'];

                            $itemsPriceBackside[] = [
                                'type_price_backside' => 'materials',
                                'stage_id_price_backside' => $stage_id_price_backside,
                                'item_id_price_backside' => $item_id_price_backside,
                                'number_operate_backside' => $number_operate_backside,
                                'price_about_backside' => $price_about_backside,
                                'total_sheet_backside' => $total_sheet_backside,
                                'quantity_color_backside' => $quantity_color_backside,
                                'machine_backside' => $machine_backside,
                                'type_npl_backside' => $type_npl_backside,
                                'quota_bom_backside' => $quota_bom_backside,
                            ];

                            $grandTotalSheetBackside += $total_sheet_backside;
                        }
                    }

                    $sum1 = $grandTotalSheetBackside + $grandTotalSheet;

                    $itemsStagesProducts = [];
                    $post_printing_stage = !empty($value['post_printing_stage']) ? $value['post_printing_stage'] : NULL;
                    $grandTotalProduct = 0;
                    if (!empty($post_printing_stage)) {
                        foreach ($post_printing_stage as $k => $val) {

                            $cdsi_stages = $val['cdsi_stages'];
                            $this->db->select('tbl_stages.*');
                            $this->db->from('tbl_stages');
                            $this->db->group_start();
                            $this->db->where('tbl_stages.code', $cdsi_stages);
                            $this->db->or_where('tbl_stages.name', $cdsi_stages);
                            $this->db->group_end();
                            $stages = $this->db->get()->row_array();
                            if (empty($stages)) {
                                $errors .= '<div>Dòng [' . $_row . '] giai đoạn [' . $cdsi_stages . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $stage_id_price_products = $stages['id'];
                            $number_operate_products = $val['cdsi_so_lan_xa_van_hanh'];
                            $long_height = $val['cdsi_long_height'];
                            $width_horizontal = $val['cdsi_horizontal'];
                            $price_about_products = $val['cdsi_don_gia_cd'];
                            // if (empty($price_about_products)) {
                            $this->db->where('id_stage', $stage_id_price_products);
                            $this->db->where('id_stage_quote', $quote_stage_id);
                            // if (!empty($long_height)) {
                            //     $this->db->where('height', $long_height);
                            // }
                            // if (!empty($width_horizontal)) {
                            //     $this->db->where('width', $width_horizontal);
                            // }
                            $dtPrice['price_sell'] = $this->db->get('tbl_stage_quote_detail')->row('price');
                            $price_about_products = !empty($dtPrice['price_sell']) ? $dtPrice['price_sell'] : 0;

                            if ($stages['formula_m2']) {
                                $price_about_products = ($price_about_products * $height_layout * $width_layout) / 10000;
                            }
                            // }
                            $total_sheet_products = $number_operate_products * $price_about_products;
                            $face_products = $val['cdsi_face_products'];

                            $itemsStagesProducts[] = [
                                'type_price_products' => 'stages',
                                'stage_id_price_products' => $stage_id_price_products,
                                'item_id_price_products' => $stage_id_price_products,
                                'number_operate_products' => $number_operate_products,
                                'price_about_products' => $price_about_products,
                                'total_sheet_products' => $total_sheet_products,
                                'not_cpln' => 0,
                                'long_height' => $long_height,
                                'width_horizontal' => $width_horizontal,
                                'face_products' => $face_products,
                            ];

                            $grandTotalProduct += $total_sheet_products;
                        }
                    }


                    $sum2 = $grandTotalProduct;
                    $g1 = 0;
                    if ($height_layout_total_quantity > 0) {
                        $g1 = ($sum1 + $sum2) / $height_layout_total_quantity;
                    }

                    $total_price_child_gvc = 0;
                    $outsourcing_shipping = $value['outsourcing_shipping'];
                    $itemsGVC = [];
                    if (!empty($outsourcing_shipping)) {
                        foreach ($outsourcing_shipping as $k => $val) {

                            $unit_kg = $val['gcvc_unit'];
                            $price_gvc = $val['gcvc_price'];
                            $kg_child_gvc = $val['gcvc_kg_con'];
                            $price_child_gvc = $price_gvc * $kg_child_gvc;
                            $total_price_child_gvc += $price_child_gvc;

                            $itemsGVC[] = [
                                'type_vc' => $val['gcvc_name'],
                                'price_gvc' => $price_gvc,
                                'unit_kg' => $unit_kg,
                                'kg_child_gvc' => $kg_child_gvc,
                                'price_child_gvc' => $price_child_gvc,
                                'total_price_child_gvc' => $total_price_child_gvc,
                            ];
                        }
                    }

                    //
                    $arrItemsNPL = [];
                    if (!empty($value['items_npl'])) {
                        $items_npl = $value['items_npl'];
                        foreach ($items_npl as $kI => $vI) {
                            $npl_ma_npl = $vI['npl_ma_npl'];
                            $this->db->select('
                                tbl_materials.*,
                            ');
                            $this->db->from('tbl_materials');
                            $this->db->where('tbl_materials.code', $npl_ma_npl);
                            $material = $this->db->get()->row_array();
                            if (empty($material)) {
                                $errors .= '<div>Dòng [' . $_row . '] thông tin NPL [' . $npl_ma_npl . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $__item_id = $material['id'];
                            $__height = number_unformat($vI['npl_height_1']);
                            $__width = number_unformat($vI['npl_width_1']);
                            $__unit_measure_sp = trim($vI['npl_dv_do_san_pham_1']);
                            $__unit_calculation_sp = trim($vI['npl_dv_tinh_san_pham_1']);
                            $__height1 = number_unformat($vI['npl_height_2']);
                            $__leave_margin = number_unformat($vI['npl_chua_bien_1']);
                            $__width1 = number_unformat($vI['npl_width_2']);
                            $__leave_margin1 = number_unformat($vI['npl_chua_bien_2']);
                            $__unit_measure_sp1 = trim($vI['npl_dv_do_san_pham_2']);
                            $__unit_calculation_sp1 = trim($vI['npl_dv_tinh_san_pham_2']);
                            $__leave_tweezers = number_unformat($vI['npl_chua_nhip']);
                            $__leave_discharge_w = number_unformat($vI['npl_chua_xa_width']);
                            $__leave_discharge_h = number_unformat($vI['npl_chua_xa_height']);
                            $__total_child_w = number_unformat($vI['npl_tong_so_con_width']);
                            $__total_child_h = number_unformat($vI['npl_tong_so_con_height']);
                            $__total_child_page = number_unformat($vI['npl_tong_so_con_to']);
                            $__price_page = number_unformat($vI['npl_gia_to']);
                            $__price_xlt = number_unformat($vI['npl_gia_xlt']);
                            $__total_money = $__price_page + $__price_xlt;

                            $arrItemsNPL[] = [
                                'item_id' => $__item_id,
                                'height' => $__height,
                                'width' => $__width,
                                'unit_measure_sp' => $__unit_measure_sp,
                                'unit_calculation_sp' => $__unit_calculation_sp,
                                'height1' => $__height1,
                                'leave_margin' => $__leave_margin,
                                'width1' => $__width1,
                                'leave_margin1' => $__leave_margin1,
                                'unit_measure_sp1' => $__unit_measure_sp1,
                                'unit_calculation_sp1' => $__unit_calculation_sp1,
                                'leave_tweezers' => $__leave_tweezers,
                                'leave_discharge_w' => $__leave_discharge_w,
                                'leave_discharge_h' => $__leave_discharge_h,
                                'total_child_w' => $__total_child_w,
                                'total_child_h' => $__total_child_h,
                                'total_child_page' => $__total_child_page,
                                'price_page' => $__price_page,
                                'price_xlt' => $__price_xlt,
                                'total_money' => $__total_money,
                            ];
                        }
                    }

                    $arrItemsLStage = [];
                    if (!empty($value['items_lstage'])) {
                        $items_lstage = $value['items_lstage'];
                        foreach ($items_lstage as $k => $val) {
                            $dt_ma_thiet_bi_cong_doan = $val['dt_ma_thiet_bi_cong_doan'];
                            $__item_id = 0;
                            if (empty($val['dt_loai']) || ($val['dt_loai'] != 1 && $val['dt_loai'] != 2)) {
                                continue;
                            } else if ($val['dt_loai'] == 1) {
                                $this->db->select('tbl_stages.*');
                                $this->db->from('tbl_stages');
                                $this->db->group_start();
                                $this->db->where('tbl_stages.code', $dt_ma_thiet_bi_cong_doan);
                                // $this->db->or_where('tbl_stages.name', $cdt_stages);
                                $this->db->group_end();
                                $stages = $this->db->get()->row_array();
                                if (empty($stages)) {
                                    $errors .= '<div>Dòng [' . $_row . '] mã công đoạn dàn trang [' . $dt_ma_thiet_bi_cong_doan . '] chưa có trong phần mềm</div>';
                                    continue;
                                }
                                $__item_id = $stages['id'];
                            } else if ($val['dt_loai'] == 2) {
                                $this->db->select('tbl_machines.*');
                                $this->db->from('tbl_machines');
                                $this->db->group_start();
                                $this->db->where('tbl_machines.code', $dt_ma_thiet_bi_cong_doan);
                                $this->db->group_end();
                                $dtMachine = $this->db->get()->row_array();
                                if (empty($dtMachine)) {
                                    $errors .= '<div>Dòng [' . $_row . '] mã thiết bị dàn trang [' . $dt_ma_thiet_bi_cong_doan . '] chưa có trong phần mềm</div>';
                                    continue;
                                }

                                $__item_id = $dtMachine['id'];
                            }

                            $__item_id = $__item_id;
                            $__type = $val['dt_loai'];
                            $__height = number_unformat($val['dt_height']);
                            $__width = number_unformat($val['dt_width']);
                            $__number_child_print_f1 = number_unformat($val['dt_so_con_to_in_1']);
                            $__number_color_print_f1 = number_unformat($val['dt_so_luong_mau_in_1']);
                            $__type_npl_f1 = trim($val['dt_loai_npl_1']);
                            $__number_zn_f1 = number_unformat($val['dt_so_luong_kem_1']);
                            $__number_operations_page_f1 = number_unformat($val['dt_so_lan_van_hanh_to_1']);
                            $__quota_zn_use_f1 = number_unformat($val['dt_dinh_muc_kem_su_dung_1']);
                            $__quota_ctp_f1 = number_unformat($val['dt_dinh_muc_nang_suat_ctp_1']);
                            $__number_child_print_f2 = number_unformat($val['dt_so_con_to_in_2']);
                            $__number_color_print_f2 = number_unformat($val['dt_so_luong_mau_in_2']);
                            $__type_npl_f2 = trim($val['dt_loai_npl_2']);
                            $__number_zn_f2 = number_unformat($val['dt_so_luong_kem_2']);
                            $__number_operations_page_f2 = number_unformat($val['dt_so_lan_van_hanh_to_2']);
                            $__quota_zn_use_f2 = number_unformat($val['dt_dinh_muc_kem_su_dung_2']);
                            $__quota_ctp_f2 = number_unformat($val['dt_dinh_muc_nang_suat_ctp_2']);
                            $__total_npl = number_unformat($val['dt_tong_so_npl_to']);
                            $__price = number_unformat($val['dt_gia_to']);
                            $__total_operations_page = $__number_operations_page_f1 + $__number_operations_page_f2;
                            $__total_price = $__total_operations_page * $__price;

                            $arrItemsLStage[] = [
                                'item_id' => $__item_id,
                                'type' => $__type,
                                'height' => $__height,
                                'width' => $__width,
                                'number_child_print_f1' => $__number_child_print_f1,
                                'number_color_print_f1' => $__number_color_print_f1,
                                'type_npl_f1' => $__type_npl_f1,
                                'number_zn_f1' => $__number_zn_f1,
                                'number_operations_page_f1' => $__number_operations_page_f1,
                                'quota_zn_use_f1' => $__quota_zn_use_f1,
                                'quota_ctp_f1' => $__quota_ctp_f1,
                                'number_child_print_f2' => $__number_child_print_f2,
                                'number_color_print_f2' => $__number_color_print_f2,
                                'type_npl_f2' => $__type_npl_f2,
                                'number_zn_f2' => $__number_zn_f2,
                                'number_operations_page_f2' => $__number_operations_page_f2,
                                'quota_zn_use_f2' => $__quota_zn_use_f2,
                                'quota_ctp_f2' => $__quota_ctp_f2,
                                'total_npl' => $__total_npl,
                                'price' => $__price,
                                'total_operations_page' => $__total_operations_page,
                                'total_price' => $__total_price,
                            ];
                        }
                    }

                    $arrItemsIStage = [];
                    if (!empty($value['items_istage'])) {
                        $items_istage = $value['items_istage'];
                        foreach ($items_istage as $k => $val) {
                            $k_nhom_cong_doan = $val['k_nhom_cong_doan'];

                            $this->db->select('tbl_category_stages.id');
                            $this->db->from('tbl_category_stages');
                            $this->db->where('tbl_category_stages.code', $k_nhom_cong_doan);
                            $dtCategoryStage = $this->db->get()->row_array();
                            if (empty($dtCategoryStage)) {
                                $errors .= '<div>Dòng [' . $_row . '] mã nhóm công đoạn trong công đoạn kiểm [' . $k_nhom_cong_doan . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $__category_stage_id = $dtCategoryStage['id'];
                            $__height = number_unformat($val['k_height']);
                            $__width = number_unformat($val['k_width']);
                            $__unit_f1 = trim($val['k_don_vi_tinh']);
                            $__type_check_f1 = trim($val['k_loai_kiem_1']);
                            $__number_o_side_f1 = number_unformat($val['k_so_lan_van_hanh_mat_1']);
                            $__productivity_norms_f1 = number_unformat($val['k_dinh_muc_nang_suat_1']);
                            $__type_check_f2 = trim($val['k_loai_kiem_2']);
                            $__number_o_side_f2 = number_unformat($val['k_so_lan_van_hanh_mat_2']);
                            $__productivity_norms_f2 = number_unformat($val['k_dinh_muc_nang_suat_2']);

                            $arrItemsIStage[] = [
                                'category_stage_id' => $__category_stage_id,
                                'height' => $__height,
                                'width' => $__width,
                                'unit_f1' => $__unit_f1,
                                'type_check_f1' => $__type_check_f1,
                                'number_o_side_f1' => $__number_o_side_f1,
                                'productivity_norms_f1' => $__productivity_norms_f1,
                                'type_check_f2' => $__type_check_f2,
                                'number_o_side_f2' => $__number_o_side_f2,
                                'productivity_norms_f2' => $__productivity_norms_f2,
                            ];
                        }
                    }

                    $arrItemsPStage = [];
                    if (!empty($value['items_pstage'])) {
                        $items_pstage = $value['items_pstage'];
                        foreach ($items_pstage as $k => $val) {

                            $pd_nhom_cong_doan = $val['pd_nhom_cong_doan'];

                            $this->db->select('tbl_category_stages.id');
                            $this->db->from('tbl_category_stages');
                            $this->db->where('tbl_category_stages.code', $pd_nhom_cong_doan);
                            $dtCategoryStage = $this->db->get()->row_array();
                            if (empty($dtCategoryStage)) {
                                $errors .= '<div>Dòng [' . $_row . '] mã nhóm công đoạn trong công đoạn phân đơn - dán tem [' . $pd_nhom_cong_doan . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $__category_stage_id = $dtCategoryStage['id'];
                            $__height = number_unformat($val['pd_height']);
                            $__width = number_unformat($val['pd_width']);
                            $__hight_bottom = number_unformat($val['pd_cao_day']);
                            $__unit = trim($val['pd_don_vi_tinh']);
                            $__number_bales = number_unformat($val['pd_so_con_kien']);
                            $__bale_norms = number_unformat($val['pd_dinh_muc_kien']);
                            $__productivity_norms = number_unformat($val['pd_dinh_muc_nang_suat']);
                            $__type_packaging = trim($val['pd_loai_bao_bi_dong_goi']);
                            $__type_tem = trim($val['pd_loai_tem_dan']);
                            $__total_tem = number_unformat($val['pd_tong_so_tem_dan']);
                            $__total_bale = number_unformat($val['pd_tong_so_kien_dan']);

                            $arrItemsPStage[] = [
                                'category_stage_id' => $__category_stage_id,
                                'height' => $__height,
                                'width' => $__width,
                                'hight_bottom' => $__hight_bottom,
                                'unit' => $__unit,
                                'number_bales' => $__number_bales,
                                'bale_norms' => $__bale_norms,
                                'productivity_norms' => $__productivity_norms,
                                'type_packaging' => $__type_packaging,
                                'type_tem' => $__type_tem,
                                'total_tem' => $__total_tem,
                                'total_bale' => $__total_bale,
                            ];
                        }
                    }

                    $arrItemsDStage = [];
                    if (!empty($value['items_dstage'])) {
                        $items_dstage = $value['items_dstage'];
                        foreach ($items_dstage as $k => $val) {

                            $gh_nhom_cong_doan = $val['gh_nhom_cong_doan'];

                            $this->db->select('tbl_category_stages.id');
                            $this->db->from('tbl_category_stages');
                            $this->db->where('tbl_category_stages.code', $gh_nhom_cong_doan);
                            $dtCategoryStage = $this->db->get()->row_array();
                            if (empty($dtCategoryStage)) {
                                $errors .= '<div>Dòng [' . $_row . '] mã nhóm công đoạn trong công đoạn mở phiếu giao hàng [' . $gh_nhom_cong_doan . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $__category_stage_id = $dtCategoryStage['id'];
                            $__height = number_unformat($val['gh_height']);
                            $__width = number_unformat($val['gh_width']);
                            $__hight_bottom = number_unformat($val['gh_cao_day']);
                            $__unit = trim($val['gh_don_vi_tinh']);
                            $__number_bales = number_unformat($val['gh_so_con_kien']);
                            $__bale_norms = number_unformat($val['gh_dinh_muc_kien']);
                            $__productivity_norms = number_unformat($val['gh_dinh_muc_nang_suat']);
                            $__type_packaging = trim($val['gh_loai_bao_bi_dong_goi']);
                            $__type_tem = trim($val['gh_loai_tem_dan']);
                            $__total_tem = number_unformat($val['gh_tong_so_tem_dan']);
                            $__total_bale = number_unformat($val['gh_tong_so_kien_dan']);

                            $arrItemsDStage[] = [
                                'category_stage_id' => $__category_stage_id,
                                'height' => $__height,
                                'width' => $__width,
                                'hight_bottom' => $__hight_bottom,
                                'unit' => $__unit,
                                'number_bales' => $__number_bales,
                                'bale_norms' => $__bale_norms,
                                'productivity_norms' => $__productivity_norms,
                                'type_packaging' => $__type_packaging,
                                'type_tem' => $__type_tem,
                                'total_tem' => $__total_tem,
                                'total_bale' => $__total_bale,
                            ];
                        }
                    }

                    $arrItemsCStage = [];
                    if (!empty($value['items_cstage'])) {
                        $items_cstage = $value['items_cstage'];
                        foreach ($items_cstage as $k => $val) {

                            $dx_nhom_cong_doan = $val['dx_nhom_cong_doan'];

                            $this->db->select('tbl_category_stages.id');
                            $this->db->from('tbl_category_stages');
                            $this->db->where('tbl_category_stages.code', $dx_nhom_cong_doan);
                            $dtCategoryStage = $this->db->get()->row_array();
                            if (empty($dtCategoryStage)) {
                                $errors .= '<div>Dòng [' . $_row . '] mã nhóm công đoạn trong công đoạn điều xe [' . $dx_nhom_cong_doan . '] chưa có trong phần mềm</div>';
                                continue;
                            }

                            $__category_stage_id = $dtCategoryStage['id'];
                            $__transportation = trim($val['dx_loai_phuong_tien']);
                            $__unit = trim($val['dx_don_vi_tinh']);
                            $__price_delivery = number_unformat($val['dx_don_gia_giao_hang']);
                            $__total_bale = number_unformat($val['dx_tong_kien']);
                            $__subtotal = number_unformat($val['dx_thanh_tien']);
                            $__supplier = trim($val['dx_nha_cung_cap']);
                            $__address_delivery = trim($val['dx_dia_chi_giao_hàng']);

                            $arrItemsCStage[] = [
                                'category_stage_id' => $__category_stage_id,
                                'transportation' => $__transportation,
                                'unit' => $__unit,
                                'price_delivery' => $__price_delivery,
                                'total_bale' => $__total_bale,
                                'subtotal' => $__subtotal,
                                'supplier' => $__supplier,
                                'address_delivery' => $__address_delivery,
                            ];
                        }
                    }

                    $arrItemsPsStage = [];
                    if (!empty($value['items_psstage'])) {
                        $items_psstage = $value['items_psstage'];
                        foreach ($items_psstage as $k => $arrItemsS) {
                            $itemsS = [];
                            if (!empty($arrItemsS)) {
                                foreach ($arrItemsS as $kS => $vS) {

                                    $dong_ma_thiet_bi_cong_doan = $vS['dong_ma_thiet_bi_cong_doan'];
                                    $__item_id = 0;
                                    if (empty($vS['dong_loai']) || ($vS['dong_loai'] != 1 && $vS['dong_loai'] != 2)) {
                                        continue;
                                    } else if ($vS['dong_loai'] == 1) {
                                        $this->db->select('tbl_stages.*');
                                        $this->db->from('tbl_stages');
                                        $this->db->group_start();
                                        $this->db->where('tbl_stages.code', $dong_ma_thiet_bi_cong_doan);
                                        // $this->db->or_where('tbl_stages.name', $cdt_stages);
                                        $this->db->group_end();
                                        $stages = $this->db->get()->row_array();
                                        if (empty($stages)) {
                                            $errors .= '<div>Dòng [' . $_row . '] mã công đoạn công đoạn động [' . $dong_ma_thiet_bi_cong_doan . '] chưa có trong phần mềm</div>';
                                            continue;
                                        }
                                        $__item_id = $stages['id'];
                                    } else if ($vS['dong_loai'] == 2) {
                                        $this->db->select('tbl_machines.*');
                                        $this->db->from('tbl_machines');
                                        $this->db->group_start();
                                        $this->db->where('tbl_machines.code', $dong_ma_thiet_bi_cong_doan);
                                        $this->db->group_end();
                                        $dtMachine = $this->db->get()->row_array();
                                        if (empty($dtMachine)) {
                                            $errors .= '<div>Dòng [' . $_row . '] mã thiết bị công đoạn động [' . $dong_ma_thiet_bi_cong_doan . '] chưa có trong phần mềm</div>';
                                            continue;
                                        }

                                        $__item_id = $dtMachine['id'];
                                    }

                                    $__item_id = $__item_id;
                                    $__type = $vS['dong_loai'];
                                    $__height = number_unformat($vS['dong_height']);
                                    $__width = number_unformat($vS['dong_width']);
                                    $__number_operating_f1 = number_unformat($vS['dong_so_con_to_van_hanh_1']);
                                    $__type_npl_f1 = trim($vS['dong_loai_npl_1']);
                                    $__number_operating_side_f1 = number_unformat($vS['dong_so_lan_van_hanh_mat_1']);
                                    $__ink_f1 = number_unformat($vS['dong_dinh_muc_mun_in_1']);
                                    $__quota_time_f1 = number_unformat($vS['dong_dinh_muc_tg_canh_bai_1']);
                                    $__quota_npl_f1 = number_unformat($vS['dong_dinh_muc_npl_canh_bai_1']);
                                    $__total_npl_f1 = number_unformat($vS['dong_tong_npl_1']);
                                    $__total_time_npl_f1 = number_unformat($vS['dong_tong_tg_canh_bai_1']);
                                    $__number_operating_f2 = number_unformat($vS['dong_so_con_to_van_hanh_2']);
                                    $__type_npl_f2 = trim($vS['dong_loai_npl_2']);
                                    $__number_operating_side_f2 = number_unformat($vS['dong_so_lan_van_hanh_mat_2']);
                                    $__ink_f2 = number_unformat($vS['dong_dinh_muc_mun_in_2']);
                                    $__quota_time_f2 = number_unformat($vS['dong_dinh_muc_tg_canh_bai_2']);
                                    $__quota_npl_f2 = number_unformat($vS['dong_dinh_muc_npl_canh_bai_2']);
                                    $__total_npl_f2 = number_unformat($vS['dong_tong_npl_2']);
                                    $__total_time_npl_f2 = number_unformat($vS['dong_tong_tg_canh_bai_2']);
                                    $__price = number_unformat($vS['dong_don_gia_lan_in']);
                                    $__total_npl_f12 = $__total_npl_f1 + $__total_npl_f2;
                                    $__total_time_f12 = $__total_time_npl_f1 + $__total_time_npl_f2;
                                    $__total_number_operating_side = $__number_operating_side_f1 + $__number_operating_side_f2;
                                    $__price_page = $__total_number_operating_side * $__price;

                                    $itemsS[] = [
                                        'item_id' => $__item_id,
                                        'type' => $__type,
                                        'height' => $__height,
                                        'width' => $__width,
                                        'number_operating_f1' => $__number_operating_f1,
                                        'type_npl_f1' => $__type_npl_f1,
                                        'number_operating_side_f1' => $__number_operating_side_f1,
                                        'ink_f1' => $__ink_f1,
                                        'quota_time_f1' => $__quota_time_f1,
                                        'quota_npl_f1' => $__quota_npl_f1,
                                        'total_npl_f1' => $__total_npl_f1,
                                        'total_time_npl_f1' => $__total_time_npl_f1,
                                        'number_operating_f2' => $__number_operating_f2,
                                        'type_npl_f2' => $__type_npl_f2,
                                        'number_operating_side_f2' => $__number_operating_side_f2,
                                        'ink_f2' => $__ink_f2,
                                        'quota_time_f2' => $__quota_time_f2,
                                        'quota_npl_f2' => $__quota_npl_f2,
                                        'total_npl_f2' => $__total_npl_f2,
                                        'total_time_npl_f2' => $__total_time_npl_f2,
                                        'price' => $__price,
                                        'total_npl_f12' => $__total_npl_f12,
                                        'total_time_f12' => $__total_time_f12,
                                        'total_number_operating_side' => $__total_number_operating_side,
                                        'price_page' => $__price_page,
                                    ];
                                }
                            }

                            $arrItemsPsStage[] = [
                                'stage_id' => $k,
                                'itemsS' => $itemsS
                            ];
                        }
                    }
                    //

                    $g2 = $total_price_child_gvc;

                    $cost_of_brand = $value['chi_phi_brand'];
                    $labor_cost = $value['chi_phi_ql_nhan_cong'];
                    $loss_cost = $value['chi_phi_hp_cong_doan'];
                    $profit = $value['loi_nhuan'];

                    $total_precent = $cost_of_brand + $labor_cost + $loss_cost + $profit;
                    $g3 = ($g1 + $g2) * $total_precent / 100;
                    $g = $g1 + $g2 + $g3;

                    $dataJson = [
                        'product_quote_reference' => '',
                        'cItemsId' => $product_id . '__products',
                        'height' => $height,
                        'corner_boundary_height' => $corner_boundary_height,
                        'perpendicular_border_height' => $perpendicular_border_height,
                        'round_square_border_height' => $round_square_border_height,
                        'product_calculation_height' => $product_calculation_height,
                        'width' => $width,
                        'corner_boundary_width' => $corner_boundary_width,
                        'perpendicular_border_width' => $perpendicular_border_width,
                        'round_square_border_width' => $round_square_border_width,
                        'product_calculation_width' => $product_calculation_width,
                        'product_calculation_height_width' => $product_calculation_height_width,
                        'height_layout' => $height_layout,
                        'height_layout_print_tweezers' => $height_layout_print_tweezers,
                        'height_layout_boong_cut' => $height_layout_boong_cut,
                        'height_layout_material_size' => $height_layout_material_size,
                        'height_layout_mode' => $height_layout_mode,
                        'height_layout_quantity' => $height_layout_quantity,
                        'width_layout' => $width_layout,
                        'width_layout_print_tweezers' => $width_layout_print_tweezers,
                        'width_layout_boong_cut' => $width_layout_boong_cut,
                        'width_layout_material_size' => $width_layout_material_size,
                        'width_layout_mode' => $width_layout_mode,
                        'width_layout_quantity' => $width_layout_quantity,
                        'height_layout_total_quantity' => $height_layout_total_quantity,
                        'ItemsPrice' => $ItemsPrice,
                        'grandTotalSheet' => $grandTotalSheet,
                        'labor_cost' => $labor_cost,
                        'loss_cost' => $loss_cost,
                        'profit' => $profit,
                        'total_precent' => $total_precent,
                        'g3' => $g3,
                        'g' => $g,
                        'itemsPriceBackside' => $itemsPriceBackside,
                        'grandTotalSheetBackside' => $grandTotalSheetBackside,
                        'sum1' => $sum1,
                        'itemsStagesProducts' => $itemsStagesProducts,
                        'grandTotalProduct' => $grandTotalProduct,
                        'sum2' => $sum2,
                        'g1' => $g1,
                        'total_price_child_gvc' => $total_price_child_gvc,
                        'g2' => $g2,
                        'itemsGVC' => $itemsGVC,
                        'cost_of_brand' => $cost_of_brand,

                        'arrItemsNPL' => $arrItemsNPL,
                        'arrItemsLStage' => $arrItemsLStage,
                        'arrItemsIStage' => $arrItemsIStage,
                        'arrItemsPStage' => $arrItemsPStage,
                        'arrItemsDStage' => $arrItemsDStage,
                        'arrItemsCStage' => $arrItemsCStage,
                        'arrItemsPsStage' => $arrItemsPsStage
                    ];

                    // print_arrays($dataJson);

                    $dataItems[] = [
                        'items_id' => $product_id . '__products',
                        'json_item' => ['id' => $product_id . '__products', 'text' => $product_code],
                        'item_code' => $dtProduct['code'],
                        'item_name' => $dtProduct['name'],
                        'images' => $images,
                        'quote_stage_id' => $quote_stage_id,
                        'unit_name' => $unit['unit'],
                        'dataJson' => json_encode($dataJson, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        'g' => $g,
                    ];
                }
            }

            $data['result'] = 1;
            $data['message'] = lang('success');
            $data['errors'] = $errors;
            $data['dataItems'] = $dataItems;
            echo json_encode($data);
            die;
        }
    }

    public function exportExcelPriceQuotes($id)
    {
        if (!$this->perPrintQuotes) {
            accessDenied();
        }

        $this->load->model('handling_price_model');
        $this->load->model('category_model');
        $excel = cloumns_excel();

        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.2); // ~ 1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.2); // ~1.02cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2); // ~
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.2); // ~1.78cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.2); // ~1.73cm
        $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0); // ~1.02cm

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $rowBegin = 1;
        $iExcel = -1;

        $where = getWhereQuotes([], true);
        $quote = $this->quotes_model->getQuotesById($id, $where);

        insertCompanyInfo($objPHPExcel);
        $iExcel = -1;

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Mã danh mục*');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Mã thành phẩm*');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Tên thành phẩm*');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Đơn vị*');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Height');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 3)] . '5');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Qui Cách SP In Cm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên Bo Góc');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên Vuông Góc');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên Vuông Tròn');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Width');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 3)] . '5');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Qui Cách SP In Cm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên Bo Góc');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên Vuông Góc');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên Vuông Tròn');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Dàn Trang');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 5)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Nhíp In');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Boong Cắt Bế');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Nhíp In');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Boong Cắt Bế');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công đoạn trước in');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 7)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Nguyên phụ liệu');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Công đoạn');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'SL màu in');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thiết bị');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại NPL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định mức BOM');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn giá NVL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thành tiền tờ');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công đoạn trước in(mặt sau)');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 7)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Nguyên phụ liệu');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Công đoạn');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'SL màu in');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thiết bị');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại NPL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định mức BOM');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn giá NVL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thành tiền/tờ');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công đoạn sau in');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 6)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Hạng mục tính giá');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Dài/Cao');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Rộng/Ngang');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Xả/Vận Hành');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Mặt in (1: mặt trước, 2: mặt sau, 3: ,ặt trước và sau)');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Giá CĐ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thành tiền');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Gia công - Vận chuyển');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 4)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Tên');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'ĐVT');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn giá');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'KG/Con');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thành tiền');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Chi Phí Brand');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        // $objPHPExcel->getActiveSheet()->mergeCells('AQ1:AQ3');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Chi Phí QL- Nhân Công');
        // $objPHPExcel->getActiveSheet()->mergeCells('AR1:AR3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Chi Phí Hao Phế các Công Đoạn');
        // $objPHPExcel->getActiveSheet()->mergeCells('AS1:AS3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Lợi Nhuận');
        // $objPHPExcel->getActiveSheet()->mergeCells('AT1:AT3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Mã Bảng Giá Công Đoạn');
        // $objPHPExcel->getActiveSheet()->mergeCells('AU1:AU3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'G(giá cuối)');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[$iExcel] . '7');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Thông tin NPL');
        // $objPHPExcel->getActiveSheet()->mergeCells('AV1:BP1');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 20)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Mã NPL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tên NPL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'ĐV Đo SP');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'ĐV Tính SP');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Biên');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'ĐV Đo SP');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'ĐV Tính SP');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Nhíp');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Xả Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Chừa Xả Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Con Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Con Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Con/Tờ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Giá/Tờ (VNĐ)');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Giá/XLT In/Tờ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Tiền/Tờ');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Dàn Trang');
        // $objPHPExcel->getActiveSheet()->mergeCells('BQ1:CM1');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 22)] . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '6', 'Loại(1: Công Đoạn, 2: thiết bị)');
        // $objPHPExcel->getActiveSheet()->mergeCells('BQ2:BQ3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mã Thiết Bị-Công Đoạn');
        // $objPHPExcel->getActiveSheet()->mergeCells('BR2:BR3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[$iExcel] . '7');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Tên Thiết Bị-Công Đoạn');
        // $objPHPExcel->getActiveSheet()->mergeCells('BS2:BS3');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[$iExcel] . '7');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Kích Thước Vận Hành');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . ($excel[$iExcel + 1]) . '6');
        // $objPHPExcel->getActiveSheet()->mergeCells('BT2:BT3');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mặt 1');
        // $objPHPExcel->getActiveSheet()->mergeCells('BV2:CB2');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 6)] . '6');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Số Con/Tờ In');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lượng Màu In');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại NPL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lượng Kẽm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Vận Hành/Tờ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Kẽm Sử Dụng');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Năng Suất /CTP');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mặt 2');
        // $objPHPExcel->getActiveSheet()->mergeCells('CC2:CH2');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 10)] . '6');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Số Con/Tờ In');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lượng Màu In');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại NPL');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lượng Kẽm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Vận Hành/Tờ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Kẽm Sử Dụng');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Năng Suất /CTP');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Lần Vận Hành/Tờ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số NPL/Tờ');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Giá/Tờ (VNĐ)');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Giá/Tờ (VNĐ)');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công Đoạn Kiểm');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 9)] . '5');
        // $objPHPExcel->getActiveSheet()->mergeCells('CN1:CW1');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '6', 'Kích Thước Vận Hành');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 1)] . '6');
        // $objPHPExcel->getActiveSheet()->mergeCells('CN2:CP2');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Nhóm Công Đoạn Kiểm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mặt 1');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 3)] . '6');
        // $objPHPExcel->getActiveSheet()->mergeCells('CQ2:CT2');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Đơn Vị Tính');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại Kiểm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Vận Hành/Mặt');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Năng Suất');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mặt 2');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 3)] . '6');
        // $objPHPExcel->getActiveSheet()->mergeCells('CU2:CW2');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Loại Kiểm');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Vận Hành/Mặt');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Năng Suất');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công Đoạn Phân Đơn - Dán Tem');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 11)] . '5');
        // $objPHPExcel->getActiveSheet()->mergeCells('CX1:DI1');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Nhóm Công Đoạn Đóng Gói');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Cao/Đáy');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Vị Tính');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Con/Kiện');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Kiện');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Năng Suất');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại Bao Bì Đóng Gói');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại Tem Dán');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Tem Dán');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Kiện Dán');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công Đoạn Mở Phiếu Giao Hàng');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 11)] . '5');
        // $objPHPExcel->getActiveSheet()->mergeCells('DJ1:DU1');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Nhóm Công Đoạn Giao Hàng');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Height');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Cao/Đáy');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Vị Tính');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Con/Kiện');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Kiện');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Năng Suất');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại Bao Bì Đóng Gói');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại Tem Dán');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Tem Dán');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Số Kiện Dán');

        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', 'Công đoạn điều xe');
        $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 7)] . '5');
        // $objPHPExcel->getActiveSheet()->mergeCells('DV1:EC1');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Nhóm Công Đoạn Giao Hàng');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại Phương Tiện');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Vị Tính');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Giá Giáo Hàng');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng Kiện');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Thành Tiền');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Nhà Cung Cấp');
        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Địa Chỉ Giao Hàng');



        //handling data
        $this->db->select('
            tbl_products.code as item_code,
            tbl_products.name as item_name,
            tbl_category_products.code as code_category,
            tblunits.unit as unit_name,
            tbl_stage_quote.code as code_stage_quote,
            tbl_quote_items.data_price_json as data_price_json
        ', false);
        $this->db->from('tbl_quote_items');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
        $this->db->join('tbl_stage_quote', 'tbl_stage_quote.id = tbl_quote_items.quote_stage_id', 'left');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_products.unit_id', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->where('tbl_quote_items.quote_id', $id);
        $items = $this->db->get()->result_array();

        // $rowBegin = 3;
        $rowBegin = 7;
        // $iExcel = 152;

        $arrStagesCustom = [];
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $data_price_json = !empty($value['data_price_json']) ? json_decode($value['data_price_json'], true) : null;
                $arrItemsPsStage = $data_price_json['arrItemsPsStage'] ?? null;
                if (!empty($arrItemsPsStage)) {
                    foreach ($arrItemsPsStage as $k => $v) {
                        if (!in_array($v['stage_id'], $arrStagesCustom)) {
                            $arrStagesCustom[] = $v['stage_id'];
                        }
                    }
                }
            }
        }

        if (!empty($arrStagesCustom)) {
            $this->db->select('tbl_stages.id, tbl_stages.code');
            $this->db->from('tbl_stages');
            $this->db->where_in('tbl_stages.id', $arrStagesCustom);
            $dtStagesCustom = $this->db->get()->result_array();
            if ($dtStagesCustom) {
                foreach ($dtStagesCustom as $key => $value) {
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '5', $value['code']);
                    // // $objPHPExcel->getActiveSheet()->mergeCells('BQ1:CM1');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '5:' . $excel[($iExcel + 22)] . '5');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '6', 'Loại(1: Công Đoạn, 2: thiết bị)');
                    // // $objPHPExcel->getActiveSheet()->mergeCells('BQ2:BQ3');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[$iExcel] . '7');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mã Thiết Bị-Công Đoạn');
                    // // $objPHPExcel->getActiveSheet()->mergeCells('BR2:BR3');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[$iExcel] . '7');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Tên Thiết Bị-Công Đoạn');
                    // // $objPHPExcel->getActiveSheet()->mergeCells('BS2:BS3');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[$iExcel] . '7');

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Kích Thước Vận Hành');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . ($excel[$iExcel + 1]) . '6');
                    // // $objPHPExcel->getActiveSheet()->mergeCells('BT2:BT3');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Height');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Width');

                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mặt 1');
                    // // $objPHPExcel->getActiveSheet()->mergeCells('BV2:CB2');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 7)] . '6');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Số Con/Tờ Vận Hành');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại NPL');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Vận Hành/Mặt');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Mực In/Lần In');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức TG Canh Bài');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức NPL Canh Bài');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng NPL');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng TG Canh Bài');


                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '6', 'Mặt 2');
                    $objPHPExcel->getActiveSheet()->mergeCells($excel[$iExcel] . '6:' . $excel[($iExcel + 10)] . '6');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[$iExcel] . '7', 'Số Con/Tờ Vận Hành');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Loại NPL');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Số Lần Vận Hành/Mặt');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức Mực In/Lần In');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức TG Canh Bài');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Định Mức NPL Canh Bài');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng NPL');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng TG Canh Bài');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng NPL Mặt 1+Mặt 2');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Tổng TG Canh Bài Mặt 1+Mặt 2');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Giá/lần In');
                    $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . '7', 'Đơn Giá/tờ');
                }
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$iExcel] . '7')->applyFromArray([
            'font' => array(
                'bold' => true,
            ),
        ]);
        $isExcelLast = $iExcel;


        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $iExcel = -1;
                $rowBegin++;
                $rowOne = $rowBegin;

                $data_price_json = !empty($value['data_price_json']) ? json_decode($value['data_price_json'], true) : null;
                // print_arrays($data_price_json);

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, $value['code_category']);
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, $value['item_code']);
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, $value['item_name']);
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, $value['unit_name']);

                //height
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['height'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['corner_boundary_height'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['perpendicular_border_height'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['round_square_border_height'] ?? ''));

                //width
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['width'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['corner_boundary_width'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['perpendicular_border_width'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['round_square_border_width'] ?? ''));

                //Dàn trang
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['height_layout'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['height_layout_print_tweezers'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['height_layout_boong_cut'] ?? ''));

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['width_layout'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['width_layout_print_tweezers'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($data_price_json['width_layout_boong_cut'] ?? ''));

                $ItemsPrice = $data_price_json['ItemsPrice'] ?? null;
                $rowBeginPlus = 1;
                if ($ItemsPrice) {
                    $rowItemsPrice = $rowOne;
                    foreach ($ItemsPrice as $kI => $vI) {
                        $iExcelIP = $iExcel;
                        $type_price = $vI['type_price'];
                        $item_id_price = $vI['item_id_price'];
                        $stage_id_price = $vI['stage_id_price'];
                        $dtItem = [];
                        $dtMachine = $this->handling_price_model->rowMachines((!empty($vI['machine']) ? $vI['machine'] : ''));
                        $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                        if ($type_price == "materials") {
                            $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                        } else if ($type_price == "stages") {
                            $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                        }

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($type_price == 'materials' ? $dtItem['code'] : ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtStages['name'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quantity_color'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtMachine['name'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_npl'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_bom'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['price_about'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_sheet'] ?? 0)));
                        $rowItemsPrice++;
                    }

                    $ctItemsPrice = count($ItemsPrice);
                    if ($rowBeginPlus < $ctItemsPrice) {
                        $rowBeginPlus = $ctItemsPrice;
                    }
                }
                $iExcel = $iExcel + 8;

                //công đoạn trước in mặt sau
                $itemsPriceBackside = $data_price_json['itemsPriceBackside'] ?? null;
                if ($itemsPriceBackside) {
                    $rowItemsPrice = $rowOne;
                    foreach ($itemsPriceBackside as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $type_price = $vI['type_price_backside'];
                        $item_id_price = $vI['item_id_price_backside'];
                        $stage_id_price = $vI['stage_id_price_backside'];
                        $dtItem = [];
                        $dtMachine = $this->handling_price_model->rowMachines((!empty($vI['machine_backside']) ? $vI['machine_backside'] : ''));
                        $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                        if ($type_price == "materials") {
                            $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                        } else if ($type_price == "stages") {
                            $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                        }

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($type_price == 'materials' ? $dtItem['code'] : ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtStages['name'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quantity_color_backside'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtMachine['name'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_npl_backside'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_bom_backside'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['price_about_backside'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_sheet_backside'] ?? 0)));
                        $rowItemsPrice++;
                    }

                    $ctItemsPrice = count($itemsPriceBackside);
                    if ($rowBeginPlus < $ctItemsPrice) {
                        $rowBeginPlus = $ctItemsPrice;
                    }
                }
                $iExcel = $iExcel + 8;

                //công đoạn sau in
                $itemsStagesProducts = $data_price_json['itemsStagesProducts'] ?? null;
                if ($itemsStagesProducts) {
                    $rowItemsPrice = $rowOne;
                    foreach ($itemsStagesProducts as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $type_price = $vI['type_price_products'];
                        $item_id_price = $vI['item_id_price_products'];
                        $stage_id_price = $vI['stage_id_price_products'];
                        $dtItem = [];
                        // $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                        if ($type_price == "materials") {
                            $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                        } else if ($type_price == "stages") {
                            $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                        }

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtItem['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['long_height'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['width_horizontal'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['number_operate_products'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['face_products'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['price_about_products'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['total_sheet_products'] ?? ''));
                        $rowItemsPrice++;
                    }

                    $ctItems = count($itemsStagesProducts);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 7;

                //gia công vận chuyển
                $itemsGVC = $data_price_json['itemsGVC'] ?? null;
                if ($itemsGVC) {
                    $rowItemsPrice = $rowOne;
                    foreach ($itemsGVC as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_vc'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['unit_kg'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price_gvc'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['kg_child_gvc'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price_child_gvc'] ?? 0)));
                        $rowItemsPrice++;
                    }

                    $ctItems = count($itemsGVC);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 5;

                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, (formatMoney($data_price_json['cost_of_brand'] ?? 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, (formatMoney($data_price_json['labor_cost'] ?? 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, (formatMoney($data_price_json['loss_cost'] ?? 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, (formatMoney($data_price_json['profit'] ?? 0)));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, ($value['code_stage_quote'] ?? ''));
                $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcel] . $rowOne, (formatMoney($data_price_json['g'] ?? 0)));

                //Thông tin NPL
                // $iExcel--;
                $arrItemsNPL = $data_price_json['arrItemsNPL'] ?? null;
                if ($arrItemsNPL) {
                    $rowItemsPrice = $rowOne;
                    foreach ($arrItemsNPL as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $dtMaterial = $this->items_model->rowMaterial($vI['item_id']);

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtMaterial['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtMaterial['name'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (($vI['unit_measure_sp'] ?? '')));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (($vI['unit_calculation_sp'] ?? '')));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['leave_margin'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['leave_margin1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (($vI['unit_measure_sp1'] ?? '')));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (($vI['unit_calculation_sp1'] ?? '')));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['leave_tweezers'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['leave_discharge_w'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['leave_discharge_h'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_child_w'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_child_h'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_child_page'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price_page'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price_xlt'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['total_money'] ?? 0)));

                        $rowItemsPrice++;
                    }

                    $ctItems = count($arrItemsNPL);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 21;

                //Dàn trang
                $arrItemsLStage = $data_price_json['arrItemsLStage'] ?? null;
                if ($arrItemsLStage) {
                    $rowItemsPrice = $rowOne;
                    foreach ($arrItemsLStage as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        if ($vI['type'] == 1) {
                            $dtInfo = $this->products_model->rowStages($vI['item_id']);
                        } else {
                            $dtInfo = $this->category_model->rowMachines($vI['item_id']);
                        }

                        $dtMaterial = $this->items_model->rowMaterial($vI['item_id']);
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['name'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_child_print_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_color_print_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_npl_f1'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_zn_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_operations_page_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_zn_use_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_ctp_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_child_print_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_color_print_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_npl_f2'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_zn_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_operations_page_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_zn_use_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_ctp_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_operations_page'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_npl'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['total_price'] ?? 0)));

                        $rowItemsPrice++;
                    }

                    $ctItems = count($arrItemsLStage);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 23;

                //công đoạn kiểm
                $arrItemsIStage = $data_price_json['arrItemsIStage'] ?? null;
                if ($arrItemsIStage) {
                    $rowItemsPrice = $rowOne;
                    foreach ($arrItemsIStage as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $dtInfo = $this->products_model->rowCategoryStages($vI['category_stage_id']);

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['unit_f1'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_check_f1'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_o_side_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['productivity_norms_f1'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_check_f2'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_o_side_f2'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['productivity_norms_f2'] ?? 0)));

                        $rowItemsPrice++;
                    }

                    $ctItems = count($arrItemsIStage);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 10;

                //công đoạn phân trang
                $arrItemsPStage = $data_price_json['arrItemsPStage'] ?? null;
                if ($arrItemsPStage) {
                    $rowItemsPrice = $rowOne;
                    foreach ($arrItemsPStage as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $dtInfo = $this->products_model->rowCategoryStages($vI['category_stage_id']);

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['hight_bottom'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['unit'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_bales'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['bale_norms'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['productivity_norms'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['type_packaging'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_tem'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_tem'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_bale'] ?? 0)));
                        $rowItemsPrice++;
                    }

                    $ctItems = count($arrItemsPStage);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 12;

                //công đoạn mở phiếu giao hàng
                $arrItemsDStage = $data_price_json['arrItemsDStage'] ?? null;
                if ($arrItemsDStage) {
                    $rowItemsPrice = $rowOne;
                    foreach ($arrItemsDStage as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $dtInfo = $this->products_model->rowCategoryStages($vI['category_stage_id']);

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['hight_bottom'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['unit'] ?? 0));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_bales'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['bale_norms'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['productivity_norms'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_packaging'] ?? 0));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_tem'] ?? 0));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_tem'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_bale'] ?? 0)));

                        $rowItemsPrice++;
                    }

                    $ctItems = count($arrItemsDStage);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 12;

                //công đoạn điều xe
                $arrItemsCStage = $data_price_json['arrItemsCStage'] ?? null;
                if ($arrItemsCStage) {
                    $rowItemsPrice = $rowOne;
                    foreach ($arrItemsCStage as $kI => $vI) {
                        $iExcelIP = $iExcel;

                        $dtInfo = $this->products_model->rowCategoryStages($vI['category_stage_id']);

                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['code'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['transportation'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['unit'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price_delivery'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_bale'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['subtotal'] ?? 0)));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['supplier'] ?? ''));
                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['address_delivery'] ?? ''));

                        $rowItemsPrice++;
                    }

                    $ctItems = count($arrItemsCStage);
                    if ($rowBeginPlus < $ctItems) {
                        $rowBeginPlus = $ctItems;
                    }
                }
                $iExcel = $iExcel + 8;

                //công đoạn động
                $arrItemsPsStage = $arrItemsCStage = $data_price_json['arrItemsPsStage'] ?? null;
                if (!empty($dtStagesCustom)) {
                    foreach ($dtStagesCustom as $kS => $vS) {

                        foreach ($arrItemsPsStage as $kIS => $vIS) {
                            if ($vS['id'] == $vIS['stage_id']) {
                                $itemsS = $vIS['itemsS'];
                                if ($itemsS) {
                                    $rowItemsPrice = $rowOne;
                                    foreach ($itemsS as $kI => $vI) {
                                        $iExcelIP = $iExcel;

                                        if ($vI['type'] == 1) {
                                            $dtInfo = $this->products_model->rowStages($vI['item_id']);
                                        } else {
                                            $dtInfo = $this->category_model->rowMachines($vI['item_id']);
                                        }

                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type'] ?? ''));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['code'] ?? ''));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($dtInfo['name'] ?? ''));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['height'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['width'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_operating_f1'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_npl_f1'] ?? ''));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_operating_side_f1'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['ink_f1'] ?? ''));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_time_f1'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_npl_f1'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_npl_f1'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_time_npl_f1'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_operating_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, ($vI['type_npl_f2'] ?? ''));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['number_operating_side_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['ink_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_time_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['quota_npl_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_npl_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_time_npl_f2'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_npl_f12'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatNumber($vI['total_time_f12'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price'] ?? 0)));
                                        $objPHPExcel->getActiveSheet()->setCellValue($excel[++$iExcelIP] . $rowItemsPrice, (formatMoney($vI['price_page'] ?? 0)));




                                        $rowItemsPrice++;
                                    }

                                    $ctItems = count($itemsS);
                                    if ($rowBeginPlus < $ctItems) {
                                        $rowBeginPlus = $ctItems;
                                    }
                                }

                                break;
                            }
                        }

                        $iExcel = $iExcel + 25;
                    }
                }

                $rowBegin = $rowBegin + $rowBeginPlus;
                // print_arrays($data_price_json);
            }
        }

        $objPHPExcel->getActiveSheet()->getStyle('A5:' . $excel[$isExcelLast] . ($rowBegin))->applyFromArray([
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
        ]);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . $excel[$isExcelLast] . ($rowBegin))->getAlignment()->setWrapText(true);

        // print_arrays($items);

        foreach ($excel as $key => $value) {
            if ($value == 'A') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(10);
            } else if ($value == 'BD') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(15);
            } else {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(15);
            }
        }

        $title = 'quotes';
        $filename = $quote['reference_no'] . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function searchQuotationRequest($id = 0)
    {
        $data = [];
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $client_id = $params['client_id'] ?? 0;
        $client_id = str_replace('customers__', '', $client_id);

        $this->db->select('
            tblquotation_request.id,
            tblquotation_request.code as text,
        ', false);
        $this->db->from('tblquotation_request');
        $this->db->where('tblquotation_request.client_id', $client_id);
        $this->db->limit($limit);
        $this->db->order_by('tblquotation_request.id DESC');
        $data['results'] = $this->db->get()->result_array();

        if ($id) {
            $quotation_request = get_table_where('tblquotation_request', ['id' => $id], '', 'row_array', '', 'id, code');
            $data['row'] = ['id' => $quotation_request['id'], 'text' => $quotation_request['code']];
        }
        echo json_encode($data);
    }
}
