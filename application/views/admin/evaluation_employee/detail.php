<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #wrapper{
        min-height: calc(100vh - 100px);
    }
    .title_header_item{
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 8px 10px;
        background: #d2e9fd;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }
    .question-scroll{
        max-height: calc(100vh - 320px);
        overflow-y: auto;
        padding-right: 10px;
    }

    .question-block{
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff;
    }

    .question-block.active{
        border-color: #337ab7;
        background: #eef5ff;
    }

    .question-header{
        display: flex;
        justify-content: space-between;
        cursor: pointer;
        font-weight: bold;
    }

    .toggle-question{
        color: #337ab7;
    }

    .question-content{
        margin-top: 10px;
    }

    .question-block.collapsed .question-content{
        display: none;
    }

    .option{
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 8px;
        cursor: pointer;
        position: relative;
    }

    .option.active{
        background: #eef5ff;
        border-color: #337ab7;
    }

    .score{
        position: absolute;
        right: 10px;
        top: 10px;
        padding: 3px 6px;
        border-radius: 4px;
        font-size: 12px;
        color: #fff;
    }

    .score-5{background:#5cb85c}
    .score-4{background:#5bc0de}
    .score-3{background:#f0ad4e}
    .score-1{background:#d9534f}
    .score-0{background:#777}
</style>
<?php echo form_open('admin/personnel_assessment/detail/' . $id . '',
    array('id' => 'evaluation_employee')); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel_s mbot10 H_scroll" id="">
                <div class="panel-body _buttons">
                    <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="col-md-5">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?= $title ?></div>
                            <div class="panel-body">
                                <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0" style="table-layout: fixed;">
                                    <tbody>
                                    <!-- <tr>
                                        <td style="width: 25%;">
                                            <label for="number" class="control-label">
                                                <small class="req text-danger">* </small>
                                                <?php echo _l('Mã phiếu'); ?>
                                            </label>
                                        </td>
                                        <td style="width: 75%;">
                                            <div class="form-group">
                                                <input type="text" name="code" value="<?=!empty($dtData) ? $dtData['code'] : ''?>" placeholder="<?= lang('Mã phiếu') ?>" class="form-control">
                                            </div>
                                        </td>
                                    </tr> -->
                                    <tr class="hide">
                                        <td style="width: 15%;">
                                            <label for="type">Loại đánh giá</label>
                                        </td>
                                        <td >
                                            <?php $valueType = !empty($dtData) ? $dtData['type'] : 0; ?>
                                            <div class="form-group">
                                                <select class="type" name="type" id="type" style="width: 100%">
                                                    <option value=""></option>
                                                    <?php if (!empty(getListTypeEvaluationEmployee())){ ?>
                                                        <?php foreach (getListTypeEvaluationEmployee() as $key => $value){ ?>
                                                            <option value="<?= $value['id'] ?>" <?= $value['id'] == $valueType ? 'selected' : ($type == $value['id'] ? 'selected' : '') ?>><?= $value['name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <?= lang('Nhân viên', 'staff_id') ?>
                                        </td>
                                        <td>
                                            <input name="staff_id" id="staff_id" class="" data-placeholder="<?= lang('Nhân viên') ?>" value="<?= !empty($dtData) ? $dtData['staff_id'] : ($dtHr['id'] ?? 0) ?>"
                                                   style="width: 100%;" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <?= lang('Mã vị trí', 'role_id') ?>
                                        </td>
                                        <td>
                                            <input onchange="getDataQuestion(this)" type="text" name="role_id" id="role_id" class="role_id modal-select2"
                                                   data-placeholder="<?= lang('Mã vị trí') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_id'] : ($dtHr['role_id'] ?? 0) ?>"
                                                   title="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <?= lang('Cấp bậc vai trò', 'role_level_id') ?>
                                        </td>
                                        <td>
                                            <input onchange="getDataQuestion(this)" type="text" name="role_level_id" id="role_level_id" class="role_level_id modal-select2"
                                                   data-placeholder="<?= lang('Cấp bậc vai trò') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['role_level_id'] : ($dtHr['role_level_id'] ?? 0) ?>"
                                                   title="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;">
                                            <label for="note">Ghi chú</label>
                                        </td>
                                        <td>
                                            <textarea name="note" cols="4" rows="4" class="form-control link_tranning"><?=!empty($dtData) ? $dtData['note'] : ''?></textarea>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?= _l('Thông tin câu hỏi') ?></div>
                            <div class="panel-body" style="margin-bottom: 25px">
                                <div class="col-md-12 table-responsive">
                                    <div class="question-scroll">
                                        <?php if (!empty($dtDataQuestion)){ ?>
                                            <?php foreach ($dtDataQuestion as $key => $value){ ?>
                                                <?php
                                                    $htmlChild = '';
                                                    if (!empty($value)){
                                                        foreach ($value as $k => $v){
                                                            $checked = in_array($v['id'], $arrIdQuestion) ? 'checked' : '';
                                                            $htmlChild .= '<div class="question-content">
                                                                <div class="option">
                                                                    <input type="hidden" name="evaluation_employee_question_id[]" value="'.$v['id'].'">
                                                                    <input '.$checked.' type="checkbox" name="question_id[]" value="'.$v['id'].'"> '.$v['question'].'
                                                                </div>
                                                            </div>';
                                                        }
                                                    }
                                                ?>
                                                <div class="question-block">
                                                    <div class="question-header">
                                                        <span><?= $key ?></span>
                                                        <span class="toggle-question"><i class="fa fa-chevron-down"></i></span>
                                                    </div>
                                                    <?= $htmlChild ?>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <input type="hidden" name="add" id="" class="form-control" value="1">
                <input type="hidden" name="view_detail" id="" class="form-control" value="1">
                <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        ajaxSelectCallBack('#staff_id', 'admin/personnel_assessment/searchStaffAndHrProfile', $("#staff_id").val());
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        ajaxSelectParams('#role_level_id', 'admin/category_regulations/searchRoleLevel', $("#role_level_id").val(), true, true);
        $("#type").select2({
            placeholder: "Chọn loại đánh giá",
        });
        _validate_form($('#evaluation_employee'), {
            code: "required",
            type: "required",
            staff_id: "required",
            role_id: "required",
            role_level_id: "required",
        },db);
    })

    function db(form) {

        $('.add').attr('disabled', 'disabled');
        var url = form.action;
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

        $.ajax({
            url : url,
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
            .done(function(data) {
                console.log(data);
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url+'admin/personnel_assessment?type=<?= $type ?>';
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }

    function ajaxSelectCallBack(element, url, id,text = '', types = '')
    {
        if (id != 0)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function (element, callback) {
                    if (id && text) {
                        callback({
                            id: id,
                            text: text
                        });
                    } else {
                        $.ajax({
                            type: "get", async: false,
                            url: site.base_url + url + '/' + $(element).val()+'/'+$("#type").val(),
                            dataType: "json",
                            success: function (data) {
                                callback(data.row);
                            }
                        });
                    }
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            type: $("#type").val(),
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            type: $("#type").val(),
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        }
    }
    // chọn đáp án
    $(document).on('click', '.option', function (e) {
        // tránh click double khi bấm trực tiếp vào input
        if ($(e.target).is('input')) return;

        var checkbox = $(this).find('input[type="checkbox"]');

        checkbox.prop('checked', !checkbox.prop('checked'));
        $(this).toggleClass('active', checkbox.prop('checked'));
    });


    // collapse câu hỏi + highlight
    $(document).on('click','.question-header',function(){
        $('.question-block').removeClass('active');
        var block = $(this).closest('.question-block');
        block.toggleClass('collapsed');
        block.addClass('active');
    });

    $("#type").change(function (){
        $("#staff_id").select2("val",null);
    })

    <?php if (!empty($dtHr['role_level_id'])){ ?>
    getDataQuestion();
    <?php } ?>

    function getDataQuestion(_this){
        role_id = $('#role_id').val();
        role_level_id = $('#role_level_id').val();
        $.ajax({
            url: site_url + 'admin/personnel_assessment/loadDataQuestion',
            type: 'POST',
            data: {
                role_id: role_id,
                role_level_id: role_level_id,
                [csrf_token_name]: hash
            },
            dataType: 'json',
            success: function (data) {
                html = '';
                if (data.dtData){
                    $.each(data.dtData, function (key, value) {
                        htmlChild = '';
                        if (value.length > 0){
                            $.each(value,function (k,v){
                                htmlChild += `<div class="question-content">
                                            <div class="option">
                                                <input type="checkbox" name="question_id[]" value="${v.id}"> ${v.question}
                                            </div>
                                        </div>`;
                            })
                        }
                        html += `<div class="question-block">
                                        <div class="question-header">
                                            <span>${key}</span>
                                            <span class="toggle-question"><i class="fa fa-chevron-down"></i></span>
                                        </div>
                                       ${htmlChild}
                                    </div>`;
                    });
                }
                $("div.question-scroll").html(html);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert_float('danger', jqXHR.responseText);
            }
        });
    }
</script>
</body>
</html>