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
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-moderation_order" class="table dt-tnh table-moderation_order-new" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('STT') ?></th>
                                            <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                            <th class="text-center"><?= lang('Mã Phiếu Yêu Cầu') ?></th>
                                            <th class="text-center"><?= lang('Mã thành phẩm') ?></th>
                                            <th class="text-center"><?= lang('Tên thành phẩm') ?></th>
                                            <th class="text-center"><?= lang('Tên Nhóm SP') ?></th>
                                            <th class="text-center"><?= lang('Tên Chủng Loại') ?></th>
                                            <th class="text-center"><?= lang('Tên Brand') ?></th>
                                            <th class="text-center"><?= lang('Mã Khách Hàng') ?></th>
                                            <th class="text-center"><?= lang('Tên Khách Hàng') ?></th>
                                            <th class="text-center"><?= lang('Đường Link') ?></th>
                                            <th class="text-center"><?= lang('Hình Ảnh SP') ?></th>
                                            <?php foreach (getListColumTable() as $key => $value){ ?>
                                                <th class="text-center" style="min-width: 100px"><?= lang($value['name']) ?></th>
                                            <?php } ?>
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
    oTable = tnhInitDataTable('#table-moderation_order',
        '<?= site_url('admin/moderation_order/getModerationOrder') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/moderation_order/getModerationOrder') ?>',
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

    $('#table-moderation_order').on('draw.dt', function(event) {
        init_datepicker();
    });

    function updateDate(_this, id,name) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['_value'] = $(_this).val();
        dataPOST['id'] = id;
        dataPOST['name'] = name;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_order/updateDate',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result == 1) {
                    alert_float('success', response.message);
                    oTable.draw('page');
                } else if (response.result == 0) {
                    alert_float('danger', response.message);
                    oTable.draw('page');
                }
            }
        });
    }
    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

   

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/moderation_order/exportExcel',
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