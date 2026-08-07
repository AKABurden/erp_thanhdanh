<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right mleft5" href="javascript:void(0)">Xuất Excel</a>
                <?php if ($this->preAdd): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/quote_shipping_unit/detail/') ?>" class=" c_modal btn btn-info H_action_button">
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
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-quote_shipping_unit" class="table dt-tnh table-quote_shipping_unit" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center"><?= lang('#') ?></th>
                                            <th class="text-center"><?= lang('Ngày') ?></th>
                                            <th class="text-center"><?= lang('Mã Nhà Cung Cấp') ?></th>
                                            <th class="text-center"><?= lang('Tên Nhà Cung Cấp') ?></th>
                                            <th class="text-center"><?= lang('Mã Chuyến') ?></th>
                                            <th class="text-center"><?= lang('Tên Chuyến') ?></th>
                                            <th class="text-center"><?= lang('Đơn Vị Tính') ?></th>
                                            <th class="text-center"><?= lang('Đơn Giá') ?></th>
                                            <th class="text-center"><?= lang('Đơn Vị Tiền Tệ') ?></th>
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
<!--<script type="text/javascript" src="--><?php //= js('datatables/jquery.dataTables.min.js') ?><!--"></script>-->
<!--<script type="text/javascript" src="--><?php //= js('datatables/dataTables.fixedColumns.min.js') ?><!--"></script>-->
<script>
    var oTable = '';

    $(document).ready(function() {
        var CustomersServerParams = {
            'start_date_search': '[name="start_date_search"]',
            'end_date_search': '[name="end_date_search"]',
        };
        oTable = initDataTableCustom('.table-quote_shipping_unit', admin_url + 'quote_shipping_unit/table', [0], [0], CustomersServerParams, ['0', 'desc']);
    })

    $(document).on('change',
        '#end_date_search,#start_date_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    $('body').on('click', '.c_delete', function () {
        if(confirm('Dữ liệu xóa sẽ không thể khôi phục!')) {
            var href = $(this).attr('href');
            var id = $(this).attr('data-id');
            var data = {id: id};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(href, data, function (result) {
                result = JSON.parse(result);
                if (result.success) {
                    oTable.draw("page")
                }
                alert_float(result.alert_type, result.message);
                return false;
            })
        }
        return false;
    })

    function agree(_this, id, status) {
        var dataPOST = {};
        dataPOST[csrf_token_name] = hash;
        dataPOST['id'] = id;
        dataPOST['status'] = status;

        $(_this).attr('disabled', 'disabled');
        $('.po').popover('hide');

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/quote_shipping_unit/update_status',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                alert_float(response.alert_type, response.message);
                if (response.success) {
                    if (typeof oTable !== 'undefined') {
                        oTable.draw('page');
                    }
                }
            },
            error: function(xhr, status, error) {
                $(_this).removeAttr('disabled');
            },
        });
    }

    function exportExcel() {
        // groups_ch = $('[name="groups_ch"]').val();
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/quote_shipping_unit/exportExcel',
            data: {
                csrf_token_name: hash,
                // groups_ch: groups_ch,
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