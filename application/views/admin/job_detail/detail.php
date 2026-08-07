<?php echo form_open('admin/job_detail/detail/'.$id.'/'.$action, array('id'=>'step-salary')); ?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab_info">Thông tin chung</a></li>
                <li><a data-toggle="tab" href="#tab_info_other1">Trách nhiệm (P1)</a></li>
                <li><a data-toggle="tab" href="#tab_info_other2">Phạm vi quyền hạn</a></li>
                <li><a data-toggle="tab" href="#tab_info_other3">Yêu cầu công việc</a></li>
                <li><a data-toggle="tab" href="#tab_info_other4">Tiêu chuẩn năng lực (P2)</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab_info" class="tab-pane fade in active">
                <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mã vị trí', 'role_id') ?>
                            <input type="text" name="role_id" id="role_id" class="role_id modal-select2"
                                   data-placeholder="<?= lang('Mã vị trí') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_id'] : '' ?>"
                                   title="">
                        </div>
                    </div>
                    <!-- <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('code', 'code') ?>
                            <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($dtData) ? $dtData['code'] : '')), 'placeholder="'.lang('code').'" id="code" required class="form-control input-tip"'); ?>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tiêu đề', 'title') ?>
                            <?php echo form_input('title', (isset($_POST['title']) ? $_POST['title'] : (!empty($dtData) ? $dtData['title'] : '')), 'placeholder="'.lang('Tiêu đề').'" id="title" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <!-- <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Version', 'version') ?>
                            <?php echo form_input('version', (isset($_POST['version']) ? $_POST['version'] : (!empty($dtData) ? $dtData['version'] : '')), 'placeholder="'.lang('Version').'" id="version" class="form-control input-tip"'); ?>
                        </div>
                    </div> -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Ngày ban hành', 'date_issue') ?>
                            <?php echo form_input('date_issue', (isset($_POST['date_issue']) ? $_POST['date_issue'] : (!empty($dtData) ? _dhau($dtData['date_issue']) : date('d/m/Y'))), 'placeholder="'.lang('Ngày ban hành').'" id="date_issue" class="form-control datepicker input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Thời gian hết hạn ( tháng)', 'month_review') ?>
                            <?php echo form_input('month_review', (isset($_POST['month_review']) ? $_POST['month_review'] : (!empty($dtData) ? formatNumber($dtData['month_review']) : 0)), 'placeholder="'.lang('Thời gian hết hạn').'" id="month_review" class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Link công việc', 'link_jd_doc') ?>
                            <?php echo form_input('link_jd_doc', (isset($_POST['link_jd_doc']) ? $_POST['link_jd_doc'] : (!empty($dtData) ? $dtData['link_jd_doc'] : '')), 'placeholder="'.lang('Link công việc').'" id="link_jd_doc" class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Mục tiêu', 'goal') ?>
                            <?php echo form_textarea('goal', (isset($_POST['goal']) ? $_POST['goal'] : (!empty($dtData) ? $dtData['goal'] : '')), 'placeholder="'.lang('Mục tiêu').'" id="goal" class="form-control input-tip tinymce"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('note', 'note') ?>
                            <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : (!empty($dtData) ? $dtData['note'] : '')), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip tinymce"'); ?>
                        </div>
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
                            'tbl_job_detail_child',
                            ['job_detail_id' => $dtData['id'] ?? 0, 'type' => 1]
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
                                        <input type="hidden" class="form-control type_other1"
                                               name="type_other1[]"
                                               value="<?= $value['type'] ?>">
                                        <input type="text" name="name_other1[]"
                                               class="name_other1 form-control"
                                               value="<?= $value['name'] ?>">
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
                    <table id="tb-other2"
                           class="dt-tnh table tnh-table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">
                                <a class="btn btn-info btn-icon add-other2"><i
                                            class="fa fa-plus"></i></a>
                            </th>
                            <th><?= lang('Tiêu chí') ?><span class="red">*</span>
                            </th>
                            <th style="width: 50px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $counterOther2 = 0;
                        $dtDataOther2 = !empty($dtData) ? get_table_where(
                            'tbl_job_detail_child',
                            ['job_detail_id' => $dtData['id'] ?? 0, 'type' => 2]
                        ) : [];
                        ?>
                        <?php if (!empty($dtDataOther2)) : ?>
                            <?php foreach ($dtDataOther2 as $key => $value) : ?>
                                <tr>
                                    <td>
                                        <div class="stt-other2 text-center"><?= ++$key ?></div>
                                    </td>
                                    <td>
                                        <input type="hidden"
                                               name="id_other2[]"
                                               class="form-control id_other2"
                                               value="<?= $value['id'] ?>">
                                        <input type="hidden" name="counterOther2[]"
                                               id="counterOther1" class="form-control"
                                               value="<?= $counterOther1 ?>">
                                        <input type="hidden" class="form-control type_other2"
                                               name="type_other2[]"
                                               value="<?= $value['type'] ?>">
                                        <input type="text" name="name_other2[]"
                                               class="name_other2 form-control"
                                               value="<?= $value['name'] ?>">
                                    </td>
                                    <td>
                                        <div class="td-actions text-center"><span
                                                    class="fa fa-remove btn btn-danger remove-row-other2"></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php $counterOther2++; ?>
                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
                <div id="tab_info_other3" class="tab-pane fade in">
                    <table id="tb-other3"
                           class="dt-tnh table tnh-table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">
                                <a class="btn btn-info btn-icon add-other3"><i
                                            class="fa fa-plus"></i></a>
                            </th>
                            <th><?= lang('Tiêu chí') ?><span class="red">*</span>
                            </th>
                            <th style="width: 50px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $counterOther3 = 0;
                        $dtDataOther3 = !empty($dtData) ? get_table_where(
                            'tbl_job_detail_child',
                            ['job_detail_id' => $dtData['id'] ?? 0, 'type' => 3]
                        ) : [];
                        ?>
                        <?php if (!empty($dtDataOther3)) : ?>
                            <?php foreach ($dtDataOther3 as $key => $value) : ?>
                                <tr>
                                    <td>
                                        <div class="stt-other3 text-center"><?= ++$key ?></div>
                                    </td>
                                    <td>
                                        <input type="hidden"
                                               name="id_other3[]"
                                               class="form-control id_other3"
                                               value="<?= $value['id'] ?>">
                                        <input type="hidden" name="counterOther3[]"
                                               id="counterOther3" class="form-control"
                                               value="<?= $counterOther3 ?>">
                                        <input type="hidden" class="form-control type_other3"
                                               name="type_other3[]"
                                               value="<?= $value['type'] ?>">
                                        <input type="text" name="name_other3[]"
                                               class="name_other3 form-control"
                                               value="<?= $value['name'] ?>">
                                    </td>
                                    <td>
                                        <div class="td-actions text-center"><span
                                                    class="fa fa-remove btn btn-danger remove-row-other3"></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php $counterOther3++; ?>
                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
                <div id="tab_info_other4" class="tab-pane fade in">
                    <table id="tb-other4"
                           class="dt-tnh table tnh-table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">
                                <a class="btn btn-info btn-icon add-other4"><i
                                            class="fa fa-plus"></i></a>
                            </th>
                            <th><?= lang('Tiêu chí') ?><span class="red">*</span>
                            </th>
                            <th style="width: 50px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $counterOther4 = 0;
                        $dtDataOther4 = !empty($dtData) ? get_table_where(
                            'tbl_job_detail_child',
                            ['job_detail_id' => $dtData['id'] ?? 0, 'type' => 4]
                        ) : [];
                        ?>
                        <?php if (!empty($dtDataOther4)) : ?>
                            <?php foreach ($dtDataOther4 as $key => $value) : ?>
                                <tr>
                                    <td>
                                        <div class="stt-other1 text-center"><?= ++$key ?></div>
                                    </td>
                                    <td>
                                        <input type="hidden"
                                               name="id_other4[]"
                                               class="form-control id_other4"
                                               value="<?= $value['id'] ?>">
                                        <input type="hidden" name="counterOther4[]"
                                               id="counterOther4" class="form-control"
                                               value="<?= $counterOther4 ?>">
                                        <input type="hidden" class="form-control type_other4"
                                               name="type_other4[]"
                                               value="<?= $value['type'] ?>">
                                        <input type="text" name="name_other4[]"
                                               class="name_other4 form-control"
                                               value="<?= $value['name'] ?>">
                                    </td>
                                    <td>
                                        <div class="td-actions text-center"><span
                                                    class="fa fa-remove btn btn-danger remove-row-other4"></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php $counterOther4++; ?>
                            <?php endforeach ?>
                        <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : ($action == 'copy' ? _l('save') : _l('edit')); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            code: 'required',
            title: 'required',
            version: 'required',
            date_issue: 'required',
            role_id: 'required',
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
                <input type="hidden" class="form-control type_other1"
                       name="type_other1[]"
                       value="1">
                <input type="text" name="name_other1[]"
                       class="name_other1 form-control"
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
    var counterOther2 = "<?= $counterOther2 ?? 0 ?>";
    $('.add-other2').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other2 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther2[]"
                       id="counterOther2" class="form-control"
                       value="${counterOther2}">
                <input type="hidden" class="form-control type_other2"
                       name="type_other2[]"
                       value="2">
                <input type="text" name="name_other2[]"
                       class="name_other2 form-control"
                       value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other2"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other2 tbody").append(trItem);
        counterOther2++;
        totalOther2();
    });
    $(document).on('click', '.remove-row-other2', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther2();
    });
    function totalOther2()
    {
        tbOther2 = '#tb-other2 tbody tr:not("[class^=not-tr]")';
        var nOther2 = $(tbOther2).length;
        var sttOther2 = 0;

        for (i = 0; i < nOther2; i++)
        {
            sttOther2++;
            element = $(tbOther2)[i];
            $(element).find('.stt-other2').html(sttOther2);
        }
    }
    var counterOther3 = "<?= $counterOther3 ?? 0 ?>";
    $('.add-other3').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other3 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther3[]"
                       id="counterOther3" class="form-control"
                       value="${counterOther3}">
                <input type="hidden" class="form-control type_other3"
                       name="type_other3[]"
                       value="3">
                <input type="text" name="name_other3[]"
                       class="name_other3 form-control"
                       value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other3"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other3 tbody").append(trItem);
        counterOther3++;
        totalOther3();
    });
    $(document).on('click', '.remove-row-other3', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther3();
    });
    function totalOther3()
    {
        tbOther3 = '#tb-other3 tbody tr:not("[class^=not-tr]")';
        var nOther3 = $(tbOther3).length;
        var sttOther3 = 0;

        for (i = 0; i < nOther3; i++)
        {
            sttOther3++;
            element = $(tbOther3)[i];
            $(element).find('.stt-other3').html(sttOther3);
        }
    }
    var counterOther4 = "<?= $counterOther4 ?? 0 ?>";
    $('.add-other4').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other4 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther4[]"
                       id="counterOther4" class="form-control"
                       value="${counterOther4}">
                <input type="hidden" class="form-control type_other4"
                       name="type_other4[]"
                       value="4">
                <input type="text" name="name_other4[]"
                       class="name_other4 form-control"
                       value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other4"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other4 tbody").append(trItem);
        counterOther4++;
        totalOther4();
    });
    $(document).on('click', '.remove-row-other4', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther4();
    });
    function totalOther4()
    {
        tbOther4 = '#tb-other4 tbody tr:not("[class^=not-tr]")';
        var nOther4 = $(tbOther4).length;
        var sttOther4 = 0;

        for (i = 0; i < nOther4; i++)
        {
            sttOther4++;
            element = $(tbOther4)[i];
            $(element).find('.stt-other4').html(sttOther4);
        }
    }
</script>