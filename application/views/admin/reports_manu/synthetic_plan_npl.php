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
                                <table id="table-synthetic_plan_npl" class="table dt-tnh table-synthetic_plan_npl-new" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã Phiếu Yêu Cầu Kế Hoạch NPL') ?></th>
                                        <th class="text-center"><?= lang('Nhóm NPL') ?></th>
                                        <th class="text-center"><?= lang('Mã NPL') ?></th>
                                        <th class="text-center"><?= lang('Tên NPL') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Cho Phép') ?></th>
                                        <th class="text-center"><?= lang('Ngày Lập Kế Hoạch') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Thực Tế') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Cần Mua') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Xuất') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Xuất') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Nhập') ?></th>
                                        <th class="text-center"><?= lang('Phiếu Nhập') ?></th>
                                        <th class="text-center"><?= lang('Số Lượng Tồn Cuối') ?></th>
                                        <th class="text-center"><?= lang('Forcast Vượt Kế Hoạch') ?></th>
                                        <th class="text-center"><?= lang('Mức Cảnh Báo Tồn') ?></th>
                                        <th class="text-center"><?= lang('YC Mua Vượt') ?></th>
                                        <th class="text-center"><?= lang('Đề Xuất Mua Vượt') ?></th>
                                        <th class="text-center"><?= lang('PO') ?></th>
                                        <th class="text-center"><?= lang('Chênh Lệch Kế Hoạch') ?></th>
                                        <th class="text-center"><?= lang('Đề Xuất Điều Chỉnh Kế Hoạch') ?></th>
                                        <th class="text-center"><?= lang('Ngày Cập Nhật') ?></th>

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
    ajaxSelectParamsCallback('#materials_search', 'admin/items/searchSelect2Materials', $('#materials_search')
        .val(), false, true);

    var oTable = '';

    var fnserverparams = {
        category_search: '#category_search',
        materials_search: '#materials_search',
    };
    oTable = tnhInitDataTable('#table-synthetic_plan_npl',
        '<?= site_url('admin/reports_manu/getSyntheticPlanNPL') ?>', {
            'order': [
                [0, 'desc']
            ],
            scrollX: true,
            "ajax": {
                "url": '<?= site_url('admin/reports_manu/getSyntheticPlanNPL') ?>',
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
            url: site.base_url + 'admin/reports_manu/exportExcelSyntheticPlanNpl',
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
</script>