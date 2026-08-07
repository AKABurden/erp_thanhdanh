<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .audit-header {
        background: #fff;
        border-left: 4px solid #4a5568;
        margin-bottom: 20px;
    }

    .audit-header h3 {
        color: #2d3748;
        margin: 0;
    }

    .stat-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.blue {
        background: #ebf4ff;
        color: #3182ce;
    }

    .stat-icon.red {
        background: #fff5f5;
        color: #e53e3e;
    }

    .stat-icon.green {
        background: #f0fff4;
        color: #38a169;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        color: #718096;
        font-size: 13px;
        margin-top: 4px;
    }

    .table-wrapper {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .table-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
        background: #f7fafc;
    }

    .table-header h4 {
        margin: 0;
        color: #2d3748;
        font-weight: 600;
    }

    .filter-row {
        padding: 20px 25px;
        background: #fff;
    }

    #audit-table thead th {
        background: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }

    #audit-table tbody td {
        padding: 12px;
        vertical-align: middle;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.in-progress {
        background: #bee3f8;
        color: #2c5282;
    }

    .status-badge.completed {
        background: #c6f6d5;
        color: #276749;
    }

    .status-badge.cancelled {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-clean {
        background: #4a5568;
        color: #fff;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-clean:hover {
        background: #2d3748;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header Section -->
                <div class="panel_s audit-header">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 style="margin-bottom: 5px;">
                                    <i class="fa fa-clipboard"></i> Phiếu yêu cầu Audit tháng
                                </h3>
                                <p class="text-muted" style="margin: 0;">Hệ thống FOSO Audit v2.0</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('audit_management/config'); ?>" class="btn btn-default"
                                    style="margin-right: 8px;">
                                    <i class="fa fa-cog"></i> Cấu hình
                                </a>
                                <a href="#" onclick="createNewAudit(); return false;" class="btn-clean">
                                    <i class="fa fa-plus"></i> Tạo Phiếu Audit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="stat-icon blue">
                                    <i class="fa fa-file-text"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="stat-number"><?php echo $stats['total_this_month']; ?></div>
                                    <div class="stat-label">Phiếu tháng này</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="stat-icon red">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="stat-number"><?php echo $stats['critical_issues']; ?></div>
                                    <div class="stat-label">Lỗi Critical</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="stat-icon green">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div class="stat-number"><?php echo $stats['completion_rate']; ?>%</div>
                                    <div class="stat-label">Tỷ lệ hoàn thành</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit List Table -->
                <div class="table-wrapper">
                    <div class="table-header">
                        <h4><i class="fa fa-list"></i> Danh sách phiếu Audit</h4>
                    </div>

                    <!-- Filters -->
                    <div class="filter-row">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" id="search_filter" class="form-control" placeholder="Tìm kiếm...">
                            </div>
                            <div class="col-md-3">
                                <select id="status_filter" class="form-control selectpicker">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="IN_PROGRESS">Đang thực hiện</option>
                                    <option value="COMPLETED">Hoàn thành</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="department_filter" class="form-control selectpicker">
                                    <option value="">-- Tất cả ban --</option>
                                    <?php foreach ($room as $key => $value) { ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn-clean" onclick="auditTable.ajax.reload();">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="padding: 0 25px 25px;">
                        <table class="table table-hover" id="audit-table" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã Phiếu</th>
                                    <th>Phòng Ban</th>
                                    <th>Ngày Audit</th>
                                    <th>Trưởng đoàn</th>
                                    <th>Kết quả</th>
                                    <th>Có sự cố</th>
                                    <th>Báo cáo</th>
                                    <th>Trạng thái</th>
                                    <th class="text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createAuditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-plus-circle"></i> Khởi tạo Phiếu Audit
                </h4>
            </div>
            <form id="createAuditForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="audit_date">Ngày thực hiện</label>
                        <input disabled type="date" name="audit_date" id="audit_date" class="form-control"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="dept_id">Đơn vị được Audit <span class="text-danger">*</span></label>
                        <select name="dept_id" id="dept_id" class="form-control selectpicker" required>
                            <option value="">-- Chọn Ban --</option>
                            <?php foreach ($room as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group" style="width: 100%;">
                        <label for="auditor">Trưởng đoàn Audit <span class="text-danger">*</span></label>
                        <?php echo render_select('auditor_id', (!empty($staff_list_all) ? $staff_list_all : []), ['staffid', ['firstname', 'lastname']]) ?>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary">Bắt đầu Audit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    var auditTable;
    var fnserverparams = {
        search_filter: "#search_filter",
        status_filter: "#status_filter",
        department_filter: "#department_filter"
    };

    $(function() {
        // Initialize DataTable using tnhInitDataTable
        auditTable = tnhInitDataTable('#audit-table', '', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?php echo admin_url("audit_management/getAuditList"); ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData || json.data || [];
                }
            },
            "columnDefs": [{
                    targets: 0,
                    name: 'id',
                    visible: false,
                    data: 0
                },
                {
                    targets: 1,
                    name: 'audit_code',
                    data: 1,
                    render: function(data, type, row) {
                        return '<a href="javascript:void(0);" onclick="openAuditSession(' + row[0] + ')" style="font-weight: 600; color: #2563eb;">' + (data || '-') + '</a>';
                    }
                },
                {
                    targets: 2,
                    name: 'department',
                    data: 2
                },
                {
                    targets: 3,
                    name: 'audit_date',
                    data: 3,
                    render: function(data) {
                        return data ? moment(data).format('DD/MM/YYYY') : '';
                    }
                },
                {
                    targets: 4,
                    name: 'team_leader',
                    data: 4
                },
                {
                    targets: 5,
                    name: 'result_percentage',
                    data: 5,
                    render: function(data) {
                        if (!data) return '<span style="color: #a0aec0;">-</span>';
                        var badge = '';
                        if (data >= 90) {
                            badge = '<span style="background: #c6f6d5; color: #276749; padding: 5px 12px; border-radius: 12px; font-weight: 600; font-size: 13px;">' + data + '%</span>';
                        } else if (data >= 70) {
                            badge = '<span style="background: #fed7d7; color: #9b2c2c; padding: 5px 12px; border-radius: 12px; font-weight: 600; font-size: 13px;">' + data + '%</span>';
                        } else {
                            badge = '<span style="background: #feebc8; color: #7c2d12; padding: 5px 12px; border-radius: 12px; font-weight: 600; font-size: 13px;">' + data + '%</span>';
                        }
                        return badge;
                    }
                },
                {
                    targets: 6,
                    name: 'has_issues',
                    data: 6,
                    render: function(data) {
                        if (data && data > 0) {
                            return '<span style="background: #fed7d7; color: #9b2c2c; padding: 5px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">' +
                                '<i class="fa fa-exclamation-triangle"></i> ' + data + ' lỗi</span>';
                        }
                        return '<span style="color: #a0aec0;">Không</span>';
                    }
                },
                {
                    targets: 7,
                    name: 'reports',
                    data: 7,
                    render: function(data, type, row) {
                        if (!data || data.length === 0) {
                            return '<span style="color: #a0aec0;">-</span>';
                        }
                        var html = '<div style="max-width: 200px;">';
                        data.forEach(function(report) {
                            html += '<a href="' + admin_url + 'production_report/modal/' + report.id + '" class="c_modal" style="display: inline-block; margin: 2px;">' +
                                '<span style="background: #ebf4ff; color: #2563eb; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">' +
                                report.reference_no + '</span></a>';
                        });
                        html += '</div>';
                        return html;
                    }
                },
                {
                    targets: 8,
                    name: 'status',
                    data: 8,
                    render: function(data) {
                        var badges = {
                            'IN_PROGRESS': '<span class="status-badge in-progress">Đang thực hiện</span>',
                            'COMPLETED': '<span class="status-badge completed">Hoàn thành</span>',
                            'CANCELLED': '<span class="status-badge cancelled">Hủy</span>'
                        };
                        return badges[data] || data;
                    }
                },
                {
                    targets: 9,
                    name: 'actions',
                    data: 0,
                    className: 'text-right',
                    orderable: false,
                    render: function(data, type, row) {
                        var actions = '<div class="btn-group">';
                        actions += '<a href="javascript:void(0);" onclick="openAuditSession(' + data + ')" class="btn btn-sm btn-default" title="Xem chi tiết"><i class="fa fa-eye"></i></a>';

                        // History log button
                        // actions += '<a href="javascript:void(0);" onclick="openHistoryLog(' + data + ')" class="btn btn-sm btn-warning" title="Lịch sử thao tác"><i class="fa fa-history"></i></a>';

                        // Only show PDF button for 100% completed audits
                        if (row[8] == 'COMPLETED' && row[5] == 100) {
                            actions += '<a href="<?php echo admin_url("audit_management/exportPDF/"); ?>' + data + '" target="_blank" class="btn btn-sm btn-info" title="In PDF"><i class="fa fa-file-pdf-o"></i></a>';
                        }

                        // Delete button - always visible, validated on backend
                        actions += '<button type="button" class="btn btn-sm btn-danger" onclick="deleteAudit(' + data + ')" title="Xóa"><i class="fa fa-trash"></i></button>';

                        actions += '</div>';
                        return actions;
                    }
                }
            ],
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-2x"></i>',
                search: '',
                lengthMenu: 'Hiển thị _MENU_ mục',
                info: 'Hiển thị _START_ đến _END_ của _TOTAL_ phiếu',
                infoEmpty: 'Không có dữ liệu',
                infoFiltered: '(lọc từ _MAX_ tổng số phiếu)',
                zeroRecords: '<div style="text-align: center; padding: 60px 20px; color: #94a3b8;">' +
                    '<i class="fa fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;"></i>' +
                    '<h3 style="font-size: 18px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Chưa có phiếu Audit nào</h3>' +
                    '<p style="font-size: 14px; color: #94a3b8;">Nhấn "Tạo Phiếu Audit" để bắt đầu</p>' +
                    '</div>',
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            }
        });

        // Filter handlers - update on change
        $('#search_filter').on('keyup', function() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                auditTable.ajax.reload();
            }, 500);
        });

        $('#status_filter, #department_filter').on('change', function() {
            auditTable.ajax.reload();
        });

        // Create form submit
        $('#createAuditForm').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: '<?php echo admin_url("audit_management/create"); ?>',
                type: 'POST',
                data: formData + '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>',
                dataType: 'json',
                success: function(response) {
                    if (response.result == 1) {
                        alert_float('success', response.message);
                        $('#createAuditModal').modal('hide');
                        // Open the newly created audit session in modal
                        if (response.audit_id) {
                            setTimeout(function() {
                                openAuditSession(response.audit_id);
                            }, 500);
                        }
                        // Reload table to show new audit
                        auditTable.ajax.reload();
                    } else {
                        alert_float('danger', response.message);
                    }
                },
                error: function() {
                    alert_float('danger', 'Có lỗi xảy ra!');
                }
            });
        });
    });

    function createNewAudit() {
        $('#createAuditForm')[0].reset();
        $('.selectpicker').selectpicker('refresh');
        $('#createAuditModal').modal('show');
    }

    function deleteAudit(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa phiếu audit này?')) {
            return;
        }

        $.ajax({
            url: '<?php echo admin_url("audit_management/delete/"); ?>' + id,
            type: 'POST',
            dataType: 'json',
            data: {
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    auditTable.ajax.reload();
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Có lỗi xảy ra!';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch (e) {}
                alert_float('danger', errorMsg);
            }
        });
    }

    function openAuditSession(auditId) {
        var url = '<?php echo admin_url("audit_management/session/"); ?>' + auditId;

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                // Show loading in modal
                var loadingHtml = '<div class="modal-dialog" style="max-width: 400px;">' +
                    '<div class="modal-content">' +
                    '<div class="modal-body text-center" style="padding: 60px 20px;">' +
                    '<i class="fa fa-spinner fa-spin fa-3x text-primary"></i>' +
                    '<p style="margin-top: 20px; font-size: 16px; color: #64748b;">Đang tải phiếu audit...</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                $('#tnhModal').html(loadingHtml);
                $('#tnhModal').modal('show');
            },
            success: function(response) {
                $('#tnhModal').html(response);
            },
            error: function() {
                $('#tnhModal').modal('hide');
                alert_float('danger', 'Không thể tải phiếu audit!');
            }
        });
    }

    function openHistoryLog(auditId) {
        var url = '<?php echo admin_url("audit_management/viewHistoryLog/"); ?>' + auditId;

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                // Show loading in modal
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

    // Tự động mở phiếu Audit nếu URL có ?open_audit=ID (từ Dashboard BOD)
    $(function() {
        var urlParams = new URLSearchParams(window.location.search);
        var openAuditId = urlParams.get('open_audit');
        if (openAuditId) {
            // Đợi DataTable load xong rồi mới mở modal
            setTimeout(function() {
                openAuditSession(parseInt(openAuditId));
            }, 800);
        }
    });
</script>