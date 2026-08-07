<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chatbot extends AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->api_key = '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';
        $this->load->model('chatbot_model');
        $this->load->model('AskGPT_model');
        $this->load->model('suggest_questions_model');
        $this->load->model('products_model');
        $this->load->model('unit_model');
        $this->load->model('orders_model');
        $this->load->model('site_model');
    }
    public function get_customers()
    {
        $keyword = $this->input->get('q');
        $this->db->like('company', $keyword);
        $query = $this->db->get('tblclients');
        $result = $query->result_array();

        $data = array_map(function ($row) {
            return $row['company'];
        }, $result);

        echo json_encode($data);
    }
    function test()
    {
        $this->load->view('admin/chatbot/excel_view');
    }
    public function index()
    {
        if (!is_admin()) {
            access_denied('chatbot');
        }
        $data['title'] = _l('Phân tích dữ liệu AI');
        // B1: Lấy session_id từ session PHP nếu chưa truyền từ URL
        $userid = get_staff_user_id();

        if (!isset($session_id) && $this->session->has_userdata('current_session_id_' . $userid)) {
            $session_id = $this->session->userdata('current_session_id_' . $userid);
        }

        // B2: Nếu vẫn chưa có → tạo mới
        if (!$session_id) {
            $session_id = $this->chatbot_model->create_session();
        } else {
            // B3: Kiểm tra session có message chưa
            $has_messages = $this->chatbot_model->check_session_has_messages($session_id);
            if ($has_messages) {
                $session_id = $this->chatbot_model->create_session(); // tạo mới nếu đã có chat
            }
        }

        // Lưu lại session_id vào session
        $this->session->set_userdata('current_session_id_' . $userid, $session_id);

        // Tiếp tục như cũ
        $messages = $this->chatbot_model->get_messages($session_id);
        $sessions = $this->chatbot_model->get_all_sessions_with_messages($userid);

        $data['session_id'] = $session_id;
        // $data['messages'] = $messages;
        $data['messages'] = [];
        // $data['sessions'] = $sessions
        $data['sessions'] = [];

        $this->load->view('admin/chatbot/manage', $data);
    }
    public function send_message()
    {
        $session_id = $this->input->post('session_id');
        $message = $this->input->post('message');

        $this->load->model('chatbot_model');
        $this->chatbot_model->save_message($session_id, 'user', $message);

        // Giả lập phản hồi bot
        $bot_reply = "GPT trả lời: " . $message;
        $this->chatbot_model->save_message($session_id, 'bot', $bot_reply);

        echo json_encode(['reply' => $bot_reply]);
    }
    public function get_session_messages()
    {
        $session_id = $this->input->post('session_id');
        $this->load->model('chatbot_model');
        $messages = $this->chatbot_model->get_messages($session_id);
        $messages = [];
        echo json_encode(['messages' => $messages]);
    }
    public function fetch_greeting_flow()
    {
        $this->load->model('chatbot_model');

        $modules = $this->chatbot_model->get_modules();
        echo json_encode([
            'bot' => 'Vui lòng chọn phân hệ:',
            'modules' => $modules
        ]);
    }

    public function fetch_groups_questions()
    {
        $module_id = $this->input->post('module_id');
        $this->load->model('chatbot_model');

        $groups = $this->chatbot_model->get_groups_with_questions($module_id);
        echo json_encode([
            'bot' => 'Dưới đây là các nhóm câu hỏi:',
            'groups' => $groups
        ]);
    }

    public function fetch_staff()
    {
        $question_id = $this->input->post('question_id');
        $this->load->model('chatbot_model');

        $is_required = $this->chatbot_model->is_question_require_staff($question_id);
        if ($is_required) {
            $staff = $this->chatbot_model->get_all_staff();
            echo json_encode([
                'require' => true,
                'staff' => $staff
            ]);
        } else {
            echo json_encode(['require' => false]);
        }
    }
    public function fetch_requirements()
    {
        $question_id = $this->input->post('question_id');
        $response = [
            'require_staff' => false,
            'require_date' => false,
            'require_detail' => false,
            'require_file' => false,
            'staff' => [],
        ];

        // 🔍 Kiểm tra xem câu hỏi có cần chọn nhân viên không
        $require_staff = $this->db->get_where('suggest_questions', [
            'id' => $question_id,
            'require_staff' => 1
        ])->row();

        if ($require_staff) {
            $response['require_staff'] = true;

            // Lấy danh sách nhân viên (tùy theo logic bạn có thể lọc theo quyền, phòng ban, trạng thái,...)
            $staffs = $this->db->select('staffid, firstname, lastname')
                ->from('tblstaff')
                ->where('active', 1)
                ->get()
                ->result();

            $response['staff'] = $staffs;
        }

        // 🔍 Kiểm tra xem câu hỏi có yêu cầu chọn khoảng thời gian không
        $require_date = $this->db->get_where('suggest_questions', [
            'id' => $question_id,
            'require_time' => 1
        ])->row();

        if ($require_date) {
            $response['require_date'] = true;
        }
        $require_detail = $this->db->get_where('suggest_questions', [
            'id' => $question_id,
            'require_detail' => 1
        ])->row();

        if ($require_detail) {
            $response['require_detail'] = true;
        }

        $require_file = $this->db->get_where('suggest_questions', [
            'id' => $question_id,
            'require_file' => 1
        ])->row();

        if ($require_file) {
            $response['require_file'] = true;
        }
        echo json_encode($response);
        exit;
    }
    public function process_question()
    {
        $session_id = $this->input->post('session_id');
        $question_text = $this->input->post('question_text');

        $excelData = [];

        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            require_once(APPPATH . 'third_party/PHPExcel/PHPExcel.php');
            $uploadPath = FCPATH . 'uploads/chatbot/' . $session_id . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $filename = time() . '_' . basename($_FILES['upload_file']['name']);
            $targetPath = $uploadPath . $filename;

            if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $targetPath)) {
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($targetPath);
                    $sheet = $objPHPExcel->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();
                    $highestColumn = $sheet->getHighestColumn();
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                    $excelData = [];
                    $headers = [];

                    for ($col = 0; $col < $highestColumnIndex; $col++) {
                        $headers[$col] = trim($sheet->getCellByColumnAndRow($col, 1)->getValue());
                    }

                    for ($row = 2; $row <= $highestRow; $row++) {
                        $rowData = [];
                        for ($col = 0; $col < $highestColumnIndex; $col++) {
                            $headerName = $headers[$col] ?: 'Cột ' . ($col + 1);
                            $cell = $sheet->getCellByColumnAndRow($col, $row);
                            $rawValue = $cell->getValue();

                            if (is_numeric($rawValue) && $rawValue > 25569 && $rawValue < 60000) {
                                try {
                                    $value = PHPExcel_Shared_Date::ExcelToPHPObject($rawValue)->format('Y-m-d');
                                } catch (Exception $e) {
                                    $value = $rawValue;
                                }
                            } else {
                                $value = trim((string)$rawValue);
                            }

                            $rowData[$headerName] = $value;
                        }
                        $excelData[] = $rowData;
                    }
                } catch (Exception $e) {
                    log_message('error', 'Lỗi khi đọc file Excel: ' . $e->getMessage());
                }
            }
        }
        $json = json_encode($excelData, JSON_UNESCAPED_UNICODE);
        $_data = $this->AskGPT_model->mapdata_order($json);
        $jsonData = $_data;

        $jsonData = preg_replace('/^```json\s*|\s*```$/s', '', $jsonData);

        // Chuyển JSON thành mảng PHP
        $arrayData = json_decode($jsonData, true);

        // Kiểm tra lỗi khi decode JSON
        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     echo "Lỗi khi chuyển JSON thành mảng: " . json_last_error_msg();
        //     exit;
        // }
        // Biến đổi JSON dữ liệu Excel
        // $json = json_encode($excelData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $client_id = $this->input->post('client_id');
        $item_id   = $this->input->post('item_id');
        $data_client = get_table_where('tblclients', ['userid' => $client_id], '', 'row_array');
        // $data_client = get_table_where('tblclients', ['userid' => $client_id], '', 'row_array');
        $info = $this->products_model->rowProduct($item_id);
        $conversion_quantity_unit = 1;
        $conversion_quantity_unit_default = 1;
        $quantity_child_sheet = $info['quantity_child_sheet'];
        $quantity_sheet_bale = $info['quantity_sheet_bale'];
        $loss = $info['loss'];
        $conversion_quantity_unit_default = $info['conversion_quantity_unit'];
        $items_code = $info['code'];
        $items_name = $info['name'];
        $product_name_customer = $info['product_name_customer'];
        $unit = $this->unit_model->rowUnit($info['unit_id']);

        $staff_id = get_staff_user_id();
        $this->db->select("tblbranch.name");
        $this->db->from('tblstaff_branch');
        $this->db->join('tblbranch', 'tblbranch.id = tblstaff_branch.id_branch', 'left');
        $this->db->where('staffid', $staff_id);
        $branchStaff = $this->db->get()->row_array();
        $dtTablePrice = $this->orders_model->getGroupPriceCustomer($client_id);
        $table_price_id = !empty($dtTablePrice) ? $dtTablePrice['id'] : 0;
        $_data = [];
        foreach ($arrayData as $key => $value) {
            $_data[$key] = [
                "date" => _d(date('Y-m-d')),
                "customer" => $data_client['company'],
                "person_contact" => "",
                "address_delivery" => "",
                "id_branch" => !empty($branchStaff) ? $branchStaff['name'] : '',
                "currencies" => "VND",
                "amount_to_vnd" => 1,
                "type_orders" => 1,
                "type_items" => 1,
                "status_orders" => 4,
                "employees" => get_staff_full_name(),
                "tax" => "",
                "cost_delivery" => "",
                "transporters" => "",
                "charge_party" => "",
                "note" => "",
                "item_code" => $items_code,
                "item_name" => $items_name,
                "product_name_customer" => $product_name_customer,
                "unit" => $unit['unit'],
                "date_ship" => _d(date('Y-m-d')),
                "order_code" => $value['order_code'],
                "command" => $value['command'],
                "quantity_put" => $value['quantity_put'],
                "quantity_loss" => 0,
                "sample_quantity_item" => 0,
                "total_quantity_item" => 0,
                "price" => 0,
                "amount" => 0,
                "date_delivery" => _d(date('Y-m-d')),
                "detail_delivery" => "",
                "note_item" => "",
                "so" => $value['so'],
                "pi" => $value['pi'],
                "po_style" => $value['po_style'],
                "item_code_tem" => ""
            ];
        }
        ob_start();
        $data['_data'] = $_data; // Mảng JSON Excel (PHP array)
        $this->load->view('admin/chatbot/excel_view', $data);
        $excelView = ob_get_clean();

        $reply = "<b>📊 Dữ liệu được trích xuất từ file Excel:</b><!--gpt-output-->" . $excelView;
        // Gửi phản hồi về giao diện
        echo json_encode(['reply' => $reply]);
    }
    public function delete_session()
    {
        $id = $this->input->post('session_id');
        $this->db->where('id', $id)->delete('chat_sessions');
        $this->db->where('session_id', $id)->delete('chat_messages');
        echo json_encode(['success' => true]);
    }
    public function process_customer_step1()
    {
        $keyword = $this->input->post('keyword');
        $results = $this->db->like('company', $keyword)
            ->or_like('userid', $keyword)
            ->limit(5)
            ->get(db_prefix() . 'clients')->result();

        if (count($results) == 0) {
            echo json_encode([
                'reply' =>
                "❌ Không tìm thấy khách hàng nào phù hợp.<br>
                ➕ <button class='add-flash-customer-ai'>Tạo mới khách hàng</button>"
            ]);
            return;
        }

        if (count($results) == 1) {
            $c = $results[0];
            echo json_encode([
                'reply' =>
                "✅ Đã tìm thấy khách hàng:<br>
                👤 <b>$c->company</b> (ID: $c->userid)<br>
                <button onclick=\"confirmCustomer($c->userid, '$c->company')\">✅ Xác nhận khách này</button>"
            ]);
            return;
        }

        $reply = "🔍 Có nhiều khách hàng phù hợp:<br>";
        foreach ($results as $c) {
            $reply .= "👤 <b>$c->company</b> (ID: $c->userid) 
            <button onclick=\"confirmCustomer($c->userid, '$c->company')\">Chọn</button><br>";
        }

        echo json_encode(['reply' => $reply]);
    }

    public function process_customer_step2()
    {
        $keyword = $this->input->post('keyword');
        $this->db->like('code', $keyword)
            ->or_like('name', $keyword);
        $results = $this->db->limit(5)->get(db_prefix() . '_products')->result();

        if (count($results) == 0) {
            echo json_encode([
                'reply' =>
                "❌ Không tìm thấy thành phẩm phù hợp. Vui lòng tìm mã khác<br>"
            ]);
            return;
        }

        if (count($results) == 1) {
            $p = $results[0];
            echo json_encode([
                'reply' =>
                "📦 Đã tìm thấy sản phẩm:<br>
                <b>$p->name</b> (Mã: $p->code)<br>
                <button onclick=\"confirmProduct($p->id, '$p->code')\">✅ Dùng sản phẩm này</button>"
            ]);
            return;
        }

        $reply = "🔍 Có nhiều sản phẩm phù hợp:<br>";
        foreach ($results as $p) {
            $reply .= "📦 <b>$p->name</b> (Mã: $p->code) 
            <button onclick=\"confirmProduct($p->id, '$p->code')\">Chọn</button><br>";
        }

        echo json_encode(['reply' => $reply]);
    }

    public function process_step3()
    {
        $client_id = $this->input->post('client_id');
        $item_id   = $this->input->post('item_id');

        if (!isset($_FILES['upload_file'])) {
            echo json_encode(['reply' => '❌ Không có file nào được tải lên.']);
            return;
        }

        // // Load thư viện xử lý Excel nếu bạn dùng PhpSpreadsheet
        // require_once(APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php');
        // use PhpOffice\PhpSpreadsheet\IOFactory;

        // $tmpPath = $_FILES['upload_file']['tmp_name'];
        // $spreadsheet = IOFactory::load($tmpPath);
        // $sheet = $spreadsheet->getActiveSheet();
        // $rows = $sheet->toArray();

        // Ví dụ: lấy 5 dòng đầu để hiển thị
        // $preview = array_slice($rows, 0, 5);
        $html = "<b>📊 Dữ liệu từ file Excel:</b><br><table border='1' cellpadding='5'>";
        // foreach ($preview as $row) {
        //     $html .= "<tr>";
        //     foreach ($row as $cell) {
        //         $html .= "<td>" . htmlentities($cell) . "</td>";
        //     }
        //     $html .= "</tr>";
        // }
        $html .= "</table>";

        echo json_encode(['reply' => $html]);
    }
    public function save_excel_data()
    {
        $dataJson = $this->input->post('data');
        $__data = json_decode($dataJson, true);

        if (!is_array($__data)) {
            echo json_encode(['status' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }
        $index_parent = 0;
        $index_parent_items = 0;
        $ref = '';
        $count = 0;
        $refItem = '';
        $dataImport = [];
        $errors = '';
        foreach ($__data as $key => $value) {
            $date = !empty($value['date']) ? to_sql_date($value['date']) : date('Y-m-d');
            $customer = $value['customer'];
            $person_contact = $value['person_contact'];
            $address_delivery = $value['address_delivery'];
            $id_branch = $value['id_branch'];
            $currencies = $value['currencies'];
            $amount_to_vnd = $value['amount_to_vnd'];
            $type_orders = $value['type_orders'];
            $type_items = $value['type_items'];
            $status_orders = $value['status_orders'];
            $employees = $value['employees'];
            $tax = $value['tax'];
            $cost_delivery = $value['cost_delivery'];
            $transporters = $value['transporters'];
            $charge_party = $value['charge_party'];
            $note = $value['note'];
            $item_code = $value['item_code'];
            $item_name = $value['item_name'];
            $discount_percent = 0;
            $discount_direct = 0;
            $product_name_customer = $value['product_name_customer'];
            $unit = $value['unit'];
            $date_ship = !empty($value['date_ship']) ? to_sql_date($value['date_ship']) : NULL;
            $order_code = $value['order_code'];
            $command = $value['command'];
            $quantity_put = $value['quantity_put'];
            $quantity_loss = $value['quantity_loss'];
            $sample_quantity_item = !empty($value['sample_quantity_item']) ? $value['sample_quantity_item'] : 0;
            $total_quantity_item = $value['total_quantity_item'];
            $price = $value['price'];
            $amount = $value['amount'];
            $date_delivery = !empty($value['date_delivery']) ? to_sql_date($value['date_delivery']) : NULL;
            $detail_delivery = $value['detail_delivery'];
            $note_item = $value['note_item'];
            $so = $value['so'];
            $pi = $value['pi'];
            $po_style = $value['po_style'];
            $item_code_tem = $value['item_code_tem'];
            $reference = 1;

            if (!empty($reference) && $reference != $ref) {
                $dataImport[$index_parent]['reference'] = $reference;
                $dataImport[$index_parent]['date'] = ($date);
                $dataImport[$index_parent]['customer'] = $customer;
                $dataImport[$index_parent]['person_contact'] = $person_contact;
                $dataImport[$index_parent]['address_delivery'] = $address_delivery;
                $dataImport[$index_parent]['id_branch'] = $id_branch;
                $dataImport[$index_parent]['currencies'] = $currencies;
                $dataImport[$index_parent]['amount_to_vnd'] = $amount_to_vnd;
                $dataImport[$index_parent]['type_orders'] = $type_orders;
                $dataImport[$index_parent]['type_items'] = $type_items;
                $dataImport[$index_parent]['status_orders'] = $status_orders;
                $dataImport[$index_parent]['employees'] = $employees;
                $dataImport[$index_parent]['tax'] = $tax;
                $dataImport[$index_parent]['cost_delivery'] = $cost_delivery;
                $dataImport[$index_parent]['transporters'] = $transporters;
                $dataImport[$index_parent]['charge_party'] = $charge_party;
                $dataImport[$index_parent]['discount_percent'] = $discount_percent;
                $dataImport[$index_parent]['discount_direct'] = $discount_direct;
                $dataImport[$index_parent]['note'] = $note;
                $dataImport[$index_parent]['so'] = $so;
                $dataImport[$index_parent]['pi'] = $pi;
                $dataImport[$index_parent]['po_style'] = $po_style;
                $dataImport[$index_parent]['item_code_tem'] = $item_code_tem;

                $refItem = '';
                $parent_current = $index_parent;
                $ref = $reference;
                $index_parent++;
            }

            if (!empty($item_code) && $item_code != $refItem) {
                $dataImport[$parent_current]['items'][$index_parent_items] = [
                    'item_code' => $item_code,
                    'item_name' => $item_name,
                    'product_name_customer' => $product_name_customer,
                    'unit' => $unit,
                    'price' => $price,
                    'amount' => $amount,
                    'date_delivery' => $date_delivery,
                    'detail_delivery' => $detail_delivery,
                    'total_quantity_item' => $total_quantity_item,
                    'note_item' => $note_item,
                ];

                $parent_current_item = $index_parent_items;
                $refItem = $item_code;
                $index_parent_items++;
            }

            if (!empty($order_code)) {
                $dataImport[$parent_current]['items'][$parent_current_item]['detail'][] = [
                    'date_ship' => $date_ship,
                    'order_code' => $order_code,
                    'command' => $command,
                    'quantity_put' => $quantity_put,
                    'quantity_loss' => $quantity_loss,
                    'sample_quantity_item' => $sample_quantity_item,
                ];
            }
        }
        $listRef = [];
        if (!empty($dataImport)) {
            foreach ($dataImport as $key => $value) {
                $date = $value['date'];
                $reference_no = $value['reference'];
                $_reference_no = getReference('orders');

                if ($this->orders_model->checkExistOrders($_reference_no)) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] không thể thêm vì đã tồn tại trong phần mềm</div>';
                    continue;
                }

                $customerName = $value['customer'];
                $customer = $this->site_model->getClientByZcodeOrCompany($customerName);
                if (empty($customer)) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại trong phầm mềm</div>';
                    continue;
                }
                $customer_id = $customer['userid'];
                $customer_name = $customer['company'];

                $so = $value['so'];
                $pi = $value['pi'];
                $po_style = $value['po_style'];
                $item_code_tem = $value['item_code_tem'];

                //handling person contract
                $person_contract = $value['person_contact'];
                if (empty($person_contract)) {
                    $this->db->select('tblcontacts.id');
                    $this->db->from('tblcontacts');
                    $this->db->where('tblcontacts.userid', $customer_id);
                    $this->db->limit(1);
                    $contract = $this->db->get()->row_array();
                    $person_contact_id = !empty($contract['id']) ? $contract['id'] : 0;
                } else {
                    $contract = $this->site_model->getContractByFirstName($person_contract, $customer_id);
                    if (empty($contract)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại người liên lạc [' . $person_contract . '] trong phầm mềm</div>';
                        continue;
                    }
                    $person_contact_id = $contract['id'];
                }

                //end

                $str_id_branch = $value['id_branch'];
                $id_branch = 0;
                if (!empty($str_id_branch)) {
                    $dtBranch = $this->site_model->getBranchByName($str_id_branch);
                    if (empty($dtBranch)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì chi nhánh xưởng [' . $str_id_branch . '] không tồn tại trong phầm mềm</div>';
                        continue;
                    }
                    $id_branch = $dtBranch['id'];
                }

                $str_currencies = $value['currencies'];
                $currencies = 0;
                if (!empty($str_currencies)) {
                    $dtCurrencies = $this->site_model->getCurrenciesByName($str_currencies);
                    if (empty($dtCurrencies)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì tiền tệ [' . $str_currencies . '] không tồn tại trong phầm mềm</div>';
                        continue;
                    }
                    $currencies = $dtCurrencies['id'];
                }

                $amount_to_vnd = $value['amount_to_vnd'];
                if (empty($amount_to_vnd)) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì quy đổi VND [' . $amount_to_vnd . '] không nhập</div>';
                    continue;
                }

                $type_orders = $value['type_orders'];
                if (!in_array($type_orders, [1, 2, 3, 4, 11, 12, 13, 14])) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì loại đơn hàng không đúng định dạng [1, 2, 3, 4, 11, 12, 13, 14]</div>';
                    continue;
                }

                $type_items = $value['type_items'];
                if (!in_array($type_items, [1, 2])) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì loại sản phẩm không đúng định dạng [1, 2]</div>';
                    continue;
                }

                $status_orders = $value['status_orders'];
                if (!in_array($status_orders, [1, 4, 5])) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì trạng thái đơn hàng không đúng định dạng [1, 4, 5]</div>';
                    continue;
                }

                $addressDelivery = $value['address_delivery'];
                $address_delivery_id = 0;
                if (!empty($addressDelivery)) {
                    $address_delivery = $this->site_model->getShippingClientByClientAndAddress($customer_id, $addressDelivery);
                    if (empty($address_delivery)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì khách hàng [' . $customerName . '] không tồn tại đỉa chỉ [' . $addressDelivery . '] trong phầm mềm</div>';
                        continue;
                    }
                    $address_delivery_id = $address_delivery['id'];
                } else {
                    $this->db->select('
                            tblshipping_client.id
                        ', false);
                    $this->db->from('tblshipping_client');
                    $this->db->where('tblshipping_client.client', $customer_id);
                    $this->db->limit(1);
                    $shipping_client = $this->db->get()->row_array();
                    $address_delivery_id = !empty($shipping_client) ? $shipping_client['id'] : 0;
                }

                //handling employee
                $employee = $value['employees'];
                if (empty($employee)) {
                    // $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại nhân viên phụ trách</div>';
                    // continue;
                    $employeeId = get_staff_user_id();
                }

                if (!empty($employee)) {
                    $staffName = $employee;
                    $staff = $this->site_model->getStaffByName($staffName);
                    if (empty($staff)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhân viên phụ trách [' . $staffName . '] không tồn tại trong phần mềm</div>';
                        continue;
                    }
                    $employeeId = $staff['staffid'];
                }
                //end employee

                //handling tax
                $tax = $value['tax'];
                $tax_id = 0;
                $tax_rate = 0;
                $tax_name = 0;
                if (!empty($tax)) {
                    $dTax = $this->site_model->getTaxesByName($tax);
                    if (empty($dTax)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax . '] không tồn tại trong phần mềm</div>';
                        continue;
                    }
                    $tax_id = $dTax['id'];
                    $tax_rate = $dTax['taxrate'];
                    $tax_name = $dTax['name'];
                }
                //end tax

                //handling transporters
                $transporters = $value['transporters'];
                $transporter_id = 0;
                if (!empty($transporters)) {
                    $transport = $this->site_model->getTransportByName($transporters);
                    if (empty($transport)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì nhà vận chuyển [' . $transporters . '] không tồn tại trong phần mềm</div>';
                        continue;
                    }
                    $transporter_id = $transport['id'];
                }
                //end handling transporters

                //charge party: Bên chịu phí
                $charge_party = !empty($value['charge_party']) ? $value['charge_party'] : 1;
                if (!in_array($charge_party, [1, 2])) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] vì bên chịu phí không đúng giá trị [1, 2]</div>';
                    continue;
                }
                $charge_party = ($charge_party == 1) ? 'company' : 'customer';
                //

                $items = $value['items'];
                if (empty($items)) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không tồn tại mặt hàng</div>';
                    continue;
                }

                //handling items
                $note = $value['note'];
                $count_items = 0;
                $total_quantity = 0;
                $total_amount_items = 0;
                $total_tax_items = 0;
                $total_discount_percent_items = 0;
                $total_discount_direct_items = 0;
                $grand_total_items = 0;
                $discount_percent = !empty($value['discount_percent']) ? $value['discount_percent'] : 0;
                $total_discount_percent = 0;
                $total_discount_direct = !empty($value['discount_direct']) ? $value['discount_direct'] : 0;
                $cost_delivery = !empty($value['cost_delivery']) ? $value['cost_delivery'] : 0;
                $grand_total = 0;
                $status = 'un_approved';
                $gift = 0;
                $total_cost_temporary_capital = 0;
                $total_profit_temporary_capital = 0;

                $flagErrorsItems = false;
                $itemsIn  = [];
                $grand_total_quantity = 0;
                $counter_item = 0;

                $dtTablePrice = $this->orders_model->getGroupPriceCustomer($customer_id);
                $table_price_id = !empty($dtTablePrice) ? $dtTablePrice['id'] : 0;
                foreach ($items as $k => $val) {
                    $item_type = !empty($val['item_type']) ? $val['item_type'] : 1;
                    $item_code = trim($val['item_code']);
                    if (empty($item_code)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng bị trống</div>';
                        $flagErrorsItems = true;
                        break;
                    }
                    if (empty($item_code)) continue;
                    $loss = 0;
                    $type_item = "products";
                    $item = $this->site_model->getProductsByCode($item_code);
                    if (empty($item)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không tồn tại trong phần mềm</div>';
                        $flagErrorsItems = true;
                        break;
                    }

                    $conversion_quantity_unit = 1;
                    $conversion_quantity_unit_default = 1;
                    $quantity_child_sheet = $item['quantity_child_sheet'];
                    $quantity_sheet_bale = $item['quantity_sheet_bale'];
                    $loss = $item['loss'];

                    $product_name_customer = $val['product_name_customer'];
                    if (empty($product_name_customer)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] chưa nhập tên TP của khách hàng</div>';
                        $flagErrorsItems = true;
                        break;
                    }

                    $unit = $val['unit'];
                    $dtUnits = $this->unit_model->rowUnitByCode($unit, '*', 'where');
                    if (empty($dtUnits)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] đơn vị [' . $unit . '] chưa có trong phần mềm</div>';
                        $flagErrorsItems = true;
                        break;
                    }

                    $unit_id = $dtUnits['unitid'];
                    if ($unit_id != $item['unit_id'] && $unit_id != $item['conversion_unit']) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có đơn vị tính [' . $unit . '] trong thành phẩm</div>';
                        $flagErrorsItems = true;
                        break;
                    }

                    if ($unit_id != $item['unit_id']) {
                        $conversion_quantity_unit = $item['conversion_quantity_unit'];
                    }

                    $ct_counter_item = 0;
                    $arrItemsChildColumns = [];
                    $counter_items_number = 0;
                    $quantity = 0;
                    $total_quantity_loss = 0;
                    $total_quantity_sample = 0;
                    $detail = !empty($val['detail']) ? $val['detail'] : null;
                    if (empty($detail)) {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có các dòng mã đơn đặt</div>';
                        $flagErrorsItems = true;
                        break;
                    } else {
                        foreach ($detail as $kD => $vD) {
                            $date_ship = $vD['date_ship'];
                            if (empty($date_ship)) {
                                $errors .= '<div>Ngày giao [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có ngày giao bị trống</div>';
                                $flagErrorsItems = true;
                                break;
                            }

                            $order_code = $vD['order_code'];
                            if (empty($order_code)) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có mã đơn đặt bị trống</div>';
                                $flagErrorsItems = true;
                                break;
                            }

                            $command = $vD['command'];
                            if (empty($command)) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] có chỉ lệnh bị trống</div>';
                                $flagErrorsItems = true;
                                break;
                            }

                            $quantity_put = $vD['quantity_put'];
                            if (empty($quantity_put)) {
                                $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có số lượng đặt</div>';
                                $flagErrorsItems = true;
                                break;
                            }

                            $quantity_loss = roundNumberFormat($quantity_put * $loss / 100, 0);
                            $sample_quantity_item = $vD['sample_quantity_item'];

                            $arrItemsChildColumns[] = [
                                'counter_item' => $counter_item,
                                'columns_id' => 0,
                                'columns_name' => $date_ship,
                                'columns_value' => 'date_ship',
                                'counter_items_number' => $counter_items_number,
                            ];
                            $arrItemsChildColumns[] = [
                                'counter_item' => $counter_item,
                                'columns_id' => 0,
                                'columns_name' => $order_code,
                                'columns_value' => 'order_code',
                                'counter_items_number' => $counter_items_number,
                            ];

                            $arrItemsChildColumns[] = [
                                'counter_item' => $counter_item,
                                'columns_id' => 0,
                                'columns_name' => $command,
                                'columns_value' => 'command',
                                'counter_items_number' => $counter_items_number,
                            ];

                            $arrItemsChildColumns[] = [
                                'counter_item' => $counter_item,
                                'columns_id' => 0,
                                'columns_name' => $quantity_put,
                                'columns_value' => 'quantity_put',
                                'counter_items_number' => $counter_items_number,
                            ];

                            $arrItemsChildColumns[] = [
                                'counter_item' => $counter_item,
                                'columns_id' => 0,
                                'columns_name' => $quantity_loss,
                                'columns_value' => 'quantity_loss',
                                'counter_items_number' => $counter_items_number,
                            ];

                            $arrItemsChildColumns[] = [
                                'counter_item' => $counter_item,
                                'columns_id' => 0,
                                'columns_name' => $sample_quantity_item,
                                'columns_value' => 'sample_quantity_item',
                                'counter_items_number' => $counter_items_number,
                            ];

                            $quantity += $quantity_put;
                            $total_quantity_loss += $quantity_loss;
                            $total_quantity_sample += is_numeric($sample_quantity_item) ? $sample_quantity_item : 0;

                            $counter_items_number++;
                            $counter_item++;
                        }
                    }

                    $ct_counter_item = $counter_items_number;
                    $sample_quantity =  $total_quantity_sample;
                    $item_id = $item['id'];
                    $items_code = $item['code'];
                    $items_name = $item['name'];
                    // $quantity = $val['quantity'];
                    $price = $val['price'];
                    if (empty($price) && !empty($table_price_id)) {
                        $price = $this->orders_model->getPriceCustomer($table_price_id, $customer_id, $item_id, 'product', $quantity);
                        if ($unit_id == $item['conversion_unit'] && !empty($item['conversion_quantity_unit'])) {
                            $price = $price / $item['conversion_quantity_unit'];
                        }
                    }

                    $note_item = $val['note_item'];
                    $amount = $quantity * $price;

                    $total_quantity_item = $sample_quantity + $quantity + $total_quantity_loss;
                    if ($type_orders == TYPE_PTM) {
                        if ($total_quantity_item < QUANTITY_PTM) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] loại phát triển mẫu có số lượng >= ' . QUANTITY_PTM . '</div>';
                            $flagErrorsItems = true;
                            break;
                        }
                    }

                    $grand_total_quantity += $total_quantity_item;

                    $sub = [];
                    $total_quantity_sub = 0;
                    $date_delivery = $val['date_delivery'];
                    if (!empty($date_delivery)) {
                        $date_shipping = $date_delivery;
                        $sub[] = [
                            'date_shipping' => $date_shipping,
                            'quantity_shipping' => $total_quantity_item,
                        ];
                    } else {
                        $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì mặt hàng [' . $item_code . '] không có ngày giao hàng dự kiến</div>';
                        $flagErrorsItems = true;
                        break;
                    }

                    $detail_delivery = $val['detail_delivery'];
                    $ship = [];
                    // if (!empty($detail_delivery)) {
                    //     $detail_delivery = explode('||', $detail_delivery);
                    //     if (!empty($detail_delivery)) {
                    //         foreach ($detail_delivery as $kD => $vD) {
                    //             $arr_detail_delivery = explode('-', $vD);
                    //             $date_detail_delivery = trim($arr_detail_delivery[0]);
                    //             if (empty($date_detail_delivery)) continue;
                    //             $quantity_detail_delivery = number_unformat($arr_detail_delivery[1]);
                    //             if (gettype($date_detail_delivery) == 'double' || gettype($date_detail_delivery) == 'int') {
                    //                 $date_detail_delivery = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date_detail_delivery));
                    //             } else if (gettype($date_detail_delivery) == 'string') {
                    //                 $date_detail_delivery = to_sql_date($date_detail_delivery);
                    //             }

                    //             $ship[] = [
                    //                 'date' => $date_detail_delivery,
                    //                 'quantity' => $quantity_detail_delivery,
                    //             ];
                    //         }
                    //     }
                    // }

                    $grand_total_item = $amount;
                    $tax_amount_item = 0;
                    $tax_name_item = '';
                    if (!empty($tax_name_item)) {
                        $info_tax = $this->site_model->getTaxesByName($tax_name_item);
                        if (empty($info_tax)) {
                            $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì thuế [' . $tax_name_item . '] không tồn tại trong phần mềm</div>';
                            break;
                        }
                        $tax_name_item = $info_tax['name'];
                        $tax_rate_item = $info_tax['taxrate'];
                        $tax_id_item = $info_tax['id'];
                    } else {
                        $tax_name_item = "0%";
                        $tax_rate_item = 0;
                        $tax_id_item = 0;
                    }

                    $discount_percent_item = 0;
                    $discount_percent_amount_item = 0;
                    if ($discount_percent_item > 0) {
                        $discount_percent_amount_item = $grand_total_item * ($discount_percent_item / 100);
                        $total_discount_percent_items += $discount_percent_amount_item;
                        $grand_total_item -= $discount_percent_amount_item;
                    }
                    $discount_direct_amount_item = 0;
                    $total_discount_direct_items += $discount_direct_amount_item;
                    $grand_total_item -= $discount_direct_amount_item;

                    //handling cost temporary capital
                    if ($type_item == "products") {
                        $itemType = "product";
                    } else if ($type_item == "items") {
                        $itemType = "items";
                    }
                    $result = $this->site_model->getWarehouseProductLIFO_FiFO($itemType, $item_id);
                    $priceCost = 0;
                    $cQuantity = $quantity;
                    foreach ($result as $i => $v) {
                        if ($cQuantity <= 0) break;
                        $qty = $v['quantity_left'];
                        $p = $v['price'];

                        $cQuantityTerm = $cQuantity;
                        $cQuantity -= $qty;
                        if ($cQuantity >= 0) {
                            $pCost = $qty * $p;
                        } else if ($cQuantity < 0) {
                            $pCost = $cQuantityTerm * $p;
                        }
                        $priceCost += $pCost;
                    }

                    if ($cQuantity > 0) {
                        $rs = $this->site_model->getOrdersItemSellFirst($item_id, $type_item);
                        if (!empty($rs)) {
                            $priceCost += $rs['price'] * $cQuantity;
                        } else {
                            $priceCost += $item['price_import'] * $cQuantity;
                        }
                    }

                    //end handling cost temporary capital
                    $cost_temporary_capital = $priceCost;
                    $profit_temporary_capital = $grand_total_item - $priceCost;

                    if ($tax_rate_item > 0) {
                        $tax_amount_item = $grand_total_item * ($tax_rate_item / 100);
                        $total_tax_items += $tax_amount_item;
                        $grand_total_item += $tax_amount_item;
                    }

                    $itemsIn[] = [
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
                        'cost_temporary_capital' => $cost_temporary_capital,
                        'profit_temporary_capital' => $profit_temporary_capital,
                        'quantity_child_sheet' => $quantity_child_sheet,
                        'quantity_sheet_bale' => $quantity_sheet_bale,
                        'sub' => $sub,
                        'arrItemsChildColumns' => $arrItemsChildColumns,
                        'ct_counter_item' => $ct_counter_item,
                        'hand_input_price' => 1,
                        'loss' => $loss,
                        'product_name_customer' => $product_name_customer,
                        'ship' => $ship,
                        'unit_id' => $unit_id,
                        'conversion_quantity_unit' => $conversion_quantity_unit,
                        'conversion_quantity_unit_default' => $conversion_quantity_unit_default
                    ];

                    $total_quantity += $quantity;
                    $total_amount_items += $amount;
                    $grand_total_items += $grand_total_item;
                    $total_cost_temporary_capital += $cost_temporary_capital;
                }

                if ($flagErrorsItems) continue;

                if (empty($itemsIn)) {
                    $errors .= '<div>Đơn hàng [' . $reference_no . '] thêm không được vì không có mặt hàng</div>';
                    continue;
                }

                $count_items = count($itemsIn);
                $grand_total = $grand_total_items;

                if ($discount_percent > 0) {
                    $total_discount_percent = $grand_total * ($discount_percent / 100);
                }
                $grand_total -= $total_discount_percent;
                $grand_total -= $total_discount_direct;

                $total_profit_temporary_capital = $grand_total - $total_cost_temporary_capital;
                $total_profit_temporary_capital -= $cost_delivery;

                $total_tax = 0;
                if ($tax_rate > 0) {
                    $total_tax = $grand_total * ($tax_rate / 100);
                }

                $grand_total += $total_tax;
                if ($charge_party == "customer") {
                    $grand_total += $cost_delivery;
                } else {
                    //công ty
                }

                $options = [
                    'date' => $date,
                    // 'reference_no' => $reference_no,
                    'reference_no' => $_reference_no,
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'address_delivery_id' => $address_delivery_id,
                    'employee_id' => $employeeId,
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
                    'total_tax' => $total_tax, //tổng thuế
                    'discount_percent' => $discount_percent, //% chiết khấu
                    'total_discount_percent' => $total_discount_percent, //tổng tiền chiết khấu phần trăm
                    'total_discount_direct' => $total_discount_direct, //tổng tiền chiết khấu tiền mặt
                    'grand_total' => $grand_total, //tổng tiền đơn hàng
                    'status' => $status,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'table_price_id' => $table_price_id,
                    'table_discount_id' => 0,
                    'cost_delivery' => $cost_delivery,
                    'gift' => $gift,
                    'transporter_id' => $transporter_id,
                    'charge_party' => $charge_party,
                    'person_contact_id' => $person_contact_id,
                    'total_cost_temporary_capital' => $total_cost_temporary_capital, //giá vốn tạm thời
                    'total_profit_temporary_capital' => $total_profit_temporary_capital, //chi phí lợi nhuận tạm thời
                    'id_branch' => $id_branch,
                    'currencies' => $currencies,
                    'amount_to_vnd' => $amount_to_vnd,
                    'type_orders' => $type_orders,
                    'status_orders' => $status_orders,
                    'type_items' => $type_items,
                    'grand_total_quantity' => $grand_total_quantity,
                    'so' => $so,
                    'pi' => $pi,
                    'po_style' => $po_style,
                    'item_code' => $item_code_tem,
                ];

                // print_arrays($options, $itemsIn);
                $order_id = $this->orders_model->insertOrdersNew($options);

                if ($order_id) {
                    if (getReference('orders') == $_reference_no) {
                        updateReference('orders');
                    }

                    foreach ($itemsIn as $k => $val) {
                        $val['order_id'] = $order_id;
                        $sub = $val['sub'];
                        $ship = $val['ship'];
                        $arrItemsChildColumns = $val['arrItemsChildColumns'];
                        unset($val['sub']);
                        unset($val['ship']);
                        unset($val['arrItemsChildColumns']);

                        $order_item_id = $this->orders_model->insertOrderItemsNew($val);
                        if ($order_item_id) {
                            if (!empty($sub)) {
                                foreach ($sub as $i => $v) {
                                    $v['order_item_id'] = $order_item_id;
                                    $this->orders_model->insertOrderItemShippingsNew($v);
                                }
                            }

                            if (!empty($ship)) {
                                foreach ($ship as $kSh => $valSh) {
                                    $this->db->insert('tbl_orders_ship', [
                                        'order_item_id' => $order_item_id,
                                        'date' => $valSh['date'],
                                        'quantity' => $valSh['quantity'],
                                    ]);
                                }
                            }

                            if (!empty($arrItemsChildColumns)) {
                                foreach ($arrItemsChildColumns as $kC => $vC) {
                                    $arrItemsChildColumns[$kC]['order_id'] = $order_id;
                                    $arrItemsChildColumns[$kC]['order_item_id'] = $order_item_id;
                                }
                                $this->orders_model->insertBatchOrderItemsColumns($arrItemsChildColumns);
                            }

                            if ($val['type_item'] == "products") {
                                $exchangeUnits = $this->products_model->getExchangeProductsByProductId($val['item_id']);
                                if (!empty($exchangeUnits)) {
                                    foreach ($exchangeUnits as $kk => $vv) {
                                        if (empty($vv)) continue;
                                        $quantity_exchange = $vv['number_exchange'];
                                        $total_quantity_exchange = $val['quantity'] / $quantity_exchange;
                                        $exchange = [
                                            'order_item_id' => $order_item_id,
                                            'unit_id' => $vv['unit_id'],
                                            'quantity_exchange' => $quantity_exchange,
                                            'total_quantity_exchange' => $total_quantity_exchange,
                                        ];
                                        $this->orders_model->insertOrderItemExchange($exchange);
                                    }
                                }
                            }
                        }
                    }
                    $listRef[] = $reference_no;
                    $count++;
                }
            }
        }
        $data['errors'] = $errors;
        if ($count) {
            $data['result'] = 1;
            $data['message'] = 'Tạo đơn hàng thành công';
            $data['order_id'] = $order_id;
            insertActivityLog([
                'type_parent_obj' => 'orders',
                'table_obj' => 'tbl_orders',
                'id_obj' => $order_id,
                'name_obj' => $reference_no,
                'content' => lang('tnh_his_add_orders') . ' [' . implode(',', $listRef) . ']',
                'actions' => 'import'
            ]);
        } else {
            $data['result'] = 0;
            $data['message'] = lang('tnh_not_data_add');
        }
        echo json_encode($data);
        die;
        // Xử lý lưu tại đây
        echo json_encode(['status' => true, 'message' => 'Lưu thành công!']);
    }
}
