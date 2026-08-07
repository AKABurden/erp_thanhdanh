<?php echo form_open('admin/category_regulations/detail_question_bank/'.$id, array('id'=>'step-salary')); ?>
<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab_info">Thông tin chung</a></li>
                <li class="hide"><a data-toggle="tab" href="#tab_info_other1">Tiêu chí chấm điểm</a></li>
                <li><a data-toggle="tab" href="#tab_info_other2">Câu hỏi và trả lời</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab_info" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Mã câu hỏi', 'code') ?>
                                <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Nhóm quy tắc', 'type') ?>
                                <select class="type" name="type" id="type" style="width: 100%" data-placeholder="<?= lang('Nhóm quy tắc') ?>">
                                    <option></option>
                                    <?php foreach ($dtType as $key => $value){ ?>
                                        <option <?= !empty($dtData) ? ($value['id'] == $dtData['type'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Mã vị trí', 'role_id') ?>
                                <input type="text" name="role_id" id="role_id" class="role_id modal-select2"
                                       data-placeholder="<?= lang('Mã vị trí') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_id'] : '' ?>"
                                       title="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Cấp bậc vai trò', 'role_level_id') ?>
                                <input type="text" name="role_level_id" id="role_level_id" class="role_id modal-select2"
                                       data-placeholder="<?= lang('Cấp bậc vai trò') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_level_id'] : '' ?>"
                                       title="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Trọng số', 'weight') ?>
                                <?php echo form_input('weight', (isset($_POST['weight']) ? $_POST['weight'] : (!empty($dtData) ? $dtData['weight'] : 0)), 'placeholder="'.lang('Trọng số').'" id="weight" class="form-control number-format input-tip"'); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Điểm tối đa', 'point_max') ?>
                                <?php echo form_input('point_max', (isset($_POST['point_max']) ? $_POST['point_max'] : (!empty($dtData) ? $dtData['point_max'] : 0)), 'placeholder="'.lang('Điểm tối đa').'" id="point_max" class="form-control number-format input-tip"'); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang('Version', 'version') ?>
                                <?php echo form_input('version', (isset($_POST['version']) ? $_POST['version'] : (!empty($dtData) ? $dtData['version'] : '')), 'placeholder="'.lang('Version').'" id="version" class="form-control input-tip"'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tab_info_other1" class="tab-pane fade in">
                    <table id="tb-other1"
                           class="dt-tnh table tnh-table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">
                                <a class="btn btn-info btn-icon add-other1"><i
                                        class="fa fa-plus"></i></a>
                            </th>
                            <th><?= lang('Tiêu chí') ?><span class="red">*</span>
                            </th>
                            <th style="width: 50px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $counterOther1 = 0;
                        $dtDataOther1 = !empty($dtData) ? get_table_where(
                            'tbl_question_bank_criteria',
                            ['question_bank_id' => $dtData['id'] ?? 0]
                        ) : [];
                        ?>
                        <?php if (!empty($dtDataOther1)) : ?>
                            <?php foreach ($dtDataOther1 as $key => $value) : ?>
                                <tr>
                                    <td>
                                        <div class="stt-other1 text-center"><?= ++$key ?></div>
                                    </td>
                                    <td>
                                        <input type="hidden"
                                               name="id_other1[]"
                                               class="form-control id_other1"
                                               value="<?= $value['id'] ?>">
                                        <input type="hidden" name="counterOther1[]"
                                               id="counterOther1" class="form-control"
                                               value="<?= $counterOther1 ?>">
                                        <input type="text" name="title[]"
                                               class="title form-control"
                                               value="<?= $value['title'] ?>">
                                    </td>
                                    <td>
                                        <div class="td-actions text-center"><span
                                                class="fa fa-remove btn btn-danger remove-row-other1"></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php $counterOther1++; ?>
                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
                <div id="tab_info_other2" class="tab-pane fade in">
                    <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Nội dung câu hỏi', 'question') ?>
                            <?php echo form_textarea('question', (isset($_POST['question']) ? $_POST['question'] : (!empty($dtData) ? $dtData['question'] : '')), 'placeholder="'.lang('Nội dung câu hỏi').'" id="question" class="form-control input-tip tinymce"'); ?>
                        </div>
                        <?= lang('Câu trả lời', 'question') ?>
                        <table id="tb-other2"
                               class="dt-tnh table tnh-table table-bordered table-hover" style="margin-top: 5px !important;">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">Thứ tự</a>
                                </th>
                                <th><?= lang('Câu trả lời') ?><span class="red">*</span></th>
                                <th style="width: 100px;"><?= lang('Điểm số') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $counterOther2 = 0;
                            $dtDataOther2 = !empty($dtData) ? get_table_where(
                                    'tbl_question_bank_answer',
                                    ['question_bank_id' => $dtData['id'] ?? 0]
                            ) : [];
                            ?>
                            <?php if (!empty($dtDataOther2)) : ?>
                                <?php foreach ($dtDataOther2 as $key => $value) : ?>
                                    <tr>
                                        <td>
                                            <div class="stt-other2 text-center">
                                                <input type="hidden"
                                                       name="prefix[<?= $counterOther2 ?>]"
                                                       class="form-control prefix"
                                                       value="<?= $value['prefix'] ?>"><?= $value['prefix'] ?></div>
                                        </td>
                                        <td>
                                            <input type="hidden"
                                                   name="id_other2[<?= $counterOther2 ?>]"
                                                   class="form-control id_other2"
                                                   value="<?= $value['id'] ?>">
                                            <input type="hidden" name="counterOther2[]"
                                                   id="counterOther2" class="form-control"
                                                   value="<?= $counterOther2 ?>">
                                            <input type="text" name="answer[<?= $counterOther2 ?>]"
                                                   class="answer form-control"
                                                   value="<?= $value['answer'] ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="point[<?= $counterOther2 ?>]"
                                                   class="point form-control"
                                                   value="<?= $value['point'] ?>">
                                        </td>
                                    </tr>
                                    <?php $counterOther2++; ?>
                                <?php endforeach ?>
                            <?php else: ?>
                                <?php foreach (getLevelAnswer() as $key => $value) : ?>
                                    <tr>
                                        <td>
                                            <div class="stt-other1 text-center">
                                                <input type="hidden"
                                                       name="prefix[<?= $counterOther2 ?>]"
                                                       class="form-control prefix"
                                                       value="<?= $value['id'] ?>"><?= $value['id'] ?></div>
                                        </td>
                                        <td>
                                            <input type="hidden"
                                                   name="id_other2[<?= $counterOther2 ?>]"
                                                   class="form-control id_other2"
                                                   value="0">
                                            <input type="hidden" name="counterOther2[]"
                                                   id="counterOther2" class="form-control"
                                                   value="<?= $counterOther2 ?>">
                                            <input type="text" name="answer[<?= $counterOther2 ?>]"
                                                   class="answer form-control"
                                                   value="">
                                        </td>
                                        <td>
                                            <input type="text" name="point[<?= $counterOther2 ?>]"
                                                   class="point form-control"
                                                   value="">
                                        </td>
                                    </tr>
                                    <?php $counterOther2++; ?>
                                <?php endforeach ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') :  _l('edit'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $("#type").select2({})
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        ajaxSelectParams('#role_level_id', 'admin/category_regulations/searchRoleLevel', $("#role_level_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            code: 'required',
            type: 'required',
            version: 'required',
            role_id: 'required',
            role_level_id: 'required',
        }, handling);

        function handling(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var data = $(form).serialize();
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
                        $('.modal-dialog .close').trigger('click');
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

    var counterOther1 = "<?= $counterOther1 ?? 0 ?>";
    $('.add-other1').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other1 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther1[]"
                       id="counterOther1" class="form-control"
                       value="${counterOther1}">
                <input type="text" name="title[]"
                       class="title form-control"
                       value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other1"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other1 tbody").append(trItem);
        counterOther1++;
        totalOther1();
    });
    $(document).on('click', '.remove-row-other1', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther1();
    });
    function totalOther1()
    {
        tbOther1 = '#tb-other1 tbody tr:not("[class^=not-tr]")';
        var nOther1 = $(tbOther1).length;
        var sttOther1 = 0;

        for (i = 0; i < nOther1; i++)
        {
            sttOther1++;
            element = $(tbOther1)[i];
            $(element).find('.stt-other1').html(sttOther1);
        }
    }
</script>