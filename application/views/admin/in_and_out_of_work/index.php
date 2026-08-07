<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-suggest-rating-process tr th:nth-child(1) {
        width: 40px;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
<!--                <a href="--><?php //=admin_url('in_and_out_of_work/import')?><!--" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">-->
<!--                    Import Excel-->
<!--                </a>-->
                <?php if ($this->preAddInandoutofwork) : ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/in_and_out_of_work/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
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
                                <div class="">
                                    <table id="table-in_and_out_of_work" class="table dt-tnh table-in_and_out_of_work-new" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><?= lang('STT') ?></th>
                                                <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                                <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                                <th class="text-center"><?= lang('Mã NV') ?></th>
                                                <th class="text-center"><?= lang('Tên Nhân Viên') ?></th>
                                                <th class="text-center"><?= lang('Vị Trí') ?></th>
                                                <th class="text-center"><?= lang('Chức Vụ') ?></th>
                                                <th class="text-center"><?= lang('Lý Do Ra Vào Cổng') ?></th>
                                                <th class="text-center"><?= lang('Số Điện Thoại Liên Hệ') ?></th>
                                                <th class="text-center"><?= lang('Thời Gian Ra Cổng') ?></th>
                                                <th class="text-center"><?= lang('Thời Gian Vào Cổng') ?></th>
                                                <th class="text-center"><?= lang('Tiến độ') ?></th>
                                                <th class="text-center"><?= lang('Tổng Đạt/Không') ?></th>
                                                <th class="text-center"><?= lang('Mã Phiếu Báo Cáo') ?></th>
                                                <th class="text-center"><?= lang('Người Duyệt') ?></th>
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
    oTable = tnhInitDataTable('#table-in_and_out_of_work',
        '<?= site_url('admin/in_and_out_of_work/getInandoutofwork') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/in_and_out_of_work/getInandoutofwork') ?>',
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


    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function agree(_this, suggest_purchase_id, status, ) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_purchase_id'] = suggest_purchase_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/in_and_out_of_work/agree',
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

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/in_and_out_of_work/exportExcel',
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