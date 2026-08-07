<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .config-header {
        background: #fff;
        border-left: 4px solid #4a5568;
        margin-bottom: 20px;
    }

    .config-header h3 {
        color: #2d3748;
        margin: 0;
    }

    .section-list-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .section-list-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
        background: #f7fafc;
    }

    .section-list-header h4 {
        margin: 0;
        color: #2d3748;
        font-weight: 600;
    }

    #sections-table thead th {
        background: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }

    #sections-table tbody td {
        padding: 12px;
        vertical-align: middle;
    }

    #sections-table tbody tr {
        transition: all 0.2s;
    }

    #sections-table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    #sections-table tbody tr[onclick] {
        cursor: pointer;
    }
    
    #sections-table tbody tr[onclick]:hover {
        background-color: #e8f4f8;
    }

    .section-item {
        padding: 16px 25px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .section-item:last-child {
        border-bottom: none;
    }

    .section-item:hover {
        background: #f8fafc;
    }

    .section-info {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .section-icon.blue {
        background: #ebf4ff;
        color: #3182ce;
    }

    .section-icon.green {
        background: #f0fff4;
        color: #38a169;
    }

    .section-icon.purple {
        background: #faf5ff;
        color: #9333ea;
    }

    .section-details {
        flex: 1;
    }

    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px 0;
    }

    .section-meta {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .section-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 600;
    }

    .badge-id {
        background: #f1f5f9;
        color: #64748b;
        font-family: monospace;
    }

    .badge-condition {
        background: #dbeafe;
        color: #1e40af;
    }

    .section-count {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .btn-edit-section {
        padding: 8px 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-edit-section:hover {
        background: #4a5568;
        border-color: #4a5568;
        color: #fff;
    }

    .btn-add-section {
        width: 100%;
        padding: 16px;
        margin-top: 20px;
        background: #fff;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        color: #94a3b8;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-add-section:hover {
        background: #f8fafc;
        border-color: #4a5568;
        color: #4a5568;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="panel_s config-header">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <a href="<?php echo admin_url('audit_management'); ?>" class="btn btn-default btn-sm"
                                    style="margin-right: 8px;">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                                <h3 style="display: inline-block; margin-bottom: 5px;">
                                    <i class="fa fa-cogs"></i> Cấu hình Biểu mẫu Audit
                                </h3>
                                <p class="text-muted"
                                    style="margin: 0; display: inline-block; margin-left: 15px; font-size: 13px;">
                                    Quản lý các phần trong phiếu kiểm tra
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section List -->
                <div style=" margin: 0 auto;">
                    <div class="section-list-card">
                        <div style="padding: 20px 25px; background: #fff;">
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-6">
                                    <input type="text" id="search_sections" class="form-control"
                                        placeholder="Tìm kiếm phần kiểm tra...">
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="<?php echo admin_url('audit_management/downloadTemplate'); ?>" 
                                        class="btn btn-default hide" style="margin-right: 5px;">
                                        <i class="fa fa-download"></i> Download mẫu có data
                                    </a>
                                    <a href="<?php echo admin_url('audit_management/exportSampleData'); ?>" 
                                        class="btn btn-success hide" style="margin-right: 5px;">
                                        <i class="fa fa-file-excel-o"></i> Xuất data hiện tại
                                    </a>
                                    <button type="button" class="btn btn-warning" 
                                        style="margin-right: 5px;"
                                        onclick="$('#modal_import_excel').modal('show');">
                                        <i class="fa fa-upload"></i> Import Excel
                                    </button>
                                    <button type="button" class="btn-add-section"
                                        style="width: auto; margin: 0; display: inline-block;"
                                        onclick="openAddSectionModal()">
                                        <i class="fa fa-plus-circle"></i> Thêm phần mới
                                    </button>
                                </div>
                            </div>

                            <table class="table table-hover" id="sections-table" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;"></th>
                                        <th>Tiêu đề</th>
                                        <th style="width: 120px;">Mã ID</th>
                                        <th style="width: 130px;">Ban</th>
                                        <th style="width: 130px;">Phòng</th>
                                        <th style="width: 100px;">Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($template as $index => $section): ?>
                                        <tr style="cursor: pointer;" onclick="editSection(<?php echo $index; ?>)">
                                            <td>
                                                <div
                                                    class="section-icon <?php echo ($index % 3 == 0) ? 'blue' : (($index % 3 == 1) ? 'green' : 'purple'); ?>">
                                                    <?php 
                                                    $romanType = isset($section['romanType']) ? $section['romanType'] : (isset($section['id']) ? explode('.', $section['id'])[0] : '');
                                                    if ($romanType == 'I'): ?>
                                                        <i class="fa fa-check-square"></i>
                                                    <?php elseif ($romanType == 'II'): ?>
                                                        <i class="fa fa-users"></i>
                                                    <?php elseif ($romanType == 'III'): ?>
                                                        <i class="fa fa-building"></i>
                                                    <?php elseif ($romanType == 'IV'): ?>
                                                        <i class="fa fa-folder-open"></i>
                                                    <?php elseif ($romanType == 'V'): ?>
                                                        <i class="fa fa-file-text"></i>
                                                    <?php else: ?>
                                                        <i class="fa fa-cog"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <strong
                                                    style="color: #1e293b;"><?php echo htmlspecialchars($section['title']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="section-badge badge-id"><?php echo $section['id']; ?></span>
                                            </td>
                                            <td>
                                                <?php if (isset($section['room']) && !empty($section['room'])): ?>
                                                    <span class="label label-info" style="font-size: 10px;">
                                                        <?php 
                                                        // Find room name
                                                        $room_name = '';
                                                        foreach ($room as $r) {
                                                            if ($r['id'] == $section['room']) {
                                                                $room_name = $r['name'];
                                                                break;
                                                            }
                                                        }
                                                        echo $room_name;
                                                        ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="font-size: 11px; color: #cbd5e1;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($section['department']) && !empty($section['department'])): ?>
                                                    <span class="label label-primary" style="font-size: 10px;">
                                                        <?php 
                                                        // Find department name
                                                        $dept_name = '';
                                                        foreach ($departments as $d) {
                                                            if ($d['departmentid'] == $section['department']) {
                                                                $dept_name = $d['name'];
                                                                break;
                                                            }
                                                        }
                                                        echo $dept_name;
                                                        ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="font-size: 11px; color: #cbd5e1;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span style="font-size: 12px; color: #64748b;">
                                                    <?php if (isset($section['subsections'])): ?>
                                                        <i class="fa fa-folder-open"></i>
                                                        <?php echo count($section['subsections']); ?> nhóm
                                                    <?php else: ?>
                                                        <i class="fa fa-list-ul"></i>
                                                        <?php echo isset($section['items']) ? count($section['items']) : 0; ?>
                                                        tiêu chí
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Excel Modal -->
<div id="modal_import_excel" class="modal fade" role="dialog">
    <form action="<?php echo admin_url('audit_management/importTemplate'); ?>" id="import_form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate="novalidate">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-upload"></i> Import Audit Template từ Excel
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Hướng dẫn:</strong>
                        <ol style="margin: 10px 0 0 0; padding-left: 20px;">
                            <li>Download file mẫu bên dưới (có sẵn danh sách Ban và Phòng)</li>
                            <li>Điền thông tin: Loại (I-V), Tên Ban, Tên Phòng, Nội dung tiêu chí</li>
                            <li>Nhập đúng tên Ban/Phòng từ danh sách tham khảo trong file</li>
                            <li>Upload file đã điền để import</li>
                        </ol>
                    </div>
                    <a target="_blank" href="<?php echo admin_url('audit_management/downloadTemplate'); ?>" class="btn btn-info btn-block" style="margin-bottom: 15px;">
                        <i class="fa fa-download"></i> Download file mẫu Excel
                    </a>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <span class="btn btn-default btn-file col-md-12" style="margin-bottom: 15px;">
                            <span><i class="fa fa-file-excel-o"></i> Chọn file Excel</span>
                            <input type="file" name="file" class="btn" id="file_import" required accept=".xlsx,.xls">
                        </span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" onclick="importExcelFile()">
                        <i class="fa fa-upload"></i> Import
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%); color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-plus-circle"></i> Thêm Phần Kiểm Tra Mới
                </h4>
            </div>
            <form id="addSectionForm">
                <div class="modal-body" style="padding: 25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Loại phần kiểm tra (Số La Mã) <span class="text-danger">*</span></label>
                                <select class="form-control" name="roman_type" id="section_roman" required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="I">I - CHECKLIST CHUNG (BẮT BUỘC)</option>
                                    <option value="II">II - CHECKLIST THEO BAN</option>
                                    <option value="III">III - CHECKLIST THEO PHÒNG</option>
                                    <option value="IV">IV - KIỂM SOÁT HỒ SƠ CHUNG</option>
                                    <option value="V">V - KIỂM SOÁT HỒ SƠ CHUNG</option>
                                </select>
                                <small class="text-muted">Chọn loại checklist để tổ chức theo nhóm</small>
                            </div>
                        </div>
                    </div>

                    <!-- Department Selection - Hidden by default -->
                    <div id="room_container" style="display: none;">
                        <div class="form-group">
                            <label>Áp dụng cho ban <span class="text-danger dept-required"
                                    style="display: none;">*</span></label>
                            <select class="form-control selectpicker" name="room" id="section_room"
                                data-none-selected-text="Chọn ban">
                                <option value="">-- Chọn ban --</option>
                                <?php foreach ($room as $key => $value) { ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                            <small class="text-muted dept-hint"></small>
                        </div>
                    </div>
                    <div id="department_container" style="display: none;">
                        <div class="form-group">
                            <label>Áp dụng cho phòng <span class="text-danger dept-required"
                                    style="display: none;">*</span></label>
                            <select class="form-control selectpicker" name="department" id="section_department"
                                data-none-selected-text="Chọn phòng">
                                <option value="">-- Chọn phòng --</option>
                                <?php foreach ($departments as $key => $value) { ?>
                                    <option value="<?= $value['departmentid'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            </select>
                            <small class="text-muted dept-hint"></small>
                        </div>
                    </div>                                       
                    <!-- Items (simple list) -->
                    <div id="items_container" style="display: block;">
                        <label>Các tiêu chí kiểm tra</label>
                        <div id="items_list">
                            <div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="text" class="form-control item-text" placeholder="Tiêu chí kiểm tra 1">
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-default" onclick="addItemRow()">
                            <i class="fa fa-plus"></i> Thêm tiêu chí
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lưu phần mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    $(function () {
        // Initialize DataTable
        var sectionsTable = $('#sections-table').DataTable({
            'order': [[1, 'asc']],
            'pageLength': 25,
            'language': {
                search: '',
                lengthMenu: 'Hiển thị _MENU_ mục',
                info: 'Hiển thị _START_ đến _END_ của _TOTAL_ phần',
                infoEmpty: 'Không có dữ liệu',
                infoFiltered: '(lọc từ _MAX_ tổng số phần)',
                zeroRecords: '<div style="text-align: center; padding: 40px; color: #94a3b8;">' +
                    '<i class="fa fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>' +
                    '<h4 style="font-weight: 600; color: #64748b;">Chưa có phần kiểm tra nào</h4>' +
                    '<p style="font-size: 14px; color: #94a3b8;">Nhấn "Thêm phần mới" để bắt đầu</p>' +
                    '</div>',
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            }
        });

        // Search filter
        $('#search_sections').on('keyup', function () {
            sectionsTable.search($(this).val()).draw();
        });
    });

    // Open Add Section Modal
    function openAddSectionModal() {
        $('#addSectionModal').modal('show');
        $('#addSectionForm')[0].reset();
        // Reset to default state
        $('#items_container').show();
        $('#department_container').hide();
        $('#room_container').hide();
        $('#items_list').html('<div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;"><input type="text" class="form-control item-text" placeholder="Tiêu chí kiểm tra 1"><button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button></div>');
        $('#addSectionForm').data('editing-index', null);
        $('.modal-title').html('<i class="fa fa-plus-circle"></i> Thêm Phần Kiểm Tra Mới');
        // Initialize selectpicker
        setTimeout(function () {
            $('#section_department').selectpicker('refresh');
            $('#section_room').selectpicker('refresh');
        }, 100);
    }
    
    // Edit existing section
    function editSection(index) {
        $.ajax({
            url: '<?php echo admin_url("audit_management/getSectionData"); ?>',
            type: 'POST',
            data: {
                index: index,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1) {
                    var section = response.section;
                    
                    // Open modal
                    $('#addSectionModal').modal('show');
                    $('#addSectionForm').data('editing-index', index);
                    $('.modal-title').html('<i class="fa fa-edit"></i> Chỉnh Sửa Phần Kiểm Tra');
                    
                    // Set roman type
                    $('#section_roman').val(section.romanType || section.id.split('.')[0]).trigger('change');
                    
                    // Set department/room
                    setTimeout(function() {
                        if (section.room) {
                            $('#section_room').val(section.room).selectpicker('refresh');
                        }
                        if (section.department) {
                            $('#section_department').val(section.department).selectpicker('refresh');
                        }
                    }, 200);
                    
                    // Load items
                    $('#items_list').html('');
                    if (section.items && section.items.length > 0) {
                        section.items.forEach(function(item) {
                            var html = '<div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">' +
                                '<input type="text" class="form-control item-text" value="' + (item.text || '') + '" placeholder="Tiêu chí kiểm tra">' +
                                '<button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button>' +
                                '</div>';
                            $('#items_list').append(html);
                        });
                    } else {
                        $('#items_list').html('<div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;"><input type="text" class="form-control item-text" placeholder="Tiêu chí kiểm tra 1"><button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button></div>');
                    }
                }
            }
        });
    }

    // Toggle department field based on Roman type
    $('#section_roman').on('change', function () {
        var romanType = $(this).val();

        if (romanType === 'I' || romanType === 'IV' || romanType === 'V') {
            // CHECKLIST CHUNG - Hide both selections
            $('#department_container').hide();
            $('#room_container').hide();
            $('#section_department').val('').selectpicker('refresh');
            $('#section_room').val('').selectpicker('refresh');
            $('.dept-required').hide();
            $('.dept-hint').text('');
            
            // Load existing data if available
            loadExistingSection(romanType, null, null);
        } else if (romanType === 'II') {
            // CHECKLIST THEO BAN - Show room selection only
            $('#department_container').hide();
            $('#room_container').show();
            $('#section_department').val('').selectpicker('refresh');
            $('.dept-required').show();
            $('.dept-hint').text('Chọn ban áp dụng checklist này');
            setTimeout(function () {
                $('#section_room').selectpicker('refresh');
            }, 100);
        }
        else if (romanType === 'III') {
            // CHECKLIST THEO PHÒNG - Show department selection only
            $('#room_container').hide();
            $('#department_container').show();
            $('#section_room').val('').selectpicker('refresh');
            $('.dept-required').show();
            $('.dept-hint').text('Chọn phòng cụ thể áp dụng checklist này');
            setTimeout(function () {
                $('#section_department').selectpicker('refresh');
            }, 100);
        }
    });
    
    // Load existing section when room is selected
    $('#section_room').on('change', function() {
        var romanType = $('#section_roman').val();
        var room = $(this).val();
        if (romanType && room) {
            loadExistingSection(romanType, room, null);
        }
    });
    
    // Load existing section when department is selected
    $('#section_department').on('change', function() {
        var romanType = $('#section_roman').val();
        var department = $(this).val();
        if (romanType && department) {
            loadExistingSection(romanType, null, department);
        }
    });
    
    // Function to load existing section data
    function loadExistingSection(romanType, room, department) {
        // Don't load if we're editing
        if ($('#addSectionForm').data('editing-index') !== null) {
            return;
        }
        
        $.ajax({
            url: '<?php echo admin_url("audit_management/checkExistingSection"); ?>',
            type: 'POST',
            data: {
                roman_type: romanType,
                room: room,
                department: department,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 1 && response.section) {
                    // Found existing section - load items
                    $('#items_list').html('');
                    if (response.section.items && response.section.items.length > 0) {
                        response.section.items.forEach(function(item) {
                            var html = '<div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">' +
                                '<input type="text" class="form-control item-text" value="' + (item.text || '') + '" placeholder="Tiêu chí kiểm tra">' +
                                '<button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button>' +
                                '</div>';
                            $('#items_list').append(html);
                        });
                        // Store the index for updating
                        $('#addSectionForm').data('editing-index', response.index);
                        $('.modal-title').html('<i class="fa fa-edit"></i> Chỉnh Sửa Phần Kiểm Tra');
                    }
                } else {
                    // No existing section - keep empty form
                    if ($('#items_list .item-row').length === 0) {
                        $('#items_list').html('<div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;"><input type="text" class="form-control item-text" placeholder="Tiêu chí kiểm tra 1"><button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button></div>');
                    }
                    $('#addSectionForm').data('editing-index', null);
                    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Thêm Phần Kiểm Tra Mới');
                }
            }
        });
    }

    // Toggle between items and subsections
    $('#section_type').on('change', function () {
        if ($(this).val() === 'items') {
            $('#items_container').show();
            $('#subsections_container').hide();
        } else {
            $('#items_container').hide();
            $('#subsections_container').show();
        }
    });

    // Add item row
    function addItemRow() {
        var html = '<div class="item-row" style="display: flex; gap: 10px; margin-bottom: 10px;">' +
            '<input type="text" class="form-control item-text" placeholder="Tiêu chí kiểm tra">' +
            '<button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button>' +
            '</div>';
        $('#items_list').append(html);
    }

    // Add subsection card
    function addSubsectionCard() {
        var html = '<div class="subsection-card" style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 15px;">' +
            '<div class="form-group"><label>Tên nhóm</label><input type="text" class="form-control subsection-title" placeholder="VD: Trang thiết bị"></div>' +
            '<div class="subsection-items">' +
            '<div style="display: flex; gap: 10px; margin-bottom: 8px;"><input type="text" class="form-control subsection-item-text" placeholder="Tiêu chí 1">' +
            '<button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button></div>' +
            '</div>' +
            '<button type="button" class="btn btn-sm btn-default btn-xs" onclick="addSubsectionItemRow(this)"><i class="fa fa-plus"></i> Thêm tiêu chí</button> ' +
            '<button type="button" class="btn btn-sm btn-danger btn-xs pull-right" onclick="$(this).closest(\'.subsection-card\').remove()"><i class="fa fa-trash"></i> Xóa nhóm</button>' +
            '</div>';
        $('#subsections_list').append(html);
    }

    // Add subsection item row
    function addSubsectionItemRow(btn) {
        var container = $(btn).siblings('.subsection-items');
        var html = '<div style="display: flex; gap: 10px; margin-bottom: 8px;">' +
            '<input type="text" class="form-control subsection-item-text" placeholder="Tiêu chí">' +
            '<button type="button" class="btn btn-sm btn-danger" onclick="$(this).parent().remove()"><i class="fa fa-trash"></i></button>' +
            '</div>';
        container.append(html);
    }



    // Submit form
    $('#addSectionForm').on('submit', function (e) {
        e.preventDefault();

        var romanType = $('#section_roman').val();
        var room = $('#section_room').val();
        var department = $('#section_department').val();

        // Validate
        if (!romanType) {
            alert_float('warning', 'Vui lòng chọn loại phần kiểm tra!');
            return;
        }

        // Validate room for type II
        if (romanType === 'II' && !room) {
            alert_float('warning', 'Vui lòng chọn ban cho loại checklist này!');
            return;
        }
        
        // Validate department for type III
        if (romanType === 'III' && !department) {
            alert_float('warning', 'Vui lòng chọn phòng cho loại checklist này!');
            return;
        }

        // Auto-generate title and ID from roman type
        var titleMap = {
            'I': 'CHECKLIST CHUNG (BẮT BUỘC)',
            'II': 'CHECKLIST THEO BAN',
            'III': 'CHECKLIST THEO PHÒNG',
            'IV': 'KIỂM SOÁT HỒ SƠ CHUNG',
            'V': 'KIỂM SOÁT HỒ SƠ CHUNG'
        };

        var formattedTitle = romanType + '. ' + titleMap[romanType];
        var formattedId = romanType;
        var displayCondition = 'always';

        // Build section data
        var sectionData = {
            title: formattedTitle,
            id: formattedId,
            displayCondition: displayCondition,
            room: room || null,
            department: department || null,
            romanType: romanType
        };

        // Collect items
        var items = [];
        $('.item-text').each(function () {
            var text = $(this).val().trim();
            if (text) {
                items.push({ text: text });
            }
        });

        if (items.length === 0) {
            alert_float('warning', 'Vui lòng thêm ít nhất một tiêu chí kiểm tra!');
            return;
        }

        sectionData.items = items;

        // Check if editing or adding
        var editingIndex = $('#addSectionForm').data('editing-index');
        var url = editingIndex !== null ? '<?php echo admin_url("audit_management/updateSection"); ?>' : '<?php echo admin_url("audit_management/addSection"); ?>';
        var postData = {
            section_data: JSON.stringify(sectionData),
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
        };
        
        if (editingIndex !== null) {
            postData.index = editingIndex;
        }

        // Send to server
        $.ajax({
            url: url,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function (response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    $('#addSectionModal').modal('hide');
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function () {
                alert_float('danger', 'Có lỗi xảy ra khi kết nối server!');
            }
        });
    });
    
    // Import Excel file
    function importExcelFile() {
        var fileInput = document.getElementById('file_import');
        var filePath = fileInput.value;
        
        if (filePath == "") {
            alert_float('warning', 'Vui lòng chọn file Excel!');
            return false;
        }
        
        var allowedExtensions = /(\.xlsx|\.xls)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert_float('warning', 'Vui lòng upload file có định dạng .XLSX hoặc .XLS!');
            fileInput.value = '';
            return false;
        }
        
        var url = '<?php echo admin_url("audit_management/importTemplate"); ?>';
        var file_data = $('#import_form input#file_import').prop('files');
        var form_data = new FormData();
        
        $.each(file_data, function (infile, valFile) {
            form_data.append('file', valFile);
        });
        
        form_data.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
        
        $.ajax({
            url: url,
            type: 'POST',
            contentType: false,
            cache: false,
            processData: false,
            data: form_data,
            dataType: 'json',
            success: function (response) {
                $('#modal_import_excel').modal('hide');
                alert_float(response.alert_type, response.message);
                if (response.success) {
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            },
            error: function () {
                alert_float('danger', 'Có lỗi xảy ra khi upload file!');
            }
        });
        
        return false;
    }
</script>