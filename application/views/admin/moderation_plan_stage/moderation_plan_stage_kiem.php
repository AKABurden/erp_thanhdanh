<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-moderation-plan-stage-kiem" class="table dt-tnh table-moderation-plan-stage-kiem-new" style="width:2200px;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Hình Ảnh SP') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã Phiếu') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Ngày Lập Phiếu') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mã SP') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Nhóm SP') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Mã Chủng Loại SP') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tên Brand') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Phân Loại Khách Hàng') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Đơn Vị Tính SP') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Nhóm Công Đoạn Kiểm') ?></th>
                                        <th class="text-center" colspan="3"><?= lang('Mặt 1') ?></th>
                                        <th class="text-center" colspan="3"><?= lang('Mặt 2') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tiêu Chuẩn/ Quy Định') ?></th>
                                        <?php foreach (getListColumTable() as $key => $value){ ?>
                                            <th class="text-center" rowspan="2" style="min-width: 100px"><?= lang($value['name']) ?></th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= lang('Loại Kiểm') ?></th>
                                        <th class="text-center"><?= lang('Số Lần Kiểm') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Năng Suất ') ?></th>
                                        <th class="text-center"><?= lang('Loại Kiểm') ?></th>
                                        <th class="text-center"><?= lang('Số Lần Kiểm') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Năng Suất ') ?></th>
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
    oTable = tnhInitDataTable('#table-moderation-plan-stage-kiem',
        '<?= site_url('admin/moderation_plan_stage/getModerationPlanStagesKiem') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/moderation_plan_stage/getModerationPlanStagesKiem') ?>',
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
                    init_datepicker();
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $('#table-moderation-plan-stage-kiem').on('draw.dt', function(event) {
        init_datepicker();
    });

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function updateModerationPlanStageKiem(_this,po_id,item_id,type_productionlist_id,stage_id,name){
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['po_id'] = po_id;
        dataPOST['item_id'] = item_id;
        dataPOST['type_productionlist_id'] = type_productionlist_id;
        dataPOST['stage_id'] = stage_id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/moderation_plan_stage/updateModerationPlanStageKiem',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function (xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }
    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_plan_stage/exportExcelStageKiem',
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