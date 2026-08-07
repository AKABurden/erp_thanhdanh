<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .progressbar li:not(.initli) {
        /*min-width: 300px;*/
    }
    .progressbar li.active:not(.initli) i {
        color: green;
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
                <?php if ($this->preAddSuggestOutsource): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/suggest_outsource/detail') ?>" class="btn btn-info H_action_button">
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
                                        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>"
                                               id="start_date_search" class="start_date_search datepicker form-control"
                                               style="width: 100%;" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('end_date', 'end_date_search') ?>
                                        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>"
                                               id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-suggest-outsource" class="table dt-tnh table-suggest-outsource-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center">
                                            STT
<!--                                            <div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>-->
                                        </th>
                                        <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
                                        <th class="text-center"><?= lang('Loại') ?></th>
                                        <th class="text-center"><?= lang('Người Lập Kế Hoạch') ?></th>
                                        <th class="text-center"><?= lang('Trạng Thái') ?></th>
                                        <th class="text-center"><?= lang('Người Tạo') ?></th>
                                        <th class="text-center"><?= lang('Quy trình') ?></th>
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
    var tAPI;

    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    tAPI = oTable = tnhInitDataTable('#table-suggest-outsource',
        '<?= site_url('admin/suggest_outsource/getSuggestOutsource') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/suggest_outsource/getSuggestOutsource') ?>',
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
            url: site.base_url+'admin/suggest_outsource/agree',
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

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_outsource/exportExcel',
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


    // $('#table-suggest-outsource tbody').on('click', 'td .rows-child', function() {
    //     var tr = $(this).closest('tr');
    //     var row = tAPI.row(tr);
    //     if (row.child.isShown()) {
    //         $(this).removeClass('fa-caret-down');
    //         $(this).addClass('fa-caret-right');
    //         row.child.hide();
    //         tr.removeClass('shown');
    //     } else {
    //         // Open this row
    //         $(this).removeClass('fa-caret-right');
    //         $(this).addClass('fa-caret-down');
    //         row.child(loadItemsTasks(row.data())).show();
    //         tr.addClass('shown');
    //     }
    // });
    //
    // $('#table-suggest-outsource thead').on('click', '.rows-child-all', function() {
    //     if ($(this).hasClass('fa-caret-right')) {
    //         $(this).addClass('fa-caret-down');
    //         $(this).removeClass('fa-caret-right');
    //         var rows = $('td .rows-child');
    //         $.each(rows, function(index, value) {
    //             var tr = $(value).parents('tr');
    //             var row = tAPI.row(tr);
    //             $(value).removeClass('fa-caret-right');
    //             $(value).addClass('fa-caret-down');
    //             row.child(loadItemsTasks(row.data())).show();
    //             tr.addClass('shown');
    //         })
    //     } else {
    //         $(this).removeClass('fa-caret-down');
    //         $(this).addClass('fa-caret-right');
    //         var rows = $('td .rows-child');
    //         $.each(rows, function(index, value) {
    //             var tr = $(value).parents('tr');
    //             var row = tAPI.row(tr);
    //             $(value).removeClass('fa-caret-down');
    //             $(value).addClass('fa-caret-right');
    //             row.child.hide();
    //             tr.removeClass('shown');
    //         })
    //     }
    //
    // });
    //
    // function loadItemsTasks(cData) {
    //     if (typeof cData === "undefined" || cData == null || !cData) return '';
    //     cHtml = cData['detail_html'];
    //     return `<div>${cHtml}</div>`;
    // }

</script>