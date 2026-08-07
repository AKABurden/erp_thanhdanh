<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<style>
    #table-category_payslip tr th:nth-child(1) {
        width: 25px;
    }

    #table-category_payslip tr th:nth-child(4) {
        width: 60px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a href="<?= admin_url('category_payslip/detail') ?>"
                   class="btn btn-info mright5 test pull-right H_action_button tnh-modal">
                    <?php echo _l('Thêm mới'); ?></a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <table id="table-category_payslip" class="table dt-tnh table-hover table-category_payslip"
                               style="width: 100%;">
                            <thead>
                            <tr>
                                <th class="text-center"><?= lang('STT') ?></th>
                                <th class="text-center"><?= lang('Mã Loại') ?></th>
                                <th class="text-center"><?= lang('Tên Loại') ?></th>
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
        oTable = tnhInitDataTable('#table-category_payslip', '<?= site_url('admin/category_payslip/getCategoryPayslip') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/category_payslip/getCategoryPayslip') ?>',
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