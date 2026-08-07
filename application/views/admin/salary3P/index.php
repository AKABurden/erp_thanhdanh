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
                <a href="<?= base_url('admin/salary3P/import') ?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                    <?php echo _l('Import Excel'); ?>
                </a>
                <?php if ($this->preAddSalary3P): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/salary3P/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
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
                            <div class="">
                                <table id="table-salary-3p" class="table dt-tnh table-salary-3p" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã khung lương') ?></th>
                                        <th class="text-center"><?= lang('Vị trí') ?></th>
                                        <th class="text-center"><?= lang('Version') ?></th>
                                        <th class="text-center"><?= lang('Status') ?></th>
                                        <th class="text-center"><?= lang('Cấp bậc vai trò') ?></th>
                                        <th class="text-center"><?= lang('Mã thâm niên') ?></th>
                                        <th class="text-center"><?= lang('Thâm niên từ (Tháng)') ?></th>
                                        <th class="text-center"><?= lang('Thâm niên đến (Tháng)') ?></th>
                                        <th class="text-center"><?= lang('Hệ số') ?></th>
                                        <th class="text-center"><?= lang('Lương P1') ?></th>
                                        <th class="text-center"><?= lang('Lương P2') ?></th>
                                        <th class="text-center"><?= lang('Lương P3') ?></th>
                                        <th class="text-center"><?= lang('Phụ cấp P3') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú phụ cấp') ?></th>
                                        <th class="text-center"><?= lang('Ngày hiệu lực') ?></th>
                                        <th class="text-center"><?= lang('Ngày hết hiệu lực') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
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
    ajaxSelectParams('#role_id_search', 'admin/suggest_task/searchRoles', 0, true, true);
    var oTable = '';

    var fnserverparams = {
        'role_id_search': '#role_id_search'
    };
    oTable = tnhInitDataTable('#table-salary-3p',
        '<?= site_url('admin/salary3P/getSalary3P') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/salary3P/getSalary3P') ?>',
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

    $(document).on('click', '.onoffswitch_salary', function() {
        var r = confirm("<?php echo _l('Phải chắc chắn thực hiện không!');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('data-switch-url'), function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
                oTable.draw();
            }, 'json');
        }
        return false;
    });
    $(document).on('change',
        '#role_id_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
</script>