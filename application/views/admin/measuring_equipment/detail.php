
<div id="modal_measuring_equipment" class="modal fade" role="dialog">
    <?php echo form_open(
        admin_url('measuring_equipment/detail/' . (!empty($measuring_equipment) ? $measuring_equipment->id : '')),
        array('id' => 'form_measuring_equipment', 'enctype' => 'multipart/form-data')
    ); ?>
    <div class="modal-dialog modal-lg" style="min-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= !empty($title) ? $title : ''; ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php $value = !empty($measuring_equipment) ? $measuring_equipment->code : ''?>
                        <?php echo render_input('code', 'Mã Thiết Bị/Công Việc', $value)?>
                    </div>
                    <div class="col-md-4">
                        <?php $value = !empty($measuring_equipment) ? $measuring_equipment->name : ''?>
                        <?php echo render_input('name', 'Tên Thiết Bị/Công Việc', $value)?>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('Trạng Thái', 'status') ?>
                            <select name="status" id="status" data-none-selected-text="<?= lang('tnh_status') ?>" class="form-control status" required="required">
                                <option value=""></option>
                                <?php foreach (status_machine_new() as $key => $value) : ?>
                                    <option <?= (!empty($measuring_equipment) && $measuring_equipment->status == $key) ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <?php $value = !empty($measuring_equipment) ? $measuring_equipment->product_in_month : ''?>
                        <?php echo render_input('product_in_month', 'Định Mức Năng Suất/Tháng', $value)?>
                    </div>
                    <div class="col-md-4">
                        <?php $value = !empty($measuring_equipment) ? $measuring_equipment->stage_id : ''?>
                        <?php echo render_select('stage_id', (!empty($list_stage) ? $list_stage : []), ['id', 'code', 'name'], 'Nhóm Công Đoạn', $value)?>
                    </div>
                    <div class="clearfix"></div>
                    <hr/>
                    <?php $timeID = time();?>
                    <div class="col-md-6">
                        <div class="form-group">
							<?php $value = !empty($measuring_equipment) ? $measuring_equipment->specifications : ''?>
                            <label for="specifications_<?=$timeID?>" class="control-label">Thông Số Kỹ Thuật</label>
                            <textarea id="specifications_<?=$timeID?>" name="specifications" class="form-control tinymce" rows="4"><?=$value?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
							<?php $value = !empty($measuring_equipment) ? $measuring_equipment->note : ''?>
                            <label for="note_<?=$timeID?>" class="control-label">Ghi Chú</label>
                            <textarea id="note_<?=$timeID?>" name="note" class="form-controltinymce" rows="4"><?=$value?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add"><?= _l('tnh_edit') ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
    $('#modal_measuring_equipment').modal('show')
    
    $(function() {
        init_datepicker();
        init_selectpicker();
        appValidateForm($('#form_measuring_equipment'), {
            code: 'required',
            name: 'required',
            category_machine_id: 'required',
            status: 'required'
        }, from_manage);

        function from_manage(form) {
            var action = $('#form_measuring_equipment').attr('action');
            $('.add').attr('disabled', 'disabled');
            tinymce.get('note_<?=$timeID?>').save();
            tinymce.get('specifications_<?=$timeID?>').save();
            var formParams = $('#form_measuring_equipment').serializeArray();
            var formData = new FormData();
            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                    url: action,
                    type: 'POST',
                    dataType: 'JSON',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                }).done(function(data) {
                    alert_float(data.alert_type, data.message);
                    if (data.success) {
                        if (typeof oTable != 'undefined') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    console.log("error");
                });

            return false;
        }

        $('.status').selectpicker();
        $('.standard').selectpicker();
        $('.category_machine_id').selectpicker();
        init_editor('#note_<?=$timeID?>');
        init_editor('#specifications_<?=$timeID?>');

    })
</script>