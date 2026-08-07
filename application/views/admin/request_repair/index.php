<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-suggest-rating-process tr th:nth-child(1) {
        width: 40px;
    }

    #table-suggest-rating-machines tr th:nth-child(2) {
        width: 100px;
    }

    #table-suggest-rating-machines tr th:nth-child(7) {
        width: 120px;
    }
</style>
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
                <?php if ($this->preAddRequestRepair) : ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/request_repair/detail') ?>" class=" tnh-modal btn btn-info H_action_button">
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
                                <div class="table-responsive">
                                    <table id="table-suggest-rating-machines" class="table dt-tnh table-suggest-rating-machines-new" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div></th>
                                                <th class="text-center"><?= lang('Mã Số Phiếu') ?></th>
                                                <th class="text-center"><?= lang('Ngày Lập Phiếu') ?></th>
<!--                                                <th class="text-center">--><?php //= lang('Đơn vị sửa chữa') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Số lượng') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Đơn giá sửa chữa') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Thành tiền') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Nhóm bảo dưỡng') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Bộ Phận bảo dưỡng') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Chi Tiết bảo dưỡng') ?><!--</th>-->
                                                <th class="text-center"><?= lang('Nhóm Thiết Bị') ?></th>
                                                <th class="text-center"><?= lang('Mã Thiết Bị') ?></th>
                                                <th class="text-center"><?= lang('Tên Thiết Bị') ?></th>
                                                <th class="text-center"><?= lang('Loại Yêu Cầu') ?></th>
<!--                                                <th class="text-center">--><?php //= lang('Kết Quả') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Biên Bản Nghiệm Thu') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Hoàn Thành Thanh Toán') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Báo Cáo Không Phù Hợp') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Phiếu công việc') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('Đánh Giá Đơn Vị Sửa Chữa') ?><!--</th>-->
                                                <th class="text-center"><?= lang('Người Đề Xuất') ?></th>
                                                <th class="text-center"><?= lang('Người duyệt') ?></th>
                                                <th class="text-center"><?= lang('Trạng thái hoàn thành') ?></th>
<!--                                                <th class="text-center">--><?php //= lang('Tiêu Chuẩn/ Quy Định') ?><!--</th>-->
<!--                                                <th class="text-center">--><?php //= lang('tnh_suppliers') ?><!--</th>-->
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
    oTable = tnhInitDataTable('#table-suggest-rating-machines',
        '<?= site_url('admin/request_repair/getRequestRepair') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            scrollX:true,
            "ajax": {
                "url": '<?= site_url('admin/request_repair/getRequestRepair') ?>',
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

    function agree(_this, suggest_id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_id'] = suggest_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/request_repair/agree',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    //if(status == 1) {
                    //    new_task(admin_url + `tasks/task?suggest_id=${suggest_id}&category_recommended_id=<?php //= id_category_request_repair ?? 40?>//`);
                    //}
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

    function agreeFinish(_this, suggest_id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['suggest_id'] = suggest_id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/request_repair/agreeFinish',
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
            url: site.base_url + 'admin/request_repair/exportExcel',
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



    $('#table-suggest-rating-machines tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
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
            tr.addClass('shown');
        }
    });

    $('#table-suggest-rating-machines thead').on('click', '.rows-child-all', function() {
        if ($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = oTable.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsTasks(row.data())).show();
                tr.addClass('shown');
            })
        } else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = oTable.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }

    });

    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData['detail_html'];
        return `<div>${cHtml}</div>`;
    }


    /* --- Helper Functions --- */
    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function unformat_number(number) {
        var _number = 0;
        if (number) {
            _number = number.toString().replace(/[^\d\.\-]/g, "");
        }
        return _number;
    };

    function formatCurrency(input, blur) {
        var input_val = input.val();
        if (input_val === "") { return; }

        if (input_val.indexOf(".") >= 0) {
            var decimal_pos = input_val.indexOf(".");
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);
            left_side = formatNumber(left_side.replace(/\D/g, ""));
            right_side = formatNumber(right_side.replace(/\D/g, ""));
            input_val = left_side + "." + right_side;
        } else {
            input_val = formatNumber(input_val.replace(/\D/g, ""));
        }

        input.val(input_val);
    }
</script>