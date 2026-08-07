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
                                    <?= lang('Nhóm NPL', 'category_search') ?>
                                    <select name="category_search" id="category_search" data-placeholder="<?= lang('tnh_item_materials_category') ?>" class="modal-select2" style="width: 100%;">
                                        <option value=""></option>
                                        <?= recursiveCategoryItems() ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <?= lang('materials', 'materials_search') ?>
                                    <input type="text" name="materials_search" id="materials_search" style="width: 100%;" data-placeholder="<?= lang('materials') ?>" value="">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="table-responsive">
                                <table id="table-synthetic_material_warehouse" class="table dt-tnh table-synthetic_material_warehouse-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã NPL') ?></th>
                                        <th class="text-center"><?= lang('Tên NPL') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Thực Tế') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Xuất Kho') ?></th>
                                        <th class="text-center"><?= lang('Số Phiếu Xuất Kho') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Nhập Kho') ?></th>
                                        <th class="text-center"><?= lang('Số Phiếu Nhập Kho Kho') ?></th>
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
<div id="return_suppliers_data"></div>
<div id="view_adjusted_data"></div>
<div id="view_transfer_data"></div>
<div id="export_different_data"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $('#category_search').select2({'allowClear': true});
    ajaxSelectParamsCallback('#materials_search', 'admin/items/searchSelect2Materials', $('#materials_search')
        .val(), false, true);

    var oTable = '';

    var fnserverparams = {
        category_search: '#category_search',
        materials_search: '#materials_search',
    };
    oTable = tnhInitDataTable('#table-synthetic_material_warehouse',
        '<?= site_url('admin/reports_warehouse/getSyntheticMaterialWarehouse') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/reports_warehouse/getSyntheticMaterialWarehouse') ?>',
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
        '#category_search,#materials_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });

    function exportExcel() {
        category_search = $('#category_search').val();
        materials_search = $('#materials_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/reports_warehouse/exportExcelWarehouseMaterial',
            data: {
                csrf_token_name: hash,
                category_search: category_search,
                materials_search: materials_search,
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
    function view_transfer(id) {
        $('#view_transfer_data').html('');
        $.get(admin_url + 'transfer/transfer_data/' + id).done(function(response) {
            $('#view_transfer_data').html(response);
            $('#view_transfer').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_transfer', function() {
        $('#view_transfer_data').html('');
    });

    function view_return_suppliers(id = null, edit = false) {
        $('#return_suppliers_data').html('');
        $.get(admin_url + 'return_suppliers/int_return_suppliers_view/' + id).done(function(response) {
            $('#return_suppliers_data').html(response);
            $('#return_suppliers').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew('tblreturn_suppliers', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#return_suppliers', function() {
        $('#return_suppliers_data').html('');
        tAPI.draw('page');
    });

    function view_adjusted(id) {
        $('#view_adjusted_data').html('');
        $.get(admin_url + 'adjusted/adjusted_data/' + id).done(function(response) {
            $('#view_adjusted_data').html(response);
            $('#view_adjusted').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            changeRowNew_ch('tbladjusted', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_adjusted', function() {
        $('#view_adjusted_data').html('');
    });
    function view_export_different(id = null) {
        $('#export_different_data').html('');
        $.get(admin_url + 'export_different/int_export_different_view/' + id).done(function(response) {
            $('#export_different_data').html(response);
            $('#view_export_different').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_datepicker();
            // changeRowNew('tblexport_different', id);
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }
    $('body').on('hidden.bs.modal', '#view_export_different', function() {
        $('#export_different_data').html('');
        tAPI.draw('page');
    });
</script>