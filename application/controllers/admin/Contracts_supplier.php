<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contracts_supplier extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        if (!has_permission('contracts_supplier', '', 'view') && !has_permission('contracts_supplier', '', 'view_own')) {
            access_denied('contracts_supplier');
        }

        $data['title'] = _l('Danh Sách Hợp Đồng Mua');
        $this->load->view('admin/contracts_supplier/manage', $data);
    }

    public function table() {
        $aColumns = [
            'tbl_contracts_supplier.id as id',
            'tbl_contracts_supplier.code as code',
            'tblsuppliers.company as company',
            'tbl_contracts_supplier.subject as subject',
            'CONCAT(tblpurchase_order.prefix, "-", tblpurchase_order.prefix) as code_purchase_order',
            'tbl_contracts_supplier.amount as amount',
            'tbl_contracts_supplier.date_start as date_start',
            'tbl_contracts_supplier.date_end as date_end',
        ];
        $sIndexColumn = 'id';
        $sTable = 'tbl_contracts_supplier';
        $where = array();
        $join = array(
            'LEFT JOIN tblsuppliers ON tblsuppliers.id = tbl_contracts_supplier.supplier_id',
            'LEFT JOIN tblpurchase_order ON tblpurchase_order.id = tbl_contracts_supplier.purchase_order_id'
        );

        if (!has_permission('contracts_supplier', '', 'view')) {
            array_push($where, 'AND tbl_contracts_supplier.create_by = ' . get_staff_user_id());
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
            'tbl_contracts_supplier.code',
            'tbl_contracts_supplier.arr_staff',
        ));
        $output = $result['output'];
        $rResult = $result['rResult'];
        $currentPage = $this->input->post('start');
        $currentall = $output['iTotalRecords'];
        foreach ($rResult as $r => $aRow) {
            $row = [];
            $row[] = $aRow['id'];
            $row[] = $aRow['code'];
            $row[] = $aRow['company'];
            $row[] = $aRow['subject'];
            $row[] = $aRow['code_purchase_order'];
            $row[] = $aRow['amount'];
            $row[] = _d($aRow['date_start']);
            $row[] = _d($aRow['date_end']);

            $edit = '<a href="' . admin_url('contracts_supplier/detail/' . $aRow['id']) . '"><i class="fa fa-edit"></i> ' . _l('edit') . '</a>';
            if(!has_permission('contracts_supplier', '', 'edit')) {
                $edit = '';
            }

//            $printpdf = '<a class="text-danger delete-remind" href="' . admin_url('contracts_supplier/delete/' . $aRow['id']) . '"><i class="fa fa-remove"></i> ' . _l('delete') . '</a>';
            $printpdf = '';
            if(!has_permission('contracts_supplier', '', 'print')) {
                $printpdf = '<a href="' . admin_url('contracts_supplier/pdf/' . $aRow['id']) . '?print=true" target="_blank"><i class="fa fa-print"></i> ' . _l('print_contracts') . '</a>';
            }

            $delete = '<a class="text-danger delete-remind" href="' . admin_url('contracts_supplier/delete/' . $aRow['id']) . '"><i class="fa fa-remove"></i> ' . _l('delete') . '</a>';
            if(!has_permission('contracts_supplier', '', 'delete')) {
                $delete = '';
            }
            $_outputStatus = '<div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">' . _l('action') . '
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu h_right">
                                    <li>' . $edit . '</li>
                                    <li>' . $printpdf . '</li>
                                    <li>' . $delete . '</li>
                                </ul>
                            </div>';
            $row[] = $_outputStatus;
            $output['aaData'][] = $row;
        }

        echo json_encode($output);die();

    }

    public function detail($id = '')
    {
        if(empty($id)) {
            if (!has_permission('contracts_supplier', '', 'create')) {
                access_denied('contracts_supplier');
            }
        }
        else {
            if (!has_permission('contracts_supplier', '', 'edit')) {
                access_denied('contracts_supplier');
            }
        }
        if($this->input->post()) {
            $data = $this->input->post();
            if(!empty($id)) {
                $dataUpdate = [
                    'supplier_id' => $data['supplier_id'],
                    'subject' => $data['subject'],
                    'arr_staff' => !empty($data['arr_staff']) ? implode(',', $data['arr_staff']) : NULL,
                    'amount' => number_format_data($data['amount'], false),
                    'date_start' => to_sql_date($data['date_start']),
                    'date_end' => !empty($data['date_end']) ? to_sql_date($data['date_end']) : NULL,
                    'date_of_receipt' => !empty($data['date_of_receipt']) ? to_sql_date($data['date_of_receipt'], true) : NULL,
                    'description' => $this->input->post('description', false),
                ];
                $this->db->where('id', $id);
                $success = $this->db->update('tbl_contracts_supplier', $dataUpdate);
                if(!empty($success)) {
                    set_alert('success', _l('Cập nhật dữ liệu thành công'));
                    redirect(admin_url('contracts_supplier/detail/' . $id));
                }
                else {
                    set_alert('danger', _l('Cập nhật dữ liệu không thành công'));
                    redirect(admin_url('contracts_supplier/detail/' . $id));
                }
            }
            else {
                $template = $this->db->get_where('tbl_contracts_sales_template', ['id' => 2])->row();
                $content_pdf = '';
                if (!empty($template->content)) {
                    $content_pdf = $template->content;
                }
                $dataInsert = [
                    'code' =>  $data['prefix'] . $data['code'],
                    'supplier_id' => $data['supplier_id'],
                    'subject' => $data['subject'],
                    'arr_staff' => !empty($data['arr_staff']) ? implode(',', $data['arr_staff']) : NULL,
                    'amount' => number_format_data($data['amount'], false),
                    'date_start' => to_sql_date($data['date_start']),
                    'date_end' => !empty($data['date_end']) ? to_sql_date($data['date_end']) : NULL,
                    'date_of_receipt' => !empty($data['date_of_receipt']) ? to_sql_date($data['date_of_receipt'], true) : NULL,
                    'content_pdf' => $content_pdf,
                    'description' => $this->input->post('description', false),
                    'create_by' => get_staff_user_id(),
                ];
                $success = $this->db->insert('tbl_contracts_supplier', $dataInsert);
                if(!empty($success)) {
                    $id = $this->db->insert_id();
                    set_alert('success', _l('Thêm dữ liệu thành công'));
                    redirect(admin_url('contracts_supplier/detail/' . $id));
                }
                else {
                    set_alert('danger', _l('Thêm dữ liệu không thành công'));
                    redirect(admin_url('contracts_supplier/detail'));
                }
            }
        }
        else {
            $data['title'] = 'Thêm Hợp Đồng Mua';
            if (!empty($id)) {
                $data['title'] = 'Sửa Hợp Đồng Mua';
                $data['contracts_supplier'] = $this->db->get_where('tbl_contracts_supplier', ['id' => $id])->row();

                $data['contracts_supplier']->list_code = explode('-', $data['contracts_supplier']->code);
                $data['contracts_supplier']->prefix = $data['contracts_supplier']->list_code[0];
                $data['contracts_supplier']->code = $data['contracts_supplier']->list_code[1];
                $data['content_data'] = $this->get_content_data($id);
            }

            $this->db->select('id, code, company');
            $data['list_suppliers'] = $this->db->get_where('tblsuppliers')->result_array();


            $this->db->select('staffid, CONCAT(firstname, " ", lastname) as name');
            $data['list_staff'] = $this->db->get_where('tblstaff', ['active' => 1])->result_array();
            $this->load->view('admin/contracts_supplier/detail', $data);
        }
    }

    public function get_content_data($id, $_content = "", $type = 0)
    {
        if (is_numeric($id)) {
            $result = get_table_where('tbl_contracts_supplier', array('id' => $id), '', 'row');
            $get_supplier = get_table_where('tblsuppliers', array('id' => $result->supplier_id), '', 'row');
            $template = get_table_where('tbl_contracts_sales_template', ['id' => 2], '', 'row');
            if (!empty($template->content)) {
                $content = $template->content;
            }
            if (!empty($result->content_pdf)) {
                $content = $result->content_pdf;
            }
            // end
            //seller
            $namePC = $result->code;
            $content = str_replace('{name_contracts}', $namePC, $content);
            $content = str_replace('{date_contracts}', _d($result->date_create), $content);

            $content = str_replace('{name_seller}', $get_supplier->company, $content);
            $content = str_replace('{address_seller}', $get_supplier->address, $content);

            //buyer
            $content = str_replace('{name_buyer}', get_option('invoice_company_name'), $content);
            $content = str_replace('{address_buyer}', get_option('invoice_company_address'), $content);
            $content = str_replace('{tel_buyer}', get_option('invoice_company_phonenumber'), $content);
            $content = str_replace('{bank_name_buyer}', get_option('bank_name'), $content);
            $content = str_replace('{beneficiary_buyer}', get_option('beneficiary'), $content);
            $content = str_replace('{address_bank_buyer}', get_option('address_bank'), $content);
            $content = str_replace('{account_buyer}', get_option('account_no'), $content);
            $content = str_replace('{swift_buyer}', get_option('swift_codes'), $content);

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
                $get_items = get_table_where('tbl_contract_items', array('contract_id' => $id));
                foreach ($get_items as $key => $value) {
                    $unit = get_table_where('tblunits', array('unitid' => $value['unit_id']), '', 'row');
                    $table .= '<tr>
                                                        <td style="width: 5%;"><span style="font-size: 10pt; text-align: center;">' . ++$key . '</span>
                                                        </td>
                                                        <td style="width: 25%;"><span style="font-size: 10pt;">' . $value['item_name'] . '</span>
                                                        </td>
                                                        <td style="width: 15%;"><span style="font-size: 10pt;">' . (!empty($unit) ? $unit->unit : '') . '</span>
                                                        </td>
                                                        <td style="width: 15%;text-align:center;"><span style="font-size: 10pt;">' . number_format($value['quantity']) . '</span>
                                                        </td>
                                                        <td style="width: 15%;text-align:right;" class="text-right"><span style="font-size: 10pt;">' . number_format($value['unit_price']) . '</span>
                                                        </td>
                                                        <td style="width: 15%;text-align:right;"><span style="font-size: 10pt;">' . number_format($value['total_amount']) . '</span>
                                                        </td>
                                                        <td style="width: 10%;text-align:center;"><span style="font-size: 10pt;">' . $value['lead_time'] . '</span>
                                                        </td>
                                                    </tr>';
                    $total += $value['total_amount'];
                    $quantity_total += $value['quantity'];
                }
                $table .= '<tr>
                                <td colspan="3"><span style="font-size: 10pt;font-weight: bold;">TOTAL EX-WORK</span>
                                </td>
                                <td style="text-align:center;">' . number_format($quantity_total) . '</td>
                                <td ></td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">' . number_format($total) . '</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                $get_items2 = get_table_where('tbl_contract_charges', array('contract_id' => $id));
                foreach ($get_items2 as $key2 => $value2) {
                    $table .= '<tr>
                                <td colspan="3"><span style="font-size: 10pt;font-weight: bold;">' . $value2['name_charge'] . '</span>
                                </td>
                                <td style="text-align:center;">' . number_format($value2['quantity_charge']) . '</td>
                                <td style="text-align:right;">' . number_format($value2['price_charge']) . '</td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">' . number_format($value2['total_amount_charge']) . '</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                    $total += $value2['total_amount_charge'];
                }
                $tax = $result->tax * $total / 100;
                if ($tax > 0) {
                    $table .= '<tr>
                                <td colspan="5"><span style="font-size: 10pt;font-weight: bold;">TAX: </span>
                                </td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">' . number_format($tax) . '</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                }
                $table .= '<tr>
                                <td colspan="5"><span style="font-size: 10pt;font-weight: bold;">TOTAL CIF YANGON: <span style="text-transform: capitalize;">' . $words->toCurrency(($total + $tax), "en_US") . '</span></span>
                                </td>
                                <td style="text-align:right;"><span style="font-size: 10pt; font-weight: bold;">' . number_format($total + $tax) . '</span>
                                </td>
                                <td><span style="font-size: 10pt; font-weight: bold;"></span>
                                </td>
                            </tr>';
                $table .= '</tbody>
                        </table>';
                $content = str_replace("{table_item}", $table, $content);
            }
            return $content;
        }
        return $_content;
    }

    public function edit_pdf($id = '')
    {
        $content = $this->input->post('content', false);
        $this->db->set('content_pdf', $content);
        $this->db->where('id', $id);
        $this->db->update('tbl_contracts_supplier');
    }

    public function pdf($id)
    {
        if(!has_permission('contracts_supplier', '', 'print')) {
            access_denied('contracts_supplier');
        }

        ini_set('max_execution_time', 300);
        ob_start();
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $result = get_table_where('tbl_contracts_supplier', array('id' => $id), '', 'row');
        $contract = new stdClass();
        $contract->content = $this->get_content_data($id);
        $contract->custom = '';
        $pdf = quote_detail_pdf($contract);
        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($result->code) . '.pdf', $type);
    }

    public function delete($id = '')
    {

        if (!has_permission('contracts_supplier', '', 'delete')) {
            ajax_access_denied('contracts_supplier');
        }
//        $contract = get_table_where('tbl_contracts_supplier', array('id' => $id), '', 'row');
        $this->db->where('id', $id);
        $response = $this->db->delete('tbl_contracts_supplier');
        $alert_type = 'warning';
        $message = _l('ch_no_delete');
        if ($response) {
            $list_get_name = $this->db->get_where('tblfiles', [
                'rel_id' => $id,
                'rel_type' => 'contracts_supplier'
            ])->result_array();
            if(!empty($list_get_name)) {
                $folder = get_upload_path_by_type('contracts_supplier') . $id . '/';
                $files = glob($folder . '/*');
                foreach($files as $file){
                    if(is_file($file)){
                        unlink($file);
                    }
                }
                $this->db->where('rel_id', $id);
                $this->db->where('rel_type', 'contracts_supplier');
                $this->db->delete('tblfiles');
            }
            $alert_type = 'success';
            $message = _l('ch_delete');
        }
        echo json_encode(array('alert_type' => $alert_type, 'message' => $message));
    }

    public function upload_file($id = '')
    {
        $return = $this->upload_file_contract_supplier($id);
        if ($return) {
            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Tải file lên thành công']);die();
        }
        else {
            echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Tải file lên không thành công']);die();
        }
    }

    public function upload_file_contract_supplier($id_contracts_supplier = '')
    {
        $path = get_upload_path_by_type('contracts_supplier') . $id_contracts_supplier . '/';
        if (isset($_FILES['file']['name'])) {
            $tmpFilePath = $_FILES['file']['tmp_name'];
            $attachment = [];
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . 'index.html', 'w');
                }
                $filename    = vn_to_str($_FILES['file']['name']);
                $newFilePath = $path . $filename;
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment[] = array(
                        'file_name' => $filename,
                        'filetype' => $_FILES["file"]["type"],
                        'rel_id' => $id_contracts_supplier,
                        'rel_type' => 'contracts_supplier',
                        'staffid' => get_staff_user_id(),
                        'dateadded' => date('Y-m-d H:i:s'),
                    );
                }
            }
            if(!empty($attachment)) {
                $this->db->insert_batch('tblfiles', $attachment);
                return true;
            }
        }
        return false;
    }

    public function table_contracts_supplier_file($id = '')
    {
        $aColumns = [
            'file_name',
            'dateadded',
            '3'
        ];
        $sIndexColumn = 'id';
        $sTable       = 'tblfiles';
        $join         = array();
        $where        = array();
        array_push($where, 'AND tblfiles.rel_id = ' . $id);
        array_push($where, 'AND tblfiles.rel_type = "contracts_supplier"');
        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
            'id',
            'rel_id'
        ));
        $output  = $result['output'];
        $rResult = $result['rResult'];
        foreach ($rResult as $r => $aRow) {
            $row = [];
            for ($i = 0 ; $i < count($aColumns) ; $i++) {
                $_data = $aRow[$aColumns[$i]];
                if ($aColumns[$i] == 'file_name') {
                    $_data = '<a target="_blank" href="' . base_url('uploads/contracts_supplier/' . $aRow['rel_id'] . '/' . $aRow['file_name']) . '">'.$aRow['file_name'].'</a>';
                }
                else if ($aColumns[$i] == 'dateadded') {
                    $_data = _dt($aRow['dateadded']);
                }
                else if ($aColumns[$i] == '3') {
                    $_data = '<a class="btn btn-danger" onclick="delete_file('.$aRow['rel_id'].','.$aRow['id'].'); return false;"><i class="fa fa-times"></i></a>';
                }
                $row[] = $_data;
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function delete_file($rel_id = '', $id = '')
    {
        if (!has_permission('contracts_supplier', '', 'edit')) {
            ajax_access_denied('contracts_supplier');
        }
        $get_name = $this->db->get_where('tblfiles', [
            'rel_id' => $rel_id,
            'id' => $id,
            'rel_type' => 'contracts_supplier'
        ])->row();
        if(!empty($get_name)) {
            $files = 'uploads/contracts_supplier/' . $rel_id . '/' . $get_name->file_name;
            if (file_exists($files)) {
                unlink($files);
            }
            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
        }
        echo json_encode(array('alert_type' => 'success', 'message' => _l('ch_delete_successfuly')));die();
    }
}
