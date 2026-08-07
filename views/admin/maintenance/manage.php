<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-procadure_detail_4 tbody tr td:nth-child(3) {
        white-space: unset;
        width: 300px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="pull-right mright5 H_border">
				<?php if(has_permission('maintenance', '', 'create')) {?>
                    <a href="<?= admin_url('maintenance/create_maintenance_stick') ?>" class="btn btn-info H_action_button c_modal">
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
									<?php echo render_select('machines_search', (!empty($machines) ? $machines : []), ['id', 'name'], 'Thiết bị máy móc', [], ['multiple' => true])?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_select('maintenance_search', (!empty($maintenance) ? $maintenance : []), ['id', 'name'], 'Bộ phận', [], ['multiple' => true])?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_datetime_input('date_start', 'Ngày bắt đầu') ?>
                                </div>
                                <div class="col-md-3">
									<?php echo render_datetime_input('date_end', 'Ngày kết thúc') ?>
                                </div>
                            </div>
                            <hr/>
                            <div class="clearfix"></div>
							<?php render_datatable(array(
								_l('#'),
								_l('Ngày'),
								_l('Tên phiếu'),
								_l('Thiết bị'),
								_l('Bộ phận'),
								_l('Số lượng'),
								_l('Chi nhánh'),
								_l('Hạng mục bảo trì'),
//								_l('Biểu đồ'),
								_l('Ghi chú cách thức bảo trì'),
								_l('options'),
							), 'maintenance_stick'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var CustomersServerParams = {
        'machines_search': '[name="machines_search"]',
        'maintenance_search': '[name="maintenance_search"]',
        'date_start': '[name="date_start"]',
        'date_end': '[name="date_end"]',
    };
    var TableData;
    $(function () {
        TableData = initDataTable('.table-maintenance_stick', admin_url + 'maintenance/table', [0], [0], CustomersServerParams, [0, 'desc']);
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $('' + filterItem).on('change', function () {
                TableData.draw('page');
            });
        });
    });
    $('body').on('click', '.remove_maintenance', function () {
        var id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa phiếu báo cáo này?')) {
            $.get(admin_url + 'maintenance/delete/' + id, function (result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                TableData.draw('page');
            }).fail(function (error) {
                alert_float('danger', error.responseText);
            });
        }
    })
    $('.table-maintenance_stick').on('draw.dt', function () {
        // viewChart();
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
                        onClick: function (evt) {}
                    }
                });
            }
        })
    }
</script>
</body>
</html>
