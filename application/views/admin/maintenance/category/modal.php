<div class="modal fade" id="modal_maintenance_category" tabindex="-1" role="dialog">
    <style>
        .select_100 button.dropdown-toggle{
            height: 100%;
        }
    </style>
    <div class="modal-dialog modal-lg">
		<?php echo form_open(admin_url('maintenance/create_category/' . (!empty($category->id) ? $category->id : '')), ['id' => 'form_category']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo !empty($title) ? _l($title) : ''; ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td style="width: 15%;">
                                <label for="code">Mã Hạng mục bảo trì</label>
                            </td>
                            <td style="width: 35%;">
                                <?php $value = !empty($category) ? $category->code : ''?>
                                <?= form_input('code', $value, 'id="code" class="form-control" placeholder="' . lang('Mã hạng mục') . '" required ') ?>
                            </td>


                        </tr>
                        <tr>
                            <td style="width: 15%;">
                                <label for="name">Tên hạng mục bảo trì</label>
                            </td>
                            <td style="width: 35%;">
								<?php $value = !empty($category) ? $category->name : ''?>
								<?= form_input('name', $value, 'id="name" class="form-control" placeholder="' . lang('Tên hạng mục') . '" required ') ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">
                                <label for="name">Máy móc</label>
                            </td>
                            <td style="width: 35%;" class="select_100">
                                <select id="id_machines" name="id_machines" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option></option>
									<?php if(!empty($machines)) {?>
										<?php foreach($machines as $key => $value) {?>
											<?php $selected = !empty($category->id_machines) && $value['id'] == $category->id_machines ? 'selected' : ''?>
                                            <option value="<?=$value['id']?>" <?=$selected?> data-content="<div>Mã máy móc: <?=$value['code']?></div><div>Tên máy móc: <?=$value['name']?></div>"><?=$value['name']?></option>
										<?php } ?>
									<?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">
                                <label for="type">Loại</label>
                            </td>
                            <td style="width: 35%;">
                                <select id="type" name="type" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <option></option>
                                    <?php if(!empty($type)) {?>
                                        <?php foreach($type as $key => $value) {?>
											<?php $selected = !empty($category) && $key == $category->type ? 'selected' : ''?>
                                            <option value="<?=$key?>" <?=$selected?>><?=$value?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
    init_selectpicker('refresh');
    $('#modal_maintenance_category').modal('show');
    appValidateForm($('#form_category'), {
        code: 'required',
        name: 'required',
        id_machines: 'required',
        type: 'required'
    }, ManageCategory);

    function ManageCategory(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function (data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                $('#modal_maintenance_category').modal('hide');
                if(typeof(TableData) != 'undefined') {
                    TableData.draw('page');
                }
            } else {
                $(form).find('button[type="submit"]').removeAttr('disabled');
            }
        })
        .fail(function (err) {
            alert_float('danger', err.responseText);
            $(form).find('button[type="submit"]').removeAttr('disabled');
        });
        return false;
    }
</script>