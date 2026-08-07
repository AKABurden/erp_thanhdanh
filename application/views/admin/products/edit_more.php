<?php echo form_open('admin/products/edit_more/' . $product['id'], array('id' => 'edit-product')); ?>
<div class="modal-dialog" style="width: 60%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_edit_more'); ?> <?= $product['code'] ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_color_formula', 'color_formula') ?>
                        <?php echo form_textarea('color_formula', (isset($_POST['color_formula']) ? $_POST['color_formula'] : $product['color_formula']), 'placeholder="' . lang('tnh_color_formula') . '" id="color_formula" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('tnh_ball_formula', 'ball_formula') ?>
                        <?php echo form_textarea('ball_formula', (isset($_POST['ball_formula']) ? $_POST['ball_formula'] : $product['ball_formula']), 'placeholder="' . lang('tnh_ball_formula') . '" id="ball_formula" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control save" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary edit"><?= lang('save') ?></button>
        </div>
    </div>
    
</div>

<?php echo form_close(); ?>
<script>
    $(function() {
        appValidateForm($('#edit-product'), {
        }, editproduct);

        function editproduct(form) {
            $('.edit').attr('disabled', 'disabled');

            tinymce.get('color_formula').save();
            tinymce.get('ball_formula').save();
            // var data = $(form).serialize();
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
                    url: site.base_url + 'admin/products/edit_more/<?= $product['id'] ?>',
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
                            oTable.draw(false);
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.edit').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.edit').removeAttr('disabled', 'disabled');
                });
            return false;
        }

        init_editor('textarea[name="color_formula"]');
        init_editor('textarea[name="ball_formula"]');
    })
</script>