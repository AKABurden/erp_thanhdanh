<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-procadure_detail_4 tbody tr td:nth-child(3) {
        white-space: unset;
        width: 300px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="pull-right mright5 H_border">
				<?php if(has_permission('category_maintenance', '', 'create')) {?>
                    <a href="<?= admin_url('maintenance/create_category') ?>" class="btn btn-info H_action_button c_modal">
						<?php echo _l('create_add_new'); ?>
                    </a>
				<?php } ?>
                <?php if(has_permission('category_maintenance', '', 'import')) {?>
                    <a href="<?= admin_url('maintenance/modal_excel_category') ?>" class="btn btn-info H_action_button c_modal">
						<?php echo _l('c_import_excel'); ?>
                    </a>
				<?php } ?>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="tab-content" id="tab_content_procadure">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php echo render_input('category_search', 'Mã hạng mục/Tên hạng mục')?>
                                </div>
                                <div class="col-md-3">
                                    <label for="type_category" class="control-label">Loại</label>
                                    <select id="type_category" name="type_category" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                        <option></option>
										<?php if(!empty($type)) {?>
											<?php foreach($type as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
											<?php } ?>
										<?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
							<?php render_datatable(array(
								_l('#'),
								_l('Mã hạng mục bảo trì'),
								_l('Tên hạng mục bảo trì'),
								_l('Mã máy móc'),
								_l('Tên máy móc'),
								_l('Loại'),
								_l('options'),
							), 'maintenance_category'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var CustomersServerParams = {
        'category_search': '[name="category_search"]',
        'type_category': '[name="type_category"]',
    };
    var TableData;
    $(function () {
        TableData = initDataTable('.table-maintenance_category', admin_url + 'maintenance/table_category', [0], [0], CustomersServerParams, [0, 'desc']);
        $.each(CustomersServerParams, function (filterIndex, filterItem) {
            $('' + filterItem).on('change', function () {
                TableData.draw('page');
            });
        });
    });
    $('body').on('click', '._delete_category', function () {
        var id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa hạng mục này?')) {
            $.get(admin_url + 'maintenance/delete_category/' + id, function (result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                TableData.draw('page');
                return false;
            }).fail(function (error) {
                alert_float('danger', error.responseText);
            });
        }
        return false;
    })
</script>
</body>
</html>
