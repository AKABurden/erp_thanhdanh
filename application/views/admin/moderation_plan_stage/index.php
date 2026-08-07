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
                                        <input type="hidden" id="type" name="type" value="<?= $type ?>">
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="<?= date('01/m/Y') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="<?= date('t/m/Y') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-moderation-plan-stage" class="table dt-tnh table-moderation-plan-stage-new" style="width:2200px;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2"><?= lang('STT') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Ngày Mở Lệnh') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Số Phiếu') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Hình Ảnh SP') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Phiếu PTM') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Mẫu Sản Xuất') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Layout Ghép') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Khuân Bế') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('NPL') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Vật Tư') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Phiếu Cắt Giấy') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Ngày Về NPL') ?></th>

                                        <th class="text-center" rowspan="2"><?= lang('Tên Thiết Bị-Công Đoạn') ?></th>
                                        <th class="text-center" colspan="2"><?= lang('Kích Thước Vận Hành') ?></th>
                                        <th class="text-center" colspan="7"><?= lang('Mặt 1') ?></th>
                                        <th class="text-center" colspan="7"><?= lang('Mặt 2') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tổng Thời Gian Canh Bài Mặt 1+Mặt 2') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tổng NPL Canh Bài Mặt 1 + Mặt 2') ?></th>
                                        <th class="text-center" rowspan="2"><?= lang('Tổng NPL Mặt 1+Mặt 2') ?></th>
                                        <?php foreach (getListColumTable() as $key => $value){ ?>
                                            <th class="text-center" rowspan="2" style="min-width: 100px"><?= lang($value['name']) ?></th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= lang('Height') ?></th>
                                        <th class="text-center"><?= lang('Width') ?></th>
                                        <th class="text-center"><?= lang('Số Con/Tờ Vận Hành') ?></th>
                                        <th class="text-center"><?= lang('Loại NPL') ?></th>
                                        <th class="text-center"><?= lang('Định Mức NPL/Lần Vận Hành') ?></th>
                                        <th class="text-center"><?= lang('Số Lần Vận Hành/Mặt') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng NPL') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Thời Gian Canh Bài') ?></th>
                                        <th class="text-center"><?= lang('Định Mức NPL Canh Bài') ?></th>
                                        <th class="text-center"><?= lang('Số Con/Tờ Vận Hành') ?></th>
                                        <th class="text-center"><?= lang('Loại NPL') ?></th>
                                        <th class="text-center"><?= lang('Định Mức NPL/Lần Vận Hành') ?></th>
                                        <th class="text-center"><?= lang('Số Lần Vận Hành/Mặt') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng NPL') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Thời Gian Canh Bài') ?></th>
                                        <th class="text-center"><?= lang('Định Mức NPL Canh Bài') ?></th>
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
        type: '#type',
    };
    oTable = tnhInitDataTable('#table-moderation-plan-stage',
        '<?= site_url('admin/moderation_plan_stage/getModerationPlanStages') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/moderation_plan_stage/getModerationPlanStages') ?>',
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

    $('#table-moderation-plan-stage').on('draw.dt', function(event) {
        // if ($("#type").val() == 1){
        //     oTable.columns(24).visible(true, true);
        // } else {
        //     oTable.columns(24).visible(true, true);
        // }
        init_datepicker();
    });

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function updateModerationPlanStage(_this,po_id,poi_id,pois_id,item_id,type_productionlist_id,stage_id,name){
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['po_id'] = po_id;
        dataPOST['poi_id'] = poi_id;
        dataPOST['pois_id'] = pois_id;
        dataPOST['item_id'] = item_id;
        dataPOST['type_productionlist_id'] = type_productionlist_id;
        dataPOST['stage_id'] = stage_id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/moderation_plan_stage/updateModerationPlanStage',
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
        type = $('#type').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_plan_stage/exportExcel',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                type: type,
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