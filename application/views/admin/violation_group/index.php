<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<style>
    #table-violation_group tr th:nth-child(5) {
        width: 25px;
    }

    #table-violation_group tr th:nth-child(7) {
        width: 60px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a href="<?= admin_url('violation_group/detail') ?>"
                   class="btn btn-info mright5 test pull-right H_action_button tnh-modal">
                    <?php echo _l('Thêm mới'); ?></a>
                <a href="<?= base_url('admin/violation_group/modal_excel_import') ?>"
                   class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <a href="<?= base_url('admin/violation_group/excel_export') ?>"
                   class="btn btn-info pull-right mright10 H_action_button">
                    <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('EXPORT EXCEL'); ?>
                </a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3 row">
                            <div class="form-group">
                                <?= lang('type', 'type') ?>
                                <select name="type_cost" id="type_cost" class="form-control selectpicker type_cost"
                                        data-none-selected-text="<?= lang('Chọn loại') ?>"
                                        data-placeholder="<?= lang('Chọn loại') ?>">
                                    <option value="0"></option>
                                    <?php foreach ($dtType as $key => $value) { ?>
                                        <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <table id="table-violation_group" class="table dt-tnh table-hover table-violation_group"
                               style="width: 100%;">
                            <thead>
                            <tr>
                                <th class="text-center"><?= lang('Mã Loại') ?></th>
                                <th class="text-center"><?= lang('Tên Loại') ?></th>
                                <th class="text-center"><?= lang('Mã Vi Phạm') ?></th>
                                <th class="text-center"><?= lang('Tên Vi Phạm') ?></th>
                                <th class="text-center"><?= lang('STT') ?></th>
                                <th class="text-center"><?= lang('Mô Tả') ?></th>
                                <th class="text-center"><?= lang('Tác vụ') ?></th>
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
<?php init_tail(); ?>
<script>
    $(function () {

        var fnserverparams = {
            'type_cost': '#type_cost'
        };
        var filterValue = {};
        for (var key in fnserverparams) {
            var elementId = fnserverparams[key];
            var element = document.querySelector(elementId);
            element.onchange = function () {
                filterValue[key] = $(fnserverparams[key]).val();
                oTable.draw('page');
            };
        }
        oTable = tnhInitDataTable('#table-violation_group', '<?= site_url('admin/violation_group/getViolationGroup') ?>', {
            'order': false,
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/violation_group/getViolationGroup') ?>',
                "type": "POST",
                "data": function (d) {
                    if (typeof (csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function (json) {
                    return json.aaData;
                }
            },
            "columnDefs": [],
        });
    });
</script>