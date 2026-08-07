<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <?php if (has_permission('depreciable_assets', '', 'export')) { ?>
                    <div class="line-sp"></div>
                    <a href="<?=admin_url('depreciable_assets/export_excel')?>"  target="_blank" class="btn btn-info mright5 test pull-right H_action_button">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('Xuất Excel'); ?>
                    </a>
                <?php } ?>
                <?php if (has_permission('depreciable_assets', '', 'create')) { ?>
                    <div class="line-sp"></div>
                    <a href="<?=admin_url('depreciable_assets/detail')?>" class="btn btn-info mright5 test pull-right H_action_button c_modal">
                        <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                        <?php echo _l('create_add_new'); ?>
                    </a>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li class="active">
                                        <a class="H_filter" data-id="all">
                                            <?= _l('leads_all') ?> (<b class="count_all">0</b>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="1">
                                            <?= _l('Tài Sản Còn Giá Trị Khấu Hao') ?> (<b class="count_1">0</b>)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="H_filter" data-id="2">
                                            <?= _l('Tài Sản Hết Giá Trị Khấu Hao') ?> (<b class="count_2">0</b>)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                        <div class="clearfix mtop20"></div>
                        <?php $table_data = array(
                                _l('#'),
                                _l('Mã Thiết Bị'),
                                _l('Tên Thiết Bị'),
                                _l('Tên Gọi Riêng Của Thiết Bị'),
                                _l('Thời Gian Bắt Đầu Sử Dụng'),
                                _l('Thời Gian Khấu Hao (Tháng)'),
                                _l('Số Ngày Còn Lại'),
                                _l('Giá Trị Tài Sản (VNĐ)'),
                                _l('Giá Trị Sử Dụng (VNĐ)'),
                                _l('Giá Trị còn lại (VNĐ)'),
                                _l('Ghi Chú'),
                                _l('ch_option')
                            );
                            render_datatable($table_data, 'depreciable_assets');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $('.H_filter').click(function (e) {
        var target = $(e.currentTarget);
        var value = target.attr('data-id');
        target.parent().parent().find('li').removeClass('active');
        target.parent().addClass('active');
        $('input[name="filterStatus"]').val(value);
        $('input[name="filterStatus"]').change();
    });
    var tAPI;
    $(function () {
        var CustomersServerParams = {
            'filterStatus': '[name="filterStatus"]',
        };
        tAPI = initDataTableCustom('.table-depreciable_assets', admin_url + 'depreciable_assets/table', [0], [0], CustomersServerParams, ['0', 'desc']);
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $(filterItem).on('change', function () {
                tAPI.draw('page');
            });
        });
        $('.table-depreciable_assets').on('draw.dt', function () {
            var invoiceReportsTable = $(this).DataTable();
            var sums = invoiceReportsTable.ajax.json().sums;
            var all = 0;
            $.each(sums, function (index, value) {
                $(`.count_${index}`).text(value);
                all+= parseFloat(value);
            })
            $(`.count_all`).text(all);
        });
    });

    $(document).on('click', '.delete-remind', function () {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function (response) {
                alert_float(response.alert_type, response.message);
                tAPI.draw('page');
            }, 'json');
        }
        return false;
    });
</script>
