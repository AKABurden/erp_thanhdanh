<!-- PHẦN CSS CUSTOM (CHỈ ÁP DỤNG TRONG MODAL NÀY) -->
<style>
    /* 1. Cấu trúc Modal & Font chữ */
    #detail_requirements_modal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        font-family: 'Segoe UI', 'Roboto', Helvetica, Arial, sans-serif;
        background-color: #f8f9fc;
    }

    /* 2. Header hiện đại với Gradient */
    #detail_requirements_modal .modal-header {
        /* Bật lại gradient để chữ trắng nổi bật */
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        padding: 15px 25px;
        border-radius: 8px 8px 0 0;
        border-bottom: none;
        color: #fff;
    }

    #detail_requirements_modal .modal-title {
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
    }

    #detail_requirements_modal .modal-title i {
        margin-right: 10px;
        opacity: 0.8;
    }

    #detail_requirements_modal .close {
        color: #fff;
        opacity: 0.8;
        text-shadow: none;
        font-size: 20px;
        font-weight: 300;
        margin-top: -2px;
    }
    #detail_requirements_modal .close:hover {
        opacity: 1;
    }

    /* 3. Body & Layout thẻ (Card) */
    #detail_requirements_modal .modal-body {
        padding: 20px 25px;
    }

    /* Card bao quanh từng nhóm thông tin */
    #detail_requirements_modal .form-section-card {
        background: #fff;
        padding: 15px 20px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #edf2f9;
        margin-bottom: 15px;
        position: relative;
        height: 100%; /* Để các card cùng hàng cao bằng nhau */
    }

    /* Tiêu đề của từng khối */
    #detail_requirements_modal .section-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #4e73df; /* Màu xanh nổi bật tiêu đề */
        margin-bottom: 12px;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 5px;
        display: inline-block;
    }

    /* 4. Tinh chỉnh Form Control */
    #detail_requirements_modal label {
        font-size: 12px;
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 5px;
    }

    #detail_requirements_modal .form-control {
        border-radius: 4px;
        height: 34px;
        border: 1px solid #d1d3e2;
        background-color: #fff;
        font-size: 13px;
        color: #495057;
        transition: all 0.2s;
        padding: 6px 12px;
    }

    #detail_requirements_modal .form-control:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.15);
    }

    #detail_requirements_modal .input-group-addon {
        background-color: #f8f9fc;
        border: 1px solid #d1d3e2;
        border-left: none;
        color: #6e707e;
        border-radius: 0 4px 4px 0;
        font-weight: 600;
        font-size: 11px;
        padding: 6px 10px;
    }

    #detail_requirements_modal textarea.form-control {
        height: auto;
        resize: vertical;
    }

    /* 5. Footer & Buttons */
    #detail_requirements_modal .modal-footer {
        background-color: #fff;
        border-top: 1px solid #eaecf4;
        padding: 15px 25px;
        border-radius: 0 0 8px 8px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    #detail_requirements_modal .btn {
        padding: 6px 20px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: none;
    }

    #detail_requirements_modal .btn-default {
        background-color: #f1f3f9;
        color: #6c757d;
        border: 1px solid #d1d3e2;
        box-shadow: none;
    }

    #detail_requirements_modal .btn-info {
        background-color: #4e73df;
        color: #fff;
    }

    #detail_requirements_modal .bootstrap-select .btn {
        background-color: #fff;
        border: 1px solid #d1d3e2;
        border-radius: 4px;
        height: 34px;
        padding: 6px 12px;
    }

    /* ========================================= */
    /* NEW CSS: STYLE CHO PHẦN THÔNG TIN JD (YÊU CẦU/NĂNG LỰC) */
    /* ========================================= */
    .jd-info-container {
        background-color: #f8f9fc;
        border-radius: 6px;
        padding: 10px 15px;
        border: 1px solid #e3e6f0;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .jd-info-item {
        margin-bottom: 10px;
    }
    .jd-info-item:last-child {
        margin-bottom: 0;
    }

    .jd-info-label {
        font-weight: 700;
        color: #4e73df;
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
    }
    .jd-info-label i {
        margin-right: 6px;
        font-size: 12px;
        opacity: 0.8;
    }

    /* Box nội dung chữ */
    .jd-info-content {
        color: #5a5c69;
        background: #fff;
        padding: 8px 12px;
        border-radius: 4px;
        border-left: 3px solid #bac8f3; /* Viền trái làm điểm nhấn */
        font-size: 12px;
        line-height: 1.5;
        min-height: 35px; /* Giữ khung không bị xẹp */
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    /* Utility classes */
    .mb-0 { margin-bottom: 0 !important; }
    .mt-15 { margin-top: 15px; }
    .pr-2 { padding-right: 0.5rem; }
    .pl-2 { padding-left: 0.5rem; }

    input.datepicker[readonly], input.datetimepicker[readonly] {
        cursor: pointer;
        background-color: #fff; /* hoặc giữ màu cũ */
    }
</style>
<style>
    .recruitment-container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 500px;
        margin: 0 auto;
    }

    /* Nhãn tiêu đề */
    .control-label-custom {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 15px;
        display: block;
        color: #333;
    }

    .text-danger-custom {
        color: #d9534f;
        margin-left: 3px;
    }

    /* Group chứa 2 button */
    .reason-selector {
        display: flex;
        gap: 15px;
    }

    /* Ẩn radio button mặc định */
    .reason-selector input[type="radio"] {
        display: none;
    }

    /* Style cho label (Nút bấm giả) */
    .reason-item {
        flex: 1;
        text-align: center;
        padding: 10px 10px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        background-color: #fff;
        transition: all 0.2s ease-in-out;
        user-select: none;
        display: block;
        margin-bottom: 0; /* Reset mặc định label của BS3 */
    }

    .reason-item:hover {
        border-color: #337ab7;
        background-color: #fcfcfc;
    }

    /* Khi radio được chọn - Trạng thái Active */
    .reason-selector input[type="radio"]:checked + .reason-item {
        border: 2px solid #3366ff;
        background-color: #f0f4ff;
        color: #1a237e;
    }

    /* Đảm bảo responsive trên mobile */
    @media (max-width: 480px) {
        .reason-selector {
            flex-direction: column;
        }
    }
</style>

<div class="modal fade" id="detail_requirements_modal" role="dialog">
    <!-- Updated: min-width 65% as requested -->
    <div class="modal-dialog modal-lg" style="min-width: 65%;">
        <div class="modal-content">
            <?php
            $disabled = array();
            if (isset($items)) {
                $disabled = array('disabled' => true);
            }
            echo form_open(admin_url('recruitment/detail_requirements/' . ($requirement['id'] ?? '')), array('id' => 'requirements-form'));
            ?>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?=$title ?? 'Tạo Yêu Cầu Tuyển Dụng' ?> </span>
                </h4>
            </div>

            <div class="modal-body" style="height:auto">

                <!-- CARD 1: VỊ TRÍ & JD (Đã làm đẹp phần mô tả) -->
                <div class="form-section-card">
                    <div class="section-label">Vị trí & Mô tả công việc</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <?php $value = isset($requirement['name']) ? $requirement['name'] : '';?>
                                <div class="form-group">
                                    <label for="budget_start">Tên Phiếu</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= $value ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label-custom">
                                        Lý Do Tuyển Dụng <span class="text-danger-custom">*</span>
                                    </label>
                                    <div class="reason-selector">
                                        <!-- Option 1: Thay Thế -->
                                        <input type="radio" name="reason" id="reason_replace" data-sub="Vị trí tuyển dụng để thay thế" value="1" <?=(isset($requirement['reason']) && $requirement['reason'] == 1 ? 'checked' : '')?>>
                                        <label for="reason_replace" class="reason-item">
                                            Thay Thế
                                        </label>
                                        <input type="radio" name="reason" id="reason_new" value="2" data-sub="Vị trí tuyển dụng mới" <?=(!isset($requirement['reason']) || $requirement['reason'] == 2 ? 'checked' : '')?>>
                                        <label for="reason_new" class="reason-item">
                                            Tuyển Mới
                                        </label>
                                    </div>
                                </div>

                                <?php $value = (isset($requirement['role_id']) ? ($requirement['role_id']) : NULL);?>
                                <?= render_select('role_id', ($list_role ?? []) , ['roleid', 'code_role', 'name'],'<span class="span_reason"></span>', $value); ?>
                            </div>

<!--                            --><?php //$value = isset($requirement['id']) ? $requirement['id_jd'] : '';?>
<!--                            --><?php //= render_select('id_jd', ($list_job_detail ?? []), array('id', 'code', 'title_version'), 'Vị trí tuyển dụng / JD', $value, [], [], 'col-md-12 p-0'); ?>
                            <div class="col-md-12">
                                <div class="jd-info-container">
                                    <div class="jd-info-item">
                                        <div class="jd-info-label"><i class="fa fa-briefcase"></i> Phòng ban</div>
                                        <div class="jd-info-content" id="room_name"><?=!empty($requirement['room_name']) ? $requirement['room_name'] : ''?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="jd-info-container">
                                    <div class="jd-info-item">
                                        <div class="jd-info-label"><i class="fa fa-briefcase"></i> Vị trí</div>
                                        <div class="jd-info-content" id="name_role"><?=!empty($requirement['role_name']) ? $requirement['role_name'] : ''?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- Container mới cho phần thông tin -->
                            <div class="jd-info-container">
                                <div class="jd-info-item">
                                    <div class="jd-info-label"><i class="fa fa-asterisk" aria-hidden="true"></i> Trách nhiệm</div>
                                    <div class="jd-info-content" id="type_1">
                                        <?=(!empty($requirement['type_1']) ? $requirement['type_1'] : ' ... (Vui lòng chọn Vị trí/JD để xem chi tiết) ...')?>
                                    </div>
                                </div>
                                <div class="jd-info-item">
                                    <div class="jd-info-label"><i class="fa fa-quote-left" aria-hidden="true"></i> Phạm vi quyền hạn</div>
                                    <div class="jd-info-content" id="type_2">
                                        <?=(!empty($requirement['type_2']) ? $requirement['type_2'] : ' ... (Vui lòng chọn Vị trí/JD để xem chi tiết) ...')?>
                                    </div>
                                </div>
                                <div class="jd-info-item">
                                    <div class="jd-info-label"><i class="fa fa-briefcase"></i> Yêu cầu công việc</div>
                                    <div class="jd-info-content" id="type_3">
                                        <?=(!empty($requirement['type_3']) ? $requirement['type_3'] : ' ... (Vui lòng chọn Vị trí/JD để xem chi tiết) ...')?>
                                    </div>
                                </div>
                                <div class="jd-info-item">
                                    <div class="jd-info-label"><i class="fa fa-star"></i> Tiêu chuẩn năng lực</div>
                                    <div class="jd-info-content" id="type_4">
                                        <?=(!empty($requirement['type_4']) ? $requirement['type_4'] : ' ... (Vui lòng chọn Vị trí/JD để xem chi tiết) ...')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: THÔNG TIN CHUNG -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-section-card">
                            <div class="section-label">Thông tin đề xuất</div>
                            <div class="row">
                                <?php $value = (isset($requirement['id_employee']) ? $requirement['id_employee'] : get_staff_user_id());?>
                                <?= render_select('id_employee', ($list_staff ?? []), array('staffid', ['firstname', 'lastname']), 'Người đề xuất', $value, [], [], 'col-md-12 p-0'); ?>

                                <?php $value = (isset($requirement['priority']) ? $requirement['priority'] : '');?>
                                <?= render_select('priority', ($this->list_priority ?? []), array('id', 'name'), 'Mức độ ưu tiên', $value, [], [], 'col-md-12 p-0'); ?>

                                <div class="col-md-12 p-0">
                                    <?php $value = (isset($requirement['quantity']) ? number_format_data($requirement['quantity']) : 1);?>
                                    <div class="form-group">
                                        <label for="budget_start">Số lượng</label>
                                        <input type="text" name="quantity" id="quantity" class="form-control" value="<?= $value ?>" data-type="currency">
                                    </div>
                                </div>

                                <div class="col-md-12 p-0">
                                    <?php $value = (isset($requirement['hiring_manager']) ? ($requirement['hiring_manager']) : '');?>
                                    <?= render_select('hiring_manager', ($list_staff_manage_human ?? []), ['staffid', ['firstname', 'lastname']], 'Quản lý tuyển dụng', $value); ?>
                                </div>
                                <div class="col-md-12 p-0">
                                    <?php $value = (isset($requirement['role_level']) ? ($requirement['role_level']) : '');?>
                                    <?= render_select('role_level', ($role_level ?? []), ['id', 'name'], 'Cấp bậc', $value); ?>
                                </div>
                                <div class="col-md-12 p-0">
                                    <?php $value = (isset($requirement['branch']) ? ($requirement['branch']) : '');?>
                                    <?= render_select('branch', ($list_branch ?? []) , ['id', 'name', 'address'],'Địa Điểm Làm Việc', $value); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-section-card">
                            <div class="section-label"><i class="fa fa-calendar-alt"></i> Kế hoạch thời gian</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['date']) ? _dC($requirement['date']) : _dC(date('Y-m-d')));?>
                                    <?= render_date_input('date', 'Ngày yêu cầu', $value, ['readonly' => true]); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['workday']) ? _dC($requirement['workday']) : _dC(date('Y-m-d')));?>
                                    <?= render_date_input('workday', 'Ngày Nhận Việc', $value, ['readonly' => true, 'data-date-min-date' => date('Y-m-d')]); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['deadline']) ? _dt($requirement['deadline']) : '');?>
                                    <?= render_datetime_input('deadline', 'Hạn chót (Deadline)', $value, ['readonly' => true, 'data-date-min-date' => date('Y-m-d')]); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['type_of_work']) ? ($requirement['type_of_work']) : '');?>
                                    <?= render_select('type_of_work', ($this->type_of_work ?? []) , ['id', 'name'],'Loại hình làm việc', $value); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['working_style']) ? ($requirement['working_style']) : '');?>
                                    <?= render_select('working_style', ($this->working_style ?? []) , ['id', 'name'],'Hình thức làm việc', $value); ?>
                                </div>

                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['budget_code']) ? ($requirement['budget_code']) : '');?>
                                    <?= render_select('budget_code', ($costs ?? []),['id', 'code', 'name'],'Mã ngân sách', $value); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['staff_budget']) ? ($requirement['staff_budget']) : '');?>
                                    <?= render_select('staff_budget', $list_staff, ['staffid', ['firstname', 'lastname']], 'Chủ sở hữu ngân sách', $value); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-section-card">
                            <div class="section-label"><i class="fa fa-money"></i> Dự trù ngân sách</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['budget_start']) ? number_format_data($requirement['budget_start']) : 0);?>
                                    <div class="form-group mb-0">
                                        <label for="budget_start">Từ mức (Min)</label>
                                        <div class="input-group">
                                            <input type="text" name="budget_start" id="budget_start" class="form-control" value="<?= $value ?>" data-type="currency">
                                            <span class="input-group-addon">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?php $value = (isset($requirement['budget_end']) ? number_format_data($requirement['budget_end']) : 0);?>
                                    <div class="form-group mb-0">
                                        <label for="budget_end">Đến mức (Max)</label>
                                        <div class="input-group">
                                            <input type="text" name="budget_end" id="budget_end" class="form-control" value="<?= $value ?>" data-type="currency">
                                            <span class="input-group-addon">VNĐ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: CHI TIẾT -->
                <div class="form-section-card mb-0">
                    <div class="section-label">Chi tiết & Ghi chú</div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php $value = (isset($requirement['note']) ? ($requirement['note']) : '');?>
                            <?= render_textarea('note', 'Ghi Chú/Mô tả thêm', $value, ['rows' => 3, 'placeholder' => 'Mô tả chi tiết lý do (VD: Nhân sự nghỉ sinh, mở rộng team dự án X...)']); ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?= _l('close') ?>
                </button>
                <button type="submit" class="btn btn-info" id="submit" autocomplete="off">
                    <?= _l('submit') ?> <i class="fa fa-paper-plane" style="margin-left:5px;"></i>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Tự động show modal khi load
    $('#detail_requirements_modal').modal('show');

    $(function() {
        init_selectpicker();
        init_datepicker();

        // Format tiền tệ khi nhập liệu
        $('input[data-type="currency"]').on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });

        // Validate form
        _validate_form($('#requirements-form'), {
            reason: "required",
            role_id: "required",
            date: "required",
            id_employee: "required",
            hiring_manager: "required",
            priority: "required",
            budget_start: "required",
            budget_end: "required",
            budget_code: "required",
            deadline: "required",
            branch: "required",
            role_level: "required",
        }, uploadForm);

        function uploadForm(form) {
            // Hiệu ứng loading cho nút submit
            var submitBtn = $(form).find('button[type="submit"]');
            submitBtn.button('loading');

            // Unformat number trước khi submit
            var budgetStart = unformat_number($('#budget_start').val());
            var budgetEnd = unformat_number($('#budget_end').val());

            // Trick: Gán lại giá trị raw vào input (ẩn) hoặc để controller xử lý
            var data = $(form).serialize();
            var action = form.action;

            $.post(action, data).done(function(response) {
                response = JSON.parse(response);
                alert_float(response.alert_type, response.message);
                if (response.success) {
                    if(typeof tAPI !== 'undefined'){ tAPI.draw('page'); }
                    if (typeof oTable != 'undefined') {
                        oTable.draw();
                    }
                    $('#detail_requirements_modal').modal('hide');
                }
            }).always(function() {
                submitBtn.button('reset');
            });

            return false;
        }
        $('input[name="reason"]').trigger('change');
    });

    $('#role_id').change(function() {
        var roleid = $(this).val();
        console.log(1);
        if(!roleid) return;

        // Hiệu ứng loading nhẹ
        $('#type_1').text('Đang tải...');
        $('#type_2').text('Đang tải...');
        $('#type_3').text('Đang tải...');
        $('#type_4').text('Đang tải...');
        $('#name_role').text('Đang tải...');
        $('#room_name').text('Đang tải...');

        $.get(admin_url + 'recruitment/get_jd_to_role_details/' + roleid, function(data) {
            // Kiểm tra nếu trả về string JSON cần parse
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }

            if (data.result) {
                // Cập nhật nội dung mô tả
                $('#type_1').html(data.data.type_1 || 'Chưa cập nhật');
                $('#type_2').html(data.data.type_2 || 'Chưa cập nhật');
                $('#type_3').html(data.data.type_3 || 'Chưa cập nhật');
                $('#type_4').html(data.data.type_4 || 'Chưa cập nhật');
                $('#name_role').html(data.data.role_name || 'Chưa cập nhật');
                $('#room_name').html(data.data.room_name || 'Chưa cập nhật');
            } else {
                alert_float(data.alert_type, data.message);
            }
        }).fail(function() {
            alert_float('danger', 'Lỗi kết nối khi lấy thông tin JD');
        });
    })

    $('input[name="reason"]').change(function() {
        $('.span_reason').text($.trim($('input[name="reason"]:checked').attr('data-sub')));
    })



    // AJAX Load thông tin JD khi chọn
    $('#id_jd').change(function() {
        var id_jd = $(this).val();
        if(!id_jd) return;

        // Hiệu ứng loading nhẹ
        $('#type_1').text('Đang tải...');
        $('#type_2').text('Đang tải...');
        $('#type_3').text('Đang tải...');
        $('#type_4').text('Đang tải...');
        $('#name_role').text('Đang tải...');
        $('#room_name').text('Đang tải...');

        $.get(admin_url + 'recruitment/get_jd_details/' + id_jd, function(data) {
            // Kiểm tra nếu trả về string JSON cần parse
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }

            if (data.result) {
                // Cập nhật nội dung mô tả
                $('#type_1').html(data.data.type_1 || 'Chưa cập nhật');
                $('#type_2').html(data.data.type_2 || 'Chưa cập nhật');
                $('#type_3').html(data.data.type_3 || 'Chưa cập nhật');
                $('#type_4').html(data.data.type_4 || 'Chưa cập nhật');
                $('#name_role').html(data.data.role_name || 'Chưa cập nhật');
                $('#room_name').html(data.data.room_name || 'Chưa cập nhật');
            } else {
                alert_float('danger', data.message);
            }
        }).fail(function() {
            alert_float('danger', 'Lỗi kết nối khi lấy thông tin JD');
        });
    })

    /* --- Helper Functions --- */
    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.toString().replace(/[^\d\.\-]/g, "");
        }
        return _number;
    };

    function formatCurrency(input, blur) {
        var input_val = input.val();
        if (input_val === "") { return; }

        if (input_val.indexOf(".") >= 0) {
            var decimal_pos = input_val.indexOf(".");
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);
            left_side = formatNumber(left_side.replace(/\D/g, ""));
            right_side = formatNumber(right_side.replace(/\D/g, ""));
            input_val = left_side + "." + right_side;
        } else {
            input_val = formatNumber(input_val.replace(/\D/g, ""));
        }

        input.val(input_val);
    }

</script>