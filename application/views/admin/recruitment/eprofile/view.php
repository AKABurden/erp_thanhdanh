<!-- PHẦN CSS CUSTOM (Tương tự Form nhưng tinh chỉnh cho chế độ xem) -->
<style>
    /* 1. Cấu trúc Modal & Font chữ */
    #view_requirements_modal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        font-family: 'Segoe UI', 'Roboto', Helvetica, Arial, sans-serif;
        background-color: #f8f9fc;
    }

    /* 2. Header (Màu khác một chút để phân biệt với form sửa - dùng màu xanh lá hoặc giữ nguyên) */
    #view_requirements_modal .modal-header {
        padding: 15px 25px;
        border-radius: 8px 8px 0 0;
        border-bottom: none;
        color: #fff;
    }

    #view_requirements_modal .modal-title {
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
    }

    #view_requirements_modal .close {
        color: #fff;
        opacity: 0.8;
        text-shadow: none;
        font-size: 20px;
    }

    #view_requirements_modal .modal-body {
        padding: 20px 25px;
    }

    /* 3. Card Layout */
    #view_requirements_modal .view-section-card {
        background: #fff;
        padding: 15px 20px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #edf2f9;
        margin-bottom: 15px;
        position: relative;
        height: 100%;
    }

    #view_requirements_modal .section-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #4e73df; /* Màu xanh lá đồng bộ header */
        margin-bottom: 12px;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 5px;
        display: inline-block;
    }

    /* 4. Hiển thị giá trị (Read-only styles) - Đã chuyển từ Input sang Div */
    #view_requirements_modal .view-group {
        margin-bottom: 15px;
    }

    #view_requirements_modal label {
        font-size: 11px;
        font-weight: 600;
        color: #858796;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }

    /* Style cho giá trị hiển thị: Thay vì border-bottom, dùng background nhẹ */
    #view_requirements_modal .view-value {
        font-size: 13px;
        color: #333;
        font-weight: 500;
        padding: 8px 12px;
        background-color: #f8f9fc; /* Nền xám rất nhạt */
        border-radius: 4px;
        border: 1px solid transparent; /* Giữ kích thước ổn định */
        min-height: 36px; /* Cao bằng input chuẩn */
        display: flex;
        align-items: center;
    }

    #view_requirements_modal .view-value.highlight {
        color: #4e73df;
        font-weight: 700;
        background-color: #e8fcf5; /* Nền xanh nhạt cho phần nổi bật */
        color: #0f6848;
    }

    /* Style cho phần JD chi tiết */
    .jd-view-container {
        background-color: #f8f9fc;
        border-radius: 6px;
        padding: 12px 15px;
        border: 1px solid #e3e6f0;
    }

    .jd-view-item {
        margin-bottom: 12px;
    }

    .jd-view-label {
        font-weight: 700;
        color: #4e73df;
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .jd-view-content {
        background: #fff;
        padding: 10px;
        border-radius: 4px;
        border-left: 3px solid #4e73df;
        font-size: 13px;
        line-height: 1.6;
        color: #5a5c69;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    .modal-footer {
        background-color: #fff;
        border-top: 1px solid #eaecf4;
        padding: 15px 25px;
        border-radius: 0 0 8px 8px;
    }

    /* Utility */
    .text-right { text-align: right; }
    .mb-0 { margin-bottom: 0 !important; }
    /*#4e73df*/
</style>

<div class="modal fade" id="view_requirements_modal" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=$title ?? ''?></h4>
            </div>

            <div class="modal-body" style="height:auto">
                <!-- KHỐI 1: VỊ TRÍ & JD -->
                <div class="view-section-card">
                    <div class="section-label">Thông tin Vị trí</div>
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Đã thay đổi Select thành Div -->
                            <div class="view-group">
                                <label>Mã phiếu YC</label>
                                <div class="view-value" style="font-size: 15px;"><?=$requirement['code'] ?? ''?></div>
                            </div>
                            <div class="view-group">
                                <label>Tên phiếu YC</label>
                                <div class="view-value" style="font-size: 15px;"><?=$requirement['name'] ?? ''?></div>
                            </div>
                            <!-- Đã thay đổi Select thành Div -->
                            <div class="view-group">
                                <label>Vị trí tuyển dụng / JD</label>
                                <div class="view-value highlight" style="font-size: 15px;"><?=$requirement['job_detail']['code']?> (<?=$requirement['job_detail']['version']?>)</div>
                            </div>
                            <div class="view-group">
                                <label>Phòng ban</label>
                                <div class="view-value"><?=!empty($requirement['room_name']) ? $requirement['room_name'] : '—'?></div>
                            </div>
                            <div class="view-group">
                                <label>Vai trò</label>
                                <div class="view-value"><?=!empty($requirement['role_name']) ? $requirement['role_name'] : '—'?></div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="jd-view-container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="jd-view-item">
                                            <div class="jd-view-label">Trách nhiệm</div>
                                            <div class="jd-view-content">
                                                <?=(!empty($requirement['type_1']) ? $requirement['type_1'] : 'Chưa cập nhật')?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="jd-view-item">
                                            <div class="jd-view-label">Phạm vi quyền hạn</div>
                                            <div class="jd-view-content">
                                                <?=(!empty($requirement['type_2']) ? $requirement['type_2'] : 'Chưa cập nhật')?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="jd-view-item">
                                            <div class="jd-view-label">Yêu cầu công việc</div>
                                            <div class="jd-view-content">
                                                <?=(!empty($requirement['type_3']) ? $requirement['type_3'] : 'Chưa cập nhật')?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="jd-view-item">
                                            <div class="jd-view-label">Tiêu chuẩn năng lực</div>
                                            <div class="jd-view-content">
                                                <?=(!empty($requirement['type_4']) ? $requirement['type_4'] : 'Chưa cập nhật')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHỐI 2: THÔNG TIN ĐỀ XUẤT -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="view-section-card">
                            <div class="section-label">Người đề xuất & Mức độ</div>
                            <!-- Đã thay đổi Select thành Div hiển thị tên -->
                            <div class="view-group">
                                <label>Người đề xuất</label>
                                <div class="view-value">
                                    <?php echo staff_profile_image($requirement['id_employee'], ['staff-profile-image-small', 'mr-1'], 'small'); ?>
                                    <?php echo get_staff_full_name($requirement['id_employee']); ?>
                                </div>
                            </div>
                            <div class="view-group">
                                <label>Mức độ ưu tiên</label>
                                <div class="view-value">
                                    <span class="label label-<?=$requirement['data_priority']['class']?>"><?=$requirement['data_priority']['name']?></span>
                                </div>
                            </div>
                            <!-- Đã thay đổi Input thành Div -->
                            <div class="view-group mb-0">
                                <label>Số lượng cần tuyển</label>
                                <div class="view-value font-weight-bold" style="font-size:16px;">
                                    <?=(isset($requirement['quantity']) ? $requirement['quantity'] : 0)?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="view-section-card">
                            <div class="section-label">Kế hoạch & Ngân sách</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Đã thay đổi Date Input thành Div -->
                                    <div class="view-group">
                                        <label>Ngày yêu cầu</label>
                                        <div class="view-value">
                                            <i class="fa fa-calendar-check-o text-success" style="margin-right: 5px;"></i>
                                            <?=(isset($requirement['date']) ? _dt($requirement['date']) : '—')?>
                                        </div>
                                    </div>
                                    <!-- Đã thay đổi Currency Input thành Div -->
                                    <div class="view-group">
                                        <label>Ngân sách dự kiến (Min)</label>
                                        <div class="view-value">
                                            <?=(isset($requirement['budget_start']) ? number_format_data($requirement['budget_start']) : 0)?> VNĐ
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="view-group">
                                        <label>Hạn chót (Deadline)</label>
                                        <div class="view-value text-danger">
                                            <i class="fa fa-clock-o" style="margin-right: 5px;"></i>
                                            <?=(isset($requirement['deadline']) ? _dt($requirement['deadline']) : '—')?>
                                        </div>
                                    </div>
                                    <div class="view-group">
                                        <label>Ngân sách dự kiến (Max)</label>
                                        <div class="view-value">
                                            <?=(isset($requirement['budget_end']) ? number_format_data($requirement['budget_end']) : 0)?> VNĐ
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KHỐI 3: GHI CHÚ -->
                <div class="view-section-card mb-0">
                    <div class="section-label">Ghi chú bổ sung</div>
                    <!-- Đã thay đổi Textarea thành Div -->
                    <div class="view-value" style="height: auto; min-height: 80px; align-items: flex-start;">
                        <?=(!empty($requirement['note']) ? nl2br($requirement['note']) : 'Không có ghi chú thêm.')?>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#view_requirements_modal').modal('show');
</script>