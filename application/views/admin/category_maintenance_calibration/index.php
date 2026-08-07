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
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/category_maintenance_calibration/detail_maintenance_calibration?type='.$type.'') ?>" class=" tnh-modal btn btn-info H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
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
                                <input type="hidden" class="type" id="type" name="type" value="<?= $type ?>">
                                <div class="col-md-3">
                                    <?= lang('Thiết bị', 'machines_search') ?>
                                    <input type="text" name="machines_search" id="machines_search" class="machines_search"
                                           data-placeholder="<?= lang('Thiết bị') ?>" style="width: 100%;"
                                           value=""
                                           title="">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-maintenance_calibration" class="table dt-tnh table-maintenance_calibration" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Nhóm Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Tên Nhóm Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Mã Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('Tên Thiết Bị') ?></th>
                                        <th class="text-center"><?= lang('dt_department_maintenance_calibration') ?></th>
                                        <th class="text-center"><?= lang('dt_detail_maintenance_calibration') ?></th>
                                        <th class="text-center"><?= lang('Thành Tiền') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Bắt Đầu') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Tái Tục') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Yêu Cầu Bảo Dưỡng') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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
    ajaxSelectParams('#machines_search', 'admin/suggest_repalce/searchMachines', $("#machines_search").val(), true, true);
    var oTable = '';

    var fnserverparams = {
        type: "#type",
        machines_search: "#machines_search",
    };
    oTable = tnhInitDataTable('#table-maintenance_calibration',
        '<?= site_url('admin/category_maintenance_calibration/getMaintenanceCalibration') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/category_maintenance_calibration/getMaintenanceCalibration') ?>',
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
        '#machines_id',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

</script>