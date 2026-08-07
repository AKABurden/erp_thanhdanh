<?php echo form_open_multipart('admin/type_orders/add/'.$id, array('id' => 'add-type_orders', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_code_type_orders', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($type_orders) ? $type_orders['code'] : '')), 'placeholder="' . lang('tnh_code_type_orders') . '" id="code" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_name_type_orders', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($type_orders) ? $type_orders['name'] : '')), 'placeholder="' . lang('tnh_name_type_orders') . '" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="bold mbot10 inline-block" for="color"><?=_l('tnh_colors')?></label>
                    <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                        <input type="text" value="<?= (!empty($type_orders) ? $type_orders['color'] : '') ?>" placeholder="<?= lang('tnh_colors') ?>" name="color" id="color" class="form-control colorpicker" required>
                        <span class="input-group-addon">
                            <i class="i_color" style="background: <?= (!empty($type_orders) ? $type_orders['color'] : '') ?>" placeholder="<?= lang('tnh_colors') ?>"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function() {

        // $('.i_color').css('background-color', '#fff');
        $('#color').colorpicker();
        $('body').on('click','.colorpicker-with-alpha',function(){
            $.each($('input.colorpicker'), function(i,v){
                $(v).parent('div').find('i:nth-child(1)').css('background-color', $(v).val());
            });
        })
        
        appValidateForm($('#add-type_orders'), {
            code: 'required',
            name: 'required',
            color: 'required',
        }, thandlingTypeOrders);

        function thandlingTypeOrders(form) {
            $('.add').attr('disabled', 'disabled');

            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/type_orders/add/<?= $id ?>',
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined' && oTable != '') {
                        oTable.draw();
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }
        // init_editor('textarea[name="content_type_orders"]');
        init_selectpicker();
        init_datepicker();
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>