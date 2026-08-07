<div class="modal-dialog modal-lg" style="min-width: 80%;">
    <?php echo form_open(
        admin_url('in_and_out_of_work/detail/' . $id),
        ['id' => 'in_and_out_of_work', 'enctype' => 'multipart/form-data']
    ); ?>
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
                        <td style="width: 15%;">
                            <?= lang('dt_reference_suggest', 'reference_no') ?>
                        </td>
                        <td style="width: 35%;">
                            <div class="form-group">
                                <input type="text" name="reference_no" class="form-control" id="reference_no" value="<?= !empty($dtData) ? $dtData['reference_no'] : $reference_no ?>" readonly="" aria-invalid="false">
                            </div>
                        </td>
                        <td style="width: 15%;">
                            <?= lang('date', 'date') ?>
                        </td>
                        <td style="width: 35%;">
                            <?= form_input(
                                'date',
                                set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) : date('d/m/Y H:i'),
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Nhân viên', 'id_staff') ?></td>
                        <td colspan="1">
                            <select name="id_staff" id="id_staff" onchange="getLoadStaff()" data-placeholder="<?= lang('Nhân viên') ?>" style="width: 100%;" class="">
                                <option value=""></option>
                                <?php foreach ($employees as $key => $value) : ?>
                                    <option <?= !empty($dtData) ? ($dtData['id_staff'] == $value['staffid'] ? 'selected' : '') : (get_staff_user_id() == $value['staffid'] ? 'selected' : '') ?> value="<?= $value['staffid'] ?>"><?= $value['fullname'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Vị trí</td>
                        <td class="name_roles"></td>
                        <td>Chức vụ</td>
                        <td class="name_departments"></td>
                    </tr>
                    <tr>
                        <td>Lý do ra vào cổng</td>
                        <td>
                            <textarea name="note_in_out" id="note_in_out" class="form-control note_in_out note"
                                rows="3"><?= !empty($dtData) ? $dtData['note_in_out'] : '' ?></textarea>
                        </td>
                        <td>Số điện thoại liên hệ</td>
                        <td><input type="text" class="phone form-control" name="phone" value="<?= !empty($dtData) ? $dtData['phone'] : '' ?>"></td>
                    </tr>
                    <tr>
                        <td>Thời gian ra cổng</td>
                        <td>
                            <?= form_input(
                                'time_out',
                                set_value('time_out') ? set_value('time_out') : !empty($dtData) ? _dt($dtData['time_out']) : '',
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                        <td>Thời gian vào cổng</td>
                        <td>
                            <?= form_input(
                                'time_in',
                                set_value('time_in') ? set_value('time_in') : !empty($dtData) ? _dt($dtData['time_in']) : '',
                                'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required '
                            ) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row mtop10">
                <div class="col-md-12">
                    <table id="tb-detail" class="table dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px"><?= lang('STT') ?><a onclick="loadItem()" class="btn btn-info btn-icon">+</a></th>
                                <th class="text-center" style="width: 100px"><?= lang('Chứng từ ra vào cổng') ?></th>
                                <th class="text-center" style="width: 250px"><?= lang('Danh Mục Hàng Hóa Ra Cổng') ?></th>
                                <th class="text-center" style="width: 100px"><?= lang('Bảo Vệ Xác Nhận') ?></th>
                                <th style="width: 50px;" style="width: 100px"><?= lang('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0;
                            if (!empty($dtItems)) { ?>
                                <?php foreach ($dtItems as $key => $value) { ?>
                                    <tr>
                                        <td>
                                            <div class="text-center"><?= (++$key) ?></div>
                                        </td>
                                        <td>
                                            <div>
                                                <input type="hidden" class="counter" name="counter[]" value="<?= $counter ?>">
                                                <input type="hidden" class="in_and_out_of_work_items_id" name="in_and_out_of_work_items_id[<?= $counter ?>]" value="<?= $value['id'] ?>">
                                                <input type="text" class="detail_reference_no form-control" name="detail_reference_no[<?= $counter ?>]" value="<?= $value['detail_reference_no'] ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <input type="text" class="detail_items form-control" name="detail_items[<?= $counter ?>]" value="<?= $value['detail_items'] ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <input type="text" class="detail_security form-control" name="detail_security[<?= $counter ?>]" value="<?= $value['detail_security'] ?>">
                                            </div>
                                        </td>
                                        <td class="text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a></td>
                                    </tr>
                                <?php $counter++;
                                } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info add"><?php echo _l('submit'); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    function getLoadStaff() {
        var staff_id = $('#id_staff').val();
        params = {
        };
        $.ajax({
            url: '<?= admin_url('in_and_out_of_work/getStaff/') ?>' + staff_id,
            type: 'GET',
            dataType: 'JSON',
            data: params,
        }).done(function(data) {
            if (data) {
                $('.name_roles').html(data.name_roles);
                $('.name_departments').html(data.name_departments);
            }
        });
    }
    init_datepicker();
    init_selectpicker('refresh');
    $("#id_staff").select2();
    counter = <?= !empty($counter) ? $counter : 0 ?>;

    function loadItem() {
        tdStt = `<div class="text-center"></div>`;
        tdName = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="text" class="detail_reference_no form-control" name="detail_reference_no[${counter}]" value="">
        </div>`;
        tdDetailItems = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="text" class="detail_items form-control" name="detail_items[${counter}]" value="">
        </div>`;
        tdDetailSecurity = `<div>
            <input type="hidden" class="counter" name="counter[]" value="${counter}">
            <input type="text" class="detail_security form-control" name="detail_security[${counter}]" value="">
        </div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;
        trItem = `<tr>
            <td class="text-center stt">${tdStt}</td>
            <td>${tdName}</td>
            <td>${tdDetailItems}</td>
            <td>${tdDetailSecurity}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-detail").find('tbody').append(trItem);
        counter++;
        getTotal();
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }
    appValidateForm($('#in_and_out_of_work'), {
        date: 'required',
        reference_no: 'required',
        id_staff: 'required',
        time_out: 'required',
        time_in: 'required'
    }, detail);

    function getTotal() {
        tb = '#tb-detail tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
        }
    }

    function detail(form) {
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
                url: url,
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