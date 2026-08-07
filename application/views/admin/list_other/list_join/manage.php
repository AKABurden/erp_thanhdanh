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
                        <a href="<?= base_url('admin/list_other/detail_join/' . $type) ?>" class=" c_modal btn btn-info H_action_button">
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
                                <table id="table-list-data-join" class="table dt-tnh table-list-data-join" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <?php foreach($name_colums as $key => $value) {?>
                                                <th class="text-center"><?=$value?></th>
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
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';

    $(document).ready(function() {
        var CustomersServerParams = {
            'start_date_search': '[name="start_date_search"]',
            'end_date_search': '[name="end_date_search"]',
        };
        oTable = initDataTableCustom('.table-list-data-join', admin_url + 'list_other/table_join/<?= $type ?>', [0], [0], CustomersServerParams, ['0', 'desc']);
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
</script>