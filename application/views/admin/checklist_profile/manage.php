<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .checklist-wrapper {
        background: #f8f9fa;
        min-height: calc(100vh - 100px);
        padding: 20px;
    }

    .checklist-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .checklist-header {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .checklist-title-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .checklist-icon-box {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .checklist-icon-box i {
        color: white;
        font-size: 20px;
    }

    .checklist-title-text h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .checklist-title-text p {
        font-size: 13px;
        color: #64748b;
        margin: 4px 0 0 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 900;
        color: #1e293b;
    }

    /* Status Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid;
        white-space: nowrap;
    }

    .status-s6 {
        background: #fffbeb;
        color: #b45309;
        border-color: #fcd34d;
    }

    .status-s7 {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .status-s8 {
        background: #f5f3ff;
        color: #6b21a8;
        border-color: #c4b5fd;
    }

    .status-s9 {
        background: #ecfdf5;
        color: #047857;
        border-color: #86efac;
    }

    /* Action Buttons */
    .btn-action {
        padding: 8px 12px;
        border-radius: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 13px;
        transition: all 0.2s;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #2563eb;
        transform: translateY(-1px);
    }

    .btn-modern {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: 1px solid #2563eb;
    }

    .btn-primary-modern:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
    }

    /* Filters */
    .checklist-filters {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .filter-group {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .filter-group i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        z-index: 1;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.2s;
        background: white;
    }

    /* Table */
    #table-checklist thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    #table-checklist tbody tr {
        transition: all 0.2s;
        cursor: pointer;
    }

    #table-checklist tbody tr:hover {
        background-color: #eff6ff;
    }

    #table-checklist tbody td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .candidate-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .candidate-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
    }

    .checklist-code {
        font-family: 'Courier New', monospace;
        font-size: 10px;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid #bfdbfe;
    }

    /* Modal cho danh sách Offer chưa có checklist */
    .offer-list-modal .modal-dialog {
        max-width: 800px;
    }

    .offer-item {
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .offer-item:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
    }

    .offer-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        color: #475569;
    }

    .offer-item:hover .offer-avatar {
        background: #3b82f6;
        color: white;
    }
</style>

<div id="wrapper" class="checklist-wrapper">
    <div class="checklist-card">
        <div class="checklist-header">
            <div class="checklist-title-section">
                <div class="checklist-icon-box">
                    <i class="fa fa-shield"></i>
                </div>
                <div class="checklist-title-text">
                    <h2>Quản Lý Tiếp Nhận (Onboarding)</h2>
                    <p>Checklist đối chiếu hồ sơ (S6 → S7 → S8 → S9)</p>
                </div>
            </div>
            <div>
                <button onclick="openCreateModal()" class="btn-modern btn-primary-modern">
                    <i class="fa fa-plus"></i>
                    Tạo Checklist Mới
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div style="padding: 15px; background: #f8fafc;">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Trạng thái S6</div>
                    <div class="stat-value" id="stat-s6">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Trạng thái S7</div>
                    <div class="stat-value" id="stat-s7">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Trạng thái S8</div>
                    <div class="stat-value" id="stat-s8">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Trạng thái S9</div>
                    <div class="stat-value" id="stat-s9">0</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="checklist-filters">
            <div class="filter-group" style="flex: 2;">
                <i class="fa fa-search"></i>
                <input type="text" id="search-filter" placeholder="Tìm kiếm ứng viên, mã checklist..." />
            </div>
            <div class="filter-group">
                <i class="fa fa-filter"></i>
                <select id="status-filter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="S6">S6: Đang đối chiếu</option>
                    <option value="S7">S7: Đã check</option>
                    <option value="S8">S8: Thử việc</option>
                    <option value="S9">S9: Chính thức</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="col-md-12">
            <div >
                <?php echo $this->load->view('admin/alert') ?>
                <table id="table-checklist" class="table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã Checklist</th>
                            <th>Ứng viên</th>
                            <th>Vị trí</th>
                            <th>Ngày Offer</th>
                            <th>Mã NV</th>
                            <th>Trạng thái</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input type="hidden" name="search_filter" id="search_filter_hidden" value="">
<input type="hidden" name="status_filter" id="status_filter_hidden" value="">

<?php init_tail(); ?>

<script>
    var oTable = null;
    var fnserverparams = {
        search_filter: "#search_filter_hidden",
        status_filter: "#status_filter_hidden"
    };

    function getStatusBadge(status) {
        var badges = {
            'S6': '<span class="status-badge status-s6"><i class="fa fa-clock-o"></i> S6: Đang Đối Chiếu</span>',
            'S7': '<span class="status-badge status-s7"><i class="fa fa-check-circle"></i> S7: Đã Check</span>',
            'S8': '<span class="status-badge status-s8"><i class="fa fa-user-plus"></i> S8: Thử Việc</span>',
            'S9': '<span class="status-badge status-s9"><i class="fa fa-trophy"></i> S9: Chính Thức</span>'
        };
        return badges[status] || badges['S6'];
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function openCreateModal() {
        // Load danh sách offer chưa có checklist
        $.ajax({
            url: admin_url + 'checklist_profile/getApprovedOffers',
            type: 'GET',
            dataType: 'JSON',
            success: function(response) {
                showOfferListModal(response.items || []);
            },
            error: function() {
                alert_float('danger', 'Có lỗi khi tải danh sách Offer');
            }
        });
    }

    function showOfferListModal(offers) {
        var html = '<div class="modal fade offer-list-modal" tabindex="-1">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header bg-primary text-white">' +
            '<h4 class="modal-title"><i class="fa fa-file-text-o"></i> Chọn Offer để tạo Checklist</h4>' +
            '<button type="button" class="close text-white" data-dismiss="modal">&times;</button>' +
            '</div>' +
            '<div class="modal-body" style="max-height: 500px; overflow-y: auto;">';

        if (offers.length === 0) {
            html += '<div style="text-align: center; padding: 40px; color: #94a3b8;">' +
                '<i class="fa fa-inbox" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>' +
                '<p>Không có Offer đã duyệt nào chưa có Checklist</p>' +
                '</div>';
        } else {
            offers.forEach(function(offer) {
                var initial = offer.ho_ten ? offer.ho_ten.charAt(0).toUpperCase() : '?';
                html += '<div class="offer-item" onclick="createChecklistFromOffer(' + offer.id + ')">' +
                    '<div style="display: flex; align-items: center; gap: 16px;">' +
                    '<div class="offer-avatar">' + initial + '</div>' +
                    '<div>' +
                    '<div style="font-weight: 700; color: #1e293b; font-size: 15px;">' + escapeHtml(offer.ho_ten) + '</div>' +
                    '<div style="font-size: 12px; color: #64748b;">' + escapeHtml(offer.position) + ' • ' + escapeHtml(offer.department) + '</div>' +
                    '<div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">' +
                    '<span class="checklist-code">' + escapeHtml(offer.ma_offer) + '</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div style="color: #3b82f6; font-weight: 600; font-size: 13px;">' +
                    'Tạo Checklist <i class="fa fa-chevron-right"></i>' +
                    '</div>' +
                    '</div>';
            });
        }

        html += '</div></div></div></div>';

        $('body').append(html);
        $('.offer-list-modal').modal('show');
        $('.offer-list-modal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    function createChecklistFromOffer(offerId) {
        $.ajax({
            url: admin_url + 'checklist_profile/createFromOffer',
            type: 'POST',
            dataType: 'JSON',
            data: {
                offer_id: offerId,
                <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    $('.offer-list-modal').modal('hide');
                    if (oTable) {
                        oTable.ajax.reload();
                    }
                    // Mở chi tiết ngay
                    setTimeout(function() {
                        handleViewDetail(response.id);
                    }, 500);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra khi tạo Checklist');
            }
        });
    }

    function handleViewDetail(id) {
        var url = admin_url + 'checklist_profile/handling/' + id;
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#tnhModal').html(response);
                $('#tnhModal').modal('show');
            },
            error: function() {
                alert_float('danger', 'Có lỗi khi tải chi tiết');
            }
        });
    }

    $(document).ready(function() {
        // Initialize DataTable
        oTable = tnhInitDataTable('#table-checklist', '', {
            'order': [[0, 'desc']],
            'responsive': true,
            "ajax": {
                "url": admin_url + 'checklist_profile/getChecklistProfile',
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
                    // Update stats - status ở index 6
                    var stats = { S6: 0, S7: 0, S8: 0, S9: 0 };
                    (json.aaData || []).forEach(function(row) {
                        var status = row[6]; // status column
                        if (stats.hasOwnProperty(status)) {
                            stats[status]++;
                        }
                    });
                    $('#stat-s6').text(stats.S6);
                    $('#stat-s7').text(stats.S7);
                    $('#stat-s8').text(stats.S8);
                    $('#stat-s9').text(stats.S9);

                    return json.aaData || json.data || [];
                }
            },
            "columnDefs": [
                { targets: 0, visible: false, data: 0 }, // id
                { targets: 1, visible: false, data: 1 }, // ma_checklist
                {
                    targets: 2, // ho_ten
                    data: 2,
                    render: function(data, type, row) {
                        return '<div class="candidate-info">' +
                            '<div class="candidate-name">' + escapeHtml(row[2]) + '</div>' +
                            '<div><span class="checklist-code">' + escapeHtml(row[1]) + '</span></div>' +
                            '</div>';
                    }
                },
                { targets: 3, data: 3 }, // position
                {
                    targets: 4, // offer_date
                    data: 4,
                    render: function(data, type, row) {
                        return '<span style="font-size: 13px; color: #64748b;">' + formatDate(row[4]) + '</span>';
                    }
                },
                {
                    targets: 5, // employee_id
                    data: 5,
                    render: function(data, type, row) {
                        return row[5] ? '<span style="font-weight: 700; color: #7c3aed;">' + escapeHtml(row[5]) + '</span>' : '<span style="color: #cbd5e1;">—</span>';
                    }
                },
                {
                    targets: 6, // status
                    data: 6,
                    render: function(data, type, row) {
                        return getStatusBadge(row[6]);
                    }
                },
                {
                    targets: 7, // actions
                    orderable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        var viewUrl = admin_url + 'checklist_profile/handling/' + row[0];
                        var deleteUrl = admin_url + 'checklist_profile/delete/' + row[0];
                        return '<div style="display: flex; gap: 8px; justify-content: flex-end;">' +
                            '<a href="' + viewUrl + '" class="btn-action tnh-modal" title="Xem chi tiết">' +
                            '<i class="fa fa-eye"></i>' +
                            '</a>' +
                            '<a href="javascript:void(0);" class="btn-action btn-delete-checklist" data-url="' + deleteUrl + '" title="Xóa">' +
                            '<i class="fa fa-trash"></i>' +
                            '</a>' +
                            '</div>';
                    }
                }
            ],
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-2x"></i>',
                zeroRecords: '<div style="text-align: center; padding: 60px; color: #94a3b8;">' +
                    '<i class="fa fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;"></i>' +
                    '<h3 style="font-size: 18px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Chưa có Checklist nào</h3>' +
                    '<p style="font-size: 14px;">Nhấn "Tạo Checklist Mới" để bắt đầu</p>' +
                    '</div>'
            }
        });

        // Filters
        $('#search-filter').on('keyup', function() {
            $('#search_filter_hidden').val($(this).val());
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                oTable.ajax.reload();
            }, 500);
        });

        $('#status-filter').on('change', function() {
            $('#status_filter_hidden').val($(this).val());
            oTable.ajax.reload();
        });

        // Delete handler
        $(document).on('click', '.btn-delete-checklist', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var deleteUrl = $(this).data('url');

            if (confirm('Bạn có chắc chắn muốn xóa Checklist này?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.result == 1) {
                            alert_float('success', response.message);
                            oTable.ajax.reload();
                        } else {
                            alert_float('danger', response.message);
                        }
                    },
                    error: function() {
                        alert_float('danger', 'Có lỗi xảy ra khi xóa');
                    }
                });
            }
        });
    });
</script>
