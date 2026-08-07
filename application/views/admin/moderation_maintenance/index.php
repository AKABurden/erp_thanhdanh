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
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('start_date', 'start_date_search') ?>
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-moderation_maintenance" class="table dt-tnh table-moderation_maintenance-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Bảo Dưỡng') ?></th>
                                            <th class="text-center"><?= lang('Loại Bảo Dưỡng') ?></th>
                                            <th class="text-center"><?= lang('Nhóm Bảo Dưỡng') ?></th>
                                            <th class="text-center"><?= lang('Bộ Phận Thiết Bị') ?></th>
                                            <th class="text-center"><?= lang('Khu Vực Bảo Dưỡng') ?></th>
                                            <th class="text-center"><?= lang('Chi Tiết Bảo Dưỡng') ?></th>
                                            <th class="text-center"><?= lang('Số lượng') ?></th>
                                            <th class="text-center"><?= lang('Mã Thiết Bị') ?></th>
                                            <th class="text-center"><?= lang('Tên Thiết Bị') ?></th>
                                            <th class="text-center"><?= lang('Chi nhánh') ?></th>
                                            <th class="text-center"><?= lang('Kết Quả') ?></th>
                                            <th class="text-center"><?= lang('Báo Cáo Sự Cố') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Dự Kiến') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Bắt Đầu') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Kết Thúc') ?></th>
                                            <th class="text-center"><?= lang('Tiêu Chuẩn/ Quy Định') ?></th>
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
    };
    oTable = tnhInitDataTable('#table-moderation_maintenance',
        '<?= site_url('admin/moderation_maintenance/getModerationMaintenance') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/moderation_maintenance/getModerationMaintenance') ?>',
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
            "createdRow": function(row, data, index) {},
            "columnDefs": [],
        });

    $('#table-moderation_maintenance').on('draw.dt', function(event) {
        init_datepicker();
    });

    function updateDate(_this, id, name) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['_value'] = $(_this).val();
        dataPOST['id'] = id;
        dataPOST['name'] = name;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_maintenance/updateDate',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    oTable.draw('page');
                } else if (response.result == 0) {
                    alert_float('danger', response.message);
                    oTable.draw('page');
                }
            }
        });
    }
    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function agree(_this, suggest_id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_id'] = suggest_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/purchase_request_material/agree',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                $(".click1")[0].click();
                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function(xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });

    }

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_maintenance/exportExcel',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
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