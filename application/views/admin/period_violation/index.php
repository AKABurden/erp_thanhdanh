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
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right hide" href="javascript:void(0)">Xuất Excel</a>
                <?php if ($this->preAddPeriodViolation): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/period_violation/add_period_violation') ?>" class="btn btn-info hide H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
                    </div>
                <?php endif ?>
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
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-period-violation" class="table dt-tnh table-period-violation" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" ><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã NV') ?></th>
                                        <th class="text-center"><?= lang('Tên NV') ?></th>
                                        <th class="text-center"><?= lang('Phiếu công việc') ?></th>
                                        <th class="text-center"><?= lang('Số lần vi phạm đã có (BCKPH)') ?></th>
                                        <th class="text-center"><?= lang('Số lần vi phạm mới (kỳ này)') ?></th>
                                        <th class="text-center"><?= lang('Số lần P1 tối đa') ?></th>
                                        <th class="text-center"><?= lang('Số lần P1') ?></th>
                                        <th class="text-center"><?= lang('Số lần P2 tối đa') ?></th>
                                        <th class="text-center"><?= lang('Số lần P2') ?></th>
                                        <th class="text-center"><?= lang('Số lần P3 tối đa') ?></th>
                                        <th class="text-center"><?= lang('Số lần P3') ?></th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                    </tr>
                                    </tfoot>
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
    };
    oTable = tnhInitDataTable('#table-period-violation',
        '<?= site_url('admin/period_violation/getPeriodViolation') ?>', {
            'order': [
                [0, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/period_violation/getPeriodViolation') ?>',
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
        '#year_search,#room_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

</script>