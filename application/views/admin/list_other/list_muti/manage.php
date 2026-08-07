<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <?php if ($this->preAdd): ?>
                    <div class="pull-right mright5 H_border">
                        <?php if (!empty($list_data)) { ?>
                            <?php foreach ($list_data as $key => $value) { ?>
                                <a href="<?= base_url('admin/list_other/detail_muti/' . $type . '/' . $key) ?>" class=" c_modal btn btn-info H_action_button modal_add modal_<?=$key?> <?=($default != $key ? 'hide' : '')?>">
                                    <?php echo _l('add'); ?>
                                </a>
                            <?php } ?>
                        <?php } ?>
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
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <?php if (!empty($list_data)) { ?>
                                        <?php foreach ($list_data as $key => $value) { ?>
                                            <li class="<?=$key == $default ? 'active' : ''?>">
                                                <a class="H_filter" data-id="<?= $key ?>">
                                                    <?= !empty($value['name_table']) ? $value['name_table'] : '' ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="filterType" id="filterType" value="<?=$default?>">
                            <div class="">
                                <table id="table-list-data-muti" class="table dt-tnh table-list-data-muti" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <?php foreach($list_data[$default]['colums_th'] as $key => $value) {?>
                                                <th class="text-center colums-type-<?=$key?>"><?=$value?></th>
											<?php } ?>
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
    var ListnameColums = <?=json_encode($list_data);?>;
    $('body').on('click', '.H_filter', function(e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        var type = $(this).attr('data-id');
        $('input[name="filterType"]').val(type).trigger('change');
        if(typeof ListnameColums[type] != 'undefined') {
            $.each(ListnameColums[type]['colums_th'], function(index, value) {
                $(`.colums-type-${index}`).text(value);
            })
        }
        $('.modal_add').addClass('hide');
        $(`.modal_${type}`).removeClass('hide');
        // tAPI.draw('page');
    });
    $(document).ready(function() {
        var CustomersServerParams = {
            'start_date_search': '[name="start_date_search"]',
            'end_date_search': '[name="end_date_search"]',
            'filterType': '[name="filterType"]',
        };
        oTable = initDataTableCustom('.table-list-data-muti', admin_url + 'list_other/table_muti/<?= $type ?>', [0], [0], CustomersServerParams, ['0', 'desc']);
    })

    $(document).on('change',
        '#end_date_search,#start_date_search,#filterType',
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