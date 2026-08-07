<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right hide" href="javascript:void(0)">Xuất Excel</a>
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
                                <table id="table-suggest-item_quaity-evaluate" class="table dt-tnh table-suggest-item_quaity-evaluate" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="text-center"><?= lang('Mã Số Phiếu đánh giá') ?></th>
                                            <th class="text-center"><?= lang('Mã Số Phiếu YCKT') ?></th>
                                            <th class="text-center"><?= lang('Ngày Kiểm Tra') ?></th>
                                            <th class="text-center"><?= lang($type_object['name_po']) ?></th>
                                            <th class="text-center"><?= lang($type_object['name_object']) ?></th>
                                            <th class="text-center"><?= lang(($type == 'products' ? 'Sản Phẩm' : 'Nguyên Phụ iệu')) ?></th>
                                            <th class="text-center"><?= lang('Phiếu công việc') ?></th>
                                            <th class="text-center"><?= lang('Ghi chú') ?></th>
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

    $(document).ready(function() {
        var CustomersServerParams = {
            'start_date_search': '[name="start_date_search"]',
            'end_date_search': '[name="end_date_search"]',
        };
        oTable = initDataTableCustom('.table-suggest-item_quaity-evaluate', admin_url + 'suggest_test_item_quality/table_evaluate/<?=$type?>', [0], [0], CustomersServerParams, <?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'desc'))); ?>);
    });


    $('.table-suggest-item_quaity-evaluate tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = tAPI.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadItemsTasks(row.data())).show();
            $('.table-chid').parents('td').attr('style', 'background: cornsilk;')
            tr.addClass('shown');
        }
    });
    $('.table-suggest-item_quaity-evaluate thead').on('click', '.rows-child-all', function() {
        if($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = tAPI.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsTasks(row.data())).show();
                $('.table-chid').parents('td').attr('style', 'background: cornsilk;')
                tr.addClass('shown');
            })
        }
        else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = tAPI.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }
    
    });
    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData[9];
        return `<div>${cHtml}</div>`;
    }




    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    // function agree(_this, id, status) {
    //     var dataPOST = {};
    //     dataPOST[csrf_token_name] = hash;
    //     dataPOST['id'] = id;
    //     dataPOST['status'] = status;
    //
    //     $(_this).attr('disabled', 'disabled');
    //     $('.po').popover('hide');
    //
    //     $.ajax({
    //         type: "POST",
    //         url: site.base_url+'admin/suggest_test_item_quality/agree',
    //         data: dataPOST,
    //         dataType: "json",
    //         success: function (response) {
    //             alert_float(response.alert_type, response.message);
    //             if (response.success && typeof oTable !== 'undefined') {
    //                 oTable.draw('page');
    //             }
    //         },
    //         error: function (xhr, status, error) {
    //             $(_this).removeAttr('disabled');
    //         },
    //     });
    //
    // }

    function exportExcel() {
        start_date_search = $('#start_date_search').val();
        end_date_search = $('#end_date_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/suggest_test_item_quality/exportExcel',
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