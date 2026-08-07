<?php echo form_open_multipart('admin/evaluate/add/'.$id.'?type='.$type, array('id' => 'add-evaluate', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
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
                        <?= lang('tnh_date_evaluate', 'date_evaluate') ?>
                        <?php echo form_input('date_evaluate', (isset($_POST['date_evaluate']) ? $_POST['date_evaluate'] : (!empty($evaluate) ? _dt($evaluate['date_evaluate']) : date('d/m/Y H:i:s'))), 'placeholder="' . lang('tnh_date_evaluate') . '" id="date_evaluate" class="form-control input-tip datetimepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_code_evaluate', 'code_evaluate') ?>
                        <?php echo form_input('code_evaluate', (isset($_POST['code_evaluate']) ? $_POST['code_evaluate'] : (!empty($evaluate) ? $evaluate['code_evaluate'] : '')), 'placeholder="' . lang('tnh_code_evaluate') . '" id="code_evaluate" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_name_evaluate', 'name_evaluate') ?>
                        <?php echo form_input('name_evaluate', (isset($_POST['name_evaluate']) ? $_POST['name_evaluate'] : (!empty($evaluate) ? $evaluate['name_evaluate'] : '')), 'placeholder="' . lang('tnh_name_evaluate') . '" id="name_evaluate" class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_content_evaluate', 'content_evaluate') ?>
                        <?php echo form_textarea('content_evaluate', (isset($_POST['content_evaluate']) ? $_POST['content_evaluate'] : (!empty($evaluate) ? $evaluate['content_evaluate'] : '')), 'placeholder="'.lang('tnh_content_evaluate').'" id="content_evaluate" class="form-control input-tip tinymce"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="dropzone dropzone-manual">
                    <div id="dropzoneTaskComment" class="dropzoneDragArea dz-default dz-message task-comment-dropzone">
                        <span><?php echo _l('drop_files_here_to_upload'); ?></span>
                    </div>
                    <div class="dropzone-task-comment-previews dropzone-previews"></div>
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
    Dropzone.options.expenseForm = false;
    if($('#dropzoneTaskComment').length > 0){
        var expenseDropzone = new Dropzone('#add-evaluate', appCreateDropzoneOptions({
            paramName: "file",
            autoProcessQueue: false,
            previewsContainer: '.dropzone-previews',
            addRemoveLinks: true,
            maxFiles: 10,
            clickable: '#dropzoneTaskComment',
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // window.location.reload();
                }
            }
        }));
    }
</script>
<script>
    $(function() {
        appValidateForm($('#add-evaluate'), {
            date_evaluate: 'required',
            code_evaluate: 'required',
            name_evaluate: 'required'
        }, handlingEvaluate);

        function handlingEvaluate(form) {
            $('.add').attr('disabled', 'disabled');
            tinymce.get('content_evaluate').save();
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

            $.each(expenseDropzone.files, function(index, value) {
                formData.append('file[]', value);
            });
            //
            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/evaluate/add/<?= $id ?>?type=<?= $type ?>',
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
        init_editor('textarea[name="content_evaluate"]');
        init_selectpicker();
        init_datepicker();
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>