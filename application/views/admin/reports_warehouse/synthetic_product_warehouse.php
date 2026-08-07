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
                                <div class="col-md-3">
                                    <?= lang('category', 'category_search') ?>
                                    <select name="category_search" id="category_search" data-placeholder="<?= lang('tnh_category_product') ?>" class="modal-select2" style="width: 100%;">
                                        <option value=""></option>
                                        <?= recursiveCategoryProducts() ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('products', 'products_search') ?>
                                    <input type="text" name="products_search" id="products_search" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic_product_warehouse" class="table dt-tnh table-synthetic_product_warehouse-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Brand') ?></th>
                                        <th class="text-center"><?= lang('Mã Thành Phẩm') ?></th>
                                        <th class="text-center"><?= lang('Tên Thành Phẩm') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Thực Tế') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Xuất Kho') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Nhập Kho') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Cuối') ?></th>
                                        <th class="text-center"><?= lang('Thời Gian Tồn Cho Phép') ?></th>
                                        <th class="text-center"><?= lang('Cảnh Báo Quá Hạn') ?></th>
                                        <th class="text-center"><?= lang('Ngày Cập Nhật Foso') ?></th>
                                        <th class="text-center"><?= lang('Ngày Điều Chỉnh') ?></th>
                                        <th class="text-center"><?= lang('Ngày Ngưng Sử Dụng') ?></th>

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
    $('#category_search').select2({'allowClear': true});
    ajaxSelectParamsCallback('#products_search', 'admin/products/searchProductsSelect2', $('#products_search').val(), false, true);

    var oTable = '';

    var fnserverparams = {
        category_search: '#category_search',
        products_search: '#products_search',
    };
    oTable = tnhInitDataTable('#table-synthetic_product_warehouse',
        '<?= site_url('admin/reports_warehouse/getSyntheticProductWarehouse') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/reports_warehouse/getSyntheticProductWarehouse') ?>',
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
        '#category_search,#products_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        category_search = $('#category_search').val();
        products_search = $('#products_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_warehouse/exportExcelWarehouseProduct',
            data: {
                csrf_token_name: hash,
                category_search: category_search,
                products_search: products_search,
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