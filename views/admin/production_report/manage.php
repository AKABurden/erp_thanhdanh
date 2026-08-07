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
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="pull-right mright5 H_border">
                <?php if(has_permission('production_report', '', 'create')) {?>
                    <a href="<?= admin_url('production_report/detail') ?>" class="btn btn-info H_action_button">
                        <?php echo _l('create_add_new'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="tab-content" id="tab_content_procadure">
                            <div class="row">
                                <div class="col-md-3">
									<?php echo render_input('code_items', 'Mã SP') ?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_input('code_production_orders', 'Mã lệnh SX Tổng') ?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_datetime_input('date_start', 'Ngày bắt đầu', (!empty($filter['date_start']) ? $filter['date_start'].' 00:00' : '')) ?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_datetime_input('date_end', 'Ngày kết thúc', (!empty($filter['date_end']) ? $filter['date_end'].' 23:59' : '')) ?>
                                </div>
                                <div class="col-md-3">
                                <?= render_select('suppler_id', (!empty($suppler) ? $suppler : ''), ['id', 'company', 'code'], 'Nhà cung cấp') ?>
                                </div>
                                <div class="col-md-3">
                                    <?= render_select('role_id', (!empty($data_roles) ? $data_roles : []), ['roleid', 'name'], 'Chức vụ', (!empty($filter['role_id']) ? $filter['role_id'] : '')) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('customers', 'customers') ?>
                                    <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                                        id="customer_search" class="customer_search" style="width: 100%;" value="<?= (!empty($filter['customer']) ? $filter['customer'] : '') ?>">
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
                                </div>
                            </div>
                            <!-- <hr/> -->
                            <div class="clearfix"></div>
							<?php render_datatable(array(
								_l('#'),
								_l('Ngày'),
								_l('Tên phiếu'),
								_l('Chi nhánh'),
								_l('Bộ phận'),
								_l('Lệnh SX Tổng'),
								// _l('Đơn hàng bán'),
								// _l('Nhà cung cấp'),
								_l('Liên quan đến'),
								_l('Người xử lý'),
								_l('Người theo dõi'),
								_l('Người tạo'),
								_l('Số lượng'),
								_l('Công đoạn phát hiện'),
								_l('Sản phẩm'),
								_l('Mô tả sự KHP'),
								_l('Thời điểm ghi nhận'),
								_l('Biểu đồ'),
								_l('options'),
							), 'production_report'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).on('click', '.status-table li a button', function(event) {
        status_table = $(this).attr('data-value');
        $('#status_table').val(status_table);
        TableData.draw();
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
    };
    var TableData;
    $(function () {
        TableData = initDataTable('.table-production_report', admin_url + 'production_report/table', [0], [0], CustomersServerParams, [0, 'desc']);
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $('' + filterItem).on('change', function () {
                TableData.draw('page');
            });
        });

        var recommend_id = <?= !empty($filter['recommend_id']) ? $filter['recommend_id'] : 0; ?>;
        if (recommend_id) {
            $('.btn-search[data-value="'+recommend_id+'"]').filter('.btn-search').click();
        }
    });
    $('body').on('click', '.remove_production_report', function () {
        var id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa phiếu báo cáo này?')) {
            $.get(admin_url + 'production_report/delete/' + id, function (result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                TableData.draw('page');
            }).fail(function (error) {
                alert_float('danger', error.responseText);
            });
        }
    })
    $('.table-production_report').on('draw.dt', function () {
        viewChart();
    });
    function viewChart() {
        var canvasChart = $('body').find('.canvasChart');
        $.each(canvasChart, function (index, value) {
            var chart = $(value);
            if (chart.length > 0) {
                data = $(chart).attr('data-json');
                data = JSON.parse(data);
                new Chart(chart, {
                    type: 'doughnut',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        onClick: function (evt) {
                        }
                    }
                });
            }
        })
    }
</script>
</body>
</html>
