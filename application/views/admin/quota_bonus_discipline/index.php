<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a href="<?= admin_url('quota_bonus_discipline/modal_excel_import/') . $type ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('c_import_excel'); ?></a>
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
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <input class="hide" name="type" value="<?= $type ?>">
                                <table id="table-quota-bonus" class="table dataTable dt-tnh table-quota-bonus-new" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('#') ?></th>
                                            <th class="text-center"><?= lang('Mã') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Tên') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Đối Tượng Áp Dụng') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Loại') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Điểm') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Hình Thức') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Giá Trị') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Công thức') ?> <?= $text_title ?></th>
                                            <th class="text-center"><?= lang('Lần') ?> <?= $text_title ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
        'type': '[name="type"]',
    };
    oTable = tnhInitDataTable('#table-quota-bonus',
        '<?= site_url('admin/quota_bonus_discipline/loadDataQuotaBonusDisciplines') ?>', {
            'order': false,
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/quota_bonus_discipline/loadDataQuotaBonusDisciplines') ?>',
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


    function updateQuota(_this, id, name) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['id'] = id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/quota_bonus_discipline/updateQuota',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function(xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }

    function updateQuotaNew(_this, id, precious_id, name) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['id'] = id;
        dataPOST['precious_id'] = precious_id;
        dataPOST['value'] = $(_this).val();
        dataPOST['name'] = name;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/quota_bonus_discipline/updateQuotaNew',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }

                if (typeof oTable !== 'undefined') {
                    oTable.draw('page');
                }
            },
            error: function(xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }
</script>