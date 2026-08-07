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
                                <table id="table-moderation-plan-stage-dieu-xe" class="table dt-tnh table-moderation-plan-stage-dieu-xe-new" style="width:2200px;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Hình Ảnh SP') ?></th>
                                        <th class="text-center"><?= lang('Mã Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Mã Phiếu Yêu Cầu Điều Xe') ?></th>
                                        <th class="text-center"><?= lang('Mã SP') ?></th>
                                        <th class="text-center"><?= lang('Mã Đơn Hàng') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Giao Hàng') ?></th>
                                        <th class="text-center"><?= lang('Tên KH') ?></th>
                                        <th class="text-center"><?= lang('Phân Loại Khách Hàng') ?></th>
                                        <th class="text-center"><?= lang('Số Con/Kiện') ?></th>
                                        <th class="text-center"><?= lang('Số Kg/Kiện') ?></th>
                                        <th class="text-center"><?= lang('Tổng Số Ký') ?></th>
                                        <th class="text-center"><?= lang('Tổng Số Kiện') ?></th>
                                        <th class="text-center"><?= lang('Định Mức Phương Tiện') ?></th>
                                        <th class="text-center"><?= lang('Phương Tiện') ?></th>
                                        <th class="text-center"><?= lang('Địa Chỉ Giao Hàng') ?></th>
                                        <th class="text-center"><?= lang('Mã Lộ Trình') ?></th>
                                        <th class="text-center"><?= lang('Đơn Vị Vận Chuyển') ?></th>
                                        <th class="text-center"><?= lang('Đơn Giá') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
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
    oTable = tnhInitDataTable('#table-moderation-plan-stage-dieu-xe',
        '<?= site_url('admin/moderation_plan_stage/getModerationPlanStagesDieuXe') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/moderation_plan_stage/getModerationPlanStagesDieuXe') ?>',
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

    $('#table-moderation-plan-stage-dieu-xe').on('draw.dt', function(event) {
        init_datepicker();
        var total_tr = $('#table-moderation-plan-stage-dieu-xe').find('tbody').find('tr');
        $.each(total_tr, function(i, v) {
            order_id = $(`.order_id_${i}`).val();
            ajaxSelectParams('#delivery_id_' + i + '', 'admin/moderation_plan_stage/searchSuggestOutsource', 0, {"order_id":order_id}, true);
        });
    });

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function updateModerationPlanStageDieuXe(_this,po_id,item_id,type_productionlist_id,order_id,stage_id,name){
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['po_id'] = po_id;
        dataPOST['item_id'] = item_id;
        dataPOST['type_productionlist_id'] = type_productionlist_id;
        dataPOST['stage_id'] = stage_id;
        dataPOST['order_id'] = order_id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/moderation_plan_stage/updateModerationPlanStageDieuXe',
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
            url: site.base_url + 'admin/moderation_plan_stage/exportExcelStageGiaoHang',
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