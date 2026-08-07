<?php init_head(); ?>
<style>
    .mce-edit-area {
        min-height:230px!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
        </div>
    </div>
	<?php echo form_open('admin/list_protocol/detail/' . (!empty($id) ? $id : ''), array('id' => 'form_detail_protocol')); ?>
    <div class="content ae-content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('info') ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                            <tbody>
                            <tr>
                                <td width="15%">
                                    <label for="name" class="control-label"><?= _l('c_name_list_protocol') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($list_protocol) ? $list_protocol->name : '' ?>
									<?= render_input('name', '', $value) ?>
                                </td>
                                <td style="width: 20%;"></td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="content" class="control-label"><?= _l('cong_content') ?></label>
                                </td>
                                <td>
									<?php $value = !empty($list_protocol) ? $list_protocol->content : '' ?>
									<?php echo render_textarea('content', '', $value, array('rows' => 10, 'data-entities-encode' => 'true'), array(), '', 'tinymce'); ?>
                                </td>
                                <td>
                                    <p>Tên khách hàng<span class="pull-right"><a class="add_merge_field pointer">{fullname}</a></span></p>
                                    <p>Chức vụ<span class="pull-right"><a class="add_merge_field pointer">{name_role}</a></span></p>
                                    <p>Phòng ban<span class="pull-right"><a class="add_merge_field pointer">{name_departments}</a></span></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <input type="hidden" name="add" id="" class="form-control" value="1">
            <button type="submit" class="btn btn-info add-order">
				<?php echo _l('submit'); ?>
            </button>
        </div>
    </div>
	<?php echo form_close(); ?>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script>
    appValidateForm($('#form_detail_protocol'), {
        name: 'required'
    });

    $('.add_merge_field').on('click', function(e) {
        e.preventDefault();
        tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).text());
    });
</script>
