<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
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
                            <div class="row" style="margin-bottom:5px">
                                <div class="col-md-3">
                                    <?php echo render_select('staff_task', !empty($staff) ? $staff : [], ['staffid', 'fullname'], 'Nhân viên', '', ['multiple' => true, 'data-actions-box' => true], [], '', '', false) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('start_date', 'start_date_search') ?>
                                    <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                           id="start_date_search" class="start_date_search datepicker form-control"
                                           style="width: 100%;" value="">
                                </div>
                                <div class="col-md-3">
                                    <?= lang('end_date', 'end_date_search') ?>
                                    <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                           id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                           value="">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic_production_report" class="table dt-tnh table-synthetic_production_report-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Chi Nhánh') ?></th>
                                        <th class="text-center"><?= lang('Phòng Ban') ?></th>
                                        <th class="text-center"><?= lang('Mã Nhân Viên Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Tên Nhân Viên Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Mã BCKPH') ?></th>
                                        <th class="text-center"><?= lang('Mã BC Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Số BC Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Số Lần Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Ngày Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Sự Cố') ?></th>
                                        <th class="text-center"><?= lang('Chi Phí Thiệt Hại') ?></th>
                                        <th class="text-center"><?= lang('Xử Lý Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Qui Trình Phòng Ngừa') ?></th>
                                        <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>
                                        <th class="text-center"><?= lang('Ngày Điều Chỉnh Foso') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Tái Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Ngày Tái Vi Phạm') ?></th>
                                        <th class="text-center"><?= lang('Chi Phí Thiệt Hại') ?></th>
                                        <th class="text-center"><?= lang('Hình Thức Kỷ Luật') ?></th>
                                        <th class="text-center"><?= lang('Ngày Áp Dụng Kỷ Luật') ?></th>
                                        <th class="text-center"><?= lang('Ngày Hoàn Thành Lưu Trữ') ?></th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
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

    var oTable = '';

    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        staff_task: '#staff_task',
    };
    oTable = tnhInitDataTable('#table-synthetic_production_report',
        '<?= site_url('admin/reports_manu/getSyntheticProductionReport') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/reports_manu/getSyntheticProductionReport') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });


    $(document).on('change',
        '#start_date_search,#end_date_search,#staff_task',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();
        staff_task = $('#staff_task').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_manu/exportExcelSyntheticProductionReport',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                staff_task: staff_task,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>