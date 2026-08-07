<div class="modal-dialog modal-lg" style="min-width: 60%;">
    <?php echo form_open(admin_url('suggest_probationary_evaluate/create_evaluate/' . $id),
        ['id' => 'create_evaluate']); ?>
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
                            <input type="text" name="reference_no" class="form-control" id="reference_no"
                                   value="<?= $reference_no ?>" readonly="" aria-invalid="false">
                        </div>
                    </td>
                    <td style="width: 15%;">
                        <?= lang('date', 'date') ?>
                    </td>
                    <td style="width: 35%;">
                        <?= form_input('date', set_value('date') ? set_value('date') : !empty($dtData) ? _dt($dtData['date']) :date('d/m/Y H:i'),
                            'id="date" class="form-control datetimepicker" placeholder="' . lang('date') . '" required ') ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Nhân viên', 'staff_id') ?></td>
                    <td colspan="3">
                        <?= !empty($dtData) ? get_staff_full_name($dtData['staff_id']) : '' ?>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Ngày bắt đầu thử việc', 'date_start_probationary') ?></td>
                    <td colspan="1">
                        <div><?= !empty($dtData) ? _dhau($dtData['date_start_probationary']) : '' ?></div>
                    </td>
                    <td><?= lang('Ngày kết thúc thử việc', 'date_end_probationary') ?></td>
                    <td colspan="1">
                        <div><?= !empty($dtData) ? _dhau($dtData['date_end_probationary']) : '' ?></div>
                    </td>
                </tr>
                <tr>
                    <td><?= lang('Quản lý trực tiếp', 'staff_manager') ?></td>
                    <td colspan="1">
                        <select name="staff_manager" id="staff_manager" class="staff_manager"
                                data-placeholder="<?= lang('Quản lý trực tiếp') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $key => $value) { ?>
                                    <option value="<?= $value['staffid'] ?>"><?= get_staff_full_name($value['staffid']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td><?= lang('Trưởng phòng nhân sự', 'staff_manager_hr') ?></td>
                    <td colspan="1">
                        <select name="staff_manager_hr" id="staff_manager_hr" class="staff_manager_hr"
                                data-placeholder="<?= lang('Trưởng phòng nhân sự') ?>" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $key => $value) { ?>
                                    <option value="<?= $value['staffid'] ?>"><?= get_staff_full_name($value['staffid']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row mtop10">
                <div class="col-md-12">
                    <table id="tb-evaluation-criteria" class="table dataTable">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 150px"><?= lang('Tiêu chí đánh giá') ?></th>
                            <th class="text-center"><?= lang('Nội dung đánh giá') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: bold" rowspan="<?= count(getListFiveCoreValue()) ?>">5 giá trị cốt lõi</td>
                                <td><?= getListFiveCoreValue(1)['name'] ?></td>
                            </tr>
                            <?php foreach (getListFiveCoreValue() as $key => $value){ ?>
                                    <?php if ($key == 0)continue; ?>
                                <tr>
                                    <td><?= $value['name'] ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td style="font-weight: bold">Tuân thủ</td>
                                <td>Quy tắc ứng xử</td>
                            </tr>
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
    init_datepicker();
    init_selectpicker('refresh');
    $("#staff_manager").select2();
    $("#staff_manager_hr").select2();
    appValidateForm($('#create_evaluate'), {
        date: 'required',
        reference_no: 'required',
        staff_manager: 'required',
        staff_manager_hr: 'required'
    }, detail);

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