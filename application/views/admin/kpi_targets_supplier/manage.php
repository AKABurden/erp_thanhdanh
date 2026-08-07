<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .fraction-group {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .fraction-main {
        font-size: 14px;
        font-weight: 600;
        color: #527fe9;
        line-height: 1;
    }

    .fraction-separator {
        width: 1.5px;
        height: 24px;
        background-color: #cbd5e1;
        transform: rotate(20deg);
    }

    .fraction-sub {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .fraction-top {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.1;
    }

    .fraction-bottom {
        font-size: 14px;
        font-weight: 500;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 1px;
        margin-top: 1px;
        line-height: 1.1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?? '' ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <a href="<?= admin_url('kpi_targets_supplier/modal_excel_import') ?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                    Import Excel
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row" style="margin-bottom:5px">
                            <div class="col-md-3">
                                <label for="year_search">Năm</label>
                                <select class="year_search" id="year_search" name="year_search" style="width: 100%">
                                    <?php foreach (getYear() as $key => $value) { ?>
                                        <option <?= date('Y') == $value ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value="" />
                        <div class="clearfix mtop20"></div>
                        <?php $table_data = array(
                            'STT',
                            'Mã Nhà Cung Cấp',
                            'Tên Nhà Cung Cấp',
                            'Nhóm Nhà Cung Cấp',
                            'Số báo giá nhận',
                            'Báo giá đã duyệt / <span class="cYear">' . date('Y') . '</span>',
                            'Báo giá chưa duyệt / <span class="cYear">' . date('Y') . '</span>',
                            'Số đơn hàng / <span class="cYear">' . date('Y') . '</span>',
                            'Giao hàng đúng hạn / <span class="cYear">' . date('Y') . '</span>',
                            'Giao hàng trễ / <span class="cYear">' . date('Y') . '</span>',
                            'Số lần lỗi chất lượng / <span class="cYear">' . date('Y') . '</span>',
                            'Số Lần complain / <span class="cYear">' . date('Y') . '</span>',
                            'Mẫu lần 1',
                            'Mẫu lần 2',
                            'Điểm cộng',
                            'Điểm trừ',
                            'Tổng điểm',
                            'Trạng thái Nhà cung cấp',
                            'Hành Động Xử Lý',
                            'Thao tác',
                        );
                        render_datatable($table_data, 'kpi_targets_supplier');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<div id="view_other_payslips_coupon"></div>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $('.H_filter').click(function(e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    $("#year_search").select2();

    $(document).on('change',
        '#year_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
            $('.cYear').text($(this).val());
        });
    var filterList = {
        'filterStatus': '[name="filterStatus"]',
        'year_search': '[name="year_search"]',
    };
    var oTable;
    $(function() {
        oTable = initDataTable('.table-kpi_targets_supplier', admin_url + 'kpi_targets_supplier/table', [1], [4, 5, 6, 7, 8, 9], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter) {
        $(filter).on('change', function(e) {
            if ($('.table-kpi_targets_supplier').hasClass('dataTable')) {
                $('.table-kpi_targets_supplier').DataTable().ajax.reload();
            }
        })
    })


    function exportExcel() {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/kpi_targets_supplier/export_excel',
            data: {
                csrf_token_name: hash,
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