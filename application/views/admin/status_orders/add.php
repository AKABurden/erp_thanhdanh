<?php echo form_open_multipart('admin/status_orders/add/'.$id, array('id' => 'add-status_orders', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
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
                        <?= lang('tnh_code_status_orders', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($status_orders) ? $status_orders['code'] : '')), 'placeholder="' . lang('tnh_code_status_orders') . '" id="code" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_name_status_orders', 'name') ?>
                        <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($status_orders) ? $status_orders['name'] : '')), 'placeholder="' . lang('tnh_name_status_orders') . '" id="name" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_time', 'time') ?>
                        <?php echo form_input('time', (isset($_POST['time']) ? $_POST['time'] : (!empty($status_orders) ? $status_orders['time'] : '')), 'placeholder="' . lang('tnh_time') . '" id="time" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="bold mbot10 inline-block" for="color"><?=_l('tnh_colors')?></label>
                    <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                        <input type="text" value="<?= (!empty($status_orders) ? $status_orders['color'] : '') ?>" placeholder="<?= lang('tnh_colors') ?>" name="color" id="color" class="form-control colorpicker" required>
                        <span class="input-group-addon">
                            <i class="i_color" style="background: <?= (!empty($status_orders) ? $status_orders['color'] : '') ?>" placeholder="<?= lang('tnh_colors') ?>"></i>
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

        $('#color').colorpicker();
        $('body').on('click','.colorpicker-with-alpha',function(){
            $.each($('input.colorpicker'), function(i,v){
                $(v).parent('div').find('i:nth-child(1)').css('background-color', $(v).val());
            });
        })

        appValidateForm($('#add-status_orders'), {
            code: 'required',
            name: 'required',
            color: 'required',
        }, handlingStatusOrders);

        function handlingStatusOrders(form) {
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
                url: site.base_url + 'admin/status_orders/add/<?= $id ?>',
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
        // init_editor('textarea[name="content_status_orders"]');
        init_selectpicker();
        init_datepicker();
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>