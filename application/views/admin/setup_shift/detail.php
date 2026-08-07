<?php echo form_open('admin/setup_shift/detail/' . $id, array('id' => 'setup-shift')); ?>
<style>
    .checkbox + .checkbox, .radio + .radio {
        margin-top: 10px !important;
    }
</style>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_name_setup_shift', 'name') ?>
                        <?php echo form_input('name',
                            (isset($_POST['name']) ? $_POST['name'] : !empty($dtSetupShift) ? $dtSetupShift['name'] : ''),
                            'placeholder="' . lang('dt_name_setup_shift') . '" id="name" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="bold mbot10 inline-block"><?= _l('Màu sắc') ?></label>
                    <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                        <input type="text" value="<?= (!empty($dtSetupShift) ? $dtSetupShift['color'] : '') ?>"
                               name="color" id="color" class="form-control colorpicker">
                        <span class="input-group-addon">
                            <i class="i_color" style=""></i>
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_time_start_setup_shift', 'time_start') ?>
                        <input type="time" id="time_start" name="time_start" class="form-control"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['time_start'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_time_end_setup_shift', 'time_end') ?>
                        <input type="time" id="time_end" name="time_end" class="form-control"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['time_end'] : '' ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_time_start_lunch_break_setup_shift', 'time_start_lunch_break') ?>
                        <input type="time" id="time_start_lunch_break" name="time_start_lunch_break"
                               class="form-control"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['time_start_lunch_break'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_time_end_lunch_break_setup_shift', 'time_end_lunch_break') ?>
                        <input type="time" id="time_end_lunch_break" name="time_end_lunch_break" class="form-control"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['time_end_lunch_break'] : '' ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_time_start_overtime_setup_shift', 'time_start_overtime') ?>
                        <input type="time" id="time_start_overtime" name="time_start_overtime" class="form-control"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['time_start_overtime'] : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang('dt_time_rice_setup_shift', 'time_rice') ?>
                        <input type="time" id="time_rice" name="time_rice" class="form-control"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['time_rice'] : '' ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Số phần cơm', 'number_rice') ?>
                        <input type="text" id="number_rice" name="number_rice" class="form-control number-format"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['number_rice'] : 1 ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Số giờ làm việc', 'number_hour') ?>
                        <input type="text" id="number_hour" name="number_hour" class="form-control number-format"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['number_hour'] : 0 ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Số ngày làm việc', 'total_date') ?>
                        <input type="text" id="total_date" name="total_date" class="form-control number-format"
                               value="<?= !empty($dtSetupShift) ? $dtSetupShift['total_date'] : 0 ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" <?= !empty($dtSetupShift) && $dtSetupShift['check_lunch_break'] ? 'checked' : '' ?> name="check_lunch_break" class="check_lunch_break"
                                   id="check_lunch_break" value="1">
                            <label for="check_lunch_break" style="color: red"><?= lang('Không trừ thời gian nghĩ trưa') ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày làm việc', 'day') ?>
                        <div style="display: flex;align-items: center">
                            <?php foreach (getListDay() as $key => $value) { ?>
                                <div class="checkbox checkbox-info" style="margin-right: 15px">
                                    <input type="checkbox" name="day[]" <?= !empty($arrDate) && in_array($value['id'],
                                        $arrDate) ? 'checked' : '' ?>
                                           id="day_<?= $value['id'] ?>"
                                           value="<?= $value['id'] ?>">
                                    <label for="day_<?= $value['id'] ?>"><?= $value['name'] ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Ngày tăng ca', 'day_overtime') ?>
                        <div style="display: flex;align-items: center">
                            <?php foreach (getListDay() as $key => $value) { ?>
                                <div class="checkbox checkbox-info" style="margin-right: 15px">
                                    <input type="checkbox"
                                           name="day_overtime[]" <?= !empty($arrDateOvertime) && in_array($value['id'],
                                        $arrDateOvertime) ? 'checked' : '' ?>
                                           id="day_overtime_<?= $value['id'] ?>"
                                           value="<?= $value['id'] ?>">
                                    <label for="day_overtime_<?= $value['id'] ?>"><?= $value['name'] ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 hide">
                    <div class="form-group">
                        <label for="day_halftime" style="color: green"><?= _l('Ngày làm việc nửa ngày') ?></label>
                        <div style="display: flex;align-items: center">
                            <?php foreach (getListDay() as $key => $value) { ?>
                                <div style="width: 100%">
                                    <div class="checkbox checkbox-info" style="margin-right: 15px">
                                        <input type="checkbox"
                                               name="day_halftime[]" <?= !empty($arrDateHalftime) && in_array($value['id'],
                                            $arrDateHalftime) ? 'checked' : '' ?>
                                                onclick="changeDayHalftime(this)"
                                               id="day_halftime_<?= $value['id'] ?>"
                                               value="<?= $value['id'] ?>">
                                        <label for="day_halftime_<?= $value['id'] ?>"><?= $value['name'] ?></label>
                                    </div>
                                    <div style="width: 95%" class=" <?= !empty($arrDateHalftime) && in_array($value['id'],
                                        $arrDateHalftime) ? '' : 'hide' ?>" id="detail_<?= $value['id'] ?>">
                                        <div>
                                            <div class="form-group">
                                                <?= lang('Thời gian BĐ', 'time_start_child') ?>
                                                <input type="time" id="time_start_child" name="time_start_child[<?= $value['id'] ?>]"
                                                       class="form-control"
                                                       value="<?= !empty($arrDateHalftimeHour[$value['id']]) ?  $arrDateHalftimeHour[$value['id']]['time_start'] : '' ?>">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group">
                                                <?= lang('Thời gian KT', 'time_end_child') ?>
                                                <input type="time" id="time_end_child" name="time_end_child[<?= $value['id'] ?>]"
                                                       class="form-control"
                                                       value="<?= !empty($arrDateHalftimeHour[$value['id']]) ?  $arrDateHalftimeHour[$value['id']]['time_end'] : '' ?>">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group">
                                                <?= lang('Thời gian TC', 'time_overtime_child') ?>
                                                <input type="time" id="time_overtime_child" name="time_overtime_child[<?= $value['id'] ?>]"
                                                       class="form-control"
                                                       value="<?= !empty($arrDateHalftimeHour[$value['id']]) ?  $arrDateHalftimeHour[$value['id']]['time_overtime'] : '' ?>">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group">
                                                <?= lang('Số giờ làm việc', 'number_hour') ?>
                                                <input type="text" id="number_hour_child" name="number_hour_child[<?= $value['id'] ?>]"
                                                       class="form-control number-format"
                                                       value="<?= !empty($arrDateHalftimeHour[$value['id']]) ?  $arrDateHalftimeHour[$value['id']]['number_hour'] : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit"
                    class="btn btn-primary add"><?= !empty($dtSetupShift) ? _l('edit') : _l('add') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>

    function changeDayHalftime(_this){
        value = $(_this).val();
        checked = $(_this).is(':checked');
        if (checked){
            $(`#detail_${value}`).removeClass('hide');
        } else {
            $(`#detail_${value}`).addClass('hide');
        }
    }

    $(function () {
        $('.i_color').css('background-color', '#fff');
        $('#color').colorpicker();
        $('body').on('click', '.colorpicker-with-alpha', function () {
            $.each($('input.colorpicker'), function (i, v) {
                $(v).parent('div').find('i:nth-child(1)').css('background-color', $(v).val());
            })
        })

        appValidateForm($('#setup-shift'), {
            name: 'required',
            time_start: 'required',
            time_end: 'required',
            time_start_lunch_break: 'required',
            time_end_lunch_break: 'required',
            time_start_overtime: 'required',
            time_rice: 'required',
            total_date: 'required',
        }, add);

        function add(form) {
            $('.add').attr('disabled', 'disabled');
            var data = $(form).serialize();
            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/setup_shift/detail/<?= $id ?>',
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function (data) {
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
                .fail(function () {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })
</script>