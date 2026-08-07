<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('delivery_criteria/detail/' . $id),
        ['id' => 'delivery_criteria']); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="title"><?= $title ?></span>
            </h4>
        </div>
        <div class="modal-body">
            <table class="tnh-tb table-bordered table-hover">
                <tbody>
                <tr>
                    <td style="width: 15%"><?= lang('Mã công đoạn', 'stage_id') ?></td>
                    <td colspan="1" style="width: 35%">
                        <input type="text" name="stage_id" id="stage_id" class="stage_id"
                               data-placeholder="<?= lang('Mã công đoạn') ?>" style="width: 100%;" value="<?= !empty($dtData) ? $dtData['stage_id'] : '' ?>"
                               title="">
                    </td>
                    <td style="width: 15%"><?= lang('name', 'Tên tiêu chí giao hàng') ?></td>
                    <td colspan="1">
                        <input type="text" name="name" id="name" class="name form-control"
                               style="width: 100%;" value="<?= !empty($dtData) ? $dtData['name'] : '' ?>"
                               title="">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Loại bàn giao', 'hand_over_category_id') ?></td>
                    <td colspan="1">
                        <select name="hand_over_category_id" id="hand_over_category_id" class="hand_over_category_id" onchange="changeHandOverCategory(this)"
                                data-placeholder="<?= lang('Loại bàn giao') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($dtCategoryHandOver)) { ?>
                                <?php foreach ($dtCategoryHandOver as $key => $value) { ?>
                                    <option <?= !empty($dtData) ? ($dtData['hand_over_category_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Tiêu chi bàn giao', 'hand_over_id') ?></td>
                    <td colspan="1">
                        <select name="hand_over_id" id="hand_over_id" class="hand_over_id"
                                data-placeholder="<?= lang('Tiêu chi bàn giao') ?>" style="width: 100%;">
                            <?php if(!empty($dtHandOver)) {?>
                                <?php foreach ($dtHandOver as $key => $value) {?>
                                    <option  <?= !empty($dtData) ? ($dtData['hand_over_id'] == $value['id'] ? 'selected' : '') : '' ?> value="<?= $value['id'] ?>"><?= $value['code'] ?> - <?= $value['name'] ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 15%"><?= lang('Tiêu chuẩn', 'standard') ?></td>
                    <td colspan="1" style="width: 35%">
                        <input type="text" name="standard" id="standard" class="standard form-control"
                               style="width: 100%;" value="<?= !empty($dtData) ? $dtData['standard'] : '' ?>"
                               title="">
                    </td>
                    <td style="width: 15%"><?= lang('Phương pháp', 'method_name') ?></td>
                    <td colspan="1">
                        <input type="text" name="method_name" id="method_name" class="method_name form-control"
                               style="width: 100%;" value="<?= !empty($dtData) ? $dtData['method_name'] : '' ?>"
                               title="">
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Quy trình', 'procedure_name') ?></td>
                    <td colspan="3">
                        <textarea name="procedure_name" id="procedure_name" class="form-control procedure_name" cols="3" rows="4"><?= !empty($dtData) ? $dtData['procedure_name'] : '' ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    init_datepicker();
    init_selectpicker('refresh');
    ajaxSelectParams('#stage_id', 'admin/delivery_criteria/searchStages', $("#stage_id").val(), true, true);
    $("#hand_over_category_id").select2();
    $("#hand_over_id").select2();
    appValidateForm($('#delivery_criteria'), {
        name: 'required',
        stage_id: 'required',
    }, detail);

    function changeHandOverCategory(_this){
        $.ajax({
            url: "<?= base_url('admin/delivery_criteria/getHandOverByCategory') ?>",
            type: 'POST',
            dataType: 'JSON',
            data: {
                'hand_over_category_id': $(_this).val(),
                'csrf_token_name':hash
            },
        }).done(function(data) {
            html = '<option></option>';
            if (data.dtData.length > 0){
                $.each(data.dtData,function (k,v){
                    html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
                })
            }
            $("select.hand_over_id").select2("val","");
            $("select.hand_over_id").html(html);
        }).fail(function() {
            alert_float('danger', lang_core['errors']);
        });
    }

    function detail(form) {
        $('.add').attr('disabled', 'disabled');
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function(data) {
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
        }).fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
</script>