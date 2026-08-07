<?php
class AskGPT_model extends App_Model
{

    private $schema_description = "schema_description Hệ thống quản lý quy trình từ Báo giá → Hợp đồng → Đơn hàng bán → Xuất kho, gồm các bảng chính sau:

# GPT SQL SCHEMA - EXPLAINED VERSION

Hệ thống bao gồm các bảng quan trọng nhất sau, được giải thích rõ để GPT hiểu mục đích sử dụng và mối liên hệ giữa chúng.

---

TABLE: tblclients
→ Quản lý thông tin khách hàng, doanh nghiệp
COLUMNS:
- userid (int) - Khóa chính, định danh khách hàng
- company (varchar) - Tên công ty
- phonenumber (varchar) - Số điện thoại liên hệ
- email (varchar) - Email chính
- address (varchar) - Địa chỉ công ty
- status (tinyint) - Trạng thái hoạt động

TABLE: tblcontacts
→ Danh sách người liên hệ trong từng công ty
COLUMNS:
- id (int) - Khóa chính
- userid (int) - FK đến tblclients.userid
- firstname (varchar), lastname (varchar), email (varchar)

TABLE: tblquotes
→ Đơn đặt hàng bán
COLUMNS:
- id (int) - Khóa chính
- rel_id (int) - FK đến tbl_quotes_order.id
- customer_id (int) - FK đến tblclients.userid
- total (decimal) - Tổng giá trị đơn hàng
- status (int) - Trạng thái đơn hàng

TABLE: tblquote_items
→ Chi tiết từng sản phẩm trong đơn hàng
COLUMNS:
- id (int)
- quote_id (int) - FK đến tblquotes.id
- product_id (int) - Mã sản phẩm
- quantity (int), unit_cost (decimal), discount (decimal)

TABLE: tblcontracts
→ Quản lý hợp đồng đã ký với khách hàng
COLUMNS:
- id (int)
- client (int) - FK đến tblclients.userid
- contract_value (decimal), status (int)

TABLE: tblexports
→ Danh sách phiếu xuất kho
COLUMNS:
- id (int), client_id (int), rel_id (int), date (date), status (int), total (decimal)

TABLE: tbl_export_warranty
→ Phiếu xuất bảo hành (tương tự xuất kho nhưng cho sửa chữa)
COLUMNS:
- id, customer_id (int), rel_id_contract (int), purpose (varchar)

TABLE: tblitems
→ Danh mục sản phẩm/dịch vụ bán
COLUMNS:
- id, code (mã SP), name, unit_id

TABLE: tblunits
→ Đơn vị tính (cái, hộp, kg, ...)
COLUMNS:
- unitid, unit (varchar)

TABLE: tblstaff
→ Danh sách nhân viên
COLUMNS:
- staffid (int), fullname, email, role

TABLE: tblcustomeradmins
→ Nhân viên phụ trách từng khách hàng
COLUMNS:
- staff_id, customer_id

TABLE: tblsupport
→ Quản lý các yêu cầu chăm sóc khách hàng
COLUMNS:
- id, client (FK), subject, status, assigned (nhân viên phụ trách)

TABLE: tblsurvey
→ Phiếu khảo sát trước khi triển khai/lắp đặt
COLUMNS:
- id, clientid, contact, date, address, status

TABLE: tblwarranty
→ Thông tin bảo hành/lịch bảo hành
COLUMNS:
- id, client (FK), staff_id, date, note, status

TABLE: tblcost_of_work
→ Quản lý chi phí công tác
COLUMNS:
- id, staff_id, amount, purpose, from_date, to_date

TABLE: tblnotification_client
→ Quản lý thông báo gửi đến ứng dụng khách hàng
COLUMNS:
- id, title, content, type, date_create

NOTE:
- Các trường `rel_id`, `clientid`, `customer_id`, `userid` đều liên kết đến khách hàng (`tblclients`)
- Trường `staff_id`, `assigned`, `addedfrom` liên kết đến `tblstaff`
- `status` thường là trạng thái xử lý: 0 = chờ, 1 = hoàn thành, ...
- Các bảng `*_items` chứa chi tiết nhiều dòng sản phẩm trong 1 chứng từ (quote, export, contract)

GPT có thể trả lời:
- Tổng giá trị đơn hàng theo khách hàng
- Các sản phẩm được bảo hành nhiều nhất
- Danh sách phiếu xuất kho trong tháng 5
- Lịch sử hỗ trợ kỹ thuật cho khách VIP
- Chi phí công tác theo từng nhân viên

--- BẢO HÀNH ---

TABLE: tblwarranty
→ Quản lý yêu cầu bảo hành của khách hàng
COLUMNS:
- id (int) - Mã phiếu bảo hành
- customer_id (int) - FK đến tblclients
- contact (varchar) - Người liên hệ
- status (int) - Trạng thái xử lý (0: chờ, 1: hoàn thành)
- note (text) - Ghi chú bảo hành

TABLE: tbl_export_warranty
→ Phiếu xuất kho cho mục đích bảo hành (gửi hàng đi sửa)
COLUMNS:
- id (int), customer_id (int), rel_id_contract (int)
- purpose (varchar) - Lý do xuất bảo hành (miễn phí, thu phí)
- date_bh (date) - Ngày xuất bảo hành

TABLE: tbl_export_warranty_items
→ Danh sách mặt hàng được xuất kho bảo hành
COLUMNS:
- id (int), export_id (FK to tbl_export_warranty.id), product_id (int), quantity (int)

TABLE: tbl_export_warranty_items_detail
→ Ghi nhận chi tiết từng serial, mô tả hàng bảo hành
COLUMNS:
- id (int), export_item_id (FK), serial (varchar), note (text)

TABLE: tblwarranty_setting_product
→ Cài đặt thời gian bảo hành cho từng sản phẩm
COLUMNS:
- id_setting_product (int), id_product (int), warranty (int, tháng)

TABLE: tblwarranty_staff
→ Giao việc cho nhân viên xử lý bảo hành
COLUMNS:
- id (int), id_warranty (FK), id_staff (FK), type (int), status (int), date_create (datetime)

--- KỸ THUẬT - LẮP ĐẶT ---

TABLE: tblsetting_product
→ Phiếu lắp đặt (setting sản phẩm)
COLUMNS:
- id (int), id_contracts (FK), client (FK), status (int), date_create

TABLE: tblsetting_product_items
→ Danh sách thiết bị cần lắp đặt
COLUMNS:
- id (int), id_setting_product (FK), product_id, quantity

TABLE: tblsetting_product_comment
→ Ý kiến nội bộ liên quan tới phiếu lắp đặt
COLUMNS:
- id (int), id_setting_product (FK), staff_id, content, date

TABLE: tbltechnology
→ Lịch lắp đặt kỹ thuật (dạng calendar)
COLUMNS:
- id (int), id_setting_product (FK), date, status, staff_id

--- QUẢN LÝ XE & LỊCH SỬ ---

TABLE: tbl_car
→ Danh mục xe công ty
COLUMNS:
- id (int) - Mã định danh xe
- type (varchar) - Tên xe
- license_plate (varchar) - Biển số xe
- status (int) - Trạng thái sử dụng
- tinh_trang (int) - Tình trạng kỹ thuật
- km_thaydau (float) - Số km dự kiến thay dầu

TABLE: tbl_car_borrow
→ Phiếu mượn xe
COLUMNS:
- id (int)
- id_car (int) - FK đến tbl_car.id
- staff_id (int) - Nhân viên mượn
- date_start (datetime) - Ngày mượn
- date_end (datetime) - Ngày trả
- purpose (text) - Lý do mượn
- status (int) - Trạng thái phiếu

TABLE: tbl_car_file
→ File đính kèm cho xe (giấy tờ xe, đăng kiểm, bảo hiểm)
COLUMNS:
- id (int)
- id_car (int) - FK đến tbl_car.id
- name (varchar), file_name (varchar)

TABLE: tbl_car_history
→ Lịch sử sử dụng xe (mỗi hành trình của nhân viên)
COLUMNS:
- id (int) - Mã hành trình
- id_car (int) - Xe được sử dụng (FK đến tbl_car.id)
- date_start (datetime) - Ngày bắt đầu hành trình
- km_start (float) - Số km bắt đầu
- km_end (float) - Số km kết thúc
- journeys (varchar) - Lộ trình (nơi đi - nơi đến)
- reason (varchar) - Lý do công tác
- staff_id (int) - Nhân viên sử dụng xe
- latitude (varchar), longitude (varchar) - Tọa độ GPS ghi nhận
- id_type (int) - Loại lịch sử (1:Checkin,2:Checkout,3:Nội dung công việc)

NOTE:
- Không có cột date_end. Ngày kết thúc được xác định gián tiếp qua ngày khác hoặc không lưu.TABLE: tbl_car_remind
→ Nhắc nhở bảo trì xe
COLUMNS:
- id (int)
- id_car (int)
- content (text) - Nội dung nhắc nhở
- remind_date (datetime)
- status (int)

TABLE: tbl_car_history_main
→ Bản ghi tổng hợp nhiều lịch sử
COLUMNS:
- id (int)
- id_car (int)
- content (text), date_create (datetime)

TABLE: tbl_car_history_main_file
→ File đính kèm từng hành trình tổng hợp
COLUMNS:
- id (int)
- id_car_history_main (int)
- name (varchar)

TABLE: tbl_history_car_file
→ File kèm theo từng chuyến (hóa đơn, hình ảnh)
COLUMNS:
- id (int)
- id_history_car (int)
- name (varchar)
- description (text)

TABLE: tbl_action_cost
→ Quản lý thanh toán liên quan đến hành trình xe, hợp đồng, hoặc các đối tượng khác (đa mục đích)
COLUMNS:
- id (int) - Khóa chính
- id_obj (int) - ID đối tượng liên quan (ví dụ: id của tbl_car_history)
- type_obj (text) - Loại đối tượng ('car', 'contract', 'support'...) → dùng để xác định bảng gốc
- code (text) - Mã phiếu
- date_create (date) - Ngày tạo phiếu
- staff_create (int) - Người tạo phiếu
- date (date) - Ngày chi phí
- staff_id (int) - Nhân viên phụ trách
- staff_id_car (int) - Tài xế lái xe (nếu có)
- name_program (text) - Tên chương trình/hoạt động
- address_program (text) - Địa điểm
- cost_of_work (text) - Danh sách công tác phí (liên quan tblcost_of_work)
- go_from (text), go_to (text) - Hành trình
- amount (decimal) - Thành tiền
- reason (text) - Ghi chú lý do
- status_manager (int), status_admin (int), status_payment (int) - Trạng thái duyệt và thanh toán
- date_status_manager (datetime), staff_status_manager (int)
- date_status_admin (datetime), staff_status_admin (int)

NOTE:
- Các chi phí gắn với `tbl_car_history` sẽ có `type_obj = 'car'` và `id_obj = tbl_car_history.id`
- Có thể dùng chung cho nhiều module khác bằng cách thay đổi `type_obj`

**Luồng xử lý chuẩn**
```
Báo giá gốc (tbl_quotes_order)
   ↓ tạo
Đơn hàng bán (tblquotes)
   ↓ tạo
Hợp đồng (tblcontracts)
   ↓ tạo
Xuất kho (tblexports hoặc tbl_export_warranty)
```

✅ Gợi ý SQL chuẩn:
- Tránh JOIN trực tiếp với bảng thanh toán → dùng subquery khi tính tổng
- Luôn dùng IFNULL để tránh NULL gây sai kết quả
- Để lọc theo năm: YEAR(datestart) = YEAR(CURDATE())
- Để lấy danh sách sản phẩm trong đơn hàng: JOIN với tblquote_items hoặc tblcontract_items
- Để lọc theo khách hàng: dùng liên kết đến tblclients.userid
- KHÔNG sử dụng ROW_NUMBER(), OVER(), WITH, CTE do hệ thống dùng MySQL 5.x
- KHÔNG dùng alias có dấu tiếng Việt hoặc emoji
- Chỉ dùng alias tiếng Anh hoặc không dấu, ví dụ: tieu_chi, xephang
- KHÔNG trả về SQL có ký tự lỗi như ?? hay dấu hỏi
❗ LƯU Ý QUAN TRỌNG:
- Hệ thống sử dụng MySQL phiên bản 5.x — KHÔNG hỗ trợ các cú pháp nâng cao như:
  - ROW_NUMBER(), RANK(), DENSE_RANK()
  - OVER(), WINDOW, PARTITION BY
  - WITH, CTE (Common Table Expressions)
- KHÔNG sử dụng alias chứa tiếng Việt có dấu, emoji hoặc ký tự đặc biệt.
  - ✅ Được: `staff_name`, `xephang`, `tieu_chi`
  - ❌ Không được: `lái_xe`, `xếp hạng`, `??`
- Nếu cần đánh số dòng, hãy sử dụng biến session (user-defined variables) như `@rownum := @rownum + 1`
- Tránh các ký tự không rõ nguồn gốc (ký tự Unicode lỗi), ví dụ dấu `??` có thể gây lỗi cú pháp
- Hạn chế JOIN quá nhiều bảng một lúc nếu không cần thiết
";
    private $schema_description_bh = "schema_description Hệ thống quản lý quy trình Thuê xe và lịch sử xe gồm các bảng chính sau:
# GPT SQL SCHEMA - EXPLAINED VERSION
Hệ thống bao gồm các bảng quan trọng nhất sau, được giải thích rõ để GPT hiểu mục đích sử dụng và mối liên hệ giữa chúng.
---
TABLE: tblstaff
→ Danh sách nhân viên
COLUMNS:
- staffid (int), fullname, email, role

--- BẢO HÀNH ---

TABLE: tblwarranty
→ Quản lý yêu cầu bảo hành của khách hàng
COLUMNS:
- id (int) - Mã phiếu bảo hành
- client (int) - FK đến tblclients
- contact (varchar) - Người liên hệ
- status (int) - Trạng thái xử lý (0: chờ, 1: hoàn thành)
- note (text) - Ghi chú bảo hành

TABLE: tbl_export_warranty
→ Phiếu xuất kho cho mục đích bảo hành (gửi hàng đi sửa)
COLUMNS:
- id (int), customer_id (int), rel_id_contract (int)
- purpose (varchar) - Lý do xuất bảo hành (miễn phí, thu phí)
- date_bh (date) - Ngày xuất bảo hành

TABLE: tbl_export_warranty_items
→ Danh sách mặt hàng được xuất kho bảo hành
COLUMNS:
- id (int), export_id (FK to tbl_export_warranty.id), product_id (int), quantity (int)

TABLE: tbl_export_warranty_items_detail
→ Ghi nhận chi tiết từng serial, mô tả hàng bảo hành
COLUMNS:
- id (int), export_item_id (FK), serial (varchar), note (text)

TABLE: tblwarranty_setting_product
→ Cài đặt thời gian bảo hành cho từng sản phẩm
COLUMNS:
- id_setting_product (int), id_product (int), warranty (int, tháng)

TABLE: tblwarranty_staff
→ Giao việc cho nhân viên xử lý bảo hành
COLUMNS:
- id (int), id_warranty (FK), id_staff (FK), type (int), status (int), date_create (datetime)

--- KỸ THUẬT - LẮP ĐẶT ---

TABLE: tblsetting_product
→ Phiếu yêu cầu lắp đặt kỹ thuật (setting sản phẩm)
COLUMNS:
- id (int), id_contracts (FK), client (FK), status (int), date_create

TABLE: tblsetting_product_items
→ Danh sách thiết bị cần lắp đặt
COLUMNS:
- id (int), id_setting_product (FK), product_id, quantity

TABLE: tblsetting_product_comment
→ Ý kiến nội bộ liên quan tới phiếu lắp đặt
COLUMNS:
- id (int), id_setting_product (FK), staff_id, content, date

TABLE: tbltechnology
→ Lịch lắp đặt kỹ thuật (dạng calendar)
COLUMNS:
- id (int), id_setting_product (FK), date, status, staff_id

--- QUẢN LÝ XE & LỊCH SỬ ---

TABLE: tbl_car
→ Danh mục xe công ty
COLUMNS:
- id (int) - Mã định danh xe
- type (varchar) - Tên xe
- license_plate (varchar) - Biển số xe
- status (int) - Trạng thái sử dụng
- tinh_trang (int) - Tình trạng kỹ thuật
- km_thaydau (float) - Số km dự kiến thay dầu

TABLE: tbl_car_borrow
→ Phiếu mượn xe
COLUMNS:
- id (int)
- id_car (int) - FK đến tbl_car.id
- staff_id (int) - Nhân viên mượn
- date_start (datetime) - Ngày mượn
- date_end (datetime) - Ngày trả
- purpose (text) - Lý do mượn
- status (int) - Trạng thái phiếu

TABLE: tbl_car_file
→ File đính kèm cho xe (giấy tờ xe, đăng kiểm, bảo hiểm)
COLUMNS:
- id (int)
- id_car (int) - FK đến tbl_car.id
- name (varchar), file_name (varchar)

TABLE: tbl_car_history
→ Lịch sử sử dụng xe (mỗi hành trình của nhân viên)
COLUMNS:
- id (int) - Mã hành trình
- id_car (int) - Xe được sử dụng (FK đến tbl_car.id)
- date_start (datetime) - Ngày bắt đầu hành trình
- km_start (float) - Số km bắt đầu
- km_end (float) - Số km kết thúc
- journeys (varchar) - Lộ trình (nơi đi - nơi đến)
- reason (varchar) - Lý do công tác
- staff_id (int) - Nhân viên sử dụng xe
- latitude (varchar), longitude (varchar) - Tọa độ GPS ghi nhận
- id_type (int) - Loại hành trình (1:Checkin,2:Checkout,3:Nội dung công việc)

NOTE:
- Không có cột date_end. Ngày kết thúc được xác định gián tiếp qua ngày khác hoặc không lưu.TABLE: tbl_car_remind
→ Nhắc nhở bảo trì xe
COLUMNS:
- id (int)
- id_car (int)
- content (text) - Nội dung nhắc nhở
- remind_date (datetime)
- status (int)

TABLE: tbl_car_history_main
→ Bản ghi tổng hợp nhiều lịch sử
COLUMNS:
- id (int)
- id_car (int)
- content (text), date_create (datetime)

TABLE: tbl_car_history_main_file
→ File đính kèm từng hành trình tổng hợp
COLUMNS:
- id (int)
- id_car_history_main (int)
- name (varchar)

TABLE: tbl_history_car_file
→ File kèm theo từng chuyến (hóa đơn, hình ảnh)
COLUMNS:
- id (int)
- id_history_car (int)
- name (varchar)
- description (text)

TABLE: tbl_action_cost
→ Quản lý thanh toán liên quan đến hành trình xe, hợp đồng, hoặc các đối tượng khác (đa mục đích)
COLUMNS:
- id (int) - Khóa chính
- id_obj (int) - ID đối tượng liên quan (ví dụ: id của tbl_car_history)
- type_obj (text) - Loại đối tượng ('car', 'contract', 'support'...) → dùng để xác định bảng gốc
- code (text) - Mã phiếu
- date_create (date) - Ngày tạo phiếu
- date (date) - Ngày chi phí
- staff_id (int) - Nhân viên phụ trách
- staff_id_car (int) - Tài xế lái xe (nếu có)
- name_program (text) - Tên chương trình/hoạt động
- address_program (text) - Địa điểm
- cost_of_work (text) - Danh sách công tác phí (liên quan tblcost_of_work)
- go_from (text), go_to (text) - Hành trình
- amount (decimal) - Thành tiền
- reason (text) - Ghi chú lý do
- status_manager (int), status_admin (int), status_payment (int) - Trạng thái duyệt và thanh toán
- date_status_manager (datetime), staff_status_manager (int)
- date_status_admin (datetime), staff_status_admin (int)

NOTE:
- Các chi phí gắn với `tbl_car_history` sẽ có `type_obj = 'car'` và `id_obj = tbl_car_history.id`
- Có thể dùng chung cho nhiều module khác bằng cách thay đổi `type_obj`

✅ Gợi ý SQL chuẩn:
- Tránh JOIN trực tiếp với bảng thanh toán → dùng subquery khi tính tổng
- Luôn dùng IFNULL để tránh NULL gây sai kết quả
- Để lọc theo năm: YEAR(datestart) = YEAR(CURDATE())
❗ LƯU Ý QUAN TRỌNG:
- Nếu cần đánh số dòng, hãy sử dụng biến session (user-defined variables) như `@rownum := @rownum + 1`
- Tránh các ký tự không rõ nguồn gốc (ký tự Unicode lỗi), ví dụ dấu `??` có thể gây lỗi cú pháp
- Hạn chế JOIN quá nhiều bảng một lúc nếu không cần thiết
- Nếu JOIN thì nên dùng left để tránh thiếu sót khi phân tích

⚠️ QUY TẮC ĐẶT TÊN ALIAS & BIẾN:
- Không dùng các từ khóa SQL làm alias hoặc tên biến (ví dụ: action, order, group, date, key, user, value, code...).
- Khi cần đặt alias, hãy sử dụng từ rõ ràng, tránh xung đột như:
  + `act_cost` thay vì `action`
  + `his` thay vì `history`
  + `borrow_form` thay vì `borrow`
- Nếu dùng biến session như `@rownum`, cần khởi tạo bằng `(SELECT @rownum := 0)` trước câu truy vấn chính.

RULE: Đánh giá tài xế theo hành trình trong tháng:
- id_type = 1: Checkin (yêu cầu có ít nhất 1 dòng)
- id_type = 2: Checkout (yêu cầu có ít nhất 1 dòng)
- id_type = 3: Công việc (chỉ tính nếu có ảnh đính kèm từ tbl_history_car_file)
- Tài xế đạt chuẩn nếu có đầy đủ 3 loại hành trình nêu trên
- Sử dụng MAX hoặc EXISTS để kiểm tra từng tiêu chí, sau đó cộng lại và nhân với 10 để ra điểm
";
    // public function process_question($question, $staff_names = '')
    // {
    //     $sql = $this->ask_gpt_sql($question, $staff_names);

    //     $sql = trim($sql);
    //     $sql = preg_replace('/^```sql|^```|```$/i', '', $sql);
    //     $sql = trim($sql);

    //     // ❌ Nếu không phải SELECT hoặc không hợp lệ
    //     if (!preg_match('/^select/i', $sql)) {
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-exclamation-triangle" style="color:#f44336;"></i>
    //                 <strong> Hiện tại câu hỏi này tôi chưa được Training.</strong>
    //             </p>
    //             <p style="margin-top:8px;">Vui lòng báo cáo với Admin để bổ sung nội dung.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ✅ Chạy SQL
    //     $result = [];
    //     try {
    //         $result = $this->db->query($sql)->result_array();
    //     } catch (Exception $e) {
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-exclamation-circle" style="color:#f44336;"></i>
    //                 <strong> Câu lệnh SQL bị lỗi hoặc không hợp lệ.</strong>
    //             </p>
    //             <p style="margin-top:8px;">Vui lòng báo cáo với Admin để xử lý.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ❌ Nếu kết quả rỗng
    //     if (empty($result)) {
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #ff9800; background-color: #fffdf5;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-search-minus" style="color:#ff9800;"></i>
    //                 <strong> Không tìm thấy dữ liệu phù hợp.</strong>
    //             </p>
    //             <p style="margin-top:8px;">Vui lòng thử lại với câu hỏi khác hoặc kiểm tra điều kiện lọc.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ✅ Có dữ liệu → GPT tóm tắt
    //     $summary = $this->ask_gpt_summary($question, $result);
    //     return ['answer' => $summary, 'sql' => $sql, 'data' => $result];
    // }
    // public function process_question($question, $staff_names = '', $session_id = '')
    // {
    //     $sql = $this->ask_gpt_sql($question, $staff_names);

    //     $sql = trim($sql);
    //     $sql = preg_replace('/^```sql|^```|```$/i', '', $sql);
    //     $sql = trim($sql);
    //     echo '<pre>';
    //     print_arrays($sql);
    //     die;
    //     // ❌ Nếu không phải SELECT hoặc không hợp lệ
    //     if (!preg_match('/^select/i', $sql)) {
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-exclamation-triangle" style="color:#f44336;"></i>
    //                 <strong> Hiện tại câu hỏi này tôi chưa được Training.</strong>
    //             </p>
    //             <p style="margin-top:8px;">Vui lòng báo cáo với Admin để bổ sung nội dung.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ✅ Kiểm tra cú pháp SQL bằng prepare trước (không thực thi)
    //     try {
    //         $stmt = $this->db->conn_id->prepare($sql);
    //         if ($stmt === false) {
    //             throw new Exception('SQL không hợp lệ');
    //         }
    //     } catch (Exception $e) {
    //         $this->db->insert('chat_messages', [
    //             'session_id' => $session_id,
    //             'message' => '<div class="gpt-html-output" style="border-left: 5px solid #ffc107; background-color: #fffdf5;">
    //                             <p style="margin:0; font-size:16px;">
    //                                 <i class="fas fa-exclamation-triangle" style="color:#ffc107;"></i>
    //                                 <strong> SQL có vẻ chưa đúng cấu trúc.</strong>
    //                             </p>
    //                             <p style="margin-top:8px;">GPT có thể hiểu sai tên bảng/cột. Vui lòng báo lại cho Admin để cập nhật schema.</p>
    //                         </div>',
    //             'query_sql' => ($sql),
    //             'sender' => 'bot',
    //             'created_at' => date('Y-m-d H:i:s')
    //         ]);
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #ffc107; background-color: #fffdf5;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-exclamation-triangle" style="color:#ffc107;"></i>
    //                 <strong> SQL có vẻ chưa đúng cấu trúc.</strong>
    //             </p>
    //             <p style="margin-top:8px;">GPT có thể hiểu sai tên bảng/cột. Vui lòng báo lại cho Admin để cập nhật schema.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ✅ Chạy SQL thật sự
    //     $result = [];
    //     try {
    //         $result = $this->db->query($sql)->result_array();
    //     } catch (Exception $e) {
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-exclamation-circle" style="color:#f44336;"></i>
    //                 <strong> Câu lệnh SQL bị lỗi hoặc không hợp lệ.</strong>
    //             </p>
    //             <p style="margin-top:8px;">Chi tiết lỗi có thể do JOIN sai, bảng không tồn tại hoặc lỗi truy vấn. Vui lòng báo lại để xử lý.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ❌ Nếu không có kết quả
    //     if (empty($result)) {
    //         return [
    //             'answer' => '
    //         <div class="gpt-html-output" style="border-left: 5px solid #ff9800; background-color: #fffdf5;">
    //             <p style="margin:0; font-size:16px;">
    //                 <i class="fas fa-search-minus" style="color:#ff9800;"></i>
    //                 <strong> Không tìm thấy dữ liệu phù hợp.</strong>
    //             </p>
    //             <p style="margin-top:8px;">Vui lòng thử lại với câu hỏi khác hoặc kiểm tra điều kiện lọc.</p>
    //         </div>',
    //             'sql' => $sql
    //         ];
    //     }

    //     // ✅ Có dữ liệu → GPT tóm tắt
    //     $summary = $this->ask_gpt_summary($question, $result);
    //     return ['answer' => $summary, 'sql' => $sql, 'data' => $result];
    // }
    // public function process_question($question, $staff_names = '', $session_id = '')
    // {
    //     $original_sql = trim($this->ask_gpt_sql($question, $staff_names));
    //     $original_sql = preg_replace('/^```sql|^```|```$/i', '', $original_sql);
    //     $original_sql = trim($original_sql);
    //     // $cleaned_sql = preg_replace('/^(\s*SET\s+@[\w]+(?:\s*:=\s*[^;]+)?;\s*)+/i', '', $original_sql);
    //     $cleaned_sql = preg_replace('/^(\s*SET\s+@[\w]+(?:\s*:=\s*[^;]+)?;\s*)+/i', '', $original_sql);
    //     if (!preg_match('/\bselect\b/i', $cleaned_sql)) {
    //         return [
    //             'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> GPT chưa được huấn luyện câu hỏi này.</strong></div>',
    //             'sql' => $original_sql
    //         ];
    //     }

    //     // 🔍 Gửi đến GPT để kiểm tra và sửa lại nếu cần
    //     $validated_sql = trim($this->validate_and_fix_sql_with_gpt($original_sql));
    //     $validated_sql = preg_replace('/^```sql|^```|```$/i', '', $validated_sql);
    //     $cleaned_sqls = preg_replace('/^(\s*SET\s+@[\w]+(?:\s*:=\s*[^;]+)?;\s*)+/i', '', $validated_sql);
    //     if (!preg_match('/\bselect\b/i', $cleaned_sqls)) {
    //         return [
    //             'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> GPT chưa được huấn luyện câu hỏi này.</strong></div>',
    //             'sql' => $validated_sql
    //         ];
    //     }

    //     // 🧪 Kiểm tra cấu trúc
    //     try {
    //         $stmt = $this->db->conn_id->prepare($validated_sql);
    //         if ($stmt === false) throw new Exception('SQL chuẩn bị thất bại');
    //     } catch (Exception $e) {
    //         return [
    //             'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #ffc107; background-color: #fffdf5;"><strong> SQL không đúng cấu trúc. Có thể GPT hiểu sai schema.</strong></div>',
    //             'sql' => $validated_sql
    //         ];
    //     }

    //     // ✅ Thực thi
    //     try {
    //         $result = $this->db->query($validated_sql)->result_array();
    //     } catch (Exception $e) {
    //         return [
    //             'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> SQL bị lỗi khi truy vấn.</strong></div>',
    //             'sql' => $validated_sql
    //         ];
    //     }

    //     if (empty($result)) {
    //         return [
    //             'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #ff9800; background-color: #fffdf5;"><strong> Không tìm thấy dữ liệu phù hợp.</strong></div>',
    //             'sql' => $validated_sql
    //         ];
    //     }

    //     // 🔎 Có dữ liệu → gọi GPT tóm tắt
    //     $summary = $this->ask_gpt_summary($question, $result);
    //     return ['answer' => $summary, 'sql' => $validated_sql, 'data' => $result];
    // }
    public function process_question($question, $staff_names = '', $session_id = '', $start_date = '', $end_date = '', $sql_question = '', $fiiter = '', $fiiter_new = '')
    {
        if (empty($sql_question)) {
            // Ghép thêm điều kiện vào câu hỏi cho GPT
            $extended_question = $question;

            if (!empty($staff_names)) {
                $extended_question .= ". Chỉ liên quan đến các nhân viên: {$staff_names}";
            }

            if (!empty($start_date) && !empty($end_date)) {
                $extended_question .= ". Chỉ lấy dữ liệu thời gian ngày giờ từ '{$start_date} 00:00:00' đến '{$end_date} 23:59:59'";
            }
            if (!empty($fiiter)) {
                $extended_question .= $fiiter;
            }

            // Gửi đến GPT để sinh SQL
            $original_sql = trim($this->ask_gpt_sql($extended_question, $staff_names));
            $original_sql = preg_replace('/^```sql|^```|```$/i', '', $original_sql);
            $original_sql = trim($original_sql);

            $cleaned_sql = preg_replace('/^(\s*SET\s+@[\w]+(?:\s*:=\s*[^;]+)?;\s*)+/i', '', $original_sql);
            if (!preg_match('/\bselect\b/i', $cleaned_sql)) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> GPT chưa được huấn luyện câu hỏi này.</strong></div>',
                    'sql' => $original_sql
                ];
            }
            // // Kiểm tra và sửa SQL nếu cần
            $validated_sql = trim($this->validate_and_fix_sql_with_gpt($original_sql));
            $validated_sql = preg_replace('/^```sql|^```|```$/i', '', $validated_sql);

            $cleaned_sqls = preg_replace('/^(\s*SET\s+@[\w]+(?:\s*:=\s*[^;]+)?;\s*)+/i', '', $validated_sql);

            if (!preg_match('/\bselect\b/i', $cleaned_sqls)) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> GPT chưa được huấn luyện câu hỏi này.</strong></div>',
                    'sql' => $validated_sql
                ];
            }

            // Thử chuẩn bị SQL
            try {
                $stmt = $this->db->conn_id->prepare($validated_sql);
                if ($stmt === false) throw new Exception('SQL chuẩn bị thất bại');
            } catch (Exception $e) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #ffc107; background-color: #fffdf5;"><strong> SQL không đúng cấu trúc. Có thể GPT hiểu sai schema.</strong></div>',
                    'sql' => $validated_sql
                ];
            }
            // Thực thi SQL
            try {
                // $result = $this->db->query($validated_sql)->result_array();
            } catch (Exception $e) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> SQL bị lỗi khi truy vấn.</strong></div>',
                    'sql' => $validated_sql
                ];
            }

            if (empty($result)) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #ff9800; background-color: #fffdf5;"><strong> Không tìm thấy dữ liệu phù hợp.</strong></div>',
                    'sql' => $validated_sql
                ];
            }
        } else {
            if (!empty($staff_names)) {
                $staff_names = explode(', ', $staff_names);
                $text_staff_names = '';
                foreach ($staff_names as $key => $value) {
                    $text_staff_names .= "'" . $value . "',";
                }
                $sql_question = str_replace('{staff}', trim($text_staff_names, ','), $sql_question);
            }
            if (!empty($start_date) && !empty($end_date)) {
                $sql_question = str_replace('{from_date}', $start_date, $sql_question);
                $sql_question = str_replace('{to_date}', $end_date, $sql_question);
            }
            if (!empty($fiiter_new)) {
                $sql_question = str_replace('{detail_id}', $fiiter_new, $sql_question);
            }
            $validated_sql = $sql_question;
            try {
                // $result = $this->db->query($validated_sql)->result_array();
            } catch (Exception $e) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #f44336; background-color: #fff6f6;"><strong> SQL bị lỗi khi truy vấn.</strong></div>',
                    'sql' => $validated_sql
                ];
            }

            if (empty($result)) {
                return [
                    'answer' => '<div class="gpt-html-output" style="border-left: 5px solid #ff9800; background-color: #fffdf5;"><strong> Không tìm thấy dữ liệu phù hợp.</strong></div>',
                    'sql' => $validated_sql
                ];
            }
        }
        // Tóm tắt kết quả bằng GPT
        $summary = $this->ask_gpt_summary($question, $result);
        return ['answer' => $summary, 'sql' => $validated_sql, 'data' => $result];
    }
    private function ask_gpt_sql($question, $staff_names = '')
    {
        $apiKey = $this->api_key;
        $staff_filter = '';
        if (!empty($staff_names)) {
            $staff_filter = "\n📌 Chỉ lấy các dữ liệu do nhân viên: {$staff_names}. tạo";
        }
        $messages = [
            ['role' => 'system', 'content' => "Bạn là chuyên gia viết SQL phân tích dữ liệu. Trả lời duy nhất bằng 1 câu SQL chuẩn, theo đúng các quy tắc dưới đây:

QUY TẮC KỸ THUẬT:
- KHÔNG dùng WITH.
- KHÔNG bỏ dấu tiếng Việt trong điều kiện lọc (phải giữ nguyên có dấu).
- KHÔNG SELECT cột trần, phải ghi rõ tên bảng (vd: tbl_car_history.date_start).
- KHÔNG dùng alias mơ hồ như a, b, t1, t2.
- KHÔNG tóm tắt, KHÔNG giải thích.
- KHÔNG được lược bỏ kết quả nếu không có dữ liệu — phải hiện đầy đủ ngày (gợi ý tạo bảng ngày ảo).
- KHÔNG dùng từ khóa SQL làm alias (vd: `date`, `group`, `value`, `code`, `key`,...).

YÊU CẦU ĐẶC BIỆT:
- Phải tạo bảng ngày ảo từ ngày đến ngày (gợi ý dùng CROSS JOIN hoặc UNION).
- Dù không có dữ liệu trong bảng chính (vd: không có hành trình), vẫn phải hiện 1 dòng tương ứng với ngày đó và tài xế.
- Dùng CROSS JOIN giữa bảng ngày ảo và nhân viên → sau đó LEFT JOIN dữ liệu.
- Dữ liệu được tính từ các bảng thuộc `$this->schema_description_bh` (đã khai báo đầy đủ schema, giữ đúng tên bảng và cột).
- Chỉ trả về đúng 1 câu SQL, không giải thích."],
            ['role' => 'user', 'content' =>  "Không được bỏ dấu tiếng việt.Hãy tạo SQL cho câu hỏi: \"" . $question . "\"{$staff_filter}"]
        ];
        return $this->call_gpt($messages);
    }

    private function ask_gpt_summary($question, $data)
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $messages = [
            [
                'role' => 'system',
                'content' => "Hãy đóng vai một trợ lý dữ liệu và **luôn trả kết quả bằng HTML thật**, không được mã hóa hoặc dùng ký tự escape như `&lt;`, `<br />`, `&nbsp;`. 

Nếu câu hỏi có yêu cầu liệt kê danh sách, thống kê top, hoặc biểu đồ – hãy trả kết quả dưới dạng:

1. Một bảng `<table>` HTML đẹp, có thẻ `<thead>` và `<tbody>`, trình bày chuyên nghiệp css đẹp lên.
3. Không bao giờ trả ký tự dạng `&lt;`, không dùng `<br />`, không thụt dòng. Trả HTML thô có thể dùng trực tiếp với `.innerHTML`.
4. KHÔNG BAO GIỜ trả thẻ `<!DOCTYPE html>` hay toàn bộ `<html>`, chỉ phần nội dung có thể gán trực tiếp vào trang đang mở."
            ],
            [
                'role' => 'user',
                'content' => "Từ câu hỏi: \"$question\"\nDữ liệu truy vấn trả về là:\n$json\n\nHãy trình bày kết quả bằng HTML."
            ]
        ];
        return $this->call_gpt($messages);
    }
    private function call_gpt($messages)
    {
        $apiKey = $this->api_key;
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => 'gpt-4o-mini',
                'temperature' => 0.2,
                'messages' => $messages
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response, true);
        return $json['choices'][0]['message']['content'] ?? 'GPT không phản hồi.';
    }
    private function validate_and_fix_sql_with_gpt($sql)
    {
        $messages = [
            [
                'role' => 'system',
                'content' => "Bạn là chuyên gia SQL cho hệ thống ERP.Không dùng WITH,Kiểm tra trùng ambiguous, Dưới đây là cấu trúc dữ liệu:\n" . $this->schema_description . "\nLưu ý: Nếu SQL có sai, hãy sửa lại đúng, Chỉ sửa sai không thay đổi cấu trúc. Chỉ trả về 1 câu lệnh SQL duy nhất."
            ],
            [
                'role' => 'user',
                'content' => "Câu SQL sau có lỗi gì không? Nếu có, hãy sửa lại đúng và trả về SQL mới duy nhất.\n\n$sql.\n\n Chỉ trả về 1 câu lệnh SQL duy nhất, không cần giải thích gì thêm"
            ]
        ];
        return $this->call_gpt($messages);
    }
    public function run_gpt()
    {
        $question_id = $this->input->post('question_id');
        $staff_ids = $this->input->post('staffid') ?? [];
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');

        $this->load->model('askgpt_model');
        $this->load->model('staff_model');
        $this->load->model('suggest_questions_model');

        // Lấy câu hỏi
        $question_obj = $this->suggest_questions_model->get($question_id);
        $question_text = $question_obj->question ?? 'Không tìm thấy câu hỏi';

        // Lấy tên nhân viên nếu có
        $staff_names = '';
        if (!empty($staff_ids)) {
            $staffs = $this->staff_model->get();
            $staff_map = [];
            foreach ($staffs as $s) {
                $staff_map[$s['staffid']] = $s['firstname'] . ' ' . $s['lastname'];
            }
            $staff_names = implode(', ', array_map(function ($id) use ($staff_map) {
                return $staff_map[$id] ?? '';
            }, $staff_ids));
        }

        // Gọi GPT xử lý
        $res = $this->process_question($question_text, $staff_names, '', $from_date, $to_date);

        // Lưu SQL vào DB
        $this->suggest_questions_model->update($question_id, ['gpt_sql' => $res['sql']]);

        echo json_encode([
            'html' => $res['answer'] ?? '',
            'sql' => $res['sql'] ?? ''
        ]);
    }
    function mapdata_order($data)
    {
        $schema_description = [
            "date" => "",
            "customer" => "",
            "person_contact" => "",
            "address_delivery" => "",
            "id_branch" => "",
            "currencies" => "",
            "amount_to_vnd" => "",
            "type_orders" => "",
            "type_items" => "",
            "status_orders" => "",
            "employees" => "",
            "tax" => "",
            "cost_delivery" => "",
            "transporters" => "",
            "charge_party" => "",
            "note" => "",
            "item_code" => "",
            "item_name" => "",
            "product_name_customer" => "",
            "unit" => "",
            "date_ship" => "",
            "order_code" => "NP2506002213",
            "command" => "NNBF251471",
            "quantity_put" => "111",
            "quantity_loss" => "",
            "sample_quantity_item" => "",
            "total_quantity_item" => "",
            "price" => "",
            "amount" => "",
            "date_delivery" => "",
            "detail_delivery" => "",
            "note_item" => "",
            "so" => "",
            "pi" => "",
            "po_style" => "",
            "item_code_tem" => ""
        ];
        $messages = [
            [
                'role' => 'system',
                'content' => "Bạn là chuyên gia map dữ liệu.Tôi có mẫu json sau đây :\n" . json_encode($schema_description) . "\nLưu ý:Chỉ trả về mảng json này và không giải thích gì thêm."
            ],
            [
                'role' => 'user',
                'content' => "Tôi có data này: \n" . $data . "\n hãy map lại theo json tôi đuâ"
            ]
        ];
        return $this->call_gpt($messages);
    }
}
