<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    /* Modern Offer Management Styles */
    .offer-wrapper {
        background: #f8f9fa;
        min-height: calc(100vh - 100px);
        padding: 20px;
    }

    .offer-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .offer-header {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .offer-title-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .offer-icon-box {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .offer-icon-box i {
        color: white;
        font-size: 20px;
    }

    .offer-title-text h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .offer-title-text p {
        font-size: 13px;
        color: #64748b;
        margin: 4px 0 0 0;
    }

    .offer-action-buttons {
        display: flex;
        gap: 10px;
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
        text-decoration: none;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }

    .btn-modern:active {
        transform: translateY(0);
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

    .btn-outline-modern {
        background: white;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .btn-outline-modern:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #475569;
    }

    /* Filter Toolbar */
    .offer-filters {
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
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

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-group select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 8px center;
        background-repeat: no-repeat;
        background-size: 20px;
        padding-right: 36px;
    }


    #table-offer {
        margin-bottom: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    #table-offer thead th {
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

    #table-offer tbody tr {
        transition: all 0.2s;
        cursor: pointer;
    }

    #table-offer tbody tr:hover {
        background-color: #eff6ff;
    }

    #table-offer tbody td {
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

    .candidate-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .offer-code {
        font-family: 'Courier New', monospace;
        font-size: 10px;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid #bfdbfe;
    }

    .date-small {
        font-size: 11px;
        color: #94a3b8;
    }

    .position-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .position-title {
        font-weight: 600;
        color: #334155;
    }

    .department-text {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .salary-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .salary-total {
        font-weight: 700;
        color: #059669;
        font-size: 15px;
    }

    .salary-detail {
        font-size: 12px;
        color: #94a3b8;
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

    .status-badge i {
        font-size: 10px;
    }

    .status-draft {
        background: #f8fafc;
        color: #64748b;
        border-color: #cbd5e1;
    }

    .status-pending {
        background: #fffbeb;
        color: #b45309;
        border-color: #fcd34d;
    }

    .status-sent {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .status-accepted {
        background: #ecfdf5;
        color: #047857;
        border-color: #86efac;
    }

    .status-rejected {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .status-approved {
        background: #f0fdf4;
        color: #15803d;
        border-color: #86efac;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

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

    .btn-action i {
        font-size: 14px;
    }

    /* Loading State */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .loading-overlay.active {
        display: flex;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .offer-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .offer-action-buttons {
            width: 100%;
            flex-direction: column;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }

        .offer-filters {
            flex-direction: column;
        }

        .filter-group {
            min-width: 100%;
        }
    }

    /* DataTables Custom Styles */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3b82f6 !important;
        color: white !important;
        border-color: #3b82f6 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #eff6ff !important;
        color: #2563eb !important;
        border-color: #3b82f6 !important;
    }

    .dataTables_wrapper .dataTables_info {
        color: #64748b;
        font-size: 13px;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        color: #94a3b8;
    }
</style>

<div id="wrapper" class="offer-wrapper">
    <!-- Main Card Container -->
    <div class="col-md-12">
        <div class="offer-card">
            <!-- Header Section -->
            <div class="offer-header">
                <div class="offer-title-section">
                    <div class="offer-icon-box">
                        <i class="fa fa-briefcase"></i>
                    </div>
                    <div class="offer-title-text">
                        <h2>Quản Lý Offer</h2>
                        <p>Danh sách thư mời tuyển dụng (Bước S5)</p>
                    </div>
                </div>
                <div class="offer-action-buttons">
                    <!-- <a href="#" class="btn-modern btn-outline-modern">
                    <i class="fa fa-file-excel-o"></i>
                    Xuất Excel
                </a> -->
                    <a href="<?= base_url('admin/propose_offer/handling/0') ?>"
                        class="btn-modern btn-primary-modern tnh-modal">
                        <i class="fa fa-plus"></i>
                        Tạo Offer Mới
                    </a>
                </div>
            </div>

            <!-- Filters Toolbar -->
            <div class="offer-filters">
                <div class="filter-group" style="flex: 2;">
                    <i class="fa fa-search"></i>
                    <input type="text" id="search-filter" placeholder="Tìm kiếm ứng viên, mã offer..." />
                </div>
                <div class="filter-group">
                    <i class="fa fa-filter"></i>
                    <select id="status-filter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="DRAFT">Nháp</option>
                        <option value="DANG_CHO_DUYET">Chờ duyệt</option>
                        <option value="DA_DUYET">Đã duyệt</option>
                        <option value="DA_GUI">Đã gửi</option>
                        <option value="CHAP_NHAN">Đã chấp nhận</option>
                        <option value="TU_CHOI">Đã từ chối</option>
                    </select>
                </div>
                <div class="filter-group">
                    <i class="fa fa-building-o"></i>
                    <select id="department-filter">
                        <option value="">Tất cả phòng ban</option>
                        <option value="Ban Điều hành">Ban Điều hành</option>
                        <option value="Khối Công Nghệ">Khối Công Nghệ</option>
                        <option value="Khối Kinh Doanh">Khối Kinh Doanh</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">

                <!-- Table Container -->
                <div class="offer-table-container">
                    <?php echo $this->load->view('admin/alert') ?>
                    <table id="table-offer" class="table table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mã Offer</th>
                                <th>Tên ứng viên</th>
                                <th>Vị trí</th>
                                <th>Phòng ban</th>
                                <th>Ngày tạo</th>
                                <th>Lương P1</th>
                                <th>Tổng lương</th>
                                <th>Lương P3</th>
                                <th>CP Lương P3</th>
                                <th>Trạng thái</th>
                                <th class="text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Hidden inputs -->
<input type="hidden" name="type_search" id="type_search" value="<?= isset($type) ? $type : 1 ?>">
<input type="hidden" name="search_filter" id="search_filter_hidden" value="">
<input type="hidden" name="status_filter" id="status_filter_hidden" value="">
<input type="hidden" name="department_filter" id="department_filter_hidden" value="">

<?php init_tail(); ?>
<?php $this->load->view('loader') ?>

<script type="text/javascript">
    var oTable = null;
    var fnserverparams = {
        type_search: "#type_search",
        search_filter: "#search_filter_hidden",
        status_filter: "#status_filter_hidden",
        department_filter: "#department_filter_hidden"
    };

    // Format currency VND
    function formatCurrency(amount) {
        if (!amount) return '0 ₫';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Format date
    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    // Get status badge HTML
    function getStatusBadge(status) {
        var badges = {
            'DRAFT': '<span class="status-badge status-draft"><i class="fa fa-file-text-o"></i> Nháp</span>',
            'DANG_CHO_DUYET': '<span class="status-badge status-pending"><i class="fa fa-clock-o"></i> Chờ duyệt</span>',
            'DA_DUYET': '<span class="status-badge status-approved"><i class="fa fa-check-circle"></i> Đã duyệt</span>',
            'DA_GUI': '<span class="status-badge status-sent"><i class="fa fa-paper-plane-o"></i> Đã gửi Offer</span>',
            'CHAP_NHAN': '<span class="status-badge status-accepted"><i class="fa fa-check-circle-o"></i> Đã chấp nhận</span>',
            'TU_CHOI': '<span class="status-badge status-rejected"><i class="fa fa-times-circle-o"></i> Đã từ chối</span>'
        };
        return badges[status] || badges['DRAFT'];
    }

    $(document).ready(function() {
        // Initialize DataTable using tnhInitDataTable
        oTable = tnhInitDataTable('#table-offer', '', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/propose_offer/getProposeOffer') ?>',
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
                    name: 'ma_offer',
                    visible: false,
                    data: 1
                },
                {
                    targets: 2,
                    name: 'ten_ung_vien',
                    data: 2,
                    render: function(data, type, row) {
                        return '<div class="candidate-info">' +
                            '<div class="candidate-name">' + (row[2] || '-') + '</div>' +
                            '<div class="candidate-meta">' +
                            '<span class="offer-code">' + (row[1] || '-') + '</span>' +
                            '<span class="date-small">| Tạo: ' + formatDate(row[5]) + '</span>' +
                            '</div>' +
                            '</div>';
                    }
                },
                {
                    targets: 3,
                    name: 'vi_tri_offer',
                    data: 3,
                    render: function(data, type, row) {
                        return '<div class="position-info">' +
                            '<div class="position-title">' + (row[3] || '-') + '</div>' +
                            '<div class="department-text">' +
                            '<i class="fa fa-building-o"></i> ' +
                            (row[4] || '-') +
                            '</div>' +
                            '</div>';
                    }
                },
                {
                    targets: 4,
                    name: 'phong_ban_offer',
                    visible: false,
                    data: 4
                },
                {
                    targets: 5,
                    name: 'ngay_tao',
                    visible: false,
                    data: 5
                },
                {
                    targets: 6,
                    name: 'luong_p1',
                    visible: false,
                    data: 6
                },
                {
                    targets: 7,
                    name: 'luong_p2',
                    data: 7,
                    render: function(data, type, row) {
                        var luongP1 = parseFloat(row[6]) || 0;
                        var luongP2 = parseFloat(row[7]) || 0;
                        var luongP3 = parseFloat(row[8]) || 0;
                        var cpLuongP3 = parseFloat(row[9]) || 0;
                        var total = luongP1 + luongP2 + luongP3 + cpLuongP3;
                        return '<div class="salary-info">' +
                            '<div class="salary-total">' + formatCurrency(total) + '</div>' +
                            '<div class="salary-detail">P1: ' + formatCurrency(luongP1) + '</div>' +
                            '<div class="salary-detail">P2: ' + formatCurrency(luongP2) + '</div>' +
                            '<div class="salary-detail">P3: ' + formatCurrency(luongP3) + '</div>' +
                            '<div class="salary-detail">CP P3: ' + formatCurrency(cpLuongP3) + '</div>' +
                            '</div>';
                    }
                },
                {
                    targets: 8,
                    name: 'luong_p1',
                    visible: false,
                    data: 8
                },
                {
                    targets: 9,
                    name: 'luong_p1',
                    visible: false,
                    data: 9
                },
                {
                    targets: 10,
                    name: 'trang_thai',
                    data: 10,
                    render: function(data, type, row) {
                        return getStatusBadge(row[10]);
                    }
                },
                {
                    targets: 11,
                    name: 'actions',
                    data: 11,
                    orderable: false,
                    className: 'text-right',
                    render: function(data, type, row) {
                        var editUrl = '<?= base_url('admin/propose_offer/handling/') ?>' + row[0];
                        var previewUrl = '<?= base_url('admin/propose_offer/preview/') ?>' + row[0];
                        var deleteUrl = '<?= base_url('admin/propose_offer/delete/') ?>' + row[0];
                        return '<div class="action-buttons">' +
                            '<a href="' + previewUrl + '" class="btn-action tnh-modal2" title="Xem trước">' +
                            '<i class="fa fa-eye"></i>' +
                            '</a>' +
                            '<a href="' + editUrl + '" class="btn-action tnh-modal" title="Chỉnh sửa">' +
                            '<i class="fa fa-edit"></i>' +
                            '</a>' +
                            '<a href="javascript:void(0);" class="btn-action btn-delete-offer" data-url="' + deleteUrl + '" title="Xóa">' +
                            '<i class="fa fa-trash"></i>' +
                            '</a>' +
                            '</div>';
                    }
                }
            ],
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-2x"></i>',
                search: '',
                lengthMenu: 'Hiển thị _MENU_ mục',
                info: 'Hiển thị _START_ đến _END_ của _TOTAL_ offer',
                infoEmpty: 'Không có dữ liệu',
                infoFiltered: '(lọc từ _MAX_ tổng số offer)',
                zeroRecords: '<div class="empty-state">' +
                    '<i class="fa fa-inbox"></i>' +
                    '<h3>Chưa có Offer nào</h3>' +
                    '<p>Nhấn "Tạo Offer Mới" để bắt đầu</p>' +
                    '</div>',
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            }
        });

        // Filter handlers - update hidden inputs
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

        $('#department-filter').on('change', function() {
            $('#department_filter_hidden').val($(this).val());
            oTable.ajax.reload();
        });

        // Delete offer handler
        $(document).on('click', '.btn-delete-offer', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var deleteUrl = $(this).data('url');

            if (confirm('Bạn có chắc chắn muốn xóa Offer này?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.result == 1) {
                            alert_float('success', response.message || 'Xóa Offer thành công!');
                            oTable.ajax.reload();
                        } else {
                            alert_float('danger', response.message || 'Có lỗi xảy ra khi xóa');
                        }
                    },
                    error: function() {
                        alert_float('danger', 'Có lỗi xảy ra khi xóa Offer');
                    }
                });
            }
        });

        // Row click handler - edit
        $('#table-offer tbody').on('click', 'tr', function(e) {
            // Don't trigger if clicking on action buttons
            if ($(e.target).closest('.action-buttons').length) {
                return;
            }

            var data = oTable.row(this).data();
            if (data && data.id) {
                var editUrl = '<?= base_url('admin/propose_offer/handling/') ?>' + data.id;
                // Open modal instead of redirect
                $.ajax({
                    url: editUrl,
                    type: 'GET',
                    success: function(response) {
                        $('#tnhModal').html(response);
                        $('#tnhModal').modal('show');
                    }
                });
            }
        });
    });
</script>