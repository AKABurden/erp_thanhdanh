



<div id="category_test_item_quality_modal" class="modal fade" role="dialog">
    <?php echo form_open('admin/categories/detail_category_test_item_quality/' . (!empty($item_quality['id']) ? $item_quality['id'] : ('?type=' . $type.'&type_event=' . $type_event)), array('id'=>'from-item_quality')); ?>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= (!empty($title) ? $title : '' ) ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('code', 'code') ?>
                            <?php echo form_input('code', (!empty($item_quality['code']) ? $item_quality['code'] : ''), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Hạng mục kiểm tra', 'name') ?>
                            <?php echo form_input('name', (!empty($item_quality['name']) ? $item_quality['name'] : ''), 'placeholder="'.lang('Hạng mục kiểm tra').'" id="name" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tiêu Chuẩn', 'standard') ?>
                            <?php echo form_input('standard', (!empty($item_quality['standard']) ? $item_quality['standard'] : ''), 'placeholder="'.lang('Tiêu chuẩn').'" id="standard" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Công cụ', 'tools') ?>
                            <?php echo form_input('tools', (!empty($item_quality['tools']) ? $item_quality['tools'] : ''), 'placeholder="'.lang('Công cụ').'" id="tools" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
							<?= lang('tnh_constitutive', 'constitutive') ?>
							<?php echo form_textarea('constitutive', (!empty($item_quality['constitutive']) ? $item_quality['constitutive'] : ''), 'placeholder="'.lang('tnh_constitutive').'" id="constitutive" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('note', 'note') ?>
                            <?php echo form_textarea('note', (!empty($item_quality['note']) ? $item_quality['note'] : ''), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                <button type="submit" class="btn btn-primary add"><?=(!empty($item_quality) ? _l('tnh_edit') : _l('Thêm mới'))?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
    $(function(){
        $('#category_test_item_quality_modal').modal('show');
       	appValidateForm($('#from-item_quality'), {
           code: 'required',
           name: 'required'
        }, addFrom);
        
        function addFrom(form) {
            $('.add').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
            	url: url,
            	type: 'POST',
            	dataType: 'JSON',
            	data: data,
            })
            .done(function(data) {
            	if (data.result) {
            		alert_float('success', data.message);
            		if (typeof oTable != 'undefined') {
            			oTable.draw();
            		}
                    $('#category_test_item_quality_modal').modal('hide');
                    $('.add').removeAttr('disabled', 'disabled');
            	} else {
            		alert_float('danger', data.message);
            		$('.add').removeAttr('disabled', 'disabled');
            	}
            })
            .fail(function() {
            	console.log("error");
            });
            return false;
        }
    })
</script>