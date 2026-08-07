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
                        <?= lang('Ngày tái đánh giá', 'date_sign') ?>
                        <?php echo form_input('date_sign', (isset($_POST['date_sign']) ? $_POST['date_sign'] : (!empty($evaluate) ? (!empty($evaluate['date_sign']) ? _dhau($evaluate['date_sign']) : '') : '')), 'placeholder="' . lang('Ngày tái đánh giá') . '" id="date_sign" class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <?php if ($_GET['type'] == 'evaluate' || $_GET['type'] == 'educate'){ ?>
                    <?php  if ($_GET['type'] == 'evaluate'){ ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('tnh_type_evaluate', 'category_evaluate_id') ?>
                                <select name="type_evaluate_id" required id="type_evaluate_id" data-placeholder="<?= lang('tnh_type_evaluate') ?>" class="modal-select2" style="width: 100%;">
                                    <option value=""></option>
                                    <?php if(!empty($dtCategoryEvaluate)): ?>
                                        <?php foreach($dtCategoryEvaluate as $key => $value): ?>
                                            <option <?= (!empty($evaluate) && $evaluate['type_evaluate_id'] == $value['id'] ? 'selected' : '') ?> data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Nhóm đánh giá', 'category_evaluate_id') ?>
                                <select name="category_evaluate_id" required id="category_evaluate_id" data-placeholder="<?= lang('Nhóm đánh giá') ?>" class="modal-select2" style="width: 100%;">
                                    <option value=""></option>
                                    <?php if(!empty($dtCategoryEvaluateDetail)): ?>
                                        <?php foreach($dtCategoryEvaluateDetail as $key => $value): ?>
                                            <option <?= (!empty($evaluate) && $evaluate['category_evaluate_id'] == $value['id'] ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    <?php } else { ?>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_type_evaluate', 'category_evaluate_id') ?>
                        <select name="type_evaluate_id" required id="type_evaluate_id" data-placeholder="<?= lang('tnh_type_evaluate') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if(!empty($dtTypeEvaluate)): ?>
                                <?php foreach($dtTypeEvaluate as $key => $value): ?>
                                    <option <?= (!empty($evaluate) && $evaluate['type_evaluate_id'] == $value['id'] ? 'selected' : '') ?> data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
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
                        <?= lang('Ngày cấp', 'date_of_issue') ?>
                        <?php echo form_input('date_of_issue', (isset($_POST['date_of_issue']) ? $_POST['date_of_issue'] : (!empty($evaluate) ? _dt($evaluate['date_of_issue']) : '')), 'placeholder="' . lang('Ngày cấp') . '" id="date_of_issue" class="form-control datetimepicker"'); ?>
                    </div>
                </div>

                <?php if($_GET['type'] == 'license' || $_GET['type'] == 'certification') {?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Thời gian sử dụng', 'time_of_use') ?>
                            <?php echo form_input('time_of_use', (isset($_POST['time_of_use']) ? $_POST['time_of_use'] : (!empty($evaluate) ? $evaluate['time_of_use'] : '')), 'placeholder="' . lang('Thời gian sử dụng') . '" id="time_of_use" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày tái cấp', 'reissue_date') ?>
                            <?php echo form_input('reissue_date', (isset($_POST['reissue_date']) ? $_POST['reissue_date'] : (!empty($evaluate) ? _dt($evaluate['reissue_date']) : '')), 'placeholder="' . lang('Ngày tái cấp') . '" id="reissue_date" class="form-control datetimepicker input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Đơn vị cấp', 'unit_of_level') ?>
                            <?php echo form_input('unit_of_level', (isset($_POST['unit_of_level']) ? $_POST['unit_of_level'] : (!empty($evaluate) ? $evaluate['unit_of_level'] : '')), 'placeholder="' . lang('Đơn vị cấp') . '" id="unit_of_level" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Đơn vị đào tạo', 'training_unit') ?>
                            <?php echo form_input('training_unit', (isset($_POST['training_unit']) ? $_POST['training_unit'] : (!empty($evaluate) ? $evaluate['training_unit'] : '')), 'placeholder="' . lang('Đơn vị đào tạo') . '" id="training_unit" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Đơn vị công chứng', 'notary_public') ?>
                            <?php echo form_input('notary_public', (isset($_POST['notary_public']) ? $_POST['notary_public'] : (!empty($evaluate) ? $evaluate['notary_public'] : '')), 'placeholder="' . lang('Đơn vị công chứng') . '" id="notary_public" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày điều chỉnh', 'adjustment_date') ?>
                            <?php echo form_input('adjustment_date', (isset($_POST['adjustment_date']) ? $_POST['adjustment_date'] : (!empty($evaluate) ? _dt($evaluate['adjustment_date']) : '')), 'placeholder="' . lang('Ngày điều chỉnh') . '" id="adjustment_date" class="form-control datetimepicker"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="active">Hoạt động</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="radio radio-primary">
                                        <input type="radio" value="0" id="active_0" name="active">
                                        <label for="check_bhxh1">Chưa sử dụng</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="radio radio-primary">
                                        <input type="radio" value="1" id="active_1" checked="" name="active">
                                        <label for="active_1">Đang sử dụng</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="radio radio-primary">
                                        <input type="radio" value="2" id="active_2" checked="" name="active">
                                        <label for="active_2">Ngưng sử dụng</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
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
        $("#category_evaluate_id").select2();
        $("#type_evaluate_id").select2();
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

        $("#type_evaluate_id").change(function (){
            type_evaluate_id = $(this).val();
            $.ajax({
                url: site.base_url+'admin/evaluate/getListCategoryEvaluate',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    type_evaluate_id: type_evaluate_id,
                },
            })
                .done(function(data) {
                    option = '';
                    if (data.listCategoryEvaluate.length > 0){
                        $.each(data.listCategoryEvaluate,function (k,v){
                            option += `<option value=${v.id}>${v.name}</option>`;
                        })
                    }
                    $("#category_evaluate_id").html(option);
                })
                .fail(function() {
                    console.log("error");
                });
        })
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>