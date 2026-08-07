<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    #tnhModal2 {
        z-index: 10002;
    }
</style>
<?php echo form_open('admin/kpi/updateCategoryEvaluateKpiNew/'.$category_evaluate_kpi.'',
    array('id' => 'update_category_evaluate_kpi')); ?>
<div class="modal-dialog modal-lg" style="width:40%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table_bonus_discipline" class="table table-hover dataTable dont-responsive-table" style="width: 100%;">
                            <thead>
                                <tr style="">
                                    <th style="width: 30px;" class="text-center"><a onclick="addItem()"><i class="fa fa-plus"></i></a></th>
                                    <th style="width: 200px;" class="text-center"><?= lang('Tên') ?></th>
                                    <th style="width: 100px;" class="text-center"><?= lang('Loại') ?></th>
                                    <th style="width: 50px;" class="text-center"><?= lang('Tác vụ') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $counter = 0; if (!empty($dtItems)){ ?>
                                <?php foreach ($dtItems as $key => $value){ ?>
                                    <?php
                                        $optionType = '';
                                        foreach ($dtType as $k => $v){
                                            $optionType .= '<option value="'.$v['id'].'" '.($v['id'] == $value['type'] ? 'selected' : '').'>'.$v['name'].'</option>';
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?=(++$key)?></td>
                                        <td><div class="name">
                                                <input type="hidden" name="counter[]" class="counter" value="<?= $counter ?>">
                                                <input type="hidden" name="id_item[]" class="id_item" value="<?= $value['id'] ?>">
                                                <input type="text" name="name[<?= $counter ?>]" required class="name form-control" value="<?= $value['name'] ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <select class="type" id="type_<?= $counter ?>" name="type[<?= $counter ?>]" style="width: 100%;"  data-placeholder="<?= lang('Loại') ?>">
                                                    <?= $optionType ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                            <?php $counter ++;} ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info only-save customer-form-submiter add">
                <?php echo _l('submit'); ?>
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
    var counter = <?= $counter ?>;
    var count_errors  = 0;
    var dtType = <?= !empty($dtType) ? json_encode($dtType) : '{}' ?>;
    function addItem(){
        tdStt = `<div class="stt"></div>`;
        tdName = `<div class="name">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="text" name="name[${counter}]" required class="name form-control" value="">
        </div>`;
        tdType = `<div>
            <select class="type" id="type_${counter}" name="type[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Loại') ?>">
                ${optionType()}
            </select>
        </div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdName}</td>
            <td>${tdType}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#table_bonus_discipline").find('tbody').append(trItem);
        $(`#type_${counter}`).select2();
        $(`#type_${counter}`).attr('required','required');
        counter ++;
        getTotal();
    }
    for (var i = 0; i <= counter; i++) {
        $(`#type_${i}`).select2();
        $(`#type_${i}`).attr('required','required');
    }
    <?php if (empty($dtItems)){ ?>
    addItem();
    <?php } ?>
    function removeRow(el)
    {
        $(el).closest('tr').remove();
        getTotal();
    }
    function getTotal(){
        tb = '#table_bonus_discipline tbody tr:not("[class^=not-tr]")';
        count_errors
        var n = $(tb).length;
        var stt = 0;
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            name = $(element).find('.name').val();
        }
    }

    function optionType(selected_id = 0){
        option = `<option></option>`;
        $.each(dtType, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option+= '<option '+selected+' value="'+el.id+'">'+el.name+'</option>';
        });
        return option;
    }

    appValidateForm($('#update_category_evaluate_kpi'), {
    }, db);

    //save db
    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', lang_core['check_date_enter']);
            return;
        }

        $('.add').attr('disabled', 'disabled');
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
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
                if (data.result) {
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
                category_evaluate_kpi();
                $('.modal-dialog .close').trigger('click');
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
</script>
