<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contracts_sales extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if (!has_permission('contracts_sales', '', 'view') && !has_permission('contracts_sales', '', 'view_own')) {
            access_denied('contracts_sales');
        }

        $data['tnh'] = true;
        $data['title'] = _l('contracts_sales');
        $this->load->view('admin/contracts_sales/manage', $data);
    }

    public function table()
    {
        if (!has_permission('contracts_sales', '', 'view')) {
                ajax_access_denied();
        }
        $this->app->get_table_data('contracts_sales');
    }

    public function table_contracts_sales_template()
    {
        $this->app->get_table_data('contracts_sales_template');
    }
    public function convert_contract($id='')
    {
            $ktr = get_table_where('tbl_contracts_sales',array('quote_id'=>$id),'','row');
            if (!empty($ktr)) {
                set_alert('warning', _l('Đơn hàng này đã tồn tại hợp đồng bán!'));
                redirect(admin_url('orders'));
            }
            $quote = get_table_where('tbl_orders',array('id'=>$id),'','row');
            $template = get_table_where('tbl_contracts_sales_template',array('id'=>1),'','row');
            $dataPost = $this->input->post();
                $content_pdf = '';
                if(!empty($template->content)) {
                    $content_pdf = $template->content;
                }
                $in = array(
                    'quote_id' => $id,
                    'prefix' => get_option('contracts_sales'),
                    'code' => sprintf('%06d', ch_getMaxID('id', 'tbl_contracts_sales') + 1),
                    'create_by' => get_staff_user_id(),
                    'date_create' => date('Y-m-d'),
                    'subject' => $dataPost['subject'],
                    'customer_id' => $quote->customer_id,
                    'tax' => $quote->tax_rate,
                    'arr_staff' => implode(',',$dataPost['arr_staff']),
                    'amount' => $quote->grand_total,
                    'date_start' => to_sql_date($dataPost['date_start']),
                    'date_end' => to_sql_date($dataPost['date_end']),
                    'description' => $dataPost['description'],
                    'content_pdf' => $content_pdf
                );
        $this->db->insert('tbl_contracts_sales', $in);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->where('id', $id);
            $update = $this->db->update('tbl_orders', array('contract_id'=>$insert_id));
            $items = get_table_where('tbl_order_items',array('order_id'=>$id));
            foreach ($items as $key => $value) {
                $item['contract_id'] = $insert_id;
                $item['type_item'] = $value['type_item'];
                $item['item_id'] = $value['item_id'];
                $item['item_code'] = $value['item_code'];
                $item['item_name'] = $value['item_name'];
                $item['unit_id'] = $value['unit_id'];
                $item['quantity'] = $value['quantity'];
                $item['unit_price'] = $value['price'];
                $item['total_amount'] = $value['total_amount'];
                $item['note_item'] = $value['note_item'];
                $this->db->insert('tbl_contract_items', $item);
            }
            // $charge = get_table_where('tbl_quote_charges',array('quote_id'=>$id));
            // foreach ($charge as $key => $value) {
            //     $charges['contract_id'] = $insert_id;
            //     $charges['name_charge'] = $value['name_charge'];
            //     $charges['quantity_charge'] = $value['quantity_charge'];
            //     $charges['price_charge'] = $value['price_charge'];
            //     $charges['total_amount_charge'] = $value['total_amount_charge'];
            //     $this->db->insert('tbl_contract_charges', $charges);
            // }
            set_alert('success', _l('ch_added_successfuly'));
            redirect(admin_url('contracts_sales/detail/'.$insert_id));
        }
        // $this->db->insert('tbl_contracts_sales', $in);
    }
    public function detail($id='')
    {
		if(empty($id)) {
			if (!has_permission('contracts_sales', '', 'create')) {
				access_denied('contracts_sales');
			}
		}
		else {
			if (!has_permission('contracts_sales', '', 'edit')) {
				access_denied('contracts_sales');
			}
		}
        if ($this->input->post()) {
            $dataPost = $this->input->post();
            if ($id == '') {
                $template = get_table_where('tbl_contracts_sales_template',array('id'=>1),'','row');
                $content_pdf = '';
                if(!empty($template->content)) {
                    $content_pdf = $template->content;
                }
                $in = array(
                    'prefix' => $dataPost['prefix'],
                    'code' => $dataPost['code'],
                    'create_by' => get_staff_user_id(),
                    'date_create' => date('Y-m-d'),
                    'subject' => $dataPost['subject'],
                    'customer_id' => $dataPost['customer_id'],
                    'arr_staff' => implode(',',$dataPost['arr_staff']),
                    'amount' => str_replace(',','',$dataPost['amount']),
                    'date_start' => to_sql_date($dataPost['date_start']),
                    'date_end' => to_sql_date($dataPost['date_end']),
                    'description' => $dataPost['description'],
                    'content_pdf' => $content_pdf
                );
                $this->db->insert('tbl_contracts_sales', $in);
                $insert_id = $this->db->insert_id();

                if ($insert_id) {
                    set_alert('success', _l('ch_added_successfuly'));
                    redirect(admin_url('contracts_sales/detail/'.$insert_id));
                }
            }
            else {
                $in = array(
                    'subject' => $dataPost['subject'],
                    'customer_id' => $dataPost['customer_id'],
                    'arr_staff' => implode(',',$dataPost['arr_staff']),
                    'amount' => str_replace(',','',$dataPost['amount']),
                    'date_start' => to_sql_date($dataPost['date_start']),
                    'date_end' => to_sql_date($dataPost['date_end']),
                    'description' => $dataPost['description']
                );
                $this->db->where('id', $id);
                $update = $this->db->update('tbl_contracts_sales', $in);
                if ($update) {
                    set_alert('success', _l('ch_updated_successfuly'));
                    redirect(admin_url('contracts_sales/detail/'.$id));
                }
            }
        }
        $data['clients'] = array();
        if ($id == '') {
            $title = _l('create_contracts_sales');
        }
        else {
            $data['dataMain'] = get_table_where('tbl_contracts_sales',array('id'=>$id),'','row');
            $data['clients'] = get_options_search_cbo('customer', $data['dataMain']->customer_id);

            $data['content_data'] = $this->get_content_data($id);
            $data['id'] = $id;
            $title = _l('edit_contracts_sales');
        }

        $this->db->select('tblstaff.staffid, CONCAT(firstname," ",lastname) as name');
        $this->db->from('tblstaff');
        $data['staff'] = $this->db->get()->result_array();

        $data['title'] = $title;
        $this->load->view('admin/contracts_sales/detail', $data);
    }

    public function template()
    {
        $data['title']     = _l('contracts_sales_template');
        $data['templates'] = get_table_where('tbl_contracts_sales_template');

        $this->load->view('admin/contracts_sales/templates', $data);
    }

    public function template_detail($id='')
    {
        if ($this->input->post()) {
            if(is_numeric($id)) {
                $this->db->where('id',$id);
                $success = $this->db->update('tbl_contracts_sales_template',$this->input->post(NULL, FALSE));
                if ($success) {
                    set_alert('success', _l('ch_added_successfuly'));
                }
            }
            else
            {
                $data=$this->input->post(NULL, FALSE);
                $data['create_by']=get_staff_user_id();
                $this->db->insert('tbl_contracts_sales_template',$data);
                $id =$this->db->insert_id();
                if ($id) {
                    set_alert('success', _l('ch_updated_successfuly'));
                }
            }
            redirect(admin_url('contracts_sales/template_detail/' . $id));
        }
        if($id == '') {
            $data['title'] = _l('add_templates');
        }
        else {
            $data['title'] = _l('edit_template');
        }
        $data['template'] = get_table_where('tbl_contracts_sales_template',array('id'=>$id),'','row');

        $this->load->view('admin/contracts_sales/template_detail', $data);
    }

    public function get_content_data($id,$_content="",$type=0)
    {
        if(is_numeric($id))
        {
            $result = get_table_where('tbl_contracts_sales',array('id'=>$id),'','row');
            $get_client = get_table_where('tblclients',array('userid'=>$result->customer_id),'','row');

            // lấy template mẫu
            $template = get_table_where('tbl_contracts_sales_template',array('id'=>1),'','row');
            if(!empty($template->content)) {
                $content = $template->content;
            }
            // end
            // thay thế nếu có
            if(!empty($result->content_pdf))
            {
                $content = $result->content_pdf;
            }
            // end
            //seller
            $namePC = $result->prefix.$result->code;
            $content = str_replace('{name_contracts_sale}', $namePC,$content);
            $content = str_replace('{date_contracts_sale}', _d($result->date_create),$content);
            $content = str_replace('{name_seller}', get_option('invoice_company_name'),$content);
            $content = str_replace('{address_seller}', get_option('invoice_company_address'),$content);
            $content = str_replace('{tel_seller}', get_option('invoice_company_phonenumber'),$content);
            $content = str_replace('{bank_name_seller}', get_option('bank_name'),$content);
            $content = str_replace('{beneficiary_seller}', get_option('beneficiary'),$content);
            $content = str_replace('{address_bank_seller}', get_option('address_bank'),$content);
            $content = str_replace('{account_seller}', get_option('account_no'),$content);
            $content = str_replace('{swift_seller}', get_option('swift_codes'),$content);

            //buyer
            $content = str_replace('{name_buyer}', $get_client->company_short,$content);
            $content = str_replace('{address_buyer}', $get_client->address,$content);
//            $content = str_replace('{name_buyer}', $get_client->company_short,$content);
//            $content = str_replace('{address_buyer}', $get_client->address,$content);
            // $client_value_39 = get_table_where('tblclient_value',array('client'=>$result->customer_id,'id_detail'=>39),'','row');
            // if(!empty($client_value_39))
            // {
            // $content = str_replace('{bank_name_buyer}', $client_value_39->value,$content);
            // }else
            // {
            // $content = str_replace('{bank_name_buyer}', '-',$content);
            // }
            // $client_value_38 = get_table_where('tblclient_value',array('client'=>$result->customer_id,'id_detail'=>38),'','row');
            // if(!empty($client_value_38))
            // {
            // $content = str_replace('{address_bank_buyer}', $client_value_38->value,$content);
            // }else
            // {
            // $content = str_replace('{address_bank_buyer}', '-',$content);
            // }
            // $client_value_41 = get_table_where('tblclient_value',array('client'=>$result->customer_id,'id_detail'=>41),'','row');
            // if(!empty($client_value_41))
            // {
            // $content = str_replace('{benificiary_buyer}', $client_value_41->value,$content);
            // }else
            // {
            // $content = str_replace('{benificiary_buyer}', '-',$content);
            // }
            // $client_value_40 = get_table_where('tblclient_value',array('client'=>$result->customer_id,'id_detail'=>40),'','row');
            // if(!empty($client_value_40))
            // {
            // $content = str_replace('{account_buyer}', $client_value_40->value,$content);
            // }else
            // {
            // $content = str_replace('{account_buyer}', '-',$content);
            // }
            // $client_value_37 = get_table_where('tblclient_value',array('client'=>$result->customer_id,'id_detail'=>37),'','row');
            // if(!empty($client_value_37))
            // {
            // $content = str_replace('{swift_buyer}', $client_value_37->value,$content);
            // }else
            // {
            // $content = str_replace('{swift_buyer}', '-',$content);
            // }
            include APPPATH . 'third_party/NumbersWords/Numbers/Words.php';
            $words = new Numbers_Words();
            if (stripos($content, "{table_item}") !== false) {
                $table = '<table class="table" border="1" width="100%">
                                <tbody>
                                    <tr style="text-align:center;">
                                        <td style="width: 5%;"><span style="font-size: 10pt; font-weight: bold; text-align: center;">STT</span>
                                        </td>
                                        <td style="width: 25%;"><span style="font-size: 10pt; font-weight: bold;">Description of Goods</span>
                                        </td>
                                        <td style="width: 15%;"><span style="font-size: 10pt; font-weight: bold;">Unit</span>
                                        </td>
                                        <td style="width: 15%;"><span style="font-size: 10pt; font-weight: bold;">Quan’</span>
                                        </td>
                                        <td style="width: 15%;"><span style="font-size: 10pt; font-weight: bold;">Unit price (USD)</span>
                                        </td>
                                        <td style="width: 15%;"><span style="font-size: 10pt; font-weight: bold;">Total amount (USD)</span>
                                        </td>
                                        <td style="width: 10%;"><span style="font-size: 10pt; font-weight: bold;">Lead time (days)</span>
                                        </td>
                                    </tr>';
                                    // lặp sản phẩm
                                    $total = 0;
                                    $quantity_total = 0;
                                    $get_items = get_table_where('tbl_contract_items',array('contract_id'=>$id));
                                    foreach ($get_items as $key => $value) {
                                        $unit = get_table_where('tblunits',array('unitid'=>$value['unit_id']),'','row');
                                        $table .= '<tr>
                                                        <td style="width: 5%;"><span style="font-size: 10pt; text-align: center;">'.++$key.'</span>
                                                        </td>
                                                        <td style="width: 25%;"><span style="font-size: 10pt;">'.$value['item_name'].'</span>
                                                        </td>
                                                        <td style="width: 15%;"><span style="font-size: 10pt;">'.(!empty($unit ) ? $unit->unit : '').'</span>
                                                        </td>
                                                        <td style="width: 15%;text-align:center;"><span style="font-size: 10pt;">'.number_format($value['quantity']).'</span>
                                                        </td>
                                                        <td style="width: 15%;text-align:right;" class="text-right"><span style="font-size: 10pt;">'.number_format($value['unit_price']).'</span>
                                                        </td>
                                                        <td style="width: 15%;text-align:right;"><span style="font-size: 10pt;">'.number_format($value['total_amount']).'</span>
                                                        </td>
                                                        <td style="width: 10%;text-align:center;"><span style="font-size: 10pt;">'.$value['lead_time'].'</span>
                                                        </td>
                                                    </tr>';
                                        $total += $value['total_amount'];
                                        $quantity_total+= $value['quantity'];

                                    }
                            $table .= '<tr>
                                <td colspan="3"><span style="font-size: 10pt;font-weight: bold;">TOTAL EX-WORK</span>
                                </td>
                                <td style="text-align:center;">'.number_format($quantity_total).'</td>
                                <td ></td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">'.number_format($total).'</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                            $get_items2 = get_table_where('tbl_contract_charges',array('contract_id'=>$id));
                            foreach ($get_items2 as $key2 => $value2) {
                            $table .='<tr>
                                <td colspan="3"><span style="font-size: 10pt;font-weight: bold;">'.$value2['name_charge'].'</span>
                                </td>
                                <td style="text-align:center;">'.number_format($value2['quantity_charge']).'</td>
                                <td style="text-align:right;">'.number_format($value2['price_charge']).'</td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">'.number_format($value2['total_amount_charge']).'</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                            $total += $value2['total_amount_charge'];
                            }
                            $tax = $result->tax*$total/100;
                            if($tax > 0){
                            $table .='<tr>
                                <td colspan="5"><span style="font-size: 10pt;font-weight: bold;">TAX: </span>
                                </td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">'.number_format($tax).'</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                            }
                            $table .='<tr>
                                <td colspan="5"><span style="font-size: 10pt;font-weight: bold;">TOTAL CIF YANGON: <span style="text-transform: capitalize;">'.$words->toCurrency(($total+$tax) ,"en_US").'</span></span>
                                </td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">'.number_format($total+$tax).'</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                $table .= '</tbody>
                        </table>';


                $content=str_replace("{table_item}", $table,$content);
            }
            return $content;
        }
        return $content;
    }

    public function edit_pdf($id='')
    {
        $content = $this->input->post('content',false);
        $this->db->set('content_pdf',$content);
        $this->db->where('id',$id);
        $this->db->update('tbl_contracts_sales');
    }

    public function pdf($id)
    {
        ini_set('max_execution_time', 300);
        ob_start();
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $result = get_table_where('tbl_contracts_sales',array('id'=>$id),'','row');
        $contract = new stdClass();
        $contract->content = $this->get_content_data($id);
        $contract->custom = '';
        //tnh
        $quote_id = $result->quote_id;
        if ($quote_id > 0)
        {
            if (checkModule('quotes')) {
                $quote = $this->site_model->rowQuotesById($quote_id);
                if (!empty($quote)) {
                    $img = file_get_contents(module_dir_url(MODULE_NAME, 'assets/images/list_of_parts.png'));
                    $img = '<img style="width: 800px; !important" src="data:image/png;base64,'.base64_encode($img).'"/>';
                    $contract->content.= $img;
                    $contract->content.= tnh_html_entity_decode($quote['parts_origin']);
                    // $quoteItems = $this->site_model->getQuoteItemsByQuoteId($quote_id);
                    $quoteItems = $this->site_model->getQuoteItemsByQuoteId($quote_id);
                    if (!empty($quoteItems)) {
                        foreach ($quoteItems as $key => $value) {
                            $info = '<br><div>'.tnh_html_entity_decode($value['info']).'</div>';
                            // $info = '<div><table border="1" style="height: auto; width: 100%; border-style: solid;border-color: #000000;" cellpadding="0" cellspacing="0" height="44">
                            //     <tbody>
                            //     <tr style="height: 44px; background-color: #67adeb;">
                            //     <td colspan="2" width="693" style="width: 99.8341%; height: 44px;"><strong>ROOFING MACHINE asd&nbsp;</strong></td>
                            //     </tr>
                            //     <tr style="height: 17px;">
                            //     <td colspan="2" width="693" style="width: 99.8341%; height: 17px;"><img src="https://demo.fososoft.com/01419F/media/4%20angle.png" width="391" height="119" alt="" style="display: block; margin-left: auto; margin-right: auto;"></td>
                            //     </tr>
                            //     </tbody>
                            // </table></div>';
                            // $sHtml = preg_replace('/(<table|tr|td|tbody|thead)(.*)(width: [0-9]+px;)/i', '$1$2', $sHtml);
                            // $info = preg_replace('/(<table|tr|td|tbody)(.*)(width="[0-9]+")/i', '${1}${2}', $info);
                            // $info = preg_replace('/(width|height)="\d*"\s/', "", $info);
                            $info = preg_replace('/(width|height)="\d*"\s/', "", $info);
                            // $info = '<br><div>'.$value['info'].'</div>';
                            // $info = preg_replace('/(\<img[^>]+)(style\=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $info);
                            // print_arrays($info);
                            $html = $info;
                            $dom = new DOMDocument();
                            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                            $images = $dom->getElementsByTagName('img');
                            if (!empty($images))
                            {
                                foreach ($images as $image) {
                                    $src = $image->getAttribute('src');
                                    // $width = $image->getAttribute('width');
                                    // $height = $image->getAttribute('height');
                                    $type = pathinfo($src, PATHINFO_EXTENSION);
                                    $data = file_get_contents($src);
                                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    $image->setAttribute("src", $base64);
                                    // if ($width > 700) {
                                    // $image->setAttribute("style", "width: 100px; !important;");
                                    // $image->setAttribute("width", 100);
                                    // $image->setAttribute("height", 100);
                                    // }
                                }
                            }
                            $html = $dom->saveHTML();
                            // // echo $html; die;
                            $contract->content.= $html;
                            // $contract->content.= $info;
                        }
                    }
                    // echo $contract->content;
                    // die;
                }
            }
        }
        //end

        $pdf      = quote_detail_pdf($contract);

        $type     = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($result->prefix.$result->code) . '.pdf', $type);
    }
    public function delete($id='')
    {
        $contract = get_table_where('tbl_contracts_sales',array('id'=>$id),'','row');
        $this->db->where('id',$id);
        $response = $this->db->delete('tbl_contracts_sales');
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($response) {
            $this->db->where('contract_id',$id);
            $response = $this->db->delete('tbl_contract_charges');
            $this->db->where('contract_id',$id);
            $response = $this->db->delete('tbl_contract_items');
            $this->db->where('id', $contract->quote_id);
            $update = $this->db->update('tbl_orders', array('contract_id'=>0));
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
            ));
    }
    public function upload_file($id = '')
    {
        $return = handleid_upload_contracts_sale($id);
        if($return) {
            set_alert('success', _l('Tải lên thành công'));
            redirect(admin_url('contracts_sales/detail/'.$id));
        }
        else {
            set_alert('success', _l('Tải lên thất bại'));
            redirect(admin_url('contracts_sales/detail/'.$id));
        }
    }
    public function table_contracts_sales_file($id='')
    {
        $this->app->get_table_data('contracts_sales_file', array(
            'id' => $id
        ));
    }
    public function delete_file($id_contracts_sales='',$id='')
    {
        $get_name = get_table_where('tblcontracts_sales_file',array('id'=>$id),'','row');
        $folder = 'uploads/contracts_sales/'.$id_contracts_sales.'/'.$get_name->name;
        $files = glob($folder . '/*');
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }
        $this->db->where('id',$id);
        $this->db->delete('tblcontracts_sales_file');
        echo json_encode(array(
            'alert_type' => 'success',
            'message' => _l('ch_delete_successfuly')
        ));
    }

    function pdfSendMail($id)
    {
        ini_set('max_execution_time', 300);
        ob_start();
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $result = get_table_where('tbl_contracts_sales',array('id'=>$id),'','row');
        $contract = new stdClass();
        $contract->content = $this->get_content_data($id);
        $contract->custom = '';
        //tnh
        $quote_id = $result->quote_id;
        if ($quote_id > 0)
        {
            if (checkModule('quotes')) {
                $quote = $this->site_model->rowQuotesById($quote_id);
                if (!empty($quote)) {
                    $img = file_get_contents(module_dir_url(MODULE_NAME, 'assets/images/list_of_parts.png'));
                    $img = '<img style="width: 800px; !important" src="data:image/png;base64,'.base64_encode($img).'"/>';
                    $contract->content.= $img;
                    $contract->content.= tnh_html_entity_decode($quote['parts_origin']);
                    $quoteItems = $this->site_model->getQuoteItemsByQuoteId($quote_id);
                    foreach ($quoteItems as $key => $value) {
                        $info = '<br><div>'.tnh_html_entity_decode($value['info']).'</div>';
                        # $info = preg_replace('/(\<img[^>]+)(style\=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $info);
                        $info = preg_replace('/(width|height)="\d*"\s/', "", $info);
                        $html = $info;
                        $dom = new DOMDocument();
                        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                        $images = $dom->getElementsByTagName('img');
                        if (!empty($images))
                        {
                            foreach ($images as $image) {
                                $src = $image->getAttribute('src');
                                $width = $image->getAttribute('width');
                                $height = $image->getAttribute('height');
                                $type = pathinfo($src, PATHINFO_EXTENSION);
                                $data = file_get_contents($src);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                $image->setAttribute("src", $base64);
                                // if ($width > 700) {
                                // $image->setAttribute("style", "width: 100px; !important;");
                                // $image->setAttribute("width", 100);
                                // $image->setAttribute("height", 100);
                                // }
                            }
                        }
                        $html = $dom->saveHTML();
                        $contract->content.= $html;
                    }
                }
            }
        }
        //end

        $pdf      = quote_detail_pdf($contract);
        $filename = base_url('file/contracts/');
        $type     = 'I';
        ob_clean();
        return $pdf->Output($filename.'contract.pdf', 'S');
        // return $fileatt;
        // print_arrays($fileatt);
        // $data = chunk_split( base64_encode(file_get_contents($fileatt)) );
        // $fileatt = $pdf->Output($filename.'contract.pdf', $type);
        // $data = chunk_split($fileatt);
        // print_arrays($fileatt);
    }

    public function sendMail($id)
    {
        $contract = get_table_where('tbl_contracts_sales', array('id' => $id), '', 'row');
        if ($this->input->post('send')) {
            $data = [];
            $this->form_validation->set_rules('title', lang("tnh_title_email"), 'required');
            $this->form_validation->set_rules('email', lang("Email"), 'required');
            if ($this->form_validation->run() == true)
            {
                $title = $this->input->post('title');
                $email = $this->input->post('email');
                $content = $this->input->post('content');

                $attachment = [
                    'base64' => $this->pdfSendMail($id),
                    'type' => 'attachment',
                    'name' => $contract->prefix.'-'.$contract->code,
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
            echo json_encode($data); die;
        } else {
            $customer = $this->site_model->rowCustomer($contract->customer_id);
            $data['contract'] = $contract;
            $data['customer'] = $customer;
            $data['id'] = $id;
            $this->load->view('admin/contracts_sales/send_email', $data);
        }
    }
}
