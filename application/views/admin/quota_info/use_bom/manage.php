<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<style>
    .bg-title {
        font-weight: 600!important;
        background-color: rgba(247, 244, 155, 0.6)!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="mbot20">
                            <div class="col-md-3">
                                <?= lang('materials', 'materials_search') ?>
                                <input type="text" name="materials_search" id="materials_search" style="width: 100%;" data-placeholder="<?= lang('materials') ?>" value="">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="clearfix"></div>

                            <div class="">

                                <table id="table-list-data-use-bom" class="table dt-tnh table-list-data-use-bom" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Mã NPL</th>
                                            <th>Tên NPL</th>
                                            <th>Mã Công Đoạn</th>
                                            <th>Tên Công Đoạn</th>
                                            <th>Định Mức Tiêu Hao</th>
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
    ajaxSelectParamsCallback('#materials_search', 'admin/items/searchSelect2Materials', $('#materials_search').val(), false, true);
    $(document).ready(function() {
        var CustomersServerParams = {
            'materials_search': '[name="materials_search"]',
        };
        oTable = initDataTableCustom('.table-list-data-use-bom', admin_url + 'quota_info/table_use_bom', [], [1,2,3,4,5], CustomersServerParams, ['0', 'asc']);
    })

    $(document).on('change', '#materials_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
</script>