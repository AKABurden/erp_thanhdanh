<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    /* Modal Styles */
    .modal-dialog {
        max-width: 1600px !important;
    }

    .modal-content {
        border-radius: 10px !important;
    }

    .audit-form-modal .modal-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0;
    }

    .audit-form-modal .modal-footer {
        flex-shrink: 0;
        border-top: 2px solid #e2e8f0;
        padding: 16px 24px;
        background: #f8fafc;
    }

    /* Header */
    .audit-form-header {
        background: #542901 !important;
        color: white;
        padding: 20px 24px;
        margin: 0;
        box-shadow: 0 2px 8px rgba(45, 55, 72, 0.2);
    }

    .audit-form-header h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .audit-form-header h4 i {
        font-size: 24px;
    }

    .audit-form-subtitle {
        margin: 8px 0 0 0;
        font-size: 13px;
        opacity: 0.9;
        display: flex;
        gap: 20px;
    }

    /* Section Card */
    .audit-section-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .audit-section-header {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        padding: 16px 20px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .audit-section-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: #ebf4ff;
        color: #3182ce;
        flex-shrink: 0;
    }

    .audit-section-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
        flex: 1;
    }

    .audit-section-badge {
        display: inline-block;
        background: #4a5568;
        color: white;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 600;
    }

    .subsection-note {
        font-size: 10px;
        background: #fff3cd;
        color: #856404;
        padding: 3px 10px;
        border-radius: 4px;
        font-weight: 600;
    }

    .audit-subsection-header {
        background: #f8fafc;
        padding: 12px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 13px;
        color: #4a5568;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Audit Items */
    .audit-item-row {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.2s;
    }

    .audit-item-row:hover {
        background-color: #fafafa;
    }

    .audit-item-row.has-no-status {
        background-color: #fff5f5;
    }

    .audit-item-row:last-child {
        border-bottom: none;
    }

    .audit-status-btn {
        padding: 6px 16px;
        border: 1px solid #e0e0e0;
        background: white;
        color: #999;
        font-size: 11px;
        font-weight: 700;
        border-radius: 6px;
        transition: all 0.2s;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .audit-status-btn:hover:not(:disabled) {
        border-color: #bbb;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .audit-status-btn.btn-yes.active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-color: #059669;
        color: white;
    }

    .audit-status-btn.btn-no.active {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-color: #dc2626;
        color: white;
    }

    .note-field {
        margin-top: 10px;
        display: none;
    }

    .note-input {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #fed7aa;
        border-radius: 6px;
        font-size: 13px;
        color: #92400e;
        background: #fffbeb;
        transition: all 0.2s;
    }

    .note-input:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .note-input::placeholder {
        color: #d97706;
    }

    .critical-badge {
        display: inline-block;
        background: #fee2e2;
        color: #991b1b;
        font-size: 9px;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid #fca5a5;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 8px;
    }

    .item-text {
        font-size: 13px;
        line-height: 1.6;
        color: #424242;
    }

    .item-text.has-issue {
        color: #c62828;
        font-weight: 600;
    }

    /* Image Upload Styles */
    .audit-image-section {
        margin-top: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
    }

    .image-upload-area {
        display: inline-block;
        position: relative;
    }

    .upload-image-btn {
        padding: 8px 16px;
        background: #3182ce;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .upload-image-btn:hover {
        background: #2c5aa0;
    }

    .upload-image-btn.disabled {
        background: #ccc;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .image-preview-container {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .image-preview-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid #e0e0e0;
        cursor: pointer;
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }

    .image-remove-btn:hover {
        background: rgba(220, 38, 38, 1);
    }

    .image-upload-hint {
        font-size: 11px;
        color: #666;
        margin-top: 6px;
        font-style: italic;
    }

    button.btn.btn-default {
        display: block;
        /* Hoặc inline-block nếu cha là flex */
        margin-left: auto;
        margin-right: 0;
    }
</style>


<!-- Modal Structure -->
<div class="modal-dialog" role="document" style="width: 60%">
    <div class="modal-content"
        style="height: 95vh; display: flex; flex-direction: column; border-radius: 10px !important; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);">
        <!-- Modal Header -->
        <div class="audit-form-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h4>
                    <?php echo $title; ?>
                </h4>
                <div class="audit-form-subtitle">
                    <span><i class="fa fa-user"></i> <?php echo $audit->team_leader; ?></span>
                    <span><i class="fa fa-calendar"></i> <?php echo _d($audit->audit_date); ?></span>
                    <span><i class="fa fa-building"></i> <?php echo $audit->department; ?></span>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                style="font-size: 28px; background: none; border: none; color: #fff; opacity: 0.8;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body" style="flex: 1; overflow-y: auto; overflow-x: hidden; padding: 0; background: #f8fafc;">
            <ul class="nav nav-tabs audit-section-card" style="margin-bottom: -14px !important; position: sticky; top: 0; z-index: 100; background: #fff;" role="tablist">
                <li role="presentation" class="active">
                    <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab">
                        <i class="icon-foso fal fa-info-circle"></i>
                        <?= _l('Phiếu Audit') ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab_history" aria-controls="tab_history" onclick="loadHistoryLog()" role="tab" data-toggle="tab">
                        <i class="icon-foso fa fa-comments-o"></i>
                        <?= _l('Audi Log') ?>
                        <span class="badge menu-badge bg-warning"></span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="item_info">
                    <div style="padding: 24px;">
                        <?php foreach ($sections as $section): ?>
                            <div class="audit-section-card">
                                <!-- Section Header -->
                                <div class="audit-section-header">

                                    <h5 class="audit-section-title"><?php echo htmlspecialchars($section['title']); ?></h5>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <?php if (!empty($section['room'])): ?>
                                            <span class="label label-info" style="font-size: 11px; padding: 5px 10px;">
                                                <i class="fa fa-users"></i> <?php echo htmlspecialchars($section['room']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($section['department'])): ?>
                                            <span class="label label-primary" style="font-size: 11px; padding: 5px 10px;">
                                                <i class="fa fa-building"></i> <?php echo htmlspecialchars($section['department']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="audit-section-badge">
                                            <?php
                                            $itemCount = 0;
                                            if (!empty($section['items'])) {
                                                $itemCount += count($section['items']);
                                            }
                                            if (!empty($section['subsections'])) {
                                                foreach ($section['subsections'] as $sub) {
                                                    $itemCount += count($sub['items']);
                                                }
                                            }
                                            echo $itemCount . ' tiêu chí';
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <!-- Direct Items (no subsections) -->
                                    <?php if (!empty($section['items'])): ?>
                                        <?php foreach ($section['items'] as $item): ?>
                                            <?php echo render_audit_item($item, $audit->status); ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- Subsections -->
                                    <?php if (!empty($section['subsections'])): ?>
                                        <?php foreach ($section['subsections'] as $subsection): ?>
                                            <div>
                                                <div class="audit-subsection-header">
                                                    <span><i class="fa fa-folder-o"></i>
                                                        <?php echo htmlspecialchars($subsection['title']); ?></span>
                                                    <?php if (isset($subsection['note']) && !empty($subsection['note'])): ?>
                                                        <span
                                                            class="subsection-note"><?php echo htmlspecialchars($subsection['note']); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php foreach ($subsection['items'] as $item): ?>
                                                    <?php echo render_audit_item($item, $audit->status); ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_history">
                    <div id="historyLogContainer"></div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer"
            style="flex-shrink: 0; border-top: 2px solid #e2e8f0; padding: 16px 24px; background: #f8fafc;">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div> </div>
                <div style="float: right;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Đóng
                    </button>
                    <?php if ($audit->status == 'COMPLETED'): ?>
                        <span class="label label-success" style="padding: 8px 16px; font-size: 13px;">
                            <i class="fa fa-check-circle"></i> Đã hoàn thành
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Modal is already shown by tnhModal, just initialize event handlers

        // Initialize sequential checking - disable all items except the first unchecked one
        updateSequentialChecking();
        updateImageUploadSections();

        // Auto-save when status changes
        $('.audit-status-btn').on('click', function() {
            var $btn = $(this);
            var itemId = $btn.data('item-id');
            var status = $btn.data('status');
            var $row = $btn.closest('.audit-item-row');
            var currentStatus = $row.find('.audit-status-btn.active').length > 0 ? $row.find('.audit-status-btn.active').data('status') : null;

            // Check if trying to change a completed item that has next items completed
            if (currentStatus) {
                // Check if any items after this one have been completed
                var hasNextCompleted = false;
                var $allRows = $('.audit-item-row');
                var currentIndex = $allRows.index($row);

                for (var i = currentIndex + 1; i < $allRows.length; i++) {
                    var $nextRow = $allRows.eq(i);
                    if ($nextRow.find('.audit-status-btn.active').length > 0) {
                        hasNextCompleted = true;
                        break;
                    }
                }

                if (hasNextCompleted) {
                    alert_float('warning', 'Không được phép thay đổi kết quả vì đã hoàn thành các bước tiếp theo!');
                    return false;
                }
            }

            // Check sequential order - must complete previous items first
            if (!currentStatus) {
                var canProceed = checkSequentialOrder($row);
                if (!canProceed) {
                    alert_float('warning', 'Vui lòng hoàn thành các bước trước đó!');
                    return false;
                }
            }

            // If trying to change to YES and has production report, prevent it
            if (status === 'yes') {
                var hasReport = $row.find('.c_modal').length > 0;
                if (hasReport) {
                    alert_float('warning', 'Không thể chuyển sang YES vì đã tạo phiếu báo cáo!');
                    return false;
                }
            }

            // Update UI
            $row.find('.audit-status-btn').removeClass('active');
            $btn.addClass('active');

            // Show/hide production report section based on status
            var $reportSection = $row.find('.production-report-section');
            if (status === 'no') {
                $reportSection.show();

                // Auto open create report in new tab and close modal
                var $createBtn = $row.find('.create-report-btn');
                if ($createBtn.length > 0) {
                    var reportUrl = $createBtn.attr('href');
                    window.open(reportUrl, '_blank');
                    $('#tnhModal').modal('hide');
                }
            } else {
                $reportSection.hide();
            }

            // Highlight row if NO
            if (status === 'no') {
                $row.addClass('has-no-status');
                $row.find('.item-text').addClass('has-issue');
            } else {
                $row.removeClass('has-no-status');
                $row.find('.item-text').removeClass('has-issue');
            }

            saveAuditItem(itemId, status);

            // Update sequential checking and load images after save
            setTimeout(function() {
                // Load current item images
                loadAuditItemImages(itemId, $row);

                updateSequentialChecking();
                updateImageUploadSections();
            }, 100);
        });

        // Handle image upload - remove previous handlers to avoid duplicates
        $(document).off('change', '.audit-image-input').on('change', '.audit-image-input', function() {
            var $input = $(this);
            var $row = $input.closest('.audit-item-row');
            var itemId = $input.data('item-id');
            var files = this.files;

            if (files.length > 0) {
                var formData = new FormData();

                for (var i = 0; i < files.length; i++) {
                    formData.append('images[]', files[i]);
                }
                formData.append('item_id', itemId);
                formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

                $.ajax({
                    url: '<?php echo admin_url("audit_management/uploadAuditItemImages"); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.result == 1) {
                            // Reload the images for this item
                            loadAuditItemImages(itemId, $row);
                            alert_float('success', 'Upload ảnh thành công!');
                        } else {
                            alert_float('danger', data.message);
                        }
                        $input.val(''); // Reset input
                    },
                    error: function() {
                        alert_float('danger', 'Có lỗi xảy ra khi upload ảnh!');
                        $input.val('');
                    }
                });
            }
        });

        // Handle image deletion - remove previous handlers to avoid duplicates
        $(document).off('click', '.image-remove-btn').on('click', '.image-remove-btn', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var imageId = $btn.data('image-id');
            var $row = $btn.closest('.audit-item-row');
            var itemId = $row.find('.audit-status-btn').first().data('item-id');

            if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
                return;
            }

            $.ajax({
                url: '<?php echo admin_url("audit_management/deleteAuditItemImage"); ?>',
                type: 'POST',
                data: {
                    image_id: imageId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.result == 1) {
                        loadAuditItemImages(itemId, $row);
                        alert_float('success', 'Xóa ảnh thành công!');
                    } else {
                        alert_float('danger', response.message);
                    }
                }
            });
        });

        // View image in modal - remove previous handlers to avoid duplicates
        $(document).off('click', '.image-preview-item').on('click', '.image-preview-item', function(e) {
            if (!$(e.target).hasClass('image-remove-btn')) {
                var imageUrl = $(this).find('img').attr('src');
                var modal = '<div class="modal fade" id="imageViewModal" tabindex="-1">' +
                    '<div class="modal-dialog modal-lg">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body" style="padding: 0;">' +
                    '<img src="' + imageUrl + '" style="width: 100%; height: auto;">' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>' +
                    '</div>' +
                    '</div></div></div>';
                $(modal).modal('show');
                $(modal).on('hidden.bs.modal', function() {
                    $(this).remove();
                });
            }
        });
    });

    function loadAuditItemImages(itemId, $row) {
        $.ajax({
            url: '<?php echo admin_url("audit_management/getAuditItemImages"); ?>',
            type: 'GET',
            data: {
                item_id: itemId
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1) {
                    var $container = $row.find('.image-preview-container');
                    $container.empty();

                    var hasStatus = $row.find('.audit-status-btn.active').length > 0;
                    var isNoStatus = $row.hasClass('has-no-status');
                    var needsReportCreation = $row.find('.create-report-btn').length > 0;
                    var hasReport = $row.find('.c_modal').length > 0;

                    // Can delete if:
                    // 1. No status yet
                    // 2. OR status=NO but no report created yet
                    var canDelete = !hasStatus || (isNoStatus && needsReportCreation && !hasReport);

                    response.data.forEach(function(image) {
                        var imageHtml = '<div class="image-preview-item">';
                        imageHtml += '<img src="<?php echo base_url(); ?>uploads/audit_items/' + image.file_name + '" alt="Audit Image">';

                        // Show delete button based on conditions
                        if (canDelete) {
                            imageHtml += '<button type="button" class="image-remove-btn" data-image-id="' + image.id + '">';
                            imageHtml += '<i class="fa fa-times"></i></button>';
                        }

                        imageHtml += '</div>';
                        $container.append(imageHtml);
                    });

                    // Update image upload sections after loading images
                    updateImageUploadSections();
                }
            }
        });
    }

    // Function to highlight next item without disabling buttons
    function updateSequentialChecking() {
        var $allRows = $('.audit-item-row');
        var foundFirstUnchecked = false;
        var hasIncompleteItem = false;

        $allRows.each(function() {
            var $row = $(this);
            var $buttons = $row.find('.audit-status-btn');
            var hasStatus = $row.find('.audit-status-btn.active').length > 0;
            var isNoStatus = $row.hasClass('has-no-status');
            var hasUncompletedReport = $row.find('.label-warning').length > 0;
            var needsReportCreation = $row.find('.create-report-btn').length > 0;

            // Reset all button styles - always enabled and visible
            $buttons.prop('disabled', false).css({
                'opacity': '1',
                'cursor': 'pointer'
            });

            // Check if this row is NO and needs report creation or has uncompleted report
            if (isNoStatus && (needsReportCreation || hasUncompletedReport)) {
                hasIncompleteItem = true;
            }

            if (!hasStatus && !foundFirstUnchecked && !hasIncompleteItem) {
                // This is the first unchecked item and no incomplete items above - highlight it
                $row.css({
                    'background': '#fffbeb',
                    'border-left': '4px solid #f59e0b'
                });
                foundFirstUnchecked = true;
            } else if (!hasStatus) {
                // Unchecked item - show normal style
                $row.css({
                    'background': '#f9fafb',
                    'border-left': '4px solid #e5e7eb'
                });
            } else if (hasStatus) {
                // This item is checked - show as completed
                $row.css({
                    'background': '',
                    'border-left': ''
                });
            }

            if (isNoStatus && (needsReportCreation || hasUncompletedReport)) {
                // This item is NO and needs report - highlight
                $row.css({
                    'background': '#fef3c7',
                    'border-left': '4px solid #f59e0b'
                });
            }
        });
    }

    // Update image upload sections based on current step and previous step
    function updateImageUploadSections() {
        var $allRows = $('.audit-item-row');
        var foundFirstUnchecked = false;
        var hasIncompleteItem = false;
        var currentStepIndex = -1;
        var previousStepIndex = -1;

        // First pass: find current step index
        $allRows.each(function(index) {
            var $row = $(this);
            var hasStatus = $row.find('.audit-status-btn.active').length > 0;
            var isNoStatus = $row.hasClass('has-no-status');
            var hasUncompletedReport = $row.find('.label-warning').length > 0;
            var needsReportCreation = $row.find('.create-report-btn').length > 0;
            var hasReport = $row.find('.c_modal').length > 0;

            // current step = first row that is:
            // 1) chưa check
            // 2) hoặc NO nhưng chưa có report
            // 3) hoặc report chưa hoàn thành
            var isCurrentCandidate =
                (!hasStatus) ||
                (isNoStatus && needsReportCreation && !hasReport) ||
                (isNoStatus && hasUncompletedReport);

            if (!foundFirstUnchecked && isCurrentCandidate) {
                currentStepIndex = index;
                // Find previous completed step
                for (var i = index - 1; i >= 0; i--) {
                    var $prevRow = $allRows.eq(i);
                    var prevHasStatus = $prevRow.find('.audit-status-btn.active').length > 0;
                    if (prevHasStatus) {
                        previousStepIndex = i;
                        break;
                    }
                }
                foundFirstUnchecked = true;
            }
        });

        // Second pass: update UI
        $allRows.each(function(index) {
            var $row = $(this);
            var $imageSection = $row.find('.audit-image-section');
            var $uploadArea = $row.find('.image-upload-area');
            var hasStatus = $row.find('.audit-status-btn.active').length > 0;
            var isNoStatus = $row.hasClass('has-no-status');
            var needsReportCreation = $row.find('.create-report-btn').length > 0;
            var hasReport = $row.find('.c_modal').length > 0;

            // Can upload if:
            // 1. Is current or previous step AND no status yet
            // 2. OR is current/previous step AND status=NO but no report created yet
            var isCurrentOrPrevious = (index === currentStepIndex || index === previousStepIndex);
            var canUpload = false;

            if (isCurrentOrPrevious) {
                if (!hasStatus) {
                    // No status yet
                    canUpload = true;
                } else if (isNoStatus && needsReportCreation && !hasReport) {
                    // Status NO but no report created yet
                    canUpload = true;
                }
            }

            if (canUpload) {
                // Show image section
                $imageSection.show();

                // Show upload button for current and previous step
                if ($uploadArea.length === 0) {
                    // Create upload area if not exists
                    var itemId = $row.find('.audit-status-btn').first().data('item-id');
                    var uploadHtml = '<div class="image-upload-area">';
                    uploadHtml += '<input type="file" class="audit-image-input" data-item-id="' + itemId + '" ';
                    uploadHtml += 'accept="image/*" multiple style="display: none;" id="image-input-' + itemId + '">';
                    uploadHtml += '<label for="image-input-' + itemId + '" class="upload-image-btn">';
                    uploadHtml += '<i class="fa fa-camera"></i> Thêm ảnh';
                    uploadHtml += '</label>';
                    uploadHtml += '<div class="image-upload-hint">Upload ảnh minh chứng cho bước này</div>';
                    uploadHtml += '</div>';

                    $imageSection.prepend(uploadHtml);
                } else {
                    $uploadArea.show();
                }

                // Show delete buttons on images
                $row.find('.image-remove-btn').show();
            } else {
                // Check if has images - if yes, still show section but hide upload button
                var hasImages = $row.find('.image-preview-item').length > 0;
                if (hasImages) {
                    $imageSection.show();
                    $uploadArea.hide();
                } else {
                    $imageSection.hide();
                }

                // Hide delete buttons
                $row.find('.image-remove-btn').hide();
            }
        });
    }

    // Check if item can be completed based on sequential order
    function checkSequentialOrder($currentRow) {
        var canProceed = true;
        var $allRows = $('.audit-item-row');
        var currentIndex = $allRows.index($currentRow);

        // Check all previous rows
        for (var i = 0; i < currentIndex; i++) {
            var $prevRow = $allRows.eq(i);
            var hasStatus = $prevRow.find('.audit-status-btn.active').length > 0;
            var isNoStatus = $prevRow.hasClass('has-no-status');
            var hasUncompletedReport = $prevRow.find('.label-warning').length > 0;
            var needsReportCreation = $prevRow.find('.create-report-btn').length > 0;

            // If previous item is not completed
            if (!hasStatus) {
                canProceed = false;
                break;
            }

            // If previous item is NO and needs report creation or has uncompleted report
            if (isNoStatus && (needsReportCreation || hasUncompletedReport)) {
                canProceed = false;
                break;
            }
        }

        return canProceed;
    }

    function saveAuditItem(itemId, status) {
        $.ajax({
            url: '<?php echo admin_url("audit_management/saveAuditItem"); ?>',
            type: 'POST',
            data: {
                item_id: itemId,
                status: status,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1) {
                    // Success - update sequential checking will be called from click handler
                    // Update audit completion percentage
                    updateAuditCompletion();

                    // Check if all items are completed
                    checkAndCompleteAudit();
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    function checkAndCompleteAudit() {
        // Check if all items have status
        var incomplete = $('.audit-item-row').filter(function() {
            return !$(this).find('.audit-status-btn.active').length;
        }).length;

        if (incomplete === 0) {
            // All items completed - auto complete audit
            autoCompleteAudit();
        }
    }

    function autoCompleteAudit() {
        $.ajax({
            url: '<?php echo admin_url("audit_management/completeAudit"); ?>',
            type: 'POST',
            data: {
                audit_id: <?php echo $audit->id; ?>,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', 'Phếu audit đã hoàn thành!');

                    if (response.data && response.data.critical_issues > 0) {
                        alert_float('warning', 'Đã tạo ' + response.data.critical_issues + ' CAPA cho các lỗi Critical!');
                    }

                    // Close modal and reload table
                    setTimeout(function() {
                        $('#tnhModal').modal('hide');
                        if (typeof auditTable !== 'undefined') {
                            auditTable.ajax.reload();
                        }
                    }, 1500);
                }
            }
        });
    }

    function updateAuditCompletion() {
        $.ajax({
            url: '<?php echo admin_url("audit_management/updateCompletion"); ?>',
            type: 'POST',
            data: {
                audit_id: <?php echo $audit->id; ?>,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1 && response.data) {
                    // Update the table if it exists
                    if (typeof auditTable !== 'undefined') {
                        auditTable.ajax.reload(null, false); // Reload without resetting paging
                    }
                    console.log('Completion updated: ' + response.data.completion + '%');
                }
            }
        });
    }

    function completeAudit() {
        // Validate all items have status
        var incomplete = $('.audit-item-row').filter(function() {
            return !$(this).find('.audit-status-btn.active').length;
        }).length;

        if (incomplete > 0) {
            alert_float('warning', 'Vui lòng hoàn thành tất cả các tiêu chí trước khi kết thúc!');
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn hoàn thành phiếu audit này?\nSau khi hoàn thành, bạn sẽ không thể chỉnh sửa.')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url("audit_management/completeAudit"); ?>',
            type: 'POST',
            data: {
                audit_id: <?php echo $audit->id; ?>,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);

                    if (response.data && response.data.critical_issues > 0) {
                        alert_float('warning', 'Đã tạo ' + response.data.critical_issues + ' CAPA cho các lỗi Critical!');
                    }

                    // Close modal and reload table
                    $('#tnhModal').modal('hide');
                    if (typeof auditTable !== 'undefined') {
                        auditTable.ajax.reload();
                    }
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra!');
            }
        });
    };

    function openHistoryLogFromSession(auditId) {
        var url = '<?php echo admin_url("audit_management/viewHistoryLog/"); ?>' + auditId;

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                // Close current modal and show loading
                $('#tnhModal').modal('hide');

                setTimeout(function() {
                    var loadingHtml = '<div class="modal-dialog" style="max-width: 400px;">' +
                        '<div class="modal-content">' +
                        '<div class="modal-body text-center" style="padding: 60px 20px;">' +
                        '<i class="fa fa-spinner fa-spin fa-3x text-warning"></i>' +
                        '<p style="margin-top: 20px; font-size: 16px; color: #64748b;">Đang tải lịch sử...</p>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $('#tnhModal').html(loadingHtml);
                    $('#tnhModal').modal('show');
                }, 300);
            },
            success: function(response) {
                $('#tnhModal').html(response);
            },
            error: function() {
                $('#tnhModal').modal('hide');
                alert_float('danger', 'Không thể tải lịch sử thao tác!');
            }
        });
    }

    function loadHistoryLog() {
        $.ajax({
            url: '<?php echo admin_url("audit_management/loadHistoryLog"); ?>',
            type: 'GET',
            data: {
                audit_id: <?php echo $audit->id; ?>
            },
            success: function(response) {
                $('#historyLogContainer').html(response);
            }
        });
    }
</script>

<?php
// Helper function to render audit item
function render_audit_item($item, $audit_status)
{
    $disabled = ($audit_status != 'IN_PROGRESS') ? 'disabled' : '';
    $isNo = ($item->status == 'no');

    $html = '<div class="audit-item-row ' . ($isNo ? 'has-no-status' : '') . '">';
    $html .= '<div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">';

    // Left: Item text
    $html .= '<div style="flex: 1;">';
    if ($item->critical) {
        $html .= '<span class="critical-badge">Critical</span>';
    }
    $html .= '<span class="item-text ' . ($isNo ? 'has-issue' : '') . '">';
    $html .= htmlspecialchars($item->item_text);
    $html .= '</span>';
    $CI = &get_instance();
    $CI->db->where('audit_item_id', $item->id);
    $images = $CI->db->get('tblaudit_item_images')->result();
    $hasImages = !empty($images);


    // Check if item has status - if yes, only show images (view only)
    $hasStatus = !empty($item->status);

    // Check if this is the current step (first uncompleted item) or previous step
    $isCurrentStep = false;
    $isPreviousStep = false;

    if ($audit_status == 'IN_PROGRESS') {
        // Get all items for this audit to check position
        $CI = &get_instance();
        $CI->db->where('audit_id', $item->audit_id);
        $CI->db->order_by('id', 'ASC');
        $all_items = $CI->db->get('tbl_audit_checklist')->result();

        $currentStepId = null;
        $previousStepId = null;
        $previousStepHasStatus = false;

        foreach ($all_items as $idx => $check_item) {

            // Case A: chưa check
            if (empty($check_item->status)) {
                $currentStepId = $check_item->id;
                if ($idx > 0)
                    $previousStepId = $all_items[$idx - 1]->id;
                break;
            }

            // Case B: đã NO nhưng chưa tạo phiếu báo cáo => đây mới là "bước hiện tại"
            if ($check_item->status == 'no') {
                $CI->db->where('id_audit_item', $check_item->id);
                $has_report = $CI->db->get('tblproduction_report')->row();

                if (!$has_report) {
                    $currentStepId = $check_item->id;
                    if ($idx > 0)
                        $previousStepId = $all_items[$idx - 1]->id;
                    break;
                }

                // (tuỳ chọn) nếu có phiếu nhưng còn process chưa hoàn thành thì vẫn coi là current step
                $CI->db->where('tbl_process_production_report.staff_process', 0);
                $CI->db->where('tbl_process_production_report.production_report_id', $has_report->id);
                $uncompleted = $CI->db->get('tbl_process_production_report')->num_rows();

                if ($uncompleted > 0) {
                    $currentStepId = $check_item->id;
                    if ($idx > 0)
                        $previousStepId = $all_items[$idx - 1]->id;
                    break;
                }
            }
        }

        if ($currentStepId == $item->id) {
            $isCurrentStep = true;
        } else if ($previousStepId == $item->id) {
            $isPreviousStep = true;
        }
    }

    // Check if item can upload image:
    // 1. Is current or previous step AND no status yet
    // 2. OR is current/previous step AND status = NO but no production report created yet
    $canUploadImage = false;

    if ($isCurrentStep || $isPreviousStep) {
        if (!$hasStatus) {
            // No status yet - allow upload
            $canUploadImage = true;
        } else if ($item->status == 'no') {
            // Status is NO - check if production report exists
            $CI->db->where('id_audit_item', $item->id);
            $has_production_report = $CI->db->get('tblproduction_report')->row();

            if (!$has_production_report) {
                // NO status but no production report yet - allow upload
                $canUploadImage = true;
            }
        }
    }
    $imageSectionStyle = ($canUploadImage || $hasImages) ? '' : 'display:none;';
    // Image upload section
    $html .= '<div class="audit-image-section" style="' . $imageSectionStyle . '">';
    if ($canUploadImage) {
        // Allow upload for current step and previous step
        $html .= '<div class="image-upload-area">';
        $html .= '<input type="file" class="audit-image-input" data-item-id="' . $item->id . '" ';
        $html .= 'accept="image/*" multiple style="display: none;" id="image-input-' . $item->id . '">';
        $html .= '<label for="image-input-' . $item->id . '" class="upload-image-btn">';
        $html .= '<i class="fa fa-camera"></i> Thêm ảnh';
        $html .= '</label>';
        $html .= '<div class="image-upload-hint">Upload ảnh minh chứng cho bước này</div>';
        $html .= '</div>';
    } else if ($hasStatus && !$canUploadImage) {
        // Has status and cannot upload - show label
        $html .= '<div style="font-size: 12px; color: #666; margin-bottom: 8px;">';
        $html .= '<i class="fa fa-images"></i> Ảnh minh chứng:';
        $html .= '</div>';
    }

    // Image preview container (for both upload and view)
    $html .= '<div class="image-preview-container" data-item-id="' . $item->id . '">';
    $CI = &get_instance();

    // Load existing images
    $CI->db->where('audit_item_id', $item->id);
    $images = $CI->db->get('tblaudit_item_images')->result();

    foreach ($images as $image) {
        $html .= '<div class="image-preview-item">';
        $html .= '<img src="' . base_url('uploads/audit_items/' . $image->file_name) . '" alt="Audit Image">';

        // Show delete button if this is current step or previous step
        if ($canUploadImage) {
            $html .= '<button type="button" class="image-remove-btn" data-image-id="' . $image->id . '">';
            $html .= '<i class="fa fa-times"></i></button>';
        }

        $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    // Production report section (always rendered for IN_PROGRESS)
    // Check if production report exists for this item
    $CI->db->where('id_audit_item', $item->id);
    $production_report = $CI->db->get('tblproduction_report')->row();

    $displayStyle = $isNo ? '' : 'display: none;';

    if (!$production_report) {
        $html .= '<div class="production-report-section" style="' . $displayStyle . '">';
        $html .= '<br><a href="' . base_url('admin/production_report/detail?id_audit_item=' . $item->id . '&audit_id=' . $item->audit_id) . '" ';
        $html .= 'class="btn btn-info btn-icon mbot10 create-report-btn" target="_blank" data-item-id="' . $item->id . '">';
        $html .= 'Tạo phiếu báo cáo</a>';
        $html .= '</div>';
    } else {
        $html .= '<div class="production-report-section mtop10" style="' . $displayStyle . '">';
        $html .= '<a href="' . base_url('admin/production_report/modal/' . $production_report->id) . '" class="c_modal">';
        $html .= htmlspecialchars($production_report->reference_no) . '</a> ';

        // Check if report has uncompleted processes
        $CI->db->select('tbl_process_production_report.*');
        $CI->db->where('tbl_process_production_report.staff_process', 0);
        $CI->db->where('tbl_process_production_report.production_report_id', $production_report->id);
        $CI->db->from('tbl_process_production_report');
        $uncompleted_count = $CI->db->get()->num_rows();

        if ($uncompleted_count > 0) {
            $html .= '<span class="label label-warning">Chưa hoàn thành</span>';
        } else {
            $html .= '<span class="label label-success">Hoàn thành</span>';
        }
        $html .= '</div>';
    }

    $html .= '</div>';

    // Right: YES/NO buttons
    $html .= '<div style="display: flex; gap: 10px; flex-shrink: 0;">';

    $yesActive = ($item->status == 'yes') ? 'active' : '';
    $noActive = ($item->status == 'no') ? 'active' : '';

    $html .= '<button type="button" class="audit-status-btn btn-yes ' . $yesActive . '" ';
    $html .= 'data-item-id="' . $item->id . '" data-status="yes" ' . $disabled . '>';
    $html .= '<i class="fa fa-check"></i> YES</button>';

    $html .= '<button type="button" class="audit-status-btn btn-no ' . $noActive . '" ';
    $html .= 'data-item-id="' . $item->id . '" data-status="no" ' . $disabled . '>';
    $html .= '<i class="fa fa-times"></i> NO</button>';

    $html .= '</div>';

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

?>