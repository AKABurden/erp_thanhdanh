<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-procadure_detail_4 tbody tr td:nth-child(3) {
        white-space: unset;
        width: 300px;
    }

    /*.table-production_report img {*/
    /*    height: 20px;*/
    /*    width: 20px;*/
    /*}*/

    .table-production_report th:not(:first-child) {
        width: 100px;
        min-width: 100px;
    }


    .table-production_report td:nth-child(17),
    .table-production_report th:nth-child(17) {
        display: none;
    }
</style>
<style type="text/css">
    .progressbar_new_img {
        text-align: center !important;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .progressbar_new_img img {
        height: 35px;
        width: 35px;
    }

    ul.progressbar_new_img li.active_img img {
        border: 2px solid #00ff50;
    }

    ul.progressbar_new_img li.cancel img {
        border: 2px solid red;
    }

    ul.progressbar_new_img li.cancel_all img {
        border: 2px solid blue;
    }

    ul.progressbar_new_img li {
        width: 87px;
        float: left;
    }

    .progressbar_new:not(.hoang) {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }

    .progressbar_new li span {
        font-size: 11px;
    }

    .progressbar_new li:not(.hoang) {
        list-style-type: none;
        width: 22%;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }

    .progressbar_new li:not(.hoang):before {
        width: 10px;
        height: 10px;
        content: ' ';
        counter-increment: step;
        line-height: 51px;
        border: 5px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
    }

    .progressbar_new li:not(.hoang):after {
        width: 100% !important;
        height: 2px !important;
        content: '' !important;
        position: absolute !important;
        background-color: #7d7d7d !important;
        top: 4px !important;
        left: -50% !important;
        z-index: -1 !important;
    }

    .progressbar_new li:first-child:after {
        content: none;
        display: none;
    }

    .progressbar_new li.active_ch:before {
        border-color: red;
    }

    .progressbar_new li.active:not(.hoang) {
        color: green;
    }

    .progressbar_new li.active:not(.hoang):before {
        border-color: #55b776;
    }

    .progressbar_new li.cancel:before {
        border-color: red;
    }

    .progressbar_new li.active+li:after {
        background-color: #55b776 !important;
    }

    .wap-icon {
        float: left;
        width: 20%;
    }

    .wap-icon img {
        cursor: pointer;
        position: relative;
    }

    .wap-icon img:hover {
        top: -5px;
        transition: all 0.5s;
    }

    .wap-icon.active .wap-title span {
        color: #2887d4;
        border: 2px solid #2887d46b;
        padding: 5px 25px;
    }

    .wap-icon.active .wap-title span::before {
        content: "✔";
        margin-right: 5px;
    }

    .wap-title {
        margin-top: 10px;
    }

    .wap-title-status {
        margin-top: 20px;
    }

    .wap-title-status {
        position: relative;
    }

    .wap-title-status::before {
        content: "";
        width: 10px;
        height: 10px;
        position: absolute;
        background: #7d7d7d;
        border-radius: 50%;
        top: -14px;
        left: calc(50% - 5px);
    }

    .wap-title-status.success::before {
        background: #4ab138;
    }

    .width-progressbar_new {
        width: 110px !important;
        text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php echo render_input('code_items', 'Mã SP', '', 'text', ['placeholder' => 'Mã SP']) ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_input('code_production_orders', 'Mã lệnh SX Tổng', '', 'text', ['placeholder' => 'Mã lệnh SX Tổng']) ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_datetime_input('date_start', 'Ngày bắt đầu', (!empty($filter['date_start']) ? $filter['date_start'] . ' 00:00' : ''), ['placeholder' => lang('Ngày bắt đầu')]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_datetime_input('date_end', 'Ngày kết thúc', (!empty($filter['date_end']) ? $filter['date_end'] . ' 23:59' : ''), ['placeholder' => lang('Ngày kết thúc')]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= render_select('suppler_id', (!empty($suppler) ? $suppler : ''), ['id', 'company', 'code'], 'Nhà cung cấp') ?>
                                </div>
                                <div class="col-md-3">
                                    <?= render_select('role_id', (!empty($data_roles) ? $data_roles : []), ['roleid', 'name'], 'Chức vụ', (!empty($filter['role_id']) ? $filter['role_id'] : '')) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('customers', 'customers') ?>
                                    <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="<?= (!empty($filter['customer']) ? $filter['customer'] : '') ?>">
                                </div>
                            </div>
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                        <li class="active">
                                            <a style="padding: 3px;">
                                                <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="all">
                                                    <?= _l('leads_all') ?>
                                                    <span class="badge menu-badge bg-warning" id="all_status" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                    <span class="check-show" style="float: left; margin-right: 5px;">
                                                    </span>
                                                </button>
                                            </a>
                                        </li>
                                        <?php if (!empty($recommended_list)) : ?>
                                            <?php foreach ($recommended_list as $key => $value) : ?>
                                                <li>
                                                    <a style="padding: 3px;">
                                                        <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="<?= $value['id'] ?>">
                                                            <?= $value['name'] ?>
                                                            <span class="badge menu-badge bg-warning" id="all_status" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                            <span class="check-show" style="float: left; margin-right: 5px;">
                                                            </span>
                                                        </button>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                                    <input type="hidden" name="_room_id" id="_room_id" class="form-control status_table" value="<?= !empty($_GET['rom_id']) ? $_GET['rom_id'] : 0 ?>">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('#'),
                            _l('Ngày'),
                            _l('Số phiếu'),
                            _l('Tên phiếu'),
                            _l('Chi nhánh'),
                            _l('Bộ phận'),
                            _l('Loại yêu cầu'),
                            _l('Mã yêu cầu'),
                            _l('Lệnh SX Tổng'),
                            // _l('Đơn hàng bán'),
                            // _l('Nhà cung cấp'),
                            _l('Liên quan đến'),
                            _l('Người tiếp nhận xử lý'),
                            _l('Người kiểm soát'),
                            _l('Người lập BC'),
                            _l('Người đánh giá'),
                            _l('Số lượng'),
                            _l('Công đoạn phát hiện'),
                            _l('Sản phẩm'),
                            _l('Mô tả sự KHP'),
                            _l('Thời điểm ghi nhận'),
                            _l('Biểu đồ'),
                            _l('Tổ - Thiết bị'),
                            _l('Mã công việc'),
                            _l('Nhóm vi phạm'),
                            _l('process'),
                        ), 'production_report'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $(document).on('click', '.status-table li a button', function(event) {
        status_table = $(this).attr('data-value');
        $('#status_table').val(status_table);
        TableData.draw('page');
    });
    ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', $('#customer_search').val(), true, true);
    var CustomersServerParams = {
        'code_items': '[name="code_items"]',
        'code_production_orders': '[name="code_production_orders"]',
        'date_start': '[name="date_start"]',
        'date_end': '[name="date_end"]',
        'suppler_id': '[name="suppler_id"]',
        'status_table': '#status_table',
        'role_id': '#role_id',
        'customer_search': "#customer_search",
        '_room_id': "#_room_id",
    };
    var TableData;
    $(function() {
        TableData = initDataTable('.table-production_report', admin_url + 'reports_production/table', [0], [0], CustomersServerParams, [0, 'desc']);
        $.each(CustomersServerParams, function(filterIndex, filterItem) {
            $('' + filterItem).on('change', function() {
                TableData.draw('page');
            });
        });

        TableData.columns(23).visible(false, false);
        var recommend_id = <?= !empty($filter['recommend_id']) ? $filter['recommend_id'] : 0; ?>;
        if (recommend_id) {
            $('.btn-search[data-value="' + recommend_id + '"]').filter('.btn-search').click();
        }
    });
    $('body').on('click', '.remove_production_report', function() {
        var id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa phiếu báo cáo này?')) {
            $.get(admin_url + 'production_report/delete/' + id, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                TableData.draw('page');
            }).fail(function(error) {
                alert_float('danger', error.responseText);
            });
        }
    })
    $('.table-production_report').on('draw.dt', function() {
        viewChart();
        $('.rows-child').click();
    });

    function loadInfoData(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[23];
    }

    $('.table-production_report tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = TableData.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadInfoData(row.data())).show();
            tr.addClass('shown');
        }
    });

    function viewChart() {
        var canvasChart = $('body').find('.canvasChart');
        $.each(canvasChart, function(index, value) {
            var chart = $(value);
            if (chart.length > 0) {
                data = $(chart).attr('data-json');
                data = JSON.parse(data);
                new Chart(chart, {
                    type: 'doughnut',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        onClick: function(evt) {}
                    }
                });
            }
        })
    }
</script>