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
                <?php if ($this->preAddSuggestPlanPurchase) : ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/suggest_plan_purchase/detail?type=' . $type . '') ?>" class="btn btn-info H_action_button">
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
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <input type="hidden" name="type" id="type" value="<?= $type ?>">
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
                            <div class="">
                                <table id="table-suggest-plan-purchase" class="table dt-tnh table-suggest-plan-purchase-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                            <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                            <th class="text-center"><?= lang('Người Lập Kế Thời') ?></th>
                                            <th class="text-center"><?= lang('Thời Gian Hoàn Thành') ?></th>
                                            <th class="text-center"><?= lang('Mã Nhóm Kế Hoạch') ?></th>
                                            <th class="text-center"><?= lang('Tổng Tiền') ?></th>
                                            <th class="text-center"><?= lang('Trạng thái') ?></th>
                                            <th class="text-center"><?= lang('Người tạo') ?></th>
                                            <th class="text-center"><?= lang('Tạo YCMH') ?></th>
                                            <th class="text-center"><?= lang('actions') ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="99"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="bold uppercase">Tổng tiền</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="grand_total text-right bold"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
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
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        type: '#type',
    };
    oTable = tnhInitDataTable('#table-suggest-plan-purchase',
        '<?= site_url('admin/suggest_plan_purchase/getSuggestPlanPurchases') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/suggest_plan_purchase/getSuggestPlanPurchases') ?>',
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
                    $('#table-suggest-plan-purchase tfoot tr td.grand_total').html('<div class="text-right">'+tnhFormatMoney(json.grand_total)+'</div>');
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {},
            // "columnDefs": [{
            //     "targets": 9,
            //     "name": 'actions',
            //     'visible': '<?= $type == 2 ? true : false ?>'
            // }, ],
        });


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
            url: site.base_url + 'admin/suggest_plan_purchase/agree',
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

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        <?php if ($type == 3) { ?>
            url = site.base_url + 'admin/suggest_plan_purchase/exportExcelNew';
        <?php } else { ?>
            url = site.base_url + 'admin/suggest_plan_purchase/exportExcel';
        <?php } ?>
        $.ajax({
            type: "POST",
            url: url,
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
                export_excel: 1,
                type: <?= $type ?>,
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

    function create_purchase(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            // $.get('<?= admin_url('suggest_plan_purchase/create_purchase/') ?>' + id, function(response) {
            //     alert_float(response.alert_type, response.message);
            //     tAPI.draw('page');
            // }, 'json');
            var dataPOST = {};
            dataPOST[csrf_token_name] = hash;
            $.ajax({
                type: "POST",
                url: site.base_url + 'admin/suggest_plan_purchase/create_purchase/' + id,
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
        return false;
    }
</script>