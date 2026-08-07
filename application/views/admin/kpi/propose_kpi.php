<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-category-kpi tr td:nth-child(1) {
        width: 80px;
        white-space: unset;
        text-align: center;
    }
    #table-category-kpi tr td:nth-child(5) {
        width: 150px;
        white-space: unset;
        text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <?php if ($this->perAddProposeKpi): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/kpi/detail_propose_kpi') ?>" class=" tnh-modal btn btn-info H_action_button">
                            <?php echo _l('Tạo mới'); ?>
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
                                <table id="table-suggest-kpi" class="table dt-tnh table-suggest-kpi" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Số Phiếu Đề Xuất') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Số Phiếu Yêu Cầu Đánh Giá') ?></th>
                                        <th class="text-center"><?= lang('Người Lập Đề Xuất') ?></th>
                                        <th class="text-center"><?= lang('Mã Vị Trí Đánh Giá') ?></th>
                                        <th class="text-center"><?= lang('Trạng Thái') ?></th>
                                        <th class="text-center"><?= lang('Người Tạo') ?></th>
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
    var oTable = '';

    var fnserverparams = {
    };
    oTable = tnhInitDataTable('#table-suggest-kpi',
        '<?= site_url('admin/kpi/getSuggestKpi') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/kpi/getSuggestKpi') ?>',
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
            url: site.base_url+'admin/kpi/agreeSuggestKpi',
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
</script>