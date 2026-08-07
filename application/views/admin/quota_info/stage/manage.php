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
                                <?= lang('Công Đoạn', 'stage_search') ?>
                                <?php $stages = $this->db->get_where('tbl_stages', ['type_use' => 0])->result_array();?>
                                <select name="stage_search" data-live-search="true" data-none-selected-text="Chọn" id="stage_search" class="form-control selectpicker" tabindex="-98">
                                    <option></option>
                                    <?php foreach($stages as $key => $value) {?>
                                        <option value="<?=$value['id']?>" data-subtext="<?=$value['code']?>"><?=$value['name']?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <?= lang('materials', 'materials_search') ?>
                                <input type="text" name="materials_search" id="materials_search" style="width: 100%;" data-placeholder="<?= lang('materials') ?>" value="">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-list-data-stage" class="table dt-tnh table-list-data-stage" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Mã Công Đoạn</th>
                                            <th>Tên Công Đoạn</th>
<!--                                            <th>Định Mức BOM</th>-->
                                            <th>Mã NPL</th>
                                            <th>Tên NPL</th>
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
    init_selectpicker();
    ajaxSelectParamsCallback('#materials_search', 'admin/items/searchSelect2Materials', $('#materials_search').val(), false, true);
    $(document).ready(function() {
        var CustomersServerParams = {
            'materials_search': '[name="materials_search"]',
            'stage_search': '[name="stage_search"]',
        };
        oTable = initDataTableCustom('.table-list-data-stage', admin_url + 'quota_info/table_stage', [], [1,2,3,4,5], CustomersServerParams, ['0', 'asc']);
    })

    $(document).on('change',
        '#materials_search, #stage_search',
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
</script>